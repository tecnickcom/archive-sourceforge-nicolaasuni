<?php
//============================================================+
// File name   : sample.php
// Begin       : 2004-10-19
// Last Update : 2005-03-20
//                                                             
// Description : Example for XHTMLTranscoder Class
// Platform    : PHP 5
//
// Author: Nicola Asuni
//                                                             
// (c) Copyright:
//               Tecnick.com S.r.l.
//               Via Ugo Foscolo n.19
//               09045 Quartu Sant'Elena (CA)
//               ITALY
//               www.tecnick.com
//               info@tecnick.com                                                            
//============================================================+

/**
 * XHTMLTranscoder class example.
 * @package com.tecnick.xhtmltranscoder
 */
 
/**
 * This script istantiates the XMLConfigReader with sample.xml file as data source and prints the resource array content.
 *
 * @name Sample
 * @abstract test script for XHTMLTranscoder class
 * @link http://xhtmlvalidator.sourceforge.net
 * @license http://www.gnu.org/copyleft/lesser.html LGPL
 * @author Nicola Asuni [www.tecnick.com]
 * @copyright Copyright (c) 2004-2005 - Tecnick.com S.r.l (www.tecnick.com) - Via Ugo Foscolo n.19 - 09045 Quartu Sant'Elena (CA) - ITALY - www.tecnick.com - info@tecnick.com
 * @version 1.0.000
 */

require_once('XHTMLTranscoder.php');

$xhtml = new XHTMLTranscoder();

// print HTML headers
$testcode  =  "<"."?"."xml version=\"1.0\" encoding=\"UTF-8\""."?".">\n";
$testcode .=  "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"DTD/xhtml1-transitional.dtd\">\n";
$testcode .=  "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\" dir=\"ltr\">\n";
$testcode .=  "<head>\n";
$testcode .=  "<title>XMLConfigReader Example</title>\n";
$testcode .=  "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />\n";
$testcode .=  "</head>\n";
$testcode .=  "<body>\n";
$testcode .=  "<h1>XHTMLTranscoder Example</h1>\n";
$testcode .=  "tags (elements) names in lowercase: <B>bold</B><br />\n";	
$testcode .=  "attributes names in lowercase: <a HREF=\"http://www.tecnick.com\">link</a><br />\n";	
$testcode .=  "elements nesting: <b>bold<i>italic</b></i><br />\n";	
$testcode .=  "elements termination: <b>bold<i>italic</b><br />\n";	
$testcode .=  "unquoted attributes: <a href=http://www.tecnick.com>link</a><br />\n";	
$testcode .=  "unminimized attributes: <input type=\"checkbox\" checked /><br />\n";	
$testcode .=  "unterminated empty tags: <br><br />\n";	
$testcode .=  "Extended characters with entities_off = false: àèìòù € &euro; &#8364; &#x20AC; & &amp;<br />\n";
$testcode .=  "preserve other languages elements (php, asp, jsp, ...):\n";	
$testcode .=  "<script language=\"JavaScript\" type=\"text/javascript\">\n";
$testcode .=  "//<![CDATA[\n";
$testcode .=  "alert('TEST JAVASCRIPT');\n";
$testcode .=  "//]]>\n";
$testcode .=  "</script>\n";
$testcode .=  "</body>\n";
$testcode .=  "</html>";

//$testcode = $xhtml->unhtmlentities($testcode, true);
//$testcode = $xhtml->htmlentitiesUTF8($testcode);
//echo $testcode;

echo $xhtml->transcode($testcode, true, false, "UTF-8");


//============================================================+
// END OF FILE                                                 
//============================================================+
?>
