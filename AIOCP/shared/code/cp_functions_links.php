<?php
//============================================================+
// File name   : cp_functions_links.php
// Begin       : 2001-09-21
// Last Update : 2008-02-25
// 
// Description : Functions for links
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
// Display links
// ------------------------------------------------------------
function F_show_links($links_category, $wherequery, $order_field, $orderdir, $firstrow, $rowsperpage) {
	global $l, $db, $selected_language;
	global $selectedletter;
	global $term, $selectedlinks, $submitted, $linkssearch, $addterms;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_page.'.CP_EXT);
	require_once('../../shared/code/cp_functions_dynamic_pages.'.CP_EXT);
	
	//initialize variable
	if(!$firstrow) {$firstrow="0";}
	else {$firstrow=intval($firstrow);} 
	if(!$rowsperpage) {$rowsperpage = K_MAX_ROWS_PER_PAGE;}
	else {$rowsperpage=intval($rowsperpage);}
	if(!isset($selectedletter)) {$selectedletter = '';}
	//else {$selectedletter=htmlentities($selectedletter);}
	
	if(!$order_field) {$order_field = "links_name";}
	else {$order_field = addslashes(preg_replace("/[\s]+/i", " ", $order_field));}
	if(!$orderdir) {$orderdir=0; $nextorderdir=1; $full_order_field = $order_field;}
	else {$orderdir=1; $nextorderdir=0; $full_order_field = $order_field." DESC";}
	$full_order_field = urldecode($full_order_field);
	
	if(!F_count_rows(K_TABLE_LINKS)) { //if the table is void (no items) display message
		echo "<h2>".$l['m_databasempty']."</h2>";
		return FALSE;
	}
	
	echo "<table class=\"edge\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">";
	
	// --- ------------------------------------------------------
	if( (!$links_category) AND (!$wherequery) ) {
		$sql = "SELECT * FROM ".K_TABLE_LINKS_CATEGORIES." ORDER BY linkscat_name LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$links_category = $m['linkscat_id'];
			}
		}
		else {
			F_display_db_error();
		}
	}
	
	if($links_category) {
		$links_category = intval($links_category);
		$sqlc = "SELECT * FROM ".K_TABLE_LINKS_CATEGORIES." WHERE linkscat_id=".$links_category."";
		if($rc = F_aiocpdb_query($sqlc, $db)) {
			if($mc = F_aiocpdb_fetch_array($rc)) {
				$currenttarget = $mc['linkscat_target'];
				$thisname = F_decode_field($mc['linkscat_name']);
				$thisdesc = F_decode_field($mc['linkscat_description']);
				
				echo "<tr class=\"edge\">";
				echo "<th class=\"edge\" align=\"left\">";
				echo "<b>".htmlentities($thisname, ENT_NOQUOTES, $l['a_meta_charset'])."</b><br />\n";
				echo F_evaluate_modules($thisdesc);
				echo "</th></tr>";
				
				if (!$wherequery) {$wherequery = "WHERE (links_category='".$links_category."')";}
				else {$wherequery .= " AND (links_category='".$links_category."')";}
			}
		}
		else {
			F_display_db_error();
		}
	}
	
	if (!$wherequery) {$wherequery = "WHERE (links_name LIKE '".$selectedletter."%')";}
	else {$wherequery .= " AND (links_name LIKE '".$selectedletter."%')";}
	
	$sql = "SELECT * FROM ".K_TABLE_LINKS." ".$wherequery." ORDER BY ".$full_order_field." LIMIT ".$firstrow.",".$rowsperpage."";

	
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
			
			echo "<tr class=\"fill".$rowclass."\">";
			echo "<td class=\"fill".$rowclass."O\">";
			echo "<a href=\"".htmlentities(urldecode($m['links_link']))."\" target=\"_blank\">";
			echo "<b>".htmlentities($m['links_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</b>";
			echo "</a>";
			echo "<br />";
			$thisldesc = F_decode_field($m['links_description']);
			echo htmlentities(F_evaluate_modules($thisldesc), ENT_NOQUOTES, $l['a_meta_charset']);
			echo "<br />";
			echo "<a href=\"".htmlentities(urldecode($m['links_link']))."\" target=\"_blank\">";
			echo "".$m['links_link']."";
			echo "</a>";
			echo "</td>";
			echo "</tr>";
		} //end of while
		echo "</table>";
		echo "</td></tr>";
	}
	else {
		F_display_db_error();
	}
	echo "</table>";
		
	// Display RSS icon Link
	//$url_request = "wherequery=".urlencode($wherequery)."";
	$url_request = "x=x";
	if ($links_category) {
		$url_request .= "&amp;links_category=".$links_category."";
	}
	if ($order_field) {
		$url_request .= "&amp;order_field=".urlencode($order_field)."";
	}
	if ($orderdir) {
		$url_request .= "&amp;orderdir=".$orderdir."";
	}
	
	echo "<br /><div align=\"center\"><a href=\"../../public/code/cp_links_rss.".CP_EXT."?".$url_request."\" target=\"_blank\"><img src=\"../../pagefiles/rss.gif\" width=\"36\" height=\"14\" alt=\"RSS (XML RDF)\" border=\"0\" /></a></div><br />";
	
	// --- ------------------------------------------------------
	// --- page jump
	$sql = "SELECT count(*) AS total FROM ".K_TABLE_LINKS." ".$wherequery."";
	
	if (!empty($order_field)) {$param_array = "&amp;order_field=".urlencode($order_field)."";}
	if (!empty($orderdir)) {$param_array .= "&amp;orderdir=".$orderdir."";}
	$param_array .= "&amp;submitted=1";
	if (!empty($links_category)) {$param_array .= "&amp;links_category=".$links_category."";}
	if (!empty($selectedlinks)) {$param_array .= "&amp;selectedlinks=".$selectedlinks."";}
	if (!empty($linkssearch)) {$param_array .= "&amp;linkssearch=".$linkssearch."";}
	if (!empty($selectedletter)) {$param_array .= "&amp;selectedletter=".$selectedletter."";}
	if (!empty($term)) {$param_array .= "&amp;term=".urlencode($term)."";}
	if (!empty($addterms)) {$param_array .= "&amp;addterms=".$addterms."";}
	F_show_page_navigator($_SERVER['SCRIPT_NAME'], $aiocp_dp, $sql, $firstrow, $rowsperpage, $param_array);
	
	return;
}

// ------------------------------------------------------------
// Display links in RSS 1.0 format
// ------------------------------------------------------------
function F_show_links_RSS($links_category, $wherequery, $order_field, $orderdir) {
	global $l, $db, $selected_language;
	global $selectedletter;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_page.'.CP_EXT);

	$RSS_HEADER = "";
	$RSS_ITEMS = "";
	
	if(!$order_field) {$order_field = "links_name";}
	else {$order_field = addslashes(preg_replace("/[\s]+/i", " ", $order_field));}
	if(!$orderdir) {$orderdir=0; $nextorderdir=1; $full_order_field = $order_field;}
	else {$orderdir=1; $nextorderdir=0; $full_order_field = $order_field." DESC";}
	$full_order_field = urldecode($full_order_field);
	if(!isset($selectedletter)) {$selectedletter = '';}
	
	if(!F_count_rows(K_TABLE_LINKS)) { //if the table is void (no items) display message
		return FALSE;
	}
	
	//send page header
	$RSS_HEADER .= "<"."?xml version=\"1.0\" encoding=\"".$l['a_meta_charset']."\"?".">\n";
	
	$RSS_HEADER .= "<!DOCTYPE rdf:RDF [\n<!ENTITY % HTMLlat1 PUBLIC\n\"-//W3C//ENTITIES Latin 1 for XHTML//EN\n\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml-lat1.ent\">\n%HTMLlat1;\n]>\n";

	$RSS_HEADER .= "<rdf:RDF xmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\" xmlns=\"http://purl.org/rss/1.0/\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\">\n\n";
	
	// --- ------------------------------------------------------
	if( (!$links_category) AND (!$wherequery) ) {
		$sql = "SELECT * FROM ".K_TABLE_LINKS_CATEGORIES." ORDER BY linkscat_name LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$links_category = $m['linkscat_id'];
			}
		}
		else {
			F_display_db_error();
		}
	}
	
	if($links_category) {
		$sqlc = "SELECT * FROM ".K_TABLE_LINKS_CATEGORIES." WHERE linkscat_id=".$links_category."";
		if($rc = F_aiocpdb_query($sqlc, $db)) {
			if($mc = F_aiocpdb_fetch_array($rc)) {
				$currenttarget = $mc['linkscat_target'];
				$thisname = F_decode_field($mc['linkscat_name']);
				$thisdesc = F_decode_field($mc['linkscat_description']);
								
				if (!$wherequery) {$wherequery = "WHERE (links_category='".$links_category."')";}
				else {$wherequery .= " AND (links_category='".$links_category."')";}
				
				$RSS_HEADER .= "<channel rdf:about=\"".K_PATH_PUBLIC_CODE."cp_links.".CP_EXT."\">\n";
				$RSS_HEADER .= "\t<title>".htmlentities($thisname, ENT_QUOTES, $l['a_meta_charset'])."</title>\n";
				$RSS_HEADER .= "\t<link>".K_PATH_PUBLIC_CODE."cp_links.".CP_EXT."?links_category=".$links_category."</link>\n";
				$RSS_HEADER .= "\t<description>\n";
				$RSS_HEADER .= "\t\t".htmlentities($thisdesc, ENT_QUOTES, $l['a_meta_charset'])."\n";
				$RSS_HEADER .= "\t</description>\n\n";	
				$RSS_HEADER .= "\t<dc:language>".$selected_language."</dc:language>\n";
			}
		}
		else {
			F_display_db_error();
		}
	}
	else { //various news from different categories
		$RSS_HEADER .= "<channel rdf:about=\"".K_PATH_PUBLIC_CODE."cp_links.".CP_EXT."\">\n";
		$RSS_HEADER .= "\t<title>".htmlentities(K_SITE_TITLE, ENT_QUOTES, $l['a_meta_charset'])."</title>\n";
		$RSS_HEADER .= "\t<link>".K_PATH_PUBLIC_CODE."cp_links.".CP_EXT."?wherequery=".urlencode($wherequery)."</link>\n";
		$RSS_HEADER .= "\t<description>\n";
		$RSS_HEADER .= "\t\tLINKS: ".K_SITE_DESCRIPTION."\n";
		$RSS_HEADER .= "\t</description>\n\n";	
	}
	
	$RSS_HEADER .= "<items>\n";
	$RSS_HEADER .= "\t<rdf:Seq>\n";
	
	if (!$wherequery) {$wherequery = "WHERE (links_name LIKE '".$selectedletter."%')";}
	else {$wherequery .= " AND (links_name LIKE '".$selectedletter."%')";}
	
	$sql = "SELECT * FROM ".K_TABLE_LINKS." ".$wherequery." ORDER BY ".$full_order_field."";

	
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			$item_uri = "".K_PATH_PUBLIC_CODE."cp_links.".CP_EXT."?selectedletter=".$m['links_name']."";
							
			$RSS_HEADER .= "\t\t<rdf:li resource=\"".$item_uri."\" />\n";

			$RSS_ITEMS .= "<item rdf:about=\"".$item_uri."\">\n";
			$RSS_ITEMS .= "\t<title>".htmlentities($m['links_name'], ENT_QUOTES, $l['a_meta_charset'])."</title>\n";
			$RSS_ITEMS .= "\t<link>".htmlentities(urldecode($m['links_link']))."</link>\n";
			$RSS_ITEMS .= "\t<description>\n";
			$RSS_ITEMS .= "\t\t".F_decode_field($m['links_description'])."\n";
			$RSS_ITEMS .= "\t</description>\n";
			$RSS_ITEMS .= "</item>\n\n";
		} //end of while
	}
	else {
		F_display_db_error();
	}
	
	$RSS_HEADER .= "\t</rdf:Seq>\n";
	$RSS_HEADER .= "</items>\n";
	$RSS_HEADER .= "</channel>\n\n";
	
	$RSS_DOC = $RSS_HEADER.$RSS_ITEMS."</rdf:RDF>";
	
	return $RSS_DOC;
}


// ------------------------------------------------------------
// Show select form for links
// ------------------------------------------------------------
function F_show_select_links($wherequery, $order_field, $orderdir, $firstrow, $rowsperpage) {
	global $l, $db, $selected_language, $aiocp_dp;
	global $links_category, $firstrow, $selectedlinks;
	global $selectedletter;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	require_once('../../shared/code/cp_functions_tree.'.CP_EXT);
	
	if(!$selectedlinks) {$selectedlinks=0;}
	if(!isset($viewmode)) {$viewmode=0;}
else {$viewmode=intval($viewmode);}
	if(!isset($selectedletter)) {$selectedletter = '';}

	// Initialize variables
	if(!$links_category) {
		$sql = "SELECT * FROM ".K_TABLE_LINKS_CATEGORIES." ORDER BY linkscat_sub_id,linkscat_position LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$links_category = $m['linkscat_id'];
			}
		}
		else {
			F_display_db_error();
		}
	}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_linksshow" id="form_linksshow">

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT category ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_category', 'h_linkscat_select'); ?></b></td>
<td class="fillOE">
<select name="links_category" id="links_category" size="0" onchange="document.form_linksshow.firstrow=0;document.form_linksshow.submit()">
<?php
$noscriptlink = $_SERVER['SCRIPT_NAME']."?";
if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {
	$noscriptlink .= "aiocp_dp=".$aiocp_dp."&amp;";
}
if (!empty($viewmode)) {$noscriptlink .= "viewmode=".$viewmode."&amp;";}
if (!empty($order_field)) {$noscriptlink .= "order_field=".urlencode($order_field)."&amp;";}
if (!empty($orderdir)) {$noscriptlink .= "orderdir=".$orderdir."&amp;";}
$noscriptlink .= "links_category=";
F_form_select_tree($links_category, false, K_TABLE_LINKS_CATEGORIES, "linkscat", $noscriptlink); 
?>
</td>
</tr>
<!-- END SELECT category ==================== -->
</table>

</td></tr>
</table>
 
<input type="hidden" name="selectedlinks" id="selectedlinks" value="<?php echo $selectedlinks; ?>" />
<input type="hidden" name="firstrow" id="firstrow" value="<?php echo $firstrow; ?>" />
<input type="hidden" name="order_field" id="order_field" value="<?php echo $order_field; ?>" />
<input type="hidden" name="orderdir" id="orderdir" value="<?php echo $orderdir; ?>" />
<input type="hidden" name="selectedletter" id="selectedletter" value="<?php echo $selectedletter; ?>" />
<input type="hidden" name="submitted" id="submitted" value="0" />
</form>

<!-- SHOW links ==================== -->
<?php
if ($links_category) {
	
	//create a list of disabled alphabetic buttons
	$disabled_letters = array();
	$alphabet = Array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
	while (list($key, $letter) = each($alphabet)) {
		$sqldab = "SELECT * FROM ".K_TABLE_LINKS." WHERE (links_category='".$links_category."' AND links_name LIKE '".$letter."%')  LIMIT 1";
		if($rdab = F_aiocpdb_query($sqldab, $db)) {
			if (!F_aiocpdb_fetch_array($rdab)) {
				$disabled_letters[] = $letter;
			}
		}
		else {
			F_display_db_error();
		}
	}

	F_alphanumeric_selector("form_linksshow", "selectedletter", $selectedletter, $disabled_letters);
	F_show_links($links_category, "", $order_field, $orderdir, $firstrow, K_MAX_ROWS_PER_PAGE);
}
?>
<!-- END SHOW links ==================== -->
<!-- ====================================================== -->

<!-- Submit form  ==================== -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
	function FJ_submit_links_form(newfirstrow, neworder_field, neworderdir, linkscategory, letter) {
		document.form_linksshow.selectedletter.value=letter;
		document.form_linksshow.links_category.value=linkscategory;
		document.form_linksshow.firstrow.value=newfirstrow;
		document.form_linksshow.order_field.value=neworder_field;
		document.form_linksshow.orderdir.value=neworderdir;
		document.form_linksshow.submitted.value=1;
		document.form_linksshow.submit();
	}

	document.form_linksshow.links_category.focus();
//]]>
</script>
<!-- END Cange focus to links_id select -->

<?php
} //end of function

// ------------------------------------------------------------
// Search form for links
// ------------------------------------------------------------
function F_search_links($wherequery, $order_field, $orderdir, $firstrow, $rowsperpage) {
	global $l, $db, $selected_language, $aiocp_dp;
	global $term, $links_category, $selectedlinks, $submitted, $linkssearch, $addterms;
	global $selectedletter;
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
	if(!isset($selectedletter)) {$selectedletter = '';}
	
	if(!$links_category) {$links_category="0";} // All categories
	
if(!F_count_rows(K_TABLE_LINKS)) { //if the table is void (no items) display message
	echo "<h2>".$l['m_databasempty']."</h2>";
}
else { //the table is not empty

// ---------------------------------------------------------------

if($linkssearch OR $submitted) { // Submitting query (search results)
	if(isset($term) AND ($term != "")) {
		$wherequery = "WHERE (";
		$terms = preg_split("/[\s]+/i", addslashes($term)); // Get all the words into an array
		$size = sizeof($terms);
		//redundant check for security feature
		if($addterms != "AND"){$addterms = "OR";}
		$wherequery .= "(";
		for($i=0;$i<$size;$i++) {
			if($i>0) {$wherequery .= " ".$addterms." ";}
			$wherequery .= "((links_name LIKE '%$terms[$i]%')";
			$wherequery .= " OR (links_link LIKE '%$terms[$i]%')";
			$wherequery .= " OR (links_description LIKE '%$terms[$i]%'))";
		}
		$wherequery .= ")";
		if($links_category) {$wherequery .= " AND (links_category=".$links_category.")";}
		$wherequery .= ")"; // close WHERE clause
	}
	F_show_links($links_category, $wherequery, $order_field, $orderdir, $firstrow, $rowsperpage);
} //end if($linkssearch OR $submitted)

// ---------------------------------------------------------------
?>

<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_linkssearch" id="form_linkssearch">

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
<select name="links_category" id="links_category" size="0">
<?php 
if(!$links_category) {echo "<option value=\"0\" selected=\"selected\">".$l['d_all_categories']."</option>";}
else {echo "<option value=\"0\">".$l['d_all_categories']."</option>";}

$noscriptlink = $_SERVER['SCRIPT_NAME']."?";
if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {
	$noscriptlink .= "aiocp_dp=".$aiocp_dp."&amp;";
}
if (!empty($viewmode)) {$noscriptlink .= "viewmode=".$viewmode."&amp;";}
if (!empty($order_field)) {$noscriptlink .= "order_field=".urlencode($order_field)."&amp;";}
if (!empty($orderdir)) {$noscriptlink .= "orderdir=".$orderdir."&amp;";}
$noscriptlink .= "links_category=";
F_form_select_tree($links_category, false, K_TABLE_LINKS_CATEGORIES, "linkscat", $noscriptlink); ?>
</td>
</tr>
<!-- END SELECT category ==================== -->

</table>

</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="selectedlinks" id="selectedlinks" value="<?php echo $selectedlinks; ?>" />
<input type="hidden" name="linkssearch" id="linkssearch" value="" />
<input type="hidden" name="firstrow" id="firstrow" value="<?php echo $firstrow; ?>" />
<input type="hidden" name="order_field" id="order_field" value="<?php echo $order_field; ?>" />
<input type="hidden" name="orderdir" id="orderdir" value="<?php echo $orderdir; ?>" />
<input type="hidden" name="selectedletter" id="selectedletter" value="<?php echo $selectedletter; ?>" />
<input type="hidden" name="submitted" id="submitted" value="0" />
<?php F_submit_button("form_linkssearch","linkssearch",$l['w_search']); ?>
</td></tr>
</table>
</form>

<?php
// ---------------------------------------------------------------
} //end of else for void table
?>
 
<!-- Submit form  ==================== -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
	function FJ_submit_links_form(newfirstrow, neworder_field, neworderdir, linkscategory, letter) {
		document.form_linkssearch.selectedletter.value=letter;
		document.form_linkssearch.links_category.value=linkscategory;
		document.form_linkssearch.firstrow.value=newfirstrow;
		document.form_linkssearch.order_field.value=neworder_field;
		document.form_linkssearch.orderdir.value=neworderdir;
		document.form_linkssearch.submitted.value=1;
		document.form_linkssearch.submit();
	}
//]]>
</script>
<!-- END Submit form ==================== -->
<?php
} //end of function


//============================================================+
// END OF FILE                                                 
//============================================================+
?>
