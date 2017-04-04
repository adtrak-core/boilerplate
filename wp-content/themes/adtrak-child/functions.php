<?php

/*
 * Enqueue and register scripts the right way.
 */
add_action('wp_enqueue_scripts', function () {
    // wp_enqueue_style('base-theme', get_theme_file_uri('/css/main.css'), [], '', 'all');
    // wp_enqueue_style('base-scripts', get_theme_file_uri('/css/min/custom.min.js'), ['jquery'], '', 'all');
});