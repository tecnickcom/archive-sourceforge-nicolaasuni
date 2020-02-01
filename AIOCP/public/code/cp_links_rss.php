<?php
/* ============================================================
 File name   : cp_links_rss.php
 Begin       : 2003-08-04
 Last update : 2003-08-04
 Author      : (5) nick
 Description : display a list of links in RSS format
=============================================================== */

require_once('../../shared/config/cp_extension.inc');
require_once('../config/cp_config.'.CP_EXT);
$pagelevel = 0; // page level
require_once('../../shared/code/cp_authorization.'.CP_EXT); // check authorization permissions
require_once('../../shared/code/cp_functions_collect_stats.'.CP_EXT); // collect site statistics

if (isset($_REQUEST['links_category'])) {
	$links_category = $_REQUEST['links_category'];
}
else {
	$news_category = FALSE;	
}

if (isset($_REQUEST['wherequery'])) {
	$wherequery = stripslashes($_REQUEST['wherequery']);
}
else {
	$wherequery = FALSE;	
}

if (isset($_REQUEST['order_field'])) {
	$order_field = stripslashes($_REQUEST['order_field']);
}
else {
	$order_field = FALSE;	
}

if (isset($_REQUEST['orderdir'])) {
	$orderdir = $_REQUEST['orderdir'];
}
else {
	$orderdir = FALSE;	
}

require_once('../../shared/code/cp_functions_links.'.CP_EXT);
header("Content-type: text/xml"); //send xml header
echo F_show_links_RSS($links_category, $wherequery, $order_field, $orderdir); //display links in RSS format
?>
