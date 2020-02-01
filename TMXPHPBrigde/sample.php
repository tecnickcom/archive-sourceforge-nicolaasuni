<?php
//============================================================+
// File name   : sample.php
// Begin       : 2004-10-19
// Last Update : 2006-08-01
// 
// Description : TMX PHP Bridge Class
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
 * TMXResourceBundle class example.
 * @package com.tecnick.tmxphpbridge
 */

/**
 * This script istantiates the TMXResourceBundle with sample_tmx.xml as TMX data source and prints the resource array content.
 *
 * @name Sample
 * @abstract test script for TMXResourceBundle class
 * @link http://tmxphpbridge.sourceforge.net
 * @license http://www.gnu.org/copyleft/lesser.html LGPL
 * @author Nicola Asuni [www.tecnick.com]
 * @copyright Copyright (c) 2004-2006 - Tecnick.com S.r.l (www.tecnick.com) - Via Ugo Foscolo n.19 - 09045 Quartu Sant'Elena (CA) - ITALY - www.tecnick.com - info@tecnick.com
 * @version 1.1.005
 */
 
require_once('TMXResourceBundle.php');

$tmxfile = "sample.xml";

// istantiate new TMXResourceBundle objects
$tmx_en = new TMXResourceBundle($tmxfile, "en", "cache_en.php"); // english
$tmx_it = new TMXResourceBundle($tmxfile, "it", "cache_it.php"); // italian

// get language arrays
$l_en = $tmx_en->getResource(); // language array for english
$l_it = $tmx_it->getResource(); // language array for italian

echo "<"."?"."xml version=\"1.0\" encoding=\"UTF-8\""."?".">\n";
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"DTD/xhtml1-transitional.dtd\">\n";
echo "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\" dir=\"ltr\">\n";
echo "<head>\n";
echo "<title>XMLConfigReader Example</title>\n";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />\n";
echo "</head>\n";
echo "<body>\n";
echo "<h1>TMXResourceBundle Example</h1>\n";

echo "<h2>English:</h2>\n";
// list keys and values
echo "<ul>\n";
while (list($key, $val) = each($l_en)) {
	echo "<li>\$tmx-&gt;resource['".$key."'] = ".$val."</li>\n";
}
echo "</ul>\n";

echo "<h2>Italian:</h2>\n";
// list keys and values
echo "<ul>";
while (list($key, $val) = each($l_it)) {
	echo "<li>\$tmx-&gt;resource['".$key."'] = ".$val."</li>\n";
}
echo "</ul>\n";
echo "</body></html>";


//============================================================+
// END OF FILE                                                 
//============================================================+
?>
