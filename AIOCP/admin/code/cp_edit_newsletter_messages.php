<?php
//============================================================+
// File name   : cp_edit_newsletter_messages.php
// Begin       : 2001-10-15
// Last Update : 2008-07-06
// 
// Description : Edit newsletter messages
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
require_once('../code/cp_functions_newsletter_edit.'.CP_EXT);
require_once('../code/cp_functions_newsletter_send.'.CP_EXT);
require_once('../../shared/code/cp_functions_form.'.CP_EXT);

$pagelevel = K_AUTH_ADMIN_CP_EDIT_NEWSLETTER_MESSAGES;
require_once('../../shared/code/cp_authorization.'.CP_EXT);



$progress_log = "../log/cp_newsletter.log";	//log file
$thispage_title = $l['t_newsletter_messages_editor'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages; ?>
<!-- ====================================================== -->
<?php
// Initialize variables
$userlevel = $_SESSION['session_user_level'];

if (isset($_REQUEST["nlmsg_id"])) {
	$nlmsg_id = $_REQUEST["nlmsg_id"];
} else {
	$nlmsg_id = "";
}
if (isset($_REQUEST["nlmsg_nlcatid"])) {
	$nlmsg_nlcatid = $_REQUEST["nlmsg_nlcatid"];
} else {
	$nlmsg_nlcatid = "";
}
if (isset($_REQUEST["nlmsg_editorid"])) {
	$nlmsg_editorid = $_REQUEST["nlmsg_editorid"];
} else {
	$nlmsg_editorid = "";
}
if (isset($_REQUEST["nlmsg_title"])) {
	$nlmsg_title = $_REQUEST["nlmsg_title"];
} else {
	$nlmsg_title = "";
}
if (isset($_REQUEST["nlmsg_message"])) {
	$nlmsg_message = $_REQUEST["nlmsg_message"];
} else {
	$nlmsg_message = "";
}
if (isset($_REQUEST["nlmsg_composedate"])) {
	$nlmsg_composedate = $_REQUEST["nlmsg_composedate"];
} else {
	$nlmsg_composedate = "";
}
if (isset($_REQUEST["nlmsg_sentdate"])) {
	$nlmsg_sentdate = $_REQUEST["nlmsg_sentdate"];
} else {
	$nlmsg_sentdate = "";
}

if (isset($_REQUEST["menu_mode"])) {
	$menu_mode = $_REQUEST["menu_mode"];
} else {
	$menu_mode = "";
}
switch($menu_mode) {

	case unhtmlentities($l['w_delete']):
	case $l['w_delete']:{
		F_stripslashes_formfields(); // ask confirmation
		F_print_error("WARNING", $l['m_delete_confirm']);
		?>
		<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_delete" id="form_delete">
		<input type="hidden" name="menu_mode" id="menu_mode" value="forcedelete" />
		<input type="hidden" name="nlmsg_id" id="nlmsg_id" value="<?php echo $nlmsg_id; ?>" />
		<input type="hidden" name="forcedelete" id="forcedelete" value="" />
		<?php 
		F_submit_button("form_delete","forcedelete",$l['w_delete']);
		F_submit_button("form_delete","forcedelete",$l['w_cancel']);
		?>
		</form>	
		<?php
		break;
	}

	case 'forcedelete':{
		F_stripslashes_formfields(); // Delete
		if($forcedelete == $l['w_delete']) { //check if delete button has been pushed
			$sql = "DELETE FROM ".K_TABLE_NEWSLETTER_MESSAGES." WHERE nlmsg_id=".$nlmsg_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			// Delete message attachments
			$sql = "DELETE FROM ".K_TABLE_NEWSLETTER_ATTACHMENTS." WHERE nlattach_nlmsgid=".$nlmsg_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			$nlmsg_id=FALSE;
		}
		break;
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_NEWSLETTER_MESSAGES, "(nlmsg_title='".$nlmsg_title."' AND nlmsg_message='".$nlmsg_message."')", "nlmsg_id", $nlmsg_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$nlmsg_message = F_newsletter_fix_relative_src_links($nlmsg_id, $nlmsg_message); //fix relative links
				$sql = "UPDATE IGNORE ".K_TABLE_NEWSLETTER_MESSAGES." SET 
				nlmsg_nlcatid='".$nlmsg_nlcatid."', 
				nlmsg_editorid='".$nlmsg_editorid."', 
				nlmsg_title='".$nlmsg_title."', 
				nlmsg_message='".$nlmsg_message."', 
				nlmsg_composedate='".$nlmsg_composedate."', 
				nlmsg_sentdate='".$nlmsg_sentdate."' 
				WHERE nlmsg_id=".$nlmsg_id."";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_add']):
	case $l['w_add']:{ // Add
		if($formstatus = F_check_form_fields()) {
			//check if nlmsg_title/nlmsg_message combination is unique
			$sql = "SELECT nlmsg_title,nlmsg_message FROM ".K_TABLE_NEWSLETTER_MESSAGES." WHERE (nlmsg_title='".$nlmsg_title."' AND nlmsg_message='".$nlmsg_message."')";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_message']);
			}
			else { //add item
				$nlmsg_editorid = $_SESSION['session_user_id'];
				$nlmsg_composedate = time(); // get the actual date and time
				$nlmsg_sentdate = "";
				$sql = "INSERT IGNORE INTO ".K_TABLE_NEWSLETTER_MESSAGES." (
				nlmsg_nlcatid, 
				nlmsg_editorid, 
				nlmsg_title, 
				nlmsg_message, 
				nlmsg_composedate, 
				nlmsg_sentdate
				) VALUES (
				'".$nlmsg_nlcatid."', 
				'".$nlmsg_editorid."', 
				'".$nlmsg_title."', 
				'".$nlmsg_message."', 
				'".$nlmsg_composedate."', 
				'".$nlmsg_sentdate."')";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$nlmsg_id = F_aiocpdb_insert_id();
					$nlmsg_message = F_newsletter_fix_relative_src_links($nlmsg_id, $nlmsg_message); //fix relative links
					$sql = "UPDATE IGNORE ".K_TABLE_NEWSLETTER_MESSAGES." SET nlmsg_message='".$nlmsg_message."' WHERE nlmsg_id=".$nlmsg_id."";
					if(!$r = F_aiocpdb_query($sql, $db)) {
						F_display_db_error();
					}
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_send']):
	case $l['w_send']:{ // print a wait message
		F_print_error("MESSAGE", $l['m_send_wait']);
		
		//open log popup display to show process progress
		@unlink($progress_log); //clear progress log file if exist
		error_log("--- START LOG: ".gmdate("Y-m-d H:i:s")." ---\n", 3, $progress_log); //create progress log file
		echo "\n<script language=\"JavaScript\" type=\"text/javascript\">\n";
		echo "//<![CDATA[\n";
		echo "logview=window.open('cp_show_progress.".CP_EXT."?log=".$progress_log."','logview','dependent,height=280,width=400,menubar=no,resizable=yes,scrollbars=no,status=no,toolbar=no');\n";
		echo "//]]>\n";
		echo "</script>\n";
		
		break;
	}

	case "startlongprocess":{ // send newsletter
		if(F_send_newsletter($nlmsg_id)) {
			F_print_error("MESSAGE", $l['m_newsletter_sent']);
		}
		else {
			F_print_error("ERROR", $l['m_newsletter_sent_error']);
		}
		error_log("--- END LOG: ".gmdate("Y-m-d H:i:s")." ---\n", 3, $progress_log); //create progress log file
		break;
	}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$nlmsg_title = "";
		$nlmsg_message = "";
		$nlmsg_id = FALSE;
		break;
		}

	default :{ 
		break;
		}

} //end of switch

// Initialize variables
$clear_fields = false;

if(!isset($nlmsg_language)) {
	$nlmsg_language = $selected_language;
}

//select category
if((!isset($nlmsg_nlcatid) OR (!$nlmsg_nlcatid)) OR (isset($changelanguage) AND $changelanguage)) {
	$sql = "SELECT * FROM ".K_TABLE_NEWSLETTER_CATEGORIES." WHERE (nlcat_level<=".$userlevel." AND nlcat_enabled=1 AND nlcat_language='".$nlmsg_language."') ORDER BY nlcat_name LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$nlmsg_nlcatid = $m['nlcat_id'];
		}
		else {
			$nlmsg_nlcatid = false;
		}
	}
	else {
		F_display_db_error();
	}
	$nlmsg_id = false;
}

if($formstatus) {
	if($nlmsg_nlcatid) {
		if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
			if($changecategory OR (!$nlmsg_id)) {
				$sql = "SELECT * FROM ".K_TABLE_NEWSLETTER_MESSAGES." WHERE nlmsg_nlcatid='".$nlmsg_nlcatid."' ORDER BY nlmsg_composedate DESC LIMIT 1";
			}
			else {$sql = "SELECT * FROM ".K_TABLE_NEWSLETTER_MESSAGES." WHERE nlmsg_id='".$nlmsg_id."' LIMIT 1";}
			if($r = F_aiocpdb_query($sql, $db)) {
				if($m = F_aiocpdb_fetch_array($r)) {
					$nlmsg_id = $m['nlmsg_id'];
					$nlmsg_nlcatid = $m['nlmsg_nlcatid'];
					$nlmsg_editorid = $m['nlmsg_editorid'];
					$nlmsg_title = $m['nlmsg_title'];
					$nlmsg_message = $m['nlmsg_message'];
					$nlmsg_composedate = $m['nlmsg_composedate'];
					$nlmsg_sentdate = $m['nlmsg_sentdate'];
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

if($clear_fields) {
	$nlmsg_title = "";
	$nlmsg_message = "";
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_nlmsgeditor" id="form_nlmsgeditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="nlmsg_title" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_title']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT  language ==================== -->

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_language', 'h_newslettercat_language'); ?></b></td>
<td class="fillOE">
<input type="hidden" name="changelanguage" id="changelanguage" value="0" />
<select name="nlmsg_language" id="nlmsg_language" size="0" onchange="document.form_nlmsgeditor.changelanguage.value=1; document.form_nlmsgeditor.submit()">
<?php
	$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"".$m['language_code']."\"";
			if($m['language_code'] == $nlmsg_language) {
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
<select name="nlmsg_nlcatid" id="nlmsg_nlcatid" size="0" onchange="document.form_nlmsgeditor.changecategory.value=1; document.form_nlmsgeditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_NEWSLETTER_CATEGORIES." WHERE (nlcat_level<=".$userlevel." AND nlcat_enabled=1 AND nlcat_language='".$nlmsg_language."') ORDER BY nlcat_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['nlcat_id']."\"";
		if($m['nlcat_id'] == $nlmsg_nlcatid) {
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

<!-- SELECT newsletter messages ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_message', 'h_newslettered_select'); ?></b></td>
<td class="fillOE">
<select name="nlmsg_id" id="nlmsg_id" size="0" onchange="document.form_nlmsgeditor.submit()">
<?php
if($nlmsg_nlcatid) {
	$sql = "SELECT * FROM ".K_TABLE_NEWSLETTER_MESSAGES." WHERE nlmsg_nlcatid='".$nlmsg_nlcatid."' ORDER BY nlmsg_composedate DESC";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
		if($m['nlmsg_sentdate']) {$thissentdate = gmdate("Y-m-d",$m['nlmsg_sentdate']);}
		else {$thissentdate = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";}
			echo "<option value=\"".$m['nlmsg_id']."\"";
			if($m['nlmsg_id'] == $nlmsg_id) {
				echo " selected=\"selected\"";
			}
			echo ">".gmdate("Y-m-d",$m['nlmsg_composedate'])." | ".$thissentdate." | ".$m['nlmsg_title']."</option>\n";
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
<!-- END SELECT newsletter messages ==================== -->

<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE"><hr /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_title', 'h_newslettered_title'); ?></b></td>
<td class="fillOE"><input type="text" name="nlmsg_title" id="nlmsg_title" value="<?php echo htmlentities($nlmsg_title, ENT_COMPAT, $l['a_meta_charset']); ?>" size="60" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b><?php echo F_display_field_name('w_message', 'h_newslettered_message'); ?></b>
<br />
<?php 
$doc_charset = F_word_language($nlmsg_language, "a_meta_charset");
F_html_button("newsletter", "form_nlmsgeditor", "nlmsg_message", $doc_charset);

$current_ta_code = $nlmsg_message;
if(!$formstatus) {
	$current_ta_code = stripslashes($current_ta_code);
}
?>
</td>
<td class="fillEE"><textarea cols="60" rows="10" name="nlmsg_message" id="nlmsg_message" onSelect="FJ_store_caret (this);" onclick="FJ_store_caret (this);" onKeyUp="FJ_store_caret (this);"><?php echo htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset); ?></textarea></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_template', 'h_newslettered_template'); ?></b></td>
<td class="fillOE">
<select name="nltemplate" id="nltemplate" size="0">
	<option value="#CATEGORYNAME#"><?php echo $l['w_newsletter_name']; ?></option>
	<option value="#CATEGORYDESCRIPTION#"><?php echo $l['w_newsletter_description']; ?></option>
	<option value="#USERNAME#"><?php echo $l['w_username']; ?></option>
	<option value="#USERFIRSTNAME#"><?php echo $l['w_firstname']; ?></option>
	<option value="#USERLASTNAME#"><?php echo $l['w_lastname']; ?></option>
	<option value="#EMAIL#"><?php echo $l['w_user_email']; ?></option>
	<option value="#USERIP#"><?php echo $l['w_userip']; ?></option>
	<option value="#UNSUBSCRIBEURL#"><?php echo $l['w_unsubscribe_url']; ?></option>
	<option value="#SUBSCRIBEURL#"><?php echo $l['w_subscribe_url']; ?></option>
</select>
<?php F_generic_button("addtag",$l['w_add'],"FJ_insert_text (document.form_nlmsgeditor.nlmsg_message,document.form_nlmsgeditor.nltemplate.options[document.form_nlmsgeditor.nltemplate.selectedIndex].value)"); ?>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_attachments', 'h_newslettered_attachments'); ?></b></td>
<td class="fillEE">
<?php 
if($nlmsg_id) {
	$sql = "SELECT COUNT(*) FROM ".K_TABLE_NEWSLETTER_ATTACHMENTS." WHERE nlattach_nlmsgid=".$nlmsg_id." LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			echo "&nbsp;<b>".$m['0']."</b>";
		}
	}
	else {
		F_display_db_error();
	}
	echo "&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "<a href=\"cp_edit_newsletter_attachments.".CP_EXT."?nlmsg_nlcatid=".$nlmsg_nlcatid."&amp;nlattach_nlmsgid=".$nlmsg_id."\"><b>".$l['t_newsletter_attachments_editor']."&nbsp;&gt;&gt;</b></a>";
}
else {
	echo "0";
}
?>

</td>
</tr>
<?php
if (isset($nlmsg_nlcatid) AND ($nlmsg_nlcatid > 0)) {
?>
<tr class="fillO">
<td class="fillOO" align="right">&nbsp;</td>
<td class="fillOE"><a href="cp_edit_newsletter_categories.<?php echo CP_EXT; ?>?nlcat_id=<?php echo $nlmsg_nlcatid; ?>"><b>&lt;&lt;&nbsp;<?php echo $l['t_newsletter_categories_editor']; ?></b></a></td>
</tr>
<?php
}
?>
</table>

</td>
</tr>
<tr  class="edge">
<td  class="edge" colspan="2" align="center">
<input type="hidden" name="nlmsg_composedate" id="nlmsg_composedate" value="<?php echo $nlmsg_composedate; ?>" />
<input type="hidden" name="nlmsg_sentdate" id="nlmsg_sentdate" value="<?php echo $nlmsg_sentdate; ?>" />
<input type="hidden" name="nlmsg_editorid" id="nlmsg_editorid" value="<?php echo $nlmsg_editorid; ?>" />
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if ($nlmsg_id) {
F_submit_button("form_nlmsgeditor","menu_mode",$l['w_send']); 
F_submit_button("form_nlmsgeditor","menu_mode",$l['w_update']); 
F_submit_button("form_nlmsgeditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_nlmsgeditor","menu_mode",$l['w_add']); 
F_submit_button("form_nlmsgeditor","menu_mode",$l['w_clear']); 

?>
</td>
</tr>

</table>
</form>

<form action="cp_newsletter_preview.<?php echo CP_EXT; ?>" method="post" enctype="multipart/form-data" name="form_nlmsgpreview" id="form_nlmsgpreview" target="_blank">
<input type="hidden" name="nlmsg_id" id="fnp_nlmsg_id" value="<?php echo $nlmsg_id; ?>" />
<?php F_generic_submit_button("form_nlmsgpreview","menu_mode",$l['w_preview'],"document.form_nlmsgpreview.nlmsg_id.value=document.form_nlmsgeditor.nlmsg_id.value"); ?>
</form>

<script language="JavaScript" src="<?php echo K_PATH_SHARED_JSCRIPTS; ?>inserttag.js" type="text/javascript"></script>

<?php //recall newsletter page to start sending
if (($menu_mode == $l['w_send']) OR ($menu_mode == unhtmlentities($l['w_send'])) ) {
	echo "<script language=\"JavaScript\" type=\"text/javascript\">";
	echo "//<![CDATA[\n";
	echo "document.form_nlmsgeditor.menu_mode.value='startlongprocess';";
	echo "document.form_nlmsgeditor.submit();";
	echo "//]]>\n";
	echo "</script>";
}

require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
