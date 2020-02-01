<?php
//============================================================+
// File name   : cp_functions_banner.php                       
// Begin       : 2002-04-30                                    
// Last Update : 2005-06-14                                    
//                                                             
// Description : Functions for display and track banners       
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
// select and display a banner for the specified zone
// ------------------------------------------------------------
function F_show_banner($banner_zone) {
	global  $l, $db, $selected_language;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_stats.'.CP_EXT);
	require_once('../../shared/code/cp_functions_dynamic_pages.'.CP_EXT);
		
	if (!isset($banner_zone) OR ($banner_zone == 1)) {
		$banner_zone_id = 1;
	}
	else {
		//select banner zone ID
		$sql = "SELECT * FROM ".K_TABLE_BANNERS_ZONES." WHERE banzone_name='".$banner_zone."' LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$banner_zone_id = $m['banzone_id'];
			}
			else {
				$banner_zone_id = 1; //default banner zone
			}
		}
		else {
			F_display_db_error();
		}
	}
	
	$todaydate = gmdate("Y-m-d"); //the actual date (now)
	
	//variables to contain the selected banners data
	$banner_data_id = array();
	$banner_data_code = array();
	$banner_max_views = array();
	$banner_views_stats = array();
	$totalweight = array(); //encreasing sum of weights
	
	// select banners
	$sql = "SELECT * FROM ".K_TABLE_BANNERS." WHERE (
		banner_enabled=1 
		AND banner_zone='".$banner_zone_id."' 
		AND banner_language='".$selected_language."' 
		AND (banner_start_date<='".$todaydate."' OR banner_start_date='') 
		AND (banner_end_date>='".$todaydate."' OR banner_end_date='') 
		AND (banner_max_views>banner_views_stats OR banner_max_views='')
		) ORDER BY banner_weight";
	
	$i = 0;
	if($r = F_aiocpdb_query($sql, $db)) {
		while($mb = F_aiocpdb_fetch_array($r)) {
			$banner_data_id[$i] = $mb['banner_id'];
			$banner_data_code[$i] = $mb['banner_code'];
			$banner_max_views[$i] = $mb['banner_max_views'];
			$banner_views_stats[$i] = $mb['banner_views_stats'];
			
			//compensate banner weight (=1 ok; <1 late; >1 advance)
			$compensateweight = 1;
			if ($mb['banner_end_date'] AND $mb['banner_start_date'] AND ($mb['banner_max_views'] > 0) AND ($mb['banner_views_stats'] >= 0) ) {
				$compensateweight = ((time() - strtotime($mb['banner_start_date'])) * ($mb['banner_max_views'] - $mb['banner_views_stats']) / ((strtotime($mb['banner_end_date']) - time()) * ($mb['banner_views_stats']+1)));
			}
			
			//echo "<h1>DEBUG: ".$mb['banner_name']." = ".$compensateweight." | ".(time() - strtotime($mb['banner_start_date']))."</h1>"; //DEBUG
			
			//calculate new banner weight
			if ($i > 0) {
				$totalweight[$i] = ($mb['banner_weight'] * $compensateweight) + $totalweight[$i-1];
			} else {
				$totalweight[$i] = $mb['banner_weight'] * $compensateweight;
			}
			
			$totalweight[$i] = (int) round($totalweight[$i]);
			
			$i++;
		}
	}
	else {
		F_display_db_error();
	}
	
	//chek if banner list is void
	if ($i == 0) {return "";}
	
	$i--;
	
	
	//echo "".$totalweight[$i].""; //DEBUG /////////////////////////////////////////////
	
	
	//extract a random number between 0 and $totalweight[$i]
	mt_srand((double)microtime()*1000000);
	$randomnumber = mt_rand(0, $totalweight[$i]);
	
	$j = 0;
	
	//choose the banner
	while ($totalweight[$j] < $randomnumber) {
		$j++;
	}
	
	//verify end of campaign
	if ($banner_views_stats[$j] >= $banner_max_views[$j]) {
		$bannerenabled = 0;
	}
	else {
		$bannerenabled = 1;
	}
	
	//update banner stats
	$sql = "UPDATE IGNORE ".K_TABLE_BANNERS." SET banner_views_stats=(banner_views_stats+1), banner_enabled='".$bannerenabled."' WHERE banner_id=".$banner_data_id[$j]."";
	if(!$r = F_aiocpdb_query($sql, $db)) {
		F_display_db_error();
	}
	
	$banner_page = K_PATH_HOST.$_SERVER['SCRIPT_NAME']; //absolute URL
	if ($_SERVER['QUERY_STRING']) {$banner_page .= "?".$_SERVER['QUERY_STRING'];}
	
	$banner_page_id = F_get_stat_page_id($banner_page);
	$banner_user_agent_id = F_get_user_agent_id($_SERVER['HTTP_USER_AGENT']);
	
	if ($_SESSION['session_user_id']==1) {
		$usid = "NULL";
	}
	else {
		$usid = $_SESSION['session_user_id'];
	}
	
	$sql = "INSERT IGNORE INTO ".K_TABLE_BANNERS_STATS." (
		banstat_banner_id,
		banstat_action,
		banstat_time,
		banstat_page,
		banstat_user_id,
		banstat_user_ip,
		banstat_user_agent
	) VALUES (
		'".$banner_data_id[$j]."',
		'0',
		'".gmdate("Y-m-d H:i:s")."',
		'".$banner_page_id."',
		".$usid.",
		'".$_SESSION['session_user_ip']."',
		".$banner_user_agent_id."
	)";
	if(!$r = F_aiocpdb_query($sql, $db)) {
		F_display_db_error();
	}
	
	$return_code = "<a href=\"../code/cp_banner.".CP_EXT."?banner=".$banner_data_id[$j]."&amp;page=".urlencode($banner_page)."\" target=\"_blank\">".F_evaluate_modules($banner_data_code[$j])."</a>";
	
	return $return_code;
}


// ------------------------------------------------------------
// process banner click
// ------------------------------------------------------------
function F_banner_click($bannerid, $page) {
	global  $l, $db;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_stats.'.CP_EXT);
	
	if ( (!$bannerid) OR (!$page) ) {return FALSE;}
	
	// select banner
	$sql = "SELECT * FROM ".K_TABLE_BANNERS." WHERE banner_id='".$bannerid."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			if ($m['banner_enabled']) { //if enabled
				//update banner stats
				$sqla = "UPDATE IGNORE ".K_TABLE_BANNERS." SET banner_clicks_stats=(banner_clicks_stats+1) WHERE banner_id=".$bannerid."";
				if(!$ra = F_aiocpdb_query($sqla, $db)) {
					F_display_db_error();
				}
				
				$banner_page_id = F_get_stat_page_id($page);
				$banner_user_agent_id = F_get_user_agent_id($_SERVER['HTTP_USER_AGENT']);
				
				if ($_SESSION['session_user_id']==1) {
					$usid = "NULL";
				}
				else {
					$usid = $_SESSION['session_user_id'];
				}
	
				$sqlb = "INSERT IGNORE INTO ".K_TABLE_BANNERS_STATS." (
					banstat_banner_id,
					banstat_action,
					banstat_time,
					banstat_page,
					banstat_user_id,
					banstat_user_ip,
					banstat_user_agent
				) VALUES (
					'".$bannerid."',
					'1',
					'".gmdate("Y-m-d H:i:s")."',
					'".$banner_page_id."',
					".$usid.",
					'".$_SESSION['session_user_ip']."',
					".$banner_user_agent_id."
				)";
				if(!$rb = F_aiocpdb_query($sqlb, $db)) {
					F_display_db_error();
				}
				
				//go to banner link
				echo "<"."?xml version=\"1.0\" encoding=\"".$l['a_meta_charset']."\"?".">\n";
				echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"DTD/xhtml1-transitional.dtd\">\n";
				echo "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"".$l['a_meta_language']."\" lang=\"".$l['a_meta_language']."\" dir=\"".$l['a_meta_dir']."\">\n";
				echo "<head>\n";
				echo "<meta http-equiv=\"Refresh\" content=\"0;url=".$m['banner_link']."\" />\n";
				echo "<meta name=\"robots\" content=\"index,follow\" />\n";
				echo "</head>\n";
				echo "<body>";
				echo "<a href=\"".htmlentities(urldecode($m['banner_link']))."\" target=\"_top\">ENTER</a>";
				echo "</body>";
				echo "</html>";
			}
		}
		else {
			return FALSE;
		}
	}
	else {
		F_display_db_error();
	}
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
