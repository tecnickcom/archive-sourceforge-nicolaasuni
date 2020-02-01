-- phpMyAdmin SQL Dump
-- version 2.9.2-rc1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost:3306
-- Generation Time: Jan 11, 2007 at 11:05 PM
-- Server version: 4.1.14
-- PHP Version: 5.2.0
-- 
-- Database: `aiocp`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_avatars`
-- 

DROP TABLE IF EXISTS `aiocp_avatars`;
CREATE TABLE `aiocp_avatars` (
  `avatar_id` int(10) unsigned NOT NULL auto_increment,
  `avatar_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `avatar_link` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `avatar_width` tinyint(3) unsigned NOT NULL default '0',
  `avatar_height` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`avatar_id`)
) TYPE=MyISAM COMMENT='Avatars images' AUTO_INCREMENT=264 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_awards`
-- 

DROP TABLE IF EXISTS `aiocp_awards`;
CREATE TABLE `aiocp_awards` (
  `award_id` int(10) unsigned NOT NULL auto_increment,
  `award_date` date NOT NULL default '0000-00-00',
  `award_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `award_link` varchar(255) collate utf8_unicode_ci default NULL,
  `award_logo` varchar(255) collate utf8_unicode_ci default NULL,
  `award_description` longtext collate utf8_unicode_ci,
  PRIMARY KEY  (`award_id`)
) TYPE=MyISAM COMMENT='Awards wons by this site' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_badwords`
-- 

DROP TABLE IF EXISTS `aiocp_badwords`;
CREATE TABLE `aiocp_badwords` (
  `badword_id` int(10) unsigned NOT NULL auto_increment,
  `badword_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`badword_id`)
) TYPE=MyISAM COMMENT='Words that must be censored' AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_banners`
-- 

DROP TABLE IF EXISTS `aiocp_banners`;
CREATE TABLE `aiocp_banners` (
  `banner_id` int(10) unsigned NOT NULL auto_increment,
  `banner_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `banner_customer_id` int(10) unsigned NOT NULL default '1',
  `banner_language` varchar(3) collate utf8_unicode_ci NOT NULL default '',
  `banner_enabled` tinyint(3) unsigned NOT NULL default '0',
  `banner_code` text collate utf8_unicode_ci NOT NULL,
  `banner_link` varchar(255) collate utf8_unicode_ci default NULL,
  `banner_zone` int(10) unsigned default '1',
  `banner_start_date` date default '0000-00-00',
  `banner_end_date` date default '0000-00-00',
  `banner_max_views` int(10) unsigned default '0',
  `banner_weight` int(10) unsigned default '0',
  `banner_cpm` float default '0',
  `banner_cpc` float default '0',
  `banner_views_stats` int(10) unsigned NOT NULL default '0',
  `banner_clicks_stats` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`banner_id`)
) TYPE=MyISAM COMMENT='Commercial banners data' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_banners_stats`
-- 

DROP TABLE IF EXISTS `aiocp_banners_stats`;
CREATE TABLE `aiocp_banners_stats` (
  `banstat_id` int(10) unsigned NOT NULL auto_increment,
  `banstat_banner_id` int(10) unsigned NOT NULL default '0',
  `banstat_action` tinyint(3) unsigned NOT NULL default '0',
  `banstat_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `banstat_page` int(10) unsigned default NULL,
  `banstat_user_id` int(10) unsigned default NULL,
  `banstat_user_ip` varchar(16) collate utf8_unicode_ci NOT NULL default '',
  `banstat_user_agent` int(10) unsigned default NULL,
  PRIMARY KEY  (`banstat_id`)
) TYPE=MyISAM COMMENT='Record banners statistic data' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_banners_zone`
-- 

DROP TABLE IF EXISTS `aiocp_banners_zone`;
CREATE TABLE `aiocp_banners_zone` (
  `banzone_id` int(10) unsigned NOT NULL auto_increment,
  `banzone_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`banzone_id`)
) TYPE=MyISAM COMMENT='Banner zone' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_button_styles`
-- 

DROP TABLE IF EXISTS `aiocp_button_styles`;
CREATE TABLE `aiocp_button_styles` (
  `buttonstyle_id` int(10) unsigned NOT NULL auto_increment,
  `buttonstyle_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `buttonstyle_imgdir` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `buttonstyle_cornerswidth` int(10) unsigned NOT NULL default '8',
  `buttonstyle_defaulttext` varchar(255) collate utf8_unicode_ci default NULL,
  `buttonstyle_font` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `buttonstyle_textsize` smallint(6) NOT NULL default '10',
  `buttonstyle_textalign` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `buttonstyle_height` smallint(6) NOT NULL default '-1',
  `buttonstyle_width` smallint(6) NOT NULL default '-1',
  `buttonstyle_gamma` float NOT NULL default '0',
  `buttonstyle_textcolor` varchar(7) collate utf8_unicode_ci NOT NULL default '',
  `buttonstyle_darkcolor` varchar(7) collate utf8_unicode_ci NOT NULL default '',
  `buttonstyle_lightcolor` varchar(7) collate utf8_unicode_ci NOT NULL default '',
  `buttonstyle_transparentcolor` varchar(7) collate utf8_unicode_ci NOT NULL default '',
  `buttonstyle_margin` int(11) NOT NULL default '3',
  `buttonstyle_horizontal` tinyint(3) unsigned NOT NULL default '1',
  `buttonstyle_usecache` tinyint(3) unsigned NOT NULL default '1',
  PRIMARY KEY  (`buttonstyle_id`)
) TYPE=MyISAM COMMENT='styles for generated graphic buttons' AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_calendar`
-- 

DROP TABLE IF EXISTS `aiocp_calendar`;
CREATE TABLE `aiocp_calendar` (
  `calendar_id` int(10) unsigned NOT NULL auto_increment,
  `calendar_category_id` int(10) unsigned NOT NULL default '1',
  `calendar_year` smallint(5) unsigned NOT NULL default '0',
  `calendar_month` tinyint(3) unsigned NOT NULL default '0',
  `calendar_day` tinyint(3) unsigned NOT NULL default '0',
  `calendar_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `calendar_text` longtext collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`calendar_id`)
) TYPE=MyISAM COMMENT='Site calendar' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_calendar_categories`
-- 

DROP TABLE IF EXISTS `aiocp_calendar_categories`;
CREATE TABLE `aiocp_calendar_categories` (
  `calcat_id` int(10) unsigned NOT NULL auto_increment,
  `calcat_item` tinyint(3) unsigned NOT NULL default '1',
  `calcat_sub_id` int(10) unsigned NOT NULL default '0',
  `calcat_position` int(10) unsigned NOT NULL default '1',
  `calcat_level` tinyint(3) unsigned NOT NULL default '0',
  `calcat_name` text collate utf8_unicode_ci NOT NULL,
  `calcat_description` longtext collate utf8_unicode_ci,
  PRIMARY KEY  (`calcat_id`)
) TYPE=MyISAM COMMENT='Calendar Categories' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_chat_messages`
-- 

DROP TABLE IF EXISTS `aiocp_chat_messages`;
CREATE TABLE `aiocp_chat_messages` (
  `msg_id` int(10) unsigned NOT NULL auto_increment,
  `msg_roomid` int(10) unsigned default '1',
  `msg_roomprivate` varchar(255) collate utf8_unicode_ci default NULL,
  `msg_user` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `msg_time` int(10) unsigned NOT NULL default '0',
  `msg_text` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`msg_id`)
) TYPE=MyISAM COMMENT='contain all chat messages and info' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_chat_online_users`
-- 

DROP TABLE IF EXISTS `aiocp_chat_online_users`;
CREATE TABLE `aiocp_chat_online_users` (
  `chatusers_username` varchar(32) collate utf8_unicode_ci NOT NULL default '',
  `chatusers_userid` int(10) unsigned NOT NULL default '0',
  `chatusers_roomid` int(10) unsigned NOT NULL default '0',
  `chatusers_roomprivate` varchar(32) collate utf8_unicode_ci default NULL,
  `chatusers_lasttime` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`chatusers_username`)
) TYPE=MyISAM COMMENT='Contain active users list on chat';

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_chat_rooms`
-- 

DROP TABLE IF EXISTS `aiocp_chat_rooms`;
CREATE TABLE `aiocp_chat_rooms` (
  `chatroom_id` int(10) unsigned NOT NULL auto_increment,
  `chatroom_language` varchar(3) collate utf8_unicode_ci NOT NULL default 'eng',
  `chatroom_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `chatroom_description` text collate utf8_unicode_ci NOT NULL,
  `chatroom_level` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`chatroom_id`)
) TYPE=MyISAM COMMENT='chat rooms data for all languages' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_countries`
-- 

DROP TABLE IF EXISTS `aiocp_countries`;
CREATE TABLE `aiocp_countries` (
  `country_id` int(10) unsigned NOT NULL auto_increment,
  `country_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `country_flag` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `country_width` tinyint(3) unsigned NOT NULL default '32',
  `country_height` tinyint(3) unsigned NOT NULL default '20',
  PRIMARY KEY  (`country_id`)
) TYPE=MyISAM COMMENT='Country table with flag images' AUTO_INCREMENT=195 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_countries_states`
-- 

DROP TABLE IF EXISTS `aiocp_countries_states`;
CREATE TABLE `aiocp_countries_states` (
  `state_id` int(10) unsigned NOT NULL auto_increment,
  `state_country_id` int(10) unsigned NOT NULL default '0',
  `state_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`state_id`)
) TYPE=MyISAM COMMENT='country states or provinces' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_dictionary_categories`
-- 

DROP TABLE IF EXISTS `aiocp_dictionary_categories`;
CREATE TABLE `aiocp_dictionary_categories` (
  `diccat_id` int(10) unsigned NOT NULL auto_increment,
  `diccat_language` varchar(3) collate utf8_unicode_ci NOT NULL default 'eng',
  `diccat_level` int(10) unsigned NOT NULL default '0',
  `diccat_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `diccat_description` longtext collate utf8_unicode_ci NOT NULL,
  `diccat_forum_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`diccat_id`)
) TYPE=MyISAM COMMENT='Dictionary Categories' AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_dictionary_words`
-- 

DROP TABLE IF EXISTS `aiocp_dictionary_words`;
CREATE TABLE `aiocp_dictionary_words` (
  `dicword_id` int(10) unsigned NOT NULL auto_increment,
  `dicword_category_id` int(10) unsigned NOT NULL default '1',
  `dicword_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `dicword_description` longtext collate utf8_unicode_ci NOT NULL,
  `dicword_correlates` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`dicword_id`)
) TYPE=MyISAM COMMENT='Dictionary Words' AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_downloads`
-- 

DROP TABLE IF EXISTS `aiocp_downloads`;
CREATE TABLE `aiocp_downloads` (
  `download_id` int(10) unsigned NOT NULL auto_increment,
  `download_category` int(10) unsigned NOT NULL default '0',
  `download_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `download_link` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `download_size` int(10) unsigned NOT NULL default '0',
  `download_description_small` text collate utf8_unicode_ci,
  `download_description_large` longtext collate utf8_unicode_ci,
  `download_publisher_name` varchar(255) collate utf8_unicode_ci default NULL,
  `download_publisher_link` varchar(255) collate utf8_unicode_ci default NULL,
  `download_date` date NOT NULL default '0000-00-00',
  `download_license` int(10) unsigned default NULL,
  `download_os` int(10) unsigned default NULL,
  `download_limitations` longtext collate utf8_unicode_ci,
  `download_requisite` text collate utf8_unicode_ci,
  `download_downloads` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`download_id`)
) TYPE=MyISAM COMMENT='File downloads information' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_downloads_categories`
-- 

DROP TABLE IF EXISTS `aiocp_downloads_categories`;
CREATE TABLE `aiocp_downloads_categories` (
  `downcat_id` int(10) unsigned NOT NULL auto_increment,
  `downcat_item` tinyint(3) unsigned NOT NULL default '1',
  `downcat_sub_id` int(10) unsigned NOT NULL default '0',
  `downcat_position` int(10) unsigned NOT NULL default '1',
  `downcat_level` tinyint(3) unsigned NOT NULL default '1',
  `downcat_name` text collate utf8_unicode_ci NOT NULL,
  `downcat_description` longtext collate utf8_unicode_ci,
  PRIMARY KEY  (`downcat_id`)
) TYPE=MyISAM COMMENT='Categories for news' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_ec_currency`
-- 

DROP TABLE IF EXISTS `aiocp_ec_currency`;
CREATE TABLE `aiocp_ec_currency` (
  `currency_id` int(10) unsigned NOT NULL auto_increment,
  `currency_iso_code_alpha` varchar(3) collate utf8_unicode_ci default NULL,
  `currency_iso_code_numeric` varchar(3) collate utf8_unicode_ci default NULL,
  `currency_uic_code` varchar(3) collate utf8_unicode_ci default NULL,
  `currency_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `currency_name_minor` varchar(255) collate utf8_unicode_ci default NULL,
  `currency_description` varchar(255) collate utf8_unicode_ci default NULL,
  `currency_unicode_symbol` varchar(255) collate utf8_unicode_ci default NULL,
  `currency_char_symbol` varchar(255) collate utf8_unicode_ci default NULL,
  `currency_decimals` tinyint(3) unsigned NOT NULL default '2',
  `currency_thousand_separator` char(1) collate utf8_unicode_ci NOT NULL default ',',
  `currency_decimals_separator` char(1) collate utf8_unicode_ci NOT NULL default '.',
  PRIMARY KEY  (`currency_id`)
) TYPE=MyISAM COMMENT='Currency table ISO 4217' AUTO_INCREMENT=175 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_ec_documents`
-- 

DROP TABLE IF EXISTS `aiocp_ec_documents`;
CREATE TABLE `aiocp_ec_documents` (
  `ecdoc_id` int(10) unsigned NOT NULL auto_increment,
  `ecdoc_type` int(10) unsigned NOT NULL default '0',
  `ecdoc_number` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `ecdoc_date` date NOT NULL default '0000-00-00',
  `ecdoc_user_id` int(10) unsigned NOT NULL default '0',
  `ecdoc_user_data` text collate utf8_unicode_ci NOT NULL,
  `ecdoc_payment_type_id` int(10) unsigned default NULL,
  `ecdoc_payment_details` text collate utf8_unicode_ci,
  `ecdoc_payment_date` date default NULL,
  `ecdoc_paid` tinyint(3) unsigned default '0',
  `ecdoc_validity` int(10) unsigned default NULL,
  `ecdoc_validity_unit` varchar(255) collate utf8_unicode_ci default NULL,
  `ecdoc_discount` float default NULL,
  `ecdoc_deduction` float default NULL,
  `ecdoc_deduction_from` float default NULL,
  `ecdoc_shipping_type_id` int(10) unsigned NOT NULL default '0',
  `ecdoc_subject` text collate utf8_unicode_ci,
  `ecdoc_notes_intro` text collate utf8_unicode_ci,
  `ecdoc_notes_end` text collate utf8_unicode_ci,
  `ecdoc_transport` tinyint(3) unsigned default '0',
  `ecdoc_driver_name` varchar(255) collate utf8_unicode_ci default NULL,
  `ecdoc_transport_subject` text collate utf8_unicode_ci,
  `ecdoc_parcels` int(10) unsigned default NULL,
  `ecdoc_parcels_aspect` text collate utf8_unicode_ci,
  `ecdoc_carriage` varchar(255) collate utf8_unicode_ci default NULL,
  `ecdoc_transport_start_time` datetime default NULL,
  `ecdoc_transport_net` float unsigned NOT NULL default '0',
  `ecdoc_transport_tax` float unsigned NOT NULL default '0',
  `ecdoc_transport_tax2` float default NULL,
  `ecdoc_transport_tax3` float default NULL,
  `ecdoc_payment_fee` float NOT NULL default '0',
  `ecdoc_expiry_time` int(10) unsigned default '0',
  `ecdoc_from_doc_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`ecdoc_id`)
) TYPE=MyISAM COMMENT='Commerce documents data' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_ec_documents_details`
-- 

DROP TABLE IF EXISTS `aiocp_ec_documents_details`;
CREATE TABLE `aiocp_ec_documents_details` (
  `docdet_id` int(10) unsigned NOT NULL auto_increment,
  `docdet_doc_id` int(10) unsigned NOT NULL default '0',
  `docdet_product_id` int(10) unsigned default NULL,
  `docdet_code` varchar(255) collate utf8_unicode_ci default NULL,
  `docdet_manufacturer_code` varchar(255) collate utf8_unicode_ci default NULL,
  `docdet_barcode` varchar(255) collate utf8_unicode_ci default NULL,
  `docdet_inventory_code` varchar(255) collate utf8_unicode_ci default NULL,
  `docdet_alternative_codes` text collate utf8_unicode_ci,
  `docdet_serial_numbers` text collate utf8_unicode_ci,
  `docdet_category_id` int(10) unsigned NOT NULL default '0',
  `docdet_manufacturer_id` int(10) unsigned NOT NULL default '0',
  `docdet_manufacturer_link` varchar(255) collate utf8_unicode_ci default NULL,
  `docdet_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `docdet_description` longtext collate utf8_unicode_ci,
  `docdet_warranty` smallint(5) unsigned default NULL,
  `docdet_warranty_id` int(10) unsigned default NULL,
  `docdet_image` varchar(255) collate utf8_unicode_ci default NULL,
  `docdet_transportable` tinyint(3) unsigned NOT NULL default '1',
  `docdet_download_link` varchar(255) collate utf8_unicode_ci default NULL,
  `docdet_weight_per_unit` float unsigned NOT NULL default '0',
  `docdet_length` float unsigned default NULL,
  `docdet_width` float unsigned default NULL,
  `docdet_height` float unsigned default NULL,
  `docdet_unit_of_measure_id` int(10) unsigned default '0',
  `docdet_cost` float NOT NULL default '0',
  `docdet_tax` float NOT NULL default '0',
  `docdet_tax2` float default NULL,
  `docdet_tax3` float default NULL,
  `docdet_quantity` float unsigned NOT NULL default '1',
  `docdet_discount` float unsigned NOT NULL default '0',
  PRIMARY KEY  (`docdet_id`)
) TYPE=MyISAM COMMENT='documents details (products details)' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_ec_documents_styles`
-- 

DROP TABLE IF EXISTS `aiocp_ec_documents_styles`;
CREATE TABLE `aiocp_ec_documents_styles` (
  `docstyle_id` int(10) unsigned NOT NULL auto_increment,
  `docstyle_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `docstyle_paper` varchar(255) collate utf8_unicode_ci default 'A4',
  `docstyle_width` float unsigned default NULL,
  `docstyle_height` float unsigned default NULL,
  `docstyle_orientation` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `docstyle_margin_top` int(10) unsigned NOT NULL default '20',
  `docstyle_margin_bottom` int(10) unsigned NOT NULL default '20',
  `docstyle_margin_left` int(10) unsigned NOT NULL default '20',
  `docstyle_margin_right` int(10) unsigned NOT NULL default '20',
  `docstyle_header` int(10) unsigned NOT NULL default '0',
  `docstyle_footer` int(10) unsigned NOT NULL default '0',
  `docstyle_main_font` varchar(255) collate utf8_unicode_ci NOT NULL default 'Helvetica',
  `docstyle_main_font_size` tinyint(3) unsigned NOT NULL default '12',
  `docstyle_data_font` varchar(255) collate utf8_unicode_ci NOT NULL default 'Helvetica',
  `docstyle_data_font_size` tinyint(3) unsigned NOT NULL default '12',
  `docstyle_image_width` float unsigned NOT NULL default '30',
  PRIMARY KEY  (`docstyle_id`)
) TYPE=MyISAM COMMENT='PDF document styles' AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_ec_documents_types`
-- 

DROP TABLE IF EXISTS `aiocp_ec_documents_types`;
CREATE TABLE `aiocp_ec_documents_types` (
  `doctype_id` int(10) unsigned NOT NULL auto_increment,
  `doctype_name` longtext collate utf8_unicode_ci NOT NULL,
  `doctype_style` int(10) unsigned NOT NULL default '1',
  `doctype_options` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`doctype_id`)
) TYPE=MyISAM COMMENT='Type of payments list (cash, cheque, ...)' AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_ec_manufacturers`
-- 

DROP TABLE IF EXISTS `aiocp_ec_manufacturers`;
CREATE TABLE `aiocp_ec_manufacturers` (
  `manuf_id` int(10) unsigned NOT NULL auto_increment,
  `manuf_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `manuf_url` varchar(255) collate utf8_unicode_ci default NULL,
  `manuf_logo` varchar(255) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`manuf_id`)
) TYPE=MyISAM COMMENT='Product''s manufacturers' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_ec_payments_categories`
-- 

DROP TABLE IF EXISTS `aiocp_ec_payments_categories`;
CREATE TABLE `aiocp_ec_payments_categories` (
  `paycat_id` int(10) unsigned NOT NULL auto_increment,
  `paycat_name` longtext collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`paycat_id`)
) TYPE=MyISAM COMMENT='Payment categories' AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_ec_payments_types`
-- 

DROP TABLE IF EXISTS `aiocp_ec_payments_types`;
CREATE TABLE `aiocp_ec_payments_types` (
  `paytype_id` int(10) unsigned NOT NULL auto_increment,
  `paytype_name` longtext collate utf8_unicode_ci NOT NULL,
  `paytype_description` longtext collate utf8_unicode_ci,
  `paytype_file_module` varchar(255) collate utf8_unicode_ci default NULL,
  `paytype_enabled` tinyint(3) unsigned NOT NULL default '0',
  `paytype_category_id` int(10) unsigned default NULL,
  `paytype_fee` float NOT NULL default '0',
  `paytype_feepercentage` float NOT NULL default '0',
  PRIMARY KEY  (`paytype_id`)
) TYPE=MyISAM COMMENT='Payment types' AUTO_INCREMENT=14 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_ec_products`
-- 

DROP TABLE IF EXISTS `aiocp_ec_products`;
CREATE TABLE `aiocp_ec_products` (
  `product_id` int(10) unsigned NOT NULL auto_increment,
  `product_code` varchar(255) collate utf8_unicode_ci default NULL,
  `product_manufacturer_code` varchar(255) collate utf8_unicode_ci default NULL,
  `product_barcode` varchar(255) collate utf8_unicode_ci default NULL,
  `product_inventory_code` varchar(255) collate utf8_unicode_ci default NULL,
  `product_alternative_codes` text collate utf8_unicode_ci,
  `product_category_id` int(10) unsigned NOT NULL default '0',
  `product_manufacturer_id` int(10) unsigned NOT NULL default '0',
  `product_manufacturer_link` varchar(255) collate utf8_unicode_ci default NULL,
  `product_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `product_description` longtext collate utf8_unicode_ci,
  `product_warranty` smallint(5) unsigned default '0',
  `product_warranty_id` int(10) unsigned default '0',
  `product_image` varchar(255) collate utf8_unicode_ci default NULL,
  `product_transportable` tinyint(3) unsigned NOT NULL default '1',
  `product_download_link` varchar(255) collate utf8_unicode_ci default NULL,
  `product_execute_module` varchar(255) collate utf8_unicode_ci default NULL,
  `product_weight_per_unit` float unsigned default NULL,
  `product_length` float unsigned default NULL,
  `product_width` float unsigned default NULL,
  `product_height` float unsigned default NULL,
  `product_unit_of_measure_id` int(10) unsigned default '0',
  `product_cost` float NOT NULL default '0',
  `product_tax` int(10) unsigned NOT NULL default '1',
  `product_tax2` int(10) unsigned NOT NULL default '0',
  `product_tax3` int(10) unsigned NOT NULL default '0',
  `product_q_sold` float NOT NULL default '0',
  `product_q_available` float default '0',
  `product_q_arriving` float NOT NULL default '0',
  `product_arriving_time` date default '0000-00-00',
  `product_date_added` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`product_id`)
) TYPE=MyISAM COMMENT='Products (e-commerce)' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_ec_products_categories`
-- 

DROP TABLE IF EXISTS `aiocp_ec_products_categories`;
CREATE TABLE `aiocp_ec_products_categories` (
  `prodcat_id` int(10) unsigned NOT NULL auto_increment,
  `prodcat_item` tinyint(3) unsigned NOT NULL default '1',
  `prodcat_sub_id` int(10) unsigned NOT NULL default '0',
  `prodcat_position` int(10) unsigned NOT NULL default '0',
  `prodcat_level` tinyint(3) unsigned NOT NULL default '0',
  `prodcat_code` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `prodcat_name` longtext collate utf8_unicode_ci,
  `prodcat_description` longtext collate utf8_unicode_ci,
  `prodcat_image` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`prodcat_id`)
) TYPE=MyISAM COMMENT='Product Categories' AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_ec_products_resources`
-- 

DROP TABLE IF EXISTS `aiocp_ec_products_resources`;
CREATE TABLE `aiocp_ec_products_resources` (
  `prodres_id` int(10) unsigned NOT NULL auto_increment,
  `prodres_product_id` int(10) unsigned NOT NULL default '0',
  `prodres_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `prodres_link` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `prodres_target` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`prodres_id`)
) TYPE=MyISAM COMMENT='Product''s external resources (updates, manuals...)' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_ec_shipping_types`
-- 

DROP TABLE IF EXISTS `aiocp_ec_shipping_types`;
CREATE TABLE `aiocp_ec_shipping_types` (
  `shipping_id` int(10) unsigned NOT NULL auto_increment,
  `shipping_name` longtext collate utf8_unicode_ci NOT NULL,
  `shipping_description` longtext collate utf8_unicode_ci,
  `shipping_file_module` varchar(255) collate utf8_unicode_ci default NULL,
  `shipping_enabled` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`shipping_id`)
) TYPE=MyISAM COMMENT='List of shipping types' AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_ec_shopping_cart`
-- 

DROP TABLE IF EXISTS `aiocp_ec_shopping_cart`;
CREATE TABLE `aiocp_ec_shopping_cart` (
  `cart_id` int(10) unsigned NOT NULL auto_increment,
  `cart_datetime` int(10) unsigned NOT NULL default '0',
  `cart_user_id` int(10) unsigned NOT NULL default '1',
  `cart_session_id` varchar(32) collate utf8_unicode_ci NOT NULL default '',
  `cart_product_id` int(10) unsigned NOT NULL default '0',
  `cart_quantity` int(10) unsigned NOT NULL default '1',
  PRIMARY KEY  (`cart_id`)
) TYPE=MyISAM COMMENT='user shopping cart' AUTO_INCREMENT=205 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_ec_shopping_cart_user_data`
-- 

DROP TABLE IF EXISTS `aiocp_ec_shopping_cart_user_data`;
CREATE TABLE `aiocp_ec_shopping_cart_user_data` (
  `scud_session_id` varchar(32) collate utf8_unicode_ci NOT NULL default '',
  `scud_user_id` int(10) unsigned NOT NULL default '1',
  `scud_payment_type_id` int(10) unsigned default NULL,
  `scud_payment_details` text collate utf8_unicode_ci,
  `scud_shipping_type_id` int(10) unsigned default NULL,
  `scud_comment` text collate utf8_unicode_ci,
  `scud_datetime` int(10) unsigned NOT NULL default '0',
  `scud_locked` tinyint(3) unsigned NOT NULL default '0',
  `scud_transaction_id` varchar(32) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`scud_session_id`)
) TYPE=MyISAM COMMENT='General shopping cart user data';

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_ec_transactions`
-- 

DROP TABLE IF EXISTS `aiocp_ec_transactions`;
CREATE TABLE `aiocp_ec_transactions` (
  `mtrans_id` int(10) unsigned NOT NULL auto_increment,
  `mtrans_date` date NOT NULL default '0000-00-00',
  `mtrans_type` int(10) unsigned NOT NULL default '0',
  `mtrans_description` text collate utf8_unicode_ci,
  `mtrans_doc_ref` varchar(255) collate utf8_unicode_ci default NULL,
  `mtrans_supplier` int(10) unsigned default NULL,
  `mtrans_direction` smallint(6) NOT NULL default '0',
  `mtrans_amount` float NOT NULL default '0',
  `mtrans_tax` float NOT NULL default '0',
  `mtrans_paid_amount` float unsigned NOT NULL default '0',
  `mtrans_work_id` int(10) unsigned default NULL,
  `mtrans_virtual` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`mtrans_id`)
) TYPE=MyISAM COMMENT='Money transactions through the cash fund' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_ec_transactions_payments`
-- 

DROP TABLE IF EXISTS `aiocp_ec_transactions_payments`;
CREATE TABLE `aiocp_ec_transactions_payments` (
  `transpay_id` int(10) unsigned NOT NULL auto_increment,
  `transpay_transaction_id` int(10) unsigned NOT NULL default '0',
  `transpay_date` date NOT NULL default '0000-00-00',
  `transpay_payment_id` int(10) unsigned NOT NULL default '0',
  `transpay_payment_details` text collate utf8_unicode_ci,
  `transpay_amount` float NOT NULL default '0',
  PRIMARY KEY  (`transpay_id`)
) TYPE=MyISAM COMMENT='Transactions payments details' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_ec_transactions_types`
-- 

DROP TABLE IF EXISTS `aiocp_ec_transactions_types`;
CREATE TABLE `aiocp_ec_transactions_types` (
  `transtype_id` int(10) unsigned NOT NULL auto_increment,
  `transtype_name` longtext collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`transtype_id`)
) TYPE=MyISAM COMMENT='List of commerce transactions types' AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_ec_units_of_measure`
-- 

DROP TABLE IF EXISTS `aiocp_ec_units_of_measure`;
CREATE TABLE `aiocp_ec_units_of_measure` (
  `unit_id` int(10) unsigned NOT NULL auto_increment,
  `unit_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `unit_discrete` tinyint(3) unsigned NOT NULL default '0',
  `unit_description` text collate utf8_unicode_ci,
  PRIMARY KEY  (`unit_id`)
) TYPE=MyISAM COMMENT='Unit of Measure list for Products' AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_ec_user_discount`
-- 

DROP TABLE IF EXISTS `aiocp_ec_user_discount`;
CREATE TABLE `aiocp_ec_user_discount` (
  `discount_id` int(10) unsigned NOT NULL auto_increment,
  `discount_userid` int(10) unsigned NOT NULL default '0',
  `discount_username` varchar(255) collate utf8_unicode_ci default NULL,
  `discount_value` float NOT NULL default '0',
  PRIMARY KEY  (`discount_id`)
) TYPE=MyISAM COMMENT='set discount percentage for some special users' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_ec_vat`
-- 

DROP TABLE IF EXISTS `aiocp_ec_vat`;
CREATE TABLE `aiocp_ec_vat` (
  `vat_id` int(10) unsigned NOT NULL auto_increment,
  `vat_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `vat_consumer` longtext collate utf8_unicode_ci,
  `vat_company` longtext collate utf8_unicode_ci,
  `vat_cmptype` longtext collate utf8_unicode_ci,
  PRIMARY KEY  (`vat_id`)
) TYPE=MyISAM COMMENT='Value Addict Tax Table' AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_ec_warranties`
-- 

DROP TABLE IF EXISTS `aiocp_ec_warranties`;
CREATE TABLE `aiocp_ec_warranties` (
  `warranty_id` int(10) unsigned NOT NULL auto_increment,
  `warranty_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `warranty_description` longtext collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`warranty_id`)
) TYPE=MyISAM COMMENT='Products warranties statements' AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_ec_works`
-- 

DROP TABLE IF EXISTS `aiocp_ec_works`;
CREATE TABLE `aiocp_ec_works` (
  `work_id` int(10) unsigned NOT NULL auto_increment,
  `work_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `work_date_start` date default NULL,
  `work_date_end` date default NULL,
  `work_description` longtext collate utf8_unicode_ci,
  PRIMARY KEY  (`work_id`)
) TYPE=MyISAM COMMENT='List of works (used in transactions)' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_emoticons`
-- 

DROP TABLE IF EXISTS `aiocp_emoticons`;
CREATE TABLE `aiocp_emoticons` (
  `smile_id` int(10) unsigned NOT NULL auto_increment,
  `smile_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `smile_link` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `smile_width` tinyint(3) unsigned NOT NULL default '15',
  `smile_height` tinyint(3) unsigned NOT NULL default '15',
  PRIMARY KEY  (`smile_id`)
) TYPE=MyISAM COMMENT='Smiles images' AUTO_INCREMENT=32 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_forum_categories`
-- 

DROP TABLE IF EXISTS `aiocp_forum_categories`;
CREATE TABLE `aiocp_forum_categories` (
  `forumcat_id` int(10) unsigned NOT NULL auto_increment,
  `forumcat_language` varchar(3) collate utf8_unicode_ci NOT NULL default 'eng',
  `forumcat_postinglevel` tinyint(3) unsigned NOT NULL default '1',
  `forumcat_readinglevel` tinyint(3) unsigned NOT NULL default '1',
  `forumcat_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `forumcat_description` text collate utf8_unicode_ci,
  `forumcat_order` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`forumcat_id`)
) TYPE=MyISAM COMMENT='Categories for forums' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_forum_forums`
-- 

DROP TABLE IF EXISTS `aiocp_forum_forums`;
CREATE TABLE `aiocp_forum_forums` (
  `forum_id` int(10) unsigned NOT NULL auto_increment,
  `forum_language` varchar(3) collate utf8_unicode_ci NOT NULL default 'eng',
  `forum_categoryid` int(10) unsigned NOT NULL default '0',
  `forum_readinglevel` tinyint(3) unsigned NOT NULL default '1',
  `forum_postinglevel` tinyint(3) unsigned NOT NULL default '1',
  `forum_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `forum_description` text collate utf8_unicode_ci,
  `forum_status` tinyint(3) unsigned NOT NULL default '0',
  `forum_order` int(10) unsigned NOT NULL default '0',
  `forum_edittimelimit` int(10) unsigned NOT NULL default '1',
  `forum_lockthread` int(10) unsigned NOT NULL default '20',
  `forum_removezeroreply` int(10) unsigned NOT NULL default '0',
  `forum_userconfirmation` text collate utf8_unicode_ci,
  `forum_topics` int(10) unsigned NOT NULL default '0',
  `forum_posts` int(10) unsigned NOT NULL default '0',
  `forum_lasttopic` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`forum_id`)
) TYPE=MyISAM COMMENT='Forums (main arguments)' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_forum_moderators`
-- 

DROP TABLE IF EXISTS `aiocp_forum_moderators`;
CREATE TABLE `aiocp_forum_moderators` (
  `moderator_id` int(10) unsigned NOT NULL auto_increment,
  `moderator_forumid` int(10) unsigned NOT NULL default '0',
  `moderator_categoryid` int(10) unsigned NOT NULL default '0',
  `moderator_userid` int(10) unsigned NOT NULL default '0',
  `moderator_email` varchar(255) collate utf8_unicode_ci default NULL,
  `moderator_confirmation` text collate utf8_unicode_ci,
  `moderator_options` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`moderator_id`)
) TYPE=MyISAM COMMENT='Moderators table with options' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_forum_posts`
-- 

DROP TABLE IF EXISTS `aiocp_forum_posts`;
CREATE TABLE `aiocp_forum_posts` (
  `forumposts_id` int(10) unsigned NOT NULL auto_increment,
  `forumposts_topicid` int(10) unsigned NOT NULL default '0',
  `forumposts_forumid` int(10) unsigned NOT NULL default '0',
  `forumposts_categoryid` int(10) unsigned NOT NULL default '0',
  `forumposts_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `forumposts_poster` int(10) unsigned NOT NULL default '1',
  `forumposts_posterip` varchar(15) collate utf8_unicode_ci NOT NULL default '',
  `forumposts_text` mediumtext collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`forumposts_id`)
) TYPE=MyISAM COMMENT='Forum Posts' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_forum_topics`
-- 

DROP TABLE IF EXISTS `aiocp_forum_topics`;
CREATE TABLE `aiocp_forum_topics` (
  `forumtopic_id` int(10) unsigned NOT NULL auto_increment,
  `forumtopic_forumid` int(10) unsigned NOT NULL default '0',
  `forumtopic_categoryid` int(10) unsigned NOT NULL default '0',
  `forumtopic_status` tinyint(3) unsigned NOT NULL default '0',
  `forumtopic_title` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `forumtopic_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `forumtopic_poster` int(10) unsigned NOT NULL default '1',
  `forumtopic_views` int(10) unsigned NOT NULL default '0',
  `forumtopic_replies` int(10) unsigned NOT NULL default '0',
  `forumtopic_lastpost` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`forumtopic_id`)
) TYPE=MyISAM COMMENT='Forum Topics' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_frame_targets`
-- 

DROP TABLE IF EXISTS `aiocp_frame_targets`;
CREATE TABLE `aiocp_frame_targets` (
  `target_id` int(10) unsigned NOT NULL auto_increment,
  `target_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`target_id`)
) TYPE=MyISAM COMMENT='Target names for links' AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_icons`
-- 

DROP TABLE IF EXISTS `aiocp_icons`;
CREATE TABLE `aiocp_icons` (
  `icon_id` int(10) unsigned NOT NULL auto_increment,
  `icon_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `icon_link` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `icon_width` tinyint(3) unsigned NOT NULL default '0',
  `icon_height` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`icon_id`)
) TYPE=MyISAM COMMENT='Icons images for menu' AUTO_INCREMENT=116 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_icons_client`
-- 

DROP TABLE IF EXISTS `aiocp_icons_client`;
CREATE TABLE `aiocp_icons_client` (
  `icon_id` int(10) unsigned NOT NULL auto_increment,
  `icon_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `icon_link` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `icon_width` tinyint(3) unsigned NOT NULL default '0',
  `icon_height` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`icon_id`)
) TYPE=MyISAM COMMENT='Icons images for client menu' AUTO_INCREMENT=150 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_language_codes`
-- 

DROP TABLE IF EXISTS `aiocp_language_codes`;
CREATE TABLE `aiocp_language_codes` (
  `language_code` varchar(3) collate utf8_unicode_ci NOT NULL default '',
  `language_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `language_enabled` tinyint(3) unsigned default '0',
  PRIMARY KEY  (`language_code`)
) TYPE=MyISAM COMMENT='MARC Language Codes';

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_language_data`
-- 

DROP TABLE IF EXISTS `aiocp_language_data`;
CREATE TABLE `aiocp_language_data` (
  `word_id` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `eng` longtext collate utf8_unicode_ci,
  `ita` longtext collate utf8_unicode_ci,
  PRIMARY KEY  (`word_id`)
) TYPE=MyISAM COMMENT='General text templates translated in all languages';

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_language_help`
-- 

DROP TABLE IF EXISTS `aiocp_language_help`;
CREATE TABLE `aiocp_language_help` (
  `help_id` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `eng` longtext collate utf8_unicode_ci,
  `ita` longtext collate utf8_unicode_ci,
  PRIMARY KEY  (`help_id`)
) TYPE=MyISAM COMMENT='Help templates translated in all languages';

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_language_pages`
-- 

DROP TABLE IF EXISTS `aiocp_language_pages`;
CREATE TABLE `aiocp_language_pages` (
  `template_id` int(10) unsigned NOT NULL auto_increment,
  `page` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `template` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `eng` longtext collate utf8_unicode_ci,
  `ita` longtext collate utf8_unicode_ci,
  PRIMARY KEY  (`template_id`)
) TYPE=MyISAM COMMENT='Pages templates translated in all languages' AUTO_INCREMENT=683 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_levels`
-- 

DROP TABLE IF EXISTS `aiocp_levels`;
CREATE TABLE `aiocp_levels` (
  `level_id` tinyint(3) unsigned NOT NULL auto_increment,
  `level_code` int(10) unsigned NOT NULL default '1',
  `level_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `level_description` text collate utf8_unicode_ci,
  `level_image` varchar(255) collate utf8_unicode_ci default NULL,
  `level_width` tinyint(3) unsigned NOT NULL default '60',
  `level_height` tinyint(3) unsigned NOT NULL default '10',
  PRIMARY KEY  (`level_id`)
) TYPE=MyISAM COMMENT='Define the user level' AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_links`
-- 

DROP TABLE IF EXISTS `aiocp_links`;
CREATE TABLE `aiocp_links` (
  `links_id` int(10) unsigned NOT NULL auto_increment,
  `links_category` smallint(5) unsigned NOT NULL default '1',
  `links_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `links_link` varchar(255) collate utf8_unicode_ci default NULL,
  `links_description` mediumtext collate utf8_unicode_ci NOT NULL,
  `links_status` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`links_id`)
) TYPE=MyISAM COMMENT='Contain all links of different categories' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_links_categories`
-- 

DROP TABLE IF EXISTS `aiocp_links_categories`;
CREATE TABLE `aiocp_links_categories` (
  `linkscat_id` smallint(5) unsigned NOT NULL auto_increment,
  `linkscat_item` tinyint(3) unsigned NOT NULL default '1',
  `linkscat_sub_id` int(10) unsigned NOT NULL default '0',
  `linkscat_position` int(10) unsigned NOT NULL default '1',
  `linkscat_name` text collate utf8_unicode_ci NOT NULL,
  `linkscat_target` tinyint(3) unsigned NOT NULL default '1',
  `linkscat_description` longtext collate utf8_unicode_ci,
  PRIMARY KEY  (`linkscat_id`)
) TYPE=MyISAM COMMENT='Categories for Links' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_menu`
-- 

DROP TABLE IF EXISTS `aiocp_menu`;
CREATE TABLE `aiocp_menu` (
  `menu_id` int(10) unsigned NOT NULL auto_increment,
  `menu_language` varchar(3) collate utf8_unicode_ci NOT NULL default 'eng',
  `menu_item` tinyint(3) unsigned default '1',
  `menu_sub_id` int(10) unsigned default '0',
  `menu_position` int(10) unsigned NOT NULL default '1',
  `menu_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `menu_description` varchar(255) collate utf8_unicode_ci default NULL,
  `menu_link` varchar(255) collate utf8_unicode_ci default NULL,
  `menu_target` tinyint(3) unsigned NOT NULL default '5',
  `menu_iconid` int(10) unsigned NOT NULL default '1',
  `menu_icon_off` int(10) unsigned NOT NULL default '1',
  `menu_icon_over` int(10) unsigned NOT NULL default '1',
  `menu_icon_on` int(10) unsigned NOT NULL default '1',
  `menu_enabled` tinyint(3) unsigned NOT NULL default '1',
  `menu_style_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`menu_id`)
) TYPE=MyISAM COMMENT='The AIOCP administrator menu' AUTO_INCREMENT=1838 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_menu_client`
-- 

DROP TABLE IF EXISTS `aiocp_menu_client`;
CREATE TABLE `aiocp_menu_client` (
  `menu_id` int(10) unsigned NOT NULL auto_increment,
  `menu_language` varchar(3) collate utf8_unicode_ci NOT NULL default 'eng',
  `menu_item` tinyint(3) unsigned default '1',
  `menu_sub_id` int(10) unsigned default '0',
  `menu_position` int(10) unsigned NOT NULL default '1',
  `menu_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `menu_description` varchar(255) collate utf8_unicode_ci default NULL,
  `menu_link` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `menu_target` tinyint(3) unsigned NOT NULL default '5',
  `menu_iconid` int(10) unsigned NOT NULL default '1',
  `menu_icon_off` int(10) unsigned NOT NULL default '1',
  `menu_icon_over` int(10) unsigned NOT NULL default '1',
  `menu_icon_on` int(10) unsigned NOT NULL default '1',
  `menu_enabled` tinyint(3) unsigned NOT NULL default '1',
  `menu_style_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`menu_id`)
) TYPE=MyISAM COMMENT='The default (main) client menu' AUTO_INCREMENT=178 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_menu_list`
-- 

DROP TABLE IF EXISTS `aiocp_menu_list`;
CREATE TABLE `aiocp_menu_list` (
  `menulst_id` int(10) unsigned NOT NULL auto_increment,
  `menulst_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `menulst_option` int(10) unsigned NOT NULL default '0',
  `menulst_style` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`menulst_id`)
) TYPE=MyISAM COMMENT='list of client menus' AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_menu_options`
-- 

DROP TABLE IF EXISTS `aiocp_menu_options`;
CREATE TABLE `aiocp_menu_options` (
  `menuopt_id` int(10) unsigned NOT NULL auto_increment,
  `menuopt_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `menuopt_horizontal` tinyint(3) unsigned NOT NULL default '0',
  `menuopt_autoscroll` tinyint(3) unsigned NOT NULL default '0',
  `menuopt_text` tinyint(3) unsigned NOT NULL default '1',
  `menuopt_width` int(10) unsigned NOT NULL default '0',
  `menuopt_height` int(10) unsigned NOT NULL default '0',
  `menuopt_hspace` int(10) unsigned default NULL,
  `menuopt_vspace` int(10) unsigned default NULL,
  `menuopt_align` varchar(255) collate utf8_unicode_ci default NULL,
  `menuopt_arrow_position` varchar(255) collate utf8_unicode_ci default NULL,
  `menuopt_popup_position` varchar(255) collate utf8_unicode_ci default NULL,
  `menuopt_target` int(10) unsigned NOT NULL default '1',
  PRIMARY KEY  (`menuopt_id`)
) TYPE=MyISAM COMMENT='menu options' AUTO_INCREMENT=1003 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_menu_styles`
-- 

DROP TABLE IF EXISTS `aiocp_menu_styles`;
CREATE TABLE `aiocp_menu_styles` (
  `menustyle_id` int(10) unsigned NOT NULL auto_increment,
  `menustyle_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `menustyle_label_position` varchar(255) collate utf8_unicode_ci NOT NULL default 'RIGHT',
  `menustyle_center_block` tinyint(3) unsigned NOT NULL default '0',
  `menustyle_padding` tinyint(3) unsigned NOT NULL default '1',
  `menustyle_gap` tinyint(3) unsigned NOT NULL default '2',
  `menustyle_border_width` tinyint(3) unsigned NOT NULL default '0',
  `menustyle_pushed` tinyint(3) unsigned NOT NULL default '1',
  `menustyle_sound_over` int(10) unsigned NOT NULL default '1',
  `menustyle_sound_click` int(10) unsigned NOT NULL default '1',
  `menustyle_background_col` varchar(7) collate utf8_unicode_ci NOT NULL default '#FFFFFF',
  `menustyle_colbck_off` varchar(7) collate utf8_unicode_ci NOT NULL default '#ECE9D8',
  `menustyle_colbck_over` varchar(7) collate utf8_unicode_ci NOT NULL default '#3366CC',
  `menustyle_colbck_on` varchar(7) collate utf8_unicode_ci NOT NULL default '#ADAA99',
  `menustyle_coltxt_off` varchar(7) collate utf8_unicode_ci NOT NULL default '#000000',
  `menustyle_coltxt_over` varchar(7) collate utf8_unicode_ci NOT NULL default '#FFFFFF',
  `menustyle_coltxt_on` varchar(7) collate utf8_unicode_ci NOT NULL default '#000000',
  `menustyle_colsdw_off` varchar(7) collate utf8_unicode_ci NOT NULL default '#FFFFFF',
  `menustyle_colsdw_over` varchar(7) collate utf8_unicode_ci NOT NULL default '#FFFFFF',
  `menustyle_colsdw_on` varchar(7) collate utf8_unicode_ci NOT NULL default '#FFFFFF',
  `menustyle_shadow_x` tinyint(4) NOT NULL default '0',
  `menustyle_shadow_y` tinyint(4) NOT NULL default '0',
  `menustyle_main_font` varchar(255) collate utf8_unicode_ci NOT NULL default 'Helvetica',
  `menustyle_main_font_style` varchar(255) collate utf8_unicode_ci NOT NULL default 'PLAIN',
  `menustyle_main_font_size` int(10) unsigned NOT NULL default '11',
  `menustyle_submenu_font` varchar(255) collate utf8_unicode_ci NOT NULL default 'Helvetica',
  `menustyle_submenu_font_style` varchar(255) collate utf8_unicode_ci NOT NULL default 'PLAIN',
  `menustyle_submenu_font_size` int(10) unsigned NOT NULL default '11',
  `menustyle_bck_img_off` int(10) unsigned NOT NULL default '1',
  `menustyle_bck_img_over` int(10) unsigned NOT NULL default '1',
  `menustyle_bck_img_on` int(10) unsigned NOT NULL default '1',
  `menustyle_icon_off` int(10) unsigned NOT NULL default '1',
  `menustyle_icon_over` int(10) unsigned NOT NULL default '1',
  `menustyle_icon_on` int(10) unsigned NOT NULL default '1',
  `menustyle_arrow_img_off_left` int(10) unsigned NOT NULL default '1',
  `menustyle_arrow_img_over_left` int(10) unsigned NOT NULL default '1',
  `menustyle_arrow_img_on_left` int(10) unsigned NOT NULL default '1',
  `menustyle_arrow_img_off_right` int(10) unsigned NOT NULL default '1',
  `menustyle_arrow_img_over_right` int(10) unsigned NOT NULL default '1',
  `menustyle_arrow_img_on_right` int(10) unsigned NOT NULL default '1',
  `menustyle_arrow_img_off_top` int(10) unsigned NOT NULL default '1',
  `menustyle_arrow_img_over_top` int(10) unsigned NOT NULL default '1',
  `menustyle_arrow_img_on_top` int(10) unsigned NOT NULL default '1',
  `menustyle_arrow_img_off_bottom` int(10) unsigned NOT NULL default '1',
  `menustyle_arrow_img_over_bottom` int(10) unsigned NOT NULL default '1',
  `menustyle_arrow_img_on_bottom` int(10) unsigned NOT NULL default '1',
  PRIMARY KEY  (`menustyle_id`)
) TYPE=MyISAM COMMENT='define graphic styles for menus and menus items' AUTO_INCREMENT=1003 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_mime`
-- 

DROP TABLE IF EXISTS `aiocp_mime`;
CREATE TABLE `aiocp_mime` (
  `mime_extension` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `mime_content` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`mime_extension`)
) TYPE=MyISAM COMMENT='MIME content type for each file extension';

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_news`
-- 

DROP TABLE IF EXISTS `aiocp_news`;
CREATE TABLE `aiocp_news` (
  `news_id` int(10) unsigned NOT NULL auto_increment,
  `news_category` smallint(5) unsigned NOT NULL default '1',
  `news_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `news_title` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `news_editorid` int(10) unsigned NOT NULL default '1',
  `news_author_name` varchar(255) collate utf8_unicode_ci default NULL,
  `news_author_email` varchar(255) collate utf8_unicode_ci default NULL,
  `news_source_name` varchar(255) collate utf8_unicode_ci default NULL,
  `news_source_link` varchar(255) collate utf8_unicode_ci default NULL,
  `news_text` mediumtext collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`news_id`)
) TYPE=MyISAM COMMENT='Contain all news of different categories' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_news_categories`
-- 

DROP TABLE IF EXISTS `aiocp_news_categories`;
CREATE TABLE `aiocp_news_categories` (
  `newscat_id` int(10) unsigned NOT NULL auto_increment,
  `newscat_language` varchar(3) collate utf8_unicode_ci NOT NULL default 'eng',
  `newscat_level` tinyint(3) unsigned NOT NULL default '1',
  `newscat_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `newscat_description` text collate utf8_unicode_ci,
  PRIMARY KEY  (`newscat_id`)
) TYPE=MyISAM COMMENT='Categories for news' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_newsletter_attachments`
-- 

DROP TABLE IF EXISTS `aiocp_newsletter_attachments`;
CREATE TABLE `aiocp_newsletter_attachments` (
  `nlattach_id` int(10) unsigned NOT NULL auto_increment,
  `nlattach_nlmsgid` int(10) unsigned NOT NULL default '0',
  `nlattach_file` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `nlattach_cid` varchar(32) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`nlattach_id`)
) TYPE=MyISAM COMMENT='Attached files in newsletter messages' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_newsletter_categories`
-- 

DROP TABLE IF EXISTS `aiocp_newsletter_categories`;
CREATE TABLE `aiocp_newsletter_categories` (
  `nlcat_id` int(10) unsigned NOT NULL auto_increment,
  `nlcat_language` varchar(3) collate utf8_unicode_ci NOT NULL default 'eng',
  `nlcat_level` tinyint(3) unsigned NOT NULL default '1',
  `nlcat_admin_email` varchar(64) collate utf8_unicode_ci default NULL,
  `nlcat_informfor` tinyint(3) unsigned NOT NULL default '0',
  `nlcat_msg_admin` text collate utf8_unicode_ci NOT NULL,
  `nlcat_sender` varchar(64) collate utf8_unicode_ci NOT NULL default '',
  `nlcat_fromemail` varchar(64) collate utf8_unicode_ci default NULL,
  `nlcat_fromname` varchar(64) collate utf8_unicode_ci NOT NULL default '',
  `nlcat_replyemail` varchar(64) collate utf8_unicode_ci default NULL,
  `nlcat_replyname` varchar(64) collate utf8_unicode_ci NOT NULL default '',
  `nlcat_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `nlcat_description` text collate utf8_unicode_ci,
  `nlcat_msg_header` text collate utf8_unicode_ci,
  `nlcat_msg_footer` text collate utf8_unicode_ci,
  `nlcat_msg_confirmation` text collate utf8_unicode_ci,
  `nlcat_enabled` tinyint(3) unsigned default '1',
  `nlcat_all_users` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`nlcat_id`)
) TYPE=MyISAM COMMENT='Newsletter categories' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_newsletter_messages`
-- 

DROP TABLE IF EXISTS `aiocp_newsletter_messages`;
CREATE TABLE `aiocp_newsletter_messages` (
  `nlmsg_id` int(10) unsigned NOT NULL auto_increment,
  `nlmsg_nlcatid` int(10) unsigned NOT NULL default '0',
  `nlmsg_editorid` int(10) unsigned NOT NULL default '0',
  `nlmsg_title` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `nlmsg_message` longtext collate utf8_unicode_ci NOT NULL,
  `nlmsg_composedate` int(10) unsigned NOT NULL default '0',
  `nlmsg_sentdate` int(10) unsigned default NULL,
  PRIMARY KEY  (`nlmsg_id`)
) TYPE=MyISAM COMMENT='Newsletter messages' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_newsletter_users`
-- 

DROP TABLE IF EXISTS `aiocp_newsletter_users`;
CREATE TABLE `aiocp_newsletter_users` (
  `nluser_id` int(10) unsigned NOT NULL auto_increment,
  `nluser_nlcatid` int(10) unsigned NOT NULL default '0',
  `nluser_userid` int(10) unsigned NOT NULL default '1',
  `nluser_userip` varchar(15) collate utf8_unicode_ci NOT NULL default '',
  `nluser_email` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `nluser_signupdate` int(10) unsigned NOT NULL default '0',
  `nluser_verifycode` varchar(32) collate utf8_unicode_ci NOT NULL default '',
  `nluser_enabled` tinyint(3) unsigned default '0',
  PRIMARY KEY  (`nluser_id`)
) TYPE=MyISAM COMMENT='Newsletter users list' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_page_data`
-- 

DROP TABLE IF EXISTS `aiocp_page_data`;
CREATE TABLE `aiocp_page_data` (
  `pagedata_id` int(10) unsigned NOT NULL auto_increment,
  `pagedata_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `pagedata_level` int(10) unsigned NOT NULL default '0',
  `pagedata_author` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `pagedata_replyto` varchar(255) collate utf8_unicode_ci default NULL,
  `pagedata_style` varchar(255) collate utf8_unicode_ci default NULL,
  `pagedata_hf` int(10) unsigned NOT NULL default '1',
  `pagedata_enabled` tinyint(3) unsigned NOT NULL default '0',
  `pagedata_timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`pagedata_id`)
) TYPE=MyISAM COMMENT='dynamic pages data' AUTO_INCREMENT=129 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_page_hf`
-- 

DROP TABLE IF EXISTS `aiocp_page_hf`;
CREATE TABLE `aiocp_page_hf` (
  `pagehf_id` int(10) unsigned NOT NULL auto_increment,
  `pagehf_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `pagehf_header` mediumtext collate utf8_unicode_ci,
  `pagehf_footer` mediumtext collate utf8_unicode_ci,
  PRIMARY KEY  (`pagehf_id`)
) TYPE=MyISAM COMMENT='Page Headers and Footers (page layouts)' AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_page_modules`
-- 

DROP TABLE IF EXISTS `aiocp_page_modules`;
CREATE TABLE `aiocp_page_modules` (
  `pagemod_id` int(10) unsigned NOT NULL auto_increment,
  `pagemod_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `pagemod_params` int(10) unsigned NOT NULL default '0',
  `pagemod_code` longtext collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`pagemod_id`)
) TYPE=MyISAM COMMENT='page modules (php or html)' AUTO_INCREMENT=84 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_polls`
-- 

DROP TABLE IF EXISTS `aiocp_polls`;
CREATE TABLE `aiocp_polls` (
  `poll_id` int(10) unsigned NOT NULL auto_increment,
  `poll_language` varchar(3) collate utf8_unicode_ci NOT NULL default 'eng',
  `poll_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `poll_description` text collate utf8_unicode_ci NOT NULL,
  `poll_date_start` int(10) unsigned default '0',
  `poll_date_end` int(10) unsigned default '0',
  `poll_level` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`poll_id`)
) TYPE=MyISAM COMMENT='Pools names and descriptions' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_polls_options`
-- 

DROP TABLE IF EXISTS `aiocp_polls_options`;
CREATE TABLE `aiocp_polls_options` (
  `polloption_id` int(10) unsigned NOT NULL auto_increment,
  `polloption_pollid` int(10) unsigned NOT NULL default '0',
  `polloption_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`polloption_id`)
) TYPE=MyISAM COMMENT='pool options (yes, no...)' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_polls_votes`
-- 

DROP TABLE IF EXISTS `aiocp_polls_votes`;
CREATE TABLE `aiocp_polls_votes` (
  `pollvote_id` int(10) unsigned NOT NULL auto_increment,
  `pollvote_pollid` int(10) unsigned NOT NULL default '0',
  `pollvote_optionid` int(10) unsigned NOT NULL default '0',
  `pollvote_userid` int(10) unsigned NOT NULL default '0',
  `pollvote_userip` varchar(16) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`pollvote_id`)
) TYPE=MyISAM COMMENT='polls votes' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_reviews`
-- 

DROP TABLE IF EXISTS `aiocp_reviews`;
CREATE TABLE `aiocp_reviews` (
  `review_id` int(10) unsigned NOT NULL auto_increment,
  `review_category` int(10) unsigned NOT NULL default '0',
  `review_date` date NOT NULL default '0000-00-00',
  `review_author_name` varchar(255) collate utf8_unicode_ci default NULL,
  `review_author_email` varchar(255) collate utf8_unicode_ci default NULL,
  `review_product_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `review_product_link` varchar(255) collate utf8_unicode_ci default NULL,
  `review_manuf_name` varchar(255) collate utf8_unicode_ci default NULL,
  `review_manuf_link` varchar(255) collate utf8_unicode_ci default NULL,
  `review_rating` tinyint(3) unsigned NOT NULL default '0',
  `review_image` varchar(255) collate utf8_unicode_ci default NULL,
  `review_text` longtext collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`review_id`)
) TYPE=MyISAM COMMENT='Products Reviews' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_reviews_categories`
-- 

DROP TABLE IF EXISTS `aiocp_reviews_categories`;
CREATE TABLE `aiocp_reviews_categories` (
  `revcat_id` int(10) unsigned NOT NULL auto_increment,
  `revcat_item` tinyint(3) unsigned NOT NULL default '1',
  `revcat_sub_id` int(10) unsigned NOT NULL default '0',
  `revcat_position` int(10) unsigned NOT NULL default '1',
  `revcat_level` tinyint(3) unsigned NOT NULL default '0',
  `revcat_name` text collate utf8_unicode_ci NOT NULL,
  `revcat_description` longtext collate utf8_unicode_ci,
  `revcat_image` varchar(255) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`revcat_id`)
) TYPE=MyISAM COMMENT='Review Categories' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_search_dictionary`
-- 

DROP TABLE IF EXISTS `aiocp_search_dictionary`;
CREATE TABLE `aiocp_search_dictionary` (
  `searchdic_url_id` int(10) unsigned NOT NULL default '0',
  `searchdic_word` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `searchdic_position` int(10) unsigned NOT NULL default '0'
) TYPE=MyISAM COMMENT='all words found during site indexing';

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_search_url`
-- 

DROP TABLE IF EXISTS `aiocp_search_url`;
CREATE TABLE `aiocp_search_url` (
  `searchurl_id` int(10) unsigned NOT NULL auto_increment,
  `searchurl_url` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `searchurl_level` int(10) unsigned NOT NULL default '0',
  `searchurl_content_type` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `searchurl_title` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `searchurl_description` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `searchurl_keywords` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `searchurl_language` varchar(3) collate utf8_unicode_ci NOT NULL default '',
  `searchurl_size` int(10) unsigned NOT NULL default '0',
  `searchurl_index_time` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`searchurl_id`)
) TYPE=MyISAM COMMENT='list of indexed site URLs' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_sessions`
-- 

DROP TABLE IF EXISTS `aiocp_sessions`;
CREATE TABLE `aiocp_sessions` (
  `cpsession_id` varchar(32) collate utf8_unicode_ci NOT NULL default '',
  `cpsession_expiry` int(10) unsigned NOT NULL default '0',
  `cpsession_data` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`cpsession_id`)
) TYPE=MyISAM COMMENT='Handle user session data';

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_software_licenses`
-- 

DROP TABLE IF EXISTS `aiocp_software_licenses`;
CREATE TABLE `aiocp_software_licenses` (
  `license_id` int(10) unsigned NOT NULL auto_increment,
  `license_free` tinyint(3) unsigned NOT NULL default '1',
  `license_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `license_link` varchar(255) collate utf8_unicode_ci default NULL,
  `license_description` text collate utf8_unicode_ci,
  PRIMARY KEY  (`license_id`)
) TYPE=MyISAM COMMENT='Software Licenses' AUTO_INCREMENT=64 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_software_os`
-- 

DROP TABLE IF EXISTS `aiocp_software_os`;
CREATE TABLE `aiocp_software_os` (
  `os_id` int(10) unsigned NOT NULL auto_increment,
  `os_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `os_link` varchar(255) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`os_id`)
) TYPE=MyISAM COMMENT='Operative Systems' AUTO_INCREMENT=30 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_sound_effects`
-- 

DROP TABLE IF EXISTS `aiocp_sound_effects`;
CREATE TABLE `aiocp_sound_effects` (
  `sound_id` int(10) unsigned NOT NULL auto_increment,
  `sound_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `sound_link` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`sound_id`)
) TYPE=MyISAM COMMENT='Sound effects for menus' AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_stats`
-- 

DROP TABLE IF EXISTS `aiocp_stats`;
CREATE TABLE `aiocp_stats` (
  `stats_id` int(10) unsigned NOT NULL auto_increment,
  `stats_datetime` datetime NOT NULL default '0000-00-00 00:00:00',
  `stats_user_id` int(10) unsigned default NULL,
  `stats_user_ip` char(16) collate utf8_unicode_ci NOT NULL default '',
  `stats_user_agent` int(10) unsigned default NULL,
  `stats_language` char(3) collate utf8_unicode_ci NOT NULL default '',
  `stats_page` int(10) unsigned default NULL,
  `stats_referer_int` int(10) unsigned default NULL,
  `stats_referer_ext` int(10) unsigned default NULL,
  PRIMARY KEY  (`stats_id`)
) TYPE=MyISAM COMMENT='site statistics data' AUTO_INCREMENT=1077919 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_stats_pages`
-- 

DROP TABLE IF EXISTS `aiocp_stats_pages`;
CREATE TABLE `aiocp_stats_pages` (
  `statpage_id` int(10) unsigned NOT NULL auto_increment,
  `statpage_url` varchar(255) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`statpage_id`)
) TYPE=MyISAM COMMENT='url of pages for statistical evaluation' AUTO_INCREMENT=19139 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_stats_referer`
-- 

DROP TABLE IF EXISTS `aiocp_stats_referer`;
CREATE TABLE `aiocp_stats_referer` (
  `statsreferer_id` int(10) unsigned NOT NULL auto_increment,
  `statsreferer_url` varchar(255) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`statsreferer_id`)
) TYPE=MyISAM COMMENT='url of external referer pages' AUTO_INCREMENT=77881 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_stats_user_agents`
-- 

DROP TABLE IF EXISTS `aiocp_stats_user_agents`;
CREATE TABLE `aiocp_stats_user_agents` (
  `statuseragent_id` int(10) unsigned NOT NULL auto_increment,
  `statuseragent_name` varchar(255) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`statuseragent_id`)
) TYPE=MyISAM COMMENT='list of user agents data' AUTO_INCREMENT=19172 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_user_agenda`
-- 

DROP TABLE IF EXISTS `aiocp_user_agenda`;
CREATE TABLE `aiocp_user_agenda` (
  `uagenda_id` int(10) unsigned NOT NULL auto_increment,
  `uagenda_userid` int(10) unsigned NOT NULL default '0',
  `uagenda_year` smallint(5) unsigned NOT NULL default '0',
  `uagenda_month` tinyint(3) unsigned NOT NULL default '0',
  `uagenda_day` tinyint(3) unsigned NOT NULL default '0',
  `uagenda_text` mediumtext collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`uagenda_id`)
) TYPE=MyISAM COMMENT='calendar notes for each user' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_users`
-- 

DROP TABLE IF EXISTS `aiocp_users`;
CREATE TABLE `aiocp_users` (
  `user_id` int(10) unsigned NOT NULL auto_increment,
  `user_regdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `user_ip` varchar(15) collate utf8_unicode_ci NOT NULL default '',
  `user_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `user_email` varchar(255) collate utf8_unicode_ci default NULL,
  `user_password` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `user_language` varchar(3) collate utf8_unicode_ci NOT NULL default 'eng',
  `user_firstname` varchar(255) collate utf8_unicode_ci default NULL,
  `user_lastname` varchar(255) collate utf8_unicode_ci default NULL,
  `user_birthdate` date default NULL,
  `user_birthplace` varchar(255) collate utf8_unicode_ci default NULL,
  `user_fiscalcode` varchar(255) collate utf8_unicode_ci default NULL,
  `user_level` tinyint(3) unsigned default '1',
  `user_group` int(10) unsigned NOT NULL default '0',
  `user_photo` varchar(255) collate utf8_unicode_ci default NULL,
  `user_signature` text collate utf8_unicode_ci,
  `user_notes` longtext collate utf8_unicode_ci,
  `user_publicopt` text collate utf8_unicode_ci,
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `user_name` (`user_name`)
) TYPE=MyISAM COMMENT='Users table' AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_users_address`
-- 

DROP TABLE IF EXISTS `aiocp_users_address`;
CREATE TABLE `aiocp_users_address` (
  `address_id` int(10) unsigned NOT NULL auto_increment,
  `address_userid` int(10) unsigned NOT NULL default '1',
  `address_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `address_address` varchar(255) collate utf8_unicode_ci default NULL,
  `address_city` varchar(255) collate utf8_unicode_ci default NULL,
  `address_state` varchar(255) collate utf8_unicode_ci default NULL,
  `address_postcode` varchar(255) collate utf8_unicode_ci default NULL,
  `address_countryid` smallint(5) unsigned NOT NULL default '1',
  `address_public` tinyint(3) unsigned NOT NULL default '0',
  `address_default` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`address_id`)
) TYPE=MyISAM COMMENT='Address table for cp_users (1->infinite)' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_users_auth`
-- 

DROP TABLE IF EXISTS `aiocp_users_auth`;
CREATE TABLE `aiocp_users_auth` (
  `ua_id` int(10) unsigned NOT NULL auto_increment,
  `ua_user_id` int(10) unsigned NOT NULL default '1',
  `ua_time_start` datetime NOT NULL default '0000-00-00 00:00:00',
  `ua_time_end` datetime NOT NULL default '0000-00-00 00:00:00',
  `ua_resource` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`ua_id`),
  FULLTEXT KEY `ua_resource` (`ua_resource`)
) TYPE=MyISAM COMMENT='set special users access permission to ua_resource ' AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_users_company`
-- 

DROP TABLE IF EXISTS `aiocp_users_company`;
CREATE TABLE `aiocp_users_company` (
  `company_id` int(10) unsigned NOT NULL auto_increment,
  `company_userid` int(10) unsigned NOT NULL default '1',
  `company_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `company_type_id` int(10) unsigned NOT NULL default '0',
  `company_link` varchar(255) collate utf8_unicode_ci default NULL,
  `company_supplier` tinyint(3) unsigned NOT NULL default '0',
  `company_fiscalcode` varchar(255) collate utf8_unicode_ci default NULL,
  `company_legal_address_id` int(10) unsigned default NULL,
  `company_billing_address_id` int(10) unsigned default NULL,
  `company_notes` longtext collate utf8_unicode_ci,
  `company_public` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`company_id`)
) TYPE=MyISAM COMMENT='User Company data' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_users_company_types`
-- 

DROP TABLE IF EXISTS `aiocp_users_company_types`;
CREATE TABLE `aiocp_users_company_types` (
  `comptype_id` int(10) unsigned NOT NULL auto_increment,
  `comptype_name` text collate utf8_unicode_ci NOT NULL,
  `comptype_description` longtext collate utf8_unicode_ci,
  `comptype_discount` float NOT NULL default '0',
  PRIMARY KEY  (`comptype_id`)
) TYPE=MyISAM COMMENT='Types of companies' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_users_groups`
-- 

DROP TABLE IF EXISTS `aiocp_users_groups`;
CREATE TABLE `aiocp_users_groups` (
  `usrgrp_id` int(10) unsigned NOT NULL auto_increment,
  `usrgrp_name` text collate utf8_unicode_ci NOT NULL,
  `usrgrp_description` longtext collate utf8_unicode_ci,
  PRIMARY KEY  (`usrgrp_id`)
) TYPE=MyISAM COMMENT='users groups' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_users_internet`
-- 

DROP TABLE IF EXISTS `aiocp_users_internet`;
CREATE TABLE `aiocp_users_internet` (
  `internet_id` int(10) unsigned NOT NULL auto_increment,
  `internet_userid` int(10) unsigned NOT NULL default '1',
  `internet_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `internet_email` varchar(255) collate utf8_unicode_ci default NULL,
  `internet_website` varchar(255) collate utf8_unicode_ci default NULL,
  `internet_icq` varchar(25) collate utf8_unicode_ci default NULL,
  `internet_aim` varchar(25) collate utf8_unicode_ci default NULL,
  `internet_yim` varchar(25) collate utf8_unicode_ci default NULL,
  `internet_msnm` varchar(25) collate utf8_unicode_ci default NULL,
  `internet_public` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`internet_id`)
) TYPE=MyISAM COMMENT='Internet contacts for cp_users (1->infinite)' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_users_phone`
-- 

DROP TABLE IF EXISTS `aiocp_users_phone`;
CREATE TABLE `aiocp_users_phone` (
  `phone_id` int(10) unsigned NOT NULL auto_increment,
  `phone_userid` int(10) unsigned NOT NULL default '1',
  `phone_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `phone_number` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `phone_public` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`phone_id`)
) TYPE=MyISAM COMMENT='Phone table for cp_users (1->infinite)' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_users_verification`
-- 

DROP TABLE IF EXISTS `aiocp_users_verification`;
CREATE TABLE `aiocp_users_verification` (
  `user_id` int(10) unsigned NOT NULL auto_increment,
  `user_regdate` int(10) unsigned NOT NULL default '0',
  `user_ip` varchar(15) collate utf8_unicode_ci NOT NULL default '',
  `user_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `user_email` varchar(255) collate utf8_unicode_ci default NULL,
  `user_password` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `user_language` varchar(3) collate utf8_unicode_ci NOT NULL default 'eng',
  `user_firstname` varchar(255) collate utf8_unicode_ci default NULL,
  `user_lastname` varchar(255) collate utf8_unicode_ci default NULL,
  `user_birthdate` date default NULL,
  `user_birthplace` varchar(255) collate utf8_unicode_ci default NULL,
  `user_fiscalcode` varchar(255) collate utf8_unicode_ci default NULL,
  `user_level` tinyint(3) unsigned default '1',
  `user_avatar` smallint(5) unsigned NOT NULL default '1',
  `user_photo` varchar(255) collate utf8_unicode_ci default NULL,
  `user_signature` text collate utf8_unicode_ci,
  `user_notes` longtext collate utf8_unicode_ci,
  `user_publicopt` text collate utf8_unicode_ci,
  `user_verifycode` varchar(32) collate utf8_unicode_ci NOT NULL default '0',
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `user_name` (`user_name`)
) TYPE=MyISAM COMMENT='Users table' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_xhtml_attributes`
-- 

DROP TABLE IF EXISTS `aiocp_xhtml_attributes`;
CREATE TABLE `aiocp_xhtml_attributes` (
  `htmattrib_id` int(10) unsigned NOT NULL auto_increment,
  `htmattrib_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `htmattrib_description` varchar(255) collate utf8_unicode_ci default NULL,
  `htmattrib_status` int(10) unsigned NOT NULL default '1',
  `htmattrib_dtd` tinyint(3) unsigned NOT NULL default '0',
  `htmattrib_type` tinyint(3) unsigned NOT NULL default '0',
  `htmattrib_values` text collate utf8_unicode_ci NOT NULL,
  `htmattrib_default` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`htmattrib_id`)
) TYPE=MyISAM COMMENT='List of HTML attributes' AUTO_INCREMENT=189 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_xhtml_tags`
-- 

DROP TABLE IF EXISTS `aiocp_xhtml_tags`;
CREATE TABLE `aiocp_xhtml_tags` (
  `tag_id` int(10) unsigned NOT NULL auto_increment,
  `tag_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `tag_description` varchar(255) collate utf8_unicode_ci default NULL,
  `tag_categoryid` int(10) unsigned NOT NULL default '0',
  `tag_statusid` int(10) unsigned NOT NULL default '0',
  `tag_endtag` tinyint(3) unsigned default '1',
  `tag_attributes` varchar(255) collate utf8_unicode_ci default NULL,
  `tag_dtd` tinyint(3) unsigned NOT NULL default '0',
  `tag_xhtml_basic` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`tag_id`)
) TYPE=MyISAM COMMENT='List of HTML elements' AUTO_INCREMENT=92 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_xhtml_tags_categories`
-- 

DROP TABLE IF EXISTS `aiocp_xhtml_tags_categories`;
CREATE TABLE `aiocp_xhtml_tags_categories` (
  `tagcat_id` int(10) unsigned NOT NULL auto_increment,
  `tagcat_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `tagcat_description` varchar(255) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`tagcat_id`)
) TYPE=MyISAM COMMENT='Categories of HTML tags' AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `aiocp_xhtml_tags_status`
-- 

DROP TABLE IF EXISTS `aiocp_xhtml_tags_status`;
CREATE TABLE `aiocp_xhtml_tags_status` (
  `tagstat_id` int(10) unsigned NOT NULL auto_increment,
  `tagstat_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `tagstat_description` varchar(255) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`tagstat_id`)
) TYPE=MyISAM COMMENT='Status of HTML tags' AUTO_INCREMENT=5 ;
