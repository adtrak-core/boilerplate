<?php
/**
 * Adtrak reset resets some key elements that are standard across all Adtrak websites
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

});

// Allow SVGs to be uploaded in media
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
	
	if($post === NULL){
		return $name;
	}

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

/**
 * Add no index to staging sites
 */
add_action('wp_head', function() {
    if (strpos($_SERVER['SERVER_NAME'],'adtrak.agency') !== false) {
        echo '<meta name="robots" content="noindex">';
        echo '<meta name="googlebot" content="noindex">';
    }
	
    if (strpos($_SERVER['SERVER_NAME'],'breeez.com') !== false) {
        echo '<meta name="robots" content="noindex">';
        echo '<meta name="googlebot" content="noindex">';
    }
});

/**
 * Prevent theme edit
 */
define( 'DISALLOW_FILE_EDIT', true );
