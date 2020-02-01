<?php
/* ============================================================
 File name   : cp_news_rss.php
 Begin       : 2003-08-04
 Last update : 2003-08-04
 Author      : (5) nick
 Description : Display News in RSS format
=============================================================== */

require_once('../../shared/config/cp_extension.inc');
require_once('../config/cp_config.'.CP_EXT);
$pagelevel = 0; // page level
require_once('../../shared/code/cp_authorization.'.CP_EXT); // check authorization permissions
require_once('../../shared/code/cp_functions_collect_stats.'.CP_EXT); // collect site statistics


if (isset($_REQUEST['news_category'])) {
	$news_category = $_REQUEST['news_category'];
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

require_once('../../shared/code/cp_functions_news.'.CP_EXT);
header("Content-type: text/xml"); //send xml header
echo F_show_news_RSS($news_category, $wherequery, $order_field, $orderdir); //display news in RSS format
?>
