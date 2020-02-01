<?php
//============================================================+
// File name   : cp_show_file.php                              
// Begin       : 2001-09-24                                    
// Last Update : 2004-09-30                                    
//                                                             
// Description : Preview a file                                
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

if (isset($_REQUEST['m']) AND ($_REQUEST['m']=="start")) {

	require_once('../../shared/config/cp_extension.inc');
	require_once('../../shared/config/cp_general_constants.'.CP_EXT); //used for verification code generation
	require_once('../../shared/code/cp_functions_general.'.CP_EXT); //used for verification code
	
	if (isset($_REQUEST['h']) AND isset($_REQUEST['c']) AND isset($_REQUEST['f'])) {
		$file = urldecode($_REQUEST['f']); //file to view or download
		//generate verification code to avoid improper use of this file
		$verifycode = F_generate_verification_code($file, 4);
		
		if ($_REQUEST['c'] == $verifycode) {
			if($_REQUEST['h']=="download") { //set downlodable file MIME
				// fix for IE catching or PHP bug issue
				header("Pragma: public");
				// Date in the past
				header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
				// always modified
				header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header('Content-Description: File Transfer');
				// force download dialog
				header("Content-Type: application/force-download");
				header("Content-Type: application/octet-stream", false);
				header("Content-Type: application/download", false);
				// use the Content-Disposition header to supply a recommended filename and 
				// force the browser to display the save dialog. 
				header("Content-Disposition: attachment; filename=".basename($file).";");
				/*
				The Content-transfer-encoding header should be binary, since the file will be read 
				directly from the disk and the raw bytes passed to the downloading computer.
				The Content-length header is useful to set for downloads. The browser will be able to 
				show a progress meter as a file downloads. The content-lenght can be determines by 
				filesize function returns the size of a file. 
				*/
				header("Content-Transfer-Encoding: binary");
				header("Content-Length: ".filesize($file));
			}
			else {
				header($_REQUEST['h']); //set proper MIME header
			}
			readfile($file);
		}
		else {
			echo "<center>ERROR</center>";
		}
	}
}
else {
	// The following code has been introduced as a turnaround for the XP SP2 security issue ("Automatic prompting for file downloads" turned off).
	echo "<form action=\"".$_SERVER['SCRIPT_NAME']."\" method=\"post\" enctype=\"multipart/form-data\" name=\"frmdwn\" id=\"frmdwn\">";
	echo "<input type=\"hidden\" name=\"h\" id=\"h\" value=\"".$_REQUEST['h']."\">";
	echo "<input type=\"hidden\" name=\"c\" id=\"c\" value=\"".$_REQUEST['c']."\">";
	echo "<input type=\"hidden\" name=\"f\" id=\"f\" value=\"".$_REQUEST['f']."\">";
	echo "<input type=\"hidden\" name=\"m\" id=\"m\" value=\"start\">";
	echo "<input type=\"submit\" name=\"DOWNLOAD\" id=\"DOWNLOAD\" value=\"&gt;\">";
	echo "</form>\n";
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
	echo "//<![CDATA[\n";
	echo "document.frmdwn.submit();\n";
	echo "//]]>\n";
	echo "</script>\n";
}
//============================================================+
// END OF FILE                                                 
//============================================================+
?>