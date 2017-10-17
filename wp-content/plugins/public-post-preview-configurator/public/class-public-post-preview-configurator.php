<?php
/**
 * Public Post Preview Configurator.
 *
 * @package   Public_Post_Preview_Configurator
 * @author    Your Name <email@example.com>
 * @license   GPLv3
 * @link      http://bjoerne.com
 * @copyright 2014 bjoerne.com
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `class-plugin-name-admin.php`
 *
 * @package Public_Post_Preview_Configurator
 * @author  Björn Weinbrenner <info@bjoerne.com>
 */
class Public_Post_Preview_Configurator {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.2
	 *
	 * @var     string
	 */
	const VERSION = '1.0.3';

	/**
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.2
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'public-post-preview-configurator';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.2
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.2
	 */
	private function __construct() {
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_filter( 'ppp_nonce_life', array( $this, 'configured_nounce_life' ) );
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.2
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
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
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.2
	 */
	public function load_plugin_textdomain() {
		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );
	}


	/**
	 * Sets a new ppp_nonce_life value based on a plugin option or returns the given value if no value has been configured.
	 *
	 * @since    1.0.2
	 */
	public function configured_nounce_life( $nonce_life ) {
		$expiration_hours = (int) get_option( 'ppp_configurator_expiration_hours' );
		if ( $expiration_hours && $expiration_hours > 0 ) {
			return 60 * 60 * $expiration_hours;
		}
		return $nonce_life;
	}
}
