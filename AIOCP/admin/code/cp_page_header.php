<?php
//============================================================+
// File name   : cp_page_header.php
// Begin       : 2001-09-18
// Last Update : 2005-06-29
// 
// Description : page header for all pages
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

//if necessary load default values
if(!isset($pagelevel)) {$pagelevel = 0;}
if(!isset($thispage_title)) {$thispage_title = "";}
if(!isset($thispage_description)) {$thispage_description = K_AIOCP_DESCRIPTION;}
if(!isset($thispage_author)) {$thispage_author = K_AIOCP_AUTHOR;}
if(!isset($thispage_reply)) {$thispage_reply = K_AIOCP_REPLY_TO;}
if(!isset($thispage_keywords)) {$thispage_keywords = K_AIOCP_KEYWORDS;}
if(!isset($thispage_style)) {$thispage_style = K_AIOCP_STYLE;}

//send XHTML headers
echo "<"."?xml version=\"1.0\" encoding=\"".$l['a_meta_charset']."\"?".">\n";
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"\n";
echo "    \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
echo "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"".$l['a_meta_language']."\" lang=\"".$l['a_meta_language']."\" dir=\"".$l['a_meta_dir']."\">\n";
?>

<head>
	<title><?php echo $thispage_title; ?> - <?php echo K_AIOCP_TITLE; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $l['a_meta_charset']; ?>" />
	<meta name="aiocp_level" content="<?php echo $pagelevel; ?>" />
	<meta name="description" content="<?php echo $thispage_title; ?> - <?php echo $thispage_description; ?>" />
	<meta name="author"      content="<?php echo $thispage_author; ?>" />
	<meta name="reply-to"    content="<?php echo $thispage_reply; ?>" />
	<meta name="keywords"    content="<?php echo $thispage_title; ?>, <?php echo $thispage_keywords; ?>" />
	<link rel="stylesheet" href="<?php echo htmlentities(urldecode($thispage_style)); ?>" type="text/css" />
	<?php require('../code/cp_checkframe.'.CP_EXT); ?>
</head>

<body>

<?php 
//load overlib to display quick description help
if (K_DISPLAY_QUICK_HELP) { ?>
<!-- overLIB ==================== -->
<div id="overDiv" style="z-index: 1000; visibility: hidden; position: absolute"></div>
<script language="JavaScript" src="<?php echo K_PATH_SHARED_JSCRIPTS; ?>overlib_aiocp.js" type="text/javascript"></script>
<!-- END overLIB ==================== -->
<?php } ?>

<div><h1><img src="<?php echo K_PATH_IMAGES; ?>dots/cube/m/20.gif" alt="" width="20" height="20" border="0" /> <?php echo $thispage_title; ?></h1></div>


<?php
//============================================================+
// END OF FILE                                                 
//============================================================+
?>
