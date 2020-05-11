<?php if($this instanceof MBP_Admin_Page_Settings) : ?>

    <?php if($this->notification_manager->notification_count(MBP_Admin_Page_Settings::NOTIFICATION_SECTION)) : ?>
        <div class="pgmb-widget postbox pgmb-notifications-container">
            <h2 class="hndle ui-sortable-handle"><span><?php echo sprintf(esc_html__("Notifications (%s)", "post-to-google-my-business"), '<span class="mbp-notification-count">'.$this->notification_manager->notification_count(MBP_Admin_Page_Settings::NOTIFICATION_SECTION).'</span>'); ?></span>
            </h2>

            <div class="pgmb-widget-inside inside">
                <?php $this->get_notifications(); ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if($this->notification_manager->notification_count(MBP_Admin_Page_Settings::NEW_FEATURES_SECTION)) : ?>
    <div class="pgmb-widget postbox pgmb-notifications-container">

        <h2 class="hndle ui-sortable-handle"><span><?php echo sprintf(esc_html__("New features (%s)", "post-to-google-my-business"), '<span class="mbp-notification-count">'.$this->notification_manager->notification_count(MBP_Admin_Page_Settings::NEW_FEATURES_SECTION).'</span>'); ?></span></h2>

        <div class="pgmb-widget-inside inside">
            <div class="pgmb-features-container">
                <?php $this->get_new_features(); ?>
            </div>

        </div>
    </div>
	<?php endif; ?>

<!--    <div class="pgmb-widget postbox">-->
<!--        <h2 class="hndle ui-sortable-handle"><span>Statistics</span></h2>-->
<!--        <div class="pgmb-widget-inside inside">-->
<!---->
<!--        </div>-->
<!--    </div>-->
    <div class="pgmb-widget postbox">
        <h2 class="hndle ui-sortable-handle"><span><?php esc_html_e('Calendar', 'post-to-google-my-business'); ?></span></h2>
        <div class="pgmb-widget-inside inside">
            <div id="calendar"></div>
        </div>
    </div>
<?php endif; ?>
