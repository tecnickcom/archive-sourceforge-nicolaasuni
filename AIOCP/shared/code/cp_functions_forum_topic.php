<?php
//============================================================+
// File name   : cp_functions_forum_topic.php                  
// Begin       : 2002-02-25                                    
// Last Update : 2003-11-18                                    
//                                                             
// Description : Functions for Forum Topics                    
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
// Edit forum topics
// ------------------------------------------------------------
function F_edit_forum_topic($categoryid, $forumid, $topicid, $forumtopic_title, $forumtopic_status, $moveto_forum) {
	global $l, $db, $selected_language, $aiocp_dp;
	global $menu_mode;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_forum.'.CP_EXT);
	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	require_once('../../shared/code/cp_functions_user.'.CP_EXT);
	
	$userid = $_SESSION['session_user_id'];
	$userip = $_SESSION['session_user_ip'];
	$userlevel = $_SESSION['session_user_level'];
	
	$userdata = F_get_user_data($userid); //get user data
	
	//check if essential arguments has been passed
	if( (!isset($categoryid)) OR (!isset($forumid)) ) {
		return FALSE;
	}
	
	// READ options needed to verify user rights
	
	//read forum options
	$forum = F_get_forum_data($forumid);
	if(!$forum) {return FALSE;}
	if($forum->status) {return FALSE;} //forum is locked or hidden
	
	//read category options
	$forumcat = F_get_forum_category_data($categoryid);
	if(!$forumcat) {return FALSE;}
	
	//Check authorization min posting level
	if($forumcat->postinglevel > $forum->postinglevel) {
		$posting_level = $forumcat->postinglevel;
	}
	else {
		$posting_level = $forum->postinglevel;
	}
	
	if($userlevel < $posting_level) {return FALSE;} //not enough authorization rights
	
	//read topic data (check for topic existence and status)
	$forumtopic = F_get_forum_topic_data($topicid);
	if(!$forumtopic) {return FALSE;}
	
	//if($forumtopic->status) {return FALSE;} //thread locked or hidden
	
	
	//--- check user -----------------------
	$usertype=0;
	if($userlevel>=10) {$usertype=1;} // administrator
	else {// check if moderator
		$sql = "SELECT * FROM ".K_TABLE_FORUM_MODERATORS." WHERE (moderator_userid='".$userid."' AND moderator_forumid='".$forumid."' AND moderator_categoryid='".$categoryid."') LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$moderator_id = $m['moderator_id'];
				$moderator_email = $m['moderator_email'];
				$moderator_options = $m['moderator_options'];
				$mod_options = unserialize($moderator_options);
				if(stripslashes($mod_options[1]) OR stripslashes($mod_options[2]) OR stripslashes($mod_options[3]) OR stripslashes($mod_options[4]) ) {
					$usertype=2; // moderator
				}
			}
		}
		else {
			F_display_db_error();
		}
	}
	//check if topic author user (in time)
	if(($usertype==0) AND ($forumtopic->poster == $userid) AND ((($forum->edittimelimit * K_SECONDS_IN_HOUR) + $forumtopic->time)<time())) {
				$usertype=3; // simple user (topic author)
	}
	if($usertype==0) {return FALSE;}
	//--- END check user --------------------


	//switch by form buttons selection ----------------------------------------------------------------
	switch($menu_mode) {
		case unhtmlentities($l['w_delete']):
		case $l['w_delete']:{
		F_stripslashes_formfields(); // Delete (only for administrator or authorized moderator)
			if(!(($usertype==1) OR (($usertype==2) AND (stripslashes($mod_options[2]))))) {
				return FALSE;
			}
			else {
				$sql = "DELETE FROM ".K_TABLE_FORUM_TOPICS." WHERE forumtopic_id='".$topicid."'";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				//delete topic messages
				$sql = "DELETE FROM ".K_TABLE_FORUM_POSTS." WHERE forumposts_topicid='".$topicid."'";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				//calculate new stats
				$forum->topics -= 1;
				$forum->posts -= ($forumtopic->replies + 1);
				//calculate new lasttopic
				$sql = "SELECT forumtopic_id FROM ".K_TABLE_FORUM_TOPICS." WHERE forumtopic_forumid='".$forumid."' ORDER BY forumtopic_time DESC LIMIT 1";
				if($r = F_aiocpdb_query($sql, $db)) {
					if($m = F_aiocpdb_fetch_array($r)) {
						$topicid = $m['forumtopic_id'];
					}
				}
				else {
					F_display_db_error();
				}
				//update forum table stats
				$sql = "UPDATE IGNORE ".K_TABLE_FORUM_FORUMS." SET 
				forum_topics='".$forum->topics."', 
				forum_posts='".$forum->posts."', 
				forum_lasttopic='".$topicid."' 
				WHERE forum_id='".$forumid."'";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
			}
			//quit here to forum view
			?>
			<script language="JavaScript" type="text/javascript">
			//<![CDATA[
			document.write("<meta http-equiv='refresh' CONTENT='0;url=../code/cp_forum_view.<?php echo CP_EXT; ?>?fmode=for&amp;forid=<?php echo $forumid; ?>&amp;catid=<?php echo $categoryid; ?>&amp;noupstat=1' />");
			//]]>
			</script>
			<?php
			return TRUE;
			break;
		}
		
		case unhtmlentities($l['w_update']):
		case $l['w_update']:{ // Update (edit message)
			if(!(($usertype==1) OR ($usertype==3) OR (($usertype==2)AND((stripslashes($mod_options[1]))OR(stripslashes($mod_options[4])))))) {
				return FALSE;
			}
			else {
				if(($usertype==1) OR (($usertype==2) AND ((stripslashes($mod_options[1])) AND (stripslashes($mod_options[4]))))) {
					$sql = "UPDATE IGNORE ".K_TABLE_FORUM_TOPICS." SET forumtopic_status='".$forumtopic_status."', forumtopic_title='".$forumtopic_title."' WHERE forumtopic_id='".$topicid."'";
				}
				elseif  ( ($usertype==3) OR ( ($usertype==2) AND (stripslashes($mod_options[1])) ) ) {
					$sql = "UPDATE IGNORE ".K_TABLE_FORUM_TOPICS." SET forumtopic_title='".$forumtopic_title."' WHERE forumtopic_id='".$topicid."'";
				}
				else {
					$sql = "UPDATE IGNORE ".K_TABLE_FORUM_TOPICS." SET forumtopic_status='".$forumtopic_status."' WHERE forumtopic_id='".$topicid."'";
				}
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				//quit here to forum view
				?>
				<script language="JavaScript" type="text/javascript">
				//<![CDATA[
				document.write("<meta http-equiv='refresh' CONTENT='0;url=../code/cp_forum_view.<?php echo CP_EXT; ?>?fmode=for&amp;forid=<?php echo $forumid; ?>&amp;catid=<?php echo $categoryid; ?>&amp;noupstat=1' />");
				//]]>
				</script>
				<?php
				return TRUE;
			}
			break;
		}
		
		case unhtmlentities($l['w_move']):
		case $l['w_move']:{ // move
			if(!(($usertype==1) OR (($usertype==2) AND (stripslashes($mod_options[3]))))) {
				return FALSE;
			}
			else {
				$newforum = F_get_forum_data($moveto_forum);
				//move topic
				$sql = "UPDATE IGNORE ".K_TABLE_FORUM_TOPICS." SET 
				forumtopic_forumid='".$moveto_forum."', 
				forumtopic_categoryid='".$newforum->categoryid."' 
				WHERE forumtopic_id='".$topicid."'";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				//move messages in topic
				$sql = "UPDATE IGNORE ".K_TABLE_FORUM_POSTS." SET 
				forumposts_forumid='".$moveto_forum."', 
				forumposts_categoryid='".$newforum->categoryid."' 
				WHERE forumposts_topicid='".$topicid."'";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				//quit here to forum view
				?>
				<script language="JavaScript" type="text/javascript">
				//<![CDATA[
				document.write("<meta http-equiv='refresh' CONTENT='0;url=../code/cp_forum_view.<?php echo CP_EXT; ?>?fmode=for&amp;forid=<?php echo $forumid; ?>&amp;catid=<?php echo $categoryid; ?>&amp;noupstat=1' />");
				//]]>
				</script>
				<?php
				return TRUE;
			}
			break;
		}
		
		case unhtmlentities($l['w_clear']):
		case $l['w_clear']:{ // Clear form fields
			$forumtopic_title = "";
			break;
		}
		
		case unhtmlentities($l['w_cancel']):
		case $l['w_cancel']:{ // Update (edit message)
			//quit here to forum view
			?>
			<script language="JavaScript" type="text/javascript">
			//<![CDATA[
			document.write("<meta http-equiv='refresh' CONTENT='0;url=../code/cp_forum_view.<?php echo CP_EXT; ?>?fmode=for&amp;forid=<?php echo $forumid; ?>&amp;catid=<?php echo $categoryid; ?>&amp;noupstat=1' />");
			//]]>
			</script>
			<?php
			return TRUE;
			break;
		}
		
		default :{
			break;
		}
	}//end of switch

// -----------------------------------------------------------

//print links for category and forum (current location)
	echo "<a href=\"cp_forum_view.".CP_EXT."?fmode=cat\">".$l['w_index']."</a>";
	echo " &raquo; ";
	echo "<a href=\"cp_forum_view.".CP_EXT."?fmode=cat&amp;catid=".$categoryid."\">".htmlentities($forumcat->name, ENT_NOQUOTES, $l['a_meta_charset'])."</a>";
	echo " &raquo; ";
	echo "<a href=\"cp_forum_view.".CP_EXT."?fmode=for&amp;forid=".$forumid."&amp;catid=".$categoryid."\">".htmlentities($forum->name, ENT_NOQUOTES, $l['a_meta_charset'])."</a>";
?>

<!-- ====================================================== -->

<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_forumtopiceditor" id="form_forumtopiceditor">

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<th class="edge" colspan="2"><?php echo htmlentities($forumtopic->title, ENT_NOQUOTES, $l['a_meta_charset']); ?></th>
</tr>

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<?php
if( ($usertype==1) OR ($usertype==3) OR ( ($usertype==2) AND (stripslashes($mod_options[1])) ) ) {
?>
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_title', 'h_forumtopiced_title'); ?></b></td>
<td class="fillOE"><input type="text" name="forumtopic_title" id="forumtopic_title" value="<?php echo htmlentities($forumtopic->title, ENT_NOQUOTES, $l['a_meta_charset']); ?>" size="50" maxlength="255"/></td>
</tr>
<?php
}
?>

<?php
if( ($usertype==1) OR ( ($usertype==2) AND (stripslashes($mod_options[4])) ) ) {
?>
<tr class="fillE" valign="middle">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_status', 'h_forumtopiced_status'); ?></b></td>
<td class="fillEE">
<select name="forumtopic_status" id="forumtopic_status" size="0">
<?php
echo "<option value=\"0\"";
if($forumtopic->status==0) {echo " selected=\"selected\"";}
echo ">".$l['w_enabled']."</option>\n";
echo "<option value=\"1\"";
if($forumtopic->status==1) {echo " selected=\"selected\"";}
echo ">".$l['w_locked']."</option>\n";
echo "<option value=\"2\"";
if($forumtopic->status==2) {echo " selected=\"selected\"";}
echo ">".$l['w_hidden']."</option>\n";
?>
</select>
</td>
</tr>
<?php
}
?>

<?php
if(($usertype==1) OR (($usertype==2) AND (stripslashes($mod_options[3])) ) ) {
?>
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_move', 'h_forumtopiced_move'); ?></b></td>
<td class="fillOE">
<select name="moveto_forum" id="moveto_forum" size="0">
<?php
	// iterate all categories
	$sql = "SELECT * FROM ".K_TABLE_FORUM_CATEGORIES." WHERE (forumcat_language='".$selected_language."' AND forumcat_postinglevel<='".$userlevel."') ORDER BY forumcat_order";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			// iterate all forums inside current category
			$sqlf = "SELECT * FROM ".K_TABLE_FORUM_FORUMS." WHERE (forum_categoryid='".$m['forumcat_id']."' AND forum_postinglevel<='".$userlevel."' AND forum_status=0) ORDER BY forum_order";
			if($rf = F_aiocpdb_query($sqlf, $db)) {
				while($mf = F_aiocpdb_fetch_array($rf)) {
					echo "<option value=\"".$mf['forum_id']."\"";
					if($mf['forum_id'] == $forumid) {
						echo " selected=\"selected\"";
					}
					echo ">".htmlentities($m['forumcat_name'], ENT_NOQUOTES, $l['a_meta_charset'])." | ".htmlentities($mf['forum_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
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
<?php F_submit_button("form_forumtopiceditor","menu_mode",$l['w_move']); ?>
</td>
</tr>
<?php
}
?>

</table>

</td>
</tr>

<tr class="edge">
<td class="edge" align="center">

<input type="hidden" name="categoryid" id="categoryid" value="<?php echo $categoryid; ?>" />
<input type="hidden" name="forumid" id="forumid" value="<?php echo $forumid; ?>" />
<input type="hidden" name="topicid" id="topicid" value="<?php echo $topicid; ?>" />

<?php
// show different form buttons by case
if ($topicid) {
	if(($usertype==1) OR ($usertype==3) OR (($usertype==2) AND ((stripslashes($mod_options[1])) OR (stripslashes($mod_options[4]))) ) ) {
			F_submit_button("form_forumtopiceditor","menu_mode",$l['w_update']); 
	}
	
	if(($usertype==1) OR (($usertype==2) AND (stripslashes($mod_options[2])) ) ) {
		F_submit_button("form_forumtopiceditor","menu_mode",$l['w_delete']); 
	}
}
?>
<?php F_submit_button("form_forumtopiceditor","menu_mode",$l['w_clear']); ?>
<?php F_submit_button("form_forumtopiceditor","menu_mode",$l['w_cancel']); ?>
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
</td>
</tr>
</table>

</form>

<!-- ====================================================== -->

<?php
return TRUE;
} //end of function

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
