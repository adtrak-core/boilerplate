<?php

// Register Region taxonomy
function register_region_taxonomy() {

	$labels = array(
		'name'                       => _x( 'Regions', 'Taxonomy General Name', 'text_domain' ),
		'singular_name'              => _x( 'Region', 'Taxonomy Singular Name', 'text_domain' ),
		'menu_name'                  => __( 'Regions', 'text_domain' ),
		'all_items'                  => __( 'All Regions', 'text_domain' ),
		'parent_item'                => __( 'Parent Region', 'text_domain' ),
		'parent_item_colon'          => __( 'Parent Region:', 'text_domain' ),
		'new_item_name'              => __( 'New Region Name', 'text_domain' ),
		'add_new_item'               => __( 'Add New Region', 'text_domain' ),
		'edit_item'                  => __( 'Edit Region', 'text_domain' ),
		'update_item'                => __( 'Update Region', 'text_domain' ),
		'view_item'                  => __( 'View Region', 'text_domain' ),
		'separate_items_with_commas' => __( 'Separate regions with commas', 'text_domain' ),
		'add_or_remove_items'        => __( 'Add or remove regions', 'text_domain' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
		'popular_items'              => __( 'Popular regions', 'text_domain' ),
		'search_items'               => __( 'Search regions', 'text_domain' ),
		'not_found'                  => __( 'Not Found', 'text_domain' ),
		'no_terms'                   => __( 'No regions', 'text_domain' ),
		'items_list'                 => __( 'Regions list', 'text_domain' ),
		'items_list_navigation'      => __( 'Regions list navigation', 'text_domain' ),
	);
	$rewrite = array(
			'slug'                       => 'regions',
			'with_front'                 => false,
			'hierarchical'               => true,
		);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
		'rewrite'                    => $rewrite,
	);
	register_taxonomy( 'regions', array( 'members' ), $args );

}
add_action( 'init', 'register_region_taxonomy', 0 );

// Register Member Services
function register_member_services() {

	$labels = array(
		'name'                       => _x( 'Member Services', 'Taxonomy General Name', 'text_domain' ),
		'singular_name'              => _x( 'Member Service', 'Taxonomy Singular Name', 'text_domain' ),
		'menu_name'                  => __( 'Member Services', 'text_domain' ),
		'all_items'                  => __( 'All Services', 'text_domain' ),
		'parent_item'                => __( 'Parent Service', 'text_domain' ),
		'parent_item_colon'          => __( 'Parent Service:', 'text_domain' ),
		'new_item_name'              => __( 'New Service Name', 'text_domain' ),
		'add_new_item'               => __( 'Add New Service', 'text_domain' ),
		'edit_item'                  => __( 'Edit Service', 'text_domain' ),
		'update_item'                => __( 'Update Service', 'text_domain' ),
		'view_item'                  => __( 'View Service', 'text_domain' ),
		'separate_items_with_commas' => __( 'Separate items with commas', 'text_domain' ),
		'add_or_remove_items'        => __( 'Add or remove items', 'text_domain' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
		'popular_items'              => __( 'Popular Items', 'text_domain' ),
		'search_items'               => __( 'Search Items', 'text_domain' ),
		'not_found'                  => __( 'Not Found', 'text_domain' ),
		'no_terms'                   => __( 'No items', 'text_domain' ),
		'items_list'                 => __( 'Items list', 'text_domain' ),
		'items_list_navigation'      => __( 'Items list navigation', 'text_domain' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'member-services', array( 'members' ), $args );

}
add_action( 'init', 'register_member_services', 0 );

// Register Resource centre categories
function register_resource_categories() {

	$labels = array(
		'name'                       => _x( 'Resource Categories', 'Taxonomy General Name', 'text_domain' ),
		'singular_name'              => _x( 'Resource Category', 'Taxonomy Singular Name', 'text_domain' ),
		'menu_name'                  => __( 'Resource Categories', 'text_domain' ),
		'all_items'                  => __( 'All Categories', 'text_domain' ),
		'parent_item'                => __( 'Parent Categories', 'text_domain' ),
		'parent_item_colon'          => __( 'Parent Category:', 'text_domain' ),
		'new_item_name'              => __( 'New Category Name', 'text_domain' ),
		'add_new_item'               => __( 'Add New Category', 'text_domain' ),
		'edit_item'                  => __( 'Edit Category', 'text_domain' ),
		'update_item'                => __( 'Update Category', 'text_domain' ),
		'view_item'                  => __( 'View Category', 'text_domain' ),
		'separate_items_with_commas' => __( 'Separate categories with commas', 'text_domain' ),
		'add_or_remove_items'        => __( 'Add or remove categories', 'text_domain' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
		'popular_items'              => __( 'Popular categories', 'text_domain' ),
		'search_items'               => __( 'Search categories', 'text_domain' ),
		'not_found'                  => __( 'Not Found', 'text_domain' ),
		'no_terms'                   => __( 'No categories', 'text_domain' ),
		'items_list'                 => __( 'Categories list', 'text_domain' ),
		'items_list_navigation'      => __( 'Categories list navigation', 'text_domain' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'resource-categories', array( 'resources' ), $args );

}
add_action( 'init', 'register_resource_categories', 0 );

?>