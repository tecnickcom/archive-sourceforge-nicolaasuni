<?php
//============================================================+
// File name   : cp_edit_page_header_footer.php                
// Begin       : 2001-04-18                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit headers and footers to be used in        
//               dynamic pages.                                
//               K_TABLE_PAGE_HEADER_FOOTER table              
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_PAGE_HEADER_FOOTER;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_page_header_footer_editor'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages; ?>
<!-- ====================================================== -->
<?php
if (isset($_REQUEST['menu_mode'])) {
	$menu_mode = $_REQUEST['menu_mode'];
} else {
	$menu_mode = "";
}
switch($menu_mode) {

	case unhtmlentities($l['w_delete']):
	case $l['w_delete']:{
		F_stripslashes_formfields(); // Delete
		if ((strcmp($pagehf_name, "default")!=0) AND (strcmp($pagehf_name, "default_popup")!=0)) { //default page cannot be deleted
			$sql = "DELETE FROM ".K_TABLE_PAGE_HEADER_FOOTER." WHERE pagehf_id=".$pagehf_id." AND pagehf_name!='default' AND pagehf_name!='default_popup'";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			$pagehf_id=FALSE;
		}
		else {
			F_print_error("WARNING", $l['m_delete_default']);
		}
		break;
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_PAGE_HEADER_FOOTER, "pagehf_name='".$pagehf_name."'", "pagehf_id", $pagehf_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$sql = "UPDATE IGNORE ".K_TABLE_PAGE_HEADER_FOOTER." SET 
						pagehf_name='".$pagehf_name."', 
						pagehf_header='".$pagehf_header."', 
						pagehf_footer='".$pagehf_footer."'
						WHERE pagehf_id=".$pagehf_id."";
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
			//check if pagehf_name is unique
			$sql = "SELECT pagehf_name FROM ".K_TABLE_PAGE_HEADER_FOOTER." WHERE pagehf_name='".$pagehf_name."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				$sql = "INSERT IGNORE INTO ".K_TABLE_PAGE_HEADER_FOOTER." 
						(
						pagehf_name,
						pagehf_header,
						pagehf_footer
						) 
						values (
						'".$pagehf_name."', 
						'".$pagehf_header."', 
						'".$pagehf_footer."'
						)";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$pagehf_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
	}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$pagehf_name = "";
		$pagehf_header = "";
		$pagehf_footer = "";
	break;
		}

	default :{ 
		break;
	}

} //end of switch

// Initialize variables
if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!isset($pagehf_id) OR (!$pagehf_id)) {
			$sql = "SELECT * FROM ".K_TABLE_PAGE_HEADER_FOOTER." ORDER BY pagehf_name LIMIT 1";
		} else {
			$sql = "SELECT * FROM ".K_TABLE_PAGE_HEADER_FOOTER." WHERE pagehf_id=".$pagehf_id." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$pagehf_id = $m['pagehf_id'];
				$pagehf_name = $m['pagehf_name'];
				$pagehf_header = $m['pagehf_header'];
				$pagehf_footer = $m['pagehf_footer'];
			}
		}
		else {
			F_display_db_error();
		}
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_pagehfeditor" id="form_pagehfeditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="pagehf_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT page header-footer ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_page', 'h_pagehf_select'); ?></b></td>
<td class="fillOE">
<select name="pagehf_id" id="pagehf_id" size="0" onchange="document.form_pagehfeditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_PAGE_HEADER_FOOTER." ORDER BY pagehf_name";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['pagehf_id']."\"";
		if($m['pagehf_id'] == $pagehf_id) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['pagehf_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT page header-footer ==================== -->

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_name', 'h_pagehf_name'); ?></b></td>
<td class="fillEE"><input type="text" name="pagehf_name" id="pagehf_name" value="<?php echo htmlentities($pagehf_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b><?php echo F_display_field_name('w_header', 'h_pagehf_header'); ?></b><br />
<?php 
$doc_charset = $l['a_meta_charset'];
F_html_button("page", "form_pagehfeditor", "pagehf_header", $doc_charset);

$current_ta_code = $pagehf_header;
if(!$formstatus) {
	$current_ta_code = stripslashes($current_ta_code);
}
?>
</td>
<td class="fillOE">
<textarea cols="60" rows="10" name="pagehf_header" id="pagehf_header"><?php echo htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset); ?></textarea>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b><?php echo F_display_field_name('w_footer', 'h_pagehf_footer'); ?></b><br />
<?php F_html_button("page", "form_pagehfeditor", "pagehf_footer", $doc_charset);

$current_ta_code = $pagehf_footer;
if(!$formstatus) {
	$current_ta_code = stripslashes($current_ta_code);
}
?>
</td>
<td class="fillEE">
<textarea cols="60" rows="10" name="pagehf_footer" id="pagehf_footer"><?php echo htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset); ?></textarea>
</td>
</tr>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if ($pagehf_id) {
	F_submit_button("form_pagehfeditor","menu_mode",$l['w_update']); 
	F_submit_button("form_pagehfeditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_pagehfeditor","menu_mode",$l['w_add']); 
F_submit_button("form_pagehfeditor","menu_mode",$l['w_clear']); 
?>
</td>

</tr>
</table>
</form>
<!-- ====================================================== -->

<!-- Cange focus to pagehf_id select -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_pagehfeditor.pagehf_id.focus();
//]]>
</script>
<!-- END Cange focus to pagehf_id select -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
