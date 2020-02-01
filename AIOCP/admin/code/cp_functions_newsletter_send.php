<?php
//============================================================+
// File name   : cp_functions_newsletter_send.php              
// Begin       : 2001-10-20                                    
// Last Update : 2008-08-10                                    
//                                                             
// Description : Send selected newsletter message              
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

//------------------------------------------------------------
// Send Newsletter to registered users
//------------------------------------------------------------
function F_send_newsletter($nlmsg_id) {
	global $l, $db, $progress_log, $selected_language;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_user.'.CP_EXT);
	require_once('../../shared/code/cp_functions_mime.'.CP_EXT);
	require_once('../../shared/code/cp_functions_html2txt.'.CP_EXT);
	require_once('../../shared/code/cp_class_mailer.'.CP_EXT);
	require_once('../../shared/code/cp_functions_language.'.CP_EXT);
	require_once('../../shared/code/cp_functions_newsletter_data.'.CP_EXT);
	require_once('cp_functions_newsletter_gc.'.CP_EXT);
	
	ini_set("memory_limit", K_MAX_MEMORY_LIMIT); //extend menory limit
	set_time_limit(K_MAX_EXECUTION_TIME); //extend the maximum execution time
	
	F_gc_newsletter_users(); //call newsletter users garbage collector
	
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
	
	//read message data
	if($nlmsg_id) {
		$sql = "SELECT * FROM ".K_TABLE_NEWSLETTER_MESSAGES." WHERE nlmsg_id=".$nlmsg_id." LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$maildata->nlcatid = $m['nlmsg_nlcatid'];
				$maildata->editorid = $m['nlmsg_editorid'];
				$mail->Subject = $m['nlmsg_title'];
				$maildata->Body = $m['nlmsg_message'];
				$maildata->composedate = $m['nlmsg_composedate'];
				$maildata->sentdate = $m['nlmsg_sentdate'];
			}
		}
		else {
			F_display_db_error();
			return FALSE;
		}
	}
	
	if($maildata->sentdate) {
		F_print_error("WARNING", $l['m_newsletter_already_sent']);
		return FALSE;
	}
	else {
		//read newsletter category data
		$CategoryData = F_get_newsletter_category_data($maildata->nlcatid);
		
		$mail->Sender = $CategoryData->Sender;
		$mail->From = $CategoryData->From;
		$mail->FromName = $CategoryData->FromName;
		
		if(!$mail->Subject) { //if void assign name of category to subject
			$mail->Subject = $CategoryData->name;
		}
		
		// check if category is enabled
		if(!$CategoryData->enabled) {
			F_print_error("ERROR", $l['m_newsletter_disabled']);
			return FALSE;
		}
		else { //send newsletter -------------------------------------
			
			//load charset from language table
			$mail->CharSet = F_word_language($CategoryData->language, "a_meta_charset");
			if(!$mail->CharSet) {$mail->CharSet = $emailcfg->CharSet;}
			
			// add reply address
			if($CategoryData->replyemail) {
				$mail->AddReplyTo($CategoryData->replyemail, $CategoryData->replyname);
			}
			
			$mail->IsHTML(TRUE); // Sets message type to HTML.
			
			//compose message body
			$mailBody = "".$CategoryData->msg_header."\n";
			$mailBody .= "".$maildata->Body."\n";
			$mailBody .= "".$CategoryData->msg_footer."\n";
			
			//Elaborate some general templates
			$mailBody = str_replace("#CATEGORYNAME#",htmlentities($CategoryData->name, ENT_NOQUOTES, $l['a_meta_charset']),$mailBody);
			$mailBody = str_replace("#CATEGORYDESCRIPTION#",$CategoryData->description,$mailBody);
			
			//Add Attachments
			$sql = "SELECT * FROM ".K_TABLE_NEWSLETTER_ATTACHMENTS." WHERE nlattach_nlmsgid=".$nlmsg_id." ORDER BY nlattach_cid DESC";
			if($r = F_aiocpdb_query($sql, $db)) {
				while($m = F_aiocpdb_fetch_array($r)) {
					$name = $m['nlattach_file'];
					$path = K_PATH_FILES_ATTACHMENTS.$name;
					$type = F_choose_mime($name); //choose the appropriate MIME type
					$encoding = $emailcfg->AttachmentsEncoding;
					$unique_cid = $m['nlattach_cid'];
					if ($unique_cid) { //add inline attachment
						$mail->AddEmbeddedImage($path, $unique_cid, $name, $encoding, $type);
						//replace relative links to attachments directory to $m['nlattach_cid']
						$mailBody = str_replace($path, "cid:".$unique_cid, $mailBody);	
					}
					else {
						$mail->AddAttachment($path, $name, $encoding, $type);
					}	
				}
			}
			else {
				F_display_db_error();
				return FALSE;
			}
			
			$email_num = 0;
			
			if (!$CategoryData->all_users) {
				//itereate for each enabled user
				$sql = "SELECT * FROM ".K_TABLE_NEWSLETTER_USERS." WHERE (nluser_nlcatid=".$maildata->nlcatid." AND nluser_enabled=1)";
				
				if($r = F_aiocpdb_query($sql, $db)) {
					echo "<ul>";
					while($m = F_aiocpdb_fetch_array($r)) {
						// get user data
						$UserData = F_get_user_data($m['nluser_userid']);
						
						//compose unsubscribe URL
						$unsubscribeURL = "".K_PATH_PUBLIC_CODE."cp_newsletter_verification.".CP_EXT."?a=0&amp;b=".$maildata->nlcatid."&amp;c=".$m['nluser_email']."&amp;d=".$m['nluser_verifycode']."&amp;e=".$m['nluser_userid']."";
						
						//--- Elaborate user Templates ---
						$mail->Body = $mailBody;
						$mail->Body = str_replace("#UNSUBSCRIBEURL#",$unsubscribeURL,$mail->Body);
						$mail->Body = str_replace("#EMAIL#",$m['nluser_email'],$mail->Body);
						$mail->Body = str_replace("#USERIP#",$UserData->ip,$mail->Body);
						$mail->Body = str_replace("#USERNAME#",htmlentities($UserData->name, ENT_NOQUOTES, $l['a_meta_charset']),$mail->Body);
						$mail->Body = str_replace("#USERFIRSTNAME#",htmlentities($UserData->firstname, ENT_NOQUOTES, $l['a_meta_charset']),$mail->Body);
						$mail->Body = str_replace("#USERLASTNAME#",htmlentities($UserData->lastname, ENT_NOQUOTES, $l['a_meta_charset']),$mail->Body);
						$mail->Body = str_replace("#VERIFYCODE#",$m['nluser_verifycode'],$mail->Body);
						
						//compose alternative TEXT message body
						$mail->AltBody = F_html_to_text($mail->Body, false, true);
						
						$mail->AddAddress($m['nluser_email'], $UserData->name); //Adds a "To" address
						
						$email_num++;
						$user_log = "".$email_num." - ".$m['nluser_email'].""; //output user data
						if(!$mail->Send()) { //send email to user
		        			$user_log .= " ===&gt; ".$l['t_error'].""; //display error message
						}
						error_log($user_log."\n", 3, $progress_log); //create progress log file
						echo "<small>".$user_log."</small><br />\n"; //output processed emails
						//echo " "; //print something to keep browser live
						if (($email_num % 300) == 0) { //force flush output every 30 processed emails
							echo "<!-- ".$email_num." -->\n"; flush(); //force flush output to browser
						}
						
		    			$mail->ClearAddresses(); // Clear all addresses for next loop
					} //end of user loop
					echo "</ul>";
				}
				else {
					F_display_db_error();
				}
			}
			else { //send email to all system users (with selected language and level)
				$progress_log = "../log/cp_newsletter.log";
				@unlink($progress_log); //clear progress log file if exist
				
				$sql = "SELECT * FROM ".K_TABLE_USERS." WHERE user_language='".$CategoryData->language."' AND user_level>=".$CategoryData->level."";
				if($r = F_aiocpdb_query($sql, $db)) {
					while($m = F_aiocpdb_fetch_array($r)) {
						//--- Elaborate user Templates ---
						$mail->Body = $mailBody;
						$mail->Body = str_replace("#EMAIL#",$m['user_email'],$mail->Body);
						$mail->Body = str_replace("#USERIP#",$m['user_ip'],$mail->Body);
						$mail->Body = str_replace("#USERNAME#",htmlentities($m['user_name'], ENT_NOQUOTES, $l['a_meta_charset']),$mail->Body);
						$mail->Body = str_replace("#USERFIRSTNAME#",htmlentities($m['user_firstname'], ENT_NOQUOTES, $l['a_meta_charset']),$mail->Body);
						$mail->Body = str_replace("#USERLASTNAME#",htmlentities($m['user_lastname'], ENT_NOQUOTES, $l['a_meta_charset']),$mail->Body);
						
						//compose alternative TEXT message body
						$mail->AltBody = F_html_to_text($mail->Body, false, true);
						
						$mail->AddAddress($m['user_email'], $m['user_name']); //Adds a "To" address
						
						$email_num++;
						$user_log = "".$email_num." - ".$m['user_email'].""; //output user data
						if(!$mail->Send()) { //send email to user
		        			$user_log .= " ===&gt; ".$l['t_error'].""; //display error message
						}
						error_log($user_log."\n", 3, $progress_log); //create progress log file
						echo "<small>".$user_log."</small><br />\n"; //output processed emails
						//echo " "; //print something to keep browser live
						if (($email_num % 50) == 0) { //force flush output every 300 processed emails
							echo "<!-- ".$email_num." -->\n"; flush(); //force flush output to browser
						}
						
		    			$mail->ClearAddresses(); // Clear all addresses for next loop
					}
				}
				else {
					F_display_db_error();
				}
			}
			
			$mail->ClearCustomHeaders(); // Clears all custom headers
			$mail->ClearAllRecipients(); // Clears all recipients assigned in the TO, CC and BCC
	  		$mail->ClearAttachments(); // Clears all previously set filesystem, string, and binary attachments
			$mail->ClearReplyTos(); // Clears all recipients assigned in the ReplyTo array
			
			// Update sent date in newsletter message
			$nlmsg_sentdate = time(); // get the actual date and time
			$sql = "UPDATE IGNORE ".K_TABLE_NEWSLETTER_MESSAGES." SET nlmsg_sentdate='".$nlmsg_sentdate."' WHERE nlmsg_id=".$nlmsg_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
		}//END newsletter already sent -------------------------------------
	}//END send newsletter -------------------------------------
	return TRUE;
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
