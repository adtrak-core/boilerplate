<?php 

namespace Adtrak\Forms;

/** @var \Billy\Framework\Enqueue $enqueue */

$enqueue->admin([
	'as' => 'font-awesome',
	'src' => 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'
]);

$enqueue->admin([
	'as' => 'forms-clipboard-js',
	'src' => Helper::assetUrl('js/clipboard.min.js')
]);

$enqueue->admin([
	'as' => 'forms-admin-js',
	'src' => Helper::assetUrl('js/forms-admin.js')
],'footer');

$enqueue->admin([
	'as' => 'forms-admin',
	'src' => Helper::assetUrl('css/forms-admin.css')
]);



$enqueue->front([
	'as' => 'forms-front',
	'src' => Helper::assetUrl('css/forms-front.css')
]);

$enqueue->front([
	'as' => 'parsleyjs',
	'src' => 'https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.7.2/parsley.min.js'
], 'footer');

$enqueue->front([
	'as' => 'forms-front-js',
	'src' => Helper::assetUrl('js/forms-front.js')
], 'footer');