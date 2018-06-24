<?php

// =================
// === PROTOTASQ ===
// =================

// === Load ===
// - Load Default Data Arrays
// - Load Helper Functions
// === Main Interface ===
// - Main Interface Settings Link
// - Main Interface
// - Domain List Tabs
// - Checkbox List
// - Domain List Buttons
// - Task Header Row Menu
// - Column Display Cell
// - Order Shift Arrow Cell
// === Task Interface ===
// - Tasks Interface
// - Subtasks Interface
// - Create Task Row
// - Create ID Cell
// - Create Select Cell
// - Create Select Cell Div
// - Create Task Link Cell


// ------------------------
// Load Default Data Arrays
// ------------------------
include(dirname(__FILE__).'/defaults.php');

// ---------------------
// Load Helper Functions
// ---------------------
include(dirname(__FILE__).'/helpers.php');

// ==============
// MAIN INTERFACE
// ==============

// -----------------------
// Main Interface Settings
// -----------------------
// admin menu to settings link
function pt_main_interface_settings() {pt_main_interface(true);}

// --------------
// Main Interface
// --------------
function pt_main_interface($settings=false) {

	$debug = true;
	global $prototasq; $nl = PHP_EOL;

	// set labels
	if (!isset($prototasq['labels'])) {$prototasq['labels'] = pt_get_labels();}
	$labels = $prototasq['labels'];

	// set common variables
	$ajaxurl = $prototasq['ajaxurl'] = pt_get_ajax_url();
	$pluginurl = $prototasq['pluginurl'] = pt_get_plugin_url();
	$userid = $prototasq['userid'] = pt_get_current_user_id();
	$filters = $prototasq['filters'] = pt_get_task_filters();
	// if ($debug) {echo "Filter Options: ".print_r($filters,true)."<br>";}

	// thickbox dimensions
	$prototasq['tbheight'] = $tbheight = '480';
	$prototasq['tbwidth'] = $tbwidth = '640';

	// set main domain tabs
	$domains = array('colony', 'project', 'task');
	// TODO: re-add messages tab when message interface ready
	// $domains[] = 'message';

	// get saved user request data
	// TODO: allow for querystring selection overrides
	$selected = pt_get_selection();
	if (isset($_REQUEST['tab'])) {$currenttab = $_REQUEST['tab'];}
	if (isset($selected['currenttab'])) {$currenttab = $selected['currenttab'];}
	else {$currenttab = 'info';}
	if ( (isset($_REQUEST['welcome'])) && ($_REQUEST['welcome'] == 'true') ) {$currenttab = 'info';}
	// fill any missing selected data keys
	foreach ($domains as $domain) {
		if (!isset($selected[$domain])) {$selected[$domain] = array();}
	}
	foreach ($filters as $filterkey => $filter) {
		if (!isset($selected[$filterkey])) {$selected[$filterkey] = array();}
	}
	$prototasq['selected'] = $selected;
	// if ($debug) {echo "Selected: ".print_r($selected,true).PHP_EOL;}

	// TODO: search form and search results ?
	// pt_search_results();

	// get existing selected data
	$data['colony'] = pt_get_record_ids('colony', $selected['colony']);
	$colonyidlist = implode(',', $data['colony']);
	$data['project'] = pt_get_record_ids('project', $selected['project']);
	$projectidlist = implode(',', $data['project']);
	// $data['sector'] = pt_get_record_ids('sector', $selected['sector']);
	$data['task'] = pt_get_record_ids('task', $selected['task']);
	$taskidlist = implode(',', $data['task']);
	// $data['subtask'] = pt_get_record_ids('subtask', $selected['subtask']);
	// if ($debug) {print_r($data);}

	if ($debug) {
		if (isset($_GET['clearsaved'])) {pt_update_user_setting('_pt_task_columns', array());}
		$saved = pt_get_user_setting('_pt_task_columns');
		// echo "Saved Column States: ".print_r($saved,true).PHP_EOL;
	}

	// open main interface
	echo "<div id='pt-interface'>".$nl;
	$tabsmenu = ''; $tabscontent = '';

	// Domain Tabs Menu
	// ----------------
	// echo "<center>";
	echo "<ul id='pt-domain-menu' class='pt-menu-inline'>".$nl;
	// echo "<li class='pt-menu-item-space pt-menu-item'></li>".$nl;

	// plugin tab
	echo "<li id='pt-menu-tab-info' class='pt-domain-menu-item pt-menu-item";
		if ($currenttab == 'info') {echo " pt-menu-item-active";} else {echo " pt-menu-item-inactive";}
	echo "'>";

		// plugin icon
		echo "<div class='pt-inline pt-button-icon pt-main-icon'></div>";

		// plugin heading
		$onclick = 'pt_showdomaintab("info");';
		echo "<div id='pt-main-heading' class='pt-inline pt-domain-menu-heading' onclick='".$onclick."'>".__('ProtoTasq')."</div>";

		// settings link
		// if (current_user_can('manage_options')) {
			$settingsurl = $ajaxurl.'?action=pt_settings_form&width='.$tbwidth.'&height='.$tbheight;
			echo "<div class='pt-inline'><a name='".__('Settings')."' class='thickbox' href='".$settingsurl."'>";
			echo "<div class='pt-settings-button pt-button-icon' style='margin-left:10px;'></div></a></div>";
		//}

	echo "</li>";

	// loop domain tabs
	foreach ($domains as $domain) {

		// list spacer
		echo "<li class='pt-menu-item-spacer pt-menu-item'></li>".$nl;

			// record label
			$onclick = 'pt_showdomaintab("'.$domain.'");';
			$id = 'pt-menu-tab-'.$domain;
			echo "<li id='".$id."' class='pt-domain-menu-item pt-menu-item ";
				if ($domain == $currenttab) {echo "pt-menu-item-active";} else {echo "pt-menu-item-inactive";}
			echo "'>".$nl;

			$letter = substr($domain, 0, 1);
			echo "<div class='pt-inline pt-letter pt-letter-".$letter."' onclick='".$onclick."'></div>";
			echo "<div class='pt-inline pt-domain-menu-heading' onclick='".$onclick."'>".$labels[$domain]['heading']."</div>".$nl;

			// record count
			if ($domain == 'message') {$count = pt_get_message_count($userid);}
			else {$count = pt_get_record_count($domain, $userid);}
			echo "<div class='pt-inline pt-item-count'>".$count."</div>".$nl;

			// add record button
			$addurl = $ajaxurl.'?action=pt_create_record_form&type='.$domain.'&width='.$tbwidth.'&height='.$tbheight;
			$addlabel = $labels['add-new-'.$domain];
			echo "<div class='pt-inline'><a name='".$addlabel."' class='thickbox' href='".$addurl."'>".$nl;
			echo "<div id='pt-add-new-".$domain."' class='pt-add-button pt-menu-button pt-button-icon' title='".$addlabel."'></div></a></div>".$nl;

		echo "</li>".$nl;

	}
	echo "</ul>".$nl;
	// echo "</center>";

	// Info Tab
	// --------
	$infotab = "<div id='pt-select-info' class='pt-select-list'";
		if ($currenttab != 'info') {$infotab .= " style='display:none;'";}
	$infotab .= ">".$nl;

		$infotab .= "<div id='pt-select-info-wrap'>";

			if (function_exists('plugins_url')) {$imageurl = plugins_url('images/prototasq.png', __FILE__);}
			else {$imageurl = 'images/prototasq.png';}

			// logo image
			$infotab .= "<span id='pt-logo-wrap'>";
				$infotab .= "<img src='".$imageurl."' width='128' height='128' id='pt-logo-image' alt='".__('Prototasq Logo')."'>".$nl;
			$infotab .= "</span>";

			// info tab content
			$infotab .= "<span id='pt-info-tab-content'>";

				// welcome header
				$infotab .= "<h3 id='pt-header-info' class='pt-selection-header'>";
				$infotab .= __('Welcome to ProtoTasq!')."</h3>".$nl;
				$infotab .= "<span class='pt-info-tagline'>";
					$infotab .= "...".__('next level task interface')."...";
				$infotab .= "</span><br>";

				// experimental note
				$infotab .= "<p class='pt-info'>";
					$infotab .= "<b>".__('Experimental Prototype Version')."</b><br>";
					$infotab .= __('Some features may or may not work as intended yet.')." :-)<br>";
					$infotab .= __('Try refreshing the page if you experience problems.')."<br>";
				$infotab .= "</p>";

				$onclick = 'showhide("quickstart-list");';
				$infotab .= "<a href='javascript:void(0);' id='pt-quickstart-link' onclick='".$onclick."'>";
				$infotab .= "<div id='pt-quickstart' class='pt-subheading'>".__('QuickStart')."</div></a>";

				$infotab .= "<ul id='pt-quickstart-list' class='pt-info-list'";
					if (!isset($_REQUEST['welcome'])) {$infotab .= " style='display:none;'";}
				$infotab .= ">";
					$infotab .= "<b>".__('Create')."</b>";
					$infotab .= "<ol id='pt-quickstart-create' class='pt-info-list'>";
						$infotab .= "<li class='pt-info-list-item'>".__('Create a Colony by click the + button!')."</li>";
						$infotab .= "<li class='pt-info-list-item'>".__('Create a Project in the Colony likewise.')."</li>";
						$infotab .= "<li class='pt-info-list-item'>".__('Create multiple Tasks for your Project.')."</li>";
					$infotab .= "</ol>";

					$infotab .= "<b>".__('Select')."</b>";
					$infotab .= "<ol id='pt-quickstart-create' class='pt-info-list'>";
						$infotab .= "<li class='pt-info-list-item'>".__('Use the tabbed menu to view record Selection Lists.')."</li>";
						$infotab .= "<li class='pt-info-list-item'>".__('Select Records for that type and click Load Tick.')."</li>";
						// $infotab .= "<li class='pt-info-list-item'>".__('Optionally select filters for that type and Load.')."</li>";
						$infotab .= "<li class='pt-info-list-item'>".__('Navigate tabs and repeat to load what you want.')."</li>";
						$infotab .= "<li class='pt-info-list-item'>".__('Click Load Task Interface at any time.')."</li>";
					$infotab .= "</ol>";

					$infotab .= "<b>".__('Edit')."</b>";
					$infotab .= "<ol id='pt-quickstart-edit' class='pt-info-list'>";
						$infotab .= "<li class='pt-info-list-item'>".__('Click the name of any record in the select list to edit it.')."</li>";
						$infotab .= "<li class='pt-info-list-item'>".__('Click any cell in the Task Interface table to edit it.')."</li>";
					$infotab .= "</ol>";
				$infotab .= "</ol>";

				// TODO: more info
				$infotab .= "<p class='pt-info'>";
					$infotab .= '';
				$infotab .= "</p>";

				// TODO: plugin info links
				// - documentation
				// - github
				// - wordquest
				// - bioship

			$infotab .= "</span>";

		$infotab .= "</div>";

		// quick add buttons
		// $infotab .= "<span id='pt-quickadd-buttons'>";
		// $infotab .= "<div id='pt-quickadd-header'>".__('Quick Add Buttons')."</div>";
		// foreach ($domains as $domain) {
		//	$addbutton = pt_add_domain_button($domain);
		//	$infotab .= $addbutton['html'];
		// }
		// $infotab .= "</span>";

	$infotab .= "</div>";
	$tabscontent .= $infotab;

	// Domain Tabs
	// -----------
	foreach ($domains as $domain) {
		$html = "<div id='pt-select-".$domain."' class='pt-select-list'";
			if ($currenttab != $domain) {$html .= " style='display:none;'";}
		$html .= ">".$nl;
			if ($domain == 'message') {$html .= pt_messages_list_tab();}
			else {$html .= pt_domain_list_tab($domain, $data);}
		$html .= "</div>".$nl;

		// output message tab first so it is not part of task form
		if ($domain == 'message') {$messagetab = $html;} else {$tabscontent .= $html;}
	}

	// Load Task Selection Form
	// ------------------------
	echo "<form id='pt-selection-form' target='pt-selection-frame' action='".$ajaxurl."' method='post'>".$nl;
	echo "<input type='hidden' name='action' value='pt_load_task_interface'>".$nl;
	echo "<input type='hidden' id='pt-current-tab' name='currenttab' value='".$currenttab."'>".$nl;
	echo "<input type='hidden' name='colonyids' id='pt-colonyid-list' value='".$colonyidlist."'>".$nl;
	echo "<input type='hidden' name='projectids' id='pt-projectid-list' value='".$projectidlist."'>".$nl;
	echo "<input type='hidden' name='taskids' id='pt-taskid-list' value='".$taskidlist."'>".$nl;

	// set current column order and display states
	$showcolumns = array();
	$columns = pt_get_task_columns();
	foreach ($columns as $slug => $column) {
		if (!isset($column['show'])) {echo "***".$slug."***";}
		if ($column['show'] == 'yes') {$showcolumns[] = $slug;}
		$ordercolumns[$column['position']] = $slug;
	}
	sort($ordercolumns);

	// hidden inputs for column display and order
	$columndisplay = $columnorder = '';
	if (count($showcolumns) > 0) {$columndisplay = implode(',', $showcolumns);}
	echo "<input type='hidden' name='column-display' id='pt-column-display-state' value='".$columndisplay."'>";
	if (count($ordercolumns) > 0) {$columnorder = implode(',', $ordercolumns);}
	echo "<input type='hidden' name='column-order' id='pt-column-display-state' value='".$columnorder."'>";

	// output the domain tabs created above (inside the form)
	echo $tabscontent;

	// Load Selected Tasks Button
	// --------------------------
	$taskcount = count($selected['task']);
	echo "<br><br>".__('Selected Tasks').": ".$taskcount."<br><br>".$nl;
	echo "<input type='submit' class='pt-submit' onclick='pt_getdatastates();' value='".__('Load Selected Tasks')."'>".$nl;

	// close colony / project / task selection form
	echo "</form>".$nl;

	// Messages Tab Content
	// --------------------
	if (isset($messagetab)) {
		echo "<form id='pt-selection-form' target='pt-messages-frame' action='".$ajaxurl."' method='post'>".$nl;
			echo $messagetab;
		echo "</form>".$nl;
	}

	// Main Task Interface Table
	// -------------------------
	echo "<div id='pt-task-interface'>";
	// note: only load here from saved selection
	// (otherwise loaded dynamically via form)
	if (count($selected['task']) > 0) {
		$table = pt_tasks_interface($selected['task']);
		echo $table['html'];
	}
	echo "</div>".$nl;

	// close main interface wrapper
	echo "</div>".$nl;

	// output response iframes
	$iframes = array('selection', 'filters', 'create-select', 'update-select');
	foreach ($iframes as $iframe) {pt_response_iframe($iframe);}

	// TODO: bottom / footer menu ?
	// pt_footer_menu();

	// TODO: maybe trigger settings tab opening ?
	// if ($settings) {}

}

// ----------------
// Domain List Tabs
// ----------------
function pt_domain_list_tab($domain, $data) {

	$nl = PHP_EOL; $debug = true;

	global $prototasq;
	if (!isset($prototasq['labels'])) {$prototasq['labels'] = pt_get_labels();}
	$selected = $prototasq['selected'];
	$labels = $prototasq['labels'];
	$tbwidth = $prototasq['tbwidth'];
	$tbheight = $prototasq['tbheight'];

	// get current user record roles
	$userid = pt_get_current_user_id();
	if (!isset($prototasq['userroleids'])) {
		$prototasq['userroleids'] = pt_get_user_role_ids($userid);
	}
	$userroleids = $prototasq['userroleids'];

	// select heading
	$list['html'] = "<span class='pt-selection-wrapper'>";
	$list['html'] .= "<h3 id='pt-header-".$domain."' class='pt-selection-header'>";
	$list['html'] .= $labels['select-'.$domain]."</h3>";

	if ($domain == 'colony') {

		// Colony Selection List
		// ---------------------
		$list['html'] .= "<div id='pt-checkboxes-colony' class='pt-checkboxes-list'>".$nl;
		$colonies = pt_get_records('colony');
		// if ($debug) {$list['html'] .= "Colonies: ".print_r($colonies, true)."<br>";}
		if (count($colonies) > 0) {
			foreach ($colonies as $i => $colony) {
				$remove = false;
				// check if private colony and if not the colony creator
				if ( ($colony['privacy'] == 'private') && ($colony['creator'] != $userid) ) {
					// remove colony from list if user is not assigned a role in it
					if (!in_array($colony['id'], $userroleids)) {$remove = true;}
				}
				// TODO: check for group privacy and if not in group
				// if ($colony['privacy'] == 'group') {}

				if ($remove) {unset($colonies[$i]);}
			}
			// if ($debug) {$list['html'] .= "Permission Filtered Colonies: ".print_r($colonies, true)."<br>";}

			// output checkbox list
			$checkboxes = pt_checkbox_list('colony', $colonies, $selected);
			$list['html'] .= $checkboxes['html'];
		} else {
			$list['html'] = "<ul id='pt-list-".$domain."' class='pt-list'></ul>";
			$list['html'] .= $labels['no-'.$domain];
		}
		$list['html'] .= "</div>";
		$buttons = pt_list_buttons('colony', $selected);
		$list['html'] .= $buttons['html']."<br>".$nl;
		$list['html'] .= "</span>";

		// Colony-Project Filters
		// ----------------------
		$list['html'] .= "<div id='pt-filters-colony' class='pt-filters-list'>".$nl;

			// Project Types Taxonomy Filter
			// TODO: ...

		$list['html'] .= "</div>";

	} elseif ($domain == 'project') {

		// Project Selection List
		// ----------------------
		$list['html'] .= "<div id='pt-checkboxes-project' class='pt-checkboxes-list'>".$nl;
		$projects = pt_get_records('project', array(), $data['colony']);
		// if ($debug) {$list['html'] .= "Projects: ".print_r($projects, true)."<br>";}
		if (count($projects) > 0) {
			foreach ($projects as $i => $project) {
				$remove = false;
				// check if private project and if not the project creator
				if ( ($project['privacy'] == 'private') && ($project['creator'] != $userid) ) {
					// remove project from list if user is not assigned a role in it
					if (!in_array($project['id'], $userroleids)) {$remove = true;}
				}
				// TODO: check for group privacy and if not in group
				// if ($project['privacy'] == 'group') {}

				if ($remove) {unset($projects[$i]);}
			}
			// if ($debug) {$list['html'] .= "Permission Filtered Projects: ".print_r($projects, true)."<br>";}

			// output checkbox list and buttons
			$checkboxes = pt_checkbox_list('project', $projects, $selected);
			$list['html'] .= $checkboxes['html'];
		} else {
			$list['html'] = "<ul id='pt-list-".$domain."' class='pt-list'></ul>";
			$list['html'] .= $labels['no-'.$domain];
		}
		$list['html'] .= "</div>";
		$buttons = pt_list_buttons('project', $selected);
		$list['html'] .= $buttons['html']."<br>".$nl;
		$list['html'] .= "</span>";

		// Project-Task Filters
		// --------------------
		// $list['html'] .= "<div id='pt-filters-project pt-filters-list'>".$nl;

			// - Sector / Section / Context
			// - Division / Department / Team
			// - Stage / Phase / Milestone
			$filters = array('sector', 'division', 'stage');
			foreach ($filters as $filter) {
				if ($filter == 'sector') {$options = pt_get_sectors();}
				elseif ($filter == 'division') {$options = pt_get_divisions();}
				elseif ($filter == 'stage') {$options = pt_get_stages();}

				$label = $labels['filter-'.$filter];
				$list['html'] .= "<span id='pt-filter-wrapper-".$filter."' class='pt-filter-wrapper'>";
				$list['html'] .= "<h4 id='pt-subheader-".$filter."' class='pt-filter-header'>".$label."</h4>";
				$checkbox = pt_checkbox_list($filter, $options, $selected);
				$list['html'] .= $checkbox['html'];
				$list['html'] .= "</span>";
			}
		// $list['html'] .= "</div>";

	} elseif ($domain == 'task') {

		// Task Selection List
		// -------------------
		$list['html'] .= "<div id='pt-checkboxes-task' class='pt-checkboxes-list'>".$nl;
		$tasks = pt_get_records('task', array(), $data['project']);
		// if ($debug) {$list['html'] .= "Tasks: ".print_r($tasks, true)."<br>";}
		if (count($tasks) > 0) {
			foreach ($tasks as $i => $task) {
				$remove = false;
				// check if private task and if not the task creator
				if ( ($task['privacy'] == 'private') && ($task['creator'] != $userid) ) {
					// remove task from list if user is not assigned a role in it
					if (!in_array($task['id'], $userroleids)) {$remove = true;}
				}
				// TODO: check for group privacy and if not in group
				// if ($task['privacy'] == 'group') {}

				if ($remove) {unset($tasks[$i]);}
			}
			// if ($debug) {$list['html'] .= "Permission Filtered Tasks: ".print_r($tasks, true)."<br>";}

			// output checkbox list and buttons
			$checkboxes = pt_checkbox_list('task', $tasks, $selected);
			$list['html'] .= $checkboxes['html'];
		} else {
			$list['html'] = "<ul id='pt-list-".$domain."' class='pt-list'></ul>";
			$list['html'] .= $labels['no-'.$domain];
		}
		$list['html'] .= "</div>";
		$buttons = pt_list_buttons('task', $selected);
		$list['html'] .= $buttons['html']."<br>".$nl;
		$list['html'] .= "</span>";

		// Task Filters
		// ------------
		// $filters = pt_get_task_filters();
		// foreach ($filters as $position => $filter) {
		//
		// }
		// $list['html'] .= "<div id='pt-filters-task' class='pt-filters-list'>".$nl;

		// $list['html'] .= "</div>";

	} elseif ($domain == 'subtask') {

		// TODO: ???

	}

	return $list['html'];
}

// -------------
// Checkbox List
// -------------
function pt_checkbox_list($type, $records, $selected) {

	$nl = PHP_EOL; $debug = true;
	// if ($debug) {print_r($records);}

	global $prototasq;
	if (!isset($prototasq['labels'])) {$prototasq['labels'] = pt_get_labels();}
	$labels = $prototasq['labels'];

	// start list
	$listid = 'pt-list-'.$type; $listclass = 'pt-list';
	$list['html'] = "<ul id='".$listid."' class='".$listclass."'>";
	$list['js'] = "list = document.createElement('ul'); ".$nl;
	$list['js'] .= "list.setAttribute('id', '".$listid."'); ".$nl;
	$list['js'] .= "list.setAttribute('class', '".$listclass."'); ".$nl;

	// select all checkbox
	$itemclass = 'pt-list-item';
	$linkdivclass = 'pt-list-item-link-div';
	$linkclass = 'pt-list-item-link pt-list-item-link-all';
	$linkanchor = $linktitle = $labels['all-'.$type]; // $linkanchor = __('ALL');
	$checkboxid = 'pt-checkbox-'.$type.'-all';
	$onclick = 'checkboxcheck("'.$checkboxid.'");';

	// create checkbox
	$checkboxid = 'pt-checkbox-'.$type.'-all';
	$checked = false;
	// TODO: debug selected array
	if (isset($selected[$type]['all']) && ($selected[$type]['all'] == 'yes')) {$checked = true;}
	$checkbox = pt_create_checkbox('all', $type, $checked);

	// HTML
	// ----
	$list['html'] .= "<li class='".$itemclass."'>".$nl;
	$list['html'] .= $checkbox['html'];
	$list['html'] .= "<div class='".$linkdivclass."'>";
	$list['html'] .= "<a class='".$linkclass."' title='".$linktitle."' onclick='".$onclick."'>".$linkanchor."</a>".$nl;
	$list['html'] .= "</div></li>".$nl;

	// JS
	// --
	$list['js'] .= "item = document.createElement('li'); ".$nl;
	$list['js'] .= "item.setAttribute('class', '".$itemclass."'); ".$nl;
	$list['js'] .= $checkbox['js'];
	$list['js'] .= "item.appendChild(checkbox); ".$nl;
	$list['js'] .= "linkdiv = document.createElement('div'); ".$nl;
	$list['js'] .= "linkdiv.setAttribute('class', '".$linkdivclass."'); ".$nl;
	$list['js'] .= "link = document.createElement('a'); ".$nl;
	$list['js'] .= "link.setAttribute('class', '".$linkclass."'); ".$nl;
	$list['js'] .= "link.setAttribute('title', '".$linktitle."'); ".$nl;
	$list['js'] .= "link.setAttribute('onclick', '".$onclick."'); ".$nl;
	$list['js'] .= "link.innerHTML = '".$linkanchor."'; ".$nl;
	$list['js'] .= "linkdiv.appendChild(link); ".$nl;
	$list['js'] .= "item.appendChild(linkdiv); ".$nl;
	$list['js'] .= "list.appendChild(item); ".$nl;

	// loop record checkboxes
	// TODO: sort records? (maybe already sorted by this point?)
	$domains = array('colony', 'project', 'task');
	if (in_array($type, $domains)) {
		foreach ($records as $record) {
			// $record = pt_get_record($record);
			$listitem = pt_checkbox_list_item($record, $type, $selected);
			$list['html'] .= $listitem['html'];
			$list['js'] .= $listitem['js'];
			$list['js'] .= "list.appendChild(item); ".$nl;
		}
	}

	// loop filter checkboxes
	$filters = pt_get_task_filters();
	if (array_key_exists($type, $filters)) {
		foreach ($filters[$type] as $slug => $filter) {
			$listitem = pt_checkbox_list_filter_item($slug, $filter, $type, $selected);
			$list['html'] .= $listitem['html'];
			$list['js'] .= $listitem['js'];
			$list['js'] .= "list.appendChild(item); ".$nl;
		}
	}

	// end list
	$list['html'] .= "</ul>";

	return $list;

}

// ------------------
// Checkbox List Item
// ------------------
function pt_checkbox_list_item($record, $type, $selected) {

	global $prototasq; $nl = PHP_EOL;
	$labels = $prototasq['labels'];
	$tbwidth = $prototasq['tbwidth'];
	$tbheight = $prototasq['tbheight'];

	$nl = PHP_EOL;
	$itemclass = 'pt-list-item';
	$itemid = 'pt-list-item-'.$record['id'];
	$linkdivclass = 'pt-list-item-link-div';
	$linkdivtitle = $record['description'];
	$linkname = $labels['edit-'.$type];
	$linktitle = $record['description'];
	$linkanchor = $record['title'];
	$linkclass = 'thickbox pt-list-item-link';

	// thickbox edit link
	$linkurl = $prototasq['ajaxurl'].'?action=pt_update_record_form&type='.$type.'&id='.$record['id'];
	$linkurl .= '&width='.$tbwidth.'&height='.$tbheight;
	// extra column classes
	$statusclass = 'pt-list-item-status';
	$parentclass = 'pt-list-item-parent';
	$grandparentclass = 'pt-list-item-grandparent';

	// create checkbox
	$checked = false;
	if ( (is_array($selected[$type])) && (in_array($record['id'], $selected[$type])) ) {$checked = true;}
	$checkbox = pt_create_checkbox($record['id'], $type, $checked);

	// display colony or project status
	if ( ($type == 'colony') || ($type == 'project') ) {
		$status = $record['status'];
		if ($type == 'colony') {$statuses = pt_get_colony_statuses();}
		elseif ($type == 'project') {$statuses = pt_get_project_statuses();}
		$statustitle = $statuses[$status];
	}

	// maybe get record parent and grandparent
	if ( ($type == 'project') || ($type == 'task') ) {
		if ($record['parent'] != '0') {
			$parent = pt_get_record($record['parent']);
			$parenttitle = $parent['title'];
			$parenttitle = str_replace("'", "", $parenttitle);
			if (strlen($parenttitle > 20)) {$ptitle = substr($parenttitle, 0, 17)."...";}
		}
	}
	if ($type == 'task') {
		$grandparent = pt_get_record($parent['parent']);
		if ($parent['parent'] != '0') {
			$gptitle = $grandparent['title'];
			$gptitle = str_replace("'", "", $gptitle);
			if (strlen($gptitle > 20)) {$gptitle = substr($gptitle, 0, 17)."...";}
		}
	}

	// HTML
	// ----
	$item['html'] = "<li id='".$itemid."' class='".$itemclass."'>".$nl;
	$item['html'] .= $checkbox['html'];
	$item['html'] .= "<div class='".$linkdivclass."'  title='".$linkdivtitle."'>";
	$item['html'] .= "<a name='".$linkname."' class='".$linkclass."' href='".$linkurl."'>".$linkanchor."</a></div>".$nl;

	// status column
	if (isset($status)) {$item['html'] .= "<div class='".$statusclass."' title='".$statustitle."'>".$statustitle."</div>";}
	// parent column
	if (isset($parenttitle)) {$item['html'] .= "<div class='".$parentclass."' title='".$parent['title']."'>".$parenttitle."</div>";}
	// grandparent column
	if (isset($gptitle)) {$item['html'] .= "<div class='".$grandparentclass."' title='".$grandparent['title']."'>".$gptitle."</div>";}
	$item['html'] .= "</li>".$nl;

	// JS
	// --
	$item['js'] = "item = document.createElement('li'); ".$nl;
	$item['js'] .= "item.setAttribute('id', '".$itemid."'); ".$nl;
	$item['js'] .= "item.setAttribute('class', '".$itemclass."'); ".$nl;
	$item['js'] .= $checkbox['js'];
	$item['js'] .= "item.appendChild(checkbox); ".$nl;
	$item['js'] .= "linkdiv = document.createElement('div'); ".$nl;
	$item['js'] .= "linkdiv.setAttribute('class', '".$linkdivclass."'); ".$nl;
	$item['js'] .= "item.appendChild(linkdiv); ".$nl;
	$item['js'] .= "link = document.createElement('a'); ".$nl;
	$item['js'] .= "link.setAttribute('name', '".$linkname."'); ".$nl;
	$item['js'] .= "link.setAttribute('class', '".$linkclass."'); ".$nl;
	$item['js'] .= "link.setAttribute('title', '".$linktitle."'); ".$nl;
	$item['js'] .= "link.setAttribute('href', '".$linkurl."'); ".$nl;
	$item['js'] .= "link.innerHTML = '".$linkanchor."'; ".$nl;
	$item['js'] .= "linkdiv.appendChild(link); ".$nl;
	$item['js'] .= "item.appendChild(linkdiv); ".$nl;

	// status column
	if (isset($status)) {
		$item['js'] .= "recordstatus = document.createElement('div'); ".$nl;
		$item['js'] .= "recordstatus.setAttribute('class', '".$statusclass."'); ".$nl;
		$item['js'] .= "recordstatus.setAttribute('title', '".$statustitle."'); ".$nl;
		$item['js'] .= "recordstatus.innerHTML = '".$statustitle."'; ".$nl;
		$item['js'] .= "item.appendChild(recordstatus); ".$nl;
	}
	// parent column
	if (isset($parenttitle)) {
		$item['js'] .= "parentrecord = document.createElement('div'); ".$nl;
		$item['js'] .= "parentrecord.setAttribute('class', '".$parentclass."'); ".$nl;
		$item['js'] .= "parentrecord.setAttribute('title', '".$parent['title']."'); ".$nl;
		// TODO: weird error caused by next line ?
		$item['js'] .= "parentrecord.innerHTML = '".$parenttitle."'; ".$nl;
		$item['js'] .= "item.appendChild(parentrecord); ".$nl;
	}
	// grandparent column
	if (isset($gptitle)) {
		$item['js'] .= "grandparent = document.createElement('div'); ".$nl;
		$item['js'] .= "grandparent.setAttribute('title', '".$grandparent['title']."'); ".$nl;
		$item['js'] .= "grandparent.setAttribute('class', '".$grandparentclass."'); ".$nl;
		$item['js'] .= "grandparent.innerHTML = '".$gptitle."'; ".$nl;
		$item['js'] .= "item.appendChild(grandparent); ".$nl;
	}

	return $item;
}

// -------------------------
// Checkbox List Filter Item
// -------------------------
function pt_checkbox_list_filter_item($slug, $options, $type, $selected) {

	// print_r($options);
	// return array('html' => '', 'js' => '');

	global $prototasq; $nl = PHP_EOL;
	$labels = $prototasq['labels'];
	$tbwidth = $prototasq['tbwidth'];
	$tbheight = $prototasq['tbheight'];

	$nl = PHP_EOL;
	$itemclass = 'pt-list-item';
	$itemid = 'pt-list-item-'.$type;
	$linkdivclass = 'pt-list-item-link-div';
	$linkdivtitle = '';
	// $linkname = $labels['edit-'.$type];
	// $linktitle = $options['label'];
	$linktitle = $linkanchor = $options['label'];
	$linkclass = 'pt-list-item-link'; // thickbox

	// TODO: thickbox edit link ?
	// $linkurl = $prototasq['ajaxurl'].'?action=pt_update_record_form&type='.$type.'&id='.$record['id'];
	// $linkurl .= '&width='.$tbwidth.'&height='.$tbheight;
	$linkurl = 'javascript:void(0);'; // TEMP

	// create checkbox
	$checked = false;
	if ( (is_array($selected[$type])) && (in_array($slug, $selected[$type])) ) {$checked = true;}
	$checkbox = pt_create_checkbox($slug, $type, $checked);

	// HTML
	// ----
	$item['html'] = "<li id='".$itemid."' class='".$itemclass."'>".$nl;
	$item['html'] .= $checkbox['html'];
	$item['html'] .= "<div class='".$linkdivclass."' title='".$linkdivtitle."'>";
	$item['html'] .= "<a class='".$linkclass."' href='".$linkurl."'>".$linkanchor."</a></div>".$nl;

	// JS
	// --
	$item['js'] = "item = document.createElement('li'); ".$nl;
	$item['js'] .= "item.setAttribute('id', '".$itemid."'); ".$nl;
	$item['js'] .= "item.setAttribute('class', '".$itemclass."'); ".$nl;
	$item['js'] .= $checkbox['js'];
	$item['js'] .= "item.appendChild(checkbox); ".$nl;
	$item['js'] .= "linkdiv = document.createElement('div'); ".$nl;
	$item['js'] .= "linkdiv.setAttribute('class', '".$linkdivclass."'); ".$nl;
	$item['js'] .= "item.appendChild(linkdiv); ".$nl;
	$item['js'] .= "link = document.createElement('a'); ".$nl;
	// $item['js'] .= "link.setAttribute('name', '".$linkname."'); ".$nl;
	$item['js'] .= "link.setAttribute('class', '".$linkclass."'); ".$nl;
	$item['js'] .= "link.setAttribute('title', '".$linktitle."'); ".$nl;
	$item['js'] .= "link.setAttribute('href', '".$linkurl."'); ".$nl;
	$item['js'] .= "link.innerHTML = '".$linkanchor."'; ".$nl;
	$item['js'] .= "linkdiv.appendChild(link); ".$nl;
	$item['js'] .= "item.appendChild(linkdiv); ".$nl;

	return $item;
}

// ---------------
// Create Checkbox
// ---------------
function pt_create_checkbox($id, $type, $checked) {

	$nl = PHP_EOL;
	$checkboxclass = 'pt-checkbox pt-checkbox-'.$type;
	$checkboxname = $checkboxid = 'pt-checkbox-'.$type.'-'.$id;
	$checkbox['html'] = "<input type='checkbox' name='".$checkboxname."' id='".$checkboxid."' class='".$checkboxclass."' value='yes'";
		if ($checked) {$checkbox['html'] .= " checked";}
	$checkbox['html'] .= "> ".$nl;

	$checkbox['js'] = "checkbox = document.createElement('input'); ".$nl;
	$checkbox['js'] .= "checkbox.setAttribute('type', 'checkbox'); ".$nl;
	$checkbox['js'] .= "checkbox.setAttribute('name', '".$checkboxname."'); ".$nl;
	$checkbox['js'] .= "checkbox.setAttribute('id', '".$checkboxid."'); ".$nl;
	$checkbox['js'] .= "checkbox.setAttribute('class', '".$checkboxclass."'); ".$nl;
	$checkbox['js'] .= "checkbox.setAttribute('value', 'yes'); ".$nl;
	if ($checked) {$checkbox['js'] .= "checkbox.checked = 1; ".$nl;}
	return $checkbox;
}

// -------------------
// Domain List Buttons
// -------------------
function pt_list_buttons($type, $selected) {

	global $prototasq; $nl = PHP_EOL;
	$labels = $prototasq['labels']; $ajaxurl = $prototasq['ajaxurl'];
	$tbwidth = $prototasq['tbwidth']; $tbheight = $prototasq['tbheight'];

	// hidden input for selection restore
	$selectedlist = '';
	if (isset($selected[$type]) && is_array($selected[$type])) {
		$selectedlist = implode(',', $selected[$type]);
	}
	$buttons['html'] = "<input type='hidden' id='pt-selection-".$type."' value='".$selectedlist."'>";

	// checkbox buttons table
	$buttons['html'] .= "<table cellpadding='0' cellspacing='0' class='pt-update-buttons'><tr>";
	$buttons['html'] .= "<td class='pt-button-cell'>".$nl;
		// clear selection
		$onclick = 'pt_clearselection("'.$type.'");';
		$title = __('Clear Selection');
		$buttons['html'] .= "<div class='pt-inline pt-button-icon pt-cross-button' onclick='".$onclick."' title='".$title."'></div><br>".$nl;
		$buttons['html'] .= "<div class='pt-inline pt-button-label' onclick='".$onclick."' title='".$title."'>".__('Clear')."</div>".$nl;
	$buttons['html'] .= "<td class='pt-button-cell'>".$nl;
		// restore selection
		$onclick = 'pt_restoreselection("'.$type.'");';
		$title = __('Restore Existing Selection');
		$buttons['html'] .= "<div class='pt-inline pt-button-icon pt-minus-button' onclick='".$onclick."' title='".$title."'></div><br>".$nl;
		$buttons['html'] .= "<div class='pt-inline pt-button-label pt-button-link' onclick='".$onclick."' title='".$title."'>".__('Restore')."</div>".$nl;
	$buttons['html'] .= "<td class='pt-button-cell'>".$nl;
		// add new record
		$addurl = $ajaxurl.'?action=pt_create_record_form&type='.$type.'&width='.$tbwidth.'&height='.$tbheight;
		$addlabel = $labels['add-new-'.$type];
		$buttons['html'] .= "<a name='".$addlabel."' class='thickbox' href='".$addurl."'>".$nl;
		$buttons['html'] .= "<div class='pt-inline pt-button-icon pt-add-button' title='".$addlabel."'></div></a><br>".$nl;
		$buttons['html'] .= "<a name='".$addlabel."' class='thickbox' href='".$addurl."'>".$nl;
		$buttons['html'] .= "<div class='pt-inline pt-button-label pt-button-link'>".__('Add')."</div></a>".$nl;
	$buttons['html'] .= "<td class='pt-button-cell'>".$nl;
		$onclick = 'pt_updateselection("'.$type.'");';
		$title = __('Update Selection');
		$buttons['html'] .= "<div class='pt-inline pt-button-icon pt-tick-button' onclick='".$onclick."' title='".$title."'></div><br>".$nl;
		$buttons['html'] .= "<div class='pt-inline pt-button-label pt-button-link' onclick='".$onclick."' title='".$title."'>".__('Load')."</div>";
	$buttons['html'] .= "</td></tr></table>".$nl;
	return $buttons;
}

// -----------------
// Add Domain Button
// -----------------
function pt_add_domain_button($domain) {
	global $prototasq; $nl = PHP_EOL;
	$labels = $prototasq['labels']; $ajaxurl = $prototasq['ajaxurl'];
	$tbwidth = $prototasq['tbwidth']; $tbheight = $prototasq['tbheight'];
	$addurl = $ajaxurl.'?action=pt_create_record_form&type='.$domain.'&width='.$tbwidth.'&height='.$tbheight;
	$addlabel = $labels['add-new-'.$domain];
	$add['html'] = "<div class='pt-add-wrap'>".$nl;
		$add['html'] .= "<a name='".$addlabel."' class='thickbox pt-add-link' href='".$addurl."'>".$nl;
		$add['html'] .= "<div class='pt-inline pt-add-label'>".$addlabel."</div></a>".$nl;
		$add['html'] .= "<a name='".$addlabel."' class='thickbox pt-add-link' href='".$addurl."'>".$nl;
		$add['html'] .= "<div class='pt-inline pt-add-button pt-button-icon' title='".$addlabel."'></div></a>".$nl;
	$add['html'] .= "</div>".$nl;
	return $add;
}

// ----------------
// Message List Tab
// ----------------
function pt_messages_list_tab() {
	$debug = true;

	$html = "<h3 id='pt-selection-header-message' class='pt-selection-header'>".__('Messages')."</h3>";

	// TODO: list messages
	$userid = pt_get_current_user_id();
	$messages = pt_get_messages($userid);
	// if ($debug) {$html .= "Messages: ".print_r($messages, true)."<br>";}

	return $html;
}

// --------------------------
// Get Message Count for User
// --------------------------
function pt_get_message_count($userid) {
	// TODO: get message count
	return 0; // TEMP
}

// --------------------
// Task Header Row Menu
// --------------------
function pt_task_header_row() {

	$nl = PHP_EOL;
	$columns = pt_get_task_columns();

	$table = $row = array('html' => '', 'js' => '');
	$rowid = 'pt-header-row'; $rowclass = 'pt-row';
	$row['html'] = "<tr id='".$rowid."' class='".$rowclass."'>".$nl;
	$row['js'] = "row = document.createElement('tr'); ".$nl;
	$row['js'] .= "row.setAttribute('id', '".$rowid."'); ".$nl;
	$row['js'] .= "row.setAttribute('class', '".$rowclass."'); ".$nl;

	$i = 1; $columncount = count($columns);
	foreach ($columns as $slug => $column) {

		// open column header cell
		$cell = array('html' => '', 'js' => '');
		$cellid = 'pt-column-'.$slug;
		$cellclass = 'pt-column';
		$cell['html'] .= "<td id='".$cellid."' class='".$cellclass."'>".$nl;
		$cell['js'] = "cell = document.createElement('tr'); ".$nl;
		$cell['js'] .= "cell.setAttribute('id', '".$cellid."'); ".$nl;
		$cell['js'] .= "cell.setAttribute('class', '".$cellclass."'); ".$nl;

		// open column menu table row
		$menuid = 'pt-column-menu-'.$slug; $menuclass = 'pt-column-menu';
		$rowclass = 'pt-column-menu-row'; $cellclass = 'pt-column-menu-cell';
		$cell['html'] .= "<table id='".$menuid."' class='".$menuclass."'>".$nl;
		$cell['html'] .= "<tr class='".$rowclass."'>".$nl;
		$cell['js'] .= "menu = document.createElement('table'); ".$nl;
		$cell['js'] .= "menu.setAttribute('id', '".$menuid."'); ".$nl;
		$cell['js'] .= "menu.setAttribute('class', '".$menuclass."'); ".$nl;
		$cell['js'] .= "menu.setAttribute('width', '100%'); ".$nl;
		$cell['js'] .= "menurow = document.createElement('tr'); ".$nl;
		$cell['js'] .= "menurow.setAttribute('class', '".$rowclass."'); ".$nl;
		$cell['js'] .= "menu.appendChild(menurow); ".$nl;

		// Column Shift Left Button
		// ------------------------
		$arrowcell = pt_arrow_cell($slug, $column, 'left', $i);
		$cell['html'] .= $arrowcell['html']; $cell['js'] .= $arrowcell['js'];

		// Toggle Column Display Menu
		// --------------------------
		$displaycell = pt_display_cell($slug, $column);
		$cell['html'] .= $displaycell['html']; $cell['js'] .= $displaycell['js'];

		// Column Shift Right Button
		// -------------------------
		$arrowcell = pt_arrow_cell($slug, $column, 'right', $i, $columncount);
		$cell['html'] .= $arrowcell['html']; $cell['js'] .= $arrowcell['js'];

		// close column menu table
		$cell['html'] .= "</td></tr></table>".$nl;
		$cell['js'] .= "cell.appendChild(menu); ";

		// header column label
		$labelclass = 'pt-header-label';
		$displayinputid = 'pt-column-menu-display-'.$slug;
		$onclick = 'radiocheck("'.$displayinputid.'");';
		$cell['html'] .= "<div class='".$labelclass."'>".$column['label']."</div>".$nl;
		// $cell['js'] .= "br = document.createElement('br'); cell.appendChild(br); ".$nl;
		$cell['js'] .= "label = document.createElement('div'); ".$nl;
		$cell['js'] .= "label.setAttribute('class', '".$labelclass."'); ".$nl;
		$cell['js'] .= "label.setAttribute('onclick', '".$onclick."'); ".$nl;
		$cell['js'] .= "label.innerHTML = '".$column['label']."'; ".$nl;
		$cell['js'] .= "cell.appendChild(label); ".$nl;

		// append cell to row
		$row['html'] .= $cell['html'];
		$row['js'] .= $cell['js'];

		$i++; // increment column count
	}

	// append row to table
	$table['html'] .= $row['html']."</tr>".$nl;
	$table['js'] .= $row['js'];
	$table['js'] .= "table.appendChild(row); ".$nl;

	return $table;
}

// -------------------
// Column Display Cell
// -------------------
function pt_display_cell($slug, $column) {

	$nl = PHP_EOL;
	$cellclass = 'pt-column-menu-cell';
	$inputclass = 'pt-display-checkbox';
	$inputid = 'pt-display-checkbox-'.$slug;
	$inputname = 'pt-display-'.$slug;
	$onchange = 'togglecolumndisplay("'.$slug.'");';

	// HTML
	$cell['html'] = "<td class='".$cellclass."'>".$nl;
	$cell['html'] .= "	<input type='checkbox' class='".$inputclass."' name='".$inputname."' id='".$inputid."' value='yes' onchange='".$onchange."'";
	if ($column['show'] == 'yes') {$cell['html'] .= " checked";}
	$cell['html'] .= ">".$nl."</td>".$nl;

	// JS
	$cell['js'] = "menucell = document.createElement('td'); ".$nl;
	$cell['js'] .= "menucell.setAttribute('class', '".$cellclass."'); ".$nl;
	$cell['js'] .= "input = document.createElement('input'); ".$nl;
	$cell['js'] .= "input.setAttribute('type', 'checkbox'); ".$nl;
	$cell['js'] .= "input.setAttribute('class', '".$inputclass."'); ".$nl;
	$cell['js'] .= "input.setAttribute('name', '".$inputname."'); ".$nl;
	$cell['js'] .= "input.setAttribute('id', '".$inputid."'); ".$nl;
	$cell['js'] .= "input.setAttribute('value', 'yes'); ".$nl;
	$cell['js'] .= "input.setAttribute('onchange', '".$onchange."'); ".$nl;
	$cell['js'] .= "menucell.appendChild(input); ".$nl;
	$cell['js'] .= "menu.appendChild(menucell); ".$nl;

	return $cell;
}


// ----------------------
// Order Shift Arrow Cell
// ----------------------
function pt_arrow_cell($slug, $column, $leftright, $i, $columncount=false) {

	$nl = PHP_EOL;
	$cellclass = 'pt-column-menu-cell';
	$cellid = 'pt-arrow-cell-'.$slug.'-'.$leftright;
	if ($column['show'] != 'yes') {$cellclass .= 'pt-column-hidden-cell';}
	$noarrowclass = 'pt-no-arrow';
	$arrowclass = 'pt-arrow pt-'.$leftright.'-arrow';
	$onclick = 'shifttaskorder("'.$leftright.'", "'.$i.'");';

	// HTML
	// ----
	$cell['html'] = "<td id='".$cellid."' class='".$cellclass."'>";
	// $cell['html'] = $i.' - '.$columncount;
	if ( ( ($leftright == 'left') && ($i == 1) )
	  || ( ($leftright == 'right') && ($i == $columncount) ) ) {
		$cell['html'] .= "	<div class='".$noarrowclass."'></div>".$nl;
	} else {$cell['html'] .= "	<div class='".$arrowclass."' onclick='".$onclick."'></div>".$nl;}
	$cell['html'] .= "</td>".$nl;

	// JS
	// --
	$cell['js'] = "menucell = document.createElement('td'); ".$nl;
	$cell['js'] .= "menucell.setAttribute('id', '".$cellid."'); ".$nl;
	$cell['js'] .= "menucell.setAttribute('class', '".$cellclass."'); ".$nl;
	$cell['js'] .= "arrow = document.createElement('div'); ".$nl;
	if ( ( ($leftright == 'left') && ($i == 1) )
	  || ( ($leftright == 'right') && ($i == $columncount) ) ) {
	  	$cell['js'] .= "arrow.setAttribute('class', '".$noarrowclass."'); ".$nl;
	} else {
		$cell['js'] .= "arrow.setAttribute('class', '".$arrowclass."'); ".$nl;
		$cell['js'] .= "arrow.setAttribute('onclick', '".$onclick."'); ".$nl;
	}
	$cell['js'] .= "menucell.appendChild(arrow); ".$nl;
	$cell['js'] .= "menu.appendChild(menucell); ".$nl;

	return $cell;

}


// ---------------
// Tasks Interface
// ---------------
function pt_tasks_interface($taskids, $data=null) {

	$nl = PHP_EOL; $debug = true;

	// maybe get task data
	if ( (is_null($data)) || (!isset($data['tasks'])) ) {
		$tasks = pt_get_records('task', $taskids);
	} else {$tasks = $data['tasks'];}
	// if ($debug) {print_r($data);}

	$sectors = pt_get_sectors();
	$divisions = pt_get_divisions();
	$stages = pt_get_stages();
	$statuses = pt_get_task_statuses();
	$priorities = pt_get_task_priorities();
	// if ($debug) {print_r($sectors); print_r($divisions); print_r($stages);}

	if (count($tasks) > 0) {

		$tableid = 'pt-task-table';
		$table['html'] = "<table id='".$tableid."' cellspacing='1'>";
		$table['js'] = "table = document.createElement('table'); ".$nl;
		$table['js'] .= "table.setAttribute('id', '".$tableid."'); ".$nl;
		$table['js'] .= "table.setAttribute('cellspacing', '1'); ".$nl;

		// Column Order / Display Selection
		// --------------------------------
		$headerrow = pt_task_header_row();
		$table['html'] .= $headerrow['html'];
		// $table['js'] .= $headerrow['js'];

		// Loop Tasks
		// ----------
		$oddeven = 'even'; $rowcount = 1;
		foreach ($tasks as $task) {

			// switch odd even display colour
			if ($oddeven == 'even') {$oddeven = 'odd';} else {$oddeven = 'even';}

			// get task metadata
			$taskid = $task['id'];
			$taskdata = pt_get_task_data($taskid);
			if ($taskdata && is_array($taskdata)) {$task = array_merge($task, $taskdata);}
			// if ($debug) {print_r($taskdata);}

			// get role data
			$roledata = pt_get_record_roles($taskid);
			if ($roledata && is_array($roledata)) {$task = array_merge($task, $roledata);}
			// if ($debug) {print_r($taskdata);}

			// Main Task Row
			// -------------
			$cells = array('html' => '', 'js' => '');
			$columns = pt_get_task_columns();
			$columncount = 1;
			foreach ($columns as $slug => $column) {

				// if ($debug) {print_r($column);}

				$type = $column['type'];
				$show = $column['show'];
				$label = $column['label'];

				// task ID cell
				if ($type == 'id') {
					// if ($debug) {echo "Task ID: ".$taskid."<br>";}
					$cell = pt_task_cell_id($taskid, $show);
				}

				// select value
				if ($type == 'select') {
					if (isset($task[$slug])) {$value = $task[$slug];} else {$value = '';}
					// if ($debug) {echo "Slug: ".$slug." - Value: ".$value."<br>";}
					if ($value != '') {
						if ($slug == 'sector') {$value = $sectors[$value]['label'];}
						elseif ($slug == 'division') {$value = $divisions[$value]['label'];}
						elseif ($slug == 'stage') {$value = $stages[$value]['label'];}
						elseif ($slug == 'status') {$value = $statuses[$value]['label'];}
						elseif ($slug == 'priority') {
							// extra step as indexed by slug by numeric is saved
							foreach ($priorities as $key => $priority) {
								if ($value == $priority['position']) {$value = $priority['label'];}
							}
						}
						// if ($debug) {echo "Label: ".$value."<br>";}
					}

					$cell = pt_task_cell_select($taskid, $slug, $value, $show);
				}

				// view value cell
				if ($type == 'view') {
					$value = '';
					if ( ($slug == 'worker') && (isset($task['worker'])) ) {$value = $task['worker'];}
					elseif ( ($slug == '') && (isset($task[''])) ) {$value = $task[''];}
					$cell = pt_task_cell_view($task, $slug, $value);
				}

				// task title cell
				if ($type == 'task') {
					$cell = pt_task_cell_link($task);
					// $middlepoint = $columncount;
				}

				// add cell to row
				$cells['html'] .= $cell['html'];
				$cells['js'] .= $cell['js'];
				$cells['js'] .= "row.appendChild(cell); ".$nl;

				$columncount++;
			}

			// create table row
			$firstlast = false;
			if ($rowcount == 1) {$firstlast = 'pt-first-row';}
			elseif ($rowcount == count($tasks)) {$firstlast = 'pt-last-row';}
			$row = pt_task_row('data', $taskid, $cells, $oddeven, $firstlast);

			// append row to table
			$table['html'] .= $row['html'];
			$table['js'] .= $row['js'].$cells['js'];
			$table['js'] .= "table.appendChild(row); ";

			// Sub Tasks Row
			// -------------
			// create second task row (expand / collapse)

			// if ($middlepoint == 1) {$subcolumns = array('subtasks', 'description', 'times');}
			// elseif ($middlepoint == ($columncount-1)) {$subcolumns = array('description', 'times', 'subtasks');}
			// else {$subcolumns = array('description', 'subtasks', 'times');} // default

			// foreach ($subcolumns as $subcolumn) {
			//	if ($subcolumn == 'description') {
			//	}
			//	// cell for subtasks interface list table
			//	if ($subcolumn == 'subtasks') {
			//		$subtaskids = pt_get_subtask_ids($taskid);
			//		$cell = pt_subtasks_interface($subtaskids, $context, $data);
			//		$cells['html'] .= $cell['html']; $cells['js'] .= $cell['js'];
			//	}

			//	if ($subcolumn == 'times') {
			//		// TODO: times column
			//	}
			// }

			// append cells to row
			// $row = pt_task_row($type, $taskid, $cells);
			// $row['js'] .= "for (i in cells) {row.appendChild(cells[i]);} ";

			// append row to table
			// $table['html'] .= $row['html'];
			// $table['js'] .= $row['js']."table.appendChild(row); ";

			$rowcount++;
		}
	} else {
		// $message = __('No Tasks Found.');
	}

	$table['html'] .= "</table>";

	return $table;
}


// ------------------
// SubTasks Interface
// ------------------
// TODO: future subtask checklist implementation
function pt_subtasks_interface($subtaskids, $data=null) {

	// maybe get subtask data
	if ( (is_null($data)) || (!isset($data['subtasks'])) ) {
		$data = pt_get_subtasks($subtaskids);
	}

	if (count($data['subtasks']) > 0) {
		$subtaskcount = 0;
		foreach ($data['subtasks'] as $id => $subtask) {
			if (in_array($id, $subtaskids)) {

				// TODO: list subtasks table
				// - up / down priority cell
				// - numered subtask cell
				// - subtask anchored edit link
				// - resources
				// - comments

			}

			if ($subtaskcount === 0) {
				// $message = __('No Matching Subtasks Found.');
			}
		}
	} else {
		// $message = __('No Subtasks Found');
	}
}

// ---------------
// Create Task Row
// ---------------
function pt_task_row($type, $taskid, $cells, $oddeven, $firstlast) {
	$nl = PHP_EOL;
	$id = "pt-task-".$type."-".$taskid;
	$class = "pt-task-".$type." ".$oddeven;
	if ($firstlast) {$class .= " ".$firstlast;}

	// HTML
	// ----
	$cell['html'] = "<tr id='".$id."' class='".$class."'>".$cells['html']."</tr>".$nl;

	// JS
	// --
	$cell['js'] = "row = document.createElement('tr'); ".$nl;
	$cell['js'] .= "row.setAttribute('id', '".$id."'); ".$nl;
	$cell['js'] .= "row.setAttribute('class', '".$class."'); ".$nl;
	return $cell;
}

// --------------
// Create ID Cell
// --------------
// Nerdy Note: It has begun! This is the First Ever ProtoTasQ function!
function pt_task_cell_id($taskid) {
	$nl = PHP_EOL;
	$cellid = "pt-task-id-".$taskid;
	$cellclass = "pt-cell pt-cell-id pt-task-id";

	// HTML
	// ----
	$cell['html'] = "<td id='".$cellid."' class='".$cellclass."'>".$taskid."</td>".$nl;

	// JS
	// --
	$cell['js'] = "cell = document.createElement('td'); ".$nl;
	$cell['js'] .= "cell.setAttribute('id', '".$cellid."'); ".$nl;
	$cell['js'] .= "cell.setAttribute('class', '".$cellclass."'); ".$nl;
	$cell['js'] .= "cell.innerHTML = '".$taskid."'; ".$nl;
	return $cell;
}

// -----------------------
// Create Task Select Cell
// -----------------------
function pt_task_cell_select($taskid, $slug, $value, $show) {
	$nl = PHP_EOL;
	$cellid = "pt-task-".$slug."-".$taskid;
	$class = "pt-cell pt-cell-".$slug." pt-task-select pt-task-select-".$slug;
	$div = pt_task_cell_create_select_div($slug, $taskid, $value);

	// HTML
	// ----
	$cell['html'] = "<td id='".$cellid."' class='".$class."'>";
	$cell['html'] .= $div['html']."</td>".$nl;

	// JS
	// --
	$cell['js'] = "cell = document.createElement('td'); ".$nl;
	$cell['js'] .= "cell.setAttribute('id', '".$cellid."'); ".$nl;
	$cell['js'] .= "cell.setAttribute('class', '".$class."'); ".$nl;
	$cell['js'] .= $div['js'];
	$cell['js'] .= "cell.appendChild(clickdiv); ".$nl;
	if ($show != 'yes') {$cell['js'] .= "cell.setAttribute('style', 'display:none;'); ".$nl;}
	return $cell;
}

// ---------------------------
// Create Task Select Cell Div
// ---------------------------
function pt_task_cell_create_select_div($slug, $taskid, $label) {
	$nl = PHP_EOL;
	$divclass = "pt-select-div-value";
	$onclick = 'createselectcell("'.$taskid.'", "'.$slug.'");';
	$div['html'] = "<div class='".$divclass."' onclick='".$onclick."'>".$label."</div>";
	$div['js'] = "clickdiv = document.createElement('div'); ".$nl;
	$div['js'] .= "clickdiv.setAttribute('class', '".$divclass."'); ".$nl;
	$div['js'] .= "clickdiv.setAttribute('onclick', '".$onclick."'); ".$nl;
	$div['js'] .= "clickdiv.innerHTML = '".$label."'; ".$nl;
	return $div;
}

// -------------------
// Task View Only Cell
// -------------------
function pt_task_cell_view($task, $slug, $value) {
	$nl = PHP_EOL;
	$cellid = "pt-task-".$slug."-".$task['id'];
	$cellclass = "pt-cell pt-task-".$slug;
	$divclass = "pt-task-".$slug."-div";

	// HTML
	// ----
	$cell['html'] = "<td id='".$cellid."' class='".$cellclass."'>";
	$cell['html'] .= "<div class='".$divclass."'>".$value."</div></td>";

	// JS
	// --
	$cell['js'] = "cell = document.createElement('td'); ".$nl;
	$cell['js'] .= "cell.setAttribute('id', '".$cellid."'); ".$nl;
	$cell['js'] .= "cell.setAttribute('class', '".$cellclass."'); ".$nl;
	$cell['js'] .= "div = document.createElement('div'); ".$nl;
	$cell['js'] .= "div.setAttribute('class', '".$divclass."'); ".$nl;
	$cell['js'] .= "div.innerHTML = '".$value."'; ".$nl;
	$cell['js'] .= "cell.appendChild(div); ";
	return $cell;
}

// ---------------------
// Create Task Link Cell
// ---------------------
function pt_task_cell_link($task) {
	$nl = PHP_EOL;
	$cellid = "pt-task-title-".$task['id'];
	$cellclass = "pt-cell pt-task-title";
	$divclass = "pt-task-title-div";
	// TODO: make this an edit link ?
	$onclick = 'toggletaskrow("'.$task['id'].'");';
	$anchor = str_replace("'", "", $task['title']);
	$divtitle = str_replace("'", "", $task['description']);

	// HTML
	// ----
	$cell['html'] = "<td id='".$cellid."' class='".$cellclass."'>";
	$cell['html'] .= "<div class='".$divclass."' onclick='".$onclick."' title='".$divtitle."'>".$anchor."</div></td>";

	// JS
	// --
	$cell['js'] = "cell = document.createElement('td'); ".$nl;
	$cell['js'] .= "cell.setAttribute('id', '".$cellid."'); ".$nl;
	$cell['js'] .= "cell.setAttribute('class', '".$cellclass."'); ".$nl;
	$cell['js'] .= "div = document.createElement('div'); ".$nl;
	$cell['js'] .= "div.setAttribute('class', '".$divclass."'); ".$nl;
	$cell['js'] .= "div.setAttribute('title', '".$divtitle."'); ".$nl;
	$cell['js'] .= "div.setAttribute('onclick', '".$onclick."'); ".$nl;
	$cell['js'] .= "div.innerHTML = '".$anchor."'; ".$nl;
	$cell['js'] .= "cell.appendChild(div); ";
	return $cell;
}

