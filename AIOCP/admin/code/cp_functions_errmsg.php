<?php
//============================================================+
// File name   : cp_functions_errmsg.php                       
// Begin       : 2001-09-17                                    
// Last Update : 2008-07-06
// 
// Description : handle error messages
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

/**
 * Handle error/warning/system messages.<br>
 * messagetype:
 * <ul>
 * <li>message</li>
 * <li>warning</li>
 * <li>error</li>
 * </ul>
 * @package com.tecnick.tcexam.shared
 * @author Nicola Asuni
 * @copyright Copyright &copy; 2004-2006, Tecnick.com LTD - Manor Coach House, Church Hill, Aldershot, Hants, GU12 4RQ, UK - www.tecnick.com - info@tecnick.com
 * @link www.tecnick.com
 * @since 2001-09-17
 */

/**
 * Handle error/warning/system messages.
 * Print a message
 * @param string messagetype:  0=no message, message; warning; error.
 * @param string messagetoprint message to print.
 * @param bool $exit if true output a message and terminate the current script [default = false].
 */
function F_print_error($messagetype="MESSAGE", $messagetoprint="", $exit=false) {
	require_once('../config/cp_config.php');
	global $l;
	
	$messagetype = strtolower($messagetype);
	
	//message is appended to the log file
	if(K_USE_ERROR_LOG AND (!strcmp($messagetype,"error"))) {
		$logsttring = date(K_TIMESTAMP_FORMAT)."\t";
		if (isset($_SESSION['session_user_id'])) {
			$logsttring .= $_SESSION['session_user_id'];
		}
		$logsttring .= "\t";
		if (isset($_SESSION['session_user_ip'])) {
			$logsttring .= $_SESSION['session_user_ip'];
		}
		$logsttring .= "\t";
		$logsttring .= $messagetype."\t";
		$logsttring .= $_SERVER['SCRIPT_NAME']."\t";
		$logsttring .= $messagetoprint."\r\n";
		error_log($logsttring, 3, "../log/cp_errors.log");
	}
	
	if(strlen($messagetoprint) > 0) {
		switch($messagetype) {
			case "message":{
				$msgtitle = $l['t_message'];
				break;
			}
			case "warning":{
				$msgtitle = $l['t_warning'];
				break;
			}
			case "error":{
				$msgtitle = $l['t_error'];
				break;
			}
			default: {//no message
				$msgtitle = $messagetype;
				break;
			}
		}
		
		echo "<div class=\"".$messagetype."\">";
		echo "".$msgtitle.": ";
		echo "".$messagetoprint."";
		echo "</div>\n";
		
		if (K_ENABLE_JSERRORS) {
			//display message on JavaScript Alert Window.
			echo "<script type=\"text/javascript\">\n";
			echo "//<![CDATA[\n";
			$messagetoprint = unhtmlentities(strip_tags($messagetoprint));
			$messagetoprint = str_replace("'", "\'", $messagetoprint);
			echo "alert('[".$msgtitle."]: ".$messagetoprint."');\n";
			echo "//]]>\n";
			echo "</script>\n";
		}
	}
	if ($exit) {
		exit(); // terminate the current script
	}
}

/**
 * Print the database error message.
 * @param bool $exit if true output a message and terminate the current script [default = true].
 * @uses F_print_error
 */
function F_display_db_error($exit=true) {
	$messagetype = "ERROR";
	$messagetoprint = "".F_aiocpdb_error()."";
	F_print_error($messagetype, $messagetoprint, $exit);
}

/**
 * Custom PHP error handler function.
 * @param int $errno The first parameter, errno, contains the level of the error raised, as an integer. 
 * @param string $errstr The second parameter, errstr, contains the error message, as a string. 
 * @param string $errfile The third parameter is optional, errfile, which contains the filename that the error was raised in, as a string. 
 * @param int $errline The fourth parameter is optional, errline, which contains the line number the error was raised at, as an integer. 
 * @uses F_print_error
 */
function F_error_handler($errno, $errstr, $errfile, $errline) {
	$messagetoprint = "[".$errno."] ".$errstr. " | LINE: ".$errline." | FILE: ".$errfile."";
	switch ($errno) { 
		case E_ERROR:
		case E_USER_ERROR: {
			F_print_error("ERROR", $messagetoprint, true);
			break;
		}
		case E_WARNING:
		case E_USER_WARNING: {
			F_print_error("ERROR", $messagetoprint, false);
			break;
		}
		case E_NOTICE:
		case E_USER_NOTICE:
		default: {
			F_print_error("WARNING", $messagetoprint, false);
			break;
		}
	}
}

// Set the custom error handler function 
$old_error_handler = set_error_handler("F_error_handler", K_ERROR_TYPES);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
