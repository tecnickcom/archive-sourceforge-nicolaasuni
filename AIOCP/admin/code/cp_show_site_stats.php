<?php
//============================================================+
// File name   : cp_show_site_stats.php                        
// Begin       : 2002-05-16                                    
// Last Update : 2005-06-29                                    
//                                                             
// Description : Display Site Stats                            
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

$pagelevel = K_AUTH_ADMIN_CP_SHOW_SITE_STATS;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_site_stats'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages; 

require_once('../code/cp_functions_site_stats.'.CP_EXT);
F_show_select_site_stats("");

require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
