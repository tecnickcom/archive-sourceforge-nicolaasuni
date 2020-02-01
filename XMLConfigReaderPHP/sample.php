<?php
//============================================================+
// File name   : sample.php
// Begin       : 2004-10-19
// Last Update : 2005-03-20
//                                                             
// Description : Example for XMLConfigReader Class
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
 * XMLConfigReader class example.
 * @package com.tecnick.xmlconfigreader
 */
 
/**
 * This script istantiates the XMLConfigReader with sample.xml file as data source and prints the resource array content.
 *
 * @name Sample
 * @abstract test script for XMLConfigReader class
 * @link http://xmlcfgreader.sourceforge.net
 * @license http://www.gnu.org/copyleft/lesser.html LGPL
 * @author Nicola Asuni [www.tecnick.com]
 * @copyright Copyright (c) 2004-2005 - Tecnick.com S.r.l (www.tecnick.com) - Via Ugo Foscolo n.19 - 09045 Quartu Sant'Elena (CA) - ITALY - www.tecnick.com - info@tecnick.com
 * @version 1.1.001
 */
 
require_once('XMLConfigReader.php');

$xmlfile = "sample.xml";

// istantiate new XMLConfigReader object
$xmldata = new XMLConfigReader($xmlfile);

// get resources array
$resource = $xmldata->getResource();

// print HTML headers
echo "<"."?"."xml version=\"1.0\" encoding=\"UTF-8\""."?".">\n";
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"DTD/xhtml1-transitional.dtd\">\n";
echo "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\" dir=\"ltr\">\n";
echo "<head>\n";
echo "<title>XMLConfigReader Example</title>\n";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />\n";
echo "</head>\n";
echo "<body>\n";
echo "<h1>XMLConfigReader Example</h1>\n";

// list keys and values
echo "<ul>\n";
while (list($key1, $val1) = each($resource)) {
	while (list($key2, $val2) = each($val1)) {
		echo "<li>\$resource['".$key1."']['".$key2."'] = ".$val2."</li>\n";
	}
}
echo "</ul>\n";

echo "First item key for &lt;description&gt;third element&lt;/description&gt; : ";
echo $xmldata->getKey("description", "third element");

echo "</body></html>";


//============================================================+
// END OF FILE                                                 
//============================================================+
?>
