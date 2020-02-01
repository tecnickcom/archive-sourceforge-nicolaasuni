<?php
//============================================================+
// File name   : cp_edit_html_image.php                        
// Begin       : 2002-04-09                                    
// Last Update : 2003-10-26                                    
//                                                             
// Description : HTML <IMG> Editor                             
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

$thispage_title = "IMAGE";

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
		} //upload file
	}
}

if (!$isrc) {$isrc = K_BLANK_IMAGE;} //assign blank image

// resolve links
if (F_is_relative_link($linktocheck)) {
	$image_source = F_resolve_url_path($imgdirfull.$isrc);
}
else {
	$image_source = $isrc;
}

//if applicable, try to obtain full real pat from web address
$real_img_path = str_replace(K_PATH_HOST.K_PATH_AIOCP, K_PATH_MAIN, $image_source);

//get image size if not specified
$size = getimagesize($real_img_path);
if ( (!$iwidth) OR ($menu_mode != $l['w_submit']) )  {$iwidth = $size[0];}
if ( (!$iheight) OR ($menu_mode != $l['w_submit']) ) {$iheight = $size[1];}


switch($menu_mode) {
	case unhtmlentities($l['w_submit']):
	case $l['w_submit']: {
		if ($image_source) {
			$imgtag = "<img src=\"".$image_source."\"";
			if ($ialign) {$imgtag .= " align=\"".$ialign."\"";}
			if ($idir) {$imgtag .= " dir=\"".$idir."\"";}
			if ($iborder) {$imgtag .= " border=\"".$iborder."\"";}
			if ($iwidth) {$imgtag .= " width=\"".$iwidth."\"";}
			if ($iheight) {$imgtag .= " height=\"".$iheight."\"";}
			if ($ihspace) {$imgtag .= " hspace=\"".$ihspace."\"";}
			if ($ivspace) {$imgtag .= " vspace=\"".$ivspace."\"";}
			if ($iname) {$imgtag .= " name=\"".$iname."\"";}
			if ($iid) {$imgtag .= " id=\"".$iid."\"";}
			if ($ititle) {$imgtag .= " title=\"".$ititle."\"";}
			if ($ilang) {$imgtag .= " lang=\"".$ilang."\"";}
			if ($ialt) {$imgtag .= " alt=\"".$ialt."\"";}
			if ($ilongdesc) {$imgtag .= " longdesc=\"".$ilongdesc."\"";}
			if ($iclass) {$imgtag .= " class=\"".$iclass."\"";}
			if ($istyle) {$imgtag .= " style=\"".$istyle."\"";}
			if ($iusemap) {$imgtag .= " usemap=\"".$iusemap."\"";}
			if ($iismap) {$imgtag .= " ismap=\"ismap\"";}
			$imgtag .= " />";
			
			if($callingform AND $callingfield) {
				?>
				<script language="JavaScript" type="text/javascript">
				//<![CDATA[
				window.opener.document.<?php echo $callingform; ?>.<?php echo $callingfield; ?>.value = '<?php echo $imgtag; ?>';
				window.close();
				//]]>
				</script>
				<?php
			}
			else {
				?>
				<script language="JavaScript" type="text/javascript">
				//<![CDATA[
				if (window.opener.document.all.tbContentElement.DOM.selection.type == "Control") {
					window.opener.document.all.tbContentElement.DOM.selection.clear();
				}
				window.opener.document.all.tbContentElement.DOM.selection.createRange().pasteHTML('<?php echo $imgtag; ?>');
				window.close();
				//]]>
				</script>
				<?php
			}
		}
		break;
	}
	
} //end of switch
?>


<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_imageeditor" id="form_imageeditor">
<input type="hidden" name="callingform" id="callingform" value="<?php echo $callingform; ?>" />
<input type="hidden" name="callingfield" id="callingfield" value="<?php echo $callingfield; ?>" />
<input type="hidden" name="templates" id="templates" value="<?php echo $templates; ?>" />

<table border="0" cellspacing="0" cellpadding="0">
<tr>
<td valign="top">


<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b>src</b><br /><small>URI of image to embed</small></td>
<td class="fillOE" valign="top"><input type="text" name="isrc" id="isrc" value="<?php echo $isrc; ?>" size="20" maxlength="255" onchange="document.form_imageeditor.menu_mode.value='change'; document.form_imageeditor.submit();" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b>src select</b><br /><small>select from dir</small></td>
<td class="fillEE" colspan="2">
<select name="isrcdir" id="isrcdir" size="0" onchange="document.form_imageeditor.isrc.value=document.form_imageeditor.isrcdir.options[document.form_imageeditor.isrcdir.selectedIndex].value; document.form_imageeditor.menu_mode.value='change'; document.form_imageeditor.submit();">
<?php
echo "<option value=\"".K_BLANK_IMAGE."\">&nbsp;</option>\n";
// read directory for files (only graphics files).
$handle = opendir($imgdirurl);
	while (false !== ($file = readdir($handle))) {
		$path_parts = pathinfo($file);
		$file_ext = strtolower($path_parts['extension']);
		//check file type (GIF, JPG, PNG)
		if(($file_ext=="png")OR($file_ext=="gif")OR($file_ext=="jpg")OR($file_ext=="jpeg")) {
			echo "<option value=\"".$file."\"";
			if($file == $isrc) {
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
<td class="fillOO" align="right"><b>src upload</b><br /><small>upload image</small></td>
<td class="fillOE" colspan="2">
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo K_MAX_UPLOAD_SIZE; ?>" />
<input type="file" name="userfile" id="userfile" size="10" /><br />
<?php F_submit_button("form_imageeditor","menu_mode",$l['w_upload']); ?> 
</td>
</tr>
<!-- END Upload file ==================== -->

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b>align</b><br /><small>vertical or horizontal alignment</small></td>
<td class="fillEE" valign="top">
<select name="ialign" id="ialign" size="0">
<option value="" selected=\"selected\">&nbsp;</option>
<option value="left">left</option>
<option value="right">right</option>
<option value="top">top</option>
<option value="middle">middle</option>
<option value="bottom">bottom</option>
</select>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b>dir</b><br /><small>direction for weak/neutral text</small></td>
<td class="fillOE" valign="top">
<select name="idir" id="idir" size="0">
<option value="" selected=\"selected\">&nbsp;</option>
<option value="ltr">ltr</option>
<option value="rtl">rtl</option>
</select>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b>border</b><br /><small>link border width</small></td>
<td class="fillEE" valign="top"><input type="text" name="iborder" id="iborder" value="" size="20" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b>width</b><br /><small>override width</small></td>
<td class="fillOE" valign="top"><input type="text" name="iwidth" id="iwidth" value="<?php echo $iwidth; ?>" size="20" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b>height</b><br /><small>override height</small></td>
<td class="fillEE" valign="top"><input type="text" name="iheight" id="iheight" value="<?php echo $iheight; ?>" size="20" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b>hspace</b><br /><small>horizontal gutter</small></td>
<td class="fillOE" valign="top"><input type="text" name="ihspace" id="ihspace" value="" size="20" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b>vspace</b><br /><small>vertical gutter</small></td>
<td class="fillEE" valign="top"><input type="text" name="ivspace" id="ivspace" value="" size="20" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b>name</b><br /><small>name of image for scripting</small></td>
<td class="fillOE" valign="top"><input type="text" name="iname" id="iname" value="" size="20" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b>id</b><br /><small>document-wide unique id</small></td>
<td class="fillEE" valign="top"><input type="text" name="iid" id="iid" value="" size="20" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b>title</b><br /><small>advisory title</small></td>
<td class="fillOE" valign="top"><input type="text" name="ititle" id="ititle" value="" size="20" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b>lang</b><br /><small>language code</small></td>
<td class="fillEE" valign="top"><input type="text" name="ilang" id="ilang" value="" size="20" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b>alt</b><br /><small>short description</small></td>
<td class="fillOE" valign="top"><textarea cols="20" rows="2" name="ialt" id="ialt"></textarea></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b>longdesc</b><br /><small>link to long description (complements alt)</small></td>
<td class="fillEE" valign="top"><input type="text" name="ilongdesc" id="ilongdesc" value="" size="20" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b>class</b><br /><small>space-separated list of classes</small></td>
<td class="fillOE" valign="top"><input type="text" name="iclass" id="iclass" value="" size="20" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b>style</b><br /><small>associated style info</small></td>
<td class="fillEE" valign="top"><textarea cols="20" rows="2" name="istyle" id="istyle"></textarea></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b>usemap</b><br /><small>use client-side image map</small></td>
<td class="fillOE" valign="top"><input type="text" name="iusemap" id="iusemap" value="" size="20" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b>ismap</b><br /><small>use server-side image map</small></td>
<td class="fillEE" valign="top"><input type="checkbox" name="iismap" id="iismap" value="1" /></td>
</tr>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php F_generic_button("cancel",$l['w_cancel'],"window.close()"); ?>
<?php F_submit_button("form_imageeditor","menu_mode",$l['w_submit']); ?> 
</td>
</tr>
</table>


</td>

<td valign="top">
<a href="<?php echo htmlentities(urldecode($image_source)); ?>"><img name="imagepreview" src="<?php echo htmlentities(urldecode($image_source)); ?>" border="0" alt="" width="<?php echo $iwidth; ?>" height="<?php echo $iheight; ?>" /></a>
</script>
</td>
</tr>
</table>
</form>
<br />
<!-- ====================================================== -->
<?php 
require_once('../code/cp_page_footer_popup.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>