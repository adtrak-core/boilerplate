<?php
/**
 * Public Post Preview Configurator.
 *
 * @package   Public_Post_Preview_Configurator_Admin
 * @author    Your Name <email@example.com>
 * @license   GPLv3
 * @link      http://bjoerne.com
 * @copyright 2014 bjoerne.com
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `class-plugin-name.php`
 *
 * @package Public_Post_Preview_Configurator_Admin
 * @author  Björn Weinbrenner <info@bjoerne.com>
 */
class Public_Post_Preview_Configurator_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.2
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.2
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.2
	 */
	private function __construct() {

		/*
		 * Call $plugin_slug from public plugin class.
		 */
		$plugin = Public_Post_Preview_Configurator::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();
		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_setting' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.2
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.2
	 */
	public function add_plugin_admin_menu() {
		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		 */
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'Public Post Preview Configurator', $this->plugin_slug ),
			__( 'Public Post Preview Configurator', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);
	}

	/**
	 * Registers the expiration hours setting.
	 *
	 * @since    1.0.2
	 */
	public function register_setting() {
		register_setting( 'ppp_configurator_group', 'ppp_configurator_expiration_hours', array( $this, 'validate_expiration_hours' ) );
	}

	/**
	 * Validates the expiration hours setting. It must be a positiv integer number. Otherwise an error message is displayed.
	 *
	 * @since    1.0.2
	 */
	public function validate_expiration_hours( $input ) {
		if ( $input == '' ) {
			return $input;
		}
		if ( ! ctype_digit( $input ) || intval( $input ) == 0 ) {
			$error_msg = _x( "Invalid value for '%s'. Must be a positive integer.", 'Error message', $this->plugin_slug );
			add_settings_error( 'ppp_validate_expiration_hours_failed', esc_attr( 'settings_updated'), sprintf( $error_msg, __( 'Expiration hours', $this->plugin_slug ) ), 'error' );
			return '';
		}
		return $input;
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.2
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.2
	 */
	public function add_action_links( $links ) {
		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . _x( 'Settings', 'Link from plugin page to settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);
	}
}
