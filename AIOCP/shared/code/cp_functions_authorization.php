<?php
//============================================================+
// File name   : cp_functions_authorization.php                
// Begin       : 2001-09-26                                    
// Last Update : 2004-02-23                                    
//                                                             
// Description : Functions for Authorization                   
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
// display small login form
// ------------------------------------------------------------
function F_small_login_form() {
	global $l, $aiocp_dp, $thispage_title;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	
	
	if ( ($_SESSION['session_user_level']<1) AND ($thispage_title != $l['t_login_form']) ) {
		echo "<form action=\"".$_SERVER['SCRIPT_NAME']."";
		if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {
			echo "?aiocp_dp=".$aiocp_dp."";
		}
		echo "\" method=\"post\" enctype=\"multipart/form-data\" name=\"form_small_login\" id=\"form_small_login\">\n";
		echo "<table class=\"fill\" border=\"0\" cellspacing=\"2\" cellpadding=\"1\">\n";
		echo "<tr class=\"fillO\">\n";
		echo "<td class=\"fillEO\" align=\"right\">\n";
		echo F_display_field_name('w_name', 'h_login_name');
		echo ":<input type=\"text\" name=\"xuser_name\" id=\"xuser_name\" size=\"10\" maxlength=\"255\" />\n";
		echo F_display_field_name('w_password', 'h_password');
		echo ":<input type=\"password\" name=\"xuser_password\" id=\"xuser_password\" size=\"10\" maxlength=\"255\" />\n";
		echo "<input type=\"hidden\" name=\"logaction\" id=\"logaction\" value=\"\" />\n";
		echo "<input type=\"button\" name=\"login\" id=\"login\" value=\"".$l['w_login']."\"";
		echo " onclick=\"document.form_small_login.logaction.value='login'; document.form_small_login.submit()\" />\n";
	//F_generic_submit_button("form_small_login","login",$l['w_login'],"document.form_small_login.logaction.value='login'");
		echo "<a href=\"cp_edit_user.".CP_EXT."\">[".$l['w_register']."]</a>\n";
		echo "</td></tr></table>\n";
		echo "</form>\n";
	}
}

// ------------------------------------------------------------
// display login form
// ------------------------------------------------------------
function F_login_form() {
	global $l, $selected_language, $aiocp_dp, $thispage_title;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	if ((isset($_REQUEST['logaction'])) AND ($_REQUEST['logaction']=="register") ) {
		echo "<"."?xml version=\"1.0\" encoding=\"".$l['a_meta_charset']."\"?".">\n";
		echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"DTD/xhtml1-transitional.dtd\">\n";
		echo "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"".$l['a_meta_language']."\" lang=\"".$l['a_meta_language']."\" dir=\"".$l['a_meta_dir']."\">\n";
		echo "<head>\n";
		echo "<title>ENTER</title>\n";
		echo "<meta http-equiv=\"Refresh\" content=\"0;url=cp_edit_user.".CP_EXT."\" />";
		echo "<meta name=\"robots\" content=\"index,follow\" />\n";
		echo "</head>\n";
		echo "<body>\n";
		echo "<a href=\"cp_edit_user.".CP_EXT."\">ENTER</a>\n";
		echo "</body>\n";
		echo "</html>\n";
		exit;
	}
	
	$thispage_title = $l['t_login_form'];
	
	require_once('../code/cp_page_header.'.CP_EXT);
	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	if ((isset($_REQUEST['logaction'])) AND ($_REQUEST['logaction']=="lostpassword") ) {
		if (isset($_REQUEST['xuser_name']) AND strlen($_REQUEST['xuser_name'])>0) {
			require_once('../../shared/code/cp_functions_user_edit.'.CP_EXT);
			if(!F_lost_password($_REQUEST['xuser_name'])) {
				F_print_error("WARNING", $l['m_unknow_user'].": ".$_REQUEST['xuser_name']);
			}
		}
		else {
			F_print_error("WARNING", $l['m_form_missing_fields'].": ".$l['w_name']);
		}
	}
?>
<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_login" id="form_login">

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_name', 'h_login_name'); ?></b></td>
<td class="fillOE"><input type="text" name="xuser_name" id="xuser_name" size="20" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_password', 'h_password'); ?></b></td>
<td class="fillEE"><input type="password" name="xuser_password" id="xuser_password" size="20" maxlength="255" /></td>
</tr>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="logaction" id="logaction" value="" />
<?php 
F_generic_submit_button("form_login","login",$l['w_login'],"document.form_login.logaction.value='login'");
F_generic_submit_button("form_login","register",$l['w_register'],"document.form_login.logaction.value='register'");
F_generic_submit_button("form_login","lostpassword",$l['w_lost_password'],"document.form_login.logaction.value='lostpassword'");
?>
</td>

</tr>
</table>
</form>
<!-- ====================================================== -->
<?php 
	require_once('../code/cp_page_footer.'.CP_EXT);
	//--- END login form ---------------------------------------------
	exit();
}

// ------------------------------------------------------------
// display logout form
// ------------------------------------------------------------
function F_logout_form() {
	global $l;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
?>
<!-- ====================================================== -->
<form action="../code/cp_logout.<?php echo CP_EXT; ?>" method="post" enctype="multipart/form-data" name="form_logout" id="form_logout">
<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<tr class="fillO">
<td class="fillOE" align="center" valign="middle">
<p><?php echo $l['d_logout_desc'] ?></p>
</td>
</tr>
</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="current_page" id="current_page" value="<?php echo $_SERVER['SCRIPT_NAME']; ?>" />
<input type="hidden" name="logaction" id="logaction" value="" />
<?php F_generic_submit_button("form_logout","login",$l['w_logout'],"document.form_logout.logaction.value=''")?>
</td>
</tr>
</table>
</form>
<!-- ====================================================== -->
<?php 
}

// ------------------------------------------------------------
// display logout form
// ------------------------------------------------------------
function F_logout_page() {
	global $l, $selected_language, $thispage_title;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	$thispage_title = $l['t_logout_form'];
	require_once('../code/cp_page_header.'.CP_EXT);
	
	F_logout_form();
	
	require_once('../code/cp_page_footer.'.CP_EXT);
	exit();
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
