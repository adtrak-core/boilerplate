<?php
function get_connect_google_popup_html(){
    $TVC_Admin_Helper = new TVC_Admin_Helper();
   return '<div class="modal fade" id="tvc_google_connect" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Connect Tatvic with your website</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    By continuing from here, you will be redirected to Tatvic’s website to configure your google analytics, google ads and google merchant center accounts.
                        <br>
                        <br>
                    Make sure you sign in with the google account that has all privileges to access google analytics, google ads and google merchant center account.
                </div>
                <div class="modal-footer">
                    <a target="_blank" class="ee-oauth-container btn darken-4 white black-text" href="'. $TVC_Admin_Helper->get_connect_url().'" style="text-transform:none; margin: 0 auto;">
                        
                        <p style="font-size: inherit; margin-top:5px;"><img width="20px" style="margin-right:8px" alt="Google sign-in"
                                 src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/53/Google_%22G%22_Logo.svg/512px-Google_%22G%22_Logo.svg.png" />Sign In With Google</p>
                    </a>
                    <!--sigin with google end-->
                </div>
            </div>
        </div>
    </div>';
}
function info_htnml($validation){
        if($validation == true){
            return '<img src="'.ENHANCAD_PLUGIN_URL.'/admin/images/config-success.svg" alt="configuration  success" class="config-success">';
        }else{
            return '<img src="'.ENHANCAD_PLUGIN_URL.'/admin/images/exclaimation.png" alt="configuration  success" class="config-fail">';
        }
}
function get_google_shopping_tabs_html($site_url, $google_merchant_center_id){
    $site_url_p = (isset($google_merchant_center_id) && $google_merchant_center_id != '')?$site_url:"javascript:void(0);";
    $site_url_p_target ="";
    if(isset($google_merchant_center_id) && $google_merchant_center_id == ''){
        $site_url_p_target = 'data-toggle="modal" data-target="#tvc_google_connect"';
    }
    $tab = (isset($_GET['tab']) && $_GET['tab'])?$_GET['tab']:"";
    $TVC_Admin_Helper = new TVC_Admin_Helper();
    $setting_status = $TVC_Admin_Helper->check_setting_status_sub_tabs();
    //print_r($setting_status);
    $google_shopping_conf_msg ="";
    if(isset($setting_status['google_shopping_conf'] ) && $setting_status['google_shopping_conf'] == false && isset($setting_status["google_shopping_conf_msg"]) && $setting_status["google_shopping_conf_msg"]){
        $google_shopping_conf_msg = '<span class="tvc-tooltiptext tvc-tooltip-right">'.((isset($setting_status["google_shopping_conf_msg"]))?$setting_status["google_shopping_conf_msg"]:"").'</span>';
    }
    $google_shopping_p_sync_msg="";
    if(isset($setting_status['google_shopping_p_sync'] ) && $setting_status['google_shopping_p_sync'] == false && isset($setting_status["google_shopping_p_sync_msg"]) && $setting_status["google_shopping_p_sync_msg"] !=""){
        $google_shopping_p_sync_msg = '<span class="tvc-tooltiptext tvc-tooltip-right">'.((isset($setting_status["google_shopping_p_sync_msg"]))?$setting_status["google_shopping_p_sync_msg"]:"").'</span>';
    }

    $google_shopping_p_campaigns_msg="";
    if(isset($setting_status['google_shopping_p_campaigns'] ) && $setting_status['google_shopping_p_campaigns'] == false && isset($setting_status["google_shopping_p_campaigns_msg"]) && $setting_status["google_shopping_p_campaigns_msg"]){
        $google_shopping_p_campaigns_msg = '<span class="tvc-tooltiptext tvc-tooltip-right">'.((isset($setting_status["google_shopping_p_campaigns_msg"]))?$setting_status["google_shopping_p_campaigns_msg"]:"").'</span>';
    }

    return '<div class="row confg-card gsf-sec">
        <div class="col-md-12 col-lg-4 mb-3 mb-lg-0">
            <div class="config-head-nav">
              <div class="tvc-tooltip btn w-100 '.(($tab=="gaa_config_page")?"config-head-active":"").'">
                <a href="' . $site_url . 'gaa_config_page" id="smart-shopping-campaigns">Configuration</a>'.$google_shopping_conf_msg
                   .((isset($setting_status['google_shopping_conf']) )?info_htnml($setting_status['google_shopping_conf']):"").'
              </div>                
            </div>
        </div>
        <div class="col-md-12 col-lg-4 mb-3 mb-lg-0">
            <div class="config-head-nav">
              <div class="tvc-tooltip btn w-100 '.(($tab=="sync_product_page")?"config-head-active":"").'" '.$site_url_p_target.'>
                <a href="'.$site_url_p.'sync_product_page"   id="smart-shopping-campaigns">Product Sync</a>'. $google_shopping_p_sync_msg
                    .((isset($setting_status['google_shopping_p_sync']) )?info_htnml($setting_status['google_shopping_p_sync']):"").' 
              </div>              
            </div>
        </div>
        <div class="col-md-12 col-lg-4 mb-3 mb-lg-0">
            <div class="config-head-nav">
              <div class="tvc-tooltip btn w-100 '.(($tab=="shopping_campaigns_page")?"config-head-active":"").'" '.$site_url_p_target.'>
                <a href="' . $site_url_p . 'shopping_campaigns_page"   id="smart-shopping-campaigns">Smart  Shopping Campaigns</a>'. $google_shopping_p_campaigns_msg
                    .((isset($setting_status['google_shopping_p_campaigns']) )?info_htnml($setting_status['google_shopping_p_campaigns']):"").'
              </div>
            </div>
        </div>
    </div>';
}
function get_tvc_google_ads_help_html(){
    return '<h5 class="content-heading">Help Center:</h5>
    <p>Once you select or create a new google ads account, your account will be enabled for the following:</p>
    <ol>
        <li>Remarketing and dynamic remarketing tags for all the major eCommerce events on your website (Optional)</li>
        <li>Your google ads account will be linked with the previously selected google analytics account (Optional)</li>
        <li>Your google ads account will be linked with google merchant center account in the next step so that you can start running google shopping campaigns(Optional)</li>
    </ol>

    <p>
        <a target="_blank" href="http://plugins.tatvic.com/help-center/Installation-Manual.pdf">Installation manual</a> | 
        <a target="_blank" href="http://plugins.tatvic.com/help-center/Google-shopping-Guide.pdf">Google shopping guide</a> | 
        <a target="_blank" href="https://wordpress.org/plugins/enhanced-e-commerce-for-woocommerce-store/faq/
">FAQ</a>
        </p>
     <h5 class="content-heading">Business Value:</h5>
     <ol>
       <li>With dynamic remarketing tags, you will be able to show ads to your past site visitors with specific product information that is tailored to your customer’s previous site visits.</li>
       <li>This plugin enables dynamic remarketing tags for crucial eCommerce events like product list views, product detail page views, add to cart and final purchase event.</li>
       <li>Dynamic remarketing along with the product feeds in your merchant center account will enable you for smart shopping campaigns which is very essential for any eCommerce business globally. <a target="_blank" href="https://support.google.com/google-ads/answer/3124536?hl=en">Learn More</a></li>
     </ol>
    ';
}
function get_tvc_help_html(){
    return '<h5 class="content-heading">Help Center:</h5>
            <ol>
            <li>Set up your Google Merchant Center Account and make your WooCommerce shop and products available to millions of shoppers across Google.
            </li>
            <li>Our plugin will help you automate everything you need to make your products available to interested customers across Google.</li>
            <li>Follow <a target="_blank" href="https://support.google.com/merchants/answer/6363310?hl=en&ref_topic=3163841">merchant center guidelines for site requirements</a> in order to avoid account suspension issues. </li>
            </ol>
            <p>
            <a target="_blank" href="http://plugins.tatvic.com/help-center/Installation-Manual.pdf">Installation manual</a> | 
            <a target="_blank" href="http://plugins.tatvic.com/help-center/Google-shopping-Guide.pdf">Google shopping guide</a> | 
            <a target="_blank" href="https://wordpress.org/plugins/enhanced-e-commerce-for-woocommerce-store/faq/
">FAQ</a>
            </p>
            <h5 class="content-heading">Business Value:</h5>
	        <ol>
	        <li>Opt your product data into programmes, like surfaces across Google, Shopping ads, local inventory ads and Shopping Actions, to highlight your products to shoppers across Google.</li>
	        <li>Your store’s products will be eligible to get featured under the shopping tab when anyone searches for products that match your store’s product attributes.</li>
	        <li>Reach out to customers leaving your store by running smart shopping campaigns based on their past site behavior.  <a target="_blank" href="https://www.google.com/intl/en_in/retail/?fmp=1&utm_id=bkws&mcsubid=in-en-ha-g-mc-bkws">Learn More</a></li>
	        </ol>';
}
?>