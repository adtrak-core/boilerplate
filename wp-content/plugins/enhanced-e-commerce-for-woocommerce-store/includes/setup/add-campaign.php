<?php
class AddCampaign {
  public $response;
  public $post_data;
  protected $merchantId;
  protected $new_campaign;
  protected $TVC_Admin_Helper;

  public function __construct() {
    global $wpdb;
    $this->includes();
    $this->TVC_Admin_Helper = new TVC_Admin_Helper();
    $this->post_data = $_POST;    
    $this->merchantId = $this->TVC_Admin_Helper->get_merchantId();
    $this->currentCustomerId = $this->TVC_Admin_Helper->get_currentCustomerId(); 
    $this->html_run();      
  }
  public function includes() {
    if (!class_exists('ShoppingApi')) {
      require_once(__DIR__ . '/ShoppingApi.php');
    }
  }
  public function html_run() {
    $this->TVC_Admin_Helper->add_spinner_html();
    $this->create_form();
  }

  public function country_dropdown($selected_code = '', $is_disabled = false) {
    $getCountris = file_get_contents(__DIR__ . "/json/countries.json");
    $contData = json_decode($getCountris);
    $wooCountry = $this->TVC_Admin_Helper->get_woo_country();
    $is_disabled = ($is_disabled) ? "style='max-width:30rem;height:35px;pointer-events:none;background:#f2f2f2;'" : "style='max-width: 30rem;height: 35px;'";
    $data = "<select id='sales_country' name='sales_country' class='form-group col-md-6' readonly='true' $is_disabled onchange='selectCountry()'>";
    foreach ($contData as $key => $value) {
      $selected = ($value->code == $wooCountry) ? "selected='selected'" : "";
      $data .= "<option value=" . $value->code . " " . $selected . " >" . $value->name . "</option>";
    }
    $data .= "</select>";
    return $data;
  }

  public function create_form() {
    $campaigns_list = [];
    $categories = [];
    $campaign_performance = [];
    $account_performance = [];
    $product_performance = [];
    $product_partition_performance = [];
    $api_obj = new ShoppingApi();

    $campaigns_list_res = $api_obj->getCampaigns();
    if(isset($campaigns_list_res->errors) && !empty($campaigns_list_res->errors)){  
    }else if( isset($campaigns_list_res->data) ){
      $campaigns_list_res = $campaigns_list_res->data;
      if(isset($campaigns_list_res['status']) && $campaigns_list_res['status'] == 200){
        $campaigns_list = $campaigns_list_res['data'];
      }
    }

    $defaultCountry = $this->TVC_Admin_Helper->get_woo_country();
    $category_list = $api_obj->getCategories($defaultCountry);    
    if(isset($category_list->errors) && !empty($category_list->errors)){        
    }else{
      $category_list = isset($category_list->data) ? $category_list->data : [];
      if (isset($category_list['status']) && $category_list['status'] == 200) {
        $categories = $category_list['data'];
      }
    }

    $currency = $this->TVC_Admin_Helper->get_user_currency_symbol();
    if (isset($_GET['edit']) && $_GET['edit'] != '') {
      $campaign_details_res = $api_obj->getCampaignDetails($_GET['edit']);
      if (isset($campaign_details_res->errors) && !empty($campaign_details_res->errors)) {
        $error_code = array_keys($campaign_details_res->errors)[0];
        if($error_code == 404){
          $error_msg = 'Campaign details not found';
        }else{
          if (isset($campaign_details_res->error_data) && !empty($campaign_details_res->error_data)) {
            // $error_msg = array_values($campaign_details_res->error_data)[0]->errors[0];
          }
        }
        $class = 'notice notice-error';
        $message = esc_html__(isset($error_msg) ? $error_msg : 'There was some error fetching campaign details.', 'sample-text-domain');
        printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html('Error : ' . $message));
      } else {
        $campaign_details = $campaign_details_res->data;
        if ($campaign_details['status'] == 200) {
          $campaign_details = $campaign_details['data'];
        }
      }
    }

    if (isset($_POST['create_campaign'])) {
      $campaign_name = isset($_POST['campaign_name'])?$_POST['campaign_name']:"";
      $campaign_budget = isset($_POST['campaign_budget'])?$_POST['campaign_budget']:"";
      $sales_country = isset($_POST['sales_country'])?$_POST['sales_country']:"";
      $all_products = isset($_POST['all_products'])?$_POST['all_products']:"";
      $category_id = isset($_POST['dimension'])?$_POST['dimension']:"";
      $category_level = isset($_POST['category_level'])?$_POST['category_level']:"";

      $campaign = $api_obj->createCampaign($campaign_name, $campaign_budget, $sales_country, $all_products, $category_id, $category_level);
      if(isset($campaign->errors) && !empty($campaign->errors)){
        $class = 'notice notice-error';
        $message = esc_html__((isset($campaign->errors) && isset($campaign->errors[0])) ? $campaign->errors[0] : 'Error', 'sample-text-domain');
        printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html('Error : ' . $message));
      }else{
        $class = 'notice notice-success';
        $campaign_neme = isset($campaign->data)?'with Resource name '.$campaign->data:"";
        $message = esc_html__('Smart Shopping Campaign Created Successfully '.$campaign_neme, 'sample-text-domain');
        printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));            
      }
    }
    if (isset($_POST['update_campaign'])) {
      $campaign_name = isset($_POST['campaign_name'])?$_POST['campaign_name']:"";
      $campaign_budget = isset($_POST['campaign_budget'])?$_POST['campaign_budget']:"";
      $campaign_id = isset($_POST['campaign_id'])?$_POST['campaign_id']:"";
      $budget_id = isset($_POST['budget_id'])?$_POST['budget_id']:"";
      $sales_country = isset($_POST['sales_country'])?$_POST['sales_country']:"";
      $all_products = isset($_POST['all_products'])?$_POST['all_products']:"";
      $ad_group_id = isset($_POST['ad_group_id'])?$_POST['ad_group_id']:"";
      $ad_group_resource_name = isset($_POST['ad_group_resource_name'])?$_POST['ad_group_resource_name']:"";
      $category_id = isset($_POST['dimension']) ? $_POST['dimension']:"";
      $category_level = isset($_POST['category_level'])?$_POST['category_level']:"";

      $campaign = $api_obj->updateCampaign($campaign_name, $campaign_budget, $campaign_id, $budget_id, $sales_country, $all_products, $category_id, $category_level, $ad_group_id, $ad_group_resource_name);
      
      if (isset($campaign->errors) && !empty($campaign->errors)) {
        $class = 'notice notice-error';
        $message = esc_html__(isset($campaign->errors) ? $campaign->errors[0] : 'Error', 'sample-text-domain');
        printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html('Error : ' . $message));
      } else if(isset($campaign->data)){
        $campaign = $campaign->data;
        if ($campaign['status'] == 200) {
          $campaign_neme = isset($campaign['data'])?'with Resource name '.$campaign['data']:"";
          $class = 'notice notice-success';
          $message = esc_html__('Smart Shopping Campaign Updated Successfully ' . $campaign_neme, 'sample-text-domain');
          printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
          // $url = admin_url('admin.php?page=tvc-configuration-page');
          //wp_redirect($url);
        }
      }
      echo "<script>jQuery('#feed-spinner').css('display', 'none');</script>";
    } ?>

<div class="tab-content">
  <div class="tab-pane show active" id="googleShoppingFeed">
    <div class="tab-card">
      <div class="row">
        <div class="col-md-6 col-lg-8 edit-section">
          <div class="edit-header-section">           
            <script>
              var back_img = '<img src="<?php echo ENHANCAD_PLUGIN_URL.'/admin/images/icon/left-angle-arrow.svg'; ?>" alt="back"/>';
              document.write('<a href="' + document.referrer + '" class="back-btn">'+back_img+'<span>Back</span></a>');
            </script>
          </div>
        <?php
        if (!isset($_GET['edit'])) { ?>
          <form method="post" id="create-form">
            <div class="form-group">
              <h2 class="lead">Create a Smart Shopping Campaign to promote your products</h2>
              <p style="text-align:left; font-size: 14px;">A Smart Shopping campaign shows your products to potential customers across Google, Google Search Partners, the Google Display Network, YouTube, and Gmail.</p>
              <p style="text-align:left">
                <a href="https://support.google.com/google-ads/answer/7674739?hl=en" target="_blank">Learn more about Smart Shopping Campaigns.</a>
              </p>
            </div>
            <div class="form-row" style="margin-bottom: 0">
              <div class="col-md-12 row">
                <label for="campaign-name" class="form-group col-md-4 mt-2 text-left font-weight-bold">Campaign name: </label>
                <input type="text" class="form-group col-md-6" name="campaign_name" id="campaign-name" required>
              </div>
              <div class="col-md-12 row">
                <label for="campaign-budget" class="form-group col-md-4 mt-2 mb-0 text-left"><span class="font-weight-bold">Daily Campaign Budget (<?php echo  $currency ;?>):</span> <p style="text-align:left;font-size: 11px;">Only pay if someone clicks your ad. Recommended minimum budget of <?php echo  $currency ; ?>5 per day.</p></label>  
                <input type="number" class="form-group col-md-6" name="campaign_budget" id="campaign-budget" style="height: 35px;" required>    
              </div>
              <div class="col-md-12 row">
                <label for="sales-country" class="form-group col-md-4 mt-2 text-left"><span class="font-weight-bold">Target Country:</span> <p style="text-align:left;font-size: 11px;">If you want to target multiple countries, then create multiple campaigns.</p></label><?php echo  $this->country_dropdown(); ?>
              </div>
              <div class="col-md-12 row">
                <label for="campaign-products" class="form-group col-md-4 mt-2 text-left font-weight-bold">Products in campaign: </label>
                <label class="mt-2"><input type="radio" id="campaign-products" name="all_products" value="1" checked />Include all Merchant Center products</label>
              </div>
              <div class="col-md-12 row" style="display:none;">
                <div class="col-md-4"></div>
                <label class=""><input type="radio" id="campaign-product-partition" value="0" name="all_products" />Select products using product partition filters</label>
              </div>
              <div class="col-md-12 row" style="display:none;">
                <div class="col-md-4"></div>
                <label>Product Partition filter dimension:</label><b>  Category</b>
              </div>
              <div class="col-md-12 row" style="display:none;">
                <div class="col-md-4"></div>
                <label>Dimension Value:</label>
                <input type="hidden" name="category_level" id="category_level" value="" />
                <select class="col-md-3 ml-2" name="dimension" id="dimension" onchange="changeCategory()">
                  <?php
                  for ($i = 0; $i < count($categories); $i++) {
                      echo '<option value="' . $categories[$i]->id . '" level="' . $categories[$i]->level . '">' . $categories[$i]->name . '</option>';
                  } ?>
                </select>
              </div>
              <div class="col-md-12 row">
                  <label class="form-group col-md-12 mt-2 mb-0 text-left font-weight-bold">Campaign duration: </label>
                  <p class="ml-3" style="text-align:left; font-size: 14px;">Your campaign will run until you pause it. You can pause your campaign at any time, however it can take up to 30 days for google to optimize your products and ads.</p>
              </div>
            </div>
            <div class="col-12 row">
              <button onclick="showLoader()" type="submit" class="btn btn-primary" id="create_campaign" name="create_campaign">Create Smart Shopping Campaign</button>
            </div>
          </form>
          <hr>
          <form method="post">
            <div class="text-left">
              <p style="font-size: 14px;">Please note that campaigns will be created with accounts configured in previous steps.</p>
              <p style="font-size: 14px;"><span>Google Merchant Center : <?php echo  $this->merchantId; ?></span>&nbsp;&nbsp;&nbsp;&nbsp;<span>Google Ads Account Id : <?php echo $this->currentCustomerId; ?></span></p>
            </div>
          </form>
          <?php
          } else if (isset($_GET['edit']) && $_GET['edit'] != '') { ?>
          <form method="post">
            <div class="form-group">
              <h2 class="lead">Create/Update a Smart Shopping Campaign to promote your products</h2>
              <p style="text-align:left; font-size: 14px;">A Smart Shopping campaign shows your products to potential customers across Google, Google Search Partners, the Google Display Network, YouTube, and Gmail.</p>
              <p style="text-align:left">
                <a href="https://support.google.com/google-ads/answer/7674739?hl=en" target="_blank">Learn more about Smart Shopping Campaigns.</a>
              </p>
            </div>
            <div class="form-row" style="margin-bottom: 0">
              <div class="col-md-12 row">
                <label for="campaign-name" class="form-group col-md-4 mt-2 text-left font-weight-bold">Campaign name: </label>
                <input type="text" class="form-group col-md-6" name="campaign_name" value="<?php echo (isset($campaign_details) && $campaign_details != '')?$campaign_details->compaignName:""; ?>" id="campaign-name" required>
              </div>
              <div class="col-md-12 row">
                <label for="campaign-budget" class="form-group col-md-4 mt-2 mb-0 text-left"><span class="font-weight-bold">Daily Campaign Budget (<?php echo $currency; ?>):</span> <p style="text-align:left;font-size: 11px;">Only pay if someone clicks your ad. Recommended minimum budget of <?php echo $currency; ?>5 per day.</p></label>    
                <input type="number" class="form-group col-md-6" name="campaign_budget" id="campaign-budget" value="<?php echo (isset($campaign_details) && $campaign_details != '')?$campaign_details->dailyBudget:""; ?>" style="height: 35px;" required>    
              </div>
              <input type="hidden" name="campaign_id" value="<?php echo (isset($campaign_details) && $campaign_details != '')?$campaign_details->compaignId:""; ?>" />
              <input type="hidden" name="budget_id" value="<?php echo (isset($campaign_details) && $campaign_details != '')?$campaign_details->budgetId:""; ?>" />
              <input type="hidden" name="ad_group_id" value="<?php echo (isset($campaign_details) && $campaign_details != '')?$campaign_details->adGroupId:""; ?>" />
              <input type="hidden" name="ad_group_resource_name" value="<?php echo(isset($campaign_details) && $campaign_details != '')? $campaign_details->adGroupResourceName:""; ?>" />
              <div class="col-md-12 row">
                <label for="sales-country" class="form-group col-md-4 mt-2 text-left"><span class="font-weight-bold">Target Country:</span> <p style="text-align:left;font-size: 11px;">If you want to target multiple countries, then create multiple campaigns.</p></label>
                <?php echo $this->country_dropdown($defaultCountry, true); ?>
              </div>
              <div class="col-md-12 row">
                <label for="campaign-products" class="form-group col-md-4 mt-2 text-left font-weight-bold">Products in campaign: </label>
                <label class="mt-2">
                <?php
                if(isset($campaign_details) && $campaign_details->category_level > 0 && $campaign_details->category_id > 0){
                  echo '<input type="radio" id="campaign-products" name="all_products" value="1" />';
                }else{
                  echo '<input type="radio" id="campaign-products" name="all_products" value="1" checked />';
                } ?> Include all Merchant Center products</label>
              </div>
              <div class="col-md-12 row" style="display: none;">
                <div class="col-md-4"></div>
                <label class="">
                <?php
                if (isset($campaign_details) && $campaign_details->category_level > 0 && $campaign_details->category_id > 0) {
                  echo '<input type="radio" id="campaign-product-partition" value="0" name="all_products" checked />';
                } else {
                  echo '<input type="radio" id="campaign-product-partition" value="0" name="all_products" />';
                } ?> Select products using product partition filters</label>
              </div>
              <div class="col-md-12 row" style="display: none;">
                <div class="col-md-4"></div>
                <label>Product Partition filter dimension:</label><b>  Category</b>
              </div>
              <div class="col-md-12 row" style="display: none;">
                <div class="col-md-4"></div>
                <label>Dimension Value:</label>
                <input type="hidden" name="category_level" id="category_level" value=" <?php echo (isset($campaign_details) && $campaign_details != '')?$campaign_details->category_level:""; ?>" />
                <select class="col-md-3 ml-2" name="dimension" id="dimension" onchange="changeCategory()">
                <?php
                  if (isset($campaign_details) && $campaign_details != '') {
                    for ($i = 0; $i < count($categories); $i++) {
                      if ($campaign_details->category_id == $categories[$i]->id) {
                        echo '<option value="' . $categories[$i]->id . '" level="' . $categories[$i]->level . '" selected="selected">' . $categories[$i]->name . '</option>';
                      } else {
                        echo '<option value="' . $categories[$i]->id . '" level="' . $categories[$i]->level . '">' . $categories[$i]->name . '</option>';
                      }
                    }
                  }?>
                </select>
              </div>
              <div class="col-md-12 row">
                <label class="form-group col-md-12 mt-2 mb-0 text-left font-weight-bold">Campaign duration: </label>
                <p class="ml-3" style="text-align:left; font-size: 14px;">Your campaign will run until you pause it. You can pause your campaign at any time, however it can take up to 30 days for google to optimize your products and ads.</p>
              </div>
            </div>
            <div class="col-12 row">
                <button onclick="showLoader()" type="submit" class="btn btn-primary btn-success" id="update_campaign" name="update_campaign">Update Smart Shopping Campaign</button>
            </div>
          </form>
          <hr>
          <form method="post">
            <div class="text-left">
              <p style="font-size: 14px;">Please note that campaigns will be created with accounts configured in previous steps.</p>
              <p style="font-size: 14px;"><span>Google Merchant Center : <?php echo $this->merchantId; ?></span>&nbsp;&nbsp;&nbsp;&nbsp;<span>Google Ads Account Id : <?php echo $this->currentCustomerId; ?></span></p>
            </div>
        </form>
        <?php } ?>
        </div>
        <div class="col-md-6 col-lg-4">
          <?php echo get_tvc_help_html(); ?>
        </div>
      </div>
    </div>
  </div>
</div>
    
<script>
  $(document).ready(function() {
    $('.select2').select2();
  });
  jQuery('.create-merchant-center').addClass('active');
  jQuery('.create-google-ads').addClass('active');
  jQuery('.sync-products').addClass('active');
  jQuery('.create-smart-shopping').addClass('active');
  function changeCategory() {
    let dimension = jQuery('#dimension').val();
    let level = jQuery('#dimension').find('option[value='+dimension+']'). attr('level');
    jQuery('#category_level').val(level);
  }
  function selectCountry() {
    var sales_country = document.getElementById('sales_country').value;
    var customer_id = "<?php echo $this->currentCustomerId; ?>";
    jQuery('#feed-spinner').css('display', 'block');
    jQuery.post(
      myAjaxNonces.ajaxurl,
      {
          action: 'tvcajax-get-campaign-categories',
          countryCode: sales_country,
          customerId: customer_id,
          campaignCategoryListsNonce: myAjaxNonces.campaignCategoryListsNonce
      },
      function( response ) {
        
        let categories = JSON.parse(response);        
        // callback( tvc_validateResponse( response ) );
        jQuery('#dimension').html('');
        jQuery.each(categories, function(key, category) {   
          jQuery('#dimension').append(jQuery('<option value='+category.id+' level='+category.level+'>'+category.name+'</option>'));
        });
        jQuery('#feed-spinner').css('display', 'none');
      }
    );
  } 
  function showLoader() {
      if($('#campaign-name').val()!=''&&$('#campaign-budget').val()!=''){
          jQuery('#feed-spinner').css('display', 'block');
      }
  }
  </script>
  <?php
  }
  public function success_message($customerId) {
    $class = 'notice notice-success';
    $message = esc_html__($customerId . ' Set as default Ads Account ID.', 'sample-text-domain');
    printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
  }

  public function success_messageV2($campaign_name, $status) {
    $active = ($status == true) ? 'active' : 'inactive';
    $class = 'notice notice-success';
    //$message = esc_html__($campaign_name . ' Campaign Set as '.$active.'.', 'sample-text-domain');
    // return '<div class='.$class.'><p>'.$campaign_name.' Campaign Set as '.$active.'</p></div>';
    $message = $campaign_name . ' set as ' . $active;
    return $message;
  }

  public function success_deleteMessage($campaign_name) {
    //$active = ($status == true) ? 'active' : 'inactive';
    $class = 'notice notice-success';
    //$message = esc_html__($campaign_name . ' Campaign Set as '.$active.'.', 'sample-text-domain');
    // return '<div class='.$class.'><p>'.$campaign_name.' Campaign Set as '.$active.'</p></div>';
    $message = $campaign_name . ' is deleted successfully';
    return $message;
  }
}