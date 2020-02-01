<?php
//============================================================+
// File name   : page_break.php
// Begin       : 2003-11-27
// Last Update : 2003-11-29
//
// Description : Example of PHP page to handle requests by the 
//               JRelaxTimer applet.
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


// get http parameters (post or get)
$t = F_getParameter("l", "0"); // applet license number
$t = F_getParameter("t", "0"); // type of break
$b = F_getParameter("b", "0"); // current break
$i = F_getParameter("i", "0"); // interval in minutes

// -------------------------------------------------------------
// Insert here your methods to display the apropriate content...

echo "<h1>License number: ".$l."</h1>";
echo "<h1>Break Type: ".$t."</h1>";
echo "<h1>Current break: ".$b."</h1>";
echo "<h1>Interval: ".$i." [minutes]</h1>";
// -------------------------------------------------------------

//=============================================================

/**
 * Get the specified http parameter passed via post or get.
 * @param string $var_name the parameter name
 * @param mixed $default_value the default value
 * @access private
 * @return mixed the parameter value
 */
function F_getParameter($var_name, $default_value = "") {
	if ( isset($_REQUEST[$var_name]) AND $_REQUEST[$var_name]) {
		return $_REQUEST[$var_name];
	}
	return $default_value;
}

//============================================================+
// END OF FILE
//============================================================+
?>
