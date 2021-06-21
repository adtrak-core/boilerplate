<?php

namespace DeliciousBrains\WPMDB\Pro\RemoteUpdates;

use DeliciousBrains\WPMDB\Common\Http\Helper;
use DeliciousBrains\WPMDB\Common\Http\Http;
use DeliciousBrains\WPMDB\Common\Http\RemotePost;
use DeliciousBrains\WPMDB\Common\Http\WPMDBRestAPIServer;
use DeliciousBrains\WPMDB\Common\MigrationState\MigrationStateManager;
use DeliciousBrains\WPMDB\Common\Properties\Properties;
use DeliciousBrains\WPMDB\Common\Settings\Settings;
use DeliciousBrains\WPMDB\Common\Util\Util;
use DeliciousBrains\WPMDB\Pro\Beta\BetaManager;
use DeliciousBrains\WPMDB\Pro\License;

class RemoteUpdatesManager {

    /**
     * @var Helper
     */
    private $http_helper;
    /**
     * @var Http
     */
    private $http;
    /**
     * @var RemotePost
     */
    private $remote_post;
    /**
     * @var WPMDBRestAPIServer
     */
    private $rest_API_server;
    /**
     * @var MigrationStateManager
     */
    private $migration_state_manager;
    /**
     * @var Properties
     */
    private $props;
    /**
     * @var Settings
     */
    private $settings;
    /**
     * @var Util
     */
    private $util;
    /**
     * @var License
     */
    private $license;

    public function __construct(
        Helper $http_helper,
        Http $http,
        RemotePost $remote_post,
        WPMDBRestAPIServer $rest_API_server,
        MigrationStateManager $migration_state_manager,
        Properties $props,
        Settings $settings,
        Util $util,
        License $license
    ) {
        $this->http_helper             = $http_helper;
        $this->http                    = $http;
        $this->remote_post             = $remote_post;
        $this->rest_API_server         = $rest_API_server;
        $this->migration_state_manager = $migration_state_manager;
        $this->props                   = $props;
        $this->settings                = $settings->get_settings();
        $this->util                    = $util;
        $this->license                 = $license;
    }

    public function register()
    {
        add_action('rest_api_init', [$this, 'register_rest_routes']);
        add_action('wp_ajax_nopriv_wpmdb_remote_update_plugin', array($this, 'respond_to_remote_update_plugin'));
    }

    public function register_rest_routes()
    {
        $this->rest_API_server->registerRestRoute('/update-plugin-on-remote', [
            'methods'  => 'POST',
            'callback' => [$this, 'ajax_update_plugin_on_remote']
        ]);
    }

    /**
     * WP REST API endpoint for `/update-plugin-on-remote`
     * Verifies that the local site has a valid license and that the remote site has a valid connection string.
     *
     * @return mixed
     */
    public function ajax_update_plugin_on_remote()
    {
        $_POST = $this->http_helper->convert_json_body_to_post();
        $key_rules = apply_filters(
            'wpmdb_key_rules',
            array(
                'action' => 'key',
                'url'    => 'url',
                'key'    => 'string',
                'slug'   => 'string',
                'nonce'  => 'key'
            )
        );

        $state_data = $this->migration_state_manager->set_post_data($key_rules);

        if (!$this->license->is_valid_licence()) {
            $message = __('Please activate your license before updating.', 'wp-migrate-db');
            return $this->http->end_ajax(new \WP_Error('invalid-license', $message));
        }

        $data = array(
            'action'  => 'wpmdb_remote_update_plugin',
            'referer' => $this->util->get_short_home_address_from_url(home_url()),
            'version' => $this->props->plugin_version,
            'slug'    => $state_data['slug']
        );

        $data['sig']     = $this->http_helper->create_signature($data, $state_data['key']);
        $ajax_url        = $this->util->ajax_url();
        $timeout         = apply_filters('wpmdb_prepare_remote_connection_timeout', 30);
        $remote_response = $this->remote_post->post($ajax_url, $data, __FUNCTION__, compact('timeout'));

        if (is_wp_error($remote_response)) {
            return $this->http->end_ajax($remote_response);
        }

        return $this->http->end_ajax(true);
    }

    /**
     * Responds to remote request to update WP Migrate DB Pro.
     *
     * @return mixed
     */
    public function respond_to_remote_update_plugin()
    {
        $key_rules = apply_filters(
            'wpmdb_key_rules',
            array(
                'action'  => 'key',
                'intent'  => 'key',
                'referer' => 'string',
                'version' => 'string',
                'slug'    => 'string',
                'sig'     => 'string'
            ),
            __FUNCTION__
        );

        $state_data = $this->migration_state_manager->set_post_data($key_rules);

        if (is_wp_error($state_data)) {
            return wp_send_json_error($state_data->get_error_message());
        }

        unset($key_rules['sig']);
        $filtered_post = $this->http_helper->filter_post_elements($state_data, array_keys($key_rules));

        if (!$this->http_helper->verify_signature($filtered_post, $this->settings['key'])) {
            $err = $this->props->invalid_content_verification_error . ' (#120)';

            return $this->http->end_ajax(
                new \WP_Error('invalid-content-verification', $err),
                $filtered_post
            );
        }

        define('DOING_CRON', true); // Keeps the plugin active.
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php'; // Required for upgrade classes.

        $skin            = new \WP_AJAX_Upgrader_Skin();
        $upgrader        = new \Plugin_Upgrader($skin);
        $plugin_basename = $this->props->plugin_basename;
        $plugin_data     = null;

        // Check if beta updates is disabled and enable it
        if (BetaManager::is_beta_version($state_data['version']) && !BetaManager::has_beta_optin($this->settings)) {
            BetaManager::set_beta_optin(true);

            //Update plugins updates transient
            $current = get_site_transient( 'update_plugins' );
            $current = apply_filters( 'site_transient_update_plugins', $current );
            set_site_transient( 'update_plugins', $current );
        }

        //if the post data contains a slug that means we're updating an addon, look for the plugin information and get the plugin basename
        if (!empty($state_data['slug'])) {
            $plugin_data = get_plugins(DIRECTORY_SEPARATOR . $state_data['slug']);
            if (!empty($plugin_data)) {
                $plugin_basename = $state_data['slug'] . DIRECTORY_SEPARATOR . key($plugin_data);
            }
        }
        //if plugin data still empty, get the plugin data for the default base name
        if (empty($plugin_data)) {
            $plugin_data = get_plugins();
            $plugin_data = isset($plugin_data[$plugin_basename]) ? $plugin_data[$plugin_basename] : [];
        }

        //set the plugin name
        $plugin_name = isset($plugin_data['Name']) ? $plugin_data['Name'] : reset($plugin_data)['Name'];

        $result = false;
        //make sure there's a plugin to update
        if ($plugin_basename !== null) {
            $result = $upgrader->upgrade($plugin_basename);
        }

        if (!$result || is_wp_error($result)) {
            return $this->http->end_ajax(
                new \WP_Error(
                    'wpmdb-remote-update-failed',
                    __(sprintf('There was an error updating %s on the <span class="semibold">remote server</span>. Please try again or update the plugin manually.', $plugin_name), 'wp-migrate-db')
                )
            );
        }

        return $this->http->end_ajax(true);
    }
}
