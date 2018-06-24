<?php

// ==================================
// ProtoTasq Standalone Config Sample
// ==================================

// Note: currently ProtoTasq is built using a WordPress layer.
// A full standalone loader may (or may not) be created in the future.

// For now this config can enable you to access ProtoTasq directly via
// {SITEURL}/wp-content/plugins/prototasq/
// rather than logging into WordPress and clicking ProtoTasq in the menu.

// You will still need to be logged in to WordPress, however you will notice
// that once you are, if you use the direct URL there is no WP Admin wrapper!
// Thus the advantage of this load method is in terms of loading speed. :-)


// ------
// USAGE:
// ------
// 1. Choose Option A, B or C and follow directions below.
// 	A. manually define an absolute path to WordPress installation
//	B. manually define all database credential constants
//	C. automatically find the installation path (works in 99% of cases)
//	   however this method is incompatible with an alternate URL (see 4.)
// 2. Save this file as config.php in /wp-content/plugins/prototasq/
// 3. Access ProtoTasq via {SITEURL}/wp-content/plugins/prototasq/

// 4. (Optional) Move Direct Interface URL (Options 1A and 1B only!)
//	a. copy (or move) config.php and index.php to an alternative location
// 		eg. /absolute/path/prototasq/
// 	b. define the PT_LOAD_PATH constant to point back to the plugin file directory
// 		define('PT_LOAD_PATH', '/absolute/path/wp-content/plugins/prototasq/');
//  c. access ProtoTasq via your new direct URL
// 		eg. {SITEURL}/prototasq/


// OPTION A: Set Absolute WordPress Load Path
// ------------------------------------------
// Uncomment this and manually define a path to wp-load.php directory
// define('WP_LOAD_PATH', '</some/absolute/path/>');


// ----------
// === OR ===
// ----------


// OPTION C: Explicitly Define Database Constants
// ----------------------------------------------
// a. uncomment and define ALL database credentials
// b. (optional) specify a $table_prefix global value

// a. uncomment and define ALL database credentials
/** MySQL Database Name */
// define('DB_NAME', '');

/** MySQL database username */
// define('DB_USER', '');

/** MySQL database password */
// define('DB_PASSWORD', '');

/** MySQL hostname */
// define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
// define('DB_CHARSET', 'utf8');

/** The Database Collate type. Do not change this if in doubt. */
// define('DB_COLLATE', '');

// b. unlike the above constants, $table_prefix is a variable
// if you use a different table prefix than wp_
// you will probably need to specify it here explicitly:
// $table_prefix = 'wp_';


// ----------
// === OR ===
// ----------


// OPTION C: Automatically Detect WordPress Load Path
// --------------------------------------------------
// you do not need to do anything extra for this, just change the filename
// note: this will work for *most* - but not all - WordPress installations

// first convert /wp-content/plugins/prototasq/ -> /
$wploadpath = dirname(dirname(dirname(dirname(__FILE__))));
if (!defined('WP_LOAD_PATH')) {
	// check in /, /wordpress, /wp (common WordPress locations)
	$paths = array($wploadpath, $wploadpath.'/wordpress', $wploadpath.'/wp');
	foreach ($paths as $filepath) {
		$wploadfilepath = $filepath.'/wp-load.php';
		if (file_exists($wploadfilepath)) {
			// we have automatically found the path to wp-load.php :-)
			if (!defined('WP_LOAD_PATH')) {define('WP_LOAD_PATH', $filepath);}
		}
	}
}

