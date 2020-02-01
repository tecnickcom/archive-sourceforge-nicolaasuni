<?php
//============================================================+
// File name   : cp_newsletter_form.php                        
// Begin       : 2001-09-24                                    
// Last Update : 2008-07-06
//                                                             
// Description : subscribe/unsubsrcibe page for newsletter     
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

$pagelevel = K_AUTH_ADMIN_CP_NEWSLETTER_FORM;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_newsletter_form'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages;

require_once('../../shared/code/cp_functions_newsletter_users.'.CP_EXT);

if (isset($_REQUEST["nlcat_language"])) {
	$nlcat_language = $_REQUEST["nlcat_language"];
} else {
	$nlcat_language = "";
}
if (isset($_REQUEST["nlmsg_nlcatid"])) {
	$nlmsg_nlcatid = $_REQUEST["nlmsg_nlcatid"];
} else {
	$nlmsg_nlcatid = "";
}
F_newsletter_subscription_form(true, $nlcat_language, $nlmsg_nlcatid);

require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
