<?php
//============================================================+
// File name   : cp_functions_forum_message.php
// Begin       : 2001-12-31
// Last Update : 2007-02-08
// 
// Description : Functions for Forum Messages
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
// Edit forum messages
// $efmm : [ n=newtopic | r=reply | e=edit ]     
// ------------------------------------------------------------
function F_edit_forum_message($efmm, $categoryid, $forumid, $topicid, $postid, $forumtopic_title, $forumposts_text) {
	global $l, $db, $aiocp_dp;
	global $menu_mode;
	global $fixed_topic_title;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_forum.'.CP_EXT);
	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	require_once('../../shared/code/cp_functions_user.'.CP_EXT);
	require_once('../../shared/code/cp_functions_aiocpcode_editor.'.CP_EXT);
	
	$userid = $_SESSION['session_user_id'];
	$userip = $_SESSION['session_user_ip'];
	$userlevel = $_SESSION['session_user_level'];
	
	$userdata = F_get_user_data($userid); //get user data
	
	//check if essential arguments has been passed
	if( (!isset($efmm)) OR (!isset($categoryid)) OR (!isset($forumid)) ) {
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

	// check user authorization level and arguments values
	switch($efmm) { //n=newtopic; r=reply; e=edit;
		case "n": { // new topic
			break;
		}
		
		case "r": { // reply to a message
			//read topic data (check for topic existence and status)
			$forumtopic = F_get_forum_topic_data($topicid);
			if(!$forumtopic) {return FALSE;}
			if($forumtopic->status) {return FALSE;} //thread locked or hidden
			break;
		}
		
		case "e": { // edit a message
			//read topic data (check for topic existence and status)
			$forumtopic = F_get_forum_topic_data($topicid);
			if(!$forumtopic) {return FALSE;}
			if($forumtopic->status) {return FALSE;} //thread locked or hidden
			
			//read message data			
			$forumposts = F_get_forum_post_data($postid);
			if(!$forumposts) {return FALSE;}
			
			if(!isset($forumposts_title)) {$forumposts_title = $forumposts->title;}
			if(!isset($forumposts_text)) {$forumposts_text = $forumposts->text;}
			
			//check user -----------------------
			$usertype=0;
			if($userlevel>=10) {$usertype=1; break;} // administrator
			else {// check if moderator
				$sql = "SELECT * FROM ".K_TABLE_FORUM_MODERATORS." WHERE (moderator_userid='".$userid."' AND moderator_forumid='".$forumid."' AND moderator_categoryid='".$categoryid."') LIMIT 1";
				if($r = F_aiocpdb_query($sql, $db)) {
					if($m = F_aiocpdb_fetch_array($r)) {
						$moderator_id = $m['moderator_id'];
						$moderator_email = $m['moderator_email'];
						$moderator_options = $m['moderator_options'];
						$mod_options = unserialize($moderator_options);
						if(stripslashes($mod_options[5])) {$usertype=2;} // moderator
					}
				}
				else {
					F_display_db_error();
				}
				
			}
			//check if message author user (in time)
			if( ($usertype==0) AND ($forumposts->poster == $userid) AND ((($forum->edittimelimit * K_SECONDS_IN_HOUR) + $forumposts->time) < time()) ) {
				$usertype=3; // simple user (message author)
			}
			if($usertype==0) {return FALSE;}
			//END check user -----------------------
			
			break;
		}
		
		default : { //no right option has been select
			return FALSE;
			break;
		}
	}//end of switch
	
	//set fixed topic title
	if (isset($fixed_topic_title) AND (strlen($fixed_topic_title)>0)) {
		$forumtopic_title = $fixed_topic_title;
	}
			
	//switch by form buttons selection ----------------------------------------------------------------
	switch($menu_mode) {
		case unhtmlentities($l['w_delete']):
		case $l['w_delete']:{
		F_stripslashes_formfields(); // Delete (only for administrator or authorized moderator)
			if  (!(($efmm=="e")AND(($usertype==1)OR(($usertype==2) AND (stripslashes($mod_options[6])))))) {
				return FALSE;
			}
			else {
				$sql = "DELETE FROM ".K_TABLE_FORUM_POSTS." WHERE forumposts_id='".$postid."'";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				//update topic stats data
				$forum->posts -= 1;
				$forumtopic->replies -=1;
				if($forumtopic->replies<0) { //delete thread (topic) without messages
					$sql = "DELETE FROM ".K_TABLE_FORUM_TOPICS." WHERE forumtopic_id='".$topicid."'";
					if(!$r = F_aiocpdb_query($sql, $db)) {
						F_display_db_error();
					}
					$forum->topics -= 1;
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
				}
				else{ //update stats
					//calculate new lastpost
					$sql = "SELECT forumposts_id FROM ".K_TABLE_FORUM_POSTS." WHERE forumposts_topicid='".$topicid."' ORDER BY forumposts_time DESC LIMIT 1";
					if($r = F_aiocpdb_query($sql, $db)) {
						if($m = F_aiocpdb_fetch_array($r)) {
							$postid = $m['forumposts_id'];
						}
					}
					else {
						F_display_db_error();
					}
					//update topics table stats
					$sql = "UPDATE IGNORE ".K_TABLE_FORUM_TOPICS." SET 
					forumtopic_replies='".$forumtopic->replies."', 
					forumtopic_lastpost='".$postid."' 
					WHERE forumtopic_id='".$topicid."'";
					if(!$r = F_aiocpdb_query($sql, $db)) {
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
				//quit here to topic view
				?>
				<script language="JavaScript" type="text/javascript">
				//<![CDATA[
				document.write("<meta http-equiv='refresh' CONTENT='0;url=../code/cp_forum_view.<?php echo CP_EXT; ?>?fmode=top&amp;topid=<?php echo $topicid; ?>&amp;forid=<?php echo $forumid; ?>&amp;catid=<?php echo $categoryid; ?>&amp;noupstat=1' />");
				//]]>
				</script>
				<?php
				return TRUE;
			}
			break;
		}
		
		case unhtmlentities($l['w_update']):
		case $l['w_update']:{ // Update (edit message)
			if(!($efmm=="e")) {
				return FALSE;
			}
			else {
				//add a line at the end of the message
				$forumposts_text .= "\n\n[small]<".gmdate("Y-m-d H:i:s")." | ".$l['d_modify_by'].": [url=.cp_user_profile.".CP_EXT."?user_id=".$userid."]".$userdata->name."[/url]>[/small]";
				$sql = "UPDATE IGNORE ".K_TABLE_FORUM_POSTS." SET 
				forumposts_topicid='".$topicid."', 
				forumposts_forumid='".$forumid."', 
				forumposts_categoryid='".$categoryid."', 
				forumposts_time='".$forumposts->time."', 
				forumposts_poster='".$forumposts->poster."', 
				forumposts_posterip='".$forumposts->posterip."', 
				forumposts_text='".$forumposts_text."' 
				WHERE forumposts_id='".$postid."'";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				//quit here to topic view
				?>
				<script language="JavaScript" type="text/javascript">
				//<![CDATA[
				document.write("<meta http-equiv='refresh' CONTENT='0;url=../code/cp_forum_view.<?php echo CP_EXT; ?>?fmode=top&amp;topid=<?php echo $topicid; ?>&amp;forid=<?php echo $forumid; ?>&amp;catid=<?php echo $categoryid; ?>&amp;noupstat=1' />");
				//]]>
				</script>
				<?php
				return TRUE;
			}
			break;
		}
		
		case unhtmlentities($l['w_add']):
		case $l['w_add']:{ // Add
			$thistime = gmdate("Y-m-d H:i:s");
			if($efmm=="n") { // new topic
				$sql = "INSERT IGNORE INTO ".K_TABLE_FORUM_TOPICS." (
				forumtopic_forumid, 
				forumtopic_categoryid, 
				forumtopic_status, 
				forumtopic_title, 
				forumtopic_time, 
				forumtopic_poster, 
				forumtopic_views, 
				forumtopic_replies, 
				forumtopic_lastpost
				) VALUES (
				'".$forumid."', 
				'".$categoryid."', 
				'0', 
				'".$forumtopic_title."', 
				'".$thistime."', 
				'".$userid."', 
				'0', 
				'0', 
				'0')";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$topicid  = F_aiocpdb_insert_id();
					$forum->topics += 1;
				}
			}
			else {
				$forumtopic->replies += 1;
				//lock topic if needed
				if($forumtopic->replies >= $forum->lockthread) {
					$forumtopic->status=1;
				}
			}
			// Add message
			$sql = "INSERT IGNORE INTO ".K_TABLE_FORUM_POSTS." (forumposts_topicid, forumposts_forumid, forumposts_categoryid, forumposts_time, forumposts_poster, forumposts_posterip, forumposts_text) values ('".$topicid."','".$forumid."','".$categoryid."','".$thistime."','".$userid."','".$userip."','".$forumposts_text."')";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			else {
				$postid  = F_aiocpdb_insert_id();
				$forum->posts += 1;
			}
			//update topic stats data
			$sql = "UPDATE IGNORE ".K_TABLE_FORUM_TOPICS." SET 
			forumtopic_status='".$forumtopic->status."', 
			forumtopic_replies='".$forumtopic->replies."', 
			forumtopic_lastpost='".$postid."' 
			WHERE forumtopic_id='".$topicid."'";
			if(!$r = F_aiocpdb_query($sql, $db)) {
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
			
			// send email to moderators if selected
			$sql = "SELECT * FROM ".K_TABLE_FORUM_MODERATORS." WHERE moderator_forumid='".$forumid."'";
			if($r = F_aiocpdb_query($sql, $db)) {
				while($m = F_aiocpdb_fetch_array($r)) {
					$mod_id = $m['moderator_id'];
					$mod_email = $m['moderator_email'];
					$mod_opt = $m['moderator_options'];
					$modopt = unserialize($mod_opt);
					if(((stripslashes($modopt[0])==1) AND ($efmm=="n")) OR (stripslashes($modopt[0])==2)) {
						F_send_forum_email($efmm, $categoryid, $forumid, $topicid, $userid, $mod_id, $mod_email);
					}
				}
			}
			else {
				F_display_db_error();
			}
			//send email to topic poster
			if($efmm=="r") {
				$topicposterdata = F_get_user_data($forumtopic->poster);
				if($topicposterdata->email) {
					F_send_forum_email("u", $categoryid, $forumid, $topicid, $userid, "", $topicposterdata->email);
				}
			}
			//quit here to topic view
			?>
			<script language="JavaScript" type="text/javascript">
			//<![CDATA[
			document.write("<meta http-equiv='refresh' CONTENT='0;url=../code/cp_forum_view.<?php echo CP_EXT; ?>?fmode=top&amp;topid=<?php echo $topicid; ?>&amp;forid=<?php echo $forumid; ?>&amp;catid=<?php echo $categoryid; ?>&amp;noupstat=1' />");
			//]]>
			</script>
			<?php
			return TRUE;
			break;
		}
		
		case unhtmlentities($l['w_clear']):
		case $l['w_clear']:{ // Clear form fields
			$forumtopic_title = "";
			$forumposts_text = "";
			break;
		}
		
		case unhtmlentities($l['w_cancel']):
		case $l['w_cancel']:{ // Update (edit message)
			//quit here to topic view
			?>
			<script language="JavaScript" type="text/javascript">
			//<![CDATA[
<?php 
		if($efmm != "n") {
?>
			document.write("<meta http-equiv='refresh' CONTENT='0;url=../code/cp_forum_view.<?php echo CP_EXT; ?>?fmode=top&amp;topid=<?php echo $topicid; ?>&amp;forid=<?php echo $forumid; ?>&amp;catid=<?php echo $categoryid; ?>&amp;noupstat=1' />");
<?php 
		}
		else {
?>
			document.write("<meta http-equiv='refresh' CONTENT='0;url=../code/cp_forum_view.<?php echo CP_EXT; ?>?fmode=for&amp;forid=<?php echo $forumid; ?>&amp;catid=<?php echo $categoryid; ?>&amp;noupstat=1' />");
<?php 
		}
?>
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

	// Initialize variables:

// -----------------------------------------------------------

//print links for category and forum (current location)
	echo "<a href=\"cp_forum_view.".CP_EXT."?fmode=cat\">".$l['w_index']."</a>";
	echo " &raquo; ";
	echo "<a href=\"cp_forum_view.".CP_EXT."?fmode=cat&amp;catid=".$categoryid."\">".htmlentities($forumcat->name, ENT_NOQUOTES, $l['a_meta_charset'])."</a>";
	echo " &raquo; ";
	echo "<a href=\"cp_forum_view.".CP_EXT."?fmode=for&amp;forid=".$forumid."&amp;catid=".$categoryid."\">".htmlentities($forum->name, ENT_NOQUOTES, $l['a_meta_charset'])."</a>";
	if($efmm!="n") {
		echo " &raquo; ";
		echo "<a href=\"cp_forum_view.".CP_EXT."?fmode=top&amp;topid=".$topicid."&amp;forid=".$forumid."&amp;catid=".$categoryid."&amp;noupstat=1\">".htmlentities($forumtopic->title, ENT_NOQUOTES, $l['a_meta_charset'])."</a>";
	}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_forummsgeditor" id="form_forummsgeditor">

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<?php
if($efmm=="n") {// newtopic
	echo "<tr class=\"fillO\">";
	echo "<td class=\"fillOO\" align=\"right\"><b>".F_display_field_name('w_subject', '')."</b></td>";
	echo "<td class=\"fillOE\"><input type=\"text\" name=\"forumtopic_title\" id=\"forumtopic_title\"";
	// set topic title to a fixed value
	if (isset($fixed_topic_title) AND (strlen($fixed_topic_title)>0)) {
		echo " disabled=\"disabled\"";
	}
	echo " value=\"".$forumtopic_title."\" size=\"50\" maxlength=\"255\" /></td>";
	echo "</tr>";
}
?>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b><?php echo F_display_field_name('w_message', ''); ?></b></td>
<td class="fillEE"><textarea cols="60" rows="20" name="forumposts_text" id="forumposts_text" onSelect="FJ_store_caret (this);" onclick="FJ_store_caret (this);" onKeyUp="FJ_store_caret (this);"><?php echo htmlentities($forumposts_text); ?></textarea>
<br />
<?php
echo aiocpCodeEditorTagButtons("form_forummsgeditor", "forumposts_text");
$usersignature = addslashes("\r\n".$userdata->signature); //add slashes
$usersignature = str_replace("\r","\\r",$usersignature); //add slashes
$usersignature = str_replace("\n","\\n",$usersignature); //add slashes
F_generic_button("addsignature",$l['w_signature'],"FJ_insert_text (document.form_forummsgeditor.forumposts_text,'".$usersignature."')");
?>
</td>
</tr>

</table>

</td>
</tr>
<tr class="edge">
<td class="edge" align="center">

<input type="hidden" name="efmm" id="efmm" value="<?php echo $efmm; ?>" />
<input type="hidden" name="categoryid" id="categoryid" value="<?php echo $categoryid; ?>" />
<input type="hidden" name="forumid" id="forumid" value="<?php echo $forumid; ?>" />
<input type="hidden" name="topicid" id="topicid" value="<?php echo $topicid; ?>" />
<input type="hidden" name="postid" id="postid" value="<?php echo $postid; ?>" />
<input type="hidden" name="fixed_topic_title" id="fixed_topic_title" value="<?php echo $fixed_topic_title; ?>" />


<?php
// show different form buttons by case
switch($efmm) { //n=newtopic; r=reply; e=edit;
	case "n": // new message
	case "r": { // reply to a message
		F_submit_button("form_forummsgeditor","menu_mode",$l['w_add']); 
		echo" ";
		break;
	}
	case "e": { // edit a message
		if ($postid) {
			F_submit_button("form_forummsgeditor","menu_mode",$l['w_update']); 
			echo " ";		
			if( ($usertype==1) OR ( ($usertype==2) AND (stripslashes($mod_options[6])) ) ) { // administrator or moderator
				F_submit_button("form_forummsgeditor","menu_mode",$l['w_delete']); 
				echo " ";		
			}
		}
		break;
	}
	default : {
	}
}//end of switch
?>
<?php F_submit_button("form_forummsgeditor","menu_mode",$l['w_clear']); ?> 
<?php F_submit_button("form_forummsgeditor","menu_mode",$l['w_cancel']); ?>
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
</td>
</tr>

</table>
</form>

<form action="cp_aiocpcode_preview.<?php echo CP_EXT; ?>" method="post" enctype="multipart/form-data" name="form_forummsgpreview" id="form_forummsgpreview" target="_blank">
<div align="center">
<input type="hidden" name="aiocpcode" id="aiocpcode" value="<?php echo htmlentities($forumposts_text); ?>" />
<?php F_generic_submit_button("form_forummsgpreview","menu_mode",$l['w_preview'],"document.form_forummsgpreview.aiocpcode.value=document.form_forummsgeditor.forumposts_text.value"); ?>
</div>
</form>

<script language="JavaScript" src="<?php echo K_PATH_SHARED_JSCRIPTS; ?>inserttag.js" type="text/javascript"></script>
<!-- ====================================================== -->

<?php
return TRUE;
} //end of function

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
