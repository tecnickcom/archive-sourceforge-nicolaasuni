<?php
//============================================================+
// File name   : cp_edit_review_categories.php                 
// Begin       : 2001-11-29                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit categories for Reviews                   
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_REVIEW_CATEGORIES;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

require_once('../../shared/code/cp_functions_form.'.CP_EXT);
require_once('../code/cp_functions_upload.'.CP_EXT);
require_once('../code/cp_functions_tree.'.CP_EXT);
require_once('../../shared/code/cp_functions_tree.'.CP_EXT);

$thispage_title = $l['t_reviews_categories_editor'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages;


//initialize variables
$tree_table = K_TABLE_REVIEWS_CATEGORIES;
$tree_suffix = "revcat";

//if the tree is void (no items), create new item (first element)
if(!F_count_rows($tree_table)) {
	$revcat_id = 1;
	$revcat_item = 1;
	$revcat_sub_id = 0;
	$revcat_position = 1;
	$revcat_level = 0;
	$rc_name = array();
	$rc_description = array();
	$revcat_image = K_BLANK_IMAGE;
	$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			$rc_name[$m['language_code']] = "default";
			$rc_description[$m['language_code']] = "default";
		}
	}
	else {
		F_display_db_error();
	}
	$revcat_name = addslashes(serialize($rc_name));
	$revcat_description = addslashes(serialize($rc_description));
	
	$sql = "INSERT IGNORE INTO ".K_TABLE_REVIEWS_CATEGORIES." (
		revcat_id,
		revcat_item, 
		revcat_sub_id, 
		revcat_position, 
		revcat_level,
		revcat_name,
		revcat_description,
		revcat_image
		) VALUES (
		'".$revcat_id."', 
		'".$revcat_item."', 
		'".$revcat_sub_id."', 
		'".$revcat_position."', 
		'".$revcat_level."', 
		'".$revcat_name."', 
		'".$revcat_description."', 
		'".$revcat_image."'
		)";
	if(!$r = F_aiocpdb_query($sql, $db)) {
		F_display_db_error();
	}
	else {
		$revcat_id = F_aiocpdb_insert_id();
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
		$sql = "SELECT * FROM ".K_TABLE_REVIEWS_CATEGORIES." WHERE revcat_id=".$revcat_id." LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$revcat_id = $m['revcat_id'];
				$revcat_item = $m['revcat_item'];
				$revcat_sub_id = $m['revcat_sub_id'];
				$revcat_position = $m['revcat_position'];
				
				$revcat_level = $m['revcat_level'];
				$revcat_name = $m['revcat_name'];
				$revcat_description = $m['revcat_description'];
				$revcat_image = $m['revcat_image'];
				
				$rc_name = unserialize($revcat_name);
				$rc_description = unserialize($revcat_description);
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
		<p><?php echo "<b>".$rc_name[$selected_language]."</b>: ".$l['m_delete_confirm'].""; ?></p>
		
		<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_delete" id="form_delete">
		<input type="hidden" name="menu_mode" id="menu_mode" value="forcedelete" />
		<input type="hidden" name="revcat_id" id="revcat_id" value="<?php echo $revcat_id; ?>" />
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
			F_delete_item($revcat_id, $revcat_item, $revcat_sub_id, $revcat_position, false, $tree_table, $tree_suffix);
			
			//delete all this category's items
			$sql = "DELETE FROM ".K_TABLE_REVIEWS." WHERE review_category=".$revcat_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
		}
		$revcat_id=FALSE;
		break;
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_REVIEWS_CATEGORIES, "revcat_name='".$revcat_name."'", "revcat_id", $revcat_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				if($_FILES['userfile']['name']) {$revcat_image = F_upload_file("userfile", K_PATH_IMAGES_REVIEWS);} //upload
				$revcat_name = addslashes(serialize($rc_name));
				$revcat_description = addslashes(serialize($rc_description));
				$sql = "UPDATE IGNORE ".K_TABLE_REVIEWS_CATEGORIES." SET 
					revcat_item='".$revcat_item."',
					revcat_sub_id='".$revcat_sub_id."',
					revcat_position='".$revcat_position."',
					revcat_level='".$revcat_level."', 
					revcat_name='".$revcat_name."', 
					revcat_description='".$revcat_description."', 
					revcat_image='".$revcat_image."' 
					WHERE revcat_id=".$revcat_id."";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
			}
		}
		break;
		}

	// Set variables to insert item after/before the selected element at the same level
	case 'addafter':{ 
		$revcat_position += 1;
	}

	case 'addbefore':{ // Add
		$revcat_item = 1;
		$revcat_level = 0;
		$revcat_image = K_BLANK_IMAGE;
		
		$rc_name = array();
		$rc_description = array();
		$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
		if($r = F_aiocpdb_query($sql, $db)) {
			while($m = F_aiocpdb_fetch_array($r)) {
				$rc_name[$m['language_code']] = "- new -";
				$rc_description[$m['language_code']] = "";
			}
		}
		else {
			F_display_db_error();
		}
		$revcat_name = addslashes(serialize($rc_name));
		$revcat_description = addslashes(serialize($rc_description));
		
		F_add_tree_position($revcat_sub_id, $revcat_position, false, $tree_table, $tree_suffix);
		
		$sql = "INSERT IGNORE INTO ".K_TABLE_REVIEWS_CATEGORIES." (
		revcat_item, 
		revcat_sub_id, 
		revcat_position, 
		revcat_level, 
		revcat_name, 
		revcat_description, 
		revcat_image
		) VALUES (
		'".$revcat_item."', 
		'".$revcat_sub_id."', 
		'".$revcat_position."', 
		'".$revcat_level."', 
		'".$revcat_name."', 
		'".$revcat_description."', 
		'".$revcat_image."')";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		else {
			$revcat_id = F_aiocpdb_insert_id();
		}
		break;
		}

	case 'moveup':{ // Move item 1 position up
		F_move_up_tree_item($revcat_id, $revcat_sub_id, $revcat_position, false, $tree_table, $tree_suffix);
		break;
	}

	case 'movedown':{ // Move item 1 position down
		F_move_down_tree_item($revcat_id, $revcat_sub_id, $revcat_position, false, $tree_table, $tree_suffix);
		break;
	}

	case 'moveback':{ // Move item and subtree 1 level up
		F_move_back_tree_item($revcat_id, $revcat_sub_id, $revcat_position, false, $tree_table, $tree_suffix);
		break;
	}

	case 'moveforward':{ // Move item and subtree 1 level up
		F_move_forward_tree_item($revcat_id, $revcat_sub_id, $revcat_position, false, $tree_table, $tree_suffix);
		break;
	}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$revcat_level = 0;
		$rc_name = array();
		$rc_description = array();
		$revcat_image = K_BLANK_IMAGE;
		break;
		}

	default :{
		break;
		}

} //end of switch

// Initialize variables
if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!isset($revcat_id) OR (!$revcat_id)) {
			$sql = "SELECT * FROM ".K_TABLE_REVIEWS_CATEGORIES." ORDER BY revcat_name LIMIT 1";
		} else {
			$sql = "SELECT * FROM ".K_TABLE_REVIEWS_CATEGORIES." WHERE revcat_id=".$revcat_id." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$revcat_id = $m['revcat_id'];
				$revcat_item = $m['revcat_item'];
				$revcat_sub_id = $m['revcat_sub_id'];
				$revcat_position = $m['revcat_position'];
				$revcat_level = $m['revcat_level'];
				$revcat_name = $m['revcat_name'];
				$rc_name = unserialize($revcat_name);
				$revcat_description = $m['revcat_description'];
				$rc_description = unserialize($revcat_description);
				$revcat_image = $m['revcat_image'];
			}
			else {
				$revcat_level = 0;
				$rc_name = array();
				$rc_description = array();
				$revcat_image = K_BLANK_IMAGE;
			}
		}
		else {
			F_display_db_error();
		}
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_reviewcateditor" id="form_reviewcateditor">
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
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_category', 'h_reviewscat_select'); ?></b></td>
<td class="fillOE">
<select name="revcat_id" id="revcat_id" size="0" onchange="document.form_reviewcateditor.submit()">
<?php
$noscriptlink = $_SERVER['SCRIPT_NAME']."?";
if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {
	$noscriptlink .= "aiocp_dp=".$aiocp_dp."&amp;";
}
$noscriptlink .= "revcat_id=";
F_form_select_tree($revcat_id, false, $tree_table, $tree_suffix, $noscriptlink);
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
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_level', 'h_reviewscat_level'); ?></b></td>
<td class="fillOE"><select name="revcat_level" id="revcat_level" size="0">
<?php
$sql = "SELECT * FROM ".K_TABLE_LEVELS." ORDER BY level_code ";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['level_code']."\"";
		if($m['level_code'] == $revcat_level) {
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
<!-- END SELECT LEVEL ==================== -->
<td rowspan="3" align="right" valign="top"><img name="imagereview" src="<?php echo K_PATH_IMAGES_REVIEWS; ?><?php echo $revcat_image; ?>" border="0" alt="" /></td>
</tr>


<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_image', 'h_reviewscat_image'); ?></b></td>
<td class="fillEE"><input type="text" name="revcat_image" id="revcat_image" value="<?php echo $revcat_image; ?>" size="40" maxlength="255" onchange="FJ_show_image2()" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_image_dir', 'h_reviewscat_imgdir'); ?></b></td>
<td class="fillOE" colspan="2">
<select name="revcat_image_dir" id="revcat_image_dir" size="0" onchange="document.form_reviewcateditor.revcat_image.value=document.form_reviewcateditor.revcat_image_dir.options[document.form_reviewcateditor.revcat_image_dir.selectedIndex].value; FJ_show_image ()">
<?php
// read directory for files (only graphics files).
$handle = opendir(K_PATH_IMAGES_REVIEWS);
echo "<option value=\"".K_BLANK_IMAGE."\" selected=\"selected\"> - </option>\n";
	while (false !== ($file = readdir($handle))) {
		$path_parts = pathinfo($file);
		$file_ext = strtolower($path_parts['extension']);
		//check file type (GIF, JPG, PNG)
		if(($file_ext=="png")OR($file_ext=="gif")OR($file_ext=="jpg")OR($file_ext=="jpeg")) {
			echo "<option value=\"".$file."\"";
			if($file == $revcat_image) {
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

<!-- Upload file ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_upload', 'h_reviewscat_imgup'); ?></b></td>
<td class="fillEE">
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo K_MAX_UPLOAD_SIZE; ?>" />
<input type="file" name="userfile" id="userfile" size="20" />
</td>
</tr>
<!-- END Upload file ==================== -->

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
			
			echo "<tr class=\"fillO\">";
			echo "<td class=\"fillOO\"><hr /></td>";
			echo "<td class=\"fillOE\"><b>".$m['language_name']."</b></td>";
			echo "</tr>";
			echo "<tr class=\"fillE\">";
			echo "<td class=\"fillEO\" align=\"right\"><b>".F_display_field_name('w_name', 'h_reviewscat_name')."</b></td>";
			echo "<td class=\"fillEE\"><input type=\"text\" name=\"rc_name[".$m['language_code']."]\" id=\"rc_name_".$m['language_code']."\" value=\"".htmlentities(stripslashes($rc_name[$m['language_code']]), ENT_COMPAT, $doc_charset)."\" size=\"50\" maxlength=\"255\" /></td>";
			echo "</tr>";
			
			echo "<tr class=\"fillO\">";
			echo "<td class=\"fillOO\" align=\"right\" valign=\"top\"><b>".F_display_field_name('w_description', 'h_reviewscat_description')."</b><br />";
?>
<input type="button" name="htmleditor_<?php echo $m['language_code']; ?>" id="htmleditor_<?php echo $m['language_code']; ?>" value="<?php echo $l['w_button_html_editor']; ?>" onclick="htmlWindow=window.open('cp_edit_html.<?php echo CP_EXT; ?>?templates=page&amp;callingform=form_reviewcateditor&amp;callingfield=elements['+FJ_get_field_index(this)+']<?php echo $doc_charset_url; ?>','htmlWindow','dependent,height=500,width=800,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')" />
<?php
echo "</td>";
$current_ta_code = $rc_description[$m['language_code']];
$current_ta_code = stripslashes($current_ta_code);
echo "<td class=\"fillOE\"><textarea cols=\"50\" rows=\"5\" name=\"rc_description[".$m['language_code']."]\" id=\"rc_description_".$m['language_code']."\">".htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset)."</textarea></td>";
echo "</tr>";
		}
	}
	else {
		F_display_db_error();
	}
?>
<!-- END iterate for each language ==================== -->

<tr class="fillE">
<td class="fillEO" align="right">&nbsp;</td>
<td class="fillEE"><a href="cp_edit_reviews.<?php echo CP_EXT; ?>?review_category=<?php echo $revcat_id; ?>"><b><?php echo $l['t_reviews_editor']; ?>&nbsp;&gt;&gt;</b></a></td>
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
<input type="hidden" name="revcat_item" id="revcat_item" value="<?php echo $revcat_item; ?>" />
<input type="hidden" name="revcat_sub_id" id="revcat_sub_id" value="<?php echo $revcat_sub_id; ?>" />
<input type="hidden" name="revcat_position" id="revcat_position" value="<?php echo $revcat_position; ?>" />
<?php //show buttons
if ($revcat_id) {
	F_submit_button("form_reviewcateditor","menu_mode",$l['w_update']); 
	F_submit_button("form_reviewcateditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_reviewcateditor","menu_mode",$l['w_add']); 
F_submit_button("form_reviewcateditor","menu_mode",$l['w_clear']); 
?>
</td>

</tr>
</table>
</form>
<!-- ====================================================== -->

<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_reviewcateditor.revcat_id.focus();

//get next elements ID
function FJ_get_field_index(what) {
	for (var i=0;i<document.form_reviewcateditor.elements.length;i++) {
		if(what == document.form_reviewcateditor.elements[i]) {
			return(i+1);
		}
	}
	return (-1);
}

function FJ_show_image (){
	document.images.imagereview.src= "<?php echo K_PATH_IMAGES_REVIEWS; ?>"+document.form_reviewcateditor.revcat_image_dir.options[document.form_reviewcateditor.revcat_image_dir.selectedIndex].value;
}

function FJ_show_image2 (){
	document.images.imagereview.src= "<?php echo K_PATH_IMAGES_REVIEWS; ?>"+document.form_reviewcateditor.revcat_image.value;
}
//]]>
</script>





<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
