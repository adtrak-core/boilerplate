<?php

/* ========================================================================================================================

Enqueue and register scripts the right way.

======================================================================================================================== */

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script('production', get_theme_file_uri() . '/js/production-dist.js', ['jquery'], '', true);
    wp_enqueue_script('fontawesome', 'https://use.fontawesome.com/731f5cd381.js', [], '', true );
});



/* ========================================================================================================================
	
Add defer attribute to scripts - ADD TO THIS AS REQUIRED
	
======================================================================================================================== */

function add_defer_attribute($tag, $handle) {
	// add script handles to the array below
	$scripts_to_defer = array('jquery-core','production','forms-front-js','parsleyjs','adtrak-cookie');
	 
	foreach($scripts_to_defer as $defer_script) {
	   if ($defer_script === $handle) {
		  return str_replace(' src', ' defer src', $tag);
	   }
	}
	return $tag;
 }
 add_filter('script_loader_tag', 'add_defer_attribute', 10, 2);



/* ========================================================================================================================

Deactivate some plugins from some pages

======================================================================================================================== */

function remove_unwanted_plugins() {
	/*
	// Turn off map other than coverage area
    if (!is_page('coverage-area')) {
        remove_action('wp_head', 'bf_head');
        remove_action('wp_footer', 'bf_footer', 30);
	}
	*/

    // Only include form scripts on pages that have forms - REMOVE if your forms are on all pages!
    if (!is_page('contact-us')) {
        wp_dequeue_script('parsleyjs');
        wp_dequeue_script('forms-front-js');
	}

	// Remove datepicker script - ENABLE if your form has a datepicker field!
	wp_dequeue_script('jquery-ui-datepicker');
}

add_action('wp_head', 'remove_unwanted_plugins', 1);




/* ========================================================================================================================
	
Deregister Certain Stylesheets
	
======================================================================================================================== */

function my_deregister_styles() {
	wp_deregister_style('forms-front'); // Disable forms stylesheet included by plugin - just use your forms.scss
	wp_deregister_style('adtrak-cookie'); // Disable separate stylesheet for cookie notice (styles can be found in footer.scss)
	wp_deregister_style('wp-block-library'); // Gutenberg related stylesheet
}
add_action('wp_print_styles', 'my_deregister_styles', 100);



/* ========================================================================================================================

Disable Gutenberg

======================================================================================================================== */

add_filter('use_block_editor_for_post', '__return_false');



/* ========================================================================================================================

Custom

======================================================================================================================== */

add_action('after_setup_theme', function () {

// Custom image sizes

    add_image_size( 'img-2000-650', 2000, 650, true );
	add_image_size( 'img-1200-500', 1200, 500, true );
	add_image_size( 'img-600-600', 600, 600, true );
	add_image_size( 'img-350-350', 350, 350, true );

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




