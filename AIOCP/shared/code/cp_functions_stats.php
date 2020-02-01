<?php
//============================================================+
// File name   : cp_functions_stats.php                        
// Begin       : 2002-05-09                                    
// Last Update : 2003-10-12                                    
//                                                             
// Description : Functions for site statistics                 
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
// search and return page id in page stats table
// add page in page stats table if not exist
// ------------------------------------------------------------
function F_get_stat_page_id($page) {
	global $l, $db;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	
	$page = preg_replace("/(\?|\&|%3F|%26|\&amp;|%26amp%3B)PHPSESSID(=|%3D)[a-z0-9]{32,32}/i", "", $page); //remove session variable PHPSESSID
	$page = addslashes($page);
	
	$sql = "SELECT statpage_id FROM ".K_TABLE_STATS_PAGES." WHERE statpage_url='".$page."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			return ($m['statpage_id']);
		}
		else { //add this page to stat pages
			$sqli = "INSERT IGNORE INTO ".K_TABLE_STATS_PAGES." (statpage_url) VALUES ('".$page."')";
			if(!$ri = F_aiocpdb_query($sqli, $db)) {
				F_display_db_error();
			}
			else {
				return (F_aiocpdb_insert_id());
			}
		}
	}
	else {
		F_display_db_error();
	}
	return "NULL";
}

// ------------------------------------------------------------
// return page url
// ------------------------------------------------------------
function F_get_stat_page($page_id) {
	global $l, $db;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	
	$sql = "SELECT statpage_url FROM ".K_TABLE_STATS_PAGES." WHERE statpage_id='".$page_id."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			return ($m['statpage_url']);
		}
	}
	else {
		F_display_db_error();
	}
	return "";
}

// ------------------------------------------------------------
// search and return page id in referer stats table
// add page in referer stats table if not exist
// ------------------------------------------------------------
function F_get_stat_referer_id($page) {
	global $l, $db;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	
	$page = preg_replace("/(\?|\&|%3F|%26|\&amp;|%26amp%3B)PHPSESSID(=|%3D)[a-z0-9]{32,32}/i", "", $page); //remove session variable PHPSESSID
	$page = addslashes($page);
	
	$sql = "SELECT statsreferer_id FROM ".K_TABLE_STATS_REFERER." WHERE statsreferer_url='".$page."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			return ($m['statsreferer_id']);
		}
		else { //add this page to stat pages
			$sqli = "INSERT IGNORE INTO ".K_TABLE_STATS_REFERER." (statsreferer_url) VALUES ('".$page."')";
			if(!$ri = F_aiocpdb_query($sqli, $db)) {
				F_display_db_error();
			}
			else {
				return (F_aiocpdb_insert_id());
			}
		}
	}
	else {
		F_display_db_error();
	}
	return "NULL";
}

// ------------------------------------------------------------
// return referer url
// ------------------------------------------------------------
function F_get_stat_referer($page_id) {
	global $l, $db;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	
	$sql = "SELECT statsreferer_url FROM ".K_TABLE_STATS_REFERER." WHERE statsreferer_id='".$page_id."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			return ($m['statsreferer_url']);
		}
	}
	else {
		F_display_db_error();
	}
	return "";
}

// ------------------------------------------------------------
// search and return user agent id in user agent stats table
// add user agent in user agent stats table if not exist
// ------------------------------------------------------------
function F_get_user_agent_id($user_agent) {
	global $l, $db;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	
	$user_agent = addslashes($user_agent);
	
	$sql = "SELECT statuseragent_id FROM ".K_TABLE_STATS_USER_AGENTS." WHERE statuseragent_name='".$user_agent."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			return ($m['statuseragent_id']);
		}
		else { //add this page to stat pages
			$sqli = "INSERT IGNORE INTO ".K_TABLE_STATS_USER_AGENTS." (statuseragent_name) VALUES ('".$user_agent."')";
			if(!$ri = F_aiocpdb_query($sqli, $db)) {
				F_display_db_error();
			}
			else {
				return (F_aiocpdb_insert_id());
			}
		}
	}
	else {
		F_display_db_error();
	}
	return "NULL";
}

// ------------------------------------------------------------
// return user agent
// ------------------------------------------------------------
function F_get_user_agent($user_agent_id) {
	global $l, $db;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	
	$sql = "SELECT statuseragent_name FROM ".K_TABLE_STATS_USER_AGENTS." WHERE statuseragent_id='".$user_agent_id."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			return ($m['statuseragent_name']);
		}
	}
	else {
		F_display_db_error();
	}
	return "";
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
