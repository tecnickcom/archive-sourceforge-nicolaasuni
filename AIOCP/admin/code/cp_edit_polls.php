<?php
//============================================================+
// File name   : cp_edit_polls.php                             
// Begin       : 2001-10-10                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit polls (K_TABLE_POLLS table)              
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_POLLS;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_polls'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages;

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
		<input type="hidden" name="poll_id" id="poll_id" value="<?php echo $poll_id; ?>" />
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
			$sql = "DELETE FROM ".K_TABLE_POLLS." WHERE poll_id=".$poll_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			// delete also options:
			$sql = "DELETE FROM ".K_TABLE_POLLS_OPTIONS." WHERE polloption_pollid=".$poll_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			$poll_id=FALSE;
		}
		break;
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_POLLS, "(poll_language='".$poll_language."' AND poll_name='".$poll_name."')", "poll_id", $poll_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				if($poll_date_start) {$poll_date_start = strtotime($poll_date_start);} // convert into a UNIX timestamp
				if($poll_date_end) {$poll_date_end = strtotime($poll_date_end);}
				$sql = "UPDATE IGNORE ".K_TABLE_POLLS." SET 
				poll_language='".$poll_language."', 
				poll_name='".$poll_name."', 
				poll_description='".$poll_description."', 
				poll_date_start='".$poll_date_start."',  
				poll_date_end='".$poll_date_end."', 
				poll_level='".$poll_level."' 
				WHERE poll_id=".$poll_id."";
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
			if($poll_date_start) {$poll_date_start = strtotime($poll_date_start);} // convert into a UNIX timestamp
			if($poll_date_end) {$poll_date_end = strtotime($poll_date_end);}
			//check if poll_name is unique for selected language
			$sql = "SELECT poll_name FROM ".K_TABLE_POLLS." WHERE (poll_language='".$poll_language."' AND poll_name='".$poll_name."')";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add
				$sql = "INSERT IGNORE INTO ".K_TABLE_POLLS." (
				poll_language, 
				poll_name, 
				poll_description, 
				poll_date_start, 
				poll_date_end, 
				poll_level
				) VALUES (
				'".$poll_language."', 
				'".$poll_name."', 
				'".$poll_description."', 
				'".$poll_date_start."', 
				'".$poll_date_end."', 
				'".$poll_level."')";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$poll_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$poll_name = "";
		$poll_description = "";
		$poll_date_start = gmdate("Y-m-d H:i:s");
		$poll_date_end = "";
		$poll_level = 0;
		break;
		}

	default :{ 
		break;
		}

} //end of switch

// Initialize variables
if(!isset($poll_language)) {
	$poll_language = $selected_language;
}

if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if((!isset($poll_id) OR (!$poll_id)) OR (isset($changelanguage) AND $changelanguage)) {
			$sql = "SELECT * FROM ".K_TABLE_POLLS." WHERE poll_language='".$poll_language."' ORDER BY poll_name LIMIT 1";
		}
		else {
			$sql = "SELECT * FROM ".K_TABLE_POLLS." WHERE poll_id=".$poll_id." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$poll_id = $m['poll_id'];
				$poll_language = $m['poll_language'];
				$poll_name = $m['poll_name'];
				$poll_description = $m['poll_description'];
				$poll_date_start = date("Y-m-d H:i:s",$m['poll_date_start']);
				if($m['poll_date_end']){$poll_date_end = date("Y-m-d H:i:s",$m['poll_date_end']);}
				$poll_level = $m['poll_level'];
			}
			else {
				$poll_name = "";
				$poll_description = "";
				$poll_date_start = gmdate("Y-m-d H:i:s");
				$poll_date_end = "";
				$poll_level = 0;
			}
		}
		else {
			F_display_db_error();
		}
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_polleditor" id="form_polleditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="poll_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT  language ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_language', 'h_pollvote_language'); ?></b></td>
<td class="fillOE">
<input type="hidden" name="changelanguage" id="changelanguage" value="1" />
<select name="poll_language" id="poll_language" size="0" onchange="document.form_polleditor.changelanguage.value=1; document.form_polleditor.submit()">
<?php
	$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"".$m['language_code']."\"";
			if($m['language_code'] == $poll_language) {
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

<!-- SELECT ROOM ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_poll', 'h_pollvote_poll'); ?></b></td>
<td class="fillEE">
<select name="poll_id" id="poll_id" size="0" onchange="document.form_polleditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_POLLS." WHERE poll_language='".$poll_language."' ORDER BY poll_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['poll_id']."\"";
		if($m['poll_id'] == $poll_id) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['poll_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT ROOM ==================== -->

<tr class="fillO">
<td class="fillOO">&nbsp;</td>
<td class="fillOE"><hr /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_name', 'h_polled_name'); ?></b></td>
<td class="fillEE"><input type="text" name="poll_name" id="poll_name" value="<?php echo htmlentities($poll_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="32" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b><?php echo $l['w_description']; ?></b>
<br />
<?php 
$doc_charset = F_word_language($poll_language, "a_meta_charset");
F_html_button("page", "form_polleditor", "poll_description", $doc_charset);

$current_ta_code = $poll_description;
if(!$formstatus) {
	$current_ta_code = stripslashes($current_ta_code);
}
?>
</td>
<td class="fillOE"><textarea cols="30" rows="5" name="poll_description" id="poll_description"><?php echo htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset); ?></textarea></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_date_start', 'h_polled_date_start'); ?></b></td>
<td class="fillEE"><input type="text" name="poll_date_start" id="poll_date_start" value="<?php echo $poll_date_start; ?>" size="30" maxlength="19" />
<input type="hidden" name="x_poll_date_start" id="x_poll_date_start" value="([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})" />
<input type="hidden" name="xl_poll_date_start" id="xl_poll_date_start" value="<?php echo $l['w_date_start']; ?>" /></td>
</tr>


<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_date_end', 'h_polled_date_end'); ?></b></td>
<td class="fillOE"><input type="text" name="poll_date_end" id="poll_date_end" value="<?php echo $poll_date_end; ?>" size="30" maxlength="19" />
<input type="hidden" name="x_poll_date_end" id="x_poll_date_end" value="([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})" />
<input type="hidden" name="xl_poll_date_end" id="xl_poll_date_end" value="<?php echo $l['w_date_end']; ?>" /></td>
</tr>


<!-- SELECT LEVEL ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_level', 'h_polled_level'); ?></b></td>
<td class="fillEE"><select name="poll_level" id="poll_level" size="0">
<?php
$sql = "SELECT * FROM ".K_TABLE_LEVELS." ORDER BY level_code ";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['level_code']."\"";
		if($m['level_code'] == $poll_level) {
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

<?php
if (isset($poll_id) AND ($poll_id > 0)) {
?>
<tr class="fillO">
<td class="fillOO" align="right">&nbsp;</td>
<td class="fillOE"><a href="cp_edit_polls_options.<?php echo CP_EXT; ?>?polloption_pollid=<?php echo $poll_id; ?>&amp;poll_language=<?php echo urlencode($poll_language); ?>"><b><?php echo $l['t_polls_option']; ?>&nbsp;&gt;&gt;</b></a></td>
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
if (isset($poll_id) AND ($poll_id > 0)) {
	F_submit_button("form_polleditor","menu_mode",$l['w_update']); 
	F_submit_button("form_polleditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_polleditor","menu_mode",$l['w_add']); 
F_submit_button("form_polleditor","menu_mode",$l['w_clear']); 
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
