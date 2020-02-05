<?php

/*
Plugin Name: Post to Google My Business
Plugin URI: https://tycoonmedia.net/wordpress-google-my-business-post/
Description: Automatically create a post on Google My Business when creating a new WordPress post
Author: tyCoon Media
Version: 2.2.10
Author URI: https://tycoonmedia.net
*/
if ( !defined( 'ABSPATH' ) ) {
    die;
}

if ( !function_exists( 'mbp_fs' ) ) {
    // Create a helper function for easy SDK access.
    function mbp_fs()
    {
        global  $mbp_fs ;
        
        if ( !isset( $mbp_fs ) ) {
            // Activate multisite network integration.
            if ( !defined( 'WP_FS__PRODUCT_1828_MULTISITE' ) ) {
                define( 'WP_FS__PRODUCT_1828_MULTISITE', true );
            }
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            $mbp_fs = fs_dynamic_init( array(
                'id'              => '1828',
                'slug'            => 'post-to-google-my-business',
                'type'            => 'plugin',
                'public_key'      => 'pk_8ef8aab9dd4277db6bc9b2441830c',
                'is_premium'      => false,
                'has_addons'      => false,
                'has_paid_plans'  => true,
                'trial'           => array(
                'days'               => 7,
                'is_require_payment' => true,
            ),
                'has_affiliation' => 'selected',
                'menu'            => array(
                'slug' => 'post_to_google_my_business',
            ),
                'is_live'         => true,
            ) );
        }
        
        return $mbp_fs;
    }
    
    require_once __DIR__ . '/vendor/autoload.php';
    // Init Freemius.
    mbp_fs();
    // Signal that SDK was initiated.
    do_action( 'mbp_fs_loaded' );
    function mbp_fs_custom_icon()
    {
        return dirname( __FILE__ ) . '/img/plugin-icon.png';
    }
    
    mbp_fs()->add_filter( 'plugin_icon', 'mbp_fs_custom_icon' );
    require_once __DIR__ . '/inc/class-mbp-autoloader.php';
    MBP_Autoloader::register();
    set_error_handler( array( 'MBP_Admin_Notices', 'error_handler' ) );
    register_activation_hook( __FILE__, array( 'MBP_Plugin', 'activate' ) );
    register_deactivation_hook( __FILE__, array( 'MBP_Plugin', 'deactivate' ) );
    register_uninstall_hook( __FILE__, array( 'MBP_Plugin', 'uninstall' ) );
    $post_to_google_my_business_plugin = new MBP_Plugin();
    add_action( 'after_setup_theme', array( $post_to_google_my_business_plugin, 'init' ) );
    //Loading textdomain from subfolder is troublesome
    
    if ( !function_exists( 'mbp_load_textdomain' ) ) {
        function mbp_load_textdomain()
        {
            $dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
            load_plugin_textdomain( 'post-to-google-my-business', false, $dir );
        }
        
        add_action( 'after_setup_theme', 'mbp_load_textdomain' );
    }
    
    restore_error_handler();
}
