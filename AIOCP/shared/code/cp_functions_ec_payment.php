<?php
//============================================================+
// File name   : cp_functions_ec_payment.php                   
// Begin       : 2002-08-29                                    
// Last Update : 2004-09-30                                    
//                                                             
// Description : Common payment functions                      
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

//------------------------------------------------------------
// Update payment details on shopping cart user data
//------------------------------------------------------------
function F_update_payment_details($user_id, $transaction_id, $payment_details) {
	global $l, $db;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	if (empty($user_id)) {return FALSE;}
		
	//update payment details
	if ($payment_details) {
		$scud_locked = 1;
		if (strcmp(substr($payment_details,0,5),"ERROR")==0) { //in case of payment error, unlock the shopping cart
			$scud_locked = 0;
		}
		$sql = "UPDATE IGNORE ".K_TABLE_EC_SHOPPING_CART_USER_DATA." SET 
			scud_locked='".$scud_locked."',
			scud_payment_details='".$payment_details."'
			WHERE scud_user_id='".$user_id."' AND scud_transaction_id='".$transaction_id."'";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
	}
}

//------------------------------------------------------------
// Load page that process shopping cart and generate order
//------------------------------------------------------------
function F_generate_order($user_id, $transaction_id) {
	require_once('../../shared/config/cp_extension.inc');
	global $l, $db;
	
	require_once('../config/cp_config.'.CP_EXT);

	
	if (empty($user_id)) {return FALSE;}
	
	$verifycode = F_generate_verification_code($user_id, 4);
	
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
	echo "//<![CDATA[\n";
	echo "location.replace(\"../../public/code/cp_ec_order.".CP_EXT."?uid=".$user_id."&tid=".$transaction_id."&vc=".$verifycode."\");\n";
	echo "//]]>\n";
	echo "</script>\n";
	
	exit;
}

//------------------------------------------------------------
// Load page that display error message
//------------------------------------------------------------
function F_display_error_page($error_message) {
	require_once('../../shared/config/cp_extension.inc');
	
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
	echo "//<![CDATA[\n";
	echo "location.replace(\"../../public/code/cp_ec_payment_error.".CP_EXT."";
	if ($error_message) {
		echo "?err=".$error_message."";
	}
	echo "\");\n";
	echo "//]]>\n";
	echo "</script>\n";
	
	exit;
}

//------------------------------------------------------------
// Load page that display user order
//------------------------------------------------------------
function F_display_user_order($user_id, $message) {
	require_once('../../shared/config/cp_extension.inc');
	
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
	echo "//<![CDATA[\n";
	echo "location.replace(\"../../public/code/cp_show_ec_documents.".CP_EXT."?uid=".$user_id."";
	if ($message) {
		echo "&mgs=".$message."";
	}
	echo "\");\n";
	echo "//]]>\n";
	echo "</script>\n";
	
	exit;
}

//------------------------------------------------------------
// Calculate total amount to pay from shopping cart
//------------------------------------------------------------
function F_calculate_total_amount($user_id, $transaction_id) {
	global $l, $db;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_ec_vat.'.CP_EXT);
	require_once('../../shared/code/cp_functions_user.'.CP_EXT);
	
	//initialize some variables
	$total_net = 0;
	$total_tax = 0;
	$total_weight = 0; //total weight in Kg
	$total_volume = 0; //total volume in m^3
	$total_items = 0; //transportable items
	
	//read shopping cart user data
	$sql = "SELECT scud_shipping_type_id, scud_payment_type_id FROM ".K_TABLE_EC_SHOPPING_CART_USER_DATA." WHERE scud_user_id='".$user_id."' AND scud_transaction_id='".$transaction_id."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$shipping_type_id = $m['scud_shipping_type_id'];
			$payment_type_id = $m['scud_payment_type_id'];
		}
	}
	else {
		F_display_db_error();
	}
	
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
	
	//totals
	$general_discount = K_EC_GENERAL_DISCOUNT + F_get_user_discount($user_id);
	if ($general_discount) {
		$total_discount_net = - $total_net * ($general_discount / 100);
		$total_discount_tax = - $total_tax * ($general_discount / 100);
		$total_net += $total_discount_net;
		$total_tax += $total_discount_tax;
	}
	
	//calculate shipping costs -----------
	if ($shipping_type_id AND ($user_id > 1)) {
		$sql_shipping = "SELECT shipping_file_module FROM ".K_TABLE_EC_SHIPPING_TYPES." WHERE shipping_id=".$shipping_type_id." LIMIT 1";
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
			}
		}
		else {
			F_display_db_error();
		}
	}
	//end calculate shipping costs -----------
	
	//calculate payment costs -----------
	if ($payment_type_id AND ($user_id > 1)) {
		$sql_payment = "SELECT paytype_fee, paytype_feepercentage FROM ".K_TABLE_EC_PAYMENTS_TYPES." WHERE paytype_id=".$payment_type_id." LIMIT 1";
		if($r_payment = F_aiocpdb_query($sql_payment, $db)) {
			if($m_payment = F_aiocpdb_fetch_array($r_payment)) {
				if ( ($m_payment['paytype_fee'] > 0) OR ($m_payment['paytype_feepercentage'] > 0) ) {
					$payment_fee = 0;
					if ($m_payment['paytype_feepercentage'] > 0) {
						$payment_fee = (($total_net + $total_tax) *($m_payment['paytype_fee']/100));
					}
					if ($m_payment['paytype_fee'] > 0) {
						$payment_fee += $m_payment['paytype_fee'];
					}
					
					$total_net += $payment_fee;
				}
			}
		}
		else {
			F_display_db_error();
		}
	}
	//end calculate payment costs -----------
	
	$total_to_pay = $total_net + $total_tax;
	
	return $total_to_pay;
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
