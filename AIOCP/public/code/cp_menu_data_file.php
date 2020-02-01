<?php
/* ============================================================
 File name   : cp_menu_data_file.php
 Begin       : 2004-05-03
 Last update : 2007-01-11
 Author      : (5) nick
 Description : create a dynamic data file for apples menus
=============================================================== */
require_once('../../shared/config/cp_extension.inc');
require_once('../config/cp_config.'.CP_EXT);
$pagelevel = 0; // page level
require_once('../../shared/code/cp_authorization.'.CP_EXT); // check authorization permissions
require_once('../../shared/code/cp_functions_menu_data.'.CP_EXT); // collect site statistics
if (!isset($_REQUEST['page'])) {
	$_REQUEST['page'] = "";
}
echo F_show_menu_data($_REQUEST['menu'], $_REQUEST['page']);
?>