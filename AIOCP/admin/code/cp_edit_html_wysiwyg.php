<?php
//============================================================+
// File name   : cp_edit_html_wysiwyg.php
// Begin       : 2002-04-08
// Last Update : 2006-02-07
//
// Description : WYSIWYG HTML Editor using
//               Java Applet jxhtmledit.jar
//               http://jxhtmledit.sourceforge.net
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_HTML_WYSIWYG;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = "HTML WYSIWYG";
$thispage_style = K_PATH_STYLE_SHEETS."aiocp_html_editor.css";

switch ($_REQUEST['templates']) { //change image directory by case
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

// read directory for files (only graphics files).
$images_list = "";
$handle = opendir($imgdirurl);
while (false !== ($file = readdir($handle))) {
	$path_parts = pathinfo($file);
	$file_ext = strtolower($path_parts['extension']);
	//check file type (GIF, JPG, PNG)
	if(($file_ext=="png")OR($file_ext=="gif")OR($file_ext=="jpg")OR($file_ext=="jpeg")) {
		$images_list .= "".$file.":";
	}
}
if (strlen($images_list)>0) {
	$images_list = substr($images_list, 0, -1); //remove last ":"
}
closedir($handle);

echo "<"."?"."xml version=\"1.0\" encoding=\"".$l['a_meta_charset']."\""."?".">\n";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $l['a_meta_language']; ?>" lang="<?php echo $l['a_meta_language']; ?>" dir="<?php echo $l['a_meta_dir']; ?>">

<head>
	<title><?php echo $thispage_title; ?> - <?php echo K_AIOCP_TITLE; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $l['a_meta_charset']; ?>" />
	<meta name="aiocp_level" content="<?php echo $pagelevel; ?>" />
	<meta name="description" content="<?php echo $thispage_title; ?> - <?php echo $thispage_description; ?>" />
	<meta name="author"      content="<?php echo $thispage_author; ?>" />
	<meta name="reply-to"    content="<?php echo $thispage_reply; ?>" />
	<meta name="keywords"    content="<?php echo $thispage_title; ?>, <?php echo $thispage_keywords; ?>" />
	<link rel="stylesheet" href="<?php echo htmlentities(urldecode($thispage_style)); ?>" type="text/css" />
</head>

<body style="margin:0px">

<object 
	classid="clsid:8AD9C840-044E-11D1-B3E9-00805F499D93"
	codebase="http://java.sun.com/products/plugin/autodl/jinstall-1_4_0-win.cab"
	codetype="application/x-java-applet;version=1.4"
	standby="JXHTMLEDIT Loading ..."
	width="640"
	height="480"
	hspace="0"
	vspace="0"
	align="middle"
	border="0"
	name="jxhtmledit"
	id="jxhtmledit"
>

<param name="java_codebase" value="<?php echo K_PATH_HOST.K_PATH_AIOCP."admin/java/"; ?>" />
<param name="java_archive" value="jxhtmledit.jar" />
<param name="java_code" value="com.tecnick.jxhtmledit.JXHTMLedit.class" />
<param name="java_type" value="application/x-java-applet;version=1.4" />
<param name="scriptable" value="true" />
<param name="mayscript" value="mayscript" />
<param name="progressbar" value="true" />
<param name="boxmessage" value="JXHTMLEDIT Loading ..." />

<param name="param_page_encoding" value="<?php echo $l['a_meta_charset']; ?>" />
<param name="param_encoding" value="<?php echo $_REQUEST['charset']; ?>" />
<param name="param_lang" value="en" />
<param name="param_separate_window" value="true" />
<param name="param_callingform" value="<?php echo $_REQUEST['callingform']; ?>" />
<param name="param_callingfield" value="<?php echo $_REQUEST['callingfield']; ?>" />
<param name="param_stylesheet" value="" />
<param name="param_hide_source" value="false" />
<param name="param_xhtml" value="true" />
<param name="param_indent" value="false" />
<param name="param_entities_off" value="false" />
<param name="param_config_files_path" value="" />
<param name="param_buttons_images_path" value="" />
<param name="param_images_path" value="<?php echo $imgdirfull; ?>" />
<param name="param_images_list" value="<?php echo $images_list;?>" />
<param name="cache_archive" value="jxhtmledit.jar" />
<param name="cache_version" value="0.0.0.1" />
<param name="cache_archive_ex" value="jxhtmledit.jar" />


<!-- // embed code for netscape browser (that do not render object) -->
<embed 
	codebase="<?php echo K_PATH_HOST.K_PATH_AIOCP."admin/java/"; ?>" 
	archive="jxhtmledit.jar" 
	code="com.tecnick.jxhtmledit.JXHTMLedit.class" 
	type="application/x-java-applet;version=1.4" 
	pluginspage="http://java.sun.com/products/plugin/index.html#download" 
	mayscript="mayscript" 
	progressbar="true" 
	boxmessage="JXHTMLEDIT Loading ..." 
	width="640" 
	height="480" 
	hspace="0" 
	vspace="0" 
	align="middle" 
	name="jxhtmledit" 
	id="jxhtmledit" 
	param_encoding="<?php echo $_REQUEST['charset']; ?>" 
	param_page_encoding="<?php echo $l['a_meta_charset']; ?>" 
	param_lang="en" 
	param_separate_window="true" 
	param_callingform="<?php echo $_REQUEST['callingform']; ?>" 
	param_callingfield="<?php echo $_REQUEST['callingfield']; ?>" 
	param_stylesheet=""
	param_hide_source="false" 
	param_xhtml="true" 
	param_indent="false" 
	param_entities_off="false" 
	param_config_files_path=""
	param_buttons_images_path="" 
	param_images_path="<?php echo $imgdirfull; ?>" 
	param_images_list="<?php echo $images_list;?>"
	cache_archive="jxhtmledit.jar"
	cache_version="0.0.0.1"
	cache_archive_ex="jxhtmledit.jar"
	>
</embed>

</object>

<?php require_once('../code/cp_page_footer_popup.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
