<?php
//============================================================+
// File name   : cp_select_icons_client.php                    
// Begin       : 2002-03-21                                    
// Last Update : 2003-01-23                                    
//                                                             
// Description : Show all client icons                         
//               Allow to select an item for edit.             
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

$pagelevel = K_AUTH_ADMIN_CP_SELECT_ICONS_CLIENT;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_icon_client_select'];

require_once('../code/cp_page_header_popup.'.CP_EXT);

require_once('../code/cp_functions_icons.'.CP_EXT);
F_select_icons(K_TABLE_ICONS_CLIENT, K_PATH_IMAGES_ICONS_CLIENT, $formname, $idfield, $fieldtype, $fsubmit);

require_once('../code/cp_page_footer_popup.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
