<?php
//============================================================+
// File name   : cp_functions_icons.php                        
// Begin       : 2001-09-07                                    
// Last Update : 2003-10-12                                    
//                                                             
// Description : Functions for Icons                           
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
function F_get_icon_id($icontable, $iconlink) {
	global $db;
	$sql = "SELECT icon_id FROM ".$icontable." WHERE icon_link='".$iconlink."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			return ($m['icon_id']);
		}
	}
	return(1); //Error code
}

// ------------------------------------------------------------
// return menu_iconid given icon_link
// ------------------------------------------------------------
function F_get_icon_link($icontable, $iconid) {
	global $db;
	$sql = "SELECT icon_link FROM ".$icontable." WHERE icon_id='".$iconid."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			return ($m['icon_link']);
		}
	}
	return(1); //Error code
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
