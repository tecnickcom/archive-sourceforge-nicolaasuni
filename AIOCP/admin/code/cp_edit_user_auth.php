<?php
//============================================================+
// File name   : cp_edit_user_auth.php                         
// Begin       : 2003-09-23                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit time limited user access permissions to  
//               a particular resource                         
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_USER_AUTH;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_user_auth_editor'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages; ?>
<!-- ====================================================== -->
<?php
if (isset($_REQUEST['ua_user_id'])) {
	$ua_user_id = $_REQUEST['ua_user_id'];
} else {
	$ua_user_id = "";
}
if (isset($_REQUEST['ua_time_start'])) {
	$ua_time_start = $_REQUEST['ua_time_start'];
} else {
	$ua_time_start = "";
}
if (isset($_REQUEST['ua_time_end'])) {
	$ua_time_end = $_REQUEST['ua_time_end'];
} else {
	$ua_time_end = "";
}
if (isset($_REQUEST['ua_resource'])) {
	$ua_resource = $_REQUEST['ua_resource'];
} else {
	$ua_resource = "";
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
		$sql = "DELETE FROM ".K_TABLE_USERS_AUTH." WHERE ua_id=".$ua_id."";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		$ua_id=FALSE;
		break;
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update target
		if($formstatus = F_check_form_fields()) {
			$sql = "UPDATE IGNORE ".K_TABLE_USERS_AUTH." SET 
			ua_user_id='".$ua_user_id."',             
			ua_time_start='".$ua_time_start."',            
			ua_time_end='".$ua_time_end."',       
			ua_resource='".$ua_resource."' 
			WHERE ua_id=".$ua_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
		}
		break;
	}

	case unhtmlentities($l['w_add']):
	case $l['w_add']:{ // Add target
		if($formstatus = F_check_form_fields()) {
			$sql = "INSERT IGNORE INTO ".K_TABLE_USERS_AUTH." (
			ua_user_id,             
			ua_time_start,            
			ua_time_end,       
			ua_resource 
			) VALUES (
			'".$ua_user_id."',             
			'".$ua_time_start."',            
			'".$ua_time_end."',       
			'".$ua_resource."' 
			)";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			else {
				$ua_id = F_aiocpdb_insert_id();
			}
		}
		break;
	}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$ua_user_id = "";             
		$ua_time_start = gmdate("Y-m-d H:i:s");           
		$ua_time_end = gmdate("Y-m-d H:i:s");     
		$ua_resource = "";
		break;
	}

	default :{ 
		break;
	}

} //end of switch

// Initialize variables
if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!isset($ua_id) OR (!$ua_id)) {
			$sql = "SELECT * FROM ".K_TABLE_USERS_AUTH." ORDER BY ua_user_id LIMIT 1";
		}	else {
			$sql = "SELECT * FROM ".K_TABLE_USERS_AUTH." WHERE ua_id=".$ua_id." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$ua_id = $m['ua_id'];
				$ua_user_id = $m['ua_user_id'];            
				$ua_time_start = $m['ua_time_start'];            
				$ua_time_end = $m['ua_time_end'];      
				$ua_resource = $m['ua_resource'];
			}
		}
		else {
			F_display_db_error();
		}
	}
}

if (isset($_REQUEST['ua_id'])) {
	$ua_id = $_REQUEST['ua_id'];
} else {
	$ua_id = "";
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_userauth" id="form_userauth">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="ua_user_id, ua_time_start, ua_time_end, ua_resource" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_user'].",".$l['w_date_start'].",".$l['w_date_end'].",".$l['w_link']; ?>" />

<input type="hidden" name="x_ua_time_start" id="x_ua_time_start" value="([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})" />
<input type="hidden" name="xl_ua_time_start" id="xl_ua_time_start" value="<?php echo $l['w_date_start']; ?>" />
<input type="hidden" name="x_ua_time_end" id="x_ua_time_end" value="([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})" />
<input type="hidden" name="xl_ua_time_end" id="xl_ua_time_end" value="<?php echo $l['w_date_end']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT Permission ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_permission', 'h_user_auth_select'); ?></b></td>
<td class="fillOE">
<select name="ua_id" id="ua_id" size="0" onchange="document.form_userauth.submit()">
<?php
$sql = "SELECT ua_id, user_name, user_firstname, user_lastname FROM ".K_TABLE_USERS_AUTH.", ".K_TABLE_USERS." WHERE ua_user_id=user_id ORDER BY user_lastname";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['ua_id']."\"";
		if($m['ua_id'] == $ua_id) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['user_lastname'], ENT_NOQUOTES, $l['a_meta_charset']).", ".htmlentities($m['user_firstname'], ENT_NOQUOTES, $l['a_meta_charset'])." (".htmlentities($m['user_name'], ENT_NOQUOTES, $l['a_meta_charset']).") - ".$m['ua_id']."";
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
<!-- END SELECT Permission ==================== -->

<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE"><hr /></td>
</tr>

<!-- SELECT USER ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_user', 'h_user_select'); ?></b></td>
<td class="fillOE">
<select name="ua_user_id" id="ua_user_id" size="0">
<option value="">&nbsp;</option>
<?php
// display users
$sql = "SELECT user_id, user_name, user_firstname, user_lastname FROM ".K_TABLE_USERS." ORDER BY user_lastname";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		if ($m['user_id'] > 1) {
			echo "<option value=\"".$m['user_id']."\"";
			if ($m['user_id'] == $ua_user_id) {
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
<!-- END SELECT USER ==================== -->

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_link', 'h_user_auth_link'); ?></b></td>
<td class="fillEE"><input type="text" name="ua_resource" id="ua_resource" value="<?php echo $ua_resource; ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_date_start', 'h_user_auth_date_start'); ?></b></td>
<td class="fillOE"><input type="text" name="ua_time_start" id="ua_time_start" value="<?php echo $ua_time_start; ?>" size="20" maxlength="19" /> [<?php echo $l['w_datetime_pattern']; ?>]</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_date_end', 'h_user_auth_date_end'); ?></b></td>
<td class="fillEE"><input type="text" name="ua_time_end" id="ua_time_end" value="<?php echo $ua_time_end; ?>" size="20" maxlength="19" /> [<?php echo $l['w_datetime_pattern']; ?>]</td>
</tr>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if ($ua_id) {
	F_submit_button("form_userauth","menu_mode",$l['w_update']); 
	F_submit_button("form_userauth","menu_mode",$l['w_delete']); 
}
F_submit_button("form_userauth","menu_mode",$l['w_add']); 
F_submit_button("form_userauth","menu_mode",$l['w_clear']); 
?>
</td>

</tr>
</table>
</form>

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
