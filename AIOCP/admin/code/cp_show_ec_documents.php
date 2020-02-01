<?php
//============================================================+
// File name   : cp_show_ec_documents.php                      
// Begin       : 2002-08-31                                    
// Last Update : 2002-09-29                                    
//                                                             
// Description : Display user commercial documents             
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

$pagelevel = K_AUTH_ADMIN_CP_SHOW_EC_DOCUMENTS;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

//leave following variables void for default values
$thispage_title = $l['t_documents'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages; ?>
<!-- ====================================================== -->
<?php
if (isset($_REQUEST['uid'])) {
	$user_id = $_REQUEST['uid'];
}
else {
	$user_id = $_SESSION['session_user_id'];
}

if ($_SESSION['session_user_id'] == $user_id) {
	
	//display message
	if (isset($_REQUEST['msg'])) {
		echo "<p>".$_REQUEST['msg']."</p>";
		echo "<p>".$l['m_order_processed']."</p>";
	}
	
	require_once('../../shared/code/cp_functions_ec_documents.'.CP_EXT);
	F_display_select_document_details($user_id);
}
?>
<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
