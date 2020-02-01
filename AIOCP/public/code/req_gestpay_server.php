<?php
//============================================================+
// File name   : req_gestpay_server.php                        
// Begin       : 2002-08-31                                    
// Last Update : 2004-08-20                                    
//                                                             
// Description : Payment module                                
//               GestPay from Banca Sella (www.sellanet.it)    
//               Receive payment answer from bank server       
//               (Server 2 Server comunication)                
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

if ($mytransactionresult != "OK") { //check for error
	//update shopping cart payment details
	F_update_payment_details($user_id, $transaction_id, "ERROR ".$myerrorcode." - ".$myerrordescription);
	
	//log error
	$logsttring = "\nGestPay";
	$logsttring .= "\t".gmdate("Y-m-d H:i:s")."";
	$logsttring .= "\t".$user_id."";
	$logsttring .= "\t".$transaction_id."";
	$logsttring .= "\t".$myerrorcode."";
	$logsttring .= "\t".$myerrordescription."";
	error_log($logsttring, 3, "../log/cp_payment_errors.log");
}
else {
	//update shopping cart payment details
	F_update_payment_details($user_id, $transaction_id, $myerrordescription." (".$myerrorbanktransactionid.")");
	
	//generate order and send order by email to customer
	require_once('../../shared/code/cp_functions_ec_order.'.CP_EXT);
	F_create_new_ec_order($user_id, $transaction_id, false, true, true);
}

exit;

//============================================================+
// END OF FILE                                                 
//============================================================+
?>