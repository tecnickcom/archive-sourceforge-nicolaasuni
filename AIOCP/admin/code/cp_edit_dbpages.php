<?php
//============================================================+
// File name   : cp_edit_dbpages.php                           
// Begin       : 2002-04-20                                    
// Last Update : 2012-11-27
//                                                             
// Description : Edit pages that will be stored on database    
//                                                             
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Tecnick.com s.r.l.
//               Via Della Pace n. 11
//               09044 Quartucciu (CA)
//               ITALY
//               www.tecnick.com
//               info@tecnick.com
//
// License: GNU GENERAL PUBLIC LICENSE v.2
//          http://www.gnu.org/copyleft/gpl.html
//============================================================+

require_once('../../shared/config/cp_extension.inc');
require_once('../config/cp_config.'.CP_EXT);

$pagelevel = K_AUTH_ADMIN_CP_EDIT_DBPAGES;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

require_once('../../shared/code/cp_functions_form.'.CP_EXT);

$thispage_title = $l['t_dynamic_page_wizard'];

if (isset($_REQUEST['page']) AND $_REQUEST['page']) {
	$sql = "SELECT pagedata_id FROM ".K_TABLE_PAGE_DATA." WHERE pagedata_name='".$_REQUEST['page']."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$pagedata_id = $m['pagedata_id'];
		}
	}
	else {
		F_display_db_error();
	}
}

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
		F_stripslashes_formfields(); // ask confirmation
		F_print_error("WARNING", $l['m_delete_confirm']);
		?>
		<p><?php echo $l['t_warning'].": ".$l['d_page_delete']; ?></p>
		
		<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_delete" id="form_delete">
		<input type="hidden" name="menu_mode" id="menu_mode" value="forcedelete" />
		<input type="hidden" name="pagedata_id" id="pagedata_id" value="<?php echo $pagedata_id; ?>" />
		<input type="hidden" name="forcedelete" id="forcedelete" value="" />
		<?php 
		F_submit_button("form_delete","forcedelete",$l['w_delete']);
		F_submit_button("form_delete","forcedelete",$l['w_cancel']);
		?>
		</form>
		
		<?php
		break;
	}

	case "forcedelete":{ // Delete
		//retrieve name from id
		$sql = "SELECT pagedata_name FROM ".K_TABLE_PAGE_DATA." WHERE pagedata_id='".$pagedata_id."'";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$pagedata_name = $m['pagedata_name'];
			}
		}
		else {
			F_display_db_error();
		}
		// delete data on table K_TABLE_PAGE_DATA
		$sql = "DELETE FROM ".K_TABLE_PAGE_DATA." WHERE pagedata_id='".$pagedata_id."'";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		// delete page templates on table K_TABLE_LANGUAGE_PAGES
		$sql = "DELETE FROM ".K_TABLE_LANGUAGE_PAGES." WHERE page='".$pagedata_name."'";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		$pagedata_id = FALSE;
		break;
		}

		case unhtmlentities($l['w_update']):
		case $l['w_update']:{ // Update
			if($formstatus = F_check_form_fields()) {
				//check if name is unique
				if(!F_check_unique(K_TABLE_PAGE_DATA, "pagedata_id='".$pagedata_id."'", "pagedata_name", $pagedata_name)) {
					F_print_error("WARNING", $l['m_duplicate_name']);
					$formstatus = FALSE; F_stripslashes_formfields();
				}
				else {
					//update K_TABLE_PAGE_DATA pagedata_enabled
					$sql = "UPDATE IGNORE ".K_TABLE_PAGE_DATA." SET 
					pagedata_name='".$pagedata_name."', 
					pagedata_level='".$pagedata_level."', 
					pagedata_author='".$pagedata_author."', 
					pagedata_replyto='".$pagedata_replyto."', 
					pagedata_style='".$pagedata_style."', 
					pagedata_hf='".$pagedata_hf."', 
					pagedata_enabled='".$pagedata_enabled."' 
					WHERE pagedata_id=".$pagedata_id."";
					if(!$r = F_aiocpdb_query($sql, $db)) {
						F_display_db_error();
					}
					//update K_TABLE_LANGUAGE_PAGES
					$SETstring1 = "";
					$SETstring2 = "";
					$SETstring3 = "";
					$SETstring4 = "";
					$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
					if($r = F_aiocpdb_query($sql, $db)) {
						while($m = F_aiocpdb_fetch_array($r)) {
							// build a list of fields to change (all language columns)
							$SETstring1 .= ", ".$m['language_code']."='".$pagedata_title[$m['language_code']]."'";
							$SETstring2 .= ", ".$m['language_code']."='".$pagedata_description[$m['language_code']]."'";
							$SETstring3 .= ", ".$m['language_code']."='".$pagedata_keywords[$m['language_code']]."'";
							$SETstring4 .= ", ".$m['language_code']."='".$pagedata_code[$m['language_code']]."'";
						}
					}
					else {
						F_display_db_error();
					}
					$SETstring1 = substr($SETstring1,1);
					$SETstring2 = substr($SETstring2,1);
					$SETstring3 = substr($SETstring3,1);
					$SETstring4 = substr($SETstring4,1);
					$sql1 = "UPDATE IGNORE ".K_TABLE_LANGUAGE_PAGES." SET ".$SETstring1." WHERE page='".$pagedata_name."' AND template='_title'";
					$sql2 = "UPDATE IGNORE ".K_TABLE_LANGUAGE_PAGES." SET ".$SETstring2." WHERE page='".$pagedata_name."' AND template='_description'";
					$sql3 = "UPDATE IGNORE ".K_TABLE_LANGUAGE_PAGES." SET ".$SETstring3." WHERE page='".$pagedata_name."' AND template='_keywords'";
					$sql4 = "UPDATE IGNORE ".K_TABLE_LANGUAGE_PAGES." SET ".$SETstring4." WHERE page='".$pagedata_name."' AND template='_body'";
					if ( (!$r1 = F_aiocpdb_query($sql1, $db)) OR
						(!$r2 = F_aiocpdb_query($sql2, $db)) OR
						(!$r3 = F_aiocpdb_query($sql3, $db)) OR
						(!$r4 = F_aiocpdb_query($sql4, $db)) ) {
						F_display_db_error();
					}
				}
			}
			break;
		}

	case unhtmlentities($l['w_add']):
	case $l['w_add']:{ // Add
		if($formstatus = F_check_form_fields()) {
			//check if template_id is unique
			$sql = "SELECT pagedata_id FROM ".K_TABLE_PAGE_DATA." WHERE pagedata_name='".$pagedata_name."'";
			if(($r = F_aiocpdb_query($sql, $db)) AND (F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add
				//add on K_TABLE_PAGE_DATA
				$sql = "INSERT IGNORE INTO ".K_TABLE_PAGE_DATA." (
				pagedata_name, 
				pagedata_level, 
				pagedata_author, 
				pagedata_replyto, 
				pagedata_style, 
				pagedata_hf, 
				pagedata_enabled
				) VALUES (
				'".$pagedata_name."', 
				'".$pagedata_level."', 
				'".$pagedata_author."', 
				'".$pagedata_replyto."', 
				'".$pagedata_style."', 
				'".$pagedata_hf."', 
				'".$pagedata_enabled."')";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$pagedata_id = F_aiocpdb_insert_id();
				}
				
				//add on K_TABLE_LANGUAGE_PAGES
				$FIELDSstring = "";
				$VALUESstring1 = "";
				$VALUESstring2 = "";
				$VALUESstring3 = "";
				$VALUESstring4 = "";
				$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
				if($r = F_aiocpdb_query($sql, $db)) {
					while($m = F_aiocpdb_fetch_array($r)) {
						$FIELDSstring .= ", ".$m['language_code']; //list of fields to change (all language columns)
						$VALUESstring1 .= ", '".$pagedata_title[$m['language_code']]."'"; //list of values
						$VALUESstring2 .= ", '".$pagedata_description[$m['language_code']]."'"; //list of values
						$VALUESstring3 .= ", '".$pagedata_keywords[$m['language_code']]."'"; //list of values
						$VALUESstring4 .= ", '".$pagedata_code[$m['language_code']]."'"; //list of values
					}
				}
				else {
					F_display_db_error();
				}
				$sql1 = "INSERT IGNORE INTO ".K_TABLE_LANGUAGE_PAGES." (page, template ".$FIELDSstring.") VALUES ('".$pagedata_name."', '_title' ".$VALUESstring1.")";
				$sql2 = "INSERT IGNORE INTO ".K_TABLE_LANGUAGE_PAGES." (page, template ".$FIELDSstring.") VALUES ('".$pagedata_name."', '_description' ".$VALUESstring2.")";
				$sql3 = "INSERT IGNORE INTO ".K_TABLE_LANGUAGE_PAGES." (page, template ".$FIELDSstring.") VALUES ('".$pagedata_name."', '_keywords' ".$VALUESstring3.")";
				$sql4 = "INSERT IGNORE INTO ".K_TABLE_LANGUAGE_PAGES." (page, template ".$FIELDSstring.") VALUES ('".$pagedata_name."', '_body' ".$VALUESstring4.")";
				if ( (!$r1 = F_aiocpdb_query($sql1, $db)) OR
					(!$r2 = F_aiocpdb_query($sql2, $db)) OR
					(!$r3 = F_aiocpdb_query($sql3, $db)) OR
					(!$r4 = F_aiocpdb_query($sql4, $db)) ) {
						F_display_db_error();
					}
			}
		}
		break;
		}

		
	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$pagedata_name = "";
		$pagedata_level = 0;
		$pagedata_author = "";
		$pagedata_replyto = "";
		$pagedata_style = "";
		$pagedata_hf = "";
		$pagedata_enabled = 0;
		
		$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
		if($r = F_aiocpdb_query($sql, $db)) {
			while($m = F_aiocpdb_fetch_array($r)) {
				$pagedata_title[$m['language_code']] = "";
				$pagedata_description[$m['language_code']] = "";
				$pagedata_keywords[$m['language_code']] = "";
				$pagedata_code[$m['language_code']] = "";
			}
		}
		else {
			F_display_db_error();
		}
		break;
		}

	default :{ 
		break;
		}
}

// Initialize variables
if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!isset($pagedata_id) OR (!$pagedata_id)) {
			$sql = "SELECT * FROM ".K_TABLE_PAGE_DATA." ORDER BY pagedata_name LIMIT 1";
		} else {
			$sql = "SELECT * FROM ".K_TABLE_PAGE_DATA." WHERE pagedata_id='".$pagedata_id."' LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$pagedata_id = $m['pagedata_id'];
				$pagedata_name = $m['pagedata_name'];
				$pagedata_level = $m['pagedata_level'];
				$pagedata_author = $m['pagedata_author'];
				$pagedata_replyto = $m['pagedata_replyto'];
				$pagedata_style = $m['pagedata_style'];
				$pagedata_hf = $m['pagedata_hf'];
				$pagedata_enabled = $m['pagedata_enabled'];
				
				$sqlL = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
				if($rL = F_aiocpdb_query($sqlL, $db)) {
					while($mL = F_aiocpdb_fetch_array($rL)) {
						$sql1 = "SELECT * FROM ".K_TABLE_LANGUAGE_PAGES." WHERE page='".$pagedata_name."' AND template='_title' LIMIT 1";
						if($r1 = F_aiocpdb_query($sql1, $db)) {
							if($m1 = F_aiocpdb_fetch_array($r1)) {
								$pagedata_title[$mL['language_code']] = $m1[$mL['language_code']];
							}
						}
						else {
							F_display_db_error();
						}
						$sql2 = "SELECT * FROM ".K_TABLE_LANGUAGE_PAGES." WHERE page='".$pagedata_name."' AND template='_description' LIMIT 1";
						if($r2 = F_aiocpdb_query($sql2, $db)) {
							if($m2 = F_aiocpdb_fetch_array($r2)) {
								$pagedata_description[$mL['language_code']] = $m2[$mL['language_code']];
							}
						}
						else {
							F_display_db_error();
						}
						$sql3 = "SELECT * FROM ".K_TABLE_LANGUAGE_PAGES." WHERE page='".$pagedata_name."' AND template='_keywords' LIMIT 1";
						if($r3 = F_aiocpdb_query($sql3, $db)) {
							if($m3 = F_aiocpdb_fetch_array($r3)) {
								$pagedata_keywords[$mL['language_code']] = $m3[$mL['language_code']];
							}
						}
						else {
							F_display_db_error();
						}
						$sql4 = "SELECT * FROM ".K_TABLE_LANGUAGE_PAGES." WHERE page='".$pagedata_name."' AND template='_body' LIMIT 1";
						if($r4 = F_aiocpdb_query($sql4, $db)) {
							if($m4 = F_aiocpdb_fetch_array($r4)) {
								$pagedata_code[$mL['language_code']] = $m4[$mL['language_code']];
							}
						}
						else {
							F_display_db_error();
						}
					}
				}
				else {
					F_display_db_error();
				}
			}
			else { //clear fields
				$pagedata_name = "";
				$pagedata_level = 0;
				$pagedata_author = "";
				$pagedata_replyto = "";
				$pagedata_style = "";
				$pagedata_hf = "";
				$pagedata_enabled = 0;
				
				$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
				if($r = F_aiocpdb_query($sql, $db)) {
					while($m = F_aiocpdb_fetch_array($r)) {
						$pagedata_title[$m['language_code']] = "";
						$pagedata_description[$m['language_code']] = "";
						$pagedata_keywords[$m['language_code']] = "";
						$pagedata_code[$m['language_code']] = "";
					}
				}
				else {
					F_display_db_error();
				}
			}
		}
		else {
			F_display_db_error();
		}
	}
}
?>
<script language="JavaScript" src="../ckeditor/ckeditor.js"></script>
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_dbpageeditor" id="form_dbpageeditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="pagedata_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT PAGE ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_page', 'h_dpageed_select'); ?></b></td>
<td class="fillEE">
<select name="pagedata_id" id="pagedata_id" size="0" onchange="document.form_dbpageeditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_PAGE_DATA." ORDER BY pagedata_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['pagedata_id']."\"";
		if($m['pagedata_id'] == $pagedata_id) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['pagedata_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT PAGE ==================== -->

<tr class="fillO">
<td class="fillOO">&nbsp;</td>
<td class="fillOE"><hr /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_enabled', 'h_dpageed_enable'); ?></b></td>
<td class="fillEE">
<?php
echo "<input type=\"radio\" name=\"pagedata_enabled\" value=\"1\"";
if($pagedata_enabled) {echo " checked=\"checked\"";}
echo " />".$l['w_yes']."&nbsp;";

echo "<input type=\"radio\" name=\"pagedata_enabled\" value=\"0\"";
if(!$pagedata_enabled) {echo " checked=\"checked\"";}
echo " />".$l['w_no']."&nbsp;";
?>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_name', 'h_dpageed_name'); ?></b></td>
<td class="fillOE"><input type="text" name="pagedata_name" id="pagedata_name" value="<?php echo htmlentities($pagedata_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_level', 'h_dpageed_level'); ?></b></td>
<td class="fillEE" colspan="2"><select name="pagedata_level" id="pagedata_level" size="0">
<?php
$sql = "SELECT * FROM ".K_TABLE_LEVELS." ORDER BY level_code";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['level_code']."\"";
		if($m['level_code'] == $pagedata_level) {
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
<td class="fillOE"><input type="text" name="pagedata_author" id="pagedata_author" value="<?php echo $pagedata_author; ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_reply', 'h_dpageed_reply'); ?></b></td>
<td class="fillEE"><input type="text" name="pagedata_replyto" id="pagedata_replyto" value="<?php echo $pagedata_replyto; ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_style', 'h_dpageed_style'); ?></b></td>
<td class="fillOE">
<select name="pagedata_style" id="pagedata_style" size="0">
<option value="">&nbsp;</option>
<?php
// read directory for files
$handle = opendir(realpath(K_PATH_PUBLIC_CODE_REAL.K_PATH_STYLE_SHEETS));
	while (false !== ($file = readdir($handle))) {
		if(($file != ".")AND($file != "..")) {
			echo "<option value=\"".$file."\"";
			if($file == $pagedata_style) {
				echo " selected=\"selected\"";
			}
			echo ">".$file."</option>\n";
		}
	}
closedir($handle);
?>
</select>
</td>
</tr>

<!-- SELECT page header-footer ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_header', 'h_dpageed_header'); ?></b></td>
<td class="fillEE">
<select name="pagedata_hf" id="pagedata_hf" size="0">
<option value="">&nbsp;</option>
<?php
$sql = "SELECT * FROM ".K_TABLE_PAGE_HEADER_FOOTER." ORDER BY pagehf_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['pagehf_id']."\"";
		if($m['pagehf_id'] == $pagedata_hf) {
			echo " selected=\"selected\"";
		}
		echo ">".$m['pagehf_name']."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT page header-footer ==================== -->

<!-- iterate for each language ==================== -->
<?php
	$ckeditor_data = '';
	$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			
			$doc_charset = F_word_language($m['language_code'], "a_meta_charset");
			if ($doc_charset) {
				$doc_charset_url = "&amp;charset=".$doc_charset;
			}
			else {
				$doc_charset_url = "";
			}
			
			echo "<tr class=\"fillO\">";
			echo "<td class=\"fillOO\"><hr /></td>";
			echo "<td class=\"fillOE\"><b>".$m['language_name']."</b></td>";
			echo "</tr>";
			
			echo "<tr class=\"fillE\">";
			echo "<td class=\"fillEO\" align=\"right\" valign=\"top\"><b>".F_display_field_name('w_title', 'h_dpageed_title')."</b>";
			echo "</td>";
			echo "<td class=\"fillEE\"><input type=\"text\" name=\"pagedata_title[".$m['language_code']."]\" id=\"pagedata_title_".$m['language_code']."\" value=\"".htmlentities(stripslashes($pagedata_title[$m['language_code']]), ENT_COMPAT, $doc_charset)."\" size=\"60\" maxlength=\"255\" /></td>";
			echo "</tr>";
			
			echo "<tr class=\"fillO\">";
			echo "<td class=\"fillOO\" align=\"right\" valign=\"top\"><b>".F_display_field_name('w_description', 'h_dpageed_description')."</b>";
			echo "</td>";
			echo "<td class=\"fillOE\"><input type=\"text\" name=\"pagedata_description[".$m['language_code']."]\" id=\"pagedata_description_".$m['language_code']."\" value=\"".htmlentities(stripslashes($pagedata_description[$m['language_code']]), ENT_COMPAT, $doc_charset)."\" size=\"60\" maxlength=\"255\" /></td>";
			echo "</tr>";
			
			echo "<tr class=\"fillE\">";
			echo "<td class=\"fillEO\" align=\"right\" valign=\"top\"><b>".F_display_field_name('w_keywords', 'h_dpageed_keywords')."</b>";
			echo "</td>";
			echo "<td class=\"fillEE\"><input type=\"text\" name=\"pagedata_keywords[".$m['language_code']."]\" id=\"pagedata_keywords_".$m['language_code']."\" value=\"".htmlentities(stripslashes($pagedata_keywords[$m['language_code']]), ENT_COMPAT, $doc_charset)."\" size=\"60\" maxlength=\"255\" /></td>";
			echo "</tr>";
			
			echo "<tr class=\"fillO\">";
			echo "<td class=\"fillOO\" align=\"right\" valign=\"top\"><b>".F_display_field_name('w_code', 'h_dpageed_code')."</b><br />";
			?>
<input type="button" name="htmleditor_<?php echo $m['language_code']; ?>" id="htmleditor_<?php echo $m['language_code']; ?>" value="<?php echo $l['w_button_html_editor']; ?>" onclick="htmlWindow=window.open('cp_edit_html.<?php echo CP_EXT; ?>?templates=page&amp;callingform=form_dbpageeditor&amp;callingfield=elements['+FJ_get_field_index(this)+']<?php echo $doc_charset_url; ?>','htmlWindow','dependent,height=500,width=800,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')" />
			<?php
			echo "</td>";
			
			$current_ta_code = $pagedata_code[$m['language_code']];
			if(!$formstatus) {
				$current_ta_code = stripslashes($current_ta_code);
			}
			
			echo "<td class=\"fillOE\"><textarea cols=\"60\" rows=\"6\" name=\"pagedata_code[".$m['language_code']."]\" id=\"pagedata_code_".$m['language_code']."\">".htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset)."</textarea></td>";
			echo "</tr>";
			
			$ckeditor_data .= "CKEDITOR.replace('pagedata_code_".$m['language_code']."', {language: '".$l['a_meta_language']."', filebrowserBrowseUrl: '../ckeditor/filemanager/index.html'});\n";
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
if ($pagedata_id) {
	F_submit_button("form_dbpageeditor","menu_mode",$l['w_update']); 
	F_submit_button("form_dbpageeditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_dbpageeditor","menu_mode",$l['w_add']); 
F_submit_button("form_dbpageeditor","menu_mode",$l['w_clear']); 
?>
</td>
</tr>

<?php
if ($pagedata_name) {
	echo "<tr class=\"edge\"><td class=\"edge\" align=\"center\">";
	F_generic_button("preview",$l['w_preview'],"previewPage=window.open('../../public/code/cp_dpage.".CP_EXT."?aiocp_dp=".$pagedata_name."','previewPage','dependent,height=500,width=500,menubar=yes,resizable=yes,scrollbars=yes,status=yes,toolbar=no')"); 
	echo "</td></tr>";
}
?>

</table>

</form>
<!-- ====================================================== -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
//get next elements ID
function FJ_get_field_index(what) {
	for (var i=0;i<document.form_dbpageeditor.elements.length;i++) {
		if(what == document.form_dbpageeditor.elements[i]) {
			return(i+1);
		}
	}
	return (-1);
}
CKEDITOR.config.contentsCss = '<?php echo K_PATH_AIOCP; ?>public/styles/default.css';
<?php echo $ckeditor_data; ?>
//]]>
</script>
<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
