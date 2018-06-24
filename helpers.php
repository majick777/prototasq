<?php

// ========================
// === Helper Functions ===
// ========================

// ====
// Misc
// ====

// ------------
// Get AJAX URL
// ------------
function pt_get_ajax_url() {
	if (function_exists('admin_url')) {$ajaxurl = admin_url('admin-ajax.php');}
	else {$ajaxurl = '';} // for non-WP just send request to self URL
	return $ajaxurl;
}

// --------------
// Get Plugin URL
// --------------
function pt_get_plugin_url() {
	if (function_exists('plugins_url')) {$pluginurl = plugins_url('', __FILE__);}
	else {
		// TODO: build current URL from $_SERVER global ?
		// TEMP: set blank for relative path loading
		$pluginurl = '';
	}
	return $pluginurl;
}


// ----------------------
// Output Response Iframe
// ----------------------
function pt_response_iframe($ref, $hide=true, $width='600', $height='80') {
	$debug = false;
	if ($debug || !$hide) {$hide = '';} else {$hide = " style='display:none;'";}
	echo "<iframe src='javascript:void(0);' id='pt-".$ref."-frame' name='pt-".$ref."-frame' width='".$width."' height='".$height."' frameborder='0'".$hide."></iframe>".PHP_EOL;
}

// ----------------------
// Output Stylesheet Link
// ----------------------
function pt_stylesheet() {
	if (function_exists('plugins_url')) {$styleurl = plugins_url('styles/prototasq.css', __FILE__);}
	else {$styleurl = 'styles/prototasq.css';}
	$styleurl .= '?version='.@filemtime(dirname(__FILE__).'/styles/prototasq.css');
	echo "<link rel='stylesheet' id='prototasq-css' href='".$styleurl."' type='text/css' media='all'>";
}

// --------------------
// Auto Resize Thickbox
// --------------------
function pt_auto_resize_thickbox($tbelement) {
	echo "<script>
	divwidth = document.getElementById('".$tbelement."').offsetWidth;
	divheight = document.getElementById('".$tbelement."').offsetHeight;
	document.getElementById('TB_ajaxContent').style.width = divwidth+'px';
	document.getElementById('TB_ajaxContent').style.height = divheight+'px';
	document.getElementById('TB_window').style.width = divwidth+'px';
	document.getElementById('TB_window').style.height = divheight+'px';
	document.getElementById('TB_window').style.maxWidth = divwidth+'px';
	</script>";
}

// -------------------
// Get Current User ID
// -------------------
function pt_get_current_user_id() {

	if (function_exists('wp_get_current_user')) {
		global $current_user;
		$current_user = wp_get_current_user();
		return $current_user->ID;
	} else {
		// TODO: non-WP method of getting user ID ?
		// ? would require a method for user logging in ?
		return false;
	}

}

// --------------------
// Get Project Template
// --------------------
function pt_get_project_template($slug) {

	// search for PHP or JSON template
	$templatedir = dirname(__FILE__).'/templates';
	$templatedir = apply_filters('pt_project_template_dir', $templatedir);
	$phppath = $templatedir.'/'.$slug.'.php';
	$jsonpath = $templatedir.'/'.$slug.'.json';
	if (file_exists($phppath)) {$templatepath = $phppath;}
	elseif (file_exists($jsonpath)) {$templatepath = $jsonpath;}
	else {return array();}

	// filter and return project template data
	$templatepath = apply_filters('pt_project_template_path', $templatepath);
	if (substr($templatepath, -4, 4) == '.php') {
		if (file_exists($templatepath)) {include($templatepath); return $template;}
	} elseif (substr($templatepath, -5, 5) == '.json') {
		if (file_exists($templatepath)) {$template = json_decode(file_get_contents($templatepath), true);}
	}
	if ( (isset($template)) && (is_array($template)) ) {
		return apply_filters('pt_project_template', $template);
	}
	return array();
}

// -------------------------
// Get Project Template List
// -------------------------
function pt_get_project_template_list() {

	$templatedir = dirname(__FILE__).'/template/';
	$templatedir = apply_filters('pt_project_template_dir', $templatedir);
	if (!is_dir($templatedir)) {return array();}

	$templates = array();
	$files = scandir($templatedir);
	foreach ($files as $file) {
		if ( ($file != '.') && ($file != '..') ) {
			if (substr($file, -4, 4) == '.php') {$templates[] = substr($file, 0, -4);}
			elseif (substr($file, -5, 5) == '.json') {$templates[] = substr($file, 0, -5);}
		}
	}
	return apply_filters('pt_project_template_list', $templates);
}

// ----------------------------
// Load Database Delta Function
// ----------------------------
// TODO: maybe remove db_delta file as probably not needed
function pt_load_db_delta() {
	// load dbDelta function (original via WP or copy)
	if (defined('ABSPATH')) {require(ABSPATH.'wp-admin/includes/upgrade.php');}
	else {require(dirname(__FILE__).'/includes/db-delta.php');}
}


// ===========
// Record Data
// ===========

// -------------
// Update Record
// -------------
// TODO: add a standalone function ?
function pt_update_data_record($data) {
	if (function_exists('wp_update_post')) {return wp_update_post($data);}
	else {}
}

// -------------
// Insert Record
// -------------
// TODO: standalone function
function pt_insert_data_record($data) {
	if (function_exists('wp_insert_post')) {return wp_insert_post($data);}
	else {}
}

// ----------------
// Get Record Count
// ----------------
function pt_get_record_count($type, $userid=false) {
	global $wpdb; $tablename = $wpdb->prefix."posts"; $posttype = 'pt-'.$type;
	$query = "SELECT COUNT(*) FROM ".$tablename." WHERE post_type = '".$posttype."'";
	// $query = "SELECT COUNT(*) FROM %s WHERE post_type = '%s'";
	// $query = $wpdb->prepare($query, $tablename, $posttype);
	if ($userid) {$query .= " AND post_author = '".$userid."'";}
	$count = $wpdb->get_var($query);
	if (!$count) {$count = 0;}
	return $count;
}

// ----------
// Get Record
// ----------
function pt_get_record($id, $columns=false) {
	$debug = true;
	global $wpdb; $tablename = $wpdb->prefix."posts";

	// only get wanted columns (see pt_post_data_map function)
	if (!$columns) {
		$columns = "ID,post_author,post_date,post_content,post_title,post_excerpt,post_status,post_password,";
		$columns .= "post_name,post_modified,post_parent,menu_order,post_type,post_mime_type,comment_count";
	}

	// TODO: test wpdb->prepare here
	$query = "SELECT ".$columns." FROM ".$tablename." WHERE ID = '".$id."'";
	// $query2 = "SELECT %s FROM %s WHERE ID = %d";
	// $query2 = $wpdb->prepare($query2, $columns, $tablename, $id);
	// if ($debug) {print_r($query); print_r($query2);}

	$result = $wpdb->get_results($query, ARRAY_A);
	// $result2 = $wpdb->get_results($query2, ARRAY_A);
	// if ($debug) {print_r($result); print_r($result2);}

	if ($result) {return pt_post_data_map($result[0]);}
	return false;
}

// -----------
// Get Records
// -----------
// TODO: (not used) fix function name collision!
// function pt_get_records($ids) {
//	global $wpdb; $tablename = $wpdb->prefix."posts";
//	$idlist = implode(',', $ids);
//	$query = "SELECT * FROM ".$tablename." WHERE ID IN ('".$idlist."')";
// 	// $query = "SELECT * FROM %s WHERE ID IN (%s)";
//	// $query = $wpdb->prepare($query, $tablename, $idlist);
//	$results = $wpdb->get_results($query, ARRAY_A);
//	return $results;
// }

// -----------
// Get Records
// -----------
function pt_get_records($type, $ids=array(), $parentids=array(), $where='', $columns=false) {

	if (is_string($ids)) {
		if (strstr($ids, ',')) {$ids = explode(',', $ids);} else {$ids = array(0 => $ids);}
	}
	if ( (count($ids) > 0) && ($ids[0] == 'all') ) {$ids = array();}
	if ( ($type != 'colony') && (count($parentids) > 0) ) {
		if (count($parentids) > 1) {$where .= " AND post_parent IN (".implode(',', $parentids).")";}
		else {$where .= " AND post_parent = '".$parentids[0]."'";}
		// $where .= " OR post_parent = '' OR post_parent = '0'";
	}

	$ids = pt_get_record_ids($type, $ids, $where);
	if (count($ids) > 0) {
		foreach ($ids as $i => $id) {$records[] = pt_get_record($id, $columns);}
		return $records;
	}
	return array();
}


// --------------
// Get Record IDs
// --------------
function pt_get_record_ids($type, $ids=array(), $where='') {
	global $wpdb; $debug = true;
	$tablename = $wpdb->prefix.'posts';
	$posttype = "pt-".$type;

	$query = "SELECT ID FROM ".$tablename." WHERE post_type = '".$posttype."'";
	// TODO: test $wpdb->prepare here
	// $query = "SELECT ID FROM %s WHERE post_type = '%s'";
	// $query = $wpdb->prepare($query, $tablename, $posttype);

	if ( (is_array($ids)) && (count($ids) > 0) ) {
		$idlist = implode(',', $ids);
		$query .= " AND ID IN (".$idlist.")";
		// $subquery = " AND ID IN (%s)";
		// $query .= $wpdb->prepare($subquery, $idlist);
		// if ($debug) {echo $query;}
	}
	$results = $wpdb->get_results($query.$where, ARRAY_A);
	$data = array();
	foreach ($results as $result) {$data[] = $result['ID'];}
	return $data;
}

// ---------------
// Post Data Map
// ---------------
function pt_post_data_map($post) {

	// so post can be existing data array or post ID
	if (!is_array($post)) {$post = pt_get_record($post);}

	// map post columns to record data
	$map = pt_get_data_map();
	$record = array();
	foreach ($map as $key => $column) {
		if (isset($post[$column])) {$record[$key] = $post[$column];}
	}

	return $record;
}

// ---------------
// Record Data Map
// ---------------
function pt_record_data_map($record) {

	// map record data to post columns
	$map = pt_get_data_map();
	$post = array();
	foreach ($map as $key => $column) {
		if (isset($record[$key])) {$post[$column] = $record[$key];}
	}
	return $post;
}

// ------------
// Get Data Map
// ------------
function pt_get_data_map() {
	$map = array(
		// Data Key		|| WP Post Column
		'id'			=> 'ID',
		'creator'		=> 'post_author',
		'created'		=> 'post_date',
						// post_date_gmt
		'title'			=> 'post_title',
		'content'		=> 'post_content',
		'description'	=> 'post_excerpt',
		'status'		=> 'post_status',
						// comment_status
						// ping_status
		'password'		=> 'post_password',
		'slug'			=> 'post_name',
						// to_ping
						// pinged
		'modified'		=> 'post_modified',
						// post_modified_gmt
						// post_content_filtered
		'parent'		=> 'post_parent',
						// guid
		'priority'		=> 'menu_order',
		'type'			=> 'post_type',
		'privacy'		=> 'post_mime_type',
		'comments'		=> 'comment_count',
	);
	return $map;
}

// ----------------
// Comment Data Map
// ----------------
function pt_get_comment_map() {
	$map = array(
		'ID'			=> 'comment_ID',
		'postid'		=> 'comment_post_ID',
		'author'		=> 'comment_author',
		'email'			=> 'comment_author_email',
		'url'			=> 'comment_author_url',
		'authorip'		=> 'comment_author_IP',
		'date'			=> 'comment_date',
						// comment_date_gmt
		'content'		=> 'comment_content',
						// comment_karma
						// comment_approved
						// comment_agent
		'type'			=> 'comment_type',
		'parent'		=> 'comment_parent',
		'userid'		=> 'user_id',
	);
	return $map;
}


// =====
// Users
// =====

// -------------
// Get All Users
// -------------
function pt_get_users() {
	global $wpdb; $tablename = $wpdb->prefix."users";
	$query = "SELECT ID,user_login,display_name FROM ".$tablename;
	$results = $wpdb->get_results($query, ARRAY_A);
	foreach ($results as $result) {$user[$result['ID']] = $result['display_name']." (".$result['user_login'].")";}
	return $users;
}

// ----------------
// Get User Display
// ----------------
function pt_get_user_display($id) {
	global $wpdb; $tablename = $wpdb->prefix."users";
	$query = "SELECT ID,user_login,display_name FROM ".$tablename." WHERE ID = '".$id."'";
	$results = $wpdb->get_results($query, ARRAY_A);
	if (count($results) > 0) {
		return $results[0]['display_name']." (".$results[0]['user_login'].")";
	}
	return '';
}

// ----------------
// Get Record Roles
// ----------------
function pt_get_record_roles($id) {
	$roles = get_post_meta($id, '_pt_roles', true);
	return $roles;
}

// -------------------
// Update Record Roles
// -------------------
function pt_update_record_roles($id, $roles) {
	return update_post_meta($id, '_pt_roles', $roledata);
}

// --------------
// Get User Roles
// --------------
function pt_get_user_roles($userid) {
	global $prototasq;
	if ( (isset($prototasq['userid'])) && ($userid == $prototasq['userid']) ) {
		if (isset($prototasq['userroles'])) {return $prototasq['userroles'];}
		else {
			$roles = get_user_meta($userid, '_pt_roles', true);
			$prototasq['userroles'] = $roles;
		}
	} else {$roles = get_user_meta($userid, '_pt_roles', true);}
	return $roles;
}

// -----------------
// Update User Roles
// -----------------
function pt_update_user_roles($userid, $roles) {
	return update_user_meta($userid, '_pt_roles', $roles);
}

// -----------------
// Get User Role IDs
// -----------------
function pt_get_user_role_ids($userid) {
	$roleids = array();
	$roles = pt_get_user_roles($userid);
	if (is_array($roles) && (count($roles) > 0)) {
		foreach ($roles as $role => $ids) {$roleids = array_merge($roleids, $ids);}
	}
	return $roleids;
}

// ----------------------
// Get Record Permissions
// ----------------------
// TODO: handle specific actions (eg. edit / delete / archive)
function pt_get_record_permission($id, $type, $action=false) {

	$debug = true;

	// for site admin
	// if (current_user_can('manage_options')) {return true;}

	// for record creator
	$record = pt_get_record($id);
	print_r($record);
	$userid = pt_get_current_user_id();
	if ($record['creator'] == $userid) {return true;}
	if ($debug) {echo "Creator: ".$record['creator']." - User: ".$userid."<br>";}

	// handle view-only permissions via record privacy
	if ($action && ($action == 'view')) {
		$privacy = $record['privacy'];
		if ($debug) {echo "Privacy: ".$privacy."<br>";}
	 	if ($privacy == 'public') {return true;}
	 	// elseif ($privacy == 'group') {} // future use
	 	// elseif ($privacy == 'private') {
	 	// 	note: currently assumed if not public
	 	// }
	}

	$allowed = false;
	$roles = pt_get_user_roles($id);
	if ($debug) {echo "User Roles: ".print_r($roles,true)."<br>";}

	// authority roles (for colony and below)
	$allowedroles = array('admin', 'owner');

	// manager roles (for project and below)
	if ($type != 'colony') {$allowedroles[] = 'manager'; $allowedroles[] = 'client';}

	// worker roles (for task and below)
	if ( ($type == 'task') || ($type == 'subtask') ) {

		// assigned worker role
		$allowedroles[] = 'worker';

		// allows evaluator to view and rate the work done
		if ( ($action == 'view') || ($action == 'rate') ) {$allowedroles[] = 'evaluator';}

		// allows for consultant view / comments (on private tasks)
		if ( ($action == 'view') || ($action == 'comment') ) {$allowedroles[] = 'consultant';}

	}

	if ($roles && (count($roles) > 0)) {
		foreach ($roles as $role => $userids) {
			if (is_array($userids) && (count($userids) > 0)) {
				if (in_array($userid, $userids)) {$allowed = true;}
			}
		}
	}
	return $allowed;
}

// ---------------------
// Get Messages for User
// ---------------------
function pt_get_messages($userid) {

	// TODO: ...
	return array();

}

// ===========
// Colony Data
// ===========

// -------------------
// Get Colony Statuses
// -------------------
function pt_get_colony_statuses($colonyid=false) {
	// TODO: allow for custom colony statuses ?
	// TEMP just use defaults for now
	return pt_colony_status_defaults();
}

// ============
// Project Data
// ============

// ----------------
// Get Project Data
// ----------------
function pt_get_project_data($projectid) {
	// cache the data in a global to prevent duplicate queries
	global $pt_data;
	if (isset($pt_data['project'][$projectid])) {return $pt_data['project'][$projectid];}
	// get data via query if there is nothing cached
	$metakey = '_pt_project_data';
	$settings = pt_get_record_setting($projectid, $metakey);
	if (!$settings) {return array();}
	return $settings;
}

// --------------------
// Get Project Statuses
// --------------------
function pt_get_project_statuses($projectid=false) {
	// TODO: allow for custom project statuses ?
	// if ($projectid) {}

	// TEMP: just use defaults for now
	return pt_project_status_defaults();
}

// -------------------
// Get Project Sectors
// -------------------
function pt_get_sectors($projectid=false) {
	// TEMP: just use defaults for now
	return pt_task_sector_defaults();
	$settings = pt_get_project_data($projectid);
	if (isset($settings['sectors'])) {return $settings['sectors'];}
	return array();
}

// ---------------------
// Get Project Divisions
// ---------------------
function pt_get_divisions($projectid=false) {
	// TEMP: just use defaults for now
	return pt_task_division_defaults();
	$settings = pt_get_project_data($projectid);
	if (isset($settings['divisions'])) {return $settings['divisions'];}
	return array();
}

// ------------------
// Get Project Stages
// ------------------
function pt_get_stages($projectid=false) {
	// TEMP: just use defaults for now
	return pt_task_stage_defaults();
	$settings = pt_get_project_data($projectid);
	if (isset($settings['stages'])) {return $settings['stages'];}
	return array();
}


// =========
// Task Data
// =========

// -----------------
// Get Task Statuses
// -----------------
function pt_get_task_statuses($projectid=false) {
	// TODO: allow for custom project-task statuses ?
	// if ($projectid) {}
	// TEMP: just use defaults for now
	return pt_task_status_defaults();
}

// -------------------
// Get Task Priorities
// -------------------
function pt_get_task_priorities($projectid=false) {
	// TODO: allow for custom project-task statuses ?
	// if ($projectid) {}
	// TEMP: just use defaults for now
	return pt_task_priority_defaults();
}

// ---------------
// Get Task Skills
// ---------------
function pt_get_task_skills($projectid=false) {
	// TODO: allow for custom project-skills ?
	// if ($projectid) {}
	// TEMP: just use defaults for now
	return pt_task_skill_defaults();
}

// --------------------
// Get Task Cell Inputs
// --------------------
// TODO: maybe deprecate as not used ?
function pt_get_cell_inputs($projectid=false) {

	$inputs = array(
		// 'colony'		// only change from task edit screen
		// 'project'	// only change from task edit screen
		'priorities' 	=> pt_get_priorities($projectid),
		'statuses' 		=> pt_get_statuses($projectid),
		'sectors' 		=> pt_get_sectors($projectid),
		'divisions' 	=> pt_get_divisions($projectid),
		'stages' 		=> pt_get_stages($projectid),
		// ...
	);
	return apply_filters('pt_task_inputs', $inputs);
}




// =====================
// Get Selected Settings
// =====================

// --------------------
// Get Current User Tab
// --------------------
// [deprecate] merged with pt_get_selection
// function pt_get_current_tab() {
//	$metakey = '_pt_current_tab';
//	$saved = pt_get_user_setting($metakey);
//	if ($saved) {$tab = $saved;} else {$tab = 'colony';}
// 	return apply_filters('pt_current_tab', $tab);
// }

// -------------------------
// Get User Selected Records
// -------------------------
function pt_get_selection() {
	$metakey = '_pt_selected';
	$saved = pt_get_user_setting($metakey);
	if ($saved) {$selected = $saved;} else {$selected = array();}
	return apply_filters('pt_selected', $selected);
}

// -----------------------
// Update Selected Records
// -----------------------
function pt_update_selected($value) {
	if (is_array($value)) {
		foreach ($value as $key => $vals) {
			$value[$key] = array_unique($vals);
		}
	}
	$metakey = '_pt_selected';
	pt_update_user_setting($metakey, $value);
}

// -------------
// Get Task Data
// -------------
function pt_get_task_data($id) {
	$metakey = '_pt_task_data';
	$taskdata = get_post_meta($id, $metakey, true);
	if (!$taskdata) {return array();}
	return $taskdata;
}

// ----------------
// Update Task Data
// ----------------
function pt_update_task_data($id, $taskdata) {
	$metakey = '_pt_task_data';
	return update_post_meta($id, $metakey, $taskdata);
}

// ----------------
// Get Task Filters
// ----------------
function pt_get_task_filters($projectid=false) {
	// if ($projectid) {
	// 	$metakey = '_pt_task_filters';
	// 	$settings = pt_get_record_setting($projectid, $metakey);
	// 	if ($settings) {return $settings;}
	// }
	$filters = pt_task_filter_defaults();
	return apply_filters('pt_task_filters', $filters);
}

// ------------------------
// Get Task Display Columns
// ------------------------
function pt_get_task_columns() {
	$columns = pt_task_column_defaults();
	$metakey = '_pt_task_columns';
	$saved = pt_get_user_setting($metakey);
	if ($saved && is_array($saved)) {
		foreach ($saved as $slug => $column) {
			$columns[$slug]['position'] = $column['position'];
			if (isset($column['show'])) {$columns[$slug]['show'] = $column['show'];}
		}
	}
	return apply_filters('pt_task_columns', $columns);
}

// ---------------------------
// Update Task Display Columns
// ---------------------------
function pt_update_task_columns($value) {
	$metakey = '_pt_task_columns';
	pt_update_user_setting($metakey, $value);
}


// ========
// Settings
// ========

// ------------------
// Get Plugin Setting
// ------------------
function pt_get_setting($key, $filter=true) {
	global $prototasq;

	// TODO: copy from existing plugin

}

// ------------------
// Get Record Setting
// ------------------
function pt_get_record_setting($id, $metakey) {
	if (function_exists('get_post_meta')) {
		return get_post_meta($id, $metakey, true);
	} else {
		global $wpdb; $tablename = $wpdb->prefix."postmeta";
		$query = "SELECT meta_value FROM ".$tablename." WHERE post_id = '".$id."' AND meta_key = '".$metakey."'";
		// $query = "SELECT meta_value FROM %s WHERE id = '%d' AND meta_key = '%s'";
		// $query = $wpdb->prepare($query, $tablename, $id, $metakey);
		return $wpdb->get_var($query);
	}
}

// ---------------------
// Update Record Setting
// ---------------------
function pt_update_record_setting($id, $metakey, $value) {
	if (function_exists('update_post_meta')) {
		return update_post_meta($id, $metakey, $value);
	} else {
		global $wpdb; $tablename = $wpdb->prefix."postmeta";
		$query = "INSERT INTO ".$tablename." (post_id, meta_key, meta_value) VALUES ('".$id."', '".metakey."', '".$value."')";
		// $query = "INSERT INTO %s (post_id, meta_key, meta_value) VALUES ('%s', '%s', '%s')";
		// $query = $wpdb->prepare($query, $id, $metakey, $value);
		return $wpdb->query($query);
	}
}

// ----------------
// Get User Setting
// ----------------
function pt_get_user_setting($metakey) {

	if (function_exists('get_user_meta')) {
		$userid = pt_get_current_user_id();
		return get_user_meta($userid, $metakey, true);
	} else {
		// TODO: non-WP method of getting user ID ?
		// ? would require a method for user logging in ?
		// global $wpdb; $tablename = $wpdb->prefix."usermeta";
		// $query = "SELECT meta_value FROM ".$tablename." WHERE id = '".$userid."' AND meta_key = '".$metakey."'";
		// return $wpdb->get_var($query);
	}

	// maybe use saved session value
	if (isset($_SESSION[$metakey])) {return json_decode($_SESSION[$metakey], true);}

	// maybe use browser cookie value
	if (isset($_COOKIE[$metakey])) {return json_decode($_COOKIE[$metakey], true);}

	return false;
}

// -------------------
// Update User Setting
// -------------------
function pt_update_user_setting($metakey, $value) {

	if (function_exists('update_user_meta')) {
		$userid = pt_get_current_user_id();
		return update_user_meta($userid, $metakey, $value);
	} else {
		// TODO: non-WP method of getting user ID ?
		// ? would require a method for user logging in ?
		global $wpdb; $tablename = $wpdb->prefix."usermeta";
		$query = "INSERT INTO ".$tablename." (user_id, meta_key, meta_value) VALUES ('".$userid."', '".metakey."', '".$value."')";
		// $query = "INSERT INTO %s (user_id, meta_key, meta_value) VALUES ('%d', '%s', '%s')";
		// $query = $wpdb->prepare($query, $userid, $metakey, $value);
		return $wpdb->query($query);
	}

	// save to session value and cookie
	$value = json_encode($value);
	$_SESSION[$metakey] = json_encode($value);
	$expires = time()+(60*60*24*7); // default 7 days
	$expires = apply_filters('pt_cookie_expiry', $expires);
	setcookie($metakey, $value, $expires);

}

// ---------------------------
// Header Javascript Variables
// ---------------------------
function pt_header_javascript() {

	$nl = PHP_EOL;
	$ajaxurl = pt_get_ajax_url();
	$pluginurl = pt_get_plugin_url();
	$thickboxloading = $pluginurl.'/thickbox/loadingAnimation.gif';

	echo "<script>var ajaxurl = '".$ajaxurl."'; ";
	echo "var pluginurl = '".$pluginurl."'; ";
	echo "var tb_pathToImage = '".$thickboxloading."'; ";

	// TODO: maybe output project input data ?
	// $inputs = pt_get_cell_inputs($projectid);
	// foreach ($input as $input => $data) {
	//	$i = 0;
	//	echo $input." = new Array(); ";
	//	foreach ($data as $values) {
	//		echo $input."[".$i."] = '".$values['slug']."'; "; $i++;
	//	}
	// }

	echo "</script>";
}
