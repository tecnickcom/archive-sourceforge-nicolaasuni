<?php
//============================================================+
// File name   : cp_functions_levels.php                       
// Begin       : 2001-10-18                                    
// Last Update : 2008-07-06
//                                                             
// Description : Functions for Levels                          
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
function F_show_online_users($wherequery, $order_field, $orderdir, $firstrow, $rowsperpage) {
	global $l, $aiocp_dp;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

?>
	<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_usersonline" id="form_usersonline">
	<?php F_list_online_users($wherequery, $order_field, $orderdir, $firstrow, $rowsperpage); ?>
	<input type="hidden" name="firstrow" id="firstrow" value="<?php echo $firstrow; ?>" />
	<input type="hidden" name="order_field" id="order_field" value="<?php echo $order_field; ?>" />
	<input type="hidden" name="orderdir" id="orderdir" value="<?php echo $orderdir; ?>" />
	<input type="hidden" name="submitted" id="submitted" value="0" />
	</form>
	<!-- Submit form with new order field ==================== -->
	<script language="JavaScript" type="text/javascript">
	//<![CDATA[
	function FJ_submit_usersonline_form(newfirstrow, neworder_field, neworderdir) {
		document.form_usersonline.firstrow.value=newfirstrow;
		document.form_usersonline.order_field.value=neworder_field;
		document.form_usersonline.orderdir.value=neworderdir;
		document.form_usersonline.submitted.value=1;
		document.form_usersonline.submit();
	}
	//]]>
	</script>
	<!-- END Submit form with new order field ==================== -->
<?php
return TRUE;
}

// ------------------------------------------------------------
// show online users
// ------------------------------------------------------------
function F_list_online_users($wherequery, $order_field, $orderdir, $firstrow, $rowsperpage) {
	global $l, $db;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_page.'.CP_EXT);
	
	//initialize variables
	if(!$firstrow) {$firstrow="0";}
	else {$firstrow=intval($firstrow);} 
	if(!$rowsperpage) {$rowsperpage = K_MAX_ROWS_PER_PAGE;}
	else {$rowsperpage=intval($rowsperpage);}
	
	if(!$order_field) {$order_field = "cpsession_expiry";}
	else {$order_field = addslashes(preg_replace("/[\s]+/i", " ", $order_field));}
	if(!$orderdir) {$orderdir=0; $nextorderdir=1; $full_order_field = $order_field;}
	else {$orderdir=1; $nextorderdir=0; $full_order_field = $order_field." DESC";}
	$full_order_field = urldecode($full_order_field);
	
	if(!F_count_rows(K_TABLE_SESSIONS)) { //if the table is void (no items) display message
		echo "<h2>".$l['m_databasempty']."</h2>";
		return FALSE;
	}
	
	if (!$wherequery) {
		$sql = "SELECT * FROM ".K_TABLE_SESSIONS." ORDER BY ".$full_order_field." LIMIT ".$firstrow.",".$rowsperpage."";
	}
	else {
		$sql = "SELECT * FROM ".K_TABLE_SESSIONS." ".$wherequery." ORDER BY ".$full_order_field." LIMIT ".$firstrow.",".$rowsperpage."";
	}
	
	echo "<table class=\"edge\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">";
	echo "<tr class=\"edge\">";
	echo "<td class=\"edge\">";
	echo "<table class=\"fill\" border=\"0\" cellspacing=\"2\" cellpadding=\"1\">";
	
	echo "<tr class=\"fill\">";
	echo "<th class=\"fillO\">".$l['w_user']."</th>";
	echo "<th class=\"fillE\">".$l['w_level']."</th>";
	echo "<th class=\"fillO\">".$l['w_language']."</th>";
	
	//display IP only on admin panel
	if ((defined("K_AUTH_ADMIN_CP_SHOW_ONLINE_USERS")) AND ($_SESSION['session_user_level'] >= K_AUTH_ADMIN_CP_SHOW_ONLINE_USERS)) {
		echo "<th class=\"fillE\">".$l['w_ip']."</th>";
	}
	echo "</tr>";
	
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			
			//change style for each row
			if (isset($rowodd) AND ($rowodd)) {
				$rowclass = "O";
				$rowodd=0;
			} else {
				$rowclass = "E";
				$rowodd=1;
			}
			
			$this_session = sess_string_to_array($m['cpsession_data']);
			echo "<tr class=\"fill".$rowclass."\">";
			
			echo "<td class=\"fill".$rowclass."O\">";
			echo "<a href=\"cp_user_profile.".CP_EXT."?user_id=".$this_session['session_user_id']."\">".$this_session['session_user_name']."</a>";
			echo "</td>";
			
			$sqll = "SELECT * FROM ".K_TABLE_LEVELS." WHERE level_code='".$this_session['session_user_level']."' LIMIT 1";
			if($rl = F_aiocpdb_query($sqll, $db)) {
				if($ml = F_aiocpdb_fetch_array($rl)) {
					echo "<td class=\"fill".$rowclass."E\">";
					if($ml['level_image']) {
						echo "<img src=\"".K_PATH_IMAGES_LEVELS.$ml['level_image']."\" border=\"0\" width=\"".$ml['level_width']."\" height=\"".$ml['level_height']."\" alt=\"".htmlentities($ml['level_name'], ENT_NOQUOTES, $l['a_meta_charset'])."\" /> ";
					}
					echo "".htmlentities($ml['level_name'], ENT_NOQUOTES, $l['a_meta_charset'])."";
					echo "</td>";
				}
				else {
					echo "<td class=\"fill".$rowclass."E\">&nbsp;</td>";
				}
			}
			
			echo "<td class=\"fill".$rowclass."O\">";
			echo $this_session['session_user_language'];
			echo "</td>";
			
			if ((defined("K_AUTH_ADMIN_CP_SHOW_ONLINE_USERS")) AND ($_SESSION['session_user_level'] >= K_AUTH_ADMIN_CP_SHOW_ONLINE_USERS)) {
				echo "<td class=\"fill".$rowclass."E\">";
				echo $this_session['session_user_ip'];
				echo "</td>";
			}
			
			echo "</tr>\n";
		}
	}
	else {
		F_display_db_error();
	}
	
	echo "</table>";
	echo "</td></tr></table>";
	
	// --- ------------------------------------------------------
	// --- page jump
	
	$sql = "SELECT count(*) AS total FROM ".K_TABLE_SESSIONS." ".$wherequery."";
	if (!empty($order_field)) {$param_array = "&amp;order_field=".urlencode($order_field)."";}
	if (!empty($orderdir)) {$param_array .= "&amp;orderdir=".$orderdir."";}
	$param_array .= "&amp;submitted=1";
	F_show_page_navigator($_SERVER['SCRIPT_NAME'], "", $sql, $firstrow, $rowsperpage, $param_array);
	
	return TRUE;
}


// ------------------------------------------------------------
// Convert encoded session string data to array
// ------------------------------------------------------------
function sess_string_to_array($sd) {
	$sess_array = array();
	$vars = preg_split('/[;}]/', $sd);
	
	for ($i=0; $i < (sizeof($vars)-1); $i++) {
		$parts = explode('|', $vars[$i]);
		$key = $parts[0];
		$val = unserialize($parts[1].";");
		$sess_array[$key] = $val;
	}
	return $sess_array;
}


//============================================================+
// END OF FILE                                                 
//============================================================+
?>
