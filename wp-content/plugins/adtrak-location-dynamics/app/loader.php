<?php

namespace Adtrak\LocationDynamics;

$admin = new Controllers\AdminController;
$front = new Controllers\FrontController;

/** @var \Billy\Framework\Loader $loader */

$loader->action([
 	'method' => 	'admin_menu',
 	'uses'   => 	[$admin, 'menu']
]);

$loader->action([
 	'method' => 	'wp_footer',
 	'uses'   => 	[$front, 'getInsightCode']
]);

$loader->action([
 	'method' => 	'init',
 	'uses'   => 	[$front, 'getCookie']
]);

$loader->action([
 	'method' => 	'init',
 	'uses'   => 	[$front, 'addShortcodes']
]);