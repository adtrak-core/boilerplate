<?php

$context = Timber::context();

$context['post'] = new Timber\Post();

$templates = ['index.twig'];

Timber::render($templates, $context);
