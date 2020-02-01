<?php
//============================================================+
// File name   : cp_languages.php                              
// Begin       : 2001-09-30                                    
// Last Update : 2003-10-12                                    
//                                                             
// Description : Load language templates for selected language 
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

$l = array(); //create array of words in selected language

if ((!isset($selected_language)) OR (!$selected_language)) {
	$selected_language = K_DEFAULT_LANGUAGE; //select default language
}

$sql = "SELECT word_id, ".K_DEFAULT_LANGUAGE.",".$selected_language." FROM ".K_TABLE_LANGUAGE_DATA."";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		if($m[$selected_language]) { //check if the word exist
			$l[$m['word_id']] = $m[$selected_language]; //add elements to array
		}
		elseif ($m[K_DEFAULT_LANGUAGE]) { //if word not exist a default language word will be used
			$l[$m['word_id']] = $m[K_DEFAULT_LANGUAGE];
		}
		else {
			$l[$m['word_id']] = "";
		}
	}
}
else {
	F_display_db_error();
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
