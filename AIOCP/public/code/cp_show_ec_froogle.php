<?php
/* ============================================================
 File name   : cp_show_ec_froogle.php
 Begin       : 2003-11-01
 Last update : 2003-11-01
 Author      : (5) nick
 Description : Display Products in Froogle format
=============================================================== */

require_once('../../shared/config/cp_extension.inc');
require_once('../config/cp_config.'.CP_EXT);
$pagelevel = 0; // page level
require_once('../../shared/code/cp_authorization.'.CP_EXT); // check authorization permissions
require_once('../../shared/code/cp_functions_collect_stats.'.CP_EXT); // collect site statistics


if (isset($_REQUEST['product_category_id'])) {
	$product_category_id = $_REQUEST['product_category_id'];
}
else {
	$product_category_id = FALSE;	
}

if (isset($_REQUEST['product_manufacturer_id'])) {
	$product_manufacturer_id = $_REQUEST['product_manufacturer_id'];
}
else {
	$product_manufacturer_id = FALSE;	
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

require_once('../../shared/code/cp_functions_ec_products.'.CP_EXT);

$filename = "products_".gmdate("YmdHis").".txt";
header("Content-Disposition: inline; filename=".$filename."", TRUE);
header("Content-Type: text/plain; filename=\"".$filename."\"", TRUE); //text/plain open on browser - text/text open on default application

echo F_show_products_froogle($product_category_id, $product_manufacturer_id, $wherequery, $order_field, $orderdir)
?>
