<?php
//============================================================+
// File name   : cp_user_agenda.php                            
// Begin       : 2001-09-29                                    
// Last Update : 2005-06-29                                    
//                                                             
// Description : Display user agenda                           
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

$pagelevel = K_AUTH_ADMIN_CP_USER_AGENDA;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_user_agenda'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages;

require_once('../../shared/code/cp_functions_user_agenda.'.CP_EXT);
F_show_user_agenda();

require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
