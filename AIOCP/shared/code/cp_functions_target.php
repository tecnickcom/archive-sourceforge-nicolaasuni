<?php
//============================================================+
// File name   : cp_functions_target.php                       
// Begin       : 2002-03-21                                    
// Last Update : 2003-10-12                                    
//                                                             
// Description : Function for frames targets                   
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
// get target name
// ------------------------------------------------------------
function F_get_target_name($target_id) {
	global $db;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	
	$sql = "SELECT * FROM ".K_TABLE_FRAME_TARGETS." WHERE target_id=".$target_id."";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			return ($m['target_name']);
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
