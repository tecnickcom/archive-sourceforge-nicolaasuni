<?php
//============================================================+
// File name   : cp_functions_aiocpcode_editor.php
// Begin       : 2002-02-20
// Last Update : 2007-02-08
// 
// Description : AIOCP Code Editor
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

// ------------------------------------------------------------
// Display AIOCP Code EDITOR
// $callingform = name of the calling form
// $callingfield = name of the calling form field (where text code will be sent)
// ------------------------------------------------------------
function AIOCPcodeEditor($callingform, $callingfield) {
	global $l, $db, $aiocp_dp;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
?>
<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {echo "?aiocp_dp=".$aiocp_dp."";} ?>" method="post" enctype="multipart/form-data" name="form_aiocpcodeeditor" id="form_aiocpcodeeditor">

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<th class="edge"><?php echo $l['w_aiocpcode']; ?></th>
</tr>

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<tr class="fillO" align="left" valign="top">
<!-- TEXTAREA ==================== -->
<td class="fillOE" align="center" valign="top">

<textarea cols="60" rows="15" name="aiocptext" id="aiocptext" onSelect="FJ_store_caret (this);" onclick="FJ_store_caret (this);" onKeyUp="FJ_store_caret (this);"></textarea>
<br />
<?php
echo aiocpCodeEditorTagButtons("form_aiocpcodeeditor", "aiocptext");
?>
</td>
</tr>
</table>
</td>
</tr>
<tr align="left" valign="top" class="edge">
<td align="center" class="edge">
<?php F_generic_button("cancel",$l['w_cancel'],"window.close()"); ?>
<?php F_generic_button("pastecode",$l['w_submit'],"window.opener.document.".$callingform.".".$callingfield.".value=document.form_aiocpcodeeditor.aiocptext.value; window.close()"); ?>
</td>
</tr>

</table>
</form>

<form action="cp_aiocpcode_preview.<?php echo CP_EXT; ?>" method="post" enctype="multipart/form-data" name="form_aiocpcodepreview" id="form_aiocpcodepreview" target="_blank">
<input type="hidden" name="aiocpcode" id="aiocpcode" value="<?php echo $aiocptext; ?>" />
<?php F_generic_submit_button("form_aiocpcodepreview","menu_mode",$l['w_preview'],"document.form_aiocpcodepreview.aiocpcode.value=document.form_aiocpcodeeditor.aiocptext.value"); ?>
</form>

<script language="JavaScript" type="text/javascript">
//<![CDATA[
//get text from calling form
document.form_aiocpcodeeditor.aiocptext.value=window.opener.document.<?php echo $callingform; ?>.<?php echo $callingfield; ?>.value;
//]]>
</script>
<!-- ====================================================== -->
<?php 
return;
}

/**
 * Display AIOCP Code EDITOR Tag Buttons
 * @author Nicola Asuni
 * @copyright Copyright &copy; 2004-2006, Tecnick.com LTD - Manor Coach House, Church Hill, Aldershot, Hants, GU12 4RQ, UK - www.tecnick.com - info@tecnick.com
 * @link www.tecnick.com
 * @since 2006-03-07
 * @param string $callingform name of calling xhtml form
 * @param string $callingfield name of calling form field (textarea where output code will be sent)
 * @return XHTML string
 */
function aiocpCodeEditorTagButtons($callingform, $callingfield) {
	global $l, $db;
	require_once('../config/cp_config.php');
	
	$buttons = "";
	$buttons .= "<script src=\"".K_PATH_SHARED_JSCRIPTS."inserttag.js\" type=\"text/javascript\"></script>\n";
	
	// --- buttons
	
	$onclick = "FJ_undo(document.getElementById('".$callingform."').".$callingfield.")";
	$buttons .= getImageButton($callingform, $callingfield, $l['w_undo'], "", K_PATH_IMAGES."aiocpcodebuttons/undo.png", $onclick, "z");
	
	$onclick = "FJ_redo(document.getElementById('".$callingform."').".$callingfield.")";
	$buttons .= getImageButton($callingform, $callingfield, $l['w_redo'], "", K_PATH_IMAGES."aiocpcodebuttons/redo.png", $onclick, "y");
	
	$onclick = "FJ_insert_tag(document.getElementById('".$callingform."').".$callingfield."";
	$buttons .= getImageButton($callingform, $callingfield, "bold", "[b]", K_PATH_IMAGES."aiocpcodebuttons/bold.png", $onclick, "b");
	$buttons .= getImageButton($callingform, $callingfield, "italic", "[i]", K_PATH_IMAGES."aiocpcodebuttons/italic.png", $onclick, "i");
	$buttons .= getImageButton($callingform, $callingfield, "small", "[small]", K_PATH_IMAGES."aiocpcodebuttons/small.png", $onclick, "s");
	$buttons .= getImageButton($callingform, $callingfield, "subscript", "[sub]", K_PATH_IMAGES."aiocpcodebuttons/subscr.png", $onclick, "v");
	$buttons .= getImageButton($callingform, $callingfield, "superscript", "[sup]", K_PATH_IMAGES."aiocpcodebuttons/superscr.png", $onclick, "a");
	$buttons .= getImageButton($callingform, $callingfield, "link", "[url]", K_PATH_IMAGES."aiocpcodebuttons/link.png", $onclick, "k");
	$buttons .= getImageButton($callingform, $callingfield, "unordered list", "[ulist]", K_PATH_IMAGES."aiocpcodebuttons/bullist.png", $onclick, "u");
	$buttons .= getImageButton($callingform, $callingfield, "ordered list", "[olist]", K_PATH_IMAGES."aiocpcodebuttons/numlist.png", $onclick, "o");
	$buttons .= getImageButton($callingform, $callingfield, "list item", "[li]", K_PATH_IMAGES."aiocpcodebuttons/li.png", $onclick, "l");
	$buttons .= getImageButton($callingform, $callingfield, "code", "[code]", K_PATH_IMAGES."aiocpcodebuttons/code.png", $onclick, "c");
	
	$onclick = "selectWindow=window.open('cp_select_emoticons.".CP_EXT."?formname=".$callingform."&amp;idfield=".$callingfield."&amp;fieldtype=2&amp;fsubmit=0','selectWindow','dependent,height=300,width=300,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')";
	$buttons .= getImageButton($callingform, $callingfield, "emoticon", "", K_PATH_IMAGES."aiocpcodebuttons/emoticon.png", $onclick, "c");
	
	$onclick = "selectWindow=window.open('cp_select_country.".CP_EXT."?formname=".$callingform."&amp;idfield=".$callingfield."&amp;fieldtype=2&amp;fsubmit=0','selectWindow','dependent,height=300,width=300,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')";
	$buttons .= getImageButton($callingform, $callingfield, "flag", "", K_PATH_IMAGES."aiocpcodebuttons/flag.png", $onclick, "c");
	
	$buttons .= "<br />&nbsp;\n";
	
	return $buttons;
}

/**
 * Display one tag button
 * @author Nicola Asuni
 * @copyright Copyright &copy; 2004-2006, Tecnick.com LTD - Manor Coach House, Church Hill, Aldershot, Hants, GU12 4RQ, UK - www.tecnick.com - info@tecnick.com
 * @link www.tecnick.com
 * @since 2006-03-07
 * @param string $callingform name of calling xhtml form
 * @param string $callingfield name of calling form field (textarea where output code will be sent)
 * @param string $name name of the button
 * @param string $tag tag value
 * @param string $image image file of button
 * @param string $onclick default onclick action
 * @param string $accesskey accesskey: character for keyboard shortcut
 * @return XHTML string
 */
function getImageButton($callingform, $callingfield, $name, $tag, $image, $onclick="", $accesskey="") {
	if (strlen($tag) > 0) {
		$onclick = $onclick.", '".$tag."')";
	} 
	$str = "<a href=\"#\" onclick=\"".$onclick."\" title=\"".$name." [".$accesskey."]\" accesskey=\"".$accesskey."\">";
	$str .= "<img src=\"".$image."\" alt=\"".$name." [".$accesskey."]\" class=\"button\" />";
	$str .= "</a>\n";
	return $str;
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
