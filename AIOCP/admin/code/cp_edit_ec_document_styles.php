<?php
//============================================================+
// File name   : cp_edit_ec_document_styles.php                
// Begin       : 2002-07-24                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit Styles for Commercial Documents          
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_EC_DOCUMENT_STYLES;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_ec_documents_styles_editor'];

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
		//check if it's used before delete
		$sql = "SELECT COUNT(*) FROM ".K_TABLE_EC_DOCUMENTS_TYPES." WHERE doctype_style='".$docstyle_id."'";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$optionused = $m['0'];
			}
		}
		else {
			F_display_db_error();
		}
		if (isset($optionused) AND $optionused) {
			F_print_error("WARNING", $l['m_not_delete_used_style']);
		}
		else {
			$sql = "DELETE FROM ".K_TABLE_EC_DOCUMENTS_STYLES." WHERE docstyle_id=".$docstyle_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			$docstyle_id=FALSE;
		}
		break;
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_EC_DOCUMENTS_STYLES,"docstyle_name='".$docstyle_name."'","docstyle_id",$docstyle_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$sql = "UPDATE IGNORE ".K_TABLE_EC_DOCUMENTS_STYLES." SET 
						docstyle_name='".$docstyle_name."', 
						docstyle_paper='".$docstyle_paper."', 
						docstyle_width='".$docstyle_width."', 
						docstyle_height='".$docstyle_height."', 
						docstyle_orientation='".$docstyle_orientation."', 
						docstyle_margin_top='".$docstyle_margin_top."', 
						docstyle_margin_bottom='".$docstyle_margin_bottom."', 
						docstyle_margin_left='".$docstyle_margin_left."', 
						docstyle_margin_right='".$docstyle_margin_right."', 
						docstyle_header='".$docstyle_header."', 
						docstyle_footer='".$docstyle_footer."', 
						docstyle_main_font='".$docstyle_main_font."', 
						docstyle_main_font_size='".$docstyle_main_font_size."', 
						docstyle_data_font='".$docstyle_data_font."', 
						docstyle_data_font_size='".$docstyle_data_font_size."',
						docstyle_image_width='".$docstyle_image_width."'
						WHERE docstyle_id=".$docstyle_id."";
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
			$sql = "SELECT docstyle_name FROM ".K_TABLE_EC_DOCUMENTS_STYLES." WHERE docstyle_name='".$docstyle_name."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				$sql = "INSERT IGNORE INTO ".K_TABLE_EC_DOCUMENTS_STYLES." (
				docstyle_name,
				docstyle_paper,
				docstyle_width, 
				docstyle_height, 
				docstyle_orientation,
				docstyle_margin_top,
				docstyle_margin_bottom,
				docstyle_margin_left,
				docstyle_margin_right,
				docstyle_header,
				docstyle_footer,
				docstyle_main_font,
				docstyle_main_font_size,
				docstyle_data_font,
				docstyle_data_font_size,
				docstyle_image_width
				) VALUES (
				'".$docstyle_name."', 
				'".$docstyle_paper."', 
				'".$docstyle_width."', 
				'".$docstyle_height."', 
				'".$docstyle_orientation."', 
				'".$docstyle_margin_top."', 
				'".$docstyle_margin_bottom."', 
				'".$docstyle_margin_left."', 
				'".$docstyle_margin_right."', 
				'".$docstyle_header."', 
				'".$docstyle_footer."', 
				'".$docstyle_main_font."', 
				'".$docstyle_main_font_size."', 
				'".$docstyle_data_font."', 
				'".$docstyle_data_font_size."',
				'".$docstyle_image_width."'
				)";
						
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$docstyle_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
	}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$docstyle_name = "";
		$docstyle_paper = "A4";
		$docstyle_width = "";
		$docstyle_height = ""; 
		$docstyle_orientation = "P";
		$docstyle_margin_top = "25";
		$docstyle_margin_bottom = "25";
		$docstyle_margin_left = "20";
		$docstyle_margin_right = "20";
		$docstyle_header = "10";
		$docstyle_footer = "10";
		$docstyle_main_font = "times";
		$docstyle_main_font_size = "10";
		$docstyle_data_font = "helvetica";
		$docstyle_data_font_size = "8";
		$docstyle_image_width = 30;
		break;
	}

	default :{ 
		break;
	}

} //end of switch

// Initialize variables
if($formstatus) {
	
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!isset($docstyle_id) OR (!$docstyle_id)) {
			$sql = "SELECT * FROM ".K_TABLE_EC_DOCUMENTS_STYLES." ORDER BY docstyle_name LIMIT 1";
		} else {
			$sql = "SELECT * FROM ".K_TABLE_EC_DOCUMENTS_STYLES." WHERE docstyle_id=".$docstyle_id." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$docstyle_id = $m['docstyle_id'];
				$docstyle_name = $m['docstyle_name'];
				$docstyle_paper = $m['docstyle_paper'];
				$docstyle_width = $m['docstyle_width'];
				$docstyle_height = $m['docstyle_height'];
				$docstyle_orientation = $m['docstyle_orientation'];
				$docstyle_margin_top = $m['docstyle_margin_top'];
				$docstyle_margin_bottom = $m['docstyle_margin_bottom'];
				$docstyle_margin_left = $m['docstyle_margin_left'];
				$docstyle_margin_right = $m['docstyle_margin_right'];
				$docstyle_header = $m['docstyle_header'];
				$docstyle_footer = $m['docstyle_footer'];
				$docstyle_main_font = $m['docstyle_main_font'];
				$docstyle_main_font_size = $m['docstyle_main_font_size'];
				$docstyle_data_font = $m['docstyle_data_font'];
				$docstyle_data_font_size = $m['docstyle_data_font_size'];
				$docstyle_image_width = $m['docstyle_image_width'];
			}
			else {
				$docstyle_name = "";
				$docstyle_paper = "A4";
				$docstyle_width = "";
				$docstyle_height = ""; 
				$docstyle_orientation = "P";
				$docstyle_margin_top = "25";
				$docstyle_margin_bottom = "25";
				$docstyle_margin_left = "20";
				$docstyle_margin_right = "20";
				$docstyle_header = "10";
				$docstyle_footer = "10";
				$docstyle_main_font = "times";
				$docstyle_main_font_size = "10";
				$docstyle_data_font = "helvetica";
				$docstyle_data_font_size = "8";
				$docstyle_image_width = 30;
			}
		}
		else {
			F_display_db_error();
		}
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_ecdocstyleeditor" id="form_ecdocstyleeditor">

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT option ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_style', 'h_docstyle_select'); ?></b></td>
<td class="fillOE" colspan="2">
<select name="docstyle_id" id="docstyle_id" size="0" onchange="document.form_ecdocstyleeditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_EC_DOCUMENTS_STYLES." ORDER BY docstyle_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['docstyle_id']."\"";
		if($m['docstyle_id'] == $docstyle_id) {
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

<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE" colspan="2"><hr /></td>
</tr>

<tr class="fillO">
<td class="fillOE" align="right"><b><?php echo F_display_field_name('w_name', 'h_docstyle_name'); ?></b></td>
<td class="fillOE" colspan="2"><input type="text" name="docstyle_name" id="docstyle_name" value="<?php echo htmlentities($docstyle_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="20" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_page_format', 'h_docstyle_paper'); ?></b></td>
<td class="fillEE">
<select name="docstyle_paper" id="docstyle_paper" size="0" onchange="document.form_ecdocstyleeditor.docstyle_width.value=''; document.form_ecdocstyleeditor.docstyle_height.value='';">
<?php
echo "<option value=\"\"";
if($docstyle_paper == "") {echo " selected=\"selected\"";}
echo ">&middot;&middot;&middot; ".$l['w_custom']." &middot;&middot;&middot;</option>\n";

echo "<option value=\"4A0\"";
if($docstyle_paper == "4A0") {echo " selected=\"selected\"";}
echo ">4A0 (1682 x 2378) mm</option>\n";

echo "<option value=\"2A0\"";
if($docstyle_paper == "2A0") {echo " selected=\"selected\"";}
echo ">2A0 (1189 x 1682) mm</option>\n";

echo "<option value=\"A0\"";
if($docstyle_paper == "A0") {echo " selected=\"selected\"";}
echo ">A0 (841 x 1189) mm</option>\n";

echo "<option value=\"A1\"";
if($docstyle_paper == "A1") {echo " selected=\"selected\"";}
echo ">A1 (594 x 841) mm</option>\n";

echo "<option value=\"A2\"";
if($docstyle_paper == "A2") {echo " selected=\"selected\"";}
echo ">A2 (420 x 594) mm</option>\n";

echo "<option value=\"A3\"";
if($docstyle_paper == "A3") {echo " selected=\"selected\"";}
echo ">A3 (297 x 420) mm</option>\n";

echo "<option value=\"A4\"";
if($docstyle_paper == "A4") {echo " selected=\"selected\"";}
echo ">A4 (210 x 297) mm</option>\n";

echo "<option value=\"A5\"";
if($docstyle_paper == "A5") {echo " selected=\"selected\"";}
echo ">A5 (148 x 210) mm</option>\n";

echo "<option value=\"A6\"";
if($docstyle_paper == "A6") {echo " selected=\"selected\"";}
echo ">A6 (105 x 148) mm</option>\n";

echo "<option value=\"A7\"";
if($docstyle_paper == "A7") {echo " selected=\"selected\"";}
echo ">A7 (74 x 105) mm</option>\n";

echo "<option value=\"A8\"";
if($docstyle_paper == "A8") {echo " selected=\"selected\"";}
echo ">A8 (52 x 74) mm</option>\n";

echo "<option value=\"A9\"";
if($docstyle_paper == "A9") {echo " selected=\"selected\"";}
echo ">A9 (37 x 52) mm</option>\n";

echo "<option value=\"A10\"";
if($docstyle_paper == "A10") {echo " selected=\"selected\"";}
echo ">A10 (26 x 37) mm</option>\n";

echo "<option value=\"B0\"";
if($docstyle_paper == "B0") {echo " selected=\"selected\"";}
echo ">B0 (1000 x 1414) mm</option>\n";

echo "<option value=\"B1\"";
if($docstyle_paper == "B1") {echo " selected=\"selected\"";}
echo ">B1 (707 x 1000) mm</option>\n";

echo "<option value=\"B2\"";
if($docstyle_paper == "B2") {echo " selected=\"selected\"";}
echo ">B2 (500 x 707) mm</option>\n";

echo "<option value=\"B3\"";
if($docstyle_paper == "B3") {echo " selected=\"selected\"";}
echo ">B3 (353 x 500) mm</option>\n";

echo "<option value=\"B4\"";
if($docstyle_paper == "B4") {echo " selected=\"selected\"";}
echo ">B4 (250 x 353) mm</option>\n";

echo "<option value=\"B5\"";
if($docstyle_paper == "B5") {echo " selected=\"selected\"";}
echo ">B5 (176 x 250) mm</option>\n";

echo "<option value=\"B6\"";
if($docstyle_paper == "B6") {echo " selected=\"selected\"";}
echo ">B6 (125 x 176) mm</option>\n";

echo "<option value=\"B7\"";
if($docstyle_paper == "B7") {echo " selected=\"selected\"";}
echo ">B7 (88 x 125) mm</option>\n";

echo "<option value=\"B8\"";
if($docstyle_paper == "B8") {echo " selected=\"selected\"";}
echo ">B8 (62 x 88) mm</option>\n";

echo "<option value=\"B9\"";
if($docstyle_paper == "B9") {echo " selected=\"selected\"";}
echo ">B9 (44 x 62) mm</option>\n";

echo "<option value=\"B10\"";
if($docstyle_paper == "B10") {echo " selected=\"selected\"";}
echo ">B10 (31 x 44) mm</option>\n";

echo "<option value=\"C0\"";
if($docstyle_paper == "C0") {echo " selected=\"selected\"";}
echo ">C0 (917 x 1297) mm</option>\n";

echo "<option value=\"C1\"";
if($docstyle_paper == "C1") {echo " selected=\"selected\"";}
echo ">C1 (648 x 917) mm</option>\n";

echo "<option value=\"C2\"";
if($docstyle_paper == "C2") {echo " selected=\"selected\"";}
echo ">C2 (458 x 648) mm</option>\n";

echo "<option value=\"C3\"";
if($docstyle_paper == "C3") {echo " selected=\"selected\"";}
echo ">C3 (324 x 458) mm</option>\n";

echo "<option value=\"C4\"";
if($docstyle_paper == "C4") {echo " selected=\"selected\"";}
echo ">C4 (229 x 324) mm</option>\n";

echo "<option value=\"C5\"";
if($docstyle_paper == "C5") {echo " selected=\"selected\"";}
echo ">C5 (162 x 229) mm</option>\n";

echo "<option value=\"C6\"";
if($docstyle_paper == "C6") {echo " selected=\"selected\"";}
echo ">C6 (114 x 162) mm</option>\n";

echo "<option value=\"C7\"";
if($docstyle_paper == "C7") {echo " selected=\"selected\"";}
echo ">C7 (81 x 114) mm</option>\n";

echo "<option value=\"C8\"";
if($docstyle_paper == "C8") {echo " selected=\"selected\"";}
echo ">C8 (57 x 81) mm</option>\n";

echo "<option value=\"C9\"";
if($docstyle_paper == "C9") {echo " selected=\"selected\"";}
echo ">C9 (40 x 57) mm</option>\n";

echo "<option value=\"C10\"";
if($docstyle_paper == "C10") {echo " selected=\"selected\"";}
echo ">C10 (28 x 40) mm</option>\n";

echo "<option value=\"RA0\"";
if($docstyle_paper == "RA0") {echo " selected=\"selected\"";}
echo ">RA0 (860 x 1220) mm</option>\n";

echo "<option value=\"RA1\"";
if($docstyle_paper == "RA1") {echo " selected=\"selected\"";}
echo ">RA1 (610 x 860) mm</option>\n";

echo "<option value=\"RA2\"";
if($docstyle_paper == "RA2") {echo " selected=\"selected\"";}
echo ">RA2 (430 x 610) mm</option>\n";

echo "<option value=\"RA3\"";
if($docstyle_paper == "RA3") {echo " selected=\"selected\"";}
echo ">RA3 (305 x 430) mm</option>\n";

echo "<option value=\"RA4\"";
if($docstyle_paper == "RA4") {echo " selected=\"selected\"";}
echo ">RA4 (215 x 305) mm</option>\n";

echo "<option value=\"SRA0\"";
if($docstyle_paper == "SRA0") {echo " selected=\"selected\"";}
echo ">SRA0 (900 x 1280) mm</option>\n";

echo "<option value=\"SRA1\"";
if($docstyle_paper == "SRA1") {echo " selected=\"selected\"";}
echo ">SRA1 (640 x 900) mm</option>\n";

echo "<option value=\"SRA2\"";
if($docstyle_paper == "SRA2") {echo " selected=\"selected\"";}
echo ">SRA2 (450 x 640) mm</option>\n";

echo "<option value=\"SRA3\"";
if($docstyle_paper == "SRA3") {echo " selected=\"selected\"";}
echo ">SRA3 (320 x 450) mm</option>\n";

echo "<option value=\"SRA4\"";
if($docstyle_paper == "SRA4") {echo " selected=\"selected\"";}
echo ">SRA4 (225 x 320) mm</option>\n";

echo "<option value=\"LETTER\"";
if($docstyle_paper == "LETTER") {echo " selected=\"selected\"";}
echo ">Letter (215,90 x 279,40) mm</option>\n";

echo "<option value=\"LEGAL\"";
if($docstyle_paper == "LEGAL") {echo " selected=\"selected\"";}
echo ">Legal (215,90 x 355,60) mm</option>\n";

echo "<option value=\"EXECUTIVE\"";
if($docstyle_paper == "EXECUTIVE") {echo " selected=\"selected\"";}
echo ">Executive (184,10 x 266,70) mm</option>\n";

echo "<option value=\"FOLIO\"";
if($docstyle_paper == "FOLIO") {echo " selected=\"selected\"";}
echo ">Folio (215,90 x 330,20) mm</option>\n";
?>
</select>
</td>
</tr>

<tr class="fillO"> 
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_width', 'h_docstyle_page_width'); ?></b></td>
<td class="fillOE"><input type="text" name="docstyle_width" id="docstyle_width" value="<?php echo $docstyle_width; ?>" size="10" maxlength="4" /> <b>[mm]</b>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_height', 'h_docstyle_page_height'); ?></b></td>
<td class="fillEE"><input type="text" name="docstyle_height" id="docstyle_height" value="<?php echo $docstyle_height; ?>" size="10" maxlength="4" /> <b>[mm]</b>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_orientation', 'h_docstyle_orientation'); ?></b></td>
<td class="fillOE">
<?php
echo "<input type=\"radio\" name=\"docstyle_orientation\" value=\"P\"";
if($docstyle_orientation == "P") {echo " checked=\"checked\"";}
echo " />".$l['w_page_portrait']."&nbsp;";

echo "<input type=\"radio\" name=\"docstyle_orientation\" value=\"L\"";
if($docstyle_orientation == "L") {echo " checked=\"checked\"";}
echo " />".$l['w_page_landscape']."&nbsp;";
?>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_margin_top', 'h_docstyle_margin_top'); ?></b></td>
<td class="fillEE"><input type="text" name="docstyle_margin_top" id="docstyle_margin_top" value="<?php echo $docstyle_margin_top; ?>" size="10" maxlength="4" /> <b>[mm]</b>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_margin_bottom', 'h_docstyle_margin_bottom'); ?></b></td>
<td class="fillOE"><input type="text" name="docstyle_margin_bottom" id="docstyle_margin_bottom" value="<?php echo $docstyle_margin_bottom; ?>" size="10" maxlength="4" /> <b>[mm]</b>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_margin_left', 'h_docstyle_margin_left'); ?></b></td>
<td class="fillEE"><input type="text" name="docstyle_margin_left" id="docstyle_margin_left" value="<?php echo $docstyle_margin_left; ?>" size="10" maxlength="4" /> <b>[mm]</b>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_margin_right', 'h_docstyle_margin_right'); ?></b></td>
<td class="fillOE"><input type="text" name="docstyle_margin_right" id="docstyle_margin_right" value="<?php echo $docstyle_margin_right; ?>" size="10" maxlength="4" /> <b>[mm]</b>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_header', 'h_docstyle_header'); ?></b></td>
<td class="fillEE"><input type="text" name="docstyle_header" id="docstyle_header" value="<?php echo $docstyle_header; ?>" size="10" maxlength="4" /> <b>[mm]</b>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_footer', 'h_docstyle_footer'); ?></b></td>
<td class="fillOE"><input type="text" name="docstyle_footer" id="docstyle_footer" value="<?php echo $docstyle_footer; ?>" size="10" maxlength="4" /> <b>[mm]</b>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_image_width', 'h_docstyle_image_width'); ?></b></td>
<td class="fillEE"><input type="text" name="docstyle_image_width" id="docstyle_image_width" value="<?php echo $docstyle_image_width; ?>" size="10" maxlength="4" /> <b>[mm]</b>
</td>
</tr>
		
<tr class="fillO">
<td class="fillOO">&nbsp;</td>
<td class="fillOE" colspan="2"><b><?php echo F_display_field_name('w_main_font', 'h_docstyle_main_font'); ?></b></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_font', 'h_docstyle_main_font'); ?></b></td>
<td class="fillEE">
<select name="docstyle_main_font" id="docstyle_main_font" size="0">
<?php F_print_font_selection($docstyle_main_font); ?>
</select>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_size', 'h_font_size'); ?></b></td>
<td class="fillOE"><input type="text" name="docstyle_main_font_size" id="docstyle_main_font_size" value="<?php echo $docstyle_main_font_size; ?>" size="10" maxlength="4" />
</td>
</tr>

<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE" colspan="2"><b><?php echo F_display_field_name('w_data_font', 'h_docstyle_data_font'); ?></b></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_font', 'h_docstyle_data_font'); ?></b></td>
<td class="fillOE">
<select name="docstyle_data_font" id="docstyle_data_font" size="0">
<?php F_print_font_selection($docstyle_data_font); ?>
</select>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_size', 'h_font_size'); ?></b></td>
<td class="fillEE"><input type="text" name="docstyle_data_font_size" id="docstyle_data_font_size" value="<?php echo $docstyle_data_font_size; ?>" size="10" maxlength="4" />
</td>
</tr>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if ($docstyle_id) {
	F_submit_button("form_ecdocstyleeditor","menu_mode",$l['w_update']); 
	F_submit_button("form_ecdocstyleeditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_ecdocstyleeditor","menu_mode",$l['w_add']); 
F_submit_button("form_ecdocstyleeditor","menu_mode",$l['w_clear']); 
?>
</td>

</tr>
</table>
</form>

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//output font selection options
function F_print_font_selection($font_field) {
	global $l;
	// read directory for font files
	$handle = opendir(K_PATH_FONTS);
	while (false !== ($file = readdir($handle))) {
		$path_parts = pathinfo($file);
		$file_ext = strtolower($path_parts['extension']);
		//check file extension
		if($file_ext=="php") {
			$fontfamily = basename($file,".php");
			echo "<option value=\"".$fontfamily."\"";
			if($fontfamily == $font_field) {
				echo " selected=\"selected\"";
			}
			echo ">".htmlentities($fontfamily, ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
		}
	}
	closedir($handle);}
?>
 
<?php
//============================================================+
// END OF FILE                                                 
//============================================================+
?>
