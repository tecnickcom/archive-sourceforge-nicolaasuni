<?php
//============================================================+
// File name   : cp_edit_dictionary_categories.php             
// Begin       : 2003-10-14                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit categories for dictionaries              
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_DICTIONARY_CATEGORIES;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_dictionary_categories_editor'];

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
		<p><?php echo $l['t_warning'].": ".$l['d_dictionary_cat_delete']; ?></p>
		
		<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_delete" id="form_delete">
		<input type="hidden" name="menu_mode" id="menu_mode" value="forcedelete" />
		<input type="hidden" name="diccat_id" id="diccat_id" value="<?php echo $diccat_id; ?>" />
		<input type="hidden" name="forcedelete" id="forcedelete" value="" />
		<?php 
		F_submit_button("form_delete","forcedelete",$l['w_delete']);
		F_submit_button("form_delete","forcedelete",$l['w_cancel']);
		?>
		</form>
		
		<?php
		break;
	}

	case "forcedelete": { // Delete newscat
		if($forcedelete == $l['w_delete']) { //check if delete button has been pushed (redundant check)
			$sql = "DELETE FROM ".K_TABLE_DICTIONARY_CATEGORIES." WHERE diccat_id=".$diccat_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			$sql = "DELETE FROM ".K_TABLE_DICTIONARY_WORDS." WHERE dicword_category_id=".$diccat_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			$diccat_id=FALSE;
		}
		break;
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update newscat
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_DICTIONARY_CATEGORIES, "diccat_name='".$diccat_name."'", "diccat_id", $diccat_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$sql = "UPDATE IGNORE ".K_TABLE_DICTIONARY_CATEGORIES." SET 
				diccat_language='".$diccat_language."', 
				diccat_level='".$diccat_level."', 
				diccat_name='".$diccat_name."', 
				diccat_description='".$diccat_description."', 
				diccat_forum_id='".$diccat_forum_id."' 
				WHERE diccat_id=".$diccat_id."";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_add']):
	case $l['w_add']:{ // Add newscat
		if($formstatus = F_check_form_fields()) {
			//check if diccat_name is unique
			$sql = "SELECT diccat_name FROM ".K_TABLE_DICTIONARY_CATEGORIES." WHERE diccat_name='".$diccat_name."' AND diccat_language='".$diccat_language."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				$sql = "INSERT IGNORE INTO ".K_TABLE_DICTIONARY_CATEGORIES." (
				diccat_language, 
				diccat_level, 
				diccat_name, 
				diccat_description,
				diccat_forum_id
				) VALUES (
				'".$diccat_language."', 
				'".$diccat_level."', 
				'".$diccat_name."', 
				'".$diccat_description."',
				'".$diccat_forum_id."')";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$diccat_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$diccat_name = "";
		$diccat_description = "";
		$diccat_level = 0;
		$diccat_forum_id = 0;
		break;
		}

	default :{ 
		break;
		}

} //end of switch

// Initialize variables
if(!isset($diccat_language)) {
	$diccat_language = $selected_language;
}

if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if ((!isset($diccat_id) OR (!$diccat_id)) OR (isset($changelanguage) AND $changelanguage)) {
			$sql = "SELECT * FROM ".K_TABLE_DICTIONARY_CATEGORIES." WHERE diccat_language='".$diccat_language."' ORDER BY diccat_name LIMIT 1";
		}
		else {
			$sql = "SELECT * FROM ".K_TABLE_DICTIONARY_CATEGORIES." WHERE diccat_id=".$diccat_id." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$diccat_id = $m['diccat_id'];
				$diccat_language = $m['diccat_language'];
				$diccat_level = $m['diccat_level'];
				$diccat_name = $m['diccat_name'];
				$diccat_description = $m['diccat_description'];
				$diccat_forum_id = $m['diccat_forum_id'];
			}
			else {
				$diccat_name = "";
				$diccat_description = "";
				$diccat_level = 0;
				$diccat_forum_id = 0;
			}
		}
		else {
			F_display_db_error();
		}
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_diccateditor" id="form_diccateditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="diccat_name" />
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
<select name="diccat_language" id="diccat_language" size="0" onchange="document.form_diccateditor.changelanguage.value=1; document.form_diccateditor.submit()">
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

<!-- SELECT newscat ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_category', 'h_diccat_select'); ?></b></td>
<td class="fillEE">
<select name="diccat_id" id="diccat_id" size="0" onchange="document.form_diccateditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_DICTIONARY_CATEGORIES." WHERE diccat_language='".$diccat_language."' ORDER BY diccat_name";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['diccat_id']."\"";
		if($m['diccat_id'] == $diccat_id) {
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
<!-- END SELECT newscat ==================== -->

<tr class="fillO">
<td class="fillOO">&nbsp;</td>
<td class="fillOE"><hr /></td>
</tr>

<!-- SELECT LEVEL ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_level', 'h_diccat_level'); ?></b></td>
<td class="fillEE"><select name="diccat_level" id="diccat_level" size="0">
<?php
$sql = "SELECT * FROM ".K_TABLE_LEVELS." ORDER BY level_code ";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['level_code']."\"";
		if($m['level_code'] == $diccat_level) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['level_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT LEVEL ==================== -->

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_name', 'h_diccat_name'); ?></b></td>
<td class="fillOE"><input type="text" name="diccat_name" id="diccat_name" value="<?php echo htmlentities($diccat_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="50" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b><?php echo F_display_field_name('w_description', 'h_diccat_description'); ?></b>
<br />
<?php 
$doc_charset = F_word_language($diccat_language, "a_meta_charset");
F_html_button("page", "form_diccateditor", "diccat_description", $doc_charset);

$current_ta_code = $diccat_description;
if(!$formstatus) {
	$current_ta_code = stripslashes($current_ta_code);
}
?>
</td>
<td class="fillEE"><textarea cols="60" rows="10" name="diccat_description" id="diccat_description"><?php echo htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset); ?></textarea></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_forum', 'h_forumed_select'); ?></b></td>
<td class="fillOE">
<select name="diccat_forum_id" id="diccat_forum_id" size="0">
<option value="0">&nbsp;</option>
<?php
$sql = "SELECT forumcat_name,forum_name,forum_id FROM ".K_TABLE_FORUM_CATEGORIES.", ".K_TABLE_FORUM_FORUMS." WHERE forumcat_id=forum_categoryid AND forum_language='".$diccat_language."' ORDER BY forum_categoryid, forum_order";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['forum_id']."\"";
		if($m['forum_id'] == $diccat_forum_id) {
			echo " selected=\"selected\"";
		}
		echo ">".$m['forumcat_name']." :: ".$m['forum_name']."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>

<?php
if (isset($diccat_id) AND $diccat_id) {
?>
<tr class="fillE">
<td class="fillEO" align="right">&nbsp;</td>
<td class="fillEE"><a href="cp_edit_dictionary_words.<?php echo CP_EXT; ?>?dicword_category_id=<?php echo $diccat_id; ?>&amp;diccat_language=<?php echo urlencode($diccat_language); ?>"><b><?php echo $l['t_dictionary_words_editor']; ?>&nbsp;&gt;&gt;</b></a></td>
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
if (isset($diccat_id) AND $diccat_id) {
	F_submit_button("form_diccateditor","menu_mode",$l['w_update']); 
	F_submit_button("form_diccateditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_diccateditor","menu_mode",$l['w_add']); 
F_submit_button("form_diccateditor","menu_mode",$l['w_clear']); 
?>
</td>

</tr>
</table>
</form>
<!-- ====================================================== -->

<!-- Cange focus to diccat_id select -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_diccateditor.diccat_id.focus();
//]]>
</script>
<!-- END Cange focus to diccat_id select -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
