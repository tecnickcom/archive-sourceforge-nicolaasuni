<?php
//============================================================+
// File name   : cp_config.php
// Begin       : 2001-10-23
// Last Update : 2008-07-06
// 
// Description : CLIENT Base cofiguration file
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
require_once('../../shared/config/cp_config.'.CP_EXT);

// -- Language -- 

/**
 * default public site language (3 letters code)
 */
define ("K_DEFAULT_LANGUAGE", "eng"); //

// -- DEFAULT META Tags --

/**
 * default site name
 */
define ("K_SITE_TITLE", "Site name");

/**
 * default site description
 */
define ("K_SITE_DESCRIPTION", "Default Client Site");

/**
 * default site author
 */
define ("K_SITE_AUTHOR", "Nicola Asuni - Tecnick.com LTD");

/**
 * default page reply email
 */
define ("K_SITE_REPLY", "");

/**
 * default keywords
 */
define ("K_SITE_KEYWORDS", "Control Panel, Server, Functions, Menu");

/**
 * default icon
 */
define ("K_SITE_ICON", "../favicon.ico");

/**
 * default stylesheet
 */
define ("K_SITE_STYLE", K_PATH_STYLE_SHEETS."default.css");

// -- Options / COSTANTS --

/**
 * max number of rows to display in tables
 */
define ("K_MAX_ROWS_PER_PAGE", 20);

/**
 * max size to be uploaded in bytes
 */
define ("K_MAX_UPLOAD_SIZE", 1000000);

/**
 * enable error log (../log/cp_errors.log)
 */
define ("K_USE_ERROR_LOG", FALSE);

/**
 * [seconds] Limits the maximum execution time for a script
 */
define ("K_MAX_EXECUTION_TIME", 180);

/**
 * if TRUE display form fields description using overlib
 */
define ("K_DISPLAY_QUICK_HELP", TRUE);

/**
 * if TRUE collect site statistics data
 */
define ("K_USE_SITE_STATISTICS", TRUE);

/**
 * if TRUE display language selector on public menu
 */
define ("K_LANG_ON_MENU", TRUE);

/**
 * max memory limit for a PHP script
 */
define ("K_MAX_MEMORY_LIMIT", "32M");


/**
 * main page (homepage)
 */
define ("K_MAIN_PAGE", "cp_dpage.".CP_EXT."?aiocp_dp=_main");

// -- Frame settings ---

/**
 * set to TRUE if your site use frames
 */
define ("K_USE_FRAMES", FALSE);

/**
 * name of the main frame (central frame)
 */
define ("K_MAIN_FRAME_NAME", "_top");

/**
 * set to TRUE to check if client browser support javascript
 */
define ("K_CHECK_JAVASCRIPT", FALSE);

/**
 * if true display enable Javascript popups for errors
 */
define ("K_ENABLE_JSERRORS", false);

/**
 * page to be redirected if Javascript is not supported
 */
define ("K_REDIRECT_JAVASCRIPT_ERROR", "../code/cp_javascript_error.".CP_EXT);

//disable graphic buttons in xhtml basic mode
if (isset($_REQUEST['xhtmlb'])) {
	define ("K_USE_GRAPHIC_BUTTONS", FALSE);
}
else {
	define ("K_USE_GRAPHIC_BUTTONS", FALSE); // if true graphics buttons instead normal form buttons
	define ("K_GRAPHIC_BUTTON_STYLE", "default"); // name of the graphic button style
}

// -- INCLUDE files --

require_once('../../public/code/cp_functions_errmsg.'.CP_EXT);
require_once('../../shared/config/cp_db_config.'.CP_EXT);
require_once('../../shared/code/cp_db_connect.'.CP_EXT);
require_once('../../shared/code/cp_functions_general.'.CP_EXT);
require('../../shared/code/cp_languages.'.CP_EXT);
require_once('../../shared/code/cp_currency.'.CP_EXT);

//set_time_limit(K_MAX_EXECUTION_TIME); //Limit the maximum execution time
ini_set("memory_limit", K_MAX_MEMORY_LIMIT); //set memory limit
ini_set("session.use_trans_sid", 0); //if =1 use PHPSESSID (for clients that do not support cookies)

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
