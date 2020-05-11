<?php
/**
 * Fetch all files from the functions folder with ext of .php
 * Then loop over and include them
 */
$files = ( defined( 'WP_DEBUG' ) AND WP_DEBUG ) ? glob( __DIR__ . '/_functions/*.php', GLOB_ERR ) : glob( __DIR__ . '/_functions/*.php' );
foreach ( $files as $file ) : include $file; endforeach;
