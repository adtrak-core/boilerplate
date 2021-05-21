<?php
$obj = new Enhanced_Ecommerce_Google_Analytics_Admin($plugin_name = 'enhanced-e-commerce-for-woocommerce-store', $version = PLUGIN_NAME_VERSION);
$today = $obj->today();
$start = $obj->start_date();
$end = $obj->end_date();
$currentime = $obj->current_time();
$endtime = $obj->end_time();

?>
<div class="col col-xs-3">
    <div class="tvc_plugin_sidebar">
    <div class="card" style="padding: 0px;">
        <div class="card-header">
            <h5> Important Links</h5>
        </div>
        <div class="card-body">
            <ul>
                <li style="padding-bottom:5px;"><a href="http://plugins.tatvic.com/help-center/Installation-Manual.pdf" target="_blank">Installation Manual</a></li>
                <li style="padding-bottom:5px;"><a href="http://plugins.tatvic.com/help-center/Google-shopping-Guide.pdf" target="_blank">Google shopping guide</a></li>
                <li style="padding-bottom:5px;"><a href="https://wordpress.org/plugins/enhanced-e-commerce-for-woocommerce-store/faq/" target="_blank">FAQ</a></li>
                <li style="padding-bottom:5px;"><a href="https://www.tatvic.com/contact/?utm_source=TatvicEE&utm_medium=Dashboard&utm_campaign=WPlisting" target="_blank">Support</a></li>
                <li style="padding-bottom:5px;"><a href="https://www.tatvic.com/privacy-policy/?ref=plugin_policy&utm_source=plugin_backend&utm_medium=woo_free_plugin&utm_campaign=GDPR_complaince_ecomm_plugins" target="_blank">Privacy Policy</a></li>
                <li style="padding-bottom:5px;"><a href="https://wordpress.org/plugins/enhanced-e-commerce-for-woocommerce-store/#developers" target="_blank">Change Logs</a></li>
            </ul>
        </div>
    </div>
    <div class="card" style="padding: 0px;">
        <div class="card-header">
            <h5>Rate Us!</h5>
        </div>
        <div class="card-body">
            <ul>
                <li style="padding-bottom:5px;">Do you Like our Plugin? Please Spare few minutes to give <h3><a href = "https://wordpress.org/support/plugin/enhanced-e-commerce-for-woocommerce-store/reviews/" target="_blank" style="float: right">
                            <div class="rating">
                                <span>☆</span><span>☆</span><span>☆</span><span>☆</span><span>☆</span>
                            </div>
                        </a></h3> Rating..!!</li>
            </ul>
        </div>
    </div>
    <div class="card" style="padding: 0px;">
        <div class="card-header">
            <h5>Tatvic also Offers</h5>
        </div>
        <div class="card-body">
            <ul>
                <li style="padding-bottom:5px;"><img src='<?php echo plugins_url('../images/woo.png', __FILE__ )  ?>' />&nbsp;<a href="https://codecanyon.net/item/actionable-google-analytics-for-woocommerce/9899552?utm_source=TatvicEE&utm_medium=DashboardSide&utm_campaign=WPlisting
" target="_blank">Actionable Google Analytics for WooCommerce - Premium Version</a>
                    <?php if($today >= $start && $today <= $end  && $currentime <= $endtime) {?>
                        <img class="new-img-blink-side" src='<?php echo plugins_url('../images/discount.gif', __FILE__ )  ?>' /><?php } ?></li>
                <li style="padding-bottom:5px;"><img style="width:25px;height: 25px;" src='<?php echo plugins_url('../images/tatvic_logo.png', __FILE__ )  ?>' />&nbsp;<a href="https://codecanyon.net/item/google-feed-manager-for-woocommerce-by-tatvic/27104089?utm_source=TatvicEE&utm_medium=Side&utm_campaign=SideGMC" target="_blank">Google Feed Manager For WooCommerce</a></li>
                <li style="padding-bottom:5px;"><img src='<?php echo plugins_url('../images/m1.png', __FILE__ )  ?>' />&nbsp;<a href="https://1.envato.market/79Oky" target="_blank">Actionable Google Analytics for Magento</a></li>
                <li style="padding-bottom:5px;"><img src='<?php echo plugins_url('../images/m2.png', __FILE__ )  ?>' />&nbsp;<a href="https://marketplace.magento.com/tatvic-actionablegoogleanalytics.html" target="_blank">Actionable Google Analytics for Magento2</a></li>
                <li style="padding-bottom:5px;"><img src='<?php echo plugins_url('../images/shopify_new.png', __FILE__ )  ?>' />&nbsp;<a href="https://apps.shopify.com/google-universal-analytics-enhanced-ecommerce" target="_blank">Actionable Google Analytics for Shopify</a></li>
                <li style="padding-bottom:5px;"><img style="width:25px;height: 25px;" src='<?php echo plugins_url('../images/tatvic_logo.png', __FILE__ )  ?>' />&nbsp;<a href="https://www.tatvic.com/excel-add-in-google-analytics/?utm_source=excel_tool_signup&utm_medium=woo_free_plugin&utm_campaign=excel_free_trial" target="_blank">Tatvic Ninja Excel AddIn</a></li>
            </ul>
        </div>
    </div>
  </div>
</div>