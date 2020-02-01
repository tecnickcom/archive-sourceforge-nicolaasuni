<?php
//============================================================+
// File name   : cp_layout_menu.php                            
// Begin       : 2001-09-02                                    
// Last Update : 2005-06-27                                    
//                                                             
// Description : System page frame CPMENU                      
//               Left side menus                               
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

$pagelevel = K_AUTH_ADMIN_CP_LAYOUT_MENU;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

//send XHTML headers
echo "<"."?xml version=\"1.0\" encoding=\"".$l['a_meta_charset']."\"?".">\n";
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"DTD/xhtml1-transitional.dtd\">\n";
echo "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"".$l['a_meta_language']."\" lang=\"".$l['a_meta_language']."\" dir=\"".$l['a_meta_dir']."\">\n";
?>

<head>
	<title><?php echo $l['t_menu']; ?> - <?php echo K_AIOCP_TITLE; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $l['a_meta_charset']; ?>" />
	<meta name="aiocp_level" content="<?php echo $pagelevel; ?>" />
	<meta name="description" content="<?php echo $l['t_menu']; ?> - <?php echo K_AIOCP_DESCRIPTION; ?>" />
	<meta name="Author"      content="<?php echo K_AIOCP_AUTHOR; ?>" />
	<meta name="Reply-to"    content="<?php echo K_AIOCP_REPLY_TO; ?>" />
	<meta name="keywords"    content="<?php echo $l['t_menu']; ?>,<?php echo K_AIOCP_KEYWORDS; ?>" />
	<link rel="stylesheet" href="<?php echo htmlentities(urldecode(K_PATH_STYLE_SHEETS."aiocp_menu.css")); ?>" type="text/css" />
	<script language="JavaScript" type="text/javascript">
	//<![CDATA[
	if(window.name != "CPMENU") {
		document.write("<meta http-equiv='refresh' CONTENT='0;url=index.php' />");
	}
	//]]>
	</script>
</head>

<body>

<script language="JavaScript" src="<?php echo K_PATH_SHARED_JSCRIPTS; ?>swap_images.js" type="text/javascript"></script>

<a href="cp_layout_main.<?php echo CP_EXT; ?>" target="<?php echo K_MAIN_FRAME_NAME; ?>"><img name="aiocp" src="<?php echo K_PATH_IMAGES; ?>tecnickcom/aiocp_36x36.png" width="36" height="36" border="0" alt="AIOCP (All In One Control Panel)" /></a>

<!-- show EXIT and HELP buttons -->

<a href="cp_logout.<?php echo CP_EXT; ?>" target="CPMAIN" onmouseover="hilite('exit','<?php echo K_PATH_IMAGES; ?>mainbuttons/cp/over/exit.gif')" onclick="hilite('exit','<?php echo K_PATH_IMAGES; ?>mainbuttons/cp/on/exit.gif')" onmouseout="hilite('exit','<?php echo K_PATH_IMAGES; ?>mainbuttons/cp/off/exit.gif')"><img name="exit" src="<?php echo K_PATH_IMAGES; ?>mainbuttons/cp/off/exit.gif" width="30" height="25" border="0" alt="<?php echo $l['w_logout']; ?>" /></a>
<a href="javascript:void(0)" target="CPMAIN" onmouseover="hilite('help','<?php echo K_PATH_IMAGES; ?>mainbuttons/cp/over/help.gif')" onclick="getHelpPage();" onmouseout="hilite('help','<?php echo K_PATH_IMAGES; ?>mainbuttons/cp/off/help.gif')" ><img name="help" src="<?php echo K_PATH_IMAGES; ?>mainbuttons/cp/off/help.gif" width="30" height="25" border="0" alt="<?php echo $l['w_help']; ?>" /></a>
<br />

<!-- display digital clock (UTC or GMT) xxx -->
<applet
	codebase="../../shared/java/" 
	archive="jadc.jar" 
	code="com.tecnick.jadc.Jadc.class" 
	name="jadc"
	id="jadc"
	alt="UTC clock"
	width="116"
	height="11"
	hspace="0"
	vspace="0"
	align="top"
>
<param name="counter_mode" value="false" />
<param name="local_time" value="false" />
<param name="timezone_hours" value="0" />
<param name="timezone_minutes" value="0" />
<param name="display_pattern" value="yyyy-MM-dd HH:mm:ss" />
<param name="background_color" value="585858" />
<param name="background_image" value="" />
<param name="img_0" value="../../images/digits/small_6x9/white/0.gif" />
<param name="img_1" value="../../images/digits/small_6x9/white/1.gif" />
<param name="img_2" value="../../images/digits/small_6x9/white/2.gif" />
<param name="img_3" value="../../images/digits/small_6x9/white/3.gif" />
<param name="img_4" value="../../images/digits/small_6x9/white/4.gif" />
<param name="img_5" value="../../images/digits/small_6x9/white/5.gif" />
<param name="img_6" value="../../images/digits/small_6x9/white/6.gif" />
<param name="img_7" value="../../images/digits/small_6x9/white/7.gif" />
<param name="img_8" value="../../images/digits/small_6x9/white/8.gif" />
<param name="img_9" value="../../images/digits/small_6x9/white/9.gif" />
<param name="img_sep" value="../../images/digits/small_6x9/white/sep.gif" />
<param name="img_dec" value="../../images/digits/small_6x9/white/dec.gif" />
<param name="img_blk" value="../../images/digits/small_6x9/white/blk.gif" />
<param name="img_pos" value="../../images/digits/small_6x9/white/pos.gif" />
<param name="img_neg" value="../../images/digits/small_6x9/white/neg.gif" />
</applet>


<!-- ====================================================== -->
<?php // Display the AIOCP tree menu
require_once('../../shared/code/cp_functions_menu_client_show.'.CP_EXT);
echo F_show_client_menu(0, false, 2);
?>

<?php 
require_once('../../shared/code/cp_functions_language.'.CP_EXT);
F_choose_language();
?>

<br />

<script language="JavaScript" type="text/javascript">
//<![CDATA[\n";
// open help window popup
function getHelpPage() {
	page_name = escape(window.parent.frames['CPMAIN'].location.href);
	help_window = window.open('cp_show_page_help.<?php echo CP_EXT; ?>?hp='+page_name, 'help_window', 'dependent,height=400,width=600,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no');
}
//]]>
</script>

<!-- ====================================================== -->
</body>
</html>

<?php
//============================================================+
// END OF FILE                                                 
//============================================================+
?>