<?php
//============================================================+
// File name   : cp_edit_ec_company_data.php                   
// Begin       : 2002-07-22                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit Company Data                             
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
require_once('../../shared/code/cp_functions_company_data.'.CP_EXT);
require_once('../code/cp_functions_upload.'.CP_EXT);
require_once('../../shared/code/cp_functions_language.'.CP_EXT);

$pagelevel = K_AUTH_ADMIN_CP_EDIT_EC_COMPANY_DATA;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_company_data_editor'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages;

if (isset($_REQUEST['menu_mode'])) {
	$menu_mode = $_REQUEST['menu_mode'];
} else {
	$menu_mode = "";
}
switch($menu_mode) {
	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			if($_FILES['userfile']['name']) { //upload image
				$companydata['logo'] = F_upload_file("userfile", K_PATH_IMAGES_COMPANY);
			}
			elseif ($logo_link) {
				$companydata['logo'] = $logo_link;
			}
			
			//prepare serialized string with all options inside
			$company_cfgdata = serialize($companydata);
			
			//write data to configuration file
			if($fp = fopen (K_FILE_COMPANY_DATA, "w")) {
				if(!fwrite($fp,$company_cfgdata)) {
					F_print_error("ERROR", "".K_FILE_COMPANY_DATA.": ".$l['m_writefile_not']);
				}
				fclose($fp);
			}
			else { //print an error message
				F_print_error("ERROR", "".K_FILE_COMPANY_DATA.": ".$l['m_openfile_not']);
			}
		}
		break;
	}
	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$companydata['name'] = "";
		$companydata['address'] = "";
		$companydata['city'] = "";
		$companydata['state'] = "";
		$companydata['postcode'] = "";
		$companydata['country'] = "";
		$companydata['telephone'] = "";
		$companydata['fax'] = "";
		$companydata['email'] = "";
		$companydata['url'] = "";
		$companydata['fiscalcode'] = "";
		$companydata['description'] = "";
		$companydata['otherdata'] = "";
		$companydata['logo'] = K_BLANK_IMAGE;
		$companydata['logowidth'] = 30;
		break;
	}
	default :{ 
		break;
	}
} //end of switch

// Initialize variables
if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		$companydata = F_get_company_data();
		$logo_link =  $companydata['logo'];
	}
}

if (!$logo_link) {
	$logo_link = K_BLANK_IMAGE;
}

$doc_charset = F_word_language($selected_language, "a_meta_charset");
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_companydataeditor" id="form_companydataeditor">

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_name', 'h_companydata_name'); ?></b></td>
<td class="fillOE"><input type="text" name="companydata[name]" id="companydata_name" value="<?php echo htmlentities($companydata['name'], ENT_NOQUOTES, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_address', ''); ?></b></td>
<td class="fillEE"><input type="text" name="companydata[address]" id="companydata_address" value="<?php echo htmlentities($companydata['address'], ENT_NOQUOTES, $l['a_meta_charset']); ?>" size="30" maxlength="64" />
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_city', ''); ?></b></td>
<td class="fillOE"><input type="text" name="companydata[city]" id="companydata_city" value="<?php echo htmlentities($companydata['city'], ENT_NOQUOTES, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_state', ''); ?></b></td>
<td class="fillEE"><input type="text" name="companydata[state]" id="companydata_state" value="<?php echo htmlentities($companydata['state'], ENT_NOQUOTES, $l['a_meta_charset']); ?>" size="30" maxlength="255" />
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_postcode', ''); ?></b></td>
<td class="fillOE"><input type="text" name="companydata[postcode]" id="companydata_postcode" value="<?php echo htmlentities($companydata['postcode'], ENT_NOQUOTES, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_country', ''); ?></b></td>
<td class="fillEE">
<select name="companydata[country]" id="companydata_country" size="0">
<?php
$sql = "SELECT * FROM ".K_TABLE_COUNTRIES." ORDER BY country_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['country_name']."\"";
		if($m['country_name'] == $companydata['country']) {
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

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_phone', ''); ?></b></td>
<td class="fillOE"><input type="text" name="companydata[telephone]" id="companydata_telephone" value="<?php echo htmlentities($companydata['telephone'], ENT_NOQUOTES, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_fax', ''); ?></b></td>
<td class="fillEE"><input type="text" name="companydata[fax]" id="companydata_fax" value="<?php echo htmlentities($companydata['fax'], ENT_NOQUOTES, $l['a_meta_charset']); ?>" size="30" maxlength="255" />
</td>
</tr>


<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_email', ''); ?></b></td>
<td class="fillOE"><input type="text" name="companydata[email]" id="companydata_email" value="<?php echo $companydata['email']; ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_url', ''); ?></b></td>
<td class="fillEE"><input type="text" name="companydata[url]" id="companydata_url" value="<?php echo htmlentities($companydata['url'], ENT_NOQUOTES, $l['a_meta_charset']); ?>" size="30" maxlength="255" />
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_fiscalcode', 'h_companydata_fiscalcode'); ?></b></td>
<td class="fillOE"><input type="text" name="companydata[fiscalcode]" id="companydata_fiscalcode" value="<?php echo htmlentities($companydata['fiscalcode'], ENT_NOQUOTES, $l['a_meta_charset']); ?>" size="30" maxlength="255" />
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b><?php echo F_display_field_name('w_description', 'h_companydata_description'); ?></b></td>
<?php
$current_ta_code = $companydata['description'];
if(!$formstatus) {
	$current_ta_code = stripslashes($current_ta_code);
}
?>
<td class="fillEE"><textarea cols="30" rows="3" name="companydata[description]" id="companydata_description"><?php echo htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset); ?></textarea></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b><?php echo F_display_field_name('w_other', ''); ?></b></td>
<?php
$current_ta_code = $companydata['otherdata'];
if(!$formstatus) {
	$current_ta_code = stripslashes($current_ta_code);
}
?>
<td class="fillOE"><textarea cols="30" rows="3" name="companydata[otherdata]" id="companydata_otherdata"><?php echo htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset); ?></textarea></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><br/>&nbsp;</td>
<td class="fillEE"><b><?php echo F_display_field_name('w_logo', 'h_companydata_logo'); ?></b></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_link', 'h_companydata_link'); ?></b></td>
<td class="fillOE"><input type="text" name="logo_link" id="logo_link" value="<?php echo $logo_link; ?>" size="30" maxlength="255" onchange="FJ_show_logo2()" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_image_dir', 'h_companydata_dir'); ?></b></td>
<td class="fillEE" colspan="2">
<select name="logo_link_dir" id="logo_link_dir" size="0" onchange="document.form_companydataeditor.logo_link.value=document.form_companydataeditor.logo_link_dir.options[document.form_companydataeditor.logo_link_dir.selectedIndex].value; FJ_show_logo()">
<?php
// read directory for files (only graphics files).
$handle = opendir(K_PATH_IMAGES_COMPANY);
echo "<option value=\"\" selected=\"selected\"> - </option>\n";
	while (false !== ($file = readdir($handle))) {
		$path_parts = pathinfo($file);
		$file_ext = strtolower($path_parts['extension']);
		//check file type (GIF, JPG, PNG)
		if(($file_ext=="png")OR($file_ext=="gif")OR($file_ext=="jpg")OR($file_ext=="jpeg")) {
			echo "<option value=\"".$file."\"";
			if($file == $logo_link) {
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
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_upload', 'h_companydata_upload'); ?></b></td>
<td class="fillOE">
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo K_MAX_UPLOAD_SIZE; ?>" />
<input type="file" name="userfile" id="userfile" size="20" />
</td>
</tr>
<!-- END Upload file ==================== -->

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_width', 'h_companydata_width'); ?></b></td>
<td class="fillEE"><input type="text" name="companydata[logowidth]" id="companydata_logowidth" value="<?php echo $companydata['logowidth']; ?>" size="30" maxlength="255" /> <b>[mm]</b></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b><?php echo F_display_field_name('w_logo', ''); ?></b></td>
<td class="fillOE"><img name="imagelogo" src="<?php echo K_PATH_IMAGES_COMPANY; ?><?php echo $logo_link; ?>" border="0" alt="" /></td>
</tr>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
F_submit_button("form_companydataeditor","menu_mode",$l['w_update']); 
F_submit_button("form_companydataeditor","menu_mode",$l['w_clear']); 
?>
</td>

</tr>
</table>
</form>

<!-- Show selected logo image ==================== -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
function FJ_show_logo(){
	document.images.imagelogo.src= "<?php echo K_PATH_IMAGES_COMPANY; ?>"+document.form_companydataeditor.logo_link_dir.options[document.form_companydataeditor.logo_link_dir.selectedIndex].value;
}

function FJ_show_logo2(){
	document.images.imagelogo.src= "<?php echo K_PATH_IMAGES_COMPANY; ?>"+document.form_companydataeditor.logo_link.value;
}
//]]>
</script>
<!-- END Show selected logo image ==================== -->
<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
