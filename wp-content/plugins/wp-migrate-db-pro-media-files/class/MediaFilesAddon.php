<?php

namespace DeliciousBrains\WPMDBMF;

use DeliciousBrains\WPMDB\Common\Filesystem\Filesystem;
use DeliciousBrains\WPMDB\Common\Properties\Properties;
use DeliciousBrains\WPMDB\Common\Util\Util;
use DeliciousBrains\WPMDB\Pro\Addon\Addon;
use DeliciousBrains\WPMDB\Pro\Addon\AddonAbstract;
use DeliciousBrains\WPMDB\Pro\Queue\Manager;
use DeliciousBrains\WPMDB\Pro\UI\Template;
use DeliciousBrains\WPMDB\WPMDBDI;

/**
 * Class MediaFilesAddon
 *
 * @package DeliciousBrains\WPMDBMF
 */
class MediaFilesAddon extends AddonAbstract
{

	/**
	 * An array strings used for translations
	 *
	 * @var array $media_strings
	 */
	protected $media_strings;

	/**
	 * An instance of MediaFilesLocal
	 *
	 * @var MediaFilesLocal
	 */
	public $media_files_local;

	/**
	 * @var Template
	 */
	private $plugins_url;

	const MDB_VERSION_REQUIRED = '1.9.6';
	/**
	 * @var Util
	 */
	private $util;
	/**
	 * @var \DeliciousBrains\WPMDB\Pro\Transfers\Files\Util
	 */
	private $transfers_util;
	/**
	 * @var Filesystem
	 */
	private $filesystem;

	public function __construct(
		Addon $addon,
		Properties $properties,
		Util $util,
		\DeliciousBrains\WPMDB\Pro\Transfers\Files\Util $transfers_util,
		Filesystem $filesystem
	) {
		parent::__construct( $addon, $properties );

		$this->plugin_slug    = 'wp-migrate-db-pro-media-files';
		$this->plugin_version = $GLOBALS['wpmdb_meta']['wp-migrate-db-pro-media-files']['version'];
		$plugin_file_path     = dirname( __DIR__ ) . '/wp-migrate-db-pro-media-files.php';
		$plugin_dir_path      = plugin_dir_path( $plugin_file_path );
		$plugin_folder_name   = basename( $plugin_dir_path );

		// @TODO see if this works
		$this->plugins_url    = trailingslashit( plugins_url( $plugin_folder_name ) );
		$this->util           = $util;
		$this->transfers_util = $transfers_util;
		$this->filesystem     = $filesystem;
	}

	public function register()
	{
		if ( !$this->meets_version_requirements( self::MDB_VERSION_REQUIRED ) ) {
			return;
		}

		// Register Queue manager actions
		WPMDBDI::getInstance()->get( Manager::class )->register();

		$this->addon_name = $this->addon->get_plugin_name( 'wp-migrate-db-pro-media-files/wp-migrate-db-pro-media-files.php' );
		add_action( 'wpmdb_load_assets', array($this, 'load_assets') );

		add_filter( 'wpmdb_diagnostic_info', array($this, 'diagnostic_info') );
		add_filter( 'wpmdb_establish_remote_connection_data', array($this, 'establish_remote_connection_data') );
		add_filter( 'wpmdb_data', array($this, 'js_variables') );

		add_action( 'wpmdb_migration_complete', array($this, 'cleanup_transfer_migration') );
		add_filter( 'wpmdb_site_details', array($this, 'filter_site_details') );
	}

	/**
	 *
	 * Strings are used on the CLI
	 * Get translated strings for javascript and other functions
	 *
	 * @return array Array of translations
	 *
	 */
	function get_strings()
	{
		$strings = array(
			'migrate_media_files_pull'     => __( 'Downloading files', 'wp-migrate-db-pro-media-files' ),
			'migrate_media_files_push'     => __( 'Uploading files', 'wp-migrate-db-pro-media-files' )
		);

		if ( is_null( $this->media_strings ) ) {
			$this->media_strings = $strings;
		}

		return $this->media_strings;
	}

	/**
	 * Retrieve a specific translated string
	 *
	 * @param string $key Array key
	 *
	 * @return string Translation
	 */
	function get_string( $key )
	{
		$strings = $this->get_strings();

		return ( isset( $strings[$key] ) ) ? $strings[$key] : '';
	}

	/**
	 * Load media related assets in core plugin
	 */
	function load_assets()
	{
		$version = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? time() : $this->plugin_version;
		$src     = $this->plugins_url . "frontend/noop.js";

		wp_enqueue_script( 'wp-migrate-db-pro-media-files-script', $src, array(
			'jquery',
			'wp-migrate-db-pro-script-v2',
		), $version, true );

		wp_localize_script( 'wp-migrate-db-pro-media-files-script', 'wpmdbmf_strings', $this->get_strings() );
		wp_localize_script( 'wp-migrate-db-pro-media-files-script', 'wpmdbmf', [
			'enabled' => true,
		] );

		if ( $this->util->isMDBPage() ) {
			$src = $this->plugins_url . 'assets/noop.css';
			wp_enqueue_style( 'wp-migrate-db-pro-media-files-styles', $src, array('wp-components'), $version );
		}
	}

	/**
	 * Check the remote site has the media addon setup
	 *
	 * @param array $data Connection data
	 *
	 * @return array Updated connection data
	 */
	function establish_remote_connection_data( $data )
	{
		$data['media_files_available'] = '1';
		$data['media_files_version']   = $this->plugin_version;
		if ( function_exists( 'ini_get' ) ) {
			$max_file_uploads = ini_get( 'max_file_uploads' );
		}
		$max_file_uploads                     = ( empty( $max_file_uploads ) ) ? 20 : $max_file_uploads;
		$data['media_files_max_file_uploads'] = apply_filters( 'wpmdbmf_max_file_uploads', $max_file_uploads );

		return $data;
	}

	/**
	 * Add media related javascript variables to the page
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	function js_variables( $data )
	{
		$data['media_files_version'] = $this->plugin_version;

		return $data;
	}

	/**
	 * Adds extra information to the core plugin's diagnostic info
	 */
	function diagnostic_info( $diagnostic_info )
	{
		$diagnostic_info['media-files'] = array(
			"Media Files",
			'Transfer Bottleneck' => size_format( $this->transfers_util->get_transfer_bottleneck() ),
			'Upload Folder Permissions'  => decoct( fileperms( $this->filesystem->get_wp_upload_dir() ) & 0777 ),
		);

		return $diagnostic_info;
	}

	public function cleanup_transfer_migration()
	{
		$uploads = \DeliciousBrains\WPMDB\Pro\Transfers\Files\Util::get_wp_uploads_dir();

		$this->transfers_util->remove_manifests( $uploads );
	}

	/**
	 * @param $site_details
	 *
	 * @return mixed
	 */
	public function filter_site_details( $site_details )
	{
		if ( isset( $site_details['plugins'] ) ) {
			return $site_details;
		}

		if ( array_key_exists( 'max_request', $site_details ) && array_key_exists( 'transfer_bottleneck', $site_details ) ) {
			return $site_details;
		}

		$site_details['content_dir']         = $this->filesystem->slash_one_direction( WP_CONTENT_DIR );
		$site_details['transfer_bottleneck'] = $this->transfers_util->get_transfer_bottleneck();
		$site_details['max_request_size']    = $this->util->get_bottleneck();
		$site_details['php_os']              = PHP_OS;

		return $site_details;
	}
}
