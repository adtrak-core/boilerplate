<?php

/**
 * Initialise the site and any requirements that are needed.
 * You can redact things here and make it inherit from a parent boilerplate
 * if you wish to streamline things/remove from repetition.
 *
 * @version 1.0.0
 */
class BaseSite extends Timber\Site
{
    public function __construct()
    {
        /* Set up initial theme, supports, commerce */
        add_action('after_setup_theme', [$this, 'theme_supports']);

        /* Register Menus */
        add_action('after_setup_theme', [$this, 'menus']);

        /* Register Sidebars */
        add_action('widgets_init', [$this, 'sidebars']);

        /* Add Context to Timber when loading */
        add_filter('timber/context', [$this, 'add_to_context']);

        /* Init parent construct from timber */
        parent::__construct();
    }

    /**
     * Add support for themes, menus, etc.
     *
     * @return void
     */
    public function theme_supports()
    {
        add_theme_support('woocommerce');
        add_theme_support('wc-product-gallery-zoom');
        add_theme_support('wc-product-gallery-lightbox');
        add_theme_support('wc-product-gallery-slider');
        add_theme_support('menus');
        add_theme_support('automatic-feed-links');
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_theme_support('align-wide');
        add_theme_support('responsive-embeds');
        add_theme_support('html5', ['comment-form', 'comment-list', 'gallery', 'caption']);
    }

    /**
     * Add context to timber here - this is useful for anything shared between themes.
     *
     * @param  mixed $context
     * @return void
     */
    public function add_to_context($context)
    {
        $context['site'] = $this;
        $context['options'] = get_fields('option');
        $context['primaryMenu'] = new Timber\Menu('Primary Menu');
        $context['secondaryMenu'] = new Timber\Menu('Secondary Menu');
        $context['footerMenu'] = new Timber\Menu('Footer Menu');
        return $context;
    }

    /**
     * Register the Menus
     *
     * @return void
     */
    public function menus()
    {
        register_nav_menus([
            'primary' => __('Primary Menu', 'adtrak'),
            'secondary' => __('Secondary Menu', 'adtrak'),
            'footer' => __('Footer Menu', 'adtrak')
        ]);
    }

    /**
     * Register the sidebars
     *
     * @return void
     */
    public function sidebars()
    {
        register_sidebar([
            'id' => 'sidebar',
            'name' => __('Sidebar', 'adtrak'),
            'description' => __('A short description of the sidebar.', 'adtrak'),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>'
        ]);
    }
}
