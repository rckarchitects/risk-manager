<?php

function RiskManager_Table(){

  global $wpdb;
  $charset_collate = $wpdb->get_charset_collate();

  $tablename = $wpdb->prefix."riskmanager";

  $sql = "CREATE TABLE " . $tablename . " (
  risk_id mediumint(11) NOT NULL AUTO_INCREMENT,
  risk_title varchar(200),
  risk_project_number varchar(12),
  risk_project_name varchar(200),
  risk_description text,
  risk_level varchar(25),
  risk_analysis text,
  risk_score varchar(25),
  risk_warnings text,
  risk_mitigation text,
  risk_management text,
  risk_responsibility varchar(200),
  risk_timestamp int(20),
  risk_date date,
  risk_category varchar(50),
  risk_user varchar(200),
  risk_drawing varchar(200),
  risk_resolved tinyint(4),
  PRIMARY KEY  (risk_id)
  ) " . $charset_collate . ";";

  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta( $sql );

}

function RiskManager_PageSelect() {
// This function looks for a project and displays the details of that project if it finds it, and a list of active projects if it doesn't

	if ($_GET['project'] == "new") { RiskManager_ProjectView($_POST['risk_project_number'][0]); }
	elseif ($_GET['project']) { RiskManager_ProjectView($_GET['project']); }
	elseif ($_GET['risk']) { RiskManager_RiskView($_GET['risk']); }
	else { RiskManager_ProjectList(); }
	
}

function RiskManager_Color($value,$type) {
	
	if ($type == 'level') {
		
		if ($value == 'green') { return 'level-green risk'; }
		elseif ($value == 'amber') { return 'level-amber risk'; }
		elseif ($value == 'red') { return 'level-red risk'; }
		
	} elseif ($type == 'score') {
		
		if ($value == 'low') { return 'score-low risk'; }
		elseif ($value == 'medium') { return 'score-medium risk'; }
		elseif ($value == 'high') { return 'score-high risk'; }
		
		else  { return 'risk'; }
		
	}	
	
}

function RiskManager_Sanitise($string) {
	
	$output = filter_var ($string, FILTER_SANITIZE_STRING);
	$output = trim($output);
	return $output;
	
}

function RiskManager_Update() {

	$count = 0;
	global $wpdb;
	$tablename = $wpdb->prefix."riskmanager";
	
	foreach ($_POST['risk_id'] AS $risk_id) {
		
		if ($_POST['risk_title'][$count] && intval($_POST['risk_id'][$count]) > 0) {
		
			$sql = "
			UPDATE `" . $tablename . "`
			SET
			`risk_title` = '" . RiskManager_Sanitise($_POST['risk_title'][$count]) . "',
			`risk_level` = '" . RiskManager_Sanitise($_POST['risk_level'][$count]) . "',
			`risk_score` = '" . RiskManager_Sanitise($_POST['risk_score'][$count]) . "',
			`risk_user` = '" . RiskManager_Sanitise($_POST['risk_user'][$count]) . "',
			`risk_date` = '" . RiskManager_Sanitise($_POST['risk_date'][$count]) . "',
			`risk_description` = '" . RiskManager_Sanitise($_POST['risk_description'][$count]) . "',
			`risk_analysis` = '" . RiskManager_Sanitise($_POST['risk_analysis'][$count]) . "',
			`risk_mitigation` = '" . RiskManager_Sanitise($_POST['risk_mitigation'][$count]) . "',
			`risk_warnings` = '" . RiskManager_Sanitise($_POST['risk_warnings'][$count]) . "',
			`risk_management` = '" . RiskManager_Sanitise($_POST['risk_management'][$count]) . "',
			`risk_category` = '" . RiskManager_Sanitise($_POST['risk_category'][$count]) . "',
			`risk_responsibility` = '" . RiskManager_Sanitise($_POST['risk_responsibility'][$count]) . "',
			`risk_drawing` = '" . RiskManager_Sanitise($_POST['risk_drawing'][$count]) . "',
			`risk_resolved` = " . intval($_POST['risk_resolved'][$count]) . ",
			`risk_timestamp` = '" . time() . "'
			WHERE
			`risk_id` = " . intval($_POST['risk_id'][$count]) . " LIMIT 1;
			";
		
		} elseif (trim($_POST['risk_title'][$count]) != "" && $_POST['risk_id'][$count] == "") {
			
			$sql = "
			INSERT INTO `" . $tablename . "`
			(
			`risk_id`,
			`risk_project_number`,
			`risk_project_name`,
			`risk_title`,
			`risk_level`,
			`risk_score`,
			`risk_user`,
			`risk_date`,
			`risk_description`,
			`risk_analysis`,
			`risk_mitigation`,
			`risk_warnings`,
			`risk_management`,
			`risk_category`,
			`risk_responsibility`,
			`risk_drawing`,
			`risk_timestamp`,
			`risk_resolved`
			) VALUES (
			NULL,
			'" . RiskManager_Sanitise($_POST['risk_project_number'][$count]) . "',
			'" . RiskManager_Sanitise($_POST['risk_project_name'][$count]) . "',
			'" . RiskManager_Sanitise($_POST['risk_title'][$count]) . "',
			'" . RiskManager_Sanitise($_POST['risk_level'][$count]) . "',
			'" . RiskManager_Sanitise($_POST['risk_score'][$count]) . "',
			'" . RiskManager_Sanitise($_POST['risk_user'][$count]) . "',
			'" . RiskManager_Sanitise($_POST['risk_date'][$count]) . "',
			'" . RiskManager_Sanitise($_POST['risk_description'][$count]) . "',
			'" . RiskManager_Sanitise($_POST['risk_analysis'][$count]) . "',
			'" . RiskManager_Sanitise($_POST['risk_mitigation'][$count]) . "',
			'" . RiskManager_Sanitise($_POST['risk_warnings'][$count]) . "',
			'" . RiskManager_Sanitise($_POST['risk_management'][$count]) . "',
			'" . RiskManager_Sanitise($_POST['risk_category'][$count]) . "',
			'" . RiskManager_Sanitise($_POST['risk_responsibility'][$count]) . "',
			'" . RiskManager_Sanitise($_POST['risk_drawing'][$count]) . "',
			'" . time() . "',
			" . intval($_POST['risk_resolved'][$count]) . "
			);
			";
			
		}
		
		//$entriesList = $wpdb->get_results($sql);
		
		echo "<pre>" . $sql . "</pre>";
		
		//echo "<pre>"; print_r($_POST) ; echo "</pre>";
		
		$count++;
		
	}
		
}

function RiskManager_Resolved($risk_resolved) {
	
	if ($risk_resolved == 1) { return 'risk-resolved'; }
	else { return 'risk-unresolved'; }	
	
}

function RiskManager_GetDatalistCategories($risk_project) {
	
	global $wpdb;
	$tablename = $wpdb->prefix."riskmanager";
	$sql = "SELECT `risk_category` FROM " . $tablename . " WHERE `risk_project_number` = '" . htmlspecialchars($risk_project) . "' GROUP BY `risk_category` ORDER BY `risk_category` ASC";
	
	$entriesList = $wpdb->get_results($sql);
	
	$output = array();
	
	foreach( $entriesList as $entry ) {
		
		$output[] = $entry->risk_category;
		
	}
	
	return $output;
	
}

function RiskManager_PresentDatalistCategories($array) {
	
	foreach ($array AS $line) {
		
		$output = $output . "<option value=\"" . $line . "\">" . $line . "</option>";
		
	}
	
	return $output;
	
}

function RiskManager_GetAllUserNames() {
	
	$user_array = get_users();
	
	$output_array = array();
	
	foreach ($user_array AS $user) {
		
		$output_array[] = $user->first_name . " " . $user->last_name;
		
	}
	
	return $output_array;
	
}

function RiskManager_GetResponsibilities($risk_project) {
	
	global $wpdb;
	
	$tablename = $wpdb->prefix."riskmanager";
	
	$sql = "SELECT `risk_responsibility` FROM `" . $tablename . "` WHERE `risk_project_number` = '" . htmlspecialchars($risk_project) . "' GROUP BY `risk_responsibility` ORDER BY `risk_responsibility`";
	
	$entriesList = $wpdb->get_results($sql);
	
	if(count($entriesList) > 0){
		
		$output_array = array();
		
		foreach( $entriesList as $entry ) {
			
			$output_array[] = $entry->risk_responsibility;
			
		}
		
		return $output_array;
		
	}
	
}

function RiskManager_DateFormat($date) {
	
	$array = explode("-",$date);
	
	if (count($array) > 1) {
		$time = mktime(12,0,0,$array[1],$array[2],$array[0]);
		return date("j M Y",$time);
	} else {
		return "-";
	}
	
}

function RiskManager_ProjectView($risk_project) {
		
// This function shows a list of risks associated with a selected project

if ($_POST['button-risk-update']) { RiskManager_Update(); }
	
	global $wpdb;
	global $wp;

	$tablename = $wpdb->prefix."riskmanager";
	
	$sql = "SELECT * FROM " . $tablename . " WHERE `risk_project_number` = '" . htmlspecialchars($risk_project) . "' ORDER BY `risk_category` ASC, `risk_resolved` ASC, `risk_title` ASC, `risk_date` ASC";
	
	$entriesList = $wpdb->get_results($sql);
	
	echo "<p><a href=\"" . home_url( add_query_arg( array(), $wp->request ) ) . "\">&larr;&nbsp;All Projects</a></p>";
	
	$plugin_path = plugin_dir_url(__FILE__);
	
	echo "<a href=\"" . $plugin_path . "pdf.php\"><button class=\"risk-print-button\">Print</button></a>";
	
	RiskManagerPopUp();
	
	$count = 0;
	
	if(count($entriesList) > 0) {
		
		$entriesList[] = NULL;
				
		foreach( $entriesList as $entry ) {
			
			if ($entry->risk_id > 0) {
			
				if (($current_cat != $entry->risk_category && $current_cat == NULL) OR ( $count == 0) ) { echo "<h2>" . $entry->risk_project_number . " " . $entry->risk_project_name . "</h2><form action='' method=\"post\"><table><tbody><tr><th>Risk</th><th>Level</th><th>Score</th><th>Added By</th><th>Date</th></tr><tr><td colspan=\"5\" class=\"category-heading\"><strong>" . $entry->risk_category . "</strong></td></tr>"; $current_cat = $entry->risk_category; $risk_project_number = $entry->risk_project_number; $risk_project_name = $entry->risk_project_name; }
				
				elseif ($current_cat != $entry->risk_category && $current_cat != NULL) { echo "<tr><td colspan=\"5\" class=\"category-heading\"><strong>" . $entry->risk_category . "</strong></td></tr>"; $current_cat = $entry->risk_category; }
				
				echo "<tr class=\"" . RiskManager_Resolved($entry->risk_resolved) . "\" id=\"rowhide_" . $entry->risk_id . "\"><td onmouseover=\"ShowRisk('risk_" . $entry->risk_id . "')\" onmouseout=\"HideRisk('risk_" . $entry->risk_id . "')\"><div class=\"risk-popup\" id=\"risk_" . $entry->risk_id . "\" onclick=\"EditRisk('" . $entry->risk_id . "')\"><p>" . $entry->risk_description . "</p><span class=\"risk-minitext\">Click to edit</span></div><a href=\"" . home_url( add_query_arg( array(), $wp->request ) ) . "?risk=" . $entry->risk_id . "\">" . $entry->risk_title . "</a></td><td><span class=\"" . RiskManager_Color($entry->risk_level,'level') . "\">" . ucwords($entry->risk_level) . "</span></td><td><span class=\"" . RiskManager_Color($entry->risk_score,'score') . "\">" . ucwords($entry->risk_score) . "</span></td><td>" . $entry->risk_user . "</td><td>" . RiskManager_DateFormat($entry->risk_date) . "</td></tr>";
				
			} elseif (!$risk_project) { echo "<h2>Add New Risk</h2><form action='' method=\"post\"><table><tbody><tr><th>Risk</th><th>Level</th><th>Score</th><th>Added By</th><th>Date</th></tr>"; }
			
			if ($entry->risk_id > 0) {
			
				$style = "row-hide";
				echo "<tr class=\"row-hide\" id=\"titleshow_f_" . $entry->risk_id . "\"><td colspan=\"5\" class=\"risk-heading\">" . $entry->risk_title . "</td></tr>";
				
			} else {
				
				$style = "row-show";
				echo "<tr><td colspan=\"5\" class=\"category-heading\">Add New</td></tr>";
				
			}
			
			if (!$entry->risk_user) { $risk_user = RiskManager_GetUserName(); } else { $risk_user = $entry->risk_user; }
			if (!$entry->risk_date) { $risk_date = date("Y-m-d",time()); } else { $risk_date = $entry->risk_date; }
			if ($entry->risk_resolved == 1) { $risk_resolved = "checked=\"checked\""; } else { unset($risk_resolved); }
			
			if ($entry->risk_id > 0) { $instruction = "Edit"; } else { $instruction = "Add"; }
			
			if (!$risk_project) {
				
					echo "<tr><td colspan=\"2\" class=\"risk-cell\"><span class=\"risk-edit-title\">Add project number</span><br /><input type=\"text\" name=\"risk_project_number[]\" class=\"risk-input\" required=\"required\" /></td><td colspan=\"3\" class=\"risk-cell\"><span class=\"risk-edit-title\">Add project name</span><input type=\"text\" name=\"risk_project_name[]\" class=\"risk-input\" required=\"required\" /></td></tr>";
					
				} else {
					
					echo "<input type=\"hidden\" name=\"risk_project_number[]\" value=\"" . $risk_project_number . "\" />";
					echo "<input type=\"hidden\" name=\"risk_project_name[]\" value=\"" . $risk_project_name . "\" />";
					
				}
				
				echo "
					<tr id=\"rowshow_" . $entry->risk_id . "\" class=\"" . $style . "\">
					<td class=\"risk-cell\"><input type=\"hidden\" value=\"" . $entry->risk_id . "\" name=\"risk_id[]\" /><span class=\"risk-edit-title\">" . $instruction . " title</span><br /><input type=\"text\" class=\"risk-input\" value=\"" . $entry->risk_title . "\" name=\"risk_title[]\" /></td>
					<td class=\"risk-cell\"><span class=\"risk-edit-title\">" . $instruction . " level</span><br />" . RiskManager_Select($entry->risk_level,'level') . "</td>
					<td class=\"risk-cell\"><span class=\"risk-edit-title\">" . $instruction . " score</span><br />" . RiskManager_Select($entry->risk_score,'score') . "</td>
					<td class=\"risk-cell\"><span class=\"risk-edit-title\">" . $instruction . " level</span><br /><input type=\"list\" name=\"risk_user[]\" list=\"name_list\" class=\"risk-input\" value=\"" . $risk_user . "\" /></td>
					<td class=\"risk-cell\"><span class=\"risk-edit-title\">" . $instruction . " date</span><br /><input type=\"date\" name=\"risk_date[]\" class=\"risk-input\" value=\"" . $risk_date . "\" /></td>
					</tr>";	
				
					
					
				echo "
					
					<tr id=\"detailshow_a_" . $entry->risk_id . "\" class=\"" . $style . "\">
					<td class=\"risk-cell\"><span class=\"risk-edit-title\">" . $instruction . " description</span><br /><textarea class=\"risk-textarea\" name=\"risk_description[]\">" . $entry->risk_description . "</textarea></td>
					<td class=\"risk-cell\" colspan=\"2\"><span class=\"risk-edit-title\">" . $instruction . " analysis</span><br /><textarea class=\"risk-textarea\" name=\"risk_analysis[]\">" . $entry->risk_analysis . "</textarea></td>
					<td class=\"risk-cell\" colspan=\"2\"><span class=\"risk-edit-title\">" . $instruction . " mitigation</span><br /><textarea class=\"risk-textarea\" name=\"risk_mitigation[]\">" . $entry->risk_mitigation . "</textarea></td>
					</tr>
					
					<tr id=\"detailshow_b_" . $entry->risk_id . "\" class=\"" . $style . "\">
					<td class=\"risk-cell\"><span class=\"risk-edit-title\">" . $instruction . " warning signs</span><br /><textarea class=\"risk-textarea\" name=\"risk_warnings[]\">" . $entry->risk_warnings . "</textarea></td>
					<td class=\"risk-cell\"><span class=\"risk-edit-title\">" . $instruction . " management strategy</span><br />" . RiskManager_Select($entry->risk_management,'management') . "</td>
					<td class=\"risk-cell\"><span class=\"risk-edit-title\">Risk eliminated?</span><br /><input type=\"checkbox\" name=\"risk_resolved[]\" value=\"1\" " .$risk_resolved . " /> Yes</td>
					<td class=\"risk-cell\"><span class=\"risk-edit-title\">" . $instruction . " category</span><br /><input type=\"list\" list=\"category_list\" name=\"risk_category[]\" class=\"risk-input\" value=\"" . $entry->risk_category . "\" /></td>
					<td class=\"risk-cell\"><span class=\"risk-edit-title\">" . $instruction . " responsbility</span><br /><input type=\"list\" name=\"risk_responsibility[]\" class=\"risk-input\" list=\"responsibility_list\" value=\"" . $entry->risk_responsibility . "\" /></td>
					</tr>
					<tr id=\"detailshow_g_" . $entry->risk_id . "\" class=\"" . $style . "\"><td class=\"risk-cell\"><span class=\"risk-edit-title\">" . $instruction . " drawing</span><br /><input type=\"text\" class=\"risk-input\" value=\"" . $entry->risk_drawing . "\" name=\"risk_drawing[]\" /></td><td colspan=\"5\" class=\"risk-cell\"></td></tr>
					";
					
			$count++;

		}
		
		echo "</tbody></table>";
		
		$risk_responsibility_list = RiskManager_GetResponsibilities($_GET['project']);
		$category_array = RiskManager_GetDatalistCategories(htmlentities($_GET['project']));
		
		echo "<datalist id=\"name_list\">" . RiskManager_PresentDatalistCategories(RiskManager_GetAllUserNames()) . "</datalist>";
		echo "<datalist id=\"category_list\">" . RiskManager_PresentDatalistCategories($category_array) . "</datalist>";
		echo "<datalist id=\"responsibility_list\">" . RiskManager_PresentDatalistCategories($risk_responsibility_list) . "</datalist>";
		
		echo "<input id=\"button-risk-update\" type=\"submit\" name=\"button-risk-update\" value=\"Update\" />";
		
		echo "</form>";
		
	}
	
}

function RiskManager_Select($value,$type) {
	
	if ($type == 'level') { $output = "<select name=\"risk_level[]\" class=\"risk-input\">"; $array = array("green","amber","red");  }
	elseif ($type == 'score') { $output = "<select name=\"risk_score[]\" class=\"risk-input\">"; $array = array("low","medium","high"); }
	elseif ($type == 'management') { $output = "<select name=\"risk_management[]\" class=\"risk-input\">"; $array = array(NULL, "eliminate","transfer","accept"); }
	
	foreach ($array AS $item) {
		
		if ($item == $value) { $selected = "selected=\"selected\""; } else { unset($selected); }
		
		$output = $output . "<option class=\"" . $type . "-" . $item . "\" value=\"" . $item . "\" " . $selected . ">" . ucwords($item) . "</option>";
		
	}	
	
	$output = $output . "</select>";
	
	return $output;
	
}

function RiskManager_GetUserName() {
	
	$user = wp_get_current_user();
	if (!$user->first_name OR !$user->last_name) { $username = $user->user_login; } else { $username = $user->first_name."&nbsp;".$user->last_name; }
	return $username;
	
}

function RiskManager_RiskView($risk_id) {
	
	
	
}

function RiskManagerPopUp() {

echo "

<script type=\"text/javascript\">
	
	function ShowRisk(id) {
	document.getElementById(id).style.display = 'block';
	}
		
	function HideRisk(id) {
	document.getElementById(id).style.display = 'none';
	}
	
	function EditRisk(id) {
	var_a = 'rowhide_' + id;
	var_b = 'rowshow_' + id;
	var_c = 'detailshow_a_' + id;
	var_d = 'detailshow_b_' + id;
	var_e = 'button-risk-update';
	var_f = 'titleshow_f_' + id;
	var_g = 'detailshow_g_' + id;
	document.getElementById(var_f).style.display = 'table-row';
	document.getElementById(var_e).style.display = 'block';
	document.getElementById(var_d).style.display = 'table-row';
	document.getElementById(var_c).style.display = 'table-row';
	document.getElementById(var_b).style.display = 'table-row';
	document.getElementById(var_g).style.display = 'table-row';
	document.getElementById(var_a).style.display = 'none';
	}
	

</script>

";
	
	
}

function RiskManager_ProjectList() {
// This function lists the projects which already have risk management entries

	global $wpdb;
	global $wp;

	$tablename = $wpdb->prefix."riskmanager";	
	$entriesList = $wpdb->get_results("SELECT COUNT(`risk_project_number`) AS `RiskNumber`,`risk_project_number`,`risk_project_name` FROM " . $tablename . " GROUP BY `risk_project_number` DESC");

	if(count($entriesList) > 0){
		
		echo "<p>List of active projects. Select one to view.</p>";
		
		echo "<table>";

		foreach( $entriesList as $entry ) {
			
			echo "<tr><td><a href=\"" . home_url( add_query_arg( array(), $wp->request ) ) . "?project=" . $entry->risk_project_number . "\">" . $entry->risk_project_number . "</a></td><td>" . $entry->risk_project_name . "</td><td>" . number_format($entry->RiskNumber) . "</td></tr>";
			
		}
		
		echo "</table>";

	} else {
		
		echo "<p>No entries found.</p>";
		
	}
	
	echo "<a href=\"" . home_url( add_query_arg( array(), $wp->request ) ) . "?project=new\"><button>Add New</button></a>";
	
}
