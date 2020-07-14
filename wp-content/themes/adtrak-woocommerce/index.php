<?php

$context = Timber::context();

$context['post'] = new Timber\Post();

$templates = ['index.twig'];

if (is_home()) {
    array_unshift($templates, 'front-page.twig', 'home.twig');
}

Timber::render($templates, $context);
