<?php
echo "<script>var return_url ='".esc_url($this->url)."';</script>";
$TVC_Admin_Helper = new TVC_Admin_Helper();
$class = "";
$message_p = "";
if (isset($_GET['connect']) && isset($_GET['subscription_id'])) { 
  /*
   * save subscription_id in "ee_options" and then API call for get subscription details
   */
  if (isset($_GET['subscription_id']) && $_GET['subscription_id']) {
    $_POST['subscription_id'] = $_GET['subscription_id'];
    Enhanced_Ecommerce_Google_Settings::add_update_settings('ee_options');
  }
  $customApiObj = new CustomApi();
  $google_detail = $customApiObj->getGoogleAnalyticDetail();
  /*
   * active licence key while come from server page
   */
  $ee_additional_data = $TVC_Admin_Helper->get_ee_additional_data();
  if(isset($ee_additional_data['temp_active_licence_key']) && $ee_additional_data['temp_active_licence_key'] != ""){
    $licence_key = $ee_additional_data['temp_active_licence_key'];
    $TVC_Admin_Helper->active_licence($licence_key, $_GET['subscription_id']);
    unset($ee_additional_data['temp_active_licence_key']);
    $TVC_Admin_Helper->set_ee_additional_data($ee_additional_data);
  }  
  
  if(property_exists($google_detail,"error") && $google_detail->error == false && !isset($_POST['ee_submit_plugin'])){
    if(property_exists($google_detail,"data") && $google_detail->data != ""){
      $googleDetail = $google_detail->data;
      //'website_url' => $googleDetail->site_url,                    
      $postData = [
          'merchant_id' => $googleDetail->merchant_id,          
          'website_url' => get_site_url(),
          'subscription_id' => $googleDetail->id,
          'account_id' => $googleDetail->google_merchant_center_id
      ];

      if ($googleDetail->is_site_verified == '0') {
          $postData['method']="file";
          $siteVerificationToken = $customApiObj->siteVerificationToken($postData);
          if (isset($siteVerificationToken->error) && !empty($siteVerificationToken->errors)) {
              goto call_method_tag;
          } else {
              $myFile = ABSPATH.$siteVerificationToken->data->token; 
              if (!file_exists($myFile)) {
                  $fh = fopen($myFile, 'w+');
                  chmod($myFile,0777);
                  $stringData = "google-site-verification: ".$siteVerificationToken->data->token;
                  fwrite($fh, $stringData);
                  fclose($fh);
              }
              $postData['method']="file";
              $siteVerification = $customApiObj->siteVerification($postData);
              if (isset($siteVerification->error) && !empty($siteVerification->errors)) {
                  call_method_tag:
                  //methd using tag
                  $postData['method']="meta";
                  $siteVerificationToken_tag = $customApiObj->siteVerificationToken($postData);
                  if(isset($siteVerificationToken_tag->data->token) && $siteVerificationToken_tag->data->token){
                      $TVC_Admin_Helper->set_ee_additional_data(array("add_site_varification_tag"=>1,"site_varification_tag_val"=> base64_encode($siteVerificationToken_tag->data->token)));
                      sleep(1);
                      $siteVerification_tag = $customApiObj->siteVerification($postData);
                      if(isset($siteVerification_tag->error) && !empty($siteVerification_tag->errors)){
                      }else{
                          $googleDetail->is_site_verified = '1';
                      }
                  }
              } else {
                  $googleDetail->is_site_verified = '1';
              }
          }
      }
      if ($googleDetail->is_domain_claim == '0') {
          $claimWebsite = $customApiObj->claimWebsite($postData);
          if (isset($claimWebsite->error) && !empty($claimWebsite->errors)) {    
          } else {
              $googleDetail->is_domain_claim = '1';
          }
      }
      $_POST['subscription_id'] = $googleDetail->id;
      $_POST['ga_eeT'] = (isset($googleDetail->enhanced_e_commerce_tracking) && $googleDetail->enhanced_e_commerce_tracking == "1") ? "on" : "";
      
      $_POST['ga_ST'] = (isset($googleDetail->add_gtag_snippet) && $googleDetail->add_gtag_snippet == "1") ? "on" : "";           
      $_POST['gm_id'] = $googleDetail->measurement_id;
      $_POST['ga_id'] = $googleDetail->property_id;
      $_POST['google_ads_id'] = $googleDetail->google_ads_id;
      $_POST['google_merchant_id'] = $googleDetail->google_merchant_center_id;
      $_POST['tracking_option'] = $googleDetail->tracking_option;
      $_POST['ga_gUser'] = 'on';
      //$_POST['ga_gCkout'] = 'on';
      $_POST['ga_Impr'] = 6;
      $_POST['ga_IPA'] = 'on';
      $_POST['ga_OPTOUT'] = 'on';
      $_POST['ga_PrivacyPolicy'] = 'on';
      $_POST['google-analytic'] = '';
      //update option in wordpress local database
      update_option('ads_tracking_id',  $googleDetail->google_ads_id);
      update_option('ads_ert', $googleDetail->remarketing_tags);
      update_option('ads_edrt', $googleDetail->dynamic_remarketing_tags);
      Enhanced_Ecommerce_Google_Settings::add_update_settings('ee_options');
      //save data in DB
      $TVC_Admin_Helper->set_update_api_to_db($googleDetail, false);
      $TVC_Admin_Helper->update_remarketing_snippets();
      if(isset($googleDetail->google_merchant_center_id) || isset($googleDetail->google_ads_id) ){
        if( $googleDetail->google_merchant_center_id != "" && $googleDetail->google_ads_id != ""){                    
          wp_redirect("admin.php?page=enhanced-ecommerce-google-analytics-admin-display&tab=sync_product_page&welcome_msg=true");
            exit;
        }else{
          wp_redirect("admin.php?page=enhanced-ecommerce-google-analytics-admin-display&tab=gaa_config_page&welcome_msg=true");
            exit;
        }
      }
    }
  }
} else if(isset($_GET['connect']) && !isset($_POST['ee_submit_plugin'])) {
    $googleDetail = [];
    $class = 'alert-message tvc-alert-error';
    $message_p = esc_html__('Google analytic detail is empty.');
    printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message_p));
}else{
    $TVC_Admin_Helper->is_ee_options_data_empty();
}
$message = new Enhanced_Ecommerce_Google_Settings();
if (isset($_POST['ee_submit_plugin'])) {
    if(!empty($_POST['ga_id'])){
      $_POST['tracking_option'] = "UA";
    }
    if(!empty($_POST['gm_id'])){
      $_POST['tracking_option'] = "GA4";
    }
    if(!empty($_POST['gm_id']) && !empty($_POST['ga_id'])){
      $_POST['tracking_option'] = "BOTH";
    }
    update_option('ads_tracking_id', $_POST['google_ads_id']);

    Enhanced_Ecommerce_Google_Settings::add_update_settings('ee_options');
    $class = 'alert-message tvc-alert-success';
    $message_p = __( 'Your settings have been saved.', 'sample-text-domain' );
    /* API Save */
    /*if(isset($_POST['ga_eeT'])){
        $_POST['enhanced_e_commerce_tracking']=($_POST['ga_eeT']=="on")?1:0;
        unset($_POST['ga_eeT']);
    }
    if(isset($_POST['ga_ST'])){
        $_POST['add_gtag_snippet']=($_POST['ga_ST']=="on")?1:0;
        unset($_POST['ga_ST']);
    } 
    if(isset($_POST['subscription_id']) && $_POST['subscription_id'] >0) {
        $customApiObj = new CustomApi();
        $response = $customApiObj->updateTrackingOption($_POST);        
        if (isset($response->errors) && !empty($response->errors)) {
            $error_code = array_keys($response->errors)[0];
            $class = 'notice notice-error';
            $r_message = esc_html__('The tracking options is not added successfully.');
            printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html('Error : ' . $r_message));
        } else {
            $response = $response->data;
            if (isset($response['status']) && $response['status'] == 200) {
                $class = 'notice notice-success';
                $r_message = esc_html__('The tracking options added successfully.');
                //printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($r_message));
            }
        }
    }else{
        $class = 'notice notice-error';
        $r_message = esc_html__('Connect Google account to enable more features.');
        printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html('Error : ' . $r_message));
    }*/
}
$data = unserialize(get_option('ee_options'));
$subscription_id = $TVC_Admin_Helper->get_subscriptionId();
$TVC_Admin_Helper->add_spinner_html();
$google_detail = $TVC_Admin_Helper->get_ee_options_data();
$googleDetail = "";
$plan_id = 1;
if(isset($google_detail['setting'])){
  $googleDetail = $google_detail['setting'];
  if(isset($googleDetail->plan_id) && !in_array($googleDetail->plan_id, array("1"))){
    $plan_id = $googleDetail->plan_id;
  }
}


?>
<div class="tab-content">
  <?php if($message_p){
    printf('<div class="%1$s"><div class="alert">%2$s</div></div>', esc_attr($class), esc_html($message_p));
  }?>
  <div class="tab-pane show active" id="googleShoppingFeed">
    <div class="tab-card">
      <div class="row">
        <div class="col-md-6 col-lg-8 border-right">
          <?php if($plan_id == 1){?>
          <div class="licence tvc-licence" >            
            <div class="tvc_licence_key_wapper <?php if($plan_id != 1){?>tvc-hide<?php }?>">
              <p>You are using our free plugin, no licence needed ! Happy analyzing..!! :)</p>
              <p class="font-weight-bold">To unlock more features of google products, consider our <a href="<?php echo $TVC_Admin_Helper->get_pro_plan_site(); ?>" target="_blank">pro version.</a></p>
              <?php /*
              <form method="post" name="google-analytic" id="tvc-licence-active"> 
                <div class="input-group">
                  <input type="text" id="licence_key" name="licence_key" class="form-control" placeholder="Already purchased? Enter licence key" required="">
                  <div class="input-group-append">
                    <button type="submit" class="btn btn-primary" name="verify-licence-key"><img src="<?php echo ENHANCAD_PLUGIN_URL.'/admin/images/icon/right-arrow.svg'; ?>" alt="active licence key"></button>
                  </div>
                </div>
              </form>
              */ ?>
            </div>         
          </div>
        <?php }?>
          <div class="google-account-analytics">
            <div class="row mb-3">
              <div class="col-6 col-md-6 col-lg-6">
                  <h2 class="ga-title">Connected Google Analytics Account:</h2>
              </div>
              <div class="col-6 col-md-6 col-lg-6 text-right">
                <div class="acc-num">
                  <p class="ga-text">
                    <?php echo  ((isset($data['ga_id']) && $data['ga_id'] != '') ? $data['ga_id'] : '<span>Get started</span>'); ?>
                  </p>
                  <?php
                  if (isset($data['ga_id']) && $data['ga_id'] != '') {
                    echo '<p class="ga-text text-right"><a target="_blank" href="' . $this->url . '" class="text-underline"><img src="'. ENHANCAD_PLUGIN_URL.'/admin/images/icon/refresh.svg" alt="refresh"/></a></p>';
                  } else { 
                    echo '<p class="ga-text text-right"><a href="#" class="text-underline" data-toggle="modal" data-target="#tvc_google_connect"><img src="'. ENHANCAD_PLUGIN_URL.'/admin/images/icon/add.svg" alt="connect account"/></a></p>';
                  }?>
                </div>
              </div>              
            </div>
            <div class="row mb-3">
              <div class="col-6 col-md-6 col-lg-6">
                <h2 class="ga-title">Connected Google Analytics 4 Account:</h2>
              </div>
              <div class="col-6 col-md-6 col-lg-6 text-right">
                <div class="acc-num">
                  <p class="ga-text">
                    <?php echo  ((isset($data['gm_id']) && $data['gm_id'] != '') ? $data['gm_id'] : '<span>Get started</span>'); ?>
                  </p>
                  <?php
                  if (isset($data['gm_id']) && $data['gm_id'] != '') {
                    echo '<p class="ga-text text-right"><a target="_blank" href="' . $this->url . '" class="text-underline"><img src="'. ENHANCAD_PLUGIN_URL.'/admin/images/icon/refresh.svg" alt="refresh"/></a></p>';
                  } else { 
                    echo '<p class="ga-text text-right"><a href="#" class="text-underline" data-toggle="modal" data-target="#tvc_google_connect"><img src="'. ENHANCAD_PLUGIN_URL.'/admin/images/icon/add.svg" alt="connect account"/></a></p>';
                  }?>
                </div>
              </div>              
            </div>
            <div class="row mb-3">
              <div class="col-6 col-md-6 col-lg-6">
                <h2 class="ga-title">Linked Google Ads Account:</h2>
              </div>
              <div class="col-6 col-md-6 col-lg-6 text-right">
                <div class="acc-num">
                  <p class="ga-text">
                    <?php echo  ((isset($data['google_ads_id']) && $data['google_ads_id'] != '') ? $data['google_ads_id'] : '<span>Get started</span>'); ?>
                  </p>
                  <?php
                  if (isset($data['google_ads_id']) && $data['google_ads_id'] != '') {
                    echo '<p class="ga-text text-right"><a target="_blank" href="' . $this->url . '" class="text-underline"><img src="'. ENHANCAD_PLUGIN_URL.'/admin/images/icon/refresh.svg" alt="refresh"/></a></p>';
                  } else { 
                    echo '<p class="ga-text text-right"><a href="#" class="text-underline" data-toggle="modal" data-target="#tvc_google_connect"><img src="'. ENHANCAD_PLUGIN_URL.'/admin/images/icon/add.svg" alt="connect account"/></a></p>';
                  }?>
                </div>
              </div>              
            </div>
            <div class="row mb-3">
              <div class="col-6 col-md-6 col-lg-6">
                <h2 class="ga-title">Linked Google Merchant Center Account:</h2>
              </div>
              <div class="col-6 col-md-6 col-lg-6 text-right">
                <div class="acc-num">
                  <p class="ga-text">
                    <?php echo  ((isset($data['google_merchant_id']) && $data['google_merchant_id'] != '') ? $data['google_merchant_id'] : '<span>Get started</span>'); ?>
                  </p>
                  <?php
                  if (isset($data['google_merchant_id']) && $data['google_merchant_id'] != '') {
                    echo '<p class="ga-text text-right"><a target="_blank" href="' . $this->url . '" class="text-underline"><img src="'. ENHANCAD_PLUGIN_URL.'/admin/images/icon/refresh.svg" alt="refresh"/></a></p>';
                  } else { 
                    echo '<p class="ga-text text-right"><a href="#" class="text-underline" data-toggle="modal" data-target="#tvc_google_connect"><img src="'. ENHANCAD_PLUGIN_URL.'/admin/images/icon/add.svg" alt="connect account"/></a></p>';
                  }?>
                </div>
              </div>              
            </div>
          </div>

          <form id="ee_plugin_form" class="tvc_ee_plugin_form" name="google-analytic-setting-form" method="post" >
            <table class="table">
              <tbody>
                <tr>
                  <th>
                    <label class="align-middle" for="tracking_code">Tracking Code</label>
                  </th>
                  <td>
                    <label  class = "align-middle">
                      <?php $ga_ST = !empty($data['ga_ST']) ? 'checked' : ''; ?>
                      <input type="checkbox"  name="ga_ST" id="ga_ST" <?php echo $ga_ST; ?> >
                      <label class="custom-control-label" for="ga_ST">Add Global Site Tracking Code 'gtag.js'</label>
                      
                      <i style="cursor: help;" class="fas fa-question-circle" title="This feature adds new gtag.js tracking code to your store. You don't need to enable this if gtag.js is implemented via any third party analytics plugin."></i>
                    </label><br/>
                    <label  class = "align-middle">
                        <?php $ga_eeT = !empty($data['ga_eeT']) ? 'checked' : ''; ?>
                        <input type="checkbox"  name="ga_eeT" id="ga_eeT" <?php echo $ga_eeT; ?> >
                        <label class="custom-control-label" for="ga_eeT">Add Enhanced Ecommerce Tracking Code</label>
                        
                        <i style="cursor: help;" class="fas fa-question-circle" title="This feature adds Enhanced Ecommerce Tracking Code to your Store"></i>
                    </label><br/>
                    <label class = "align-middle">
                        <?php $ga_gUser = !empty($data['ga_gUser']) ? 'checked' : ''; ?>
                        <input type="checkbox"  name="ga_gUser" id="ga_gUser" <?php echo $ga_gUser; ?> >
                        <label class="custom-control-label" for="ga_gUser">Add Code to Track the Login Step of Guest Users (Optional)</label>
                        
                        <i style="cursor: help;" class="fas fa-question-circle" title="If you have Guest Check out enable, we recommend you to add this code"></i>
                    </label>  
                  </td>
                </tr>
                <tr>
                  <th>
                    <label for="ga_Impr">Impression Thresold</label>
                  </th>
                  <td>
                    <?php $ga_Impr = !empty($data['ga_Impr']) ? $data['ga_Impr'] : 6; ?>
                    <input type="number" min="1" id="ga_Impr"  name = "ga_Impr" value = "<?php echo $ga_Impr; ?>">
                    <label for="ga_Impr"></label>
                    <i style="cursor: help;" class="fas fa-question-circle" title="This feature sets Impression threshold for category page. It sends hit after these many numbers of products impressions."></i>
                    <p class="description"><b>Note : To avoid processing load on server we recommend upto 6 Impression Thresold.</b></p>
                  </td>
                </tr>
                <tr>
                  <th>
                    <label class = "align-middle" for="ga_IPA">I.P. Anoymization</label>
                  </th>
                  <td>
                    <label  class = "align-middle">
                      <?php $ga_IPA = !empty($data['ga_IPA']) ? 'checked' : ''; ?>
                      <input class="" type="checkbox" name="ga_IPA" id="ga_IPA"  <?php echo $ga_IPA; ?>>
                      <label class="custom-control-label" for="ga_IPA">Enable I.P. Anonymization</label>
                      
                      <i style="cursor: help;" class="fas fa-question-circle" title="Use this feature to anonymize (or stop collecting) the I.P Address of your users in Google Analytics. Be in legal compliance by using I.P Anonymization which is important for EU countries As per the GDPR compliance"></i>
                    </label>
                  </td>
                </tr>
                <tr>
                  <th>
                      <label class = "align-middle" for="ga_OPTOUT">Google Analytics Opt Out</label>
                  </th>
                  <td>
                    <label  class = "align-middle">
                      <?php $ga_OPTOUT = !empty($data['ga_OPTOUT']) ? 'checked' : ''; ?>
                      <input class="" type="checkbox" name="ga_OPTOUT" id="ga_OPTOUT"  <?php echo $ga_OPTOUT; ?>>
                      <label class="custom-control-label" for="ga_OPTOUT">Enable Google Analytics Opt Out (Optional)</label>
                      
                      <i style="cursor: help;" class="fas fa-question-circle" title="Use this feature to provide website visitors the ability to prevent their data from being used by Google Analytics As per the GDPR compliance.Go through the documentation to check the setup"></i>
                    </label>
                  </td>
                </tr>
                <tr>
                  <th>
                    <label class = "align-middle" for="ga_PrivacyPolicy">Privacy Policy</label>
                  </th>
                  <td>
                    <label class = "align-middle">
                      <?php $ga_PrivacyPolicy = !empty($data['ga_PrivacyPolicy']) ? 'checked' : ''; ?>
                      <input type="checkbox" name="ga_PrivacyPolicy" id="ga_PrivacyPolicy" required="required" <?php echo $ga_PrivacyPolicy; ?>>
                      <label class="custom-control-label" for="ga_PrivacyPolicy">Accept Privacy Policy of Plugin</label>
                      
                      <p class="description">By using Tatvic Plugin, you agree to Tatvic plugin's <a href= "https://www.tatvic.com/privacy-policy/?ref=plugin_policy&utm_source=plugin_backend&utm_medium=woo_premium_plugin&utm_campaign=GDPR_complaince_ecomm_plugins" target="_blank">Privacy Policy</a></p>
                    </label>
                  </td>
                </tr>
              </tbody>
            </table>
            <p class="submit save-for-later" id="save-for-later">
              <input type="hidden" id="ga_id" name = "ga_id" value="<?= (!empty($data['ga_id']))?$data['ga_id']:""; ?>"/>
              <input type="hidden" id="gm_id" name = "gm_id" value="<?= (!empty($data['gm_id']))?$data['gm_id']:""; ?>"/>
              <input type="hidden" id="google_ads_id" name = "google_ads_id" value="<?= (!empty($data['google_ads_id']))?$data['google_ads_id']:""; ?>"/>
              <input type="hidden" id="google_merchant_id" name = "google_merchant_id" value="<?= (!empty($data['google_merchant_id']))?$data['google_merchant_id']:""; ?>"/>
              <input type="hidden" name="subscription_id" value="<?php echo (!empty($data['subscription_id']))?$data['subscription_id']:""; ?>">
              <button type="submit"  class="btn btn-primary" id="ee_submit_plugin" name="ee_submit_plugin">Save</button>
          </p>
          </form>
        </div>
        <div class="col-md-6 col-lg-4">
          <?php echo get_tvc_google_ga_sidebar(); ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php echo get_connect_google_popup_html(); 
  echo get_connect_google_popup_html_to_active_licence();
?>
<script>
$(document).ready(function () {
  $(document).on('click','#tvc_google_connect_active_licence_close',function(event){
    $('#tvc_google_connect_active_licence').modal('hide');
  });
  $(document).on('click','.tvc_licence_key_change',function(event){
    $(".tvc_licence_key_change_wapper").slideUp(500);
    $(".tvc_licence_key_wapper").slideDown(700);
  });
  $(document).on('submit','form#tvc-licence-active',function(event){
    event.preventDefault();
    let licence_key = $("#licence_key").val();
    var form_data = jQuery("#tvc-licence-active").serialize();
    if(licence_key!=""){
      var data = {
        action: "tvc_call_active_licence",
        licence_key:licence_key        
      };
      $.ajax({
        type: "POST",
        dataType: "json",
        url: myAjaxNonces.ajaxurl,
        data: data,
        beforeSend: function(){
          tvc_helper.loaderSection(true);
        },
        success: function(response){
          if (response.error === false) {          
            tvc_helper.tvc_alert("success","",response.message);
            setTimeout(function(){ 
              location.reload();
            }, 2000);
          }else{
            if( response.is_connect == false){    
              $('#tvc_google_connect_active_licence').modal('show');          
            }else{
              tvc_helper.tvc_alert("error","",response.message);
            }
          }
          tvc_helper.loaderSection(false);
        }
      });
    }else{
      tvc_helper.tvc_alert("error","Licence key is required.");
    }
  });
});
</script>