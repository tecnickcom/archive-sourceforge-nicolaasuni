<?php
//============================================================+
// File name   : cp_edit_licenses.php                          
// Begin       : 2001-11-11                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit Software Licenses                        
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_LICENSES;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_software_licenses_editor'];

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
		F_stripslashes_formfields(); // Delete
		$sql = "DELETE FROM ".K_TABLE_SOFTWARE_LICENSES." WHERE license_id=".$license_id."";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		$license_id=FALSE;
		break;
		}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_SOFTWARE_LICENSES, "license_name='".$license_name."'", "license_id", $license_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$sql = "UPDATE IGNORE ".K_TABLE_SOFTWARE_LICENSES." SET
				license_free='".$license_free."',
				license_name='".$license_name."',
				license_link='".$license_link."',
				license_description='".$license_description."' 
				WHERE license_id=".$license_id."";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_add']):
	case $l['w_add']:{ // Add
		if($formstatus = F_check_form_fields()) {
			//check if license_name is unique
			$sql = "SELECT license_name FROM ".K_TABLE_SOFTWARE_LICENSES." WHERE license_name='".$license_name."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				$sql = "INSERT IGNORE INTO ".K_TABLE_SOFTWARE_LICENSES." (
				license_free, 
				license_name, 
				license_link, 
				license_description
				) VALUES (
				'".$license_free."', 
				'".$license_name."', 
				'".$license_link."', 
				'".$license_description."')";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$license_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$license_free = 1;
		$license_name = "";
		$license_link = "";
		$license_description = "";
		break;
		}

	default :{
		break;
		}

} //end of switch

// Initialize variables
if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!isset($license_id) OR (!$license_id)) {
			$sql = "SELECT * FROM ".K_TABLE_SOFTWARE_LICENSES." ORDER BY license_name LIMIT 1";
		} else {
			$sql = "SELECT * FROM ".K_TABLE_SOFTWARE_LICENSES." WHERE license_id=".$license_id." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$license_id = $m['license_id'];
				$license_free = $m['license_free'];
				$license_name = $m['license_name'];
				$license_link = $m['license_link'];
				$license_description = $m['license_description'];
			}
		}
		else {
			F_display_db_error();
		}
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_licenseseditor" id="form_licenseseditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="license_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT licenses ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_license', 'h_license_select'); ?></b></td>
<td class="fillOE">
<select name="license_id" id="license_id" size="0" onchange="document.form_licenseseditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_SOFTWARE_LICENSES." ORDER BY license_name";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['license_id']."\"";
		if($m['license_id'] == $license_id) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['license_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT licenses ==================== -->

<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE"><hr /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_name', 'h_license_name'); ?></b></td>
<td class="fillOE"><input type="text" name="license_name" id="license_name" value="<?php echo htmlentities($license_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_type', 'h_license_type'); ?></b></td>
<td class="fillEE">
<select name="license_free" id="license_free" size="0">
<?php
		if($license_free) {
			echo "<option value=\"1\" selected=\"selected\">".$l['w_sfree']."</option>\n";
			echo "<option value=\"0\">".$l['w_snotfree']."</option>\n";
		}
		else {
			echo "<option value=\"1\">".$l['w_sfree']."</option>\n";
			echo "<option value=\"0\" selected=\"selected\">".$l['w_snotfree']."</option>\n";
		}
?>
</select>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_link', 'h_license_link'); ?></b></td>
<td class="fillOE"><input type="text" name="license_link" id="license_link" value="<?php echo $license_link; ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b><?php echo F_display_field_name('w_description', 'h_license_description'); ?></b>
<br />
<?php 
$doc_charset = $l['a_meta_charset'];
F_html_button(0,"form_licenseseditor","license_description", $doc_charset);

$current_ta_code = $license_description;
if(!$formstatus) {
	$current_ta_code = stripslashes($current_ta_code);
}
?>
</td>
<td class="fillEE"><textarea cols="30" rows="5" name="license_description" id="license_description"><?php echo htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset); ?></textarea></td>
</tr>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if ($license_id) {
	F_submit_button("form_licenseseditor","menu_mode",$l['w_update']); 
	F_submit_button("form_licenseseditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_licenseseditor","menu_mode",$l['w_add']); 
F_submit_button("form_licenseseditor","menu_mode",$l['w_clear']); 
?>
</td>

</tr>
</table>
</form>
<!-- ====================================================== -->

<!-- Cange focus to license_id select -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_licenseseditor.license_id.focus();
//]]>
</script>
<!-- END Cange focus to license_id select -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
