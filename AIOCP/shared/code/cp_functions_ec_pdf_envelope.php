<?php
//============================================================+
// File name   : cp_functions_ec_pdf_envelope.php              
// Begin       : 2002-08-17                                    
// Last Update : 2004-07-13                                    
//                                                             
// Description : Create PDF postal envelope                    
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
// generate a pdf document
// ------------------------------------------------------------
function F_generate_pdf_envelope($user_id, $force_user, $not_default) {
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
	
	//get document style
	$sql = "SELECT * FROM ".K_TABLE_EC_DOCUMENTS_STYLES." WHERE docstyle_name='_envelope' LIMIT 1";
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
	
	//get company data
	$company_data = F_get_company_data();
	
	//create new PDF document (document units are set by default to millimeters)
	$pdf = new EC_DOC_PDF($doc_style['docstyle_orientation'], 'mm', $page_format); 
	
	// set document informations
	$pdf->SetCreator("AIOCP - All In One Control Panel ver.".K_AIOCP_VERSION."");
	$pdf->SetAuthor($company_data['name']);
	$pdf->SetTitle($lang['w_envelope']);
	$pdf->SetSubject($lang['w_envelope']);
	$pdf->SetKeywords($lang['w_envelope']);
	
	//print header but not footer
	$pdf->print_header = true;	
	$pdf->print_footer = false;	
	
	//set margins
	$pdf->SetMargins($doc_style['docstyle_margin_left'], $doc_style['docstyle_margin_top'], $doc_style['docstyle_margin_right']);
	//set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, $doc_style['docstyle_margin_bottom']);
	
	$pdf->SetHeaderMargin($doc_style['docstyle_header']);
	
	$pdf->SetBarcode('');
	
	$pdf->header_font = Array($doc_style['docstyle_data_font'], '', $doc_style['docstyle_data_font_size']);
	
	$pdf->lang_templates = $lang;
	$pdf->company_data = $company_data;
	
	//initialize document
	$pdf->Open();
	
	//calculate some sizes
	$page_width = $pdf->w - $doc_style['docstyle_margin_left'] - $doc_style['docstyle_margin_right'];
	$address_X = round($pdf->w / 2, 2);
	$pdf->header_width = round(($page_width * 0.50) - $company_data['logowidth'], 2); //header width (leave space for postal marks)
	
	$data_cell_height = round((K_CELL_HEIGHT_RATIO * $doc_style['docstyle_data_font_size']) / $pdf->getScaleFactor(), 2);
	$main_cell_height = round((K_CELL_HEIGHT_RATIO * $doc_style['docstyle_main_font_size']) / $pdf->getScaleFactor(), 2);
	
	//add page
	$pdf->AddPage();
	
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetLineWidth(0.1);
	$pdf->SetDrawColor(0, 0, 0);
	
	//START get and print customer data -------------------------
	
	//search if user is a company
	$sql_company = "SELECT * FROM ".K_TABLE_USERS_COMPANY." WHERE company_userid=".$user_id." LIMIT 1";
	if($r_company = F_aiocpdb_query($sql_company, $db)) {
		if(($customer = F_aiocpdb_fetch_array($r_company)) AND (!$force_user) ){ //user is a company
			
			//address
			if (!$not_default) {
				$sql_address = "SELECT * FROM ".K_TABLE_USERS_ADDRESS." WHERE address_id=".$customer['company_billing_address_id']." LIMIT 1";
			}
			else {
				$sql_address = "SELECT * FROM ".K_TABLE_USERS_ADDRESS." WHERE address_id=".$customer['company_legal_address_id']." LIMIT 1";
			}
			if($r_address = F_aiocpdb_query($sql_address, $db)) {
				if($address = F_aiocpdb_fetch_array($r_address)) {
					$pdf->SetX($address_X);
					$pdf->SetFont($doc_style['docstyle_main_font'], 'B', $doc_style['docstyle_main_font_size']);
					$pdf->MultiCell(0, $main_cell_height, $customer['company_name'], 0, 'L', 0);
					$pdf->SetFont($doc_style['docstyle_main_font'], '', $doc_style['docstyle_main_font_size']);
					$pdf->SetX($address_X);
					$pdf->MultiCell(0, $main_cell_height, $address['address_address']."\n".$address['address_postcode']." ".$address['address_city']." (".$address['address_state'].")\n".F_GetCountryName($address['address_countryid']), 0, 'L', 0);
				}
			}
			else {
				F_display_db_error();
			}
		}
		else { //user is not a company
			$sql_user = "SELECT * FROM ".K_TABLE_USERS." WHERE user_id=".$user_id." LIMIT 1";
			if($r_user = F_aiocpdb_query($sql_user, $db)) {
				if($customer = F_aiocpdb_fetch_array($r_user)) {
					
					if (!$not_default) {
						$sql_address = "SELECT * FROM ".K_TABLE_USERS_ADDRESS." WHERE address_userid=".$user_id." AND address_default=1 LIMIT 1";
					}
					else {
						$sql_address = "SELECT * FROM ".K_TABLE_USERS_ADDRESS." WHERE address_id =".$not_default." LIMIT 1";
					}
					if($r_address = F_aiocpdb_query($sql_address, $db)) {
						if($address = F_aiocpdb_fetch_array($r_address)) {
							$pdf->SetFont($doc_style['docstyle_main_font'], 'B', $doc_style['docstyle_main_font_size']);
							$pdf->SetX($address_X);
							$pdf->MultiCell(0, $main_cell_height, $customer['user_firstname']." ".$customer['user_lastname'], 0, 'L', 0);
							$pdf->SetFont($doc_style['docstyle_main_font'], '', $doc_style['docstyle_main_font_size']);
							$pdf->SetX($address_X);
							$pdf->MultiCell(0, $main_cell_height, $address['address_address']."\n".$address['address_postcode']." ".$address['address_city']." ".$address['address_state']."\n".F_GetCountryName($address['address_countryid']), 0, 'L', 0);
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
		}
	}
	else {
		F_display_db_error();
	}
	
	//END get and print customer data -------------------------
	
	//Close and output PDF document
	$pdf->Output();
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>