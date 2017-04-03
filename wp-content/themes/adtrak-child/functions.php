<?php

/*
 * Enqueue and register scripts the right way.
 */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('base-theme', get_theme_file_uri() . '/css/main.css', [], '', 'all');
    wp_enqueue_script('production', get_theme_file_uri() . '/js/production-dist.js', [], '', true);
    wp_enqueue_script('fontawesome', 'https://use.fontawesome.com/731f5cd381.js', [], '', true );
});

/* ========================================================================================================================
	
Custom
	
======================================================================================================================== */

add_action('after_setup_theme', function () {

// Custom image sizes

    add_image_size( 'hero-2000', 2000, 650, true );
	add_image_size( 'hero-1200', 1200, 500, true );
	add_image_size( 'hero-600', 600, 600, true );

});

/* ========================================================================================================================
	
TypeKit
	
======================================================================================================================== */

/*
function theme_typekit() {
    wp_enqueue_script( 'theme_typekit', '//use.typekit.net/xxxxxxx.js');
}
add_action( 'wp_enqueue_scripts', 'theme_typekit' );

function theme_typekit_inline() {
  if ( wp_script_is( 'theme_typekit', 'done' ) ) { ?>
  	<script type="text/javascript">try{Typekit.load();}catch(e){}</script>
<?php }
}
add_action( 'wp_head', 'theme_typekit_inline' );
*/

/* ========================================================================================================================
	
Allow excerpts on pages
	
======================================================================================================================== */

add_action( 'init', 'my_add_excerpts_to_pages' );
function my_add_excerpts_to_pages() {
     add_post_type_support( 'page', 'excerpt' );
}

/* ========================================================================================================================
	
Advanced Custom Fields
	
======================================================================================================================== */

if (function_exists('acf_add_local_field_group')) {
    include_once __DIR__ . '/includes/acf/global.php';
    include_once __DIR__ . '/includes/acf/buckets.php';
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