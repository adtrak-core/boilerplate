<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}
if ( ! class_exists( 'TVC_Admin_Auto_Product_sync_Helper' ) ) {
  Class TVC_Admin_Auto_Product_sync_Helper{
  	protected $TVC_Admin_Helper;
  	protected $TVC_Admin_DB_Helper;
    protected $time_space;
    private $apiDomain;
    protected $batch_size;
  	public function __construct() {
  		$this->TVC_Admin_Helper = new TVC_Admin_Helper();
  		$this->TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
      $this->apiDomain = TVC_API_CALL_URL;
      $this->includes();
      add_action('admin_init', array($this,'add_table_in_db'));
      $this->customApiObj = new CustomApi();
      $this->time_space = $this->TVC_Admin_Helper->get_auto_sync_time_space();
      $this->batch_size = $this->TVC_Admin_Helper->get_auto_sync_batch_size();
      //add_action('admin_init',array($this,'add_woo_req'));
      
      add_action('admin_init',array($this,'add_schedule_event'));
      add_action( 'ee_auto_product_sync_check', array($this, 'call_auto_sync_product' ), 10, 1 );

      //add_action('admin_init',array($this,'call_auto_sync_product_tttt'));
    }

    public function includes() {
      require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
      if (!class_exists('CustomApi')) {
        require_once(ENHANCAD_PLUGIN_DIR . 'includes/setup/CustomApi.php');
      }      
    }

    public function add_woo_req(){
     // include_once WC_ABSPATH . 'packages/action-scheduler/action-scheduler.php';
    }

    public function add_table_in_db(){       
      //add_filter( 'cron_schedules', array($this,'tvc_add_cron_interval') ); 
      global $wpdb;
      $tablename = $wpdb->prefix ."ee_product_sync_data";
      $query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $tablename ) );   
      if ( $wpdb->get_var( $query ) === $tablename ) {
          
      }else{     
        $sql_create = "CREATE TABLE ".$tablename." ( `id` BIGINT(20) NOT NULL AUTO_INCREMENT , `w_product_id` BIGINT(20) NOT NULL , `w_cat_id` INT(10) NOT NULL , `g_cat_id` INT(10) NOT NULL , `g_attribute_mapping` LONGTEXT NOT NULL , `update_date` DATE NOT NULL , `status` INT(1) NOT NULL DEFAULT '1', PRIMARY KEY (`id`) );";         
        if(maybe_create_table( $tablename, $sql_create )){  
          $this->import_last_sync_in_db();
        }
      }

      $tablename = $wpdb->prefix ."ee_product_sync_call"; 
      $sql_create = "CREATE TABLE ".$tablename." ( `id` BIGINT(20) NOT NULL AUTO_INCREMENT, `sync_product_ids` LONGTEXT NULL, `w_total_product` INT(10) NOT NULL , `total_sync_product` INT(10) NOT NULL ,last_sync  DATETIME NOT NULL, create_sync DATETIME NOT NULL, next_sync DATETIME NOT NULL, `last_sync_product_id` BIGINT(20) NOT NULL, `action_scheduler_id` INT(10) NOT NULL, `status` INT(1) NOT NULL COMMENT '0 failed, 1 completed', PRIMARY KEY (`id`) );";    
      if(!maybe_create_table( $tablename, $sql_create )){ }   
    }

    public function import_last_sync_in_db(){
      $ee_prod_mapped_cats = unserialize(get_option('ee_prod_mapped_cats'));
      $ee_prod_mapped_attrs = unserialize(get_option('ee_prod_mapped_attrs'));
      if($ee_prod_mapped_cats != "" && $ee_prod_mapped_attrs != ""){
        global $wpdb;
        //$table, $field_name = "*"
        $row_count = $this->TVC_Admin_DB_Helper->tvc_row_count('ee_product_sync_data');     
        if($row_count == 0){
          if(!empty($ee_prod_mapped_cats)){
            foreach($ee_prod_mapped_cats as $mc_key => $mappedCat){
              $args= array(
                'post_type' => 'product',
                'numberposts' => -1,
                'post_status' => 'publish',
                'tax_query' => array( array(                
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' =>$mc_key,
                    'operator' => 'IN'
                  )                
                )
              );
              $all_products = get_posts($args);
              if(!empty($all_products)){
                foreach($all_products as $postkey => $postvalue){
                  $t_data = array(
                    'w_product_id'=>$postvalue->ID,
                    'w_cat_id'=>$mc_key,
                    'g_cat_id'=>$mappedCat['id'],
                    'g_attribute_mapping'=> json_encode($ee_prod_mapped_attrs),
                    'update_date'=>date('Y-m-d')
                  );

                  $this->TVC_Admin_DB_Helper->tvc_add_row('ee_product_sync_data', $t_data);
                }
                wp_reset_postdata();
              }
            }
          }
        }
      }
    }

    public function update_last_sync_in_db(){
      $ee_prod_mapped_cats = unserialize(get_option('ee_prod_mapped_cats'));
      $ee_prod_mapped_attrs = unserialize(get_option('ee_prod_mapped_attrs'));  
      if($ee_prod_mapped_cats != "" && $ee_prod_mapped_attrs != "" &&!empty($ee_prod_mapped_cats)){
        global $wpdb;     
        foreach($ee_prod_mapped_cats as $mc_key => $mappedCat){
          $args= array(
            'post_type' => 'product',
            'numberposts' => -1,
            'post_status' => 'publish',
            'tax_query' => array( array(                
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' =>$mc_key,
                'operator' => 'IN'
              )                
            )
          );
          $all_products = get_posts($args);        
          $where ="w_cat_id = ".$mc_key;
          $p_c_ids = $this->TVC_Admin_DB_Helper->tvc_get_results_in_array('ee_product_sync_data', $where, array('w_product_id','w_cat_id'), true);
          if(!empty($all_products)){
            foreach($all_products as $postkey => $postvalue){
              $t_data = array(
                'w_product_id'=>$postvalue->ID,
                'w_cat_id'=>$mc_key,
                'g_cat_id'=>$mappedCat['id'],
                'g_attribute_mapping'=> json_encode($ee_prod_mapped_attrs),
                'update_date'=>date('Y-m-d')
              );
              //$table, $where, $field_name = "*"
              $p_c_id = $postvalue->ID."_".$mc_key;
              if(!in_array($p_c_id, $p_c_ids)){
                $this->TVC_Admin_DB_Helper->tvc_add_row('ee_product_sync_data', $t_data);
              }else{
                $this->TVC_Admin_DB_Helper->tvc_update_row('ee_product_sync_data', $t_data, array('w_product_id'=>$postvalue->ID, 'w_cat_id'=> $mc_key));
              }
            }
            wp_reset_postdata();
          }
        }          
      }    
    }

    public function tvc_get_post_meta($post_id){
      $where ="post_id = ".$post_id;
      $rows = $this->TVC_Admin_DB_Helper->tvc_get_results_in_array('postmeta', $where, array('meta_key','meta_value'));
      $metas = array();
      if(!empty($rows)){
        foreach($rows as $val){
          $metas[$val['meta_key']] = $val['meta_value'];
        }
      }
      return $metas;
    }
    
    public function tvc_get_map_product_attribute($products, $tvc_currency, $merchantId){
      if(!empty($products)){
        $items = [];
        $skipProducts = [];
        $product_ids = [];
        $batchId = time();
        foreach ($products as $postkey => $postvalue) {
          $product_ids[] = $postvalue->w_product_id;
          $postmeta = [];
          $postmeta = $this->tvc_get_post_meta($postvalue->w_product_id);
          $prd = wc_get_product($postvalue->w_product_id);
          $postObj = (object) array_merge((array) get_post($postvalue->w_product_id), (array) $postmeta);
          
          $product = array(
            'offer_id'=>$postvalue->w_product_id,
            'channel'=>'online',
            'link'=>get_permalink($postvalue->w_product_id),
            'google_product_category'=>$postvalue->g_cat_id
          );

          $temp_product=array();
          $fixed_att_select_list = array("gender", "age_group", "shipping", "tax", "content_language", "target_country", "condition");
          $formArray = json_decode($postvalue->g_attribute_mapping, true);
          foreach ($fixed_att_select_list as $fixed_key) {
            if(isset($formArray[$fixed_key]) && $formArray[$fixed_key] != "" ){
              if($fixed_key == "shipping" && $formArray[$fixed_key] != ""){
                $temp_product[$fixed_key]['price']['value'] = $formArray[$fixed_key];
                $temp_product[$fixed_key]['price']['currency'] = $tvc_currency;
                $temp_product[$fixed_key]['country'] = $formArray['target_country'];        
              }else if($fixed_key == "tax" && $formArray[$fixed_key] != ""){                
                $temp_product['taxes']['rate'] = $formArray[$fixed_key];
                $temp_product['taxes']['country'] = $formArray['target_country'];
              }else if( $formArray[$fixed_key] != ""){
                $temp_product[$fixed_key] = $formArray[$fixed_key];
              }          
            }
            unset($formArray[$fixed_key]);
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
            if($can_add_item_group_id == true){
              $p_variations = $prd->get_available_variations();                
              if(!empty($p_variations)){                  
                foreach ($p_variations as $v_key => $v_value) {
                  $postmeta_var = (object)$this->tvc_get_post_meta($v_value['variation_id']);
                  $formArray_val = $formArray['title'];
                  $product['title'] = (isset($postObj->$formArray_val))?$postObj->$formArray_val:get_the_title($postvalue->w_product_id);
                  $tvc_temp_desc_key = $formArray['description'];
                  $product['description'] = (isset($v_value['variation_description']) && $v_value['variation_description'] != "")?$v_value['variation_description']:$postObj->$tvc_temp_desc_key;
                  $product['offer_id'] = $v_value['variation_id'];
                  $product['id'] = $v_value['variation_id'];
                  $product['item_group_id'] = $postvalue->w_product_id;
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
                  $item = [
                    'merchant_id' => $merchantId,
                    'batch_id' => ++$batchId,
                    'method' => 'insert',
                    'product' => $product
                  ];
                  $items[] = $item;
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
            $item = [
              'merchant_id' => $merchantId,
              'batch_id' => ++$batchId,
              'method' => 'insert',
              'product' => $product
            ];            
            $items[] = $item;
          }
        }
        return array('items' => $items, 'skipProducts'=> $skipProducts, 'product_ids'=>$product_ids);        
      }
    }
    public function call_auto_sync_product($last_sync_product_id = array()){
      $product_count = $this->TVC_Admin_DB_Helper->tvc_row_count('ee_product_sync_data');
      //$count = 0;
      $pre_last_sync_product_id = $last_sync_product_id;
      if( $product_count > 0 ){  
        $tvc_currency =  $this->TVC_Admin_Helper->get_woo_currency(); 
        $merchantId = $this->TVC_Admin_Helper->get_merchantId();
        $customerId = $this->TVC_Admin_Helper->get_currentCustomerId();
        $accountId = $this->TVC_Admin_Helper->get_main_merchantId();
        $subscriptionId =  $this->TVC_Admin_Helper->get_subscriptionId();  
        $last_sync_product_id =( $last_sync_product_id > 0)?$last_sync_product_id:0;
        global $wpdb;
        $tablename = $wpdb->prefix .'ee_product_sync_data';
        $sql = "select * from ".$tablename." where id > ".$last_sync_product_id." LIMIT ".$this->batch_size;
        $products = $wpdb->get_results($sql, OBJECT); 
        $entries = [];       
        if(!empty($products)){
         $p_map_attribute = $this->tvc_get_map_product_attribute($products, $tvc_currency, $merchantId);
          if(!empty($p_map_attribute) && isset($p_map_attribute['items']) && !empty($p_map_attribute['items'])){
            // call product sync API
            $data = [
              'merchant_id' => $accountId,
              'account_id' => $merchantId,
              'subscription_id' => $subscriptionId,
              'entries' => $p_map_attribute['items']
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
            $sync_status = 0;
            if((isset($response_body->error) && $response_body->error == '')){
              $sync_status = 1;
            }
            // End call product sync API
            $sync_product_ids = (isset($p_map_attribute['product_ids']))?$p_map_attribute['product_ids']:""; 
            $last_sync_product_id =end($products)->id;
            $total_sync_product = 0;
            $action_scheduler_id ="";
            $last_sync = date( 'Y-m-d H:i:s', current_time( 'timestamp') );
            $next_sync = date( 'Y-m-d H:i:s', current_time( 'timestamp')+$this->time_space);
            if($pre_last_sync_product_id == 0){
              $last_sync_row = $this->TVC_Admin_DB_Helper->tvc_get_last_row('ee_product_sync_call');
              $total_sync_product = count($sync_product_ids);           
              if(!empty($last_sync_row)){
                $action_scheduler_id = $last_sync_row['id']+1;
                $last_sync = $last_sync_row['create_sync'];
                $next_sync = date( 'Y-m-d H:i:s', current_time( 'timestamp')+ $this->time_space);
              }else{
                $action_scheduler_id = 1;
              }
            }else{
              $last_sync_row = $this->TVC_Admin_DB_Helper->tvc_get_last_row('ee_product_sync_call');
                if(!empty($last_sync_row)){
                  $total_sync_product = count($sync_product_ids) + $last_sync_row['total_sync_product'];
                  $action_scheduler_id = $last_sync_row['action_scheduler_id'];
                  $next_sync = $last_sync_row['next_sync'];
                  $last_sync = $last_sync_row['last_sync'];
                }
            }
            $t_data = array(
              'sync_product_ids'=>json_encode($sync_product_ids),
              'w_total_product'=>$product_count,
              'total_sync_product'=>$total_sync_product,
              'last_sync'=>$last_sync,
              'create_sync'=>date( 'Y-m-d H:i:s', current_time( 'timestamp') ),
              'next_sync'=>$next_sync,
              'last_sync_product_id'=>$last_sync_product_id,
              'action_scheduler_id'=> $action_scheduler_id,
              'status'=>$sync_status
            );
            $this->TVC_Admin_DB_Helper->tvc_add_row('ee_product_sync_call', $t_data);
            as_enqueue_async_action('ee_auto_product_sync_check', array('last_sync_product_id' => $last_sync_product_id));         
          }          
        }
      }
    }
    public function add_schedule_event(){
      $row_count = $this->TVC_Admin_DB_Helper->tvc_row_count('ee_product_sync_data'); 
      if($row_count >0){
        if ( function_exists( 'as_next_scheduled_action' ) && false === as_next_scheduled_action( 'ee_auto_product_sync_check' ) ) {
          //strtotime( 'midnight tonight' )
          as_schedule_recurring_action( strtotime( "+2 minutes" ), $this->time_space, 'ee_auto_product_sync_check',array("last_sync_product_id"=>0),"product_sync");
        }
      }
    }

    /*protected function maybe_remove_cronjobs() {
      if ( function_exists( 'as_next_scheduled_action' ) && as_next_scheduled_action( 'ee_auto_product_sync_check' ) ) {
        as_unschedule_all_actions( 'ee_auto_product_sync_check' );
      }
      if ( function_exists( 'as_next_scheduled_action' ) && as_next_scheduled_action( 'ee_auto_product_sync_recheck' ) ) {
        as_unschedule_all_actions( 'ee_auto_product_sync_recheck' );
      }
    }*/

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
    public function get_tvc_access_token(){
      if(!empty($this->access_token)){
        return $this->access_token;
      }else   if(isset($_SESSION['access_token']) && $_SESSION['access_token']){
        $this->access_token = $_SESSION['access_token'];
        return $this->access_token;
      }else{
        $google_detail = $this->TVC_Admin_Helper->get_ee_options_data();          
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
        $google_detail = $this->TVC_Admin_Helper->get_ee_options_data();          
        $this->refresh_token = $google_detail['setting']->refresh_token;
        return $this->refresh_token;
      }
    }
    /*      
    function tvc_add_cron_interval( $schedules ) { 
      $schedules['five_seconds'] = array(
        'interval' => 5,
        'display'  => esc_html__( 'Every Five Seconds' ) );
      return $schedules;
    }
    */      
  }// end Class
}
new TVC_Admin_Auto_Product_sync_Helper();
?>