<?php
$message = new Enhanced_Ecommerce_Google_Settings();
$obj = new Enhanced_Ecommerce_Google_Analytics_Admin($plugin_name = 'enhanced-e-commerce-for-woocommerce-store', $version = PLUGIN_TVC_VERSION);
$today = $obj->today();
$start = $obj->start_date();
$end = $obj->end_date();
$currentime = $obj->current_time();
$endtime = $obj->end_time();
?>

<style>
    td{
        text-align: center !important;
    }
    th{
        text-align: center !important;
    }
    .fa-times{
        color:red;
    }
    .fa-check{
        color:green;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class= "col col-9">
            <div class="card mw-100" style="padding:0;">
                <?php $message->show_message();?>
                <div class="card-header">
                    <h5>Feature difference between <span style="font-weight: 800;">Free & Premium</span> Plugin</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-10 my-4 mx-auto">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="table-responsve">
                                        <table class="table table-striped table-hover">
                                            <thead class="thead-inverse">
                                            <tr>
                                                <th class="w-25" style="font-weight: 900;">Features</th>
                                                <th class="" style="font-weight: 900;">Free</th>
                                                <th class="" style="font-weight: 900;">Premium</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td class="w-25 option">Basic UA Tracking (Pageviews)</td>
                                                <td><i class="fa fa-check"></i></td>
                                                <td><i class="fa fa-check"></i></td>
                                            </tr>
                                            <tr>
                                                <td class="option">Enhanced Ecommerce</td>
                                                <td><strong>Only 4 Reports</strong></td>
                                                <td><strong>All reports</strong></td>
                                            </tr>
                                            <tr>
                                                <td class="option">I.P. Anonymization</td>
                                                <td><i class="fa fa-check"></i></td>
                                                <td><i class="fa fa-check"></i></td>
                                            </tr>
                                            <tr>
                                                <td class="option">Google Opt-Out</td>
                                                <td><i class="fa fa-check"></i></td>
                                                <td><i class="fa fa-check"></i></td>
                                            </tr>
                                            <tr>
                                                <td class="option">Product List Performance</td>
                                                <td><i class="fa fa-times"></i></td>
                                                <td><i class="fa fa-check"></i></td>
                                            </tr>
                                            <tr>
                                                <td class="option">Display Feature</td>
                                                <td><i class="fa fa-times"></i></td>
                                                <td><i class="fa fa-check"></i></td>
                                            </tr>
                                            <tr>
                                                <td class="option">Product Variations (eg. color,size)</td>
                                                <td><i class="fa fa-times"></i></td>
                                                <td><i class="fa fa-check"></i></td>
                                            </tr>
                                            <tr>
                                                <td class="option">Automated Product Refund (from Admin Panel)</td>
                                                <td><i class="fa fa-times"></i></td>
                                                <td><i class="fa fa-check"></i></td>
                                            </tr>
                                            <tr>
                                                <td class="option">Google Adwords Conversion Tracking</td>
                                                <td><i class="fa fa-times"></i></td>
                                                <td><i class="fa fa-check"></i></td>
                                            </tr>
                                            <tr>
                                                <td class="option">Facebook Pixel Implementation (Standard Ecommerce Events)</td>
                                                <td><i class="fa fa-times"></i></td>
                                                <td><i class="fa fa-check"></i></td>
                                            </tr>
                                            <tr>
                                                <td class="option">Add Google Optimize Snippet</td>
                                                <td><i class="fa fa-times"></i></td>
                                                <td><i class="fa fa-check"></i></td>
                                            </tr>
                                            <tr>
                                                <td class="option">Site Speed Sample Rate</td>
                                                <td><i class="fa fa-times"></i></td>
                                                <td><i class="fa fa-check"></i></td>
                                            </tr>
                                            <tr>
                                                <td class="option">User ID Tracking</td>
                                                <td><i class="fa fa-times"></i></td>
                                                <td><i class="fa fa-check"></i></td>
                                            </tr>
                                            <tr>
                                                <td class="option">Form Field Tracking</td>
                                                <td><i class="fa fa-times"></i></td>
                                                <td><i class="fa fa-check"></i></td>
                                            </tr>
                                            <tr>
                                                <td class="option">Content Grouping</td>
                                                <td><i class="fa fa-times"></i></td>
                                                <td><i class="fa fa-check"></i></td>
                                            </tr>

                                            <tr>
                                                <td class="option">Internal Promotion</td>
                                                <td><i class="fa fa-times"></i></td>
                                                <td><i class="fa fa-check"></i></td>
                                            </tr>
                                            <tr>
                                                <td class="option">20 Ready to Use Custom Dimensions/ Metrics</td>
                                                <td><i class="fa fa-times"></i></td>
                                                <td><i class="fa fa-check"></i></td>
                                            </tr>
                                            <tr>
                                                <td class="option">Premium Support</td>
                                                <td><i class="fa fa-times"></i></td>
                                                <td><i class="fa fa-check"></i></td>
                                            </tr>
                                            <tr>
                                                <td class="option">Child / Custom Theme Support</td>
                                                <td><i class="fa fa-times"></i></td>
                                                <td><i class="fa fa-check"></i></td>
                                            </tr>
                                            <tr>
                                                <td class="option">Quick Expert Support ( Query Support)</td>
                                                <td><i class="fa fa-times"></i></td>
                                                <td><i class="fa fa-check"></i></td>
                                            </tr>
                                            <tr>
                                                <td class="option">Paid Customization (As per the requirement)</td>
                                                <td><i class="fa fa-times"></i></td>
                                                <td><i class="fa fa-check"></i></td>
                                            </tr>
                                            <tr>
                                                <td class="option">Data Studio Dashboards (Paid)</td>
                                                <td><i class="fa fa-times"></i></td>
                                                <td><i class="fa fa-check"></i></td>
                                            </tr>
                                            </tbody>
                                            <tfoot class="thead-inverse">
                                            <tr>
                                                <th class="w-25"></th>
                                                <th class="w-25"></th>
                                                <th class=""><a href="#" target="_blank"><button class="btn btn-primary"><strong>Get premium plugin</strong>
                                                <?php if($today >= $start && $today <= $end  && $currentime <= $endtime) {?>
                                                <img class="new-img-blink-side" src='<?php echo plugins_url('../images/discount.gif', __FILE__ )  ?>' />
                                                <?php }?>
                                                </button></a></th>
                                            </tr>
                                            </tfoot>
                                        </table>
                                        <p class="description" style="font-size: 15px;"><strong>Feel free to contact us regarding premium version inquiry at <span style="color:blue;font-size:15px;">analytics2@tatvic.com<span></strong>.<br/>Agencies & Marketers can also contact us for the bulk licenses for their clients.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                </div>
            </div>
        </div>
        <?php require_once('sidebar.php');?>
    </div>
</div>