<?php

$context = Timber::context();
$context['search_query'] = get_search_query();
$context['posts'] = new Timber\PostQuery();

Timber::render( [ 'search.twig' ], $context );