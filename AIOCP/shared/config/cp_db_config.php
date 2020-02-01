<?php
//============================================================+
// File name   : cp_db_config.php
// Begin       : 2001-09-02
// Last Update : 2006-11-27
// 
// Description : Database Settings
//               and database table names
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
 * database type (for Database Abstraction Layer)
 */
define ("K_DATABASE_TYPE", "MYSQL");

/**
 * database Host name (localhost)
 */
define ("K_DATABASE_HOST", "localhost");

/**
 * database name (AIOCP)
 */
define ("K_DATABASE_NAME", "aiocp");

/**
 * database user name
 */
define ("K_DATABASE_USER_NAME", "root");

/**
 * database user password
 */
define ("K_DATABASE_USER_PASSWORD", "kmyoxen");

/**
 * prefix for database tables names
 */
define ("K_TABLE_PREFIX", "aiocp_");


// --- database tables names (do not change)
define ("K_TABLE_AVATARS", K_TABLE_PREFIX."avatars");
define ("K_TABLE_AWARDS", K_TABLE_PREFIX."awards");
define ("K_TABLE_BADWORDS", K_TABLE_PREFIX."badwords");
define ("K_TABLE_BANNERS", K_TABLE_PREFIX."banners");
define ("K_TABLE_BANNERS_STATS", K_TABLE_PREFIX."banners_stats");
define ("K_TABLE_BANNERS_ZONES", K_TABLE_PREFIX."banners_zone");
define ("K_TABLE_BUTTON_STYLES", K_TABLE_PREFIX."button_styles");
define ("K_TABLE_CALENDAR", K_TABLE_PREFIX."calendar");
define ("K_TABLE_CALENDAR_CATEGORIES", K_TABLE_PREFIX."calendar_categories");
define ("K_TABLE_CHAT_MESSAGES", K_TABLE_PREFIX."chat_messages");
define ("K_TABLE_ONLINE_USERS", K_TABLE_PREFIX."chat_online_users");
define ("K_TABLE_CHAT_ROOMS", K_TABLE_PREFIX."chat_rooms");
define ("K_TABLE_COUNTRIES", K_TABLE_PREFIX."countries");
define ("K_TABLE_COUNTRIES_STATES", K_TABLE_PREFIX."countries_states");
define ("K_TABLE_DICTIONARY_CATEGORIES", K_TABLE_PREFIX."dictionary_categories");
define ("K_TABLE_DICTIONARY_WORDS", K_TABLE_PREFIX."dictionary_words");
define ("K_TABLE_DOWNLOADS", K_TABLE_PREFIX."downloads");
define ("K_TABLE_DOWNLOADS_CATEGORIES", K_TABLE_PREFIX."downloads_categories");
define ("K_TABLE_EC_CURRENCY", K_TABLE_PREFIX."ec_currency");
define ("K_TABLE_EC_DOCUMENTS", K_TABLE_PREFIX."ec_documents");
define ("K_TABLE_EC_DOCUMENTS_DETAILS", K_TABLE_PREFIX."ec_documents_details");
define ("K_TABLE_EC_DOCUMENTS_STYLES", K_TABLE_PREFIX."ec_documents_styles");
define ("K_TABLE_EC_DOCUMENTS_TYPES", K_TABLE_PREFIX."ec_documents_types");
define ("K_TABLE_EC_MANUFACTURERS", K_TABLE_PREFIX."ec_manufacturers");
define ("K_TABLE_EC_PAYMENTS_CATEGORIES", K_TABLE_PREFIX."ec_payments_categories");
define ("K_TABLE_EC_PAYMENTS_TYPES", K_TABLE_PREFIX."ec_payments_types");
define ("K_TABLE_EC_PRODUCTS", K_TABLE_PREFIX."ec_products");
define ("K_TABLE_EC_PRODUCTS_CATEGORIES", K_TABLE_PREFIX."ec_products_categories");
define ("K_TABLE_EC_PRODUCTS_RESOURCES", K_TABLE_PREFIX."ec_products_resources");
define ("K_TABLE_EC_SHIPPING_TYPES", K_TABLE_PREFIX."ec_shipping_types");
define ("K_TABLE_EC_SHOPPING_CART", K_TABLE_PREFIX."ec_shopping_cart");
define ("K_TABLE_EC_SHOPPING_CART_USER_DATA", K_TABLE_PREFIX."ec_shopping_cart_user_data");
define ("K_TABLE_EC_TRANSACTIONS", K_TABLE_PREFIX."ec_transactions");
define ("K_TABLE_EC_TRANSACTIONS_PAYMENTS", K_TABLE_PREFIX."ec_transactions_payments");
define ("K_TABLE_EC_TRANSACTIONS_TYPES", K_TABLE_PREFIX."ec_transactions_types");
define ("K_TABLE_EC_UNITS_OF_MEASURE", K_TABLE_PREFIX."ec_units_of_measure");
define ("K_TABLE_EC_USER_DISCOUNT", K_TABLE_PREFIX."ec_user_discount");
define ("K_TABLE_EC_VAT", K_TABLE_PREFIX."ec_vat");
define ("K_TABLE_EC_WARRANTIES", K_TABLE_PREFIX."ec_warranties");
define ("K_TABLE_EC_WORKS", K_TABLE_PREFIX."ec_works");
define ("K_TABLE_FORUM_CATEGORIES", K_TABLE_PREFIX."forum_categories");
define ("K_TABLE_FORUM_FORUMS", K_TABLE_PREFIX."forum_forums");
define ("K_TABLE_FORUM_MODERATORS", K_TABLE_PREFIX."forum_moderators");
define ("K_TABLE_FORUM_POSTS", K_TABLE_PREFIX."forum_posts");
define ("K_TABLE_FORUM_TOPICS", K_TABLE_PREFIX."forum_topics");
define ("K_TABLE_FRAME_TARGETS", K_TABLE_PREFIX."frame_targets");
define ("K_TABLE_ICONS", K_TABLE_PREFIX."icons");
define ("K_TABLE_ICONS_CLIENT", K_TABLE_PREFIX."icons_client");
define ("K_TABLE_LANGUAGE_CODES", K_TABLE_PREFIX."language_codes");
define ("K_TABLE_LANGUAGE_DATA", K_TABLE_PREFIX."language_data");
define ("K_TABLE_LANGUAGE_HELP", K_TABLE_PREFIX."language_help");
define ("K_TABLE_LANGUAGE_PAGES", K_TABLE_PREFIX."language_pages");
define ("K_TABLE_LEVELS", K_TABLE_PREFIX."levels");
define ("K_TABLE_LINKS", K_TABLE_PREFIX."links");
define ("K_TABLE_LINKS_CATEGORIES", K_TABLE_PREFIX."links_categories");
define ("K_TABLE_MENU", K_TABLE_PREFIX."menu");
define ("K_TABLE_MENU_LIST", K_TABLE_PREFIX."menu_list");
define ("K_TABLE_MENU_CLIENT", K_TABLE_PREFIX."menu_client");
define ("K_TABLE_MENU_OPTIONS", K_TABLE_PREFIX."menu_options");
define ("K_TABLE_MENU_STYLES", K_TABLE_PREFIX."menu_styles");
define ("K_TABLE_MENU_EFFECTS", K_TABLE_PREFIX."menu_effects");
define ("K_TABLE_MIME", K_TABLE_PREFIX."mime");
define ("K_TABLE_NEWS", K_TABLE_PREFIX."news");
define ("K_TABLE_NEWS_CATEGORIES", K_TABLE_PREFIX."news_categories");
define ("K_TABLE_NEWSLETTER_ATTACHMENTS", K_TABLE_PREFIX."newsletter_attachments");
define ("K_TABLE_NEWSLETTER_CATEGORIES", K_TABLE_PREFIX."newsletter_categories");
define ("K_TABLE_NEWSLETTER_MESSAGES", K_TABLE_PREFIX."newsletter_messages");
define ("K_TABLE_NEWSLETTER_USERS", K_TABLE_PREFIX."newsletter_users");
define ("K_TABLE_PAGE_DATA", K_TABLE_PREFIX."page_data");
define ("K_TABLE_PAGE_HEADER_FOOTER", K_TABLE_PREFIX."page_hf");
define ("K_TABLE_PAGE_MODULES", K_TABLE_PREFIX."page_modules");
define ("K_TABLE_POLLS", K_TABLE_PREFIX."polls");
define ("K_TABLE_POLLS_OPTIONS", K_TABLE_PREFIX."polls_options");
define ("K_TABLE_POLLS_VOTES", K_TABLE_PREFIX."polls_votes");
define ("K_TABLE_REVIEWS", K_TABLE_PREFIX."reviews");
define ("K_TABLE_REVIEWS_CATEGORIES", K_TABLE_PREFIX."reviews_categories");
define ("K_TABLE_SEARCH_DICTIONARY", K_TABLE_PREFIX."search_dictionary");
define ("K_TABLE_SEARCH_URL", K_TABLE_PREFIX."search_url");
define ("K_TABLE_SESSIONS", K_TABLE_PREFIX."sessions");
define ("K_TABLE_EMOTICONS", K_TABLE_PREFIX."emoticons");
define ("K_TABLE_SOFTWARE_LICENSES", K_TABLE_PREFIX."software_licenses");
define ("K_TABLE_SOFTWARE_OS", K_TABLE_PREFIX."software_os");
define ("K_TABLE_SOUNDS_MENU", K_TABLE_PREFIX."sound_effects");
define ("K_TABLE_STATS", K_TABLE_PREFIX."stats");
define ("K_TABLE_STATS_PAGES", K_TABLE_PREFIX."stats_pages");
define ("K_TABLE_STATS_REFERER", K_TABLE_PREFIX."stats_referer");
define ("K_TABLE_STATS_USER_AGENTS", K_TABLE_PREFIX."stats_user_agents");
define ("K_TABLE_USER_AGENDA", K_TABLE_PREFIX."user_agenda");
define ("K_TABLE_USERS", K_TABLE_PREFIX."users");
define ("K_TABLE_USERS_ADDRESS", K_TABLE_PREFIX."users_address");
define ("K_TABLE_USERS_AUTH", K_TABLE_PREFIX."users_auth");
define ("K_TABLE_USERS_COMPANY", K_TABLE_PREFIX."users_company");
define ("K_TABLE_USERS_COMPANY_TYPES", K_TABLE_PREFIX."users_company_types");
define ("K_TABLE_USERS_GROUPS", K_TABLE_PREFIX."users_groups");
define ("K_TABLE_USERS_INTERNET", K_TABLE_PREFIX."users_internet");
define ("K_TABLE_USERS_PHONE", K_TABLE_PREFIX."users_phone");
define ("K_TABLE_USERS_VERIFICATION", K_TABLE_PREFIX."users_verification");
define ("K_TABLE_XHTML_ATTRIBUTES", K_TABLE_PREFIX."xhtml_attributes");
define ("K_TABLE_XHTML_TAGS", K_TABLE_PREFIX."xhtml_tags");
define ("K_TABLE_XHTML_TAGS_CATEGORIES", K_TABLE_PREFIX."xhtml_tags_categories");
define ("K_TABLE_XHTML_TAGS_STATUS", K_TABLE_PREFIX."xhtml_tags_status");

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
