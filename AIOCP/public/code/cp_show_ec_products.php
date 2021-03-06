<?php
/* ============================================================
 File name   : cp_show_ec_products.php
 Begin       : 2002-08-21 11:34:56
 Last update : 2006-02-08
 Author      : (5) nick
 Description : Product's catalog
=============================================================== */

require_once('../../shared/config/cp_extension.inc');
require_once('../config/cp_config.'.CP_EXT);
$pagelevel = 0; // page level
require_once('../../shared/code/cp_authorization.'.CP_EXT); // check authorization permissions
require_once('../../shared/code/cp_functions_collect_stats.'.CP_EXT); // collect site statistics
$page_name = "show_ec_products"; // name of the page (used in DB)
$pt = F_load_page_templates($selected_language, $page_name); // load page templates for the current language

// The following are values for META TAGS
// (leave void for default values)
$thispage_author = "Nicola Asuni"; // name of page author
$thispage_reply = ""; // email address
$thispage_style = ""; // CSS page link
$thispage_title = $pt['_title']; // page title
$thispage_description = $pt['_description']; // page description
$thispage_keywords = $pt['_keywords']; // page keywords

require_once('../code/cp_page_header.php'); // page header
F_print_error(0, ""); //clear error and system messages

require_once('../../shared/code/cp_functions_ec_products.'.CP_EXT);
if (isset($_REQUEST['pid'])) {
	//display single product
	F_display_single_product($_REQUEST['pid']);
} else {
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
	//display product list
	F_show_select_products($wherequery, $order_field, $orderdir, $firstrow, $rowsperpage);
}

require_once('../code/cp_page_footer.php'); // page footer
?>
