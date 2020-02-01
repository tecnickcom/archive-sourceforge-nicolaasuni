<?php
//============================================================+
// File name   : cp_show_ec_warranty.php                       
// Begin       : 2002-07-10                                    
// Last Update : 2002-09-29                                    
//                                                             
// Description : Display requested warranty certificate        
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

require_once('../../shared/config/cp_extension.inc');
require_once('../config/cp_config.'.CP_EXT);

$pagelevel = 0;
require_once('../../shared/code/cp_authorization.'.CP_EXT);
require_once('../../shared/code/cp_functions_collect_stats.'.CP_EXT);

//leave following variables void for default values
$thispage_title = $l['t_warranty'];
$thispage_description = "";
$thispage_author = "";
$thispage_reply = "";
$thispage_keywords = "";
$thispage_style = "";

require_once('../code/cp_page_header_popup.'.CP_EXT);

require_once('../../shared/code/cp_functions_warranty.'.CP_EXT);
if(isset($_REQUEST['warranty_id'])) {
	echo F_show_warranty($_REQUEST['warranty_id']);
}
?>

<hr />

<form action="">
<div align="center">
<?php
require_once('../../shared/code/cp_functions_form.'.CP_EXT);
F_generic_button("close",$l['w_close'],"window.close()");  
?>
</div>
</form>

<!-- ====================================================== -->
<?php 
require_once('../code/cp_page_footer_popup.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
