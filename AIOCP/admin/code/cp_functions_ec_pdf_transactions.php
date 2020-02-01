<?php
//============================================================+
// File name   : cp_functions_ec_pdf_transactions.php          
// Begin       : 2002-09-09                                    
// Last Update : 2003-10-12                                    
//                                                             
// Description : Create PDF transaction document               
//                                                             
// Note: actually do not support right-to-left languages       
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

// ------------------------------------------------------------
// generate a pdf transaction document
// $mode=false => output to a browser
// $mode=true => return content
// ------------------------------------------------------------
function F_generate_pdf_transactions($sql, $display_mode, $display_option, $filter_values, $output_mode=false) {
	global $l, $db, $selected_language;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_company_data.'.CP_EXT);
	require_once('../../shared/code/cp_functions_html2txt.'.CP_EXT);
	require_once('../../shared/pdf/cp_ec_pdf_doc.'.CP_EXT);
	
	$filter_values = unserialize(stripslashes($filter_values));
	$display_option = unserialize(stripslashes($display_option));
	
	//some static values
	$font_size_data = 6;
	$cell_width_currency = 15;
	$cell_width_flag = 2.5;
	$cell_width_date = 13;
	$cell_width_id = 8;
	$cell_width_refer = 20;
	
	$doc_language = $selected_language;
	if (K_DOCUMENTS_LANGUAGE) {
		$doc_language = "".K_DOCUMENTS_LANGUAGE."";
	}
	
	//get language template table
	require_once('../../shared/code/cp_functions_doc_language.'.CP_EXT);
	$lang = F_get_pdf_language_templates($doc_language);
	
	//get document style
	$sqlds = "SELECT * FROM ".K_TABLE_EC_DOCUMENTS_STYLES." WHERE docstyle_name='_transactions' LIMIT 1";
	if($rds = F_aiocpdb_query($sqlds, $db)) {
		if(!$doc_style = F_aiocpdb_fetch_array($rds)) {
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
	$pdf->SetTitle($lang['w_transactions']);
	$pdf->SetSubject($lang['w_transactions']." ".$filter_values[0]." - ".$filter_values[1]."");
	$pdf->SetKeywords($company_data['name']." ".$lang['w_transactions']);
	
	//set margins
	$pdf->SetMargins($doc_style['docstyle_margin_left'], $doc_style['docstyle_margin_top'], $doc_style['docstyle_margin_right']);
	//set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, $doc_style['docstyle_margin_bottom']);
	
	$pdf->SetHeaderMargin($doc_style['docstyle_header']);
	$pdf->SetFooterMargin($doc_style['docstyle_footer']);
	
	$pdf->header_font = Array($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
	$pdf->footer_font = Array($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
	
	$pdf->lang_templates = $l;
	$pdf->company_data = $company_data;
	
	//initialize document
	$pdf->Open();
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
	$data_cell_width_large = round(0.2 * $page_width, 2);
	$data_cell_width_small = round(0.1 * $page_width, 2);
	$data_cell_height = round((K_CELL_HEIGHT_RATIO * $doc_style['docstyle_data_font_size']) / $pdf->k, 2);
	$main_cell_height = round((K_CELL_HEIGHT_RATIO * $doc_style['docstyle_main_font_size']) / $pdf->k, 2);
	$info_cell_width = round($page_width / 4, 2);
	
	$pdf->SetFillColor(204, 204, 204);
	$pdf->SetLineWidth(0.1);
	$pdf->SetDrawColor(0, 0, 0);
	
	//print document name (title)
	$pdf->SetFont($doc_style['docstyle_main_font'], 'B', $doc_style['docstyle_main_font_size'] * K_TITLE_MAGNIFICATION);
	$pdf->Cell(0, $main_cell_height * K_TITLE_MAGNIFICATION, $lang['t_transactions'], 0, 1, 'C', 0);
	$pdf->Ln();
	
	
	// display filter options =================
	
	//print filter header
	$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
	$pdf->Cell(0, $data_cell_height, strtoupper($lang['w_filter']), 0, 1, 'C', 1);
	$pdf->Ln();
	
	//draw a line here
	
	$temp_name = $lang['w_transaction'];
	if ($display_mode) {
		$temp_name = $lang['w_payment'];
	}
	$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
	$pdf->Cell($data_cell_width_large, $data_cell_height, $lang['w_mode'], 0, 0, 'R', 0);
	$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
	$pdf->Cell($page_width - $data_cell_width_large, $data_cell_height, $temp_name, 1, 1, 'L', 0);

	if ($filter_values[0]) {
		$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
		$pdf->Cell($data_cell_width_large, $data_cell_height, $lang['w_date_start'], 0, 0, 'R', 0);
		$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
		$pdf->Cell($page_width - $data_cell_width_large, $data_cell_height, $filter_values[0], 1, 1, 'L', 0);
	}

	if ($filter_values[1]) {
		$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
		$pdf->Cell($data_cell_width_large, $data_cell_height, $lang['w_date_end'], 0, 0, 'R', 0);
		$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
		$pdf->Cell($page_width - $data_cell_width_large, $data_cell_height, $filter_values[1], 1, 1, 'L', 0);
	}

	$temp_name = $lang['w_all'];
	if ($filter_values[2]) { //work
		$sqlwrk = "SELECT * FROM ".K_TABLE_EC_WORKS." WHERE work_id=".$filter_values[2]." LIMIT 1";
		if($rwrk = F_aiocpdb_query($sqlwrk, $db)) {
			if($mwrk = F_aiocpdb_fetch_array($rwrk)) {
				$temp_name = $mwrk['work_date_start']." - ".$mwrk['work_name'];
			}
		}
		else {
			F_display_db_error();
		}
	}
	$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
	$pdf->Cell($data_cell_width_large, $data_cell_height, $lang['w_work'], 0, 0, 'R', 0);
	$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
	$pdf->Cell($page_width - $data_cell_width_large, $data_cell_height, $temp_name, 1, 1, 'L', 0);

	$temp_name = $lang['w_all'];
	if ($filter_values[3]) { //transaction type
		$sqltt = "SELECT * FROM ".K_TABLE_EC_TRANSACTIONS_TYPES." WHERE transtype_id=".$filter_values[3]." LIMIT 1";
		if($rtt = F_aiocpdb_query($sqltt, $db)) {
			if($mtt = F_aiocpdb_fetch_array($rtt)) {
				$temp_name = unserialize($mtt['transtype_name']);
				$temp_name = $temp_name[$doc_language];
			}
		}
		else {
			F_display_db_error();
		}
	}
	$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
	$pdf->Cell($data_cell_width_large, $data_cell_height, $lang['w_type'], 0, 0, 'R', 0);
	$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
	$pdf->Cell($page_width - $data_cell_width_large, $data_cell_height, $temp_name, 1, 1, 'L', 0);

	if ($filter_values[4] < 0) {
		$temp_name = $lang['w_all'];
	}
	elseif ($filter_values[4] == 0) {
		$temp_name = $lang['w_real'];
	}
	elseif ($filter_values[4] > 0) {
		$temp_name = $lang['w_virtual'];
	}
	$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
	$pdf->Cell($data_cell_width_large, $data_cell_height, $lang['w_type'], 0, 0, 'R', 0);
	$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
	$pdf->Cell($page_width - $data_cell_width_large, $data_cell_height, $temp_name, 1, 1, 'L', 0);

	$temp_name = $lang['w_all'];
	if ($filter_values[5]) { //supplier
		$sqlsup = "SELECT * FROM ".K_TABLE_USERS_COMPANY." WHERE company_userid=".$filter_values[5]." LIMIT 1";
		if($rsup = F_aiocpdb_query($sqlsup, $db)) {
			if($msup = F_aiocpdb_fetch_array($rsup)) {
				$temp_name = $msup['company_name'];
			}
		}
		else {
			F_display_db_error();
		}
	}
	$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
	$pdf->Cell($data_cell_width_large, $data_cell_height, $lang['w_supplier'], 0, 0, 'R', 0);
	$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
	$pdf->Cell($page_width - $data_cell_width_large, $data_cell_height, $temp_name, 1, 1, 'L', 0);

	if ($filter_values[6] < 0) {
		$temp_name = $lang['w_out']." (-)";
	}
	elseif ($filter_values[6] == 0) {
		$temp_name = $lang['w_all'];
	}
	elseif ($filter_values[6] > 0) {
		$temp_name = $lang['w_in']." (+)";
	}
	$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
	$pdf->Cell($data_cell_width_large, $data_cell_height, $lang['w_direction'], 0, 0, 'R', 0);
	$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
	$pdf->Cell($page_width - $data_cell_width_large, $data_cell_height, $temp_name, 1, 1, 'L', 0);

	switch ($filter_values[7]) {
		default:
		case 0: { // all
			$temp_name = $lang['w_all'];
			break;
		}
		case 1: { // paid
			$temp_name = $lang['w_paid'];
			break;
		}
		case 2: { // unpaid
			$temp_name = $lang['w_unpaid'];
			break;
		}
		case 3: { // partial
			$temp_name = $lang['w_partial'];
			break;
		}
		case 4: { // paid and partial
			$temp_name = $lang['w_paid']." + ".$lang['w_partial'];
			break;
		}
		case 5: { // unpaid and partial
			$temp_name = $lang['w_unpaid']." + ".$lang['w_partial'];
			break;
		}
	}
	$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
	$pdf->Cell($data_cell_width_large, $data_cell_height, $lang['w_paid'], 0, 0, 'R', 0);
	$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
	$pdf->Cell($page_width - $data_cell_width_large, $data_cell_height, $temp_name, 1, 1, 'L', 0);

	$temp_name = $lang['w_all'];
	if ($filter_values[8]) { //payment category
		$sqlpc = "SELECT * FROM ".K_TABLE_EC_PAYMENTS_CATEGORIES." WHERE paycat_id=".$filter_values[8]." LIMIT 1";
		if($rpc = F_aiocpdb_query($sqlpc, $db)) {
			if($mpc = F_aiocpdb_fetch_array($rpc)) {
				$temp_name = unserialize($mpc['paycat_name']);
				$temp_name = $temp_name[$doc_language];
			}
		}
		else {
			F_display_db_error();
		}
	}
	$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
	$pdf->Cell($data_cell_width_large, $data_cell_height, $lang['w_payment_category'], 0, 0, 'R', 0);
	$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
	$pdf->Cell($page_width - $data_cell_width_large, $data_cell_height, $temp_name, 1, 1, 'L', 0);

	$temp_name = $lang['w_all'];
	if ($filter_values[9]) { //payment type
		$sqlpt = "SELECT * FROM ".K_TABLE_EC_PAYMENTS_TYPES." WHERE paytype_id=".$filter_values[9]." LIMIT 1";
		if($rpt = F_aiocpdb_query($sqlpt, $db)) {
			if($mpt = F_aiocpdb_fetch_array($rpt)) {
				$temp_name = unserialize($mpt['paytype_name']);
				$temp_name = $temp_name[$doc_language];
			}
		}
		else {
			F_display_db_error();
		}
	}
	$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
	$pdf->Cell($data_cell_width_large, $data_cell_height, $lang['w_payment'], 0, 0, 'R', 0);
	$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
	$pdf->Cell($page_width - $data_cell_width_large, $data_cell_height, $temp_name, 1, 1, 'L', 0);

	$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
	$pdf->Cell($data_cell_width_large, $data_cell_height, $lang['w_keywords'], 0, 0, 'R', 0);
	$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
	$pdf->MultiCell($page_width - $data_cell_width_large, $data_cell_height, $filter_values[10], 'LRTB', 'L', 0);

	//draw a line here
	$pdf->Ln();
	$pdf->Cell(0, $data_cell_height, "", 0, 1, 'C', 1);
	//$pdf->Cell(0, 1, "", 0, 1, 'C', 1);

	// END display filter options =================

	// DISPLAY TRANSACTION DATA =======================
	
	$cell_width_left_total = $page_width - (9 * $cell_width_currency);
	$cell_width_tax = $cell_width_left_total - ($cell_width_flag * 3) - $cell_width_id - $cell_width_date;
	$cell_width_left_transaction = $cell_width_left_total - $cell_width_tax;
	$cell_width_left_payment = ($cell_width_flag * 3) + $cell_width_id;
	$cell_width_left_payment_data = $cell_width_left_total + ($cell_width_currency * 3);
	if (!$display_option[8]) {
		$cell_width_left_payment_data -= $cell_width_currency;
	}
	if (!$display_option[9]) {
		$cell_width_left_payment_data -= $cell_width_currency;
	}
	
	$cell_width_work = ($page_width - ($cell_width_flag * 3) - $cell_width_id - $cell_width_date - $cell_width_refer)/2;
	$cell_width_supplier = $cell_width_work;
	
	$data_cell_height = round((K_CELL_HEIGHT_RATIO * $font_size_data) / $pdf->k, 2);
	
	//initialize variables
	$rowodd = 0;
	
	$total_amount_in = 0;
	$total_tax_in = 0;
	
	$paid_amount_in = 0;
	$paid_tax_in = 0;
	
	$total_amount_out = 0;
	$total_tax_out = 0;
	
	$paid_amount_out = 0;
	$paid_tax_out = 0;
	
	$previous_transition_id = 0;
	
	$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $font_size_data);
	
	// START table headers -----------------
	
	$pdf->SetFillColor(240, 240, 240);
	
	// start table row
	$pdf->Ln();
	
	//virtual
	$pdf->Cell($cell_width_flag, $data_cell_height, "", 1, 0, 'C', 1);
	
	//unpaid
	$pdf->Cell($cell_width_flag, $data_cell_height, "", 1, 0, 'C', 1);
	
	$pdf->Cell($cell_width_flag, $data_cell_height, "", 1, 0, 'C', 1);
	
	$pdf->Cell($cell_width_id, $data_cell_height, "#", 1, 0, 'C', 1);
	
	$pdf->Cell($cell_width_date, $data_cell_height, $lang['w_date'], 1, 0, 'C', 1);
	
	$temp_x = $pdf->GetX();
	
	if ($display_option[3]) {
		$pdf->Cell($cell_width_refer, $data_cell_height, $lang['w_referer'], 1, 0, 'L', 1);
	}
	
	if ($display_option[0]) {
		$pdf->Cell($cell_width_work, $data_cell_height, $lang['w_work'], 1, 0, 'L', 1);
	}
	
	if ($display_option[4]) {
		$pdf->Cell($cell_width_supplier, $data_cell_height, $lang['w_supplier'], 1, 0, 'L', 1);
	}
	
	if (($display_option[0]) OR ($display_option[3]) OR ($display_option[4]) ) {
	$pdf->Ln();
	}
	
	$temp_name = "";
	if ($display_option[1]) {
		$temp_name = $lang['w_type'];
	}
	
	if ($display_option[2]) {
		$temp_name .= " - ".$lang['w_description'];
	}
	
	if (($display_option[1]) OR ($display_option[2])) {
		$pdf->SetX($temp_x);
		$pdf->MultiCell($page_width - $cell_width_date - $cell_width_left_payment, $data_cell_height, $temp_name, 1, 'L', 1);
		$pdf->Cell($cell_width_left_transaction, "", 0, 0, 'L', 0);
	}
	elseif (($display_option[0]) OR ($display_option[3]) OR ($display_option[4]) ) {
		$pdf->Ln();
		$pdf->Cell($cell_width_left_transaction, "", 0, 0, 'L', 0);
	}
	
	$pdf->Cell($cell_width_tax, $data_cell_height, $lang['w_ec_tax'], 'LRT', 0, 'C', 1);
	
	$temp_width = $cell_width_currency;
	
	if ($display_option[8]) {
		$temp_width += $cell_width_currency;
	}
	
	if ($display_option[9]) {
		$temp_width += $cell_width_currency;
	}
	
	//total
	$pdf->Cell($temp_width, $data_cell_height, $lang['w_total']." [".K_MONEY_CURRENCY_SYMBOL."]", 'LRT', 0, 'C', 1);
	
	
	//paid
	$pdf->Cell($temp_width, $data_cell_height, $lang['w_paid']." [".K_MONEY_CURRENCY_SYMBOL."]", 'LRT', 0, 'C', 1);
	
	//difference
	if ($display_option[7]) {
		$pdf->Cell($temp_width, $data_cell_height, $lang['w_difference']." [".K_MONEY_CURRENCY_SYMBOL."]", 'LRT', 0, 'C', 1);
	}
	
	$pdf->Ln();
	$pdf->Cell($cell_width_left_transaction, "", 0, 0, 'L', 0);
	
	$pdf->Cell($cell_width_tax, $data_cell_height, "[%]", 'LRB', 0, 'C', 1);
	
	//total
	$pdf->Cell($cell_width_currency, $data_cell_height, $lang['w_total'], 'LRB', 0, 'C', 1);
	
	if ($display_option[8]) {
		$pdf->Cell($cell_width_currency, $data_cell_height, $lang['w_net'], 'LRB', 0, 'C', 1);
	}
	
	if ($display_option[9]) {
		$pdf->Cell($cell_width_currency, $data_cell_height, $lang['w_tax'], 'LRB', 0, 'C', 1);
	}
	
	//paid
	$pdf->Cell($cell_width_currency, $data_cell_height, $lang['w_total'], 'LRB', 0, 'C', 1);
	
	if ($display_option[8]) {
		$pdf->Cell($cell_width_currency, $data_cell_height, $lang['w_net'], 'LRB', 0, 'C', 1);
	}
	
	if ($display_option[9]) {
		$pdf->Cell($cell_width_currency, $data_cell_height, $lang['w_tax'], 'LRB', 0, 'C', 1);
	}
	
	if ($display_option[7]) {
		//difference
		$pdf->Cell($cell_width_currency, $data_cell_height, $lang['w_total'], 'LRB', 0, 'C', 1);
		
		if ($display_option[8]) {
			$pdf->Cell($cell_width_currency, $data_cell_height, $lang['w_net'], 'LRB', 0, 'C', 1);
		}
		
		if ($display_option[9]) {
			$pdf->Cell($cell_width_currency, $data_cell_height, $lang['w_tax'], 'LRB', 0, 'C', 1);
		}
	}
	
	// start new table row for payment header
		
	$pdf->Ln();
	$pdf->Cell($cell_width_left_payment, $data_cell_height, "", 0, 0, 'C', 0);
	
	$pdf->Cell($cell_width_date, $data_cell_height, $lang['w_date'], 1, 0, 'C', 1);
	
	$temp_name = "";
	if ($display_option[5]) {
		$temp_name .= $lang['w_payment'];
	}
	
	if ($display_option[6]) {
		$temp_name .= " - ".$lang['w_payment_details'];
	}
	
	if ( ($display_option[5]) OR ($display_option[6]) ) {
	$pdf->MultiCell($page_width - $cell_width_date - $cell_width_left_payment, $data_cell_height, $temp_name, 1, 'L', 1);
	$pdf->Cell($cell_width_left_payment_data, $data_cell_height, "", 0, 0, 'L', 0);
	}
	else {
		$pdf->Cell($cell_width_left_payment_data - $cell_width_date - $cell_width_left_payment, $data_cell_height, "", 1, 0, 'L', 0);
	}
	
	$pdf->Cell($cell_width_currency, $data_cell_height, $lang['w_total'], 1, 0, 'C', 1);
	
	if ($display_option[8]) {
		$pdf->Cell($cell_width_currency, $data_cell_height, $lang['w_net'], 1, 0, 'C', 1);
	}
	
	if ($display_option[9]) {
		$pdf->Cell($cell_width_currency, $data_cell_height, $lang['w_tax'], 1, 0, 'C', 1);
	}
	
	// END table headers -----------------

	//draw horizontal line
	$pdf->Ln();
	$pdf->Cell(0, $data_cell_height, "", 'B', 1, 'C', 0);
	$pdf->Cell(0, $data_cell_height, "", 0, 0, 'C', 0);

	$pdf->SetFillColor(240, 240, 240);
	$pdf->Ln();
	$pdf->SetFont($doc_style['docstyle_data_font'], '', $font_size_data);
	
	//start table data
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			
			if ($m['transpay_amount'] > 0) {
				$this_paid_amount = ($m['mtrans_direction'] * $m['transpay_amount']);
				$this_paid_tax = $this_paid_amount * ($m['mtrans_tax'] / (100 + $m['mtrans_tax']));
				$this_paid_net = $this_paid_amount - $this_paid_tax;
				
				if ($m['mtrans_direction'] > 0) {
					$paid_amount_in += $this_paid_amount;
					$paid_tax_in += $this_paid_tax;
				}
				else {
					$paid_amount_out += $this_paid_amount;
					$paid_tax_out += $this_paid_tax;
				}
			}
			
			if ($m['mtrans_id'] != $previous_transition_id) { //write unique transaction only one time
				$previous_transition_id = $m['mtrans_id'];
				
				//change style for each transaction row
				if (isset($rowodd) AND ($rowodd)) {
					$rowodd=0;
				} else {
					$rowodd=1;
				}
				
				// calculate totals
				$this_total_amount = ($m['mtrans_direction'] * $m['mtrans_amount']);
				$this_tax = $this_total_amount * ($m['mtrans_tax'] / (100 + $m['mtrans_tax']));
				$this_net = $this_total_amount - $this_tax;
				
				if ($m['mtrans_direction'] > 0) {
					$total_amount_in += $this_total_amount;
					$total_tax_in += $this_tax;
				}
				else {
					$total_amount_out += $this_total_amount;
					$total_tax_out += $this_tax;
				}
				
				// calculate paid totals
				$this_total_paid_amount = ($m['mtrans_direction'] * $m['mtrans_paid_amount']);
				$this_total_paid_tax = $this_total_paid_amount * ($m['mtrans_tax'] / (100 + $m['mtrans_tax']));
				$this_total_paid_net = $this_total_paid_amount - $this_total_paid_tax;
				
				// start table row
				$pdf->Ln();
				
				//virtual
				if ($m['mtrans_virtual']) {
					$pdf->SetFillColor(0, 0, 0);
					$pdf->Cell($cell_width_flag, $data_cell_height, "", 1, 0, 'C', 1);
				}
				else {
					$pdf->SetFillColor(255, 255, 255);
					$pdf->Cell($cell_width_flag, $data_cell_height, "", 1, 0, 'C', 1);
				}
				
				//unpaid
				if (($this_total_amount - $this_total_paid_amount) != 0) {
					$pdf->SetFillColor(255, 0, 0);
					$pdf->Cell($cell_width_flag, $data_cell_height, "U", 1, 0, 'C', 1);
				}
				else {
					$pdf->SetFillColor(0, 255, 0);
					$pdf->Cell($cell_width_flag, $data_cell_height, "P", 1, 0, 'C', 1);
				}
				
				$pdf->SetFillColor(204, 204, 204);
				
				// direction
				if ($m['mtrans_direction'] < 0) {
					$pdf->SetFillColor(255, 0, 0);
					$pdf->Cell($cell_width_flag, $data_cell_height, "-", 1, 0, 'C', 1);
				}
				else {
					$pdf->SetFillColor(0, 255, 0);
					$pdf->Cell($cell_width_flag, $data_cell_height, "+", 1, 0, 'C', 1);
				}
				
				$pdf->Cell($cell_width_id, $data_cell_height, $m['mtrans_id'], 1, 0, 'R', 0);
				
				$pdf->Cell($cell_width_date, $data_cell_height, $m['mtrans_date'], 1, 0, 'C', 0);
				
				$temp_x = $pdf->GetX();
				
				if ($display_option[3]) {
					$pdf->Cell($cell_width_refer, $data_cell_height, $m['mtrans_doc_ref'], 1, 0, 'L', 0);
				}
				
				
				if ($display_option[0]) {
					$temp_name = "";
					// work
					$sqlwrk = "SELECT * FROM ".K_TABLE_EC_WORKS." WHERE work_id='".$m['mtrans_work_id']."' LIMIT 1";
					if($rwrk = F_aiocpdb_query($sqlwrk, $db)) {
						if($mwrk = F_aiocpdb_fetch_array($rwrk)) {
							$temp_name = $mwrk['work_name'];
						}
					}
					else {
						F_display_db_error();
					}
					$pdf->Cell($cell_width_work, $data_cell_height, $temp_name, 1, 0, 'L', 0);
				}
				
				if ($display_option[4]) {
					$temp_name = "";
					if ($m['mtrans_supplier']) {
						$sqlsup = "SELECT * FROM ".K_TABLE_USERS_COMPANY." WHERE company_userid=".$m['mtrans_supplier']." LIMIT 1";
						if($rsup = F_aiocpdb_query($sqlsup, $db)) {
							if($msup = F_aiocpdb_fetch_array($rsup)) {
								$temp_name = $msup['company_name'];
							}
						}
						else {
							F_display_db_error();
						}
					}
					$pdf->Cell($cell_width_supplier, $data_cell_height, $temp_name, 1, 0, 'L', 0);
				}
				
				if (($display_option[0]) OR ($display_option[3]) OR ($display_option[4]) ) {
					$pdf->Ln();
				}
				
				$temp_name = "";
				if ($display_option[1]) {
					
					// transaction type
					$sqltt = "SELECT * FROM ".K_TABLE_EC_TRANSACTIONS_TYPES." WHERE transtype_id='".$m['mtrans_type']."' LIMIT 1";
					if($rtt = F_aiocpdb_query($sqltt, $db)) {
						if($mtt = F_aiocpdb_fetch_array($rtt)) {
							$temp_name = unserialize($mtt['transtype_name']);
							$temp_name = $temp_name[$doc_language];
						}
					}
					else {
						F_display_db_error();
					}
				}
				
				if ($display_option[2]) {
					$temp_name .= " - ".$m['mtrans_description'];
				}
				
				if (($display_option[1]) OR ($display_option[2])) {
				$pdf->SetX($temp_x);
				$pdf->MultiCell($page_width - $cell_width_date - $cell_width_left_payment, $data_cell_height, $temp_name, 1, 'L', 0);
				$pdf->Cell($cell_width_left_transaction, "", 0, 0, 'L', 0);
				}
				elseif (($display_option[0]) OR ($display_option[3]) OR ($display_option[4]) ) {
					$pdf->Ln();
					$pdf->Cell($cell_width_left_transaction, "", 0, 0, 'L', 0);
				}
				
				// start values -------------------
				
				
				$pdf->Cell($cell_width_tax, $data_cell_height, $m['mtrans_tax'], 1, 0, 'R', 0);
				
				//total
				$pdf->Cell($cell_width_currency, $data_cell_height, F_FormatCurrency($this_total_amount), 1, 0, 'R', 0);
				
				if ($display_option[8]) {
					$pdf->Cell($cell_width_currency, $data_cell_height, F_FormatCurrency($this_net), 1, 0, 'R', 0);
				}
				
				if ($display_option[9]) {
					$pdf->Cell($cell_width_currency, $data_cell_height, F_FormatCurrency($this_tax), 1, 0, 'R', 0);
				}
				
				//paid
				$pdf->Cell($cell_width_currency, $data_cell_height, F_FormatCurrency($this_total_paid_amount), 1, 0, 'R', 0);
				
				if ($display_option[8]) {
					$pdf->Cell($cell_width_currency, $data_cell_height, F_FormatCurrency($this_total_paid_net), 1, 0, 'R', 0);
				}
				
				if ($display_option[9]) {
					$pdf->Cell($cell_width_currency, $data_cell_height, F_FormatCurrency($this_total_paid_tax), 1, 0, 'R', 0);
				}
				
				if ($display_option[7]) {
					//difference
					$pdf->Cell($cell_width_currency, $data_cell_height, F_FormatCurrency($this_total_amount - $this_total_paid_amount), 1, 0, 'R', 0);
					
					if ($display_option[8]) {
						$pdf->Cell($cell_width_currency, $data_cell_height, F_FormatCurrency($this_net - $this_total_paid_net), 1, 0, 'R', 0);
					}
					
					if ($display_option[9]) {
						$pdf->Cell($cell_width_currency, $data_cell_height, F_FormatCurrency($this_tax - $this_total_paid_tax), 1, 0, 'R', 0);
					}
				}
			} //end unique transaction
			
			// start new table row for payment data
			if ($m['transpay_date']) {
				
				$pdf->Ln();
				$pdf->Cell($cell_width_left_payment, $data_cell_height, "", 0, 0, 'C', 0);
				
				$pdf->Cell($cell_width_date, $data_cell_height, $m['transpay_date'], 1, 0, 'C', 0);
				
				$temp_name = "";
				if ($display_option[5]) {
					//get payment type name
					$sqlpay = "SELECT * FROM ".K_TABLE_EC_PAYMENTS_TYPES." WHERE paytype_id='".$m['transpay_payment_id']."' LIMIT 1";
					if($rpay = F_aiocpdb_query($sqlpay, $db)) {
						if($mpay = F_aiocpdb_fetch_array($rpay)) {
							$temp_name = unserialize($mpay['paytype_name']);
							$temp_name = $temp_name[$doc_language];
						}
					}
					else {
						F_display_db_error();
					}
				}
				
				if ($display_option[6]) {
					$temp_name .= " - ".$m['transpay_payment_details'];
				}
				
				if ( ($display_option[5]) OR ($display_option[6]) ) {
				$pdf->MultiCell($page_width - $cell_width_date - $cell_width_left_payment, $data_cell_height, $temp_name, 1, 'L', 0);
				$pdf->Cell($cell_width_left_payment_data, $data_cell_height, "", 0, 0, 'L', 0);
				}
				else {
					$pdf->Cell($cell_width_left_payment_data - $cell_width_date - $cell_width_left_payment, $data_cell_height, "", 1, 0, 'L', 0);
				}
				
				$pdf->Cell($cell_width_currency, $data_cell_height, F_FormatCurrency($this_paid_amount), 1, 0, 'R', 0);
				
				if ($display_option[8]) {
					$pdf->Cell($cell_width_currency, $data_cell_height, F_FormatCurrency($this_paid_net), 1, 0, 'R', 0);
				}
				
				if ($display_option[9]) {
					$pdf->Cell($cell_width_currency, $data_cell_height, F_FormatCurrency($this_paid_tax), 1, 0, 'R', 0);
				}
			}
		}
	}
	else {
		F_display_db_error();
	}
	
	//display totals
		
		//draw horizontal line
		$pdf->Ln();
		$pdf->Cell(0, $data_cell_height, "", 'B', 1, 'C', 0);
		$pdf->Cell(0, $data_cell_height, "", 0, 0, 'C', 0);
		
		$paid_net_in = $paid_amount_in - $paid_tax_in;
		$paid_net_out = $paid_amount_out - $paid_tax_out;
		
		//in (+)
		$pdf->Ln();
		
		$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $font_size_data);
		$pdf->Cell($cell_width_left_total, $data_cell_height, $l['w_total']." ".$l['w_in'], 0, 0, 'R', 0);
		
		$pdf->SetFont($doc_style['docstyle_data_font'], '', $font_size_data);
		
		if (!$display_mode) {
			$total_net_in = $total_amount_in - $total_tax_in;
			$total_net_out = $total_amount_out - $total_tax_out;
			
			$pdf->Cell($cell_width_currency, $data_cell_height, F_FormatCurrency($total_amount_in), 1, 0, 'R', 0);
			
			if ($display_option[8]) {
				$pdf->Cell($cell_width_currency, $data_cell_height, F_FormatCurrency($total_net_in), 1, 0, 'R', 0);
			}
			
			if ($display_option[9]) {
				$pdf->Cell($cell_width_currency, $data_cell_height, F_FormatCurrency($total_tax_in), 1, 0, 'R', 0);
			}
		}
		else {
			$pdf->Cell($cell_width_currency * 3, $data_cell_height, "", 0, 0, 'R', 0);
		}
		
		$pdf->Cell($cell_width_currency, $data_cell_height, F_FormatCurrency($paid_amount_in), 1, 0, 'R', 0);
		
		if ($display_option[8]) {
			$pdf->Cell($cell_width_currency, $data_cell_height, F_FormatCurrency($paid_net_in), 1, 0, 'R', 0);
		}
		
		if ($display_option[9]) {
			$pdf->Cell($cell_width_currency, $data_cell_height, F_FormatCurrency($paid_tax_in), 1, 0, 'R', 0);
		}
		
		if ((!$display_mode) AND ($display_option[7]) ) {
			$pdf->Cell($cell_width_currency, $data_cell_height, F_FormatCurrency($total_amount_in - $paid_amount_in), 1, 0, 'R', 0);
			
			if ($display_option[8]) {
				$pdf->Cell($cell_width_currency, $data_cell_height, F_FormatCurrency($total_net_in - $paid_net_in), 1, 0, 'R', 0);
			}
			
			if ($display_option[9]) {
				$pdf->Cell($cell_width_currency, $data_cell_height, F_FormatCurrency($total_tax_in - $paid_tax_in), 1, 0, 'R', 0);
			}
		}
		
		//out (-)
		$pdf->Ln();
		
		$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $font_size_data);
		$pdf->Cell($cell_width_left_total, $data_cell_height, $l['w_total']." ".$l['w_out'], 0, 0, 'R', 0);
		
		$pdf->SetFont($doc_style['docstyle_data_font'], '', $font_size_data);
		
		if (!$display_mode) {
			$pdf->Cell($cell_width_currency, $data_cell_height, F_FormatCurrency($total_amount_out), 1, 0, 'R', 0);
			
			if ($display_option[8]) {
				$pdf->Cell($cell_width_currency, $data_cell_height, F_FormatCurrency($total_net_out), 1, 0, 'R', 0);
			}
			
			if ($display_option[9]) {
				$pdf->Cell($cell_width_currency, $data_cell_height, F_FormatCurrency($total_tax_out), 1, 0, 'R', 0);
			}
		}
		else {
			$pdf->Cell($cell_width_currency * 3, $data_cell_height, "", 0, 0, 'R', 0);
		}
		
		$pdf->Cell($cell_width_currency, $data_cell_height, F_FormatCurrency($paid_amount_out), 1, 0, 'R', 0);
		
		if ($display_option[8]) {
			$pdf->Cell($cell_width_currency, $data_cell_height, F_FormatCurrency($paid_net_out), 1, 0, 'R', 0);
		}
		
		if ($display_option[9]) {
			$pdf->Cell($cell_width_currency, $data_cell_height, F_FormatCurrency($paid_tax_out), 1, 0, 'R', 0);
		}
		
		if ( (!$display_mode) AND ($display_option[7]) ) {
			
			$pdf->Cell($cell_width_currency, $data_cell_height, F_FormatCurrency($total_amount_out - $paid_amount_out), 1, 0, 'R', 0);
			
			if ($display_option[8]) {
				$pdf->Cell($cell_width_currency, $data_cell_height, F_FormatCurrency($total_net_out - $paid_net_out), 1, 0, 'R', 0);
			}
			
			if ($display_option[9]) {
				$pdf->Cell($cell_width_currency, $data_cell_height, F_FormatCurrency($total_tax_out - $paid_tax_out), 1, 0, 'R', 0);
			}
		}
		
		//difference (in - out)
		$pdf->Ln();
		
		$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $font_size_data);
		$pdf->Cell($cell_width_left_total, $data_cell_height, $l['w_difference'], 0, 0, 'R', 0);
		
		$pdf->SetFont($doc_style['docstyle_data_font'], '', $font_size_data);
		if (!$display_mode) {
			$pdf->Cell($cell_width_currency, $data_cell_height, F_FormatCurrency($total_amount_in + $total_amount_out), 1, 0, 'R', 0);
			
			if ($display_option[8]) {
				$pdf->Cell($cell_width_currency, $data_cell_height, F_FormatCurrency($total_net_in + $total_net_out), 1, 0, 'R', 0);
			}
			
			if ($display_option[9]) {
				$pdf->Cell($cell_width_currency, $data_cell_height, F_FormatCurrency($total_tax_in + $total_tax_out), 1, 0, 'R', 0);
			}
		}
		else {
			$pdf->Cell($cell_width_currency * 3, $data_cell_height, "", 0, 0, 'R', 0);
		}
		
		$pdf->Cell($cell_width_currency, $data_cell_height, F_FormatCurrency($paid_amount_in + $paid_amount_out), 1, 0, 'R', 0);
		
		if ($display_option[8]) {
			$pdf->Cell($cell_width_currency, $data_cell_height, F_FormatCurrency($paid_net_in + $paid_net_out), 1, 0, 'R', 0);
		}
		
		if ($display_option[9]) {
			$pdf->Cell($cell_width_currency, $data_cell_height, F_FormatCurrency($paid_tax_in + $paid_tax_out), 1, 0, 'R', 0);
		}

	// END DISPLAY TRANSACTION DATA =======================

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