<?php
//============================================================+
// File name   : cp_functions_ec_pdf_product_report.php
// Begin       : 2002-08-12
// Last Update : 2008-06-11
// 
// Description : Create PDF document of product's data
// 
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
// generate a pdf document
// ------------------------------------------------------------
function F_pdf_product_report($product_id, $user_id=1) {
	global $l, $db, $selected_language;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_company_data.'.CP_EXT);
	require_once('../../shared/code/cp_functions_html2txt.'.CP_EXT);
	require_once('../../shared/pdf/cp_ec_pdf_doc.'.CP_EXT);
	require_once('../../shared/code/cp_functions_ec_vat.'.CP_EXT);
	
	$doc_language = $selected_language;
	if (K_DOCUMENTS_LANGUAGE) {
		$doc_language = "".K_DOCUMENTS_LANGUAGE."";
	}
	
	//get language template table
	require_once('../../shared/code/cp_functions_doc_language.'.CP_EXT);
	$lang = F_get_pdf_language_templates($doc_language);
	
	//get document style
	$sql = "SELECT * FROM ".K_TABLE_EC_DOCUMENTS_STYLES." WHERE docstyle_name='_product_report' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if(!$doc_style = F_aiocpdb_fetch_array($r)) {
			return FALSE;
		}
	}
	else {
		F_display_db_error();
	}
	
	//get product details
	$sqlp = "SELECT * FROM ".K_TABLE_EC_PRODUCTS." WHERE product_id=".$product_id." LIMIT 1";
	if($rp = F_aiocpdb_query($sqlp, $db)) {
		if(!$product = F_aiocpdb_fetch_array($rp)) {
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
	$pdf->SetTitle($product['product_name']);
	$pdf->SetSubject($lang['w_product_report'].": ".$product['product_code']." - ".$product['product_name']);
	$pdf->SetKeywords($product['product_name']." ".$product['product_code']);
	
	//set margins
	$pdf->SetMargins($doc_style['docstyle_margin_left'], $doc_style['docstyle_margin_top'], $doc_style['docstyle_margin_right']);
	//set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, $doc_style['docstyle_margin_bottom']);
	
	$pdf->SetHeaderMargin($doc_style['docstyle_header']);
	$pdf->SetFooterMargin($doc_style['docstyle_footer']);
	
	$pdf->SetBarcode('');
	
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
	$data_cell_height = round((K_CELL_HEIGHT_RATIO * $doc_style['docstyle_data_font_size']) / $pdf->getScaleFactor(), 2);
	$main_cell_height = round((K_CELL_HEIGHT_RATIO * $doc_style['docstyle_main_font_size']) / $pdf->getScaleFactor(), 2);
	
	$pdf->SetFillColor(204, 204, 204);
	$pdf->SetLineWidth(0.1);
	$pdf->SetDrawColor(0, 0, 0);
	
	//print document name (title)
	$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
	$pdf->Cell(0, $main_cell_height, $lang['w_product_report'], 1, 1, 'C', 1);
	
	$pdf->Ln();
	
	// START products description ---------------------
	
	$product_description = unserialize($product['product_description']);
	$product_description = stripslashes($product_description[$doc_language]);
	
	//print product image
	$desc_x = $doc_style['docstyle_margin_left'] + ($doc_style['docstyle_image_width'] * 1.05);
	$desc_width = ($page_width - $doc_style['docstyle_image_width']) / 3;
	$img_x = $doc_style['docstyle_margin_left'];
	$img_y = $pdf->GetY();
	if (($product['product_image']) AND ($product['product_image'] != K_BLANK_IMAGE)) {
		$pdf->Image(K_PATH_IMAGES_PRODUCTS.$product['product_image'], $img_x, $img_y, $doc_style['docstyle_image_width']);
	}
	else {
		$pdf->img_rb_y = $pdf->GetY();
	}
	$img_pos = $pdf->img_rb_y; //record image bottom coordinate
	
	$pdf->SetX($desc_x);
	$pdf->SetFont($doc_style['docstyle_main_font'], 'B', $doc_style['docstyle_main_font_size'] * K_TITLE_MAGNIFICATION);
	$pdf->MultiCell(0, $main_cell_height * K_TITLE_MAGNIFICATION, $product['product_code']." - ".$product['product_name'], 0, 'L', 0);

	$pdf->SetX($desc_x);
	$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
	$pdf->Cell(0, $data_cell_height, '', 0, 1, 'C', 0); //vertical space (void line)
	
	//print barcode
	if ($product['product_barcode']) {
		$barcode_height = min(20, $img_pos - $pdf->GetY());
		$pdf->SetX($desc_x);
		$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
		$pdf->Cell($desc_width, $data_cell_height, $lang['w_barcode'].": ", 0, 0, 'R', 0);
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
		$pdf->write1DBarcode($product['product_barcode'], K_BARCODE_TYPE, $desc_x + $desc_width, $pdf->GetY(), $page_width - $pdf->GetX(), $barcode_height, $xres, $barstyle, 'N');
	}
	
	$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
	
	$pdf->Ln();
	
	
	if ($product['product_manufacturer_code']) {
		$pdf->SetX($desc_x);
		$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
		$pdf->Cell($desc_width, $data_cell_height, $lang['w_manufacturer_code'].": ", 0, 0, 'R', 0);
		$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
		$pdf->MultiCell(0, $data_cell_height, $product['product_manufacturer_code'], 0, 'L', 0);
	}
	
	if ($product['product_inventory_code']) {
		$pdf->SetX($desc_x);
		$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
		$pdf->Cell($desc_width, $data_cell_height, $lang['w_inventory_code'].": ", 0, 0, 'R', 0);
		$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
		$pdf->MultiCell(0, $data_cell_height, $product['product_inventory_code'], 0, 'L', 0);
	}
	
	if ($product['product_alternative_codes']) {
		$pdf->SetX($desc_x);
		$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
		$pdf->Cell($desc_width, $data_cell_height, $lang['w_alternative_codes'].": ", 0, 0, 'R', 0);
		$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
		$alternative_codes = str_replace("\n", " | ",$product['product_alternative_codes']);
		$pdf->MultiCell(0, $data_cell_height, $alternative_codes, 0, 'L', 0);
	}
	
	if ($product['product_manufacturer_id']) {
		$pdf->SetX($desc_x);
		$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
		$pdf->Cell($desc_width, $data_cell_height, $lang['w_manufacturer'].": ", 0, 0, 'R', 0);
		$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
		
		$sqlmf = "SELECT * FROM ".K_TABLE_EC_MANUFACTURERS." WHERE manuf_id='".$product['product_manufacturer_id']."' LIMIT 1";
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
	
	if ($product['product_warranty']) {
		$pdf->SetX($desc_x);
		$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
		$pdf->Cell($desc_width, $data_cell_height, $lang['w_warranty'].": ", 0, 0, 'R', 0);
		$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
		$pdf->MultiCell(0, $data_cell_height, $product['product_warranty']." ".$lang['w_months'], 0, 'L', 0);
	}
	
	if ($product['product_unit_of_measure_id']) {
		//get unit of measure name
		$sqlu = "SELECT * FROM ".K_TABLE_EC_UNITS_OF_MEASURE." WHERE unit_id=".$product['product_unit_of_measure_id']." LIMIT 1";
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
	
	if ($product['product_transportable']) {
		if ($product['product_length'] AND $product['product_width'] AND $product['product_height']) {
			$pdf->SetX($desc_x);
			$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
			$pdf->Cell($desc_width, $data_cell_height, $lang['w_packaged_measures'].": ", 0, 0, 'R', 0);
			$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
			
			$tempstr = "".$product['product_length']."m x ".$product['product_width']."m x ".$product['product_height']."m =";
			$tempstr .= " ".($product['product_length']*$product['product_width']*$product['product_height'])."m³";
			$pdf->MultiCell(0, $data_cell_height, $tempstr, 0, 'L', 0);
		}
		
		if ($product['product_weight_per_unit']) {
			$pdf->SetX($desc_x);
			$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
			$pdf->Cell($desc_width, $data_cell_height, $lang['w_packaged_weight'].": ", 0, 0, 'R', 0);
			$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
			$pdf->MultiCell(0, $data_cell_height, $product['product_weight_per_unit']." Kg", 0, 'L', 0);
		}
	}
	
	if ($product['product_cost']) {
		$pdf->SetX($desc_x);
		$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
		$pdf->Cell($desc_width, $data_cell_height, $lang['w_cost_per_unit'].": ", 0, 0, 'R', 0);
		$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
		$pdf->MultiCell(0, $data_cell_height, K_MONEY_CURRENCY_SYMBOL." ".$product['product_cost']." +".$lang['w_ec_tax'], 0, 'L', 0);
	}
	
	//print VAT tax only if user is logged
	if (($product['product_tax']>1) AND ($user_id>1)) {
		$vat = F_get_vat_value($product['product_tax'], $user_id);
		$pdf->SetX($desc_x);
		$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
		$pdf->Cell($desc_width, $data_cell_height, $lang['w_ec_tax'].": ", 0, 0, 'R', 0);
		$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
		$tax_amount = $product['product_cost'] * ($vat / 100);
		$pdf->MultiCell(0, $data_cell_height, $vat."%  (".K_MONEY_CURRENCY_SYMBOL. " ".F_FormatCurrency($tax_amount).")", 0, 'L', 0);
	}
	else {
		$tax_amount = 0;
	}
	
	if ((K_EC_DISPLAY_TAX_2) AND ($product['product_tax2']>1) AND ($user_id>1)) {
		$vat2 = F_get_vat_value($product['product_tax2'], $user_id);
		$tax_amount2 = $product['product_cost'] * ($vat2 / 100);
		$pdf->SetX($desc_x);
		$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
		$pdf->Cell($desc_width, $data_cell_height, $lang['w_ec_tax2'].": ", 0, 0, 'R', 0);
		$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
		$pdf->MultiCell(0, $data_cell_height, $vat."%  (".K_MONEY_CURRENCY_SYMBOL. " ".F_FormatCurrency($tax_amount2).")", 0, 'L', 0);
	}
	else {
		$tax_amount2 = 0;
	}
	
	if ((K_EC_DISPLAY_TAX_3) AND ($product['product_tax3']>1) AND ($user_id>1)) {
		$vat3 = F_get_vat_value($product['product_tax3'], $user_id);
		$tax_amount3 = ($product['product_cost'] + $tax_amount + $tax_amount2) * ($vat3 / 100);
		$pdf->SetX($desc_x);
		$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
		$pdf->Cell($desc_width, $data_cell_height, $lang['w_ec_tax3'].": ", 0, 0, 'R', 0);
		$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
		$pdf->MultiCell(0, $data_cell_height, $vat."%  (".K_MONEY_CURRENCY_SYMBOL. " ".F_FormatCurrency($tax_amount3).")", 0, 'L', 0);
	}
	else {
		$tax_amount3 = 0;
	}
	
	
	$pdf->SetY(max($img_pos, $pdf->GetY()));
	
	if ($product_description) {
		$pdf->Ln();
		$pdf->SetFont($doc_style['docstyle_main_font'], '', $doc_style['docstyle_main_font_size']);
		//$pdf->MultiCell(0, $data_cell_height, F_html_to_text($product_description, false, false), 0, 'L', 0);
		$pdf->WriteHTML($product_description, true);
	}
	// END product description ---------------------
	
	//display product warranty text
	if ($product['product_warranty_id']) {
		$sqlw = "SELECT * FROM ".K_TABLE_EC_WARRANTIES." WHERE warranty_id=".$product['product_warranty_id']." LIMIT 1";
		if($rw = F_aiocpdb_query($sqlw, $db)) {
			if($mw = F_aiocpdb_fetch_array($rw)) {
				$warranty_description = unserialize($mw['warranty_description']);
				$warranty_description = stripslashes($warranty_description[$doc_language]);
				
				$pdf->AddPage();
				
				//print document name (title)
				$pdf->SetFont($doc_style['docstyle_data_font'], 'B', $doc_style['docstyle_data_font_size']);
				$pdf->Cell(0, $data_cell_height, $lang['w_warranty'], 1, 1, 'C', 1);
				
				$pdf->Ln();
				
				$pdf->SetFont($doc_style['docstyle_main_font'], '', $doc_style['docstyle_main_font_size']);
				//$pdf->MultiCell(0, $data_cell_height, F_html_to_text($warranty_description, false, false), 0, 'L', 0);
				$pdf->WriteHTML($warranty_description, true);
			}
		}
		else {
			F_display_db_error();
		}
	}
	
	
	//Close and output PDF document
	$pdf->Output();
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>