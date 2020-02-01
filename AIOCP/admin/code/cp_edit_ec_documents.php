<?php
//============================================================+
// File name   : cp_edit_ec_documents.php                      
// Begin       : 2002-07-02                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit Business Documents (invoices,...)        
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_EC_DOCUMENTS;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

require_once('../../shared/code/cp_functions_form.'.CP_EXT);
require_once('../../shared/code/cp_functions_ec_documents.'.CP_EXT);
require_once('../../shared/code/cp_functions_user.'.CP_EXT);

$thispage_title = $l['t_ec_documents_editor'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages

F_documents_garbage_collector(); //delete expired documents and restore products quantities

//initialize variables
$update_availble_quantity = false;
$update_sold_quantity = false;
$mark_order_processed = false;
$hidden_fields = "";

if (isset($_REQUEST['menu_mode'])) {
	$menu_mode = $_REQUEST['menu_mode'];
} else {
	$menu_mode = "";
}
switch($menu_mode) {

	case unhtmlentities($l['w_delete']):
	case $l['w_delete']:{
		F_stripslashes_formfields(); // Delete target
		F_print_error("WARNING", $l['m_delete_confirm']);
		?>
		<p><?php echo $l['t_warning'].": ".$l['d_document_delete']; ?></p>
		
		<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_delete" id="form_delete">
		<input type="hidden" name="menu_mode" id="menu_mode" value="forcedelete" />
		<input type="hidden" name="forcedelete" id="forcedelete" value="" />
		<input type="hidden" name="ecdoc_id" id="ecdoc_id" value="<?php echo $ecdoc_id; ?>" />
		<input type="hidden" name="ecdoc_type" id="ecdoc_type" value="<?php echo $ecdoc_type; ?>" />
		<input type="hidden" name="ecdoc_from_doc_id" id="ecdoc_from_doc_id" value="<?php echo $ecdoc_from_doc_id; ?>" />
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
			$sql = "DELETE FROM ".K_TABLE_EC_DOCUMENTS." WHERE ecdoc_id=".$ecdoc_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			
			// restore products quantity ------
			if ( ($ecdoc_type == K_EC_ORDER_DOC_ID) OR ($ecdoc_type == K_EC_INVOICE_DOC_ID) ) { //if is a order or invoice
				$sql = "SELECT docdet_product_id, docdet_quantity FROM ".K_TABLE_EC_DOCUMENTS_DETAILS." WHERE docdet_doc_id=".$ecdoc_id."";
				if($r = F_aiocpdb_query($sql, $db)) {
					while($m = F_aiocpdb_fetch_array($r)) {
						if ( ($ecdoc_type == K_EC_ORDER_DOC_ID) OR (($ecdoc_type == K_EC_INVOICE_DOC_ID) AND (!$ecdoc_from_doc_id)) ) {
							// restore product available quantity
							$sqlup = "UPDATE IGNORE ".K_TABLE_EC_PRODUCTS." SET 
							product_q_available=product_q_available+'".$m['docdet_quantity']."'
							WHERE product_id='".$m['docdet_product_id']."' AND product_q_available IS NOT NULL";
							if(!$rup = F_aiocpdb_query($sqlup, $db)) {
								F_display_db_error();
							}
						}
						if ($ecdoc_type == K_EC_INVOICE_DOC_ID) {
							// restore product sold quantity
							$sqlup = "UPDATE IGNORE ".K_TABLE_EC_PRODUCTS." SET 
							product_q_sold=product_q_sold-'".$m['docdet_quantity']."'
							WHERE product_id='".$m['docdet_product_id']."'";
							if(!$rup = F_aiocpdb_query($sqlup, $db)) {
								F_display_db_error();
							}
						}
					}
				}
				else {
					F_display_db_error();
				}
			}
			// end restore products quantity ------
			
			//if invoice is from order => set order expiry time 
			if ( ($ecdoc_type == K_EC_INVOICE_DOC_ID) AND ($ecdoc_from_doc_id) ) {
				$sql = "UPDATE IGNORE ".K_TABLE_EC_DOCUMENTS." SET 
				ecdoc_expiry_time='".(K_EC_ORDER_EXPIRY_TIME + time())."',
				ecdoc_from_doc_id='0'
				WHERE ecdoc_id=".$ecdoc_from_doc_id."";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
			}
			
			//delete details
			$sql = "DELETE FROM ".K_TABLE_EC_DOCUMENTS_DETAILS." WHERE docdet_doc_id=".$ecdoc_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			$ecdoc_id=FALSE;
		}
		break;
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_EC_DOCUMENTS, "ecdoc_number='".$ecdoc_number."' AND YEAR(ecdoc_date)='".$tyear."' AND ecdoc_type='".$ecdoc_type."'" , "ecdoc_id", $ecdoc_id)) {
				F_print_error("WARNING", $l['m_duplicate_data']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				if ($ecdoc_type == K_EC_ORDER_DOC_ID) { //set expiry time for orders
					$ecdoc_expiry_time == K_EC_ORDER_EXPIRY_TIME + time();
				}
				else {
					$ecdoc_expiry_time = "";
				}
				
				$ecdoc_user_data = addslashes(serialize(F_get_user_document_data($ecdoc_user_id)));
				
				$sql = "UPDATE IGNORE ".K_TABLE_EC_DOCUMENTS." SET 
				ecdoc_type='".$ecdoc_type."',
				ecdoc_number='".$ecdoc_number."',
				ecdoc_date='".$ecdoc_date."',
				ecdoc_user_id='".$ecdoc_user_id."',
				ecdoc_user_data='".$ecdoc_user_data."',
				ecdoc_payment_type_id='".$ecdoc_payment_type_id."',
				ecdoc_payment_details='".$ecdoc_payment_details."',
				ecdoc_payment_date='".$ecdoc_payment_date."',
				ecdoc_paid='".$ecdoc_paid."',
				ecdoc_validity='".$ecdoc_validity."',
				ecdoc_validity_unit='".$ecdoc_validity_unit."',
				ecdoc_discount='".$ecdoc_discount."',
				ecdoc_deduction='".$ecdoc_deduction."',
				ecdoc_deduction_from='".$ecdoc_deduction_from."',
				ecdoc_shipping_type_id='".$ecdoc_shipping_type_id."',
				ecdoc_subject='".$ecdoc_subject."',
				ecdoc_notes_intro='".$ecdoc_notes_intro."',
				ecdoc_notes_end='".$ecdoc_notes_end."',
				ecdoc_transport='".$ecdoc_transport."',
				ecdoc_driver_name='".$ecdoc_driver_name."',
				ecdoc_transport_subject='".$ecdoc_transport_subject."',
				ecdoc_parcels='".$ecdoc_parcels."',
				ecdoc_parcels_aspect='".$ecdoc_parcels_aspect."',
				ecdoc_carriage='".$ecdoc_carriage."',
				ecdoc_transport_start_time='".$ecdoc_transport_start_time."',
				ecdoc_transport_net='".$transport_net."',
				ecdoc_transport_tax='".$transport_tax."',
				ecdoc_transport_tax2='".$transport_tax2."',
				ecdoc_transport_tax3='".$transport_tax3."',
				ecdoc_payment_fee='".$payment_fee."',
				ecdoc_expiry_time='".$ecdoc_expiry_time."',
				ecdoc_from_doc_id='".$ecdoc_from_doc_id."'
				WHERE ecdoc_id=".$ecdoc_id."";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
			}
		}
		break;
	}

	case unhtmlentities($l['w_copy']):
	case $l['w_copy']: //copy document data to another document
		$old_ecdoc_type = $ecdoc_type;
		$ecdoc_type = $newdoctypeid;
		//document auto numbering
		$sql = "SELECT COUNT(*) FROM ".K_TABLE_EC_DOCUMENTS." WHERE YEAR(ecdoc_date)='".$tyear."' AND ecdoc_type='".$ecdoc_type."'";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$ecdoc_number = $m['0'] + 1;
			}
			else {
				$ecdoc_number = 1;
			}
		}
		else {
			F_display_db_error();
		}
		$olddocid = $ecdoc_id;
		
		//check if products quantity must be updated
		if ( ($old_ecdoc_type != K_EC_ORDER_DOC_ID) AND ($old_ecdoc_type != K_EC_INVOICE_DOC_ID) ) {
			if ($ecdoc_type == K_EC_ORDER_DOC_ID) {
				$update_availble_quantity = true;
				$update_sold_quantity = false;
			}
			elseif ($ecdoc_type == K_EC_INVOICE_DOC_ID) {
				$update_availble_quantity = true;
				$update_sold_quantity = true;
			}
		}
		elseif ( ($old_ecdoc_type == K_EC_ORDER_DOC_ID) AND ($ecdoc_type == K_EC_INVOICE_DOC_ID) ) {
			$mark_order_processed = true;
		}
		
	case unhtmlentities($l['w_add']):
	case $l['w_add']:{ // Add
		if($formstatus = F_check_form_fields()) {
			//check if ecdoc_name is unique
			$sql = "SELECT ecdoc_number FROM ".K_TABLE_EC_DOCUMENTS." WHERE ecdoc_number='".$ecdoc_number."' AND YEAR(ecdoc_date)='".$tyear."' AND ecdoc_type='".$ecdoc_type."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_data']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				if ($ecdoc_type == K_EC_ORDER_DOC_ID) { //set expiry time for orders
					$ecdoc_expiry_time = K_EC_ORDER_EXPIRY_TIME + time();
				}
				else {
					$ecdoc_expiry_time = "";
				}
				
				$ecdoc_user_data = addslashes(serialize(F_get_user_document_data($ecdoc_user_id)));
				
				$sql = "INSERT IGNORE INTO ".K_TABLE_EC_DOCUMENTS." (
				ecdoc_type,
				ecdoc_number,
				ecdoc_date,
				ecdoc_user_id,
				ecdoc_user_data,
				ecdoc_payment_type_id,
				ecdoc_payment_details,
				ecdoc_payment_date,
				ecdoc_paid,
				ecdoc_validity,
				ecdoc_validity_unit,
				ecdoc_discount,
				ecdoc_deduction,
				ecdoc_deduction_from,
				ecdoc_shipping_type_id,
				ecdoc_subject,
				ecdoc_notes_intro,
				ecdoc_notes_end,
				ecdoc_transport,
				ecdoc_driver_name,
				ecdoc_transport_subject,
				ecdoc_parcels,
				ecdoc_parcels_aspect,
				ecdoc_carriage,
				ecdoc_transport_start_time,
				ecdoc_transport_net,
				ecdoc_transport_tax,
				ecdoc_transport_tax2,
				ecdoc_transport_tax3,
				ecdoc_payment_fee,
				ecdoc_expiry_time,
				ecdoc_from_doc_id
				) VALUES (
				'".$ecdoc_type."',
				'".$ecdoc_number."',
				'".$ecdoc_date."',
				'".$ecdoc_user_id."',
				'".$ecdoc_user_data."',
				'".$ecdoc_payment_type_id."',
				'".$ecdoc_payment_details."',
				'".$ecdoc_payment_date."',
				'".$ecdoc_paid."',
				'".$ecdoc_validity."',
				'".$ecdoc_validity_unit."',
				'".$ecdoc_discount."',
				'".$ecdoc_deduction."',
				'".$ecdoc_deduction_from."',
				'".$ecdoc_shipping_type_id."',
				'".$ecdoc_subject."',
				'".$ecdoc_notes_intro."',
				'".$ecdoc_notes_end."',
				'".$ecdoc_transport."',
				'".$ecdoc_driver_name."',
				'".$ecdoc_transport_subject."',
				'".$ecdoc_parcels."',
				'".$ecdoc_parcels_aspect."',
				'".$ecdoc_carriage."',
				'".$ecdoc_transport_start_time."',
				'".$transport_net."',
				'".$transport_tax."',
				'".$transport_tax2."',
				'".$transport_tax3."',
				'".$payment_fee."',
				'".$ecdoc_expiry_time."',
				'".$olddocid."'
				)";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$ecdoc_id = F_aiocpdb_insert_id();
				}
				
				// ------------------------------------------------------
				if (($menu_mode == $l['w_copy']) OR ($menu_mode == unhtmlentities($l['w_copy'])) )  { //copy document details
					$sql = "SELECT * FROM ".K_TABLE_EC_DOCUMENTS_DETAILS." WHERE docdet_doc_id=".$olddocid."";
					if($r = F_aiocpdb_query($sql, $db)) {
						while($m = F_aiocpdb_fetch_array($r)) {
							$sqld = "INSERT IGNORE INTO ".K_TABLE_EC_DOCUMENTS_DETAILS." (
							docdet_doc_id,
							docdet_product_id,
							docdet_code,
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
							'".$ecdoc_id."',
							'".$m['docdet_product_id']."',
							'".$m['docdet_code']."',
							'".$m['docdet_barcode']."',
							'".$m['docdet_inventory_code']."',
							'".$m['docdet_alternative_codes']."',
							'".$m['docdet_serial_numbers']."',
							'".$m['docdet_category_id']."',
							'".$m['docdet_manufacturer_id']."',
							'".$m['docdet_manufacturer_link']."',
							'".$m['docdet_name']."',
							'".addslashes($m['docdet_description'])."',
							'".$m['docdet_warranty']."',
							'".$m['docdet_warranty_id']."',
							'".$m['docdet_image']."',
							'".$m['docdet_transportable']."',
							'".$m['docdet_download_link']."',
							'".$m['docdet_weight_per_unit']."',
							'".$m['docdet_length']."',
							'".$m['docdet_width']."',
							'".$m['docdet_height']."',
							'".$m['docdet_unit_of_measure_id']."',
							'".$m['docdet_cost']."',
							'".$m['docdet_tax']."',
							'".$m['docdet_tax2']."',
							'".$m['docdet_tax3']."',
							'".$m['docdet_quantity']."',
							'".$m['docdet_discount']."'
							)";
							if(!$rd = F_aiocpdb_query($sqld, $db)) {
								F_display_db_error();
							}
							
							//update products quantity
							if ($update_availble_quantity) {
								// restore product available quantity
								$sqlup = "UPDATE IGNORE ".K_TABLE_EC_PRODUCTS." SET 
								product_q_available=product_q_available-'".$m['docdet_quantity']."'
								WHERE product_id='".$m['docdet_product_id']."' AND product_q_available IS NOT NULL";
								if(!$rup = F_aiocpdb_query($sqlup, $db)) {
									F_display_db_error();
								}
							}
							if ($update_sold_quantity) {
								// restore product sold quantity
								$sqlup = "UPDATE IGNORE ".K_TABLE_EC_PRODUCTS." SET 
								product_q_sold=product_q_sold+'".$m['docdet_quantity']."'
								WHERE product_id='".$m['docdet_product_id']."'";
								if(!$rup = F_aiocpdb_query($sqlup, $db)) {
									F_display_db_error();
								}
							}
						}
					}
					else {
						F_display_db_error();
					}
					//mark order as processed (avoid order to be deleted by garbage collector)
					if ($mark_order_processed) {
						$sql = "UPDATE IGNORE ".K_TABLE_EC_DOCUMENTS." SET 
						ecdoc_expiry_time='0'
						WHERE ecdoc_id=".$olddocid."";
						if(!$r = F_aiocpdb_query($sql, $db)) {
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
		//$ecdoc_type = "";
		
		//document auto numbering
		$sql = "SELECT COUNT(*) FROM ".K_TABLE_EC_DOCUMENTS." WHERE YEAR(ecdoc_date)='".$tyear."' AND ecdoc_type='".$ecdoc_type."'";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$ecdoc_number = $m['0'] + 1;
			}
			else {
				$ecdoc_number = 1;
			}
		}
		else {
			F_display_db_error();
		}
		
		$ecdoc_date = date("Y-m-d");
		$ecdoc_user_id = "";
		$ecdoc_payment_type_id = "";
		$ecdoc_payment_details = "";
		$ecdoc_payment_date = "";
		$ecdoc_paid = 0;
		$ecdoc_validity = "";
		$ecdoc_validity_unit = "";
		$ecdoc_discount = K_EC_GENERAL_DISCOUNT;
		$ecdoc_deduction = "";
		$ecdoc_deduction_from = "";
		$ecdoc_shipping_type_id = "";
		$ecdoc_subject = "";
		$ecdoc_notes_intro = "";
		$ecdoc_notes_end = "";
		$ecdoc_transport = 0;
		$ecdoc_driver_name = "";
		$ecdoc_transport_subject = "";
		$ecdoc_parcels = "";
		$ecdoc_parcels_aspect = "";
		$ecdoc_carriage = "";
		$ecdoc_transport_start_time = date("Y-m-d H:i");
		$ecdoc_from_doc_id=0;
		break;
	}

	/*
	case unhtmlentities($l['w_add_transaction']):
	case $l['w_add_transaction']:{ // switch to transactions editor
		echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
		echo "//<![CDATA[\n";
		echo "location.replace(\"../code/cp_edit_ec_transactions.".CP_EXT."?\");\n";
		echo "//]]>\n";
		echo "</script>\n";
		break;
	}
	*/

	default :{ 
		break;
	}

} //end of switch

// Initialize variables

if ( (isset($changetype) AND $changetype) OR (isset($changeyear) AND $changeyear) OR (isset($changecustomer) AND $changecustomer) ) {
	$ecdoc_id = FALSE;
	$only_pending = 0;
}
if ( (isset($changepending) AND $changepending) ) {
	$ecdoc_id = FALSE;
}

$currentyear = date('Y');
$firstyear = $currentyear;
$sql = "SELECT MIN(ecdoc_date) FROM ".K_TABLE_EC_DOCUMENTS."";
if($r = F_aiocpdb_query($sql, $db)) {
	if($m = F_aiocpdb_fetch_array($r)) {
		$firstyear = date('Y',strtotime($m[0]));
	}
}
else {
	F_display_db_error();
}

if (!isset($tyear) OR (!$tyear) OR ($tyear < $firstyear)) {
	$tyear = $currentyear;
}

if ( (!isset($ecdoc_type)) OR (!$ecdoc_type)) {
	$ecdoc_type = K_EC_ORDER_DOC_ID;
	$only_pending = 1;
}

$whereselect = "";

//sql where condition to select only pending orders
if (isset($only_pending) AND $only_pending) {
	$whereselect .= "AND ecdoc_expiry_time>0";
}

//sql where condition to select only specified user documents
if (isset($docuser) AND ($docuser > 0)) {
	$whereselect .= " AND ecdoc_user_id='".$docuser."'";
} else {
	$docuser = 0;
}

if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!isset($ecdoc_id) OR (!$ecdoc_id)) {
			$sql = "SELECT * FROM ".K_TABLE_EC_DOCUMENTS." WHERE (YEAR(ecdoc_date)='".$tyear."' AND ecdoc_type='".$ecdoc_type."'".$whereselect.") ORDER BY ecdoc_date DESC LIMIT 1";
		} else {
			$sql = "SELECT * FROM ".K_TABLE_EC_DOCUMENTS." WHERE ecdoc_id=".$ecdoc_id." LIMIT 1";
			$whereselect = "";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$ecdoc_id = $m['ecdoc_id'];
				$ecdoc_type = $m['ecdoc_type'];
				$ecdoc_number = $m['ecdoc_number'];
				$ecdoc_date = $m['ecdoc_date'];
				$tyear = date('Y',strtotime($ecdoc_date));
				$ecdoc_user_id = $m['ecdoc_user_id'];
				$ecdoc_payment_type_id = $m['ecdoc_payment_type_id'];
				$ecdoc_payment_details = $m['ecdoc_payment_details'];
				$ecdoc_payment_date = $m['ecdoc_payment_date'];
				$ecdoc_paid = $m['ecdoc_paid'];
				$ecdoc_validity = $m['ecdoc_validity'];
				$ecdoc_validity_unit = $m['ecdoc_validity_unit'];
				$ecdoc_discount = $m['ecdoc_discount'];
				$ecdoc_deduction = $m['ecdoc_deduction'];
				$ecdoc_deduction_from = $m['ecdoc_deduction_from'];
				$ecdoc_shipping_type_id = $m['ecdoc_shipping_type_id'];
				$ecdoc_subject = $m['ecdoc_subject'];
				$ecdoc_notes_intro = $m['ecdoc_notes_intro'];
				$ecdoc_notes_end = $m['ecdoc_notes_end'];
				$ecdoc_transport = $m['ecdoc_transport'];
				$ecdoc_driver_name = $m['ecdoc_driver_name'];
				$ecdoc_transport_subject = $m['ecdoc_transport_subject'];
				$ecdoc_parcels = $m['ecdoc_parcels'];
				$ecdoc_parcels_aspect = $m['ecdoc_parcels_aspect'];
				$ecdoc_carriage = $m['ecdoc_carriage'];
				$ecdoc_transport_start_time = $m['ecdoc_transport_start_time'];
				$transport_net = $m['ecdoc_transport_net'];
				$transport_tax = $m['ecdoc_transport_tax'];
				$transport_tax2 = $m['ecdoc_transport_tax2'];
				$transport_tax3 = $m['ecdoc_transport_tax3'];
				$payment_fee = $m['ecdoc_payment_fee'];
				$ecdoc_expiry_time = $m['ecdoc_expiry_time'];
				if ($ecdoc_expiry_time > 0) {
					$only_pending = 1;
				}
				else {
					$only_pending = 0;
				}
				$ecdoc_from_doc_id = $m['ecdoc_from_doc_id'];
			}
			else {
				$sqlc = "SELECT COUNT(*) FROM ".K_TABLE_EC_DOCUMENTS." WHERE YEAR(ecdoc_date)='".$tyear."' AND ecdoc_type='".$ecdoc_type."'";
				if($rc = F_aiocpdb_query($sqlc, $db)) {
					if($mc = F_aiocpdb_fetch_array($rc)) {
						$ecdoc_number = $mc['0'] + 1;
					}
					else {
						$ecdoc_number = 1;
					}
				}
				else {
					F_display_db_error();
				}
				$ecdoc_date = date("Y-m-d");
				$ecdoc_user_id = "";
				$ecdoc_payment_type_id = "";
				$ecdoc_payment_details = "";
				$ecdoc_payment_date = "";
				$ecdoc_paid = 0;
				$ecdoc_validity = "";
				$ecdoc_validity_unit = "";
				$ecdoc_discount = K_EC_GENERAL_DISCOUNT + F_get_user_discount($docuser);
				$ecdoc_deduction = "";
				$ecdoc_deduction_from = "";
				$ecdoc_shipping_type_id = "";
				$ecdoc_subject = "";
				$ecdoc_notes_intro = "";
				$ecdoc_notes_end = "";
				$ecdoc_transport = 0;
				$ecdoc_driver_name = "";
				$ecdoc_transport_subject = "";
				$ecdoc_parcels = "";
				$ecdoc_parcels_aspect = "";
				$ecdoc_carriage = "";
				$ecdoc_transport_start_time = date("Y-m-d H:i");
				$ecdoc_from_doc_id = 0;
			}
		}
		else {
			F_display_db_error();
		}
	}
}

if (($menu_mode == $l['w_send']) OR ($menu_mode == unhtmlentities($l['w_send'])) ) { // Send doc by email to user
	$document_type = "";
	//get document name
	$sql = "SELECT *  FROM ".K_TABLE_EC_DOCUMENTS_TYPES." WHERE doctype_id='".$ecdoc_type."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$document_type = unserialize($m['doctype_name']);
			$document_type = $document_type[$selected_language];
		}
	}
	else {
		F_display_db_error();
	}
	$message = "";
	if ($ecdoc_paid) { //if this invoice has been paid
		$message = F_get_downloadable_files_links($ecdoc_id); //get links list of downloadable files 
	}
	F_send_pdfdoc_email($ecdoc_id, $ecdoc_user_id, $document_type, $ecdoc_number, $ecdoc_date, $message);
	
	//remove pending flag from RGA documents
	if ($ecdoc_type == K_EC_RGA_DOC_ID) {
		$sql = "UPDATE IGNORE ".K_TABLE_EC_DOCUMENTS." SET ecdoc_expiry_time='0' WHERE ecdoc_id=".$ecdoc_id."";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_businessdoceditor" id="form_businessdoceditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="ecdoc_number,ecdoc_date" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_number'].",".$l['w_date']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT CUSTOMER ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_user', 'h_ecdoc_user_select'); ?></b></td>
<td class="fillEE">
<input type="hidden" name="changecustomer" id="changecustomer" value="0" />

<select name="docuser" id="docuser" size="0" onchange="document.form_businessdoceditor.changecustomer.value=1; document.form_businessdoceditor.submit()">
<option value="">&nbsp;</option>
<?php
// display companies
$select_set = false;
$sql = "SELECT company_userid, company_name, user_name FROM ".K_TABLE_USERS_COMPANY.", ".K_TABLE_USERS." WHERE company_userid=user_id ORDER BY company_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['company_userid']."\"";
		if($m['company_userid'] == $docuser) {
			echo " selected=\"selected\"";
			$select_set = true;
		}
		echo ">".htmlentities($m['company_name'], ENT_NOQUOTES, $l['a_meta_charset'])." (".htmlentities($m['user_name'], ENT_NOQUOTES, $l['a_meta_charset']).")";
		echo "&nbsp;</option>\n";
	}
}
else {
	F_display_db_error();
}

echo "<option value=\"\">----------&nbsp;</option>\n";

// display users
$sql = "SELECT user_id, user_name, user_firstname, user_lastname FROM ".K_TABLE_USERS." ORDER BY user_lastname";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		if ($m['user_id'] > 1) {
			echo "<option value=\"".$m['user_id']."\"";
			if( ($m['user_id'] == $docuser) AND (!$select_set) ) {
				echo " selected=\"selected\"";
			}
			echo ">".htmlentities($m['user_lastname'], ENT_NOQUOTES, $l['a_meta_charset']).", ".htmlentities($m['user_firstname'], ENT_NOQUOTES, $l['a_meta_charset'])." (".htmlentities($m['user_name'], ENT_NOQUOTES, $l['a_meta_charset']).")";
			echo "&nbsp;</option>\n";
		}
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT CUSTOMER ==================== -->

<!-- SELECT year ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_year', 'h_ecdoc_year'); ?></b></td>
<td class="fillOE">
<input type="hidden" name="changeyear" id="changeyear" value="0" />

<select name="tyear" id="tyear" size="0" onchange="document.form_businessdoceditor.changeyear.value=1; document.form_businessdoceditor.submit()">
<?php
for ($i=$firstyear; $i<=$currentyear; $i++) {
	echo "<option value=\"".$i."\"";
	if ($tyear == $i) {echo " selected=\"selected\"";}
	echo ">".$i."&nbsp;</option>\n";
}
?>
</select>
</td>
</tr>
<!-- END SELECT year ==================== -->

<!-- SELECT  ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_document_type', 'h_doctype_select'); ?></b></td>
<td class="fillEE">
<input type="hidden" name="changetype" id="changetype" value="0" />

<select name="ecdoc_type" id="ecdoc_type" size="0" onchange="document.form_businessdoceditor.changetype.value=1; document.form_businessdoceditor.submit()">
<?php
$doc_options = array();
	$sql = "SELECT *  FROM ".K_TABLE_EC_DOCUMENTS_TYPES." ORDER BY doctype_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			$select_name = unserialize($m['doctype_name']);
			echo "<option value=\"".$m['doctype_id']."\"";
			if($m['doctype_id'] == $ecdoc_type) {
				echo " selected=\"selected\"";
				$doc_options = unserialize($m['doctype_options']);
			}
			echo ">".htmlentities($select_name[$selected_language], ENT_NOQUOTES, $l['a_meta_charset'])."&nbsp;</option>\n";
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

<?php
//show an option to display only pending orders
if (($ecdoc_type == K_EC_ORDER_DOC_ID) OR ($ecdoc_type == K_EC_RGA_DOC_ID) ){
	echo "<tr class=\"fillE\">";
	echo "<td class=\"fillEO\" align=\"right\"><b>";
	echo F_display_field_name('w_only_pending', 'h_doctype_pending');
	echo "</b></td>";
	echo "<td class=\"fillEE\">";
	echo "<input type=\"hidden\" name=\"changepending\" id=\"changepending\" value=\"0\" />";
	echo "<input type=\"checkbox\" name=\"only_pending\" id=\"only_pending\" value=\"1\"";
	if (isset($only_pending) AND $only_pending) {
		echo " checked=\"checked\"";
	}
	echo" onclick=\"document.form_businessdoceditor.changepending.value=1;document.form_businessdoceditor.submit()\" />";
	echo "</td></tr>";
}
?>

<!-- SELECT ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_document', 'h_ecdoc_select'); ?></b></td>
<td class="fillOE">
<input type="hidden" name="changedoc" id="changedoc" value="0" />

<select name="ecdoc_id" id="ecdoc_id" size="0" onchange="document.form_businessdoceditor.changedoc.value=1; document.form_businessdoceditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_EC_DOCUMENTS." WHERE (YEAR(ecdoc_date)='".$tyear."' AND ecdoc_type='".$ecdoc_type."'".$whereselect.") ORDER BY ecdoc_date DESC, ecdoc_number DESC";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['ecdoc_id']."\"";
		if($m['ecdoc_id'] == $ecdoc_id) {
			echo " selected=\"selected\"";
		}
		echo ">".$m['ecdoc_date']." - ".htmlentities($m['ecdoc_number'], ENT_NOQUOTES, $l['a_meta_charset'])."&nbsp;</option>\n";
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

<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE"><hr /></td>
</tr>

<!-- SELECT CUSTOMER ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_destination', 'h_ecdoc_company'); ?></b></td>
<td class="fillEE">
<select name="ecdoc_user_id" id="ecdoc_user_id" size="0">
<?php
$select_set = false;
// display companies
$sql = "SELECT company_userid, company_name, user_name FROM ".K_TABLE_USERS_COMPANY.", ".K_TABLE_USERS." WHERE company_userid=user_id ORDER BY company_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['company_userid']."\"";
		if($m['company_userid'] == $ecdoc_user_id) {
			echo " selected=\"selected\"";
			$select_set = true;
		}
		echo ">".htmlentities($m['company_name'], ENT_NOQUOTES, $l['a_meta_charset'])." (".htmlentities($m['user_name'], ENT_NOQUOTES, $l['a_meta_charset']).")";
		echo "&nbsp;</option>\n";
	}
}
else {
	F_display_db_error();
}

echo "<option value=\"\">----------&nbsp;</option>\n";

// display users
$sql = "SELECT user_id, user_name, user_firstname, user_lastname FROM ".K_TABLE_USERS." ORDER BY user_lastname";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		if ($m['user_id'] > 1) {
			echo "<option value=\"".$m['user_id']."\"";
			if( ($m['user_id'] == $ecdoc_user_id) AND (!$select_set) ) {
				echo " selected=\"selected\"";
			}
			echo ">".htmlentities($m['user_lastname'], ENT_NOQUOTES, $l['a_meta_charset']).", ".htmlentities($m['user_firstname'], ENT_NOQUOTES, $l['a_meta_charset'])." (".htmlentities($m['user_name'], ENT_NOQUOTES, $l['a_meta_charset']).")";
			echo "&nbsp;</option>\n";
		}
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT CUSTOMER ==================== -->

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_number', 'h_ecdoc_number'); ?></b></td>
<td class="fillOE"><input type="text" name="ecdoc_number" id="ecdoc_number" value="<?php echo htmlentities($ecdoc_number, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="10" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_date', 'h_ecdoc_date'); ?></b></td>
<td class="fillEE"><input type="text" name="ecdoc_date" id="ecdoc_date" value="<?php echo $ecdoc_date; ?>" size="30" maxlength="10" />
<input type="hidden" name="x_ecdoc_date" id="x_ecdoc_date" value="([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})" />
<input type="hidden" name="xl_ecdoc_date" id="xl_ecdoc_date" value="<?php echo $l['w_date']; ?>" />
</td>
</tr>

<?php $doc_charset = F_word_language($selected_language, "a_meta_charset"); 

if (!$doc_options[0]) {
	$hidden_fields .= "<input type=\"hidden\" name=\"ecdoc_payment_type_id\" id=\"ecdoc_payment_type_id\" value=\"".$ecdoc_payment_type_id."\" />";
}
else {
?>
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_payment', 'h_paytype_select'); ?></b></td>
<td class="fillOE">
<select name="ecdoc_payment_type_id" id="ecdoc_payment_type_id" size="0">
<option value="">&nbsp;&nbsp;</option>
<?php
	//$sql = "SELECT * FROM ".K_TABLE_EC_PAYMENTS_TYPES." WHERE paytype_enabled=1 ORDER BY paytype_name";
	$sql = "SELECT * FROM ".K_TABLE_EC_PAYMENTS_TYPES." ORDER BY paytype_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			$select_name = unserialize($m['paytype_name']);
			echo "<option value=\"".$m['paytype_id']."\"";
			if($m['paytype_id'] == $ecdoc_payment_type_id) {
				echo " selected=\"selected\"";
			}
			echo ">".htmlentities($select_name[$selected_language], ENT_NOQUOTES, $l['a_meta_charset'])."&nbsp;</option>\n";
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


if (!$doc_options[1]) {
	$hidden_fields .= "<input type=\"hidden\" name=\"ecdoc_payment_details\" id=\"ecdoc_payment_details\" value=\"".$ecdoc_payment_details."\" />";
	$hidden_fields .= "<input type=\"hidden\" name=\"ecdoc_payment_date\" id=\"ecdoc_payment_date\" value=\"".$ecdoc_payment_date."\" />";
}
else {
?>
<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b><?php echo F_display_field_name('w_payment_details', 'h_mtrans_payment_details'); ?></b>
</td>
<?php
$current_ta_code = $ecdoc_payment_details;
if(!$formstatus) {
	$current_ta_code = stripslashes($current_ta_code);
}
?>
<td class="fillEE">
<textarea cols="30" rows="3" name="ecdoc_payment_details" id="ecdoc_payment_details"><?php echo htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset); ?></textarea>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_payment_date', 'h_ecdoc_payment_date'); ?></b></td>
<td class="fillOE"><input type="text" name="ecdoc_payment_date" id="ecdoc_payment_date" value="<?php echo $ecdoc_payment_date; ?>" size="30" maxlength="10" />
<input type="hidden" name="x_ecdoc_payment_date" id="x_ecdoc_payment_date" value="([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})" />
<input type="hidden" name="xl_ecdoc_payment_date" id="xl_ecdoc_payment_date" value="<?php echo $l['w_payment_date']; ?>" />
</td>
</tr>
<?php
}


if (!$doc_options[2]) {
	$hidden_fields .= "<input type=\"hidden\" name=\"ecdoc_paid\" id=\"ecdoc_paid\" value=\"".$ecdoc_paid."\" />";
}
else {
?>
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_paid', 'h_ecdoc_paid'); ?></b></td>
<td class="fillEE">
<?php
echo "<input type=\"radio\" name=\"ecdoc_paid\" value=\"1\"";
if($ecdoc_paid) {echo " checked=\"checked\"";}
echo " />".$l['w_yes']."&nbsp;";

echo "<input type=\"radio\" name=\"ecdoc_paid\" value=\"0\"";
if(!$ecdoc_paid) {echo " checked=\"checked\"";}
echo " />".$l['w_no']."&nbsp;";
?>
</td>
</tr>
<?php
}


if (!$doc_options[3]) {
	$hidden_fields .= "<input type=\"hidden\" name=\"ecdoc_validity\" id=\"ecdoc_validity\" value=\"".$ecdoc_validity."\" />";
}
else {
?>
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_validity', 'h_ecdoc_validity'); ?></b></td>
<td class="fillOE">
<input type="text" name="ecdoc_validity" id="ecdoc_validity" value="<?php echo $ecdoc_validity; ?>" size="10" maxlength="255" /><select name="ecdoc_validity_unit" id="ecdoc_validity_unit" size="0">
<?php

echo "<option value=\"days\"";
if ($ecdoc_validity_unit == "days") {echo " selected=\"selected\"";}
echo ">".$l['w_days']."&nbsp;</option>\n";

echo "<option value=\"months\"";
if ($ecdoc_validity_unit == "months") {echo " selected=\"selected\"";}
echo ">".$l['w_months']."&nbsp;</option>\n";

echo "<option value=\"years\"";
if ($ecdoc_validity_unit == "years") {echo " selected=\"selected\"";}
echo ">".$l['w_years']."&nbsp;</option>\n";

?>
</select>
</td>
</tr>
<?php
}


if (!$doc_options[4]) {
	$hidden_fields .= "<input type=\"hidden\" name=\"ecdoc_discount\" id=\"ecdoc_discount\" value=\"".$ecdoc_discount."\" />";
	$hidden_fields .= "<input type=\"hidden\" name=\"ecdoc_deduction\" id=\"ecdoc_deduction\" value=\"".$ecdoc_deduction."\" />";
	$hidden_fields .= "<input type=\"hidden\" name=\"ecdoc_deduction_from\" id=\"ecdoc_deduction_from\" value=\"".$ecdoc_deduction_from."\" />";
	$hidden_fields .= "<input type=\"hidden\" name=\"ecdoc_shipping_type_id\" id=\"ecdoc_shipping_type_id\" value=\"".$ecdoc_shipping_type_id."\" />";
}
else {
?>
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_discount', 'h_ecdoc_discount'); ?></b></td>
<td class="fillEE"><input type="text" name="ecdoc_discount" id="ecdoc_discount" value="<?php echo $ecdoc_discount; ?>" size="30" maxlength="255" /> <b>[%]</b></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_deduction', 'h_ecdoc_deduction'); ?></b></td>
<td class="fillOE"><input type="text" name="ecdoc_deduction" id="ecdoc_deduction" value="<?php echo $ecdoc_deduction; ?>" size="30" maxlength="255" /> <b>[%]</b></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_deduction_from', 'h_ecdoc_deduction_from'); ?></b></td>
<td class="fillEE"><input type="text" name="ecdoc_deduction_from" id="ecdoc_deduction_from" value="<?php echo $ecdoc_deduction_from; ?>" size="30" maxlength="255" /> <b>[%]</b></td>
</tr>

<!-- SELECT  ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_shipping', 'h_shipping_select'); ?></b></td>
<td class="fillOE">
<select name="ecdoc_shipping_type_id" id="ecdoc_shipping_type_id" size="0">
<?php
	$sql = "SELECT * FROM ".K_TABLE_EC_SHIPPING_TYPES." WHERE shipping_enabled=1 ORDER BY shipping_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			$select_name = unserialize($m['shipping_name']);
			echo "<option value=\"".$m['shipping_id']."\"";
			if($m['shipping_id'] == $ecdoc_shipping_type_id) {
				echo " selected=\"selected\"";
			}
			echo ">".htmlentities($select_name[$selected_language], ENT_NOQUOTES, $l['a_meta_charset'])."&nbsp;</option>\n";
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

<?php
}


if (!$doc_options[6]) {
	$hidden_fields .= "<input type=\"hidden\" name=\"ecdoc_subject\" id=\"ecdoc_subject\" value=\"".htmlentities($ecdoc_subject, ENT_COMPAT, $l['a_meta_charset'])."\" />";
}
else {
?>
<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b><?php echo F_display_field_name('w_subject', 'h_ecdoc_subject'); ?></b>
</td>
<?php
$current_ta_code = $ecdoc_subject;
if(!$formstatus) {
	$current_ta_code = stripslashes($current_ta_code);
}
?>
<td class="fillEE">
<textarea cols="30" rows="3" name="ecdoc_subject" id="ecdoc_subject"><?php echo htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset); ?></textarea>
</td>
</tr>
<?php
}


if (!$doc_options[7]) {
	$hidden_fields .= "<input type=\"hidden\" name=\"ecdoc_notes_intro\" id=\"ecdoc_notes_intro\" value=\"".htmlentities($ecdoc_notes_intro, ENT_COMPAT, $l['a_meta_charset'])."\" />";
}
else {
?>
<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b><?php echo F_display_field_name('w_notes_intro', 'h_ecdoc_notes_intro'); ?></b>
</td>
<?php
$current_ta_code = $ecdoc_notes_intro;
if(!$formstatus) {
	$current_ta_code = stripslashes($current_ta_code);
}
?>
<td class="fillOE">
<textarea cols="30" rows="3" name="ecdoc_notes_intro" id="ecdoc_notes_intro"><?php echo htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset); ?></textarea>
</td>
</tr>
<?php
}


if (!$doc_options[8]) {
	$hidden_fields .= "<input type=\"hidden\" name=\"ecdoc_notes_end\" id=\"ecdoc_notes_end\" value=\"".htmlentities($ecdoc_notes_end, ENT_COMPAT, $l['a_meta_charset'])."\" />";
}
else {
?>
<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b><?php echo F_display_field_name('w_notes_end', 'h_ecdoc_notes_end'); ?></b>
</td>
<?php
$current_ta_code = $ecdoc_notes_end;
if(!$formstatus) {
	$current_ta_code = stripslashes($current_ta_code);
}
?>
<td class="fillEE">
<textarea cols="30" rows="3" name="ecdoc_notes_end" id="ecdoc_notes_end"><?php echo htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset); ?></textarea>
</td>
</tr>
<?php
}


if (!$doc_options[5]) {
	$hidden_fields .= "<input type=\"hidden\" name=\"ecdoc_transport\" id=\"ecdoc_transport\" value=\"".$ecdoc_transport."\" />";
	$hidden_fields .= "<input type=\"hidden\" name=\"ecdoc_driver_name\" id=\"ecdoc_driver_name\" value=\"".htmlentities($ecdoc_driver_name, ENT_COMPAT, $l['a_meta_charset'])."\" />";
	$hidden_fields .= "<input type=\"hidden\" name=\"ecdoc_transport_subject\" id=\"ecdoc_transport_subject\" value=\"".htmlentities($ecdoc_transport_subject, ENT_COMPAT, $l['a_meta_charset'])."\" />";
	$hidden_fields .= "<input type=\"hidden\" name=\"ecdoc_parcels\" id=\"ecdoc_parcels\" value=\"".htmlentities($ecdoc_parcels, ENT_COMPAT, $l['a_meta_charset'])."\" />";
	$hidden_fields .= "<input type=\"hidden\" name=\"ecdoc_parcels_aspect\" id=\"ecdoc_parcels_aspect\" value=\"".htmlentities($ecdoc_parcels_aspect, ENT_COMPAT, $l['a_meta_charset'])."\" />";
	$hidden_fields .= "<input type=\"hidden\" name=\"ecdoc_carriage\" id=\"ecdoc_carriage\" value=\"".htmlentities($ecdoc_carriage, ENT_COMPAT, $l['a_meta_charset'])."\" />";
	$hidden_fields .= "<input type=\"hidden\" name=\"ecdoc_transport_start_time\" id=\"ecdoc_transport_start_time\" value=\"".$ecdoc_transport_start_time."\" />";
}
else {
?>
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_transport', 'h_ecdoc_transport'); ?></b></td>
<td class="fillOE">
<?php
echo "<input type=\"radio\" name=\"ecdoc_transport\" value=\"1\"";
if($ecdoc_transport) {echo " checked=\"checked\"";}
echo " />".$l['w_yes']."&nbsp;";

echo "<input type=\"radio\" name=\"ecdoc_transport\" value=\"0\"";
if(!$ecdoc_transport) {echo " checked=\"checked\"";}
echo " />".$l['w_no']."&nbsp;";
?>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_driver_name', 'h_ecdoc_driver_name'); ?></b></td>
<td class="fillEE"><input type="text" name="ecdoc_driver_name" id="ecdoc_driver_name" value="<?php echo htmlentities($ecdoc_driver_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b><?php echo F_display_field_name('w_transport_subject', 'h_ecdoc_transport_subject'); ?></b>
</td>
<?php
$current_ta_code = $ecdoc_transport_subject;
if(!$formstatus) {
	$current_ta_code = stripslashes($current_ta_code);
}
?>
<td class="fillOE">
<textarea cols="30" rows="3" name="ecdoc_transport_subject" id="ecdoc_transport_subject"><?php echo htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset); ?></textarea>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_parcels', 'h_ecdoc_parcels'); ?></b></td>
<td class="fillEE"><input type="text" name="ecdoc_parcels" id="ecdoc_parcels" value="<?php echo htmlentities($ecdoc_parcels, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b><?php echo F_display_field_name('w_parcels_aspect', 'h_ecdoc_parcels_aspect'); ?></b>
</td>
<?php
$current_ta_code = $ecdoc_parcels_aspect;
if(!$formstatus) {
	$current_ta_code = stripslashes($current_ta_code);
}
?>
<td class="fillOE">
<textarea cols="30" rows="3" name="ecdoc_parcels_aspect" id="ecdoc_parcels_aspect"><?php echo htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset); ?></textarea>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_carriage', 'h_ecdoc_carriage'); ?></b></td>
<td class="fillEE"><input type="text" name="ecdoc_carriage" id="ecdoc_carriage" value="<?php echo htmlentities($ecdoc_carriage, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_transport_start_time', 'h_ecdoc_transport_start_time'); ?></b></td>
<td class="fillOE"><input type="text" name="ecdoc_transport_start_time" id="ecdoc_transport_start_time" value="<?php echo $ecdoc_transport_start_time; ?>" size="30" maxlength="16" /></td>
</tr>
<?php
}
if (isset($ecdoc_id) AND ($ecdoc_id > 0)) {
?>
<tr class="fillE">
<td class="fillEO" align="right">&nbsp;</td>
<td class="fillEE"><a href="cp_edit_ec_documents_details.<?php echo CP_EXT; ?>?docdet_doc_id=<?php echo $ecdoc_id; ?>"><b><?php echo $l['t_ec_documents_details_editor']; ?>&nbsp;&gt;&gt;</b></a></td>
</tr>
<?php } ?>
</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">

<?php //show buttons
if (isset($ecdoc_id) AND ($ecdoc_id > 0)) {
?>

<!-- SELECT  ==================== -->
<b><?php echo F_display_field_name('w_copy_data_to', 'h_ecdoc_copy_data_to'); ?></b>
<select name="newdoctypeid" id="newdoctypeid" size="0">
<?php
	$sql = "SELECT * FROM ".K_TABLE_EC_DOCUMENTS_TYPES." ORDER BY doctype_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			$select_name = unserialize($m['doctype_name']);
			echo "<option value=\"".$m['doctype_id']."\"";
			if($m['doctype_id'] == $doctype_id) {
				echo " selected=\"selected\"";
			}
			echo ">".htmlentities($select_name[$selected_language], ENT_NOQUOTES, $l['a_meta_charset'])."&nbsp;</option>\n";
		}
	}
	else {
		F_display_db_error();
	}
?>
</select>
<!-- END SELECT  ==================== -->
<?php
F_submit_button("form_businessdoceditor","menu_mode",$l['w_copy']); 
F_submit_button("form_businessdoceditor","menu_mode",$l['w_send']); //button to send doc by email to user
?>
<br />
<?php
}

echo $hidden_fields;
?>

<input type="hidden" name="ecdoc_from_doc_id" id="ecdoc_from_doc_id" value="<?php echo $ecdoc_from_doc_id; ?>" />
<input type="hidden" name="menu_mode" id="menu_mode" value="" />

<?php //show buttons
if (isset($ecdoc_id) AND ($ecdoc_id > 0)) {
	F_submit_button("form_businessdoceditor","menu_mode",$l['w_update']); 
	F_submit_button("form_businessdoceditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_businessdoceditor","menu_mode",$l['w_add']); 
F_submit_button("form_businessdoceditor","menu_mode",$l['w_clear']); 
?>
</td>
</tr>
</table>

<br/>

<?php 
//display products details in a table
if (isset($ecdoc_id) AND ($ecdoc_id > 0)) {
	F_display_document_details($ecdoc_id, true, true); 
}
?>

</form>
<!-- ====================================================== -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
