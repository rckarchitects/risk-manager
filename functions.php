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
	
	if ($_GET['project']) { RiskManager_ProjectView($_GET['project']); }
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

function RiskManager_Update() {
	
	echo "<pre>";
	print_r($_POST);
	echo "</pre>";
	
}

function RiskManager_Resolved($risk_resolved) {
	
	if ($risk_resolved == 1) { return 'risk-resolved'; }
	else { return 'risk-unresolved'; }	
	
}

function RiskManager_ProjectView($risk_project) {
// This function shows a list of risks associated with a selected project

if ($_POST['risk-update']) { RiskManager_Update(); }
	
	global $wpdb;
	global $wp;

	$tablename = $wpdb->prefix."riskmanager";
	
	$sql = "SELECT * FROM " . $tablename . " WHERE `risk_project_number` = '" . htmlspecialchars($risk_project) . "' ORDER BY `risk_category` ASC,`risk_date` ASC";
	
	$entriesList = $wpdb->get_results($sql);
	
	echo "<a href=\"" . home_url( add_query_arg( array(), $wp->request ) ) . "\">&larr;&nbsp;All Projects</a>";
	
	RiskManagerPopUp();

	
	if(count($entriesList) > 0){
		
		foreach( $entriesList as $entry ) {
			
			if ($current_cat != $entry->risk_category && $current_cat == NULL) { echo "<h2>" . $entry->risk_project_number . " " . $entry->risk_project_name . "</h2><form action='' method=\"post\"><table><tbody><tr><th>Risk</th><th>Level</th><th>Score</th><th>Added By</th><th>Date</th></tr><tr><td colspan=\"5\"><strong>" . $entry->risk_category . "</strong></td></tr>"; $current_cat = $entry->risk_category; }
			elseif ($current_cat != $entry->risk_category && $current_cat != NULL) { echo "<tr><td colspan=\"5\"><strong>" . $entry->risk_category . "</strong></td></tr>"; $current_cat = $entry->risk_category; }

			echo "<tr class=\"" . RiskManager_Resolved($entry->risk_resolved) . "\" id=\"rowhide_" . $entry->risk_id . "\"><td onmouseover=\"ShowRisk('risk_" . $entry->risk_id . "')\" onmouseout=\"HideRisk('risk_" . $entry->risk_id . "')\"><div class=\"risk-popup\" id=\"risk_" . $entry->risk_id . "\" onclick=\"EditRisk('" . $entry->risk_id . "')\"><p>" . $entry->risk_description . "</p><span class=\"risk-minitext\">Click to edit</span></div><a href=\"" . home_url( add_query_arg( array(), $wp->request ) ) . "?risk=" . $entry->risk_id . "\">" . $entry->risk_title . "</a></td><td><span class=\"" . RiskManager_Color($entry->risk_level,'level') . "\">" . ucwords($entry->risk_level) . "</span></td><td><span class=\"" . RiskManager_Color($entry->risk_score,'score') . "\">" . ucwords($entry->risk_score) . "</span></td><td>" . $entry->risk_user . "</td><td>" . $entry->risk_date . "</td></tr>";
			
			echo "	<tr id=\"rowshow_" . $entry->risk_id . "\" class=\"row-hide\">
					
					<td class=\"risk-cell\"><input type=\"hidden\" value=\"" . $entry->risk_id . "\" name=\"risk_id[]\" /><input type=\"text\" class=\"risk-input\" value=\"" . $entry->risk_title . "\" name=\"risk_title[]\" /></td>
					<td class=\"risk-cell\">" . RiskManager_Select($entry->risk_score,'level') . "</td>
					<td class=\"risk-cell\">" . RiskManager_Select($entry->risk_score,'score') . "</td>
					<td class=\"risk-cell\"><input type=\"text\" name=\"risk_user[]\" class=\"risk-input\" value=\"" . RiskManagerGetUserName() . "\" /></td>
					<td class=\"risk-cell\"><input type=\"date\" name=\"risk_date[]\" class=\"risk-input\" value=\"" . date ("Y-m-d",time()) . "\" /></td>
					</tr>
					
					<tr id=\"detailshow_a_" . $entry->risk_id . "\" class=\"row-hide\">
					<td class=\"risk-cell\"><span class=\"risk-edit-title\">Edit description</span><br /><textarea class=\"risk-textarea\" name=\"risk_description[]\">" . $entry->risk_description . "</textarea></td>
					<td class=\"risk-cell\" colspan=\"2\"><span class=\"risk-edit-title\">Edit analysis</span><br /><textarea class=\"risk-textarea\" name=\"risk_analysis[]\">" . $entry->risk_analysis . "</textarea></td>
					<td class=\"risk-cell\" colspan=\"2\"><span class=\"risk-edit-title\">Edit mitigation</span><br /><textarea class=\"risk-textarea\" name=\"risk_mitigation[]\">" . $entry->risk_mitigation . "</textarea></td>
					</tr>
					
					<tr id=\"detailshow_b_" . $entry->risk_id . "\" class=\"row-hide\">
					<td class=\"risk-cell\"><span class=\"risk-edit-title\">Edit warning signs</span><br /><textarea class=\"risk-textarea\" name=\"risk_warning[]\">" . $entry->risk_warning . "</textarea></td>
					<td class=\"risk-cell\"><span class=\"risk-edit-title\">Edit management strategy</span><br />" . RiskManager_Select($entry->risk_management,'management') . "</td>
					<td class=\"risk-cell\"><input type=\"checkbox\" name=\"risk_eliminated[]\" value=\"1\" /> Risk eliminated?</td>
					<td class=\"risk-cell\"><input type=\"text\" name=\"risk_category[]\" class=\"risk-input\" value=\"" . $entry->risk_category . "\" /></td>
					<td class=\"risk-cell\"><input type=\"text\" name=\"risk_responsibility[]\" class=\"risk-input\" value=\"" . $entry->risk_responsibility . "\" /></td>
					<td class=\"risk-cell\"></td>
					</tr>
					
					";

		}
		
		echo "</tbody></table><input id=\"button-risk-update\" style=\"display: none;\" type=\"submit\" value=\"Update\" /></form>";
		
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

function RiskManagerGetUserName() {
	
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
	document.getElementById(var_e).style.display = 'block';
	document.getElementById(var_d).style.display = 'table-row';
	document.getElementById(var_c).style.display = 'table-row';
	document.getElementById(var_b).style.display = 'table-row';
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
	
}
