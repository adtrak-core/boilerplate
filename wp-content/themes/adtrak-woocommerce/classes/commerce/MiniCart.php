<?php

use Timber\Timber;

/**
 * Class to control the Mini Cart (Displayed in the header)
 * This usually just controlls the display of the product qty/amount.
 *
 * @version 1.0.0
 */
class CommerceMiniCart
{
    public function __construct()
    {
        add_filter('woocommerce_add_to_cart_fragments', [$this, 'cart_link_fragment']);
    }


    /**
     * Cart fragments to reload on ajax add to cart (triggered by WooComm)
     *
     * @param  mixed $fragments
     * @return array
     */
    public function cart_link_fragment($fragments): array
    {
        global $woocommerce;

        $context = ['cart' => $woocommerce->cart];

        $fragments['a.cart-mini-contents'] = Timber::compile(
            'partials/shop/cart-fragment-link.twig',
            $context
        );

        $fragments['div.cart-slideout-details'] = Timber::compile(
            'partials/shop/ajax-cart-content.twig',
            $context
        );

        return $fragments;
    }
}
