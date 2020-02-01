<?php
//============================================================+
// File name   : cp_whois.php                                  
// Begin       : 2001-10-02                                    
// Last Update : 2008-07-06
//                                                             
// Description : do whois queries                              
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

$pagelevel = K_AUTH_ADMIN_CP_WHOIS;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_whois'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages;

require_once('../../shared/code/cp_functions_whois.'.CP_EXT);
if (isset($_REQUEST['whois_query'])) {
	$whois_query = $_REQUEST['whois_query'];
} else {
	$whois_query = "";
}
F_show_whois($whois_query); 
?>
<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
