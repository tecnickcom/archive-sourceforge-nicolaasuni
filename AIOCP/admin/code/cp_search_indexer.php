<?php
//============================================================+
// File name   : cp_search_indexer.php                         
// Begin       : 2002-05-31                                    
// Last Update : 2003-10-26                                    
//                                                             
// Description : Search Indexer for public site                
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

require_once('../../shared/config/cp_extension.inc');
require_once('../config/cp_config.'.CP_EXT);
require_once('../../shared/code/cp_functions_form.'.CP_EXT);
require_once('../../admin/code/cp_functions_search_spider.'.CP_EXT);

$pagelevel = K_AUTH_ADMIN_CP_SEARCH_INDEXER;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

//initialize menu command
if (isset($_REQUEST["menu_mode"])) {
	$menu_mode = $_REQUEST["menu_mode"];
}
else {
	$menu_mode = "";
}

$progress_log = "../log/cp_search_indexer.log";

$thispage_title = $l['t_search_indexer'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages;

switch($menu_mode) {
	case unhtmlentities($l['w_v_index']):
	case $l['w_v_index']:{ // Backup site
		F_print_error("MESSAGE", $l['m_search_index_wait']);
		//open log popup display to show process progress
		@unlink($progress_log); //clear progress log file if exist
		error_log("--- START LOG: ".gmdate("Y-m-d H:i:s")." ---\n", 3, $progress_log); //create progress log file
		echo "\n<script language=\"JavaScript\" type=\"text/javascript\">\n";
		echo "//<![CDATA[\n";
		echo "logview=window.open('cp_show_progress.".CP_EXT."?log=".$progress_log."','logview','dependent,height=280,width=400,menubar=no,resizable=yes,scrollbars=no,status=no,toolbar=no');\n";
		echo "//]]>\n";
		echo "</script>\n";
		break;
	}
	case "startlongprocess":{ // start backup
		F_site_search_indexer();
		error_log("--- END LOG: ".gmdate("Y-m-d H:i:s")." ---\n", 3, $progress_log); //create progress log file
		F_print_error("MESSAGE", $l['m_search_index_complete']);
		break;
	}
}
?>
<!-- ====================================================== -->	
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_site_indexing" id="form_site_indexing">

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<tr class="fillO">
<td class="fillOO">
<?php echo $l['d_site_indexing']; ?>
</td>
</tr>

</table>

</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
F_submit_button("form_site_indexing","menu_mode",$l['w_v_index']); 
?>
</td>
</tr>
</table>
</form>
<!-- ====================================================== -->

<?php //recall backup page to start backup
if (($menu_mode == $l['w_v_index']) OR ($menu_mode == unhtmlentities($l['w_v_index'])) ) {
	echo "<script language=\"JavaScript\" type=\"text/javascript\">";
	echo "//<![CDATA[\n";
	echo "document.form_site_indexing.menu_mode.value='startlongprocess';";
	echo "document.form_site_indexing.submit();";
	echo "//]]>\n";
	echo "</script>";
}

require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>