<?php

use DeliciousBrains\WPMDB\WPMDBDI;
use DeliciousBrains\WPMDBMF\CliCommand\MediaFilesCli;
use DeliciousBrains\WPMDBMF\MediaFilesAddon;
use DeliciousBrains\WPMDBMF\MediaFilesLocal;
use DeliciousBrains\WPMDBMF\MediaFilesRemote;

function wpmdb_setup_media_files_addon()
{
	global $wpmdbpro_media_files;

	if (!is_null($wpmdbpro_media_files)) {
		return $wpmdbpro_media_files;
	}

	$container = WPMDBDI::getInstance();
	$container->get(MediaFilesAddon::class)->register();
	$container->get(MediaFilesLocal::class)->register();
	$container->get(MediaFilesRemote::class)->register();
	$container->get(MediaFilesCli::class)->register();

	if (!function_exists('wp_migrate_db_pro_loaded') || !wp_migrate_db_pro_loaded()) {
		return false;
	}

	$wpmdbpro_media_files = $container->get(MediaFilesAddon::class);

	load_plugin_textdomain('wp-migrate-db-pro-media-files', false, dirname(plugin_basename(__FILE__)) . '/languages/');

	return $wpmdbpro_media_files;
}
