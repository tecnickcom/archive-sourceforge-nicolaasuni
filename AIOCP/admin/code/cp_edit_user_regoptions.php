<?php
//============================================================+
// File name   : cp_edit_user_regoptions.php                   
// Begin       : 2002-02-13                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit User Registration Options                
//               (save options in a text file)                 
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
require_once('../../shared/code/cp_functions_user_edit.'.CP_EXT);

$pagelevel = K_AUTH_ADMIN_CP_EDIT_USER_REGOPTIONS;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_user_regoptions'];

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
	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//prepare serialized string with all options inside
			$usregopt['vermsg'] = serialize($user_vermsg);
			$usregopt['options'] = serialize($user_options);
			$usregopt['informfor'] = serialize($admin_inform);
			$usregopt['msgadmin'] = $usregopt_msgadmin;
			$usregopt['agreement'] = $usreg_agreement;
			$user_cfgdata = serialize($usregopt);
			
			//write data to configuration file
			if($fp = fopen (K_FILE_USER_REG_OPTIONS, "w")) {
				if(!fwrite($fp,$user_cfgdata)) {
					F_print_error("ERROR", "".K_FILE_USER_REG_OPTIONS.": ".$l['m_writefile_not']);
				}
				fclose($fp);
			}
			else { //print an error message
				F_print_error("ERROR", "".K_FILE_USER_REG_OPTIONS.": ".$l['m_openfile_not']);
			}
		}
		break;
	}
	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$usregopt['verification'] = 0;
		$usregopt['adminemail'] = "";
		$usregopt_msgadmin = "";
		$usreg_agreement = 1;
		$admin_inform = array();
		$user_vermsg = array();
		$user_options = array();
		break;
	}
	default :{ 
		break;
	}
} //end of switch

// Initialize variables
if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		$usregopt = F_get_user_reg_options();
		$usregopt['msgadmin'] = stripslashes($usregopt['msgadmin']);
		$admin_inform = unserialize($usregopt['informfor']);
		$user_vermsg = unserialize($usregopt['vermsg']);
		$user_options = unserialize($usregopt['options']);
		$usreg_agreement = $usregopt['agreement'];
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_userregopteditor" id="form_userregopteditor">

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_additional_fields', 'h_usreg_additional_fields'); ?></b></td>
<td class="fillOE">&nbsp;</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_firstname', ''); ?></b></td>
<td class="fillEE"><?php F_print_fields_option(0); ?></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_lastname', ''); ?></b></td>
<td class="fillOE"><?php F_print_fields_option(1); ?></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_birthdate', ''); ?></b></td>
<td class="fillEE"><?php F_print_fields_option(2); ?></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_birthplace', ''); ?></b></td>
<td class="fillOE"><?php F_print_fields_option(3); ?></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_fiscalcode', 'h_usred_fiscalcode'); ?></b></td>
<td class="fillEE"><?php F_print_fields_option(4); ?></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_photo', 'h_usered_photo'); ?></b></td>
<td class="fillOE"><?php F_print_fields_option(5); ?></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right">&nbsp;</td>
<td class="fillOE"><hr /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_inform_email', 'h_usreg_inform_email'); ?></b></td>
<td class="fillEE"><input type="text" name="usregopt[adminemail]" id="usregopt_adminemail" value="<?php echo $usregopt['adminemail']; ?>" size="30" maxlength="64" />
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_for', 'h_usreg_notify_for'); ?></b></td>
<td class="fillOE">
<?php 
F_print_info_option(0); echo "".$l['w_request']." ";
F_print_info_option(1); echo "".$l['w_confirm']." ";
F_print_info_option(2); echo "".$l['w_delete']." ";
F_print_info_option(3); echo "".$l['w_modify']." ";
?>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b><?php echo F_display_field_name('w_confirmation', 'h_usreg_adm_confirmation'); ?><br />(<?php echo $l['w_administrator']; ?>)</b>
<br />
<?php 
$doc_charset = $l['a_meta_charset'];
F_html_button("user", "form_userregopteditor", "usregopt_msgadmin", $doc_charset);

$current_ta_code = $usregopt['msgadmin'];
if(!$formstatus) {
	$current_ta_code = stripslashes($current_ta_code);
}
?>
</td>
<td class="fillEE"><textarea cols="50" rows="5" name="usregopt_msgadmin" id="usregopt_msgadmin"><?php echo htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset); ?></textarea></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right">&nbsp;</td>
<td class="fillOE"><hr /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_user_agreement', 'h_usreg_agreement'); ?></b></td>
<td class="fillEE">
<?php 
echo "<input type=\"checkbox\" name=\"usreg_agreement\" id=\"usreg_agreement\" value=\"1\"";
	if(stripslashes($usreg_agreement)) {echo " checked=\"checked\"";}
	echo " />";?>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_email_verification', 'h_usreg_email_verification'); ?></b></td>
<td class="fillOE">
<select name="usregopt[verification]" id="usregopt_verification" size="0">
<?php
		if($usregopt['verification']) {
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

<!-- iterate for each language ==================== -->
<?php
	$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<tr class=\"fillE\">";
			echo "<td class=\"fillEO\"><hr /></td>";
			echo "<td class=\"fillEE\"><b>".$m['language_name']."</b></td>";
			echo "</tr>";
			echo "<tr class=\"fillO\">";
			echo "<td class=\"fillOO\" align=\"right\" valign=\"top\"><b>".F_display_field_name('w_confirmation', 'h_usreg_usr_confirmation')."<br />(".F_display_field_name('w_user', '').")</b><br />";
			
			$doc_charset = F_word_language($m['language_code'], "a_meta_charset");
			if ($doc_charset) {
				$doc_charset_url = "&amp;charset=".$doc_charset;
			}
			else {
				$doc_charset_url = "";
			}
?>
<input type="button" name="htmleditor_<?php echo $m['language_code']; ?>" id="htmleditor_<?php echo $m['language_code']; ?>" value="<?php echo $l['w_button_html_editor']; ?>" onclick="htmlWindow=window.open('cp_edit_html.<?php echo CP_EXT; ?>?templates=user&amp;callingform=form_userregopteditor&amp;callingfield=elements['+FJ_get_field_index(this)+']<?php echo $doc_charset_url; ?>','htmlWindow','dependent,height=500,width=800,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')" />
<?php
echo "</td>";
$current_ta_code = $user_vermsg[$m['language_code']];
$current_ta_code = stripslashes($current_ta_code);
echo "<td class=\"fillOE\"><textarea cols=\"50\" rows=\"5\" name=\"user_vermsg[".$m['language_code']."]\" id=\"user_vermsg_".$m['language_code']."\">".htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset)."</textarea></td>";
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
F_submit_button("form_userregopteditor","menu_mode",$l['w_update']); 
F_submit_button("form_userregopteditor","menu_mode",$l['w_clear']); 
?>
</td>

</tr>
</table>
</form>
<!-- ====================================================== -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
//get next elements ID
function FJ_get_field_index(what) {
	for (var i=0;i<document.form_userregopteditor.elements.length;i++) {
		if(what == document.form_userregopteditor.elements[i]) {
			return(i+1);
		}
	}
	return (-1);
}
//]]>
</script>
<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//Print Yes/No Option selection
function F_print_fields_option($optnumber) {
	global $l,$user_options;
	echo "<input type=\"radio\" name=\"user_options[".$optnumber."]\" value=\"0\"";
	if(!stripslashes($user_options[$optnumber])) {echo " checked=\"checked\"";}
	echo " />".$l['w_no']."&nbsp;";
	echo "<input type=\"radio\" name=\"user_options[".$optnumber."]\" value=\"1\"";
	if(stripslashes($user_options[$optnumber])==1) {echo " checked=\"checked\"";}
	echo " />".$l['w_optional']."&nbsp;";
		echo "<input type=\"radio\" name=\"user_options[".$optnumber."]\" value=\"2\"";
	if(stripslashes($user_options[$optnumber])==2) {echo " checked=\"checked\"";}
	echo " />".$l['w_required']."&nbsp;";
}

//Print informfor selection
function F_print_info_option($key) {
	global $l,$admin_inform;
	echo "<input type=\"checkbox\" name=\"admin_inform[".$key."]\" id=\"admin_inform_".$key."\" value=\"1\"";
	if(isset($admin_inform[$key]) AND stripslashes($admin_inform[$key])) {
		echo " checked=\"checked\"";
	}
	echo " />";
}

?>

<?php
//============================================================+
// END OF FILE                                                 
//============================================================+
?>
