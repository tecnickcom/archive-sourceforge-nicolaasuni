<?php
//============================================================+
// File name   : cp_show_news.php                              
// Begin       : 2001-09-19                                    
// Last Update : 2008-07-06
//                                                             
// Description : News Preview                                  
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

$pagelevel = K_AUTH_ADMIN_CP_SHOW_NEWS;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_news_show'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages; ?>
<!-- ====================================================== -->
<?php
require_once('../../shared/code/cp_functions_news.'.CP_EXT);
if (isset($_REQUEST['nid'])) {
	//display single product
	F_display_single_news($_REQUEST['nid']);
} else {
	if (isset($_REQUEST['selectednews'])) {
		$selectednews = $_REQUEST['selectednews'];
	} else {
		$selectednews = 0;
	}
	if (isset($_REQUEST['viewmode'])) {
		$viewmode = intval($_REQUEST['viewmode']);
	} else {
		$viewmode = 0;
	}
	if (isset($_REQUEST['wherequery'])) {
		$wherequery = $_REQUEST['wherequery'];
	} else {
		$wherequery = "";
	}
	if (isset($_REQUEST['order_field'])) {
		$order_field = $_REQUEST['order_field'];
	} else {
		$order_field = "";
	}
	if (isset($_REQUEST['orderdir'])) {
		$orderdir = $_REQUEST['orderdir'];
	} else {
		$orderdir = "";
	}
	if (isset($_REQUEST['firstrow'])) {
		$firstrow = $_REQUEST['firstrow'];
	} else {
		$firstrow = "";
	}
	if (isset($_REQUEST['rowsperpage'])) {
		$rowsperpage = $_REQUEST['rowsperpage'];
	} else {
		$rowsperpage = "";
	}
	F_show_select_news($wherequery, $order_field, $orderdir, $firstrow, $rowsperpage);
}
?>
<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
