<?php
//============================================================+
// File name   : cp_functions_newsletter_gc.php                
// Begin       : 2003-08-26                                    
// Last Update : 2003-10-12                                    
//                                                             
// Description : Garbage collector for newsletter users        
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
// Garbage Collector for Newsletter Users
// Delete old disabled users
// Banned users will not be removed (nluser_enabled=2)
//------------------------------------------------------------
function F_gc_newsletter_users() {
	global $l, $db, $selected_language;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	$expirytime = time() - (K_MAX_WAIT_VERIFICATION * K_SECONDS_IN_DAY);
	$sql = "DELETE FROM ".K_TABLE_NEWSLETTER_USERS." WHERE (nluser_enabled=0 AND nluser_signupdate<=".$expirytime.")";
	if(!$r = F_aiocpdb_query($sql, $db)) {
		F_display_db_error();
	}
	return;
}
//============================================================+
// END OF FILE                                                 
//============================================================+
?>
