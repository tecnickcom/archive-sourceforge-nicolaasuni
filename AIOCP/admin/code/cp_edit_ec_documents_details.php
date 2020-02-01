<?php
//============================================================+
// File name   : cp_edit_ec_documents_details.php              
// Begin       : 2002-07-02                                    
// Last Update : 2004-07-13                                    
//                                                             
// Description : Edit Business Documents details               
//               (products data)                               
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_EC_DOCUMENTS_DETAILS;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

require_once('../../shared/code/cp_functions_form.'.CP_EXT);
require_once('../../shared/code/cp_functions_tree.'.CP_EXT);
require_once('../../shared/code/cp_functions_ec_documents.'.CP_EXT);
require_once('../../shared/code/cp_functions_ec_vat.'.CP_EXT);

$thispage_title = $l['t_ec_documents_details_editor'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages; ?>
<!-- ====================================================== -->
<?php

//initialize variables
if (!isset($docdet_tax2)) {
	$docdet_tax2 = NULL;
}
if (!isset($docdet_tax3)) {
	$docdet_tax3 = NULL;
}
				
//read document type
if (isset($docdet_doc_id) AND $docdet_doc_id) {
	$sql = "SELECT ecdoc_type,ecdoc_from_doc_id,ecdoc_user_id FROM ".K_TABLE_EC_DOCUMENTS." WHERE ecdoc_id=".$docdet_doc_id." LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$ecdoc_type = $m['ecdoc_type'];
			$ecdoc_from_doc_id = $m['ecdoc_from_doc_id'];
			$ecdoc_user_id = $m['ecdoc_user_id'];
		}
	}
	else {
		F_display_db_error();
	}
}

switch($menu_mode) {

	case unhtmlentities($l['w_delete']):
	case $l['w_delete']:{
		F_stripslashes_formfields(); // Delete
		// restore product quantity ------
		if ( ($ecdoc_type == K_EC_ORDER_DOC_ID) OR (($ecdoc_type == K_EC_INVOICE_DOC_ID) AND (!$ecdoc_from_doc_id)) ) {
			// restore product available quantity
			$sqlup = "UPDATE IGNORE ".K_TABLE_EC_PRODUCTS." SET 
			product_q_available=product_q_available+'".$old_docdet_quantity."'
			WHERE product_id='".$docdet_product_id."' AND product_q_available IS NOT NULL";
			if(!$rup = F_aiocpdb_query($sqlup, $db)) {
				F_display_db_error();
			}
		}
		if ($ecdoc_type == K_EC_INVOICE_DOC_ID) {
			// restore product sold quantity
			$sqlup = "UPDATE IGNORE ".K_TABLE_EC_PRODUCTS." SET 
			product_q_sold=product_q_sold-'".$old_docdet_quantity."'
			WHERE product_id='".$docdet_product_id."'";
			if(!$rup = F_aiocpdb_query($sqlup, $db)) {
				F_display_db_error();
			}
		}
		// end restore product quantity ------
		
		$sql = "DELETE FROM ".K_TABLE_EC_DOCUMENTS_DETAILS." WHERE docdet_id=".$docdet_id."";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		$docdet_id=FALSE;
		break;
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_EC_DOCUMENTS_DETAILS, "docdet_doc_id='".$docdet_doc_id."' AND docdet_name ='".$docdet_name."'" , "docdet_id", $docdet_id)) {
				F_print_error("WARNING", $l['m_duplicate_data']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$docdet_description = addslashes(serialize($r_text));
				$sql = "UPDATE IGNORE ".K_TABLE_EC_DOCUMENTS_DETAILS." SET 
				docdet_doc_id='".$docdet_doc_id."',
				docdet_product_id='".$docdet_product_id."',
				docdet_code='".$docdet_code."',
				docdet_manufacturer_code='".$docdet_manufacturer_code."',
				docdet_barcode='".$docdet_barcode."',
				docdet_inventory_code='".$docdet_inventory_code."',
				docdet_alternative_codes='".$docdet_alternative_codes."',
				docdet_serial_numbers='".$docdet_serial_numbers."',
				docdet_category_id='".$docdet_category_id."',
				docdet_manufacturer_id='".$docdet_manufacturer_id."',
				docdet_manufacturer_link='".$docdet_manufacturer_link."',
				docdet_name='".$docdet_name."',
				docdet_description='".$docdet_description."',
				docdet_warranty = '".$docdet_warranty."',
				docdet_warranty_id = '".$docdet_warranty_id."',
				docdet_image='".$docdet_image."',
				docdet_transportable='".$docdet_transportable."',
				docdet_download_link='".$docdet_download_link."',
				docdet_weight_per_unit='".$docdet_weight_per_unit."',
				docdet_length='".$docdet_length."',
				docdet_width='".$docdet_width."',
				docdet_height='".$docdet_height."',
				docdet_unit_of_measure_id='".$docdet_unit_of_measure_id."',
				docdet_cost='".$docdet_cost."',
				docdet_tax='".$docdet_tax."',
				docdet_tax2='".$docdet_tax2."',
				docdet_tax3='".$docdet_tax3."',
				docdet_quantity='".$docdet_quantity."',
				docdet_discount='".$docdet_discount."'
				WHERE docdet_id=".$docdet_id."";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				
				$changed_quantity = $docdet_quantity - $old_docdet_quantity;
			}
		}
		break;
	}

	case unhtmlentities($l['w_add']):
	case $l['w_add']:{ // Add
		if($formstatus = F_check_form_fields()) {
			//check if docdet_name is unique
			$sql = "SELECT docdet_number FROM ".K_TABLE_EC_DOCUMENTS_DETAILS." WHERE docdet_doc_id='".$docdet_doc_id."' AND docdet_name='".$docdet_name."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_data']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				$docdet_description = addslashes(serialize($r_text));
				$sql = "INSERT IGNORE INTO ".K_TABLE_EC_DOCUMENTS_DETAILS." (
				docdet_doc_id,
				docdet_product_id,
				docdet_code,
				docdet_manufacturer_code,
				docdet_barcode,
				docdet_inventory_code,
				docdet_alternative_codes,
				docdet_serial_numbers,
				docdet_category_id,
				docdet_manufacturer_id,
				docdet_manufacturer_link,
				docdet_name,
				docdet_description,
				docdet_warranty,
				docdet_warranty_id,
				docdet_image,
				docdet_transportable,
				docdet_download_link,
				docdet_weight_per_unit,
				docdet_length,
				docdet_width,
				docdet_height,
				docdet_unit_of_measure_id,
				docdet_cost,
				docdet_tax,
				docdet_tax2,
				docdet_tax3,
				docdet_quantity,
				docdet_discount
				) VALUES (
				'".$docdet_doc_id."',
				'".$docdet_product_id."',
				'".$docdet_code."',
				'".$docdet_manufacturer_code."',
				'".$docdet_barcode."',
				'".$docdet_inventory_code."',
				'".$docdet_alternative_codes."',
				'".$docdet_serial_numbers."',
				'".$docdet_category_id."',
				'".$docdet_manufacturer_id."',
				'".$docdet_manufacturer_link."',
				'".$docdet_name."',
				'".$docdet_description."',
				'".$docdet_warranty."',
				'".$docdet_warranty_id."',
				'".$docdet_image."',
				'".$docdet_transportable."',
				'".$docdet_download_link."',
				'".$docdet_weight_per_unit."',
				'".$docdet_length."',
				'".$docdet_width."',
				'".$docdet_height."',
				'".$docdet_unit_of_measure_id."',
				'".$docdet_cost."',
				'".$docdet_tax."',
				'".$docdet_tax2."',
				'".$docdet_tax3."',
				'".$docdet_quantity."',
				'".$docdet_discount."'
				)";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$docdet_id = F_aiocpdb_insert_id();
				}
				$changed_quantity = $docdet_quantity;
			}
		}
		break;
	}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		//$docdet_doc_id = "";
		$docdet_product_id = "";
		$docdet_code = "";
		$docdet_manufacturer_code = "";
		$docdet_barcode = "";
		$docdet_inventory_code = "";
		$docdet_alternative_codes = "";
		$docdet_serial_numbers = "";
		$docdet_category_id = "";
		$docdet_manufacturer_id = "";
		$docdet_manufacturer_link = "";
		$docdet_name = "";
		$docdet_description = "";
		$docdet_warranty = "";
		$docdet_warranty_id = "";
		$docdet_image = "";
		$docdet_transportable = 1;
		$docdet_download_link = "";
		$docdet_weight_per_unit = "";
		$docdet_length = "";
		$docdet_width = "";
		$docdet_height = "";
		$docdet_unit_of_measure_id = "";
		$docdet_cost = "";
		$docdet_tax = 0;
		$docdet_tax2 = NULL;
		$docdet_tax3 = NULL;
		$docdet_quantity = 1;
		$docdet_discount = 0;
		$r_text = array();
		break;
	}

	default :{ 
		break;
	}

} //end of switch

// restore product quantity ------
if (($menu_mode == $l['w_add']) OR ($menu_mode == unhtmlentities($l['w_add'])) OR ($menu_mode == $l['w_update']) OR ($menu_mode == unhtmlentities($l['w_update'])) ){
	
	if ($changed_quantity >= 0) {
		$available_sign = "-";
		$sold_sign = "+";
	}
	else {
		$available_sign = "+";
		$sold_sign = "-";
	}
	$changed_quantity = abs($changed_quantity);
	
	if ($changed_quantity >= 0) {
		if ( ($ecdoc_type == K_EC_ORDER_DOC_ID) OR (($ecdoc_type == K_EC_INVOICE_DOC_ID) AND (!$ecdoc_from_doc_id)) ) {
			// restore product available quantity
			$sqlup = "UPDATE IGNORE ".K_TABLE_EC_PRODUCTS." SET 
			product_q_available=product_q_available".$available_sign."'".$changed_quantity."'
			WHERE product_id='".$docdet_product_id."' AND product_q_available IS NOT NULL";
			if(!$rup = F_aiocpdb_query($sqlup, $db)) {
				F_display_db_error();
			}
		}
		if ($ecdoc_type == K_EC_INVOICE_DOC_ID) {
			// restore product sold quantity
			$sqlup = "UPDATE IGNORE ".K_TABLE_EC_PRODUCTS." SET 
			product_q_sold=product_q_sold".$sold_sign."'".$changed_quantity."'
			WHERE product_id='".$docdet_product_id."'";
			if(!$rup = F_aiocpdb_query($sqlup, $db)) {
				F_display_db_error();
			}
		}
	}
}
// end restore product quantity ------

// Initialize variables
if (!isset($orderby)) {
	$orderby = "product_name";
}

if(!$product_category_id) {
	if (!$docdet_product_id) {
		$sql = "SELECT * FROM ".K_TABLE_EC_PRODUCTS_CATEGORIES." ORDER BY prodcat_name LIMIT 1";
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
		$docdet_product_id = false;
	}
}

//get data from products table
if ( (isset($changeproduct) AND $changeproduct) OR (isset($changecategory) AND $changecategory) ) {
	if((isset($changecategory) AND $changecategory) OR (!isset($docdet_product_id) OR (!$docdet_product_id))) {
		$docdet_product_id = false;
		$sql = "SELECT * FROM ".K_TABLE_EC_PRODUCTS." WHERE product_category_id=".$product_category_id." ORDER BY product_name LIMIT 1";
	}
	else {
		$sql = "SELECT * FROM ".K_TABLE_EC_PRODUCTS." WHERE product_id=".$docdet_product_id." LIMIT 1";
	}
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$docdet_product_id = $m['product_id'];
			$docdet_code = $m['product_code'];
			$docdet_manufacturer_code = $m['product_manufacturer_code'];
			$docdet_barcode = $m['product_barcode'];
			$docdet_inventory_code = $m['product_inventory_code'];
			$docdet_alternative_codes = $m['product_alternative_codes'];
			$docdet_serial_numbers = $m['docdet_serial_numbers'];
			$docdet_manufacturer_id = $m['product_manufacturer_id'];
			$docdet_manufacturer_link = $m['product_manufacturer_link'];
			$docdet_name = $m['product_name'];
			$docdet_description = $m['product_description'];
			$docdet_warranty = $m['product_warranty'];
			$docdet_warranty_id = $m['product_warranty_id'];
			$docdet_image = $m['product_image'];
			$docdet_transportable = $m['product_transportable'];
			$docdet_download_link = $m['product_download_link'];
			$docdet_weight_per_unit = $m['product_weight_per_unit'];
			$docdet_length = $m['product_length'];
			$docdet_width = $m['product_width'];
			$docdet_height = $m['product_height'];
			$docdet_unit_of_measure_id = $m['product_unit_of_measure_id'];
			$docdet_cost = $m['product_cost'];
			$docdet_tax = F_get_vat_value($m['product_tax'], $ecdoc_user_id);
			$docdet_tax2 = F_get_vat_value($m['product_tax2'], $ecdoc_user_id);
			$docdet_tax3 = F_get_vat_value($m['product_tax3'], $ecdoc_user_id);
			$docdet_q_sold = $m['product_q_sold'];
			$docdet_q_available = $m['product_q_available'];
			$docdet_q_arriving = $m['product_q_arriving'];
			$docdet_arriving_time = $m['product_arriving_time'];
			$product_date_added = $m['product_date_added'];
			$r_text = unserialize($docdet_description);
			$docdet_quantity = 1;
		}
	}
	else {
		F_display_db_error();
	}
}
elseif($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!$docdet_id) {
			$sql = "SELECT * FROM ".K_TABLE_EC_DOCUMENTS_DETAILS." WHERE docdet_doc_id='".$docdet_doc_id."' ORDER BY docdet_name LIMIT 1";
		}
		else {
			$sql = "SELECT * FROM ".K_TABLE_EC_DOCUMENTS_DETAILS." WHERE docdet_id=".$docdet_id." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$docdet_id = $m['docdet_id'];
				$docdet_doc_id = $m['docdet_doc_id'];
				$docdet_product_id = $m['docdet_product_id'];
				$docdet_code = $m['docdet_code'];
				$docdet_manufacturer_code = $m['docdet_manufacturer_code'];
				$docdet_barcode = $m['docdet_barcode'];
				$docdet_inventory_code = $m['docdet_inventory_code'];
				$docdet_alternative_codes = $m['docdet_alternative_codes'];
				$docdet_serial_numbers = $m['docdet_serial_numbers'];
				$docdet_category_id = $m['docdet_category_id'];
				$docdet_manufacturer_id = $m['docdet_manufacturer_id'];
				$docdet_manufacturer_link = $m['docdet_manufacturer_link'];
				$docdet_name = $m['docdet_name'];
				$docdet_description = $m['docdet_description'];
				$docdet_warranty = $m['docdet_warranty'];
				$docdet_warranty_id = $m['docdet_warranty_id'];
				$docdet_image = $m['docdet_image'];
				$docdet_transportable = $m['docdet_transportable'];
				$docdet_download_link = $m['docdet_download_link'];
				$docdet_weight_per_unit = $m['docdet_weight_per_unit'];
				$docdet_length = $m['docdet_length'];
				$docdet_width = $m['docdet_width'];
				$docdet_height = $m['docdet_height'];
				$docdet_unit_of_measure_id = $m['docdet_unit_of_measure_id'];
				$docdet_cost = $m['docdet_cost'];
				$docdet_tax = $m['docdet_tax'];
				$docdet_tax2 = $m['docdet_tax2'];
				$docdet_tax3 = $m['docdet_tax3'];
				$docdet_quantity = $m['docdet_quantity'];
				$docdet_discount = $m['docdet_discount'];
				$r_text = unserialize($docdet_description);
			}
			else {
				$docdet_product_id = "";
				$docdet_code = "";
				$docdet_manufacturer_code = "";
				$docdet_barcode = "";
				$docdet_inventory_code = "";
				$docdet_alternative_codes = "";
				$docdet_serial_numbers = "";
				$docdet_category_id = "";
				$docdet_manufacturer_id = "";
				$docdet_manufacturer_link = "";
				$docdet_name = "";
				$docdet_description = "";
				$docdet_warranty = "";
				$docdet_warranty_id = "";
				$docdet_image = "";
				$docdet_transportable = 1;
				$docdet_download_link = "";
				$docdet_weight_per_unit = "";
				$docdet_length = "";
				$docdet_width = "";
				$docdet_height = "";
				$docdet_unit_of_measure_id = "";
				$docdet_cost = "";
				$docdet_tax = 0;
				$docdet_tax2 = NULL;
				$docdet_tax3 = NULL;
				$docdet_quantity = 1;
				$docdet_discount = 0;
				$r_text = array();
			}
		}
		else {
			F_display_db_error();
		}
	}
}

//get right category for selected product
if ($docdet_product_id) {
	$sql = "SELECT product_category_id FROM ".K_TABLE_EC_PRODUCTS." WHERE product_id=".$docdet_product_id." LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$product_category_id = $m['product_category_id'];
		}
		else {
			$product_category_id = false;
		}
	}
	else {
		F_display_db_error();
	}
}


//get unit of measure for this product
$docdet_unit = "";
if (isset($docdet_unit_of_measure_id) AND $docdet_unit_of_measure_id) {
	$sqlu = "SELECT * FROM ".K_TABLE_EC_UNITS_OF_MEASURE." WHERE unit_id='".$docdet_unit_of_measure_id."'";
	if($ru = F_aiocpdb_query($sqlu, $db)) {
		if($mu = F_aiocpdb_fetch_array($ru)) {
			$docdet_unit = $mu['unit_name'];
		}
	}
	else {
		F_display_db_error();
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_businessdocdeteditor" id="form_businessdocdeteditor">

<input type="hidden" name="docdet_doc_id" id="docdet_doc_id" value="<?php echo $docdet_doc_id; ?>" />
<input type="hidden" name="docdet_code" id="docdet_code" value="<?php echo $docdet_code; ?>" />
<input type="hidden" name="docdet_manufacturer_code" id="docdet_manufacturer_code" value="<?php echo $docdet_manufacturer_code; ?>" />
<input type="hidden" name="docdet_barcode" id="docdet_barcode" value="<?php echo $docdet_barcode; ?>" />
<input type="hidden" name="docdet_inventory_code" id="docdet_inventory_code" value="<?php echo $docdet_inventory_code; ?>" />
<input type="hidden" name="docdet_alternative_codes" id="docdet_alternative_codes" value="<?php echo $docdet_alternative_codes; ?>" />
<input type="hidden" name="docdet_category_id" id="docdet_category_id" value="<?php echo $docdet_category_id; ?>" />
<input type="hidden" name="docdet_manufacturer_id" id="docdet_manufacturer_id" value="<?php echo $docdet_manufacturer_id; ?>" />
<input type="hidden" name="docdet_manufacturer_link" id="docdet_manufacturer_link" value="<?php echo $docdet_manufacturer_link; ?>" />
<input type="hidden" name="docdet_name" id="docdet_name" value="<?php echo $docdet_name; ?>" />

<input type="hidden" name="docdet_warranty" id="docdet_warranty" value="<?php echo $docdet_warranty; ?>" />
<input type="hidden" name="docdet_warranty_id" id="docdet_warranty_id" value="<?php echo $docdet_warranty_id; ?>" />

<input type="hidden" name="docdet_image" id="docdet_image" value="<?php echo $docdet_image; ?>" />
<input type="hidden" name="docdet_transportable" id="docdet_transportable" value="<?php echo $docdet_transportable; ?>" />
<input type="hidden" name="docdet_download_link" id="docdet_download_link" value="<?php echo $docdet_download_link; ?>" />
<input type="hidden" name="docdet_weight_per_unit" id="docdet_weight_per_unit" value="<?php echo $docdet_weight_per_unit; ?>" />
<input type="hidden" name="docdet_length" id="docdet_length" value="<?php echo $docdet_length; ?>" />
<input type="hidden" name="docdet_width" id="docdet_width" value="<?php echo $docdet_width; ?>" />
<input type="hidden" name="docdet_height" id="docdet_height" value="<?php echo $docdet_height; ?>" />
				
<input type="hidden" name="docdet_unit_of_measure_id" id="docdet_unit_of_measure_id" value="<?php echo $docdet_unit_of_measure_id; ?>" />

<!-- comma separated list of required fields -->
<!-- <input type="hidden" name="ff_required" id="ff_required" value="docdet_number,docdet_date" /> -->

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<th class="edge">
<?php
$sql = "SELECT ecdoc_number,ecdoc_date,ecdoc_type,ecdoc_discount,ecdoc_deduction,ecdoc_deduction_from FROM ".K_TABLE_EC_DOCUMENTS." WHERE ecdoc_id='".$docdet_doc_id."' LIMIT 1";
if($r = F_aiocpdb_query($sql, $db)) {
	if($m = F_aiocpdb_fetch_array($r)) {
		$ecdoc_number = $m['ecdoc_number'];
		$ecdoc_type = $m['ecdoc_type'];
		$ecdoc_date = $m['ecdoc_date'];
		$sql2 = "SELECT doctype_name FROM ".K_TABLE_EC_DOCUMENTS_TYPES." WHERE doctype_id='".$ecdoc_type."' LIMIT 1";
		if($r2 = F_aiocpdb_query($sql2, $db)) {
			if($m2 = F_aiocpdb_fetch_array($r2)) {
				$select_name = unserialize($m2['doctype_name']);
				$document_type = $select_name[$selected_language];
				echo "".$ecdoc_date." - ".$document_type." - ".$ecdoc_number."";
			}
		}
		else {
			F_display_db_error();
		}
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
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_detail', 'h_docdet_select'); ?></b></td>
<td class="fillEE">
<select name="docdet_id" id="docdet_id" size="0" onchange="document.form_businessdocdeteditor.submit()">
<?php
$i=1;
$sql = "SELECT * FROM ".K_TABLE_EC_DOCUMENTS_DETAILS." WHERE docdet_doc_id='".$docdet_doc_id."'";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['docdet_id']."\"";
		if($m['docdet_id'] == $docdet_id) {
			echo " selected=\"selected\"";
		}
		echo ">".$i++.") ".$m['docdet_code']." - ".$m['docdet_name']."</option>\n";
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

<!-- SELECT category ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_category', 'h_productscat_select'); ?></b></td>
<td class="fillEE" colspan="2">
<input type="hidden" name="changecategory" id="changecategory" value="0" />
<select name="product_category_id" id="product_category_id" size="0" onchange="document.form_businessdocdeteditor.changecategory.value=1; document.form_businessdocdeteditor.submit()">
<?php
$noscriptlink = $_SERVER['SCRIPT_NAME']."?";
if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {
	$noscriptlink .= "aiocp_dp=".$aiocp_dp."&amp;";
}
$noscriptlink .= "changecategory=1&amp;";
$noscriptlink .= "product_category_id=";
F_form_select_tree($product_category_id, false, K_TABLE_EC_PRODUCTS_CATEGORIES, "prodcat", $noscriptlink);
?>
</td>
</tr>
<!-- END SELECT category ==================== -->

<!-- SELECT  ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_product', 'h_productsed_select'); ?></b></td>
<td class="fillOE" colspan="2">
<input type="hidden" name="changeproduct" id="changeproduct" value="0" />
<select name="docdet_product_id" id="docdet_product_id" size="0" onchange="document.form_businessdocdeteditor.changeproduct.value=1; document.form_businessdocdeteditor.submit()">
<?php
if(!isset($docdet_product_id) OR (!$docdet_product_id)) {
	echo "<option value=\"\" selected=\"selected\">&nbsp;</option>\n";
}

if (!$product_category_id) {
	$whereprodcat = "";
}
else {
	$whereprodcat = " WHERE product_category_id=".$product_category_id."";
}

$sql = "SELECT * FROM ".K_TABLE_EC_PRODUCTS."".$whereprodcat." ORDER BY product_code";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['product_id']."\"";
		if($m['product_id'] == $docdet_product_id) {
			echo " selected=\"selected\"";
		}
		echo ">".$m['product_code']."";
		if ($m['product_unit_of_measure_id']) {
			$this_unit = "";
			$sqltu = "SELECT * FROM ".K_TABLE_EC_UNITS_OF_MEASURE." WHERE unit_id='".$m['product_unit_of_measure_id']."'";
			if($rtu = F_aiocpdb_query($sqltu, $db)) {
				if($mtu = F_aiocpdb_fetch_array($rtu)) {
					$this_unit = $mtu['unit_name'];
				}
			}
			else {
				F_display_db_error();
			}
			if ($this_unit) {
				echo " (".$this_unit." ".$m['product_q_available'].")";
			}
		}
		echo " ".$m['product_name']."</option>\n";
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
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_order_by', ''); ?></b></td>
<td class="fillEE" colspan="2">
<?php
echo "<input type=\"radio\" name=\"orderby\" value=\"product_code\"";
if($orderby == "product_code") {echo " checked=\"checked\"";}
echo "onclick=\"document.form_businessdocdeteditor.submit()\" />".$l['w_code']."&nbsp;";

echo "<input type=\"radio\" name=\"orderby\" value=\"product_name\"";
if($orderby == "product_name") {echo " checked=\"checked\"";}
echo "onclick=\"document.form_businessdocdeteditor.submit()\" />".$l['w_name']."&nbsp;";
?>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_cost_per_unit', 'h_productsed_cost'); ?></b></td>
<td class="fillOE"><input type="text" name="docdet_cost" id="docdet_cost" value="<?php echo $docdet_cost; ?>" size="30" maxlength="255" onchange="FJ_calc_totals()" /> <b>[<?php echo K_MONEY_CURRENCY_UNICODE_SYMBOL; ?>]</b></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_quantity', 'h_docdet_quantity'); ?></b></td>
<td class="fillEE"><input type="text" name="docdet_quantity" id="docdet_quantity" value="<?php echo $docdet_quantity; ?>" size="8" maxlength="255" onchange="FJ_calc_totals()" /> <b><?php echo $docdet_unit; ?> (</b> <input type="text" name="totnet" id="totnet" size="15" maxlength="255" readonly="readonly" disabled="disabled" /> <b>[<?php echo K_MONEY_CURRENCY_UNICODE_SYMBOL; ?>] )</b></td>
</tr>

<input type="hidden" name="old_docdet_quantity" id="old_docdet_quantity" value="<?php echo $docdet_quantity; ?>" />

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_discount', 'h_docdet_discount'); ?></b></td>
<td class="fillOE"><input type="text" name="docdet_discount" id="docdet_discount" value="<?php echo $docdet_discount; ?>" size="8" maxlength="255" onchange="FJ_calc_totals()" /> <b>[%] (</b> <input type="text" name="totdiscount" id="totdiscount" size="15" maxlength="255" readonly="readonly" disabled="disabled" /> <b>[<?php echo K_MONEY_CURRENCY_UNICODE_SYMBOL; ?>] )</b></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_ec_tax', 'h_productsed_tax'); ?></b></td>
<td class="fillEE"><input type="text" name="docdet_tax" id="docdet_tax" value="<?php echo $docdet_tax; ?>" size="8" maxlength="255" onchange="FJ_calc_totals()" /> <b>[%] (</b> <input type="text" name="tottax" id="tottax" size="15" maxlength="255" readonly="readonly" disabled="disabled" /> <b>[<?php echo K_MONEY_CURRENCY_UNICODE_SYMBOL; ?>] )</b></td>
</tr>

<?php
if (K_EC_DISPLAY_TAX_2) {
?>
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_ec_tax2', 'h_productsed_tax'); ?></b></td>
<td class="fillOE"><input type="text" name="docdet_tax2" id="docdet_tax2" value="<?php echo $docdet_tax2; ?>" size="8" maxlength="255" onchange="FJ_calc_totals()" /> <b>[%] (</b> <input type="text" name="tottax2" id="tottax2" size="15" maxlength="255" readonly="readonly" disabled="disabled" /> <b>[<?php echo K_MONEY_CURRENCY_UNICODE_SYMBOL; ?>] )</b></td>
</tr>
<?php
}
if (K_EC_DISPLAY_TAX_3) {
?>
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_ec_tax3', 'h_productsed_tax'); ?></b></td>
<td class="fillEE"><input type="text" name="docdet_tax3" id="docdet_tax3" value="<?php echo $docdet_tax3; ?>" size="8" maxlength="255" onchange="FJ_calc_totals()" /> <b>[%] (</b> <input type="text" name="tottax3" id="tottax3" size="15" maxlength="255" readonly="readonly" disabled="disabled" /> <b>[<?php echo K_MONEY_CURRENCY_UNICODE_SYMBOL; ?>] )</b></td>
</tr>
<?php
}
?>
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_total', 'h_productsed_total'); ?></b></td>
<td class="fillOE"><input type="text" name="total" id="total" size="30" maxlength="255" readonly="readonly" disabled="disabled" /> <b>[<?php echo K_MONEY_CURRENCY_UNICODE_SYMBOL; ?>]</b></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b><?php echo F_display_field_name('w_serial_numbers', 'h_productsed_serial_numbers'); ?></b></td>
<td class="fillEE"><textarea cols="30" rows="2" name="docdet_serial_numbers" id="docdet_serial_numbers"><?php echo stripslashes($docdet_serial_numbers); ?></textarea></td>
</tr>


<!-- iterate for each language ==================== -->
<?php
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
<input type="button" name="htmleditor_<?php echo $m['language_code']; ?>" id="htmleditor_<?php echo $m['language_code']; ?>" value="<?php echo $l['w_button_html_editor']; ?>" onclick="htmlWindow=window.open('cp_edit_html.<?php echo CP_EXT; ?>?templates=0&amp;callingform=form_businessdocdeteditor&amp;callingfield=elements['+FJ_get_field_index(this)+']<?php echo $doc_charset_url; ?>','htmlWindow','dependent,height=500,width=800,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')" />
<?php
echo "</td>";

$current_ta_code = $r_text[$m['language_code']];
$current_ta_code = stripslashes($current_ta_code);
echo "<td class=\"fillEE\" colspan=\"2\"><textarea cols=\"30\" rows=\"3\" name=\"r_text[".$m['language_code']."]\" id=\"r_text_".$m['language_code']."\">".htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset)."</textarea></td>";
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
<td class="fillOE"><a href="cp_edit_ec_documents.<?php echo CP_EXT; ?>?ecdoc_id=<?php echo $docdet_doc_id; ?>"><b>&lt;&lt;&nbsp;<?php echo $l['t_ec_documents_editor']; ?></b></a></td>
</tr>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">


<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if ($docdet_id) {
	F_submit_button("form_businessdocdeteditor","menu_mode",$l['w_update']); 
	F_submit_button("form_businessdocdeteditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_businessdocdeteditor","menu_mode",$l['w_add']); 
F_submit_button("form_businessdocdeteditor","menu_mode",$l['w_clear']); 
?>
</td>

</tr>
</table>

<br/>

<?php 
//display products details in a table
F_display_document_details($docdet_doc_id, true, false); 
?>

</form>
<!-- ====================================================== -->

<!-- Cange focus to docdet_id select -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_businessdocdeteditor.docdet_id.focus();

//get next elements ID
function FJ_get_field_index(what) {
	for (var i=0;i<document.form_businessdocdeteditor.elements.length;i++) {
		if(what == document.form_businessdocdeteditor.elements[i]) {
			return(i+1);
		}
	}
	return (-1);
}

function FJ_calc_totals() {
	
	var decimals = Math.pow(10,<?php echo K_MONEY_DECIMALS; ?>);
	var total_net = document.form_businessdocdeteditor.docdet_quantity.value * document.form_businessdocdeteditor.docdet_cost.value;
	document.form_businessdocdeteditor.totnet.value = Math.round( (total_net) * decimals)/decimals;
	
	var total_discount = (document.form_businessdocdeteditor.totnet.value / 100) * document.form_businessdocdeteditor.docdet_discount.value;
	document.form_businessdocdeteditor.totdiscount.value = Math.round( (total_discount) * decimals)/decimals;
	
	var total_tax = ((document.form_businessdocdeteditor.totnet.value - document.form_businessdocdeteditor.totdiscount.value) / 100) * document.form_businessdocdeteditor.docdet_tax.value;
	document.form_businessdocdeteditor.tottax.value = Math.round( (total_tax) * decimals)/decimals;
	
	var total_tax2 = 0;
	var total_tax3 = 0;
	<?php
	if ((K_EC_DISPLAY_TAX_2) AND ($docdet_tax2 != NULL)) {
	?>
	total_tax2 = ((document.form_businessdocdeteditor.totnet.value - document.form_businessdocdeteditor.totdiscount.value) / 100) * document.form_businessdocdeteditor.docdet_tax2.value;
	document.form_businessdocdeteditor.tottax2.value = Math.round( (total_tax2) * decimals)/decimals;
	<?php
	}
	
	if ((K_EC_DISPLAY_TAX_3) AND ($docdet_tax3 != NULL)) {
	?>
	total_tax3 = ((total_tax + total_tax2 + document.form_businessdocdeteditor.totnet.value - document.form_businessdocdeteditor.totdiscount.value) / 100) * document.form_businessdocdeteditor.docdet_tax3.value;
	document.form_businessdocdeteditor.tottax3.value = Math.round( (total_tax3) * decimals)/decimals;
	<?php
	}
	?>
	
	document.form_businessdocdeteditor.total.value = Math.round((total_net - total_discount + total_tax + total_tax2 + total_tax3) * decimals)/decimals;
}

FJ_calc_totals();
//]]>
</script>
<!-- END Cange focus to docdet_id select -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
