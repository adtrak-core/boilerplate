<?php

if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array (
	'key' => 'group_587ccfaf71ead',
	'title' => 'Buckets',
	'fields' => array (
		array (
			'layout' => 'horizontal',
			'choices' => array (
				'nomargins' => 'No Margins',
				'marginsbetween' => 'Margins Between',
			),
			'default_value' => '',
			'other_choice' => 0,
			'save_other_choice' => 0,
			'allow_null' => 0,
			'return_format' => 'value',
			'key' => 'field_587ceac7b46c3',
			'label' => 'Bucket Layout',
			'name' => 'bucket_layout',
			'type' => 'radio',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
		),
		array (
			'post_type' => array (
			),
			'taxonomy' => array (
			),
			'min' => 0,
			'max' => 6,
			'filters' => array (
				0 => 'search',
				1 => 'post_type',
				2 => 'taxonomy',
			),
			'elements' => array (
				0 => 'featured_image',
			),
			'return_format' => 'object',
			'key' => 'field_587cd17ed0a2b',
			'label' => 'Buckets',
			'name' => 'buckets',
			'type' => 'relationship',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
		),
	),
	'location' => array (
		array (
			array (
				'param' => 'page_type',
				'operator' => '==',
				'value' => 'front_page',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'acf_after_title',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => 1,
	'description' => '',
));

endif;

?>