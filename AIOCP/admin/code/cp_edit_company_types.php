<?php
//============================================================+
// File name   : cp_edit_company_types.php                     
// Begin       : 2003-11-05                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit Company Types                            
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_COMPANY_TYPES;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_company_types_editor'];

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
		F_stripslashes_formfields(); // Delete
		$sql = "DELETE FROM ".K_TABLE_USERS_COMPANY_TYPES." WHERE comptype_id=".$comptype_id."";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		$comptype_id=FALSE;
		break;
		}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_USERS_COMPANY_TYPES,"comptype_name='".$comptype_name."'","comptype_id",$comptype_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$comptype_name = addslashes(serialize($a_name));
				$comptype_description = addslashes(serialize($a_description));
				$sql = "UPDATE IGNORE ".K_TABLE_USERS_COMPANY_TYPES." SET 
				comptype_name='".$comptype_name."',
				comptype_description='".$comptype_description."',
				comptype_discount='".$comptype_discount."'
				WHERE comptype_id=".$comptype_id."";
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
			$sql = "SELECT comptype_id FROM ".K_TABLE_USERS_COMPANY_TYPES." WHERE comptype_name='".$comptype_name."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				$comptype_name = addslashes(serialize($a_name));
				$comptype_description = addslashes(serialize($a_description));
				$sql = "INSERT IGNORE INTO ".K_TABLE_USERS_COMPANY_TYPES." (
				comptype_name,
				comptype_description,
				comptype_discount
				) VALUES (
				'".$comptype_name."',
				'".$comptype_description."',
				'".$comptype_discount."'
				)";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$comptype_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
	}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$comptype_name = "";
		$comptype_description = "";
		$comptype_discount = 0;
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
		if(!isset($comptype_id) OR (!$comptype_id)) {
			$sql = "SELECT * FROM ".K_TABLE_USERS_COMPANY_TYPES." ORDER BY comptype_name LIMIT 1";
		} else {
			$sql = "SELECT * FROM ".K_TABLE_USERS_COMPANY_TYPES." WHERE comptype_id=".$comptype_id." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$comptype_id = $m['comptype_id'];
				$comptype_name = $m['comptype_name'];
				$comptype_description = $m['comptype_description'];
				$comptype_discount = $m['comptype_discount'];
				$a_name = unserialize($comptype_name);
				$a_description = unserialize($comptype_description);
			}
			else {
				$comptype_name = "";
				$comptype_description = "";
				$comptype_discount = 0;
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
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_copanytypeseditor" id="form_copanytypeseditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="a_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT  ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_type', 'h_comptype_select'); ?></b></td>
<td class="fillOE">
<select name="comptype_id" id="comptype_id" size="0" onchange="document.form_copanytypeseditor.submit()">
<?php
	$sql = "SELECT * FROM ".K_TABLE_USERS_COMPANY_TYPES." ORDER BY comptype_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			$select_name = unserialize($m['comptype_name']);
			echo "<option value=\"".$m['comptype_id']."\"";
			if($m['comptype_id'] == $comptype_id) {
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

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_discount', 'h_comptype_discount'); ?></b></td>
<td class="fillOE"><input type="text" name="comptype_discount" id="comptype_discount" value="<?php echo $comptype_discount; ?>" size="20" maxlength="255" /> <b>[%]</b></td>
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
			echo "<td class=\"fillEO\" align=\"right\"><b>".F_display_field_name('w_name', 'h_comptype_name')."</b></td>";
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
			echo "<td class=\"fillOO\" align=\"right\" valign=\"top\"><b>".F_display_field_name('w_description', 'h_comptype_description')."</b><br />";
?>
<input type="button" name="htmleditor_<?php echo $m['language_code']; ?>" id="htmleditor_<?php echo $m['language_code']; ?>" value="<?php echo $l['w_button_html_editor']; ?>" onclick="htmlWindow=window.open('cp_edit_html.<?php echo CP_EXT; ?>?templates=0&amp;callingform=form_copanytypeseditor&amp;callingfield=elements['+FJ_get_field_index(this)+']<?php echo $doc_charset_url; ?>','htmlWindow','dependent,height=500,width=800,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')" />

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
if (isset($comptype_id) AND $comptype_id) {
	F_submit_button("form_copanytypeseditor","menu_mode",$l['w_update']); 
	F_submit_button("form_copanytypeseditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_copanytypeseditor","menu_mode",$l['w_add']); 
F_submit_button("form_copanytypeseditor","menu_mode",$l['w_clear']); 
?>
</td>
</tr>
</table>
</form>
<!-- ====================================================== -->

<!-- Cange focus to comptype_id select -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_copanytypeseditor.comptype_id.focus();

//get next elements ID
function FJ_get_field_index(what) {
	for (var i=0;i<document.form_copanytypeseditor.elements.length;i++) {
		if(what == document.form_copanytypeseditor.elements[i]) {
			return(i+1);
		}
	}
	return (-1);
}
//]]>
</script>
<!-- END Cange focus to comptype_id select -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
