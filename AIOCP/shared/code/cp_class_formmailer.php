<?php
//============================================================+
// File name   : cp_class_formmailer.php                       
// Begin       : 2001-11-07                                    
// Last Update : 2002-09-29                                    
//                                                             
// Description : Extend phpmailer class with inheritance       
//               to implement form mailer                      
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

require_once('../../shared/config/cp_extension.inc');
require_once('../config/cp_config.'.CP_EXT);

require_once('../../shared/config/cp_email_config.'.CP_EXT); //Include default public variables
require_once("../../shared/phpmailer/cp_class_phpmailer.php");

class C_form_mailer extends phpmailer {
	
	// Replace the default error_handler
	function error_handler($msg) {
		F_print_error("ERROR", $msg);
	exit;
	}
} //end of class

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
