<?php
//============================================================+
// File name   : cp_edit_page_modules.php
// Begin       : 2002-10-15
// Last Update : 2007-01-11
// 
// Description : Edit standard and custom Page Modules
//               A page module is a PHP code block
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_PAGE_MODULE;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_page_module_editor'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages;

if (!isset($menu_mode)) {
	$menu_mode = false;
}

switch($menu_mode) {

	case unhtmlentities($l['w_delete']):
	case $l['w_delete']:{
		F_stripslashes_formfields(); // Delete
		F_print_error("WARNING", $l['m_delete_confirm']);
		?>
		<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_delete" id="form_delete">
		<input type="hidden" name="menu_mode" id="menu_mode" value="forcedelete" />
		<input type="hidden" name="forcedelete" id="forcedelete" value="" />
		<input type="hidden" name="pagemod_id" id="pagemod_id" value="<?php echo $pagemod_id; ?>" />
		<?php 
		F_submit_button("form_delete","forcedelete",$l['w_delete']);
		F_submit_button("form_delete","forcedelete",$l['w_cancel']);
		?>
		</form>
		<?php
		break;
	}

	case "forcedelete":{ // Delete category and all associated messages and users
		if($forcedelete == $l['w_delete']) { //check if delete button has been pushed (redundant check)
			$sql = "DELETE FROM ".K_TABLE_PAGE_MODULES." WHERE pagemod_id=".$pagemod_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			$pagemod_id=FALSE;
		}
		break;
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_PAGE_MODULES,"pagemod_name='".$pagemod_name."'","pagemod_id",$pagemod_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$sql = "UPDATE IGNORE ".K_TABLE_PAGE_MODULES." SET 
				pagemod_name='".$pagemod_name."', 
				pagemod_params='".$pagemod_params."', 
				pagemod_code='".$pagemod_code."'
				WHERE pagemod_id=".$pagemod_id."";
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
			//check if name is unique
			$sql = "SELECT pagemod_id FROM ".K_TABLE_PAGE_MODULES." WHERE pagemod_name='".$pagemod_name."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				$sql = "INSERT IGNORE INTO ".K_TABLE_PAGE_MODULES." (
				pagemod_name, 
				pagemod_params, 
				pagemod_code
				) VALUES (
				'".$pagemod_name."', 
				'".$pagemod_params."', 
				'".$pagemod_code."'
				)";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$pagemod_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$pagemod_name = "";
		$pagemod_params = 0;
		$pagemod_code = "";
		break;
		}

	default :{
		break;
		}

} //end of switch

// Initialize variables
if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!isset($pagemod_id) OR (!$pagemod_id)) {
			$sql = "SELECT * FROM ".K_TABLE_PAGE_MODULES." ORDER BY pagemod_name LIMIT 1";
		} else {
			$sql = "SELECT * FROM ".K_TABLE_PAGE_MODULES." WHERE pagemod_id=".intval($pagemod_id)." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$pagemod_id = $m['pagemod_id'];
				$pagemod_name = $m['pagemod_name'];
				$pagemod_params = $m['pagemod_params'];
				$pagemod_code = $m['pagemod_code'];
			}
			else {
				$pagemod_name = "";
				$pagemod_params = 0;
				$pagemod_code = "";
			}
		}
		else {
			F_display_db_error();
		}
	}
}

?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_pagemoduleseditor" id="form_pagemoduleseditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="pagemod_name,pagemod_code" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name'].",".$l['w_code']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT  ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_module', 'h_pagemod_select'); ?></b></td>
<td class="fillOE">
<select name="pagemod_id" id="pagemod_id" size="0" onchange="document.form_pagemoduleseditor.submit()">
<?php
	$sql = "SELECT * FROM ".K_TABLE_PAGE_MODULES." ORDER BY pagemod_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"".$m['pagemod_id']."\"";
			if($m['pagemod_id'] == $pagemod_id) {
				echo " selected=\"selected\"";
			}
			echo ">".htmlentities($m['pagemod_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
		}
	}
	else {
		F_display_db_error();
	}
?>
</select>
</td>
</tr>
<!-- END SELECT  ==================== -->

<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE"><hr /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_name', 'h_pagemod_name'); ?></b></td>
<td class="fillOE"><input type="text" name="pagemod_name" id="pagemod_name" value="<?php echo htmlentities($pagemod_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="50" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_parameters', 'h_pagemod_parameters'); ?></b></td>
<td class="fillEE"><input type="text" name="pagemod_params" id="pagemod_params" value="<?php echo $pagemod_params; ?>" size="4" maxlength="255" /></td>
</tr>

<?php
$doc_charset = F_word_language($selected_language, "a_meta_charset");
$current_ta_code = $pagemod_code;
if(!$formstatus) {
	$current_ta_code = stripslashes($current_ta_code);
}
?>
<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b><?php echo F_display_field_name('w_code', 'h_pagemod_code'); ?></b></td>
<td class="fillOE"><textarea cols="70" rows="15" name="pagemod_code" id="pagemod_code"><?php echo htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset); ?></textarea></td>
</tr>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if ($pagemod_id) {
	F_submit_button("form_pagemoduleseditor","menu_mode",$l['w_update']); 
	F_submit_button("form_pagemoduleseditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_pagemoduleseditor","menu_mode",$l['w_add']); 
F_submit_button("form_pagemoduleseditor","menu_mode",$l['w_clear']); 
?>
</td>
</tr>
</table>
</form>
<!-- ====================================================== -->

<!-- Cange focus to pagemod_id select -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_pagemoduleseditor.pagemod_id.focus();
//]]>
</script>
<!-- END Cange focus to pagemod_id select -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
