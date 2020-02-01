<?php
//============================================================+
// File name   : cp_edit_ec_currency.php                       
// Begin       : 2002-08-30                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit currencies data (code, symbols, ...)     
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_EC_CURRENCY;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_currency_editor'];

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
		$sql = "DELETE FROM ".K_TABLE_EC_CURRENCY." WHERE currency_id=".$currency_id."";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		$currency_id=FALSE;
		break;
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_EC_CURRENCY, "currency_iso_code_alpha='".$currency_iso_code_alpha."'", "currency_id", $currency_id)) {
				F_print_error("WARNING", $l['m_duplicate_code']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$sql = "UPDATE IGNORE ".K_TABLE_EC_CURRENCY." SET 
				currency_iso_code_alpha='".$currency_iso_code_alpha."',
				currency_iso_code_numeric='".$currency_iso_code_numeric."',
				currency_uic_code='".$currency_uic_code."',
				currency_name='".$currency_name."',
				currency_name_minor='".$currency_name_minor."',
				currency_description='".$currency_description."',
				currency_unicode_symbol='".$currency_unicode_symbol."',
				currency_char_symbol='".$currency_char_symbol."',
				currency_decimals='".$currency_decimals."',
				currency_thousand_separator='".$currency_thousand_separator."',
				currency_decimals_separator='".$currency_decimals_separator."'
				WHERE currency_id=".$currency_id."";
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
			//check if currency_iso_code_alpha is unique
			$sql = "SELECT currency_iso_code_alpha FROM ".K_TABLE_EC_CURRENCY." WHERE currency_iso_code_alpha='".$currency_iso_code_alpha."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_code']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				$sql = "INSERT IGNORE INTO ".K_TABLE_EC_CURRENCY." (
				currency_iso_code_alpha,
				currency_iso_code_numeric,
				currency_uic_code,
				currency_name,
				currency_name_minor,
				currency_description,
				currency_unicode_symbol,
				currency_char_symbol,
				currency_decimals,
				currency_thousand_separator,
				currency_decimals_separator
				) VALUES (
				'".$currency_iso_code_alpha."',
				'".$currency_iso_code_numeric."',
				'".$currency_uic_code."',
				'".$currency_name."',
				'".$currency_name_minor."',
				'".$currency_description."',
				'".$currency_unicode_symbol."',
				'".$currency_char_symbol."',
				'".$currency_decimals."',
				'".$currency_thousand_separator."',
				'".$currency_decimals_separator."'
				)";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$currency_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
	}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$currency_iso_code_alpha = "";
		$currency_iso_code_numeric = "";
		$currency_uic_code = "";
		$currency_name = "";
		$currency_name_minor = "";
		$currency_description = "";
		$currency_unicode_symbol = "";
		$currency_char_symbol = "";
		$currency_decimals = "";
		$currency_thousand_separator = "";
		$currency_decimals_separator = "";
	break;
		}

	default :{ 
		break;
	}

} //end of switch

// Initialize variables
if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!isset($currency_id) OR (!$currency_id)) {
			$sql = "SELECT * FROM ".K_TABLE_EC_CURRENCY." ORDER BY currency_iso_code_alpha LIMIT 1";
		} else {
			$sql = "SELECT * FROM ".K_TABLE_EC_CURRENCY." WHERE currency_id=".$currency_id." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$currency_id = $m['currency_id'];
				$currency_iso_code_alpha = $m['currency_iso_code_alpha'];
				$currency_iso_code_numeric = $m['currency_iso_code_numeric'];
				$currency_uic_code = $m['currency_uic_code'];
				$currency_name = $m['currency_name'];
				$currency_name_minor = $m['currency_name_minor'];
				$currency_description = $m['currency_description'];
				$currency_unicode_symbol = $m['currency_unicode_symbol'];
				$currency_char_symbol = $m['currency_char_symbol'];
				$currency_decimals = $m['currency_decimals'];
				$currency_thousand_separator = $m['currency_thousand_separator'];
				$currency_decimals_separator = $m['currency_decimals_separator'];
			}
			else {
				$currency_iso_code_alpha = "";
				$currency_iso_code_numeric = "";
				$currency_uic_code = "";
				$currency_name = "";
				$currency_name_minor = "";
				$currency_description = "";
				$currency_unicode_symbol = "";
				$currency_char_symbol = "";
				$currency_decimals = "";
				$currency_thousand_separator = "";
				$currency_decimals_separator = "";
			}
		}
		else {
			F_display_db_error();
		}
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_currencyeditor" id="form_currencyeditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="currency_name,currency_iso_code_alpha,currency_thousand_separator,currency_decimals_separator" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name'].",".$l['w_iso_4217_alpha_code'].",".$l['w_thousand_separator'].",".$l['w_decimals_separator']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT target ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_currency', 'h_currency_select'); ?></b></td>
<td class="fillOE">
<select name="currency_id" id="currency_id" size="0" onchange="document.form_currencyeditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_EC_CURRENCY." ORDER BY currency_iso_code_alpha";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['currency_id']."\"";
		if($m['currency_id'] == $currency_id) {
			echo " selected=\"selected\"";
		}
		echo ">".$m['currency_iso_code_alpha']." - ".htmlentities($m['currency_description'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT target ==================== -->

<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE"><hr /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_name', 'h_currency_name'); ?></b></td>
<td class="fillOE"><input type="text" name="currency_name" id="currency_name" value="<?php echo htmlentities($currency_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="20" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_name_minor', 'h_currency_name_minor'); ?></b></td>
<td class="fillEE"><input type="text" name="currency_name_minor" id="currency_name_minor" value="<?php echo $currency_name_minor; ?>" size="20" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_iso_4217_alpha_code', 'h_currency_iso_4217_alpha_code'); ?></b></td>
<td class="fillOE"><input type="text" name="currency_iso_code_alpha" id="currency_iso_code_alpha" value="<?php echo $currency_iso_code_alpha; ?>" size="20" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_iso_4217_num_code', 'h_currency_iso_4217_num_code'); ?></b></td>
<td class="fillEE"><input type="text" name="currency_iso_code_numeric" id="currency_iso_code_numeric" value="<?php echo $currency_iso_code_numeric; ?>" size="20" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_uic_code', 'h_currency_uic_code'); ?></b></td>
<td class="fillOE"><input type="text" name="currency_uic_code" id="currency_uic_code" value="<?php echo $currency_uic_code; ?>" size="20" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_unicode_symbol', 'h_currency_unicode_symbol'); ?></b></td>
<td class="fillEE"><input type="text" name="currency_unicode_symbol" id="currency_unicode_symbol" value="<?php echo htmlentities($currency_unicode_symbol); ?>" size="20" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_symbol', 'h_currency_symbol'); ?></b></td>
<td class="fillOE"><input type="text" name="currency_char_symbol" id="currency_char_symbol" value="<?php echo $currency_char_symbol; ?>" size="20" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_decimals', 'h_currency_decimals'); ?></b></td>
<td class="fillEE"><input type="text" name="currency_decimals" id="currency_decimals" value="<?php echo $currency_decimals; ?>" size="20" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_thousand_separator', 'h_currency_thousand_separator'); ?></b></td>
<td class="fillOE"><input type="text" name="currency_thousand_separator" id="currency_thousand_separator" value="<?php echo $currency_thousand_separator; ?>" size="20" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_decimals_separator', 'h_currency_decimals_separator'); ?></b></td>
<td class="fillEE"><input type="text" name="currency_decimals_separator" id="currency_decimals_separator" value="<?php echo $currency_decimals_separator; ?>" size="20" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b><?php echo F_display_field_name('w_description', 'h_currency_description'); ?></b>
<?php 
$doc_charset = F_word_language($selected_language, "a_meta_charset");

$current_ta_code = $currency_description;
if(!$formstatus) {
	$current_ta_code = stripslashes($current_ta_code);
}
?>
</td>
<td class="fillOE"><textarea cols="20" rows="3" name="currency_description" id="currency_description"><?php echo htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset); ?></textarea></td>
</tr>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if ($currency_id) {
	F_submit_button("form_currencyeditor","menu_mode",$l['w_update']); 
	F_submit_button("form_currencyeditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_currencyeditor","menu_mode",$l['w_add']); 
F_submit_button("form_currencyeditor","menu_mode",$l['w_clear']); 
?>
</td>

</tr>
</table>
</form>
<!-- ====================================================== -->

<!-- Cange focus to currency_id select -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_currencyeditor.currency_id.focus();
//]]>
</script>
<!-- END Cange focus to currency_id select -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
