<?php
//============================================================+
// File name   : cp_edit_menu.php                              
// Begin       : 2001-09-05                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit Menu entries on menu tables              
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_MENU;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

require_once('../code/cp_functions_tree.'.CP_EXT);
require_once('../../shared/code/cp_functions_icons.'.CP_EXT);
require_once('../../shared/code/cp_functions_form.'.CP_EXT);

$thispage_title = $l['t_menu_editor'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages; ?>
<!-- ====================================================== -->
<?php
//initialize variables
$tree_suffix = "menu";

if (!isset($menu_table)) {
	$menu_table = K_TABLE_MENU_CLIENT;
}

if ($menu_table == K_TABLE_MENU) { //options for AIOCP administrator menu
	$icontable = K_TABLE_ICONS;
	$iconpath = K_PATH_IMAGES_ICONS;
	$selecticon = "cp_select_icons.".CP_EXT;
	
}
else { //options for client menus
	$icontable = K_TABLE_ICONS_CLIENT;
	$iconpath = K_PATH_IMAGES_ICONS_CLIENT;
	$selecticon = "cp_select_icons_client.".CP_EXT;
}

if(!isset($menu_language)) {
	$menu_language = $selected_language;
}

//load icon images
if(isset($icon_link)) {
	$menu_iconid = F_get_icon_id($icontable, $icon_link);
}
if(isset($icon_off_link)) {
	$menu_icon_off = F_get_icon_id($icontable, $icon_off_link);
}
if(isset($icon_over_link)) {
	$menu_icon_over = F_get_icon_id($icontable, $icon_over_link);
}
if(isset($icon_on_link)) {
	$menu_icon_on = F_get_icon_id($icontable, $icon_on_link);
}
	
//check if menu is void for the selected language
$numofrows = 0;
$sql = "SELECT COUNT(*) FROM ".$menu_table." WHERE menu_language='".$menu_language."' LIMIT 1";
if($r = F_aiocpdb_query($sql, $db)) {
	if($m = F_aiocpdb_fetch_array($r)) {
		$numofrows = $m['0'];
	}
}
else {
	F_display_db_error();
}

if(!$numofrows) { //if the tree is void (no items) for selected language, create new item (first element)
	$menu_item = 1;
	$menu_sub_id = 0;
	$menu_position = 1;
	$menu_name = "new item";
	$menu_description = "";
	$menu_link="";
	$menu_target=1;
	$menu_iconid=1; //general icon to be display on pages
	$menu_icon_off=1; //off-state button icon
	$menu_icon_over=1; //over-state button icon
	$menu_icon_on=1; //on-state button icon
	$menu_enabled=1;
	$menu_style_id="";
	$sql = "INSERT IGNORE INTO ".$menu_table." (
	menu_language, 
	menu_item, 
	menu_sub_id, 
	menu_position, 
	menu_name, 
	menu_description, 
	menu_link,
	menu_target, 
	menu_iconid, 
	menu_icon_off, 
	menu_icon_over, 
	menu_icon_on, 
	menu_enabled,
	menu_style_id
	) VALUES (
	'".$menu_language."', 
	'".$menu_item."', 
	'".$menu_sub_id."', 
	'".$menu_position."', 
	'".$menu_name."', 
	'".$menu_description."', 
	'".$menu_link."', 
	'".$menu_target."', 
	'".$menu_iconid."', 
	'".$menu_icon_off."', 
	'".$menu_icon_over."', 
	'".$menu_icon_on."', 
	'".$menu_enabled."',
	'".$menu_style_id."'
	)";
	if(!$r = F_aiocpdb_query($sql, $db)) {
		F_display_db_error();
	}
	//read new menu_id
	$sql = "SELECT * FROM ".$menu_table." WHERE menu_language='".$menu_language."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$menu_id = $m['menu_id'];
		}
	}
	else {
		F_display_db_error();
	}
	$changelanguage = 1;
}
if (isset($_REQUEST['menu_mode'])) {
	$menu_mode = $_REQUEST['menu_mode'];
} else {
	$menu_mode = "";
}
//get item data
switch($menu_mode) {
	
	case 'delete':
	case 'forcedelete':
	case 'addafter':
	case 'addbefore':
	case 'moveup':
	case 'movedown':
	case 'moveback':
	case 'moveforward': {
		$sql = "SELECT * FROM ".$menu_table." WHERE menu_id=".$menu_id." LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$menu_id = $m['menu_id'];
				$menu_item = $m['menu_item'];
				$menu_language = $m['menu_language'];
				$menu_sub_id = $m['menu_sub_id'];
				$menu_position = $m['menu_position'];
				$menu_name = $m['menu_name'];
				$menu_description = $m['menu_description'];
				$menu_link = $m['menu_link'];
				$menu_target = $m['menu_target'];
				$menu_iconid = $m['menu_iconid'];
				$menu_icon_off = $m['menu_icon_off'];
				$menu_icon_over = $m['menu_icon_over'];
				$menu_icon_on = $m['menu_icon_on'];
				$menu_enabled = $m['menu_enabled'];
				$menu_style_id = $m['menu_style_id'];
			}
			else {
				F_display_db_error();
			}
		}
	break;
	}
	
	default : {
		break;
	}
}

switch($menu_mode) {

	case 'delete': // ask confirmation
	case unhtmlentities($l['w_delete']):
	case $l['w_delete']:{
		F_stripslashes_formfields();
		F_print_error("WARNING", $l['m_delete_confirm']);
		?>
		<p><?php echo "<b>".$menu_name."</b>: ".$l['m_delete_confirm'].""; ?></p>
		
		<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_delete" id="form_delete">
		<input type="hidden" name="menu_mode" id="menu_mode" value="forcedelete" />
		<input type="hidden" name="menu_id" id="menu_id" value="<?php echo $menu_id; ?>" />
		<input type="hidden" name="menu_language" id="menu_language" value="<?php echo $menu_language; ?>" />
		<input type="hidden" name="menu_table" id="menu_table" value="<?php echo $menu_table; ?>" />
		<input type="hidden" name="forcedelete" id="forcedelete" value="" />
		<?php 
		F_submit_button("form_delete","forcedelete",$l['w_delete']);
		F_submit_button("form_delete","forcedelete",$l['w_cancel']);
		?>
		</form>
		
		<?php
		break;
	}

	case 'forcedelete':{
		F_stripslashes_formfields(); // Delete menu item
		if($forcedelete == $l['w_delete']) { //check if delete button has been pushed
			F_delete_item($menu_id, $menu_item, $menu_sub_id, $menu_position, $menu_language, $menu_table, $tree_suffix);
		}
		$menu_id=FALSE;
		break;
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update menu item
		$sql = "UPDATE IGNORE ".$menu_table." SET 
		menu_language='".$menu_language."', 
		menu_item='".$menu_item."', 
		menu_sub_id='".$menu_sub_id."', 
		menu_position='".$menu_position."', 
		menu_name='".$menu_name."', 
		menu_description='".$menu_description."', 
		menu_link='".$menu_link."', 
		menu_target='".$menu_target."', 
		menu_iconid='".$menu_iconid."', 
		menu_icon_off='".$menu_icon_off."', 
		menu_icon_over='".$menu_icon_over."', 
		menu_icon_on='".$menu_icon_on."', 
		menu_enabled='".$menu_enabled."',
		menu_style_id='".$menu_style_id."' 
		WHERE menu_id='".$menu_id."'";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		break;
	}

		// Set variables to insert item after/before the selected element at the same level
	case 'addafter':{ 
		$menu_position += 1;
	}

	case 'addbefore':{
		$menu_item = 1;
		$menu_name = "new item";
		$menu_description = "";
		$menu_link = "";
		$menu_target = 1;
		$menu_iconid = 1;
		$menu_icon_off = 1;
		$menu_icon_over = 1;
		$menu_icon_on = 1;
		$menu_enabled = 0;
		$menu_style_id="";
		
		F_add_tree_position($menu_sub_id, $menu_position, $menu_language, $menu_table, $tree_suffix);
		
		$sql = "INSERT IGNORE INTO ".$menu_table." (
		menu_language, 
		menu_item, 
		menu_sub_id, 
		menu_position, 
		menu_name, 
		menu_description, 
		menu_link, 
		menu_target, 
		menu_iconid, 
		menu_icon_off, 
		menu_icon_over, 
		menu_icon_on, 
		menu_enabled,
		menu_style_id
		) VALUES (
		'".$menu_language."', 
		'".$menu_item."', 
		'".$menu_sub_id."', 
		'".$menu_position."', 
		'".$menu_name."', 
		'".$menu_description."', 
		'".$menu_link."', 
		'".$menu_target."', 
		'".$menu_iconid."', 
		'".$menu_icon_off."', 
		'".$menu_icon_over."', 
		'".$menu_icon_on."', 
		'".$menu_enabled."',
		'".$menu_style_id."' 
		)";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		else {
			$menu_id = F_aiocpdb_insert_id();
		}
		break;
	}

	case 'moveup':{ // Move item 1 position up
		F_move_up_tree_item($menu_id, $menu_sub_id, $menu_position, $menu_language, $menu_table, $tree_suffix);
		break;
	}

	case 'movedown':{ // Move item 1 position down
		F_move_down_tree_item($menu_id, $menu_sub_id, $menu_position, $menu_language, $menu_table, $tree_suffix);
		break;
	}

	case 'moveback':{ // Move item and subtree 1 level up
		F_move_back_tree_item($menu_id, $menu_sub_id, $menu_position, $menu_language, $menu_table, $tree_suffix);
		break;
	}

	case 'moveforward':{ // Move item and subtree 1 level up
		F_move_forward_tree_item($menu_id, $menu_sub_id, $menu_position, $menu_language, $menu_table, $tree_suffix);
		break;
	}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$menu_name = "";
		$menu_description = "";
		$menu_link = "";
		$menu_target = 1;
		$menu_iconid = 1;
		$menu_icon_off = 1;
		$menu_icon_over = 1;
		$menu_icon_on = 1;
		$menu_enabled = 0;
		$menu_style_id = "";
		break;
		}

	default : {
		break;
	}
}

// -- Initialize variables
if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if((isset($changetable) AND $changetable) OR (isset($changelanguage) AND $changelanguage) OR (!isset($menu_id) OR (!$menu_id))) {
			$sql = "SELECT * FROM ".$menu_table." WHERE menu_language='".$menu_language."' LIMIT 1";
		}
		else {$sql = "SELECT * FROM ".$menu_table." WHERE menu_id=".$menu_id." LIMIT 1";}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$menu_id = $m['menu_id'];
				$menu_item = $m['menu_item'];
				$menu_language = $m['menu_language'];
				$menu_sub_id = $m['menu_sub_id'];
				$menu_position = $m['menu_position'];
				$menu_name = $m['menu_name'];
				$menu_description = $m['menu_description'];
				$menu_link = $m['menu_link'];
				$menu_target = $m['menu_target'];
				$menu_iconid = $m['menu_iconid'];
				$menu_icon_off = $m['menu_icon_off'];
				$menu_icon_over = $m['menu_icon_over'];
				$menu_icon_on = $m['menu_icon_on'];
				$menu_enabled = $m['menu_enabled'];
				$menu_style_id = $m['menu_style_id'];
			}
			else {
				F_display_db_error();
			}
		}
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_menueditor" id="form_menueditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="menu_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<th class="edge"><?php echo $l['d_editor_item']; ?></th>
<th class="edge"><?php echo $l['d_editor_tree']; ?></th>
</tr>

<tr class="edge" valign="top">
<td class="edge" valign="top">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT MENU TABLE  ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_menu', 'h_menued_select'); ?></b></td>
<td class="fillOE">
<input type="hidden" name="changetable" id="changetable" value="0" />
<select name="menu_table" id="menu_table" size="0" onchange="document.form_menueditor.changetable.value=1; document.form_menueditor.submit()">
<?php
//display AIOCP administrator menu table
echo "<option value=\"".K_TABLE_MENU."\"";
if($menu_table == K_TABLE_MENU) {echo " selected=\"selected\"";}
echo ">".$l['w_aiocp']."&nbsp;</option>\n";

//display client menu tables
$sql = "SELECT * FROM ".K_TABLE_MENU_LIST." ORDER BY menulst_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		if ($m['menulst_id'] == 1) { //display default menu client
			echo "<option value=\"".K_TABLE_MENU_CLIENT."\"";
			if($menu_table == K_TABLE_MENU_CLIENT) {	echo " selected=\"selected\"";}
			echo ">".htmlentities($m['menulst_name'], ENT_NOQUOTES, $l['a_meta_charset'])."&nbsp;</option>\n";
		}
		else {
			echo "<option value=\"".K_TABLE_MENU_CLIENT."_".$m['menulst_id']."\"";
			if($menu_table == K_TABLE_MENU_CLIENT."_".$m['menulst_id']) {echo " selected=\"selected\"";}
			echo ">".htmlentities($m['menulst_name'], ENT_NOQUOTES, $l['a_meta_charset'])."&nbsp;</option>\n";
		}
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT MENU TABLE  ==================== -->

<!-- SELECT language ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_language', 'h_menued_language'); ?></b></td>
<td class="fillEE">
<input type="hidden" name="changelanguage" id="changelanguage" value="0" />
<select name="menu_language" id="menu_language" size="0" onchange="document.form_menueditor.changelanguage.value=1; document.form_menueditor.submit()">
<?php
	$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"".$m['language_code']."\"";
			if($m['language_code'] == $menu_language) {
				echo " selected=\"selected\"";
			}
			echo ">".htmlentities($m['language_name'], ENT_NOQUOTES, $l['a_meta_charset'])."&nbsp;</option>\n";
		}
	}
	else {
		F_display_db_error();
	}
?>
</select>
</td>
</tr>
<!-- END SELECT language ==================== -->

<tr class="fillO">
<td class="fillOO" align="right">&nbsp;</td>
<td class="fillOE" colspan="2"><hr /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_name', 'h_menued_name'); ?></b></td>
<td class="fillEE" colspan="2"><input type="text" name="menu_name" id="menu_name" value="<?php echo htmlentities($menu_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="20" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_link', 'h_menued_link'); ?></b></td>
<td class="fillOE" colspan="2"><input type="text" name="menu_link" id="menu_link" value="<?php echo $menu_link; ?>" size="20" maxlength="255" />
<?php 
if ($menu_link) {
	//calculate full path for the given page link
	if($menu_table == K_TABLE_MENU) {
		$thislinkpath = realpath($menu_link);
	}
	else {
		$thislinkpath = realpath(K_PATH_PUBLIC_CODE_REAL.$menu_link);
	}
	$thislinkpath = str_replace("\\", "/", $thislinkpath); //Unix - Windows compatibility
	//display an edit button
	F_generic_button("edit", $l['w_edit'], "window.open('cp_edit_file.".CP_EXT."?file=".urlencode($thislinkpath)."','CPMAIN')");
}
?>
</td>
</tr>

<?php //display dynamic page selector
if ($menu_table != K_TABLE_MENU) {
?>
<!-- SELECT DYNAMIC PAGE ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_page', 'h_menued_page'); ?></b></td>
<td class="fillOE" colspan="2">
<select name="menu_dpagelink" id="menu_dpagelink" size="0" onchange="document.form_menueditor.menu_link.value=document.form_menueditor.menu_dpagelink.options[document.form_menueditor.menu_dpagelink.selectedIndex].value;">
<option value="">&nbsp;</option>
<?php
$sql = "SELECT * FROM ".K_TABLE_PAGE_DATA." ORDER BY pagedata_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"cp_dpage.".CP_EXT."?aiocp_dp=".$m['pagedata_name']."\">".htmlentities($m['pagedata_name'], ENT_NOQUOTES, $l['a_meta_charset'])."&nbsp;</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT DYNAMIC PAGE ==================== -->
<?php
}
?>

<!-- SELECT TARGET ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_target', 'h_menued_target'); ?></b></td>
<td class="fillEE" colspan="2"><select name="menu_target" id="menu_target" size="0">
<?php
$sql = "SELECT * FROM ".K_TABLE_FRAME_TARGETS." ORDER BY target_name";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['target_id']."\"";
		if($m['target_id'] == $menu_target) {
			echo " selected=\"selected\"";
		}
		echo ">".$m['target_name']."&nbsp;</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select></td>
</tr>
<!-- END SELECT TARGET ==================== -->

<tr class="fillO">
<td class="fillOO" align="right" valign="top"><b><?php echo F_display_field_name('w_description', 'h_menued_description'); ?></b></td>
<td class="fillOE" colspan="2"><textarea cols="20" rows="6" name="menu_description" id="menu_description"><?php echo htmlentities($menu_description, ENT_NOQUOTES, $l['a_meta_charset']); ?></textarea></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_enabled', 'h_menued_enable'); ?></b></td>
<td class="fillEE">
<?php
echo "<input type=\"radio\" name=\"menu_enabled\" value=\"1\"";
if($menu_enabled) {echo " checked=\"checked\"";}
echo " />".$l['w_yes']."&nbsp;";

echo "<input type=\"radio\" name=\"menu_enabled\" value=\"0\"";
if(!$menu_enabled) {echo " checked=\"checked\"";}
echo " />".$l['w_no']."&nbsp;";
?>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_style', 'h_menustyle_select'); ?></b></td>
<td class="fillOE" colspan="2"><select name="menu_style_id" id="menu_style_id" size="0">
<option value="">&nbsp;</option>
<?php
$sql = "SELECT * FROM ".K_TABLE_MENU_STYLES." ORDER BY menustyle_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['menustyle_id']."\"";
		if($m['menustyle_id'] == $menu_style_id) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['menustyle_name'], ENT_NOQUOTES, $l['a_meta_charset'])."&nbsp;</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select></td>
</tr>

<!-- SELECT ICON ID ==================== -->
<tr class="fillE">
<td class="fillEO" align="right" valign="top">
<a href="javascript:void(0);" onclick="selectWindow=window.open('<?php echo $selecticon; ?>?formname=form_menueditor&amp;idfield=icon_link&amp;fieldtype=0&amp;fsubmit=0','selectWindow','dependent,height=450,width=450,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')"><b><?php echo $l['w_icon']; ?></b></a>
</td>
<td class="fillEE" valign="top">
<select name="icon_link" id="icon_link" size="0" onfocus="FJ_show_menu_icon()" onchange="FJ_show_menu_icon()">
<?php
$sql = "SELECT * FROM ".$icontable." ORDER BY icon_name";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['icon_link']."\"";
		if($m['icon_id'] == $menu_iconid) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['icon_name'], ENT_NOQUOTES, $l['a_meta_charset'])."&nbsp;</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select> <b><?php echo F_display_field_name('w_general', 'h_menued_icon'); ?></b><br />
</td>
<td class="fillEE" align="right"><img name="menuicon" src="<?php echo $iconpath.K_BLANK_IMAGE; ?>" border="0" alt="" /></td></tr>
<!-- END SELECT ICON ==================== -->

<?php
//display 3 state button icons only for client first level of tree (root menu items)
if (($menu_sub_id == 0) AND ($menu_table != K_TABLE_MENU) ) {
?>

<tr class="fillO">
<td class="fillOO" align="right">&nbsp;</td>
<td class="fillOE" colspan="2"><b><?php echo $l['w_button']; ?></b></td>
</tr>

	<!-- SELECT icon_off==================== -->
	<tr class="fillE">
	<td class="fillEO" align="right" valign="top">
	<a href="javascript:void(0);" onclick="selectWindow=window.open('<?php echo $selecticon; ?>?formname=form_menueditor&amp;idfield=icon_off_link&amp;fieldtype=0&amp;fsubmit=0','selectWindow','dependent,height=450,width=450,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')"><b><?php echo $l['w_icon']; ?></b></a>
	</td>
	<td class="fillEE" valign="top">
	<select name="icon_off_link" id="icon_off_link" size="0" onfocus="FJ_show_menu_icon_off()" onchange="FJ_show_menu_icon_off()">
	<?php
	$sql = "SELECT * FROM ".$icontable." ORDER BY icon_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"".$m['icon_link']."\"";
			if($m['icon_id'] == $menu_icon_off) {
				echo " selected=\"selected\"";
			}
			echo ">".htmlentities($m['icon_name'], ENT_NOQUOTES, $l['a_meta_charset'])."&nbsp;</option>\n";
		}
	}
	else {
		F_display_db_error();
	}
	?>
	</select> <b><?php echo F_display_field_name('w_off', 'h_menued_icon_off'); ?></b><br />
	
	</td>
	<td class="fillEE" align="right"><img name="menuicon_off" src="<?php echo $iconpath.K_BLANK_IMAGE; ?>" border="0" alt="" /></td></tr>
	<!-- END SELECT ICON ==================== -->
	
	<!-- SELECT icon_over==================== -->
	<tr class="fillO">
	<td class="fillOO" align="right" valign="top">
	<a href="javascript:void(0);" onclick="selectWindow=window.open('<?php echo $selecticon; ?>?formname=form_menueditor&amp;idfield=icon_over_link&amp;fieldtype=0&amp;fsubmit=0','selectWindow','dependent,height=450,width=450,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')"><b><?php echo $l['w_icon']; ?></b></a>
	</td>
	<td class="fillOE" valign="top">
	<select name="icon_over_link" id="icon_over_link" size="0" onfocus="FJ_show_menu_icon_over()" onchange="FJ_show_menu_icon_over()">
	<?php
	$sql = "SELECT * FROM ".$icontable." ORDER BY icon_name";
	
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"".$m['icon_link']."\"";
			if($m['icon_id'] == $menu_icon_over) {
				echo " selected=\"selected\"";
			}
			echo ">".htmlentities($m['icon_name'], ENT_NOQUOTES, $l['a_meta_charset'])."&nbsp;</option>\n";
		}
	}
	else {
		F_display_db_error();
	}
	?>
	</select> <b><?php echo F_display_field_name('w_over', 'h_menued_icon_over'); ?></b><br />
	
	</td>
	<td class="fillOE" align="right"><img name="menuicon_over" src="<?php echo $iconpath.K_BLANK_IMAGE; ?>" border="0" alt="" /></td></tr>
	<!-- END SELECT ICON_over ==================== -->
	
	<!-- SELECT icon_on==================== -->
	<tr class="fillE">
	<td class="fillEO" align="right" valign="top">
	<a href="javascript:void(0);" onclick="selectWindow=window.open('<?php echo $selecticon; ?>?formname=form_menueditor&amp;idfield=icon_on_link&amp;fieldtype=0&amp;fsubmit=0','selectWindow','dependent,height=450,width=450,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')"><b><?php echo $l['w_icon']; ?></b></a>
	</td>
	<td class="fillEE" valign="top">
	<select name="icon_on_link" id="icon_on_link" size="0" onfocus="FJ_show_menu_icon_on()" onchange="FJ_show_menu_icon_on()">
	<?php
	$sql = "SELECT * FROM ".$icontable." ORDER BY icon_name";
	
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"".$m['icon_link']."\"";
			if($m['icon_id'] == $menu_icon_on) {
				echo " selected=\"selected\"";
			}
			echo ">".htmlentities($m['icon_name'], ENT_NOQUOTES, $l['a_meta_charset'])."&nbsp;</option>\n";
		}
	}
	else {
		F_display_db_error();
	}
	?>
	</select> <b><?php echo F_display_field_name('w_on', 'h_menued_icon_on'); ?></b><br />
	
	</td>
	<td class="fillEE" align="right"><img name="menuicon_on" src="<?php echo $iconpath.K_BLANK_IMAGE; ?>" border="0" alt="" /></td></tr>
	<!-- END SELECT ICON_over ==================== -->
<?php
}
else { //set void images
?>
<tr class="fillE">
<td class="fillEO">
<input type="hidden" name="menu_icon_off" id="menu_icon_off" value="1" />
<input type="hidden" name="menu_icon_over" id="menu_icon_over" value="1" />
<input type="hidden" name="menu_icon_on" id="menu_icon_on" value="1" />
</td></tr>
<?php
}
?>

</table>

<input type="hidden" name="menu_id" id="menu_id" value="<?php echo $menu_id; ?>" />
<input type="hidden" name="menu_item" id="menu_item" value="<?php echo $menu_item; ?>" />
<input type="hidden" name="menu_sub_id" id="menu_sub_id" value="<?php echo $menu_sub_id; ?>" />
<input type="hidden" name="menu_position" id="menu_position" value="<?php echo $menu_position; ?>" />

<div align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php F_submit_button("form_menueditor","menu_mode",$l['w_update']); ?>
<?php //F_submit_button("form_menueditor","menu_mode",$l['w_clear']); ?>
</div>

<?php if ($menu_table == K_TABLE_MENU) { //AIOCP menu?>
<div align="center"><a class="edge" href="javascript:FJ_reload_real_menu()"><b><?php echo $l['d_menu_reload'] ?></b></a></div>
<?php } ?>
</td>

<td class="edge">
<table class="fill" border="0" cellspacing="2" cellpadding="1">
<tr class="fill">
<td class="fill">
<!-- Draw the menu tree with option buttons -->
<?php F_explore_tree($menu_language, $menu_table, $tree_suffix); ?>
</td>
</tr>
</table>
</td>

</tr>
</table>

</form>

<!-- Show selected icon image ==================== -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
function FJ_show_menu_icon(){
	document.images.menuicon.src= "<?php echo $iconpath; ?>"+document.form_menueditor.icon_link.options[document.form_menueditor.icon_link.selectedIndex].value;
}
FJ_show_menu_icon();
//]]>
</script>
<!-- END Show selected icon image ==================== -->
<?php
//display 3 state button icons only for first level of tree (root menu items)
if (($menu_sub_id == 0) AND ($menu_table != K_TABLE_MENU) ) {
?>
<!-- Show selected icons images ==================== -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
function FJ_show_menu_icon_off(){
	document.images.menuicon_off.src= "<?php echo $iconpath; ?>"+document.form_menueditor.icon_off_link.options[document.form_menueditor.icon_off_link.selectedIndex].value;
}
function FJ_show_menu_icon_over(){
	document.images.menuicon_over.src= "<?php echo $iconpath; ?>"+document.form_menueditor.icon_over_link.options[document.form_menueditor.icon_over_link.selectedIndex].value;
}
function FJ_show_menu_icon_on(){
	document.images.menuicon_on.src= "<?php echo $iconpath; ?>"+document.form_menueditor.icon_on_link.options[document.form_menueditor.icon_on_link.selectedIndex].value;
}
FJ_show_menu_icon_off();
FJ_show_menu_icon_over();
FJ_show_menu_icon_on();
//]]>
</script>
<!-- END Show selected icons images ==================== -->
<?php 
} 
?>


<!-- Refresh Real Menu ==================== -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
function FJ_reload_real_menu() {
	parent.frames['CPMENU'].location.href="cp_layout_menu.<?php echo CP_EXT; ?>"; 
	parent.frames['CPMENU'].location.reload();
}
//]]>
</script>
<!-- END Refresh Real Menu ==================== -->


<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
