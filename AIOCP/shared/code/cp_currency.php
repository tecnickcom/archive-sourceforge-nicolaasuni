<?php
//============================================================+
// File name   : cp_currency.php                               
// Begin       : 2002-08-30                                    
// Last Update : 2003-11-18                                    
//                                                             
// Description : Load currency data                            
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

$sql = "SELECT * FROM ".K_TABLE_EC_CURRENCY." WHERE currency_iso_code_alpha='".strtoupper(K_MONEY_CURRENCY_ISO_ALPHA_CODE)."' LIMIT 1";
if ($r = F_aiocpdb_query($sql, $db)) {
	if ($m = F_aiocpdb_fetch_array($r)) {
		define ("K_MONEY_CURRENCY", $m['currency_name']);
		define ("K_MONEY_DECIMALS", $m['currency_decimals']); //number of decimals for currency
		define ("K_MONEY_THOUSAND_SEPARATOR", $m['currency_thousand_separator']); //thousand separator (used in PDF documents)
		define ("K_MONEY_DECIMAL_SEPARATOR", $m['currency_decimals_separator']); //decimal separator (used in PDF documents)
		define ("K_MONEY_CURRENCY_ISO_NUMERIC_CODE", $m['currency_iso_code_numeric']);
		define ("K_MONEY_CURRENCY_UIC_CODE", $m['currency_uic_code']);
		define ("K_MONEY_CURRENCY_NAME_MINOR", $m['currency_name_minor']);
		define ("K_MONEY_CURRENCY_DESCRIPTION", $m['currency_description']);
		define ("K_MONEY_CURRENCY_SYMBOL", $m['currency_char_symbol']);
		
		if ($m['currency_unicode_symbol']) {
			define ("K_MONEY_CURRENCY_UNICODE_SYMBOL", $m['currency_unicode_symbol']);
		}
		else {
			define ("K_MONEY_CURRENCY_UNICODE_SYMBOL", htmlentities($m['currency_char_symbol'], ENT_NOQUOTES, "UTF-8"));
		}
	}
}
else {
	F_display_db_error();
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>