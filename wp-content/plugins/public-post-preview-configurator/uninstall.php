<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   Public_Post_Preview_Configurator
 * @author    Björn Weinbrenner <info@bjoerne.com>
 * @license   GPLv3
 * @link      http://bjoerne.com
 * @copyright 2014 bjoerne.com
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

if ( is_multisite() ) {

	$blogs = $wpdb->get_results( "SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A );
	if ( $blogs ) {
		foreach ( $blogs as $blog ) {
			switch_to_blog( $blog['blog_id'] );
			delete_option( 'ppp_configurator_expiration_hours' );
			restore_current_blog();
		}
	}
} else {
	delete_option( 'ppp_configurator_expiration_hours' );
}
