<?php
//============================================================+
// File name   : cp_ping.php                                   
// Begin       : 2001-10-03                                    
// Last Update : 2008-07-06
//                                                             
// Description : execute ping                                  
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
require_once('../../shared/code/cp_functions_form.'.CP_EXT);

$pagelevel = K_AUTH_ADMIN_CP_PING;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_ping'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages; ?>
<!-- ====================================================== -->
<?php
if(isset($iphosttoping)) {
	$shellcmd = "ping ".escapeshellcmd($iphosttoping);
	echo "<p><b>".$l['d_output_cmd'].":</b> ".$shellcmd."</p>";
	echo "<pre class=\"shell\">";
	echo system($shellcmd);
	echo "\n\n</pre>";
	echo "<p><a href=\"".htmlentities(urldecode($_SERVER['SCRIPT_NAME']))."\"><b>&lt;&lt; ".$l['d_reload_page']." &gt;&gt;</b></a></p>";
} else {
	$iphosttoping = "";
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_ping" id="form_ping">
<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_domain', 'h_ip_domain'); ?></b></td>
<td class="fillOE"><input type="text" name="iphosttoping" id="iphosttoping" value="<?php echo $iphosttoping; ?>" size="20" maxlength="255" />
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
</td>
</tr>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<?php F_submit_button("form_ping","menu_mode",$l['w_ping']); ?>
</td>
</tr>
</table>
</form>
<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
