<?php

/**
 * Adtrak Class Functions for Specific Internals
 *
 * @version 1.0.0
 */
class Adtrak
{
    public function __construct()
    {
        add_filter('timber/twig', [$this, 'addFunctionsToTwig']);
    }

    /**
     * Get Adtrak Logo
     *
     * @param  mixed $colour
     * @param  mixed $icon
     * @return void
     */
    public static function getLogo($colour = null, $icon = false)
    {
        $end = ($icon === true) ? '-icon.svg' : '-logo.svg';

        switch ($colour) {
            case 'white':
                $logo = 'adtrak-white';
                break;
            case 'black':
                $logo = 'adtrak-black';
                break;
            default:
                $logo = 'adtrak';
                break;
        }

        $logo = get_theme_file_uri('assets/svg/' . $logo . $end);

        return sprintf('<img src="%s" alt="Adtrak Logo">', $logo);
    }

    /**
     * Add the Adtrak Class functions to Timber
     *
     * @param  mixed $twig
     * @return void
     */
    public function addFunctionsToTwig($twig)
    {
        $twig->addFunction(new \Timber\Twig_Function('get_adtrak_logo', [$this, 'getLogo']));

        return $twig;
    }
}
