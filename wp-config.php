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
if ($_SERVER['HTTP_HOST'] == "dev-server" || $_SERVER['HTTP_HOST'] == "localhost" || $_SERVER['HTTP_HOST'] == "localhost:8888" || $_SERVER['HTTP_HOST'] == "localhost.shared_boilerplate" ) {
    $dbName = 'localhost.shared_boilerplate';
    $dbUser = 'root';
    $dbPass = '';
	$debug = true;
}
//demo
else if ($_SERVER['HTTP_HOST'] == "ad.trak.agency") {
    $dbName = '';
    $dbUser = '';
    $dbPass = '';
	$debug = true;
}
//live
else {
    $dbName = '';
    $dbUser = '';
    $dbPass = '';
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

/* define debug */
define('WP_DEBUG', $debug);

/** Limit the number of revisions per post **/
define('WP_POST_REVISIONS', 2);

/**#@+
 * UPDATE THESE! Change these to different unique phrases!
 * PLEASE UPDATE THESE
 * IF YOU DON'T UPDATE THESE ALL SITES COULD BE INSECURE
 * ---------------------------------------------------------
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 */
define('AUTH_KEY',         'J;G2VURn16@9T0qkSShN,kWx[M|X[9:2w9kZsH=?zv0i^fR9:]2Xtx+9Vv>mnx&A');
define('SECURE_AUTH_KEY',  'xW-8OMfUSIH)C#*jsy]~FNoSv&|kXd,!/vWk>juVEn*+[]:Hg,K{Q>3H8<X?RRmV');
define('LOGGED_IN_KEY',    '>N@&|[UWxa$01]#`8:c7+kgbIrv+A4`cAR|yCe!L;+`g?65!N3v_iMcJ,h qN8]8');
define('NONCE_KEY',        'q*?ZfK5^&|_Lk%`?1p4wp^OKY8si5 pAyh^.WGQ6K.FA)x7}H~.4+[}himn/%5<#');
define('AUTH_SALT',        'yvFTQ.X-e^NNTFj `96z?yMivrA*UKo7M%PSqar6fC$sIl/!lz6M-?mu0}&#O }#');
define('SECURE_AUTH_SALT', 'AXLs-[T4k$grc|_o%Ate&J|2?4WZ /8%DTiS #@`tmht8{OZ!DXC~/;a2-#anOw3');
define('LOGGED_IN_SALT',   'TC2mcQ6T~*L2679SD(|q8ndhFi(Fu{aEKO8 ]^m|/T*|z@C2*mYfK8<vQo7YlN%1');
define('NONCE_SALT',       '?UEuM>+|,||]ccOPJ732(ZmFkmAz+]kh6V-aO%.t{8J)4M&ulIK}Si.$|O5/>p-o');

// Change this per client
$table_prefix = 'adtrakwp_';


/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
