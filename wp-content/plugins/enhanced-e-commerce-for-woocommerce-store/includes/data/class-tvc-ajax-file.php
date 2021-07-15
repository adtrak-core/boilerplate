<?php

/**
* TVC Ajax File Class.
*
* @package TVC Product Feed Manager/Data/Classes
*/
if(!defined('ABSPATH')){
exit;
}

if(!class_exists('TVC_Ajax_File')) :
/**
 * Ajax File Class
 */
class TVC_Ajax_File extends TVC_Ajax_Calls {
  private $apiDomain;
  protected $access_token;
  protected $refresh_token;
  public function __construct(){
    parent::__construct();
    $this->apiDomain = TVC_API_CALL_URL;
    // hooks
    add_action('wp_ajax_tvcajax-get-campaign-categories', array($this, 'tvcajax_get_campaign_categories'));
    add_action('wp_ajax_tvcajax-update-campaign-status', array($this, 'tvcajax_update_campaign_status'));
    add_action('wp_ajax_tvcajax-delete-campaign', array($this, 'tvcajax_delete_campaign'));
    
    add_action('wp_ajax_tvcajax-product-syncup', array($this, 'tvcajax_product_syncup'));
    add_action('wp_ajax_tvcajax-gmc-category-lists', array($this, 'tvcajax_get_gmc_categories'));
    add_action('wp_ajax_tvcajax-custom-metrics-dimension', array($this, 'tvcajax_custom_metrics_dimension'));
    add_action('wp_ajax_tvcajax-store-time-taken', array($this, 'tvcajax_store_time_taken'));

    add_action('wp_ajax_tvc_call_api_sync', array($this, 'tvc_call_api_sync'));
    add_action('wp_ajax_tvc_call_domain_claim', array($this, 'tvc_call_domain_claim'));
    add_action('wp_ajax_tvc_call_site_verified', array($this, 'tvc_call_site_verified'));
    add_action('wp_ajax_tvc_call_notice_dismiss', array($this, 'tvc_call_notice_dismiss'));
    add_action('wp_ajax_tvc_call_notification_dismiss', array($this, 'tvc_call_notification_dismiss'));
    add_action('wp_ajax_tvc_call_active_licence', array($this, 'tvc_call_active_licence'));
    add_action('wp_ajax_tvc_call_add_survey', array($this, 'tvc_call_add_survey'));
  }

  public function tvc_call_add_survey(){
    if ( is_admin() ) {
      if(!class_exists('CustomApi')){
        include(ENHANCAD_PLUGIN_DIR . 'includes/setup/CustomApi.php');
      }
      $customObj = new CustomApi();
      unset($_POST['action']);    
      echo json_encode($customObj->add_survey_of_deactivate_plugin($_POST));
      exit;
    }
  }
  //active licence key
  public function tvc_call_active_licence(){
    if ( is_admin() ) {
      $licence_key = isset($_POST['licence_key'])?$_POST['licence_key']:"";
      $TVC_Admin_Helper = new TVC_Admin_Helper();
      $subscription_id = $TVC_Admin_Helper->get_subscriptionId();      
      if($subscription_id!="" && $licence_key != ""){
        $response = $TVC_Admin_Helper->active_licence($licence_key, $subscription_id);
        
        if($response->error== false){
          //$key, $html, $title = null, $link = null, $link_title = null, $overwrite= false
          $TVC_Admin_Helper->add_ee_msg_nofification("active_licence_key", "Your plan is now successfully activated.", "Congratulations!!", "", "", true);
          $TVC_Admin_Helper->update_subscription_details_api_to_db();
          echo json_encode(array('error' => false, "is_connect"=>true, 'message' => "The licence key has been activated."));
        }else{
          echo json_encode(array('error' => true, "is_connect"=>true, 'message' => $response->message));
        }       
      }else if($licence_key != ""){ 
        $ee_additional_data = $TVC_Admin_Helper->get_ee_additional_data();
        $ee_additional_data['temp_active_licence_key'] = $licence_key;
        $TVC_Admin_Helper->set_ee_additional_data($ee_additional_data);       
        echo json_encode(array('error' => true, "is_connect"=>false, 'message' => ""));
      }else{
        echo json_encode(array('error' => true, "is_connect"=>false, 'message' => "Licence key is required."));
      }      
    }
    exit;
  }

  public function tvc_call_notification_dismiss(){
    if($this->safe_ajax_call(filter_input(INPUT_POST, 'TVCNonce'), 'tvc_call_notification_dismiss-nonce')){      
      $ee_dismiss_id = isset($_POST['data']['ee_dismiss_id'])?$_POST['data']['ee_dismiss_id']:"";
      if($ee_dismiss_id != ""){
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $ee_msg_list = $TVC_Admin_Helper->get_ee_msg_nofification_list();
        if( isset($ee_msg_list[$ee_dismiss_id]) ){          
          unset($ee_msg_list[$ee_dismiss_id]);
          $ee_msg_list[$ee_dismiss_id]["active"]=0;
          $TVC_Admin_Helper->set_ee_msg_nofification_list($ee_msg_list);
          echo json_encode(array('status' => 'success', 'message' => $ee_additional_data));
        }
        
      }       
    }
    exit;
  }
  public function tvc_call_notice_dismiss(){
    if($this->safe_ajax_call(filter_input(INPUT_POST, 'apiNoticDismissNonce'), 'tvc_call_notice_dismiss-nonce')){      
      $ee_notice_dismiss_id = $_POST['data']['ee_notice_dismiss_id'];
      if($ee_notice_dismiss_id != ""){
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $ee_additional_data = $TVC_Admin_Helper->get_ee_additional_data();
        $ee_additional_data['dismissed_'.$ee_notice_dismiss_id] = 1;
        $TVC_Admin_Helper->set_ee_additional_data($ee_additional_data);
        echo json_encode(array('status' => 'success', 'message' => $ee_additional_data));
      }       
    }
    exit;
  }
  public function tvc_call_api_sync(){
    if($this->safe_ajax_call(filter_input(INPUT_POST, 'apiSyncupNonce'), 'tvc_call_api_sync-nonce')){
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $api_rs = $TVC_Admin_Helper->set_update_api_to_db();
        if(isset($api_rs['error']) && isset($api_rs['message']) && $api_rs['message']){
          echo json_encode($api_rs);
        }else{
          echo json_encode(array('error' => true, 'message' => "Please try after some time."));
        }
        exit;
    }
    exit;
  }
  public function tvc_call_site_verified(){
    if($this->safe_ajax_call(filter_input(INPUT_POST, 'SiteVerifiedNonce'), 'tvc_call_site_verified-nonce')){
      $TVC_Admin_Helper = new TVC_Admin_Helper();
      $tvc_rs =[];
      $tvc_rs = $TVC_Admin_Helper->call_site_verified();
      if(isset($tvc_rs['error']) && $tvc_rs['error'] == 1){
        echo json_encode(array('status' => 'error', 'message' => $tvc_rs['msg']));
      }else{
        echo json_encode(array('status' => 'success', 'message' => $tvc_rs['msg']));
      }      
      exit;
    }
    exit;
  }
  public function tvc_call_domain_claim(){
    if($this->safe_ajax_call(filter_input(INPUT_POST, 'apiDomainClaimNonce'), 'tvc_call_domain_claim-nonce')){
      $TVC_Admin_Helper = new TVC_Admin_Helper();
      $tvc_rs = $TVC_Admin_Helper->call_domain_claim();
      if(isset($tvc_rs['error']) && $tvc_rs['error'] == 1){
        echo json_encode(array('status' => 'error', 'message' => $tvc_rs['msg']));
      }else{
        echo json_encode(array('status' => 'success', 'message' => $tvc_rs['msg']));
      }      
      exit;
    }
    exit;
  }
  public function get_tvc_access_token(){
      if(!empty($this->access_token)){
          return $this->access_token;
      }else   if(isset($_SESSION['access_token']) && $_SESSION['access_token']){
          $this->access_token = $_SESSION['access_token'];
          return $this->access_token;
      }else{
          $TVC_Admin_Helper = new TVC_Admin_Helper();
          $google_detail = $TVC_Admin_Helper->get_ee_options_data();          
          $this->access_token = $google_detail['setting']->access_token;
          return $this->access_token;
      }
  }
  
  public function get_tvc_refresh_token(){
      if(!empty($this->refresh_token)){
          return $this->refresh_token;
      }else   if(isset($_SESSION['refresh_token']) && $_SESSION['refresh_token']){
          $this->refresh_token = $_SESSION['refresh_token'];
          return $this->refresh_token;
      }else{
          $TVC_Admin_Helper = new TVC_Admin_Helper();
          $google_detail = $TVC_Admin_Helper->get_ee_options_data();          
          $this->refresh_token = $google_detail['setting']->refresh_token;
          return $this->refresh_token;
      }
  }
  /**
   * Delete the campaign
   */
  public function tvcajax_delete_campaign(){
      // make sure this call is legal
      if($this->safe_ajax_call(filter_input(INPUT_POST, 'campaignDeleteNonce'), 'tvcajax-delete-campaign-nonce')){

          $merchantId = filter_input(INPUT_POST, 'merchantId');
          $customerId = filter_input(INPUT_POST, 'customerId');
          $campaignId = filter_input(INPUT_POST, 'campaignId');

          $url = $this->apiDomain.'/campaigns/delete';

          $data = [
              'merchant_id' => $merchantId,
              'customer_id' => $customerId,
              'campaign_id' => $campaignId
          ];

          $args = array(
              'headers' => array(
                  'Authorization' => "Bearer MTIzNA==",
                  'Content-Type' => 'application/json'
              ),
              'method' => 'DELETE',
              'body' => wp_json_encode($data)
          );

          // Send remote request
          $request = wp_remote_request($url, $args);

          // Retrieve information
          $response_code = wp_remote_retrieve_response_code($request);
          $response_message = wp_remote_retrieve_response_message($request);
          $response_body = json_decode(wp_remote_retrieve_body($request));

          if((isset($response_body->error) && $response_body->error == '')){
              $message = $response_body->message;
              echo json_encode(['status' => 'success', 'message' => $message]);
//                    return new WP_REST_Response(
//                        array(
//                            'status' => $response_code,
//                            'message' => $response_message,
//                            'data' => $response_body->data
//                        )
//                    );
          }else{
              $message = is_array($response_body->errors) ? $response_body->errors[0] : "Face some unprocessable entity";
              echo json_encode(['status' => 'error', 'message' => $message]);
              // return new WP_Error($response_code, $response_message, $response_body);
          }

          //   echo json_encode( $categories );
      }
      // IMPORTANT: don't forget to exit
      exit;
  }

  /**
   * Update the campaign status pause/active
   */
  public function tvcajax_update_campaign_status(){
      // make sure this call is legal
      if($this->safe_ajax_call(filter_input(INPUT_POST, 'campaignStatusNonce'), 'tvcajax-update-campaign-status-nonce')){

          if(!class_exists('ShoppingApi')){
              //require_once(__DIR__ . '/ShoppingApi.php');
              include(ENHANCAD_PLUGIN_DIR . 'includes/setup/ShoppingApi.php');
          }

          $merchantId = filter_input(INPUT_POST, 'merchantId');
          $customerId = filter_input(INPUT_POST, 'customerId');
          $campaignId = filter_input(INPUT_POST, 'campaignId');
          $budgetId = filter_input(INPUT_POST, 'budgetId');
          $campaignName = filter_input(INPUT_POST, 'campaignName');
          $budget = filter_input(INPUT_POST, 'budget');
          $status = filter_input(INPUT_POST, 'status');
          $url = $this->apiDomain.'/campaigns/update';
          $shoppingObj = new ShoppingApi();
          $campaignData = $shoppingObj->getCampaignDetails($campaignId);

          $data = [
              'merchant_id' => $merchantId,
              'customer_id' => $customerId,
              'campaign_id' => $campaignId,
              'account_budget_id' => $budgetId,
              'campaign_name' => $campaignName,
              'budget' => $budget,
              'status' => $status,
              'target_country' => $campaignData->data['data']->targetCountry,
              'ad_group_id' => $campaignData->data['data']->adGroupId,
              'ad_group_resource_name' => $campaignData->data['data']->adGroupResourceName
          ];

          $args = array(
              'headers' => array(
                  'Authorization' => "Bearer MTIzNA==",
                  'Content-Type' => 'application/json'
              ),
              'method' => 'PATCH',
              'body' => wp_json_encode($data)
          );

          // Send remote request
          $request = wp_remote_request($url, $args);

          // Retrieve information
          $response_code = wp_remote_retrieve_response_code($request);
          $response_message = wp_remote_retrieve_response_message($request);
          $response_body = json_decode(wp_remote_retrieve_body($request));
          if((isset($response_body->error) && $response_body->error == '')){
              $message = $response_body->message;
              echo json_encode(['status' => 'success', 'message' => $message]);
//                    return new WP_REST_Response(
//                        array(
//                            'status' => $response_code,
//                            'message' => $response_message,
//                            'data' => $response_body->data
//                        )
//                    );
          }else{
              $message = is_array($response_body->errors) ? $response_body->errors[0] : "Face some unprocessable entity";
              echo json_encode(['status' => 'error', 'message' => $message]);
              // return new WP_Error($response_code, $response_message, $response_body);
          }

          //   echo json_encode( $categories );
      }

      // IMPORTANT: don't forget to exit
      exit;
  }

  /**
   * Returns the campaign categories from a selected country
   */
  public function tvcajax_get_campaign_categories(){
      // make sure this call is legal
      if($this->safe_ajax_call(filter_input(INPUT_POST, 'campaignCategoryListsNonce'), 'tvcajax-campaign-category-lists-nonce')){

          $country_code = filter_input(INPUT_POST, 'countryCode');
          $customer_id = filter_input(INPUT_POST, 'customerId');
          $url = $this->apiDomain.'/products/categories';

          $data = [
              'customer_id' => $customer_id,
              'country_code' => $country_code
          ];

          $args = array(
              'headers' => array(
                  'Authorization' => "Bearer MTIzNA==",
                  'Content-Type' => 'application/json'
              ),
              'body' => wp_json_encode($data)
          );

          // Send remote request
          $request = wp_remote_post($url, $args);

          // Retrieve information
          $response_code = wp_remote_retrieve_response_code($request);
          $response_message = wp_remote_retrieve_response_message($request);
          $response_body = json_decode(wp_remote_retrieve_body($request));

          if((isset($response_body->error) && $response_body->error == '')){
              echo json_encode($response_body->data);
//                    return new WP_REST_Response(
//                        array(
//                            'status' => $response_code,
//                            'message' => $response_message,
//                            'data' => $response_body->data
//                        )
//                    );
          }else{
              echo json_encode([]);
              // return new WP_Error($response_code, $response_message, $response_body);
          }

          //   echo json_encode( $categories );
      }

      // IMPORTANT: don't forget to exit
      exit;
  }

  /**
   * Returns the campaign categories from a selected country
   */
  public function tvcajax_get_gmc_categories(){
      // make sure this call is legal
      if($this->safe_ajax_call(filter_input(INPUT_POST, 'gmcCategoryListsNonce'), 'tvcajax-gmc-category-lists-nonce')){

          $country_code = filter_input(INPUT_POST, 'countryCode');
          $customer_id = filter_input(INPUT_POST, 'customerId');
          $parent = filter_input(INPUT_POST, 'parent');
          $url = $this->apiDomain.'/products/gmc-categories';

          $data = [
              'customer_id' => $customer_id,
              'country_code' => $country_code,
              'parent' => $parent
          ];

          $args = array(
              'headers' => array(
                  'Authorization' => "Bearer MTIzNA==",
                  'Content-Type' => 'application/json'
              ),
              'body' => wp_json_encode($data)
          );

          // Send remote request
          $request = wp_remote_post($url, $args);

          // Retrieve information
          $response_code = wp_remote_retrieve_response_code($request);
          $response_message = wp_remote_retrieve_response_message($request);
          $response_body = json_decode(wp_remote_retrieve_body($request));

          if((isset($response_body->error) && $response_body->error == '')){
              echo json_encode($response_body->data);
//                    return new WP_REST_Response(
//                        array(
//                            'status' => $response_code,
//                            'message' => $response_message,
//                            'data' => $response_body->data
//                        )
//                    );
          }else{
              echo json_encode([]);
              // return new WP_Error($response_code, $response_message, $response_body);
          }

          //   echo json_encode( $categories );
      }

      // IMPORTANT: don't forget to exit
      exit;
  }

  /**
   * create product batch for product sync up
   */
  public function tvcajax_product_syncup(){
    // make sure this call is legal
    ini_set('max_execution_time', '0');
    ini_set('memory_limit','-1');
    if($this->safe_ajax_call(filter_input(INPUT_POST, 'productSyncupNonce'), 'tvcajax-product-syncup-nonce')){

      if(!class_exists('CustomApi')){
          include(ENHANCAD_PLUGIN_DIR . 'includes/setup/CustomApi.php');
      }
      $customObj = new CustomApi();
      //echo "<br>1=>".round(microtime(true) * 1000);
      $batchId = time();
      $merchantId = filter_input(INPUT_POST, 'merchantId');
      $customerId = filter_input(INPUT_POST, 'customerId');
      $accountId = filter_input(INPUT_POST, 'accountId');
      $subscriptionId = filter_input(INPUT_POST, 'subscriptionId');
      //$platformCustomerId = filter_input(INPUT_POST, 'platformCustomerId');
      $data = filter_input(INPUT_POST, 'data');          
      parse_str($data, $formArray);      
      $mappedCatsDB = [];
      $mappedCats = [];
      $mappedAttrs = [];
      $skipProducts = [];
      foreach($formArray as $key => $value){
        if(preg_match("/^category-name-/i", $key)){
          if($value != ''){
            $keyArray = explode("name-", $key);
            $mappedCatsDB[$keyArray[1]]['name'] = $value;
          }
          unset($formArray[$key]);
        }else if(preg_match("/^category-/i", $key)){
          if($value != '' && $value > 0){
            $keyArray = explode("-", $key);
            $mappedCats[$keyArray[1]] = $value;
            $mappedCatsDB[$keyArray[1]]['id'] = $value;
          }
          unset($formArray[$key]);
        }else{
          if($value){
              $mappedAttrs[$key] = $value;
          }
        }
      }             
      update_option("ee_prod_mapped_cats", serialize($mappedCatsDB));
      update_option("ee_prod_mapped_attrs", serialize($mappedAttrs));           

      if(!empty($mappedCats)){
          $catMapRequest = [];
          $catMapRequest['subscription_id'] = $subscriptionId;
          $catMapRequest['customer_id'] = $customerId;
          $catMapRequest['merchant_id'] = $merchantId;
          $catMapRequest['category'] = $mappedCats;
          //$catMapResponse = $customObj->setGmcCategoryMapping($catMapRequest);
      }
      if(!empty($mappedAttrs)){
          $attrMapRequest = [];
          $attrMapRequest['subscription_id'] = $subscriptionId;
          $attrMapRequest['customer_id'] = $customerId;
          $attrMapRequest['merchant_id'] = $merchantId;
          $attrMapRequest['attribute'] = $mappedAttrs;
          //$attrMapResponse = $customObj->setGmcAttributeMapping($attrMapRequest);
      }
      $TVC_Admin_Helper = new TVC_Admin_Helper();
      $cat_list = $TVC_Admin_Helper->get_tvc_product_cat_list_with_name();
      $tvc_currency = ((get_option('woocommerce_currency') != '')? get_option('woocommerce_currency') : 'USD');
      $temp_product=array();
      $fixed_att_select_list = array("gender", "age_group", "shipping", "tax", "content_language", "target_country", "condition");
      foreach ($fixed_att_select_list as $fixed_key) {
        if(isset($formArray[$fixed_key]) && $formArray[$fixed_key] != "" ){
          if($fixed_key == "shipping" && $formArray[$fixed_key] != ""){
            $temp_product[$fixed_key]['price']['value'] = $formArray[$fixed_key];
            $temp_product[$fixed_key]['price']['currency'] = $tvc_currency;
            $temp_product[$fixed_key]['country'] = $formArray['target_country'];
            //$temp_product[$fixed_key] =$formArray['target_country'].'::'.$formArray[$fixed_key].' '.$tvc_currency;
          }else if($fixed_key == "tax" && $formArray[$fixed_key] != ""){
            //$temp_product[$fixed_key] =$formArray['target_country'].'::'.$formArray[$fixed_key];
            $temp_product['taxes']['rate'] = $formArray[$fixed_key];
            $temp_product['taxes']['country'] = $formArray['target_country'];
          }else if( $formArray[$fixed_key] != ""){
            $temp_product[$fixed_key] = $formArray[$fixed_key];
          }          
        }
        unset($formArray[$fixed_key]);
      }
      
      $entries = [];
      if(!empty($mappedCats)){
        foreach($mappedCats as $mc_key => $mappedCat){
          $all_products = get_posts(array(
            'post_type' => 'product',
            'numberposts' => -1,
            'post_status' => 'publish',
            'tax_query' => array(
              array(
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => $mc_key, /* category name */
                'operator' => 'IN'
              )
            )
          ));
                 
          foreach($all_products as $postkey => $postvalue){
            
            $postmeta = [];
            $postmeta = $TVC_Admin_Helper->tvc_get_post_meta($postvalue->ID);
            $prd = wc_get_product($postvalue->ID);
            $postObj = (object) array_merge((array) $postvalue, (array) $postmeta);
            
            $product = array(
              'offer_id'=>$postvalue->ID,
              'channel'=>'online',
              'link'=>get_permalink($postvalue->ID),
              'google_product_category'=>$mappedCat
            );
            if(isset($cat_list[$mc_key]) && $cat_list[$mc_key]!=""){
              //$product['product_type'] = "Home > ".str_replace(array("-","_"), array(" "," ") , $cat_list[$mc_key]);
            }
            $product = array_merge($temp_product,$product);

            if($prd->get_type() == "variable"){             
              $variation_attributes = $prd->get_variation_attributes();
              $can_add_item_group_id = false;
              $is_color_size = false;
              if(!empty($variation_attributes)){
                foreach($variation_attributes as $va_key => $va_value ){
                  $va_key = str_replace("_", " ", $va_key);                  
                  if (strpos($va_key, 'color') !== false) {
                    $can_add_item_group_id = true;
                    $is_color_size = true;
                    break;
                  }else if (strpos($va_key, 'size') !== false) {
                    $can_add_item_group_id = true;
                    $is_color_size = true;
                    break;
                  }
                }
              }
              if(isset($formArray['gender']) && $formArray['gender']!= ""){
                $can_add_item_group_id = true;
              }else if(isset($formArray['age_group']) && $formArray['age_group']!= ""){
                $can_add_item_group_id = true;
              }
              //echo "<br>".$mc_key."=>".$postvalue->ID."can_add_item_group_id=>".$can_add_item_group_id;
              if($can_add_item_group_id == true){
                $p_variations = $prd->get_available_variations();                
                if(!empty($p_variations)){                  
                  foreach ($p_variations as $v_key => $v_value) {
                    $postmeta_var = (object)$TVC_Admin_Helper->tvc_get_post_meta($v_value['variation_id']);
                    $product['title'] = (isset($postObj->$formArray['title']))?$postObj->$formArray['title']:get_the_title($postvalue->ID);
                    $tvc_temp_desc_key = $formArray['description'];
                    $product['description'] = (isset($v_value['variation_description']) && $v_value['variation_description'] != "")?$v_value['variation_description']:$postObj->$tvc_temp_desc_key;
                    $product['offer_id'] = $v_value['variation_id'];
                    $product['id'] = $v_value['variation_id'];
                    $product['item_group_id'] = $postvalue->ID;
                    $image_id = $v_value['image_id'];
                    $product['image_link'] = wp_get_attachment_image_url($image_id, 'full');
                    if($is_color_size == true){
                      if(isset($v_value['attributes']) && !empty($v_value['attributes']) ){
                        foreach($v_value['attributes'] as $va_key => $va_value ){
                          $va_key = str_replace("_", " ", $va_key);                  
                          if (strpos($va_key, 'color') !== false) {
                            $product['color'] = $va_value;
                          }else if (strpos($va_key, 'size') !== false) {
                            $product['sizes'] = $va_value;
                          }
                        }
                      }
                    }
                    foreach($formArray as $key => $value){
                      if($key == 'price'){
                        if(isset($postmeta_var->$value) && $postmeta_var->$value > 0){
                          $product[$key]['value'] = $postmeta_var->$value;
                        }else if(isset($postmeta_var->_regular_price) && $postmeta_var->_regular_price && $postmeta_var->_regular_price >0 ){
                          $product[$key]['value'] = $postmeta_var->_regular_price;
                        }else if(isset($postmeta_var->_price) && $postmeta_var->_price && $postmeta_var->_price >0 ){
                          $product[$key]['value'] = $postmeta_var->_price;
                        }else if(isset($postmeta_var->_sale_price) && $postmeta_var->_sale_price && $postmeta_var->_sale_price >0 ){
                          $product[$key]['value'] = $postmeta_var->_sale_price;
                        }
                        if(isset($product[$key]['value']) && $product[$key]['value'] >0){
                          $product[$key]['currency'] = $tvc_currency;
                        }else{
                          $skipProducts[$postmeta_var->ID] = $postmeta_var;
                        }
                      }else if($key == 'sale_price'){
                        if(isset($postmeta_var->$value) && $postmeta_var->$value > 0){
                          $product[$key]['value'] = $postmeta_var->$value;
                        }else if(isset($postmeta_var->_sale_price) && $postmeta_var->_sale_price && $postmeta_var->_sale_price >0 ){
                          $product[$key]['value'] = $postmeta_var->_sale_price;
                        }
                        if(isset($product[$key]['value']) && $product[$key]['value'] >0){
                          $product[$key]['currency'] = $tvc_currency;
                        }                                                
                      }else if($key == 'availability'){
                        $tvc_find = array("instock","outofstock","onbackorder");
                        $tvc_replace = array("in stock","out of stock","preorder");
                        if(isset($postmeta_var->$value) && $postmeta_var->$value != ""){
                          $stock_status = $postmeta_var->$value;
                          $stock_status = str_replace($tvc_find,$tvc_replace,$stock_status);
                          $product[$key] = $stock_status;
                        }else{
                          $stock_status = $postmeta_var->_stock_status;
                          $stock_status = str_replace($tvc_find,$tvc_replace,$stock_status);
                          $product[$key] = $stock_status;
                        }
                      }else if(isset($postmeta_var->$value) && $postmeta_var->$value != ""){$product[$key] = $postmeta_var->$value;                        
                      }
                    }
                    $entrie = [
                      'merchant_id' => $merchantId,
                      'batch_id' => ++$batchId,
                      'method' => 'insert',
                      'product' => $product
                    ];
                    $entries[] = $entrie;
                  }
                }
              }else{
                goto simpleproduct;
              }
            }else{
              simpleproduct: 
              $image_id = $prd->get_image_id();
              $product['image_link'] = wp_get_attachment_image_url($image_id, 'full');     
              foreach($formArray as $key => $value){
                if($key == 'price'){
                  if(isset($postObj->$value) && $postObj->$value > 0){
                    $product[$key]['value'] = $postObj->$value;
                  }else if(isset($postObj->_regular_price) && $postObj->_regular_price && $postObj->_regular_price >0 ){
                    $product[$key]['value'] = $postObj->_regular_price;
                  }else if(isset($postObj->_price) && $postObj->_price && $postObj->_price >0 ){
                    $product[$key]['value'] = $postObj->_price;
                  }else if(isset($postObj->_sale_price) && $postObj->_sale_price && $postObj->_sale_price >0 ){
                    $product[$key]['value'] = $postObj->_sale_price;
                  }
                  if(isset($product[$key]['value']) && $product[$key]['value'] >0){
                    $product[$key]['currency'] = $tvc_currency;
                  }else{
                    $skipProducts[$postObj->ID] = $postObj;
                  }
                }else if($key == 'sale_price'){
                  if(isset($postObj->$value) && $postObj->$value > 0){
                    $product[$key]['value'] = $postObj->$value;
                  }else if(isset($postObj->_sale_price) && $postObj->_sale_price && $postObj->_sale_price >0 ){
                    $product[$key]['value'] = $postObj->_sale_price;
                  }
                  if(isset($product[$key]['value']) && $product[$key]['value'] >0){
                    $product[$key]['currency'] = $tvc_currency;
                  }                  
                }else if($key == 'availability'){
                  $tvc_find = array("instock","outofstock","onbackorder");
                  $tvc_replace = array("in stock","out of stock","preorder");
                  if(isset($postObj->$value) && $postObj->$value != ""){
                    $stock_status = $postObj->$value;
                    $stock_status = str_replace($tvc_find,$tvc_replace,$stock_status);
                    $product[$key] = $stock_status;
                  }else{
                    $stock_status = $postObj->_stock_status;
                    $stock_status = str_replace($tvc_find,$tvc_replace,$stock_status);
                    $product[$key] = $stock_status;
                  }
                }else if(isset($postObj->$value) && $postObj->$value != ""){
                  //echo $key."==".$postObj->$value."<br>";
                  $product[$key] = $postObj->$value;
                }
              }

              $entrie = [
                'merchant_id' => $merchantId,
                'batch_id' => ++$batchId,
                'method' => 'insert',
                'product' => $product
              ];
              $entries[] = $entrie;
            }

            
          }
          wp_reset_query();
        }
        $data = [
          'merchant_id' => $accountId,
          'account_id' => $merchantId,
          'subscription_id' => $subscriptionId,
          'entries' => $entries
        ];
        $url = $this->apiDomain.'/products/batch';        
        $args = array(
          'timeout' => 10000,
          'headers' => array(
            'Authorization' => "Bearer MTIzNA==",
            'Content-Type' => 'application/json',
            'AccessToken' => $this->generateAccessToken($this->get_tvc_access_token(), $this->get_tvc_refresh_token())
          ),
          'body' => wp_json_encode($data)
        );


        
        $request = wp_remote_post($url, $args); 
         
        // Retrieve information
        $response_code = wp_remote_retrieve_response_code($request);
        $response_message = wp_remote_retrieve_response_message($request);
        $response_body = json_decode(wp_remote_retrieve_body($request));
        
        if((isset($response_body->error) && $response_body->error == '')){
          $TVC_Admin_Auto_Product_sync_Helper = new TVC_Admin_Auto_Product_sync_Helper();
          $TVC_Admin_Auto_Product_sync_Helper->update_last_sync_in_db();
          $customObj->setGmcCategoryMapping($catMapRequest);
          $customObj->setGmcAttributeMapping($attrMapRequest);
          echo json_encode(['status' => 'success', 'skipProducts' => count($skipProducts)]);
        }else{
          foreach($response_body->errors as $err){
            $message = $err;
            break;
          }
          echo json_encode(['status' => 'error', 'message' => $message]);
        }
      }else{
          echo json_encode(['status' => 'error', 'message' => "Category mapping is null."]); 
      }
      
      //   echo json_encode( $categories );
    }
    // IMPORTANT: don't forget to exit
    exit;
  }

  /**
   * create product batch for product sync up
   */
  public function tvcajax_custom_metrics_dimension(){
      // make sure this call is legal
      if($this->safe_ajax_call(filter_input(INPUT_POST, 'customMetricsDimensionNonce'), 'tvcajax-custom-metrics-dimension-nonce')){

          if(!class_exists('CustomApi')){
              include(ENHANCAD_PLUGIN_DIR . 'includes/setup/CustomApi.php');
          }
          $customObj = new CustomApi();

          $accountId = filter_input(INPUT_POST, 'accountId');
//                $accountId = '184918792';
          $webPropertyId = filter_input(INPUT_POST, 'webPropertyId');
//                $webPropertyId = 'UA-184918792-5';
          $subscriptionId = filter_input(INPUT_POST, 'subscriptionId');
          $data = filter_input(INPUT_POST, 'data');
          parse_str($data, $formArray);
          // Only for print array

          $customDimension = [];
          $customMetrics = [];
          $dimensions = [];
          $metrics = [];

          for($i = 1; $i <= 12; $i++){
              $dimension['id'] = "";
              $dimension['index'] = $formArray['did-' . $i];
              $dimension['active'] = true;
              $dimension['kind'] = "";
              $dimension['name'] = $formArray['dname-' . $i];
              $dimension['scope'] = $formArray['dscope-' . $i];
              $dimension['created'] = "";
              $dimension['updated'] = "";
              $dimension['self_link'] = "";
              $dimension['parent_link']['href'] = "";
              $dimension['parent_link']['parent_link_type'] = "";
              $dimensions[] = $dimension;
          }

          for($i = 1; $i <= 7; $i++){
              $metric['id'] = "";
              $metric['index'] = $formArray['mid-' . $i];
              $metric['active'] = true;
              $metric['kind'] = "";
              $metric['name'] = $formArray['mname-' . $i];
              $metric['scope'] = $formArray['mscope-' . $i];
              $metric['created'] = "";
              $metric['updated'] = "";
              $metric['self_link'] = "";
              $metric['max_value'] = "";
              $metric['min_value'] = "";
              $metric['type'] = "INTEGER";
              $metric['parent_link']['href'] = "";
              $metric['parent_link']['parent_link_type'] = "";
              $metrics[] = $metric;
          }

          if(!empty($dimensions)){
              $dimenRequest = [];
              $dimenRequest['account_id'] = $accountId;
              $dimenRequest['web_property_id'] = $webPropertyId;
              $dimenRequest['subscription_id'] = $subscriptionId;
              $dimenRequest['data'] = $dimensions;
              $dimenResponse = $customObj->createCustomDimensions($dimenRequest);
          }
          if(!empty($metrics)){
              $metrRequest = [];
              $metrRequest['account_id'] = $accountId;
              $metrRequest['web_property_id'] = $webPropertyId;
              $metrRequest['subscription_id'] = $subscriptionId;
              $metrRequest['data'] = $metrics;
              $metrResponse = $customObj->createCustomMetrics($metrRequest);
          }


          // Retrieve information
          /* $response_code = wp_remote_retrieve_response_code($request);
            $response_message = wp_remote_retrieve_response_message($request);
            $response_body = json_decode(wp_remote_retrieve_body($request)); */




          if((isset($dimenResponse->error) && $dimenResponse->error == '' && isset($metrResponse->error) && $metrResponse->error == '')){
              echo json_encode(['status' => 'success']);
//                    return new WP_REST_Response(
//                        array(
//                            'status' => $response_code,
//                            'message' => $response_message,
//                            'data' => $response_body->data
//                        )
//                    );
          }else{
              $metrError = '';
              $dimenError = '';
              $message = NULL;
              if($dimenResponse->errors){
                  
                  $dimenError = $dimenResponse->errors[0];
                  $message = str_replace('this entity', 'dimensions ', $dimenError);
              }
              if($metrResponse->errors){
                  $metrError = str_replace('this entity', 'metrics ', $metrResponse->errors[0]);
                  $message = is_null($message) ? $metrError : $message . ' ' . $metrError;
              }
              echo json_encode(['status' => 'error', 'message' => $message]);
          }
      }

      // IMPORTANT: don't forget to exit
      exit;
  }

  public function tvcajax_store_time_taken(){
      // make sure this call is legal
      if($this->safe_ajax_call(filter_input(INPUT_POST, 'campaignCategoryListsNonce'), 'tvcajax-store-time-taken-nonce')){
          $ee_options_data = unserialize(get_option('ee_options'));
          if(isset($ee_options_data['subscription_id'])) {
              $ee_subscription_id = $ee_options_data['subscription_id'];
          } else {
              $ee_subscription_id = null;
          }
          $url = $this->apiDomain.'/customer-subscriptions/update-setup-time';
          $data = [
              'subscription_id' => $ee_subscription_id,
              'setup_start_time' => date('Y-m-d H:i:s'),
          ];
          $args = array(
              'headers' => array(
                  'Authorization' => "Bearer MTIzNA==",
                  'Content-Type' => 'application/json'
              ),
              'body' => wp_json_encode($data)
          );
          // Send remote request
          $request = wp_remote_post($url, $args);
          // Retrieve information
          $response_code = wp_remote_retrieve_response_code($request);
          $response_message = wp_remote_retrieve_response_message($request);
          $response_body = json_decode(wp_remote_retrieve_body($request));
          if((isset($response_body->error) && $response_body->error == '')){
              echo json_encode($response_body->data);
//                    return new WP_REST_Response(
//                        array(
//                            'status' => $response_code,
//                            'message' => $response_message,
//                            'data' => $response_body->data
//                        )
//                    );
          }else{
              echo json_encode([]);
              // return new WP_Error($response_code, $response_message, $response_body);
          }

          //   echo json_encode( $categories );
      }

      // IMPORTANT: don't forget to exit
      exit;
  }
  public function generateAccessToken($access_token, $refresh_token) {
      $request = "https://www.googleapis.com/oauth2/v1/tokeninfo?"
              . "access_token=" . $access_token;

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $request);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
      $response = curl_exec($ch);
      $result = json_decode($response);

      if (isset($result->error) && $result->error) {
          $credentials_file = ENHANCAD_PLUGIN_DIR . 'includes/setup/json/client-secrets.json';
          $str = file_get_contents($credentials_file);
          $credentials = $str ? json_decode($str, true) : [];
          $url = 'https://www.googleapis.com/oauth2/v4/token';
          $header = array("content-type: application/json");
          $clientId = $credentials['web']['client_id'];
          $clientSecret = $credentials['web']['client_secret'];
          $refreshToken = $refresh_token;
          $data = [
              "grant_type" => 'refresh_token',
              "client_id" => $clientId,
              'client_secret' => $clientSecret,
              'refresh_token' => $refreshToken,
          ];

          $postData = json_encode($data);
          $ch = curl_init();
          curl_setopt_array($ch, array(
              CURLOPT_URL => $url, //esc_url($this->curl_url),
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_HTTPHEADER => $header,
              CURLOPT_POSTFIELDS => $postData
          ));
          $response = curl_exec($ch);
          $response = json_decode($response);
          return $response->access_token;
      } else {
          return $access_token;
      }
  }

}
// End of TVC_Ajax_File_Class
endif;
$tvcajax_file_class = new TVC_Ajax_File();