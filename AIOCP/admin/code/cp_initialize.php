<?php
//============================================================+
// File name   : cp_initialize.php                             
// Begin       : 2001-09-05                                    
// Last Update : 2003-07-18                                    
//                                                             
// Description : Check various security things                 
//               (warning messages in english)                 
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

$files_to_check = array();
$i=0;
$files_to_check[$i++] = "../../admin/config/cp_config.".CP_EXT;
$files_to_check[$i++] = "../../shared/config/cp_config.".CP_EXT;
$files_to_check[$i++] = "../../public/config/cp_config.".CP_EXT;
$files_to_check[$i++] = "../../shared/config/cp_extension.inc";
$files_to_check[$i++] = "../../shared/config/cp_db_config.".CP_EXT;
$files_to_check[$i++] = "../../shared/config/cp_email_config.".CP_EXT;
$files_to_check[$i++] = "../../shared/config/cp_paths.".CP_EXT;
$files_to_check[$i++] = "../../shared/config/cp_general_constants.".CP_EXT;
//$files_to_check[$i++] = "../../admin/config/cp_tags.".CP_EXT;

// -- Check if the config files are writable (shouldn't be!) --
// -- and try to change status (only for unix systems)
$write_check = true;
while(list ($key, $val) = each ($files_to_check)) {
    if(@fopen($val, "a")) { //if file is writeable
		chmod($val, 0644); //try to change permission
		if(@fopen($val, "a")) { //try to check anohter time
			$write_check = false;
		}
	}
}

// -- Display error message  --
if(!$write_check) {
	$thispage_title = $l['t_warning'];

//send XHTML headers
echo "<"."?xml version=\"1.0\" encoding=\"".$l['a_meta_charset']."\"?".">\n";
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"DTD/xhtml1-transitional.dtd\">\n";
echo "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"".$l['a_meta_language']."\" lang=\"".$l['a_meta_language']."\" dir=\"".$l['a_meta_dir']."\">\n";
?>

<head>
	<title><?php echo $thispage_title; ?> - <?php echo K_AIOCP_TITLE; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $l['a_meta_charset']; ?>" />
	<meta name="aiocp_level" content="<?php echo $pagelevel; ?>" />
	<meta name="description" content="<?php echo $thispage_title; ?>- <?php echo K_AIOCP_DESCRIPTION; ?>" />
	<meta name="Author"      content="<?php echo K_AIOCP_AUTHOR; ?>" />
	<meta name="Reply-to"    content="<?php echo K_AIOCP_REPLY_TO; ?>" />
	<meta name="keywords"    content="<?php echo $thispage_title; ?>,<?php echo K_AIOCP_KEYWORDS; ?>" />
	<link rel="stylesheet" href="<?php echo htmlentities(urldecode(K_AIOCP_STYLE)); ?>" type="text/css" />  
</head>

<body>

<h1><?php echo $thispage_title; ?></h1>

<!-- ====================================================== -->
<p>
<b>
One or more config files are writeable by the webserver!<br />
This is a security risk.<br />
<i>Control Panel</i> will not be able to run until this is fixed.<br />
<br />
On Windows system switch on the 'read-only' attribute from the <i>Properties</i> of config files (config directory).<br />
<br />
On UNIX or UNIX-like systems this can be done with the following command from the shell:<br />
<br />
<?php
while(list ($key, $val) = each ($files_to_check)) {
  echo "chmod 644 ".$val."<br />";
}
?>
<br />
Or use your FTP program to do this.<br />
<br />

</b>
</p>
<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT); ?>

	<?php
	exit();
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
