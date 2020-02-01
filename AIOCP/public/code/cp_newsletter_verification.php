<?php
/* ============================================================
 File name   : cp_newsletter_verification.php
 Begin       : 2002-04-07 08:41:14
 Last update : 2002-04-07 08:41:14
 Author      : (5) nick
 Description : verify newsletter user
=============================================================== */

require_once('../../shared/config/cp_extension.inc');
require_once('../config/cp_config.'.CP_EXT);
$pagelevel = 0; // page level
require_once('../../shared/code/cp_authorization.'.CP_EXT); // check authorization permissions
require_once('../../shared/code/cp_functions_collect_stats.'.CP_EXT); // collect site statistics
$page_name = "newsletter_user_verification"; // name of the page (used in DB)
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


<?php // newsletter verify user
//------------------------------------------------------------
require_once('../../shared/code/cp_functions_newsletter_users.'.CP_EXT);
F_enable_newsletter_user($a, $b, $c, $d, $e);
//------------------------------------------------------------
?>
<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.php'); // page footer; ?>
