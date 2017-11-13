<?php

/*
 * Enqueue and register scripts the right way.
 */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('base-theme', get_theme_file_uri() . '/css/main.css', [], '', 'all');
    wp_enqueue_script('production', get_theme_file_uri() . '/js/production-dist.js', ['jquery'], '', true);
    wp_enqueue_script('fontawesome', 'https://use.fontawesome.com/731f5cd381.js', [], '', true );
});

// Deactivate some plugins from some pages

/* 
function remove_unwanted_plugins() {
    // Turn off map other than coverage area
    if (!is_page('coverage-area')) {
        remove_action('wp_head', 'bf_head');
        remove_action('wp_footer', 'bf_footer', 30);
    }
    // Turn forms other than contact page
    if (!is_page('contact-us')) {
        wp_dequeue_script('parsleyjs');
        wp_dequeue_script('forms-front-js');
    }
}

add_action('wp_head', 'remove_unwanted_plugins', 1);

*/

/* ========================================================================================================================
	
Custom
	
======================================================================================================================== */

add_action('after_setup_theme', function () {

// Custom image sizes

    add_image_size( 'hero-2000', 2000, 650, true );
	add_image_size( 'hero-1200', 1200, 500, true );
	add_image_size( 'hero-600', 600, 600, true );
	add_image_size( 'square-350', 350, 350, true );
	add_image_size( 'gallery-300', 300, 220, true );

	// More navs

	register_nav_menus([
		'secondary' => __('Secondary Menu', 'adtrak')
	]);

});

/* ========================================================================================================================
	
Allow excerpts on pages
	
======================================================================================================================== */

add_action( 'init', 'my_add_excerpts_to_pages' );
function my_add_excerpts_to_pages() {
     add_post_type_support( 'page', 'excerpt' );
}

/* ========================================================================================================================
	
Address - Stacked
	
======================================================================================================================== */

function address_stacked() {
	// loop through the rows of data
	while ( have_rows('site_address', 'options') ) : the_row();
		// display a sub field value
		the_sub_field('address_line', 'options');
		echo "<br/>";

	endwhile;
	the_field('site_postcode', 'option');
}

add_shortcode( 'address_stacked', 'address_stacked' );

/* ========================================================================================================================
	
Address - Inline
	
======================================================================================================================== */

function address_inline() {
	// loop through the rows of data
	while ( have_rows('site_address', 'options') ) : the_row();
		// display a sub field value
		the_sub_field('address_line', 'options');
		echo ",&nbsp;";
	endwhile;
	the_field('site_postcode', 'option');
}

add_shortcode('address_inline', 'address_inline');