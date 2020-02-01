<?php
//============================================================+
// File name   : cp_db_connect.php          
// Begin       : 2001-09-02                 
// Last Update : 2003-10-12                 
//                                          
// Description : open connection with active database
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

// Load the Database Abstraction Layer for selected DATABASE type
require_once('../../shared/code/cp_db_dal.'.CP_EXT);

if(!$db = @F_db_connect(K_DATABASE_HOST, K_DATABASE_PORT, K_DATABASE_USER_NAME, K_DATABASE_USER_PASSWORD, K_DATABASE_NAME)) {
	die('<h2>'.F_db_error().'</h2>');
}

//============================================================+
// END OF FILE                              
//============================================================+
