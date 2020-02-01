<?php
//============================================================+
// File name   : cp_auth.php                                   
// Begin       : 2002-09-02                                    
// Last Update : 2003-11-05                                    
//                                                             
// Description : Define access levels for each admin page      
//               Note:                                         
//                0 = Anonymous user (uregistered user)        
//               10 = Site Administrator                       
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

// ************************************************************
// SECURITY WARNING :
// SET THIS FILE AS READ ONLY AFTER MODIFICATIONS   
// ************************************************************

define ("K_AUTH_ADMIN_CP_AIOCPCODE_PREVIEW", 10); // show aiocp code preview
define ("K_AUTH_ADMIN_CP_AIOCP_CREDITS", 0); // Display AIOCP and third party Software Credits
define ("K_AUTH_ADMIN_CP_BACKUP", 10); // Backup site and database on two zip files
define ("K_AUTH_ADMIN_CP_CHAT", 5); // Chat board
define ("K_AUTH_ADMIN_CP_CHECK_LINKS", 10); // find and delete broken links from database
define ("K_AUTH_ADMIN_CP_DOWNLOAD_CODE", 10); // calculate verification link for protected downloads
define ("K_AUTH_ADMIN_CP_EC_ORDER", 10); // Create order from shopping cart
define ("K_AUTH_ADMIN_CP_EC_PAYMENT_ERROR", 0); // Display payment error
define ("K_AUTH_ADMIN_CP_EDIT_AIOCPCODE", 10); // AIOCP Code Editor
define ("K_AUTH_ADMIN_CP_EDIT_AVATARS", 10); // Edit Avatars List
define ("K_AUTH_ADMIN_CP_EDIT_AWARDS", 10); // Edit Awards
define ("K_AUTH_ADMIN_CP_EDIT_BADWORDS", 10); // Edit badwords list
define ("K_AUTH_ADMIN_CP_EDIT_BANNER", 10); // Edit banners
define ("K_AUTH_ADMIN_CP_EDIT_BANNER_ZONE", 10); // Edit banner zones
define ("K_AUTH_ADMIN_CP_EDIT_BUTTON_STYLE", 10); // Edit styles for graphic buttons (and bars)
define ("K_AUTH_ADMIN_CP_EDIT_CALENDAR", 10); // Edit calendar
define ("K_AUTH_ADMIN_CP_EDIT_CALENDAR_CATEGORIES", 10); // Edit calendar categories
define ("K_AUTH_ADMIN_CP_EDIT_CHAT_ROOMS", 10); // Edit chat rooms for different languages
define ("K_AUTH_ADMIN_CP_EDIT_COUNTRY", 10); // Edit countries data and flags
define ("K_AUTH_ADMIN_CP_EDIT_COMPANY_TYPES", 10); // Edit company types
define ("K_AUTH_ADMIN_CP_EDIT_DBPAGES", 10); // Edit pages that will be stored on database
define ("K_AUTH_ADMIN_CP_EDIT_DICTIONARY_CATEGORIES", 10); // Edit dictionary categories
define ("K_AUTH_ADMIN_CP_EDIT_DICTIONARY_WORDS", 10); // Edit dictionary words
define ("K_AUTH_ADMIN_CP_EDIT_DOWNLOADS", 10); // Edit Downloads
define ("K_AUTH_ADMIN_CP_EDIT_DOWNLOAD_CATEGORIES", 10); // Edit categories for downloads
define ("K_AUTH_ADMIN_CP_EDIT_EC_COMPANY_DATA", 10); // Edit Company Data (site owner data)
define ("K_AUTH_ADMIN_CP_EDIT_EC_CURRENCY", 10); // Edit currencies data (code, symbols, ...)
define ("K_AUTH_ADMIN_CP_EDIT_EC_DOCUMENTS", 10); // Edit Business Documents (invoices,...)
define ("K_AUTH_ADMIN_CP_EDIT_EC_DOCUMENTS_DETAILS", 10); // Edit Business Documents details (products data)
define ("K_AUTH_ADMIN_CP_EDIT_EC_DOCUMENTS_TYPES", 10); // Edit Commercial Documents Types
define ("K_AUTH_ADMIN_CP_EDIT_EC_DOCUMENT_STYLES", 10); // Edit Styles for Commercial Documents
define ("K_AUTH_ADMIN_CP_EDIT_EC_MANUFACTURERS", 10); // Edit Product's Manufacturers
define ("K_AUTH_ADMIN_CP_EDIT_EC_PAYTYPE_CATEGORIES", 10); // Edit Payment Categories
define ("K_AUTH_ADMIN_CP_EDIT_EC_PAYTYPE_TYPES", 10); // Edit Payment Methods list
define ("K_AUTH_ADMIN_CP_EDIT_EC_PRODUCTS", 10); // Edit Products
define ("K_AUTH_ADMIN_CP_EDIT_EC_PRODUCTS_CATEGORIES", 10); // Edit Products Categories
define ("K_AUTH_ADMIN_CP_EDIT_EC_SHIPPING_TYPES", 10); // Edit Shipping methods Types list
define ("K_AUTH_ADMIN_CP_EDIT_EC_TRANSACTIONS", 10); // Edit Money Transactions
define ("K_AUTH_ADMIN_CP_EDIT_EC_TRANSACTIONS_TYPES", 10); // Edit Transaction Types
define ("K_AUTH_ADMIN_CP_EDIT_EC_USER_DISCOUNT", 10); // Edit User Discounts
define ("K_AUTH_ADMIN_CP_EDIT_EC_VAT", 10); // Edit VAT percentages
define ("K_AUTH_ADMIN_CP_EDIT_EC_WARRANTIES", 10); // Edit products warranty certificates
define ("K_AUTH_ADMIN_CP_EDIT_EC_WORKS", 10); // Edit Works List
define ("K_AUTH_ADMIN_CP_EDIT_FILE", 10); // Text File Editor
define ("K_AUTH_ADMIN_CP_EDIT_FORUMS", 10); // Edit forums
define ("K_AUTH_ADMIN_CP_EDIT_FORUM_CATEGORIES", 10); // Edit forum categories
define ("K_AUTH_ADMIN_CP_EDIT_FORUM_MODERATORS", 10); // Edit forums moderators
define ("K_AUTH_ADMIN_CP_EDIT_HTML", 10); // HTML Editor
define ("K_AUTH_ADMIN_CP_EDIT_HTML_COLORS", 10); // HTML Color Picker
define ("K_AUTH_ADMIN_CP_EDIT_HTML_IMAGE", 10); // HTML <img /> Editor
define ("K_AUTH_ADMIN_CP_EDIT_HTML_LINK", 10); // HTML <a></a> Editor
define ("K_AUTH_ADMIN_CP_EDIT_HTML_OPTIONS", 10); // HTML Options Editor
define ("K_AUTH_ADMIN_CP_EDIT_HTML_TABLE", 10); // HTML <table></table> Editor
define ("K_AUTH_ADMIN_CP_EDIT_HTML_WYSIWYG", 10); // WYSIWYG HTML Editor
define ("K_AUTH_ADMIN_CP_EDIT_ICONS", 10); // Edit AIOCP System Icons (menu)
define ("K_AUTH_ADMIN_CP_EDIT_ICONS_CLIENT", 10); // Edit Menu Client Icons
define ("K_AUTH_ADMIN_CP_EDIT_LANGUAGES_HELP", 10); // Edit help templates in all enabled languages
define ("K_AUTH_ADMIN_CP_EDIT_LANGUAGE_CODES", 10); // Enable/Disable system languages
define ("K_AUTH_ADMIN_CP_EDIT_LANGUAGE_PAGES", 10); // Edit page templates in all enabled languages
define ("K_AUTH_ADMIN_CP_EDIT_LANGUAGE_TEMPLATES", 10); // Edit words templates in all enabled languages
define ("K_AUTH_ADMIN_CP_EDIT_LEVELS", 10); // Edit system authorization leves
define ("K_AUTH_ADMIN_CP_EDIT_LICENSES", 10); // Edit Software Licenses
define ("K_AUTH_ADMIN_CP_EDIT_LINKS", 10); // Edit links
define ("K_AUTH_ADMIN_CP_EDIT_LINKS_CATEGORIES", 10); // Edit links categories
define ("K_AUTH_ADMIN_CP_EDIT_MENU", 10); // Edit Menu entries on menu tables
define ("K_AUTH_ADMIN_CP_EDIT_MENU_CLIENT_OPTIONS", 10); // Edit Client Menu Options
define ("K_AUTH_ADMIN_CP_EDIT_MENU_LIST", 10); // Edit Menu List (add/remove client menus)
define ("K_AUTH_ADMIN_CP_EDIT_MENU_STYLE", 10); // Edit styles for client menus
define ("K_AUTH_ADMIN_CP_EDIT_MIME", 10); // Edit MIME content type associations to file extensions
define ("K_AUTH_ADMIN_CP_EDIT_NEWPAGE", 10); // New PHP page wizard editor - Create new php pages in AIOCP style 
define ("K_AUTH_ADMIN_CP_EDIT_NEWS", 10); // Edit News
define ("K_AUTH_ADMIN_CP_EDIT_NEWSCAT", 10); // Edit News Categories
define ("K_AUTH_ADMIN_CP_EDIT_NEWSLETTER_ATTACHMENTS", 10); // Edit newsletter attachments
define ("K_AUTH_ADMIN_CP_EDIT_NEWSLETTER_CATEGORIES", 10); // Edit newsletter categories
define ("K_AUTH_ADMIN_CP_EDIT_NEWSLETTER_MESSAGES", 10); // Edit newsletter messages
define ("K_AUTH_ADMIN_CP_EDIT_NEWSLETTER_USERS", 10); // Edit newsletter users
define ("K_AUTH_ADMIN_CP_EDIT_OS", 10); // Edit Operative Systems
define ("K_AUTH_ADMIN_CP_EDIT_PAGE_HEADER_FOOTER", 10); // Edit headers and footers to be used in dynamic pages
define ("K_AUTH_ADMIN_CP_EDIT_POLLS", 10); // Edit polls
define ("K_AUTH_ADMIN_CP_EDIT_POLLS_OPTIONS", 10); // Edit polls options
define ("K_AUTH_ADMIN_CP_EDIT_REVIEWS", 10); // Edit Reviews
define ("K_AUTH_ADMIN_CP_EDIT_REVIEW_CATEGORIES", 10); // Edit Reviews Categories
define ("K_AUTH_ADMIN_CP_EDIT_SMILES", 10); // Edit smiles
define ("K_AUTH_ADMIN_CP_EDIT_SOUNDS_MENU", 10); // Edit sound clips for menus
define ("K_AUTH_ADMIN_CP_EDIT_TARGETS", 10); // Edit frame targets
define ("K_AUTH_ADMIN_CP_EDIT_UNIT_OF_MEASURE", 10); // Edit units of measure
define ("K_AUTH_ADMIN_CP_EDIT_USER", 10); // Edit user data
define ("K_AUTH_ADMIN_CP_EDIT_USER_AUTH", 10); // Edit time limited user access permissions to a particular resource
define ("K_AUTH_ADMIN_CP_EDIT_USER_GROUPS", 10); // Edit user groups
define ("K_AUTH_ADMIN_CP_EDIT_USER_REGOPTIONS", 10); // Edit User Registration Option
define ("K_AUTH_ADMIN_CP_FORUM_EDIT_MESSAGE", 10); // Forum Message Editor
define ("K_AUTH_ADMIN_CP_FORUM_EDIT_TOPIC", 10); // Forum Topic Editor
define ("K_AUTH_ADMIN_CP_FORUM_LAST_POSTS", 10); // Show forum last posts
define ("K_AUTH_ADMIN_CP_FORUM_VIEW", 10); // Show categories and forums
define ("K_AUTH_ADMIN_CP_HELP", 1); // Display AIOCP Manual
define ("K_AUTH_ADMIN_CP_LAYOUT_HEADER", 0); // System page frame CPHEADER
define ("K_AUTH_ADMIN_CP_LAYOUT_HEADER_LEFT", 0); // System page frame CPHEADERLEFT
define ("K_AUTH_ADMIN_CP_LAYOUT_HEADER_RIGHT", 0); // System page frame CPHEADERRIGHT
define ("K_AUTH_ADMIN_CP_LAYOUT_MAIN", 10); // Main page (frame name: CPMAIN)
define ("K_AUTH_ADMIN_CP_LAYOUT_MENU", 0); // System page frame CPMENU
define ("K_AUTH_ADMIN_CP_LAYOUT_MENU_BOTTOM", 0); // System page frame CPMENUBOTTOM
define ("K_AUTH_ADMIN_CP_LAYOUT_MENU_TOP", 0); // System page frame CPMENUTOP
define ("K_AUTH_ADMIN_CP_NEWSLETTER_FORM", 0); // subscribe/unsubsrcibe page for newsletter
define ("K_AUTH_ADMIN_CP_EDIT_NEWSLETTER_PREVIEW", 10); // Preview selected newsletter
define ("K_AUTH_ADMIN_CP_OPTIMIZE_TABLES", 10); // Optimize MySQL tables
define ("K_AUTH_ADMIN_CP_EDIT_PAGE_MODULE", 10); // Edit custom PHP modules
define ("K_AUTH_ADMIN_CP_PHP_INFO", 10); // Outputs a large amount of information about the current state of PHP
define ("K_AUTH_ADMIN_CP_PING", 10); // Execute ping
define ("K_AUTH_ADMIN_CP_POLLS_RESULTS", 10); // display polls results with statistics
define ("K_AUTH_ADMIN_CP_POLLS_VOTE", 10); // Vote polls
define ("K_AUTH_ADMIN_CP_SEARCH_COMPANY", 10); // Search for Company
define ("K_AUTH_ADMIN_CP_SEARCH_DICTIONARY", 10); // Search for Dictionary
define ("K_AUTH_ADMIN_CP_SEARCH_DOWNLOADS", 10); // Search for Downloads
define ("K_AUTH_ADMIN_CP_SEARCH_EC_PRODUCTS", 10); // Search for Products
define ("K_AUTH_ADMIN_CP_SEARCH_FORUM", 10); // Search for Forum
define ("K_AUTH_ADMIN_CP_SEARCH_INDEXER", 10); // Search Indexer for public site
define ("K_AUTH_ADMIN_CP_SEARCH_LINKS", 10); // Search for Links
define ("K_AUTH_ADMIN_CP_SEARCH_NEWS", 10); // Search for News
define ("K_AUTH_ADMIN_CP_SEARCH_NEWSLETTER", 10); // Search for Newsletter
define ("K_AUTH_ADMIN_CP_SEARCH_NL_USERS", 10); // Search for Newsletter Users
define ("K_AUTH_ADMIN_CP_SEARCH_REVIEWS", 10); // Search for Reviews
define ("K_AUTH_ADMIN_CP_SEARCH_USERS", 10); // Search for users
define ("K_AUTH_ADMIN_CP_SELECT_AVATARS", 1); // Avatars selection page
define ("K_AUTH_ADMIN_CP_SELECT_COMPANY", 10); // Show list of Companies
define ("K_AUTH_ADMIN_CP_SELECT_COUNTRY", 1); // Select page for countries flag
define ("K_AUTH_ADMIN_CP_SELECT_ICONS", 1); // Select page for AIOCP system icons
define ("K_AUTH_ADMIN_CP_SELECT_ICONS_CLIENT", 10); // Select page for client menu icons
define ("K_AUTH_ADMIN_CP_SELECT_NL_USERS", 10); // Show a list of newsletter users
define ("K_AUTH_ADMIN_CP_SELECT_EMOTICONS", 1); // Select page for emoticons
define ("K_AUTH_ADMIN_CP_SELECT_USERS", 10); // Show list of users
define ("K_AUTH_ADMIN_CP_SHELL", 10); // remote shell (keep this level to 10 for security reasons)
define ("K_AUTH_ADMIN_CP_SHOW_AWARDS", 1); // Award List Preview
define ("K_AUTH_ADMIN_CP_SHOW_BANNER_STATS", 10); // Display banner Stats
define ("K_AUTH_ADMIN_CP_SHOW_CALENDAR", 1); // Show site calendar
define ("K_AUTH_ADMIN_CP_SHOW_DICTIONARY", 1); // Dictionary Preview
define ("K_AUTH_ADMIN_CP_SHOW_DOWNLOADS", 1); // Downloads List Preview
define ("K_AUTH_ADMIN_CP_SHOW_EC_DOCUMENTS", 1); // Display user commercial documents
define ("K_AUTH_ADMIN_CP_SHOW_EC_PRODUCTS", 1); // Display products list
define ("K_AUTH_ADMIN_CP_SHOW_EC_SHOPPING_CART", 1); // Display current user shopping cart
define ("K_AUTH_ADMIN_CP_SHOW_EC_TRANSACTIONS", 10); // Display transactions
define ("K_AUTH_ADMIN_CP_SHOW_EC_WARRANTY", 1); // Display requested warranty certificate
define ("K_AUTH_ADMIN_CP_SHOW_LINKS", 1); // Links List Preview
define ("K_AUTH_ADMIN_CP_SHOW_NEWS", 1); // News Preview
define ("K_AUTH_ADMIN_CP_SHOW_NEWSLETTER", 1); // Newsletter Online Preview
define ("K_AUTH_ADMIN_CP_SHOW_ONLINE_USERS", 10); // Show online users
define ("K_AUTH_ADMIN_CP_SHOW_PAGE_HELP", 0); // Show page help popups
define ("K_AUTH_ADMIN_CP_SHOW_REVIEWS", 10); // Display Reviews
define ("K_AUTH_ADMIN_CP_SHOW_SITE_STATS", 10); // Display Site Stats
define ("K_AUTH_ADMIN_CP_TRACEROUTE", 10); // Execute traceroute
define ("K_AUTH_ADMIN_CP_UPDATE_EC_PRODUCTS", 10); //Update products data using text file
define ("K_AUTH_ADMIN_CP_USER_AGENDA", 10); // Display user agenda
define ("K_AUTH_ADMIN_CP_USER_PROFILE", 10); // Display all user data
define ("K_AUTH_ADMIN_CP_WHOIS", 10); // Do WHOIS queries

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
