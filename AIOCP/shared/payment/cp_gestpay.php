<?php
//============================================================+
// File name   : cp_gestpay.php                                
// Begin       : 2002-08-29                                    
// Last Update : 2003-01-26                                    
//                                                             
// Description : Payment module                                
//               GestPay from Banca Sella (www.sellanet.it)    
//               Send payment request to bank server           
//               Based on 1.1.6 specifications                 
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
require_once('../../shared/config/cp_paths.'.CP_EXT);
require_once('../../shared/code/cp_functions_ec_payment.'.CP_EXT);
require_once('../../shared/payment/inc_gestpay_crypt.'.CP_EXT);
require_once('../../shared/payment/inc_gestpay_functions.'.CP_EXT);

// Please set to the right values the following 2 variables

// shop login (e.g. 9000001)
$myshoplogin = "GESPAY07149";

// connection error page URL (e.g. "http://www.mysite.com/connectionerror.html")
$myerrpage= K_PATH_HOST.K_PATH_AIOCP."public/code/cp_ec_payment_error.php";

// DO NOT MODIFY BELOW THIS LINE
// ----------------------------------------------------------------

//Code that identifies the currency in which is denominated transaction amount (UIC code tables)
$mycurrency = intval(K_MONEY_CURRENCY_UIC_CODE);

//check decimals
if (K_MONEY_DECIMALS > 2) {
	$numofdecimals = 2;
}
else {
	$numofdecimals = K_MONEY_DECIMALS;
}

//Transaction amount. Donot insert separator of thousands. Decimals (max 2 numbers) are optional and separator is the full mark (e.g. 1256.28).
$myamount = "".round(F_calculate_total_amount($user_id, $transaction_id), $numofdecimals)."";

// Identifier attributed to merchant’s transaction (e.g. "34az85ord19")
$myshoptransactionID = $user_id."-".$selected_language."-".$transaction_id; 

// Buyer’s name and surname (e.g. John Smith)
$mybuyername = $user_data->firstname." ".$user_data->lastname;

// Buyer’s e-mail address (e.g. john.smith@isp.com)
$mybuyeremail = $user_data->email;

// Code that identifies the language used in the communication with the buyer (see Language Code table)
$mylanguage = F_get_gestpay_language_code($selected_language);

// custom parameters (e.g. "BV_CLIENTCODE=12*P1*BV_SESSIONID=398")
/*
The CustomInfo attribute contains specific information that the merchant wants to communicate or receive from GestPay. Definition of which information are inserted in the CustomInfo attribute is realised in back office environment in the Fields and Parameters section.
The inserted information will follow this formalism:
datum1=value1*P1*datum2=value2*P1* … *P1*datumn=valuen
Separator among logically different information is the reserved data sequence *P1*
*/
$mycustominfo = "";


$objCrypt = new GestPayCrypt;
//$objCrypt = new GestPayCryptHS; // HTTPS version

$objCrypt->SetShopLogin($myshoplogin);
$objCrypt->SetCurrency($mycurrency);
$objCrypt->SetAmount($myamount);
$objCrypt->SetShopTransactionID($myshoptransactionID);
$objCrypt->SetBuyerName($mybuyername);
$objCrypt->SetBuyerEmail($mybuyeremail);
$objCrypt->SetLanguage($mylanguage);
$objCrypt->SetCustomInfo($mycustominfo);

$objCrypt->Encrypt();

$ed = $objCrypt->GetErrorDescription();
if($ed != "") {
	F_print_error("ERROR", $objCrypt->GetErrorCode()." ".$ed);
}
else {
	$b = $objCrypt->GetEncryptedString();
	$a = $objCrypt->GetShopLogin();
	
	echo "<form action=\"https://ecomm.sella.it/gestpay/pagam.asp\" name=\"GestPayForm\" id=\"GestPayForm\" target=\"_top\">\n";
	echo "<input type=\"hidden\" name=\"a\" id=\"a\" value=\"".$a."\" />\n";
	echo "<input type=\"hidden\" name=\"b\" id=\"b\" value=\"".$b."\" />\n";
	echo "</form>\n";
	
	//submit form to bank server
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
	echo "//<![CDATA[\n";
	echo "document.GestPayForm.submit();\n";
	echo "//]]>\n";
	echo "</script>\n";
}

exit;

//============================================================+
// END OF FILE                                                 
//============================================================+
?>