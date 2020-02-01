<?php
//============================================================+
// File name   : cp_functions_downloads.php
// Begin       : 2001-11-20
// Last Update : 2008-01-01
// 
// Description : Functions for downloads
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
// Display downloads
// $viewmode: 0=compact(headers only); 1=full 
// $selecteddownload = download to display in full mode while in compact mode
// ------------------------------------------------------------
function F_show_downloads($download_category, $viewmode, $selecteddownload, $downloaded, $wherequery, $order_field, $orderdir, $firstrow, $rowsperpage) {
	global $l, $db, $selected_language;
	global $term, $submitted, $downloadssearch, $addterms;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_page.'.CP_EXT);
	require_once('../../shared/code/cp_functions_dynamic_pages.'.CP_EXT);
	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	
	//initialize variables
	$userlevel = $_SESSION['session_user_level'];
	
	if(!$firstrow) {$firstrow="0";}
	else {$firstrow=intval($firstrow);} 
	if(!$rowsperpage) {$rowsperpage = K_MAX_ROWS_PER_PAGE;}
	else {$rowsperpage=intval($rowsperpage);}
	
	if(!$order_field) {$order_field = "download_name";}
	else {$order_field = addslashes(preg_replace("/[\s]+/i", " ", $order_field));}
	if(!$orderdir) {$orderdir=0; $nextorderdir=1; $full_order_field = $order_field;}
	else {$orderdir=1; $nextorderdir=0; $full_order_field = $order_field." DESC";}
	$full_order_field = urldecode($full_order_field);
	
	if(!F_count_rows(K_TABLE_DOWNLOADS)) { //if the table is void (no items) display message
		echo "<h2>".$l['m_databasempty']."</h2>";
		return FALSE;
	}
	
	echo "<table class=\"edge\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">";
	// --- ------------------------------------------------------
	
	if (isset($download_category) AND (strlen($download_category)>0) AND ($download_category==0)) { //select all categories
		$wherequery = "WHERE 1";
	}
		
	if( (!$download_category) AND (!$wherequery) ) { // select category
		$sql = "SELECT * FROM ".K_TABLE_DOWNLOADS_CATEGORIES." ORDER BY downcat_name LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$download_category = $m['downcat_id'];
			}
		}
		else {
			F_display_db_error();
		}
	}
	
	if($download_category) {
		$download_category = intval($download_category);
		$sqlc = "SELECT * FROM ".K_TABLE_DOWNLOADS_CATEGORIES." WHERE downcat_id=".$download_category."";
		if($rc = F_aiocpdb_query($sqlc, $db)) {
			if($mc = F_aiocpdb_fetch_array($rc)) {
				if($mc['downcat_level'] > $userlevel) {
					echo "<tr><td></td></tr></table>\n";
					F_print_error("WARNING", $l['m_authorization_deny']);
					//F_logout_form();
					return;
				}
				$thisname = F_decode_field($mc['downcat_name']);
				$thisdesc = F_decode_field($mc['downcat_description']);
				
				echo "<tr class=\"edge\">";
				echo "<th class=\"edge\" align=\"left\">";
				echo "".htmlentities($thisname, ENT_NOQUOTES, $l['a_meta_charset']).":<br />\n";
				echo F_evaluate_modules($thisdesc);
				echo "</th></tr>";
				
				if (!$wherequery) {$wherequery = "WHERE (download_category='".$download_category."')";}
				else {$wherequery .= " AND (download_category='".$download_category."')";}
			} else {
				F_display_db_error();
			}
		} else {
			F_display_db_error();
		}
	}
	
	if (!$wherequery) {
		$sql = "SELECT * FROM ".K_TABLE_DOWNLOADS." ORDER BY ".$full_order_field." LIMIT ".$firstrow.",".$rowsperpage."";
	}
	else {
		$sql = "SELECT * FROM ".K_TABLE_DOWNLOADS." ".$wherequery." ORDER BY ".$full_order_field." LIMIT ".$firstrow.",".$rowsperpage."";
	}
	
	if($r = F_aiocpdb_query($sql, $db)) {
		echo "<tr class=\"edge\">";
		echo "<td class=\"edge\">";
		echo "<table class=\"fill\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\" style=\"width:100%\">";
		while($m = F_aiocpdb_fetch_array($r)) {
			
			//change style for each row
			if (isset($rowodd) AND ($rowodd)) {
				$rowclass = "O";
				$rowodd = 0;
			} else {
				$rowclass = "E";
				$rowodd = 1;
			}
			
			//get category data
			if (!$download_category) {$catdata = F_get_download_category_data($m['download_category']);}
			//check authorization rights
			if (($download_category) OR ($userlevel >= $catdata->level)) {
				
				if(($viewmode)OR($m['download_id'] == $selecteddownload)) { //full mode
					
					//check if is linked externally (not default directory)
					if(F_is_relative_link($m['download_link'])) {
						$dfilepath = K_PATH_FILES_DOWNLOAD.$m['download_link'];
					}
					else {
						$dfilepath = $m['download_link'];
					}
					
					echo "<tr class=\"fill".$rowclass."\"><td class=\"fill".$rowclass."E\">";
					
					//send file to user and update stats -----------------------------------
					if($downloaded) { 
						$sqldl = "UPDATE IGNORE ".K_TABLE_DOWNLOADS." SET download_downloads=download_downloads+1 WHERE download_id=".$selecteddownload."";
						if(!$rdl = F_aiocpdb_query($sqldl, $db)) {
							F_display_db_error();
						}
						else {
							$m['download_downloads']+=1;
						}
						
						//create verification code to avoid improper use of cp_download.php file
						$verifycode = F_generate_verification_code($dfilepath, 4);
						echo "<script language=\"JavaScript\" type=\"text/javascript\">";
						echo "//<![CDATA[\n";
						echo "dw=window.open('../../shared/code/cp_download.".CP_EXT."?c=".$verifycode."&d=4&f=".urlencode($dfilepath)."', 'dw', 'dependent,height=1,width=1,menubar=no,resizable=no,scrollbars=no,status=no,toolbar=no');\n";
						// automatically close download popup after 20 secs
						echo "setInterval('dw.close()', 20000);\n";
						echo "//]]>\n";
						echo "</script>\n";	
						echo "<a href=\"../../shared/code/cp_download.".CP_EXT."?c=".$verifycode."&d=4&f=".urlencode($dfilepath)."\" target=\"_blank\" title=\"".$l['w_download']." - new window\"><strong>&nbsp;&nbsp;&nbsp;&gt;&gt;&gt; ".$l['w_download'].": ".basename($dfilepath)."</strong></a><br />";
					}
					//END send file to user and update stats -----------------------------------
					
					echo "<table class=\"edge\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\" style=\"width:100%\">";
					echo "<tr class=\"edge\">";
					echo "<th class=\"edge\" align=\"left\">";
					//display download button
					F_generic_button("download",$l['w_download'],"FJ_submit_downloads_form('".$firstrow."','".urlencode($order_field)."','".$orderdir."',".$m['download_category'].",".$m['download_id'].",1)");
					echo "&nbsp;&nbsp;";
					
					echo "<a class=\"edge\" href=\"javascript:FJ_submit_downloads_form('".$firstrow."','".urlencode($order_field)."','".$orderdir."',".$m['download_category'].",".$m['download_id'].",1);\">".htmlentities($m['download_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</a>";
					
					// noscript alternative for search engines
					echo "<noscript><a href=\"cp_downloads.".CP_EXT."?did=".$m['download_id']."\">".htmlentities($m['download_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</a></noscript>";
					
					echo "</th></tr>\n";
					echo "<tr class=\"edge\">";
					echo "<td class=\"edge\">";
					echo "<table class=\"fill\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\" style=\"width:100%\">";
					
					echo "<tr class=\"fillE\"><td class=\"fillEO\"><b>".$l['w_date']."</b></td><td class=\"fillEE\">".$m['download_date']."&nbsp;</td></tr>";
					
					echo "<tr class=\"fillO\"><td class=\"fillOO\"><b>".$l['w_size']."</b></td><td class=\"fillOE\">".F_format_iec_byte_size($m['download_size'])."&nbsp;</td></tr>";
					
					echo "<tr class=\"fillE\"><td class=\"fillEO\"><b>".$l['w_publisher']."</b></td><td class=\"fillEE\"><a class=\"downloads\"  href=\"".htmlentities(urldecode($m['download_publisher_link']))."\" target=\"_blank\">".htmlentities($m['download_publisher_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</a>&nbsp;</td></tr>";
					
					$sqlos = "SELECT * FROM ".K_TABLE_SOFTWARE_OS." WHERE os_id=".$m['download_os']."";
					if($ros = F_aiocpdb_query($sqlos, $db)) {
						if($mos = F_aiocpdb_fetch_array($ros)) {
							echo "<tr class=\"fillO\"><td class=\"fillOO\"><b>".$l['w_os']."</b></td><td class=\"fillOE\">";
							if (isset($mos['os_link']) and (strlen($mos['os_link']) > 0)) {
								echo "<a href=\"".$mos['os_link']."\" target=\"_blank\">".htmlentities($mos['os_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</a>";
							}else {
								echo "".htmlentities($mos['os_name'], ENT_NOQUOTES, $l['a_meta_charset'])."";
							}
							echo "&nbsp;</td></tr>";
						}
					}
					else {
						F_display_db_error();
					}
					
					$sqllic = "SELECT * FROM ".K_TABLE_SOFTWARE_LICENSES." WHERE license_id=".$m['download_license']."";
					if($rlic = F_aiocpdb_query($sqllic, $db)) {
						if($mlic = F_aiocpdb_fetch_array($rlic)) {
							echo "<tr class=\"fillE\"><td class=\"fillEO\"><b>".$l['w_license']."</b></td><td class=\"fillEE\">";
							if (isset($mlic['license_link']) and (strlen($mlic['license_link']) > 0)) {
								echo "<a href=\"".$mlic['license_link']."\" target=\"_blank\">".htmlentities($mlic['license_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</a>";
							}else {
								echo "".htmlentities($mlic['license_name'], ENT_NOQUOTES, $l['a_meta_charset'])."";
							}
							echo "&nbsp;</td></tr>";
						}
					}
					else {
						F_display_db_error();
					}
					
					$thisldesc = F_decode_field($m['download_description_large']);
					echo "<tr class=\"fillO\"><td class=\"fillOO\" valign=\"top\"><b>".$l['w_description']."</b></td><td class=\"fillOE\">".F_evaluate_modules($thisldesc)."&nbsp;</td></tr>";
					
					$thislimit = F_decode_field($m['download_limitations']);
					echo "<tr class=\"fillE\"><td class=\"fillEO\" valign=\"top\"><b>".$l['w_limitations']."</b></td><td class=\"fillEE\">".F_evaluate_modules($thislimit)."&nbsp;</td></tr>";
					
					echo "<tr class=\"fillO\"><td class=\"fillOO\"><b>".$l['w_minimum_requirements']."</b></td><td class=\"fillOE\">".F_evaluate_modules($m['download_requisite'])."&nbsp;</td></tr>";
					
					echo "<tr class=\"fillE\"><td class=\"fillEO\"><b>".$l['w_downloads']."</b></td><td class=\"fillEE\">".$m['download_downloads']."&nbsp;</td></tr>";
					
					echo "</table>";
					echo "</td></tr></table>";
					echo "</td></tr>";
				}
				else { //compact mode --------------------------------------------------------------
					echo "<tr class=\"fill".$rowclass."\"><td class=\"fill".$rowclass."E\">";
					
					echo "<a href=\"javascript:FJ_submit_downloads_form('".$firstrow."','".urlencode($order_field)."','0',".$m['download_category'].",".$m['download_id'].",0);\"><b>".$m['download_name']."</b></a>";
					
					// noscript alternative for search engines
					// noscript alternative for search engines
					echo "<noscript><a href=\"cp_downloads.".CP_EXT."?did=".$m['download_id']."\">".htmlentities($m['download_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</a></noscript>";
					
					$thisldesc = F_decode_field($m['download_description_small']);
					echo " | ".$thisldesc."";
					echo " | <b>".$l['w_date']."</b>: ".$m['download_date']."";
					echo " | <b>".$l['w_size']."</b>: ".F_format_iec_byte_size($m['download_size'])."";
					$sqlos = "SELECT * FROM ".K_TABLE_SOFTWARE_OS." WHERE os_id=".$m['download_os']."";
					if($ros = F_aiocpdb_query($sqlos, $db)) {
						if($mos = F_aiocpdb_fetch_array($ros)) {
							echo " | <b>".$l['w_os_short']."</b>: ".htmlentities($mos['os_name'], ENT_NOQUOTES, $l['a_meta_charset'])."";
						}
					}
					else {
						F_display_db_error();
					}
					$sqllic = "SELECT * FROM ".K_TABLE_SOFTWARE_LICENSES." WHERE license_id=".$m['download_license']."";
					if($rlic = F_aiocpdb_query($sqllic, $db)) {
						if($mlic = F_aiocpdb_fetch_array($rlic)) {
							echo " | <b>".$l['w_license']."</b>: ".htmlentities($mlic['license_name'], ENT_NOQUOTES, $l['a_meta_charset'])."";
						}
					}
					else {
						F_display_db_error();
					}
					echo " | <b>".$l['w_downloads']."</b>: ".$m['download_downloads']."";
					
					echo "</td></tr>";
				}
			}
		} //end of while
		echo "<tr><td></td></tr>";
		echo "</table>";
		echo "</td></tr>";
	}
	else {
		F_display_db_error();
	}
	echo "</table>";
	// --- ------------------------------------------------------
	// --- page jump
	$sql = "SELECT count(*) AS total FROM ".K_TABLE_DOWNLOADS." ".$wherequery."";
	if (!empty($order_field)) {$param_array = "&amp;order_field=".urlencode($order_field)."";}
	if (!empty($orderdir)) {$param_array .= "&amp;orderdir=".$orderdir."";}
	$param_array .= "&amp;submitted=1";
	if (!empty($download_category)) {$param_array .= "&amp;download_category=".$download_category."";}
	if (!empty($viewmode)) {$param_array .= "&amp;viewmode=".$viewmode."";}	
	if (!empty($term)) {$param_array .= "&amp;term=".urlencode($term)."";}
	if (!empty($addterms)) {$param_array .= "&amp;addterms=".$addterms."";}
	if (!empty($downloadssearch)) {$param_array .= "&amp;downloadssearch=".$downloadssearch."";}
	F_show_page_navigator($_SERVER['SCRIPT_NAME'], $aiocp_dp, $sql, $firstrow, $rowsperpage, $param_array);
	
return;
}

// ------------------------------------------------------------
// Show select form for downloads
// ------------------------------------------------------------
function F_show_select_downloads($wherequery, $order_field, $orderdir, $firstrow, $rowsperpage) {
	global $l, $db, $selected_language, $aiocp_dp;
	global $download_category, $viewmode, $selecteddownload, $downloaded;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	require_once('../../shared/code/cp_functions_tree.'.CP_EXT);

	if(!isset($selecteddownload)) {$selecteddownload=0;}
else {$selecteddownload=intval($selecteddownload);}
	if(!isset($viewmode)) {$viewmode=0;}
else {$viewmode=intval($viewmode);}

	$userlevel = $_SESSION['session_user_level'];
	
	// Initialize variables
	if(!$download_category) {
		$sql = "SELECT * FROM ".K_TABLE_DOWNLOADS_CATEGORIES." WHERE downcat_level<=".$userlevel." ORDER BY downcat_sub_id,downcat_position LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$download_category = $m['downcat_id'];
			}
		}
		else {
			F_display_db_error();
		}
	}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_downloadsshow" id="form_downloadsshow">

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT category ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_category', 'h_downloadcat_select'); ?></b></td>
<td class="fillOE">
<select name="download_category" id="download_category" size="0" onchange="document.form_downloadsshow.firstrow.value=0;document.form_downloadsshow.submit()">
<?php 
$noscriptlink = $_SERVER['SCRIPT_NAME']."?";
if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {
	$noscriptlink .= "aiocp_dp=".$aiocp_dp."&amp;";
}
if (!empty($viewmode)) {$noscriptlink .= "viewmode=".$viewmode."&amp;";}
if (!empty($order_field)) {$noscriptlink .= "order_field=".urlencode($order_field)."&amp;";}
if (!empty($orderdir)) {$noscriptlink .= "orderdir=".$orderdir."&amp;";}
$noscriptlink .= "download_category=";
F_form_select_tree($download_category, false, K_TABLE_DOWNLOADS_CATEGORIES, "downcat", $noscriptlink); ?>
</td>
</tr>
<!-- END SELECT category ==================== -->

<!-- SELECT view mode ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_mode', 'h_list_mode'); ?></b></td>
<td class="fillEE">
<select name="viewmode" id="viewmode" size="0" onchange="document.form_downloadsshow.downloaded.value=''; document.form_downloadsshow.submit()">
<?php
if(!$viewmode) {
echo "<option value=\"0\" selected=\"selected\">".$l['w_compact']."</option>";
echo "<option value=\"1\">".$l['w_full']."</option>";
}
else {
echo "<option value=\"0\">".$l['w_compact']."</option>";
echo "<option value=\"1\" selected=\"selected\">".$l['w_full']."</option>";
}
?> 
</select>
<noscript>
<ul>
<?php
$noscriptlink = $_SERVER['SCRIPT_NAME']."?";
if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {
	$noscriptlink .= "aiocp_dp=".$aiocp_dp."&amp;";
}
echo "<li><a href=\"".$noscriptlink."viewmode=0\">".$l['w_compact']."</a></li>\n";
echo "<li><a href=\"".$noscriptlink."viewmode=1\">".$l['w_full']."</a></li>\n";
?>
</ul>
</noscript>
</td>
</tr>
<!-- END view mode ==================== -->

<!-- SELECT ORDER mode ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_order_by', 'h_list_order_by'); ?></b></td>
<td class="fillOE">
<select name="order_field" id="order_field" size="0" onchange="document.form_downloadsshow.submit()">
<?php
echo "<option value=\"download_name\"";
if($order_field=="download_name") {echo" selected=\"selected\"";}
echo">".$l['w_name']."</option>";
echo "<option value=\"download_date DESC\"";
if($order_field=="download_date DESC") {echo" selected=\"selected\"";}
echo">".$l['w_date']."</option>";
echo "<option value=\"download_size\"";
if($order_field=="download_size") {echo" selected=\"selected\"";}
echo">".$l['w_size']."</option>";
echo "<option value=\"download_os\"";
if($order_field=="download_os") {echo" selected=\"selected\"";}
echo">".$l['w_os_short']."</option>";
echo "<option value=\"download_license\"";
if($order_field=="download_license") {echo" selected=\"selected\"";}
echo">".$l['w_license']."</option>";
echo "<option value=\"download_downloads DESC\"";
if($order_field=="download_downloads DESC") {echo" selected=\"selected\"";}
echo">".$l['w_downloads']."</option>";
?> 
</select>
</td>
</tr>
<!-- END ORDER mode ==================== -->

</table>

</td>
</tr>
</table>
<input type="hidden" name="downloaded" id="downloaded" value="0" />
<input type="hidden" name="selecteddownload" id="selecteddownload" value="<?php echo $selecteddownload; ?>" />
<input type="hidden" name="orderdir" id="orderdir" value="<?php echo $orderdir; ?>" />
<input type="hidden" name="firstrow" id="firstrow" value="<?php echo $firstrow; ?>" />
<input type="hidden" name="submitted" id="submitted" value="0" />
<br />
<!-- SHOW downloads ==================== -->
<?php 
if ($download_category) {
	F_show_downloads($download_category, $viewmode, $selecteddownload, $downloaded, $wherequery, $order_field, $orderdir, $firstrow, K_MAX_ROWS_PER_PAGE);
}
?>
<!-- END SHOW downloads ==================== -->
</form>
<!-- ====================================================== -->

<!-- Submit form  ==================== -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
function FJ_submit_downloads_form(newfirstrow, neworder_field, neworderdir, downloadscategory, downloadselected, downloadnow) {
	document.form_downloadsshow.download_category.value=downloadscategory;
	document.form_downloadsshow.selecteddownload.value=downloadselected;
	document.form_downloadsshow.downloaded.value=downloadnow;
	document.form_downloadsshow.order_field.value=neworder_field;
	document.form_downloadsshow.orderdir.value=neworderdir;
	document.form_downloadsshow.firstrow.value=newfirstrow;
	document.form_downloadsshow.submitted.value=1;
	document.form_downloadsshow.submit();
}

document.form_downloadsshow.download_category.focus();
//]]>
</script>
<!-- END Cange focus to download_id select -->

<?php
} //end of function

// ------------------------------------------------------------
// Show select form for downloads
// ------------------------------------------------------------
function F_search_downloads($wherequery, $order_field, $orderdir, $firstrow, $rowsperpage) {
	global $l, $db, $selected_language;
	global $download_category, $viewmode, $selecteddownload, $downloaded;
	global $term, $submitted, $downloadssearch, $aiocp_dp, $addterms;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	require_once('../../shared/code/cp_functions_tree.'.CP_EXT);
	
	if(!$firstrow) {$firstrow="0";}
	else {$firstrow=intval($firstrow);}
	if(!$rowsperpage) {$rowsperpage = K_MAX_ROWS_PER_PAGE;}
	else {$rowsperpage=intval($rowsperpage);}
	if(!isset($orderdir)) {$orderdir=1;} 
	else {$orderdir=intval($orderdir);}
	if(!$download_category) {$download_category="0";} // All categories
	if(!isset($selecteddownload)) {$selecteddownload=0;}
else {$selecteddownload=intval($selecteddownload);}
	if(!isset($viewmode)) {$viewmode=0;}
else {$viewmode=intval($viewmode);}
	
if(!F_count_rows(K_TABLE_DOWNLOADS)) { //if the table is void (no items) display message
	echo "<h2>".$l['m_databasempty']."</h2>";
}
else { //the table is not empty

// ---------------------------------------------------------------

if($downloadssearch OR $submitted) { // Submitting query (search results)
	if(isset($term) AND ($term != "")) {
		$wherequery = "WHERE (";
		$terms = preg_split("/[\s]+/i", addslashes($term)); // Get all the words into an array
		$size = sizeof($terms);
		//redundant check for security feature
		if($addterms != "AND") {$addterms = "OR";}
		$wherequery .= "(";
		for($i=0; $i<$size; $i++) {
			if($i>0) {$wherequery .= " ".$addterms." ";}
			$wherequery .= "((download_name LIKE '%$terms[$i]%')";
			$wherequery .= " OR (download_link LIKE '%$terms[$i]%')";
			$wherequery .= " OR (download_publisher_name LIKE '%$terms[$i]%')";
			$wherequery .= " OR (download_description_small LIKE '%$terms[$i]%')";
			$wherequery .= " OR (download_description_large LIKE '%$terms[$i]%'))";
		}
		$wherequery .= ")";
		if($download_category) {$wherequery .= " AND (download_category=".$download_category.")";}
		$wherequery .= ")"; // close WHERE clause
	}
	F_show_downloads($download_category, $viewmode, $selecteddownload, $downloaded, $wherequery, $order_field, $orderdir, $firstrow, $rowsperpage);
} //end if($downloadssearch OR $submitted)

// ---------------------------------------------------------------
?>

<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_downloadssearch" id="form_downloadssearch">

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<tr class="fillO">
<td class="fillOO" align="right">
<b><?php echo F_display_field_name('w_keywords', 'h_search_keywords'); ?></b>
</td>
<td class="fillOE">
<input type="text" name="term" id="term" value="<?php echo htmlentities($term, ENT_COMPAT, $l['a_meta_charset']); ?>" />
</td></tr>

<?php
if($addterms == "OR") {
echo "<tr class=\"fillE\"><td class=\"fillEO\">&nbsp;</td><td class=\"fillEE\"><input type=\"radio\" name=\"addterms\" value=\"AND\" /> ".$l['d_search_all']."</td></tr>";
echo "<tr class=\"fillO\"><td class=\"fillOO\">&nbsp;</td><td class=\"fillOE\"><input type=\"radio\" name=\"addterms\" value=\"OR\" checked=\"checked\" /> ".$l['d_search_any']."</td></tr>";
}
else {
echo "<tr class=\"fillE\"><td class=\"fillEO\">&nbsp;</td><td class=\"fillEE\"><input type=\"radio\" name=\"addterms\" value=\"AND\" checked=\"checked\" /> ".$l['d_search_all']."</td></tr>";
echo "<tr class=\"fillO\"><td class=\"fillOO\">&nbsp;</td><td class=\"fillOE\"><input type=\"radio\" name=\"addterms\" value=\"OR\" /> ".$l['d_search_any']."</td></tr>";
}
?>

<!-- SELECT category ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_category', ''); ?></b></td>
<td class="fillEE">
<select name="download_category" id="download_category" size="0">
<?php 
if(!$download_category) {echo "<option value=\"0\" selected=\"selected\">".$l['d_all_categories']."</option>";}
else {echo "<option value=\"0\">".$l['d_all_categories']."</option>";}

$noscriptlink = $_SERVER['SCRIPT_NAME']."?";
if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {
	$noscriptlink .= "aiocp_dp=".$aiocp_dp."&amp;";
}
if (!empty($viewmode)) {$noscriptlink .= "viewmode=".$viewmode."&amp;";}
if (!empty($order_field)) {$noscriptlink .= "order_field=".urlencode($order_field)."&amp;";}
if (!empty($orderdir)) {$noscriptlink .= "orderdir=".$orderdir."&amp;";}
$noscriptlink .= "download_category=";
F_form_select_tree($download_category, false, K_TABLE_DOWNLOADS_CATEGORIES, "downcat", $noscriptlink); ?>
</td>
</tr>
<!-- END SELECT category ==================== -->

<!-- SELECT ORDER mode ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_order_by', ''); ?></b></td>
<td class="fillOE">
<select name="order_field" id="order_field" size="0">
<?php
echo "<option value=\"download_name\"";
if($order_field=="download_name") {echo" selected=\"selected\"";}
echo">".$l['w_name']."</option>";
echo "<option value=\"download_date DESC\"";
if($order_field=="download_date DESC") {echo" selected=\"selected\"";}
echo">".$l['w_date']."</option>";
echo "<option value=\"download_size\"";
if($order_field=="download_size") {echo" selected=\"selected\"";}
echo">".$l['w_size']."</option>";
echo "<option value=\"download_os\"";
if($order_field=="download_os") {echo" selected=\"selected\"";}
echo">".$l['w_os_short']."</option>";
echo "<option value=\"download_license\"";
if($order_field=="download_license") {echo" selected=\"selected\"";}
echo">".$l['w_license']."</option>";
echo "<option value=\"download_downloads DESC\"";
if($order_field=="download_downloads DESC") {echo" selected=\"selected\"";}
echo">".$l['w_downloads']."</option>";
?> 
</select>
</td>
</tr>
<!-- END ORDER mode ==================== -->
</table>

</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="downloaded" id="downloaded" />
<input type="hidden" name="selecteddownload" id="selecteddownload" value="<?php echo $selecteddownload; ?>" />
<input type="hidden" name="downloadssearch" id="downloadssearch" value="" />
<input type="hidden" name="orderdir" id="orderdir" value="<?php echo $orderdir; ?>" />
<input type="hidden" name="firstrow" id="firstrow" value="<?php echo $firstrow; ?>" />
<input type="hidden" name="submitted" id="submitted" value="0" />
<?php F_submit_button("form_downloadssearch","downloadssearch",$l['w_search']); ?>
</td></tr>
</table>
</form>

<?php
// ---------------------------------------------------------------
} //end of else for void table
?>

<!-- ====================================================== -->

<!-- Submit form  ==================== -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
function FJ_submit_downloads_form(newfirstrow, neworder_field, neworderdir, downloadscategory, downloadselected, downloadnow) {
	document.form_downloadssearch.download_category.value=downloadscategory;
	document.form_downloadssearch.selecteddownload.value=downloadselected;
	document.form_downloadssearch.downloaded.value=downloadnow;
	document.form_downloadssearch.order_field.value=neworder_field;
	document.form_downloadssearch.orderdir.value=neworderdir;
	document.form_downloadssearch.firstrow.value=newfirstrow;
	document.form_downloadssearch.submitted.value=1;
	document.form_downloadssearch.submit();
}
//]]>
</script>
<!-- END Submit form ==================== -->

<?php
} //end of function

// ------------------------------------------------------------
// return filesize rounded to 2 decimals, and
// display size using IEC prefixes
// see: http://www.technick.net/guide_tables_prefix.php
// ------------------------------------------------------------
function F_format_iec_byte_size($size) {
	$exp = 0; 
	while($size >= pow(1024,($exp+1))) {$exp++;}
	$ext = array("B","KiB","MiB","GiB","TiB","PiB","EiB","ZiB","YiB");
	$newsize = round($size/pow(1024,$exp),2); 
	$newsize .= $ext[$exp]; //attach IEC prefix
	return ($newsize);
} //end of function

// ----------------------------------------------------------
// read category data
// ----------------------------------------------------------
function F_get_download_category_data($categoryid) {
	global $db, $l;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	
	$sql = "SELECT * FROM ".K_TABLE_DOWNLOADS_CATEGORIES." WHERE downcat_id='".$categoryid."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$downcat->level = $m['downcat_level'];
			$downcat->name = $m['downcat_name'];
			$downcat->description = $m['downcat_description'];
			return $downcat;
		}
	}
	else {
		F_display_db_error();
	}
	return FALSE;
}


// ------------------------------------------------------------
// Display single news
// ------------------------------------------------------------
function F_display_single_download($did) {
	global $l, $db, $selected_language, $aiocp_dp;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	if(!F_count_rows(K_TABLE_NEWS)) { //if the table is void (no items) display message
		echo "<h2>".$l['m_databasempty']."</h2>";
	}
	else { //the table is not empty
		$wherequery = "WHERE download_id='".$did."'";
		F_show_fixed_downloads("", 1, $did, 0, $wherequery, "", "", 0, K_MAX_ROWS_PER_PAGE);
	} 
}

// ------------------------------------------------------------
// Show downloads without selector
// ------------------------------------------------------------
function F_show_fixed_downloads($download_category, $viewmode, $selecteddownload, $downloaded, $wherequery, $order_field, $orderdir, $firstrow, $rowsperpage) {
	global $l, $db, $selected_language, $aiocp_dp;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
?>

<!-- ====================================================== -->
<form action="<?php echo "cp_downloads.".CP_EXT.""; ?>" method="post" enctype="multipart/form-data" name="form_downloadsshow" id="form_downloadsshow">

<input type="hidden" name="download_category" id="download_category" value="<?php echo $download_category; ?>" />
<input type="hidden" name="viewmode" id="viewmode" value="<?php echo $viewmode; ?>" />
<input type="hidden" name="order_field" id="order_field" value="<?php echo $order_field; ?>" />
<input type="hidden" name="downloaded" id="downloaded" value="0" />
<input type="hidden" name="selecteddownload" id="selecteddownload" value="<?php echo $selecteddownload; ?>" />
<input type="hidden" name="orderdir" id="orderdir" value="<?php echo $orderdir; ?>" />
<input type="hidden" name="firstrow" id="firstrow" value="<?php echo $firstrow; ?>" />
<input type="hidden" name="submitted" id="submitted" value="0" />

<!-- SHOW downloads ==================== -->
<?php 
F_show_downloads($download_category, $viewmode, $selecteddownload, $downloaded, $wherequery, $order_field, $orderdir, $firstrow, $rowsperpage);
?>
<!-- END SHOW downloads ==================== -->

</form>
<!-- ====================================================== -->

<!-- Submit form  ==================== -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
function FJ_submit_downloads_form(newfirstrow, neworder_field, neworderdir, downloadscategory, downloadselected, downloadnow) {
	document.form_downloadsshow.download_category.value=downloadscategory;
	document.form_downloadsshow.selecteddownload.value=downloadselected;
	document.form_downloadsshow.downloaded.value=downloadnow;
	document.form_downloadsshow.order_field.value=neworder_field;
	document.form_downloadsshow.orderdir.value=neworderdir;
	document.form_downloadsshow.firstrow.value=newfirstrow;
	document.form_downloadsshow.submitted.value=1;
	document.form_downloadsshow.submit();
}
//]]>
</script>
<!-- END Cange focus to download_id select -->

<?php
} //end of function
//============================================================+
// END OF FILE                                                 
//============================================================+
?>
