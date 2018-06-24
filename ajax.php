<?php

// ======================
// ProtoTasq AJAX Actions
// ======================

// === Selection ===
// - Update Selection
// - Load Task Interface
// === Records ===
// - Create Record
// - Update Record
// - Add / Update Record Form
// - Create / Update Record
// - Delete Record
// * Archive Record
// * Tag Form
// * Label Editor Form
// === Messages ===
// * Message Form
// === Settings ===
// * Settings Form
// === Inputs ===
// - Create / Update Select Input


// Development TODOs
// -----------------
// * project type selection / update
// - add sector/stage/division/skill button
// - tag selection
// - record colours
// - archive record


// maybe Debug AJAX Action
$debug = false;
if ($debug) {
	if (isset($_REQUEST['action'])) {
		echo "AJAX Action: ".$_REQUEST['action']."<br>"; exit;
	}
}


// =========
// Selection
// =========

// ----------------
// Update Selection
// ----------------
add_action('wp_ajax_pt_update_selection', 'pt_update_selection');
function pt_update_selection() {

	$nl = PHP_EOL; $debug = true;
	$type = $_REQUEST['type'];
	$checked = $_REQUEST['checked'];
	$types = array('colony', 'project', 'task');
	if (!in_array($type, $types)) {$error = __('Invalid Record Type.');}
	if ($checked == '') {$error = __('Nothing Selected to Load.');}
	if (isset($error)) {echo "<script>alert('".$error."');</script>"; exit;}
	if (strstr($checked, ',')) {$checked = explode(',', $checked);}
	else {$checked[0] = $checked;}
	if ($debug) {echo "Checked: ".print_r($checked,true)."<br>";}

	// get saved selection
	$selected = pt_get_selection();
	if ($debug) {echo "Saved Selection: ".print_r($selected,true)."<br>";}

	if ($type == 'colony') {

		// revalidate colony IDs
		$checked = pt_get_record_ids($type, $checked);
		if ($debug) {echo "Validated: ".print_r($checked,true)."<br>";}

		// update project selection list
		$projects = pt_get_records('project', array(), $checked);
		if ($debug) {echo "Projects: ".print_r($checked,true)."<br>";}
		if (count($projects) > 0) {

			// check permissions to view these projects
			foreach ($projects as $i => $project) {
				$allowed = pt_get_record_permission($project['id'], 'project', 'view');
				if (!$allowed) {unset($projects[$i]);}
			}
			if ($debug) {echo "Permission Filtered Projects: ".print_r($projects,true)."<br>";}

			if (count($projects) > 0) {
				// create checkbox list
				$list = pt_checkbox_list('project', $projects, $selected);
				$js = $list['js']; $html = '';
			} else {$list['js'] = ''; $html = __('No Projects to View.');}
		} else {$list['js'] = ''; $html = __('No Projects to View.');}

		// insert list into parent frame project tab content
		$id = 'pt-checkboxes-project';
		$js .= "tab = parent.document.getElementById('".$id."'); ".$nl;
		$js .= "tab.innerHTML = '".$html."'; ".$nl;
		if ($debug) {$js .= "console.log(tab); console.log(list); ".$nl;}
		$js .= "tab.appendChild(list); ".$nl;

		// merge and update saved selection
		if ( (isset($selected['colony'])) && (count($selected['colony']) > 0)) {
			$selected['colony'] = array_merge($selected['colony'], $checked);
		} else {$selected['colony'] = $checked;}
		if (isset($_REQUEST['currenttab'])) {$selected['currenttab'] = $currenttab;}
		pt_update_selected($selected);

	} elseif ($type == 'project') {

		// revalidate project IDs
		$checked = pt_get_record_ids($type, $checked);
		if ($debug) {echo "Validated: ".print_r($checked,true)."<br>";}

		// update task selection list
		$tasks = pt_get_records('task', array(), $checked);
		if ($debug) {echo "Tasks: ".print_r($tasks,true)."<br>";}

		if (count($tasks) > 0) {

			// check permissions to view these tasks
			foreach ($tasks as $i => $task) {
				$allowed = pt_get_record_permission($task['id'], 'task', 'view');
				if (!$allowed) {unset($tasks[$i]);}
			}
			if ($debug) {echo "Permission Filtered Tasks: ".print_r($tasks,true)."<br>";}

			if (count($tasks) > 0) {
				// create checkbox list
				$list = pt_checkbox_list('task', $tasks, $selected);
				$js = $list['js'];  $html = '';
			} else {$list['js'] = ''; $html = __('No Projects to view.');}
		} else {$list['js'] = ''; $html = __('No Projects to view.');}

		// insert list into parent frame project tab content
		$id = 'pt-checkboxes-task';
		$js .= "tab = parent.document.getElementById('".$id."'); ".$nl;
		$js .= "tab.innerHTML = '".$html."'; ".$nl;
		$js .= "tab.appendChild(list); ".$nl;

		// merge and update saved selection
		if ( (isset($selected['project'])) && (count($selected['project']) > 0)) {
			$selected['project'] = array_merge($selected['project'], $checked);
		} else {$selected['project'] = $checked;}
		if (isset($_REQUEST['currenttab'])) {$selected['currenttab'] = $currenttab;}
		pt_update_selected($selected);

	} elseif ($type == 'task') {

		// load the tasks interface table
		pt_load_task_interface($checked);

	}

	echo "<script>".$js."</script>"; exit;
}

// -------------------
// Load Task Interface
// -------------------
add_action('wp_ajax_pt_load_task_interface', 'pt_load_task_interface');
function pt_load_task_interface($taskids=false) {

	$nl = PHP_EOL; $debug = true;

	if (!is_user_logged_in()) {echo __('Session timed out. Please login and try again.'); exit;}
	$userid = pt_get_current_user_id();

	// update column states
	$columns = pt_get_task_columns();
	foreach ($columns as $slug => $column) {unset($columns[$slug]['label']); unset($columns[$slug]['type']);}
	if (isset($_POST['column-display'])) {
		$columndisplay = $_POST['column-display'];
		if (strstr($display, ',')) {$display = explode(',', $columndisplay);} else {$display[0] = $columndisplay;}
		if ($debug) {echo "Posted Column Display States: ".print_r($display,true)."<br>";}
		foreach ($columns as $slug => $column) {
			if (in_array($slug, $display)) {$columns[$slug]['show'] = 'yes';}
			else {$columns[$slug]['show'] = '';}
		}
	}
	if (isset($_POST['column-order'])) {
		$order = $_POST['column-order'];
		$order = explode(',', $order);
		foreach ($order as $i => $slug) {$columns[$slug]['position'] = $i;}
	}
	// if ($debug) {print_r($columns);}
	pt_update_task_columns($columns);

	// get task ids
	if (!$taskids) {
		$taskids = $_POST['taskids'];
		if (strstr($taskids, ',')) {$taskids = explode(',', $taskids);} else {$taskids[0] = $taskids;}
	}
	if ( (count($taskids) == 0) || ($taskids[0] == '') ) {
		echo "<script>alert('".__('No Tasks have been selected.')."');</script>"; exit;
	}
	if ($debug) {echo "Task IDs: ".print_r($taskids,true)."<br>";}

	if (in_array('all', $taskids)) {

		// TODO: get all the tasks for requested project IDs instead
		// $projectids = $_POST['projectids'];

	}

	// recheck task view permissions
	foreach ($taskids as $i => $taskid) {
		$allowed = pt_get_record_permission($taskid, 'task', 'view');
		if (!$allowed) {unset($taskids[$i]);}
	}
	if (count($taskids) == 0) {
		echo "<script>alert('".__('No Tasks selected can be viewed.')."');</script>"; exit;
	}

	// save updated selection
	$selected = pt_get_selection();
	if ( (isset($selected['task'])) && (count($selected['task']) > 0)) {
		$selected['task'] = array_merge($selected['task'], $taskids);
	} else {$selected['task'] = $taskids;}
	if (isset($_REQUEST['currenttab'])) {$selected['currenttab'] = $currenttab;}
	pt_update_selected($selected);
	if ($debug) {echo "Selected: ".print_r($selected,true)."<br>";}

	// create task interface table
	$table = pt_tasks_interface($taskids);

	echo $table['html'];

	print_r($table['js']);

	// load task interface into the parent window
	echo "<script>".$nl.$table['js'].$nl;
	echo "container = parent.document.getElementById('pt-task-interface'); ".$nl;
	echo "container.innerHTML = ''; ".$nl;
	echo "container.appendChild(table); ".$nl;
	echo "</script>";

	exit;
}


// =======
// Records
// =======

// ---------------
// Create Record Form
// ---------------
add_action('wp_ajax_pt_create_record_form', 'pt_create_record_form');
function pt_create_record_form() {
	$type = $_REQUEST['type'];
	if ($type == 'message') {pt_message_form(); exit;}
	$types = array('colony', 'project', 'task', 'subtask');
	if (!in_array($type, $types)) {exit;}
	pt_record_form($type);
}

// ------------------
// Update Record Form
// ------------------
add_action('wp_ajax_pt_update_record_form', 'pt_update_record_form');
function pt_update_record_form() {
	$id = $_REQUEST['id'];
	$type = $_REQUEST['type'];
	if ($type == 'message') {pt_message_form($id); exit;}
	$types = array('colony', 'project', 'task', 'subtask');
	if (!in_array($type, $types)) {exit;}
	pt_record_form($type, $id);
}

// ---------------------------
// Create / Update Record Form
// ---------------------------
// handles: colony, project, task, (subtask)
function pt_record_form($type, $id=false) {

	$debug = true;
	global $prototasq, $pt_data;
	$labels = $prototasq['labels'] = pt_get_labels();
	$ajaxurl = $prototasq['ajaxurl'] = pt_get_ajax_url();
	$action = 'pt_create_record';

	if (!is_user_logged_in()) {
		echo __('Session timed out. Please login and try again.'); exit;
		// TODO: output interstitial login form?
	}

	if ($id) {
		$action = 'pt_update_record';

		// make sure we have a valid user
		$userid = pt_get_current_user_id();

		// revalidate record ID
		$record = pt_get_record($id);
		// if ($debug) {echo "Record: ".print_r($record);}
		if (!$record) {echo __('Error! That record no longer exists.'); exit;}

		// check record permissions
		$readonly = '';
		if ($record['creator'] != $userid) {
			// check role edit permissions for this action
			$type = substr($record['type'], 3, strlen($record['type']));
			$allowed = pt_get_record_permission($id, $type, 'edit');

			// no edit permissions so check view permissions
			if (!$allowed) {
				$allowed = pt_get_record_permission($id, $type, 'view');
				if ($allowed) {$readonly = ' readonly';}
				else {echo __('Error! You do not have permission to view this.'); exit;}
			}
		}

		// merge in metadata for this project / task
		if ($type == 'project') {
			$projectdata = pt_get_project_data($id);
			$record = array_merge($record, $projectdata);
		}
		elseif ( ($type == 'task') || ($type == 'subtask') ) {
			$taskdata = pt_get_task_data($id);
			$record = array_merge($record, $taskdata);
		}
	}

	// output stylesheet
	pt_stylesheet();

	// start form wrapper
	echo "<div id='pt-new-".$type."' class='pt-new-record'>";

	// new / edit record header
	// if ($id) {$label = $labels['add-new-'.$type];} else {$label = $labels['edit-'.$type];}
	// echo "<h3 id='pt-header-".$type."' class='pt-selection-header pt-thickbox-header'>".$label."</h3>";}

	// start form table
	echo "<form action='".$ajaxurl."' target='pt-response-frame' method='post'>";
	echo "<input type='hidden' name='action' value='".$action."'>";
	echo "<input type='hidden' name='type' value='".$type."'>";
	if (function_exists('wp_nonce_field')) {echo wp_nonce_field('pt-new-record');}
	echo "<table>";

	// display existing values (non-modifyable)
	if ($id) {

		// Record ID
		// ---------
		echo "<tr><td class='pt-input-label'><b>".__('ID')."</b>:</td><td width='20'></td>";
		echo "<td class='pt-input'><input type='hidden' name='id' value='".$id."'>".$id."</td></tr>";

		// Record Creator
		// --------------
		$displayname = pt_get_user_display($record['creator']);
		echo "<tr><td class='pt-input-label'><b>".__('Creator')."</b>:</td><td width='20'></td>";
		echo "<td class='pt-input'>".$displayname;
		if ($record['creator'] == $userid) {echo " (You)";}
		echo "</td></tr>";

		// created (reformatted)
		// $created = strtotime($record['created']);
		// $created = date('d/m/Y H:i:s', $created);
		// echo "<tr><td><b>".__('Created')."</b>:</td><td width='20'></td>";
		// echo "<td>".$created."</td></tr>";
		// modified (reformatted)
		// $modified = strtotime($record['modified']);
		// $modified = date('d/m/Y H:i:s', $modified);
		// echo "<tr><td><b>".__('Modified')."</b>:</td><td width='20'></td>";
		// echo "<td>".$modified."</td></tr>";
	}

	// Task / Subtask Status
	// ---------------------
	if ( ($type == 'task') || ($type == 'subtask') ) {
		echo "<tr><td class='pt-input-label'><b>".__('Status')."</b>:</td><td width='20'></td>";
		echo "<td><select name='status' class='pt-input pt-input-select'".$readonly.">";
		// TODO: re-validate existing status value ?
		$statuses = pt_get_task_statuses();
		foreach ($statuses as $slug => $status) {
			echo "<option value='".$slug."'";
			if ($id && ($slug == $record['status'])) {echo " selected='selected'";}
			echo ">".$status['label']."</option>";
		}
		echo "</select></td></tr>";
	}

	// Task Priority
	// -------------
	if ( ($type == 'task') || ($type == 'subtask') ) {
		echo "<tr><td class='pt-input-label'><b>".__('Priority')."</b>:</td><td width='20'></td>";
		echo "<td><select name='priority' class='pt-input pt-input-select'".$readonly.">";
		$priorities = pt_get_task_priorities();
		// TODO: re-validate existing priority value ?
		foreach ($priorities as $slug => $priority) {
			echo "<option value='".$slug."'";
			if ($id && ($slug == $record['priority'])) {echo " selected='selected'";}
			echo ">".$priority['label']."</option>";
		}
		echo "</select></td></tr>";
	}

	// Parent Domain
	// -------------
	if ($type != 'colony') {

		if ($id) {
			// re-validate parent record still exists
			$parentrecord = pt_get_record($record['parent']);
			if (!$parentrecord) {
				// TODO: output this warning somewhere
				$warning = __('Warning! Parent record no longer exists.');
			}
		} else {
			// TODO: for new task, select colony AND project

		}

		// TODO: check user permissions to move to these ?
		if ($type == 'project') {
			$parentlabel = $labels['colony']['single'];
			$parents = pt_get_records('colony');
		} elseif ($type == 'task') {
			$parentlabel = $labels['project']['single'];
			$parents = pt_get_records('project');
		} elseif ($type == 'subtask') {
			$parentlabel = $labels['task']['single'];
			$parents = pt_get_records('task');
		}

		echo "<tr><td class='pt-input-label'><b>".$parentlabel."</b>:</td><td width='20'></td>";
		echo "<td><select name='parent' class='pt-input pt-input-select'".$readonly.">";
		// TODO: maybe group parents by grandparent ?
		echo "<option value=''";
			if (!$id) {echo " selected='selected'";}
		echo ">".__('None')."</option>";
		if (count($parents) > 0) {
			foreach ($parents as $parent) {
				echo "<option value='".$parent['id']."'";
				if ($id && ($parent['id'] == $record['parent'])) {echo " selected='selected'";}
				echo ">".$parent['title']."</option>";
			}
		}
		echo "</select></td></tr>";
	}

	// Record Title
	// ------------
	echo "<tr><td class='pt-input-label'><b>".__('Title')."</b>:</td><td width='20'></td>";
	echo "<td><input type='text' name='title' class='pt-input pt-input-title' value='".$record['title']."'".$readonly.">";
	echo "</td></tr>";

	// Record Short Description
	// ------------------------
	echo "<tr><td class='pt-input-label'><b>".__('Description')."</b>:</td><td width='20'></td>";
	echo "<td><textarea name='description' rows='2' class='pt-input pt-input-description'".$readonly.">".$record['description']."</textarea>";
	echo "</td></tr>";

	// Record Content
	// --------------
	// (mission / outline / brief / notes)
	if ($type == 'colony') {$contentlabel = __('Mission');}
	elseif ($type == 'project') {$contentlabel = __('Outline');}
	elseif ($type == 'task') {$contentlabel = __('Task Brief');}
	else {$contentlabel = __('Notes');}
	echo "<tr><td class='pt-input-label'><b>".$contentlabel."</b>:</td><td width='20'></td>";
	echo "<td><textarea name='content' rows='8' class='pt-input pt-input-content'".$readonly.">".$record['content']."</textarea>";
	echo "</td></tr>";

	// Colony / Project Status
	// -----------------------
	if ( ($type == 'colony') || ($type == 'project') ) {
		echo "<tr><td class='pt-input-label'><b>".__('Status')."</b>:</td><td width='20'></td>";
		echo "<td><select name='status' class='pt-input pt-input-select'".$readonly.">";
		// TODO: re-validate existing status value ?
		if ($type == 'colony') {$statuses = pt_get_colony_statuses();}
		elseif ($type == 'project') {$statuses = pt_get_project_statuses();}
		if (!$id) {$record['status'] = 'pending';}
		foreach ($statuses as $slug => $label) {
			echo "<option value='".$slug."'";
			if ($id && ($slug == $record['status'])) {echo " selected='selected'";}
			echo ">".$label."</option>";
		}
		echo "</select></td></tr>";
	}

	// Category / Taxonomy Selection
	// -----------------------------
	if ($type == 'project') {

		// Project Type Selection
		// ----------------------
		// TODO: multiple checkbox list ?
		// $types = pt_get_project_types($record['parent']);

	} elseif ($type == 'task') {

		echo "<tr><td></td><td></td><td align='center'>";
			echo "<table><tr><td>";

			// Sector Selection
			// ----------------
			// TODO: maybe allow for multiple sector selection ?
			$sectors = pt_get_sectors($record['parent']);
			if (count($sectors) > 0) {
				echo pt_record_select_cell('sector', $sectors, $id, __('Sector'));
			}

			// Division Selection
			// ------------------
			$divisions = pt_get_divisions($record['parent']);
			if (count($divisions) > 0) {
				echo pt_record_select_cell('division', $divisions, $id, __('Division'));
			}

			// Stage Selection
			// ---------------
			$stages = pt_get_stages($record['parent']);
			if (count($stages) > 0) {
				echo pt_record_select_cell('stage', $stages, $id, __('Stage'));
			}

			echo "</tr></table>";
		echo "</td></tr>";
	}

	// Skill Selection
	// ---------------
	if ( ($type == 'task') || ($type == 'subtask') ) {
		if ($id) {$skills = pt_get_task_skills($record['parent']);}
		$skills = pt_get_task_skills();
		if (count($skills) > 0) {
			echo pt_record_select_skill_row('skill', $skills, $id, __('Skill'));
		}
	}
	// TODO: "add skill" button! yeesh

	// Tag Selection
	// -------------
	// TODO: ...

	// Colours
	// -------
	// TODO: ...

	// Record Privacy
	// --------------
	// (public / private / group ?)
	if ($record['creator'] == $userid) {
		// TODO: ...
	}

	// User Roles
	// ----------
	if ($id) {echo pt_select_role_rows($type, $id);}
	else {echo pt_select_role_rows($type);}

	// Record Update Submit Button
	// ---------------------------
	if ($id) {$submitlabel = $labels['update-'.$type]; $title = __('Cancel Update');}
	else {$submitlabel = $labels['create-'.$type]; $title = __('Cancel Creation');}
	$onclick = "closethickbox();";
	echo "<tr height='10'><td> </td></tr>";
	echo "<tr><td align='center'>";
		echo "<input type='button' id='pt-form-cancel-button' class='pt-cancel' onclick='".$onclick."' value='".__('Cancel')."' title='".$title."'>";
	echo "</td><td></td>";
	if (!$id) {$submitid = 'pt-create-submit';} else {$submitid = 'pt-update-submit';}
	echo "<td align='center'><input type='submit' id='".$submitid."' class='pt-submit' value='".$submitlabel."'></td></tr>";

	// close table form
	echo "</table></form>";

	// Response Wrapper
	// ----------------
	echo "<div id='pt-record-form-response-wrapper'";
		if (!$id) {echo " style='display:none;'";}
	echo "><center>";

		echo "<table id='pt-form-add-buttons'><tr>";

			// close window
			$label = __('Close');
			$onclick = 'closethickbox();';
			echo "<td><input type='button' id='pt-form-return-button' class='pt-cancel' onclick='".$onclick."' value='".$label."'";
				if ($action == 'create') {echo " style='display:none;'";}
			echo "></td><td width='20'></td>";

			// add new colony
			if ($type == 'colony') {
				$label = $labels['add-new-colony'];
				$onclick = 'newrecord("colony");';
				echo "<td id='pt-new-colony-button'>";
				echo "<input type='button' class='pt-add-new-button' onclick='".$onclick."' value='".$label."'>";
				echo "</td><td width='20'></td>";
			}
			// add new project
			if ($type == 'project') {
				$onclick = 'newrecord("project");';
				$label = $labels['add-new-project'];
				echo "<td id='pt-new-project-button'>";
				echo "<input type='button' class='pt-add-new-button' onclick='".$onclick."' value='".$label."'>";
				echo "</td><td width='20'></td>";
			}
			// add new task
			if ( ($type == 'project') || ($type == 'task') ) {
				$label = $labels['add-new-task'];
				$onclick = 'newrecord("task");';
				echo "<td id='pt-new-task-button'>";
				echo "<input type='button' class='pt-add-new-button' class='pt-add-new=button' onclick='".$onclick."' value='".$label."'>";
				echo "</td><td width='20'></td>";
			}
		echO "</tr></table>";

		// form submit response
		echo "<div id='pt-record-form-response' style='display:none;'></div>";

	echo "</center></div>";

	// delete record button
	// TODO: form placement
	if ($id && ($type != 'colony')) {
		if ($record['creator'] == $userid) {
			// echo "<form target='pt-reponse-frame' action='".$ajaxurl."'>";
			// echo "<input type='hidden' name='action' value='pt_delete_record'>";
			// echo "<input type='hidden' name='id' value = '".$id."'>";
			// wp_nonce_field('pt-delete-record');
			// $label = $labels['delete-'.$type];
			// echo "<input type='submit' value='".$label."'>";
			// echo "</form>";
		}
	}


	// TODO: resources, links, comments ..?

	// output response iframe
	pt_response_iframe('response', true, '500', '100');

	// close wrapper
	echo "</div>";

	// dynamic resize of thickbox window
	$tbelement = "pt-new-".$type;
	pt_auto_resize_thickbox($tbelement);

	exit;
}

// -----------------
// Record Select Row
// -----------------
function pt_record_select_skill_row($key, $typedata, $id, $label) {
	global $pt_data;
	if ($id) {$current = $pt_data['task'][$id][$key];}
	echo "<tr><td><b>".$label."</b>:</td><td width='20'></td>";
	echo "<td align='center'><select name='".$type."'>";
	echo "<option value=''";
		if (!$id || ($current == '')) {echo " selected='selected'";}
	echo "></option>";

	$prevgroup = '';
	$divisions = pt_get_divisions();
	foreach ($typedata as $slug => $data) {
		if (isset($data['division'])) {
			$optgroup = $data['division'];
			if ($optgroup != $prevgroup) {
				$division = $typedata[$slug]['division'];
				$label = $divisions[$division]['label'];
				if ($prevgroup != '') {echo "</optgroup>";}
				echo "<optgroup label='".$label."'>";
			}
		}
		echo "<option value='".$slug."'";
			if ($id && ($current == $slug)) {echo " selected='selected'";}
		echo ">".$data['label']."</option>";
		$prevgroup = $optgroup;
	}
	echo "</optgroup></select></td></tr>";
}

// ------------------
// Record Select Cell
// ------------------
function pt_record_select_cell($key, $typedata, $id, $label) {
	global $pt_data;
	if ($id) {$current = $pt_data['task'][$id][$key];}
	echo "<td align='center'>";
	echo "<b>".$label."</b><br>";
	echo "<select name='".$type."'>";
	echo "<option value=''";
		if (!$id || ($current == '')) {echo " selected='selected'";}
	echo "></option>";
	foreach ($typedata as $slug => $data) {
		echo "<option value='".$slug."'";
		if ($id && ($current == $slug)) {echo " selected='selected'";}
		echo ">".$data['label']."</option>";
	}
	echo "</select></td>";
	// </tr>";
}

// -----------------
// Select Roles Rows
// -----------------
function pt_select_role_rows($type, $id=false) {

	// TODO: external interested party (view permissions)
	// consultants / contributors / client (comment permissions)
	// Assign (single): manager / worker / evaluator

	$users = pt_get_users();
	if ($id) {$roles = pt_get_record_roles($id);}
	if ($type == 'colony') {$rolekeys = array('admin');}
	elseif ($type == 'project') {$rolekeys = array('manager', 'client');}
	elseif ($type == 'task') {$rolekeys = array('manager', 'worker', 'evaluator', 'consultant');}
	elseif ($type == 'subtask') {$rolekeys = array('worker', 'consultant');}

	foreach ($rolekeys as $rolekey) {
		$label = $labels[$rolekey];
		echo "<tr><td>".$label."</td><td width='20'></td>";
		echo "<td><select name='admin'>";
		echo "<option value=''>".__('Unassigned')."</option>";
		// TODO: maybe add an assign to self option ?
		echo "<option value='SELF'>".__('Self')."</option>";
		foreach ($users as $userid => $user) {
			echo "<option value='".$userid."'";
				if ($id && in_array($userid, $roles[$rolekey])) {echo " selected='selected'";}
			echo ">".user."</option>";
		}
		echo "</select></td></tr>";
	}
}

// ----------------------
// Create / Update Record
// ----------------------
add_action('wp_ajax_pt_create_record', 'pt_create_record');
add_action('wp_ajax_pt_update_record', 'pt_update_record');
function pt_create_record() {pt_modify_record('create');}
function pt_update_record() {pt_modify_record('update');}

function pt_modify_record($action) {

	$debug = true;
	// TODO: validate nonce ?
	// check_admin_referer(); / wp_verify_nonce();

	if (!isset($_POST['type'])) {exit;}
	$type = $_POST['type'];

	if ($action == 'update') {

		if (!isset($_POST['id'])) {echo __('Error! Missing Record ID.'); exit;}
		$id = $_POST['id']; $valid = pt_get_record($id);
		if (!$valid) {echo __('Error! Invalid Record ID.'); exit;}

		// check edit permissions
		if (function_exists('current_user_can') && current_user_can('manage_options')) {$allowed = true;}
		else {
			// check edit permissions
			$allowed = pt_get_record_permission($id, $type, 'edit');
		}
		if (!isset($allowed) || !$allowed) {echo __('Error! You do not have permissions to edit that.'); exit;}

	} else {
		// TODO: recheck create permissions for adding to parent type
		// $allowed = pt_get_record_permission($parentid, $parenttype, 'create');
		// if (!isset($allowed)) {echo __('Error! You do not have permissions to add to that.'); exit;}
	}

	// get status options
	if ($type == 'colony') {$statuses = pt_get_colony_statuses(); $roles = array('admin');}
	elseif ($type == 'project') {$statuses = pt_get_project_statuses(); $roles = array('manager');}
	elseif ($type == 'task') {$statuses = pt_get_task_statuses(); $roles = array('worker', 'evaluator', 'consultant');}
	elseif ($type == 'subtask') {$statuses = pt_get_task_statuses(); $roles = array('worker', 'consultant');}
	$statuslist = implode('/', array_keys($statuses));

	// common record data keys
	$postkeys = array(
		'status'		=> $statuslist,
		'title'			=> 'text',
		'description'	=> 'textarea',
		'content'		=> 'textarea',
		'parent'		=> 'numeric',
	);

	// task priority and skill
	if ( ($type == 'task') || ($type == 'subtask') ) {

		// task priority options
		$priorities = pt_get_task_priorities();
		$options = implode('/', array_keys($priorities));
		$postkeys['priority'] = $options;

		// task skill options
		$skillslist = pt_get_task_skills();
		$options = implode('/', array_keys($skillslist));
		$postkeys['skill'] = $options;
	}
	if ($debug) {print_r($postkeys);}

	// sanitize posted data
	foreach ($postkeys as $key => $datatype) {
		if (isset($_POST[$key])) {
			$posted = $_POST[$key];

			if (strstr($datatype, '/')) {
				$valid = explode('/', $datatype);
				if (in_array($posted, $valid)) {$data[$key] = $posted;}
			} elseif ($datatype == 'checkbox') {
				if ( ($posted == '') || ($posted == 'yes') ) {$data[$key] = $posted;}
			} elseif ($datatype == 'numeric') {
				$posted = absint($posted);
				if (is_numeric($posted)) {$data[$key] = $posted;}
			} elseif ($datatype == 'alphanumeric') {
				$checkposted = preg_match('/^[a-zA-Z0-9_]+$/', $posted);
				if ($checkposted) {$data[$key] = $posted;}
			} elseif ($datatype == 'text') {
				$posted = sanitize_text_field($posted);
				$data[$key] = $posted;
			} elseif ($datatype == 'textarea') {
				$posted = stripslashes(wp_kses_post($posted));
				$data[$key] = $posted;
			} elseif ($datatype == 'url') {
				// TODO: replace with a regex URL filter?
				$url = filter_var($posted, FILTER_SANITIZE_STRING);
				if ( (substr($url, 0, 4) != 'http') || (!filter_var($vurl, FILTER_VALIDATE_URL)) ) {$posted = '';}
				$data[$key] = $posted;
			}
		}
	}

	// output head and stylesheet
	echo "<html><head>"; pt_stylesheet(); echo "</head>";

	// open response wrapper
	echo "<body><div class='pt-update-response'>";

	// set post data
	$post = pt_record_data_map($data);
	$post['post_type'] = 'pt-'.$type;
	if (isset($postmeta)) {$post['meta_input'] = $postmeta;}

	if ($debug) {
		echo "Post Keys: ".print_r($postkeys, true)."<br>";
		echo "Data: ".print_r($data, true)."<br>";
		echo "Post: ".print_r($post, true)."<br>";
	}

	// update or create record
	if ($id) {
		$post['ID'] = $id;
		$update = pt_update_data_record($post);
		if ($update) {$response = __('Successfully Updated.');}
		else {$response = __('Update Failed.');}
	} else {
		$id = pt_insert_data_record($post);
		if ($id) {$response = __('Successfully Created.')." ID: ".print_r($id, true);}
		else {$response = __('Creation Failed.');}
	}
	if ($debug) {echo "Response: ".$response."<br>";}

	// note: other options for post insert/update
	// 'post_category'
	// (array) Array of category names, slugs, or IDs. Defaults to value of the 'default_category' option.
	// 'tags_input'
	// (array) Array of tag names, slugs, or IDs. Default empty.
	// 'tax_input'
	// (array) Array of taxonomy terms keyed by their taxonomy name. Default empty.
	// 'meta_input'
	// (array) Array of post meta values keyed by their post meta key. Default empty.

	if ($id) {

		if ($type == 'task') {

			// get existing task data
			$taskdata = pt_get_task_data($id);

			// get value options
			$values['sector'] = pt_get_sectors($parentid);
			$values['division'] = pt_get_divisions($parentid);
			$values['stage'] = pt_get_stages($parentid);

			// loop data types
			foreach ($datatypes as $datatype) {
				if (isset($_POST[$datatype])) {
					$value = $_POST[$datatype];
					if (in_array($value, $values[$datatype])) {$taskdata[$datatype] = $value;}
					else {$taskdata[$datatype] = '';}
				}
			}

			// update task data
			$update = pt_update_task_data($id, $taskdata);
			if ($debug) {echo "Task Data: ".print_r($taskdata, true)."<br>";}
		}

		echo "A";

		// role record and user
		// TODO: allow for some multiple roles ?
		$roledata = array();
		foreach ($roles as $role) {
			if (isset($_POST[$role])) {
				$roleuserid = (int)$_POST[$role];
				$roledata[$role] = $roleuserid;
			}
		}
		if (count($roledata) > 0) {
			// update user roles for record
			pt_update_record_roles($id, $roledata);
			if ($debug) {echo "Roles: ".print_r($roledata, true)."<br>";}

			// update record roles for assigned users
			foreach ($roledata as $role => $userid) {
				$roles = pt_get_user_roles($userid);
				if (!$roles) {$roles = array();}
				if (!isset($roles[$role])) {$roles[$role] = array();}
				$roles[$role][] = $id;
				$update = pt_update_user_roles($userid, $roles);
			}
		}

		echo "B";

		// maybe add back to selection list in parent window
		// if ( ($type == 'colony') || ($type == 'project') || ($type == 'task') ) {
			$record = pt_get_record($id);
			$item = pt_checkbox_list_item($record, $type, array());

			echo "C";

			$nl = PHP_EOL;
			echo "<script>";
			// send reponse output to parent div
			echo "responsediv = parent.document.getElementById('pt-record-form-response'); ".$nl;
			echo "responsediv.innerHTML = '".$response."'; ".$nl;
			echo "responsediv.style.display = ''; ".$nl;
			echo "responsewrapper = parent.document.getElementById('pt-record-form-response-wrapper'); ".$nl;
			echo "responsewrapper.style.display = ''; ".$nl;

			// remove existing list item if updating
			if ($action == 'update') {
				$itemid = 'pt-list-item-'.$id;
				echo "item = parent.document.getElementById('".$itemid."'); ".$nl;
				echo "item.parentNode.removeChild(item); ".$nl;
			}

			// change the buttons
			if ($action == 'create') {
				$labels = pt_get_labels(); $label = $labels['update-'.$type];
				echo "parent.document.getElementById('pt-form-cancel-button').style.display = 'none'; ".$nl;
				echo "parent.document.getElementById('pt-form-return-button').style.display = ''; ".$nl;
				echo "parent.document.getElementById('pt-create-submit').value = '".$label."'; ".$nl;
			}

			// add the new item to the select list
			echo $item['js'];
			echo "selectlist = parent.document.getElementById('pt-list-".$type."'); ".$nl;
			// if ($debug) {echo "console.log(selectlist); console.log(item); ";}
			echo "selectlist.appendChild(item); ".$nl;
			echo "</script>";
		// }

	}

	// close body wrapper
	echo "</div></body></html>";

	exit;
}

// -------------
// Delete Record
// -------------
add_action('wp_ajax_pt_delete_record', 'pt_delete_record');
function pt_delete_record() {
	$id = $_POST['id'];
	$userid = pt_get_current_user_id();
	if (!$userid) {exit;}
	$record = pt_get_record($id);
	if (!$record) {echo __('Error! That record no longer exists.'); exit;}
	if ($record['creator'] != $userid) {
		$type = substr($record['type'], 3, strlen($record['type']));

		// colonies cannot be deleted
		if ($type == 'colony') {echo __('Error! You cannot delete a Colony.'); exit;}

		// check role permissions for this action
		$allowed = pt_get_record_permission($id, $type, 'delete');
		if (!$allowed) {echo __('Error! You do not have permission to do that.'); exit;}

		// TODO: check this is not already a deployed task contract
		// ie. if it is already on-chain then deleting is pointless
		// if ($record['type'] == 'pt-task') {}

	} else {$allowed = true;}

	exit; // TEMP (dissallow delete anyway for now)

	$delete = wp_delete_post($id);
	if ($delete) {echo __('Successfully deleted.');}
	else {echo __('Record deletion failed.');}
	exit;
}

// --------------
// Archive Record
// --------------
function pt_archive_record() {
	// TODO: low priority action
}

// ------------------
// Tag Selection Form
// ------------------
function pt_tag_form() {
	// TODO: medium priority
}

// -----------------
// Label Editor Form
// -----------------
function pt_label_form() {
	// TODO: low priority
}

// ========
// Messages
// ========
function pt_message_form() {

	// output stylesheet
	pt_stylesheet();

	// open response wrapper
	echo "<div id='pt-messages-form' class='pt-form-wrapper'>";

	// TODO: ...
	echo "NEW MESSAGE FORM";

	// close wrapper
	echo "</div>";

	exit;
}

// ========
// Settings
// ========
add_action('wp_ajax_pt_settings_form', 'pt_settings_form');
function pt_settings_form() {

	// output stylesheet
	pt_stylesheet();

	// open response wrapper
	echo "<div id='pt-settings-form' class='pt-form-wrapper'>";

	echo "<h3>".__('User Settings')."</h3>";
	// TODO: user settings form
	echo __('There are no user settings yet.')."<br><br>";

	echo "<a href='admin.php?page=prototasq&clearsaved'>".__('Clear Saved Selections')."</a><br>";

	if (current_user_can('manage_options')) {
		echo "<h3>".__('Admin Settings')."</h3>";
		// TODO: plugin settings form
		echo __('There are no admin settings yet.');
	}

	// close wrapper
	echo "</div>";

	exit;
}

// ======
// Inputs
// ======

// ----------------------------
// Create / Update Select Input
// ----------------------------
add_action('wp_ajax_pt_create_select', 'pt_create_select_ajax');
add_action('wp_ajax_pt_update_select', 'pt_update_select_ajax');
function pt_create_select_ajax() {pt_select_ajax('create');}
function pt_update_select_ajax() {pt_select_ajax('update');}

function pt_select_ajax($action) {

	$nl = PHP_EOL; $debug = true;

	$type = $_REQUEST['type'];
	$taskid = $_REQUEST['taskid'];
	// $current = $_REQUEST['current'];

	if (!is_user_logged_in()) {echo __('Session timed out. Please login and try again.'); exit;}

	// check standalone task permissions
	$allowed = pt_get_record_permission($taskid, $type, 'edit');
	if (!$allowed) {echo __('Error! You do not have permission to edit that.'); exit;}

	$task = pt_get_record($taskid);
	$taskdata = pt_get_task_data($taskid);
	if ($taskdata && is_array($taskdata)) {$task = array_merge($task, $taskdata);}
	if (isset($task[$type])) {$current = $task[$type];} else {$current = 'none';}
	if ($debug) {echo "Current: ".$current."<br>";}

	if ($action == 'create') {

		if ($debug) {echo "Type: ".$type."<br>";}
		if ($type == 'priority') {
			$datas = pt_get_task_priorities();
			foreach ($datas as $slug => $priority) {
				if ($priority['position'] == $current) {$current = $slug;}
			}
		}
		elseif ($type == 'status') {$datas = pt_get_task_statuses();}
		elseif ($type == 'division') {$datas = pt_get_divisions();}
		elseif ($type == 'sector') {$datas = pt_get_sectors();}
		elseif ($type == 'stage') {$datas = pt_get_stages();}
		if ($debug) {echo "Options: ".print_r($datas,true)."<br>";}

		$onchange = 'updateselectcell("'.$taskid.'", "'.$type.'");';
		echo "<script>selected = '".$current."'; ".$nl;
		echo "options = new Array(); labels = new Array(); ".$nl;

		// add a cancel change select option
		echo "options[0] = 'cancel'; labels[0] = '".__('No Change')."'; ".$nl;

		$i = 1;
		foreach ($datas as $slug => $data) {
			echo "options[".$i."] = '".$slug."'; ";
			echo "labels[".$i."] = '".$data['label']."'; ".$nl;
			$i++;
		}
		echo "cellid = 'pt-task-".$type."-".$taskid."'; ".$nl;
		echo "cell = parent.document.getElementById(cellid); ".$nl;
		echo "select = document.createElement('select'); ".$nl;
		echo "select.setAttribute('id', cellid+'-select'); ".$nl;
		echo "select.setAttribute('class', 'pt-dynamic-select'); ".$nl;
		echo "select.setAttribute('name', '".$type."'); ".$nl;
		echo "select.setAttribute('onchange', '".$onchange."'); ".$nl;
		echo "for (i in options) { ".$nl;
		// echo "	console.log(options[i]); ".$nl;
		echo "	option = document.createElement('option'); ".$nl;
		echo "	option.setAttribute('value', options[i]); ".$nl;
		echo "	if (selected == options[i]) {option.setAttribute('selected', 'selected');} ".$nl;
		echo "	option.innerHTML = labels[i]; ".$nl;
		echo "	select.appendChild(option); ".$nl;
		echo "}".$nl;
		echo "cell.innerHTML = ''; ".$nl;
		echo "cell.appendChild(select); ".$nl;
		echo "</script>";
	}

	if ($action == 'update') {

		$value = $_GET['value'];

		if ($value != 'cancel') {

			// update record data or meta data
			$metatypes = array('sector', 'division', 'stage');
			if (in_array($type, $metatypes)) {

				echo "OKA1";

				// validate value and get label
				if ($type == 'sector') {$data = pt_get_sectors();}
				elseif ($type == 'division') {$data = pt_get_divisions();}
				elseif ($type == 'stage') {$data = pt_get_stages();}
				if (!array_key_exists($value, $data)) {
					echo "<script>alert('Error! Invalid selection value!')</script>"; exit;
				}
				$label = $data[$value]['label'];
				echo "OKA2";

				// update record meta data
				$taskdata = pt_get_task_data($taskid);
				$taskdata[$type] = $value;
				echo "OKA3";
				$update = pt_update_task_data($taskid, $taskdata);
				echo "OKA4";

				if ($debug) {echo "Task Data: ".print_r($taskdata,true)."<br>";}

			} else {

				echo "OKAB";

				// validate value and get label and post column key
				if ($type == 'colony') {
					$datakey = 'post_parent';
					// TODO: validate colony still exists
					// $task = pt_get_record($taskid, 'post_parent');
					// $project = pt_get_record($task['parent'], 'post_title');
					// $data = pt_get_records('colony');
					// TODO: validate permissions
					// TODO: get colony title
					// $label = $data[];
				} elseif ($type == 'project') {
					$datakey = 'post_parent';
					$data = pt_get_records('project');
					// TODO: validate project still exists
					// TODO: validate permissions
					// TODO: get project title
					// $label = $data[];
				} elseif ($type == 'priority') {
					$datakey = 'menu_order';
					$data = pt_get_task_priorities();
					$label = $data[$value]['label'];
				} elseif ($type == 'status') {
					$datakey = 'post_status';
					$data = pt_get_task_statuses();
					$label = $data[$value]['label'];
				}

				if ($debug) {
					echo "Data: ".print_r($data,true)."<br>";
					echo "Label: ".$label."<br>";
				}

				// update record data
				$record = array('ID' => $taskid, $datakey => $value);
				$update = pt_update_data_record($record);
			}
		}

		echo "OKC";
		$div = pt_task_cell_create_select_div($slug, $taskid, $label);

		$cellid = "pt-task-".$type."-".$taskid;
		echo "<script>".$nl;
		echo "cellid = '".$cellid."'; ".$nl;
		echo "cell = parent.document.getElementById(cellid); ".$nl;
		echo "cell.innerHTML = ''; ".$nl;
			echo $div['js'].$nl;
		echo "cell.appendChild(clickdiv); ".$nl;
		echo "</script>".$nl;

	}

	exit;
}

// ----------------
// Response Handler
// ----------------
add_action('wp_ajax_pt_response_handler', 'pt_response_handler');
function pt_response_handler() {

	$log = $_REQUEST['log'];
	if ($log == '') {exit;}
	$log = json_decode($log, true);

	if (defined('PT_LOG_FILEPATH')) {$logpath = PT_LOG_FILEPATH;}
	else {$logpath = dirname(__FILE__).'/console.log';}

	if ( (is_array($log)) || (is_object($log)) ) {$log = print_r($log, true);}

	// TODO: maybe process response here

	error_log($log.PHP_EOL, 3, $logpath); exit;
}


// ====
// IPFS
// ====

// -----------------
// Load IPFS Scripts
// -----------------
function pt_ipfs_load_scripts() {

	$ipfs = plugins_url('scripts/buffer.js', __FILE__);
	$ipfs = plugins_url('scripts/ipfs-api.min.js', __FILE__);
	echo "<script src='".$buffer."'></script>";
	echo "<script src='".$ipfs."'></script>";

}

// ----------------
// IPFS Upload File
// ----------------
add_action('wp_ajax_pt_ipfs_upload', 'pt_ipfs_upload');
function pt_ipfs_upload() {

	pt_ipfs_load_scripts();
	$data = $_POST['data'];

	echo "<script>data = '".$data."';
	const ipfs = new ipfsApi({ host: 'ipfs.infura.io', port: 5001, protocol: 'https' });
	const data = Buffer.from(JSON.stringify(briefData));
	const files = await ipfs.files.add(data)
	const { hash } = files[0];
	parent.document.getElementById('pt_ipfs_hash').value = hash;
	</script>";
	exit;

}

// ------------------
// IPFS Download File
// ------------------
add_action('wp_ajax_pt_ipfs_download', 'pt_ipfs_download');
function pt_ipfs_download() {

	pt_ipfs_load_scripts();
	$taskid = $_POST['taskid'];

	echo "<script>taskId = '".$taskid."';
	const task = await colonyClient.getTask.call({ taskId })
	const buffer = await ipfs.files.cat(`/ipfs/${task.specificationHash}`);
	const contents = JSON.parse(buffer.toString());
	parent.document.getElementById('pt_ipfs_brief').value = contents;
	</script>";
	exit;
}

