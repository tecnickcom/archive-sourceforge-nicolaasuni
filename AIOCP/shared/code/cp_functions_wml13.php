<?php
//============================================================+
// File name   : cp_functions_wml13.php                  
// Begin       : 2003-04-14                                    
// Last Update : 2003-04-17                                    
//                                                             
// Description :  Get XHTML well formed and return WML 1.3 
//
// Reference   : WAP-191-WML-20000219-a.pdf
//               http://www.wapforum.org/
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
// Get XHTML well formed and return WML 1.3 
//
// Before use this function remember:
// - XHTML Basic do not support frames so be sure to specify on 
//   your pages appropriate <noframes> alternative content 
// - HTML Forms may work or not
// ------------------------------------------------------------
function F_html_to_wml13($page_url, $xhtml_full, $charset) {
	global $l, $db;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_general.'.CP_EXT);
	
	$wmlcode = $xhtml_full;
	
	$dollar_replacement = ":.dlr.:"; //string replacement for dollar symbol
	$wmlcode = str_replace("\$", $dollar_replacement,  $wmlcode); //replace special character
	
	//get current path from current url
	preg_match("/([^\?\#]*)/i", $page_url, $regurl); //remove queries from link
	$current_path = substr($regurl[1], 0, - strlen(basename($regurl[1])));
	
	//get page title
	$page_title = "";
	if (preg_match("'<title[^>]*?>(.*?)</title>'si", $wmlcode, $regurl)) {
		$page_title = $regurl[1];
	}
		
	//remove applet and get alternative content
	$wmlcode = preg_replace("/<applet[^>]*?>(.*?)<\/applet>/esi", "preg_replace(\"/<param[^>]*>/i\", \"\", \"\\1\")", $wmlcode);
	//remove object and get alternative content
	$wmlcode = preg_replace("/<object[^>]*?>(.*?)<\/object>/esi", "preg_replace(\"/<param[^>]*>/i\", \"\", \"\\1\")", $wmlcode);
	
	//table of tags to remove or substitute
	$replace_table = array (
		"'<p[^>]*?>(.*?)</p>'si" => "<br />\\1<br />",
		"'<\?xml[^>]*?\?>'si" => "",
		"'<!DOCTYPE[^>]*?>'si" => "",
		"'<frameset[^>]*?>.*?</frameset>'si" => "",
		"'<html([^>]*?)>'si" => "<wml\\1>",
		"'</html>'si" => "</wml>",
		"'<style[^>]*?>.*?</style>'si" => "",
		"'<script[^>]*?>.*?</script>'si" => "",
		"'<noscript[^>]*?>.*?</noscript>'si" => "",
		"'<link[^>]*?/>'si" => "",
		"'<%[^>]*?%>'si" => "",
		"'<#[^>]*?#>'si" => "",
		"'<\?[^>]*?\?>'si" => "",
		"'<!--[^>]*?-->'si" => "",
		"'<title[^>]*?>.*?</title>'si" => "",
		"'<h1[^>]*?>(.*?)</h1>'si" => "<br /><big><strong>\\1</strong></big><br />",
		"'<h2[^>]*?>(.*?)</h2>'si" => "<br /><big>\\1</big><br />",
		"'<h3[^>]*?>(.*?)</h3>'si" => "<br /><strong>\\1</strong><br />",
		"'<h4[^>]*?>(.*?)</h4>'si" => "<br /><b><i>\\1</i></b><br />",
		"'<h5[^>]*?>(.*?)</h5>'si" => "<br /><i>\\1</i><br />",
		"'<h[6-9][^>]*?>(.*?)</h[6-9]>'si" => "<br />\\1<br />",
		"'<sup[^>]*?>(.*?)</sup>'si" => "<i>\\1</i>",
		"'<sub[^>]*?>(.*?)</sub>'si" => "<i>\\1</i>",
		"'<var[^>]*?>(.*?)</var>'si" => "<i>\\1</i>",
		"'<strike[^>]*?>(.*?)</strike>'si" => "<u>\\1</u>",
		"'<center[^>]*?>(.*?)</center>'si" => "<p align=\"center\">\\1</p>",
		"'<li[^>]*?>(.*?)</li>'si" => " \\1<br />",
		"'<tt[^>]*?>(.*?)</tt>'si" => "<pre>\\1</pre>",
		"'<div[^>]*?>(.*?)</div>'si" => "<br />\\1<br />",
		"'<dt[^>]*?>(.*?)</dt>'si" => "<br />\\1<br />",
		"'<dl[^>]*?>(.*?)</dl>'si" => "<br />\\1<br />",
		"'<th[^>]*?>(.*?)</th>'si" => "<td>\\1</td>",
		"'<area[^>]*?>(.*?)</area>'si" => "<br />\\1<br />",
		"'<title[^>]*?>.*?</title>'si" => "",
		"'<body[^>]*?>(.*?)</body>'si" => "<card title=\"".$page_title."\"><p>\\1</p></card>",
		
		"'<form[^>]*?>(.*?)</form>'si" => "",
		"'<textarea[^>]*?>(.*?)</textarea>'si" => "\\1",
		"'<select[^>]*?>(.*?)</select>'si" => "",
		"'<input[^>]*?/>'si" => "",
		
		"'<table[^>]*?>(.*?)</table>'si" => "<br />\\1<br />",
		"'<tr[^>]*?>(.*?)</tr>'si" => "\\1<br />",
		"'<td[^>]*?>(.*?)</td>'si" => " \\1 ",
		"'<th[^>]*?>(.*?)</th>'si" => " \\1 "
	);
			
	//remove some usupported tags
	$wmlcode = preg_replace(array_keys($replace_table), array_values($replace_table), $wmlcode);
	
	//replace images using alt description
	$wmlcode = preg_replace("'<img[^>]*alt[\s]*=[\s]*[\"\']*([^\"\'<>]*)[\"\'][^>]*>'i", "[IMAGE: \\1]",  $wmlcode);
	
	//change meta content type
	//<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	$wmlcode = preg_replace("/http-equiv=['\"]?Content-Type['\"]?[\s]+content=['\"][^'\"<>; ]*/ei", "'http-equiv=\"Content-Type\" content=\"text/vnd.wap.wml'", $wmlcode);
	
	//remove some attributes
	$wmlcode = preg_replace("/<wml([^>]*)xmlns=\"([^\"]*)\"/ei", "'<wml'.stripslashes('\\1')", $wmlcode);
	$wmlcode = preg_replace("/<wml([^>]*)lang=\"([^\"]*)\"/ei", "'<wml'.stripslashes('\\1')", $wmlcode);
	$wmlcode = preg_replace("/<wml([^>]*)dir=\"([^\"]*)\"/ei", "'<wml'.stripslashes('\\1')", $wmlcode);
		
	// strip non WML tags
	$wmlcode_tags = "<a><anchor><access><b><big><br><card><do><em><fieldset><go><head><i><img><input><meta><noop><p><postfield><pre><prev><onevent><optgroup><option><refresh><select><setvar><small><strong><table><td><template><timer><tr><u><wml>";
	$wmlcode = strip_tags($wmlcode, $wmlcode_tags);
	
	//redirect links to wml transcoder
	$wmlcode = preg_replace("/<a([^>]*)href=\"([^\"]*)\"[^>]*>/ei", "'<a href=\"'.F_convert_internal_links('\\2','$current_path').'\">'", $wmlcode);
		
	// add WML 1.3 Basic DTD
	$wmlcode = "<"."?xml version=\"1.0\" encoding=\"".$charset."\"?".">\n<!DOCTYPE wml PUBLIC \"-//WAPFORUM//DTD WML 1.3//EN\" \"http://www.wapforum.org/DTD/wml13.dtd\">\n".$wmlcode;
	
	//replace white spaces
	if (!preg_match("/<pre[^>]*>/i", $fullpage)) { //check for <pre> tag
		$xhtml_basic = preg_replace("/[\s]+/i", " ", $xhtml_basic);
	}
	
	$wmlcode = str_replace($dollar_replacement, "\$\$",  $wmlcode); //escape dollar sign
		
	return $wmlcode;
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
			
			// add a variable to path to inform xhtml engine that we are in transcoded mode
			$newurl_parts = parse_url($newurl);
			if (empty($newurl_parts['query'])) {
				$newurl .= "?";
			}
			else {
				$newurl .= "&amp;";
			}
			$newurl .= "xhtmlb=1";
			$newurl = htmlentities(urldecode(basename($_SERVER['SCRIPT_NAME'])))."?hpage=".$newurl;
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