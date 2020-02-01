<?php
//============================================================+
// File name   : cp_user_profile.php                           
// Begin       : 2002-02-05                                    
// Last Update : 2008-07-06
//                                                             
// Description : Show user public profile                      
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
$pagelevel = 1;
require_once('../../shared/code/cp_authorization.'.CP_EXT);
require_once('../../shared/code/cp_functions_collect_stats.'.CP_EXT);

//leave following variables void for default values
$thispage_title = $l['t_user_profile'];
$thispage_description = "";
$thispage_author = "";
$thispage_reply = "";
$thispage_keywords = "";
$thispage_style = "";

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages;

require_once('../code/cp_functions_user_show.'.CP_EXT);
if(!isset($user_id) OR (!$user_id)) {
	$user_id = $_SESSION['session_user_id'];
}
if(!F_show_user_profile($user_id)) {
	F_print_error("WARNING", $l['m_authorization_deny']);
	F_logout_form();
}

require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
