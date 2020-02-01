<?php
//============================================================+
// File name   : cp_functions_paste_services.php               
// Begin       : 2001-04-06                                    
// Last Update : 2003-10-12                                    
//                                                             
// Description : Functions for paste code templates in page    
//               builder                                       
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
// Return service code as a string
// ------------------------------------------------------------
function F_get_service_code($module_id) {
	global $db;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	$return_code = "";
	$sql = "SELECT * FROM ".K_TABLE_PAGE_MODULES." WHERE pagemod_id='".$module_id."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$return_code = $m['pagemod_code'];
			$return_code = str_replace("$","\$", $return_code);
		}
	}
	else {
		F_display_db_error();
	}
	return $return_code;
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
