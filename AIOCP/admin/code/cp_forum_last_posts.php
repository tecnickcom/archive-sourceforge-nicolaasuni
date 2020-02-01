<?php
//============================================================+
// File name   : cp_forum_last_posts.php                       
// Begin       : 2002-03-21                                    
// Last Update : 2002-09-29                                    
//                                                             
// Description : show forum last posts                         
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

$pagelevel = K_AUTH_ADMIN_CP_FORUM_LAST_POSTS;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_forum_last_posts'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages; ?>
<!-- ====================================================== -->
<?php
require_once('../../shared/code/cp_functions_forum_last_posts.'.CP_EXT);
echo F_show_forum_last_posts($selected_language, "", "", 10, 40);
?>
<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
