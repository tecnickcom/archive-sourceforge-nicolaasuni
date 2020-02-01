<?php
//============================================================+
// File name   : cp_db_dal.php                                 
// Begin       : 2003-10-12                                    
// Last Update : 2003-10-12                                    
//                                                             
// Description : Load the functions for the selected database  
//               type (Database Abstraction Layer)             
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

// Load the Database Abstraction Layer for selected DATABASE type

switch (K_DATABASE_TYPE) {
	case 'MYSQL':
	default: {
		require_once('../../shared/code/cp_db_dal_mysql.'.CP_EXT);
		break;
	}
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
