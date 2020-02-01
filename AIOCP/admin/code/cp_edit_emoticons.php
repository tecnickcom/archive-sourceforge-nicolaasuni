<?php
//============================================================+
// File name   : cp_edit_emoticons.php                         
// Begin       : 2001-09-08                                    
// Last Update : 2008-07-06
//                                                             
// Description : Add/remove/update smiles in                   
//               K_TABLE_EMOTICONS table                       
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

require_once('../code/cp_functions_upload.'.CP_EXT);

$pagelevel = K_AUTH_ADMIN_CP_EDIT_SMILES;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_emoticons_editor'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages; ?>
<!-- ====================================================== -->
<?php
// Initialize variables
if(isset($smile_link) AND (!empty($smile_link))) {
	$size = GetImageSize(K_PATH_IMAGES_EMOTICONS.$smile_link);
	$smile_width = $size[0];
	$smile_height = $size[1];
} else {
	$smile_link = "";
}

require_once('../../shared/code/cp_functions_form.'.CP_EXT);
if (isset($_REQUEST['menu_mode'])) {
	$menu_mode = $_REQUEST['menu_mode'];
} else {
	$menu_mode = "";
}
switch($menu_mode) {

	case unhtmlentities($l['w_delete']):
	case $l['w_delete']:{
		F_stripslashes_formfields(); // Delete smile
		$sql = "DELETE FROM ".K_TABLE_EMOTICONS." WHERE smile_id=".$smile_id."";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		$smile_id=FALSE;
		break;
		}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update smile
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_EMOTICONS, "smile_name='".$smile_name."'", "smile_id", $smile_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				if($_FILES['userfile']['name']) {
					$smile_link = F_upload_file("userfile", K_PATH_IMAGES_EMOTICONS);
					$size = GetImageSize(K_PATH_IMAGES_EMOTICONS.$smile_link);
					$smile_width = $size[0];
					$smile_height = $size[1];
				}
				$sql = "UPDATE IGNORE ".K_TABLE_EMOTICONS." SET 
				smile_name='".$smile_name."', 
				smile_link='".$smile_link."', 
				smile_width='".$smile_width."', 
				smile_height='".$smile_height."' 
				WHERE smile_id=".$smile_id."";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_add']):
	case $l['w_add']:{ // Add smile
		if($formstatus = F_check_form_fields()) {
			//check if smile_name is unique
			$sql = "SELECT smile_name FROM ".K_TABLE_EMOTICONS." WHERE smile_name='".$smile_name."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				if($_FILES['userfile']['name']) {
					$smile_link = F_upload_file("userfile", K_PATH_IMAGES_EMOTICONS);
					$size = GetImageSize(K_PATH_IMAGES_EMOTICONS.$smile_link);
					$smile_width = $size[0];
					$smile_height = $size[1];
				}
				$sql = "INSERT IGNORE INTO ".K_TABLE_EMOTICONS." (
				smile_name, 
				smile_link, 
				smile_width, 
				smile_height
				) VALUES (
				'".$smile_name."', 
				'".$smile_link."', 
				'".$smile_width."', 
				'".$smile_height."' )";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$smile_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$smile_name = "";
		$smile_link = K_BLANK_IMAGE;
		$smile_width = "15";
		$smile_height = "15";
		break;
		}

	default :{ 
		break;
		}

} //end of switch

// Initialize variables
if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!F_count_rows(K_TABLE_EMOTICONS)) { //if the table is void (no items) assign new values
			$smile_id = 1;
			$smile_name = "- NO SMILE -";
			$smile_link = K_BLANK_IMAGE;
			$smile_width = "15";
			$smile_height = "15";
		}
		if(!isset($smile_id) OR (!$smile_id)) {
			$sql = "SELECT * FROM ".K_TABLE_EMOTICONS." ORDER BY smile_name LIMIT 1";
		} else {
			$sql = "SELECT * FROM ".K_TABLE_EMOTICONS." WHERE smile_id=".$smile_id." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$smile_id = $m['smile_id'];
				$smile_name = $m['smile_name'];
				$smile_link = $m['smile_link'];
				$smile_width = $m['smile_width'];
				$smile_height = $m['smile_height'];
			}
		}
		else {
			F_display_db_error();
		}
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_smileeditor" id="form_smileeditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="smile_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT smile ==================== -->
<tr class="fillO">
<td class="fillOO" align="right">
<b><?php echo F_display_field_name_link('w_emoticon', 'h_emoticon_select', "selectWindow=window.open('cp_select_emoticons.".CP_EXT."?formname=form_smileeditor&amp;idfield=smile_id&amp;fieldtype=0&amp;fsubmit=1','selectWindow','dependent,height=300,width=300,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')"); ?></b>
</td>
<td class="fillOE">
<select name="smile_id" id="smile_id" size="0" onchange="document.form_smileeditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_EMOTICONS." ORDER BY smile_name";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['smile_id']."\"";
		if($m['smile_id'] == $smile_id) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['smile_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
<td class="fillOE" rowspan="2" align="right" valign="top"><img name="imagesmile" src="<?php echo K_PATH_IMAGES_EMOTICONS; ?><?php echo $smile_link; ?>" border="0" alt="" width="<?php echo $smile_width; ?>" height="<?php echo $smile_height; ?>" /></td></tr>
<!-- END SELECT smile ==================== -->

<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE"><hr /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_name', 'h_emoticon_name'); ?></b></td>
<td class="fillOE" colspan="2"><input type="text" name="smile_name" id="smile_name" value="<?php echo htmlentities($smile_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="20" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_link', 'h_emoticon_link'); ?></b></td>
<td class="fillEE"><input type="text" name="smile_link" id="smile_link" value="<?php echo $smile_link; ?>" size="30" maxlength="255" onchange="FJ_show_smile2()" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_image_dir', 'h_emoticon_directory'); ?></b></td>
<td class="fillOE" colspan="2">
<select name="smile_link_dir" id="smile_link_dir" size="0" onchange="document.form_smileeditor.smile_link.value=document.form_smileeditor.smile_link_dir.options[document.form_smileeditor.smile_link_dir.selectedIndex].value; FJ_show_smile ()">
<?php
// read directory for files (only graphics files).
$handle = opendir(K_PATH_IMAGES_EMOTICONS);
echo "<option value=\"\" selected=\"selected\"> - </option>\n";
	while (false !== ($file = readdir($handle))) {
		$path_parts = pathinfo($file);
		$file_ext = strtolower($path_parts['extension']);
		//check file type (GIF, JPG, PNG)
		if(($file_ext=="png")OR($file_ext=="gif")OR($file_ext=="jpg")OR($file_ext=="jpeg")) {
			echo "<option value=\"".$file."\"";
			if($file == $smile_link) {
				echo " selected=\"selected\"";
			}
			echo ">".$file."</option>\n";
		}
	}
closedir($handle);
?>
</select>
</td>
</tr>

<!-- Upload file ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_upload', 'h_emoticon_upload'); ?></b></td>
<td class="fillEE">
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo K_MAX_UPLOAD_SIZE; ?>" />
<input type="file" name="userfile" id="userfile" size="20" />
</td>
</tr>
<!-- END Upload file ==================== -->

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo $l['w_width']; ?>:</b></td>
<td class="fillOE" colspan="2"><?php echo $smile_width; ?> px</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo $l['w_height']; ?>:</b></td>
<td class="fillEE" colspan="2"><?php echo $smile_height; ?> px</td>
</tr>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if ($smile_id) {
	F_submit_button("form_smileeditor","menu_mode",$l['w_update']); 
	F_submit_button("form_smileeditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_smileeditor","menu_mode",$l['w_add']); 
F_submit_button("form_smileeditor","menu_mode",$l['w_clear']); 
?>
</td>

</tr>
</table>
</form>
<!-- ====================================================== -->

<!-- Show selected smile image ==================== -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
function FJ_show_smile(){
	document.images.imagesmile.src= "<?php echo K_PATH_IMAGES_EMOTICONS; ?>"+document.form_smileeditor.smile_link_dir.options[document.form_smileeditor.smile_link_dir.selectedIndex].value;
}

function FJ_show_smile2(){
	document.images.imagesmile.src= "<?php echo K_PATH_IMAGES_EMOTICONS; ?>"+document.form_smileeditor.smile_link.value;
}

document.form_smileeditor.smile_id.focus();
//]]>
</script>
<!-- END Cange focus to smile_id select -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
