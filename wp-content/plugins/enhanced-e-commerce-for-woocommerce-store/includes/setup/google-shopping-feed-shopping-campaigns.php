<?php

class CampaignsConfiguration
{
    public $merchantId;

    public $customApiObj;

    public function __construct()
    {
    	$this->includes();
        $this->returnUrl = $_SERVER['REQUEST_URI'];
    	$this->url = "https://".TVC_AUTH_CONNECT_URL."/config/ga_rdr_gmc.php?return_url=" . $_SERVER['HTTP_HOST'] . $this->returnUrl;

        $this->customApiObj = new CustomApi();
    	//$this->customApiObj->getGoogleAnalyticDetail();

        $this->merchantId = (isset($GLOBALS['tatvicData']['tvc_merchant'])) ? $GLOBALS['tatvicData']['tvc_merchant']:"";
        $this->currentCustomerId = (isset($GLOBALS['tatvicData']['tvc_customer'])) ? $GLOBALS['tatvicData']['tvc_customer']:"";
        
        $this->subscriptionId = (isset($GLOBALS['tatvicData']['tvc_subscription'])) ? $GLOBALS['tatvicData']['tvc_subscription']:"";
        $this->new_campaign = true;
        $this->date_range_type = isset($_POST['customRadio'])  ? $_POST['customRadio'] : 1;
        $this->days = (isset($_POST['days']) && $_POST['days'] != '') ? $_POST['days'] : 7;
        $this->from_date = (isset($_POST['from_date']) && $_POST['from_date'] != '') ? $_POST['from_date'] : "";
        $this->to_date = (isset($_POST['to_date']) && $_POST['to_date'] != '') ? $_POST['to_date'] : "";
        $this->country = (isset($GLOBALS['tatvicData']['tvc_country'])) ? $GLOBALS['tatvicData']['tvc_country']:"";
        $this->site_url = "admin.php?page=enhanced-ecommerce-google-analytics-admin-display&tab=";
        
    	
        $this->html_run();
    }

    public function includes()
    {
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

    public function html_run()
    {
    	$this->spinner();
        $this->create_form();
    }

    public function spinner()
    {
        $spinner_gif = ENHANCAD_PLUGIN_URL . '/admin/images/ajax-loader.gif';
        echo '<div class="feed-spinner" id="feed-spinner" style="display:none;">
				<img id="img-spinner" src="' . $spinner_gif . '" alt="Loading" />
			</div>';
    }

    public function create_form()
    {

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
			$class = 'notice notice-error';
	        $message = esc_html__('Not any campaigns found.');
	        printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
        } else {
         
            $campaigns_list_res = $campaigns_list_res->data;

            if ($campaigns_list_res['status'] == 200) {
                $campaigns_list = $campaigns_list_res['data'];
            }
        }

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
		    $totalConversion = 0;
		    if (!empty($account_performance->dailyConversions)) {
		        foreach ($account_performance->dailyConversions as $key => $dailyConversion) {
		            $totalConversion = $totalConversion + $dailyConversion->conversions;
		        }
		    }

		    $totalSale = 0;
		    if (!empty($account_performance->dailySales)) {
		        foreach ($account_performance->dailySales as $key => $dailySale) {
		            $totalSale = $totalSale + $dailySale->sales;
		        }
		    }

		    $totalCost = 0;
		    if (!empty($account_performance->dailyCost)) {
		        foreach ($account_performance->dailyCost as $key => $dailyCostData) {
		            $totalCost = $totalCost + $dailyCostData->costs;
		        }
		    }

		    $totalClick = 0;
		    if (!empty($account_performance->dailyClicks)) {
		        foreach ($account_performance->dailyClicks as $key => $dailyClick) {
		            $totalClick = $totalClick + $dailyClick->clicks;
		        }
		    }

		    //Campaign Performance
		    $campaign_performance_res = $api_old_obj->campaignPerformance($this->date_range_type, $this->days, $this->from_date, $this->to_date);

		    if (isset($campaign_performance_res->errors) && !empty($campaign_performance_res->errors)) {

		    } else {
		        $campaign_performance_res = $campaign_performance_res->data;
		        if ($campaign_performance_res['status'] == 200) {
		            $campaign_performance = $campaign_performance_res['data'];
		        }
		    }
		}else if(isset($_GET['id']) && $_GET['id'] != '') {
			//Product Performance
			$product_performance_res = $api_old_obj->productPerformance($_GET['id'], $this->date_range_type, $this->days, $this->from_date, $this->to_date);

			if (isset($product_performance_res->errors) && !empty($product_performance_res->errors)) {

			} else {
			    $product_performance_res = $product_performance_res->data;
			    if ($product_performance_res['status'] == 200) {
			        $product_performance = $product_performance_res['data'];
			    }
			}

			//Product Partition Performance
			$product_partition_performance_res = $api_old_obj->productPartitionPerformance($_GET['id'], $this->date_range_type, $this->days, $this->from_date, $this->to_date);

			if (isset($product_partition_performance_res->errors) && !empty($product_partition_performance_res->errors)) {

			} else {
			    $product_partition_performance_res = $product_partition_performance_res->data;
			    if ($product_partition_performance_res['status'] == 200) {
			        $product_partition_performance = $product_partition_performance_res['data'];
			    }
			}
		}

		$google_detail = $this->customApiObj->getGoogleAnalyticDetail();
        if (isset($google_detail->data['status']) && $google_detail->data['status'] == 200) {
            if (isset($google_detail->data['data'])) {
                $googleDetail = $google_detail->data['data'];
            }
        } else {
            $googleDetail = [];
            $class = 'notice notice-error';
            $message = esc_html__('Google analytic detail is empty.');
            printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
        }

    	echo '<div class="container-fluid">
	<div class="row">
		<div class= "col col-12">
			<div class="card mw-100" style="padding:0;">
				<div class="card-body">
	                <div class="tab-pane show active" id="googleShoppingFeed">
	                    <div class="row">
	                        <div class="col-md-6 col-lg-8 border-right">
	                            <div class="configuration-section" id="config-pt1">
	                               '.get_google_shopping_tabs_html($this->site_url,(isset($googleDetail->google_merchant_center_id))?$googleDetail->google_merchant_center_id:"").'
	                            </div>
	                            <div class="mt-3" id="config-pt2">
                                	<form method="post" id="date_range_form">
                                    <div class="campaigns" id="camp">
									    <div class="row">
									      	<div class="col-md-5">
									        	<h3 class="title">Smart Shopping Campaigns</h3>
									      	</div>
									  		<div class="col-md-7">
									        	<div class="campaign-date">
									          		<h4 class="sub-title">Date Range:</h4>
										          	<div class="radio-buttons">
										            	<div class="custom-control custom-radio form-group">
										              		<input type="radio" id="customRange1" name="customRadio" value="1" class="custom-control-input"'; echo $this->date_range_type == 1 ? "checked" : ""; echo ' >
										              		<label class="custom-control-label" for="customRange1">
										                		<select name="days" class="form-control select2" onchange="date_range_select()">
										                  			<option value="7" '; echo $this->days == 7 ? "selected" : "active"; echo '>Last 7 Days</option>
																	<option value="14" '; echo $this->days == 14 ? "selected" : "active"; echo '>Last 14 days</option>
																	<option value="30" '; echo $this->days == 30 ? "selected" : "active"; echo '>Last 30 days</option>
																	<option value="last_month" '; echo $this->days == "last_month" ? "selected" : "active"; echo '>Last month</option>
										              				<option value="this_month" '; echo $this->days == "this_month" ? "selected" : "active"; echo '>This month</option>
										                		</select>
										              		</label>
										            	</div>
										            	<div class="custom-control custom-radio form-group mb-0">
										              		<input type="radio" id="customRange2" name="customRadio" value="2" class="custom-control-input"'; echo $this->date_range_type == 2 ? "checked" : ""; echo ' >
										              		<label class="custom-control-label" for="customRange2">
										                		<div class="input-group input-daterange">
										                  			<div class="input-group-addon pr-3 text">From</div>
										                  			<input type="text" class="form-control" id="from_date" name="from_date" value="'.$this->from_date.'">
										                  			<div class="input-group-addon text px-3">to</div>
										                  			<input type="text" class="form-control" id="to_date" name="to_date" value="'.$this->to_date.'">
										              			</div>
										              		</label>
										            	</div>
										          	</div>
									          		<label class="mt-2 mb-2 error-msg float-left hidden" id="errorMessage">Please select both from and to date</label>
									          		<button type="button" class="btn btn-primary btn-success btn-sm" onclick="validateAll()" id="select_range" name="select_range">Submit</button>
									        		</div>
									      		</div>
									    	</div>
										</div>
										</form>';
                                    	if(!isset($_GET['id'])) {
                            	   echo '<div class="account-performance">
                                            <div class="row">
                                              	<div class="col-md-12">
	                                                <h3 class="title">Account Performance</h3>
                                              	</div>
                                              	<div class="col-md-6">
	                                                <div class="chart">
	                                                  	<h4 class="sub-title">Daily Clicks</h4>
	                                                  	<canvas id="dailyClick" width="400" height="400"></canvas>
	                                                </div>
                                              	</div>
                                              	<div class="col-md-6">
	                                                <div class="chart">
	                                                  	<h4 class="sub-title">Daily Cost</h4>
	                                                  	<canvas id="dailyCost" width="400" height="400"></canvas>
	                                                </div>
                                              	</div>
                                              	<div class="col-md-6">
	                                                <div class="chart">
	                                                  	<h4 class="sub-title">Daily Conversions</h4>
	                                                  	<canvas id="dailyConversions" width="400" height="400"></canvas>
	                                                </div>
                                              	</div>
                                              	<div class="col-md-6">
	                                                <div class="chart">
	                                                  	<h4 class="sub-title">Daily Sales</h4>
	                                                  	<canvas id="dailySales" width="400" height="400"></canvas>
	                                                </div>
                                              	</div>
                                            </div>
                                        </div>
                                        <div class="account-performance">
                                            <div class="row">
                                              	<div class="col-md-12">
                                                	<h3 class="title">Campaign Performance</h3>
                                              	</div>
                                              	<div class="col-md-12">
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
	                                                    	<tbody>';
	                                                    	$total_campaigns = count($campaign_performance);
											                for ($i = 0; $i < $total_campaigns; $i++) {
											                    $checked =  $campaign_performance[$i]->active == 0 ? '' : 'checked';
											                    if ($campaign_performance[$i]->active != 2) {

										                  echo '<tr>
	                                                            	<td><a href="' . admin_url('admin.php?page=enhanced-ecommerce-google-analytics-admin-display&tab=shopping_campaigns_page&id=' . $campaign_performance[$i]->compaignId) . '" class="text-underline">'. $campaign_performance[$i]->compaignName .'</a></td>
	                                                            	<td><input type="text" class="form-control" value="$' . $campaign_performance[$i]->dailyBudget . '"></td>
	                                                            	<td class="text-center">
	                                                              	<div class="custom-control custom-switch">
																		<input type="checkbox" class="custom-control-input"  id="customSwitch'.$i.'" '.$checked.' onchange="updateCampaignStatus('.$this->merchantId.','.$this->currentCustomerId.','.$campaign_performance[$i]->compaignId.','.$campaign_performance[$i]->dailyBudget.','.$campaign_performance[$i]->budgetId.','.$i.')">
                                                               			<label class="custom-control-label" for="customSwitch'.$i.'"></label>
	                                                              	</div>
	                                                            	</td>
	                                                            	<td class="text-center">' . $campaign_performance[$i]->clicks . '</td>
	                                                            	<td class="text-center">' . $campaign_performance[$i]->cost . '</td>
	                                                            	<td class="text-center">' . $campaign_performance[$i]->conversions . '</td>
	                                                            	<td class="text-center">' . $campaign_performance[$i]->sales . '</td>
	                                                            	<input type="hidden" value="'.$campaign_performance[$i]->compaignName.'" id="campaign_name_'.$i.'" />
	                                                        		<td><a href="' . admin_url('admin.php?page=enhanced-ecommerce-google-analytics-admin-display&tab=add_campaign_page&edit=' . $campaign_performance[$i]->compaignId) . '">Edit</a> <a href="#" onclick="deleteCampaign('.$this->merchantId.','.$this->currentCustomerId.','.$campaign_performance[$i]->compaignId.','.$i.')">Delete</a></td>
	                                                        	</tr>';
	                                                        	}
											                }
											                
	                                                    echo '</tbody>
	                                                	</table>
	                                                </div>
                                              </div>
                                            </div>
                                        </div>';
                                    	}
                                    	if(isset($_GET['id']) && $_GET['id'] != '') {
                                   echo '<div class="account-performance">
                                            <div class="row">
                                              	<div class="col-md-12">
	                                                <h3 class="title">Product Performance</h3>
                                              	</div>
                                              	<div class="col-md-12">
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
	                                                    	<tbody>';

                                    	                	for ($i = 0; $i < count($product_performance); $i++) {
										                  echo '<tr>
											                      	<td class="product-image">
											                      		<img src="'.plugins_url('img/sneaker.jpg', __FILE__ ).'" alt=""/></td>
											                      	<td>' . $product_performance[$i]->product . '</td>
											                      	<td class="text-center">' . $product_performance[$i]->clicks . '</td>
											                      	<td class="text-center">' . $product_performance[$i]->cost . '</td>
											                      	<td class="text-center">' . $product_performance[$i]->conversions . '</td>
											                      	<td class="text-center">' . $product_performance[$i]->sales . '</td>
											                  	</tr>';
											                }
	                                                   echo '</tbody>
	                                                	</table>
	                                                </div>
                                              	</div>
                                            </div>
                                        </div>
                                        <div class="account-performance">
                                            <div class="row">
                                              	<div class="col-md-12">
	                                                <h3 class="title">Product Partition Performance</h3>
                                              	</div>
                                              	<div class="col-md-12">
	                                                <div class="table-section">
	                                                  	<table id="partitionPerformance" class="table dt-responsive nowrap" style="width:100%">
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
	                                                    	<tbody>';
                                        	                for ($i = 0; $i < count($product_partition_performance); $i++) {
											              echo '<tr>
											              	        <td><a href="" class="text-underline">' . $product_partition_performance[$i]->compaignName . '</a></td>
										                	      	<td class="text-center">' . $product_partition_performance[$i]->productDimention . '</td>
											                      	<td class="text-center">' . $product_partition_performance[$i]->productDimentionValue . '</td>
											                      	<td class="text-center">' . $product_partition_performance[$i]->clicks . '</td>
											                      	<td class="text-center">' . $product_partition_performance[$i]->cost . '</td>
											                      	<td class="text-center">' . $product_partition_performance[$i]->conversions . '</td>
											                      	<td class="text-center">' . $product_partition_performance[$i]->sales . '</td>
											                  	</tr>';
											                }
	                                                    echo'</tbody>
	                                                	</table>
	                                                </div>
                                             	</div>
                                            </div>
                                        </div>';
	                                    }

                            		echo'<div class="text-left">
                                    		<a href="admin.php?page=enhanced-ecommerce-google-analytics-admin-display&tab=add_campaign_page" class="btn btn-primary btn-success">Create Smart Shopping Campaignn</a>
                                    	</div>
                            		</div>
                                </div>
                            
	                        <div class="col-md-6 col-lg-4">
	                            <div class="right-content">
	                               '.get_tvc_help_html().'
	                            </div>
	                        </div>
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

	var ctx = document.getElementById(\'dailyClick\').getContext(\'2d\');

	var dailyClicksData = '. (isset($account_performance->dailyClicks)?json_encode($account_performance->dailyClicks):0) .' ;
  
  	var labels = [];
  	var values = [];

	dailyClicksData.forEach(clickData => {
      	labels.push(clickData.date);
      	values.push(clickData.clicks);
  	})

    var dailyClick = new Chart(ctx, {
        type: \'line\',
        data: {
            labels: labels,
            datasets: [{
                label: \'Clicks\',
              	data: values,
                backgroundColor: [
                    \'rgba(255, 99, 132, 0.2)\',
                    \'rgba(54, 162, 235, 0.2)\',
                    \'rgba(255, 206, 86, 0.2)\',
                    \'rgba(75, 192, 192, 0.2)\',
                    \'rgba(153, 102, 255, 0.2)\',
                    \'rgba(255, 159, 64, 0.2)\'
                ],
                borderColor: [
                    \'rgba(255, 99, 132, 1)\',
                    \'rgba(54, 162, 235, 1)\',
                    \'rgba(255, 206, 86, 1)\',
                    \'rgba(75, 192, 192, 1)\',
                    \'rgba(153, 102, 255, 1)\',
                    \'rgba(255, 159, 64, 1)\'
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
  
  
    var ctx = document.getElementById(\'dailyCost\').getContext(\'2d\');
  	var dailyCostData = '. (isset($account_performance->dailyCost)?json_encode($account_performance->dailyCost):0) .' ;
  
  	var labels = [];
  	var values = [];
  
  	dailyCostData.forEach(costData => {
      	labels.push(costData.date);
      	values.push(costData.costs);
  	})
    var dailyClick = new Chart(ctx, {
        type: \'line\',
        data: {
            labels: labels,
            datasets: [{
                label: \'Cost\',
              	data: values,
                backgroundColor: [
                    \'rgba(255, 99, 132, 0.2)\',
                    \'rgba(54, 162, 235, 0.2)\',
                    \'rgba(255, 206, 86, 0.2)\',
                    \'rgba(75, 192, 192, 0.2)\',
                    \'rgba(153, 102, 255, 0.2)\',
                    \'rgba(255, 159, 64, 0.2)\'
                ],
                borderColor: [
                    \'rgba(255, 99, 132, 1)\',
                    \'rgba(54, 162, 235, 1)\',
                    \'rgba(255, 206, 86, 1)\',
                    \'rgba(75, 192, 192, 1)\',
                    \'rgba(153, 102, 255, 1)\',
                    \'rgba(255, 159, 64, 1)\'
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
  
  
    var ctx = document.getElementById(\'dailyConversions\').getContext(\'2d\');
  	var dailyConversionsData = '. (isset($account_performance->dailyConversions)?json_encode($account_performance->dailyConversions):0) .' ;
  
  	var labels = [];
  	var values = [];
  
  	dailyConversionsData.forEach(conversionsData => {
      	labels.push(conversionsData.date);
      	values.push(conversionsData.conversions);
  	})

    var dailyClick = new Chart(ctx, {
        type: \'line\',
        data: {
            labels: labels,
            datasets: [{
                label: \'Conversions\',
              	data: values,
                backgroundColor: [
                    \'rgba(255, 99, 132, 0.2)\',
                    \'rgba(54, 162, 235, 0.2)\',
                    \'rgba(255, 206, 86, 0.2)\',
                    \'rgba(75, 192, 192, 0.2)\',
                    \'rgba(153, 102, 255, 0.2)\',
                    \'rgba(255, 159, 64, 0.2)\'
                ],
                borderColor: [
                    \'rgba(255, 99, 132, 1)\',
                    \'rgba(54, 162, 235, 1)\',
                    \'rgba(255, 206, 86, 1)\',
                    \'rgba(75, 192, 192, 1)\',
                    \'rgba(153, 102, 255, 1)\',
                    \'rgba(255, 159, 64, 1)\'
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
  
  
    var ctx = document.getElementById(\'dailySales\').getContext(\'2d\');
  	var dailySalesData = '. (isset($account_performance->dailySales)?json_encode($account_performance->dailySales):0) .' ;

  	var labels = [];
  	var values = [];

  	dailySalesData.forEach(salesData => {
      	labels.push(salesData.date);
      	values.push(salesData.sales);
  	})
    var dailyClick = new Chart(ctx, {
        type: \'line\',
        data: {
            labels: labels,
            datasets: [{
                label: \'Sales\',
              	data: values,
                backgroundColor: [
                    \'rgba(255, 99, 132, 0.2)\',
                    \'rgba(54, 162, 235, 0.2)\',
                    \'rgba(255, 206, 86, 0.2)\',
                    \'rgba(75, 192, 192, 0.2)\',
                    \'rgba(153, 102, 255, 0.2)\',
                    \'rgba(255, 159, 64, 0.2)\'
                ],
                borderColor: [
                    \'rgba(255, 99, 132, 1)\',
                    \'rgba(54, 162, 235, 1)\',
                    \'rgba(255, 206, 86, 1)\',
                    \'rgba(75, 192, 192, 1)\',
                    \'rgba(153, 102, 255, 1)\',
                    \'rgba(255, 159, 64, 1)\'
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
			        campaignDeleteNonce: myAjaxNonces.campaignDeleteNonce,
			    },
			    function( response ) {
			    	jQuery("#feed-spinner").css("display", "none");
			        console.log(response);
			        var rsp = JSON.parse(response)
	            	if (rsp.status == "success") {
	                	var message = campaign_name + " is deleted successfully";
	                	alert(message);
	        			window.location.href = "admin.php?page=enhanced-ecommerce-google-analytics-admin-display&tab=shopping_campaigns_page";
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
                campaignStatusNonce: myAjaxNonces.campaignStatusNonce,
    
            },
            function( response ) {
            	jQuery("#feed-spinner").css("display", "none");
                console.log(response);
                var rsp = JSON.parse(response)
            	if (rsp.status == "success") {
                	var message = campaign_name + " status updated successfully";
                	alert(message);
                	window.location.href = "admin.php?page=enhanced-ecommerce-google-analytics-admin-display&tab=shopping_campaigns_page";
        		} else {
        			var message = rsp.message;
                	alert(message);
        		}
            }
        );
	}
</script>';
    }
}
?>