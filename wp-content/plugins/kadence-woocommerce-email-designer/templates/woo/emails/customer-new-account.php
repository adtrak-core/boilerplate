<?php
/**
 * Customer new account email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-new-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates/Emails
 * @version     3.7.0
 */

/**
 * NOTES ABOUT TEMPLATE EDIT FOR KADENCE WOOMAIL DESIGNER, 
 * 1. add hook 'kadence_woomail_designer_email_text' to pull in main text
 * 2. Remove static main text area.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

do_action( 'woocommerce_email_header', $email_heading, $email );

$button_check    = Kadence_Woomail_Customizer::opt( 'customer_new_account_btn_switch' );
$account_section = Kadence_Woomail_Customizer::opt( 'customer_new_account_account_section' );

/**
 * @hooked Kadence_Woomail_Designer::email_main_text_area_no_order
 */
do_action( 'kadence_woomail_designer_email_text', $email ); ?>

<?php if ( 'yes' === get_option( 'woocommerce_registration_generate_password' ) && $password_generated ) : ?>

	<p><?php printf( __( 'Your password has been automatically generated: %s', 'kadence-woocommerce-email-designer' ), '<strong>' . esc_html( $user_pass ) . '</strong>' ); ?></p>

<?php
endif;
if ( true == $account_section ) {
	if ( true == $button_check ) {
		echo '<p>' . esc_html__( 'You can access your account area to view your orders and change your password.', 'kadence-woocommerce-email-designer' ) . '</p>';
		echo '<p class="btn-container"><a href="' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '" class="btn">' . esc_html__( 'View Account', 'kadence-woocommerce-email-designer' ) . '</a></p>';
	} else {
	?>
	<p><?php printf( __( 'You can access your account area to view your orders and change your password here: %s.', 'kadence-woocommerce-email-designer' ), make_clickable( esc_url( wc_get_page_permalink( 'myaccount' ) ) ) ); ?></p>
	<?php
	}
}
/**
 * Show user-defined additonal content - this is set in each email's settings.
 */
if ( isset( $additional_content ) && ! empty( $additional_content ) ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

do_action( 'woocommerce_email_footer', $email );
