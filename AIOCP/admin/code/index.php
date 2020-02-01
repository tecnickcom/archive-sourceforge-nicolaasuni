<?php
//============================================================+
// File name   : index.php
// Begin       : 2001-09-02
// Last Update : 2006-11-27
//
// Description : Load given page in argument on a
//               Main index file (charge frames).
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
$pagelevel = 0;
require_once('../../shared/code/cp_authorization.'.CP_EXT);
//require_once('../code/cp_initialize.'.CP_EXT); //check file permissions

if(isset($_REQUEST['load_page']) AND (strncmp($_REQUEST['load_page'], K_PATH_HOST, strlen(K_PATH_HOST))==0) ) {
	$load_page = $_REQUEST['load_page'];
} else {
	//default page
	$load_page="cp_layout_main.".CP_EXT;
}

//send XHTML headers
echo "<"."?xml version=\"1.0\" encoding=\"".$l['a_meta_charset']."\"?".">\n";
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Frameset//EN\" \"DTD/xhtml1-frameset.dtd\">\n";
echo "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"".$l['a_meta_language']."\" lang=\"".$l['a_meta_language']."\" dir=\"".$l['a_meta_dir']."\">\n";
?>

<head>
	<title><?php echo K_AIOCP_TITLE; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $l['a_meta_charset']; ?>" />
	<meta name="aiocp_level" content="0" />
	<meta name="description" content="<?php echo K_AIOCP_DESCRIPTION; ?>" />
	<meta name="Author"      content="<?php echo K_AIOCP_AUTHOR; ?>" />
	<meta name="Reply-to"    content="<?php echo K_AIOCP_REPLY_TO; ?>" />
	<meta name="keywords"    content="<?php echo K_AIOCP_KEYWORDS; ?>" />
	<link rel="shortcut icon" href="<?php echo htmlentities(urldecode(K_AIOCP_ICON)); ?>" />
</head>

<!-- frames -->
<?php
	$menu_frame = "<frame name=\"CPMENU\" src=\"cp_layout_menu.".CP_EXT."\" marginwidth=\"0\" marginheight=\"0\" scrolling=\"auto\" />\n";
	$main_frame = "<frame name=\"CPMAIN\" src=\"".$load_page."\" marginwidth=\"0\" marginheight=\"0\" scrolling=\"auto\" />\n";
	
	//set menu on the left or right by language direction
	if ($l['a_meta_dir'] == "rtl") {
		echo "<frameset cols=\"*,200\">";
		echo $main_frame;
		echo $menu_frame;
	}
	else {
		echo "<frameset cols=\"200,*\">";
		echo $menu_frame;
		echo $main_frame;
	}
?>

</frameset>

<!-- end frames -->

  <noframes>
	  <body>
	  <h1>Sorry, your browser don't support frames!</h1>
	  <h2>Frames links:</h2>
	  <ul>
		  <li><a href="cp_layout_menu.<?php echo CP_EXT; ?>">CPMENU</a></li>
		  <li><a href="<?php echo htmlentities(urldecode($load_page)); ?>">CPMAIN</a></li>
	  </ul>
	  </body>
  </noframes>
  
</html>

<?php
//============================================================+
// END OF FILE                                                 
//============================================================+
?>
