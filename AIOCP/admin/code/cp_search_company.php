<?php
//============================================================+
// File name   : cp_search_company.php                         
// Begin       : 2002-04-26                                    
// Last Update : 2008-07-06
//                                                             
// Description : Advanced Search for Company                   
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

$pagelevel = K_AUTH_ADMIN_CP_SEARCH_COMPANY;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

require_once('../../shared/code/cp_functions_form.'.CP_EXT);
require_once('../code/cp_functions_company_select.'.CP_EXT);

$thispage_title = $l['t_company_search'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages; ?>
<!-- ====================================================== -->
<?php
if (isset($_REQUEST['term'])) {
	$term = $_REQUEST['term'];
} else {
	$term = "";
}
if (isset($_REQUEST['addterms'])) {
	$addterms = $_REQUEST['addterms'];
} else {
	$addterms = "";
}
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
if (isset($_REQUEST['companysearch'])) {
	$companysearch = $_REQUEST['companysearch'];
} else {
	$companysearch = "";
}
if (isset($_REQUEST['submitted'])) {
	$submitted = $_REQUEST['submitted'];
} else {
	$submitted = "";
}
if($companysearch OR $submitted) { // Submitting query (search results)
	if(isset($term) AND (!empty($term))){
		$wherequery = "WHERE (";
		$terms = preg_split("/[\s]+/i", addslashes($term)); // Get all the words into an array
		$size = sizeof($terms);
		//redundant check for security feature
		if($addterms != "AND"){$addterms = "OR";}
		$wherequery .= "(";
		for($i=0;$i<$size;$i++) {
			if($i>0) {$wherequery .= " ".$addterms." ";}
			$wherequery .= "((company_name LIKE '%$terms[$i]%')";
			$wherequery .= " OR (company_fiscalcode  LIKE '%$terms[$i]%')";
			$wherequery .= " OR (company_notes LIKE '%$terms[$i]%'))";
		}
		$wherequery .= ")";
		if($supplier!="all") {$wherequery .= " AND (company_supplier='".$supplier."')";}
		$wherequery .= ")"; // close WHERE clause
	}
	F_show_select_company($wherequery, $order_field, $orderdir, $firstrow, K_MAX_ROWS_PER_PAGE);
}
// ---------------------------------------------------------------
?>

<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_companysearch" id="form_companysearch">
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
if($addterms == "AND") {
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
<b><?php echo $l['w_supplier']; ?></b>
</td>
<td class="fillEE"><select name="supplier" id="supplier">
<?php
echo "<option value=\"all\" selected=\"selected\">".$l['w_all']."</option>\n";
echo "<option value=\"1\">".$l['w_yes']."</option>\n";
echo "<option value=\"0\">".$l['w_no']."</option>\n";
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
<input type="hidden" name="companysearch" id="companysearch" value="" />
<?php F_submit_button("form_companysearch","companysearch",$l['w_search']); ?>
</td></tr>

</table>
</form>

<!-- Submit form with new order field ==================== -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
function FJ_submit_searchcompany_form(newfirstrow, neworder_field, neworderdir) {
	document.form_companysearch.order_field.value=neworder_field;
	document.form_companysearch.orderdir.value=neworderdir;
	document.form_companysearch.firstrow.value=newfirstrow;
	document.form_companysearch.submitted.value=1;
	document.form_companysearch.submit();
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
