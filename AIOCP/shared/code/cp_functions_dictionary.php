<?php
//============================================================+
// File name   : cp_functions_dictionary.php
// Begin       : 2003-10-14
// Last Update : 2008-02-25
// 
// Description : Functions for dictionary
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
// Display Dictionary
// $viewmode: 0=compact(headers only); 1=full 
// $selectedword = news to display in full mode while in compact mode
// ------------------------------------------------------------
function F_show_dictionary_words($dicword_category_id, $viewmode, $selectedword, $wherequery, $order_field, $orderdir, $firstrow, $rowsperpage) {
	global $l, $db, $selected_language, $aiocp_dp;
	global $selectedletter;
	global $term, $submitted, $wordsearch, $addterms;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_page.'.CP_EXT);
	require_once('../../shared/code/cp_functions_forum.'.CP_EXT);
	require_once('../../shared/code/cp_functions_dynamic_pages.'.CP_EXT);
	
	//initialize variable
	$userlevel = $_SESSION['session_user_level'];
	
	if(!$firstrow) {$firstrow="0";}
	else {$firstrow=intval($firstrow);} 
	if(!$rowsperpage) {$rowsperpage = K_MAX_ROWS_PER_PAGE;}
	else {$rowsperpage=intval($rowsperpage);}
	if(!isset($selectedletter)) {$selectedletter = '';}
	
	if(!$order_field) {$order_field = "dicword_name";}
	else {$order_field = addslashes(preg_replace("/[\s]+/i", " ", $order_field));}
	if(!$orderdir) {$orderdir=0; $nextorderdir=1; $full_order_field = $order_field;}
	else {$orderdir=1; $nextorderdir=0; $full_order_field = $order_field." DESC";}
	$full_order_field = urldecode($full_order_field);
	
	if(!F_count_rows(K_TABLE_DICTIONARY_WORDS)) { //if the table is void (no items) display message
		echo "<h2>".$l['m_databasempty']."</h2>";
		return FALSE;
	}
	
	// display alphabetical selector
	
	echo "<table class=\"edge\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">";
	
	// --- ------------------------------------------------------
	
	if (isset($dicword_category_id) AND (strlen($dicword_category_id)>0) AND ($dicword_category_id==0)) { //select all categories
		$wherequery = "WHERE 1";
	}
	
	if( (!$dicword_category_id) AND (!$wherequery) ) {
		$sql = "SELECT * FROM ".K_TABLE_DICTIONARY_CATEGORIES." WHERE (diccat_level<=".$userlevel." AND diccat_language='".$selected_language."') ORDER BY diccat_name LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$dicword_category_id = $m['diccat_id'];
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

	if($dicword_category_id) {
		$sqlc = "SELECT * FROM ".K_TABLE_DICTIONARY_CATEGORIES." WHERE diccat_id=".$dicword_category_id."";
		if($rc = F_aiocpdb_query($sqlc, $db)) {
			if($mc = F_aiocpdb_fetch_array($rc)) {
				if($mc['diccat_level']>$userlevel) {
					echo "<tr><td></td></tr></table>\n";
					F_print_error("WARNING", $l['m_authorization_deny']);
					//F_logout_form();
					return;
				}
				
				echo "<tr class=\"edge\">";
				echo "<th class=\"edge\" align=\"left\">";
				//echo "".htmlentities($mc['diccat_name'], ENT_NOQUOTES, $l['a_meta_charset']).":<br />\n";
				echo F_evaluate_modules($mc['diccat_description']);
				echo "</th></tr>";
				
				if (!$wherequery) {$wherequery = "WHERE (dicword_category_id='".$dicword_category_id."')";}
				else {$wherequery .= " AND (dicword_category_id='".$dicword_category_id."')";}
				$wherequery .= " AND (dicword_name LIKE '".$selectedletter."%')";
			}
		}
		else {
			F_display_db_error();
		}
	}
	
	if (!$wherequery) {$wherequery = "WHERE (dicword_name LIKE '".$selectedletter."%')";}
	else {$wherequery .= " AND (dicword_name LIKE '".$selectedletter."%')";}
	
	$sql = "SELECT * FROM ".K_TABLE_DICTIONARY_WORDS." ".$wherequery." ORDER BY ".$full_order_field." LIMIT ".$firstrow.",".$rowsperpage."";
	
	// get category data
	if ($dicword_category_id) {
		$catdata = F_get_dictionary_words_category_data($dicword_category_id);
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
			if (!$dicword_category_id) {$catdata = F_get_dictionary_words_category_data($m['dicword_category_id']);}
			//check authorization rights
			if (($dicword_category_id) OR ($userlevel >= $catdata->level)) {
				
				if(($viewmode)OR($m['dicword_id'] == $selectedword)) { //full mode	
					echo "<tr class=\"fill".$rowclass."\"><td class=\"fill".$rowclass."E\">";
					
					echo "<table class=\"edge\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\" style=\"width:100%\">";
					echo "<tr class=\"edge\">";
					echo "<th class=\"edge\" align=\"left\">";
					echo "".htmlentities($m['dicword_name'], ENT_NOQUOTES, $l['a_meta_charset'])."";
					echo "</th></tr>";
					echo "<tr class=\"edge\">";
					echo "<td class=\"edge\">";
					echo "<table class=\"fill\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\" style=\"width:100%\">";
					echo "<tr class=\"fillE\"><td class=\"fillEE\">";
					echo F_evaluate_modules($m['dicword_description']);
					// list correlated words
					F_print_correlated_words($m['dicword_category_id'], $m['dicword_correlates']);
					echo "</td></tr>";
					echo "</table>";
					
					//User comments via Forum Module
					if (isset($catdata->forum_id) AND ($catdata->forum_id > 0)) {
						$forum_data = F_get_forum_data($catdata->forum_id);
						//check if this topic already exist
						if ($topic_id = F_get_forum_topic_id($m['dicword_name'], $catdata->forum_id)) {
							//link to topic
							echo "<div align=\"right\"><a href=\"cp_forum_view.".CP_EXT."?fmode=top&amp;topid=".$topic_id."&amp;forid=".$catdata->forum_id."&amp;catid=".$forum_data->categoryid."\"  class=\"edge\">[".$l['w_user_notes']."]</a></div>";
						}
						else { // print a button to create new topic
							echo "<div align=\"right\"><a href=\"cp_forum_edit_message.".CP_EXT."?efmm=n&amp;forumid=".$catdata->forum_id."&amp;categoryid=".$forum_data->categoryid."&amp;fixed_topic_title=".urlencode($m['dicword_name'])."\" class=\"edge\">[".$l['w_add_note']."]</a></div>";	
						}
					}
					
					echo "</td></tr></table>";
					echo "</td></tr>";
				}
				else { //compact mode
					echo "<tr class=\"fill".$rowclass."\"><td class=\"fill".$rowclass."E\">";
					
					echo "<a href=\"javascript:FJ_submit_dictionary_words_form('".$firstrow."','".urlencode($order_field)."','0','".$m['dicword_category_id']."','".$m['dicword_id']."','".$selectedletter."');\">".htmlentities($m['dicword_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</a>";
					
					// noscript alternative for search engines
					echo "<noscript><a href=\"cp_dictionary.".CP_EXT."?wid=".$m['dicword_id']."\">".htmlentities($m['dicword_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</a></noscript>";
					
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
	if ($dicword_category_id) {
		$url_request .= "&amp;dicword_category_id=".$dicword_category_id."";
	}
	if ($order_field) {
		$url_request .= "&amp;order_field=".urlencode($order_field)."";
	}
	if ($orderdir) {
		$url_request .= "&amp;orderdir=".$orderdir."";
	}
	
	echo "<br /><div align=\"center\"><a href=\"../../public/code/cp_dictionary_rss.".CP_EXT."?".$url_request."\" target=\"_blank\"><img src=\"../../pagefiles/rss.gif\" width=\"36\" height=\"14\" alt=\"RSS (XML RDF)\" border=\"0\" /></a></div><br />";
	
	// --- ------------------------------------------------------
	// --- page jump
	$sql = "SELECT count(*) AS total FROM ".K_TABLE_DICTIONARY_WORDS." ".$wherequery."";
	
	if (!empty($order_field)) {$param_array = "&amp;order_field=".urlencode($order_field)."";}
	if (!empty($orderdir)) {$param_array .= "&amp;orderdir=".$orderdir."";}
	if (!empty($dicword_category_id)) {$param_array .= "&amp;dicword_category_id=".$dicword_category_id."";}
	if (!empty($diccat_language)) {$param_array .= "&amp;diccat_language=".$diccat_language."";}
	if (!empty($viewmode)) {$param_array .= "&amp;viewmode=".$viewmode."";}
	$param_array .= "&amp;selectedword=0";
	if (!empty($selectedletter)) {$param_array .= "&amp;selectedletter=".$selectedletter."";}
	$param_array .= "&amp;submitted=1";
	if (!empty($term)) {$param_array .= "&amp;term=".urlencode($term)."";}
	if (!empty($addterms)) {$param_array .= "&amp;addterms=".$addterms."";}
	if (!empty($wordsearch)) {$param_array .= "&amp;wordsearch=".$wordsearch."";}
	F_show_page_navigator($_SERVER['SCRIPT_NAME'], $aiocp_dp, $sql, $firstrow, $rowsperpage, $param_array);
	
	// show links for all categories (needed be search engine crawlers)
	$sqlc = "SELECT * FROM ".K_TABLE_DICTIONARY_CATEGORIES." WHERE (diccat_level<=".$userlevel." AND diccat_language='".$selected_language."') ORDER BY diccat_name";
		if($rc = F_aiocpdb_query($sqlc, $db)) {
			echo "<div align=\"center\" class=\"small\">".$l['w_category'].": ";
			$notfirst = false;
			while($mc = F_aiocpdb_fetch_array($rc)) {
				if ($notfirst) {
					echo " |";
				}
				echo " <a href=\"".$_SERVER['SCRIPT_NAME']."";
				if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {
					echo "?aiocp_dp=".$aiocp_dp."";
					echo "&amp;dicword_category_id=".$mc['diccat_id']."";
				} else {
					echo "?dicword_category_id=".$mc['diccat_id']."";
				}
				echo "\">".htmlentities($mc['diccat_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</a>";
				$notfirst = true;
			}
			echo "</div>\n";
		}
		else {
			F_display_db_error();
		}
	
	return;
}


// ------------------------------------------------------------
// Display Dictionary in RSS 1.0 format
// ------------------------------------------------------------
function F_show_dictionary_words_RSS($dicword_category_id, $wherequery, $order_field, $orderdir) {
	global $l, $db, $selected_language;
	global $selectedletter;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_page.'.CP_EXT);
	
	//initialize variable
	$userlevel = $_SESSION['session_user_level'];
	
	$RSS_HEADER = "";
	$RSS_ITEMS = "";
	
	if(!$order_field) {$order_field = "dicword_name";}
	else {$order_field = addslashes(preg_replace("/[\s]+/i", " ", $order_field));}
	if(!$orderdir) {$orderdir=0; $nextorderdir=1; $full_order_field = $order_field;}
	else {$orderdir=1; $nextorderdir=0; $full_order_field = $order_field." DESC";}
	$full_order_field = urldecode($full_order_field);
	if(!isset($selectedletter)) {$selectedletter = '';}
	
	if(!F_count_rows(K_TABLE_DICTIONARY_WORDS)) { //if the table is void (no items) display message
		return FALSE;
	}
	
	//send page header
	$RSS_HEADER .= "<"."?xml version=\"1.0\" encoding=\"".$l['a_meta_charset']."\"?".">\n";
	
	$RSS_HEADER .= "<!DOCTYPE rdf:RDF [\n<!ENTITY % HTMLlat1 PUBLIC\n\"-//W3C//ENTITIES Latin 1 for XHTML//EN\n\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml-lat1.ent\">\n%HTMLlat1;\n]>\n";

	$RSS_HEADER .= "<rdf:RDF xmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\" xmlns=\"http://purl.org/rss/1.0/\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\">\n\n";
	
	// --- ------------------------------------------------------
	
	if (isset($dicword_category_id) AND ($dicword_category_id==0)) { //select all categories
		$wherequery = "WHERE 1";
	}
	
	if( (!$dicword_category_id) AND (!$wherequery) ) {
		$sql = "SELECT * FROM ".K_TABLE_DICTIONARY_CATEGORIES." WHERE (diccat_level<=".$userlevel." AND diccat_language='".$selected_language."') ORDER BY diccat_name LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$dicword_category_id = $m['diccat_id'];
			}
			else {
				return FALSE; //authorization deny
			}
		}
		else {
			F_display_db_error();
		}
	}

	if($dicword_category_id) {
		$sqlc = "SELECT * FROM ".K_TABLE_DICTIONARY_CATEGORIES." WHERE diccat_id=".$dicword_category_id."";
		if($rc = F_aiocpdb_query($sqlc, $db)) {
			if($mc = F_aiocpdb_fetch_array($rc)) {
				if($mc['diccat_level']>$userlevel) {
					return FALSE; //authorization deny
				}
				
				if (!$wherequery) {$wherequery = "WHERE (dicword_category_id='".$dicword_category_id."')";}
				else {$wherequery .= " AND (dicword_category_id='".$dicword_category_id."')";}
				
				$RSS_HEADER .= "<channel rdf:about=\"".K_PATH_PUBLIC_CODE."cp_dictionary_words.".CP_EXT."\">\n";
				$RSS_HEADER .= "\t<title>".htmlentities($mc['diccat_name'], ENT_QUOTES, $l['a_meta_charset'])."</title>\n";
				$RSS_HEADER .= "\t<link>".K_PATH_PUBLIC_CODE."cp_dictionary_words.".CP_EXT."?dicword_category_id=".$dicword_category_id."</link>\n";
				$RSS_HEADER .= "\t<description>\n";
				$RSS_HEADER .= "\t\t".$mc['diccat_description']."\n";
				$RSS_HEADER .= "\t</description>\n\n";	
				$RSS_HEADER .= "\t<dc:language>".$mc['diccat_language']."</dc:language>\n";
			}
		}
		else {
			F_display_db_error();
		}
	}
	else { //various words from different categories
		$RSS_HEADER .= "<channel rdf:about=\"".K_PATH_PUBLIC_CODE."cp_dictionary_words.".CP_EXT."\">\n";
		$RSS_HEADER .= "\t<title>".htmlentities(K_SITE_TITLE, ENT_QUOTES, $l['a_meta_charset'])."</title>\n";
		$RSS_HEADER .= "\t<link>".K_PATH_PUBLIC_CODE."cp_dictionary_words.".CP_EXT."?wherequery=".urlencode($wherequery)."</link>\n";
		$RSS_HEADER .= "\t<description>\n";
		$RSS_HEADER .= "\t\tDICTIONARY: ".K_SITE_DESCRIPTION."\n";
		$RSS_HEADER .= "\t</description>\n\n";	
	}
	
	$RSS_HEADER .= "<items>\n";
	$RSS_HEADER .= "\t<rdf:Seq>\n";
	
	if (!$wherequery) {$wherequery = "WHERE (dicword_name LIKE '".$selectedletter."%')";}
	else {$wherequery .= " AND (dicword_name LIKE '".$selectedletter."%')";}
	
	$sql = "SELECT * FROM ".K_TABLE_DICTIONARY_WORDS." ".$wherequery." ORDER BY ".$full_order_field."";
	
	if($r = F_aiocpdb_query($sql, $db)) {;
		while($m = F_aiocpdb_fetch_array($r)) {
			
			//get category data
			if (!$dicword_category_id) {$catdata = F_get_dictionary_words_category_data($m['dicword_category_id']);}
			//check authorization rights
			if (($dicword_category_id) OR ($userlevel >= $catdata->level)) {
				$item_uri = "".K_PATH_PUBLIC_CODE."cp_dictionary_words.".CP_EXT."?dicword_category_id=".$dicword_category_id."&amp;selectedword=".$m['dicword_id']."&amp;selectedletter=".$m['dicword_name']."";
							
				$RSS_HEADER .= "\t\t<rdf:li resource=\"".$item_uri."\" />\n";

				$RSS_ITEMS .= "<item rdf:about=\"".$item_uri."\">\n";
				$RSS_ITEMS .= "\t<title>".htmlentities($m['dicword_name'], ENT_QUOTES, $l['a_meta_charset'])."</title>\n";
				$RSS_ITEMS .= "\t<link>".$item_uri."</link>\n";
				$RSS_ITEMS .= "\t<description>\n";
				$RSS_ITEMS .= "\t\t".htmlentities(unhtmlentities($m['dicword_description']), ENT_QUOTES, $l['a_meta_charset'])."\n";
				$RSS_ITEMS .= "\t</description>\n";
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
function F_show_select_dictionary_words($wherequery, $order_field, $orderdir, $firstrow, $rowsperpage) {
	global $l, $db, $selected_language, $aiocp_dp;
	global $diccat_language, $changelanguage, $dicword_category_id, $viewmode, $selectedword;
	global $selectedletter;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	
	if(!$selectedword) {$selectedword=0;}
	if(!isset($viewmode)) {$viewmode=0;}
else {$viewmode=intval($viewmode);}
	if(!isset($selectedletter)) {$selectedletter = '';}
	
// Initialize variables
$userlevel = $_SESSION['session_user_level'];

if(!$diccat_language) {$diccat_language = $selected_language;}

if((!isset($dicword_category_id) OR (!$dicword_category_id)) OR (isset($changelanguage) AND $changelanguage)) {
	$sql = "SELECT * FROM ".K_TABLE_DICTIONARY_CATEGORIES." WHERE (diccat_level<=".$userlevel." AND diccat_language='".$diccat_language."') ORDER BY diccat_name LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$dicword_category_id = $m['diccat_id'];
		}
		else {
			$dicword_category_id = FALSE;
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
<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_dicwordslanguage" id="form_dicwordslanguage">

<table class="fill" border="0" cellspacing="2" cellpadding="1">
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_language', 'h_diccat_language'); ?></b></td>
<td class="fillOE">
 <select name="diccat_language" id="diccat_language" size="0" onchange="document.form_dicwordslanguage.submit()">
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
			if($m['language_code'] == $diccat_language) {
				echo " selected=\"selected\"";
			}
			echo ">".htmlentities($m['language_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
			$nsl .= "<li><a href=\"".$noscriptlink."diccat_language=".$m['language_code']."\">".htmlentities($m['language_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</a></li>";
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

<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_dicwordshow" id="form_dicwordshow">

<table class="fill" border="0" cellspacing="2" cellpadding="1">
<!-- SELECT category ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_category', 'h_diccat_select'); ?></b></td>
<td class="fillEE">
<select name="dicword_category_id" id="dicword_category_id" size="0" onchange="document.form_dicwordshow.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_DICTIONARY_CATEGORIES." WHERE (diccat_level<=".$userlevel." AND diccat_language='".$diccat_language."') ORDER BY diccat_name";

if($r = F_aiocpdb_query($sql, $db)) {
	$nsl = "<ul>";
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['diccat_id']."\"";
		if($m['diccat_id'] == $dicword_category_id) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['diccat_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
		$nsl .= "<li><a href=\"".$noscriptlink."dicword_category_id=".$m['diccat_id']."\">".htmlentities($m['diccat_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</a></li>";
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
<select name="viewmode" id="viewmode" size="0" onchange="document.form_dicwordshow.selectedword.value=''; document.form_dicwordshow.submit()">
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

<input type="hidden" name="diccat_language" id="fds_diccat_language" value="<?php echo $diccat_language; ?>" />
<input type="hidden" name="selectedword" id="fds_selectedword" value="<?php echo $selectedword; ?>" />
<input type="hidden" name="order_field" id="fds_order_field" value="<?php echo $order_field; ?>" />
<input type="hidden" name="orderdir" id="fds_orderdir" value="<?php echo $orderdir; ?>" />
<input type="hidden" name="firstrow" id="fds_firstrow" value="<?php echo $firstrow; ?>" />
<input type="hidden" name="selectedletter" id="fds_selectedletter" value="<?php echo $selectedletter; ?>" />
<input type="hidden" name="submitted" id="fds_submitted" value="0" />
</form>

</td>
</tr>
</table>
<br />
<!-- SHOW NEWS ==================== -->
<?php 
if ($dicword_category_id) {
	
	//create a list of disabled alphabetic buttons
	$disabled_letters = array();
	$alphabet = Array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
	while (list($key, $letter) = each($alphabet)) {
		$sqldab = "SELECT * FROM ".K_TABLE_DICTIONARY_WORDS." WHERE (dicword_category_id='".$dicword_category_id."' AND dicword_name LIKE '".$letter."%')  LIMIT 1";
		if($rdab = F_aiocpdb_query($sqldab, $db)) {
			if (!F_aiocpdb_fetch_array($rdab)) {
				$disabled_letters[] = $letter;
			}
		}
		else {
			F_display_db_error();
		}
	}
	
	F_alphanumeric_selector("form_dicwordshow", "selectedletter", $selectedletter, $disabled_letters);
	F_show_dictionary_words($dicword_category_id, $viewmode, $selectedword, $wherequery, $order_field, $orderdir, $firstrow, $rowsperpage);
}
?>
<!-- END SHOW NEWS ==================== -->

<!-- ====================================================== -->

<!-- Submit form  ==================== -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
function FJ_submit_dictionary_words_form(newfirstrow, neworder_field, neworderdir, wordcategory, wordselected, letter) {
	document.form_dicwordshow.selectedletter.value=letter;
	document.form_dicwordshow.dicword_category_id.value=wordcategory;
	document.form_dicwordshow.selectedword.value=wordselected;
	document.form_dicwordshow.order_field.value=neworder_field;
	document.form_dicwordshow.orderdir.value=neworderdir;
	document.form_dicwordshow.firstrow.value=newfirstrow;
	document.form_dicwordshow.submitted.value=1;
	document.form_dicwordshow.submit();
}

document.form_dicwordshow.dicword_category_id.focus();
//]]>
</script>
<!-- END Cange focus to dicword_id select -->
<?php
} //end of function

// ------------------------------------------------------------
// Search form for news
// ------------------------------------------------------------
function F_search_dictionary_words($wherequery, $order_field, $orderdir, $firstrow, $rowsperpage) {
	global $l, $db, $selected_language, $aiocp_dp;
	global $term, $submitted, $dicword_category_id, $wordsearch,  $selectedword, $addterms;
	global $selectedletter;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	
	if(!$firstrow) {$firstrow="0";}
	else {$firstrow=intval($firstrow);} 
	if(!$rowsperpage) {$rowsperpage = K_MAX_ROWS_PER_PAGE;}
	else {$rowsperpage=intval($rowsperpage);}
	if(!isset($selectedletter)) {$selectedletter = '';}
	
	//Initialize variables
	$userlevel = $_SESSION['session_user_level'];
	if(!$dicword_category_id) {$dicword_category_id="0";} // All categories
	if(!$selectedword) {$selectedword=0;}
	if(!isset($viewmode)) {$viewmode=0;}
else {$viewmode=intval($viewmode);}

if(!F_count_rows(K_TABLE_DICTIONARY_WORDS)) { //if the table is void (no items) display message
	echo "<h2>".$l['m_databasempty']."</h2>";
}
else { //the table is not empty

// ---------------------------------------------------------------

if($wordsearch OR $submitted) { // Submitting query (search results)
	if(isset($term) AND ($term != "")) {
		$wherequery = "WHERE (";
		$terms = preg_split("/[\s]+/i", addslashes($term)); // Get all the words into an array
		$size = sizeof($terms);
		//redundant check for security feature
		if($addterms != "AND"){$addterms = "OR";}
		$wherequery .= "(";
		for($i=0;$i<$size;$i++) {
			if($i>0) {$wherequery .= " ".$addterms." ";}
			$wherequery .= "((dicword_name LIKE '%$terms[$i]%')";
			$wherequery .= " OR (dicword_description LIKE '%$terms[$i]%'))";
		}
		$wherequery .= ")";
		if($dicword_category_id) {$wherequery .= " AND (dicword_category_id=".$dicword_category_id.")";}
		$wherequery .= ")"; // close WHERE clause
	}
	
	F_show_dictionary_words($dicword_category_id, $viewmode, $selectedword, $wherequery, $order_field, $orderdir, $firstrow, $rowsperpage);
} //end if($wordsearch OR $submitted)

// ---------------------------------------------------------------
?>

<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_dicwordsearch" id="form_dicwordsearch">
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
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_category', 'h_diccat_select'); ?></b></td>
<td class="fillEE"><select name="dicword_category_id" id="dicword_category_id">
<?php
if(!$dicword_category_id) {echo "<option value=\"0\" selected=\"selected\">".$l['d_all_categories']."</option>";}
else {echo "<option value=\"0\">".$l['d_all_categories']."</option>";}
$sql = "SELECT * FROM ".K_TABLE_DICTIONARY_CATEGORIES." WHERE (diccat_level<=".$userlevel." AND diccat_language='".$selected_language."') ORDER BY diccat_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['diccat_id']."\"";
		if($m['diccat_id'] == $dicword_category_id) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['diccat_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
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
<input type="hidden" name="selectedword" id="selectedword" value="<?php echo $selectedword; ?>" />
<input type="hidden" name="wordsearch" id="wordsearch" value="" />
<input type="hidden" name="order_field" id="order_field" value="<?php echo $order_field; ?>" />
<input type="hidden" name="orderdir" id="orderdir" value="<?php echo $orderdir; ?>" />
<input type="hidden" name="firstrow" id="firstrow" value="<?php echo $firstrow; ?>" />
<input type="hidden" name="selectedletter" id="selectedletter" value="<?php echo $selectedletter; ?>" />
<input type="hidden" name="submitted" id="submitted" value="0" />
<?php F_submit_button("form_dicwordsearch","wordsearch",$l['w_search']); ?>
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
function FJ_submit_dictionary_words_form(newfirstrow, neworder_field, neworderdir, wordcategory, wordselected, letter) {
	//document.form_dicwordsearch.dicword_category_id.value=wordcategory;
	document.form_dicwordsearch.selectedletter.value=letter;
	document.form_dicwordsearch.selectedword.value=wordselected;
	document.form_dicwordsearch.order_field.value=neworder_field;
	document.form_dicwordsearch.orderdir.value=neworderdir;
	document.form_dicwordsearch.firstrow.value=newfirstrow;
	document.form_dicwordsearch.submitted.value=1;
	document.form_dicwordsearch.submit();
}
//]]>
</script>
<!-- END Submit form ==================== -->
<?php
} //end of function

// ----------------------------------------------------------
// read category data
// ----------------------------------------------------------
function F_get_dictionary_words_category_data($categoryid) {
	global $db, $l;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	
	$sql = "SELECT * FROM ".K_TABLE_DICTIONARY_CATEGORIES." WHERE diccat_id='".$categoryid."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$diccat->language = $m['diccat_language'];
			$diccat->level = $m['diccat_level'];
			$diccat->name = $m['diccat_name'];
			$diccat->description = $m['diccat_description'];
			$diccat->forum_id = $m['diccat_forum_id'];
			return $diccat;
		}
	}
	else {
		F_display_db_error();
	}
	return FALSE;
}


// ----------------------------------------------------------
// get word_id from name
// ----------------------------------------------------------
function F_get_word_id($category, $word_name) {
	global $db, $l;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	$sql = "SELECT dicword_id FROM ".K_TABLE_DICTIONARY_WORDS." WHERE dicword_category_id='".$category."' AND dicword_name='".$word_name."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			return $m['dicword_id'];
		}
	}
	else {
		F_display_db_error();
	}
	return FALSE;
}

// ----------------------------------------------------------
// print correlated words
// ----------------------------------------------------------
function F_print_correlated_words($category, $correlated) {
	global $db, $l;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	$output = "";
	$wordslist = explode(",", $correlated);
		
	while (list($key, $word) = each($wordslist)) {
		$word = trim($word);
		$wordid = F_get_word_id($category, $word);
		if ($wordid) {
			$output .="<a href=\"javascript:FJ_submit_dictionary_words_form('', '', '', '".$category."','".$wordid."','".$word."') \">".$word."</a>, ";	
		}
	}
	//remove trailing comma
	if (strlen($output)>2) {
		$output = substr($output, 0, -2);
		$output = "<br />\n<br />\n".$l['w_correlates'].": ".$output;
	}
	echo "<small>&nbsp;".$output."</small>";
}


// ------------------------------------------------------------
// Display single dictionary word
// ------------------------------------------------------------
function F_display_single_dictionary_word($wid) {
	global $l, $db, $selected_language, $aiocp_dp;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	if(!F_count_rows(K_TABLE_DICTIONARY_WORDS)) { //if the table is void (no items) display message
		echo "<h2>".$l['m_databasempty']."</h2>";
	}
	else { //the table is not empty
		$wherequery = "WHERE dicword_id='".$wid."'";
		F_show_fixed_dictionary_words("", 1, $wid, $wherequery, "", "", 0, K_MAX_ROWS_PER_PAGE);
	} 
}

// ------------------------------------------------------------
// Show news without selectionform
// ------------------------------------------------------------
function F_show_fixed_dictionary_words($dicword_category_id, $viewmode, $selectedword, $wherequery, $order_field, $orderdir, $firstrow, $rowsperpage) {
	global $l, $db, $selected_language, $aiocp_dp;
	global $selectedletter;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	
?>

<form action="<?php echo "cp_dictionary_words.".CP_EXT.""; ?>" method="post" enctype="multipart/form-data" name="form_dicwordshow" id="form_dicwordshow">
<input type="hidden" name="diccat_language" id="diccat_language" value="<?php echo $selected_language; ?>" />
<input type="hidden" name="dicword_category_id" id="dicword_category_id" value="<?php echo $dicword_category_id; ?>" />
<input type="hidden" name="viewmode" id="viewmode" value="<?php echo $viewmode; ?>" />
<input type="hidden" name="selectedword" id="selectedword" value="<?php echo $selectedword; ?>" />
<input type="hidden" name="order_field" id="order_field" value="<?php echo $order_field; ?>" />
<input type="hidden" name="orderdir" id="orderdir" value="<?php echo $orderdir; ?>" />
<input type="hidden" name="firstrow" id="firstrow" value="<?php echo $firstrow; ?>" />
<input type="hidden" name="selectedletter" id="selectedletter" value="<?php echo $selectedletter; ?>" />
<input type="hidden" name="submitted" id="submitted" value="0" />
</form>

<!-- SHOW NEWS ==================== -->
<?php 
if ($dicword_category_id) {
	
	//create a list of disabled alphabetic buttons
	$disabled_letters = array();
	$alphabet = Array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
	while (list($key, $letter) = each($alphabet)) {
		$sqldab = "SELECT * FROM ".K_TABLE_DICTIONARY_WORDS." WHERE (dicword_category_id='".$dicword_category_id."' AND dicword_name LIKE '".$letter."%')  LIMIT 1";
		if($rdab = F_aiocpdb_query($sqldab, $db)) {
			if (!F_aiocpdb_fetch_array($rdab)) {
				$disabled_letters[] = $letter;
			}
		}
		else {
			F_display_db_error();
		}
	}
}
else {
	$disabled_letters = NULL;
}

F_alphanumeric_selector("form_dicwordshow", "selectedletter", $selectedletter, $disabled_letters);
F_show_dictionary_words($dicword_category_id, $viewmode, $selectedword, $wherequery, $order_field, $orderdir, $firstrow, $rowsperpage);
?>
<!-- END SHOW NEWS ==================== -->

<!-- ====================================================== -->

<!-- Submit form  ==================== -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
function FJ_submit_dictionary_words_form(newfirstrow, neworder_field, neworderdir, diccategory, wordselected, letter) {
	document.form_dicwordshow.selectedletter.value=letter;
	document.form_dicwordshow.dicword_category_id.value=diccategory;
	document.form_dicwordshow.selectedword.value=wordselected;
	document.form_dicwordshow.order_field.value=neworder_field;
	document.form_dicwordshow.orderdir.value=neworderdir;
	document.form_dicwordshow.firstrow.value=newfirstrow;
	document.form_dicwordshow.submitted.value=1;
	document.form_dicwordshow.submit();
}
//]]>
</script>
<!-- END Cange focus to dicword_id select -->
<?php
} //end of function
//============================================================+
// END OF FILE                                                 
//============================================================+
?>
