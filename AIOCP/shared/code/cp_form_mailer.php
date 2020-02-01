<?php
//============================================================+
// File name   : cp_form_mailer.php                            
// Begin       : 2001-11-07                                    
// Last Update : 2008-08-10
//                                                             
// Description : Form Mail module                              
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

require_once('../../shared/config/cp_extension.inc');
require_once('../config/cp_config.'.CP_EXT);

require_once('../../shared/code/cp_functions_html2txt.'.CP_EXT);
require_once('../../shared/code/cp_class_mailer.'.CP_EXT);
require_once('../../shared/code/cp_functions_form.'.CP_EXT);

// --- variables ---
//fields that will not be included in the message body
$hiddenFields = array("ff_to", "ff_cc", "ff_bcc", "ff_html", "ff_userdata", "ff_redirect", "ff_sort", "ff_print_blank", "ff_required", "ff_required_labels", "menu_mode");

// ------------------------------------------------------------
// Sort Form Fields
// ------------------------------------------------------------
function F_sort_form_fields() {
	global $formfields;
	switch(substr($formfields['ff_sort'],0,1)) {
		case "A":
		case "a": { //sort alphabetical
			ksort($formfields);
			break;
		}
		case "R":
		case "r": { //sort reverse alphabetical
			krsort($formfields);
			break;
		}
		case "O":
		case "o": {
			$formfields['ff_sort'] = substr($formfields['ff_sort'],2); //remove "o:" from string beginning
			$fields_order =  explode(",",$formfields['ff_sort']);
			for($i=0; $i<count($fields_order); $i++) { //for each fieldname
				$fieldname = trim($fields_order[$i]);
				$sorted_array[$fieldname] = $formfields[$fieldname]; //create new sorted array
				unset($formfields[$fieldname]); //delete taken values from original array
			}
			$formfields = array_merge($sorted_array,$formfields); //merge arrays to account remaining fields
			break;
		}
		default: {
			break;
		}
	}
	return true;
}

// ------------------------------------------------------------
// Display form fields
// ------------------------------------------------------------
function F_show_form_fields() {
	global $formfields, $hiddenFields;
	reset($formfields);
	while(list($key,$value) = each($formfields)) {
		if((!in_array($key,$hiddenFields)) AND (($formfields['ff_print_blank']) OR ($value)) AND (substr($key,0,2)!="x_") AND (substr($key,0,3)!="xl_")) {
			if(is_array($value)) {$val = implode(", ",$value);}
			$bodyfields .= "<b>".htmlspecialchars($key).":</b> ".htmlspecialchars(stripslashes($value))."<br />\n";
		}
	}
	return $bodyfields;
}

// ------------------------------------------------------------
// Clear form fields
// ------------------------------------------------------------
function F_clear_form_fields() {
	global $formfields, $hiddenFields;
	
	$formfields = F_decode_form_fields(); //decode form fields
	
	reset($formfields);
	while(list($key,$value) = each($formfields)) {
		if((!in_array($key,$hiddenFields)) AND (substr($key,0,2)!="x_") AND (substr($key,0,3)!="xl_")) {
			global $$key;
			$$key = "";
		}
	}
}

// ------------------------------------------------------------
// Return User Data
// ------------------------------------------------------------
function F_print_user_data() {
	global $l, $selected_language;
	
	$user_data = "<hr />\n";
	$user_data .= "<h2>".$l['w_user_data'].":</h2>\n";
	$user_data .= "<ul>\n";
	$user_data .= "<li>".$l['w_id'].":</b> ".$_SESSION['session_user_id']."</li>\n";
	$user_data .= "<li>".$l['w_username'].":</b> ".$_SESSION['session_user_name']."</li>\n";
	$user_data .= "<li>".$l['w_ip'].":</b> ".$_SESSION['session_user_ip']."</li>\n";
	$user_data .= "<li>".$l['w_level'].":</b> ".$_SESSION['session_user_level']."</li>\n";
	$user_data .= "<li>".$l['w_language'].":</b> ".$selected_language."</li>\n";
	$user_data .= "<li>".$l['w_browser'].":</b> ".htmlspecialchars($_SERVER['HTTP_USER_AGENT'])."</li>\n";
	$user_data .= "</ul>\n";
	return $user_data;
}

// ------------------------------------------------------------
// Evaluate form input
// ------------------------------------------------------------

if (isset($_REQUEST['menu_mode'])) {
	$menu_mode = $_REQUEST['menu_mode'];
} else {
	$menu_mode = "";
}
switch($menu_mode) {
	case unhtmlentities($l['w_send']):
	case $l['w_send']:{ // Send message
		
		$formfields = F_decode_form_fields(); //decode form fields
		
		// avoid improper use of form mail module (2004-05-31)
		if (!in_array($formfields['ff_to'], split(",",K_CONTACT_US_EMAIL))) {
			F_print_error("WARNING", $l['m_authorization_deny']);
			break;
		}
		
		// check if a recipient has been specified
		if(!($formfields['ff_to'] OR $formfields['ff_cc'] OR $formfields['ff_bcc'])) {
			F_print_error("ERROR", $l['m_mail_001']);
			break;
		}
		
		if($missing_fields = F_check_required_fields($formfields)) {
			F_print_error("WARNING", $l['m_form_missing_fields'].": ".$missing_fields);
			break;
		}
		
		if($wrong_fields = F_check_fields_format($formfields)) {
			F_print_error("WARNING", $l['m_form_wrong_fields'].": ".$wrong_fields);
			break;
		}
		
		$fmail = new C_mailer; // Instantiate C_mailer class
		
		$mail->language = $selected_language;
		
		//Load default values
		$fmail->Priority = $emailcfg->Priority;
		$fmail->ContentType = $emailcfg->ContentType;
		$fmail->Encoding = $emailcfg->Encoding;
		$fmail->WordWrap = $emailcfg->WordWrap;
		$fmail->Mailer = $emailcfg->Mailer;
		$fmail->Sendmail = $emailcfg->Sendmail;
		$fmail->UseMSMailHeaders = $emailcfg->UseMSMailHeaders;
		$fmail->Host = $emailcfg->Host;
		$fmail->Port = $emailcfg->Port;
		$fmail->Helo = $emailcfg->Helo;
		$fmail->SMTPAuth = $emailcfg->SMTPAuth;
		$fmail->Username = $emailcfg->Username;
		$fmail->Password = $emailcfg->Password;
		$fmail->Timeout = $emailcfg->Timeout;
		$fmail->SMTPDebug = $emailcfg->SMTPDebug;
		//$fmail->SMTPclassPath = $emailcfg->SMTPclassPath;
		
		$fmail->CharSet = $l['a_meta_charset'];
		$fmail->Subject = stripslashes($formfields['subject']);
		if($formfields['email']) {$fmail->AddReplyTo($formfields['email'], stripslashes($formfields['name']));}
		
		$fmail->From = $emailcfg->From;
		$fmail->FromName = $emailcfg->FromName;
		
		if($formfields['ff_to']) {$fmail->AddAddress($formfields['ff_to'], "");}
		if($formfields['ff_cc']) {$fmail->AddCC($formfields['ff_cc'], "");}
		if($formfields['ff_bcc']) {$fmail->AddBCC($formfields['ff_bcc'], "");}
		
		// Sets message type (HTML/TEXT - true/false)
		if(!strcasecmp($formfields['ff_html'],"true")) {
			$fmail->IsHTML(TRUE);
		}
		else { //default mode
			$fmail->IsHTML(FALSE);
		}
		
		if($formfields['ff_sort']) {F_sort_form_fields();} //sort fields
		
		// compose message body: -----------------
		//$fmail->Body = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">\n";
		//$fmail->Body .= "<html lang=\"".$l['a_meta_language']."\" dir=\"".$l['a_meta_dir']."\">\n";
		//send XHTML headers
		$fmail->Body .= "<"."?xml version=\"1.0\" encoding=\"".$l['a_meta_charset']."\"?\>\n";
		$fmail->Body .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"DTD/xhtml1-transitional.dtd\">\n";
		$fmail->Body .= "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"".$l['a_meta_language']."\" lang=\"".$l['a_meta_language']."\" dir=\"".$l['a_meta_dir']."\">\n";
		
		$fmail->Body .= "<head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=".$l['a_meta_charset']."\" /></head>\n";
		$fmail->Body .= "<body>\n";
		
		$fmail->Body .= "<p>";
		$fmail->Body .= F_show_form_fields();
		$fmail->Body .= "</p>";
		
		if(!strcasecmp($formfields['ff_userdata'],"true")) { //show additional user data
			$fmail->Body .= F_print_user_data();
		}
		
		$fmail->Body .= "\n</body></html>";
		// END compose message body: -----------------
		
		//add html formatting and set Alternative body
		if(!strcasecmp($formfields['ff_html'],"true")) {
			$fmail->AltBody = F_html_to_text($fmail->Body, false, true);
		}
		else {
			$fmail->Body = F_html_to_text($fmail->Body, false, true);
		}
		
		if(!$fmail->Send()) { //send email
			F_print_error("ERROR", $l['m_mail_error']);
			break;
		}
		
		$fmail->ClearAddresses();
		$fmail->ClearAttachments();
		$fmail->ClearReplyTos();
		
		if($formfields['ff_redirect']) {
			echo "<meta http-equiv='refresh' CONTENT='0;url=".$formfields['ff_redirect']."' />";
		}
		else {
			F_print_error("MESSAGE", $l['m_form_sent']);
		}
		break;
	} //end send case
	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{
		F_clear_form_fields();
		break;
	}
	default:{
		break;
	}
} //end of switch

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
