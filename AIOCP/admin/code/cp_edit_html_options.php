<?php
//============================================================+
// File name   : cp_edit_html_options.php                      
// Begin       : 2001-11-02                                    
// Last Update : 2002-09-29                                    
//                                                             
// Description : HTML Options Editor                           
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
require_once('../code/cp_functions_htmloptionseditor.'.CP_EXT);

$pagelevel = K_AUTH_ADMIN_CP_EDIT_HTML_OPTIONS;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_html_options_editor'];

require_once('../code/cp_page_header_popup.'.CP_EXT); 

F_html_options_editor($_REQUEST['tag_name']);

require_once('../code/cp_page_footer_popup.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
