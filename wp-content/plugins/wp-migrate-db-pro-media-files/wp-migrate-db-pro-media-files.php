<?php
/*
Plugin Name: WP Migrate DB Pro Media Files
Plugin URI: https://deliciousbrains.com/wp-migrate-db-pro/
Description: An extension to WP Migrate DB Pro, allows the migration of media files.
Author: Delicious Brains
Version: 2.0.1
Author URI: https://deliciousbrains.com
Network: True
*/

// Copyright (c) 2013 Delicious Brains. All rights reserved.
//
// Released under the GPL license
// http://www.opensource.org/licenses/gpl-license.php
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// **********************************************************************

require_once 'version.php';
$GLOBALS['wpmdb_meta']['wp-migrate-db-pro-media-files']['folder'] = basename(plugin_dir_path(__FILE__));

const WPMDB_MF_REQUIRED_CORE_VERSION = '2.0';

function get_mdb_version_mf()
{
	$path = __DIR__ . '/../wp-migrate-db-pro/version.php';
	if (!file_exists($path)) {
		return false;
	}

	require_once __DIR__ . '/../wp-migrate-db-pro/version.php';

	return $GLOBALS['wpmdb_meta']['wp-migrate-db-pro']['version'];
}

if (version_compare(PHP_VERSION, '5.6', '>=')) {
	$mdbVersion = get_mdb_version_mf();

	if ($mdbVersion && version_compare($mdbVersion, WPMDB_MF_REQUIRED_CORE_VERSION, '>=')) {
		require_once __DIR__ . '/class/autoload.php';
		require_once __DIR__ . '/setup.php';
	}
}

/**
 * Populate the $wpmdbpro_media_files global with an instance of the WPMDBPro_Media_Files class and return it.
 *
 * @return \DeliciousBrains\WPMDBMF\MediaFilesAddon The one true global instance of the WPMDBPro_Media_Files class.
 */
function wp_migrate_db_pro_media_files()
{
	if (!class_exists('\DeliciousBrains\WPMDB\Pro\WPMigrateDBPro')) {
		return;
	}

	if (function_exists('wp_migrate_db_pro')) {
		wp_migrate_db_pro();
	} else {
		return false;
	}

	if (function_exists('wpmdb_setup_media_files_addon')) {
		return wpmdb_setup_media_files_addon();
	}
}

/**
 * By default load plugin on admin pages, a little later than WP Migrate DB Pro.
 */
add_action('plugins_loaded', 'wp_migrate_db_pro_media_files', 20);
