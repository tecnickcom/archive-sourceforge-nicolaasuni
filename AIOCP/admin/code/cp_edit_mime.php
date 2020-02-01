<?php
//============================================================+
// File name   : cp_edit_mime.php                              
// Begin       : 2001-11-13                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit MIME content type associations to        
//               file extensions                               
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_MIME;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_mime_editor'];

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
		$sql = "DELETE FROM ".K_TABLE_MIME." WHERE mime_extension='".$mime_extension."'";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		$mime_extension=FALSE;
		break;
		}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_MIME, "mime_extension='".$mime_name."'", "mime_extension", $mime_extension)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$sql = "UPDATE IGNORE ".K_TABLE_MIME." SET mime_content='".$mime_content."' WHERE mime_extension='".$mime_extension."'";
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
			//check if mime_extension is unique
			$sql = "SELECT mime_extension FROM ".K_TABLE_MIME." WHERE mime_extension='".$mime_name."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
					F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				$sql = "INSERT IGNORE INTO ".K_TABLE_MIME." (mime_extension, mime_content) VALUES ('".$mime_name."','".$mime_content."')";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$mime_extension = F_aiocpdb_insert_id();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$mime_content = "";
		$mime_name = "";
		break;
		}

	default :{
		break;
		}

} //end of switch

// Initialize variables
if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!isset($mime_extension) OR (!$mime_extension)) {
			$sql = "SELECT * FROM ".K_TABLE_MIME." ORDER BY mime_extension LIMIT 1";
		} else {
			$sql = "SELECT * FROM ".K_TABLE_MIME." WHERE mime_extension='".$mime_extension."' LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$mime_extension = $m['mime_extension'];
				$mime_content = $m['mime_content'];
			}
			else {
				$mime_content = "";
				$mime_name = "";
				$mime_extension = "";
			}
		}
		else {
			F_display_db_error();
		}
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_mimeeditor" id="form_mimeeditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="mime_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT licenses ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_file_extension', 'h_file_extension'); ?></b></td>
<td class="fillOE">
<select name="mime_extension" id="mime_extension" size="0" onchange="document.form_mimeeditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_MIME." ORDER BY mime_extension";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['mime_extension']."\"";
		if($m['mime_extension'] == $mime_extension) {
			echo " selected=\"selected\"";
		}
		echo ">".$m['mime_extension']."</option>\n";
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
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_name', 'h_file_extension'); ?></b></td>
<td class="fillOE"><input type="text" name="mime_name" id="mime_name" value="<?php echo $mime_extension; ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_content', 'h_mime_content'); ?></b></td>
<td class="fillEE"><input type="text" name="mime_content" id="mime_content" value="<?php echo $mime_content; ?>" size="30" maxlength="255" /></td>
</tr>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if ($mime_extension) {
	F_submit_button("form_mimeeditor","menu_mode",$l['w_update']); 
	F_submit_button("form_mimeeditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_mimeeditor","menu_mode",$l['w_add']); 
F_submit_button("form_mimeeditor","menu_mode",$l['w_clear']); 
?>
</td>

</tr>
</table>
</form>
<!-- ====================================================== -->

<!-- Cange focus to mime_extension select -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_mimeeditor.mime_extension.focus();
//]]>
</script>
<!-- END Cange focus to mime_extension select -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
