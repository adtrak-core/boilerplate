<?php

class Icons {

    public function __construct() {
        add_action('init', [$this, 'icon']);
    }
    
    public static function getIcon($iconName, $classes = null) {
        return sprintf('<svg class="icon icon-'.$iconName.' '.$classes.'"><use href="'.get_stylesheet_directory_uri().'/_resources/images/icons-sprite.svg#icon-'.$iconName.'"></use></svg>');
    }
}