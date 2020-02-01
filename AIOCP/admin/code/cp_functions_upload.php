<?php
//============================================================+
// File name   : cp_functions_upload.php                       
// Begin       : 2001-11-19                                    
// Last Update : 2002-03-06                                    
//                                                             
// Description : Functions for upload                          
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
// Uploads image file to the server 
//------------------------------------------------------------
function F_upload_file($fieldname, $uploaddir) {
	global $l;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	if(move_uploaded_file ($_FILES[$fieldname]['tmp_name'], $uploaddir.$_FILES[$fieldname]['name'])) {
		F_print_error("MESSAGE", htmlentities($_FILES[$fieldname]['name']).": ".$l['m_upload_yes']);
		return $_FILES[$fieldname]['name'];
	}
	
	F_print_error("ERROR", htmlentities($_FILES[$fieldname]['name']).": ".$l['m_upload_not']."");
	return FALSE;
}
// ------------------------------------------------------------

//------------------------------------------------------------
// return the file size in bytes
//------------------------------------------------------------
function F_read_file_size($filetocheck) {
	global $l;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	$filesize = 0;
	
	if($fp = fopen($filetocheck, "rb")) {
		$s_array = fstat($fp);
		if($s_array['size']) {
			$filesize = $s_array['size'];
		}
		else {//read size from remote file (very slow function)
			while(!feof($fp)) {
				$content = fread($fp, 1);
				$filesize++;
			}
		}
		fclose($fp);
		return($filesize);
	}
	
	F_print_error("ERROR", basename($filetocheck).": ".$l['m_openfile_not']);
	return FALSE;
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>