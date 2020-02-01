<?php
//============================================================+
// File name   : cp_functions_banner_stats.php                 
// Begin       : 2002-05-02                                    
// Last Update : 2008-07-06
//                                                             
// Description : Functions for Banner                          
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
// Display banner statistics
// ------------------------------------------------------------
function F_banner_statistics($selectby_query, $where_query, $ntoplimit) {
	global $l, $db;
	global $timeinterval, $banmonth, $banyear;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	
	$barswidth = 5; //width of the graph bar
	$barsmaxlength = 150; //max length of graph bar
	
	$l_month_names = array($l['w_january'], $l['w_february'], $l['w_march'], $l['w_april'], $l['w_may'], $l['w_june'], $l['w_july'], $l['w_august'], $l['w_september'], $l['w_october'], $l['w_november'], $l['w_december']);
	
	$l_day_names = array($l['w_sunday'], $l['w_monday'], $l['w_tuesday'], $l['w_wednesday'], $l['w_thursday'], $l['w_friday'], $l['w_saturday']);
	
	$rowodd = 0;
	
	//calculate some statistics parameters:
	$max_impressions = 0;
	$max_clicks = 0;
	$max_ctr = 0;
	
	$total_impressions = 0;
	$total_clicks = 0;
	$total_cpm = 0;
	$total_cpc = 0;
	
	$total_records = 0;
	
	$sql = "SELECT ".$selectby_query."(banstat_time) AS time, SUM(banstat_action) AS clicks, SUM(NOT(banstat_action)) AS impressions, SUM(banner_cpc * banstat_action) AS totalcpc, (SUM(banner_cpm * NOT(banstat_action))/1000) AS totalcpm FROM ".K_TABLE_BANNERS_STATS.", ".K_TABLE_BANNERS." WHERE banner_id=banstat_banner_id ".$where_query." GROUP BY time";

	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			if ($max_impressions < $m['impressions']) {
				$max_impressions = $m['impressions'];
			}
			if ($max_clicks < $m['clicks']) {
				$max_clicks = $m['clicks'];
			}
			if ($m['impressions']>0) {
				$ctr = round(($m['clicks'] * 100) / $m['impressions'], 2);
			} else {
				$ctr = 0;
			}
			if ($max_ctr < $ctr) {
				$max_ctr = $ctr;
			}
			
			$total_impressions += $m['impressions'];
			$total_clicks += $m['clicks'];
			$total_cpm += $m['totalcpm'];
			$total_cpc += $m['totalcpc'];
			++$total_records;
		}
	}
	
	if (!$total_records) {return FALSE;} //void database
	
	echo "<table class=\"edge\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">";
	echo "<tr class=\"edge\">";
	echo "<td class=\"edge\">";
	echo "<table class=\"fill\" border=\"0\" cellspacing=\"2\" cellpadding=\"1\">";
	
	//columns names
	echo "<tr class=\"fill\">";
	echo "<th class=\"fillO\" valign=\"top\">&nbsp;".$l['w_time']."</th>";
	echo "<th class=\"fillE\" valign=\"top\" colspan=\"3\">";
	echo "<img src=\"../../shared/code/cp_button_and_bar.".CP_EXT."?s=bargraph&amp;w=10&amp;h=10&amp;dcr=0&amp;dcg=127&amp;dcb=255\" border=\"0\" width=\"10\" height=\"10\" /> ";
	echo "".$l['w_impressions']."</th>";
	
	echo "<th class=\"fillE\" valign=\"top\" colspan=\"3\">";
	echo "<img src=\"../../shared/code/cp_button_and_bar.".CP_EXT."?s=bargraph&amp;w=10&amp;h=10&amp;dcr=255&amp;dcg=255&amp;dcb=0\" border=\"0\" width=\"10\" height=\"10\" /> ";
	echo "".$l['w_clicks']."</th>";
	
	echo "<th class=\"fillE\" valign=\"top\">";
	echo "<img src=\"../../shared/code/cp_button_and_bar.".CP_EXT."?s=bargraph&amp;w=10&amp;h=10&amp;dcr=255&amp;dcg=0&amp;dcb=0\" border=\"0\" width=\"10\" height=\"10\" /> ";
	echo "CTR</th>";
	
	echo "<th class=\"fillO\" valign=\"top\">&nbsp;".$l['w_total']."</th>";
	echo "<th class=\"fillO\" valign=\"top\">&nbsp;".$l['w_graph']."</th>";
	echo "</tr>";
	
	//units of measure
	echo "<tr class=\"fill\">";
	echo "<th class=\"fillO\" valign=\"top\">";
	if ($timeinterval == "day") {
		$time_unit = $l['w_day'];
	}
	elseif  ($timeinterval == "month") {
		$time_unit = $l['w_month'];
	}
	else {
		$time_unit = $l['w_year'];
	}
	echo $time_unit;
	echo "</th>";
	echo "<th class=\"fillE\" valign=\"top\">&nbsp;[ # ]</th>";
	echo "<th class=\"fillE\" valign=\"top\">&nbsp;[ % ]</th>";
	echo "<th class=\"fillE\" valign=\"top\">&nbsp;[ ".K_MONEY_CURRENCY_UNICODE_SYMBOL." ]</th>";
	echo "<th class=\"fillO\" valign=\"top\">&nbsp;[ # ]</th>";
	echo "<th class=\"fillO\" valign=\"top\">&nbsp;[ % ]</th>";
	echo "<th class=\"fillO\" valign=\"top\">&nbsp;[ ".K_MONEY_CURRENCY_UNICODE_SYMBOL." ]</th>";
	echo "<th class=\"fillE\" valign=\"top\">&nbsp;[ % ]</th>";
	echo "<th class=\"fillO\" valign=\"top\">&nbsp;[ ".K_MONEY_CURRENCY_UNICODE_SYMBOL." ]</th>";
	echo "<th class=\"fillE\" valign=\"top\">&nbsp;</th>";
	echo "</tr>";
	
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			//change style for each row
			if (isset($rowodd) AND ($rowodd)) {
				$rowclass = "O";
				$rowodd = 0;
			} else {
				$rowclass = "E";
				$rowodd = 1;
			}
			
			echo "<tr class=\"fill".$rowclass."\">";
			echo "<td class=\"fill".$rowclass."O\" align=\"right\">&nbsp;";
			if ($timeinterval == "month") {
				echo $l_month_names[$m['time']-1]." (".$m['time'].")";
			}
			elseif ($timeinterval == "day") {
				echo $l_day_names[round(date("w",mktime(0,0,0,$banmonth,$m['time'],$banyear)))]." ".$m['time'];
			}
			else {
				echo $m['time'];
			}
			echo "</td>";
			echo "<td class=\"fill".$rowclass."E\" align=\"right\">&nbsp;".$m['impressions']."</td>";
			if ($total_impressions > 0) {
				$temp = round(($m['impressions']/$total_impressions)*100,2);
			}
			else {
				$temp = "&nbsp;";
			}
			echo "<td class=\"fill".$rowclass."E\" align=\"right\">&nbsp;".$temp."%</td>";
			echo "<td class=\"fill".$rowclass."E\" align=\"right\">&nbsp;".round($m['totalcpm'],2)."</td>";
			echo "<td class=\"fill".$rowclass."O\" align=\"right\">&nbsp;".$m['clicks']."</td>";
			if ($total_clicks > 0) {
				$temp = round(($m['clicks']/$total_clicks)*100,2);
			}
			else {
				$temp = "&nbsp;";
			}
			echo "<td class=\"fill".$rowclass."O\" align=\"right\">&nbsp;".$temp."%</td>";
			echo "<td class=\"fill".$rowclass."O\" align=\"right\">&nbsp;".round($m['totalcpc'],2)."</td>";
			if ($m['impressions']) {$ctr = round(($m['clicks'] * 100) / $m['impressions'], 2);}
			else $ctr = "&nbsp;";
			echo "<td class=\"fill".$rowclass."E\" align=\"right\">&nbsp;".$ctr."</td>";
			$totalmoney = round(($m['totalcpm'] + $m['totalcpc']),2);
			echo "<td class=\"fill".$rowclass."O\" align=\"right\">&nbsp;".$totalmoney."</td>";
			echo "<td class=\"fill".$rowclass."E\" align=\"left\">";
			
			//impressions bar
			if($max_impressions> 0) {$barlength = round(($m['impressions'] * $barsmaxlength)/$max_impressions);}
			else {$barlength = 0;}
			echo "<img src=\"../../shared/code/cp_button_and_bar.".CP_EXT."?s=bargraph&amp;w=".$barlength."&amp;h=".$barswidth."&amp;dcr=0&amp;dcg=127&amp;dcb=255\" border=\"0\" width=\"".$barlength."\" height=\"".$barswidth."\" class=\"blockview\" />";
			
			//clicks bar
			if($max_clicks> 0) {$barlength = round(($m['clicks'] * $barsmaxlength)/$max_clicks);}
			else {$barlength = 0;}
			echo "<img src=\"../../shared/code/cp_button_and_bar.".CP_EXT."?s=bargraph&amp;w=".$barlength."&amp;h=".$barswidth."&amp;dcr=255&amp;dcg=255&amp;dcb=0\" border=\"0\" width=\"".$barlength."\" height=\"".$barswidth."\" class=\"blockview\" />";
			
			//ctr bar
			if($max_ctr> 0) {$barlength = round(($ctr * $barsmaxlength)/$max_ctr);}
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
	echo "<td class=\"fillOE\" align=\"right\">&nbsp;<b>".$total_impressions."</b></td>";
	echo "<td class=\"fillOE\" align=\"right\">&nbsp;</td>";
	echo "<td class=\"fillOE\" align=\"right\">&nbsp;<b>".round($total_cpm,2)."</b></td>";
	echo "<td class=\"fillOO\" align=\"right\">&nbsp;<b>".$total_clicks."</b></td>";
	echo "<td class=\"fillOO\" align=\"right\">&nbsp;</td>";
	echo "<td class=\"fillOO\" align=\"right\">&nbsp;<b>".round($total_cpc,2)."</b></td>";
	echo "<td class=\"fillOE\" align=\"right\">&nbsp;</td>";
	echo "<td class=\"fillOO\" align=\"right\">&nbsp;<b>".round(($total_cpm + $total_cpc),2)."</b></td>";
	echo "<td class=\"fillOE\" align=\"right\">&nbsp;</td>";
	echo "</tr>";
	
	//AVERAGE
	echo "<tr class=\"fillE\">";
	echo "<td class=\"fillE0\" align=\"right\">&nbsp;<b>".$l['w_average'].":</b></td>";
	echo "<td class=\"fillEE\" align=\"right\">&nbsp;<b>".round($total_impressions/$total_records, 2)."</b></td>";
	echo "<td class=\"fillEE\" align=\"right\">&nbsp;</td>";
	echo "<td class=\"fillEE\" align=\"right\">&nbsp;<b>".round($total_cpm/$total_records, 2)."</b></td>";
	echo "<td class=\"fillEO\" align=\"right\">&nbsp;<b>".round($total_clicks/$total_records, 2)."</b></td>";
	echo "<td class=\"fillEO\" align=\"right\">&nbsp;</td>";
	echo "<td class=\"fillEO\" align=\"right\">&nbsp;<b>".round($total_cpc/$total_records, 2)."</b></td>";
	echo "<td class=\"fillEE\" align=\"right\">&nbsp;</td>";
	echo "<td class=\"fillEO\" align=\"right\">&nbsp;<b>".round(($total_cpm + $total_cpc)/$total_records, 2)."</b></td>";
	echo "<td class=\"fillEE\">&nbsp;<b>[ # / ".$time_unit." ]</b></td>";
	echo "</tr>";
	
	echo"</table>";
	echo "</td>";
	echo "</tr>";
	echo "</table>";
}

// ------------------------------------------------------------
// Display raw banner stats
// ------------------------------------------------------------
function F_banner_raw_statistics($wherequery) {
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
	
	if(!$order_field) {$order_field = "banstat_time";}
	else {$order_field = addslashes(preg_replace("/[\s]+/i", " ", $order_field));}
	if(!$orderdir) {$orderdir=0; $nextorderdir=1; $full_order_field = $order_field;}
	else {$orderdir=1; $nextorderdir=0; $full_order_field = $order_field." DESC";}
	$full_order_field = urldecode($full_order_field);
	
	if (!$wherequery) {
		$wherequery = "1";
		$sql = "SELECT * FROM ".K_TABLE_BANNERS_STATS.", ".K_TABLE_STATS_PAGES." WHERE  banstat_page=statpage_id ORDER BY ".$full_order_field." LIMIT ".$firstrow.",".$rowsperpage."";
	}
	else {
		$sql = "SELECT * FROM ".K_TABLE_BANNERS_STATS.", ".K_TABLE_STATS_PAGES." WHERE  banstat_page=statpage_id AND ".$wherequery." ORDER BY ".$full_order_field." LIMIT ".$firstrow.",".$rowsperpage."";
	}
	
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			// -- Table structure with links:
			echo "<table class=\"edge\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">";
			echo "<tr class=\"edge\">";
			echo "<td class=\"edge\">";
			echo "<table class=\"fill\" border=\"0\" cellspacing=\"2\" cellpadding=\"1\">";
			echo "<tr class=\"fill\">";
			echo "<th class=\"fillO\"><a href=\"javascript:FJ_submit_bannerstats_form(0,'banstat_time','".$nextorderdir."');\">".$l['w_time']."</a></th>";
			echo "<th class=\"fillE\"><a href=\"javascript:FJ_submit_bannerstats_form(0,'banstat_banner_id','".$nextorderdir."');\">".$l['w_banner']."</a></th>";
			echo "<th class=\"fillO\"><a href=\"javascript:FJ_submit_bannerstats_form(0,'banstat_action','".$nextorderdir."');\">".$l['w_action']."</a></th>";
			echo "<th class=\"fillE\"><a href=\"javascript:FJ_submit_bannerstats_form(0,'statpage_url','".$nextorderdir."');\">".$l['w_page']."</a></th>";
			echo "<th class=\"fillO\"><a href=\"javascript:FJ_submit_bannerstats_form(0,'banstat_user_id','".$nextorderdir."');\">".$l['w_user']."</a></th>";
			echo "<th class=\"fillE\"><a href=\"javascript:FJ_submit_bannerstats_form(0,'banstat_user_ip','".$nextorderdir."');\">".$l['w_ip']."</a></th>";
			echo "<th class=\"fillO\"><a href=\"javascript:FJ_submit_bannerstats_form(0,'banstat_user_agent','".$nextorderdir."');\">".$l['w_user_agent']."</a></th>";
			echo "</tr>";
			
			do {
				//change style for each row
				if (isset($rowodd) AND ($rowodd)) {
					$rowclass = "O";
					$rowodd = 0;
				} else {
					$rowclass = "E";
					$rowodd = 1;
				}
				
				echo "<tr class=\"fill".$rowclass."\">";
				echo "<td class=\"fill".$rowclass."O\" style=\"white-space:nowrap\">&nbsp;".$m['banstat_time']."</td>";
				echo "<td class=\"fill".$rowclass."E\" style=\"white-space:nowrap\">&nbsp;";
				$sqlb = "SELECT banner_name FROM ".K_TABLE_BANNERS." WHERE banner_id=".$m['banstat_banner_id']." LIMIT 1";
				if($rb = F_aiocpdb_query($sqlb, $db)) {
					if($mb = F_aiocpdb_fetch_array($rb)) {
						echo $mb['banner_name'];
					}
				}
				else {
					F_display_db_error();
				}
				echo "</td>";
				
				
				
				
				echo "<td class=\"fill".$rowclass."O\" style=\"white-space:nowrap\">&nbsp;";
				if ($m['banstat_action']) {
					echo $l['w_click'];
				}
				else {
					echo $l['w_impression'];
				}
				echo"</td>";
				echo "<td class=\"fill".$rowclass."E\" style=\"white-space:nowrap\">&nbsp;<a href=\"".htmlentities(urldecode($m['statpage_url']))."\" target=\"_blank\">".htmlentities(urldecode($m['statpage_url']))."</a></td>";
				echo "<td class=\"fill".$rowclass."O\" style=\"white-space:nowrap\">&nbsp;";
				if (!$m['banstat_user_id']) {$m['banstat_user_id']=1;}
				$sqlu = "SELECT user_name FROM ".K_TABLE_USERS." WHERE user_id=".$m['banstat_user_id']." LIMIT 1";
				if($ru = F_aiocpdb_query($sqlu, $db)) {
					if($mu = F_aiocpdb_fetch_array($ru)) {
						if ($m['banstat_user_id'] > 1) {
						echo "<a href=\"cp_user_profile.".CP_EXT."?user_id=".$m['banstat_user_id']."\">".htmlentities($mu['user_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</a>";
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
				echo "<td class=\"fill".$rowclass."E\" style=\"white-space:nowrap\">&nbsp;".$m['banstat_user_ip']."</td>";
				
				echo "<td class=\"fill".$rowclass."O\" style=\"white-space:nowrap\">&nbsp;";
				if ($m['banstat_user_agent']) {
					echo htmlentities(F_get_user_agent($m['banstat_user_agent']), ENT_NOQUOTES, $l['a_meta_charset']);
				}
				else {
					echo "&nbsp;";
				}
				echo "</td>";
			} while($m = F_aiocpdb_fetch_array($r));
			echo"</table>";
			echo "</td>";
			echo "</tr>";
			echo "</table>";
			
			
			// ---------------------------------------------------------------
			// -- page jumper (menu for successive pages)
			$sql = "SELECT count(*) AS total FROM ".K_TABLE_BANNERS_STATS." WHERE ".$wherequery."";
			if (!empty($order_field)) {$param_array = "&amp;order_field=".urlencode($order_field)."";}
			if (!empty($orderdir)) {$param_array .= "&amp;orderdir=".$orderdir."";}
			$param_array .= "&amp;submitted=1";
			if (!empty($banmonth)) {$param_array .= "&amp;banmonth=".$banmonth."";}
			if (!empty($banyear)) {$param_array .= "&amp;banyear=".$banyear."";}
			if (!empty($timeinterval)) {$param_array .= "&amp;timeinterval=".$timeinterval."";}
			if (!empty($banner_id)) {$param_array .= "&amp;banner_id=".$banner_id."";}
			if (!empty($banner_zone)) {$param_array .= "&amp;banner_zone=".$banner_zone."";}
			if (!empty($banner_customer_id)) {$param_array .= "&amp;banner_customer_id=".$banner_customer_id."";}
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
// Show select form for banner stats (filters)
// ------------------------------------------------------------
function F_show_select_banner_stats($sqlquery) {
	global $l, $db, $selected_language;
	global $order_field, $orderdir, $firstrow, $rowsperpage;
	global $banner_customer_id, $customerchange;
	global $banner_zone, $zonechange;
	global $banner_id, $bannerchange;
	global $timeinterval, $timechange;
	global $banmonth, $monthchange;
	global $banyear, $yearchange;
	global $menu_mode, $forcedelete, $aiocp_dp;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	
	$userid = $_SESSION['session_user_id'];
	$userlevel = $_SESSION['session_user_level'];
	
	if ($userlevel >= K_AUTH_BANNER_STATS_LEVEL) {
		switch($menu_mode) {
			case unhtmlentities($l['w_delete']):
			case $l['w_delete']:{
				F_stripslashes_formfields(); // ask confirmation
				F_print_error("WARNING", $l['m_delete_confirm']);
				?>
				<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_delete" id="form_delete">
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
		
			case 'forcedelete':{
		F_stripslashes_formfields(); // Delete newscat
				if($forcedelete == $l['w_delete']) { //check if delete button has been pushed (redundant check)
					$sql = "DELETE FROM ".K_TABLE_BANNERS_STATS." WHERE 1";
					if(!$r = F_aiocpdb_query($sql, $db)) {
						F_display_db_error();
					}
				}
				break;
			}
		}
	}
	
	
	//initialize variables
	
	$l_month_names = array($l['w_january'], $l['w_february'], $l['w_march'], $l['w_april'], $l['w_may'], $l['w_june'], $l['w_july'], $l['w_august'], $l['w_september'], $l['w_october'], $l['w_november'], $l['w_december']);
	
	if(isset($customerchange) AND ($customerchange == 1)) {
		$banner_zone = NULL;
		$banner_id = NULL;
	}
	elseif(isset($zonechange) AND ($zonechange == 1)) {
		$banner_id = NULL;
	}
	
	if (isset($timechange) AND ($timechange == 1)) {
		$banmonth = NULL;
		$banyear = NULL;
	}
	elseif (isset($yearchange) AND ($yearchange == 1)){
		$banmonth = NULL;
	}
	
	$currentyear = gmdate('Y');
	$firstyear = $currentyear;
	
	if ((isset($banyear)) AND ($banyear < $currentyear)) {
		$currentmonth = 12;
	}
	else {
		$currentmonth = round(gmdate('m'));
	}
	$firstmonth = $currentmonth;
	
	//strings for sql query
	$sql_query = "";
	$selectby_query = "";
	$where_query = "";
	$bannersid = array(); //array of selected banners
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_selectbanners" id="form_selectbanners">

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT customer ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_company', ''); ?></b></td>
<td class="fillOE">
<select name="banner_customer_id" id="banner_customer_id" size="0" onchange="document.form_selectbanners.customerchange.value=1; document.form_selectbanners.submit()">
<?php
if ($userlevel >= K_AUTH_BANNER_STATS_LEVEL) {
echo "<option value=\"\">&nbsp;</option>";
}
$sql = "SELECT banner_customer_id FROM ".K_TABLE_BANNERS." GROUP BY banner_customer_id";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) { //get customer IDs
		$sql2 = "SELECT company_userid,company_id,company_name FROM ".K_TABLE_USERS_COMPANY." WHERE company_id='".$m['banner_customer_id']."'LIMIT 1";
		if($r2 = F_aiocpdb_query($sql2, $db)) { //get customer data
			if($m2 = F_aiocpdb_fetch_array($r2)) {
				//customer can see only his account
				if (($userlevel >= K_AUTH_BANNER_STATS_LEVEL) OR ($userid == $m2['company_userid'])) {
					echo "<option value=\"".$m2['company_id']."\"";
					if ($m2['company_id'] == $banner_customer_id) {
						echo " selected=\"selected\"";
					}
					echo ">".htmlentities($m2['company_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
					
					if ($userlevel < K_AUTH_BANNER_STATS_LEVEL) { //initialize variable for customer user
						$banner_customer_id = $m2['company_id'];
						break; //end cycle
					}
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
?>
</select>
</td>
</tr>
<!-- END SELECT customer ==================== -->

<?php //check authorization for customer user
if (($userlevel < K_AUTH_BANNER_STATS_LEVEL) AND (!$banner_customer_id)) {
	F_print_error("WARNING", $l['m_authorization_deny']);
	F_logout_form();
	return FALSE;
}
?>

<!-- SELECT banner zone ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_zone', ''); ?></b></td>
<td class="fillEE">
<select name="banner_zone" id="banner_zone" size="0" onchange="document.form_selectbanners.zonechange.value=1; document.form_selectbanners.submit()">
<?php
echo "<option value=\"\">&nbsp;</option>";
if ($banner_customer_id) {
	$sql = "SELECT banner_zone FROM ".K_TABLE_BANNERS." WHERE banner_customer_id='".$banner_customer_id."' GROUP BY banner_zone";
}
else {
	$sql = "SELECT banner_zone FROM ".K_TABLE_BANNERS." GROUP BY banner_zone";
}
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) { //get banner zones
		$sql2 = "SELECT * FROM ".K_TABLE_BANNERS_ZONES." WHERE banzone_id='".$m['banner_zone']."'LIMIT 1";
		if($r2 = F_aiocpdb_query($sql2, $db)) { //get banner zone data
			if($m2 = F_aiocpdb_fetch_array($r2)) {
				echo "<option value=\"".$m2['banzone_id']."\"";
				if ($m2['banzone_id'] == $banner_zone) {
					echo " selected=\"selected\"";
				}
				echo ">".htmlentities($m2['banzone_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
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
?>
</select>
</td>
</tr>
<!-- END SELECT banner zone ==================== -->

<!-- SELECT banner  ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_banner', ''); ?></b></td>
<td class="fillOE">
<select name="banner_id" id="banner_id" size="0" onchange="document.form_selectbanners.bannerchange.value=1; document.form_selectbanners.submit()">
<?php
echo "<option value=\"\">&nbsp;</option>";
if (($banner_customer_id) OR ($banner_zone)) {
	if ($banner_customer_id) {
		$bannerwhere = "banner_customer_id='".$banner_customer_id."'";
	}
	if ($banner_zone) {
	 	if ($banner_customer_id) { $bannerwhere .= " AND";}
		$bannerwhere .= " banner_zone='".$banner_zone."'";
	}
	$sql = "SELECT banner_id,banner_name,banner_start_date FROM ".K_TABLE_BANNERS." WHERE ".$bannerwhere." GROUP BY banner_id";
}
else {
	$sql = "SELECT banner_id,banner_name,banner_start_date FROM ".K_TABLE_BANNERS." GROUP BY banner_id";
}
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) { //get banner zones
		if (gmdate('Y',strtotime($m['banner_start_date'])) < $firstyear) {
			$firstyear = gmdate('Y',strtotime($m['banner_start_date']));
		}
		if (gmdate('m',strtotime($m['banner_start_date'])) < $firstmonth) {
			$firstmonth = round(gmdate('m',strtotime($m['banner_start_date'])));
		}
		echo "<option value=\"".$m['banner_id']."\"";
		if ($m['banner_id'] == $banner_id) {
			echo " selected=\"selected\"";
		}
		echo ">".$m['banner_name']."</option>\n";
		$bannersid[] = $m['banner_id'];
	}
}
else {
	F_display_db_error();
}
?>
</select>


</td>
</tr>
<!-- END SELECT banner  ==================== -->

<!-- SELECT time interval  ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_time', ''); ?></b></td>
<td class="fillEE">
<select name="timeinterval" id="timeinterval" size="0" onchange="document.form_selectbanners.timechange.value=1; document.form_selectbanners.submit()">
<?php
if (!$timeinterval) {$timeinterval = "day";} //default view

echo "<option value=\"raw\"";
if ($timeinterval == "raw") {echo " selected=\"selected\"";}
echo ">&nbsp;</option>\n";

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
if (($timeinterval == "day") OR ($timeinterval == "month")) {
	//select default year
	if (!$banyear) {
		$banyear = $currentyear;
	}
?>
<!-- SELECT year  ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_year', ''); ?></b></td>
<td class="fillOE">
<select name="banyear" id="banyear" size="0" onchange="document.form_selectbanners.yearchange.value=1; document.form_selectbanners.submit()">
<?php
for ($i=$firstyear; $i<=$currentyear; $i++) {
	echo "<option value=\"".$i."\"";
	if ($banyear == $i) {echo " selected=\"selected\"";}
	echo ">".$i."</option>\n";
}
?>
</select>
</td>
</tr>
<!-- END SELECT year  ==================== -->
<?php
}

if ($timeinterval == "day") {
	//select default month
	if (!$banmonth) {
		if ($banyear == $currentyear) {
			$banmonth = $currentmonth;
		}
		else {
			$banmonth = $firstmonth;
		}
	}
?>
<!-- SELECT month  ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_month', ''); ?></b></td>
<td class="fillOE">
<select name="banmonth" id="banmonth" size="0" onchange="document.form_selectbanners.monthchange.value=1; document.form_selectbanners.submit()">
<?php
for ($i=$firstmonth; $i<=$currentmonth; $i++) {
	echo "<option value=\"".$i."\"";
	if ($banmonth == $i) {echo " selected=\"selected\"";}
	echo ">(".$i.") ".$l_month_names[$i-1]."</option>\n";
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

<input type="hidden" name="customerchange" id="customerchange" value="0" />
<input type="hidden" name="zonechange" id="zonechange" value="0" />
<input type="hidden" name="bannerchange" id="bannerchange" value="0" />
<input type="hidden" name="timechange" id="timechange" value="0" />
<input type="hidden" name="monthchange" id="monthchange" value="0" />
<input type="hidden" name="yearchange" id="yearchange" value="0" />
<input type="hidden" name="submitted" id="submitted" value="0" />

<input type="hidden" name="firstrow" id="firstrow" value="<?php echo $firstrow; ?>" />
<input type="hidden" name="order_field" id="order_field" value="<?php echo $order_field; ?>" />
<input type="hidden" name="orderdir" id="orderdir" value="<?php echo $orderdir; ?>" />

<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php
//show delete button
F_submit_button("form_selectbanners","menu_mode",$l['w_delete']); 
?>
</form>

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
		$where_query = "AND YEAR(banstat_time)=".$banyear."";
		break;
	}
	default:
	case "day": {
		$selectby_query = "DAYOFMONTH";
		$where_query = "AND MONTH(banstat_time)=".$banmonth."";
		$where_query .= " AND YEAR(banstat_time)=".$banyear."";
		break;
	}
}

if ($banner_id) {
	$where_query .= " AND banstat_banner_id=".$banner_id."";
}
elseif ($bannersid) {
	$where_query .= " AND (";
	while(list($key, $val) = each($bannersid)) {
		if ($key > 0) {$where_query .= " OR";}
		$where_query .= " banstat_banner_id=".$val."";
	}
	$where_query .= ")";
}

if ($selectby_query) {
	//display stats
	$ntoplimit = 10; //limit results for top statistics
	F_banner_statistics($selectby_query, $where_query, $ntoplimit); //display stats
}
else { //display raw stats
	if ($where_query) {
		$where_query = substr($where_query, 5);
	}
	F_banner_raw_statistics($where_query); 
}
?>
<!-- END SHOW stats ==================== -->

<!-- Submit form with new order field ==================== -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
function FJ_submit_bannerstats_form(newfirstrow, neworder_field, neworderdir) {
	document.form_selectbanners.firstrow.value=newfirstrow;
	document.form_selectbanners.order_field.value=neworder_field;
	document.form_selectbanners.orderdir.value=neworderdir;
	document.form_selectbanners.submitted.value=1;
	document.form_selectbanners.submit();
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
