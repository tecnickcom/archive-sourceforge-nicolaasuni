<?php
//============================================================+
// File name   : cp_show_ec_transactions.php                   
// Begin       : 2002-09-07                                    
// Last Update : 2005-06-29                                    
//                                                             
// Description : Display transacions                           
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

$pagelevel = K_AUTH_ADMIN_CP_SHOW_EC_TRANSACTIONS;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

//leave following variables void for default values
$thispage_title = $l['t_transactions'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages;

require_once('../code/cp_functions_ec_transactions.'.CP_EXT);
F_display_select_transaction_details();

require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
