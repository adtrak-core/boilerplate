<?php

/**
 * Wrapper around registering any custom post types here.
 *
 * @version 1.0.0
 */
class CustomPostTypes
{
    /**
     * We have declared some 'sensible' defaults here but these can be
     * overrided per CPT
     */
    public $defaultOptions = [
        'hierarchical' => false,
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_admin_bar' => true,
        'show_in_nav_menus' => true,
        'can_export' => true,
        'has_archive' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'capability_type' => 'post',
        'menu_icon' => null,
    ];

    public function __construct()
    {
        add_action('init', [$this, 'examplePostType']);
    }

    /**
     * Demonstration of registering a new post type using the default options
     * and overwriting various elements.
     */
    public function examplePostType()
    {
        $labels = [
            'name' => __('Example', 'jb'),
            'singular_name' => __('Example', 'jb'),
        ];

        /* These options at least will be needed per each */
        $args = array_merge($this->defaultOptions, [
            'label' => __('Example', 'jb'),
            'labels' => $labels,
            'supports' => ['title', 'editor']
        ]);

        /* Overrite default options as you want */
        $args['public'] = true;

        register_post_type('examples', $args);
    }
}
