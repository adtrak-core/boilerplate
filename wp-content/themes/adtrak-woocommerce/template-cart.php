<?php
/*
Template Name: Cart
*/

$context = Timber::context();

$context['post'] = new Timber\Post();

$template = ['page-cart.twig'];

Timber::render($template, $context);
