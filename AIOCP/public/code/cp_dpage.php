<?php
//============================================================+
// File name   : cp_dpage.php
// Begin       : 2002-04-21
// Last Update : 2007-06-06
// 
// Description : display dinamic pages (from database)
//               @param $aiocp_dp the page name
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

$pagelevel = 0; // page level
require_once('../../shared/code/cp_authorization.'.CP_EXT); // check authorization permissions
require_once('../../shared/code/cp_functions_collect_stats.'.CP_EXT); // collect site statistics

require_once('../../shared/code/cp_functions_dynamic_pages.'.CP_EXT); //functions for display selected page

$aiocp_dp = $_REQUEST['aiocp_dp'];
$aiocp_dp = substr($aiocp_dp,0,80);
$aiocp_dp = preg_replace("/[^0-9 A-Za-z_-]+/i", "", $aiocp_dp);

echo F_show_dynamic_page($aiocp_dp); //display selected page

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
