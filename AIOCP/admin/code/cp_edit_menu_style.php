<?php
//============================================================+
// File name   : cp_edit_menu_style.php                        
// Begin       : 2002-03-21                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit styles for dynamic menu                  
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

require_once('../../shared/config/cp_extension.inc');
require_once('../config/cp_config.'.CP_EXT);
require_once('../../shared/code/cp_functions_form.'.CP_EXT);
require_once('../code/cp_functions_upload.'.CP_EXT);

$pagelevel = K_AUTH_ADMIN_CP_EDIT_MENU_STYLE;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

require_once('../../shared/code/cp_functions_icons.'.CP_EXT);

$thispage_title = $l['t_menu_style_editor'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages; ?>
<!-- ====================================================== -->
<?php

$javascript_display = "";
$id = 0;

//get images IDs from links
if (isset($menustyle_bck_img_off) AND (!empty($menustyle_bck_img_off))) {$bck_img_off = F_get_icon_id(K_TABLE_ICONS_CLIENT, $menustyle_bck_img_off);}
if (isset($menustyle_bck_img_over) AND (!empty($menustyle_bck_img_over))) {$bck_img_over = F_get_icon_id(K_TABLE_ICONS_CLIENT, $menustyle_bck_img_over);}
if (isset($menustyle_bck_img_on) AND (!empty($menustyle_bck_img_on))) {$bck_img_on = F_get_icon_id(K_TABLE_ICONS_CLIENT, $menustyle_bck_img_on);}
if (isset($menustyle_icon_off) AND (!empty($menustyle_icon_off))) {$icon_off = F_get_icon_id(K_TABLE_ICONS_CLIENT, $menustyle_icon_off);}
if (isset($menustyle_icon_over) AND (!empty($menustyle_icon_over))) {$icon_over = F_get_icon_id(K_TABLE_ICONS_CLIENT, $menustyle_icon_over);}
if (isset($menustyle_icon_on) AND (!empty($menustyle_icon_on))) {$icon_on = F_get_icon_id(K_TABLE_ICONS_CLIENT, $menustyle_icon_on);}
if (isset($menustyle_arrow_img_off_left) AND (!empty($menustyle_arrow_img_off_left))) {$arrow_img_off_left = F_get_icon_id(K_TABLE_ICONS_CLIENT, $menustyle_arrow_img_off_left);}
if (isset($menustyle_arrow_img_over_left) AND (!empty($menustyle_arrow_img_over_left))) {$arrow_img_over_left = F_get_icon_id(K_TABLE_ICONS_CLIENT, $menustyle_arrow_img_over_left);}
if (isset($menustyle_arrow_img_on_left) AND (!empty($menustyle_arrow_img_on_left))) {$arrow_img_on_left = F_get_icon_id(K_TABLE_ICONS_CLIENT, $menustyle_arrow_img_on_left);}
if (isset($menustyle_arrow_img_off_right) AND (!empty($menustyle_arrow_img_off_right))) {$arrow_img_off_right = F_get_icon_id(K_TABLE_ICONS_CLIENT, $menustyle_arrow_img_off_right);}
if (isset($menustyle_arrow_img_over_right) AND (!empty($menustyle_arrow_img_over_right))) {$arrow_img_over_right = F_get_icon_id(K_TABLE_ICONS_CLIENT, $menustyle_arrow_img_over_right);}
if (isset($menustyle_arrow_img_on_right) AND (!empty($menustyle_arrow_img_on_right))) {$arrow_img_on_right = F_get_icon_id(K_TABLE_ICONS_CLIENT, $menustyle_arrow_img_on_right);}
if (isset($menustyle_arrow_img_off_top) AND (!empty($menustyle_arrow_img_off_top))) {$arrow_img_off_top = F_get_icon_id(K_TABLE_ICONS_CLIENT, $menustyle_arrow_img_off_top);}
if (isset($menustyle_arrow_img_over_top) AND (!empty($menustyle_arrow_img_over_top))) {$arrow_img_over_top = F_get_icon_id(K_TABLE_ICONS_CLIENT, $menustyle_arrow_img_over_top);}
if (isset($menustyle_arrow_img_on_top) AND (!empty($menustyle_arrow_img_on_top))) {$arrow_img_on_top = F_get_icon_id(K_TABLE_ICONS_CLIENT, $menustyle_arrow_img_on_top);}
if (isset($menustyle_arrow_img_off_bottom) AND (!empty($menustyle_arrow_img_off_bottom))) {$arrow_img_off_bottom = F_get_icon_id(K_TABLE_ICONS_CLIENT, $menustyle_arrow_img_off_bottom);}
if (isset($menustyle_arrow_img_over_bottom) AND (!empty($menustyle_arrow_img_over_bottom))) {$arrow_img_over_bottom = F_get_icon_id(K_TABLE_ICONS_CLIENT, $menustyle_arrow_img_over_bottom);}
if (isset($menustyle_arrow_img_on_bottom) AND (!empty($menustyle_arrow_img_on_bottom))) {$arrow_img_on_bottom = F_get_icon_id(K_TABLE_ICONS_CLIENT, $menustyle_arrow_img_on_bottom);}

if (isset($_REQUEST['menu_mode'])) {
	$menu_mode = $_REQUEST['menu_mode'];
} else {
	$menu_mode = "";
}
switch($menu_mode) {

	case unhtmlentities($l['w_delete']):
	case $l['w_delete']:{
		F_stripslashes_formfields(); // Delete
		//check if it's used before delete
		$sql = "SELECT COUNT(*) FROM ".K_TABLE_MENU_LIST." WHERE menulst_style='".$menustyle_id."'";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$styleused = $m['0'];
			}
		}
		else {
			F_display_db_error();
		}
		if (isset($styleused) AND $styleused) {
			F_print_error("WARNING", $l['m_not_delete_used_style']);
		}
		else {
			$sql = "DELETE FROM ".K_TABLE_MENU_STYLES." WHERE menustyle_id=".$menustyle_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			$menustyle_id=FALSE;
		}
		break;
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_MENU_STYLES,"menustyle_name='".$menustyle_name."'","menustyle_id",$menustyle_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$sql = "UPDATE IGNORE ".K_TABLE_MENU_STYLES." SET 
						menustyle_name='".$menustyle_name."', 
						menustyle_label_position='".$menustyle_label_position."', 
						menustyle_center_block='".$menustyle_center_block."', 
						menustyle_padding='".$menustyle_padding."', 
						menustyle_gap='".$menustyle_gap."', 
						menustyle_border_width='".$menustyle_border_width."', 
						menustyle_pushed='".$menustyle_pushed."', 
						menustyle_sound_over='".$menustyle_sound_over."', 
						menustyle_sound_click='".$menustyle_sound_click."', 
						menustyle_background_col='".$menustyle_background_col."', 
						menustyle_colbck_off='".$menustyle_colbck_off."', 
						menustyle_colbck_over='".$menustyle_colbck_over."', 
						menustyle_colbck_on='".$menustyle_colbck_on."', 
						menustyle_coltxt_off='".$menustyle_coltxt_off."', 
						menustyle_coltxt_over='".$menustyle_coltxt_over."', 
						menustyle_coltxt_on='".$menustyle_coltxt_on."', 
						menustyle_colsdw_off='".$menustyle_colsdw_off."', 
						menustyle_colsdw_over='".$menustyle_colsdw_over."', 
						menustyle_colsdw_on='".$menustyle_colsdw_on."', 
						menustyle_shadow_x='".$menustyle_shadow_x."', 
						menustyle_shadow_y='".$menustyle_shadow_y."', 
						menustyle_main_font='".$menustyle_main_font."', 
						menustyle_main_font_style='".$menustyle_main_font_style."', 
						menustyle_main_font_size='".$menustyle_main_font_size."', 
						menustyle_submenu_font='".$menustyle_submenu_font."', 
						menustyle_submenu_font_style='".$menustyle_submenu_font_style."', 
						menustyle_submenu_font_size='".$menustyle_submenu_font_size."', 
						menustyle_bck_img_off='".$bck_img_off."', 
						menustyle_bck_img_over='".$bck_img_over."', 
						menustyle_bck_img_on='".$bck_img_on."', 
						menustyle_icon_off='".$icon_off."', 
						menustyle_icon_over='".$icon_over."', 
						menustyle_icon_on='".$icon_on."', 
						menustyle_arrow_img_off_left='".$arrow_img_off_left."', 
						menustyle_arrow_img_over_left='".$arrow_img_over_left."', 
						menustyle_arrow_img_on_left='".$arrow_img_on_left."', 
						menustyle_arrow_img_off_right='".$arrow_img_off_right."', 
						menustyle_arrow_img_over_right='".$arrow_img_over_right."', 
						menustyle_arrow_img_on_right='".$arrow_img_on_right."', 
						menustyle_arrow_img_off_top='".$arrow_img_off_top."', 
						menustyle_arrow_img_over_top='".$arrow_img_over_top."', 
						menustyle_arrow_img_on_top='".$arrow_img_on_top."', 
						menustyle_arrow_img_off_bottom='".$arrow_img_off_bottom."', 
						menustyle_arrow_img_over_bottom='".$arrow_img_over_bottom."', 
						menustyle_arrow_img_on_bottom='".$arrow_img_on_bottom."' 
						WHERE menustyle_id=".$menustyle_id."";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
			}
		}
		break;
	}

	case unhtmlentities($l['w_add']):
	case $l['w_add']:{ // Add
		if($formstatus = F_check_form_fields()) {
			//check if menustyle_name is unique
			$sql = "SELECT menustyle_name FROM ".K_TABLE_MENU_STYLES." WHERE menustyle_name='".$menustyle_name."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				//upload images
				$sql = "INSERT IGNORE INTO ".K_TABLE_MENU_STYLES." (
						menustyle_name,
						menustyle_label_position,
						menustyle_center_block,
						menustyle_padding,
						menustyle_gap,
						menustyle_border_width,
						menustyle_pushed,
						menustyle_sound_over,
						menustyle_sound_click,
						menustyle_background_col,
						menustyle_colbck_off,
						menustyle_colbck_over,
						menustyle_colbck_on,
						menustyle_coltxt_off,
						menustyle_coltxt_over,
						menustyle_coltxt_on,
						menustyle_colsdw_off, 
						menustyle_colsdw_over, 
						menustyle_colsdw_on, 
						menustyle_shadow_x, 
						menustyle_shadow_y, 
						menustyle_main_font,
						menustyle_main_font_style,
						menustyle_main_font_size,
						menustyle_submenu_font,
						menustyle_submenu_font_style,
						menustyle_submenu_font_size,
						menustyle_bck_img_off,
						menustyle_bck_img_over,
						menustyle_bck_img_on,
						menustyle_icon_off,
						menustyle_icon_over,
						menustyle_icon_on,
						menustyle_arrow_img_off_left,
						menustyle_arrow_img_over_left,
						menustyle_arrow_img_on_left,
						menustyle_arrow_img_off_right,
						menustyle_arrow_img_over_right,
						menustyle_arrow_img_on_right,
						menustyle_arrow_img_off_top,
						menustyle_arrow_img_over_top,
						menustyle_arrow_img_on_top,
						menustyle_arrow_img_off_bottom,
						menustyle_arrow_img_over_bottom,
						menustyle_arrow_img_on_bottom
						) VALUES (
						'".$menustyle_name."', 
						'".$menustyle_label_position."', 
						'".$menustyle_center_block."', 
						'".$menustyle_padding."', 
						'".$menustyle_gap."', 
						'".$menustyle_border_width."', 
						'".$menustyle_pushed."', 
						'".$menustyle_sound_over."', 
						'".$menustyle_sound_click."', 
						'".$menustyle_background_col."', 
						'".$menustyle_colbck_off."', 
						'".$menustyle_colbck_over."', 
						'".$menustyle_colbck_on."', 
						'".$menustyle_coltxt_off."', 
						'".$menustyle_coltxt_over."', 
						'".$menustyle_coltxt_on."', 
						'".$menustyle_colsdw_off."', 
						'".$menustyle_colsdw_over."', 
						'".$menustyle_colsdw_on."', 
						'".$menustyle_shadow_x."', 
						'".$menustyle_shadow_y."', 
						'".$menustyle_main_font."', 
						'".$menustyle_main_font_style."', 
						'".$menustyle_main_font_size."', 
						'".$menustyle_submenu_font."', 
						'".$menustyle_submenu_font_style."', 
						'".$menustyle_submenu_font_size."', 
						'".$bck_img_off."', 
						'".$bck_img_over."', 
						'".$bck_img_on."', 
						'".$icon_off."', 
						'".$icon_over."', 
						'".$icon_on."', 
						'".$arrow_img_off_left."', 
						'".$arrow_img_over_left."', 
						'".$arrow_img_on_left."', 
						'".$arrow_img_off_right."', 
						'".$arrow_img_over_right."', 
						'".$arrow_img_on_right."', 
						'".$arrow_img_off_top."', 
						'".$arrow_img_over_top."', 
						'".$arrow_img_on_top."', 
						'".$arrow_img_off_bottom."', 
						'".$arrow_img_over_bottom."', 
						'".$arrow_img_on_bottom."'
						)";
						
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$menustyle_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
	}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$menustyle_name = "";
		$menustyle_label_position = "";
		$menustyle_center_block = 0;
		$menustyle_padding = 1;
		$menustyle_gap = 2;
		$menustyle_border_width = 2;
		$menustyle_pushed = 1;
		$menustyle_sound_over = "";
		$menustyle_sound_click = "";
		$menustyle_background_col = "FFFFFF";
		$menustyle_colbck_off = "ECE9D8";
		$menustyle_colbck_over = "3366CC";
		$menustyle_colbck_on = "ADAA99";
		$menustyle_coltxt_off = "000000";
		$menustyle_coltxt_over = "FFFFFF";
		$menustyle_coltxt_on = "000000";
		$menustyle_colsdw_off = "FFFFFF";
		$menustyle_colsdw_over = "FFFFFF";
		$menustyle_colsdw_on = "FFFFFF";
		$menustyle_shadow_x = 0;
		$menustyle_shadow_y = 0;
		$menustyle_main_font = "Helvetica, Verdana, Arial";
		$menustyle_main_font_style = "PLAIN";
		$menustyle_main_font_size = "11";
		$menustyle_submenu_font = "Helvetica, Verdana, Arial";
		$menustyle_submenu_font_style = "PLAIN";
		$menustyle_submenu_font_size = "11";
		$menustyle_bck_img_off = "";
		$menustyle_bck_img_over = "";
		$menustyle_bck_img_on = "";
		$menustyle_icon_off = "";
		$menustyle_icon_over = "";
		$menustyle_icon_on = "";
		$menustyle_arrow_img_off_left = "";
		$menustyle_arrow_img_over_left = "";
		$menustyle_arrow_img_on_left = "";
		$menustyle_arrow_img_off_right = "";
		$menustyle_arrow_img_over_right = "";
		$menustyle_arrow_img_on_right = "";
		$menustyle_arrow_img_off_top = "";
		$menustyle_arrow_img_over_top = "";
		$menustyle_arrow_img_on_top = "";
		$menustyle_arrow_img_off_bottom = "";
		$menustyle_arrow_img_over_bottom = "";
		$menustyle_arrow_img_on_bottom = "";
		break;
	}

	default :{ 
		break;
	}

} //end of switch

// Initialize variables
if($formstatus) {
	
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!isset($menustyle_id) OR (!$menustyle_id)) {
			$sql = "SELECT * FROM ".K_TABLE_MENU_STYLES." ORDER BY menustyle_name LIMIT 1";
		} else {
			$sql = "SELECT * FROM ".K_TABLE_MENU_STYLES." WHERE menustyle_id=".$menustyle_id." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$menustyle_id = $m['menustyle_id'];
				$menustyle_name = $m['menustyle_name'];
				$menustyle_label_position = $m['menustyle_label_position'];
				$menustyle_center_block = $m['menustyle_center_block'];
				$menustyle_padding = $m['menustyle_padding'];
				$menustyle_gap = $m['menustyle_gap'];
				$menustyle_border_width = $m['menustyle_border_width'];
				$menustyle_pushed = $m['menustyle_pushed'];
				$menustyle_sound_over = $m['menustyle_sound_over'];
				$menustyle_sound_click = $m['menustyle_sound_click'];
				$menustyle_background_col = $m['menustyle_background_col'];
				$menustyle_colbck_off = $m['menustyle_colbck_off'];
				$menustyle_colbck_over = $m['menustyle_colbck_over'];
				$menustyle_colbck_on = $m['menustyle_colbck_on'];
				$menustyle_coltxt_off = $m['menustyle_coltxt_off'];
				$menustyle_coltxt_over = $m['menustyle_coltxt_over'];
				$menustyle_coltxt_on = $m['menustyle_coltxt_on'];
				$menustyle_colsdw_off = $m['menustyle_colsdw_off'];
				$menustyle_colsdw_over = $m['menustyle_colsdw_over'];
				$menustyle_colsdw_on = $m['menustyle_colsdw_on'];
				$menustyle_shadow_x = $m['menustyle_shadow_x'];
				$menustyle_shadow_y = $m['menustyle_shadow_y'];
				$menustyle_main_font = $m['menustyle_main_font'];
				$menustyle_main_font_style = $m['menustyle_main_font_style'];
				$menustyle_main_font_size = $m['menustyle_main_font_size'];
				$menustyle_submenu_font = $m['menustyle_submenu_font'];
				$menustyle_submenu_font_style = $m['menustyle_submenu_font_style'];
				$menustyle_submenu_font_size = $m['menustyle_submenu_font_size'];
				$menustyle_bck_img_off = F_get_icon_link(K_TABLE_ICONS_CLIENT, $m['menustyle_bck_img_off']);
				$menustyle_bck_img_over = F_get_icon_link(K_TABLE_ICONS_CLIENT, $m['menustyle_bck_img_over']);
				$menustyle_bck_img_on = F_get_icon_link(K_TABLE_ICONS_CLIENT, $m['menustyle_bck_img_on']);
				$menustyle_icon_off = F_get_icon_link(K_TABLE_ICONS_CLIENT, $m['menustyle_icon_off']);
				$menustyle_icon_over = F_get_icon_link(K_TABLE_ICONS_CLIENT, $m['menustyle_icon_over']);
				$menustyle_icon_on = F_get_icon_link(K_TABLE_ICONS_CLIENT, $m['menustyle_icon_on']);
				$menustyle_arrow_img_off_left = F_get_icon_link(K_TABLE_ICONS_CLIENT, $m['menustyle_arrow_img_off_left']);
				$menustyle_arrow_img_over_left = F_get_icon_link(K_TABLE_ICONS_CLIENT, $m['menustyle_arrow_img_over_left']);
				$menustyle_arrow_img_on_left = F_get_icon_link(K_TABLE_ICONS_CLIENT, $m['menustyle_arrow_img_on_left']);
				$menustyle_arrow_img_off_right = F_get_icon_link(K_TABLE_ICONS_CLIENT, $m['menustyle_arrow_img_off_right']);
				$menustyle_arrow_img_over_right = F_get_icon_link(K_TABLE_ICONS_CLIENT, $m['menustyle_arrow_img_over_right']);
				$menustyle_arrow_img_on_right = F_get_icon_link(K_TABLE_ICONS_CLIENT, $m['menustyle_arrow_img_on_right']);
				$menustyle_arrow_img_off_top = F_get_icon_link(K_TABLE_ICONS_CLIENT, $m['menustyle_arrow_img_off_top']);
				$menustyle_arrow_img_over_top = F_get_icon_link(K_TABLE_ICONS_CLIENT, $m['menustyle_arrow_img_over_top']);
				$menustyle_arrow_img_on_top = F_get_icon_link(K_TABLE_ICONS_CLIENT, $m['menustyle_arrow_img_on_top']);
				$menustyle_arrow_img_off_bottom = F_get_icon_link(K_TABLE_ICONS_CLIENT, $m['menustyle_arrow_img_off_bottom']);
				$menustyle_arrow_img_over_bottom = F_get_icon_link(K_TABLE_ICONS_CLIENT, $m['menustyle_arrow_img_over_bottom']);
				$menustyle_arrow_img_on_bottom = F_get_icon_link(K_TABLE_ICONS_CLIENT, $m['menustyle_arrow_img_on_bottom']);
			}
			else {
				$menustyle_name = "";
				$menustyle_label_position = "";
				$menustyle_center_block = 0;
				$menustyle_padding = 1;
				$menustyle_gap = 2;
				$menustyle_border_width = 2;
				$menustyle_pushed = 1;
				$menustyle_sound_over = "";
				$menustyle_sound_click = "";
				$menustyle_background_col = "FFFFFF";
				$menustyle_colbck_off = "ECE9D8";
				$menustyle_colbck_over = "3366CC";
				$menustyle_colbck_on = "ADAA99";
				$menustyle_coltxt_off = "000000";
				$menustyle_coltxt_over = "FFFFFF";
				$menustyle_coltxt_on = "000000";
				$menustyle_colsdw_off = "FFFFFF";
				$menustyle_colsdw_over = "FFFFFF";
				$menustyle_colsdw_on = "FFFFFF";
				$menustyle_shadow_x = 0;
				$menustyle_shadow_y = 0;
				$menustyle_main_font = "Helvetica, Verdana, Arial";
				$menustyle_main_font_style = "PLAIN";
				$menustyle_main_font_size = "11";
				$menustyle_submenu_font = "Helvetica, Verdana, Arial";
				$menustyle_submenu_font_style = "PLAIN";
				$menustyle_submenu_font_size = "11";
				$menustyle_bck_img_off = "";
				$menustyle_bck_img_over = "";
				$menustyle_bck_img_on = "";
				$menustyle_icon_off = "";
				$menustyle_icon_over = "";
				$menustyle_icon_on = "";
				$menustyle_arrow_img_off_left = "";
				$menustyle_arrow_img_over_left = "";
				$menustyle_arrow_img_on_left = "";
				$menustyle_arrow_img_off_right = "";
				$menustyle_arrow_img_over_right = "";
				$menustyle_arrow_img_on_right = "";
				$menustyle_arrow_img_off_top = "";
				$menustyle_arrow_img_over_top = "";
				$menustyle_arrow_img_on_top = "";
				$menustyle_arrow_img_off_bottom = "";
				$menustyle_arrow_img_over_bottom = "";
				$menustyle_arrow_img_on_bottom = "";
			}
		}
		else {
			F_display_db_error();
		}
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_menustyleeditor" id="form_menustyleeditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="menustyle_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT style ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_style', 'h_menustyle_select'); ?></b></td>
<td class="fillOE" colspan="2">
<select name="menustyle_id" id="menustyle_id" size="0" onchange="document.form_menustyleeditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_MENU_STYLES." ORDER BY menustyle_name";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['menustyle_id']."\"";
		if($m['menustyle_id'] == $menustyle_id) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['menustyle_name'], ENT_NOQUOTES, $l['a_meta_charset'])."&nbsp;</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT style ==================== -->

<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE" colspan="2"><hr /></td>
</tr>

<tr class="fillO">
<td class="fillOE" align="right"><b><?php echo F_display_field_name('w_name', 'h_menustyle_name'); ?></b></td>
<td class="fillOE" colspan="2"><input type="text" name="menustyle_name" id="menustyle_name" value="<?php echo htmlentities($menustyle_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="20" maxlength="255" /></td>
</tr>


<!--  -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_padding', 'h_menuopt_padding'); ?></b></td>
<td class="fillEE" colspan="2"><input type="text" name="menustyle_padding" id="menustyle_padding" value="<?php echo $menustyle_padding; ?>" size="10" maxlength="4" /> <b>[<?php echo $l['w_pixels']; ?>]</b>
</td>
</tr>

<!--  -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_gap', 'h_menuopt_gap'); ?></b></td>
<td class="fillOE" colspan="2"><input type="text" name="menustyle_gap" id="menustyle_gap" value="<?php echo $menustyle_gap; ?>" size="10" maxlength="4" /> <b>[<?php echo $l['w_pixels']; ?>]</b>
</td>
</tr>

<!-- border width -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_border', 'h_menuopt_border'); ?></b></td>
<td class="fillEE" colspan="2"><input type="text" name="menustyle_border_width" id="menustyle_border_width" value="<?php echo $menustyle_border_width; ?>" size="10" maxlength="4" /> <b>[<?php echo $l['w_pixels']; ?>]</b>
</td>
</tr>

<!--  -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_button_effect', 'h_menuopt_button_effect'); ?></b></td>
<td class="fillOE" colspan="2">
<?php
echo "<input type=\"radio\" name=\"menustyle_pushed\" value=\"1\"";
if($menustyle_pushed) {echo " checked=\"checked\"";}
echo " />".$l['w_yes']."&nbsp;";

echo "<input type=\"radio\" name=\"menustyle_pushed\" value=\"0\"";
if(!$menustyle_pushed) {echo " checked=\"checked\"";}
echo " />".$l['w_no']."&nbsp;";
?>
</td>
</tr>

<!-- label position from image position -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_label', 'h_menuopt_label'); ?></b></td>
<td class="fillEE" colspan="2">
<select name="menustyle_label_position" id="menustyle_label_position" size="0">
<?php
echo "<option value=\"\"";
if(!$menustyle_label_position) {echo " selected=\"selected\"";}
echo ">".$l['w_auto']."&nbsp;</option>\n";

echo "<option value=\"left\"";
if($menustyle_label_position == "left") {echo " selected=\"selected\"";}
echo ">".$l['w_left']."&nbsp;</option>\n";

echo "<option value=\"right\"";
if($menustyle_label_position == "right") {echo " selected=\"selected\"";}
echo ">".$l['w_right']."&nbsp;</option>\n";

echo "<option value=\"top\"";
if($menustyle_label_position == "top") {echo " selected=\"selected\"";}
echo ">".$l['w_top']."&nbsp;</option>\n";

echo "<option value=\"bottom\"";
if($menustyle_label_position == "bottom") {echo " selected=\"selected\"";}
echo ">".$l['w_bottom']."&nbsp;</option>\n";
?>
</select>
</td>
</tr>

<!-- center block (label + image) -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_center', 'h_menuopt_center'); ?></b></td>
<td class="fillOE" colspan="2">
<?php
echo "<input type=\"radio\" name=\"menustyle_center_block\" value=\"1\"";
if($menustyle_center_block) {echo " checked=\"checked\"";}
echo " />".$l['w_yes']."&nbsp;";

echo "<input type=\"radio\" name=\"menustyle_center_block\" value=\"0\"";
if(!$menustyle_center_block) {echo " checked=\"checked\"";}
echo " />".$l['w_no']."&nbsp;";
?>
<b>(<?php echo $l['w_label']." + ".$l['w_icon']; ?>)</b>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right">&nbsp;</td>
<td class="fillOE" colspan="2"><br /><b><?php echo F_display_field_name('w_colors', 'h_menustyle_colors'); ?></b></td>

<!-- Mouse Off Font Color -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_background', ''); ?></b></td>
<td class="fillEE"><input type="text" name="menustyle_background_col" id="menustyle_background_col" value="<?php echo $menustyle_background_col; ?>" size="10" maxlength="8" onchange="FJ_show_colors()" onfocus="FJ_show_colors()" onBlur="FJ_show_colors()" />
<?php F_generic_button("pickcolor1",$l['w_pick'],"colorWindow=window.open('cp_edit_html_colors.".CP_EXT."?callingform=form_menustyleeditor&amp;callingfield=menustyle_background_col','colorWindow','dependent,height=490,width=330,menubar=no,resizable=no,scrollbars=no,status=no,toolbar=no')"); ?>
</td>
<td class="fillEE"><div id="pickedcolor1" style="position:relative; visibility:visible">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_mouse_off_background', ''); ?></b></td>
<td class="fillOE"><input type="text" name="menustyle_colbck_off" id="menustyle_colbck_off" value="<?php echo $menustyle_colbck_off; ?>" size="10" maxlength="8" onchange="FJ_show_colors()" onfocus="FJ_show_colors()" onBlur="FJ_show_colors()" />
<?php F_generic_button("pickcolor2",$l['w_pick'],"colorWindow=window.open('cp_edit_html_colors.".CP_EXT."?callingform=form_menustyleeditor&amp;callingfield=menustyle_colbck_off','colorWindow','dependent,height=490,width=330,menubar=no,resizable=no,scrollbars=no,status=no,toolbar=no')"); ?>
</td>
<td class="fillOE"><div id="pickedcolor2" style="position:relative; visibility:visible">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_mouse_over_background', ''); ?></b></td>
<td class="fillEE"><input type="text" name="menustyle_colbck_over" id="menustyle_colbck_over" value="<?php echo $menustyle_colbck_over; ?>" size="10" maxlength="8" onchange="FJ_show_colors()" onfocus="FJ_show_colors()" onBlur="FJ_show_colors()" />
<?php F_generic_button("pickcolor3",$l['w_pick'],"colorWindow=window.open('cp_edit_html_colors.".CP_EXT."?callingform=form_menustyleeditor&amp;callingfield=menustyle_colbck_over','colorWindow','dependent,height=490,width=330,menubar=no,resizable=no,scrollbars=no,status=no,toolbar=no')"); ?>
</td>
<td class="fillEE"><div id="pickedcolor3" style="position:relative; visibility:visible">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_mouse_on_background', ''); ?></b></td>
<td class="fillOE"><input type="text" name="menustyle_colbck_on" id="menustyle_colbck_on" value="<?php echo $menustyle_colbck_on; ?>" size="10" maxlength="8" onchange="FJ_show_colors()" onfocus="FJ_show_colors()" onBlur="FJ_show_colors()" />
<?php F_generic_button("pickcolor4",$l['w_pick'],"colorWindow=window.open('cp_edit_html_colors.".CP_EXT."?callingform=form_menustyleeditor&amp;callingfield=menustyle_colbck_on','colorWindow','dependent,height=490,width=330,menubar=no,resizable=no,scrollbars=no,status=no,toolbar=no')"); ?>
</td>
<td class="fillOE"><div id="pickedcolor4" style="position:relative; visibility:visible">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_mouse_off_text', ''); ?></b></td>
<td class="fillEE"><input type="text" name="menustyle_coltxt_off" id="menustyle_coltxt_off" value="<?php echo $menustyle_coltxt_off; ?>" size="10" maxlength="8" onchange="FJ_show_colors()" onfocus="FJ_show_colors()" onBlur="FJ_show_colors()" />
<?php F_generic_button("pickcolor5",$l['w_pick'],"colorWindow=window.open('cp_edit_html_colors.".CP_EXT."?callingform=form_menustyleeditor&amp;callingfield=menustyle_coltxt_off','colorWindow','dependent,height=490,width=330,menubar=no,resizable=no,scrollbars=no,status=no,toolbar=no')"); ?>
</td>
<td class="fillEE"><div id="pickedcolor5" style="position:relative; visibility:visible">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_mouse_over_text', ''); ?></b></td>
<td class="fillOE"><input type="text" name="menustyle_coltxt_over" id="menustyle_coltxt_over" value="<?php echo $menustyle_coltxt_over; ?>" size="10" maxlength="8" onchange="FJ_show_colors()" onfocus="FJ_show_colors()" onBlur="FJ_show_colors()" />
<?php F_generic_button("pickcolor6",$l['w_pick'],"colorWindow=window.open('cp_edit_html_colors.".CP_EXT."?callingform=form_menustyleeditor&amp;callingfield=menustyle_coltxt_over','colorWindow','dependent,height=490,width=330,menubar=no,resizable=no,scrollbars=no,status=no,toolbar=no')"); ?>
</td>
<td class="fillOE"><div id="pickedcolor6" style="position:relative; visibility:visible">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_mouse_on_text', ''); ?></b></td>
<td class="fillEE"><input type="text" name="menustyle_coltxt_on" id="menustyle_coltxt_on" value="<?php echo $menustyle_coltxt_on; ?>" size="10" maxlength="8" onchange="FJ_show_colors()" onfocus="FJ_show_colors()" onBlur="FJ_show_colors()" />
<?php F_generic_button("pickcolor7",$l['w_pick'],"colorWindow=window.open('cp_edit_html_colors.".CP_EXT."?callingform=form_menustyleeditor&amp;callingfield=menustyle_coltxt_on','colorWindow','dependent,height=490,width=330,menubar=no,resizable=no,scrollbars=no,status=no,toolbar=no')"); ?>
</td>
<td class="fillEE"><div id="pickedcolor7" style="position:relative; visibility:visible">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_mouse_off_shadow', ''); ?></b></td>
<td class="fillOE"><input type="text" name="menustyle_colsdw_off" id="menustyle_colsdw_off" value="<?php echo $menustyle_colsdw_off; ?>" size="10" maxlength="8" onchange="FJ_show_colors()" onfocus="FJ_show_colors()" onBlur="FJ_show_colors()" />
<?php F_generic_button("pickcolor8",$l['w_pick'],"colorWindow=window.open('cp_edit_html_colors.".CP_EXT."?callingform=form_menustyleeditor&amp;callingfield=menustyle_colsdw_off','colorWindow','dependent,height=490,width=330,menubar=no,resizable=no,scrollbars=no,status=no,toolbar=no')"); ?>
</td>
<td class="fillOE"><div id="pickedcolor8" style="position:relative; visibility:visible">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_mouse_over_shadow', ''); ?></b></td>
<td class="fillEE"><input type="text" name="menustyle_colsdw_over" id="menustyle_colsdw_over" value="<?php echo $menustyle_colsdw_over; ?>" size="10" maxlength="8" onchange="FJ_show_colors()" onfocus="FJ_show_colors()" onBlur="FJ_show_colors()" />
<?php F_generic_button("pickcolor9",$l['w_pick'],"colorWindow=window.open('cp_edit_html_colors.".CP_EXT."?callingform=form_menustyleeditor&amp;callingfield=menustyle_colsdw_over','colorWindow','dependent,height=490,width=330,menubar=no,resizable=no,scrollbars=no,status=no,toolbar=no')"); ?>
</td>
<td class="fillEE"><div id="pickedcolor9" style="position:relative; visibility:visible">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_mouse_on_shadow', ''); ?></b></td>
<td class="fillOE"><input type="text" name="menustyle_colsdw_on" id="menustyle_colsdw_on" value="<?php echo $menustyle_colsdw_on; ?>" size="10" maxlength="8" onchange="FJ_show_colors()" onfocus="FJ_show_colors()" onBlur="FJ_show_colors()" />
<?php F_generic_button("pickcolor10",$l['w_pick'],"colorWindow=window.open('cp_edit_html_colors.".CP_EXT."?callingform=form_menustyleeditor&amp;callingfield=menustyle_colsdw_on','colorWindow','dependent,height=490,width=330,menubar=no,resizable=no,scrollbars=no,status=no,toolbar=no')"); ?>
</td>
<td class="fillOE"><div id="pickedcolor10" style="position:relative; visibility:visible">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right">&nbsp;</td>
<td class="fillEE">&nbsp;</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_shadow_x', 'h_menuopt_shadow_x'); ?></b></td>
<td class="fillOE" colspan="2"><input type="text" name="menustyle_shadow_x" id="menustyle_shadow_x" value="<?php echo $menustyle_shadow_x; ?>" size="10" maxlength="4" /> <b>[<?php echo $l['w_pixels']; ?>]</b>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_shadow_y', 'h_menuopt_shadow_y'); ?></b></td>
<td class="fillEE" colspan="2"><input type="text" name="menustyle_shadow_y" id="menustyle_shadow_y" value="<?php echo $menustyle_shadow_y; ?>" size="10" maxlength="4" /> <b>[<?php echo $l['w_pixels']; ?>]</b>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right">&nbsp;</td>
<td class="fillOE">&nbsp;</td>
</tr>

<!-- SELECT SOUNDS ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_sound', 'h_sound_select'); ?></b></td>
<td class="fillEE" colspan="2">
<select name="menustyle_sound_over" id="menustyle_sound_over" size="0">
<?php
$sql = "SELECT * FROM ".K_TABLE_SOUNDS_MENU." ORDER BY sound_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['sound_id']."\"";
		if($m['sound_id'] == $menustyle_sound_over) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['sound_name'], ENT_NOQUOTES, $l['a_meta_charset'])."&nbsp;</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select> <b><?php echo $l['w_over']; ?></b>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_sound', 'h_sound_select'); ?></b></td>
<td class="fillOE" colspan="2">
<select name="menustyle_sound_click" id="menustyle_sound_click" size="0">
<?php
$sql = "SELECT * FROM ".K_TABLE_SOUNDS_MENU." ORDER BY sound_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['sound_id']."\"";
		if($m['sound_id'] == $menustyle_sound_click) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['sound_name'], ENT_NOQUOTES, $l['a_meta_charset'])."&nbsp;</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select> <b><?php echo $l['w_click']; ?></b>
</td>
</tr>
<!-- END SELECT SOUNDS ==================== -->

<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE" colspan="2"><br /><b><?php echo F_display_field_name('w_menu', 'h_menustyle_menu'); ?></b></td>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_font_name', ''); ?></b></td>
<td class="fillOE" colspan="2"><input type="text" name="menustyle_main_font" id="menustyle_main_font" value="<?php echo htmlentities($menustyle_main_font, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_font_size', ''); ?></b></td>
<td class="fillEE" colspan="2"><input type="text" name="menustyle_main_font_size" id="menustyle_main_font_size" value="<?php echo $menustyle_main_font_size; ?>" size="10" maxlength="255" /> <b>[<?php echo $l['w_pixels']; ?>]</b></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_font_style', ''); ?></b></td>
<td class="fillOE" colspan="2">
<select name="menustyle_main_font_style" id="menustyle_main_font_style" size="1">
<?php
echo "<option value=\"PLAIN\""; if ($menustyle_main_font_style=="PLAIN") { echo " selected=\"selected\"";} echo">PLAIN&nbsp;</option>";
echo "<option value=\"BOLD\""; if ($menustyle_main_font_style=="BOLD") { echo " selected=\"selected\"";} echo">BOLD&nbsp;</option>";
echo "<option value=\"ITALIC\""; if ($menustyle_main_font_style=="ITALIC") { echo " selected=\"selected\"";} echo">ITALIC&nbsp;</option>";
echo "<option value=\"BOLD+ITALIC\""; if ($menustyle_main_font_style=="BOLD+ITALIC") { echo " selected=\"selected\"";} echo">BOLD+ITALIC&nbsp;</option>";
?>
</select>
</td>
</tr>

<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE" colspan="2"><br /><b><?php echo F_display_field_name('w_submenu', 'h_menustyle_submenu'); ?></b></td>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_font_name', ''); ?></b></td>
<td class="fillOE" colspan="2"><input type="text" name="menustyle_submenu_font" id="menustyle_submenu_font" value="<?php echo htmlentities($menustyle_submenu_font, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_font_size', ''); ?></b></td>
<td class="fillEE" colspan="2"><input type="text" name="menustyle_submenu_font_size" id="menustyle_submenu_font_size" value="<?php echo $menustyle_submenu_font_size; ?>" size="10" maxlength="255" /> <b>[<?php echo $l['w_pixels']; ?>]</b></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_font_style', ''); ?></b></td>
<td class="fillOE" colspan="2">
<select name="menustyle_submenu_font_style" id="menustyle_submenu_font_style" size="1">
<?php
echo "<option value=\"PLAIN\""; if ($menustyle_submenu_font_style=="PLAIN") { echo " selected=\"selected\"";} echo">PLAIN&nbsp;</option>";
echo "<option value=\"BOLD\""; if ($menustyle_submenu_font_style=="BOLD") { echo " selected=\"selected\"";} echo">BOLD&nbsp;</option>";
echo "<option value=\"ITALIC\""; if ($menustyle_submenu_font_style=="ITALIC") { echo " selected=\"selected\"";} echo">ITALIC&nbsp;</option>";
echo "<option value=\"BOLD+ITALIC\""; if ($menustyle_submenu_font_style=="BOLD+ITALIC") { echo " selected=\"selected\"";} echo">BOLD+ITALIC&nbsp;</option>";
?>
</select>
</td>
</tr>


<!-- BUTTON BACKGROUNDS ***************************  -->

<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE" colspan="2"><br /><b><?php echo F_display_field_name('w_background', 'h_menustyle_bck_img'); ?></b></td>
</tr>

<?php 
F_display_select_image("O", "menustyle_bck_img_off", $menustyle_bck_img_off, $l['w_off']); 
F_display_select_image("E", "menustyle_bck_img_over", $menustyle_bck_img_over, $l['w_over']); 
F_display_select_image("O", "menustyle_bck_img_on", $menustyle_bck_img_on, $l['w_on']); 
?>


<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE" colspan="2"><br /><b><?php echo F_display_field_name('w_icon', 'h_menustyle_icon'); ?></b></td>
</tr>

<?php 
F_display_select_image("O", "menustyle_icon_off", $menustyle_icon_off, $l['w_off']); 
F_display_select_image("E", "menustyle_icon_over", $menustyle_icon_over, $l['w_over']); 
F_display_select_image("O", "menustyle_icon_on", $menustyle_icon_on, $l['w_on']); 
?>


<!-- ARROWS ***************************  -->

<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE" colspan="2"><br /><b><?php echo F_display_field_name('w_arrow', 'h_menustyle_arrow')." - ".$l['w_left']; ?></b></td>
</tr>

<?php 
F_display_select_image("O", "menustyle_arrow_img_off_left", $menustyle_arrow_img_off_left, $l['w_off']); 
F_display_select_image("E", "menustyle_arrow_img_over_left", $menustyle_arrow_img_over_left, $l['w_over']); 
F_display_select_image("O", "menustyle_arrow_img_on_left", $menustyle_arrow_img_on_left, $l['w_on']); 
?>

<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE" colspan="2"><br /><b><?php echo F_display_field_name('w_arrow', 'h_menustyle_arrow')." - ".$l['w_right']; ?></b></td>
</tr>

<?php 
F_display_select_image("O", "menustyle_arrow_img_off_right", $menustyle_arrow_img_off_right, $l['w_off']); 
F_display_select_image("E", "menustyle_arrow_img_over_right", $menustyle_arrow_img_over_right, $l['w_over']); 
F_display_select_image("O", "menustyle_arrow_img_on_right", $menustyle_arrow_img_on_right, $l['w_on']); 
?>

<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE" colspan="2"><br /><b><?php echo F_display_field_name('w_arrow', 'h_menustyle_arrow')." - ".$l['w_top']; ?></b></td>
</tr>

<?php 
F_display_select_image("O", "menustyle_arrow_img_off_top", $menustyle_arrow_img_off_top, $l['w_off']); 
F_display_select_image("E", "menustyle_arrow_img_over_top", $menustyle_arrow_img_over_top, $l['w_over']); 
F_display_select_image("O", "menustyle_arrow_img_on_top", $menustyle_arrow_img_on_top, $l['w_on']); 
?>

<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE" colspan="2"><br /><b><?php echo F_display_field_name('w_arrow', 'h_menustyle_arrow')." - ".$l['w_bottom']; ?></b></td>
</tr>

<?php 
F_display_select_image("O", "menustyle_arrow_img_off_bottom", $menustyle_arrow_img_off_bottom, $l['w_off']); 
F_display_select_image("E", "menustyle_arrow_img_over_bottom", $menustyle_arrow_img_over_bottom, $l['w_over']); 
F_display_select_image("O", "menustyle_arrow_img_on_bottom", $menustyle_arrow_img_on_bottom, $l['w_on']); 
?>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if ($menustyle_id) {
	F_submit_button("form_menustyleeditor","menu_mode",$l['w_update']); 
	F_submit_button("form_menustyleeditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_menustyleeditor","menu_mode",$l['w_add']); 
F_submit_button("form_menustyleeditor","menu_mode",$l['w_clear']); 
?>
</td>
</tr>
</table>
</form>
<!-- ====================================================== -->

<!-- Show selected avatar image ==================== -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
function FJ_show_selected_img(i) {
	blank_image = "<?php echo K_PATH_IMAGES_ICONS_CLIENT."".K_BLANK_IMAGE; ?>";
	
	<?php 
	if ($javascript_display) {
		echo "switch (i) {".$javascript_display."}"; 
	}
	?>
}

// Display Colors
function FJ_show_colors() {
	if(document.layers){  //netscape 4
		document.layers['pickedcolor1'].bgColor = document.form_menustyleeditor.menustyle_background_col.value;  
		document.layers['pickedcolor2'].bgColor = document.form_menustyleeditor.menustyle_colbck_off.value; 
		document.layers['pickedcolor3'].bgColor = document.form_menustyleeditor.menustyle_colbck_over.value; 
		document.layers['pickedcolor4'].bgColor = document.form_menustyleeditor.menustyle_colbck_on.value; 
		document.layers['pickedcolor5'].bgColor = document.form_menustyleeditor.menustyle_coltxt_off.value;      
		document.layers['pickedcolor6'].bgColor = document.form_menustyleeditor.menustyle_coltxt_over.value;
		document.layers['pickedcolor7'].bgColor = document.form_menustyleeditor.menustyle_coltxt_on.value;
		document.layers['pickedcolor8'].bgColor = document.form_menustyleeditor.menustyle_colsdw_off.value;   
		document.layers['pickedcolor9'].bgColor = document.form_menustyleeditor.menustyle_colsdw_over.value; 
		document.layers['pickedcolor10'].bgColor = document.form_menustyleeditor.menustyle_colsdw_on.value;   
	}         
	if(document.all){ //IE 
		document.all.pickedcolor1.style.backgroundColor = document.form_menustyleeditor.menustyle_background_col.value;
		document.all.pickedcolor2.style.backgroundColor = document.form_menustyleeditor.menustyle_colbck_off.value;
		document.all.pickedcolor3.style.backgroundColor = document.form_menustyleeditor.menustyle_colbck_over.value;
		document.all.pickedcolor4.style.backgroundColor = document.form_menustyleeditor.menustyle_colbck_on.value;
		document.all.pickedcolor5.style.backgroundColor = document.form_menustyleeditor.menustyle_coltxt_off.value;
		document.all.pickedcolor6.style.backgroundColor = document.form_menustyleeditor.menustyle_coltxt_over.value;
		document.all.pickedcolor7.style.backgroundColor = document.form_menustyleeditor.menustyle_coltxt_on.value;
		document.all.pickedcolor8.style.backgroundColor = document.form_menustyleeditor.menustyle_colsdw_off.value;
		document.all.pickedcolor9.style.backgroundColor = document.form_menustyleeditor.menustyle_colsdw_over.value;
		document.all.pickedcolor10.style.backgroundColor = document.form_menustyleeditor.menustyle_colsdw_on.value;
	}        
	if(!document.all && document.getElementById){  //netscape 6   
		document.getElementById("pickedcolor1").style.backgroundColor = document.form_menustyleeditor.menustyle_background_col.value;

		document.getElementById("pickedcolor2").style.backgroundColor = document.form_menustyleeditor.menustyle_colbck_off.value;
		document.getElementById("pickedcolor3").style.backgroundColor = document.form_menustyleeditor.menustyle_colbck_over.value;
		document.getElementById("pickedcolor4").style.backgroundColor = document.form_menustyleeditor.menustyle_colbck_on.value;
		document.getElementById("pickedcolor5").style.backgroundColor = document.form_menustyleeditor.menustyle_coltxt_off.value;
		document.getElementById("pickedcolor6").style.backgroundColor = document.form_menustyleeditor.menustyle_coltxt_over.value;
		document.getElementById("pickedcolor7").style.backgroundColor = document.form_menustyleeditor.menustyle_coltxt_on.value; 
		document.getElementById("pickedcolor8").style.backgroundColor = document.form_menustyleeditor.menustyle_colsdw_off.value;
		document.getElementById("pickedcolor9").style.backgroundColor = document.form_menustyleeditor.menustyle_colsdw_over.value;
		document.getElementById("pickedcolor10").style.backgroundColor = document.form_menustyleeditor.menustyle_colsdw_on.value;   
	}
	return;
}

FJ_show_colors();

for (i=1; i<=<?php echo $id; ?>; i++) {
	FJ_show_selected_img(i);
}

//]]>
</script>
<!-- END Cange focus to menustyle_id select -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT); 

// ------------------------------------------------------------
// display image selection row
// ------------------------------------------------------------
function F_display_select_image($row, $field_id, $field_value, $name) {
	global $l, $db, $javascript_display, $id;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	$id++;
	
	echo "<tr class=\"fill".$row."\">";
	echo "<td class=\"fill".$row."O\" align=\"right\">";
	
	echo "<a href=\"javascript:void(0);\" onclick=\"selectWindow=window.open('cp_select_icons_client.".CP_EXT."?formname=form_menustyleeditor&amp;idfield=".$field_id."&amp;fieldtype=0&amp;fsubmit=0','selectWindow','dependent,height=450,width=450,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')\"><b>".$name."</b></a></td>";

	echo "<td class=\"fill".$row."E\">";
	echo "<select name=\"".$field_id."\" id=\"".$field_id."\" size=\"0\" onfocus=\"FJ_show_selected_img(".$id.")\" onchange=\"FJ_show_selected_img(".$id.")\">";

	//echo "<option value=\"\">&nbsp;&nbsp;</option>";
	$sql = "SELECT * FROM ".K_TABLE_ICONS_CLIENT." ORDER BY icon_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"".$m['icon_link']."\"";
			if($m['icon_link'] == $field_value) {
				echo " selected=\"selected\"";
			}
			echo ">".htmlentities($m['icon_name'], ENT_NOQUOTES, $l['a_meta_charset'])."&nbsp;</option>\n";
		}
	}
	else {
		F_display_db_error();
	}
	echo "</select>";
	echo "</td>";
	echo "<td class=\"fill".$row."O\"><img name=\"img".$id."\" src=\"".K_PATH_IMAGES_ICONS_CLIENT.$field_value."\" border=\"0\" alt=\"\" /></td>";
	echo "</tr>";
	
	$javascript_display .= "case ".$id.": {\n";
	$javascript_display .= "temp_img = document.form_menustyleeditor.".$field_id.".options[document.form_menustyleeditor.".$field_id.".selectedIndex].value;\n";
	$javascript_display .= "if (temp_img.length > 0) {\n";
	$javascript_display .= "document.images.img".$id.".src = \"".K_PATH_IMAGES_ICONS_CLIENT."\"+temp_img;\n";
	$javascript_display .= "}\n";
	$javascript_display .= "else {\n";
	$javascript_display .= "document.images.img".$id.".src = blank_image;\n";
	$javascript_display .= "}\n";
	$javascript_display .= "break;\n";
	$javascript_display .= "}\n";
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
