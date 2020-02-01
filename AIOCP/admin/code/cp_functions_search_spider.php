<?php
//============================================================+
// File name   : cp_functions_search_spider.php                
// Begin       : 2002-05-29                                    
// Last Update : 2006-02-08
// 
// Description : search functions for page indexing
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
// Analize and return page data
// ------------------------------------------------------------
function F_get_page_data($page_url, $page_code) {
	require_once('../../shared/config/cp_extension.inc');
	require_once('../../shared/config/cp_paths.'.CP_EXT);
	require_once('../../shared/code/cp_functions_html2txt.'.CP_EXT);
	require_once('../../shared/code/cp_functions_general.'.CP_EXT);
	
	$page_data = Array(); //this contain the page data
	
	//get page url
	$page_data['url'] = htmlentities(unhtmlentities($page_url));
	
	//get index time
	$page_data['index_time'] = gmdate("Y-m-d H:i:s");
	
	//get page size in bytes
	$page_data['size'] = strlen($page_code);
	
	//replace blank characters by space
	$page_code = preg_replace("/[\s]+/i", " ", $page_code); 
	
	//get page title
	if ( preg_match("/<title[^>]*>([^<>]*)<\/title>/i", $page_code, $regs) ) {
		$page_data['title'] = trim($regs[1]);
	}
	else {
		$page_data['title'] = "";
	}
    
	//get content type
	if ( preg_match("/<meta[\s]+http-equiv=['\"]?Content-Type['\"]?[\s]+content=['\"]?([^'\"<>]*)/i", $page_code, $regs) ) {
		$page_data['content_type'] = trim($regs[1]);
	}
	else {
		$page_data['content_type'] = "";
	}
	
	//get description
	if ( preg_match("/<meta[\s]+name=['\"]?description['\"]?[\s]+content=['\"]?([^\"<>]*)/i", $page_code, $regs) ) {
		$page_data['description'] = trim($regs[1]);
	}
	else {
		$page_data['description'] = "";
	}
	
	//get keywords
	if ( preg_match("/<meta[\s]+name=['\"]?keywords['\"]?[\s]+content=['\"]?([^\"<>]*)/i", $page_code, $regs) ) {
		$page_data['keywords'] = trim($regs[1]);
	}
	else {
		$page_data['keywords'] = "";
	}
	
	//get page level (AIOCP authorization level)
	if ( preg_match("/<meta[\s]+name=['\"]?aiocp_level['\"]?[\s]+content=['\"]?([^\"<>]*)/i", $page_code, $regs) ) {
		$page_data['level'] = trim($regs[1]);
	}
	else {
		$page_data['level'] = 0;
	}
	
	//get page language
	if ( preg_match("/<html([^>]*)>/i", $page_code, $regs) ) {
		if ( preg_match("/lang=['\"]?([^'\"<>]*)/i", $regs[1], $subregs) ) {
				$page_data['language'] = trim($subregs[1]);
			}
		else {
			$page_data['language'] = "";
		}
	}
	else {
		$page_data['language'] = "";
	}
	
	//get links list (eliminate url fragments #)
	preg_match_all("/(<frame[^>]*src[\s]*=|href[\s]*=|http-equiv=[\'\"]?refresh[\'\"]?[\s]*content=[\'\"][0-9]+;[\s]*url[\s]*=|window[\.]location[\s]*=|[\.]location[\.]replace[\s]*[(]|window[\.]open[\s]*[(])[\s]*[\'\"]?([^\'\"\#\)\> ]*)/i", $page_code, $regs);
	
	$i=0; //this will count the found URLs
	$page_data['links'] = Array();
	
	//get current path from current url
	preg_match("/([^\?\#]*)/i", $page_url, $regurl); //remove queries from link
	$current_path = substr($regurl[1], 0, - strlen(basename($regurl[1])));
	
	//resolve relative links to absolute and remove external links
	while(list($key, $linkurl) = each($regs[2])) { //for each found link
		
		$linkurl = trim($linkurl); //remove white spaces
		$linkurl = preg_replace("/(\?|\&|%3F|%26|\&amp;|%26amp%3B)PHPSESSID(=|%3D)[a-z0-9]{32,32}/i", "", $linkurl); //remove session variable PHPSESSID
		$linkurl = preg_replace("/(\?|\&|%3F|%26|\&amp;|%26amp%3B)altmenu(=|%3D)[0-1]{1}/i", "", $linkurl); //remove altmenu parameter
		//$linkurl = urldecode($linkurl); //decode url
		$linkurl = str_replace("&amp;", "&", $linkurl); //decode url
		
		if (!F_is_relative_link($linkurl)) { //found absolute link
			if (!strncasecmp($linkurl, K_PATH_SEARCH_START, strlen(K_PATH_SEARCH_START))) { //valid internal url
				if (F_check_excluded_urls($linkurl)) {
					$page_data['links'][$i++] = $linkurl;
				}
			}
		}
		else { //found relative link
			if ( !preg_match("/[\/]?mailto:|[\/]?javascript:|[\/]?news:|[\/]?aim:/i", $linkurl) ) { //if link is valid
				//resolve link
				if (!strncasecmp($linkurl, "/", 1)) { //link refer to the host root
					if (!strncasecmp(K_PATH_HOST.$linkurl, K_PATH_SEARCH_START, strlen(K_PATH_SEARCH_START))) { //valid internal url
						$current_url = K_PATH_HOST.$linkurl;
						if (F_check_excluded_urls($current_url)) {
							$page_data['links'][$i++] = $current_url;
						}
					}
				}
				else { //link is relative to the current path
					$current_url = F_resolve_url_path($current_path.$linkurl);
					if (F_check_excluded_urls($current_url)) {
						$page_data['links'][$i++] = $current_url;
					}
				}
			}
		}
	}
	
	//convert code to text
	$page_code = F_html_to_text($page_code, false, false);
	
	//put words in array
	$page_data['words'] = Array();
	$page_data['words'] = preg_split("/[\W\s]+/i", $page_code);
	
	return ($page_data);
}

// ------------------------------------------------------------
// the spider function to index site
// ------------------------------------------------------------
function F_site_search_indexer() {
	global $l, $db, $progress_log;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_get_page.'.CP_EXT);
	
	ini_set("memory_limit", K_MAX_MEMORY_LIMIT);
	set_time_limit(K_MAX_EXECUTION_TIME); //extend the maximum execution time
	
	define ("K_MIN_SEARCH_CHARS", 3); // minimum word lenght 
	
	// google sitemap default parameters
	define ("K_GOOGLE_PRIORITY", "0.5"); // default page priority
	define ("K_GOOGLE_CHANGEFREQ", "monthly"); // default change frequency
	define ("K_GOOGLE_SITEMAP_FILE", "../log/sitemap.xml"); // default sitemap file
	
	ini_set("memory_limit", K_MAX_MEMORY_LIMIT);
	set_time_limit(K_MAX_EXECUTION_TIME); //extend the maximum execution time to one day
	
	$links_list = Array(); //list of links to be processed
	$link_id = 0; //links list array index
	
	//get site links starting from first site page
	$links_list[$link_id] = K_PATH_SEARCH_FIRST_PAGE;
	
	//clean database
	$sql = "DELETE FROM ".K_TABLE_SEARCH_URL."";
	if(!$r = F_aiocpdb_query($sql, $db)) {
		F_display_db_error();
	}
	$sql = "DELETE FROM ".K_TABLE_SEARCH_DICTIONARY."";
	if(!$r = F_aiocpdb_query($sql, $db)) {
		F_display_db_error();
	}
	
	$page_num = 0; //count pages
	
  	// build a google sitemap on site root
	$sitemap = "";
	$sitemap .= "<"."?xml version=\"1.0\" encoding=\"UTF-8\"?".">\n";
	$sitemap .= "<urlset xmlns=\"http://www.google.com/schemas/sitemap/0.84\">\n";
	@unlink(K_GOOGLE_SITEMAP_FILE); //clear sitemap file if exist
	error_log($sitemap, 3, K_GOOGLE_SITEMAP_FILE);
	
	do {
		$raw_data = F_get_page_content($links_list[$link_id]);
		if ($raw_data) {
			$page_data = F_get_page_data($links_list[$link_id], $raw_data);
			if ($page_data) {
				$page_num++;
				$link_log = "".$page_num." - ".$links_list[$link_id].""; //output current link
				error_log($link_log."\n", 3, $progress_log); //create progress log file
				echo "<small>".$link_log."</small><br />\n"; //output links list (to keep browser live)
				//echo " "; //print something to keep browser live
				if (($page_num % 10) == 0) { //force flush output every 10 processed links
					echo "<!-- ".$page_num." -->\n"; flush(); //force flush output to browser
				}
				
				//put new links on links list
				while(list($key, $linkurl) = each($page_data['links'])) { //for each link found on the current page
					if ($linkurl) {
						if(!in_array($linkurl, $links_list)) { //if the link has not been already added on list
							array_push($links_list, $linkurl); //add link to list
						}
					}
				}
				
				// build a google sitemap on site root
				$sitemap = "";
				$sitemap .= "\t<url>\n";
				$sitemap .= "\t\t<loc>".$page_data['url']."</loc>\n";
				$sitemap .= "\t\t<lastmod>".substr($page_data['index_time'],0,10)."</lastmod>\n";
				$sitemap .= "\t\t<changefreq>".K_GOOGLE_CHANGEFREQ."</changefreq>\n";
				$sitemap .= "\t\t<priority>".K_GOOGLE_PRIORITY."</priority>\n";
				$sitemap .= "\t</url>\n";
				error_log($sitemap, 3, K_GOOGLE_SITEMAP_FILE);
				
				//put url on database table
				$sql = "INSERT IGNORE INTO ".K_TABLE_SEARCH_URL." (
					searchurl_url,
					searchurl_level,
					searchurl_content_type,
					searchurl_title,
					searchurl_description,
					searchurl_keywords,
					searchurl_language,
					searchurl_size,
					searchurl_index_time
				) VALUES (
					'".$page_data['url']."',
					'".$page_data['level']."',
					'".$page_data['content_type']."',
					'".addslashes($page_data['title'])."',
					'".addslashes($page_data['description'])."',
					'".addslashes($page_data['keywords'])."',
					'".$page_data['language']."',
					'".$page_data['size']."',
					'".$page_data['index_time']."'
				)";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$url_id = F_aiocpdb_insert_id();
				}
				
				//put words on database table
				while(list($word_position, $word) = each($page_data['words'])) { //for each found word
					if (strlen($word)>=K_MIN_SEARCH_CHARS) { // min number of indexed characters
						$sql = "INSERT IGNORE INTO ".K_TABLE_SEARCH_DICTIONARY." (
							searchdic_url_id,
							searchdic_word,
							searchdic_position
						) VALUES (
							'".$url_id."',
							'".addslashes($word)."',
							'".$word_position."'
						)";
						if(!$r = F_aiocpdb_query($sql, $db)) {
							F_display_db_error();
						}
					}
				}
			}
		}
		$link_id++; //go to next link
	} while ($link_id < count($links_list));
	
	// close google sitemap
	$sitemap = "</urlset>\n";
	error_log($sitemap, 3, K_GOOGLE_SITEMAP_FILE);
}

// ------------------------------------------------------------
// check if this url must be excluded from indexing process
// ------------------------------------------------------------
function F_check_excluded_urls($url_to_check) {
	
	// array of excluded pages from search indexing
	$excluded_pages = Array(
		"/admin/code/",
		"cp_html2xhtmlbasic",
		"cp_html2txt",
		"cp_forum_last_post",
		"cp_forum_view",
		"cp_show_ec_shopping_cart",
		".ps",
		".jpg",
		".jpeg",
		".gif",
		".png",
		".java",
		".class",
		".js",
		".jar",
		".gz",
		".zip",
		".css"
	);
	
	$url_data = parse_url($url_to_check);
	$current_url = $url_data['path'];
	
	while(list($key, $val) = each($excluded_pages)) { //for each excluded page
		if (stristr($current_url, $val)) {
			return FALSE;
		}
	}
	return TRUE;
}
//============================================================+
// END OF FILE                                                 
//============================================================+
?>