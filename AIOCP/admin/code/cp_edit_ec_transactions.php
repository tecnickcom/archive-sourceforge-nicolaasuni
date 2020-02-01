<?php
//============================================================+
// File name   : cp_edit_ec_transactions.php                   
// Begin       : 2002-06-24                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit Money Transactions                       
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
require_once('../../shared/code/cp_functions_form.'.CP_EXT);

$pagelevel = K_AUTH_ADMIN_CP_EDIT_EC_TRANSACTIONS;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_money_transactions_editor'];

if (isset($_REQUEST['tid'])) {
	$mtrans_id = $_REQUEST['tid'];
}


require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages;

if (isset($_REQUEST['menu_mode'])) {
	$menu_mode = $_REQUEST['menu_mode'];
} else {
	$menu_mode = "";
}
switch($menu_mode) {

	case unhtmlentities($l['w_delete']):
	case $l['w_delete']:{
		F_stripslashes_formfields(); // Delete target
		F_print_error("WARNING", $l['m_delete_confirm']);
		?>
		<p><?php echo $l['t_warning'].": ".$l['d_transaction_delete']; ?></p>
		
		<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_delete" id="form_delete">
		<input type="hidden" name="menu_mode" id="menu_mode" value="forcedelete" />
		<input type="hidden" name="mtrans_id" id="mtrans_id" value="<?php echo $mtrans_id; ?>" />
		<input type="hidden" name="forcedelete" id="forcedelete" value="" />
		<?php 
		F_submit_button("form_delete","forcedelete",$l['w_delete']);
		F_submit_button("form_delete","forcedelete",$l['w_cancel']);
		?>
		</form>
		
		<?php
		break;
	}

		case "forcedelete":{ // Delete category and all associated messages and users
		if($forcedelete == $l['w_delete']) { //check if delete button has been pushed (redundant check)
			$sql = "DELETE FROM ".K_TABLE_EC_TRANSACTIONS." WHERE mtrans_id=".$mtrans_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			$sql = "DELETE FROM ".K_TABLE_EC_TRANSACTIONS_PAYMENTS." WHERE transpay_transaction_id=".$mtrans_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			$mtrans_id=FALSE;
		}
		break;
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update target
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_EC_TRANSACTIONS, "mtrans_date='".$mtrans_date."' AND mtrans_description='".$mtrans_description."'" , "mtrans_id", $mtrans_id)) {
				F_print_error("WARNING", $l['m_duplicate_data']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$sql = "UPDATE IGNORE ".K_TABLE_EC_TRANSACTIONS." SET 
				mtrans_date='".$mtrans_date."',
				mtrans_type='".$mtrans_type."',
				mtrans_description='".$mtrans_description."',
				mtrans_doc_ref='".$mtrans_doc_ref."',
				mtrans_supplier='".$mtrans_supplier."',
				mtrans_direction='".$mtrans_direction."',
				mtrans_amount='".$mtrans_amount."',
				mtrans_tax='".$mtrans_tax."',
				mtrans_paid_amount='".$mtrans_paid_amount."',
				mtrans_work_id='".$mtrans_work_id."',
				mtrans_virtual='".$mtrans_virtual."'
				WHERE mtrans_id=".$mtrans_id."";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
			}
		}
		break;
	}

	case unhtmlentities($l['w_add']):
	case $l['w_add']:{ // Add target
		if($formstatus = F_check_form_fields()) {
			//check if mtrans_name is unique
			$sql = "SELECT mtrans_date, mtrans_description FROM ".K_TABLE_EC_TRANSACTIONS." WHERE mtrans_date='".$mtrans_date."' AND mtrans_description='".$mtrans_description."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_data']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				$mtrans_paid_amount = 0;
				
				$sql = "INSERT IGNORE INTO ".K_TABLE_EC_TRANSACTIONS." (
				mtrans_date,
				mtrans_type,
				mtrans_description,
				mtrans_doc_ref,
				mtrans_supplier,
				mtrans_direction,
				mtrans_amount,
				mtrans_tax,
				mtrans_paid_amount,
				mtrans_work_id,
				mtrans_virtual
				) VALUES (
				'".$mtrans_date."',
				'".$mtrans_type."',
				'".$mtrans_description."',
				'".$mtrans_doc_ref."',
				'".$mtrans_supplier."',
				'".$mtrans_direction."',
				'".$mtrans_amount."',
				'".$mtrans_tax."',
				'".$mtrans_paid_amount."',
				'".$mtrans_work_id."',
				'".$mtrans_virtual."'
				)";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$mtrans_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
	}
	
	default :
	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$mtrans_date = gmdate("Y-m-d");
		$mtrans_type = "";
		$mtrans_description = "";
		$mtrans_doc_ref = "";
		$mtrans_supplier = "";
		$mtrans_direction = +1;
		$mtrans_amount = 0;
		$mtrans_tax = 0;
		$mtrans_paid_amount = 0;
		$mtrans_work_id = "";
		$mtrans_virtual = 0;
		break;
	}

} //end of switch

// Initialize variables


$currentyear = gmdate('Y');
$firstyear = $currentyear;
$sql = "SELECT MIN(mtrans_date) FROM ".K_TABLE_EC_TRANSACTIONS."";
if($r = F_aiocpdb_query($sql, $db)) {
	if($m = F_aiocpdb_fetch_array($r)) {
		$firstyear = gmdate('Y',strtotime($m[0]));
	}
}
else {
	F_display_db_error();
}

if ( (!isset($tyear) OR (!$tyear)) OR ($tyear < $firstyear)) {
	$tyear = $currentyear;
}

if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!isset($mtrans_id) OR (!$mtrans_id)) {
			$sql = "SELECT * FROM ".K_TABLE_EC_TRANSACTIONS." WHERE YEAR(mtrans_date)='".$tyear."' ORDER BY mtrans_date DESC LIMIT 1";
		} else {
			$sql = "SELECT * FROM ".K_TABLE_EC_TRANSACTIONS." WHERE mtrans_id=".$mtrans_id." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$mtrans_id = $m['mtrans_id'];
				$mtrans_date = $m['mtrans_date'];
				$mtrans_type = $m['mtrans_type'];
				$mtrans_description = $m['mtrans_description'];
				$mtrans_doc_ref = $m['mtrans_doc_ref'];
				$mtrans_supplier = $m['mtrans_supplier'];
				$mtrans_direction = $m['mtrans_direction'];
				$mtrans_amount = $m['mtrans_amount'];
				$mtrans_tax = $m['mtrans_tax'];
				$mtrans_paid_amount = $m['mtrans_paid_amount'];
				$mtrans_work_id = $m['mtrans_work_id'];
				$mtrans_virtual = $m['mtrans_virtual'];
			}
			else {
				$mtrans_date = gmdate("Y-m-d");
				$mtrans_type = "";
				$mtrans_description = "";
				$mtrans_doc_ref = "";
				$mtrans_supplier = "";
				$mtrans_direction = +1;
				$mtrans_amount = 0;
				$mtrans_tax = 0;
				$mtrans_paid_amount = 0;
				$mtrans_work_id = "";
				$mtrans_virtual = 0;
			}
		}
		else {
			F_display_db_error();
		}
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_moneytransactionseditor" id="form_moneytransactionseditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="mtrans_date" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_date']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT year ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_year', 'h_mtrans_year'); ?></b></td>
<td class="fillOE">
<input type="hidden" name="changeyear" id="changeyear" value="0" />

<select name="tyear" id="tyear" size="0" onchange="document.form_moneytransactionseditor.changeyear.value=1; document.form_moneytransactionseditor.submit()">
<?php
for ($i=$firstyear; $i<=$currentyear; $i++) {
	echo "<option value=\"".$i."\"";
	if ($tyear == $i) {echo " selected=\"selected\"";}
	echo ">".$i."</option>\n";
}
?>
</select>
</td>
</tr>
<!-- END SELECT year ==================== -->

<!-- SELECT ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_transaction', 'h_mtrans_select'); ?></b></td>
<td class="fillEE">
<select name="mtrans_id" id="mtrans_id" size="0" onchange="document.form_moneytransactionseditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_EC_TRANSACTIONS." WHERE YEAR(mtrans_date)='".$tyear."' ORDER BY mtrans_date DESC";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['mtrans_id']."\"";
		if($m['mtrans_id'] == $mtrans_id) {
			echo " selected=\"selected\"";
		}
		echo ">";
		if ($m['mtrans_virtual']) {
			echo "[*]";
		}
		else {
			echo "[&nbsp;]";
		}
		if ($m['mtrans_amount'] != $m['mtrans_paid_amount']) {
			echo "[*]";
		}
		else {
			echo "[&nbsp;]";
		}
		echo " ".$m['mtrans_id']." ";
		echo "".$m['mtrans_date']." ";
		if ($m['mtrans_direction'] > 0) {
			echo "+";
		}
		else {
			echo "-";
		}
		echo "".$m['mtrans_amount']." ".K_MONEY_CURRENCY_UNICODE_SYMBOL."";
		
		echo "</option>\n";
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
<td class="fillOO">&nbsp;</td>
<td class="fillOE"><hr /></td>
</tr>


<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_date', 'h_mtrans_date'); ?></b></td>
<td class="fillEE"><input type="text" name="mtrans_date" id="mtrans_date" value="<?php echo $mtrans_date; ?>" size="30" maxlength="10" />
<input type="hidden" name="x_mtrans_date" id="x_mtrans_date" value="([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})" />
<input type="hidden" name="xl_mtrans_date" id="xl_mtrans_date" value="<?php echo $l['w_date']; ?>" />
</td>
</tr>

<!-- SELECT ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_work', 'h_work_select'); ?></b></td>
<td class="fillOE">
<select name="mtrans_work_id" id="mtrans_work_id" size="0">
<?php
	echo "<option value=\"\"";
	if(!$mtrans_work_id) {
		echo " selected=\"selected\"";
	}
	echo ">&nbsp;</option>\n";
	
$sql = "SELECT * FROM ".K_TABLE_EC_WORKS." WHERE YEAR(work_date_start)='".$tyear."' ORDER BY work_date_start DESC";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['work_id']."\"";
		if($m['work_id'] == $mtrans_work_id) {
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
<select name="mtrans_type" id="mtrans_type" size="0">
<?php
echo "<option value=\"\"";
if(!$mtrans_type) {
	echo " selected=\"selected\"";
}
echo ">&nbsp;</option>\n";
	$sql = "SELECT * FROM ".K_TABLE_EC_TRANSACTIONS_TYPES." ORDER BY transtype_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			$select_name = unserialize($m['transtype_name']);
			echo "<option value=\"".$m['transtype_id']."\"";
			if($m['transtype_id'] == $mtrans_type) {
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
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_virtual', 'h_mtrans_virtual'); ?></b></td>
<td class="fillOE">
<?php
echo "<input type=\"checkbox\" name=\"mtrans_virtual\" id=\"mtrans_virtual\" value=\"1\"";
if ($mtrans_virtual) { echo "checked";}
echo" />";
?>
</td>
</tr>

<?php $doc_charset = F_word_language($selected_language, "a_meta_charset"); ?>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b><?php echo F_display_field_name('w_description', 'h_mtrans_description'); ?></b>
</td>
<?php
$current_ta_code = $mtrans_description;
if(!$formstatus) {
	$current_ta_code = stripslashes($current_ta_code);
}
?>
<td class="fillEE">
<textarea cols="30" rows="3" name="mtrans_description" id="mtrans_description"><?php echo htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset); ?></textarea>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_doc_referer', 'h_mtrans_doc_reference'); ?></b></td>
<td class="fillOE"><input type="text" name="mtrans_doc_ref" id="mtrans_doc_ref" value="<?php echo htmlentities($mtrans_doc_ref, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_supplier', ''); ?></b></td>
<td class="fillEE">
<select name="mtrans_supplier" id="mtrans_supplier" size="0">
<?php
echo "<option value=\"\"";
if(!$mtrans_supplier) {
	echo " selected=\"selected\"";
}
echo ">&nbsp;</option>\n";

$sql = "SELECT * FROM ".K_TABLE_USERS_COMPANY." WHERE company_supplier=1 ORDER BY company_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['company_userid']."\"";
		if($m['company_userid'] == $mtrans_supplier) {
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
<?php
echo "<input type=\"radio\" name=\"mtrans_direction\" value=\"+1\"";
if($mtrans_direction >= 0) {echo " checked=\"checked\"";}
echo " />".$l['w_in']." (+)&nbsp;";

echo "<input type=\"radio\" name=\"mtrans_direction\" value=\"-1\"";
if($mtrans_direction < 0) {echo " checked=\"checked\"";}
echo " />".$l['w_out']." (-)&nbsp;";
?>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_amount', 'h_mtrans_amount'); ?></b></td>
<td class="fillEE"><input type="text" name="mtrans_amount" id="mtrans_amount" value="<?php echo $mtrans_amount; ?>" size="30" maxlength="255" /> <b>[<?php echo K_MONEY_CURRENCY_UNICODE_SYMBOL; ?>]</b></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_ec_tax', 'h_mtrans_tax'); ?></b></td>
<td class="fillOE"><input type="text" name="mtrans_tax" id="mtrans_tax" value="<?php echo $mtrans_tax; ?>" size="30" maxlength="255" /> <b>[%]</b></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_paid_amount', 'h_mtrans_paid_amount'); ?></b></td>
<td class="fillEE"><b><?php echo $mtrans_paid_amount." ".K_MONEY_CURRENCY_UNICODE_SYMBOL; ?></b>
<input type="hidden" name="mtrans_paid_amount" id="mtrans_paid_amount" value="<?php echo $mtrans_paid_amount; ?>" />
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_difference', 'h_mtrans_difference'); ?></b></td>
<td class="fillOE"><b><?php echo $mtrans_amount - $mtrans_paid_amount." ".K_MONEY_CURRENCY_UNICODE_SYMBOL; ?></b></td>
</tr>

<?php
if (isset($mtrans_id) AND ($mtrans_id > 0)) {
?>
<tr class="fillE">
<td class="fillEO" align="right">&nbsp;</td>
<td class="fillEE"><a href="cp_edit_ec_transactions_payments.<?php echo CP_EXT; ?>?tid=<?php echo $mtrans_id; ?>"><b><?php echo $l['t_money_transactions_payments_editor']; ?>&nbsp;&gt;&gt;</b></a></td>
</tr>
<?php
}
?>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if (isset($mtrans_id) AND ($mtrans_id > 0)) {
	F_submit_button("form_moneytransactionseditor","menu_mode",$l['w_update']); 
	F_submit_button("form_moneytransactionseditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_moneytransactionseditor","menu_mode",$l['w_add']); 
F_submit_button("form_moneytransactionseditor","menu_mode",$l['w_clear']); 
?>
</td>

</tr>
</table>
</form>

<p>
<a href="cp_show_ec_transactions.<?php echo CP_EXT; ?>"><b><?php echo $l['t_transactions']; ?>&nbsp;&gt;&gt;</b></a>
</p>
<!-- ====================================================== -->

<!-- Cange focus to mtrans_id select -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_moneytransactionseditor.mtrans_id.focus();
//]]>
</script>
<!-- END Cange focus to mtrans_id select -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
