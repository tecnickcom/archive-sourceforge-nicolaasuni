<?php
//============================================================+
// File name   : cp_user_verification.php                      
// Begin       : 2002-02-14                                    
// Last Update : 2002-09-29                                    
//                                                             
// Description : verification page for user registration       
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

$pagelevel = 0;
require_once('../../shared/code/cp_authorization.'.CP_EXT);
require_once('../../shared/code/cp_functions_collect_stats.'.CP_EXT);

$thispage_title = $l['t_user_verification'];
//leave following variables void for default values
$thispage_description = "";
$thispage_author = "";
$thispage_reply = "";
$thispage_keywords = "";
$thispage_style = "";

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages; ?>
<!-- ====================================================== -->
<?php
require_once('../../shared/code/cp_functions_user_edit.'.CP_EXT);
F_verify_user($a, $b, $c);
?>
<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
