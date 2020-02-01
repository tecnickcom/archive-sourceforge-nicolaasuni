<?php
//============================================================+
// File name   : cp_show_ec_rga.php                            
// Begin       : 2002-10-19                                    
// Last Update : 2002-10-19                                    
//                                                             
// Description : Display RGA Module                            
//               Return Goods Authorization                    
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

$pagelevel = 1;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

//leave following variables void for default values
$thispage_title = $l['t_return_goods_authorization'];
$thispage_description = "";
$thispage_author = "";
$thispage_reply = "";
$thispage_keywords = "";
$thispage_style = "";

require_once('../code/cp_page_header.'.CP_EXT);

require_once('../../shared/code/cp_functions_ec_rga.'.CP_EXT);
F_display_select_rga_invoice($_SESSION['session_user_id']);
?>
<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
