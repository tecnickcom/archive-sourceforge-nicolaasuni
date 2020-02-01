<?php
//============================================================+
// File name   : cp_functions_menu_textbar.php                    
// Begin       : 2005-03-25                                    
// Last Update : 2005-06-30                                    
//                                                             
// Description : Functions for show first level menu items
//               as text links.
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

$popup_position = ""; //global variable that contain the popop position

/**
 * Show the client menu
 * @param menu_name menu name
*/
function F_show_textbar_menu($menu_name) {
	global $l, $db, $selected_language;
	global $txtmenu;
	global $aiocp_dp, $hp;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	//select menu table
	if (isset($menu_name) AND ( (strcmp($menu_name, "0") == 0) OR (is_int($menu_name) AND ($menu_name == 0))) ) {
		$menutable = K_TABLE_MENU;
		$menulst_option = 1000;
		$menulst_style = 1000;
	}
	else {
		$menutable = K_TABLE_MENU_CLIENT;
		if (!isset($menu_name) OR ($menu_name == 1)) {
			$sql = "SELECT * FROM ".K_TABLE_MENU_LIST." WHERE menulst_id='1' LIMIT 1";
		}
		else {
			$sql = "SELECT * FROM ".K_TABLE_MENU_LIST." WHERE menulst_name='".$menu_name."' LIMIT 1";
		}
		//get menu option
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$menulst_id = $m['menulst_id'];
			}
			else {
				return "";
			}
		}
		else {
			F_display_db_error();
		}
		if ($menulst_id > 1) {
			$menutable = K_TABLE_MENU_CLIENT."_".$menulst_id;
		}
	}


	$txtmenu = "";
	
	$bindex = F_explore_menu_level($menutable, 0, 0);
	
	return "<div id=\"textmenubar\"><ul>".$txtmenu."</ul></div>";
}
// ------------------------------------------------------------

// ------------------------------------------------------------
// Explore tree recursively
// Show the tree items in the right order with option buttons
// ------------------------------------------------------------
function F_explore_menu_level($menutable, $treelevel, $iid) {
	global $l, $db, $selected_language;
	global $txtmenu;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_target.'.CP_EXT);
	
	$isubid = $iid;
	
	$sql = "SELECT * FROM ".$menutable." WHERE (menu_language='".$selected_language."' AND menu_sub_id=".$treelevel.") ORDER BY menu_position";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			if (isset($m['menu_enabled']) AND ($m['menu_enabled'])) {
				$iid++;
				$txtmenu .= "\n<li>";
				if ($m['menu_link']) {
					$txtmenu .= "<a href=\"".str_replace(" ","+",$m['menu_link'])."\"";
					if ($m['menu_target'] > 1) {
						$txtmenu .= " target=\"".F_get_target_name($m['menu_target'])."\"";
					}
				}
				elseif ($menutable == K_TABLE_MENU) { //display subtree icons
					$txtmenu .= "<a href=\"cp_layout_main.".CP_EXT."?previous_language=".$selected_language."&amp;menu_sub_id=".$m['menu_id']."&amp;node=".urlencode($m['menu_name'])."\"";	
				}
				else {
					$txtmenu .= "<a href=\"#\"";
				}
				
				if ($m['menu_description']) {
					$txtmenu .= " title=\"".$m['menu_description']."\"";
				}
				$txtmenu .= ">".$m['menu_name']."</a>";
				$txtmenu .= "</li>";
			} //end if disabled
		}
	}
	else {
		F_display_db_error();
	}
	
	return $iid;
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
