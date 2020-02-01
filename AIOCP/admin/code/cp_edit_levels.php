<?php
//============================================================+
// File name   : cp_edit_levels.php                            
// Begin       : 2001-09-11                                    
// Last Update : 2008-07-06
//                                                             
// Description : Add/remove/update levels in                   
//               K_TABLE_LEVELS table                          
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_LEVELS;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

require_once('../../shared/code/cp_functions_form.'.CP_EXT);
require_once('../code/cp_functions_upload.'.CP_EXT);

$thispage_title = $l['t_level_editor'];

// Initialize variables
if(isset($level_image) AND (!empty($level_image))) {
	$size = GetImageSize(K_PATH_IMAGES_LEVELS.$level_image);
	$level_width = $size[0];
	$level_height = $size[1];
} else {
	$level_image = "";
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
		F_stripslashes_formfields(); // Delete level
		$sql = "DELETE FROM ".K_TABLE_LEVELS." WHERE level_id=".$level_id."";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		$level_id=FALSE;
		break;
		}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update level
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_LEVELS, "level_name='".$level_name."'", "level_id", $level_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				if(!F_check_unique(K_TABLE_LEVELS, "level_code='".$level_code."'", "level_id", $level_id)) {
					F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
				}
				else {
					//upload file
					if($_FILES['userfile']['name']) {
						$level_image = F_upload_file("userfile", K_PATH_IMAGES_LEVELS);
						$size = GetImageSize(K_PATH_IMAGES_LEVELS.$level_image);
						$level_width = $size[0];
						$level_height = $size[1];
					}
					
					$sql = "UPDATE IGNORE ".K_TABLE_LEVELS." SET 
					level_code='".$level_code."', 
					level_name='".$level_name."', 
					level_description='".$level_description."', 
					level_image='".$level_image."', 
					level_width='".$level_width."', 
					level_height='".$level_height."' 
					WHERE level_id=".$level_id."";
					if(!$r = F_aiocpdb_query($sql, $db)) {
						F_display_db_error();
					}
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_add']):
	case $l['w_add']:{ // Add level
		if($formstatus = F_check_form_fields()) {
			//check if level_code is unique
			$sql = "SELECT * FROM ".K_TABLE_LEVELS." WHERE level_code='".$level_code."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_code']);
			}
			else {
				//check if level_name is unique
				$sql = "SELECT level_name FROM ".K_TABLE_LEVELS." WHERE level_name='".$level_name."'";
				if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
					F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
				}
				else { //add item
					//upload file
					if($_FILES['userfile']['name']) {
						$level_image = F_upload_file("userfile", K_PATH_IMAGES_LEVELS);
						$size = GetImageSize(K_PATH_IMAGES_LEVELS.$level_image);
						$level_width = $size[0];
						$level_height = $size[1];
					}
					
					$sql = "INSERT IGNORE INTO ".K_TABLE_LEVELS." (
					level_code, 
					level_name, 
					level_description, 
					level_image, 
					level_width, 
					level_height
					) VALUES (
					'".$level_code."', 
					'".$level_name."', 
					'".$level_description."', 
					'".$level_image."', 
					'".$level_width."', 
					'".$level_height."')";
					if(!$r = F_aiocpdb_query($sql, $db)) {
						F_display_db_error();
					}
					else {
						$level_id = F_aiocpdb_insert_id();
					}
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$level_code = "";
		$level_name = "";
		$level_description = "";
		$level_image = "";
		$level_width = 0;
		$level_height = 0;
		break;
		}

	default :{ 
		break;
		}

} //end of switch

// Initialize variables
if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!isset($level_id) OR (!$level_id)) {
			$sql = "SELECT * FROM ".K_TABLE_LEVELS." ORDER BY level_code LIMIT 1";
		} else {
			$sql = "SELECT * FROM ".K_TABLE_LEVELS." WHERE level_id=".$level_id." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$level_id = $m['level_id'];
				$level_code = $m['level_code'];
				$level_name = $m['level_name'];
				$level_description = $m['level_description'];
				$level_image = $m['level_image'];
				$level_width = $m['level_width'];
				$level_height = $m['level_height'];
			}
		}
		else {
			F_display_db_error();
		}
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_leveleditor" id="form_leveleditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="level_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT level ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_level', 'h_level_select'); ?></b></td>
<td class="fillOE">
<select name="level_id" id="level_id" size="0" onchange="document.form_leveleditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_LEVELS." ORDER BY level_code";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['level_id']."\"";
		if($m['level_id'] == $level_id) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['level_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select> <img name="imagelevel" src="<?php echo K_PATH_IMAGES_LEVELS; ?><?php echo $level_image; ?>" border="0" alt="" width="<?php echo $level_width; ?>" height="<?php echo $level_height; ?>" /></td></tr>
<!-- END SELECT level ==================== -->

<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE"><hr /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_name', 'h_level_name'); ?></b></td>
<td class="fillOE"><input type="text" name="level_name" id="level_name" value="<?php echo htmlentities($level_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="20" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_code', 'h_level_code'); ?></b></td>
<td class="fillEE"><input type="text" name="level_code" id="level_code" value="<?php echo $level_code; ?>" size="20" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_image', 'h_level_image'); ?></b></td>
<td class="fillOE">
<select name="level_image" id="level_image" size="0" onchange="FJ_show_level()">
<?php
// read directory for files (only graphics files).
$handle = opendir(K_PATH_IMAGES_LEVELS);
	while (false !== ($file = readdir($handle))) {
		$path_parts = pathinfo($file);
		$file_ext = strtolower($path_parts['extension']);
		//check file type (GIF, JPG, PNG)
		if(($file_ext=="png")OR($file_ext=="gif")OR($file_ext=="jpg")OR($file_ext=="jpeg")) {
			echo "<option value=\"".$file."\"";
			if($file == $level_image) {
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
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_upload', ''); ?></b></td>
<td class="fillEE" colspan="2">
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo K_MAX_UPLOAD_SIZE; ?>" />
<input type="file" name="userfile" id="userfile" size="20" />
</td>
</tr>
<!-- END Upload file ==================== -->


<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo $l['w_width']; ?>:</b></td>
<td class="fillOE" colspan="2"><?php echo $level_width; ?> px</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo $l['w_height']; ?>:</b></td>
<td class="fillEE" colspan="2"><?php echo $level_height; ?> px</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b><?php echo F_display_field_name('w_description', ''); ?></b>
<br />
<?php 
$doc_charset = $l['a_meta_charset'];
F_html_button(0, "form_leveleditor", "level_description", $doc_charset);

$current_ta_code = $level_description;
if(!$formstatus) {
	$current_ta_code = stripslashes($current_ta_code);
}
?>
</td>
<td class="fillOE">
<textarea cols="30" rows="5" name="level_description" id="level_description"><?php echo htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset); ?></textarea>
</td>
</tr>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if ($level_id) {
	F_submit_button("form_leveleditor","menu_mode",$l['w_update']); 
	F_submit_button("form_leveleditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_leveleditor","menu_mode",$l['w_add']); 
F_submit_button("form_leveleditor","menu_mode",$l['w_clear']); 
?>
</td>

</tr>
</table>
</form>
<!-- ====================================================== -->

<!-- Show selected level image ==================== -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
function FJ_show_level (){
	document.images.imagelevel.src= "<?php echo K_PATH_IMAGES_LEVELS; ?>"+document.form_leveleditor.level_image.options[document.form_leveleditor.level_image.selectedIndex].value;
}
FJ_show_level ();

document.form_leveleditor.level_id.focus();
//]]>
</script>
<!-- END Cange focus to level_id select -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>

