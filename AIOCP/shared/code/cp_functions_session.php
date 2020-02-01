<?php
//============================================================+
// File name   : cp_functions_session.php
// Begin       : 2001-09-26
// Last Update : 2011-10-29
//
// Description : User-level session storage functions.
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
 * User-level session storage functions.<br>
 * This script uses the session_set_save_handler() function to set the user-level session storage functions which are used for storing and retrieving data associated with a session.<br>
 * The session data is stored on a local database.
 * NOTE: This script automatically starts the user's session.
 * @package com.tecnick.aiocp.shared
 * @author Nicola Asuni
 * @copyright Copyright &copy; 2001-2006, Tecnick.com LTD - Manor Coach House, Church Hill, Aldershot, Hants, GU12 4RQ, UK - www.tecnick.com - info@tecnick.com
 * @link www.tecnick.com
 * @since 2001-09-26
 */

//database handler
global $db;

// PHP session settings
ini_set('session.save_handler', 'user');
ini_set('session.name', 'PHPSESSID');
ini_set('session.gc_maxlifetime', K_SESSION_LIFE);
//ini_set('session.cookie_lifetime', K_SESSION_LIFE);
ini_set('session.use_cookies', TRUE);
//ini_set('"session.cache_limiter', 'private_no_expire');

/**
 * Open session.
 * @param string $save_path path were to store session data
 * @param string $session_name name of session
 * @return bool always TRUE
 */
function F_session_open($save_path, $session_name) {
	return true;
}

/**
 * Close session.<br>
 * Call garbage collector function to remove expired sessions.
 * @return bool always TRUE
 */
function F_session_close() {
	F_session_gc(); //call garbage collector
	return true;
}

/**
 * Get session data.
 * @param string $key session ID.
 * @return string session data.
 */
function F_session_read($key) {
	global $db;
	$sql = "SELECT cpsession_data
			FROM ".K_TABLE_SESSIONS."
			WHERE cpsession_id='".$key."'
				AND cpsession_expiry>=".time()."
			LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			return $m['cpsession_data'];
		}
		else return('');
	}
	return('');
}

/**
 * Insert or Update session.
 * @param string $key session ID.
 * @param string $val session data.
 * @return resource database query result.
 */
function F_session_write($key, $val) {
	global $db;
	$val = stripslashes($val);
	$expiry = (time() + K_SESSION_LIFE);
	// check if this session already exist on database
	$sql = "SELECT cpsession_id
			FROM ".K_TABLE_SESSIONS."
			WHERE cpsession_id='".$key."'
			LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			// SQL to update existing session
			$sqlup = "UPDATE ".K_TABLE_SESSIONS." SET
				cpsession_expiry=".$expiry.",
				cpsession_data='".$val."'
				WHERE cpsession_id='".$key."'";
		}
		else {
			// SQL to insert new session
			$sqlup = "INSERT INTO ".K_TABLE_SESSIONS."
					(cpsession_id, cpsession_expiry, cpsession_data)
					VALUES ('".$key."',".$expiry.",'".$val."')";
		}
	}
	return F_aiocpdb_query($sqlup, $db);
}

/**
 * Deletes the specific session.
 * @param string $key session ID of session to destroy.
 * @return resource database query result.
 */
function F_session_destroy($key) {
	global $db;
	$sql = "DELETE FROM ".K_TABLE_SESSIONS." WHERE cpsession_id='".$key."'";
	return F_aiocpdb_query($sql, $db);
}

/**
 * Garbage collector.<br>
 * Deletes expired sessions.<br>
 * NOTE: while time() function returns a 32 bit integer, it works fine until year 2038.
 * @return int number of deleted sessions.
 */
function F_session_gc() {
	global $db;
	$sql = "DELETE FROM ".K_TABLE_SESSIONS." WHERE cpsession_expiry<=".time()."";
	if(!$r = F_aiocpdb_query($sql, $db)) {
		return FALSE;
	}
	return F_aiocpdb_affected_rows($db, $r);
}

/**
 * Convert encoded session string data to array.
 * @author Nicola Asuni
 * @copyright Copyright &copy; 2004-2006, Tecnick.com LTD - Manor Coach House, Church Hill, Aldershot, Hants, GU12 4RQ, UK - www.tecnick.com - info@tecnick.com
 * @link www.tecnick.com
 * @since 2001-10-18
 * @param string $sd input data string
 * @return array
 */
function F_session_string_to_array($sd) {
	$sess_array = array();
	$vars = preg_split('/[;}]/', $sd);
	for ($i=0; $i < count($vars)-1; $i++) {
		$parts = explode('|', $vars[$i]);
		$key = $parts[0];
		$val = unserialize($parts[1].";");
		$sess_array[$key] = $val;
	}
	return $sess_array;
}

/**
 * Generate a client fingerprint (unique ID for the client browser)
 * @author Nicola Asuni
 * @copyright Copyright © 2004-2010, Nicola Asuni - Tecnick.com LTD - UK - www.tecnick.com - info@tecnick.com
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @link www.tecnick.com
 * @since 2010-10-04
 * @return string client ID
 */
function getClientFingerprint() {
	$sid = K_RANDOM_SECURITY;
	if (isset($_SERVER['REMOTE_ADDR'])) {
		$sid .= $_SERVER['REMOTE_ADDR'];
	}
	if (isset($_SERVER['HTTP_USER_AGENT'])) {
		$sid .= $_SERVER['HTTP_USER_AGENT'];
	}
	if (isset($_SERVER['HTTP_ACCEPT'])) {
		$sid .= $_SERVER['HTTP_ACCEPT'];
	}
	if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
		$sid .= $_SERVER['HTTP_ACCEPT_ENCODING'];
	}
	if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
		$sid .= $_SERVER['HTTP_ACCEPT_LANGUAGE'];
	}
	if (isset($_SERVER['HTTP_ACCEPT_CHARSET'])) {
		$sid .= $_SERVER['HTTP_ACCEPT_CHARSET'];
	}
	return $sid;
}

/**
 * Generate and return a new session ID.
 * @author Nicola Asuni
 * @copyright Copyright © 2004-2010, Nicola Asuni - Tecnick.com LTD - UK - www.tecnick.com - info@tecnick.com
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @link www.tecnick.com
 * @since 2010-10-04
 * @return string PHPSESSID
 */
function getNewSessionID() {
	return md5(uniqid(microtime().getmypid(), true).getClientFingerprint().uniqid(session_id().microtime(), true));
}

// ------------------------------------------------------------

// Sets user-level session storage functions.
session_set_save_handler('F_session_open', 'F_session_close', 'F_session_read', 'F_session_write', 'F_session_destroy', 'F_session_gc');

// start user session
if (isset($_REQUEST['PHPSESSID'])) {
	// sanitize $PHPSESSID from get/post/cookie
	$PHPSESSID = preg_replace('/[^0-9a-f]*/', '', $_REQUEST['PHPSESSID']);
	if (strlen($PHPSESSID) != 32) {
		// generate new ID
		$PHPSESSID = getNewSessionID();
	}
} else {
	// create new PHPSESSID
	$PHPSESSID = getNewSessionID();
}

if ((!isset($_REQUEST['menu_mode'])) OR ($_REQUEST['menu_mode'] != 'startlongprocess')) {
	// fix flush problem on long processes
	session_id($PHPSESSID); //set session id
}

session_start(); //start session
header('Cache-control: private'); // fix IE6 bug

//============================================================+
// END OF FILE
//============================================================+
