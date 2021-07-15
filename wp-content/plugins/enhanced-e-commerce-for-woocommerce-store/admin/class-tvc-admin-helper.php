<?php
Class TVC_Admin_Helper{
	protected $customApiObj;
	protected $ee_options_data = "";
	protected $e_options_settings = "";
	protected $merchantId = "";
	protected $main_merchantId = "";
	protected $subscriptionId = "";
	protected $time_zone = "";
	protected $connect_actual_link = "";
	protected $connect_url = "";
	protected $woo_country = "";
	protected $woo_currency = "";
	protected $currentCustomerId = "";
	protected $user_currency_symbol = "";
	protected $setting_status = "";
	protected $ee_additional_data = "";
	protected $TVC_Admin_DB_Helper;
	public function __construct() {
    $this->includes();
    $this->customApiObj = new CustomApi();
    $this->TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
  }
  
  public function includes() {
    if (!class_exists('CustomApi.php')) {
      require_once(ENHANCAD_PLUGIN_DIR . 'includes/setup/CustomApi.php');
    }
    if (!class_exists('ShoppingApi')) {
      require_once(ENHANCAD_PLUGIN_DIR . 'includes/setup/ShoppingApi.php');
    }    
  }
  /*
   * verstion auto updated
   */
  public function need_auto_update_db(){
  	$old_ee_auto_update_id ="tvc_3.0.4";
  	$new_ee_auto_update_id ="tvc_4.0.0";
  	$ee_auto_update_id = get_option('ee_auto_update_id');
  	if($ee_auto_update_id!=""){
  		if( $ee_auto_update_id != $new_ee_auto_update_id){
  			global $wpdb;
  			$tablename = $wpdb->prefix ."ee_products_sync_list";
  			$wpdb->query("DROP TABLE IF EXISTS ".$tablename);
  			$tablename = $wpdb->prefix ."ee_product_sync_data";
  			$this->TVC_Admin_DB_Helper->tvc_safe_truncate_table($tablename);
  			$tablename = $wpdb->prefix ."ee_product_sync_call";
  			$this->TVC_Admin_DB_Helper->tvc_safe_truncate_table($tablename);
  			new TVC_Admin_Auto_Product_sync_Helper();
  			update_option("ee_auto_update_id", $new_ee_auto_update_id);
  		}
  	}else{
  		update_option("ee_auto_update_id", $old_ee_auto_update_id);
  	}
  }
  /*
   * Check auto update time
   */
  public function is_need_to_update_api_to_db(){
  	if($this->get_subscriptionId() != ""){
  		$google_detail = $this->get_ee_options_data();
  		if(isset($google_detail['sync_time']) && $google_detail['sync_time']){
  			$current = current_time( 'timestamp' );
  			//echo date( 'M-d-Y H:i', current_time( 'timestamp' ))."==>".date( 'M-d-Y H:i', $google_detail['sync_time']);
  			$diffrent_hours = floor(( $current - $google_detail['sync_time'])/(60*60));
  			if($diffrent_hours > 11){
  				return true;
  			}
  		}else if(empty($google_detail)){
  			return true;
  		}
  	}
  	return false;
  }
  /*
   * if user has subscription id  and if DB data is empty then call update data
   */
  public function is_ee_options_data_empty(){
  	if($this->get_subscriptionId() != ""){
  		if(empty($this->get_ee_options_data())){
  			$this->set_update_api_to_db();
  		}
  	}
  }
  
	/*
   * Update user only subscription details in DB
   */
	public function update_subscription_details_api_to_db($googleDetail = null){
		if(empty($googleDetail)){			
  		$google_detail = $this->customApiObj->getGoogleAnalyticDetail();
  		if(property_exists($google_detail,"error") && $google_detail->error == false){
  			if(property_exists($google_detail,"data") && $google_detail->data != ""){
	        $googleDetail = $google_detail->data;
	      }
  		}
		}
		if(!empty($googleDetail)){
			$get_ee_options_data = $this->get_ee_options_data();
			$get_ee_options_data["setting"] = $googleDetail;
			$this->set_ee_options_data($get_ee_options_data);
		}
	}
	/*
   * Update user subscription and shopping details in DB
   */
	public function set_update_api_to_db($googleDetail = null, $is_import_gmc_products = true){
		if(empty($googleDetail)){			
  		$google_detail = $this->customApiObj->getGoogleAnalyticDetail();
  		if(property_exists($google_detail,"error") && $google_detail->error == false){
  			if(property_exists($google_detail,"data") && $google_detail->data != "") {
	        $googleDetail = $google_detail->data;
	      }
  		}else{
  			return array("error"=>true, "message"=>"Please try after some time.");
  		}
		}
		$syncProductStat = [];
		$syncProductList = [];
		$campaigns_list = [];
		if(isset($googleDetail->google_merchant_center_id) &&  $googleDetail->google_merchant_center_id != ""){
			/*$syncProduct_list_res = $this->customApiObj->getSyncProductList(['merchant_id' => $this->get_merchantId()]);
			if(isset($syncProduct_list_res->data) && isset($syncProduct_list_res->status) && $syncProduct_list_res->status == 200){
			  if (isset($syncProduct_list_res->data->statistics)) {
			    $syncProductStat = $syncProduct_list_res->data->statistics;
			  }
			  if (isset($syncProduct_list_res->data->products)) {
					$syncProductList = $syncProduct_list_res->data->products;
				}
			}*/
			if($is_import_gmc_products){
				$this->import_gmc_products_sync_in_db();
			}			
			$product_status = $this->TVC_Admin_DB_Helper->tvc_get_counts_groupby('ee_products_sync_list','google_status');
			$syncProductStat = array("approved" => 0, "disapproved" => 0, "pending" => 0 );
			foreach ($product_status as $key => $value) {
				if(isset($value['google_status']) ){
					$syncProductStat[$value['google_status']] = (isset($value['count']) && $value['count'] >0)?$value['count']:0;
				}
			}
			$syncProductStat["total"] = $this->TVC_Admin_DB_Helper->tvc_row_count('ee_products_sync_list');
		}else{
		$syncProductStat = array("total" =>0, "approved" => 0, "disapproved" => 0, "pending" => 0 );
		}
		if(isset($googleDetail->google_ads_id) && $googleDetail->google_ads_id != ""){ 
			$shopping_api = new ShoppingApi();			
			$campaigns_list_res = $shopping_api->getCampaigns();
			if(isset($campaigns_list_res->data) && isset($campaigns_list_res->status) && $campaigns_list_res->status == 200) {
			  if (isset($campaigns_list_res->data['data'])) {
			    $campaigns_list = $campaigns_list_res->data['data'];
			  }
			}
		}
		
		$this->set_ee_options_data(array("setting" => $googleDetail, "prod_sync_status" => (object) $syncProductStat, "campaigns_list"=>$campaigns_list, "sync_time"=>current_time( 'timestamp' )));
		return array("error"=>false, "message"=>"Details updated successfully.");
	}
	/*
   * update remarketing snippets
   */
	public function update_remarketing_snippets(){
		$customer_id = $this->get_currentCustomerId();
		if($customer_id != ""){
			$rs = $this->customApiObj->get_remarketing_snippets($customer_id);
			$remarketing_snippets=array();
			if(property_exists($rs,"error") && $rs->error == false){
				if(property_exists($rs,"data") && $rs->data != "") {
					$remarketing_snippets["snippets"]=base64_encode($rs->data->snippets);
					$remarketing_snippets["id"]=$rs->data->id;
	      }
			}
			update_option("ee_remarketing_snippets", serialize($remarketing_snippets));
		}
	}
	/*
   * import GMC products in DB
   */
	public function import_gmc_products_sync_in_db($next_page_token = null){
    $merchant_id = $this->get_merchantId();
    $last_row = $this->TVC_Admin_DB_Helper->tvc_get_last_row('ee_products_sync_list',array("gmc_id"));
    if(!empty($last_row) && isset($last_row['gmc_id']) && $last_row['gmc_id'] != $merchant_id){
    	global $wpdb;
  		$tablename = $wpdb->prefix ."ee_products_sync_list";
  		$wpdb->query("DROP TABLE IF EXISTS ".$tablename);
  		$tablename = $wpdb->prefix ."ee_product_sync_data";
  		$this->TVC_Admin_DB_Helper->tvc_safe_truncate_table($tablename);
  		$tablename = $wpdb->prefix ."ee_product_sync_call";
  		$this->TVC_Admin_DB_Helper->tvc_safe_truncate_table($tablename);
  		new TVC_Admin_Auto_Product_sync_Helper();
    }
    if( $merchant_id != "" ){
    	$args = array( 'merchant_id' => $merchant_id );
    	if($next_page_token != ""){
    		$args["pageToken"] = $next_page_token;
    	}
    	$syncProduct_list_res = $this->customApiObj->getSyncProductList($args);
    	if(isset($syncProduct_list_res->data) && isset($syncProduct_list_res->status) && $syncProduct_list_res->status == 200){
    		if (isset($syncProduct_list_res->data->products)) {
    			$rs_next_page_token = $syncProduct_list_res->data->nextPageToken;
					$sync_product_list = $syncProduct_list_res->data->products;
					if(!empty($sync_product_list)){
						foreach ($sync_product_list as $key => $value) {
							$googleStatus =$value->googleStatus;
							if ($value->googleStatus != "disapproved" && $value->googleStatus != "approved") {
                $googleStatus = "pending";
              } 
							$t_data = array(
								'gmc_id' => $merchant_id,
                'name'=>$value->name,
                'product_id'=>$value->productId,
                'google_status'=>$googleStatus,
                'image_link'=> $value->imageLink,
                'issues'=>json_encode($value->issues)
              );
              $where ="product_id = '".$value->productId."'";
              $row_count = $this->TVC_Admin_DB_Helper->tvc_check_row('ee_products_sync_list', $where);
              if($row_count == 0){
              	$this->TVC_Admin_DB_Helper->tvc_add_row('ee_products_sync_list', $t_data);
              }
						}
					}
					if($rs_next_page_token!=""){
						$this->import_gmc_products_sync_in_db($rs_next_page_token);
					}
				}
    	}
    }
  }
 	/*
   * get API data from DB
   */
	public function get_ee_options_data(){
		if(!empty($this->ee_options_data)){
			return $this->ee_options_data;
		}else{
			$this->ee_options_data = unserialize(get_option('ee_api_data'));
			return $this->ee_options_data;
		}
	} 
	/*
   * set API data in DB
   */
	public function set_ee_options_data($ee_options_data){
		update_option("ee_api_data", serialize($ee_options_data));
	}
	/*
   * set additional data in DB
   */
	public function set_ee_additional_data($ee_additional_data){
		update_option("ee_additional_data", serialize($ee_additional_data));
	}
	/*
   * get additional data from DB
   */
	public function get_ee_additional_data(){
		if(!empty($this->ee_additional_data)){
			return $this->ee_additional_data;
		}else{
			$this->ee_additional_data = unserialize(get_option('ee_additional_data'));
			return $this->ee_additional_data;
		}
	}
	/*
   * get plugin setting data from DB
   */
	public function get_ee_options_settings(){
		if(!empty($this->e_options_settings)){
			return $this->e_options_settings;
		}else{
			$this->e_options_settings = unserialize(get_option('ee_options'));
			return $this->e_options_settings;
		}
	}
	/*
   * get subscriptionId
   */
	public function get_subscriptionId(){
		if(!empty($this->subscriptionId)){
			return $this->subscriptionId;
		}else{
			$ee_options_settings = "";
			if(!isset($GLOBALS['tatvicData']['tvc_subscription'])){
				$ee_options_settings = $this->get_ee_options_settings();
			}
			$this->subscriptionId = (isset($GLOBALS['tatvicData']['tvc_subscription'])) ? $GLOBALS['tatvicData']['tvc_subscription'] : ((isset($ee_options_settings['subscription_id']))?$ee_options_settings['subscription_id']:"");
			return $this->subscriptionId;
		}		
	}
	/*
   * get merchantId
   */
	public function get_merchantId(){
		if(!empty($this->merchantId)){
			return $this->merchantId;
		}else{
			$tvc_merchant = "";
			$google_detail = $this->get_ee_options_data();
			if(!isset($GLOBALS['tatvicData']['tvc_merchant']) && isset($google_detail['setting']->google_merchant_center_id)){
				$tvc_merchant = $google_detail['setting']->google_merchant_center_id;
			}
			$this->merchantId = (isset($GLOBALS['tatvicData']['tvc_merchant'])) ? $GLOBALS['tatvicData']['tvc_merchant'] : $tvc_merchant;
			return $this->merchantId;
		}
	}
	/*
   * get main_merchantId
   */
	public function get_main_merchantId(){
		if(!empty($this->main_merchantId)){
			return $this->main_merchantId;
		}else{
			$main_merchantId = "";
			$google_detail = $this->get_ee_options_data();			
			if(!isset($GLOBALS['tatvicData']['tvc_main_merchant_id']) && isset($google_detail['setting']->merchant_id)){
				$main_merchantId = $google_detail['setting']->merchant_id;
			}
			$this->main_merchantId = (isset($GLOBALS['tatvicData']['tvc_main_merchant_id'])) ? $GLOBALS['tatvicData']['tvc_main_merchant_id'] : $main_merchantId;
			return $this->main_merchantId;
		}		
	}
	/*
   * get admin time zone
   */
	public function get_time_zone(){
		if(!empty($this->time_zone)){
			return $this->time_zone;
		}else{
			$timezone = get_option('timezone_string');
			if($timezone == ""){
	      $timezone = "America/New_York"; 
	    }
			$this->time_zone = $timezone;
			return $this->time_zone;
		}
	}

	public function get_connect_actual_link(){
		if(!empty($this->connect_actual_link)){
			return $this->connect_actual_link;
		}else{
			$this->connect_actual_link = get_site_url();
			return $this->connect_actual_link;
		}
	}

	public function get_connect_url(){
		if(!empty($this->connect_url)){
			return $this->connect_url;
		}else{
			$this->connect_url = "https://".TVC_AUTH_CONNECT_URL."/config/ga_rdr_gmc.php?return_url=".TVC_AUTH_CONNECT_URL."/config/ads-analytics-form.php?domain=" . $this->get_connect_actual_link() . "&amp;country=" . $this->get_woo_country(). "&amp;user_currency=".$this->get_woo_currency()."&amp;subscription_id=" . $this->get_subscriptionId() . "&amp;confirm_url=" . admin_url() . "&amp;timezone=".$this->get_time_zone();
			return $this->connect_url;
		}
	}

	public function get_woo_currency(){
		if(!empty($this->woo_currency)){
			return $this->woo_currency;
		}else{			
	    $this->woo_currency = get_option('woocommerce_currency');
	        return $this->woo_currency;
	  }
	}

	public function get_woo_country(){
		if(!empty($this->woo_country)){
			return $this->woo_country;
		}else{
			$store_raw_country = get_option('woocommerce_default_country');
			$country = explode(":", $store_raw_country);
	    $this->woo_country = (isset($country[0]))?$country[0]:"";
	    return $this->woo_country;
	  }
	}
	
	public function get_api_customer_id(){
		$google_detail = $this->get_ee_options_data();
		if(isset($google_detail['setting'])){
      $googleDetail = (array) $google_detail['setting'];
			return ((isset($googleDetail['customer_id']))?$googleDetail['customer_id']:"");
		}
	}
	//tvc_customer = >google_ads_id
	public function get_currentCustomerId(){
		if(!empty($this->currentCustomerId)){
			return $this->currentCustomerId;
		}else{
			$ee_options_settings = "";
			if(!isset($GLOBALS['tatvicData']['tvc_customer'])){
				$ee_options_settings = $this->get_ee_options_settings();
			}
			$this->currentCustomerId = (isset($GLOBALS['tatvicData']['tvc_customer'])) ? $GLOBALS['tatvicData']['tvc_customer'] : ((isset($ee_options_settings['google_ads_id']))?$ee_options_settings['google_ads_id']:"");
			return $this->currentCustomerId;
		}
	}
	public function get_user_currency_symbol(){
		if(!empty($this->user_currency_symbol)){
			return $this->user_currency_symbol;
		}else{
			$currency_symbol="";
			$currency_symbol_rs = $this->customApiObj->getCampaignCurrencySymbol(['customer_id' => $this->get_currentCustomerId()]);
      if(isset($currency_symbol_rs->data) && isset($currency_symbol_rs->data['status']) && $currency_symbol_rs->data['status'] == 200){	         
      	$currency_symbol = get_woocommerce_currency_symbol($currency_symbol_rs->data['data']->currency);	            
      }else{
        $currency_symbol = get_woocommerce_currency_symbol("USD");
      }
			$this->user_currency_symbol = $currency_symbol;
			return $this->user_currency_symbol;
		}
	}
	
	public function add_spinner_html(){
		$spinner_gif = ENHANCAD_PLUGIN_URL . '/admin/images/ajax-loader.gif';		
    echo '<div class="feed-spinner" id="feed-spinner" style="display:none;">
				<img id="img-spinner" src="' . $spinner_gif . '" alt="Loading" />
			</div>';	
	}

	public function get_gmcAttributes() {
    $path = ENHANCAD_PLUGIN_URL . '/includes/setup/json/gmc_attrbutes.json';
    $str = file_get_contents($path);
    $attributes = $str ? json_decode($str, true) : [];
    return $attributes;
  }
  public function get_gmc_countries_list() {
    $path = ENHANCAD_PLUGIN_URL . '/includes/setup/json/countries.json';
    $str = file_get_contents($path);
    $attributes = $str ? json_decode($str, true) : [];
    return $attributes;
  }
  public function get_gmc_language_list() {
    $path = ENHANCAD_PLUGIN_URL . '/includes/setup/json/iso_lang.json';
    $str = file_get_contents($path);
    $attributes = $str ? json_decode($str, true) : [];
    return $attributes;
  }
  /* start display form input*/
  public function tvc_language_select($name, $class_id="", string $label="Please Select", string $sel_val = "en", bool $require = false){
  	if($name){
  		$countries_list = $this->get_gmc_language_list();
	  	?>
	  	<select class="form-control select2 <?php echo $class_id; ?> <?php echo ($require == true)?"field-required":""; ?>" name="<?php echo $name; ?>" id="<?php echo $class_id; ?>" >
	  		<option value=""><?php echo $label; ?></option>
	  		<?php foreach ($countries_list as $Key => $val) {?>
	  			<option value="<?php echo $val["code"];?>" <?php echo($val["code"] == $sel_val)?"selected":""; ?>><?php echo $val["name"]." (".$val["native_name"].")";?></option>
	  		<?php
	  		}?>
	  	</select>
	  	<?php
  	}
  }
  public function tvc_countries_select($name, $class_id="", string $label="Please Select", bool $require = false){
  	if($name){
  		$countries_list = $this->get_gmc_countries_list();
  		$sel_val = $this->get_woo_country();
	  	?>
	  	<select class="form-control select2 <?php echo $class_id; ?> <?php echo ($require == true)?"field-required":""; ?>" name="<?php echo $name; ?>" id="<?php echo $class_id; ?>" >
	  		<option value=""><?php echo $label; ?></option>
	  		<?php foreach ($countries_list as $Key => $val) {?>
	  			<option value="<?php echo $val["code"];?>" <?php echo($val["code"] == $sel_val)?"selected":""; ?>><?php echo $val["name"];?></option>
	  		<?php
	  		}?>
	  	</select>
	  	<?php
  	}
  }
  public function tvc_select($name, $class_id="", string $label="Please Select", string $sel_val = null, bool $require = false, $option_list = array()){
  	if(!empty($option_list) && $name){
	  	?>
	  	<select class="form-control select2 <?php echo $class_id; ?> <?php echo ($require == true)?"field-required":""; ?>" name="<?php echo $name; ?>" id="<?php echo $class_id; ?>" >
	  		<option value=""><?php echo $label; ?></option>
	  		<?php foreach ($option_list as $Key => $val) {?>
	  			<option value="<?php echo $val["field"];?>" <?php echo($val["field"] == $sel_val)?"selected":""; ?>><?php echo $val["field"];?></option>
	  		<?php
	  		}?>
	  	</select>
	  	<?php
  	}
  }
  public function tvc_text($name, string $type="text", string $class_id="", string $label=null, $sel_val = null, bool $require = false){
  	?>
  	<input type="<?php echo $type; ?>" name="<?php echo $name; ?>" class="tvc-text <?php echo $class_id; ?>" id="<?php echo $class_id; ?>" placeholder="<?php echo $label; ?>" value="<?php echo $sel_val; ?>">
  	<?php
  }
 
  /* end from input*/
  public function check_setting_status(){        
   if(!empty($this->setting_status)){
      return $this->setting_status;
    }else{
      $google_detail = $this->get_ee_options_data();
      $setting_status = array();
      if(isset($google_detail['setting'])){
        $googleDetail = $google_detail['setting'];               
        //for google analytic            
        if(isset($googleDetail->tracking_option) && isset($googleDetail->measurement_id) && isset($googleDetail->property_id) && $googleDetail->tracking_option == "BOTH" ){
            if($googleDetail->property_id != "" && $googleDetail->measurement_id != ""){
                $setting_status['google_analytic']= true;
                $setting_status['google_analytic_msg']= "";
            }else if($googleDetail->property_id == "" ){
                $setting_status['google_analytic']= false;
                $setting_status['google_analytic_msg']= "There is a configuration issue in your Google Analytics account set up <a target='_blank' href='".esc_url($this->get_connect_url())."'>click here</a>.";
            }else if($googleDetail->measurement_id == "" ){
                $setting_status['google_analytic']= false;
                $setting_status['google_analytic_msg']= "There is a configuration issue in your Google Analytics account set up <a target='_blank' href='".esc_url($this->get_connect_url())."'>click here</a>.";
            }
        }else if(isset($googleDetail->tracking_option) && isset($googleDetail->measurement_id) && $googleDetail->tracking_option == "GA4"){
            if( $googleDetail->measurement_id != ""){
                $setting_status['google_analytic']= true;
                $setting_status['google_analytic_msg']= "";
            }else{
                $setting_status['google_analytic']= false;
                $setting_status['google_analytic_msg']= "There is a configuration issue in your Google Analytics account set up <a target='_blank' href='".esc_url($this->get_connect_url())."'>click here</a>.";
            }
        }else if(isset($googleDetail->tracking_option) && isset($googleDetail->property_id) && $googleDetail->tracking_option == "UA" ){
            if($googleDetail->property_id != ""){
                $setting_status['google_analytic']= true;
                $setting_status['google_analytic_msg']= "";
            }else{
                $setting_status['google_analytic']= false;
                $setting_status['google_analytic_msg']= "There is a configuration issue in your Google Analytics account set up <a target='_blank' href='".esc_url($this->get_connect_url())."'>click here</a>.";
            }
        }else{
            $setting_status['google_analytic']= false;
            $setting_status['google_analytic_msg']= "";
        }
        // for google shopping
        if(property_exists($googleDetail,"google_merchant_center_id") && property_exists($googleDetail,"google_ads_id") ){
          //main tab
          if( $googleDetail->google_merchant_center_id != "" && $googleDetail->google_ads_id != ""){
              $setting_status['google_shopping']= true;
              $setting_status['google_shopping_msg']= "";
          }else if($googleDetail->google_merchant_center_id == ""){
              $setting_status['google_shopping']= false;
              $setting_status['google_shopping_msg']= "Connect your merchant center account and make your products available to shoppers across Google <a target='_blank' href='".esc_url($this->get_connect_url())."'>click here</a>.";
          }else if($googleDetail->google_ads_id == ""){
              $setting_status['google_shopping']= false;
              $setting_status['google_shopping_msg']= "Link your Google Ads with Merchant center to start running shopping campaigns <a target='_blank' href='".esc_url($this->get_connect_url())."'>click here</a>.";
          }
        }else{
            $setting_status['google_shopping']= false;
            $setting_status['google_shopping_msg']= "";
        }
          
        //google_ads_id
        if(property_exists($googleDetail,"google_ads_id") && property_exists($googleDetail,"google_merchant_center_id") ){
          if( $googleDetail->google_ads_id != "" && $googleDetail->google_merchant_center_id != ""){
            $setting_status['google_ads']= true;
            $setting_status['google_ads_msg']= "";
          }else if($googleDetail->google_merchant_center_id == ""){
            $setting_status['google_ads']= false;
            $setting_status['google_ads_msg']= "Link your Google Ads with Merchant center to start running shopping campaigns <a target='_blank' href='".esc_url($this->get_connect_url())."'>click here</a>.";
          }else if($googleDetail->google_ads_id == ""){
            $setting_status['google_ads']= false;
            $setting_status['google_ads_msg']= "Configure Google Ads account to reach to millions of interested shoppers <a target='_blank' href='".esc_url($this->get_connect_url())."'>click here</a>.";
          }              
        }else{
          $setting_status['google_ads']= false;
          $setting_status['google_ads_msg']= "";
        }
      }
      $this->setting_status = $setting_status;            
      return $setting_status;
    }
  }
  public function check_setting_status_sub_tabs(){        
    $google_detail = $this->get_ee_options_data();
    $setting_status = array();
    if(isset($google_detail['setting'])){
      $googleDetail = $google_detail['setting'];            
      //sub tab shopping config
      if(property_exists($googleDetail,"google_merchant_center_id") && property_exists($googleDetail,"is_site_verified") && property_exists($googleDetail,"is_domain_claim") && property_exists($googleDetail,"google_ads_id")){
        if( $googleDetail->google_merchant_center_id != "" && $googleDetail->google_ads_id != "" && $googleDetail->is_site_verified == 1 && $googleDetail->is_domain_claim == 1 ){
            $setting_status['google_shopping_conf']= true;
            $setting_status['google_shopping_conf_msg']= "Google Shopping Configuration Success.";
        }else if($googleDetail->google_merchant_center_id == "" || $googleDetail->google_ads_id == "" ){
            $setting_status['google_shopping_conf']= false;
            $setting_status['google_shopping_conf_msg']= "Connect your merchant center account and make your products available to shoppers across Google <a target='_blank' href='".esc_url($this->get_connect_url())."'>click here</a>.";
        }else if($googleDetail->is_site_verified ==0 && $googleDetail->is_domain_claim ==0 ){
            $setting_status['google_shopping_conf']= false;
            $setting_status['google_shopping_conf_msg']= "Site verification and domain claim for your merchant center account failed.";
        }else if($googleDetail->is_site_verified ==0 ){
             $setting_status['google_shopping_conf']= false;
             $setting_status['google_shopping_conf_msg']= "Site verification and domain claim for your merchant center account failed.";
        }else if($googleDetail->is_domain_claim ==0 ){
            $setting_status['google_shopping_conf']= false;
            $setting_status['google_shopping_conf_msg']= "Domain claim is pending. Your store url may be linked to other merchant center account.";
        }                                      
      }else{
          $setting_status['google_shopping_conf']= false;
          $missing="";
      }
      //sub tab product sync
      $syncProductList = [];
      $syncProductStat = [];
      if(property_exists($googleDetail,"google_merchant_center_id") && $googleDetail->google_merchant_center_id != ''){
        if(isset($google_detail['prod_sync_status']) && $google_detail['prod_sync_status']){                      
          $syncProductStat = $google_detail['prod_sync_status'];
          $sync_product_total = (!empty($syncProductStat)) ? $syncProductStat->total : "0";
          $sync_product_approved = (!empty($syncProductStat)) ? $syncProductStat->approved : "0";
          $sync_product_disapproved = (!empty($syncProductStat)) ? $syncProductStat->disapproved : "0";
          $sync_product_pending = (!empty($syncProductStat)) ? $syncProductStat->pending : "0";

          if($sync_product_total > 1 && $sync_product_approved > 1 && $sync_product_disapproved < 1){
              $setting_status['google_shopping_p_sync']= true;
              $setting_status['google_shopping_p_sync_msg']= "Google Shopping product sync is a success.";
          }else if($sync_product_total < 1){
              $setting_status['google_shopping_p_sync']= false;
              $setting_status['google_shopping_p_sync_msg']= "Sync your product data into Merchant center and get eligible for free listing across Google.";
          }else if($sync_product_disapproved > 0){
              $setting_status['google_shopping_p_sync']= false;
              $setting_status['google_shopping_p_sync_msg']= "There seems to be some problem with your product data. Rectify the issues by selecting right attributes.";
          }                  
        }                
      }else{
          $setting_status['google_shopping_p_sync']= false;
          $setting_status['google_shopping_p_sync_msg']= "Connect your merchant center account and make your products available to shoppers across Google <a target='_blank' href='".esc_url($this->get_connect_url())."'>click here</a>.";
      } 

      //sub tab product Campaigns
      if(property_exists($googleDetail,"google_merchant_center_id") && $googleDetail->google_merchant_center_id != ''){                
         if(isset($google_detail['campaigns_list']) && $google_detail['campaigns_list']){
            $campaigns_list = $google_detail['campaigns_list'];
            $totalCampaigns = count($campaigns_list);
            if($totalCampaigns < 1){
                $setting_status['google_shopping_p_campaigns']= false;
                $setting_status['google_shopping_p_campaigns_msg']= "Reach out to customers based on their past site behavior by running start shopping campaign.";
            }else{
                $setting_status['google_shopping_p_campaigns']= true;
            }                    
          }else{
              $setting_status['google_shopping_p_campaigns']= false;
              $setting_status['google_shopping_p_campaigns_msg']= "Reach out to customers based on their past site behavior by running start shopping campaign.";
          }                
      }else{
          $setting_status['google_shopping_p_campaigns']= false;
          $setting_status['google_shopping_p_campaigns_msg']= "Connect your merchant center account and make your products available to shoppers across Google <a target='_blank' href='".esc_url($this->get_connect_url())."'>click here</a>.";
      }          
    }                  
    return $setting_status;        
	}

	public function is_current_tab_in($tabs){
		if(isset($_GET['tab']) && is_array($tabs) && in_array($_GET['tab'], $tabs)){
			return true;
		}else if(isset($_GET['tab']) && $_GET['tab'] ==$tabs){
			return true;
		}
		return false;
	}

	public function get_tvc_product_cat_list(){
		$args = array(
	    'hide_empty'   => 1,
	    'taxonomy' => 'product_cat',
	    'orderby'  => 'term_id'
    );
    $shop_categories_list = get_categories( $args );
    $tvc_cat_id_list = [];
    foreach ($shop_categories_list as $key => $value) {
		  $tvc_cat_id_list[]=$value->term_id;
		}
		return json_encode($tvc_cat_id_list);		
	}
	public function get_tvc_product_cat_list_with_name(){
		$args = array(
	    'hide_empty'   => 1,
	    'taxonomy' => 'product_cat',
	    'orderby'  => 'term_id'
    );
    $shop_categories_list = get_categories( $args );
    $tvc_cat_id_list = [];
    foreach ($shop_categories_list as $key => $value) {
		  $tvc_cat_id_list[$value->term_id]=$value->name;
		}
		return $tvc_cat_id_list;		
	}

	public function call_tvc_site_verified_and_domain_claim(){   
    $google_detail = $this->get_ee_options_data();
    $class = 'notice notice-error tvc-notice-error';
    if(!isset($_GET['welcome_msg']) && isset($google_detail['setting']) && $google_detail['setting'] ){    
      $googleDetail = $google_detail['setting'];
      if(isset($googleDetail->google_merchant_center_id) && $googleDetail->google_merchant_center_id){
	      $message = "";
	      $call_js_function_args="";
	      if (isset($googleDetail->is_site_verified) && isset($googleDetail->is_domain_claim) && $googleDetail->is_site_verified == '0' && $googleDetail->is_domain_claim == '0') {
	        $message = esc_html__('Site verification and domain claim for merchant center account failed. Without a verified and claimed website, your products will get disapproved.');
	        $call_js_function_args = "both";
	      }else if(isset($googleDetail->is_site_verified) && $googleDetail->is_site_verified == '0'){
	        $message = esc_html__('Site verification and domain claim for merchant center account failed. Without a verified and claimed website, your products will get disapproved.');
	        $call_js_function_args = "site_verified";
	      }else if(isset($googleDetail->is_domain_claim) && $googleDetail->is_domain_claim == '0'){
	        $message = esc_html__('Domain claim for merchant center account failed. Without a verified and claimed website, your products will get disapproved.'); 
	        $call_js_function_args = "domain_claim";       
	      }
	      if($message!= ""){
	      	printf('<div class="%1$s"><p><b>%2$s Click <a href="javascript:void(0)" id="call_both_verification" onclick="call_tvc_site_verified_and_domain_claim(\'%3$s\');">here</a></b> to verify and claim the domain.</p></div>', esc_attr($class), esc_html($message),$call_js_function_args);
	      	?>
	      	<script>
	      		function call_tvc_site_verified_and_domain_claim(call_args){
	      			var tvs_this = event.target;
	      			$("#call_both_verification").css("visibility","hidden");
	      			$(tvs_this).after('<div class="call_both_verification-spinner tvc-nb-spinner" id="both_verification-spinner"></div>');
	      			if(call_args == "domain_claim"){
	      				call_domain_claim_both();
	      			}else{
		      			jQuery.post(myAjaxNonces.ajaxurl,{
						      action: "tvc_call_site_verified",
						      apiDomainClaimNonce: myAjaxNonces.SiteVerifiedNonce
						    },function( response ){
						      var rsp = JSON.parse(response);    
						      if(rsp.status == "success"){ 
						      	if(call_args == "site_verified"){
						      		tvc_helper.tvc_alert("success","",rsp.message);
						        	location.reload();
						      	}else{
						      		call_domain_claim_both(rsp.message);
						      	}				        
						      }else{
						        tvc_helper.tvc_alert("error","",rsp.message);
						        $("#both_verification-spinner").remove();
						      }
						    });
		      		}
	      		}
	      		function call_domain_claim_both(first_message=null){
	      			//console.log("call_domain_claim");				    
					    jQuery.post(myAjaxNonces.ajaxurl,{
					      action: "tvc_call_domain_claim",
					      apiDomainClaimNonce: myAjaxNonces.apiDomainClaimNonce
					    },function( response ){
					      var rsp = JSON.parse(response);    
					      if(rsp.status == "success"){
					      	if(first_message != "" || first_message == null){
					      		tvc_helper.tvc_alert("success","",first_message,true,4000);
						      	setTimeout(function(){
						        	tvc_helper.tvc_alert("success","",rsp.message,true,4000); 
						        	location.reload();
						        }, 4000);
						      }else{
						      	tvc_helper.tvc_alert("success","",rsp.message,true,4000);
						      	setTimeout(function(){						        	 
						        	location.reload();
						        }, 4000);
						      }				        
					      }else{
					        tvc_helper.tvc_alert("error","",rsp.message,true)
					      }
					      $("#both_verification-spinner").remove();
					    });
					  }  
	      	</script>
	      	<?php
	      }
	    }
    }
	}
	public function call_domain_claim(){
		$googleDetail = [];
    $google_detail = $this->get_ee_options_data();
    if(isset($google_detail['setting']) && $google_detail['setting']){      
      $googleDetail = $google_detail['setting'];
      if($googleDetail->is_site_verified == '0'){
      	return array('error'=>true, 'msg'=>"First need to verified your site. Click on site verification refresh icon to verified your site.");
      }else if(property_exists($googleDetail,"is_domain_claim") && $googleDetail->is_domain_claim == '0'){
      	//'website_url' => $googleDetail->site_url,
        $postData = [
		      'merchant_id' => $googleDetail->merchant_id,  
		       'website_url' => get_site_url(),
		      'subscription_id' => $googleDetail->id,
		      'account_id' => $googleDetail->google_merchant_center_id
		    ];		    
				$claimWebsite = $this->customApiObj->claimWebsite($postData);
		    if(isset($claimWebsite->error) && !empty($claimWebsite->errors)){ 
		    	return array('error'=>true, 'msg'=>$claimWebsite->errors[0]);
		    }else{
		      $this->set_update_api_to_db();
		      return array('error'=>false, 'msg'=>"Domain claimed successfully.");
		    }
		  }else{
		  	return array('error'=>false, 'msg'=>"Already domain claimed successfully.");
		  }      
    }		
	}

	
	public function call_site_verified(){
		$googleDetail = [];
    $google_detail = $this->get_ee_options_data();
    if(isset($google_detail['setting']) && $google_detail['setting']){      
      $googleDetail = $google_detail['setting'];
      if(property_exists($googleDetail,"is_site_verified") && $googleDetail->is_site_verified == '0'){
      	//'website_url' => $googleDetail->site_url, 
        $postData = [
		      'merchant_id' => $googleDetail->merchant_id,
		      'website_url' => get_site_url(),		      
		      'subscription_id' => $googleDetail->id,
		      'account_id' => $googleDetail->google_merchant_center_id
		    ];
		   	$postData['method']="file"; 
				$siteVerificationToken = $this->customApiObj->siteVerificationToken($postData);

        if(isset($siteVerificationToken->error) && !empty($siteVerificationToken->errors)){
        	goto call_method_tag;        	
        }else{
          $myFile = ABSPATH.$siteVerificationToken->data->token;
          if(!file_exists($myFile)){
            $fh = fopen($myFile, 'w+');
            chmod($myFile,0777);
            $stringData = "google-site-verification: ".$siteVerificationToken->data->token;
            fwrite($fh, $stringData);
            fclose($fh);
          }
          $postData['method']="file";
          $siteVerification = $this->customApiObj->siteVerification($postData);          
          if(isset($siteVerification->error) && !empty($siteVerification->errors)){
          	call_method_tag:
          	//methd using tag
          	$postData['method']="meta";
          	$siteVerificationToken_tag = $this->customApiObj->siteVerificationToken($postData);
          	if(isset($siteVerificationToken_tag->data->token) && $siteVerificationToken_tag->data->token){
          		$ee_additional_data = $this->get_ee_additional_data();
          		$ee_additional_data['add_site_varification_tag']=1;
          		$ee_additional_data['site_varification_tag_val']=base64_encode($siteVerificationToken_tag->data->token);

          		$this->set_ee_additional_data($ee_additional_data);
          		sleep(1);
          		$siteVerification_tag = $this->customApiObj->siteVerification($postData);
          		if(isset($siteVerification_tag->error) && !empty($siteVerification_tag->errors)){
          			return array('error'=>true, 'msg'=>$siteVerification_tag->errors[0]);
          		}else{
          			$this->set_update_api_to_db();
          			return array('error'=>false, 'msg'=>"Site verification successfully.");
          		}
          	}else{
          		return array('error'=>true, 'msg'=>$siteVerificationToken_tag->errors[0]);
          	}       	
          	// one more try
          }else{
            $this->set_update_api_to_db();
		      	return array('error'=>false, 'msg'=>"Site verification successfully.");
          }
        }
		  }else{
		  	return array('error'=>false, 'msg'=>"Already site verification successfully.");
		  }      
    }		
	}

	public function get_tvc_popup_message(){
		return '<div id="tvc_popup_box">
		<span class="close" id="tvc_close_msg" onclick="tvc_helper.tvc_close_msg()"> × </span>
			<div id="box">
				<div class="tvc_msg_icon" id="tvc_msg_icon"></div>
				<h4 id="tvc_msg_title"></h4>
				<p id="tvc_msg_content"></p>
				<div id="tvc_closeModal"></div>
			</div>
		</div>';		
	}

	public function get_auto_sync_time_space(){
		$time_space = strtotime("25 days",0);	//day	
		return $time_space;
	}

	public function get_auto_sync_batch_size(){
		return "200";
	}

	public function get_last_auto_sync_product_info(){
		return $this->TVC_Admin_DB_Helper->tvc_get_last_row('ee_product_sync_call', array("total_sync_product","create_sync","next_sync","status"));
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

  public function getTableColumns($table) {
  	global $wpdb;
		$tablename = $wpdb->prefix .$table;
    return $wpdb->get_results("SELECT column_name as field FROM information_schema.columns WHERE table_name = '$table'");
  }

  public function getTableData($table = null, $columns = array()) {
  	if($table ==""){
  		$table = $wpdb->prefix.'postmeta';
  	}
  	global $wpdb;
    return $wpdb->get_results("SELECT  DISTINCT " . implode(',', $columns) . " as field FROM $table");
  }
  /* message notification */
  public function set_ee_msg_nofification_list($ee_msg_list){
		update_option("ee_msg_nofifications", serialize($ee_msg_list));
	}
  public function get_ee_msg_nofification_list(){
  	return unserialize(get_option('ee_msg_nofifications'));
  }
  public function add_ee_msg_nofification($key, $html, $title = null, $link = null, $link_title = null, $overwrite= false, $link_type = "internal"){
  	$ee_msg_list = $this->get_ee_msg_nofification_list();
  	if((!isset($ee_msg_list[$key]) && $html !="") ||($overwrite == true && isset($ee_msg_list[$key]) && $html !="")){
	  	$msg = array();  	
	  	$date_formate=get_option('date_format')." ".get_option('time_format');
	    if($date_formate ==""){
	      $date_formate = 'M-d-Y';
	    } 
	  	$msg["title"] = isset($title)?$title:"";
	  	$msg["date"] = date( $date_formate, current_time( 'timestamp' ) );
	  	$msg["html"] = base64_encode((isset($html))?$html:"");
	  	if($link != ""){
	  		$msg["link"] = $link;
	  		$msg["link_title"] = (isset($link_title) && $link_title)?$link_title:"Learn more";
	  		$msg["link_type"] = $link_type;
	  	}
	  	$msg["active"] = 1;
	  	$ee_msg_list[$key] = $msg;
	  	$this->set_ee_msg_nofification_list($ee_msg_list);
	  }
  }

  public function add_tvc_fixed_nofification(){
  	$nofifications = [];
  	/*
  	 * add fixed notification
  	 */
  	$nofifications["tvc_f_notif_1"] = array(
  		"tittle"=>"Congratulations..!! You are one step closer.",
  		"html"=>"Thanks for installing the new avatar of Enhanced Ecommerce for WooCommerce plugin. Explore the full potential of Google Analytics, Google Ads and Google shopping by setting up all your Google accounts and take data driven decisions to scale your eCommerce business faster."
  	);
  	$nofifications["tvc_f_notif_2"] = array(
  		"tittle"=>"Share your feedback.",
  		"html"=>"Your feedback is very important to us. Please write about your experience and the the new feature requests here.",
  		"link"=>"https://wordpress.org/support/plugin/enhanced-e-commerce-for-woocommerce-store/reviews/",
  		"link_title"=>"Share Feedback",
  		"link_type"=>"external"
  	);
  	/*
  	 * add payment notification
  	 */  	
  	$google_detail = $this->get_ee_options_data();
		if(isset($google_detail['setting'])){
		  $googleDetail = $google_detail['setting'];
		  
		  if(isset($googleDetail->subscription_expiry_date) && !in_array($googleDetail->plan_id, array("1"))){ 
		  	$current = strtotime("now");
		  	//echo "<br>curent date: ".date( 'M-d-Y H:i',$current);
		  	$subscription_expiry_time = strtotime($googleDetail->subscription_expiry_date);	
		  	//echo "<br>subscription expiry date: ".date( 'M-d-Y H:i',$subscription_expiry_time);
			  $diffrent_day = floor(( $subscription_expiry_time - $current)/(60*60*24) +1);
			  if($diffrent_day < 6 && $diffrent_day > 0){	
			  	$befor_day = $diffrent_day." ".($diffrent_day == 1 ? 'day':'days');		  	 
			  	$nofifications["tvc_pay_not_".date("YYYY_m_d",$current)] = array(
			  		"tittle"=>"Gentle reminder",
			  		"html"=>"Your plan is expiring in ".$befor_day.".  Payment will be auto debited from your configured paypal account on “next billing date”."
			  	);
			  }
			  $diffrent_day = floor(( $current - $subscription_expiry_time)/(60*60*24)-1);
			  /*if($diffrent_day == 5 ){		  	 
			  	$nofifications["tvc_expired_plan_not_".date("YYYY_m_d",$current)] = array(
			  		"tittle"=>"Your plan expired.",
			  		"html"=>"Your subscription plan has been expiring in some time."
			  	);
			  }*/
			  if($diffrent_day == 6 ){		  	 
			  	$nofifications["tvc_expired_plan_not_".date("YYYY_m_d",$current)] = array(
			  		"tittle"=>"Plan Expired..!!",
			  		"html"=>"Your plan is expired now. Contact “analytics2@tatvic.com” or call us at “(415) 968-6313” to renew your plan."
			  	);
			  }
			}
		  
		}
  	/*
  	 * add notifications
  	 */
  	if(!empty($nofifications)){
	  	foreach ($nofifications as $key => $value){
	  		if(isset($value["html"]) && $value["html"] != ""){
	  			$n_link = isset($value["link"])?$value["link"]:"";
	  			$n_link_title = isset($value["link_title"])?$value["link_title"]:"";
	  			$link_type = isset($value["link_type"])?$value["link_type"]:"";
	  			$this->add_ee_msg_nofification( $key, $value["html"], $value["tittle"], $n_link,  $n_link_title, "", $link_type);
	  		}	  		
	  	}
	  }
  }

  public function active_licence($licence_key, $subscription_id){
  	if($licence_key != ""){
  		$customObj = new CustomApi();
    	return $customObj->active_licence_Key($licence_key, $subscription_id);
  	}  	
  }

  public function get_pro_plan_site(){
  	return "https://codecanyon.net/item/actionable-google-analytics-for-woocommerce/9899552?utm_source=TatvicEE&utm_medium=DashboardBuyBottom&utm_campaign=WPlisting";
  }

  public function is_ga_property(){
  	$data = $this->get_ee_options_settings();
	  $is_connected = false;
	  if((isset($data['ga_id']) && $data['ga_id'] != '') || (isset($data['ga_id']) && $data['ga_id'] != '')){
	    return true;
	  }else{
	  	return false;
	  }
  }
   /*
   * get user plan id
   */
  public function get_plan_id(){
  	if(!empty($this->plan_id)){
			return $this->plan_id;
		}else{
			$plan_id = 1;
	  	if(isset($google_detail['setting'])){
			  $googleDetail = $google_detail['setting'];
			  if(isset($googleDetail->plan_id) && !in_array($googleDetail->plan_id, array("1"))){
			    $plan_id = $googleDetail->plan_id;
			  }
			}
			return $this->plan_id = $plan_id;
  	}
	}
  
}