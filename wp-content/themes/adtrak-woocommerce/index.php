<?php

$context = Timber::context();

$context['posts'] = new Timber\PostQuery();

$context['categories'] = get_categories([
    'orderby' => 'name',
    'order'   => 'ASC'
]);

$context['archives'] = wp_get_archives([
    'type'            => 'monthly',
    'echo'            => false,
]);

$templates = ['index.twig'];

Timber::render($templates, $context);
