<?php
//============================================================+
// File name   : cp_functions_newsletter.php
// Begin       : 2001-10-19
// Last Update : 2008-02-25
// 
// Description : Functions for Newsletter
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
// Display Newsletter
// $viewmode: 0=compact(headers only); 1=full 
// $selectednewsletter = news to display in full mode while in compact mode
// ------------------------------------------------------------
function F_show_newsletter($nlmsg_nlcatid, $viewmode, $selectednewsletter, $wherequery, $order_field, $orderdir, $firstrow, $rowsperpage) {
	global $l, $db, $selected_language;
	global $term, $submitted, $newslettersearch, $addterms;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_newsletter_data.'.CP_EXT);
	require_once('../../shared/code/cp_functions_page.'.CP_EXT);
	
	//initialize variable
	$userlevel = $_SESSION['session_user_level'];
	
	if(!$firstrow) {$firstrow="0";}
	else {$firstrow=intval($firstrow);}
	if(!$rowsperpage) {$rowsperpage = K_MAX_ROWS_PER_PAGE;}
	else {$rowsperpage=intval($rowsperpage);}
	
	if(!$order_field) {$order_field = "nlmsg_sentdate DESC";}
	else {$order_field = addslashes(preg_replace("/[\s]+/i", " ", $order_field));}
	if(!$orderdir) {$orderdir=0; $nextorderdir=1; $full_order_field = $order_field;}
	else {$orderdir=1; $nextorderdir=0; $full_order_field = $order_field." DESC";}
	$full_order_field = urldecode($full_order_field);
	
	if(!F_count_rows(K_TABLE_NEWSLETTER_MESSAGES)) { //if the table is void (no items) display message
		echo "<h2>".$l['m_databasempty']."</h2>";
		return FALSE;
	}
	
	echo "<table class=\"edge\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">";
	// --- ------------------------------------------------------
	
	if (isset($nlmsg_nlcatid) AND (strlen($nlmsg_nlcatid)>0) AND ($nlmsg_nlcatid==0)) { //select all categories
		$wherequery = "WHERE 1";
	}
	
	if( (!$nlmsg_nlcatid) AND (!$wherequery) ) { // select category
		$sql = "SELECT * FROM ".K_TABLE_NEWSLETTER_CATEGORIES." WHERE (nlcat_level<=".$userlevel." AND nlcat_enabled=1 AND nlcat_all_users=0 AND nlcat_language='".$selected_language."') ORDER BY nlcat_name LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$nlmsg_nlcatid = $m['nlcat_id'];
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
	
	if($nlmsg_nlcatid) {
		$nlmsg_nlcatid = intval($nlmsg_nlcatid);
		$sqlc = "SELECT * FROM ".K_TABLE_NEWSLETTER_CATEGORIES." WHERE nlcat_id=".$nlmsg_nlcatid." AND nlcat_enabled=1  AND nlcat_all_users=0 AND nlcat_level<=".$userlevel."";
		if($rc = F_aiocpdb_query($sqlc, $db)) {
			if($mc = F_aiocpdb_fetch_array($rc)) {
				if($mc['nlcat_level']>$userlevel) {
					echo "<tr><td></td></tr></table>\n";
					F_print_error("WARNING", $l['m_authorization_deny']);
					//F_logout_form();
					return;
				}
				echo "<tr class=\"edge\">";
				echo "<th class=\"edge\" align=\"left\">";
				echo "".htmlentities($mc['nlcat_name'], ENT_NOQUOTES, $l['a_meta_charset']).":<br /><i>&nbsp;".$mc['nlcat_description']."</i>";
				echo "</th></tr>";
				
				if (!$wherequery) {$wherequery = "WHERE (nlmsg_nlcatid='".$nlmsg_nlcatid."')";}
				else {$wherequery .= " AND (nlmsg_nlcatid='".$nlmsg_nlcatid."')";}
			}
		}
		else {
			F_display_db_error();
		}
	}
	
	if (!$wherequery) {
		$sql = "SELECT * FROM ".K_TABLE_NEWSLETTER_MESSAGES." WHERE (nlmsg_sentdate>0) ORDER BY ".$full_order_field." LIMIT ".$firstrow.",".$rowsperpage."";
	}
	else {
		$sql = "SELECT * FROM ".K_TABLE_NEWSLETTER_MESSAGES." ".$wherequery." AND (nlmsg_sentdate>0) ORDER BY ".$full_order_field." LIMIT ".$firstrow.",".$rowsperpage."";
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
			if (!$nlmsg_nlcatid) {$catdata = F_get_newsletter_category_data($m['nlmsg_nlcatid']);}
			//check authorization rights
			if (($nlmsg_nlcatid) OR ($userlevel >= $catdata->level)) {
				
				if(($viewmode)OR($m['nlmsg_id'] == $selectednewsletter)) { //full mode	
					echo "<tr class=\"fill".$rowclass."\"><td class=\"fill".$rowclass."E\">";
					
					echo "<table class=\"edge\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\" style=\"width:100%\">";
					echo "<tr class=\"edge\">";
					echo "<th class=\"edge\" align=\"left\">";
					echo gmdate("Y-m-d",$m['nlmsg_sentdate']);
					echo "&nbsp;&nbsp;";
					echo "".htmlentities($m['nlmsg_title'], ENT_NOQUOTES, $l['a_meta_charset'])."";
					echo "</th></tr>";
					echo "<tr class=\"edge\">";
					echo "<td class=\"edge\">";
					echo "<table class=\"fill\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\" style=\"width:100%\">";
					
					// --- SHOW ATTACHMENTS ---
					//list of attached files in selected message
					$sqla = "SELECT * FROM ".K_TABLE_NEWSLETTER_ATTACHMENTS." WHERE nlattach_nlmsgid=".$m['nlmsg_id']." ORDER BY nlattach_file";
					if($ra = F_aiocpdb_query($sqla, $db)) {
						echo "<tr class=\"fillO\"><td class=\"fillOE\">";
						echo "<b>".$l['w_attachments'].":</b> ";
						while($ma = F_aiocpdb_fetch_array($ra)) {
							echo "<a href=\"".htmlentities(urldecode(K_PATH_FILES_ATTACHMENTS.$ma['nlattach_file']))."\" target=\"_blank\">";
							echo $ma['nlattach_file'];
							echo "</a>";
							echo ", ";
						}
						echo "</td></tr>";
					}
					else {
						F_display_db_error();
					}
					// --- END SHOW ATTACHMENTS ---
					
					echo "<tr class=\"fillE\"><td class=\"fillEE\">".$m['nlmsg_message']."</td></tr>";
					echo "</table>";
					echo "</td></tr>";
					echo "</table>";
				}
				else { //compact mode
					echo "<tr class=\"fill".$rowclass."\"><td class=\"fill".$rowclass."E\">";
					echo "<a href=\"javascript:FJ_submit_newsletters_form('".$firstrow."','".urlencode($order_field)."','0','".$m['nlmsg_nlcatid']."','".$m['nlmsg_id']."');\">".gmdate("Y-m-d",$m['nlmsg_sentdate'])." - ".htmlentities($m['nlmsg_title'], ENT_NOQUOTES, $l['a_meta_charset'])."</a>";
					
					// noscript alternative for search engines
					echo "<noscript><a href=\"cp_newsletter.".CP_EXT."?nid=".$m['nlmsg_id']."\">".gmdate("Y-m-d",$m['nlmsg_sentdate'])." - ".htmlentities($m['nlmsg_title'], ENT_NOQUOTES, $l['a_meta_charset'])."</a></noscript>";
					
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
	if ($nlmsg_nlcatid) {
		$url_request .= "&amp;nlmsg_nlcatid=".$nlmsg_nlcatid."";
	}
	if ($order_field) {
		$url_request .= "&amp;order_field=".urlencode($order_field)."";
	}
	if ($orderdir) {
		$url_request .= "&amp;orderdir=".$orderdir."";
	}
	
	echo "<br /><div align=\"center\"><a href=\"../../public/code/cp_newsletter_rss.".CP_EXT."?".$url_request."\" target=\"_blank\"><img src=\"../../pagefiles/rss.gif\" width=\"36\" height=\"14\" alt=\"RSS (XML RDF)\" border=\"0\" /></a></div><br />";
	
	// --- ------------------------------------------------------
	// --- page jump
	$sql = "SELECT count(*) AS total FROM ".K_TABLE_NEWSLETTER_MESSAGES." ".$wherequery."";
	
	if (!empty($order_field)) {$param_array = "&amp;order_field=".urlencode($order_field)."";}
	if (!empty($orderdir)) {$param_array .= "&amp;orderdir=".$orderdir."";}
	$param_array .= "&amp;submitted=1";
	if (!empty($viewmode)) {$param_array .= "&amp;viewmode=".$viewmode."";}
	if (!empty($nlcat_language)) {$param_array .= "&amp;nlcat_language=".$nlcat_language."";}
	if (!empty($viewmode)) {$param_array .= "&amp;viewmode=".$viewmode."";}
	if (!empty($nlmsg_nlcatid)) {$param_array .= "&amp;nlmsg_nlcatid=".$nlmsg_nlcatid."";}
	if (!empty($term)) {$param_array .= "&amp;term=".urlencode($term)."";}
	if (!empty($addterms)) {$param_array .= "&amp;addterms=".$addterms."";}
	if (!empty($newslettersearch)) {$param_array .= "&amp;newslettersearch=".$newslettersearch."";}
	if (!empty($selectednewsletter)) {$param_array .= "&amp;selectednewsletter=".$selectednewsletter."";}
	F_show_page_navigator($_SERVER['SCRIPT_NAME'], $aiocp_dp, $sql, $firstrow, $rowsperpage, $param_array);
	
return;
}


// ------------------------------------------------------------
// Display Newsletter in RSS 1.0 format
// ------------------------------------------------------------
function F_show_newsletter_RSS($nlmsg_nlcatid, $wherequery, $order_field, $orderdir) {
	global $l, $db, $selected_language;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_newsletter_data.'.CP_EXT);
	require_once('../../shared/code/cp_functions_page.'.CP_EXT);
	
	//initialize variable
	$userlevel = $_SESSION['session_user_level'];
	
	$RSS_HEADER = "";
	$RSS_ITEMS = "";
	
	if(!$order_field) {$order_field = "nlmsg_sentdate DESC";}
	else {$order_field = addslashes(preg_replace("/[\s]+/i", " ", $order_field));}
	if(!$orderdir) {$orderdir=0; $nextorderdir=1; $full_order_field = $order_field;}
	else {$orderdir=1; $nextorderdir=0; $full_order_field = $order_field." DESC";}
	$full_order_field = urldecode($full_order_field);
	
	if(!F_count_rows(K_TABLE_NEWSLETTER_MESSAGES)) { //if the table is void (no items) display message
		return FALSE;
	}
	
	//send page header
	$RSS_HEADER .= "<"."?xml version=\"1.0\" encoding=\"".$l['a_meta_charset']."\"?".">\n";
	
	$RSS_HEADER .= "<!DOCTYPE rdf:RDF [\n<!ENTITY % HTMLlat1 PUBLIC\n\"-//W3C//ENTITIES Latin 1 for XHTML//EN\n\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml-lat1.ent\">\n%HTMLlat1;\n]>\n";

	$RSS_HEADER .= "<rdf:RDF xmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\" xmlns=\"http://purl.org/rss/1.0/\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\">\n\n";
	
	if (isset($nlmsg_nlcatid) AND ($nlmsg_nlcatid==0)) { //select all categories
		$wherequery = "WHERE 1";
	}
	
	if( (!$nlmsg_nlcatid) AND (!$wherequery) ) { // select category
		$sql = "SELECT * FROM ".K_TABLE_NEWSLETTER_CATEGORIES." WHERE (nlcat_level<=".$userlevel." AND nlcat_enabled=1 AND nlcat_all_users=0 AND nlcat_language='".$selected_language."') ORDER BY nlcat_name LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$nlmsg_nlcatid = $m['nlcat_id'];
			}
			else {
				return FALSE; //authorization deny
			}
		}
		else {
			F_display_db_error();
		}
	}
	
	if($nlmsg_nlcatid) {
		$sqlc = "SELECT * FROM ".K_TABLE_NEWSLETTER_CATEGORIES." WHERE nlcat_id=".$nlmsg_nlcatid." AND nlcat_enabled=1  AND nlcat_all_users=0 AND nlcat_level<=".$userlevel."";
		if($rc = F_aiocpdb_query($sqlc, $db)) {
			if($mc = F_aiocpdb_fetch_array($rc)) {
				
				if (!$wherequery) {$wherequery = "WHERE (nlmsg_nlcatid='".$nlmsg_nlcatid."')";}
				else {$wherequery .= " AND (nlmsg_nlcatid='".$nlmsg_nlcatid."')";}
				
				$RSS_HEADER .= "<channel rdf:about=\"".K_PATH_PUBLIC_CODE."cp_newsletter.".CP_EXT."\">\n";
				$RSS_HEADER .= "\t<title>".htmlentities($mc['nlcat_name'], ENT_QUOTES, $l['a_meta_charset'])."</title>\n";
				$RSS_HEADER .= "\t<link>".K_PATH_PUBLIC_CODE."cp_newsletter.".CP_EXT."?nlmsg_nlcatid=".$nlmsg_nlcatid."</link>\n";
				$RSS_HEADER .= "\t<description>\n";
				$RSS_HEADER .= "\t\t".$mc['nlcat_description']."\n";
				$RSS_HEADER .= "\t</description>\n\n";	
				$RSS_HEADER .= "\t<dc:language>".$mc['nlcat_language']."</dc:language>\n";
			}
		}
		else {
			F_display_db_error();
		}
	}
	else { //various news from different categories
		$RSS_HEADER .= "<channel rdf:about=\"".K_PATH_PUBLIC_CODE."cp_newsletter.".CP_EXT."\">\n";
		$RSS_HEADER .= "\t<title>".htmlentities(K_SITE_TITLE, ENT_QUOTES, $l['a_meta_charset'])."</title>\n";
		$RSS_HEADER .= "\t<link>".K_PATH_PUBLIC_CODE."cp_newsletter.".CP_EXT."?wherequery=".urlencode($wherequery)."</link>\n";
		$RSS_HEADER .= "\t<description>\n";
		$RSS_HEADER .= "\t\tNEWSLETTERS: ".K_SITE_DESCRIPTION."\n";
		$RSS_HEADER .= "\t</description>\n\n";	
	}
	
	$RSS_HEADER .= "<items>\n";
	$RSS_HEADER .= "\t<rdf:Seq>\n";
	
	if (!$wherequery) {
		$sql = "SELECT * FROM ".K_TABLE_NEWSLETTER_MESSAGES." WHERE (nlmsg_sentdate>0) ORDER BY ".$full_order_field."";
	}
	else {
		$sql = "SELECT * FROM ".K_TABLE_NEWSLETTER_MESSAGES." ".$wherequery." AND (nlmsg_sentdate>0) ORDER BY ".$full_order_field."";
	}
	
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			
			//get category data
			if (!$nlmsg_nlcatid) {$catdata = F_get_newsletter_category_data($m['nlmsg_nlcatid']);}
			//check authorization rights
			if (($nlmsg_nlcatid) OR ($userlevel >= $catdata->level)) {
				$item_uri = "".K_PATH_PUBLIC_CODE."cp_newsletter.".CP_EXT."?nlmsg_nlcatid=".$m['nlmsg_nlcatid']."&amp;selectednewsletter=".$m['nlmsg_id']."";
				$nlmsg_date =  gmdate("Y-m-d",$m['nlmsg_sentdate']);
							
				$RSS_HEADER .= "\t\t<rdf:li resource=\"".$item_uri."\" />\n";

				$RSS_ITEMS .= "<item rdf:about=\"".$item_uri."\">\n";
				$RSS_ITEMS .= "\t<title>".htmlentities($m['nlmsg_title'], ENT_QUOTES, $l['a_meta_charset'])."</title>\n";
				$RSS_ITEMS .= "\t<link>".$item_uri."</link>\n";
				$RSS_ITEMS .= "\t<description>\n";
				$RSS_ITEMS .= "\t\t[".$nlmsg_date."] ".htmlentities(unhtmlentities($m['nlmsg_message']), ENT_QUOTES, $l['a_meta_charset'])."\n";
				
					// --- SHOW ATTACHMENTS ---
					//list of attached files in selected message
					$sqla = "SELECT * FROM ".K_TABLE_NEWSLETTER_ATTACHMENTS." WHERE nlattach_nlmsgid=".$m['nlmsg_id']." ORDER BY nlattach_file";
					if($ra = F_aiocpdb_query($sqla, $db)) {
						$RSS_ITEMS .= "\n<h3>".$l['w_attachments'].":</h3>\n<ul>\n";
						while($ma = F_aiocpdb_fetch_array($ra)) {
							$RSS_ITEMS .= "<li><a href=\"".htmlentities(urldecode(K_PATH_FILES_ATTACHMENTS.$ma['nlattach_file']))."\" target=\"_blank\">";
							$RSS_ITEMS .= $ma['nlattach_file'];
							$RSS_ITEMS .= "</a></li>\n";
						}
						$RSS_ITEMS .= "</ul>\n";
					}
					else {
						F_display_db_error();
					}
					// --- END SHOW ATTACHMENTS ---
				
				$RSS_ITEMS .= "\t</description>\n";	
				
				// http://dublincore.org/documents/dces/
				// Title Creator Subject Description Publisher Date Type Format Identifier Source Language Relation Coverage Rights
				$RSS_ITEMS .= "\t<dc:date>".$nlmsg_date."</dc:date>\n";
				$RSS_ITEMS .= "\t<dc:publisher>".htmlentities(K_SITE_TITLE, ENT_QUOTES, $l['a_meta_charset'])."</dc:publisher>\n";
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
// Display single news
// ------------------------------------------------------------
function F_display_single_newsletter($nid) {
	global $l, $db, $selected_language, $aiocp_dp;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	if(!F_count_rows(K_TABLE_NEWSLETTER_MESSAGES)) { //if the table is void (no items) display message
		echo "<h2>".$l['m_databasempty']."</h2>";
	}
	else { //the table is not empty
		$wherequery = "WHERE nlmsg_id='".$nid."'";
		F_show_fixed_newsletter("", 1, $nid, $wherequery, "", "", 0, K_MAX_ROWS_PER_PAGE);
	} 
}

// ------------------------------------------------------------
// Show select form for newsletter
// ------------------------------------------------------------
function F_show_select_newsletter($wherequery, $order_field, $orderdir, $firstrow, $rowsperpage) {
	global $l, $db, $selected_language, $aiocp_dp;
	global $nlcat_language, $changelanguage, $nlmsg_nlcatid, $viewmode, $selectednewsletter;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	
	if(!$selectednewsletter) {$selectednewsletter=0;}
	if(!isset($viewmode)) {$viewmode=0;}
else {$viewmode=intval($viewmode);}
	
// Initialize variables
$userlevel = $_SESSION['session_user_level'];

if(!$nlcat_language) {$nlcat_language = $selected_language;}

if((!$nlmsg_nlcatid) OR $changelanguage) {
	$sql = "SELECT * FROM ".K_TABLE_NEWSLETTER_CATEGORIES." WHERE (nlcat_level<=".$userlevel." AND nlcat_enabled=1 AND nlcat_all_users=0 AND nlcat_language='".$nlcat_language."') ORDER BY nlcat_name LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$nlmsg_nlcatid = $m['nlcat_id'];
		}
		else {
			$nlmsg_nlcatid = FALSE;
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
<tr class="edge">
<td class="edge">

<!-- SELECT language ==================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_newsletterlanguage" id="form_newsletterlanguage">

<table class="fill" border="0" cellspacing="2" cellpadding="1">
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_language', 'h_newslettercat_language'); ?></b></td>
<td class="fillOE">
 <select name="nlcat_language" id="nlcat_language" size="0" onchange="document.form_newsletterlanguage.submit()">
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
			if($m['language_code'] == $nlcat_language) {
				echo " selected=\"selected\"";
			}
			echo ">".htmlentities($m['language_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
			$nsl .= "<li><a href=\"".$noscriptlink."nlcat_language=".$m['language_code']."\">".htmlentities($m['language_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</a></li>";
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

<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_newslettershow" id="form_newslettershow">

<table class="fill" border="0" cellspacing="2" cellpadding="1">
<!-- SELECT category ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_category', 'h_newslettercat_select'); ?></b></td>
<td class="fillEE">
<select name="nlmsg_nlcatid" id="nlmsg_nlcatid" size="0" onchange="document.form_newslettershow.firstrow.value=0;document.form_newslettershow.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_NEWSLETTER_CATEGORIES." WHERE (nlcat_level<=".$userlevel." AND nlcat_enabled=1 AND nlcat_all_users=0 AND nlcat_language='".$nlcat_language."') ORDER BY nlcat_name";

if($r = F_aiocpdb_query($sql, $db)) {
	$nsl = "<ul>";
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['nlcat_id']."\"";
		if($m['nlcat_id'] == $nlmsg_nlcatid) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['nlcat_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
		$nsl .= "<li><a href=\"".$noscriptlink."nlmsg_nlcatid=".$m['nlcat_id']."\">".htmlentities($m['nlcat_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</a></li>";
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
<select name="viewmode" id="viewmode" size="0" onchange="document.form_newslettershow.selectednewsletter.value=''; document.form_newslettershow.submit()">
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

<input type="hidden" name="nlcat_language" id="fns_nlcat_language" value="<?php echo $nlcat_language; ?>" />
<input type="hidden" name="selectednewsletter" id="fns_selectednewsletter" value="<?php echo $selectednewsletter; ?>" />
<input type="hidden" name="firstrow" id="fns_firstrow" value="<?php echo $firstrow; ?>" />
<input type="hidden" name="order_field" id="fns_order_field" value="<?php echo $order_field; ?>" />
<input type="hidden" name="orderdir" id="fns_orderdir" value="<?php echo $orderdir; ?>" />
<input type="hidden" name="submitted" id="fns_submitted" value="0" />
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
if ($nlmsg_nlcatid) {
	F_show_newsletter($nlmsg_nlcatid, $viewmode, $selectednewsletter, $wherequery, $order_field, $orderdir, $firstrow, K_MAX_ROWS_PER_PAGE);
}
?>
<!-- END SHOW NEWS ==================== -->

<!-- ====================================================== -->
<!-- Submit form  ==================== -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
	function FJ_submit_newsletters_form(newfirstrow, neworder_field, neworderdir, newscategory, newsselected) {
		document.form_newslettershow.nlmsg_nlcatid.value=newscategory;
		document.form_newslettershow.selectednewsletter.value=newsselected;
		document.form_newslettershow.firstrow.value=newfirstrow;
		document.form_newslettershow.order_field.value=neworder_field;
		document.form_newslettershow.orderdir.value=neworderdir;
		document.form_newslettershow.submitted.value=1;
		document.form_newslettershow.submit();
	}

	document.form_newslettershow.nlmsg_nlcatid.focus();
//]]>
</script>
<!-- END Cange focus to news_id select -->
<?php
} //end of function

// ------------------------------------------------------------
// Show select form for newsletter
// ------------------------------------------------------------
function F_search_newsletter($wherequery, $order_field, $orderdir, $firstrow, $rowsperpage) {
	global $l, $db, $selected_language;
	global $term, $changelanguage, $nlmsg_nlcatid, $selectednewsletter, $submitted, $newslettersearch, $addterms;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	
	if(!$firstrow) {$firstrow="0";}
	else {$firstrow=intval($firstrow);} 
	if(!$rowsperpage) {$rowsperpage = K_MAX_ROWS_PER_PAGE;}
	else {$rowsperpage=intval($rowsperpage);}
	
//Initialize variables
$userlevel = $_SESSION['session_user_level'];
if(!$nlmsg_nlcatid) {$nlmsg_nlcatid="0";} // All categories
if(!$selectednewsletter) {$selectednewsletter=0;}
if(!isset($viewmode)) {$viewmode=0;}
else {$viewmode=intval($viewmode);}

if(!F_count_rows(K_TABLE_NEWSLETTER_MESSAGES)) { //if the table is void (no items) display message
	echo "<h2>".$l['m_databasempty']."</h2>";
}
else { //the table is not empty

// ---------------------------------------------------------------

if($newslettersearch OR $submitted) { // Submitting query (search results)
	if(isset($term) AND ($term != "")) {
		$wherequery = "WHERE (";
		$terms = preg_split("/[\s]+/i", addslashes($term)); // Get all the words into an array
		$size = sizeof($terms);
		//redundant check for security feature
		if($addterms != "AND"){$addterms = "OR";}
		$wherequery .= "(";
		for($i=0;$i<$size;$i++) {
			if($i>0) {$wherequery .= " ".$addterms." ";}
			$wherequery .= "((nlmsg_title LIKE '%$terms[$i]%')";
			$wherequery .= " OR (nlmsg_message LIKE '%$terms[$i]%'))";
		}
		$wherequery .= ")";
		if($nlmsg_nlcatid) {$wherequery .= " AND (nlmsg_nlcatid=".$nlmsg_nlcatid.")";}
		$wherequery .= ")"; // close WHERE clause
	}
	else {
		$wherequery = "";
	}
	F_show_newsletter($nlmsg_nlcatid, $viewmode, $selectednewsletter, $wherequery, $order_field, $orderdir, $firstrow, $rowsperpage);
} //end if($newslettersearch OR $submitted)

// ---------------------------------------------------------------
?>

<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_newslettersearch" id="form_newslettersearch">

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
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_category', 'h_newslettercat_select'); ?></b></td>
<td class="fillEE"><select name="nlmsg_nlcatid" id="nlmsg_nlcatid">
<?php
if(!$nlmsg_nlcatid) {echo "<option value=\"0\" selected=\"selected\">".$l['d_all_categories']."</option>";}
else {echo "<option value=\"0\">".$l['d_all_categories']."</option>";}

$sql = "SELECT * FROM ".K_TABLE_NEWSLETTER_CATEGORIES." WHERE (nlcat_level<=".$userlevel." AND nlcat_enabled=1 AND nlcat_all_users=0 AND nlcat_language='".$selected_language."')  ORDER BY nlcat_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['nlcat_id']."\"";
		if($m['nlcat_id'] == $nlmsg_nlcatid) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['nlcat_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
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
<input type="hidden" name="selectednewsletter" id="selectednewsletter" value="<?php echo $selectednewsletter; ?>" />
<input type="hidden" name="newslettersearch" id="newslettersearch" value="" />
<input type="hidden" name="firstrow" id="firstrow" value="<?php echo $firstrow; ?>" />
<input type="hidden" name="order_field" id="order_field" value="<?php echo $order_field; ?>" />
<input type="hidden" name="orderdir" id="orderdir" value="<?php echo $orderdir; ?>" />
<input type="hidden" name="submitted" id="submitted" value="0" />
<?php F_submit_button("form_newslettersearch","newslettersearch",$l['w_search']); ?>
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
	function FJ_submit_newsletters_form(newfirstrow, neworder_field, neworderdir, newscategory, newsselected) {
		//document.form_newslettersearch.nlmsg_nlcatid.value=newscategory;
		document.form_newslettersearch.selectednewsletter.value=newsselected;
		document.form_newslettersearch.firstrow.value=newfirstrow;
		document.form_newslettersearch.order_field.value=neworder_field;
		document.form_newslettersearch.orderdir.value=neworderdir;
		document.form_newslettersearch.submitted.value=1;
		document.form_newslettersearch.submit();
	}
//]]>
</script>
<!-- END Submit form ==================== -->
<?php
} //end of function


// ------------------------------------------------------------
// Show newsletter without selector
// ------------------------------------------------------------
function F_show_fixed_newsletter($nlmsg_nlcatid, $viewmode, $selectednewsletter, $wherequery, $order_field, $orderdir, $firstrow, $rowsperpage) {
	global $l, $db, $selected_language, $aiocp_dp;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
?>

<form action="<?php echo "newsletter.".CP_EXT.""; ?>" method="post" enctype="multipart/form-data" name="form_newslettershow" id="form_newslettershow">
<input type="hidden" name="nlcat_language" id="nlcat_language" value="<?php echo $selected_language; ?>" />
<input type="hidden" name="viewmode" id="viewmode" value="<?php echo $viewmode; ?>" />
<input type="hidden" name="nlmsg_nlcatid" id="nlmsg_nlcatid" value="<?php echo $nlmsg_nlcatid; ?>" />
<input type="hidden" name="selectednewsletter" id="selectednewsletter" value="<?php echo $selectednewsletter; ?>" />
<input type="hidden" name="firstrow" id="firstrow" value="<?php echo $firstrow; ?>" />
<input type="hidden" name="order_field" id="order_field" value="<?php echo $order_field; ?>" />
<input type="hidden" name="orderdir" id="orderdir" value="<?php echo $orderdir; ?>" />
<input type="hidden" name="submitted" id="submitted" value="0" />
</form>

<!-- SHOW NEWS ==================== -->
<?php 
F_show_newsletter($nlmsg_nlcatid, $viewmode, $selectednewsletter, $wherequery, $order_field, $orderdir, $firstrow, $rowsperpage);
?>
<!-- END SHOW NEWS ==================== -->

<!-- ====================================================== -->
<!-- Submit form  ==================== -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
	function FJ_submit_newsletters_form(newfirstrow, neworder_field, neworderdir, newscategory, newsselected) {
		document.form_newslettershow.nlmsg_nlcatid.value=newscategory;
		document.form_newslettershow.selectednewsletter.value=newsselected;
		document.form_newslettershow.firstrow.value=newfirstrow;
		document.form_newslettershow.order_field.value=neworder_field;
		document.form_newslettershow.orderdir.value=neworderdir;
		document.form_newslettershow.submitted.value=1;
		document.form_newslettershow.submit();
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
