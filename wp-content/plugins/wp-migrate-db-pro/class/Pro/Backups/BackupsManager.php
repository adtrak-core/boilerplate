<?php

namespace DeliciousBrains\WPMDB\Pro\Backups;

use DeliciousBrains\WPMDB\Common\Filesystem\Filesystem;
use DeliciousBrains\WPMDB\Common\Http\Helper;
use DeliciousBrains\WPMDB\Common\Http\WPMDBRestAPIServer;

class BackupsManager
{
	/**
	 * @var Helper
	 */
	private $http_helper;
	/**
	 * @var Filesystem
	 */
	private $filesystem;
	/**
	 * @var WPMDBRestAPIServer
	 */
	private $rest_API_server;

	/**
	 * BackupsManager constructor.
	 *
	 * @param Helper             $http_helper
	 * @param Filesystem         $filesystem
	 * @param WPMDBRestAPIServer $rest_API_server
	 */
	public function __construct(
		Helper $http_helper,
		Filesystem $filesystem,
		WPMDBRestAPIServer $rest_API_server
	) {
		$this->http_helper = $http_helper;
		$this->filesystem = $filesystem;
		$this->rest_API_server = $rest_API_server;
	}

	public function register()
	{
		add_action('rest_api_init', [$this, 'register_rest_routes']);
		add_action('admin_init', [$this, 'trigger_download']);
	}

	public function register_rest_routes()
	{
		$this->rest_API_server->registerRestRoute(
			'/get-backups',
			[
				'methods'  => 'POST',
				'callback' => [$this, 'ajax_get_backups'],
			]
		);
		$this->rest_API_server->registerRestRoute(
			'/get-backup',
			[
				'methods'  => 'POST',
				'callback' => [$this, 'ajax_get_backup'],
			]
		);
		$this->rest_API_server->registerRestRoute(
			'/delete-backup',
			[
				'methods'  => 'POST',
				'callback' => [$this, 'ajax_delete_backup'],
			]
		);
	}

	public function trigger_download()
	{
		if (!isset($_GET['wpmdb-download-backup'])) {
			return false;
		}

		$backup = filter_input(INPUT_GET, 'wpmdb-download-backup', FILTER_SANITIZE_STRING);
		$is_compressed = (bool) filter_input(INPUT_GET, 'wpmdb-compressed-backup', FILTER_VALIDATE_BOOLEAN);
		if (empty($backup)) {
			wp_die(__('Backup not found.', 'wp-migrate-db'));
		}

		$this->download_backup($backup, $is_compressed);
	}

	public function download_backup($backup, $is_compressed = false)
	{
		$backup_dir = $this->filesystem->get_upload_info('path') . DIRECTORY_SEPARATOR;
		$ext        = ($is_compressed ? '.sql.gz' : '.sql');
		$diskfile   = $backup_dir . $backup;
		$diskfile  .= $ext;

		if (!file_exists($diskfile)) {
			wp_die(__('Could not find backup file to download:', 'wp-migrate-db') . '<br>' . esc_html($diskfile));
		}

		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Length: ' . $this->filesystem->filesize($diskfile));
		header('Content-Disposition: attachment; filename=' . $backup . $ext);
		readfile($diskfile);
		exit;
	}

	public function ajax_get_backups()
	{
		$this->http_helper->convert_json_body_to_post();
		$backups = $this->filesystem->get_backups();
		$backups = is_array($backups) ? $backups : array();
		wp_send_json_success($backups);
	}

	public function ajax_get_backup()
	{
		$_POST          = $this->http_helper->convert_json_body_to_post();
		$path           = isset($_POST['path']) ? sanitize_file_name($_POST['path']) : '';
		$is_compressed  = isset($_POST['isCompressed']) ? (bool) $_POST['isCompressed'] : false;
		$backup_dir     = $this->filesystem->get_upload_info('path') . DIRECTORY_SEPARATOR;
		$file_path       = $backup_dir . $path . ($is_compressed ? '.sql.gz' : '.sql');

		if (!file_exists($file_path)) {
			$error = sprintf(__('File does not exist — %s', 'wp-migrate-db'), $file_path);
			wp_send_json_error($error);
		}

		$redirect_query = [
			'page'                  => 'wp-migrate-db-pro',
			'wpmdb-download-backup' => $path,
			'wpmdb-compressed-backup' => $is_compressed
		];

		$path     = is_multisite() ? 'settings.php' : 'tools.php';
		$redirect = add_query_arg($redirect_query, network_admin_url($path));

		wp_send_json_success(['redirect' => $redirect]);
	}

	public function ajax_delete_backup()
	{
		$_POST      = $this->http_helper->convert_json_body_to_post();
		$path       = isset($_POST['path']) ? sanitize_file_name($_POST['path']) : '';
		$is_compressed  = isset($_POST['isCompressed']) ? (bool) $_POST['isCompressed'] : false;
		$backup_dir = $this->filesystem->get_upload_info('path') . DIRECTORY_SEPARATOR;
		$file_path  = $backup_dir . $path . ($is_compressed ? '.sql.gz' : '.sql');
		if (!file_exists($file_path)) {
			$error = sprintf(__('File does not exist — %s', 'wp-migrate-db'), $file_path);
			wp_send_json_error($error);
		}

		$deleted = $this->filesystem->unlink($file_path);

		if (!$deleted) {
			$error = sprintf(__('Unable to delete file — %s', 'wp-migrate-db'), $file_path);
			wp_send_json_error($error);
		}

		wp_send_json_success();
	}
}
