<?php

/**
 * Cleanup Function for removing/standardising unnecessary things in WP
 *
 * @version 1.0.0
 */
class Cleanup
{
    public function __construct()
    {
        add_action('init', [$this, 'disableEmjoisTinymce']);
        add_filter('the_generator', [$this, 'removeVersionNumber']);
        add_action('admin_init', [$this, 'removeImageLinkDefault'], 10);
    }

    public function disableEmjoisTinymce($plugins)
    {
        if (is_array($plugins)) {
            return array_diff($plugins, ['wpemoji']);
        } else {
            return [];
        }
    }

    public function removeVersionNumber()
    {
        return '';
    }

    /**
     * Remove default link to image
     */
    public function removeImageLinkDefault()
    {
        $image_set = get_option('image_default_link_type');

        if ($image_set !== 'none') {
            update_option('image_default_link_type', 'none');
        }
    }
}
