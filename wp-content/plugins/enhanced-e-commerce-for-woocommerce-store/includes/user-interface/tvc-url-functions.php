<?php

/**
 * Checks if the user is on one of the plugin pages or not.
 * Also returns true when current page can not be defined
 *
 * @since 2.3.4
 *
 * @param string $url default $_SERVER[ 'REQUEST_URI' ]
 *
 * @return boolean
 */
function tvc_on_any_own_plugin_page( $url = null ) {
	$ref_url = null === $url ? tvc_get_current_page_url() : $url;

	// return true if the current page url has not been identified
	if ( empty( $ref_url ) ) {
		return true;
	}

	if ( false === stripos( $ref_url, '/wp-admin/admin.php?page=' . ENHANCAD_PLUGIN_NAME ) && false === stripos( $ref_url, '/wp-admin/admin.php?page=tvc-' ) ) {
		return false;
	} else {
		return true;
	}
}

/**
 * Checks if the user is on the options page
 * Also returns true when current page can not be defined
 *
 * @since 2.3.4
 *
 * @param string $url default $_SERVER[ 'REQUEST_URI' ]
 *
 * @return boolean
 */
function tvc_on_plugins_settings_page( $url = null ) {
	$ref_url = null === $url ? tvc_get_current_page_url() : $url;
	// return true if the current page url has not been identified
	if ( empty( $ref_url ) ) {
		return true;
	}
	if ( true === stripos( $ref_url, '/wp-admin/admin.php?page=tvc-' ) ) {
		return false;
	} else {
		return true;
	}
}

/**
 * Checks if the user is on one of the plugins pages but not on the options page
 * Also returns true when current page can not be defined
 *
 * @since 2.3.4
 *
 * @param string $url default $_SERVER[ 'REQUEST_URI' ]
 *
 * @return boolean
 */
function tvc_on_own_main_plugin_page( $url = null ) {
	$ref_url = null === $url ? tvc_get_current_page_url() : $url;

	// return true if the current page url has not been identified
	if ( empty( $ref_url ) ) {
		return true;
	}

	// return true if the url contains the plugin name under the page attribute or if it's an ajax call
	if ( false === stripos( $ref_url, '/wp-admin/admin.php?page=' . ENHANCAD_PLUGIN_NAME ) && false === stripos( $ref_url, '/wp-admin/admin-ajax.php' ) ) {
		return false;
	} else {
		return true;
	}
}

/**
 * Get the $_SERVER[ 'REQUEST_URI' ] variable
 *
 * @since 2.3.4
 * @return string    $_SERVER[ 'REQUEST_URI' ] or empty
 */
function tvc_get_current_page_url() {
	return isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '';
}