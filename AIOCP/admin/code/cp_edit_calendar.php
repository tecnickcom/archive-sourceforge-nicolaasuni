<?php
//============================================================+
// File name   : cp_edit_calendar.php                          
// Begin       : 2001-09-29                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit calendar                                 
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

require_once('../../shared/config/cp_extension.inc');
require_once('../config/cp_config.'.CP_EXT);

$pagelevel = K_AUTH_ADMIN_CP_EDIT_CALENDAR;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

require_once('../../shared/code/cp_functions_calendar.'.CP_EXT);
require_once('../../shared/code/cp_functions_form.'.CP_EXT);
require_once('../../shared/code/cp_functions_tree.'.CP_EXT);
	
$thispage_title = $l['t_calendar_editor'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages;

if (isset($_REQUEST['menu_mode'])) {
	$menu_mode = $_REQUEST['menu_mode'];
} else {
	$menu_mode = "";
}
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
		$sql = "DELETE FROM ".K_TABLE_CALENDAR." WHERE calendar_id=".$calendar_id."";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		break;
	}
	
	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($calendar_id) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_CALENDAR, "(calendar_category_id='".$calendar_category_id."' AND calendar_year='".$year."' AND calendar_month='".$month."' AND calendar_day='".$day."' AND calendar_name='".$calendar_name."')", "calendar_id", $calendar_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$calendar_name = addslashes(serialize($c_name));
				$calendar_text = addslashes(serialize($c_text));
				$sql = "UPDATE IGNORE ".K_TABLE_CALENDAR." SET 
				calendar_name='".$calendar_name."',
				calendar_text='".$calendar_text."' 
				WHERE calendar_id=".$calendar_id."";
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
		$sql = "SELECT calendar_id FROM ".K_TABLE_CALENDAR." WHERE (calendar_category_id='".$calendar_category_id."' AND calendar_year='".$year."' AND calendar_month='".$month."' AND calendar_day='".$day."' AND calendar_name='".$calendar_name."')";
		if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_calendar']);
		}
		else { //add item
			$calendar_name = addslashes(serialize($c_name));
			$calendar_text = addslashes(serialize($c_text));
			$sql = "INSERT IGNORE INTO ".K_TABLE_CALENDAR." (
			calendar_category_id,
			calendar_year, 
			calendar_month, 
			calendar_day, 
			calendar_name,
			calendar_text
			) VALUES (
			'".$calendar_category_id."',
			'".$year."',
			'".$month."',
			'".$day."',
			'".$calendar_name."',
			'".$calendar_text."')";
			if(!$r = F_aiocpdb_query($sql, $db))  {
				F_display_db_error();
			}
			else {
				$calendar_id = F_aiocpdb_insert_id();
			}
		}
		break;
	}
	
	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$c_name = array();
		$c_text = array();
		break;
	}
		
	default :{ 
		break;
	}
}//end of switch

// Initialize variables
if(!isset($calendar_category_id) OR (!$calendar_category_id)) {
	$sql = "SELECT * FROM ".K_TABLE_CALENDAR_CATEGORIES." ORDER BY calcat_sub_id,calcat_position LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$calendar_category_id = $m['calcat_id'];
		}
		else {
			$calendar_category_id = false;
		}
	}
	else {
		F_display_db_error();
	}
}


	if(!isset($year) OR empty($year)) {$year = gmdate("Y");} // year 4 digits
	if(!isset($month) OR empty($month)) {$month = gmdate("m");} //month (0-12)
	if(!isset($day) OR empty($day)) {$day = gmdate("d");} //day (0-31)
	if(!isset($weekday) OR empty($weekday)) {$weekday = gmdate("w");} //weekday (0=Sunday, ..., 6=Saturday)
	
	$lastmonthday = F_get_last_day($month,$year); //last day of month (1-31)
	$selected_days = Array();
	
	$c_name = array(); //reset name variable
	$c_text  = array(); //reset text variable
	
	//check non-empty days in current month
	for ($i=1; $i<=$lastmonthday; $i++) { //for each day in month
		$sql = "SELECT * FROM ".K_TABLE_CALENDAR." WHERE (calendar_category_id='".$calendar_category_id."' AND calendar_year='".$year."' AND calendar_month='".$month."' AND calendar_day='".$i."') LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$temp_name = $m['calendar_name']; 
				$temp_name = unserialize($temp_name);
				$this_name = stripslashes($temp_name[$selected_language]);
				if(strlen($this_name)>0) {
					$selected_days[] = $i; //record days with activities
				}
			}
		}
		else {
			F_display_db_error();
		}
	}
	
	
// Get current event
if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if( (isset($changecategory) AND $changecategory)
		OR (isset($changeyear) AND $changeyear)
		OR (isset($changemonth) AND $changemonth)
		OR (isset($changeday) AND $changeday)
		OR ((!isset($calendar_id)) OR (!$calendar_id))
		) {
			$sql = "SELECT * FROM ".K_TABLE_CALENDAR." WHERE (calendar_category_id='".$calendar_category_id."' AND calendar_year='".$year."' AND calendar_month='".$month."' AND calendar_day='".$day."') LIMIT 1";
		}
		else {
			$sql = "SELECT * FROM ".K_TABLE_CALENDAR." WHERE calendar_id=".$calendar_id." LIMIT 1";
		}
		
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$calendar_id = $m['calendar_id'];
				$calendar_year = $m['calendar_year'];
				$calendar_month = $m['calendar_month'];
				$calendar_day = $m['calendar_day'];
				$c_name = unserialize($m['calendar_name']);
				$c_text = unserialize($m['calendar_text']);
			}
		}
		else {
			F_display_db_error();
		}
	}
}
	// --------------------------------------------------
?>

<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_calendar" id="form_calendar">

<?php F_print_calendar_form($day, $month, $year, $selected_days); ?>

<br />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_date', ''); ?></b></td>
<td class="fillEE">
<?php echo "".$year."-".$month."-".$day.""; ?>
</td>
</tr>
<!-- SELECT category ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_category', 'h_calendarcat_select'); ?></b></td>
<td class="fillOE">
<input type="hidden" name="changecategory" id="changecategory" value="0" />
<select name="calendar_category_id" id="calendar_category_id" size="0" onchange="document.form_calendar.changecategory.value=1; document.form_calendar.submit()">
<?php
$noscriptlink = $_SERVER['SCRIPT_NAME']."?";
if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {
	$noscriptlink .= "aiocp_dp=".$aiocp_dp."&amp;";
}
$noscriptlink .= "changecategory=1&amp;";
$noscriptlink .= "calendar_category_id=";
F_form_select_tree($calendar_category_id, false, K_TABLE_CALENDAR_CATEGORIES, "calcat", $noscriptlink);
?>
</td>
</tr>
<!-- END SELECT category ==================== -->

<!-- SELECT links ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_event', 'h_calendared_select'); ?></b></td>
<td class="fillEE">
<select name="calendar_id" id="calendar_id" size="0" onchange="document.form_calendar.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_CALENDAR." WHERE (calendar_category_id='".$calendar_category_id."' AND calendar_year='".$year."' AND calendar_month='".$month."' AND calendar_day='".$day."') ORDER BY calendar_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		$temp_name = $m['calendar_name']; 
		$temp_name = unserialize($temp_name);
		$this_name = stripslashes($temp_name[$selected_language]);
		echo "<option value=\"".$m['calendar_id']."\"";
		if($m['calendar_id'] == $calendar_id) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($this_name, ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT links ==================== -->

<tr class="fillO">
<td class="fillOO">&nbsp;</td>
<td class="fillOE"><hr /></td>
</tr>

<!-- iterate for each language ==================== -->
<?php
	$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			
			$doc_charset = F_word_language($m['language_code'], "a_meta_charset");
			if ($doc_charset) {
				$doc_charset_url = "&amp;charset=".$doc_charset;
			}
			else {
				$doc_charset_url = "";
			}
			
			echo "<tr class=\"fillO\">";
			echo "<td class=\"fillOO\"><hr /></td>";
			echo "<td class=\"fillOE\"><b>".$m['language_name']."</b></td>";
			echo "</tr>";
			
			echo "<tr class=\"fillE\">";
			echo "<td class=\"fillEO\" align=\"right\"><b>".F_display_field_name('w_name', 'h_calendar_name')."</b></td>";
			
			if (!isset($c_name[$m['language_code']])) {
				$c_name[$m['language_code']] = "";
			}
			
			echo "<td class=\"fillEE\"><input type=\"text\" name=\"c_name[".$m['language_code']."]\" id=\"c_name_".$m['language_code']."\" value=\"".htmlentities(stripslashes($c_name[$m['language_code']]), ENT_COMPAT, $doc_charset)."\" size=\"50\" maxlength=\"255\" /></td>";
			echo "</tr>";
			
			echo "<tr class=\"fillO\">";
			echo "<td class=\"fillOO\" align=\"right\" valign=\"top\"><b>".F_display_field_name('w_description', '')."</b><br />";
?>
<input type="button" name="htmleditor_<?php echo $m['language_code']; ?>" id="htmleditor_<?php echo $m['language_code']; ?>" value="<?php echo $l['w_button_html_editor']; ?>" onclick="htmlWindow=window.open('cp_edit_html.<?php echo CP_EXT; ?>?templates=page&amp;callingform=form_calendar&amp;callingfield=elements['+FJ_get_field_index(this)+']<?php echo $doc_charset_url; ?>','htmlWindow','dependent,height=500,width=800,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')" />
<?php
echo "</td>";
if (isset($c_text[$m['language_code']])) {
	$current_ta_code = $c_text[$m['language_code']];
} else {
	$current_ta_code = "";
}

$current_ta_code = stripslashes($current_ta_code);
echo "<td class=\"fillOE\"><textarea cols=\"50\" rows=\"5\" name=\"c_text[".$m['language_code']."]\" id=\"c_text_".$m['language_code']."\">".htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset)."</textarea></td>";
echo "</tr>";
		}
	}
	else {
		F_display_db_error();
	}
?>
<!-- END iterate for each language ==================== -->
</table>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="day" id="day" value="<?php echo $day; ?>" />
<?php //show buttons
if(isset($calendar_id) AND ($calendar_id > 0)) {
	F_submit_button("form_calendar","menu_mode",$l['w_update']);
	F_submit_button("form_calendar","menu_mode",$l['w_delete']); 
}
F_submit_button("form_calendar","menu_mode",$l['w_add']); 
F_submit_button("form_calendar","menu_mode",$l['w_clear']); 
?>
</td>
</tr>

</table>
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
</form>

<script language="JavaScript" type="text/javascript">
//<![CDATA[
//get next elements ID
function FJ_get_field_index(what) {
	for (var i=0;i<document.form_calendar.elements.length;i++) {
		if(what == document.form_calendar.elements[i]) {
			return(i+1);
		}
	}
	return (-1);
}
//]]>
</script>

<?php
require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
