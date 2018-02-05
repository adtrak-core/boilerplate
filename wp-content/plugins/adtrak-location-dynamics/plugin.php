<?php
/**
 * @wordpress-plugin
 * Plugin Name: 	Adtrak Location Dynamics
 * Plugin URI: 		http://plugins.adtrakdev.com/downloads/location-dynamics/
 * Description: 	Plugin for displaying number easily.
 * Version: 		3.3.5
 * Author: 			Adtrak
 * Author URI: 		http://adtrak.co.uk
 * License: 		GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:     adtrak-location-dynamics
 */

# if this file is called directly, abort
if (! defined( 'WPINC' )) die;


if (! defined( 'ADTK_HOME_URL' ))
	define('ADTK_HOME_URL', 'http://plugins.adtrakdev.com/');

if (! class_exists('EDD_SL_Plugin_Updater')) {
    include (dirname( __FILE__ ) . '/updater.php');
}

// retrieve our license key from the DB (SET THIS)
$license_key = 'ADTRAKLOCATIONDYNAMICS'; 

// setup the updater
$edd_updater = new EDD_SL_Plugin_Updater(ADTK_HOME_URL, __FILE__, array(
    'version'      => '3.3.5',        // current version number
    'license'      => $license_key,    // license key (used get_option above to retrieve from DB)
    'item_name'    => 'Location Dynamics',    // name of this plugin
    'author'       => 'Adtrak'        // author of this plugin
));


require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/vendor/getbilly/framework/bootstrap/autoload.php';