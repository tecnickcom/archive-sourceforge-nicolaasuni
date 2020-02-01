<?php
//============================================================+
// File name   : cp_select_country.php                         
// Begin       : 2001-09-09                                    
// Last Update : 2005-06-29                                    
//                                                             
// Description : Show all country in K_TABLE_COUNTRIES table.  
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

$pagelevel = K_AUTH_ADMIN_CP_SELECT_COUNTRY;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_country_select'];

require_once('../code/cp_page_header_popup.'.CP_EXT);

require_once('../../shared/code/cp_functions_countries.'.CP_EXT);
F_select_country($formname, $idfield, $fieldtype, $fsubmit); 

require_once('../code/cp_page_footer_popup.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
