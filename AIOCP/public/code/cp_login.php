<?php
//============================================================+
// File name   : cp_login.php                                  
// Begin       : 2002-03-21                                    
// Last Update : 2003-07-18                                    
//                                                             
// Description : display Login interface and charge mai page   
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

$pagelevel = 1;
require_once('../../shared/code/cp_authorization.'.CP_EXT);
require_once('../../shared/code/cp_functions_collect_stats.'.CP_EXT);

//send XHTML headers
echo "<"."?xml version=\"1.0\" encoding=\"".$l['a_meta_charset']."\"?".">\n";
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"DTD/xhtml1-transitional.dtd\">\n";
echo "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"".$l['a_meta_language']."\" lang=\"".$l['a_meta_language']."\" dir=\"".$l['a_meta_dir']."\">\n";
?>

<head>
	<meta http-equiv='refresh' CONTENT='0;url="<?php echo K_MAIN_PAGE; ?>"' />
</head>

<body>
<a href="<?php echo htmlentities(urldecode(K_MAIN_PAGE)); ?>">LOGIN...</a>
</body>

</html>

<?php
//============================================================+
// END OF FILE                                                 
//============================================================+
?>
