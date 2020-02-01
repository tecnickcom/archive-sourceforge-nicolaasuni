<?php
//============================================================+
// File name   : cp_functions_whois.php                        
// Begin       : 2001-10-02                                    
// Last Update : 2005-06-29                                    
//                                                             
// Description : do whois queries                              
//                                                             
//  This interface designed by Nicola Asuni are intendent      
//	to be used with:                                           
//	whois2.php base class to do whois queries with php         
//  � 1999,2000,2001 easyDNS Technologies Inc. & Mark Jeftovic 
//    Placed under the GPL.                                    
//	Homepage: http://www.easydns.com/~markjr/whois2/           
//============================================================+

// ------------------------------------------------------------
// Show WHOIS form
// ------------------------------------------------------------
function F_show_whois($whois_query) {
	global $l, $aiocp_dp;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	
	if(isset($whois_query) AND $whois_query) {
		require_once("../../shared/whois/main.whois");
		$whois = new Whois();
		if(isset($whois->Query['errstr'])) {
			echo $l['t_error']."<br />".implode($whois->Query['errstr'],"<br />");
		}
		else {
			$result = $whois->Lookup($whois_query);
			if(isset($whois->Query['errstr'])) {
				echo $l['t_error']."<br />".implode($whois->Query['errstr'],"<br />");
			}
			else {
				if(!empty($result['rawdata'])) {
					echo "<br />".implode($result['rawdata'],"<br />");
				}
				else {
					F_print_error("WARNING", $l['m_search_void']);
				}
			}
		}
	}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_whois" id="form_whois">
<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<tr class="fillO">
<td class="fillOO" colspan="2">
<small><?php echo $l['d_whois']; ?></small>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_domain', 'h_whois_domain'); ?></b></td>
<td class="fillEE"><input type="text" name="whois_query" id="whois_query" value="<?php echo $whois_query; ?>" size="20" maxlength="255" /> <?php F_submit_button("form_whois","menu_mode",$l['w_whois']); ?><input type="hidden" name="menu_mode" id="menu_mode" value="" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" colspan="2" align="center">
<!-- copyright notice -->
<a href="http://www.easydns.com/~markjr/whois2/" target="_blank"><img src="../../shared/whois/whois2-icon.gif" width="88" height="31" border="0" alt="Powered by whois2.php" /></a>
</td>
</tr>

</table>

</td>
</tr>
</table>
</form>
<!-- ====================================================== -->
<?php
} //end of function

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
