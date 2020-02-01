<?php
//============================================================+
// File name   : cp_edit_polls_options.php                     
// Begin       : 2001-10-10                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit polls options                            
//               (K_TABLE_POLLS_OPTIONS table)                 
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_POLLS_OPTIONS;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_polls_option'];

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
		F_stripslashes_formfields(); // Delete
		$sql = "DELETE FROM ".K_TABLE_POLLS_OPTIONS." WHERE polloption_id=".$polloption_id."";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		$polloption_id=FALSE;
		break;
		}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_POLLS_OPTIONS, "(polloption_pollid=".$polloption_pollid." AND polloption_name='".$polloption_name."')", "polloption_id", $polloption_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$sql = "UPDATE IGNORE ".K_TABLE_POLLS_OPTIONS." SET 
				polloption_pollid=".$polloption_pollid.", 
				polloption_name='".$polloption_name."' 
				WHERE polloption_id=".$polloption_id."";
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
			//check if polloption_name is unique for selected language
			$sql = "SELECT polloption_name FROM ".K_TABLE_POLLS_OPTIONS." WHERE (polloption_pollid=".$polloption_pollid." AND polloption_name='".$polloption_name."')";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add
				$sql = "INSERT IGNORE INTO ".K_TABLE_POLLS_OPTIONS." (
				polloption_pollid, 
				polloption_name
				) VALUES (
				'".$$polloption_pollid."', 
				'".$polloption_name."')";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$polloption_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$polloption_name = "";
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

if((!isset($polloption_pollid) OR (!$polloption_pollid)) OR (isset($changelanguage) AND $changelanguage)) {
	$sql = "SELECT * FROM ".K_TABLE_POLLS." WHERE poll_language='".$poll_language."' ORDER BY poll_name LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$polloption_pollid = $m['poll_id'];
		}
		else {
			$polloption_pollid = false;
		}
	}
	else {
		F_display_db_error();
	}
}

if($formstatus) {
	if($polloption_pollid) {
		if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
			if($changepoll OR (!$polloption_id)) {
				$sql = "SELECT * FROM ".K_TABLE_POLLS_OPTIONS." WHERE polloption_pollid=".$polloption_pollid." ORDER BY polloption_name LIMIT 1";
			}
			else {$sql = "SELECT * FROM ".K_TABLE_POLLS_OPTIONS." WHERE polloption_id=".$polloption_id." LIMIT 1";}
			if($r = F_aiocpdb_query($sql, $db)) {
				if($m = F_aiocpdb_fetch_array($r)) {
					$polloption_id = $m['polloption_id'];
					$polloption_pollid = $m['polloption_pollid'];
					$polloption_name = $m['polloption_name'];
				}
				else {
					$polloption_name = "";
				}
			}
			else {
				F_display_db_error();
			}
		}
	}
	else {
		$polloption_name = "";
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_polloptioneditor" id="form_polloptioneditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="polloption_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT  language ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_language', 'h_pollvote_language'); ?></b></td>
<td class="fillOE">
<input type="hidden" name="changelanguage" id="changelanguage" value="0" />
<select name="poll_language" id="poll_language" size="0" onchange="document.form_polloptioneditor.changelanguage.value=1; document.form_polloptioneditor.submit()">
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

<!-- SELECT poll ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_poll', 'h_pollvote_poll'); ?></b></td>
<td class="fillEE">
<input type="hidden" name="changepoll" id="changepoll" value="0" />
<select name="polloption_pollid" id="polloption_pollid" size="0" onchange="document.form_polloptioneditor.changepoll.value=1; document.form_polloptioneditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_POLLS." WHERE poll_language='".$poll_language."' ORDER BY poll_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['poll_id']."\"";
		if($m['poll_id'] == $polloption_pollid) {
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
<!-- END SELECT poll ==================== -->

<!-- SELECT option ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_option', 'h_pollopt_select'); ?></b></td>
<td class="fillOE">
<select name="polloption_id" id="polloption_id" size="0" onchange="document.form_polloptioneditor.submit()">
<?php
if($polloption_pollid) {
	$sql = "SELECT * FROM ".K_TABLE_POLLS_OPTIONS." WHERE polloption_pollid='".$polloption_pollid."' ORDER BY polloption_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"".$m['polloption_id']."\"";
			if($m['polloption_id'] == $polloption_id) {
				echo " selected=\"selected\"";
			}
			echo ">".htmlentities($m['polloption_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
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
<!-- END SELECT option ==================== -->

<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE"><hr /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_name', 'h_pollopt_name'); ?></b></td>
<td class="fillOE"><input type="text" name="polloption_name" id="polloption_name" value="<?php echo htmlentities($polloption_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="32" /></td>
</tr>

<?php //show buttons
if (isset($polloption_pollid) AND ($polloption_pollid > 0)) {
?>
<tr class="fillE">
<td class="fillEO" align="right">&nbsp;</td>
<td class="fillEE"><a href="cp_edit_polls.<?php echo CP_EXT; ?>?poll_id=<?php echo $polloption_pollid; ?>&amp;poll_language=<?php echo urlencode($poll_language); ?>"><b>&lt;&lt;&nbsp;<?php echo $l['t_polls']; ?></b></a></td>
</tr>
<?php //show buttons
}
?>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if (isset($polloption_id) AND ($polloption_id > 0)) {
	F_submit_button("form_polloptioneditor","menu_mode",$l['w_update']); 
	F_submit_button("form_polloptioneditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_polloptioneditor","menu_mode",$l['w_add']); 
F_submit_button("form_polloptioneditor","menu_mode",$l['w_clear']); 
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
