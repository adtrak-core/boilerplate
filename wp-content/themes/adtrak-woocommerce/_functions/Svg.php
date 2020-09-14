<?php

class Svg
{
    /**
     * Path where SVGs are loaded.
     *
     * @var string
     */
    public static $path = '/_resources/images/';

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        add_filter('timber/twig', [$this, 'addInlineSvgToTimber']);
    }

    /**
     * inlineSvg
     *
     * This requires file_get_contents. If the server has this disabled
     * this will break.
     *
     * @param  mixed $filename
     * @return string
     */
    public static function inlineSvg($filename): string
    {
        if (!$filename || $filename == null) {
            return '';
        }

        $path = self::$path . $filename . '.svg';

        if (!file_exists(get_stylesheet_directory() . $path)) {
            return '';
        }

        return file_get_contents(get_theme_file_path($path));
    }

    /**
     * Add the inline_svg function to Timber
     *
     * @param  mixed $twig
     * @return void
     */
    public function addInlineSvgToTimber($twig)
    {
        $twig->addFunction(new \Timber\Twig_Function('inline_svg', [$this, 'inlineSvg']));

        return $twig;
    }
}
