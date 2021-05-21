<?php
// Exit if accessed directly
if ( ! defined('ABSPATH') ) {
	exit;
}
if ( ! class_exists( 'Kadence_Woomail_Import_Export' ) ) {

	class Kadence_Woomail_Import_Export {
		/**
		* @var null
		*/
		private static $instance = null;
		private static $woo_core_options = array(
			'woocommerce_email_header_image',
			'woocommerce_email_footer_text',
			'woocommerce_email_body_background_color',
			'woocommerce_email_text_color',
			'woocommerce_email_background_color',
			'woocommerce_new_order_settings[heading]',
			'woocommerce_new_order_settings[subject]',

			'woocommerce_cancelled_order_settings[heading]',
			'woocommerce_customer_processing_order_settings[heading]',
			'woocommerce_customer_completed_order_settings[heading]',
			'woocommerce_customer_refunded_order_settings[heading_full]',
			'woocommerce_customer_refunded_order_settings[heading_partial]',

			'woocommerce_customer_on_hold_order_settings[heading]',
			'woocommerce_customer_invoice_settings[heading]',
			'woocommerce_customer_invoice_settings[heading_paid]',
			'woocommerce_failed_order_settings[heading]',
			'woocommerce_customer_new_account_settings[heading]',
			'woocommerce_customer_note_settings[heading]',
			'woocommerce_customer_reset_password_settings[heading]',

			'woocommerce_cancelled_order_settings[subject]',
			'woocommerce_customer_processing_order_settings[subject]',
			'woocommerce_customer_completed_order_settings[subject]',

			'woocommerce_customer_refunded_order_settings[subject_full]',
			'woocommerce_customer_refunded_order_settings[subject_partial]',

			'woocommerce_customer_on_hold_order_settings[subject]',

			'woocommerce_customer_invoice_settings[subject]',
			'woocommerce_customer_invoice_settings[subject_paid]',

			'woocommerce_failed_order_settings[subject]',
			'woocommerce_customer_new_account_settings[subject]',
			'woocommerce_customer_note_settings[subject]',
			'woocommerce_customer_reset_password_settings[subject]',
		);
		private static $prebuilt_options = array(
			'kt_skinny' => 'a:2:{s:8:"template";s:24:"kadence-woomail-designer";s:7:"options";a:158:{s:30:"kt_woomail[new_order_subtitle]";s:0:"";s:26:"kt_woomail[new_order_body]";s:78:"You have received an order from {customer_full_name}. The order is as follows:";s:36:"kt_woomail[cancelled_order_subtitle]";s:0:"";s:32:"kt_woomail[cancelled_order_body]";s:96:"The order {order_number} from {customer_full_name} has been cancelled. The order was as follows:";s:46:"kt_woomail[customer_processing_order_subtitle]";s:19:"ORDER IS PROCESSING";s:42:"kt_woomail[customer_processing_order_body]";s:111:"Your order has been received and is now being processed. Your order details are shown below for your reference:";s:45:"kt_woomail[customer_completed_order_subtitle]";s:21:"THANKS FOR YOUR ORDER";s:41:"kt_woomail[customer_completed_order_body]";s:118:"Hi there. Your recent order on {site_title} has been completed. Your order details are shown below for your reference:";s:44:"kt_woomail[customer_refunded_order_subtitle]";s:29:"ORDER #{order_number} DETAILS";s:42:"kt_woomail[customer_refunded_order_switch]";b:1;s:45:"kt_woomail[customer_refunded_order_body_full]";s:55:"Hi there. Your order on {site_title} has been refunded.";s:48:"kt_woomail[customer_refunded_order_body_partial]";s:65:"Hi there. Your order on {site_title} has been partially refunded.";s:43:"kt_woomail[customer_on_hold_order_subtitle]";s:21:"THANKS FOR YOUR ORDER";s:39:"kt_woomail[customer_on_hold_order_body]";s:120:"Your order is on-hold until we confirm payment has been received. Your order details are shown below for your reference:";s:37:"kt_woomail[customer_invoice_subtitle]";s:33:"INVOICE FOR ORDER #{order_number}";s:35:"kt_woomail[customer_invoice_switch]";b:1;s:38:"kt_woomail[customer_invoice_body_paid]";s:0:"";s:33:"kt_woomail[customer_invoice_body]";s:69:"An order has been created for you on {site_title}. {invoice_pay_link}";s:33:"kt_woomail[failed_order_subtitle]";s:0:"";s:29:"kt_woomail[failed_order_body]";s:96:"Payment for order {order_number} from {customer_full_name} has failed. The order was as follows:";s:41:"kt_woomail[customer_new_account_subtitle]";s:0:"";s:37:"kt_woomail[customer_new_account_body]";s:84:"Thanks for creating an account on {site_title}. Your username is {customer_username}";s:34:"kt_woomail[customer_note_subtitle]";s:30:"A NOTE WAS ADDED TO YOUR ORDER";s:30:"kt_woomail[customer_note_body]";s:48:"Hello, a note has just been added to your order:";s:44:"kt_woomail[customer_reset_password_subtitle]";s:12:"INSTRUCTIONS";s:40:"kt_woomail[customer_reset_password_body]";s:228:"Someone requested that the password be reset for the following account:

Username: {customer_username}

If this was a mistake, just ignore this email and nothing will happen.

To reset your password, visit the following address:";s:31:"kt_woomail[email_load_template]";s:7:"kt_full";s:22:"kt_woomail[email_type]";s:24:"customer_completed_order";s:27:"kt_woomail[email_text_info]";s:0:"";s:25:"kt_woomail[content_width]";s:3:"480";s:25:"kt_woomail[border_radius]";s:1:"3";s:24:"kt_woomail[border_width]";s:1:"1";s:24:"kt_woomail[border_color]";s:7:"#dedede";s:18:"kt_woomail[shadow]";s:1:"1";s:25:"kt_woomail[email_padding]";s:2:"70";s:32:"kt_woomail[email_padding_bottom]";s:2:"70";s:30:"kt_woomail[header_image_align]";s:6:"center";s:33:"kt_woomail[header_image_maxwidth]";s:3:"300";s:41:"kt_woomail[header_image_background_color]";s:11:"transparent";s:43:"kt_woomail[header_image_padding_top_bottom]";s:1:"0";s:35:"kt_woomail[header_background_color]";s:7:"#ffffff";s:29:"kt_woomail[header_text_align]";s:6:"center";s:30:"kt_woomail[header_padding_top]";s:2:"36";s:33:"kt_woomail[header_padding_bottom]";s:2:"36";s:37:"kt_woomail[header_padding_left_right]";s:2:"48";s:29:"kt_woomail[heading_font_size]";s:2:"30";s:31:"kt_woomail[heading_line_height]";s:2:"40";s:31:"kt_woomail[heading_font_family]";s:9:"helvetica";s:30:"kt_woomail[heading_font_style]";s:6:"normal";s:31:"kt_woomail[heading_font_weight]";s:3:"400";s:25:"kt_woomail[heading_color]";s:7:"#4a2a4d";s:31:"kt_woomail[subtitle_fontt_info]";s:0:"";s:30:"kt_woomail[subtitle_placement]";s:5:"below";s:30:"kt_woomail[subtitle_font_size]";s:2:"18";s:32:"kt_woomail[subtitle_line_height]";s:2:"24";s:32:"kt_woomail[subtitle_font_family]";s:9:"helvetica";s:31:"kt_woomail[subtitle_font_style]";s:6:"normal";s:32:"kt_woomail[subtitle_font_weight]";s:3:"300";s:26:"kt_woomail[subtitle_color]";s:7:"#4a2a4d";s:31:"kt_woomail[content_padding_top]";s:1:"0";s:34:"kt_woomail[content_padding_bottom]";s:1:"0";s:27:"kt_woomail[content_padding]";s:2:"48";s:21:"kt_woomail[font_size]";s:2:"14";s:23:"kt_woomail[line_height]";s:2:"24";s:23:"kt_woomail[font_family]";s:9:"helvetica";s:23:"kt_woomail[font_weight]";s:3:"400";s:22:"kt_woomail[link_color]";s:7:"#4a2a4d";s:24:"kt_woomail[h2_font_size]";s:2:"16";s:26:"kt_woomail[h2_line_height]";s:2:"26";s:26:"kt_woomail[h2_padding_top]";s:1:"0";s:29:"kt_woomail[h2_padding_bottom]";s:1:"0";s:25:"kt_woomail[h2_margin_top]";s:1:"5";s:28:"kt_woomail[h2_margin_bottom]";s:2:"25";s:26:"kt_woomail[h2_font_family]";s:9:"helvetica";s:25:"kt_woomail[h2_font_style]";s:6:"normal";s:26:"kt_woomail[h2_font_weight]";s:3:"500";s:29:"kt_woomail[h2_text_transform]";s:4:"none";s:20:"kt_woomail[h2_color]";s:7:"#4a2a4d";s:25:"kt_woomail[h2_text_align]";s:6:"center";s:20:"kt_woomail[h2_style]";s:4:"none";s:31:"kt_woomail[h2_separator_height]";s:0:"";s:30:"kt_woomail[h2_separator_style]";s:5:"solid";s:30:"kt_woomail[h2_separator_color]";s:7:"#96588a";s:24:"kt_woomail[h3_font_info]";s:0:"";s:24:"kt_woomail[h3_font_size]";s:2:"16";s:26:"kt_woomail[h3_line_height]";s:2:"26";s:26:"kt_woomail[h3_font_family]";s:9:"helvetica";s:25:"kt_woomail[h3_font_style]";s:6:"normal";s:26:"kt_woomail[h3_font_weight]";s:3:"500";s:20:"kt_woomail[h3_color]";s:7:"#787878";s:29:"kt_woomail[order_items_style]";s:6:"normal";s:40:"kt_woomail[items_table_background_color]";s:0:"";s:31:"kt_woomail[items_table_padding]";s:2:"12";s:36:"kt_woomail[items_table_border_width]";s:1:"1";s:36:"kt_woomail[items_table_border_color]";s:7:"#e4e4e4";s:36:"kt_woomail[items_table_border_style]";s:5:"solid";s:31:"kt_woomail[order_heading_style]";s:6:"normal";s:38:"kt_woomail[addresses_background_color]";s:0:"";s:34:"kt_woomail[addresses_border_width]";s:1:"2";s:34:"kt_woomail[addresses_border_color]";s:7:"#e5e5e5";s:34:"kt_woomail[addresses_border_style]";s:5:"solid";s:32:"kt_woomail[addresses_text_color]";s:7:"#8f8f8f";s:32:"kt_woomail[addresses_text_align]";s:6:"center";s:39:"kt_woomail[footer_background_placement]";s:6:"inside";s:35:"kt_woomail[footer_background_color]";s:0:"";s:30:"kt_woomail[footer_top_padding]";s:1:"0";s:33:"kt_woomail[footer_bottom_padding]";s:2:"31";s:37:"kt_woomail[footer_left_right_padding]";s:2:"48";s:34:"kt_woomail[footer_social_repeater]";s:1111:"[{"icon_value":"kt-woomail-facebook","icon_color":"gray","text":"undefined","link":"https://www.facebook.com","text2":"undefined","image_url":"","choice":"customizer_repeater_icon","title":"","subtitle":"undefined","id":"social-repeater-5afb186e6db1f","shortcode":"undefined"},{"icon_value":"kt-woomail-instagram","icon_color":"gray","text":"undefined","link":"https://www.instagram.com","text2":"undefined","image_url":"","choice":"customizer_repeater_icon","title":"","subtitle":"undefined","id":"customizer-repeater-5afb18a75dfdc","shortcode":"undefined"},{"icon_value":"kt-woomail-twitter","icon_color":"gray","text":"undefined","link":"https://www.twitter.com","text2":"undefined","image_url":"","choice":"customizer_repeater_icon","title":"","subtitle":"undefined","id":"customizer-repeater-5afb188d6d776","shortcode":"undefined"},{"icon_value":"kt-woomail-link","icon_color":"gray","text":"undefined","link":"https://www.google.com","text2":"undefined","image_url":"","choice":"customizer_repeater_icon","title":"","subtitle":"undefined","id":"customizer-repeater-5afb18c76ca11","shortcode":"undefined"}]";s:37:"kt_woomail[footer_social_title_color]";s:7:"#000000";s:36:"kt_woomail[footer_social_title_size]";s:2:"18";s:43:"kt_woomail[footer_social_title_font_family]";s:9:"helvetica";s:43:"kt_woomail[footer_social_title_font_weight]";s:3:"400";s:37:"kt_woomail[footer_social_top_padding]";s:2:"15";s:40:"kt_woomail[footer_social_bottom_padding]";s:2:"20";s:38:"kt_woomail[footer_social_border_width]";s:1:"1";s:38:"kt_woomail[footer_social_border_color]";s:7:"#dddddd";s:38:"kt_woomail[footer_social_border_style]";s:5:"solid";s:29:"kt_woomail[footer_text_align]";s:6:"center";s:28:"kt_woomail[footer_font_size]";s:2:"12";s:30:"kt_woomail[footer_font_family]";s:9:"helvetica";s:30:"kt_woomail[footer_font_weight]";s:3:"400";s:24:"kt_woomail[footer_color]";s:7:"#9e9e9e";s:37:"kt_woomail[footer_credit_top_padding]";s:2:"20";s:40:"kt_woomail[footer_credit_bottom_padding]";s:1:"0";s:22:"kt_woomail[custom_css]";s:76:"#addresses td {
    width: 100%;
    display:block;
    margin-bottom:30px
}";s:25:"kt_woomail[import_export]";s:0:"";s:30:"woocommerce_email_header_image";s:0:"";s:34:"woocommerce_email_background_color";s:7:"#f7f7f7";s:28:"woocommerce_email_text_color";s:7:"#515151";s:39:"woocommerce_email_body_background_color";s:7:"#ffffff";s:29:"woocommerce_email_footer_text";s:12:"{site_title}";s:39:"woocommerce_new_order_settings[subject]";s:65:"[{site_title}] New customer order ({order_number}) - {order_date}";s:39:"woocommerce_new_order_settings[heading]";s:9:"NEW ORDER";s:45:"woocommerce_cancelled_order_settings[subject]";s:47:"[{site_title}] Cancelled order ({order_number})";s:45:"woocommerce_cancelled_order_settings[heading]";s:15:"ORDER CANCELLED";s:55:"woocommerce_customer_processing_order_settings[subject]";s:0:"";s:55:"woocommerce_customer_processing_order_settings[heading]";s:9:"THANK YOU";s:54:"woocommerce_customer_completed_order_settings[subject]";s:0:"";s:54:"woocommerce_customer_completed_order_settings[heading]";s:14:"ORDER COMPLETE";s:58:"woocommerce_customer_refunded_order_settings[subject_full]";s:0:"";s:61:"woocommerce_customer_refunded_order_settings[subject_partial]";s:0:"";s:58:"woocommerce_customer_refunded_order_settings[heading_full]";s:14:"ORDER REFUNDED";s:61:"woocommerce_customer_refunded_order_settings[heading_partial]";s:18:"PARTIALLY REFUNDED";s:52:"woocommerce_customer_on_hold_order_settings[subject]";s:0:"";s:52:"woocommerce_customer_on_hold_order_settings[heading]";s:13:"ORDER DETAILS";s:46:"woocommerce_customer_invoice_settings[subject]";s:0:"";s:51:"woocommerce_customer_invoice_settings[subject_paid]";s:0:"";s:46:"woocommerce_customer_invoice_settings[heading]";s:13:"ORDER DETAILS";s:51:"woocommerce_customer_invoice_settings[heading_paid]";s:13:"ORDER DETAILS";s:42:"woocommerce_failed_order_settings[subject]";s:44:"[{site_title}] Failed order ({order_number})";s:42:"woocommerce_failed_order_settings[heading]";s:12:"ORDER FAILED";s:50:"woocommerce_customer_new_account_settings[subject]";s:0:"";s:50:"woocommerce_customer_new_account_settings[heading]";s:0:"";s:43:"woocommerce_customer_note_settings[subject]";s:0:"";s:43:"woocommerce_customer_note_settings[heading]";s:16:"ORDER NOTE ADDED";s:53:"woocommerce_customer_reset_password_settings[subject]";s:0:"";s:53:"woocommerce_customer_reset_password_settings[heading]";s:14:"PASSWORD RESET";}}',

'kt_full' => 'a:2:{s:8:"template";s:24:"kadence-woomail-designer";s:7:"options";a:158:{s:30:"kt_woomail[new_order_subtitle]";s:24:"Hello from {site_title}!";s:26:"kt_woomail[new_order_body]";s:78:"You have received an order from {customer_full_name}. The order is as follows:";s:36:"kt_woomail[cancelled_order_subtitle]";s:24:"Hello from {site_title}!";s:32:"kt_woomail[cancelled_order_body]";s:96:"The order {order_number} from {customer_full_name} has been cancelled. The order was as follows:";s:46:"kt_woomail[customer_processing_order_subtitle]";s:24:"Hello from {site_title}!";s:42:"kt_woomail[customer_processing_order_body]";s:111:"Your order has been received and is now being processed. Your order details are shown below for your reference:";s:45:"kt_woomail[customer_completed_order_subtitle]";s:24:"Hello from {site_title}!";s:41:"kt_woomail[customer_completed_order_body]";s:118:"Hi there. Your recent order on {site_title} has been completed. Your order details are shown below for your reference:";s:44:"kt_woomail[customer_refunded_order_subtitle]";s:24:"Hello from {site_title}!";s:42:"kt_woomail[customer_refunded_order_switch]";b:1;s:45:"kt_woomail[customer_refunded_order_body_full]";s:55:"Hi there. Your order on {site_title} has been refunded.";s:48:"kt_woomail[customer_refunded_order_body_partial]";s:65:"Hi there. Your order on {site_title} has been partially refunded.";s:43:"kt_woomail[customer_on_hold_order_subtitle]";s:24:"Hello from {site_title}!";s:39:"kt_woomail[customer_on_hold_order_body]";s:120:"Your order is on-hold until we confirm payment has been received. Your order details are shown below for your reference:";s:37:"kt_woomail[customer_invoice_subtitle]";s:24:"Hello from {site_title}!";s:35:"kt_woomail[customer_invoice_switch]";b:0;s:38:"kt_woomail[customer_invoice_body_paid]";s:0:"";s:33:"kt_woomail[customer_invoice_body]";s:69:"An order has been created for you on {site_title}. {invoice_pay_link}";s:33:"kt_woomail[failed_order_subtitle]";s:24:"Hello from {site_title}!";s:29:"kt_woomail[failed_order_body]";s:96:"Payment for order {order_number} from {customer_full_name} has failed. The order was as follows:";s:41:"kt_woomail[customer_new_account_subtitle]";s:24:"Hello from {site_title}!";s:37:"kt_woomail[customer_new_account_body]";s:84:"Thanks for creating an account on {site_title}. Your username is {customer_username}";s:34:"kt_woomail[customer_note_subtitle]";s:24:"Hello from {site_title}!";s:30:"kt_woomail[customer_note_body]";s:48:"Hello, a note has just been added to your order:";s:44:"kt_woomail[customer_reset_password_subtitle]";s:24:"Hello from {site_title}!";s:40:"kt_woomail[customer_reset_password_body]";s:228:"Someone requested that the password be reset for the following account:

Username: {customer_username}

If this was a mistake, just ignore this email and nothing will happen.

To reset your password, visit the following address:";s:31:"kt_woomail[email_load_template]";s:7:"kt_full";s:22:"kt_woomail[email_type]";s:24:"customer_completed_order";s:27:"kt_woomail[email_text_info]";s:0:"";s:25:"kt_woomail[content_width]";s:3:"600";s:25:"kt_woomail[border_radius]";s:1:"0";s:24:"kt_woomail[border_width]";s:1:"0";s:24:"kt_woomail[border_color]";s:7:"#ffffff";s:18:"kt_woomail[shadow]";s:1:"0";s:25:"kt_woomail[email_padding]";s:1:"0";s:32:"kt_woomail[email_padding_bottom]";s:1:"0";s:30:"kt_woomail[header_image_align]";s:6:"center";s:33:"kt_woomail[header_image_maxwidth]";s:3:"260";s:41:"kt_woomail[header_image_background_color]";s:7:"#f2f2f2";s:43:"kt_woomail[header_image_padding_top_bottom]";s:2:"21";s:35:"kt_woomail[header_background_color]";s:7:"#ffffff";s:29:"kt_woomail[header_text_align]";s:6:"center";s:30:"kt_woomail[header_padding_top]";s:2:"36";s:33:"kt_woomail[header_padding_bottom]";s:2:"36";s:37:"kt_woomail[header_padding_left_right]";s:2:"48";s:29:"kt_woomail[heading_font_size]";s:2:"32";s:31:"kt_woomail[heading_line_height]";s:2:"40";s:31:"kt_woomail[heading_font_family]";s:9:"helvetica";s:30:"kt_woomail[heading_font_style]";s:6:"normal";s:31:"kt_woomail[heading_font_weight]";s:3:"600";s:25:"kt_woomail[heading_color]";s:7:"#222222";s:31:"kt_woomail[subtitle_fontt_info]";s:0:"";s:30:"kt_woomail[subtitle_placement]";s:5:"above";s:30:"kt_woomail[subtitle_font_size]";s:2:"20";s:32:"kt_woomail[subtitle_line_height]";s:2:"34";s:32:"kt_woomail[subtitle_font_family]";s:7:"georgia";s:31:"kt_woomail[subtitle_font_style]";s:6:"italic";s:32:"kt_woomail[subtitle_font_weight]";s:3:"400";s:26:"kt_woomail[subtitle_color]";s:7:"#999999";s:31:"kt_woomail[content_padding_top]";s:1:"0";s:34:"kt_woomail[content_padding_bottom]";s:1:"0";s:27:"kt_woomail[content_padding]";s:2:"20";s:21:"kt_woomail[font_size]";s:2:"14";s:23:"kt_woomail[line_height]";s:2:"24";s:23:"kt_woomail[font_family]";s:9:"helvetica";s:23:"kt_woomail[font_weight]";s:3:"400";s:22:"kt_woomail[link_color]";s:7:"#f96a01";s:24:"kt_woomail[h2_font_size]";s:2:"28";s:26:"kt_woomail[h2_line_height]";s:2:"34";s:26:"kt_woomail[h2_padding_top]";s:2:"34";s:29:"kt_woomail[h2_padding_bottom]";s:1:"0";s:25:"kt_woomail[h2_margin_top]";s:2:"30";s:28:"kt_woomail[h2_margin_bottom]";s:2:"30";s:26:"kt_woomail[h2_font_family]";s:9:"helvetica";s:25:"kt_woomail[h2_font_style]";s:6:"normal";s:26:"kt_woomail[h2_font_weight]";s:3:"700";s:29:"kt_woomail[h2_text_transform]";s:4:"none";s:20:"kt_woomail[h2_color]";s:7:"#222222";s:25:"kt_woomail[h2_text_align]";s:6:"center";s:20:"kt_woomail[h2_style]";s:5:"above";s:31:"kt_woomail[h2_separator_height]";s:1:"1";s:30:"kt_woomail[h2_separator_style]";s:6:"dashed";s:30:"kt_woomail[h2_separator_color]";s:7:"#e5e5e5";s:24:"kt_woomail[h3_font_info]";s:0:"";s:24:"kt_woomail[h3_font_size]";s:2:"16";s:26:"kt_woomail[h3_line_height]";s:2:"30";s:26:"kt_woomail[h3_font_family]";s:7:"georgia";s:25:"kt_woomail[h3_font_style]";s:6:"italic";s:26:"kt_woomail[h3_font_weight]";s:3:"400";s:20:"kt_woomail[h3_color]";s:7:"#999999";s:29:"kt_woomail[order_items_style]";s:5:"light";s:40:"kt_woomail[items_table_background_color]";s:0:"";s:31:"kt_woomail[items_table_padding]";s:2:"12";s:36:"kt_woomail[items_table_border_width]";s:1:"1";s:36:"kt_woomail[items_table_border_color]";s:7:"#e5e5e5";s:36:"kt_woomail[items_table_border_style]";s:6:"dashed";s:31:"kt_woomail[order_heading_style]";s:5:"split";s:38:"kt_woomail[addresses_background_color]";s:0:"";s:34:"kt_woomail[addresses_border_width]";s:1:"1";s:34:"kt_woomail[addresses_border_color]";s:7:"#e5e5e5";s:34:"kt_woomail[addresses_border_style]";s:6:"dashed";s:32:"kt_woomail[addresses_text_color]";s:7:"#8f8f8f";s:32:"kt_woomail[addresses_text_align]";s:6:"center";s:39:"kt_woomail[footer_background_placement]";s:7:"outside";s:35:"kt_woomail[footer_background_color]";s:7:"#333333";s:30:"kt_woomail[footer_top_padding]";s:2:"25";s:33:"kt_woomail[footer_bottom_padding]";s:2:"25";s:37:"kt_woomail[footer_left_right_padding]";s:2:"20";s:34:"kt_woomail[footer_social_repeater]";s:1115:"[{"icon_value":"kt-woomail-facebook","icon_color":"white","text":"undefined","link":"https://www.facebook.com","text2":"undefined","image_url":"","choice":"customizer_repeater_icon","title":"","subtitle":"undefined","id":"social-repeater-5afb186e6db1f","shortcode":"undefined"},{"icon_value":"kt-woomail-instagram","icon_color":"white","text":"undefined","link":"https://www.instagram.com","text2":"undefined","image_url":"","choice":"customizer_repeater_icon","title":"","subtitle":"undefined","id":"customizer-repeater-5afb18a75dfdc","shortcode":"undefined"},{"icon_value":"kt-woomail-twitter","icon_color":"white","text":"undefined","link":"https://www.twitter.com","text2":"undefined","image_url":"","choice":"customizer_repeater_icon","title":"","subtitle":"undefined","id":"customizer-repeater-5afb188d6d776","shortcode":"undefined"},{"icon_value":"kt-woomail-link","icon_color":"white","text":"undefined","link":"https://www.google.com","text2":"undefined","image_url":"","choice":"customizer_repeater_icon","title":"","subtitle":"undefined","id":"customizer-repeater-5afb18c76ca11","shortcode":"undefined"}]";s:37:"kt_woomail[footer_social_title_color]";s:7:"#000000";s:36:"kt_woomail[footer_social_title_size]";s:2:"18";s:43:"kt_woomail[footer_social_title_font_family]";s:9:"helvetica";s:43:"kt_woomail[footer_social_title_font_weight]";s:3:"400";s:37:"kt_woomail[footer_social_top_padding]";s:2:"15";s:40:"kt_woomail[footer_social_bottom_padding]";s:2:"30";s:38:"kt_woomail[footer_social_border_width]";s:1:"1";s:38:"kt_woomail[footer_social_border_color]";s:7:"#e5e5e5";s:38:"kt_woomail[footer_social_border_style]";s:6:"dashed";s:29:"kt_woomail[footer_text_align]";s:6:"center";s:28:"kt_woomail[footer_font_size]";s:2:"12";s:30:"kt_woomail[footer_font_family]";s:9:"helvetica";s:30:"kt_woomail[footer_font_weight]";s:3:"400";s:24:"kt_woomail[footer_color]";s:7:"#e5e5e5";s:37:"kt_woomail[footer_credit_top_padding]";s:2:"30";s:40:"kt_woomail[footer_credit_bottom_padding]";s:1:"0";s:22:"kt_woomail[custom_css]";s:0:"";s:25:"kt_woomail[import_export]";s:0:"";s:30:"woocommerce_email_header_image";s:93:"https://www.kadencethemes.com/wp-content/uploads/2018/05/kadence_woomail_example_logo-min.png";s:34:"woocommerce_email_background_color";s:7:"#ffffff";s:28:"woocommerce_email_text_color";s:7:"#515151";s:39:"woocommerce_email_body_background_color";s:7:"#ffffff";s:29:"woocommerce_email_footer_text";s:52:"Copyright © 2018 {site_title}, All rights reserved.";s:39:"woocommerce_new_order_settings[subject]";s:65:"[{site_title}] New customer order ({order_number}) - {order_date}";s:39:"woocommerce_new_order_settings[heading]";s:19:"New customer order!";s:45:"woocommerce_cancelled_order_settings[subject]";s:0:"";s:45:"woocommerce_cancelled_order_settings[heading]";s:31:"Order #{order_number} Cancelled";s:55:"woocommerce_customer_processing_order_settings[subject]";s:0:"";s:55:"woocommerce_customer_processing_order_settings[heading]";s:22:"Thanks for your order!";s:54:"woocommerce_customer_completed_order_settings[subject]";s:0:"";s:54:"woocommerce_customer_completed_order_settings[heading]";s:22:"Thanks for your order!";s:58:"woocommerce_customer_refunded_order_settings[subject_full]";s:0:"";s:61:"woocommerce_customer_refunded_order_settings[subject_partial]";s:0:"";s:58:"woocommerce_customer_refunded_order_settings[heading_full]";s:30:"Order #{order_number} refunded";s:61:"woocommerce_customer_refunded_order_settings[heading_partial]";s:29:"Order #{order_number} details";s:52:"woocommerce_customer_on_hold_order_settings[subject]";s:0:"";s:52:"woocommerce_customer_on_hold_order_settings[heading]";s:22:"Thanks for your order!";s:46:"woocommerce_customer_invoice_settings[subject]";s:0:"";s:51:"woocommerce_customer_invoice_settings[subject_paid]";s:0:"";s:46:"woocommerce_customer_invoice_settings[heading]";s:33:"Invoice for order #{order_number}";s:51:"woocommerce_customer_invoice_settings[heading_paid]";s:22:"Thanks for your order!";s:42:"woocommerce_failed_order_settings[subject]";s:44:"[{site_title}] Failed order ({order_number})";s:42:"woocommerce_failed_order_settings[heading]";s:28:"Order #{order_number} failed";s:50:"woocommerce_customer_new_account_settings[subject]";s:0:"";s:50:"woocommerce_customer_new_account_settings[heading]";s:0:"";s:43:"woocommerce_customer_note_settings[subject]";s:0:"";s:43:"woocommerce_customer_note_settings[heading]";s:41:"A note was added to order #{order_number}";s:53:"woocommerce_customer_reset_password_settings[subject]";s:0:"";s:53:"woocommerce_customer_reset_password_settings[heading]";s:27:"Password reset instructions";}}',
'kt_flat' => 'a:2:{s:8:"template";s:24:"kadence-woomail-designer";s:7:"options";a:158:{s:30:"kt_woomail[new_order_subtitle]";s:24:"Hello from {site_title}!";s:26:"kt_woomail[new_order_body]";s:78:"You have received an order from {customer_full_name}. The order is as follows:";s:36:"kt_woomail[cancelled_order_subtitle]";s:24:"Hello from {site_title}!";s:32:"kt_woomail[cancelled_order_body]";s:96:"The order {order_number} from {customer_full_name} has been cancelled. The order was as follows:";s:46:"kt_woomail[customer_processing_order_subtitle]";s:24:"Hello from {site_title}!";s:42:"kt_woomail[customer_processing_order_body]";s:111:"Your order has been received and is now being processed. Your order details are shown below for your reference:";s:45:"kt_woomail[customer_completed_order_subtitle]";s:24:"Hello from {site_title}!";s:41:"kt_woomail[customer_completed_order_body]";s:118:"Hi there. Your recent order on {site_title} has been completed. Your order details are shown below for your reference:";s:44:"kt_woomail[customer_refunded_order_subtitle]";s:24:"Hello from {site_title}!";s:42:"kt_woomail[customer_refunded_order_switch]";b:1;s:45:"kt_woomail[customer_refunded_order_body_full]";s:55:"Hi there. Your order on {site_title} has been refunded.";s:48:"kt_woomail[customer_refunded_order_body_partial]";s:65:"Hi there. Your order on {site_title} has been partially refunded.";s:43:"kt_woomail[customer_on_hold_order_subtitle]";s:24:"Hello from {site_title}!";s:39:"kt_woomail[customer_on_hold_order_body]";s:120:"Your order is on-hold until we confirm payment has been received. Your order details are shown below for your reference:";s:37:"kt_woomail[customer_invoice_subtitle]";s:24:"Hello from {site_title}!";s:35:"kt_woomail[customer_invoice_switch]";b:0;s:38:"kt_woomail[customer_invoice_body_paid]";s:0:"";s:33:"kt_woomail[customer_invoice_body]";s:69:"An order has been created for you on {site_title}. {invoice_pay_link}";s:33:"kt_woomail[failed_order_subtitle]";s:24:"Hello from {site_title}!";s:29:"kt_woomail[failed_order_body]";s:96:"Payment for order {order_number} from {customer_full_name} has failed. The order was as follows:";s:41:"kt_woomail[customer_new_account_subtitle]";s:24:"Hello from {site_title}!";s:37:"kt_woomail[customer_new_account_body]";s:84:"Thanks for creating an account on {site_title}. Your username is {customer_username}";s:34:"kt_woomail[customer_note_subtitle]";s:24:"Hello from {site_title}!";s:30:"kt_woomail[customer_note_body]";s:48:"Hello, a note has just been added to your order:";s:44:"kt_woomail[customer_reset_password_subtitle]";s:24:"Hello from {site_title}!";s:40:"kt_woomail[customer_reset_password_body]";s:228:"Someone requested that the password be reset for the following account:

Username: {customer_username}

If this was a mistake, just ignore this email and nothing will happen.

To reset your password, visit the following address:";s:31:"kt_woomail[email_load_template]";s:7:"kt_full";s:22:"kt_woomail[email_type]";s:24:"customer_completed_order";s:27:"kt_woomail[email_text_info]";s:0:"";s:25:"kt_woomail[content_width]";s:3:"600";s:25:"kt_woomail[border_radius]";s:1:"0";s:24:"kt_woomail[border_width]";s:1:"0";s:24:"kt_woomail[border_color]";s:7:"#ffffff";s:18:"kt_woomail[shadow]";s:1:"0";s:25:"kt_woomail[email_padding]";s:1:"0";s:32:"kt_woomail[email_padding_bottom]";s:1:"0";s:30:"kt_woomail[header_image_align]";s:6:"center";s:33:"kt_woomail[header_image_maxwidth]";s:3:"160";s:41:"kt_woomail[header_image_background_color]";s:7:"#fff7f7";s:43:"kt_woomail[header_image_padding_top_bottom]";s:2:"25";s:35:"kt_woomail[header_background_color]";s:7:"#feee8c";s:29:"kt_woomail[header_text_align]";s:4:"left";s:30:"kt_woomail[header_padding_top]";s:2:"48";s:33:"kt_woomail[header_padding_bottom]";s:2:"48";s:37:"kt_woomail[header_padding_left_right]";s:2:"48";s:29:"kt_woomail[heading_font_size]";s:2:"32";s:31:"kt_woomail[heading_line_height]";s:2:"40";s:31:"kt_woomail[heading_font_family]";s:9:"helvetica";s:30:"kt_woomail[heading_font_style]";s:6:"normal";s:31:"kt_woomail[heading_font_weight]";s:3:"600";s:25:"kt_woomail[heading_color]";s:7:"#000000";s:31:"kt_woomail[subtitle_fontt_info]";s:0:"";s:30:"kt_woomail[subtitle_placement]";s:5:"above";s:30:"kt_woomail[subtitle_font_size]";s:2:"20";s:32:"kt_woomail[subtitle_line_height]";s:2:"34";s:32:"kt_woomail[subtitle_font_family]";s:7:"georgia";s:31:"kt_woomail[subtitle_font_style]";s:6:"italic";s:32:"kt_woomail[subtitle_font_weight]";s:3:"400";s:26:"kt_woomail[subtitle_color]";s:7:"#000000";s:31:"kt_woomail[content_padding_top]";s:1:"0";s:34:"kt_woomail[content_padding_bottom]";s:2:"10";s:27:"kt_woomail[content_padding]";s:2:"48";s:21:"kt_woomail[font_size]";s:2:"14";s:23:"kt_woomail[line_height]";s:2:"24";s:23:"kt_woomail[font_family]";s:9:"helvetica";s:23:"kt_woomail[font_weight]";s:3:"400";s:22:"kt_woomail[link_color]";s:7:"#fe4365";s:24:"kt_woomail[h2_font_size]";s:2:"24";s:26:"kt_woomail[h2_line_height]";s:2:"25";s:26:"kt_woomail[h2_padding_top]";s:1:"0";s:29:"kt_woomail[h2_padding_bottom]";s:1:"0";s:25:"kt_woomail[h2_margin_top]";s:2:"15";s:28:"kt_woomail[h2_margin_bottom]";s:2:"33";s:26:"kt_woomail[h2_font_family]";s:9:"helvetica";s:25:"kt_woomail[h2_font_style]";s:6:"normal";s:26:"kt_woomail[h2_font_weight]";s:3:"700";s:29:"kt_woomail[h2_text_transform]";s:4:"none";s:20:"kt_woomail[h2_color]";s:7:"#000000";s:25:"kt_woomail[h2_text_align]";s:4:"left";s:20:"kt_woomail[h2_style]";s:5:"below";s:31:"kt_woomail[h2_separator_height]";s:1:"2";s:30:"kt_woomail[h2_separator_style]";s:5:"solid";s:30:"kt_woomail[h2_separator_color]";s:7:"#000000";s:24:"kt_woomail[h3_font_info]";s:0:"";s:24:"kt_woomail[h3_font_size]";s:2:"16";s:26:"kt_woomail[h3_line_height]";s:2:"30";s:26:"kt_woomail[h3_font_family]";s:7:"georgia";s:25:"kt_woomail[h3_font_style]";s:6:"italic";s:26:"kt_woomail[h3_font_weight]";s:3:"400";s:20:"kt_woomail[h3_color]";s:7:"#000000";s:29:"kt_woomail[order_items_style]";s:6:"normal";s:40:"kt_woomail[items_table_background_color]";s:0:"";s:31:"kt_woomail[items_table_padding]";s:1:"8";s:36:"kt_woomail[items_table_border_width]";s:1:"1";s:36:"kt_woomail[items_table_border_color]";s:7:"#000000";s:36:"kt_woomail[items_table_border_style]";s:5:"solid";s:31:"kt_woomail[order_heading_style]";s:6:"normal";s:38:"kt_woomail[addresses_background_color]";s:0:"";s:34:"kt_woomail[addresses_border_width]";s:1:"2";s:34:"kt_woomail[addresses_border_color]";s:7:"#000000";s:34:"kt_woomail[addresses_border_style]";s:5:"solid";s:32:"kt_woomail[addresses_text_color]";s:7:"#000000";s:32:"kt_woomail[addresses_text_align]";s:4:"left";s:39:"kt_woomail[footer_background_placement]";s:6:"inside";s:35:"kt_woomail[footer_background_color]";s:7:"#000000";s:30:"kt_woomail[footer_top_padding]";s:2:"25";s:33:"kt_woomail[footer_bottom_padding]";s:2:"25";s:37:"kt_woomail[footer_left_right_padding]";s:2:"48";s:34:"kt_woomail[footer_social_repeater]";s:1115:"[{"icon_value":"kt-woomail-facebook","icon_color":"white","text":"undefined","link":"https://www.facebook.com","text2":"undefined","image_url":"","choice":"customizer_repeater_icon","title":"","subtitle":"undefined","id":"social-repeater-5afb186e6db1f","shortcode":"undefined"},{"icon_value":"kt-woomail-instagram","icon_color":"white","text":"undefined","link":"https://www.instagram.com","text2":"undefined","image_url":"","choice":"customizer_repeater_icon","title":"","subtitle":"undefined","id":"customizer-repeater-5afb18a75dfdc","shortcode":"undefined"},{"icon_value":"kt-woomail-twitter","icon_color":"white","text":"undefined","link":"https://www.twitter.com","text2":"undefined","image_url":"","choice":"customizer_repeater_icon","title":"","subtitle":"undefined","id":"customizer-repeater-5afb188d6d776","shortcode":"undefined"},{"icon_value":"kt-woomail-link","icon_color":"white","text":"undefined","link":"https://www.google.com","text2":"undefined","image_url":"","choice":"customizer_repeater_icon","title":"","subtitle":"undefined","id":"customizer-repeater-5afb18c76ca11","shortcode":"undefined"}]";s:37:"kt_woomail[footer_social_title_color]";s:7:"#000000";s:36:"kt_woomail[footer_social_title_size]";s:2:"18";s:43:"kt_woomail[footer_social_title_font_family]";s:9:"helvetica";s:43:"kt_woomail[footer_social_title_font_weight]";s:3:"400";s:37:"kt_woomail[footer_social_top_padding]";s:2:"15";s:40:"kt_woomail[footer_social_bottom_padding]";s:2:"30";s:38:"kt_woomail[footer_social_border_width]";s:1:"1";s:38:"kt_woomail[footer_social_border_color]";s:7:"#cccccc";s:38:"kt_woomail[footer_social_border_style]";s:5:"solid";s:29:"kt_woomail[footer_text_align]";s:6:"center";s:28:"kt_woomail[footer_font_size]";s:2:"12";s:30:"kt_woomail[footer_font_family]";s:9:"helvetica";s:30:"kt_woomail[footer_font_weight]";s:3:"400";s:24:"kt_woomail[footer_color]";s:7:"#f7f7f7";s:37:"kt_woomail[footer_credit_top_padding]";s:2:"30";s:40:"kt_woomail[footer_credit_bottom_padding]";s:1:"0";s:22:"kt_woomail[custom_css]";s:0:"";s:25:"kt_woomail[import_export]";s:0:"";s:30:"woocommerce_email_header_image";s:99:"https://www.kadencethemes.com/wp-content/uploads/2018/05/kadence_woomail_example_logo_black-min.png";s:34:"woocommerce_email_background_color";s:7:"#fff7f7";s:28:"woocommerce_email_text_color";s:7:"#000000";s:39:"woocommerce_email_body_background_color";s:7:"#feee8c";s:29:"woocommerce_email_footer_text";s:52:"Copyright © 2018 {site_title}, All rights reserved.";s:39:"woocommerce_new_order_settings[subject]";s:65:"[{site_title}] New customer order ({order_number}) - {order_date}";s:39:"woocommerce_new_order_settings[heading]";s:19:"New customer order!";s:45:"woocommerce_cancelled_order_settings[subject]";s:0:"";s:45:"woocommerce_cancelled_order_settings[heading]";s:31:"Order #{order_number} Cancelled";s:55:"woocommerce_customer_processing_order_settings[subject]";s:0:"";s:55:"woocommerce_customer_processing_order_settings[heading]";s:22:"Thanks for your order!";s:54:"woocommerce_customer_completed_order_settings[subject]";s:0:"";s:54:"woocommerce_customer_completed_order_settings[heading]";s:22:"Thanks for your order!";s:58:"woocommerce_customer_refunded_order_settings[subject_full]";s:0:"";s:61:"woocommerce_customer_refunded_order_settings[subject_partial]";s:0:"";s:58:"woocommerce_customer_refunded_order_settings[heading_full]";s:30:"Order #{order_number} refunded";s:61:"woocommerce_customer_refunded_order_settings[heading_partial]";s:29:"Order #{order_number} details";s:52:"woocommerce_customer_on_hold_order_settings[subject]";s:0:"";s:52:"woocommerce_customer_on_hold_order_settings[heading]";s:22:"Thanks for your order!";s:46:"woocommerce_customer_invoice_settings[subject]";s:0:"";s:51:"woocommerce_customer_invoice_settings[subject_paid]";s:0:"";s:46:"woocommerce_customer_invoice_settings[heading]";s:33:"Invoice for order #{order_number}";s:51:"woocommerce_customer_invoice_settings[heading_paid]";s:22:"Thanks for your order!";s:42:"woocommerce_failed_order_settings[subject]";s:44:"[{site_title}] Failed order ({order_number})";s:42:"woocommerce_failed_order_settings[heading]";s:28:"Order #{order_number} failed";s:50:"woocommerce_customer_new_account_settings[subject]";s:0:"";s:50:"woocommerce_customer_new_account_settings[heading]";s:0:"";s:43:"woocommerce_customer_note_settings[subject]";s:0:"";s:43:"woocommerce_customer_note_settings[heading]";s:41:"A note was added to order #{order_number}";s:53:"woocommerce_customer_reset_password_settings[subject]";s:0:"";s:53:"woocommerce_customer_reset_password_settings[heading]";s:27:"Password reset instructions";}}',
);
		/**
		 * Instance Control
		*/
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}
		/**
		* Class constructor
		*
		* @access public
		* @return void
		*/
		public function __construct() {

			// Only proceed if this is own request
			if ( ! Kadence_Woomail_Designer::is_own_customizer_request() && ! Kadence_Woomail_Designer::is_own_preview_request() ) {
				return;
			}

			add_action( 'customize_register', array( $this, 'import_export_requests' ), 999999 );
			add_action( 'customize_controls_print_scripts', array( $this, 'controls_print_scripts' ) );

		}

		/**
	 	 * Check to see if we need to do an export or import.
	 	 * @param object $wp_customize An instance of WP_Customize_Manager.
		 * @return void
		 */
		static public function import_export_requests( $wp_customize ) {
			// Check if user is allowed to change values
			if ( ! Kadence_Woomail_Designer::is_admin()) {
				exit;
			}
			
			if ( isset( $_REQUEST['kt-woomail-export'] ) ) {
				self::export_woomail( $wp_customize );
			}
			if ( isset( $_REQUEST['kt-woomail-import'] ) && isset( $_FILES['kadence-woomail-import-file'] ) ) {
				self::import_woomail( $wp_customize );
			}

			if ( isset( $_REQUEST['kt-woomail-import-template'] ) ) {
				self::import_woomail_template( $wp_customize );
			}


		}

		/**
		 * Export woomail settings.
		 *
		 * @access private
		 * @param object $wp_customize An instance of WP_Customize_Manager.
		 * @return void
		 */
		static private function export_woomail( $wp_customize )  {
			if ( ! wp_verify_nonce( $_REQUEST['kt-woomail-export'], 'kt-woomail-exporting' ) ) {
				return;
			}
			
			$template	= 'kadence-woomail-designer';
			$charset	= get_option( 'blog_charset' );
			$data		= array(
							  'template'  => $template,
							  'options'	  => array()
						  );

			// Get options from the Customizer API.
			$settings = $wp_customize->settings();

			foreach ( $settings as $key => $setting ) {
				if ( stristr( $key, 'kt_woomail' ) || in_array( $key, self::$woo_core_options ) ) {
					// to prevent issues we don't want to export the order id.
					if( $key != 'kt_woomail[preview_order_id]' ) {
						$data['options'][ $key ] = $setting->value();
					}
				}
			}


			// Set the download headers.
			header( 'Content-disposition: attachment; filename=kadence-woomail-designer-export.dat' );
			header( 'Content-Type: application/octet-stream; charset=' . $charset );

			// Serialize the export data.
			echo base64_encode( serialize( $data ) );

			// Start the download.
			die();
		}
		/**
		 * Imports uploaded kadence woo email settings
		 *
		 * @access private
		 * @param object $wp_customize An instance of WP_Customize_Manager.
		 * @return void
		 */
		static private function import_woomail( $wp_customize ) {
			// Make sure we have a valid nonce.
			if ( ! wp_verify_nonce( $_REQUEST['kt-woomail-import'], 'kt-woomail-importing' ) ) {
				return;
			}
			// Make sure WordPress upload support is loaded.
			if ( ! function_exists( 'wp_handle_upload' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
			}
			
			// Load the export/import option class.
			require_once KT_WOOMAIL_PATH . 'includes/class-kadence-woomail-import-option.php';
			
			// Setup global vars.
			global $wp_customize;
			global $kt_woomail_import_error;

			// Setup internal vars.
			$kt_woomail_import_error	 = false;
			$template	 = 'kadence-woomail-designer';
			$overrides   = array( 'test_form' => false, 'test_type' => false, 'mimes' => array('dat' => 'text/plain') );
			$file        = wp_handle_upload( $_FILES['kadence-woomail-import-file'], $overrides );

			// Make sure we have an uploaded file.
			if ( isset( $file['error'] ) ) {
				$kt_woomail_import_error = $file['error'];
				return;
			}
			if ( ! file_exists( $file['file'] ) ) {
				$kt_woomail_import_error = __( 'Error importing settings! Please try again.', 'kadence-woocommerce-email-designer' );
				return;
			}

			// Get the upload data.
			$raw  = file_get_contents( $file['file'] );
			//$data = @unserialize( $raw );
			// $data = self::mb_unserialize( $raw );
			$data = unserialize( base64_decode( $raw ) );
			if ( 'array' != gettype( $data ) || ! isset( $data['template'] ) ) {
				 $data = self::mb_unserialize( $raw );
			}
			// Remove the uploaded file.
			unlink( $file['file'] );

			// Data checks.
			if ( 'array' != gettype( $data ) ) {
				$kt_woomail_import_error = __( 'Error importing settings! Please check that you uploaded an email customizer export file.', 'kadence-woocommerce-email-designer' );
				return;
			}
			if ( ! isset( $data['template'] ) ) {
				$kt_woomail_import_error = __( 'Error importing settings! Please check that you uploaded an email customizer export file.', 'kadence-woocommerce-email-designer' );
				return;
			}
			if ( $data['template'] != $template ) {
				$kt_woomail_import_error = __( 'Error importing settings! The settings you uploaded are not for the Kadence Woomail Designer.', 'kadence-woocommerce-email-designer' );
				return;
			}

			// Import custom options.
			if ( isset( $data['options'] ) ) {
				
				foreach ( $data['options'] as $option_key => $option_value ) {
					
					$option = new Kadence_Woomail_Import_Option( $wp_customize, $option_key, array(
						'default'		=> '',
						'type'			=> 'option',
						'capability'	=> Kadence_Woomail_Designer::get_admin_capability(),
					) );

					$option->import( $option_value );
				}
			}


			// Call the customize_save action.
			do_action( 'customize_save', $wp_customize );

			// Call the customize_save_after action.
			do_action( 'customize_save_after', $wp_customize );

			wp_redirect( Kadence_Woomail_Customizer::get_customizer_url() );

			exit;
		}
		/**
		 * Mulit-byte Unserialize
		 *
		 * UTF-8 will screw up a serialized string
		 *
		 * @access private
		 * @param string
		 * @return string
		 */
		static private function mb_unserialize( $string ) {
			$string2 = preg_replace_callback(
				'!s:(\d+):"(.*?)";!s',
				function( $m ){
					$len = strlen($m[2]);
					$result = "s:$len:\"{$m[2]}\";";
					return $result;

				},
			$string );
			return @unserialize( $string2 );
		}

		/**
		 * Imports prebuilt kadence woo email settings
		 *
		 * @access private
		 * @param object $wp_customize An instance of WP_Customize_Manager.
		 * @return void
		 */
		static private function import_woomail_template( $wp_customize ) {
			// Make sure we have a valid nonce.
			if ( ! wp_verify_nonce( $_REQUEST['kt-woomail-import-template'], 'kt-woomail-importing-template' ) ) {
				return;
			}
			// Load the export/import option class.
			require_once KT_WOOMAIL_PATH . 'includes/class-kadence-woomail-import-option.php';
			
			// Setup global vars.
			global $wp_customize;
			global $kt_woomail_import_error;
			
			// Setup internal vars.
			$kt_woomail_import_error	 = false;
			$template	 = 'kadence-woomail-designer';
			$prebuilt    = $_REQUEST['kt-woomail-prebuilt-template'];
			$raw_data 	= self::prebuilt( $prebuilt );
			
			$data = @unserialize( $raw_data );
			
			
			// Data checks.
			if ( 'array' != gettype( $data ) ) {
				$kt_woomail_import_error = __( 'Error importing settings! The template you selected is not found.', 'kadence-woocommerce-email-designer' );
				return;
			}
			if ( ! isset( $data['template'] ) ) {
				$kt_woomail_import_error = __( 'Error importing settings! The template you selected is not valid.', 'kadence-woocommerce-email-designer' );
				return;
			}
			if ( $data['template'] != $template ) {
				$kt_woomail_import_error = __( 'Error importing settings! The template you selected is not valid.', 'kadence-woocommerce-email-designer' );
				return;
			}
			

			// Import custom options.
			if ( isset( $data['options'] ) ) {
				
				foreach ( $data['options'] as $option_key => $option_value ) {
					
					$option = new Kadence_Woomail_Import_Option( $wp_customize, $option_key, array(
						'default'		=> '',
						'type'			=> 'option',
						'capability'	=> Kadence_Woomail_Designer::get_admin_capability(),
					) );

					$option->import( $option_value );
				}
			}


			// Call the customize_save action.
			do_action( 'customize_save', $wp_customize );

			// Call the customize_save_after action.
			//do_action( 'customize_save_after', $wp_customize );

			wp_redirect( Kadence_Woomail_Customizer::get_customizer_url() );

			exit;
		}
		/**
		 * Prints error scripts for the control.
		 *
		 * @since 0.1
		 * @return void
		 */
		static public function controls_print_scripts() {
			global $kt_woomail_import_error;
			
			if ( $kt_woomail_import_error ) {
				echo '<script> alert("' . $kt_woomail_import_error . '"); </script>';
			}
		}
		/**
		 * Get value for prebuilt
		 *
		 * @access public
		 * @param string $key
		 * @return string
		 */
		public static function prebuilt( $key ) {
			if ( isset( self::$prebuilt_options[$key] ) ) {
				$data = self::$prebuilt_options[$key];
			} else {
				$data = null;
			}

			// Allow developers to override with there templates
			return apply_filters( 'kadence_woomail_template_data', $data, $key );
		}
	}
	Kadence_Woomail_Import_Export::get_instance();
}