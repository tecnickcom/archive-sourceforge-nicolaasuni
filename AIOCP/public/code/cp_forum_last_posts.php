<?php
/* ============================================================
 File name   : cp_forum_last_posts.php
 Begin       : 2002-04-05 14:38:43
 Last update : 2002-04-21 16:16:43
 Author      : (5) nick
 Description : Array
=============================================================== */

require_once('../../shared/config/cp_extension.inc');
require_once('../config/cp_config.'.CP_EXT);
$pagelevel = 0; // page level
require_once('../../shared/code/cp_authorization.'.CP_EXT); // check authorization permissions
require_once('../../shared/code/cp_functions_collect_stats.'.CP_EXT); // collect site statistics
$page_name = "forum_last_post"; // name of the page (used in DB)
$pt = F_load_page_templates($selected_language, $page_name); // load page templates for the current language

// The following are values for META TAGS
// (leave void for default values)
$thispage_author = ""; // name of page author
$thispage_reply = ""; // email address
$thispage_style = ""; // CSS page link
$thispage_title = $pt['_title']; // page title
$thispage_description = $pt['_description']; // page description
$thispage_keywords = $pt['_keywords']; // page keywords

require_once('../code/cp_page_header.php'); // page header
F_print_error(0, ""); //clear error and system messages
?>
<!-- ====================================================== -->
<?php
require_once('../../shared/code/cp_functions_forum_last_posts.'.CP_EXT);
echo F_show_forum_last_posts($selected_language, "", "", 10, 40);
?>
<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.php'); // page footer; ?>
