<?php
/**
 * Core functions for your site. This page will usually be changed
 * in development to suit specific needs, it also includes the modules
 * which are theme specific and clean up output.
 * @author  Adtrak
 * @package AdtrakParent
 * @version 1.0.0
 */

/**
 * setup the theme, register navs here, adds html5 support still
 */
add_action('after_setup_theme', function () {
    // Hide the admin bar.
    show_admin_bar(false);

    // Enable plugins to manage the document title
    add_theme_support('title-tag');

    // Enable post thumbnails
    add_theme_support('post-thumbnails');

    // Enable HTML5 markup support
    add_theme_support('html5', ['caption', 'comment-form', 'comment-list', 'gallery', 'search-form']);

    // Register wp_nav_menu() menus
    register_nav_menus([
        'primary', __('Primary Menu', 'adtrak'),
        'footer', __('Footer Menu', 'adtrak')
    ]);

    add_image_size( 'hero-2000', 2000, 650, true );
	add_image_size( 'hero-1200', 1200, 500, true );
	add_image_size( 'hero-600', 600, 600, true );
});

/*
 * Set custom excerpt more.
 */
add_filter('excerpt_more', function () {
    return '...';
});

/*
 * Set custom excerpt length.
 */
add_filter('excerpt_length', function () {
    return 101;
});

add_filter('upload_mimes', function($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
});

/**
 * Filters the page title appropriately depending on the current page
 * This will 90% of the time be overwritten by Yoast, but we have this here just incase.
 */
add_filter('wp_title', function () {
	global $post;

	$name = get_bloginfo('name');
	$description = get_bloginfo('description');

	if (is_front_page() || is_home()) {
		if ($description) {
			return sprintf('%s - %s', $name, $description);
		}
		return $name;
	}

	if (is_category()) {
		return sprintf('%s - %s', trim(single_cat_title('', false)), $name);
	}

	return sprintf('%s - %s', trim($post->post_title), $name);
});

/**
 * Remove the WordPress version from RSS feeds
 */
add_filter('the_generator', '__return_false');

/**
 * Wrap embedded media as suggested by Readability
 *
 * @link https://gist.github.com/965956
 * @link http://www.readability.com/publishers/guidelines#publisher
 */
add_filter('embed_oembed_html', function ($cache) {
	return '<div class="entry-content-asset">' . $cache . '</div>';
});

/**
 * Don't return the default description in the RSS feed if it hasn't been changed
 */
function remove_default_description($bloginfo) {
  $default_tagline = 'Just another WordPress site';
  return ($bloginfo === $default_tagline) ? '' : $bloginfo;
}
add_filter('get_bloginfo_rss', 'remove_default_description');

add_action('init', function() {
    if (function_exists('acf_add_options_page')) {
        $specific_page = acf_add_options_page([
            'page_title' 	=> 'Site Options',
            'menu_title' 	=> 'Site Options',
            'menu_slug' 	=> 'site-options',
            'position' 		=> 75,
            'capability' 	=> 'edit_themes',
            'icon_url' 		=> 'dashicons-hammer',
            'redirect' 		=> false
        ]);

        $marketing_page = acf_add_options_page([
            'page_title' 	=> 'Marketing',
            'menu_title' 	=> 'Marketing',
            'menu_slug' 	=> 'marketing',
            'position' 		=> 75,
            'capability' 	=> 'edit_themes',
            'icon_url' 		=> 'dashicons-randomize',
            'redirect' 		=> false
        ]);
    }

    if (function_exists('acf_add_local_field_group')) {
        include_once __DIR__ . '/includes/acf/options-site.php';
        include_once __DIR__ . '/includes/acf/options-marketing.php';
    }
});