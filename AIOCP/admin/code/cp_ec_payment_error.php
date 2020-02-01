<?php
//============================================================+
// File name   : cp_ec_payment_error.php                       
// Begin       : 2002-08-31                                    
// Last Update : 2003-10-21                                    
//                                                             
// Description : Display payment error                         
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

$pagelevel = K_AUTH_ADMIN_CP_EC_PAYMENT_ERROR;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_payment_error']; // page title

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages; ?>
<!-- ====================================================== -->
<?php
if (isset($_REQUEST['err'])) {
	echo "".$l['w_error'].": ".$_REQUEST['err']."";
}
else {
	echo "".$l['w_error'].": ".$l['w_unknow_error']."";
}
?>
<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
