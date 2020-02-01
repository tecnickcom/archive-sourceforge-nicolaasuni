<?php
//============================================================+
// File name   : cp_functions_forum_last_news.php             
// Begin       : 2005-03-30                                    
// Last Update : 2005-03-30                                    
//                                                             
// Description : Functions to show forum last posts            
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
// show last $rows posted messages
// ------------------------------------------------------------
function F_show_forum_last_news($news_category, $wherequery, $rows) {
	global $l, $db, $selected_language;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_news.'.CP_EXT);
	
	$newsdata = "";
	//initialize variable
	$userlevel = $_SESSION['session_user_level'];
	
	$firstrow="0";
	$rowsperpage = $rows;	
	$full_order_field = "news_date DESC";
	
	if(!F_count_rows(K_TABLE_NEWS)) { //if the table is void (no items) display message
		return "";
	}
		
	// --- ------------------------------------------------------
	
	//if (isset($news_category) AND (strlen($news_category)>0) AND ($news_category==0)) { //select all categories
	//	$wherequery = "WHERE 1";
	//}
	
	if( (!$news_category) AND (!$wherequery) ) {
		$sql = "SELECT * FROM ".K_TABLE_NEWS_CATEGORIES." WHERE (newscat_level<=".$userlevel." AND newscat_language='".$selected_language."') ORDER BY newscat_name LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$news_category = $m['newscat_id'];
			}
			else {
				return "";
			}
		}
		else {
			F_display_db_error();
		}
	}

	if($news_category) {
		$sqlc = "SELECT * FROM ".K_TABLE_NEWS_CATEGORIES." WHERE newscat_id=".$news_category."";
		if($rc = F_aiocpdb_query($sqlc, $db)) {
			if($mc = F_aiocpdb_fetch_array($rc)) {
				if($mc['newscat_level']>$userlevel) {
					return "";
				}				
				if (!$wherequery) {$wherequery = "WHERE (news_category='".$news_category."')";}
				else {$wherequery .= " AND (news_category='".$news_category."')";}
			}
		}
		else {
			F_display_db_error();
		}
	}
	
	if (!$wherequery) {
		$sql = "SELECT * FROM ".K_TABLE_NEWS." ORDER BY ".$full_order_field." LIMIT ".$firstrow.",".$rowsperpage."";
	}
	else {
		$sql = "SELECT * FROM ".K_TABLE_NEWS." ".$wherequery." ORDER BY ".$full_order_field." LIMIT ".$firstrow.",".$rowsperpage."";
	}
	
	if($r = F_aiocpdb_query($sql, $db)) {
		
		while($m = F_aiocpdb_fetch_array($r)) {
			
			//get category data
			if (!$news_category) {$catdata = F_get_news_category_data($m['news_category']);}
			//check authorization rights
			if (($news_category) OR ($userlevel >= $catdata->level)) {
				//$newsdata .= $m['news_date']." - ";
				$newsdata .= "<a href=\"cp_news.php?nid=".$m['news_id']."\">";
				$newsdata .= htmlentities($m['news_title'], ENT_NOQUOTES, $l['a_meta_charset']);
				$newsdata .= "</a><br />\n";
			}
		} //end of while
	}
	else {
		F_display_db_error();
	}
	return $newsdata;
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>