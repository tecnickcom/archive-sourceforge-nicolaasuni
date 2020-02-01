<?php
//============================================================+
// File name   : cp_php_info.php                               
// Begin       : 2001-09-08                                    
// Last Update : 2002-09-29                                    
//                                                             
// Description : Outputs a large amount of information about   
//               the current state of PHP.                     
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
require_once('../config/cp_auth.'.CP_EXT);

$pagelevel = K_AUTH_ADMIN_CP_PHP_INFO;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

phpinfo(); 

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
