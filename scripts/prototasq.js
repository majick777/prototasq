/* ================ */
/* Prototasq v0.9.0 */
/* ================ */

/* To load tarp.require in global scope */
/* self.require = Tarp.require; */

/* Switch Domain Menu Tabs */
function pt_showdomaintab(id) {
	selectid = 'pt-select-'+id; tabid = 'pt-menu-tab-'+id;
	if (document.getElementById('pt-select-info')) {
		document.getElementById('pt-select-info').style.display = 'none';
	}
	document.getElementById('pt-select-colony').style.display = 'none';
	document.getElementById('pt-select-project').style.display = 'none';
	document.getElementById('pt-select-task').style.display = 'none';
	if (document.getElementById('pt-select-message')) {
		document.getElementById('pt-select-message').style.display = 'none';
	}
	document.getElementById(selectid).style.display = '';

	/* upate tab button active/inactive classes */
	activeclass = 'pt-domain-menu-item pt-menu-item pt-menu-item-active';
	inactiveclass = 'pt-domain-menu-item pt-menu-item pt-menu-item-inactive';
	if (document.getElementById('pt-menu-tab-info')) {
		document.getElementById('pt-menu-tab-info').setAttribute('class', inactiveclass);
	}
	document.getElementById('pt-menu-tab-colony').setAttribute('class', inactiveclass);
	document.getElementById('pt-menu-tab-project').setAttribute('class', inactiveclass);
	document.getElementById('pt-menu-tab-task').setAttribute('class', inactiveclass);
	if (document.getElementById('pt-menu-tab-message')) {
		document.getElementById('pt-menu-tab-message').setAttribute('class', inactiveclass);
	}
	document.getElementById(tabid).setAttribute('class', activeclass);
}

/* Update Selection */
function pt_updateselection(type) {
	selected = '';
	checkboxclass = 'pt-checkbox-'+type;
	checkall = document.getElementById('pt-checkbox-'+type+'-all');
	if (checkall.checked == '1') {selected = 'all';}
	else {
		checkboxes = document.getElementsByClassName(checkboxclass);
		for (i in checkboxes) {
			if (checkboxes[i].checked == '1') {
				id = checkboxes[i].id;
				id = id.replace('pt-checkbox-'+type+'-', '');
				if (selected == '') {selected = id;} else {selected += ','+id;}
			}
		}
		selected = encodeURIComponent(selected);
	}
	framesrc = ajaxurl+'?action=pt_update_selection&type='+type+'&checked='+selected;
	console.log(framesrc);
	document.getElementById('pt-selection-frame').src = framesrc;
}

/* Restore Selection */
function pt_restoreselection(type, selectedlist) {
	checkboxclass = 'pt-checkbox-'+type;
	checkboxes = document.getElementsByClassName(checkboxclass);

	/* get existing stored selection values */
	selectedlist = document.getElementById('pt-selection-'+type);
	if (selectedlist.value.indexOf(',') != -1) {selected = selectedlist.value.split(',');}
	else {selected = new Array(); selected[0] = selectedlist.value;}

	/* store existing checkbox values */
	j = 0; stored = new Array();
	for (i in checkboxes) {
		checkboxid = checkboxes[i].id;
		if (checkboxid != undefined) {
			checkbox = document.getElementById(checkboxid);
			checkvalue = checkboxid.replace('pt-checkbox-'+type+'-', '');
			if (checkbox.checked) {stored[j] = checkvalue; j++;}
		}
	}
	storelist = stored.join(',');
	selectedlist.value = stored.join(',');

	/* check previous checked boxes */
	clearselection(type);
	for (i in selected) {
		if (selected[i] != '') {
			checkboxid = 'pt-checkbox-'+type+'-'+selected[i];
			console.log(checkboxid);
			document.getElementById(checkboxid).checked = 1;
		}
	}
}

/* Clear Selection */
function pt_clearselection(type) {
	checkboxclass = 'pt-checkbox-'+type;
	checkboxes = document.getElementsByClassName(checkboxclass);
	for (i in checkboxes) {checkboxes[i].checked = 0;}
}

/* Get Column States */
function pt_getdatastates() {

	/* TODO: get selected colonies */

	/* TODO: get selected projects */

	/* TODO: get selected filters */

	/* get selected tasks */
	checkboxes = document.getElementsByClassName('pt-checkbox-task');
	j= 0; k = 0; taskids = alltaskids = new Array(); alltasks = false;
	for (i in checkboxes) {
		console.log(checkboxes[i]);
		if (checkboxes[i].name) {
			taskid = checkboxes[i].name;
			taskid = taskid.replace('pt-checkbox-task-', '');
			if (checkboxes[i].checked == '1') {
				if (taskid == 'all') {alltasks = true;}
				taskids[j] = taskid; j++;
			}
			alltaskids[k] = taskid; k++;
		}
	}
	if (alltasks) {taskidlist = alltaskids.join(',');} else {taskidlist = taskids.join(',');}
	/* console.log(taskidlist); */

	/* get column display states */
	checkboxes = document.getElementsByClassName('pt-display-checkbox');
	j = 0; show = new Array();
	for (i in checkboxes) {
		if (checkboxes[i].checked == '1') {
			columnid = checkboxes[i].id;
			columnid.replace('pt-display-checkbox-', '');
			show[j] = column; j++;
		}
	}
	showlist = show.join(',');
	console.log(showlist);

	/* TODO: get column order states */
	/* orderlist = order.join(','); */

	/* update loader form inputs */
	document.getElementById('pt-taskid-list').value = taskidlist;
	document.getElementById('pt-column-display-state').value = showlist;
	/* document.getElementById('pt-column-display-order').value = orderlist; */
}

/* Shift Task Order */
function shifttaskorder(leftright, id) {
	listid = 'pt-task-order-item-'+id;
	if (document.getElementById(listid)) {listitem = document.getElementById(listid);}
	else {console.log('Error. Could not find list element '+listid); return false;}

	/* TODO: swap the list items over */
	/* TODO: reorder the task table (difficult!!!) */

}

/* Toggle Column Display */
function togglecolumndisplay(column) {
	inputid = 'pt-display-checkbox-'+column;
	if (document.getElementById(inputid)) {columndisplay = document.getElementById(inputid);}
	else {console.log('Error. Could not find checkbox element '+inputid); return false;}

	/* toggle column cell display */
	cellclass = 'pt-cell-'+column;
	cells = document.getElementsByClassName(cellclass);
	console.log(cellclass); console.log(cells);
	if (cells) {
		/* switched around because checkbox is checked immediately */
		if (columndisplay.checked) { for (i in cells) {if (cells[i]) {cells[i].style.display = '';} } }
		else { for (i in cells) {if (cells[i]) {cells[i].style.display = 'none';} } }
	}

	/* toggle column label and menu arrows */
	labelid = 'pt-column-menu-display-'+column;
	larrowid = 'pt-arrow-cell-'+column+'-left';
	rarrowid = 'pt-arrow-cell-'+column+'-right';
	if (columndisplay.checked) {display = 'none';} else {display = '';}
	labelid.style.display = display; larrow.style.display = display; rarrow.style.display = display;
}

/* Toggle Task Row Display */
function toggletaskrow(taskid) {

	/* TODO: show / hide the selected task row */


}

/* Check Checkbox via Link */
function checkboxcheck(id) {
	checkbox = document.getElementById(id);
	if (checkbox) {checkbox.checked = '1';}
}

/* Close Thickbox */
function closethickbox() {
	elements = document.getElementsByClassName('tb-close-icon');
	elements[0].click();
}

/* New Record Form */
function newrecord(type) {
	document.getElementById('pt-add-new-'+type).click();
}

/* Create Select Cell */
function createselectcell(taskid, type) {
	src = ajaxurl+'?action=pt_create_select&taskid='+taskid+'&type='+type;
	document.getElementById('pt-create-select-frame').src = src;
}

/* Update Select Cell */
function updateselectcell(taskid, type) {
	selectid = 'pt-task-'+type+'-'+taskid+'-select';
	select = parent.document.getElementById(selectid);
	value = select.options[select.selectedIndex].value;
	/* process cancellation option ? */
	src = ajaxurl+'?action=pt_update_select&taskid='+taskid+'&type='+type+'&value='+value;
	document.getElementById('pt-update-select-frame').src = src;
}

/* Show / Hide Element */
function showhide(id) {
	if (document.getElementById('pt-'+id).style.display == 'none') {
		document.getElementById('pt-'+id).style.display = '';
	} else {document.getElementById('pt-'+id).style.display = 'none';}
}

/* Object Response Handler */
function pt_response_handler(response) {
	// stringify and send to server handler
	string = JSON.stringify(response);
	var xhttp = new XMLHttpRequest();
	xhttp.open('POST', ajaxurl+'?action=pt_response_handler', true);
	xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	xhttp.send('log='+encodeURIComponent(string));
}

/* Simple Console Logging Wrapper */
pt_logoriginal = console.log;
function pt_logger(log) {
	// stringify and send to server logger
	string = JSON.stringify(log);
	var xhttp = new XMLHttpRequest();
	xhttp.open('POST', pluginurl+'/logger.php', true);
	xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	xhttp.send('log='+encodeURIComponent(string));
	// use original copy of console.log as normal
	pt_logoriginal(log);
}
// replace inbuilt console.log with log wrapper function
console.log = pt_logger;