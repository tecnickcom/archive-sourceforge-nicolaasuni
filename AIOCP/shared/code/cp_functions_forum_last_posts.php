<?php
//============================================================+
// File name   : cp_functions_forum_last_posts.php             
// Begin       : 2002-03-21                                    
// Last Update : 2003-11-18                                    
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
function F_show_forum_last_posts($language, $category_id, $forum_id, $rows, $truncateat) {
	global $l, $db, $selected_language;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_forum.'.CP_EXT);
	require_once('../../shared/code/cp_functions_user.'.CP_EXT);
	
	$userlevel = $_SESSION['session_user_level'];
	$wherequery = "";
	
	if ($category_id) {
		//read category data
		$catdata = F_get_forum_category_data($category_id);
		//check category authorization rights
		if($userlevel < $catdata->readinglevel) {
			return FALSE;
		}
		else {
			$wherequery = "WHERE (forumtopic_categoryid='".$category_id."')";
		}
	}
	
	if ($forum_id) {
		// read forum data
		$fdata = F_get_forum_data($forum_id);
		//check status and authorization rights
		if( ($userlevel < $fdata->readinglevel) OR ($fdata->status == 2) ) {
			return FALSE;
		}
		else {
			$wherequery .= "AND (forumtopic_forumid='".$forum_id."')";
		}
	}
	
	ob_start(); //store "echo" data to buffer
	
	//show forum topics
	echo "<table class=\"edge\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">";
	echo "<tr class=\"edge\">";
	echo "<th class=\"edge\">".$l['w_last_messages']."</th>";
	echo "</tr>";
	
	echo "<tr class=\"edge\">";
	echo "<td class=\"edge\">";
	
	echo "<table class=\"fill\" border=\"0\" cellspacing=\"2\" cellpadding=\"1\">";
	
	// iterate all topics inside forum
	$sql = "SELECT * FROM ".K_TABLE_FORUM_TOPICS." ".$wherequery." ORDER BY forumtopic_lastpost DESC LIMIT ".$rows."";
	
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			
			if ($category_id AND $forum_id) {
				//read category data
				$catdata = F_get_forum_category_data($m['forumtopic_categoryid']);
				// read forum data
				$fdata = F_get_forum_data($m['forumtopic_forumid']);
			}
			
			//check authorization rights
			if (($category_id AND $forum_id) OR ( ($userlevel >= $catdata->readinglevel) AND ($userlevel >= $fdata->readinglevel) AND ($fdata->status < 2) )) {
				
				//change style for each row
				if (isset($rowodd) AND ($rowodd)) {
					$rowclass = "O";
					$rowodd = 0;
				} else {
					$rowclass = "E";
					$rowodd = 1;
				}
				
				echo "<tr class=\"fill".$rowclass."\" valign=\"top\">";
				echo "<td class=\"fill".$rowclass."E\" valign=\"top\">";
				echo "<a href=\"cp_forum_view.".CP_EXT."?fmode=top&amp;topid=".$m['forumtopic_id']."&amp;forid=".$m['forumtopic_forumid']."&amp;catid=".$m['forumtopic_categoryid']."&amp;orderdir=1\"><small>".htmlentities(substr($m['forumtopic_title'],0,$truncateat), ENT_NOQUOTES, $l['a_meta_charset'])."</small></a>";
				echo "</td></tr>";
			}
		}
	}
	else {
		F_display_db_error();
	}
	
	echo "</table>";
	echo "</td></tr></table>";
	
	$thing_to_return = ob_get_contents(); //read buffer
	ob_end_clean (); //clean buffer
	return $thing_to_return;
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>