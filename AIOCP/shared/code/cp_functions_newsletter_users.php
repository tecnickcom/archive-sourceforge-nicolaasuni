<?php
//============================================================+
// File name   : cp_functions_newsletter_users.php             
// Begin       : 2001-10-22                                    
// Last Update : 2008-08-10                                    
//                                                             
// Description : Functions for Newsletter Users                
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
// enable/disable user if the arguments are right
// $enable=1 enable
// $enable=0 disable
// ------------------------------------------------------------
function F_enable_newsletter_user($enable, $nlcatid, $email, $verifycode, $userid) {
	global $db, $l;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_newsletter_data.'.CP_EXT);
	
	//read newsletter category data
	$CategoryData = F_get_newsletter_category_data($nlcatid);
				
	$sql = "UPDATE IGNORE ".K_TABLE_NEWSLETTER_USERS." SET nluser_enabled=".$enable." WHERE (nluser_verifycode='".$verifycode."' AND nluser_nlcatid='".$nlcatid."' AND nluser_email='".$email."')";
	if($r = F_aiocpdb_query($sql, $db)) {
		if(F_aiocpdb_affected_rows()) { //record updated
			if($enable) {F_print_error("MESSAGE", $email.": ".$l['m_nlemail_enabled']);
				if(($CategoryData->informfor)>=1) {F_send_admin_info(1, $verifycode, $nlcatid, $email, $userid);}
			}
			else {
				F_print_error("MESSAGE", $email.": ".$l['m_nlemail_disabled']);
				if(($CategoryData->informfor)>=1) {F_send_admin_info(2, $verifycode, $nlcatid, $email, $userid);}
			}
			return TRUE;
		}
		else { //check if record exist
			$sql = "SELECT * FROM ".K_TABLE_NEWSLETTER_USERS." WHERE (nluser_verifycode='".$verifycode."' AND nluser_nlcatid='".$nlcatid."' AND nluser_email='".$email."') LIMIT 1";
			if($r = F_aiocpdb_query($sql, $db)) {
				if($m = F_aiocpdb_fetch_array($r)) { // record exist
					if($m['nluser_enabled']==2) {
						F_print_error("ERROR", $email.": ".$l['m_nlemail_banned']);
						return FALSE;
					}
					else {
						if($enable) {F_print_error("WARNING", $email.": ".$l['m_nlemail_already_enabled']);}
						else {F_print_error("WARNING", $email.": ".$l['m_nlemail_already_disabled']);}
						return FALSE;
					}
				}
				else {
					F_print_error("ERROR", $email.": ".$l['m_nlemail_notexist']);
					return FALSE;
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
}

// ------------------------------------------------------------
// subscribe user
// ------------------------------------------------------------
function F_subscribe_newsletter_user($nluser_nlcatid, $nluser_email) {
	global $l, $db, $selected_language;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	
	//check if nluser_email/nluser_nlcatid combination is unique or if it's disabled (not confirmed)
	//nluser_enabled=2 means that user is banned
	$sql = "SELECT * FROM ".K_TABLE_NEWSLETTER_USERS." WHERE nluser_nlcatid='".$nluser_nlcatid."' AND nluser_email='".$nluser_email."' AND nluser_enabled>0";
	if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
			F_print_error("WARNING", $l['m_duplicate_email']);
			return FALSE;
	}
	else { //add item
		$nluser_signupdate = time(); // get the actual date and time
		$nluser_userid = $_SESSION['session_user_id'];
		$nluser_userip = $_SERVER['REMOTE_ADDR']; // get the ip number of the actual user
		//generate verification code:
		mt_srand((double)microtime()*1000000);
		$nluser_verifycode = md5(uniqid(mt_rand(),true));
		$sql = "INSERT IGNORE INTO ".K_TABLE_NEWSLETTER_USERS." (
		nluser_nlcatid, 
		nluser_userid, 
		nluser_userip, 
		nluser_email, 
		nluser_signupdate, 
		nluser_verifycode, 
		nluser_enabled
		) VALUES (
		'".$nluser_nlcatid."', 
		'".$nluser_userid."', 
		'".$nluser_userip."', 
		'".$nluser_email."', 
		'".$nluser_signupdate."', 
		'".$nluser_verifycode."', 
		0)";
		if($r = F_aiocpdb_query($sql, $db)) {
			//send confirmation email
			$nluser_id = F_aiocpdb_insert_id();
			F_send_newsletter_verification($nluser_verifycode, $nluser_nlcatid, $nluser_email, $nluser_userid);
		}
		else {
			F_display_db_error();
		}
	}
	return TRUE;
}

// ------------------------------------------------------------
// display subscription form
// ------------------------------------------------------------
function F_newsletter_subscription_form($fullselect, $nlcat_language, $nlmsg_nlcatid) {
	global $l, $db, $selected_language;
	global $nluser_email, $x_nluser_email, $changelanguage, $menu_mode, $aiocp_dp;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	
	// Initialize variables
	$userlevel = $_SESSION['session_user_level'];
	$userid = $_SESSION['session_user_id'];
	
	if(!$nlcat_language) {$nlcat_language = $selected_language;}
	
	if((!$nlmsg_nlcatid) OR $changelanguage) {
		$sql = "SELECT * FROM ".K_TABLE_NEWSLETTER_CATEGORIES." WHERE (nlcat_level<=".$userlevel." AND nlcat_enabled=1 AND nlcat_all_users=0 AND nlcat_language='".$nlcat_language."') ORDER BY nlcat_name LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$nlmsg_nlcatid = $m['nlcat_id'];
			}
			else {
				$nlmsg_nlcatid = false;
			}
		}
		else {
			F_display_db_error();
		}
	}
	
	if(isset($menu_mode) AND (($menu_mode == $l['w_subscribe']) OR ($menu_mode == unhtmlentities($l['w_subscribe'])) ) ) {
			if(F_check_form_fields()) {
				if(F_subscribe_newsletter_user($nlmsg_nlcatid, $nluser_email, $userid)) {
					F_print_error("MESSAGE", $nluser_email.": ".$l['m_user_verification_sent']);
				}
			}
	}
	
?>
<!-- ====================================================== -->
<!-- Subscription form ==================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_nlsubscription" id="form_nlsubscription">

<input type="hidden" name="ff_required" id="ff_required" value="nluser_email" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_email']; ?>" />
<input type="hidden" name="x_nluser_email" id="x_nluser_email" value="^([a-zA-Z0-9_\.\-]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$" />
<input type="hidden" name="xl_nluser_email" id="xl_nluser_email" value="<?php echo $l['w_email']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<?php 
if(!$fullselect) {
?>
	<input type="hidden" name="nlcat_language" id="nlcat_language" value="<?php echo $nlcat_language; ?>" />
	<input type="hidden" name="nlmsg_nlcatid" id="nlmsg_nlcatid" value="<?php echo $nlmsg_nlcatid; ?>" />
	<table class="fill" border="0" cellspacing="2" cellpadding="1">
<?php
}
else {
	if (F_count_rows(K_TABLE_LANGUAGE_CODES, "WHERE language_enabled=1") <= 1) {
		echo "<input type=\"hidden\" name=\"nlcat_language\" id=\"nlcat_language\" value=\"".$nlcat_language."\" />";
	}
?>
	<table class="fill" border="0" cellspacing="2" cellpadding="1">
<?php
//display language selector only if enabled languages are more than one
if (F_count_rows(K_TABLE_LANGUAGE_CODES, "WHERE language_enabled=1") > 1) {
?>
<!-- SELECT language ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_language', 'h_newslettercat_language'); ?></b></td>
<td class="fillOE">
<input type="hidden" name="changelanguage" id="changelanguage" value="0" />
 <select name="nlcat_language" id="nlcat_language" size="0" onchange="document.form_nlsubscription.changelanguage.value=1; document.form_nlsubscription.submit()">
<?php
	$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"".$m['language_code']."\"";
			if($m['language_code'] == $nlcat_language) {
				echo " selected=\"selected\"";
			}
			echo ">".htmlentities($m['language_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
		}
	}
	else {
		F_display_db_error();
	}
?>
</select>
</td>
</tr>
<!-- END SELECT language ==================== -->
<?php
}
?>

<!-- SELECT category ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_category', 'h_newslettercat_select'); ?></b></td>
<td class="fillEE">
<input type="hidden" name="changecategory" id="changecategory" value="0" />
<select name="nlmsg_nlcatid" id="nlmsg_nlcatid" size="0" onchange="document.form_nlsubscription.changecategory.value=1; document.form_nlsubscription.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_NEWSLETTER_CATEGORIES." WHERE (nlcat_level<=".$userlevel." AND nlcat_enabled=1 AND nlcat_all_users=0 AND nlcat_language='".$nlcat_language."') ORDER BY nlcat_name";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['nlcat_id']."\"";
		if($m['nlcat_id'] == $nlmsg_nlcatid) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['nlcat_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT category ==================== -->

<?php 
} 

	//display selected category:
	if($nlmsg_nlcatid) {
?>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_email', 'h_newsletter_user_email'); ?></b></td>
<td class="fillOE"><input type="text" name="nluser_email" id="nluser_email" size="20" maxlength="255" /></td>
</tr>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php F_submit_button("form_nlsubscription","menu_mode",$l['w_subscribe']); ?>
</td>
</tr>

<!-- END Subscription form ==================== -->
</table>
</form>
<!-- ====================================================== -->
<?php
	} //end if($nlmsg_nlcatid)
	else {
	?>
	</table>
	</td>
	</tr>
	</table>
	</form>
<?php
	}
} //end of function

// ------------------------------------------------------------
// send confirmation email
// ------------------------------------------------------------
function F_send_newsletter_verification($nluser_verifycode, $nlmsg_nlcatid, $nluser_email, $nluser_userid) {
	global $l, $db, $selected_language, $emailcfg;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_mime.'.CP_EXT);
	require_once('../../shared/code/cp_functions_html2txt.'.CP_EXT);
	require_once('../../shared/code/cp_functions_language.'.CP_EXT);
	require_once('../../shared/code/cp_functions_newsletter_data.'.CP_EXT);
	require_once('../../shared/code/cp_functions_user.'.CP_EXT);
	require_once('../../shared/code/cp_class_mailer.'.CP_EXT);
	
	//Initialize variables
	$maildata = NULL; //this avoid passing variables from URL
	$CategoryData = NULL;
	$UserData = NULL;
	
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
	
	//read newsletter category data
	$CategoryData = F_get_newsletter_category_data($nlmsg_nlcatid);
	
	$mail->Sender = $CategoryData->Sender;
	$mail->From = $CategoryData->From;
	$mail->FromName = $CategoryData->FromName;
	
// check if category is enabled
if(!$CategoryData->enabled) {
	F_print_error("ERROR", $l['m_newsletter_disabled']);
}
else { //send newsletter -------------------------------------
	
	//load charset from language table
	$mail->CharSet = F_word_language($CategoryData->language, "a_meta_charset");
	if(!$mail->CharSet) {$mail->CharSet = $emailcfg->CharSet;}
	
	//assign name of category to subject
	$mail->Subject = $CategoryData->name;
	
	// add reply address
	if($CategoryData->replyemail) {
		$mail->AddReplyTo($CategoryData->replyemail, $CategoryData->replyname);
	}
	
	// get user data
	$UserData = F_get_user_data($nluser_userid);
	
	$mail->IsHTML(TRUE); // Sets message type to HTML.
	
	//compose message body
	$mail->Body = $CategoryData->msg_confirmation;
	
	//compose confirmation URL
	$subscribeURL = "".K_PATH_PUBLIC_CODE."cp_newsletter_verification.".CP_EXT."?a=1&amp;b=".$nlmsg_nlcatid."&amp;c=".$nluser_email."&amp;d=".$nluser_verifycode."&amp;e=".$nluser_userid."";
	
	//--- Elaborate Templates ---
	$mail->Body = str_replace("#CATEGORYNAME#",htmlentities($CategoryData->name, ENT_NOQUOTES, $l['a_meta_charset']),$mail->Body);
	$mail->Body = str_replace("#CATEGORYDESCRIPTION#",$CategoryData->description,$mail->Body);
	$mail->Body = str_replace("#SUBSCRIBEURL#",$subscribeURL,$mail->Body);
	$mail->Body = str_replace("#EMAIL#",$nluser_email,$mail->Body);
	if (($UserData->ip == "0.0.0.0") OR (!$UserData->ip)) {
		$UserData->ip = $_SERVER['REMOTE_ADDR'];
	}
	$mail->Body = str_replace("#USERIP#",$UserData->ip,$mail->Body);
	$mail->Body = str_replace("#USERNAME#",htmlentities($UserData->name, ENT_NOQUOTES, $l['a_meta_charset']),$mail->Body);
	$mail->Body = str_replace("#USERFIRSTNAME#",htmlentities($UserData>firstname, ENT_NOQUOTES, $l['a_meta_charset']),$mail->Body);
	$mail->Body = str_replace("#USERLASTNAME#",htmlentities($UserData->lastname, ENT_NOQUOTES, $l['a_meta_charset']),$mail->Body);
	$mail->Body = str_replace("#VERIFYCODE#",$nluser_verifycode,$mail->Body);
	
	//compose alternative TEXT message body
	$mail->AltBody = F_html_to_text($mail->Body, false, true);
	
	$mail->AddAddress($nluser_email, $UserData->name); //Adds a "To" address
			
	if(!$mail->Send()) { //send email to user
		F_print_error("ERROR", $l['m_unable_to_send_confirmation_email']);
	}
	
	$mail->ClearAddresses(); // Clear all addresses
	$mail->ClearReplyTos(); // Clears all recipients assigned in the ReplyTo array.
	
	if(($CategoryData->informfor)>=2) {
		F_send_admin_info(0, $nluser_verifycode, $nlmsg_nlcatid, $nluser_email, $nluser_userid);
	}
}//END newsletter category disabled -------------------------------------
return TRUE;
}//END function


// ----------------------------------------------------------
// Send information message to administrator:
// $type: 0=join request; 1=confirmation
// ----------------------------------------------------------
function F_send_admin_info($type, $verifycode, $nlcatid, $email, $userid) {
	global $l, $db, $selected_language, $emailcfg;
			
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_mime.'.CP_EXT);
	require_once('../../shared/code/cp_functions_html2txt.'.CP_EXT);
	require_once('../../shared/code/cp_functions_language.'.CP_EXT);
	require_once('../../shared/code/cp_functions_newsletter_data.'.CP_EXT);
	require_once('../../shared/code/cp_functions_user.'.CP_EXT);
	require_once('../../shared/code/cp_class_mailer.'.CP_EXT);
	
	//Initialize variables
	$maildata = NULL; //this avoid passing variables from URL
	$CategoryData = NULL;
	$UserData = NULL;
	
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
		
	//read newsletter category data
	$CategoryData = F_get_newsletter_category_data($nlcatid);
	
	$mail->Sender = $CategoryData->Sender;
	$mail->From = $CategoryData->From;
	$mail->FromName = $CategoryData->FromName;
	
		//load join message from language table
		switch($type) {
			case 0:{
				$JoinMessage = F_word_language($CategoryData->language, "d_join_request");
				break;
			}
			case 1:{
				$JoinMessage = F_word_language($CategoryData->language, "w_subscription");
				break;
			}
			case 2:{
				$JoinMessage = F_word_language($CategoryData->language, "w_unsubscription");
				break;
			}
		}
		
		//assign subject
		$mail->Subject = $JoinMessage." - ".$CategoryData->name;
		
	//load charset from language table
	$mail->CharSet = F_word_language($CategoryData->language, "a_meta_charset");
	if(!$mail->CharSet) {$mail->CharSet = $emailcfg->CharSet;}
	

		
		//compose message body for administartor
		$mail->Body = $CategoryData->msg_admin;
		
		// get user data
		$UserData = F_get_user_data($userid);
		
		$mail->IsHTML(TRUE); // Sets message type to HTML.
	
		//--- Elaborate Templates ---
		$mail->Body = str_replace("#CATEGORYNAME#",htmlentities($CategoryData->name, ENT_NOQUOTES, $l['a_meta_charset']),$mail->Body);
		$mail->Body = str_replace("#CATEGORYDESCRIPTION#",$CategoryData->description,$mail->Body);
		$mail->Body = str_replace("#SUBSCRIBEURL#",$subscribeURL,$mail->Body);
		$mail->Body = str_replace("#EMAIL#",$email,$mail->Body);
		if (($UserData->ip == "0.0.0.0") OR (!$UserData->ip)) {
			$UserData->ip = $_SERVER['REMOTE_ADDR'];
		}
		$mail->Body = str_replace("#USERIP#",$UserData->ip,$mail->Body);
		$mail->Body = str_replace("#USERNAME#",htmlentities($UserData->name, ENT_NOQUOTES, $l['a_meta_charset']),$mail->Body);
		$mail->Body = str_replace("#USERFIRSTNAME#",htmlentities($UserData->firstname, ENT_NOQUOTES, $l['a_meta_charset']),$mail->Body);
		$mail->Body = str_replace("#USERLASTNAME#",htmlentities($UserData->lastname, ENT_NOQUOTES, $l['a_meta_charset']),$mail->Body);
		$mail->Body = str_replace("#VERIFYCODE#",$verifycode,$mail->Body);
		
		//compose alternative TEXT message body
		$mail->AltBody = F_html_to_text($mail->Body, false, true);
		
		$mail->AddAddress($CategoryData->admin_email, ""); //Adds a "To" address
			
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
