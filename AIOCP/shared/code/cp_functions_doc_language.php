<?php
//============================================================+
// File name   : cp_functions_doc_language.php                 
// Begin       : 2003-01-26                                    
// Last Update : 2003-10-12                                    
//                                                             
// Description : Load language templates for document          
//               language                                      
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
// return all templates language for PDF document
// ------------------------------------------------------------
function F_get_pdf_language_templates($doc_language) {
	global $l, $db;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	if ((!isset($doc_language)) AND (!$doc_language)) {
		$doc_language = K_DEFAULT_LANGUAGE;//select default language
	} 
	
	$sql = "SELECT word_id, ".K_DEFAULT_LANGUAGE.",".$doc_language." FROM ".K_TABLE_LANGUAGE_DATA."";
	if($r = F_aiocpdb_query($sql, $db)) {
		$ld = array(); //create array of words in selected language
		while($m = F_aiocpdb_fetch_array($r)) {
			if($m[$doc_language]) { //check if the word exist
				$ld[$m['word_id']] = unhtmlentities($m[$doc_language], FALSE); //add elements to array
			}
			elseif ($m[K_DEFAULT_LANGUAGE]) { //if word not exist a default language word will be used
				$ld[$m['word_id']] = unhtmlentities($m[K_DEFAULT_LANGUAGE], FALSE);
			}
			else {
				$ld[$m['word_id']] = "";
			}
		}
		return $ld;
	}
	else {
		F_display_db_error();
	}
	return false;
}
//============================================================+
// END OF FILE                                                 
//============================================================+
?>
