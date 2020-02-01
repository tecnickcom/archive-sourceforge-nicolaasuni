<?php
//============================================================+
// File name   : cp_polls_vote.php                             
// Begin       : 2001-10-10                                    
// Last Update : 2008-07-06
//                                                             
// Description : Vote polls (K_TABLE_POLLS_VOTES table)        
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

$pagelevel = K_AUTH_ADMIN_CP_POLLS_VOTE;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_polls_vote'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages;

require_once('../../shared/code/cp_functions_polls_vote.'.CP_EXT);

if (isset($_REQUEST["poll_language"])) {
	$poll_language = $_REQUEST["poll_language"];
} else {
	$poll_language = "";
}
if (isset($_REQUEST["poll_id"])) {
	$poll_id = $_REQUEST["poll_id"];
} else {
	$poll_id = "";
}

F_show_poll_form($poll_language, $poll_id, true);

require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
