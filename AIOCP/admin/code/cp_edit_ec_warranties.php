<?php
//============================================================+
// File name   : cp_edit_ec_warranties.php                     
// Begin       : 2002-07-10                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit products warranty certificates           
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_EC_WARRANTIES;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_warranty_editor'];

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
		$sql = "DELETE FROM ".K_TABLE_EC_WARRANTIES." WHERE warranty_id=".$warranty_id."";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		$warranty_id=FALSE;
		break;
		}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_EC_WARRANTIES,"warranty_name='".$warranty_name."'","warranty_id",$warranty_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$warranty_description = addslashes(serialize($a_description));
				$sql = "UPDATE IGNORE ".K_TABLE_EC_WARRANTIES." SET 
				warranty_name='".$warranty_name."', 
				warranty_description='".$warranty_description."' 
				WHERE warranty_id=".$warranty_id."";
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
			$sql = "SELECT warranty_id FROM ".K_TABLE_EC_WARRANTIES." WHERE warranty_name='".$warranty_name."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				$warranty_description = addslashes(serialize($a_description));
				$sql = "INSERT IGNORE INTO ".K_TABLE_EC_WARRANTIES." (
				warranty_name, 
				warranty_description
				) VALUES (
				'".$warranty_name."', 
				'".$warranty_description."')";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$warranty_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$warranty_name = "";
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
		if(!isset($warranty_id) OR (!$warranty_id)) {
			$sql = "SELECT * FROM ".K_TABLE_EC_WARRANTIES." ORDER BY warranty_name LIMIT 1";
		} else {
			$sql = "SELECT * FROM ".K_TABLE_EC_WARRANTIES." WHERE warranty_id=".$warranty_id." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$warranty_id = $m['warranty_id'];
				$warranty_name = $m['warranty_name'];
				$warranty_description = $m['warranty_description'];
				$a_description = unserialize($warranty_description);
			}
			else {
				$warranty_name = "";
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
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_warrantyeditor" id="form_warrantyeditor">

<!-- comma separated list of required fields -->

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT download ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_warranty', 'h_warranty_select'); ?></b></td>
<td class="fillOE">
<select name="warranty_id" id="warranty_id" size="0" onchange="document.form_warrantyeditor.submit()">
<?php
	$sql = "SELECT * FROM ".K_TABLE_EC_WARRANTIES." ORDER BY warranty_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"".$m['warranty_id']."\"";
			if($m['warranty_id'] == $warranty_id) {
				echo " selected=\"selected\"";
			}
			echo ">[".$m['warranty_id']."] ".htmlentities($m['warranty_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
		}
	}
	else {
		F_display_db_error();
	}
?>
</select>
</td>
</tr>
<!-- END SELECT download ==================== -->

<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE"><hr /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_name', 'h_warranty_name'); ?></b></td>
<td class="fillOE"><input type="text" name="warranty_name" id="warranty_name" value="<?php echo htmlentities($warranty_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="50" maxlength="255" /></td>
</tr>

<!-- iterate for each language ==================== -->
<?php
	$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<tr class=\"fillE\">";
			echo "<td class=\"fillEO\"><hr /></td>";
			echo "<td class=\"fillEE\"><b>".$m['language_name']."</b></td>";
			echo "</tr>";
			echo "<tr class=\"fillO\">";
			echo "<td class=\"fillOO\" align=\"right\" valign=\"top\"><b>".F_display_field_name('w_description', 'h_warranty_description')."</b><br />";
	
	$doc_charset = F_word_language($m['language_code'], "a_meta_charset");
	if ($doc_charset) {
		$doc_charset_url = "&amp;charset=".$doc_charset;
	}
	else {
		$doc_charset_url = "";
	}
?>
<input type="button" name="htmleditor_<?php echo $m['language_code']; ?>" id="htmleditor_<?php echo $m['language_code']; ?>" value="<?php echo $l['w_button_html_editor']; ?>" onclick="htmlWindow=window.open('cp_edit_html.<?php echo CP_EXT; ?>?templates=0&amp;callingform=form_warrantyeditor&amp;callingfield=elements['+FJ_get_field_index(this)+']<?php echo $doc_charset_url; ?>','htmlWindow','dependent,height=500,width=800,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')" />

<?php
echo "</td>";
if (isset($a_description[$m['language_code']])) {
	$current_ta_code = $a_description[$m['language_code']];
} else {
	$current_ta_code = "";
}
$current_ta_code = stripslashes($current_ta_code);
echo "<td class=\"fillOE\"><textarea cols=\"50\" rows=\"5\" name=\"a_description[".$m['language_code']."]\" id=\"a_description_".$m['language_code']."\">".htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset)."</textarea></td>";
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
if (isset($warranty_id) AND ($warranty_id > 0)) {
	F_submit_button("form_warrantyeditor","menu_mode",$l['w_update']); 
	F_submit_button("form_warrantyeditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_warrantyeditor","menu_mode",$l['w_add']); 
F_submit_button("form_warrantyeditor","menu_mode",$l['w_clear']); 
?>
</td>
</tr>
</table>
</form>
<!-- ====================================================== -->

<!-- Cange focus to warranty_id select -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_warrantyeditor.warranty_id.focus();

//get next elements ID
function FJ_get_field_index(what) {
	for (var i=0;i<document.form_warrantyeditor.elements.length;i++) {
		if(what == document.form_warrantyeditor.elements[i]) {
			return(i+1);
		}
	}
	return (-1);
}
//]]>
</script>
<!-- END Cange focus to warranty_id select -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
