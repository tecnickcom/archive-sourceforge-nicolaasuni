<?php
//============================================================+
// File name   : cp_edit_html_fckeditor.php
// Begin       : 2006-03-01
// Last Update : 2012-11-27
//
// Description : WYSIWYG HTML Editor using
//               CKeditor
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Tecnick.com s.r.l.
//               Via Della Pace n. 11
//               09044 Quartucciu (CA)
//               ITALY
//               www.tecnick.com
//               info@tecnick.com
//
// License: GNU GENERAL PUBLIC LICENSE v.2
//          http://www.gnu.org/copyleft/gpl.html
//============================================================+

require_once('../../shared/config/cp_extension.inc');
require_once('../config/cp_config.'.CP_EXT);
require_once('../../shared/code/cp_functions_form.'.CP_EXT);

$pagelevel = K_AUTH_ADMIN_CP_EDIT_HTML_WYSIWYG;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

if (isset($_POST['htmlcode'])) {
	 echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
	 echo "//<![CDATA[\n";
	 // send data to calling form
	 echo "window.opener.document.".$_REQUEST['callingform'].".".$_REQUEST['callingfield'].".value='".ereg_replace("(\r\n|\n|\r)", " ", $_POST['htmlcode'])."';\n";
	 echo "window.close();\n"; // close this window
	 echo "//]]>\n";
	 echo "</script>\n";
	 exit;
}

$thispage_title = "HTML WYSIWYG";
$thispage_style = K_PATH_STYLE_SHEETS."aiocp_html_editor.css";

// set upload/image directories
switch ($_REQUEST['templates']) { //change image directory by case
	case 'newsletter': {
		// newsletter attachments needs a separate dir because
		// they will be handled by database
		$Config['UserFilesPath'] = K_PATH_AIOCP."attachments/";
		$Config['UserFilesAbsolutePath'] = K_PATH_MAIN."attachments/";
		break;
	}
	
	default: {
		$Config['UserFilesPath'] = K_PATH_AIOCP."pagefiles/";
		$Config['UserFilesAbsolutePath'] = K_PATH_MAIN."pagefiles/";
		break;
	}
}

echo "<"."?"."xml version=\"1.0\" encoding=\"".$l['a_meta_charset']."\""."?".">\n";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $l['a_meta_language']; ?>" lang="<?php echo $l['a_meta_language']; ?>" dir="<?php echo $l['a_meta_dir']; ?>">

<head>
	<title><?php echo $thispage_title; ?> - <?php echo K_AIOCP_TITLE; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $l['a_meta_charset']; ?>" />
	<meta name="aiocp_level" content="<?php echo $pagelevel; ?>" />
	<meta name="description" content="<?php echo $thispage_title; ?> - <?php echo $thispage_description; ?>" />
	<meta name="author" content="<?php echo $thispage_author; ?>" />
	<meta name="reply-to" content="<?php echo $thispage_reply; ?>" />
	<meta name="keywords" content="<?php echo $thispage_title; ?>, <?php echo $thispage_keywords; ?>" />
	<link rel="stylesheet" href="<?php echo htmlentities(urldecode($thispage_style)); ?>" type="text/css" />
</head>

<body style="margin:0px">

<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_fckeditor" id="form_fckeditor">
<textarea cols="60" rows="6" name="htmlcode" id="htmlcode"></textarea>
<input type="hidden" name="callingform" id="callingform" value="<?php echo $_REQUEST['callingform']; ?>" />
<input type="hidden" name="callingfield" id="callingfield" value="<?php echo $_REQUEST['callingfield']; ?>" />
</form>

<script language="JavaScript" src="../ckeditor/ckeditor.js"></script>
<script language="JavaScript" type="text/javascript">
//<![CDATA[
	// get data from calling form
	document.getElementById('htmlcode').value=window.opener.document.<?php echo $callingform.".".$callingfield; ?>.value;
	CKEDITOR.config.contentsCss = '<?php echo K_PATH_AIOCP; ?>public/styles/default.css';
	CKEDITOR.replace('htmlcode', {language: '<?php echo $l['a_meta_language']; ?>', filebrowserBrowseUrl: '../ckeditor/filemanager/index.html'});
//]]>
</script>

<?php 

require_once('../code/cp_page_footer_popup.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
