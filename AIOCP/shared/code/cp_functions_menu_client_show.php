<?php
//============================================================+
// File name   : cp_functions_menu_client_show.php             
// Begin       : 2002-03-21                                    
// Last Update : 2003-10-12                                    
//                                                             
// Description : Functions for show Client Menu                
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

$menucode = ""; //global variable that contain the entire menu code
$menualtcode = ""; //global variable that contain the alternative code for menu (for web spiders)

//------------------------------------------------------------
// Return client menu options
//------------------------------------------------------------
function F_get_menu_client_options($menuopt_id) {
	global $l, $db;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	
	//read menu options
	$sql = "SELECT * FROM ".K_TABLE_MENU_OPTIONS." WHERE menuopt_id=".$menuopt_id." LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			$menuopt = F_aiocpdb_fetch_array($r);
			if($menuopt) {
				return $menuopt;
			}
			else {
				return FALSE;
			}
		}
		else {
			F_display_db_error();
		}
	return FALSE;
}

//------------------------------------------------------------
// Return client menu style
//------------------------------------------------------------
function F_get_menu_client_style($menustyle_id) {
	global $l, $db;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	
	$sql = "SELECT * FROM ".K_TABLE_MENU_STYLES." WHERE menustyle_id='".$menustyle_id."'";
	if($r = F_aiocpdb_query($sql, $db)) {
		$menustyle = F_aiocpdb_fetch_array($r);
		if ($menustyle) {
			return $menustyle;
		}
		else {
			return FALSE;
		}
	}
	else {
		F_display_db_error();
	}
	return FALSE;
}

// ------------------------------------------------------------

// ------------------------------------------------------------
// Show the client menu
// ------------------------------------------------------------
function F_show_client_menu($menu_name, $forcetext=false, $menutype=1) {
	global $l, $db, $selected_language;
	global $menucode, $menualtcode, $popup_position;
	global $aiocp_dp, $hp;
	require_once('../../shared/config/cp_extension.inc');
	
	switch ($menutype) {
		default:
		case 1: {	
			require_once('../../shared/code/cp_functions_menu_jddm.'.CP_EXT);
			return F_show_jddm_menu($menu_name, $forcetext);
			break;
		}
		case 2: {	
			require_once('../../shared/code/cp_functions_menu_jwtm.'.CP_EXT);
			return F_show_jwtm_menu($menu_name, $forcetext);
			break;
		}
	}
}


//============================================================+
// END OF FILE                                                 
//============================================================+
?>
