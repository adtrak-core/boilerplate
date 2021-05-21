<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              tatvic.com
 * @since             1.0.0
 * @package           Enhanced E-commerce for Woocommerce store
 *
 * @wordpress-plugin
 * Plugin Name:       Enhanced E-commerce for Woocommerce store
 * Plugin URI:        https://www.tatvic.com/tatvic-labs/woocommerce-extension/
 * Description:       Automates eCommerce tracking in Google Analytics, dynamic remarkting in Google Ads, and provides complete Google Shopping features.
 * Version:           3.0.5
 * Author:            Tatvic
 * Author URI:        www.tatvic.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       www.tatvic.com
 * Domain Path:       /languages
 * WC requires at least: 1.4.1
 * WC tested up to: 5.0.0
 */

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
    die;
}
/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_NAME_VERSION', '3.0.5' );
$fullName = plugin_basename( __FILE__ );
$dir = str_replace('/enhanced-ecommerce-google-analytics.php','',$fullName);
if ( ! defined( 'ENHANCAD_PLUGIN_NAME' ) ) {
    define( 'ENHANCAD_PLUGIN_NAME', $dir);
}
// Store the directory of the plugin
if ( ! defined( 'ENHANCAD_PLUGIN_DIR' ) ) {
    define( 'ENHANCAD_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}
// Store the url of the plugin
if ( ! defined( 'ENHANCAD_PLUGIN_URL' ) ) {
    define( 'ENHANCAD_PLUGIN_URL', plugins_url() . '/' . ENHANCAD_PLUGIN_NAME );
}
// Store the transient alive time
if ( ! defined( 'TVC_TRANSIENT_LIVE' ) ) {
    define( 'TVC_TRANSIENT_LIVE', 20 * MINUTE_IN_SECONDS );
}
// Store the base uploads folder, should also work in a multi site environment
if ( ! defined( 'TVC_UPLOADS_DIR' ) ) {
    $wp_upload_dir = wp_get_upload_dir(); // @since 2.10.0 switched from wp_upload_dir to wp_get_upload_dir.
    $upload_dir    = is_multisite() && defined( 'UPLOADS' ) ? UPLOADS : $wp_upload_dir['basedir'];
    if ( ! file_exists( $upload_dir ) && ! is_dir( $upload_dir ) ) {
        define( 'TVC_UPLOADS_DIR', $wp_upload_dir['basedir'] );
    } else {
        define( 'TVC_UPLOADS_DIR', $upload_dir );
    }
}

if ( ! defined( 'TVC_UPLOADS_URL' ) ) {
    $wp_upload_dir = wp_upload_dir();
    // correct baseurl for https if required
    if ( is_ssl() ) {
        $url = str_replace( 'http://', 'https://', $wp_upload_dir['baseurl'] );
    } else {
        $url = $wp_upload_dir['baseurl'];
    }
    define( 'TVC_UPLOADS_URL', apply_filters( 'tvc_corrected_uploads_url', $url ) );
}
// store the folder that contains the channels data
if ( ! defined( 'TVC_CHANNEL_DATA_DIR' ) ) {
    define( 'TVC_CHANNEL_DATA_DIR', ENHANCAD_PLUGIN_DIR . 'includes/application' );
}
// store the folder that contains the backup files
if ( ! defined( 'TVC_BACKUP_DIR' ) ) {
    define( 'TVC_BACKUP_DIR', TVC_UPLOADS_DIR . '/tvc-backups' );
}
// store the folder that contains the feeds
if ( ! defined( 'TVC_FEEDS_DIR' ) ) {
    define( 'TVC_FEEDS_DIR', TVC_UPLOADS_DIR . '/tvc-feeds' );
}
// Store the plugin constructor
if ( ! defined( 'TVC_PLUGIN_CONSTRUCTOR' ) ) {
    define( 'TVC_PLUGIN_CONSTRUCTOR', plugin_basename( __FILE__ ) );
}
// Store the plugin title
if ( ! defined( 'TVC_EDD_SL_ITEM_NAME' ) ) {
    define( 'TVC_EDD_SL_ITEM_NAME', 'Tatvic Product Feed Manager' );
}
// Store the plugin title
if ( ! defined( 'TVC_MIN_REQUIRED_WC_VERSION' ) ) {
    define( 'TVC_MIN_REQUIRED_WC_VERSION', '3.0.0' );
}
if ( ! defined( 'TVC_API_CALL_URL' ) ) {
   define( 'TVC_API_CALL_URL', 'https://connect.tatvic.com/laravelapi/public/api' );   
}
if ( ! defined( 'TVC_AUTH_CONNECT_URL' ) ) {    
    define( 'TVC_AUTH_CONNECT_URL', 'estorenew.tatvic.com' );
}
if(!defined('TVC_Admin_Helper')){
    include(ENHANCAD_PLUGIN_DIR . '/admin/class-tvc-admin-helper.php');
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-enhanced-ecommerce-google-analytics-activator.php
 */
function activate_enhanced_ecommerce_google_analytics() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-enhanced-ecommerce-google-analytics-activator.php';
    Enhanced_Ecommerce_Google_Analytics_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-enhanced-ecommerce-google-analytics-deactivator.php
 */
function deactivate_enhanced_ecommerce_google_analytics() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-enhanced-ecommerce-google-analytics-deactivator.php';
    Enhanced_Ecommerce_Google_Analytics_Deactivator::deactivate();
}
register_activation_hook( __FILE__, 'activate_enhanced_ecommerce_google_analytics' );
register_deactivation_hook( __FILE__, 'deactivate_enhanced_ecommerce_google_analytics' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-enhanced-ecommerce-google-analytics.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */

function run_enhanced_ecommerce_google_analytics() {

    $plugin = new Enhanced_Ecommerce_Google_Analytics();
    $plugin->run();

}
run_enhanced_ecommerce_google_analytics();