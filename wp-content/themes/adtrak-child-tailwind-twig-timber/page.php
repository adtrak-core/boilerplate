
<?php

$context = Timber::context();
$context['post'] = new Timber\Post();

Timber::render( [ 'page.twig' ], $context );
