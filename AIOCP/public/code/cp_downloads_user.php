<?php
/* ============================================================
 File name   : 
 Begin       : 2003-12-21 18:22:16
 Last update : 2003-12-21 18:22:16
 Author      : (2) admin
 Description : mostra una lista di file scaricabili specifici per l'utente corrente
=============================================================== */

require_once('../../shared/config/cp_extension.inc');
require_once('../config/cp_config.'.CP_EXT);
$pagelevel = 0; // page level
require_once('../../shared/code/cp_authorization.'.CP_EXT); // check authorization permissions
require_once('../../shared/code/cp_functions_collect_stats.'.CP_EXT); // collect site statistics
$page_name = "cp_downloads_user"; // name of the page (used in DB)
$pt = F_load_page_templates($selected_language, $page_name); // load page templates for the current language

// The following are values for META TAGS
// (leave void for default values)
$thispage_author = "Nicola Asuni"; // name of page author
$thispage_reply = ""; // email address
$thispage_style = ""; // CSS page link
$thispage_title = $pt['_title']; // page title
$thispage_description = $pt['_description']; // page description
$thispage_keywords = $pt['_keywords']; // page keywords

require_once('../code/cp_page_header.php'); // page header
F_print_error(0, ""); //clear error and system messages
?>
<!-- ====================================================== -->
<!-- put your code in this area-->

<?php
// AIOCP standard module
// display user specific downloads
// Author: Nicola Asuni; (c) Copyright: Tecnick.com LTD

if (!isset($viewmode)) {$viewmode=0;}
if (!isset($selecteddownload)) {$selecteddownload="";}
if (!isset($downloaded)) {$downloaded="";}
if (!isset($firstrow)) {$firstrow=0;}
if (!isset($rowsperpage)) {$rowsperpage=K_MAX_ROWS_PER_PAGE;}

require_once('../../shared/code/cp_functions_downloads_users.'.CP_EXT);
F_show_user_downloads($viewmode, $selecteddownload, $downloaded, $firstrow, $rowsperpage);
?>
<!-- ====================================================== -->
<?php
require_once('../code/cp_page_footer.php'); // page footer
?>
