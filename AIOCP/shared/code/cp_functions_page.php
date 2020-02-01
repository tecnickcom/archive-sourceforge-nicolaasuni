<?php
//============================================================+
// File name   : cp_functions_page.php
// Begin       : 2002-03-21
// Last Update : 2006-02-08
// 
// Description : Functions for pages
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
//============================================================+

/**
 * Display Pages navigation index.
 * @param string $script_name url of the calling page
 * @param string $aiocp_dp dynamyc page name (if any)
 * @param string $sql sql used to select records
 * @param int $firstrow first row number
 * @param int $rowsperpage number of max rows per page
 * @param string $param_array parameters to pass on url via GET
 * @return mixed the number of pages in case of success, FALSE otherwise
 */
function F_show_page_navigator($script_name, $aiocp_dp, $sql, $firstrow, $rowsperpage, $param_array) {
	global $l, $db;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	$max_pages = 4; // max pages to display on page selector
	
	if(!$sql) {return FALSE;}
	
	if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
	}
	
	// build base url for all links
	$baseaddress = $script_name;
	if (empty($param_array)) {
		if (empty($aiocp_dp)) {
			$baseaddress .= "?";
		} else {
			$baseaddress .= "?aiocp_dp=".$aiocp_dp."&amp;";
		}
	} else {
		if (empty($aiocp_dp)) {
			$param_array = substr($param_array, 5); // remove first "&amp;"
			$baseaddress .= "?".$param_array."&amp;";
		} else {
			$baseaddress .= "?aiocp_dp=".$aiocp_dp."".$param_array."&amp;";
		}
	}
	
	$count_rows = preg_match("/GROUP BY/i", $sql); //check if query contain a "GROUP BY"
	
	$all_updates = F_aiocpdb_num_rows($r);
	if ( ($all_updates == 1) AND (!$count_rows) ) {
		list($all_updates) = F_aiocpdb_fetch_array($r);
	}
	
	if(!$all_updates) { //no records
		F_print_error("MESSAGE", $l['m_search_void']);
	}
	else {
		if($all_updates > $rowsperpage) {
			echo "<div align=\"center\" class=\"small\">".$l['w_page'].": ";
			
			$page_range = $max_pages * $rowsperpage;
			if ($firstrow <= $page_range) {
				$page_range = (2 * $page_range) - $firstrow + $rowsperpage;
			}
			elseif ($firstrow >= ($all_updates - $page_range)) {
				$page_range = (2 * $page_range) - ($all_updates - (2 * $rowsperpage) - $firstrow);
			}
						
			if ($firstrow >= $rowsperpage) {
				
				echo "<a href=\"".$baseaddress."firstrow=0\">1</a> | ";
				echo "<a href=\"".$baseaddress."firstrow=".($firstrow - $rowsperpage)."\">&lt;&lt;</a> | ";
			}
			else {
				echo "1 | &lt;&lt; | ";
			}
			$count = 2;
			$x = 0;
			for($x = $rowsperpage; $x < ($all_updates - $rowsperpage); $x += $rowsperpage) {
				if(($x >= ($firstrow - $page_range)) AND ($x <= ($firstrow + $page_range))) {
					if($x == $firstrow) {
						echo "".$count." | ";
					}
					else {
						echo "<a href=\"".$baseaddress."firstrow=".$x."\">".$count."</a> | ";
					}
				}
				$count++;
			}
			
			if (($firstrow + $rowsperpage) < $all_updates) {
				echo "<a href=\"".$baseaddress."firstrow=".($firstrow + $rowsperpage)."\">&gt;&gt;</a> | ";
				echo "<a href=\"".$baseaddress."firstrow=".$x."\">".$count."</a>";
			}
			else {
				echo "&gt;&gt; | ".$count."";
			}
			echo "</div>";
		}
	}
	return $all_updates; //return number of records found
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
