<?php
//============================================================+
// File name   : cp_edit_ec_transactions.php                   
// Begin       : 2002-06-24                                    
// Last Update : 2003-11-18                                    
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

$thispage_title = $l['t_money_transactions_payments_editor'];

if (isset($_REQUEST['tid'])) {
	$transpay_transaction_id = $_REQUEST['tid'];
}
if (!$transpay_transaction_id) {
	F_print_error("ERROR", $l['m_unauthorized_access']);
	return;
}

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages; ?>
<!-- ====================================================== -->
<?php
switch($menu_mode) {

	case unhtmlentities($l['w_delete']):
	case $l['w_delete']:{
		F_stripslashes_formfields(); // Delete target
		F_print_error("WARNING", $l['m_delete_confirm']);
		?>
		
		<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_delete" id="form_delete">
		<input type="hidden" name="menu_mode" id="menu_mode" value="forcedelete" />
		<input type="hidden" name="transpay_transaction_id" id="transpay_transaction_id" value="<?php echo $transpay_transaction_id; ?>" />
		<input type="hidden" name="transpay_id" id="transpay_id" value="<?php echo $transpay_id; ?>" />
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
			$sql = "DELETE FROM ".K_TABLE_EC_TRANSACTIONS_PAYMENTS." WHERE transpay_id=".$transpay_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			$transpay_id=FALSE;
			F_update_tansaction_paid_amount($transpay_transaction_id);
		}
		break;
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update target
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_EC_TRANSACTIONS_PAYMENTS, "transpay_date='".$transpay_date."' AND transpay_transaction_id='".$transpay_transaction_id."' AND transpay_payment_id='".$transpay_payment_id."' AND transpay_amount='".$transpay_amount."' " , "transpay_id", $transpay_id)) {
				F_print_error("WARNING", $l['m_duplicate_data']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$sql = "UPDATE IGNORE ".K_TABLE_EC_TRANSACTIONS_PAYMENTS." SET 
				transpay_transaction_id='".$transpay_transaction_id."',
				transpay_date='".$transpay_date."',
				transpay_payment_id='".$transpay_payment_id."',
				transpay_payment_details='".$transpay_payment_details."',
				transpay_amount='".$transpay_amount."'
				WHERE transpay_id=".$transpay_id."";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
			}
		}
		F_update_tansaction_paid_amount($transpay_transaction_id);
		break;
	}

	case unhtmlentities($l['w_add']):
	case $l['w_add']:{ // Add target
		if($formstatus = F_check_form_fields()) {
			//check if transpay_name is unique
			$sql = "SELECT transpay_date, transpay_description FROM ".K_TABLE_EC_TRANSACTIONS_PAYMENTS." WHERE transpay_date='".$transpay_date."' AND transpay_transaction_id='".$transpay_transaction_id."' AND transpay_payment_id='".$transpay_payment_id."' AND transpay_amount='".$transpay_amount."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_data']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				$sql = "INSERT IGNORE INTO ".K_TABLE_EC_TRANSACTIONS_PAYMENTS." (
				transpay_transaction_id,
				transpay_date,
				transpay_payment_id,
				transpay_payment_details,
				transpay_amount
				) VALUES (
				'".$transpay_transaction_id."',
				'".$transpay_date."',
				'".$transpay_payment_id."',
				'".$transpay_payment_details."',
				'".$transpay_amount."'
				)";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$transpay_id = F_aiocpdb_insert_id();
				}
			}
		}
		F_update_tansaction_paid_amount($transpay_transaction_id);
		break;
	}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$transpay_date = gmdate("Y-m-d");
		$transpay_payment_id = "";
		$transpay_payment_details = "";
		$transpay_amount = 0;
		break;
	}

	default :{ 
		break;
	}

} //end of switch

// Initialize variables

//read transaction details
$sql = "SELECT * FROM ".K_TABLE_EC_TRANSACTIONS." WHERE mtrans_id='".$transpay_transaction_id."' LIMIT 1";
if($r = F_aiocpdb_query($sql, $db)) {
	if($m = F_aiocpdb_fetch_array($r)) {
		$mtrans_id = $m['mtrans_id'];
		$mtrans_date = $m['mtrans_date'];
		$mtrans_type = $m['mtrans_type'];
		$mtrans_description = $m['mtrans_description'];
		$mtrans_doc_ref = $m['mtrans_doc_ref'];
		$mtrans_direction = $m['mtrans_direction'];
		$mtrans_amount = $m['mtrans_amount'];
		$mtrans_tax = $m['mtrans_tax'];
		$mtrans_paid_amount = $m['mtrans_paid_amount'];
		$mtrans_work_id = $m['mtrans_work_id'];
		$mtrans_virtual = $m['mtrans_virtual'];
	}
}
else {
	F_display_db_error();
}

if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!$transpay_id) {$sql = "SELECT * FROM ".K_TABLE_EC_TRANSACTIONS_PAYMENTS." WHERE transpay_transaction_id='".$transpay_transaction_id."' ORDER BY transpay_date DESC LIMIT 1";}
		else {$sql = "SELECT * FROM ".K_TABLE_EC_TRANSACTIONS_PAYMENTS." WHERE transpay_id=".$transpay_id." LIMIT 1";}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$transpay_id = $m['transpay_id'];
				$transpay_transaction_id = $m['transpay_transaction_id'];
				$transpay_date = $m['transpay_date'];
				$transpay_payment_id = $m['transpay_payment_id'];
				$transpay_payment_details = $m['transpay_payment_details'];
				$transpay_amount = $m['transpay_amount'];
			}
			else {
				$transpay_date = gmdate("Y-m-d");
				$transpay_payment_id = "";
				$transpay_payment_details = "";
				$transpay_amount = 0;
			}
		}
		else {
			F_display_db_error();
		}
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_transactionpaymentseditor" id="form_transactionpaymentseditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="transpay_date" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_date']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<th class="edge">
<?php 
echo "(".$mtrans_id.") ";
echo "".$mtrans_date." ";
if ($mtrans_direction > 0) {
	echo "+";
}
else {
	echo "-";
}
echo "".$mtrans_amount." ".K_MONEY_CURRENCY_UNICODE_SYMBOL."";
?>
</th>
</tr>

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">


<!-- SELECT ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_payment', 'h_transpay_select'); ?></b></td>
<td class="fillEE">
<select name="transpay_id" id="transpay_id" size="0" onchange="document.form_transactionpaymentseditor.submit()">
<?php
$i=0;
$sql = "SELECT * FROM ".K_TABLE_EC_TRANSACTIONS_PAYMENTS." WHERE transpay_transaction_id='".$transpay_transaction_id."' ORDER BY transpay_date DESC";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['transpay_id']."\"";
		if($m['transpay_id'] == $transpay_id) {
			echo " selected=\"selected\"";
		}
		echo ">".++$i." ".$m['transpay_date']." - ".$m['transpay_amount']."</option>\n";
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

<input type="hidden" name="x_transpay_date" id="x_transpay_date" value="([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})" />
<input type="hidden" name="xl_transpay_date" id="xl_transpay_date" value="<?php echo $l['w_date']; ?>" />

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_date', 'h_transpay_date'); ?></b></td>
<td class="fillEE"><input type="text" name="transpay_date" id="transpay_date" value="<?php echo $transpay_date; ?>" size="30" maxlength="10" /></td>
</tr>

<?php $doc_charset = F_word_language($selected_language, "a_meta_charset"); ?>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_payment', 'h_paytype_select'); ?></b></td>
<td class="fillOE">
<select name="transpay_payment_id" id="transpay_payment_id" size="0">
<?php
	$sql = "SELECT * FROM ".K_TABLE_EC_PAYMENTS_TYPES." ORDER BY paytype_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			$select_name = unserialize($m['paytype_name']);
			echo "<option value=\"".$m['paytype_id']."\"";
			if($m['paytype_id'] == $transpay_payment_id) {
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
<td class="fillEO" align="right" valign="top"><b><?php echo F_display_field_name('w_details', 'h_mtrans_payment_details'); ?></b>
</td>
<?php
$current_ta_code = $transpay_payment_details;
if(!$formstatus) {
	$current_ta_code = stripslashes($current_ta_code);
}
?>
<td class="fillEE">
<textarea cols="30" rows="3" name="transpay_payment_details" id="transpay_payment_details"><?php echo htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset); ?></textarea>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_amount', 'h_mtrans_paid_amount'); ?></b></td>
<td class="fillOE"><input type="text" name="transpay_amount" id="transpay_amount" value="<?php echo $transpay_amount; ?>" size="30" maxlength="255" /> <b>[<?php echo K_MONEY_CURRENCY_UNICODE_SYMBOL; ?>]</b></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right">&nbsp;</td>
<td class="fillEE"><a href="cp_edit_ec_transactions.<?php echo CP_EXT; ?>?tid=<?php echo $transpay_transaction_id; ?>"><b>&lt;&lt;&nbsp;<?php echo $l['t_money_transactions_editor']; ?></b></a></td>
</tr>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="transpay_transaction_id" id="transpay_transaction_id" value="<?php echo $transpay_transaction_id; ?>" />
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if ($transpay_id) {
	F_submit_button("form_transactionpaymentseditor","menu_mode",$l['w_update']); 
	F_submit_button("form_transactionpaymentseditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_transactionpaymentseditor","menu_mode",$l['w_add']); 
F_submit_button("form_transactionpaymentseditor","menu_mode",$l['w_clear']); 
?>
</td>

</tr>
</table>
</form>
<!-- ====================================================== -->

<!-- Cange focus to transpay_id select -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_transactionpaymentseditor.transpay_id.focus();
//]]>
</script>
<!-- END Cange focus to transpay_id select -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT); 

// ------------------------------------------------------------
// Update paid amount on transaction
// ------------------------------------------------------------
function F_update_tansaction_paid_amount($transaction_id) {
	global $db, $l;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	$paid_amount = 0; //total paid amount for this transaction entry
	
	//sum all transaction payments
	$sql = "SELECT * FROM ".K_TABLE_EC_TRANSACTIONS_PAYMENTS." WHERE transpay_transaction_id=".$transaction_id."";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			$paid_amount += $m['transpay_amount'];
		}
	}
	else {
		F_display_db_error();
	}
	
	$sql = "UPDATE IGNORE ".K_TABLE_EC_TRANSACTIONS." SET 
	mtrans_paid_amount='".$paid_amount."'
	WHERE mtrans_id=".$transaction_id."";
	if(!$r = F_aiocpdb_query($sql, $db)) {
		F_display_db_error();
	}
}
?>

<?php
//============================================================+
// END OF FILE                                                 
//============================================================+
?>
