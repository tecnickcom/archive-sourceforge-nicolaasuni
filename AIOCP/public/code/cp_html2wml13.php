<?php
//============================================================+
// File name   : cp_html2wml13.php                        
// Begin       : 2003-04-15                                    
// Last Update : 2003-04-16                                    
//                                                             
// Description : convert html to WML 1.3 (transcoding)            
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

require_once('../../shared/config/cp_extension.inc');
require_once('../../shared/code/cp_functions_get_page.'.CP_EXT);
require_once('../../shared/code/cp_functions_xhtml_validator.'.CP_EXT);
require_once('../../shared/code/cp_functions_wml13.'.CP_EXT);
	
if (isset($_REQUEST['hpage'])) {
	$hpage = urldecode($_REQUEST['hpage']);
	$pagecode = F_get_page_content($hpage);
	$charset = false;
	$pagecode = F_validate_xhtml($pagecode, $charset, false); //get well-formed xhtml code
	
	//convert page to wml 1.3
	$wmlpage = F_html_to_wml13($hpage, $pagecode, $charset);
		
	//send headers
	header('Cache-Control: public', TRUE); 
	header("Last-Modified: ".gmdate('D, d M Y H:i:s T', time())."", TRUE); 
	header('Content-Transfer-Encoding: base64', TRUE); 
	header("Accept-Ranges: bytes", TRUE); 
	header("Content-Length: ".strlen($wmlpage)."", TRUE); 
	header("Content-Type: text/vnd.wap.wml;", TRUE);
	
	//display page content
	echo $wmlpage;
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
