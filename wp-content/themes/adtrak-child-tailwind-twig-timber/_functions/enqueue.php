<?php 
    add_action('wp_enqueue_scripts', function () {
        wp_enqueue_script('production', get_theme_file_uri() . '/js/production-dist.js', ['jquery'], '', true);
        wp_enqueue_script('svgxuse', get_theme_file_uri() . '/js/svgxuse.js', [], '', true );
        wp_localize_script('production', 'themeURL', array(
          'themeURL' => get_stylesheet_directory_uri()
          )
		);
    });
