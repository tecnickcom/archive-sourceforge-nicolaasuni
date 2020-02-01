<?php
//============================================================+
// File name   : cp_edit_icons.php                             
// Begin       : 2001-09-07                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit AIOCP System Icons (menu)                
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
require_once('../code/cp_functions_upload.'.CP_EXT);

$pagelevel = K_AUTH_ADMIN_CP_EDIT_ICONS;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_icon_editor'];

// Initialize variables
if(isset($icon_link) AND (!empty($icon_link))) {
	$size = GetImageSize(K_PATH_IMAGES_ICONS.$icon_link);
	$icon_width = $size[0];
	$icon_height = $size[1];
} else {
	$icon_link = "";
}

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
		F_stripslashes_formfields(); // Delete icon
		$sql = "DELETE FROM ".K_TABLE_ICONS." WHERE icon_id=".$icon_id."";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		$icon_id=FALSE;
		break;
		}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update icon
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_ICONS, "icon_name='".$icon_name."'", "icon_id", $icon_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				if($_FILES['userfile']['name']) {
					$icon_link = F_upload_file("userfile", K_PATH_IMAGES_ICONS);
					$size = GetImageSize(K_PATH_IMAGES_ICONS.$icon_link);
					$icon_width = $size[0];
					$icon_height = $size[1];
				}
				$sql = "UPDATE IGNORE ".K_TABLE_ICONS." SET 
				icon_name='".$icon_name."', 
				icon_link='".$icon_link."', 
				icon_width='".$icon_width."', 
				icon_height='".$icon_height."' 
				WHERE icon_id=".$icon_id."";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_add']):
	case $l['w_add']:{ // Add icon
		if($formstatus = F_check_form_fields()) {
			//check if icon_name is unique
			$sql = "SELECT icon_name FROM ".K_TABLE_ICONS." WHERE icon_name='".$icon_name."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
					F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				if($_FILES['userfile']['name']) {
					$icon_link = F_upload_file("userfile", K_PATH_IMAGES_ICONS);
					$size = GetImageSize(K_PATH_IMAGES_ICONS.$icon_link);
					$icon_width = $size[0];
					$icon_height = $size[1];
				}
				$sql = "INSERT IGNORE INTO ".K_TABLE_ICONS." (
				icon_name, 
				icon_link, 
				icon_width, 
				icon_height
				) VALUES (
				'".$icon_name."', 
				'".$icon_link."', 
				'".$icon_width."', 
				'".$icon_height."')";
				if(!$r = F_aiocpdb_query($sql, $db))  {
					F_display_db_error();
				}
				else {
					$icon_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$icon_name = "";
		$icon_link = K_BLANK_IMAGE;
		$icon_width = "50";
		$icon_height = "50";
		break;
		}

	default :{ 
		break;
		}

} //end of switch

// Initialize variables
if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!isset($icon_id) OR (!$icon_id)) {
			$sql = "SELECT * FROM ".K_TABLE_ICONS." ORDER BY icon_name LIMIT 1";
		} else {
			$sql = "SELECT * FROM ".K_TABLE_ICONS." WHERE icon_id=".$icon_id." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$icon_id = $m['icon_id'];
				$icon_name = $m['icon_name'];
				$icon_link = $m['icon_link'];
				$icon_width = $m['icon_width'];
				$icon_height = $m['icon_height'];
			}
			else {
				$icon_id = 1;
				$icon_name = "- NO ICON -";
				$icon_link = K_BLANK_IMAGE;
				$icon_width = "50";
				$icon_height = "50";
			}
		}
		else {
			F_display_db_error();
		}
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_iconeditor" id="form_iconeditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="icon_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT ICON ==================== -->
<tr class="fillO">
<td class="fillOO" align="right">
<b><?php echo F_display_field_name_link('w_icon', 'h_icon_select', "selectWindow=window.open('cp_select_icons.".CP_EXT."?formname=form_iconeditor&amp;idfield=icon_id&amp;fieldtype=0&amp;fsubmit=1','selectWindow','dependent,height=450,width=450,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')"); ?></b>
</td>
<td class="fillOE">
<select name="icon_id" id="icon_id" size="0" onchange="document.form_iconeditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_ICONS." ORDER BY icon_name";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['icon_id']."\"";
		if($m['icon_id'] == $icon_id) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['icon_name'], ENT_NOQUOTES, $l['a_meta_charset'])."&nbsp;</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
<td class="fillOE" rowspan="2" align="right" valign="top"><img name="imageicon" src="<?php echo K_PATH_IMAGES_ICONS; ?><?php echo $icon_link; ?>" border="0" alt="" width="<?php echo $icon_width; ?>" height="<?php echo $icon_height; ?>" /></td></tr>
<!-- END SELECT ICON ==================== -->

<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE"><hr /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_name', 'h_icon_name'); ?></b></td>
<td class="fillOE" colspan="2"><input type="text" name="icon_name" id="icon_name" value="<?php echo htmlentities($icon_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="20" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_link', 'h_icon_link'); ?></b></td>
<td class="fillEE"><input type="text" name="icon_link" id="icon_link" value="<?php echo $icon_link; ?>" size="30" maxlength="255" onchange="FJ_show_icon2()" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_image_dir', 'h_icon_directory'); ?></b></td>
<td class="fillOE" colspan="2">
<select name="icon_link_dir" id="icon_link_dir" size="0" onchange="document.form_iconeditor.icon_link.value=document.form_iconeditor.icon_link_dir.options[document.form_iconeditor.icon_link_dir.selectedIndex].value; FJ_show_icon ()">
<?php
// read directory for files (only graphics files).
$handle = opendir(K_PATH_IMAGES_ICONS);
echo "<option value=\"\" selected=\"selected\"> - &nbsp;</option>\n";
	while (false !== ($file = readdir($handle))) {
		$path_parts = pathinfo($file);
		$file_ext = strtolower($path_parts['extension']);
		//check file type (GIF, JPG, PNG)
		if(($file_ext=="png")OR($file_ext=="gif")OR($file_ext=="jpg")OR($file_ext=="jpeg")) {
			echo "<option value=\"".$file."\"";
			if($file == $icon_link) {
				echo " selected=\"selected\"";
			}
			echo ">".$file."&nbsp;</option>\n";
		}
	}
closedir($handle);
?>
</select>
</td>
</tr>

<!-- Upload file ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_upload', 'h_icon_upload'); ?></b></td>
<td class="fillEE">
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo K_MAX_UPLOAD_SIZE; ?>" />
<input type="file" name="userfile" id="userfile" size="20" />
</td>
</tr>
<!-- END Upload file ==================== -->

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo $l['w_width']; ?>:</b></td>
<td class="fillOE" colspan="2"><?php echo $icon_width; ?> px</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo $l['w_height']; ?>:</b></td>
<td class="fillEE" colspan="2"><?php echo $icon_height; ?> px</td>
</tr>

</table>

</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if ($icon_id) {
	F_submit_button("form_iconeditor","menu_mode",$l['w_update']); 
	F_submit_button("form_iconeditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_iconeditor","menu_mode",$l['w_add']); 
F_submit_button("form_iconeditor","menu_mode",$l['w_clear']); 
?>

</td>
</tr>

</table>
</form>

<!-- ====================================================== -->

<!-- Show selected icon image ==================== -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
function FJ_show_icon (){
	document.images.imageicon.src= "<?php echo K_PATH_IMAGES_ICONS; ?>"+document.form_iconeditor.icon_link_dir.options[document.form_iconeditor.icon_link_dir.selectedIndex].value;
}

function FJ_show_icon2 (){
	document.images.imageicon.src= "<?php echo K_PATH_IMAGES_ICONS; ?>"+document.form_iconeditor.icon_link.value;
}

document.form_iconeditor.icon_id.focus();
//]]>
</script>
<!-- END Cange focus to icon_id select -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
