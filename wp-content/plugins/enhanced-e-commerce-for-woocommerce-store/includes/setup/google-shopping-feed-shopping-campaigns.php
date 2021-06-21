<?php

class CampaignsConfiguration
{
  protected $merchantId;
  protected $currentCustomerId;
  protected $subscriptionId;

  protected $customApiObj;
  protected $currency_symbol;
  public function __construct(){
    $this->includes();
    $this->TVC_Admin_Helper = new TVC_Admin_Helper();
    $this->returnUrl = $_SERVER['REQUEST_URI'];

    $this->customApiObj = new CustomApi();

    $this->merchantId = $this->TVC_Admin_Helper->get_merchantId();
    $this->currentCustomerId = $this->TVC_Admin_Helper->get_currentCustomerId();    
    $this->subscriptionId = $this->TVC_Admin_Helper->get_subscriptionId();;
    $this->new_campaign = true;

    $this->date_range_type = (isset($_POST['customRadio']))  ? $_POST['customRadio'] : 1;
    $this->days = (isset($_POST['days']) && $_POST['days'] != '') ? $_POST['days'] : 7;
    $this->from_date = (isset($_POST['from_date']) && $_POST['from_date'] != '') ? $_POST['from_date'] : "";
    $this->to_date = (isset($_POST['to_date']) && $_POST['to_date'] != '') ? $_POST['to_date'] : "";
    $this->country = $this->TVC_Admin_Helper->get_woo_country();
    $this->currency_symbol = $this->TVC_Admin_Helper->get_user_currency_symbol();
    $this->site_url = "admin.php?page=enhanced-ecommerce-google-analytics-admin-display&tab="; 
    $this->html_run();
  }

  public function includes(){
    if (!class_exists('CustomApi.php')) {
      require_once(__DIR__ . '/CustomApi.php');
    }
    if (!class_exists('ShoppingApi')) {
      require_once(__DIR__ . '/ShoppingApi.php');
    }
    if (!class_exists('Tatvic_Category_Wrapper')) {
      require_once(__DIR__ . '/tatvic-category-wrapper.php');
    }
  }

  public function html_run(){
  	$this->TVC_Admin_Helper->add_spinner_html();
    $this->create_form();
  }

  public function create_form(){
    $googleDetail = [];
    $google_detail = $this->TVC_Admin_Helper->get_ee_options_data();
    if(isset($google_detail['setting'])){
      if ($google_detail['setting']) {
        $googleDetail = $google_detail['setting'];
      }
    }

    $date_range_type = isset($_POST['customRadio'])  ? $_POST['customRadio'] : 1;
		$days = (isset($_POST['days']) && $_POST['days'] != '') ? $_POST['days'] : 7;
		$from_date = (isset($_POST['from_date']) && $_POST['from_date'] != '') ? $_POST['from_date'] : "";
		$to_date = (isset($_POST['to_date']) && $_POST['to_date'] != '') ? $_POST['to_date'] : "";
    $campaigns_list = [];
    $categories = [];
    $campaign_performance = [];
    $account_performance = [];
    $product_performance = [];
    $product_partition_performance = [];
		$api_old_obj = new ShoppingApi();

    $campaigns_list_res = $api_old_obj->getCampaigns();

    if (isset($campaigns_list_res->errors) && !empty($campaigns_list_res->errors)) {
      $class = 'error';
      $message = esc_html__('Not any campaigns found.');
      printf('<div class="alert-message"><div class="%1$s" role="alert"><img src="'. plugins_url('img/missing-warning.svg', __FILE__) . '" alt="" class="mr-2"/>%2$s</div></div>', esc_attr($class), esc_html($message));
    } else if(isset($campaigns_list_res->data)){
      $campaigns_list_res = $campaigns_list_res->data;
      if ($campaigns_list_res['status'] == 200) {
        $campaigns_list = $campaigns_list_res['data'];
      }
    }
    $totalConversion = 0;
    $totalSale = 0;
    $totalCost = 0;
    $totalClick = 0;
		if(count($campaigns_list) > 0) {
		  //Account Performance
	    $account_performance_res = $api_old_obj->accountPerformance($this->date_range_type, $this->days, $this->from_date, $this->to_date);
	    if (isset($account_performance_res->errors) && !empty($account_performance_res->errors)) {
	    } else {
        $account_performance_res = $account_performance_res->data;
        if ($account_performance_res['status'] == 200) {
          $account_performance = $account_performance_res['data'];
        }
	    }
	    // Count account performance	    
	    if (!empty($account_performance->dailyConversions)) {
        foreach($account_performance->dailyConversions as $key => $dailyConversion){
            $totalConversion = $totalConversion + $dailyConversion->conversions;
        }
	    }	    
	    if (!empty($account_performance->dailySales)) {
        foreach ($account_performance->dailySales as $key => $dailySale) {
          $totalSale = $totalSale + $dailySale->sales;
        }
	    }	    
	    if (!empty($account_performance->dailyCost)) {
        foreach ($account_performance->dailyCost as $key => $dailyCostData) {
          $totalCost = $totalCost + $dailyCostData->costs;
        }
	    }
	   
	    if (!empty($account_performance->dailyClicks)) {
        foreach ($account_performance->dailyClicks as $key => $dailyClick) {
          $totalClick = $totalClick + $dailyClick->clicks;
        }
	    }
		  //Campaign Performance
		  $campaign_performance_res = $api_old_obj->campaignPerformance($this->date_range_type, $this->days, $this->from_date, $this->to_date);
	    if (isset($campaign_performance_res->errors) && !empty($campaign_performance_res->errors)) {

	    } else if(isset($campaign_performance_res->data)){
        $campaign_performance_res = $campaign_performance_res->data;
        if ($campaign_performance_res['status'] == 200) {
          $campaign_performance = $campaign_performance_res['data'];
        }
	    }
		}else if(isset($_GET['id']) && $_GET['id'] != '') {
			//Product Performance
			$product_performance_res = $api_old_obj->productPerformance($_GET['id'], $this->date_range_type, $this->days, $this->from_date, $this->to_date);

			if (isset($product_performance_res->errors) && !empty($product_performance_res->errors)) {

			} else if(isset($product_performance_res->data)){
		    $product_performance_res = $product_performance_res->data;
		    if ($product_performance_res['status'] == 200) {
		      $product_performance = $product_performance_res['data'];
		    }
			}

			//Product Partition Performance
			$product_partition_performance_res = $api_old_obj->productPartitionPerformance($_GET['id'], $this->date_range_type, $this->days, $this->from_date, $this->to_date);

			if (isset($product_partition_performance_res->errors) && !empty($product_partition_performance_res->errors)) {

			} else if(isset($product_partition_performance_res->data)){
		    $product_partition_performance_res = $product_partition_performance_res->data;
		    if ($product_partition_performance_res['status'] == 200) {
		        $product_partition_performance = $product_partition_performance_res['data'];
		    }
			}
		}
    $plan_id = 1;
    if(isset($googleDetail->plan_id) && !in_array($googleDetail->plan_id, array("1"))){
      $plan_id = $googleDetail->plan_id;
    }
    ?>
<div class="tab-content">
  <div class="tab-pane show active" id="googleShoppingFeed">
    <div class="tab-card">
      <div class="row">
        <div class="col-md-12">
          <div class="edit-section">
            <div class="edit-header-section">
              <script>
                var back_img = '<img src="<?php echo ENHANCAD_PLUGIN_URL.'/admin/images/icon/left-angle-arrow.svg'; ?>" alt="back"/>';
                document.write('<a href="' + document.referrer + '" class="back-btn">'+back_img+'<span>Back</span></a>');
              </script>
            </div>
            <div class="row">
              <div class="configuration-section col-md-6 col-lg-8">
                <?php echo get_google_shopping_tabs_html($this->site_url, (isset($googleDetail->google_merchant_center_id))?$googleDetail->google_merchant_center_id:""); ?>
              </div>
            </div>
            <div class="tab-content" id="myTabContent">
              <div class="tab-pane fade show active" id="smartShopping">
                <div class="smart-shopping-section">
                  <form method="post" id="date_range_form">
                    <input type="hidden" id="customRange2" name="customRadio" value="2" class="custom-control-input" checked="checked">
                    <div class="campaigns">
                      <div class="d-flex justify-content-between align-items-center">
                        <h3 class="title mb-0">Smart Shopping Campaigns</h3>
                        <div class="campaing-date">
                          <div class="input-group input-daterange">
                              <input type="text" class="form-control" id="from_date" name="from_date" value="<?php echo $this->from_date; ?>">
                              <div class="input-group-addon text px-3">to</div>
                              <input type="text" class="form-control" id="to_date" name="to_date" value="<?php echo $this->to_date; ?>">
                              <label class="mt-2 mb-2 error-msg float-left hidden" id="errorMessage">Please select both from and to date</label>
                          </div>
                        </div>
                        <div class="create-campaign-btn">
                            <button type="button" class="btn btn-outline-primary" onclick="validateAll()" id="select_range" name="select_range">Submit</button>
                            <a href="<?php echo $this->site_url.'add_campaign_page';?>" class="btn btn-outline-primary">Create a New Campaign</a>
                        </div>
                      </div>
                    </div>
                  </form>                
                  <div class="account-performance">
                    <div class="row">
                    	<div class="col-md-12">
                        <h3 class="title">Account Performance</h3>
                    	</div>
                    	<div class="col-md-6 col-lg-3">
                        <div class="chart">
                          <h4 class="sub-title">Daily Clicks</h4>
                          <canvas id="dailyClick" width="240" height="180"></canvas>
                        </div>
                    	</div>
                    	<div class="col-md-6 col-lg-3">
                        <div class="chart">
                          <h4 class="sub-title">Daily Cost</h4>
                          <canvas id="dailyCost" width="240" height="180"></canvas>
                        </div>
                    	</div>
                    	<div class="col-md-6 col-lg-3">
                        <div class="chart">
                          <h4 class="sub-title">Daily Conversions</h4>
                          <canvas id="dailyConversions" width="240" height="180"></canvas>
                        </div>
                    	</div>
                    	<div class="col-md-6 col-lg-3">
                        <div class="chart">
                          <h4 class="sub-title">Daily Sales</h4>
                          <canvas id="dailySales" width="240" height="180"></canvas>
                        </div>
                    	</div>
                    </div>
                  </div>
                </div>
                <div class="account-performance">
                  <div class="row">
                    <div class="col-lg-8">
                      <h3 class="title">Compaign Performance</h3>
                      <div class="table-section">
                        <table id="campaingPerformance" class="table dt-responsive nowrap" style="width:100%">
                          <thead>
                            <tr>
                              <th>Campaign</th>
                              <th width="100">Daily Budget</th>
                              <th class="text-center">Active</th>
                              <th class="text-center">Clicks</th>
                              <th class="text-center">Cost</th>
                              <th class="text-center">Conversions</th>
                              <th class="text-center">Sales</th>
                              <th class="text-center">Action</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php 
                            $total_campaigns = count($campaign_performance);
                            for ($i = 0; $i < $total_campaigns; $i++) {
                              $checked =  $campaign_performance[$i]->active == 0 ? '' : 'checked';
                              if ($campaign_performance[$i]->active != 2) {?>
                                <tr>
                                  <td><a href="<?php echo $this->site_url.'shopping_campaigns_page&id='.$campaign_performance[$i]->compaignId; ?>" class="text-underline"><?php echo $campaign_performance[$i]->compaignName; ?></a></td>
                                  <td><?php echo  $this->currency_symbol.$campaign_performance[$i]->dailyBudget; ?></td>
                                  <td class="text-center">
                                    <div class="custom-control custom-switch">
                                      <input type="checkbox" class="custom-control-input"  id="customSwitch<?php echo $i; ?>" <?php echo $checked; ?> onchange="updateCampaignStatus('<?php echo $this->merchantId; ?>','<?php echo $this->currentCustomerId; ?>','<?php echo $campaign_performance[$i]->compaignId; ?>','<?php echo $campaign_performance[$i]->dailyBudget; ?>','<?php echo $campaign_performance[$i]->budgetId; ?>','<?php echo$i; ?>')">
                                      <label class="custom-control-label" for="customSwitch<?php echo $i; ?>"></label>
                                    </div>
                                  </td>
                                  <td class="text-center"><?php echo $campaign_performance[$i]->clicks; ?></td>
                                  <td class="text-center"><?php echo $campaign_performance[$i]->cost; ?></td>
                                  <td class="text-center"><?php echo $campaign_performance[$i]->conversions; ?></td>
                                  <td class="text-center"><?php echo $campaign_performance[$i]->sales; ?></td>
                                    <input type="hidden" value="<?php echo  $campaign_performance[$i]->compaignName; ?>" id="campaign_name_<?php echo  $i; ?>" />
                                  <td><a href="<?php echo $this->site_url.'add_campaign_page&edit='.$campaign_performance[$i]->compaignId; ?>">Edit</a> | <a href="#" onclick="deleteCampaign('<?php echo $this->merchantId; ?>','<?php echo $this->currentCustomerId; ?>','<?php echo $campaign_performance[$i]->compaignId; ?>','<?php echo $i; ?>')">Delete</a></td>
                                </tr>
                              <?php
                              }				                
                            }?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                    <?php if($plan_id == 1){?>
                    <div class="col-lg-4">
                      <div class="pro-account ml-lg-auto">
                        <div class="card">
                          <div class="card-body">
                            <div class="account-img">
                              <img src="<?php echo ENHANCAD_PLUGIN_URL.'/admin/images/icon/undraw_update_uxn2.svg'; ?>" alt=""/>
                            </div>
                            <div class="pro-content">
                              <h3 class="userName">Hello <?php echo get_bloginfo(); ?>!</h3>
                              <p>Explore <a target="_blank" href="<?php echo $this->TVC_Admin_Helper->get_pro_plan_site(); ?>" class="font-weight-bold">Pro account</a> with Premium features.</p>
                            </div>
                          </div>
                          <div class="card-footer">
                            <a target="_blank" href="<?php echo $this->TVC_Admin_Helper->get_pro_plan_site().'pricings'; ?>"><button class="btn btn-primary">Learn more</button></a>
                          </div>
                        </div>
                      </div>
                    </div>
                  <?php } ?>
                  </div>
                </div><!-- account-performance-->
                <?php
                if(isset($_GET['id']) && $_GET['id'] != '') { ?>
                <div class="account-performance">
                  <div class="row">
                    <div class="col-lg-6">
                      <h3 class="title">Product Performance :</h3>
                      <div class="table-section">
                        <table id="productPerformance" class="table dt-responsive nowrap" style="width:100%">
                          <thead>
                              <tr>
                                  <th></th>
                                  <th>Product</th>
                                  <th class="text-center">Clicks</th>
                                  <th class="text-center">Cost</th>
                                  <th class="text-center">Conversions</th>
                                  <th class="text-center">Sales</th>
                              </tr>
                          </thead>
                          <tbody>
                          <?php for ($i = 0; $i < count($product_performance); $i++) { ?>
                            <tr>
                              <td class="product-image"><img src="<?php echo ENHANCAD_PLUGIN_URL.'/admin/images/icon/sneaker.jpg'; ?>" alt=""/></td>
                              <td><?php echo $product_performance[$i]->product; ?></td>
                              <td class="text-center"><?php echo $product_performance[$i]->clicks; ?></td>
                              <td class="text-center"><?php echo $product_performance[$i]->cost; ?></td>
                              <td class="text-center"><?php echo $product_performance[$i]->conversions; ?></td>
                              <td class="text-center"><?php echo $product_performance[$i]->sales; ?></td>
                            </tr>
                          <?php } ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div><!-- account-performance-->
                <div class="account-performance">
                  <div class="row">
                    <div class="col-lg-6">
                      <h3 class="title">Product Partition Performance</h3>
                      <div class="table-section">
                        <table id="productPerformance" class="table dt-responsive nowrap" style="width:100%">
                          <thead>
                            <tr>
                              <th>Campaign</th>
                              <th class="text-center">Product Dimension</th>
                              <th class="text-center">Product Dimension Value</th>
                              <th class="text-center">Clicks</th>
                              <th class="text-center">Cost</th>
                              <th class="text-center">Conversions</th>
                              <th class="text-center">Sales</th>
                            </tr>
                          </thead>
                          <tbody>
                          <?php for ($i = 0; $i < count($product_partition_performance); $i++) { ?>
                            <tr>
                              <td><a href="" class="text-underline"><?php echo $product_partition_performance[$i]->compaignName; ?></a></td>
                              <td class="text-center"><?php echo $product_partition_performance[$i]->productDimention; ?></td>
                              <td class="text-center"><?php echo $product_partition_performance[$i]->productDimentionValue; ?></td>
                              <td class="text-center"><?php echo $product_partition_performance[$i]->clicks; ?></td>
                              <td class="text-center"><?php echo $product_partition_performance[$i]->cost; ?></td>
                              <td class="text-center"><?php echo $product_partition_performance[$i]->conversions; ?></td>
                              <td class="text-center"><?php echo $product_partition_performance[$i]->sales; ?></td>
                            </tr>			                
                          <?php } ?>
                          </tbody>
                        </table>
                      </div>
             	      </div>
                  </div>
                </div><!-- account-performance -->
                <?php } ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
	    $(".select2").select2();
  });

	var ctx = document.getElementById('dailyClick').getContext('2d');
	var dailyClicksData = jQuery.parseJSON('<?php echo (isset($account_performance->dailyClicks)?json_encode($account_performance->dailyClicks):''); ?>');
   
  var labels = [];
  var values = [];
  if(dailyClicksData.length > 0 ){
  	dailyClicksData.forEach(clickData => {
    	labels.push(clickData.date);
    	values.push(clickData.clicks);
    })
  }
  var dailyClick = new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Clicks',
      	data: values,
        backgroundColor: [
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(255, 159, 64, 0.2)'
        ],
        borderColor: [
            'rgba(255, 99, 132, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)'
        ],
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        yAxes: [{
          ticks: {
            beginAtZero: true
          }
        }]
      }
    }
  });  
  
  var ctx = document.getElementById('dailyCost').getContext('2d');
  var dailyCostData = jQuery.parseJSON('<?php echo (isset($account_performance->dailyCost)?json_encode($account_performance->dailyCost):0); ?>');
  var labels = [];
  var values = [];
  if(dailyClicksData.length > 0 ){
  	dailyCostData.forEach(costData => {
      	labels.push(costData.date);
      	values.push(costData.costs);
  	})
  }
  var dailyClick = new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Cost',
      	data: values,
        backgroundColor: [
          'rgba(255, 99, 132, 0.2)',
          'rgba(54, 162, 235, 0.2)',
          'rgba(255, 206, 86, 0.2)',
          'rgba(75, 192, 192, 0.2)',
          'rgba(153, 102, 255, 0.2)',
          'rgba(255, 159, 64, 0.2)'
          ],
          borderColor: [
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(255, 159, 64, 0.2)'
          ],
          borderWidth: 1
      }]
    },
    options: {
      scales: {
        yAxes: [{
          ticks: {
            beginAtZero: true
          }
        }]
      }
    }
  });  
  
  var ctx = document.getElementById('dailyConversions').getContext('2d');
  var dailyConversionsData = jQuery.parseJSON('<?php echo (isset($account_performance->dailyConversions)?json_encode($account_performance->dailyConversions):0); ?>');
  
  var labels = [];
  var values = [];  
  if(dailyClicksData.length > 0 ){
  	dailyConversionsData.forEach(conversionsData => {
      	labels.push(conversionsData.date);
      	values.push(conversionsData.conversions);
  	})
  }

  var dailyClick = new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Conversions',
      	data: values,
        backgroundColor: [
          'rgba(255, 99, 132, 0.2)',
          'rgba(54, 162, 235, 0.2)',
          'rgba(255, 206, 86, 0.2)',
          'rgba(75, 192, 192, 0.2)',
          'rgba(153, 102, 255, 0.2)',
          'rgba(255, 159, 64, 0.2)'
        ],
        borderColor: [
          'rgba(255, 99, 132, 0.2)',
          'rgba(54, 162, 235, 0.2)',
          'rgba(255, 206, 86, 0.2)',
          'rgba(75, 192, 192, 0.2)',
          'rgba(153, 102, 255, 0.2)',
          'rgba(255, 159, 64, 0.2)'
        ],
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        yAxes: [{
          ticks: {
            beginAtZero: true
          }
        }]
      }
    }
  });  
  
  var ctx = document.getElementById('dailySales').getContext('2d');
	var dailySalesData = jQuery.parseJSON('<?php echo (isset($account_performance->dailySales)?json_encode($account_performance->dailySales):0); ?>');

	var labels = [];
	var values = [];
  if(dailyClicksData.length > 0 ){
  	dailySalesData.forEach(salesData => {
    	labels.push(salesData.date);
    	values.push(salesData.sales);
  	})
  }
  var dailyClick = new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Sales',
      	data: values,
        backgroundColor: [
          'rgba(255, 99, 132, 0.2)',
          'rgba(54, 162, 235, 0.2)',
          'rgba(255, 206, 86, 0.2)',
          'rgba(75, 192, 192, 0.2)',
          'rgba(153, 102, 255, 0.2)',
          'rgba(255, 159, 64, 0.2)'
        ],
        borderColor: [
          'rgba(255, 99, 132, 0.2)',
          'rgba(54, 162, 235, 0.2)',
          'rgba(255, 206, 86, 0.2)',
          'rgba(75, 192, 192, 0.2)',
          'rgba(153, 102, 255, 0.2)',
          'rgba(255, 159, 64, 0.2)'
        ],
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        yAxes: [{
          ticks: {
            beginAtZero: true
          }
        }]
      }
    }
  });

	function validateAll() {
		if ($("#customRange1").prop("checked")==true) {
			jQuery("#date_range_form").submit();
		}

		if ($("#customRange2").prop("checked")==true) {
      if(document.getElementById("from_date").value == "" || document.getElementById("to_date").value == "") {
        document.getElementById("errorMessage").classList.remove("hidden");
      } else {
        document.getElementById("errorMessage").classList.add("hidden");
        jQuery("#date_range_form").submit();
      }
		}
  }

  jQuery(".input-daterange input").each(function() {
  	jQuery(this).datepicker({
      todayHighlight: true,
      autoclose: true,
      defaultViewDate: new Date(),
      endDate: new Date(),
      format: "yyyy-mm-dd"
  	}).on("changeDate", changeStartDate);
  });

	function changeStartDate() {
  	var from_date = "";
  	var to_date = "";

  	from_date = jQuery("#from_date").val();
  	to_date = jQuery("#to_date").val();

  	jQuery("#from_date").datepicker("destroy").datepicker({
      todayHighlight: true,
      autoclose: true,
      defaultViewDate: new Date(),
      endDate: to_date == "" ? new Date() : to_date,
     	format: "yyyy-mm-dd"
  	});

  	jQuery("#to_date").datepicker("destroy").datepicker({
      todayHighlight: true,
      autoclose: true,
      defaultViewDate: new Date(),
      endDate: new Date(),
      startDate: from_date == "" ? "" : from_date,
     	format: "yyyy-mm-dd"
  	});
 	}

  function date_range_select() {
    document.getElementById("customRange1").checked = true;
  }

  function deleteCampaign(merchantId, customerId, campaignId, currentRow) {
		var confirm = window.confirm("Are you sure you want to delete campaign?");
		var campaign_name = jQuery("#campaign_name_"+currentRow).val();
		if(confirm) {
			jQuery("#feed-spinner").css("display", "block");
	  	jQuery.post(
	    	myAjaxNonces.ajaxurl,
		    {
	        action: "tvcajax-delete-campaign",
	        merchantId: merchantId,
	        customerId: customerId,
	        campaignId: campaignId,
	        campaignDeleteNonce: myAjaxNonces.campaignDeleteNonce
		    },
		    function( response ) {
		    	jQuery("#feed-spinner").css("display", "none");
          console.log(response);
          var rsp = JSON.parse(response)
          if (rsp.status == "success") {
            var message = campaign_name + " is deleted successfully";
            alert(message);
      			window.location.href = "<?php echo $this->site_url.'shopping_campaigns_page';?>";
      		} else {
      			var message = rsp.message;
              	alert(message);
      		}
		    }
      );
		}
	}

	function updateCampaignStatus(merchantId, customerId, campaignId, budget, budgetId, currentRow) {     
    var campaign_status = jQuery("#customSwitch"+currentRow).prop("checked");
    var campaign_name = jQuery("#campaign_name_"+currentRow).val();
   	jQuery("#feed-spinner").css("display", "block");
    jQuery.post(
      myAjaxNonces.ajaxurl,
      {
        action: "tvcajax-update-campaign-status",
        merchantId: merchantId,
        customerId: customerId,
        campaignId: campaignId,
        campaignName: campaign_name,
        budget: budget,
        budgetId: budgetId,
        status: campaign_status == true ? 2 : 3,
        campaignStatusNonce: myAjaxNonces.campaignStatusNonce
      },
      function( response ) {
      	jQuery("#feed-spinner").css("display", "none");
        console.log(response);
        var rsp = JSON.parse(response)
      	if(rsp.status == "success"){
          var message = campaign_name + " status updated successfully";
          alert(message);
          window.location.href = "<?php echo $this->site_url.'shopping_campaigns_page'; ?>";
  		  }else{
          var message = rsp.message;
          alert(message);
  		  }
      }
    );
	}
</script>
  <?php
  }
}
?>