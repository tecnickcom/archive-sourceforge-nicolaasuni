<?php
//============================================================+
// File name   : cp_chat_board.php                             
// Begin       : 2001-10-04                                    
// Last Update : 2008-07-06
//                                                             
// Description : Chat Board                                    
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
// Show Chat Board
// ------------------------------------------------------------
function F_chat() {
	global $l, $db, $selected_language;
	global $chatroom_id, $chatroom_private, $chatroom_language, $changelanguage, $newmessage, $menu_mode, $refreshtime;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_chat.'.CP_EXT);
	require_once('../../shared/code/cp_functions_dynamic_pages.'.CP_EXT);
		
	// --- Initialize variables
	if(!$refreshtime) {$refreshtime = K_CHAT_REFRESH_TIME;} //refresh time in seconds
	
	//read and assign user name and level
	$userid = $_SESSION['session_user_id'];
	$user = $_SESSION['session_user_name']; 
	$userlevel = $_SESSION['session_user_level'];
	
	if((!$chatroom_id) AND $chatroom_private) { //check if chat is private
		$chatroom_name = $chatroom_private;
		$chatroom_description = $l['w_private'];
		$chatroom_level = 0;
	} else { //read chat room data
		if(!$chatroom_language) {$chatroom_language = $selected_language;}
		if((!$chatroom_id) OR $changelanguage) {$sql = "SELECT * FROM ".K_TABLE_CHAT_ROOMS." WHERE (chatroom_level<=".$userlevel." AND chatroom_language='".$chatroom_language."') ORDER BY chatroom_name LIMIT 1";}
		else {$sql = "SELECT * FROM ".K_TABLE_CHAT_ROOMS." WHERE (chatroom_level<=".$userlevel." AND chatroom_id=".$chatroom_id.") LIMIT 1";}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
			$chatroom_id = $m['chatroom_id'];
			$chatroom_language = $m['chatroom_language'];
			$chatroom_name = $m['chatroom_name'];
			$chatroom_description = $m['chatroom_description'];
			$chatroom_level = $m['chatroom_level'];
			$chatroom_private = "";
			}
		}
		else {
			F_display_db_error();
		}
	}
	// --- END Initialize variables
	
if (!isset($chatroom_description)) {
	$chatroom_description = "";
}

F_add_online_users($user, $userid, $chatroom_id, $chatroom_private); //update user on active users list

require_once('../../shared/code/cp_functions_form.'.CP_EXT);

switch($menu_mode) {

	case unhtmlentities($l['w_send']):
	case $l['w_send']:{ // add a message
		F_add_chat_messages($newmessage, $user, $chatroom_id, $chatroom_private);
		$newmessage = "";
		break;
		}

	case "private":{ // 
		F_add_chat_messages($l['d_privatechat'], $user, $chatroom_id, $chatroom_private);
		$newmessage = "";
		break;
		}

	case unhtmlentities($l['w_refresh']):
	case $l['w_refresh']:{ 
		break;
		}

	default :{ 
		break;
		}

} //end of switch
?>

<!-- ====================================================== -->
<table class="edge" border="0" cellspacing="1" cellpadding="2">



<tr class="edge">
<td class="edge">


<table class="fill" border="0" cellspacing="2" cellpadding="1">

<?php
//display language selector only if enabled languages are more than one
if (F_count_rows(K_TABLE_LANGUAGE_CODES, "WHERE language_enabled=1") > 1) {
?>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b><?php echo F_display_field_name('w_language', 'h_chat_language'); ?></b></td>
<td class="fillOE">
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_chatroomlanguage" id="form_chatroomlanguage">
<select name="chatroom_language" id="fcl_chatroom_language" size="0" onchange="document.form_chatroomlanguage.submit()">
<?php
if(!$chatroom_id) { //check if chat is private
	echo "<option value=\"".$chatroom_id."\" selected=\"selected\">".$l['w_private']."</option>\n";
}
else {
	$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"".$m['language_code']."\"";
			if($m['language_code'] == $chatroom_language) {
				echo " selected=\"selected\"";
			}
			echo ">".htmlentities($m['language_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
		}
	}
	else {
		F_display_db_error();
	}
}
?>
</select>
<input type="hidden" name="changelanguage" id="changelanguage" value="1" />
</form>
</td>
</tr>
<!-- END SELECT language ==================== -->
<?php
}
?>

<!-- SELECT ROOM ==================== -->
<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b><?php echo F_display_field_name('w_room', 'h_chat_room'); ?></b></td>
<td class="fillEE">
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_chatroom" id="form_chatroom">
<select name="chatroom_id" id="fcr_chatroom_id" size="0" onchange="document.form_chatroom.submit()">
<?php
if(!$chatroom_id) { //check if chat is private
	echo "<option value=\"".$chatroom_id."\" selected=\"selected\">".$chatroom_name."</option>\n";
}
else {
	$sql = "SELECT chatroom_name FROM ".K_TABLE_CHAT_ROOMS." WHERE (chatroom_language='".$chatroom_language."' AND chatroom_level<=".$userlevel.") ORDER BY chatroom_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"".$m['chatroom_id']."\"";
			if($m['chatroom_id'] == $chatroom_id) {
				echo " selected=\"selected\"";
			}
			echo ">".htmlentities($m['chatroom_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
		}
	}
	else {
		F_display_db_error();
	}
}
?>
</select>
<input type="hidden" name="chatroom_language" id="fcr_chatroom_language" value="<?php echo $chatroom_language; ?>" />
</form>
</td>
</tr>
<!-- END SELECT ROOM ==================== -->
</table>
</td></tr>
</table>

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<th class="edge"><?php echo F_evaluate_modules($chatroom_description); ?></th>
<th class="edge"><?php echo $l['d_online_users']; ?></th>
</tr>

<tr class="edge">
<td class="edge">

<!-- CHAT FORM ==================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_chat" id="form_chat">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_message', 'h_chat_message'); ?></b></td>
<td class="fillEE"> <input type="text" name="newmessage" id="newmessage" value="<?php echo $newmessage; ?>" size="40" />
<?php F_submit_button("form_chat","menu_mode",$l['w_send']); ?></td>
</tr>

<tr class="fillO">
<td class="fillOE" colspan="2">
<!-- CHAT MESSAGES HISTORY ==================== -->
<textarea cols="60" rows="15" name="chathistory" id="chathistory" readonly="readonly"><?php F_show_chat_messages($user, $chatroom_id, $chatroom_private); ?></textarea>
<!-- END CHAT MESSAGES HISTORY ==================== -->
</td>
</tr>

<tr class="fillE">
<td class="fillEE" colspan="2">
<?php F_submit_button("form_chat","menu_mode",$l['w_refresh']); ?> <input type="text" name="refreshtime" id="refreshtime" value="<?php echo $refreshtime; ?>" size="3" onchange="document.form_chat.submit()" /> <b><?php echo $l['d_refreshtime']; ?></b></td>
</tr>

</table>

<input type="hidden" name="chatroom_private" id="chatroom_private" value="<?php echo $chatroom_private; ?>" />
<input type="hidden" name="chatroom_language" id="chatroom_language" value="<?php echo $chatroom_language; ?>" />
<input type="hidden" name="chatroom_id" id="chatroom_id" value="<?php echo $chatroom_id; ?>" />
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
</form>

</td>
<td class="edge" align="left" valign="top">

<!-- ONLINE USERS ==================== -->
<?php
if($chatroom_id) {
	F_show_chat_online_users($user, $chatroom_id);
}
?>
<!-- END ONLINE USERS ==================== -->

</td>
</tr>
</table>

<!-- ====================================================== -->

<script language="JavaScript" type="text/javascript">
//<![CDATA[
// refresh page every "selectedrefresh" seconds
var selectedrefresh = 1000 * document.form_chat.refreshtime.value;
setTimeout('document.form_chat.submit()', selectedrefresh);
//]]>
</script>

<?php
} //end of function

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
