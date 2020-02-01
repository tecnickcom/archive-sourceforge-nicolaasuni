<?php
//============================================================+
// File name   : cp_edit_country.php                           
// Begin       : 2001-09-08                                    
// Last Update : 2008-07-06
//                                                             
// Description : Add/remove/update countries in                
//               K_TABLE_COUNTRIES table                       
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

require_once('../code/cp_functions_upload.'.CP_EXT);

$pagelevel = K_AUTH_ADMIN_CP_EDIT_COUNTRY;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_country_editor'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages; ?>
<!-- ====================================================== -->
<?php
// Initialize variables
if(isset($country_flag) AND (!empty($country_flag))) {
	$size = GetImageSize(K_PATH_IMAGES_FLAGS.$country_flag);
	$country_width = $size[0];
	$country_height = $size[1];
} else {
	$country_flag = "";
}

require_once('../../shared/code/cp_functions_form.'.CP_EXT);
if (isset($_REQUEST['menu_mode'])) {
	$menu_mode = $_REQUEST['menu_mode'];
} else {
	$menu_mode = "";
}
switch($menu_mode) {

	case unhtmlentities($l['w_delete']):
	case $l['w_delete']:{
		F_stripslashes_formfields(); // Delete country
		$sql = "DELETE FROM ".K_TABLE_COUNTRIES." WHERE country_id=".$country_id."";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		$country_id=FALSE;
		break;
		}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update country
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_COUNTRIES, "country_name='".$country_name."'", "country_id", $country_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				if($_FILES['userfile']['name']) {
					$country_flag = F_upload_file("userfile", K_PATH_IMAGES_FLAGS);
					$size = GetImageSize(K_PATH_IMAGES_FLAGS.$country_flag);
					$country_width = $size[0];
					$country_height = $size[1];
				}
				$sql = "UPDATE IGNORE ".K_TABLE_COUNTRIES." SET 
				country_name='".$country_name."', 
				country_flag='".$country_flag."', 
				country_width='".$country_width."', 
				country_height='".$country_height."' 
				WHERE country_id=".$country_id."";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_add']):
	case $l['w_add']:{ // Add country
		if($formstatus = F_check_form_fields()) {
			//check if country_name is unique
			$sql = "SELECT country_name FROM ".K_TABLE_COUNTRIES." WHERE country_name='".$country_name."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				if($_FILES['userfile']['name']) {
					$country_flag = F_upload_file("userfile", K_PATH_IMAGES_FLAGS);
					$size = GetImageSize(K_PATH_IMAGES_FLAGS.$country_flag);
					$country_width = $size[0];
					$country_height = $size[1];
				}
				$sql = "INSERT IGNORE INTO ".K_TABLE_COUNTRIES." (
				country_name, 
				country_flag, 
				country_width, 
				country_height
				) VALUES (
				'".$country_name."', 
				'".$country_flag."', 
				'".$country_width."', 
				'".$country_height."')";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$country_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$country_name = "";
		$country_flag = "";
		$country_width = 0;
		$country_height = 0;
		break;
		}

	default :{ 
		break;
		}

} //end of switch

// Initialize variables
if($formstatus) {
	
	if(!F_count_rows(K_TABLE_COUNTRIES)) { //if the table is void (no items) assign new values
		$country_id = 1;
		$country_name = "- NO COUNTRY -";
		$country_flag = K_BLANK_IMAGE;
		$country_width = "32";
		$country_height = "20";
	}
	
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!isset($country_id) OR (!$country_id)) {
			$sql = "SELECT * FROM ".K_TABLE_COUNTRIES." ORDER BY country_name LIMIT 1";
		} else {
			$sql = "SELECT * FROM ".K_TABLE_COUNTRIES." WHERE country_id=".$country_id." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$country_id = $m['country_id'];
				$country_name = $m['country_name'];
				$country_flag = $m['country_flag'];
				$country_width = $m['country_width'];
				$country_height = $m['country_height'];
			}
		}
		else {
			F_display_db_error();
		}
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_countryeditor" id="form_countryeditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="country_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT country ==================== -->
<tr class="fillO">
<td class="fillOO" align="right">
<b><?php echo F_display_field_name_link('w_country', 'h_country_select', "selectWindow=window.open('cp_select_country.".CP_EXT."?formname=form_countryeditor&amp;idfield=country_id&amp;fieldtype=0&amp;fsubmit=1','selectWindow','dependent,height=450,width=450,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')"); ?></b>
</td>
<td class="fillOE">
<select name="country_id" id="country_id" size="0" onchange="document.form_countryeditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_COUNTRIES." ORDER BY country_name";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['country_id']."\"";
		if($m['country_id'] == $country_id) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['country_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
<td class="fillOE" rowspan="2" align="right" valign="top"><img name="imagecountry" src="<?php echo K_PATH_IMAGES_FLAGS; ?><?php echo $country_flag; ?>" border="0" alt="" width="<?php echo $country_width; ?>" height="<?php echo $country_height; ?>" /></td></tr>
<!-- END SELECT country ==================== -->

<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE"><hr /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_name', 'h_country_name'); ?></b></td>
<td class="fillOE" colspan="2"><input type="text" name="country_name" id="country_name" value="<?php echo htmlentities($country_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="20" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_link', 'h_country_link'); ?></b></td>
<td class="fillEE"><input type="text" name="country_flag" id="country_flag" value="<?php echo $country_flag; ?>" size="30" maxlength="255" onchange="FJ_show_flag2 ()" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_image_dir', 'h_country_directory'); ?></b></td>
<td class="fillOE" colspan="2">
<select name="country_flag_dir" id="country_flag_dir" size="0" onchange="document.form_countryeditor.country_flag.value=document.form_countryeditor.country_flag_dir.options[document.form_countryeditor.country_flag_dir.selectedIndex].value; FJ_show_flag ()">
<?php
// read directory for files (only graphics files).
$handle = opendir(K_PATH_IMAGES_FLAGS);
echo "<option value=\"\" selected=\"selected\"> - </option>\n";
	while (false !== ($file = readdir($handle))) {
		$path_parts = pathinfo($file);
		$file_ext = strtolower($path_parts['extension']);
		//check file type (GIF, JPG, PNG)
		if(($file_ext=="png")OR($file_ext=="gif")OR($file_ext=="jpg")OR($file_ext=="jpeg")) {
			echo "<option value=\"".$file."\"";
			if($file == $country_flag) {
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
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_upload', 'h_country_upload'); ?></b></td>
<td class="fillEE">
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo K_MAX_UPLOAD_SIZE; ?>" />
<input type="file" name="userfile" id="userfile" size="20" />
</td>
</tr>
<!-- END Upload file ==================== -->

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo $l['w_width']; ?>:</b></td>
<td class="fillOE" colspan="2"><?php echo $country_width; ?> px</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo $l['w_height']; ?>:</b></td>
<td class="fillEE" colspan="2"><?php echo $country_height; ?> px</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right">&nbsp;</td>
<td class="fillOE"><a href="cp_edit_country_states.<?php echo CP_EXT; ?>?state_country_id=<?php echo $country_id; ?>"><b><?php echo $l['t_country_states_editor']; ?>&nbsp;&gt;&gt;</b></a></td>
</tr>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if ($country_id) {
	F_submit_button("form_countryeditor","menu_mode",$l['w_update']); 
	F_submit_button("form_countryeditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_countryeditor","menu_mode",$l['w_add']); 
F_submit_button("form_countryeditor","menu_mode",$l['w_clear']); 
?>
</td>
</tr>
</table>
</form>
<!-- ====================================================== -->


<!-- Show selected flag image ==================== -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
function FJ_show_flag() {
	document.images.imagecountry.src= "<?php echo K_PATH_IMAGES_FLAGS; ?>"+document.form_countryeditor.country_flag_dir.options[document.form_countryeditor.country_flag_dir.selectedIndex].value;
}

function FJ_show_flag2() {
	document.images.imagecountry.src= "<?php echo K_PATH_IMAGES_FLAGS; ?>"+document.form_countryeditor.country_flag.value;
}

document.form_countryeditor.country_id.focus();
//]]>
</script>
<!-- END Cange focus to country_id select -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
