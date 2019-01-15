<?php

namespace DeliciousBrains\WPMDBMF;

use DeliciousBrains\WPMDB\Container;

class Initialize {

	public function __construct() { }

	public function registerAddon() {
		$container = Container::getInstance();
		$container->add( 'media_files_addon', 'DeliciousBrains\WPMDBMF\MediaFilesAddon' )
		          ->withArguments( [
			          'addon',
			          'properties',
			          'dynamic_properties',
			          'template',
		          ] );

		$container->add( 'media_files_addon_base', 'DeliciousBrains\WPMDBMF\MediaFilesBase' )
		          ->withArguments( [
			          'filesystem',
			          'migration_state_manager',
			          'form_data',
		          ] );

		$container->add( 'media_files_addon_local', 'DeliciousBrains\WPMDBMF\MediaFilesLocal' )
		          ->withArguments( [
			          'filesystem',
			          'migration_state_manager',
			          'form_data',
			          'http',
			          'settings',
			          'util',
			          'http_helper',
			          'remote_post',
			          'error_log',
			          'state_data_container',
		          ] );

		$container->add( 'media_files_addon_remote', 'DeliciousBrains\WPMDBMF\MediaFilesRemote' )
		          ->withArguments( [
			          'filesystem',
			          'migration_state_manager',
			          'form_data',
			          'http',
			          'settings',
			          'util',
			          'http_helper',
			          'error_log',
			          'properties',
			          'scramble',
		          ] );

		$container->add( 'media_files_cli', 'DeliciousBrains\WPMDBMF\CliCommand\MediaFilesCli' )
		          ->withArguments( [
			          'addon',
			          'properties',
			          'dynamic_properties',
			          'template',
			          'cli',
			          'cli_manager',
			          'util',
			          'state_data_container',
		          ] );
	}
}
