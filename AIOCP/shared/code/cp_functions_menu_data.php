<?php
//============================================================+
// File name   : cp_functions_menu_data.php
// Begin       : 2004-05-03
// Last Update : 2008-07-05
// 
// Description : Create a dinamic data file for applets menus
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
 * Return the client menu data
 * @param menu_name menu name
 * @param page URL of calling page
*/
function F_show_menu_data($menu_name, $page) {
	global $l, $db, $selected_language, $popup_position;
	global $menucode;
	global $aiocp_dp, $hp;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	require_once('../../shared/code/cp_functions_sounds.'.CP_EXT);
	require_once('../../shared/code/cp_functions_icons.'.CP_EXT);
	require_once('../../shared/code/cp_functions_target.'.CP_EXT);
	require_once('../../shared/code/cp_functions_menu_client_show.'.CP_EXT);
	
	//select menu table
	if (isset($menu_name) AND ( (strcmp($menu_name, "0") == 0) OR (is_int($menu_name) AND ($menu_name == 0))) ) {
		$menutable = K_TABLE_MENU;
		$menulst_option = 1000;
		$menulst_style = 1000;
	}
	else {
		$menutable = K_TABLE_MENU_CLIENT;
		if (isset($menu_name) AND ($menu_name == 1)) {
			$sql = "SELECT * FROM ".K_TABLE_MENU_LIST." WHERE menulst_id='1' LIMIT 1";
		}
		else {
			$sql = "SELECT * FROM ".K_TABLE_MENU_LIST." WHERE menulst_name='".$menu_name."' LIMIT 1";
		}
		//get menu option
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$menulst_id = $m['menulst_id'];
				$menulst_option = $m['menulst_option'];
				$menulst_style = $m['menulst_style'];
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
	
	$menuconfig = F_get_menu_client_options($menulst_option); //get client menu options
	$popup_position = $menuconfig['menuopt_popup_position'];
	
	//reset menu code
	$menucode = "";
	
	$bindex = F_explore_data_menu_level($menutable, 0, 0, 0);
	//display change language buttons only in main menu
	if ((K_LANG_ON_MENU) AND (($menutable == K_TABLE_MENU_CLIENT) OR ($menutable == K_TABLE_MENU))) {
		F_display_data_language_button($bindex, $page, $aiocp_dp, $hp);
	}
	
	return $menucode;
}

// ------------------------------------------------------------

// ------------------------------------------------------------
// Explore tree recursively
// Show the tree items in the right order with option buttons
// ------------------------------------------------------------
function F_explore_data_menu_level($menutable, $treelevel, $iid, $level) {
	global $l, $db, $selected_language, $popup_position;
	global $menucode;
	global $aiocp_dp, $hp;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	require_once('../../shared/code/cp_functions_sounds.'.CP_EXT);
	require_once('../../shared/code/cp_functions_icons.'.CP_EXT);
	require_once('../../shared/code/cp_functions_target.'.CP_EXT);
	require_once('../../shared/code/cp_functions_menu_client_show.'.CP_EXT);
	
	$isubid = $iid;
	
	$sql = "SELECT * FROM ".$menutable." WHERE (menu_language='".$selected_language."' AND menu_sub_id=".$treelevel.") ORDER BY menu_position";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			if (isset($m['menu_enabled']) AND ($m['menu_enabled'])) {
				
				if ($m['menu_style_id']) {
					$buttonstyle = F_get_menu_client_style($m['menu_style_id']);
				}
				else {
					$buttonstyle = false;
				}
				
				$iid++;
				
				$menucode .= "".$level."";
				
				$menucode .= "\t";
				
				if ($m['menu_enabled']) {
					$menucode .= "true";
				}
				else {
					$menucode .= "false";
				}
				
				$menucode .= "\t";
				
				if ($m['menu_link']) {
					$menucode .= $m['menu_link'];
				}
				elseif ($menutable == K_TABLE_MENU) { //display subtree icons
					$menucode .= "cp_layout_main.".CP_EXT."?previous_language=".$selected_language."&menu_sub_id=".$m['menu_id']."&node=".urlencode($m['menu_name'])."";		
				}
				
				$menucode .= "\t".F_get_target_name($m['menu_target']);
				$menucode .= "\t".$l['a_meta_charset'];
				$menucode .= "\t".F_compact_string($m['menu_name']);
				$menucode .= "\t".F_compact_string($m['menu_description']);
				
				//positions
				if (!isset($buttonstyle['menustyle_label_position'])) {
					if ($l['a_meta_dir'] == "rtl") {
						$buttonstyle['menustyle_label_position'] = "LEFT";
					}
					else {
						$buttonstyle['menustyle_label_position'] = "RIGHT";
					}
				}
				$menucode .= "\t".$buttonstyle['menustyle_label_position'];
				$menucode .= "\t";
				if ((isset($buttonstyle['menustyle_center_block'])) AND ($buttonstyle['menustyle_center_block'])) {
					$menucode .= "true";
				}
				else {
					$menucode .= "false";
				}
				
				$menucode .= "\t";
				if ((isset($buttonstyle['menustyle_padding'])) AND ($buttonstyle['menustyle_padding']>=0)) {
					$menucode .= $buttonstyle['menustyle_padding'];
				}
				$menucode .= "\t";
				if ((isset($buttonstyle['menustyle_gap'])) AND ($buttonstyle['menustyle_gap']>=0)) {
					$menucode .= $buttonstyle['menustyle_gap'];
				}
				$menucode .= "\t";
				if ((isset($buttonstyle['menustyle_border_width'])) AND ($buttonstyle['menustyle_border_width']>=0)) {
					$menucode .= $buttonstyle['menustyle_border_width'];
				}
				$menucode .= "\t";
				if ((isset($buttonstyle['menustyle_pushed'])) AND ($buttonstyle['menustyle_pushed'])) {
					$menucode .= "true";
				} else {
					$menucode .= "false";
				}
				
				if ($level == 0) {
					$menucode .= "\t";
					if (isset($buttonstyle['menustyle_main_font'])) {	
						$menucode .= $buttonstyle['menustyle_main_font'];
					}
					$menucode .= "\t";
					if (isset($buttonstyle['menustyle_main_font_style'])) {
						$menucode .= $buttonstyle['menustyle_main_font_style'];
					}
					$menucode .= "\t";
					if (isset($buttonstyle['menustyle_main_font_size'])) {
						$menucode .= $buttonstyle['menustyle_main_font_size'];
					}
				} else {
					$menucode .= "\t";
					if (isset($buttonstyle['menustyle_submenu_font'])) {
						$menucode .= $buttonstyle['menustyle_submenu_font'];
					}
					$menucode .= "\t";
					if (isset($buttonstyle['menustyle_submenu_font_style'])) {
						$menucode .= $buttonstyle['menustyle_submenu_font_style'];
					}
					$menucode .= "\t";
					if (isset($buttonstyle['menustyle_submenu_font_size'])) {
						$menucode .= $buttonstyle['menustyle_submenu_font_size'];
					}
				}
				$menucode .= "\t";
				if (isset($buttonstyle['menustyle_colbck_off'])) {
					$menucode .= substr($buttonstyle['menustyle_colbck_off'], 1);
				}
				$menucode .= "\t";
				if (isset($buttonstyle['menustyle_colbck_over'])) {
					$menucode .= substr($buttonstyle['menustyle_colbck_over'], 1);
				}
				$menucode .= "\t";
				if (isset($buttonstyle['menustyle_colbck_on'])) {
					$menucode .= substr($buttonstyle['menustyle_colbck_on'], 1);
				}
				$menucode .= "\t";
				if (isset($buttonstyle['menustyle_coltxt_off'])) {
					$menucode .= substr($buttonstyle['menustyle_coltxt_off'], 1);
				}
				$menucode .= "\t";
				if (isset($buttonstyle['menustyle_coltxt_over'])) {
					$menucode .= substr($buttonstyle['menustyle_coltxt_over'], 1);
				}
				$menucode .= "\t";
				if (isset($buttonstyle['menustyle_coltxt_on'])) {
					$menucode .= substr($buttonstyle['menustyle_coltxt_on'], 1);
				}
				$menucode .= "\t";
				if (isset($buttonstyle['menustyle_colsdw_off'])) {
					$menucode .= substr($buttonstyle['menustyle_colsdw_off'], 1);
				}
				$menucode .= "\t";
				if (isset($buttonstyle['menustyle_colsdw_over'])) {
					$menucode .= substr($buttonstyle['menustyle_colsdw_over'], 1);
				}
				$menucode .= "\t";
				if (isset($buttonstyle['menustyle_colsdw_on'])) {
					$menucode .= substr($buttonstyle['menustyle_colsdw_on'], 1);
				}
				$menucode .= "\t";
				if (isset($buttonstyle['menustyle_shadow_x'])) {
					$menucode .= $buttonstyle['menustyle_shadow_x'];
				}
				$menucode .= "\t";
				if (isset($buttonstyle['menustyle_shadow_y'])) {
					$menucode .= $buttonstyle['menustyle_shadow_y'];
				}
				
				// icons
				$menucode .= "\t";
				if ($m['menu_icon_off']>1) {
					$menucode .= K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $m['menu_icon_off']);
				}
				elseif ($buttonstyle AND isset($buttonstyle['menustyle_icon_off']) AND ($buttonstyle['menustyle_icon_off']>1)) {
					$menucode .= K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $buttonstyle['menustyle_icon_off']);
				}
				$menucode .= "\t";
				if ($m['menu_icon_over']>1) {
					$menucode .= K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $m['menu_icon_over']);
				}
				elseif ($buttonstyle AND isset($buttonstyle['menustyle_icon_over']) AND ($buttonstyle['menustyle_icon_over']>1)) {
					$menucode .= K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $buttonstyle['menustyle_icon_over']);
				}
				$menucode .= "\t";
				if ($m['menu_icon_on']>1) {
					$menucode .= K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $m['menu_icon_on']);
				}
				elseif ($buttonstyle AND isset($buttonstyle['menustyle_icon_on']) AND ($buttonstyle['menustyle_icon_on']>1)) {
					$menucode .= K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $buttonstyle['menustyle_icon_on']);
				}
				
				//backgrounds images
				$menucode .= "\t";
				if (isset($buttonstyle['menustyle_bck_img_off']) AND ($buttonstyle['menustyle_bck_img_off']>1)) {
					$menucode .= K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $buttonstyle['menustyle_bck_img_off']);
				}
				$menucode .= "\t";
				if (isset($buttonstyle['menustyle_bck_img_over']) AND ($buttonstyle['menustyle_bck_img_over']>1)) {
					$menucode .= K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $buttonstyle['menustyle_bck_img_over']);
				}
				$menucode .= "\t";
				if (isset($buttonstyle['menustyle_bck_img_on']) AND ($buttonstyle['menustyle_bck_img_on']>1)) {
					$menucode .= K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $buttonstyle['menustyle_bck_img_on']);
				}
				
				//arrows 
				switch ($popup_position) {
					case "LEFT": {
						$menucode .= "\t";
						if (isset($buttonstyle['buttonstyle_arrow_img_off_left']) AND ($buttonstyle['buttonstyle_arrow_img_off_left']>1)) {
							$menucode .= K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $buttonstyle['buttonstyle_arrow_img_off_left']);
						}
						$menucode .= "\t";
						if (isset($buttonstyle['buttonstyle_arrow_img_over_left']) AND ($buttonstyle['buttonstyle_arrow_img_over_left']>1)) {
							$menucode .= K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $buttonstyle['buttonstyle_arrow_img_over_left']);
						}
						$menucode .= "\t";
						if (isset($buttonstyle['buttonstyle_arrow_img_on_left']) AND ($buttonstyle['buttonstyle_arrow_img_on_left']>1)) {
							$menucode .= K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $buttonstyle['buttonstyle_arrow_img_on_left']);
						}
						break;
					}
					case "RIGHT": {
						$menucode .= "\t";
						if (isset($buttonstyle['buttonstyle_arrow_img_off_right']) AND ($buttonstyle['buttonstyle_arrow_img_off_right']>1)) {
							$menucode .= K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $buttonstyle['buttonstyle_arrow_img_off_right']);
						}
						$menucode .= "\t";
						if (isset($buttonstyle['buttonstyle_arrow_img_over_right']) AND ($buttonstyle['buttonstyle_arrow_img_over_right']>1)) {
							$menucode .= K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $buttonstyle['buttonstyle_arrow_img_over_right']);
						}
						$menucode .= "\t";
						if (isset($buttonstyle['buttonstyle_arrow_img_on_right']) AND ($buttonstyle['buttonstyle_arrow_img_on_right']>1)) {
							$menucode .= K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $buttonstyle['buttonstyle_arrow_img_on_right']);
						}
						break;
					}
					case "TOP": {
						$menucode .= "\t";
						if (isset($buttonstyle['buttonstyle_arrow_img_off_top']) AND ($buttonstyle['buttonstyle_arrow_img_off_top']>1)) {
							$menucode .= K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $buttonstyle['buttonstyle_arrow_img_off_top']);
						}
						$menucode .= "\t";
						if (isset($buttonstyle['buttonstyle_arrow_img_over_top']) AND ($buttonstyle['buttonstyle_arrow_img_over_top']>1)) {
							$menucode .= K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $buttonstyle['buttonstyle_arrow_img_over_top']);
						}
						$menucode .= "\t";
						if (isset($buttonstyle['buttonstyle_arrow_img_on_top']) AND ($buttonstyle['buttonstyle_arrow_img_on_top']>1)) {
							$menucode .= K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $buttonstyle['buttonstyle_arrow_img_on_top']);
						}
						break;
					}
					case "BOTTOM": {
						$menucode .= "\t";
						if (isset($buttonstyle['buttonstyle_arrow_img_off_bottom']) AND ($buttonstyle['buttonstyle_arrow_img_off_bottom']>1)) {
							$menucode .= K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $buttonstyle['buttonstyle_arrow_img_off_bottom']);
						}
						$menucode .= "\t";
						if (isset($buttonstyle['buttonstyle_arrow_img_over_bottom']) AND ($buttonstyle['buttonstyle_arrow_img_over_bottom']>1)) {
							$menucode .= K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $buttonstyle['buttonstyle_arrow_img_over_bottom']);
						}
						$menucode .= "\t";
						if (isset($buttonstyle['buttonstyle_arrow_img_on_bottom']) AND ($buttonstyle['buttonstyle_arrow_img_on_bottom']>1)) {
							$menucode .= K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $buttonstyle['buttonstyle_arrow_img_on_bottom']);
						}
						break;
					}
				}
				
				//sounds
				$menucode .= "\t";
				if (isset($buttonstyle['menustyle_sound_over']) AND ($buttonstyle['menustyle_sound_over']>1)) {
					$menucode .= K_PATH_SOUNDS_MENU.F_get_sound_link($buttonstyle['menustyle_sound_over']);
				}
				$menucode .= "\t";
				if (isset($buttonstyle['menustyle_sound_click']) AND ($buttonstyle['menustyle_sound_click']>1)) {
					$menucode .= K_PATH_SOUNDS_MENU.F_get_sound_link($buttonstyle['menustyle_sound_click']);
				}
				
				$menucode .= "\n"; // end row
				
				if(!$m['menu_item']) { //is a node (not the first)
					$iid = F_explore_data_menu_level($menutable, $m['menu_id'], $iid, $level+1);
				}
			} //end if disabled
		} // end while
	}
	else {
		F_display_db_error();
	}
	
	return $iid;
}
// ------------------------------------------------------------

// ------------------------------------------------------------
// Display language selector button
// ------------------------------------------------------------
function F_display_data_language_button($iid, $page) {
	global $l, $db, $selected_language;
	global $menucode, $menualtcode;
	global $aiocp_dp, $hp;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_target.'.CP_EXT);
	
	$iid++;
	$isubid = $iid;
	
	$menucode .= "0";
	
	$menucode .= "\ttrue";
	$menucode .= "\t#";
	$menucode .= "\t_self";
	$menucode .= "\t".$l['a_meta_charset'];
	$menucode .= "\t".F_compact_string($l['w_language']);
	$menucode .= "\t".F_compact_string($l['w_language']);
	
	//use default options
	$menucode .= "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
	$menucode .= "\n"; // end row
	
	//languages
	$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			$iid++;
			
			$menucode .= "1";
			
			$menucode .= "\t";
			if ($m['language_code'] == $selected_language) { //disable current language
				$menucode .= "false";
			}
			else {
				$menucode .= "true";
			}
			
			$menucode .= "\t";
			
			if (K_USE_FRAMES) {
				$menucode .= "index.".CP_EXT."?choosed_language=".$m['language_code']."";
				$menucode .= "\t_top";
			}
			else {
				$thislink = "".$page."?choosed_language=".$m['language_code']."";
				if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {$thislink .= "&aiocp_dp=".urlencode($aiocp_dp)."";}
				if (isset($hp)) {$thislink .= "&hp=".urlencode($hp)."";}
				$menucode .= $thislink;
				$menucode .= "\t_self";
				
			}
			
			$menucode .= "\t".$l['a_meta_charset'];
			$menucode .= "\t".F_compact_string($m['language_name']);
			$menucode .= "\t".F_compact_string($m['language_name']);
			
			//use default options
			$menucode .= "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
			$menucode .= "\n"; // end row
		}
	}
	else {
		F_display_db_error();
	}
	
	$menualtcode .= "</ul></li>";
	
	return;
}
//
//============================================================+
// END OF FILE                                                 
//============================================================+
?>
