<?php
//============================================================+
// File name   : cp_functions_user_edit.php
// Begin       : 2001-09-28
// Last Update : 2008-08-10
// 
// Description : Functions for Edit User
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

//------------------------------------------------------------
// Display user registration agreement
//------------------------------------------------------------
function F_display_reg_agreement() {
	global $l, $selected_language;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
?>
<form action="cp_edit_user.<?php echo CP_EXT; ?>" method="post" enctype="multipart/form-data" name="form_usreg_agreement" id="form_usreg_agreement">
<input type="hidden" name="user_agreed" id="user_agreed" value="" />

<?php
// display here the user registration agreement
$pt = F_load_page_templates($selected_language, 'user_reg_agreement');
echo $pt['agreement'];
?>
<br />
<div align="center">
<?php F_submit_button("form_usreg_agreement","user_agreed",$l['w_agree']); ?>&nbsp;
<?php F_submit_button("form_usreg_agreement","user_agreed",$l['w_not_agree']); ?>
</div>
</form>
<?php
}

//------------------------------------------------------------
// Uploads image file to the server 
// resize image to configured values
// change image format to PNG
// change image name to be unique for each user
//------------------------------------------------------------
function F_upload_user_image($user_name) {
	global $l;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	
	$src_file = $_FILES['userfile']['tmp_name'];
	$src_path_parts = pathinfo($_FILES['userfile']['name']);
	$src_ext = strtolower($src_path_parts['extension']);
	
	$dst_file = $user_name.".png";
	$dst_path = K_PATH_IMAGES_USER_PHOTO.$dst_file;
	$dst_small_path = K_PATH_IMAGES_USER_PHOTO."s_".$dst_file; //full path of miniature file
	
	//load source image and check format
	switch($src_ext) {
		/*
		case "gif":{
			$src_img = ImageCreateFromGif($src_file);
			break;
		}
		*/
		case "jpeg":
		case "jpg":{
			$src_img = ImageCreateFromJPEG($src_file);
			break;
		}
		case "png":{
			$src_img = ImageCreateFromPNG($src_file);
			break;
		}
		case "bmp":{
			$src_img = ImageCreateFromWBMP($src_file);
			break;
		}
		case "xbm":{
			$src_img = ImageCreateFromXBM($src_file);
			break;
		}
		case "xpm":{
			$src_img = ImageCreateFromXPM($src_file);
			break;
		}
		default:{
			F_print_error("WARNING", $_FILES['userfile']['name'].": ".$l['m_upload_not']." - ".$l['m_image_desc'].":<br />JPG/PNG/BPM/XBM/XPM");
			return K_BLANK_IMAGE;
			break;
		}
	}
	
	//check for size
	if($_FILES['userfile']['size'] > K_MAX_USER_IMAGE_SIZE) {
		F_print_error("WARNING", $_FILES['userfile']['name'].": ".$l['m_upload_not']." - ".$l['w_max_size'].": ".K_MAX_USER_IMAGE_SIZE."bytes.");
		return K_BLANK_IMAGE;
	}
	
	$src_size = GetImageSize($src_file); //get image size information
	$src_width = $src_size[0];
	$src_height = $src_size[1];
	
	if($src_width < $src_height) {
		$dh = K_USER_IMAGE_HEIGHT;
		$dy = 0;
		$dw = round(($src_width * K_USER_IMAGE_HEIGHT ) / $src_height);
		$dx = round((K_USER_IMAGE_WIDTH - $dw) / 2);
	}
	else {
		$dw = K_USER_IMAGE_WIDTH;
		$dx = 0;
		$dh = round(($src_height * K_USER_IMAGE_WIDTH ) / $src_width);
		$dy = round((K_USER_IMAGE_HEIGHT - $dh) / 2);
	}
	
	//create resized user image
	if (K_USE_GD2) {
		$dst_img = ImageCreateTrueColor(K_USER_IMAGE_WIDTH, K_USER_IMAGE_HEIGHT); //GD2
	}
	else {
		$dst_img = ImageCreate(K_USER_IMAGE_WIDTH, K_USER_IMAGE_HEIGHT);
	}
	//set background color
	$dst_background_color = ImageColorAllocate($dst_img, K_IMAGE_BACKGROUND_R, K_IMAGE_BACKGROUND_G, K_IMAGE_BACKGROUND_B); 
	imagefill($dst_img, 0, 0, $dst_background_color);
	if (K_USE_GD2) {
		ImageCopyResampled($dst_img, $src_img, $dx, $dy, 0,  0, $dw, $dh, $src_width, $src_height);
	}
	else {
		ImageCopyResized($dst_img, $src_img, $dx, $dy, 0,  0, $dw, $dh, $src_width, $src_height);
	}
	ImagePNG($dst_img,$dst_path);
	ImageDestroy($src_img);
	ImageDestroy($dst_img);
	
	return $dst_file;
}

//******************************************************************************************************************



// ------------------------------------------------------------
// Edit user phone numbers
// ------------------------------------------------------------
function F_edit_user_phone($user_id) {
	global $l, $db, $aiocp_dp, $selected_language;
	global $uemode, $forcedelete;
	global $menu_mode, $phone_id, $phone_name, $phone_number, $phone_public;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_user.'.CP_EXT);
	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	
	//check authorization
	if(!isset($user_id)) {return FALSE;}
	
	$userid = $_SESSION['session_user_id'];
	$userip = $_SESSION['session_user_ip'];
	$userlevel = $_SESSION['session_user_level'];
	
	if(!(($userid == $user_id) OR ($userlevel >= K_AUTH_EDIT_USER_LEVEL))) {return FALSE;}
	if(($user_id==1) AND ($userlevel!=10)) {return FALSE;} //anonymous user may be edit only by administrator
	
	$userdata = F_get_user_data($user_id);
	

	switch($menu_mode) {

		case unhtmlentities($l['w_delete']):
		case $l['w_delete']:{
		F_stripslashes_formfields(); // ask confirmation
		F_print_error("WARNING", $l['m_delete_confirm']);
		?>
		<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_delete" id="form_delete">
		<input type="hidden" name="menu_mode" id="menu_mode" value="forcedelete" />
		<input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id; ?>" />
		<input type="hidden" name="phone_id" id="phone_id" value="<?php echo $phone_id; ?>" />
		<input type="hidden" name="uemode" id="uemode" value="<?php echo $uemode; ?>" />
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
			$sql = "DELETE FROM ".K_TABLE_USERS_PHONE." WHERE phone_id=".$phone_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			$phone_id=FALSE;
		}
		break;
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update phone
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_USERS_PHONE, "(phone_userid='".$user_id."' AND phone_name='".$phone_name."')", "phone_id", $phone_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$sql = "UPDATE IGNORE ".K_TABLE_USERS_PHONE." SET 
				phone_userid='".$user_id."', 
				phone_name='".$phone_name."', 
				phone_number='".$phone_number."', 
				phone_public='".$phone_public."' 
				WHERE phone_id=".$phone_id."";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_add']):
	case $l['w_add']:{ // Add phone
		if($formstatus = F_check_form_fields()) {
			//check if phone_name is unique
			$sql = "SELECT phone_name FROM ".K_TABLE_USERS_PHONE." WHERE (phone_userid='".$user_id."' AND phone_name='".$phone_name."')";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
					F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				$sql = "INSERT IGNORE INTO ".K_TABLE_USERS_PHONE." (
				phone_userid, 
				phone_name, 
				phone_number, 
				phone_public
				) VALUES (
				'".$user_id."', 
				'".$phone_name."', 
				'".$phone_number."', 
				'".$phone_public."')";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$phone_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$phone_name = "";
		$phone_number = "";
		$phone_public = 0;
		break;
		}

	default :{ 
		break;
		}

} //end of switch

if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!$phone_id) {
			$sql = "SELECT * FROM ".K_TABLE_USERS_PHONE." WHERE phone_userid=".$user_id." ORDER BY phone_name LIMIT 1";
		}
		else {$sql = "SELECT * FROM ".K_TABLE_USERS_PHONE." WHERE phone_id=".$phone_id." LIMIT 1";}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$phone_id = $m['phone_id'];
				$user_id = $m['phone_userid'];
				$phone_name = $m['phone_name'];
				$phone_number = $m['phone_number'];
				$phone_public = $m['phone_public'];
			}
			else {
				$phone_name = "";
				$phone_number = "";
				$phone_public = 0;
			}
		}
		else {
			F_display_db_error();
		}
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_phoneeditor" id="form_phoneeditor">
<input type="hidden" name="uemode" id="uemode" value="<?php echo $uemode; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<th class="edge">
<?php echo "<a class=\"edge\" href=\"cp_user_profile.".CP_EXT."?user_id=".$user_id."\">".htmlentities($userdata->name, ENT_NOQUOTES, $l['a_meta_charset'])."</a>"; ?>
</th></tr>

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT phone ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_phone', 'h_phone_select'); ?></b></td>
<td class="fillOE">
<select name="phone_id" id="phone_id" size="0" onchange="document.form_phoneeditor.submit()">
<?php
if($user_id) {
	$sql = "SELECT * FROM ".K_TABLE_USERS_PHONE." WHERE phone_userid=".$user_id." ORDER BY phone_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"".$m['phone_id']."\"";
			if($m['phone_id'] == $phone_id) {
				echo " selected=\"selected\"";
			}
			echo ">".htmlentities($m['phone_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
		}
	}
	else {
		F_display_db_error();
	}
}
?>
</select>
</td>
</tr>
<!-- END SELECT phone ==================== -->

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_public', 'h_public_option'); ?></b></td>
<td class="fillEE">
<?php
echo "<input type=\"radio\" name=\"phone_public\" value=\"1\"";
if($phone_public) {echo " checked=\"checked\"";}
echo " />".$l['w_yes']."";
echo "<input type=\"radio\" name=\"phone_public\" value=\"0\"";
if(!$phone_public) {echo " checked=\"checked\"";}
echo " />".$l['w_no']."";
?>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_name', 'h_usrphone_name'); ?></b></td>
<td class="fillOE"><input type="text" name="phone_name" id="phone_name" value="<?php echo htmlentities($phone_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_number', ''); ?></b></td>
<td class="fillEE"><input type="text" name="phone_number" id="phone_number" value="<?php echo htmlentities($phone_number, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id; ?>" />
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if ($phone_id) {
	F_submit_button("form_phoneeditor","menu_mode",$l['w_update']); 
	F_submit_button("form_phoneeditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_phoneeditor","menu_mode",$l['w_add']); 
F_submit_button("form_phoneeditor","menu_mode",$l['w_clear']); 
?>
</td>

</tr>
</table>

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="phone_name,phone_number" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name'].",".$l['w_number']; ?>" />
</form>

<p>
<a href="cp_edit_user.<?php echo CP_EXT; ?>?uemode=user&amp;user_id=<?php echo $user_id; ?>"><b>&lt;&lt;&nbsp;<?php echo $l['t_user_editor']; ?></b></a>
<br />
<a href="cp_edit_user.<?php echo CP_EXT; ?>?uemode=address&amp;user_id=<?php echo $user_id; ?>"><b><?php echo $l['t_address_editor']; ?>&nbsp;&gt;&gt;</b></a>
<br />
<a href="cp_edit_user.<?php echo CP_EXT; ?>?uemode=internet&amp;user_id=<?php echo $user_id; ?>"><b><?php echo $l['t_internet_editor']; ?>&nbsp;&gt;&gt;</b></a>
<br />
<a href="cp_edit_user.<?php echo CP_EXT; ?>?uemode=company&amp;user_id=<?php echo $user_id; ?>"><b><?php echo $l['t_company_editor']; ?>&nbsp;&gt;&gt;</b></a>
</p>

<!-- ====================================================== -->

<!-- Cange focus to phone_id select -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_phoneeditor.phone_id.focus();
//]]>
</script>
<!-- END Cange focus to phone_id select -->

<?php
return TRUE;
} //end of function

//******************************************************************************************************************



// ------------------------------------------------------------
// Edit user addresses
// ------------------------------------------------------------
function F_edit_user_address($user_id) {
	global $l, $db, $aiocp_dp, $aiocp_dp;
	global $uemode, $forcedelete;
	global $menu_mode, $address_id, $address_name, $address_address, $address_city, $address_state, $address_postcode, $address_default ;
	global $address_countryid, $address_public, $country_flag, $country_width, $country_height;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_user.'.CP_EXT);
	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	
	//check authorization
	if(!isset($user_id)) {return FALSE;}
	
	$userid = $_SESSION['session_user_id'];
	$userip = $_SESSION['session_user_ip'];
	$userlevel = $_SESSION['session_user_level'];
	
	if(!(($userid == $user_id) OR ($userlevel >= K_AUTH_EDIT_USER_LEVEL))) {return FALSE;}
	if(($user_id == 1) AND ($userlevel != 10)) {return FALSE;} //anonymous user may be edit only by administrator
	
	$userdata = F_get_user_data($user_id);

switch($menu_mode) {

	case unhtmlentities($l['w_delete']):
	case $l['w_delete']:{
		F_stripslashes_formfields(); // ask confirmation
		F_print_error("WARNING", $l['m_delete_confirm']);
		?>
		<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_delete" id="form_delete">
		<input type="hidden" name="menu_mode" id="menu_mode" value="forcedelete" />
		<input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id; ?>" />
		<input type="hidden" name="address_id" id="address_id" value="<?php echo $address_id; ?>" />
		<input type="hidden" name="uemode" id="uemode" value="<?php echo $uemode; ?>" />
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
			$sql = "DELETE FROM ".K_TABLE_USERS_ADDRESS." WHERE address_id='".$address_id."'";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			$address_id=FALSE;
		}
		break;
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update address
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_USERS_ADDRESS, "(address_userid='".$user_id."' AND address_name='".$address_name."')", "address_id", $address_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				// check/set default attribute
				if ($address_default) {
					$sql = "UPDATE IGNORE ".K_TABLE_USERS_ADDRESS." SET 
					address_default='0' 
					WHERE address_userid='".$user_id."'";
					if(!$r = F_aiocpdb_query($sql, $db)) {
						F_display_db_error();
					}
				}
				
				$sql = "UPDATE IGNORE ".K_TABLE_USERS_ADDRESS." SET 
				address_userid='".$user_id."', 
				address_name='".$address_name."', 
				address_address='".$address_address."', 
				address_city='".$address_city."', 
				address_state='".$address_state."', 
				address_postcode='".$address_postcode."', 
				address_countryid='".$address_countryid."', 
				address_public='".$address_public."', 
				address_default='".$address_default."' 
				WHERE address_id='".$address_id."'";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_add']):
	case $l['w_add']:{ // Add address
		if($formstatus = F_check_form_fields()) {
			//check if address_name is unique
			$sql = "SELECT address_name FROM ".K_TABLE_USERS_ADDRESS." WHERE (address_userid='".$user_id."' AND address_name='".$address_name."')";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				// check/set default attribute
				if ($address_default) {
					$sql = "UPDATE IGNORE ".K_TABLE_USERS_ADDRESS." SET address_default='0' WHERE address_userid='".$user_id."'";
					if(!$r = F_aiocpdb_query($sql, $db)) {
						F_display_db_error();
					}
				}
				else {
					$sql = "SELECT COUNT(*) FROM ".K_TABLE_USERS_ADDRESS." WHERE address_userid='".$user_id."'";
					if($r = F_aiocpdb_query($sql, $db)) {
						if($m = F_aiocpdb_fetch_array($r)) {
							if (!$m['0']) {
								$address_default = 1;
							}
						}
					}
					else {
						F_display_db_error();
					}
				}
				
				$sql = "INSERT IGNORE INTO ".K_TABLE_USERS_ADDRESS." (
				address_userid, 
				address_name, 
				address_address, 
				address_city, 
				address_state, 
				address_postcode, 
				address_countryid, 
				address_public, 
				address_default
				) VALUES(
				'".$user_id."',
				'".$address_name."',
				'".$address_address."',
				'".$address_city."',
				'".$address_state."',
				'".$address_postcode."',
				'".$address_countryid."',
				'".$address_public."', 
				'".$address_default."'
				)";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$address_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$address_name = "";
		$address_address = "";
		$address_city = "";
		$address_state = "";
		$address_postcode = "";
		$address_countryid = "";
		$address_public = 0;
		$address_default = 0;
		$country_flag = K_BLANK_IMAGE;
		$country_width = 32;
		$country_height = 20;
		break;
		}

	default :{ 
		break;
		}

} //end of switch

if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!$address_id) {
			$sql = "SELECT * FROM ".K_TABLE_USERS_ADDRESS." WHERE address_userid=".$user_id." ORDER BY address_name LIMIT 1";
		}
		else {$sql = "SELECT * FROM ".K_TABLE_USERS_ADDRESS." WHERE address_id=".$address_id." LIMIT 1";}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$address_id = $m['address_id'];
				$user_id = $m['address_userid'];
				$address_name = $m['address_name']; //name of location
				$address_address = $m['address_address'];
				$address_city = $m['address_city'];
				$address_state = $m['address_state'];
				$address_postcode = $m['address_postcode'];
				$address_countryid = $m['address_countryid'];
				$address_public = $m['address_public'];
				$address_default = $m['address_default'];
				
				$sql2 = "SELECT * FROM ".K_TABLE_COUNTRIES." WHERE country_id=".$address_countryid."";
				if($r2 = F_aiocpdb_query($sql2, $db)) {
					if($m2 = F_aiocpdb_fetch_array($r2)) {
						$country_flag = $m2['country_flag'];
						$country_width = $m2['country_width'];
						$country_height = $m2['country_height'];
					}
				}
			}
			else  {
				$address_name = "";
				$address_address = "";
				$address_city = "";
				$address_state = "";
				$address_postcode = "";
				$address_countryid = "";
				$address_public = 0;
				$address_default = 0;
				$country_flag = K_BLANK_IMAGE;
				$country_width = 32;
				$country_height = 20;
			}
		}
		else {
			F_display_db_error();
		}
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_addresseditor" id="form_addresseditor">
<input type="hidden" name="uemode" id="uemode" value="<?php echo $uemode; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<th class="edge">
<?php echo "<a class=\"edge\" href=\"cp_user_profile.".CP_EXT."?user_id=".$user_id."\">".htmlentities($userdata->name, ENT_NOQUOTES, $l['a_meta_charset'])."</a>"; ?>
</th></tr>

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT address ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_address', 'h_address_select'); ?></b></td>
<td class="fillOE">
<select name="address_id" id="address_id" size="0" onchange="document.form_addresseditor.submit()">
<?php
if($user_id) {
	$sql = "SELECT * FROM ".K_TABLE_USERS_ADDRESS." WHERE address_userid=".$user_id." ORDER BY address_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"".$m['address_id']."\"";
			if($m['address_id'] == $address_id) {
				echo " selected=\"selected\"";
			}
			echo ">".htmlentities($m['address_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
		}
	}
	else {
		F_display_db_error();
	}
}
?>
</select>
</td>
</tr>
<!-- END SELECT address ==================== -->
	
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_public', 'h_public_option'); ?></b></td>
<td class="fillEE">
<?php
echo "<input type=\"radio\" name=\"address_public\" value=\"1\"";
if($address_public) {echo " checked=\"checked\"";}
echo " />".$l['w_yes']."";
echo "<input type=\"radio\" name=\"address_public\" value=\"0\"";
if(!$address_public) {echo " checked=\"checked\"";}
echo " />".$l['w_no']."";
?>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_default', 'h_usraddr_default'); ?></b></td>
<td class="fillOE">
<?php
echo "<input type=\"radio\" name=\"address_default\" value=\"1\"";
if($address_default) {echo " checked=\"checked\"";}
echo " />".$l['w_yes']."";
echo "<input type=\"radio\" name=\"address_default\" value=\"0\"";
if(!$address_default) {echo " checked=\"checked\"";}
echo " />".$l['w_no']."";
?>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_name', 'h_usraddr_name'); ?></b></td>
<td class="fillEE"><input type="text" name="address_name" id="address_name" value="<?php echo htmlentities($address_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_address', ''); ?></b></td>
<td class="fillOE"><input type="text" name="address_address" id="address_address" value="<?php echo htmlentities($address_address, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_city', ''); ?></b></td>
<td class="fillEE"><input type="text" name="address_city" id="address_city" value="<?php echo htmlentities($address_city, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_state', ''); ?></b></td>
<td class="fillOE"><input type="text" name="address_state" id="address_state" value="<?php echo htmlentities($address_state, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_postcode', ''); ?></b></td>
<td class="fillEE"><input type="text" name="address_postcode" id="address_postcode" value="<?php echo htmlentities($address_postcode, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_country', ''); ?></b></td>
<td class="fillOE">
<select name="address_countryid" id="address_countryid" size="0">
<?php
$sql = "SELECT * FROM ".K_TABLE_COUNTRIES." ORDER BY country_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['country_id']."\"";
		if($m['country_id'] == $address_countryid) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['country_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select> <img name="flagimage" src="<?php echo K_PATH_IMAGES_FLAGS; ?><?php echo $country_flag; ?>" border="0" alt="" width="<?php echo $country_width; ?>" height="<?php echo $country_height; ?>" />
</td>
</tr>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id; ?>" />
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if($address_id) {
	F_submit_button("form_addresseditor","menu_mode",$l['w_update']); 
	F_submit_button("form_addresseditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_addresseditor","menu_mode",$l['w_add']); 
F_submit_button("form_addresseditor","menu_mode",$l['w_clear']); 

if ( ($userlevel >= K_AUTH_EDIT_USER_LEVEL) AND ($address_id) ) {
	//generate a verification code to avoid unauthorized calls to PDF viewer
	$verifycode = F_generate_verification_code($user_id, 4);
	F_generic_button("pdfenvelope", $l['w_envelope'], "PDFENV=window.open('../../admin/code/cp_show_ec_pdf_envelope.".CP_EXT."?uid=".$user_id."&amp;usr=1&amp;def=".$address_id."&amp;vc=".$verifycode."&amp;selected_language=".$selected_language."','PDFENV','menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')");
}

?>
</td>

</tr>
</table>

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="address_name,address_address,address_city,address_state,address_postcode,address_countryid" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name'].",".$l['w_address'].",".$l['w_city'].",".$l['w_state'].",".$l['w_postcode'].",".$l['w_country']; ?>" />
</form>

<p>
<a href="cp_edit_user.<?php echo CP_EXT; ?>?uemode=user&amp;user_id=<?php echo $user_id; ?>"><b>&lt;&lt;&nbsp;<?php echo $l['t_user_editor'] ?></b></a>
<br />
<a href="cp_edit_user.<?php echo CP_EXT; ?>?uemode=phone&amp;user_id=<?php echo $user_id; ?>"><b><?php echo $l['t_phone_editor'] ?>&nbsp;&gt;&gt;</b></a>
<br />
<a href="cp_edit_user.<?php echo CP_EXT; ?>?uemode=internet&amp;user_id=<?php echo $user_id; ?>"><b><?php echo $l['t_internet_editor'] ?>&nbsp;&gt;&gt;</b></a>
<br />
<a href="cp_edit_user.<?php echo CP_EXT; ?>?uemode=company&amp;user_id=<?php echo $user_id; ?>"><b><?php echo $l['t_company_editor']; ?>&nbsp;&gt;&gt;</b></a>
</p>

<!-- ====================================================== -->

<!-- Cange focus to address_id select -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_addresseditor.address_id.focus();
//]]>
</script>
<!-- END Cange focus to address_id select -->

<?php
return TRUE;
} //end of function

//******************************************************************************************************************




// ------------------------------------------------------------
// Edit user internet addresses
// ------------------------------------------------------------
function F_edit_user_internet($user_id) {
	global $l, $db, $aiocp_dp;
	global $uemode, $forcedelete;
	global $menu_mode, $internet_id, $internet_name, $internet_email, $internet_website, $internet_icq, $internet_aim;
	global $internet_yim, $internet_msnm, $internet_public;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_user.'.CP_EXT);
	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	
	//check authorization
	if(!isset($user_id)) {return FALSE;}
	
	$userid = $_SESSION['session_user_id'];
	$userip = $_SESSION['session_user_ip'];
	$userlevel = $_SESSION['session_user_level'];
	
	if(!(($userid == $user_id) OR ($userlevel >= K_AUTH_EDIT_USER_LEVEL))) {return FALSE;}
	if(($user_id == 1) AND ($userlevel != 10)) {return FALSE;} //anonymous user may be edit only by administrator
	
	$userdata = F_get_user_data($user_id);

	switch($menu_mode) {

	case unhtmlentities($l['w_delete']):
	case $l['w_delete']:{
		F_stripslashes_formfields(); // ask confirmation
		F_print_error("WARNING", $l['m_delete_confirm']);
		?>
		<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_delete" id="form_delete">
		<input type="hidden" name="menu_mode" id="menu_mode" value="forcedelete" />
		<input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id; ?>" />
		<input type="hidden" name="internet_id" id="internet_id" value="<?php echo $internet_id; ?>" />
		<input type="hidden" name="uemode" id="uemode" value="<?php echo $uemode; ?>" />
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
			$sql = "DELETE FROM ".K_TABLE_USERS_INTERNET." WHERE internet_id='".$internet_id."'";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			$internet_id=FALSE;
		}
		break;
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update internet
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_USERS_INTERNET, "(internet_userid='".$user_id."' AND internet_name='".$internet_name."')", "internet_id", $internet_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				if ((!empty($internet_website)) AND (substr($internet_website,0,4)!="http")) {
					$internet_website = "http://".$internet_website;
				}
				$sql = "UPDATE IGNORE ".K_TABLE_USERS_INTERNET." SET 
				internet_userid='".$user_id."', 
				internet_name='".$internet_name."', 
				internet_email='".$internet_email."', 
				internet_website='".$internet_website."', 
				internet_icq='".$internet_icq."', 
				internet_aim='".$internet_aim."', 
				internet_yim='".$internet_yim."', 
				internet_msnm='".$internet_msnm."', 
				internet_public='".$internet_public."' 
				WHERE internet_id='".$internet_id."'";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_add']):
	case $l['w_add']:{ // Add internet
		if($formstatus = F_check_form_fields()) {
			//check if internet_name is unique
			$sql = "SELECT internet_name FROM ".K_TABLE_USERS_INTERNET." WHERE (internet_userid='".$user_id."' AND internet_name='".$internet_name."')";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				if ((!empty($internet_website)) AND (substr($internet_website,0,4)!="http")) {
					$internet_website = "http://".$internet_website;
				}
				$sql = "INSERT IGNORE INTO ".K_TABLE_USERS_INTERNET." (
				internet_userid, 
				internet_name, 
				internet_email, 
				internet_website, 
				internet_icq, 
				internet_aim, 
				internet_yim, 
				internet_msnm, 
				internet_public
				) VALUES(
				'".$user_id."',
				'".$internet_name."',
				'".$internet_email."',
				'".$internet_website."',
				'".$internet_icq."',
				'".$internet_aim."',
				'".$internet_yim."',
				'".$internet_msnm."',
				'".$internet_public."'
				)";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$internet_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$internet_name = ""; //Contact number
		$internet_email = "";
		$internet_website = "";
		$internet_icq = ""; //ICQ number
		$internet_aim = ""; //AOL messenger
		$internet_yim = ""; //Yahoo messenger
		$internet_msnm = ""; //Microsoft messenger
		$internet_public = 0;
		break;
		}

	default :{ 
		break;
		}

} //end of switch

if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!$internet_id) {
			$sql = "SELECT * FROM ".K_TABLE_USERS_INTERNET." WHERE internet_userid=".$user_id." ORDER BY internet_name LIMIT 1";
		}
		else {$sql = "SELECT * FROM ".K_TABLE_USERS_INTERNET." WHERE internet_id=".$internet_id." LIMIT 1";}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$internet_id = $m['internet_id'];
				$user_id = $m['internet_userid'];
				$internet_name = $m['internet_name']; //Contact number
				$internet_email = $m['internet_email'];
				$internet_website = $m['internet_website'];
				$internet_icq = $m['internet_icq']; //ICQ number
				$internet_aim = $m['internet_aim']; //AOL messenger
				$internet_yim = $m['internet_yim']; //Yahoo messenger
				$internet_msnm = $m['internet_msnm']; //Microsoft messenger
				$internet_public = $m['internet_public'];
			}
			else {
				$internet_name = ""; //Contact number
				$internet_email = "";
				$internet_website = "";
				$internet_icq = ""; //ICQ number
				$internet_aim = ""; //AOL messenger
				$internet_yim = ""; //Yahoo messenger
				$internet_msnm = ""; //Microsoft messenger
				$internet_public = 0;
			}
		}
		else {
			F_display_db_error();
		}
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_interneteditor" id="form_interneteditor">
<input type="hidden" name="uemode" id="uemode" value="<?php echo $uemode; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<th class="edge">
<?php echo "<a class=\"edge\" href=\"cp_user_profile.".CP_EXT."?user_id=".$user_id."\">".htmlentities($userdata->name, ENT_NOQUOTES, $l['a_meta_charset'])."</a>"; ?>
</th></tr>

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT internet ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_internet', 'h_internet_select'); ?></b></td>
<td class="fillOE">
<select name="internet_id" id="internet_id" size="0" onchange="document.form_interneteditor.submit()">
<?php
if($user_id) {
	$sql = "SELECT * FROM ".K_TABLE_USERS_INTERNET." WHERE internet_userid=".$user_id." ORDER BY internet_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"".$m['internet_id']."\"";
			if($m['internet_id'] == $internet_id) {
				echo " selected=\"selected\"";
			}
			echo ">".htmlentities($m['internet_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
		}
	}
	else {
		F_display_db_error();
	}
}
?>
</select>
</td>
</tr>
<!-- END SELECT internet ==================== -->

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_public', 'h_public_option'); ?></b></td>
<td class="fillEE">
<?php
echo "<input type=\"radio\" name=\"internet_public\" value=\"1\"";
if($internet_public) {echo " checked=\"checked\"";}
echo " />".$l['w_yes']."";
echo "<input type=\"radio\" name=\"internet_public\" value=\"0\"";
if(!$internet_public) {echo " checked=\"checked\"";}
echo " />".$l['w_no']."";
?>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_name', 'h_usrint_name'); ?></b></td>
<td class="fillOE"><input type="text" name="internet_name" id="internet_name" value="<?php echo htmlentities($internet_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_email', 'h_usrint_email'); ?></b></td>
<td class="fillEE"><input type="text" name="internet_email" id="internet_email" value="<?php echo htmlentities($internet_email, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_website', 'h_usrint_website'); ?></b></td>
<td class="fillOE"><input type="text" name="internet_website" id="internet_website" value="<?php echo htmlentities($internet_website, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_icq', 'h_usrint_icq'); ?></b></td>
<td class="fillEE"><input type="text" name="internet_icq" id="internet_icq" value="<?php echo htmlentities($internet_icq, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_aim', 'h_usrint_aim'); ?></b></td>
<td class="fillOE"><input type="text" name="internet_aim" id="internet_aim" value="<?php echo htmlentities($internet_aim, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_yim', 'h_usrint_yim'); ?></b></td>
<td class="fillEE"><input type="text" name="internet_yim" id="internet_yim" value="<?php echo htmlentities($internet_yim, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_msnm', 'h_usrint_msnm'); ?></b></td>
<td class="fillOE"><input type="text" name="internet_msnm" id="internet_msnm" value="<?php echo htmlentities($internet_msnm, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id; ?>" />
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if ($internet_id) {
	F_submit_button("form_interneteditor","menu_mode",$l['w_update']); 
	F_submit_button("form_interneteditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_interneteditor","menu_mode",$l['w_add']); 
F_submit_button("form_interneteditor","menu_mode",$l['w_clear']); 
?>
</td>
</tr>
</table>

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="internet_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />

<!-- format of fields (regular expression) -->
<input type="hidden" name="x_internet_email" id="x_internet_email" value="^([a-zA-Z0-9_\.\-]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$" />
<input type="hidden" name="xl_internet_email" id="xl_internet_email" value="<?php echo $l['w_email']; ?>" />
</form>

<p>
<a href="cp_edit_user.<?php echo CP_EXT; ?>?uemode=user&amp;user_id=<?php echo $user_id; ?>"><b>&lt;&lt;&nbsp;<?php echo $l['t_user_editor']; ?></b></a>
<br />
<a href="cp_edit_user.<?php echo CP_EXT; ?>?uemode=address&amp;user_id=<?php echo $user_id; ?>"><b><?php echo $l['t_address_editor']; ?>&nbsp;&gt;&gt;</b></a>
<br />
<a href="cp_edit_user.<?php echo CP_EXT; ?>?uemode=phone&amp;user_id=<?php echo $user_id; ?>"><b><?php echo $l['t_phone_editor']; ?>&nbsp;&gt;&gt;</b></a>
<br />
<a href="cp_edit_user.<?php echo CP_EXT; ?>?uemode=company&amp;user_id=<?php echo $user_id; ?>"><b><?php echo $l['t_company_editor']; ?>&nbsp;&gt;&gt;</b></a>
</p>

<!-- ====================================================== -->

<!-- Cange focus to internet_id select -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_interneteditor.internet_id.focus();
//]]>
</script>
<!-- END Cange focus to internet_id select -->
<?php
return TRUE;
} //end of function

//******************************************************************************************************************



// ------------------------------------------------------------
// Edit user company
// ------------------------------------------------------------
function F_edit_user_company($user_id) {
	global $l, $db, $aiocp_dp, $selected_language;
	global $uemode, $forcedelete;
	global $menu_mode, $company_id, $company_name, $company_type_id, $company_link, $company_supplier, $company_piva, $company_fc, $company_legal_address_id, $company_billing_address_id, $company_notes, $company_public;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_user.'.CP_EXT);
	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	
	//check authorization
	if(!isset($user_id)) {return FALSE;}
	
	$userid = $_SESSION['session_user_id'];
	$userip = $_SESSION['session_user_ip'];
	$userlevel = $_SESSION['session_user_level'];
	
	if(!(($userid == $user_id) OR ($userlevel >= K_AUTH_EDIT_USER_LEVEL))) {return FALSE;}
	if(($user_id==1) AND ($userlevel!=10)) {return FALSE;} //anonymous user may be edit only by administrator
	
	$company_fiscalcode = "";
	if (isset($company_piva) AND !empty($company_piva)) {
		$company_fiscalcode .= $company_piva;
	}
	if (isset($company_fc) AND !empty($company_fc)) {
		$company_fiscalcode .= " - ".$company_fc;
	}
	
	$userdata = F_get_user_data($user_id);
	

switch($menu_mode) {

	case unhtmlentities($l['w_delete']):
	case $l['w_delete']:{
		F_stripslashes_formfields(); // ask confirmation
		F_print_error("WARNING", $l['m_delete_confirm']);
		?>
		<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_delete" id="form_delete">
		<input type="hidden" name="menu_mode" id="menu_mode" value="forcedelete" />
		<input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id; ?>" />
		<input type="hidden" name="company_id" id="company_id" value="<?php echo $company_id; ?>" />
		<input type="hidden" name="uemode" id="uemode" value="<?php echo $uemode; ?>" />
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
			$sql = "DELETE FROM ".K_TABLE_USERS_COMPANY." WHERE company_id=".$company_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			$company_id=FALSE;
		}
		break;
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update company
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_USERS_COMPANY, "(company_userid='".$user_id."' AND company_name='".$company_name."')", "company_id", $company_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				if ((!empty($company_link)) AND (substr($company_link,0,4)!="http")) {
					$company_link = "http://".$company_link;
				}
				$sql = "UPDATE IGNORE ".K_TABLE_USERS_COMPANY." SET 
				company_userid='".$user_id."', 
				company_name='".$company_name."', 
				company_type_id='".$company_type_id."',
				company_link='".$company_link."', 
				company_supplier='".$company_supplier."', 
				company_fiscalcode='".$company_fiscalcode."', 
				company_legal_address_id='".$company_legal_address_id."',
				company_billing_address_id='".$company_billing_address_id."',
				company_notes='".$company_notes."',
				company_public='".$company_public."'
				WHERE company_id=".$company_id."";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_add']):
	case $l['w_add']:{ // Add company
		if($formstatus = F_check_form_fields()) {
			//check if company_name is unique
			$sql = "SELECT company_name FROM ".K_TABLE_USERS_COMPANY." WHERE (company_userid='".$user_id."' AND company_name='".$company_name."')";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
					F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				if ((!empty($company_link)) AND (substr($company_link,0,4)!="http")) {
					$company_link = "http://".$company_link;
				}
				$sql = "INSERT IGNORE INTO ".K_TABLE_USERS_COMPANY." (
				company_userid, 
				company_name, 
				company_type_id,
				company_link, 
				company_supplier, 
				company_fiscalcode, 
				company_legal_address_id, 
				company_billing_address_id, 
				company_notes, 
				company_public
				) VALUES (
				'".$user_id."', 
				'".$company_name."', 
				'".$company_type_id."',
				'".$company_link."', 
				'".$company_supplier."', 
				'".$company_fiscalcode."', 
				'".$company_legal_address_id."', 
				'".$company_billing_address_id."', 
				'".$company_notes."', 
				'".$company_public."'
				)";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$company_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$company_name = "";
		$company_type_id = "";
		$company_link = "";
		$company_supplier = "";
		$company_fiscalcode = "";
		$company_legal_address_id = "";
		$company_billing_address_id = "";
		$company_notes = "";
		$company_public = 0;
		break;
		}

	default :{ 
		break;
		}

} //end of switch

if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!$company_id) {
			$sql = "SELECT * FROM ".K_TABLE_USERS_COMPANY." WHERE company_userid=".$user_id." ORDER BY company_name LIMIT 1";
		}
		else {$sql = "SELECT * FROM ".K_TABLE_USERS_COMPANY." WHERE company_id=".$company_id." LIMIT 1";}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$company_id = $m['company_id'];
				$user_id = $m['company_userid'];
				$company_name = $m['company_name'];
				$company_type_id = $m['company_type_id'];
				$company_link = $m['company_link'];
				$company_supplier = $m['company_supplier'];
				$company_fiscalcode = $m['company_fiscalcode'];
				$company_legal_address_id = $m['company_legal_address_id'];
				$company_billing_address_id = $m['company_billing_address_id'];
				$company_notes = $m['company_notes'];
				$company_public = $m['company_public'];
			}
			else {
				$company_name = "";
				$company_type_id = "";
				$company_link = "";
				$company_supplier = "";
				$company_fiscalcode = "";
				$company_legal_address_id = "";
				$company_billing_address_id = "";
				$company_notes = "";
				$company_public = 0;
			}
		}
		else {
			F_display_db_error();
		}
	}
}

if (!empty($company_fiscalcode)) {
	$company_fiscal_codes = split(" - ", $company_fiscalcode, 2);
	if ($company_fiscal_codes) {
		$company_piva = $company_fiscal_codes[0];
		$company_fc = $company_fiscal_codes[1];
	} else {
		$company_piva = $company_fiscalcode;
		$company_fc = "";
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_companyeditor" id="form_companyeditor">
<input type="hidden" name="uemode" id="uemode" value="<?php echo $uemode; ?>" />
<input type="hidden" name="company_id" id="company_id" value="<?php echo $company_id; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<th class="edge">
<?php echo "<a class=\"edge\" href=\"cp_user_profile.".CP_EXT."?user_id=".$user_id."\">".htmlentities($userdata->name, ENT_NOQUOTES, $l['a_meta_charset'])."</a>"; ?>
</th></tr>

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_public', 'h_public_option'); ?></b></td>
<td class="fillEE">
<?php
echo "<input type=\"radio\" name=\"company_public\" value=\"1\"";
if($company_public) {echo " checked=\"checked\"";}
echo " />".$l['w_yes']."";
echo "<input type=\"radio\" name=\"company_public\" value=\"0\"";
if(!$company_public) {echo " checked=\"checked\"";}
echo " />".$l['w_no']."";
?>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_name', 'h_usrcmp_name'); ?></b></td>
<td class="fillOE"><input type="text" name="company_name" id="company_name" value="<?php echo htmlentities($company_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_type', 'h_comptype_select'); ?></b></td>
<td class="fillEE">
<select name="company_type_id" id="company_type_id" size="0">
<option value="0">&nbsp;</option>
<?php
	$sql = "SELECT * FROM ".K_TABLE_USERS_COMPANY_TYPES." ORDER BY comptype_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			$select_name = unserialize($m['comptype_name']);
			echo "<option value=\"".$m['comptype_id']."\"";
			if($m['comptype_id'] == $company_type_id) {
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

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_link', 'h_usrcmp_link'); ?></b></td>
<td class="fillOE"><input type="text" name="company_link" id="company_link" value="<?php echo htmlentities($company_link, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_supplier', 'h_usrcmp_supplier'); ?></b></td>
<td class="fillEE">
<?php
echo "<input type=\"radio\" name=\"company_supplier\" value=\"1\"";
if($company_supplier) {echo " checked=\"checked\"";}
echo " />".$l['w_yes']."";
echo "<input type=\"radio\" name=\"company_supplier\" value=\"0\"";
if(!$company_supplier) {echo " checked=\"checked\"";}
echo " />".$l['w_no']."";
?>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_piva', ''); ?></b></td>
<td class="fillOE"><input type="text" name="company_piva" id="company_piva" value="<?php echo htmlentities($company_piva, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_fiscal_code', ''); ?></b></td>
<td class="fillOE"><input type="text" name="company_fc" id="company_fc" value="<?php echo htmlentities($company_fc, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<!-- SELECT address ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_legal_address', 'h_usrcmp_legal_address'); ?></b></td>
<td class="fillEE">
<select name="company_legal_address_id" id="company_legal_address_id" size="0">
<option value="">&nbsp;</option>
<?php
if($user_id) {
	$sql = "SELECT * FROM ".K_TABLE_USERS_ADDRESS." WHERE address_userid=".$user_id." ORDER BY address_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"".$m['address_id']."\"";
			if($m['address_id'] == $company_legal_address_id) {
				echo " selected=\"selected\"";
			}
			echo ">".$m['address_name']."</option>\n";
		}
	}
	else {
		F_display_db_error();
	}
}
?>
</select> 
<a href="cp_edit_user.<?php echo CP_EXT; ?>?uemode=address&amp;user_id=<?php echo $user_id; ?>"><b><?php echo $l['w_edit']; ?></b></a>

<?php
if ( ($userlevel >= K_AUTH_EDIT_USER_LEVEL) AND ($company_legal_address_id) ) {
	//generate a verification code to avoid unauthorized calls to PDF viewer
	$verifycode = F_generate_verification_code($user_id, 4);
	F_generic_button("pdfenvelope", $l['w_envelope'], "PDFENV=window.open('cp_show_ec_pdf_envelope.".CP_EXT."?uid=".$user_id."&amp;usr=0&amp;def=".$company_legal_address_id."&amp;vc=".$verifycode."&amp;selected_language=".$selected_language."','PDFENV','menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')");
}
?>
</td>
</tr>
<!-- END SELECT address ==================== -->

<!-- SELECT address ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_billing_address', 'h_usrcmp_billing_address'); ?></b></td>
<td class="fillOE">
<select name="company_billing_address_id" id="company_billing_address_id" size="0">
<option value="">&nbsp;</option>
<?php
if($user_id) {
	$sql = "SELECT * FROM ".K_TABLE_USERS_ADDRESS." WHERE address_userid=".$user_id." ORDER BY address_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"".$m['address_id']."\"";
			if($m['address_id'] == $company_billing_address_id) {
				echo " selected=\"selected\"";
			}
			echo ">".htmlentities($m['address_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
		}
	}
	else {
		F_display_db_error();
	}
}
?>
</select> 
<a href="cp_edit_user.<?php echo CP_EXT; ?>?uemode=address&amp;user_id=<?php echo $user_id; ?>"><b><?php echo $l['w_edit']; ?></b></a>
<?php
if ( ($userlevel >= K_AUTH_EDIT_USER_LEVEL) AND ($company_billing_address_id) ) {
	//generate a verification code to avoid unauthorized calls to PDF viewer
	$verifycode = F_generate_verification_code($user_id, 4);
	F_generic_button("pdfenvelope", $l['w_envelope'], "PDFENV=window.open('cp_show_ec_pdf_envelope.".CP_EXT."?uid=".$user_id."&amp;usr=0&amp;def=0&amp;vc=".$verifycode."&amp;selected_language=".$selected_language."','PDFENV','menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')");
}
?>
</td>
</tr>
<!-- END SELECT address ==================== -->

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b><?php echo F_display_field_name('w_note', 'h_usrcmp_note'); ?></b>
<br />
<?php F_aiocp_code_button("form_companyeditor","company_notes"); ?></td>
<td class="fillEE">
<textarea cols="30" rows="5" name="company_notes" id="company_notes"><?php echo $company_notes; ?></textarea>
</td>
</tr>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id; ?>" />
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if ($company_id) {
	F_submit_button("form_companyeditor","menu_mode",$l['w_update']); 
	F_submit_button("form_companyeditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_companyeditor","menu_mode",$l['w_add']); 
F_submit_button("form_companyeditor","menu_mode",$l['w_clear']); 
?>
</td>

</tr>
</table>

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="company_name,company_piva,company_fc,company_legal_address_id,company_billing_address_id" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name'].",".$l['w_piva'].",".$l['w_fiscal_code'].",".$l['w_legal_address'].",".$l['w_billing_address']; ?>" />

</form>

<p>
<a href="cp_edit_user.<?php echo CP_EXT; ?>?uemode=user&amp;user_id=<?php echo $user_id; ?>"><b>&lt;&lt;&nbsp;<?php echo $l['t_user_editor']; ?></b></a>
<br />
<a href="cp_edit_user.<?php echo CP_EXT; ?>?uemode=address&amp;user_id=<?php echo $user_id; ?>"><b><?php echo $l['t_address_editor']; ?>&nbsp;&gt;&gt;</b></a>
<br />
<a href="cp_edit_user.<?php echo CP_EXT; ?>?uemode=internet&amp;user_id=<?php echo $user_id; ?>"><b><?php echo $l['t_internet_editor']; ?>&nbsp;&gt;&gt;</b></a>
<br />
<a href="cp_edit_user.<?php echo CP_EXT; ?>?uemode=phone&amp;user_id=<?php echo $user_id; ?>"><b><?php echo $l['t_phone_editor'] ?>&nbsp;&gt;&gt;</b></a>
</p>

<!-- ====================================================== -->

<?php
return TRUE;
} //end of function

//******************************************************************************************************************




// ------------------------------------------------------------
// Edit user data
// $adm if true means that this page has been called from Control Panel
// ------------------------------------------------------------
function F_edit_user_data($user_id, $adm) {
	global $l, $db, $menu_mode, $aiocp_dp, $selected_language;
	global $uemode, $forcedelete;
	global $user_regdate, $user_ip, $user_name, $user_email, $user_password, $user_language, $user_firstname, $user_lastname, $user_birthdate, $user_birthplace, $user_piva, $user_fc, $user_level, $user_group, $leveldata, $levelimage, $user_photo, $user_signature, $user_notes, $user_publicopt, $usroptions, $newpassword, $newpassword_repeat;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_levels.'.CP_EXT);
	require_once('../../shared/code/cp_functions_user.'.CP_EXT);
	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	
	$userid = $_SESSION['session_user_id'];
	$userip = $_SESSION['session_user_ip'];
	$userlevel = $_SESSION['session_user_level'];
	
	//default required fields
	$requiredfields = "user_name";
	$requiredfields_labels = $l['w_name'];
	
	//check authorization and set edit mode
	if($userlevel >= K_AUTH_EDIT_USER_LEVEL) { // full edit for administrator
		$editmode = "adm";
	} else {
		$requiredfields .= ",user_email"; 
		$requiredfields_labels .= ",".$l['w_email'];
		if((!isset($user_id)) OR (!$user_id) OR ($user_id == 1)) {
			$editmode = "reg";  // user registration
		} else {
			if(($userid == $user_id) AND ($user_id != 1)) {
				$editmode = "edt";  // user edit
			} else {
				return FALSE;
			}
		}
	}
	
	$userdata = F_get_user_data($user_id); //get user data
	
	$usregopt = F_get_user_reg_options(); //get user registration options
	$user_options = unserialize($usregopt['options']);
	$admin_inform = unserialize($usregopt['informfor']);
	
	// Initialize variables
	if(!$levelimage) {$levelimage = K_BLANK_IMAGE;}
	if(!$user_photo) {$user_photo = K_BLANK_IMAGE;}
	
	$user_fiscalcode = "";
	if (isset($user_piva) AND !empty($user_piva)) {
		$user_fiscalcode .= $user_piva;
	}
	if (isset($user_fc) AND !empty($user_fc)) {
		$user_fiscalcode .= " - ".$user_fc;
	}
	
	
switch($menu_mode) {

	case unhtmlentities($l['w_delete']):
	case $l['w_delete']:{
		F_stripslashes_formfields(); // ask confirmation
		F_print_error("WARNING", $l['m_delete_confirm']);
		?>
		<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_delete" id="form_delete">
		<input type="hidden" name="menu_mode" id="menu_mode" value="forcedelete" />
		<input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id; ?>" />
		<input type="hidden" name="adm" id="adm" value="<?php echo $adm; ?>" />
		<input type="hidden" name="user_name" id="user_name" value="<?php echo htmlentities($user_name, ENT_COMPAT, $l['a_meta_charset']); ?>" />
		<input type="hidden" name="uemode" id="uemode" value="<?php echo $uemode; ?>" />
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
		F_stripslashes_formfields();
		if($forcedelete == $l['w_delete']) { //check if delete button has been pushed (redundant check)
			if($editmode!="reg") {
				if($user_id==1) { //can't delete anonymous user
					F_print_error("WARNING", $l['m_delete_anonymous']);
				}
				else {
					$sql = "DELETE FROM ".K_TABLE_USERS." WHERE user_id=".$user_id."";
					if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
					}
					$sql = "DELETE FROM ".K_TABLE_USERS_ADDRESS." WHERE address_userid=".$user_id."";
					if(!$r = F_aiocpdb_query($sql, $db)) {
						F_display_db_error();
					}
					$sql = "DELETE FROM ".K_TABLE_USERS_INTERNET." WHERE internet_userid=".$user_id."";
					if(!$r = F_aiocpdb_query($sql, $db)) {
						F_display_db_error();
					}
					$sql = "DELETE FROM ".K_TABLE_USERS_PHONE." WHERE phone_userid=".$user_id."";
					if(!$r = F_aiocpdb_query($sql, $db)) {
						F_display_db_error();
					}
					$sql = "DELETE FROM ".K_TABLE_USERS_COMPANY." WHERE company_userid=".$user_id."";
					if(!$r = F_aiocpdb_query($sql, $db)) {
						F_display_db_error();
					}
					$sql = "DELETE FROM ".K_TABLE_USER_AGENDA." WHERE uagenda_userid=".$user_id."";
					if(!$r = F_aiocpdb_query($sql, $db)) {
						F_display_db_error();
					}
					
					if(($user_photo)AND($user_photo != K_BLANK_IMAGE)) { //if exist delete user photo
						//create user photo path
						$thisuser_photo = K_PATH_IMAGES_USER_PHOTO.$user_photo;
						if(file_exists($thisuser_photo)) { // check if file exist
							if(!unlink($thisuser_photo)) { // delete user photo
								F_print_error("ERROR", $thisuser_photo.": ".$l['m_delete_not']);
							}
						}
					}
					if($admin_inform[2]) { //send email to administrator
						F_send_user_reg_email("d", $user_id, $usregopt['adminemail'], $usregopt, "", "");
					}
					
					$user_id=FALSE;
					
					//print message and exit
					F_print_error("MESSAGE", $user_name.": ".$l['m_user_deleted']);
					
					echo "<a href=\"".$_SERVER['SCRIPT_NAME']."\">".$l['w_reload']." &gt;&gt;</a>";
					return TRUE;
				}
			}
		}
		break;
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update user
		if($editmode!="reg") {
			if($formstatus = F_check_form_fields()) {
				//check if name is unique
				if(!F_check_unique(K_TABLE_USERS, "user_name='".$user_name."'", "user_id", $user_id)) {
					F_print_error("WARNING", $l['m_duplicate_name']);
					$formstatus = FALSE; F_stripslashes_formfields();
					break;
				}
				$sql = "SELECT user_name FROM ".K_TABLE_USERS_VERIFICATION." WHERE user_name='".$user_name."'";
				if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
					F_print_error("WARNING", $l['m_duplicate_name']);
					$formstatus = FALSE; F_stripslashes_formfields();
					break;
				}
				
				$user_publicopt = addslashes(serialize($usroptions));
				
				if(!empty($newpassword) OR !empty($newpassword_repeat)) {// update password
					if($newpassword == $newpassword_repeat) { 
						$user_password = md5($newpassword);
					}
					else { //print message and exit
						F_print_error("WARNING", $l['m_different_passwords']);
						$formstatus = FALSE; F_stripslashes_formfields();
						break;
					}
				}
				
				if($_FILES['userfile']['name']) {
					$user_photo = F_upload_user_image($user_name);
				} //upload photo
				
				//check if email is changed and if email verification is required
				if(($editmode!="adm") AND ($usregopt['verification']) AND ($userdata->email != $user_email)) {
					//generate verification code:
					mt_srand((double)microtime()*1000000);
					$user_verifycode = md5(uniqid(mt_rand(),true));
					//put user data in a temporary table
					$user_regdate = strtotime($user_regdate); // get the original registration timestamp
					$sql = "INSERT IGNORE INTO ".K_TABLE_USERS_VERIFICATION." (
					user_regdate, 
					user_ip, 
					user_name, 
					user_email, 
					user_password, 
					user_language, 
					user_firstname, 
					user_lastname, 
					user_birthdate, 
					user_birthplace, 
					user_fiscalcode, 
					user_level, 
					user_photo, 
					user_signature, 
					user_notes, 
					user_publicopt, 
					user_verifycode
					) VALUES (
					'".$user_regdate."', 
					'".$user_ip."', 
					'".$user_name."', 
					'".$user_email."', 
					'".$user_password."', 
					'".$user_language."', 
					'".$user_firstname."', 
					'".$user_lastname."', 
					'".$user_birthdate."', 
					'".$user_birthplace."', 
					'".$user_fiscalcode."', 
					'".$user_level."', 
					'".$user_photo."', 
					'".$user_signature."', 
					'".$user_notes."', 
					'".$user_publicopt."', 
					'".$user_verifycode."')";
					if(!$r = F_aiocpdb_query($sql, $db)) {
						F_display_db_error();
					}
					else {
						$user_id = F_aiocpdb_insert_id();
						//send verification email to user
						F_send_user_reg_email("u", $user_id, $user_email, $usregopt, $newpassword, $user_verifycode);
						F_print_error("MESSAGE", $user_email.": ".$l['m_user_verification_sent']);
						if($admin_inform[0]) { //send email to administrator
							F_send_user_reg_email("m", $user_id, $usregopt['adminemail'], $usregopt, $user_password, $user_verifycode);
						}
						return TRUE;
					}
				}
				else{ //update without verification
					$sql = "UPDATE IGNORE ".K_TABLE_USERS." SET 
					user_regdate='".$user_regdate."', 
					user_ip='".$user_ip."', 
					user_name='".$user_name."', 
					user_email='".$user_email."', 
					user_password='".$user_password."', 
					user_language='".$user_language."', 
					user_firstname='".$user_firstname."', 
					user_lastname='".$user_lastname."', 
					user_birthdate='".$user_birthdate."', 
					user_birthplace='".$user_birthplace."', 
					user_fiscalcode='".$user_fiscalcode."', 
					user_level='".$user_level."', 
					user_group='".$user_group."', 
					user_photo='".$user_photo."', 
					user_signature='".$user_signature."', 
					user_notes='".$user_notes."', 
					user_publicopt='".$user_publicopt."' 
					WHERE user_id=".$user_id."";
					if(!$r = F_aiocpdb_query($sql, $db)) {
						F_display_db_error();
					}
					//print message
					F_print_error("MESSAGE", $user_name.": ".$l['m_user_updated']);
				}
				if($admin_inform[3]) { //send email to administrator
					F_send_user_reg_email("m", $user_id, $usregopt['adminemail'], $usregopt, $user_password, "");
				}
			}
		}
		break;
	}

	case unhtmlentities($l['w_add']):
	case $l['w_add']:{ // Add user
		if($editmode!="edt") {
			if($formstatus = F_check_form_fields()) {
				//check if user_name is unique
				$sql = "SELECT user_name FROM ".K_TABLE_USERS." WHERE user_name='".$user_name."'";
				if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
					F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
					break;
				}
				$sql = "SELECT user_name FROM ".K_TABLE_USERS_VERIFICATION." WHERE user_name='".$user_name."'";
				if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
					F_print_error("WARNING", $l['m_duplicate_name']);
					$formstatus = FALSE; F_stripslashes_formfields();
					break;
				}
				
				//add item
				$user_publicopt = addslashes(serialize($usroptions));
				$user_ip = $_SERVER['REMOTE_ADDR']; // get the ip number of the user
				
				if(!empty($newpassword) OR !empty($newpassword_repeat)) {// update password
					if($newpassword == $newpassword_repeat) { 
						$user_password = md5($newpassword);
					}
					else { //print message and exit
						F_print_error("WARNING", $l['m_different_passwords']);
						$formstatus = FALSE; F_stripslashes_formfields();
						break;
					}
				}
				
				if($_FILES['userfile']['name']) {//upload photo
					$user_photo = F_upload_user_image($user_name);
				}
				
				if(($editmode!="adm") AND ($usregopt['verification'])) { //check if email verification is required
					//generate verification code:
					mt_srand((double)microtime()*1000000);
					$user_verifycode = md5(uniqid(mt_rand(),true));
					//put user data in a temporary table
					$user_regdate = time(); // get the registration timestamp
					$sql = "INSERT IGNORE INTO ".K_TABLE_USERS_VERIFICATION." (
					user_regdate, 
					user_ip, 
					user_name, 
					user_email, 
					user_password, 
					user_language, 
					user_firstname, 
					user_lastname, 
					user_birthdate, 
					user_birthplace, 
					user_fiscalcode, 
					user_level, 
					user_photo, 
					user_signature, 
					user_notes, 
					user_publicopt, 
					user_verifycode
					) VALUES (
					'".$user_regdate."', 
					'".$user_ip."', 
					'".$user_name."', 
					'".$user_email."', 
					'".$user_password."', 
					'".$user_language."', 
					'".$user_firstname."', 
					'".$user_lastname."', 
					'".$user_birthdate."', 
					'".$user_birthplace."', 
					'".$user_fiscalcode."', 
					'".$user_level."', 
					'".$user_photo."', 
					'".$user_signature."', 
					'".$user_notes."', 
					'".$user_publicopt."', 
					'".$user_verifycode."')";
					if(!$r = F_aiocpdb_query($sql, $db)) {
						F_display_db_error();
					}
					else {
						$user_id = F_aiocpdb_insert_id();
						//send verification email to user
						F_send_user_reg_email("u", $user_id, $user_email, $usregopt, $newpassword, $user_verifycode);
						if($admin_inform[0]) { //send email to administrator
							F_send_user_reg_email("r", $user_id, $usregopt['adminemail'], $usregopt, $newpassword, $user_verifycode);
						}
						//print message and exit
						F_print_error("MESSAGE", $user_email.": ".$l['m_user_verification_sent']);
						return TRUE;
					}
				}
				else { //add user without email verification
					$user_regdate = gmdate("Y-m-d H:i:s"); // get the registration date and time
					$sql = "INSERT IGNORE INTO ".K_TABLE_USERS." (
					user_regdate, 
					user_ip, 
					user_name, 
					user_email, 
					user_password, 
					user_language, 
					user_firstname, 
					user_lastname, 
					user_birthdate, 
					user_birthplace, 
					user_fiscalcode, 
					user_level, 
					user_group,
					user_photo, 
					user_signature, 
					user_notes, 
					user_publicopt
					) VALUES (
					'".$user_regdate."', 
					'".$user_ip."', 
					'".$user_name."', 
					'".$user_email."', 
					'".$user_password."', 
					'".$user_language."', 
					'".$user_firstname."', 
					'".$user_lastname."', 
					'".$user_birthdate."', 
					'".$user_birthplace."', 
					'".$user_fiscalcode."', 
					'".$user_level."',
					'".$user_group ."',
					'".$user_photo."', 
					'".$user_signature."', 
					'".$user_notes."', 
					'".$user_publicopt."')";
					if(!$r = F_aiocpdb_query($sql, $db)) {
						F_display_db_error();
					}
					else {
						$user_id = F_aiocpdb_insert_id();
						if($editmode!="adm") {
							if($admin_inform[1]) { //send email to administrator
								F_send_user_reg_email("c", $user_id, $usregopt['adminemail'], $usregopt, $newpassword, "");
							}
							//print message and exit
							F_print_error("MESSAGE", $user_name.": ".$l['m_user_registered']);
							return TRUE;
						}
					}
				}
			}
		}
		break;
	}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$user_regdate = "";
		$user_ip = "";
		$user_name = "";
		$user_email = "";
		$user_password = "";
		$user_language = "";
		$user_firstname = "";
		$user_lastname = "";
		$user_birthdate = "";
		$user_birthplace = "";
		$user_fiscalcode = "";
		$user_level = "";
		$user_group = 0;
		$levelimage = K_BLANK_IMAGE;
		$user_photo = K_BLANK_IMAGE;
		$user_signature = "";
		$user_notes = "";
		$usroptions = array();
		break;
	}

	default :{ 
		break;
	}

} //end of switch

// --- Initialize variables
if($formstatus) {
	if (($editmode!="reg") AND ($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(((!isset($user_id)) OR (!$user_id)) AND ($editmode=="adm") AND ($adm)) {
			$sql = "SELECT * FROM ".K_TABLE_USERS." ORDER BY user_name LIMIT 1";
		}
		else {
			if(!$user_id) {
				$user_id = $userid;
			}
			$sql = "SELECT * FROM ".K_TABLE_USERS." WHERE user_id=".$user_id." LIMIT 1";
		}
		if($sql) {
			if($r = F_aiocpdb_query($sql, $db)) {
				if($m = F_aiocpdb_fetch_array($r)) {
					$user_id = $m['user_id'];
					$user_regdate = $m['user_regdate'];
					$user_ip = $m['user_ip'];
					$user_name = $m['user_name'];
					$user_email = $m['user_email'];
					$user_password = $m['user_password'];
					$user_language = $m['user_language'];
					$user_firstname = $m['user_firstname'];
					$user_lastname = $m['user_lastname'];
					$user_birthdate = $m['user_birthdate'];
					$user_birthplace = $m['user_birthplace'];
					$user_fiscalcode = $m['user_fiscalcode'];
					$user_level = $m['user_level'];
					$user_group = $m['user_group'];
					$leveldata = F_get_level_data($user_level);
					if($leveldata) {$levelimage = $leveldata->image;}
					else {$levelimage = K_BLANK_IMAGE;}
					$user_photo = $m['user_photo'];
					$user_signature = $m['user_signature'];
					$user_notes = $m['user_notes'];
					$user_publicopt = $m['user_publicopt'];
					$usroptions = unserialize($user_publicopt);
				}
				else {
					$user_regdate = "";
					$user_ip = "";
					$user_name = "";
					$user_email = "";
					$user_password = "";
					$user_language = "";
					$user_firstname = "";
					$user_lastname = "";
					$user_birthdate = "";
					$user_birthplace = "";
					$user_fiscalcode = "";
					$user_level = "";
					$user_group = 0;
					$levelimage = K_BLANK_IMAGE;
					$user_photo = K_BLANK_IMAGE;
					$user_signature = "";
					$user_notes = "";
					$usroptions = array();
				}
			}
			else {
				F_display_db_error();
			}
		}
	}
}

if (!empty($user_fiscalcode)) {
	$user_fiscal_codes = split(" - ", $user_fiscalcode, 2);
	if ($user_fiscal_codes) {
		$user_piva = $user_fiscal_codes[0];
		$user_fc = $user_fiscal_codes[1];
	} else {
		$user_piva = $user_fiscalcode;
		$user_fc = "";
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_usereditor" id="form_usereditor">
<input type="hidden" name="uemode" id="uemode" value="<?php echo $uemode; ?>" />
<input type="hidden" name="user_agreed" id="user_agreed" value="<?php echo $l['w_agree']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<?php
if($editmode=="reg") {
	$requiredfields .= ",newpassword,newpassword_repeat";
	$requiredfields_labels .= ",".$l['w_password'].",".$l['w_password_repeat'];
	echo "<tr class=\"edge\" align=\"left\" valign=\"top\">";
	echo "<th class=\"edge\">";
	echo $l['d_new_user_registration'];
	echo "</th></tr>";
}
?>

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<?php
if(($editmode=="adm")AND($adm)) {
?>
<!-- SELECT user ==================== -->
<tr class="fillO">
<td class="fillOO" align="right">
<a href="../../admin/code/cp_select_users.<?php echo CP_EXT; ?>"><b><?php echo $l['w_user']; ?></b></a>
</td>
<td class="fillOE" colspan="2">
<select name="user_id" id="user_id" size="0" onchange="document.form_usereditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_USERS." ORDER BY user_name";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['user_id']."\"";
		if($m['user_id'] == $user_id) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['user_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select> <?php echo"<a href=\"cp_user_profile.".CP_EXT."?user_id=".$user_id."\">".$l['w_show']."</a>"; ?>
</td>
</tr>
<!-- END SELECT user ==================== -->

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_users', ''); ?></b></td>
<td class="fillOE">
<?php
// display some user stats
$sqlus = "SELECT COUNT(user_id) FROM ".K_TABLE_USERS." WHERE 1";
if($rus = F_aiocpdb_query($sqlus, $db)) {
	$mus = F_aiocpdb_fetch_array($rus);
}
else {
	F_display_db_error();
}
$sqluv = "SELECT COUNT(user_id) FROM ".K_TABLE_USERS_VERIFICATION." WHERE 1";
if($ruv = F_aiocpdb_query($sqluv, $db)) {
	$muv = F_aiocpdb_fetch_array($ruv);
}
else {
	F_display_db_error();
}
echo "".$mus[0]." (+".$muv[0].")";
?>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right">&nbsp;</td>
<td class="fillEE" colspan="2"><hr /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right">
<?php
}
else {
	echo "<tr class=\"fillO\">\n";
	echo "<td class=\"fillOO\" align=\"right\">\n";
	echo"<input type=\"hidden\" name=\"user_id\" id=\"user_id\" value=\"".$user_id."\" />";
}
?>

<b><?php echo F_display_field_name('w_name', 'h_usered_name'); ?></b></td>
<td class="fillOE" colspan="2"><input type="text" name="user_name" id="user_name" value="<?php echo htmlentities($user_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="20" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_email', 'h_usered_email'); ?></b></td>
<td class="fillEE" colspan="2"><input type="text" name="user_email" id="user_email" value="<?php echo $user_email; ?>" size="20" maxlength="255" /> <?php F_print_user_public_option("email"); ?>
<input type="hidden" name="x_user_email" id="x_user_email" value="^([a-zA-Z0-9_\.\-]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$" />
<input type="hidden" name="xl_user_email" id="xl_user_email" value="<?php echo $l['w_email']; ?>" />
</td>
</tr>
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_password', 'h_usered_password'); ?></b></td>
<td class="fillOE" colspan="2">
<input type="password" name="newpassword" id="newpassword" size="20" maxlength="255" />
<input type="hidden" name="user_password" id="user_password" value="<?php echo $user_password; ?>" />
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_password_repeat', 'h_usered_repeat_password'); ?></b></td>
<td class="fillOE" colspan="2">
<input type="password" name="newpassword_repeat" id="newpassword_repeat" size="20" maxlength="255" /></td>
</tr>

<?php if($editmode!="reg") { ?>
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo $l['w_regdate'] ?>:</b></td>
<td class="fillEE" colspan="2"><?php echo $user_regdate; ?>
<input type="hidden" name="user_regdate" id="user_regdate" value="<?php echo $user_regdate; ?>" />
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo $l['w_ip'] ?>:</b></td>
<td class="fillOE" colspan="2"><?php echo $user_ip; ?>
<input type="hidden" name="user_ip" id="user_ip" value="<?php echo $user_ip; ?>" />
<?php
} 

if(($editmode=="adm") AND ($adm) AND ($userlevel>=10)) { //only administrator may change user authorization level
?>
</td>
</tr>
<!-- SELECT LEVEL ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_level', 'h_usered_level'); ?></b></td>
<td class="fillEE"><select name="user_level" id="user_level" size="0">
<?php
	$sql = "SELECT * FROM ".K_TABLE_LEVELS." ORDER BY level_code";
	
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"".$m['level_code']."\"";
			if($m['level_code'] == $user_level) {
				echo " selected=\"selected\"";
			}
			echo ">".htmlentities($m['level_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
		}
	}
	else {
		F_display_db_error();
	}
?>
</select>  <img name="levelimage" src="<?php echo K_PATH_IMAGES_LEVELS; ?><?php echo $levelimage; ?>" border="0" alt="" /></td>
</tr>
<!-- END SELECT LEVEL ==================== -->
<?php
}
else {
	echo "<input type=\"hidden\" name=\"user_level\" id=\"user_level\" value=\"".$user_level."\" />";
	echo "</td></tr>\n";
}
if($editmode=="adm") {
?>
<!-- SELECT  ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_group', 'h_usrgrp_select'); ?></b></td>
<td class="fillOE">
<select name="user_group" id="user_group" size="0">
<?php
	$sql = "SELECT * FROM ".K_TABLE_USERS_GROUPS." ORDER BY usrgrp_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			$select_name = unserialize($m['usrgrp_name']);
			echo "<option value=\"".$m['usrgrp_id']."\"";
			if($m['usrgrp_id'] == $user_group) {
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
<!-- END SELECT  ==================== -->
<?php
}
?>

<!-- SELECT language ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_language', 'h_usered_language'); ?></b></td>
<td class="fillEE">
<select name="user_language" id="user_language" size="0">
<?php
	$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"".$m['language_code']."\"";
			if($m['language_code'] == $user_language) {
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
<!-- END SELECT language ==================== -->

<?php 
//check for no/optional/required fields

if(($editmode=="adm") OR ($user_options[0]>0)) {
?>
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_firstname', ''); ?></b></td>
<td class="fillOE"><input type="text" name="user_firstname" id="user_firstname" value="<?php echo htmlentities($user_firstname, ENT_COMPAT, $l['a_meta_charset']); ?>" size="20" maxlength="255" /> <?php F_print_user_public_option("firstname");
	if($user_options[0]==2) {
		$requiredfields .= ",user_firstname";
		$requiredfields_labels .= ",".$l['w_firstname'];
	}
}
else {
	echo "<input type=\"hidden\" name=\"user_firstname\" id=\"user_firstname\" value=\"".htmlentities($user_firstname, ENT_COMPAT, $l['a_meta_charset'])."\" />";
}
?>
</td>
</tr>
<?php

if(($editmode=="adm") OR ($user_options[1]>0)) {
?>
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_lastname', ''); ?></b></td>
<td class="fillEE"><input type="text" name="user_lastname" id="user_lastname" value="<?php echo htmlentities($user_lastname, ENT_COMPAT, $l['a_meta_charset']); ?>" size="20" maxlength="255" /> <?php F_print_user_public_option("lastname"); ?>
<?php
	if($user_options[1]==2) {
		$requiredfields .= ",user_lastname";
		$requiredfields_labels .= ",".$l['w_lastname'];
	}
}
else {
	echo "<input type=\"hidden\" name=\"user_lastname\" id=\"user_lastname\" value=\"".htmlentities($user_lastname, ENT_COMPAT, $l['a_meta_charset'])."\" />";
}
?>
</td>
</tr>
<?php
if(($editmode=="adm") OR ($user_options[2]>0)) {
?>
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo $l['w_birthdate']; ?> [<?php echo $l['w_datepattern']; ?>]</b></td>
<td class="fillOE"><input type="text" name="user_birthdate" id="user_birthdate" value="<?php echo $user_birthdate; ?>" size="20" maxlength="10" /> <?php F_print_user_public_option("birthdate"); ?>
<input type="hidden" name="x_user_birthdate" id="x_user_birthdate" value="([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})" />
<input type="hidden" name="xl_user_birthdate" id="xl_user_birthdate" value="<?php echo $l['w_birthdate']; ?>" />
<?php 
	if($user_options[2]==2) {
		$requiredfields .= ",user_birthdate";
		$requiredfields_labels .= ",".$l['w_birthdate'];
	}
}
else {
	echo "<input type=\"hidden\" name=\"user_birthdate\" id=\"user_birthdate\" value=\"".$user_birthdate."\" />";
}
?>
</td>
</tr>
<?php
if(($editmode=="adm") OR ($user_options[3]>0)) {
?>
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_birthplace', ''); ?></b></td>
<td class="fillEE"><input type="text" name="user_birthplace" id="user_birthplace" value="<?php echo htmlentities($user_birthplace, ENT_COMPAT, $l['a_meta_charset']); ?>" size="20" maxlength="255" /> <?php F_print_user_public_option("birthplace"); ?>
<?php 
	if($user_options[3]==2) {
		$requiredfields .= ",user_birthplace";
		$requiredfields_labels .= ",".$l['w_birthplace'];
	}
}
else {echo "<input type=\"hidden\" name=\"user_birthplace\" id=\"user_birthplace\" value=\"".htmlentities($user_birthplace, ENT_COMPAT, $l['a_meta_charset'])."\" />";}
?>
</td>
</tr>
<?php
if(($editmode=="adm") OR ($user_options[4]>0)) {
?>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_piva', ''); ?></b></td>
<td class="fillOE"><input type="text" name="user_piva" id="user_piva" value="<?php echo htmlentities($user_piva, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_fiscal_code', ''); ?></b></td>
<td class="fillOE"><input type="text" name="user_fc" id="user_fc" value="<?php echo htmlentities($user_fc, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<?php 
	if($user_options[4]==2) {
		$requiredfields .= ",user_piva,user_fc";
		$requiredfields_labels .= ",".$l['w_piva'].",".$l['w_fiscal_code'];
	}
}
else {echo "<input type=\"hidden\" name=\"user_fiscalcode\" id=\"user_fiscalcode\" value=\"".htmlentities($user_fiscalcode, ENT_COMPAT, $l['a_meta_charset'])."\" />";}
?>
</td>
</tr>
<?php
if(($editmode=="adm") OR ($user_options[5]>0)) {
?>

<!-- Upload photo ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('d_upload_photo', 'h_usered_pic_upload'); ?></b></td>
<td class="fillOE">
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo K_MAX_USER_IMAGE_SIZE; ?>" />
<input type="file" name="userfile" id="userfile" size="20" accept="image/jpeg,image/jpg,image/gif,image/png,image/x-png" />
</td>
</tr>
<!-- END Upload photo ==================== -->

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_photo', 'h_usered_photo'); ?></b></td>
<td class="fillEE">
<select name="user_photo" id="user_photo" size="0" onchange="show_photo()">
<?php
// read directory for files (only graphics files).
$handle = opendir(K_PATH_IMAGES_USER_PHOTO);
	while (false !== ($file = readdir($handle))) {
		$path_parts = pathinfo($file);
		$file_ext = strtolower($path_parts['extension']);
		//check file type (GIF, JPG, PNG)
		if(($file_ext=="png")OR($file_ext=="gif")OR($file_ext=="jpg")OR($file_ext=="jpeg")) {
			if(($file == ($user_name.".".$path_parts['extension']))OR($file == K_BLANK_IMAGE)) {
				echo "<option value=\"".$file."\"";
				if($file == $user_photo) {
					echo " selected=\"selected\"";
				}
				echo ">".$file."</option>\n";
			}
		}
	}
closedir($handle);
?>
</select>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right">&nbsp;</td>
<td class="fillOE" valign="top">
<img name="photoimage" src="<?php echo K_PATH_IMAGES_USER_PHOTO.K_BLANK_IMAGE; ?>" border="1" alt="" />
<?php 
	if($user_options[5]==2) {
		$requiredfields .= ",user_photo";
		$requiredfields_labels .= ",".$l['w_photo'];
	}
}
else {
	echo "<input type=\"hidden\" name=\"user_photo\" id=\"user_photo\" value=\"".$user_photo."\" />";
}
?>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b><?php echo F_display_field_name('w_signature', 'h_usered_signature'); ?></b>
<br />
<?php F_aiocp_code_button("form_usereditor","user_signature"); ?>
</td>
<td class="fillEE">
<textarea cols="30" rows="5" name="user_signature" id="user_signature"><?php echo htmlentities($user_signature, ENT_NOQUOTES, $l['a_meta_charset']); ?></textarea>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b><?php echo F_display_field_name('w_note', 'h_usered_note'); ?></b>
<br />
<?php F_aiocp_code_button("form_usereditor","user_notes"); ?>
</td>
<td class="fillOE">
<textarea cols="30" rows="5" name="user_notes" id="user_notes"><?php echo htmlentities($user_notes, ENT_NOQUOTES, $l['a_meta_charset']); ?></textarea>
</td>
</tr>

<?php if($editmode!="reg") { ?>
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_address', 'h_usered_address'); ?></b></td>
<td class="fillEE">&nbsp;&nbsp;<a href="cp_edit_user.<?php echo CP_EXT; ?>?uemode=address&amp;user_id=<?php echo $user_id; ?>"><b><?php echo $l['t_address_editor']; ?>&nbsp;&gt;&gt;</b></a></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_phone', 'h_usered_phone'); ?></b></td>
<td class="fillOE">&nbsp;&nbsp;<a href="cp_edit_user.<?php echo CP_EXT; ?>?uemode=phone&amp;user_id=<?php echo $user_id; ?>"><b><?php echo $l['t_phone_editor']; ?>&nbsp;&gt;&gt;</b></a></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_internet', 'h_usered_internet'); ?></b></td>
<td class="fillEE">&nbsp;&nbsp;<a href="cp_edit_user.<?php echo CP_EXT; ?>?uemode=internet&amp;user_id=<?php echo $user_id; ?>"><b><?php echo $l['t_internet_editor']; ?>&nbsp;&gt;&gt;</b></a></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_company', 'h_usered_company'); ?></b></td>
<td class="fillOE">&nbsp;&nbsp;<a href="cp_edit_user.<?php echo CP_EXT; ?>?uemode=company&amp;user_id=<?php echo $user_id; ?>"><b><?php echo $l['t_company_editor']; ?>&nbsp;&gt;&gt;</b></a></td>
</tr>

<?php } ?>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php
// show buttons by case
if ( ($user_id) AND ($editmode!="reg") ) {
	F_submit_button("form_usereditor","menu_mode",$l['w_update']);
	F_submit_button("form_usereditor","menu_mode",$l['w_delete']);
}
if($editmode!="edt") {F_submit_button("form_usereditor","menu_mode",$l['w_add']);}

F_submit_button("form_usereditor","menu_mode",$l['w_clear']); 

if ( ($userlevel >= K_AUTH_EDIT_USER_LEVEL) AND ($user_id) AND ($adm)) {
	//generate a verification code to avoid unauthorized calls to PDF viewer
	$verifycode = F_generate_verification_code($user_id, 4);
	F_generic_button("pdfenvelope", $l['w_envelope'], "PDFENV=window.open('cp_show_ec_pdf_envelope.".CP_EXT."?uid=".$user_id."&amp;usr=1&amp;def=0&amp;vc=".$verifycode."&amp;selected_language=".$selected_language."','PDFENV','menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')");
}
?>
</td>

</tr>
</table>

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="<?php echo $requiredfields; ?>" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $requiredfields_labels; ?>" />

<input type="hidden" name="adm" id="adm" value="<?php echo $adm; ?>" />
</form>
<!-- ====================================================== -->

<?php
if(($editmode=="adm") OR ($user_options[5]>0)) {
?>
<!-- Show selected photo image ==================== -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
function show_photo() {
	document.images.photoimage.src= "<?php echo K_PATH_IMAGES_USER_PHOTO; ?>"+document.form_usereditor.user_photo.options[document.form_usereditor.user_photo.selectedIndex].value;
}
show_photo();
//]]>
</script>
<!-- END Show selected photo image ==================== -->
<?php
}
return TRUE;
} //end of function

//------------------------------------------------------------
// Print Options selection
//------------------------------------------------------------
function F_print_user_public_option($optname) {
	global $l, $usroptions;
	echo "(<input type=\"checkbox\" name=\"usroptions[".$optname."]\" id=\"usroptions_".$optname."\" value=\"1\"";
	if($usroptions[$optname]) {echo " checked=\"checked\"";}
	echo " /> ".F_display_field_name('w_public', 'h_public_option').")";
}

//------------------------------------------------------------
// Read User Registration Options from configuration file
//------------------------------------------------------------
function F_get_user_reg_options() {
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	global $l;
	
	if($fp = fopen(K_FILE_USER_REG_OPTIONS, "r")) {
		$contents = fread($fp, filesize(K_FILE_USER_REG_OPTIONS));
		fclose($fp);
	}
	else { //print an error message
		F_print_error("ERROR", "".K_FILE_USER_REG_OPTIONS.": ".$l['m_openfile_not']);
		return FALSE;
	}
	return unserialize($contents);
}

// ----------------------------------------------------------
// Send information/verification message to administrator or user
// $type: 
//
//		r=inform administrator for new user request;
//		c=inform administrator for new user confirm;
//		m=inform administrator for user modify;
//		d=inform administrator for user delete;
//		u=send verification message to user;
// ----------------------------------------------------------
function F_send_user_reg_email($type, $userid, $email, $usregopt, $usrpassword, $userverifycode) {
	global $l, $db, $selected_language, $emailcfg;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_user.'.CP_EXT);
	require_once('../../shared/code/cp_functions_html2txt.'.CP_EXT);
	require_once('../../shared/code/cp_functions_language.'.CP_EXT);
	require_once('../../shared/code/cp_class_mailer.'.CP_EXT);
	
	//Initialize variables
	$maildata = NULL; //this avoid passing variables from URL
	$UserData = NULL;
	
	// Instantiate C_mailer class
	$mail = new C_mailer;
	
	$mail->language = $selected_language;
	
	//Load default values
	$mail->Priority = $emailcfg->Priority;
	$mail->ContentType = $emailcfg->ContentType;
	$mail->Encoding = $emailcfg->Encoding;
	$mail->WordWrap = $emailcfg->WordWrap;
	$mail->Mailer = $emailcfg->Mailer;
	$mail->Sendmail = $emailcfg->Sendmail;
	$mail->UseMSMailHeaders = $emailcfg->UseMSMailHeaders;
	$mail->Host = $emailcfg->Host;
	$mail->Port = $emailcfg->Port;
	$mail->Helo = $emailcfg->Helo;
	$mail->SMTPAuth = $emailcfg->SMTPAuth;
	$mail->Username = $emailcfg->Username;
	$mail->Password = $emailcfg->Password;
	$mail->Timeout = $emailcfg->Timeout;
	$mail->SMTPDebug = $emailcfg->SMTPDebug;
	//$mail->SMTPclassPath = $emailcfg->SMTPclassPath;
	$mail->PluginDir = $emailcfg->PluginDir;
	
	$mail->Sender = $emailcfg->Sender;
	$mail->From = $emailcfg->From;
	$mail->FromName = $emailcfg->FromName;
	if ($emailcfg->Reply) {
		$mail->AddReplyTo($emailcfg->Reply, $emailcfg->ReplyName);
	}
	
		// prepare message by case (load subject from language table)
		switch($type) {
			case "r":{ //request
				$mail->Subject = unhtmlentities(F_word_language(K_DEFAULT_LANGUAGE, "d_new_user_request")); //message subject
				$mail->Body = stripslashes($usregopt['msgadmin']); // message body
				$mail->CharSet = F_word_language(K_DEFAULT_LANGUAGE, "a_meta_charset");
				break;
			}
			case "c":{ //confirmation
				$mail->Subject = unhtmlentities(F_word_language(K_DEFAULT_LANGUAGE, "d_new_user_registration"));
				$mail->Body = stripslashes($usregopt['msgadmin']);
				$mail->CharSet = F_word_language(K_DEFAULT_LANGUAGE, "a_meta_charset");
				break;
			}
			case "m":{ //modify
				$mail->Subject = unhtmlentities(F_word_language(K_DEFAULT_LANGUAGE, "d_user_modify"));
				$mail->Body = stripslashes($usregopt['msgadmin']);
				$mail->CharSet = F_word_language(K_DEFAULT_LANGUAGE, "a_meta_charset");
				break;
			}
			case "d":{ //delete
				$mail->Subject = unhtmlentities(F_word_language(K_DEFAULT_LANGUAGE, "d_user_delete"));
				$mail->Body = stripslashes($usregopt['msgadmin']);
				$mail->CharSet = F_word_language(K_DEFAULT_LANGUAGE, "a_meta_charset");
				break;
			}
			case "u":{ //user verification
				$mail->Subject = unhtmlentities(F_word_language($selected_language, "d_verification_request"));
				$user_vermsg = unserialize($usregopt['vermsg']);
				$mail->Body = stripslashes($user_vermsg[$selected_language]);
				$mail->CharSet = F_word_language($selected_language, "a_meta_charset");
				break;
			}
		}
		
	//check charset
	if(!$mail->CharSet) {$mail->CharSet = $emailcfg->CharSet;}
	
		// get user data
		if(($type=="u")OR($type=="r")) {
			$UserData = F_get_user_verification_data($userid);
		}
		else {
			$UserData = F_get_user_data($userid);
		}
		
		$mail->IsHTML(TRUE); // Sets message type to HTML.
		
		//$userverifycode
		//compose confirmation URL
		$subscribeURL = "".K_PATH_PUBLIC_CODE."cp_user_verification.".CP_EXT."?a=".$UserData->email."&amp;b=".$userverifycode."&amp;c=".$userid."";
		
		//--- Elaborate Templates ---
		$mail->Body = str_replace("#USERNAME#",htmlentities($UserData->name, ENT_NOQUOTES, $l['a_meta_charset']),$mail->Body);
		$mail->Body = str_replace("#PASSWORD#",$usrpassword,$mail->Body);
		$mail->Body = str_replace("#EMAIL#","<a href=\"mailto:".$UserData->email."\">".$UserData->email."</a>",$mail->Body);
		$mail->Body = str_replace("#USERFIRSTNAME#",htmlentities($UserData->firstname, ENT_NOQUOTES, $l['a_meta_charset']),$mail->Body);
		$mail->Body = str_replace("#USERLASTNAME#",htmlentities($UserData->lastname, ENT_NOQUOTES, $l['a_meta_charset']),$mail->Body);
		if (($UserData->ip == "0.0.0.0") OR (!$UserData->ip)) {
			$UserData->ip = $_SERVER['REMOTE_ADDR'];
		}
		$mail->Body = str_replace("#USERIP#",$UserData->ip,$mail->Body);
		$mail->Body = str_replace("#SUBSCRIBEURL#",$subscribeURL,$mail->Body);
		
		//compose alternative TEXT message body
		$mail->AltBody = F_html_to_text($mail->Body, false, true);
		
		$mail->AddAddress($email, ""); //Adds a "To" address
		
		if(!$mail->Send()) { //send email to user
			F_print_error("ERROR", $l['m_unable_to_send_confirmation_email']);
		}
		
		$mail->ClearAddresses(); // Clear all addresses
		$mail->ClearReplyTos(); // Clears all recipients assigned in the ReplyTo array.
return;
}

// ------------------------------------------------------------
// verify and enable user if the arguments are right
// ------------------------------------------------------------
function F_verify_user($email, $verifycode, $userid) {
	global $db, $l;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	$sql = "SELECT * FROM ".K_TABLE_USERS_VERIFICATION." WHERE (user_verifycode='".$verifycode."' AND user_id='".$userid."' AND user_email='".$email."') LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) { // record exist
			$user_regdate = gmdate("Y-m-d H:i:s", $m['user_regdate']);
			//copy values to user table
			$sql2 = "REPLACE INTO ".K_TABLE_USERS." (
			user_regdate, 
			user_ip, 
			user_name, 
			user_email, 
			user_password, 
			user_language, 
			user_firstname, 
			user_lastname, 
			user_birthdate, 
			user_birthplace, 
			user_fiscalcode, 
			user_level, 
			user_photo, 
			user_signature, 
			user_notes, 
			user_publicopt
			) VALUES (
			'".$user_regdate."', 
			'".$m['user_ip']."', 
			'".addslashes($m['user_name'])."', 
			'".$m['user_email']."', 
			'".$m['user_password']."', 
			'".$m['user_language']."', 
			'".addslashes($m['user_firstname'])."', 
			'".addslashes($m['user_lastname'])."', 
			'".$m['user_birthdate']."', 
			'".addslashes($m['user_birthplace'])."', 
			'".addslashes($m['user_fiscalcode'])."', 
			'1', 
			'".$m['user_photo']."', 
			'".addslashes($m['user_signature'])."', 
			'".addslashes($m['user_notes'])."', 
			'".$m['user_publicopt']."')";
			if(!$r2 = F_aiocpdb_query($sql2, $db)) {
				F_display_db_error();
			}
			$user_id = F_aiocpdb_insert_id();
			//delete user from verification table
			$sql3 = "DELETE FROM ".K_TABLE_USERS_VERIFICATION." WHERE user_id=".$userid."";
			if(!$r3 = F_aiocpdb_query($sql3, $db)) {
				F_display_db_error();
			}
			F_print_error("MESSAGE", $m['user_name'].": ".$l['m_user_verification_ok']);
		}
		else {
			F_print_error("ERROR", $l['m_user_verification_error']);
			return FALSE;
		}
	}
	else {
		F_display_db_error();
	}
	
	//call garbage collector
	F_gc_waiting_verify_user();
	
	$usregopt = F_get_user_reg_options(); //get user registration options
	$user_options = unserialize($usregopt['options']);
	$admin_inform = unserialize($usregopt['informfor']);
	
	if($admin_inform[1]) { //send email to administrator
		F_send_user_reg_email("c", $user_id, $usregopt['adminemail'], $usregopt, "", $verifycode);
	}
	return TRUE;
}


// ------------------------------------------------------------
// garbage collector for users waiting verification
// ------------------------------------------------------------
function F_gc_waiting_verify_user() {
	global $db, $l;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	
	$usregopt = F_get_user_reg_options(); //get user registration options
	$user_expiration_time = time() - (K_MAX_WAIT_VERIFICATION * K_SECONDS_IN_DAY);
	
	//delete expired users photos
	$sql = "SELECT user_photo FROM ".K_TABLE_USERS_VERIFICATION." WHERE (user_regdate <= $user_expiration_time)";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			if($m['user_photo'] AND ($m['user_photo'] != K_BLANK_IMAGE)) { //if exist delete user photo
				$thisuser_photo = K_PATH_IMAGES_USER_PHOTO.$m['user_photo']; //create user photo path
				if(file_exists($thisuser_photo)) { // check if file exist
					unlink($thisuser_photo); //delete file;
				}
			}
		}
	}
	else {
		F_display_db_error();
	}
	
	// delete users
	$sql = "DELETE FROM ".K_TABLE_USERS_VERIFICATION." WHERE (user_regdate <= $user_expiration_time)";
	if(!$r = F_aiocpdb_query($sql, $db)) {
		F_display_db_error();
	}
	
	return TRUE;
}


//------------------------------------------------------------
// Lost password 
// send a new verification message to user with new password
//------------------------------------------------------------
function F_lost_password($user_name) {
	global $l, $db;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	require_once('../../shared/code/cp_functions_levels.'.CP_EXT);
	require_once('../../shared/code/cp_functions_user.'.CP_EXT);
	
	//get user_id
	$sql = "SELECT user_id FROM ".K_TABLE_USERS." WHERE user_name='".$user_name."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$user_id = $m['user_id'];
		}
		else {
			return FALSE;
		}
	}
	else {
		F_display_db_error();
	}
	
	$userdata = F_get_user_data($user_id); //get user data
	
	$usregopt = F_get_user_reg_options(); //get user registration options
	$user_options = unserialize($usregopt['options']);
	$admin_inform = unserialize($usregopt['informfor']);
	
	//generate new password
	mt_srand((double)microtime()*1000000);
	$newpassword = substr(md5(uniqid($_SERVER['REMOTE_ADDR'].mt_rand(),true)), 0, K_PASSWORD_LENGTH);
	
	//generate verification code:
	mt_srand((double)microtime()*1000000);
	$user_verifycode = md5(uniqid(mt_rand(),true));
	
	//put user data in a temporary table
	$sql = "INSERT IGNORE INTO ".K_TABLE_USERS_VERIFICATION." (
	user_regdate, 
	user_ip, 
	user_name, 
	user_email, 
	user_password, 
	user_language, 
	user_firstname, 
	user_lastname, 
	user_birthdate, 
	user_birthplace, 
	user_fiscalcode, 
	user_level, 
	user_photo, 
	user_signature, 
	user_notes, 
	user_publicopt, 
	user_verifycode
	) VALUES (
	'".$userdata->regdate."', 
	'".$userdata->ip."', 
	'".addslashes($userdata->name)."', 
	'".$userdata->email."', 
	'".md5($newpassword)."', 
	'".$userdata->language."', 
	'".addslashes($userdata->firstname)."', 
	'".addslashes($userdata->lastname)."', 
	'".$userdata->birthdate."', 
	'".addslashes($userdata->birthplace)."', 
	'".addslashes($userdata->fiscalcode)."', 
	'".$userdata->level."', 
	'".$userdata->photo."', 
	'".addslashes($userdata->signature)."', 
	'".addslashes($userdata->notes)."', 
	'".$userdata->publicopt."', 
	'".$user_verifycode."'
	)";
	if(!$r = F_aiocpdb_query($sql, $db)) {
		F_display_db_error();
	}
	else {
		$newuser_id = F_aiocpdb_insert_id();
		//send verification email to user
		F_send_user_reg_email("u", $newuser_id, $userdata->email, $usregopt, $newpassword, $user_verifycode);
		//F_print_error("MESSAGE", $userdata->email.": ".$l['m_user_verification_sent']);
		F_print_error("MESSAGE", "(email): ".$l['m_user_verification_sent']);
		if($admin_inform[3]) { //send email to administrator
			F_send_user_reg_email("m", $newuser_id, $usregopt['adminemail'], $usregopt, $user_password, $user_verifycode);
		}
	}
	return TRUE;
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
