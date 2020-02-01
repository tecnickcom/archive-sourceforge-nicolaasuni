<?php
//============================================================+
// File name   : cp_edit_news.php                              
// Begin       : 2001-09-19                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit News                                     
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


$pagelevel = K_AUTH_ADMIN_CP_EDIT_NEWS;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_news_editor'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages; ?>
<!-- ====================================================== -->
<?php
// Initialize variables
$userlevel = $_SESSION['session_user_level'];

require_once('../../shared/code/cp_functions_form.'.CP_EXT);

if (!isset($news_editorid)) {
	$news_editorid = "";
}
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
		<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_delete" id="form_delete">
		<input type="hidden" name="menu_mode" id="menu_mode" value="forcedelete" />
		<input type="hidden" name="news_id" id="news_id" value="<?php echo $news_id; ?>" />
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
			$sql = "DELETE FROM ".K_TABLE_NEWS." WHERE news_id=".$news_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			$news_id=FALSE;
		}
		break;
		}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update news
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_NEWS, "(news_date='".$news_date."' AND news_title='".$news_title."')", "news_id", $news_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$sql = "UPDATE IGNORE ".K_TABLE_NEWS." SET 
				news_category='".$news_category."', 
				news_date='".$news_date."', 
				news_title='".$news_title."', 
				news_editorid='".$news_editorid."', 
				news_author_name='".$news_author_name."', 
				news_author_email='".$news_author_email."', 
				news_source_name='".$news_source_name."', 
				news_source_link='".$news_source_link."', 
				news_text='".$news_text."' 
				WHERE news_id=".$news_id."";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_add']):
	case $l['w_add']:{ // Add news
		if($formstatus = F_check_form_fields()) {
			//check if news is unique
			$sql = "SELECT news_id FROM ".K_TABLE_NEWS." WHERE (news_date='".$news_date."' AND news_title='".$news_title."')";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_news']);
			}
			else { //add item
				$news_editorid = $_SESSION['session_user_id'];
				$news_date = gmdate("Y-m-d H:i:s"); // get the actual date and time
				$sql = "INSERT IGNORE INTO ".K_TABLE_NEWS." (
				news_category, 
				news_date, 
				news_title, 
				news_editorid, 
				news_author_name, 
				news_author_email, 
				news_source_name, 
				news_source_link, 
				news_text
				) VALUES (
				'".$news_category."', 
				'".$news_date."', 
				'".$news_title."', 
				'".$news_editorid."', 
				'".$news_author_name."', 
				'".$news_author_email."', 
				'".$news_source_name."', 
				'".$news_source_link."', 
				'".$news_text."'
				)";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$news_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$news_date = gmdate("Y-m-d H:i:s"); // get the actual date and time
		$news_title = "";
		$news_author_name = "";
		$news_author_email = "";
		$news_source_name = "";
		$news_source_link = "";
		$news_text = "";
		break;
		}

	default :{ 
		break;
		}

} //end of switch


// Initialize variables
$clear_fields = false;

if(!isset($newscat_language)) {
	$newscat_language = $selected_language;
}

if((!isset($news_category) OR (!$news_category)) OR (isset($changelanguage) AND $changelanguage)) {
	$sql = "SELECT * FROM ".K_TABLE_NEWS_CATEGORIES." WHERE (newscat_level<=".$userlevel." AND newscat_language='".$newscat_language."') ORDER BY newscat_name LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$news_category = $m['newscat_id'];
		}
		else {
			$news_category = false;
		}
	}
	else {
		F_display_db_error();
	}
	$news_id = false;
}

if($formstatus) {
	if($news_category) {
		if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
			if($changecategory OR (!$news_id)) {
				$sql = "SELECT * FROM ".K_TABLE_NEWS." WHERE news_category=".$news_category." ORDER BY news_date DESC LIMIT 1";
			}
			else {$sql = "SELECT * FROM ".K_TABLE_NEWS." WHERE news_id=".$news_id." LIMIT 1";}
			if($r = F_aiocpdb_query($sql, $db)) {
				if($m = F_aiocpdb_fetch_array($r)) {
					$news_id = $m['news_id'];
					$news_category = $m['news_category'];
					$news_date = $m['news_date'];
					$news_title = $m['news_title'];
					$news_editorid = $m['news_editorid'];
					$news_author_name = $m['news_author_name'];
					$news_author_email = $m['news_author_email'];
					$news_source_name = $m['news_source_name'];
					$news_source_link = $m['news_source_link'];
					$news_text = $m['news_text'];
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
	$news_date = gmdate("Y-m-d H:i:s"); // get the actual date and time
	$news_title = "";
	$news_author_name = "";
	$news_author_email = "";
	$news_source_name = "";
	$news_source_link = "";
	$news_text = "";
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_newseditor" id="form_newseditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="news_title" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_title']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT  language ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_language', 'h_newscat_language'); ?></b></td>
<td class="fillOE">
<input type="hidden" name="changelanguage" id="changelanguage" value="0" />
<select name="newscat_language" id="newscat_language" size="0" onchange="document.form_newseditor.changelanguage.value=1; document.form_newseditor.submit()">
<?php
	$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"".$m['language_code']."\"";
			if($m['language_code'] == $newscat_language) {
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
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_category', 'h_newscat_select'); ?></b></td>
<td class="fillEE">
<input type="hidden" name="changecategory" id="changecategory" value="0" />
<select name="news_category" id="news_category" size="0" onchange="document.form_newseditor.changecategory.value=1; document.form_newseditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_NEWS_CATEGORIES." WHERE (newscat_level<=".$userlevel." AND newscat_language='".$newscat_language."') ORDER BY newscat_name";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['newscat_id']."\"";
		if($m['newscat_id'] == $news_category) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['newscat_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
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

<!-- SELECT news ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_news', 'h_newsed_select'); ?></b></td>
<td class="fillOE">
<select name="news_id" id="news_id" size="0" onchange="document.form_newseditor.submit()">
<?php
if($news_category) {
	$sql = "SELECT * FROM ".K_TABLE_NEWS." WHERE news_category=".$news_category." ORDER BY news_date DESC";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"".$m['news_id']."\"";
			if($m['news_id'] == $news_id) {
				echo " selected=\"selected\"";
			}
			echo ">[".$m['news_id']."] &lt;".$m['news_date']."&gt; ".htmlentities($m['news_title'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
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
<!-- END SELECT news ==================== -->

<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE"><hr /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_author', 'h_newsed_author'); ?></b></td>
<td class="fillOE"><input type="text" name="news_author_name" id="news_author_name" value="<?php echo htmlentities($news_author_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="50" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_email', 'h_newsed_email'); ?></b></td>
<td class="fillEE"><input type="text" name="news_author_email" id="news_author_email" value="<?php echo $news_author_email; ?>" size="50" maxlength="255" /> (<?php echo $l['w_author']; ?>)</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_source', 'h_newsed_source'); ?></b></td>
<td class="fillOE"><input type="text" name="news_source_name" id="news_source_name" value="<?php echo htmlentities($news_source_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="50" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_link', 'h_newsed_link'); ?></b></td>
<td class="fillEE"><input type="text" name="news_source_link" id="news_source_link" value="<?php echo $news_source_link; ?>" size="50" maxlength="255" /> (<?php echo $l['w_source']; ?>)</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_title', 'h_newsed_title'); ?></b></td>
<td class="fillOE"><input type="text" name="news_title" id="news_title" value="<?php echo htmlentities($news_title, ENT_COMPAT, $l['a_meta_charset']); ?>" size="50" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b><?php echo F_display_field_name('w_text', 'h_newsed_text'); ?></b>
<br />
<?php 
$doc_charset = F_word_language($newscat_language, "a_meta_charset");
F_html_button("page", "form_newseditor", "news_text", $doc_charset);

$current_ta_code = $news_text;
if(!$formstatus) {
	$current_ta_code = stripslashes($current_ta_code);
}
?>
</td>
<td class="fillEE">
<textarea cols="60" rows="10" name="news_text" id="news_text"><?php echo htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset); ?></textarea>
</td>
</tr>

<?php
if (isset($news_category) AND ($news_category > 0)) {
?>
<tr class="fillO">
<td class="fillOO" align="right">&nbsp;</td>
<td class="fillOE"><a href="cp_edit_newscat.<?php echo CP_EXT; ?>?newscat_id=<?php echo $news_category; ?>&amp;newscat_language=<?php echo urlencode($newscat_language); ?>"><b>&lt;&lt;&nbsp;<?php echo $l['t_news_categories_editor']; ?></b></a></td>
</tr>
<?php
}
?>
</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="news_date" id="news_date" value="<?php echo $news_date; ?>" />
<input type="hidden" name="news_editorid" id="news_editorid" value="<?php echo $news_editorid; ?>" />
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if ($news_id) {
	F_submit_button("form_newseditor","menu_mode",$l['w_update']); 
	F_submit_button("form_newseditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_newseditor","menu_mode",$l['w_add']); 
F_submit_button("form_newseditor","menu_mode",$l['w_clear']); 
?>
</td>

</tr>
</table>
</form>
<!-- ====================================================== -->

<!-- Cange focus to news_id select -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_newseditor.news_id.focus();
//]]>
</script>
<!-- END Cange focus to news_id select -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
