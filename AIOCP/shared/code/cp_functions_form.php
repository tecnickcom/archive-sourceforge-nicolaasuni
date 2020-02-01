<?php
//============================================================+
// File name   : cp_functions_form.php
// Begin       : 2001-11-07
// Last Update : 2011-07-17
//
// Description : Functions to handle Form Fields
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

$formstatus = TRUE; //reset form status

/**
 * Returns an array containing form fields.
 * @return array containing form fields
 */
function F_decode_form_fields() {
	return $_REQUEST;
}

/**
 * Check Required Form Fields.
 * Returns a string containing a list of missing fields (comma separated).
 * @param $formfields (string) input array containing form fields
 * @return array containing a list of missing fields (if any)
 */
function F_check_required_fields($formfields) {
	if (empty($formfields) OR !array_key_exists('ff_required', $formfields) OR strlen($formfields['ff_required']) <= 0) {
		return FALSE;
	}
	$missing_fields = '';
	$required_fields = explode(',',$formfields['ff_required']);
	$required_fields_labels = explode(',',$formfields['ff_required_labels']); // form fields labels
	for($i=0; $i<count($required_fields); $i++) { //for each required field
		$fieldname = trim($required_fields[$i]);
		$fieldname = preg_replace('/[^a-z0-9_\[\]]/i', '', $fieldname);
		if (!array_key_exists($fieldname, $formfields) OR strlen(trim($formfields[$fieldname])) <= 0) { //if is empty
			if ($required_fields_labels[$i]) { // check if field has label
				$fieldname = $required_fields_labels[$i];
				$fieldname = preg_replace('/[^a-z0-9_\[\]]/i', '', $fieldname);
			}
			$missing_fields .= ', '.stripslashes($fieldname);
		}
	}
	if (strlen($missing_fields)>1) {
		$missing_fields = substr($missing_fields, 1); // cuts first comma
	}
	return ($missing_fields);
}

/**
 * Check fields format using regular expression comparisons.
 * Returns a string containing a list of wrong fields (comma separated).
 *
 * NOTE:
 * to check a field create a new hidden field with the same name starting with 'x_'
 *
 * An example powerful regular expression for email check is:
 *  ^([a-zA-Z0-9_\.\-]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$
 * @param $formfields (string) input array containing form fields
 * @return array containing a list of wrongfields (if any)
 */
function F_check_fields_format($formfields) {
	if (empty($formfields)) {
		return '';
	}
	reset($formfields);
	$wrongfields = '';
	while (list($key,$value) = each($formfields)) {
		if (substr($key,0,2) == 'x_') {
			$fieldname = substr($key,2);
			$fieldname = preg_replace('/[^a-z0-9_\[\]]/i', '', $fieldname);
			if (array_key_exists($fieldname, $formfields) AND strlen($formfields[$fieldname]) > 0) { //if is not empty
				if (!preg_match("'".stripslashes($value)."'i", $formfields[$fieldname])) { //check regular expression
					if (isset($formfields['xl_'.$fieldname]) AND !empty($formfields['xl_'.$fieldname])) { //check if field has label
						$fieldname = $formfields['xl_'.$fieldname];
						$fieldname = preg_replace('/[^a-z0-9_\[\]]/i', '', $fieldname);
					}
					$wrongfields .= ', '.stripslashes($fieldname);
				}
			}
		}
	}
	if (strlen($wrongfields) > 1) {
		$wrongfields = substr($wrongfields, 2); // cuts first 2 chars
	}
	return ($wrongfields);
}

/**
 * Check Form Fields.
 * see: F_check_required_fields, F_check_fields_format
 * @return false in case of error, true otherwise
 */
function F_check_form_fields() {
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	global $l;
	$formfields = F_decode_form_fields(); //decode form fields
	//check missing fields
	if ($missing_fields = F_check_required_fields($formfields)) {
		F_print_error('WARNING', $l['m_form_missing_fields'].': <span style="color:#660000;">'.$missing_fields.'</span>');
		F_stripslashes_formfields();
		return FALSE;
	}
	//check fields format
	if ($wrong_fields = F_check_fields_format($formfields)) {
		F_print_error('WARNING', $l['m_form_wrong_fields'].': <span style="color:#660000;">'.$wrong_fields.'</span>');
		F_stripslashes_formfields();
		return FALSE;
	}
	return TRUE;
}

/**
 * Strip slashes from posted form fields.
 */
function F_stripslashes_formfields() {
	foreach ($_POST as $key => $value) {
		if (($key{0} != '_') AND (is_string($value))) {
			$key = preg_replace('/[^a-z0-9_\[\]]/i', '', $key);
			global $$key;
			if (!isset($$key)) {
				$$key = stripslashes($value);
			}
		}
	}
}

/**
 * THis is a callback function used by F_stripslashes_arrayfields() function.
 * @param $item (mixed) Item value.
 * @param $key (mixed) Array key.
 */
function F_stripslasharray(&$item, $key) {
	$item = stripslashes($item);
}

/**
 * Strip slashes from array elements.
 * @param $data (array) Array to process.
 */
function F_stripslashes_arrayfields(&$data) {
	array_walk_recursive($data, 'F_stripslasharray');
}

/**
 * Check if a specified field is unique on the specified table.
 * This check is normally performed before update.
 * @param string $table table name
 * @param string $where where part of the query
 * @param $fieldname name of the field
 * @param $fieldid ID of the record to check
 * @return true if unique, false otherwise
 */
function F_check_unique($table, $where, $fieldname, $fieldid) {
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	global $db, $l;
	$sqlc = 'SELECT * FROM '.$table.' WHERE '.$where.' LIMIT 1';
	if ($rc = F_aiocpdb_query($sqlc, $db)) {
		if ($mc = F_aiocpdb_fetch_array($rc)) {
			if ($mc[$fieldname] == $fieldid) {return TRUE;} //name not changed
		} else {
			return TRUE;  //changed name but unique
		}
	} else {
		F_display_db_error();
	}
	return FALSE;
}

/**
 * Display a Form Button to open XHTML editor
 * @param string $templates nae of templates category to display
 * @param string $callingform name of the calling form
 * @param string $callingfield name of the textarea field on the calling form
 * @param string $doc_charset document charset
 */
function F_html_button($templates, $callingform, $callingfield, $doc_charset) {
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	global $l;
	if ($doc_charset) {
		$doc_charset_url = '&amp;charset='.$doc_charset;
	} else {
		$doc_charset_url = '';
	}
	F_generic_button(''.$callingform.'_'.$callingfield.'',$l['w_button_html_editor'],"htmlWindow=window.open('cp_edit_html.".CP_EXT."?templates=".$templates."&amp;callingform=".$callingform."&amp;callingfield=".$callingfield."".$doc_charset_url."','htmlWindow','dependent,height=500,width=800,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')");
}

/**
 * Display AIOCP code Form Button
 * Switch between graphic and text button
 * @param string $callingform name of the calling form
 * @param string $callingfield name of the textarea field on the calling form
 */
function F_aiocp_code_button($callingform, $callingfield) {
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	global $l;
	F_generic_button(''.$callingform.'_'.$callingfield.'',$l['w_button_aiocpcode_editor'],"htmlWindow=window.open('cp_edit_aiocpcode.".CP_EXT."?callingform=".$callingform."&amp;callingfield=".$callingfield."','htmlWindow','dependent,height=500,width=600,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')");
}

/**
 * Display generic Form Button
 * Switch between graphic and text button
 * @param string $name button name
 * @param string $value button value
 * @param string $event action for onclik event
 * @param string $hreftxt href link, default is '#'
 */
function F_generic_button($name, $value, $event, $hreftxt='#') {
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	global $l;
	$name = md5($name); //calculate unique name
	$name = preg_replace("/[0-9]/esi", "", $name); //remove numbers
	if (K_USE_GRAPHIC_BUTTONS) {
		if ((!isset($hreftxt)) || (strlen($hreftxt)<1)) {
			$hreftxt = "#";
		}
		echo "<a href=\"".htmlentities(urldecode($hreftxt))."\" onclick=\"".$event."\">";
		echo "<img src=\"../../shared/code/cp_button_and_bar.".CP_EXT."?s=".K_GRAPHIC_BUTTON_STYLE."&amp;t=".urlencode($value)."\" border=\"0\" alt=\"".$value."\" /></a>";
	} else {
		echo "<input type=\"button\" name=\"".$name."\" id=\"".$name."\" value=\"".$value."\" onclick=\"".$event."\" />";
	}
}

/**
 * Display Submit Form Button
 * Switch between graphic and text button
 * @param string $formname form name
 * @param string $bname button name
 * @param string $bvalue button value
 */
function F_submit_button($formname, $bname, $bvalue) {
	F_generic_submit_button($formname,"button_".$bname.$bvalue,$bvalue,"document.".$formname.".".$bname.".value='".$bvalue."'");
}

/**
 * Display generic Submit Form Button
 * Switch between graphic and text button
 * @param string $formname form name
 * @param string $bname button name
 * @param string $bvalue button value
 * @param string $bevent action event
 */
function F_generic_submit_button($formname, $bname, $bvalue, $bevent) {
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	global $l;
	$bname = md5($bname); //calculate unique name
	$bname = preg_replace("/[0-9]/esi", "", $bname); //remove numbers
	if(K_USE_GRAPHIC_BUTTONS) {
		echo "<a href=\"javascript:document.".$formname.".submit();\" onclick=\"".$bevent."\"><img src=\"../../shared/code/cp_button_and_bar.".CP_EXT."?s=".K_GRAPHIC_BUTTON_STYLE."&amp;t=".urlencode($bvalue)."\" border=\"0\" alt=\"".$bvalue."\" /></a>";
	}
	else {
		echo "<input type=\"button\" name=\"".$bname."\" id=\"".$bname."\" value=\"".$bvalue."\" onclick=\"".$bevent."; document.".$formname.".submit()\" />";
	}
}

/**
 * Display field name and enable an overlib quick description
 * help if K_DISPLAY_QUICK_HELP constant is set to TRUE
 * @param string $field_template name of the language resource ($l[$field_template]) to display
 * @param string $help_template name of the language help resource to display as tooltip
 */
function F_display_field_name($field_template, $help_template) {
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_language.'.CP_EXT);
	global $l, $selected_language;
	if ((!K_DISPLAY_QUICK_HELP) OR (!$help_template)) {
		return ($l[$field_template]);
	}
	else {
		$helpdesc = F_help_language($selected_language, $help_template);
		$helpdesc = F_remove_linebreaks($helpdesc);
		return ("<a class=\"quickhelp\" onmouseover=\"FJ_show_hide_form_elements(true); return overlib('".F_replace_quotes(F_compact_string($helpdesc))."', CAPTION, '".F_replace_quotes($l[$field_template])."', AUTOSTATUSCAP, CSSCLASS, FGCLASS, 'overlibfg', BGCLASS, 'overlibbg', TEXTFONTCLASS, 'overlibtxt', CAPTIONFONTCLASS, 'overlibcf');\" onmouseout=\"FJ_show_hide_form_elements(false); nd();\" onfocus=\"FJ_show_hide_form_elements(true); return overlib('".F_replace_quotes(F_compact_string($helpdesc))."', CAPTION, '".F_replace_quotes($l[$field_template])."', AUTOSTATUSCAP, CSSCLASS, FGCLASS, 'overlibfg', BGCLASS, 'overlibbg', TEXTFONTCLASS, 'overlibtxt', CAPTIONFONTCLASS, 'overlibcf');\" onblur=\"FJ_show_hide_form_elements(false); nd();\" href=\"javascript:void(0)\">".$l[$field_template]."</a>");
	}
}

/**
 * Display field name with link and enable an overlib quick
 * description help if K_DISPLAY_QUICK_HELP constant is set to TRUE
 * @param string $field_template name of the language resource ($l[$field_template]) to display
 * @param string $help_template name of the language help resource to display as tooltip
 * @param string $onclick action to perform on onclick event
 */
function F_display_field_name_link($field_template, $help_template, $onclick) {
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_language.'.CP_EXT);
	global $l, $selected_language;
	if ((!K_DISPLAY_QUICK_HELP) OR (!$help_template)) {
		return ("<a class=\"quickhelplink\" href=\"javascript:void(0)\" onclick=\"".$onclick."\">".$l[$field_template]."</a>");
	}
	else {
		$helpdesc = F_help_language($selected_language, $help_template);
		$helpdesc = F_remove_linebreaks($helpdesc);
		return ("<a class=\"quickhelplink\" onmouseover=\"FJ_show_hide_form_elements(true); return overlib('".F_replace_quotes(F_compact_string($helpdesc))."', CAPTION, '".F_replace_quotes($l[$field_template])."', AUTOSTATUSCAP, CSSCLASS, FGCLASS, 'overlibfg', BGCLASS, 'overlibbg', TEXTFONTCLASS, 'overlibtxt', CAPTIONFONTCLASS, 'overlibcf')\" onmouseout=\"FJ_show_hide_form_elements(false); nd();\" onfocus=\"FJ_show_hide_form_elements(true); return overlib('".F_replace_quotes(F_compact_string($helpdesc))."', CAPTION, '".F_replace_quotes($l[$field_template])."', AUTOSTATUSCAP, CSSCLASS, FGCLASS, 'overlibfg', BGCLASS, 'overlibbg', TEXTFONTCLASS, 'overlibtxt', CAPTIONFONTCLASS, 'overlibcf')\" onblur=\"FJ_show_hide_form_elements(false); nd();\" href=\"javascript:void(0)\" onclick=\"".$onclick."\">".$l[$field_template]."</a>");
	}
}

/**
 * Display a list of numeric and alphabetic buttons selectors
 * @param string $formname name of the form
 * @param string $fieldname name of the field
 * @param string $selectedletter selected letter
 * @param string $disabled_letters list of disabled letters,default=NULL
 */
function F_alphanumeric_selector($formname, $fieldname, $selectedletter, $disabled_letters=NULL) {
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	global $aiocp_dp;
	$alphabet = Array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
	echo "\n<form action=\"".$_SERVER['SCRIPT_NAME']."";
	if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {
		echo "?aiocp_dp=".$aiocp_dp."";
	}
	echo "\" name=\"form_alphanumsel\" id=\"form_alphanumsel\">";
 	F_generic_submit_button($formname, "alphaall", "*", "document.".$formname.".".$fieldname.".value='';document.".$formname.".firstrow.value=0");
	while (list($key, $letter) = each($alphabet)) {
		echo "\n";
		if ($letter != $selectedletter) {
			if (is_array($disabled_letters) AND in_array($letter, $disabled_letters)) {
				echo ''; //do not display disabled letters
			} else {
				F_generic_submit_button($formname, 'alpha'.$key,$letter, 'document.'.$formname.'.'.$fieldname.'.value=\''.$letter.'\';document.'.$formname.'.firstrow.value=0');
			}
		} else {
			echo '<b>'.$letter.'</b>';
		}
	}
	echo "</form>\n";
}

//============================================================+
// END OF FILE
//============================================================+
?>
