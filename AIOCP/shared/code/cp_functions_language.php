<?php
//============================================================+
// File name   : cp_functions_language.php
// Begin       : 2001-10-23
// Last Update : 2007-01-11
// 
// Description : Functions to select language
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

// ------------------------------------------------------------
// Show Laguage menu
// note that choosed_language will be evaluated in cp_authorization file
// ------------------------------------------------------------
function F_choose_language() {
	global $l, $db, $selected_language, $aiocp_dp;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	if(isset($_REQUEST['choosed_language'])) { //if the below form has been submitted...
		
		echo "<"."?xml version=\"1.0\" encoding=\"".$l['a_meta_charset']."\"?".">\n";
		echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"DTD/xhtml1-transitional.dtd\">\n";
		echo "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"".$l['a_meta_language']."\" lang=\"".$l['a_meta_language']."\" dir=\"".$l['a_meta_dir']."\">\n";
		echo "<head>\n";
		echo "<title>ENTER</title>\n";
		if(K_CHECK_JAVASCRIPT) {
			echo "<noscript><meta http-equiv='refresh' content='0;url=".K_REDIRECT_JAVASCRIPT_ERROR."' /></noscript>\n";
			echo "<meta name=\"robots\" content=\"index,follow\" />\n";
		}
		
		if(K_USE_FRAMES) { //reload all frames from index (if javascript enable)
			echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
			echo "//<![CDATA[\n";
			echo "var mainpage = escape(parent.frames['".K_MAIN_FRAME_NAME."'].location.href);\n";
			echo "top.location.replace(\"../code/index.".CP_EXT."?load_page=\" + mainpage);\n";
			echo "//]]>\n";
			echo "</script>\n";
			echo "</head>\n";
			echo "<body>\n";
			echo "<a href=\"".$_SERVER['SCRIPT_NAME']."\" target=\"_top\">ENTER</a>\n";
			echo "</body>\n";
			echo "</html>\n";
			exit;
		}
		else { //reload page
			echo "<meta http-equiv=\"refresh\" content=\"0\" />\n"; //reload page
			echo "</head>\n";
			echo "<body>\n";
			echo "<a href=\"".$_SERVER['SCRIPT_NAME']."\" target=\"_top\">ENTER</a>\n";
			echo "</body>\n";
			echo "</html>\n";
			exit;
		}
	}
?>
<table border="0" cellspacing="0" cellpadding="0">
<tr><td>
	<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_changelanguage" id="form_changelanguage">
	<select class="changelanguage" name="choosed_language" id="choosed_language" size="0" onchange="document.form_changelanguage.submit()">
<?php
	$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"".$m['language_code']."\"";
			if($m['language_code'] == $selected_language) {
				echo " selected=\"selected\"";
			}
			echo ">".htmlentities($m['language_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
		}
	}
	else {
		F_display_db_error();
	}
?>
</select>

<?php
if (isset($flaglinks)) {
	echo $flaglinks; //display flag links
}
?>
</form>
</td></tr>
</table>
<?php
}


// ------------------------------------------------------------
// Show Laguage selection menu
// note that choosed_language will be evaluated in cp_authorization file
// ------------------------------------------------------------
function F_choose_page_language() {
	global $l, $db, $selected_language;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	$lscode = ""; //return code
	$flaglinks = ""; //flag links
	
	//reload all frames with new language
	
	if ($_SERVER["argc"]) { 
		$param_separator = "&amp;";
	}
	else {
		$param_separator = "?";
	}
	
	if(K_USE_FRAMES) { //reload all frames from index (if javascript enable)
		$select_action = "top.location.replace(\"../code/index.".CP_EXT."?load_page=\" + escape(parent.frames['".K_MAIN_FRAME_NAME."'].location.href) + '".$param_separator."choosed_language=' + document.form_changelanguage.choosed_language.options[document.form_changelanguage.choosed_language.selectedIndex].value);";
		$flag_action = "top.location.replace(\"../code/index.".CP_EXT."?load_page=\" + escape(parent.frames['".K_MAIN_FRAME_NAME."'].location.href) + '".$param_separator."choosed_language=";
	}
	else { //reload page
		$select_action = "top.location.replace(window.location.href + '".$param_separator."choosed_language=' + document.form_changelanguage.choosed_language.options[document.form_changelanguage.choosed_language.selectedIndex].value);";
		$flag_action = "top.location.replace(window.location.href + '".$param_separator."choosed_language=";
	}
	
	$lscode .= "<form action=\"".$_SERVER['SCRIPT_NAME']."";
	if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {
		$lscode .= "?aiocp_dp=".$aiocp_dp."";
	}
	$lscode .= "\" method=\"post\" enctype=\"multipart/form-data\" name=\"form_changelanguage\" id=\"form_changelanguage\">";
	$lscode .= "<select class=\"changelanguage\" name=\"choosed_language\" id=\"choosed_language\" size=\"0\" onchange=\"".$select_action."\">";
	$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			$lscode .= "<option value=\"".$m['language_code']."\"";
			if($m['language_code'] == $selected_language) {
				$lscode .= " selected=\"selected\"";
			}
			$lscode .= ">".$m['language_name']."</option>\n";
			
			if (K_SHOW_LANG_FLAGS) {
				$flaglinks .= "<img src=\"".F_word_language($m['language_code'], "x_flag")."\" border=\"0\" alt=\"".$m['language_name']."\" onclick=\"".$flag_action.$m['language_code']."');\" /> ";
			}
		}
	}
	else {
		F_display_db_error();
	}
	$lscode .= "</select>\n";
	$lscode .= $flaglinks."\n";
	$lscode .= "</form>\n";
	return $lscode;
}


// ------------------------------------------------------------
// Return the selected word template for specified language
// ------------------------------------------------------------
function F_word_language($language, $word) {
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	global $db;
	
	if (!$word) {
		return "";
	}
	
	$sql = "SELECT ".$language." FROM ".K_TABLE_LANGUAGE_DATA." WHERE word_id='".$word."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$thisword = $m[$language];
		}
		else {
			//try to return in default language
			if($language != K_DEFAULT_LANGUAGE) {
				return F_word_language(K_DEFAULT_LANGUAGE, $word);
			}
			$thisword = "";
		}
	}
	else {
		F_display_db_error();
	}
	return ($thisword);
}

// ------------------------------------------------------------
// Return the selected Help template for specified language
// ------------------------------------------------------------
function F_help_language($language, $help_template) {
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	global $db;
	
	if (!$help_template) {return "";}
	
	$sql = "SELECT ".$language." FROM ".K_TABLE_LANGUAGE_HELP." WHERE help_id='".$help_template."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$thistemplate = $m[$language]; 
			//$thistemplate = F_remove_linebreaks($thistemplate); // remove line breaks
		}
		else {
			//try to return in default language
			if($language != K_DEFAULT_LANGUAGE) {
				return F_help_language(K_DEFAULT_LANGUAGE, $help_template);
			}
			$thistemplate = "";
		}
	}
	else {
		F_display_db_error();
	}
	return ($thistemplate);
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
