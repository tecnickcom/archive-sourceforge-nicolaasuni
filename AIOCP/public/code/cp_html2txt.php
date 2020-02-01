<?php
//============================================================+
// File name   : cp_html2txt.php                               
// Begin       : 2002-06-02                                    
// Last Update : 2003-04-02                                    
//                                                             
// Description : convert html to text (transcoding)            
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
require_once('../../shared/code/cp_functions_get_page.'.CP_EXT);
require_once('../../shared/code/cp_functions_html2txt.'.CP_EXT);

if (isset($_REQUEST['page'])) {
	$page = urldecode($_REQUEST['page']);
	$pagecode = F_get_page_content($page);
	if ($pagecode) {
		//create new filename
		$filename = str_replace("cp_dpage.".CP_EXT."?aiocp_dp=", "aiocp_dp_", $page);
		$filename = current(split("\.",basename($filename))).".txt";
		
		$show_links = false;
		if (isset($_REQUEST['txtlnk']) AND $_REQUEST['txtlnk']) {
			$show_links = true;
		}
		
		//convert page to text
		$textpage = F_html_to_text($pagecode, false, $show_links);
		
		//send headers
		header('Cache-Control: public', TRUE); 
		header("Last-Modified: ".gmdate('D, d M Y H:i:s T', time())."", TRUE); 
		header('Content-Transfer-Encoding: base64', TRUE); 
		header("Accept-Ranges: bytes", TRUE); 
		header("Content-Length: ".strlen($textpage)."", TRUE); 
		header("Content-Disposition: inline; filename=".$filename."", TRUE);
		header("Content-Type: text/plain; filename=\"".$filename."\"", TRUE); //text/plain open on browser - text/text open on default application
		
		//display page content
		echo $textpage;
	}
	else {
		//close window
		echo "<script language=\"JavaScript\" type=\"text/javascript\">";
		echo "//<![CDATA[\n";
		echo "window.close();";
		echo "//]]>\n";
		echo "</script>";
	}
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
