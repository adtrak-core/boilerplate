<?php
/**
 * Woocommerce integration with settings.
 *
 * @package Kadence WooCommerce Email Designer
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Integration with WooCommerce Settings Page
 */
if ( ! class_exists( 'Kadence_Woomail_Woo' ) ) {
	/**
	 * Class Kadence Woomail Woo.
	 */
	class Kadence_Woomail_Woo {

		/**
		 * The instance Control Var.
		 *
		 * @var null
		 */
		private static $instance = null;

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

			// Add email Customzier setting to normal woocommerce email settings area.
			add_filter( 'woocommerce_email_settings', array( $this, 'add_mail_customizer_to_woocommerce_email_settings' ) );

			// Print email Customzier button in normal woocommerce email settings area.
			add_action( 'woocommerce_admin_field_kt_woomail_open_customizer_button', array( $this, 'print_open_customizer_button' ) );

		}

		/**
		 * Add Open Designer to settings button
		 *
		 * @access public
		 * @param array $settings
		 * @return array
		 */
		public function add_mail_customizer_to_woocommerce_email_settings( $settings ) {

			// Open section
			$settings[] = array(
				'id'    => 'kt_woomail',
				'type'  => 'title',
				'title' => __('Woocommerce Email Designer', 'kadence-woocommerce-email-designer'),
			);

			// Add Open Designer button
			$settings[] = array(
				'id'    => 'kt_woomail_open_customizer_button',
				'type'  => 'kt_woomail_open_customizer_button',
			);

			// Close section
			$settings[] = array(
				'id'    => 'kt_woomail',
				'type'  => 'sectionend',
			);

			// Return remaining settings
			return $settings;

		}

		/**
		 * Print Open Designer button
		 *
		 * @access public
		 * @param array $options settings options.
		 * @return void
		 */
		public function print_open_customizer_button( $options ) {
			?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<?php _e( 'Customize WooCommerce Emails', 'kadence-woocommerce-email-designer' ); ?>
				</th>
				<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $options['type'] ) ); ?>">
					<a href="<?php echo esc_url( Kadence_Woomail_Customizer::get_customizer_url() ); ?>">
						<button type="button" class="button button-secondary" value="<?php _e( 'Open Woocommerce Email Designer', 'kadence-woocommerce-email-designer' ); ?>">
							<?php _e( 'Open Woocommerce Email Designer', 'kadence-woocommerce-email-designer' ); ?>
						</button>
					</a>
					<p class="description"><?php printf( __( 'Make Woocommerce Emails match your brand. <a href="%s">Kadence Woocommerce Email Designer</a> plugin by <a href="%s">Kadence Themes</a>.', 'kadence-woocommerce-email-designer' ), 'https://www.kadencethemes.com/products/kadence-woomail-designer', 'https://www.kadencethemes.com' ); ?></p>
				</td>
			</tr>
			<?php
		}

		/**
		 * Get WooCommerce email settings page URL
		 *
		 * @access public
		 * @return string
		 */
		public static function get_email_settings_page_url() {
			return admin_url( 'admin.php?page=wc-settings&tab=email' );
		}

	}

	Kadence_Woomail_Woo::get_instance();

}
