<?php
//============================================================+
// File name   : cp_search_forum.php                           
// Begin       : 2002-03-21                                    
// Last Update : 2008-07-06
//                                                             
// Description : Advanced Search for Forum                     
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

$pagelevel = K_AUTH_ADMIN_CP_SEARCH_FORUM;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_forum_search'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages; ?>
<!-- ====================================================== -->
<?php
require_once('../../shared/code/cp_functions_forum_search.'.CP_EXT);

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

F_search_forum($wherequery, $order_field, $orderdir, $firstrow, $rowsperpage);
?>
<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
