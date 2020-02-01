<?php
//============================================================+
// File name   : cp_functions_ec_order.php                     
// Begin       : 2002-08-21                                    
// Last Update : 2004-12-22                                    
//                                                             
// Description : Functions for orders (ecommerce)              
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

// ----------------------------------------------------------
// create order from user shopping cart
// ----------------------------------------------------------
function F_create_new_ec_order($user_id, $transaction_id, $display_doc=false, $send_email=false, $paid=false) {
	global $db, $l, $selected_language;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	require_once('../../shared/code/cp_functions_ec_documents.'.CP_EXT);
	require_once('../../shared/code/cp_functions_user.'.CP_EXT);
	require_once('../../shared/code/cp_functions_ec_vat.'.CP_EXT);
	
	$currentuserdata = F_get_user_document_data($user_id);
	if ($currentuserdata) {
		$ecdoc_user_data = addslashes(serialize($currentuserdata));
	}
	else {
		$ecdoc_user_data = "";
	}
	
	$general_discount = K_EC_GENERAL_DISCOUNT + F_get_user_discount($user_id);
	
	//read shopping cart user data
	$sql = "SELECT * FROM ".K_TABLE_EC_SHOPPING_CART_USER_DATA." WHERE 
	scud_user_id='".$user_id."' AND scud_transaction_id='".$transaction_id."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if (!$scud = F_aiocpdb_fetch_array($r)) {
			return false;
		}
	}
	else {
		F_display_db_error();
	}
	
	//set document data
	$ecdoc_type = K_EC_ORDER_DOC_ID; //order
	//get document name
	$sqln = "SELECT doctype_name FROM ".K_TABLE_EC_DOCUMENTS_TYPES." WHERE doctype_id='".$ecdoc_type."' LIMIT 1";
	if($rn = F_aiocpdb_query($sqln, $db)) {
		if($mn = F_aiocpdb_fetch_array($rn)) {
			$select_name = unserialize($mn['doctype_name']);
			$document_type = $select_name[$selected_language];
		}
	}
	else {
		F_display_db_error();
	}
	
	//document auto numbering
	$ecdoc_number = "";
	$sql = "SELECT COUNT(*) FROM ".K_TABLE_EC_DOCUMENTS." WHERE YEAR(ecdoc_date)='".gmdate('Y')."' AND ecdoc_type='".$ecdoc_type."'";
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
	
	$ecdoc_date = gmdate("Y-m-d"); //document date
	
	if ($paid) {
		$ecdoc_paid = 1;
		$ecdoc_payment_date = $ecdoc_date;
	}
	else {
		$ecdoc_paid = 0;
		$ecdoc_payment_date = "";
	}
	
	// generate order document from shopping cart
	$sqli = "INSERT IGNORE INTO ".K_TABLE_EC_DOCUMENTS." (
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
		'".$user_id."',
		'".$ecdoc_user_data."',
		'".$scud['scud_payment_type_id']."',
		'".addslashes($scud['scud_payment_details'])."',
		'".$ecdoc_payment_date."',
		'".$ecdoc_paid."',
		'',
		'',
		'".$general_discount."',
		'',
		'',
		'".$scud['scud_shipping_type_id']."',
		'".K_EC_ORDER_SUBJECT."',
		'".K_EC_ORDER_INTRO."',
		'".K_EC_ORDER_FOOTER."\n\n".addslashes($l['w_user_comment']).":\n".addslashes($scud['scud_comment'])."',
		'',
		'',
		'',
		'',
		'',
		'',
		'',
		'0',
		'0',
		'0',
		'0',
		'0',
		'".(K_EC_ORDER_EXPIRY_TIME + time())."',
		'0'
		)";
	if($ri = F_aiocpdb_query($sqli, $db)) {
		$ecdoc_id = F_aiocpdb_insert_id();
	}
	else {
		F_display_db_error();
	}
	
	$total_net = 0;
	$total_tax = 0;
	$total_weight = 0; //total weight in Kg
	$total_volume = 0; //total volume in m^3
	$total_items = 0; //transportable items
	
	$executable_module = Array(); //array of executable modules
	
	//generate documents details
	
	$sqlsc = "SELECT * FROM ".K_TABLE_EC_SHOPPING_CART." WHERE cart_user_id='".$user_id."'";
	if($rsc = F_aiocpdb_query($sqlsc, $db)) {
		while($msc = F_aiocpdb_fetch_array($rsc)) {
			//get product info
			$sql = "SELECT * FROM ".K_TABLE_EC_PRODUCTS." WHERE product_id='".$msc['cart_product_id']."' LIMIT 1";
			if($r = F_aiocpdb_query($sql, $db)) {
				if($m = F_aiocpdb_fetch_array($r)) {
					
					if($m['product_transportable']) { //count transportable items
						$total_items++;
					}
					
					//calculate totals
					$net_amount = $msc['cart_quantity'] * $m['product_cost'];
					$total_net += $net_amount;
					$vat = F_get_vat_value($m['product_tax'], $user_id);
					$tax_amount = $net_amount * ($vat / 100);
					
					if ($m['product_tax2']>1) {
						$vat2 = F_get_vat_value($m['product_tax2'], $user_id);
						$tax_amount2 = $net_amount * ($vat2 / 100);
					}
					else {
						$vat2 = NULL;
						$tax_amount2 = 0;
					}
					
					if ($m['product_tax3']>1) {
						$vat3 = F_get_vat_value($m['product_tax3'], $user_id);
						$tax_amount3 = ($net_amount + $tax_amount + $tax_amount2) * ($vat3 / 100);
					}
					else {
						$vat3 = NULL;
						$tax_amount3 = 0;
					}
					
					$total_tax += ($tax_amount + $tax_amount2 + $tax_amount3);
					$weight_amount = $m['product_weight_per_unit'] * $msc['cart_quantity'];
					$total_weight += $weight_amount;
					$volume_amount = ($m['product_length'] * $m['product_width'] * $m['product_height']) * $msc['cart_quantity'];
					$total_volume += $volume_amount;
					
					if (isset($m['product_execute_module']) AND $m['product_execute_module']) {
						$executable_module[] = $m['product_execute_module'];
					}
					
					//add document detail
					$sqlupdd = "INSERT IGNORE INTO ".K_TABLE_EC_DOCUMENTS_DETAILS." (
					docdet_doc_id,
					docdet_product_id,
					docdet_code,
					docdet_manufacturer_code,
					docdet_barcode,
					docdet_inventory_code,
					docdet_alternative_codes,
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
					'".$m['product_id']."',
					'".$m['product_code']."',
					'".$m['product_manufacturer_code']."',
					'".$m['product_barcode']."',
					'".$m['product_inventory_code']."',
					'".$m['product_alternative_codes']."',
					'".$m['product_category_id']."',
					'".$m['product_manufacturer_id']."',
					'".$m['product_manufacturer_link']."',
					'".$m['product_name']."',
					'".addslashes($m['product_description'])."',
					'".$m['product_warranty']."',
					'".$m['product_warranty_id']."',
					'".$m['product_image']."',
					'".$m['product_transportable']."',
					'".$m['product_download_link']."',
					'".$m['product_weight_per_unit']."',
					'".$m['product_length']."',
					'".$m['product_width']."',
					'".$m['product_height']."',
					'".$m['product_unit_of_measure_id']."',
					'".$m['product_cost']."',
					'".$vat."',
					'".$vat2."',
					'".$vat3."',
					'".$msc['cart_quantity']."',
					'".$m['product_discount']."'
					)";
					if(!$rupdd = F_aiocpdb_query($sqlupdd, $db)) {
						F_display_db_error();
					}
					
					// update product available quantity (statistics)
					//sold quantity will be updated on invoice creation
					//product_q_sold='".($m['product_q_sold'] + $msc['cart_quantity'])."',
					if ($m['product_q_available']) {
						$sqluq = "UPDATE IGNORE ".K_TABLE_EC_PRODUCTS." SET 
						product_q_available='".($m['product_q_available'] - $msc['cart_quantity'])."'
						WHERE product_id='".$m['product_id']."'";
						if(!$ruq = F_aiocpdb_query($sqluq, $db)) {
							F_display_db_error();
						}
					}
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
	
	if ($general_discount) {
		$total_discount_net = - $total_net * ($general_discount / 100);
		$total_discount_tax = - $total_tax * ($general_discount / 100);
		$total_net += $total_discount_net;
		$total_tax += $total_discount_tax;
	}
	
	
	// get shipping_zone details
	//data for shipping costs calculations
	$shipping_state = stripslashes($ecdoc_user_data->state);
	$shipping_postcode = stripslashes($ecdoc_user_data->postcode);
	$shipping_country = stripslashes($ecdoc_user_data->country_id);
	
	//calculate shipping costs -----------
	if ($scud['scud_shipping_type_id']) {
		$sql_shipping = "SELECT shipping_file_module FROM ".K_TABLE_EC_SHIPPING_TYPES." WHERE shipping_id=".$scud['scud_shipping_type_id']." LIMIT 1";
		if($r_shipping = F_aiocpdb_query($sql_shipping, $db)) {
			if($m_shipping = F_aiocpdb_fetch_array($r_shipping)) {
				//load selected shipping module
				require_once(K_PATH_FILES_SHIPPING_MODULES.$m_shipping['shipping_file_module']);
				
				if ((!isset($transport_net)) OR (!$transport_net)) {
					$transport_net = 0;
				}
				if ((!isset($transport_tax)) OR (!$transport_tax)) {
					$transport_tax = 0;
				}
				if ((!isset($transport_tax2)) OR (!$transport_tax2)) {
					$transport_tax2 = 0;
				}
				if ((!isset($transport_tax3)) OR (!$transport_tax3)) {
					$transport_tax3 = 0;
				}
				
				$total_net += $transport_net;
				$total_tax += ($transport_tax + $transport_tax2 + $transport_tax3);
				
				
				//update transport costs
				$sql = "UPDATE IGNORE ".K_TABLE_EC_DOCUMENTS." SET 
					ecdoc_transport_subject='".$l['w_sale']."',
					ecdoc_carriage='".$transport_carriage."',
					ecdoc_transport_net='".$transport_net."',
					ecdoc_transport_tax='".$transport_tax."',
					ecdoc_transport_tax2='".$transport_tax2."',
					ecdoc_transport_tax3='".$transport_tax3."'
					WHERE ecdoc_id=".$ecdoc_id."";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
			}
		}
		else {
			F_display_db_error();
		}
	}
	//end calculate shipping costs -----------
	
	//calculate payment costs -----------
	if ($scud['scud_payment_type_id']) {
		$sql_payment = "SELECT paytype_fee, paytype_feepercentage FROM ".K_TABLE_EC_PAYMENTS_TYPES." WHERE paytype_id=".$scud['scud_payment_type_id']." LIMIT 1";
		if($r_payment = F_aiocpdb_query($sql_payment, $db)) {
			if($m_payment = F_aiocpdb_fetch_array($r_payment)) {
				$payment_fee = 0;
				if ($m_payment['paytype_feepercentage'] > 0) {
					$payment_fee = (($total_net + $total_tax) *($m_payment['paytype_fee']/100));
				}
				if ($m_payment['paytype_fee'] > 0) {
					$payment_fee += $m_payment['paytype_fee'];
				}
				$total_net += $payment_fee;
				
				//update payment costs
				$sql = "UPDATE IGNORE ".K_TABLE_EC_DOCUMENTS." SET 
					ecdoc_payment_fee='".$payment_fee."'
					WHERE ecdoc_id=".$ecdoc_id."";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
			}
		}
		else {
			F_display_db_error();
		}
	}
	//end calculate payment costs -----------
	
	// display document details
	if ($display_doc) {
		// display confirmation message ...
		echo "<p>".$l['m_order_processed']."</p>";
		
		F_display_document_details($ecdoc_id, false, true);
	}
	
	// send order document email to user
	if ($send_email) {
		$message = "";
		if ($paid) { //if this order has been paid
			$message = F_get_downloadable_files_links($ecdoc_id); //get links list of downloadable files 
		}
		F_send_pdfdoc_email($ecdoc_id, $user_id, $document_type, $ecdoc_number, $ecdoc_date, $message);
	}
	
	// delete shopping cart user data
	$sql = "DELETE FROM ".K_TABLE_EC_SHOPPING_CART_USER_DATA." WHERE scud_user_id='".$user_id."'";
	if(!$r = F_aiocpdb_query($sql, $db)) {
		F_display_db_error();
	}
	$sql = "DELETE FROM ".K_TABLE_EC_SHOPPING_CART." WHERE cart_user_id='".$user_id."'";
	if(!$r = F_aiocpdb_query($sql, $db)) {
		F_display_db_error();
	}
	
	//execute PHP molues asscociated with products (if any)
	if ($paid AND isset($executable_module) AND (count($executable_module)>0)) { //if this order has been paid
		while(list($mkey, $current_module) = each($executable_module)) { //for each module on array
			$sqlcm = "SELECT * FROM ".K_TABLE_PAGE_MODULES."";
				if($rcm = F_aiocpdb_query($sqlcm, $db)) {
					while($mcm = F_aiocpdb_fetch_array($rcm)) {	
						if (!$mcm['pagemod_params']) { //modules without parameters
							$template_name = "#".strtoupper($mcm['pagemod_name'])."#";
							if (strcmp($current_module, $template_name) == 0) {
								$code_to_evaluate = $mcm['pagemod_code'];
								eval($code_to_evaluate); //evaluate code
							}
						}
						else { //there are parameters on template
							//build template search string
							$template_name = "\#".strtoupper($mcm['pagemod_name'])."=";
							for ($i=1; $i<=$mcm['pagemod_params']; $i++) {
								$template_name .= "([A-Za-z0-9_]*),";
							}
							$template_name = substr($template_name, 0, -1); //remove trailing comma
							$template_name .= "\#";
							
							if (ereg($template_name, $current_module, $cregs)) {
								$code_to_evaluate = $mcm['pagemod_code'];
								//substitute parameters in the code block
								while(list($ckey, $cval) = each($cregs)) {
								if ($ckey) {
									$code_to_evaluate = str_replace("#P".$ckey."#", $cval, $code_to_evaluate);
								}
							}
							eval($code_to_evaluate); //evaluate code
						}
					}
				}
			}
			else {
				F_display_db_error();
			}
		}
	}
	
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
