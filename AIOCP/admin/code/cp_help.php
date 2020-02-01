<?php
//============================================================+
// File name   : cp_help.php                                   
// Begin       : 2001-09-02                                    
// Last Update : 2002-09-29                                    
//                                                             
// Description : Display AIOCP Manual                          
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

include('../../shared/config/cp_extension.inc');
include('../config/cp_config.'.CP_EXT);

$pagelevel = K_AUTH_ADMIN_CP_HELP;
include('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_help'];
?>

<?php include('../code/cp_page_header.'.CP_EXT); ?>
<?php F_print_error(0, ""); //clear header messages; ?>
<!-- ====================================================== -->

<!-- ====================================================== -->
<?php include('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
