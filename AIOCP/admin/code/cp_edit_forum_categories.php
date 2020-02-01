<?php
//============================================================+
// File name   : cp_edit_forum_categories.php                  
// Begin       : 2001-12-13                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit forum categories                         
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_FORUM_CATEGORIES;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_forum_categories_editor'];

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
		<p><?php echo $l['t_warning'].": ".$l['d_forum_cat_delete']; ?></p>
		
		<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_delete" id="form_delete">
		<input type="hidden" name="menu_mode" id="menu_mode" value="forcedelete" />
		<input type="hidden" name="forumcat_id" id="forumcat_id" value="<?php echo $forumcat_id; ?>" />
		<input type="hidden" name="forumcat_order" id="forumcat_order" value="<?php echo $forumcat_order; ?>" />
		<input type="hidden" name="forcedelete" id="forcedelete" value="" />
		<?php 
		F_submit_button("form_delete","forcedelete",$l['w_delete']);
		F_submit_button("form_delete","forcedelete",$l['w_cancel']);
		?>
		</form>
		
		<?php
		break;
	}

	case "forcedelete":{ // Delete category and all associated messages and users
		if($forcedelete == $l['w_delete']) { //check if delete button has been pushed (redundant check)
			$sql = "DELETE FROM ".K_TABLE_FORUM_CATEGORIES." WHERE forumcat_id=".$forumcat_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			$sql = "DELETE FROM ".K_TABLE_FORUM_FORUMS." WHERE forum_categoryid=".$forumcat_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			$sql = "DELETE FROM ".K_TABLE_FORUM_TOPICS." WHERE forumtopic_categoryid=".$forumcat_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			$sql = "DELETE FROM ".K_TABLE_FORUM_POSTS." WHERE forumposts_categoryid=".$forumcat_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			//delete moderators
			$sql = "DELETE FROM ".K_TABLE_FORUM_MODERATORS." WHERE moderator_categoryid=".$forumcat_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			//reorder positions here...
			$sql = "UPDATE IGNORE ".K_TABLE_FORUM_CATEGORIES." SET forumcat_order=forumcat_order-1 WHERE forumcat_order>".$forumcat_order."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
		}
		$forumcat_id=FALSE;
		break;
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_FORUM_CATEGORIES, "forumcat_name='".$forumcat_name."'", "forumcat_id", $forumcat_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$sql = "UPDATE IGNORE ".K_TABLE_FORUM_CATEGORIES." SET forumcat_language='".$forumcat_language."', forumcat_readinglevel='".$forumcat_readinglevel."', forumcat_postinglevel='".$forumcat_postinglevel."', forumcat_name='".$forumcat_name."', forumcat_description='".$forumcat_description."', forumcat_order='".$forumcat_order."' WHERE forumcat_id=".$forumcat_id."";
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
			//check if forumcat_name is unique
			$sql = "SELECT forumcat_name FROM ".K_TABLE_FORUM_CATEGORIES." WHERE forumcat_name='".$forumcat_name."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				$forumcat_order = F_count_rows(K_TABLE_FORUM_CATEGORIES)+1;
				$sql = "INSERT IGNORE INTO ".K_TABLE_FORUM_CATEGORIES." (
				forumcat_language, 
				forumcat_readinglevel, 
				forumcat_postinglevel, 
				forumcat_name, 
				forumcat_description, 
				forumcat_order
				) VALUES (
				'".$forumcat_language."', 
				'".$forumcat_readinglevel."', 
				'".$forumcat_postinglevel."', 
				'".$forumcat_name."', 
				'".$forumcat_description."', 
				'".$forumcat_order."'
				)";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$forumcat_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
		}

	case '-':{ // change order: move up
		if($forumcat_order>1) {
			$sql = "UPDATE IGNORE ".K_TABLE_FORUM_CATEGORIES." SET forumcat_order=(forumcat_order+1) WHERE forumcat_order=(".$forumcat_order."-1)";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			$sql = "UPDATE IGNORE ".K_TABLE_FORUM_CATEGORIES." SET forumcat_order=(forumcat_order-1) WHERE forumcat_id=".$forumcat_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
		}
		break;
		}

	case '+':{ // change order: move down
		if($forumcat_order<F_count_rows(K_TABLE_FORUM_CATEGORIES)) {
			$sql = "UPDATE IGNORE ".K_TABLE_FORUM_CATEGORIES." SET forumcat_order=(forumcat_order-1) WHERE forumcat_order=(".$forumcat_order."+1)";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			$sql = "UPDATE IGNORE ".K_TABLE_FORUM_CATEGORIES." SET forumcat_order=(forumcat_order+1) WHERE forumcat_id=".$forumcat_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
		}
		break;
		}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$forumcat_name = "";
		$forumcat_description = "";
		$forumcat_readinglevel = 0;
		$forumcat_postinglevel = 0;
		break;
		}

	default :{ 
		break;
		}

} //end of switch

// Initialize variables
if(!isset($forumcat_language)) {
	$forumcat_language = $selected_language;
}

if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if((!isset($forumcat_id) OR (!$forumcat_id)) OR (isset($changelanguage) AND $changelanguage)) {
			$sql = "SELECT * FROM ".K_TABLE_FORUM_CATEGORIES." WHERE forumcat_language='".$forumcat_language."' ORDER BY forumcat_order LIMIT 1";
		}
		else {
			$sql = "SELECT * FROM ".K_TABLE_FORUM_CATEGORIES." WHERE forumcat_id=".$forumcat_id." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$forumcat_id = $m['forumcat_id'];
				$forumcat_language = $m['forumcat_language'];
				$forumcat_readinglevel = $m['forumcat_readinglevel'];
				$forumcat_postinglevel = $m['forumcat_postinglevel'];
				$forumcat_name = $m['forumcat_name'];
				$forumcat_description = $m['forumcat_description'];
				$forumcat_order = $m['forumcat_order'];
			}
			else {
				$forumcat_name = "";
				$forumcat_description = "";
				$forumcat_readinglevel = 0;
				$forumcat_postinglevel = 0;
			}
		}
		else {
			F_display_db_error();
		}
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_forumcateditor" id="form_forumcateditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="forumcat_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT  language ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_language', 'h_forumcat_language'); ?></b></td>
<td class="fillOE">
<input type="hidden" name="changelanguage" id="changelanguage" value="0" />
<select name="forumcat_language" id="forumcat_language" size="0" onchange="document.form_forumcateditor.changelanguage.value=1; document.form_forumcateditor.submit()">
<?php
	$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"".$m['language_code']."\"";
			if($m['language_code'] == $forumcat_language) {
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
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_category', 'h_forumcat_select'); ?></b></td>
<td class="fillEE">
<select name="forumcat_id" id="forumcat_id" size="0" onchange="document.form_forumcateditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_FORUM_CATEGORIES." WHERE forumcat_language='".$forumcat_language."' ORDER BY forumcat_order";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['forumcat_id']."\"";
		if($m['forumcat_id'] == $forumcat_id) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['forumcat_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
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

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_name', 'h_forumcat_name'); ?></b></td>
<td class="fillEE"><input type="text" name="forumcat_name" id="forumcat_name" value="<?php echo htmlentities($forumcat_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="50" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b><?php echo F_display_field_name('w_description', 'h_forumcat_description'); ?></b>
<br />
<?php 
$doc_charset = F_word_language($forumcat_language, "a_meta_charset");
F_html_button("forum", "form_forumcateditor", "forumcat_description", $doc_charset);

$current_ta_code = $forumcat_description;
if(!$formstatus) {
	$current_ta_code = stripslashes($current_ta_code);
}
?>
</td>
<td class="fillOE"><textarea cols="50" rows="5" name="forumcat_description" id="forumcat_description"><?php echo htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset); ?></textarea></td>
</tr>

<!-- SELECT LEVEL ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_reading', 'h_forumcat_reading'); ?></b></td>
<td class="fillEE"><select name="forumcat_readinglevel" id="forumcat_readinglevel" size="0">
<?php
$sql = "SELECT * FROM ".K_TABLE_LEVELS." ORDER BY level_code ";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['level_code']."\"";
		if($m['level_code'] == $forumcat_readinglevel) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['level_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select> <b><?php echo $l['w_level']; ?></b>
</td>
</tr>
<!-- END SELECT LEVEL ==================== -->

<!-- SELECT LEVEL ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_posting', 'h_forumcat_posting'); ?></b></td>
<td class="fillOE"><select name="forumcat_postinglevel" id="forumcat_postinglevel" size="0">
<?php
$sql = "SELECT * FROM ".K_TABLE_LEVELS." ORDER BY level_code ";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['level_code']."\"";
		if($m['level_code'] == $forumcat_postinglevel) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['level_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select> <b><?php echo $l['w_level']; ?></b>
</td>
</tr>
<!-- END SELECT LEVEL ==================== -->

<tr class="fillE" valign="middle">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_position', 'h_forumcat_position'); ?></b></td>
<td class="fillEE">
<?php 
if (!isset($forumcat_order) OR (!$forumcat_order)) {
	$forumcat_order = "";
}
F_submit_button("form_forumcateditor","menu_mode","-");?> <b><?php echo $forumcat_order; ?></b> <?php F_submit_button("form_forumcateditor","menu_mode","+");?>
</td>
</tr>

<?php 
if (isset($forumcat_id) AND $forumcat_id) {
?>
<tr class="fillO">
<td class="fillOO" align="right">&nbsp;</td>
<td class="fillOE"><a href="cp_edit_forums.<?php echo CP_EXT; ?>?forum_categoryid=<?php echo $forumcat_id; ?>&amp;forumcat_language=<?php echo urlencode($forumcat_language); ?>"><b><?php echo $l['t_forums_editor']; ?>&nbsp;&gt;&gt;</b></a></td>
</tr>
<?php 
}
?>
</table>

</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="forumcat_order" id="forumcat_order" value="<?php echo $forumcat_order; ?>" />
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if (isset($forumcat_id) AND $forumcat_id) {
	F_submit_button("form_forumcateditor","menu_mode",$l['w_update']); 
	F_submit_button("form_forumcateditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_forumcateditor","menu_mode",$l['w_add']); 
F_submit_button("form_forumcateditor","menu_mode",$l['w_clear']); 
?>
</td>
</tr>

</table>
</form>
<!-- ====================================================== -->

<!-- Cange focus to forumcat_id select -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_forumcateditor.forumcat_id.focus();
//]]>
</script>
<!-- END Cange focus to forumcat_id select -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
