<?php
//============================================================+
// File name   : cp_check_links.php                            
// Begin       : 2001-09-25                                    
// Last Update : 2003-10-26                                    
//                                                             
// Description : find and delete broken links from database    
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
require_once('../code/cp_functions_checklinks.'.CP_EXT);

$pagelevel = K_AUTH_ADMIN_CP_CHECK_LINKS;
require_once('../../shared/code/cp_authorization.'.CP_EXT);


//initialize menu command
if (isset($_REQUEST["menu_mode"])) {
	$menu_mode = $_REQUEST["menu_mode"];
}
else {
	$menu_mode = "";
}


require_once('../../shared/code/cp_functions_form.'.CP_EXT);

$progress_log = "../log/cp_links.log";	//log file

$thispage_title = $l['t_links_check'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages;

switch($menu_mode) {

	case unhtmlentities($l['w_check']):
	case $l['w_check']:{ // Backup site
		F_print_error("MESSAGE", $l['m_linkcheck_wait']);
		
		//open log popup display to show process progress
		@unlink($progress_log); //clear progress log file if exist
		error_log("--- START LOG: ".gmdate("Y-m-d H:i:s")." ---\n", 3, $progress_log); //create progress log file
		echo "\n<script language=\"JavaScript\" type=\"text/javascript\">\n";
		echo "//<![CDATA[\n";
		echo "logview=window.open('cp_show_progress.".CP_EXT."?log=".$progress_log."','logview','dependent,height=280,width=400,menubar=no,resizable=yes,scrollbars=no,status=no,toolbar=no');\n";
		echo "//]]>\n";
		echo "</script>\n";
		
		break;
	}
	
	case "startlongprocess":{ // start backup
		F_check_links();
		error_log("--- END LOG: ".gmdate("Y-m-d H:i:s")." ---\n", 3, $progress_log); //create progress log file
		F_print_error("MESSAGE", $l['m_linkcheck_end']);
		break;
	}
	
}
?>
<!-- ====================================================== -->	
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_linkcheck" id="form_linkcheck">

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">
<tr class="fillO">
<td class="fillOO">
<?php echo $l['m_link_check']; ?>
</td>
</tr>
</table>

</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
F_submit_button("form_linkcheck","menu_mode",$l['w_check']); 
?>
</td>
</tr>
</table>
</form>
<!-- ====================================================== -->
<?php //recall backup page to start backup
if (($menu_mode == $l['w_check']) OR ($menu_mode == unhtmlentities($l['w_check'])) ) {
	echo "<script language=\"JavaScript\" type=\"text/javascript\">";
	echo "//<![CDATA[\n";
	echo "document.form_linkcheck.menu_mode.value='startlongprocess';";
	echo "document.form_linkcheck.submit();";
	echo "//]]>\n";
	echo "</script>";
}
?>
<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>