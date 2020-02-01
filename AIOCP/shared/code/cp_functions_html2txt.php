<?php
//============================================================+
// File name   : cp_functions_html2txt.php                     
// Begin       : 2001-10-21                                    
// Last Update : 2006-02-01                                    
//                                                             
// Description : Convert HTML to TEXT                          
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

function F_html_to_text($textequivalent, $preserve_newlines=false, $display_links=false) {
	require_once('../../shared/config/cp_extension.inc');
	require_once('../../shared/code/cp_functions_general.'.CP_EXT);
	
	$dollar_replacement = ":.dlr.:"; //string replacement for dollar symbol
	
	//tags conversion table
	$tags2textTable = array (
		"'<br[^>]*?>'i" => "\n",
		"'<p[^>]*?>'i" => "\n",
		"'</p>'i" => "\n",
		"'<div[^>]*?>'i" => "\n",
		"'</div>'i" => "\n",
		"'<table[^>]*?>'i" => "\n",
		"'</table>'i" => "\n",
		"'<tr[^>]*?>'i" => "\n",
		"'<th[^>]*?>'i" => "\t ",
		"'<td[^>]*?>'i" => "\t ",
		"'<li[^>]*?>\t'i" => "\n",
		"'<h[0-9][^>]*?>'i" => "\n\n",
		"'</h[0-9]>'i" => "\n",
		"'<head[^>]*?>.*?</head>'si" => "\n",  // Strip out head
		"'<style[^>]*?>.*?</style>'si" => "\n",  // Strip out style
		"'<script[^>]*?>.*?</script>'si" => "\n"  // Strip out javascript
	);
	
	$textequivalent = str_replace("\r\n", "\n",  $textequivalent);
	
	$textequivalent = str_replace("\$", $dollar_replacement,  $textequivalent); //replace special character
	
	//remove session variable PHPSESSID from links
	$textequivalent = preg_replace("/(\?|\&|%3F|%26|\&amp;|%26amp%3B)PHPSESSID(=|%3D)[a-z0-9]{32,32}/i", "", $textequivalent); 
	
	//remove applet and get alternative content
	$textequivalent = preg_replace("/<applet[^>]*?>(.*?)<\/applet>/esi", "preg_replace(\"/<param[^>]*>/i\", \"\", \"\\1\")", $textequivalent);
	
	//remove object and get alternative content
	$textequivalent = preg_replace("/<object[^>]*?>(.*?)<\/object>/esi", "preg_replace(\"/<param[^>]*>/i\", \"\", \"\\1\")", $textequivalent);
	
	//indent list elements
	$firstposition = 0;
	while (($pos=strpos($textequivalent, "<ul")) > $firstposition) {
		$textequivalent = preg_replace("/<ul[^>]*?>(.*?)<\/ul>/esi", "preg_replace(\"/<li[^>]*>/i\", \"<li>\t\", \"\\1\")", $textequivalent);
		$firstposition = $pos;
	}
	$firstposition = 0;
	while (($pos=strpos($textequivalent, "<ol")) > $firstposition) {
		$textequivalent = preg_replace("/<ol[^>]*?>(.*?)<\/ol>/esi", "preg_replace(\"/<li[^>]*>/i\", \"<li>\t\", \"\\1\")", $textequivalent);
		$firstposition = $pos;
	}
	
	$textequivalent = preg_replace("'<img[^>]*alt[\s]*=[\s]*[\"\']*([^\"\'<>]*)[\"\'][^>]*>'i", "[IMAGE: \\1]",  $textequivalent);
	
	//give textual representation of links and images
	if ($display_links) {
		$textequivalent = preg_replace("'<a[^>]*href[\s]*=[\s]*[\"\']*([^\"\'<>]*)[\"\'][^>]*>(.*?)</a>'si", "\\2 [LINK: \\1]",  $textequivalent);
	}
	
	if (!$preserve_newlines){ //remove newlines
		$textequivalent = str_replace("\n", "",  $textequivalent);
	}
	
	$textequivalent = preg_replace(array_keys($tags2textTable), array_values($tags2textTable), $textequivalent);
		
	$textequivalent = preg_replace("'<[^>]*?>'si", "", $textequivalent); //strip out remaining tags
	
	//remove some newlines in excess
	$textequivalent = preg_replace("'[ \t\f]+[\r\n]'si", "\n",  $textequivalent);
	$textequivalent = preg_replace("'[\r\n][\r\n]+'si", "\n\n",  $textequivalent);
	
	$textequivalent = unhtmlentities($textequivalent, FALSE);
	
	$textequivalent = str_replace($dollar_replacement, "\$",  $textequivalent); //restore special character
	
	return stripslashes(trim($textequivalent));
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
