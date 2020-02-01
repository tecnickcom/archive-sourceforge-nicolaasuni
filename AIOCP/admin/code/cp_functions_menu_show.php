<?php
//============================================================+
// File name   : cp_functions_menu_show.php                    
// Begin       : 2001-09-06                                    
// Last Update : 2003-10-12                                    
//                                                             
// Description : Functions for show Menu                       
//                                                             
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

// ------------------------------------------------------------
// Explore tree recursively
// Show the tree items in the right order with option buttons
// ------------------------------------------------------------
function F_show_explore_level($treelevel, $menu_language, $menutable) {
	global $l, $db;
	$maxnamelength = 18; //max length for a name
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_target.'.CP_EXT);

	$sql = "SELECT * FROM ".$menutable." WHERE (menu_language='".$menu_language."' AND menu_sub_id=".$treelevel.") ORDER BY menu_position";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			if (isset($m['menu_enabled']) AND ($m['menu_enabled'])) {
				if(!$m['menu_item']){ //is a node (not the first)
					echo "foldersTree".$m['menu_id']." = newSubNode(foldersTree".$m['menu_sub_id'].",  newNode(\"".substr(addslashes($m['menu_name']),0,$maxnamelength)."\", ";
					if ($m['menu_link']) { //is a node with page link
						echo "\"".$m['menu_link']."\", \"".F_get_target_name($m['menu_target'])."\"));\n";
					}
					else { //is only a link for subtree
						echo "\"cp_layout_main.".CP_EXT."?previous_language=".$menu_language."&amp;menu_sub_id=".$m['menu_id']."&amp;node=".urlencode($m['menu_name'])."\", \"CPMAIN\"));\n";
					}
					F_show_explore_level($m['menu_id'], $menu_language, $menutable);
				}
				else { //is a item
					echo "newSubItem(foldersTree".$m['menu_sub_id'].", newItem(\"".substr(addslashes($m['menu_name']),0,$maxnamelength)."\", \"".$m['menu_link']."\",";
					echo " \"".F_get_target_name($m['menu_target'])."\"));\n";
				}
			}
		}
	}
	else {
		F_display_db_error();
	}
return;
}
// ------------------------------------------------------------

// ------------------------------------------------------------
// Show the menu tree
// Interface for Javascript TreeMenu
// ("NAME OF THE PAGE","PAGE LINK","NAME OF TARGET FRAME", "NAME OF MENU TABLE", "DATABASE")
// ------------------------------------------------------------
function F_show_tree_menu($mttitle, $mtfirstpage, $mttarget, $menu_language, $menutable) {
	global $l, $db;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	// Practical Browser Sniffing Script
	echo "<script language=\"JavaScript\" src=\"".K_PATH_SHARED_JSCRIPTS."ua.js\" type=\"text/javascript\"></script>\n";
	//Infrastructure code for the tree
	echo "<script language=\"JavaScript\" src=\"".K_PATH_JSCRIPTS."menutree.js\" type=\"text/javascript\"></script>\n";
	
	//Execution of the code that actually builds the specific tree
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
	echo "//<![CDATA[\n";
	// Main root
	echo "foldersTree0 = newNode(\"".$mttitle."\", \"".$mtfirstpage."\", \"".$mttarget."\");\n";
	F_show_explore_level(0, $menu_language, $menutable);
	echo "initializeDocument();";
	echo "//]]>\n";
	echo "</script>";
	return;
}
// ------------------------------------------------------------

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
