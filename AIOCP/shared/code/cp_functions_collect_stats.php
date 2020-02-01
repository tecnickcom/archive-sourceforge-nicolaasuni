<?php
//============================================================+
// File name   : cp_functions_collect_stats.php                
// Begin       : 2002-05-09                                    
// Last Update : 2003-11-18                                    
//                                                             
// Description : Functions for collect site statistics         
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

if (K_USE_SITE_STATISTICS) {F_collect_stats();}

// ------------------------------------------------------------
// record site statistics
// ------------------------------------------------------------
function F_collect_stats() {
	global  $l, $db, $selected_language;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_stats.'.CP_EXT);
	
	$page = K_PATH_HOST.$_SERVER['SCRIPT_NAME']; //absolute URL
	if ($_SERVER['QUERY_STRING']) {$page .= "?".$_SERVER['QUERY_STRING'];}
	
	$page_id = F_get_stat_page_id($page);
	$user_agent_id = F_get_user_agent_id($_SERVER['HTTP_USER_AGENT']);
	
	
	if (!isset($_SESSION['session_referer_page'])) { //user is arrived here from external site
		if (!isset($_SERVER['HTTP_REFERER'])) { //referer do not exist
			$referer_int_id = "NULL";
			$referer_ext_id = "NULL";
		}
		else { //referer exist
			$referer_int_id = "NULL";
			$referer_ext_id = F_get_stat_referer_id($_SERVER['HTTP_REFERER']); //search id in external referer
		}
	}
	else { //user is arrived here from a page on this site
		$referer_ext_id = "NULL";
		$referer_int_id = $_SESSION['session_referer_page'];
	}
	
	//assign this page to user session
	$_SESSION['session_referer_page'] = $page_id;
	
	if ($_SESSION['session_user_id']==1) {
		$usid = "NULL";
	}
	else {
		$usid = $_SESSION['session_user_id'];
	}
	$sql = "INSERT IGNORE INTO ".K_TABLE_STATS." (
		stats_datetime,
		stats_user_id,
		stats_user_ip,
		stats_user_agent,
		stats_language,
		stats_page,
		stats_referer_int,
		stats_referer_ext
	) VALUES (
		'".gmdate("Y-m-d H:i:s")."',
		".$usid.",
		'".$_SESSION['session_user_ip']."',
		'".$user_agent_id."',
		'".$selected_language."',
		'".$page_id."',
		".$referer_int_id.",
		".$referer_ext_id."
	)";
	if(!$r = F_aiocpdb_query($sql, $db)) {
		F_display_db_error();
	}
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
