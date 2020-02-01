<?php
//============================================================+
// File name   : cp_edit_newsletter_categories.php             
// Begin       : 2001-10-15                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit categories for newsletter                
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
require_once('../../shared/config/cp_email_config.'.CP_EXT);
require_once('../../shared/code/cp_functions_form.'.CP_EXT);

$pagelevel = K_AUTH_ADMIN_CP_EDIT_NEWSLETTER_CATEGORIES;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_newsletter_categories_editor'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages; ?>
<!-- ====================================================== -->
<?php
if (isset($_REQUEST['menu_mode'])) {
	$menu_mode = $_REQUEST['menu_mode'];
} else {
	$menu_mode = "";
}
switch($menu_mode) {

	case unhtmlentities($l['w_delete']):
	case $l['w_delete']:{
		F_stripslashes_formfields(); // ask confirmation
		F_print_error("WARNING", $l['m_delete_confirm']);
		?>
		<p><?php echo $l['t_warning'].": ".$l['d_newsletter_cat_delete']; ?></p>
		
		<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_delete" id="form_delete">
		<input type="hidden" name="menu_mode" id="menu_mode" value="forcedelete" />
		<input type="hidden" name="nlcat_id" id="nlcat_id" value="<?php echo $nlcat_id; ?>" />
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
			$sql = "DELETE FROM ".K_TABLE_NEWSLETTER_CATEGORIES." WHERE nlcat_id=".$nlcat_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			$sql = "DELETE FROM ".K_TABLE_NEWSLETTER_MESSAGES." WHERE nlmsg_nlcatid=".$nlcat_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			$sql = "DELETE FROM ".K_TABLE_NEWSLETTER_USERS." WHERE nluser_nlcatid=".$nlcat_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			$nlcat_id=FALSE;
		}
		break;
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update newsletter categories
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_NEWSLETTER_CATEGORIES, "(nlcat_name='".$nlcat_name."' AND nlcat_language='".$nlcat_language."')", "nlcat_id", $nlcat_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$sql = "UPDATE IGNORE ".K_TABLE_NEWSLETTER_CATEGORIES." SET 
				nlcat_language='".$nlcat_language."', 
				nlcat_level='".$nlcat_level."', 
				nlcat_admin_email='".$nlcat_admin_email."', 
				nlcat_informfor='".$nlcat_informfor."', 
				nlcat_msg_admin='".$nlcat_msg_admin."', 
				nlcat_sender='".$nlcat_sender."', 
				nlcat_fromemail='".$nlcat_fromemail."', 
				nlcat_fromname='".$nlcat_fromname."', 
				nlcat_replyemail='".$nlcat_replyemail."', 
				nlcat_replyname='".$nlcat_replyname."', 
				nlcat_name='".$nlcat_name."', 
				nlcat_description='".$nlcat_description."', 
				nlcat_msg_header='".$nlcat_msg_header."', 
				nlcat_msg_footer='".$nlcat_msg_footer."', 
				nlcat_msg_confirmation='".$nlcat_msg_confirmation."', 
				nlcat_enabled='".$nlcat_enabled."',
				nlcat_all_users='".$nlcat_all_users."'
				WHERE nlcat_id=".$nlcat_id."";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_add']):
	case $l['w_add']:{ // Add newsletter categories
		if($formstatus = F_check_form_fields()) {
			//check if nlcat_name/nlcat_language combination is unique
			$sql = "SELECT nlcat_name,nlcat_language FROM ".K_TABLE_NEWSLETTER_CATEGORIES." WHERE (nlcat_name='".$nlcat_name."' AND nlcat_language='".$nlcat_language."')";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				$sql = "INSERT IGNORE INTO ".K_TABLE_NEWSLETTER_CATEGORIES." (
				nlcat_language, 
				nlcat_level, 
				nlcat_admin_email, 
				nlcat_informfor, 
				nlcat_msg_admin, 
				nlcat_sender, 
				nlcat_fromemail, 
				nlcat_fromname, 
				nlcat_replyemail, 
				nlcat_replyname, 
				nlcat_name, 
				nlcat_description, 
				nlcat_msg_header, 
				nlcat_msg_footer, 
				nlcat_msg_confirmation, 
				nlcat_enabled,
				nlcat_all_users
				) VALUES (
				'".$nlcat_language."', 
				'".$nlcat_level."', 
				'".$nlcat_admin_email."', 
				'".$nlcat_informfor."', 
				'".$nlcat_msg_admin."', 
				'".$nlcat_sender."', 
				'".$nlcat_fromemail."', 
				'".$nlcat_fromname."', 
				'".$nlcat_replyemail."', 
				'".$nlcat_replyname."', 
				'".$nlcat_name."', 
				'".$nlcat_description."', 
				'".$nlcat_msg_header."', 
				'".$nlcat_msg_footer."', 
				'".$nlcat_msg_confirmation."', 
				'".$nlcat_enabled."',
				'".$nlcat_all_users."'
				)";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$nlcat_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$nlcat_level = 0;
		$nlcat_admin_email = $emailcfg->AdminEmail;
		$nlcat_informfor = 0;
		$nlcat_msg_admin = $emailcfg->MsgAdmin;
		$nlcat_sender = $emailcfg->Sender;
		$nlcat_fromemail = $emailcfg->From;
		$nlcat_fromname = $emailcfg->FromName;
		$nlcat_replyemail = $emailcfg->Reply;
		$nlcat_replyname = $emailcfg->ReplyName;
		$nlcat_name = "";
		$nlcat_description = "";
		$nlcat_msg_header = $emailcfg->MsgHeader;
		$nlcat_msg_footer = $emailcfg->MsgFooter;
		$nlcat_msg_confirmation = $emailcfg->confirmationMessage;
		$nlcat_enabled = 1;
		$nlcat_all_users = 0;
		break;
		}

	default :{ 
		break;
		}

} //end of switch

// Initialize variables
if(!isset($nlcat_language)) {
	$nlcat_language = $selected_language;
}

if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if((!isset($nlcat_id) OR (!$nlcat_id)) OR (isset($changelanguage) AND $changelanguage)) {
			$sql = "SELECT * FROM ".K_TABLE_NEWSLETTER_CATEGORIES." WHERE nlcat_language='".$nlcat_language."' ORDER BY nlcat_name LIMIT 1";
		} else {
			$sql = "SELECT * FROM ".K_TABLE_NEWSLETTER_CATEGORIES." WHERE nlcat_id=".$nlcat_id." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$nlcat_id = $m['nlcat_id'];
				$nlcat_language = $m['nlcat_language'];
				$nlcat_level = $m['nlcat_level'];
				$nlcat_admin_email = $m['nlcat_admin_email'];
				$nlcat_informfor = $m['nlcat_informfor'];
				$nlcat_msg_admin = $m['nlcat_msg_admin'];
				$nlcat_sender = $m['nlcat_sender'];
				$nlcat_fromemail = $m['nlcat_fromemail'];
				$nlcat_fromname = $m['nlcat_fromname'];
				$nlcat_replyemail = $m['nlcat_replyemail'];
				$nlcat_replyname = $m['nlcat_replyname'];
				$nlcat_name = $m['nlcat_name'];
				$nlcat_description = $m['nlcat_description'];
				$nlcat_msg_header = $m['nlcat_msg_header'];
				$nlcat_msg_footer = $m['nlcat_msg_footer'];
				$nlcat_msg_confirmation = $m['nlcat_msg_confirmation'];
				$nlcat_enabled = $m['nlcat_enabled'];
				$nlcat_all_users = $m['nlcat_all_users'];
			}
			else {
				$nlcat_level = 0;
				$nlcat_admin_email = $emailcfg->AdminEmail;
				$nlcat_informfor = 0;
				$nlcat_msg_admin = $emailcfg->MsgAdmin;
				$nlcat_sender = $emailcfg->Sender;
				$nlcat_fromemail = $emailcfg->From;
				$nlcat_fromname = $emailcfg->FromName;
				$nlcat_replyemail = $emailcfg->Reply;
				$nlcat_replyname = $emailcfg->ReplyName;
				$nlcat_name = "";
				$nlcat_description = "";
				$nlcat_msg_header = $emailcfg->MsgHeader;
				$nlcat_msg_footer = $emailcfg->MsgFooter;
				$nlcat_msg_confirmation = $emailcfg->confirmationMessage;
				$nlcat_enabled = 1;
				$nlcat_all_users = 0;
			}
		}
		else {
			F_display_db_error();
		}
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_nlcateditor" id="form_nlcateditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="nlcat_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT  language ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_language', 'h_newslettercat_language'); ?></b></td>
<td class="fillOE">
<input type="hidden" name="changelanguage" id="changelanguage" value="0" />
<select name="nlcat_language" id="nlcat_language" size="0" onchange="document.form_nlcateditor.changelanguage.value=1; document.form_nlcateditor.submit()">
<?php
	$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"".$m['language_code']."\"";
			if($m['language_code'] == $nlcat_language) {
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

<!-- SELECT newsletter categories ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_category', 'h_newslettercat_select'); ?></b></td>
<td class="fillEE">
<select name="nlcat_id" id="nlcat_id" size="0" onchange="document.form_nlcateditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_NEWSLETTER_CATEGORIES." WHERE nlcat_language='".$nlcat_language."' ORDER BY nlcat_name";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['nlcat_id']."\"";
		if($m['nlcat_id'] == $nlcat_id) {
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
<!-- END SELECT newsletter categories ==================== -->

<tr class="fillO">
<td class="fillOO">&nbsp;</td>
<td class="fillOE"><hr /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_enabled', 'h_newslettercat_enable'); ?></b></td>
<td class="fillEE">
<select name="nlcat_enabled" id="nlcat_enabled" size="0">
<?php
		if($nlcat_enabled) {
			echo "<option value=\"1\" selected=\"selected\">".$l['w_yes']."</option>\n";
			echo "<option value=\"0\">".$l['w_no']."</option>\n";
		}
		else {
			echo "<option value=\"1\">".$l['w_yes']."</option>\n";
			echo "<option value=\"0\" selected=\"selected\">".$l['w_no']."</option>\n";
		}
?>
</select>
</td>
</tr>

<!-- SELECT LEVEL ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_level', 'h_newslettercat_level'); ?></b></td>
<td class="fillOE"><select name="nlcat_level" id="nlcat_level" size="0">
<?php
$sql = "SELECT * FROM ".K_TABLE_LEVELS." ORDER BY level_code ";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['level_code']."\"";
		if($m['level_code'] == $nlcat_level) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['level_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT LEVEL ==================== -->

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_email_sender', 'h_newslettercat_email_sender'); ?></b></td>
<td class="fillEE"><input type="text" name="nlcat_sender" id="nlcat_sender" value="<?php echo htmlentities($nlcat_sender, ENT_COMPAT, $l['a_meta_charset']); ?>" size="50" maxlength="64" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_email_from', 'h_newslettercat_email_from'); ?></b></td>
<td class="fillOE"><input type="text" name="nlcat_fromemail" id="nlcat_fromemail" value="<?php echo $nlcat_fromemail; ?>" size="50" maxlength="64" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_email_from_name', 'h_newslettercat_email_name'); ?></b></td>
<td class="fillEE"><input type="text" name="nlcat_fromname" id="nlcat_fromname" value="<?php echo htmlentities($nlcat_fromname, ENT_COMPAT, $l['a_meta_charset']); ?>" size="50" maxlength="64" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_email_reply', 'h_newslettercat_email_reply'); ?></b></td>
<td class="fillOE"><input type="text" name="nlcat_replyemail" id="nlcat_replyemail" value="<?php echo $nlcat_replyemail; ?>" size="50" maxlength="64" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_email_reply_name', 'h_newslettercat_reply_name'); ?></b></td>
<td class="fillEE"><input type="text" name="nlcat_replyname" id="nlcat_replyname" value="<?php echo htmlentities($nlcat_replyname, ENT_COMPAT, $l['a_meta_charset']); ?>" size="50" maxlength="64" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_name', 'h_newslettercat_name'); ?></b></td>
<td class="fillOE"><input type="text" name="nlcat_name" id="nlcat_name" value="<?php echo htmlentities($nlcat_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="60" maxlength="64" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b><?php echo F_display_field_name('w_description', 'h_newslettercat_description'); ?></b>
<br />
<?php 
$doc_charset = F_word_language($nlcat_language, "a_meta_charset");
F_html_button("newsletter", "form_nlcateditor", "nlcat_description", $doc_charset);

$current_ta_code = $nlcat_description;
if(!$formstatus) {
	$current_ta_code = stripslashes($current_ta_code);
}
?>
</td>
<td class="fillEE"><textarea cols="60" rows="5" name="nlcat_description" id="nlcat_description"><?php echo htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset); ?></textarea></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b><?php echo F_display_field_name('w_header', 'h_newslettercat_header'); ?></b>
<br />
<?php F_html_button("newsletter", "form_nlcateditor", "nlcat_msg_header", $doc_charset);

$current_ta_code = $nlcat_msg_header;
if(!$formstatus) {
	$current_ta_code = stripslashes($current_ta_code);
}
?>
</td>
<td class="fillOE"><textarea cols="60" rows="5" name="nlcat_msg_header" id="nlcat_msg_header"><?php echo htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset); ?></textarea></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b><?php echo F_display_field_name('w_footer', 'h_newslettercat_footer'); ?></b>
<br />
<?php F_html_button("newsletter", "form_nlcateditor", "nlcat_msg_footer", $doc_charset);

$current_ta_code = $nlcat_msg_footer;
if(!$formstatus) {
	$current_ta_code = stripslashes($current_ta_code);
}
?>
</td>
<td class="fillEE"><textarea cols="60" rows="5" name="nlcat_msg_footer" id="nlcat_msg_footer"><?php echo htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset); ?></textarea></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b><?php echo F_display_field_name('w_confirmation', 'h_newslettercat_confirm_user'); ?><br />(<?php echo $l['w_user']; ?>)</b>
<br />
<?php F_html_button("newsletter", "form_nlcateditor", "nlcat_msg_confirmation", $doc_charset);

$current_ta_code = $nlcat_msg_confirmation;
if(!$formstatus) {
	$current_ta_code = stripslashes($current_ta_code);
}
?>
</td>
<td class="fillOE"><textarea cols="60" rows="5" name="nlcat_msg_confirmation" id="nlcat_msg_confirmation"><?php echo htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset); ?></textarea></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_inform_email', 'h_newslettercat_inform_email'); ?></b></td>
<td class="fillEE"><input type="text" name="nlcat_admin_email" id="nlcat_admin_email" value="<?php echo $nlcat_admin_email; ?>" size="30" maxlength="64" />
<select name="nlcat_informfor" id="nlcat_informfor" size="0">
<?php
	echo "<option value=\"0\"";
	if(!$nlcat_informfor) {echo " selected=\"selected\"";}
	echo ">".$l['w_never']."</option>\n";
	echo "<option value=\"1\"";
	if($nlcat_informfor==1) {echo " selected=\"selected\"";}
	echo ">".$l['w_confirm']."</option>\n";
	echo "<option value=\"2\"";
	if($nlcat_informfor==2) {echo " selected=\"selected\"";}
	echo ">".$l['w_request']."</option>\n";
	echo "<option value=\"3\"";
	if($nlcat_informfor==3) {echo " selected=\"selected\"";}
	echo ">".$l['w_always']."</option>\n";
?>
</select>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b><?php echo F_display_field_name('w_confirmation', 'h_newslettercat_confirm_admin'); ?><br />(<?php echo $l['w_administrator']; ?>)</b>
<br />
<?php 
$doc_charset = $l['a_meta_charset'];
F_html_button("newsletter", "form_nlcateditor", "nlcat_msg_admin", $doc_charset);

$current_ta_code = $nlcat_msg_admin;
if(!$formstatus) {
	$current_ta_code = stripslashes($current_ta_code);
}
?>
</td>
<td class="fillOE"><textarea cols="60" rows="5" name="nlcat_msg_admin" id="nlcat_msg_admin"><?php echo htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset); ?></textarea></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_all_users', 'h_newslettercat_all_users'); ?></b></td>
<td class="fillEE">
<?php
echo "<input type=\"checkbox\" name=\"nlcat_all_users\" id=\"nlcat_all_users\" value=\"1\"";
if ($nlcat_all_users) {
	echo " checked=\"checked\"";
}
echo " />";
?>
</td>
</tr>
<?php
if (isset($nlcat_id) AND $nlcat_id) {
?>
<tr class="fillO">
<td class="fillO0" align="right">&nbsp;</td>
<td class="fillOE"><a href="cp_edit_newsletter_messages.<?php echo CP_EXT; ?>?nlmsg_nlcatid=<?php echo $nlcat_id; ?>"><b><?php echo $l['t_newsletter_messages_editor']; ?>&nbsp;&gt;&gt;</b></a></td>
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
if (isset($nlcat_id) AND $nlcat_id) {
	F_submit_button("form_nlcateditor","menu_mode",$l['w_update']); 
	F_submit_button("form_nlcateditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_nlcateditor","menu_mode",$l['w_add']); 
F_submit_button("form_nlcateditor","menu_mode",$l['w_clear']); 
?>
</td>

</tr>
</table>
</form>
<!-- ====================================================== -->

<!-- Cange focus to nlcat_id select -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_nlcateditor.nlcat_id.focus();
//]]>
</script>
<!-- END Cange focus to nlcat_id select -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
