<?php
//============================================================+
// File name   : cp_functions_ec_shopping_cart.php             
// Begin       : 2002-08-21                                    
// Last Update : 2005-06-27                                    
//                                                             
// Description : Functions for shopping cart (ecommerce)       
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
// Garbage Collector for shopping cart
// remove expired entries
// ----------------------------------------------------------
function F_shopping_cart_gc() {
	global $db, $l;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	
	$expiry = time() - K_SHOPPING_CART_LIFE; //expiration time
	//delete expired entries
	$sql = "DELETE FROM ".K_TABLE_EC_SHOPPING_CART." WHERE cart_datetime<='".$expiry."'";
	if(!$r = F_aiocpdb_query($sql, $db)) {
		return FALSE;
	}
	
	//delete expired entries
	$sql = "DELETE FROM ".K_TABLE_EC_SHOPPING_CART_USER_DATA." WHERE scud_datetime<='".$expiry."'";
	if(!$r = F_aiocpdb_query($sql, $db)) {
		return FALSE;
	}
}

// ----------------------------------------------------------
// Update shopping cart entries and shopping cart user data
// ----------------------------------------------------------
function F_shopping_cart_refresh($session_id, $user_id) {
	global $db, $l;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	
	F_shopping_cart_gc(); //garbage collector
	
	if ($user_id > 1) { //if the session is not anonymous
		//convert current anonymous sessions entries to current user_id
		$sql = "UPDATE IGNORE ".K_TABLE_EC_SHOPPING_CART." SET cart_user_id='".$user_id."' WHERE (cart_session_id='".$session_id."' AND cart_user_id=1)";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			return FALSE;
		}
		
		$sql = "UPDATE IGNORE ".K_TABLE_EC_SHOPPING_CART_USER_DATA." SET scud_user_id='".$user_id."' WHERE (scud_session_id='".$session_id."' AND scud_user_id=1) LIMIT 1";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			return FALSE;
		}
		
		//convert old user_id sessions to current session
		$sql = "UPDATE IGNORE ".K_TABLE_EC_SHOPPING_CART." SET cart_session_id='".$session_id."' WHERE cart_user_id='".$user_id."'";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			return FALSE;
		}
		
		$sql = "UPDATE IGNORE ".K_TABLE_EC_SHOPPING_CART_USER_DATA." SET scud_session_id='".$session_id."' WHERE scud_user_id='".$user_id."' LIMIT 1";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			return FALSE;
		}
	}
	
	//update current session time
	$sql = "UPDATE IGNORE ".K_TABLE_EC_SHOPPING_CART." SET cart_datetime=".time()." WHERE cart_session_id='".$session_id."'";
	if(!$r = F_aiocpdb_query($sql, $db)) {
		return FALSE;
	}
	
	$sql = "UPDATE IGNORE ".K_TABLE_EC_SHOPPING_CART_USER_DATA." SET scud_datetime=".time()." WHERE cart_session_id='".$session_id."'";
	if(!$r = F_aiocpdb_query($sql, $db)) {
		return FALSE;
	}
	return TRUE;
}

// ----------------------------------------------------------
// display shopping cart
// ----------------------------------------------------------
function F_display_shopping_cart($session_id, $user_id, $new_product_id) {
	global $db, $l, $selected_language, $menu_mode, $aiocp_dp;
	global $shipping_type_id, $payment_type_id, $comment;
	global $prod_quantity;
		
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	require_once('../../shared/code/cp_functions_ec_payment.'.CP_EXT);
	require_once('../../shared/code/cp_functions_user.'.CP_EXT);
	require_once('../../shared/code/cp_functions_ec_vat.'.CP_EXT);
	
	$currentuserdata = F_get_user_document_data($user_id);
	if (!$currentuserdata) {
		//display warning message
		echo $l['m_incomplete_user_data'];
	}	
		
	$general_discount = K_EC_GENERAL_DISCOUNT + F_get_user_discount($user_id);
	
	//refresh shopping cart data
	F_shopping_cart_refresh($session_id, $user_id);
	
	$create_user_cart = false;
	$scud_locked = true;
	
	//read shopping cart data
	$sql = "SELECT * FROM ".K_TABLE_EC_SHOPPING_CART_USER_DATA." WHERE scud_user_id='".$user_id."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			if (!isset($payment_type_id)) {
				$payment_type_id = $m['scud_payment_type_id'];
			}
			if (!isset($shipping_type_id)) {
				$shipping_type_id = $m['scud_shipping_type_id'];
			}
			if (!isset($comment)) {
				$comment = $m['scud_comment'];
			}
			$scud_locked = $m['scud_locked'];
		}
		else {
			$create_user_cart = true;
			$scud_locked = false;
		}
	}
	else {
		F_display_db_error();
	}
	
	// get shipping_zone details
	$address = F_get_user_shipping_address($user_id);
	if ($address) {
		//data for shipping costs calculations
		$shipping_state = $address['address_state'];
		$shipping_postcode = $address['address_postcode'];
		$shipping_country = $address['address_countryid'];
	}
	
	if (!$scud_locked) {
		//add new product to shopping cart
		if ($new_product_id) {
			//search if product has been already added
			$sqlqu = "SELECT * FROM ".K_TABLE_EC_SHOPPING_CART." 
				WHERE (
					cart_session_id='".$session_id."' AND 
					cart_user_id='".$user_id."' AND 
					cart_product_id='".$new_product_id."'
				) 
				LIMIT 1";
			if($rqu = F_aiocpdb_query($sqlqu, $db)) {
				if(!F_aiocpdb_fetch_array($rqu)) { // add new product
					$sqln = "INSERT IGNORE INTO ".K_TABLE_EC_SHOPPING_CART." (
						cart_datetime,
						cart_user_id,
						cart_session_id,
						cart_product_id,
						cart_quantity
					) VALUES (
						'".time()."',
						'".$user_id."',
						'".$session_id."',
						'".$new_product_id."',
						'1'
					)";
					if(!F_aiocpdb_query($sqln, $db)) {
						F_display_db_error();
					}
				}
			}
			else {
				F_display_db_error();
			}
		}
		
		//update products quantity
		if (isset($prod_quantity)) {
			while(list($key, $val) = each($prod_quantity)) { //for each product quantity
				if ($val > 0) {
					$sqlu = "UPDATE IGNORE ".K_TABLE_EC_SHOPPING_CART." SET cart_quantity='".$val."' WHERE cart_id ='".$key."' AND cart_session_id='".$session_id."' LIMIT 1";
				}
				else { //quantity==0 => delete entry
					$sqlu = "DELETE FROM ".K_TABLE_EC_SHOPPING_CART." WHERE cart_id='".$key."' AND cart_session_id='".$session_id."' LIMIT 1";
				}
				if(!$ru = F_aiocpdb_query($sqlu, $db)) {
					F_display_db_error();
				}
			}
		}
		
		//update shopping cart user data
		if (isset($payment_type_id) OR isset($shipping_type_id) OR isset($comment)) {
			$sqlu = "UPDATE IGNORE ".K_TABLE_EC_SHOPPING_CART_USER_DATA." SET
				scud_user_id='".$user_id."',
				scud_payment_type_id='".$payment_type_id."',
				scud_shipping_type_id='".$shipping_type_id."',
				scud_comment='".$comment."',
				scud_datetime='".time()."' 
				WHERE scud_session_id='".$session_id."'";
			if(!$ru = F_aiocpdb_query($sqlu, $db)) {
				F_display_db_error();
			}
		}
	} //end if locked

	switch($menu_mode) {
		case unhtmlentities($l['w_order_now']):
		case $l['w_order_now']: { //Send data to order module
			if ($currentuserdata) { //check if all user needed data are available
				// generate unique transaction ID
				$transaction_id = F_generate_unique_code($user_id.K_RANDOM_SECURITY);
				//lock shopping cart
				$sql = "UPDATE IGNORE ".K_TABLE_EC_SHOPPING_CART_USER_DATA." SET 
				scud_locked=1,
				scud_transaction_id='".$transaction_id."'
				WHERE scud_user_id='".$user_id."' LIMIT 1";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					return FALSE;
				}
				
				if ($payment_type_id) {
					$sql_payment = "SELECT paytype_file_module FROM ".K_TABLE_EC_PAYMENTS_TYPES." WHERE paytype_id=".$payment_type_id." LIMIT 1";
					if($r_payment = F_aiocpdb_query($sql_payment, $db)) {
						if($m_payment = F_aiocpdb_fetch_array($r_payment)) {
							//load selected payment module
							if ($m_payment['paytype_file_module']) {
								$user_data = F_get_user_data($user_id); //get user data
								require_once(K_PATH_FILES_PAYMENT_MODULES.$m_payment['paytype_file_module']);
								exit();
								// NOTE:
								// the payment module interrupt this script from here,
								// in this case the order generation will be called from payment module.
							}
						}
					}
					else {
						F_display_db_error();
					}
				}
				F_generate_order($user_id, $transaction_id); // generate order from shopping cart
				break;
			}
		}
	}
?>

<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_shoppingcart" id="form_shoppingcart">

<!-- DISPLAY SHOPPING CART DETAILS -->
<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT  ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_payment', 'h_paytype_select'); ?></b></td>
<td class="fillOE">
<select name="payment_type_id" id="payment_type_id" size="0" onchange="document.form_shoppingcart.submit()">
<?php
	$sql = "SELECT * FROM ".K_TABLE_EC_PAYMENTS_TYPES." WHERE paytype_enabled=1 ORDER BY paytype_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			if (!$payment_type_id) {
				$payment_type_id = $m['paytype_id'];
			}
			$select_name = unserialize($m['paytype_name']);
			echo "<option value=\"".$m['paytype_id']."\"";
			if($m['paytype_id'] == $payment_type_id) {
				echo " selected=\"selected\"";
			}
			echo ">".htmlentities($select_name[$selected_language], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
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

<!-- SELECT  ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_shipping', 'h_shipping_select'); ?></b></td>
<td class="fillEE">
<select name="shipping_type_id" id="shipping_type_id" size="0" onchange="document.form_shoppingcart.submit()">
<?php
	$sql = "SELECT * FROM ".K_TABLE_EC_SHIPPING_TYPES." WHERE shipping_enabled=1 ORDER BY shipping_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			if (!$shipping_type_id) {
				$shipping_type_id = $m['shipping_id'];
			}
			$select_name = unserialize($m['shipping_name']);
			echo "<option value=\"".$m['shipping_id']."\"";
			if($m['shipping_id'] == $shipping_type_id) {
				echo "selected=\"selected\"";
			}
			echo ">".htmlentities($select_name[$selected_language], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
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
<td class="fillOO" align="right" valign="top"><b><?php echo F_display_field_name('w_comment', 'h_comment'); ?></b></td>
<td class="fillOE">
<?php $doc_charset = F_word_language($selected_language, "a_meta_charset"); ?>
<textarea cols="40" rows="4" name="comment" id="comment"><?php echo htmlentities(stripslashes($comment), ENT_NOQUOTES, $doc_charset); ?></textarea>
</td>
</tr>

</table>

</td>
</tr>
</table>
<?php
//create a new user shopping cart data
if ($create_user_cart) {
	$sqlr = "REPLACE INTO ".K_TABLE_EC_SHOPPING_CART_USER_DATA." (
		scud_session_id, 
		scud_user_id, 
		scud_payment_type_id, 
		scud_shipping_type_id, 
		scud_comment,
		scud_datetime,
		scud_locked
		) VALUES (
		'".$session_id."', 
		'".$user_id."', 
		'".$payment_type_id."', 
		'".$shipping_type_id."', 
		'".$comment."',
		'".time()."',
		'0'
		)";
	if(!$rr = F_aiocpdb_query($sqlr, $db)) {
		F_display_db_error();
	}
}
?>
<br/>

<!-- DISPLAY SHOPPING CART DETAILS -->
<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">
<tr class="fill">
<th class="fillE"><?php echo $l['w_code']; ?></th>
<th class="fillO"><?php echo $l['w_product']; ?></th>
<th class="fillE"><?php echo $l['w_unit']; ?></th>
<th class="fillO"><?php echo $l['w_quantity']; ?></th>
<th class="fillE"><?php echo $l['w_cost_per_unit']." [".K_MONEY_CURRENCY_UNICODE_SYMBOL."]"; ?></th>
<th class="fillO"><?php echo $l['w_total_net']." [".K_MONEY_CURRENCY_UNICODE_SYMBOL."]"; ?></th>
<th class="fillE"><?php echo $l['w_ec_tax']." [%]"; ?></th>
<?php
	if (K_EC_DISPLAY_TAX_2) {
		echo "<th class=\"fillO\">".$l['w_ec_tax2']." [%]</th>";
	}
	if (K_EC_DISPLAY_TAX_3) {
		echo "<th class=\"fillE\">".$l['w_ec_tax3']." [%]</th>";
	}
?>
<th class="fillO"><?php echo $l['w_weight']." [Kg]"; ?></th>
<th class="fillE"><?php echo $l['w_volume']." [m&sup3;]"; ?></th>
</tr>

<!-- SELECT ==================== -->
<?php
//initialize variables
$rowclass = "O";
$rowodd = 0;

$total_net = 0;
$total_tax = 0;
$total_tax1 = 0;
$total_tax2 = 0;
$total_tax3 = 0;
$total_weight = 0; //total weight in Kg
$total_volume = 0; //total volume in m^3
$total_items = 0; //transportable items

$sqlsc = "SELECT * FROM ".K_TABLE_EC_SHOPPING_CART." WHERE cart_session_id='".$session_id."'";
if (isset($user_id) AND ($user_id > 1)) {
	$sqlsc .= " AND cart_user_id='".$user_id."'";
}
if($rsc = F_aiocpdb_query($sqlsc, $db)) {
	while($msc = F_aiocpdb_fetch_array($rsc)) {
		//get product info
		$sql = "SELECT * FROM ".K_TABLE_EC_PRODUCTS." WHERE product_id='".$msc['cart_product_id']."' LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				
				if($m['product_transportable']) { //count transportable items
					$total_items++;
				}
				
				//change style for each row
				if (isset($rowodd) AND ($rowodd)) {
					$rowclass = "O";
					$rowodd = 0;
				} else {
					$rowclass = "E";
					$rowodd = 1;
				}
				
				echo "<tr class=\"fill".$rowclass."\">";
				echo "<td class=\"fill".$rowclass."O\">&nbsp;";
				echo "<a href=\"cp_show_ec_products.".CP_EXT."?pid=".$m['product_id']."\">".htmlentities($m['product_code'], ENT_NOQUOTES, $l['a_meta_charset'])."</a>";
				echo "</td>";
				
				echo "<td class=\"fill".$rowclass."E\">&nbsp;";
				echo "<a href=\"cp_show_ec_products.".CP_EXT."?pid=".$m['product_id']."\">".htmlentities($m['product_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</a>";
				echo "</td>";
				
				echo "<td class=\"fill".$rowclass."O\" align=\"right\">&nbsp;";
				$sqlu = "SELECT * FROM ".K_TABLE_EC_UNITS_OF_MEASURE." WHERE unit_id=".$m['product_unit_of_measure_id']." LIMIT 1";
				if($ru = F_aiocpdb_query($sqlu, $db)) {
					if($mu = F_aiocpdb_fetch_array($ru)) {
						echo htmlentities($mu['unit_name'], ENT_NOQUOTES, $l['a_meta_charset']);
					}
				}
				else {
					F_display_db_error();
				}
				echo "</td>";
				
				echo "<td class=\"fill".$rowclass."E\" align=\"right\"><input type=\"text\" name=\"prod_quantity[".$msc['cart_id']."]\" id=\"prod_quantity_".$msc['cart_id']."\" value=\"".$msc['cart_quantity']."\" size=\"6\" maxlength=\"10\" /></td>";
				
				echo "<td class=\"fill".$rowclass."O\" align=\"right\">&nbsp;".F_FormatCurrency($m['product_cost'])."</td>";
				
				$net_amount = $msc['cart_quantity'] * $m['product_cost'];
				$total_net += $net_amount;
				
				echo "<td class=\"fill".$rowclass."O\" align=\"right\">&nbsp;".F_FormatCurrency($net_amount)."</td>";
				
				$vat = F_get_vat_value($m['product_tax'], $user_id);
				$tax_amount = $net_amount * ($vat / 100);
				
				echo "<td class=\"fill".$rowclass."E\" align=\"right\">&nbsp;".$vat."</td>";
				
				if (K_EC_DISPLAY_TAX_2){
					if ($m['product_tax2']>1) {
						$vat2 = F_get_vat_value($m['product_tax2'], $user_id);
						$tax_amount2 = $net_amount * ($vat2 / 100);
					}
					else {
						$vat2 = "";
						$tax_amount2 = 0;
					}
					echo "<td class=\"fill".$rowclass."O\" align=\"right\">&nbsp;".$vat2."</td>";
				}
				else {
					$vat2 = "";
					$tax_amount2 = 0;
				}
				
				if (K_EC_DISPLAY_TAX_3) {
					if ($m['product_tax3']>1) {
						$vat3 = F_get_vat_value($m['product_tax3'], $user_id);
						$tax_amount3 = ($net_amount + $tax_amount + $tax_amount2) * ($vat3 / 100);
					}
					else {
						$vat3 = "";
						$tax_amount3 = 0;
					}
					echo "<td class=\"fill".$rowclass."E\" align=\"right\">&nbsp;".$vat3."</td>";
				}
				else {
					$vat3 = "";
					$tax_amount3 = 0;
				}
				
				$total_tax1 += $tax_amount;
				$total_tax2 += $tax_amount2;
				$total_tax3 += $tax_amount3;
				$total_taxes = $tax_amount + $tax_amount2 + $tax_amount3;
				$total_tax += $total_taxes;
								
				$weight_amount = $m['product_weight_per_unit'] * $msc['cart_quantity'];
				$total_weight += $weight_amount;
				echo "<td class=\"fill".$rowclass."O\" align=\"right\">&nbsp;".$weight_amount."</td>";
				
				$volume_amount = ($m['product_length'] * $m['product_width'] * $m['product_height']) * $msc['cart_quantity'];
				$total_volume += $volume_amount;
				echo "<td class=\"fill".$rowclass."E\" align=\"right\">&nbsp;".$volume_amount."</td>";
				echo "</tr>";
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
	$current_colspan = 9;
	if (K_EC_DISPLAY_TAX_2) {$current_colspan += 1;}
	if (K_EC_DISPLAY_TAX_3) {$current_colspan += 1;}
	echo "<tr><th class=\"fill\" colspan=\"".$current_colspan."\"><hr /></th></tr>";
	
	if ($general_discount OR ($shipping_type_id AND ($user_id > 1))) {
		echo "<tr>";
		echo "<td class=\"fillOE\" align=\"right\" colspan=\"5\"><b>".$l['w_subtotal']."</b>&nbsp;</td>";
		echo "<td class=\"fillOO\" align=\"right\">&nbsp;".F_FormatCurrency($total_net)."</td>";
		echo "<td class=\"fillOE\" align=\"right\">&nbsp;".F_FormatCurrency($total_tax1)."</td>";
		if (K_EC_DISPLAY_TAX_2) {
			echo "<td class=\"fillOO\" align=\"right\">&nbsp;".F_FormatCurrency($total_tax2)."</td>";
		}
		if (K_EC_DISPLAY_TAX_3) {
			echo "<td class=\"fillOE\" align=\"right\">&nbsp;".F_FormatCurrency($total_tax2)."</td>";
		}
		echo "<td class=\"fillOO\" align=\"right\">&nbsp;</td>";
		echo "<td class=\"fillOE\" align=\"right\">&nbsp;</td>";
		echo "</tr>";
	}
	
	if ($general_discount) {
		$total_discount_net = - $total_net * ($general_discount / 100);
		$total_discount_tax = - $total_tax * ($general_discount / 100);
		$total_discount_tax1 = - $total_tax1 * ($general_discount / 100);
		$total_discount_tax2 = - $total_tax2 * ($general_discount / 100);
		$total_discount_tax3 = - $total_tax3 * ($general_discount / 100);
		
		$total_net += $total_discount_net;
		$total_tax += $total_discount_tax;
		$total_tax1 += $total_discount_tax1;
		$total_tax2 += $total_discount_tax2;
		$total_tax3 += $total_discount_tax3;
		
		echo "<tr>";
		echo "<td class=\"fillEE\" align=\"right\" colspan=\"5\"><b>".$l['w_discount']." ".$general_discount."%"."</b>&nbsp;</td>";
		echo "<td class=\"fillEO\" align=\"right\">&nbsp;".F_FormatCurrency($total_discount_net)."</td>";
		echo "<td class=\"fillEE\" align=\"right\">&nbsp;".F_FormatCurrency($total_discount_tax1)."</td>";
		if (K_EC_DISPLAY_TAX_2) {
			echo "<td class=\"fillEO\" align=\"right\">&nbsp;".F_FormatCurrency($total_discount_tax2)."</td>";
		}
		if (K_EC_DISPLAY_TAX_3) {
			echo "<td class=\"fillEE\" align=\"right\">&nbsp;".F_FormatCurrency($total_discount_tax3)."</td>";
		}
		echo "<td class=\"fillEO\" align=\"right\">&nbsp;</td>";
		echo "<td class=\"fillEE\" align=\"right\">&nbsp;</td>";
		echo "</tr>";
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
				
				if ($transport_net > 0) {
					$total_net += $transport_net;
					$total_tax += ($transport_tax + $transport_tax2 + $transport_tax3);
					
					echo "<tr>";
					echo "<td class=\"fillOE\" align=\"right\" colspan=\"5\"><b>".$l['w_transport']."</b>&nbsp;</td>";
					echo "<td class=\"fillOO\" align=\"right\">&nbsp;".F_FormatCurrency($transport_net)."</td>";
					echo "<td class=\"fillOE\" align=\"right\">&nbsp;".F_FormatCurrency($transport_tax)."</td>";
					if (K_EC_DISPLAY_TAX_2) {
						echo "<td class=\"fillOO\" align=\"right\">&nbsp;".F_FormatCurrency($transport_tax2)."</td>";
					}
					if (K_EC_DISPLAY_TAX_3) {
						echo "<td class=\"fillOE\" align=\"right\">&nbsp;".F_FormatCurrency($transport_tax3)."</td>";
					}
					echo "<td class=\"fillOO\" align=\"right\">&nbsp;</td>";
					echo "<td class=\"fillOE\" align=\"right\">&nbsp;</td>";
					echo "</tr>";
				}
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
					
					echo "<tr>";
					echo "<td class=\"fillEE\" align=\"right\" colspan=\"5\"><b>".$l['w_payment_fees']."</b>&nbsp;</td>";
					echo "<td class=\"fillEO\" align=\"right\">&nbsp;".F_FormatCurrency($payment_fee)."</td>";
					echo "<td class=\"fillEE\" align=\"right\">&nbsp;".F_FormatCurrency(0)."</td>";
					if (K_EC_DISPLAY_TAX_2) {
						echo "<td class=\"fillEO\" align=\"right\">&nbsp;".F_FormatCurrency(0)."</td>";
					}
					if (K_EC_DISPLAY_TAX_3) {
						echo "<td class=\"fillEE\" align=\"right\">&nbsp;".F_FormatCurrency(0)."</td>";
					}
					echo "<td class=\"fillEO\" align=\"right\">&nbsp;</td>";
					echo "<td class=\"fillEE\" align=\"right\">&nbsp;</td>";
					echo "</tr>";
				}
			}
		}
		else {
			F_display_db_error();
		}
	}
	//end calculate payment costs -----------
	
	echo "<tr>";
	echo "<td class=\"fillOE\" align=\"right\" colspan=\"5\"><b>".$l['w_total']."</b>&nbsp;</td>";
	echo "<td class=\"fillOO\" align=\"right\">&nbsp;".F_FormatCurrency($total_net)."</td>";
	echo "<td class=\"fillOE\" align=\"right\">&nbsp;".F_FormatCurrency($total_tax1)."</td>";
	if (K_EC_DISPLAY_TAX_2) {
			echo "<td class=\"fillOO\" align=\"right\">&nbsp;".F_FormatCurrency($total_tax2)."</td>";
	}
	if (K_EC_DISPLAY_TAX_3) {
		echo "<td class=\"fillOE\" align=\"right\">&nbsp;".F_FormatCurrency($total_tax3)."</td>";
	}
	echo "<td class=\"fillOO\" align=\"right\">&nbsp;".$total_weight."</td>";
	echo "<td class=\"fillOE\" align=\"right\">&nbsp;".$total_volume."</td>";
	echo "</tr>";
	
	echo "<tr><th class=\"fill\" colspan=\"".$current_colspan."\"><hr /></th></tr>";
	
	echo "<tr>";
	echo "<td class=\"fillEO\" align=\"right\" colspan=\"5\"><b>".$l['w_total_to_pay']."</b>&nbsp;</td>";
	echo "<td class=\"fillEE\" align=\"left\" colspan=\"".($current_colspan-5)."\">&nbsp;<b>".K_MONEY_CURRENCY_UNICODE_SYMBOL." ".F_FormatCurrency($total_net + $total_tax)."</b></td>";
	echo "</tr>";
?>
</table>
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
</td>
</tr>
<tr class="edge">
<td class="edge" align="center">
<?php //show buttons
F_submit_button("form_shoppingcart","menu_mode",$l['w_update']);


// 2004-08-20 require additional user level to acquire expensive products
if ((($total_net + $total_tax) >= K_EXPENSIVE_PURHCASE_LIMIT) AND ($_SESSION['session_user_level'] < K_EXPENSIVE_PURHCASE_LEVEL)){
	//display warning message
	echo "<br /><br /><b>".$l['m_insufficient_purchase_level']."</b><br />";
	return;
}
elseif (($user_id > 1) AND (($total_net + $total_tax) > 0)) {
	F_submit_button("form_shoppingcart","menu_mode",$l['w_order_now']); 
}
?>
</td>
</tr>

</table>
<br />
<?php echo "<a href=\"cp_show_ec_products.".CP_EXT."\"><b>&lt;&lt; ".$l['w_products_select']."</b></a>"; ?>

</form>
<?php
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
