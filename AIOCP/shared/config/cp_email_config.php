<?php
//============================================================+
// File name   : cp_email_config.php                           
// Begin       : 2001-10-20                                    
// Last Update : 2008-08-10                                    
//                                                             
// Description : Default values for public variables of        
//				 C_mailer class                                
//                                                             
//                                                             
// Author: Nicola Asuni                                        
//                                                             
// (c) Copyright:                                              
//              Tecnick.com LTD                             
//              Manor Coach House, Church Hill                           
//              Aldershot, Hants, GU12 4RQ                   
//              UK                                          
//              www.tecnick.com                                
//              info@tecnick.com                               
//============================================================+

// Email priority (1 = High, 3 = Normal, 5 = low). Default value is 3.
$emailcfg->Priority = 3;

// Sets the CharSet of the message. Default value is "iso-8859-1".
$emailcfg->CharSet = "iso-8859-1";

// Sets the Content-type of the message. Default value is "text/plain".
$emailcfg->ContentType = "text/plain";

// Sets the Encoding of the message. Options for this are "8bit" (default), * "7bit", "binary", "base64", and "quoted-printable".
$emailcfg->Encoding = "8bit";

// Sets the Encoding of the attachments.
//Default value is "base64"
$emailcfg->AttachmentsEncoding = "base64";

// Sets the default Administrator email.
// the join requests and confirmations will be sent to this address
$emailcfg->AdminEmail = "";

// Sets the From email address for the message. Default value is "root@localhost".
$emailcfg->From = "";

// Sets the From name of the message. Default value is "Root User".
$emailcfg->FromName = "AIOCP";

// Sets the Sender email of the message. If not empty, will be sent via -f to sendmail * or as 'MAIL FROM' in smtp mode. Default value is "".
$emailcfg->Sender = "";

// Sets "Reply-To" address.
$emailcfg->Reply = "";

// Sets "Reply-To" name.
$emailcfg->ReplyName = "";

// Sets word wrapping on the message. Default value is false (off).
$emailcfg->WordWrap = false;

// Method to send mail: ("mail", "sendmail", or "smtp").
// Default value is "mail".
$emailcfg->Mailer = "smtp";

// Sets the path of the sendmail program. Default value is "/usr/sbin/sendmail".
$emailcfg->Sendmail = "/usr/sbin/sendmail";

// Turns Microsoft mail client headers on and off. Default value is false (off).
$emailcfg->UseMSMailHeaders = false;

// Sets default value for Header of messages
// HTML
$emailcfg->MsgHeader = "
<"."?xml version=\"1.0\" encoding=\"iso-8859-1\"?>
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\" dir=\"ltr\">
<body>
<small>You are receiving this email because you or someone using your email address is registered on this Newsletter. To unsubscribe use the following link: <a href=\"#UNSUBSCRIBEURL#\">#UNSUBSCRIBEURL#</a></small>
<hr />
<br />
";

//Sets default value for Footer of messages
// HTML or TEXT
$emailcfg->MsgFooter = "</body></html>";

//Sets default value for confirmation message (message sent after subscription)
// HTML
$emailcfg->confirmationMessage = "
<"."?xml version=\"1.0\" encoding=\"iso-8859-1\"?>
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\" dir=\"ltr\">
<body>
<p>Request verification for email: #EMAIL#<br /><br />
This is an automated response to verify a request for joining the newsletter:<br />
<b>#CATEGORYNAME#</b><br />
<i>#CATEGORYDESCRIPTION#</i><br />
This request has been submitted by remote IP: #USERIP#<br />
If this message has been sent in error, please disregard.<br /><br />
<b>TO JOIN THE NEWSLETTER, USE THE FOLLOWING LINK:<br />
<a href=\"#SUBSCRIBEURL#\">#SUBSCRIBEURL#</a></b>
</p></body></html>";

//Sets default value for administrator information message
// HTML
$emailcfg->MsgAdmin = "
<"."?xml version=\"1.0\" encoding=\"iso-8859-1\"?>
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\" dir=\"ltr\">
<body>
<p>
Newsletter:<br />
<b>#CATEGORYNAME#</b><br />
<i>#CATEGORYDESCRIPTION#</i><br />
<br />
User information:<br />
<ul>
<li>email: <a href=\"mailto:#EMAIL#\">#EMAIL#</a></li>
<li>remote IP: #USERIP#</li>
<li>username: #USERNAME#</li>
<li>firstname: #USERFIRSTNAME#</li>
<li>lastname: #USERLASTNAME#</li>
<li>verification code: #VERIFYCODE#</li>
</ul>
</p></body></html>";

// --- SMTP VARIABLES ---------------------------------------------


// Sets the SMTP hosts. All hosts must be separated by a semicolon 
// (e.g. Host("smtp1.domain.com;smtp2.domain.com").
// Hosts will be tried in order.
// Default value is "localhost".
$emailcfg->Host = "";

// Sets the SMTP server port. Default value is 25.
$emailcfg->Port = 25;

// Default value is "localhost.localdomain".
$emailcfg->Helo = "";

// Sets SMTP authentication. Remember to set the Username and Password.
// Default value is false (off).
$emailcfg->SMTPAuth = false;

// Sets SMTP username. Default value is "".
$emailcfg->Username = "";

// Sets SMTP password. Default value is "".
$emailcfg->Password = "";

// Sets the SMTP server timeout in seconds. 
// Does not function at this time because PHP for win32 does not support it.
// Default value is 10.
$emailcfg->Timeout = 10;

// Sets SMTP class debugging on or off. Default value is false (off).
$emailcfg->SMTPDebug = false;

// Sets plugins directory path
$emailcfg->PluginDir   = "../../shared/phpmailer/";


//============================================================+
// END OF FILE  
//============================================================+
?>
