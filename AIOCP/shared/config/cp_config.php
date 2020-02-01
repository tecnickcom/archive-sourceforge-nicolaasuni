<?php
//============================================================+
// File name   : cp_config.php
// Begin       : 2002-02-24
// Last Update : 2014-03-24
//
// Description : SHARED cofiguration file
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

// -- INCLUDE files --
require_once('../../shared/config/cp_extension.inc');
require_once('../../shared/config/cp_paths.'.CP_EXT);
require_once('../../shared/config/cp_general_constants.'.CP_EXT);
require_once('../../shared/config/cp_ecommerce.'.CP_EXT);
require_once('../../shared/config/tcpdf_config.'.CP_EXT);

// -- Options / COSTANTS --

/**
 * AIOCP version
 */
define ('K_AIOCP_VERSION', '1.4.003');

/**
 * allowed email for formail module
 */
define ('K_CONTACT_US_EMAIL', '');

/**
 * user session life time in seconds (without actions)
 */
define ('K_SESSION_LIFE', K_SECONDS_IN_HOUR);

/**
 * max shopping cart entry life time in seconds
 */
define ('K_SHOPPING_CART_LIFE', K_SECONDS_IN_DAY);

/**
 * chat default refresh time in seconds
 */
define ('K_CHAT_REFRESH_TIME', 20);

/**
 * seconds of live time for a chat message
 */
define ('K_CHAT_LIVE_TIME', 600);

/**
 * seconds of live time for a chat user
 */
define ('K_CHAT_USER_LIVE_TIME', 90);

/**
 * max number of days to wait user verification (after registration request)
 */
define ('K_MAX_WAIT_VERIFICATION', 15);


/**
 * enable bad word censor
 */
define ('K_BAD_WORD_CENSOR', TRUE);

/**
 * if true display flags for language selection from F_choose_page_language().Flags URL must be defined on x_flag language template.
 */
define ('K_SHOW_LANG_FLAGS', true);

/**
 * Minimum level to be a Forum Moderator
 */
define ('K_AUTH_MIN_MODERATOR_LEVEL', 5);

/**
 * Minimum user level that may access to user editor
 */
define ('K_AUTH_EDIT_USER_LEVEL', 10);

/**
 * Minimum user level that may access to product editor
 */
define ('K_AUTH_EDIT_PRODUCTS_LEVEL', 10);

/**
 * Minimum user level that may access to banner stats
 */
define ('K_AUTH_BANNER_STATS_LEVEL', 10);

/**
 * Minimum user level that could see the edit link on public pages
 */
define ('K_AUTH_EDIT_PAGES_LEVEL', 10);

//images settings

/**
 * set to true if you are using GD2 graphic library (enable true colors for generated images)
 */
define ('K_USE_GD2', true);

/**
 * max size for user image in bytes
 */
define ('K_MAX_USER_IMAGE_SIZE', 500000);

/**
 * height for user image in pixel
 */
define ('K_USER_IMAGE_HEIGHT', 100);

/**
 * width for user image in pixel
 */
define ('K_USER_IMAGE_WIDTH', 100);

/**
 * height for product small image in pixel
 */
define ('K_PRODUCT_IMAGE_HEIGHT', 100);

/**
 * width for product small image in pixel
 */
define ('K_PRODUCT_IMAGE_WIDTH', 100);

/**
 * height for manufacturer logo in pixel
 */
define ('K_MANUFACTURER_IMAGE_HEIGHT', 100);

/**
 * width for manufacturer logo in pixel
 */
define ('K_MANUFACTURER_IMAGE_WIDTH', 100);

/**
 * default blank image filename
 */
define ('K_BLANK_IMAGE', '_blank.png');

//defaul background color for generated thumbnails

/**
 * red value of background color (0-255)
 */
define ('K_IMAGE_BACKGROUND_R', 255);

/**
 * green value of background color (0-255)
 */
define ('K_IMAGE_BACKGROUND_G', 255);
/**
 * blue value of background color (0-255)
 */
define ('K_IMAGE_BACKGROUND_B', 255);

// Client Cookie settings

/**
 * domain for cookies
 */
define ('K_COOKIE_DOMAIN', '');

/**
 * path for cookies
 */
define ('K_COOKIE_PATH', '/');

/**
 * If true uses secure cookies
 */
define ('K_COOKIE_SECURE', FALSE);

/**
 * cookie expire time (one year)
 */
define ('K_COOKIE_EXPIRE', K_SECONDS_IN_YEAR);

/**
 * 1,2,3 various pages redirection modes after login
 */
define ('K_REDIRECT_LOGIN_MODE', 3);

/**
 * default user password length (for genenerated passwords)
 */
define ('K_PASSWORD_LENGTH', 8);

/**
 * define timestamp format using PHP notation
 */
define ('K_TIMESTAMP_FORMAT', 'Y-m-d H:i:s');

/**
 * error reporting for PHP
 */
define ('K_ERROR_TYPES', E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE);
// use the following line for dubug
//define ('K_ERROR_TYPES', E_ALL | E_STRICT);

/**
 * define error reporting level
 */
error_reporting(K_ERROR_TYPES);

global $selected_language;

ini_set('arg_separator.output', '&amp;');
//date_default_timezone_set(K_TIMEZONE);

if(!defined('PHP_VERSION_ID')) {
	$version = PHP_VERSION;
	define('PHP_VERSION_ID', (($version{0} * 10000) + ($version{2} * 100) + $version{4}));
}
if (PHP_VERSION_ID < 50300) {
	@set_magic_quotes_runtime(false); //disable magic quotes
	ini_set('magic_quotes_gpc', 'On');
	ini_set('magic_quotes_runtime', 'Off');
	ini_set('register_long_arrays', 'On');
	//ini_set('register_globals', 'On');
}

// --- get 'post', 'get' and 'cookie' variables
foreach ($_REQUEST as $postkey => $postvalue) {
	if (($postkey{0} != '_') AND (!preg_match('/[A-Z]/', $postkey{0}))) {
		if (!function_exists('get_magic_quotes_gpc') OR !get_magic_quotes_gpc()) {
			$postvalue = addSlashesArray($postvalue);
			$_REQUEST[$postkey] = $postvalue;
			if (isset($_GET[$postkey])) {
				$_GET[$postkey] = $postvalue;
			} elseif (isset($_POST[$postkey])) {
				$_POST[$postkey] = $postvalue;
			} elseif (isset($_COOKIE[$postkey])) {
				$_COOKIE[$postkey] = $postvalue;
			}
		}
		$$postkey = $postvalue;
	}
}

/**
 * Escape strings with backslashes before characters that need to be escaped.
 * These characters are single quote ('), double quote ("), backslash (\) and NUL (the NULL byte). 
 * @param $data (array|string) String or array to escape
 * @return array|string
 */
function addSlashesArray($data) {
	if (is_array($data)) {
		return array_map('addSlashesArray', $data);
	}
	if (is_string($data)) {
		return addslashes($data);
	}
	return $data;
}

//============================================================+
// END OF FILE
//============================================================+
