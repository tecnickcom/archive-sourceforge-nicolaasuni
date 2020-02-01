<?php
//============================================================+
// File name   : cp_edit_products_categories.php               
// Begin       : 2002-06-19                                    
// Last Update : 2008-07-07
//                                                             
// Description : Edit Products Categories                      
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_EC_PRODUCTS_CATEGORIES;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

require_once('../code/cp_functions_tree.'.CP_EXT);
require_once('../../shared/code/cp_functions_tree.'.CP_EXT);
require_once('../../shared/code/cp_functions_form.'.CP_EXT);
require_once('../code/cp_functions_upload.'.CP_EXT);

$thispage_title = $l['t_product_categories_editor'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages;


//initialize variables
$tree_table = K_TABLE_EC_PRODUCTS_CATEGORIES;
$tree_suffix = "prodcat";

//if the tree is void (no items), create new item (first element)
if(!F_count_rows($tree_table)) {
	$prodcat_id = 1;
	$prodcat_item = 1;
	$prodcat_sub_id = 0;
	$prodcat_position = 1;
	$prodcat_level = 0;
	$prodcat_code = "";
	$rc_name = array();
	$rc_description = array();
	$prodcat_image = K_BLANK_IMAGE;
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
	$prodcat_name = addslashes(serialize($rc_name));
	$prodcat_description = addslashes(serialize($rc_description));
	$sql = "INSERT IGNORE INTO ".K_TABLE_EC_PRODUCTS_CATEGORIES." (
		prodcat_id,
		prodcat_item, 
		prodcat_sub_id, 
		prodcat_position, 
		prodcat_level, 
		prodcat_code, 
		prodcat_name, 
		prodcat_description, 
		prodcat_image
	) VALUES (
		'".$prodcat_id."', 
		'".$prodcat_item."', 
		'".$prodcat_sub_id."', 
		'".$prodcat_position."', 
		'".$prodcat_level."', 
		'".$prodcat_code."', 
		'".$prodcat_name."', 
		'".$prodcat_description."', 
		'".$prodcat_image."'
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
		$sql = "SELECT * FROM ".K_TABLE_EC_PRODUCTS_CATEGORIES." WHERE prodcat_id=".$prodcat_id." LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$prodcat_id = $m['prodcat_id'];
				$prodcat_item = $m['prodcat_item'];
				$prodcat_sub_id = $m['prodcat_sub_id'];
				$prodcat_position = $m['prodcat_position'];
				$prodcat_level = $m['prodcat_level'];
				$prodcat_code = $m['prodcat_code'];
				$prodcat_name = $m['prodcat_name'];
				$rc_name = unserialize($prodcat_name);
				$prodcat_description = $m['prodcat_description'];
				$rc_description = unserialize($prodcat_description);
				$prodcat_image = $m['prodcat_image'];
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
		<input type="hidden" name="prodcat_id" id="prodcat_id" value="<?php echo $prodcat_id; ?>" />
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
			F_delete_item($prodcat_id, $prodcat_item, $prodcat_sub_id, $prodcat_position, false, $tree_table, $tree_suffix);
			
			//delete all this category's products images
			$sql = "SELECT * FROM ".K_TABLE_EC_PRODUCTS." WHERE product_category_id=".$prodcat_id."";
			if($r = F_aiocpdb_query($sql, $db)) {
				while($m = F_aiocpdb_fetch_array($r)) {
					if(($m['product_image'])AND($m['product_image'] != K_BLANK_IMAGE)) { //if exist delete product image
						//create product photo path
						$thisproduct_photo = K_PATH_IMAGES_PRODUCTS_CATEGORIES.$m['product_image'];
						$thisproduct_photo_small = K_PATH_IMAGES_PRODUCTS_CATEGORIES."s_".$m['product_image'];
						if(file_exists($thisproduct_photo)) { // check if file exist
							if(!unlink($thisproduct_photo)) { // delete product photo
								F_print_error("ERROR", $thisproduct_photo.": ".$l['m_delete_not']);
							}
						}
						if(file_exists($thisproduct_photo_small)) { // check if file exist
							if(!unlink($thisproduct_photo_small)) { // delete product small photo
								F_print_error("ERROR", $thisproduct_photo_small.": ".$l['m_delete_not']);
							}
						}
					}
				}
			}
			else {
				F_display_db_error();
			}
			
			//delete all this category's products
			$sql = "DELETE FROM ".K_TABLE_EC_PRODUCTS." WHERE product_category_id=".$prodcat_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
		}
		$prodcat_id=FALSE;
		break;
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_EC_PRODUCTS_CATEGORIES, "prodcat_name='".$prodcat_name."' AND prodcat_sub_id='".$prodcat_sub_id."'", "prodcat_id", $prodcat_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				if($_FILES['userfile']['name']) {$prodcat_image = F_upload_file("userfile", K_PATH_IMAGES_PRODUCTS_CATEGORIES);} //upload
				$prodcat_name = addslashes(serialize($rc_name));
				$prodcat_description = addslashes(serialize($rc_description));
				$sql = "UPDATE IGNORE ".K_TABLE_EC_PRODUCTS_CATEGORIES." SET 
					prodcat_item='".$prodcat_item."',
					prodcat_sub_id='".$prodcat_sub_id."',
					prodcat_position='".$prodcat_position."',
					prodcat_level='".$prodcat_level."', 
					prodcat_code='".$prodcat_code."', 
					prodcat_name='".$prodcat_name."', 
					prodcat_description='".$prodcat_description."', 
					prodcat_image='".$prodcat_image."'
					WHERE prodcat_id=".$prodcat_id."";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
			}
		}
		break;
	}

	// Set variables to insert item after/before the selected element at the same level
	case 'addafter':{ 
		$prodcat_position += 1;
	}

	case 'addbefore':{ // Add
		$prodcat_item = 1;
		$prodcat_level = 0;
		$prodcat_code = "";
		$rc_name = array();
		$rc_description = array();
		$prodcat_image = K_BLANK_IMAGE;
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
		$prodcat_name = addslashes(serialize($rc_name));
		$prodcat_description = addslashes(serialize($rc_description));
		
		F_add_tree_position($prodcat_sub_id, $prodcat_position, false, $tree_table, $tree_suffix);
		$sql = "INSERT IGNORE INTO ".K_TABLE_EC_PRODUCTS_CATEGORIES." (
			prodcat_item, 
			prodcat_sub_id, 
			prodcat_position, 
			prodcat_level, 
			prodcat_code, 
			prodcat_name, 
			prodcat_description, 
			prodcat_image
			) VALUES (
			'".$prodcat_item."', 
			'".$prodcat_sub_id."', 
			'".$prodcat_position."', 
			'".$prodcat_level."', 
			'".$prodcat_code."', 
			'".$prodcat_name."', 
			'".$prodcat_description."', 
			'".$prodcat_image."'
			)";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		else {
			$prodcat_id = F_aiocpdb_insert_id();
		}
		break;
	}

	case 'moveup':{ // Move item 1 position up
		F_move_up_tree_item($prodcat_id, $prodcat_sub_id, $prodcat_position, false, $tree_table, $tree_suffix);
		break;
	}

	case 'movedown':{ // Move item 1 position down
		F_move_down_tree_item($prodcat_id, $prodcat_sub_id, $prodcat_position, false, $tree_table, $tree_suffix);
		break;
	}

	case 'moveback':{ // Move item and subtree 1 level up
		F_move_back_tree_item($prodcat_id, $prodcat_sub_id, $prodcat_position, false, $tree_table, $tree_suffix);
		break;
	}

	case 'moveforward':{ // Move item and subtree 1 level up
		F_move_forward_tree_item($prodcat_id, $prodcat_sub_id, $prodcat_position, false, $tree_table, $tree_suffix);
		break;
	}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$prodcat_level = 0;
		$prodcat_code = "";
		$rc_name = array();
		$rc_description = array();
		$prodcat_image = K_BLANK_IMAGE;
		break;
		}

	default :{
		break;
		}

} //end of switch

// Initialize variables
if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!isset($prodcat_id) OR (!$prodcat_id)) {
			$sql = "SELECT * FROM ".K_TABLE_EC_PRODUCTS_CATEGORIES." ORDER BY prodcat_name LIMIT 1";
		} else {
			$sql = "SELECT * FROM ".K_TABLE_EC_PRODUCTS_CATEGORIES." WHERE prodcat_id=".$prodcat_id." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$prodcat_id = $m['prodcat_id'];
				$prodcat_item = $m['prodcat_item'];
				$prodcat_sub_id = $m['prodcat_sub_id'];
				$prodcat_position = $m['prodcat_position'];
				$prodcat_level = $m['prodcat_level'];
				$prodcat_code = $m['prodcat_code'];
				$prodcat_name = $m['prodcat_name'];
				$rc_name = unserialize($prodcat_name);
				$prodcat_description = $m['prodcat_description'];
				$rc_description = unserialize($prodcat_description);
				$prodcat_image = $m['prodcat_image'];
			}
			else {
				$prodcat_level = 0;
				$prodcat_code = "";
				$rc_name = array();
				$rc_description = array();
				$prodcat_image = K_BLANK_IMAGE;
			}
		}
		else {
			F_display_db_error();
		}
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_productcateditor" id="form_productcateditor">
<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<th class="edge"><?php echo $l['d_editor_item'].": [".$prodcat_id."]"; ?></th>
<th class="edge"><?php echo $l['d_editor_tree']; ?></th>
</tr>

<tr class="edge" valign="top">
<td class="edge" valign="top">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT category ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_category', 'h_productscat_select'); ?></b></td>
<td class="fillOE">
<select name="prodcat_id" id="prodcat_id" size="0" onchange="document.form_productcateditor.submit()">
<?php
$noscriptlink = $_SERVER['SCRIPT_NAME']."?";
if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {
	$noscriptlink .= "aiocp_dp=".$aiocp_dp."&amp;";
}
$noscriptlink .= "prodcat_id=";
F_form_select_tree($prodcat_id, false, $tree_table, $tree_suffix, $noscriptlink); ?>
</td>
</tr>
<!-- END SELECT category ==================== -->

<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE"><hr /></td>
</tr>

<!-- SELECT LEVEL ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_level', 'h_productscat_level'); ?></b></td>
<td class="fillOE"><select name="prodcat_level" id="prodcat_level" size="0">
<?php
$sql = "SELECT * FROM ".K_TABLE_LEVELS." ORDER BY level_code ";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['level_code']."\"";
		if($m['level_code'] == $prodcat_level) {
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
<td rowspan="3" align="right" valign="top"><img name="imageproductcat" src="<?php echo K_PATH_IMAGES_PRODUCTS_CATEGORIES."".$prodcat_image; ?>" border="0" alt="" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_code', 'h_productscat_code'); ?></b></td>
<td class="fillEE"><input type="text" name="prodcat_code" id="prodcat_code" value="<?php echo $prodcat_code; ?>" size="40" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_image', 'h_productscat_image'); ?></b></td>
<td class="fillOE"><input type="text" name="prodcat_image" id="prodcat_image" value="<?php echo $prodcat_image; ?>" size="40" maxlength="255" onchange="FJ_show_image2()" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_image_dir', 'h_productscat_imgdir'); ?></b></td>
<td class="fillEE" colspan="2">
<select name="prodcat_image_dir" id="prodcat_image_dir" size="0" onchange="document.form_productcateditor.prodcat_image.value=document.form_productcateditor.prodcat_image_dir.options[document.form_productcateditor.prodcat_image_dir.selectedIndex].value; FJ_show_image ()">
<?php
// read directory for files (only graphics files).
$handle = opendir(K_PATH_IMAGES_PRODUCTS_CATEGORIES);
echo "<option value=\"".K_BLANK_IMAGE."\" selected=\"selected\"> - </option>\n";
	while (false !== ($file = readdir($handle))) {
		$path_parts = pathinfo($file);
		$file_ext = strtolower($path_parts['extension']);
		//check file type (GIF, JPG, PNG)
		if(($file_ext=="png")OR($file_ext=="gif")OR($file_ext=="jpg")OR($file_ext=="jpeg")) {
			echo "<option value=\"".$file."\"";
			if($file == $prodcat_image) {
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
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_upload', 'h_productscat_imgup'); ?></b></td>
<td class="fillOE">
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
			
			echo "<tr class=\"fillE\">";
			echo "<td class=\"fillEO\"><hr /></td>";
			echo "<td class=\"fillEE\"><b>".$m['language_name']."</b></td>";
			echo "</tr>";
			echo "<tr class=\"fillO\">";
			echo "<td class=\"fillOO\" align=\"right\"><b>".F_display_field_name('w_name', 'h_productscat_name')."</b></td>";
			echo "<td class=\"fillOE\"><input type=\"text\" name=\"rc_name[".$m['language_code']."]\" id=\"rc_name_".$m['language_code']."\" value=\"".htmlentities(stripslashes($rc_name[$m['language_code']]), ENT_COMPAT, $doc_charset)."\" size=\"50\" maxlength=\"255\" /></td>";
			echo "</tr>";
			
			echo "<tr class=\"fillE\">";
			echo "<td class=\"fillEO\" align=\"right\" valign=\"top\"><b>".F_display_field_name('w_description', 'h_productscat_description')."</b><br />";
?>
<input type="button" name="htmleditor_<?php echo $m['language_code']; ?>" id="htmleditor_<?php echo $m['language_code']; ?>" value="<?php echo $l['w_button_html_editor']; ?>" onclick="htmlWindow=window.open('cp_edit_html.<?php echo CP_EXT; ?>?templates=0&amp;callingform=form_productcateditor&amp;callingfield=elements['+FJ_get_field_index(this)+']<?php echo $doc_charset_url; ?>','htmlWindow','dependent,height=500,width=800,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')" />
<?php
echo "</td>";
$current_ta_code = $rc_description[$m['language_code']];
$current_ta_code = stripslashes($current_ta_code);
echo "<td class=\"fillEE\"><textarea cols=\"50\" rows=\"5\" name=\"rc_description[".$m['language_code']."]\" id=\"rc_description_".$m['language_code']."\">".htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset)."</textarea></td>";
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
<td class="fillOE"><a href="cp_edit_ec_products.<?php echo CP_EXT; ?>?products_category=<?php echo $prodcat_id; ?>"><b><?php echo $l['t_products_editor']; ?>&nbsp;&gt;&gt;</b></a></td>
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
<input type="hidden" name="prodcat_item" id="prodcat_item" value="<?php echo $prodcat_item; ?>" />
<input type="hidden" name="prodcat_sub_id" id="prodcat_sub_id" value="<?php echo $prodcat_sub_id; ?>" />
<input type="hidden" name="prodcat_position" id="prodcat_position" value="<?php echo $prodcat_position; ?>" />
<?php //show buttons
if ($prodcat_id) {
	F_submit_button("form_productcateditor","menu_mode",$l['w_update']); 
	F_submit_button("form_productcateditor","menu_mode","delete"); 
}
F_submit_button("form_productcateditor","menu_mode",$l['w_clear']); 
?>
</td>

</tr>
</table>
</form>
<!-- ====================================================== -->

<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_productcateditor.prodcat_id.focus();

//get next elements ID
function FJ_get_field_index(what) {
	for (var i=0;i<document.form_productcateditor.elements.length;i++) {
		if(what == document.form_productcateditor.elements[i]) {
			return(i+1);
		}
	}
	return (-1);
}

function FJ_show_image(){
	document.images.imageproductcat.src= "<?php echo K_PATH_IMAGES_PRODUCTS_CATEGORIES; ?>"+document.form_productcateditor.prodcat_image_dir.options[document.form_productcateditor.prodcat_image_dir.selectedIndex].value;
}

function FJ_show_image2(){
	document.images.imageproductcat.src= "<?php echo K_PATH_IMAGES_PRODUCTS_CATEGORIES; ?>"+document.form_productcateditor.prodcat_image.value;
}
//]]>
</script>

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
