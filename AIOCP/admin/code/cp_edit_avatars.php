<?php
//============================================================+
// File name   : cp_edit_avatars.php                           
// Begin       : 2001-09-08                                    
// Last Update : 2008-07-06
//                                                             
// Description : Add/remove/update avatars in                  
//               K_TABLE_AVATARS table                         
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_AVATARS;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

require_once('../../shared/code/cp_functions_form.'.CP_EXT);
require_once('../code/cp_functions_upload.'.CP_EXT);

$thispage_title = $l['t_avatar_editor'];

// Initialize variables
if(isset($avatar_link) AND (strlen($avatar_link) > 0 )) {
	$size = GetImageSize(K_PATH_IMAGES_AVATARS.$avatar_link);
	$avatar_width = $size[0];
	$avatar_height = $size[1];
} else {
	$avatar_link = "";
}

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
		F_stripslashes_formfields(); // Delete avatar
		$sql = "DELETE FROM ".K_TABLE_AVATARS." WHERE avatar_id=".$avatar_id."";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		$avatar_id=FALSE;
		break;
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update avatar
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_AVATARS,"avatar_name='".$avatar_name."'","avatar_id",$avatar_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				if($_FILES['userfile']['name']) {
					$avatar_link = F_upload_file("userfile", K_PATH_IMAGES_AVATARS);
					$size = GetImageSize(K_PATH_IMAGES_AVATARS.$avatar_link);
					$avatar_width = $size[0];
					$avatar_height = $size[1];
				} //upload file
				$sql = "UPDATE IGNORE ".K_TABLE_AVATARS." SET 
				avatar_name='".$avatar_name."', 
				avatar_link='".$avatar_link."', 
				avatar_width='".$avatar_width."', 
				avatar_height='".$avatar_height."' 
				WHERE avatar_id=".$avatar_id."";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
			}
		}
		break;
	}

	case unhtmlentities($l['w_add']):
	case $l['w_add']:{ // Add avatar
		if($formstatus = F_check_form_fields()) {
			//check if avatar_name is unique
			$sql = "SELECT avatar_name FROM ".K_TABLE_AVATARS." WHERE avatar_name='".$avatar_name."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				if($_FILES['userfile']['name']) {
					$avatar_link = F_upload_file("userfile", K_PATH_IMAGES_AVATARS);
					$size = GetImageSize(K_PATH_IMAGES_AVATARS.$avatar_link);
					$avatar_width = $size[0];
					$avatar_height = $size[1];
				} //upload file
				$sql = "INSERT IGNORE INTO ".K_TABLE_AVATARS." (
				avatar_name, 
				avatar_link, 
				avatar_width, 
				avatar_height
				) VALUES (
				'".$avatar_name."', 
				'".$avatar_link."', 
				'".$avatar_width."', 
				'".$avatar_height."')";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$avatar_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
	}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$avatar_name = "";
		$avatar_link = K_BLANK_IMAGE;
		$avatar_width = "50";
		$avatar_height = "50";
		break;
	}

	default :{ 
		break;
	}

} //end of switch

// Initialize variables
if($formstatus) {
	
	if(!F_count_rows(K_TABLE_AVATARS)) { //if the table is void (no items) assign new values
		$avatar_id = 1;
		$avatar_name = "- NO avatar -";
		$avatar_link = K_BLANK_IMAGE;
		$avatar_width = "50";
		$avatar_height = "50";
	}
	
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!isset($avatar_id) OR (!$avatar_id)) {
			$sql = "SELECT * FROM ".K_TABLE_AVATARS." ORDER BY avatar_name LIMIT 1";
		} else {
			$sql = "SELECT * FROM ".K_TABLE_AVATARS." WHERE avatar_id=".$avatar_id." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$avatar_id = $m['avatar_id'];
				$avatar_name = $m['avatar_name'];
				$avatar_link = $m['avatar_link'];
				$avatar_width = $m['avatar_width'];
				$avatar_height = $m['avatar_height'];
			}
		}
		else {
			F_display_db_error();
		}
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_avatareditor" id="form_avatareditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="avatar_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT avatar ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name_link('w_avatar', 'h_avatar_select', "selectWindow=window.open('cp_select_avatars.".CP_EXT."?formname=form_avatareditor&amp;idfield=avatar_id&amp;fieldtype=0&amp;fsubmit=1','selectWindow','dependent,height=450,width=450,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')"); ?></b>
</td>
<td class="fillOE">
<select name="avatar_id" id="avatar_id" size="0" onchange="document.form_avatareditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_AVATARS." ORDER BY avatar_name";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['avatar_id']."\"";
		if($m['avatar_id'] == $avatar_id) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['avatar_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
<td class="fillOE" rowspan="2" align="right" valign="top"><img name="imageavatar" src="<?php echo K_PATH_IMAGES_AVATARS; ?><?php echo $avatar_link; ?>" border="0" alt="" width="<?php echo $avatar_width; ?>" height="<?php echo $avatar_height; ?>" /></td></tr>
<!-- END SELECT avatar ==================== -->

<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE"><hr /></td>
</tr>

<tr class="fillO">
<td class="fillOE" align="right"><b><?php echo F_display_field_name('w_name', 'h_avatar_name'); ?></b></td>
<td class="fillOE" colspan="2"><input type="text" name="avatar_name" id="avatar_name" value="<?php echo htmlentities($avatar_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="20" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_link', 'h_avatar_link'); ?></b></td>
<td class="fillEE" colspan="2"><input type="text" name="avatar_link" id="avatar_link" value="<?php echo $avatar_link; ?>" size="30" maxlength="255" onchange="FJ_show_avatar2()" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_image_dir', 'h_avatar_directory'); ?></b></td>
<td class="fillOE" colspan="2">
<select name="avatar_link_dir" id="avatar_link_dir" size="0" onchange="document.form_avatareditor.avatar_link.value=document.form_avatareditor.avatar_link_dir.options[document.form_avatareditor.avatar_link_dir.selectedIndex].value; FJ_show_avatar()">
<?php
// read directory for files (only graphics files).
$handle = opendir(K_PATH_IMAGES_AVATARS);
echo "<option value=\"\" selected=\"selected\"> - </option>\n";
	while (false !== ($file = readdir($handle))) {
		$path_parts = pathinfo($file);
		$file_ext = strtolower($path_parts['extension']);
		//check file type (GIF, JPG, PNG)
		if(($file_ext=="png")OR($file_ext=="gif")OR($file_ext=="jpg")OR($file_ext=="jpeg")) {
			if($file == $avatar_link) {echo "<option value=\"".$file."\" selected=\"selected\">".$file."</option>\n";}
			else {echo "<option value=\"".$file."\">".$file."</option>\n";}
		}
	}
closedir($handle);
?>
</select>
</td>
</tr>

<!-- Upload file ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_upload', 'h_avatar_upload'); ?></b></td>
<td class="fillEE" colspan="2">
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo K_MAX_UPLOAD_SIZE; ?>" />
<input type="file" name="userfile" id="userfile" size="20" />
</td>
</tr>
<!-- END Upload file ==================== -->


<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo $l['w_width']; ?>:</b></td>
<td class="fillOE" colspan="2"><?php echo $avatar_width; ?> [px]</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo $l['w_height']; ?>:</b></td>
<td class="fillEE" colspan="2"><?php echo $avatar_height; ?> [px]</td>
</tr>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if ($avatar_id) {
	F_submit_button("form_avatareditor","menu_mode",$l['w_update']); 
	F_submit_button("form_avatareditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_avatareditor","menu_mode",$l['w_add']); 
F_submit_button("form_avatareditor","menu_mode",$l['w_clear']); 
?>
</td>
</tr>
</table>
</form>
<!-- ====================================================== -->

<!-- Show selected avatar image ==================== -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
function FJ_show_avatar() {
	document.images.imageavatar.src= "<?php echo K_PATH_IMAGES_AVATARS; ?>"+document.form_avatareditor.avatar_link_dir.options[document.form_avatareditor.avatar_link_dir.selectedIndex].value;
}

function FJ_show_avatar2() {
	document.images.imageavatar.src= "<?php echo K_PATH_IMAGES_AVATARS; ?>"+document.form_avatareditor.avatar_link.value;
}

document.form_avatareditor.avatar_id.focus();
//]]>
</script>
<!-- END Cange focus to avatar_id select -->

<!-- ====================================================== -->
<?php 

require_once('../code/cp_page_footer.'.CP_EXT); 

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
