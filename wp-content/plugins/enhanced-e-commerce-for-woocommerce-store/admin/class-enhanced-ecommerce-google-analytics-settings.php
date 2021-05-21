<?php
/**
 * The admin-setting functionality of the plugin.
 *
 * @link       tatvic.com
 * @since      1.0.0
 *
 * @package    Enhanced_Ecommerce_Google_Analytics
 * @subpackage Enhanced_Ecommerce_Google_Analytics/admin
 */

/**
 * The admin-setting functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Enhanced_Ecommerce_Google_Analytics
 * @subpackage Enhanced_Ecommerce_Google_Analytics/admin
 * @author     Chiranjiv Pathak <chiranjiv@tatvic.com>
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

class Enhanced_Ecommerce_Google_Settings {

    public static function add_update_settings($settings) {

        if ( !get_option($settings)) {
            $ee_options = array();
            if(is_array($_POST)) {
                foreach ($_POST as $key => $value) {
                    if($key == "ee_submit_plugin"){
                        unset($_POST["ee_submit_plugin"]);
                        continue;
                    }
                    if(!isset($_POST[$key])){
                        $_POST[$key] = '';
                    }
                    if(isset($_POST[$key])) {
                        $ee_options[$key] = $_POST[$key];
                    }
                }
            }
            if(!add_option( $settings, serialize( $ee_options ) )){
                update_option($settings, serialize( $ee_options ));
            }
        }
        else {
            $get_ee_settings = unserialize(get_option($settings));
            if(is_array($get_ee_settings)) {
                foreach ($get_ee_settings as $key => $value) {
                    if($key == "ee_submit_plugin"){
                        unset($_POST["ee_submit_plugin"]);
                        continue;
                    }
                    if(!isset($_POST[$key]) ){
                        $_POST[$key] = '';
                    }
                    if( $_POST[$key] != $value ) {
                        $get_ee_settings[$key] =  $_POST[$key];
                    }
                }
            }
            if(is_array($_POST)) {
                foreach($_POST as $key=>$value){
                    if($key == "ee_submit_plugin"){
                        unset($_POST["ee_submit_plugin"]);
                        continue;
                    }
                    if(!array_key_exists($key,$get_ee_settings)){
                        $get_ee_settings[$key] =  $value;
                    }
                }
            }
            
            update_option($settings, serialize( $get_ee_settings ));
        }
        self::admin_notice__success();
    }

    public static function update_analytics_options($settings) {
        if ( !get_option($settings)) {
            $ee_options = array();
            if(is_array($_POST)) {
                foreach ($_POST as $key => $value) {
                    if($key == "ee_submit_plugin"){
                        unset($_POST["ee_submit_plugin"]);
                        continue;
                    }
                    if(!isset($_POST[$key])){
                        $_POST[$key] = $value;
                    }
                    if(isset($_POST[$key])) {
                        $ee_options[$key] = $_POST[$key];
                    }
                }
            }
            add_option( $settings, serialize( $ee_options ) );
        } else {
            $get_ee_settings = unserialize(get_option($settings));
            if(is_array($get_ee_settings)) {
                foreach ($get_ee_settings as $key => $value) {
                    if($key == "ee_submit_plugin"){
                        unset($_POST["ee_submit_plugin"]);
                        continue;
                    }
                    if(!isset($_POST[$key])){
                        $_POST[$key] = $value;
                    }
                    if( $_POST[$key] != $value && $_POST[$key] != '') {
                        $get_ee_settings[$key] =  $_POST[$key];
                    }
                }
            }

            if(is_array($_POST)) {
                foreach($_POST as $key=>$value){
                    if($key == "ee_submit_plugin"){
                        unset($_POST["ee_submit_plugin"]);
                        continue;
                    }
                    if(!array_key_exists($key,$get_ee_settings)){
                        $get_ee_settings[$key] =  $value;
                    }
                }
            }
            update_option($settings, serialize( $get_ee_settings ));
        }
    }


    private static function admin_notice__success() {
        $class = 'notice notice-success';
        $message = __( 'Your settings have been saved.', 'sample-text-domain' );
        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );

    }
    public function show_message(){
        echo '
            <div class="notice notice-warning is-dismissible">
                    <strong><p>Get all the 9 GA - Enhanced Ecommerce reports, 20+ custom dimensions and metrics, google ads conversion tracking, FB pixel tracking, Google Optimize integration and many more advanced features in the <a href="https://codecanyon.net/item/actionable-google-analytics-for-woocommerce/9899552?utm_source=TatvicEE&utm_medium=DashboardMsgText&utm_campaign=WPlisting" target="_blank">Premium Version</a>.</p></strong>
            </div>
        ';
    }
}
