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
Timber::$dirname = ['_views'];
Timber::$autoescape = false;


/**
 * Initialise Theme Classes
 */
require_once(__DIR__ . '/_functions/BaseSite.php');
require_once(__DIR__ . '/_functions/Cleanup.php');
require_once(__DIR__ . '/_functions/Scripts.php');
require_once(__DIR__ . '/_functions/CustomPostTypes.php');
require_once(__DIR__ . '/_functions/Image.php');
require_once(__DIR__ . '/_functions/CustomFieldBlocks.php');
require_once(__DIR__ . '/_functions/Adtrak.php');
require_once(__DIR__ . '/_functions/Icons.php');
require_once(__DIR__ . '/_functions/AddressOutput.php');
require_once(__DIR__ . '/_functions/OptionsPages.php');

new BaseSite();
new Scripts();
new Cleanup();
new CustomPostTypes();
new Image();
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
function get_adtrak_logo($colour = null, $icon = false)
{
    return Adtrak::getLogo($colour, $icon);
}

/**
 * Adding icon function.
 */
function icon($iconName, $classes = null) {
    return Icons::getIcon($iconName, $classes);
}



/**
 * Initialise Commerce Based Class Features
 */
require_once(__DIR__ . '/_functions/commerce/MiniCart.php');
require_once(__DIR__ . '/_functions/commerce/CommerceOverrides.php');

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

/* Hide the admin bar for logged in users */
show_admin_bar(false);

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


add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script('production', get_theme_file_uri() . '/dist/production-dist.js', ['jquery'], '', true);
    wp_localize_script('production', 'themeURL', array(
        'themeURL' => get_stylesheet_directory_uri()
        )
    );
});
