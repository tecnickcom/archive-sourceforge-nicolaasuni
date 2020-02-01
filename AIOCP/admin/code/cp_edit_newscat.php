<?php
//============================================================+
// File name   : cp_edit_newscat.php                           
// Begin       : 2001-09-19                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit categories for news                      
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_NEWSCAT;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_news_categories_editor'];

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
		F_stripslashes_formfields(); // ask confirmation
		F_print_error("WARNING", $l['m_delete_confirm']);
		?>
		<p><?php echo $l['t_warning'].": ".$l['d_news_cat_delete']; ?></p>
		
		<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_delete" id="form_delete">
		<input type="hidden" name="menu_mode" id="menu_mode" value="forcedelete" />
		<input type="hidden" name="newscat_id" id="newscat_id" value="<?php echo $newscat_id; ?>" />
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
			$sql = "DELETE FROM ".K_TABLE_NEWS_CATEGORIES." WHERE newscat_id=".$newscat_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			$sql = "DELETE FROM ".K_TABLE_NEWS." WHERE news_category=".$newscat_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			$newscat_id=FALSE;
		}
		break;
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update newscat
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_NEWS_CATEGORIES, "newscat_name='".$newscat_name."'", "newscat_id", $newscat_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$sql = "UPDATE IGNORE ".K_TABLE_NEWS_CATEGORIES." SET 
				newscat_language='".$newscat_language."', 
				newscat_level='".$newscat_level."', 
				newscat_name='".$newscat_name."', 
				newscat_description='".$newscat_description."' 
				WHERE newscat_id=".$newscat_id."";
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
			//check if newscat_name is unique
			$sql = "SELECT newscat_name FROM ".K_TABLE_NEWS_CATEGORIES." WHERE newscat_name='".$newscat_name."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				$sql = "INSERT IGNORE INTO ".K_TABLE_NEWS_CATEGORIES." (
				newscat_language, 
				newscat_level, 
				newscat_name, 
				newscat_description
				) VALUES (
				'".$newscat_language."', 
				'".$newscat_level."', 
				'".$newscat_name."', 
				'".$newscat_description."')";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$newscat_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$newscat_name = "";
		$newscat_description = "";
		$newscat_level = 0;
		break;
		}

	default :{ 
		break;
		}

} //end of switch

// Initialize variables
if(!isset($newscat_language)) {
	$newscat_language = $selected_language;
}

if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if((!isset($newscat_id) OR (!$newscat_id)) OR (isset($changelanguage) AND $changelanguage)) {
			$sql = "SELECT * FROM ".K_TABLE_NEWS_CATEGORIES." WHERE newscat_language='".$newscat_language."' ORDER BY newscat_name LIMIT 1";
		}
		else {
			$sql = "SELECT * FROM ".K_TABLE_NEWS_CATEGORIES." WHERE newscat_id=".$newscat_id." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$newscat_id = $m['newscat_id'];
				$newscat_language = $m['newscat_language'];
				$newscat_level = $m['newscat_level'];
				$newscat_name = $m['newscat_name'];
				$newscat_description = $m['newscat_description'];
			}
			else {
				$newscat_name = "";
				$newscat_description = "";
				$newscat_level = 0;
			}
		}
		else {
			F_display_db_error();
		}
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_newscateditor" id="form_newscateditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="newscat_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT  language ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_language', 'h_newscat_language'); ?></b></td>
<td class="fillOE">
<input type="hidden" name="changelanguage" id="changelanguage" value="0" />
<select name="newscat_language" id="newscat_language" size="0" onchange="document.form_newscateditor.changelanguage.value=1; document.form_newscateditor.submit()">
<?php
	$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"".$m['language_code']."\"";
			if($m['language_code'] == $newscat_language) {
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
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_category', 'h_newscat_select'); ?></b></td>
<td class="fillEE">
<select name="newscat_id" id="newscat_id" size="0" onchange="document.form_newscateditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_NEWS_CATEGORIES." WHERE newscat_language='".$newscat_language."' ORDER BY newscat_name";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['newscat_id']."\"";
		if($m['newscat_id'] == $newscat_id) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['newscat_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
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
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_level', 'h_newscat_level'); ?></b></td>
<td class="fillEE"><select name="newscat_level" id="newscat_level" size="0">
<?php
$sql = "SELECT * FROM ".K_TABLE_LEVELS." ORDER BY level_code ";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['level_code']."\"";
		if($m['level_code'] == $newscat_level) {
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
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_name', 'h_newscat_name'); ?></b></td>
<td class="fillOE"><input type="text" name="newscat_name" id="newscat_name" value="<?php echo htmlentities($newscat_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="50" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b><?php echo F_display_field_name('w_description', 'h_newscat_description'); ?></b>
<br />
<?php 
$doc_charset = F_word_language($newscat_language, "a_meta_charset");
F_html_button("page", "form_newscateditor", "newscat_description", $doc_charset);

$current_ta_code = $newscat_description;
if(!$formstatus) {
	$current_ta_code = stripslashes($current_ta_code);
}
?>
</td>
<td class="fillEE"><textarea cols="60" rows="10" name="newscat_description" id="newscat_description"><?php echo htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset); ?></textarea></td>
</tr>

<?php
if (isset($newscat_id) AND $newscat_id) {
?>
<tr class="fillO">
<td class="fillOO" align="right">&nbsp;</td>
<td class="fillOE"><a href="cp_edit_news.<?php echo CP_EXT; ?>?news_category=<?php echo $newscat_id; ?>&amp;newscat_language=<?php echo urlencode($newscat_language); ?>"><b><?php echo $l['t_news_editor']; ?>&nbsp;&gt;&gt;</b></a></td>
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
if (isset($newscat_id) AND $newscat_id) {
	F_submit_button("form_newscateditor","menu_mode",$l['w_update']); 
	F_submit_button("form_newscateditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_newscateditor","menu_mode",$l['w_add']); 
F_submit_button("form_newscateditor","menu_mode",$l['w_clear']); 
?>
</td>

</tr>
</table>
</form>
<!-- ====================================================== -->

<!-- Cange focus to newscat_id select -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_newscateditor.newscat_id.focus();
//]]>
</script>
<!-- END Cange focus to newscat_id select -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
