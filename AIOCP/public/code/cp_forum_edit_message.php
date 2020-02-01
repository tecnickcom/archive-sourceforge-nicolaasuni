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
$pagelevel = 0; // page level
require_once('../../shared/code/cp_authorization.'.CP_EXT); // check authorization permissions
require_once('../../shared/code/cp_functions_collect_stats.'.CP_EXT); // collect site statistics
$page_name = "forum_edit"; // name of the page (used in DB)
$pt = F_load_page_templates($selected_language, $page_name); // load page templates for the current language

require_once('../../shared/code/cp_functions_forum_message.'.CP_EXT);

switch($efmm) { // e=edit; n=newtopic; r=reply;
	case "e": { // edit a message
		// The following are values for META TAGS
		// (leave void for default values)
		$thispage_author = ""; // name of page author
		$thispage_reply = ""; // email address
		$thispage_style = ""; // CSS page link
		$thispage_title = $pt['_title']; // page title
		$thispage_description = $pt['_description']; // page description
		$thispage_keywords = $pt['_keywords']; // page keywords
		break;
	}
	case "n": { // new topic
		// The following are values for META TAGS
		// (leave void for default values)
		$thispage_author = ""; // name of page author
		$thispage_reply = ""; // email address
		$thispage_style = ""; // CSS page link
		$thispage_title = $pt['_title_newtopic']; // page title
		$thispage_description = $pt['_description_newtopic']; // page description
		$thispage_keywords = $pt['_keywords_newtopic']; // page keywords
		break;
	}
	case "r": { // reply to a message
		// The following are values for META TAGS
		// (leave void for default values)
		$thispage_author = ""; // name of page author
		$thispage_reply = ""; // email address
		$thispage_style = ""; // CSS page link
		$thispage_title = $pt['_title_reply']; // page title
		$thispage_description = $pt['_description_reply']; // page description
		$thispage_keywords = $pt['_keywords_reply']; // page keywords
		break;
	}
	default : {
		// The following are values for META TAGS
		// (leave void for default values)
		$thispage_author = ""; // name of page author
		$thispage_reply = ""; // email address
		$thispage_style = ""; // CSS page link
		$thispage_title = $pt['_title_error']; // page title
		$thispage_description = $pt['_description_error']; // page description
		$thispage_keywords = $pt['_keywords_error']; // page keywords
		$thispage_error = TRUE;
		break;
	}
}//end of switch

require_once('../code/cp_page_header.'.CP_EXT);

if($thispage_error) {
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
