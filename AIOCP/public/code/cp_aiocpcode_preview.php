<?php
//============================================================+
// File name   : cp_aiocpcode_preview.php                      
// Begin       : 2002-01-30                                    
// Last Update : 2002-09-29                                    
//                                                             
// Description : show aiocp code preview                       
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

$thispage_title = $l['t_aiocpcode_preview'];

require_once('../code/cp_page_header_popup.'.CP_EXT);

require_once('../../shared/code/cp_functions_aiocpcode.'.CP_EXT);
require_once('../../shared/code/cp_functions_form.'.CP_EXT);
?>

<?php echo F_decode_aiocp_code(stripslashes($aiocpcode)); ?>

<hr />

<form action="">
<div align="center">
<?php F_generic_button("close",$l['w_close'],"window.close()"); ?>
</div>
</form>

<!-- ====================================================== -->
<?php 
require_once('../code/cp_page_footer_popup.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
