<?php

$context = Timber::context();
$context['posts'] = new Timber\PostQuery();

$context['archives'] = wp_get_archives( array('type=>monthly', 'echo'=>0 ));
$context['categories'] = wp_list_categories(array('title_li' => '', 'echo'=>0));

Timber::render( [ 'index.twig' ], $context );
