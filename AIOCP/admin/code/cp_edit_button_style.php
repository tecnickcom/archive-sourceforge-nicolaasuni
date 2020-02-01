<?php
//============================================================+
// File name   : cp_edit_button_style.php                      
// Begin       : 2002-05-20                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit styles for graphic buttons (and bars)    
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_BUTTON_STYLE;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_button_style_editor'];

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
		if ($buttonstyle_id > 3) { //first 3 default values could not be deleted
			$sql = "DELETE FROM ".K_TABLE_BUTTON_STYLES." WHERE buttonstyle_id=".$buttonstyle_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			$buttonstyle_id=FALSE;
		}
		break;
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_BUTTON_STYLES,"buttonstyle_name='".$buttonstyle_name."'","buttonstyle_id",$buttonstyle_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				if (strlen($buttonstyle_width)<1) {$buttonstyle_width = "-1";}
				$sql = "UPDATE IGNORE ".K_TABLE_BUTTON_STYLES." SET 
   						buttonstyle_imgdir='".$buttonstyle_imgdir."', 
						buttonstyle_cornerswidth='".$buttonstyle_cornerswidth."', 
						buttonstyle_defaulttext='".$buttonstyle_defaulttext."', 
						buttonstyle_font='".$buttonstyle_font."', 
						buttonstyle_textsize='".$buttonstyle_textsize."', 
						buttonstyle_textalign='".$buttonstyle_textalign."', 
						buttonstyle_height='".$buttonstyle_height."', 
						buttonstyle_width='".$buttonstyle_width."',
						buttonstyle_gamma='".$buttonstyle_gamma."', 
						buttonstyle_textcolor='".$buttonstyle_textcolor."', 
						buttonstyle_darkcolor='".$buttonstyle_darkcolor."', 
						buttonstyle_lightcolor='".$buttonstyle_lightcolor."', 
						buttonstyle_transparentcolor='".$buttonstyle_transparentcolor."', 
						buttonstyle_margin='".$buttonstyle_margin."', 
						buttonstyle_horizontal='".$buttonstyle_horizontal."', 
						buttonstyle_usecache='".$buttonstyle_usecache."' 
						WHERE buttonstyle_id=".$buttonstyle_id."";
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
			//check if buttonstyle_name is unique
			$sql = "SELECT buttonstyle_name FROM ".K_TABLE_BUTTON_STYLES." WHERE buttonstyle_name='".$buttonstyle_name."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				if (!$buttonstyle_width=="") {$buttonstyle_width = "-1";}
				$sql = "INSERT IGNORE INTO ".K_TABLE_BUTTON_STYLES." (
						buttonstyle_name, 
   						buttonstyle_imgdir, 
						buttonstyle_cornerswidth, 
						buttonstyle_defaulttext, 
						buttonstyle_font, 
						buttonstyle_textsize, 
						buttonstyle_textalign, 
						buttonstyle_height, 
						buttonstyle_width, 
						buttonstyle_gamma, 
						buttonstyle_textcolor, 
						buttonstyle_darkcolor, 
						buttonstyle_lightcolor, 
						buttonstyle_transparentcolor, 
						buttonstyle_margin, 
						buttonstyle_horizontal, 
						buttonstyle_usecache
						) VALUES (
						'".$buttonstyle_name."', 
   						'".$buttonstyle_imgdir."', 
						'".$buttonstyle_cornerswidth."', 
						'".$buttonstyle_defaulttext."', 
						'".$buttonstyle_font."', 
						'".$buttonstyle_textsize."', 
						'".$buttonstyle_textalign."', 
						'".$buttonstyle_height."', 
						'".$buttonstyle_width."', 
						'".$buttonstyle_gamma."', 
						'".$buttonstyle_textcolor."', 
						'".$buttonstyle_darkcolor."', 
						'".$buttonstyle_lightcolor."', 
						'".$buttonstyle_transparentcolor."', 
						'".$buttonstyle_margin."', 
						'".$buttonstyle_horizontal."', 
						'".$buttonstyle_usecache."' 
						)";
						
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$buttonstyle_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
	}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$buttonstyle_name = ""; 
   		$buttonstyle_imgdir = K_PATH_IMAGES_BUTTONS."glass/"; 
   		$buttonstyle_cornerswidth = "8"; 
   		$buttonstyle_defaulttext = ""; 
   		$buttonstyle_font = "verdana.ttf"; 
   		$buttonstyle_textsize = "10"; 
   		$buttonstyle_textalign = ""; 
   		$buttonstyle_height = "-1"; 
   		$buttonstyle_width = "-1"; 
   		$buttonstyle_gamma = "1"; 
   		$buttonstyle_textcolor = "#000000"; 
   		$buttonstyle_darkcolor = "#000000"; 
   		$buttonstyle_lightcolor = "#FFFFFF"; 
   		$buttonstyle_transparentcolor = "#FFFFFF"; 
   		$buttonstyle_margin = "3"; 
   		$buttonstyle_horizontal = "1"; 
   		$buttonstyle_usecache = "1"; 
		break;
	}

	default :{ 
		break;
	}

} //end of switch

// Initialize variables
if($formstatus) {
	
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if(!isset($buttonstyle_id) OR (!$buttonstyle_id)) {
			$sql = "SELECT * FROM ".K_TABLE_BUTTON_STYLES." ORDER BY buttonstyle_name LIMIT 1";
		} else {
			$sql = "SELECT * FROM ".K_TABLE_BUTTON_STYLES." WHERE buttonstyle_id=".$buttonstyle_id." LIMIT 1";
		}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$buttonstyle_id = $m['buttonstyle_id'];
				$buttonstyle_name = $m['buttonstyle_name'];
   				$buttonstyle_imgdir = $m['buttonstyle_imgdir'];
		   		$buttonstyle_cornerswidth = $m['buttonstyle_cornerswidth'];
		   		$buttonstyle_defaulttext = $m['buttonstyle_defaulttext'];
		   		$buttonstyle_font = $m['buttonstyle_font'];
		   		$buttonstyle_textsize = $m['buttonstyle_textsize'];
		   		$buttonstyle_textalign = $m['buttonstyle_textalign'];
		   		$buttonstyle_height = $m['buttonstyle_height'];
		   		$buttonstyle_width = $m['buttonstyle_width'];
		   		$buttonstyle_gamma = $m['buttonstyle_gamma'];
		   		$buttonstyle_textcolor = $m['buttonstyle_textcolor'];
		   		$buttonstyle_darkcolor = $m['buttonstyle_darkcolor'];
		   		$buttonstyle_lightcolor = $m['buttonstyle_lightcolor'];
		   		$buttonstyle_transparentcolor = $m['buttonstyle_transparentcolor'];
		   		$buttonstyle_margin = $m['buttonstyle_margin'];
		   		$buttonstyle_horizontal = $m['buttonstyle_horizontal'];
		   		$buttonstyle_usecache = $m['buttonstyle_usecache'];
			}
			else {
				$buttonstyle_name = ""; 
		   		$buttonstyle_imgdir = K_PATH_IMAGES_BUTTONS."glass/"; 
		   		$buttonstyle_cornerswidth = "8"; 
		   		$buttonstyle_defaulttext = ""; 
		   		$buttonstyle_font = "verdana.ttf"; 
		   		$buttonstyle_textsize = "10"; 
		   		$buttonstyle_textalign = ""; 
		   		$buttonstyle_height = "-1"; 
		   		$buttonstyle_width = "-1"; 
		   		$buttonstyle_gamma = "1"; 
		   		$buttonstyle_textcolor = "#000000"; 
		   		$buttonstyle_darkcolor = "#000000"; 
		   		$buttonstyle_lightcolor = "#FFFFFF"; 
		   		$buttonstyle_transparentcolor = "#FFFFFF"; 
		   		$buttonstyle_margin = "3"; 
		   		$buttonstyle_horizontal = "1"; 
		   		$buttonstyle_usecache = "1"; 
			}
		}
		else {
			F_display_db_error();
		}
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_buttonstyleeditor" id="form_buttonstyleeditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="buttonstyle_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT style ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_style', 'h_buttonstyle_select'); ?></b></td>
<td class="fillOE" colspan="2">
<select name="buttonstyle_id" id="buttonstyle_id" size="0" onchange="document.form_buttonstyleeditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_BUTTON_STYLES." ORDER BY buttonstyle_name";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['buttonstyle_id']."\"";
		if($m['buttonstyle_id'] == $buttonstyle_id) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlentities($m['buttonstyle_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
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
<td class="fillOE" align="right"><b><?php echo F_display_field_name('w_name', 'h_buttonstyle_name'); ?></b></td>
<td class="fillOE" colspan="2"><input type="text" name="buttonstyle_name" id="buttonstyle_name" value="<?php echo htmlentities($buttonstyle_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="20" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEE" align="right"><b><?php echo F_display_field_name('w_text', 'h_buttonstyle_text'); ?></b></td>
<td class="fillEE" colspan="2"><input type="text" name="buttonstyle_defaulttext" id="buttonstyle_defaulttext" value="<?php echo htmlentities($buttonstyle_defaulttext, ENT_COMPAT, $l['a_meta_charset']); ?>" size="20" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_font', 'h_buttonstyle_font'); ?></b></td>
<td class="fillOE" colspan="2">
<select name="buttonstyle_font" id="buttonstyle_font" size="0">
<?php
// read directory for files (only graphics files).
$handle = opendir(K_PATH_FONTS);
while (false !== ($file = readdir($handle))) {
	$path_parts = pathinfo($file);
	$file_ext = strtolower($path_parts['extension']);
	//check file type 
	if($file_ext=="ttf") {
		echo "<option value=\"".$file."\"";
		if($file == $buttonstyle_font) {
			echo " selected=\"selected\"";
		}
		echo ">".$file."</option>\n";
	}
}
closedir($handle);
?>
</select>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_font_size', 'h_buttonstyle_font_size'); ?></b></td>
<td class="fillEE" colspan="2"><input type="text" name="buttonstyle_textsize" id="buttonstyle_textsize" value="<?php echo $buttonstyle_textsize; ?>" size="10" maxlength="255" /> <b>[<?php echo $l['w_pixels']; ?>]</b></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_align', 'h_buttonstyle_align'); ?></b></td>
<td class="fillOE" colspan="2">
<select name="buttonstyle_textalign" id="buttonstyle_textalign" size="0">
<?php
echo "<option value=\"left\"";
if ($buttonstyle_textalign == "left") {echo " selected=\"selected\"";}
echo ">".$l['w_left']."</option>\n";

echo "<option value=\"middle\"";
if ($buttonstyle_textalign == "middle") {echo " selected=\"selected\"";}
echo ">".$l['w_middle']."</option>\n";

echo "<option value=\"right\"";
if ($buttonstyle_textalign == "right") {echo " selected=\"selected\"";}
echo ">".$l['w_right']."</option>\n";
?>
</select>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_image', 'h_buttonstyle_image'); ?></b></td>
<td class="fillEE" colspan="2">
<select name="buttonstyle_imgdir" id="buttonstyle_imgdir" size="0">
<?php
// read directory for files (only graphics files).
$handle = opendir(K_PATH_IMAGES_BUTTONS);
while (false !== ($file = readdir($handle))) {
	if(($file!=".") and ($file!="..")) {
		echo "<option value=\"".$file."\"";
		if($file == $buttonstyle_imgdir) {
			echo " selected=\"selected\"";
		}
		echo ">".$file."</option>\n";
	}
}
closedir($handle);
?>
</select>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_corners', 'h_buttonstyle_corners'); ?></b></td>
<td class="fillOE" colspan="2"><input type="text" name="buttonstyle_cornerswidth" id="buttonstyle_cornerswidth" value="<?php echo $buttonstyle_cornerswidth; ?>" size="10" maxlength="255" /> <b>[<?php echo $l['w_pixels']; ?>]</b></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_size', 'h_buttonstyle_height'); ?></b></td>
<td class="fillEE" colspan="2">
<select name="buttonstyle_height" id="buttonstyle_height" size="0">
<?php
echo "<option value=\"-1\"";
if ($buttonstyle_height == "-1") {echo " selected=\"selected\"";}
echo ">".$l['w_auto']."</option>\n";
for ($ih=5; $ih<=40; $ih+=5) {
	echo "<option value=\"".$ih."\"";
	if ($buttonstyle_height == $ih) {echo " selected=\"selected\"";}
	echo ">".$ih."</option>\n";
}
?>
</select> <b>[<?php echo $l['w_pixels']; ?>]</b>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_width', 'h_buttonstyle_width'); ?></b></td>
<td class="fillOE" colspan="2"><input type="text" name="buttonstyle_width" id="buttonstyle_width" value="<?php echo $buttonstyle_width; ?>" size="10" maxlength="255" /> <b>[<?php echo $l['w_pixels']; ?>]</b></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_padding', 'h_buttonstyle_padding'); ?></b></td>
<td class="fillEE" colspan="2"><input type="text" name="buttonstyle_margin" id="buttonstyle_margin" value="<?php echo $buttonstyle_margin; ?>" size="10" maxlength="255" /> <b>[<?php echo $l['w_pixels']; ?>]</b></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_horizontal', 'h_buttonstyle_horizontal'); ?></b></td>
<td class="fillOE" colspan="2">
<?php
echo "<input type=\"radio\" name=\"buttonstyle_horizontal\" value=\"1\"";
if($buttonstyle_horizontal) {echo " checked=\"checked\"";}
echo " />".$l['w_yes']."&nbsp;";

echo "<input type=\"radio\" name=\"buttonstyle_horizontal\" value=\"0\"";
if(!$buttonstyle_horizontal) {echo " checked=\"checked\"";}
echo " />".$l['w_no']."&nbsp;";
?>
</td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_cache', 'h_buttonstyle_cache'); ?></b></td>
<td class="fillEE" colspan="2">
<?php
echo "<input type=\"radio\" name=\"buttonstyle_usecache\" value=\"1\"";
if($buttonstyle_usecache) {echo " checked=\"checked\"";}
echo " />".$l['w_yes']."&nbsp;";

echo "<input type=\"radio\" name=\"buttonstyle_usecache\" value=\"0\"";
if(!$buttonstyle_usecache) {echo " checked=\"checked\"";}
echo " />".$l['w_no']."&nbsp;";
?>
</td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right">&nbsp;</td>
<td class="fillOE" colspan="2"><br /><b><?php echo F_display_field_name('w_colors', 'h_buttonstyle_colors'); ?></b></td>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_gamma', 'h_buttonstyle_color_gamma'); ?></b></td>
<td class="fillEE" colspan="2"><input type="text" name="buttonstyle_gamma" id="buttonstyle_gamma" value="<?php echo $buttonstyle_gamma; ?>" size="10" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_text', 'h_buttonstyle_color_text'); ?></b></td>
<td class="fillOE"><input type="text" name="buttonstyle_textcolor" id="buttonstyle_textcolor" value="<?php echo $buttonstyle_textcolor; ?>" size="10" maxlength="8" onchange="FJ_show_colors()" onfocus="FJ_show_colors()" onBlur="FJ_show_colors()" />
<?php F_generic_button("pickcolor1",$l['w_pick'],"colorWindow=window.open('cp_edit_html_colors.".CP_EXT."?callingform=form_buttonstyleeditor&amp;callingfield=buttonstyle_textcolor','colorWindow','dependent,height=490,width=330,menubar=no,resizable=no,scrollbars=no,status=no,toolbar=no')"); ?>
</td>
<td class="fillOE"><div id="pickedcolor1" style="position:relative; visibility:visible">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_dark', 'h_buttonstyle_color_dark'); ?></b></td>
<td class="fillEE"><input type="text" name="buttonstyle_darkcolor" id="buttonstyle_darkcolor" value="<?php echo $buttonstyle_darkcolor; ?>" size="10" maxlength="8" onchange="FJ_show_colors()" onfocus="FJ_show_colors()" onBlur="FJ_show_colors()" />
<?php F_generic_button("pickcolor2",$l['w_pick'],"colorWindow=window.open('cp_edit_html_colors.".CP_EXT."?callingform=form_buttonstyleeditor&amp;callingfield=buttonstyle_darkcolor','colorWindow','dependent,height=490,width=330,menubar=no,resizable=no,scrollbars=no,status=no,toolbar=no')"); ?>
</td>
<td class="fillEE"><div id="pickedcolor2" style="position:relative; visibility:visible">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_light', 'h_buttonstyle_color_light'); ?></b></td>
<td class="fillOE"><input type="text" name="buttonstyle_lightcolor" id="buttonstyle_lightcolor" value="<?php echo $buttonstyle_lightcolor; ?>" size="10" maxlength="8" onchange="FJ_show_colors()" onfocus="FJ_show_colors()" onBlur="FJ_show_colors()" />
<?php F_generic_button("pickcolor3",$l['w_pick'],"colorWindow=window.open('cp_edit_html_colors.".CP_EXT."?callingform=form_buttonstyleeditor&amp;callingfield=buttonstyle_lightcolor','colorWindow','dependent,height=490,width=330,menubar=no,resizable=no,scrollbars=no,status=no,toolbar=no')"); ?>
</td>
<td class="fillOE"><div id="pickedcolor3" style="position:relative; visibility:visible">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_transparent', 'h_buttonstyle_color_transparent'); ?></b></td>
<td class="fillEE"><input type="text" name="buttonstyle_transparentcolor" id="buttonstyle_transparentcolor" value="<?php echo $buttonstyle_transparentcolor; ?>" size="10" maxlength="8" onchange="FJ_show_colors()" onfocus="FJ_show_colors()" onBlur="FJ_show_colors()" />
<?php F_generic_button("pickcolor4",$l['w_pick'],"colorWindow=window.open('cp_edit_html_colors.".CP_EXT."?callingform=form_buttonstyleeditor&amp;callingfield=buttonstyle_transparentcolor','colorWindow','dependent,height=490,width=330,menubar=no,resizable=no,scrollbars=no,status=no,toolbar=no')"); ?>
</td>
<td class="fillEILLE"><div id="pickedcolor4" style="position:relative; visibility:visible">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div></td>
</tr>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if ($buttonstyle_id) {
	F_submit_button("form_buttonstyleeditor","menu_mode",$l['w_update']); 
	F_submit_button("form_buttonstyleeditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_buttonstyleeditor","menu_mode",$l['w_add']); 
F_submit_button("form_buttonstyleeditor","menu_mode",$l['w_clear']); 
?>
</td>
</tr>
</table>
</form>
<!-- ====================================================== -->

<!-- Show selected avatar image ==================== -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
// Display Colors
function FJ_show_colors() {
	if(document.layers){  //netscape 4
		document.layers['pickedcolor1'].bgColor = document.form_buttonstyleeditor.buttonstyle_textcolor.value;   
		document.layers['pickedcolor2'].bgColor = document.form_buttonstyleeditor.buttonstyle_darkcolor.value; 
		document.layers['pickedcolor3'].bgColor = document.form_buttonstyleeditor.buttonstyle_lightcolor.value;   
		document.layers['pickedcolor4'].bgColor = document.form_buttonstyleeditor.buttonstyle_transparentcolor.value;         
	}         
	if(document.all){ //IE 
		document.all.pickedcolor1.style.backgroundColor = document.form_buttonstyleeditor.buttonstyle_textcolor.value;
		document.all.pickedcolor2.style.backgroundColor = document.form_buttonstyleeditor.buttonstyle_darkcolor.value;
		document.all.pickedcolor3.style.backgroundColor = document.form_buttonstyleeditor.buttonstyle_lightcolor.value;
		document.all.pickedcolor4.style.backgroundColor = document.form_buttonstyleeditor.buttonstyle_transparentcolor.value;
	}        
	if(!document.all && document.getElementById){  //netscape 6   
		document.getElementById("pickedcolor1").style.backgroundColor = document.form_buttonstyleeditor.buttonstyle_textcolor.value;
		document.getElementById("pickedcolor2").style.backgroundColor = document.form_buttonstyleeditor.buttonstyle_darkcolor.value;
		document.getElementById("pickedcolor3").style.backgroundColor = document.form_buttonstyleeditor.buttonstyle_lightcolor.value;
		document.getElementById("pickedcolor4").style.backgroundColor = document.form_buttonstyleeditor.buttonstyle_transparentcolor.value;
	}
	return;
}

FJ_show_colors();

document.form_buttonstyleeditor.buttonstyle_id.focus();
//]]>
</script>
<!-- END Cange focus to buttonstyle_id select -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
