<?php
//============================================================+
// File name   : cp_edit_newsletter_preview.php                
// Begin       : 2001-10-20                                    
// Last Update : 2003-11-18                                    
//                                                             
// Description : Preview selected newsletter                   
//               (as will be sent)                             
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_NEWSLETTER_PREVIEW;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

require_once('../../shared/code/cp_functions_html2txt.'.CP_EXT);
require_once('../../shared/code/cp_functions_newsletter_data.'.CP_EXT);

//Initialize variables
$maildata = NULL; //this avoid passing variables from URL

//read message data
if($nlmsg_id) {
	
	$sql = "SELECT * FROM ".K_TABLE_NEWSLETTER_MESSAGES." WHERE nlmsg_id=".$nlmsg_id." LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$maildata->nlcatid = $m['nlmsg_nlcatid'];
			$maildata->editorid = $m['nlmsg_editorid'];
			$maildata->Subject = $m['nlmsg_title'];
			$maildata->Body = $m['nlmsg_message'];
			$maildata->composedate = $m['nlmsg_composedate'];
			$maildata->sentdate = $m['nlmsg_sentdate'];
		}
	}
	else {
		F_display_db_error();
	}
	
	//read newsletter category data
	if($maildata->nlcatid) {
		//read newsletter category data
		$CategoryData = F_get_newsletter_category_data($maildata->nlcatid);
	}
	
	//compose message body
	$mail->Body = "".$CategoryData->msg_header."\n";
	$mail->Body .= "".$maildata->Body."\n";
	$mail->Body .= "".$CategoryData->msg_footer."\n";
	
	//--- Elaborate Templates ---
	$mail->Body = str_replace("#CATEGORYNAME#",htmlentities($CategoryData->name, ENT_NOQUOTES, $l['a_meta_charset']),$mail->Body);
	$mail->Body = str_replace("#CATEGORYDESCRIPTION#",$CategoryData->description,$mail->Body);
	
	echo $mail->Body; //show HTML newsletter
} // END if($nlmsg_id)

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
