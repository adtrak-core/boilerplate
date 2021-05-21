<?php
echo "<script>var return_url ='".esc_url($this->url)."';</script>";
$TVC_Admin_Helper = new TVC_Admin_Helper();
if (isset($_GET['connect']) && isset($_GET['subscription_id'])) {
    
    if (isset($_GET['subscription_id']) && $_GET['subscription_id']) {
        $_POST['subscription_id'] = $_GET['subscription_id'];
        Enhanced_Ecommerce_Google_Settings::add_update_settings('ee_options');
    }    
    $customApiObj = new CustomApi();
    $google_detail = $customApiObj->getGoogleAnalyticDetail();    
    if(isset($google_detail->data['status']) && $google_detail->data['status'] == 200 && !isset($_POST['ee_submit_plugin'])){
        if (isset($google_detail->data['data'])) {
            $googleDetail = $google_detail->data['data'];
            // save API data in DB                      
            $postData = [
                'merchant_id' => $googleDetail->merchant_id,  
                'website_url' => $googleDetail->site_url, 
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
            $TVC_Admin_Helper->set_update_api_to_db($googleDetail);
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
    $class = 'notice notice-error';
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
$ads_trck_id = $TVC_Admin_Helper->get_ee_options_settings();
?>
<div class="container-fluid">
    <div class="row">
        <div class= "col col-9" >
            <div class="card mw-100" style="padding:0px;">
                <?php $message->show_message(); ?>
               <?php /* <div class="card-header">
                    <h3>Enhanced Ecommerce Google Analytics <a href = "https://wordpress.org/support/plugin/enhanced-e-commerce-for-woocommerce-store/reviews/" target="_blank" style="float: right">
                            <div class="rating">
                                <span>☆</span><span>☆</span><span>☆</span><span>☆</span><span>☆</span>
                            </div>
                        </a>
                    </h3>
                </div>*/ ?>
                <div class="card-body">
                    <form id="ee_plugin_form" class="tvc_ee_plugin_form" method="post" action="" enctype="multipart/form-data" >
                        <table class="table table-bordered">
                            <tbody>
                            <tr>
                                <td>
                                    <label class="align-middle" for="woocommerce_ee_google_analytics_ga_id">Universal Analytics ID</label>
                                </td>
                                <td>
                                    <?php if(isset($data['ga_id']) && $data['ga_id'] != '') { ?>
                                        <span class="tvc_title_val"><?= $data['ga_id']; ?></span>
                                        <p class="hint-text" style="color: #666;display: inline-block;float: right;padding-top: 5px;">To update analytics id, <a target="_blank" href="<?=esc_url($this->url)?>">click here</a></p>
                                    <?php } else {?>
                                        <div class="tvc_animate_btn_wrap">
                                            <button type="button" class="btn btn-primary tvc_animate_btn" data-toggle="modal" data-target="#staticBackdrop">
                                                Connect
                                            </button>
                                        </div>
                                    <?php } ?>
                                </td>

                            </tr>
                            <tr>
                                <td>
                                    <label class="align-middle" for="woocommerce_ee_google_analytics_ga_id">Google Analytics 4 ID</label>
                                </td>
                                <td>
                                    <?php if(isset($data['gm_id']) && $data['gm_id'] != '') { ?>
                                        <span class="tvc_title_val"><?= $data['gm_id']; ?></span>
                                        <p class="hint-text" style="color: #666;display: inline-block;float: right;padding-top: 5px;">To update google analytics 4 id, <a target="_blank" href="<?=esc_url($this->url)?>">click here</a></p>
                                    <?php } else {?>
                                        <div class="tvc_animate_btn_wrap">
                                        <button type="button" class="btn btn-primary tvc_animate_btn" data-toggle="modal" data-target="#staticBackdrop">
                                            Connect
                                        </button>
                                        </div>
                                    <?php } ?>
                                </td>

                            </tr>
                            <tr>
                                <td>
                                    <label class="align-middle" for="woocommerce_ee_google_analytics_ads_id">Linked Google Ads Account <i style="cursor: help;" class="fas fa-question-circle" title="To link Google Ads and Google Analytics, you’ll need administrative access to a Google Ads account and “Edit permissions” to a Google Analytics account.
Why link Google Ads and Analytics account?
When you link Google Ads and Analytics, you can:
See ad and site performance data in the Google Ads reports in Analytics.
Import Analytics goals and Ecommerce transactions into your Google Ads account.
Import cross-device conversions into your Google Ads account when you activate Google signals.
Import Analytics metrics like Bounce Rate, Avg. Session Duration, and Pages/Session into your Google Ads account.
Enhance your Google Ads remarketing with Analytics Remarketing and Dynamic Remarketing.
Get richer data in the Analytics Multi-Channel Funnels reports."></i></label>
                                </td>
                                <td>
                                    <?php if(isset($data['google_ads_id']) && $data['google_ads_id'] != '') { ?>
                                        <span class="tvc_title_val"><?= $data['google_ads_id']; ?></span>
                                        <p class="hint-text" style="color: #666;display: inline-block;float: right;padding-top: 5px;">To update ads account id, <a target="_blank" href="<?=esc_url($this->url)?>">click here</a></p>
                                    <?php } else {?>
                                        <div class="tvc_animate_btn_wrap">
                                            <button type="button" class="btn btn-primary tvc_animate_btn" data-toggle="modal" data-target="#staticBackdrop">
                                                Connect
                                            </button>
                                        </div>
                                    <?php } ?>
                                </td>

                            </tr>
                            <tr>
                                <td>
                                    <label class="align-middle" for="woocommerce_ee_google_analytics_merchant_id">Linked Google Merchant Center Account</label>
                                </td>
                                <td>
                                    <?php if(isset($data['google_merchant_id']) && $data['google_merchant_id'] != '') { ?>
                                        <span class="tvc_title_val"><?= $data['google_merchant_id']; ?></span>
                                        <p class="hint-text" style="color: #666;display: inline-block;float: right;padding-top: 5px;">To update merchant account id, <a target="_blank" href="<?=esc_url($this->url)?>">click here</a></p>
                                    <?php } else {?>
                                        <div class="tvc_animate_btn_wrap">
                                            <button type="button" class="btn btn-primary tvc_animate_btn" data-toggle="modal" data-target="#staticBackdrop">
                                                Connect
                                            </button>
                                        </div>
                                    <?php } ?>
                                </td>

                            </tr>
                            <tr>
                                <td>
                                    <label class="align-middle" for="tracking_code">Tracking Code</label>
                                </td>
                                <td>
                                    <label  class = "align-middle">
                                        <?php $ga_ST = !empty($data['ga_ST']) ? 'checked' : ''; ?>
                                        <input type="checkbox"  name="ga_ST" id="ga_ST" <?php echo $ga_ST; ?> >
                                        <label for="ga_ST">Add Global Site Tracking Code 'gtag.js'</label>
                                        
                                        <i style="cursor: help;" class="fas fa-question-circle" title="This feature adds new gtag.js tracking code to your store. You don't need to enable this if gtag.js is implemented via any third party analytics plugin."></i>
                                        <!--<p class="description">This feature adds new gtag.js tracking code to your store. You don't need to enable this if gtag.js is implemented via any third party analytics plugin.</p>-->
                                    </label><br/>
                                    <label  class = "align-middle">
                                        <?php $ga_eeT = !empty($data['ga_eeT']) ? 'checked' : ''; ?>
                                        <input type="checkbox"  name="ga_eeT" id="ga_eeT" <?php echo $ga_eeT; ?> >
                                        <label for="ga_eeT">Add Enhanced Ecommerce Tracking Code</label>
                                        
                                        <i style="cursor: help;" class="fas fa-question-circle" title="This feature adds Enhanced Ecommerce Tracking Code to your Store"></i>
                                        <!--<p class="description">This feature adds Enhanced Ecommerce Tracking Code to your Store</p>-->
                                    </label><br/>
                                    <label  class = "align-middle">
                                        <?php $ga_gUser = !empty($data['ga_gUser']) ? 'checked' : ''; ?>
                                        <input type="checkbox"  name="ga_gUser" id="ga_gUser" <?php echo $ga_gUser; ?> >
                                        <label for="ga_gUser">Add Code to Track the Login Step of Guest Users (Optional)</label>
                                        
                                        <i style="cursor: help;" class="fas fa-question-circle" title="If you have Guest Check out enable, we recommend you to add this code"></i>
                                        <!--<p class="description">If you have Guest Check out enable, we recommend you to add this code</p>-->
                                    </label><br/>    
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="ga_Impr">Impression Thresold</label>
                                </td>
                                <td>
                                    <?php $ga_Impr = !empty($data['ga_Impr']) ? $data['ga_Impr'] : 6; ?>
                                    <input type="number" min="1" id="ga_Impr"  name = "ga_Impr" value = "<?php echo $ga_Impr; ?>">
                                    <label for="ga_Impr"></label>
                                    <i style="cursor: help;" class="fas fa-question-circle" title="This feature sets Impression threshold for category page. It sends hit after these many numbers of products impressions."></i>
                                    <p class="description"><br><b>Note : To avoid processing load on server we recommend upto 6 Impression Thresold.</b></p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label class = "align-middle" for="ga_IPA">I.P. Anoymization</label>
                                </td>
                                <td>
                                    <label  class = "align-middle">
                                        <?php $ga_IPA = !empty($data['ga_IPA']) ? 'checked' : ''; ?>
                                        <input class="" type="checkbox" name="ga_IPA" id="ga_IPA"  <?php echo $ga_IPA; ?>>
                                        <label for="ga_IPA">Enable I.P. Anonymization</label>
                                        
                                        <i style="cursor: help;" class="fas fa-question-circle" title="Use this feature to anonymize (or stop collecting) the I.P Address of your users in Google Analytics. Be in legal compliance by using I.P Anonymization which is important for EU countries As per the GDPR compliance"></i>
                                        <!-- <p class="description">Use this feature to anonymize (or stop collecting) the I.P Address of your users in Google Analytics. Be in legal compliance by using I.P Anonymization which is important for EU countries As per the GDPR compliance</p>-->
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label class = "align-middle" for="ga_OPTOUT">Google Analytics Opt Out</label>
                                </td>
                                <td>
                                    <label  class = "align-middle">
                                        <?php $ga_OPTOUT = !empty($data['ga_OPTOUT']) ? 'checked' : ''; ?>
                                        <input class="" type="checkbox" name="ga_OPTOUT" id="ga_OPTOUT"  <?php echo $ga_OPTOUT; ?>>
                                        <label for="ga_OPTOUT">Enable Google Analytics Opt Out (Optional)</label>
                                        
                                        <i style="cursor: help;" class="fas fa-question-circle" title="Use this feature to provide website visitors the ability to prevent their data from being used by Google Analytics As per the GDPR compliance.Go through the documentation to check the setup"></i>
                                        <!--<p class="description">Use this feature to provide website visitors the ability to prevent their data from being used by Google Analytics As per the GDPR compliance.Go through the documentation to check the setup</p>-->
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label class = "align-middle" for="ga_PrivacyPolicy">Privacy Policy</label>
                                </td>
                                <td>
                                    <label  class = "align-middle">
                                        <?php $ga_PrivacyPolicy = !empty($data['ga_PrivacyPolicy']) ? 'checked' : ''; ?>
                                        <input type="checkbox" name="ga_PrivacyPolicy" id="ga_PrivacyPolicy" required="required" <?php echo $ga_PrivacyPolicy; ?>>
                                        <label for="ga_PrivacyPolicy">Accept Privacy Policy of Plugin</label>
                                        
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
                            <button type="submit"  class="btn btn-primary btn-success" id="ee_submit_plugin" name="ee_submit_plugin">Submit</button>
                        </p>
                    </form>
                </div>
            </div>
            <!-- Modal -->
            <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
                            <a target="_blank" class="ee-oauth-container btn darken-4 white black-text" href="<?=esc_url($this->url)?>" style="text-transform:none; margin: 0 auto;">
                                <p style="font-size: inherit; margin-top:5px;"><img width="20px" style="margin-right:8px" alt="Google sign-in"
                                         src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/53/Google_%22G%22_Logo.svg/512px-Google_%22G%22_Logo.svg.png" />Sign In With Google</p>
                            </a>
                            <!--sigin with google end-->
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal End -->
        </div>
        <?php require_once('sidebar.php'); ?>
    </div>
</div>