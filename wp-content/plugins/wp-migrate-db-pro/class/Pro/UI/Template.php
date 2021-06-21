<?php

namespace DeliciousBrains\WPMDB\Pro\UI;

use DeliciousBrains\WPMDB\Common\Filesystem\Filesystem;
use DeliciousBrains\WPMDB\Common\FormData\FormData;
use DeliciousBrains\WPMDB\Common\Profile\ProfileManager;
use DeliciousBrains\WPMDB\Common\Properties\DynamicProperties;
use DeliciousBrains\WPMDB\Common\Properties\Properties;
use DeliciousBrains\WPMDB\Common\Settings\Settings;
use DeliciousBrains\WPMDB\Common\Sql\Table;
use DeliciousBrains\WPMDB\Common\UI\Notice;
use DeliciousBrains\WPMDB\Common\Util\Util;
use DeliciousBrains\WPMDB\Pro\Addon\Addon;
use DeliciousBrains\WPMDB\Pro\Api;
use DeliciousBrains\WPMDB\Pro\Beta\BetaManager;
use DeliciousBrains\WPMDB\Pro\License;
use DeliciousBrains\WPMDB\Pro\Plugin\ProPluginManager;
use DeliciousBrains\WPMDB\WPMDBDI;

class Template extends \DeliciousBrains\WPMDB\Common\UI\TemplateBase
{

    /**
     * @var Notice
     */
    public $notice;
    /**
     * @var FormData
     */
    public $form_data;
    /**
     * @var DynamicProperties
     */
    public $dynamic_props;
    /**
     * @var
     */
    public $addons;
    /**
     * @var License
     */
    public $license;
    /**
     * @var Addon
     */
    public $addon;
    /**
     * @var ProPluginManager
     */
    public $plugin_manager;

    public function __construct(
        Settings $settings,
        Util $util,
        ProfileManager $profile,
        Filesystem $filesystem,
        Table $table,
        Notice $notice,
        FormData $form_data,
        Addon $addon,
        Properties $properties,
        ProPluginManager $plugin_manager
    ) {
        parent::__construct($settings, $util, $profile, $filesystem, $table, $properties);
        $this->notice    = $notice;
        $this->form_data = $form_data;


        $this->dynamic_props  = DynamicProperties::getInstance();
        $this->addon          = $addon;
        $this->plugin_manager = $plugin_manager;

        // Insert backups tab into plugin_tabs array
        array_splice($this->plugin_tabs, 1, 0, [
            [
                'slug'  => 'backups',
                'title' => _x('Backups', 'Get backups', 'wp-migrate-db'),
            ],
        ]);
    }

    public function register()
    {
        $this->license = WPMDBDI::getInstance()->get(License::class);
        // templating actions
        add_filter('wpmdb_notification_strings', [$this, 'notifications']);

        $accepted_fields = $this->form_data->get_accepted_fields();
        $accepted_fields = array_diff($accepted_fields, ['exclude_post_revisions']);
        $this->form_data->set_accepted_fields($accepted_fields);
    }

    function notifications($notifications)
    {
        $notice_id    = 'outdated_addons_warning';
        $notice_links = $this->notice->check_notice($notice_id);

        if (!$notice_links) {
            return;
        }

        foreach ($this->addon->getAddons() as $addon_basename => $addon) {
            if (false == $this->addon->is_addon_outdated($addon_basename) || false == is_plugin_active($addon_basename)) {
                continue;
            }
            $update_url = wp_nonce_url(network_admin_url('update.php?action=upgrade-plugin&plugin=' . urlencode($addon_basename)), 'upgrade-plugin_' . $addon_basename);
            $addon_slug = current(explode('/', $addon_basename));
            if (isset($GLOBALS['wpmdb_meta'][$addon_slug]['version'])) {
                $version = ' (' . $GLOBALS['wpmdb_meta'][$addon_slug]['version'] . ')';
            } else {
                $version = '';
            }

            $installed_version = $GLOBALS['wpmdb_meta'][$this->props->plugin_slug]['version'];
            $is_core_beta      = BetaManager::is_beta_version($installed_version);

            // Maybe prompt user to disable addons if beta doesn't support them
            if ($is_core_beta) {
                $url = add_query_arg(array(
                    'action' => 'deactivate',
                    'plugin' => $addon_basename,
                    'paged'  => 1,
                ), network_admin_url('plugins.php'));

                $url = add_query_arg('_wpnonce', wp_create_nonce('deactivate-plugin_' . $addon_basename), $url);

                $msg = '<div><strong>Update Required</strong> &mdash; ' .
                       sprintf(__('The version of the %1$s addon you have installed%2$s is out-of-date and will not work with this beta version WP Migrate DB Pro. There may be a <a href="%3$s">beta update available</a>, otherwise please <a href="%4$s">deactivate this addon</a>.', 'wp-migrate-db'), $addon['name'], $version, $update_url, $url) . '</div>';
            } else {
                $msg = '<div><strong>Update Required</strong> &mdash; ' .
                       sprintf(__('The version of the %1$s addon you have installed%2$s is out-of-date and will not work with this version WP Migrate DB Pro. <a href="%3$s">Update Now</a>', 'wp-migrate-db'), $addon['name'], $version, $update_url) . '</div>';
            }

            // @TODO enable these notifications once Addons are released on 2.0 branch

//            $notifications[$addon_basename] = [
//                'message' => $msg,
//                'link'    => false,
//                'id'      => $addon_basename,
//            ];
        }

        $secret_key_notice_id = 'secret_key_warning';

        $secret_key_links = $this->notice->check_notice($secret_key_notice_id, true, 604800);

        if (!$secret_key_links) {
            return $notifications;
        }

        // Only show the warning if the key is 32 characters in length
        if (strlen($this->settings['key']) <= 32) {
            $notifications[$secret_key_notice_id] = [
                'message' => $this->template_to_string('secret-key-warning', 'pro', $secret_key_links),
                'link'    => $secret_key_links,
                'id'      => $secret_key_notice_id,
            ];
        }

        if (!defined('WP_HTTP_BLOCK_EXTERNAL') || !WP_HTTP_BLOCK_EXTERNAL) {
            return $notifications;
        }

        $notice_id = 'block_external_warning';

        $notice_links = $this->notice->check_notice($notice_id, true, 604800);

        if (!$notice_links) {
            return $notifications;
        }

        $notifications[$notice_id] = [
            'message' => $this->template_to_string('block-external-warning', 'pro', $notice_links),
            'link'    => $notice_links,
            'id'      => $notice_id,
        ];

        return $notifications;
    }
}
