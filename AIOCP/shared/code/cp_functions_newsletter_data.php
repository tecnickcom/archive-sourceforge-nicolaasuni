<?php
//============================================================+
// File name   : cp_functions_newsletter_data.php              
// Begin       : 2001-10-26                                    
// Last Update : 2003-10-12                                    
//                                                             
// Description : Functions for Newsletter                      
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
// read newsletter category data
// ------------------------------------------------------------
function F_get_newsletter_category_data($nlCategory) {
	global $db;
	if($nlCategory) {
		$sql = "SELECT * FROM ".K_TABLE_NEWSLETTER_CATEGORIES." WHERE nlcat_id=".$nlCategory." LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$thisdata->nlcatid = $m['nlcat_id'];
				$thisdata->language = $m['nlcat_language'];
				$thisdata->level = $m['nlcat_level'];
				$thisdata->admin_email = $m['nlcat_admin_email'];
				$thisdata->informfor = $m['nlcat_informfor'];
				$thisdata->msg_admin = $m['nlcat_msg_admin'];
				$thisdata->Sender = $m['nlcat_sender'];
				$thisdata->From = $m['nlcat_fromemail'];
				$thisdata->FromName = $m['nlcat_fromname'];
				$thisdata->replyemail = $m['nlcat_replyemail'];
				$thisdata->replyname = $m['nlcat_replyname'];
				$thisdata->name = $m['nlcat_name'];
				$thisdata->description = $m['nlcat_description'];
				$thisdata->msg_header = $m['nlcat_msg_header'];
				$thisdata->msg_footer = $m['nlcat_msg_footer'];
				$thisdata->msg_confirmation = $m['nlcat_msg_confirmation'];
				$thisdata->enabled = $m['nlcat_enabled'];
				$thisdata->all_users = $m['nlcat_all_users'];
			}
		}
		else {
			F_display_db_error();
		}
	}
return $thisdata;
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
