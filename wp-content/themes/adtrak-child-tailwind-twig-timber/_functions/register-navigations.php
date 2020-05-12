<?php 
add_action( 'init', 'register_navigations' );
function register_navigations() {
     register_nav_menus([
      'secondary' => __('Secondary Menu', 'adtrak')
    ]);
}
?>