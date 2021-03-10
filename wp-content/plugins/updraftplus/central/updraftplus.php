<?php

if (!interface_exists('UpdraftCentral_Host_Interface')) require_once('interface.php');

/**
 * This class is the basic bridge between the UpdraftCentral and UpdraftPlus.
 */
class UpdraftPlus_Host implements UpdraftCentral_Host_Interface {

	const PLUGIN_NAME = 'updraftplus';

	public $text_domain;

	protected static $_instance = null;

	/**
	 * Creates an instance of this class. Singleton Pattern
	 *
	 * @return object Instance of this class
	 */
	public static function instance() {
		if (empty(self::$_instance)) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Class constructor
	 */
	public function __construct() {
		add_action('updraftplus_debugtools_dashboard', array($this, 'debugtools_dashboard'), 20);

		$this->text_domain = self::PLUGIN_NAME;
		$this->maybe_initialize_required_objects();
	}

	/**
	 * Below are interface methods' implementations that are required by UpdraftCentral to function properly. Please
	 * see the "interface.php" to check all the required interface methods.
	 */

	/**
	 * Retrieves current clean url for anchor link where href attribute value is not url (for ex. #div) or empty
	 *
	 * @return string
	 */
	public function get_current_clean_url() {
		$class_name = $this->get_class_name();
		$url = call_user_func(array($class_name, 'get_current_clean_url'));
		if (!empty($url)) return $url;

		return '';
	}

	/**
	 * Returns the name of the plugin that is associated with this host class
	 *
	 * @return string
	 */
	public function get_plugin_name() {
		return self::PLUGIN_NAME;
	}

	/**
	 * Checks whether the plugin's DIR constant is currently define or not
	 *
	 * @return bool
	 */
	public function is_host_dir_set() {
		return defined('UPDRAFTPLUS_DIR') ? true : false;
	}

	/**
	 * Retrieves the host directory associated with the host plugin
	 *
	 * @return string
	 */
	public function get_host_dir() {
		return $this->is_host_dir_set() ? UPDRAFTPLUS_DIR : '';
	}

	/**
	 * Checks whether the plugin's SET_TIME_LIMIT constant is currently define or not
	 *
	 * @return bool
	 */
	public function is_time_limit_set() {
		return defined('UPDRAFTPLUS_SET_TIME_LIMIT') ? true : false;
	}

	/**
	 * Retrieves the script execution limit set by the host plugin, otherwise,
	 * we will pull from the PHP config file (php.ini) or set to 60 (1 minute) by default
	 *
	 * @return int
	 */
	public function get_time_limit() {
		$php_limit = ini_get('max_execution_time');
		$default = !empty($php_limit) ? $php_limit : 60;

		return $this->is_time_limit_set() ? UPDRAFTPLUS_SET_TIME_LIMIT : $default;
	}

	/**
	 * Retrieves the filter used by UpdraftPlus to log errors or certain events
	 *
	 * @return string
	 */
	public function get_logline_filter() {
		return 'updraftplus_logline';
	}

	/**
	 * Retrieves the filter used by UpdraftPlus to pull the authentication headers
	 *
	 * @return string
	 */
	public function get_auth_headers_filter() {
		return 'updraftplus_auth_headers';
	}

	/**
	 * Retrieves the filter used by UpdraftPlus to pull the remotecontrol login key
	 *
	 * @return string
	 */
	public function get_login_key_filter() {
		return 'updraftplus_remotecontrol_login_key';
	}

	/**
	 * Retrieves the UpdraftCentral generated keys
	 *
	 * @return array|bool
	 */
	public function get_central_localkeys() {
		return $this->get_option('updraft_central_localkeys');
	}

	/**
	 * Updates the UpdraftCentral's keys
	 *
	 * @param string $value	    Specify option value
	 * @param bool   $use_cache Whether or not to use the WP options cache
	 * @param string $autoload	Whether to autoload (only takes effect on a change of value)
	 *
	 * @return bool
	 */
	public function update_central_localkeys($value, $use_cache = true, $autoload = 'yes') {
		if ($this->has_options()) {
			return $this->update_option('updraft_central_localkeys', $value, $use_cache, $autoload);
		}

		return false;
	}

	/**
	 * Checks whether debug mod is set
	 *
	 * @return bool
	 */
	public function get_debug_mode() {
		return $this->get_option('updraft_debug_mode');
	}

	/**
	 * Gets an RPC object, and sets some defaults on it that we always want
	 *
	 * @param string $indicator_name indicator name
	 * @return array|bool
	 */
	public function get_udrpc($indicator_name) {
		global $updraftplus;

		if ($updraftplus) {
			return $updraftplus->get_udrpc($indicator_name);
		}

		return false;
	}

	/**
	 * Used as a central location (to avoid repetition) to register or de-register hooks into the WP HTTP API
	 *
	 * @param bool $register True to register, false to de-register
	 * @return void
	 */
	public function register_wp_http_option_hooks($register = true) {
		global $updraftplus;

		if ($updraftplus) {
			$updraftplus->register_wp_http_option_hooks($register);
		}
	}

	/**
	 * Retrieves the WordPress version
	 *
	 * @return string|bool
	 */
	public function get_wordpress_version() {
		global $updraftplus;

		if ($updraftplus) {
			return $updraftplus->get_wordpress_version();
		}

		return false;
	}

	/**
	 * Retrieves the class name of the host plugin
	 *
	 * @return string|bool
	 */
	public function get_class_name() {
		global $updraftplus;

		if ($updraftplus) {
			return get_class($updraftplus);
		}

		return false;
	}

	/**
	 * Returns the instance of the host plugin
	 *
	 * @return object|bool
	 */
	public function get_instance() {
		global $updraftplus;

		if ($updraftplus) {
			return $updraftplus;
		}

		return false;
	}

	/**
	 * Returns the admin instance of the host plugin
	 *
	 * @return object|bool
	 */
	public function get_admin_instance() {
		global $updraftplus_admin;

		if ($updraftplus_admin) {
			return $updraftplus_admin;
		} else {
			if (defined('UPDRAFTPLUS_DIR') && file_exists(UPDRAFTPLUS_DIR.'/admin.php')) {
				include_once(UPDRAFTPLUS_DIR.'/admin.php');
				$updraftplus_admin = new UpdraftPlus_Admin();
				return $updraftplus_admin;
			}
		}

		return false;
	}

	/**
	 * Retrieves the host plugin's Options class
	 *
	 * @return class|bool
	 */
	public function get_option_class() {
		if ($this->has_options()) {
			return UpdraftPlus_Options;
		}

		return false;
	}

	/**
	 * Checks whether the host plugin's Options class exists
	 *
	 * @return bool
	 */
	public function has_options() {
		return class_exists('UpdraftPlus_Options');
	}

	/**
	 * Updates a specific option's value
	 *
	 * @param string $option	Specify option name
	 * @param string $value	    Specify option value
	 * @param bool   $use_cache Whether or not to use the WP options cache
	 * @param string $autoload	Whether to autoload (only takes effect on a change of value)
	 *
	 * @return bool
	 */
	public function update_option($option, $value, $use_cache = true, $autoload = 'yes') {
		if ($this->has_options()) {
			return UpdraftPlus_Options::update_updraft_option($option, $value, $use_cache, $autoload);
		}

		return false;
	}

	/**
	 * Retrieves a specific option's value
	 *
	 * @param string $option  Specify option name
	 * @param mixed  $default Optional. The default value to return when option is not found
	 *
	 * @return mixed|bool
	 */
	public function get_option($option, $default = null) {
		if ($this->has_options()) {
			return UpdraftPlus_Options::get_updraft_option($option, $default);
		}

		return false;
	}

	/**
	 * Returns the current version of the host plugin
	 *
	 * @return string|bool
	 */
	public function get_version() {
		global $updraftplus;

		if ($updraftplus) {
			return $updraftplus->version;
		}

		return false;
	}

	/**
	 * Returns the admin page url of the host plugin
	 *
	 * @return string
	 */
	public function admin_page_url() {
		if ($this->has_options()) {
			return UpdraftPlus_Options::admin_page_url();
		}

		return '';
	}

	/**
	 * Returns the filesystem class of the host's plugin
	 *
	 * @return class|bool
	 */
	public function get_filesystem_functions() {
		if ($this->has_filesystem_functions()) {
			return UpdraftPlus_Filesystem_Functions;
		}

		return false;
	}

	/**
	 * Checks whether the filesystem class of the host plugin exists
	 *
	 * @return bool
	 */
	public function has_filesystem_functions() {
		return class_exists('UpdraftPlus_Filesystem_Functions');
	}

	/**
	 * Returns the updraftplus.com destination path
	 *
	 * @return string
	 */
	public function get_udcom_destination() {
		return defined('UPDRAFTPLUS_OVERRIDE_UDCOM_DESTINATION') ? UPDRAFTPLUS_OVERRIDE_UDCOM_DESTINATION : 'https://updraftplus.com/?updraftcentral_action=receive_key';
	}

	/**
	 * Returns the googlecloud callback url used by the analytics module
	 *
	 * @return string
	 */
	public function get_googlecloud_callback_url() {
		return defined('UPDRAFTPLUS_GOOGLECLOUD_CALLBACK_URL') ? UPDRAFTPLUS_GOOGLECLOUD_CALLBACK_URL : 'https://auth.updraftplus.com/auth/googleanalytics';
	}

	/**
	 * Returns the googlecloud client id used to connect to the google app
	 *
	 * @return string
	 */
	public function get_googlecloud_client_id() {
		return defined('UPDRAFTPLUS_GOOGLECLOUD_CLIENT_ID') ? UPDRAFTPLUS_GOOGLECLOUD_CLIENT_ID : '306245874349-6s896c3tjpra26ns3dpplhqcl6rv6qlb.apps.googleusercontent.com';
	}

	/**
	 * Checks whether force debugging is set
	 *
	 * @return bool
	 */
	public function is_force_debug() {
		return (defined('UPDRAFTPLUS_UDRPC_FORCE_DEBUG') && UPDRAFTPLUS_UDRPC_FORCE_DEBUG) ? true : false;
	}

	/**
	 * Checks whether autobackup addon is present
	 *
	 * @return bool
	 */
	public function has_autobackup_addon() {
		return class_exists('UpdraftPlus_Addon_Autobackup');
	}

	/**
	 * Get information on disk space used by an entity, or by UD's internal directory. Returns as a human-readable string.
	 *
	 * @param string $entity The entity (e.g. 'plugins'; 'all' for all entities, or 'ud' for UD's internal directory)
	 * @param string $format Return format - 'text' or 'numeric'
	 * @return string|integer|bool If $format is text, It returns strings. Otherwise integer value.
	 */
	public function get_disk_space_used($entity, $format = 'text') {
		if ($this->has_filesystem_functions()) {
			return UpdraftPlus_Filesystem_Functions::get_disk_space_used($entity, $format);
		}

		return false;
	}

	/**
	 * Checks whether autobackup is run by default
	 *
	 * @param bool $default The default value to set when the option is not found
	 *
	 * @return bool
	 */
	public function get_autobackup_default($default = true) {
		return $this->get_option('updraft_autobackup_default', $default);
	}

	/**
	 * Adds a section to the 'advanced tools' page for generating UpdraftCentral keys. Called by a filter
	 * inside the constructor method of this class.
	 *
	 * @return void
	 */
	public function debugtools_dashboard() {
		global $updraftcentral_main;

		if (!class_exists('UpdraftCentral_Main')) {
			if (defined('UPDRAFTCENTRAL_CLIENT_DIR') && file_exists(UPDRAFTCENTRAL_CLIENT_DIR.'/bootstrap.php')) {
				include_once(UPDRAFTCENTRAL_CLIENT_DIR.'/bootstrap.php');
				$updraftcentral_main = new UpdraftCentral_Main();
			}
		}

		if ($updraftcentral_main) {
			$updraftcentral_main->debugtools_dashboard();
		}
	}

	/**
	 * Initializes required objects (if not yet initialized) for UpdraftCentral usage
	 *
	 * @return void
	 */
	private function maybe_initialize_required_objects() {
		global $updraftplus;

		if (!class_exists('UpdraftPlus')) {
			if (defined('UPDRAFTPLUS_DIR') && file_exists(UPDRAFTPLUS_DIR.'/class-updraftplus.php')) {
				include_once(UPDRAFTPLUS_DIR.'/class-updraftplus.php');
				$updraftplus = new UpdraftPlus();
			}
		}

		if (!class_exists('UpdraftPlus_Options')) {
			if (defined('UPDRAFTPLUS_DIR') && file_exists(UPDRAFTPLUS_DIR.'/options.php')) {
				require_once(UPDRAFTPLUS_DIR.'/options.php');
			}
		}

		if (!class_exists('UpdraftPlus_Filesystem_Functions')) {
			if (defined('UPDRAFTPLUS_DIR') && file_exists(UPDRAFTPLUS_DIR.'/includes/class-filesystem-functions.php')) {
				require_once(UPDRAFTPLUS_DIR.'/includes/class-filesystem-functions.php');
			}
		}
	}
}
