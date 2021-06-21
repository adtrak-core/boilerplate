<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       tatvic.com
 * @since      1.0.0
 *
 * @package    Enhanced_Ecommerce_Google_Analytics
 * @subpackage Enhanced_Ecommerce_Google_Analytics/admin/partials
 *
 */
if (!defined('ABSPATH')) {
    exit;
}
class TVC_Tabs {
  protected $TVC_Admin_Helper;
  protected $site_url;
  public function __construct() {
      $this->TVC_Admin_Helper = new TVC_Admin_Helper();
      $this->site_url = "admin.php?page=enhanced-ecommerce-google-analytics-admin-display&tab=";
      
      $this->create_tabs();
  }    
  protected function info_htnml($validation){
      if($validation == true){
          return '<img src="'.ENHANCAD_PLUGIN_URL.'/admin/images/config-success.svg" alt="configuration  success" class="config-success">';
      }else{
          return '<img src="'.ENHANCAD_PLUGIN_URL.'/admin/images/exclaimation.png" alt="configuration  success" class="config-fail">';
      }
  }
  /* add active tab class */
  protected function is_active_tabs($tab_name=""){
      if($tab_name!="" && isset($_GET['tab']) && $_GET['tab'] == $tab_name){
          return "active";
      }
      return ;
  }
  
  protected function create_tabs(){
    $setting_status = $this->TVC_Admin_Helper->check_setting_status();
    ?>
      <ul class="nav nav-pills nav-justified">
        <li class="nav-item">       
          <div class="tvc-tooltip <?php echo (empty($_GET['tab']))?'active':$this->is_active_tabs('general_settings'); ?>">
                <?php if(isset($setting_status['google_analytic']) && $setting_status['google_analytic'] == false ){?>
                <?php echo (isset($setting_status['google_analytic_msg'])?'<span class="tvc-tooltiptext tvc-tooltip-right">'.$setting_status['google_analytic_msg'].'</span>':"") ?>
                <?php }?>
            <div class="nav-item nav-link <?php echo (empty($_GET['tab']))?'active':$this->is_active_tabs('general_settings'); ?>">
                <?php if(isset($setting_status['google_analytic']) ){
                    echo $this->info_htnml($setting_status['google_analytic']);
                }?>
                <a  href="<?php echo $this->site_url.'general_settings'; ?>"  class=""> Google Analytics</a>
            </div>
          </div>
        </li>
        <li class="nav-item">
            <div class="tvc-tooltip <?php echo $this->is_active_tabs('google_ads'); ?>">
                <?php if(isset($setting_status['google_ads']) && $setting_status['google_ads'] == false ){?>
                <?php echo (isset($setting_status['google_ads'])?'<span class="tvc-tooltiptext tvc-tooltip-right">'.$setting_status['google_ads_msg'].'</span>':"") ?>
                <?php } ?>
                <div class="nav-link <?php echo $this->is_active_tabs('google_ads'); ?>">
                <?php if(isset($setting_status['google_ads']) ){
                    echo $this->info_htnml($setting_status['google_ads']);
                }?>
            <a href="<?php echo $this->site_url.'google_ads'; ?>" class="">Google Ads</a>
           </div>
        </li>
        <?php
        $sub_tab_active="";
        if(isset($_GET['tab']) && ($_GET['tab'] == 'google_shopping_feed' || $_GET['tab'] == 'gaa_config_page' || $_GET['tab'] == 'sync_product_page' || $_GET['tab'] == 'shopping_campaigns_page' || $_GET['tab'] == 'add_campaign_page')){
            $sub_tab_active="active";
        }
        ?>
        <li class="nav-item">
            <div class="tvc-tooltip <?php echo (($sub_tab_active)?$sub_tab_active:$this->is_active_tabs('google_shopping_feed')); ?>">
                <?php if(isset($setting_status['google_shopping']) && $setting_status['google_shopping'] == false ){
                  echo (isset($setting_status['google_shopping_msg'])?'<span class="tvc-tooltiptext tvc-tooltip-right">'.$setting_status['google_shopping_msg'].'</span>':"");
                } ?>
                <div class="nav-link <?php echo (($sub_tab_active)?$sub_tab_active:$this->is_active_tabs('google_shopping_feed')); ?>">
                <?php if(isset($setting_status['google_shopping']) ){
                  echo $this->info_htnml($setting_status['google_shopping']); 
                } ?>
                <a href="<?php echo $this->site_url.'google_shopping_feed'; ?>" class="">Google Shopping</a>
            </div>
        </li>
        <li class="tvc-help-need">          
          For any query, reach out to us at <a href="tel:+1 (415) 968-6313">+1 (415) 968-6313</a>
        </li>
      </ul>     
    <?php
  }
} ?>