<?php
//============================================================+
// File name   : cp_edit_forums.php                            
// Begin       : 2001-12-18                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit forums                                   
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_FORUMS;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_forums_editor'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages; ?>
<!-- ====================================================== -->
<?php
// Initialize variables
$userlevel = $_SESSION['session_user_level'];
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
		<p><?php echo $l['t_warning'].": ".$l['d_forum_delete']; ?></p>
		
		<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_delete" id="form_delete">
		<input type="hidden" name="menu_mode" id="menu_mode" value="forcedelete" />
		<input type="hidden" name="forum_id" id="forum_id" value="<?php echo $forum_id; ?>" />
		<input type="hidden" name="forum_order" id="forum_order" value="<?php echo $forum_order; ?>" />
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
			$sql = "DELETE FROM ".K_TABLE_FORUM_FORUMS." WHERE forum_id='".$forum_id."'";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			$sql = "DELETE FROM ".K_TABLE_FORUM_TOPICS." WHERE forumtopic_forumid='".$forum_id."'";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			$sql = "DELETE FROM ".K_TABLE_FORUM_POSTS." WHERE forumposts_forumid='".$forum_id."'";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			//delete moderators
			$sql = "DELETE FROM ".K_TABLE_FORUM_MODERATORS." WHERE moderator_forumid='".$forum_id."'";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			//reorder positions here...
			$sql = "UPDATE IGNORE ".K_TABLE_FORUM_FORUMS." SET forum_order=forum_order-1 WHERE forum_order>$forum_order";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
		}
		$forum_id=FALSE;
		break;
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_FORUM_FORUMS, "(forum_name='".$forum_name."' AND forum_categoryid='".$forum_categoryid."')", "forum_id", $forum_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$sql = "UPDATE IGNORE ".K_TABLE_FORUM_FORUMS." SET 
				forum_language='".$forumcat_language."', 
				forum_categoryid='".$forum_categoryid."', 
				forum_readinglevel='".$forum_readinglevel."', 
				forum_postinglevel='".$forum_postinglevel."', 
				forum_name='".$forum_name."', 
				forum_description='".$forum_description."', 
				forum_status='".$forum_status."',  
				forum_order='".$forum_order."', 
				forum_edittimelimit='".$forum_edittimelimit."', 
				forum_lockthread='".$forum_lockthread."', 
				forum_removezeroreply='".$forum_removezeroreply."', 
				forum_userconfirmation='".$forum_userconfirmation."', 
				forum_topics='".$forum_topics."', 
				forum_posts='".$forum_posts."', 
				forum_lasttopic='".$forum_lasttopic."' 
				WHERE forum_id='".$forum_id."'";
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
			//check if forum_name is unique
			$sql = "SELECT forum_name FROM ".K_TABLE_FORUM_FORUMS." WHERE (forum_name='".$forum_name."' AND forum_categoryid='".$forum_categoryid."')";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				$forum_topics=0;
				$forum_posts=0;
				$forum_lasttopic=0;
				//count number of forums for each category
				$sql = "SELECT COUNT(*) FROM ".K_TABLE_FORUM_FORUMS." WHERE forum_categoryid='".$forum_categoryid."' LIMIT 1";
				if($r = F_aiocpdb_query($sql, $db)) {
					if($m = F_aiocpdb_fetch_array($r)) {
						$forum_order = $m['0']+1;
					} 
				} 
				else {
					F_display_db_error();
				}
				$sql = "INSERT IGNORE INTO ".K_TABLE_FORUM_FORUMS." (
				forum_language, 
				forum_categoryid, 
				forum_readinglevel, 
				forum_postinglevel, 
				forum_name, 
				forum_description, 
				forum_status, 
				forum_order, 
				forum_edittimelimit, 
				forum_lockthread, 
				forum_removezeroreply, 
				forum_userconfirmation, 
				forum_topics, 
				forum_posts, 
				forum_lasttopic
				) VALUES (
				'".$forumcat_language."', 
				'".$forum_categoryid."', 
				'".$forum_readinglevel."', 
				'".$forum_postinglevel."', 
				'".$forum_name."', 
				'".$forum_description."', 
				'".$forum_status."', 
				'".$forum_order."', 
				'".$forum_edittimelimit."', 
				'".$forum_lockthread."', 
				'".$forum_removezeroreply."', 
				'".$forum_userconfirmation."', 
				'".$forum_topics."', 
				'".$forum_posts."', 
				'".$forum_lasttopic."')";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$forum_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
		}

	case '-':{ // change order: move up
		if($forum_order>1) {
			$sql = "UPDATE IGNORE ".K_TABLE_FORUM_FORUMS." SET forum_order=(forum_order+1) WHERE forum_order=($forum_order-1)";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			$sql = "UPDATE IGNORE ".K_TABLE_FORUM_FORUMS." SET forum_order=(forum_order-1) WHERE forum_id='".$forum_id."'";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
		}
		break;
		}

	case '+':{ // change order: move down
		if($forum_order<F_count_rows(K_TABLE_FORUM_FORUMS)) {
			$sql = "UPDATE IGNORE ".K_TABLE_FORUM_FORUMS." SET forum_order=(forum_order-1) WHERE forum_order=($forum_order+1)";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			$sql = "UPDATE IGNORE ".K_TABLE_FORUM_FORUMS." SET forum_order=(forum_order+1) WHERE forum_id='".$forum_id."'";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
		}
		break;
		}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$forum_language = $selected_language;
		$forum_categoryid = "";
		$forum_readinglevel = "";
		$forum_postinglevel = "";
		$forum_name = "";
		$forum_description = "";
		$forum_status = "";
		$forum_edittimelimit = 1; //max time in hours when a user may modify his message
		$forum_lockthread = 20; //number of messages befor lock thread
		$forum_removezeroreply = 0;
		$forum_userconfirmation = K_DEAFULT_EMAIL_MSG;
		break;
		}

	default :{ 
		break;
		}

} //end of switch

// Initialize variables
$clear_fields = false;

if (!isset($forumcat_language)) {
	$forumcat_language = $selected_language;
}

//select category
if((!isset($forum_categoryid) OR (!$forum_categoryid)) OR (isset( $changelanguage) AND $changelanguage)) {
	$sql = "SELECT * FROM ".K_TABLE_FORUM_CATEGORIES." WHERE (forumcat_postinglevel<='".$userlevel."' AND forumcat_language='".$forumcat_language."') ORDER BY forumcat_order LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$forum_categoryid = $m['forumcat_id'];
		}
		else {
			$forum_categoryid = FALSE;
		}
	}
	else {
		F_display_db_error();
	}
	$forum_id = FALSE;
}

if($formstatus) {
	if($forum_categoryid) {
		if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
			if($changecategory OR (!$forum_id)) {
				$sql = "SELECT * FROM ".K_TABLE_FORUM_FORUMS." WHERE forum_categoryid='".$forum_categoryid."' ORDER BY forum_order LIMIT 1";
			}
			else {$sql = "SELECT * FROM ".K_TABLE_FORUM_FORUMS." WHERE forum_id='".$forum_id."' LIMIT 1";}
			if($r = F_aiocpdb_query($sql, $db)) {
				if($m = F_aiocpdb_fetch_array($r)) {
					$forum_id = $m['forum_id'];
					$forum_language = $m['forum_language'];
					$forum_categoryid = $m['forum_categoryid'];
					$forum_readinglevel = $m['forum_readinglevel'];
					$forum_postinglevel = $m['forum_postinglevel'];
					$forum_name = $m['forum_name'];
					$forum_description = $m['forum_description'];
					$forum_status = $m['forum_status'];
					$forum_order = $m['forum_order'];
					$forum_edittimelimit = $m['forum_edittimelimit'];
					$forum_lockthread = $m['forum_lockthread'];
					$forum_removezeroreply = $m['forum_removezeroreply'];
					$forum_topics = $m['forum_topics'];
					$forum_posts = $m['forum_posts'];
					$forum_lasttopic = $m['forum_lasttopic'];
					$forum_userconfirmation = $m['forum_userconfirmation'];
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
	$forum_language = $forumcat_language;
	$forum_readinglevel = "";
	$forum_postinglevel = "";
	$forum_name = "";
	$forum_description = "";
	$forum_status = "";
	$forum_edittimelimit = 1;
	$forum_lockthread = 20;
	$forum_removezeroreply = 0;
	$forum_userconfirmation = K_DEAFULT_EMAIL_MSG;
}

if (!isset($forum_order)) {
	$forum_order = "";
}
if (!isset($forum_topics)) {
	$forum_topics = "";
}
if (!isset($forum_posts)) {
	$forum_posts = "";
}
if (!isset($forum_lasttopic)) {
	$forum_lasttopic = "";
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_forumseditor" id="form_forumseditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="forum_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT  language ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_language', 'h_forumcat_language'); ?></b></td>
<td class="fillOE">
<input type="hidden" name="changelanguage" id="changelanguage" value="0" />
<select name="forumcat_language" id="forumcat_language" size="0" onchange="document.form_forumseditor.changelanguage.value=1; document.form_forumseditor.submit()">
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
<select name="forum_categoryid" id="forum_categoryid" size="0" onchange="document.form_forumseditor.changecategory.value=1; document.form_forumseditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_FORUM_CATEGORIES." WHERE (forumcat_postinglevel<='".$userlevel."' AND forumcat_language='".$forumcat_language."') ORDER BY forumcat_order";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['forumcat_id']."\"";
		if($m['forumcat_id'] == $forum_categoryid) {
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

<!-- SELECT forums ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_forum', 'h_forumed_select'); ?></b></td>
<td class="fillOE">
<select name="forum_id" id="forum_id" size="0" onchange="document.form_forumseditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_FORUM_FORUMS." WHERE forum_categoryid='".$forum_categoryid."' ORDER BY forum_order";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['forum_id']."\"";
		if($m['forum_id'] == $forum_id) {
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
<!-- END SELECT forums ==================== -->

<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE"><hr /></td>
</tr>


<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_name', 'h_forumed_name'); ?></b></td>
<td class="fillOE"><input type="text" name="forum_name" id="forum_name" value="<?php echo htmlentities($forum_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="50" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b><?php echo F_display_field_name('w_description', 'h_forumed_description'); ?></b>
<br />
<?php 
$doc_charset = F_word_language($forumcat_language, "a_meta_charset");
F_html_button("forum", "form_forumseditor", "forum_description", $doc_charset);

$current_ta_code = $forum_description;
if(!$formstatus) {
	$current_ta_code = stripslashes($current_ta_code);
}
?>
</td>

<td class="fillEE"><textarea cols="50" rows="5" name="forum_description" id="forum_description"><?php echo htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset); ?></textarea></td>
</tr>

<!-- SELECT LEVEL ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_reading', 'h_forumed_reading'); ?></b></td>
<td class="fillOE"><select name="forum_readinglevel" id="forum_readinglevel" size="0">
<?php
$sql = "SELECT * FROM ".K_TABLE_LEVELS." ORDER BY level_code ";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['level_code']."\"";
		if($m['level_code'] == $forum_readinglevel) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['level_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select> <b><?php echo $l['w_level']; ?></b>
</td>
</tr>
<!-- END SELECT LEVEL ==================== -->

<!-- SELECT LEVEL ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_posting', 'h_forumed_posting'); ?></b></td>
<td class="fillEE"><select name="forum_postinglevel" id="forum_postinglevel" size="0">
<?php
$sql = "SELECT * FROM ".K_TABLE_LEVELS." ORDER BY level_code ";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['level_code']."\"";
		if($m['level_code'] == $forum_postinglevel) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['level_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select> <b><?php echo $l['w_level']; ?></b>
</td>
</tr>
<!-- END SELECT LEVEL ==================== -->

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_edit_time_limit', 'h_forumed_etl'); ?></b></td>
<td class="fillOE"><input type="text" name="forum_edittimelimit" id="forum_edittimelimit" value="<?php echo $forum_edittimelimit; ?>" size="4" maxlength="4" /> <b>[<?php echo $l['w_hours']; ?>]</b></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_lock_thread', 'h_forumed_lock_thread'); ?></b></td>
<td class="fillEE"><input type="text" name="forum_lockthread" id="forum_lockthread" value="<?php echo $forum_lockthread; ?>" size="4" maxlength="4" /> <b>[<?php echo $l['w_messages']; ?>]</b></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_remove_zero_reply', 'h_forumed_zero_reply'); ?></b></td>
<td class="fillOE"><input type="text" name="forum_removezeroreply" id="forum_removezeroreply" value="<?php echo $forum_removezeroreply; ?>" size="4" maxlength="4" /> <b>[<?php echo $l['w_days']; ?>]</b></td>
</tr>

<tr class="fillE" valign="middle">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_status', 'h_forumed_status'); ?></b></td>
<td class="fillEE">
<select name="forum_status" id="forum_status" size="0">
<?php
echo "<option value=\"0\"";
if($forum_status==0) {echo " selected=\"selected\"";}
echo ">".$l['w_enabled']."</option>\n";
echo "<option value=\"1\"";
if($forum_status==1) {echo " selected=\"selected\"";}
echo ">".$l['w_locked']."</option>\n";
echo "<option value=\"2\"";
if($forum_status==2) {echo " selected=\"selected\"";}
echo ">".$l['w_hidden']."</option>\n";
?>
</select>
</td>
</tr>

<tr class="fillO" valign="middle">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_position', 'h_forumed_position'); ?></b></td>
<td class="fillOE">
<?php F_submit_button("form_forumseditor","menu_mode","-");?> <b><?php echo $forum_order; ?></b> <?php F_submit_button("form_forumseditor","menu_mode","+");?>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b><?php echo F_display_field_name('d_email_message', 'h_forumed_email_msg'); ?><br />(<?php echo $l['w_user']; ?>)</b>
<br />
<?php 
$doc_charset = $l['a_meta_charset'];
F_html_button("forum", "form_forumseditor", "forum_userconfirmation", $doc_charset);

$current_ta_code = $forum_userconfirmation;
if(!$formstatus) {
	$current_ta_code = stripslashes($current_ta_code);
}
?>
</td>
<td class="fillEE"><textarea cols="50" rows="5" name="forum_userconfirmation" id="forum_userconfirmation"><?php echo htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset); ?></textarea></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right">&nbsp;</td>
<td class="fillOE"><a href="cp_edit_forum_categories.<?php echo CP_EXT; ?>?forum_id=<?php echo $forum_categoryid; ?>&amp;forum_language=<?php echo urlencode($forum_language); ?>"><b>&lt;&lt;&nbsp;<?php echo $l['t_forum_categories_editor']; ?></b></a></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right">&nbsp;</td>
<td class="fillEE"><a href="cp_edit_forum_moderators.<?php echo CP_EXT; ?>?moderator_categoryid=<?php echo $forum_categoryid; ?>&amp;forumcat_language=<?php echo urlencode($forumcat_language); ?>&amp;moderator_forumid=<?php echo $forum_id; ?>"><b><?php echo $l['t_forums_moderators_editor']; ?>&nbsp;&gt;&gt;</b></a></td>
</tr>

</table>

</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="forum_order" id="forum_order" value="<?php echo $forum_order; ?>" />
<input type="hidden" name="forum_topics" id="forum_topics" value="<?php echo $forum_topics; ?>" />
<input type="hidden" name="forum_posts" id="forum_posts" value="<?php echo $forum_posts; ?>" />
<input type="hidden" name="forum_lasttopic" id="forum_lasttopic" value="<?php echo $forum_lasttopic; ?>" />
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if ($forum_id) {
	F_submit_button("form_forumseditor","menu_mode",$l['w_update']); 
	F_submit_button("form_forumseditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_forumseditor","menu_mode",$l['w_add']); 
F_submit_button("form_forumseditor","menu_mode",$l['w_clear']); 
?>
</td>

</tr>
</table>
</form>
<!-- ====================================================== -->

<!-- Cange focus to forum_id select -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_forumseditor.forum_id.focus();
//]]>
</script>
<!-- END Cange focus to forum_id select -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
