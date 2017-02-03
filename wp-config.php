<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

//Local
if ($_SERVER['HTTP_HOST'] == "dev-server" || $_SERVER['HTTP_HOST'] == "localhost" || $_SERVER['HTTP_HOST'] == "localhost.wp_boilerplate" ) {
    $dbName = "localhost.wp_boilerplate";
    $dbUser = "root";
    $dbPass = "";
	$debug = true;
}
//demo
else if ($_SERVER['HTTP_HOST'] == "demo.adtrakdesign.co.uk" || $_SERVER['HTTP_HOST'] == "www.adtrakdemo.co.uk" || $_SERVER['HTTP_HOST'] == "ad.trak.agency") {
    $dbName = "";
    $dbUser = "";
    $dbPass = "";
	$debug = true;
}
//live
else {
    $dbName = "";
    $dbUser = "";
    $dbPass = "";
	$debug = false;
}

define('DB_NAME', $dbName);

/** MySQL database username */
define('DB_USER', $dbUser);

/** MySQL database password */
define('DB_PASSWORD', $dbPass);

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

define('WP_DEBUG', $debug);

/** Limit the number of revisions per post **/
define('WP_POST_REVISIONS', 2);

/**#@+
 * Authentication Unique Keys and Salts.
 * UPDATE THESE! Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 */
define('AUTH_KEY',         'put your unique phrase here');
define('SECURE_AUTH_KEY',  'put your unique phrase here');
define('LOGGED_IN_KEY',    'put your unique phrase here');
define('NONCE_KEY',        'put your unique phrase here');
define('AUTH_SALT',        'put your unique phrase here');
define('SECURE_AUTH_SALT', 'put your unique phrase here');
define('LOGGED_IN_SALT',   'put your unique phrase here');
define('NONCE_SALT',       'put your unique phrase here');

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
