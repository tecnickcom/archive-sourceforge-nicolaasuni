<?php
/* ============================================================
 File name   : cp_dictionary_rss.php
 Begin       : 2003-10-14
 Last update : 2003-10-14
 Author      : (5) nick
 Description : Display Dictionary in RSS format
=============================================================== */

require_once('../../shared/config/cp_extension.inc');
require_once('../config/cp_config.'.CP_EXT);
$pagelevel = 0; // page level
require_once('../../shared/code/cp_authorization.'.CP_EXT); // check authorization permissions
require_once('../../shared/code/cp_functions_collect_stats.'.CP_EXT); // collect site statistics


if (isset($_REQUEST['dicword_category_id'])) {
	$dicword_category_id = $_REQUEST['dicword_category_id'];
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

require_once('../../shared/code/cp_functions_dictionary.'.CP_EXT);
header("Content-type: text/xml"); //send xml header
echo F_show_dictionary_words_RSS($dicword_category_id, $wherequery, $order_field, $orderdir); //display news in RSS format
?>
