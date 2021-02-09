<?php
/* ========================================================================================================================

Get Adtrak Logo.

Acceepts:

======================================================================================================================== */
function get_adtrak_logo_new($option = null, $icon = false) {
    if ($icon == true) {
        $end = '-icon.svg';
    } else {
        $end = '-logo.svg';
    }

    switch ($option) {
        case 'white':
            return '<img class="lazyload" data-src="' . get_theme_file_uri('/_resources/images/adtrak-white' . $end) . '" alt="Adtrak Logo">';
            break;
        default:
            return '<img class="lazyload" data-src="' . get_theme_file_uri('/_resources/images/adtrak-navy' . $end) . '" alt="Adtrak Logo">';
            break;
    }
}
?>