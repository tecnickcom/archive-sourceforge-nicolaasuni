<?php
//============================================================+
// File name   : cp_edit_ec_transactions_types.php             
// Begin       : 2002-07-25                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit Transaction Types                        
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_EC_TRANSACTIONS_TYPES;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_ec_transaction_type_editor'];

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
		$sql = "DELETE FROM ".K_TABLE_EC_TRANSACTIONS_TYPES." WHERE transtype_id=".$transtype_id."";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		$transtype_id=FALSE;
		break;
		}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_EC_TRANSACTIONS_TYPES,"transtype_name='".$transtype_name."'","transtype_id",$transtype_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$transtype_name = addslashes(serialize($a_name));
				$sql = "UPDATE IGNORE ".K_TABLE_EC_TRANSACTIONS_TYPES." SET 
				transtype_name='".$transtype_name."'
				WHERE transtype_id=".$transtype_id."";
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
			$sql = "SELECT transtype_id FROM ".K_TABLE_EC_TRANSACTIONS_TYPES." WHERE transtype_name='".$transtype_name."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				$transtype_name = addslashes(serialize($a_name));
				$sql = "INSERT IGNORE INTO ".K_TABLE_EC_TRANSACTIONS_TYPES." (
				transtype_name
				) VALUES (
				'".$transtype_name."'
				)";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$transtype_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$transtype_name = "";
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
		if(!isset($transtype_id) OR (!$transtype_id)) {
			$sql = "SELECT * FROM ".K_TABLE_EC_TRANSACTIONS_TYPES." ORDER BY transtype_name LIMIT 1";
		}
		else {$sql = "SELECT * FROM ".K_TABLE_EC_TRANSACTIONS_TYPES." WHERE transtype_id=".$transtype_id." LIMIT 1";}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$transtype_id = $m['transtype_id'];
				$transtype_name = $m['transtype_name'];
				$a_name = unserialize($transtype_name);
			}
			else {
				$transtype_name = "";
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
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_transactiontypeeditor" id="form_transactiontypeeditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="a_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT  ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_transaction_type', 'h_transtype_select'); ?></b></td>
<td class="fillOE">
<select name="transtype_id" id="transtype_id" size="0" onchange="document.form_transactiontypeeditor.submit()">
<?php
	$sql = "SELECT * FROM ".K_TABLE_EC_TRANSACTIONS_TYPES." ORDER BY transtype_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			$select_name = unserialize($m['transtype_name']);
			echo "<option value=\"".$m['transtype_id']."\"";
			if($m['transtype_id'] == $transtype_id) {
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
			echo "<td class=\"fillEO\" align=\"right\" valign=\"top\"><b>".F_display_field_name('w_name', 'h_transtype_name')."</b><br />";
	
	$doc_charset = F_word_language($m['language_code'], "a_meta_charset");
	if ($doc_charset) {
		$doc_charset_url = "&amp;charset=".$doc_charset;
	}
	else {
		$doc_charset_url = "";
	}
	
echo "</td>";

$current_ta_code = $a_name[$m['language_code']];
$current_ta_code = stripslashes($current_ta_code);
echo "<td class=\"fillEE\"><textarea cols=\"50\" rows=\"2\" name=\"a_name[".$m['language_code']."]\" id=\"a_name_".$m['language_code']."\">".htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset)."</textarea></td>";
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
if ($transtype_id) {
	F_submit_button("form_transactiontypeeditor","menu_mode",$l['w_update']); 
	F_submit_button("form_transactiontypeeditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_transactiontypeeditor","menu_mode",$l['w_add']); 
F_submit_button("form_transactiontypeeditor","menu_mode",$l['w_clear']); 
?>
</td>
</tr>
</table>
</form>
<!-- ====================================================== -->

<!-- Cange focus to transtype_id select -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_transactiontypeeditor.transtype_id.focus();

//get next elements ID
function FJ_get_field_index(what) {
	for (var i=0;i<document.form_transactiontypeeditor.elements.length;i++) {
		if(what == document.form_transactiontypeeditor.elements[i]) {
			return(i+1);
		}
	}
	return (-1);
}
//]]>
</script>
<!-- END Cange focus to transtype_id select -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
