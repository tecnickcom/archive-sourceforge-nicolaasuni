<?php
//============================================================+
// File name   : cp_functions_mime.php                         
// Begin       : 2001-10-20                                    
// Last Update : 2003-10-12                                    
//                                                             
// Description : choose right MIME type from file extension    
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
// Choose the right MIME (Content-type) by file extension
//------------------------------------------------------------
function F_choose_mime($file) {
	global $db;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	
	//extract extension from file
	$path_parts = pathinfo($file);
	$file_ext = strtolower($path_parts['extension']);
	
	//read mime from table
	$sql = "SELECT * FROM ".K_TABLE_MIME." WHERE mime_extension='".$file_ext."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			return ($m['mime_content']);
		}
	}
	else {
		F_display_db_error();
	}
	return ("unknown");
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
