<?php
//============================================================+
// File name   : cp_functions_company_data.php                 
// Begin       : 2002-07-22                                    
// Last Update : 2002-09-29                                    
//                                                             
// Description : read company data from text cfg file          
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
// Read Company Data from configuration file
//------------------------------------------------------------
function F_get_company_data() {
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	global $l;
	
	if($fp = fopen(K_FILE_COMPANY_DATA, "r")) {
		$contents = fread($fp, filesize(K_FILE_COMPANY_DATA));
		fclose($fp);
	}
	else { //print an error message
		F_print_error("ERROR", "".K_FILE_COMPANY_DATA.": ".$l['m_openfile_not']);
		return FALSE;
	}
	
	$contents = unserialize($contents);
	while(list($key, $val) = each($contents)) {
		$contents[$key] = stripslashes($val);
	}
	
	return $contents;
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
