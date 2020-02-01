<?php
//============================================================+
// File name   : cp_show_ec_shopping_cart.php                  
// Begin       : 2002-08-22                                    
// Last Update : 2002-09-29                                    
//                                                             
// Description : Display current user shopping cart            
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

$pagelevel = K_AUTH_ADMIN_CP_SHOW_EC_SHOPPING_CART;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_shopping_cart'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages; ?>
<!-- ====================================================== -->
<?php
if (isset($_REQUEST['npid'])) {
	$new_product_id = $_REQUEST['npid'];
}
else {
	$new_product_id = false;
}
require_once('../../shared/code/cp_functions_ec_shopping_cart.'.CP_EXT);
F_display_shopping_cart($PHPSESSID, $_SESSION['session_user_id'], $new_product_id);
?>
<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
