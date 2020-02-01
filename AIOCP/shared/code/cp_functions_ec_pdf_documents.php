<?php
//============================================================+
// File name   : cp_functions_ec_pdf_documents.php
// Begin       : 2002-07-23
// Last Update : 2008-12-06
// 
// Description : Create PDF version of commercial documents
// Note: currently do not support right-to-left languages
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

// ------------------------------------------------------------
// generate a pdf document
// $mode=false => output to a browser
// $mode=true => return content
// ------------------------------------------------------------
function F_generate_pdf_document($ecdoc_id, $output_mode=false) {
	global $l, $db, $selected_language;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_company_data.'.CP_EXT);
	require_once('../../shared/code/cp_functions_html2txt.'.CP_EXT);
	require_once('../../shared/pdf/cp_ec_pdf_doc.'.CP_EXT);
	
	$doc_language = $selected_language;
	if (K_DOCUMENTS_LANGUAGE) {
		$doc_language = "".K_DOCUMENTS_LANGUAGE."";
	}
	
	//get language template table
	require_once('../../shared/code/cp_functions_doc_language.'.CP_EXT);
	$lang = F_get_pdf_language_templates($doc_language);
	
	//get general document data
	$sql = "SELECT * FROM ".K_TABLE_EC_DOCUMENTS." WHERE ecdoc_id=".$ecdoc_id." LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if(!$doc = F_aiocpdb_fetch_array($r)) {
			return FALSE;
		}
	}
	else {
		F_display_db_error();
	}
	
	$ecdoc_user_data = unserialize($doc['ecdoc_user_data']); //user address data
	
	//get document options
	$sql = "SELECT * FROM ".K_TABLE_EC_DOCUMENTS_TYPES." WHERE doctype_id=".$doc['ecdoc_type']." LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($doc_type = F_aiocpdb_fetch_array($r)) {
			$doc_name = unserialize($doc_type['doctype_name']);
			$doc_options = unserialize($doc_type['doctype_options']);
			while(list($key, $val) = each($doc_options)) {
				$doc_options[$key] = stripslashes($val);
			}
		}
		else {
			return FALSE;
		}
	}
	else {
		F_display_db_error();
	}
	
	//get document style
	$sql = "SELECT * FROM ".K_TABLE_EC_DOCUMENTS_STYLES." WHERE docstyle_id=".$doc_type['doctype_style']." LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if(!$doc_style = F_aiocpdb_fetch_array($r)) {
			return FALSE;
		}
	}
	else {
		F_display_db_error();
	}
	
	//get company data
	$company_data = F_get_company_data();
	
	if ($doc_style['docstyle_paper']) {
		$page_format = $doc_style['docstyle_paper'];
	}
	else {
		$page_format = Array($doc_style['docstyle_width'], $doc_style['docstyle_height']);
	}
	
	//create new PDF document (document units are set by default to millimeters)
	$pdf = new EC_DOC_PDF($doc_style['docstyle_orientation'], 'mm', $page_format); 
	
	// set document informations
	$pdf->SetCreator("AIOCP - All In One Control Panel ver.".K_AIOCP_VERSION."");
	$pdf->SetAuthor($company_data['name']);
	$pdf->SetTitle($doc_name[$doc_language]);
	$pdf->SetSubject($doc_name[$doc_language]." - ".$doc['ecdoc_number']." - ".$doc['ecdoc_date']);
	$pdf->SetKeywords($company_data['name']." ".$doc_name[$doc_language]." ".$doc['ecdoc_number']." ".$doc['ecdoc_date']);
	
	//set margins
	$pdf->SetMargins($doc_style['docstyle_margin_left'], $doc_style['docstyle_margin_top'], $doc_style['docstyle_margin_right']);
	//set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, $doc_style['docstyle_margin_bottom']);
	
	$pdf->SetHeaderMargin($doc_style['docstyle_header']);
	$pdf->SetFooterMargin($doc_style['docstyle_footer']);
	
	if ($doc_options[10]) {
		$pdf->SetBarcode($doc['ecdoc_type']."+".$doc['ecdoc_number']."+".$doc['ecdoc_date']."+".$ecdoc_id);
	}
	
	$pdf->header_font = Array($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
	$pdf->footer_font = Array($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
	
	$pdf->lang_templates = $lang;
	$pdf->company_data = $company_data;
	
	//initialize document
	$pdf->AddPage();
	
	/*
	//text direction support for Right to Left languages...
	// (must be implemented)
	require_once('../../shared/code/cp_functions_language.'.CP_EXT);
	//choose text justification
	if (strtolower(F_word_language($selected_language, "a_meta_dir")) == "rtl") {
	}
	else {
	}
	*/
	
	//calculate some sizes
	$page_width = $pdf->w - $doc_style['docstyle_margin_left'] - $doc_style['docstyle_margin_right'];
	$page_elements = 8;
	if (K_EC_DISPLAY_TAX_2) {$page_elements += 1;}
	if (K_EC_DISPLAY_TAX_3) {$page_elements += 1;}
	$data_cell_width_small = round($page_width / $page_elements, 2);
	$data_cell_width_large = 2 * $data_cell_width_small;
	$data_cell_height = round((K_CELL_HEIGHT_RATIO * $doc_style['docstyle_data_font_size']) / $pdf->getScaleFactor(), 2);
	$main_cell_height = round((K_CELL_HEIGHT_RATIO * $doc_style['docstyle_main_font_size']) / $pdf->getScaleFactor(), 2);
	$info_cell_width = round($page_width / 4, 2);
	
	$pdf->SetFillColor(204, 204, 204);
	$pdf->SetLineWidth(0.1);
	$pdf->SetDrawColor(0, 0, 0);
	
	//print document name (title)
	$pdf->SetFont($doc_style['docstyle_main_font'], 'B', $doc_style['docstyle_main_font_size'] * K_TITLE_MAGNIFICATION);
	$pdf->Cell(0, $main_cell_height * K_TITLE_MAGNIFICATION, $doc_name[$doc_language], 1, 1, 'C', 0);
	
	//START - print main document information
	//print headers
	$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
	$pdf->Cell($info_cell_width, $data_cell_height, $lang['w_number'], 1, 0, 'C', 1);
	$pdf->Cell($info_cell_width, $data_cell_height, $lang['w_date'], 1, 0, 'C', 1);
	if ($doc_options[4]) { // currency
		$pdf->Cell($info_cell_width, $data_cell_height, $lang['w_currency'], 1, 0, 'C', 1);
	}
	else {
		$pdf->Cell($info_cell_width, $data_cell_height, '', 1, 0, 'C', 1);
	}
	if ($doc_options[3] AND $doc['ecdoc_validity']) { //document validity
		$pdf->Cell($info_cell_width, $data_cell_height, $lang['w_validity'], 1, 0, 'C', 1);
	}
	else {
		$pdf->Cell($info_cell_width, $data_cell_height, '', 1, 0, 'C', 1);
	}
	
	$pdf->Ln();
	
	//print info
	$pdf->SetFont($doc_style['docstyle_main_font'], '', $doc_style['docstyle_main_font_size']);
	
	$pdf->Cell($info_cell_width, $main_cell_height, $doc['ecdoc_number'], 1, 0, 'C', 0);
	$pdf->Cell($info_cell_width, $main_cell_height, $doc['ecdoc_date'], 1, 0, 'C', 0);
	if ($doc_options[4]) { // currency
		$pdf->Cell($info_cell_width, $main_cell_height, K_MONEY_CURRENCY, 1, 0, 'C', 0);
	}
	else {
		$pdf->Cell($info_cell_width, $main_cell_height, '', 1, 0, 'C', 0);
	}
	if ($doc_options[3] AND $doc['ecdoc_validity']) { //document validity
		$pdf->Cell($info_cell_width, $main_cell_height, $doc['ecdoc_validity']." ".$lang['w_'.$doc['ecdoc_validity_unit']], 1, 0, 'C', 0);
	}
	else {
		$pdf->Cell($info_cell_width, $main_cell_height, '', 1, 0, 'C', 0);
	}
	
	$pdf->Ln();
	//END - print main document information
	
	//START get and print customer data -------------------------
	$pdf->Ln();
	
	$address_table_width = round(($page_width * 0.95) / 2, 2);
	$second_address_X = $address_table_width + $doc_style['docstyle_margin_left'] + round($page_width * 0.05, 2);
	
	// get shipping_zone details
	//data for shipping costs calculations
	$shipping_state = stripslashes($ecdoc_user_data->state);
	$shipping_postcode = stripslashes($ecdoc_user_data->postcode);
	$shipping_country = stripslashes($ecdoc_user_data->country_id);
	
	$startY = $pdf->GetY();
	if ($ecdoc_user_data->l_address) { //user is a company
		//print legal address
		$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
		$pdf->Cell($address_table_width, $data_cell_height, $lang['w_at_the_order_of'], 1, 1, 'L', 1);
		$pdf->MultiCell($address_table_width, $data_cell_height, stripslashes($ecdoc_user_data->name), 'LRT', 'L', 0);
		$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
		$pdf->MultiCell($address_table_width, $data_cell_height, stripslashes($ecdoc_user_data->l_address)."\n".stripslashes($ecdoc_user_data->l_postcode)." ".stripslashes($ecdoc_user_data->l_city)." (".stripslashes($ecdoc_user_data->l_state).")\n".stripslashes($ecdoc_user_data->l_country)."\n".stripslashes($ecdoc_user_data->email), 'LRB', 'L', 0);
		$pdf->MultiCell($address_table_width, $data_cell_height, $lang['w_fiscalcode'].": ".stripslashes($ecdoc_user_data->fiscalcode), 'LRB', 'L', 0);
		$endY = $pdf->GetY();
	}
	
	if ($ecdoc_user_data->address) {
		$pdf->SetY($startY);
		$pdf->SetX($second_address_X);
		$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
		if ($ecdoc_user_data->l_address) {
			$pdf->Cell($address_table_width, $data_cell_height, $lang['w_destination'], 1, 1, 'L', 1);
		}
		else {
			$pdf->Cell($address_table_width, $data_cell_height, $lang['w_at_the_order_of'], 1, 1, 'L', 1);
		}
		$pdf->SetX($second_address_X);
		$pdf->MultiCell($address_table_width, $data_cell_height, stripslashes($ecdoc_user_data->name), 'LRT', 'L', 0);
		$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
		$pdf->SetX($second_address_X);
		$pdf->MultiCell($address_table_width, $data_cell_height, stripslashes($ecdoc_user_data->address)."\n".stripslashes($ecdoc_user_data->postcode)." ".stripslashes($ecdoc_user_data->city)." (".stripslashes($ecdoc_user_data->state).")\n".stripslashes($ecdoc_user_data->country)."\n".stripslashes($ecdoc_user_data->email), 'LRB', 'L', 0);
		if (!$ecdoc_user_data->l_address) {
			$pdf->SetX($second_address_X);
			$pdf->MultiCell($address_table_width, $data_cell_height, $lang['w_fiscalcode'].": ".stripslashes($ecdoc_user_data->fiscalcode), 'LRB', 'L', 0);
		}
	}
	
	$pdf->SetY(max($endY,$pdf->GetY()));
	//END get and print customer data -------------------------
	
	
	//subject
	if ($doc_options[6] AND $doc['ecdoc_subject']) {
		$pdf->Ln(1);
		$pdf->SetFont($doc_style['docstyle_main_font'], 'B', $doc_style['docstyle_main_font_size']);
		$pdf->MultiCell(0, $main_cell_height, $lang['w_subject'].": ".$doc['ecdoc_subject'], 0, 'L', 0);
	}
	
	//introductive notes
	if ($doc_options[7] AND $doc['ecdoc_notes_intro']) {
		$pdf->Ln(1);
		$pdf->SetFont($doc_style['docstyle_main_font'], '', $doc_style['docstyle_main_font_size']);
		$pdf->MultiCell(0, $main_cell_height, $doc['ecdoc_notes_intro'], 0, 'L', 0);
	}
	
	
	// START DATA TABLE ---------------------
	
	$pdf->Ln(1);
	
	//print table header
	$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
	
	//draw horizontal line indicating start of data
	$pdf->SetDrawColor(128, 128, 128);
	$pdf->SetLineWidth(0.4);
	$pdf->Cell(0, $data_cell_height, '', 'T', 1, 'C', 0);
	
	$pdf->SetFillColor(204, 204, 204);
	$pdf->SetLineWidth(0.1);
	$pdf->SetDrawColor(0, 0, 0);
	
	if ($doc_options[4]) { //print costs
		$pdf->Cell($data_cell_width_small, $data_cell_height, $lang['w_code'], 'LTB', 0, 'L', 1);
		$pdf->Cell($page_width-$data_cell_width_small, $data_cell_height, $lang['w_product'], 'RTB', 1, 'L', 1);
		$pdf->Cell($data_cell_width_small, $data_cell_height, $lang['w_unit'], 1, 0, 'C', 0);
		$pdf->Cell($data_cell_width_small, $data_cell_height, $lang['w_quantity'], 1, 0, 'C', 0);
		$pdf->Cell($data_cell_width_large, $data_cell_height, $lang['w_cost_per_unit']." [".K_MONEY_CURRENCY_SYMBOL."]", 1, 0, 'C', 0);
		$pdf->Cell($data_cell_width_small, $data_cell_height, $lang['w_discount']." [%]", 1, 0, 'C', 0);
		$pdf->Cell($data_cell_width_large, $data_cell_height, $lang['w_total_net']." [".K_MONEY_CURRENCY_SYMBOL."]", 1, 0, 'C', 0);
		$pdf->Cell($data_cell_width_small, $data_cell_height, $lang['w_ec_tax']." [%]", 1, 0, 'C', 0);
		
		if (K_EC_DISPLAY_TAX_2) {
			$pdf->Cell($data_cell_width_small, $data_cell_height, $lang['w_ec_tax2']." [%]", 1, 0, 'C', 0);
		}
		if (K_EC_DISPLAY_TAX_3) {
			$pdf->Cell($data_cell_width_small, $data_cell_height, $lang['w_ec_tax3']." [%]", 1, 1, 'C', 0);
		}
	}
	else { //do not print costs
		if ($doc['ecdoc_type'] == K_EC_RGA_DOC_ID) { // RGA document
			$pdf->Cell($data_cell_width_small, $data_cell_height, $lang['w_code'], '1', 0, 'C', 0);
			$pdf->Cell($data_cell_width_small, $data_cell_height, $lang['w_serial_num'], 1, 0, 'C', 0);
			$pdf->Cell($data_cell_width_small, $data_cell_height, $lang['w_unit'], 1, 0, 'C', 0);
			$pdf->Cell($page_width-(3*$data_cell_width_small), $data_cell_height, $lang['w_product'], '1', 1, 'L', 0);
			$pdf->Cell($data_cell_width_small, $data_cell_height, '', 0, 0, 'C', 0);
			$pdf->Cell($page_width-$data_cell_width_small, $data_cell_height, $lang['w_reason'], '1', 1, 'L', 0);
		}
		else {
			$pdf->Cell($data_cell_width_small, $data_cell_height, $lang['w_unit'], 1, 0, 'C', 0);
			$pdf->Cell($data_cell_width_small, $data_cell_height, $lang['w_quantity'], 1, 0, 'C', 0);
			$pdf->Cell($data_cell_width_small, $data_cell_height, $lang['w_code'], '1', 0, 'C', 0);
			$pdf->Cell($page_width-(3*$data_cell_width_small), $data_cell_height, $lang['w_product'], '1', 1, 'L', 0);
		}
	}
	
	$pdf->Ln();
	
	$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
	
	$total_net = 0;
	$total_tax = 0; //sum of all taxes amounts
	$total_tax1 = 0;
	$total_tax2 = 0;
	$total_tax3 = 0;
	$total_weight = 0; //total weight in Kg
	$total_volume = 0; //total volume in m^3
	$total_items = 0; //transportable items
	$total_parcels = $doc['ecdoc_parcels'];
	
	//get document details
	$sqld = "SELECT * FROM ".K_TABLE_EC_DOCUMENTS_DETAILS." WHERE docdet_doc_id='".$ecdoc_id."'";
	if($rd = F_aiocpdb_query($sqld, $db)) {
		while($doc_det = F_aiocpdb_fetch_array($rd)) {
			
			//get unit of measure name
			$sqlu = "SELECT * FROM ".K_TABLE_EC_UNITS_OF_MEASURE." WHERE unit_id=".$doc_det['docdet_unit_of_measure_id']." LIMIT 1";
			if($ru = F_aiocpdb_query($sqlu, $db)) {
				if($mu = F_aiocpdb_fetch_array($ru)) {
					$unit_of_measure = $mu['unit_name'];
				}
				else {
					$unit_of_measure = "";
				}
			}
			else {
				F_display_db_error();
			}
			
			if($doc_det['docdet_transportable']) { //count transportable items
				$total_items++;
			}
			
			if ($doc_options[4]) { //print costs
				//product code
				$pdf->Cell($data_cell_width_small, $data_cell_height, $doc_det['docdet_code'], 'LTB', 0, 'L', 1);
				//product name
				$pdf->Cell($page_width-$data_cell_width_small, $data_cell_height, $doc_det['docdet_name'], 'RTB', 1, 'L', 1);
				//print unit of measure name
				$pdf->Cell($data_cell_width_small, $data_cell_height, $unit_of_measure, 1, 0, 'R', 0);
				//quantity
				$pdf->Cell($data_cell_width_small, $data_cell_height, $doc_det['docdet_quantity'], 1, 0, 'R', 0);
				//unitary cost
				$pdf->Cell($data_cell_width_large, $data_cell_height, F_FormatCurrency($doc_det['docdet_cost']), 1, 0, 'R', 0);
				//discount
				$pdf->Cell($data_cell_width_small, $data_cell_height, $doc_det['docdet_discount'], 1, 0, 'R', 0);
				//total
				$net_amount = ($doc_det['docdet_quantity'] * $doc_det['docdet_cost']) * (1 - ($doc_det['docdet_discount'] / 100));
				$total_net += $net_amount;
				$pdf->Cell($data_cell_width_large, $data_cell_height, F_FormatCurrency($net_amount), 1, 0, 'R', 0);
				//tax [%]
				$pdf->Cell($data_cell_width_small, $data_cell_height, $doc_det['docdet_tax'], 1, 0, 'R', 0);
				$tax_amount = $net_amount * ($doc_det['docdet_tax'] / 100);
				
				$tax_amount2 = 0;
				if (K_EC_DISPLAY_TAX_2) {
					if (!$doc_det['docdet_tax2']) {$doc_det['docdet_tax2'] = 0;} 
					$pdf->Cell($data_cell_width_small, $data_cell_height, $doc_det['docdet_tax2'], 1, 0, 'R', 0);
					$tax_amount2 = $net_amount * ($doc_det['docdet_tax2'] / 100);
				}
				
				$tax_amount3 = 0;
				if (K_EC_DISPLAY_TAX_3) {
					if (!$doc_det['docdet_tax3']) {$doc_det['docdet_tax3'] = 0;} 
					$pdf->Cell($data_cell_width_small, $data_cell_height, $doc_det['docdet_tax3'], 1, 0, 'R', 0);
					$tax_amount3 = ($net_amount + $tax_amount + $tax_amount2) * ($doc_det['docdet_tax3'] / 100);
				}
				
				//tax
				$total_tax1 += $tax_amount;
				$total_tax2 += $tax_amount2;
				$total_tax3 += $tax_amount3;
				$total_tax += ($tax_amount + $tax_amount2 + $tax_amount3);
				
				$pdf->Ln();
			}
			else { // do not print costs
				if ($doc_det['docdet_transportable']) { //print only transportable items
					if ($doc['ecdoc_type'] == K_EC_RGA_DOC_ID) { // RGA document
						//product code
						$pdf->Cell($data_cell_width_small, $data_cell_height, $doc_det['docdet_code'], 1, 0, 'L', 0);
						//serial number
						$pdf->Cell($data_cell_width_small, $data_cell_height, $doc_det['docdet_serial_numbers'], 1, 0, 'L', 0);
 						//print unit of measure name
						$pdf->Cell($data_cell_width_small, $data_cell_height, $unit_of_measure, 1, 0, 'C', 0);
						//product name
						$pdf->Cell($page_width-(3 * $data_cell_width_small), $data_cell_height, $doc_det['docdet_name'],
 1, 1, 'L', 0);
 						//reason
						$reason = unserialize($doc_det['docdet_description']);
						$reason = stripslashes($reason[$doc_language]);
						$pdf->Cell($data_cell_width_small, $data_cell_height, '', 0, 0, 'C', 0);
						$pdf->MultiCell($page_width-$data_cell_width_small, $data_cell_height, $reason, 1, 'L', 0);
					}
					else {
						//print unit of measure name
						$pdf->Cell($data_cell_width_small, $data_cell_height, $unit_of_measure, 1, 0, 'R', 0);
						//quantity
						$pdf->Cell($data_cell_width_small, $data_cell_height, $doc_det['docdet_quantity'], 1, 0, 'R', 0);
						//product code
						$pdf->Cell($data_cell_width_small, $data_cell_height, $doc_det['docdet_code'], 1, 0, 'L', 0);
						//product name
						$pdf->Cell($page_width-(3 * $data_cell_width_small), $data_cell_height, $doc_det['docdet_name'], 1, 1, 'L', 0);
					}
				}
			}
			
			$total_weight += $doc_det['docdet_weight_per_unit']; //kg
			$total_volume += ($doc_det['docdet_length'] * $doc_det['docdet_width'] * $doc_det['docdet_height']); //cm^3
		}
	}
	else {
		F_display_db_error();
	}
	
	if ($doc_options[4]) { //print costs
		
		$pdf->Ln();
		
		//print total totals
		if (($doc['ecdoc_discount'] > 0) OR (($doc['ecdoc_deduction'] > 0) AND ($doc['ecdoc_deduction_from'] > 0) )) {
			$pdf->Cell($data_cell_width_small * 5, $data_cell_height, $lang['w_subtotal']."  [".K_MONEY_CURRENCY_SYMBOL."] ", 0, 0, 'R', 0);
			$pdf->Cell($data_cell_width_large, $data_cell_height, F_FormatCurrency($total_net), 1, 0, 'R', 0);
			$pdf->Cell($data_cell_width_small, $data_cell_height, F_FormatCurrency($total_tax1), 1, 0, 'R', 0);
			if (K_EC_DISPLAY_TAX_2) {
				$pdf->Cell($data_cell_width_small, $data_cell_height, F_FormatCurrency($total_tax2), 1, 0, 'R', 0);
			}
			if (K_EC_DISPLAY_TAX_3) {
				$pdf->Cell($data_cell_width_small, $data_cell_height, F_FormatCurrency($total_tax3), 1, 0, 'R', 0);
			}
			$pdf->Ln();
		}
		
		//print discount totals
		if ($doc['ecdoc_discount'] > 0) { //apply discount
			$total_discount_net = - $total_net * ($doc['ecdoc_discount'] / 100);
			$total_discount_tax = - $total_tax * ($doc['ecdoc_discount'] / 100);
			$total_discount_tax1 = - $total_tax1 * ($doc['ecdoc_discount'] / 100);
			$total_discount_tax2 = - $total_tax2 * ($doc['ecdoc_discount'] / 100);
			$total_discount_tax3 = - $total_tax3 * ($doc['ecdoc_discount'] / 100);
			
			$total_net += $total_discount_net;
			$total_tax += $total_discount_tax;
			$total_tax1 += $total_discount_tax1;
			$total_tax2 += $total_discount_tax2;
			$total_tax3 += $total_discount_tax3;
			
			$pdf->Cell($data_cell_width_small * 5, $data_cell_height, $lang['w_discount']." ".$doc['ecdoc_discount']."%"."  [".K_MONEY_CURRENCY_SYMBOL."] ", 0, 0, 'R', 0);
			$pdf->Cell($data_cell_width_large, $data_cell_height, F_FormatCurrency($total_discount_net), 1, 0, 'R', 0);
			$pdf->Cell($data_cell_width_small, $data_cell_height, F_FormatCurrency($total_discount_tax1), 1, 0, 'R', 0);
			if (K_EC_DISPLAY_TAX_2) {
				$pdf->Cell($data_cell_width_small, $data_cell_height, F_FormatCurrency($total_discount_tax2), 1, 0, 'R', 0);
			}
			if (K_EC_DISPLAY_TAX_3) {
				$pdf->Cell($data_cell_width_small, $data_cell_height, F_FormatCurrency($total_discount_tax3), 1, 0, 'R', 0);
			}
			$pdf->Ln();
		}
		
		//print deduction totals
		if (($doc['ecdoc_deduction'] > 0) AND ($doc['ecdoc_deduction_from'] > 0) ) { //apply deduction
			$deduction_from = $total_net * ($doc['ecdoc_deduction_from'] / 100);
			$total_deduction_net = - $deduction_from * ($doc['ecdoc_deduction'] / 100);
			
			$pdf->Cell($data_cell_width_small * 5, $data_cell_height, $lang['w_deduction']." ".$doc['ecdoc_deduction']."% ".$lang['w_from']." ".$doc['ecdoc_deduction_from']."% ".$lang['w_total_net']."  [".K_MONEY_CURRENCY_SYMBOL."] ", 0, 0, 'R', 0);
			$pdf->Cell($data_cell_width_large, $data_cell_height, F_FormatCurrency($total_deduction_net), 1, 0, 'R', 0);
			$pdf->Cell($data_cell_width_small, $data_cell_height, F_FormatCurrency(0), 1, 0, 'R', 0);
			if (K_EC_DISPLAY_TAX_2) {
				$pdf->Cell($data_cell_width_small, $data_cell_height, F_FormatCurrency(0), 1, 0, 'R', 0);
			}
			if (K_EC_DISPLAY_TAX_3) {
				$pdf->Cell($data_cell_width_small, $data_cell_height, F_FormatCurrency(0), 1, 0, 'R', 0);
			}
			$pdf->Ln();
			
			$total_net += $total_deduction_net;
		}
		
		//print transport totals
		if ((isset($doc['ecdoc_transport_net'])) AND ($doc['ecdoc_transport_net'] > 0) ) { // transport costs
			$total_net += $doc['ecdoc_transport_net'];
			if ((!isset($doc['ecdoc_transport_tax2'])) OR (!$doc['ecdoc_transport_tax2'])) {
				$doc['ecdoc_transport_tax2'] = 0;
			}
			if ((!isset($doc['ecdoc_transport_tax3'])) OR (!$doc['ecdoc_transport_tax3'])) {
				$doc['ecdoc_transport_tax3'] = 0;
			}
			$total_tax += ($doc['ecdoc_transport_tax'] + $doc['ecdoc_transport_tax2'] + $doc['ecdoc_transport_tax3']);
			$total_tax1 += $doc['ecdoc_transport_tax'];
			$total_tax2 += $doc['ecdoc_transport_tax2'];
			$total_tax3 += $doc['ecdoc_transport_tax3'];
			
			$pdf->Cell($data_cell_width_small * 5, $data_cell_height, $lang['w_transport']."  [".K_MONEY_CURRENCY_SYMBOL."] ", 0, 0, 'R', 0);
			$pdf->Cell($data_cell_width_large, $data_cell_height, F_FormatCurrency($doc['ecdoc_transport_net']), 1, 0, 'R', 0);
			$pdf->Cell($data_cell_width_small, $data_cell_height, F_FormatCurrency($doc['ecdoc_transport_tax']), 1, 0, 'R', 0);
			if (K_EC_DISPLAY_TAX_2) {
				$pdf->Cell($data_cell_width_small, $data_cell_height, F_FormatCurrency($doc['ecdoc_transport_tax2']), 1, 0, 'R', 0);
			}
			if (K_EC_DISPLAY_TAX_3) {
				$pdf->Cell($data_cell_width_small, $data_cell_height, F_FormatCurrency($doc['ecdoc_transport_tax3']), 1, 0, 'R', 0);
			}
			$pdf->Ln();
		}
		
		if ((isset($doc['ecdoc_payment_fee'])) AND ($doc['ecdoc_payment_fee'] > 0) ) { // payment costs
			
			$pdf->Cell($data_cell_width_small * 5, $data_cell_height, $lang['w_payment_fees']."  [".K_MONEY_CURRENCY_SYMBOL."] ", 0, 0, 'R', 0);
			$pdf->Cell($data_cell_width_large, $data_cell_height, F_FormatCurrency($doc['ecdoc_payment_fee']), 1, 0, 'R', 0);
			$pdf->Cell($data_cell_width_small, $data_cell_height, F_FormatCurrency(0), 1, 0, 'R', 0);
			if (K_EC_DISPLAY_TAX_2) {
				$pdf->Cell($data_cell_width_small, $data_cell_height, F_FormatCurrency(0), 1, 0, 'R', 0);
			}
			if (K_EC_DISPLAY_TAX_3) {
				$pdf->Cell($data_cell_width_small, $data_cell_height, F_FormatCurrency(0), 1, 0, 'R', 0);
			}
			
			$pdf->Ln();
			
			$total_net += $doc['ecdoc_payment_fee'];
		}
		
		
		$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
		
		$pdf->Cell($data_cell_width_small * 5, $data_cell_height, $lang['w_total']."  [".K_MONEY_CURRENCY_SYMBOL."] ", 0, 0, 'R', 0);
		//$pdf->Cell($data_cell_width_small, $data_cell_height, '', 0, 0, 'R', 0);
		$pdf->Cell($data_cell_width_large, $data_cell_height, F_FormatCurrency($total_net), 1, 0, 'R', 0);
		//$pdf->Cell($data_cell_width_small, $data_cell_height, '', 0, 0, 'R', 0);
		$pdf->Cell($data_cell_width_small, $data_cell_height, F_FormatCurrency($total_tax1), 1, 0, 'R', 0);
		if (K_EC_DISPLAY_TAX_2) {
			$pdf->Cell($data_cell_width_small, $data_cell_height, F_FormatCurrency($total_tax2), 1, 0, 'R', 0);
		}
		if (K_EC_DISPLAY_TAX_3) {
			$pdf->Cell($data_cell_width_small, $data_cell_height, F_FormatCurrency($total_tax3), 1, 0, 'R', 0);
		}
		
		$pdf->Ln();
		$pdf->Ln();
		
		$pdf->Cell($data_cell_width_small * 5, $data_cell_height, $lang['w_total_to_pay']." ", 0, 0, 'R', 0);
		$pdf->Cell($data_cell_width_small, $data_cell_height, '', 0, 0, 'R', 0);
		$pdf->Cell($data_cell_width_large, $data_cell_height, " ".K_MONEY_CURRENCY_SYMBOL."  ".F_FormatCurrency($total_net + $total_tax), 0, 1, 'L', 0);
	}
	
	//draw horizontal line indicating end of data
	$pdf->SetDrawColor(128, 128, 128);
	$pdf->SetLineWidth(0.4);
	$pdf->Cell(0, $data_cell_height, '', 'B', 1, 'C', 0);
	// END DATA TABLE ---------------------
	
	
	$pdf->SetLineWidth(0.1);
	$pdf->SetDrawColor(0, 0, 0);
	$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
	
	// START payment data -----------------------------
	if ($doc_options[0] AND $doc['ecdoc_payment_type_id']) {
		$pdf->Ln(1);
		//get payment type
		$payment_type = "";
		$sql = "SELECT * FROM ".K_TABLE_EC_PAYMENTS_TYPES." WHERE paytype_id=".$doc['ecdoc_payment_type_id']." LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$doc_payment = unserialize($m['paytype_name']);
				$payment_type = $doc_payment[$doc_language];
			}
		}
		else {
			F_display_db_error();
		}
		
		$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
		$pdf->Cell($data_cell_width_large, $data_cell_height, $lang['w_payment'], 0, 0, 'R', 0);
		$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
		$pdf->Cell($page_width - $data_cell_width_large, $data_cell_height, $payment_type, 1, 1, 'L', 0);
		
		if ($doc_options[1] AND $doc['ecdoc_payment_details']) {
			$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
			$pdf->Cell($data_cell_width_large, $data_cell_height, $lang['w_payment_details'], 0, 0, 'R', 0);
			$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
			$pdf->MultiCell(0, $data_cell_height, $doc['ecdoc_payment_details'], 1, 'L', 0);			
		}
		$pdf->Ln(1);
	}
	// END payment data -----------------------------
	
	// START transport data -----------------------------
	if ($doc_options[5] AND $doc['ecdoc_transport']) {
		$pdf->Ln(1);
		$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
		$pdf->Cell(0, $data_cell_height, $lang['w_transport'], 1, 1, 'C', 1);
		
		$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
		$pdf->Cell($data_cell_width_large, $data_cell_height, $lang['w_driver_name'], 1, 0, 'R', 0);
		$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
		$pdf->Cell($page_width - $data_cell_width_large, $data_cell_height, $doc['ecdoc_driver_name'], 1, 1, 'L', 0);
		
		$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
		$pdf->Cell($data_cell_width_large, $data_cell_height, $lang['w_transport_subject'], 1, 0, 'R', 0);
		$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
		$pdf->Cell($page_width - $data_cell_width_large, $data_cell_height, $doc['ecdoc_transport_subject'], 1, 1, 'L', 0);
		
		$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
		$pdf->Cell($data_cell_width_large, $data_cell_height, $lang['w_parcels'], 1, 0, 'R', 0);
		$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
		$pdf->Cell($page_width - $data_cell_width_large, $data_cell_height, $doc['ecdoc_parcels']." ".$doc['ecdoc_parcels_aspect'], 1, 1, 'L', 0);
		
		if ($total_weight) {
			$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
			$pdf->Cell($data_cell_width_large, $data_cell_height, $lang['w_weight'], 1, 0, 'R', 0);
			$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
			$pdf->Cell($page_width - $data_cell_width_large, $data_cell_height, $total_weight." Kg", 1, 1, 'L', 0);
		}
		
		if ($total_volume) {
			$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
			$pdf->Cell($data_cell_width_large, $data_cell_height, $lang['w_volume'], 1, 0, 'R', 0);
			$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
			$pdf->Cell($page_width - $data_cell_width_large, $data_cell_height, round($total_volume,2)." m³", 1, 1, 'L', 0);
		}
		
		$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
		$pdf->Cell($data_cell_width_large, $data_cell_height, $lang['w_carriage'], 1, 0, 'R', 0);
		$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
		$pdf->Cell($page_width - $data_cell_width_large, $data_cell_height, $doc['ecdoc_carriage'], 1, 1, 'L', 0);
		
		$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
		$pdf->Cell($data_cell_width_large, $data_cell_height, $lang['w_transport_start_time'], 1, 0, 'R', 0);
		$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
		$pdf->Cell($page_width - $data_cell_width_large, $data_cell_height, $doc['ecdoc_transport_start_time'], 1, 1, 'L', 0);
		//signature place
		$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
		$pdf->Cell($page_width/2, $data_cell_height, $lang['w_driver_signature'], 1, 0, 'C', 1);
		$pdf->Cell($page_width/2, $data_cell_height, $lang['w_receiver_signature'], 1, 1, 'C', 1);
		$pdf->Cell($page_width/2, $data_cell_height * 2, '', 1, 0, 'C', 0);
		$pdf->Cell($page_width/2, $data_cell_height * 2, '', 1, 0, 'C', 0);
		$pdf->Ln();
	}
	// END transport data -----------------------------
	
	//conclusive notes
	if ($doc_options[8] AND $doc['ecdoc_notes_end']) {
		$pdf->Ln(1);
		$pdf->SetFont($doc_style['docstyle_main_font'], '', $doc_style['docstyle_main_font_size']);
		$pdf->MultiCell(0, $main_cell_height, $doc['ecdoc_notes_end'], 0, 'L', 0);
	}
	
	// START products description ---------------------
	if ($doc_options[9]) { 
		$pdf->AddPage();
		
		$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
		$pdf->Cell(0, $data_cell_height, $lang['w_products_description'], 1, 1, 'C', 1);
		
		$pdf->Ln();
		
		//get document details
		$sqld = "SELECT * FROM ".K_TABLE_EC_DOCUMENTS_DETAILS." WHERE docdet_doc_id='".$ecdoc_id."'";
		if($rd = F_aiocpdb_query($sqld, $db)) {
			while($doc_des = F_aiocpdb_fetch_array($rd)) {
				
				$product_description = unserialize($doc_des['docdet_description']);
				$product_description = stripslashes($product_description[$doc_language]);
				
				//print product image
				$desc_x = $doc_style['docstyle_margin_left'] + ($doc_style['docstyle_image_width'] * 1.05);
				$desc_width = ($page_width - $doc_style['docstyle_image_width']) / 3;
				$img_x = $doc_style['docstyle_margin_left'];
				$img_y = $pdf->GetY();
				if (($doc_des['docdet_image']) AND ($doc_des['docdet_image'] != K_BLANK_IMAGE)) {
					$pdf->Image(K_PATH_IMAGES_PRODUCTS.$doc_des['docdet_image'], $img_x, $img_y, $doc_style['docstyle_image_width']);
				}
				else {
					$pdf->img_rb_y = $pdf->GetY();
				}
				$img_pos = $pdf->img_rb_y; //record image bottom coordinate
				
				$pdf->SetX($desc_x);
				$pdf->SetFont($doc_style['docstyle_main_font'], 'B', $doc_style['docstyle_main_font_size'] * K_TITLE_MAGNIFICATION);
				$pdf->MultiCell(0, $main_cell_height * K_TITLE_MAGNIFICATION, $doc_des['docdet_code']." - ".$doc_des['docdet_name'], 0, 'L', 0);
			
				$pdf->SetX($desc_x);
				$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
				$pdf->Cell(0, $data_cell_height, '', 0, 1, 'C', 0); //vertical space (void line)
				
				//print barcode
				if ($doc_des['docdet_barcode']) {
					$pdf->SetX($desc_x);
					$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
					$pdf->Cell($desc_width, $data_cell_height, $lang['w_barcode'].": ", 0, 0, 'R', 0);
					$barcode_height = min(20, $img_pos - $pdf->GetY());
					$xres = 0.4;
					$barstyle = array(
						"position" => "L",
						"border" => true,
						"padding" => 1,
						"fgcolor" => array(0,0,0),
						"bgcolor" => false,
						"text" => true,
						"font" => "",
						"fontsize" => 8,
						"stretchtext" => 4
					);
					$pdf->write1DBarcode($doc_des['docdet_barcode'], K_BARCODE_TYPE, $desc_x + $desc_width, $pdf->GetY(), $page_width - $pdf->GetX(), $barcode_height, $xres, $barstyle, 'N');
				}
				
				$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
				
				$pdf->Ln();
				
				
				if ($doc_des['docdet_manufacturer_code']) {
					$pdf->SetX($desc_x);
					$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
					$pdf->Cell($desc_width, $data_cell_height, $lang['w_manufacturer_code'].": ", 0, 0, 'R', 0);
					$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
					$pdf->MultiCell(0, $data_cell_height, $doc_des['docdet_manufacturer_code'], 0, 'L', 0);
				}
				
				if ($doc_des['docdet_inventory_code']) {
					$pdf->SetX($desc_x);
					$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
					$pdf->Cell($desc_width, $data_cell_height, $lang['w_inventory_code'].": ", 0, 0, 'R', 0);
					$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
					$pdf->MultiCell(0, $data_cell_height, $doc_des['docdet_inventory_code'], 0, 'L', 0);
				}
				
				if ($doc_des['docdet_alternative_codes']) {
					$pdf->SetX($desc_x);
					$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
					$pdf->Cell($desc_width, $data_cell_height, $lang['w_alternative_codes'].": ", 0, 0, 'R', 0);
					$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
					$alternative_codes = str_replace("\n", " | ",$doc_des['docdet_alternative_codes']);
					$pdf->MultiCell(0, $data_cell_height, $alternative_codes, 0, 'L', 0);
				}
				
				if ($doc_des['docdet_manufacturer_id']) {
					$pdf->SetX($desc_x);
					$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
					$pdf->Cell($desc_width, $data_cell_height, $lang['w_manufacturer'].": ", 0, 0, 'R', 0);
					$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
					
					$sqlmf = "SELECT * FROM ".K_TABLE_EC_MANUFACTURERS." WHERE manuf_id='".$doc_des['docdet_manufacturer_id']."' LIMIT 1";
					if($rmf = F_aiocpdb_query($sqlmf, $db)) {
						if($mmf = F_aiocpdb_fetch_array($rmf)) {
							$product_manufacturer = $mmf['manuf_name'];
							if ($mmf['manuf_url']) {
								$product_manufacturer .= " (".$mmf['manuf_url'].")";
							}
						}
					}
					else {
						F_display_db_error();
					}
					$pdf->MultiCell(0, $data_cell_height, $product_manufacturer, 0, 'L', 0);
				}
				
				if ($doc_des['docdet_warranty']) {
					$pdf->SetX($desc_x);
					$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
					$pdf->Cell($desc_width, $data_cell_height, $lang['w_warranty'].": ", 0, 0, 'R', 0);
					$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
					$pdf->MultiCell(0, $data_cell_height, $doc_des['docdet_warranty']." ".$lang['w_months'], 0, 'L', 0);
				}
				
				if ($doc_des['docdet_unit_of_measure_id']) {
					//get unit of measure name
					$sqlu = "SELECT * FROM ".K_TABLE_EC_UNITS_OF_MEASURE." WHERE unit_id=".$doc_des['docdet_unit_of_measure_id']." LIMIT 1";
					if($ru = F_aiocpdb_query($sqlu, $db)) {
						if($mu = F_aiocpdb_fetch_array($ru)) {
							$unit_of_measure = $mu['unit_name'];
						}
						else {
							$unit_of_measure = "";
						}
					}
					else {
						F_display_db_error();
					}
					if ($unit_of_measure) {
						$pdf->SetX($desc_x);
						$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
						$pdf->Cell($desc_width, $data_cell_height, $lang['w_unit'].": ", 0, 0, 'R', 0);
						$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
						$pdf->MultiCell(0, $data_cell_height, $unit_of_measure, 0, 'L', 0);
					}
				}
				
				if ($doc_des['docdet_transportable']) {
					if ($doc_des['docdet_length'] AND $doc_des['docdet_width'] AND $doc_des['docdet_height']) {
						$pdf->SetX($desc_x);
						$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
						$pdf->Cell($desc_width, $data_cell_height, $lang['w_packaged_measures'].": ", 0, 0, 'R', 0);
						$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
						
						$tempstr = "".$doc_des['docdet_length']."m x ".$doc_des['docdet_width']."m x ".$doc_des['docdet_height']."m =";
						$tempstr .= " ".($doc_des['docdet_length']*$doc_des['docdet_width']*$doc_des['docdet_height'])."m³";
						$pdf->MultiCell(0, $data_cell_height, $tempstr, 0, 'L', 0);
					}
					
					if ($doc_des['docdet_weight_per_unit']) {
						$pdf->SetX($desc_x);
						$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
						$pdf->Cell($desc_width, $data_cell_height, $lang['w_packaged_weight'].": ", 0, 0, 'R', 0);
						$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
						$pdf->MultiCell(0, $data_cell_height, $doc_des['docdet_weight_per_unit']." Kg", 0, 'L', 0);
					}
				}
				
				if ($doc_des['docdet_cost']) {
					$pdf->SetX($desc_x);
					$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
					$pdf->Cell($desc_width, $data_cell_height, $lang['w_cost_per_unit'].": ", 0, 0, 'R', 0);
					$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
					$pdf->MultiCell(0, $data_cell_height, K_MONEY_CURRENCY_SYMBOL." ".$doc_des['docdet_cost']." +".$lang['w_ec_tax'], 0, 'L', 0);
				}
				
				if ($doc_des['docdet_tax']) {
					$pdf->SetX($desc_x);
					$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
					$pdf->Cell($desc_width, $data_cell_height, $lang['w_ec_tax'].": ", 0, 0, 'R', 0);
					$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
					$tax_amount = $doc_des['docdet_cost'] * ($doc_des['docdet_tax'] / 100);
					$pdf->MultiCell(0, $data_cell_height, $doc_des['docdet_tax']."%  (".K_MONEY_CURRENCY_SYMBOL. " ".F_FormatCurrency($tax_amount).")", 0, 'L', 0);
				}
				
				$pdf->SetY(max($img_pos, $pdf->GetY()));
				
				if ($product_description) {
					$pdf->Ln();
					$pdf->SetFont($doc_style['docstyle_main_font'], '', $doc_style['docstyle_main_font_size']);
					//$pdf->MultiCell(0, $data_cell_height, F_html_to_text($product_description, false, false), 0, 'L', 0);
					$pdf->WriteHTML($product_description, true);
				}
				
				$pdf->Cell(0, $data_cell_height, '', 0, 1, 'C', 0);
				$pdf->Cell(0, $data_cell_height, '', 'T', 1, 'C', 0);
			}
		}
		else {
			F_display_db_error();
		}
	}
	// END products description ---------------------
	
	if (!$output_mode) {
		//Close and output PDF document
		$pdf->Output();
	}
	else {
		return $pdf->GetPDFData();
	}
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
