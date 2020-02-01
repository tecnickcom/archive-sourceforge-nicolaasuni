<?php
//============================================================+
// File name   : cp_edit_banner_zone.php                       
// Begin       : 2002-04-28                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit banner zones                             
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_BANNER_ZONE;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

require_once('../../shared/code/cp_functions_form.'.CP_EXT);

$thispage_title = $l['t_banner_zone_editor'];

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
		$sql = "DELETE FROM ".K_TABLE_BANNERS_ZONES." WHERE banzone_id=".$banzone_id."";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		$banzone_id=FALSE;
		break;
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_BANNERS_ZONES, "banzone_name='".$banzone_name."'", "banzone_id", $banzone_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$sql = "UPDATE IGNORE ".K_TABLE_BANNERS_ZONES." SET banzone_name='".$banzone_name."' WHERE banzone_id=".$banzone_id."";
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
			//check if banzone_name is unique
			$sql = "SELECT banzone_name FROM ".K_TABLE_BANNERS_ZONES." WHERE banzone_name='".$banzone_name."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				$sql = "INSERT IGNORE INTO ".K_TABLE_BANNERS_ZONES." (banzone_name) VALUES ('".$banzone_name."')";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$banzone_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
	}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$banzone_name = "";
	break;
		}

	default :{ 
		break;
	}

} //end of switch

// Initialize variables
if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!isset($banzone_id) OR (!$banzone_id)) {
			$sql = "SELECT * FROM ".K_TABLE_BANNERS_ZONES." ORDER BY banzone_name LIMIT 1";
		} else {
			$sql = "SELECT * FROM ".K_TABLE_BANNERS_ZONES." WHERE banzone_id=".$banzone_id." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$banzone_id = $m['banzone_id'];
				$banzone_name = $m['banzone_name'];
			}
		}
		else {
			F_display_db_error();
		}
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_bannerzoneeditor" id="form_bannerzoneeditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="banzone_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT zone ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_zone', 'h_banner_zone_select'); ?></b></td>
<td class="fillOE">
<select name="banzone_id" id="banzone_id" size="0" onchange="document.form_bannerzoneeditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_BANNERS_ZONES." ORDER BY banzone_name";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['banzone_id']."\"";
		if($m['banzone_id'] == $banzone_id) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['banzone_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT zone ==================== -->

<?php
if (!isset($banzone_name)) {
	$banzone_name = "";
?>
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_name', 'h_banner_zone_name'); ?></b></td>
<td class="fillEE"><input type="text" name="banzone_name" id="banzone_name" value="<?php echo htmlentities($banzone_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="20" maxlength="255" /></td>
</tr>
<?php
}
?>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if (isset($banzone_id) AND ($banzone_id > 1)) { //the default zone could not be modified
	F_submit_button("form_bannerzoneeditor","menu_mode",$l['w_update']); 
	F_submit_button("form_bannerzoneeditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_bannerzoneeditor","menu_mode",$l['w_add']); 
F_submit_button("form_bannerzoneeditor","menu_mode",$l['w_clear']); 
?>
</td>

</tr>
</table>
</form>
<!-- ====================================================== -->

<!-- Cange focus to banzone_id select -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_bannerzoneeditor.banzone_id.focus();
//]]>
</script>
<!-- END Cange focus to banzone_id select -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
