<?php

$context = Timber::context();
$timber_post = new Timber\Post();
$context['post'] = $timber_post;

Timber::render( [ 'page-'.$timber_post->slug.'.twig', 'page.twig' ], $context );
