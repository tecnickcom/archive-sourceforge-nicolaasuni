<?php
//============================================================+
// File name   : fckconfig_aiocp.php
// Begin       : 2006-03-02
// Last Update : 2006-03-02
// 
// Description : Custom AIOCP config file for FCKeditor
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

// fix for IE catching or PHP bug issue
header("Pragma: public");
// Date in the past
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
// always modified
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
// force download dialog
header("Content-Type: application/javascript");
// use the Content-Disposition header to supply a recommended filename and 
// force the browser to display the save dialog. 
header("Content-Disposition: attachment; filename=fkcconfig_aiocp.js;");
/*
The Content-transfer-encoding header should be binary, since the file will be read 
directly from the disk and the raw bytes passed to the downloading computer.
The Content-length header is useful to set for downloads. The browser will be able to 
show a progress meter as a file downloads. The content-lenght can be determines by 
filesize function returns the size of a file. 
*/
header("Content-Transfer-Encoding: binary");

require_once('../../shared/config/cp_extension.inc');
require_once('../config/cp_config.'.CP_EXT);
$pagelevel = 0; // page level
require_once('../../shared/code/cp_authorization.'.CP_EXT); // check authorization permissions

// load default public CSS stylesheet
echo "FCKConfig.EditorAreaCSS = '".K_PATH_AIOCP."public/styles/default.css';\n";

// set the language
echo "FCKConfig.AutoDetectLanguage = false;\n";
echo "FCKConfig.DefaultLanguage = '".$l['a_meta_language']."';\n";
echo "FCKConfig.ContentLangDirection = '".$l['a_meta_dir']."';\n";

//============================================================+
// END OF FILE                                                 
//============================================================+
?>