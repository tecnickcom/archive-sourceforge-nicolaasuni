<?php
//============================================================+
// File name   : cp_paths.php
// Begin       : 2002-01-15
// Last Update : 2008-05-30
// 
// Description : Cofiguration file for paths
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
 * Configuration file for files and directories paths.
 * @package com.tecnick.aiocp.shared
 * @author Nicola Asuni
 * @copyright Copyright &copy; 2004-2006, Tecnick.com LTD - Manor Coach House, Church Hill, Aldershot, Hants, GU12 4RQ, UK - www.tecnick.com - info@tecnick.com
 * @link www.tecnick.com
 * @since 2002-01-15
 */

/**
 * host URL (e.g.: "http://www.yoursite.com")
 */
define ("K_PATH_HOST", "http://localhost");

/**
 * relative URL where this program is installed (e.g.: "/")
 */
define ("K_PATH_AIOCP", "/_OPENSOURCE/AIOCP/");

/**
 * real full path where this program is installed (e.g: "/usr/local/apache/htdocs/AIOCP/")
 */
define ("K_PATH_MAIN", "/var/www/_OPENSOURCE/AIOCP/");
				
/**
 * real full path where database data files are stored (needed for backup - leave void to not backup data) (e.g.: /var/lib/mysql/AIOCP/)
 */
define ("K_PATH_DATABASE_DATA", "");

/**
 * standard port
 */
define ("K_STANDARD_PORT", 80);

// ----------------------------------------

/**
 * public code
 */
define ("K_PATH_PUBLIC_CODE", K_PATH_HOST.K_PATH_AIOCP."public/code/");

/**
 * server path of public code
 */
define ("K_PATH_PUBLIC_CODE_REAL", K_PATH_MAIN."public/code/");

/**
 * starting directory for search indexing
 */
define ("K_PATH_SEARCH_START", K_PATH_PUBLIC_CODE);

/**
 * starting page for search indexing
 */
define ("K_PATH_SEARCH_FIRST_PAGE", K_PATH_SEARCH_START."cp_dpage.php?aiocp_dp=_main");

/**
 * cache directory for temporary files (full path)
 */
define ("K_PATH_CACHE", K_PATH_MAIN."cache/");

/**
 * cache directory for temporary files (url path)
 */
define ("K_PATH_URL_CACHE", K_PATH_AIOCP."cache/");

/**
 * full font path
 */
define ("K_PATH_FONTS", K_PATH_MAIN."fonts/");

//putenv("GDFONTPATH=".K_PATH_FONTS); //set GD library font path for GD

/**
 * relative path to CSS dir
 */
define ("K_PATH_STYLE_SHEETS", "../styles/");

/**
 * relative path to javascript files
 */
define ("K_PATH_JSCRIPTS", "../jscripts/");

/**
 * relative path to shared javascript files
 */
define ("K_PATH_SHARED_JSCRIPTS", "../../shared/jscripts/");

/**
 * use "tracert" for windows and "/usr/sbin/traceroute" for unix
 */
define ("K_PATH_TRACEROUTE", "tracert");

/**
 * directory path for backups
 */
define ("K_PATH_FILES_BACKUP", K_PATH_MAIN."admin/backup/");

/**
 * relative newsletter attached files directory
 */
define ("K_PATH_FILES_ATTACHMENTS", "../../attachments/");

/**
 * full newsletter attached files directory
 */
define ("K_PATH_FILES_ATTACHMENTS_FULL", K_PATH_HOST.K_PATH_AIOCP."attachments/");

/**
 * relative directory for dynamic pages files
 */
define ("K_PATH_FILES_PAGES", "../../pagefiles/");

/**
 * full directory for dynamic pages files
 */
define ("K_PATH_FILES_PAGES_FULL", K_PATH_HOST.K_PATH_AIOCP."pagefiles/");

/**
 * download dir
 */
define ("K_PATH_FILES_DOWNLOAD", "../../download/");

/**
 * banners dir
 */
define ("K_PATH_FILES_BANNERS", "../../banners/");

/**
 * shipping modules directory (for e-commerce)
 */
define ("K_PATH_FILES_SHIPPING_MODULES", "../../shared/shipping/");

/**
 * payment modules directory (for e-commerce)
 */
define ("K_PATH_FILES_PAYMENT_MODULES", "../../shared/payment/");

/**
 * directory path where are stored the downloadable proctucts (software). Please protect this directory from http access
 */
define ("K_PATH_FILES_DOWNLOADABLES", K_PATH_MAIN."download/");

/**
 * relative path to images
 */
define ("K_PATH_IMAGES", "../../images/");

/**
 * relative path to avatars
 */
define ("K_PATH_IMAGES_AVATARS", K_PATH_IMAGES."avatars/");

/**
 * relative path to awards
 */
define ("K_PATH_IMAGES_AWARDS", K_PATH_IMAGES."awards/");

/**
 * relative path to AIOCP backgrounds
 */
define ("K_PATH_IMAGES_BACKGROUNDS", K_PATH_IMAGES."backgrounds/cp/");

/**
 * relative path to dynamic buttons images
 */
define ("K_PATH_IMAGES_BUTTONS", K_PATH_IMAGES."dbuttons/");

/**
 * relative path to your company images
 */
define ("K_PATH_IMAGES_COMPANY", K_PATH_IMAGES."company/");

/**
 * relative path to html editor images
 */
define ("K_PATH_IMAGES_HTMLED", K_PATH_IMAGES."html_editor/");

/**
 * relative path to flag images
 */
define ("K_PATH_IMAGES_FLAGS", K_PATH_IMAGES."flags/");

/**
 * relative path to forum images
 */
define ("K_PATH_IMAGES_FORUM", K_PATH_IMAGES."forum/");

/**
 * relative path to icons
 */
define ("K_PATH_IMAGES_ICONS", K_PATH_IMAGES."icons/");

/**
 * relative path to client icons
 */
define ("K_PATH_IMAGES_ICONS_CLIENT", K_PATH_IMAGES."icons_client/");

/**
 * relative path to users' levels images
 */
define ("K_PATH_IMAGES_LEVELS", K_PATH_IMAGES."levels/");

/**
 * relative path to reviews images
 */
define ("K_PATH_IMAGES_REVIEWS", K_PATH_IMAGES."reviews/");

/**
 * relative path to manufacturers images
 */
define ("K_PATH_IMAGES_MANUFACTURERS", K_PATH_IMAGES."manufacturers/");

/**
 * relative path to products images
 */
define ("K_PATH_IMAGES_PRODUCTS", K_PATH_IMAGES."products/");

/**
 * relative path to products categories images
 */
define ("K_PATH_IMAGES_PRODUCTS_CATEGORIES", K_PATH_IMAGES."products_categories/");

/**
 * relative path to shell images
 */
define ("K_PATH_IMAGES_SHELL", K_PATH_IMAGES."shell/");

/**
 * relative path to emoticons images
 */
define ("K_PATH_IMAGES_EMOTICONS", K_PATH_IMAGES."emoticons/");

/**
 * relative path to users' photos
 */
define ("K_PATH_IMAGES_USER_PHOTO", K_PATH_IMAGES."userphoto/");

/**
 * relative path to bar images
 */
define ("K_PATH_IMAGES_BARS", K_PATH_IMAGES."bars/");


/**
 * relative path to sounds directory
 */
define ("K_PATH_SOUNDS", "../../sounds/");

/**
 * path to menu sound files
 */
define ("K_PATH_SOUNDS_MENU", K_PATH_SOUNDS."menu/");


/**
 * relative path to java directory
 */
define ("K_PATH_JAVA", "../java/");

/**
 * path to shared Java applications
 */
define ("K_PATH_SHARED_JAVA", "../../shared/java/");


/**
 * path to users' registration configuration file
 */
define ("K_FILE_USER_REG_OPTIONS", "../../shared/config/cp_user_registration.cfg");

/**
 * path to company data configuration file
 */
define ("K_FILE_COMPANY_DATA", "../../shared/config/cp_company_data.cfg");

// the following lines are for PHP5 compatibility
if (!isset($_SERVER['PATH_TRANSLATED'])) {
	$_SERVER['PATH_TRANSLATED'] = $_SERVER['SCRIPT_FILENAME'];
}
if (!isset($_SERVER['PATH_INFO'])) {
	$_SERVER['PATH_INFO'] = $_SERVER['SCRIPT_NAME'];
}

// DOCUMENT_ROOT fix for IIS Webserver
if ((!isset($_SERVER['DOCUMENT_ROOT'])) OR (empty($_SERVER['DOCUMENT_ROOT']))) {
	if(isset($_SERVER['SCRIPT_FILENAME'])) {
		$_SERVER['DOCUMENT_ROOT'] = str_replace( '\\', '/', substr($_SERVER['SCRIPT_FILENAME'], 0, 0-strlen($_SERVER['PHP_SELF'])));
	} elseif(isset($_SERVER['PATH_TRANSLATED'])) {
		$_SERVER['DOCUMENT_ROOT'] = str_replace( '\\', '/', substr(str_replace('\\\\', '\\', $_SERVER['PATH_TRANSLATED']), 0, 0-strlen($_SERVER['PHP_SELF'])));
	}	else {
		// define here your DOCUMENT_ROOT path if the previous fails
		$_SERVER['DOCUMENT_ROOT'] = "/var/www";
	}
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
