<?php

  function add_defer_attribute($tag, $handle) {
    // add script handles to the array below
    $scripts_to_defer = array('production','adtrak-cookie','location-dynamics-front', 'svgxuse');

    foreach($scripts_to_defer as $defer_script) {
      if ($defer_script === $handle) {
        return str_replace(' src', ' defer src', $tag);
      }
    }
    return $tag;
  }
  add_filter('script_loader_tag', 'add_defer_attribute', 10, 2);

  function remove_unwanted_plugins() {
    /*
    // Turn off map other than coverage area
      if (!is_page('coverage-area')) {
          remove_action('wp_head', 'bf_head');
          remove_action('wp_footer', 'bf_footer', 30);
    }
    */

    // Remove datepicker script - ENABLE if your form has a datepicker field!
    wp_dequeue_script('jquery-ui-datepicker');
  }

  add_action('wp_head', 'remove_unwanted_plugins', 1);

  function my_deregister_styles() {
    wp_deregister_style('adtrak-cookie'); // Disable separate stylesheet for cookie notice (styles can be found in footer.scss)
    wp_deregister_style('wp-block-library'); // Gutenberg related stylesheet
    wp_deregister_style( 'dashicons' );
  }
  add_action('wp_print_styles', 'my_deregister_styles', 100);

  add_filter( 'wp_default_scripts', 'dequeue_jquery_migrate' );

  function dequeue_jquery_migrate( &$scripts){
    if(!is_admin()){
      $scripts->remove( 'jquery');
      $scripts->add( 'jquery', false, array( 'jquery-core' ), '1.10.2' );
    }
  }


//Disable gutenberg
add_filter('use_block_editor_for_post', '__return_false');


// Custom image sizes
add_action('after_setup_theme', function () {	
  // add_image_size( 'logos', 140, 70, true );

	// More navs
	register_nav_menus([
		'secondary' => __('Secondary Menu', 'adtrak')
	]);
});

// Add excerpts to pages
add_action( 'init', 'my_add_excerpts_to_pages' );
function my_add_excerpts_to_pages() {
     add_post_type_support( 'page', 'excerpt' );
}

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
add_shortcode('address_stacked', 'address_stacked');




