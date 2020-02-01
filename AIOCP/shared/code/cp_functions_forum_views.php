<?php
//============================================================+
// File name   : cp_functions_forum_views.php
// Begin       : 2002-01-31
// Last Update : 2008-07-06
// 
// Description : Functions to show forum in different views
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
// show all forum categories
// if $catid is set the other categories will be collapsed
// ------------------------------------------------------------
function F_show_categories($catid) {
	global $l, $db, $selected_language;
	global $menu_mode;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_forum.'.CP_EXT);
	require_once('../../shared/code/cp_functions_dynamic_pages.'.CP_EXT);
	
	$userlevel = $_SESSION['session_user_level'];
	
	$catdata = F_get_forum_category_data($catid);
	
	$catid = intval($catid);
	
	//check authorization rights
	if (($catdata !== false) AND ($userlevel < $catdata->readinglevel)) {
		return FALSE;
	}
	
	//print links for category and forum (current location)
	echo "<p>";
	echo "<a href=\"cp_forum_view.".CP_EXT."?fmode=cat\">".$l['w_index']."</a>";
	echo "</p>";
	
	echo "<table class=\"edge\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">";
	echo "<tr class=\"edge\">";
	echo "<td class=\"edge\">";
	echo "<table class=\"fill\" border=\"0\" cellspacing=\"2\" cellpadding=\"1\">";
	
	echo "<tr class=\"fill\">";
	echo "<th class=\"fillO\">".$l['w_status']."</th>";
	echo "<th class=\"fillE\">".$l['w_forum']."</th>";
	echo "<th class=\"fillO\">".$l['w_topics']."</th>";
	echo "<th class=\"fillE\">".$l['w_posts']."</th>";
	echo "<th class=\"fillO\">".$l['w_last_topic']."</th>";
	echo "</tr>";
	
	// iterate all categories
	$sql = "SELECT * FROM ".K_TABLE_FORUM_CATEGORIES." WHERE (forumcat_language='".$selected_language."' AND forumcat_readinglevel<='".$userlevel."') ORDER BY forumcat_order";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			
			echo "<tr class=\"fill\">";
			echo "<td class=\"edge\" colspan=\"5\">";
			echo "<a class=\"edge\" href=\"cp_forum_view.".CP_EXT."?fmode=cat&amp;catid=".$m['forumcat_id']."\"><b>".htmlentities($m['forumcat_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</b></a><br />";
			echo F_evaluate_modules($m['forumcat_description']);
			echo "</td></tr>";
			
			if((!isset($catid)) OR (isset($catid) AND ($catid == $m['forumcat_id']))) {
				// iterate all forums inside category
				$sqlf = "SELECT * FROM ".K_TABLE_FORUM_FORUMS." WHERE (forum_categoryid='".$m['forumcat_id']."' AND forum_readinglevel<='".$userlevel."' AND forum_status<2) ORDER BY forum_order";
				if($rf = F_aiocpdb_query($sqlf, $db)) {
					while($mf = F_aiocpdb_fetch_array($rf)) {
						//change style for each row
						if (isset($rowodd) AND ($rowodd)) {
							$rowclass = "O";
							$rowodd = 0;
						} else {
							$rowclass = "E";
							$rowodd = 1;
						}
						
						echo "<tr class=\"fill".$rowclass."\">";
						
						$lasttopic = F_get_forum_topic_data($mf['forum_lasttopic']);
						$lastpost = F_get_forum_post_data($lasttopic->lastpost);
						
						echo "<td class=\"fill".$rowclass."0\" align=\"center\" valign=\"middle\">";
						//display status image
						if(!$mf['forum_status']) {
							$pathtoimage = K_PATH_IMAGES_FORUM;
							$status_alt = $l['w_enabled'];
						}
						else {
							$pathtoimage = K_PATH_IMAGES_FORUM."locked_";
							$status_alt = $l['w_locked'];
						}
						if($_SESSION['session_last_visit'] >= strtotime($lastpost->time)) {
							echo "<img src=\"".$pathtoimage."no_new_posts.gif\" border=\"0\" alt=\"".$status_alt." - ".$l['w_no_new_posts']."\" />";
						}
						else {
							echo "<img src=\"".$pathtoimage."new_posts.gif\" border=\"0\" alt=\"".$status_alt." - ".$l['w_new_posts']."\" />";
						}
						echo "</td>";
						
						echo "<td class=\"fill".$rowclass."E\">";
						echo "<a href=\"cp_forum_view.".CP_EXT."?fmode=for&amp;forid=".$mf['forum_id']."&amp;catid=".$m['forumcat_id']."\">".htmlentities($mf['forum_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</a><br />";
						//echo "<i class=\"forumdesc\">".F_evaluate_modules($mf['forum_description'])."</i>";
						echo F_evaluate_modules($mf['forum_description']);
						echo "</td>";
						
						echo "<td class=\"fill".$rowclass."O\" align=\"right\">";
						echo "".$mf['forum_topics']."";
						echo "</td>";
						
						echo "<td class=\"fill".$rowclass."E\" align=\"right\">";
						echo "".$mf['forum_posts']."";
						echo "</td>";
						
						echo "<td class=\"fill".$rowclass."O\">";
						echo "<a href=\"cp_forum_view.".CP_EXT."?fmode=top&amp;topid=".$mf['forum_lasttopic']."&amp;forid=".$mf['forum_id']."&amp;catid=".$m['forumcat_id']."\"><small>".$lasttopic->time."</small></a>";
						echo "</td>";
						
						echo "</tr>";
					}
				}
				else {
					F_display_db_error();
				}
			} //end iterate forums
		}
	}
	else {
		F_display_db_error();
	}
	echo "</table>";
	echo "</td></tr></table>";
	return TRUE;
}


// ------------------------------------------------------------
// show all topics for a particular forum
// ------------------------------------------------------------
function F_show_forum($forid, $catid, $order_field, $orderdir, $firstrow, $rowsperpage) {
	global $l, $db, $selected_language, $aiocp_dp;
	global $menu_mode;
	global $term, $submitted, $forumsearch, $addterms;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_forum.'.CP_EXT);
	require_once('../../shared/code/cp_functions_user.'.CP_EXT);
	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	require_once('../../shared/code/cp_functions_page.'.CP_EXT);
	require_once('../../shared/code/cp_functions_dynamic_pages.'.CP_EXT);
	
	$userid = $_SESSION['session_user_id'];
	$userlevel = $_SESSION['session_user_level'];
	
	//initialize variables
	if(!$firstrow) {$firstrow="0";}
	else {$firstrow=intval($firstrow);} 
	if(!$rowsperpage) {$rowsperpage = K_MAX_ROWS_PER_PAGE;}
	else {$rowsperpage=intval($rowsperpage);}
	
	if(!$order_field) {$order_field = "forumtopic_time";}
	else {$order_field = addslashes(preg_replace("/[\s]+/i", " ", $order_field));}
	if(!$orderdir) {$orderdir=0; $nextorderdir=1; $full_order_field = $order_field." DESC";}
	else {$orderdir=1; $nextorderdir=0; $full_order_field = $order_field;}
	$full_order_field = urldecode($full_order_field);
	
	$forid = intval($forid);
	$catid = intval($catid);
	
	//read category data
	$catdata = F_get_forum_category_data($catid);
	
	//check category authorization rights
	if($userlevel < $catdata->readinglevel) {return FALSE;}
	
	// read forum data
	$fdata = F_get_forum_data($forid);
	
	if($fdata->removezeroreply) { //Remove topics with no replies
		F_remove_zeroreplies_topics($forid, $fdata->removezeroreply);
	}
	
	//check status and authorization rights
	if( ($userlevel < $fdata->readinglevel) OR ($fdata->status == 2) ) {return FALSE;}
	
	//check user -----------------------
	$usertype=0; //simple user
	if($userlevel>=10) {$usertype=1;} // administrator
	else {// check if moderator
		$sql = "SELECT moderator_options FROM ".K_TABLE_FORUM_MODERATORS." WHERE (moderator_userid='".$userid."' AND moderator_forumid='".$forid."' AND moderator_categoryid='".$catid."') LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$moderator_options = $m['moderator_options'];
				$mod_options = unserialize($moderator_options);
				if(stripslashes($mod_options[5])) {$usertype=1;} // moderator
			}
		}
		else {
			F_display_db_error();
		}
	}
	//END check user -----------------------
	
	if( ($userlevel >= $fdata->postinglevel) AND ($userlevel >= $catdata->postinglevel) AND ($fdata->status == 0) ) {
		//show NEW TOPIC button
		echo"\n<form action=\"cp_forum_edit_message.".CP_EXT."\" method=\"post\" enctype=\"multipart/form-data\" name=\"form_newtopic\" id=\"form_newtopic\">\n";
		echo "<input type=\"hidden\" name=\"efmm\" id=\"efmm\" value=\"\" />\n";
		echo "<input type=\"hidden\" name=\"forumid\" id=\"forumid\" value=\"".$forid."\" />\n";
		echo "<input type=\"hidden\" name=\"categoryid\" id=\"categoryid\" value=\"".$catid."\" />\n";
		F_generic_submit_button("form_newtopic", "newtopic", $l['w_new_topic'], "document.form_newtopic.efmm.value='n'");
		echo "</form>";
	}
	elseif ($fdata->status != 0) { //forum locked
		echo "".$l['m_forum_locked']."<br /><br />";
	}
	else { //user need registration
		echo "".$l['m_forum_reg_need']." [<a href=\"cp_login.".CP_EXT."\">".$l['w_login']."</a>] [<a href=\"cp_edit_user.".CP_EXT."\">".$l['w_register']."</a>]<br /><br />";
	}
	
	//print links for category and forum (current location)
	echo "<a href=\"cp_forum_view.".CP_EXT."?fmode=cat\">".$l['w_index']."</a>";
	echo " &raquo; ";
	echo "<a href=\"cp_forum_view.".CP_EXT."?fmode=cat&amp;catid=".$catid."\">".htmlentities($catdata->name, ENT_NOQUOTES, $l['a_meta_charset'])."</a>";
	echo " &raquo; ";
	echo "<a href=\"cp_forum_view.".CP_EXT."?fmode=for&amp;forid=".$forid."&amp;catid=".$catid."\">".htmlentities($fdata->name, ENT_NOQUOTES, $l['a_meta_charset'])."</a>";
	
	// form for edit buttons
	echo"\n<form action=\"cp_forum_edit_topic.".CP_EXT."\" method=\"post\" enctype=\"multipart/form-data\" name=\"form_edit_buttons\" id=\"form_edit_buttons\">\n";
	
	//show forum topics
	echo "<table class=\"edge\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">";
	echo "<tr class=\"edge\">";
	echo "<td class=\"edge\">";
	
	echo "<input type=\"hidden\" name=\"topicid\" id=\"feb_topicid\" value=\"\" />\n";
	echo "<input type=\"hidden\" name=\"forumid\" id=\"feb_forumid\" value=\"".$forid."\" />\n";
	echo "<input type=\"hidden\" name=\"categoryid\" id=\"feb_categoryid\" value=\"".$catid."\" />\n";
	
	echo "<table class=\"fill\" border=\"0\" cellspacing=\"2\" cellpadding=\"1\">";	
	echo "<tr class=\"fill\">";
	echo "<th class=\"fillO\"><a href=\"javascript:FJ_submit_forumviews_form(0,'forumtopic_status','".$nextorderdir."');\">".$l['w_status']."</a></th>";
	echo "<th class=\"fillE\"><a href=\"javascript:FJ_submit_forumviews_form(0,'forumtopic_title','".$nextorderdir."');\">".$l['w_topic']."</a></th>";
	echo "<th class=\"fillO\"><a href=\"javascript:FJ_submit_forumviews_form(0,'forumtopic_replies','".$nextorderdir."');\">".$l['w_replies']."</a></th>";
	echo "<th class=\"fillE\"><a href=\"javascript:FJ_submit_forumviews_form(0,'forumtopic_views','".$nextorderdir."');\">".$l['w_views']."</a></th>";
	echo "<th class=\"fillO\"><a href=\"javascript:FJ_submit_forumviews_form(0,'forumtopic_poster','".$nextorderdir."');\">".$l['w_author']."</a></th>";
	echo "<th class=\"fillE\"><a href=\"javascript:FJ_submit_forumviews_form(0,'forumtopic_time','".$nextorderdir."');\">".$l['w_date']."</a></th>";
	echo "<th class=\"fillO\"><a href=\"javascript:FJ_submit_forumviews_form(0,'forumtopic_lastpost','".$nextorderdir."');\">".$l['w_last_reply']."</a></th>";
	echo "</tr>";
	
	
	// iterate all topics inside forum
	$sql = "SELECT * FROM ".K_TABLE_FORUM_TOPICS." WHERE (forumtopic_forumid='".$forid."' AND forumtopic_status<2) ORDER BY ".$full_order_field." LIMIT ".$firstrow.",".$rowsperpage."";
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
			
			$poster = F_get_user_data($m['forumtopic_poster']);
			$lastpost = F_get_forum_post_data($m['forumtopic_lastpost']);
			
			echo "<tr class=\"fill".$rowclass."\">";
						
			echo "<td class=\"fill".$rowclass."O\" align=\"center\" valign=\"middle\">";
			//display status image
			if(!$m['forumtopic_status']) {
				$pathtoimage = K_PATH_IMAGES_FORUM;
				$status_alt = $l['w_enabled'];
			}
			else {
				$pathtoimage = K_PATH_IMAGES_FORUM."locked_";
				$status_alt = $l['w_locked'];
			}
			if($_SESSION['session_last_visit'] >= strtotime($lastpost->time)) {
				echo "<img src=\"".$pathtoimage."no_new_posts.gif\" border=\"0\" alt=\"".$status_alt." - ".$l['w_no_new_posts']."\" />";
			}
			else {
				echo "<img src=\"".$pathtoimage."new_posts.gif\" border=\"0\" alt=\"".$status_alt." - ".$l['w_new_posts']."\" />";
			}
			
			//show EDIT button only to authorized users
			if(!$fdata->status) {
				if($usertype OR (($m['forumtopic_poster'] == $userid) AND ((($fdata->edittimelimit * K_SECONDS_IN_HOUR ) + $m['forumtopic_time']) < time())) ) {
					//show EDIT button
					F_generic_submit_button("form_edit_buttons", "edit".$m['forumtopic_id']."", $l['w_edit'], "document.form_edit_buttons.topicid.value='".$m['forumtopic_id']."'");
				}
			}
			echo "</td>";
			
			echo "<td class=\"fill".$rowclass."E\">";
			echo "<a href=\"cp_forum_view.".CP_EXT."?fmode=top&amp;topid=".$m['forumtopic_id']."&amp;forid=".$forid."&amp;catid=".$catid."\">".htmlentities($m['forumtopic_title'], ENT_NOQUOTES, $l['a_meta_charset'])."</a>";
			echo "</td>";
			
			echo "<td class=\"fill".$rowclass."O\" align=\"right\">";
			echo "".$m['forumtopic_replies']."";
			echo "</td>";
			
			echo "<td class=\"fill".$rowclass."E\" align=\"right\">";
			echo "".$m['forumtopic_views']."";
			echo "</td>";
			
			echo "<td class=\"fill".$rowclass."O\" align=\"center\">";
			echo "<a href=\"cp_user_profile.".CP_EXT."?user_id=".$m['forumtopic_poster']."\">".htmlentities($poster->name, ENT_NOQUOTES, $l['a_meta_charset'])."</a>";
			echo "</td>";
			
			echo "<td class=\"fill".$rowclass."E\" align=\"center\">";
			echo "<small>".$m['forumtopic_time']."</small>";
			echo "</td>";
			
			echo "<td class=\"fill".$rowclass."O\" align=\"center\">";
			echo "<a href=\"cp_forum_view.".CP_EXT."?fmode=top&amp;topid=".$m['forumtopic_id']."&amp;forid=".$forid."&amp;catid=".$catid."&amp;orderdir=1\"><small>".$lastpost->time."</small></a>";
			echo "</td>";
			
			echo "</tr>";
		}
	}
	else {
		F_display_db_error();
	}
	
	echo "</table>";
	echo "</td></tr></table>";
	echo "</form>";
	// --- ------------------------------------------------------
	// --- page jump
	
	$sql = "SELECT count(*) AS total FROM ".K_TABLE_FORUM_TOPICS." WHERE forumtopic_forumid='".$forid."'";
	if (!empty($order_field)) {$param_array = "&amp;order_field=".urlencode($order_field)."";}
	if (!empty($orderdir)) {$param_array .= "&amp;orderdir=".$orderdir."";}
	$param_array .= "&amp;submitted=1";
	$param_array .= "&amp;fmode=for";
	if (!empty($catid)) {$param_array .= "&amp;catid=".$catid."";}
	if (!empty($forid)) {$param_array .= "&amp;forid=".$forid."";}
	if (!empty($forumsearch)) {$param_array .= "&amp;forumsearch=".$forumsearch."";}
	if (!empty($term)) {$param_array .= "&amp;term=".urlencode($term)."";}
	if (!empty($addterms)) {$param_array .= "&amp;addterms=".$addterms."";}
	F_show_page_navigator($_SERVER['SCRIPT_NAME'], $aiocp_dp, $sql, $firstrow, $rowsperpage, $param_array);
	
	?>
	<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_forumtopicorder" id="form_forumtopicorder">
	<input type="hidden" name="fmode" id="fmode" value="for" />
	<input type="hidden" name="catid" id="catid" value="<?php echo $catid; ?>" />
	<input type="hidden" name="forid" id="forid" value="<?php echo $forid; ?>" />
	<input type="hidden" name="firstrow" id="firstrow" value="<?php echo $firstrow; ?>" />
	<input type="hidden" name="order_field" id="order_field" value="<?php echo $order_field; ?>" />
	<input type="hidden" name="orderdir" id="orderdir" value="<?php echo $orderdir; ?>" />
	<input type="hidden" name="submitted" id="submitted" value="0" />
	</form>
	<!-- Submit form with new order field ==================== -->
	<script language="JavaScript" type="text/javascript">
	//<![CDATA[
	function FJ_submit_forumviews_form(newfirstrow, neworder_field, neworderdir) {
		document.form_forumtopicorder.firstrow.value=newfirstrow;
		document.form_forumtopicorder.order_field.value=neworder_field;
		document.form_forumtopicorder.orderdir.value=neworderdir;
		document.form_forumtopicorder.submitted.value=1;
		document.form_forumtopicorder.submit();
	}
	//]]>
	</script>
	<!-- END Submit form with new order field ==================== -->
<?php
	return TRUE;
}

// ------------------------------------------------------------
// show all messages on a particular topic
// ------------------------------------------------------------
function F_show_topic($topid, $forid, $catid, $order_field, $orderdir, $firstrow, $rowsperpage) {
	global $l, $db, $selected_language, $aiocp_dp;
	global $menu_mode, $noupstat;
	global $term, $submitted, $forumsearch, $addterms;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_forum.'.CP_EXT);
	require_once('../../shared/code/cp_functions_user.'.CP_EXT);
	require_once('../../shared/code/cp_functions_aiocpcode.'.CP_EXT);
	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	require_once('../../shared/code/cp_functions_page.'.CP_EXT);
	
	$userid = $_SESSION['session_user_id'];
	$userlevel = $_SESSION['session_user_level'];
	
	//initialize variables
	if(!$firstrow) {$firstrow="0";}
	else {$firstrow=intval($firstrow);} 
	if(!$rowsperpage) {$rowsperpage = K_MAX_ROWS_PER_PAGE;}
	else {$rowsperpage=intval($rowsperpage);}
	
	if(!$order_field) {$order_field = "forumposts_time";}
	else {$order_field = addslashes(preg_replace("/[\s]+/i", " ", $order_field));}
	if(!$orderdir) {$orderdir=0; $nextorderdir=1; $full_order_field = $order_field;}
	else {$orderdir=1; $nextorderdir=0; $full_order_field = $order_field." DESC";}
	$full_order_field = urldecode($full_order_field);
	
	$topid = intval($topid);
	$forid = intval($forid);
	$catid = intval($catid);
	
	//read category data
	$catdata = F_get_forum_category_data($catid);
	
	//check category authorization rights
	if($userlevel < $catdata->readinglevel) {return FALSE;}
	
	// read forum data
	$fdata = F_get_forum_data($forid);
	
	//check status and authorization rights
	if( ($userlevel < $fdata->readinglevel) OR ($fdata->status == 2) ) {return FALSE;}
	
	// read topic data
	$tdata = F_get_forum_topic_data($topid);
	
	//update topic views
	$sqltw = "UPDATE IGNORE ".K_TABLE_FORUM_TOPICS." SET forumtopic_views=forumtopic_views+1 WHERE forumtopic_id='".$topid."'";
	if(!$rtw = F_aiocpdb_query($sqltw, $db)) {
		F_display_db_error();
	}
	
	//check user -----------------------
	$usertype=0; //simple user
	if($userlevel>=10) {$usertype=1;} // administrator
	else {// check if moderator
		$sql = "SELECT moderator_options FROM ".K_TABLE_FORUM_MODERATORS." WHERE (moderator_userid='".$userid."' AND moderator_forumid='".$forid."' AND moderator_categoryid='".$catid."') LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$moderator_options = $m['moderator_options'];
				$mod_options = unserialize($moderator_options);
				if(stripslashes($mod_options[5])) {$usertype=1;} // moderator
			}
		}
		else {
			F_display_db_error();
		}
	}
	//END check user -----------------------
	
	//form for buttons
	echo"\n<form action=\"cp_forum_edit_message.".CP_EXT."\" method=\"post\" enctype=\"multipart/form-data\" name=\"form_edit_buttons\" id=\"form_edit_buttons\">\n";
	echo "<input type=\"hidden\" name=\"efmm\" id=\"efmm\" value=\"\" />\n";
	echo "<input type=\"hidden\" name=\"postid\" id=\"postid\" value=\"\" />\n";
	echo "<input type=\"hidden\" name=\"topicid\" id=\"topicid\" value=\"".$topid."\" />\n";
	echo "<input type=\"hidden\" name=\"forumid\" id=\"forumid\" value=\"".$forid."\" />\n";
	echo "<input type=\"hidden\" name=\"categoryid\" id=\"categoryid\" value=\"".$catid."\" />\n";
	
	//check status and authorization level
	if( ($userlevel >= $fdata->postinglevel) AND ($userlevel >= $catdata->postinglevel) AND (!$fdata->status) AND (!$tdata->status) ) {
		//show REPLY button
		F_generic_submit_button("form_edit_buttons", "reply", $l['w_reply'], "document.form_edit_buttons.efmm.value='r'");
	}
	elseif ($fdata->status != 0) { //forum locked
		echo "".$l['m_forum_locked']."<br /><br />";
	}
	else { //user need registration
		echo "".$l['m_forum_reg_need']." [<a href=\"cp_login.".CP_EXT."\">".$l['w_login']."</a>] [<a href=\"cp_edit_user.".CP_EXT."\">".$l['w_register']."</a>]<br /><br />";
	}
	
	//print links for category and forum (current location)
	echo "<br /><br />";
	echo "<a href=\"cp_forum_view.".CP_EXT."?fmode=cat\">".$l['w_index']."</a>";
	echo " &raquo; ";
	echo "<a href=\"cp_forum_view.".CP_EXT."?fmode=cat&amp;catid=".$catid."\">".htmlentities($catdata->name, ENT_NOQUOTES, $l['a_meta_charset'])."</a>";
	echo " &raquo; ";
	echo "<a href=\"cp_forum_view.".CP_EXT."?fmode=for&amp;forid=".$forid."&amp;catid=".$catid."\">".htmlentities($fdata->name, ENT_NOQUOTES, $l['a_meta_charset'])."</a>";
	echo " &raquo; ";
	echo "<a href=\"cp_forum_view.".CP_EXT."?fmode=top&amp;topid=".$topid."&amp;forid=".$forid."&amp;catid=".$catid."&amp;noupstat=1\">".htmlentities($tdata->title, ENT_NOQUOTES, $l['a_meta_charset'])."</a>";
	
	//show MESSAGES
	echo "<table class=\"edge\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">";
	echo "<tr class=\"edge\">";
	echo "<td class=\"edge\">";
	echo "<table class=\"fill\" border=\"0\" cellspacing=\"2\" cellpadding=\"1\">";
	
	echo "<tr class=\"fill\">";
	echo "<th class=\"fillO\"><a href=\"javascript:FJ_submit_forumviews_form(0,'forumposts_time','".$nextorderdir."');\">".$l['w_data']."</a></th>";
	echo "<th class=\"fillE\">".$l['w_message']."</th>";
	echo "</tr>";
	
	// iterate all topics inside forum
	$sql = "SELECT * FROM ".K_TABLE_FORUM_POSTS." WHERE forumposts_topicid='".$topid."' ORDER BY ".$full_order_field." LIMIT ".$firstrow.",".$rowsperpage."";
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
			
			echo "<tr class=\"fill".$rowclass."\" valign=\"top\">";
			
			echo "<td class=\"fill".$rowclass."O\" valign=\"top\">";
			echo "<small>".$m['forumposts_time']."</small>";
			echo "<br />";
			$poster = F_get_user_data($m['forumposts_poster']);
			echo "<a href=\"cp_user_profile.".CP_EXT."?user_id=".$m['forumposts_poster']."\">".htmlentities($poster->name, ENT_NOQUOTES, $l['a_meta_charset'])."</a>";
			echo "<br />";
			
			$sqll = "SELECT * FROM ".K_TABLE_LEVELS." WHERE level_code='".$poster->level."' LIMIT 1";
			if($rl = F_aiocpdb_query($sqll, $db)) {
				if($ml = F_aiocpdb_fetch_array($rl)) {
					echo "<small>".htmlentities($ml['level_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</small><br />";
					if($ml['level_image']) {
						echo "<img src=\"".K_PATH_IMAGES_LEVELS.$ml['level_image']."\" border=\"0\" width=\"".$ml['level_width']."\" height=\"".$ml['level_height']."\" alt=\"".htmlentities($ml['level_name'], ENT_NOQUOTES, $l['a_meta_charset'])."\" /> ";
					}
				}
			}
			else {
				F_display_db_error();
			}
			
			echo "<br />";
			if (isset($poster->photo) AND $poster->photo) {
				echo "<a href=\"cp_user_profile.".CP_EXT."?user_id=".$m['forumposts_poster']."\"><img src=\"".K_PATH_IMAGES_USER_PHOTO.$poster->photo."\" border=\"0\" width=\"".K_USER_IMAGE_WIDTH."\" height=\"".K_USER_IMAGE_HEIGHT."\" alt=\"".htmlentities($poster->name, ENT_NOQUOTES, $l['a_meta_charset'])."\" /></a>";
			}
			echo "<br /><hr />";
			
			//show EDIT button only to authorized users
			if((!$fdata->status)AND(!$tdata->status)) {
				if($usertype OR (($m['forumposts_poster'] == $userid) AND ((($fdata->edittimelimit * K_SECONDS_IN_HOUR ) + $m['forumposts_time']) < time())) ) {
					F_generic_submit_button("form_edit_buttons", "edit".$m['forumposts_id']."", $l['w_edit'], "document.form_edit_buttons.efmm.value='e'; document.form_edit_buttons.postid.value='".$m['forumposts_id']."'");
				}
			}
			echo "</td>";
			
			echo "<td class=\"fill".$rowclass."E\" valign=\"top\">";
			echo "".F_decode_aiocp_code($m['forumposts_text'])."";
			echo "</td>";
			
			echo "</tr>";
		}
	}
	else {
		F_display_db_error();
	}
	
	echo "</table>";
	echo "</td></tr></table>";
	echo "</form>\n"; //close form for buttons
	// --- ------------------------------------------------------
	// --- page jump
	
	$sql = "SELECT count(*) AS total FROM ".K_TABLE_FORUM_POSTS." WHERE forumposts_topicid='".$topid."'";
	if (!empty($order_field)) {$param_array = "&amp;order_field=".urlencode($order_field)."";}
	if (!empty($orderdir)) {$param_array .= "&amp;orderdir=".$orderdir."";}
	$param_array .= "&amp;submitted=1";
	$param_array .= "&amp;fmode=top";
	if (!empty($topid)) {$param_array .= "&amp;topid=".$topid."";}
	if (!empty($catid)) {$param_array .= "&amp;catid=".$catid."";}
	if (!empty($forid)) {$param_array .= "&amp;forid=".$forid."";}
	if (!empty($forumsearch)) {$param_array .= "&amp;forumsearch=".$forumsearch."";}
	if (!empty($term)) {$param_array .= "&amp;term=".urlencode($term)."";}
	if (!empty($addterms)) {$param_array .= "&amp;addterms=".$addterms."";}
	F_show_page_navigator($_SERVER['SCRIPT_NAME'], $aiocp_dp, $sql, $firstrow, $rowsperpage, $param_array);
	?>
	<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_forumpostorder" id="form_forumpostorder">
	<input type="hidden" name="fmode" id="fmode" value="top" />
	<input type="hidden" name="topid" id="topid" value="<?php echo $topid; ?>" />
	<input type="hidden" name="catid" id="catid" value="<?php echo $catid; ?>" />
	<input type="hidden" name="forid" id="forid" value="<?php echo $forid; ?>" />
	<input type="hidden" name="firstrow" id="firstrow" value="<?php echo $firstrow; ?>" />
	<input type="hidden" name="order_field" id="order_field" value="<?php echo $order_field; ?>" />
	<input type="hidden" name="orderdir" id="orderdir" value="<?php echo $orderdir; ?>" />
	<input type="hidden" name="submitted" id="submitted" value="0" />
	</form>
	<!-- Submit form with new order field ==================== -->
	<script language="JavaScript" type="text/javascript">
	//<![CDATA[
	function FJ_submit_forumviews_form(newfirstrow, neworder_field, neworderdir) {
		document.form_forumpostorder.firstrow.value=newfirstrow;
		document.form_forumpostorder.order_field.value=neworder_field;
		document.form_forumpostorder.orderdir.value=neworderdir;
		document.form_forumpostorder.submitted.value=1;
		document.form_forumpostorder.submit();
	}
	//]]>
	</script>
	<!-- END Submit form with new order field ==================== -->
<?php
	
	return TRUE;
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>