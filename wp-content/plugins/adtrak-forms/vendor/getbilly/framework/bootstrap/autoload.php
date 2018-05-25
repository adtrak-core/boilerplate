<?php

/**
 * Ensure this is only ran once.
 */
if (defined('BILLY_AUTOLOAD'))
	return;

define('BILLY_AUTOLOAD', microtime(true));

if ( ! file_exists(__DIR__ . '/globals.php'))
    throw new Exception("Globals file not found.");

@require 'globals.php';

if (array_search(ABSPATH . 'wp-admin/includes/plugin.php', get_included_files()) === false) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

$billy = new Billy\Framework\Application();

/**
 * load all plugins
 */
$iterator = new DirectoryIterator(plugin_directory());

foreach ($iterator as $directory) {
    
	if ( ! $directory->valid() || $directory->isDot() || ! $directory->isDir())
        continue;
    
    $root = $directory->getPath() . '/' . $directory->getFilename();
   
    if ( ! file_exists($root . '/plugin.config.php'))
        continue;
	
	$config = $billy->getPluginConfig($root);
    $plugin = substr($root . '/plugin.php', strlen(plugin_directory()));
    $plugin = ltrim($plugin, '/');

	register_activation_hook($plugin, function() use ($billy, $config, $root)
	{
		$billy->loadPlugin($config);
		$billy->activatePlugin($root);
	});

	register_deactivation_hook($plugin, function() use ($billy, $root)
	{
		$billy->deactivatePlugin($root);
	});

    if ( ! is_plugin_active($plugin))
        continue;
	
 	@require_once $root . '/plugin.php';

	$billy->loadPlugin($config);
}