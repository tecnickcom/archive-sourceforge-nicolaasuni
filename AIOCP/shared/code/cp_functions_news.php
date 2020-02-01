<?php
//============================================================+
// File name   : cp_functions_news.php
// Begin       : 2001-09-20
// Last Update : 2008-02-25
// 
// Description : Functions for news
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
// Display News
// $viewmode: 0=compact(headers only); 1=full 
// $selectednews = news to display in full mode while in compact mode
// ------------------------------------------------------------
function F_show_news($news_category, $viewmode, $selectednews, $wherequery, $order_field, $orderdir, $firstrow, $rowsperpage) {
	global $l, $db, $selected_language;
	global $term, $submitted, $newssearch, $addterms;
	
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
	
	if(!$order_field) {$order_field = "news_date DESC";}
	else {$order_field = addslashes($order_field);}
	if(!$orderdir) {$orderdir=0; $nextorderdir=1; $full_order_field = $order_field;}
	else {$orderdir=1; $nextorderdir=0; $full_order_field = $order_field." DESC";}
	$full_order_field = urldecode($full_order_field);
	
	if(!F_count_rows(K_TABLE_NEWS)) { //if the table is void (no items) display message
		echo "<h2>".$l['m_databasempty']."</h2>";
		return FALSE;
	}
	
	echo "<table class=\"edge\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">";
	
	// --- ------------------------------------------------------
	
	if (isset($news_category) AND (strlen($news_category)>0) AND ($news_category==0)) { //select all categories
		$wherequery = "WHERE 1";
	}
	
	if( (!$news_category) AND (!$wherequery) ) {
		$sql = "SELECT * FROM ".K_TABLE_NEWS_CATEGORIES." WHERE (newscat_level<=".$userlevel." AND newscat_language='".$selected_language."') ORDER BY newscat_name LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$news_category = $m['newscat_id'];
			}
			else {
				echo "<tr><td>&nbsp;</td></tr></table>\n";
				F_print_error("WARNING", $l['m_authorization_deny']);
				//F_logout_form();
				return;
			}
		}
		else {
			F_display_db_error();
		}
	}

	if($news_category) {
		$news_category = intval($news_category);
		$sqlc = "SELECT * FROM ".K_TABLE_NEWS_CATEGORIES." WHERE newscat_id=".$news_category."";
		if($rc = F_aiocpdb_query($sqlc, $db)) {
			if($mc = F_aiocpdb_fetch_array($rc)) {
				if($mc['newscat_level']>$userlevel) {
					echo "<tr><td>&nbsp;</td></tr></table>\n";
					F_print_error("WARNING", $l['m_authorization_deny']);
					//F_logout_form();
					return;
				}
				
				echo "<tr class=\"edge\">";
				echo "<th class=\"edge\" align=\"left\">";
				echo F_evaluate_modules($mc['newscat_description']);
				echo "</th></tr>";
				
				if (!$wherequery) {$wherequery = "WHERE (news_category='".$news_category."')";}
				else {$wherequery .= " AND (news_category='".$news_category."')";}
			}
		}
		else {
			F_display_db_error();
		}
	}
	
	if (!$wherequery) {
		$sql = "SELECT * FROM ".K_TABLE_NEWS." ORDER BY ".$full_order_field." LIMIT ".$firstrow.",".$rowsperpage."";
	}
	else {
		$sql = "SELECT * FROM ".K_TABLE_NEWS." ".$wherequery." ORDER BY ".$full_order_field." LIMIT ".$firstrow.",".$rowsperpage."";
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
			if (!$news_category) {$catdata = F_get_news_category_data($m['news_category']);}
			//check authorization rights
			if (($news_category) OR ($userlevel >= $catdata->level)) {
				
				if(($viewmode)OR($m['news_id'] == $selectednews)) { //full mode	
					echo "<tr class=\"fill".$rowclass."\"><td class=\"fill".$rowclass."E\">";
					
					echo "<table class=\"edge\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\" style=\"width:100%\">";
					echo "<tr class=\"edge\">";
					echo "<th class=\"edge\" align=\"left\">";
					echo "".htmlentities($m['news_title'], ENT_NOQUOTES, $l['a_meta_charset'])."";
					echo "</th></tr>";
					echo "<tr class=\"edge\">";
					echo "<td class=\"edge\">";
					echo "<table class=\"fill\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\" style=\"width:100%\">";
					
					echo "<tr class=\"fillO\">";
					echo "<td class=\"fillOE\">";
					echo $m['news_date'];
					echo "&nbsp;&nbsp;&brvbar;&nbsp;&nbsp;";
					if($m['news_author_name']) {
						echo $l['w_author'].": ";
						if($m['news_author_email']) {echo "<a href=\"mailto:".$m['news_author_email']."\">";}
						echo "".htmlentities($m['news_author_name'], ENT_NOQUOTES, $l['a_meta_charset'])."";
						if($m['news_author_email']) {echo "</a>";}
					}
					echo "&nbsp;&nbsp;&brvbar;&nbsp;&nbsp;";
					if($m['news_source_name']) {
						echo $l['w_source'].": ";
						if($m['news_source_link']) {echo "<a href=\"".htmlentities(urldecode($m['news_source_link']))."\" target=\"_blank\">";}
						echo "".htmlentities($m['news_source_name'], ENT_NOQUOTES, $l['a_meta_charset'])."";
						if($m['news_source_link']) {echo "</a>";}
					}
					echo "</td>";
					echo "</tr>";
					echo "<tr class=\"fillE\"><td class=\"fillEE\">".F_evaluate_modules($m['news_text'])."</td></tr>";
					echo "</table>";
					echo "</td></tr></table>";
					echo "</td></tr>";
				}
				else { //compact mode
					echo "<tr class=\"fill".$rowclass."\"><td class=\"fill".$rowclass."E\">";
					echo "<a href=\"javascript:FJ_submit_news_form('".$firstrow."','".urlencode($order_field)."','0','".$m['news_category']."','".$m['news_id']."');\">".htmlentities($m['news_title'], ENT_NOQUOTES, $l['a_meta_charset'])."</a>";
					
					// noscript alternative for search engines
					echo "<noscript><a href=\"cp_news.".CP_EXT."?nid=".$m['news_id']."\">".htmlentities($m['news_title'], ENT_NOQUOTES, $l['a_meta_charset'])."</a></noscript>";
					
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
	if ($news_category) {
		$url_request .= "&amp;news_category=".$news_category."";
	}
	if ($order_field) {
		$url_request .= "&amp;order_field=".$order_field."";
	}
	if ($orderdir) {
		$url_request .= "&amp;orderdir=".$orderdir."";
	}
	
	echo "<br /><div align=\"center\"><a href=\"../../public/code/cp_news_rss.".CP_EXT."?".urlencode($url_request)."\" target=\"_blank\"><img src=\"../../pagefiles/rss.gif\" width=\"36\" height=\"14\" alt=\"RSS (XML RDF)\" border=\"0\" /></a></div><br />";
	
	// --- ------------------------------------------------------
	// --- page jump
	$sql = "SELECT count(*) AS total FROM ".K_TABLE_NEWS." ".$wherequery."";
	if (!empty($order_field)) {$param_array = "&amp;order_field=".urlencode($order_field)."";}
	if (!empty($orderdir)) {$param_array .= "&amp;orderdir=".$orderdir."";}
	$param_array .= "&amp;submitted=1";
	if (!empty($newscat_language)) {$param_array .= "&amp;newscat_language=".$newscat_language."";}
	if (!empty($news_category)) {$param_array .= "&amp;news_category=".$news_category."";}
	if (!empty($viewmode)) {$param_array .= "&amp;viewmode=".$viewmode."";}
	if (!empty($term)) {$param_array .= "&amp;term=".urlencode($term)."";}
	if (!empty($addterms)) {$param_array .= "&amp;addterms=".$addterms."";}
	if (!empty($newssearch)) {$param_array .= "&amp;newssearch=".$newssearch."";}
	F_show_page_navigator($_SERVER['SCRIPT_NAME'], $aiocp_dp, $sql, $firstrow, $rowsperpage, $param_array);
	
	return;
}

// ------------------------------------------------------------
// Display News in RSS 1.0 format
// ------------------------------------------------------------
function F_show_news_RSS($news_category, $wherequery, $order_field, $orderdir) {
	global $l, $db, $selected_language;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_page.'.CP_EXT);
	
	//initialize variable
	$userlevel = $_SESSION['session_user_level'];
	
	$RSS_HEADER = "";
	$RSS_ITEMS = "";
	
	if(!$order_field) {$order_field = "news_date DESC";}
	else {$order_field = addslashes(preg_replace("/[\s]+/i", " ", $order_field));}
	if(!$orderdir) {$orderdir=0; $nextorderdir=1; $full_order_field = $order_field;}
	else {$orderdir=1; $nextorderdir=0; $full_order_field = $order_field." DESC";}
	$full_order_field = urldecode($full_order_field);
	
	if(!F_count_rows(K_TABLE_NEWS)) { //if the table is void (no items) display message
		return FALSE;
	}
	
	//send page header
	$RSS_HEADER .= "<"."?xml version=\"1.0\" encoding=\"".$l['a_meta_charset']."\"?".">\n";
	
	$RSS_HEADER .= "<!DOCTYPE rdf:RDF [\n<!ENTITY % HTMLlat1 PUBLIC\n\"-//W3C//ENTITIES Latin 1 for XHTML//EN\n\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml-lat1.ent\">\n%HTMLlat1;\n]>\n";

	$RSS_HEADER .= "<rdf:RDF xmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\" xmlns=\"http://purl.org/rss/1.0/\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\">\n\n";
	
	// --- ------------------------------------------------------
	
	if (isset($news_category) AND ($news_category==0)) { //select all categories
		$wherequery = "WHERE 1";
	}
	
	if( (!$news_category) AND (!$wherequery) ) {
		$sql = "SELECT * FROM ".K_TABLE_NEWS_CATEGORIES." WHERE (newscat_level<=".$userlevel." AND newscat_language='".$selected_language."') ORDER BY newscat_name LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$news_category = $m['newscat_id'];
			}
			else {
				return FALSE; //authorization deny
			}
		}
		else {
			F_display_db_error();
		}
	}

	if($news_category) {
		$sqlc = "SELECT * FROM ".K_TABLE_NEWS_CATEGORIES." WHERE newscat_id=".$news_category."";
		if($rc = F_aiocpdb_query($sqlc, $db)) {
			if($mc = F_aiocpdb_fetch_array($rc)) {
				if($mc['newscat_level']>$userlevel) {
					return FALSE; //authorization deny
				}
				
				if (!$wherequery) {$wherequery = "WHERE (news_category='".$news_category."')";}
				else {$wherequery .= " AND (news_category='".$news_category."')";}
				
				$RSS_HEADER .= "<channel rdf:about=\"".K_PATH_PUBLIC_CODE."cp_news.".CP_EXT."\">\n";
				$RSS_HEADER .= "\t<title>".htmlentities($mc['newscat_name'], ENT_QUOTES, $l['a_meta_charset'])."</title>\n";
				$RSS_HEADER .= "\t<link>".K_PATH_PUBLIC_CODE."cp_news.".CP_EXT."?news_category=".$news_category."</link>\n";
				$RSS_HEADER .= "\t<description>\n";
				$RSS_HEADER .= "\t\t".$mc['newscat_description']."\n";
				$RSS_HEADER .= "\t</description>\n\n";	
				$RSS_HEADER .= "\t<dc:language>".$mc['newscat_language']."</dc:language>\n";
	
			}
		}
		else {
			F_display_db_error();
		}
	}
	else { //various news from different categories
		$RSS_HEADER .= "<channel rdf:about=\"".K_PATH_PUBLIC_CODE."cp_news.".CP_EXT."\">\n";
		$RSS_HEADER .= "\t<title>".htmlentities(K_SITE_TITLE, ENT_QUOTES, $l['a_meta_charset'])."</title>\n";
		$RSS_HEADER .= "\t<link>".K_PATH_PUBLIC_CODE."cp_news.".CP_EXT."?wherequery=".urlencode($wherequery)."</link>\n";
		$RSS_HEADER .= "\t<description>\n";
		$RSS_HEADER .= "\t\tNEWS: ".K_SITE_DESCRIPTION."\n";
		$RSS_HEADER .= "\t</description>\n\n";	
	}
	
	$RSS_HEADER .= "<items>\n";
	$RSS_HEADER .= "\t<rdf:Seq>\n";

	if (!$wherequery) {
		$sql = "SELECT * FROM ".K_TABLE_NEWS." ORDER BY ".$full_order_field."";
	}
	else {
		$sql = "SELECT * FROM ".K_TABLE_NEWS." ".$wherequery." ORDER BY ".$full_order_field."";
	}
	
	if($r = F_aiocpdb_query($sql, $db)) {;
		while($m = F_aiocpdb_fetch_array($r)) {
			
			//get category data
			if (!$news_category) {
				$catdata = F_get_news_category_data($m['news_category']);	
			}
			//check authorization rights
			if (($news_category) OR ($userlevel >= $catdata->level)) {
				$item_uri = "".K_PATH_PUBLIC_CODE."cp_news.".CP_EXT."?news_category=".$m['news_category']."&amp;selectednews=".$m['news_id']."";
							
				$RSS_HEADER .= "\t\t<rdf:li resource=\"".$item_uri."\" />\n";

				$RSS_ITEMS .= "<item rdf:about=\"".$item_uri."\">\n";
				$RSS_ITEMS .= "\t<title>".htmlentities($m['news_title'], ENT_QUOTES, $l['a_meta_charset'])."</title>\n";
				$RSS_ITEMS .= "\t<link>".htmlentities(urldecode($m['news_source_link']))."</link>\n";
				$RSS_ITEMS .= "\t<description>\n";
				$RSS_ITEMS .= "\t\t[".$m['news_date']."] ".htmlentities(unhtmlentities($m['news_text']), ENT_QUOTES, $l['a_meta_charset'])."\n";
				$RSS_ITEMS .= "\t</description>\n";
				
				// http://dublincore.org/documents/dces/
				// Title Creator Subject Description Publisher Date Type Format Identifier Source Language Relation Coverage Rights
				$RSS_ITEMS .= "\t<dc:date>".$m['news_date']."</dc:date>\n";
				$RSS_ITEMS .= "\t<dc:creator>".htmlentities($m['news_author_name'], ENT_QUOTES, $l['a_meta_charset'])." [".$m['news_author_email']."]</dc:creator>\n";
				$RSS_ITEMS .= "\t<dc:source>".htmlentities($m['news_source_name'], ENT_QUOTES, $l['a_meta_charset'])." [".htmlentities(urldecode($m['news_source_link']))."]</dc:source>\n";
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
// Show select form for news
// ------------------------------------------------------------
function F_show_select_news($wherequery, $order_field, $orderdir, $firstrow, $rowsperpage) {
	global $l, $db, $selected_language, $aiocp_dp;
	global $newscat_language, $changelanguage, $news_category, $viewmode, $selectednews;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	
	if(!$selectednews) {$selectednews=0;}
	if(!isset($viewmode)) {$viewmode=0;}
else {$viewmode=intval($viewmode);}
	
// Initialize variables
$userlevel = $_SESSION['session_user_level'];

if(!$newscat_language) {$newscat_language = $selected_language;}

if((!isset($news_category)) OR $changelanguage) {
	$sql = "SELECT * FROM ".K_TABLE_NEWS_CATEGORIES." WHERE (newscat_level<=".$userlevel." AND newscat_language='".$newscat_language."') ORDER BY newscat_name LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$news_category = $m['newscat_id'];
		}
		else {
			$news_category = FALSE;
		}
	}
	else {
		F_display_db_error();
	}
}
?>
<!-- ====================================================== -->

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<?php
//display language selector only if enabled languages are more than one
if (F_count_rows(K_TABLE_LANGUAGE_CODES, "WHERE language_enabled=1") > 1) {
?>
<!-- SELECT language ==================== -->

<tr class="edge">
<td class="edge">
<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_newslanguage" id="form_newslanguage">

<table class="fill" border="0" cellspacing="2" cellpadding="1">
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_language', 'h_newscat_language'); ?></b></td>
<td class="fillOE">

<select name="newscat_language" id="newscat_language" size="0" onchange="document.form_newslanguage.submit()">
<?php
	$noscriptlink = $_SERVER['SCRIPT_NAME']."?";
	if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {
		$noscriptlink .= "aiocp_dp=".$aiocp_dp."&amp;";
	}
	
	$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		$nsl = "<ul>"; //alternative noscript links (used by search engines)
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"".$m['language_code']."\"";
			if($m['language_code'] == $newscat_language) {
				echo " selected=\"selected\"";
			}
			echo ">".htmlentities($m['language_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
			$nsl .= "<li><a href=\"".$noscriptlink."newscat_language=".$m['language_code']."\">".htmlentities($m['language_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</a></li>";
		}
		$nsl .= "</ul>";
	}
	else {
		F_display_db_error();
	}
?>
</select>
<noscript><?php echo $nsl; ?></noscript>
<input type="hidden" name="changelanguage" id="changelanguage" value="1" />
</td>
</tr>
</table>
</form>
</td></tr>
<!-- END SELECT language ==================== -->
<?php
}
?>

<tr class="edge">
<td class="edge">

<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_newsshow" id="form_newsshow">

<table class="fill" border="0" cellspacing="2" cellpadding="1">
<!-- SELECT category ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_category', 'h_newscat_select'); ?></b></td>
<td class="fillEE">
<select name="news_category" id="news_category" size="0" onchange="document.form_newsshow.firstrow=0;document.form_newsshow.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_NEWS_CATEGORIES." WHERE (newscat_level<=".$userlevel." AND newscat_language='".$newscat_language."') ORDER BY newscat_name";

if($r = F_aiocpdb_query($sql, $db)) {
	$nsl = "<ul>";
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['newscat_id']."\"";
		if($m['newscat_id'] == $news_category) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['newscat_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
		$nsl .= "<li><a href=\"".$noscriptlink."news_category=".$m['newscat_id']."\">".htmlentities($m['newscat_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</a></li>";
	}
	$nsl .= "</ul>";
}
else {
	F_display_db_error();
}
?>
</select>
<noscript><?php echo $nsl; ?></noscript>
</td>
</tr>
<!-- END SELECT category ==================== -->

<!-- SELECT view mode ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_mode', 'h_list_mode'); ?></b></td>
<td class="fillOE">
<select name="viewmode" id="viewmode" size="0" onchange="document.form_newsshow.selectednews.value=''; document.form_newsshow.submit()">
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

<input type="hidden" name="newscat_language" id="fsn_newscat_language" value="<?php echo $newscat_language; ?>" />
<input type="hidden" name="selectednews" id="fsn_selectednews" value="<?php echo $selectednews; ?>" />
<input type="hidden" name="order_field" id="fsn_order_field" value="<?php echo $order_field; ?>" />
<input type="hidden" name="orderdir" id="fsn_orderdir" value="<?php echo $orderdir; ?>" />
<input type="hidden" name="firstrow" id="fsn_firstrow" value="<?php echo $firstrow; ?>" />
<input type="hidden" name="submitted" id="fsn_submitted" value="0" />
</td>
</tr>
<!-- END view mode ==================== -->
</table>

</form>

</td>
</tr>
</table>
<br />
<!-- SHOW NEWS ==================== -->
<?php 
if ($news_category) {
	F_show_news($news_category, $viewmode, $selectednews, $wherequery, $order_field, $orderdir, $firstrow, $rowsperpage);
}
?>
<!-- END SHOW NEWS ==================== -->

<!-- ====================================================== -->

<!-- Submit form  ==================== -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
function FJ_submit_news_form(newfirstrow, neworder_field, neworderdir, newscategory, newsselected) {
	document.form_newsshow.news_category.value=newscategory;
	document.form_newsshow.selectednews.value=newsselected;
	document.form_newsshow.order_field.value=neworder_field;
	document.form_newsshow.orderdir.value=neworderdir;
	document.form_newsshow.firstrow.value=newfirstrow;
	document.form_newsshow.submitted.value=1;
	document.form_newsshow.submit();
}

document.form_newsshow.news_category.focus();
//]]>
</script>
<!-- END Cange focus to news_id select -->
<?php
} //end of function

// ------------------------------------------------------------
// Search form for news
// ------------------------------------------------------------
function F_search_news($wherequery, $order_field, $orderdir, $firstrow, $rowsperpage) {
	global $l, $db, $selected_language, $aiocp_dp;
	global $term, $submitted, $news_category, $newssearch,  $selectednews, $addterms;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	
	if(!$firstrow) {$firstrow="0";}
	else {$firstrow=intval($firstrow);} 
	if(!$rowsperpage) {$rowsperpage = K_MAX_ROWS_PER_PAGE;}
	else {$rowsperpage=intval($rowsperpage);}
	
	//Initialize variables
	$userlevel = $_SESSION['session_user_level'];
	if(!$news_category) {$news_category="0";} // All categories
	if(!$selectednews) {$selectednews=0;}
	if(!isset($viewmode)) {$viewmode=0;}
else {$viewmode=intval($viewmode);}

if(!F_count_rows(K_TABLE_NEWS)) { //if the table is void (no items) display message
	echo "<h2>".$l['m_databasempty']."</h2>";
}
else { //the table is not empty

// ---------------------------------------------------------------

if($newssearch OR $submitted) { // Submitting query (search results)
	if(isset($term) AND ($term != "")) {
		$wherequery = "WHERE (";
		$terms = preg_split("/[\s]+/i", addslashes($term)); // Get all the words into an array
		$size = sizeof($terms);
		//redundant check for security feature
		if($addterms != "AND"){$addterms = "OR";}
		$wherequery .= "(";
		for($i=0;$i<$size;$i++) {
			if($i>0) {$wherequery .= " ".$addterms." ";}
			$wherequery .= "((news_title LIKE '%$terms[$i]%')";
			$wherequery .= " OR (news_text LIKE '%$terms[$i]%')";
			$wherequery .= " OR (news_author_name LIKE '%$terms[$i]%')";
			$wherequery .= " OR (news_source_name LIKE '%$terms[$i]%'))";
		}
		$wherequery .= ")";
		if($news_category) {$wherequery .= " AND (news_category=".$news_category.")";}
		$wherequery .= ")"; // close WHERE clause
	}
	
	F_show_news($news_category, $viewmode, $selectednews, $wherequery, $order_field, $orderdir, $firstrow, $rowsperpage);
} //end if($newssearch OR $submitted)

// ---------------------------------------------------------------
?>

<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_newssearch" id="form_newssearch">
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
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_category', 'h_newscat_select'); ?></b></td>
<td class="fillEE"><select name="news_category" id="news_category">
<?php
if(!$news_category) {echo "<option value=\"0\" selected=\"selected\">".$l['d_all_categories']."</option>";}
else {echo "<option value=\"0\">".$l['d_all_categories']."</option>";}
$sql = "SELECT * FROM ".K_TABLE_NEWS_CATEGORIES." WHERE (newscat_level<=".$userlevel." AND newscat_language='".$selected_language."') ORDER BY newscat_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['newscat_id']."\"";
		if($m['newscat_id'] == $news_category) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['newscat_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td></tr>

</table>

</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="selectednews" id="selectednews" value="<?php echo $selectednews; ?>" />
<input type="hidden" name="newssearch" id="newssearch" value="" />
<input type="hidden" name="order_field" id="order_field" value="<?php echo $order_field; ?>" />
<input type="hidden" name="orderdir" id="orderdir" value="<?php echo $orderdir; ?>" />
<input type="hidden" name="firstrow" id="firstrow" value="<?php echo $firstrow; ?>" />
<input type="hidden" name="submitted" id="submitted" value="0" />
<?php F_submit_button("form_newssearch","newssearch",$l['w_search']); ?>
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
function FJ_submit_news_form(newfirstrow, neworder_field, neworderdir, newscategory, newsselected) {
	//document.form_newssearch.news_category.value=newscategory;
	document.form_newssearch.selectednews.value=newsselected;
	document.form_newssearch.order_field.value=neworder_field;
	document.form_newssearch.orderdir.value=neworderdir;
	document.form_newssearch.firstrow.value=newfirstrow;
	document.form_newssearch.submitted.value=1;
	document.form_newssearch.submit();
}
//]]>
</script>
<!-- END Submit form ==================== -->
<?php
} //end of function

// ----------------------------------------------------------
// read category data
// ----------------------------------------------------------
function F_get_news_category_data($categoryid) {
	global $db, $l;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	
	$sql = "SELECT * FROM ".K_TABLE_NEWS_CATEGORIES." WHERE newscat_id='".$categoryid."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$newscat->language = $m['newscat_language'];
			$newscat->level = $m['newscat_level'];
			$newscat->name = $m['newscat_name'];
			$newscat->description = $m['newscat_description'];
			return $newscat;
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
function F_display_single_news($nid) {
	global $l, $db, $selected_language, $aiocp_dp;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	if(!F_count_rows(K_TABLE_NEWS)) { //if the table is void (no items) display message
		echo "<h2>".$l['m_databasempty']."</h2>";
	}
	else { //the table is not empty
		$wherequery = "WHERE news_id='".$nid."'";
		F_show_fixed_news("", 1, $nid, $wherequery, "", "", 0, K_MAX_ROWS_PER_PAGE);
	} 
}

// ------------------------------------------------------------
// Show news without selectionform
// ------------------------------------------------------------
function F_show_fixed_news($news_category, $viewmode, $selectednews, $wherequery, $order_field, $orderdir, $firstrow, $rowsperpage) {
	global $l, $db, $selected_language, $aiocp_dp;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	
?>

<form action="<?php echo "cp_news.".CP_EXT.""; ?>" method="post" enctype="multipart/form-data" name="form_newsshow" id="form_newsshow">
<input type="hidden" name="newscat_language" id="newscat_language" value="<?php echo $selected_language; ?>" />
<input type="hidden" name="news_category" id="news_category" value="<?php echo $news_category; ?>" />
<input type="hidden" name="viewmode" id="viewmode" value="<?php echo $viewmode; ?>" />
<input type="hidden" name="selectednews" id="selectednews" value="<?php echo $selectednews; ?>" />
<input type="hidden" name="order_field" id="order_field" value="<?php echo $order_field; ?>" />
<input type="hidden" name="orderdir" id="orderdir" value="<?php echo $orderdir; ?>" />
<input type="hidden" name="firstrow" id="firstrow" value="<?php echo $firstrow; ?>" />
<input type="hidden" name="submitted" id="submitted" value="0" />
</form>

<!-- SHOW NEWS ==================== -->
<?php 
F_show_news($news_category, $viewmode, $selectednews, $wherequery, $order_field, $orderdir, $firstrow, $rowsperpage);
?>
<!-- END SHOW NEWS ==================== -->

<!-- ====================================================== -->

<!-- Submit form  ==================== -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
function FJ_submit_news_form(newfirstrow, neworder_field, neworderdir, newscategory, newsselected) {
	document.form_newsshow.news_category.value=newscategory;
	document.form_newsshow.selectednews.value=newsselected;
	document.form_newsshow.order_field.value=neworder_field;
	document.form_newsshow.orderdir.value=neworderdir;
	document.form_newsshow.firstrow.value=newfirstrow;
	document.form_newsshow.submitted.value=1;
	document.form_newsshow.submit();
}
//]]>
</script>
<!-- END Cange focus to news_id select -->
<?php
} //end of function
//============================================================+
// END OF FILE                                                 
//============================================================+
?>
