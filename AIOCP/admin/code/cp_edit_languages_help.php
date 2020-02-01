<?php
//============================================================+
// File name   : cp_edit_languages_help.php                    
// Begin       : 2002-03-21                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit help templates in all enabled languages  
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_LANGUAGES_HELP;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_help_templates_editor'];

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
		F_print_error("WARNING", $l['m_delete_confirm']);
		?>		
		<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_delete" id="form_delete">
		<input type="hidden" name="menu_mode" id="menu_mode" value="forcedelete" />
		<input type="hidden" name="help_id" id="help_id" value="<?php echo $help_id; ?>" />
		<input type="hidden" name="forcedelete" id="forcedelete" value="" />
		<?php 
		F_submit_button("form_delete","forcedelete",$l['w_delete']);
		F_submit_button("form_delete","forcedelete",$l['w_cancel']);
		?>
		</form>
		<?php
		break;
	}

	case "forcedelete":{ 
		if($forcedelete == $l['w_delete']) { //check if delete button has been pushed (redundant check)
			$sql = "DELETE FROM ".K_TABLE_LANGUAGE_HELP." WHERE help_id='".$help_id."'";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			$help_id=FALSE;
		}
		break;
	}
	
	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update word
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_LANGUAGE_HELP, "help_id='".$newhelp_id."'", "help_id", $help_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE;
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
				$sql = "UPDATE IGNORE ".K_TABLE_LANGUAGE_HELP." SET ".$SETstring." WHERE help_id='".$help_id."'";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				if ($help_id != $newhelp_id) { //change template ID
					$sql = "UPDATE IGNORE ".K_TABLE_LANGUAGE_HELP." SET help_id='".$newhelp_id."' WHERE help_id='".$help_id."'";
					if(!$r = F_aiocpdb_query($sql, $db)) {
						F_display_db_error();
					}
					$help_id = $newhelp_id;
				}
			}
		}
		break;
	}

	case unhtmlentities($l['w_add']):
	case $l['w_add']:{ // Add word
		if($formstatus = F_check_form_fields()) {
			//check if help_id is unique
			$sql = "SELECT help_id FROM ".K_TABLE_LANGUAGE_HELP." WHERE help_id='".$newhelp_id."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE;
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
				$sql = "INSERT IGNORE INTO ".K_TABLE_LANGUAGE_HELP." (help_id ".$FIELDSstring.") VALUES ('".$newhelp_id."' ".$VALUESstring.")";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
			}
			$help_id = $newhelp_id;
		}
		break;
		}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$newhelp_id = "";
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
if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!isset($help_id) OR (!$help_id)) {
			$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_HELP." ORDER BY help_id LIMIT 1";
		} else {
			$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_HELP." WHERE help_id='".$help_id."' LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$help_id = $m['help_id'];
				$newhelp_id = $help_id;
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
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_languagehelpeditor" id="form_languagehelpeditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="newhelp_id" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT WORD ID ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_template', 'h_template_select'); ?></b></td>
<td class="fillOE">
<select name="help_id" id="help_id" size="0" onchange="document.form_languagehelpeditor.submit()">
<?php
$sql = "SELECT help_id FROM ".K_TABLE_LANGUAGE_HELP." ORDER BY help_id";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['help_id']."\"";
		if($m['help_id'] == $help_id) {
			echo " selected=\"selected\"";
		}
		echo ">".$m['help_id']."</option>\n";
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
<td class="fillOE"><input type="text" name="newhelp_id" id="newhelp_id" value="<?php echo $newhelp_id; ?>" size="50" /></td>
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
<input type="button" name="htmleditor_<?php echo $m['language_code']; ?>" id="htmleditor_<?php echo $m['language_code']; ?>" value="<?php echo $l['w_button_html_editor']; ?>" onclick="htmlWindow=window.open('cp_edit_html.<?php echo CP_EXT; ?>?templates=0&amp;callingform=form_languagehelpeditor&amp;callingfield=elements['+FJ_get_field_index(this)+']<?php echo $doc_charset_url; ?>','htmlWindow','dependent,height=500,width=800,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')" />
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
if ($help_id) {
	F_submit_button("form_languagehelpeditor","menu_mode",$l['w_update']); 
	F_submit_button("form_languagehelpeditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_languagehelpeditor","menu_mode",$l['w_add']); 
F_submit_button("form_languagehelpeditor","menu_mode",$l['w_clear']); 
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
	for (var i=0;i<document.form_languagehelpeditor.elements.length;i++) {
		if(what == document.form_languagehelpeditor.elements[i]) {
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
