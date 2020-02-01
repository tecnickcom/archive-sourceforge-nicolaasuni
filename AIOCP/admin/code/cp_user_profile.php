<?php
//============================================================+
// File name   : cp_user_profile.php
// Begin       : 2001-09-15
// Last Update : 2008-07-06
// 
// Description : show all user data
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
require_once('../../shared/code/cp_functions_levels.'.CP_EXT);
require_once('../../shared/code/cp_functions_aiocpcode.'.CP_EXT);

$pagelevel = K_AUTH_ADMIN_CP_USER_PROFILE;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_user_profile'];

// Initialize variables

$rowodd = true;

if(!isset($user_id) OR (!$user_id)) {
	$sql = "SELECT * FROM ".K_TABLE_USERS." ORDER BY user_name LIMIT 1";
} else {
	$sql = "SELECT * FROM ".K_TABLE_USERS." WHERE user_id=".$user_id." LIMIT 1";
}
if($r = F_aiocpdb_query($sql, $db)) {
	if($m = F_aiocpdb_fetch_array($r)) {
		$user_id = $m['user_id'];
		$user_regdate = $m['user_regdate'];
		$user_ip = $m['user_ip'];
		$user_name = htmlentities($m['user_name'], ENT_NOQUOTES, $l['a_meta_charset']);
		$user_email = htmlentities($m['user_email'], ENT_NOQUOTES, $l['a_meta_charset']);
		$user_language = $m['user_language'];
		$user_firstname = htmlentities($m['user_firstname'], ENT_NOQUOTES, $l['a_meta_charset']);
		$user_lastname = htmlentities($m['user_lastname'], ENT_NOQUOTES, $l['a_meta_charset']);
		$user_birthdate = htmlentities($m['user_birthdate'], ENT_NOQUOTES, $l['a_meta_charset']);
		$user_birthplace = htmlentities($m['user_birthplace'], ENT_NOQUOTES, $l['a_meta_charset']);
		$user_fiscalcode = htmlentities($m['user_fiscalcode'], ENT_NOQUOTES, $l['a_meta_charset']);
		//$user_rank = $m['user_rank'];
		$user_level = $m['user_level'];
		$leveldata = F_get_level_data($user_level);
		if($leveldata) {$levelimage = $leveldata->image;}
		else {$levelimage = K_BLANK_IMAGE;}
		$user_photo = $m['user_photo'];
		$user_signature = $m['user_signature'];
		$user_notes = $m['user_notes'];
	}
}
else {
	F_display_db_error();
}

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages; ?>
<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_usershow" id="form_usershow">
<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT user ==================== -->
<tr class="fillO">
<td class="fillOO" align="right">
<a href="cp_select_users.<?php echo CP_EXT; ?>"><b><?php echo $l['w_user']; ?></b></a>
</td>
<td class="fillOE">
<select name="user_id" id="user_id" size="0" onchange="document.form_usershow.submit()">
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
</select>
<a href="cp_edit_user.<?php echo CP_EXT; ?>?uemode=user&amp;user_id=<?php echo $user_id; ?>"><?php echo $l['w_edit']; ?></a>
</td>
</tr>
<!-- END SELECT user ==================== -->

<tr class="fillE">
<td class="fillEO" align="right">&nbsp;</td>
<td class="fillEE"><hr /></td>
</tr>

<?php 

$rowodd = F_usrprofile_display_table_row("", "w_name", "", $user_name, $rowodd);
$rowodd = F_usrprofile_display_table_row("", "w_email", "", "<a href=\"mailto:".$user_email."\">".$user_email."</a>", $rowodd);
$rowodd = F_usrprofile_display_table_row("", 'w_regdate', '', $user_regdate, $rowodd);
$rowodd = F_usrprofile_display_table_row("", 'w_ip', '', $user_ip, $rowodd);
$rowodd = F_usrprofile_display_table_row("", 'w_language', '', $user_language, $rowodd);
$rowodd = F_usrprofile_display_table_row("", 'w_firstname', '', $user_firstname, $rowodd);
$rowodd = F_usrprofile_display_table_row("", 'w_lastname', '', $user_lastname, $rowodd);
$rowodd = F_usrprofile_display_table_row("", 'w_birthdate', '', $user_birthdate, $rowodd);
$rowodd = F_usrprofile_display_table_row("", 'w_birthplace', '', $user_birthplace, $rowodd);
$rowodd = F_usrprofile_display_table_row("", 'w_fiscalcode', 'h_usred_fiscalcode', $user_fiscalcode, $rowodd);


$tmpstr = "";
$sqll = "SELECT * FROM ".K_TABLE_LEVELS." WHERE level_code='".$user_level."' LIMIT 1";
if($rl = F_aiocpdb_query($sqll, $db)) {
	if($ml = F_aiocpdb_fetch_array($rl)) {
		$tmpstr .= "".htmlentities($ml['level_name'], ENT_NOQUOTES, $l['a_meta_charset'])." ";
		if($ml['level_image']) {
			$tmpstr .= "<img src=\"".K_PATH_IMAGES_LEVELS.$ml['level_image']."\" border=\"0\" width=\"".$ml['level_width']."\" height=\"".$ml['level_height']."\" alt=\"".htmlentities($ml['level_name'], ENT_NOQUOTES, $l['a_meta_charset'])."\" /> ";
		}
		$rowodd = F_usrprofile_display_table_row("", 'w_level', '', $tmpstr, $rowodd);
	}
}
else {
	F_display_db_error();
}

if($user_photo AND ($user_photo != K_BLANK_IMAGE)) {
	$tmpstr = "<img src=\"".K_PATH_IMAGES_USER_PHOTO.$user_photo."\" width=\"".K_USER_IMAGE_WIDTH."\" height=\"".K_USER_IMAGE_HEIGHT."\" border=\"1\" alt=\"".$l['w_photo']."\" />";
	$rowodd = F_usrprofile_display_table_row("", 'w_photo', '', $tmpstr, $rowodd);
}

$rowodd = F_usrprofile_display_table_row("", 'w_signature', '', F_decode_aiocp_code(stripslashes($user_signature)), $rowodd);

$rowodd = F_usrprofile_display_table_row("", 'w_note', '', F_decode_aiocp_code(stripslashes($user_notes)), $rowodd);

// display company information
$sqlc = "SELECT * FROM ".K_TABLE_USERS_COMPANY." WHERE company_userid=".$user_id." LIMIT 1";
if($rc = F_aiocpdb_query($sqlc, $db)) {
	if($mc = F_aiocpdb_fetch_array($rc)) {
		if (isset($mc['company_link']) AND !empty($mc['company_link'])) {
			if (substr($mc['company_link'],0,4)!="http") {
				$mc['company_link'] = "http://".$mc['company_link'];
			}
			$tempstr = "<a href=\"".htmlentities(urldecode($mc['company_link']))."\" target=\"_blank\">".htmlentities($mc['company_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</a>";
		}
		else {
			$tempstr = htmlentities($mc['company_name'], ENT_NOQUOTES, $l['a_meta_charset']);
		}
		$rowodd = F_usrprofile_display_table_row("", 'w_company', '', $tempstr, $rowodd);
		if ($mc['company_supplier']) {
			$tmpstr = $l['w_yes'];
		}
		else {
			$tmpstr = $l['w_no'];
		}
		$rowodd = F_usrprofile_display_table_row("", 'w_supplier', '', $tmpstr, $rowodd);
		$rowodd = F_usrprofile_display_table_row("", 'w_fiscalcode', '', htmlentities($mc['company_fiscalcode'], ENT_NOQUOTES, $l['a_meta_charset']), $rowodd);
		$rowodd = F_usrprofile_display_table_row("", 'w_note', '', F_decode_aiocp_code(stripslashes($mc['company_notes'])), $rowodd);
	}
}
else {
	F_display_db_error();
}

?>


<!-- ADDRESS DATA ==================== -->

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b><?php echo F_display_field_name('w_address', ''); ?></b></td>
<td class="fillOE">
<?php
$sql2 = "SELECT * FROM ".K_TABLE_USERS_ADDRESS." WHERE address_userid=".$user_id." ORDER BY address_name";
if($r2 = F_aiocpdb_query($sql2, $db)) {
	while($m2 = F_aiocpdb_fetch_array($r2)) {
?>

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<th class="edge">
<?php echo htmlentities($m2['address_name'], ENT_NOQUOTES, $l['a_meta_charset']); ?>
</th>
</tr>

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<?php
if ($m2['address_id'] == $mc['company_legal_address_id']) {
	echo "<tr class=\"fillE\"><td class=\"fillEO\" colspan=\"2\">".$l['w_legal_address']."</td></td></tr>";
}
elseif ($m2['address_id'] == $mc['company_billing_address_id']) {
	echo "<tr class=\"fillE\"><td class=\"fillEO\" colspan=\"2\">".$l['w_billing_address']."</td></td></tr>";
}
?>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_address', ''); ?></b></td>
<td class="fillEE"><?php echo htmlentities($m2['address_address'], ENT_NOQUOTES, $l['a_meta_charset']); ?></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_city', ''); ?></b></td>
<td class="fillOE"><?php echo htmlentities($m2['address_city'], ENT_NOQUOTES, $l['a_meta_charset']); ?></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_state', ''); ?></b></td>
<td class="fillEE"><?php echo htmlentities($m2['address_state'], ENT_NOQUOTES, $l['a_meta_charset']); ?></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_postcode', ''); ?></b></td>
<td class="fillOE"><?php echo htmlentities($m2['address_postcode'], ENT_NOQUOTES, $l['a_meta_charset']); ?></td>
</tr>

<?php
$sql3 = "SELECT * FROM ".K_TABLE_COUNTRIES." WHERE country_id=".$m2['address_countryid']."";
if($r3 = F_aiocpdb_query($sql3, $db)) {
	while($m3 = F_aiocpdb_fetch_array($r3)) {
			echo "<tr class=\"fillE\"><td class=\"fillEO\" align=\"right\"><b>".$l['w_country']."</b></td><td class=\"fillEE\">".htmlentities($m3['country_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</td></tr>";
			echo "<tr class=\"fillO\"><td class=\"fillOO\" align=\"right\"><b>".$l['w_flag']."</b></td><td class=\"fillOE\"><img src=\"".K_PATH_IMAGES_FLAGS.$m3['country_flag']."\" border=\"0\" alt=\"\" width=\"".$m3['country_width']."\" height=\"".$m3['country_height']."\" /></td></tr>";
	}
}
else {
	F_display_db_error();
}
?>

</table>
</td></tr></table><br />
<?php
	}
}
else {
	F_display_db_error();
}
?>
</td>
</tr>
<!-- END ADDRESS DATA ==================== -->

<!-- PHONE DATA ==================== -->
<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b><?php echo F_display_field_name('w_phone', ''); ?></b></td>
<td class="fillEE">

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">
<?php
$sqlp = "SELECT * FROM ".K_TABLE_USERS_PHONE." WHERE phone_userid=".$user_id." ORDER BY phone_name";
if($rp = F_aiocpdb_query($sqlp, $db)) {
	while($mp = F_aiocpdb_fetch_array($rp)) {
		$rowodd = F_usrprofile_display_table_row(htmlentities($mp['phone_name'], ENT_NOQUOTES, $l['a_meta_charset']), "", "", $mp['phone_number'], $rowodd);
	}
}
else {
	F_display_db_error();
}
?>
</table>
</td></tr></table>
</td>
</tr>
<!-- END PHONE DATA ==================== -->

<!-- INTERNET DATA ==================== -->
<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b><?php echo F_display_field_name('w_internet', ''); ?></b></td>
<td class="fillOE">
<?php
$sql = "SELECT * FROM ".K_TABLE_USERS_INTERNET." WHERE internet_userid=".$user_id." ORDER BY internet_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
?>
<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<th class="edge">
<?php echo htmlentities($m['internet_name'], ENT_NOQUOTES, $l['a_meta_charset']); ?>
</th>
</tr>

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<?php 

if($m['internet_email']) {
	$rowodd = F_usrprofile_display_table_row("", "w_email", "", "<a href=\"mailto:".$m['internet_email']."\">".$m['internet_email']."</a>", $rowodd);
}

if($m['internet_website']) {
	$rowodd = F_usrprofile_display_table_row("", "w_website", "", "<a href=\"".$m['internet_website']."\" target=\"_blank\">".$m['internet_website']."</a>", $rowodd);
}

if($m['internet_icq']) {
	$rowodd = F_usrprofile_display_table_row("", "w_icq", "", "<a href=\"http://wwp.icq.com/".$m['internet_icq']."#pager\" target=\"_blank\">".$m['internet_icq']."</a>", $rowodd);
}

if($m['internet_aim']) {
	$rowodd = F_usrprofile_display_table_row("", "w_aim", "", "<a href=\"aim:goim?screenname=".$m['internet_aim']."&amp;message=".$l['w_aim_message']."\" target=\"_blank\">".$m['internet_aim']."</a>", $rowodd);
}

if($m['internet_yim']) {
	$rowodd = F_usrprofile_display_table_row("", "w_yim", "", "<a href=\"http://edit.yahoo.com/config/send_webmesg?.target=".$m['internet_yim']."&amp.src=pg\" target=\"_blank\">".$m['internet_yim']."</a>", $rowodd);
}

if($m['internet_msnm']) {
	$rowodd = F_usrprofile_display_table_row("", "w_msnm", "", "".$m['internet_msnm']."", $rowodd);
}

?>

</table>
</td></tr></table><br />

<?php
	}
}
else {
	F_display_db_error();
}
?>


</td>
</tr>
<!-- END INTERNET DATA ==================== -->

</table>

</td>
</tr>
</table>
</form>
<!-- ====================================================== -->

<!-- Cange focus to user_id select -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_usershow.user_id.focus();
//]]>
</script>
<!-- END Cange focus to user_id select -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

// ------------------------------------------------------------
//display user profile table row
// ------------------------------------------------------------
function F_usrprofile_display_table_row($name="", $rowname, $rowhelp, $value, $rowodd) {
	//change style for each row
	if (isset($rowodd) AND ($rowodd)) {
		$rowclass = "O";
		$rowodd = 0;
	} else {
		$rowclass = "E";
		$rowodd = 1;
	}
	
	echo "<tr class=\"fill".$rowclass."\">";
	echo "<td class=\"fill".$rowclass."O\" align=\"right\" valign=\"top\">";
	if (!empty($name)) {
		echo "<b>".$name."</b>";
	}
	else {
		echo "<b>".F_display_field_name($rowname, $rowhelp)."</b>";
	}
	echo "</td>";
	
	echo "<td class=\"fill".$rowclass."E\" valign=\"top\">";
	echo "".$value."";
	echo "</td>";
	echo "</tr>";
	return !$rowodd;
}
?>

<?php
//============================================================+
// END OF FILE                                                 
//============================================================+
?>
