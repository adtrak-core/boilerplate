<?php

/**
 * My Addresses
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-address.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 2.6.0
 */

defined('ABSPATH') || exit;

$customer_id = get_current_user_id();
$fetch_addresses = [
  'billing' => __('Billing address', 'woocommerce'),
];

if (!wc_ship_to_billing_address_only() && wc_shipping_enabled()) {
  $fetch_addresses = array_merge($fetch_addresses, [
    'shipping' => __('Shipping address', 'woocommerce'),
  ]);
}

$get_addresses = apply_filters('woocommerce_my_account_get_addresses', $fetch_addresses, $customer_id); ?>

<p><?= apply_filters('woocommerce_my_account_my_address_description', esc_html__('The following addresses will be used on the checkout page by default.', 'woocommerce')); ?></p>

<div class="flex flex-wrap row woocommerce-Addresses">
  <?php foreach ($get_addresses as $name => $address_title) : ?>
    <?php $address = wc_get_account_formatted_address($name); ?>

    <div class="column w-full md:w-6/12">
      <header class="flex items-center">
        <h3><?= esc_html($address_title); ?></h3>
        <a href="<?= esc_url(wc_get_endpoint_url('edit-address', $name)); ?>" class="ml-1 edit"><?= $address ? esc_html__('(Edit)', 'woocommerce') : esc_html__('(Add)', 'woocommerce'); ?></a>
      </header>
      <address>
        <?= $address ? wp_kses_post($address) : esc_html_e('You have not set up this type of address yet.', 'woocommerce'); ?>
      </address>
    </div>

  <?php endforeach; ?>
</div>