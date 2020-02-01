<?php
//============================================================+
// File name   : cp_functions_ec_rga.php                       
// Begin       : 2002-10-19                                    
// Last Update : 2003-11-06                                    
//                                                             
// Description : Functions for Return Goods Authorization (RGA)
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
// Select user invoices
// for RGA goods selections
// ----------------------------------------------------------
function F_display_select_rga_invoice($user_id) {
	global $db, $l, $selected_language, $aiocp_dp;
	global $menu_mode, $changeyear, $tyear, $changedoc, $ecdoc_id;
	global $selected, $serial, $reason;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	require_once('../../shared/code/cp_functions_user.'.CP_EXT);
	
	
	if ((($menu_mode == $l['w_send']) OR ($menu_mode == unhtmlentities($l['w_send']))) AND (!empty($selected)) ){
		if (F_create_new_ec_rga($user_id, $ecdoc_id, $selected, $serial, $reason)) {
			echo "".$l['d_rga_request_sent']."";
		}
		else {
			F_print_error("WARNING", $l['m_authorization_deny']);
		}
	}
	else {
		
		// Initialize variables
		$ecdoc_type = K_EC_INVOICE_DOC_ID; //select goods only from RGA
		
		if (isset($changeyear) AND $changeyear) {
			$ecdoc_id = FALSE;
		}
		
		$currentyear = gmdate('Y');
		$firstyear = $currentyear;
		$sql = "SELECT MIN(ecdoc_date) FROM ".K_TABLE_EC_DOCUMENTS."";
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
		
		if($formstatus) {
			if(!$ecdoc_id) {$sql = "SELECT * FROM ".K_TABLE_EC_DOCUMENTS." WHERE (YEAR(ecdoc_date)='".$tyear."' AND ecdoc_type='".$ecdoc_type."' AND ecdoc_user_id='".$user_id."') ORDER BY ecdoc_date DESC LIMIT 1";}
			else {$sql = "SELECT * FROM ".K_TABLE_EC_DOCUMENTS." WHERE ecdoc_id='".$ecdoc_id."' AND ecdoc_user_id='".$user_id."' LIMIT 1";}
			if($r = F_aiocpdb_query($sql, $db)) {
				if($m = F_aiocpdb_fetch_array($r)) {
					$ecdoc_id = $m['ecdoc_id'];
				}
				else {
					F_print_error("WARNING", $l['m_authorization_deny']);
					return FALSE;
				}
			}
			else {
				F_display_db_error();
			}
		}
	?>
	
		<!-- ====================================================== -->
		<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_selectrgagoods" id="form_selectrgagoods">
		
		<table class="edge" border="0" cellspacing="1" cellpadding="2">
		
		<tr class="edge">
		<td class="edge">
		
		<table class="fill" border="0" cellspacing="2" cellpadding="1">
		
		<!-- SELECT year ==================== -->
		<tr class="fillO">
		<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_year', 'h_ecdoc_year'); ?></b></td>
		<td class="fillOE">
		<input type="hidden" name="changeyear" id="changeyear" value="0" />
		
		<select name="tyear" id="tyear" size="0" onchange="document.form_selectrgagoods.changeyear.value=1; document.form_selectrgagoods.submit()">
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
		
		<!-- SELECT ==================== -->
		<tr class="fillO">
		<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_document', 'h_ecdoc_select'); ?></b></td>
		<td class="fillOE">
		<input type="hidden" name="changedoc" id="changedoc" value="0" />
		
		<select name="ecdoc_id" id="ecdoc_id" size="0" onchange="document.form_selectrgagoods.changedoc.value=1; document.form_selectrgagoods.submit()">
		<?php
		$sql = "SELECT * FROM ".K_TABLE_EC_DOCUMENTS." WHERE (YEAR(ecdoc_date)='".$tyear."' AND ecdoc_type='".$ecdoc_type."' AND ecdoc_user_id='".$user_id."') ORDER BY ecdoc_date DESC, ecdoc_number";
		
		if($r = F_aiocpdb_query($sql, $db)) {
			while($m = F_aiocpdb_fetch_array($r)) {
				echo "<option value=\"".$m['ecdoc_id']."\"";
				if($m['ecdoc_id'] == $ecdoc_id) {
					echo " selected=\"selected\"";
				}
				echo ">".$m['ecdoc_date']." - ".$m['ecdoc_number']."</option>\n";
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
			F_display_rga_invoice($ecdoc_id);
		}
		?>
		<!-- END SHOW links ==================== -->
		
		</form>
		<!-- ====================================================== -->
<?php
	}
}

// ----------------------------------------------------------
// display documents details
// ----------------------------------------------------------
function F_display_rga_invoice($ecdoc_id) {
	global $db, $l, $selected_language;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	require_once('../../shared/code/cp_functions_user.'.CP_EXT);
	
	$user_id = $_SESSION['session_user_id'];
	
	//get main document data
	$sql = "SELECT * FROM ".K_TABLE_EC_DOCUMENTS." WHERE ecdoc_id='".$ecdoc_id."' AND ecdoc_user_id='".$user_id."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($md = F_aiocpdb_fetch_array($r)) {
			$ecdoc_number = $md['ecdoc_number'];
			$ecdoc_type = $md['ecdoc_type'];
			$ecdoc_date = $md['ecdoc_date'];
			$total_parcels = $md['ecdoc_parcels'];
			$ecdoc_user_id = $md['ecdoc_user_id'];
			$ecdoc_user_data = unserialize($md['ecdoc_user_data']);
			$sql2 = "SELECT doctype_name FROM ".K_TABLE_EC_DOCUMENTS_TYPES." WHERE doctype_id='".$ecdoc_type."' LIMIT 1";
			if($r2 = F_aiocpdb_query($sql2, $db)) {
				if($m2 = F_aiocpdb_fetch_array($r2)) {
					$select_name = unserialize($m2['doctype_name']);
					$document_type = $select_name[$selected_language];
				}
			}
			else {
				F_display_db_error();
			}
		}
		else {
			F_print_error("WARNING", $l['m_authorization_deny']);
			return FALSE;
		}
	}
	else {
			F_display_db_error();
	}
?>

<!-- DISPLAY DOCUMENT'S PRODUCTS LIST -->

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<th class="edge">
<?php echo "".$ecdoc_date." - ".$document_type." - ".$ecdoc_number.""; ?>
</th>
</tr>

<tr class="edge">
<th class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">
<tr class="fill">
<th class="fillE">&nbsp;</th>
<th class="fillO"><?php echo $l['w_code']; ?></th>
<th class="fillE"><?php echo $l['w_product']; ?></th>
<th class="fillO"><?php echo $l['w_unit']; ?></th>
<th class="fillE"><?php echo $l['w_serial_number']; ?></th>
<th class="fillE"><?php echo $l['w_warranty']; ?></th>
</tr>

<!-- SELECT ==================== -->
<?php
//initialize variables
$rowclass = "O";
$rowodd = 0;
$j = 0; //count goods

$sql = "SELECT * FROM ".K_TABLE_EC_DOCUMENTS_DETAILS." WHERE docdet_doc_id='".$ecdoc_id."'";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		
		if($m['docdet_transportable']) { //count transportable items
			
			$serial_num = str_replace("\r\n", "\n", $m['docdet_serial_numbers']);
			$serial_num = explode("\n", $serial_num); //put serial numbers in array
			$serials_size = sizeof($serial_num);
			
			//get unit of measure data
			$sqlu = "SELECT * FROM ".K_TABLE_EC_UNITS_OF_MEASURE." WHERE unit_id=".$m['docdet_unit_of_measure_id']." LIMIT 1";
			if($ru = F_aiocpdb_query($sqlu, $db)) {
				if($mu = F_aiocpdb_fetch_array($ru)) {
					$unit_name = $mu['unit_name'];
					$unit_discrete = $mu['unit_discrete'];
				}
			}
			else {
				F_display_db_error();
			}
			
			if (isset($unit_discrete) AND $unit_discrete) {
				$rows_for_this_product = $m['docdet_quantity'];
			}
			else {
				$rows_for_this_product = 1;
			}
			
			for ($i=0; $i<$rows_for_this_product; $i++) {
				//change style for each row
				if (isset($rowodd) AND ($rowodd)) {
					$rowclass = "O";
					$rowodd = 0;
				} else {
					$rowclass = "E";
					$rowodd = 1;
				}
				
				echo "<tr class=\"fill".$rowclass."\">";
				echo "<td class=\"fill".$rowclass."E\"><input type=\"checkbox\" name=\"selected[".$j."][".$i."]\" id=\"selected_".$j."_".$i."\" value=\"1\" /></td>"; //select item
				
				echo "<td class=\"fill".$rowclass."O\">&nbsp;".$m['docdet_code']."</td>";
				
				echo "<td class=\"fill".$rowclass."E\">&nbsp;".$m['docdet_name']."</td>";
				
				echo "<td class=\"fill".$rowclass."O\" align=\"right\">&nbsp;";
				echo $unit_name;
				echo "</td>";
				
				echo "<td class=\"fill".$rowclass."E\">&nbsp;";
				if ( (!empty($m['docdet_serial_numbers'])) AND ($i < $serials_size)) {
					echo "<input type=\"hidden\" name=\"serial[".$j."][".$i."]\" id=\"serial_".$j."_".$i."\" value=\"".$serial_num[$i]."\" />";
					echo $serial_num[$i];
				}
				else {
				echo "<input type=\"text\" name=\"serial[".$j."][".$i."]\" id=\"serial_".$j."_".$i."\" value=\"\" size=\"12\" maxlength=\"255\" />";
				}
				echo "</td>";
				
				//display warranty information
				echo "<td class=\"fill".$rowclass."O\">&nbsp;";
				if (isset($m['docdet_warranty'])) {
					$elapsed_time = time() - strtotime($ecdoc_date);
					$warranty_duration = $m['docdet_warranty'] * K_SECONDS_IN_MONTH;
					$difference_days = round(($elapsed_time - $warranty_duration) / K_SECONDS_IN_DAY);
					if ($elapsed_time > $warranty_duration) { // expired warranty
						echo "<b>".$l['w_expired']." (".$difference_days." ".$l['w_days'].")</b>";
					}
					else { // valid warranty
						echo "".$l['w_valid_f']." (".$difference_days." ".$l['w_days'].")";
					}
				}
				echo "</td>";
				echo "</tr>";
				
				//row to specify the return reason
				echo "<tr class=\"fill".$rowclass."\">";
				echo "<td class=\"fill".$rowclass."E\" align=\"right\" colspan=\"6\">";
				echo "<textarea cols=\"50\" rows=\"3\" name=\"reason[".$j."][".$i."]\" id=\"reason_".$j."_".$i."\"></textarea>";
				echo "</td>"; //select item
				echo "</tr>";
			}
		}
		$j++;
	}
}
else {
	F_display_db_error();
}

?>
</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
F_submit_button("form_selectrgagoods","menu_mode",$l['w_send']); 
F_submit_button("form_selectrgagoods","menu_mode",$l['w_clear']); 
?>
</td>
</tr>

</table>
<?php
}

// ----------------------------------------------------------
// create RGA document from user selection and original invoice
// ----------------------------------------------------------
function F_create_new_ec_rga($user_id, $ecdoc_id, $selected, $serial, $reason) {
	global $db, $l, $selected_language;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_ec_documents.'.CP_EXT);
	require_once('../../shared/code/cp_functions_user.'.CP_EXT);
	
	//security controls:
	if ($user_id != $_SESSION['session_user_id']) {
		return FALSE;
	}
	
	$sql = "SELECT ecdoc_user_id FROM ".K_TABLE_EC_DOCUMENTS." WHERE ecdoc_id='".$ecdoc_id."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if(!F_aiocpdb_fetch_array($r)) {
			return FALSE;
		}
	}
	else {
		F_display_db_error();
	}
	
	//set document data
	$ecdoc_type = K_EC_RGA_DOC_ID; //Return Goods Authorization (RGA) document ID
	
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
	
	$ecdoc_user_data = addslashes(serialize(F_get_user_document_data($user_id)));
	
	// generate RGA document from user selection and original invoice
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
		'',
		'',
		'',
		'',
		'',
		'',
		'',
		'',
		'',
		'',
		'".K_EC_RGA_SUBJECT."',
		'".K_EC_RGA_INTRO."',
		'".K_EC_RGA_FOOTER."',
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
		'".(K_EC_RGA_EXPIRY_TIME + time())."',
		'".$ecdoc_id."'
		)";
	if($ri = F_aiocpdb_query($sqli, $db)) {
		$rga_ecdoc_id = F_aiocpdb_insert_id();
	}
	else {
		F_display_db_error();
	}
	
	$key_product = 0;
	$sql = "SELECT * FROM ".K_TABLE_EC_DOCUMENTS_DETAILS." WHERE docdet_doc_id=".$ecdoc_id."";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			
			if (sizeof($selected[$key_product])) { //if product has been selected
				//for each serial add 1 product row
				while(list($key_selected, $val_selected) = each($selected[$key_product])) {
					
					if ($val_selected) {
						if (isset($serial[$key_product][$key_selected])) {
							$this_serial = $serial[$key_product][$key_selected];
						}
						else {
							$this_serial = "";
						}
						if (isset($reason[$key_product][$key_selected])) {
							$temp_reason = array();
							//add reason in each language
							$sqll = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
							if($rl = F_aiocpdb_query($sqll, $db)) {
								while($ml = F_aiocpdb_fetch_array($rl)) {
									$temp_reason[$ml['language_code']] = $reason[$key_product][$key_selected];
								}
							}
							else {
								F_display_db_error();
							}
							$this_reason = addslashes(serialize($temp_reason));
						}
						else {
							$this_reason = "";
						}
						
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
						'".$rga_ecdoc_id."',
						'".$m['docdet_product_id']."',
						'".$m['docdet_code']."',
						'".$m['docdet_barcode']."',
						'".$m['docdet_inventory_code']."',
						'".$m['docdet_alternative_codes']."',
						'".$this_serial."',
						'".$m['docdet_category_id']."',
						'".$m['docdet_manufacturer_id']."',
						'".$m['docdet_manufacturer_link']."',
						'".$m['docdet_name']."',
						'".$this_reason."',
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
					}
				} // end for each serial
			}
			$key_product++;
		}
	}
	else {
		F_display_db_error();
	}
	return TRUE;
}
//============================================================+
// END OF FILE                                                 
//============================================================+
?>
