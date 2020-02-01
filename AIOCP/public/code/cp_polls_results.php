<?php
/* ============================================================
 File name   : 
 Begin       : 2002-04-07 09:22:57
 Last update : 2002-04-07 09:22:57
 Author      : (5) nick
 Description : display polls results
=============================================================== */

require_once('../../shared/config/cp_extension.inc');
require_once('../config/cp_config.'.CP_EXT);
$pagelevel = 0; // page level
require_once('../../shared/code/cp_authorization.'.CP_EXT); // check authorization permissions
require_once('../../shared/code/cp_functions_collect_stats.'.CP_EXT); // collect site statistics
$page_name = "polls_results"; // name of the page (used in DB)
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

require_once('../../shared/code/cp_functions_polls_results.'.CP_EXT);
$fullselect = TRUE;
$barsmaxlength = 200; // max length of the graph bars in pixels
$barswidth = 10; // width of the graph bars in pixels (10,15,20,25,30,35,40)
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
F_show_poll_results($poll_language, $poll_id, $fullselect, $barsmaxlength, $barswidth); 

require_once('../code/cp_page_footer.php'); // page footer; 
?>
