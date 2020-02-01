<?php
//============================================================+
// File name   : cp_ec_pdf_doc.php                             
// Begin       : 2002-07-27                                    
// Last Update : 2004-07-15                                    
//                                                             
// Description : FPDF class extension                          
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

require_once('../../shared/pdf/tcpdf.php');

/**
 * AIOCP FPDF class extension for commercial documents
 * @name EC_DOC_PDF
 * @package FPDF
 * @version 1.0
 * @author Nicola Asuni
*/
class EC_DOC_PDF extends TCPDF {
	
	/**
	 * company data
	 */
	var $company_data;
	
	/**
	 * Class constructor.
	 */
	function EC_DOC_PDF ($orientation='P', $unit='mm', $format='A4', $unicode=false, $encoding="ISO-8859-1") {
		//Call parent constructor
		$this->TCPDF($orientation, $unit, $format, $unicode, $encoding);
	}
	
	/**
 	 * This method is used to render the page header
	 */
	function Header() {
		$header_string = "";
		
		if ($this->company_data['fiscalcode']) {
			 $header_string .= $this->lang_templates['w_fiscalcode'].": ".$this->company_data['fiscalcode']."";
		}
		
		if ($this->company_data['address']) {
			 $header_string .= " - ".$this->company_data['address'];
		}
		if ($this->company_data['postcode']) {
			 $header_string .= " - ".$this->company_data['postcode'];
		}
		if ($this->company_data['city']) {
			 $header_string .= " - ".$this->company_data['city'];
		}
		if ($this->company_data['state']) {
			 $header_string .= " (".$this->company_data['state'].")";
		}
		if ($this->company_data['country']) {
			 $header_string .= " - ".strtoupper($this->company_data['country']);
		}
		
		if ($this->company_data['telephone']) {
			 $header_string .= " - ".$this->lang_templates['w_telephone_abbr']." ".$this->company_data['telephone'];
		}
		if ($this->company_data['fax']) {
			 $header_string .= " - ".$this->lang_templates['w_fax']." ".$this->company_data['fax'];
		}
		
		if ($this->company_data['url']) {
			 $header_string .= " - ".$this->company_data['url'];
		}
		if ($this->company_data['email']) {
			 $header_string .= " - ".$this->company_data['email'];
		}
		
		if (empty($this->company_data['logo'])) {
			$companylogo = K_BLANK_IMAGE;
		} else {
			$companylogo = "company/".$this->company_data['logo'];
		}
		
		// set default header data
		$this->SetHeaderData($companylogo, $this->company_data['logowidth'], $this->company_data['name'], $header_string);
		parent::Header();
	}
	
} // END OF CLASS

//============================================================+
// END OF FILE                                                 
//============================================================+
?>