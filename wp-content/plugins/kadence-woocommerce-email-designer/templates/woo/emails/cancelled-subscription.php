<?php
/**
 * Cancelled Subscription email
 *
 * @author  Prospress
 * @package WooCommerce_Subscriptions/Templates/Emails
 * @version 2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * NOTES ABOUT TEMPLATE EDIT FOR KADENCE WOOMAIL DESIGNER,
 * 1. add hook 'kadence_woomail_designer_email_details' to pull in main text
 * 2. Remove static main text area.
 */
$responsive_check = Kadence_Woomail_Customizer::opt( 'responsive_mode' );

do_action( 'woocommerce_email_header', $email_heading, $email );

/**
 * Kadence Email Details
 *
 * @hooked Kadence_Woomail_Designer::email_main_text_area
 */
do_action( 'kadence_woomail_designer_email_details', $subscription, $sent_to_admin, $plain_text, $email );

if ( true == $responsive_check ) {
	?>
	<div class="email-spacing-wrap" style="margin-bottom: 40px;">
		<table class="td" cellspacing="0" cellpadding="6" style="width: 100%;" border="1">
			<tbody>
				<tr>
					<td class="td" scope="row" style="text-align:<?php echo esc_attr( $text_align ); ?>;">
						<p style="margin-bottom: 0"><strong><?php esc_html_e( 'Subscription', 'kadence-woocommerce-email-designer' ); ?></strong></p>
						<p style="margin-bottom: 0"><a href="<?php echo esc_url( wcs_get_edit_post_link( $subscription->get_id() ) ); ?>">#<?php echo esc_html( $subscription->get_order_number() ); ?></a></p>
					</td>
					<td class="td" scope="row" style="text-align:<?php echo esc_attr( $text_align ); ?>;">
						<p style="margin-bottom: 0"><strong><?php echo esc_html_x( 'Price', 'table heading', 'kadence-woocommerce-email-designer' ); ?></strong></p>
						<p style="margin-bottom: 0"><?php echo wp_kses_post( $subscription->get_formatted_order_total() ); ?></p>
					</td>
				</tr>
				<tr>
					<td class="td" scope="row" style="text-align:<?php echo esc_attr( $text_align ); ?>;">
						<p style="margin-bottom: 0"><strong><?php echo esc_html_x( 'Last Order Date', 'table heading', 'kadence-woocommerce-email-designer' ); ?></strong></p>
						<p style="margin-bottom: 0">
							<?php
							$last_order_time_created = $subscription->get_time( 'last_order_date_created', 'site' );
							if ( ! empty( $last_order_time_created ) ) {
								echo esc_html( date_i18n( wc_date_format(), $last_order_time_created ) );
							} else {
								esc_html_e( '-', 'kadence-woocommerce-email-designer' );
							}
							?>
						</p>
					</td>
					<td class="td" scope="row" style="text-align:<?php echo esc_attr( $text_align ); ?>;">
						<p style="margin-bottom: 0"><strong><?php echo esc_html_x( 'End of Prepaid Term', 'table heading', 'kadence-woocommerce-email-designer' ); ?></strong></p>
						<p style="margin-bottom: 0"><?php echo esc_html( date_i18n( wc_date_format(), $subscription->get_time( 'end', 'site' ) ) ); ?></p>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php
} else {
	?>
	<div class="email-spacing-wrap" style="margin-bottom: 40px;">
		<table class="td" cellspacing="0" cellpadding="6" style="width: 100%;" border="1">
			<thead>
				<tr>
					<th class="td" scope="col" style="text-align:left;"><?php esc_html_e( 'Subscription', 'kadence-woocommerce-email-designer' ); ?></th>
					<th class="td" scope="col" style="text-align:left;"><?php echo esc_html_x( 'Price', 'table headings in notification email', 'kadence-woocommerce-email-designer' ); ?></th>
					<th class="td" scope="col" style="text-align:left;"><?php echo esc_html_x( 'Last Order Date', 'table heading', 'kadence-woocommerce-email-designer' ); ?></th>
					<th class="td" scope="col" style="text-align:left;"><?php echo esc_html_x( 'End of Prepaid Term', 'table headings in notification email', 'kadence-woocommerce-email-designer' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="td" width="1%" style="text-align:left; vertical-align:middle;">
						<a href="<?php echo esc_url( wcs_get_edit_post_link( $subscription->get_id() ) ); ?>">#<?php echo esc_html( $subscription->get_order_number() ); ?></a>
					</td>
					<td class="td" style="text-align:left; vertical-align:middle;">
						<?php echo wp_kses_post( $subscription->get_formatted_order_total() ); ?>
					</td>
					<td class="td" style="text-align:left; vertical-align:middle;">
						<?php
						$last_order_time_created = $subscription->get_time( 'last_order_date_created', 'site' );
						if ( ! empty( $last_order_time_created ) ) {
							echo esc_html( date_i18n( wc_date_format(), $last_order_time_created ) );
						} else {
							esc_html_e( '-', 'kadence-woocommerce-email-designer' );
						}
						?>
					</td>
					<td class="td" style="text-align:left; vertical-align:middle;">
						<?php echo esc_html( date_i18n( wc_date_format(), $subscription->get_time( 'end', 'site' ) ) ); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php
}

do_action( 'woocommerce_subscriptions_email_order_details', $subscription, $sent_to_admin, $plain_text, $email );

do_action( 'woocommerce_email_customer_details', $subscription, $sent_to_admin, $plain_text, $email );

/**
 * Show user-defined additonal content - this is set in each email's settings.
 */
if ( isset( $additional_content ) && ! empty( $additional_content ) ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

do_action( 'woocommerce_email_footer', $email );
?>
