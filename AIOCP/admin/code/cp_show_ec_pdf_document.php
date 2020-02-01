<?php
//============================================================+
// File name   : cp_show_ec_pdf_document.php                   
// Begin       : 2002-07-24                                    
// Last Update : 2003-10-21                                    
//                                                             
// Description : Display a PDF version of commercial document  
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

require_once('../../shared/config/cp_extension.inc');
require_once('../config/cp_config.'.CP_EXT);

if(isset($_REQUEST['ecdoc_id']) AND isset($_REQUEST['vc'])) {
	$verifycode = F_generate_verification_code($_REQUEST['ecdoc_id'], 4);
	if ($verifycode == $_REQUEST['vc']) { //verify request validity
		require_once('../../shared/code/cp_functions_ec_pdf_documents.'.CP_EXT);
		F_generate_pdf_document($_REQUEST['ecdoc_id'], false);
		exit();
	}
}
echo "<h1>".$l['m_unauthorized_access']."</h1>";

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
