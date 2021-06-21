<?php


namespace DeliciousBrains\WPMDB\Pro\Transfers\Files;


use DeliciousBrains\WPMDB\Common\Error\ErrorLog;
use DeliciousBrains\WPMDB\Common\Filesystem\Filesystem;
use DeliciousBrains\WPMDB\Common\Http\Helper;
use DeliciousBrains\WPMDB\Common\Http\Http;
use DeliciousBrains\WPMDB\Common\Http\RemotePost;
use DeliciousBrains\WPMDB\Common\Http\Scramble;
use DeliciousBrains\WPMDB\Common\MigrationPersistence\Persistence;
use DeliciousBrains\WPMDB\Common\MigrationState\MigrationStateManager;
use DeliciousBrains\WPMDB\Common\MigrationState\StateDataContainer;
use DeliciousBrains\WPMDB\Common\Properties\Properties;
use DeliciousBrains\WPMDB\Common\Settings\Settings;
use DeliciousBrains\WPMDB\Common\Util\ZipAndEncode;
use DeliciousBrains\WPMDB\Pro\Queue\Manager;
use DeliciousBrains\WPMDB\Pro\Transfers\Receiver;
use DeliciousBrains\WPMDB\Pro\Transfers\Sender;

class PluginHelper
{

    public $filesystem;
    /**
     * @var Http
     */
    private $http;
    /**
     * @var ErrorLog
     */
    private $error_log;
    /**
     * @var Helper
     */
    private $http_helper;
    /**
     * @var RemotePost
     */
    private $remote_post;
    /**
     * @var Settings
     */
    private $settings;
    /**
     * @var MigrationStateManager
     */
    private $migration_state_manager;
    /**
     * @var Scramble
     */
    private $scramble;
    /**
     * @var FileProcessor
     */
    private $file_processor;
    /**
     * @var Util
     */
    private $transfer_util;
    /**
     * @var Manager
     */
    private $queue_manager;
    /**
     * @var Properties
     */
    private $properties;
    /**
     * @var Sender
     */
    private $sender;
    /**
     * @var Receiver
     */
    private $receiver;
    /**
     * @var Manager
     */
    private $manager;
    /**
     * @var StateDataContainer
     */
    private $state_data_container;

    public function __construct(
        Filesystem $filesystem,
        Properties $properties,
        Http $http,
        Helper $http_helper,
        Settings $settings,
        MigrationStateManager $migration_state_manager,
        Scramble $scramble,
        FileProcessor $file_processor,
        Util $transfer_util,
        Manager $queue_manager,
        Sender $sender,
        Receiver $receiver,
        Manager $manager,
        StateDataContainer $state_data_container
    ) {
        $this->filesystem              = $filesystem;
        $this->http                    = $http;
        $this->http_helper             = $http_helper;
        $this->settings                = $settings->get_settings();
        $this->migration_state_manager = $migration_state_manager;
        $this->scramble                = $scramble;
        $this->file_processor          = $file_processor;
        $this->transfer_util           = $transfer_util;
        $this->properties              = $properties;
        $this->queue_manager           = $queue_manager;
        $this->sender                  = $sender;
        $this->receiver                = $receiver;
        $this->manager                 = $manager;
        $this->state_data_container    = $state_data_container;
    }


    /**
     * @param $stage
     *
     * @return mixed|null
     */
    public function respond_to_get_remote_folders($stage)
    {
        $key_rules         = array(
            'action'   => 'key',
            'intent'   => 'key',
            'folders'  => 'serialized',
            'excludes' => 'serialized',
            'stage'    => 'string',
            'sig'      => 'string',
            'date'     => 'string',
            'timezone' => 'string',
        );
        $_POST['folders']  = stripslashes($_POST['folders']);
        $_POST['excludes'] = stripslashes($_POST['excludes']);

        $state_data = Persistence::setRemotePostData($key_rules, __METHOD__);

        $filtered_post = $this->http_helper->filter_post_elements(
            $state_data,
            array(
                'action',
                'intent',
                'folders',
                'excludes',
                'stage',
            )
        );

        $verification = $this->http_helper->verify_signature($filtered_post, $this->settings['key']);

        if (!$verification) {
            return $this->http->end_ajax(new \WP_Error('wpmdbtpf_invalid_post_data', __('Could not validate $_POST data.') . ' (#100tp)'));
        }

        $abs_path = '';

        if ('plugins' === $stage) {
            $abs_path = WP_PLUGIN_DIR;
        }

        if ('themes' === $stage) {
            $abs_path = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR;
        }

        if ('media_files' === $stage) {
            $uploads_path = Util::get_wp_uploads_dir();
            $uploads_name = wp_basename($uploads_path);
            $abs_path     = apply_filters('wpmdb_mf_media_upload_path', WP_CONTENT_DIR . DIRECTORY_SEPARATOR . $uploads_name, $state_data);
        }

        $slashed  = $this->filesystem->slash_one_direction($abs_path);
        $date     = isset($_POST['date']) ? $state_data['date'] : null;
        $timezone = isset($_POST['timezone']) ? $state_data['timezone'] : null;

        $folders = unserialize($state_data['folders']);

        if ('media_files' === $stage) {
            $folders = apply_filters('wpmdb_mf_remote_uploads_folder', $folders, $state_data);
        }

        if (empty($folders)) {
            return $this->http->end_ajax(new \WP_Error('wpmdb_empty_folder_list', __('Empty folder path provided.', 'wp-migrate-db')));
        }

        $items = $folders;

        if ($stage === 'media_files' && isset($folders[0])) {
            $items = $this->get_top_level_items($folders[0]);
        }

        $files = $this->file_processor->get_local_files($items, $slashed, unserialize($state_data['excludes']), $stage, $date, $timezone);


        $files = ZipAndEncode::encode(json_encode($files));

        return $this->http->end_ajax($files);
    }

    /**
     *
     * Respond to request to save queue status
     *
     * @return mixed|null
     */
    public function respond_to_save_queue_status()
    {
        $key_rules = array(
            'action'          => 'key',
            'remote_state_id' => 'key',
            'stage'           => 'string',
            'intent'          => 'string',
            'sig'             => 'string',
        );

        $state_data = $this->migration_state_manager->set_post_data($key_rules);

        $filtered_post = $this->http_helper->filter_post_elements(
            $state_data,
            array(
                'action',
                'remote_state_id',
                'intent',
                'stage',
            )
        );

        $settings = $this->settings;

        if (!$this->http_helper->verify_signature($filtered_post, $settings['key'])) {
            return $this->transfer_util->ajax_error($this->properties->invalid_content_verification_error . ' (#100tp)', $filtered_post);
        }

        if (empty($_POST['queue_status'])) {
            return $this->transfer_util->ajax_error(__('Saving queue status to remote failed.'));
        }

        $queue_status = filter_var($_POST['queue_status'], FILTER_SANITIZE_STRING);
        $queue_data   = unserialize(gzdecode(base64_decode($queue_status)));

        if ($queue_data) {
            $this->transfer_util->remove_tmp_folder($state_data['stage']);

            try {
                $this->transfer_util->save_queue_status($queue_data, $state_data['stage'], $state_data['remote_state_id']);
            } catch (\Exception $e) {
                return $this->http->end_ajax(new \WP_Error('wpmdb_failed_save_queue', $e->getMessage()));
            }

            return $this->http->end_ajax(json_encode(true));
        }
    }

    /**
     *
     * Fired off a nopriv AJAX hook that listens to pull requests for file batches
     *
     * @return mixed
     */
    public function respond_to_request_files()
    {
        $key_rules = array(
            'action'          => 'key',
            'remote_state_id' => 'key',
            'stage'           => 'string',
            'intent'          => 'string',
            'bottleneck'      => 'numeric',
            'sig'             => 'string',
        );

        $state_data    = $this->migration_state_manager->set_post_data($key_rules, 'remote_state_id');
        $filtered_post = $this->http_helper->filter_post_elements(
            $state_data,
            array(
                'action',
                'remote_state_id',
                'stage',
                'intent',
                'bottleneck',
            )
        );

        $settings = $this->settings;

        if (!$this->http_helper->verify_signature($filtered_post, $settings['key'])) {
            return $this->transfer_util->ajax_error($this->properties->invalid_content_verification_error . ' (#100tp)', $filtered_post);
        }

        try {
            $this->sender->respond_to_send_file($state_data);
        } catch (\Exception $e) {
            $this->transfer_util->catch_general_error($e->getMessage());
        }
    }

    /**
     * @return null
     * @throws \Exception
     */
    public function respond_to_post_file()
    {
        $key_rules = array(
            'action'          => 'key',
            'remote_state_id' => 'key',
            'stage'           => 'string',
            'intent'          => 'string',
            'folders'         => 'array',
            'theme_folders'   => 'array',
            'plugin_folders'  => 'array',
            'sig'             => 'string',
        );

        //Sending ALL local state data, probably too much data and should be paired down
        $state_data = Persistence::setRemotePostData($key_rules, __METHOD__);

        $filtered_post = $this->http_helper->filter_post_elements(
            $state_data,
            array(
                'action',
                'remote_state_id',
                'stage',
                'intent',
            )
        );

        $settings = $this->settings;

        if (!isset($_POST['content']) || !$this->http_helper->verify_signature($filtered_post, $settings['key'])) {
            throw new \Exception(__('Failed to respond to payload post.', 'wp-migrate-db'));
        }

        $payload_content = filter_var($_POST['content'], FILTER_SANITIZE_STRING);
        $receiver        = $this->receiver;

        try {
            $receiver->receive_post_data($state_data, $payload_content);
        } catch (\Exception $e) {
            return $this->transfer_util->catch_general_error($e->getMessage());
        }
    }

    public function get_top_level_items($dir)
    {
        $file_data = $this->filesystem->scandir($dir);

        $items = [];

        if (!$file_data) {
            return false;
        }

        foreach ($file_data as $item) {
            $items[] = $item['absolute_path'];
        }

        return $items;
    }

    public function cleanup_transfer_migration($stage)
    {
        $this->manager->drop_tables();

        $this->remove_tmp_files($stage);
    }

    /**
     *
     * @param $stage
     */
    public function remove_tmp_files($stage, $env = 'local')
    {
        if (in_array($stage, ['themes', 'plugins'])) {
            $this->transfer_util->remove_tmp_folder('themes');
            $this->transfer_util->remove_tmp_folder('plugins');
        }

        if ($stage === 'media_files') {
            $this->transfer_util->remove_tmp_folder($stage);
        }

        $id = null;

        if ($env === 'local') {
            $state_data = Persistence::getStateData();
            $id         = isset($state_data['migration_state_id']) ? $state_data['migration_state_id'] : null;
        } else {
            $state_data = Persistence::getRemoteStateData();
            $id         = isset($state_data['remote_state_id']) ? $state_data['remote_state_id'] : null;
        }

        if ($id) {
            $this->remove_chunk_data($id, $env);
            $this->remove_folder_options($id);
        }

        return;
    }

    public function remove_folder_options($id)
    {
        delete_site_option('wpmdb_folder_transfers_media_files_' . $id);
        delete_site_option('wpmdb_folder_transfers_themes_' . $id);
        delete_site_option('wpmdb_folder_transfers_plugins_' . $id);
    }

    public function remove_chunk_data($id, $env)
    {
        if (!$id || $env !== 'local') {
            return;
        }

        $chunk_file = Chunker::get_chunk_path($id);
        if ($this->filesystem->file_exists($chunk_file)) {
            $this->filesystem->unlink($chunk_file);
        }

        $chunk_option_name = 'wpmdb_file_chunk_' . $id;
        delete_site_option($chunk_option_name);
    }

}
