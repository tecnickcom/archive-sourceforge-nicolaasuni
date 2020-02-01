<?php
//============================================================+
// File name   : cp_show_ec_pdf_transactions.php               
// Begin       : 2002-09-08                                    
// Last Update : 2003-10-21                                    
//                                                             
// Description : Display a PDF transactions document           
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

if(isset($_REQUEST['tsql']) AND isset($_REQUEST['dt'])  AND isset($_REQUEST['tf']) AND isset($_REQUEST['do']) AND isset($_REQUEST['vc'])) {
	$_REQUEST['tsql'] = stripslashes($_REQUEST['tsql']);
	$verifycode = F_generate_verification_code($_REQUEST['tsql'], 4);
	if ($verifycode == $_REQUEST['vc']) { // verify request validity
		require_once('../code/cp_functions_ec_pdf_transactions.'.CP_EXT);
		F_generate_pdf_transactions($_REQUEST['tsql'], $_REQUEST['dt'], $_REQUEST['do'], $_REQUEST['tf'], false);
		exit();
	}
}
echo "<h1>".$l['m_unauthorized_access']."</h1>";

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
