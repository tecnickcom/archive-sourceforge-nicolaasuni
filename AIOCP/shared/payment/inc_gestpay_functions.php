<?php
//============================================================+
// File name   : inc_gestpay_function.php                      
// Begin       : 2002-08-30                                    
// Last Update : 2002-09-29                                    
//                                                             
// Description : General functions for GestPay module          
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

// ------------------------------------------------------------
// Return GestPay language code
// $selected_language = MARC21 language code
// ------------------------------------------------------------
function F_get_gestpay_language_code($selected_language) {
	
	$language_table = Array (
		"ita" => "1",
		"eng" => "2",
		"spa" => "3",
		"fre" => "4",
		"ger" => "5"
	);
	
	if (array_key_exists($selected_language, $language_table)) {
		$language_code = $language_table[$selected_language];
	}
	elseif (array_key_exists(K_DEFAULT_LANGUAGE, $language_table)) {
		$language_code = $language_table[K_DEFAULT_LANGUAGE];
	}
	else {
		$language_code = $language_table["eng"];
	}
	
	return $language_code;
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
