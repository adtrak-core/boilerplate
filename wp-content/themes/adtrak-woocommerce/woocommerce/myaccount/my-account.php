<?php

/**
 * My Account page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.0
 */

defined('ABSPATH') || exit; ?>

<section class="flex flex-wrap row">
  <div class="w-full md:w-3/12 xl:w-2/12 column">
    <?php do_action('woocommerce_account_navigation'); ?>
  </div>

  <div class="woocommerce-MyAccount-content column w-full md:w-9/12 xl:w-10/12">
    <?php do_action('woocommerce_account_content'); ?>
  </div>
</section>