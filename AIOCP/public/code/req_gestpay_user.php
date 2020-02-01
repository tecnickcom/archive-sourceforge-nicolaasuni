<?php
//============================================================+
// File name   : req_gestpay_user.php                          
// Begin       : 2002-08-31                                    
// Last Update : 2003-01-26                                    
//                                                             
// Description : Payment module                                
//               GestPay from Banca Sella (www.sellanet.it)    
//               Page where user will be redirect after        
//               payment                                       
//                                                             
//                                                             
// Note: all parameters must be strings.                       
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
require_once('../../shared/code/cp_functions_ec_payment.'.CP_EXT);
require_once('../../shared/payment/inc_gestpay_receive.'.CP_EXT);

if (($mytransactionresult != "OK") OR ($myerrorcode != "0")) { //check for error
	// redirect user to error page
	F_display_error_page($myerrorcode." - ".$myerrordescription);
}
else {
	// redirect user to order page
	F_display_user_order($user_id, $myerrordescription);
}

exit;

//============================================================+
// END OF FILE                                                 
//============================================================+
?>