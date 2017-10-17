<?php
/**
 * @package   Public_Post_Preview_Configurator
 * @author    Björn Weinbrenner <info@bjoerne.com>
 * @license   GPLv3
 * @link      http://bjoerne.com
 * @copyright 2014 bjoerne.com
 *
 * @wordpress-plugin
 * Plugin Name:       Public Post Preview Configurator
 * Plugin URI:        http://www.bjoerne.com
 * Description:       Enables you to configure 'public post preview' plugin with a user interface.
 * Version:           1.0.3
 * Author:            Björn Weinbrenner
 * Author URI:        http://www.bjoerne.com/
 * Text Domain:       public-post-preview-configurator
 * License:           GPLv3
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/bjoerne2/public-post-preview-configurator
 * WordPress-Plugin-Boilerplate: v2.6.1
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once( plugin_dir_path( __FILE__ ) . 'public/class-public-post-preview-configurator.php' );

add_action( 'plugins_loaded', array( 'Public_Post_Preview_Configurator', 'get_instance' ) );

if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-public-post-preview-configurator-admin.php' );
	add_action( 'plugins_loaded', array( 'Public_Post_Preview_Configurator_Admin', 'get_instance' ) );
}
