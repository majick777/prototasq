<?php

// ===========================
// Standalone ProtoTasq Loader
// ===========================

// --------------------------------
// Require Standalone Configuration
// --------------------------------
$config = dirname(__FILE__).'/config.php';
if (file_exists($config)) {

	require($config);

	if (defined('WP_LOAD_PATH')) {
		if (is_dir(WP_LOAD_PATH)) {
			$wpload = WP_LOAD_PATH.'/wp-load.php';
			if (file_exists($wpload)) {
				// TODO: use SHORTINIT load of WordPress install
				// (requires specifying exact files to load)
				// define('SHORTINIT', true);
				require($wpload);
			} else {
				echo "Error! WordPress database load configuration file not found.<br>";
				echo "File Path: ".$wpload; exit;
			}
		} else {
			echo "Error! WordPress database load configuration directory not found.<br>";
			echo "Path: ".WP_LOAD_PATH; exit;
		}

	} else {

		if (!defined('DB_NAME') || (DB_NAME == '')) {echo "Error! No Database Name specified."; exit;}
		elseif (!defined('DB_USER') || (DB_USER == '')) {echo "Error! No Database User specified."; exit;}
		elseif (!defined('DB_PASSWORD') || (DB_PASSWORD == '')) {echo "Error! No Database Password specified."; exit;}
		elseif (!defined('DB_HOST') || (DB_HOST == '')) {echo "Error! No Database Host specified."; exit;}
		// elseif (!defined('DB_CHARSET') || (DB_HOST == '')) {echo "Error! No Database Character Set specified."; exit;}
		// elseif (!defined('DB_COLLATE')) {echo "Error! No Database Collation specified."; exit;}

		// TODO: verify database credentials via MySQL connection ?

	}
} else {echo "Error! Standalone Configuration File was not found!"; exit;}

// --------------------------
// Define ProtoTasq Load Path
// --------------------------
if (!defined('PT_LOAD_PATH')) {define('PT_LOAD_PATH', dirname(__FILE__));}

// ------------------
// Start User Session
// ------------------
$sessionid = (string)session_id();
if (empty($sessionid)) {session_name('PHPSESSID'); session_start();}


// =================
// WordPress Classes
// =================
// (imported for cross-compatible standalone loading)

// ------------------------
// WordPress Database Class
// ------------------------
// Source: /wp-includes/wp-db.php
if (!class_exists('wpdb')) {require(PT_LOAD_PATH.'/includes/wp-db.php');}
if (!isset($wpdb)) {$wpdb = new wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);}

// ----------------------
// WordPress Filter Class
// ----------------------
if (!class_exists('WP_Hook')) {require(PT_LOAD_PATH.'/includes/class-wp-hook.php');}


// ===================
// WordPress Functions
// ===================
// (imported for cross-compatible standalone loading)

// TODO: replace dummy functions with copies of the real functions!
// include(dirname(PT_LOAD_PATH).'/wp-functions.php');

// ------------------
// WP Dummy Functions
// ------------------
// ...TEMP... (do nothing)

// actions and filters dummy functions
if (!function_exists('add_action')) {function add_action($hook, $action, $priority=10) {} }
if (!function_exists('remove_action')) {function remove_action($hook, $action, $priority=10) {} }
if (!function_exists('do_action')) {function do_action($hook) {} }
if (!function_exists('add_filter')) {function add_filter($filter, $action, $priority=10) {} }
if (!function_exists('remove_filter')) {function remove_filter($filter, $action, $priority=10) {} }
if (!function_exists('apply_filters')) {function apply_filters($filter, $value) {return $value;} }


// ==============
// Custom Filters
// ==============
// Usage: create a filters.php to modify defaults using standard WordPress functions
// (Note: current directory by default. Filepath can be overridden in config.php)
if (defined('PT_FILTER_PATH')) {$filters = PT_FILTER_PATH; if (file_exists($filters)) {include($filters);}}
elseif (file_exists(PT_LOAD_PATH.'/filters.php')) {$filters = PT_LOAD_PATH.'/filters.php'; include($filters);}
elseif (file_exists(dirname(__FILE__).'/filters.php')) {$filters = dirname(__FILE__).'/filters.php'; include($filters);}


// ==============
// Load ProtoTasq
// ==============
include_once(PT_LOAD_PATH.'/prototasq.php');


// =============
// Template Tags
// =============

// -----------------------
// Open HTML and Body Tags
// -----------------------
function pt_html_body_open($classes=false) {
	echo "<html>".PHP_EOL;
	echo "<body";
		if ($classes) {echo " class='".$classes."'";}
	echo ">".PHP_EOL;
}

// ------------------------
// Close HTML and Body Tags
// ------------------------
function pt_html_body_close() {
	echo "</body>".PHP_EOL."</html>".PHP_EOL;
}

// ------------------
// Output Script Tags
// ------------------
function pt_script_tags() {

	// header javascript variables
	pt_header_javascript();

	$scripts = pt_get_scripts();
	foreach ($scripts as $handle => $script) {
		if (isset($script['url']) || isset($script['path'])) {
			if (!isset($script['url'])) {
				$script['filepath'] = PT_LOAD_PATH.'/'.$script['path'];
				$script['url'] = $script['path']; // relative URL
				if (!isset($script['version'])) {@filemtime($script['filepath']);}
			}
			if (isset($script['version'])) {$script['url'] .= '?version='.$script['version'];}
			echo "<script src='".$script['url']."'></script>";
		}
	}
}

// -----------------
// Output Style Tags
// -----------------
function pt_style_tags() {

	$styles = pt_get_styles();
	if (isset($styles['wp-prototasq'])) {unset($styles['wp-prototasq']);}

	foreach ($styles as $style) {
		if (isset($style['url']) || isset($style['path'])) {
			if (!isset($style['url'])) {
				$style['filepath'] = PT_LOAD_PATH.'/'.$style['path'];
				$style['url'] = $style['path']; // relative URL
				if (!isset($style['version'])) {$style['version'] = @filemtime($style['filepath']);}
			}
			if (isset($style['version'])) {$style['url'].'?version='.$style['version'];}
			echo "<link rel='stylesheet' href='".$style['url']."'>";
		}
	}
}

// --------------------
// Output HTML Head Tag
// --------------------
function pt_html_head() {
	echo "<head>".PHP_EOL;
	pt_script_tags();
	pt_style_tags();
	echo "</head>".PHP_EOL;
}


// ============
// AJAX Actions
// ============
if ( (isset($_REQUEST['action'])) && (!function_exists('add_action')) ) {

	$action = $_REQUEST['action'];

	// include AJAX functions
	include(PT_LOAD_PATH.'/ajax.php');

	// update selection
	if ($action == 'pt_update_selection') {pt_update_selection();}

	// load task interface
	if ($action == 'pt_load_task_interface') {pt_load_task_interface();}

	// add / update / delete record
	if ($action == 'pt_add_record') {pt_add_record();}
	if ($action == 'pt_update_record') {pt_update_record();}
	if ($action == 'pt_delete_record') {pt_delete_record();}
	// if ($action == 'pt_archive_record') {pt_archive_record();}

	// create / update task select input
	if ($action == 'pt_create_select') {pt_select_ajax('create');}
	elseif ($action == 'pt_update_select') {pt_select_ajax('update');}

	exit;
}


// =======================
// Main Interface Template
// =======================

// maybe open html and body tags
pt_html_body_open();

// print script and style tags in head
pt_html_head();

// load main interface
pt_main_interface();

// close html and body tags
pt_html_body_close();

// ------------------------------
// Join me in standing alone. O_o
