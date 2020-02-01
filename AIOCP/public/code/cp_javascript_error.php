<?php
//============================================================+
// File name   : cp_javascript_error.php                       
// Begin       : 2001-09-12                                    
// Last Update : 2003-07-18                                    
//                                                             
// Description : Display error message for JavaScript-disabled 
//               browsers                                      
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

//send XHTML headers
echo "<"."?xml version=\"1.0\" encoding=\"".$l['a_meta_charset']."\"?".">\n";
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"DTD/xhtml1-transitional.dtd\">\n";
echo "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"".$l['a_meta_language']."\" lang=\"".$l['a_meta_language']."\" dir=\"".$l['a_meta_dir']."\">\n";
?>

<head>
	<title><?php echo $l['t_warning'] ?> - <?php echo K_AIOCP_TITLE; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $l['a_meta_charset']; ?>" />
	<meta name="aiocp_level" CONTENT="0" />
	<meta name="description" content="<?php echo $l['t_warning']; ?> - <?php echo K_AIOCP_DESCRIPTION; ?>" />
	<meta name="Author"      content="<?php echo K_AIOCP_AUTHOR; ?>" />
	<meta name="Reply-to"    content="<?php echo K_AIOCP_REPLY_TO; ?>" />
	<meta name="keywords"    content="<?php echo $l['t_warning']; ?>,<?php echo K_AIOCP_KEYWORDS; ?>" />
	<link rel="stylesheet" href="<?php echo htmlentities(urldecode(K_AIOCP_STYLE)); ?>" type="text/css" />
</head>

<body>
<!-- ====================================================== -->

<h2><?php echo $l['t_warning']; ?>:</h2>
<p>
<b>
Your browser is not JavaScript-enabled!<br />
Please enable the JavaScript feature from your browser options or upgrade it to a newer version.
</b>
</p>

<?php
exit();

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
