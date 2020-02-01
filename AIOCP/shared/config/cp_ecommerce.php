<?php
//============================================================+
// File name   : cp_ecommerce.php                              
// Begin       : 2002-08-25                                    
// Last Update : 2004-08-20                                    
//                                                             
// Description : General E-Commerce settings                   
//                                                             
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
 * fix PDF document language code - leave void to use selected language
 */
define ("K_DOCUMENTS_LANGUAGE", "");

/**
 * ISO 4217 currency alphabetic code (3 uppercase letters)
 */
define ("K_MONEY_CURRENCY_ISO_ALPHA_CODE", "EUR");

/**
 * barcode type (used in PDF documents) - see barcode class on shared/barcode directory for more info
 */
define ("K_BARCODE_TYPE", "C128B");

/**
 * default general discount for e-commerce
 */
define ("K_EC_GENERAL_DISCOUNT", 0);

//invoices settings

/**
 * ID of the invoice document for e-commerce sales
 */
define ("K_EC_INVOICE_DOC_ID", 1);

/**
 * if true display additional (second) tax field (this tax will be applied to the net amount)
 */
define ("K_EC_DISPLAY_TAX_2", false);

/**
 * if true display additional (third) tax field (this tax will be applied to the total amount (net + previous taxes).
 */
define ("K_EC_DISPLAY_TAX_3", false);

// Orders Settings

/**
 * ID of the order document for e-commerce sales
 */
define ("K_EC_ORDER_DOC_ID", 3);

/**
 * default expiration time for orders
 */
define ("K_EC_ORDER_EXPIRY_TIME", K_SECONDS_IN_YEAR);

/**
 * Default subject for orders (only one language)
 */
define ("K_EC_ORDER_SUBJECT", "");

/**
 * Default intro for orders (only one language)
 */
define ("K_EC_ORDER_INTRO", "");

/**
 * Default intro for orders (only one language)
 */
define ("K_EC_ORDER_FOOTER", "");

// Return Goods Authorization (RGA) settings

/**
 * ID of the Return Goods Authorization (RGA) document for e-commerce sales
 */
define ("K_EC_RGA_DOC_ID", 5);

/**
 * default expiration time for orders
 */
define ("K_EC_RGA_EXPIRY_TIME", K_SECONDS_IN_YEAR);

/**
 * Default subject for orders (only one language)
 */
define ("K_EC_RGA_SUBJECT", "");

/**
 * Default intro for orders (only one language)
 */
define ("K_EC_RGA_INTRO", "");

/**
 * Default intro for orders (only one language)
 */
define ("K_EC_RGA_FOOTER", "");

// E-commerce risk limit

/**
 * shopping cart total limit that require specific usel level
 */
define ("K_EXPENSIVE_PURHCASE_LIMIT", 300);

/**
 * user level required to make purchases with total amount above K_EXPENSIVE_PURHCASE_LIMIT limit
 */
define ("K_EXPENSIVE_PURHCASE_LEVEL", 1);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
