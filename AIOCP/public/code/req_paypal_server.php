<?php
//============================================================+
// File name   : req_paypal_server.php                         
// Begin       : 2002-09-18                                    
// Last Update : 2007-01-09                                    
//                                                             
// Description : Payment module                                
//               PayPal(www.paypal.com)                        
//               Receive payment answer from PayPal server     
//               (Server 2 Server comunication)                
//                                                             
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
//============================================================+

require_once('../../shared/config/cp_extension.inc');
require_once('../../shared/code/cp_functions_ec_payment.'.CP_EXT);

list($user_id, $selected_language, $transaction_id) = split('[-]', $_REQUEST['cm']);
$payment_status = $_REQUEST['st'];

if (strcasecmp($payment_status, "Completed") == 0) {
	//update shopping cart payment details
	F_update_payment_details($user_id, $transaction_id, "Payment: ".$payment_status);
	//generate order and send order by email to customer
	require_once('../../shared/code/cp_functions_ec_order.'.CP_EXT);
	F_create_new_ec_order($user_id, $transaction_id, false, true, true);
	// redirect user to order page
	F_display_user_order($user_id, "Payment: ".$payment_status);
}
else { //invalid
	//update shopping cart payment details
	F_update_payment_details($user_id, $transaction_id, "ERROR: Payment ".$payment_status);
	//log error
	$logsttring = "\nPayPal";
	$logsttring .= "\t".$_POST['txn_id']."";
	$logsttring .= "\t".gmdate("Y-m-d H:i:s")."";
	$logsttring .= "\t".$user_id."";
	$logsttring .= "\t".$transaction_id."";
	$logsttring .= "\t".$payment_status."";
	if (isset($_REQUEST['pr'])) {
		$logsttring .= "\t".$_REQUEST['pr']."";
	}
	error_log($logsttring, 3, "../log/cp_payment_errors.log");
}
exit;

//============================================================+
// END OF FILE                                                 
//============================================================+
?>