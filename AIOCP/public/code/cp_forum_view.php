<?php
//============================================================+
// File name   : cp_forum_view.php                             
// Begin       : 2002-01-31                                    
// Last Update : 2008-07-06
//                                                             
// Description : Show categories and forums                    
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

$pagelevel = 0;
require_once('../../shared/code/cp_authorization.'.CP_EXT);
require_once('../../shared/code/cp_functions_collect_stats.'.CP_EXT);

$thispage_title = $l['t_forum'];
//leave following variables void for default values
$thispage_description = "";
$thispage_author = "";
$thispage_reply = "";
$thispage_keywords = "";
$thispage_style = "";

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages;

require_once('../../shared/code/cp_functions_forum_views.'.CP_EXT);

if (!isset($catid)) {
	$catid = "";
}
if (isset($_REQUEST["fmode"])) {
	$fmode = $_REQUEST["fmode"];
} else {
	$fmode = "";
}
switch($fmode) {
	case "top": { // topic
		if(!F_show_topic($topid, $forid, $catid, $order_field, $orderdir, $firstrow, K_MAX_ROWS_PER_PAGE)) {
			F_print_error("WARNING", $l['m_authorization_deny']);
			F_logout_form();
		}
		break;
	}
	case "for": { // forum
		if(!F_show_forum($forid, $catid, $order_field, $orderdir, $firstrow, K_MAX_ROWS_PER_PAGE)) {
			F_print_error("WARNING", $l['m_authorization_deny']);
			F_logout_form();
		}
		break;
	}
	case "cat":
	default : {
		if(!F_show_categories($catid)) {
			F_print_error("WARNING", $l['m_authorization_deny']);
			F_logout_form();
		}
		break;
	}
}//end of switch
?>
<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT); 

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
