<?php
//============================================================+
// File name   : cp_ec_order.php                               
// Begin       : 2002-08-29                                    
// Last Update : 2003-01-26                                    
//                                                             
// Description : Create order from shopping cart               
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

$pagelevel = K_AUTH_ADMIN_CP_EC_ORDER;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_order'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages; ?>
<!-- ====================================================== -->
<?php
if (isset($_REQUEST['uid']) AND isset($_REQUEST['tid']) AND isset($_REQUEST['vc'])) {
	$verifycode = F_generate_verification_code($_REQUEST['uid'], 4);
	if ($_REQUEST['vc'] == $verifycode) {
		require_once('../../shared/code/cp_functions_ec_order.'.CP_EXT);
		F_create_new_ec_order($_REQUEST['uid'], $_REQUEST['tid'], true, true, false);
	}
	else {
		F_print_error("ERROR", $l['m_unauthorized_access']);
	}
}
else {
	F_print_error("ERROR", $l['m_unauthorized_access']);
}
?>
<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
