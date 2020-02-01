<?php
//============================================================+
// File name   : cp_functions_traceroute.php                   
// Begin       : 2001-10-02                                    
// Last Update : 2002-12-31                                    
//                                                             
// Description : execute traceroute                            
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

// ------------------------------------------------------------
// Show WHOIS form
// ------------------------------------------------------------
function F_show_traceroute($iphosttotrace) {
	global $l, $aiocp_dp;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	
	if(isset($iphosttotrace)) {
		$shellcmd = K_PATH_TRACEROUTE." ".escapeshellcmd($iphosttotrace);
		echo "<p><b>".$l['d_output_cmd'].":</b> ".$shellcmd."</p>";
		echo "<pre class=\"shell\">";
		echo system($shellcmd);
		echo "\n\n</pre>";
		echo "<p><a href=\"".htmlentities(urldecode($_SERVER['SCRIPT_NAME']))."\"><b>&lt;&lt; ".$l['d_reload_page']." &gt;&gt;</b></a></p>";
	}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_traceroute" id="form_traceroute">
<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_domain', 'h_traceroute_domain'); ?></b></td>
<td class="fillOE"><input type="text" name="iphosttotrace" id="iphosttotrace" value="<?php echo $iphosttotrace; ?>" size="20" maxlength="255" /> 
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
</td>
</tr>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<?php F_submit_button("form_traceroute","menu_mode",$l['w_traceroute']); ?>
</td>
</tr>
</table>
</form>
<?php
} //end of function

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
