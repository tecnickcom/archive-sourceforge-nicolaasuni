<?php
//============================================================+
// File name   : cp_download_code.php                          
// Begin       : 2003-08-20                                    
// Last Update : 2008-07-06
//                                                             
// Description : calculate verification link for 
//               protected download
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

$pagelevel = K_AUTH_ADMIN_CP_DOWNLOAD_CODE;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_download_link'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages; 

require_once('../../shared/code/cp_functions_form.'.CP_EXT);

if (isset($_REQUEST['menu_mode'])) {
	$menu_mode = $_REQUEST['menu_mode'];
} else {
	$menu_mode = "";
}
switch($menu_mode) {

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Delete country
		$real_link = K_PATH_FILES_DOWNLOADABLES.$downlink;
		$verifycode = F_generate_verification_code($real_link, 6);
		$full_link =  K_PATH_HOST.K_PATH_AIOCP."shared/code/cp_download.".CP_EXT."?c=".$verifycode."&amp;d=6&amp;f=".urlencode($real_link)."";
		echo "<form><b>".$l['w_link']."</b>:<textarea cols=\"40\" rows=\"1\" name=\"link\" id=\"link\" readonly=\"readonly\" wrap=\"off\">".$full_link."</textarea></form><br />";
		break;
		}

	default :{ 
		break;
		}

} //end of switch

if (!isset($downlink)) {
	$downlink = "";
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_downloadlink" id="form_downloadlink">

<input type="hidden" name="downlink" id="downlink" value="<?php echo $downlink; ?>" />


<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_download_dir', 'h_downloaded_dir'); ?></b></td>
<td class="fillEE" colspan="2">
<select name="download_link_dir" id="download_link_dir" size="0" onchange="document.form_downloadlink.downlink.value=document.form_downloadlink.download_link_dir.options[document.form_downloadlink.download_link_dir.selectedIndex].value">
<?php
// read directory for files
$handle = opendir(K_PATH_FILES_DOWNLOAD);
echo "<option value=\"\" selected=\"selected\"> - </option>\n";
	while (false !== ($file = readdir($handle))) {
		if(($file != ".")AND($file != "..")) {
			echo "<option value=\"".$file."\"";
			if($file == $downlink) {
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



</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
F_submit_button("form_downloadlink","menu_mode",$l['w_update']); 
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
