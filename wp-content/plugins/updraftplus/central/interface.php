<?php

/**
 * This interface is the basic blueprint of the host (plugin) methods needed by UpdraftCentral
 * for it to work and function properly.
 */
interface UpdraftCentral_Host_Interface {
	public function retrieve_show_message($key, $echo = false);
	public function get_current_clean_url();
	public function get_plugin_name();
	public function is_host_dir_set();
	public function get_host_dir();
	public function is_time_limit_set();
	public function get_time_limit();
	public function get_logline_filter();
	public function get_auth_headers_filter();
	public function get_login_key_filter();
	public function get_central_localkeys();
	public function update_central_localkeys($value, $use_cache = true, $autoload = 'yes');
	public function get_debug_mode();
	public function get_udrpc($indicator_name);
	public function register_wp_http_option_hooks($register = true);
	public function get_wordpress_version();
	public function get_class_name();
	public function get_instance();
	public function get_admin_instance();
	public function get_option_class();
	public function has_options();
	public function update_option($option, $value, $use_cache = true, $autoload = 'yes');
	public function get_option($option, $default = null);
	public function get_version();
	public function admin_page_url();
	public function get_filesystem_functions();
	public function has_filesystem_functions();
	public function get_udcom_destination();
	public function get_googlecloud_callback_url();
	public function get_googlecloud_client_id();
	public function is_force_debug();
	public function has_autobackup_addon();
	public function get_disk_space_used($entity, $format = 'text');
	public function get_autobackup_default($default = true);
	public function debugtools_dashboard();
}
