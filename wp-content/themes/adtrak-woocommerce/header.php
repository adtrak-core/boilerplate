<?php

/**
 * Third party plugins that hijack the theme will call wp_head() to get the header template. We use this to start our output buffer and render into the view/page-plugin.twig template in footer.php
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */

$GLOBALS['timberContext'] = Timber::context();
ob_start();
