<?php
//============================================================+
// File name   : cp_authorization.php
// Begin       : 2001-09-26
// Last Update : 2010-10-04
//
// Description : Check user authorization level.
//               Grants / deny access to pages.
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
 * <p>This script handles user's sessions.<br>
 * Just the registered users granted with a username and a password are entitled to access the restricted areas (level > 0) of TCExam and the public area to perform the tests.<br>
 * The user's level is a numeric value that indicates which resources (pages, modules, services) are accessible by the user.<br>
 * To gain access to a specific resource, the user's level must be equal or greater to the one specified for the requested resource.<br>
 * TCExam has 10 predefined user's levels:<ul>
 * <li>0 = anonymous user (unregistered).</li>
 * <li>1 = basic user (registered);</li>
 * <li>2-9 = configurable/custom levels;</li>
 * <li>10 = administrator with full access rights</li>
 * </ul>
 * </p>
 *
 * @package com.tecnick.aiocp.shared
 * @author Nicola Asuni
 * @copyright Copyright &copy; 2004-2006, Tecnick.com LTD - Manor Coach House, Church Hill, Aldershot, Hants, GU12 4RQ, UK - www.tecnick.com - info@tecnick.com
 * @link www.tecnick.com
 * @since 2001-09-26
 */

require_once('../../shared/config/cp_extension.inc');
require_once('../config/cp_config.'.CP_EXT);
require_once('../../shared/code/cp_functions_authorization.'.CP_EXT);
require_once('../../shared/code/cp_functions_session.'.CP_EXT);

$logged = false; // the user is not yet logged in

// --- read existing user's session data from database
$PHPSESSIDSQL = F_aiocpdb_escape_string($PHPSESSID);
$session_hash = md5($PHPSESSID.getClientFingerprint());
$sqls = "SELECT * FROM ".K_TABLE_SESSIONS." WHERE cpsession_id='".$PHPSESSIDSQL."'";
if ($rs = F_aiocpdb_query($sqls, $db)) {
	if ($ms = F_aiocpdb_fetch_array($rs)) { // the user's session already exist
		// decode session data
		session_decode($ms['cpsession_data']);
		// check for possible session hijacking (doesn't work with java applets)
		/*
		if ((!isset($_SESSION['session_hash'])) OR ($_SESSION['session_hash'] != $session_hash)) {
			// display login form
			session_regenerate_id();
			F_login_form();
			exit();
		}
		*/
		// update session expiration time
		$expiry = (time() + K_SESSION_LIFE);
		$sqlx = "UPDATE IGNORE ".K_TABLE_SESSIONS." SET cpsession_expiry='".$expiry."' WHERE cpsession_id='".$PHPSESSIDSQL."'";
		if (!$rx = F_aiocpdb_query($sqlx, $db)) {
			F_display_db_error();
		}
	} else { // session do not exist so, create new anonymous session
		$_SESSION['session_hash'] = $session_hash;
		$_SESSION['session_user_id'] = 1;
		$_SESSION['session_user_name'] = "_A_".substr($PHPSESSID, 12, 8);
		$_SESSION['session_user_ip'] = $_SERVER['REMOTE_ADDR'];
		$_SESSION['session_user_level'] = 0;
		$_SESSION['session_user_language'] = K_DEFAULT_LANGUAGE;
		$_SESSION['session_alt_menu'] = 0; //default alternative menu disabled

		// read client cookie
		if (isset($_COOKIE['LastVisit'])) {
			$_SESSION['session_last_visit'] = intval($_COOKIE['LastVisit']);
		}
		else {
			$_SESSION['session_last_visit'] = 0;
		}

		// set client cookie
		$cookie_now_time = time(); // note: while time() function returns a 32 bit integer, it works fine until year 2038.
		$cookie_expire_time = $cookie_now_time + K_COOKIE_EXPIRE; // set cookie expiration time
		setcookie("LastVisit", $cookie_now_time, $cookie_expire_time, K_COOKIE_PATH, K_COOKIE_DOMAIN, K_COOKIE_SECURE);
		setcookie("PHPSESSID", $PHPSESSID, $cookie_expire_time, K_COOKIE_PATH, K_COOKIE_DOMAIN, K_COOKIE_SECURE);
	}
}
else {
	F_display_db_error();
}

// check if login information has been submitted
if ((isset($_POST['logaction'])) AND ($_POST['logaction'] == "login")) {
	$xuser_password = md5($_POST['xuser_password']); // one-way password encoding
	// check if submitted login information are correct
	$sql = "SELECT * FROM ".K_TABLE_USERS." WHERE user_name='".$_POST['xuser_name']."' AND user_password='".$xuser_password."'";
	if ($r = F_aiocpdb_query($sql, $db)) {
		if ($m = F_aiocpdb_fetch_array($r)) {
			// sets some user's session data
			$_SESSION['session_user_id'] = $m['user_id'];
			$_SESSION['session_user_name'] = $m['user_name'];
			$_SESSION['session_user_ip'] = $_SERVER['REMOTE_ADDR'];
			$_SESSION['session_user_level'] = $m['user_level'];
			$_SESSION['session_user_language'] = $m['user_language'];
			$_SESSION['session_alt_menu'] = 0; //default alternative menu disabled
			// read client cookie
			if (isset($_COOKIE['LastVisit'])) {
				$_SESSION['session_last_visit'] = intval($_COOKIE['LastVisit']);
			}
			else {
				$_SESSION['session_last_visit'] = 0;
			}
			$logged=TRUE;
		}
		else {
			F_print_error("WARNING", $l['m_login_wrong']);
		}
	}
	else {
		F_display_db_error();
	}
}

// If language has been manually changed
if (isset($_REQUEST['choosed_language'])) {
	if (strlen($_REQUEST['choosed_language']) > 3) {
		$_REQUEST['choosed_language'] = "eng";
	}
	$_SESSION['session_user_language'] = $_REQUEST['choosed_language'];
	$selected_language = $_SESSION['session_user_language'];
}

// If a user has choosen alternate menu
if (isset($_REQUEST['altmenu'])) {
	if ($_REQUEST['altmenu']) {
		$_SESSION['session_alt_menu'] = 1; //use alternate menu
	}
	else {
		$_SESSION['session_alt_menu'] = 0; //use standard menu
	}
}

//Load selected language
if (isset($_SESSION['session_user_language'])) {
	$selected_language = $_SESSION['session_user_language'];
}
require('../../shared/code/cp_languages.'.CP_EXT); //reload language file

if (!isset($pagelevel)) {
	// set default page level
	$pagelevel = 0;
}

// check user's level
if ($pagelevel) { // pagelevel=0 means access to anonymous user
	// pagelevel >= 1
	if ($_SESSION['session_user_level'] < $pagelevel) { //check user level
		if ($_SESSION['session_user_id'] == 1) { //actions for anonymous user
			F_login_form(); //display login form
		}
		else {
			//check if user has a special permission to access the requested resource:
			$current_time = gmdate("Y-m-d H:i:s");

			//delete expired special accounts (garbage collector)
			$sql = "DELETE FROM ".K_TABLE_USERS_AUTH." WHERE ua_time_end<'".$current_time."'";
			if (!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}

			// get current page
			$current_page = K_PATH_HOST.$_SERVER['SCRIPT_NAME'];
			if ($_SERVER['QUERY_STRING']) {
				$current_page .= "?".$_SERVER['QUERY_STRING'];
			}

			//check user permission
			$sql = "SELECT * FROM ".K_TABLE_USERS_AUTH." WHERE ua_user_id='".$_SESSION['session_user_id']."' AND ua_time_start<='".$current_time."' AND ua_time_end>='".$current_time."' AND LOCATE(ua_resource,'".$current_page."')>0 LIMIT 1";
			if ($r = F_aiocpdb_query($sql, $db)) {
				if (!F_aiocpdb_fetch_array($r)) {
					F_print_error("WARNING", $l['m_authorization_deny']); //display error message
					F_logout_page();
				}
			}
			else {
				F_display_db_error();
			}
		}
	}
}

if ($logged) { //if user is just logged in: reloads page
	switch(K_REDIRECT_LOGIN_MODE) {
		case 1: {
			// relative redirect
			header("Location: ".$_SERVER['SCRIPT_NAME']);
			break;
		}
		case 2: {
			// absolute redirect
			header("Location: ".K_PATH_HOST.$_SERVER['SCRIPT_NAME']);
			break;
		}
		case 3:
		default: {
			// html redirect
			header("Location: ".K_PATH_HOST.$_SERVER['SCRIPT_NAME']);
			echo "<"."?xml version=\"1.0\" encoding=\"".$l['a_meta_charset']."\"?".">\n";
			echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"DTD/xhtml1-transitional.dtd\">\n";
			echo "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"".$l['a_meta_language']."\" lang=\"".$l['a_meta_language']."\" dir=\"".$l['a_meta_dir']."\">\n";
			echo "<head>\n";
			echo "<title>ENTER</title>\n";
			if (K_CHECK_JAVASCRIPT) {
				//echo "<noscript><meta http-equiv='refresh' content='0;url=".K_REDIRECT_JAVASCRIPT_ERROR."' /></noscript>\n";
				echo "<meta name=\"robots\" content=\"index,follow\" />\n";
			}

			if (K_USE_FRAMES) { //reload all frames from index (if javascript enable)
				echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
				echo "//<![CDATA[\n";
				echo "var mainpage = escape(parent.frames['".K_MAIN_FRAME_NAME."'].location.href);\n";
				echo "top.location.replace(\"../code/index.".CP_EXT."?load_page=\" + mainpage);\n";
				echo "//]]>\n";
				echo "</script>\n";
				echo "</head>\n";
				echo "<body>\n";
				echo "<a href=\"".$_SERVER['SCRIPT_NAME']."\" target=\"_top\">ENTER</a>\n";
			}
			else { //reload page
				echo "<meta http-equiv=\"refresh\" content=\"0\" />\n"; //reload page
				echo "</head>\n";
				echo "<body>\n";
				echo "<a href=\"".$_SERVER['SCRIPT_NAME']."\" target=\"_top\">ENTER</a>\n";
			}
			echo "</body>\n";
			echo "</html>\n";
			break;
		}
	}
	exit;
}

//============================================================+
// END OF FILE
//============================================================+
