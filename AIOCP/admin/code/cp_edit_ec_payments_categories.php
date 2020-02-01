<?php
//============================================================+
// File name   : cp_edit_ec_payments_categories.php            
// Begin       : 2002-08-08                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit Payment Categories                       
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_EC_PAYTYPE_CATEGORIES;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_payments_categories_editor'];

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
		$sql = "DELETE FROM ".K_TABLE_EC_PAYMENTS_CATEGORIES." WHERE paycat_id=".$paycat_id."";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		$paycat_id=FALSE;
		break;
		}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_EC_PAYMENTS_CATEGORIES,"paycat_name='".$paycat_name."'","paycat_id",$paycat_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$paycat_name = addslashes(serialize($a_name));
				$sql = "UPDATE IGNORE ".K_TABLE_EC_PAYMENTS_CATEGORIES." SET 
				paycat_name='".$paycat_name."'
				WHERE paycat_id=".$paycat_id."";
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
			$sql = "SELECT paycat_id FROM ".K_TABLE_EC_PAYMENTS_CATEGORIES." WHERE paycat_name='".$paycat_name."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				$paycat_name = addslashes(serialize($a_name));
				$sql = "INSERT IGNORE INTO ".K_TABLE_EC_PAYMENTS_CATEGORIES." (
				paycat_name
				) VALUES (
				'".$paycat_name."'
				)";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$paycat_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$paycat_name = "";
		$a_name = array();
		break;
		}

	default :{ 
		break;
		}

} //end of switch

// Initialize variables
if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!isset($paycat_id) OR (!$paycat_id)) {
			$sql = "SELECT * FROM ".K_TABLE_EC_PAYMENTS_CATEGORIES." ORDER BY paycat_name LIMIT 1";
		} else {
			$sql = "SELECT * FROM ".K_TABLE_EC_PAYMENTS_CATEGORIES." WHERE paycat_id=".$paycat_id." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$paycat_id = $m['paycat_id'];
				$paycat_name = $m['paycat_name'];
				$a_name = unserialize($paycat_name);
			}
			else {
				$paycat_name = "";
				$a_name = array();
			}
		}
		else {
			F_display_db_error();
		}
	}
}

?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_paymentcategoryeditor" id="form_paymentcategoryeditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="a_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT  ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_category', 'h_paycat_select'); ?></b></td>
<td class="fillOE">
<select name="paycat_id" id="paycat_id" size="0" onchange="document.form_paymentcategoryeditor.submit()">
<?php
	$sql = "SELECT * FROM ".K_TABLE_EC_PAYMENTS_CATEGORIES." ORDER BY paycat_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			$select_name = unserialize($m['paycat_name']);
			echo "<option value=\"".$m['paycat_id']."\"";
			if($m['paycat_id'] == $paycat_id) {
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
			echo "<td class=\"fillEO\" align=\"right\"><b>".F_display_field_name('w_name', 'h_paycat_name')."</b></td>";
			echo "<td class=\"fillEE\"><input type=\"text\" name=\"a_name[".$m['language_code']."]\" id=\"a_name_".$m['language_code']."\" value=\"".htmlentities(stripslashes($a_name[$m['language_code']]), ENT_COMPAT, $l['a_meta_charset'])."\" size=\"50\" maxlength=\"255\" /></td>";
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
if ($paycat_id) {
	F_submit_button("form_paymentcategoryeditor","menu_mode",$l['w_update']); 
	F_submit_button("form_paymentcategoryeditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_paymentcategoryeditor","menu_mode",$l['w_add']); 
F_submit_button("form_paymentcategoryeditor","menu_mode",$l['w_clear']); 
?>
</td>
</tr>
</table>
</form>
<!-- ====================================================== -->

<!-- Cange focus to paycat_id select -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_paymentcategoryeditor.paycat_id.focus();

//get next elements ID
function FJ_get_field_index(what) {
	for (var i=0;i<document.form_paymentcategoryeditor.elements.length;i++) {
		if(what == document.form_paymentcategoryeditor.elements[i]) {
			return(i+1);
		}
	}
	return (-1);
}
//]]>
</script>
<!-- END Cange focus to paycat_id select -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
