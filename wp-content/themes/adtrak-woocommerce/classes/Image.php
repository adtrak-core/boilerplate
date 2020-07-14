<?php

/**
 * Image Class for setting up proper sizing. Image sizes defined
 * should correspond to tailwind presets.
 *
 * @version 1.2.0
 */
class Image
{
    public function __construct()
    {
        add_action('after_setup_theme', [$this, 'addSizes']);
        add_filter('image_size_names_choose', [$this, 'addSizeNames']);
        add_filter('timber/twig', [$this, 'addResponsiveImageToTimber']);
    }

    /**
     * Sets up some image sizes for WP to generate
     *
     * @return void
     */
    public function addSizes()
    {
        add_image_size('sm', 360, 9999);
        add_image_size('md', 560, 9999);
        add_image_size('lg', 768, 9999);
        add_image_size('xl', 960, 9999);
        add_image_size('xxl', 1200, 9999);
        add_image_size('sup', 1500, 9999);
    }

    /**
     * Gives the sizes a name in the admin so the client does
     * not see the defaults
     *
     * @param  mixed $sizes
     * @return array
     */
    public function addSizeNames($sizes): array
    {
        return array_merge($sizes, [
            'sm' => __('Small'),
            'md' => __('Medium'),
            'lg' => __('Large'),
            'xl' => __('Extra Large'),
            'xxl' => __('2x Extra Large'),
            'sup' => __('Super Large'),
        ]);
    }

    /**
     * Generate a responsive image so we don't overload mobiles
     * Browsers will handle this nicely.
     * Added option for lazyload.
     *
     * @param  int $image_id
     * @param  string $classes
     * @param  string $lazyload
     * @return string
     */
    public static function responsiveImage(
        $image_id,
        $classes = null,
        $lazyload = 'auto'
    ): string {
        if ($image_id === null || $image_id === 0) {
            return '';
        }

        $src = wp_get_attachment_image_src($image_id, 'xxl')[0];
        $srcset = wp_get_attachment_image_srcset($image_id, 'sup');
        $sizes = wp_get_attachment_image_sizes($image_id, 'sup');
        $alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);

        if (!$src || !$srcset) {
            return '';
        }

        return sprintf(
            '<img src="%s" srcset="%s" sizes="%s" alt="%s" class="%s" lazyload="%s">',
            esc_attr($src),
            esc_attr($srcset),
            $sizes,
            $alt,
            $classes,
            $lazyload
        );
    }

    /**
     * Generate a responsive image from a post id/thumbnail.
     *
     * @param  int $post_id
     * @param  string $classes
     * @param  string $lazyload
     * @return string
     */
    public static function responsveImageFromThumb(
        $post_id,
        $classes = null,
        $lazyload = 'auto'
    ): string {
        if ($post_id === null || $post_id === 0) {
            return '';
        }

        $id = get_post_thumbnail_id($post_id);

        if (!$id) {
            return '';
        }

        return self::responsiveImage($id, $classes, $lazyload);
    }

    /**
     * addResponsiveImageToTimber
     *
     * @param  mixed $twig
     * @return void
     */
    public function addResponsiveImageToTimber($twig)
    {
        $twig->addFunction(new \Timber\Twig_Function('responsive_image', [$this, 'responsiveImage']));

        $twig->addFunction(new \Timber\Twig_Function('responsive_image_from_thumb', [$this, 'responsveImageFromThumb']));

        return $twig;
    }
}
