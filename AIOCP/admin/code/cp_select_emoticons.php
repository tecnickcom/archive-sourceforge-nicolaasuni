<?php
//============================================================+
// File name   : cp_select_emoticons.php                       
// Begin       : 2001-09-09                                    
// Last Update : 2002-11-17                                    
//                                                             
// Description : Show all smiles in K_TABLE_EMOTICONS table.   
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
// passed values:
// $fieldtype: 1=select form field; 0=input form field
// $formname name of the calling form
// $idfield name of the field that contain smile_id
// $fsubmit 1=submit calling form after selection

require_once('../../shared/config/cp_extension.inc');
require_once('../config/cp_config.'.CP_EXT);
require_once('../../shared/code/cp_functions_emoticons.'.CP_EXT);

$pagelevel = K_AUTH_ADMIN_CP_SELECT_EMOTICONS;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_emoticon_select'];

require_once('../code/cp_page_header_popup.'.CP_EXT);

F_select_smiles($formname, $idfield, $fieldtype, $fsubmit);

require_once('../code/cp_page_footer_popup.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
