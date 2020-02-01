<?php
//============================================================+
// File name   : cp_functions_levels.php                       
// Begin       : 2001-09-14                                    
// Last Update : 2003-10-12                                    
//                                                             
// Description : Functions for Levels                          
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

//------------------------------------------------------------
// get level data from ID 
//------------------------------------------------------------
function F_get_level_data($levelcode) {
	global $db, $l;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	$sql = "SELECT * FROM ".K_TABLE_LEVELS." WHERE level_code='".$levelcode."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$leveldata = new stdClass();
			$leveldata->id = $m['level_id'];
			$leveldata->code = $m['level_code'];
			$leveldata->name = $m['level_name'];
			$leveldata->description = $m['level_description'];
			$leveldata->image = $m['level_image'];
			$leveldata->width = $m['level_width'];
			$leveldata->height = $m['level_height'];
			return $leveldata;
		}
	}
	else {
		F_display_db_error();
	}
	return FALSE;
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
