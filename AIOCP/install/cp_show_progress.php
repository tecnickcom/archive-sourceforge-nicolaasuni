<?php
//============================================================+
// File name   : cp_show_progress.php
// Begin       : 2003-03-31
// Last Update : 2006-03-08
// 
// Description : Display process progress on a popup window
//               usage: cp_show_progress.php?log=filename.log
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

$timeout = 10000; //timeout refresh in msec
$autorefresh = TRUE; //automatically refresh

$progress_log = "install.log";

$log_data = @file($progress_log); //get file content in array
	
$page_body = "<b>LOG FILE:</b>\n";
$page_body .= "<form action=\"".$_SERVER['SCRIPT_NAME']."\" method=\"post\" enctype=\"multipart/form-data\" id=\"form_log\">\n";
$page_body .= "<div align=\"center\">\n";
$page_body .= "<textarea cols=\"50\" rows=\"10\" name=\"logtext\" id=\"logtext\" readonly=\"readonly\">\n";
		
if ($log_data) {
	$log_data = array_reverse($log_data); //reverse array (last entry on the top)
	if (strcmp("--- END LOG:", substr($log_data[0], 0, 12)) == 0) {
		$autorefresh = FALSE; //stop autorefresh
	}
	while(list($key, $logline) = each($log_data)) { //for each log line
		$page_body .= htmlspecialchars($logline, ENT_NOQUOTES, "UTF-8");
	}
}
	
$page_body .= "\n</textarea>\n";
$page_body .= "</div>\n";
$page_body .= "</form>\n";
	
if ($autorefresh) {
	//code to refresh page automatically
	$page_body .= "\n<script type=\"text/javascript\">\n";
	$page_body .= "//<![CDATA[\n";
	$page_body .= "setTimeout('document.getElementById('form_log').submit()', ".$timeout.");\n"; //refresh page every $timeout seconds
	$page_body .= "//]]>\n";
	$page_body .= "</script>\n";
}

//send XHTML headers
echo "<"."?xml version=\"1.0\" encoding=\"UTF-8\"?".">\n";
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
echo "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\" dir=\"ltr\">\n";

echo "<head>\n";
echo "<title>TCExam - INSTALL LOG</title>\n";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />\n";
echo "<meta name=\"description\" content=\"TCExam Installation LOG\" />\n";
echo "<meta name=\"author\" content=\"Nicola Asuni - Tecnick.com LTD\" />\n";
echo "<meta name=\"reply-to\" content=\"info@tecnick.com\" />\n";
echo "<meta http-equiv=\"Pragma\" content=\"no-cache\" />\n";
echo "<link rel=\"stylesheet\" href=\"../admin/styles/default.css\" type=\"text/css\" />\n";
echo "</head>\n";

echo "<body>\n";

echo $page_body;

//display close window button
echo "<form action=\"".$_SERVER['SCRIPT_NAME']."\" id=\"closeform\"><div align=\"center\">\n";
echo "<input type=\"button\" name=\"close\" id=\"close\" value=\"close\" onclick=\"window.close()\" />\n";
echo "</div></form>\n";

echo "</body>\n";
echo "</html>";

//============================================================+
// END OF FILE                                                 
//============================================================+
?>