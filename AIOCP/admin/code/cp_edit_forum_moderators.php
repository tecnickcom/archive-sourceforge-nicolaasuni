<?php
//============================================================+
// File name   : cp_edit_forum_moderators.php                  
// Begin       : 2001-12-31                                    
// Last Update : 2008-07-07
//                                                             
// Description : Edit forums moderators                        
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
require_once('../../shared/code/cp_functions_user.'.CP_EXT);

$pagelevel = K_AUTH_ADMIN_CP_EDIT_FORUM_MODERATORS;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_forums_moderators_editor'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages; ?>
<!-- ====================================================== -->
<?php
// Initialize variables
$userlevel = $_SESSION['session_user_level'];

require_once('../../shared/code/cp_functions_form.'.CP_EXT);

if (!isset($moderator_userid)) {
	$moderator_userid = 0;
}

if (isset($_REQUEST['menu_mode'])) {
	$menu_mode = $_REQUEST['menu_mode'];
} else {
	$menu_mode = "";
}
switch($menu_mode) {

	case unhtmlentities($l['w_delete']):
	case $l['w_delete']:{
		F_stripslashes_formfields();
		$sql = "DELETE FROM ".K_TABLE_FORUM_MODERATORS." WHERE moderator_id='".$moderator_id."'";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		$moderator_id=FALSE;
		break;
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_FORUM_MODERATORS, "(moderator_userid='".$moderator_userid."' AND moderator_forumid='".$moderator_forumid."')", "moderator_id", $moderator_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$moderator_options = addslashes(serialize($mod_options));
				$sql = "UPDATE IGNORE ".K_TABLE_FORUM_MODERATORS." SET 
				moderator_forumid='".$moderator_forumid."', 
				moderator_categoryid='".$moderator_categoryid."', 
				moderator_userid='".$moderator_userid."', 
				moderator_email='".$moderator_email."', 
				moderator_confirmation='".$moderator_confirmation."', 
				moderator_options='".$moderator_options."' 
				WHERE moderator_id='".$moderator_id."'";
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
			//check if moderator_name is unique
			$sql = "SELECT moderator_userid FROM ".K_TABLE_FORUM_MODERATORS." WHERE (moderator_userid='".$moderator_userid."' AND moderator_forumid='".$moderator_forumid."')";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				$moderator_options = addslashes(serialize($mod_options));
				$sql = "INSERT IGNORE INTO ".K_TABLE_FORUM_MODERATORS." (
				moderator_forumid, 
				moderator_categoryid, 
				moderator_userid, 
				moderator_email, 
				moderator_confirmation, 
				moderator_options
				) VALUES (
				'".$moderator_forumid."', 
				'".$moderator_categoryid."', 
				'".$moderator_userid."', 
				'".$moderator_email."', 
				'".$moderator_confirmation."', 
				'".$moderator_options."')";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$moderator_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$moderator_forumid = "";
		$moderator_categoryid = "";
		$moderator_email = "";
		$moderator_confirmation = K_DEAFULT_EMAIL_MSG;
		$mod_options = array();
		break;
		}

	default :{ 
		break;
		}

} //end of switch

// Initialize variables
$clear_fields = false;

if(!isset($forumcat_language)) {
	$forumcat_language = $selected_language;
}

//select category
if((!isset($moderator_categoryid) OR (!$moderator_categoryid)) OR (isset($changelanguage) AND $changelanguage)) {
	$sql = "SELECT * FROM ".K_TABLE_FORUM_CATEGORIES." WHERE (forumcat_postinglevel<='".$userlevel."' AND forumcat_language='".$forumcat_language."') ORDER BY forumcat_order LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$moderator_categoryid = $m['forumcat_id'];
		}
		else {
			$moderator_categoryid = FALSE;
		}
	}
	else {
		F_display_db_error();
	}
	$moderator_forumid = FALSE;
	$moderator_id = FALSE;
}

//select forum
if ((!isset($moderator_forumid) OR (!$moderator_forumid)) OR (isset($changecategory) AND $changecategory)) {
	$sql = "SELECT * FROM ".K_TABLE_FORUM_FORUMS." WHERE (forum_postinglevel<='".$userlevel."'  AND forum_categoryid='".$moderator_categoryid."') ORDER BY forum_order LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$moderator_forumid = $m['forum_id'];
		}
		else {
			$moderator_forumid = FALSE;
		}
	}
	else {
		F_display_db_error();
	}
	$moderator_id = FALSE;
}

if($formstatus) {
	if($moderator_categoryid AND $moderator_forumid) {
		if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
			if($changelanguage OR$changecategory OR $changeforum OR (!$moderator_id)) {
				$sql = "SELECT * FROM ".K_TABLE_FORUM_MODERATORS." WHERE (moderator_forumid='".$moderator_forumid."') LIMIT 1";
			}
			else {$sql = "SELECT * FROM ".K_TABLE_FORUM_MODERATORS." WHERE moderator_id='".$moderator_id."' LIMIT 1";}
			if($r = F_aiocpdb_query($sql, $db)) {
				if($m = F_aiocpdb_fetch_array($r)) {
					$moderator_id = $m['moderator_id'];
					$moderator_forumid = $m['moderator_forumid'];
					$moderator_categoryid = $m['moderator_categoryid'];
					$moderator_userid = $m['moderator_userid'];
					$moderator_email = $m['moderator_email'];
					$moderator_confirmation = $m['moderator_confirmation'];
					$moderator_options = $m['moderator_options'];
					$mod_options = unserialize($moderator_options);
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
	$moderator_email = "";
	$moderator_confirmation = K_DEAFULT_EMAIL_MSG;
	$mod_options = array();
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_fmoderatorseditor" id="form_fmoderatorseditor">

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT  language ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_language', 'h_forumcat_language'); ?></b></td>
<td class="fillOE">
<input type="hidden" name="changelanguage" id="changelanguage" value="0" />
<select name="forumcat_language" id="forumcat_language" size="0" onchange="document.form_fmoderatorseditor.changelanguage.value=1; document.form_fmoderatorseditor.submit()">
<?php
	$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"".$m['language_code']."\"";
			if($m['language_code'] == $forumcat_language) {
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
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_category', 'h_forumcat_select'); ?></b></td>
<td class="fillEE">
<input type="hidden" name="changecategory" id="changecategory" value="0" />
<select name="moderator_categoryid" id="moderator_categoryid" size="0" onchange="document.form_fmoderatorseditor.changecategory.value=1; document.form_fmoderatorseditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_FORUM_CATEGORIES." WHERE (forumcat_postinglevel<='".$userlevel."' AND forumcat_language='".$forumcat_language."') ORDER BY forumcat_order";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['forumcat_id']."\"";
		if($m['forumcat_id'] == $moderator_categoryid) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['forumcat_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
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

<!-- SELECT forum ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_forum', 'h_forumed_select'); ?></b></td>
<td class="fillOE">
<input type="hidden" name="changeforum" id="changeforum" value="0" />
<select name="moderator_forumid" id="moderator_forumid" size="0" onchange="document.form_fmoderatorseditor.changeforum.value=1; document.form_fmoderatorseditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_FORUM_FORUMS." WHERE (forum_postinglevel<='".$userlevel."' AND forum_categoryid='".$moderator_categoryid."') ORDER BY forum_order";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['forum_id']."\"";
		if($m['forum_id'] == $moderator_forumid) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['forum_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT forum ==================== -->

<!-- SELECT moderator ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_moderator', 'h_forummod_select'); ?></b></td>
<td class="fillEE">
<select name="moderator_id" id="moderator_id" size="0" onchange="document.form_fmoderatorseditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_FORUM_MODERATORS." WHERE moderator_forumid='".$moderator_forumid."'";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		$thisuser = F_get_user_data($m['moderator_userid']);
		echo "<option value=\"".$m['moderator_id']."\"";
		if($m['moderator_id'] == $moderator_id) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($thisuser->name, ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT moderator ==================== -->

<tr class="fillO">
<td class="fillOO">&nbsp;</td>
<td class="fillOE"><hr /></td>
</tr>

<!-- SELECT user ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_user', 'h_forummod_user'); ?></b></td>
<td class="fillEE" colspan="2">
<select name="moderator_userid" id="moderator_userid" size="0">
<?php
$sql = "SELECT * FROM ".K_TABLE_USERS." ORDER BY user_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		if($m['user_level'] >= K_AUTH_MIN_MODERATOR_LEVEL) { //show only authorized users
			echo "<option value=\"".$m['user_id']."\"";
			if($m['user_id'] == $moderator_userid) {
				echo " selected=\"selected\"";
			}
			echo ">".htmlentities($m['user_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
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
<!-- END SELECT user ==================== -->

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_inform_email', 'h_forummod_email'); ?></b></td>
<td class="fillOE"><input type="text" name="moderator_email" id="moderator_email" value="<?php echo $moderator_email; ?>" size="20" maxlength="255" />
<select name="mod_options[0]" id="mod_options_0" size="0">
<?php
	echo "<option value=\"0\"";
	if(isset($mod_options[0]) AND !stripslashes($mod_options[0])) {
		echo " selected=\"selected\"";
	}
	echo ">".$l['w_never']."</option>\n";
	echo "<option value=\"1\"";
	if(isset($mod_options[0]) AND stripslashes($mod_options[0])==1) {
		echo " selected=\"selected\"";
	}
	echo ">".$l['w_new_thread']."</option>\n";
	echo "<option value=\"2\"";
	if(isset($mod_options[0]) AND stripslashes($mod_options[0])==2) {
		echo " selected=\"selected\"";
	}
	echo ">".$l['w_new_post']."</option>\n";
?>
</select>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b><?php echo F_display_field_name('d_email_message', 'h_forummod_email_msg'); ?></b>
<br />
<?php 
$doc_charset = $l['a_meta_charset'];
F_html_button("forum", "form_fmoderatorseditor", "moderator_confirmation", $doc_charset);

$current_ta_code = $moderator_confirmation;
if(!$formstatus) {
	$current_ta_code = stripslashes($current_ta_code);
}
?>
</td>
<td class="fillEE"><textarea cols="40" rows="5" name="moderator_confirmation" id="moderator_confirmation"><?php echo htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset); ?></textarea></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"></td>
<td class="fillOE"><br /><b><?php echo F_display_field_name('w_thread_options', 'h_forummod_thread_opt'); ?></b></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_edit', ''); ?></b></td>
<td class="fillEE"><?php F_print_moderator_option (1); ?></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_delete', ''); ?></b></td>
<td class="fillOE"><?php F_print_moderator_option (2); ?></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_move', ''); ?></b></td>
<td class="fillEE"><?php F_print_moderator_option (3); ?></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_status', ''); ?></b></td>
<td class="fillOE"><?php F_print_moderator_option (4); ?></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"></td>
<td class="fillEE"><br /><b><?php echo F_display_field_name('w_post_options', 'h_forummod_post_opt'); ?></b></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_edit', ''); ?></b></td>
<td class="fillOE"><?php F_print_moderator_option (5); ?></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_delete', ''); ?></b></td>
<td class="fillEE"><?php F_print_moderator_option (6); ?></td>
</tr>


<tr class="fillO">
<td class="fillOO" align="right">&nbsp;</td>
<td class="fillOE"><a href="cp_edit_forums.<?php echo CP_EXT; ?>?forum_categoryid=<?php echo $moderator_categoryid; ?>&amp;forumcat_language=<?php echo urlencode($forumcat_language); ?>&amp;forum_id=<?php echo $moderator_forumid; ?>"><b>&lt;&lt;&nbsp;<?php echo $l['t_forums_editor']; ?></b></a></td>
</tr>

</table>

</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if ($moderator_id) {
	F_submit_button("form_fmoderatorseditor","menu_mode",$l['w_update']); 
	F_submit_button("form_fmoderatorseditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_fmoderatorseditor","menu_mode",$l['w_add']); 
F_submit_button("form_fmoderatorseditor","menu_mode",$l['w_clear']); 
?>
</td>

</tr>
</table>
</form>
<!-- ====================================================== -->

<!-- Cange focus to moderator_id select -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_fmoderatorseditor.moderator_id.focus();
//]]>
</script>
<!-- END Cange focus to moderator_id select -->
<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//Print Yes/No Option selection
function F_print_moderator_option ($optnumber) {
	global $l, $mod_options;
	echo "<input type=\"radio\" name=\"mod_options[".$optnumber."]\" value=\"1\"";
	if(isset($mod_options[$optnumber]) AND stripslashes($mod_options[$optnumber])) {
		echo " checked=\"checked\"";
	}
	echo " />".$l['w_yes']."&nbsp;";
	echo "<input type=\"radio\" name=\"mod_options[".$optnumber."]\" value=\"0\"";
	if(!isset($mod_options[$optnumber]) OR !stripslashes($mod_options[$optnumber])) {
		echo " checked=\"checked\"";
	}
	echo " />".$l['w_no'];
}
?>

<?php
//============================================================+
// END OF FILE                                                 
//============================================================+
?>