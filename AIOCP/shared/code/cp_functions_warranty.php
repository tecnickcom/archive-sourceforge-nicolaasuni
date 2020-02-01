<?php
//============================================================+
// File name   : cp_functions_warranty.php                     
// Begin       : 2002-07-10                                    
// Last Update : 2003-10-12                                    
//                                                             
// Description : Functions to display warranty certificates    
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
// select and display a banner for the specified zone
// ------------------------------------------------------------
function F_show_warranty($warranty_id) {
	global $l, $db, $selected_language;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	
	$sql = "SELECT * FROM ".K_TABLE_EC_WARRANTIES." WHERE warranty_id=".$warranty_id." LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$warranty_name = $m['warranty_name'];
			$warranty_description = $m['warranty_description'];
			$a_description = unserialize($warranty_description);
			
			return $a_description[$selected_language];
		}
	}
	else {
		F_display_db_error();
	}
	return "";
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
