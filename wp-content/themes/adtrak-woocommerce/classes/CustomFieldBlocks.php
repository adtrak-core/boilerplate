<?php

use Timber\Timber;

/**
 * Class Wrapper around the ACF Blocks initialisation.
 * Timber is used to render the individual partails.
 *
 * @version 1.0.0
 */
class CustomFieldBlocks
{
    public function __construct()
    {
        add_action('acf/init', [$this, 'acfInitBlocks']);
    }

    /**
     * Initialise the callbacks/heroes here. This is to use the
     * blocks within the guttenberg editor (recommended if we're
     * using commmerce as it will allow handy links to products)
     *
     * @return void
     */
    public function acfInitBlocks()
    {
        if (!function_exists('acf_register_block_type')) {
            return;
        }

        acf_register_block_type([
            'name' => 'hero_block',
            'title' => __('Hero', 'jb'),
            'description' => __('An example hero', 'jb'),
            'render_callback' => [$this, 'heroRenderBlock'],
            'post_types' => ['post', 'page'],
            'category' => 'layout'
        ]);
    }

    /**
     * Render function for the Hero Block
     *
     * @param  mixed $block
     * @param  mixed $content
     * @param  mixed $is_preview
     *
     * @return void
     */
    public function heroRenderBlock($block)
    {
        $context = Timber::context();

        $context['block'] = $block;
        $context['fields'] = get_fields();

        Timber::render('components/hero.twig', $context);
    }
}
