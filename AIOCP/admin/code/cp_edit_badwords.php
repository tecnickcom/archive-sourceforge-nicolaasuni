<?php
//============================================================+
// File name   : cp_edit_badwords.php                          
// Begin       : 2002-02-26                                    
// Last Update : 2008-07-06
//                                                             
// Description : Add/remove/update bad words                   
//               that must be censored                         
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_BADWORDS;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

require_once('../../shared/code/cp_functions_form.'.CP_EXT);

$thispage_title = $l['t_badwords_editor'];

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
		F_stripslashes_formfields(); // Delete
		$sql = "DELETE FROM ".K_TABLE_BADWORDS." WHERE badword_id=".$badword_id."";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		$badword_id=FALSE;
		break;
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_BADWORDS, "badword_name='".$badword_name."'", "badword_id", $badword_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$sql = "UPDATE IGNORE ".K_TABLE_BADWORDS." SET badword_name='".$badword_name."' WHERE badword_id=".$badword_id."";
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
			//check if badword_name is unique
			$sql = "SELECT badword_name FROM ".K_TABLE_BADWORDS." WHERE badword_name='".$badword_name."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				$sql = "INSERT IGNORE INTO ".K_TABLE_BADWORDS." (badword_name) VALUES ('".$badword_name."')";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$badword_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
	}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$badword_name = "";
	break;
		}

	default :{ 
		break;
	}

} //end of switch

// Initialize variables
if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!isset($badword_id) OR (!$badword_id)) {
			$sql = "SELECT * FROM ".K_TABLE_BADWORDS." ORDER BY badword_name LIMIT 1";
		} else {
			$sql = "SELECT * FROM ".K_TABLE_BADWORDS." WHERE badword_id=".$badword_id." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$badword_id = $m['badword_id'];
				$badword_name = $m['badword_name'];
			}
		}
		else {
			F_display_db_error();
		}
	}
}
if (!isset($badword_name)) {
	$badword_name = "";
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_badwordseditor" id="form_badwordseditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="badword_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT badword ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_badword', 'h_badword_select'); ?></b></td>
<td class="fillOE">
<select name="badword_id" id="badword_id" size="0" onchange="document.form_badwordseditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_BADWORDS." ORDER BY badword_name";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['badword_id']."\"";
		if($m['badword_id'] == $badword_id) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['badword_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT badword ==================== -->

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_name', 'h_badword_name'); ?></b></td>
<td class="fillEE"><input type="text" name="badword_name" id="badword_name" value="<?php echo htmlentities($badword_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="20" maxlength="255" /></td>
</tr>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if (isset($badword_id) AND ($badword_id > 0)) {
	F_submit_button("form_badwordseditor","menu_mode",$l['w_update']); 
	F_submit_button("form_badwordseditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_badwordseditor","menu_mode",$l['w_add']); 
F_submit_button("form_badwordseditor","menu_mode",$l['w_clear']); 
?>

</td>
</tr>
</table>
</form>
<!-- ====================================================== -->

<!-- Cange focus to badword_id select -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_badwordseditor.badword_id.focus();
//]]>
</script>
<!-- END Cange focus to badword_id select -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
