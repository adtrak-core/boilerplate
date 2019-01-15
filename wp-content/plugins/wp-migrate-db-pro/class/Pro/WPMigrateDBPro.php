<?php

namespace DeliciousBrains\WPMDB\Pro;

use DeliciousBrains\WPMDB\WPMigrateDB;

/**
 * Class WPMigrateDBPro
 *
 * Base class for setting up Pro plugin
 *
 * @package DeliciousBrains\WPMDB\Pro
 */
class WPMigrateDBPro extends WPMigrateDB {
	/**
	 * Register WordPress hooks here
	 */
	public function register() {
		parent::register();
		$register_pro = new RegisterPro();
		$register_pro->loadContainer();
		$register_pro->register();
	}
}
