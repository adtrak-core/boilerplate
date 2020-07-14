<?php

/**
 * Order Customer Details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details-customer.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.4
 */

defined('ABSPATH') || exit;

$show_shipping = !wc_ship_to_billing_address_only() && $order->needs_shipping_address();
?>
<section class="woocommerce-customer-details flex flex-wrap row">
  <div class="column w-full md:w-6/12">
    <h4 class="mb-3"><?php esc_html_e('Billing address', 'woocommerce'); ?></h4>
    <address>
      <?php echo wp_kses_post($order->get_formatted_billing_address(esc_html__('N/A', 'woocommerce'))); ?>

      <?php if ($order->get_billing_phone()) : ?>
        <p class="woocommerce-customer-details--phone"><?php echo esc_html($order->get_billing_phone()); ?></p>
      <?php endif; ?>

      <?php if ($order->get_billing_email()) : ?>
        <p class="woocommerce-customer-details--email"><?php echo esc_html($order->get_billing_email()); ?></p>
      <?php endif; ?>
    </address>
  </div>

  <?php if ($show_shipping) : ?>
    <div class="column w-full md:w-6/12">
      <h4 class="mb-3"><?php esc_html_e('Shipping address', 'woocommerce'); ?></h4>
      <address>
        <?php echo wp_kses_post($order->get_formatted_shipping_address(esc_html__('N/A', 'woocommerce'))); ?>
      </address>
    </div>
  <?php endif; ?>

  <?php do_action('woocommerce_order_details_after_customer_details', $order); ?>
</section>