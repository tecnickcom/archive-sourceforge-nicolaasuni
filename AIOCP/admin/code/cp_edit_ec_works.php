<?php
//============================================================+
// File name   : cp_edit_ec_works.php                          
// Begin       : 2002-09-06                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit Works List                               
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
require_once('../../shared/code/cp_functions_form.'.CP_EXT);

$pagelevel = K_AUTH_ADMIN_CP_EDIT_EC_WORKS;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_works_editor'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages;

if (isset($_REQUEST['menu_mode'])) {
	$menu_mode = $_REQUEST['menu_mode'];
} else {
	$menu_mode = "";
}
switch($menu_mode) {

	case unhtmlentities($l['w_delete']):
	case $l['w_delete']:{
		F_stripslashes_formfields(); // Delete target
		$sql = "DELETE FROM ".K_TABLE_EC_WORKS." WHERE work_id=".$work_id."";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		$work_id=FALSE;
		break;
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update target
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_EC_WORKS, "work_name='".$work_name."'", "work_id", $work_id)) {
				F_print_error("WARNING", $l['m_duplicate_data']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$sql = "UPDATE IGNORE ".K_TABLE_EC_WORKS." SET 
				work_name='".$work_name."',
				work_date_start='".$work_date_start."',
				work_date_end='".$work_date_end."',
				work_description ='".$work_description."'
				WHERE work_id=".$work_id."";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
			}
		}
		break;
	}

	case unhtmlentities($l['w_add']):
	case $l['w_add']:{ // Add target
		if($formstatus = F_check_form_fields()) {
			//check if work_name is unique
			$sql = "SELECT work_date, work_description FROM ".K_TABLE_EC_WORKS." WHERE work_name='".$work_name."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_data']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				$sql = "INSERT IGNORE INTO ".K_TABLE_EC_WORKS." (
				work_name,
				work_date_start,
				work_date_end,
				work_description
				) VALUES (
				'".$work_name."',
				'".$work_date_start."',
				'".$work_date_end."',
				'".$work_description."'
				)";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$work_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
	}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$work_name = "";
		$work_date_start = gmdate("Y-m-d");
		$work_date_end = gmdate("Y-m-d");
		$work_description = "";
		break;
	}

	default :{ 
		break;
	}

} //end of switch

// Initialize variables


$currentyear = gmdate('Y');
$firstyear = $currentyear;
$sql = "SELECT MIN(work_date_start) FROM ".K_TABLE_EC_WORKS."";
if($r = F_aiocpdb_query($sql, $db)) {
	if($m = F_aiocpdb_fetch_array($r)) {
		$firstyear = gmdate('Y',strtotime($m[0]));
	}
}
else {
	F_display_db_error();
}

if ( (!isset($tyear) OR (!$tyear)) OR ($tyear < $firstyear)) {
	$tyear = $currentyear;
}

if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!isset($work_id) OR (!$work_id)) {
			$sql = "SELECT * FROM ".K_TABLE_EC_WORKS." WHERE YEAR(work_date_start)='".$tyear."' ORDER BY work_date_start DESC LIMIT 1";
		} else {
			$sql = "SELECT * FROM ".K_TABLE_EC_WORKS." WHERE work_id=".$work_id." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$work_id = $m['work_id'];
				$work_name = $m['work_name'];
				$work_date_start = $m['work_date_start'];
				$work_date_end = $m['work_date_end'];
				$work_description = $m['work_description'];
			}
			else {
				$work_name = "";
				$work_date_start = gmdate("Y-m-d");
				$work_date_end = gmdate("Y-m-d");
				$work_description = "";
			}
		}
		else {
			F_display_db_error();
		}
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_workslisteditor" id="form_workslisteditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="work_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT year ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_year', 'h_work_year'); ?></b></td>
<td class="fillOE">
<input type="hidden" name="changeyear" id="changeyear" value="0" />

<select name="tyear" id="tyear" size="0" onchange="document.form_workslisteditor.changeyear.value=1; document.form_workslisteditor.submit()">
<?php
for ($i=$firstyear; $i<=$currentyear; $i++) {
	echo "<option value=\"".$i."\"";
	if ($tyear == $i) {echo " selected=\"selected\"";}
	echo ">".$i."</option>\n";
}
?>
</select>
</td>
</tr>
<!-- END SELECT year ==================== -->

<!-- SELECT ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_work', 'h_work_select'); ?></b></td>
<td class="fillEE">
<select name="work_id" id="work_id" size="0" onchange="document.form_workslisteditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_EC_WORKS." WHERE YEAR(work_date_start)='".$tyear."' ORDER BY work_date_start DESC";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['work_id']."\"";
		if($m['work_id'] == $work_id) {
			echo " selected=\"selected\"";
		}
		echo ">".$m['work_date_start']." - ".htmlentities($m['work_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT ==================== -->

<tr class="fillO">
<td class="fillOO">&nbsp;</td>
<td class="fillOE"><hr /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_name', 'h_work_name'); ?></b></td>
<td class="fillOE"><input type="text" name="work_name" id="work_name" value="<?php echo htmlentities($work_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="40" maxlength="255" />
<input type="hidden" name="x_work_date_start" id="x_work_date_start" value="([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})" />
<input type="hidden" name="xl_work_date_start" id="xl_work_date_start" value="<?php echo $l['w_date_start']; ?>" /></td>
</tr>



<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_date_start', 'h_work_date_start'); ?></b></td>
<td class="fillEE"><input type="text" name="work_date_start" id="work_date_start" value="<?php echo $work_date_start; ?>" size="40" maxlength="10" />
<input type="hidden" name="x_work_date_end" id="x_work_date_end" value="([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})" />
<input type="hidden" name="xl_work_date_end" id="xl_work_date_end" value="<?php echo $l['w_date_end']; ?>" /></td>
</tr>



<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_date_end', 'h_work_date_end'); ?></b></td>
<td class="fillEE"><input type="text" name="work_date_end" id="work_date_end" value="<?php echo $work_date_end; ?>" size="40" maxlength="10" /></td>
</tr>

<?php $doc_charset = F_word_language($selected_language, "a_meta_charset"); ?>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b><?php echo F_display_field_name('w_description', 'h_work_description'); ?></b>
</td>
<?php
$current_ta_code = $work_description;
if(!$formstatus) {
	$current_ta_code = stripslashes($current_ta_code);
}
?>
<td class="fillOE">
<textarea cols="40" rows="10" name="work_description" id="work_description"><?php echo htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset); ?></textarea>
</td>
</tr>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if (isset($work_id) AND ($work_id > 0)) {
	F_submit_button("form_workslisteditor","menu_mode",$l['w_update']); 
	F_submit_button("form_workslisteditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_workslisteditor","menu_mode",$l['w_add']); 
F_submit_button("form_workslisteditor","menu_mode",$l['w_clear']); 
?>
</td>

</tr>
</table>
</form>
<!-- ====================================================== -->

<!-- Cange focus to work_id select -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_workslisteditor.work_id.focus();
//]]>
</script>
<!-- END Cange focus to work_id select -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
