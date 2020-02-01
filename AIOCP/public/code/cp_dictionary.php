<?php
/* ============================================================
 File name   : cp_dictionary.php
 Begin       : 2003-10-14 13:22:46
 Last update : 2003-10-14 13:22:46
 Author      : (2) admin
 Description : display dictionary
=============================================================== */

require_once('../../shared/config/cp_extension.inc');
require_once('../config/cp_config.'.CP_EXT);
$pagelevel = 0; // page level
require_once('../../shared/code/cp_authorization.'.CP_EXT); // check authorization permissions
require_once('../../shared/code/cp_functions_collect_stats.'.CP_EXT); // collect site statistics
$page_name = "dictionary_show"; // name of the page (used in DB)
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

// AIOCP standard module
// display dictionary with category selector
// Author: Nicola Asuni; (c) Copyright: Tecnick.com LTD
require_once('../../shared/code/cp_functions_dictionary.'.CP_EXT);
if (isset($_REQUEST['wid'])) {
	//display single news
	F_display_single_dictionary_word($_REQUEST['wid']);
}
else {
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
	F_show_select_dictionary_words($wherequery, $order_field, $orderdir, $firstrow, $rowsperpage);
}

require_once('../code/cp_page_footer.php'); // page footer
?>
