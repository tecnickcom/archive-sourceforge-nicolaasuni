<?php
//============================================================+
// File name   : cp_functions_ec_documents.php
// Begin       : 2002-08-03
// Last Update : 2008-08-10
// 
// Description : Display Documents details and totals
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
// display select options for user documents
// ----------------------------------------------------------
function F_display_select_document_details($user_id) {
	global $db, $l, $selected_language, $aiocp_dp;
	global $changetype, $ecdoc_type, $changeyear, $tyear, $changedoc, $ecdoc_id, $only_pending;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	require_once('../../shared/code/cp_functions_user.'.CP_EXT);
	
	// Initialize variables
		
	if ((isset($changetype) AND $changetype) OR (isset($changeyear) AND $changeyear)) {
		$ecdoc_id = FALSE;
	}
	
	$currentyear = gmdate('Y');
	$firstyear = $currentyear;
	$sql = "SELECT MIN(ecdoc_date) FROM ".K_TABLE_EC_DOCUMENTS." WHERE ecdoc_user_id=".$user_id."";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$firstyear = gmdate('Y',strtotime($m[0]));
		}
	}
	else {
		F_display_db_error();
	}
	
	if ( (!isset($tyear)) OR ($tyear < $firstyear)) {
		$tyear = $currentyear;
	}
	
	if ( (!isset($ecdoc_type)) OR (!$ecdoc_type)) {
		$ecdoc_type = K_EC_ORDER_DOC_ID;
		$only_pending = 1;
	}
	
	//sql where condition to select only pending orders
	if (isset($only_pending) AND $only_pending) {
		$whereselect = "AND ecdoc_expiry_time>0";
	}
	else {
		$whereselect = "";
	}
	
	if($formstatus) {
		if(!$ecdoc_id) {$sql = "SELECT * FROM ".K_TABLE_EC_DOCUMENTS." WHERE (YEAR(ecdoc_date)='".$tyear."' AND ecdoc_type='".$ecdoc_type."'".$whereselect.") AND ecdoc_user_id=".$user_id." ORDER BY ecdoc_date DESC LIMIT 1";}
		else {$sql = "SELECT * FROM ".K_TABLE_EC_DOCUMENTS." WHERE ecdoc_id=".$ecdoc_id." AND ecdoc_user_id=".$user_id." LIMIT 1";}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$ecdoc_id = $m['ecdoc_id'];
			}
		}
		else {
			F_display_db_error();
		}
	}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_documentselectshow" id="form_documentselectshow">

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT year ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_year', 'h_ecdoc_year'); ?></b></td>
<td class="fillOE">
<input type="hidden" name="changeyear" id="changeyear" value="0" />

<select name="tyear" id="tyear" size="0" onchange="document.form_documentselectshow.changeyear.value=1; document.form_documentselectshow.submit()">
<?php
for ($i=$firstyear; $i<=$currentyear; $i++) {
	echo "<option value=\"".$i."\"";
	if ($tyear == $i) {echo " selected=\"selected\"";}
	echo ">".$i."</option>\n";
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

<select name="ecdoc_type" id="ecdoc_type" size="0" onchange="document.form_documentselectshow.changetype.value=1; document.form_documentselectshow.submit()">
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

<?php
//show an option to display only pending orders
if ($ecdoc_type == K_EC_ORDER_DOC_ID) {
	echo "<tr class=\"fillE\">";
	echo "<td class=\"fillEO\" align=\"right\"><b>";
	echo F_display_field_name('w_only_pending', 'h_doctype_pending');
	echo "</b></td>";
	echo "<td class=\"fillEE\">";
	echo "<input type=\"checkbox\" name=\"only_pending\" id=\"only_pending\" value=\"1\"";
	if (isset($only_pending) AND $only_pending) {
		echo " checked=\"checked\"";
	}
	echo" onclick=\"document.form_documentselectshow.changetype.value=1;document.form_documentselectshow.submit()\" />";
	echo "</td></tr>";
}
?>

<!-- SELECT ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_document', 'h_ecdoc_select'); ?></b></td>
<td class="fillOE">
<input type="hidden" name="changedoc" id="changedoc" value="0" />

<select name="ecdoc_id" id="ecdoc_id" size="0" onchange="document.form_documentselectshow.changedoc.value=1; document.form_documentselectshow.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_EC_DOCUMENTS." WHERE (YEAR(ecdoc_date)='".$tyear."' AND ecdoc_type='".$ecdoc_type."'".$whereselect.") AND ecdoc_user_id=".$user_id." ORDER BY ecdoc_date DESC, ecdoc_number";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['ecdoc_id']."\"";
		if($m['ecdoc_id'] == $ecdoc_id) {
			echo " selected=\"selected\"";
		}
		echo ">".$m['ecdoc_date']." - ".htmlentities($m['ecdoc_number'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
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

</table>

</td></tr>
</table>
 
<br />
<!-- SHOW links ==================== -->
<?php
if ($ecdoc_id) {
	F_display_document_details($ecdoc_id, false, true);
}
?>
<!-- END SHOW links ==================== -->

</form>
<!-- ====================================================== -->
<?php
}

// ----------------------------------------------------------
// display documents details
// ----------------------------------------------------------
function F_display_document_details($ecdoc_id, $show_edit, $show_totals) {
	global $db, $l, $selected_language;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	require_once('../../shared/code/cp_functions_user.'.CP_EXT);
	
	//get main document data
	$sql = "SELECT * FROM ".K_TABLE_EC_DOCUMENTS." WHERE ecdoc_id='".$ecdoc_id."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($md = F_aiocpdb_fetch_array($r)) {
			$ecdoc_number = $md['ecdoc_number'];
			$ecdoc_type = $md['ecdoc_type'];
			$ecdoc_date = $md['ecdoc_date'];
			$total_parcels = $md['ecdoc_parcels'];
			$ecdoc_user_id = $md['ecdoc_user_id'];
			$ecdoc_from_doc_id = $md['ecdoc_from_doc_id'];
			$ecdoc_user_data = unserialize($md['ecdoc_user_data']);
			
			$sql2 = "SELECT * FROM ".K_TABLE_EC_DOCUMENTS_TYPES." WHERE doctype_id='".$ecdoc_type."' LIMIT 1";
			if($r2 = F_aiocpdb_query($sql2, $db)) {
				if($m2 = F_aiocpdb_fetch_array($r2)) {
					$select_name = unserialize($m2['doctype_name']);
					$document_type = $select_name[$selected_language];
					$doc_options = array();
					$doc_options = unserialize($m2['doctype_options']);
					while(list($key, $val) = each($doc_options)) {
						$doc_options[$key] = stripslashes($val);
					}
				}
			}
			else {
				F_display_db_error();
			}
			
			// get shipping_zone details
			//data for shipping costs calculations
			$shipping_state = stripslashes($ecdoc_user_data->state);
			$shipping_postcode = stripslashes($ecdoc_user_data->postcode);
			$shipping_country = stripslashes($ecdoc_user_data->country_id);
		}
	}
	else {
			F_display_db_error();
	}
	
	// START payment data -----------------------------
	$payment_string = "";
	if ($md['ecdoc_payment_type_id']) {
		//get payment type
		$payment_type = "";
		$sql = "SELECT * FROM ".K_TABLE_EC_PAYMENTS_TYPES." WHERE paytype_id=".$md['ecdoc_payment_type_id']." LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$doc_payment = unserialize($m['paytype_name']);
				$payment_type = $doc_payment[$selected_language];
			}
		}
		else {
			F_display_db_error();
		}
		
		$payment_string = "<b>".$l['w_payment'].":</b> ".$payment_type."";
		if ($md['ecdoc_payment_details']) {
			$payment_string .=  " - ".$md['ecdoc_payment_details']."";
		}
	}
	// END payment data -----------------------------
?>

<!-- DISPLAY DOCUMENT'S PRODUCTS LIST -->
<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<th class="edge">
<?php echo "".$ecdoc_date." - ".htmlentities($document_type, ENT_NOQUOTES, $l['a_meta_charset'])." - ".htmlentities($ecdoc_number, ENT_NOQUOTES, $l['a_meta_charset']).""; ?>
</th>
</tr>

<tr class="edge">
<th class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">
<tr class="fill">
<?php 
echo "<th class=\"fillO\">".$l['w_code']."</th>";
echo "<th class=\"fillE\">".$l['w_product']."</th>";
echo "<th class=\"fillO\">".$l['w_unit']."</th>";
if ($ecdoc_type == K_EC_RGA_DOC_ID) {
	echo "<th class=\"fillE\">".$l['w_serial_num']."</th>";
}
else {
	echo "<th class=\"fillE\">".$l['w_quantity']."</th>";
}
if ($doc_options[4]) { //print costs
	echo "<th class=\"fillO\">".$l['w_cost_per_unit']." [".K_MONEY_CURRENCY_UNICODE_SYMBOL."]</th>";
	echo "<th class=\"fillE\">".$l['w_discount']." [%]</th>";
	echo "<th class=\"fillO\">".$l['w_total_net']." [".K_MONEY_CURRENCY_UNICODE_SYMBOL."]</th>";
	echo "<th class=\"fillE\">".$l['w_ec_tax']." [%]</th>";
	if (K_EC_DISPLAY_TAX_2) {
		echo "<th class=\"fillO\">".$l['w_ec_tax2']." [%]</th>";
	}
	if (K_EC_DISPLAY_TAX_3) {
		echo "<th class=\"fillE\">".$l['w_ec_tax3']." [%]</th>";
	}
}
echo "<th class=\"fillO\">".$l['w_weight']." [Kg]</th>";
echo "<th class=\"fillE\">".$l['w_volume']." [m&sup3;]</th>";
if ($ecdoc_type == K_EC_RGA_DOC_ID) {
	echo "<th class=\"fillE\">".$l['w_warranty']."</th>";
}
?>
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

$sql = "SELECT * FROM ".K_TABLE_EC_DOCUMENTS_DETAILS." WHERE docdet_doc_id='".$ecdoc_id."'";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		
		if($m['docdet_transportable']) { //count transportable items
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
		
		$unit_name = "";
		$sqlu = "SELECT * FROM ".K_TABLE_EC_UNITS_OF_MEASURE." WHERE unit_id=".$m['docdet_unit_of_measure_id']." LIMIT 1";
		if($ru = F_aiocpdb_query($sqlu, $db)) {
			if($mu = F_aiocpdb_fetch_array($ru)) {
				$unit_name = $mu['unit_name'];
			}
		}
		else {
			F_display_db_error();
		}
		
		echo "<tr class=\"fill".$rowclass."\">";
		echo "<td class=\"fill".$rowclass."O\">&nbsp;".htmlentities($m['docdet_code'], ENT_NOQUOTES, $l['a_meta_charset'])."</td>";
		
		echo "<td class=\"fill".$rowclass."E\">&nbsp;";
		if ($show_edit) {
			echo "<a href=\"cp_edit_ec_documents_details.".CP_EXT."?docdet_doc_id=".$ecdoc_id."&amp;docdet_id=".$m['docdet_id']."\">".htmlentities($m['docdet_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</a>";
		}
		else {
			echo htmlentities($m['docdet_name'], ENT_NOQUOTES, $l['a_meta_charset']);
		}
		echo "</td>";
		
		echo "<td class=\"fill".$rowclass."O\" align=\"right\">&nbsp;";
		echo htmlentities($unit_name, ENT_NOQUOTES, $l['a_meta_charset']);
		echo "</td>";
		
		if ($ecdoc_type == K_EC_RGA_DOC_ID) {
			echo "<td class=\"fill".$rowclass."E\">&nbsp;".htmlentities($m['docdet_serial_numbers'], ENT_NOQUOTES, $l['a_meta_charset'])."</td>";
		}
		else {
			echo "<td class=\"fill".$rowclass."E\" align=\"right\">&nbsp;".$m['docdet_quantity']."</td>";
		}
		
		if ($doc_options[4]) { //print costs
			echo "<td class=\"fill".$rowclass."O\" align=\"right\">&nbsp;".F_FormatCurrency($m['docdet_cost'])."</td>";
			echo "<td class=\"fill".$rowclass."E\" align=\"right\">&nbsp;".$m['docdet_discount']."</td>";
			
			$net_amount = ($m['docdet_quantity'] * $m['docdet_cost']) * (1 - ($m['docdet_discount'] / 100));
			$total_net += $net_amount;
			echo "<td class=\"fill".$rowclass."O\" align=\"right\">&nbsp;".F_FormatCurrency($net_amount)."</td>";
			
			$tax_amount = $net_amount * ($m['docdet_tax'] / 100);
			echo "<td class=\"fill".$rowclass."E\" align=\"right\">&nbsp;".$m['docdet_tax']."</td>";
			
			if (K_EC_DISPLAY_TAX_2) {
				if (($m['docdet_tax2'] != NULL)) {
					$tax_amount2 = $net_amount * ($m['docdet_tax2'] / 100);
					echo "<td class=\"fill".$rowclass."O\" align=\"right\">&nbsp;".$m['docdet_tax2']."</td>";
				}
				else {
					$tax_amount2 = 0;
					echo "<td class=\"fill".$rowclass."O\" align=\"right\">&nbsp;0</td>";
				}		
			}
			else {
				$tax_amount2 = 0;
			}
			
			if (K_EC_DISPLAY_TAX_3) {
				if (($m['docdet_tax3'] != NULL)) {
					$tax_amount3 = ($net_amount + $tax_amount + $tax_amount2) * ($m['docdet_tax3'] / 100);
					echo "<td class=\"fill".$rowclass."E\" align=\"right\">&nbsp;".$m['docdet_tax3']."</td>";
				}
				else {
					$tax_amount3 = 0;
					echo "<td class=\"fill".$rowclass."E\" align=\"right\">&nbsp;0</td>";
				}		
			}
			else {
				$tax_amount3 = 0;
			}

			$total_tax1 += $tax_amount;
			$total_tax2 += $tax_amount2;
			$total_tax3 += $tax_amount3;
			$total_taxes = $tax_amount + $tax_amount2 + $tax_amount3;
			$total_tax += $total_taxes;
			
			//echo "<td class=\"fill".$rowclass."O\" align=\"right\">&nbsp;".F_FormatCurrency($tax_amount)."</td>";
		}
		
		$weight_amount = $m['docdet_weight_per_unit'] * $m['docdet_quantity'];
		$total_weight += $weight_amount;
		echo "<td class=\"fill".$rowclass."O\" align=\"right\">&nbsp;".$weight_amount."</td>";
		
		$volume_amount = ($m['docdet_length'] * $m['docdet_width'] * $m['docdet_height']) * $m['docdet_quantity'];
		$total_volume += $volume_amount;
		echo "<td class=\"fill".$rowclass."E\" align=\"right\">&nbsp;".$volume_amount."</td>";
		
		// warranty validity
		if ($ecdoc_type == K_EC_RGA_DOC_ID) {
			echo "<td class=\"fill".$rowclass."E\">&nbsp;";
			if (isset($m['docdet_warranty'])) {
				//get request data
				$sqlrd = "SELECT * FROM ".K_TABLE_EC_DOCUMENTS." WHERE ecdoc_id='".$ecdoc_from_doc_id."' LIMIT 1";
				if($rrd = F_aiocpdb_query($sqlrd, $db)) {
					if($mrd = F_aiocpdb_fetch_array($rrd)) {
						$request_date = $mrd['ecdoc_date'];
						
						$elapsed_time = strtotime($ecdoc_date) - strtotime($request_date);
						$warranty_duration = $m['docdet_warranty'] * K_SECONDS_IN_MONTH;
						$difference_days = round(($elapsed_time - $warranty_duration) / K_SECONDS_IN_DAY);
						if ($elapsed_time > $warranty_duration) { // expired warranty
							echo "<b>".$l['w_expired']." (".$difference_days." ".$l['w_days'].")</b>";
						}
						else { // valid warranty
							echo "".$l['w_valid_f']." (".$difference_days." ".$l['w_days'].")";
						}
					}
				}
				else {
					F_display_db_error();
				}
			}
			echo "</td>";
			
			//display reason row
			echo "</tr>";
			echo "<td class=\"fill".$rowclass."O\">&nbsp;</td>";
			
			echo "<td class=\"fill".$rowclass."E\" colspan=\"6\">&nbsp;";
			$doc_charset = F_word_language($selected_language, "a_meta_charset");
			$r_text = unserialize($m['docdet_description']);
			$reason = htmlentities(stripslashes($r_text[$selected_language]), ENT_NOQUOTES, $doc_charset);
			echo "".$reason."";
			echo "</td>";
		}
		
		echo "</tr>";
		
	}
}
else {
	F_display_db_error();
}

if($show_totals AND $doc_options[4]) { //display totals
	
	$current_colspan = 10;
	if (K_EC_DISPLAY_TAX_2) {$current_colspan += 1;};
	if (K_EC_DISPLAY_TAX_3) {$current_colspan += 1;};
	echo "<tr><th class=\"fill\" colspan=\"".$current_colspan."\"><hr /></th></tr>";
	
	if (($md['ecdoc_discount'] > 0) OR (($md['ecdoc_deduction'] > 0) AND ($md['ecdoc_deduction_from'] > 0) )) {
		echo "<tr>";
		echo "<td class=\"fillOE\" align=\"right\" colspan=\"6\"><b>".$l['w_subtotal']."</b>&nbsp;</td>";
		echo "<td class=\"fillOO\" align=\"right\">&nbsp;".F_FormatCurrency($total_net)."</td>";
		echo "<td class=\"fillOE\" align=\"right\">&nbsp;".F_FormatCurrency($total_tax1)."</td>";
		if (K_EC_DISPLAY_TAX_2) {
			echo "<td class=\"fillOO\" align=\"right\">&nbsp;".F_FormatCurrency($total_tax2)."</td>";
		}
		if (K_EC_DISPLAY_TAX_3) {
			echo "<td class=\"fillOE\" align=\"right\">&nbsp;".F_FormatCurrency($total_tax3)."</td>";
		}
		echo "<td class=\"fillOO\" align=\"right\">&nbsp;</td>";
		echo "<td class=\"fillOE\" align=\"right\">&nbsp;</td>";
		echo "</tr>";
	}
	
	if ($md['ecdoc_discount']) {
		$total_discount_net = - $total_net * ($md['ecdoc_discount'] / 100);
		$total_discount_tax = - $total_tax * ($md['ecdoc_discount'] / 100);
		$total_discount_tax1 = - $total_tax1 * ($md['ecdoc_discount'] / 100);
		$total_discount_tax2 = - $total_tax2 * ($md['ecdoc_discount'] / 100);
		$total_discount_tax3 = - $total_tax3 * ($md['ecdoc_discount'] / 100);
		
		$total_net += $total_discount_net;
		$total_tax += $total_discount_tax;
		$total_tax1 += $total_discount_tax1;
		$total_tax2 += $total_discount_tax2;
		$total_tax3 += $total_discount_tax3;
		
		echo "<tr>";
		echo "<td class=\"fillEE\" align=\"right\" colspan=\"6\"><b>".$l['w_discount']." ".$md['ecdoc_discount']."%"."</b>&nbsp;</td>";
		echo "<td class=\"fillEO\" align=\"right\">&nbsp;".F_FormatCurrency($total_discount_net)."</td>";
		echo "<td class=\"fillEE\" align=\"right\">&nbsp;".F_FormatCurrency($total_discount_tax1)."</td>";
		if (K_EC_DISPLAY_TAX_2) {
			echo "<td class=\"fillOO\" align=\"right\">&nbsp;".F_FormatCurrency($total_discount_tax2)."</td>";
		}
		if (K_EC_DISPLAY_TAX_3) {
			echo "<td class=\"fillOE\" align=\"right\">&nbsp;".F_FormatCurrency($total_discount_tax3)."</td>";
		}
		echo "<td class=\"fillOO\" align=\"right\">&nbsp;</td>";
		echo "<td class=\"fillOE\" align=\"right\">&nbsp;</td>";
		echo "</tr>";
	}
	
	if (($md['ecdoc_deduction'] > 0) AND ($md['ecdoc_deduction_from'] > 0) ) {
		$deduction_from = $total_net * ($md['ecdoc_deduction_from'] / 100);
		$total_deduction_net = - $deduction_from * ($md['ecdoc_deduction'] / 100);
		$total_net += $total_deduction_net;
		
		echo "<tr>";
		echo "<td class=\"fillOE\" align=\"right\" colspan=\"6\"><b>".$l['w_deduction']." ".$md['ecdoc_deduction']."% ".$l['w_from']." ".$md['ecdoc_deduction_from']."% ".$l['w_total_net']."</b>&nbsp;</td>";
		echo "<td class=\"fillOO\" align=\"right\">&nbsp;".F_FormatCurrency($total_deduction_net)."</td>";
		echo "<td class=\"fillOE\" align=\"right\">&nbsp;".F_FormatCurrency(0)."</td>";
		if (K_EC_DISPLAY_TAX_2) {
			echo "<td class=\"fillOO\" align=\"right\">&nbsp;".F_FormatCurrency(0)."</td>";
		}
		if (K_EC_DISPLAY_TAX_3) {
			echo "<td class=\"fillOE\" align=\"right\">&nbsp;".F_FormatCurrency(0)."</td>";
		}
		echo "<td class=\"fillOO\" align=\"right\">&nbsp;</td>";
		echo "<td class=\"fillOE\" align=\"right\">&nbsp;</td>";
		echo "</tr>";
	}
	
	if ((isset($md['ecdoc_transport_net'])) AND ($md['ecdoc_transport_net'] > 0) ) {
		$total_net += $md['ecdoc_transport_net'];
		if ((!isset($md['ecdoc_transport_tax2'])) OR (!$md['ecdoc_transport_tax2'])) {
			$md['ecdoc_transport_tax2'] = 0;
		}
		if ((!isset($md['ecdoc_transport_tax3'])) OR (!$md['ecdoc_transport_tax3'])) {
			$md['ecdoc_transport_tax3'] = 0;
		}
		$total_tax += ($md['ecdoc_transport_tax'] + $md['ecdoc_transport_tax2'] + $md['ecdoc_transport_tax3']);
		$total_tax1 += $md['ecdoc_transport_tax'];
		$total_tax2 += $md['ecdoc_transport_tax2'];
		$total_tax3 += $md['ecdoc_transport_tax3'];
		
		echo "<tr>";
		echo "<td class=\"fillEE\" align=\"right\" colspan=\"6\"><b>".$l['w_transport']."</b>&nbsp;</td>";
		echo "<td class=\"fillEO\" align=\"right\">&nbsp;".F_FormatCurrency($md['ecdoc_transport_net'])."</td>";
		echo "<td class=\"fillEE\" align=\"right\">&nbsp;".F_FormatCurrency($md['ecdoc_transport_tax'])."</td>";
		if (K_EC_DISPLAY_TAX_2) {
			echo "<td class=\"fillEO\" align=\"right\">&nbsp;".F_FormatCurrency($md['ecdoc_transport_tax1'])."</td>";
		}
		if (K_EC_DISPLAY_TAX_3) {
			echo "<td class=\"fillEE\" align=\"right\">&nbsp;".F_FormatCurrency($md['ecdoc_transport_tax2'])."</td>";
		}
		echo "<td class=\"fillOO\" align=\"right\">&nbsp;</td>";
		echo "<td class=\"fillOE\" align=\"right\">&nbsp;</td>";
		echo "</tr>";
	}
	
	if ((isset($md['ecdoc_payment_fee'])) AND ($md['ecdoc_payment_fee'] > 0) ) {
		$total_net += $md['ecdoc_payment_fee'];
		
		echo "<tr>";
		echo "<td class=\"fillEE\" align=\"right\" colspan=\"6\"><b>".$l['w_payment_fees']."</b>&nbsp;</td>";
		echo "<td class=\"fillEO\" align=\"right\">&nbsp;".F_FormatCurrency($md['ecdoc_payment_fee'])."</td>";
		echo "<td class=\"fillEE\" align=\"right\">&nbsp;".F_FormatCurrency(0)."</td>";
		if (K_EC_DISPLAY_TAX_2) {
			echo "<td class=\"fillEO\" align=\"right\">&nbsp;".F_FormatCurrency(0)."</td>";
		}
		if (K_EC_DISPLAY_TAX_3) {
			echo "<td class=\"fillEE\" align=\"right\">&nbsp;".F_FormatCurrency(0)."</td>";
		}
		echo "<td class=\"fillOO\" align=\"right\">&nbsp;</td>";
		echo "<td class=\"fillOE\" align=\"right\">&nbsp;</td>";
		echo "</tr>";
	}
	
	echo "<tr>";
	echo "<td class=\"fillOE\" align=\"right\" colspan=\"6\"><b>".$l['w_total']."</b>&nbsp;</td>";
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
	
	$current_colspan -= 6;
	echo "<tr>";
	echo "<td class=\"fillEO\" align=\"right\" colspan=\"6\"><b>".$l['w_total_to_pay']."</b>&nbsp;</td>";
	echo "<td class=\"fillEE\" align=\"left\" colspan=\"".$current_colspan."\">&nbsp;<b>".K_MONEY_CURRENCY_UNICODE_SYMBOL." ".F_FormatCurrency($total_net + $total_tax)."</b>";
		
	
	// calculate some costs for next update (on cp_edit_ec_documents.php)
	
	//calculate shipping costs -----------
	$transport_net = 0;
	$transport_tax = 0;
	if ($md['ecdoc_shipping_type_id']) {
		$sql_shipping = "SELECT shipping_file_module FROM ".K_TABLE_EC_SHIPPING_TYPES." WHERE shipping_id=".$md['ecdoc_shipping_type_id']." LIMIT 1";
		if($r_shipping = F_aiocpdb_query($sql_shipping, $db)) {
			if($m_shipping = F_aiocpdb_fetch_array($r_shipping)) {
				//load selected shipping module
				require_once(K_PATH_FILES_SHIPPING_MODULES.$m_shipping['shipping_file_module']);
			}
		}
		else {
			F_display_db_error();
		}
	}
	//the following are for remember new values on submitting form
	echo "<input type=\"hidden\" name=\"transport_net\" id=\"transport_net\" value=\"".$transport_net."\" />";
	echo "<input type=\"hidden\" name=\"transport_tax\" id=\"transport_tax\" value=\"".$transport_tax."\" />";
	//end calculate shipping costs -----------
	
	//calculate payment costs -----------
	$payment_fee = 0;
	if ($md['ecdoc_payment_type_id']) {
		$sql_payment = "SELECT paytype_fee, paytype_feepercentage FROM ".K_TABLE_EC_PAYMENTS_TYPES." WHERE paytype_id=".$md['ecdoc_payment_type_id']." LIMIT 1";
		if($r_payment = F_aiocpdb_query($sql_payment, $db)) {
			if($m_payment = F_aiocpdb_fetch_array($r_payment)) {
				if ($m_payment['paytype_feepercentage'] > 0) {
					$payment_fee = (($total_net + $total_tax) *($m_payment['paytype_fee']/100));
				}
				if ($m_payment['paytype_fee'] > 0) {
					$payment_fee += $m_payment['paytype_fee'];
				}
			}
		}
		else {
			F_display_db_error();
		}
	}
	//the following are for remember new values on submitting form
	echo "<input type=\"hidden\" name=\"payment_fee\" id=\"payment_fee\" value=\"".$payment_fee."\" />";
	//end calculate payment costs -----------
	echo "</td>";
	echo "</tr>";
	
} //end display totals
?>
</table>
</td>
</tr>

<tr class="edge">
<td class="edge">
<?php echo "".$payment_string.""; ?>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<?php
if($show_totals) {
	//generate a verification code to avoid unauthorized calls to PDF viewer
	$verifycode = F_generate_verification_code($ecdoc_id, 4);
	F_generic_button("pdfdocument", $l['w_display_document'], "PDFDOC=window.open('cp_show_ec_pdf_document.".CP_EXT."?ecdoc_id=".$ecdoc_id."&amp;vc=".$verifycode."&amp;selected_language=".$selected_language."','PDFDOC','menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')");
}
?>
</td>
</tr>

</table>
<?php
}

// ----------------------------------------------------------
// Garbage Collector for expired orders
// (restore products available quantities)
// ----------------------------------------------------------
function F_orders_garbage_collector() {
	global $db, $l, $selected_language;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	$current_time = time();
	
	// restore product available quantity
	$sqlp = "SELECT * FROM ".K_TABLE_EC_DOCUMENTS." WHERE ecdoc_type=".K_EC_ORDER_DOC_ID." AND ecdoc_expiry_time>0 AND ecdoc_expiry_time<".$current_time."";
	if($rp = F_aiocpdb_query($sqlp, $db)) {
		while($mp = F_aiocpdb_fetch_array($rp)) {
			
			$sqld = "SELECT * FROM ".K_TABLE_EC_DOCUMENTS_DETAILS." WHERE docdet_doc_id=".$mp['ecdoc_id']."";
			if($rd = F_aiocpdb_query($sqld, $db)) {
				while($md = F_aiocpdb_fetch_array($rd)) {
					
					// restore product available quantity
					$sqlup = "UPDATE IGNORE ".K_TABLE_EC_PRODUCTS." SET 
					product_q_available=product_q_available+'".$md['docdet_quantity']."'
					WHERE product_id='".$md['docdet_product_id']."' AND product_q_available IS NOT NULL";
					if(!$rup = F_aiocpdb_query($sqlup, $db)) {
						F_display_db_error();
					}
				}
			}
			else {
				F_display_db_error();
			}
			
			//delete expired order documents details
			$sqldd = "DELETE FROM ".K_TABLE_EC_DOCUMENTS_DETAILS." WHERE docdet_doc_id=".$mp['ecdoc_id']."";
			if(!$rdd = F_aiocpdb_query($sqldd, $db)) {
				F_display_db_error();
			}
		}
	}
	else {
		F_display_db_error();
	}
	
	//delete expired order documents
	$sql = "DELETE FROM ".K_TABLE_EC_DOCUMENTS." WHERE ecdoc_type=".K_EC_ORDER_DOC_ID." AND ecdoc_expiry_time>0 AND ecdoc_expiry_time<".$current_time."";
	if(!$r = F_aiocpdb_query($sql, $db)) {
		F_display_db_error();
	}
}

// ----------------------------------------------------------
// Garbage Collector for expired RGA
// ----------------------------------------------------------
function F_rga_garbage_collector() {
	global $db, $l, $selected_language;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	
	$current_time = time();
	
	//select expired RGA
	$sqlp = "SELECT * FROM ".K_TABLE_EC_DOCUMENTS." WHERE ecdoc_type=".K_EC_RGA_DOC_ID." AND ecdoc_expiry_time>0 AND ecdoc_expiry_time<".$current_time."";
	if($rp = F_aiocpdb_query($sqlp, $db)) {
		while($mp = F_aiocpdb_fetch_array($rp)) {
			//delete expired order documents details
			$sqldd = "DELETE FROM ".K_TABLE_EC_DOCUMENTS_DETAILS." WHERE docdet_doc_id=".$mp['ecdoc_id']."";
			if(!$rdd = F_aiocpdb_query($sqldd, $db)) {
				F_display_db_error();
			}
		}
	}
	else {
		F_display_db_error();
	}
	
	//delete expired order documents
	$sql = "DELETE FROM ".K_TABLE_EC_DOCUMENTS." WHERE ecdoc_type=".K_EC_RGA_DOC_ID." AND ecdoc_expiry_time>0 AND ecdoc_expiry_time<".$current_time."";
	if(!$r = F_aiocpdb_query($sql, $db)) {
		F_display_db_error();
	}
}

// ----------------------------------------------------------
// Garbage Collector for expired RGA
// ----------------------------------------------------------
function F_documents_garbage_collector() {
	F_orders_garbage_collector(); //delete expired orders and restore products quantities
	F_rga_garbage_collector(); //delete expired RGA documents
}

// ----------------------------------------------------------
// Reurn formatted html code containing the link list
// of downloadable products
// the links will remain valid for 5.8 days
// ----------------------------------------------------------
function F_get_downloadable_files_links($ecdoc_id) {
	global $l, $db, $selected_language;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	
	$links_list = "";
	
	$sql = "SELECT docdet_download_link,docdet_name FROM ".K_TABLE_EC_DOCUMENTS_DETAILS." WHERE docdet_doc_id='".$ecdoc_id."'";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			if (!empty($m['docdet_download_link'])) {
				//check if is linked externally (not default directory)
				if(F_is_relative_link($m['download_link'])) {
					$real_link = K_PATH_FILES_DOWNLOADABLES.$m['docdet_download_link'];
				}
				else {
					$real_link = $m['docdet_download_link'];
				}
				
				$verifycode = F_generate_verification_code($real_link, 6);
				$full_link =  K_PATH_HOST.K_PATH_AIOCP."shared/code/cp_download.".CP_EXT."?c=".$verifycode."&amp;d=6&amp;f=".urlencode($real_link)."";
				$links_list .= "<li><a href=\"".$full_link."\">".htmlentities($m['docdet_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</a></li>\n";
			}
		}
	}
	else {
		F_display_db_error();
	}
	
	if (!empty($links_list)) {
		$links_list = "<ul>\n".$links_list."</ul>";
	}
	
	return $links_list;
}

//------------------------------------------------------------
// Send PDF document to user email
//------------------------------------------------------------
function F_send_pdfdoc_email($ecdoc_id, $ecdoc_user_id, $document_type="", $ecdoc_number="0", $ecdoc_date="0000-00-00", $message="") {
	global $l, $db, $selected_language;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_class_mailer.'.CP_EXT);
	require_once('../../shared/code/cp_functions_language.'.CP_EXT);
	require_once('../../shared/code/cp_functions_ec_pdf_documents.'.CP_EXT);
	require_once('../../shared/code/cp_functions_user.'.CP_EXT);
	require_once('../../shared/code/cp_functions_html2txt.'.CP_EXT);
	
	// Instantiate C_mailer class
	$mail = new C_mailer;
	
	$mail->language = $selected_language;
	
	//Load default values
	$mail->Priority = $emailcfg->Priority;
	$mail->ContentType = $emailcfg->ContentType;
	$mail->Encoding = $emailcfg->Encoding;
	$mail->WordWrap = $emailcfg->WordWrap;
	$mail->Mailer = $emailcfg->Mailer;
	$mail->Sendmail = $emailcfg->Sendmail;
	$mail->UseMSMailHeaders = $emailcfg->UseMSMailHeaders;
	$mail->Host = $emailcfg->Host;
	$mail->Port = $emailcfg->Port;
	$mail->Helo = $emailcfg->Helo;
	$mail->SMTPAuth = $emailcfg->SMTPAuth;
	$mail->Username = $emailcfg->Username;
	$mail->Password = $emailcfg->Password;
	$mail->Timeout = $emailcfg->Timeout;
	$mail->SMTPDebug = $emailcfg->SMTPDebug;
	//$mail->SMTPclassPath = $emailcfg->SMTPclassPath;
	$mail->PluginDir = $emailcfg->PluginDir;
	
	
	$mail->Sender = $emailcfg->Sender;
	$mail->From = $emailcfg->From;
	$mail->FromName = $emailcfg->FromName;
	if ($emailcfg->Reply) {
		$mail->AddReplyTo($emailcfg->Reply, $emailcfg->ReplyName);
	}
	
	$mail->CharSet = F_word_language($selected_language, "a_meta_charset");
	if(!$mail->CharSet) {$mail->CharSet = $emailcfg->CharSet;}
	
	$mail->Subject = "".$document_type." ".$ecdoc_number." - ".$ecdoc_date."";
	
	$mail->IsHTML(TRUE); // Sets message type to HTML.
	
	$doc_name = $document_type."_".$ecdoc_number."_".$ecdoc_date.".pdf";
	
	$cid = md5($doc_name);
	
	$mail->Body = "<"."?xml version=\"1.0\" encoding=\"".$l['a_meta_charset']."\"?".">\n";
	$mail->Body .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"DTD/xhtml1-transitional.dtd\">\n";
	$mail->Body .= "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"".$l['a_meta_language']."\" lang=\"".$l['a_meta_language']."\" dir=\"".$l['a_meta_dir']."\">\n";
	$mail->Body .= "<body>\n";
	$mail->Body .= "".$l['w_attachment'].": ".$doc_name."";
	$mail->Body .= "<hr />";
	$mail->Body .= $message;
	$mail->Body .= "\n</body></html>";
	
	//compose alternative TEXT message body
	$mail->AltBody = F_html_to_text($mail->Body, false, true);
	
	//Attach document
	$pdf_content = F_generate_pdf_document($ecdoc_id, true); //generate order document
	$mail->AddStringAttachment($pdf_content, $doc_name, $emailcfg->AttachmentsEncoding, "application/octet-stream", $cid);
	
	$userdata = F_get_user_data($ecdoc_user_id);
	
	$mail->AddAddress($userdata->email, $userdata->name); //Adds a "To" address
	
	if($mail->Send()) { //send email to user
		F_print_error("MESSAGE", $l['m_email_sent']);
	}
	else {
		F_print_error("ERROR", $l['m_email_sent_error']);
	}
	
 	$mail->ClearAddresses(); // Clear all addresses for next loop
	$mail->ClearCustomHeaders(); // Clears all custom headers
	$mail->ClearAllRecipients(); // Clears all recipients assigned in the TO, CC and BCC
 	$mail->ClearAttachments(); // Clears all previously set filesystem, string, and binary attachments
	$mail->ClearReplyTos(); // Clears all recipients assigned in the ReplyTo array
	
	return TRUE;
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
