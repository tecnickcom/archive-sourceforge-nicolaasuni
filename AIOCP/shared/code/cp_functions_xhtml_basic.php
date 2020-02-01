<?php
//============================================================+
// File name   : cp_functions_xhtml_basic.php                  
// Begin       : 2002-06-02                                    
// Last Update : 2003-10-12                                    
//                                                             
// Description :  Get XHTML well formed and return XHTML Basic 
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
// Get XHTML well formed and return XHTML Basic 
// This function work well with AIOCP pages
//
// Before use this function remember:
// - XHTML Basic do not support frames so be sure to specify on 
//   your pages appropriate <noframes> alternative content 
// - HTML Forms may work or not
// - XHTML Basic do not handle nested tables, this function do not resolve this problem
// ------------------------------------------------------------
function F_html_to_xhtml_basic($page_url, $xhtml_basic, $charset) {
	global $l, $db;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_general.'.CP_EXT);
	
	//get current path from current url
	preg_match("/([^\?\#]*)/i", $page_url, $regurl); //remove queries from link
	$current_path = substr($regurl[1], 0, - strlen(basename($regurl[1])));
	
	//remove applet and get alternative content
	$xhtml_basic = preg_replace("/<applet[^>]*?>(.*?)<\/applet>/esi", "preg_replace(\"/<param[^>]*>/i\", \"\", \"\\1\")", $xhtml_basic);
	
	//remove object and get alternative content
	$xhtml_basic = preg_replace("/<object[^>]*?>(.*?)<\/object>/esi", "preg_replace(\"/<param[^>]*>/i\", \"\", \"\\1\")", $xhtml_basic);
	
	//table of tags to remove
	$replace_table = array (
		"'<\?xml[^>]*?\?>'si" => "",
		"'<!DOCTYPE[^>]*?>'si" => "",
		"'<frameset[^>]*?>.*?</frameset>'si" => "",
		"'<style[^>]*?>.*?</style>'si" => "",
		"'<script[^>]*?>.*?</script>'si" => "",
		"'<noscript[^>]*?>.*?</noscript>'si" => "",
		"'<%[^>]*?%>'si" => "",
		"'<#[^>]*?#>'si" => "",
		"'<\?[^>]*?\?>'si" => "",
		"'<!--[^>]*?-->'si" => ""
	);
	
	//remove some usupported tags
	$xhtml_basic = preg_replace(array_keys($replace_table), array_values($replace_table), $xhtml_basic);
	
	//get list of xhtml basic tags
	$xhtml_basic_tags = "";
	$sql = "SELECT tag_name FROM ".K_TABLE_XHTML_TAGS." WHERE tag_xhtml_basic=1";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			$xhtml_basic_tags .= "<".$m['tag_name'].">";
		}
	}
	else {
		F_display_db_error();
	}
	
	// strip non xhtml basic tags
	$xhtml_basic = strip_tags($xhtml_basic, $xhtml_basic_tags);
	
	// replace document stylesheet with the default xhtml_basic.css
	$xhtml_basic = preg_replace("/<link[\s]+rel=\"stylesheet\"[\s]+href=\"[^\"]*\"[\s]+type=\"text\/css\"[\s]+\/>/i", "<link rel=\"stylesheet\" href=\"".htmlentities(urldecode(K_PATH_STYLE_SHEETS."xhtml_basic.css"))."\" type=\"text/css\" />", $xhtml_basic, 1);
	
	//redirect links to xhtml engine
	$xhtml_basic = preg_replace("/<a([^>]*)href=\"([^\"]*)\"/ei", "'<a'.stripslashes('\\1').'href=\"'.F_convert_internal_links('\\2','$current_path').'\"'", $xhtml_basic);
	
	//remove events parameters from anchors tags
	$xhtml_basic = preg_replace("/<a([^>]*)onmouseover=\"([^\"]*)\"/ei", "'<a'.stripslashes('\\1')", $xhtml_basic);
	$xhtml_basic = preg_replace("/<a([^>]*)onmousemove=\"([^\"]*)\"/ei", "'<a'.stripslashes('\\1')", $xhtml_basic);
	$xhtml_basic = preg_replace("/<a([^>]*)onmouseout=\"([^\"]*)\"/ei", "'<a'.stripslashes('\\1')", $xhtml_basic);
	$xhtml_basic = preg_replace("/<a([^>]*)onmouseup=\"([^\"]*)\"/ei", "'<a'.stripslashes('\\1')", $xhtml_basic);
	$xhtml_basic = preg_replace("/<a([^>]*)onmousedown=\"([^\"]*)\"/ei", "'<a'.stripslashes('\\1')", $xhtml_basic);
	$xhtml_basic = preg_replace("/<a([^>]*)onkeypress=\"([^\"]*)\"/ei", "'<a'.stripslashes('\\1')", $xhtml_basic);
	$xhtml_basic = preg_replace("/<a([^>]*)onkeydown=\"([^\"]*)\"/ei", "'<a'.stripslashes('\\1')", $xhtml_basic);
	$xhtml_basic = preg_replace("/<a([^>]*)onkeyup=\"([^\"]*)\"/ei", "'<a'.stripslashes('\\1')", $xhtml_basic);
	$xhtml_basic = preg_replace("/<a([^>]*)onfocus=\"([^\"]*)\"/ei", "'<a'.stripslashes('\\1')", $xhtml_basic);
	$xhtml_basic = preg_replace("/<a([^>]*)onblur=\"([^\"]*)\"/ei", "'<a'.stripslashes('\\1')", $xhtml_basic);
	$xhtml_basic = preg_replace("/<a([^>]*)onclick=\"([^\"]*)\"/ei", "'<a'.stripslashes('\\1')", $xhtml_basic);
	$xhtml_basic = preg_replace("/<a([^>]*)ondblclick=\"([^\"]*)\"/ei", "'<a'.stripslashes('\\1')", $xhtml_basic);
	
	// add XHTML Basic DTD
	$xhtml_basic = "<"."?xml version=\"1.0\" encoding=\"".$charset."\"?"."><!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML Basic 1.0//EN\" \"http://www.w3.org/TR/xhtml-basic/xhtml-basic10.dtd\">\n".$xhtml_basic;
	
	//replace white spaces
	if (!preg_match("/<pre[^>]*>/i", $xhtml_basic)) { //check for <pre> tag
		$xhtml_basic = preg_replace("/[\s]+/i", " ", $xhtml_basic);
	}
	
	return $xhtml_basic;
}

// ------------------------------------------------------------
// replace internal links to point on xhtml basic engine
// ------------------------------------------------------------
function F_convert_internal_links($foundurl, $current_path) {
	
	$newurl = $foundurl;
	
	if ( !preg_match("/[\/]?xexit:|[\/]?mailto:|[\/]?javascript:|[\/]?news:|[\/]?aim:/i", $foundurl) ) { //if link is valid
		if (F_is_relative_link($foundurl)) { //found relative link
			//resolve link
			if (!strncasecmp($foundurl, "/", 1)) { //link refer to the host root
				$newurl = K_PATH_HOST.$foundurl;
			}
			else { //link is relative to the current path
				$newurl = F_resolve_url_path($current_path.$foundurl);
			}
			
			// add a variable to path to inform xhtml engine that we are in xhtml basic mode
			$newurl_parts = parse_url($newurl);
			if (empty($newurl_parts['query'])) {
				$newurl .= "?";
			}
			else {
				$newurl .= "&amp;";
			}
			$newurl .= "xhtmlb=1";
			$newurl = htmlentities(urldecode(basename($_SERVER['SCRIPT_NAME'])))."?page=".$newurl;
		}
	}
	elseif ( preg_match("/[\/]?javascript:/i", $foundurl) ) { //if link is a javascript link
		$newurl = "";
	}
	elseif ( preg_match("/[\/]?xexit:/i", $foundurl) ) { //xexit is an AIOCP code to excape certain links
		$newurl = substr(strrchr($newurl, ":"), 1);
	}
	
	$newurl = str_replace("&amp;", "&", $newurl);
	$newurl = str_replace("&", "&amp;", $newurl);
	
	return $newurl;
}
//============================================================+
// END OF FILE                                                 
//============================================================+
?>