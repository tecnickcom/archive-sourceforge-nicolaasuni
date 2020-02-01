<?php
//============================================================+
// File name   : cp_edit_awards.php                            
// Begin       : 2001-11-25                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit Awards                                   
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

require_once('../code/cp_functions_upload.'.CP_EXT);

$pagelevel = K_AUTH_ADMIN_CP_EDIT_AWARDS;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_award_editor'];

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
		<input type="hidden" name="award_id" id="award_id" value="<?php echo $award_id; ?>" />
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
			$sql = "DELETE FROM ".K_TABLE_AWARDS." WHERE award_id=".$award_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			$award_id=FALSE;
		}
		break;
		}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_AWARDS,"(award_name='".$award_name."' AND award_date='".$award_date."')","award_id",$award_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				if($_FILES['userfile']['name']) {$award_logo = F_upload_file("userfile", K_PATH_IMAGES_AWARDS);} //upload file
				$award_description = addslashes(serialize($a_description));
				$sql = "UPDATE IGNORE ".K_TABLE_AWARDS." SET 
				award_date='".$award_date."', 
				award_name='".$award_name."', 
				award_link='".$award_link."', 
				award_description='".$award_description."', 
				award_logo='".$award_logo."' 
				WHERE award_id=".$award_id."";
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
			//check if name/date is unique
			$sql = "SELECT award_id FROM ".K_TABLE_AWARDS." WHERE (award_name='".$award_name."' AND award_date='".$award_date."')";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				if($_FILES['userfile']['name']) {$award_logo = F_upload_file("userfile", K_PATH_IMAGES_AWARDS);} //upload file
				$award_description = addslashes(serialize($a_description));
				$sql = "INSERT IGNORE INTO ".K_TABLE_AWARDS." (
				award_date, 
				award_name, 
				award_link, 
				award_description, 
				award_logo
				) VALUES (
				'".$award_date."', 
				'".$award_name."', 
				'".$award_link."', 
				'".$award_description."', 
				'".$award_logo."')";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$award_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$award_date = gmdate("Y-m-d");
		$award_name = "";
		$award_link = "";
		$a_description = array();
		$award_logo = "";
		break;
		}

	default :{ 
		break;
		}

} //end of switch

// Initialize variables
if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!isset($award_id) OR (!$award_id)) {
			$sql = "SELECT * FROM ".K_TABLE_AWARDS." ORDER BY award_date DESC LIMIT 1";
		} else {
			$sql = "SELECT * FROM ".K_TABLE_AWARDS." WHERE award_id=".$award_id." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$award_id = $m['award_id'];
				$award_date = $m['award_date'];
				$award_name = $m['award_name'];
				$award_link = $m['award_link'];
				$award_description = $m['award_description'];
				$a_description = unserialize($award_description);
				$award_logo = $m['award_logo'];
			}
			else {
				$award_date = gmdate("Y-m-d");
				$award_name = "";
				$award_link = "";
				$a_description = array();
				$award_logo = "";
			}
		}
		else {
			F_display_db_error();
		}
	}
}

?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_awardeditor" id="form_awardeditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="award_date, award_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_date'].",".$l['w_name']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT download ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_award', 'h_award_select'); ?></b></td>
<td class="fillOE">
<select name="award_id" id="award_id" size="0" onchange="document.form_awardeditor.submit()">
<?php
	$sql = "SELECT * FROM ".K_TABLE_AWARDS." ORDER BY award_date DESC";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"".$m['award_id']."\"";
			if($m['award_id'] == $award_id) {
				echo " selected=\"selected\"";
			}
			echo ">".htmlentities($m['award_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
		}
	}
	else {
		F_display_db_error();
	}
?>
</select>
</td>
</tr>
<!-- END SELECT download ==================== -->

<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE"><hr /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_date', 'h_award_date'); ?></b></td>
<td class="fillOE"><input type="text" name="award_date" id="award_date" value="<?php echo $award_date; ?>" size="50" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_name', 'h_award_name'); ?></b></td>
<td class="fillEE"><input type="text" name="award_name" id="award_name" value="<?php echo htmlentities($award_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="50" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_link', 'h_award_link'); ?></b></td>
<td class="fillOE"><input type="text" name="award_link" id="award_link" value="<?php echo $award_link; ?>" size="50" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_logo', 'h_award_logo'); ?></b></td>
<td class="fillEE"><input type="text" name="award_logo" id="award_logo" value="<?php echo $award_logo; ?>" size="50" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_image_dir', 'h_award_dir'); ?></b></td>
<td class="fillOE" colspan="2">
<select name="award_link_dir" id="award_link_dir" size="0" onchange="document.form_awardeditor.award_logo.value=document.form_awardeditor.award_link_dir.options[document.form_awardeditor.award_link_dir.selectedIndex].value">
<?php
// read directory for files
$handle = opendir(K_PATH_IMAGES_AWARDS);
echo "<option value=\"\" selected=\"selected\"> - </option>\n";
	while (false !== ($file = readdir($handle))) {
		if(($file != ".")AND($file != "..")) {
			echo "<option value=\"".$file."\"";
			if($file == $award_link) {
				echo " selected=\"selected\"";
			}
			echo ">".$file."</option>\n";
		}
	}
closedir($handle);
?>
</select>
</td>
</tr>

<!-- Upload file ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_upload', 'h_award_upload'); ?></b></td>
<td class="fillEE">
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo K_MAX_UPLOAD_SIZE; ?>" />
<input type="file" name="userfile" id="userfile" size="20" />
</td>
</tr>
<!-- END Upload file ==================== -->


<!-- iterate for each language ==================== -->
<?php
	$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<tr class=\"fillO\">";
			echo "<td class=\"fillOO\"><hr /></td>";
			echo "<td class=\"fillOE\"><b>".$m['language_name']."</b></td>";
			echo "</tr>";
			echo "<tr class=\"fillE\">";
			echo "<td class=\"fillEO\" align=\"right\" valign=\"top\"><b>".F_display_field_name('w_description', 'h_award_description')."</b><br />";
	
	$doc_charset = F_word_language($m['language_code'], "a_meta_charset");
	if ($doc_charset) {
		$doc_charset_url = "&amp;charset=".$doc_charset;
	}
	else {
		$doc_charset_url = "";
	}
?>




<input type="button" name="htmleditor_<?php echo $m['language_code']; ?>" id="htmleditor_<?php echo $m['language_code']; ?>" value="<?php echo $l['w_button_html_editor']; ?>" onclick="htmlWindow=window.open('cp_edit_html.<?php echo CP_EXT; ?>?templates=page&amp;callingform=form_awardeditor&amp;callingfield=elements['+FJ_get_field_index(this)+']<?php echo $doc_charset_url; ?>','htmlWindow','dependent,height=500,width=800,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')" />

<?php
echo "</td>";
if (isset($a_description[$m['language_code']])) {
	$current_ta_code = $a_description[$m['language_code']];
} else {
	$current_ta_code = "";
}
$current_ta_code = stripslashes($current_ta_code);
echo "<td class=\"fillEE\"><textarea cols=\"50\" rows=\"5\" name=\"a_description[".$m['language_code']."]\" id=\"a_description_".$m['language_code']."\">".htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset)."</textarea></td>";
echo "</tr>";
		}
	}
	else {
		F_display_db_error();
	}
?>
<!-- END iterate for each language ==================== -->

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if (isset($award_id) AND ($award_id > 0)) {
	F_submit_button("form_awardeditor","menu_mode",$l['w_update']); 
	F_submit_button("form_awardeditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_awardeditor","menu_mode",$l['w_add']); 
F_submit_button("form_awardeditor","menu_mode",$l['w_clear']); 
?>
</td>
</tr>
</table>
</form>
<!-- ====================================================== -->

<!-- Cange focus to award_id select -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_awardeditor.award_id.focus();

//get next elements ID
function FJ_get_field_index(what) {
	for (var i=0;i<document.form_awardeditor.elements.length;i++) {
		if(what == document.form_awardeditor.elements[i]) {
			return(i+1);
		}
	}
	return (-1);
}
//]]>
</script>
<!-- END Cange focus to award_id select -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
