<?php

namespace Adtrak\Forms;

$admin = new Controllers\AdminController;
$front = new Controllers\FrontController;

/** @var \Billy\Framework\Loader $loader */

$loader->action([
 	'method' => 	'admin_menu',
 	'uses'   => 	[$admin, 'menu']
]);

$loader->action([
 	'method' => 	'admin_enqueue_scripts',
 	'uses'   => 	[$admin, 'addScripts']
]);

$loader->action([
 	'method' => 	'wp_enqueue_scripts',
 	'uses'   => 	[$front, 'addScripts']
]);

$loader->action([
 	'method' => 	'init',
 	'uses'   => 	[$front, 'addShortcode']
]);

$loader->action([
 	'method' => 	'wp_head',
 	'uses'   => 	[$front, 'setBaseUrl']
]);

$loader->action([
     'method'     => 'wp_ajax_form_send',
     'uses'     => [$front, 'sendMail']
]);

$loader->action([
     'method'     => 'wp_ajax_nopriv_form_send',
     'uses'     => [$front, 'sendMail']
]);