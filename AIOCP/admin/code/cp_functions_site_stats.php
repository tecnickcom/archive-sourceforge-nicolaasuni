<?php
//============================================================+
// File name   : cp_functions_site_stats.php                   
// Begin       : 2002-05-20                                    
// Last Update : 2008-07-06
//                                                             
// Description : function to elaborate and display site stats  
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
// Display site statistics
// ------------------------------------------------------------
function F_site_statistics($selectby_query, $where_query, $ntoplimit) {
	global $l, $db;
	global $timeinterval, $statday, $statmonth, $statyear;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	$barswidth = 5; //width of the graph bar
	$barsmaxlength = 150; //max length of graph bar
	
	$l_month_names = array($l['w_january'], $l['w_february'], $l['w_march'], $l['w_april'], $l['w_may'], $l['w_june'], $l['w_july'], $l['w_august'], $l['w_september'], $l['w_october'], $l['w_november'], $l['w_december']);
	
	$l_day_names = array($l['w_sunday'], $l['w_monday'], $l['w_tuesday'], $l['w_wednesday'], $l['w_thursday'], $l['w_friday'], $l['w_saturday']);
	
	$rowodd = 0;
	
	//general stats query
	$sql  = "SELECT ".$selectby_query."(stats_datetime) AS time, COUNT(stats_id) AS views, COUNT(DISTINCT stats_user_ip, stats_user_agent) AS users, COUNT(DISTINCT stats_user_id) AS regusers, COUNT(stats_referer_ext) AS referers FROM ".K_TABLE_STATS." WHERE 1 ".$where_query." GROUP BY time";
	
	//calculate some statistics parameters:
	
	$max_views = 0; //max number of page views in time unit
	$max_users = 0; //max number of users in time unit
	$max_referers = 0; //max number of external referers in time unit
	
	$total_views = 0;
	$total_users = 0;
	$total_regusers = 0;
	$total_referers = 0;
	
	$total_records = 0; //total number of records
	$prevtime = 0;
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			if ($max_views < $m['views']) {
				$max_views = $m['views'];
			}
			if ($max_users < $m['users']) {
				$max_users = $m['users'];
			}
			if ($max_referers < $m['referers']) {
				$max_referers = $m['referers'];
			}
			
			$total_views += $m['views'];
			$total_users += $m['users'];
			$total_regusers += $m['regusers'];
			$total_referers += $m['referers'];
			++$total_records;
		}
	}
	else {
		F_display_db_error();
	}
	
	if (!$total_records) {return FALSE;} //void database
	
	echo "<table class=\"edge\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">";
	echo "<tr class=\"edge\">";
	echo "<td class=\"edge\"><b>".$l['w_general_statistics']."</b></td>";
	echo "</tr>";
	echo "<tr class=\"edge\">";
	echo "<td class=\"edge\">";
	
	echo "<table class=\"fill\" border=\"0\" cellspacing=\"2\" cellpadding=\"1\">";
	
	echo "<tr class=\"fill\">";
	echo "<th class=\"fillO\" valign=\"top\">&nbsp;".$l['w_time']."</th>";
	
	echo "<th class=\"fillE\" valign=\"top\" colspan=\"2\">";
	echo "<img src=\"../../shared/code/cp_button_and_bar.".CP_EXT."?s=bargraph&amp;w=10&amp;h=10&amp;dcr=0&amp;dcg=127&amp;dcb=255\" border=\"0\" width=\"10\" height=\"10\" /> ";
	echo "".$l['w_pages']."";
	echo "</th>";
	
	echo "<th class=\"fillO\" valign=\"top\" colspan=\"2\">";
	echo "<img src=\"../../shared/code/cp_button_and_bar.".CP_EXT."?s=bargraph&amp;w=10&amp;h=10&amp;dcr=255&amp;dcg=255&amp;dcb=0\" border=\"0\" width=\"10\" height=\"10\" /> ";
	echo "".$l['w_users']."";
	echo "</th>";
	
	echo "<th class=\"fillE\" valign=\"top\" colspan=\"2\">";
	echo "<img src=\"../../shared/code/cp_button_and_bar.".CP_EXT."?s=bargraph&amp;w=10&amp;h=10&amp;dcr=0&amp;dcg=255&amp;dcb=0\" border=\"0\" width=\"10\" height=\"10\" /> ";
	echo "".$l['w_registered']."";
	echo "</th>";
	
	echo "<th class=\"fillO\" valign=\"top\" colspan=\"2\">";
	echo "<img src=\"../../shared/code/cp_button_and_bar.".CP_EXT."?s=bargraph&amp;w=10&amp;h=10&amp;dcr=255&amp;dcg=0&amp;dcb=0\" border=\"0\" width=\"10\" height=\"10\" /> ";
	echo "".$l['w_referers']."";
	echo "</th>";
	
	echo "<th class=\"fillE\" valign=\"top\">&nbsp;".$l['w_graph']."</th>";
	echo "</tr>";
	
	//UNITS OF MEASURES
	echo "<tr class=\"fill\">";
	echo "<th class=\"fillO\" valign=\"top\">";
	if ($timeinterval == "hour") {
		$time_unit = $l['w_hour'];
	}
	elseif ($timeinterval == "day") {
		$time_unit = $l['w_day'];
	}
	elseif  ($timeinterval == "month") {
		$time_unit = $l['w_month'];
	}
	else {
		$time_unit = $l['w_year'];
	}
	echo "[".$time_unit."]";
	echo "</th>";
	
	echo "<th class=\"fillE\" valign=\"top\">[ # ]</th>";
	echo "<th class=\"fillE\" valign=\"top\">[ % ]</th>";
	echo "<th class=\"fillO\" valign=\"top\">[ # ]</th>";
	echo "<th class=\"fillO\" valign=\"top\">[ % ]</th>";
	echo "<th class=\"fillE\" valign=\"top\">[ # ]</th>";
	echo "<th class=\"fillE\" valign=\"top\">[ % ]</th>";
	echo "<th class=\"fillO\" valign=\"top\">[ # ]</th>";
	echo "<th class=\"fillO\" valign=\"top\">[ % ]</th>";
	echo "<th class=\"fillE\" valign=\"top\">&nbsp;</th>";
	echo "</tr>";
	
	
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			//change style for each row
			if (isset($rowodd) AND ($rowodd)) {
				$rowclass = "O";
				$rowodd=0;
			} else {
				$rowclass = "E";
				$rowodd=1;
			}
			
			echo "<tr class=\"fill".$rowclass."\">";
			echo "<td class=\"fill".$rowclass."O\" align=\"right\">&nbsp;";
			if ($timeinterval == "month") {
				echo $l_month_names[$m['time']-1]." (".$m['time'].")";
			}
			elseif ($timeinterval == "day") {
				echo $l_day_names[round(date("w",mktime(0,0,0,$statmonth,$m['time'],$statyear)))]." ".$m['time'];
			}
			else {
				echo $m['time'];
			}
			echo "</td>";
			
			echo "<td class=\"fill".$rowclass."E\" align=\"right\">&nbsp;".$m['views']."</td>";
			if ($total_views > 0) {
				$temp = round(($m['views']/$total_views)*100,2);
			}
			else {
				$temp = "&nbsp;";
			}
			echo "<td class=\"fill".$rowclass."E\" align=\"right\">&nbsp;".$temp."</td>";
			
			echo "<td class=\"fill".$rowclass."O\" align=\"right\">&nbsp;".$m['users']."</td>";
			if ($total_users > 0) {
				$temp = round(($m['users']/$total_users)*100,2);
			}
			else {
				$temp = "&nbsp;";
			}
			echo "<td class=\"fill".$rowclass."O\" align=\"right\">&nbsp;".$temp."</td>";
			
			echo "<td class=\"fill".$rowclass."E\" align=\"right\">&nbsp;".$m['regusers']."</td>";
			if ($m['users'] > 0) {
				$temp = round(($m['regusers']/$m['users'])*100,2);
			}
			else {
				$temp = "&nbsp;";
			}
			echo "<td class=\"fill".$rowclass."E\" align=\"right\">&nbsp;".$temp."</td>";
			
			echo "<td class=\"fill".$rowclass."O\" align=\"right\">&nbsp;".$m['referers']."</td>";
			if ($total_referers > 0) {
				$temp = round(($m['referers']/$total_referers)*100,2);
			}
			else {
				$temp = "&nbsp;";
			}
			echo "<td class=\"fill".$rowclass."O\" align=\"right\">&nbsp;".$temp."</td>";
			
			echo "<td class=\"fill".$rowclass."E\">";
			//pages bar
			if($max_views> 0) {$barlength = round(($m['views'] * $barsmaxlength)/$max_views);}
			else {$barlength = 0;}
			echo "<img src=\"../../shared/code/cp_button_and_bar.".CP_EXT."?s=bargraph&amp;w=".$barlength."&amp;h=".$barswidth."&amp;dcr=0&amp;dcg=128&amp;dcb=255\" border=\"0\" width=\"".$barlength."\" height=\"".$barswidth."\" class=\"blockview\" />";
			
			//users bar
			if($max_users) {$barlength = round(($m['users'] * $barsmaxlength)/$max_users);}
			else {$barlength = 0;}
			echo "<img src=\"../../shared/code/cp_button_and_bar.".CP_EXT."?s=bargraph&amp;w=".$barlength."&amp;h=".$barswidth."&amp;dcr=255&amp;dcg=255&amp;dcb=0\" border=\"0\" width=\"".$barlength."\" height=\"".$barswidth."\" class=\"blockview\" />";
			
			//registered users bar
			if($max_users) {$barlength = round(($m['regusers'] * $barsmaxlength)/$max_users);}
			else {$barlength = 0;}
			echo "<img src=\"../../shared/code/cp_button_and_bar.".CP_EXT."?s=bargraph&amp;w=".$barlength."&amp;h=".$barswidth."&amp;dcr=0&amp;dcg=255&amp;dcb=0\" border=\"0\" width=\"".$barlength."\" height=\"".$barswidth."\" class=\"blockview\" />";
			
			//referers bar
			if($max_referers) {$barlength = round(($m['referers'] * $barsmaxlength)/$max_referers);}
			else {$barlength = 0;}
			echo "<img src=\"../../shared/code/cp_button_and_bar.".CP_EXT."?s=bargraph&amp;w=".$barlength."&amp;h=".$barswidth."&amp;dcr=255&amp;dcg=0&amp;dcb=0\" border=\"0\" width=\"".$barlength."\" height=\"".$barswidth."\" class=\"blockview\" />";
			echo "</td>";
			
			echo "</tr>";
			
		}
	}
	else {
		F_display_db_error();
	}
	
	echo "<tr class=\"fillE\">";
	echo "<td class=\"fillE0\" colspan=\"10\"><hr /></td>";
	echo "</tr>";
	
	//TOTAL
	echo "<tr class=\"fillO\">";
	echo "<td class=\"fillO0\" align=\"right\">&nbsp;<b>".$l['w_total'].":</b></td>";
	echo "<td class=\"fillOE\" align=\"right\">&nbsp;<b>".$total_views."</b></td>";
	echo "<td class=\"fillOE\" align=\"right\">&nbsp;</td>";
	echo "<td class=\"fillOO\" align=\"right\">&nbsp;<b>".$total_users."</b></td>";
	echo "<td class=\"fillOO\" align=\"right\">&nbsp;</td>";
	echo "<td class=\"fillOE\" align=\"right\">&nbsp;<b>".$total_regusers."</b></td>";
	echo "<td class=\"fillOE\" align=\"right\">&nbsp;</td>";
	echo "<td class=\"fillOO\" align=\"right\">&nbsp;<b>".$total_referers."</b></td>";
	echo "<td class=\"fillOO\" align=\"right\">&nbsp;</td>";
	echo "<td class=\"fillOE\" align=\"right\">&nbsp;</td>";
	echo "</tr>";
	
	//AVERAGE
	echo "<tr class=\"fillE\">";
	echo "<td class=\"fillE0\" align=\"right\">&nbsp;<b>".$l['w_average'].":</b></td>";
	echo "<td class=\"fillEE\" align=\"right\">&nbsp;<b>".round($total_views/$total_records, 2)."</b></td>";
	echo "<td class=\"fillEE\" align=\"right\">&nbsp;</td>";
	echo "<td class=\"fillEO\" align=\"right\">&nbsp;<b>".round($total_users/$total_records, 2)."</b></td>";
	echo "<td class=\"fillEO\" align=\"right\">&nbsp;</td>";
	echo "<td class=\"fillEE\" align=\"right\">&nbsp;<b>".round($total_regusers/$total_records, 2)."</b></td>";
	echo "<td class=\"fillEE\" align=\"right\">&nbsp;</td>";
	echo "<td class=\"fillEO\" align=\"right\">&nbsp;<b>".round($total_referers/$total_records, 2)."</b></td>";
	echo "<td class=\"fillEO\" align=\"right\">&nbsp;</td>";
	echo "<td class=\"fillEE\">&nbsp;<b>[ # / ".$time_unit." ]</b></td>";
	echo "</tr>";
	
	echo"</table>";
	
	echo "</td>";
	echo "</tr>";
	echo "</table>";
	
	
	// ---------------------------------------------
	// TOP STATISTICS 
	// ---------------------------------------------
	
	echo "<br />";
	
	// TOP PAGES
	$sql  = "SELECT stats_page, statpage_url AS name, COUNT(stats_page) AS activity FROM ".K_TABLE_STATS.", ".K_TABLE_STATS_PAGES." WHERE (stats_page=statpage_id) ".$where_query." GROUP BY stats_page ORDER BY activity DESC LIMIT 0,".$ntoplimit."";
	F_site_top_statistics($l['w_top_pages'], $sql, $l['w_page'], $total_views, "name", "", "_blank");
	echo "<br />";
	
	// TOP REFERERS
	$sql  = "SELECT stats_referer_ext, statsreferer_url AS name, COUNT(stats_referer_ext) AS activity FROM ".K_TABLE_STATS.", ".K_TABLE_STATS_REFERER." WHERE (stats_referer_ext=statsreferer_id) ".$where_query." GROUP BY stats_referer_ext ORDER BY activity DESC LIMIT 0,".$ntoplimit."";
	F_site_top_statistics($l['w_top_referers'], $sql, $l['w_referer'], $total_referers, "name", "", "_blank");
	echo "<br />";
	
	// TOP USER AGENTS
	$sql  = "SELECT statuseragent_id, statuseragent_name AS name, COUNT(DISTINCT stats_user_agent, stats_user_ip) AS activity FROM ".K_TABLE_STATS.", ".K_TABLE_STATS_USER_AGENTS." WHERE (stats_user_agent=statuseragent_id) ".$where_query." GROUP BY stats_user_agent ORDER BY activity DESC LIMIT 0,".$ntoplimit."";
	F_site_top_statistics($l['w_top_user_agents'], $sql, $l['w_user_agent'], $total_users, "", "", "");
	echo "<br />";
	
	// TOP REGISTERD USERS
	$sql  = "SELECT stats_user_id, user_name AS name, COUNT(stats_user_id) AS activity FROM ".K_TABLE_STATS.", ".K_TABLE_USERS." WHERE (stats_user_id=user_id) ".$where_query." GROUP BY stats_user_id ORDER BY activity DESC LIMIT 0,".$ntoplimit."";
	F_site_top_statistics($l['w_top_reg_users'], $sql, $l['w_user'], $total_views, "stats_user_id", "../code/cp_user_profile.".CP_EXT."?user_id=", "");
	echo "<br />";
	
	// TOP IP
	$sql  = "SELECT stats_user_ip AS name, COUNT(stats_user_ip) AS activity FROM ".K_TABLE_STATS." WHERE 1 ".$where_query." GROUP BY stats_user_ip ORDER BY activity DESC LIMIT 0,".$ntoplimit."";
	F_site_top_statistics($l['w_top_user_ip'], $sql, $l['w_userip'], $total_views, "", "", "");
	echo "<br />";
	
	// TOP LANGUAGES
	$sql  = "SELECT stats_language AS name, COUNT(stats_language) AS activity FROM ".K_TABLE_STATS." WHERE 1 ".$where_query." GROUP BY stats_language ORDER BY activity DESC LIMIT 0,".$ntoplimit."";
	F_site_top_statistics($l['w_top_languages'], $sql, $l['w_language'], $total_views, "", "", "");
	echo "<br />";
	
	// TOP ENTRY PAGES
	$sql  = "SELECT stats_page, statpage_url AS name, COUNT(stats_page) AS activity FROM ".K_TABLE_STATS.", ".K_TABLE_STATS_PAGES." WHERE (stats_page=statpage_id) AND (stats_referer_int IS NULL) ".$where_query." GROUP BY stats_page ORDER BY activity DESC LIMIT 0,".$ntoplimit."";
	F_site_top_statistics($l['w_top_entry_pages'], $sql, $l['w_page'], $total_views, "name", "", "_blank");
	echo "<br />";
	
	
	// TOP EXIT PAGES (complex calculus)
	//create temporary table to store exit pages data
	$sql  = "CREATE TEMPORARY TABLE tmp_stats_exit_pages (activity int(10) unsigned NOT NULL, name varchar(255) NOT NULL default '') TYPE=MyISAM";
	if(!$r = F_aiocpdb_query($sql, $db)) {
		F_display_db_error();
	}
	//select pages
	$sql  = "SELECT stats_page, statpage_url, COUNT(stats_page) AS activity FROM ".K_TABLE_STATS.", ".K_TABLE_STATS_PAGES." WHERE (stats_page=statpage_id) ".$where_query." GROUP BY stats_page LIMIT 0,".$ntoplimit."";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			//select referers
			$sqlr  = "SELECT COUNT(stats_referer_int) AS activity FROM ".K_TABLE_STATS." WHERE stats_referer_int=".$m['stats_page']." ".$where_query." GROUP BY stats_referer_int LIMIT 1";
			if($rr = F_aiocpdb_query($sqlr, $db)) {
				if($mr = F_aiocpdb_fetch_array($rr)) {
					$exit_requests = $m['activity'] - $mr['activity'];
				}
				else {
					$exit_requests = 0;
				}
				//add data to temporary table
				if ($exit_requests > 0) {
					$sqlex  = "INSERT IGNORE INTO tmp_stats_exit_pages (activity, name) VALUES ('".$exit_requests."','".$m['statpage_url']."')";
					if(!$rex = F_aiocpdb_query($sqlex, $db)) {
						F_display_db_error();
					}
				}
			}
			else {
				F_display_db_error();
			}
		}
	}
	else {
		F_display_db_error();
	}
	//display top exit pages stats
	$sql  = "SELECT * FROM tmp_stats_exit_pages ORDER BY activity DESC";
	F_site_top_statistics($l['w_top_exit_pages'], $sql, $l['w_page'], $total_views, "name", "", "_blank");
	
	//delete temporary table
	$sql  = "DROP TABLE IF EXISTS tmp_stats_exit_pages";
	if(!$r = F_aiocpdb_query($sql, $db)) {
		F_display_db_error();
	}
	echo "<br />";

}



// ------------------------------------------------------------
// Display TOP stats
// ------------------------------------------------------------
function F_site_top_statistics($title, $sql, $statname, $divisor, $hreffield, $href, $target) {
	global $l, $db;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	$barswidth = 15;
	$barsmaxlength = 100;
	
	echo "<table class=\"edge\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">";
	echo "<tr class=\"edge\">";
	echo "<th class=\"edge\" align=\"left\">".$title."</th>";
	echo "</tr>";
	echo "<tr class=\"edge\">";
	echo "<td class=\"edge\">";
	echo "<table class=\"fill\" border=\"0\" cellspacing=\"2\" cellpadding=\"1\">";
	echo "<tr class=\"fill\">";
	echo "<th class=\"fillO\" valign=\"top\">&nbsp;</th>";
	echo "<th class=\"fillE\" valign=\"top\">&nbsp;[ # ]</th>";
	echo "<th class=\"fillO\" valign=\"top\">&nbsp;[ % ]</th>";
	echo "<th class=\"fillE\" valign=\"top\">&nbsp;".$l['w_graph']."</th>";
	echo "<th class=\"fillO\" valign=\"top\">&nbsp;".$statname."</th>";
	echo "</tr>";
	
	$item_number = 0;
	
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			
			if (!$item_number) { //normalize graphic bars length to first value
				$barsmaxlength = round($barsmaxlength * $divisor / $m['activity']);
			}
			$item_number++;
			
			//change style for each row
			if (isset($rowodd) AND ($rowodd)) {
				$rowclass = "O";
				$rowodd=0;
			} else {
				$rowclass = "E";
				$rowodd=1;
			}
			
			echo "<tr class=\"fill".$rowclass."\">";
			echo "<td class=\"fill".$rowclass."O\" align=\"right\"><b>".$item_number."</b></td>";
			echo "<td class=\"fill".$rowclass."E\" align=\"right\">&nbsp;".$m['activity']."</td>";
			echo "<td class=\"fill".$rowclass."O\" align=\"right\">&nbsp;";
			if ($divisor) {
				echo round(($m['activity']/$divisor)*100,2);
				$barlength = round(($m['activity'] * $barsmaxlength)/$divisor);
			}
			else {
				echo "&nbsp;";
				$barlength = 0;
			}
			echo "</td>";
			//Activity bar
			echo "<td class=\"fill".$rowclass."E\">";
			echo "<img src=\"../../shared/code/cp_button_and_bar.".CP_EXT."?s=bargraph&amp;w=".$barlength."&amp;h=".$barswidth."&amp;dcr=0&amp;dcg=128&amp;dcb=255\" border=\"0\" width=\"".$barlength."\" height=\"".$barswidth."\" class=\"blockview\" />";
			echo"</td>";
			echo "<td class=\"fill".$rowclass."O\">&nbsp;";
			if ($hreffield) {
				if(!$target) {
					$target = "_self";
				}
				echo "<a href=\"".$href.$m[$hreffield]."\" target=\"".$target."\">".htmlentities($m['name'], ENT_NOQUOTES, $l['a_meta_charset'])."</a>";
			}
			else {
				echo $m['name'];
			}
			echo "</td>";
			echo "</tr>";
		}
	}
	else {
		F_display_db_error();
	}
	echo"</table>";
	
	echo "</td>";
	echo "</tr>";
	echo "</table>";
}



// ------------------------------------------------------------
// Display raw site stats
// ------------------------------------------------------------
function F_site_raw_statistics($wherequery) {
	global $l, $db;
	global $order_field, $orderdir, $firstrow, $rowsperpage;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_page.'.CP_EXT);
	require_once('../../shared/code/cp_functions_stats.'.CP_EXT);
	
	if(!$firstrow) {$firstrow="0";}
	else {$firstrow=intval($firstrow);} 
	if(!$rowsperpage) {$rowsperpage = K_MAX_ROWS_PER_PAGE;}
	else {$rowsperpage=intval($rowsperpage);}
	
	if(!$order_field) {$order_field = "stats_datetime";}
	else {$order_field = addslashes(preg_replace("/[\s]+/i", " ", $order_field));}
	if(!$orderdir) {$orderdir=0; $nextorderdir=1; $full_order_field = $order_field;}
	else {$orderdir=1; $nextorderdir=0; $full_order_field = $order_field." DESC";}
	$full_order_field = urldecode($full_order_field);
	
	if (!$wherequery) {
		$wherequery = "1";
		$sql = "SELECT * FROM ".K_TABLE_STATS." WHERE 1 ORDER BY ".$full_order_field." LIMIT ".$firstrow.",".$rowsperpage."";
	}
	else {
		$sql = "SELECT * FROM ".K_TABLE_STATS." WHERE 1 AND ".$wherequery." ORDER BY ".$full_order_field." LIMIT ".$firstrow.",".$rowsperpage."";
	}
	
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			// -- Table structure with links:
			echo "<table class=\"edge\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">";
			echo "<tr class=\"edge\">";
			echo "<td class=\"edge\">";
			echo "<table class=\"fill\" border=\"0\" cellspacing=\"2\" cellpadding=\"1\">";
			echo "<tr class=\"fill\">";
			echo "<th class=\"fillO\"><a href=\"javascript:FJ_submit_sitestats_form(0,'stats_datetime','".$nextorderdir."');\">".$l['w_time']."</a></th>";
			echo "<th class=\"fillE\"><a href=\"javascript:FJ_submit_sitestats_form(0,'stats_page','".$nextorderdir."');\">".$l['w_page']."</a></th>";
			echo "<th class=\"fillO\"><a href=\"javascript:FJ_submit_sitestats_form(0,'stats_user_id','".$nextorderdir."');\">".$l['w_user']."</a></th>";
			echo "<th class=\"fillE\"><a href=\"javascript:FJ_submit_sitestats_form(0,'stats_user_ip','".$nextorderdir."');\">".$l['w_ip']."</a></th>";
			echo "<th class=\"fillO\"><a href=\"javascript:FJ_submit_sitestats_form(0,'stats_language','".$nextorderdir."');\">".$l['w_language']."</a></th>";
			echo "<th class=\"fillE\"><a href=\"javascript:FJ_submit_sitestats_form(0,'stats_user_agent','".$nextorderdir."');\">".$l['w_user_agent']."</a></th>";
			echo "<th class=\"fillO\"><a href=\"javascript:FJ_submit_sitestats_form(0,'stats_referer_ext','".$nextorderdir."');\">".$l['w_referer']."</a></th>";
			echo "</tr>";
			
			do {
				//change style for each row
				if (isset($rowodd) AND ($rowodd)) {
					$rowclass = "O";
					$rowodd=0;
				} else {
					$rowclass = "E";
					$rowodd=1;
				}
				
				echo "<tr class=\"fill".$rowclass."\">";
				echo "<td class=\"fill".$rowclass."O\" style=\"white-space:nowrap\">&nbsp;".$m['stats_datetime']."</td>";
				
				echo "<td class=\"fill".$rowclass."E\" style=\"white-space:nowrap\">&nbsp;";
				if ($m['stats_page']) {
					$current_page = F_get_stat_page($m['stats_page']);
					echo "<a href=\"".$current_page."\" target=\"_blank\">".$current_page."</a>";
				}
				else {
					echo "&nbsp;";
				}
				echo "</td>";

				echo "<td class=\"fill".$rowclass."O\" style=\"white-space:nowrap\">&nbsp;";
				if(!$m['stats_user_id']) {$m['stats_user_id']=1;}
				$sqlu = "SELECT user_name FROM ".K_TABLE_USERS." WHERE user_id=".$m['stats_user_id']." LIMIT 1";
				if($ru = F_aiocpdb_query($sqlu, $db)) {
					if($mu = F_aiocpdb_fetch_array($ru)) {
						if ($m['stats_user_id'] > 1) {
						echo "<a href=\"cp_user_profile.".CP_EXT."?user_id=".$m['stats_user_id']."\">".htmlentities($mu['user_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</a>";
						}
						else {
							echo htmlentities($mu['user_name'], ENT_NOQUOTES, $l['a_meta_charset']);
						}
					}
				}
				else {
					F_display_db_error();
				}
				echo "</td>";
				echo "<td class=\"fill".$rowclass."E\" style=\"white-space:nowrap\">&nbsp;".$m['stats_user_ip']."</td>";
				echo "<td class=\"fill".$rowclass."O\" style=\"white-space:nowrap\">&nbsp;".$m['stats_language']."</td>";
				echo "<td class=\"fill".$rowclass."E\" style=\"white-space:nowrap\">&nbsp;";
				if ($m['stats_user_agent']) {
					echo F_get_user_agent($m['stats_user_agent']);
				}
				else {
					echo "&nbsp;";
				}
				echo "</td>";
				if ($m['stats_referer_int']) {
					$refurl = F_get_stat_page($m['stats_referer_int']);
					echo "<td class=\"fill".$rowclass."O\" style=\"white-space:nowrap\">&nbsp;<a href=\"".$refurl."\" target=\"_blank\">".$refurl."</a></td>";
				}
				elseif ($m['stats_referer_ext']) {
					$refurl = F_get_stat_referer($m['stats_referer_ext']);
					echo "<td class=\"fill".$rowclass."O\" style=\"white-space:nowrap\">&nbsp;<a href=\"".$refurl."\" target=\"_blank\">".$refurl."</a></td>";
				}
				else {
					echo "<td class=\"fill".$rowclass."O\">&nbsp</td>";
				}
				
			} while($m = F_aiocpdb_fetch_array($r));
			echo"</table>";
			echo "</td>";
			echo "</tr>";
			echo "</table>";
			
			// ---------------------------------------------------------------
			// -- page jumper (menu for successive pages)
			$sql = "SELECT count(*) AS total FROM ".K_TABLE_STATS." WHERE ".$wherequery."";
			if (!empty($order_field)) {$param_array = "&amp;order_field=".urlencode($order_field)."";}
			if (!empty($orderdir)) {$param_array .= "&amp;orderdir=".$orderdir."";}
			$param_array .= "&amp;submitted=1";
			F_show_page_navigator($_SERVER['SCRIPT_NAME'], $aiocp_dp, $sql, $firstrow, $rowsperpage, $param_array);
		}
		else {
			F_print_error("MESSAGE", $l['m_search_void']);
		}
	}
	else {
		F_display_db_error();
	}
	
	return TRUE;
}

// ------------------------------------------------------------
// Show select form for site stats (filters)
// ------------------------------------------------------------
function F_show_select_site_stats($sqlquery) {
	global $l, $db, $selected_language;
	global $order_field, $orderdir, $firstrow, $rowsperpage;
	global $timeinterval, $timechange;
	global $statday, $daychange;
	global $statmonth, $monthchange;
	global $statyear, $yearchange;
	global $menu_mode, $forcedelete;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	
	$userid = $_SESSION['session_user_id'];
	$userlevel = $_SESSION['session_user_level'];
	
	
	switch($menu_mode) {
		case unhtmlentities($l['w_delete']):
	case $l['w_delete']:{
		F_stripslashes_formfields(); // ask confirmation
			F_print_error("WARNING", $l['m_delete_confirm']);
			?>
			<p><?php echo $l['t_warning'].": ".$l['d_site_stats_delete']; ?></p>
			<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_delete" id="form_delete">
			<input type="hidden" name="menu_mode" id="menu_mode" value="forcedelete" />
			<input type="hidden" name="forcedelete" id="forcedelete" value="" />
			<?php 
			F_submit_button("form_delete","forcedelete",$l['w_delete']);
			F_submit_button("form_delete","forcedelete",$l['w_cancel']);
			?>
			</form>
			<?php
			break;
		}
	
		case "forcedelete": { // Delete newscat
			if($forcedelete == $l['w_delete']) { //check if delete button has been pushed (redundant check)
				$sql = "DELETE FROM ".K_TABLE_STATS." WHERE 1";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				$sql = "DELETE FROM ".K_TABLE_STATS_PAGES." WHERE 1";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				$sql = "DELETE FROM ".K_TABLE_STATS_REFERER." WHERE 1";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				$sql = "DELETE FROM ".K_TABLE_STATS_USER_AGENTS." WHERE 1";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
			}
			break;
		}
	}
	
	
	
	//initialize variables
	
	$l_month_names = array($l['w_january'], $l['w_february'], $l['w_march'], $l['w_april'], $l['w_may'], $l['w_june'], $l['w_july'], $l['w_august'], $l['w_september'], $l['w_october'], $l['w_november'], $l['w_december']);
	
	$l_day_names = array($l['w_sunday'], $l['w_monday'], $l['w_tuesday'], $l['w_wednesday'], $l['w_thursday'], $l['w_friday'], $l['w_saturday']);
	
	if (isset($timechange) AND ($timechange == 1)) {
		$statday = NULL;
		$statmonth = NULL;
		$statyear = NULL;
	}
	elseif (isset($yearchange) AND ($yearchange == 1)){
		$statday = NULL;
		$statmonth = NULL;
	}
	elseif (isset($monthchange) AND ($monthchange == 1)){
		$statday = NULL;
	}
	
	$currentyear = gmdate('Y');
	$firstyear = $currentyear;
	
	
	if ((isset($statyear)) AND ($statyear < $currentyear)) {
		$currentmonth = 12;
		$currentday = 31;
	}
	else {
		$currentmonth = round(gmdate('m'));
		if ((isset($statmonth)) AND ($statmonth != $currentmonth)) {
			$currentday = 31;
		}
		else {
			$currentday = round(gmdate('d'));
		}
	}
	$firstmonth = $currentmonth;
	$firstday = $currentday;
	
	//strings for sql query
	$sql_query = "";
	$selectby_query = "";
	$where_query = "";
	
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_selectsitestats" id="form_selectsitestats">

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<?php
//collect some information
$wherestat = "WHERE 1";
if (isset($statyear)) {
	$wherestat .= " AND YEAR(stats_datetime)='".$statyear."'";
}
if (isset($statmonth)) {
	$wherestat .= " AND MONTH(stats_datetime)='".$statmonth."'";
}
if (isset($statday)) {
	$wherestat .= " AND DAYOFMONTH(stats_datetime)='".$statday."'";
}

$sql = "SELECT stats_datetime FROM ".K_TABLE_STATS." ".$wherestat."";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) { //get site zones
		if (gmdate('Y',strtotime($m['stats_datetime'])) < $firstyear) {
			$firstyear = gmdate('Y',strtotime($m['stats_datetime']));
		}
		if (gmdate('m',strtotime($m['stats_datetime'])) < $firstmonth) {
			$firstmonth = round(gmdate('m',strtotime($m['stats_datetime'])));
		}
		if (gmdate('d',strtotime($m['stats_datetime'])) < $firstday) {
			$firstday = round(gmdate('d',strtotime($m['stats_datetime'])));
		}
	}
}
else {
	F_display_db_error();
}
?>

<!-- SELECT time interval  ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_time', ''); ?></b></td>
<td class="fillEE">
<select name="timeinterval" id="timeinterval" size="0" onchange="document.form_selectsitestats.timechange.value=1; document.form_selectsitestats.submit()">
<?php
if (!$timeinterval) {$timeinterval = "day";} //default view

echo "<option value=\"raw\"";
if ($timeinterval == "raw") {echo " selected=\"selected\"";}
echo ">&nbsp;</option>\n";

echo "<option value=\"hour\"";
if ($timeinterval == "hour") {echo " selected=\"selected\"";}
echo ">".$l['w_hour']."</option>\n";

echo "<option value=\"day\"";
if ($timeinterval == "day") {echo " selected=\"selected\"";}
echo ">".$l['w_day']."</option>\n";

echo "<option value=\"month\"";
if ($timeinterval == "month") {echo " selected=\"selected\"";}
echo ">".$l['w_month']."</option>\n";

echo "<option value=\"year\"";
if ($timeinterval == "year") {echo " selected=\"selected\"";}
echo ">".$l['w_year']."</option>\n";
?>
</select>
</td>
</tr>
<!-- END SELECT time interval  ==================== -->
<?php
if (($timeinterval == "hour") OR  ($timeinterval == "day") OR ($timeinterval == "month")) {
	//select default year
	if (!isset($statyear)) {
		$statyear = $currentyear;
	}
?>
<!-- SELECT year  ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_year', ''); ?></b></td>
<td class="fillOE">
<select name="statyear" id="statyear" size="0" onchange="document.form_selectsitestats.yearchange.value=1; document.form_selectsitestats.submit()">
<?php
for ($i=$firstyear; $i<=$currentyear; $i++) {
	echo "<option value=\"".$i."\"";
	if ($statyear == $i) {echo " selected=\"selected\"";}
	echo ">".$i."</option>\n";
}
?>
</select>
</td>
</tr>
<!-- END SELECT year  ==================== -->
<?php
}

if (($timeinterval == "hour") OR  ($timeinterval == "day")) {
	//select default month
	if (!isset($statmonth)) {
		if ($statyear == $currentyear) {
			$statmonth = $currentmonth;
		}
		else {
			$statmonth = $firstmonth;
		}
	}
?>
<!-- SELECT month  ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_month', ''); ?></b></td>
<td class="fillOE">
<select name="statmonth" id="statmonth" size="0" onchange="document.form_selectsitestats.monthchange.value=1; document.form_selectsitestats.submit()">
<?php
for ($i=$firstmonth; $i<=$currentmonth; $i++) {
	echo "<option value=\"".$i."\"";
	if ($statmonth == $i) {echo " selected=\"selected\"";}
	echo ">(".$i.") ".$l_month_names[$i-1]."</option>\n";
}
?>
</select>
</td>
</tr>
<!-- END SELECT month  ==================== -->
<?php
}

if ($timeinterval == "hour") {
	//select default month
	if (!isset($statday)) {
		if (($statyear == $currentyear) AND ($statmonth == $currentmonth)) {
			$statday = $currentday;
		}
		else {
			$statday = $firstday;
		}
	}
?>
<!-- SELECT month  ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_day', ''); ?></b></td>
<td class="fillOE">
<select name="statday" id="statday" size="0" onchange="document.form_selectsitestats.daychange.value=1; document.form_selectsitestats.submit()">
<?php
for ($i=$firstday; $i<=$currentday; $i++) {
	echo "<option value=\"".$i."\"";
	if ($statday == $i) {echo " selected=\"selected\"";}
	echo ">".$i." ".$l_day_names[round(date("w",mktime(0,0,0,$statmonth,$i,$statyear)))]."</option>\n";
}
?>
</select>
</td>
</tr>
<!-- END SELECT month  ==================== -->
<?php
}

?>
</table>

</td></tr>
</table>
 
<br />

<!-- SHOW stats ==================== -->
<?php
// build where query

switch ($timeinterval) {
	case "raw": {
		$selectby_query = "";
		break;
	}
	case "year": {
		$selectby_query = "YEAR";
		break;
	}
	case "month": {
		$selectby_query = "MONTH";
		$where_query = "AND YEAR(stats_datetime)=".$statyear."";
		break;
	}
	default:
	case "day": {
		$selectby_query = "DAYOFMONTH";
		$where_query = "AND MONTH(stats_datetime)=".$statmonth."";
		$where_query .= " AND YEAR(stats_datetime)=".$statyear."";
		break;
	}
	case "hour": {
		$selectby_query = "HOUR";
		$where_query = "AND DAYOFMONTH(stats_datetime)=".$statday."";
		$where_query .= " AND MONTH(stats_datetime)=".$statmonth."";
		$where_query .= " AND YEAR(stats_datetime)=".$statyear."";
		break;
	}
}
?>

<input type="hidden" name="timechange" id="timechange" value="" />
<input type="hidden" name="daychange" id="daychange" value="" />
<input type="hidden" name="monthchange" id="monthchange" value="" />
<input type="hidden" name="yearchange" id="yearchange" value="" />
<input type="hidden" name="submitted" id="submitted" value="" />

<input type="hidden" name="firstrow" id="firstrow" value="<?php echo $firstrow; ?>" />
<input type="hidden" name="order_field" id="order_field" value="<?php echo $order_field; ?>" />
<input type="hidden" name="orderdir" id="orderdir" value="<?php echo $orderdir; ?>" />

<input type="hidden" name="menu_mode" id="menu_mode" value="" />

<?php 

if ($selectby_query) {
	//general stats
	$ntoplimit = 10; //limit results for top statistics
	F_site_statistics($selectby_query, $where_query, $ntoplimit); //display stats
}
else { //display raw stats
	$where_query = substr($where_query, 5);
	F_site_raw_statistics($where_query); 
}

//show delete button
F_submit_button("form_selectsitestats","menu_mode",$l['w_delete']); 
?>
<!-- END SHOW stats ==================== -->

</form>

<!-- Submit form with new order field ==================== -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
function FJ_submit_sitestats_form(newfirstrow, neworder_field, neworderdir) {
	document.form_selectsitestats.firstrow.value=newfirstrow;
	document.form_selectsitestats.order_field.value=neworder_field;
	document.form_selectsitestats.orderdir.value=neworderdir;
	document.form_selectsitestats.submitted.value=1;
	document.form_selectsitestats.submit();
}
//]]>
</script>
<!-- END Submit form with new order field ==================== -->
<!-- ====================================================== -->

<?php
} //end of function

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
