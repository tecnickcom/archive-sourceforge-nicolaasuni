<?php
//============================================================+
// File name   : cp_edit_user.php
// Begin       : 2002-02-08 
// Last Update : 2008-08-10
// 
// Description : Edit user data (register)
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
$page_name = "user_edit"; // name of the page (used in DB)
$pt = F_load_page_templates($selected_language, $page_name); // load page templates for the current language

require_once('../../shared/code/cp_functions_user_edit.'.CP_EXT);

if (!isset($user_id)) {
	$user_id = $_SESSION['session_user_id'];
}
if (isset($_REQUEST["uemode"])) {
	$uemode = $_REQUEST["uemode"];
} else {
	$uemode = "";
}
switch($uemode) {
	case "company": { // edit user company
		// The following are values for META TAGS
		// (leave void for default values)
		$thispage_author = ""; // name of page author
		$thispage_reply = ""; // email address
		$thispage_style = ""; // CSS page link
		$thispage_title = $pt['_title_company']; // page title
		$thispage_description = $pt['_description_company']; // page description
		$thispage_keywords = $pt['_keywords_company']; // page keywords
		require_once('../code/cp_page_header.'.CP_EXT);
		F_print_error(0, ""); //clear header messages
		if(!F_edit_user_company($user_id)) {
			F_print_error("WARNING", $l['m_authorization_deny']);
			F_logout_form();
		}
		break;
	}
	case "phone": { // edit user phone numbers
		// The following are values for META TAGS
		// (leave void for default values)
		$thispage_author = ""; // name of page author
		$thispage_reply = ""; // email address
		$thispage_style = ""; // CSS page link
		$thispage_title = $pt['_title_phone']; // page title
		$thispage_description = $pt['_description_phone']; // page description
		$thispage_keywords = $pt['_keywords_phone']; // page keywords
		
		require_once('../code/cp_page_header.'.CP_EXT);
		F_print_error(0, ""); //clear header messages
		if(!F_edit_user_phone($user_id)) {
			F_print_error("WARNING", $l['m_authorization_deny']);
			F_logout_form();
		}
		break;
	}
	case "address": { // edit user postal ddresses
		// The following are values for META TAGS
		// (leave void for default values)
		$thispage_author = ""; // name of page author
		$thispage_reply = ""; // email address
		$thispage_style = ""; // CSS page link
		$thispage_title = $pt['_title_address']; // page title
		$thispage_description = $pt['_description_address']; // page description
		$thispage_keywords = $pt['_keywords_address']; // page keywords
		
		require_once('../code/cp_page_header.'.CP_EXT);
		F_print_error(0, ""); //clear header messages
		if(!F_edit_user_address($user_id)) {
			F_print_error("WARNING", $l['m_authorization_deny']);
			F_logout_form();
		}
		break;
	}
	case "internet": { // edit user internet data
		// The following are values for META TAGS
		// (leave void for default values)
		$thispage_author = ""; // name of page author
		$thispage_reply = ""; // email address
		$thispage_style = ""; // CSS page link
		$thispage_title = $pt['_title_internet']; // page title
		$thispage_description = $pt['_description_internet']; // page description
		$thispage_keywords = $pt['_keywords_internet']; // page keywords
		require_once('../code/cp_page_header.'.CP_EXT);
		F_print_error(0, ""); //clear header messages
		if(!F_edit_user_internet($user_id)) {
			F_print_error("WARNING", $l['m_authorization_deny']);
			F_logout_form();
		}
		break;
	}
	case "user": 
	default: { // edit user data
		$usregopt = F_get_user_reg_options(); //get user registration options
		//display registration agreement if necessary
		if ($usregopt['agreement'] AND (!isset($user_id) OR (!$user_id) OR ($user_id == 1)) AND ((!isset($user_agreed) OR (!$user_agreed)) OR ($user_agreed != $l['w_agree']) ) AND ($_SESSION['session_user_level'] < K_AUTH_EDIT_USER_LEVEL) ) {
			// The following are values for META TAGS
			// (leave void for default values)
			$thispage_author = ""; // name of page author
			$thispage_reply = ""; // email address
			$thispage_style = ""; // CSS page link
			$thispage_title = $pt['_title_agreement']; // page title
			$thispage_description = $pt['_description_agreement']; // page description
			$thispage_keywords = $pt['_keywords_agreement']; // page keywords
			require_once('../code/cp_page_header.'.CP_EXT);
			F_print_error(0, ""); //clear header messages
			F_display_reg_agreement();
		} else {
			if ((!isset($user_id) OR (!$user_id) OR ($user_id == 1)) AND ($_SESSION['session_user_level'] < K_AUTH_EDIT_USER_LEVEL)) {
				//registration
				$thispage_author = ""; // name of page author
				$thispage_reply = ""; // email address
				$thispage_style = ""; // CSS page link
				$thispage_title = $pt['_title_registration']; // page title
				$thispage_description = $pt['_description_registration']; // page description
				$thispage_keywords = $pt['_keywords_registration']; // page keywords
			} else {
				//edit
				$thispage_author = ""; // name of page author
				$thispage_reply = ""; // email address
				$thispage_style = ""; // CSS page link
				$thispage_title = $pt['_title']; // page title
				$thispage_description = $pt['_description']; // page description
				$thispage_keywords = $pt['_keywords']; // page keywords
			}
			require_once('../code/cp_page_header.'.CP_EXT);
			F_print_error(0, ""); //clear header messages
			if(!F_edit_user_data($user_id, false)) {
				F_print_error("WARNING", $l['m_authorization_deny']);
				F_logout_form();
			}
		}
		break;
	}
}//end of switch

require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
