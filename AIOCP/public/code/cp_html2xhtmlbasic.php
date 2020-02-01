<?php
//============================================================+
// File name   : cp_html2xhtmlbasic.php                        
// Begin       : 2002-06-02                                    
// Last Update : 2003-04-07                                    
//                                                             
// Description : convert html to text (transcoding)            
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
require_once('../../shared/code/cp_functions_xhtml_basic.'.CP_EXT);

if (isset($_REQUEST['page'])) {
	$page = urldecode($_REQUEST['page']);
	$pagecode = F_get_page_content($page);
	$charset = false;
	$pagecode = F_validate_xhtml($pagecode, $charset, false); //get well-formed xhtml code
	
	echo F_html_to_xhtml_basic($page, $pagecode, $charset);
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
