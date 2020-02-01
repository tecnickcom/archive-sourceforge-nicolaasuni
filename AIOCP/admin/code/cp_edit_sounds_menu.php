<?php
//============================================================+
// File name   : cp_edit_sounds_menu.php                       
// Begin       : 2001-04-15                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit sound clips for menus                    
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_SOUNDS_MENU;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_sounds_menu_editor'];

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
		$sql = "DELETE FROM ".K_TABLE_SOUNDS_MENU." WHERE sound_id=".$sound_id."";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		$sound_id=FALSE;
		break;
		}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update icon
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_SOUNDS_MENU, "sound_name='".$sound_name."'", "sound_id", $sound_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				if($_FILES['userfile']['name']) {
					$sound_link = F_upload_file("userfile", K_PATH_SOUNDS_MENU);
				}
				$sql = "UPDATE IGNORE ".K_TABLE_SOUNDS_MENU." SET 
				sound_name='".$sound_name."', 
				sound_link='".$sound_link."' 
				WHERE sound_id=".$sound_id."";
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
			//check if sound_name is unique
			$sql = "SELECT sound_name FROM ".K_TABLE_SOUNDS_MENU." WHERE sound_name='".$sound_name."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
					F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				if($_FILES['userfile']['name']) {
					$sound_link = F_upload_file("userfile", K_PATH_SOUNDS_MENU);
				}
				$sql = "INSERT IGNORE INTO ".K_TABLE_SOUNDS_MENU." (
				sound_name, 
				sound_link
				) VALUES (
				'".$sound_name."', 
				'".$sound_link."')";
				if(!$r = F_aiocpdb_query($sql, $db))  {
					F_display_db_error();
				}
				else {
					$sound_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$sound_name = "";
		$sound_link = "";
		break;
		}

	default :{ 
		break;
		}

} //end of switch

// Initialize variables
if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!isset($sound_id) OR (!$sound_id)) {
			$sql = "SELECT * FROM ".K_TABLE_SOUNDS_MENU." ORDER BY sound_name LIMIT 1";
		} else {
			$sql = "SELECT * FROM ".K_TABLE_SOUNDS_MENU." WHERE sound_id=".$sound_id." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$sound_id = $m['sound_id'];
				$sound_name = $m['sound_name'];
				$sound_link = $m['sound_link'];
			}
			else {
				$sound_id = 1;
				$sound_name = "- NO SOUND -";
				$sound_link = "";
			}
		}
		else {
			F_display_db_error();
		}
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_soundmenueditor" id="form_soundmenueditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="sound_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT SOUND ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_sound', 'h_sound_select'); ?></b></td>
<td class="fillOE">
<select name="sound_id" id="sound_id" size="0" onchange="document.form_soundmenueditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_SOUNDS_MENU." ORDER BY sound_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['sound_id']."\"";
		if($m['sound_id'] == $sound_id) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['sound_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
<?php
if ($sound_link) { //display a button to listen audio
	echo "<applet code=\"com.tecnick.jplaysound.JPlaySound.class\" codebase=\"".K_PATH_JAVA."\" archive=\"jplaysound.jar\" align=\"top\" width=\"20\" height=\"20\" hspace=\"0\" vspace=\"0\" name=\"playSound\" id=\"playSound\"><param name=\"soundfile\" value=\"".K_PATH_SOUNDS_MENU.$sound_link."\"><param name=\"label\" value=\">\"></applet>";
}
?>
</td>
</tr>
<!-- END SELECT SOUND ==================== -->

<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE"><hr /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_name', 'h_sound_name'); ?></b></td>
<td class="fillOE"><input type="text" name="sound_name" id="sound_name" value="<?php echo htmlentities($sound_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="20" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_link', 'h_sound_link'); ?></b></td>
<td class="fillEE"><input type="text" name="sound_link" id="sound_link" value="<?php echo $sound_link; ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_sound_dir', 'h_sound_directory'); ?></b></td>
<td class="fillOE">
<select name="sound_link_dir" id="sound_link_dir" size="0" onchange="document.form_soundmenueditor.sound_link.value=document.form_soundmenueditor.sound_link_dir.options[document.form_soundmenueditor.sound_link_dir.selectedIndex].value;">
<?php
// read directory for files (only graphics files).
$handle = opendir(K_PATH_SOUNDS_MENU);
echo "<option value=\"\" selected=\"selected\"> - </option>\n";
	while (false !== ($file = readdir($handle))) {
		$path_parts = pathinfo($file);
		$file_ext = strtolower($path_parts['extension']);
		//check file type (AU)
		if ($file_ext=="au") {
			echo "<option value=\"".$file."\"";
			if($file == $sound_link) {
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
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_upload', 'h_sound_upload'); ?></b></td>
<td class="fillEE">
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo K_MAX_UPLOAD_SIZE; ?>" />
<input type="file" name="userfile" id="userfile" size="20" />
</td>
</tr>
<!-- END Upload file ==================== -->

</table>

</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if ($sound_id) {
	F_submit_button("form_soundmenueditor","menu_mode",$l['w_update']); 
	F_submit_button("form_soundmenueditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_soundmenueditor","menu_mode",$l['w_add']); 
F_submit_button("form_soundmenueditor","menu_mode",$l['w_clear']); 
?>

</td>
</tr>

</table>
</form>

<!-- ====================================================== -->

<!-- Cange focus to sound_id select -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_soundmenueditor.sound_id.focus();
//]]>
</script>
<!-- END Cange focus to sound_id select -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
