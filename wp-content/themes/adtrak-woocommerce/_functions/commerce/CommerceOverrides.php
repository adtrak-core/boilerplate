<?php

/**
 * Healthy overrides for WooCommerce
 */
class CommerceOverrides
{
    public function __construct()
    {
        add_filter('woocommerce_output_related_products_args', [$this, 'relatedProductsShown']);

        $this->reorderProductSummary();
    }

    /**
     * Remove action removes the call of the old items/price/etc.
     * Add action will add the values in in a resonable place.
     *
     * @return void
     */
    public function reorderProductSummary()
    {
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);

        add_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 21);
        add_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 29);
    }

    /**
     * Change the quantity of related products per row
     * Change the columns of related products to show.
     *
     * @param  mixed $args
     * @return array
     */
    public function relatedProductsShown($args)
    {
        $args['posts_per_page'] = 4;
        $args['columns'] = 1;
        return $args;
    }
}
