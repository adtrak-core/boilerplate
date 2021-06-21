<?php

namespace DeliciousBrains\WPMDB\Pro\Migration\Connection;

use DeliciousBrains\WPMDB\Common\BackupExport;
use DeliciousBrains\WPMDB\Common\Error\ErrorLog;
use DeliciousBrains\WPMDB\Common\Filesystem\Filesystem;
use DeliciousBrains\WPMDB\Common\FormData\FormData;
use DeliciousBrains\WPMDB\Common\Http\Helper;
use DeliciousBrains\WPMDB\Common\Http\Http;
use DeliciousBrains\WPMDB\Common\Http\RemotePost;
use DeliciousBrains\WPMDB\Common\Http\Scramble;
use DeliciousBrains\WPMDB\Common\Http\WPMDBRestAPIServer;
use DeliciousBrains\WPMDB\Common\MigrationPersistence\Persistence;
use DeliciousBrains\WPMDB\Common\MigrationState\MigrationState;
use DeliciousBrains\WPMDB\Common\MigrationState\MigrationStateManager;
use DeliciousBrains\WPMDB\Common\Multisite\Multisite;
use DeliciousBrains\WPMDB\Common\Properties\DynamicProperties;
use DeliciousBrains\WPMDB\Common\Properties\Properties;
use DeliciousBrains\WPMDB\Common\Settings\Settings;
use DeliciousBrains\WPMDB\Common\Sql\Table;
use DeliciousBrains\WPMDB\Common\Sql\TableHelper;
use DeliciousBrains\WPMDB\Common\Util\Util;
use DeliciousBrains\WPMDB\Pro\Beta\BetaManager;
use DeliciousBrains\WPMDB\Pro\License;
use DeliciousBrains\WPMDB\Pro\UsageTracking;

class Remote
{

    /**
     * @var Scramble
     */
    private $scrambler;
    /**
     * @var Http
     */
    private $http;
    /**
     * @var Helper
     */
    private $http_helper;
    /**
     * @var Properties
     */
    private $props;
    /**
     * @var ErrorLog
     */
    private $error_log;
    /**
     * @var FormData
     */
    private $form_data;
    /**
     * @var array
     */
    private $migration_options;
    /**
     * @var Settings
     */
    private $settings;
    /**
     * @var License
     */
    private $license;
    /**
     * @var RemotePost
     */
    private $remote_post;
    /**
     * @var Util
     */
    private $util;
    /**
     * @var Table
     */
    private $table;
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var DynamicProperties
     */
    private $dynamic_props;
    /**
     * @var Multisite
     */
    private $multisite;
    /**
     * @var TableHelper
     */
    private $table_helper;
    /**
     * @var BackupExport
     */
    private $backup_export;

    public function __construct(
        Scramble $scrambler,
        Http $http,
        Helper $http_helper,
        Properties $props,
        ErrorLog $error_log,
        License $license,
        RemotePost $remote_post,
        Util $util,
        Table $table,
        FormData $form_data,
        Settings $settings,
        Filesystem $filesystem,
        Multisite $multisite,
        TableHelper $table_helper,
        BackupExport $backup_export
    ) {
        $this->scrambler     = $scrambler;
        $this->http          = $http;
        $this->http_helper   = $http_helper;
        $this->props         = $props;
        $this->error_log     = $error_log;
        $this->form_data     = $form_data;
        $this->settings      = $settings->get_settings();
        $this->license       = $license;
        $this->remote_post   = $remote_post;
        $this->util          = $util;
        $this->table         = $table;
        $this->filesystem    = $filesystem;
        $this->dynamic_props = DynamicProperties::getInstance();
        $this->multisite     = $multisite;
        $this->table_helper  = $table_helper;
        $this->backup_export = $backup_export;
    }

    public function register()
    {
        // external AJAX handlers
        add_action('wp_ajax_nopriv_wpmdb_verify_connection_to_remote_site', array($this, 'respond_to_verify_connection_to_remote_site'));
        add_action('wp_ajax_nopriv_wpmdb_remote_initiate_migration', array($this, 'respond_to_remote_initiate_migration'));

        // Report outdated addon versions to the other site.
        add_filter('wpmdb_establish_remote_connection_data', array($this, 'report_outdated_addon_versions'), 100);
    }


    /**
     * No privileges AJAX endpoint for the wpmdb_verify_connection_to_remote_site action.
     * Verifies that the connecting site is using the same version of WP Migrate DB as the local site.     * Verifies that the request is originating from a trusted source by verifying the request signature.
     * Verifies that the local site has a valid licence.
     * Verifies that the local site is allowed to perform a pull / push migration.
     * If all is successful, returns an array of local site information used to complete the migration.
     *
     * @return mixed
     */
    public function respond_to_verify_connection_to_remote_site()
    {
        $key_rules = apply_filters(
            'wpmdb_respond_to_verify_connection_key_rules',
            array(
                'action'  => 'key',
                'intent'  => 'key',
                'referer' => 'string',
                'version' => 'string',
                'sig'     => 'string',
            ),
            __FUNCTION__
        );

        Persistence::cleanupStateOptions('wpmdb_remote_migration_state'); // Wipe old migration options
        $state_data = Persistence::setRemotePostData($key_rules, __METHOD__);

        if (is_wp_error($state_data)) {
            return wp_send_json_error($state_data->get_error_message());
        }

        $return = array();

        unset($key_rules['sig']);
        $filtered_post = $this->http_helper->filter_post_elements($state_data, array_keys($key_rules));

        // Only scramble response once we know it can be handled.
        add_filter('wpmdb_before_response', array($this->scrambler, 'scramble'));

        if (!$this->http_helper->verify_signature($filtered_post, $this->settings['key'])) {
            $err = $this->props->invalid_content_verification_error . ' (#120)';

            return $this->http->end_ajax(
                new \WP_Error('invalid-content-verification', $err),
                $filtered_post
            );
        }

        /**
         * Check remote version against local version
         */
        if (!isset($filtered_post['version']) || version_compare($filtered_post['version'], $this->props->plugin_version, '!=')) {
            if (!isset($filtered_post['version'])) {
                $return['message'] = sprintf(__('<b>Version Mismatch</b> &mdash; We\'ve detected you have version %1$s of WP Migrate DB Pro at %2$s but are using an outdated version here. Please go to the Plugins page on both installs and check for updates.', 'wp-migrate-db'), $GLOBALS['wpmdb_meta'][$this->props->plugin_slug]['version'], $this->util->get_short_home_address_from_url(home_url()));
            } else {
                if (version_compare($filtered_post['version'], $this->props->plugin_version, '>')) {
                    $return['message'] = sprintf(__('<b>Version Mismatch</b> &mdash; We\'ve detected you have version %1$s of WP Migrate DB Pro at %2$s but are using %3$s here. (#196)', 'wp-migrate-db'), $GLOBALS['wpmdb_meta'][$this->props->plugin_slug]['version'], $this->util->get_short_home_address_from_url(home_url()), $filtered_post['version']);
                } else {
                    $return['message'] = sprintf(__('<b>Version Mismatch</b> &mdash; We\'ve detected you have version %1$s of WP Migrate DB Pro at %2$s but are using %3$s here. Please go to the Plugins page on both installs and check for updates.', 'wp-migrate-db'), $GLOBALS['wpmdb_meta'][$this->props->plugin_slug]['version'], $this->util->get_short_home_address_from_url(home_url()), $filtered_post['version']);

                    // If the other site is pre-2.0, we need to serialize the response.
                    if (version_compare($filtered_post['version'], '2.0b1', '<')) {
                        $return['error']    = 1;
                        $return['error_id'] = 'version_mismatch';
                        $return             = serialize($return);
                        return $this->http->end_ajax($return, false, true);
                    }
                }
            }

            return $this->http->end_ajax(
            	new \WP_Error(
            		'wpmdb-version-mismatch',
					$return['message']
				)
			);
        }

        /**
         * Do license check
         */
        if (!$this->license->is_valid_licence()) {
            $local_host = $this->util->get_short_home_address_from_url(home_url());

            return $this->http->end_ajax(
                new \WP_Error(
                    'wpmdb-license-not-valid',
                    sprintf(__("<b>Activate Remote License</b> &mdash; Looks like you don't have a WP Migrate DB Pro license active at %s (#195).", 'wp-migrate-db'), $local_host)
                )
            );
        }

        /**
         * Check allow push/pull settings
         */
        $key = 'allow_' . $state_data['intent'];
        if (!isset($this->settings[$key]) || (bool)$this->settings[$key] !== true) {
            if ($state_data['intent'] === 'pull') {
                $message = __('The connection succeeded but the remote site is configured to reject pull connections. You can change this in the "settings" tab on the remote site. (#122)', 'wp-migrate-db');
            } else {
                $message = __('The connection succeeded but the remote site is configured to reject push connections. You can change this in the "settings" tab on the remote site. (#122)', 'wp-migrate-db');
            }

            return $this->http->end_ajax(
                new \WP_Error(
                    'wpmdb-license-push-pull-disabled',
                    $message
                )
            );
        }

        $site_details = $this->util->site_details();

        $return['tables']                 = $this->table->get_tables();
        $return['prefixed_tables']        = $this->table->get_tables('prefix');
        $return['table_sizes']            = $this->table->get_table_sizes();
        $return['table_rows']             = $this->table->get_table_row_count();
        $return['table_sizes_hr']         = array_map(array($this->table, 'format_table_sizes'), $this->table->get_table_sizes());
        $return['path']                   = $this->util->get_absolute_root_file_path();
        $return['url']                    = home_url();
        $return['prefix']                 = $site_details['prefix']; // TODO: Remove backwards compatibility.
        $return['bottleneck']             = $this->util->get_bottleneck();
        $return['delay_between_requests'] = $this->settings['delay_between_requests'];
        $return['error']                  = 0;
        $return['plugin_version']         = $this->props->plugin_version;
        $return['domain']                 = $this->multisite->get_domain_current_site();
        $return['path_current_site']      = $this->util->get_path_current_site();
        $return['uploads_dir']            = $site_details['uploads_dir']; // TODO: Remove backwards compatibility.
        $return['gzip']                   = (Util::gzip() ? '1' : '0');
        $return['post_types']             = $this->table->get_post_types();
        // TODO: Use WP_Filesystem API.
        $return['write_permissions']      = (is_writeable($this->filesystem->get_upload_info('path')) ? 'true' : 'false');
        $return['upload_dir_long']        = $this->filesystem->get_upload_info('path');
        $return['wp_upload_dir']          = $this->filesystem->get_wp_upload_dir();
        $return['temp_prefix']            = $this->props->temp_prefix;
        $return['lower_case_table_names'] = $this->table->get_lower_case_table_names_setting();
        $return['subsites']               = $site_details['subsites']; // TODO: Remove backwards compatibility.
        $return['site_details']           = $this->util->site_details();
        $return['beta_optin']             = BetaManager::has_beta_optin($this->settings);
        $return                           = apply_filters('wpmdb_establish_remote_connection_data', $return);
        $result                           = $this->http->end_ajax($return, false, true);

        return $result;
    }

    /**
     * Reports outdated addon versions (pre-2.0) to the site initiating the migration.
     * TODO: Remove when enough people have moved on from 1.9.x and earlier.
     *
     * Hooks on: wpmdb_establish_remote_connection_data
     *
     * @param array $return The existing site data.
     *
     * @return array
     */
    public function report_outdated_addon_versions($return)
    {
        if (!isset($return['media_files_version']) && isset($GLOBALS['wpmdb_meta']['wp-migrate-db-pro-media-files'])) {
            $return['media_files_version']   = $GLOBALS['wpmdb_meta']['wp-migrate-db-pro-media-files']['version'];
            $return['media_files_available'] = '1';
        }

        if (!isset($return['theme_plugin_files_version']) && isset($GLOBALS['wpmdb_meta']['wp-migrate-db-pro-theme-plugin-files'])) {
            $return['theme_plugin_files_version']   = $GLOBALS['wpmdb_meta']['wp-migrate-db-pro-theme-plugin-files']['version'];
            $return['theme_plugin_files_available'] = '1';
        }

        if (!isset($return['mst_version']) && isset($GLOBALS['wpmdb_meta']['wp-migrate-db-pro-multisite-tools'])) {
            $return['mst_version']   = $GLOBALS['wpmdb_meta']['wp-migrate-db-pro-multisite-tools']['version'];
            $return['mst_available'] = '1';
        }

        return $return;
    }

    /**
     * Validates migration request as the remote site and sets up anything that may be needed before the migration starts.
     *
     * @return array
     */
    public function respond_to_remote_initiate_migration()
    {
        add_filter('wpmdb_before_response', array($this->scrambler, 'scramble'));

	    $key_rules = apply_filters(
		    'wpmdb_initiate_key_rules',
		    array(
			    'action'       => 'key',
			    'intent'       => 'key',
			    'form_data'    => 'string',
			    'sig'          => 'string',
			    'site_details' => 'string',
		    ),
		    __FUNCTION__
	    );

        Persistence::cleanupStateOptions();
        Persistence::cleanupStateOptions('wpmdb_remote_migration_state');

        $state_data = Persistence::setRemotePostData($key_rules, __METHOD__);

        if (is_wp_error($state_data)) {
            return wp_send_json_error($state_data->get_error_message());
        }

        $return        = array();
        $filtered_post = $this->http_helper->filter_post_elements(
            $state_data,
            array(
                'action',
                'intent',
                'form_data',
                'site_details',
            )
        );

        $signature = $this->http_helper->verify_signature($filtered_post, $this->settings['key']);

        if (!$signature) {
            $error_msg = $this->props->invalid_content_verification_error . ' (#111)';
            return $this->http->end_ajax(
                new \WP_Error(
                    'wpmdb-invalid-content-verification-error',
                    $error_msg
                ),
                $filtered_post
            );
        }


        $state_data['form_data'] = base64_decode($state_data['form_data']);
        $settings_key            = 'allow_' . $state_data['intent'];

        if (isset($this->settings[$settings_key]) && true !== (bool)$this->settings[$settings_key]) {
            if ('pull' === $state_data['intent']) {
                $message = __('The connection succeeded but the remote site is configured to reject pull connections. You can change this in the "settings" tab on the remote site. (#110)', 'wp-migrate-db');
            } else {
                $message = __('The connection succeeded but the remote site is configured to reject push connections. You can change this in the "settings" tab on the remote site. (#110)', 'wp-migrate-db');
            }

            return $this->http->end_ajax(
                new \WP_Error(
                    'wpmdb-push-pull-disabled',
                    $message
                )
            );
        }

        UsageTracking::log_usage($state_data['intent'] . '-remote');

        $state_data['site_details'] = Util::unserialize(
            base64_decode($filtered_post['site_details']),
            __METHOD__
        );

        // ***+=== @TODO - revisit usage of parse_migration_form_data
        $this->migration_options = $this->form_data->parse_and_save_migration_form_data($state_data['form_data']);

        $return = $this->maybe_setup_backups($state_data, $return);

        // Store current migration state and return its id.
        $state = array_merge($state_data, $return);

        $key_rules += [
            'db_version',
            'site_url',
            'dump_filename',
            'dump_url',
        ];

        $result = Persistence::setRemotePostData(
            $key_rules,
            __METHOD__,
            'wpmdb_remote_migration_state',
            $state,
            false
        );

        return wp_send_json_success($result);
    }

    /**
     * @param       $state_data
     * @param array $return
     *
     * @return array
     */
    protected function maybe_setup_backups($state_data, array $return)
    {
        global $wpdb;
        if ('push' === $state_data['intent']) {
            if ('none' !== $this->migration_options['current_migration']['backup_option']) {
                list($dump_filename, $dump_url) = $this->backup_export->setup_backups();
                $return['dump_filename'] = $dump_filename;
                $return['dump_url']      = $dump_url;
            }

            // sets up our table to store 'ALTER' queries
            $create_alter_table_query = $this->table->get_create_alter_table_query();
            $process_chunk_result     = $this->table->process_chunk($create_alter_table_query);

            if (true !== $process_chunk_result) {
                return $this->http->end_ajax($process_chunk_result);
            }

            $return['db_version'] = $wpdb->db_version();
            $return['site_url']   = site_url();
        }

        return $return;
    }
}
