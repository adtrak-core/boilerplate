<?php

/**
 * Class wrapper around scripts/handling the enqueue/dequeue.
 *
 * @version 1.1.0
 */
class Scripts
{
    /**
     * Scripts you want to set up to async load
     *
     * @var array
     */
    public $asyncScripts = [];

    /**
     * Scripts you want to set up to defer load
     *
     * @var array
     */
    public $deferScripts = [];

    public function __construct()
    {
        add_filter('script_loader_tag', [$this, 'defer_scripts'], 10, 2);
        add_filter('script_loader_tag', [$this, 'async_scripts'], 10, 2);
        add_filter('woocommerce_enqueue_styles', '__return_empty_array');
        // add_action('wp_enqueue_scripts', [$this, 'enable_scripts']);
        // add_action('wp_enqueue_scripts', [$this, 'disable_scripts']);
    }

    /**
     * Loop through the array of Async scripts and replace the src
     * with `async src` to load them properly.
     *
     * @param  mixed $tag
     * @param  mixed $handle
     * @return void
     */
    public function async_scripts($tag, $handle)
    {
        foreach ($this->asyncScripts as $script) {
            if ($script === $handle) {
                return str_replace(' src', ' async src', $tag);
            }
        }

        return $tag;
    }

    /**
     * Loop through the array of Defer scripts and replace the src
     * with `defer src` to load them properly.
     *
     * @param  mixed $tag
     * @param  mixed $handle
     * @return void
     */
    public function defer_scripts($tag, $handle)
    {
        foreach ($this->deferScripts as $script) {
            if ($script === $handle) {
                return str_replace(' src', ' defer src', $tag);
            }
        }

        return $tag;
    }

    /**
     * Dequeue/Deregister any scripts you wish here.
     *
     * Don't disable jQuery unless you're prepared for a headache. Unfortunately a lot of
     * WooCommerce core functionality and addons require jQuery to function properly.
     * There is little way around this while jQuery is still in core. We don't like it,
     * but it's a problem we have to deal with.
     *
     * @return void
     */
    public function disable_scripts()
    {
    }

    /**
     * Queue any scripts here you wish to extend with.
     *
     * @return void
     */
    public function enable_scripts()
    {
    }

    /**
     * Remove any styles you wish here.
     *
     * @return void
     */
    public function disable_styles()
    {
    }

    /**
     * Enable any styles you wish here
     *
     * @return void
     */
    public function enable_styles()
    {
    }
}
