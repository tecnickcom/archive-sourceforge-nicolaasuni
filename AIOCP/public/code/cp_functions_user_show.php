<?php
//============================================================+
// File name   : cp_functions_user_show.php                    
// Begin       : 2001-02-05                                    
// Last Update : 2003-11-18                                    
//                                                             
// Description : Functions for show user data (public profile) 
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

//------------------------------------------------------------
// Show user public profile
//------------------------------------------------------------
function F_show_user_profile($user_id) {
	global $l, $db;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_user.'.CP_EXT);
	require_once('../../shared/code/cp_functions_user_edit.'.CP_EXT);
	require_once('../../shared/code/cp_functions_levels.'.CP_EXT);
	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	require_once('../../shared/code/cp_functions_aiocpcode.'.CP_EXT);
	
	$rowodd = false;
	
	// Read user data
	$user = F_get_user_data($user_id);
	$usr_pub_options = unserialize($user->publicopt);
	$usregopt = F_get_user_reg_options(); //get user registration options
	$user_reg_options = unserialize($usregopt['options']);
?>

<!-- ====================================================== -->
<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<?php 

$rowodd = F_usrprofile_display_table_row("", "w_name", "", htmlentities($user->name, ENT_NOQUOTES, $l['a_meta_charset']), $rowodd);

if($usr_pub_options['email']) {
	$rowodd = F_usrprofile_display_table_row("", "w_email", "", "<a href=\"mailto:".$user->email."\">".$user->email."</a>", $rowodd);
}

$rowodd = F_usrprofile_display_table_row("", 'w_regdate', '', $user->regdate, $rowodd);
$rowodd = F_usrprofile_display_table_row("", 'w_language', '', $user->language, $rowodd);

if($usr_pub_options['firstname'] AND ($user_reg_options[0]>0)) {
	$rowodd = F_usrprofile_display_table_row("", 'w_firstname', '', htmlentities($user->firstname, ENT_NOQUOTES, $l['a_meta_charset']), $rowodd);
}

if($usr_pub_options['lastname'] AND ($user_reg_options[1]>0)) {
	$rowodd = F_usrprofile_display_table_row("", 'w_lastname', '', htmlentities($user->lastname, ENT_NOQUOTES, $l['a_meta_charset']), $rowodd);
}

if($usr_pub_options['birthdate'] AND ($user_reg_options[2]>0)) {
	$rowodd = F_usrprofile_display_table_row("", 'w_birthdate', '', $user->birthdate, $rowodd);
}


if($usr_pub_options['birthplace'] AND ($user_reg_options[3]>0)) {
	$rowodd = F_usrprofile_display_table_row("", 'w_birthplace', '', htmlentities($user->birthplace, ENT_NOQUOTES, $l['a_meta_charset']), $rowodd);
}

if($usr_pub_options['birthplace'] AND ($user_reg_options[3]>0)) {
	$rowodd = F_usrprofile_display_table_row("", 'w_fiscalcode', 'h_usred_fiscalcode', htmlentities($user->fiscalcode, ENT_NOQUOTES, $l['a_meta_charset']), $rowodd);
}

$tmpstr = "";
$sqll = "SELECT * FROM ".K_TABLE_LEVELS." WHERE level_code='".$user->level."' LIMIT 1";
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

if($user->photo AND ($user->photo != K_BLANK_IMAGE)) {
	$tmpstr = "<img src=\"".K_PATH_IMAGES_USER_PHOTO.$user->photo."\" width=\"".K_USER_IMAGE_WIDTH."\" height=\"".K_USER_IMAGE_HEIGHT."\" border=\"1\" alt=\"".$l['w_photo']."\" />";
	$rowodd = F_usrprofile_display_table_row("", 'w_photo', '', $tmpstr, $rowodd);
}

if($user->signature) {
	$rowodd = F_usrprofile_display_table_row("", 'w_signature', '', F_decode_aiocp_code(stripslashes($user->signature)), $rowodd);
}

if($user->notes) {
	$rowodd = F_usrprofile_display_table_row("", 'w_note', '', F_decode_aiocp_code(stripslashes($user->notes)), $rowodd);
}

// display company information
$sqlc = "SELECT * FROM ".K_TABLE_USERS_COMPANY." WHERE company_userid=".$user_id." LIMIT 1";
if($rc = F_aiocpdb_query($sqlc, $db)) {
	if($mc = F_aiocpdb_fetch_array($rc)) {
		if ($mc['company_link']) {
			$tempstr = "<a href=\"".htmlentities(urldecode($mc['company_link']))."\" target=\"_blank\">".htmlentities($mc['company_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</a>";
		}
		else {
			$tempstr = htmlentities($mc['company_name'], ENT_NOQUOTES, $l['a_meta_charset']);
		}
		$rowodd = F_usrprofile_display_table_row("", 'w_company', '', $tempstr, $rowodd);
		$rowodd = F_usrprofile_display_table_row("", 'w_fiscalcode', '', htmlentities($mc['company_fiscalcode'], ENT_NOQUOTES, $l['a_meta_charset']), $rowodd);
	}
}
else {
	F_display_db_error();
}
?>
<!-- END COMPANY DATA ==================== -->

<!-- ADDRESS DATA ==================== -->

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b><?php echo F_display_field_name('w_address', ''); ?></b></td>
<td class="fillEE">
<?php
$sql2 = "SELECT * FROM ".K_TABLE_USERS_ADDRESS." WHERE ((address_userid=".$user_id.") AND (address_public=1)) ORDER BY address_name";
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
<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b><?php echo F_display_field_name('w_phone', ''); ?></b></td>
<td class="fillOE">

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">
<?php
$sqlp = "SELECT * FROM ".K_TABLE_USERS_PHONE." WHERE ((phone_userid=".$user_id.") AND (phone_public=1)) ORDER BY phone_name";
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
<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b><?php echo F_display_field_name('w_internet', ''); ?></b></td>
<td class="fillEE">
<?php
$sql = "SELECT * FROM ".K_TABLE_USERS_INTERNET." WHERE ((internet_userid=".$user_id.") AND (internet_public=1)) ORDER BY internet_name";
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

<?php
// show EDIT button
if( ($user_id!=1) AND (($_SESSION['session_user_id'] == $user_id) OR ($_SESSION['session_user_level'] >= K_AUTH_EDIT_USER_LEVEL))) {
	echo"<form action=\"cp_edit_user.".CP_EXT."\" method=\"post\" enctype=\"multipart/form-data\" name=\"form_eduser\" id=\"form_eduser\">";
	echo "<input type=\"hidden\" name=\"uemode\" id=\"uemode\" value=\"user\" />";
	echo "<input type=\"hidden\" name=\"user_id\" id=\"user_id\" value=\"".$user_id."\" />";
	echo "<tr class=\"edge\" align=\"left\" valign=\"top\">";
	echo "<th class=\"edge\" align=\"center\">";
	echo "<input type=\"hidden\" name=\"menu_mode\" id=\"menu_mode\" value=\"\" />";
	F_submit_button("form_eduser","menu_mode",$l['w_edit']);
	echo "</th></tr>";
	echo "</form>";
}
?>

</table>
	
<?php
return TRUE;
} //end of function

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

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
