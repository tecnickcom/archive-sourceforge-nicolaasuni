<?php
//============================================================+
// File name   : cp_functions_sounds.php                       
// Begin       : 2003-01-23                                    
// Last Update : 2003-10-12                                    
//                                                             
// Description : Functions for Sounds                          
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
// return menu_iconid given icon_link
// ------------------------------------------------------------
function F_get_sound_id($soundlink) {
	global $db;
	$sql = "SELECT sound_id FROM ".K_TABLE_SOUNDS_MENU." WHERE sound_link='".$soundlink."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			return ($m['sound_id']);
		}
	}
	return(1); //Error code
}

// ------------------------------------------------------------
// return menu_iconid given icon_link
// ------------------------------------------------------------
function F_get_sound_link($soundid) {
	global $db;
	$sql = "SELECT sound_link FROM ".K_TABLE_SOUNDS_MENU." WHERE sound_id='".$soundid."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			return ($m['sound_link']);
		}
	}
	return(1); //Error code
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
