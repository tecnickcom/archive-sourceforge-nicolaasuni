<?php
//============================================================+
// File name   : cp_edit_newsletter_users.php
// Begin       : 2001-10-18
// Last Update : 2008-07-06
// 
// Description : Edit newsletter users
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
require_once('cp_functions_newsletter_gc.'.CP_EXT);

$pagelevel = K_AUTH_ADMIN_CP_EDIT_NEWSLETTER_USERS;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_newsletter_users_editor'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages;

if (isset($_REQUEST["nluser_userip"])) {
	$nluser_userip = $_REQUEST["nluser_userip"];
} else {
	$nluser_userip = "";
}
if (isset($_REQUEST["nluser_email"])) {
	$nluser_email = $_REQUEST["nluser_email"];
} else {
	$nluser_email = "";
}
if (isset($_REQUEST["nluser_signupdate"])) {
	$nluser_signupdate = $_REQUEST["nluser_signupdate"];
} else {
	$nluser_signupdate = "";
}
if (isset($_REQUEST["nluser_verifycode"])) {
	$nluser_verifycode = $_REQUEST["nluser_verifycode"];
} else {
	$nluser_verifycode = "";
}
if (isset($_REQUEST["nluser_enabled"])) {
	$nluser_enabled = $_REQUEST["nluser_enabled"];
} else {
	$nluser_enabled = "";
}

if (isset($_REQUEST['menu_mode'])) {
	$menu_mode = $_REQUEST['menu_mode'];
} else {
	$menu_mode = "";
}
switch($menu_mode) {

	case unhtmlentities($l['w_delete']):
	case $l['w_delete']:{
		F_stripslashes_formfields(); // delete
		$sql = "DELETE FROM ".K_TABLE_NEWSLETTER_USERS." WHERE nluser_id=".$nluser_id."";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		$nluser_id=FALSE;
		F_gc_newsletter_users(); //garbage collector
		break;
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_NEWSLETTER_USERS, "(nluser_nlcatid='".$nluser_nlcatid."' AND nluser_email='".$nluser_email."')", "nluser_id", $nluser_id)) {
				F_print_error("WARNING", $l['m_duplicate_email']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$sql = "UPDATE IGNORE ".K_TABLE_NEWSLETTER_USERS." SET 
				nluser_nlcatid='".$nluser_nlcatid."', 
				nluser_userid='".$nluser_userid."', 
				nluser_userip='".$nluser_userip."', 
				nluser_email='".$nluser_email."', 
				nluser_signupdate='".$nluser_signupdate."', 
				nluser_verifycode='".$nluser_verifycode."', 
				nluser_enabled='".$nluser_enabled."' 
				WHERE nluser_id=".$nluser_id."";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
			}
		}
		F_gc_newsletter_users(); //garbage collector
		break;
	}

	case unhtmlentities($l['w_add']):
	case $l['w_add']:{ // Add
		if($formstatus = F_check_form_fields()) {
			//check if nluser_email/nluser_nlcatid combination is unique
			$sql = "SELECT * FROM ".K_TABLE_NEWSLETTER_USERS." WHERE (nluser_nlcatid='".$nluser_nlcatid."' AND nluser_email='".$nluser_email."')";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
					F_print_error("WARNING", $l['m_duplicate_email']);
					$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				$nluser_signupdate = time(); // get the actual date and time
				$nluser_userip = $_SERVER['REMOTE_ADDR']; // get the ip number of the actual user
				//generate verification code:
				mt_srand((double)microtime()*1000000);
				$nluser_verifycode = md5(uniqid(mt_rand(),true));
				$sql = "INSERT IGNORE INTO ".K_TABLE_NEWSLETTER_USERS." (
				nluser_nlcatid, 
				nluser_userid, 
				nluser_userip, 
				nluser_email, 
				nluser_signupdate, 
				nluser_verifycode, 
				nluser_enabled
				) VALUES (
				'".$nluser_nlcatid."', 
				'".$nluser_userid."', 
				'".$nluser_userip."', 
				'".$nluser_email."', 
				'".$nluser_signupdate."', 
				'".$nluser_verifycode."', 
				'".$nluser_enabled."')";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$nluser_id = F_aiocpdb_insert_id();
				}
			}
		}
		F_gc_newsletter_users(); //garbage collector
		break;
	}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$nluser_email = "";
		$nluser_enabled = 0;
		F_gc_newsletter_users(); //garbage collector
		break;
	}

	default :{
		break;
	}

} //end of switch

// Initialize variables
$clear_fields = false;
if(!isset($nluser_language)) {
	$nluser_language = $selected_language;
}

//select category
if((!isset($nluser_nlcatid) OR (!$nluser_nlcatid)) OR (isset($changelanguage) AND $changelanguage)) {
	$sql = "SELECT * FROM ".K_TABLE_NEWSLETTER_CATEGORIES." WHERE nlcat_language='".$nluser_language."' ORDER BY nlcat_name LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$nluser_nlcatid = $m['nlcat_id'];
		}
		else {
			$nluser_nlcatid = false;
		}
	}
	else {
		F_display_db_error();
	}
	$nluser_id = false;
}

if($formstatus) {
	if($nluser_nlcatid) {
		if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
			if($changecategory OR (!$nluser_id)) {
				if(!$showonlydisabled) {$sql = "SELECT * FROM ".K_TABLE_NEWSLETTER_USERS." WHERE nluser_nlcatid=".$nluser_nlcatid." ORDER BY nluser_email LIMIT 1";}
				else {$sql = "SELECT * FROM ".K_TABLE_NEWSLETTER_USERS." WHERE (nluser_nlcatid=".$nluser_nlcatid." AND nluser_enabled=0) ORDER BY nluser_email LIMIT 1";}
			}
			else {
				if(!$showonlydisabled) {$sql = "SELECT * FROM ".K_TABLE_NEWSLETTER_USERS." WHERE nluser_id=".$nluser_id." LIMIT 1";}
				else {$sql = "SELECT * FROM ".K_TABLE_NEWSLETTER_USERS." WHERE (nluser_id=".$nluser_id." AND nluser_enabled=0) LIMIT 1";}
			}
			if($r = F_aiocpdb_query($sql, $db)) {
				if($m = F_aiocpdb_fetch_array($r)) {
					$nluser_id = $m['nluser_id'];
					$nluser_nlcatid = $m['nluser_nlcatid'];
					$nluser_userid = $m['nluser_userid'];
					$nluser_userip = $m['nluser_userip'];
					$nluser_email = $m['nluser_email'];
					$nluser_signupdate = $m['nluser_signupdate'];
					$nluser_verifycode = $m['nluser_verifycode'];
					$nluser_enabled = $m['nluser_enabled'];
				}
				else {
					$clear_fields = true;
				}
			}
			else {
				F_display_db_error();
			}
		}
	}
	else {
		$clear_fields = true;
	}
}

if ($clear_fields) {
	$nluser_email = "";
	$nluser_enabled = 0;
	$nluser_userid = 1;
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_nlusreditor" id="form_nlusreditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="nluser_email" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_email']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT  language ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_language', 'h_newslettercat_language'); ?></b></td>
<td class="fillOE">
<input type="hidden" name="changelanguage" id="changelanguage" value="0" />
<select name="nluser_language" id="nluser_language" size="0" onchange="document.form_nlusreditor.changelanguage.value=1; document.form_nlusreditor.submit()">
<?php
	$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"".$m['language_code']."\"";
			if($m['language_code'] == $nluser_language) {
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

<!-- SELECT category ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_category', 'h_newslettercat_select'); ?></b></td>
<td class="fillEE">
<input type="hidden" name="changecategory" id="changecategory" value="0" />
<select name="nluser_nlcatid" id="nluser_nlcatid" size="0" onchange="document.form_nlusreditor.changecategory.value=1; document.form_nlusreditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_NEWSLETTER_CATEGORIES." WHERE nlcat_language='".$nluser_language."' ORDER BY nlcat_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['nlcat_id']."\"";
		if($m['nlcat_id'] == $nluser_nlcatid) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['nlcat_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT category ==================== -->

<!-- SELECT newsletter user ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_user', 'h_newslusr_select'); ?></b></td>
<td class="fillOE">
<select name="nluser_id" id="nluser_id" size="0" onchange="document.form_nlusreditor.submit()">
<?php
if($nluser_nlcatid) {
	if($showonlydisabled) {
		$sql = "SELECT * FROM ".K_TABLE_NEWSLETTER_USERS." WHERE (nluser_nlcatid='".$nluser_nlcatid."' AND nluser_enabled=0) ORDER BY nluser_email";
	}
	else {
		$sql = "SELECT * FROM ".K_TABLE_NEWSLETTER_USERS." WHERE nluser_nlcatid='".$nluser_nlcatid."' ORDER BY nluser_email";
	}
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			if($m['nluser_id'] == $nluser_id) {
				if($m['nluser_enabled']) {$enabledmark="X";}
				else {$enabledmark=" ";}
				echo "<option value=\"".$m['nluser_id']."\" selected=\"selected\">".gmdate("Y-m-d",$m['nluser_signupdate'])." |".$enabledmark."| ".$m['nluser_email']."</option>\n";
			}
			else {
				echo "<option value=\"".$m['nluser_id']."\">".gmdate("Y-m-d",$m['nluser_signupdate'])." |".$enabledmark."| ".$m['nluser_email']."</option>\n";
			}
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
<!-- END SELECT newsletter user ==================== -->

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_users', ''); ?></b></td>
<td class="fillOE">
<?php
// display some user stats
$sqlus = "SELECT COUNT(nluser_id), SUM(nluser_enabled) FROM ".K_TABLE_NEWSLETTER_USERS." WHERE 1";
if($rus = F_aiocpdb_query($sqlus, $db)) {
	if($mus = F_aiocpdb_fetch_array($rus)) {
		echo "".$mus[0]." (".$mus[1]." / ".($mus[0]-$mus[1]).")";
	}
}
else {
	F_display_db_error();
}
?>
</td>
</tr>

<tr class="fillE">
<td class="fillE0">&nbsp;</td>
<td class="fillEE"><hr /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_email', 'h_newsletter_user_email'); ?></b></td>
<td class="fillOE"><input type="text" name="nluser_email" id="nluser_email" value="<?php echo $nluser_email; ?>" size="30" maxlength="60" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_user', 'h_newslusr_user'); ?></b></td>
<td class="fillEE" colspan="2">
<select name="nluser_userid" id="nluser_userid" size="0">
<?php
	$sql = "SELECT * FROM ".K_TABLE_USERS." ORDER BY user_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"".$m['user_id']."\"";
			if($m['user_id'] == $nluser_userid) {
				echo " selected=\"selected\"";
			}
			echo ">".htmlentities($m['user_name'], ENT_NOQUOTES, $l['a_meta_charset'])." (".htmlentities($m['user_firstname'], ENT_NOQUOTES, $l['a_meta_charset'])." ".htmlentities($m['user_lastname'], ENT_NOQUOTES, $l['a_meta_charset']).")</option>\n";
		}
	}
	else {
		F_display_db_error();
	}
?>
</select> <?php echo"<a href=\"cp_user_profile.".CP_EXT."?user_id=".$nluser_userid."\">".$l['w_show']."</a>"; ?>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_status', 'h_newslusr_status'); ?></b></td>
<td class="fillOE">
<select name="nluser_enabled" id="nluser_enabled" size="0">
<?php
			echo "<option value=\"0\"";
			if(!$nluser_enabled) {echo " selected=\"selected\"";}
			echo ">".$l['w_disabled']."</option>\n";
			echo "<option value=\"1\"";
			if($nluser_enabled==1) {echo " selected=\"selected\"";}
			echo ">".$l['w_enabled']."</option>\n";
			echo "<option value=\"2\"";
			if($nluser_enabled==2) {echo " selected=\"selected\"";}
			echo ">".$l['w_banned']."</option>\n";
?>
</select>
<?php
echo "<input type=\"checkbox\" name=\"showonlydisabled\" id=\"showonlydisabled\" value=\"1\" onclick=\"document.form_nlusreditor.submit()\"";
if(isset($showonlydisabled) AND ($showonlydisabled > 0)) {
	echo " checked=\"checked\"";
}
echo " />".F_display_field_name('d_show_only_disabled', 'h_newslusr_only_disabled');
?>
</td>
</tr>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="nluser_signupdate" id="nluser_signupdate" value="<?php echo $nluser_signupdate; ?>" />
<input type="hidden" name="nluser_verifycode" id="nluser_verifycode" value="<?php echo $nluser_verifycode; ?>" />
<input type="hidden" name="nluser_userip" id="nluser_userip" value="<?php echo $nluser_userip; ?>" />
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if ($nluser_id) {
	F_submit_button("form_nlusreditor","menu_mode",$l['w_update']); 
	F_submit_button("form_nlusreditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_nlusreditor","menu_mode",$l['w_add']); 
F_submit_button("form_nlusreditor","menu_mode",$l['w_clear']); 
?>
</td>

</tr>
</table>
</form>
<!-- ====================================================== -->

<!-- Cange focus to nluser_id select -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_nlusreditor.nluser_id.focus();
//]]>
</script>
<!-- END Cange focus to nluser_id select -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
