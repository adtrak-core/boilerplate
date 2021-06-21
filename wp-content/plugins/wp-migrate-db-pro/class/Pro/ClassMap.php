<?php

namespace DeliciousBrains\WPMDB\Pro;

use DeliciousBrains\WPMDB\Pro\Backups\BackupsManager;
use DeliciousBrains\WPMDB\Pro\Beta\BetaManager;
use DeliciousBrains\WPMDB\Pro\Cli\Export;
use DeliciousBrains\WPMDB\Pro\Migration\Flush;
use DeliciousBrains\WPMDB\Pro\Migration\Connection;
use DeliciousBrains\WPMDB\Pro\Migration\FinalizeComplete;
use DeliciousBrains\WPMDB\Pro\Migration\Tables\Local;
use DeliciousBrains\WPMDB\Pro\Migration\Tables\Remote;
use DeliciousBrains\WPMDB\Pro\Plugin\ProPluginManager;
use DeliciousBrains\WPMDB\Pro\Queue\Manager;
use DeliciousBrains\WPMDB\Pro\Queue\QueueHelper;
use DeliciousBrains\WPMDB\Pro\Transfers\Files\Chunker;
use DeliciousBrains\WPMDB\Pro\Transfers\Files\Excludes;
use DeliciousBrains\WPMDB\Pro\Transfers\Files\FileProcessor;
use DeliciousBrains\WPMDB\Pro\Transfers\Files\Payload;
use DeliciousBrains\WPMDB\Pro\Transfers\Files\PluginHelper;
use DeliciousBrains\WPMDB\Pro\Transfers\Files\TransferManager;
use DeliciousBrains\WPMDB\Pro\Transfers\Files\Util;
use DeliciousBrains\WPMDB\Pro\Transfers\Receiver;
use DeliciousBrains\WPMDB\Pro\Transfers\Sender;
use DeliciousBrains\WPMDB\Pro\UI\Template;
use DeliciousBrains\WPMDB\Pro\RemoteUpdates\RemoteUpdatesManager;

class ClassMap extends \DeliciousBrains\WPMDB\ClassMap
{

    public $import;
    public $api;
    public $license;
    public $download;
    public $addon;
    public $template;
    public $pro_plugin_manager;
    public $usage_tracking;
    public $beta_manager;
    public $connection;
    public $finalize_complete;
    public $backups_manager;
    public $cli_export;
    public $transfers_util;
    public $transfers_chunker;
    public $transfers_payload;
    public $transfers_receiver;
    public $transfers_sender;
    public $transfers_excludes;
    public $queue_manager;
    public $transfers_manager;
    public $transfers_file_processor;
    public $common_flush;
    /**
     * @var Remote
     */
    public $remote_tables;
    /**
     * @var Local
     */
    public $local_tables;
    /**
     * @var Connection\Remote
     */
    public $remote_connection;
    /**
     * @var Connection\Local
     */
    public $local_connection;
    /**
     * @var PluginHelper
     */
    public $transfers_plugin_helper;
    /**
     * @var QueueHelper
     */
    public $transfers_queue_helper;

    public function __construct()
    {
        parent::__construct();

        $this->import = new Import(
            $this->http,
            $this->migration_state_manager,
            $this->error_log,
            $this->filesystem,
            $this->backup_export,
            $this->table,
            $this->form_data,
            $this->properties,
            $this->WPMDBRestAPIServer,
            $this->http_helper
        );

        $this->download = new Download(
            $this->properties,
            $this->settings
        );

        $this->addon = new Addon\Addon(
            $this->download,
            $this->error_log,
            $this->settings,
            $this->properties
        );

        $this->common_flush = new \DeliciousBrains\WPMDB\Common\Migration\Flush($this->http_helper, $this->util, $this->remote_post, $this->http);
        $this->flush = new Flush($this->http_helper, $this->util, $this->remote_post, $this->http);

        $this->pro_plugin_manager = new ProPluginManager(
            $this->settings,
            $this->assets,
            $this->util,
            $this->table,
            $this->http,
            $this->filesystem,
            $this->multisite,
            $this->addon,
            $this->download,
            $this->properties,
            $this->migration_helper,
            $this->WPMDBRestAPIServer,
            $this->http_helper,
            $this->template_base,
            $this->notice
        );

        $this->template = new Template(
            $this->settings,
            $this->util,
            $this->profile_manager,
            $this->filesystem,
            $this->table,
            $this->notice,
            $this->form_data,
            $this->addon,
            $this->properties,
            $this->pro_plugin_manager
        );

        $this->usage_tracking = new UsageTracking(
            $this->settings,
            $this->filesystem,
            $this->error_log,
            $this->template,
            $this->form_data,
            $this->state_data_container,
            $this->properties,
            $this->migration_state_manager,
            $this->http,
            $this->http_helper,
            $this->WPMDBRestAPIServer
        );

        $this->api = new Api(
            $this->util,
            $this->settings,
            $this->error_log,
            $this->properties,
            $this->usage_tracking
        );

        $this->license = new License(
            $this->api,
            $this->settings,
            $this->util,
            $this->migration_state_manager,
            $this->download,
            $this->http,
            $this->error_log,
            $this->http_helper,
            $this->scrambler,
            $this->remote_post,
            $this->properties,
            $this->WPMDBRestAPIServer
        );

        $this->beta_manager     = new BetaManager(
            $this->util,
            $this->addon,
            $this->api,
            $this->settings,
            $this->template,
            $this->download,
            $this->properties
        );
        $this->local_connection = new Connection\Local(
            $this->http,
            $this->http_helper,
            $this->properties,
            $this->license,
            $this->remote_post,
            $this->util,
            $this->WPMDBRestAPIServer
        );

        $this->remote_connection = new Connection\Remote(
            $this->scrambler,
            $this->http,
            $this->http_helper,
            $this->properties,
            $this->error_log,
            $this->license,
            $this->remote_post,
            $this->util,
            $this->table,
            $this->form_data,
            $this->settings,
            $this->filesystem,
            $this->multisite,
            $this->table_helper,
            $this->backup_export
        );

        $this->local_tables = new Local();

        $this->finalize_complete = new FinalizeComplete(
            $this->scrambler,
            $this->migration_state_manager,
            $this->http,
            $this->http_helper,
            $this->properties,
            $this->error_log,
            $this->migration_manager,
            $this->form_data,
            $this->finalize_migration,
            $this->settings,
            $this->WPMDBRestAPIServer,
            $this->flush
        );

        $this->remote_tables = new Remote(
            $this->scrambler,
            $this->settings,
            $this->migration_state_manager,
            $this->http,
            $this->http_helper,
            $this->table_helper,
            $this->error_log,
            $this->properties,
            $this->form_data,
            $this->migration_manager,
            $this->table,
            $this->backup_export,
            $this->finalize_complete,
            $this->WPMDBRestAPIServer
        );

        $this->cli_export = new Export(
            $this->form_data,
            $this->util,
            $this->cli_manager,
            $this->table,
            $this->error_log,
            $this->initiate_migration,
            $this->finalize_migration,
            $this->http_helper,
            $this->migration_manager,
            $this->migration_state_manager
        );

        $this->backups_manager = new BackupsManager(
            $this->http_helper,
            $this->filesystem,
            $this->WPMDBRestAPIServer
        );

        $this->remote_updates_manager = new RemoteUpdatesManager(
            $this->http_helper,
            $this->http,
            $this->remote_post,
            $this->WPMDBRestAPIServer,
            $this->migration_state_manager,
            $this->properties,
            $this->settings,
            $this->util,
            $this->license
        );

        // Transfers classes

        $this->transfers_util = new Util(
            $this->filesystem,
            $this->http,
            $this->error_log,
            $this->http_helper,
            $this->remote_post,
            $this->settings,
            $this->migration_state_manager,
            $this->util
        );

        $this->transfers_chunker = new Chunker(
            $this->transfers_util
        );

        $this->transfers_payload = new Payload(
            $this->transfers_util,
            $this->transfers_chunker,
            $this->filesystem,
            $this->http,
            $this->util
        );

        $this->transfers_receiver = new Receiver(
            $this->transfers_util,
            $this->transfers_payload,
            $this->settings,
            $this->error_log,
            $this->filesystem
        );

        $this->transfers_sender = new Sender(
            $this->transfers_util,
            $this->transfers_payload
        );

        $this->transfers_excludes = new Excludes();

        $this->queue_manager = new Manager(
            $this->properties,
            $this->state_data_container,
            $this->migration_state_manager,
            $this->form_data
        );

        $this->transfers_manager = new TransferManager(
            $this->queue_manager,
            $this->transfers_payload,
            $this->transfers_util,
            $this->http_helper,
            $this->http,
            $this->transfers_receiver,
            $this->transfers_sender
        );

        $this->transfers_file_processor = new FileProcessor(
            $this->filesystem,
            $this->http
        );

        $this->transfers_plugin_helper = new PluginHelper(
            $this->filesystem,
            $this->properties,
            $this->http,
            $this->http_helper,
            $this->settings,
            $this->migration_state_manager,
            $this->scrambler,
            $this->transfers_file_processor,
            $this->transfers_util,
            $this->queue_manager,
            $this->transfers_sender,
            $this->transfers_receiver,
            $this->queue_manager,
            $this->state_data_container
        );

        $this->transfers_queue_helper = new QueueHelper(
            $this->filesystem,
            $this->http,
            $this->http_helper,
            $this->transfers_util,
            $this->queue_manager,
            $this->util
        );
    }
}
