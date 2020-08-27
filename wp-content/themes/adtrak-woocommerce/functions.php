<?php

$composer_autoload = __DIR__ . '/vendor/autoload.php';

if (file_exists($composer_autoload)) {
    require_once $composer_autoload;
    $timber = new Timber\Timber();
}

if (!class_exists('Timber')) {
    add_action(
        'admin_notices',
        function () {
            echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url(admin_url('plugins.php#timber')) . '">' . esc_url(admin_url('plugins.php')) . '</a></p></div>';
        }
    );

    return;
}

/**
 * Initialise Timber Directories
 */
Timber::$dirname = ['views'];
Timber::$autoescape = false;


/**
 * Initialise Theme Classes
 */
require_once(__DIR__ . '/classes/BaseSite.php');
require_once(__DIR__ . '/classes/Cleanup.php');
require_once(__DIR__ . '/classes/Scripts.php');
require_once(__DIR__ . '/classes/CustomPostTypes.php');
require_once(__DIR__ . '/classes/Image.php');
require_once(__DIR__ . '/classes/Svg.php');
require_once(__DIR__ . '/classes/CustomFieldBlocks.php');
require_once(__DIR__ . '/classes/Adtrak.php');

new BaseSite();
new Scripts();
new Cleanup();
new CustomPostTypes();
new Image();
new Svg();
new CustomFieldBlocks();
new Adtrak();

/**
 * You won't need this if you only are only templating with twig
 */
function responsive_image($image_id, $classes = null)
{
    return Image::responsiveImage($image_id, $classes);
}

/**
 * You won't need this if you only are only templating with twig
 */
function responsive_image_from_thumb($post_id, $classes = null)
{
    return Image::responsveImageFromThumb($post_id, $classes);
}

/**
 * You won't need this if you only are only templating with twig
 */
function inline_svg($filename)
{
    return Svg::inlineSvg($filename);
}

/**
 * You won't need this if you only are only templating with twig
 */
function get_adtrak_logo($colour = null, $icon = false)
{
    return Adtrak::getLogo($colour, $icon);
}

/**
 * Initialise Commerce Based Class Features
 */
require_once(__DIR__ . '/classes/commerce/MiniCart.php');
require_once(__DIR__ . '/classes/commerce/CommerceOverrides.php');

new CommerceMiniCart();
new CommerceOverrides();

/**
 * WooCommerce + Timber Configuration
 */
function timber_set_product($post)
{
    global $product;

    if (is_woocommerce()) {
        $product = wc_get_product($post->ID);
    }
}

/**
 * Handy function to dump the value and wrap in pre tags. Used for debugging only.
 * Doesn't display anything is debug is not enabled.
 * Commented out due to conflict with Location Dynamics Plugin
 */
// function dump($value)
// {
//     if (defined('WP_DEBUG') && WP_DEBUG === TRUE) {
//         echo '<pre>';
//         print_r($value);
//         echo '</pre>';
//     }
// }
