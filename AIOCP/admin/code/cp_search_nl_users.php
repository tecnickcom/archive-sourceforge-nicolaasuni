<?php
//============================================================+
// File name   : cp_search_nl_users.php                        
// Begin       : 2002-03-07                                    
// Last Update : 2008-07-06
//                                                             
// Description : Advanced Search for Newsletter Users          
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

require_once('../../shared/config/cp_extension.inc');
require_once('../config/cp_config.'.CP_EXT);

$pagelevel = K_AUTH_ADMIN_CP_SEARCH_NL_USERS;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

require_once('../../shared/code/cp_functions_form.'.CP_EXT);
require_once('../code/cp_functions_newsletter_users.'.CP_EXT);

$thispage_title = $l['t_nl_user_search'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages;

if (isset($_REQUEST['order_field'])) {
	$order_field = $_REQUEST['order_field'];
} else {
	$order_field = "";
}
if (isset($_REQUEST['orderdir'])) {
	$orderdir = $_REQUEST['orderdir'];
} else {
	$orderdir = "";
}
if (isset($_REQUEST['firstrow'])) {
	$firstrow = $_REQUEST['firstrow'];
} else {
	$firstrow = "";
}
if (isset($_REQUEST['rowsperpage'])) {
	$rowsperpage = $_REQUEST['rowsperpage'];
} else {
	$rowsperpage = "";
}
if (isset($_REQUEST['term'])) {
	$term = $_REQUEST['term'];
} else {
	$term = "";
}

if((isset($nlusersearch) AND $nlusersearch) OR (isset($submitted) AND $submitted)) { // Submitting query (search results)
	if (isset($term) AND (!empty($term))) {
		$wherequery = "WHERE (";
		$terms = preg_split("/[\s]+/i", addslashes($term)); // Get all the words into an array
		$size = sizeof($terms);
		//redundant check for security feature
		if($addterms != "AND"){$addterms = "OR";}
		$wherequery .= "(";
		for($i=0;$i<$size;$i++) {
			if($i>0) {$wherequery .= " ".$addterms." ";}
			$wherequery .= "(nluser_email LIKE '%$terms[$i]%')";
		}
		$wherequery .= ")";
		if($nluser_nlcatid!="all") {$wherequery .= " AND (nluser_nlcatid=".$nluser_nlcatid.")";}
		$wherequery .= ")"; // close WHERE clause
	}
	F_select_nl_user($wherequery, $order_field, $orderdir, $firstrow, K_MAX_ROWS_PER_PAGE);
}
// ---------------------------------------------------------------
?>

<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_nlusersearch" id="form_nlusersearch">
<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<tr class="fillO">
<td class="fillOO" align="right">
<b><?php echo F_display_field_name('w_keywords', 'h_search_keywords'); ?></b>
</td>
<td class="fillOE"><input type="text" name="term" id="term" value="<?php echo htmlentities($term, ENT_COMPAT, $l['a_meta_charset']); ?>" /></td></tr>

<?php
if (isset($addterms) AND ($addterms == "AND")) {
echo "<tr class=\"fillE\"><td class=\"fillEO\">&nbsp;</td><td class=\"fillEE\"><input type=\"radio\" name=\"addterms\" value=\"OR\" /> ".$l['d_search_any']."</td></tr>";
echo "<tr class=\"fillE\"><td class=\"fillEO\">&nbsp;</td><td class=\"fillEE\"><input type=\"radio\" name=\"addterms\" value=\"AND\" checked=\"checked\" /> ".$l['d_search_all']."</td></tr>";
}
else {
echo "<tr class=\"fillO\"><td class=\"fillOO\">&nbsp;</td><td class=\"fillOE\"><input type=\"radio\" name=\"addterms\" value=\"OR\" checked=\"checked\" /> ".$l['d_search_any']."</td></tr>";
echo "<tr class=\"fillO\"><td class=\"fillOO\">&nbsp;</td><td class=\"fillOE\"><input type=\"radio\" name=\"addterms\" value=\"AND\" /> ".$l['d_search_all']."</td></tr>";
}
?>

<tr class="fillE">
<td class="fillEO" align="right">
<b><?php echo F_display_field_name('w_category', 'h_newslettercat_select'); ?></b>
</td>
<td class="fillEE"><select name="nluser_nlcatid" id="nluser_nlcatid">
<?php
	if($nluser_nlcatid=="all") {echo "<option value=\"all\" selected=\"selected\">".$l['d_all_categories']."</option>";}
	else {echo "<option value=\"all\">".$l['d_all_categories']."</option>";}
	
	$sql2 = "SELECT nlcat_id,nlcat_language,nlcat_name FROM ".K_TABLE_NEWSLETTER_CATEGORIES." ORDER BY nlcat_language,nlcat_name";
	if($r2 = F_aiocpdb_query($sql2, $db)) {
		if($m2 = F_aiocpdb_fetch_array($r2)) {
			echo "<option value=\"".$m2['nlcat_id']."\"";
			if($m2['nlcat_id'] == $nluser_nlcatid) {
				echo " selected=\"selected\"";
			}
			echo ">[".$m2['nlcat_language']."] ".htmlentities($m2['nlcat_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
		}
	}
	else {
		F_display_db_error();
	}
?>
</select></td></tr>
</table>

</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="order_field" id="order_field" value="<?php echo $order_field; ?>" />
<input type="hidden" name="orderdir" id="orderdir" value="<?php echo $orderdir; ?>" />
<input type="hidden" name="firstrow" id="firstrow" value="<?php echo $firstrow; ?>" />
<input type="hidden" name="submitted" id="submitted" value="0" />
<input type="hidden" name="nlusersearch" id="nlusersearch" value="" />
<?php F_submit_button("form_nlusersearch","nlusersearch",$l['w_search']); ?>
</td></tr>

</table>
</form>

<!-- Submit form with new order field ==================== -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
function FJ_submit_searchnlusers_form(newfirstrow, neworder_field, neworderdir) {
	document.form_nlusersearch.order_field.value=neworder_field;
	document.form_nlusersearch.orderdir.value=neworderdir;
	document.form_nlusersearch.firstrow.value=newfirstrow;
	document.form_nlusersearch.submitted.value=1;
	document.form_nlusersearch.submit();
}
//]]>
</script>
<!-- END Submit form with new order field ==================== -->
<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
