<?php
//============================================================+
// File name   : cp_logout.php
// Begin       : 2001-09-28
// Last Update : 2010-10-04
//
// Description : destroy user session (logout)
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
require_once('../../shared/code/cp_functions_session.'.CP_EXT);

// Destroys all user's session data
session_unset();
session_destroy();
// destroy session ID cookie
setcookie('PHPSESSID', '', 1, K_COOKIE_PATH, K_COOKIE_DOMAIN, K_COOKIE_SECURE);

F_print_error(0, ''); //clear error messages
if (!$current_page) {
	$current_page = '../code/cp_layout_main.'.CP_EXT;
}

echo "<"."?xml version=\"1.0\" encoding=\"".$l['a_meta_charset']."\"?".">\n";
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"DTD/xhtml1-transitional.dtd\">\n";
echo "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"".$l['a_meta_language']."\" lang=\"".$l['a_meta_language']."\" dir=\"".$l['a_meta_dir']."\">\n";
echo "<head>\n";
echo "<title>LOGOUT</title>\n";
echo "<meta http-equiv=\"refresh\" content=\"0;url=".$current_page."\" />\n"; //reload page
echo "</head>\n";
echo "<body>\n";
echo "<a href=\"".$current_page."\" target=\"_top\">LOGOUT</a>\n";
echo "</body>\n";
echo "</html>\n";

//============================================================+
// END OF FILE
//============================================================+
