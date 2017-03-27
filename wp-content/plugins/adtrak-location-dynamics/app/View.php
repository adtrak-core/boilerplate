<?php 

namespace Adtrak\LocationDynamics;

use Twig_Loader_Filesystem;
use Twig_Environment;
use Twig_Extension_Debug;
use Twig_SimpleFunction;

class View
{
	public static $template;
	public static $loader;
	public static $twig; 

	public static function constructTwig()
	{
		self::$loader = new Twig_Loader_Filesystem();
		self::$loader->addPath(Helper::get('views'));
		
		self::$twig = new Twig_Environment(self::$loader, self::environment());

		if (defined('WP_DEBUG') && true === WP_DEBUG) {
			self::$twig->addExtension(new Twig_Extension_Debug());
		}

		foreach(self::variables() as $key => $value) {
			self::$twig->addGlobal($key, $value);	
		}

		foreach (self::functions() as $function) {
			self::$twig->addFunction(new Twig_SimpleFunction($function, $function));
		}
	}

	protected static function environment()
	{
		$envSettings = [
			'charset' 			=> 'utf-8',
			'auto_reload' 		=> true,
            'strict_variables' 	=> false,
            'autoescape' 		=> true,
			'cache' 			=> content_directory() . '/twig_cache',
			'debug'				=> true
		];

		if (defined('WP_DEBUG') && true === WP_DEBUG) {
			$envSettings['debug'] = true;
		}

		return $envSettings;
	}

	protected static function variables()
	{
		# Here we set some default global variables
		return [
			'site' => [
				'lang_attributes' 		=> get_bloginfo('language'),
				'charset' 				=> get_bloginfo('charset'),
				'url' 					=> get_bloginfo('url'),
				'stylesheet_directory' 	=> get_stylesheet_directory_uri(),
				'title' 				=> get_bloginfo('name'),
				'description' 			=> get_bloginfo('description')
			],
			'plugin' => [
                'directory'             => plugin_dir_url(__DIR__)
			]
		];
	}

	protected static function functions()
	{
		return [
			'dd',
			'wp_head',
			'wp_footer',
			'wp_title',
			'body_class',
			'wp_nav_menu'
		];
	}

	/**
	 * A wrapper function for rendering templates
	 *
	 * @param   string   $template      The name of the template that is to be rendered
	 * @param   array    $vals          An array of variables that are to be rendered with the template
	 * @param   boolean  $echo          Boolean of whether to echo or return the twig render
	 */
	public static function render($template, $vals, $echo = true)
	{
		# Construct the twig 
		self::constructTwig();

		# Check whether we are echoing or returning
		if ($echo === true) {
			echo self::$twig->render($template, $vals);
		} else {
			return self::$twig->render($template, $vals);
		}
	}
}