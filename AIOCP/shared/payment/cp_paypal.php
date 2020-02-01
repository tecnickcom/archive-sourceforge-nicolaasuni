<?php
//============================================================+
// File name   : cp_paypal.php 
// Begin       : 2002-09-18 
// Last Update : 2007-01-09
//  
// Description : Payment module
//               PayPal Single Item
//               www.paypal.com
//
// Notes:
// - you must add transaction fee to total amount
// based on PayPal Specification updated at 2002-11-18
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
require_once('../../shared/config/cp_paths.'.CP_EXT);
require_once('../../shared/code/cp_functions_ec_payment.'.CP_EXT);

// Please set to the right values the following variables

// This is your PayPal ID, or email address, where payments will be sent. This email address must be confirmed and linked to your Verified Business or Premier account.
$pp_paypal_id = "";

//The internet URL of the 150 by 50 pixel image you would like to use as your logo. This is optional; if omitted, your customer will see your Business Name (if you have a Business account) or email address (if you have a Premier
$pp_image_url = "";

//list here PayPal supported currencies
$supported_currencies = array("CAD","EUR","GBP","JPY","USD");

// DO NOT MODIFY BELOW THIS LINE
// ----------------------------------------------------------------

// check currency
if (!in_array(K_MONEY_CURRENCY_ISO_ALPHA_CODE, $supported_currencies)) {
	F_print_error("ERROR", $l['m_wrong_currency']);
	exit;
}

// Description of item (maximum 127 characters). This is optional; if omitted, customer will see a field in which they have the option of entering an Item Name.
//$pp_item_name = $l['w_order'];
$pp_item_name = "Order";

// Pass-through variable for you track payments. It will not be displayed to your customer, but will get passed back to you at the completion of payment (maximum 127 characters). This is optional; if omitted, no variable will be passed back to you.
$pp_item_number = $transaction_id;

// An internet URL where the user will be returned after completing the payment. For example, a URL on your site that hosts a "Thank you for your payment"page. This item is optional, if omitted, users will be taken to the PayPal site.
//$pp_return_page = K_PATH_HOST.K_PATH_AIOCP."public/code/cp_show_ec_documents.".CP_EXT."?uid=".$user_id."";

// An internet URL where the user will be returned if payment is canceled. For example, a URL on your site which hosts a "Payment Canceled"page. This item is optional, if omitted, users will be taken to the PayPal site.
//$pp_cancel_return = K_PATH_HOST.K_PATH_AIOCP."public/code/cp_ec_payment_error.".CP_EXT."?err=".urlencode('Payment Canceled')."";

//check decimals
if (K_MONEY_DECIMALS > 2) {
	$numofdecimals = 2;
}
else {
	$numofdecimals = K_MONEY_DECIMALS;
}

//Transaction amount. Donot insert separator of thousands. Decimals (max 2 numbers) are optional and separator is the full mark (e.g. 1256.28).
$amount = "".round(F_calculate_total_amount($user_id, $transaction_id), $numofdecimals)."";

// start form

echo "<form action=\"https://www.paypal.com/cgi-bin/webscr\" method=\"post\" name=\"PayPalForm\" id=\"PayPalForm\" target=\"_top\">\n";

// [required] Must be set to "_xclick".
echo "<input type=\"hidden\" name=\"cmd\" id=\"cmd\" value=\"_xclick\" />\n";

// required] This is your PayPal ID, or email address, where payments will be sent. This email address must be confirmed and linked to your Verified Business or Premier account.
echo "<input type=\"hidden\" name=\"business\" id=\"business\" value=\"".$pp_paypal_id."\" />\n";

// Description of item (maximum 127 characters). This is optional; if omitted, customer will see a field in which they have the option of entering an Item Name.
if (strlen($pp_item_name) > 0) {
	echo "<input type=\"hidden\" name=\"item_name\" id=\"item_name\" value=\"".$pp_item_name."\" />\n";
}

// Pass-through variable for you track payments. It will not be displayed to your customer, but will get passed back to you at the completion of payment (maximum 127 characters). This is optional; if omitted, no variable will be passed back to you.
if (strlen($pp_item_number) > 0) {
	echo "<input type=\"hidden\" name=\"item_number\" id=\"item_number\" value=\"".$pp_item_number."\" />\n";
}

// The internet URL of the 150 by 50 pixel image you would like to use as your logo. This is optional; if omitted, your customer will see your Business Name (if you have a Business account) or email address (if you have a Premier account).
if (strlen($pp_image_url) > 0) {
	echo "<input type=\"hidden\" name=\"image_url\" id=\"image_url\" value=\"".$pp_image_url."\" />\n";
}

// Shipping address. If set to "1,"your customer will not be asked for a shipping address. This is optional; if omitted or set to "0"your customer will be prompted to include a shipping address.
echo "<input type=\"hidden\" name=\"no_shipping\" id=\"no_shipping\" value=\"1\" />\n";

// An internet URL where the user will be returned after completing the payment. For example, a URL on your site that hosts a "Thank you for your payment"page. This item is optional, if omitted, users will be taken to the PayPal site.
if (strlen($pp_return_page) > 0) {
	echo "<input type=\"hidden\" name=\"return\" id=\"return\" value=\"".$pp_return_page."\" />\n";
}

//Return URL behavior. If set to "1"and if a "return"value is submitted, upon completion of the payment the buyer will be sent back to the return URL using a GET method, and no transaction variables will be submitted. If set to "2"and if a "return"value is submitted, the buyer will be sent back to the return URL using a POST method, to which all available transaction variables will also be posted. If omitted or set to "0”, GET methods will be used for all Subscriptions transactions and Single Item Purchase, Donations, or Shopping Cart transactions in which IPN is not enabled. POST methods with variables will be used for the rest.
echo "<input type=\"hidden\" name=\"rm\" id=\"rm\" value=\"0\" />\n";

// An internet URL where the user will be returned if payment is canceled. For example, a URL on your site which hosts a "Payment Canceled"page. This item is optional, if omitted, users will be taken to the PayPal site.
if (strlen($pp_cancel_return) > 0) {
	echo "<input type=\"hidden\" name=\"cancel_return\" id=\"cancel_return\" value=\"".$pp_cancel_return."\" />\n";
}

// Including a note with payment. If set to "1,"your customer will not be prompted to include a note. This is optional; if omitted or set to "0"your customer will be prompted to include a note.
echo "<input type=\"hidden\" name=\"no_note\" id=\"no_note\" value=\"1\" />\n";

// Label that will appear above the note field (maximum 40 characters). This value is not saved and will not appear in any of your notifications. This is optional; if omitted, no variable will be passed back to you.
echo "<input type=\"hidden\" name=\"cn\" id=\"cn\" value=\"\" />\n";

// Sets the background color of your payment pages. If set to "1,"the background color will be black. This is optional: if omitted or set to "0"the background color will be white.
echo "<input type=\"hidden\" name=\"cs\" id=\"cs\" value=\"0\" />\n";

// First option field name (maximum 64 characters). This is optional; if omitted, no variable will be passed back to you.
//echo "<input type=\"hidden\" name=\"on0\" id=\"on0\" value=\"\" />\n";

// First set of option value(s). If this option is selected through a text box (or radio button), each value should be no more than 64 characters. If this value is entered by the customer through a text box, there is a 200-character limit. This is optional; if omitted, no variable will be passed back to you.
//echo "<input type=\"hidden\" name=\"os0\" id=\"os0\" value=\"\" />\n";

// Second option field name (maximum 64 characters). This is optional; if omitted, no variable will be passed back to you.
//echo "<input type=\"hidden\" name=\"on1\" id=\"on1\" value=\"\" />\n";

// Second set of option value(s). If this option is selected through a text box (or radio button), each value should be no more than 64 characters. If this value is entered by the customer through a text box, there is a 200-character limit. This is optional; if omitted, no variable will be passed back to you.
//echo "<input type=\"hidden\" name=\"os1\" id=\"os1\" value=\"\" />\n";

// If set to "1", the user will be able to edit the quantity. This means your customer will see a field next to quantity which they must complete. This is optional; if omitted or set to "0", the quantity will not be editable by the user. Instead, it will default to 1
echo "<input type=\"hidden\" name=\"undefined_quantity\" id=\"undefined_quantity\" value=\"0\" />\n";

// The cost of shipping this item if you have enabled item-specific shipping costs. If shipping is used and shipping_extra is not defined, this flat amount will be charged regardless of the quantity of items purchased. If you are using item-based shipping, make sure override check-box is checked in your Profile. This is optional; if omitted, and your Profile-based shipping is enabled, your customer will be charged the amount or percentage defined in your Profile.
//echo "<input type=\"hidden\" name=\"shipping\" id=\"shipping\" value=\"\" />\n";

// The cost of shipping each additional item. This is optional; if omitted, and your Profile-based shipping is enabled, your customer will be charged the amount or percentage defined in your Profile.
//echo "<input type=\"hidden\" name=\"shipping2\" id=\"shipping2\" value=\"\" />\n";

// The cost of handling. This is not quantity-specific. The same handling will be charged regardless of the number of items purchased. This is optional; if omitted, no handling charges will be assessed.
//echo "<input type=\"hidden\" name=\"handling\" id=\"handling\" value=\"\" />\n";

// Pass-through variable that will never be presented to your customer. This is optional; if omitted, no variable will be passed back to you.
echo "<input type=\"hidden\" name=\"custom\" id=\"custom\" value=\"".$user_id."-".$selected_language."-".$transaction_id."\" />\n";

// Pass-through variable that will never be presented to your customer. This is optional; if omitted, no variable will be passed back to you.
//echo "<input type=\"hidden\" name=\"invoice\" id=\"invoice\" value=\"".$transaction_id."\" />\n";

// total amount to pay
echo "<input type=\"hidden\" name=\"amount\" id=\"amount\" value=\"".$amount."\" />\n";

//Transaction-based tax override variable. Set to a flat tax amount you would like to apply to the transaction regardless of the buyer’s location. If present, this value overrides any tax settings that may be set in the seller’s Profile. If omitted, Profile tax settings (if any) will apply.
echo "<input type=\"hidden\" name=\"tax\" id=\"tax\" value=\"0\" />\n";

//currency
echo "<input type=\"hidden\" name=\"currency_code\" id=\"currency_code\" value=\"".K_MONEY_CURRENCY_ISO_ALPHA_CODE."\" />\n";


// The following form are to Pre–Populate Your Customer’s PayPal Sign-Up:
// ----------------------------------------------------------------------

// First name Alpha characters only. Maximum length = 32
if (isset($user_data->firstname) AND strlen($user_data->firstname) > 0) {
	echo "<input type=\"hidden\" name=\"first_name\" id=\"first_name\" value=\"".$user_data->firstname."\" />\n";
}

// Last name Alpha characters only. Maximum length = 64 
if (isset($user_data->lastname) AND strlen($user_data->lastname) > 0) {
	echo "<input type=\"hidden\" name=\"last_name\" id=\"last_name\" value=\"".$user_data->lastname."\" />\n";
}

if (isset($address['address_address']) AND strlen($address['address_address']) > 0) {
	if (strlen($address['address_address']) > 100) {
		$pp_address1 = substr($address['address_address'],0,100);
		$pp_address2 = substr($address['address_address'],99,100);
	}
	else {
		$pp_address1 = $address['address_address'];
		$pp_address2 = "";
	}
	// Street (1 of 2 fields) Alpha-Numeric characters only. Maximum length = 100
	echo "<input type=\"hidden\" name=\"address1\" id=\"address1\" value=\"".$pp_address1."\" />\n";
	
	// Street (2 of 2 fields) Alpha-Numeric characters only. Maximum length = 100
	echo "<input type=\"hidden\" name=\"address2\" id=\"address2\" value=\"".$pp_address2."\" />\n";
	
}

// City (1 of 2 fields) Alpha characters only. Maximum length = 100
if (isset($address['address_city']) AND strlen($address['address_city']) > 0) {
	echo "<input type=\"hidden\" name=\"city\" id=\"city\" value=\"".$address['address_city']."\" />\n";
}

// State Must be 2 character official abbreviation
if (isset($address['address_state']) AND strlen($address['address_state']) > 0) {
	echo "<input type=\"hidden\" name=\"state\" id=\"state\" value=\"".$address['address_state']."\" />\n";
}

// Zip Numeric characters only. Maximum length = 32 characters
if (isset($address['address_postcode']) AND strlen($address['address_postcode']) > 0) {
	echo "<input type=\"hidden\" name=\"zip\" id=\"zip\" value=\"".$address['address_postcode']."\" />\n";
}

// Home phone (1 of 3 fields) Numeric characters only. Maximum length = 3 characters
//echo "<input type=\"hidden\" name=\"night_phone_a\" id=\"night_phone_a\" value=\"\" />\n";

// Home phone (2 of 3 fields) Numeric characters only. Maximum length = 3 characters
//echo "<input type=\"hidden\" name=\"night_phone_b\" id=\"night_phone_b\" value=\"\" />\n";

// Home phone (3 of 3 fields) Numeric characters only. Maximum length = 3 characters
//echo "<input type=\"hidden\" name=\"night_phone_c\" id=\"night_phone_c\" value=\"\" />\n";

// Work phone (1 of 3 fields) Numeric characters only. Maximum length = 3 characters
//echo "<input type=\"hidden\" name=\"day_phone_a\" id=\"day_phone_a\" value=\"\" />\n";

// Work phone (2 of 3 fields) Numeric characters only. Maximum length = 3 characters
//echo "<input type=\"hidden\" name=\"day_phone_b\" id=\"day_phone_b\" value=\"\" />\n";

// Work phone (3 of 3 fields) Numeric characters only. Maximum length = 3 characters
//echo "<input type=\"hidden\" name=\"day_phone_c\" id=\"day_phone_c\" value=\"\" />\n";

echo "</form>\n";

//submit form to PayPal server
echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
echo "//<![CDATA[\n";
echo "document.PayPalForm.submit();\n";
echo "//]]>\n";
echo "</script>\n";

exit;

//============================================================+
// END OF FILE                                                 
//============================================================+
?>