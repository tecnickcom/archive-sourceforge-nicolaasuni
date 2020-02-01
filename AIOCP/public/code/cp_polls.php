<?php
/* ============================================================
 File name   : cp_polls.php
 Begin       : 2002-04-07 09:19:50
 Last update : 2002-04-07 09:19:50
 Author      : (5) nick
 Description : poll form
=============================================================== */

require_once('../../shared/config/cp_extension.inc');
require_once('../config/cp_config.'.CP_EXT);
$pagelevel = 0; // page level
require_once('../../shared/code/cp_authorization.'.CP_EXT); // check authorization permissions
require_once('../../shared/code/cp_functions_collect_stats.'.CP_EXT); // collect site statistics
$page_name = "polls_vote"; // name of the page (used in DB)
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

require_once('../../shared/code/cp_functions_polls_vote.'.CP_EXT);
$fullselect = true; // change to false to hide poll selector

if (isset($_REQUEST["poll_language"])) {
	$poll_language = $_REQUEST["poll_language"];
} else {
	$poll_language = $selected_language;
}
if (isset($_REQUEST["poll_id"])) {
	$poll_id = $_REQUEST["poll_id"];
} else {
	$poll_id = 0;
}
F_show_poll_form($poll_language, $poll_id, $fullselect);

require_once('../code/cp_page_footer.php'); // page footer; 
?>
