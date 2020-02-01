<?php
//============================================================+
// File name   : cp_functions_company_select.php
// Begin       : 2002-04-26
// Last Update : 2007-02-08
// 
// Description : Functions for select Companies
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
// Display company selection table
// ------------------------------------------------------------
function F_select_company($wherequery, $order_field, $orderdir, $firstrow, $rowsperpage) {
	global $l;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
?>
	<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_companyselect" id="form_companyselect">
	<?php F_show_select_company($wherequery, $order_field, $orderdir, $firstrow, $rowsperpage); ?>
	<input type="hidden" name="firstrow" id="firstrow" value="<?php echo $firstrow; ?>" />
	<input type="hidden" name="order_field" id="order_field" value="<?php echo $order_field; ?>" />
	<input type="hidden" name="orderdir" id="orderdir" value="<?php echo $orderdir; ?>" />
	<input type="hidden" name="submitted" id="submitted" value="0" />
	<input type="hidden" name="companysearch" id="companysearch" value="" />
	</form>
	<!-- Submit form with new order field ==================== -->
	<script language="JavaScript" type="text/javascript">
	//<![CDATA[
	function FJ_submit_company_form(newfirstrow, neworder_field, neworderdir) {
		document.form_companyselect.firstrow.value=newfirstrow;
		document.form_companyselect.order_field.value=neworder_field;
		document.form_companyselect.orderdir.value=neworderdir;
		document.form_companyselect.submitted.value=1;
		document.form_companyselect.submit();
	}
	//]]>
	</script>
	<!-- END Submit form with new order field ==================== -->
<?php
return TRUE;
}

// ------------------------------------------------------------
// Display company selection table
// ------------------------------------------------------------
function F_show_select_company($wherequery, $order_field, $orderdir, $firstrow, $rowsperpage) {
	global $l, $db;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_page.'.CP_EXT);
	
	if(!$firstrow) {$firstrow="0";}
	else {$firstrow=intval($firstrow);}
	if(!$rowsperpage) {$rowsperpage = K_MAX_ROWS_PER_PAGE;}
	else {$rowsperpage=intval($rowsperpage);}
	
	if(!$order_field) {$order_field = "company_name";}
	else {$order_field = addslashes(preg_replace("/[\s]+/i", " ", $order_field));}
	if(!$orderdir) {$orderdir=0; $nextorderdir=1; $full_order_field = $order_field;}
	else {$orderdir=1; $nextorderdir=0; $full_order_field = $order_field." DESC";}
	$full_order_field = urldecode($full_order_field);
	
	if(!F_count_rows(K_TABLE_USERS_COMPANY)) { //if the table is void (no items) display message
		echo "<h2>".$l['m_databasempty']."</h2>";
		return FALSE;
	}
	
	if (!$wherequery) {
		$sql = "SELECT * FROM ".K_TABLE_USERS_COMPANY." ORDER BY ".$full_order_field." LIMIT ".$firstrow.",".$rowsperpage."";
	}
	else {
		$sql = "SELECT * FROM ".K_TABLE_USERS_COMPANY." ".$wherequery." ORDER BY ".$full_order_field." LIMIT ".$firstrow.",".$rowsperpage."";
	}
	
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			// -- Table structure with links:
			echo "<table class=\"edge\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">";
			echo "<tr class=\"edge\">";
			echo "<td class=\"edge\">";
			echo "<table class=\"fill\" border=\"0\" cellspacing=\"2\" cellpadding=\"1\">";
			echo "<tr class=\"fill\">";
			echo "<th class=\"fillO\">&nbsp;</th>";
			echo "<th class=\"fillE\"><a href=\"javascript:FJ_submit_company_form(0,'company_name','".$nextorderdir."');\">".$l['w_name']."</a></th>";
			echo "<th class=\"fillO\"><a href=\"javascript:FJ_submit_company_form(0,'company_link','".$nextorderdir."');\">".$l['w_link']."</a></th>";
			echo "<th class=\"fillE\"><a href=\"javascript:FJ_submit_company_form(0,'company_supplier','".$nextorderdir."');\">".$l['w_supplier']."</a></th>";
			echo "<th class=\"fillO\"><a href=\"javascript:FJ_submit_company_form(0,'company_fiscalcode','".$nextorderdir."');\">".$l['w_fiscalcode']."</a></th>";
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
				echo "<td class=\"fill".$rowclass."O\"><a href=\"cp_edit_user.".CP_EXT."?uemode=company&amp;user_id=".$m['company_userid']."\"><b>".$l['w_edit']."</b></a></td>";
				echo "<td class=\"fill".$rowclass."E\">&nbsp;".htmlentities($m['company_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</td>";
				echo "<td class=\"fill".$rowclass."O\">&nbsp;";
				if (isset($mc['company_link']) AND !empty($mc['company_link'])) {
					if (substr($mc['company_link'],0,4)!="http") {
						$mc['company_link'] = "http://".$mc['company_link'];
					}
					echo "<a href=\"".htmlentities(urldecode($m['company_link']))."\" target=\"_blank\">".htmlentities(urldecode($m['company_link']))."</a>";
				}
				echo "</td>";
				echo "<td class=\"fill".$rowclass."E\">&nbsp;";
				if ($m['company_supplier']) {
							echo $l['w_yes'];
						}
						else {
							echo $l['w_no'];
						}
				echo "</td>";
				echo "<td class=\"fill".$rowclass."O\">&nbsp;".htmlentities($m['company_fiscalcode'], ENT_NOQUOTES, $l['a_meta_charset'])."</td>";
				echo "</tr>";
			} while($m = F_aiocpdb_fetch_array($r));
			echo"</table>";
			echo "</td>";
			echo "</tr>";
			echo "</table>";
			
			// ---------------------------------------------------------------
			// -- page jumper (menu for successive pages)
			$sql = "SELECT count(*) AS total FROM ".K_TABLE_USERS_COMPANY." ".$wherequery."";
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
