<?php
//============================================================+
// File name   : cp_edit_chat_rooms.php                        
// Begin       : 2001-10-04                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit chat rooms for different languages       
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_CHAT_ROOMS;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_chat_rooms'];

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
		$sql = "DELETE FROM ".K_TABLE_CHAT_ROOMS." WHERE chatroom_id=".$chatroom_id."";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		$chatroom_id=FALSE;
		break;
		}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_CHAT_ROOMS, "(chatroom_language='".$chatroom_language."' AND chatroom_name='".$chatroom_name."')", "chatroom_id", $chatroom_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$sql = "UPDATE IGNORE ".K_TABLE_CHAT_ROOMS." SET 
				chatroom_language='".$chatroom_language."', 
				chatroom_name='".$chatroom_name."', 
				chatroom_description='".$chatroom_description."', 
				chatroom_level='".$chatroom_level."' 
				WHERE chatroom_id=".$chatroom_id."";
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
			//check if chatroom_name is unique for selected language
			$sql = "SELECT chatroom_name FROM ".K_TABLE_CHAT_ROOMS." WHERE (chatroom_language='".$chatroom_language."' AND chatroom_name='".$chatroom_name."')";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add
				$sql = "INSERT IGNORE INTO ".K_TABLE_CHAT_ROOMS." (
				chatroom_language, 
				chatroom_name, 
				chatroom_description, 
				chatroom_level
				) VALUES (
				'".$chatroom_language."', 
				'".$chatroom_name."', 
				'".$chatroom_description."', 
				'".$chatroom_level."')";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$chatroom_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$chatroom_name = "";
		$chatroom_description = "";
		$chatroom_level = 0;
		break;
		}

	default :{ 
		break;
		}

} //end of switch

// Initialize variables
if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!isset($chatroom_language) OR (!$chatroom_language)) {
			$sql = "SELECT * FROM ".K_TABLE_CHAT_ROOMS." ORDER BY chatroom_language, chatroom_name LIMIT 1";
		} else {
			if ((!isset($chatroom_id) OR (!$chatroom_id)) OR (isset($changelanguage) AND $changelanguage)) {
				$sql = "SELECT * FROM ".K_TABLE_CHAT_ROOMS." WHERE chatroom_language='".$chatroom_language."' ORDER BY chatroom_name LIMIT 1";
			} else {$sql = "SELECT * FROM ".K_TABLE_CHAT_ROOMS." WHERE chatroom_id=".$chatroom_id." LIMIT 1";}
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$chatroom_id = $m['chatroom_id'];
				$chatroom_language = $m['chatroom_language'];
				$chatroom_name = $m['chatroom_name'];
				$chatroom_description = $m['chatroom_description'];
				$chatroom_level = $m['chatroom_level'];
			}
			else {
				$chatroom_name = "";
				$chatroom_description = "";
				$chatroom_level = 0;
			}
		}
		else {
			F_display_db_error();
		}
	}
}
if (!isset($chatroom_language)) {
	$chatroom_language = $selected_language;
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_chatroomeditor" id="form_chatroomeditor">

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT  language ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_language', 'h_chat_language'); ?></b></td>
<td class="fillOE">
<input type="hidden" name="changelanguage" id="changelanguage" value="0" />
<select name="chatroom_language" id="chatroom_language" size="0" onchange="document.form_chatroomeditor.changelanguage.value=1; document.form_chatroomeditor.submit()">
<?php
	$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"".$m['language_code']."\"";
			if($m['language_code'] == $chatroom_language) {
				echo " selected=\"selected\"";
			}
			echo ">".htmlentities($m['language_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
		}
	}
	else {
		F_display_db_error();
	}
?>
</select>
</td>
</tr>

<!-- END SELECT language ==================== -->

<!-- SELECT ROOM ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_room', 'h_chat_room'); ?></b></td>
<td class="fillEE">
<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="chatroom_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />
<select name="chatroom_id" id="chatroom_id" size="0" onchange="document.form_chatroomeditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_CHAT_ROOMS." WHERE chatroom_language='".$chatroom_language."' ORDER BY chatroom_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['chatroom_id']."\"";
		if($m['chatroom_id'] == $chatroom_id) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['chatroom_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT ROOM ==================== -->

<tr class="fillO">
<td class="fillOO">&nbsp;</td>
<td class="fillOE"><hr /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_name', 'h_chat_name'); ?></b></td>
<td class="fillEE"><input type="text" name="chatroom_name" id="chatroom_name" value="<?php echo htmlentities($chatroom_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="32" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b><?php echo F_display_field_name('w_description', 'h_chat_description'); ?></b>
<br />
<?php 
$doc_charset = F_word_language($chatroom_language, "a_meta_charset");
F_html_button("page", "form_chatroomeditor", "chatroom_description", $doc_charset);

$current_ta_code = $chatroom_description;
if(!$formstatus) {
	$current_ta_code = stripslashes($current_ta_code);
}
?>
</td>
<td class="fillOE"><textarea cols="30" rows="5" name="chatroom_description" id="chatroom_description"><?php echo htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset); ?></textarea></td>
</tr>

<!-- SELECT LEVEL ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_level', 'h_chat_level'); ?></b></td>
<td class="fillEE"><select name="chatroom_level" id="chatroom_level" size="0">
<?php
$sql = "SELECT * FROM ".K_TABLE_LEVELS." ORDER BY level_code ";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['level_code']."\"";
		if($m['level_code'] == $chatroom_level) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['level_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT LEVEL ==================== -->

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if (isset($chatroom_id) AND ($chatroom_id > 0)) {
	F_submit_button("form_chatroomeditor","menu_mode",$l['w_update']); 
	F_submit_button("form_chatroomeditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_chatroomeditor","menu_mode",$l['w_add']); 
F_submit_button("form_chatroomeditor","menu_mode",$l['w_clear']); 
?>

</td>
</tr>
</table>
</form>
<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
