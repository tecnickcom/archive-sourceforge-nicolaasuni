<?php
//============================================================+
// File name   : cp_edit_menu_list.php                         
// Begin       : 2002-05-11                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit Client Menu List                         
//               (add/remove client menus)                     
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_MENU_LIST;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_menu_client_list'];

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
		<p><?php echo $l['t_warning'].": ".$l['d_menu_delete']; ?></p>
		
		<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_delete" id="form_delete">
		<input type="hidden" name="menu_mode" id="menu_mode" value="forcedelete" />
		<input type="hidden" name="menulst_id" id="menulst_id" value="<?php echo $menulst_id; ?>" />
		<input type="hidden" name="forcedelete" id="forcedelete" value="" />
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
			//menu 1 is reserved as default client menu and cannot be deleted
			if (isset($menulst_id) AND ($menulst_id > 1)) {
				$sql = "DELETE FROM ".K_TABLE_MENU_LIST." WHERE menulst_id=".$menulst_id."";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				//delete menu table
				$sql = "DROP TABLE IF EXISTS ".K_TABLE_MENU_CLIENT."_".$menulst_id."";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
			}
			$menulst_id=FALSE;
			break;
		}
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_MENU_LIST,"menulst_name='".$menulst_name."'","menulst_id",$menulst_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$sql = "UPDATE IGNORE ".K_TABLE_MENU_LIST." SET 
						menulst_name='".$menulst_name."', 
						menulst_option='".$menulst_option."', 
						menulst_style='".$menulst_style."' 
						WHERE menulst_id=".$menulst_id."";
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
			$sql = "SELECT menulst_name FROM ".K_TABLE_MENU_LIST." WHERE menulst_name='".$menulst_name."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add
				$sql = "INSERT IGNORE INTO ".K_TABLE_MENU_LIST." (
						menulst_name, 
						menulst_option,
						menulst_style
						) VALUES (
						'".$menulst_name."', 
						'".$menulst_option."', 
						'".$menulst_style."')";
						
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$menulst_id = F_aiocpdb_insert_id();
					
					//delete menu table if already exist
					$sql = "DROP TABLE IF EXISTS ".K_TABLE_MENU_CLIENT."_".$menulst_id."";
					if(!$r = F_aiocpdb_query($sql, $db)) {
						F_display_db_error();
					}
					
					//add database menu table
					$sql = "CREATE TABLE ".K_TABLE_MENU_CLIENT."_".$menulst_id." (
					menu_id int(10) unsigned NOT NULL auto_increment,	
					menu_language char(3) NOT NULL default 'eng', 
					menu_item tinyint(3) unsigned default '1', 
					menu_sub_id int(10) unsigned default '0', 
					menu_position int(10) unsigned NOT NULL default '1', 
					menu_name varchar(255) NOT NULL default '', 
					menu_description varchar(255) default NULL, 
					menu_link varchar(255) NOT NULL default '', 
					menu_target tinyint(3) unsigned NOT NULL default '5', 
					menu_iconid int(10) unsigned NOT NULL default '1', 
					menu_icon_off int(10) unsigned NOT NULL default '1', 
					menu_icon_over int(10) unsigned NOT NULL default '1', 
					menu_icon_on int(10) unsigned NOT NULL default '1', 
					menu_enabled tinyint(3) unsigned NOT NULL default '1', 
					menu_style_id int(10) unsigned default NULL,
					PRIMARY KEY  (menu_id) ) TYPE=MyISAM COMMENT='Client menu ".$menulst_id."'";
					if(!$r = F_aiocpdb_query($sql, $db)) {
						F_display_db_error();
					}
				}
			}
		}
		break;
	}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$menulst_name = "";
		break;
	}

	default :{ 
		break;
	}

} //end of switch

// Initialize variables
if($formstatus) {
	
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!isset($menulst_id) OR (!$menulst_id)) {
			$sql = "SELECT * FROM ".K_TABLE_MENU_LIST." ORDER BY menulst_name LIMIT 1";
		} else {
			$sql = "SELECT * FROM ".K_TABLE_MENU_LIST." WHERE menulst_id=".$menulst_id." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$menulst_id = $m['menulst_id'];
				$menulst_name = $m['menulst_name'];
				$menulst_option = $m['menulst_option'];
				$menulst_style = $m['menulst_style'];
			}
			else {
				$menulst_name = "";
			}
		}
		else {
			F_display_db_error();
		}
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_menulisteditor" id="form_menulisteditor">

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT menu ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_menu', 'h_menulist_select'); ?></b></td>
<td class="fillOE" colspan="2">
<select name="menulst_id" id="menulst_id" size="0" onchange="document.form_menulisteditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_MENU_LIST." ORDER BY menulst_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['menulst_id']."\"";
		if($m['menulst_id'] == $menulst_id) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['menulst_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT menu ==================== -->

<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE" colspan="2"><hr /></td>
</tr>

<tr class="fillO">
<td class="fillOE" align="right"><b><?php echo F_display_field_name('w_name', 'h_menulist_name'); ?></b></td>
<td class="fillOE" colspan="2"><input type="text" name="menulst_name" id="menulst_name" value="<?php echo htmlentities($menulst_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="20" maxlength="255" /></td>
</tr>

<!-- SELECT option ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_option', 'h_menuopt_select'); ?></b></td>
<td class="fillEE" colspan="2">
<select name="menulst_option" id="menulst_option" size="0">
<?php
$sql = "SELECT * FROM ".K_TABLE_MENU_OPTIONS." ORDER BY menuopt_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['menuopt_id']."\"";
		if($m['menuopt_id'] == $menulst_option) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['menuopt_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT option ==================== -->

<!-- SELECT style ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_style', 'h_menuopt_style'); ?></b></td>
<td class="fillOE" colspan="2">
<select name="menulst_style" id="menulst_style" size="0">
<?php
$sql = "SELECT * FROM ".K_TABLE_MENU_STYLES." ORDER BY menustyle_name";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['menustyle_id']."\"";
		if($m['menustyle_id'] == $menulst_style) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['menustyle_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT style ==================== -->

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if ($menulst_id) {
	F_submit_button("form_menulisteditor","menu_mode",$l['w_update']); 
	if ($menulst_id > 1) { //menu 1 is reserved as default client menu and cannot be deleted
		F_submit_button("form_menulisteditor","menu_mode",$l['w_delete']); 
	}
}
F_submit_button("form_menulisteditor","menu_mode",$l['w_add']); 
F_submit_button("form_menulisteditor","menu_mode",$l['w_clear']); 
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
