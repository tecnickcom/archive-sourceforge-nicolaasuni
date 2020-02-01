<?php
//============================================================+
// File name   : cp_functions_calendar.php
// Begin       : 2001-09-29
// Last Update : 2008-07-06
// 
// Description : Functions for calendar (UTC time)
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
// Display News
// $viewmode: 0=compact(headers only); 1=full 
// $selectedevent = news to display in full mode while in compact mode
// ------------------------------------------------------------
function F_show_calendar_events($calendar_category_id, $viewmode, $selectedevent, $wherequery) {
	global $l, $db, $selected_language;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_page.'.CP_EXT);
	require_once('../../shared/code/cp_functions_dynamic_pages.'.CP_EXT);
	
	//initialize variable
	$userlevel = $_SESSION['session_user_level'];
	
	if(!F_count_rows(K_TABLE_CALENDAR)) { //if the table is void (no items)
		return FALSE;
	}
	
	echo "<table class=\"edge\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\" style=\"width:100%\">";
	
	// --- ------------------------------------------------------
	
	if (isset($calendar_category_id) AND ($calendar_category_id==0)) { //select all categories
		$wherequery = "WHERE 1";
	}
	
	if( (!$calendar_category_id) AND (!$wherequery) ) {
		$sql = "SELECT * FROM ".K_TABLE_CALENDAR_CATEGORIES." WHERE (calcat_level<=".$userlevel." AND calcat_language='".$selected_language."') ORDER BY calcat_sub_id,calcat_position LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$calendar_category_id = $m['calcat_id'];
			}
			else {
				echo "<tr><td></td></tr></table>\n";
				F_print_error("WARNING", $l['m_authorization_deny']);
				//F_logout_form();
				return;
			}
		}
		else {
			F_display_db_error();
		}
	}

	if($calendar_category_id) {
		$sqlc = "SELECT * FROM ".K_TABLE_CALENDAR_CATEGORIES." WHERE calcat_id=".$calendar_category_id."";
		if($rc = F_aiocpdb_query($sqlc, $db)) {
			if($mc = F_aiocpdb_fetch_array($rc)) {
				if($mc['calcat_level']>$userlevel) {
					echo "<tr><td></td></tr></table>\n";
					F_print_error("WARNING", $l['m_authorization_deny']);
					//F_logout_form();
					return;
				}
				
				echo "<tr class=\"edge\">";
				echo "<th class=\"edge\" align=\"left\">";
				$this_desc = unserialize($mc['calcat_description']);
				$this_desc = stripslashes($this_desc[$selected_language]);
				//$this_desc = "".htmlentities($this_desc, ENT_NOQUOTES, $l['a_meta_charset'])."";
				echo F_evaluate_modules($this_desc);
				echo "</td></tr>";
				
				if (!$wherequery) {$wherequery = "WHERE (calendar_category_id='".$calendar_category_id."')";}
				else {$wherequery .= " AND (calendar_category_id='".$calendar_category_id."')";}
			}
		}
		else {
			F_display_db_error();
		}
	}
	
	if (!$wherequery) {
		$sql = "SELECT * FROM ".K_TABLE_CALENDAR." ORDER BY calendar_name";
	}
	else {
		$sql = "SELECT * FROM ".K_TABLE_CALENDAR." ".$wherequery." ORDER BY calendar_name";
	}
	
	if($r = F_aiocpdb_query($sql, $db)) {
		echo "<tr class=\"edge\">";
		echo "<td class=\"edge\">";
		echo "<table class=\"fill\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\" style=\"width:100%\">";
		while($m = F_aiocpdb_fetch_array($r)) {
			
			//change style for each row
			if (isset($rowodd) AND ($rowodd)) {
				$rowclass = "O";
				$rowodd = 0;
			} else {
				$rowclass = "E";
				$rowodd = 1;
			}
			
			//get category data
			if (!$calendar_category_id) {$catdata = F_get_calendar_category_data($m['calendar_category']);}
			//check authorization rights
			if (($calendar_category_id) OR ($userlevel >= $catdata->level)) {
				
				$this_name = unserialize($m['calendar_name']);
				$this_name = stripslashes($this_name[$selected_language]);
				$this_name = "".htmlentities($this_name, ENT_NOQUOTES, $l['a_meta_charset'])."";
				
				if(($viewmode)OR($m['calendar_id'] == $selectedevent)) { //full mode	
					echo "<tr class=\"fill".$rowclass."\"><td class=\"fill".$rowclass."E\">";
					
					echo "<table class=\"edge\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\" style=\"width:100%\">";
					echo "<tr class=\"edge\">";
					echo "<th class=\"edge\" align=\"left\">";
					echo "".$this_name."";
					echo "</td></tr>";
					echo "<tr class=\"edge\">";
					echo "<td class=\"edge\">";
					echo "<table class=\"fill\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\" style=\"width:100%\">";
					$this_text = unserialize($m['calendar_text']);
					$this_text = stripslashes($this_text[$selected_language]);
					echo "<tr class=\"fillE\"><td class=\"fillEE\">".F_evaluate_modules($this_text)."</td></tr>";
					echo "</table>";
					echo "</td></tr></table>";
					echo "</td></tr>";
				}
				else { //compact mode
					echo "<tr class=\"fill".$rowclass."\"><td class=\"fill".$rowclass."E\">";
					echo "<a href=\"javascript:FJ_submit_calendar_form('".$m['calendar_category_id']."','".$m['calendar_id']."');\">";
					echo "".$this_name."";
					echo "</a>";
					echo "</td></tr>";
				}
			}
		} //end of while
		echo "</table>";
		echo "</td></tr>";
	}
	else {
		F_display_db_error();
	}
	echo "</table>";
	
	// Display RSS icon Link
	//$url_request = "wherequery=".urlencode($wherequery)."";
	$url_request = "x=x";
	if ($calendar_category_id) {
		$url_request .= "&amp;calendar_category=".$calendar_category_id."";
	}
	
 return TRUE;
}


// ------------------------------------------------------------
// Show calendar
// ------------------------------------------------------------
function F_show_calendar() {
	global $l, $db, $aiocp_dp, $selected_language;
	global $day, $month, $year, $menu_mode, $calendar_category_id, $calendar_id, $c_text, $cname;
	global $viewmode, $selectedevent, $wherequery;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	require_once('../../shared/code/cp_functions_dynamic_pages.'.CP_EXT);
	require_once('../../shared/code/cp_functions_tree.'.CP_EXT);
	
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
		default :{ 
			break;
		}
	}//end of switch
	
	// Initialize variables:

	if(!$calendar_category_id) {
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
		$calendar_id = false;
	}


	if(!isset($year)) {$year = gmdate("Y");} // year 4 digits
	if(!isset($month)) {$month = gmdate("m");} //month (0-12)
	if(!isset($day)) {$day = gmdate("d");} //day (0-31)
	if(!isset($weekday)) {$weekday = gmdate("w");} //weekday (0=Sunday, ..., 6=Saturday)
	
	$lastmonthday = F_get_last_day($month,$year); //last day of month (1-31)
	$selected_days = Array();
	
	// reset variables
	$c_name = array();
	$c_text = array();
	
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
	
	// -------------------------------------------------
?>

<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_calendar" id="form_calendar">

<?php F_print_calendar_form($day, $month, $year, $selected_days); ?>

<input type="hidden" name="day" id="day" value="<?php echo $day; ?>" />
<input type="hidden" name="calendar_id" id="calendar_id" value="<?php echo $calendar_id; ?>" />
<input type="hidden" name="selectedevent" id="selectedevent" value="0" />
<input type="hidden" name="submitted" id="submitted" value="0" />
<input type="hidden" name="menu_mode" id="menu_mode" value="" />


<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT category ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_category', 'h_calendarcat_select'); ?></b></td>
<td class="fillEE">
<input type="hidden" name="changecategory" id="changecategory" value="0" />
<select name="calendar_category_id" id="calendar_category_id" size="0" onchange="document.form_calendar.changecategory.value=1; document.form_calendar.submit()">
<?php
$noscriptlink = $_SERVER['SCRIPT_NAME']."?";
if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {
	$noscriptlink .= "aiocp_dp=".$aiocp_dp."&amp;";
}
if (!empty($viewmode)) {$noscriptlink .= "viewmode=".$viewmode."&amp;";}
if (!empty($order_field)) {$noscriptlink .= "order_field=".urlencode($order_field)."&amp;";}
if (!empty($orderdir)) {$noscriptlink .= "orderdir=".$orderdir."&amp;";}
$noscriptlink .= "calendar_category_id=";
F_form_select_tree($calendar_category_id, false, K_TABLE_CALENDAR_CATEGORIES, "calcat", $noscriptlink);
?>
</td>
</tr>
<!-- END SELECT category ==================== -->

<!-- SELECT view mode ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_mode', 'h_list_mode'); ?></b></td>
<td class="fillOE">
<select name="viewmode" id="viewmode" size="0" onchange="document.form_calendar.selectedevent.value=''; document.form_calendar.submit()">
<?php
if(!$viewmode) {
echo "<option value=\"0\" selected=\"selected\">".$l['w_compact']."</option>";
echo "<option value=\"1\">".$l['w_full']."</option>";
}
else {
echo "<option value=\"0\">".$l['w_compact']."</option>";
echo "<option value=\"1\" selected=\"selected\">".$l['w_full']."</option>";
}
?> 
</select>
<noscript>
<ul>
<?php
$noscriptlink = $_SERVER['SCRIPT_NAME']."?";
if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {
	$noscriptlink .= "aiocp_dp=".$aiocp_dp."&amp;";
}
echo "<li><a href=\"".$noscriptlink."viewmode=0\">".$l['w_compact']."</a></li>\n";
echo "<li><a href=\"".$noscriptlink."viewmode=1\">".$l['w_full']."</a></li>\n";
?>
</ul>
</noscript>
</td>
</tr>
</table>

<tr class="edge">
<td class="edge">

<?php
$wherequery = "WHERE calendar_year='".$year."' AND calendar_month='".$month."' AND calendar_day='".$day."'";
F_show_calendar_events($calendar_category_id, $viewmode, $selectedevent, $wherequery);
?>

</td>
</tr>
</table>
</form>

<script language="JavaScript" type="text/javascript">
//<![CDATA[
//get next elements ID
function getFieldIndex(what) {
	for (var i=0;i<document.form_calendar.elements.length;i++) {
		if(what == document.form_calendar.elements[i]) {
			return(i+1);
		}
	}
	return (-1);
}

function FJ_submit_calendar_form(calendarcategory, eventselected) {
	//document.form_calendar.calendar_category_id.value=calendarcategory;
	document.form_calendar.selectedevent.value=eventselected;
	document.form_calendar.submitted.value=1;
	document.form_calendar.submit();
}
//]]>
</script>


<?php
}



// ------------------------------------------------------------
// print a calendar form
// ------------------------------------------------------------
function F_print_calendar_form($day, $month, $year, $selected_days=NULL) {
	global $l;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	$l_day_names = array($l['w_sunday'], $l['w_monday'], $l['w_tuesday'], $l['w_wednesday'], $l['w_thursday'], $l['w_friday'], $l['w_saturday']);
	$l_month_names = array($l['w_january'], $l['w_february'], $l['w_march'], $l['w_april'], $l['w_may'], $l['w_june'], $l['w_july'], $l['w_august'], $l['w_september'], $l['w_october'], $l['w_november'], $l['w_december']);
	
	$firstweekday = date("w",mktime(0,0,0,$month,1,$year)); // get the first day of the week (0-6)
	$lastmonthday = F_get_last_day($month,$year); //last day of month (1-31)
	
?>

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<tr class="fill">
<td class="fill">
<!-- SELECT MONTH -->
<input type="hidden" name="changemonth" id="changemonth" value="0" />
<select name="month" id="month" size="0" onchange="document.form_calendar.changemonth.value=1; document.form_calendar.submit()">
<?php
for ($smonth=1; $smonth<=12; $smonth++) {
	echo "<option value=\"".$smonth."\"";
	if($smonth == $month) {
		echo " selected=\"selected\"";
	}
	echo ">".$smonth." [".$l_month_names[$smonth-1]."]</option>\n";
}
?>
</select>
<!-- END SELECT MONTH -->

<!-- SELECT YEAR -->
<input type="hidden" name="changeyear" id="changeyear" value="0" />
<?php F_submit_button("form_calendar","menu_mode","-"); ?><input type="text" name="year" id="year" value="<?php echo $year; ?>" size="6" maxlength="4" onchange="document.form_calendar.changeyear.value=1; document.form_calendar.submit()" /><?php F_submit_button("form_calendar","menu_mode","+"); ?>
<!-- END SELECT YEAR -->
</td>
</tr>
<tr class="fill">
<td class="fill">
<input type="hidden" name="changeday" id="changeday" value="0" />
<!-- SHOW WEEKDAYS -->
<table class="fill" border="0" cellspacing="1" cellpadding="1"  style="width:100%">
<tr class="fill">
<?php
	// write day names
	for ($i=0; $i<7; $i++) {
		//change style for each row
		if (isset($rowodd) AND ($rowodd)) {
			$rowclass = "O";
			$rowodd=0;
		} else {
			$rowclass = "E";
			$rowodd=1;
		}
		echo "<th class=\"fill".$rowclass."\"><b class=\"fixed\">".substr($l_day_names[$i],0,3)."</b></th>";
	}
?>
</tr>


<?php
$rowodd = 1; $rowclass = "O";
$colodd = 1; $colclass = "O";

	echo "<tr class=\"fillO\">";
	// write blank days
	for ($i=0; $i<$firstweekday; $i++) {
		//change style for each column
		if($colodd) {$colclass = "O"; $colodd=0;}
		else {$colclass = "E"; $colodd=1;}
		
		echo "<td class=\"fillE".$rowclass.$colclass."\">&nbsp;</td>";
	}
	// write days
	for ($i=1; $i<=$lastmonthday; $i++) {
		
		//change style for each column
		if($colodd) {$colclass = "O"; $colodd=0;}
		else {$colclass = "E"; $colodd=1;}
		
		$thiswday = date("w",mktime(0,0,0,$month,$i,$year));
		if(!$thiswday) { //create new row
			$colodd = 0;
			$colclass = "O";
			//change row style
			if (isset($rowodd) AND ($rowodd)) {
				$rowclass = "E";
				$rowodd = 0;
			} else {
				$rowclass = "O";
				$rowodd = 1;
			}
			echo "</tr><tr class=\"fill".$rowclass.$colclass."\">";
			
		}
		if ($i == $day) {
			echo "<td class=\"edge\" align=\"right\"><a class=\"edge\" href=\"javascript:document.form_calendar.changeday.value=1;document.form_calendar.day.value=".$i.";document.form_calendar.submit()\"><b>".$i."</b></a></td>";
		}
		elseif (is_array($selected_days) AND in_array($i, $selected_days)) {
			echo "<td class=\"edge\" align=\"right\"><a class=\"edge\" href=\"javascript:document.form_calendar.changeday.value=1;document.form_calendar.day.value=".$i.";document.form_calendar.submit()\">".$i."</a></td>";
		}
		else {
			echo "<td class=\"fill".$rowclass.$colclass."\" align=\"right\"><a href=\"javascript:document.form_calendar.changeday.value=1;document.form_calendar.day.value=".$i.";document.form_calendar.submit()\">".$i."</a></td>";;
			//use bold for selected days 
		}
	}
	
	//draw last blank days
	for ($i = $thiswday+1; $i<7; $i++) {
		//change style for each column
		if($colodd) {$colclass = "O"; $colodd=0;}
		else {$colclass = "E"; $colodd=1;}
		
		echo "<td class=\"fill".$rowclass.$colclass."\">&nbsp;</td>";
	}
?>
</tr>
</table>
<!-- END SHOW WEEKDAYS -->

</td>
</tr>
</table>

</td>
</tr>
</table>

<?php
return TRUE;
}

// ------------------------------------------------------------
// Return the last day of a month
// ------------------------------------------------------------
function F_get_last_day($tmonth,$tyear) {
	for ($iday=31; $iday>=28; $iday--) {
        if(checkdate($tmonth, $iday, $tyear)) { 
			return $iday;
		}
    }
return FALSE;
}

// ----------------------------------------------------------
// read category data
// ----------------------------------------------------------
function F_get_calendar_category_data($categoryid) {
	global $db, $l;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	$sql = "SELECT * FROM ".K_TABLE_CALENDAR_CATEGORIES." WHERE calcat_id='".$categoryid."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$calcat->language = $m['calcat_language'];
			$calcat->level = $m['calcat_level'];
			$calcat->name = $m['calcat_name'];
			$calcat->description = $m['calcat_description'];
			return $calcat;
		}
	}
	else {
		F_display_db_error();
	}
	return FALSE;
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
