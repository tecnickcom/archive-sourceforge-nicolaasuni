<?php
//============================================================+
// File name   : inc_gestpay_receive.php                       
// Begin       : 2002-08-31                                    
// Last Update : 2003-01-10                                    
//                                                             
// Description : Payment module                                
//               GestPay from Banca Sella (www.sellanet.it)    
//               Receive and decode bank parameters            
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
require_once('../../shared/payment/inc_gestpay_crypt.'.CP_EXT);

if (!(isset($_REQUEST['a']) AND isset($_REQUEST['b']))) {
	exit();
}

$parameter_a = trim($_REQUEST['a']);
$parameter_b = trim($_REQUEST['b']);

$objdeCrypt = new GestPayCrypt;
//$objdeCrypt = new GestPayCryptHS; // HTTPS version

$objdeCrypt->SetShopLogin($parameter_a);
$objdeCrypt->SetEncryptedString($parameter_b);
$objdeCrypt->Decrypt();

$myshoplogin = trim($objdeCrypt->GetShopLogin());
$mycurrency = $objdeCrypt->GetCurrency();
$myamount = $objdeCrypt->GetAmount();
$myshoptransactionID = trim($objdeCrypt->GetShopTransactionID());
$mybuyername = trim($objdeCrypt->GetBuyerName());
$mybuyeremail = trim($objdeCrypt->GetBuyerEmail());
$mytransactionresult = trim($objdeCrypt->GetTransactionResult());
$myauthorizationcode = trim($objdeCrypt->GetAuthorizationCode());
$myerrorcode = trim($objdeCrypt->GetErrorCode());
$myerrordescription = trim($objdeCrypt->GetErrorDescription());
$myerrorbanktransactionid = trim($objdeCrypt->GetBankTransactionID());
$myalertcode = trim($objdeCrypt->GetAlertCode());
$myalertdescription = trim($objdeCrypt->GetAlertDescription());
$mycustominfo = trim($objdeCrypt->GetCustomInfo());

list($user_id, $selected_language, $transaction_id) = split('[-]', $myshoptransactionID);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>