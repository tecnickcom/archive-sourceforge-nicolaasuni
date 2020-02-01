<?php
//============================================================+
// File name   : cp_functions_newsletter_users.php             
// Begin       : 2002-03-07                                    
// Last Update : 2006-11-27                                    
//                                                             
// Description : Functions for select User                     
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
// Display User selection table
// ------------------------------------------------------------
function F_select_nl_user($wherequery, $order_field, $orderdir, $firstrow, $rowsperpage) {
	global $l;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
?>
	<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_nluserselect" id="form_nluserselect">
	<?php F_show_select_nl_user($wherequery, $order_field, $orderdir, $firstrow, $rowsperpage); ?>
	<input type="hidden" name="firstrow" id="firstrow" value="<?php echo $firstrow; ?>" />
	<input type="hidden" name="order_field" id="order_field" value="<?php echo $order_field; ?>" />
	<input type="hidden" name="orderdir" id="orderdir" value="<?php echo $orderdir; ?>" />
	<input type="hidden" name="submitted" id="submitted" value="0" />
	<input type="hidden" name="usersearch" id="usersearch" value="" />
	</form>
	<!-- Submit form with new order field ==================== -->
	<script language="JavaScript" type="text/javascript">
	//<![CDATA[
	function FJ_submit_newsletterusers_form(newfirstrow, neworder_field, neworderdir) {
		document.form_nluserselect.firstrow.value=newfirstrow;
		document.form_nluserselect.order_field.value=neworder_field;
		document.form_nluserselect.orderdir.value=neworderdir;
		document.form_nluserselect.submitted.value=1;
		document.form_nluserselect.submit();
	}
	//]]>
	</script>
	<!-- END Submit form with new order field ==================== -->
<?php
return TRUE;
}

// ------------------------------------------------------------
// Display User selection table
// ------------------------------------------------------------
function F_show_select_nl_user($wherequery, $order_field, $orderdir, $firstrow, $rowsperpage) {
	global $l, $db;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_page.'.CP_EXT);
	
	if(!$firstrow) {$firstrow="0";}
	else {$firstrow=intval($firstrow);} 
	if(!$rowsperpage) {$rowsperpage = K_MAX_ROWS_PER_PAGE;}
	else {$rowsperpage=intval($rowsperpage);}
	
	if(!$order_field) {$order_field = "nluser_nlcatid";}
	else {$order_field = addslashes(preg_replace("/[\s]+/i", " ", $order_field));}
	if(!$orderdir) {$orderdir=0; $nextorderdir=1; $full_order_field = $order_field."";}
	else {$orderdir=1; $nextorderdir=0; $full_order_field = $order_field." DESC";}
	$full_order_field = urldecode($full_order_field);
	
	if(!F_count_rows(K_TABLE_NEWSLETTER_USERS)) { //if the table is void (no items) display message
		echo "<h2>".$l['m_databasempty']."</h2>";
		return FALSE;
	}
	
	if (!$wherequery) {
		$sql = "SELECT * FROM ".K_TABLE_NEWSLETTER_USERS." ORDER BY ".$full_order_field." LIMIT ".$firstrow.",".$rowsperpage."";
	}
	else {
		$sql = "SELECT * FROM ".K_TABLE_NEWSLETTER_USERS." ".$wherequery." ORDER BY ".$full_order_field." LIMIT ".$firstrow.",".$rowsperpage."";
	}
	
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			// -- Table structure with links:
			echo "<table class=\"edge\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">";
			echo "<tr class=\"edge\">";
			echo "<td class=\"edge\">";
			echo "<table class=\"fill\" border=\"0\" cellspacing=\"2\" cellpadding=\"1\">";
			echo "<tr class=\"fill\">";
			echo "<th class=\"fillO\"><a href=\"javascript:FJ_submit_newsletterusers_form(0,'nluser_nlcatid','".$nextorderdir."');\">".$l['w_category']."</a></th>";
			echo "<th class=\"fillE\"><a href=\"javascript:FJ_submit_newsletterusers_form(0,'nluser_email','".$nextorderdir."');\">".$l['w_email']."</a></th>";
			echo "<th class=\"fillE\"><a href=\"javascript:FJ_submit_newsletterusers_form(0,'nluser_signupdate','".$nextorderdir."');\">".$l['w_regdate']."</a></th>";
			echo "<th class=\"fillO\"><a href=\"javascript:FJ_submit_newsletterusers_form(0,'nluser_enabled','".$nextorderdir."');\">".$l['w_enabled']."</a></th>";
			echo "<th class=\"fillO\"><a href=\"javascript:FJ_submit_newsletterusers_form(0,'nluser_userid','".$nextorderdir."');\">".$l['w_user']."</a></th>";
			echo "<th class=\"fillE\"><a href=\"javascript:FJ_submit_newsletterusers_form(0,'nluser_userip','".$nextorderdir."');\">".$l['w_ip']."</a></th>";
			echo "</tr>";
			
			do {
				//change style for each row
				if (isset($rowodd) AND ($rowodd)) {
					$rowclass = "O";
					$rowodd=0;
				} else {
					$rowclass = "E";
					$rowodd=1;
				}
				
				echo "<tr class=\"fill".$rowclass."\">";
				
				echo "<td class=\"fill".$rowclass."O\">&nbsp;<a href=\"cp_edit_newsletter_categories.".CP_EXT."?nlcat_id=".$m['nluser_nlcatid']."\">";
				$sql2 = "SELECT nlcat_language,nlcat_name FROM ".K_TABLE_NEWSLETTER_CATEGORIES." WHERE nlcat_id=".$m['nluser_nlcatid']."";
				if($r2 = F_aiocpdb_query($sql2, $db)) {
					if($m2 = F_aiocpdb_fetch_array($r2)) {
						
						echo "[".$m2['nlcat_language']."] ".$m2['nlcat_name']."";
					}
				}
				else {
					F_display_db_error();
				}
				echo "</a></td>";
				
				echo "<td class=\"fill".$rowclass."E\">&nbsp;<a href=\"cp_edit_newsletter_users.".CP_EXT."?nluser_nlcatid=".$m['nluser_nlcatid']."&amp;nluser_id=".$m['nluser_id']."\">".$m['nluser_email']."</a></td>";
				echo "<td class=\"fill".$rowclass."O\">&nbsp;".gmdate("Y-m-d H:i:s",$m['nluser_signupdate'])."</td>";
				echo "<td class=\"fill".$rowclass."E\">&nbsp;";
				switch ($m['nluser_enabled']) {
					case 0: {
						echo $l['w_disabled'];
						break;
					}
					case 1: {
						echo $l['w_enabled'];
						break;
					}
					case 2: {
						echo $l['w_banned'];
						break;
					}
				}
				echo "</td>";
				
				$sql2 = "SELECT user_name FROM ".K_TABLE_USERS." WHERE user_id=".$m['nluser_userid']."";
				if($r2 = F_aiocpdb_query($sql2, $db)) {
					if($m2 = F_aiocpdb_fetch_array($r2)) {
						echo "<td class=\"fill".$rowclass."O\"><a href=\"cp_edit_user.".CP_EXT."?uemode=user&amp;user_id=".$m['nluser_userid']."\">".htmlentities($m2['user_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</a></td>";
					}
				}
				else {
					F_display_db_error();
				}
				
				echo "<td class=\"fill".$rowclass."E\">&nbsp;".$m['nluser_userip']."</td>";
				echo "</tr>";
			} while($m = F_aiocpdb_fetch_array($r));
			echo"</table>";
			echo "</td>";
			echo "</tr>";
			echo "</table>";
			
			// ---------------------------------------------------------------
			// -- page jumper (menu for successive pages)
			$sql = "SELECT count(*) AS total FROM ".K_TABLE_NEWSLETTER_USERS." ".$wherequery."";
			if (!empty($order_field)) {$param_array = "&amp;order_field=".urlencode($order_field)."";}
			if (!empty($orderdir)) {$param_array .= "&amp;orderdir=".$orderdir."";}
			$param_array .= "&amp;submitted=1";
			F_show_page_navigator($_SERVER['SCRIPT_NAME'], $aiocp_dp, $sql, $firstrow, $rowsperpage, $param_array);
		}
		else {
			F_print_error("MESSAGE", $l['m_search_void']);
		}
	}
	else {
		F_display_db_error();
	}
	
	return TRUE;
}
//============================================================+
// END OF FILE                                                 
//============================================================+
?>
