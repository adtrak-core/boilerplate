<?php
class GoogleShoppingFeed {
  protected $site_url="";  
  protected $TVC_Admin_Helper="";
  protected $subscriptionId = "";
  public function __construct() {
    $this->TVC_Admin_Helper = new TVC_Admin_Helper();
    $this->subscriptionId = $this->TVC_Admin_Helper->get_subscriptionId(); 
    $this->site_url = "admin.php?page=enhanced-ecommerce-google-analytics-admin-display&tab=";     
    $this->create_form();
  }
  public function add_list_html($title, $val){
    return '<li>
      <div class="row">
        <div class="col-7 col-md-8 align-self-center pr-0">
            <span class="text">'.$title.'</span>
        </div>
        <div class="col-5 col-md-4 align-self-center text-right">
            <span class="text"><strong>'. $val.'</strong></span>
        </div>
      </div>
    </li>';
  }
  public function configuration_list_html($title, $val){
    $imge = (isset($val) && $val != "" && $val != 0) ? '<img src="' . ENHANCAD_PLUGIN_URL.'/admin/images/config-success.svg" alt="config-success"/>' : '<img src="' . ENHANCAD_PLUGIN_URL.'/admin/images/exclaimation.png" alt="no-config-success"/>';
    return '<li>
      <div class="row">
        <div class="col-7 col-md-7 col-lg-9 align-self-center pr-0">
            <span class="text">'.$title.'</span>
        </div>
        <div class="col-5 col-md-5 col-lg-3 align-self-center text-right">
            <div class="list-image">'.$imge.'</div>
        </div>
      </div>
    </li>';
  }
  public function configuration_error_list_html($title, $val, $call_domain_claim, $googleDetail){
    if(isset($googleDetail->google_merchant_center_id) && $googleDetail->google_merchant_center_id && $this->subscriptionId != "" ){
      return '<li>
        <div class="row">
          <div class="col-7 col-md-7 col-lg-7 align-self-center pr-0">
              <span class="text">'.$title.'</span>
          </div>
          <div class="col-5 col-md-5 col-lg-5 align-self-center text-right">
              <div class="list-image"><img id="refresh_'.$call_domain_claim.'" onclick="'.$call_domain_claim.'();" src="'. ENHANCAD_PLUGIN_URL.'/admin/images/refresh.png"><img src="' . ENHANCAD_PLUGIN_URL.'/admin/images/exclaimation.png" alt="no-config-success"/></div>
          </div>
        </div>
      </li>';
    }else{
      return '<li>
        <div class="row">
          <div class="col-7 col-md-7 col-lg-7 align-self-center pr-0">
              <span class="text">'.$title.'</span>
          </div>
          <div class="col-5 col-md-5 col-lg-5 align-self-center text-right">
              <div class="list-image"><img src="' . ENHANCAD_PLUGIN_URL.'/admin/images/exclaimation.png" alt="no-config-success"/></div>
          </div>
        </div>
      </li>';
    }
  }
  public function create_form() {
    $googleDetail = [];    
    $google_detail = $this->TVC_Admin_Helper->get_ee_options_data();
    if(isset($google_detail['setting'])){
      if ($google_detail['setting']) {
        $googleDetail = $google_detail['setting'];
      }
    }      

    $syncProductStat = [];        
    $args = array('post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => -1);
    $products = new WP_Query($args);
    $totalActiveWooProduct = $products->found_posts;
    if(isset($google_detail['prod_sync_status'])){
      if ($google_detail['prod_sync_status']) {
        $syncProductStat = $google_detail['prod_sync_status'];
      }
    }
    $sync_product_total = (!empty($syncProductStat)) ? $syncProductStat->total : "0";
    $sync_product_approved = (!empty($syncProductStat)) ? $syncProductStat->approved : "0";
    $sync_product_disapproved = (!empty($syncProductStat)) ? $syncProductStat->disapproved : "0";
    $sync_product_pending = (!empty($syncProductStat)) ? $syncProductStat->pending : "0";

    $totalCampaigns = 0;$campaignActive = 0; $campaignClicks = 0;
    $campaignCost = 0; $campaignConversions = 0; $campaignSales = 0;
    // Get currency
    $currency = $this->TVC_Admin_Helper->get_user_currency_symbol();
    if(isset($google_detail['campaigns_list'])){
      if ($google_detail['campaigns_list']) {
        $campaigns_list = $google_detail['campaigns_list'];
        $totalCampaigns = count($campaigns_list);
        foreach ($campaigns_list as $campaign) {
          if ($campaign->active == 1) {
            $campaignActive = $campaignActive + $campaign->active;
          }
          $campaignClicks = $campaignClicks + $campaign->clicks;
          $campaignCost = $campaignCost + $campaign->cost;
          $campaignConversions = $campaignConversions + $campaign->conversions;
          $campaignSales = $campaignSales + $campaign->sales;
        }
        if (count($campaigns_list) > 0) {
          $campaignConversions = $campaignConversions / count($campaigns_list);
        }
      }
      $campaignActive = (isset($googleDetail->google_ads_id) && $googleDetail->google_ads_id != "" ? $campaignActive : '0');
      $campaignCost = (isset($googleDetail->google_ads_id) && $googleDetail->google_ads_id != "" ? $currency . $campaignCost : '0');
      $campaignClicks = (isset($googleDetail->google_ads_id) && $googleDetail->google_ads_id != "" ? $campaignClicks : '0');
      $campaignConversions = (isset($googleDetail->google_ads_id) && $googleDetail->google_ads_id != "" ? $campaignConversions . "%" : '0');
      $campaignSales = (isset($googleDetail->google_ads_id) && $googleDetail->google_ads_id != "" ? $currency . $campaignSales : '0');
   }
  $last_api_sync_up = "";
  if(isset($google_detail['sync_time']) && $google_detail['sync_time']){      
    $date_formate=get_option('date_format')." ".get_option('time_format');
    if($date_formate ==""){
      $date_formate = 'M-d-Y H:i';
    }
    $last_api_sync_up = date( $date_formate, $google_detail['sync_time']);      
  }
  $is_need_to_update = $this->TVC_Admin_Helper->is_need_to_update_api_to_db();
?>
<div class="container-fluid">
	<div class="row">
		<div class= "col col-12">
			<div class="card mw-100" style="padding:0;">
				<div class="card-body">
          <div class="tab-pane show active" id="googleShoppingFeed">
	          <div class="row">
	            <div class="col-md-6 col-lg-8 border-right">
                <?php if($this->subscriptionId != ""){?>
                  <div class="tvc-api-sunc">                    
                    <span>
                    <?php if($last_api_sync_up){
                      echo "Details last synced at ".$last_api_sync_up; 
                    }else{
                      echo "Refresh sync up";
                    }?></span><img id="refresh_api" onclick="call_tvc_api_sync_up();" src="<?php echo ENHANCAD_PLUGIN_URL.'/admin/images/refresh.png'; ?>">                    
                  </div>
                <?php } ?>
                <?php
                  $last_auto_sync = $this->TVC_Admin_Helper->get_last_auto_sync_product_info();
                  if(!empty($last_auto_sync)){
                    $status = isset($last_auto_sync['status'])?$last_auto_sync['status']:0;
                    $status_text = array("0"=>"Failed","1"=>"Completed");
                    $create_sync = (isset($last_auto_sync['create_sync']))?$last_auto_sync['create_sync']:"";
                    $create_sync = date($date_formate,strtotime($create_sync));
                    $next_sync = (isset($last_auto_sync['next_sync']))?$last_auto_sync['next_sync']:"";
                    $next_sync = date($date_formate,strtotime($next_sync));
                    ?>
                    <div class="product-auto-sync-details">
                      <strong>Last auto product sync details</strong>
                      <table>
                        <tr><th>Last sync</th><th width="10">Sync product</th><th width="10px">Status</th><th>Upcoming sync</th></tr>
                        <tr>
                          <td><?php echo  $create_sync;?></td>
                          <td width="10"><?php echo  (isset($last_auto_sync['total_sync_product']))?$last_auto_sync['total_sync_product']:"";?></td>
                          <td><?php echo $status_text[$status]; ?></td>
                          <td><?php echo  $next_sync;?></td>
                        </tr>
                      </table>
                    </div>
                    <?php
                  }
                ?>
                <div class="configuration-section" id="config-pt1">
                  <div class="row confg-card gsf-sec">                    
                    <div class="col-md-12 col-lg-4 mb-3 mb-lg-0">
                      <div class="card configure-card">
                        <div class="card-header">
                          <h4 class="confg-title">Configuration</h4>
                        </div>
                        <div class="card-body">
                          <ul class="list-unstyled"><?php
                          $is_domain_claim = (isset($googleDetail->is_domain_claim))?$googleDetail->is_domain_claim:"";
                          $is_site_verified = (isset($googleDetail->is_site_verified))?$googleDetail->is_site_verified:"";
                            echo $this->configuration_list_html("Google merchant center",(isset($googleDetail->google_merchant_center_id))?$googleDetail->google_merchant_center_id:"");
                            if($is_site_verified ==1){
                              echo $this->configuration_list_html("Site Verified",$is_site_verified);
                            }else{
                              echo $this->configuration_error_list_html("Site Verified",$is_site_verified,"call_site_verified", $googleDetail);
                            }
                            if($is_domain_claim ==1){
                              echo $this->configuration_list_html("Domain claim",$is_domain_claim);
                            }else{
                              echo $this->configuration_error_list_html("Domain claim",$is_domain_claim, 'call_domain_claim', $googleDetail);
                            }
                            echo $this->configuration_list_html("Google Ads linking",((isset($googleDetail->google_ads_id)))?$googleDetail->google_ads_id:"");
                            ?>
                            </ul>
                        </div>
                        <div class="card-footer">
                          <a href="<?php echo $this->site_url.'gaa_config_page'; ?>" class="btn btn-primary btn-success w-100" id="configuration">Edit</a>
                        </div>
                      </div>
                    </div>
                   <div class="col-md-12 col-lg-4 mb-3 mb-lg-0">
                     <div class="card">
                        <div class="card-header">
                          <h4 class="confg-title">Product Sync</h4>
                        </div>
                        <div class="card-body">
                          <ul class="list-unstyled">
                            <?php
                            echo $this->add_list_html("Active products in WooCommerce",$totalActiveWooProduct)
                            .$this->add_list_html("Total synced products in Merchant center", $sync_product_total)
                            .$this->add_list_html("Approved", $sync_product_approved)
                            .$this->add_list_html("Disapproved", $sync_product_disapproved)
                            .$this->add_list_html("Pending", $sync_product_pending);
                            ?>
                          </ul>
                        </div>
                        <?php
                        if (isset($googleDetail->google_merchant_center_id) && $googleDetail->google_merchant_center_id != "") {?>
                        <div class="card-footer">
                          <a href="<?php echo $this->site_url.'sync_product_page'; ?>" class="btn btn-primary btn-success w-100" id="product-sync">Edit</a>
                        </div>
                        <?php } ?>
                      </div>
                    </div>
                    <div class="col-md-12 col-lg-4 mb-3 mb-lg-0">
                     <div class="card">
                        <div class="card-header">
                          <h4 class="confg-title">Smart  Shopping Campaigns</h4>
                        </div>
                        <div class="card-body">
                          <ul class="list-unstyled">
                            <?php
                            echo $this->add_list_html("Total campaign",$totalCampaigns)
                            .$this->add_list_html("Active campaigns",$campaignActive)
                            .$this->add_list_html("Cost",$campaignCost)
                            .$this->add_list_html("Click",$campaignClicks)
                            .$this->add_list_html("Conversion%",$campaignConversions)
                            .$this->add_list_html("Sales",$campaignSales); ?>               
                          </ul>
                        </div>
                        <?php if (isset($googleDetail->google_ads_id) && $googleDetail->google_ads_id != "") { ?>
                        <div class="card-footer">
                          <a href="<?php echo $this->site_url.'shopping_campaigns_page'; ?>" id="smart-shopping-campaigns" class="btn btn-primary btn-success w-100">Edit</a>
                        </div>
                        <?php }?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6 col-lg-4">
                <div class="right-content"><?php echo get_tvc_help_html(); ?></div>
              </div>
            </div>
          </div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
  function call_site_verified(){
    var tvs_this = event.target;
    $("#refresh_call_site_verified").css("visibility","hidden");
    $(tvs_this).after('<div class="domain-claim-spinner tvc-nb-spinner" id="site-verified-spinner"></div>');
    jQuery.post(myAjaxNonces.ajaxurl,{
      action: "tvc_call_site_verified",
      apiDomainClaimNonce: myAjaxNonces.SiteVerifiedNonce
    },function( response ){
      var rsp = JSON.parse(response);    
      if(rsp.status == "success"){        
        tvc_helper.tvc_alert("success","",rsp.message,true);
        location.reload();
      }else{
        tvc_helper.tvc_alert("error","",rsp.message,true);
      }
      $("#site-verified-spinner").remove();
    });
  }
  function call_domain_claim(){
    var tvs_this = event.target;
    $("#refresh_call_domain_claim").css("visibility","hidden");
    $(tvs_this).after('<div class="domain-claim-spinner tvc-nb-spinner" id="domain-claim-spinner"></div>');
    jQuery.post(myAjaxNonces.ajaxurl,{
      action: "tvc_call_domain_claim",
      apiDomainClaimNonce: myAjaxNonces.apiDomainClaimNonce
    },function( response ){
      var rsp = JSON.parse(response);    
      if(rsp.status == "success"){
        tvc_helper.tvc_alert("success","",rsp.message,true);        
        //alert(rsp.message);
        location.reload();
      }else{
        tvc_helper.tvc_alert("error","",rsp.message,true)
      }
      $("#domain-claim-spinner").remove();
    });
  }
  $(document).ready(function() {
    var is_need_to_update = "<?php echo $is_need_to_update; ?>";
    if(is_need_to_update == 1 || is_need_to_update == true){
      tvc_helper.tvc_alert("error","Attention !","Auto-sync up is in the process do not refresh the page.");
      call_tvc_api_sync_up();
    }    
  });
  function call_tvc_api_sync_up(){
    var tvs_this = $("#refresh_api");
    $("#tvc_msg").remove();
    $("#refresh_api").css("visibility","hidden");
    $(tvs_this).after('<div class="tvc-nb-spinner" id="tvc-nb-spinner"></div>');
    jQuery.post(myAjaxNonces.ajaxurl,{
      action: "tvc_call_api_sync",
      apiSyncupNonce: myAjaxNonces.apiSyncupNonce
    },function( response ){
      var rsp = JSON.parse(response);    
      if(rsp.status == "success"){
        $("#tvc-nb-spinner").remove();
        tvc_helper.tvc_alert("success","",rsp.message,true);
        setTimeout(function(){ location.reload();}, 5000);
      }    
    });
  }  
</script>
  <?php
  }
}
?>