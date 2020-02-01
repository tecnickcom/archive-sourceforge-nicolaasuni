<?php
//============================================================+
// File name   : cp_edit_menu_client_options.php               
// Begin       : 2002-03-21                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit Client Menu Options                      
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_MENU_CLIENT_OPTIONS;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_menu_client_options'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages; ?>
<!-- ====================================================== -->
<?php
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
		$sql = "SELECT COUNT(*) FROM ".K_TABLE_MENU_LIST." WHERE menulst_option='".$menuopt_id."'";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$optionused = $m['0'];
			}
		}
		else {
			F_display_db_error();
		}
		if (isset($optionused) AND $optionused) {
			F_print_error("WARNING", $l['m_not_delete_used_option']);
		}
		else {
			$sql = "DELETE FROM ".K_TABLE_MENU_OPTIONS." WHERE menuopt_id=".$menuopt_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			$menuopt_id=FALSE;
		}
		break;
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_MENU_OPTIONS,"menuopt_name='".$menuopt_name."'","menuopt_id",$menuopt_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				$sql = "UPDATE IGNORE ".K_TABLE_MENU_OPTIONS." SET 
						menuopt_name='".$menuopt_name."', 
						menuopt_horizontal='".$menuopt_horizontal."', 
						menuopt_autoscroll='".$menuopt_autoscroll."', 
						menuopt_text='".$menuopt_text."', 
						menuopt_width='".$menuopt_width."', 
						menuopt_height='".$menuopt_height."', 
						menuopt_hspace='".$menuopt_hspace."', 
						menuopt_vspace='".$menuopt_vspace."', 
						menuopt_align='".$menuopt_align."', 
						menuopt_arrow_position='".$menuopt_arrow_position."', 
						menuopt_popup_position='".$menuopt_popup_position."', 
						menuopt_target='".$menuopt_target."'
						WHERE menuopt_id=".$menuopt_id."";
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
			//check if name is unique
			$sql = "SELECT menuopt_name FROM ".K_TABLE_MENU_OPTIONS." WHERE menuopt_name='".$menuopt_name."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				$sql = "INSERT IGNORE INTO ".K_TABLE_MENU_OPTIONS." (
						menuopt_name,
						menuopt_horizontal,
						menuopt_autoscroll,
						menuopt_text,
						menuopt_width,
						menuopt_height,
						menuopt_hspace,
						menuopt_vspace,
						menuopt_align,
						menuopt_arrow_position,
						menuopt_popup_position,
						menuopt_target
						) VALUES (
						'".$menuopt_name."', 
						'".$menuopt_horizontal."', 
						'".$menuopt_autoscroll."', 
						'".$menuopt_text."', 
						'".$menuopt_width."', 
						'".$menuopt_height."', 
						'".$menuopt_hspace."', 
						'".$menuopt_vspace."', 
						'".$menuopt_align."', 
						'".$menuopt_arrow_position."', 
						'".$menuopt_popup_position."', 
						'".$menuopt_target."'
						)";
						
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$menuopt_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
	}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$menuopt_name = "";
		$menuopt_horizontal = 0;
		$menuopt_autoscroll = 0;
		$menuopt_text = 1;
		$menuopt_width = 150;
		$menuopt_height = 300;
		$menuopt_hspace = 0;
		$menuopt_vspace = 0;
		$menuopt_align = "";
		$menuopt_arrow_position = "";
		$menuopt_popup_position = "";
		$menuopt_target = "";
		break;
	}

	default :{ 
		break;
	}

} //end of switch

// Initialize variables
if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!isset($menuopt_id) OR (!$menuopt_id)) {
			$sql = "SELECT * FROM ".K_TABLE_MENU_OPTIONS." ORDER BY menuopt_name LIMIT 1";
		} else {
			$sql = "SELECT * FROM ".K_TABLE_MENU_OPTIONS." WHERE menuopt_id=".$menuopt_id." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$menuopt_id = $m['menuopt_id'];
				$menuopt_name = $m['menuopt_name'];
				$menuopt_horizontal = $m['menuopt_horizontal'];
				$menuopt_autoscroll = $m['menuopt_autoscroll'];
				$menuopt_text = $m['menuopt_text'];
				$menuopt_width = $m['menuopt_width'];
				$menuopt_height = $m['menuopt_height'];
				$menuopt_hspace = $m['menuopt_hspace'];
				$menuopt_vspace = $m['menuopt_vspace'];
				$menuopt_align = $m['menuopt_align'];
				$menuopt_arrow_position = $m['menuopt_arrow_position'];
				$menuopt_popup_position = $m['menuopt_popup_position'];
				$menuopt_target = $m['menuopt_target'];
			}
			else {
				$menuopt_name = "";
				$menuopt_horizontal = 0;
				$menuopt_autoscroll = 0;
				$menuopt_text = 1;
				$menuopt_width = 150;
				$menuopt_height = 300;
				$menuopt_hspace = 0;
				$menuopt_vspace = 0;
				$menuopt_align = "";
				$menuopt_arrow_position = "";
				$menuopt_popup_position = "";
				$menuopt_target = "";
			}
		}
		else {
			F_display_db_error();
		}
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_menuopteditor" id="form_menuopteditor">

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT option ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_option', 'h_menuopt_select'); ?></b></td>
<td class="fillOE" colspan="2">
<select name="menuopt_id" id="menuopt_id" size="0" onchange="document.form_menuopteditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_MENU_OPTIONS." ORDER BY menuopt_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['menuopt_id']."\"";
		if($m['menuopt_id'] == $menuopt_id) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['menuopt_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT option ==================== -->

<tr class="fillE">
<td class="fillEO">&nbsp;</td>
<td class="fillEE" colspan="2"><hr /></td>
</tr>

<tr class="fillO">
<td class="fillOE" align="right"><b><?php echo F_display_field_name('w_name', 'h_menuopt_name'); ?></b></td>
<td class="fillOE" colspan="2"><input type="text" name="menuopt_name" id="menuopt_name" value="<?php echo htmlentities($menuopt_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="20" maxlength="255" /></td>
</tr>

<!-- Horizontal Menu - Tells the menu to become horizontal instead of top to bottom style (1=on/0=off) -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_horizontal', 'h_menuopt_horizontal'); ?></b></td>
<td class="fillEE">
<?php
echo "<input type=\"radio\" name=\"menuopt_horizontal\" value=\"1\"";
if($menuopt_horizontal) {echo " checked=\"checked\"";}
echo " />".$l['w_yes']."&nbsp;";

echo "<input type=\"radio\" name=\"menuopt_horizontal\" value=\"0\"";
if(!$menuopt_horizontal) {echo " checked=\"checked\"";}
echo " />".$l['w_no']."&nbsp;";
?>
</td>
</tr>

<!-- Autoscroll feature (javascript) -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_autoscroll', 'h_menuopt_autoscroll'); ?></b></td>
<td class="fillOE">
<?php
echo "<input type=\"radio\" name=\"menuopt_autoscroll\" value=\"1\"";
if($menuopt_autoscroll) {echo " checked=\"checked\"";}
echo " />".$l['w_yes']."&nbsp;";

echo "<input type=\"radio\" name=\"menuopt_autoscroll\" value=\"0\"";
if(!$menuopt_autoscroll) {echo " checked=\"checked\"";}
echo " />".$l['w_no']."&nbsp;";
?>
</td>
</tr>

<!-- if TRUE DISPLAY LINK TO ALTERNATIVE TEXT MENU VERSION -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_text', 'h_menuopt_text'); ?></b></td>
<td class="fillEE">
<?php
echo "<input type=\"radio\" name=\"menuopt_text\" value=\"1\"";
if($menuopt_text) {echo " checked=\"checked\"";}
echo " />".$l['w_yes']."&nbsp;";

echo "<input type=\"radio\" name=\"menuopt_text\" value=\"0\"";
if(!$menuopt_text) {echo " checked=\"checked\"";}
echo " />".$l['w_no']."&nbsp;";
?>
</td>
</tr>

<!-- Applet's width -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_width', 'h_menuopt_width'); ?></b></td>
<td class="fillOE"><input type="text" name="menuopt_width" id="menuopt_width" value="<?php echo $menuopt_width; ?>" size="10" maxlength="4" /> <b>[<?php echo $l['w_pixels']; ?>]</b>
</td>
</tr>

<!-- Applet's width Height -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_height', 'h_menuopt_height'); ?></b></td>
<td class="fillEE"><input type="text" name="menuopt_height" id="menuopt_height" value="<?php echo $menuopt_height; ?>" size="10" maxlength="4" /> <b>[<?php echo $l['w_pixels']; ?>]</b>
</td>
</tr>

<!--  -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_hspace', 'h_menuopt_hspace'); ?></b></td>
<td class="fillOE"><input type="text" name="menuopt_hspace" id="menuopt_hspace" value="<?php echo $menuopt_hspace; ?>" size="10" maxlength="4" /> <b>[<?php echo $l['w_pixels']; ?>]</b>
</td>
</tr>

<!--  -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_vspace', 'h_menuopt_vspace'); ?></b></td>
<td class="fillEE"><input type="text" name="menuopt_vspace" id="menuopt_vspace" value="<?php echo $menuopt_vspace; ?>" size="10" maxlength="4" /> <b>[<?php echo $l['w_pixels']; ?>]</b>
</td>
</tr>

<!--  -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_align', 'h_menuopt_align'); ?></b></td>
<td class="fillOE">
<select name="menuopt_align" id="menuopt_align" size="0">
<?php
echo "<option value=\"\"";
if(!$menuopt_align) {echo " selected=\"selected\"";}
echo ">&nbsp;</option>\n";

echo "<option value=\"top\"";
if($menuopt_align == "top") {echo " selected=\"selected\"";}
echo ">".$l['w_top']."</option>\n";

echo "<option value=\"middle\"";
if($menuopt_align == "middle") {echo " selected=\"selected\"";}
echo ">".$l['w_middle']."</option>\n";

echo "<option value=\"bottom\"";
if($menuopt_align == "bottom") {echo " selected=\"selected\"";}
echo ">".$l['w_bottom']."</option>\n";

echo "<option value=\"left\"";
if($menuopt_align == "left") {echo " selected=\"selected\"";}
echo ">".$l['w_left']."</option>\n";

echo "<option value=\"right\"";
if($menuopt_align == "right") {echo " selected=\"selected\"";}
echo ">".$l['w_right']."</option>\n";
?>
</select>
</td>
</tr>

<!-- submenu arrow -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_arrow', 'h_menuopt_arrow'); ?></b></td>
<td class="fillEE">
<select name="menuopt_arrow_position" id="menuopt_arrow_position" size="0">
<?php
echo "<option value=\"\"";
if(!$menuopt_arrow_position) {echo " selected=\"selected\"";}
echo ">".$l['w_auto']."</option>\n";

echo "<option value=\"LEFT\"";
if($menuopt_arrow_position == "LEFT") {echo " selected=\"selected\"";}
echo ">".$l['w_left']."</option>\n";

echo "<option value=\"RIGHT\"";
if($menuopt_arrow_position == "RIGHT") {echo " selected=\"selected\"";}
echo ">".$l['w_right']."</option>\n";
?>
</select>
</td>
</tr>

<!-- popup position -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_submenu', 'h_menuopt_submenu'); ?></b></td>
<td class="fillOE">
<select name="menuopt_popup_position" id="menuopt_popup_position" size="0">
<?php
echo "<option value=\"\"";
if(!$menuopt_popup_position) {echo " selected=\"selected\"";}
echo ">".$l['w_auto']."</option>\n";

echo "<option value=\"LEFT\"";
if($menuopt_popup_position == "LEFT") {echo " selected=\"selected\"";}
echo ">".$l['w_left']."</option>\n";

echo "<option value=\"RIGHT\"";
if($menuopt_popup_position == "RIGHT") {echo " selected=\"selected\"";}
echo ">".$l['w_right']."</option>\n";

echo "<option value=\"TOP\"";
if($menuopt_popup_position == "TOP") {echo " selected=\"selected\"";}
echo ">".$l['w_top']."</option>\n";

echo "<option value=\"BOTTOM\"";
if($menuopt_popup_position == "BOTTOM") {echo " selected=\"selected\"";}
echo ">".$l['w_bottom']."</option>\n";
?>
</select>
</td>
</tr>

<!-- MENU TARGET -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_target', 'h_target_name'); ?></b></td>
<td class="fillEE">
<select name="menuopt_target" id="menuopt_target" size="0">
<?php
$sql = "SELECT * FROM ".K_TABLE_FRAME_TARGETS." ORDER BY target_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['target_id']."\"";
		if($m['target_id'] == $menuopt_target) {
			echo " selected=\"selected\"";
		}
		echo ">".$m['target_name']."&nbsp;</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if ($menuopt_id) {
	F_submit_button("form_menuopteditor","menu_mode",$l['w_update']); 
	F_submit_button("form_menuopteditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_menuopteditor","menu_mode",$l['w_add']); 
F_submit_button("form_menuopteditor","menu_mode",$l['w_clear']); 
?>
</td>

</tr>
</table>
</form>

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
