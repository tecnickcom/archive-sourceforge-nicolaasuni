<?php
//============================================================+
// File name   : cp_functions_forum_search.php
// Begin       : 2002-03-21
// Last Update : 2008-01-01
// 
// Description : Functions for Forum Search
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
// Search form for forum
// ------------------------------------------------------------
function F_search_forum($wherequery, $order_field, $orderdir, $firstrow, $rowsperpage) {
	global $l, $db, $selected_language, $aiocp_dp;
	global $term, $submitted, $forum_category, $forum_forum, $forumsearch, $addterms;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	require_once('../../shared/code/cp_functions_forum.'.CP_EXT);
	
	if(!$firstrow) {$firstrow="0";}
	else {$firstrow=intval($firstrow);} 
	if(!$rowsperpage) {$rowsperpage = K_MAX_ROWS_PER_PAGE;}
	else {$rowsperpage=intval($rowsperpage);}
	if(!isset($orderdir)) {$orderdir=1;} 
	else {$orderdir=intval($orderdir);}
	//Initialize variables
	$userlevel = $_SESSION['session_user_level'];
	if(!$forum_category) {$forum_category="0";} // All categories
	if(!$forum_forum) {$forum_forum="0";} // All forums
	
	
	if(!F_count_rows(K_TABLE_FORUM_POSTS)) { //if the table is void (no items) display message
		echo "<h2>".$l['m_databasempty']."</h2>";
		return FALSE;
	}
	
	// ---------------------------------------------------------------
	if($forumsearch OR $submitted) { // Submitting query (search results)
		if(isset($term) AND ($term != "")) {
			$wherequery = "WHERE forumposts_topicid=forumtopic_id AND (";
			$terms = preg_split("/[\s]+/i", addslashes($term)); // Get all the words into an array
			$size = sizeof($terms);
			//redundant check for security feature
			if($addterms != "AND") {$addterms = "OR";}
			$wherequery .= "(";
			for($i=0;$i<$size;$i++) {
				if($i>0) {$wherequery .= " ".$addterms." ";}
				$wherequery .= "(forumposts_text LIKE '%$terms[$i]%' OR forumtopic_title LIKE '%$terms[$i]%')";
			}
			$wherequery .= ")";
			if($forum_forum) {
				$wherequery .= " AND (forumposts_forumid=".$forum_forum.")";
			}
			elseif($forum_category) {
				$wherequery .= " AND (forumposts_categoryid=".$forum_category.")";
				}
			$wherequery .= ")"; // close WHERE clause
		}
		
		$wherequery .= "GROUP BY forumposts_topicid";
		
		F_show_forum_search_results($wherequery, $order_field, $orderdir, $firstrow, $rowsperpage);
	} //end if($newssearch OR $submitted)
	// ---------------------------------------------------------------
?>

<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_forumsearch" id="form_forumsearch">

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
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_category', 'h_forumcat_select'); ?></b></td>
<td class="fillEE"><select name="forum_category" id="ffs_forum_category">
<?php
if(!$forum_category) {echo "<option value=\"0\" selected=\"selected\">".$l['d_all_categories']."</option>";}
else {echo "<option value=\"0\">".$l['d_all_categories']."</option>";}
$sql = "SELECT * FROM ".K_TABLE_FORUM_CATEGORIES." WHERE (forumcat_language='".$selected_language."' AND forumcat_readinglevel<='".$userlevel."') ORDER BY forumcat_order";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['forumcat_id']."\"";
		if($m['forumcat_id'] == $forum_category) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['forumcat_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT category ==================== -->

<!-- SELECT forums ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_forum', 'h_forumed_select'); ?></b></td>
<td class="fillOE">
<select name="forum_forum" id="ffs_forum_forum" size="0">
<?php
if(!$forum_forum) {echo "<option value=\"0\" selected=\"selected\">".$l['d_all_forums']."</option>";}
else {echo "<option value=\"0\">".$l['d_all_forums']."</option>";}
$sql = "SELECT * FROM ".K_TABLE_FORUM_FORUMS." WHERE (forum_language='".$selected_language."' AND forum_readinglevel<='".$userlevel."' AND forum_status<2) ORDER BY forum_categoryid, forum_order";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		//read category data
		$catdata = F_get_forum_category_data($m['forum_categoryid']);
		echo "<option value=\"".$m['forum_id']."\"";
		if($m['forum_id'] == $forum_forum) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($catdata->name, ENT_NOQUOTES, $l['a_meta_charset'])." | ".htmlentities($m['forum_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT forums ==================== -->

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="forumsearch" id="ffs_forumsearch" value="" />
<input type="hidden" name="firstrow" id="ffs_firstrow" value="<?php echo $firstrow; ?>" />
<input type="hidden" name="order_field" id="ffs_order_field" value="<?php echo $order_field; ?>" />
<input type="hidden" name="orderdir" id="ffs_orderdir" value="<?php echo $orderdir; ?>" />
<input type="hidden" name="submitted" id="ffs_submitted" value="0" />
<?php F_submit_button("form_forumsearch","forumsearch",$l['w_search']); ?>
</td>
</tr>

</table>

</form>

<!-- Submit form with new order field ==================== -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
function FJ_submit_forumsearch_form(newfirstrow, neworder_field, neworderdir) {
		document.form_forumsearch.firstrow.value=newfirstrow;
		document.form_forumsearch.order_field.value=neworder_field;
		document.form_forumsearch.orderdir.value=neworderdir;
		document.form_forumsearch.submitted.value=1;
		document.form_forumsearch.submit();
}
//]]>
</script>
<!-- END Submit form with new order field ==================== -->

<?php
return TRUE;
} //end of function

// ------------------------------------------------------------
// show all messages on a particular topic
// ------------------------------------------------------------
function F_show_forum_search_results($wherequery, $order_field, $orderdir, $firstrow, $rowsperpage) {
	global $l, $db, $selected_language, $aiocp_dp;
	global $menu_mode, $noupstat, $forum_category, $forum_forum;
	global $term, $submitted, $forumsearch, $addterms;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_forum.'.CP_EXT);
	require_once('../../shared/code/cp_functions_user.'.CP_EXT);
	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	require_once('../../shared/code/cp_functions_page.'.CP_EXT);
	
	$userid = $_SESSION['session_user_id'];
	$userlevel = $_SESSION['session_user_level'];
	
	//initialize variables
	if(!$firstrow) {$firstrow="0";}
	else {$firstrow=intval($firstrow);} 
	if(!$rowsperpage) {$rowsperpage = K_MAX_ROWS_PER_PAGE;}
	else {$rowsperpage=intval($rowsperpage);}
	
	if(!$order_field) {$order_field = "forumposts_time";}
	else {$order_field = addslashes(preg_replace("/[\s]+/i", " ", $order_field));}
	if(!$orderdir) {$orderdir=0; $nextorderdir=1; $full_order_field = $order_field;}
	else {$orderdir=1; $nextorderdir=0; $full_order_field = $order_field." DESC";}
	$full_order_field = urldecode($full_order_field);
	
	if (!$wherequery) {
		$wherequery = "";
	}

	$sql = "SELECT *,COUNT(forumposts_id) AS nposts FROM ".K_TABLE_FORUM_POSTS.", ".K_TABLE_FORUM_TOPICS." ".$wherequery." ORDER BY nposts DESC, ".$full_order_field." LIMIT ".$firstrow.",".$rowsperpage."";
	
	echo "<table class=\"edge\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">";
	echo "<tr class=\"edge\">";
	echo "<td class=\"edge\">";
	
	echo "<table class=\"fill\" border=\"0\" cellspacing=\"2\" cellpadding=\"1\">";
	//show forum topics
	
	echo "<tr class=\"fill\">";
	echo "<th class=\"fillO\"><a href=\"javascript:FJ_submit_forumsearch_form(0,'forumposts_categoryid','".$nextorderdir."');\">".$l['w_category']."</a></th>";
	echo "<th class=\"fillE\"><a href=\"javascript:FJ_submit_forumsearch_form(0,'forumposts_forumid','".$nextorderdir."');\">".$l['w_forum']."</a></th>";
	echo "<th class=\"fillO\"><a href=\"javascript:FJ_submit_forumsearch_form(0,'forumposts_topicid','".$nextorderdir."');\">".$l['w_topic']."</a></th>";
	echo "<th class=\"fillE\"><a href=\"javascript:FJ_submit_forumsearch_form(0,'forumposts_time','".$nextorderdir."');\">".$l['w_date']."</a></th>";
	echo "<th class=\"fillO\"><a href=\"javascript:FJ_submit_forumsearch_form(0,'forumposts_poster','".$nextorderdir."');\">".$l['w_author']."</a></th>";
	echo "</tr>";
	
	// iterate all topics inside forum
	
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			
			//read category data
			$catdata = F_get_forum_category_data($m['forumposts_categoryid']);
			// read forum data
			$fdata = F_get_forum_data($m['forumposts_forumid']);
			// read topic data
			$tdata = F_get_forum_topic_data($m['forumposts_topicid']);
			
			//check authorization rights
			if ( ($userlevel >= $catdata->readinglevel) AND  ($userlevel >= $fdata->readinglevel) AND ($fdata->status < 2) ) {
				
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
				echo "<a href=\"cp_forum_view.".CP_EXT."?fmode=cat&amp;catid=".$m['forumposts_categoryid']."\"><small>".htmlentities($catdata->name, ENT_NOQUOTES, $l['a_meta_charset'])."</small></a>";
				echo "</td>";
				
				echo "<td class=\"fill".$rowclass."E\">";
				echo "<a href=\"cp_forum_view.".CP_EXT."?fmode=for&amp;forid=".$m['forumposts_forumid']."&amp;catid=".$m['forumposts_categoryid']."\"><small>".htmlentities($fdata->name, ENT_NOQUOTES, $l['a_meta_charset'])."</small></a>";
				echo "</td>";
				
				echo "<td class=\"fill".$rowclass."O\">";
				echo "<a href=\"cp_forum_view.".CP_EXT."?fmode=top&amp;topid=".$m['forumposts_topicid']."&amp;forid=".$m['forumposts_forumid']."&amp;catid=".$m['forumposts_categoryid']."\"><small>".htmlentities($tdata->title, ENT_NOQUOTES, $l['a_meta_charset'])."</small></a>";
				echo "</td>";
				
				echo "<td class=\"fill".$rowclass."E\">";
				echo "<a href=\"cp_forum_view.".CP_EXT."?fmode=top&amp;topid=".$m['forumposts_topicid']."&amp;forid=".$m['forumposts_forumid']."&amp;catid=".$m['forumposts_categoryid']."&amp;orderdir=1\"><small>".$m['forumposts_time']."</small></a>";
				echo "</td>";
				
				$poster = F_get_user_data($m['forumposts_poster']);
				echo "<td class=\"fill".$rowclass."O\">";
				echo "<a href=\"cp_user_profile.".CP_EXT."?user_id=".$m['forumposts_poster']."\">".htmlentities($poster->name, ENT_NOQUOTES, $l['a_meta_charset'])."</a>";
				echo "</td>";
				
				echo "</tr>";
			}
		}
	}
	else {
		F_display_db_error();
	}
	
	echo "</table>";
	echo "</td>";
	echo "</tr>";
	echo "</table>";
	//echo "</form>\n"; //close form for buttons
	// --- ------------------------------------------------------
	// --- page jump
	
	$sql = "SELECT count(*) AS total FROM ".K_TABLE_FORUM_POSTS.", ".K_TABLE_FORUM_TOPICS." ".$wherequery."";
	if (!empty($order_field)) {$param_array = "&amp;order_field=".urlencode($order_field)."";}
	if (!empty($orderdir)) {$param_array .= "&amp;orderdir=".$orderdir."";}
	$param_array .= "&amp;submitted=1";
	if (!empty($forum_category)) {$param_array .= "&amp;forum_category=".$forum_category."";}
	if (!empty($forum_forum)) {$param_array .= "&amp;forum_forum=".$forum_forum."";}
	if (!empty($forumsearch)) {$param_array .= "&amp;forumsearch=".$forumsearch."";}
	if (!empty($term)) {$param_array .= "&amp;term=".urlencode($term)."";}
	if (!empty($addterms)) {$param_array .= "&amp;addterms=".$addterms."";}
	F_show_page_navigator($_SERVER['SCRIPT_NAME'], $aiocp_dp, $sql, $firstrow, $rowsperpage, $param_array);
	
	?>
	<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_forumpostorder" id="form_forumpostorder">
	<input type="hidden" name="forum_category" id="forum_category" value="<?php echo $forum_category; ?>" />
	<input type="hidden" name="forum_forum" id="forum_forum" value="<?php echo $forum_forum; ?>" />
	<input type="hidden" name="firstrow" id="firstrow" value="<?php echo $firstrow; ?>" />
	<input type="hidden" name="order_field" id="order_field" value="<?php echo $order_field; ?>" />
	<input type="hidden" name="orderdir" id="orderdir" value="<?php echo $orderdir; ?>" />
	<input type="hidden" name="submitted" id="submitted" value="0" />
	</form>
	<!-- Submit form with new order field ==================== -->
	<script language="JavaScript" type="text/javascript">
	//<![CDATA[
	function FJ_submit_forumsearch_form(newfirstrow, neworder_field, neworderdir) {
		document.form_forumpostorder.firstrow.value=newfirstrow;
		document.form_forumpostorder.order_field.value=neworder_field;
		document.form_forumpostorder.orderdir.value=neworderdir;
		document.form_forumpostorder.submitted.value=1;
		document.form_forumpostorder.submit();
	}
	//]]>
	</script>
	<!-- END Submit form with new order field ==================== -->
<?php
	
	return TRUE;
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
