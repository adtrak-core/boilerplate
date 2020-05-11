<?php

//grab members
$members = [
  'post_type' =>  'members',
  'posts_per_page' => '6',
  'orderby' => 'rand',
];

//grab testimonials
$testimonials = [
  'post_type' =>  'testimonials',
  'posts_per_page' => '4',
  'orderby' => 'rand',
];


$context = Timber::context();
$context['post'] = new Timber\Post();
$context['members'] = new Timber\PostQuery($members);
$context['testimonials'] = new Timber\PostQuery($testimonials);

Timber::render( [ 'front-page.twig' ], $context );
