<?php
//============================================================+
// File name   : cp_functions_ec_transactions.php              
// Begin       : 2002-09-05                                    
// Last Update : 2005-06-29                                    
//                                                             
// Description : Functions for commercial transactions         
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

// ----------------------------------------------------------
// display select options for transactions
// ----------------------------------------------------------
function F_display_select_transaction_details() {
	global $db, $l, $selected_language;
	global $menu_mode, $t_date_start, $t_date_end, $t_work_id, $t_type, $t_virtual, $t_payment_type_id, $t_paytype_category_id, $t_direction, $t_paid, $t_mode, $t_description, $t_supplier, $t_option;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	
	if (!isset($t_option)) {
		$t_option = array(1,1,1,1,1,1,1,1,1,1);
	}
	
	if (!isset($t_date_start)) {
		$t_date_start = date("Y-m-d");
	}
	
	if (!isset($t_date_end)) {
		$t_date_end = date("Y-m-d");
	}
	
	//build MySQL WHERE query
	$where_query = "";
	if (!isset($t_mode)) {
		$t_mode = 0;
	}
	
	if (!$t_mode) { //order by transactions
			$where_query .= " AND mtrans_date>='".$t_date_start."'";
			$where_query .= " AND mtrans_date<='".$t_date_end."'";
	}
	else { //order by payments
			$where_query .= " AND transpay_date>='".$t_date_start."'";
			$where_query .= " AND transpay_date<='".$t_date_end."'";
	}
	
	if (isset($t_work_id) AND ($t_work_id > 0)) {
		$where_query .= " AND mtrans_work_id='".$t_work_id."'";
	}
	if (isset($t_type) AND ($t_type > 0) ) {
		$where_query .= " AND mtrans_type='".$t_type."'";
	}
	
	if (isset($t_supplier) AND ($t_supplier > 0) ) {
		$where_query .= " AND mtrans_supplier='".$t_supplier."'";
	}
	
	if (!isset($t_virtual)) {
		$t_virtual = -1;
	}
	if ($t_virtual >= 0) {
		$where_query .= " AND mtrans_virtual='".$t_virtual."'";
	}
	
	if (isset($t_direction) AND ($t_direction)) {
		$where_query .= " AND mtrans_direction='".$t_direction."'";
	}
	if (isset($t_paid) AND $t_paid) {
		switch ($t_paid) {
			case 1: { // paid
				$where_query .= " AND mtrans_amount=mtrans_paid_amount";
				break;
			}
			case 2: { // unpaid
				$where_query .= " AND mtrans_paid_amount=0";
				break;
			}
			case 3: { // partial
				$where_query .= " AND mtrans_paid_amount>0 AND mtrans_amount<>mtrans_paid_amount";
				break;
			}
			case 4: { // paid and partial
				$where_query .= " AND mtrans_paid_amount>0 AND mtrans_amount>=mtrans_paid_amount";
				break;
			}
			case 5: { // unpaid and partial
				$where_query .= " AND mtrans_amount>mtrans_paid_amount";
				break;
			}
		}
	}
	if (isset($t_payment_type_id) AND ($t_payment_type_id)) {
		$where_query .= " AND transpay_payment_id='".$t_payment_type_id."'";
	}
	if (isset($t_paytype_category_id) AND ($t_paytype_category_id)) {
		$sql = "SELECT * FROM ".K_TABLE_EC_PAYMENTS_TYPES." WHERE paytype_category_id=".$t_paytype_category_id."";
		if($r = F_aiocpdb_query($sql, $db)) {
			while($m = F_aiocpdb_fetch_array($r)) {
				$temp_query .= " OR transpay_payment_id='".$m['paytype_id']."'";
			}
			$temp_query = substr($temp_query, 3); //remove first OR
			if ($temp_query) {
				$where_query .= " AND (".$temp_query.")";
			}
		}
		else {
			F_display_db_error();
		}
	}
	
	if (isset($t_description) AND ($t_description)) {
		$wherequery .= " AND ((mtrans_description LIKE '%".$t_description."%') OR (mtrans_doc_ref LIKE '%".$t_description."%'))";
	}
	
	if (!$where_query) {
		$where_query = " WHERE YEAR(mtrans_date)='".gmdate('Y')."'"; //display all current year entries
	}
	else {
		$where_query = " WHERE 1".$where_query;
	}

	if (!$t_mode) {
		//order by transactions
		$sql_query = "SELECT * FROM ".K_TABLE_EC_TRANSACTIONS." LEFT JOIN ".K_TABLE_EC_TRANSACTIONS_PAYMENTS." ON mtrans_id=transpay_transaction_id ".$where_query." ORDER BY mtrans_date DESC, transpay_date DESC";
	}
	else {
		//order by payments
		$sql_query = "SELECT * FROM ".K_TABLE_EC_TRANSACTIONS." INNER JOIN ".K_TABLE_EC_TRANSACTIONS_PAYMENTS." ON mtrans_id=transpay_transaction_id ".$where_query." ORDER BY transpay_date DESC";
	}
	
	//remember the filter variables values for pdf document
	$t_filter = array($t_date_start, $t_date_end, $t_work_id, $t_type, $t_virtual, $t_supplier, $t_direction, $t_paid, $t_paytype_category_id, $t_payment_type_id, $t_description);
	$t_filter = urlencode(serialize($t_filter));
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_transactionselectshow" id="form_transactionselectshow">

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<th class="edge"><?php echo $l['w_filter']; ?></th>
<th class="edge"><?php echo $l['w_display']; ?></th>
</tr>

<tr class="edge">
<td class="edge" valign="top">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_mode', 'h_transaction_order_mode'); ?></b></td>
<td class="fillEE">
<select name="t_mode" id="t_mode" size="0">
<?php
echo "<option value=\"0\"";
if(!$t_mode) {
	echo " selected=\"selected\"";
}
echo ">".$l['w_transaction']."</option>\n";

echo "<option value=\"1\"";
if($t_mode) {
	echo " selected=\"selected\"";
}
echo ">".$l['w_payment']."</option>\n";
?>
</select>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_date_start', 'h_select_date_start'); ?></b></td>
<td class="fillOE"><input type="text" name="t_date_start" id="t_date_start" value="<?php echo $t_date_start; ?>" size="20" maxlength="10" />
<input type="hidden" name="x_t_date_start" id="x_t_date_start" value="([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})" />
<input type="hidden" name="xl_t_date_start" id="xl_t_date_start" value="<?php echo $l['w_date_start']; ?>" />
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_date_end', 'h_select_date_end'); ?></b></td>
<td class="fillEE"><input type="text" name="t_date_end" id="t_date_end" value="<?php echo $t_date_end; ?>" size="20" maxlength="10" />

<input type="hidden" name="x_t_date_end" id="x_t_date_end" value="([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})" />
<input type="hidden" name="xl_t_date_end" id="xl_t_date_end" value="<?php echo $l['w_date_end']; ?>" />
</td>
</tr>

<!-- SELECT ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_work', 'h_work_select'); ?></b></td>
<td class="fillOE">
<select name="t_work_id" id="t_work_id" size="0">
<?php
echo "<option value=\"\"";
if(!$t_work_id) {
	echo " selected=\"selected\"";
}
echo ">".$l['w_all']."</option>\n";

$sql = "SELECT * FROM ".K_TABLE_EC_WORKS." ORDER BY work_date_start DESC";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['work_id']."\"";
		if($m['work_id'] == $t_work_id) {
			echo " selected=\"selected\"";
		}
		echo ">".$m['work_date_start']." - ".htmlentities($m['work_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT ==================== -->

<!-- SELECT ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_type', 'h_mtrans_type'); ?></b></td>
<td class="fillEE">
<select name="t_type" id="t_type" size="0">
<?php
echo "<option value=\"\"";
if(!$t_type) {
	echo " selected=\"selected\"";
}
echo ">".$l['w_all']."</option>\n";

	$sql = "SELECT * FROM ".K_TABLE_EC_TRANSACTIONS_TYPES." ORDER BY transtype_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			$select_name = unserialize($m['transtype_name']);
			echo "<option value=\"".$m['transtype_id']."\"";
			if($m['transtype_id'] == $t_type) {
				echo " selected=\"selected\"";
			}
			echo ">".htmlentities($select_name[$selected_language], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
		}
	}
	else {
		F_display_db_error();
	}
?>
</select>
</td>
</tr>
<!-- END SELECT ==================== -->

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_type', ''); ?></b></td>
<td class="fillOE">
<select name="t_virtual" id="t_virtual" size="0">
<?php
echo "<option value=\"-1\"";
if($t_virtual < 0) {
	echo " selected=\"selected\"";
}
echo ">".$l['w_all']."</option>\n";

echo "<option value=\"0\"";
if($t_virtual == 0) {
	echo " selected=\"selected\"";
}
echo ">".$l['w_real']."</option>\n";

echo "<option value=\"1\"";
if($t_virtual > 0) {
	echo " selected=\"selected\"";
}
echo ">".$l['w_virtual']."</option>\n";
?>
</select>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_supplier', ''); ?></b></td>
<td class="fillEE">
<select name="t_supplier" id="t_supplier" size="0">
<?php
echo "<option value=\"\"";
if(!$t_supplier) {
	echo " selected=\"selected\"";
}
echo ">".$l['w_all']."</option>\n";

$sql = "SELECT * FROM ".K_TABLE_USERS_COMPANY." WHERE company_supplier=1 ORDER BY company_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['company_userid']."\"";
		if($m['company_userid'] == $t_supplier) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['company_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_direction', 'h_mtrans_direction'); ?></b></td>
<td class="fillOE">
<select name="t_direction" id="t_direction" size="0">
<?php
echo "<option value=\"0\"";
if($t_direction == 0) {
	echo " selected=\"selected\"";
}
echo ">".$l['w_all']."</option>\n";

echo "<option value=\"+1\"";
if($t_direction > 0) {
	echo " selected=\"selected\"";
}
echo ">".$l['w_in']." (+)</option>\n";

echo "<option value=\"-1\"";
if($t_direction < 0) {
	echo " selected=\"selected\"";
}
echo ">".$l['w_out']." (-)</option>\n";
?>
</select>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_paid', ''); ?></b></td>
<td class="fillEE">
<select name="t_paid" id="t_paid" size="0">
<?php
echo "<option value=\"0\"";
if($t_paid == 0) {
	echo " selected=\"selected\"";
}
echo ">".$l['w_all']."</option>\n";

echo "<option value=\"1\"";
if($t_paid == 1) {
	echo " selected=\"selected\"";
}
echo ">".$l['w_paid']."</option>\n";

echo "<option value=\"2\"";
if($t_paid == 2) {
	echo " selected=\"selected\"";
}
echo ">".$l['w_unpaid']."</option>\n";

echo "<option value=\"3\"";
if($t_paid == 3) {
	echo " selected=\"selected\"";
}
echo ">".$l['w_partial']."</option>\n";

echo "<option value=\"4\"";
if($t_paid == 4) {
	echo " selected=\"selected\"";
}
echo ">".$l['w_paid']." + ".$l['w_partial']."</option>\n";

echo "<option value=\"5\"";
if($t_paid == 5) {
	echo " selected=\"selected\"";
}
echo ">".$l['w_unpaid']." + ".$l['w_partial']."</option>\n";
?>
</select>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_payment_category', 'h_paycat_select'); ?></b></td>
<td class="fillOE">
<select name="t_paytype_category_id" id="t_paytype_category_id" size="0">
<?php
	echo "<option value=\"\"";
	if(!$t_paytype_category_id) {
		echo " selected=\"selected\"";
	}
	echo ">".$l['w_all']."</option>\n";
	
	$sql = "SELECT * FROM ".K_TABLE_EC_PAYMENTS_CATEGORIES." ORDER BY paycat_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			$select_name = unserialize($m['paycat_name']);
			echo "<option value=\"".$m['paycat_id']."\"";
			if($m['paycat_id'] == $t_paytype_category_id) {
				echo " selected=\"selected\"";
			}
			echo ">".htmlentities($select_name[$selected_language], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
		}
	}
	else {
		F_display_db_error();
	}
?>
</select>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_payment', 'h_paytype_select'); ?></b></td>
<td class="fillEE">
<select name="t_payment_type_id" id="t_payment_type_id" size="0">
<?php
echo "<option value=\"\"";
if(!$t_payment_type_id) {
	echo " selected=\"selected\"";
}
echo ">".$l['w_all']."</option>\n";

	$sql = "SELECT * FROM ".K_TABLE_EC_PAYMENTS_TYPES." ORDER BY paytype_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			$select_name = unserialize($m['paytype_name']);
			echo "<option value=\"".$m['paytype_id']."\"";
			if($m['paytype_id'] == $t_payment_type_id) {
				echo " selected=\"selected\"";
			}
			echo ">".htmlentities($select_name[$selected_language], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
		}
	}
	else {
		F_display_db_error();
	}
?>
</select>
</td>
</tr>

<?php $doc_charset = F_word_language($selected_language, "a_meta_charset"); ?>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b><?php echo F_display_field_name('w_keywords', 'h_search_description'); ?></b>
</td>
<?php
$current_ta_code = $t_description;
if(!$formstatus) {
	$current_ta_code = stripslashes($current_ta_code);
}
?>
<td class="fillOE">
<textarea cols="30" rows="3" name="t_description" id="t_description"><?php echo htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset); ?></textarea>
</td>
</tr>

</table>

</td>

<td class="edge" valign="top">
<!-- display options (hide/show some fields) -->
<table class="fill" border="0" cellspacing="2" cellpadding="1">

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_work', ''); ?></b></td>
<td class="fillOE">
<?php
echo "<input type=\"checkbox\" name=\"t_option[0]\" id=\"t_option_0\" value=\"1\"";
if ($t_option[0]) { echo "checked";}
echo" />";
?>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_type', ''); ?></b></td>
<td class="fillEE">
<?php
echo "<input type=\"checkbox\" name=\"t_option[1]\" id=\"t_option_1\" value=\"1\"";
if ($t_option[1]) { echo "checked";}
echo" />";
?>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_description', ''); ?></b></td>
<td class="fillOE">
<?php
echo "<input type=\"checkbox\" name=\"t_option[2]\" id=\"t_option_2\" value=\"1\"";
if ($t_option[2]) { echo "checked";}
echo" />";
?>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_doc_referer', ''); ?></b></td>
<td class="fillEE">
<?php
echo "<input type=\"checkbox\" name=\"t_option[3]\" id=\"t_option_3\" value=\"1\"";
if ($t_option[3]) { echo "checked";}
echo" />";
?>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_supplier', ''); ?></b></td>
<td class="fillOE">
<?php
echo "<input type=\"checkbox\" name=\"t_option[4]\" id=\"t_option_4\" value=\"1\"";
if ($t_option[4]) { echo "checked";}
echo" />";
?>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_payment', ''); ?></b></td>
<td class="fillEE">
<?php
echo "<input type=\"checkbox\" name=\"t_option[5]\" id=\"t_option_5\" value=\"1\"";
if ($t_option[5]) { echo "checked";}
echo" />";
?>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_payment_details', ''); ?></b></td>
<td class="fillOE">
<?php
echo "<input type=\"checkbox\" name=\"t_option[6]\" id=\"t_option_6\" value=\"1\"";
if ($t_option[6]) { echo "checked";}
echo" />";
?>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_difference', ''); ?></b></td>
<td class="fillEE">
<?php
echo "<input type=\"checkbox\" name=\"t_option[7]\" id=\"t_option_7\" value=\"1\"";
if ($t_option[7]) { echo "checked";}
echo" />";
?>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_net', ''); ?></b></td>
<td class="fillOE">
<?php
echo "<input type=\"checkbox\" name=\"t_option[8]\" id=\"t_option_8\" value=\"1\"";
if ($t_option[8]) { echo "checked";}
echo" />";
?>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_ec_tax', ''); ?></b></td>
<td class="fillEE">
<?php
echo "<input type=\"checkbox\" name=\"t_option[9]\" id=\"t_option_9\" value=\"1\"";
if ($t_option[9]) { echo "checked";}
echo" />";
?>
</td>
</tr>

</table>

</td>

</tr>

<tr class="edge">
<td class="edge" align="center" colspan="2">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<input type="hidden" name="t_option[10]" id="t_option_10" value="1" />
<?php //show buttons
F_submit_button("form_transactionselectshow","menu_mode",$l['w_update']);
?>
</td>
</tr>
</table>
 
<br />

<!-- SHOW links ==================== -->
<?php
F_display_transaction_details($sql_query, $t_mode, $t_option, $t_filter);
?>
<!-- END SHOW links ==================== -->


</form>
<!-- ====================================================== -->
<?php
}

// ----------------------------------------------------------
// display transactions details
// ----------------------------------------------------------
function F_display_transaction_details($sql, $display_mode, $display_option, $filter_values) {
	global $db, $l, $selected_language;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	
?>

<!-- DISPLAY DOCUMENT'S PRODUCTS LIST -->
<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<tr class="fill">
<th class="fillE">&nbsp;</th>
<th class="fillO">&nbsp;</th>
<th class="fillE">&nbsp;</th>
<th class="fillO">&nbsp;</th>
<th class="fillE">&nbsp;</th>
<th class="fillO">&nbsp;</th>
<th class="fillE">&nbsp;</th>
<th class="fillO">&nbsp;</th>
<th class="fillE">&nbsp;</th>
<th class="fillO">&nbsp;</th>
<th class="fillO">&nbsp;</th>
<th class="fillE" colspan="3"><?php echo $l['w_total']; ?></th>
<th class="fillO" colspan="3"><?php echo $l['w_paid']; ?></th>
<?php
if ($display_option[7]) {
	echo "<th class=\"fillE\" colspan=\"3\">".$l['w_difference']."</th>";
}
else {
	echo "<th class=\"fillE\" colspan=\"3\">&nbsp;</th>";
}
?>
</tr>

<tr class="fill">
<th class="fillE">&nbsp;</th>
<th class="fillO">&nbsp;</th>
<th class="fillE">&nbsp;</th>
<th class="fillO"><?php echo $l['w_num']; ?></th>
<th class="fillE"><?php echo $l['w_date']; ?></th>
<?php
if ($display_option[0]) {
	echo "<th class=\"fillO\">".$l['w_work']."</th>";
}
else {
	echo "<th class=\"fillO\">&nbsp;</th>";
}

if ($display_option[1]) {
	echo "<th class=\"fillE\">".$l['w_type']."</th>";
}
else {
	echo "<th class=\"fillE\">&nbsp;</th>";
}

if ($display_option[2]) {
	echo "<th class=\"fillO\">".$l['w_description']."</th>";
}
else {
	echo "<th class=\"fillO\">&nbsp;</th>";
}

if ($display_option[3]) {
	echo "<th class=\"fillE\">".$l['w_refer']."</th>";
}
else {
	echo "<th class=\"fillE\">&nbsp;</th>";
}

if ($display_option[4]) {
	echo "<th class=\"fillO\">".$l['w_supplier']."</th>";
}
else {
	echo "<th class=\"fillO\">&nbsp;</th>";
}
?>

<th class="fillE"><?php echo $l['w_ec_tax']." [%]"; ?></th>
<th class="fillO"><?php echo $l['w_amount']." [".K_MONEY_CURRENCY_UNICODE_SYMBOL."]"; ?></th>

<?php
if ($display_option[8]) {
	echo "<th class=\"fillE\">".$l['w_net']." [".K_MONEY_CURRENCY_UNICODE_SYMBOL."]</th>";
}
else {
	echo "<th class=\"fillE\">&nbsp;</th>";
}

if ($display_option[9]) {
	echo "<th class=\"fillO\">".$l['w_ec_tax']." [".K_MONEY_CURRENCY_UNICODE_SYMBOL."]</th>";
}
else {
	echo "<th class=\"fillO\">&nbsp;</th>";
}
?>

<th class="fillE"><?php echo $l['w_amount']." [".K_MONEY_CURRENCY_UNICODE_SYMBOL."]"; ?></th>

<?php
if ($display_option[8]) {
	echo "<th class=\"fillO\">".$l['w_net']." [".K_MONEY_CURRENCY_UNICODE_SYMBOL."]</th>";
}
else {
	echo "<th class=\"fillO\">&nbsp;</th>";
}

if ($display_option[9]) {
	echo "<th class=\"fillE\">".$l['w_ec_tax']." [".K_MONEY_CURRENCY_UNICODE_SYMBOL."]</th>";
}
else {
	echo "<th class=\"fillE\">&nbsp;</th>";
}


if ($display_option[7]) {
	echo "<th class=\"fillO\">".$l['w_amount']." [".K_MONEY_CURRENCY_UNICODE_SYMBOL."]</th>";
	
	if ($display_option[8]) {
		echo "<th class=\"fillE\">".$l['w_net']." [".K_MONEY_CURRENCY_UNICODE_SYMBOL."]</th>";
	}
	else {
		echo "<th class=\"fillE\">&nbsp;</th>";
	}
	
	if ($display_option[9]) {
		echo "<th class=\"fillO\">".$l['w_ec_tax']." [".K_MONEY_CURRENCY_UNICODE_SYMBOL."]</th>";
	}
	else {
		echo "<th class=\"fillO\">&nbsp;</th>";
	}
}
else {
	echo "<th class=\"fill0\" colspan=\"3\">&nbsp;</th>";
}
?>
</tr>

<!-- SELECT ==================== -->
<?php
//initialize variables
$rowclass = "O";
$rowodd = 0;

$total_amount_in = 0;
$total_tax_in = 0;

$paid_amount_in = 0;
$paid_tax_in = 0;

$total_amount_out = 0;
$total_tax_out = 0;

$paid_amount_out = 0;
$paid_tax_out = 0;

$previous_transition_id = 0;

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		
		if ($m['transpay_amount'] > 0) {
			$this_paid_amount = ($m['mtrans_direction'] * $m['transpay_amount']);
			$this_paid_tax = $this_paid_amount * ($m['mtrans_tax'] / (100 + $m['mtrans_tax']));
			$this_paid_net = $this_paid_amount - $this_paid_tax;
			
			if ($m['mtrans_direction'] > 0) {
				$paid_amount_in += $this_paid_amount;
				$paid_tax_in += $this_paid_tax;
			}
			else {
				$paid_amount_out += $this_paid_amount;
				$paid_tax_out += $this_paid_tax;
			}
		}
		
		if ($m['mtrans_id'] != $previous_transition_id) { //write unique transaction only one time
			$previous_transition_id = $m['mtrans_id'];
			
			//change style for each transaction row
			if (isset($rowodd) AND ($rowodd)) {
				$rowclass = "O";
				$rowodd=0;
			} else {
				$rowclass = "E";
				$rowodd=1;
			}
			
			// calculate totals
			$this_total_amount = ($m['mtrans_direction'] * $m['mtrans_amount']);
			$this_tax = $this_total_amount * ($m['mtrans_tax'] / (100 + $m['mtrans_tax']));
			$this_net = $this_total_amount - $this_tax;
			
			if ($m['mtrans_direction'] > 0) {
				$total_amount_in += $this_total_amount;
				$total_tax_in += $this_tax;
			}
			else {
				$total_amount_out += $this_total_amount;
				$total_tax_out += $this_tax;
			}
			
			// calculate paid totals
			$this_total_paid_amount = ($m['mtrans_direction'] * $m['mtrans_paid_amount']);
			$this_total_paid_tax = $this_total_paid_amount * ($m['mtrans_tax'] / (100 + $m['mtrans_tax']));
			$this_total_paid_net = $this_total_paid_amount - $this_total_paid_tax;
			
			// start table row
			echo "<tr class=\"fill".$rowclass."\">";
			
			//virtual
			echo "<td class=\"fill".$rowclass."O\" valign=\"top\">";
			if ($m['mtrans_virtual']) {
				echo "<img src=\"../../images/dots/ball/y/10.gif\" alt=\"".$l['w_virtual']."\" width=\"10\" height=\"10\" border=\"0\">";
			}
			else {
				echo "<img src=\"../../images/dots/ball/m/10.gif\" alt=\"".$l['w_real']."\" width=\"10\" height=\"10\" border=\"0\">";
			}
			echo "</td>";
			
			//unpaid
			echo "<td class=\"fill".$rowclass."E\" valign=\"top\">";
			if (($this_total_amount - $this_total_paid_amount) != 0) {
				echo "<img src=\"../../images/dots/ball/r/10.gif\" alt=\"".$l['w_unpaid']."\" width=\"10\" height=\"10\" border=\"0\">";
			}
			else {
				echo "<img src=\"../../images/dots/ball/g/10.gif\" alt=\"".$l['w_paid']."\" width=\"10\" height=\"10\" border=\"0\">";
			}
			echo "</td>";
			
			// direction
			echo "<td class=\"fill".$rowclass."E\" valign=\"top\" align=\"center\">";
			if ($m['mtrans_direction'] < 0) {
				echo "<b>-</b>";
			}
			else {
				echo "<b>+</b>";
			}
			echo "</td>";
			
			echo "<td class=\"fill".$rowclass."O\" valign=\"top\" align=\"right\">&nbsp;<a href=\"cp_edit_ec_transactions.".CP_EXT."?tid=".$m['mtrans_id']."\">".$m['mtrans_id']."</a></td>";
			
			echo "<td class=\"fill".$rowclass."E\" valign=\"top\" style=\"white-space:nowrap\">&nbsp;<a href=\"cp_edit_ec_transactions.".CP_EXT."?tid=".$m['mtrans_id']."\">".$m['mtrans_date']."</a></td>";
			
			if ($display_option[0]) {
				// work
				echo "<td class=\"fill".$rowclass."O\" valign=\"top\">&nbsp;";
				$sqlwrk = "SELECT * FROM ".K_TABLE_EC_WORKS." WHERE work_id='".$m['mtrans_work_id']."' LIMIT 1";
				if($rwrk = F_aiocpdb_query($sqlwrk, $db)) {
					if($mwrk = F_aiocpdb_fetch_array($rwrk)) {
						echo "<a href=\"../code/cp_edit_ec_works.".CP_EXT."?work_id=".$mwrk['work_id']."\">".htmlentities($mwrk['work_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</a>";
					}
				}
				else {
					F_display_db_error();
				}
				echo "</td>";
			}
			else {
				echo "<td class=\"fill".$rowclass."O\" valign=\"top\">&nbsp;</td>";
			}
			
			if ($display_option[1]) {
				// transaction type
				echo "<td class=\"fill".$rowclass."E\" valign=\"top\">&nbsp;";
				$sqltt = "SELECT * FROM ".K_TABLE_EC_TRANSACTIONS_TYPES." WHERE transtype_id='".$m['mtrans_type']."' LIMIT 1";
				if($rtt = F_aiocpdb_query($sqltt, $db)) {
					if($mtt = F_aiocpdb_fetch_array($rtt)) {
						$transaction_type_name = unserialize($mtt['transtype_name']);
						echo htmlentities($transaction_type_name[$selected_language], ENT_NOQUOTES, $l['a_meta_charset']);
					}
				}
				else {
					F_display_db_error();
				}
				echo "</td>";
			}
			else {
				echo "<td class=\"fill".$rowclass."E\" valign=\"top\">&nbsp;</td>";
			}
			
			if ($display_option[2]) {
				echo "<td class=\"fill".$rowclass."O\" valign=\"top\">&nbsp;".htmlentities(nl2br($m['mtrans_description']), ENT_NOQUOTES, $l['a_meta_charset'])."</td>";
			}
			else {
				echo "<td class=\"fill".$rowclass."O\" valign=\"top\">&nbsp;</td>";
			}
			
			if ($display_option[3]) {
				echo "<td class=\"fill".$rowclass."E\" valign=\"top\">&nbsp;".$m['mtrans_doc_ref']."</td>";
			}
			else {
				echo "<td class=\"fill".$rowclass."E\" valign=\"top\">&nbsp;</td>";
			}
			
			if ($display_option[4]) {
				echo "<td class=\"fill".$rowclass."O\" valign=\"top\">&nbsp;";
				if ($m['mtrans_supplier']) {
					$sqlsup = "SELECT * FROM ".K_TABLE_USERS_COMPANY." WHERE company_userid=".$m['mtrans_supplier']." LIMIT 1";
					if($rsup = F_aiocpdb_query($sqlsup, $db)) {
						if($msup = F_aiocpdb_fetch_array($rsup)) {
							echo "<a href=\"../code/cp_edit_user.".CP_EXT."?uemode=company&amp;user_id=".$m['mtrans_supplier']."\">".htmlentities($msup['company_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</a>";
						}
					}
					else {
						F_display_db_error();
					}
				}
				echo "</td>";
			}
			else {
				echo "<td class=\"fill".$rowclass."O\" valign=\"top\">&nbsp;</td>";
			}
			
			echo "<td class=\"fill".$rowclass."E\" valign=\"top\" align=\"right\">&nbsp;".$m['mtrans_tax']."</td>";
			
			//total
			echo "<td class=\"fill".$rowclass."O\" valign=\"top\" align=\"right\" style=\"white-space:nowrap\">&nbsp;".F_FormatCurrency($this_total_amount)."</td>";
			
			if ($display_option[8]) {
				echo "<td class=\"fill".$rowclass."E\" valign=\"top\" align=\"right\" style=\"white-space:nowrap\">&nbsp;".F_FormatCurrency($this_net)."</td>";
			}
			else {
				echo "<td class=\"fill".$rowclass."E\" valign=\"top\" align=\"right\">&nbsp;</td>";
			}
			
			if ($display_option[9]) {
				echo "<td class=\"fill".$rowclass."O\" valign=\"top\" align=\"right\" style=\"white-space:nowrap\">&nbsp;".F_FormatCurrency($this_tax)."</td>";
			}
			else {
				echo "<td class=\"fill".$rowclass."O\" valign=\"top\" align=\"right\">&nbsp;</td>";
			}
			
			//paid
			echo "<td class=\"fill".$rowclass."E\" valign=\"top\" align=\"right\" style=\"white-space:nowrap\">&nbsp;".F_FormatCurrency($this_total_paid_amount)."</td>";
			
			if ($display_option[8]) {
				echo "<td class=\"fill".$rowclass."O\" valign=\"top\" align=\"right\" style=\"white-space:nowrap\">&nbsp;".F_FormatCurrency($this_total_paid_net)."</td>";
			}
			else {
				echo "<td class=\"fill".$rowclass."O\" valign=\"top\" align=\"right\">&nbsp;</td>";
			}
			
			if ($display_option[9]) {
				echo "<td class=\"fill".$rowclass."E\" valign=\"top\" align=\"right\" style=\"white-space:nowrap\">&nbsp;".F_FormatCurrency($this_total_paid_tax)."</td>";
			}
			else {
				echo "<td class=\"fill".$rowclass."E\" valign=\"top\" align=\"right\">&nbsp;</td>";
			}
			
			if ($display_option[7]) {
				//difference
				echo "<td class=\"fill".$rowclass."O\" valign=\"top\" align=\"right\" style=\"white-space:nowrap\">&nbsp;".F_FormatCurrency($this_total_amount - $this_total_paid_amount)."</td>";
				
				if ($display_option[8]) {
					echo "<td class=\"fill".$rowclass."E\" valign=\"top\" align=\"right\" style=\"white-space:nowrap\">&nbsp;".F_FormatCurrency($this_net - $this_total_paid_net)."</td>";
				}
				else {
					echo "<td class=\"fill".$rowclass."E\" valign=\"top\" align=\"right\">&nbsp;</td>";
				}
				
				if ($display_option[9]) {
					echo "<td class=\"fill".$rowclass."O\" valign=\"top\" align=\"right\" style=\"white-space:nowrap\">&nbsp;".F_FormatCurrency($this_tax - $this_total_paid_tax)."</td>";
				}
				else {
					echo "<td class=\"fill".$rowclass."O\" valign=\"top\" align=\"right\">&nbsp;</td>";
				}
			}
			else {
				echo "<td class=\"fill".$rowclass."O\" colspan=\"3\"valign=\"top\">&nbsp;</td>";
			}
			
		} //end unique transaction
		
		// start new table row for payment data
		if ($m['transpay_date']) {
			echo "</tr>";
			echo "<tr class=\"fill".$rowclass."\">";
			
			echo "<td class=\"fill".$rowclass."O\" valign=\"top\" colspan=\"4\">&nbsp;</td>";
			
			echo "<td class=\"fill".$rowclass."E\" valign=\"top\" style=\"white-space:nowrap\">&nbsp;".$m['transpay_date']."</td>";
			
			echo "<td class=\"fill".$rowclass."O\" valign=\"top\" colspan=\"9\">&nbsp;";
			
			if ($display_option[5]) {
				//get payment type name
				$sqlpay = "SELECT * FROM ".K_TABLE_EC_PAYMENTS_TYPES." WHERE paytype_id='".$m['transpay_payment_id']."' LIMIT 1";
				if($rpay = F_aiocpdb_query($sqlpay, $db)) {
					if($mpay = F_aiocpdb_fetch_array($rpay)) {
						$payment_name = unserialize($mpay['paytype_name']);
						echo "".$payment_name[$selected_language];
					}
				}
				else {
					F_display_db_error();
				}
			}
			if ($display_option[6]) {
				echo " - ".htmlentities($m['transpay_payment_details'], ENT_NOQUOTES, $l['a_meta_charset'])."";
			}
			echo "</td>";
			echo "<td class=\"fill".$rowclass."O\" valign=\"top\" align=\"right\" style=\"white-space:nowrap\">&nbsp;".F_FormatCurrency($this_paid_amount)."</td>";
			
			if ($display_option[8]) {
				echo "<td class=\"fill".$rowclass."E\" valign=\"top\" align=\"right\" style=\"white-space:nowrap\">&nbsp;".F_FormatCurrency($this_paid_net)."</td>";
			}
			else {
				echo "<td class=\"fill".$rowclass."E\" valign=\"top\" align=\"right\">&nbsp;</td>";
			}
			
			if ($display_option[9]) {
				echo "<td class=\"fill".$rowclass."O\" valign=\"top\" align=\"right\" style=\"white-space:nowrap\">&nbsp;".F_FormatCurrency($this_paid_tax)."</td>";
			}
			else {
				echo "<td class=\"fill".$rowclass."O\" valign=\"top\" align=\"right\">&nbsp;</td>";
			}
			
			echo "<td class=\"fill".$rowclass."E\" colspan=\"3\">&nbsp;</td>";
		}
		
		echo "</tr>";
	}
}
else {
	F_display_db_error();
}

//display totals
	
	$paid_net_in = $paid_amount_in - $paid_tax_in;
	$paid_net_out = $paid_amount_out - $paid_tax_out;
	
	echo "<tr class=\"fill\"><th class=\"fill\" colspan=\"20\"><hr /></th></tr>\n";
	
	//in (+)
	echo "<tr>";
	echo "<td class=\"fillOO\" align=\"right\" colspan=\"11\"><b>".$l['w_total']." ".$l['w_in']."</b>&nbsp;</td>";
	
	if (!$display_mode) {
		$total_net_in = $total_amount_in - $total_tax_in;
		$total_net_out = $total_amount_out - $total_tax_out;
		
		echo "<td class=\"fillOE\" align=\"right\" style=\"white-space:nowrap\">&nbsp;".F_FormatCurrency($total_amount_in)."</td>";
		
		if ($display_option[8]) {
			echo "<td class=\"fillOO\" align=\"right\" style=\"white-space:nowrap\">&nbsp;".F_FormatCurrency($total_net_in)."</td>";
		}
		else {
			echo "<td class=\"fillOO\" align=\"right\">&nbsp;</td>";
		}
		
		if ($display_option[9]) {
			echo "<td class=\"fillOE\" align=\"right\" style=\"white-space:nowrap\">&nbsp;".F_FormatCurrency($total_tax_in)."</td>";
		}
		else {
			echo "<td class=\"fillOE\" align=\"right\">&nbsp;</td>";
		}
	}
	else {
		echo "<td class=\"fillOE\" colspan=\"3\">&nbsp;</td>";
	}
	
	echo "<td class=\"fillOO\" align=\"right\" style=\"white-space:nowrap\">&nbsp;".F_FormatCurrency($paid_amount_in)."</td>";
	
	if ($display_option[8]) {
		echo "<td class=\"fillOE\" align=\"right\" style=\"white-space:nowrap\">&nbsp;".F_FormatCurrency($paid_net_in)."</td>";
	}
	else {
		echo "<td class=\"fillOE\" align=\"right\">&nbsp;</td>";
	}
	
	if ($display_option[9]) {
		echo "<td class=\"fillOO\" align=\"right\" style=\"white-space:nowrap\">&nbsp;".F_FormatCurrency($paid_tax_in)."</td>";
	}
	else {
		echo "<td class=\"fillOO\" align=\"right\">&nbsp;</td>";
	}
	
	if ((!$display_mode) AND ($display_option[7]) ) {
		echo "<td class=\"fillOE\" align=\"right\" style=\"white-space:nowrap\">&nbsp;".F_FormatCurrency($total_amount_in - $paid_amount_in)."</td>";
		
		if ($display_option[8]) {
			echo "<td class=\"fillOO\" align=\"right\" style=\"white-space:nowrap\">&nbsp;".F_FormatCurrency($total_net_in - $paid_net_in)."</td>";
		}
		else {
			echo "<td class=\"fillOO\" align=\"right\">&nbsp;</td>";
		}
		
		if ($display_option[9]) {
			echo "<td class=\"fillOE\" align=\"right\" style=\"white-space:nowrap\">&nbsp;".F_FormatCurrency($total_tax_in - $paid_tax_in)."</td>";
		}
		else {
			echo "<td class=\"fillOE\" align=\"right\">&nbsp;</td>";
		}
	}
	else {
		echo "<td class=\"fillOE\" colspan=\"3\">&nbsp;</td>";
	}
	echo "</tr>";
	
	//out (-)
	echo "<tr>";
	echo "<td class=\"fillOO\" align=\"right\" colspan=\"11\"><b>".$l['w_total']." ".$l['w_out']."</b>&nbsp;</td>";
	
	if (!$display_mode) {
		echo "<td class=\"fillOE\" align=\"right\" style=\"white-space:nowrap\">&nbsp;".F_FormatCurrency($total_amount_out)."</td>";
		
		if ($display_option[8]) {
			echo "<td class=\"fillOO\" align=\"right\" style=\"white-space:nowrap\">&nbsp;".F_FormatCurrency($total_net_out)."</td>";
		}
		else {
			echo "<td class=\"fillOO\" align=\"right\">&nbsp;</td>";
		}
		
		if ($display_option[9]) {
			echo "<td class=\"fillOE\" align=\"right\" style=\"white-space:nowrap\">&nbsp;".F_FormatCurrency($total_tax_out)."</td>";
		}
		else {
			echo "<td class=\"fillOE\" align=\"right\">&nbsp;</td>";
		}
	}
	else {
		echo "<td class=\"fillOE\" colspan=\"3\">&nbsp;</td>";
	}
	
	echo "<td class=\"fillOO\" align=\"right\" style=\"white-space:nowrap\">&nbsp;".F_FormatCurrency($paid_amount_out)."</td>";
	
	if ($display_option[8]) {
		echo "<td class=\"fillOE\" align=\"right\" style=\"white-space:nowrap\">&nbsp;".F_FormatCurrency($paid_net_out)."</td>";
	}
	else {
		echo "<td class=\"fillOE\" align=\"right\">&nbsp;</td>";
	}
	
	if ($display_option[9]) {
		echo "<td class=\"fillOO\" align=\"right\" style=\"white-space:nowrap\">&nbsp;".F_FormatCurrency($paid_tax_out)."</td>";
	}
	else {
		echo "<td class=\"fillOO\" align=\"right\">&nbsp;</td>";
	}
	
	if ( (!$display_mode) AND ($display_option[7]) ) {
		echo "<td class=\"fillOE\" align=\"right\" style=\"white-space:nowrap\">&nbsp;".F_FormatCurrency($total_amount_out - $paid_amount_out)."</td>";
		
		if ($display_option[8]) {
			echo "<td class=\"fillOO\" align=\"right\" style=\"white-space:nowrap\">&nbsp;".F_FormatCurrency($total_net_out - $paid_net_out)."</td>";
		}
		else {
			echo "<td class=\"fillOO\" align=\"right\">&nbsp;</td>";
		}
		
		if ($display_option[9]) {
			echo "<td class=\"fillOE\" align=\"right\" style=\"white-space:nowrap\">&nbsp;".F_FormatCurrency($total_tax_out - $paid_tax_out)."</td>";
		}
		else {
			echo "<td class=\"fillOE\" align=\"right\">&nbsp;</td>";
		}
	}
	else {
		echo "<td class=\"fillOE\" colspan=\"3\">&nbsp;</td>";
	}
	echo "</tr>";
	
	echo "<tr>";
	echo "<td class=\"fillOO\" align=\"right\" colspan=\"11\">&nbsp;</td>";
	echo "<td class=\"fillOO\" align=\"right\" colspan=\"9\"><hr /></td>";
	echo "</tr>";
	
	//difference (in - out)
	echo "<tr>";
	echo "<td class=\"fillOO\" align=\"right\" colspan=\"11\"><b>".$l['w_difference']."</b>&nbsp;</td>";
	
	if (!$display_mode) {
		echo "<td class=\"fillOE\" align=\"right\" style=\"white-space:nowrap\">&nbsp;".F_FormatCurrency($total_amount_in + $total_amount_out)."</td>";
		
		if ($display_option[8]) {
			echo "<td class=\"fillOO\" align=\"right\" style=\"white-space:nowrap\">&nbsp;".F_FormatCurrency($total_net_in + $total_net_out)."</td>";
		}
		else {
			echo "<td class=\"fillOO\" align=\"right\">&nbsp;</td>";
		}
		
		if ($display_option[9]) {
			echo "<td class=\"fillOE\" align=\"right\" style=\"white-space:nowrap\">&nbsp;".F_FormatCurrency($total_tax_in + $total_tax_out)."</td>";
		}
		else {
			echo "<td class=\"fillOE\" align=\"right\">&nbsp;</td>";
		}
	}
	else {
		echo "<td class=\"fillOE\" colspan=\"3\">&nbsp;</td>";
	}
	
	echo "<td class=\"fillOO\" align=\"right\" style=\"white-space:nowrap\">&nbsp;".F_FormatCurrency($paid_amount_in + $paid_amount_out)."</td>";
	
	if ($display_option[8]) {
		echo "<td class=\"fillOE\" align=\"right\" style=\"white-space:nowrap\">&nbsp;".F_FormatCurrency($paid_net_in + $paid_net_out)."</td>";
	}
	else {
		echo "<td class=\"fillOE\" align=\"right\">&nbsp;</td>";
	}
	
	if ($display_option[9]) {
		echo "<td class=\"fillOO\" align=\"right\" style=\"white-space:nowrap\">&nbsp;".F_FormatCurrency($paid_tax_in + $paid_tax_out)."</td>";
	}
	else {
		echo "<td class=\"fillOO\" align=\"right\">&nbsp;</td>";
	}
	
	echo "<td class=\"fillOE\" colspan=\"3\">&nbsp;</td>";
	echo "</tr>";
?>
</table>

</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<?php
//generate a verification code to avoid unauthorized calls to PDF viewer
$verifycode = F_generate_verification_code($sql, 4);
F_generic_button("pdftransaction", $l['w_display_document'], "PDFTRN=window.open('cp_show_ec_pdf_transactions.".CP_EXT."?tsql=".urlencode($sql)."&amp;dt=".$display_mode."&amp;do=".urlencode(serialize($display_option))."&amp;vc=".$verifycode."&amp;selected_language=".$selected_language."&amp;tf=".$filter_values."','PDFTRN','menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')");
?>
</td>
</tr>

</table>
<?php
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
