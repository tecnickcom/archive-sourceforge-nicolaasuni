<?php
//============================================================+
// File name   : cp_show_progress.php                          
// Begin       : 2003-03-31                                    
// Last Update : 2003-04-02                                    
//                                                             
// Description : Display process progress on a popup window    
//               usage: cp_show_progress.php?log=filename.log  
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
//require_once('../../shared/code/cp_authorization.'.CP_EXT);

require_once('../../shared/code/cp_functions_form.'.CP_EXT);

$timeout = 10000; //timeout refresh in msec
$autorefresh = TRUE; //automatically refresh

//close this popup window (for default)
$page_body = "\n<script language=\"JavaScript\" type=\"text/javascript\">\n";
$page_body .= "//<![CDATA[\n";
$page_body .= "window.close();\n";
$page_body .= "//]]>\n";
$page_body .= "</script>\n";


if (isset($_REQUEST['log']) AND (strrchr($_REQUEST['log'], '.')==".log") ) {

	$log_data = @file($_REQUEST['log']); //get file content in array
	
	$page_body = "<b>LOG FILE:</b>";
	$page_body .= "<form action=\"".$_SERVER['SCRIPT_NAME']."\" method=\"post\" enctype=\"multipart/form-data\" name=\"form_log\" id=\"form_log\">";
	$page_body .= "<input type=\"hidden\" name=\"log\" id=\"log\" value=\"".$_REQUEST['log']."\" />"; //remember log file name
	$page_body .= "<div align=\"center\">";
	$page_body .= "<textarea cols=\"50\" rows=\"10\" name=\"logtext\" id=\"logtext\" readonly=\"readonly\" wrap=\"off\">\n";
		
	if ($log_data) {
		$log_data = array_reverse($log_data); //reverse array (last entry on the top)
		if (strcmp("--- END LOG:", substr($log_data[0], 0, 12)) == 0) {
			$autorefresh = FALSE; //stop autorefresh
		}
		while(list($key, $logline) = each($log_data)) { //for each log line
			$page_body .= htmlentities($logline);
		}
	}
	
	$page_body .= "</textarea>";
	$page_body .= "</div>";
	$page_body .= "</form>";
	
	if ($autorefresh) {
		//code to refresh page automatically
		$page_body .= "\n<script language=\"JavaScript\" type=\"text/javascript\">\n";
		$page_body .= "//<![CDATA[\n";
		$page_body .= "setTimeout('document.form_log.submit()', ".$timeout.");\n"; //refresh page every $timeout seconds
		$page_body .= "//]]>\n";
		$page_body .= "</script>\n";
	}

}

$thispage_title = "LOG";
$thispage_description = $thispage_title;
$thispage_keywords = $thispage_title;

require_once('../code/cp_page_header_popup.'.CP_EXT);

echo $page_body;

//display close window button
echo "<form action=\"\"><div align=\"center\">";
F_generic_button("close",$l['w_close'],"window.close()");
echo "</div></form>";

require_once('../code/cp_page_footer_popup.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>