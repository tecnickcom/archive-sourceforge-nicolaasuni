<?php
//============================================================+
// File name   : cp_php_code_preview.php                       
// Begin       : 2001-04-07                                    
// Last Update : 2002-09-29                                    
//                                                             
// Description : Preview colored PHP code                      
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

if (isset($phpcode)) {
	$phpcode = stripslashes($phpcode);
	highlight_string("".$phpcode."");
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>