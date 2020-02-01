<?php
//============================================================+
// File name   : cp_functions_permissions.php                  
// Begin       : 2003-09-30                                    
// Last Update : 2003-10-12                                    
//                                                             
// Description : set users temporary access permission to 
//               selected resource
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

// ----------------------------------------------------------
// Set users temporary access permission to 
// selected resource
// ----------------------------------------------------------
function F_set_user_permission($user_id, $time_start, $time_end, $resource) {
	global $db, $l, $selected_language;

	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	$sql = "INSERT IGNORE INTO ".K_TABLE_USERS_AUTH." (
			ua_user_id,             
			ua_time_start,            
			ua_time_end,       
			ua_resource 
			) VALUES (
			'".$user_id."',             
			'".$time_start."',            
			'".$time_end."',       
			'".$resource."' 
			)";
	if(!$r = F_aiocpdb_query($sql, $db)) {
		F_display_db_error();
	}
}


// ----------------------------------------------------------
// Set users temporary access permission to 
// selected user for selected number of days
// ----------------------------------------------------------
function F_set_days_user_permission($user_id, $days, $resource) {
	global $db, $l, $selected_language;

	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	$time_start = gmdate("Y-m-d H:i:s");
	$time_end = gmdate("Y-m-d H:i:s", ($days * K_SECONDS_IN_DAY) + time());
	
	F_set_user_permission($user_id, $time_start, $time_end, $resource);
}
//============================================================+
// END OF FILE                                                 
//============================================================+
?>
