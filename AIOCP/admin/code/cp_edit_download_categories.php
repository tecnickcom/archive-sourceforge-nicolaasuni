<?php
//============================================================+
// File name   : cp_edit_download_categories.php               
// Begin       : 2001-11-17                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit categories for downloads                 
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_DOWNLOAD_CATEGORIES;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

require_once('../../shared/code/cp_functions_form.'.CP_EXT);
require_once('../code/cp_functions_tree.'.CP_EXT);
require_once('../../shared/code/cp_functions_tree.'.CP_EXT);

$thispage_title = $l['t_download_categories_editor'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages;


//initialize variables
$tree_table = K_TABLE_DOWNLOADS_CATEGORIES;
$tree_suffix = "downcat";

//if the tree is void (no items), create new item (first element)
if(!F_count_rows($tree_table)) {
	$downcat_id = 1;
	$downcat_item = 1;
	$downcat_sub_id = 0;
	$downcat_position = 1;
	$downcat_level = 0;
	$dc_name = array();
	$dc_description = array();
	$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			$dc_name[$m['language_code']] = "default";
			$dc_description[$m['language_code']] = "default";
		}
	}
	else {
		F_display_db_error();
	}
	$downcat_name = addslashes(serialize($dc_name));
	$downcat_description = addslashes(serialize($dc_description));
	
	$sql = "INSERT IGNORE INTO ".K_TABLE_DOWNLOADS_CATEGORIES." (
		downcat_id,
		downcat_item, 
		downcat_sub_id, 
		downcat_position, 
		downcat_level,
		downcat_name,
		downcat_description
		) VALUES (
		'".$downcat_id."', 
		'".$downcat_item."', 
		'".$downcat_sub_id."', 
		'".$downcat_position."', 
		'".$downcat_level."', 
		'".$downcat_name."', 
		'".$downcat_description."'
		)";
	if(!$r = F_aiocpdb_query($sql, $db)) {
		F_display_db_error();
	}
	else {
		$prodcat_id = F_aiocpdb_insert_id();
	}
}


if (isset($_REQUEST['menu_mode'])) {
	$menu_mode = $_REQUEST['menu_mode'];
} else {
	$menu_mode = "";
}

switch($menu_mode) {
	
	case 'delete':
	case 'forcedelete':
	case 'addafter':
	case 'addbefore':
	case 'moveup':
	case 'movedown':
	case 'moveback':
	case 'moveforward': {
		$sql = "SELECT * FROM ".K_TABLE_DOWNLOADS_CATEGORIES." WHERE downcat_id=".$downcat_id." LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$downcat_id = $m['downcat_id'];
				$downcat_item = $m['downcat_item'];
				$downcat_sub_id = $m['downcat_sub_id'];
				$downcat_position = $m['downcat_position'];
				
				$downcat_level = $m['downcat_level'];
				$downcat_name = $m['downcat_name'];
				$downcat_description = $m['downcat_description'];
				
				$dc_name = unserialize($downcat_name);
				$dc_description = unserialize($downcat_description);
			}
			else {
				F_display_db_error();
			}
		}
	break;
	}
	
	default : {
		break;
	}
}


switch($menu_mode) {

	case 'delete': // ask confirmation
	case unhtmlentities($l['w_delete']):
	case $l['w_delete']:{
		F_stripslashes_formfields();
		F_print_error("WARNING", $l['m_delete_confirm']);
		?>
		<p><?php echo "<b>".$dc_name[$selected_language]."</b>: ".$l['m_delete_confirm'].""; ?></p>
		
		<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_delete" id="form_delete">
		<input type="hidden" name="menu_mode" id="menu_mode" value="forcedelete" />
		<input type="hidden" name="downcat_id" id="downcat_id" value="<?php echo $downcat_id; ?>" />
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
			F_delete_item($downcat_id, $downcat_item, $downcat_sub_id, $downcat_position, false, $tree_table, $tree_suffix);
			
			//delete all this category's items
			$sql = "DELETE FROM ".K_TABLE_DOWNLOADS." WHERE download_category=".$downcat_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
		}
		$downcat_id=FALSE;
		break;
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_DOWNLOADS_CATEGORIES, "downcat_name='".$downcat_name."'", "downcat_id", $downcat_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				//compose downcat_name from array
				$downcat_name = addslashes(serialize($dc_name));
				//compose downcat_description from array
				$downcat_description = addslashes(serialize($dc_description));
				$sql = "UPDATE IGNORE ".K_TABLE_DOWNLOADS_CATEGORIES." SET 
					downcat_item='".$downcat_item."',
					downcat_sub_id='".$downcat_sub_id."',
					downcat_position='".$downcat_position."',
					downcat_level='".$downcat_level."',
					downcat_name='".$downcat_name."',
					downcat_description='".$downcat_description."' 
					WHERE downcat_id=".$downcat_id."";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
			}
		}
		break;
		}

	// Set variables to insert item after/before the selected element at the same level
	case 'addafter':{ 
		$downcat_position += 1;
	}

	case 'addbefore':{ // Add
		$downcat_item = 1;
		$downcat_level = 0;
		$dc_name = array();
		$dc_description = array();
		$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
		if($r = F_aiocpdb_query($sql, $db)) {
			while($m = F_aiocpdb_fetch_array($r)) {
				$dc_name[$m['language_code']] = "- new -";
				$dc_description[$m['language_code']] = "";
			}
		}
		else {
			F_display_db_error();
		}
		$downcat_name = addslashes(serialize($dc_name));
		$downcat_description = addslashes(serialize($dc_description));
		
		F_add_tree_position($downcat_sub_id, $downcat_position, false, $tree_table, $tree_suffix);
		
		$sql = "INSERT IGNORE INTO ".K_TABLE_DOWNLOADS_CATEGORIES." (
		downcat_item, 
		downcat_sub_id, 
		downcat_position, 
		downcat_level, 
		downcat_name, 
		downcat_description
		) VALUES (
		'".$downcat_item."', 
		'".$downcat_sub_id."', 
		'".$downcat_position."', 
		'".$downcat_level."', 
		'".$downcat_name."', 
		'".$downcat_description."')";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		else {
			$downcat_id = F_aiocpdb_insert_id();
		}
		break;
		}

	case 'moveup':{ // Move item 1 position up
		F_move_up_tree_item($downcat_id, $downcat_sub_id, $downcat_position, false, $tree_table, $tree_suffix);
		break;
	}

	case 'movedown':{ // Move item 1 position down
		F_move_down_tree_item($downcat_id, $downcat_sub_id, $downcat_position, false, $tree_table, $tree_suffix);
		break;
	}

	case 'moveback':{ // Move item and subtree 1 level up
		F_move_back_tree_item($downcat_id, $downcat_sub_id, $downcat_position, false, $tree_table, $tree_suffix);
		break;
	}

	case 'moveforward':{ // Move item and subtree 1 level up
		F_move_forward_tree_item($downcat_id, $downcat_sub_id, $downcat_position, false, $tree_table, $tree_suffix);
		break;
	}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$dc_name = array();
		$dc_description = array();
		$downcat_level = 0;
		break;
		}

	default :{ 
		break;
		}

} //end of switch

// Initialize variables
if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!isset($downcat_id) OR (!$downcat_id)) {
			$sql = "SELECT * FROM ".K_TABLE_DOWNLOADS_CATEGORIES." ORDER BY downcat_name LIMIT 1";
		} else {
			$sql = "SELECT * FROM ".K_TABLE_DOWNLOADS_CATEGORIES." WHERE downcat_id=".$downcat_id." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$downcat_id = $m['downcat_id'];
				$downcat_item = $m['downcat_item'];
				$downcat_sub_id = $m['downcat_sub_id'];
				$downcat_position = $m['downcat_position'];
				$downcat_level = $m['downcat_level'];
				$downcat_name = $m['downcat_name'];
				$dc_name = unserialize($downcat_name);
				$downcat_description = $m['downcat_description'];
				$dc_description = unserialize($downcat_description);
			}
			else { //clear fields
				$dc_name = array();
				$dc_description = array();
				$downcat_level = 0;
			}
		}
		else {
			F_display_db_error();
		}
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_downcateditor" id="form_downcateditor">

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<th class="edge"><?php echo $l['d_editor_item']; ?></th>
<th class="edge"><?php echo $l['d_editor_tree']; ?></th>
</tr>

<tr class="edge" valign="top">
<td class="edge" valign="top">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT category ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_category', 'h_downloadcat_select'); ?></b></td>
<td class="fillOE">
<select name="downcat_id" id="downcat_id" size="0" onchange="document.form_downcateditor.submit()">
<?php
$noscriptlink = $_SERVER['SCRIPT_NAME']."?";
if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {
	$noscriptlink .= "aiocp_dp=".$aiocp_dp."&amp;";
}
$noscriptlink .= "downcat_id=";
F_form_select_tree($downcat_id, false, $tree_table, $tree_suffix, $noscriptlink);
?>
</td>
</tr>
<!-- END SELECT category ==================== -->

<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE"><hr /></td>
</tr>

<!-- SELECT LEVEL ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_level', 'h_downloadcat_level'); ?></b></td>
<td class="fillOE"><select name="downcat_level" id="downcat_level" size="0">
<?php
$sql = "SELECT * FROM ".K_TABLE_LEVELS." ORDER BY level_code ";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['level_code']."\"";
		if($m['level_code'] == $downcat_level) {
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

<!-- iterate for each language ==================== -->
<?php
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
			
			echo "<tr class=\"fillE\">";
			echo "<td class=\"fillEO\"><hr /></td>";
			echo "<td class=\"fillEE\"><b>".$m['language_name']."</b></td>";
			echo "</tr>";
			echo "<tr class=\"fillO\">";
			echo "<td class=\"fillOO\" align=\"right\"><b>".F_display_field_name('w_name', '')."</b></td>";
			echo "<td class=\"fillOE\"><input type=\"text\" name=\"dc_name[".$m['language_code']."]\" id=\"dc_name_".$m['language_code']."\" value=\"".htmlentities(stripslashes($dc_name[$m['language_code']]), ENT_COMPAT, $doc_charset)."\" size=\"50\" maxlength=\"255\" /></td>";
			echo "</tr>";
			
			echo "<tr class=\"fillE\">";
			echo "<td class=\"fillEO\" align=\"right\" valign=\"top\"><b>".F_display_field_name('w_description', '')."</b><br />";
?>
<input type="button" name="htmleditor_<?php echo $m['language_code']; ?>" id="htmleditor_<?php echo $m['language_code']; ?>" value="<?php echo $l['w_button_html_editor']; ?>" onclick="htmlWindow=window.open('cp_edit_html.<?php echo CP_EXT; ?>?templates=page&amp;callingform=form_downcateditor&amp;callingfield=elements['+FJ_get_field_index(this)+']<?php echo $doc_charset_url; ?>','htmlWindow','dependent,height=500,width=800,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')" />
<?php
echo "</td>";
$current_ta_code = $dc_description[$m['language_code']];
$current_ta_code = stripslashes($current_ta_code);
echo "<td class=\"fillEE\"><textarea cols=\"50\" rows=\"5\" name=\"dc_description[".$m['language_code']."]\" id=\"dc_description_".$m['language_code']."\">".htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset)."</textarea></td>";
echo "</tr>";
		}
	}
	else {
		F_display_db_error();
	}
?>
<!-- END iterate for each language ==================== -->

<tr class="fillO">
<td class="fillOO" align="right">&nbsp;</td>
<td class="fillOE"><a href="cp_edit_downloads.<?php echo CP_EXT; ?>?download_category=<?php echo $downcat_id; ?>"><b><?php echo $l['t_download_editor']; ?>&nbsp;&gt;&gt;</b></a></td>
</tr>

</table>
</td>

<td class="edge" rowspan="2">
<table class="fill" border="0" cellspacing="2" cellpadding="1">
<tr class="fill">
<td class="fill">
<!-- Draw the menu tree with option buttons -->
<?php F_explore_tree(false, $tree_table, $tree_suffix); ?>
</td>
</tr>
</table>
</td>

</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<input type="hidden" name="downcat_item" id="downcat_item" value="<?php echo $downcat_item; ?>" />
<input type="hidden" name="downcat_sub_id" id="downcat_sub_id" value="<?php echo $downcat_sub_id; ?>" />
<input type="hidden" name="downcat_position" id="downcat_position" value="<?php echo $downcat_position; ?>" />
<?php //show buttons
if ($downcat_id) {
	F_submit_button("form_downcateditor","menu_mode",$l['w_update']); 
	F_submit_button("form_downcateditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_downcateditor","menu_mode",$l['w_add']); 
F_submit_button("form_downcateditor","menu_mode",$l['w_clear']); 
?>
</td>
</tr>
</table>
</form>
<!-- ====================================================== -->

<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_downcateditor.downcat_id.focus();

//get next elements ID
function FJ_get_field_index(what) {
	for (var i=0;i<document.form_downcateditor.elements.length;i++) {
		if(what == document.form_downcateditor.elements[i]) {
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
