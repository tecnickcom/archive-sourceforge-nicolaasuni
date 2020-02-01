<?php
//============================================================+
// File name   : cp_functions_review.php
// Begin       : 2001-09-20
// Last Update : 2008-02-25
// 
// Description : Functions for reviews
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
// Display Reviews
// $viewmode: 0=compact(headers only); 1=full 
// $selectedreviews = reviews to display in full mode while in compact mode
// ------------------------------------------------------------
function F_show_reviews($review_category, $viewmode, $selectedreviews, $wherequery, $order_field, $orderdir, $firstrow, $rowsperpage) {
	global $l, $db, $selected_language;
	global $term, $submitted, $reviewssearch, $addterms;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_page.'.CP_EXT);
	require_once('../../shared/code/cp_functions_dynamic_pages.'.CP_EXT);
	
	//initialize variable
	$userlevel = $_SESSION['session_user_level'];
	
	if(!$firstrow) {$firstrow="0";}
	else {$firstrow=intval($firstrow);} 
	if(!$rowsperpage) {$rowsperpage = K_MAX_ROWS_PER_PAGE;}
	else {$rowsperpage=intval($rowsperpage);}
	$orderdir = 0;
	if(!$order_field) {$order_field = "review_date DESC";}
	else {$order_field = addslashes($order_field);}
	if(!$orderdir) {$orderdir=0; $nextorderdir=1; $full_order_field = $order_field;}
	else {$orderdir=1; $nextorderdir=0; $full_order_field = $order_field." DESC";}
	$full_order_field = urldecode($full_order_field);
	
	if(!F_count_rows(K_TABLE_REVIEWS)) { //if the table is void (no items) display message
		echo "<h2>".$l['m_databasempty']."</h2>";
		return FALSE;
	}
	
	echo "<table class=\"edge\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">";
	// --- ------------------------------------------------------
	
	if (isset($review_category) AND (strlen($review_category)>0) AND ($review_category==0)) { //select all categories
		$wherequery = "WHERE 1";
	}
	
	if( (!$review_category) AND (!$wherequery) ) {
		$sql = "SELECT * FROM ".K_TABLE_REVIEWS_CATEGORIES." WHERE revcat_level<=".$userlevel." ORDER BY revcat_name LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$review_category = $m['revcat_id'];
			}
			else {
				echo "<tr><td></td></tr></table>\n";
				F_print_error("WARNING", $l['m_authorization_deny']);
				//F_logout_form();
				return;
			}
		}
		else {
			F_display_db_error();
		}
	}
	
	if($review_category) {
		$sqlc = "SELECT * FROM ".K_TABLE_REVIEWS_CATEGORIES." WHERE revcat_id=".$review_category."";
		if($rc = F_aiocpdb_query($sqlc, $db)) {
			if($mc = F_aiocpdb_fetch_array($rc)) {
				if($mc['revcat_level']>$userlevel) {
					echo "<tr><td></td></tr></table>\n";
					F_print_error("WARNING", $l['m_authorization_deny']);
					//F_logout_form();
					return;
				}
				echo "<tr class=\"edge\">";
				echo "<th class=\"edge\" align=\"left\">";
				echo "".htmlentities(F_decode_field($mc['revcat_name']), ENT_NOQUOTES, $l['a_meta_charset']).":<br />\n";
				echo F_evaluate_modules(F_decode_field($mc['revcat_description']));
				echo "</th></tr>";
				
				$subcatquery = "";
				// search sub categories
				$sqlsc = "SELECT * FROM ".K_TABLE_REVIEWS_CATEGORIES." WHERE revcat_sub_id=".$review_category."";
				if($rsc = F_aiocpdb_query($sqlsc, $db)) {
					while($msc = F_aiocpdb_fetch_array($rsc)) {
						$subcatquery .= " OR review_category='".$msc['revcat_id']."'";
					}
				}
				else {
					F_display_db_error();
				}
				
				if (!$wherequery) {$wherequery = "WHERE (review_category='".$review_category."'".$subcatquery.")";}
				else {$wherequery .= " AND (review_category='".$review_category."'".$subcatquery.")";}
			}
		}
		else {
			F_display_db_error();
		}
	}
	
	if (!$wherequery) {
		$sql = "SELECT * FROM ".K_TABLE_REVIEWS." ORDER BY ".$full_order_field." LIMIT ".$firstrow.",".$rowsperpage."";
	}
	else {
		$sql = "SELECT * FROM ".K_TABLE_REVIEWS." ".$wherequery." ORDER BY ".$full_order_field." LIMIT ".$firstrow.",".$rowsperpage."";
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
			if (!$review_category) {$catdata = F_get_review_category_data($m['review_category']);}
			//check authorization rights
			if (($review_category) OR ($userlevel >= $catdata->level)) {
				
				if(($viewmode)OR($m['review_id'] == $selectedreviews)) { //full mode	
					echo "<tr class=\"fill".$rowclass."\"><td class=\"fill".$rowclass."E\">";
					
					echo "<table class=\"edge\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\" style=\"width:100%\">";
					echo "<tr class=\"edge\">";
					echo "<th class=\"edge\" align=\"left\">";
					echo "<a class=\"edge\" href=\"".htmlentities(urldecode($m['review_product_link']))."\" target=\"_blank\">".htmlentities($m['review_product_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</a>";
					echo "</th></tr>";
					echo "<tr class=\"edge\">";
					echo "<td class=\"edge\">";
					echo "<table class=\"fill\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\" style=\"width:100%\">";
					echo "<tr class=\"fillO\"><td class=\"fillOO\" valign=\"top\" align=\"left\">";
					echo "<img src=\"";
					if(F_is_relative_link($m['review_image'])) {
						echo K_PATH_IMAGES_REVIEWS;
					}
					echo "".$m['review_image']."\" border=\"0\" alt=\"\" />";
					echo "</td>";
					
					echo "<td class=\"fillOE\" valign=\"top\">";
					echo "<table class=\"fill\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\" style=\"width:100%\">";
					echo "<tr><td class=\"fillOO\" align=\"right\"><b>".$l['w_manufacturer']."</b></td>";
					if (!$m['review_manuf_link']) {$m['review_manuf_link'] = "#";}
					echo "<td class=\"fillOE\"><a href=\"".htmlentities(urldecode($m['review_manuf_link']))."\" target=\"_blank\">".htmlentities($m['review_manuf_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</a></td></tr>";
					echo "<tr class=\"fillE\"><td class=\"fillEO\" align=\"right\"><b>".$l['w_author']."</b></td>";
					if (!$m['review_author_link']) {$m['review_author_link'] = "#";}
					echo "<td class=\"fillEE\"><a href=\"".htmlentities(urldecode($m['review_author_link']))."\" target=\"_blank\">".htmlentities($m['review_author_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</a></td></tr>";
					echo "<tr class=\"fillO\"><td class=\"fillOO\" align=\"right\"><b>".$l['w_date']."</b></td>";
					echo "<td class=\"fillOE\">".$m['review_date']."</td></tr>";
					if($m['review_rating']) {
						echo "<tr class=\"fillE\"><td class=\"fillEO\" align=\"right\"><b>&nbsp;".$l['w_rating']."</b></td>";
						echo "<td class=\"fillEE\">".$m['review_rating']."</td></tr>";
					}
					echo "</table>";
					echo "</td></tr>";
					
					echo "<tr class=\"fillO\"><td class=\"fillOE\" colspan=\"3\">"; //reviews text
					echo F_evaluate_modules(F_decode_field($m['review_text']));
					echo "</td></tr>";
					echo "</table>";
					echo "</td></tr></table>";
					echo "</td></tr>";
				}
				else { //compact mode
					echo "<tr class=\"fill".$rowclass."\"><td class=\"fill".$rowclass."E\">";
					echo "<a href=\"javascript:FJ_submit_reviews_form('".$firstrow."','".urlencode($order_field)."','".$orderdir."','".$m['review_category']."','".$m['review_id']."');\">".$m['review_date']." - ".htmlentities($m['review_product_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</a>";
					
					// noscript alternative for search engines
					echo "<noscript><a href=\"cp_reviews.".CP_EXT."?rid=".$m['review_id']."\">".$m['review_date']." - ".htmlentities($m['review_product_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</a></noscript>";
					
					echo "</td></tr>\n";
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
		
	// Display RSS icon Link
	//$url_request = "wherequery=".urlencode($wherequery)."";
	$url_request = "x=x";
	if ($review_category) {
		$url_request .= "&amp;review_category=".$review_category."";
	}
	if ($order_field) {
		$url_request .= "&amp;order_field=".urlencode($order_field)."";
	}
	if ($orderdir) {
		$url_request .= "&amp;orderdir=".$orderdir."";
	}
	
	echo "<br /><div align=\"center\"><a href=\"../../public/code/cp_reviews_rss.".CP_EXT."?".$url_request."\" target=\"_blank\"><img src=\"../../pagefiles/rss.gif\" width=\"36\" height=\"14\" alt=\"RSS (XML RDF)\" border=\"0\" /></a></div><br />";
	
	// --- ------------------------------------------------------
	// --- page jump
	$sql = "SELECT count(*) AS total FROM ".K_TABLE_REVIEWS." ".$wherequery."";
	if (!empty($order_field)) {$param_array = "&amp;order_field=".urlencode($order_field)."";}
	if (!empty($orderdir)) {$param_array .= "&amp;orderdir=".$orderdir."";}
	$param_array .= "&amp;submitted=1";
	if (!empty($viewmode)) {$param_array .= "&amp;viewmode=".$viewmode."";}
	if (!empty($review_category)) {$param_array .= "&amp;review_category=".$review_category."";}
	if (!empty($term)) {$param_array .= "&amp;term=".urlencode($term)."";}
	if (!empty($addterms)) {$param_array .= "&amp;addterms=".$addterms."";}
	if (!empty($reviewssearch)) {$param_array .= "&amp;reviewssearch=".$reviewssearch."";}
	F_show_page_navigator($_SERVER['SCRIPT_NAME'], $aiocp_dp, $sql, $firstrow, $rowsperpage, $param_array);
	
return;
}



// ------------------------------------------------------------
// Display Reviews in RSS 1.0 format
// ------------------------------------------------------------
function F_show_reviews_RSS($review_category, $wherequery, $order_field, $orderdir) {
	global $l, $db, $selected_language;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_page.'.CP_EXT);
	
	//initialize variable
	$userlevel = $_SESSION['session_user_level'];

	$RSS_HEADER = "";
	$RSS_ITEMS = "";
	
	if(!$order_field) {$order_field = "review_date DESC";}
	else {$order_field = addslashes(preg_replace("/[\s]+/i", " ", $order_field));} 
	if(!$orderdir) {$orderdir=0; $nextorderdir=1; $full_order_field = $order_field;}
	else {$orderdir=1; $nextorderdir=0; $full_order_field = $order_field." DESC";}
	$full_order_field = urldecode($full_order_field);
	
	if(!F_count_rows(K_TABLE_REVIEWS)) { //if the table is void (no items) display message
		return FALSE;
	}
	
	//send page header
	$RSS_HEADER .= "<"."?xml version=\"1.0\" encoding=\"".$l['a_meta_charset']."\"?".">\n";
	
	$RSS_HEADER .= "<!DOCTYPE rdf:RDF [\n<!ENTITY % HTMLlat1 PUBLIC\n\"-//W3C//ENTITIES Latin 1 for XHTML//EN\n\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml-lat1.ent\">\n%HTMLlat1;\n]>\n";

	$RSS_HEADER .= "<rdf:RDF xmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\" xmlns=\"http://purl.org/rss/1.0/\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\">\n\n";
	// --- ------------------------------------------------------
	
	if (isset($review_category) AND ($review_category==0)) { //select all categories
		$wherequery = "WHERE 1";
	}
	
	if( (!$review_category) AND (!$wherequery) ) {
		$sql = "SELECT * FROM ".K_TABLE_REVIEWS_CATEGORIES." WHERE revcat_level<=".$userlevel." ORDER BY revcat_name LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$review_category = $m['revcat_id'];
			}
		}
		else {
			F_display_db_error();
		}
	}
	
	if($review_category) {
		$sqlc = "SELECT * FROM ".K_TABLE_REVIEWS_CATEGORIES." WHERE revcat_id=".$review_category."";
		if($rc = F_aiocpdb_query($sqlc, $db)) {
			if($mc = F_aiocpdb_fetch_array($rc)) {
				if($mc['revcat_level'] > $userlevel) {
					return FALSE; //authorization deny
				}
				
				if (!$wherequery) {$wherequery = "WHERE (review_category='".$review_category."')";}
				else {$wherequery .= " AND (review_category='".$review_category."')";}
				
				$RSS_HEADER .= "<channel rdf:about=\"".K_PATH_PUBLIC_CODE."cp_review.".CP_EXT."\">\n";
				$RSS_HEADER .= "\t<title>".htmlentities(F_decode_field($mc['revcat_name']), ENT_QUOTES, $l['a_meta_charset'])."</title>\n";
				$RSS_HEADER .= "\t<link>".K_PATH_PUBLIC_CODE."cp_review.".CP_EXT."?review_category=".$review_category."</link>\n";
				$RSS_HEADER .= "\t<description>\n";
				$RSS_HEADER .= "\t\t".htmlentities(F_decode_field($mc['revcat_description']))."\n";
				$RSS_HEADER .= "\t</description>\n\n";	
				$RSS_HEADER .= "\t<dc:language>".$selected_language."</dc:language>\n";
				if(F_is_relative_link($mc['revcat_image'])) {
						$mc['revcat_image'] = F_resolve_url_path(K_PATH_IMAGES_REVIEWS.$mc['revcat_image']);
				}
				$RSS_HEADER .= "<image rdf:resource=\"".$mc['revcat_image']."\" />\n";
			}
		}
		else {
			F_display_db_error();
		}
	}
	else { //various news from different categories
		$RSS_HEADER .= "<channel rdf:about=\"".K_PATH_PUBLIC_CODE."cp_review.".CP_EXT."\">\n";
		$RSS_HEADER .= "\t<title>".htmlentities(K_SITE_TITLE, ENT_QUOTES, $l['a_meta_charset'])."</title>\n";
		$RSS_HEADER .= "\t<link>".K_PATH_PUBLIC_CODE."cp_review.".CP_EXT."?wherequery=".urlencode($wherequery)."</link>\n";
		$RSS_HEADER .= "\t<description>\n";
		$RSS_HEADER .= "\t\tREVIEWS: ".K_SITE_DESCRIPTION."\n";
		$RSS_HEADER .= "\t</description>\n\n";	
	}
	
	$RSS_HEADER .= "<items>\n";
	$RSS_HEADER .= "\t<rdf:Seq>\n";
	
	if (!$wherequery) {
		$sql = "SELECT * FROM ".K_TABLE_REVIEWS." ORDER BY ".$full_order_field."";
	}
	else {
		$sql = "SELECT * FROM ".K_TABLE_REVIEWS." ".$wherequery." ORDER BY ".$full_order_field."";
	}
	
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			
			//get category data
			if (!$review_category) {$catdata = F_get_review_category_data($m['review_category']);}
			//check authorization rights
			if (($review_category) OR ($userlevel >= $catdata->level)) {
				$item_uri = "".K_PATH_PUBLIC_CODE."cp_review.".CP_EXT."?review_category=".$m['review_category']."&amp;selectedreviews=".$m['review_id']."";
							
				$RSS_HEADER .= "\t\t<rdf:li resource=\"".$item_uri."\" />\n";

				$RSS_ITEMS .= "<item rdf:about=\"".$item_uri."\">\n";
				$RSS_ITEMS .= "\t<title>".htmlentities($m['review_product_name'], ENT_QUOTES, $l['a_meta_charset'])."</title>\n";
				$RSS_ITEMS .= "\t<link>".htmlentities(urldecode($m['review_product_link']))."</link>\n";
				$RSS_ITEMS .= "\t<description>\n";
				if(F_is_relative_link($m['review_image'])) {
						$m['review_image'] = F_resolve_url_path(K_PATH_IMAGES_REVIEWS.$m['review_image']);
				}
				$RSS_ITEMS .= "\t\t<img src=\"".$m['review_image']."\" border=\"0\" alt=\"".$m['review_product_name']."\" /><br />";
				$RSS_ITEMS .= "\t\t".htmlentities(unhtmlentities(F_decode_field($m['review_text'])), ENT_QUOTES, $l['a_meta_charset'])."\n";
				$RSS_ITEMS .= "\t</description>\n";
				
				// http://dublincore.org/documents/dces/
				// Title Creator Subject Description Publisher Date Type Format Identifier Source Language Relation Coverage Rights
				$RSS_ITEMS .= "\t<dc:date>".$m['review_date']."</dc:date>\n";
				$RSS_ITEMS .= "\t<dc:creator>".htmlentities($m['review_author_name'], ENT_QUOTES, $l['a_meta_charset'])." [".htmlentities(urldecode($m['review_author_link']))."]</dc:creator>\n";
				$RSS_ITEMS .= "\t<dc:source>".htmlentities($m['review_manuf_name'], ENT_QUOTES, $l['a_meta_charset'])." [".htmlentities(urldecode($m['review_manuf_link']))."]</dc:source>\n";
				$RSS_ITEMS .= "</item>\n\n";
			}
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
// Show select form for reviews
// ------------------------------------------------------------
function F_show_select_reviews($wherequery, $order_field, $orderdir, $firstrow, $rowsperpage) {
	global $l, $db, $selected_language, $aiocp_dp;
	global $review_category, $viewmode, $selectedreviews;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	require_once('../../shared/code/cp_functions_tree.'.CP_EXT);
	
	if(!$selectedreviews) {$selectedreviews=0;}
	if(!isset($viewmode)) {$viewmode=0;}
else {$viewmode=intval($viewmode);}
	
	// Initialize variables
	$userlevel = $_SESSION['session_user_level'];

	if(!$review_category) {
		$sql = "SELECT * FROM ".K_TABLE_REVIEWS_CATEGORIES." WHERE revcat_level<=".$userlevel." ORDER BY revcat_sub_id,revcat_position LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$review_category = $m['revcat_id'];
			}
		}
		else {
			F_display_db_error();
		}
	}
?>
<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_reviewsshow" id="form_reviewsshow">

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT category ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_category', 'h_reviewscat_select'); ?></b></td>
<td class="fillOE">
<select name="review_category" id="review_category" size="0" onchange="document.form_reviewsshow.firstrow=0;document.form_reviewsshow.submit()">
<?php 
$noscriptlink = $_SERVER['SCRIPT_NAME']."?";
if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {
	$noscriptlink .= "aiocp_dp=".$aiocp_dp."&amp;";
}
if (!empty($viewmode)) {$noscriptlink .= "viewmode=".$viewmode."&amp;";}
if (!empty($order_field)) {$noscriptlink .= "order_field=".urlencode($order_field)."&amp;";}
if (!empty($orderdir)) {$noscriptlink .= "orderdir=".$orderdir."&amp;";}
$noscriptlink .= "review_category=";
F_form_select_tree($review_category, false, K_TABLE_REVIEWS_CATEGORIES, "revcat", $noscriptlink);
?>
</td>
</tr>
<!-- END SELECT category ==================== -->

<!-- SELECT view mode ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_mode', 'h_list_mode'); ?></b></td>
<td class="fillEE">
<select name="viewmode" id="viewmode" size="0" onchange="document.form_reviewsshow.selectedreviews.value=''; document.form_reviewsshow.submit()">
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

</table>

</td>
</tr>
</table>
<br />
<!-- SHOW reviews ==================== -->
<?php 
if ($review_category) {
	F_show_reviews($review_category, $viewmode, $selectedreviews, $wherequery, $order_field, $orderdir, $firstrow, K_MAX_ROWS_PER_PAGE);
}
?>
<!-- END SHOW reviews ==================== -->

<input type="hidden" name="selectedreviews" id="selectedreviews" value="<?php echo $selectedreviews; ?>" />
<input type="hidden" name="order_field" id="order_field" value="<?php echo $order_field; ?>" />
<input type="hidden" name="orderdir" id="orderdir" value="<?php echo $orderdir; ?>" />
<input type="hidden" name="firstrow" id="firstrow" value="<?php echo $firstrow; ?>" />
<input type="hidden" name="submitted" id="submitted" value="0" />
</form>
<!-- ====================================================== -->

<!-- Submit form  ==================== -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
function FJ_submit_reviews_form(newfirstrow, neworder_field, neworderdir, reviewscategory, reviewsselected) {
	document.form_reviewsshow.review_category.value=reviewscategory;
	document.form_reviewsshow.selectedreviews.value=reviewsselected;
	document.form_reviewsshow.firstrow.value=newfirstrow;
	document.form_reviewsshow.order_field.value=neworder_field;
	document.form_reviewsshow.orderdir.value=neworderdir;
	document.form_reviewsshow.submitted.value=1;
	document.form_reviewsshow.submit();
}

document.form_reviewsshow.review_category.focus();
//]]>
</script>
<!-- END Cange focus to review_id select -->
<?php
} //end of function


// ------------------------------------------------------------
// Search form for reviewsletter
// ------------------------------------------------------------
function F_search_reviews($wherequery, $order_field, $orderdir, $firstrow, $rowsperpage) {
	global $l, $db, $selected_language, $aiocp_dp;
	global $term, $submitted, $review_category, $reviewssearch, $selectedreviews, $addterms;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	require_once('../../shared/code/cp_functions_tree.'.CP_EXT);
	
	if(!$firstrow) {$firstrow="0";}
	else {$firstrow=intval($firstrow);} 
	if(!$rowsperpage) {$rowsperpage = K_MAX_ROWS_PER_PAGE;}
	else {$rowsperpage=intval($rowsperpage);}
	
	//Initialize variables
	$userlevel = $_SESSION['session_user_level'];
	if(!$review_category) {$review_category="0";} // All categories
	if(!$selectedreviews) {$selectedreviews=0;}
	if(!isset($viewmode)) {$viewmode=0;}
else {$viewmode=intval($viewmode);}

if(!F_count_rows(K_TABLE_REVIEWS)) { //if the table is void (no items) display message
	echo "<h2>".$l['m_databasempty']."</h2>";
}
else { //the table is not empty

// ---------------------------------------------------------------

if($reviewssearch OR $submitted) { // Submitting query (search results)
	if(isset($term) AND ($term != "")) {
		$wherequery = "WHERE (";
		$terms = preg_split("/[\s]+/i", addslashes($term)); // Get all the words into an array
		$size = sizeof($terms);
		//redundant check for security feature
		if($addterms != "AND"){$addterms = "OR";}
		$wherequery .= "(";
		for($i=0;$i<$size;$i++) {
			if($i>0) {$wherequery .= " ".$addterms." ";}
			$wherequery .= "((review_product_name LIKE '%$terms[$i]%')";
			$wherequery .= " OR (review_text LIKE '%$terms[$i]%')";
			$wherequery .= " OR (review_author_name LIKE '%$terms[$i]%')";
			$wherequery .= " OR (review_manuf_name LIKE '%$terms[$i]%'))";
		}
		$wherequery .= ")";
		if($review_category) {$wherequery .= " AND (review_category=".$review_category.")";}
		$wherequery .= ")";
	}
	F_show_reviews($review_category, $viewmode, $selectedreviews, $wherequery, $order_field, $orderdir, $firstrow, $rowsperpage);
} //end if($reviewssearch OR $submitted)

// ---------------------------------------------------------------
?>

<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_reviewssearch" id="form_reviewssearch">

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

<tr class="fillE">
<td class="fillEO" align="right"><?php echo $l['w_category']; ?></td>
<td class="fillEE">
<select name="review_category" id="review_category">
<?php 
if(!$review_category) {echo "<option value=\"0\" selected=\"selected\">".$l['d_all_categories']."</option>";}
else {echo "<option value=\"0\">".$l['d_all_categories']."</option>";}
$noscriptlink = $_SERVER['SCRIPT_NAME']."?";
if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {
	$noscriptlink .= "aiocp_dp=".$aiocp_dp."&amp;";
}
if (!empty($viewmode)) {$noscriptlink .= "viewmode=".$viewmode."&amp;";}
if (!empty($order_field)) {$noscriptlink .= "order_field=".urlencode($order_field)."&amp;";}
if (!empty($orderdir)) {$noscriptlink .= "orderdir=".$orderdir."&amp;";}
$noscriptlink .= "review_category=";
F_form_select_tree($review_category, false, K_TABLE_REVIEWS_CATEGORIES, "revcat", $noscriptlink); ?>
</td></tr>
</table>

</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="selectedreviews" id="selectedreviews" value="<?php echo $selectedreviews; ?>" />
<input type="hidden" name="reviewssearch" id="reviewssearch" value="" />
<input type="hidden" name="firstrow" id="firstrow" value="<?php echo $firstrow; ?>" />
<input type="hidden" name="order_field" id="order_field" value="<?php echo $order_field; ?>" />
<input type="hidden" name="orderdir" id="orderdir" value="<?php echo $orderdir; ?>" />
<input type="hidden" name="submitted" id="submitted" value="0" />
<?php F_submit_button("form_reviewssearch","reviewssearch",$l['w_search']); ?>
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
function FJ_submit_reviews_form(newfirstrow, neworder_field, neworderdir, reviewscategory, reviewsselected) {
	//document.form_reviewssearch.review_category.value=reviewscategory;
	document.form_reviewssearch.selectedreviews.value=reviewsselected;
	document.form_reviewssearch.firstrow.value=newfirstrow;
	document.form_reviewssearch.order_field.value=neworder_field;
	document.form_reviewssearch.orderdir.value=neworderdir;
	document.form_reviewssearch.submitted.value=1;
	document.form_reviewssearch.submit();
}
//]]>
</script>
<!-- END Submit form ==================== -->
<?php
} //end of function

// ----------------------------------------------------------
// read category data
// ----------------------------------------------------------
function F_get_review_category_data($categoryid) {
	global $db, $l;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	
	$sql = "SELECT * FROM ".K_TABLE_REVIEWS_CATEGORIES." WHERE revcat_id='".$categoryid."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$revcat->level = $m['revcat_level'];
			$revcat->name = $m['revcat_name'];
			$revcat->description = $m['revcat_description'];
			$revcat->image = $m['revcat_image'];
			return $revcat;
		}
	}
	else {
		F_display_db_error();
	}
	return FALSE;
}

// ------------------------------------------------------------
// Display single review
// ------------------------------------------------------------
function F_display_single_review($rid) {
	global $l, $db, $selected_language, $aiocp_dp;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	if(!F_count_rows(K_TABLE_REVIEWS)) { //if the table is void (no items) display message
		echo "<h2>".$l['m_databasempty']."</h2>";
	}
	else { //the table is not empty
		$wherequery = "WHERE review_id='".$rid."'";
		F_show_fixed_reviews("", 1, $rid, $wherequery, "", 0, 0, K_MAX_ROWS_PER_PAGE);
	} 
}

// ------------------------------------------------------------
// Show reviews without selectionform
// ------------------------------------------------------------
function F_show_fixed_reviews($review_category, $viewmode, $selectedreviews, $wherequery, $order_field, $orderdir, $firstrow, $rowsperpage) {
	global $l, $db, $selected_language, $aiocp_dp;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
?>
<!-- ====================================================== -->
<form action="<?php echo "cp_reviews.".CP_EXT.""; ?>" method="post" enctype="multipart/form-data" name="form_reviewsshow" id="form_reviewsshow">

<!-- SHOW reviews ==================== -->
<?php 
F_show_reviews($review_category, $viewmode, $selectedreviews, $wherequery, $order_field, $orderdir, $firstrow, $rowsperpage);
?>
<!-- END SHOW reviews ==================== -->
<input type="hidden" name="review_category" id="review_category" value="<?php echo $review_category; ?>" />
<input type="hidden" name="viewmode" id="viewmode" value="<?php echo $viewmode; ?>" />
<input type="hidden" name="selectedreviews" id="selectedreviews" value="<?php echo $selectedreviews; ?>" />
<input type="hidden" name="order_field" id="order_field" value="<?php echo $order_field; ?>" />
<input type="hidden" name="orderdir" id="orderdir" value="<?php echo $orderdir; ?>" />
<input type="hidden" name="firstrow" id="firstrow" value="<?php echo $firstrow; ?>" />
<input type="hidden" name="submitted" id="submitted" value="0" />
</form>
<!-- ====================================================== -->

<!-- Submit form  ==================== -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
function FJ_submit_reviews_form(newfirstrow, neworder_field, neworderdir, reviewscategory, reviewsselected) {
	document.form_reviewsshow.review_category.value=reviewscategory;
	document.form_reviewsshow.selectedreviews.value=reviewsselected;
	document.form_reviewsshow.firstrow.value=newfirstrow;
	document.form_reviewsshow.order_field.value=neworder_field;
	document.form_reviewsshow.orderdir.value=neworderdir;
	document.form_reviewsshow.submitted.value=1;
	document.form_reviewsshow.submit();
}
//]]>
</script>
<!-- END Cange focus to review_id select -->
<?php
} //end of function

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
