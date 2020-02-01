<?php
//============================================================+
// File name   : cp_functions_user_agenda.php                  
// Begin       : 2001-09-29                                    
// Last Update : 2008-07-06
//                                                             
// Description : Functions for personal agenda (UTC time)      
//                                                             
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Tecnick.com LTD
//               Manor Coach House, Church Hill
//               Aldershot, Hants, GU12 4RQ
//               UK
//               www.tecnick.com
//               info@tecnick.com
//
// License: GNU GENERAL PUBLIC LICENSE v.2
//          http://www.gnu.org/copyleft/gpl.html
//============================================================+

// ------------------------------------------------------------
// Show calendar
// ------------------------------------------------------------
function F_show_user_agenda() {
	global $l, $db, $aiocp_dp;
	global $day, $month, $year, $menu_mode, $uagenda_id, $uagenda_text;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_calendar.'.CP_EXT);
	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	
	$userid = $_SESSION['session_user_id'];
	
// -- encrease/decrease year
switch($menu_mode) {
	case "-": {
		$year--;
		break;
	}
	case "+": {
		$year++;
		break;
	}
	case unhtmlentities($l['w_delete']):
	case $l['w_delete']:{
		F_stripslashes_formfields(); // Delete
		$sql = "DELETE FROM ".K_TABLE_USER_AGENDA." WHERE uagenda_id=".$uagenda_id."";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		break;
	}
	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($uagenda_id) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_USER_AGENDA, "(uagenda_userid=".$userid." AND uagenda_year=".$year." AND uagenda_month=".$month." AND uagenda_day=".$day.")", "uagenda_id", $uagenda_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$sql = "UPDATE IGNORE ".K_TABLE_USER_AGENDA." SET uagenda_text='".$uagenda_text."' WHERE uagenda_id=".$uagenda_id."";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
			}
		}
		break;
	}
	case unhtmlentities($l['w_add']):
	case $l['w_add']:{ // Add
		//check if name is unique
		$sql = "SELECT uagenda_id FROM ".K_TABLE_USER_AGENDA." WHERE (uagenda_userid=".$userid." AND uagenda_year=".$year." AND uagenda_month=".$month." AND uagenda_day=".$day.")";
		if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_calendar']);
		}
		else { //add item
			$sql = "INSERT IGNORE INTO ".K_TABLE_USER_AGENDA." (
			uagenda_userid, 
			uagenda_year, 
			uagenda_month, 
			uagenda_day, 
			uagenda_text
			) VALUES (
			'".$userid."', 
			'".$year."', 
			'".$month."', 
			'".$day."', 
			'".$uagenda_text."')";
			if(!$r = F_aiocpdb_query($sql, $db))  {
				F_display_db_error();
			}
			else {
				$uagenda_id = F_aiocpdb_insert_id();
			}
		}
		break;
	}
	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$uagenda_text = "";
		break;
		}
	default :{ 
		break;
	}
}//end of switch

	// Initialize variables:
	if(!isset($year)) {$year = gmdate("Y");} // year 4 digits
	if(!isset($month)) {$month = gmdate("m");} //month (0-12)
	if(!isset($day)) {$day = gmdate("d");} //day (0-31)
	if(!isset($weekday)) {$weekday = gmdate("w");} //weekday (0=Sunday, ..., 6=Saturday)
	
	$lastmonthday = F_get_last_day($month,$year); //last day of month (1-31)
	$selected_days = Array();
	
	for ($i=1; $i<=$lastmonthday; $i++) { //for each day in month
		$sql = "SELECT * FROM ".K_TABLE_USER_AGENDA." WHERE (uagenda_userid=".$userid." AND uagenda_year=".$year." AND uagenda_month=".$month." AND uagenda_day=".$i.") LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				if ($i == $day) {
					$today_notes = $m['uagenda_text'];
					$uagenda_id = $m['uagenda_id'];
				}
				if (isset($m['calendar_text']) AND strlen($m['calendar_text'])>0) {
					$selected_days[] = $i; //record days with activities
				}
			}
		}
		else {
			F_display_db_error();
		}
	}
	
	
	$lastmonthday = F_get_last_day($month,$year); //last day of month (1-31)
	$selected_days = Array();
	
	$today_notes = ""; //reset text variable
	
	for ($i=1; $i<=$lastmonthday; $i++) { //for each day in month
		$sql = "SELECT * FROM ".K_TABLE_USER_AGENDA." WHERE (uagenda_userid=".$userid." AND uagenda_year=".$year." AND uagenda_month=".$month." AND uagenda_day=".$i.") LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$temp_text = $m['uagenda_text']; 
				if ($i == $day) {
					$today_notes = $temp_text;
					$uagenda_id = $m['uagenda_id'];
				}
				if(strlen($temp_text)>0) {
					$selected_days[] = $i; //record days with activities
				}
			}
		}
		else {
			F_display_db_error();
		}
	}
?>

<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_calendar" id="form_calendar">

<table border="0" cellspacing="4" cellpadding="0">
<tr valign="top">
<td valign="top">
<?php F_print_calendar_form($day, $month, $year, $selected_days); ?>
</td>
<td>

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">
<tr class="fill">
<td class="fill">
<textarea cols="35" rows="8" name="uagenda_text" id="uagenda_text">
<?php echo htmlentities($today_notes, ENT_NOQUOTES, $l['a_meta_charset']); ?>
</textarea>
</td>
</tr>
</table>

</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="day" id="day" value="<?php echo $day; ?>" />
<input type="hidden" name="uagenda_id" id="uagenda_id" value="<?php echo $uagenda_id; ?>" />

<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if($uagenda_id) {
	F_submit_button("form_calendar","menu_mode",$l['w_update']);
	F_submit_button("form_calendar","menu_mode",$l['w_delete']); 
}
F_submit_button("form_calendar","menu_mode",$l['w_add']); 
F_submit_button("form_calendar","menu_mode",$l['w_clear']); 
?>
</td>
</tr>
</table>

</td>
</tr>
</table>

</form>
<?php
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
