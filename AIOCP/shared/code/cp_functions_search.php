<?php
//============================================================+
// File name   : cp_functions_search.php                       
// Begin       : 2002-05-31                                    
// Last Update : 2003-11-18                                    
//                                                             
// Description : Functions for search engine                   
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
// Site Search form 
// ------------------------------------------------------------
function F_site_search_form() {
	global $l, $db, $selected_language, $aiocp_dp;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	
	//initialize variables
	
	$userlevel = $_SESSION['session_user_level'];
	
	if(isset($_REQUEST['firstrow'])) {
		$firstrow = $_REQUEST['firstrow'];
	}
	else {
		$firstrow = "0";
	}
	
	if(isset($_REQUEST['rowsperpage'])) {
		$rowsperpage = $_REQUEST['rowsperpage'];
	}
	else {
		$rowsperpage = K_MAX_ROWS_PER_PAGE;
	}
	
	if(isset($_REQUEST['search_keywords'])) {
		$search_keywords = $_REQUEST['search_keywords'];
	}
	else {
		$search_keywords = "";
	}
	/*
	if(isset($_REQUEST['search_match'])) {
		$search_match = $_REQUEST['search_match'];
	}
	else {
		$search_match = "AND";
	}
	*/
	if(isset($_REQUEST['search_for'])) {
		$search_for = $_REQUEST['search_for'];
	}
	else {
		$search_for = "substr";
	}
	
	if(isset($_REQUEST['search_language'])) {
		$search_language = $_REQUEST['search_language'];
	}
	else {
		$search_language = $selected_language;
	}
	
// ---------------------------------------------------------------
?>

<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_site_search" id="form_site_search">
<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<tr class="fillE">
<td class="fillEO" align="right">
<b><?php echo F_display_field_name('w_keywords', 'h_search_keywords'); ?></b>
</td>
<td class="fillEE">
<input type="text" name="search_keywords" id="search_keywords" value="<?php echo $search_keywords; ?>" />
</td></tr>

<?php /*
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_match', ''); ?></b></td>
<td class="fillEE">
<select name="search_match" id="search_match" size="0">
<?php
echo "<option value=\"AND\"";
if($search_match == "AND") {echo " selected=\"selected\"";}
echo ">".$l['w_all']."</option>\n";

echo "<option value=\"OR\"";
if($search_match == "OR") {echo " selected=\"selected\"";}
echo ">".$l['w_any']."</option>\n";
?>
</select>
</td>
</tr>
*/ ?>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_search_for', ''); ?></b></td>
<td class="fillOE">
<select name="search_for" id="search_for" size="0">
<?php
echo "<option value=\"substr\"";
if($search_for == "substr") {echo " selected=\"selected\"";}
echo ">".$l['w_substring']."</option>\n";

echo "<option value=\"whole\"";
if($search_for == "whole") {echo " selected=\"selected\"";}
echo ">".$l['w_whole_word']."</option>\n";

echo "<option value=\"begin\"";
if($search_for == "begin") {echo " selected=\"selected\"";}
echo ">".$l['w_beginning']."</option>\n";

echo "<option value=\"end\"";
if($search_for == "end") {echo " selected=\"selected\"";}
echo ">".$l['w_ending']."</option>\n";
?>
</select>
</td>
</tr>

<?php
//display language selector only if enabled languages are more than one
if (F_count_rows(K_TABLE_LANGUAGE_CODES, "WHERE language_enabled=1") > 1) {
?>
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_language', ''); ?></b></td>
<td class="fillEE">
<select name="search_language" id="search_language" size="0">
<?php
	echo "<option value=\"\"";
	if(!$search_language) {
		echo " selected=\"selected\"";
	}
	echo ">".$l['w_all']."</option>\n";
	
	$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"".$m['language_code']."\"";
			if($m['language_code'] == $search_language) {
				echo " selected=\"selected\"";
			}
			echo ">".htmlentities($m['language_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
		}
	}
	else {
		F_display_db_error();
	}
?>
</select>
</td>
</tr>
<?php
}
else {
	echo "<input type=\"hidden\" name=\"search_language\" id=\"search_language\" value=\"".$search_language."\" />";
}
?>
</table>

</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="firstrow" id="firstrow" value="<?php echo $firstrow; ?>" />
<input type="hidden" name="sitesearch" id="sitesearch" value="0" />
<?php F_submit_button("form_site_search", "sitesearch", $l['w_search']); ?>
</td></tr>
</table>
</form>

<!-- Submit form  ==================== -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
	function FJ_submit_search_form(newfirstrow) {
		document.form_site_search.firstrow.value=newfirstrow;
		document.form_site_search.sitesearch.value=1;
		document.form_site_search.submit();
	}
//]]>
</script>
<!-- END Submit form ==================== -->

<?php
	// if search has been submitted, search and display results
	if(isset($_REQUEST['sitesearch'])) {
		$search_time_start = getmicrotime(); //start benchmark cronometer
		if($search_keywords) { //build sql query
			$terms = preg_split("/[\s]+/i", addslashes($search_keywords)); // Get all the words into an array
			$size = sizeof($terms);
			//redundant check for security feature
			//if($search_match != "AND") {$search_match = "OR";}
			$search_match = "OR";
			
			$wherequery = "";
			for($i=0; $i<$size; $i++) {
				if($i>0) {$wherequery .= " ".$search_match." ";}
				switch ($search_for) {
					case "whole": {
						$wherequery .= "searchdic_word='".$terms[$i]."'";
						break;
					}
					case "begin": {
						$wherequery .= "searchdic_word LIKE '%".$terms[$i]."'";
						break;
					}
					case "end": {
						$wherequery .= "searchdic_word LIKE '".$terms[$i]."%'";
						break;
					}
					case "substr":
					default: {
						$wherequery .= "searchdic_word LIKE '%".$terms[$i]."%'";
						break;
					}
				}
				//search also in page title, description and keywords (very slow)
				//$wherequery .= $search_match." searchurl_title LIKE '%".$terms[$i]."%'";
				//$wherequery .= $search_match." searchurl_description LIKE '%".$terms[$i]."%'";
				//$wherequery .= $search_match." searchurl_keywords LIKE '%".$terms[$i]."%'";
			}
			
			if ($search_language) {
				$lang_code = F_word_language($search_language, "a_meta_language");
				if ($lang_code) {
					$wherequery .= " AND searchurl_language='".$lang_code."'";
				}
			}
			
			//build where query
			$search_where_sql = "WHERE searchdic_url_id=searchurl_id AND searchurl_level<=".$userlevel." AND ".$wherequery." GROUP BY searchurl_id";
			
			//echo "<b>".$search_where_sql."</b>"; 
			
			$numresults = F_site_search_results($search_where_sql, $firstrow, $rowsperpage); //display search results
			
			//display search statistics
			echo "<div>";
			echo $l['w_pages'].": ".$numresults."";
			echo " - ";
			$search_time_stop = getmicrotime(); //stop benchmark cronometer
			echo $l['w_time'].": ".round($search_time_stop - $search_time_start, 3)." sec";
			echo "</div>";
		}
	}
} //end of function


// ------------------------------------------------------------
// Display search results 
// ------------------------------------------------------------
function F_site_search_results($where_query, $firstrow, $rowsperpage) {
	global $l, $db, $selected_language;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_page.'.CP_EXT);
	
	$numresult = $firstrow; //count results
	
	$sql = "SELECT COUNT(searchdic_word) AS nwords, STDDEV(searchdic_position) AS wdev, searchurl_url, searchurl_content_type, searchurl_title, searchurl_description, searchurl_keywords, searchurl_language, searchurl_size, searchurl_index_time FROM ".K_TABLE_SEARCH_DICTIONARY.", ".K_TABLE_SEARCH_URL." ";
	$sql .= $where_query." ORDER BY nwords DESC, wdev";
	$sql .= " LIMIT ".$firstrow.", ".$rowsperpage."";
	
	//echo "<b>".$sql."</b>"; 
	
	echo "<table class=\"edge\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">";
	echo "<tr class=\"edge\">";
	echo "<td class=\"edge\">";
	
	if($r = F_aiocpdb_query($sql, $db)) {
		echo "<table class=\"fill\" border=\"0\" cellspacing=\"2\" cellpadding=\"1\">";
		
		$rowclass = "O";
		$rowodd=0;
		
		while($m = F_aiocpdb_fetch_array($r)) {
			//change style for each row
			if (isset($rowodd) AND ($rowodd)) {
				$rowclass = "O";
				$rowodd = 0;
			} else {
				$rowclass = "E";
				$rowodd = 1;
			}
			
			$numresult++;
			echo "<tr class=\"fill".$rowclass."\">";
			echo "<td class=\"fill".$rowclass."O\" valign=\"top\">";
			echo "".$numresult."";
			echo "</td>";
			echo "<td class=\"fill".$rowclass."E\">";
			
			//display results
			echo "<a href=\"".$m['searchurl_url']."\" target=\"".K_MAIN_FRAME_NAME."\"><b>".htmlentities($m['searchurl_title'], ENT_NOQUOTES, $l['a_meta_charset'])."</b></a>";
			echo " (".htmlentities($m['searchurl_language'], ENT_NOQUOTES, $l['a_meta_charset']).")<br />";
			echo "".htmlentities($m['searchurl_description'], ENT_NOQUOTES, $l['a_meta_charset'])."<br />";
			echo "<a href=\"".$m['searchurl_url']."\" target=\"".K_MAIN_FRAME_NAME."\">".$m['searchurl_url']."</a><br />";
			echo "".$m['searchurl_index_time']."";
			echo " (".htmlentities($m['searchurl_content_type'], ENT_NOQUOTES, $l['a_meta_charset']).")";
			echo " ".$m['searchurl_size']." Bytes";
			
			echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
	else {
		F_display_db_error();
	}
	
	echo "</td>";
	echo "</tr>";
	echo "<tr class=\"edge\">";
	echo "<td class=\"edge\">";
	
	// --- page jump
	$sql = "SELECT COUNT(*) AS total FROM ".K_TABLE_SEARCH_DICTIONARY.", ".K_TABLE_SEARCH_URL." ".$where_query."";
	if (!empty($search_for)) {$param_array .= "&amp;search_for=".$search_for."";}
	if (!empty($search_match)) {$param_array .= "&amp;search_match=".$search_match."";}
	if (!empty($search_keywords)) {$param_array .= "&amp;search_keywords=".$search_keywords."";}
	if (!empty($search_language)) {$param_array .= "&amp;search_language=".$search_language."";}
	if (!empty($sitesearch)) {$param_array .= "&amp;sitesearch=".$sitesearch."";}
	$numofpages = F_show_page_navigator($_SERVER['SCRIPT_NAME'], $aiocp_dp, $sql, $firstrow, $rowsperpage, $param_array);
	
	echo "</td>";
	echo "</tr>";
	echo "</table>";
	
	return $numofpages;
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
