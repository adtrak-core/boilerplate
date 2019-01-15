<?php

namespace DeliciousBrains\WPMDB\Pro;

use DeliciousBrains\WPMDB\Container;

class RegisterPro {

	/**
	 * @var
	 */
	private $pro_migration_manager;
	/**
	 * @var
	 */
	private $migration_manager;
	/**
	 * @var
	 */
	private $usage_tracking;
	/**
	 * @var
	 */
	private $template;
	/**
	 * @var
	 */
	private $license;
	/**
	 * @var
	 */
	private $import;
	/**
	 * @var
	 */
	private $addon;
	/**
	 * @var
	 */
	private $beta_manager;
	/**
	 * @var
	 */
	private $pro_plugin_manager;
	/**
	 * @var
	 */
	private $menu;
	/**
	 * @var mixed|object
	 */
	private $backups_manager;
	/**
	 * @see Addons and the Pro plugin call this
	 */
	public function loadContainer() {
		$container = Container::getInstance();

		$container->add( 'import', 'DeliciousBrains\WPMDB\Pro\Import' )
		          ->withArguments( [
			          'http',
			          'migration_state_manager',
			          'error_log',
			          'filesystem',
			          'backup_export',
			          'table',
			          'form_data',
			          'properties',
		          ] );

		//Api
		$container->add( 'api', 'DeliciousBrains\WPMDB\Pro\Api' )
		          ->withArguments( [
			          'util',
			          'settings',
			          'error_log',
			          'properties',
		          ] );

		//License
		$container->add( 'license', 'DeliciousBrains\WPMDB\Pro\License' )
		          ->withArguments( [
			          'api',
			          'settings',
			          'util',
			          'migration_state_manager',
			          'download',
			          'http',
			          'dynamic_properties',
			          'error_log',
			          'http_helper',
			          'scramble',
			          'remote_post',
			          'properties',
		          ] );

		// Addon
		$container->add( 'addon', 'DeliciousBrains\WPMDB\Pro\Addon\Addon' )
		          ->withArguments( [
			          'api',
			          'download',
			          'error_log',
			          'settings',
			          'properties',
		          ] );

		// Template
		$container->add( 'template', 'DeliciousBrains\WPMDB\Pro\UI\Template' )
		          ->withArguments( [
			          'settings',
			          'util',
			          'profile_manager',
			          'filesystem',
			          'table',
			          'notice',
			          'form_data',
			          'addon',
			          'dynamic_properties',
			          'license',
			          'properties',
			          'pro_plugin_manager'
		          ] );

		$container->add( 'usage_tracking', 'DeliciousBrains\WPMDB\Pro\UsageTracking' )
		          ->withArguments( [
			          'settings',
			          'license',
			          'filesystem',
			          'error_log',
			          'template',
			          'form_data',
			          'dynamic_properties',
			          'state_data_container',
			          'properties',
		          ] );

		// BetaManager
		$container->add( 'beta_manager', 'DeliciousBrains\WPMDB\Pro\Beta\BetaManager' )
		          ->withArguments( [
			          'util',
			          'addon',
			          'api',
			          'settings',
			          'template',
			          'download',
			          'properties',
		          ] );


		$container->add( 'connection', 'DeliciousBrains\WPMDB\Pro\Migration\Connection' )
		          ->withArguments( [
			          'scramble',
			          'migration_state_manager',
			          'http',
			          'http_helper',
			          'properties',
			          'error_log',
			          'license',
			          'remote_post',
			          'util',
			          'table',
			          'form_data',
			          'settings',
			          'filesystem',
			          'dynamic_properties',
			          'migration_state',
			          'multisite',
			          'table_helper',
		          ] );

		$container->add( 'finalize_complete', 'DeliciousBrains\WPMDB\Pro\Migration\FinalizeComplete' )
		          ->withArguments( [
			          'scramble',
			          'migration_state_manager',
			          'http',
			          'http_helper',
			          'properties',
			          'error_log',
			          'migration_manager',
			          'form_data',
			          'finalize_migration',
			          'settings',
		          ] );

		//ProMigrationManager
		$container->add( 'pro_migration_manager', 'DeliciousBrains\WPMDB\Pro\Migration\ProMigrationManager' )
		          ->withArguments( [
			          'scramble',
			          'settings',
			          'migration_state_manager',
			          'http',
			          'http_helper',
			          'error_log',
			          'properties',
			          'form_data',
			          'migration_manager',
			          'table',
			          'backup_export',
			          'connection',
			          'finalize_complete',
		          ] );

		$container->add( 'pro_plugin_manager', 'DeliciousBrains\WPMDB\Pro\Plugin\ProPluginManager' )
		          ->withArguments( [
			          'settings',
			          'assets',
			          'util',
			          'table',
			          'http',
			          'filesystem',
			          'multisite',
			          'license',
			          'api',
			          'addon',
			          'download',
			          'properties',
//			          'template_base'
		          ] );

		$container->add( 'download', 'DeliciousBrains\WPMDB\Pro\Download' )
		          ->withArguments( [
			          'properties',
			          'settings',
		          ] );

		$container->add( 'cli_export', 'DeliciousBrains\WPMDB\Pro\Cli\Export' )
		          ->withArguments( [
			          'form_data',
			          'util',
			          'cli_manager',
			          'table',
			          'error_log',
			          'initiate_migration',
			          'finalize_migration',
			          'http_helper',
			          'migration_manager',
			          'migration_state_manager',
			          'dynamic_properties',
		          ] );
		//Backups
		$container->add( 'backups_manager', 'DeliciousBrains\WPMDB\Pro\Backups\BackupsManager' )
		          ->withArguments( [
			          'http',
			          'filesystem'
		          ] );
	}

	/**
	 *
	 */
	public function loadTransfersContainer() {
		$container = Container::getInstance();

		//Receiver
		$container->add( 'transfers_receiver', 'DeliciousBrains\WPMDB\Pro\Transfers\Receiver' )
		          ->withArguments( [
			          'transfers_files_util',
			          'transfers_files_payload',
			          'settings',
			          'error_log',
			          'filesystem',
		          ] );

		// Sender
		$container->add( 'transfers_sender', 'DeliciousBrains\WPMDB\Pro\Transfers\Sender' )
		          ->withArguments( [
			          'transfers_files_util',
			          'transfers_files_payload',
		          ] );

		// Register File transfer classes

		$container->add( 'transfers_files_util', 'DeliciousBrains\WPMDB\Pro\Transfers\Files\Util' )
		          ->withArguments( [
			          'filesystem',
			          'http',
			          'error_log',
			          'http_helper',
			          'remote_post',
			          'settings',
			          'migration_state_manager',
		          ] );

		$container->add( 'transfers_files_chunker', 'DeliciousBrains\WPMDB\Pro\Transfers\Files\Chunker' )
		          ->withArguments( [
			          'transfers_files_util',
		          ] );

		$container->add( 'transfers_files_excludes', 'DeliciousBrains\WPMDB\Pro\Transfers\Files\Excludes' );

		$container->add( 'transfers_files_file_processor', 'DeliciousBrains\WPMDB\Pro\Transfers\Files\FileProcessor' )
		          ->withArguments( [
			          'filesystem',
		          ] );

		$container->add( 'transfers_files_payload', 'DeliciousBrains\WPMDB\Pro\Transfers\Files\Payload' )
		          ->withArguments( [
			          'transfers_files_util',
			          'transfers_files_chunker',
			          'filesystem',
			          'http',
		          ] );

		$container->add( 'transfers_files_transfer_manager', 'DeliciousBrains\WPMDB\Pro\Transfers\Files\TransferManager' )
		          ->withArguments( [
			          'queue_manager',
			          'transfers_files_payload',
			          'transfers_files_util',
			          'http_helper',
			          'transfers_receiver',
			          'transfers_sender',
		          ] );

		// Register queue classes
		$container->add( 'queue_manager', 'DeliciousBrains\WPMDB\Pro\Queue\Manager' )
		          ->withArguments( [
			          'properties',
			          'state_data_container',
			          'migration_state_manager',
			          'form_data',
		          ] );
	}

	public function register() {
		$container                   = Container::getInstance();
		$this->pro_migration_manager = $container->get( 'pro_migration_manager' );
		$this->migration_manager     = $container->get( 'migration_manager' );
		$this->template              = $container->get( 'template' );
		$this->license               = $container->get( 'license' );
		$this->import                = $container->get( 'import' );
		$this->addon                 = $container->get( 'addon' );
		$this->beta_manager          = $container->get( 'beta_manager' );
		$this->pro_plugin_manager    = $container->get( 'pro_plugin_manager' );
		$this->menu                  = $container->get( 'menu' );
		$this->usage_tracking        = $container->get( 'usage_tracking' );
		$this->backups_manager       = $container->get( 'backups_manager' );

		// Register other class actions and filters
		$this->pro_migration_manager->register();
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
	}
}
