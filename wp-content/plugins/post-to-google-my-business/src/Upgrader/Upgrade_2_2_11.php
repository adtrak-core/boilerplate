<?php


namespace PGMB\Upgrader;


use PGMB\FormFields;
use PGMB\Notifications\BasicNotification;
use PGMB\Notifications\FeatureNotification;
use PGMB\WeDevsSettingsAPI;

class Upgrade_2_2_11 implements Upgrade {

	private $settings;

	public function __construct() {
		$this->settings = new WeDevsSettingsAPI();
	}

	public function run() {
		$notification_manager = new \PGMB\Notifications\NotificationManager('mbp');
		$current_user = wp_get_current_user();
		$notification = BasicNotification::create(
			\MBP_Admin_Page_Settings::NOTIFICATION_SECTION,
			'2_2_11_upgrade_notification',
			esc_html__('Thanks for updating Post to Google My Business!', 'post-to-google-my-business'),
			nl2br(sprintf(
				esc_html__("Hi %1\$s,\n\nThanks for updating Post to Google My Business to the latest version. I've added this new dashboard section, check out the new features section below!\n\nIf you're enjoying the plugin and have a moment to %2\$s, that's really appreciated and really helps me move the plugin forward.\n\n%3\$s"),
				esc_html($current_user->display_name),
				sprintf(
					'<a target="_blank" href="%s">%s</a>',
					'https://wordpress.org/plugins/post-to-google-my-business/',
					esc_html__('leave a review', 'post-to-google-my-business')
				),
				sprintf(
					'<strong>%s</strong><br /><i>%s</i>',
					'Koen',
					esc_html__('Plugin Developer', 'post-to-google-my-business')
				)
			)),
			'img/koen.png',
			esc_html__('Developer profile photo','post-to-google-my-business')
		);
		$notification_manager->add_notification($notification);

		$autopost_feature = FeatureNotification::create(
			\MBP_Admin_Page_Settings::NEW_FEATURES_SECTION,
			'2_2_11_autopost_categories',
			esc_html__('[Pro] Auto-post only with specific tag/category', 'post-to-google-my-business'),
			esc_html__('This allows you to limit auto-post to a specific tag or category. Your post will be auto-posted when they have a tag/category where this option is enabled.', 'post-to-google-my-business'),
			'img/features/autopost_categories.png',
			esc_html__('Screenshot', 'post-to-google-my-business')
		);
		$notification_manager->add_notification($autopost_feature);

		$calendar_feature = FeatureNotification::create(
			\MBP_Admin_Page_Settings::NEW_FEATURES_SECTION,
			'2_2_11_calendar',
			esc_html__('Post calendar', 'post-to-google-my-business'),
			esc_html__("Track your posts in time with the new post calender. For easy scheduling and planning.", 'post-to-google-my-business'),
			'img/features/calendar.jpg',
			esc_html__('Screenshot', 'post-to-google-my-business')
		);
		$notification_manager->add_notification($calendar_feature);

		$calendar_feature = FeatureNotification::create(
			\MBP_Admin_Page_Settings::NEW_FEATURES_SECTION,
			'2_2_11_autopost_editor',
			esc_html__('Full editor for auto-post template', 'post-to-google-my-business'),
			esc_html__('Fine-tune one template for all your automated posts to follow.  The new editor gives you complete control over the auto-post template.', 'post-to-google-my-business'),
			'img/features/autopost_editor.png',
			esc_html__('Screenshot', 'post-to-google-my-business')
		);
		$notification_manager->add_notification($calendar_feature);

		$covid_post = FeatureNotification::create(
			\MBP_Admin_Page_Settings::NEW_FEATURES_SECTION,
			'2_2_11_COVID_post',
			esc_html__('COVID-19 alert/update post support', 'post-to-google-my-business'),
			esc_html__("A very effective way to keep your customers up-to-date on any changes to your business due to the corona virus (COVID-19)"),
			'img/features/covid19.png',
			esc_html__('Screenshot', 'post-to-google-my-business')
		);
		$notification_manager->add_notification($covid_post);


		$template = $this->settings->get_option('template', 'mbp_quick_post_settings', 'New post: %post_title% - %post_content%');
        $cta = $this->settings->get_option('cta', 'mbp_quick_post_settings', 'LEARN_MORE');
        $url_template = $this->settings->get_option('url', 'mbp_quick_post_settings', '%post_permalink%');
        $location = $this->settings->get_option('google_location', 'mbp_google_settings');
        $content_image = $this->settings->get_option('fetch_content_image', 'mbp_quick_post_settings', 'off') == 'on';
        $featured_image = $this->settings->get_option('use_featured_image', 'mbp_quick_post_settings', 'on') == 'on';

        $new_fields = FormFields::default_autopost_fields();

        $new_fields['mbp_post_text'] = $template;
        if($cta != 'NONE'){
	        $new_fields['mbp_button'] = true;
        }
        $new_fields['mbp_button_type'] = $cta;
        $new_fields['mbp_button_url'] = $url_template;
        $new_fields['mbp_selected_location'] = [$location];
        $new_fields['mbp_content_image'] = $content_image;
        $new_fields['mbp_featured_image'] = $featured_image;
		$new_value = get_option("mbp_quick_post_settings");
		if($new_value && is_array($new_value)){
			$new_value['autopost_template'] = $new_fields;
			update_option("mbp_quick_post_settings", $new_value);
		}
	}
}
