<?php
//============================================================+
// File name   : cp_aiocp_credits.php                          
// Begin       : 2002-03-11                                    
// Last Update : 2008-07-07                                   
//                                                             
// Description : Display AIOCP and third party Software Credits
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

$pagelevel = K_AUTH_ADMIN_CP_AIOCP_CREDITS;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_credits'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages; ?>
<!-- ====================================================== -->
<p>
<b>AIOCP - All In One Control Panel</b><br />
Author: Nicola Asuni<br />
Copyright: Tecnick.com LTD<br />
Address: Manor Coach House, Church Hill, Aldershot, Hants, GU12 4RQ, UK<br />
URL: <a href="http://www.tecnick.com" target="_blank">www.tecnick.com</a><br />
email: <a href="mailto:info@tecnick.com">info@tecnick.com</a><br />
License: <a href="../../LICENSE.TXT" target="_blank">GNU GENERAL PUBLIC LICENSE v.2</a>
</p>

<p>
<b>phpMyAdmin</b> (MySQL administrator) is distribuited "as is" without modifications<br />
Homepage: <a href="http://phpwizard.net/projects/phpMyAdmin/" target="_blank">http://phpwizard.net/projects/phpMyAdmin/</a><br />
License: <a href="http://www.gnu.org/copyleft/gpl.html" target="_blank">GPL (GNU GENERAL PUBLIC LICENSE)</a><br />
Location: /admin/phpMyAdmin/<br />
</p>

<p>
<b>overLIB</b> library is distribuited "as is" with some modifications<br />
This library has been used to obtain the "onMouseOver" description effect.<br />
Author: Erik Bosrup (<a href="mailto:erik@bosrup.com">erik@bosrup.com</a>)<br />
Homepage: <a href="http://www.bosrup.com/web/overlib/" target="_blank">http://www.bosrup.com/web/overlib/</a><br />
This script is published under an open source license.<br />
License: <a href="http://www.bosrup.com/web/overlib/license.html" target="_blank">http://www.bosrup.com/web/overlib/license.html</a><br />
Location: /shared/jscripts/<br />
</p>

<p>
<b>whois2.php</b> (base class to do whois queries with php) is distribuited "as is" without modifications<br />
(except the path in include and require statements).<br />
Homepage: <a href="http://www.easydns.com/~markjr/whois2/" target="_blank">http://www.easydns.com/~markjr/whois2/</a><br />
License: <a href="http://www.gnu.org/copyleft/gpl.html" target="_blank">GPL (GNU GENERAL PUBLIC LICENSE)</a><br />
Location: /admin/whois/<br />
</p>

<p>
<b>PHPMailer</b> (PHP email class) is distribuited "as is" without modifications<br />
Class for sending email using either sendmail, PHP mail(), or SMTP.<br />
Methods are based upon the standard AspEmail(tm) classes.<br />
Author: Brent R. Matzelle (<a href="mailto:bmatzelle@yahoo.com">bmatzelle@yahoo.com</a>)<br />
License: <a href="http://www.gnu.org/copyleft/lesser.html" target="_blank">LGPL (GNU LESSER GENERAL PUBLIC LICENSE)</a><br />
Location: /admin/phpmailer/<br />
</p>


<p>
<b>ZIP class</b> is a modified version of ZIP File Maker 1.1.<br />
Author: Eric Mueller (<a href="mailto:eric@themepark.com">eric@themepark.com</a>)<br />
Homepage: <a href="http://www.zend.com/codex.php?id=696&amp;single=1" target="_blank">http://www.zend.com/codex.php?id=696&amp;single=1</a><br />
Licence: Public Domain<br />
Location: /admin/code/cp_functions_zip.php<br />
</p>

<p>
Some functions of shell explorer has been inspired to:<br />
PHP Explorer 0.5 Alpha version<br />
Author: Marcelo L. Mottalli (<a href="mailto:mottalli@sinectis.com.ar">mottalli@sinectis.com.ar</a>)<br />
Homepage: <a href="http://phpexplorer.sourceforge.net/" target="_blank">http://phpexplorer.sourceforge.net/</a><br />
Licence: Public Domain<br />
Although I have not used his source-code, there are some concepts I've borrowed from him.<br />
Location: /admin/cp/cp_functions_shell.php<br />
</p>

<!-- ====================================================== -->
<?php 

require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
