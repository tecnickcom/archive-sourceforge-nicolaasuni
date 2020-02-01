<?php
//============================================================+
// File name   : cp_edit_language_pages.php                    
// Begin       : 2001-04-05                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit page templates in multilanguage          
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_LANGUAGE_PAGES;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_page_templates_editor'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages; ?>
<!-- ====================================================== -->
<?php
if (isset($_REQUEST['menu_mode_page'])) {
	$menu_mode_page = $_REQUEST['menu_mode_page'];
} else {
	$menu_mode_page = "";
}
if ($menu_mode_page == $l['w_delete']) { // delete all templates for selected page
	$sql = "DELETE FROM ".K_TABLE_LANGUAGE_PAGES." WHERE page='".$page."'";
	if(!$r = F_aiocpdb_query($sql, $db)) {
		F_display_db_error();
	}
	$page=FALSE;
	$template_id=FALSE;
	$menu_mode = $l['w_clear'];
}
if (isset($_REQUEST['menu_mode'])) {
	$menu_mode = $_REQUEST['menu_mode'];
} else {
	$menu_mode = "";
}
switch($menu_mode) {

	case unhtmlentities($l['w_delete']):
	case $l['w_delete']:{
		F_stripslashes_formfields(); // Delete word
		$sql = "DELETE FROM ".K_TABLE_LANGUAGE_PAGES." WHERE template_id='".$template_id."'";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		$page=FALSE;
		$template_id=FALSE;
		break;
		}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update word
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_LANGUAGE_PAGES, "template_id='".$template_id."' AND page='".$page."'", "template", $template)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$SETstring = "";
				$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
				if($r = F_aiocpdb_query($sql, $db)) {
					while($m = F_aiocpdb_fetch_array($r)) {
						$SETstring .= ", ".$m['language_code']."='".$word[$m['language_code']]."'"; //list of fields to change (all language columns)
					}
				}
				else {
					F_display_db_error();
				}
				$SETstring = substr($SETstring,1);
				$sql = "UPDATE IGNORE ".K_TABLE_LANGUAGE_PAGES." SET ".$SETstring." WHERE template_id='".$template_id."'";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
			}
		}
		break;
	}

	case unhtmlentities($l['w_add']):
	case $l['w_add']:{ // Add word
		if($formstatus = F_check_form_fields()) {
			//check if template_id is unique
			$sql = "SELECT template_id FROM ".K_TABLE_LANGUAGE_PAGES." WHERE template='".$template."' AND page='".$page."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add words
				$FIELDSstring = "";
				$VALUESstring = "";
				$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
				if($r = F_aiocpdb_query($sql, $db)) {
					while($m = F_aiocpdb_fetch_array($r)) {
						$FIELDSstring .= ", ".$m['language_code']; //list of fields to change (all language columns)
						$VALUESstring .= ", '".$word[$m['language_code']]."'"; //list of values
					}
				}
				else {
					F_display_db_error();
				}
				$sql = "INSERT IGNORE INTO ".K_TABLE_LANGUAGE_PAGES." (page, template ".$FIELDSstring.") VALUES ('".$page."','".$template."' ".$VALUESstring.")";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
			}
			$template_id = F_aiocpdb_insert_id();
		}
		break;
		}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$template = "";
		$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
		if($r = F_aiocpdb_query($sql, $db)) {
			while($m = F_aiocpdb_fetch_array($r)) {
				$word[$m['language_code']] = "";
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

} //end of switch


// Initialize variables

if (!isset($page) OR (!$page)) {
	$sql = "SELECT page FROM ".K_TABLE_LANGUAGE_PAGES." GROUP BY page ORDER BY page";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$page = $m['page'];
		}
	}
	else {
		F_display_db_error();
	}
}

if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if((isset($changepage) AND $changepage) OR (!isset($template_id) OR (!$template_id))) {
			$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_PAGES." WHERE page='".$page."' ORDER BY template LIMIT 1";
		} else {
			$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_PAGES." WHERE template_id='".$template_id."' LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$template_id = $m['template_id'];
				$page = $m['page'];
				$template = $m['template'];
				$sqll = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
				if($rl = F_aiocpdb_query($sqll, $db)) {
					while($ml = F_aiocpdb_fetch_array($rl)) {
						$word[$ml['language_code']] = $m[$ml['language_code']];
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

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_pagetemplateeditor" id="form_pagetemplateeditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="template" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT WORD ID ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_page', 'h_page_select'); ?></b></td>
<td class="fillEE">
<input type="hidden" name="changepage" id="changepage" value="0" />
<input type="hidden" name="menu_mode_page" id="menu_mode_page" value="" />
<select name="page" id="page" size="0" onchange="document.form_pagetemplateeditor.changepage.value=1; document.form_pagetemplateeditor.submit()">
<?php
$sql = "SELECT page FROM ".K_TABLE_LANGUAGE_PAGES." GROUP BY page ORDER BY page";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['page']."\"";
		if($m['page'] == $page) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['page'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select> 
<?php F_submit_button("form_pagetemplateeditor","menu_mode_page",$l['w_delete']); ?>
</td>
</tr>
<!-- END SELECT WORD ID ==================== -->

<!-- SELECT WORD ID ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_template', 'h_template_select'); ?></b></td>
<td class="fillOE">
<select name="template_id" id="template_id" size="0" onchange="document.form_pagetemplateeditor.submit()">
<?php
$sql = "SELECT template_id,template FROM ".K_TABLE_LANGUAGE_PAGES." WHERE page='".$page."' ORDER BY template";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['template_id']."\"";
		if($m['template_id'] == $template_id) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['template'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT WORD ID ==================== -->

<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE"><hr /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_name', 'h_template_name'); ?></b></td>
<td class="fillOE"><input type="text" name="template" id="template" value="<?php echo htmlentities($template, ENT_COMPAT, $l['a_meta_charset']); ?>" size="50" /></td>
</tr>

<!-- language words ==================== -->
<?php
	$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<tr class=\"fillE\">";
			echo "<td class=\"fillEO\" align=\"right\" valign=\"top\"><b>".$m['language_name']."</b><br />";
			
			$doc_charset = F_word_language($m['language_code'], "a_meta_charset");
			if ($doc_charset) {
				$doc_charset_url = "&amp;charset=".$doc_charset;
			}
			else {
				$doc_charset_url = "";
			}
			?>
<input type="button" name="htmleditor_<?php echo $m['language_code']; ?>" id="htmleditor_<?php echo $m['language_code']; ?>" value="<?php echo $l['w_button_html_editor']; ?>" onclick="htmlWindow=window.open('cp_edit_html.<?php echo CP_EXT; ?>?templates=0&amp;callingform=form_pagetemplateeditor&amp;callingfield=elements['+FJ_get_field_index(this)+']<?php echo $doc_charset_url; ?>','htmlWindow','dependent,height=500,width=800,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')" />
			<?php
			echo "</td>";
			$current_ta_code = $word[$m['language_code']];
			if(!$formstatus) {
				$current_ta_code = stripslashes($current_ta_code);
			}
			echo "<td class=\"fillEE\"><textarea cols=\"50\" rows=\"3\" name=\"word[".$m['language_code']."]\" id=\"word_".$m['language_code']."\">".htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset)."</textarea></td>";
			echo "</tr>";
		}
	}
	else {
		F_display_db_error();
	}
?>
<!-- END  language words ==================== -->

</table>

</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if ($page) {
	F_submit_button("form_pagetemplateeditor","menu_mode",$l['w_update']); 
	F_submit_button("form_pagetemplateeditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_pagetemplateeditor","menu_mode",$l['w_add']); 
F_submit_button("form_pagetemplateeditor","menu_mode",$l['w_clear']); 
?>
</td>
</tr>
</table>
</form>
<!-- ====================================================== -->

<script language="JavaScript" type="text/javascript">
//<![CDATA[
//get next elements ID
function FJ_get_field_index(what) {
	for (var i=0;i<document.form_pagetemplateeditor.elements.length;i++) {
		if(what == document.form_pagetemplateeditor.elements[i]) {
			return(i+1);
		}
	}
	return (-1);
}
//]]>
</script>
<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
