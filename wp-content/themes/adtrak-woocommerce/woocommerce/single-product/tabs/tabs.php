<?php

/**
 * Single Product tabs
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/tabs/tabs.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.8.0
 */

if (!defined('ABSPATH')) {
  exit;
}

/**
 * Filter tabs and allow third parties to add their own.
 *
 * Each tab is an array containing title, callback and priority.
 *
 * @see woocommerce_default_product_tabs()
 */
$product_tabs = apply_filters('woocommerce_product_tabs', array());

if (!empty($product_tabs)) : ?>

    <nav class="flex justify-center w-full" role="tablist">
      <?php $i = 0;
      foreach ($product_tabs as $key => $product_tab) : ?>
        <a class="product-tab-link <?= ($i == 0) ? 'active' : ''; ?>" href="#tab-<?= esc_attr($key); ?>">
          <?= wp_kses_post(apply_filters('woocommerce_product_' . $key . '_tab_title', $product_tab['title'], $key)); ?>
        </a>
      <?php $i++;
      endforeach; ?>
    </nav>

  <div class="flex justify-between mb-10">
    

    <?php
    $i = 0;
    foreach ($product_tabs as $key => $product_tab) : ?>
      <div class="w-full product-tab-panel copy <?= ($i != 0) ? 'hidden' : ''; ?>" id="tab-<?= esc_attr($key); ?>" role="tabpanel" aria-labelledby="tab-title-<?= esc_attr($key); ?>">
        <?php
        if (isset($product_tab['callback'])) {
          call_user_func($product_tab['callback'], $key, $product_tab);
        }
        ?>
      </div>
    <?php
      $i++;
    endforeach; ?>

    <?php do_action('woocommerce_product_after_tabs'); ?>
  </div>

<?php endif; ?>