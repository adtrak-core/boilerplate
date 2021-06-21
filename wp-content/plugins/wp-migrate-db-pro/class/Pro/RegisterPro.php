<?php

namespace DeliciousBrains\WPMDB\Pro;

use DeliciousBrains\WPMDB\Common\Compatibility\CompatibilityManager;
use DeliciousBrains\WPMDB\Common\Filesystem\Filesystem;
use DeliciousBrains\WPMDB\Common\Migration\MigrationManager;
use DeliciousBrains\WPMDB\Common\Plugin\Assets;
use DeliciousBrains\WPMDB\Common\Plugin\Menu;
use DeliciousBrains\WPMDB\Common\Plugin\PluginManagerBase;
use DeliciousBrains\WPMDB\Common\Properties\Properties;
use DeliciousBrains\WPMDB\Common\Util\Util;
use DeliciousBrains\WPMDB\Pro\Addon\Addon;
use DeliciousBrains\WPMDB\Pro\Backups\BackupsManager;
use DeliciousBrains\WPMDB\Pro\Beta\BetaManager;
use DeliciousBrains\WPMDB\Pro\Cli\Export;
use DeliciousBrains\WPMDB\Pro\Migration\Connection\Local;
use DeliciousBrains\WPMDB\Pro\Migration\Connection\Remote;
use DeliciousBrains\WPMDB\Pro\Migration\FinalizeComplete;
use DeliciousBrains\WPMDB\Pro\Plugin\ProPluginManager;
use DeliciousBrains\WPMDB\Pro\RemoteUpdates\RemoteUpdatesManager;
use DeliciousBrains\WPMDB\Pro\UI\Template;
use DeliciousBrains\WPMDB\WPMDBDI;

class RegisterPro
{

    /**
     * @var MigrationManager
     */
    private $migration_manager;
    /**
     * @var UsageTracking
     */
    private $usage_tracking;
    /**
     * @var Template
     */
    private $template;
    /**
     * @var License
     */
    private $license;
    /**
     * @var $import
     */
    private $import;
    /**
     * @var Addon
     */
    private $addon;
    /**
     * @var BetaManager
     */
    private $beta_manager;
    /**
     * @var ProPluginManager
     */
    private $pro_plugin_manager;
    /**
     * @var Menu
     */
    private $menu;
    /**
     * @var BackupsManager
     */
    private $backups_manager;
    /**
     * @var Export
     */
    private $cli_export;
    /**
     * @var FinalizeComplete
     */
    private $finalize_complete;
    /**
     * @var Local
     */
    private $local_connection;
    /**
     * @var Remote
     */
    private $remote_connection;
    /**
     * @var Migration\Tables\Remote
     */
    private $remote_table;

    /**
     * @var Flush
     */
    private $flush;

    public function register()
    {
        $container = WPMDBDI::getInstance();

        $filesystem = $container->get(Filesystem::class);
        $filesystem->register();

        //        $this->pro_migration_manager = $container->get(RespondToMigrationAction::class);
        $container->set(
            Menu::class,
            new Menu(
                $container->get(Util::class),
                $container->get(Properties::class),
                $container->get(PluginManagerBase::class),
                $container->get(Assets::class),
                $container->get(CompatibilityManager::class)
            )
        );

        $this->remote_table = $container->get(Migration\Tables\Remote::class);

        $this->local_connection       = $container->get(Local::class);
        $this->remote_connection      = $container->get(Remote::class);
        $this->finalize_complete      = $container->get(FinalizeComplete::class);
        $this->migration_manager      = $container->get(MigrationManager::class);
        $this->template               = $container->get(Template::class);
        $this->license                = $container->get(License::class);
        $this->import                 = $container->get(Import::class);
        $this->addon                  = $container->get(Addon::class);
        $this->beta_manager           = $container->get(BetaManager::class);
        $this->pro_plugin_manager     = $container->get(ProPluginManager::class);
        $this->menu                   = $container->get(Menu::class);
        $this->usage_tracking         = $container->get(UsageTracking::class);
        $this->backups_manager        = $container->get(BackupsManager::class);
        $this->cli_export             = $container->get(Export::class);
        $this->remote_updates_manager = $container->get(RemoteUpdatesManager::class);

        // Register other class actions and filters
        $this->local_connection->register();
        $this->remote_connection->register();
        $this->remote_table->register();
        $this->finalize_complete->register();
        $this->migration_manager->register();
        $this->template->register();
        $this->license->register();
        $this->import->register();
        $this->addon->register();
        $this->beta_manager->register();
        $this->pro_plugin_manager->register();
        $this->menu->register();
        $this->usage_tracking->register();
        $this->backups_manager->register();
        $this->remote_updates_manager->register();

        if (!class_exists('\DeliciousBrains\WPMDBCli\Cli')) {
            $this->cli_export->register();
        }
    }

    // @TODO remove once enough users off of 1.9.* branch
    public function loadContainer() { }

    public function loadTransfersContainer() { }
}
