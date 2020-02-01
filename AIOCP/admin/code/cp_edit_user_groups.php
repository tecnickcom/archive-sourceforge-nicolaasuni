<?php
//============================================================+
// File name   : cp_edit_user_groups.php                       
// Begin       : 2003-11-05                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit User Groups                              
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_USER_GROUPS;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_users_groups_editor'];

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
		F_stripslashes_formfields(); // ask confirmation
		F_print_error("WARNING", $l['m_delete_confirm']);
		?>		
		<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_delete" id="form_delete">
		<input type="hidden" name="menu_mode" id="menu_mode" value="forcedelete" />
		<input type="hidden" name="usrgrp_id" id="usrgrp_id" value="<?php echo $usrgrp_id; ?>" />
		<input type="hidden" name="forcedelete" id="forcedelete" value="" />
		<?php 
		F_submit_button("form_delete","forcedelete",$l['w_delete']);
		F_submit_button("form_delete","forcedelete",$l['w_cancel']);
		?>
		</form>
		
		<?php
		break;
	}

	case "forcedelete": { // Delete newscat
		if($forcedelete == $l['w_delete']) { //check if delete button has been pushed (redundant check)
			$sql = "DELETE FROM ".K_TABLE_USERS_GROUPS." WHERE usrgrp_id=".$usrgrp_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			$usrgrp_id=FALSE;
		}
		break;
	}
	
	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_USERS_GROUPS,"usrgrp_name='".$usrgrp_name."'","usrgrp_id",$usrgrp_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$usrgrp_name = addslashes(serialize($a_name));
				$usrgrp_description = addslashes(serialize($a_description));
				$sql = "UPDATE IGNORE ".K_TABLE_USERS_GROUPS." SET 
				usrgrp_name='".$usrgrp_name."',
				usrgrp_description='".$usrgrp_description."'
				WHERE usrgrp_id=".$usrgrp_id."";
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
			//check if name/date is unique
			$sql = "SELECT usrgrp_id FROM ".K_TABLE_USERS_GROUPS." WHERE usrgrp_name='".$usrgrp_name."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				$usrgrp_name = addslashes(serialize($a_name));
				$usrgrp_description = addslashes(serialize($a_description));
				$sql = "INSERT IGNORE INTO ".K_TABLE_USERS_GROUPS." (
				usrgrp_name,
				usrgrp_description
				) VALUES (
				'".$usrgrp_name."',
				'".$usrgrp_description."'
				)";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$usrgrp_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
	}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$usrgrp_name = "";
		$usrgrp_description = "";
		$a_name = array();
		$a_description = array();
		break;
	}

	default :{ 
		break;
	}

} //end of switch

// Initialize variables
if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!isset($usrgrp_id) OR (!$usrgrp_id)) {
			$sql = "SELECT * FROM ".K_TABLE_USERS_GROUPS." ORDER BY usrgrp_name LIMIT 1";
		} else {
			$sql = "SELECT * FROM ".K_TABLE_USERS_GROUPS." WHERE usrgrp_id=".$usrgrp_id." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$usrgrp_id = $m['usrgrp_id'];
				$usrgrp_name = $m['usrgrp_name'];
				$usrgrp_description = $m['usrgrp_description'];
				$a_name = unserialize($usrgrp_name);
				$a_description = unserialize($usrgrp_description);
			}
			else {
				$usrgrp_name = "";
				$usrgrp_description = "";
				$a_name = array();
				$a_description = array();
			}
		}
		else {
			F_display_db_error();
		}
	}
}

?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_usergroupeditor" id="form_usergroupeditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="a_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT  ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_group', 'h_usrgrp_select'); ?></b></td>
<td class="fillOE">
<select name="usrgrp_id" id="usrgrp_id" size="0" onchange="document.form_usergroupeditor.submit()">
<?php
	$sql = "SELECT * FROM ".K_TABLE_USERS_GROUPS." ORDER BY usrgrp_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			$select_name = unserialize($m['usrgrp_name']);
			echo "<option value=\"".$m['usrgrp_id']."\"";
			if($m['usrgrp_id'] == $usrgrp_id) {
				echo " selected=\"selected\"";
			}
			echo ">".htmlentities($select_name[$selected_language], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
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

<!-- iterate for each language ==================== -->
<?php
	$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<tr class=\"fillO\">";
			echo "<td class=\"fillOO\"><hr /></td>";
			echo "<td class=\"fillOE\"><b>".$m['language_name']."</b></td>";
			echo "</tr>";
			echo "<tr class=\"fillE\">";
			echo "<td class=\"fillEO\" align=\"right\"><b>".F_display_field_name('w_name', 'h_usrgrp_name')."</b></td>";
			if (!isset($a_name[$m['language_code']])) {
				$a_name[$m['language_code']] = "";
			}
			echo "<td class=\"fillEE\"><input type=\"text\" name=\"a_name[".$m['language_code']."]\" id=\"a_name_".$m['language_code']."\" value=\"".htmlentities(stripslashes($a_name[$m['language_code']]), ENT_COMPAT, $l['a_meta_charset'])."\" size=\"50\" maxlength=\"255\" /></td>";
echo "</tr>";
			
			$doc_charset = F_word_language($m['language_code'], "a_meta_charset");
			if ($doc_charset) {
				$doc_charset_url = "&amp;charset=".$doc_charset;
			}
			else {
				$doc_charset_url = "";
			}
			
			echo "<tr class=\"fillO\">";
			echo "<td class=\"fillOO\" align=\"right\" valign=\"top\"><b>".F_display_field_name('w_description', 'h_usrgrp_description')."</b><br />";
?>
<input type="button" name="htmleditor_<?php echo $m['language_code']; ?>" id="htmleditor_<?php echo $m['language_code']; ?>" value="<?php echo $l['w_button_html_editor']; ?>" onclick="htmlWindow=window.open('cp_edit_html.<?php echo CP_EXT; ?>?templates=0&amp;callingform=form_usergroupeditor&amp;callingfield=elements['+FJ_get_field_index(this)+']<?php echo $doc_charset_url; ?>','htmlWindow','dependent,height=500,width=800,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')" />

<?php
echo "</td>";
if (!isset($a_description[$m['language_code']])) {
	$a_description[$m['language_code']] = "";
}
$current_ta_code = $a_description[$m['language_code']];
$current_ta_code = stripslashes($current_ta_code);
echo "<td class=\"fillOE\"><textarea cols=\"50\" rows=\"4\" name=\"a_description[".$m['language_code']."]\" id=\"a_description_".$m['language_code']."\">".htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset)."</textarea></td>";
echo "</tr>";

		}
	}
	else {
		F_display_db_error();
	}
?>
<!-- END iterate for each language ==================== -->

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if (isset($usrgrp_id) AND $usrgrp_id) {
	F_submit_button("form_usergroupeditor","menu_mode",$l['w_update']); 
	F_submit_button("form_usergroupeditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_usergroupeditor","menu_mode",$l['w_add']); 
F_submit_button("form_usergroupeditor","menu_mode",$l['w_clear']); 
?>
</td>
</tr>
</table>
</form>
<!-- ====================================================== -->

<!-- Cange focus to usrgrp_id select -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_usergroupeditor.usrgrp_id.focus();

//get next elements ID
function FJ_get_field_index(what) {
	for (var i=0;i<document.form_usergroupeditor.elements.length;i++) {
		if(what == document.form_usergroupeditor.elements[i]) {
			return(i+1);
		}
	}
	return (-1);
}
//]]>
</script>
<!-- END Cange focus to usrgrp_id select -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
