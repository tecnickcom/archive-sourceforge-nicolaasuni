<?php
//============================================================+
// File name   : cp_edit_unit_of_measure.php                   
// Begin       : 2002-06-18                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit units of measure                         
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_UNIT_OF_MEASURE;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_unit_of_measure_editor'];

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
		$sql = "DELETE FROM ".K_TABLE_EC_UNITS_OF_MEASURE." WHERE unit_id=".$unit_id."";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		$unit_id=FALSE;
		break;
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update target
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_EC_UNITS_OF_MEASURE, "unit_name='".$unit_name."'", "unit_id", $unit_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$sql = "UPDATE IGNORE ".K_TABLE_EC_UNITS_OF_MEASURE." SET 
				unit_name='".$unit_name."', 
				unit_discrete='".$unit_discrete."', 
				unit_description='".$unit_description."' 
				WHERE unit_id=".$unit_id."";
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
			//check if unit_name is unique
			$sql = "SELECT unit_name FROM ".K_TABLE_EC_UNITS_OF_MEASURE." WHERE unit_name='".$unit_name."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				$sql = "INSERT IGNORE INTO ".K_TABLE_EC_UNITS_OF_MEASURE." (
				unit_name, 
				unit_discrete, 
				unit_description
				) VALUES (
				'".$unit_name."', 
				'".$unit_discrete."', 
				'".$unit_description."'
				)";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$unit_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
	}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$unit_name = "";
		$unit_discrete = 0;
		$unit_description = "";
	break;
		}

	default :{ 
		break;
	}

} //end of switch

// Initialize variables
if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!isset($unit_id) OR (!$unit_id)) {
			$sql = "SELECT * FROM ".K_TABLE_EC_UNITS_OF_MEASURE." ORDER BY unit_name LIMIT 1";
		} else {
			$sql = "SELECT * FROM ".K_TABLE_EC_UNITS_OF_MEASURE." WHERE unit_id=".$unit_id." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$unit_id = $m['unit_id'];
				$unit_name = $m['unit_name'];
				$unit_discrete = $m['unit_discrete']; 
				$unit_description = $m['unit_description'];
			}
			else {
				$unit_name = "";
				$unit_discrete = 0;
				$unit_description = "";
			}
		}
		else {
			F_display_db_error();
		}
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_unitseditor" id="form_unitseditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="unit_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT unit ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_unit', 'h_unit_select'); ?></b></td>
<td class="fillOE">
<select name="unit_id" id="unit_id" size="0" onchange="document.form_unitseditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_EC_UNITS_OF_MEASURE." ORDER BY unit_name";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['unit_id']."\"";
		if($m['unit_id'] == $unit_id) {
			echo " selected=\"selected\"";
		}
		echo ">[".$m['unit_id']."] ".htmlentities($m['unit_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT unit ==================== -->

<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE" colspan="2"><hr /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_name', 'h_unit_name'); ?></b></td>
<td class="fillOE"><input type="text" name="unit_name" id="unit_name" value="<?php echo htmlentities($unit_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_discrete', 'h_unit_discrete'); ?></b></td>
<td class="fillEE">
<?php 
echo "<input type=\"checkbox\" name=\"unit_discrete\" id=\"unit_discrete\" value=\"1\"";
if ($unit_discrete) {
	echo " checked=\"checked\"";
}
echo " />";
?>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b><?php echo F_display_field_name('w_description', 'h_unit_description'); ?></b>
<br />
<?php 
$doc_charset = F_word_language($selected_language, "a_meta_charset");
F_html_button(0, "form_unitseditor", "unit_description", $doc_charset);

$current_ta_code = $unit_description;
if(!$formstatus) {
	$current_ta_code = stripslashes($current_ta_code);
}
?>
</td>
<td class="fillOE">
<textarea cols="30" rows="4" name="unit_description" id="unit_description"><?php echo htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset); ?></textarea>
</td>
</tr>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if ($unit_id) {
	F_submit_button("form_unitseditor","menu_mode",$l['w_update']); 
	F_submit_button("form_unitseditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_unitseditor","menu_mode",$l['w_add']); 
F_submit_button("form_unitseditor","menu_mode",$l['w_clear']); 
?>
</td>

</tr>
</table>
</form>
<!-- ====================================================== -->

<!-- Cange focus to unit_id select -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_unitseditor.unit_id.focus();
//]]>
</script>
<!-- END Cange focus to unit_id select -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
