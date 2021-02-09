<?php

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

?>