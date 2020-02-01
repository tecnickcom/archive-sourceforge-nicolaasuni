<?php
//============================================================+
// File name   : cp_functions_awards.php
// Begin       : 2001-11-25
// Last Update : 2008-02-25
// 
// Description : Functions for awards
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
function F_select_awards($wherequery, $order_field, $orderdir, $firstrow, $rowsperpage) {
	global $l, $aiocp_dp;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
?>
	<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_awardlist" id="form_awardlist">
	<?php F_show_awards($wherequery, $order_field, $orderdir, $firstrow, $rowsperpage); ?>
	<input type="hidden" name="firstrow" id="firstrow" value="<?php echo $firstrow; ?>" />
	<input type="hidden" name="order_field" id="order_field" value="<?php echo $order_field; ?>" />
	<input type="hidden" name="orderdir" id="orderdir" value="<?php echo $orderdir; ?>" />
	<input type="hidden" name="submitted" id="submitted" value="0" />
	</form>
	<!-- Submit form with new order field ==================== -->
	<script language="JavaScript" type="text/javascript">
	//<![CDATA[
	function FJ_submit_awards_form(newfirstrow, neworder_field, neworderdir) {
		document.form_awardlist.firstrow.value=newfirstrow;
		document.form_awardlist.order_field.value=neworder_field;
		document.form_awardlist.orderdir.value=neworderdir;
		document.form_awardlist.submitted.value=1;
		document.form_awardlist.submit();
	}
	//]]>
	</script>
	<!-- END Submit form with new order field ==================== -->
<?php
return TRUE;
}

// ------------------------------------------------------------
// Display links
// ------------------------------------------------------------
function F_show_awards($wherequery, $order_field, $orderdir, $firstrow, $rowsperpage) {
	global $l, $db, $selected_language;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_page.'.CP_EXT);
	require_once('../../shared/code/cp_functions_dynamic_pages.'.CP_EXT);
	
	//initialize variable
	if(!$firstrow) {$firstrow = "0";} 
	if(!$rowsperpage) {$rowsperpage = K_MAX_ROWS_PER_PAGE;}
	else {$rowsperpage=intval($rowsperpage);}
	
	if(!$order_field) {$order_field = "award_date DESC";}
	else {$order_field = addslashes(preg_replace("/[\s]+/i", " ", $order_field));}
	if(!$orderdir) {$orderdir=0; $nextorderdir=1; $full_order_field = $order_field;}
	else {$orderdir=1; $nextorderdir=0; $full_order_field = $order_field." DESC";}
	$full_order_field = urldecode($full_order_field);
	
	if(!F_count_rows(K_TABLE_AWARDS)) { //if the table is void (no items) display message
		echo "<h2>".$l['m_databasempty']."</h2>";
		return FALSE;
	}
	
	if (!$wherequery) {
		$sql = "SELECT * FROM ".K_TABLE_AWARDS." ORDER BY ".$full_order_field." LIMIT ".$firstrow.",".$rowsperpage."";
	}
	else {
		$sql = "SELECT * FROM ".K_TABLE_AWARDS." ".$wherequery." ORDER BY ".$full_order_field." LIMIT ".$firstrow.",".$rowsperpage."";
	}
	
	if($r = F_aiocpdb_query($sql, $db)) {
		echo "<table class=\"edge\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">";
		echo "<tr class=\"edge\"><td class=\"edge\">";
		echo "<table border=\"0\" cellspacing=\"1\" cellpadding=\"1\" class=\"fill\" style=\"width:100%\">";
		while($m = F_aiocpdb_fetch_array($r)) {
			
			//change style for each row
			if (isset($rowodd) AND ($rowodd)) {
				$rowclass = "O";
				$rowodd = 0;
			} else {
				$rowclass = "E";
				$rowodd = 1;
			}
			
			echo "<tr class=\"fill".$rowclass."\"><td class=\"fill".$rowclass."O\" valign=\"top\">";
			echo "<img src=\"";
			if(F_is_relative_link($m['award_logo'])) {echo K_PATH_IMAGES_AWARDS;}
			echo "".$m['award_logo']."\" border=\"0\" alt=\"".htmlentities($m['award_name'], ENT_NOQUOTES, $l['a_meta_charset'])."\" /><br />&nbsp;";
			echo "</td><td class=\"fill".$rowclass."E\" valign=\"top\">";
			
			echo "<small>".$m['award_date']."</small><br />";
			
			if (isset($m['award_link']) AND $m['award_link']) {
				echo "<a href=\"".trim($m['award_link'])."\" target=\"_blank\"><b>".$m['award_name']."</b></a>";
			}
			else {
				echo "<b>".htmlentities($m['award_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</b>";
			}
			echo "<br />";
			
			$thisldesc = F_decode_field($m['award_description']);
			echo F_evaluate_modules($thisldesc);
			echo "</td></tr>";
			
		} //end of while
		echo "</table>";
		echo "</td></tr></table>";
	}
	else {
		F_display_db_error();
	}
	
		
	// Display RSS icon Link
	//$url_request = "wherequery=".urlencode($wherequery)."";
	$url_request = "x=x";
	if ($order_field) {
		$url_request .= "&amp;order_field=".urlencode($order_field)."";
	}
	if ($orderdir) {
		$url_request .= "&amp;orderdir=".$orderdir."";
	}
	
	echo "<br /><div align=\"center\"><a href=\"../../public/code/cp_awards_rss.".CP_EXT."?".$url_request."\" target=\"_blank\"><img src=\"../../pagefiles/rss.gif\" width=\"36\" height=\"14\" alt=\"RSS (XML RDF)\" border=\"0\" /></a></div><br />";
	
	// --- ------------------------------------------------------
	// --- page jump
	$sql = "SELECT count(*) AS total FROM ".K_TABLE_AWARDS." ".$wherequery."";
	if (!empty($order_field)) {$param_array = "&amp;order_field=".urlencode($order_field)."";}
	if (!empty($orderdir)) {$param_array .= "&amp;orderdir=".$orderdir."";}
	$param_array .= "&amp;submitted=1";
	F_show_page_navigator($_SERVER['SCRIPT_NAME'], $aiocp_dp, $sql, $firstrow, $rowsperpage, $param_array);
return;
}



// ------------------------------------------------------------
// Display links in RSS 1.0 format
// ------------------------------------------------------------
function F_show_awards_RSS($wherequery, $order_field, $orderdir) {
	global $l, $db, $selected_language;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_page.'.CP_EXT);

	$RSS_HEADER = "";
	$RSS_ITEMS = "";
	
	if(!$order_field) {$order_field = "award_date DESC";}
	else {$order_field = addslashes(preg_replace("/[\s]+/i", " ", $order_field));}
	if(!$orderdir) {$orderdir=0; $nextorderdir=1; $full_order_field = $order_field;}
	else {$orderdir=1; $nextorderdir=0; $full_order_field = $order_field." DESC";}
	$full_order_field = urldecode($full_order_field);
	
	if(!F_count_rows(K_TABLE_AWARDS)) { //if the table is void (no items) display message
		return FALSE;
	}
	
	
	//send page header
	$RSS_HEADER .= "<"."?xml version=\"1.0\" encoding=\"".$l['a_meta_charset']."\"?".">\n";
	
	$RSS_HEADER .= "<!DOCTYPE rdf:RDF [\n<!ENTITY % HTMLlat1 PUBLIC\n\"-//W3C//ENTITIES Latin 1 for XHTML//EN\n\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml-lat1.ent\">\n%HTMLlat1;\n]>\n";

	$RSS_HEADER .= "<rdf:RDF xmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\" xmlns=\"http://purl.org/rss/1.0/\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\">\n\n";
	
$RSS_HEADER .= "<channel rdf:about=\"".K_PATH_PUBLIC_CODE."cp_links.".CP_EXT."\">\n";
		$RSS_HEADER .= "\t<title>".htmlentities(K_SITE_TITLE, ENT_QUOTES, $l['a_meta_charset'])."</title>\n";
		$RSS_HEADER .= "\t<link>".K_PATH_PUBLIC_CODE."cp_links.".CP_EXT."";
		if ($wherequery) {
			$RSS_HEADER .= "?wherequery=".urlencode($wherequery)."";
		}
		$RSS_HEADER .= "</link>\n";
		$RSS_HEADER .= "\t<description>\n";
		$RSS_HEADER .= "\t\tLINKS: ".K_SITE_DESCRIPTION."\n";
		$RSS_HEADER .= "\t</description>\n\n";	
		$RSS_HEADER .= "<items>\n";
	$RSS_HEADER .= "\t<rdf:Seq>\n";
	
	if (!$wherequery) {
		$sql = "SELECT * FROM ".K_TABLE_AWARDS." ORDER BY ".$full_order_field."";
	}
	else {
		$sql = "SELECT * FROM ".K_TABLE_USERS." ".$wherequery." ORDER BY ".$full_order_field."";
	}
	
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			$item_uri = "".K_PATH_PUBLIC_CODE."cp_awards.".CP_EXT."";
			if ($wherequery) {
				$item_uri .= "?wherequery=".urlencode($wherequery)."";
			}
							
			$RSS_HEADER .= "\t\t<rdf:li resource=\"".$item_uri."\" />\n";

			$RSS_ITEMS .= "<item rdf:about=\"".$item_uri."\">\n";
			$RSS_ITEMS .= "\t<title>".htmlentities($m['award_name'], ENT_QUOTES, $l['a_meta_charset'])."</title>\n";
			$RSS_ITEMS .= "\t<link>".htmlentities(urldecode($m['award_link']))."</link>\n";
			$RSS_ITEMS .= "\t<description>\n";
			if(F_is_relative_link($m['award_logo'])) {
				$m['award_logo'] = F_resolve_url_path(K_PATH_IMAGES_AWARDS.$m['award_logo']);
			}	
			$RSS_ITEMS .= "\t\t<img src=\"".$m['award_logo']."\" border=\"0\" alt=\"".$m['award_name']."\" /><br />\n";
			$RSS_ITEMS .= "\t\t".htmlentities(unhtmlentities(F_decode_field($m['award_description'])), ENT_QUOTES, $l['a_meta_charset'])."\n";
			$RSS_ITEMS .= "\t</description>\n";
				
			// http://dublincore.org/documents/dces/
			// Title Creator Subject Description Publisher Date Type Format Identifier Source Language Relation Coverage Rights
			$RSS_ITEMS .= "\t<dc:date>".$m['award_date']."</dc:date>\n";
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
// Display all awards for selectd product
// ------------------------------------------------------------
function F_show_product_awards($product_name) {
	global $l, $db, $selected_language;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	$award_str = ""; // returning string
	
	$sql = "SELECT * FROM ".K_TABLE_AWARDS." WHERE award_name LIKE '%".$product_name."%' ORDER BY award_date DESC";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			if (isset($m['award_link']) AND (!empty($m['award_link']))) {
				$award_str .= "<a href=\"".trim($m['award_link'])."\" target=\"_blank\">";
			}
			if (isset($m['award_logo']) AND (!empty($m['award_logo']))) {
				$award_str .= "<img src=\"";
				if(F_is_relative_link($m['award_logo'])) {
					$award_str .= K_PATH_IMAGES_AWARDS;
				}
				$award_str .= "".$m['award_logo']."\" border=\"0\" alt=\"[".$m['award_date']."] ".htmlentities($m['award_name'], ENT_NOQUOTES, $l['a_meta_charset'])."\" />";
			} else {
				$award_str .= "".htmlentities($m['award_name'], ENT_NOQUOTES, $l['a_meta_charset'])."";
			}
			if (isset($m['award_link']) AND (!empty($m['award_link']))) {
				$award_str .= "</a>";
			}
			$award_str .= "\n";
		} //end of while
	}
	else {
		F_display_db_error();
	}
		
	return $award_str;
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
