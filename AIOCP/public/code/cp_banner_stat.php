<?php
/* ============================================================
 File name   : cp_banner_stat.php
 Begin       : 2002-05-03 16:28:36
 Last update : 2002-05-03 16:28:36
 Author      : (5) nick
 Description : display banner statistics
=============================================================== */

require_once('../../shared/config/cp_extension.inc');
require_once('../config/cp_config.'.CP_EXT);
$pagelevel = 1; // page level
require_once('../../shared/code/cp_authorization.'.CP_EXT); // check authorization permissions
require_once('../../shared/code/cp_functions_collect_stats.'.CP_EXT); // collect site statistics
$page_name = "banner_statistics"; // name of the page (used in DB)
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


<?php // banner statistics
//------------------------------------------------------------
require_once('../../shared/code/cp_functions_banner_stats.'.CP_EXT);
F_show_select_banner_stats('');
//------------------------------------------------------------
?>
<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.php'); // page footer; ?>

