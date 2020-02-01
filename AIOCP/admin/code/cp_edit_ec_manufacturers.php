<?php
//============================================================+
// File name   : cp_edit_ec_manufacturers.php                  
// Begin       : 2002-10-19                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit Product's manufacturers                  
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_EC_MANUFACTURERS;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

require_once('../../shared/code/cp_functions_form.'.CP_EXT);
require_once('../code/cp_functions_upload.'.CP_EXT);

$thispage_title = $l['t_manufacturers_editor'];

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
		$sql = "DELETE FROM ".K_TABLE_EC_MANUFACTURERS." WHERE manuf_id=".$manuf_id."";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		$manuf_id=FALSE;
		
		if(($manuf_logo)AND($manuf_logo != K_BLANK_IMAGE)) { //if exist delete image
			//create image path
			$thismanuf_photo = K_PATH_IMAGES_MANUFACTURERS.$manuf_logo;
			if(file_exists($thismanuf_photo)) { // check if file exist
				if(!unlink($thismanuf_photo)) { // delete image
					F_print_error("ERROR", $thismanuf_photo.": ".$l['m_delete_not']);
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_EC_MANUFACTURERS, "manuf_name='".$manuf_name."'", "manuf_id", $manuf_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				if($_FILES['userfile']['name']) { //upload file
					$manuf_logo = F_upload_manuf_logo(md5($manuf_name));
				}
				if ((!empty($manuf_url)) AND F_is_relative_link($manuf_url)) {
					$manuf_url = "http://".$manuf_url;
				}
				$sql = "UPDATE IGNORE ".K_TABLE_EC_MANUFACTURERS." SET 
				manuf_name='".$manuf_name."',
				manuf_url='".$manuf_url."',
				manuf_logo='".$manuf_logo."'
				WHERE manuf_id='".$manuf_id."'";
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
			//check if name is unique
			$sql = "SELECT manuf_id FROM ".K_TABLE_EC_MANUFACTURERS." WHERE manuf_name='".$manuf_name."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				//add item
				if($_FILES['userfile']['name']) { //upload file
					$manuf_logo = F_upload_manuf_logo(md5($manuf_name));
				}
				if ((!empty($manuf_url)) AND F_is_relative_link($manuf_url)) {
					$manuf_url = "http://".$manuf_url;
				}
				$sql = "INSERT IGNORE INTO ".K_TABLE_EC_MANUFACTURERS." (
				manuf_name,
				manuf_url,
				manuf_logo
				) VALUES (
				'".$manuf_name."',
				'".$manuf_url."',
				'".$manuf_logo."'
				)";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$manuf_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$manuf_name = "";
		$manuf_url = "";
		$manuf_logo = K_BLANK_IMAGE;
		break;
		}

	default :{ 
		break;
		}

} //end of switch


if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!isset($manuf_id) OR (!$manuf_id)) {
			$sql = "SELECT * FROM ".K_TABLE_EC_MANUFACTURERS." ORDER BY manuf_name LIMIT 1";
		}
		else {$sql = "SELECT * FROM ".K_TABLE_EC_MANUFACTURERS." WHERE manuf_id=".$manuf_id." LIMIT 1";}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$manuf_id = $m['manuf_id'];
				$manuf_name = $m['manuf_name'];
				$manuf_url = $m['manuf_url'];
				$manuf_logo = $m['manuf_logo'];
			}
			else {
				$manuf_name = "";
				$manuf_url = "";
				$manuf_logo = K_BLANK_IMAGE;
			}
		}
		else {
			F_display_db_error();
		}
	}
}
?>
   
<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_manufacturerseditor" id="form_manufacturerseditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="manuf_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT  ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_manufacturer', 'h_productsed_manufacturer'); ?></b></td>
<td class="fillOE" colspan="2">
<select name="manuf_id" id="manuf_id" size="0" onchange="document.form_manufacturerseditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_EC_MANUFACTURERS." ORDER BY manuf_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['manuf_id']."\"";
		if($m['manuf_id'] == $manuf_id) {
			echo " selected=\"selected\"";
		}
		echo ">[".$m['manuf_id']."] ".htmlentities($m['manuf_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT  ==================== -->

<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE" colspan="2"><hr /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_name', 'h_productsed_product'); ?></b></td>
<td class="fillOE"><input type="text" name="manuf_name" id="manuf_name" value="<?php echo htmlentities($manuf_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>

<td class="fillEE" rowspan="5" align="right" valign="top"><img name="imagemanufacturer" src="<?php echo htmlentities(urldecode(K_PATH_IMAGES_MANUFACTURERS.$manuf_logo)); ?>" border="0" alt="" height="<?php echo K_MANUFACTURER_IMAGE_HEIGHT; ?>" width="<?php echo K_MANUFACTURER_IMAGE_WIDTH; ?>" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_link', 'h_manufacturer_link'); ?></b></td>
<td class="fillEE"><input type="text" name="manuf_url" id="manuf_url" value="<?php echo $manuf_url; ?>" size="30" maxlength="255" /></td>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_image', 'h_manufacturer_image'); ?></b></td>
<td class="fillOE"><input type="text" name="manuf_logo" id="manuf_logo" value="<?php echo $manuf_logo; ?>" size="30" maxlength="255" onchange="FJ_show_image2()" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_image_dir', 'h_productsed_imagedir'); ?></b></td>
<td class="fillEE">
<select name="manuf_logo_dir" id="manuf_logo_dir" size="0" onchange="document.form_manufacturerseditor.manuf_logo.value=document.form_manufacturerseditor.manuf_logo_dir.options[document.form_manufacturerseditor.manuf_logo_dir.selectedIndex].value; FJ_show_image()">
<?php
// read directory for files (only graphics files).
$handle = opendir(K_PATH_IMAGES_MANUFACTURERS);
echo "<option value=\"".K_BLANK_IMAGE."\" selected=\"selected\"> - </option>\n";
	while (false !== ($file = readdir($handle))) {
		$path_parts = pathinfo($file);
		$file_ext = strtolower($path_parts['extension']);
		//check file type (GIF, JPG, PNG)
		if(($file_ext=="png") OR ($file_ext=="gif") OR ($file_ext=="jpg") OR ($file_ext=="jpeg")) {
			echo "<option value=\"".$file."\"";
			if($file == $manuf_logo) {
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
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_upload', 'h_productsed_imageup'); ?></b></td>
<td class="fillOE">
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo K_MAX_UPLOAD_SIZE; ?>" />
<input type="file" name="userfile" id="userfile" size="20" />
</td>
</tr>
<!-- END Upload file ==================== -->

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if (isset($manuf_id) AND ($manuf_id > 0)) {
	F_submit_button("form_manufacturerseditor","menu_mode",$l['w_update']); 
	F_submit_button("form_manufacturerseditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_manufacturerseditor","menu_mode",$l['w_add']); 
F_submit_button("form_manufacturerseditor","menu_mode",$l['w_clear']); 
?>

</td>
</tr>

</table>
</form>
<!-- ====================================================== -->

<script language="JavaScript" type="text/javascript">
//<![CDATA[
function FJ_show_image(){
	tmpimage = document.form_manufacturerseditor.manuf_logo_dir.options[document.form_manufacturerseditor.manuf_logo_dir.selectedIndex].value;
	if (tmpimage.length > 0) {
		document.images.imagemanufacturer.src="<?php echo K_PATH_IMAGES_MANUFACTURERS; ?>"+tmpimage;
	}
}

function FJ_show_image2(){
	tmpimage = document.form_manufacturerseditor.manuf_logo.value;
	if (tmpimage.length > 0) {
		document.images.imagemanufacturer.src= "<?php echo K_PATH_IMAGES_MANUFACTURERS; ?>"+tmpimage;
	}
}
//]]>
</script>

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);


//------------------------------------------------------------
// Uploads image file to the server 
// create resized thumbnail image to configured values
// change image format to JPEG
//------------------------------------------------------------
function F_upload_manuf_logo($imagename) {
	global $l;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	$src_file = $_FILES['userfile']['tmp_name'];
	$src_path_parts = pathinfo($_FILES['userfile']['name']);
	$src_ext = strtolower($src_path_parts['extension']);
	
	$dst_file = $imagename.".jpg";
	$dst_small_path = K_PATH_IMAGES_MANUFACTURERS.$dst_file; //full path of miniature file
	
	//load source image and check format
	switch($src_ext) {
		/*
		case "gif":{
			$src_img = ImageCreateFromGif($src_file);
			break;
		}
		*/
		case "jpeg":
		case "jpg":{
			$src_img = ImageCreateFromJPEG($src_file);
			break;
		}
		case "png":{
			$src_img = ImageCreateFromPNG($src_file);
			break;
		}
		case "bmp":{
			$src_img = ImageCreateFromWBMP($src_file);
			break;
		}
		case "xbm":{
			$src_img = ImageCreateFromXBM($src_file);
			break;
		}
		case "xpm":{
			$src_img = ImageCreateFromXPM($src_file);
			break;
		}
		default:{
			F_print_error("WARNING", $_FILES['userfile']['name'].": ".$l['m_upload_not']." - ".$l['m_image_desc'].": JPG/PNG/BPM/XBM/XPM");
			return K_BLANK_IMAGE;
			break;
		}
	}
	
	$src_size = GetImageSize($src_file); //get image size information
	$src_width = $src_size[0];
	$src_height = $src_size[1];
	
	if($src_width < $src_height) {
		$dh = K_MANUFACTURER_IMAGE_HEIGHT;
		$dy = 0;
		$dw = round(($src_width * K_MANUFACTURER_IMAGE_HEIGHT ) / $src_height);
		$dx = round((K_MANUFACTURER_IMAGE_WIDTH - $dw) / 2);
	}
	else {
		$dw = K_MANUFACTURER_IMAGE_WIDTH;
		$dx = 0;
		$dh = round(($src_height * K_MANUFACTURER_IMAGE_WIDTH ) / $src_width);
		$dy = round((K_MANUFACTURER_IMAGE_HEIGHT - $dh) / 2);
	}
	
	//create resized image
	if (K_USE_GD2) {
		$dst_small_img = ImageCreateTrueColor(K_MANUFACTURER_IMAGE_WIDTH, K_MANUFACTURER_IMAGE_HEIGHT); //GD2
		//set background color
		$dst_background_color = ImageColorAllocate($dst_small_img, K_IMAGE_BACKGROUND_R, K_IMAGE_BACKGROUND_G, K_IMAGE_BACKGROUND_B);
		imagefill($dst_small_img, 0, 0, $dst_background_color);
		ImageCopyResampled($dst_small_img, $src_img, $dx, $dy, 0,  0, $dw, $dh, $src_width, $src_height);
	}
	else {
		$dst_small_img = ImageCreate(K_MANUFACTURER_IMAGE_WIDTH, K_MANUFACTURER_IMAGE_HEIGHT);
		//set background color
		$dst_background_color = ImageColorAllocate($dst_small_img, K_IMAGE_BACKGROUND_R, K_IMAGE_BACKGROUND_G, K_IMAGE_BACKGROUND_B);
		imagefill($dst_small_img, 0, 0, $dst_background_color);
		ImageCopyResized($dst_small_img, $src_img, $dx, $dy, 0,  0, $dw, $dh, $src_width, $src_height);
	}
	
	imagejpeg($dst_small_img,$dst_small_path); //output image thumbnail to filesystem
	
	ImageDestroy($src_img);
	ImageDestroy($dst_small_img);
	
	return $dst_file;
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
