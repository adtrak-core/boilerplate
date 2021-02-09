<?php

$context = Timber::context();
$context['posts'] = new Timber\PostQuery();

$context['categories'] = get_categories( array(
    'orderby' => 'name',
    'order'   => 'ASC'
) );

Timber::render( [ 'index.twig' ], $context );
