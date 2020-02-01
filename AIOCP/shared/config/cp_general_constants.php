<?php
//============================================================+
// File name   : cp_general_constants.php
// Begin       : 2002-03-01
// Last Update : 2006-11-27
// 
// Description : SHARED cofiguration file for general contants
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
 * Configuration file for general constants.
 * @package com.tecnick.aiocp.shared
 * @author Nicola Asuni
 * @copyright Copyright &copy; 2004-2006, Tecnick.com LTD - Manor Coach House, Church Hill, Aldershot, Hants, GU12 4RQ, UK - www.tecnick.com - info@tecnick.com
 * @link www.tecnick.com
 * @since 2002-03-01
 */

/**
 * number of seconds in one minute
 */
define ("K_SECONDS_IN_MINUTE", 60);

/**
 * number of seconds in one hour
 */
define ("K_SECONDS_IN_HOUR", 60 * K_SECONDS_IN_MINUTE);

/**
 * number of seconds in one day
 */
define ("K_SECONDS_IN_DAY", 24 * K_SECONDS_IN_HOUR);

/**
 * number of seconds in one week
 */
define ("K_SECONDS_IN_WEEK", 7 * K_SECONDS_IN_DAY);

/**
 * number of seconds in one month
 */
define ("K_SECONDS_IN_MONTH", 30 * K_SECONDS_IN_DAY);

/**
 * number of seconds in one year
 */
define ("K_SECONDS_IN_YEAR", 365 * K_SECONDS_IN_DAY);

/**
 * string used as a seed for some security code generation please change this value and keep it secret
 */
define ("K_RANDOM_SECURITY", "9dh46sge"); 

//============================================================+
// END OF FILE                                                 
//============================================================+
?>