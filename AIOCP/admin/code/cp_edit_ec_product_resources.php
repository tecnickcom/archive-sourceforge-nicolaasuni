<?php
//============================================================+
// File name   : cp_edit_ec_product_resources.php              
// Begin       : 2002-10-10                                    
// Last Update : 2005-07-04                                    
//                                                             
// Description : Edit Product's resources                      
//               (updates, manuals, ...)                       
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_EC_PRODUCTS;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

require_once('../../shared/code/cp_functions_form.'.CP_EXT);
require_once('../code/cp_functions_upload.'.CP_EXT);

$thispage_title = $l['t_ec_documents_products_editor'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages; ?>
<!-- ====================================================== -->
<?php

switch($menu_mode) {

	case unhtmlentities($l['w_delete']):
	case $l['w_delete']:{
		F_stripslashes_formfields(); // Delete
		$sql = "DELETE FROM ".K_TABLE_EC_PRODUCTS_RESOURCES." WHERE prodres_id=".$prodres_id."";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		$prodres_id=FALSE;
		break;
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_EC_PRODUCTS_RESOURCES, "prodres_product_id='".$prodres_product_id."' AND prodres_name ='".$prodres_name."'" , "prodres_id", $prodres_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				if($_FILES['userfile']['name']) { //upload file
					$prodres_link = K_PATH_FILES_ATTACHMENTS.F_upload_file("userfile", K_PATH_FILES_ATTACHMENTS);
				}
				
				$sql = "UPDATE IGNORE ".K_TABLE_EC_PRODUCTS_RESOURCES." SET 
				prodres_product_id='".$prodres_product_id."',
				prodres_name='".$prodres_name."',
				prodres_link='".$prodres_link."',
				prodres_target='".$prodres_target."'
				WHERE prodres_id=".$prodres_id."";
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
			//check if prodres_name is unique
			$sql = "SELECT prodres_id FROM ".K_TABLE_EC_PRODUCTS_RESOURCES." WHERE prodres_product_id='".$prodres_product_id."' AND prodres_name='".$prodres_name."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				if($_FILES['userfile']['name']) { //upload file
					$prodres_link = K_PATH_FILES_ATTACHMENTS.F_upload_file("userfile", K_PATH_FILES_ATTACHMENTS);
				}
				$sql = "INSERT IGNORE INTO ".K_TABLE_EC_PRODUCTS_RESOURCES." (
				prodres_product_id,
				prodres_name,
				prodres_link,
				prodres_target
				) VALUES (
				'".$prodres_product_id."',
				'".$prodres_name."',
				'".$prodres_link."',
				'".$prodres_target."'
				)";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$prodres_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
	}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$prodres_name = "";
		$prodres_link = "";
		$prodres_target = 1;
		break;
	}

	default :{ 
		break;
	}

} //end of switch

// Initialize variables

if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!$prodres_id) {
			$sql = "SELECT * FROM ".K_TABLE_EC_PRODUCTS_RESOURCES." WHERE prodres_product_id='".$prodres_product_id."' ORDER BY prodres_name LIMIT 1";
		}
		else {
			$sql = "SELECT * FROM ".K_TABLE_EC_PRODUCTS_RESOURCES." WHERE prodres_id=".$prodres_id." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$prodres_id = $m['prodres_id'];
				$prodres_product_id = $m['prodres_product_id'];
				$prodres_name = $m['prodres_name'];
				$prodres_link = $m['prodres_link'];
				$prodres_target = $m['prodres_target'];
			}
			else {
				$prodres_name = "";
				$prodres_link = "";
				$prodres_target = 1;
			}
		}
		else {
			F_display_db_error();
		}
	}
}

?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_productresources" id="form_productresources">

<input type="hidden" name="prodres_product_id" id="prodres_product_id" value="<?php echo $prodres_product_id; ?>" />

<!-- comma separated list of required fields -->
<!-- <input type="hidden" name="ff_required" id="ff_required" value="prodres_name,prodres_link" /> -->

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<th class="edge">
<?php
$sql = "SELECT * FROM ".K_TABLE_EC_PRODUCTS." WHERE product_id=".$prodres_product_id." LIMIT 1";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "".htmlentities($m['product_code'], ENT_NOQUOTES, $l['a_meta_charset'])." - ".htmlentities($m['product_name'], ENT_NOQUOTES, $l['a_meta_charset'])."";
	}
}
else {
	F_display_db_error();
}
?>
</th>
</tr>

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_resource', 'h_prodres_select'); ?></b></td>
<td class="fillEE">
<select name="prodres_id" id="prodres_id" size="0" onchange="document.form_productresources.submit()">
<?php
$i=1;
$sql = "SELECT * FROM ".K_TABLE_EC_PRODUCTS_RESOURCES." WHERE prodres_product_id='".$prodres_product_id."' ORDER BY prodres_name";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['prodres_id']."\"";
		if($m['prodres_id'] == $prodres_id) {
			echo " selected=\"selected\"";
		}
		echo ">".$i++.") ".htmlentities($m['prodres_name'], ENT_NOQUOTES, $l['a_meta_charset'])."&nbsp;</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT ==================== -->

<tr class="fillO">
<td class="fillOO">&nbsp;</td>
<td class="fillOE"><hr /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_name', 'h_productsed_name'); ?></b></td>
<td class="fillEE"><input type="text" name="prodres_name" id="prodres_name" value="<?php echo htmlentities($prodres_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_link', 'h_productsed_link'); ?></b></td>
<td class="fillOE"><input type="text" name="prodres_link" id="prodres_link" value="<?php echo htmlentities($prodres_link, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_directory', 'h_productsed_directory'); ?></b></td>
<td class="fillEE" colspan="2">
<select name="prodres_link_dir" id="prodres_link_dir" size="0" onchange="document.form_productresources.prodres_link.value='<?php echo K_PATH_FILES_ATTACHMENTS; ?>'+document.form_productresources.prodres_link_dir.options[document.form_productresources.prodres_link_dir.selectedIndex].value;">
<?php
// read download directory for files.
$handle = opendir(K_PATH_FILES_ATTACHMENTS);
echo "<option value=\"\" selected=\"selected\"> - &nbsp;</option>\n";
	while (false !== ($file = readdir($handle))) {
		if ( ($file != ".") AND ($file != "..") ) {
			echo "<option value=\"".$file."\"";
			if($file == basename($prodres_link)) {
				echo " selected=\"selected\"";
			}
			echo ">".$file."&nbsp;</option>\n";
		}
	}
closedir($handle);
?>
</select>
</td>
</tr>

<!-- Upload file ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_upload', 'h_productsed_upload'); ?></b></td>
<td class="fillOE" colspan="2">
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo K_MAX_UPLOAD_SIZE; ?>" />
<input type="file" name="userfile" id="userfile" size="20" />
</td>
</tr>
<!-- END Upload file ==================== -->

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_target', 'h_menued_target'); ?></b></td>
<td class="fillEE" colspan="2"><select name="prodres_target" id="prodres_target" size="0">
<?php
$sql = "SELECT * FROM ".K_TABLE_FRAME_TARGETS." ORDER BY target_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['target_id']."\"";
		if($m['target_id'] == $prodres_target) {
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

<tr class="fillO">
<td class="fillOO" align="right">&nbsp;</td>
<td class="fillOE"><a href="cp_edit_ec_products.<?php echo CP_EXT; ?>?product_id=<?php echo $prodres_product_id; ?>"><b>&lt;&lt;&nbsp;<?php echo $l['t_products_editor']; ?></b></a></td>
</tr>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">


<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if ($prodres_id) {
	F_submit_button("form_productresources","menu_mode",$l['w_update']); 
	F_submit_button("form_productresources","menu_mode",$l['w_delete']); 
}
F_submit_button("form_productresources","menu_mode",$l['w_add']); 
F_submit_button("form_productresources","menu_mode",$l['w_clear']); 
?>
</td>

</tr>
</table>

</form>
<!-- ====================================================== -->

<!-- Cange focus to prodres_id select -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_productresources.prodres_id.focus();
//]]>
</script>
<!-- END Cange focus to prodres_id select -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
