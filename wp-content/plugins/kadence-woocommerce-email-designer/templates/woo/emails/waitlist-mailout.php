<?php
/**
 * Waitlist Mailout email
 *
 * @author         Neil Pie
 * @package        WooCommerce_Waitlist/Templates/Emails
 * @version 2.1.9
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
$eobject         = new stdClass();
$eobject->id     = 'woocommerce_waitlist_mailout';
$eobject->object = wc_get_product( $product_id );
$hide_check = Kadence_Woomail_Customizer::opt( 'woocommerce_waitlist_mailout_hide_content' );
do_action( 'woocommerce_email_header', $email_heading, $eobject ); ?>

<?php
/**
 * @hooked Kadence_Woomail_Designer::email_main_text_area
 */
do_action( 'kadence_woomail_designer_email_text', $eobject );
?>
<?php if ( ! $hide_check ) { ?>
	<p>
		<?php printf( __( '%s is now back in stock at %s. ', 'kadence-woocommerce-email-designer' ), $product_title, get_bloginfo( 'name' ) );
		_e( 'You have been sent this email because your email address was registered on a waitlist for this product.', 'kadence-woocommerce-email-designer' ); ?>
	</p>
	<p>
		<?php printf( __( 'If you would like to purchase %s please visit the following link: %s', 'kadence-woocommerce-email-designer' ), $product_title, '<a href="' . $product_link . '">' . $product_link . '<a>' ); ?>
	</p>

	<?php if ( WooCommerce_Waitlist_Plugin::persistent_waitlists_are_disabled( $product_id ) && ! $triggered_manually ) {
		echo '<p>' . __( 'You have been removed from the waitlist for this product', 'kadence-woocommerce-email-designer' ) . '</p>';
	}
}
/**
 * Show user-defined additonal content - this is set in each email's settings.
 */
if ( isset( $additional_content ) && ! empty( $additional_content ) ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}
do_action( 'woocommerce_email_footer', $eobject ); ?>