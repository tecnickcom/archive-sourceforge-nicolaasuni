<?php
//============================================================+
// File name   : cp_config.php
// Begin       : 2001-09-02
// Last Update : 2006-11-27
// 
// Description : ADMIN Base cofiguration file
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
require_once('../config/cp_auth.'.CP_EXT);
require_once('../../shared/config/cp_config.'.CP_EXT);

// -- Language -- 

/**
 * default administration panel language
 */
define ("K_DEFAULT_LANGUAGE", "eng");

// -- Options / COSTANTS --

/**
 * [seconds] Limits the maximum execution time for a script
 */
define ("K_MAX_EXECUTION_TIME", 2*K_SECONDS_IN_HOUR);

/**
 * max memory limit
 */
define ("K_MAX_MEMORY_LIMIT", "128M");

/**
 * max number of rows to display in tables
 */
define ("K_MAX_ROWS_PER_PAGE", 50);

/**
 * number of icons in the same row (for main page)
 */
define ("K_MAX_ICONS_IN_ROW", 7);

/**
 * background image file for overlib tooltips
 */
define ("K_OVERLIB_IMAGE", K_PATH_IMAGES."overlib/description.gif");

/**
 * max size to be uploaded in bytes
 */
define ("K_MAX_UPLOAD_SIZE", 10000000);

/**
 * enable error log (../log/cp_errors.log)
 */
define ("K_USE_ERROR_LOG", TRUE);

/**
 * number of times that link could be unavailable before deleting
 */
define ("K_MIN_CHECK_LINK_TIMES", 3);

/**
 * if true graphics buttons instead normal form buttons
 */
define ("K_USE_GRAPHIC_BUTTONS", FALSE);

/**
 * name of the graphic button style
 */
define ("K_GRAPHIC_BUTTON_STYLE", "AIOCP");

/**
 * if TRUE display form fields description using overlib
 */
define ("K_DISPLAY_QUICK_HELP", TRUE);

/**
 * if TRUE display language selector on AIOCP menu
 */
define ("K_LANG_ON_MENU", FALSE);

/**
 * if true uses frames for AIOCP administration area (do not change)
 */
define ("K_USE_FRAMES", TRUE);

/**
 * name of the main AIOCP frame (do not change)
 */
define ("K_MAIN_FRAME_NAME", "CPMAIN");

/**
 * if true check if the client is javascript-enabled
 */
define ("K_CHECK_JAVASCRIPT", TRUE);

/**
 * Javascript error page
 */
define ("K_REDIRECT_JAVASCRIPT_ERROR", "../code/cp_javascript_error.".CP_EXT);

/**
 * directory names excluded from backup process (separated by comma)
 */
define ("K_BACKUP_EXCLUDE", "admin/backup,cache,install");

// -- DEFAULT META and BODY Tags --

/**
 * default page title for administration area
 */
define ("K_AIOCP_TITLE", "All In One Control Panel");

/**
 * default page description for administration area
 */
define ("K_AIOCP_DESCRIPTION", "All In One Control Panel by Tecnick.com");

/**
 * default page author for administration area
 */
define ("K_AIOCP_AUTHOR", "Nicola Asuni - Tecnick.com LTD");

/**
 * default "reply-to" meta tag for administration area
 */
define ("K_AIOCP_REPLY_TO", "");

/**
 * default page keywords for administration area
 */
define ("K_AIOCP_KEYWORDS", "Control Panel, Server, Functions, Menu");

/**
 * default AIOCP icon
 */
define ("K_AIOCP_ICON", "../favicon.ico");

/**
 * CSS stylesheet for AIOCP administration area
 */
define ("K_AIOCP_STYLE", K_PATH_STYLE_SHEETS."aiocp.css");

/**
 * CSS stylesheet for AIOCP help
 */
define ("K_AIOCP_HELP_STYLE", K_PATH_STYLE_SHEETS."aiocp_help.css");

/**
 * if true display admin clock in UTC (GMT)
 */
define ("K_CLOCK_IN_UTC", true);

/**
 * if true display enable Javascript popups for errors
 */
define ("K_ENABLE_JSERRORS", false);

/**
 * set the default email message
 */
define ("K_DEAFULT_EMAIL_MSG","<"."?xml version=\"1.0\" encoding=\"iso-8859-1\"?".">
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">
<body>
<!-- START OF MESSAGE BODY -->

<!-- END OF MESSAGE BODY -->
</body>
</html>");

// -- INCLUDE files -- 
require_once('../../admin/code/cp_functions_errmsg.'.CP_EXT);
require_once('../../shared/config/cp_db_config.'.CP_EXT);
require_once('../../shared/code/cp_db_connect.'.CP_EXT);
require_once('../../shared/code/cp_functions_general.'.CP_EXT);
require('../../shared/code/cp_languages.'.CP_EXT);
require_once('../../shared/code/cp_currency.'.CP_EXT);

ini_set("memory_limit", K_MAX_MEMORY_LIMIT); //set memory limit
ini_set("session.use_trans_sid", 0); //if =1 use PHPSESSID 
//set_time_limit(K_MAX_EXECUTION_TIME); //Limit the maximum execution time

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
