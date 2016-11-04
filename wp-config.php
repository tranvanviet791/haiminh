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

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'haiminh');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         ';`@Pa6QLIYx3c5jv@o<jjHN9^GTGE7%]D`PoIZO~-0wI;7mt>i,OOe0aBX [UypY');
define('SECURE_AUTH_KEY',  'F5`Ji1g5*`5,~x|,3Xmkjjkf<TrgFr]5-TkM$80@p(7*M`Z)<`bIUA4j:%MM:h` ');
define('LOGGED_IN_KEY',    '^7N]z9vsXtk4lhIk[Y#Ull9qyP(eM7]wD)-bAU9fy0p&^I^CO Hlj:!oKACsj,=3');
define('NONCE_KEY',        '+Yp@h:mJUA7%v.N#~xQV9gb%$7.V;,RjO54Tm //_,ETF3KL-jT.W=R8B>pQ,$0/');
define('AUTH_SALT',        '$YOKy}pa6N(G+ t?mEVpm{+hcOVzQr]}Ipfq<)&<bJMKp(|+^J49+#QSDQ2ak9E`');
define('SECURE_AUTH_SALT', '`*S}!o?/ZwtZQ<J}va-qP(2_9KWfm+#|w7=[WjTW~5C**056(:qwgJ$N&8OCProu');
define('LOGGED_IN_SALT',   'dwV|P&+,E.P*ncx[vQEI6gRtP/<Kt u YZCHcSSf5J]SL$5GDw<}ZXpS]/WOTa^0');
define('NONCE_SALT',       '6M~f`tle[9C2r-c:6}Lh|@w:kKQYp8JKcUC3K+yUsW{vH>K.%V?@z@dwZ[`Hlr&u');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
