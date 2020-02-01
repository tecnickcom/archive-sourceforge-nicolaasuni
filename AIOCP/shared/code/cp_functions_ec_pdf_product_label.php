<?php
//============================================================+
// File name   : cp_functions_ec_pdf_product_label.php
// Begin       : 2002-08-12
// Last Update : 2008-06-11
// 
// Description : Create PDF product label
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
function F_pdf_product_label($wherequery) {
	global $l, $db, $selected_language;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/config/tcpdf_config.'.CP_EXT);
	require_once('../../shared/pdf/cp_ec_pdf_doc.'.CP_EXT);
	
	$doc_language = $selected_language;
	if (K_DOCUMENTS_LANGUAGE) {
		$doc_language = "".K_DOCUMENTS_LANGUAGE."";
	}
	
	//get language template table
	require_once('../../shared/code/cp_functions_doc_language.'.CP_EXT);
	$lang = F_get_pdf_language_templates($doc_language);
	
	//get document style
	$sql = "SELECT * FROM ".K_TABLE_EC_DOCUMENTS_STYLES." WHERE docstyle_name='_product_label' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if(!$doc_style = F_aiocpdb_fetch_array($r)) {
			return FALSE;
		}
	}
	else {
		F_display_db_error();
	}
	
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
	$pdf->SetAuthor("AIOCP");
	$pdf->SetTitle($lang['w_product_label']);
	$pdf->SetSubject($lang['w_product_label']);
	$pdf->SetKeywords($lang['w_product_label']);
	
	//do not print header and footer
	$pdf->print_header = false;	
	$pdf->print_footer = false;	
	
	//set margins
	$pdf->SetMargins($doc_style['docstyle_margin_left'], $doc_style['docstyle_margin_top'], $doc_style['docstyle_margin_right']);
	//set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, $doc_style['docstyle_margin_bottom']);
	
	$pdf->SetHeaderMargin($doc_style['docstyle_header']);
	$pdf->SetFooterMargin($doc_style['docstyle_footer']);
	
	$pdf->SetBarcode('');
	
	//calculate some sizes
	$page_width = $pdf->w - $doc_style['docstyle_margin_left'] - $doc_style['docstyle_margin_right'];
	$data_cell_height = round((K_CELL_HEIGHT_RATIO * $doc_style['docstyle_data_font_size']) / $pdf->getScaleFactor(), 2);
	$main_cell_height = round((K_CELL_HEIGHT_RATIO * $doc_style['docstyle_main_font_size']) / $pdf->getScaleFactor(), 2);
	
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetLineWidth(0.1);
	$pdf->SetDrawColor(0, 0, 0);
	
	//initialize document
	$pdf->Open();
	
	//get product details
	$sqlp = "SELECT * FROM ".K_TABLE_EC_PRODUCTS." ".$wherequery."";
	if($rp = F_aiocpdb_query($sqlp, $db)) {
		while($product = F_aiocpdb_fetch_array($rp)) {
			
			$pdf->AddPage();
			
			// START product label ---------------------
			$pdf->SetFont($doc_style['docstyle_main_font'], '', $doc_style['docstyle_main_font_size']);
			$pdf->MultiCell(0, $main_cell_height, $product['product_name'], 0, 'C', 0);
			
			$pdf->SetFont($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
			$pdf->MultiCell(0, $data_cell_height, $product['product_code'], 0, 'C', 0);
			
			$xres = 0.3;
			$barstyle = array(
				"position" => "S",
				"border" => true,
				"padding" => 1,
				"fgcolor" => array(0,0,0),
				"bgcolor" => false,
				"text" => true,
				"font" => "",
				"fontsize" => 8,
				"stretchtext" => 4
			);
			$barcode_height = '';
			$pdf->write1DBarcode($product['product_barcode'], K_BARCODE_TYPE, $doc_style['docstyle_margin_left'], $pdf->GetY(), $page_width, $barcode_height, $xres, $barstyle, '');
			// END product label ---------------------
		}
	}
	else {
		F_display_db_error();
	}
	
	//Close and output PDF document
	$pdf->Output();
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>