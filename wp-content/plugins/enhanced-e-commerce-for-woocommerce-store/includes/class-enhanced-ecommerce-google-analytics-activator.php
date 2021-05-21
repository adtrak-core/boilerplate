<?php

/**
 * Fired during plugin activation
 *
 * @link       test.com
 * @since      1.0.0
 *
 * @package    Enhanced_Ecommerce_Google_Analytics_Activator
 * @subpackage Enhanced_Ecommerce_Google_Analytics_Activator/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Enhanced_Ecommerce_Google_Analytics_Activator
 * @subpackage Enhanced_Ecommerce_Google_Analytics_Activator/includes
 * @author     Chiranjiv Pathak <chiranijv@tatvic.com>
 */

class Enhanced_Ecommerce_Google_Analytics_Activator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate() {    	
    	if (!is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
           wp_die('Hey, It seems WooCommerce plugin is not active on your wp-admin. Enhanced ecommerce plugin can only be activated if you have active WooCommerce plugin in your wp-admin. <br><a href="' . admin_url( 'plugins.php' ) . '">&laquo; Return to Plugins</a>');
        }
    }
}
