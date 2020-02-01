<?php
//============================================================+
// File name   : cp_upload_image.php                           
// Begin       : 2002-11-03                                    
// Last Update : 2003-10-21                                    
//                                                             
// Description : Upload image to specified directory on server 
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_HTML_IMAGE;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

require_once('../../shared/code/cp_functions_form.'.CP_EXT);
require_once('../code/cp_functions_upload.'.CP_EXT);

$thispage_title = "IMAGE UPLOAD";

require_once('../code/cp_page_header_popup.'.CP_EXT);

switch ($templates) { //change image directory by case
	case 'newsletter': {
		// newsletter attachments needs a separate dir because
		// they will be handled by database
		$imgdirurl = K_PATH_FILES_ATTACHMENTS;
		$imgdirfull = K_PATH_FILES_ATTACHMENTS_FULL;
		break;
	}
	
	default: {
		$imgdirurl = K_PATH_FILES_PAGES;
		$imgdirfull = K_PATH_FILES_PAGES_FULL;
		break;
	}
}

if (($menu_mode == $l['w_upload']) OR ($menu_mode == unhtmlentities($l['w_upload'])) ) {
	if($formstatus = F_check_form_fields()) {
		if($_FILES['userfile']['name']) {
			$isrc = F_upload_file("userfile", $imgdirurl);
			
			//close this window
			echo "<script language=\"JavaScript\" type=\"text/javascript\">";
			echo "//<![CDATA[\n";
			echo "close();\n";
			echo "//]]>\n";
			echo "</script>\n";
		} //upload file
	}
}

?>


<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_imageupload" id="form_imageupload">
<input type="hidden" name="templates" id="templates" value="<?php echo $templates; ?>" />

<table border="0" cellspacing="0" cellpadding="0">
<tr>
<td valign="top">


<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- Upload file ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b>src upload</b><br /><small>upload image</small></td>
<td class="fillOE" colspan="2">
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
<?php F_generic_button("cancel",$l['w_cancel'],"window.close()"); ?>
<?php F_submit_button("form_imageupload","menu_mode",$l['w_upload']); ?> 
</td>
</tr>
</table>
</td>
</tr>
</table>
</form>
<!-- ====================================================== -->
<?php 
require_once('../code/cp_page_footer_popup.'.CP_EXT); 

//============================================================+
// END OF FILE                                                 
//============================================================+
?>