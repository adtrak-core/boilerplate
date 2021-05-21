<?php
class GAAConfiguration {
  protected $merchantId;
  protected $TVC_Admin_Helper;
  protected $currentCustomerId;
  protected $subscriptionId;
  protected $country;
  public function __construct() {
    ini_set('max_execution_time', '0'); 
    ini_set('memory_limit','-1');
    $this->includes();
    $this->TVC_Admin_Helper = new TVC_Admin_Helper();
    $this->merchantId = $this->TVC_Admin_Helper->get_merchantId();
    $this->accountId = $this->TVC_Admin_Helper->get_main_merchantId();
    $this->currentCustomerId = $this->TVC_Admin_Helper->get_currentCustomerId(); 
    $this->subscriptionId = $this->TVC_Admin_Helper->get_subscriptionId(); 
    $this->country = $this->TVC_Admin_Helper->get_woo_country(); 
    $this->site_url = "admin.php?page=enhanced-ecommerce-google-analytics-admin-display&tab=";     
    $this->url = $this->TVC_Admin_Helper->get_connect_url();       
    $this->html_run();
  }
  public function includes() {
    if (!class_exists('Tatvic_Category_Wrapper')) {
      require_once(__DIR__ . '/tatvic-category-wrapper.php');
    }
  }

  public function html_run() {
    $this->TVC_Admin_Helper->add_spinner_html();
    $this->create_form();
  }    

  public function wooCommerceAttributes() {
    $queries = new TVC_Queries();
    global $wpdb;
    $tve_table_prefix = $wpdb->prefix;
    $column1 = json_decode(json_encode($queries->getTableColumns($tve_table_prefix.'posts')), true);
    $column2 = json_decode(json_encode($queries->getTableData($tve_table_prefix.'postmeta', ['meta_key'])), true);
    return array_merge($column1, $column2);
  }

  public function create_form() {
    if(isset($_GET['welcome_msg']) && $_GET['welcome_msg'] == true){
      $class = 'notice notice-success';
      $message = esc_html__('Get your WooCommerce products in front of the millions of shoppers across Google by setting up your Google Merchant Center account from below.');
      printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
      ?>
      <script>
        $(document).ready(function() {
          var msg="<?php echo $message;?>"
          tvc_helper.tvc_alert("success","Hey!",msg,true);
        });
      </script>
      <?php
    }
    $category_wrapper_obj = new Tatvic_Category_Wrapper();
    $category_wrapper = $category_wrapper_obj->category_table_content('mapping');
    $googleDetail = [];
    $google_detail = $this->TVC_Admin_Helper->get_ee_options_data();
    if(isset($google_detail['setting'])){
      if ($google_detail['setting']) {
        $googleDetail = $google_detail['setting'];
      }
    }?>
<div class="container-fluid">
	<div class="row">
		<div class= "col col-12">
			<div class="card mw-100" style="padding:0;">
				<div class="card-body">
	        <div class="tab-pane show active" id="googleShoppingFeed">
	          <div class="row">
	            <div class="col-md-6 col-lg-8 border-right">
                <div class="configuration-section" id="config-pt1">
                  <?php echo get_google_shopping_tabs_html($this->site_url,(isset($googleDetail->google_merchant_center_id))?$googleDetail->google_merchant_center_id:""); ?>
                </div>
	              <div class="mt-3" id="config-pt2">
	                <div class="google-account-analytics" id="gaa-config">
                    <div class="licence">
                      <p>You are using our free plugin, no licence needed ! Happy analyzing..!! :)</p>
                      <p>To unlock more features of google products, consider our <a href="https://codecanyon.net/item/actionable-google-analytics-for-woocommerce/9899552?utm_source=TatvicEE&utm_medium=DashboardBuyBottom&utm_campaign=WPlisting" target="_blank" class="text-underline">pro version.</a></p>
                    </div>
	                  <div class="row mb-3">
                    <div class="col-6 col-md-6">
                      <h2 class="ga-title">Connected Google Merchant center account:</h2>
                    </div>
                    <div class="col-6 col-md-3">
                      <p class="ga-text"><?php echo (isset($googleDetail->google_merchant_center_id) && $googleDetail->google_merchant_center_id != '' ? $googleDetail->google_merchant_center_id : 'N/A'); ?></p>
                    </div>
                    <div class="col-6 col-md-3">
                    <?php
                    if(isset($googleDetail->google_merchant_center_id) && $googleDetail->google_merchant_center_id != ''){
                      echo '<p class="ga-text text-right">Click here to <a target="_blank" href="' . $this->url . '" class="text-underline">update</a></p>';
                    }else{
                      echo '<p class="ga-text text-right">Click here to <a href="#" class="text-underline" data-toggle="modal" data-target="#tvc_google_connect">connect</a></p>';
                    }?>
                    </div>
	                </div>
	               <div class="row mb-3">
                    <div class="col-6 col-md-6">
                      <h2 class="ga-title">Linked Google Ads Account:</h2>
                    </div>
                    <div class="col-6 col-md-3">
                     <p class="ga-text"><?php echo (isset($googleDetail->google_ads_id) && $googleDetail->google_ads_id != '' ? $googleDetail->google_ads_id : 'N/A');?></p>
                    </div>
                    <div class="col-6 col-md-3">
                    <?php
                    if (isset($googleDetail->google_ads_id) && $googleDetail->google_ads_id != '') {
                      echo '<p class="ga-text text-right">Click here to <a target="_blank" href="' . $this->url . '" class="text-underline">update</a></p>';
                    } else {
                      echo '<p class="ga-text text-right">Click here to <a href="#"  data-toggle="modal" data-target="#tvc_google_connect" class="text-underline">connect</a></p>';
                    } ?>
                    </div>
	                </div>
                  <?php
                  if (isset($googleDetail->google_merchant_center_id) && $googleDetail->google_merchant_center_id != '') {?>
                  <div class="row mb-3">
                    <div class="col-6 col-md-6">
                      <h2 class="ga-title">Sync Products:</h2>
                    </div>
                    <div class="col-6 col-md-6">
                      <button id="tvc_btn_product_sync" type="button" class="btn btn-primary btn-success" data-toggle="modal" data-target="#syncProduct">Sync New Products</button>                        
                    </div>
	                </div>
                  <div class="row mb-3">
                    <div class="col-6 col-md-6">
                      <h2 class="ga-title">Smart Shopping Campaigns:</h2>
                    </div>
                    <div class="col-6 col-md-6">
                      <a href="admin.php?page=enhanced-ecommerce-google-analytics-admin-display&tab=add_campaign_page" class="btn btn-primary btn-success">Create Smart Shopping Campaign</a>
                    </div>
                  </div>
                  <?php }else{ ?>
                  <div class="row mb-3">
                    <div class="col-6 col-md-6">
                      <h2 class="ga-title">Sync Products:</h2>
                    </div>
                    <div class="col-6 col-md-6">                     
                      <button type="button" class="btn btn-primary btn-success" data-toggle="modal" data-target="#tvc_google_connect">Sync New Products</button>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-6 col-md-6">
                      <h2 class="ga-title">Smart Shopping Campaigns:</h2>
                    </div>
                    <div class="col-6 col-md-6">
                      <a href="#" class="btn btn-primary btn-success" data-toggle="modal" data-target="#tvc_google_connect">Create Smart Shopping Campaign</a>
                    </div>
                  </div>
                  <?php } ?>
                  </div>
                </div>
              </div>
              <div class="col-md-6 col-lg-4">
                <div class="right-content"> <?php echo get_tvc_help_html(); ?></div>
              </div>
            </div>
          </div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade popup-modal create-campa overlay" id="syncProduct" data-backdrop="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">      
      <div class="modal-body">
        <button type="button" class="close tvc-popup-close" data-dismiss="modal"> &times; </button>
        <h5>Map your product attributes</h5>
        <p>Google Merchant Center uses attributes to format your product information for Shopping Ads. Map your product attributes to the Merchant Center product attributes below. You can also edit each product’s individual attributes after you sync your products. Not all fields below are marked required, however based on your shop's categories and your country you might map a few optional attributes as well. See the full guide <a target="_blank" href="https://support.google.com/merchants/answer/7052112">here</a>.
        </p>
        <div class="wizard-section campaign-wizard">
          <div class="wizard-content">
            <input type="hidden" name="merchant_id" id="merchant_id" value="<?php echo $this->merchantId; ?>">
            <form class="tab-wizard wizard-  wizard" id="productSync" method="POST">
              <h5><span class="wiz-title">Category Mapping</span></h5>
              <section>
                <div class="card-wrapper">                                        
                  <div class="row">
                    <div class="col-6">
                      <h6 class="heading-tbl">WooCommerce Category</h6>
                    </div>
                    <div class="col-6">
                      <h6 class="heading-tbl">Google Merchant Center Category</h6>
                    </div>
                  </div><?php echo $category_wrapper; ?>
                </div>
              </section>
              <!-- Step 2 -->
              <h5><span class="wiz-title">Product Attribution Mapping</span></h5>
              <section>
              <div class="card-wrapper">                                        
                <div class="row">
                  <div class="col-6">
                    <h6 class="heading-tbl">Google Merchant center product attributes</h6>
                  </div>
                  <div class="col-6">
                    <h6 class="heading-tbl">WooCommerce product attributes</h6>
                  </div>
                </div>
                <?php
                $ee_mapped_attrs = unserialize(get_option('ee_prod_mapped_attrs'));
                $wooCommerceAttributes = $this->wooCommerceAttributes();
                foreach ($this->TVC_Admin_Helper->get_gmcAttributes() as $key => $attribute) {
                  $sel_val="";
                  echo '<div class="row">
                    <div class="col-6 align-self-center">
                      <div class="form-group">
                        <span class="td-head">' . $attribute["field"] . " " . (isset($attribute["required"]) && $attribute["required"] == 1 ? '<span style="color: red;"> *</span>' : "") . '</span> 
                        <small class="form-label-control">' . (isset($attribute["desc"])? $attribute["desc"]:"") . '</small>
                      </div>
                    </div>
                    <div class="col-6 align-self-center">
                      <div class="form-group">';
                        $tvc_select_option = $wooCommerceAttributes;
                        $require = (isset($attribute['required']) && $attribute['required'])?true:false;
                        $sel_val_def = (isset($attribute['wAttribute']))?$attribute['wAttribute']:"";
                        if($attribute["field"]=='link'){
                            echo "product link";
                        }else if($attribute["field"]=='shipping'){
                          $sel_val = (isset($ee_mapped_attrs[$attribute["field"]]))?$ee_mapped_attrs[$attribute["field"]]:$sel_val_def;
                          //$name, $class_id, string $label=null, $sel_val = null, bool $require = false
                          echo $this->TVC_Admin_Helper->tvc_text($attribute["field"], 'number', '', 'Add shipping flat rate', $sel_val, $require);
                        }else if($attribute["field"]=='tax'){
                          $sel_val = (isset($ee_mapped_attrs[$attribute["field"]]))?$ee_mapped_attrs[$attribute["field"]]:$sel_val_def;
                          //$name, $class_id, string $label=null, $sel_val = null, bool $require = false
                          echo $this->TVC_Admin_Helper->tvc_text($attribute["field"], 'number', '', 'Add TAX flat (%)', $sel_val, $require);
                        }else if($attribute["field"]=='content_language'){
                          echo $this->TVC_Admin_Helper->tvc_language_select($attribute["field"], '', 'Please Select Attribute', 'en',$require);
                        }else if($attribute["field"]=='target_country'){
                          //$name, $class_id, bool $require = false
                          echo $this->TVC_Admin_Helper->tvc_countries_select($attribute["field"], '', 'Please Select Attribute', $require);
                        }else{
                          if(isset($attribute['fixed_options']) && $attribute['fixed_options'] !=""){
                            $tvc_select_option_t = explode(",", $attribute['fixed_options']);
                            $tvc_select_option=[];
                            foreach( $tvc_select_option_t as $o_val ){
                              $tvc_select_option[]['field'] = $o_val;
                            }
                            $sel_val = $sel_val_def; 
                            $this->TVC_Admin_Helper->tvc_select($attribute["field"],'','Please Select Attribute', $sel_val, $require, $tvc_select_option);
                          }else{
                            $sel_val = (isset($ee_mapped_attrs[$attribute["field"]]))?$ee_mapped_attrs[$attribute["field"]]:$sel_val_def;
                          //$name, $class_id, $label="Please Select", $sel_val, $require, $option_list
                          $this->TVC_Admin_Helper->tvc_select($attribute["field"],'','Please Select Attribute', $sel_val, $require, $tvc_select_option);
                          }
                        }
                      echo '</div>
                    </div>
                  </div>';
                }?>
              </div>
              </section>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php echo get_connect_google_popup_html()?>
<?php $shop_categories_list = $this->TVC_Admin_Helper->get_tvc_product_cat_list(); 
$is_need_to_domain_claim = false;
if(isset($googleDetail->google_merchant_center_id) && $googleDetail->google_merchant_center_id && $this->subscriptionId != "" && isset($googleDetail->is_domain_claim) && $googleDetail->is_domain_claim == '0'){
  $is_need_to_domain_claim = true;
}?>
<script type="text/javascript">
$(document).ready(function() {
	$(".select2").select2();

  $(document).on("click", "#tvc_btn_product_sync", function(event){
    var is_need_to_domain_claim = "<?php echo $is_need_to_domain_claim; ?>";
    if(is_need_to_domain_claim == 1 || is_need_to_domain_claim == true){
      event.preventDefault();
      jQuery.post(myAjaxNonces.ajaxurl,{
        action: "tvc_call_domain_claim",
        apiDomainClaimNonce: myAjaxNonces.apiDomainClaimNonce
      },function( response ){
        
      });
    }
  }); 
});
$(".tab-wizard").steps({
  headerTag: "h5",
  bodyTag: "section",
  transitionEffect: "fade",
  titleTemplate: '<span class="step">#index#</span> #title#',
  labels: {
    finish: "Sync Products",
    next: "Next",
    previous: "Previous",
  },
  onStepChanging: function(e, currentIndex, newIndex) {
    var shop_categories = JSON.parse("<?php echo $shop_categories_list; ?>");
    var is_tvc_cat_selecte = false;
    shop_categories.forEach(function(v,i){
      if(is_tvc_cat_selecte == false && $("#category-"+v).val() != "" && $("#category-"+v).val() != 0){
        is_tvc_cat_selecte =true;
        return false;
      }
    });    
    if(is_tvc_cat_selecte == 1 || is_tvc_cat_selecte == true){
      return true;
    }else{
      tvc_helper.tvc_alert("error","","Select at least one Google Merchant Center Category.",true);
      return false;
    }
  },
  onStepChanged: function(event, currentIndex, priorIndex) {
    $('.steps .current').prevAll().addClass('disabled');
  },
  onFinished: function(event, currentIndex) {
    var valid=true;
    jQuery(".field-required").each(function() {
      if($(this).val()==0 && valid){
        valid=false;
        $(this).select2('focus');
      }
    });
    if(!valid){
      tvc_helper.tvc_alert("error","","Please select all required fields");
    }else{
      submitProductSyncUp();
    }//check for required fields end        	
  }
});

function submitProductSyncUp() {
	var merchantId = '<?php echo $this->merchantId; ?>';
  var accountId = '<?php echo $this->accountId; ?>';
  var customerId = '<?php echo $this->currentCustomerId; ?>';
  var subscriptionId = '<?php echo $this->subscriptionId; ?>';         
	var formData = jQuery("#productSync").serialize();
	//console.log(formData);
	jQuery("#feed-spinner").css("display", "block");                
	jQuery.post(
    myAjaxNonces.ajaxurl,
    {
      action: "tvcajax-product-syncup",
      merchantId: merchantId,
      customerId: customerId,
      accountId: accountId,
      subscriptionId: subscriptionId,
      data: formData,
      productSyncupNonce: myAjaxNonces.productSyncupNonce
    },
    function( response ) {
      jQuery("#feed-spinner").css("display", "none");
      //console.log(response);
      var rsp = JSON.parse(response)
      if (rsp.status == "success") {
        $('#syncProduct').modal('hide');
        var message = "Your products have been synced in your merchant center account. It takes up to 30 minutes to reflect the product data in merchant center. As soon as they are updated, they will be shown in the \"Product Sync\" dashboard.";
          if (rsp.skipProducts > 0) {
            message = message + "\n Because of pricing issues, " + rsp.skipProducts + " products did not sync.";
          }
          tvc_helper.tvc_alert("success","",message);
          setTimeout(function(){
            window.location.replace("<?php echo $this->site_url.'sync_product_page'; ?>");
          }, 7000);
      } else {
        vc_helper.tvc_alert("error","",rsp.message);
      }
    }
  );
}

$(document).on("show.bs.modal", "#syncProduct", function (e) {
	jQuery("#feed-spinner").css("display", "block");
  selectCategory();
  $("select[id^=catmap]").each(function(){
  	removeChildCategory($(this).attr("id"))
	});
});

function selectCategory() {
  var country_id = "<?php echo $this->country; ?>";
  var customer_id = '<?php echo $this->currentCustomerId?>';
  var parent = "";
  jQuery.post(
    myAjaxNonces.ajaxurl,
    {
      action: "tvcajax-gmc-category-lists",
      countryCode: country_id,
      customerId: customer_id,
      parent: parent,
      gmcCategoryListsNonce: myAjaxNonces.gmcCategoryListsNonce
    },
    function( response ) {
      var categories = JSON.parse(response);
      var obj;
			$("select[id^=catmap]").each(function(){
				obj = $("#catmap-"+$(this).attr("catid")+"_0");
      	obj.empty();
    		obj.append("<option id='0' value='0' resourcename='0'>Select a category</option>");
      	$.each(categories, function (i, value) {
          obj.append("<option id=" + JSON.stringify(value.id) + " value=" + JSON.stringify(value.id) + " resourceName=" + JSON.stringify(value.resourceName) + ">" + value.name + "</option>");                
        });
			});
			jQuery("#feed-spinner").css("display", "none");
  });
}

function selectSubCategory(thisObj) {
	var selectId;
	var wooCategoryId;
	var GmcCategoryId;
	var GmcParent;
	selectId = thisObj.id;
	wooCategoryId = $(thisObj).attr("catid");
	GmcCategoryId = $(thisObj).find(":selected").val();
	GmcParent = $(thisObj).find(":selected").attr("resourcename");
  //$("#"+selectId).select2().find(":selected").val();
  // $("#"+selectId).select2().find(":selected").data("id");
  //console.log(selectId+"--"+wooCategoryId+"--"+GmcCategoryId+"--"+GmcParent);
  	
  jQuery("#feed-spinner").css("display", "block");
	removeChildCategory(selectId);
	selectChildCategoryValue(wooCategoryId);
  if (GmcParent != undefined) {
    var country_id = "<?php echo $this->country; ?>";
    var customer_id = '<?php echo $this->currentCustomerId?>';
  	jQuery.post(
      myAjaxNonces.ajaxurl,
      {
        action: "tvcajax-gmc-category-lists",
        countryCode: country_id,
        customerId: customer_id,
        parent: GmcParent,
        gmcCategoryListsNonce: myAjaxNonces.gmcCategoryListsNonce
      },
      function( response ) {
        var categories = JSON.parse(response);
        var newId;
      	var slitedId = selectId.split("_");
      	newId = slitedId[0]+"_"+ ++slitedId[1];
      	if(categories.length === 0){		
      	}else{
      		//console.log(newId);
        	$("#"+newId).empty();
        	$("#"+newId).append("<option id='0' value='0' resourcename='0'>Select a sub-category</option>");
          $.each(categories, function (i, value) {
            $("#"+newId).append("<option id=" + JSON.stringify(value.id) + " value=" + JSON.stringify(value.id) + " resourceName=" + JSON.stringify(value.resourceName) + ">" + value.name + "</option>");
          });
          $("#"+newId).addClass("form-control");
          //$("#"+newId).select2();
          $("#"+newId).css("display", "block");
      	}
      	jQuery("#feed-spinner").css("display", "none");
      }
    );	
  }
}

function removeChildCategory(currentId){
	var currentSplit = currentId.split("_");
  var childEleId;
	for (i = ++currentSplit[1]; i < 6; i++) {
		childEleId = currentSplit[0]+"_"+ i;
		//console.log($("#"+childEleId));
  	$("#"+childEleId).empty();
		$("#"+childEleId).removeClass("form-control");
    $("#"+childEleId).css("display", "none");
    if ($("#"+childEleId).data("select2")) {
		  $("#"+childEleId).off("select2:select");
			$("#"+childEleId).select2("destroy");
      $("#"+childEleId).removeClass("select2");
	 	}
	}
}

function selectChildCategoryValue(wooCategoryId){
	var childCatvala;
	for(i = 0; i < 6; i++){
		childCatvala = $("#catmap-"+wooCategoryId+"_"+i).find(":selected").attr("id");
    childCatname = $("#catmap-"+wooCategoryId+"_"+i).find(":selected").text();
		if($("#catmap-"+wooCategoryId+"_"+0).find(":selected").attr("id") <= 0){
			$("#category-"+wooCategoryId).val(0);
		}else{
			if(childCatvala > 0){
				$("#category-"+wooCategoryId).val(childCatvala);
        $("#category-name-"+wooCategoryId).val(childCatname);
			}
		}
	}
}
$( ".wizard-content" ).on( "click", ".change_prodct_feed_cat", function() {
 // console.log( $( this ).attr("data-id") );
  $(this).hide();
  var feed_select_cat_id = $( this ).attr("data-id");
  var woo_cat_id = $( this ).attr("data-cat-id");
  
  jQuery("#category-"+woo_cat_id).val("0");
  jQuery("#category-name-"+woo_cat_id).val("");
  jQuery("#label-"+feed_select_cat_id).hide();
  jQuery("#"+feed_select_cat_id).slideDown();
});
function changeProdctFeedCat(feed_select_cat_id){
  jQuery("#label-"+feed_select_cat_id).hide();
  jQuery("#"+feed_select_cat_id).slideDown();
}
</script>
  <?php 
  } //create_form
} ?>