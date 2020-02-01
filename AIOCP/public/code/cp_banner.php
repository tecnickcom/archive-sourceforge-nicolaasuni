<?php
//============================================================+
// File name   : cp_banner.php                                 
// Begin       : 2002-04-30                                    
// Last Update : 2002-09-29                                    
//                                                             
// Description : Open banner link and update statistics        
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
$pagelevel = 0; // page level
require_once('../../shared/code/cp_authorization.'.CP_EXT);
//require_once('../../shared/code/cp_functions_collect_stats.'.CP_EXT);

require_once('../../shared/code/cp_functions_banner.'.CP_EXT);
F_banner_click($banner, $page);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
