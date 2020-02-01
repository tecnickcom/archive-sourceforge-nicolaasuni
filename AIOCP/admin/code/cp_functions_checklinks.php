<?php
//============================================================+
// File name   : cp_functions_checklinks.php                   
// Begin       : 2001-09-25                                    
// Last Update : 2003-11-24                                    
//                                                             
// Description : Functions for check links                     
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
// check links
// ------------------------------------------------------------
function F_check_links() {
	global $l, $db, $selected_language, $progress_log;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	ini_set("memory_limit", K_MAX_MEMORY_LIMIT); //extend menory limit
	set_time_limit(K_MAX_EXECUTION_TIME); //extend the maximum execution time
	
	// --- iterate categories -----------------------
	$sqlc = "SELECT * FROM ".K_TABLE_LINKS_CATEGORIES." ORDER BY linkscat_name";
	if($rc = F_aiocpdb_query($sqlc, $db)) {
		while($mc = F_aiocpdb_fetch_array($rc)) {
			$thisname = F_decode_field($mc['linkscat_name']);
			echo "<i class=\"linkscategory\">".$thisname."</i><br />";
			error_log("    [CAT] ".$thisname."\n", 3, $progress_log); //create progress log file
			// --- iterate links -----------------------
			$sql = "SELECT * FROM ".K_TABLE_LINKS." WHERE links_category=".$mc['linkscat_id']." ORDER BY links_name";
			if($r = F_aiocpdb_query($sql, $db)) {
				while($m = F_aiocpdb_fetch_array($r)) {
					if(!F_checklink($m['links_link'])) { // broken link
						echo "<b class=\"linkbroken\">".$l['w_broken']."</b> - ";
						echo "<a href=\"../code/cp_edit_links.".CP_EXT."?links_id=".$m['links_id']."\" class=\"linkbroken\">";
						echo "".$m['links_name']." (".$m['links_link'].")";
						echo "</a>";
						error_log("      [".$m['links_id'].":BROKEN] ".$m['links_link']."", 3, $progress_log); //create progress log file
						if($m['links_status'] >= K_MIN_CHECK_LINK_TIMES) { //eliminate link if it has been K_MIN_CHECK_LINK_TIMES times unavailable
							//eliminate item from database
							$sqlx = "DELETE FROM ".K_TABLE_LINKS." WHERE links_id=".$m['links_id']."";
							if(!$rx = F_aiocpdb_query($sqlx, $db)) {
								F_display_db_error();
							}
							else {
								echo " ==&gt;".$l['w_deleted']."";
								error_log(" ==> ".$l['w_deleted']."", 3, $progress_log); //create progress log file
							}
						}
						else {//update links_status
							$newstatus = $m['links_status'] + 1;
							$sqlx = "UPDATE IGNORE ".K_TABLE_LINKS." SET links_status=".$newstatus." WHERE links_id=".$m['links_id']."";
							if(!$rx = F_aiocpdb_query($sqlx, $db)) {
								F_display_db_error();
							}
						}
						echo "<br />";
						error_log("\n", 3, $progress_log); //create progress log file
					}
					else { //valid link
						echo "<b class=\"linkok\">".$l['w_ok']."</b> - ";
						echo "<a href=\"../code/cp_edit_links.".CP_EXT."?links_id=".$m['links_id']."\" class=\"linkok\">";
						echo "".$m['links_name']." (".$m['links_link'].")";
						echo "</a><br />";
						error_log("      [".$m['links_id'].":OK] ".$m['links_link']."\n", 3, $progress_log); //create progress log file
						if($m['links_status']) { //restore status OK
							$sqlx = "UPDATE IGNORE ".K_TABLE_LINKS." SET links_status=0 WHERE links_id=".$m['links_id']."";
							if(!$rx = F_aiocpdb_query($sqlx, $db)) {
								F_display_db_error();
							}
						}
					}
					flush(); //force browser output
				}
			}
			else {
				F_display_db_error();
			}
			// --- END iterate links -------------------
			flush(); //force browser output
		}
	}
	else {
		F_display_db_error();
	}
return;
}

// ------------------------------------------------------------
// check single link
// ------------------------------------------------------------
function F_checklink($url) {
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	$cl_port = 80;
	$cl_timeout = 30;
	
	//get url parts
	$url_parts = parse_url($url);
	
	if (empty($url_parts['port'])) {
		$url_parts['port'] = $cl_port; //standard port
	}
	
	if(@fsockopen($url_parts['host'], $url_parts['port'], $errno, $errstr, $cl_timeout)) {
		return true;
	}
	return false;
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
