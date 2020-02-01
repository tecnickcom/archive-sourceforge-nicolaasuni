<?php
//============================================================+
// File name   : cp_edit_ec_products.php                       
// Begin       : 2002-06-19                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit Products                                 
//                                                             
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Tecnick.com s.r.l.
//               Via Della Pace n. 11
//               09044 Quartucciu (CA)
//               ITALY
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
require_once('../../shared/code/cp_functions_tree.'.CP_EXT);

$thispage_title = $l['t_products_editor'];

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
		<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_delete" id="form_delete">
		<input type="hidden" name="menu_mode" id="menu_mode" value="forcedelete" />
		<input type="hidden" name="product_id" id="product_id" value="<?php echo $product_id; ?>" />
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
			$sql = "DELETE FROM ".K_TABLE_EC_PRODUCTS." WHERE product_id=".$product_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			
			$sql = "DELETE FROM ".K_TABLE_EC_PRODUCTS_RESOURCES." WHERE prodres_product_id=".$product_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			
			$product_id=FALSE;
			
			if(($product_image)AND($product_image != K_BLANK_IMAGE)) { //if exist delete product image
				//create product photo path
				$thisproduct_photo = K_PATH_IMAGES_PRODUCTS.$product_image;
				$thisproduct_photo_small = K_PATH_IMAGES_PRODUCTS."s_".$product_image;
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
		break;
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_EC_PRODUCTS, "product_name='".$product_name."'", "product_id", $product_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			elseif(!F_check_unique(K_TABLE_EC_PRODUCTS, "product_code='".$product_code."'", "product_id", $product_id)) {
				F_print_error("WARNING", $l['m_duplicate_code']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				if($_FILES['userfile']['name']) { //upload file
					$product_image = F_upload_product_image(md5($product_name.$product_code));
				}
				$product_description = addslashes(serialize($r_text));
				$product_date_added = gmdate("Y-m-d H:i:s"); // get the actual date and time
				if ((!empty($product_manufacturer_link)) AND F_is_relative_link($product_manufacturer_link)) {
					$product_manufacturer_link = "http://".$product_manufacturer_link;
				}
				$sql = "UPDATE IGNORE ".K_TABLE_EC_PRODUCTS." SET 
				product_code='".$product_code."',
				product_manufacturer_code='".$product_manufacturer_code."',
				product_barcode='".$product_barcode."',
				product_inventory_code='".$product_inventory_code."',
				product_alternative_codes='".$product_alternative_codes."',
				product_category_id='".$product_category_id."',
				product_manufacturer_id='".$product_manufacturer_id."',
				product_manufacturer_link='".$product_manufacturer_link."',
				product_name='".$product_name."',
				product_description='".$product_description."',
				product_warranty='".$product_warranty."',
				product_warranty_id='".$product_warranty_id."',
				product_image='".$product_image."',
				product_transportable='".$product_transportable."',
				product_download_link='".$product_download_link."',
				product_execute_module='".$product_execute_module."',
				product_weight_per_unit='".$product_weight_per_unit."',
				product_length='".$product_length."',
				product_width='".$product_width."',
				product_height='".$product_height."',
				product_unit_of_measure_id='".$product_unit_of_measure_id."',
				product_cost='".$product_cost."',
				product_tax='".$product_tax."',
				product_tax2='".$product_tax2."',
				product_tax3='".$product_tax3."',
				product_q_sold='".$product_q_sold."',
				product_q_available='".$product_q_available."',
				product_q_arriving='".$product_q_arriving."',
				product_arriving_time='".$product_arriving_time."',
				product_date_added='".$product_date_added."'
				WHERE product_id='".$product_id."'";
				
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
			}
		}
		break;
	}

	
	case unhtmlentities($l['w_copy']):
	case $l['w_copy']: //copy product data to another category
		$old_product_id = $product_id;
		$old_category_id = $product_category_id;
		$product_category_id = $new_category_id;
		
	case unhtmlentities($l['w_add']):
	case $l['w_add']:{ // Add
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			$sql = "SELECT product_id FROM ".K_TABLE_EC_PRODUCTS." WHERE product_name='".$product_name."' AND product_category_id='".$product_category_id."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$sql = "SELECT product_id FROM ".K_TABLE_EC_PRODUCTS." WHERE product_code='".$product_code."' AND product_category_id='".$product_category_id."'";
				if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
					F_print_error("WARNING", $l['m_duplicate_code']);
					$formstatus = FALSE; F_stripslashes_formfields();
				}
				else { //check link validity
					//add item
					if($_FILES['userfile']['name']) { //upload file
						$product_image = F_upload_product_image(md5($product_name.$product_code));
					}
					$product_description = addslashes(serialize($r_text));
					$product_date_added = gmdate("Y-m-d H:i:s"); // get the actual date and time
					if ((!empty($product_manufacturer_link)) AND F_is_relative_link($product_manufacturer_link)) {
						$product_manufacturer_link = "http://".$product_manufacturer_link;
					}
					$sql = "INSERT IGNORE INTO ".K_TABLE_EC_PRODUCTS." (
					product_code,
					product_manufacturer_code,
					product_barcode,
					product_inventory_code,
					product_alternative_codes,
					product_category_id,
					product_manufacturer_id,
					product_manufacturer_link,
					product_name,
					product_description,
					product_warranty,
					product_warranty_id,
					product_image,
					product_transportable,
					product_download_link,
					product_execute_module,
					product_weight_per_unit,
					product_length, 
					product_width, 
					product_height,
					product_unit_of_measure_id,
					product_cost,
					product_tax,
					product_tax2,
					product_tax3,
					product_q_sold,
					product_q_available,
					product_q_arriving,
					product_arriving_time,
					product_date_added
					) VALUES (
					'".$product_code."', 
					'".$product_manufacturer_code."',
					'".$product_barcode."',
					'".$product_inventory_code."',
					'".$product_alternative_codes."',
					'".$product_category_id."', 
					'".$product_manufacturer_id."', 
					'".$product_manufacturer_link."', 
					'".$product_name."', 
					'".$product_description."', 
					'".$product_warranty."', 
					'".$product_warranty_id."', 
					'".$product_image."', 
					'".$product_transportable."', 
					'".$product_download_link."', 
					'".$product_execute_module."',
					'".$product_weight_per_unit."', 
					'".$product_length."', 
					'".$product_width."', 
					'".$product_height."',
					'".$product_unit_of_measure_id."', 
					'".$product_cost."', 
					'".$product_tax."', 
					'".$product_tax2."', 
					'".$product_tax3."', 
					'".$product_q_sold."', 
					'".$product_q_available."', 
					'".$product_q_arriving."', 
					'".$product_arriving_time."',
					'".$product_date_added."'
					)";
					if(!$r = F_aiocpdb_query($sql, $db)) {
						F_display_db_error();
					}
					else {
						$product_id = F_aiocpdb_insert_id();
					}
					
					// ------------------------------------------------------
					//copy products resources
					if (($menu_mode == $l['w_copy']) OR ($menu_mode == unhtmlentities($l['w_copy']))) { 	
						$sql = "SELECT * FROM ".K_TABLE_EC_PRODUCTS_RESOURCES." WHERE prodres_product_id='".$old_product_id."'";
						if($r = F_aiocpdb_query($sql, $db)) {
							while($m = F_aiocpdb_fetch_array($r)) {
								$sqld = "INSERT IGNORE INTO ".K_TABLE_EC_PRODUCTS_RESOURCES." (
								prodres_product_id,
								prodres_name,
								prodres_link,
								prodres_target
								) VALUES (
								'".$product_id."',
								'".$m['prodres_name']."',
								'".$m['prodres_link']."',
								'".$m['prodres_target']."'
								)";
								if(!$rd = F_aiocpdb_query($sqld, $db)) {
									F_display_db_error();
								}
							}
						}
						else {
							F_display_db_error();
						}	
					}
				}
			}
		}
		break;
	}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$product_code = "";
		$product_manufacturer_code = "";
		$product_barcode = "";
		$product_inventory_code = "";
		$product_alternative_codes = "";
		//$product_category_id = "";
		$product_manufacturer_id = "";
		$product_manufacturer_link = "";
		$product_name = "";
		$product_description = "";
		$product_warranty = 0;
		$product_warranty_id = "";
		$product_image = K_BLANK_IMAGE;
		$product_transportable = 1;
		$product_download_link = "";
		$product_execute_module = "";
		$product_weight_per_unit = 0;
		$product_length = 0;
		$product_width = 0;
		$product_height = 0;
		$product_unit_of_measure_id = "";
		$product_cost = "";
		$product_tax = "";
		$product_tax2 = "";
		$product_tax3 = "";
		$product_q_sold = "";
		$product_q_available = "";
		$product_q_arriving = "";
		$product_arriving_time = "";
		$product_date_added = gmdate("Y-m-d H:i:s");
		$r_text = array();
		break;
		}

	default :{ 
		break;
		}

} //end of switch


// Initialize variables
if (!isset($orderby) OR (!$orderby)) {
	$orderby = "product_name";
}

if((!isset($product_category_id) OR (!$product_category_id)) AND (!isset($product_id) OR (!$product_id)) ) {
	$sql = "SELECT * FROM ".K_TABLE_EC_PRODUCTS_CATEGORIES." ORDER BY prodcat_sub_id,prodcat_position LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$product_category_id = $m['prodcat_id'];
		}
		else {
			$product_category_id = false;
		}
	}
	else {
		F_display_db_error();
	}
}

if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if((!isset($product_id) OR (!$product_id)) OR (isset($changecategory) AND $changecategory)) {
			$sql = "SELECT * FROM ".K_TABLE_EC_PRODUCTS." WHERE product_category_id=".$product_category_id." ORDER BY product_name LIMIT 1";
		}
		else {$sql = "SELECT * FROM ".K_TABLE_EC_PRODUCTS." WHERE product_id=".$product_id." LIMIT 1";}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$product_id = $m['product_id'];
				$product_code = $m['product_code'];
				$product_category_id = $m['product_category_id'];
				$product_manufacturer_code = $m['product_manufacturer_code'];
				$product_barcode = $m['product_barcode'];
				$product_inventory_code = $m['product_inventory_code'];
				$product_alternative_codes = $m['product_alternative_codes'];
				$product_manufacturer_id = $m['product_manufacturer_id'];
				$product_manufacturer_link = $m['product_manufacturer_link'];
				$product_name = $m['product_name'];
				$product_description = $m['product_description'];
				$product_warranty = $m['product_warranty'];
				$product_warranty_id = $m['product_warranty_id'];
				$product_image = $m['product_image'];
				$product_transportable = $m['product_transportable'];
				$product_download_link = $m['product_download_link'];
				$product_execute_module = $m['product_execute_module'];
				$product_weight_per_unit = $m['product_weight_per_unit'];
				$product_length = $m['product_length'];
				$product_width = $m['product_width'];
				$product_height = $m['product_height'];
				$product_unit_of_measure_id = $m['product_unit_of_measure_id'];
				$product_cost = $m['product_cost'];
				$product_tax = $m['product_tax'];
				$product_tax2 = $m['product_tax2'];
				$product_tax3 = $m['product_tax3'];
				$product_q_sold = $m['product_q_sold'];
				$product_q_available = $m['product_q_available'];
				$product_q_arriving = $m['product_q_arriving'];
				$product_arriving_time = $m['product_arriving_time'];
				$r_text = unserialize($product_description);
			}
			else {
				$product_code = "";
				//$product_category_id = "";
				$product_manufacturer_code = "";
				$product_barcode = "";
				$product_inventory_code = "";
				$product_alternative_codes = "";
				$product_manufacturer_id = "";
				$product_manufacturer_link = "";
				$product_name = "";
				$product_description = "";
				$product_warranty = 0;
				$product_warranty_id = "";
				$product_image = K_BLANK_IMAGE;
				$product_transportable = 1;
				$product_download_link = "";
				$product_execute_module = "";
				$product_weight_per_unit = 0;
				$product_length = 0;
				$product_width = 0;
				$product_height = 0;
				$product_unit_of_measure_id = "";
				$product_cost = "";
				$product_tax = "";
				$product_tax2 = "";
				$product_tax3 = "";
				$product_q_sold = "";
				$product_q_available = "";
				$product_q_arriving = "";
				$product_arriving_time = "";
				$r_text = array();
			}
		}
		else {
			F_display_db_error();
		}
	}
}

if (!isset($product_date_added)) {
	$product_date_added = "";
}
?>
   
<!-- ====================================================== -->
<script language="JavaScript" src="../ckeditor/ckeditor.js"></script>
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_productseditor" id="form_productseditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="product_code,product_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_code'].",".$l['w_name']; ?>" />

<input type="hidden" name="product_date_added" id="product_date_added" value="<?php echo $product_date_added; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT category ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_category', 'h_productscat_select'); ?></b></td>
<td class="fillOE" colspan="2">
<input type="hidden" name="changecategory" id="changecategory" value="0" />
<select name="product_category_id" id="product_category_id" size="0" onchange="document.form_productseditor.changecategory.value=1; document.form_productseditor.submit()">
<?php
$noscriptlink = $_SERVER['SCRIPT_NAME']."?";
if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {
	$noscriptlink .= "aiocp_dp=".$aiocp_dp."&amp;";
}
$noscriptlink .= "changecategory=1&amp;";
$noscriptlink .= "product_category_id=";
F_form_select_tree($product_category_id, false, K_TABLE_EC_PRODUCTS_CATEGORIES, "prodcat", $noscriptlink); ?>
</td>
</tr>
<!-- END SELECT category ==================== -->

<!-- SELECT  ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_product', 'h_productsed_select'); ?></b></td>
<td class="fillEE" colspan="2">
<select name="product_id" id="product_id" size="0" onchange="document.form_productseditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_EC_PRODUCTS." WHERE product_category_id=".$product_category_id." ORDER BY ".$orderby."";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['product_id']."\"";
		if($m['product_id'] == $product_id) {
			echo " selected=\"selected\"";
		}
		echo ">[".$m['product_id']."] ".htmlentities($m['product_code'], ENT_NOQUOTES, $l['a_meta_charset'])." - ".htmlentities($m['product_name'], ENT_NOQUOTES, $l['a_meta_charset'])."&nbsp;</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT  ==================== -->

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_order_by', ''); ?></b></td>
<td class="fillOE" colspan="2">
<?php
echo "<input type=\"radio\" name=\"orderby\" value=\"product_code\"";
if($orderby == "product_code") {echo " checked=\"checked\"";}
echo "onclick=\"document.form_productseditor.submit()\" />".$l['w_code']."&nbsp;";

echo "<input type=\"radio\" name=\"orderby\" value=\"product_name\"";
if($orderby == "product_name") {echo " checked=\"checked\"";}
echo "onclick=\"document.form_productseditor.submit()\" />".$l['w_name']."&nbsp;";
?>
</td>
</tr>

<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE" colspan="2"><hr /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_name', 'h_productsed_product'); ?></b></td>
<td class="fillOE"><input type="text" name="product_name" id="product_name" value="<?php echo htmlentities($product_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
<?php
$img_rowspan = 28;
if (K_EC_DISPLAY_TAX_2) {$img_rowspan += 1;};
if (K_EC_DISPLAY_TAX_3) {$img_rowspan += 1;};
?>
<td class="fillEE" rowspan="<?php echo $img_rowspan; ?>" align="right" valign="top"><a href="<?php echo htmlentities(urldecode(K_PATH_IMAGES_PRODUCTS."".$product_image)); ?>" target="_blank"><img name="imageproduct" src="<?php echo htmlentities(urldecode(K_PATH_IMAGES_PRODUCTS."s_".$product_image)); ?>" border="0" alt="" height="<?php echo K_PRODUCT_IMAGE_HEIGHT; ?>" width="<?php echo K_PRODUCT_IMAGE_WIDTH; ?>" /></a></td>
</tr>


<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_code', 'h_productsed_code'); ?></b></td>
<td class="fillEE"><input type="text" name="product_code" id="product_code" value="<?php echo htmlentities($product_code, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_barcode', 'h_productsed_barcode'); ?></b></td>
<td class="fillOE"><input type="text" name="product_barcode" id="product_barcode" value="<?php echo htmlentities($product_barcode, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_manufacturer_code', 'h_productsed_manufacturer_code'); ?></b></td>
<td class="fillEE"><input type="text" name="product_manufacturer_code" id="product_manufacturer_code" value="<?php echo htmlentities($product_manufacturer_code, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_inventory_code', 'h_productsed_inventory_code'); ?></b></td>
<td class="fillOE"><input type="text" name="product_inventory_code" id="product_inventory_code" value="<?php echo htmlentities($product_inventory_code, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b><?php echo F_display_field_name('w_alternative_codes', 'h_productsed_alternative_codes'); ?></b></td>
<td class="fillEE"><textarea cols="30" rows="2" name="product_alternative_codes" id="product_alternative_codes"><?php echo htmlentities(stripslashes($product_alternative_codes), ENT_NOQUOTES, $l['a_meta_charset']); ?></textarea></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_warranty_time', 'h_productsed_warranty_time'); ?></b></td>
<td class="fillOE"><input type="text" name="product_warranty" id="product_warranty" value="<?php echo $product_warranty; ?>" size="30" maxlength="255" /> <b>[<?php echo $l['w_months']; ?>]</b></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_warranty', 'h_productsed_warranty'); ?></b></td>
<td class="fillEE">
<select name="product_warranty_id" id="product_warranty_id" size="0">
<option value="">&nbsp;&nbsp;</option>
<?php
$sql = "SELECT * FROM ".K_TABLE_EC_WARRANTIES." WHERE 1 ORDER BY warranty_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['warranty_id']."\"";
		if($m['warranty_id'] == $product_warranty_id) {
			 echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['warranty_name'], ENT_NOQUOTES, $l['a_meta_charset'])."&nbsp;</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_manufacturer', 'h_productsed_manufacturer'); ?></b></td>
<td class="fillOE">
<select name="product_manufacturer_id" id="product_manufacturer_id" size="0">
<option value="">&nbsp;&nbsp;</option>
<?php
$sql = "SELECT * FROM ".K_TABLE_EC_MANUFACTURERS." WHERE 1 ORDER BY manuf_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['manuf_id']."\"";
		if($m['manuf_id'] == $product_manufacturer_id) {
			 echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['manuf_name'], ENT_NOQUOTES, $l['a_meta_charset'])."&nbsp;</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_link', 'h_productsed_manufacturer_link'); ?></b></td>
<td class="fillEE"><input type="text" name="product_manufacturer_link" id="product_manufacturer_link" value="<?php echo htmlentities($product_manufacturer_link, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<!-- SELECT  ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_unit', 'h_productsed_unit'); ?></b></td>
<td class="fillOE">
<select name="product_unit_of_measure_id" id="product_unit_of_measure_id" size="0">
<option value="">&nbsp;&nbsp;</option>
<?php
$sql = "SELECT * FROM ".K_TABLE_EC_UNITS_OF_MEASURE." WHERE 1 ORDER BY unit_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['unit_id']."\"";
		if($m['unit_id'] == $product_unit_of_measure_id) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['unit_name'], ENT_NOQUOTES, $l['a_meta_charset'])."&nbsp;</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT  ==================== -->

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_transportable', 'h_productsed_transportable'); ?></b></td>
<td class="fillEE">
<?php
echo "<input type=\"checkbox\" name=\"product_transportable\" id=\"product_transportable\" value=\"1\"";
if ($product_transportable) { echo "checked";}
echo" />";
?>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_downloadable_file', 'h_downloadable_file'); ?></b></td>
<td class="fillOE">
<select name="product_download_link" id="product_download_link" size="0">
<?php
// read directory for files	define
$handle = opendir(K_PATH_FILES_DOWNLOADABLES);
echo "<option value=\"\">&nbsp;&nbsp;</option>\n";
	while (false !== ($file = readdir($handle))) {
		if(($file != ".")AND($file != "..")) {
			echo "<option value=\"".$file."\"";
			if($file == $product_download_link) {
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

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_module', 'h_productsed_select_module'); ?></b></td>
<td class="fillEE">
<select name="module_selector" id="module_selector" size="0" onchange="document.form_productseditor.product_execute_module.value=document.form_productseditor.module_selector.options[document.form_productseditor.module_selector.selectedIndex].value;">
<option value="">&nbsp;</option>
<?php
$sql = "SELECT * FROM ".K_TABLE_PAGE_MODULES." ORDER BY pagemod_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			$template_symbol = "#".strtoupper($m['pagemod_name'])."";
			if($m['pagemod_params']) {
				$template_symbol .= "=";
				for ($i=1; $i<=$m['pagemod_params']; $i++) {
					$template_symbol .= "0,";
				}
				$template_symbol = substr($template_symbol, 0, -1); //remove trailing comma
			}
			$template_symbol .= "#";
			
			echo "<option value=\"".$template_symbol."\">".htmlentities($m['pagemod_name'], ENT_NOQUOTES, $l['a_meta_charset'])."&nbsp;</option>\n";
		}
	}
	else {
		F_display_db_error();
	}
?>
</select>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_module', 'h_productsed_execute_module'); ?></b></td>
<td class="fillOE"><input type="text" name="product_execute_module" id="product_execute_module" value="<?php echo htmlentities($product_execute_module, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_packaged_weight', 'h_productsed_weight'); ?></b></td>
<td class="fillEE"><input type="text" name="product_weight_per_unit" id="product_weight_per_unit" value="<?php echo $product_weight_per_unit; ?>" size="30" maxlength="255" /> <b>[Kg]</b></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_length', 'h_productsed_length'); ?></b></td>
<td class="fillOE"><input type="text" name="product_length" id="product_length" value="<?php echo $product_length; ?>" size="30" maxlength="255" /> <b>[m]</b></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_width', 'h_productsed_width'); ?></b></td>
<td class="fillEE"><input type="text" name="product_width" id="product_width" value="<?php echo $product_width; ?>" size="30" maxlength="255" /> <b>[m]</b></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_height', 'h_productsed_height'); ?></b></td>
<td class="fillOE"><input type="text" name="product_height" id="product_height" value="<?php echo $product_height; ?>" size="30" maxlength="255" /> <b>[m]</b></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_cost_per_unit', 'h_productsed_cost'); ?></b></td>
<td class="fillEE"><input type="text" name="product_cost" id="product_cost" value="<?php echo $product_cost; ?>" size="30" maxlength="255" /> <b>[<?php echo K_MONEY_CURRENCY_UNICODE_SYMBOL; ?>]</b></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_ec_tax', 'h_vat_select'); ?></b></td>
<td class="fillOE">
<select name="product_tax" id="product_tax" size="0">
<?php
$sql = "SELECT * FROM ".K_TABLE_EC_VAT." WHERE 1 ORDER BY vat_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['vat_id']."\"";
		if($m['vat_id'] == $product_tax) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['vat_name'], ENT_NOQUOTES, $l['a_meta_charset'])."&nbsp;</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
<?php
if (!K_EC_DISPLAY_TAX_2) {
	echo "<input type=\"hidden\" name=\"product_tax2\" id=\"product_tax2\" value=\"0\" />";
}
if (!K_EC_DISPLAY_TAX_3) {
	echo "<input type=\"hidden\" name=\"product_tax3\" id=\"product_tax3\" value=\"0\" />";
}
?>
</td>
</tr>
<?php
if (K_EC_DISPLAY_TAX_2) {
?>
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_ec_tax2', 'h_vat2_select'); ?></b></td>
<td class="fillEE">
<select name="product_tax2" id="product_tax2" size="0">
<option value="0">&nbsp;&nbsp;</option>
<?php
$sql = "SELECT * FROM ".K_TABLE_EC_VAT." WHERE 1 ORDER BY vat_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['vat_id']."\"";
		if($m['vat_id'] == $product_tax2) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['vat_name'], ENT_NOQUOTES, $l['a_meta_charset'])."&nbsp;</option>\n";
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
}
if (K_EC_DISPLAY_TAX_3) {
?>
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_ec_tax3', 'h_vat3_select'); ?></b></td>
<td class="fillOE">
<select name="product_tax3" id="product_tax3" size="0">
<option value="0">&nbsp;&nbsp;</option>
<?php
$sql = "SELECT * FROM ".K_TABLE_EC_VAT." WHERE 1 ORDER BY vat_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['vat_id']."\"";
		if($m['vat_id'] == $product_tax3) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['vat_name'], ENT_NOQUOTES, $l['a_meta_charset'])."&nbsp;</option>\n";
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
}
?>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_sold_quantity', 'h_productsed_sold_quantity'); ?></b></td>
<td class="fillEE"><input type="text" name="product_q_sold" id="product_q_sold" value="<?php echo $product_q_sold; ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_quantity', 'h_productsed_quantity'); ?></b></td>
<td class="fillOE"><input type="text" name="product_q_available" id="product_q_available" value="<?php echo $product_q_available; ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_arriving_quantity', 'h_productsed_arriving_quantity'); ?></b></td>
<td class="fillEE"><input type="text" name="product_q_arriving" id="product_q_arriving" value="<?php echo $product_q_arriving; ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_arriving_time', 'h_productsed_arriving_time'); ?></b></td>
<td class="fillOE"><input type="text" name="product_arriving_time" id="product_arriving_time" value="<?php echo $product_arriving_time; ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_image', 'h_productsed_image'); ?></b></td>
<td class="fillEE"><input type="text" name="product_image" id="product_image" value="<?php echo htmlentities($product_image, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" onchange="FJ_show_image2()" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_image_dir', 'h_productsed_imagedir'); ?></b></td>
<td class="fillOE">
<select name="product_image_dir" id="product_image_dir" size="0" onchange="document.form_productseditor.product_image.value=document.form_productseditor.product_image_dir.options[document.form_productseditor.product_image_dir.selectedIndex].value; FJ_show_image ()">
<?php
// read directory for files (only graphics files).
$handle = opendir(K_PATH_IMAGES_PRODUCTS);
echo "<option value=\"".K_BLANK_IMAGE."\" selected=\"selected\"> - &nbsp;</option>\n";
	while (false !== ($file = readdir($handle))) {
		$path_parts = pathinfo($file);
		$file_ext = strtolower($path_parts['extension']);
		//check file type (GIF, JPG, PNG)
		if(($file_ext=="png")OR($file_ext=="gif")OR($file_ext=="jpg")OR($file_ext=="jpeg")) {
			echo "<option value=\"".$file."\"";
			if($file == $product_image) {
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
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_upload', 'h_productsed_imageup'); ?></b></td>
<td class="fillEE">
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo K_MAX_UPLOAD_SIZE; ?>" />
<input type="file" name="userfile" id="userfile" size="20" />
</td>
</tr>
<!-- END Upload file ==================== -->


<!-- iterate for each language ==================== -->
<?php
	$ckeditor_data = '';
	$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<tr class=\"fillO\">";
			echo "<td class=\"fillOO\"><hr /></td>";
			echo "<td class=\"fillOE\" colspan=\"2\"><b>".$m['language_name']."</b></td>";
			echo "</tr>";
			echo "<tr class=\"fillE\">";
			echo "<td class=\"fillEO\" align=\"right\" valign=\"top\"><b>".F_display_field_name('w_description', 'h_productsed_description')."</b><br />";
			
			$doc_charset = F_word_language($m['language_code'], "a_meta_charset");
			if ($doc_charset) {
				$doc_charset_url = "&amp;charset=".$doc_charset;
			}
			else {
				$doc_charset_url = "";
			}
?>
<input type="button" name="htmleditor_<?php echo $m['language_code']; ?>" id="htmleditor_<?php echo $m['language_code']; ?>" value="<?php echo $l['w_button_html_editor']; ?>" onclick="htmlWindow=window.open('cp_edit_html.<?php echo CP_EXT; ?>?templates=0&amp;callingform=form_productseditor&amp;callingfield=elements['+FJ_get_field_index(this)+']<?php echo $doc_charset_url; ?>','htmlWindow','dependent,height=500,width=800,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')" />
<?php
echo "</td>";
if (isset($r_text[$m['language_code']])) {
	$current_ta_code = $r_text[$m['language_code']];
} else {
	$current_ta_code = "";
}

$current_ta_code = stripslashes($current_ta_code);
echo "<td class=\"fillEE\" colspan=\"2\"><textarea cols=\"50\" rows=\"5\" name=\"r_text[".$m['language_code']."]\" id=\"r_text_".$m['language_code']."\">".htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset)."</textarea></td>";
echo "</tr>";

$ckeditor_data .= "CKEDITOR.replace('r_text_".$m['language_code']."', {language: '".$l['a_meta_language']."', filebrowserBrowseUrl: '../ckeditor/filemanager/index.html'});\n";

		}
	}
	else {
		F_display_db_error();
	}
?>
<!-- END iterate for each language ==================== -->

<tr class="fillO">
<td class="fillOO" align="right">&nbsp;</td>
<td class="fillOE">
<?php
if (isset($product_id) AND ($product_id > 0)) {
	echo "<a href=\"cp_edit_ec_product_resources.".CP_EXT."?prodres_product_id=".$product_id."\"><b>".$l['t_ec_documents_products_editor']."&nbsp;&gt;&gt;</b></a>";
}
else {
	echo "&nbsp;";
}
?>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right">&nbsp;</td>
<td class="fillEE" colspan="2"><a href="cp_edit_ec_products_categories.<?php echo CP_EXT; ?>?prodcat_id=<?php echo $product_category_id; ?>"><b>&lt;&lt;&nbsp;<?php echo $l['t_product_categories_editor']; ?></b></a></td>
</tr>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if (isset($product_id) AND ($product_id > 0)) {	
	//copy to another category option
	echo "<b>".F_display_field_name('w_copy_data_to', 'h_ecdoc_copy_data_to')."</b>";
	echo "<select name=\"new_category_id\" id=\"new_category_id\" size=\"0\">";
	
	$noscriptlink = $_SERVER['SCRIPT_NAME']."?";
	if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {
		$noscriptlink .= "aiocp_dp=".$aiocp_dp."&amp;";
	}
	$noscriptlink .= "new_category_id=";
	F_form_select_tree($product_category_id, false, K_TABLE_EC_PRODUCTS_CATEGORIES, "prodcat", $noscriptlink);
	
	F_submit_button("form_productseditor","menu_mode",$l['w_copy']); 
	echo "<br /><br />\n";

	F_submit_button("form_productseditor","menu_mode",$l['w_update']); 
	F_submit_button("form_productseditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_productseditor","menu_mode",$l['w_add']); 
F_submit_button("form_productseditor","menu_mode",$l['w_clear']); 
?>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">

<?php
if (isset($product_id) AND ($product_id > 0)) {
	
	//$_SESSION['session_user_level']
	
	$wherequery = "WHERE (product_id='".$product_id."')";
	//generate a verification code to avoid unauthorized calls to PDF viewer
	$verifycode = F_generate_verification_code($wherequery, 4);
	F_generic_button("pdflabel", $l['w_label'], "PDFLAB=window.open('cp_show_ec_pdf_product_label.".CP_EXT."?wherequery=".urlencode($wherequery)."&amp;vc=".$verifycode."&amp;selected_language=".$selected_language."','PDFLAB','menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')");
	
	//generate a verification code to avoid unauthorized calls to PDF viewer
	$verifycode = F_generate_verification_code($product_id, 4);
	F_generic_button("pdfreport", $l['w_report'], "PDFREP=window.open('cp_show_ec_pdf_product_report.".CP_EXT."?product_id=".$product_id."&amp;user_id=".$_SESSION['session_user_id']."&amp;vc=".$verifycode."&amp;selected_language=".$selected_language."','PDFREP','menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')");
}
?>
</td>
</tr>

</table>
</form>
<!-- ====================================================== -->

<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_productseditor.product_id.focus();

//get next elements ID
function FJ_get_field_index(what) {
	for (var i=0;i<document.form_productseditor.elements.length;i++) {
		if(what == document.form_productseditor.elements[i]) {
			return(i+1);
		}
	}
	return (-1);
}

function FJ_show_image(){
	document.images.imageproduct.src= "<?php echo K_PATH_IMAGES_PRODUCTS; ?>"+document.form_productseditor.product_image_dir.options[document.form_productseditor.product_image_dir.selectedIndex].value;
}

function FJ_show_image2(){
	document.images.imageproduct.src= "<?php echo K_PATH_IMAGES_PRODUCTS; ?>"+document.form_productseditor.product_image.value;
}

CKEDITOR.config.contentsCss = '<?php echo K_PATH_AIOCP; ?>public/styles/default.css';
<?php echo $ckeditor_data; ?>
//]]>
</script>

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);


//------------------------------------------------------------
// Uploads image file to the server 
// create resized thumbnail image to configured values
// change image format to JPEG
//------------------------------------------------------------
function F_upload_product_image($imagename) {
	global $l;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	$src_file = $_FILES['userfile']['tmp_name'];
	$src_path_parts = pathinfo($_FILES['userfile']['name']);
	$src_ext = strtolower($src_path_parts['extension']);
	
	$dst_file = $imagename.".jpg";
	$dst_path = K_PATH_IMAGES_PRODUCTS.$dst_file;
	$dst_small_path = K_PATH_IMAGES_PRODUCTS."s_".$dst_file; //full path of miniature file

	// load an image
	$i = new Imagick($src_file);
	
	$i->setImageFormat('jpeg');
	$i->setImageCompressionQuality(80);
	// save the image with original size
	$i->writeImage($dst_path);
	
	// thumbnail the image
	$i->ThumbnailImage(K_PRODUCT_IMAGE_WIDTH, K_PRODUCT_IMAGE_HEIGHT, true);
	// get the current image dimensions
	$geo = $i->getImageGeometry();
	
	if ($geo['width'] != $geo['height']) {
		if ($geo['width'] < $geo['height']) {
			$ix = round((K_PRODUCT_IMAGE_WIDTH - $geo['width']) / 2);
			$iy = 0;
		} else {
			$ix = 0;
			$iy = round((K_PRODUCT_IMAGE_HEIGHT - $geo['height']) / 2);;
		}
		$i->setBackgroundColor(new ImagickPixel('white'));
		$i->extentImage(K_PRODUCT_IMAGE_WIDTH, K_PRODUCT_IMAGE_HEIGHT, $ix, $iy);
	}

	// save the thumbnail image
	$i->writeImage($dst_small_path);

	return $dst_file;
}

//============================================================+
// END OF FILE                                                 
//============================================================+
