<?php
//============================================================+
// File name   : cp_traceroute.php                             
// Begin       : 2001-10-02                                    
// Last Update : 2002-09-29                                    
//                                                             
// Description : execute traceroute                            
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
require_once('../../shared/code/cp_functions_traceroute.'.CP_EXT);

$pagelevel = K_AUTH_ADMIN_CP_TRACEROUTE;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_traceroute'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages;
if (isset($_REQUEST['iphosttotrace'])) {
	$iphosttotrace = $_REQUEST['iphosttotrace'];
} else {
	$iphosttotrace = "";
}
F_show_traceroute($iphosttotrace);
require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
