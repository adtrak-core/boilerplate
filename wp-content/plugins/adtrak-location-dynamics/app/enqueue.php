<?php 

namespace Adtrak\LocationDynamics;

/** @var \Billy\Framework\Enqueue $enqueue */

$enqueue->admin([
	'as' => 'location-dynamics-admin',
	'src' => Helper::assetUrl('css/location-dynamics-admin.css')
]);

$enqueue->admin([
	'as' => 'location-dynamics-admin',
	'src' => Helper::assetUrl('js/location-dynamics-admin.js')
]);

$enqueue->front([
	'as' => 'location-dynamics-front',
	'src' => Helper::assetUrl('js/location-dynamics-front.js')
]);