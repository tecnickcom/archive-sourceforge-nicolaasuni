<?php
//============================================================+
// File name   : cp_edit_os.php                                
// Begin       : 2001-11-11                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit Operative Systems                        
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_OS;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_software_os_editor'];

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
		$sql = "DELETE FROM ".K_TABLE_SOFTWARE_OS." WHERE os_id=".$os_id."";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		$os_id=FALSE;
		break;
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_SOFTWARE_OS, "os_name='".$os_name."'", "os_id", $os_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$sql = "UPDATE IGNORE ".K_TABLE_SOFTWARE_OS." SET 
				os_name='".$os_name."', 
				os_link='".$os_link."' 
				WHERE os_id=".$os_id."";
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
			//check if os_name is unique
			$sql = "SELECT os_name FROM ".K_TABLE_SOFTWARE_OS." WHERE os_name='".$os_name."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				$sql = "INSERT IGNORE INTO ".K_TABLE_SOFTWARE_OS." (
				os_name, 
				os_link
				) VALUES (
				'".$os_name."', 
				'".$os_link."')";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$os_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
	}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$os_name = "";
		$os_link = "";
		break;
	}

	default :{
		break;
	}

} //end of switch

// Initialize variables
if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!isset($os_id) OR (!$os_id)) {
			$sql = "SELECT * FROM ".K_TABLE_SOFTWARE_OS." ORDER BY os_name LIMIT 1";
		} else {
			$sql = "SELECT * FROM ".K_TABLE_SOFTWARE_OS." WHERE os_id=".$os_id." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$os_id = $m['os_id'];
				$os_name = $m['os_name'];
				$os_link = $m['os_link'];
			}
		}
		else {
			F_display_db_error();
		}
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_oseditor" id="form_oseditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="os_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT os ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_os', 'h_os_select'); ?></b></td>
<td class="fillOE">
<select name="os_id" id="os_id" size="0" onchange="document.form_oseditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_SOFTWARE_OS." ORDER BY os_name";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['os_id']."\"";
		if($m['os_id'] == $os_id) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['os_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT os ==================== -->

<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE"><hr /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_name', 'h_os_name'); ?></b></td>
<td class="fillOE"><input type="text" name="os_name" id="os_name" value="<?php echo htmlentities($os_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_link', 'h_os_link'); ?></b></td>
<td class="fillEE"><input type="text" name="os_link" id="os_link" value="<?php echo $os_link; ?>" size="30" maxlength="255" /></td>
</tr>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if ($os_id) {
	F_submit_button("form_oseditor","menu_mode",$l['w_update']); 
	F_submit_button("form_oseditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_oseditor","menu_mode",$l['w_add']); 
F_submit_button("form_oseditor","menu_mode",$l['w_clear']); 
?>
</td>

</tr>
</table>
</form>
<!-- ====================================================== -->

<!-- Cange focus to os_id select -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_oseditor.os_id.focus();
//]]>
</script>
<!-- END Cange focus to os_id select -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
