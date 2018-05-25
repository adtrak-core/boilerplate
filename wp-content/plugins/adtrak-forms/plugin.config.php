<?php

return [
	
	/**
     * The version constraint.
     */
    'version' => '1.5.12',

    /**
     * The asset path.
     */
    'assets' => '/resources/assets/',
    
    /**
     * Views
     */
    'views' => __DIR__ . '/resources/views',    

    /**
     * Templates
     */
    'templates' => __DIR__ . '/resources/templates/',

    /**
     * Activate
     */
    'activators' => [
        __DIR__ . '/app/activate.php'
    ],

    /**
     * Deactivate
     */
    'deactivators' => [
        __DIR__ . '/app/deactivate.php'
    ],

    /**
     * Loader
     */
    'loader' => [
        __DIR__ . '/app/loader.php'
    ],

    /**
     * The styles and scripts to auto-load.
     */
    'enqueue' => [
        __DIR__ . '/app/enqueue.php'
    ],

    /**
     * Sentry URL.
     */
    'sentryUrl' => 'https://8fd3e0c92699438f880d7a3a66313d58:ced94ed4c1b44edd94f30ac63fbcfd8c@sentry.io/133100'
];