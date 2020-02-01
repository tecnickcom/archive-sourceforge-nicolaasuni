<?php
//============================================================+
// File name   : cp_edit_user.php                              
// Begin       : 2002-02-08                                    
// Last Update : 2009-03-05
//                                                             
// Description : Edit user data                                
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
require_once('../../shared/code/cp_functions_user_edit.'.CP_EXT);

$pagelevel = K_AUTH_ADMIN_CP_EDIT_USER;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

if (!isset($_REQUEST['user_id'])) {
	$user_id = '';
}
if (isset($_REQUEST['uemode'])) {
	$uemode = $_REQUEST['uemode'];
} else {
	$uemode = '';
}
switch($uemode) {
	case 'company': { // edit user company
		$thispage_title = $l['t_company_editor'];
		require_once('../code/cp_page_header.'.CP_EXT);
		F_print_error(0, ''); //clear header messages
		if(!F_edit_user_company($user_id)) {
			F_print_error('WARNING', $l['m_authorization_deny']);
			F_logout_form();
		}
		break;
	}
	case 'phone': { // edit user phone numbers
		$thispage_title = $l['t_phone_editor'];
		require_once('../code/cp_page_header.'.CP_EXT);
		F_print_error(0, ''); //clear header messages
		if(!F_edit_user_phone($user_id)) {
			F_print_error('WARNING', $l['m_authorization_deny']);
			F_logout_form();
		}
		break;
	}
	case 'address': { // edit user postal ddresses
		$thispage_title = $l['t_address_editor'];
		require_once('../code/cp_page_header.'.CP_EXT);
		F_print_error(0, ''); //clear header messages
		if(!F_edit_user_address($user_id)) {
			F_print_error('WARNING', $l['m_authorization_deny']);
			F_logout_form();
		}
		break;
	}
	case 'internet': { // edit user internet data
		$thispage_title = $l['t_internet_editor'];
		require_once('../code/cp_page_header.'.CP_EXT);
		F_print_error(0, ''); //clear header messages
		if(!F_edit_user_internet($user_id)) {
			F_print_error('WARNING', $l['m_authorization_deny']);
			F_logout_form();
		}
		break;
	}
	case 'user': 
	default: { // edit user data
		$thispage_title = $l['t_user_editor'];
		require_once('../code/cp_page_header.'.CP_EXT);
		F_print_error(0, ''); //clear header messages
		if(!F_edit_user_data($user_id,true)) {
			F_print_error('WARNING', $l['m_authorization_deny']);
			F_logout_form();
		}
		break;
	}
}//end of switch

require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
