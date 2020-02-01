<?php
//============================================================+
// File name   : cp_functions_get_page.php                     
// Begin       : 2002-05-29                                    
// Last Update : 2002-09-29                                    
//                                                             
// Description : Functions for pages                           
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

// ------------------------------------------------------------
// connect to page and return content
// $page_url must be a full url address
// ------------------------------------------------------------
function F_get_page_content($page_url) {
	global $PHPSESSID;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../../shared/config/cp_paths.'.CP_EXT);
	
	$page_content = ""; //this will contain the page content
	$timeout = 30; //max seconds to wait for link response
	
	//decode url
	$page_url = str_replace("&amp;","&", urldecode($page_url));
	
	//get url parts
	$url_parts = parse_url($page_url);
	
	if (empty($url_parts['port'])) {
		$url_parts['port'] = K_STANDARD_PORT; //standard port
	}
	
	//attach current user PHPSESSID.
	if (isset($PHPSESSID)) {
		//To index all pages (level from 0 to 10) you must be an administrator (level 10)
		if (empty($url_parts['query'])) {
			$page_url .= "?";
		}
		else {
			$page_url .= "&";
		}
		$page_url .= "PHPSESSID=".$PHPSESSID."";
	}
	
	$fp = fsockopen($url_parts['host'], $url_parts['port'], $errno, $errstr, $timeout);
	if (!$fp) {
		//echo "".$errstr." (".$errno.")\n"; exit;
	} 
	else { //valid link
		
		//complete get (see http://www.w3.org/Protocols/rfc2616/rfc2616.html)
		$request_header = "GET ".$page_url." HTTP/1.0\r\n";
		$request_header .= "Host: ".$url_parts['host'].":".$url_parts['port']."\r\n";
		if ( (!empty($url_parts['user'])) AND (!empty($url_parts['pass'])) ) {
			$request_header .= "Authorization: Basic ".base64_encode($url_parts['user'].":".$url_parts['pass'])."\r\n";
		}
		$request_header .= "Accept: text/*\r\n"; //accept only text type documents
		$request_header .= "Accept-Charset: *\r\n";
		$request_header .= "Accept-Encoding: *\r\n";
		$request_header .= "Accept-Language: *\r\n";
		$request_header .= "User-Agent: AIOCP Search Engine\r\n\r\n";
		
		//send header request
		fputs($fp, $request_header);
		
		//get page content
		while (!feof($fp)) {
			$page_content .= fread($fp, 4096);
		}
		
		fclose ($fp);
		
		//check page status (only codes 2xx are OK)
		if (preg_match("/HTTP\/[0-9\.]+ ([0-9])/i", $page_content, $regs)) {
			if ($regs[1] != 2) {
				$page_content = "";
			}
			else {
				//remove headers from response
				$page_content = preg_replace("/[^<]*\r\n\r\n/i", "", $page_content, 1);
			}
		}
	}
	return $page_content;
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
