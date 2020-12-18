<?php

/**
 * Returns an instance of the host plugin class where the UpdraftCentral "central" folder is being
 * integrated.
 */
class UpdraftCentral_Factory {

	/**
	 * Creates a host plugin instance for the given plugin name if found. Otherwise, it will return null
	 *
	 * @param string $plugin The name of the host plugin that the "central" folder is currently embedded
	 * @return object|null
	 */
	public static function create_host($plugin) {
		// N.B. You can add additional host plugins here. Just make sure that you will create
		// a host class for that particular plugin (see updraftplus/central/updraftplus.php as an example).
		$mapped_classes = array(
			'updraftplus' => 'UpdraftPlus_Host',
			// 'wp-optimize' => 'WPOptimize_Host'
		);

		if (!isset($mapped_classes[$plugin])) return null;

		$host_class = $mapped_classes[$plugin];
		if (!class_exists($host_class)) {
			$central_folder = defined('UPDRAFTCENTRAL_CLIENT_DIR') ? UPDRAFTCENTRAL_CLIENT_DIR : dirname(__FILE__);
			if (file_exists($central_folder.'/'.$plugin.'.php')) {
				include_once($central_folder.'/'.$plugin.'.php');
			} else {
				return null;
			}
		}

		return $host_class::instance();// phpcs:ignore PHPCompatibility.Syntax.NewDynamicAccessToStatic.Found
	}
}

global $updraftcentral_host_plugin;
$updraftcentral_host_plugin = UpdraftCentral_Factory::create_host(defined('UPDRAFTCENTRAL_HOST_PLUGIN') ? UPDRAFTCENTRAL_HOST_PLUGIN : 'updraftplus');
