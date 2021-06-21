<?php

namespace DeliciousBrains\WPMDBMF\CliCommand;

use DeliciousBrains\WPMDB\Common\Cli\Cli;
use DeliciousBrains\WPMDB\Common\Cli\CliManager;
use DeliciousBrains\WPMDB\Common\Filesystem\Filesystem;
use DeliciousBrains\WPMDB\Common\MigrationState\StateDataContainer;
use DeliciousBrains\WPMDB\Common\Properties\Properties;
use DeliciousBrains\WPMDB\Common\Util\Util;
use DeliciousBrains\WPMDB\Pro\Addon\Addon;
use DeliciousBrains\WPMDB\WPMDBDI;
use DeliciousBrains\WPMDBMF\MediaFilesLocal;

class MediaFilesCli extends \DeliciousBrains\WPMDBMF\MediaFilesAddon
{
    /**
     * @var Cli
     */
    private $cli;
    /**
     * @var CliManager
     */
    private $cli_manager;
    /**
     * @var Util
     */
    protected $util;
    /**
     * @var StateDataContainer
     */
    private $state_data_container;
    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(
        Addon $addon,
        Properties $properties,
        Cli $cli,
        CliManager $cli_manager,
        Util $util,
        StateDataContainer $state_data_container,
        \DeliciousBrains\WPMDB\Pro\Transfers\Files\Util $transfers_util,
        Filesystem $filesystem
    ) {
        parent::__construct(
            $addon,
            $properties,
            $util,
            $transfers_util,
            $filesystem
        );

        $this->cli                  = $cli;
        $this->cli_manager          = $cli_manager;
        $this->util                 = $util;
        $this->state_data_container = $state_data_container;
        $this->filesystem           = $filesystem;
    }

    public function register()
    {
        // Accepted profile fields exclusive to Media Files.
        add_filter('wpmdb_accepted_profile_fields', [$this, 'accepted_profile_fields']);

        // Announce extra args for Media Files.
        add_filter('wpmdb_cli_filter_get_extra_args', [$this, 'filter_extra_args'], 10, 1);

        // Add extra args for Media Files migrations.
        add_filter('wpmdb_cli_filter_get_profile_data_from_args', [$this, 'add_mf_profile_args'], 11, 3);

        // Add the Media Files stage.
        add_filter('wpmdb_cli_profile_before_migration', [$this, 'add_mf_stage']);

        // Initialize the CLI Migration.
        add_filter('wpmdb_pro_cli_finalize_migration', [$this, 'cli_migration'], 10, 4);

        $this->media_files_local = WPMDBDI::getInstance()->get(MediaFilesLocal::class);
    }

    /**
     * Checks if the current migration includes a Media Files migration.
     *
     * @param array $profile
     *
     * @return bool
     */
    public function is_mf_migration($profile)
    {
        if (!isset($profile['media_files']) || true !== $profile['media_files']['enabled']) {
            return false;
        }

        return true;
    }

    /**
     * Adds extra profile fields used by the Media Files addon.
     *
     * @param array $fields
     *
     * @return array
     */
    public function accepted_profile_fields($fields)
    {
        $fields[] = 'exclude_media';
        $fields[] = 'media_date';

        return $fields;
    }

    /**
     * Add extra CLI args used by the Media Files addon.
     * Hooks on: wpmdb_cli_filter_get_extra_args
     *
     * @param array $args
     *
     * @return array
     */
    public function filter_extra_args($args)
    {
        $args[] = 'media';
        $args[] = 'exclude-media';
        $args[] = 'media-date';

        return $args;
    }

    /**
     * Adds extra args for Media Files migrations.
     * Hooks on: wpmdb_cli_filter_get_profile_data_from_args
     *
     * @param array $profile
     * @param array $args
     * @param array $assoc_args
     *
     * @return array|WP_Error
     */
    public function add_mf_profile_args($profile, $args, $assoc_args)
    {
        if (!isset($assoc_args['media'])) {
            return $profile; // Not a media files migration.
        }

        if (!in_array($assoc_args['media'], ['all', 'since-date'])) {
            return $this->cli->cli_error(__('--media must be set to an acceptable value, see: wp help migratedb ' . $assoc_args['action'], 'wp-migrate-db-pro-media-files'));
        }

        $media_files = [
            'enabled'          => true,
            'option'           => $assoc_args['media'],
            'version_mismatch' => false,
            'available'        => true,
        ];

        if ('since-date' === $assoc_args['media']) {
            if (!isset($assoc_args['media-date'])) {
                return $this->cli->cli_error(__('--media-date required when using --media=since-date, see: wp help migratedb ' . $assoc_args['action'], 'wp-migrate-db-pro-media-files'));
            }

            $media_date = $assoc_args['media-date'];
            $valid_date = false;

            if (preg_match('/^\d\d\d\d-\d\d-\d\d( \d\d:\d\d:\d\d)?$/', $media_date)) {
                $mm         = substr($media_date, 5, 2);
                $jj         = substr($media_date, 8, 2);
                $aa         = substr($media_date, 0, 4);
                $valid_date = wp_checkdate($mm, $jj, $aa, $media_date);
            }

            if (!$valid_date) {
                return $this->cli->cli_error(__('--media-date parameter received an invalid date format, see wp help migratedb ' . $assoc_args['action'], 'wp-migrate-db-pro-media-files'));
            }

            $media_files['date'] = $media_date;
        }

        if (!empty($assoc_args['exclude-media'])) {
            $media_files['excludes'] = str_replace(',', "\n", $assoc_args['exclude-media']);
        }

        $profile['media_files'] = $media_files;

        return $profile;
    }

    /**
     * Adds the Media Files stage to the current migration.
     *
     * @param array $profile
     *
     * @return array
     */
    public function add_mf_stage($profile)
    {
        if (is_wp_error($profile)) {
            return $profile;
        }

        if ($this->is_mf_migration($profile)) {
            $profile['current_migration']['stages'][] = 'media_files';
        }

        return $profile;
    }

    /**
     * Gets the correct folder based on the migration type.
     *
     * @param array $profile
     * @param array $post_data
     *
     * @return string
     */
    public function get_folder($profile, $post_data)
    {
        $site_details = json_decode($post_data['site_details'], true);

        if ('push' === $profile['action']) {
            $folder = $site_details['local']['uploads']['basedir'];
        } else {
            $folder = $site_details['remote']['uploads']['basedir'];
        }

        return apply_filters('wpmdb_cli_media_files_folder', $folder);
    }

    /**
     * Initialize the MF stage.
     *
     * @param array $profile
     * @param array $post_data
     *
     * @return array|WP_Error
     */
    public function initialize_mf_migration($profile, $post_data)
    {
        \WP_CLI::log(__('Initiating media migration...', 'wp-migrate-db-pro-media-files'));

        $date        = new \DateTime();
        $tz          = $date->getTimezone();
        $mf_options  = $profile['media_files'];

        $_POST = [
            'action'             => $profile['action'],
            'migration_state_id' => $profile['current_migration']['migration_id'],
            'folder'             => $this->get_folder($profile, $post_data),
            'date'               => null,
            'timezone'           => $tz->getName(),
            'stage'              => 'media_files',
        ];

        if (!empty($mf_options['excludes'])) {
            $_POST['excludes'] = json_encode($mf_options['excludes']);
        }

        if ('all' !== $mf_options['option'] && isset($mf_options['date'])) {
            $_POST['date'] = $mf_options['date'];
        }

        $response = $this->media_files_local->ajax_initiate_media_file_migration();

        return $this->cli->verify_cli_response($response, 'initialize_mf_migration()');
    }

    /**
     * Transfers files during the MF stage.
     *
     * @param array $profile
     * @param array $post_data
     *
     * @return array|WP_Error
     */
    public function mf_transfer_files($profile, $post_data)
    {
        $_POST = [
            'action'             => $profile['action'],
            'stage'              => 'media_files',
            'migration_state_id' => $profile['current_migration']['migration_id'],
        ];

        $response = $this->media_files_local->ajax_mf_transfer_files();

        return $this->cli->verify_cli_response($response, 'tansfer_mf_files()');
    }

    /**
     * Run the media migration from the CLI.
     *
     * @param bool  $outcome
     * @param array $profile
     * @param array $verify_connection_response
     * @param array $post_data
     *
     * @return bool|WP_Error
     */
    public function cli_migration($outcome, $profile, $verify_connection_response, $post_data)
    {
        if (true !== $outcome || !$this->is_mf_migration($profile)) {
            return $outcome;
        }

        if (!isset($verify_connection_response['media_files_max_file_uploads'])) {
            return $this->cli->cli_error(__('WP Migrate DB Pro Media Files does not seem to be installed/active on the remote website.', 'wp-migrate-db-pro-media-files'));
        }

        $intent = $profile['action'];

        // Kick off the Media Files stage.
        $mf_initialize_response = $this->initialize_mf_migration($profile, $post_data);
        if (is_wp_error($mf_initialize_response)) {
            return $mf_initialize_response;
        }

        $queue_status = $mf_initialize_response['queue_status'];
        $total_size   = isset($queue_status['size']) ? (int) $queue_status['size'] : 0;

        $migrate_bar = $this->make_progress_bar($this->get_string('migrate_media_files_' . $intent), 0);
        $migrate_bar->setTotal($total_size);

        $result = ['status' => 0];
        while (!is_wp_error($result) && $result['status'] !== 'complete') {
            // Delay between requests
            do_action('wpmdb_media_files_cli_before_migrate_media');

            // Migrate the files.
            $result = $this->mf_transfer_files($profile, $post_data);

            if (isset($result['status']['error'])) {
                return new \WP_Error('wpmdb_cli_mf_migration_failed', $result['status']['message']);
            }

            $batch_size = is_array($result['status']) ? array_sum(array_column($result['status'], 'batch_size')) : 0;

            // Update progress.
            $migrate_bar->tick($batch_size);
        }

        if (is_wp_error($result)) {
            return $result;
        }

        // Finish things up.
        $migrate_bar->finish();

        return true;
    }

    /**
     * Like WP_CLI\Utils\make_progress_bar, but uses our own wrapper classes
     *
     * @param $message
     * @param $count
     *
     * @return MediaFilesCliBar|MediaFilesCliBarNoOp
     */
    public function make_progress_bar($message, $count)
    {
        if (method_exists('cli\Shell', 'isPiped') && \cli\Shell::isPiped()) {
            return new MediaFilesCliBarNoOp();
        }

        return new  MediaFilesCliBar($message, $count);
    }
}
