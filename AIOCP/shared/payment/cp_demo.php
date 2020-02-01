<?php
//============================================================+
// File name   : cp_demo.php                                   
// Begin       : 2002-08-26                                    
// Last Update : 2002-09-29                                    
//                                                             
// Description : Demo payment module                           
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

//output variables
$payment_details = "";

if (empty($user_id)) {return FALSE;}

//update data
require_once('../../shared/code/cp_functions_ec_payment.'.CP_EXT);
F_update_payment_details($user_id, $transaction_id, $payment_details); //update payment details on shopping cart data
F_generate_order($user_id, $transaction_id); // generate order from shopping cart

exit;

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
