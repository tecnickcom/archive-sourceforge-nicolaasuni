<?php
//============================================================+
// File name   : cp_layout_main.php
// Begin       : 2001-09-02
// Last Update : 2007-01-11
// 
// Description : Main page (frame name: CPMAIN)
//               Display Menu icons
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
require_once('../../shared/code/cp_functions_target.'.CP_EXT);

$pagelevel = K_AUTH_ADMIN_CP_LAYOUT_MAIN;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

if (isset($previous_language) AND ($previous_language != $selected_language)) {
	$menu_sub_id = 0; //return to menu tree root if language has been changed
}

if (isset($menu_sub_id) AND ($menu_sub_id > 0) AND isset($node)) { //display node name as title
	$thispage_title = $node;
}
else {
	$thispage_title = $l['t_control_panel'];
}

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages;

if (!K_DISPLAY_QUICK_HELP) { ?>
<!-- overLIB ==================== -->
<div id="overDiv" style="z-index: 1000; visibility: hidden; position: absolute"></div>
<script language="JavaScript" src="<?php echo K_PATH_SHARED_JSCRIPTS; ?>overlib_aiocp.js" type="text/javascript"></script>
<!-- END overLIB ==================== -->
<?php } ?>

<!-- MENU (icons with overlib description) -->
<table border="0" cellspacing="0" cellpadding="0" align="center"><tr>
<?php
if (!isset($menu_sub_id)) {
	$menu_sub_id = 0; //current menu level
}

$countcols = 0;
$sql = "SELECT * FROM ".K_TABLE_MENU." WHERE (menu_language='".$selected_language."' AND menu_sub_id=".$menu_sub_id.") ORDER BY menu_position";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<td width=\"60\" height=\"80\" align=\"center\" valign=\"top\">\n";
		echo "<a class=\"overlib\" onmouseover=\"FJ_show_hide_form_elements(true); return overlib('<b>".addslashes(F_compact_string($m['menu_description']))."</b>', TEXTSIZE, 2, TEXTCOLOR, '#444444', WIDTH, 205, HEIGHT, 66, BACKGROUND, '".K_OVERLIB_IMAGE."', PADX, 15, 27, PADY, 5, 12)\" onmouseout=\"FJ_show_hide_form_elements(false); nd();\" onfocus=\"FJ_show_hide_form_elements(true); return overlib('<b>".addslashes(F_compact_string($m['menu_description']))."</b>', TEXTSIZE, 2, TEXTCOLOR, '#444444', WIDTH, 205, HEIGHT, 66, BACKGROUND, '".K_OVERLIB_IMAGE."', PADX, 15, 27, PADY, 5, 12)\" onblur=\"FJ_show_hide_form_elements(false); nd();\" ";
		if ($m['menu_link']) { //is a node with page link
			echo "href=\"".htmlentities(urldecode($m['menu_link']))."\" target=\"".F_get_target_name($m['menu_target'])."\">";
		}
		else { //is only a link for subtree
			echo "href=\"cp_layout_main.".CP_EXT."?previous_language=".$selected_language."&amp;menu_sub_id=".$m['menu_id']."&amp;node=".urlencode($m['menu_name'])."\" target=\"CPMAIN\">";
		}

//MENU ICON
		$sql3 = "SELECT * FROM ".K_TABLE_ICONS." WHERE icon_id=".$m['menu_iconid']."";
		if($r3 = F_aiocpdb_query($sql3, $db)) {
			if($m3 = F_aiocpdb_fetch_array($r3)) {
				echo "<img src=\"";
				if(F_is_relative_link($m3['icon_link'])) {echo K_PATH_IMAGES_ICONS;}
				echo "".$m3['icon_link']."\" width=\"".$m3['icon_width']."\" height=\"".$m3['icon_height']."\" border=\"0\" alt=\"\" />";
			}
			else {
				F_display_db_error();
			}
		}
		else {
			F_display_db_error();
		}

		echo "<br />";
		echo "<font size=\"1\">".htmlentities($m['menu_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</font></A></td>";
		$countcols++;
		if($countcols == K_MAX_ICONS_IN_ROW) {
			echo "</tr><tr>";
			$countcols = 0;
		}
	}
}
else {
	F_display_db_error();
}
?>
</tr></table>
<!-- END MENU -->

<!-- ====================================================== -->
<?php
require_once('../code/cp_page_footer.'.CP_EXT); 

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
