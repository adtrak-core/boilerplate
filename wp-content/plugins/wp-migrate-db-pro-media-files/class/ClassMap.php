<?php

namespace DeliciousBrains\WPMDBMF;

use DeliciousBrains\WPMDBMF\CliCommand\MediaFilesCli;

class ClassMap extends \DeliciousBrains\WPMDB\Pro\ClassMap
{

    public $media_files_addon;
    public $media_files_cli;
    public $media_files_addon_remote;
    public $media_files_addon_local;
    public $media_files_addon_base;

    public function __construct()
    {
        parent::__construct();

        $this->media_files_addon = new MediaFilesAddon(
            $this->addon,
            $this->properties,
            $this->util,
            $this->transfers_util,
            $this->filesystem
        );

        $this->media_files_addon_local = new MediaFilesLocal(
            $this->form_data,
            $this->http,
            $this->util,
            $this->http_helper,
            $this->WPMDBRestAPIServer,
            $this->transfers_manager,
            $this->transfers_util,
            $this->transfers_file_processor,
            $this->transfers_queue_helper,
            $this->queue_manager,
            $this->transfers_plugin_helper,
            $this->profile_manager
        );

        $this->media_files_addon_remote = new MediaFilesRemote(
            $this->transfers_plugin_helper
        );

        $this->media_files_cli = new MediaFilesCli(
            $this->addon,
            $this->properties,
            $this->cli,
            $this->cli_manager,
            $this->util,
            $this->state_data_container,
            $this->transfers_util,
            $this->filesystem
        );
    }
}
