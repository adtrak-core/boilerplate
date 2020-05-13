<?php

$context = Timber::context();
$context['post'] = new Timber\Post();

Timber::render( [ '404.twig' ], $context );