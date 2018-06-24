<?php

// === Resources ===
// - Get Scripts
// - Get Styles
// - Get Labels
// === Colony Defaults ===
// - Colony Statuses
// === Project Defaults ===
// - Project Statuses
// === Task Defaults ===
// - Task Filters
// - Task Columns
// - Task Priorities
// - Task Statuses
// - Task Divisions
// * Task Sectors
// * Task Stages


// =========
// RESOURCES
// =========

// -------
// Scripts
// -------
function pt_get_scripts() {

	// TODO: maybe use minimized scripts
	// if (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) {$suffix = '';} else {$suffix = '.min';}
	$suffix = ''; // TEMP

	$scripts = array(

		/* jQuery */
		'jquery'			=> array('path' => 'scripts/jquery.js', 'version' => '1.12.4'),
		'minilog'			=> array('path' => 'scripts/minilog.js'),

		/* Require */
		'require'			=> array('path' => 'scripts/require'.$suffix.'.js'),
		// (Tarp.require)	// https://github.com/letorbi/tarp.require
		// 'requirejs'		=> array('path' => 'scripts/requirejs.'.$suffix.'.js'),
		// (RequireJS)		// http://requirejs.org/docs/start.html

		/* Interface */
		'thickbox'			=> array('path' => 'thickbox/thickbox.js', 'deps' => array('jquery')),
		'colorpicker'		=> array('path' => 'scripts/colorpicker.js', 'deps' => array('jquery')),
		// 'sortable'			=> array('path' => 'scripts/sortable.js', 'deps' => array('jquery')),
		'prototasq'			=> array('path' => 'scripts/prototasq.js', 'deps' => array('require')),

	);
	return apply_filters('pt_scripts', $scripts);
}

// ------------------
// Get Colony Scripts
// ------------------
function pt_get_colony_scripts() {

	$scripts = array(
		/* Note: Required as needed, but listed here for reference */

		/* ----------------
		/* Loaders/Wrappers */
		/* ---------------- */
		// 'prototasq-colony'	=> array('path' => 'scripts/prototasq-colony.js',
		// 'prototasq-network'	=> array('path' => 'scripts/prototasq-network.js',
		// 'prototasq-uport'	=> array('path' => 'scripts/prototasq-uport.js',

		/* ---------------- */
		/* Identity/Wallets */
		/* ---------------- */
		// 'metamask'		=> array('path' => 'scripts/metamask.js'), // browser injected
		// 'uport-connect'	=> array('path' => 'scripts/uport-connect.min.js', 'version' => '0.7.3'),
							// https://unpkg.com/uport-connect/dist/uport-connect.min.js
		/* -------- */
		/* Ethereum */
		/* -------- */
		// 'buffer'			=> array('path' => 'scripts/buffer.js'),
		// 'ipfs-api'		=> array('path' => 'scripts/ipfs.min.js', 'version' => '22.0.2'),
							// https://unpkg.com/ipfs-api/dist/index.min.js
		// 'web3'			=> array('path' => 'scripts/web3.'.$suffix.'js'),
							// https://cdn.jsdelivr.net/gh/ethereum/web3.js/dist/web3.min.js

		/* ------------------- */
		/* Colony Dependencies */
		/* ------------------- */
		// 'ethers'			=> array('path' => 'scripts/colony/ethers'.$suffix.'.js'),
		//					// https://cdn.ethers.io/scripts/ethers-v3.min.js
		// 'lodash-isequal'	=> array('path' => 'scripts/lodash.isequal.js',
		//					// https://unpkg.com/lodash.isequal@4.5.0/index.js
		// 'lodash-isplainobject'	=> array('path' => 'scripts/lodash.isplainobject.js',
		//					// https://unpkg.com/lodash.isplainobject@4.0.6/index.js
		// 'bn'				=> array('path' => 'scripts/bn.js'),
		//					// source: package @colony-js/flow-typed)
		// 'whatwg-fetch'	=> array('path' => 'scripts/whatwg-fetch.js'),
		//					// https://unpkg.com/whatwg-fetch@2.0.4/fetch.js
		// 'node-fetch'		=> array('path' => 'scripts/node-fetch.js'),
		// 					// https://unpkg.com/node-fetch@2.1.2/browser.js
		// 'isomorphic-fetch'	=> array('path' => 'scripts/isomorphic-fetch.js', 'deps' => array('whatwg-fetch', 'node-fetch')),
		// https://raw.githubusercontent.com/matthew-andrews/isomorphic-fetch/master/fetch-npm-node.js
		// 'util'				=> array('path' => 'scripts/util.js'),
		//					// https://unpkg.com/util@0.11.0/util.js
		// 'assert'			=> array('path' => 'scripts/assert.js', 'deps' => array('util')),
		// 					// https://unpkg.com/assert@1.4.1/assert.js
		//					// https://github.com/defunctzombie/commonjs-assert/blob/master/assert.js
		// 'core-js'		=> array('path' => 'scripts/core-js.js'),
		//					// https://unpkg.com/core-js@2.5.7/ ???????????????????????
		// 'regenerator-runtime' => array('path' => 'scripts/runtime.js'),
		//					//https://unpkg.com/regenerator-runtime@0.11.1/runtime.js
		// 'babel-runtime'	=> array('path' => 'scripts/babel-runtime.js',
		//	'deps' => array('core-js', 'regenerator-runtime')),
		// 'jsonfile'		=> array('path' => 'scripts/jsonfile.js'),
		//					// https://unpkg.com/jsonfile@4.0.0/index.js
		//					// (fs)
		//					// (graceful-fs) https://unpkg.com/graceful-fs@4.1.11/graceful-fs.js (optional)
		// 'web3-utils'		=> array('path' => 'scripts/web3-utils.js'),
		//					// source: package @colony-js/flow-typed)
		// 'object'			=> array('path' => 'scripts/object.js'),
		//					// source: package @colony-js/flow-typed)
		// 'soliditySha3'	=> array('path' => 'colony/soliditySha3.js', 'deps' => array('underscore')),
		// 'underscore'		=> array('path' => 'colony/underscore.js'),
		//					// https://unpkg.com/underscore@1.9.1/underscore.js
		// 'path'			=> array('path' => 'colony/path.js'),
		//					// https://unpkg.com/path@0.12.7/path.js

    	// TODO: reputation mining dependencies
    	// 'express'
   		// 'ganache-core'
    	// 'yargs'

		/* ========= */
		/* Colony JS */
		/* ========= */
		// 'colony-js-contract-loader' => array('path' => 'colony/colony-js-contract-loader'),
		//	'deps' => array('assert', 'babel-runtime')),

		// 'colony-js-contract-loader-http' => array('path' => 'colony/colony-js-contract-loader-http',
		//	'deps' => array('colony-js-contract-loader', 'jsonfile')),

		// 'colony-js-adapter' => array('path' => 'colony/colony-js-adapter',
		//	'deps' => array('colony-js-contract-loader', 'bn')),

		// 'colony-js-utils' => array('path' => 'colony/colony-js-utils',
		//	'deps' => array('assert', 'babel-time', 'bn', 'web3-utils')),

		// 'colony-js-adapter-ethers' => array('path' => 'colony/colony-js-adapter-ethers',
		//	'deps' => array('colony-js-adapter', 'colony-js-contract-loader', 'colony/colony-js-utils',
	    //		'babel-runtime', 'ethers'),

		// 'colony-js-contract-client' => array('path' => 'scripts/colony/colony-js-contract-client.js',
		//	'deps' => array(
		//		'colony-js-adapter', 'colony-js-contract-loader', 'colony/colony-js-utils',
        //		'assert', 'babel-runtime', 'bn', 'bs58', 'lodash.isequal', 'lodash.isplainobject', 'web3-utils'
		//	),

		// 'colony-js-client' => array('path' => 'colony/colony-js-client',
		//	'deps' => array('colony-js-adapter', 'colony-js-contract-client', 'colony-js-contract-loader',
	    //		'colony-js-utils', 'assert', 'babel-runtime', 'bn', 'web3-utils')),

		// 'colony-reputation-miner' => array('path' => 'colony/ReputationMiner.js'),
		//	'deps' => array('colony/colony-js-contract-loader-fs', 'bn', 'ethers', express',
		//    'ganache-core', 'json-file', 'web3-utils', 'yargs')),

		/* ============ */
		/* Integrations */
		/* ============ */
		// 'colony-ipfs'	=> array('path' => 'scripts/colony-ipfs'.$suffix.'.js')),
		// 'colony-tasks'	=> array('path' => 'scripts/colony-tasks'.$suffix.'.js')),
	);
	return apply_filters('pt_colony_scripts', $scripts);
}


// ------
// Styles
// ------
function pt_get_styles() {
	$styles = array(
		'thickbox'			=> array('path' => 'thickbox/thickbox.css'),
		'prototasq'			=> array('path' => 'styles/prototasq.css'),
		'wp-prototasq'		=> array('path' => 'styles/wp-prototasq.css'),
		'colony'			=> array('path' => 'styles/colony.css'),
	);
	return apply_filters('pt_styles', $styles);
}

// ------
// Labels
// ------
function pt_get_labels() {
	$labels = array(
		'colony'			=> array('single' => __('Colony'),	'plural' => __('Colonies'),	'heading' => __('olonies')),
		'project'			=> array('single' => __('Project'),	'plural' => __('Projects'),	'heading' => __('rojects')),
		'sector'			=> array('single' => __('Sector'),	'plural' => __('Sectors')),
		'division'			=> array('single' => __('Division'), 'plural' => __('Divisions')),
		'stage'				=> array('single' => __('Stage'), 	'plural' => __('Stages')),
		'task'				=> array('single' => __('Task'),	'plural' => __('Tasks'),	'heading' => __('asks')),
		'subtask'			=> array('single' => __('Subtask'),	'plural' => __('Subtasks')),
		'message'			=> array('single' => __('Message'),	'plural' => __('Messages'),	'heading' => __('essages')),
		'add-colony'		=> __('Add Colony'),
		'add-project'		=> __('Add Project'),
		'add-sector'		=> __('Add Sector'),
		'add-division'		=> __('Add Division'),
		'add-stage'			=> __('Add Stage'),
		'add-task'			=> __('Add Task'),
		'add-subtask'		=> __('Add Subtask'),
		'add-message'		=> __('Add Message'),
		'add-new-colony'	=> __('Add New Colony'),
		'add-new-project'	=> __('Add New Project'),
		'add-new-sector'	=> __('Add New Sector'),
		'add-new-division'	=> __('Add New Division'),
		'add-new-stage'		=> __('Add New Stage'),
		'add-new-task'		=> __('Add New Task'),
		'add-new-subtask'	=> __('Add New Subtask'),
		'add-new-message'	=> __('Add New Message'),
		'create-colony'		=> __('Create Colony'),
		'create-project'	=> __('Create Project'),
		'create-sector'		=> __('Create Sector'),
		'create-division'	=> __('Create Division'),
		'create-stage'		=> __('Create Stage'),
		'create-task'		=> __('Create Task'),
		'create-subtask'	=> __('Create Subtask'),
		'create-message'	=> __('Create Message'),
		'edit-colony'		=> __('Edit Colony'),
		'edit-project'		=> __('Edit Project'),
		'edit-sector'		=> __('Edit Sector'),
		'edit-division'		=> __('Edit Division'),
		'edit-stage'		=> __('Edit Stage'),
		'edit-task'			=> __('Edit Task'),
		'edit-subtask'		=> __('Edit Subtask'),
		'edit-message'		=> __('Edit Message'),
		'select-colony'		=> __('Select Colonies'),
		'select-project'	=> __('Select Project'),
		'select-sector'		=> __('Select Sector'),
		'select-division'	=> __('Select Division'),
		'select-stage'		=> __('Select Stage'),
		'select-task'		=> __('Select Task'),
		'select-subtask'	=> __('Select Subtask'),
		'edit-message'		=> __('Select Message'),
		'update-colony'		=> __('Update Colony'),
		'update-project'	=> __('Update Project'),
		'update-sector'		=> __('Update Sector'),
		'update-division'	=> __('Update Division'),
		'update-stage'		=> __('Update Stage'),
		'update-task'		=> __('Update Task'),
		'update-subtask'	=> __('Update Subtask'),
		'update-message'	=> __('Update Message'),
		'delete-colony'		=> __('Delete Colony'),
		'delete-project'	=> __('Delete Project'),
		'delete-sector'		=> __('Delete Sector'),
		'delete-division'	=> __('Delete Division'),
		'delete-stage'		=> __('Delete Stage'),
		'delete-task'		=> __('Delete Task'),
		'delete-subtask'	=> __('Delete Subtask'),
		'delete-message'	=> __('Delete Message'),
		'all-colony'		=> __('All Colonies'),
		'all-project'		=> __('All Projects'),
		'all-sector'		=> __('All Sectors'),
		'all-division'		=> __('All Divisions'),
		'all-stage'			=> __('All Stages'),
		'all-task'			=> __('All Tasks'),
		'all-subtask'		=> __('All Subtasks'),
		'all-message'		=> __('All Messages'),
		'no-colony'			=> __('No Colonies Found'),
		'no-project'		=> __('No Projects Found'),
		'no-sector'			=> __('No Sectors Found'),
		'no-task'			=> __('No Tasks Found'),
		'no-subtask'		=> __('No Subtasks Found'),
		'no-message'		=> __('No Messages Found'),
		'filter-status'		=> __('Filter Statuses'),
		'filter-priority'	=> __('Filter Priorities'),
		'filter-sector'		=> __('Filter Sectors'),
		'filter-division'	=> __('Filter Divisions'),
		'filter-stage'		=> __('Filter Stages'),
		'owner'				=> __('Creator'),
		'admin'				=> __('Administrator'),
		'manager'			=> __('Manager'),
		'client'			=> __('Client'),
		'worker'			=> __('Worker'),
		'evaluator'			=> __('Evaluator'),
		'consultant'		=> __('Consultant'),
		'participant'		=> __('Participant'),
	);
	return apply_filters('pt_labels', $labels);
}

// ===============
// Colony Defaults
// ===============

// ---------------
// Colony Statuses
// ---------------
function pt_colony_status_defaults() {
	$statuses = array(
		'draft' 	=> __('Draft'),
		'pending'	=> __('Pending'),
		'local'		=> __('Local'),
		'testing'	=> __('Testing'),
		'tested'	=> __('Tested'),
		'deploying'	=> __('Creating'),
		'deployed'	=> __('Created')
	);
	return apply_filters('pt_colony_status_defaults', $statuses);
}

// ================
// Project Defaults
// ================

// ----------------
// Project Statuses
// ----------------
function pt_project_status_defaults() {
	$statuses = array(
		'draft'		=> __('Draft'),
		'pending'	=> __('Pending'),
		'active'	=> __('Active'),
		'inactive'	=> __('Inactive'),
		'suspended'	=> __('Suspended'),
		'cancelled'	=> __('Cancelled'),
		'archived'	=> __('Archived'),
		'finished'	=> __('Finished')
	);
	return apply_filters('pt_project_status_defaults', $statuses);
}


// =============
// Task Defaults
// =============

// --------------------
// Task Filter Defaults
// --------------------
function pt_task_filter_defaults() {
	$filters = array(
		'priority'	=> pt_task_priority_defaults(),
		'status'	=> pt_task_status_defaults(),
		'stage'		=> pt_task_stage_defaults(),
		'sector'	=> pt_task_sector_defaults(),
		'division'	=> pt_task_division_defaults(),
	);
	return apply_filters('pt_task_filter_defaults', $filters);
}

// --------------------
// Task Column Defaults
// --------------------
function pt_task_column_defaults() {

	$columns = array(
		'id' 			=> array('label' => __('ID'), 		'type' => 'id',		'show' => 'yes', 'position' => 0),
		// 'colony'		=> array('label' => __('Colony'),	'type' => 'view',	'show' => 'yes', 'position' => 1),
		// 'project'	=> array('label' => __('Project'),	'type' => 'view',	'show' => 'yes', 'position' => 2),
		'sector'		=> array('label' => __('Sector'),	'type' => 'select',	'show' => 'yes', 'position' => 3),
		'division'		=> array('label' => __('Division'),	'type' => 'select',	'show' => 'yes', 'position' => 4),
		'stage'			=> array('label' => __('Stage'),	'type' => 'select',	'show' => 'yes', 'position' => 5),
		'status'		=> array('label' => __('Status'),	'type' => 'select',	'show' => 'yes', 'position' => 6),
		'priority'		=> array('label' => __('Priority'),	'type' => 'select',	'show' => 'yes', 'position' => 7),

		'task'			=> array('label' => __('Task'),		'type' => 'task',	'show' => 'yes', 'position' => 8),
		'worker'		=> array('label' => __('Worker'),	'type' => 'view',	'show' => 'yes', 'position' => 9),
		// 'estimated'	=> array('label' => __('Estimated'),	'type' => 'view',	'show' => 'yes', 'position' => 10),
		// 'elapsed'	=> array('label' => __('Elapsed'),	'type' => 'view',	'show' => 'yes', 'position' => 11),
		// 'funding'	=> array('label' => __('Funding'),	'type' => 'view',	'show' => 'yes', 'position' => 12),

	);
	return apply_filters('pt_task_column_defaults', $columns);
}

// ----------------------
// Task Priority Defaults
// ----------------------
function pt_task_priority_defaults() {
	$priorities = array(
		'critical'	=> array('label' => __('Critical'), 	'position' => 950),
		'urgent'	=> array('label' => __('Urgent'),		'position' => 900),
		'very=high'	=> array('label' => __('Very High'),	'position' => 800),
		'high'		=> array('label' => __('High'),			'position' => 700),
		'mid-high'	=> array('label' => __('Mid High'),		'position' => 600),
		'medium'	=> array('label' => __('Medium'),		'position' => 500),
		'mid-low'	=> array('label' => __('Mid Low'),		'position' => 400),
		'low'		=> array('label' => __('Low'),			'position' => 300),
		'very-low'	=> array('label' => __('Very Low'),		'position' => 200),
		'idea'		=> array('label' => __('Idea'),			'position' => 100),
		'ignore'	=> array('label' => __('Ignore'),		'position' => 0),
	);
	return apply_filters('pt_task_priority_defaults', $priorities);
}

// --------------------
// Task Status Defaults
// --------------------
function pt_task_status_defaults() {
	$statuses = array(
		'pending'	=> array('label' => __('Pending'), 		'position' => 0),
		'commenced'	=> array('label' => __('Commenced'), 	'position' => 10),
		'outline'	=> array('label' => __('Outline'), 		'position' => 20),
		'draft'		=> array('label' => __('Drafted'), 		'position' => 30),
		'working'	=> array('label' => __('In Progress'), 	'position' => 40),
		'feedback'	=> array('label' => __('Feedback'), 	'position' => 50),
		'optimized'	=> array('label' => __('Optimized'), 	'position' => 60),
		'blocked'	=> array('label' => __('Blocked'), 		'position' => 70),
		'completed'	=> array('label' => __('Completed'), 	'position' => 80),
		'reviewing'	=> array('label' => __('Reviewing'), 	'position' => 90),
		'done'		=> array('label' => __('Done'), 		'position' => 100),
	);
	return apply_filters('pt_task_status_defaults', $statuses);
}

// --------------------
// Task Sector Defaults
// --------------------
function pt_task_sector_defaults($slug=false) {

	// TEMP: default to project template wordpress-site.php
	if (!$slug) {$slug = 'wordpress-site';}

	global $prototasq;
	if (!isset($prototasq['template'][$slug]['sectors'])) {
		$prototasq['template'][$slug] = pt_get_project_template($slug);
	}
	$sectors = $prototasq['template'][$slug]['sectors'];

	return apply_filters('pt_task_sector_defaults', $sectors);
}

// ----------------------
// Task Division Defaults
// ----------------------
function pt_task_division_defaults($slug=false) {

	// TEMP: default to project template wordpress-site.php
	if (!$slug) {$slug = 'wordpress-site';}

	global $prototasq;
	if (!isset($prototasq['template'][$slug]['divisions'])) {
		$prototasq['template'][$slug] = pt_get_project_template($slug);
	}
	$divisions = $prototasq['template'][$slug]['divisions'];

	return apply_filters('pt_task_division_defaults', $divisions);
}

// -------------------
// Task Stage Defaults
// -------------------
function pt_task_stage_defaults($slug=false) {

	// TEMP: default to project template wordpress-site.php
	if (!$slug) {$slug = 'wordpress-site';}

	global $prototasq;
	if (!isset($prototasq['template'][$slug]['stages'])) {
		$prototasq['template'][$slug] = pt_get_project_template($slug);
	}
	$stages = $prototasq['template'][$slug]['stages'];

	return apply_filters('pt_task_stage_defaults', $stages);
}

// -------------------
// Task Skill Defaults
// -------------------
function pt_task_skill_defaults($slug=false) {

	// TEMP: default to project template wordpress-site.php
	if (!$slug) {$slug = 'wordpress-site';}

	global $prototasq;
	if (!isset($prototasq['template'][$slug]['skills'])) {
		$prototasq['template'][$slug] = pt_get_project_template($slug);
	}
	$skills = $prototasq['template'][$slug]['skills'];

	return apply_filters('pt_task_skill_defaults', $skills);
}

