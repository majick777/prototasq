<?php

/*
Plugin Name: ProtoTasq
Plugin URI: http://wordquest.org/plugins/prototasq/
Author: Tony Hayes
Description: Next Level Interface for Project Task Management
Version: 0.9.5
Author URI: http://wordquest.org/
*/

// ==============================
// ProtoTasq Loader for WordPress
// ==============================

// -----------------
// Include ProtoTasq
// -----------------
include(dirname(__FILE__).'/prototasq.php');

// -------------------
// Load AJAX Functions
// -------------------
include(dirname(__FILE__).'/ajax.php');

// --------------
// Add Admin Menu
// --------------
add_action('admin_menu', 'pt_add_admin_page');
function pt_add_admin_page() {
	$title = $menu = __('ProtoTasq');
	$settingsmenu = __('ProtoTasq Settings');
	$icon = plugins_url('images/icon.png', __FILE__);
	$icon = apply_filters('pt_menu_icon_url', $icon);
	$position = apply_filters('pt_menu_position', 1);
	add_menu_page($title, $menu, 'manage_options', 'prototasq', 'pt_main_interface', $icon, $position);

	// TODO: find another way to do this (sluf conflict creates duplicate content on page?)
	// add_options_page($title, $settingsmenu, 'manage_options', 'prototasq', 'pt_main_interface_settings');
}

// -------------------------------
// Enqueue Header Script Variables
// -------------------------------
add_action('admin_head', 'pt_header_javascript');
add_action('wp_head', 'pt_header_javascript');

// ---------------
// Enqueue Scripts
// ---------------
// TODO: check frontend / backend contexts
if (function_exists('wp_enqueue_script')) {
	add_action('wp_enqueue_scripts', 'pt_wp_enqueue_scripts');
	add_action('admin_enqueue_scripts', 'pt_wp_enqueue_scripts');
}

function pt_wp_enqueue_scripts() {
	$debug = false;
	$scripts = pt_get_scripts();

	// let WP handle jquery and thickbox loading
	if (isset($scripts['jquery'])) {unset($scripts['jquery']);}
	if (isset($scripts['thickbox'])) {unset($scripts['thickbox']);}
	wp_enqueue_script('thickbox');

	if ($debug) {echo "<!-- Start ProtoTasq Scripts -->";}
	foreach ($scripts as $handle => $script) {
		if (isset($script['url']) || isset($script['path'])) {
			if (!isset($script['deps'])) {$script['deps'] = array();}
			if (isset($script['url'])) {
				if ($debug) {echo PHP_EOL.print_r($script, true).PHP_EOL;}
				wp_enqueue_script($script['slug'], $script['url'], $script['deps'], $script['version'], true);
			} else {
				$script['filepath'] = dirname(__FILE__).'/'.$script['path'];
				if (file_exists($script['filepath'])) {
					$script['url'] = plugins_url($script['path'], __FILE__);
					if (!isset($script['version'])) {$script['version'] = filemtime($script['filepath']);}
					if ($debug) {echo $handle.":".PHP_EOL.print_r($script, true).PHP_EOL;}
					wp_enqueue_script($handle, $script['url'], $script['deps'], $script['version']);
				}
			}
		}
	}
	if ($debug) {echo "<!-- End ProtoTasq Scripts -->";}
}

// --------------
// Enqueue Styles
// --------------
// TODO: check frontend / backend contexts
if (function_exists('wp_enqueue_style')) {
	add_action('wp_enqueue_scripts', 'pt_wp_enqueue_styles');
	add_action('admin_enqueue_scripts', 'pt_wp_enqueue_styles');
}

function pt_wp_enqueue_styles() {
	$debug = false;
	$styles = pt_get_styles();

	// let WP handle thickbox stylesheet
	if (isset($styles['thickbox'])) {unset($styles['thickbox']);}
	wp_enqueue_style('thickbox');

	if ($debug) {echo "<!-- Start ProtoTasq Styles -->";}
	foreach ($styles as $handle => $style) {
		if (isset($style['url']) || isset($style['path'])) {
			if (!isset($style['deps'])) {$style['deps'] = array();}
			if (isset($style['url'])) {
				if ($debug) {echo PHP_EOL.print_r($style,true).PHP_EOL;}
				wp_enqueue_style($style['slug'], $style['url'], $style['deps'], $style['version'], true);
			} else {
				$style['filepath'] = dirname(__FILE__).'/'.$style['path'];
				if (file_exists($style['filepath'])) {
					if (!isset($style['version'])) {$style['version'] = filemtime($style['filepath']);}
					$style['url'] = plugins_url($style['path'], __FILE__);
					if ($debug) {echo $handle.":".PHP_EOL.print_r($style,true).PHP_EOL;}
					wp_enqueue_style($handle, $style['url'], $style['deps'], $style['version']);
				}
			}
		}
	}
	if ($debug) {echo "<!-- End ProtoTasq Styles -->";}
}

// ----------------------------
// Frontend Interface Shortcode
// ----------------------------
add_shortcode('prototasq', 'pt_interface_shortcode');
function pt_interface_shortcode($atts) {
	// TODO: process shortcode atts to interface request ?
	pt_main_interface();
}
