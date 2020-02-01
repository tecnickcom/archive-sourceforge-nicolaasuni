<?php
//============================================================+
// File name   : cp_edit_newpage.php                           
// Begin       : 2001-04-04                                    
// Last Update : 2003-11-18                                    
//                                                             
// Description : New PHP page wizard editor                    
//               Create new php pages in AIOCP style           
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_NEWPAGE;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

require_once('../../shared/code/cp_functions_form.'.CP_EXT);
require_once('../code/cp_functions_paste_services.'.CP_EXT);

$thispage_title = $l['t_php_page_wizard'];
?>

<?php
switch($menu_mode) {
	case unhtmlentities($l['w_submit']):
	case $l['w_submit']: { //compose and submit php page
		if($formstatus = F_check_form_fields()) { //check form data
			// Check if pagename is unique
			$sql = "SELECT page FROM ".K_TABLE_LANGUAGE_PAGES." WHERE page='".$newpage_name."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				//build strings for add queries (check enabled languages...)
				$fields_string = ""; //list of languages to update
				$p_title_string = "";
				$p_description_string = "";
				$p_keywords_string = "";
				$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
				if($r = F_aiocpdb_query($sql, $db)) {
					while($m = F_aiocpdb_fetch_array($r)) {
						$fields_string .= ", ".$m['language_code']; //list of fields to change (all language columns)
						$p_title_string .= ", '".$newpage_title[$m['language_code']]."'";
						$p_description_string .= ", '".$newpage_description[$m['language_code']]."'";
						$p_keywords_string .= ", '".$newpage_keywords[$m['language_code']]."'";
					}
				}
				else {
					F_display_db_error();
				}
				// add dynamic page data to database...
				$sql1 = "INSERT IGNORE INTO ".K_TABLE_LANGUAGE_PAGES." (page, template ".$fields_string.") VALUES ('".$newpage_name."', '_title' ".$p_title_string.")";
				$sql2 = "INSERT IGNORE INTO ".K_TABLE_LANGUAGE_PAGES." (page, template ".$fields_string.") VALUES ('".$newpage_name."', '_description' ".$p_description_string.")";
				$sql3 = "INSERT IGNORE INTO ".K_TABLE_LANGUAGE_PAGES." (page, template ".$fields_string.") VALUES ('".$newpage_name."', '_keywords' ".$p_keywords_string.")";
				if( (!$r1 = F_aiocpdb_query($sql1, $db)) OR (!$r2 = F_aiocpdb_query($sql2, $db)) OR (!$r3 = F_aiocpdb_query($sql3, $db)) ) {
					F_display_db_error();
				}
				
				// Build page text code
				$newpagetxt = "<"."?php\n";
				$newpagetxt .= "/* ============================================================\n"; //comments
				$newpagetxt .= " File name   : \n";
				$newpagetxt .= " Begin       : ".gmdate("Y-m-d H:i:s")."\n";
				$newpagetxt .= " Last update : ".gmdate("Y-m-d H:i:s")."\n";
				$newpagetxt .= " Author      : (".$_SESSION['session_user_id'].") ".$_SESSION['session_user_name']."\n";
				$newpagetxt .= " Description : ".stripslashes($newpage_description[K_DEFAULT_LANGUAGE])."\n";
				$newpagetxt .= "=============================================================== */\n";
				$newpagetxt .= "\n";
				$newpagetxt .= "require_once('../../shared/config/cp_extension.inc');\n";
				$newpagetxt .= "require_once('../config/cp_config.'.CP_EXT);\n";
				$newpagetxt .= "\$pagelevel = ".$newpage_level."; // page level\n";
				$newpagetxt .= "require_once('../../shared/code/cp_authorization.'.CP_EXT); // check authorization permissions\n";
				$newpagetxt .= "require_once('../../shared/code/cp_functions_collect_stats.'.CP_EXT); // collect site statistics\n";
				$newpagetxt .= "\$page_name = \"".$newpage_name."\"; // name of the page (used in DB)\n";
				$newpagetxt .= "\$pt = F_load_page_templates(\$selected_language, \$page_name); // load page templates for the current language\n";
				$newpagetxt .= "\n";
				$newpagetxt .= "// The following are values for META TAGS\n";
				$newpagetxt .= "// (leave void for default values)\n";
				$newpagetxt .= "\$thispage_author = \"".$newpage_author."\"; // name of page author\n";
				$newpagetxt .= "\$thispage_reply = \"".$newpage_reply."\"; // email address\n";
				$newpagetxt .= "\$thispage_style = \"".$newpage_style."\"; // CSS page link\n";
				$newpagetxt .= "\$thispage_title = \$pt['_title']; // page title\n";
				$newpagetxt .= "\$thispage_description = \$pt['_description']; // page description\n";
				$newpagetxt .= "\$thispage_keywords = \$pt['_keywords']; // page keywords\n";
				$newpagetxt .= "\n";
				if ($newpage_header) {$newpagetxt .= "require_once('".$newpage_header."'); // page header\n";}
				$newpagetxt .= "F_print_error(0, \"\"); //clear error and system messages\n";
				$newpagetxt .= "?".">\n";
				$newpagetxt .= "<!-- ====================================================== -->\n";
				$newpagetxt .= "<!-- put your code in this area-->\n";
				$newpagetxt .= "\n";
				if ($newpage_service) { // put a module code (forum, newsletter, ...)
					$newpagetxt .= "<"."?php\n";
					$newpagetxt .= F_get_service_code($newpage_service);
					$newpagetxt .= "\n?".">\n";
				}
				$newpagetxt .= "<!-- ====================================================== -->\n";
				
				if ($newpage_footer) {
					$newpagetxt .= "<"."?php\n";
					$newpagetxt .= "require_once('".$newpage_footer."'); // page footer\n";
					$newpagetxt .= "?".">\n";
				}
				
				//paste to calling document
				echo "<form name=\"senderform\" id=\"senderform\">";
				echo "<textarea cols=\"10\" rows=\"10\" name=\"texttosend\" id=\"texttosend\">".$newpagetxt."</textarea>";
				echo "</form>";
				echo "<script language=\"JavaScript\" type=\"text/javascript\">";
				echo "//<![CDATA[\n";
				echo "window.opener.document.".$callingform.".".$callingfield.".value=document.senderform.texttosend.value;";
				echo "window.close()";
				echo "//]]>\n";
				echo "</script>";
				exit();
			}
		}
		break;
	}
	
	default:
	case unhtmlentities($l['w_clear']):
	case $l['w_clear']: {
		$newpage_level = 0;
		$newpage_author = "";
		$newpage_reply = "";
		$newpage_style = "";
		$newpage_header = "../code/cp_page_header.".CP_EXT;
		$newpage_footer = "../code/cp_page_footer.".CP_EXT;
		$newpage_title = array();
		$newpage_description = array();
		$newpage_keywords = array();
		break;
	}
}

require_once('../code/cp_page_header_popup.'.CP_EXT);

//load overlib to display quick description help
if (K_DISPLAY_QUICK_HELP) { ?>
<!-- overLIB ==================== -->
<div id="overDiv" style="z-index: 1000; visibility: hidden; position: absolute"></div>
<script language="JavaScript" src="<?php echo K_PATH_SHARED_JSCRIPTS; ?>overlib_aiocp.js" type="text/javascript"></script>
<!-- END overLIB ==================== -->
<?php } ?>

<!-- ====================================================== -->

<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_newpageeditor" id="form_newpageeditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="newpage_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_name', 'h_dpageed_name'); ?></b></td>
<td class="fillOE"><input type="text" name="newpage_name" id="newpage_name" value="<?php echo $newpage_name; ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_level', 'h_dpageed_level'); ?></b></td>
<td class="fillEE" colspan="2"><select name="newpage_level" id="newpage_level" size="0">
<?php
$sql = "SELECT * FROM ".K_TABLE_LEVELS." ORDER BY level_code";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['level_code']."\"";
		if($m['level_code'] == $newpage_level) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['level_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_author', 'h_dpageed_author'); ?></b></td>
<td class="fillOE"><input type="text" name="newpage_author" id="newpage_author" value="<?php echo $newpage_author; ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_reply', 'h_dpageed_reply'); ?></b></td>
<td class="fillEE"><input type="text" name="newpage_reply" id="newpage_reply" value="<?php echo $newpage_reply; ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_style', 'h_dpageed_style'); ?></b></td>
<td class="fillOE"><input type="text" name="newpage_style" id="newpage_style" value="<?php echo $newpage_style; ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_header', ''); ?></b></td>
<td class="fillEE"><input type="text" name="newpage_header" id="newpage_header" value="<?php echo $newpage_header; ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_footer', ''); ?></b></td>
<td class="fillOE"><input type="text" name="newpage_footer" id="newpage_footer" value="<?php echo $newpage_footer; ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_module', 'h_pagemod_select'); ?></b></td>
<td class="fillEE" colspan="2"><select name="newpage_service" id="newpage_service" size="0">
<option value="">&nbsp;</option>
<?php
	$sql = "SELECT * FROM ".K_TABLE_PAGE_MODULES." ORDER BY pagemod_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"".$m['pagemod_id']."\"";
			if($m['pagemod_id'] == $newpage_service) {
				echo " selected=\"selected\"";
			}
			echo ">".$m['pagemod_name']."</option>\n";
		}
	}
	else {
		F_display_db_error();
	}
?>
</select></td>
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
			echo "<td class=\"fillEO\" align=\"right\" valign=\"top\"><b>".F_display_field_name('w_title', 'h_dpageed_title')."</b>";
			echo "</td>";
			echo "<td class=\"fillEE\"><input type=\"text\" name=\"newpage_title[".$m['language_code']."]\" id=\"newpage_title_".$m['language_code']."\" value=\"".stripslashes($newpage_title[$m['language_code']])."\" size=\"30\" maxlength=\"255\" /></td>";
			echo "</tr>";
			
			echo "<tr class=\"fillO\">";
			echo "<td class=\"fillOO\" align=\"right\" valign=\"top\"><b>".F_display_field_name('w_description', 'h_dpageed_description')."</b>";
			echo "</td>";
			echo "<td class=\"fillOE\"><input type=\"text\" name=\"newpage_description[".$m['language_code']."]\" id=\"newpage_description_".$m['language_code']."\" value=\"".stripslashes($newpage_description[$m['language_code']])."\" size=\"30\" maxlength=\"255\" /></td>";
			echo "</tr>";
			
			echo "<tr class=\"fillE\">";
			echo "<td class=\"fillEO\" align=\"right\" valign=\"top\"><b>".F_display_field_name('w_keywords', 'h_dpageed_keywords')."</b>";
			echo "</td>";
			echo "<td class=\"fillEE\"><input type=\"text\" name=\"newpage_keywords[".$m['language_code']."]\" id=\"newpage_keywords_".$m['language_code']."\" value=\"".stripslashes($newpage_keywords[$m['language_code']])."\" size=\"30\" maxlength=\"255\" /></td>";
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
<input type="hidden" name="callingform" id="callingform" value="<?php echo $callingform; ?>" />
<input type="hidden" name="callingfield" id="callingfield" value="<?php echo $callingfield; ?>" />
<?php F_submit_button("form_newpageeditor", "menu_mode", $l['w_clear']); ?> 
<?php F_generic_button("cancel", $l['w_cancel'], "window.close()"); ?>
<?php F_submit_button("form_newpageeditor", "menu_mode", $l['w_submit']); ?> 
</td>
</tr>

</table>

</form>
<!-- ====================================================== -->
<?php 
require_once('../code/cp_page_footer_popup.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
