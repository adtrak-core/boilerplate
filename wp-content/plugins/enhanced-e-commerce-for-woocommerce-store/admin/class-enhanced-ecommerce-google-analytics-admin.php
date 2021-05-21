<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       tatvic.com
 * @since      1.0.0
 *
 * @package    Enhanced_Ecommerce_Google_Analytics
 * @subpackage Enhanced_Ecommerce_Google_Analytics/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Enhanced_Ecommerce_Google_Analytics
 * @subpackage Enhanced_Ecommerce_Google_Analytics/admin
 * @author     Chiranjiv Pathak <chiranjiv@tatvic.com>
 */

class Enhanced_Ecommerce_Google_Analytics_Admin extends TVC_Admin_Helper {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since      1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    protected $ga_id;
    protected $ga_LC;
    protected $ga_eeT;
    protected $site_url;
    public function __construct($plugin_name, $version) {                       
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->url = $this->get_connect_url();
        $this->site_url = "admin.php?page=enhanced-ecommerce-google-analytics-admin-display&tab=";
    }
    public function tvc_admin_notice(){
        //global $pagenow;
        $ee_additional_data = $this->get_ee_additional_data();
        if(isset($ee_additional_data['dismissed_ee_adimin_notic_a']) && $ee_additional_data['dismissed_ee_adimin_notic_a'] == 1){
        }else{
            if(!$this->get_subscriptionId()){          
                echo '<div class="notice notice-info is-dismissible" data-id="ee_adimin_notic_a">
                      <p>Tatvic EE plugin is now fully compatible with Google Analytics 4. Also, explore the new features of Google Shopping and Dynamic remarketing to reach million of shoppers across Google and scale your eCommerce business faster. <a href="admin.php?page=enhanced-ecommerce-google-analytics-admin-display"><b><u>CONFIGURE NOW</u></b></a></p>
                     </div>'; 
            }
        }
        if(isset($ee_additional_data['dismissed_ee_adimin_notic_b']) && $ee_additional_data['dismissed_ee_adimin_notic_b'] == 1){
        }else{
            $google_detail = $this->get_ee_options_data();
            if(isset($google_detail['setting']) && $google_detail['setting']){
                $googleDetail = $google_detail['setting'];
                if(isset($googleDetail->google_merchant_center_id) && $googleDetail->google_merchant_center_id =="" && $this->subscriptionId != "" ){
                    echo '<div class="notice notice-info is-dismissible" data-id="ee_adimin_notic_b">
                      <p>Leverage the power of Google Shopping to reach out millions of shoppers across Google. Automate entire Google Shopping and get eligible for free listing when user searches on Google for products similar to your eCommerce business. <a href="admin.php?page=enhanced-ecommerce-google-analytics-admin-display"><b><u>Automate now</u></b></a></p>
                     </div>';
                     
                }
            } 
        }
        ?>
        <script>
            (function( $ ) {
                $( function() {
                    $( '.notice' ).on( 'click', '.notice-dismiss', function( event, el ) {
                        var ee_notice_dismiss_id = $(this).parent('.is-dismissible').attr("data-id");
                        jQuery.post(myAjaxNonces.ajaxurl,{
                            action: "tvc_call_notice_dismiss",
                            data:{ee_notice_dismiss_id:ee_notice_dismiss_id},
                            dataType: "json",
                            apiDomainClaimNonce: myAjaxNonces.apiNoticDismissNonce
                        },function( response ){
                            
                        });
                    });
                } );
            })( jQuery );
         </script>
        <?php
               
        
        
    }

    
    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        $screen = get_current_screen();
        if ($screen->id == 'toplevel_page_enhanced-ecommerce-google-analytics-admin-display' || (isset($_GET['page']) && $_GET['page'] == 'enhanced-ecommerce-google-analytics-admin-display')) {
            wp_register_style('font_awesome', '//use.fontawesome.com/releases/v5.0.13/css/all.css');
            wp_enqueue_style('font_awesome');
            wp_register_style('plugin-bootstrap',ENHANCAD_PLUGIN_URL . '/includes/setup/plugins/bootstrap/css/bootstrap.min.css');
            wp_enqueue_style('plugin-bootstrap');
            wp_register_style('aga_confirm', '//cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css');
            wp_enqueue_style('aga_confirm');
           
            wp_enqueue_style('custom-css', ENHANCAD_PLUGIN_URL . '/admin/css/custom-style.css', array(), $this->version, 'all' );
            if($this->is_current_tab_in(array('sync_product_page','gaa_config_page'))){
                wp_register_style('plugin-select2',ENHANCAD_PLUGIN_URL . '/includes/setup/plugins/select2/select2.min.css');
                wp_enqueue_style('plugin-select2');
                wp_register_style('plugin-steps',ENHANCAD_PLUGIN_URL . '/includes/setup/plugins/jquery-steps/jquery.steps.css');
                wp_enqueue_style('plugin-steps');
            }
            if($this->is_current_tab_in(array("shopping_campaigns_page","add_campaign_page"))){
                wp_register_style('plugin-select2',ENHANCAD_PLUGIN_URL . '/includes/setup/plugins/select2/select2.min.css');
                wp_enqueue_style('plugin-select2');
                wp_register_style('bootstrap-datepicker',ENHANCAD_PLUGIN_URL. '/includes/setup/plugins/datepicker/bootstrap-datepicker.min.css');
                wp_enqueue_style('bootstrap-datepicker');
            }
            wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/enhanced-ecommerce-google-analytics-admin.css', array(), $this->version, 'all');
        }
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        $screen = get_current_screen();
        if ($screen->id == 'toplevel_page_enhanced-ecommerce-google-analytics-admin-display' || (isset($_GET['page']) && $_GET['page'] == 'enhanced-ecommerce-google-analytics-admin-display')) {

            wp_enqueue_script( 'custom-jquery', ENHANCAD_PLUGIN_URL . '/admin/js/jquery-3.5.1.min.js', array( 'jquery' ), $this->version, false );
            wp_register_script('popper_bootstrap', '//cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js');
            wp_enqueue_script('popper_bootstrap');
            wp_register_script('aga_bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/4.5.1/js/bootstrap.min.js');
            wp_enqueue_script('aga_bootstrap');
            wp_register_script('aga_bootstrap_mod', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js');
            wp_enqueue_script('aga_bootstrap_mod');
            wp_register_script('aga_confirm_js', '//cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js');
            wp_enqueue_script('aga_confirm_js');
            wp_enqueue_script( 'tvc-ee-custom-js', ENHANCAD_PLUGIN_URL . '/admin/js/tvc-ee-custom.js', array( 'jquery' ), $this->version, false );
            if($this->is_current_tab_in(array('sync_product_page','gaa_config_page'))){
                wp_register_script('plugin-select2',ENHANCAD_PLUGIN_URL . '/includes/setup/plugins/select2/select2.min.js');
                wp_enqueue_script('plugin-select2');
                wp_register_script('plugin-step-js',ENHANCAD_PLUGIN_URL . '/includes/setup/plugins/jquery-steps/jquery.steps.js');
                wp_enqueue_script('plugin-step-js');
            }
            if($this->is_current_tab_in(array("shopping_campaigns_page","add_campaign_page"))){
                wp_register_script('plugin-select2',ENHANCAD_PLUGIN_URL . '/includes/setup/plugins/select2/select2.min.js');
                wp_enqueue_script('plugin-select2');
                wp_register_script('plugin-chart',ENHANCAD_PLUGIN_URL . '/includes/setup/plugins/chart/chart.js');
                wp_enqueue_script('plugin-chart');
                wp_register_script('bootstrap_datepicker',ENHANCAD_PLUGIN_URL . '/includes/setup/plugins/datepicker/bootstrap-datepicker.min.js');
                wp_enqueue_script('bootstrap_datepicker');
            }
        }
    }

    /**
     * Display Admin Page.
     *
     * @since    1.0.0
     */
    public function display_admin_page() {
        add_menu_page(
            'Tatvic EE Plugin', 'Tatvic EE Plugin', 'manage_options', "enhanced-ecommerce-google-analytics-admin-display", array($this, 'showPage'), plugin_dir_url(__FILE__) . 'images/tatvic_logo.png', 26
        );
        add_submenu_page(
            'enhanced-ecommerce-google-analytics-admin-display',
            esc_html__('Google Ads', 'actionable-google-analytics-admin-display'),
            esc_html__('Google Ads', 'actionable-google-analytics-admin-display'),
            'manage_woocommerce',
            'enhanced-ecommerce-google-analytics-admin-display&tab=google_ads',
            array($this, 'showPage')
        );
        add_submenu_page(
            'enhanced-ecommerce-google-analytics-admin-display',
            esc_html__('Google Shopping', 'enhanced-ecommerce-google-analytics-admin-display'),
            esc_html__('Google Shopping', 'enhanced-ecommerce-google-analytics-admin-display'),
            'manage_woocommerce',
            'enhanced-ecommerce-google-analytics-admin-display&tab=google_shopping_feed',
            array($this, 'showPage')
        );
    }
    /**
     * Display Tab page.
     *
     * @since    1.0.0
     */
    public function showPage() {
        echo '<div class="tvc_plugin_container">';
        require_once( 'partials/enhanced-ecommerce-google-analytics-admin-display.php');
        new TVC_Tabs();
        echo $this->call_tvc_site_verified_and_domain_claim();
        if (!empty($_GET['tab'])) {
            $get_action = $_GET['tab'];
        } else {
            $get_action = "general_settings";
        }
        if (method_exists($this, $get_action)) {
            $this->$get_action();
        }
        echo '</div>';
        echo $this->get_tvc_popup_message();
    }
    public function check_nall_and_message($val, $msg, $msg_false){
        if((isset($val) && $val != "" && $val != 0) ){
            return $msg;
        }else{
             return $msg_false;
        }
    }
    
    public function general_settings() {       
        require_once( 'partials/general-fields.php');
    }
    public function google_ads() {        
        require_once(ENHANCAD_PLUGIN_DIR . 'includes/setup/help-html.php');
        require_once(ENHANCAD_PLUGIN_DIR . 'includes/setup/google-ads.php');
        new GoogleAds();
    }
    public function google_shopping_feed() {
        include(ENHANCAD_PLUGIN_DIR . 'includes/setup/help-html.php');
        include(ENHANCAD_PLUGIN_DIR . 'includes/setup/google-shopping-feed.php');
        new GoogleShoppingFeed();
    }
    public function gaa_config_page() { 
        include(ENHANCAD_PLUGIN_DIR . 'includes/setup/help-html.php');       
        include(ENHANCAD_PLUGIN_DIR . 'includes/setup/google-shopping-feed-gaa-config.php');        
        new GAAConfiguration();
    }    
    public function sync_product_page() {
        include(ENHANCAD_PLUGIN_DIR . 'includes/setup/help-html.php');
        include(ENHANCAD_PLUGIN_DIR . 'includes/setup/google-shopping-feed-sync-product.php');
        new SyncProductConfiguration();
    }
    public function shopping_campaigns_page() {
        include(ENHANCAD_PLUGIN_DIR . 'includes/setup/help-html.php');
        include(ENHANCAD_PLUGIN_DIR . 'includes/setup/google-shopping-feed-shopping-campaigns.php');
        new CampaignsConfiguration();
    }
    public function add_campaign_page() {
        include(ENHANCAD_PLUGIN_DIR . 'includes/setup/help-html.php');
        include(ENHANCAD_PLUGIN_DIR . 'includes/setup/add-campaign.php');
        new AddCampaign();
    }    
    public function conversion_tracking() {
        require_once( 'partials/conversion-tracking.php');
    }
    public function google_optimize() {
        require_once( 'partials/google-optimize.php');
    }
    public function about_plugin() {
        require_once( 'partials/about-plugin.php');
    }
    public function country_location() {
        // date function to hide 30% off sale after certain date
        return date_default_timezone_set('Australia/Sydney'); // Change this depending on what timezone your in
    }    
    public function today() {
        $this->country_location();
        return strtotime(date('Y-m-d'));
    }

    public function current_time() {
        $this->country_location();
        return strtotime(date('h:i A'));
    }

    public function start_date() {
        $this->country_location();
        return strtotime(date('Y') . '-09-01');
    }

    public function end_date() {
        $this->country_location();
        return strtotime(date('Y') . '-09-08');
    }

    public function end_time() {
        $this->country_location();
        return strtotime('11:59 PM');
    }
}
