<?php
//============================================================+
// File name   : cp_forum_edit_message.php                     
// Begin       : 2002-01-28                                    
// Last Update : 2002-09-29                                    
//                                                             
// Description : Forum Message Editor                          
//               $efmm : [ n=newtopic | r=reply | e=edit ]     
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
require_once('../../shared/code/cp_functions_forum_message.'.CP_EXT);

$pagelevel = K_AUTH_ADMIN_CP_FORUM_EDIT_MESSAGE;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

switch($efmm) { //n=newtopic; r=reply; e=edit;
	case "n": { // new topic
		$thispage_title = $l['t_forum_newtopic_editor'];
		break;
	}
	case "r": { // reply to a message
		$thispage_title = $l['t_forum_reply_editor'];
		break;
	}
	case "e": { // edit a message
		$thispage_title = $l['t_forum_message_editor'];
		break;
	}
	default : {
		$thispage_title = $l['t_error'];
		break;
	}
}//end of switch

require_once('../code/cp_page_header.'.CP_EXT);

if($thispage_title == $l['t_error']) {
	F_print_error("WARNING", $l['m_authorization_deny']);
	F_logout_form();
}
else {
	F_print_error(0, ""); //clear header messages
	if(!F_edit_forum_message($efmm, $categoryid, $forumid, $topicid, $postid, $forumtopic_title, $forumposts_text)) {
		F_print_error("WARNING", $l['m_authorization_deny']);
		F_logout_form();
	}
}

require_once('../code/cp_page_footer.'.CP_EXT); 

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
