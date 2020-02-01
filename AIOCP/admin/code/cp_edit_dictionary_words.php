<?php
//============================================================+
// File name   : cp_edit_dictionary_words.php                  
// Begin       : 2003-10-14                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit Dictionary Words                         
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


$pagelevel = K_AUTH_ADMIN_CP_EDIT_DICTIONARY_WORDS;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_dictionary_words_editor'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages;

// Initialize variables
$userlevel = $_SESSION['session_user_level'];

require_once('../../shared/code/cp_functions_form.'.CP_EXT);

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
		<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_delete" id="form_delete">
		<input type="hidden" name="menu_mode" id="menu_mode" value="forcedelete" />
		<input type="hidden" name="dicword_id" id="dicword_id" value="<?php echo $dicword_id; ?>" />
		<input type="hidden" name="forcedelete" id="forcedelete" value="" />
		<?php 
		F_submit_button("form_delete","forcedelete",$l['w_delete']);
		F_submit_button("form_delete","forcedelete",$l['w_cancel']);
		?>
		</form>	
		<?php
		break;
	}

	case 'forcedelete':{
		F_stripslashes_formfields(); // Delete
		if($forcedelete == $l['w_delete']) { //check if delete button has been pushed
			$sql = "DELETE FROM ".K_TABLE_DICTIONARY_WORDS." WHERE dicword_id=".$dicword_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			$dicword_id=FALSE;
		}
		break;
		}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update news
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_DICTIONARY_WORDS, "dicword_name='".$dicword_name."'", "dicword_id", $dicword_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$sql = "UPDATE IGNORE ".K_TABLE_DICTIONARY_WORDS." SET 
				dicword_category_id='".$dicword_category_id."', 
				dicword_name='".$dicword_name."', 
				dicword_description='".$dicword_description."', 
				dicword_correlates='".$dicword_correlates."' 
				WHERE dicword_id=".$dicword_id."";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_add']):
	case $l['w_add']:{ // Add news
		if($formstatus = F_check_form_fields()) {
			//check if news is unique
			$sql = "SELECT dicword_id FROM ".K_TABLE_DICTIONARY_WORDS." WHERE (dicword_name='".$dicword_name."' AND dicword_category_id='".$dicword_category_id."')";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
			}
			else { //add item
				$sql = "INSERT IGNORE INTO ".K_TABLE_DICTIONARY_WORDS." (
				dicword_category_id, 
				dicword_name, 
				dicword_description, 
				dicword_correlates 
				) VALUES (
				'".$dicword_category_id."', 
				'".$dicword_name."', 
				'".$dicword_description."', 
				'".$dicword_correlates."' 
				)";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$dicword_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
	}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$dicword_name = "";
		$dicword_description = "";
		$dicword_correlates = "";
		break;
	}

	default :{ 
		break;
	}

} //end of switch


// Initialize variables
$clear_fields = false;

if(!isset($diccat_language)) {
	$diccat_language = $selected_language;
}

if((!isset($dicword_category_id) OR (!$dicword_category_id)) OR (isset($changelanguage) AND $changelanguage)) {
	$sql = "SELECT * FROM ".K_TABLE_DICTIONARY_CATEGORIES." WHERE (diccat_level<=".$userlevel." AND diccat_language='".$diccat_language."') ORDER BY diccat_name LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$dicword_category_id = $m['diccat_id'];
		}
		else {
			$dicword_category_id = false;
		}
	}
	else {
		F_display_db_error();
	}
	$dicword_id = false;
}

if($formstatus) {
	if($dicword_category_id) {
		if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
			if($changecategory OR (!$dicword_id)) {
				$sql = "SELECT * FROM ".K_TABLE_DICTIONARY_WORDS." WHERE dicword_category_id=".$dicword_category_id." ORDER BY dicword_name LIMIT 1";
			}
			else {$sql = "SELECT * FROM ".K_TABLE_DICTIONARY_WORDS." WHERE dicword_id=".$dicword_id." LIMIT 1";}
			if($r = F_aiocpdb_query($sql, $db)) {
				if($m = F_aiocpdb_fetch_array($r)) {
					$dicword_id = $m['dicword_id'];
					$dicword_category_id = $m['dicword_category_id'];
					$dicword_name = $m['dicword_name'];
					$dicword_description = $m['dicword_description'];
					$dicword_correlates = $m['dicword_correlates'];
				}
				else {
					$clear_fields = true;
				}
			}
			else {
				F_display_db_error();
			}
		}
	}
	else {
		$clear_fields = true;
	}
}

if ($clear_fields) {
	$dicword_name = "";
	$dicword_description = "";
	$dicword_correlates = "";
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_dicwordseditor" id="form_dicwordseditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="dicword_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT  language ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_language', 'h_diccat_language'); ?></b></td>
<td class="fillOE">
<input type="hidden" name="changelanguage" id="changelanguage" value="0" />
<select name="diccat_language" id="diccat_language" size="0" onchange="document.form_dicwordseditor.changelanguage.value=1; document.form_dicwordseditor.submit()">
<?php
	$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"".$m['language_code']."\"";
			if($m['language_code'] == $diccat_language) {
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
</td>
</tr>
<!-- END SELECT language ==================== -->


<!-- SELECT category ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_category', 'h_diccat_select'); ?></b></td>
<td class="fillEE">
<input type="hidden" name="changecategory" id="changecategory" value="0" />
<select name="dicword_category_id" id="dicword_category_id" size="0" onchange="document.form_dicwordseditor.changecategory.value=1; document.form_dicwordseditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_DICTIONARY_CATEGORIES." WHERE (diccat_level<=".$userlevel." AND diccat_language='".$diccat_language."') ORDER BY diccat_name";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['diccat_id']."\"";
		if($m['diccat_id'] == $dicword_category_id) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['diccat_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT category ==================== -->

<!-- SELECT news ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_word', 'h_dicwordsed_select'); ?></b></td>
<td class="fillOE">
<select name="dicword_id" id="dicword_id" size="0" onchange="document.form_dicwordseditor.submit()">
<?php
if($dicword_category_id) {
	$sql = "SELECT * FROM ".K_TABLE_DICTIONARY_WORDS." WHERE dicword_category_id=".$dicword_category_id." ORDER BY dicword_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"".$m['dicword_id']."\"";
			if($m['dicword_id'] == $dicword_id) {
				echo " selected=\"selected\"";
			}
			echo ">".htmlentities($m['dicword_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
		}
	}
	else {
		F_display_db_error();
	}
}
?>
</select>
</td>
</tr>
<!-- END SELECT news ==================== -->

<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE"><hr /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_name', 'h_dicwordsed_name'); ?></b></td>
<td class="fillOE"><input type="text" name="dicword_name" id="dicword_name" value="<?php echo htmlentities($dicword_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="50" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b><?php echo F_display_field_name('w_description', 'h_dicwordsed_description'); ?></b>
<br />
<?php 
$doc_charset = F_word_language($diccat_language, "a_meta_charset");
F_html_button("page", "form_dicwordseditor", "dicword_description", $doc_charset);

$current_ta_code = $dicword_description;
if(!$formstatus) {
	$current_ta_code = stripslashes($current_ta_code);
}
?>
</td>
<td class="fillEE">
<textarea cols="60" rows="10" name="dicword_description" id="dicword_description"><?php echo htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset); ?></textarea>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_correlates', 'h_dicwordsed_correlates'); ?></b></td>
<td class="fillOE"><input type="text" name="dicword_correlates" id="dicword_correlates" value="<?php echo $dicword_correlates; ?>" size="50" maxlength="255" /></td>
</tr>

<?php
if (isset($dicword_category_id) AND ($dicword_category_id > 0)) {
?>
<tr class="fillE">
<td class="fillEO" align="right">&nbsp;</td>
<td class="fillEE"><a href="cp_edit_dictionary_categories.<?php echo CP_EXT; ?>?diccat_id=<?php echo $dicword_category_id; ?>&amp;diccat_language=<?php echo urlencode($diccat_language); ?>"><b>&lt;&lt;&nbsp;<?php echo $l['t_dictionary_categories_editor']; ?></b></a></td>
</tr>
<?php
}
?>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if (isset($dicword_id) AND ($dicword_id > 0)) {
	F_submit_button("form_dicwordseditor","menu_mode",$l['w_update']); 
	F_submit_button("form_dicwordseditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_dicwordseditor","menu_mode",$l['w_add']); 
F_submit_button("form_dicwordseditor","menu_mode",$l['w_clear']); 
?>
</td>

</tr>
</table>
</form>
<!-- ====================================================== -->

<!-- Cange focus to dicword_id select -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_dicwordseditor.dicword_id.focus();
//]]>
</script>
<!-- END Cange focus to dicword_id select -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
