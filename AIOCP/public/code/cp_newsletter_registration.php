<?php
/* ============================================================
 File name   : 
 Begin       : 2002-04-07 08:45:30
 Last update : 2002-04-07 08:45:30
 Author      : (5) nick
 Description : subscribe newsletter users 
=============================================================== */

require_once('../../shared/config/cp_extension.inc');
require_once('../config/cp_config.'.CP_EXT);
$pagelevel = 0; // page level
require_once('../../shared/code/cp_authorization.'.CP_EXT); // check authorization permissions
require_once('../../shared/code/cp_functions_collect_stats.'.CP_EXT); // collect site statistics
$page_name = "newsletter_registration"; // name of the page (used in DB)
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

require_once('../../shared/code/cp_functions_newsletter_users.'.CP_EXT);

if (isset($_REQUEST["nlcat_language"])) {
	$nlcat_language = $_REQUEST["nlcat_language"];
} else {
	$nlcat_language = "";
}
if (isset($_REQUEST["nlmsg_nlcatid"])) {
	$nlmsg_nlcatid = $_REQUEST["nlmsg_nlcatid"];
} else {
	$nlmsg_nlcatid = "";
}
F_newsletter_subscription_form(true, $nlcat_language, $nlmsg_nlcatid);

require_once('../code/cp_page_footer.php'); // page footer; 
?>
