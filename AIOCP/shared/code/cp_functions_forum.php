<?php
//============================================================+
// File name   : cp_functions_forum.php                        
// Begin       : 2002-01-30                                    
// Last Update : 2008-08-10
//                                                             
// Description : Functions for Forum                           
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

// ----------------------------------------------------------
// read category data
// ----------------------------------------------------------
function F_get_forum_category_data($categoryid) {
	global $db, $l;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	$sql = "SELECT * FROM ".K_TABLE_FORUM_CATEGORIES." WHERE forumcat_id='".$categoryid."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$forumcat = new stdClass();
			$forumcat->postinglevel = $m['forumcat_postinglevel'];
			$forumcat->readinglevel = $m['forumcat_readinglevel'];
			$forumcat->language = $m['forumcat_language'];
			$forumcat->name = $m['forumcat_name'];
			$forumcat->description = $m['forumcat_description'];
			$forumcat->order = $m['forumcat_order'];
			return $forumcat;
		}
	}
	else {
		F_display_db_error();
	}
	return FALSE;
}

// ----------------------------------------------------------
// read forum options
// ----------------------------------------------------------
function F_get_forum_data($forumid) {
	global $db, $l;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	
	$sql = "SELECT * FROM ".K_TABLE_FORUM_FORUMS." WHERE forum_id='".$forumid."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$forum = new stdClass();
			$forum->language = $m['forum_language'];
			$forum->categoryid = $m['forum_categoryid'];
			$forum->postinglevel = $m['forum_postinglevel'];
			$forum->status = $m['forum_status'];
			$forum->readinglevel = $m['forum_readinglevel'];
			$forum->name = $m['forum_name'];
			$forum->description = $m['forum_description'];
			$forum->order = $m['forum_order'];
			$forum->edittimelimit = $m['forum_edittimelimit'];
			$forum->lockthread = $m['forum_lockthread'];
			$forum->removezeroreply = $m['forum_removezeroreply'];
			$forum->userconfirmation = $m['forum_userconfirmation'];
			$forum->topics = $m['forum_topics'];
			$forum->posts = $m['forum_posts'];
			$forum->lasttopic = $m['forum_lasttopic'];
			return $forum;
		}
	}
	else {
		F_display_db_error();
	}
	return FALSE;
}

// ----------------------------------------------------------
// read forum options
// ----------------------------------------------------------
function F_get_forum_topic_data($topicid) {
	global $db, $l;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	
	$sql = "SELECT * FROM ".K_TABLE_FORUM_TOPICS." WHERE forumtopic_id='".$topicid."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$forumtopic = new stdClass();
			$forumtopic->forumid = $m['forumtopic_forumid'];
			$forumtopic->categoryid = $m['forumtopic_categoryid'];
			$forumtopic->status = $m['forumtopic_status'];
			$forumtopic->title = $m['forumtopic_title'];
			$forumtopic->time = $m['forumtopic_time'];
			$forumtopic->poster = $m['forumtopic_poster'];
			$forumtopic->views = $m['forumtopic_views'];
			$forumtopic->replies = $m['forumtopic_replies'];
			$forumtopic->lastpost = $m['forumtopic_lastpost'];
			return $forumtopic;
		}
	}
	else {
		F_display_db_error();
	}
	return FALSE;
}

// ----------------------------------------------------------
// get forum topicid from topic name in selected forum
// ----------------------------------------------------------
function F_get_forum_topic_id($topic_name, $forum_id) {
	global $db, $l;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	
	$sql = "SELECT * FROM ".K_TABLE_FORUM_TOPICS." WHERE forumtopic_title='".$topic_name."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			return $m['forumtopic_id'];
		}
	}
	else {
		F_display_db_error();
	}
	return FALSE;
}

// ----------------------------------------------------------
// read message data
// ----------------------------------------------------------
function F_get_forum_post_data($postid) {
	global $db, $l;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	
	$sql = "SELECT * FROM ".K_TABLE_FORUM_POSTS." WHERE forumposts_id='".$postid."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$forumposts = new stdClass();
			$forumposts->topicid = $m['forumposts_topicid'];
			$forumposts->forumid = $m['forumposts_forumid'];
			$forumposts->categoryid = $m['forumposts_categoryid'];
			$forumposts->time = $m['forumposts_time'];
			$forumposts->poster = $m['forumposts_poster'];
			$forumposts->posterip = $m['forumposts_posterip'];
			$forumposts->text = $m['forumposts_text'];
			return $forumposts;
		}
	}
	else {
		F_display_db_error();
	}
	return FALSE;
}

// ----------------------------------------------------------
// read moderator data
// ----------------------------------------------------------
function F_get_forum_moderator_data($modid) {
	global $db, $l;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	
	$sql = "SELECT * FROM ".K_TABLE_FORUM_MODERATORS." WHERE moderator_id='".$modid."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$moderator = new stdClass();
			$moderator->email = $m['moderator_email'];
			$moderator->forumid = $m['moderator_forumid'];
			$moderator->categoryid = $m['moderator_categoryid'];
			$moderator->userid = $m['moderator_userid'];
			$moderator->email = $m['moderator_email'];
			$moderator->confirmation = $m['moderator_confirmation'];
			$moderator->options = $m['moderator_options'];
			return $moderator;
		}
	}
	else {
		F_display_db_error();
	}
	return FALSE;
}

// ----------------------------------------------------------
// remove topics with no replies
// ----------------------------------------------------------
function F_remove_zeroreplies_topics($forid, $days) {
	global $db;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	
	if ($forid AND $days) {
		//calculate message expiration time
		$expiretime = time() - ($days * K_SECONDS_IN_DAY);
		$expiretime = gmdate("Y-m-d H:i:s", $expiretime);
		
		//search topics to delete and delete relative messages
		$sqlt = "SELECT * FROM ".K_TABLE_FORUM_TOPICS." WHERE ((forumtopic_forumid=".$forid.") AND (forumtopic_replies=0) AND (forumtopic_time<='".$expiretime."'))";
			if($rt = F_aiocpdb_query($sqlt, $db)) {
				while($mt = F_aiocpdb_fetch_array($rt)) {
					//delete topic messages
					$sql = "DELETE FROM ".K_TABLE_FORUM_POSTS." WHERE forumposts_topicid='".$mt['forumtopic_id']."'";
					if(!$r = F_aiocpdb_query($sql, $db)) {
						F_display_db_error();
					}
				}
			}
			else {
				F_display_db_error();
			}
		
		//delete topics
		$sql = "DELETE FROM ".K_TABLE_FORUM_TOPICS." WHERE ((forumtopic_forumid=".$forid.") AND (forumtopic_replies=0) AND (forumtopic_time<='".$expiretime."'))";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		$deletedtopics = F_aiocpdb_affected_rows();
		
		//calculate new lasttopic
		$sql = "SELECT forumtopic_id FROM ".K_TABLE_FORUM_TOPICS." WHERE forumtopic_forumid=".$forid." ORDER BY forumtopic_time DESC LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$lasttopic = $m['forumtopic_id'];
			}
		}
		else {
			F_display_db_error();
		}
		
		//update forum table stats
		$sql = "UPDATE IGNORE ".K_TABLE_FORUM_FORUMS." SET forum_topics=(forum_topics-".$deletedtopics."), forum_posts=(forum_posts-".$deletedtopics."), forum_lasttopic='".$lasttopic."' WHERE forum_id=".$forid."";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
	}
	return TRUE;
}

// ----------------------------------------------------------
// Send information message to moderator or user
// $type: 
//		n=inform moderator for new topic;
//		r=inform moderator for new message; 
//		u=inform user for reply to his message; 
// ----------------------------------------------------------
function F_send_forum_email($type, $categoryid, $forumid, $topicid, $userid, $modid, $email) {
	global $l, $db, $selected_language, $emailcfg;
			
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_user.'.CP_EXT);
	require_once('../../shared/code/cp_functions_html2txt.'.CP_EXT);
	require_once('../../shared/code/cp_functions_language.'.CP_EXT);
	require_once('../../shared/code/cp_class_mailer.'.CP_EXT);
	
	//Initialize variables
	$maildata = NULL; //this avoid passing variables from URL
	$CategoryData = NULL;
	$ForumData = NULL;
	$TopicData = NULL;
	$UserData = NULL;
	$ModData = NULL;
	
	// Instantiate C_mailer class
	$mail = new C_mailer;
	
	$mail->language = $selected_language;
	
	//Load default values
	$mail->Priority = $emailcfg->Priority;
	$mail->ContentType = $emailcfg->ContentType;
	$mail->Encoding = $emailcfg->Encoding;
	$mail->WordWrap = $emailcfg->WordWrap;
	$mail->Mailer = $emailcfg->Mailer;
	$mail->Sendmail = $emailcfg->Sendmail;
	$mail->UseMSMailHeaders = $emailcfg->UseMSMailHeaders;
	$mail->Host = $emailcfg->Host;
	$mail->Port = $emailcfg->Port;
	$mail->Helo = $emailcfg->Helo;
	$mail->SMTPAuth = $emailcfg->SMTPAuth;
	$mail->Username = $emailcfg->Username;
	$mail->Password = $emailcfg->Password;
	$mail->Timeout = $emailcfg->Timeout;
	$mail->SMTPDebug = $emailcfg->SMTPDebug;
	//$mail->SMTPclassPath = $emailcfg->SMTPclassPath;
	$mail->PluginDir = $emailcfg->PluginDir;
	
	$mail->Sender = $emailcfg->Sender;
	$mail->From = $emailcfg->From;
	$mail->FromName = $emailcfg->FromName;
	if ($emailcfg->Reply) {
		$mail->AddReplyTo($emailcfg->Reply, $emailcfg->ReplyName);
	}
	
	//read data
	$CategoryData = F_get_forum_category_data($categoryid);
	$ForumData = F_get_forum_data($forumid);
	$TopicData = F_get_forum_topic_data($topicid);
	if($modid) {$ModData = F_get_forum_moderator_data($modid);}
	
		// prepare message by case (load subject from language table)
		switch($type) {
			case "n":{
				$mail->Subject = unhtmlentities(F_word_language($CategoryData->language, "d_new_topic")); //message subject
				$mail->Body = $ModData->confirmation; // message body
				break;
			}
			case "r":{
				$mail->Subject = unhtmlentities(F_word_language($CategoryData->language, "d_new_message"));
				$mail->Body = $ModData->confirmation;
				break;
			}
			case "u":{
				$mail->Subject = unhtmlentities(F_word_language($CategoryData->language, "d_reply_to_message"));
				$mail->Body = $ForumData->userconfirmation;
				break;
			}
		}
		
	//load charset from language table
	$mail->CharSet = F_word_language($CategoryData->language, "a_meta_charset");
	if(!$mail->CharSet) {$mail->CharSet = $emailcfg->CharSet;}
	
		// get user data
		$UserData = F_get_user_data($userid);
		
		$mail->IsHTML(TRUE); // Sets message type to HTML.
		
		//--- Elaborate Templates ---
		$mail->Body = str_replace("#CATEGORY#","<a href=\"".htmlentities(urldecode(K_PATH_PUBLIC_CODE."cp_forum_view.".CP_EXT."?fmode=cat&catid=".$categoryid))."\">".htmlentities($CategoryData->name, ENT_NOQUOTES, $l['a_meta_charset'])."</a>",$mail->Body);
		$mail->Body = str_replace("#FORUM#","<a href=\"".htmlentities(urldecode(K_PATH_PUBLIC_CODE."cp_forum_view.".CP_EXT."?fmode=for&forid=".$forumid."&catid=".$categoryid))."\">".htmlentities($ForumData->name, ENT_NOQUOTES, $l['a_meta_charset'])."</a>",$mail->Body);
		$mail->Body = str_replace("#TOPIC#","<a href=\"".htmlentities(urldecode(K_PATH_PUBLIC_CODE."cp_forum_view.".CP_EXT."?fmode=top&topid=".$topicid."&forid=".$forumid."&catid=".$categoryid))."\">".htmlentities($TopicData->title, ENT_NOQUOTES, $l['a_meta_charset'])."</a>",$mail->Body);
		$mail->Body = str_replace("#USERNAME#",htmlentities($UserData->name, ENT_NOQUOTES, $l['a_meta_charset']),$mail->Body);
		$mail->Body = str_replace("#EMAIL#","<a href=\"mailto:".$UserData->email."\">".$UserData->email."</a>",$mail->Body);
		$mail->Body = str_replace("#USERFIRSTNAME#",htmlentities($UserData->firstname, ENT_NOQUOTES, $l['a_meta_charset']),$mail->Body);
		$mail->Body = str_replace("#USERLASTNAME#",htmlentities($UserData->lastname, ENT_NOQUOTES, $l['a_meta_charset']),$mail->Body);
		if (($UserData->ip == "0.0.0.0") OR (!$UserData->ip)) {
			$UserData->ip = $_SERVER['REMOTE_ADDR'];
		}
		$mail->Body = str_replace("#USERIP#",$UserData->ip,$mail->Body);
		
		//compose alternative TEXT message body
		$mail->AltBody = F_html_to_text($mail->Body, false, true);
		
		$mail->AddAddress($email, ""); //Adds a "To" address
			
		if(!$mail->Send()) { //send email to user
			F_print_error("ERROR", $l['m_unable_to_send_confirmation_email']);
		}
		
		$mail->ClearAddresses(); // Clear all addresses
		$mail->ClearReplyTos(); // Clears all recipients assigned in the ReplyTo array.
return;
}
//============================================================+
// END OF FILE                                                 
//============================================================+
?>
