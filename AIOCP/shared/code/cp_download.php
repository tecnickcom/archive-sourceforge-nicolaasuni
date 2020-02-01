<?php
//============================================================+
// File name   : cp_download.php
// Begin       : 2002-05-12
// Last Update : 2010-10-21
//
// Description : Send requested file to user
//               (force download)
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
	require_once('../../shared/code/cp_functions_general.'.CP_EXT);

	//the files with the following extensions can't be downloaded (security feature)
	$denied_extensions = array(CP_EXT,'php','cgi','asp','jsp','cfg','inc');

	if (isset($_REQUEST['c']) AND isset($_REQUEST['f'])) {
		if (isset($_REQUEST['d'])) {
			$duration = $_REQUEST['d'];
		} else {
			$duration = 4;
		}
		//file to download
		$file_to_download = urldecode($_REQUEST['f']);

		//check file extension - scripts files are locked for security reasons
		$path_parts = pathinfo($file_to_download);
		$file_ext = strtolower($path_parts['extension']);
		if(in_array($file_ext, $denied_extensions)) {
			echo "<div align=\"center\">DOWNLOAD<br />ERROR</div>";
			exit();
		}

		//generate verification code to avoid improper use of this file
		$verifycode = F_generate_verification_code($file_to_download, $duration);

		if ($_REQUEST['c'] == $verifycode) {
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
			header("Content-Disposition: attachment; filename=".basename($file_to_download).";");
			/*
			The Content-transfer-encoding header should be binary, since the file will be read
			directly from the disk and the raw bytes passed to the downloading computer.
			The Content-length header is useful to set for downloads. The browser will be able to
			show a progress meter as a file downloads. The content-lenght can be determines by
			filesize function returns the size of a file.
			*/
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".filesize($file_to_download));
			ob_clean();
			flush();
			//readfile($file_to_download);
			echo file_get_contents($file_to_download);
			exit;
		}
		else {
			echo "<center>DOWNLOAD<br />ERROR</center>";
		}
	}
}
else {
	// The following code has been introduced as a turnaround for the XP SP2 security issue ("Automatic prompting for file downloads" turned off).
	$filename = basename(urldecode($f));
	echo "<"."?"."xml version=\"1.0\" encoding=\"iso-8859-1\""."?".">\n";
	echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"\n";
	echo "    \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
	echo "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\" dir=\"ltr\">\n";
	echo "<head>\n";
	echo "<title>DOWNLOAD FILE</title>\n";
	echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n";
	echo "<meta name=\"description\" content=\"download: ".$filename."\" />\n";
	echo "<meta name=\"author\" content=\"Nicola Asuni\" />\n";
	echo "</head>\n";
	echo "<body>\n";
	echo "<form action=\"".$_SERVER['SCRIPT_NAME']."\" method=\"post\" enctype=\"multipart/form-data\" name=\"frmdwn\" id=\"frmdwn\">\n";
	echo "<a href=\"#\" onclick=\"document.frmdwn.submit()\" title=\"DOWNLOAD\">".$filename."</a><br />\n";
	echo "<input type=\"hidden\" name=\"c\" id=\"c\" value=\"".$_REQUEST['c']."\" />\n";
	echo "<input type=\"hidden\" name=\"f\" id=\"f\" value=\"".$_REQUEST['f']."\" />\n";
	echo "<input type=\"hidden\" name=\"d\" id=\"d\" value=\"".$_REQUEST['d']."\" />\n";
	echo "<input type=\"hidden\" name=\"m\" id=\"m\" value=\"start\" />\n";
	echo "<input type=\"button\" name=\"close\" id=\"close\" value=\"X\" onclick=\"self.close()\" title=\"CLOSE\" />";
	echo "<input type=\"submit\" name=\"DOWNLOAD\" id=\"DOWNLOAD\" value=\"&nbsp;&nbsp;&gt;&nbsp;&nbsp;\" title=\"DOWNLOAD\" />\n";
	echo "</form>\n\n";
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
	echo "//<![CDATA[\n";
	echo "self.focus();\n";
	echo "document.frmdwn.submit();\n";
	echo "//]]>\n";
	echo "</script>\n";
	echo "</body>\n";
	echo "</html>";
}
