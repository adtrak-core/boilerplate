<?php

return [
	
	/**
     * The version constraint.
     */
    'version' => '3.3.4',

    /**
     * The asset path.
     */
    'assets' => '/resources/assets/',

    /**
     * Sentry url
     */
    'sentryUrl' => '',

    /**
     * Views
     */
    'views' => __DIR__ . '/resources/views',

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
    ]
];