<?php
//============================================================+
// File name   : cp_functions_chat.php                         
// Begin       : 2001-10-04                                    
// Last Update : 2005-06-29                                    
//                                                             
// Description : Functions for chat                            
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
// Display chat messages for selected room
// ------------------------------------------------------------
function F_show_chat_messages($user, $chatroom_id, $chatroom_private) {
	global $l, $db, $selected_language;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	F_gc_chat_messages();
	F_gc_chat_users();
	if(!$chatroom_id) { //check if chat is private
		$sql = "SELECT * FROM ".K_TABLE_CHAT_MESSAGES." WHERE (msg_roomid=0 AND (msg_roomprivate='".$chatroom_private."' OR msg_roomprivate='".$user."')) ORDER BY msg_time DESC";
	}
	else { //read current room messages and private messages
		$sql = "SELECT * FROM ".K_TABLE_CHAT_MESSAGES." WHERE (msg_roomid=".$chatroom_id." OR (msg_roomid=0 AND msg_roomprivate='".$user."')) ORDER BY msg_time DESC";
	}
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			//avoid to print my WARNING message for private request
			if(!(($m['msg_text'] == $l['d_privatechat'])AND($m['msg_user'] == $user))) { 
				echo "".gmdate("H:i:s",$m['msg_time'])."";
				echo " ".htmlentities($m['msg_user'], ENT_NOQUOTES, $l['a_meta_charset'])." > ";
				echo "".htmlentities($m['msg_text'], ENT_NOQUOTES, $l['a_meta_charset'])."\n\n";
			}
		}
	}
	else {
		F_display_db_error();
	}
return;
}

// ------------------------------------------------------------
// Display chat messages for selected room
// ------------------------------------------------------------
function F_add_chat_messages($newmessage, $user, $chatroom_id, $chatroom_private) {
	global $l, $db, $selected_language;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	$current_time =  time(); // get current date and time
	$sql = "INSERT IGNORE INTO ".K_TABLE_CHAT_MESSAGES." (msg_roomid, msg_roomprivate, msg_user, msg_time, msg_text) VALUES ('".$chatroom_id."','".$chatroom_private."','".$user."','".$current_time."','".$newmessage."')";
	if(!$r = F_aiocpdb_query($sql, $db)) {
		F_display_db_error();
	}
return;
}

// ------------------------------------------------------------
// Delete old messages
// ------------------------------------------------------------
function F_gc_chat_messages() {
	global $l, $db, $selected_language;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	$expiration_time = time() - K_CHAT_LIVE_TIME;
	$sql = "DELETE FROM ".K_TABLE_CHAT_MESSAGES." WHERE msg_time<$expiration_time";
	if(!$r = F_aiocpdb_query($sql, $db)) {
		F_display_db_error();
	}
return;
}

// ------------------------------------------------------------
// //Update online users list
// ------------------------------------------------------------
function F_add_online_users($user, $userid, $chatroom_id, $chatroom_private) {
	global $l, $db, $selected_language;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	$currenttime = time();
	$sql = "REPLACE INTO ".K_TABLE_ONLINE_USERS." (chatusers_username, chatusers_userid, chatusers_roomid, chatusers_roomprivate, chatusers_lasttime) VALUES ('".$user."','".$userid."','".$chatroom_id."','".$chatroom_private."','".$currenttime."')";
	if(!$r = F_aiocpdb_query($sql, $db)) {
		F_display_db_error();
	}
return;
}

// ------------------------------------------------------------
// Show online users
// ------------------------------------------------------------
function F_show_chat_online_users($user, $chatroom_id) {
	global $l, $db, $selected_language;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	$sql = "SELECT * FROM ".K_TABLE_ONLINE_USERS." WHERE (chatusers_roomid=".$chatroom_id." OR (chatusers_roomid=0 AND  chatusers_roomprivate='".$user."')) ORDER BY chatusers_username";
	if($r = F_aiocpdb_query($sql, $db)) {
		echo "<table class=\"fill\" border=\"0\" cellspacing=\"2\" cellpadding=\"1\">";
		while($m = F_aiocpdb_fetch_array($r)) {
			//change style for each row
			if (isset($rowodd) AND ($rowodd)) {
				$rowclass = "O";
				$rowodd = 0;
			} else {
				$rowclass = "E";
				$rowodd = 1;
			}
			
			echo "<tr class=\"fillO\"><td class=\"fill".$rowclass."O\">";
			echo " <a href=\"".htmlentities(urldecode($_SERVER['SCRIPT_NAME']))."?menu_mode=private&amp;chatroom_id=0&amp;chatroom_private=".urlencode($m['chatusers_username'])."\">".htmlentities($m['chatusers_username'], ENT_NOQUOTES, $l['a_meta_charset'])."</a><br />";
			echo "</td></tr>";
		}
		echo "</table>";
	}
	else {
		F_display_db_error();
	}
return;
}

// ------------------------------------------------------------
// Delete inactive users
// ------------------------------------------------------------
function F_gc_chat_users() {
	global $l, $db, $selected_language;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	$expiration_time = time() - K_CHAT_USER_LIVE_TIME;
	$sql = "DELETE FROM ".K_TABLE_ONLINE_USERS." WHERE chatusers_lasttime<$expiration_time";
	if(!$r = F_aiocpdb_query($sql, $db)) {
		F_display_db_error();
	}
return;
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
