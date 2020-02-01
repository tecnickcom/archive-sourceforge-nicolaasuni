<?php
//============================================================+
// File name   : cp_functions_transcoding_bar.php
// Begin       : 2002-06-03
// Last Update : 2007-01-11
//
// Description : display a bar with links for online document  
//               transcoding
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
// Display a transcoding menu bar
// ------------------------------------------------------------
function F_show_transcoding_bar() {
	global $l, $xhtmlb, $page;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	//build bar
	$trancodebar = "<div class=\"transcodingbar\">";
	
	//NOTE: "xexit:" is a special AIOCP code to escape links from trancoding
	//open page editor (ADMIN)
	if (($_SESSION['session_user_level'] >= K_AUTH_EDIT_PAGES_LEVEL) AND isset($_REQUEST['aiocp_dp']) AND $_REQUEST['aiocp_dp']) {
		$trancodebar .= " | <a href=\"xexit:../../admin/code/index.".CP_EXT."?load_page=../../admin/code/cp_edit_dbpages.".CP_EXT."%3Fpage=".urlencode($_REQUEST['aiocp_dp'])."\" target=\"_top\">".$l['w_edit']."</a>";	
	}
	
	$current_page = K_PATH_HOST.$_SERVER['SCRIPT_NAME'];
	if ($_SERVER['QUERY_STRING']) {
		$current_page .= "?".$_SERVER['QUERY_STRING'];
	}
	
	//convert page to TEXT
	$trancodebar .= " | <a href=\"xexit:../code/cp_html2txt.".CP_EXT."?page=".urlencode($current_page)."\" target=\"_blank\">TXT</a>";
	
	//convert page to TEXT with links inside
	$trancodebar .= " | <a href=\"xexit:../code/cp_html2txt.".CP_EXT."?txtlnk=1&amp;page=".urlencode($current_page)."\" target=\"_blank\">TXT+</a>";
	
	if (isset($xhtmlb) AND $xhtmlb) { //we are inside XHTML transcoding script
		//remove xhtmlb parameter from page URL
		$current_page = preg_replace("/(\?|\&|%3F|%26|\&amp;|%26amp%3B)xhtmlb(=|%3D)[0-9]{1}/i", "", $current_page); 
		//return to original page
		$trancodebar .= " | <a href=\"xexit:".$current_page."\" target=\"_top\">ORIGINAL</a>";
	}
	else { //we are in real page mode
		if ($_SERVER['QUERY_STRING']) {
			$xhtml_page = $current_page."&amp;";
		}
		else {
			$xhtml_page = $current_page."?";
		}
		// add a variable to path to inform xhtml engine that we are in xhtml basic mode
		$xhtml_page .= "xhtmlb=1"; 
		//convert page to XHTML 1.0 BASIC
		//$trancodebar .= " | <a href=\"../code/cp_html2xhtmlbasic.".CP_EXT."?page=".urlencode($xhtml_page)."\" target=\"_top\">XHTML 1.0 BASIC</a>";
		
		$trancodebar = str_replace("xexit:", "", $trancodebar); //remove "xexit:" from links (real page mode)
	}
		
	$trancodebar .= " |&nbsp;</div>";
	
	return $trancodebar;
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
