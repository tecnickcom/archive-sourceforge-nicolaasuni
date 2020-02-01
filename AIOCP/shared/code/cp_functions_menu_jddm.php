<?php
//============================================================+
// File name   : cp_functions_menu_jddm.php
// Begin       : 2002-03-21
// Last Update : 2010-12-21
//
// Description : Functions for show Menu using JDDM applet
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
 * @param forcetext (optional, default=false) if true display menu as an unordered html list
 * @param externalfile (optional, default=true) if true load menu data from external file (created dinamically)
*/
function F_show_jddm_menu($menu_name, $forcetext=false, $externalfile=true) {
	global $l, $db, $selected_language;
	global $menucode, $menualtcode, $popup_position;
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
	$menustyle = F_get_menu_client_style($menulst_style); //get client menu style
	
	if ((!$menuconfig) OR (!$menustyle)) {
		return "";
	}
	
	//reset menu code
	$menucode = "\n";
	$menualtcode = "\n";
	
	if ($menuconfig['menuopt_autoscroll']) {
		//scroll_layer_name
		$scroll_layer_name = "scrlyr".$menu_name;
		$menucode .= "<div id=\"".$scroll_layer_name."\" style=\"left:".$menuconfig['menuopt_hspace']."px; top:".$menuconfig['menuopt_vspace']."px; width:".($menuconfig['menuopt_width']+8)."px; height:".($menuconfig['menuopt_height']+20)."px; position:relative; visibility:visible; z-index:0;\">\n";
	}
	
	$menucode .= "<applet";
	$menucode .= " codebase=\"".K_PATH_SHARED_JAVA."\"";
	$menucode .= " archive=\"jddm.jar\"";
	$menucode .= " code=\"com.tecnick.jddm.Jddm.class\"";
	$menucode .= " name=\"JM_".$menu_name."\"";
	$menucode .= " id=\"JM_".$menu_name."\"";
	$menucode .= " alt=\"\"";
	$menucode .= " width=\"".$menuconfig['menuopt_width']."\"";
	$menucode .= " height=\"".$menuconfig['menuopt_height']."\"";
	if ($menuconfig['menuopt_hspace']) {$menucode .= " hspace=\"".$menuconfig['menuopt_hspace']."\"";}
	if ($menuconfig['menuopt_vspace']) {$menucode .= " vspace=\"".$menuconfig['menuopt_vspace']."\"";}
	if ($menuconfig['menuopt_align']) {$menucode .= " align=\"".$menuconfig['menuopt_align']."\"";}
	$menucode .= ">\n";
	
	$menucode .= "<param name=\"mayscript\" value=\"mayscript\" />\n";
	$menucode .= "<param name=\"Copyright\" value=\"Tecnick.com LTD - www.tecnick.com\" />\n";
	$menucode .= "<param name=\"license_number\" value=\":: AIOCP Bundle ::\" />\n";
	
	$menucode .= "<param name=\"horizontal\" value=\"";
	if ($menuconfig['menuopt_horizontal']) {
		$menucode .= "true";
	}
	else {
		$menucode .= "false";
	}
	$menucode .= "\" />\n";
	if (!$menuconfig['menuopt_popup_position']) {
		if ($menuconfig['menuopt_horizontal']) {
			$menuconfig['menuopt_popup_position'] = "BOTTOM";
		}
		else {
			if ($l['a_meta_dir'] == "rtl") {
				$menuconfig['menuopt_popup_position'] = "LEFT";
			}
			else {
				$menuconfig['menuopt_popup_position'] = "RIGHT";
			}
		}
	}
	$menucode .= "<param name=\"popup_position\" value=\"".$menuconfig['menuopt_popup_position']."\" />\n";
	if (!$menuconfig['menuopt_arrow_position']) {
		if ($l['a_meta_dir'] == "rtl") {
			$menuconfig['menuopt_arrow_position'] = "LEFT";
		}
		else {
			$menuconfig['menuopt_arrow_position'] = "RIGHT";
		}
	}
	$menucode .= "<param name=\"arrow_position\" value=\"".$menuconfig['menuopt_arrow_position']."\" />\n";
	
	$popup_position = $menuconfig['menuopt_popup_position'];
	
	switch ($menuconfig['menuopt_popup_position']) {
		case "LEFT": {
			if ($menustyle['menustyle_arrow_img_off_left']>1) {
				$menucode .= "<param name=\"default_arrow_img_off\" value=\"".K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $menustyle['menustyle_arrow_img_off_left'])."\" />\n";
			}
			if ($menustyle['menustyle_arrow_img_over_left']>1) {
				$menucode .= "<param name=\"default_arrow_img_over\" value=\"".K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $menustyle['menustyle_arrow_img_over_left'])."\" />\n";
			}
			if ($menustyle['menustyle_arrow_img_on_left']>1) {
				$menucode .= "<param name=\"default_arrow_img_on\" value=\"".K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $menustyle['menustyle_arrow_img_on_left'])."\" />\n";
			}
			break;
		}
		case "RIGHT": {
			if ($menustyle['menustyle_arrow_img_off_right']>1) {
				$menucode .= "<param name=\"default_arrow_img_off\" value=\"".K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $menustyle['menustyle_arrow_img_off_right'])."\" />\n";
			}
			if ($menustyle['menustyle_arrow_img_over_right']>1) {
				$menucode .= "<param name=\"default_arrow_img_over\" value=\"".K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $menustyle['menustyle_arrow_img_over_right'])."\" />\n";
			}
			if ($menustyle['menustyle_arrow_img_on_right']>1) {
				$menucode .= "<param name=\"default_arrow_img_on\" value=\"".K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $menustyle['menustyle_arrow_img_on_right'])."\" />\n";
			}
			break;
		}
		case "TOP": {
			if ($menustyle['menustyle_arrow_img_off_top']>1) {
				$menucode .= "<param name=\"default_arrow_img_off\" value=\"".K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $menustyle['menustyle_arrow_img_off_top'])."\" />\n";
			}
			if ($menustyle['menustyle_arrow_img_over_top']>1) {
				$menucode .= "<param name=\"default_arrow_img_over\" value=\"".K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $menustyle['menustyle_arrow_img_over_top'])."\" />\n";
			}
			if ($menustyle['menustyle_arrow_img_on_top']>1) {
				$menucode .= "<param name=\"default_arrow_img_on\" value=\"".K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $menustyle['menustyle_arrow_img_on_top'])."\" />\n";
			}
			break;
		}
		case "BOTTOM": {
			if ($menustyle['menustyle_arrow_img_off_bottom']>1) {
				$menucode .= "<param name=\"default_arrow_img_off\" value=\"".K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $menustyle['menustyle_arrow_img_off_bottom'])."\" />\n";
			}
			if ($menustyle['menustyle_arrow_img_over_bottom']>1) {
				$menucode .= "<param name=\"default_arrow_img_over\" value=\"".K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $menustyle['menustyle_arrow_img_over_bottom'])."\" />\n";
			}
			if ($menustyle['menustyle_arrow_img_on_bottom']>1) {
				$menucode .= "<param name=\"default_arrow_img_on\" value=\"".K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $menustyle['menustyle_arrow_img_on_bottom'])."\" />\n";
			}
			break;
		}
	}
	
	if (!$menustyle['menustyle_label_position']) {
		if ($l['a_meta_dir'] == "rtl") {
			$menustyle['menustyle_label_position'] = "LEFT";
		}
		else {
			$menustyle['menustyle_label_position'] = "RIGHT";
		}
	}
	$menucode .= "<param name=\"default_label_position\" value=\"".$menustyle['menustyle_label_position']."\" />\n";
	$menucode .= "<param name=\"default_center_block\" value=\"";
	if ($menustyle['menustyle_center_block']) {
		$menucode .= "true";
	}
	else {
		$menucode .= "false";
	}
	$menucode .= "\" />\n";
	
	if ($menustyle['menustyle_padding']>=0) {$menucode .= "<param name=\"default_padding\" value=\"".$menustyle['menustyle_padding']."\" />\n";}
	if ($menustyle['menustyle_gap']>=0) {$menucode .= "<param name=\"default_gap\" value=\"".$menustyle['menustyle_gap']."\" />\n";}
	if ($menustyle['menustyle_border_width']>=0) {$menucode .= "<param name=\"default_border_width\" value=\"".$menustyle['menustyle_border_width']."\" />\n";}
	if ($menustyle['menustyle_pushed']) {$menucode .= "<param name=\"default_pushed\" value=\"true\" />\n";}
	else {$menucode .= "<param name=\"default_pushed\" value=\"false\" />\n";}
	
	if ($menustyle['menustyle_sound_over'] > 1) {$menucode .= "<param name=\"default_sound_over\" value=\"".K_PATH_SOUNDS_MENU.F_get_sound_link($menustyle['menustyle_sound_over'])."\" />\n";}
	if ($menustyle['menustyle_sound_click'] > 1) {$menucode .= "<param name=\"default_sound_click\" value=\"".K_PATH_SOUNDS_MENU.F_get_sound_link($menustyle['menustyle_sound_click'])."\" />\n";}
	
	if ($menustyle['menustyle_background_col']) {$menucode .= "<param name=\"background_col\" value=\"".substr($menustyle['menustyle_background_col'], 1)."\" />\n";}

	if ($menustyle['menustyle_colbck_off']) {$menucode .= "<param name=\"default_colbck_off\" value=\"".substr($menustyle['menustyle_colbck_off'], 1)."\" />\n";}
	if ($menustyle['menustyle_colbck_over']) {$menucode .= "<param name=\"default_colbck_over\" value=\"".substr($menustyle['menustyle_colbck_over'], 1)."\" />\n";}
	if ($menustyle['menustyle_colbck_on']) {$menucode .= "<param name=\"default_colbck_on\" value=\"".substr($menustyle['menustyle_colbck_on'], 1)."\" />\n";}

	if ($menustyle['menustyle_coltxt_off']) {$menucode .= "<param name=\"default_coltxt_off\" value=\"".substr($menustyle['menustyle_coltxt_off'], 1)."\" />\n";}
	if ($menustyle['menustyle_coltxt_over']) {$menucode .= "<param name=\"default_coltxt_over\" value=\"".substr($menustyle['menustyle_coltxt_over'], 1)."\" />\n";}
	if ($menustyle['menustyle_coltxt_on']) {$menucode .= "<param name=\"default_coltxt_on\" value=\"".substr($menustyle['menustyle_coltxt_on'], 1)."\" />\n";}

	if ($menustyle['menustyle_colsdw_off']) {$menucode .= "<param name=\"default_colsdw_off\" value=\"".substr($menustyle['menustyle_colsdw_off'], 1)."\" />\n";}
	if ($menustyle['menustyle_colsdw_over']) {$menucode .= "<param name=\"default_colsdw_over\" value=\"".substr($menustyle['menustyle_colsdw_over'], 1)."\" />\n";}
	if ($menustyle['menustyle_colsdw_on']) {$menucode .= "<param name=\"default_colsdw_on\" value=\"".substr($menustyle['menustyle_colsdw_on'], 1)."\" />\n";}

	if ($menustyle['menustyle_shadow_x']) {$menucode .= "<param name=\"default_shadow_x\" value=\"".$menustyle['menustyle_shadow_x']."\" />\n";}
	if ($menustyle['menustyle_shadow_y']) {$menucode .= "<param name=\"default_shadow_y\" value=\"".$menustyle['menustyle_shadow_y']."\" />\n";}

	if ($menustyle['menustyle_main_font']) {$menucode .= "<param name=\"default_main_font\" value=\"".$menustyle['menustyle_main_font']."\" />\n";}
	if ($menustyle['menustyle_main_font_style']) {$menucode .= "<param name=\"default_main_font_style\" value=\"".$menustyle['menustyle_main_font_style']."\" />\n";}
	if ($menustyle['menustyle_main_font_size']) {$menucode .= "<param name=\"default_main_font_size\" value=\"".$menustyle['menustyle_main_font_size']."\" />\n";}
	
	if ($menustyle['menustyle_submenu_font']) {$menucode .= "<param name=\"default_submenu_font\" value=\"".$menustyle['menustyle_submenu_font']."\" />\n";}
	if ($menustyle['menustyle_submenu_font_style']) {$menucode .= "<param name=\"default_submenu_font_style\" value=\"".$menustyle['menustyle_submenu_font_style']."\" />\n";}
	if ($menustyle['menustyle_submenu_font_size']) {$menucode .= "<param name=\"default_submenu_font_size\" value=\"".$menustyle['menustyle_submenu_font_size']."\" />\n";}
	
	if ($l['a_meta_charset']) {
		$menucode .= "<param name=\"page_encoding\" value=\"".$l['a_meta_charset']."\" />\n";
		$menucode .= "<param name=\"default_encoding\" value=\"".$l['a_meta_charset']."\" />\n";
	}
	if ($l['w_disabled']) {$menucode .= "<param name=\"disabled_msg\" value=\"".$l['w_disabled']."\" />\n";}
	
	if ($menuconfig['menuopt_target']>1) {$menucode .= "<param name=\"default_target\" value=\"".F_get_target_name($menuconfig['menuopt_target'])."\" />\n";}
	
	if ($menustyle['menustyle_bck_img_off']>1) {$menucode .= "<param name=\"default_bck_img_off\" value=\"".K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT,$menustyle['menustyle_bck_img_off'])."\" />\n";}
	if ($menustyle['menustyle_bck_img_over']>1) {$menucode .= "<param name=\"default_bck_img_over\" value=\"".K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT,$menustyle['menustyle_bck_img_over'])."\" />\n";}
	if ($menustyle['menustyle_bck_img_on']>1) {$menucode .= "<param name=\"default_bck_img_on\" value=\"".K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT,$menustyle['menustyle_bck_img_on'])."\" />\n";}
	
	if ($menustyle['menustyle_icon_off']>1) {$menucode .= "<param name=\"default_icon_off\" value=\"".K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT,$menustyle['menustyle_icon_off'])."\" />\n";}
	if ($menustyle['menustyle_icon_over']>1) {$menucode .= "<param name=\"default_icon_over\" value=\"".K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT,$menustyle['menustyle_icon_over'])."\" />\n";}
	if ($menustyle['menustyle_icon_on']>1) {$menucode .= "<param name=\"default_icon_on\" value=\"".K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT,$menustyle['menustyle_icon_on'])."\" />\n";}
	
	if ( $externalfile AND ($_SESSION['session_alt_menu'] != 1) AND (!$forcetext) ) {
		$appletheader = $menucode; //remember settings
	}
	
	$bindex = F_explore_jddm_menu_level($menutable, 0, 0);
	//display change language buttons only in main menu
	if ((K_LANG_ON_MENU) AND ($menutable == K_TABLE_MENU_CLIENT)) {
		F_display_jddm_language_button($bindex);
	}
	
	if ( $externalfile AND ($_SESSION['session_alt_menu'] != 1) AND (!$forcetext) ) {
		$menucode = $appletheader;
		$menucode .= "<param name=\"data_file\" value=\"cp_menu_data_file.".CP_EXT."?menu=".$menu_name."&amp;page=".$_SERVER['SCRIPT_NAME']."&amp;selected_language=".$selected_language."";
		if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {$menucode .= "&amp;aiocp_dp=".urlencode($aiocp_dp)."";}
		if (isset($hp)) {$menucode .= "&amp;hp=".urlencode($hp)."";}
		$menucode .= "\" />\n";
	}
	
	$menualtcode = "<ul class=\"altmenu\">".$menualtcode."</ul>\n";
	$menucode .= $menualtcode; //alternative content
	$menucode .= "</applet>";
	
	if ($menuconfig['menuopt_text']) {
		//link to switch alternate menu version
		$currentpagelink = "".$_SERVER['SCRIPT_NAME']."?altmenu=1";
		if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {$currentpagelink .= "&amp;aiocp_dp=".urlencode($aiocp_dp)."";}
		if (isset($hp)) {$currentpagelink .= "&amp;hp=".urlencode($hp)."";}
		$menucode .= "<br /><a class=\"small\" href=\"".$currentpagelink."\">txt &gt;&gt;</a>";
	}
	
	if ($menuconfig['menuopt_autoscroll']) { // autoscroll feature
		$menucode .= "</div>\n";
		
		//scrolling functions
		$menucode .= "<script language=\"JavaScript\" type=\"text/javascript\">\n";
		$menucode .= "//<![CDATA[\n";
		
		$menucode .= "var scroll_speed_".$scroll_layer_name." = 1; \n";// 1 = max speed
		$menucode .= "var pos_tollerance_".$scroll_layer_name." = Math.round(1 + (scroll_speed_".$scroll_layer_name." / 2));\n";
		$menucode .= "var previousY_".$scroll_layer_name." = 0;\n";
		$menucode .= "var newY_".$scroll_layer_name." = 0;\n";
		
		$menucode .= "function scroll_object_".$scroll_layer_name."() {\n";
		$menucode .= "	current_y = get_current_position_".$scroll_layer_name."();\n";
		$menucode .= "	if ( (current_y < (previousY_".$scroll_layer_name." - pos_tollerance_".$scroll_layer_name.")) || (current_y > (previousY_".$scroll_layer_name." + pos_tollerance_".$scroll_layer_name.")) ) {\n";
		$menucode .= "		newY_".$scroll_layer_name." = previousY_".$scroll_layer_name." + Math.round((current_y - previousY_".$scroll_layer_name.")/scroll_speed_".$scroll_layer_name.");\n";
		$menucode .= "		previousY_".$scroll_layer_name." = newY_".$scroll_layer_name.";\n";
		$menucode .= "		if (scroll_speed_".$scroll_layer_name." == 1) {\n";
		$menucode .= "			set_visibility_".$scroll_layer_name."(0);\n"; //solve a java 1.4.1 IE refresh issue
		$menucode .= "		}\n";
		$menucode .= "		set_position_".$scroll_layer_name."(newY_".$scroll_layer_name.");\n";
		$menucode .= "	}\n";
		$menucode .= "	else {\n";
		$menucode .= "		set_position_".$scroll_layer_name."(current_y);\n";
		$menucode .= "		if (scroll_speed_".$scroll_layer_name." == 1) {\n";
		$menucode .= "			set_visibility_".$scroll_layer_name."(1);\n"; //solve a java 1.4.1 IE refresh issue
		$menucode .= "		}\n";
		$menucode .= "	}\n";
		$menucode .= "	window.setTimeout(\"scroll_object_".$scroll_layer_name."()\",10);\n";
		$menucode .= "}\n";
		
		$menucode .= "function set_visibility_".$scroll_layer_name."(visibility_value) {\n";
		$menucode .= "	if (!visibility_value) {\n";
		$menucode .= "		visibility_value='hidden';\n";
		$menucode .= "	}\n";
		$menucode .= "	else {\n";
		$menucode .= "		visibility_value='visible';\n";
		$menucode .= "	}\n";
		$menucode .= "	if (document.all) {document.all.".$scroll_layer_name.".style.visibility = visibility_value;}\n";
		$menucode .= "	else if (document.layers) {document.layers['".$scroll_layer_name."'].visibility = visibility_value;}\n";
		$menucode .= "	else if (!document.all && document.getElementById) {document.getElementById('".$scroll_layer_name."').style.visibility = visibility_value;}\n";
		$menucode .= "}\n";
		
		$menucode .= "function set_position_".$scroll_layer_name."(new_position) {\n";
		$menucode .= "	if (document.all) {document.all.".$scroll_layer_name.".style.pixelTop = new_position;}\n";
		$menucode .= "	else if (document.layers) {document.layers['".$scroll_layer_name."'].top = new_position;}\n";
		$menucode .= "	else if (!document.all && document.getElementById) {document.getElementById('".$scroll_layer_name."').style.top = new_position + \"px\";}\n";
		$menucode .= "}\n";
		
		$menucode .= "function get_current_position_".$scroll_layer_name."() {\n";
		$menucode .= "	var current_y = 0;\n";
		$menucode .= "	if (document.layers) {current_y = window.pageYOffset;}\n";
		$menucode .= "	else if(!document.all && document.getElementById) {current_y = scrollY;} \n";
		$menucode .= "	else if(document.all) {current_y = document.body.scrollTop;}\n";
		$menucode .= "	return current_y;\n";
		$menucode .= "}\n";
		
		$menucode .= "scroll_object_".$scroll_layer_name."();\n";
		$menucode .= "//]]>\n";
		$menucode .= "</script>\n";
	}
	
	if (($_SESSION['session_alt_menu'] == 1) OR ($forcetext) ) { //check if user has selected alternate menu
		$menucode = "";
		// IE 5+ conditional comment makes this only visible in IE 5+
		$menucode .= "<!--[if gte IE 5]><![if lt IE 7]>\n";
		$menucode .= "<style type=\"text/css\">\n";
		//the behaviour to mimic the li:hover rules in IE 5+
		$menucode .= "ul.altmenu li {\n";
		$menucode .= "	behavior: url(".K_PATH_SHARED_JSCRIPTS."IEmen.htc);\n";
		$menucode .= "}\n";
		if ($menuconfig['menuopt_horizontal']) {
			$toppos = $menuconfig['menuopt_height']-2;
			$leftpos = "0";
		} else {
			$toppos = "0";
			$leftpos = $menuconfig['menuopt_width']-30;
		}
		$menucode .= "ul.altmenu ul {\n";
		$menucode .= "	display:none; position:absolute; top:".$toppos."px; left:".$leftpos."px;\n";
		$menucode .= "}\n";
		if ($menuconfig['menuopt_horizontal']) {
			$menucode .= "ul.altmenu ul ul {\n";
			$menucode .= "	display:none; position:absolute; top:0px; left:150px;\n";
			$menucode .= "}\n";
		}
		$menucode .= "</style>\n";
		$menucode .= "<![endif]><![endif]-->\n";
		$menucode .= $menualtcode; //alternative content
		if (!$forcetext) {
			//link to switch java menu version
			$currentpagelink = "".$_SERVER['SCRIPT_NAME']."?altmenu=0";
			if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {$currentpagelink .= "&amp;aiocp_dp=".urlencode($aiocp_dp)."";}
			if (isset($hp)) {$currentpagelink .= "&amp;hp=".urlencode($hp)."";}
			$menucode .= "<a class=\"small\" href=\"".$currentpagelink."\">java &gt;&gt;</a>";
			$menucode .= "</div>"; //alternative content
		}
	}
	
	return $menucode;
}
// ------------------------------------------------------------

// ------------------------------------------------------------
// Explore tree recursively
// Show the tree items in the right order with option buttons
// ------------------------------------------------------------
function F_explore_jddm_menu_level($menutable, $treelevel, $iid) {
	global $l, $db, $selected_language;
	global $menucode, $menualtcode, $popup_position;
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
				$menucode .= "\n";
				$menucode .= "<param name=\"id".$iid."\" value=\"".$iid."\" />\n";
				$menucode .= "<param name=\"subid".$iid."\" value=\"".$isubid."\" />\n";
				
				$menucode .= "<param name=\"node".$iid."\" value=\"";
				if ($m['menu_item']) {
					$menucode .= "false";
				}
				else {
					$menucode .= "true";
				}
				$menucode .= "\" />\n";
				$menucode .= "<param name=\"enabled".$iid."\" value=\"";
				if ($m['menu_enabled']) {
					$menucode .= "true";
				}
				else {
					$menucode .= "false";
				}
				$menucode .= "\" />\n";
				$menualtcode .= "\n<li>";
				if ($m['menu_link']) {
					$menucode .= "<param name=\"link".$iid."\" value=\"".$m['menu_link']."\" />\n";
					$menualtcode .= "<a href=\"".str_replace(" ","+",$m['menu_link'])."\"";
					if ($m['menu_target'] > 1) {
						$menucode .= "<param name=\"target".$iid."\" value=\"".F_get_target_name($m['menu_target'])."\" />\n";
						$menualtcode .= " target=\"".F_get_target_name($m['menu_target'])."\"";
					}
				}
				elseif ($menutable == K_TABLE_MENU) { //display subtree icons
					$menucode .= "<param name=\"link".$iid."\" value=\"cp_layout_main.".CP_EXT."?previous_language=".$selected_language."&amp;menu_sub_id=".$m['menu_id']."&amp;node=".urlencode($m['menu_name'])."\" />\n";				
					$menualtcode .= "<a href=\"cp_layout_main.".CP_EXT."?previous_language=".$selected_language."&amp;menu_sub_id=".$m['menu_id']."&amp;node=".urlencode($m['menu_name'])."\"";	
				}
				else {
					$menualtcode .= "<a href=\"#\"";
				}
				$menucode .= "<param name=\"name".$iid."\" value=\"".$m['menu_name']."\" />\n";
				
				if ($m['menu_description']) {
					$menucode .= "<param name=\"description".$iid."\" value=\"".$m['menu_description']."\" />\n";
					$menualtcode .= " title=\"".$m['menu_description']."\"";
				}
				
				$menualtcode .= ">".$m['menu_name']."</a>";
				
				// icons
				if ($m['menu_icon_off']>1) {
					$menucode .= "<param name=\"icon_off".$iid."\" value=\"".K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $m['menu_icon_off'])."\" />\n";
				}
				elseif ($buttonstyle AND ($buttonstyle['menustyle_icon_off']>1)) {
					$menucode .= "<param name=\"icon_off".$iid."\" value=\"".K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $buttonstyle['menustyle_icon_off'])."\" />\n";
				}
				if ($m['menu_icon_over']>1) {
					$menucode .= "<param name=\"icon_over".$iid."\" value=\"".K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $m['menu_icon_over'])."\" />\n";
				}
				elseif ($buttonstyle AND ($buttonstyle['menustyle_icon_over']>1)) {
					$menucode .= "<param name=\"icon_over".$iid."\" value=\"".K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $buttonstyle['menustyle_icon_over'])."\" />\n";
				}
				if ($m['menu_icon_on']>1) {
					$menucode .= "<param name=\"icon_on".$iid."\" value=\"".K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $m['menu_icon_on'])."\" />\n";
				}
				elseif ($buttonstyle AND ($buttonstyle['menustyle_icon_on']>1)) {
					$menucode .= "<param name=\"icon_on".$iid."\" value=\"".K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $buttonstyle['menustyle_icon_on'])."\" />\n";
				}
				
				
				if ($buttonstyle) {
					//backgrounds images
					if ($buttonstyle['menustyle_bck_img_off']>1) {
						$menucode .= "<param name=\"bck_img_off".$iid."\" value=\"".K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $buttonstyle['menustyle_bck_img_off'])."\" />\n";
					}
					if ($buttonstyle['menustyle_bck_img_over']>1) {
						$menucode .= "<param name=\"bck_img_over".$iid."\" value=\"".K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $buttonstyle['menustyle_bck_img_over'])."\" />\n";
					}
					if ($buttonstyle['menustyle_bck_img_on']>1) {
						$menucode .= "<param name=\"bck_img_on".$iid."\" value=\"".K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $buttonstyle['menustyle_bck_img_on'])."\" />\n";
					}
					
					//sounds
					if ($buttonstyle['menustyle_sound_over']>1) {
						$menucode .= "<param name=\"sound_over".$iid."\" value=\"".K_PATH_SOUNDS_MENU.F_get_sound_link($buttonstyle['menustyle_sound_over'])."\" />\n";
					}
					if ($buttonstyle['menustyle_sound_click']>1) {
						$menucode .= "<param name=\"sound_click".$iid."\" value=\"".K_PATH_SOUNDS_MENU.F_get_sound_link($buttonstyle['menustyle_sound_click'])."\" />\n";
					}
					
					if ($buttonstyle['menustyle_colbck_off']) {$menucode .= "<param name=\"colbck_off".$iid."\" value=\"".substr($buttonstyle['menustyle_colbck_off'], 1)."\" />\n";}
					if ($buttonstyle['menustyle_colbck_over']) {$menucode .= "<param name=\"colbck_over".$iid."\" value=\"".substr($buttonstyle['menustyle_colbck_over'], 1)."\" />\n";}
					if ($buttonstyle['menustyle_colbck_on']) {$menucode .= "<param name=\"colbck_on".$iid."\" value=\"".substr($buttonstyle['menustyle_colbck_on'], 1)."\" />\n";}
					if ($buttonstyle['menustyle_coltxt_off']) {$menucode .= "<param name=\"coltxt_off".$iid."\" value=\"".substr($buttonstyle['menustyle_coltxt_off'], 1)."\" />\n";}
					if ($buttonstyle['menustyle_coltxt_over']) {$menucode .= "<param name=\"coltxt_over".$iid."\" value=\"".substr($buttonstyle['menustyle_coltxt_over'], 1)."\" />\n";}
					if ($buttonstyle['menustyle_coltxt_on']) {$menucode .= "<param name=\"coltxt_on".$iid."\" value=\"".substr($buttonstyle['menustyle_coltxt_on'], 1)."\" />\n";}
					if ($buttonstyle['menustyle_colsdw_off']) {$menucode .= "<param name=\"colsdw_off".$iid."\" value=\"".substr($buttonstyle['menustyle_colsdw_off'], 1)."\" />\n";}
					if ($buttonstyle['menustyle_colsdw_over']) {$menucode .= "<param name=\"colsdw_over".$iid."\" value=\"".substr($buttonstyle['menustyle_colsdw_over'], 1)."\" />\n";}
					if ($buttonstyle['menustyle_colsdw_on']) {$menucode .= "<param name=\"colsdw_on".$iid."\" value=\"".substr($buttonstyle['menustyle_colsdw_on'], 1)."\" />\n";}
					
					if ($buttonstyle['menustyle_shadow_x']) {$menucode .= "<param name=\"shadow_x".$iid."\" value=\"".$buttonstyle['menustyle_shadow_x']."\" />\n";}
					if ($buttonstyle['menustyle_shadow_y']) {$menucode .= "<param name=\"shadow_y".$iid."\" value=\"".$buttonstyle['menustyle_shadow_y']."\" />\n";}
					
					//main font
					if ($isubid == 0) {
						if ($buttonstyle['menustyle_main_font']) {$menucode .= "<param name=\"font".$iid."\" value=\"".$buttonstyle['menustyle_main_font']."\" />\n";}
						if ($buttonstyle['menustyle_main_font_style']) {$menucode .= "<param name=\"font_style".$iid."\" value=\"".$buttonstyle['menustyle_main_font_style']."\" />\n";}
						if ($buttonstyle['menustyle_main_font_size']) {$menucode .= "<param name=\"font_size".$iid."\" value=\"".$buttonstyle['menustyle_main_font_size']."\" />\n";}
					}
					else {
						if ($buttonstyle['menustyle_submenu_font']) {$menucode .= "<param name=\"font".$iid."\" value=\"".$buttonstyle['menustyle_submenu_font']."\" />\n";}
						if ($buttonstyle['menustyle_submenu_font_style']) {$menucode .= "<param name=\"font_style".$iid."\" value=\"".$buttonstyle['menustyle_submenu_font_style']."\" />\n";}
						if ($buttonstyle['menustyle_submenu_font_size']) {$menucode .= "<param name=\"font_size".$iid."\" value=\"".$buttonstyle['menustyle_submenu_font_size']."\" />\n";}
					}
					
					//positions
					if (!$buttonstyle['menustyle_label_position']) {
						if ($l['a_meta_dir'] == "rtl") {
							$buttonstyle['menustyle_label_position'] = "LEFT";
						}
						else {
							$buttonstyle['menustyle_label_position'] = "RIGHT";
						}
					}
					$menucode .= "<param name=\"label_position".$iid."\" value=\"".$buttonstyle['menustyle_label_position']."\" />\n";
					$menucode .= "<param name=\"center_block".$iid."\" value=\"";
					if ($buttonstyle['menustyle_center_block']) {
						$menucode .= "true";
					}
					else {
						$menucode .= "false";
					}
					$menucode .= "\" />\n";
					
					if ($buttonstyle['menustyle_padding']>=0) {$menucode .= "<param name=\"padding".$iid."\" value=\"".$buttonstyle['menustyle_padding']."\" />\n";}
					if ($buttonstyle['menustyle_gap']>=0) {$menucode .= "<param name=\"gap".$iid."\" value=\"".$buttonstyle['menustyle_gap']."\" />\n";}
					if ($buttonstyle['menustyle_border_width']>=0) {$menucode .= "<param name=\"border_width".$iid."\" value=\"".$buttonstyle['menustyle_border_width']."\" />\n";}
					
					if ($buttonstyle['menustyle_pushed']) {$menucode .= "<param name=\"pushed".$iid."\" value=\"true\" />\n";}
					else {$menucode .= "<param name=\"pushed".$iid."\" value=\"false\" />\n";}
					
					
					//arrows
					switch ($popup_position) {
						case "LEFT": {
							if ($buttonstyle['buttonstyle_arrow_img_off_left']>1) {
								$menucode .= "<param name=\"arrow_img_off".$iid."\" value=\"".K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $buttonstyle['buttonstyle_arrow_img_off_left'])."\" />\n";
							}
							if ($buttonstyle['buttonstyle_arrow_img_over_left']>1) {
								$menucode .= "<param name=\"arrow_img_over".$iid."\" value=\"".K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $buttonstyle['buttonstyle_arrow_img_over_left'])."\" />\n";
							}
							if ($buttonstyle['buttonstyle_arrow_img_on_left']>1) {
								$menucode .= "<param name=\"arrow_img_on".$iid."\" value=\"".K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $buttonstyle['buttonstyle_arrow_img_on_left'])."\" />\n";
							}
							break;
						}
						case "RIGHT": {
							if ($buttonstyle['buttonstyle_arrow_img_off_right']>1) {
								$menucode .= "<param name=\"arrow_img_off".$iid."\" value=\"".K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $buttonstyle['buttonstyle_arrow_img_off_right'])."\" />\n";
							}
							if ($buttonstyle['buttonstyle_arrow_img_over_right']>1) {
								$menucode .= "<param name=\"arrow_img_over".$iid."\" value=\"".K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $buttonstyle['buttonstyle_arrow_img_over_right'])."\" />\n";
							}
							if ($buttonstyle['buttonstyle_arrow_img_on_right']>1) {
								$menucode .= "<param name=\"arrow_img_on".$iid."\" value=\"".K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $buttonstyle['buttonstyle_arrow_img_on_right'])."\" />\n";
							}
							break;
						}
						case "TOP": {
							if ($buttonstyle['buttonstyle_arrow_img_off_top']>1) {
								$menucode .= "<param name=\"arrow_img_off".$iid."\" value=\"".K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $buttonstyle['buttonstyle_arrow_img_off_top'])."\" />\n";
							}
							if ($buttonstyle['buttonstyle_arrow_img_over_top']>1) {
								$menucode .= "<param name=\"arrow_img_over".$iid."\" value=\"".K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $buttonstyle['buttonstyle_arrow_img_over_top'])."\" />\n";
							}
							if ($buttonstyle['buttonstyle_arrow_img_on_top']>1) {
								$menucode .= "<param name=\"arrow_img_on".$iid."\" value=\"".K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $buttonstyle['buttonstyle_arrow_img_on_top'])."\" />\n";
							}
							break;
						}
						case "BOTTOM": {
							if ($buttonstyle['buttonstyle_arrow_img_off_bottom']>1) {
								$menucode .= "<param name=\"arrow_img_off".$iid."\" value=\"".K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $buttonstyle['buttonstyle_arrow_img_off_bottom'])."\" />\n";
							}
							if ($buttonstyle['buttonstyle_arrow_img_over_bottom']>1) {
								$menucode .= "<param name=\"arrow_img_over".$iid."\" value=\"".K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $buttonstyle['buttonstyle_arrow_img_over_bottom'])."\" />\n";
							}
							if ($buttonstyle['buttonstyle_arrow_img_on_bottom']>1) {
								$menucode .= "<param name=\"arrow_img_on".$iid."\" value=\"".K_PATH_IMAGES_ICONS_CLIENT.F_get_icon_link(K_TABLE_ICONS_CLIENT, $buttonstyle['buttonstyle_arrow_img_on_bottom'])."\" />\n";
							}
							break;
						}
					}
				}
				
				if(!$m['menu_item']) { //is a node (not the first)
					$menualtcode .= "\n<!--[if lte IE 6]><iframe class=\"menu\"></iframe><![endif]-->\n";
					$menualtcode .= "<ul>";
					$iid = F_explore_jddm_menu_level($menutable, $m['menu_id'], $iid);
					if(substr($menualtcode,-4,4)=="<ul>") {
						$menualtcode = substr($menualtcode,0,-4);
					} else {
						$menualtcode .= "</ul>";
					}
					$menualtcode .= "</li>";
				}
				else {
					$menualtcode .= "</li>";
				}
			} //end if disabled
		}
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
function F_display_jddm_language_button($iid) {
	global $l, $db, $selected_language;
	global $menucode, $menualtcode;
	global $aiocp_dp, $hp; //dynamic page name
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	require_once('../../shared/code/cp_functions_target.'.CP_EXT);
	
	$isubid = $iid;
	$iid++;
	
	//laguage button
	$menucode .= "\n\n";
	$menucode .= "<param name=\"id".$iid."\" value=\"".$iid."\" />\n";
	$menucode .= "<param name=\"subid".$iid."\" value=\"0\" />\n";
	$menucode .= "<param name=\"node".$iid."\" value=\"true\" />\n";
	$menucode .= "<param name=\"enabled".$iid."\" value=\"true\" />\n";
	$menucode .= "<param name=\"name".$iid."\" value=\"".$l['w_language']."\" />\n";
	$menucode .= "<param name=\"description".$iid."\" value=\"".$l['d_language_change']."\" />\n";
	
	$menualtcode .= "\n<li><a href=\"#\" title=\"".$l['w_language']."\">".$l['w_language']."</a><ul>";
	
	//languages
	$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			$iid++;
			$menucode .= "\n\n";
			$menucode .= "<param name=\"id".$iid."\" value=\"".$iid."\" />\n";
			$menucode .= "<param name=\"subid".$iid."\" value=\"".$isubid."\" />\n";
			$menucode .= "<param name=\"node".$iid."\" value=\"false\" />\n";
			$menucode .= "<param name=\"enabled".$iid."\" value=\"";
			if ($m['language_code'] == $selected_language) { //disable current language
				$menucode .= "false";
			}
			else {
				$menucode .= "true";
			}
			$menucode .= "\" />\n";
			
			if (K_USE_FRAMES) {
				$thislink = "index.".CP_EXT."?choosed_language=".$m['language_code']."";
				if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {$thislink .= "&amp;aiocp_dp=".urlencode($aiocp_dp)."";}
				if (isset($hp)) {$thislink .= "&amp;hp=".urlencode($hp)."";}
				$menucode .= "<param name=\"target".$iid."\" value=\"_top\" />\n";
				$menucode .= "<param name=\"link".$iid."\" value=\"".$thislink."\" />\n";
				$menualtcode .= "\n<li><a href=\"".$thislink."\" target=\"_top\">".$m['language_name']."</a></li>";
			}
			else {
				$thislink = "".$_SERVER['SCRIPT_NAME']."?choosed_language=".$m['language_code']."";
				if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {$thislink .= "&amp;aiocp_dp=".urlencode($aiocp_dp)."";}
				if (isset($hp)) {$thislink .= "&amp;hp=".urlencode($hp)."";}
				$menucode .= "<param name=\"target".$iid."\" value=\"_self\" />\n";
				$menucode .= "<param name=\"link".$iid."\" value=\"".$thislink."\" />\n";
				$menualtcode .= "\n<li><a href=\"".$thislink."\" title=\"".$m['language_name']."\" target=\"_self\">".$m['language_name']."</a></li>";
			}
			$menucode .= "<param name=\"name".$iid."\" value=\"".$m['language_name']."\" />\n";
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
