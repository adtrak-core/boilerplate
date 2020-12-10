<?php
/**
 * Cleanup utilities.
 *
 * @since      1.0.0
 * @package    AdtrakCore
 * @subpackage AdtrakCore/Classes
 * @author     Jack Whiting <jack.whiting@adtrak.co.uk>
 */

namespace AdtrakCore\Classes;

class Cleanup
{	
	public function __construct($version)
	{
		$this->version = $version;
	}

	/**
	 * clean up the output in the headers.
	 * @since    1.0.0
	 */
	public function headers()
	{
		remove_action('wp_head', 'feed_links_extra', 3);
		remove_action('wp_head', 'rsd_link');
		remove_action('wp_head', 'wlwmanifest_link');
		remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
		remove_action('wp_head', 'wp_generator');
		remove_action('wp_head', 'wp_shortlink_wp_head', 10);
		remove_action('wp_head', 'print_emoji_detection_script', 7);
		remove_action('admin_print_scripts', 'print_emoji_detection_script');
		remove_action('wp_print_styles', 'print_emoji_styles');
		remove_action('admin_print_styles', 'print_emoji_styles');
		remove_action('wp_head', 'wp_oembed_add_discovery_links');
		remove_action('wp_head', 'wp_oembed_add_host_js');
		remove_action('wp_head', 'rest_output_link_wp_head', 10);
		remove_filter('the_content_feed', 'wp_staticize_emoji');
		remove_filter('comment_text_rss', 'wp_staticize_emoji');
		remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

		add_action('wp_head', 'ob_start', 1, 0);
		add_action('wp_head', function () {
			$pattern = '/.*' . preg_quote(esc_url(get_feed_link('comments_' . get_default_feed())), '/') . '.*[\r\n]+/';
			echo preg_replace($pattern, '', ob_get_clean());
		}, 3, 0);

		add_filter('use_default_gallery_style', '__return_false');

		add_filter('nav_menu_item_id', '__return_null');
	}

	/**
	 * move js to the footer.
	 * @since    1.0.0
	 */
	public function js_to_footer() 
	{
  		remove_action('wp_head', 'wp_print_scripts');
  		remove_action('wp_head', 'wp_print_head_scripts', 9);
  		remove_action('wp_head', 'wp_enqueue_scripts', 1);
	}

	/**
	 * remove versions from scripts/styles.
	 * @since    1.0.0
	 */
	public function remove_script_version($src) 
	{
  		return $src ? esc_url(remove_query_arg('ver', $src)) : false;
	}

	/**
	 * cleanup output of stylesheet <link> tags.
	 * @since    1.0.0
	 */
	public function clean_stylesheets($input) 
	{
		preg_match_all("!<link rel='stylesheet'\s?(id='[^']+')?\s+href='(.*)' type='text/css' media='(.*)' />!", $input, $matches);

		if (empty($matches[2])) return $input;

		// Only display media if it is meaningful
		$media = $matches[3][0] !== '' && $matches[3][0] !== 'all' ? ' media="' . $matches[3][0] . '"' : '';
		return '<link rel="stylesheet" href="' . $matches[2][0] . '"' . $media . '>' . "\n";
	}

	/**
	 * cleanup output of script <script> tags.
	 * @since    1.0.0
	 */
	// public function clean_scripts($input)
	// {
	// 	$input = str_replace("type='text/javascript' ", '', $input);
	// 	return str_replace("'", '"', $input);
	// }

	/**
	 * cleanup output of <body> tag, make usable classes.
	 * @since    1.0.0
	 */
	public function cleanup_body($classes)
	{
		// Add post/page slug if not present
		if (is_single() || is_page() && !is_front_page()) {
			if (!in_array(basename(get_permalink()), $classes)) {
				$classes[] = basename(get_permalink());
			}
		}

		// Remove unnecessary classes
		$home_id_class = 'page-id-' . get_option('page_on_front');
		$remove_classes = [
			'page-template-default',
			$home_id_class
		];
		$classes = array_diff($classes, $remove_classes);

		return $classes;
	}

	/**
	 * remove unnecessary self-closing tags.
	 * @since    1.0.0
	 */
	public function remove_self_closing_tags($input) 
	{
  		return str_replace(' />', '>', $input);
	}
}