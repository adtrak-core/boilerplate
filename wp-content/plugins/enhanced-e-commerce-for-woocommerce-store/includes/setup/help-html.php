<?php
function get_connect_google_popup_html(){
  $TVC_Admin_Helper = new TVC_Admin_Helper();
  return '<div class="modal fade popup-modal overlay" id="tvc_google_connect" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-body">
          <h5 class="modal-title" id="staticBackdropLabel">Connect Tatvic with your website</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
          </button>
          <br>
          <p> By continuing from here, you will be redirected to Tatvic’s website to configure your google analytics, google ads and google merchant center accounts.</p>
              
          <p>Make sure you sign in with the google account that has all privileges to access google analytics, google ads and google merchant center account.</p>
        </div>
        <div class="modal-footer">
          <a target="_blank" class="ee-oauth-container btn darken-4 white black-text" href="'. $TVC_Admin_Helper->get_connect_url().'" style="text-transform:none; margin: 0 auto;">
            <p style="font-size: inherit; margin-top:5px;"><img width="20px" style="margin-right:8px" alt="Google sign-in" src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/53/Google_%22G%22_Logo.svg/512px-Google_%22G%22_Logo.svg.png" />Sign In With Google</p>
          </a>
        </div>
      </div>
    </div>
  </div>';
}
function get_connect_google_popup_html_to_active_licence(){
  $TVC_Admin_Helper = new TVC_Admin_Helper();
  return '<div class="modal fade popup-modal overlay" id="tvc_google_connect_active_licence">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-body">
          <h5 class="modal-title" id="staticBackdropLabel">Connect Tatvic with your website to active licence key</h5>
          <button type="button" id="tvc_google_connect_active_licence_close" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
          </button>
          <br>
          <p> By continuing from here, you will be redirected to Tatvic’s website to configure your google analytics, google ads and google merchant center accounts.</p>
              
          <p>Make sure you sign in with the google account that has all privileges to access google analytics, google ads and google merchant center account.</p>
        </div>
        <div class="modal-footer">
          <a target="_blank" class="ee-oauth-container btn darken-4 white black-text" href="'. $TVC_Admin_Helper->get_connect_url().'" style="text-transform:none; margin: 0 auto;">
            <p style="font-size: inherit; margin-top:5px;"><img width="20px" style="margin-right:8px" alt="Google sign-in" src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/53/Google_%22G%22_Logo.svg/512px-Google_%22G%22_Logo.svg.png" />Sign In With Google</p>
          </a>
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

    return '<ul class="nav nav-tabs nav-justified edit-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
          <div class="tvc-tooltip nav-link '.(($tab=="gaa_config_page")?"active":"").'">
            <a href="' . $site_url . 'gaa_config_page" id="smart-shopping-campaigns">Configuration</a>'.$google_shopping_conf_msg
               .((isset($setting_status['google_shopping_conf']) )?info_htnml($setting_status['google_shopping_conf']):"").'
          </div>
        </li>
        <li class="nav-item" role="presentation">
            <div class="tvc-tooltip nav-link '.(($tab=="sync_product_page")?"active":"").'" '.$site_url_p_target.'>
              <a href="'.$site_url_p.'sync_product_page"   id="smart-shopping-campaigns">Product Sync</a>'. $google_shopping_p_sync_msg
                  .((isset($setting_status['google_shopping_p_sync']) )?info_htnml($setting_status['google_shopping_p_sync']):"").' 
            </div>              
        </li>
        <li class="nav-item" role="presentation">
          <div class="tvc-tooltip nav-link '.(($tab=="shopping_campaigns_page")?"active":"").'" '.$site_url_p_target.'>
            <a href="' . $site_url_p . 'shopping_campaigns_page"   id="smart-shopping-campaigns">Smart  Shopping Campaigns</a>'. $google_shopping_p_campaigns_msg
                .((isset($setting_status['google_shopping_p_campaigns']) )?info_htnml($setting_status['google_shopping_p_campaigns']):"").'
          </div>
        </li>
      </ul>';
}
function get_tvc_google_ads_help_html(){
  $TVC_Admin_Helper = new TVC_Admin_Helper();
  ob_start(); ?>
  <div class="right-content">
    <div class="content-section">
      <div class="content-icon">
        <img src="<?php echo ENHANCAD_PLUGIN_URL.'/admin/images/icon/information.svg'; ?>" alt="information"/>
      </div>
      <h4 class="content-heading">Help Center:</h4>
      <section class="tvc-help-slider">
        <?php if($TVC_Admin_Helper->is_ga_property() == false){?>
          <div>
            In order to configure your Google Ads account, you need to sign in with the associated Google account. Click on "Get started" <img src="<?php echo ENHANCAD_PLUGIN_URL."/admin/images/icon/add.svg"; ?>" alt="connect account"/> icon to set up the plugin.
          </div>
          <div>
            Once you select or create a new google ads account, your account will be enabled for the following:
            <ol>
              <li>Remarketing and dynamic remarketing tags for all the major eCommerce events on your website (Optional)</li>
              <li>Your google ads account will be linked with the previously selected google analytics account (Optional)</li>
              <li>Your google ads account will be linked with google merchant center account in the next step so that you can start running google shopping campaigns(Optional)</li>
            </ol>
          </div>
        <?php }else{ ?>
          <div>
            You can update or change the google ads account anytime by clicking on <img src="<?php echo ENHANCAD_PLUGIN_URL."/admin/images/icon/refresh.svg"; ?>" alt="refresh"/> icon.
          </div>
        <?php }?>  
         
      </section>      
    </div>
    <nav>
        <ul class="pagination justify-content-center">
          <li class="page-item page-prev h-page-prev">
            <span class="page-link"><img src="<?php echo ENHANCAD_PLUGIN_URL.'/admin/images/icon/arrow-down-sign-to-navigate.svg'; ?>" alt=""/></span>
          </li>
          <li class="page-item"><span class="paging_info" id="paging_info">1</span></li>
          <li class="page-item page-next h-page-next">
            <span class="page-link"><img src="<?php echo ENHANCAD_PLUGIN_URL.'/admin/images/icon/arrow-down-sign-to-navigate.svg'; ?>" alt=""/></span>
          </li>
        </ul>
      </nav> 
  </div>
  <script>
    let rtl= <?php echo (is_rtl())?"true":"false"; ?>; 
    $(".tvc-help-slider").slick({
        autoplay: false,
        dots: false,
        prevArrow:$('.h-page-prev'),
        nextArrow:$('.h-page-next'),
        rtl:rtl
    });
    $(".tvc-help-slider").on("beforeChange", function(event, slick, currentSlide, nextSlide){
      $("#paging_info").html(nextSlide+1);
    });
  </script>
  <div class="right-content">
    <div class="content-section">
      <div class="content-icon">
        <img src="<?php echo ENHANCAD_PLUGIN_URL.'/admin/images/icon/lamp.svg'; ?>" alt="information"/>
      </div>
      <h4 class="content-heading">Business Value:</h4>
      <section class="tvc-b-value-slider">
         <div>With dynamic remarketing tags, you will be able to show ads to your past site visitors with specific product information that is tailored to your customer’s previous site visits.</div>
         <div>This plugin enables dynamic remarketing tags for crucial eCommerce events like product list views, product detail page views, add to cart and final purchase event.</div>
         <div>Dynamic remarketing along with the product feeds in your merchant center account will enable you for smart shopping campaigns which is very essential for any eCommerce business globally. <a target="_blank" href="https://support.google.com/google-ads/answer/3124536?hl=en">Learn More</a>
         </div>
      </section>      
    </div>
    <nav>
        <ul class="pagination justify-content-center">
          <li class="page-item page-prev b-page-prev">
            <span class="page-link"><img src="<?php echo ENHANCAD_PLUGIN_URL.'/admin/images/icon/arrow-down-sign-to-navigate.svg'; ?>" alt=""/></span>
          </li>
          <li class="page-item"><span class="paging_info" id="b-paging-info">1</span></li>
          <li class="page-item page-next b-page-next">
            <span class="page-link"><img src="<?php echo ENHANCAD_PLUGIN_URL.'/admin/images/icon/arrow-down-sign-to-navigate.svg'; ?>" alt=""/></span>
          </li>
        </ul>
      </nav> 
  </div>
  <div class="tvc-footer-links">
    <a target="_blank" href="http://plugins.tatvic.com/help-center/Installation-Manual.pdf" tabindex="0">Installation manual</a> | <a target="_blank" href="http://plugins.tatvic.com/help-center/Google-shopping-Guide.pdf" tabindex="0">Google shopping guide</a> | <a target="_blank" href="https://wordpress.org/plugins/enhanced-e-commerce-for-woocommerce-store/faq/" tabindex="0">FAQ</a>
  </div>
  <script>
    //let rtl= "<?php echo (is_rtl())?"true":"false"; ?>"; 
    $(".tvc-b-value-slider").slick({
        autoplay: false,
        dots: false,
        prevArrow:$('.b-page-prev'),
        nextArrow:$('.b-page-next'),
        rtl:rtl
    });
    $(".tvc-b-value-slider").on("beforeChange", function(event, slick, currentSlide, nextSlide){
      $("#b-paging-info").html(nextSlide+1);
    });
  </script>
  <?php
  return ob_get_clean();
}
function get_tvc_help_html(){
  ob_start(); ?>
  <div class="right-content">
    <div class="content-section">
      <div class="content-icon">
        <img src="<?php echo ENHANCAD_PLUGIN_URL.'/admin/images/icon/information.svg'; ?>" alt="information"/>
      </div>
      <h4 class="content-heading">Help Center:</h4>
      <section class="tvc-help-slider">
        <div>Set up your Google Merchant Center Account and make your WooCommerce shop and products available to millions of shoppers across Google.</div>
        <div>Our plugin will help you automate everything you need to make your products available to interested customers across Google.</div>
        <div>Follow <a target="_blank" href="https://support.google.com/merchants/answer/6363310?hl=en&ref_topic=3163841">merchant center guidelines for site requirements</a> in order to avoid account suspension issues. 
       </div>
      </section>      
    </div>
    <nav>
        <ul class="pagination justify-content-center">
          <li class="page-item page-prev h-page-prev">
            <span class="page-link"><img src="<?php echo ENHANCAD_PLUGIN_URL.'/admin/images/icon/arrow-down-sign-to-navigate.svg'; ?>" alt=""/></span>
          </li>
          <li class="page-item"><span class="paging_info" id="paging_info">1</span></li>
          <li class="page-item page-next h-page-next">
            <span class="page-link"><img src="<?php echo ENHANCAD_PLUGIN_URL.'/admin/images/icon/arrow-down-sign-to-navigate.svg'; ?>" alt=""/></span>
          </li>
        </ul>
      </nav> 
  </div>
  <script>
    let rtl= <?php echo (is_rtl())?"true":"false"; ?>;   
    $(".tvc-help-slider").slick({
        autoplay: false,
        dots: false,
        prevArrow:$('.h-page-prev'),
        nextArrow:$('.h-page-next'),
        rtl:rtl
    });
    $(".tvc-help-slider").on("beforeChange", function(event, slick, currentSlide, nextSlide){
      $("#paging_info").html(nextSlide+1);
    });
  </script>
  <div class="right-content">
    <div class="content-section">
      <div class="content-icon">
        <img src="<?php echo ENHANCAD_PLUGIN_URL.'/admin/images/icon/lamp.svg'; ?>" alt="information"/>
      </div>
      <h4 class="content-heading">Business Value:</h4>
      <section class="tvc-b-value-slider">
         <div>Opt your product data into programmes, like surfaces across Google, Shopping ads, local inventory ads and Shopping Actions, to highlight your products to shoppers across Google.</div>
         <div>Your store’s products will be eligible to get featured under the shopping tab when anyone searches for products that match your store’s product attributes.</div>
         <div>Reach out to customers leaving your store by running smart shopping campaigns based on their past site behavior.  <a target="_blank" href="https://www.google.com/intl/en_in/retail/?fmp=1&utm_id=bkws&mcsubid=in-en-ha-g-mc-bkws">Learn More</a></div>
      </section>      
    </div>
    <nav>
        <ul class="pagination justify-content-center">
          <li class="page-item page-prev b-page-prev">
            <span class="page-link"><img src="<?php echo ENHANCAD_PLUGIN_URL.'/admin/images/icon/arrow-down-sign-to-navigate.svg'; ?>" alt=""/></span>
          </li>
          <li class="page-item"><span class="paging_info" id="b-paging-info">1</span></li>
          <li class="page-item page-next b-page-next">
            <span class="page-link"><img src="<?php echo ENHANCAD_PLUGIN_URL.'/admin/images/icon/arrow-down-sign-to-navigate.svg'; ?>" alt=""/></span>
          </li>
        </ul>
      </nav> 
  </div>
  <div class="tvc-footer-links">
    <a target="_blank" href="http://plugins.tatvic.com/help-center/Installation-Manual.pdf" tabindex="0">Installation manual</a> | <a target="_blank" href="http://plugins.tatvic.com/help-center/Google-shopping-Guide.pdf" tabindex="0">Google shopping guide</a> | <a target="_blank" href="https://wordpress.org/plugins/enhanced-e-commerce-for-woocommerce-store/faq/" tabindex="0">FAQ</a>
  </div>
  <script>
    //let rtl= "<?php echo (is_rtl())?"true":"false"; ?>"; 
    $(".tvc-b-value-slider").slick({
        autoplay: false,
        dots: false,
        prevArrow:$('.b-page-prev'),
        nextArrow:$('.b-page-next'),
        rtl:rtl
    });
    $(".tvc-b-value-slider").on("beforeChange", function(event, slick, currentSlide, nextSlide){
      $("#b-paging-info").html(nextSlide+1);
    });
  </script>
  <?php
  return ob_get_clean();    
}
function get_tvc_google_ga_sidebar(){
  $TVC_Admin_Helper = new TVC_Admin_Helper();  
  ob_start(); ?>
  <div class="right-content">
    <div class="content-section">
      <div class="content-icon">
        <img src="<?php echo ENHANCAD_PLUGIN_URL.'/admin/images/icon/information.svg'; ?>" alt="information"/>
      </div>
      <h4 class="content-heading">Help Center:</h4>
      <section class="tvc-help-slider">
        <?php if($TVC_Admin_Helper->is_ga_property() == false){?>
          <div>
              In order to configure your Google Analytics account, you need to sign in with the associated Google account. Click on "Get started" <img src="<?php echo ENHANCAD_PLUGIN_URL."/admin/images/icon/add.svg"; ?>" alt="connect account"/> icon to set up the plugin.
          </div>
          <div>
            Once you sign in with an associated google account, you will be asked to select a google analytics account from the drop down.
          </div>
          <div>
            If you have already added the gtag.js snippet manually, YOU MUST uncheck the “add gtag.js”.
          </div>
          <?php
        }else{
          ?>
          <div>
            You can update or change the google analytics account anytime by clicking on <img src="<?php echo ENHANCAD_PLUGIN_URL."/admin/images/icon/refresh.svg"; ?>" alt="refresh"/> icon.
          </div>
          <?php
        }?>
      </section>      
    </div>
    <nav>
        <ul class="pagination justify-content-center">
          <li class="page-item page-prev h-page-prev help-ga-prev">
            <span class="page-link"><img src="<?php echo ENHANCAD_PLUGIN_URL.'/admin/images/icon/arrow-down-sign-to-navigate.svg'; ?>" alt=""/></span>
          </li>
          <li class="page-item"><span class="paging_info" id="help_ga_paging_info">1</span></li>
          <li class="page-item page-next h-page-next help-ga-next">
            <span class="page-link"><img src="<?php echo ENHANCAD_PLUGIN_URL.'/admin/images/icon/arrow-down-sign-to-navigate.svg'; ?>" alt=""/></span>
          </li>
        </ul>
      </nav> 
  </div>
  <script>
    $(".tvc-help-slider").slick({
        autoplay: false,
        dots: false,
        prevArrow:$('.help-ga-prev'),
        nextArrow:$('.help-ga-next')
    });
    $(".tvc-help-slider").on("beforeChange", function(event, slick, currentSlide, nextSlide){
      $("#help_ga_paging_info").html(nextSlide+1);
    });
  </script>
  <div class="right-content">
    <div class="content-section">
      <div class="content-icon">
        <img src="<?php echo ENHANCAD_PLUGIN_URL.'/admin/images/icon/lamp.svg'; ?>" alt="information"/>
      </div>
      <h4 class="content-heading">Business Value:</h4>
      <section class="tvc-b-value-slider">
         <div>
          <p>Once you set up google analytics account, your website will be tagged for all the important eCommerce events in google analytics and you will be able to start taking data driven decisions in order to scale your eCommerce business faster. Some of the important data points are:</p>
          <ol>
            <li>What exactly is your site’s conversion rate?</li>
            <li>What is the exact drop at each stage in your eCommerce funnel? For example, 100 people are coming to your website, how many users are seeing any product detail page, how many are adding products to cart, how many are abandoning cart etc.</li>
          </ol>
         </div>
         <div>What all are your star products and what all are just consuming the space in your website?</div>
      </section>      
    </div>
    <nav>
        <ul class="pagination justify-content-center">
          <li class="page-item page-prev b-page-prev value-ga-prev">
            <span class="page-link"><img src="<?php echo ENHANCAD_PLUGIN_URL.'/admin/images/icon/arrow-down-sign-to-navigate.svg'; ?>" alt=""/></span>
          </li>
          <li class="page-item"><span class="paging_info" id="value_ga_paging_info">1</span></li>
          <li class="page-item page-next b-page-next value-ga-next">
            <span class="page-link"><img src="<?php echo ENHANCAD_PLUGIN_URL.'/admin/images/icon/arrow-down-sign-to-navigate.svg'; ?>" alt=""/></span>
          </li>
        </ul>
      </nav> 
  </div>
  <div class="tvc-footer-links">
    <a target="_blank" href="http://plugins.tatvic.com/help-center/Installation-Manual.pdf" tabindex="0">Installation manual</a> | <a target="_blank" href="http://plugins.tatvic.com/help-center/Google-shopping-Guide.pdf" tabindex="0">Google shopping guide</a> | <a target="_blank" href="https://wordpress.org/plugins/enhanced-e-commerce-for-woocommerce-store/faq/" tabindex="0">FAQ</a>
  </div>
  <script>
    $(".tvc-b-value-slider").slick({
        autoplay: false,
        dots: false,
        prevArrow:$('.value-ga-prev'),
        nextArrow:$('.value-ga-next')
    });
    $(".tvc-b-value-slider").on("beforeChange", function(event, slick, currentSlide, nextSlide){
      $("#value_ga_paging_info").html(nextSlide+1);
    });
  </script>
  <?php
  return ob_get_clean();    
}