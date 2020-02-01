<?php
//============================================================+
// File name   : cp_edit_ec_user_discount.php                  
// Begin       : 2003-04-03                                    
// Last Update : 2008-07-06
//                                                             
// Description : Set e-commerce discount percentage for some   
//               users                                         
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_EC_USER_DISCOUNT; 
require_once('../../shared/code/cp_authorization.'.CP_EXT);

require_once('../../shared/code/cp_functions_form.'.CP_EXT);

$thispage_title = $l['t_user_discount_editor'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages;


//get username from $discount_userid
if (isset($discount_userid) AND $discount_userid) {
	$sqlc = "SELECT company_userid, company_name, user_name FROM ".K_TABLE_USERS_COMPANY.", ".K_TABLE_USERS." WHERE company_userid='".$discount_userid."' LIMIT 1";
	if($rc = F_aiocpdb_query($sqlc, $db)) {
		if($mc = F_aiocpdb_fetch_array($rc)) { //if user is a company
			$discount_username = "".htmlentities($mc['company_name'], ENT_NOQUOTES, $l['a_meta_charset'])." (".htmlentities($mc['user_name'], ENT_NOQUOTES, $l['a_meta_charset']).")";
		}
		else { //is a simple user
			$sqlu = "SELECT user_id, user_name, user_firstname, user_lastname FROM ".K_TABLE_USERS." WHERE user_id='".$discount_userid."' LIMIT 1";
			if($ru = F_aiocpdb_query($sqlu, $db)) {
				if($mu = F_aiocpdb_fetch_array($ru)) {
					$discount_username = "".htmlentities($mu['user_lastname'], ENT_NOQUOTES, $l['a_meta_charset']).", ".htmlentities($mu['user_firstname'], ENT_NOQUOTES, $l['a_meta_charset'])." (".htmlentities($mu['user_name'], ENT_NOQUOTES, $l['a_meta_charset']).")";
				}
			}
			else {
				F_display_db_error();
			}
		}
	}
	else {
		F_display_db_error();
	}
}

if (isset($_REQUEST['menu_mode'])) {
	$menu_mode = $_REQUEST['menu_mode'];
} else {
	$menu_mode = "";
}
switch($menu_mode) {

	case unhtmlentities($l['w_delete']):
	case $l['w_delete']:{
		F_stripslashes_formfields(); // Delete target
		$sql = "DELETE FROM ".K_TABLE_EC_USER_DISCOUNT." WHERE discount_id=".$discount_id."";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		$discount_id=FALSE;
		break;
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update target
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_EC_USER_DISCOUNT, "discount_userid='".$discount_userid."'", "discount_id", $discount_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$sql = "UPDATE IGNORE ".K_TABLE_EC_USER_DISCOUNT." SET 
					discount_userid='".$discount_userid."',
					discount_username='".$discount_username."',
					discount_value='".$discount_value."' 
					WHERE discount_id=".$discount_id."";
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
			//check if discount_name is unique
			$sql = "SELECT discount_name FROM ".K_TABLE_EC_USER_DISCOUNT." WHERE discount_userid='".$discount_userid."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				$sql = "INSERT IGNORE INTO ".K_TABLE_EC_USER_DISCOUNT." (
					discount_userid,
					discount_username,
					discount_value
					) VALUES (
					'".$discount_userid."',
					'".$discount_username."',
					'".$discount_value."'
					)";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$discount_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
	}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$discount_userid = "";
		$discount_username = "";
		$discount_value = 0;
	break;
		}

	default :{ 
		break;
	}

} //end of switch

// Initialize variables
if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!isset($discount_id) OR (!$discount_id)) {
			$sql = "SELECT * FROM ".K_TABLE_EC_USER_DISCOUNT." ORDER BY discount_userid LIMIT 1";
		} else {
			$sql = "SELECT * FROM ".K_TABLE_EC_USER_DISCOUNT." WHERE discount_id=".$discount_id." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$discount_id = $m['discount_id'];
				$discount_userid = $m['discount_userid'];
				$discount_username = $m['discount_username'];
				$discount_value = $m['discount_value'];
			}
			else {
				$discount_userid = "";
				$discount_username = "";
				$discount_value = 0;
			}
		}
		else {
			F_display_db_error();
		}
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_usrdiscount" id="form_usrdiscount">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="discount_value" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_discount']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT  ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_discount', 'h_discount_select'); ?></b></td>
<td class="fillOE">
<select name="discount_id" id="discount_id" size="0" onchange="document.form_usrdiscount.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_EC_USER_DISCOUNT." ORDER BY discount_username";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['discount_id']."\"";
		if($m['discount_id'] == $discount_id) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['discount_username'], ENT_NOQUOTES, $l['a_meta_charset'])." : ".$m['discount_value']."%</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT  ==================== -->


<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE"><hr /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_user', 'h_user_select'); ?></b></td>
<td class="fillOE">
<select name="discount_userid" id="discount_userid" size="0">
<?php
$select_set = false;
//display registered companies
$sql = "SELECT company_userid, company_name, user_name FROM ".K_TABLE_USERS_COMPANY.", ".K_TABLE_USERS." WHERE company_userid=user_id ORDER BY company_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['company_userid']."\"";
		if($m['company_userid'] == $discount_userid) {
			echo " selected=\"selected\"";
			$select_set = true;
		}
		echo ">".htmlentities($m['company_name'], ENT_NOQUOTES, $l['a_meta_charset'])." (".htmlentities($m['user_name'], ENT_NOQUOTES, $l['a_meta_charset']).")";
		echo "</option>\n";
	}
}
else {
	F_display_db_error();
}

// display users
$sql = "SELECT user_id, user_name, user_firstname, user_lastname FROM ".K_TABLE_USERS." ORDER BY user_lastname";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		if ($m['user_id'] > 1) {
			echo "<option value=\"".$m['user_id']."\"";
			if( ($m['user_id'] == $discount_userid) AND (!$select_set) ) {
				echo " selected=\"selected\"";
			}
			echo ">".htmlentities($m['user_lastname'], ENT_NOQUOTES, $l['a_meta_charset']).", ".htmlentities($m['user_firstname'], ENT_NOQUOTES, $l['a_meta_charset'])." (".htmlentities($m['user_name'], ENT_NOQUOTES, $l['a_meta_charset']).")";
			echo "</option>\n";
		}
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
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_discount', 'h_user_discount'); ?></b></td>
<td class="fillEE">
<input type="text" name="discount_value" id="discount_value" value="<?php echo $discount_value; ?>" size="10" maxlength="255" /> <b>[%]</b>
</td>
</tr>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if (isset($discount_id) AND ($discount_id > 0)) {
	F_submit_button("form_usrdiscount","menu_mode",$l['w_update']); 
	F_submit_button("form_usrdiscount","menu_mode",$l['w_delete']); 
}
F_submit_button("form_usrdiscount","menu_mode",$l['w_add']); 
F_submit_button("form_usrdiscount","menu_mode",$l['w_clear']); 
?>
</td>

</tr>
</table>
</form>
<!-- ====================================================== -->

<!-- Cange focus to discount_id select -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_usrdiscount.discount_id.focus();
//]]>
</script>
<!-- END Cange focus to discount_id select -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
