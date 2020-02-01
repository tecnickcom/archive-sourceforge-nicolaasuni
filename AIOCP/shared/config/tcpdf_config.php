<?php
//============================================================+
// File name   : tcpdf_config.php
// Begin       : 2004-06-11
// Last Update : 2009-04-20
//
// Description : Congiguration file for TCPDF.
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
//============================================================+

/**
 * Congiguration file for TCPDF.
 * @author Nicola Asuni
 * @copyright Copyright &copy; 2004, Tecnick.com LTD - Manor Coach House, Church Hill, Aldershot, Hants, GU12 4RQ, UK - www.tecnick.com - info@tecnick.com
 * @package com.tecnick.tcpdf
 * @version 1.53.0.TC015
 * @link http://tcpdf.sourceforge.net
 * @license http://www.gnu.org/copyleft/lesser.html LGPL
 * @since 2004-06-11
 */


/**
 * Constant used by TCPDF library.
 */
define ('K_PATH_URL', K_PATH_HOST.K_PATH_AIOCP);

/**
 * page format
 */
define ('PDF_PAGE_FORMAT', 'A4');

/**
 * page orientation (P=portrait, L=landscape)
 */
define ('PDF_PAGE_ORIENTATION', 'P');

/**
 * document creator
 */
define ('PDF_CREATOR', 'TCPDF');

/**
 * document author
 */
define ('PDF_AUTHOR', 'TCPDF');

/**
 * header title
 */
define ('PDF_HEADER_TITLE', 'TCPDF-AIOCP');

/**
 * header description string
 */
define ('PDF_HEADER_STRING', "by Nicola Asuni - Tecnick.com\nwww.tcpdf.org");

/**
 * image logo
 */
define ('PDF_HEADER_LOGO', '');

/**
 * header logo image width [mm]
 */
define ('PDF_HEADER_LOGO_WIDTH', 30);

/**
 *  document unit of measure [pt=point, mm=millimeter, cm=centimeter, in=inch]
 */
define ('PDF_UNIT', 'mm');

/**
 * header margin
 */
define ('PDF_MARGIN_HEADER', 5);

/**
 * footer margin
 */
define ('PDF_MARGIN_FOOTER', 10);

/**
 * top margin
 */
define ('PDF_MARGIN_TOP', 27);

/**
 * bottom margin
 */
define ('PDF_MARGIN_BOTTOM', 25);

/**
 * left margin
 */
define ('PDF_MARGIN_LEFT', 15);

/**
 * right margin
 */
define ('PDF_MARGIN_RIGHT', 15);

/**
 * default main font name
 */
define ('PDF_FONT_NAME_MAIN', 'helvetica');

/**
 * default main font size
 */
define ('PDF_FONT_SIZE_MAIN', 10);

/**
 * default data font name
 */
define ('PDF_FONT_NAME_DATA', 'times');

/**
 * default data font size
 */
define ('PDF_FONT_SIZE_DATA', 8);

/**
 * default monospaced font name
 */
define ('PDF_FONT_MONOSPACED', 'courier');

/**
 * ratio used to adjust the conversion of pixels to user units
 */
define ('PDF_IMAGE_SCALE_RATIO', 1);

/**
 * magnification factor for titles
 */
define('HEAD_MAGNIFICATION', 1.1);

/**
 * height of cell repect font height
 */
define('K_CELL_HEIGHT_RATIO', 1.25);

/**
 * title magnification respect main font size
 */
define('K_TITLE_MAGNIFICATION', 1.3);

/**
 * reduction factor for small font
 */
define('K_SMALL_RATIO', 2/3);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
