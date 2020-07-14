<?php
/*
Template Name: Checkout
*/

$context = Timber::context();

$context['post'] = new Timber\Post();

$template = ['page-checkout.twig'];

Timber::render($template, $context);
