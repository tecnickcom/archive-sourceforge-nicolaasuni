<?php
//============================================================+
// File name   : cp_edit_links_categories.php                  
// Begin       : 2001-09-21                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit categories for links                     
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_LINKS_CATEGORIES;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

require_once('../../shared/code/cp_functions_form.'.CP_EXT);
require_once('../code/cp_functions_tree.'.CP_EXT);
require_once('../../shared/code/cp_functions_tree.'.CP_EXT);

$thispage_title = $l['t_links_categories_editor'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages;


//initialize variables
$tree_table = K_TABLE_LINKS_CATEGORIES;
$tree_suffix = "linkscat";

//if the tree is void (no items), create new item (first element)
if(!F_count_rows($tree_table)) {
	$linkscat_id = 1;
	$linkscat_item = 1;
	$linkscat_sub_id = 0;
	$linkscat_position = 1;
	$lc_name = array();
	$lc_description = array();
	$linkscat_target = 2;
	$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			$lc_name[$m['language_code']] = "default";
			$lc_description[$m['language_code']] = "default";
		}
	}
	else {
		F_display_db_error();
	}
	$linkscat_name = addslashes(serialize($lc_name));
	$linkscat_description = addslashes(serialize($lc_description));
	
	$sql = "INSERT IGNORE INTO ".K_TABLE_LINKS_CATEGORIES." (
		linkscat_id,
		linkscat_item, 
		linkscat_sub_id, 
		linkscat_position, 
		linkscat_name,
		linkscat_target,
		linkscat_description
		) VALUES (
		'".$linkscat_id."', 
		'".$linkscat_item."', 
		'".$linkscat_sub_id."', 
		'".$linkscat_position."', 
		'".$linkscat_name."', 
		'".$linkscat_target."', 
		'".$linkscat_description."'
		)";
	if(!$r = F_aiocpdb_query($sql, $db)) {
		F_display_db_error();
	}
	else {
		$linkscat_id = F_aiocpdb_insert_id();
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
		$sql = "SELECT * FROM ".K_TABLE_LINKS_CATEGORIES." WHERE linkscat_id=".$linkscat_id." LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$linkscat_id = $m['linkscat_id'];
				$linkscat_item = $m['linkscat_item'];
				$linkscat_sub_id = $m['linkscat_sub_id'];
				$linkscat_position = $m['linkscat_position'];
				
				$linkscat_name = $m['linkscat_name'];
				$linkscat_target = $m['linkscat_target'];
				$linkscat_description = $m['linkscat_description'];
				
				$lc_name = unserialize($linkscat_name);
				$lc_description = unserialize($linkscat_description);
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
		<p><?php echo "<b>".$lc_name[$selected_language]."</b>: ".$l['m_delete_confirm'].""; ?></p>
		
		<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_delete" id="form_delete">
		<input type="hidden" name="menu_mode" id="menu_mode" value="forcedelete" />
		<input type="hidden" name="linkscat_id" id="linkscat_id" value="<?php echo $linkscat_id; ?>" />
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
			F_delete_item($linkscat_id, $linkscat_item, $linkscat_sub_id, $linkscat_position, false, $tree_table, $tree_suffix);
			
			//delete all this category's items
			$sql = "DELETE FROM ".K_TABLE_LINKS." WHERE links_category=".$linkscat_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
		}
		$linkscat_id=FALSE;
		break;
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update linkscat
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_LINKS_CATEGORIES, "linkscat_name='".$linkscat_name."'", "linkscat_id", $linkscat_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$linkscat_name = addslashes(serialize($lc_name));
				$linkscat_description = addslashes(serialize($lc_description));
				$sql = "UPDATE IGNORE ".K_TABLE_LINKS_CATEGORIES." SET 
					linkscat_item='".$linkscat_item."',
					linkscat_sub_id='".$linkscat_sub_id."',
					linkscat_position='".$linkscat_position."',
					linkscat_name='".$linkscat_name."',
					linkscat_target='".$linkscat_target."',
					linkscat_description='".$linkscat_description."' 
					WHERE linkscat_id=".$linkscat_id."";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
			}
		}
		break;
		}

	// Set variables to insert item after/before the selected element at the same level
	case 'addafter':{ 
		$linkscat_position += 1;
	}

	case 'addbefore':{ // Add
		$linkscat_item = 1;
		
		$lc_name = array();
		$lc_description = array();
		$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
		if($r = F_aiocpdb_query($sql, $db)) {
			while($m = F_aiocpdb_fetch_array($r)) {
				$lc_name[$m['language_code']] = "- new -";
				$lc_description[$m['language_code']] = "";
			}
		}
		else {
			F_display_db_error();
		}
		$linkscat_name = addslashes(serialize($lc_name));
		$linkscat_description = addslashes(serialize($lc_description));
		
		F_add_tree_position($linkscat_sub_id, $linkscat_position, false, $tree_table, $tree_suffix);
		
		$sql = "INSERT IGNORE INTO ".K_TABLE_LINKS_CATEGORIES." (
		linkscat_item, 
		linkscat_sub_id, 
		linkscat_position, 
		linkscat_name, 
		linkscat_target, 
		linkscat_description
		) VALUES (
		'".$linkscat_item."', 
		'".$linkscat_sub_id."', 
		'".$linkscat_position."', 
		'".$linkscat_name."', 
		'".$linkscat_target."', 
		'".$linkscat_description."')";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		else {
			$linkscat_id = F_aiocpdb_insert_id();
		}
		break;
		}

	case 'moveup':{ // Move item 1 position up
		F_move_up_tree_item($linkscat_id, $linkscat_sub_id, $linkscat_position, false, $tree_table, $tree_suffix);
		break;
	}

	case 'movedown':{ // Move item 1 position down
		F_move_down_tree_item($linkscat_id, $linkscat_sub_id, $linkscat_position, false, $tree_table, $tree_suffix);
		break;
	}

	case 'moveback':{ // Move item and subtree 1 level up
		F_move_back_tree_item($linkscat_id, $linkscat_sub_id, $linkscat_position, false, $tree_table, $tree_suffix);
		break;
	}

	case 'moveforward':{ // Move item and subtree 1 level up
		F_move_forward_tree_item($linkscat_id, $linkscat_sub_id, $linkscat_position, false, $tree_table, $tree_suffix);
		break;
	}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$lc_name = array();
		$lc_description = array();
		break;
		}

	default :{
		break;
		}

} //end of switch

// Initialize variables
if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!isset($linkscat_id) OR (!$linkscat_id)) {
			$sql = "SELECT * FROM ".K_TABLE_LINKS_CATEGORIES." ORDER BY linkscat_name LIMIT 1";
		} else {
			$sql = "SELECT * FROM ".K_TABLE_LINKS_CATEGORIES." WHERE linkscat_id=".$linkscat_id." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$linkscat_id = $m['linkscat_id'];
				$linkscat_item = $m['linkscat_item'];
				$linkscat_sub_id = $m['linkscat_sub_id'];
				$linkscat_position = $m['linkscat_position'];
				$linkscat_name = $m['linkscat_name'];
				$lc_name = unserialize($linkscat_name);
				$linkscat_target = $m['linkscat_target'];
				$linkscat_description = $m['linkscat_description'];
				$lc_description = unserialize($linkscat_description);
			}
			else {
				$lc_name = array();
				$lc_description = array();
			}
		}
		else {
			F_display_db_error();
		}
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_linkscateditor" id="form_linkscateditor">
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
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_category', 'h_linkscat_select'); ?></b></td>
<td class="fillOE">
<select name="linkscat_id" id="linkscat_id" size="0" onchange="document.form_linkscateditor.submit()">
<?php
$noscriptlink = $_SERVER['SCRIPT_NAME']."?";
if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {
	$noscriptlink .= "aiocp_dp=".$aiocp_dp."&amp;";
}
$noscriptlink .= "linkscat_id=";
F_form_select_tree($linkscat_id, false, $tree_table, $tree_suffix, $noscriptlink);
?>
</td>
</tr>
<!-- END SELECT category ==================== -->

<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE"><hr /></td>
</tr>

<!-- SELECT TARGET ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_target', 'h_target_name'); ?></b></td>
<td class="fillOE" colspan="2"><select name="linkscat_target" id="linkscat_target" size="0">
<?php
$sql = "SELECT * FROM ".K_TABLE_FRAME_TARGETS." ORDER BY target_name";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['target_id']."\"";
		if($m['target_id'] == $linkscat_target) {
			echo " selected=\"selected\"";
		}
		echo ">".$m['target_name']."&nbsp;</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select></td>
</tr>
<!-- END SELECT TARGET ==================== -->

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
			echo "<td class=\"fillOO\" align=\"right\"><b>".F_display_field_name('w_name', 'h_linkscat_name')."</b></td>";
			echo "<td class=\"fillOE\"><input type=\"text\" name=\"lc_name[".$m['language_code']."]\" id=\"lc_name_".$m['language_code']."\" value=\"".htmlentities(stripslashes($lc_name[$m['language_code']]), ENT_COMPAT, $doc_charset)."\" size=\"50\" maxlength=\"255\" /></td>";
			echo "</tr>";
			
			echo "<tr class=\"fillE\">";
			echo "<td class=\"fillEO\" align=\"right\" valign=\"top\"><b>".F_display_field_name('w_description', 'h_linkscat_description')."</b><br />";
?>
<input type="button" name="htmleditor_<?php echo $m['language_code']; ?>" id="htmleditor_<?php echo $m['language_code']; ?>" value="<?php echo $l['w_button_html_editor']; ?>" onclick="htmlWindow=window.open('cp_edit_html.<?php echo CP_EXT; ?>?templates=page&amp;callingform=form_linkscateditor&amp;callingfield=elements['+FJ_get_field_index(this)+']<?php echo $doc_charset_url; ?>','htmlWindow','dependent,height=500,width=800,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')" />
<?php
echo "</td>";
$current_ta_code = $lc_description[$m['language_code']];
$current_ta_code = stripslashes($current_ta_code);
echo "<td class=\"fillEE\"><textarea cols=\"50\" rows=\"5\" name=\"lc_description[".$m['language_code']."]\" id=\"lc_description_".$m['language_code']."\">".htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset)."</textarea></td>";
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
<td class="fillOE"><a href="cp_edit_links.<?php echo CP_EXT; ?>?links_category=<?php echo $linkscat_id; ?>"><b><?php echo $l['t_links_editor']; ?>&nbsp;&gt;&gt;</b></a></td>
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
<input type="hidden" name="linkscat_item" id="linkscat_item" value="<?php echo $linkscat_item; ?>" />
<input type="hidden" name="linkscat_sub_id" id="linkscat_sub_id" value="<?php echo $linkscat_sub_id; ?>" />
<input type="hidden" name="linkscat_position" id="linkscat_position" value="<?php echo $linkscat_position; ?>" />
<?php //show buttons
if ($linkscat_id) {
	F_submit_button("form_linkscateditor","menu_mode",$l['w_update']); 
	F_submit_button("form_linkscateditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_linkscateditor","menu_mode",$l['w_add']); 
F_submit_button("form_linkscateditor","menu_mode",$l['w_clear']); 
?>
</td>

</tr>
</table>
</form>
<!-- ====================================================== -->


<!-- Cange focus to linkscat_id select -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_linkscateditor.linkscat_id.focus();

//get next elements ID
function FJ_get_field_index(what) {
	for (var i=0;i<document.form_linkscateditor.elements.length;i++) {
		if(what == document.form_linkscateditor.elements[i]) {
			return(i+1);
		}
	}
	return (-1);
}
//]]>
</script>
<!-- END Cange focus to linkscat_id select -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
