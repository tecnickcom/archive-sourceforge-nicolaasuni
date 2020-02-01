<?php
//============================================================+
// File name   : cp_edit_ec_documents_types.php                
// Begin       : 2002-07-01                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit Commercial Documents Types               
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_EC_DOCUMENTS_TYPES;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_ec_documents_type_editor'];

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
		$sql = "DELETE FROM ".K_TABLE_EC_DOCUMENTS_TYPES." WHERE doctype_id=".$doctype_id."";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		$doctype_id=FALSE;
		break;
		}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_EC_DOCUMENTS_TYPES,"doctype_name='".$doctype_name."'","doctype_id",$doctype_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$doctype_name = addslashes(serialize($a_name));
				$doctype_options = addslashes(serialize($dt_options));
				$sql = "UPDATE IGNORE ".K_TABLE_EC_DOCUMENTS_TYPES." SET 
				doctype_name='".$doctype_name."',
				doctype_style='".$doctype_style."',
				doctype_options='".$doctype_options."'
				WHERE doctype_id=".$doctype_id."";
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
			$sql = "SELECT doctype_id FROM ".K_TABLE_EC_DOCUMENTS_TYPES." WHERE doctype_name='".$doctype_name."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				$doctype_name = addslashes(serialize($a_name));
				$doctype_options = addslashes(serialize($dt_options));
				$sql = "INSERT IGNORE INTO ".K_TABLE_EC_DOCUMENTS_TYPES." (
				doctype_name,
				doctype_style,
				doctype_options
				) VALUES (
				'".$doctype_name."',
				'".$doctype_style."',
				'".$doctype_options."'
				)";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$doctype_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$doctype_name = "";
		$doctype_style = "";
		$a_name = array();
		$dt_options = array();
		break;
		}

	default :{ 
		break;
		}

} //end of switch

// Initialize variables
if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!isset($doctype_id) OR (!$doctype_id)) {
			$sql = "SELECT * FROM ".K_TABLE_EC_DOCUMENTS_TYPES." ORDER BY doctype_name LIMIT 1";
		}
		else {$sql = "SELECT * FROM ".K_TABLE_EC_DOCUMENTS_TYPES." WHERE doctype_id=".$doctype_id." LIMIT 1";}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$doctype_id = $m['doctype_id'];
				$doctype_name = $m['doctype_name'];
				$a_name = unserialize($doctype_name);
				$doctype_style = $m['doctype_style'];
				$doctype_options = $m['doctype_options'];
				$dt_options = unserialize($doctype_options);
			}
			else {
				$doctype_name = "";
				$doctype_style = "";
				$a_name = array();
				$dt_options = array();
			}
		}
		else {
			F_display_db_error();
		}
	}
}

?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_documenttypeeditor" id="form_documenttypeeditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="a_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT  ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_document_type', 'h_doctype_select'); ?></b></td>
<td class="fillOE">
<select name="doctype_id" id="doctype_id" size="0" onchange="document.form_documenttypeeditor.submit()">
<?php
	$sql = "SELECT * FROM ".K_TABLE_EC_DOCUMENTS_TYPES." ORDER BY doctype_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			$select_name = unserialize($m['doctype_name']);
			echo "<option value=\"".$m['doctype_id']."\"";
			if($m['doctype_id'] == $doctype_id) {
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
			echo "<td class=\"fillEO\" align=\"right\" valign=\"top\"><b>".F_display_field_name('w_name', 'h_doctype_name')."</b><br />";
	
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


<!-- SELECT option ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_style', 'h_docstyle_select'); ?></b></td>
<td class="fillOE" colspan="2">
<select name="doctype_style" id="doctype_style" size="0">
<?php
$sql = "SELECT * FROM ".K_TABLE_EC_DOCUMENTS_STYLES." ORDER BY docstyle_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['docstyle_id']."\"";
		if($m['docstyle_id'] == $doctype_style) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['docstyle_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
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

<tr class="fillO">
<td class="fillOO" align="right"></td>
<td class="fillOE"><br /><b><?php echo F_display_field_name('w_options', 'h_docstyle_options'); ?></b></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_payment', 'h_paytype_select'); ?></b></td>
<td class="fillEE"><?php F_print_doctype_option(0); ?></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_payment_details', 'h_mtrans_payment_details'); ?></b></td>
<td class="fillOE"><?php F_print_doctype_option(1); ?></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_paid', 'h_docstyle_paid'); ?></b></td>
<td class="fillEE"><?php F_print_doctype_option(2); ?></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_validity', 'h_ecdoc_validity'); ?></b></td>
<td class="fillOE"><?php F_print_doctype_option(3); ?></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_costs', 'h_docstyle_costs'); ?></b></td>
<td class="fillEE"><?php F_print_doctype_option(4); ?></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_transport', 'h_ecdoc_transport'); ?></b></td>
<td class="fillOE"><?php F_print_doctype_option(5); ?></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_subject', 'h_ecdoc_subject'); ?></b></td>
<td class="fillEE"><?php F_print_doctype_option(6); ?></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_notes_intro', 'h_ecdoc_notes_intro'); ?></b></td>
<td class="fillOE"><?php F_print_doctype_option(7); ?></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_notes_end', 'h_ecdoc_notes_end'); ?></b></td>
<td class="fillEE"><?php F_print_doctype_option(8); ?></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_description', 'h_ecdoc_product_descriptions'); ?></b></td>
<td class="fillOE"><?php F_print_doctype_option(9); ?></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_barcode', 'h_docstyle_barcode'); ?></b></td>
<td class="fillEE"><?php F_print_doctype_option(10); ?></td>
</tr>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if ($doctype_id) {
	F_submit_button("form_documenttypeeditor","menu_mode",$l['w_update']); 
	F_submit_button("form_documenttypeeditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_documenttypeeditor","menu_mode",$l['w_add']); 
F_submit_button("form_documenttypeeditor","menu_mode",$l['w_clear']); 
?>
</td>
</tr>
</table>
</form>
<!-- ====================================================== -->

<!-- Cange focus to doctype_id select -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_documenttypeeditor.doctype_id.focus();

//get next elements ID
function FJ_get_field_index(what) {
	for (var i=0;i<document.form_documenttypeeditor.elements.length;i++) {
		if(what == document.form_documenttypeeditor.elements[i]) {
			return(i+1);
		}
	}
	return (-1);
}
//]]>
</script>
<!-- END Cange focus to doctype_id select -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//Print Yes/No Option selection
function F_print_doctype_option($optnumber) {
	global $l,$dt_options;
	echo "<input type=\"radio\" name=\"dt_options[".$optnumber."]\" value=\"1\"";
	if(stripslashes($dt_options[$optnumber])) {echo " checked=\"checked\"";}
	echo " />".$l['w_yes']."&nbsp;";
	echo "<input type=\"radio\" name=\"dt_options[".$optnumber."]\" value=\"0\"";
	if(!stripslashes($dt_options[$optnumber])) {echo " checked=\"checked\"";}
	echo " />".$l['w_no'];
}
?>

<?php
//============================================================+
// END OF FILE                                                 
//============================================================+
?>
