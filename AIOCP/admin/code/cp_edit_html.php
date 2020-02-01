<?php
//============================================================+
// File name   : cp_edit_html.php                              
// Begin       : 2001-10-26                                    
// Last Update : 2005-06-29                                    
//                                                             
// Description : HTML Editor                                   
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_HTML;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

require_once('../code/cp_functions_htmleditor.'.CP_EXT);

$thispage_title = $l['t_xhtml_editor'];

//get variables
if (isset($_REQUEST['callingform'])) {$callingform = $_REQUEST['callingform'];}
else {$callingform = false;}
if (isset($_REQUEST['callingfield'])) {$callingfield = $_REQUEST['callingfield'];}
else {$callingfield = false;}
if (isset($_REQUEST['templates'])) {$templates = $_REQUEST['templates'];}
else {$templates = false;}
if (isset($_REQUEST['charset'])) {$doc_charset = $_REQUEST['charset'];}
else {$doc_charset = $l['a_meta_charset'];}

require_once('../code/cp_page_header_popup.'.CP_EXT);
F_html_editor($callingform, $callingfield, $templates, $doc_charset);
require_once('../code/cp_page_footer_popup.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
