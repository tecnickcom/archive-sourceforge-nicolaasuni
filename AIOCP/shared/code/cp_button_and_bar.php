<?php
//============================================================+
// File name   : cp_button_and_bar.php                         
// Begin       : 2002-01-15                                    
// Last Update : 2003-10-25                                    
//                                                             
// Description : istantiate a button (or bar) graphic object   
//               using requested and default values            
// the default values could be replaced by button style values 
// and by user requested values                                
//                                                             
// Example of use:                                             
// <img src="../../shared/code/cp_button_and_bar.php?s=style_name&amp;t=ciao" border="0" />
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
require_once('../../shared/config/cp_config.'.CP_EXT);
require_once('../../shared/config/cp_db_config.'.CP_EXT);
require_once('../../shared/code/cp_db_connect.'.CP_EXT);
require_once('../../shared/code/cp_functions_button.'.CP_EXT);
require_once('../../shared/code/cp_functions_general.'.CP_EXT);

// Instantiate C_button class
$imgbutton = new C_button;

// set button default values
$buttonstyle_imgdir = K_PATH_IMAGES_BUTTONS."bevel/";
$buttonstyle_cornerswidth = 8;
$buttonstyle_defaulttext = "";
$buttonstyle_font = "Vera.ttf";
$buttonstyle_textsize = 10;
$buttonstyle_textalign = "middle";
$buttonstyle_height = -1;
$buttonstyle_width = -1;
$buttonstyle_gamma = 1;
$buttonstyle_textcolor = "#000000";
$buttonstyle_darkcolor = "#0080FF";
$buttonstyle_lightcolor = "#FFFFFF";
$buttonstyle_transparentcolor = "#FFFFFF";
$buttonstyle_usetransparent = true;
$buttonstyle_margin = 3;
$buttonstyle_horizontal = 1;
$buttonstyle_usecache = true;

if(isset($_REQUEST['s'])) { //if button style has been requested, load defaults from database
	$sqlbutton = "SELECT * FROM ".K_TABLE_BUTTON_STYLES." WHERE buttonstyle_name='".$_REQUEST['s']."' LIMIT 1";
	if($rbutton = F_aiocpdb_query($sqlbutton, $db)) {
		if($mbutton = F_aiocpdb_fetch_array($rbutton)) {
			$buttonstyle_imgdir = K_PATH_IMAGES_BUTTONS.$mbutton['buttonstyle_imgdir']."/";
	   		$buttonstyle_cornerswidth = $mbutton['buttonstyle_cornerswidth'];
	   		$buttonstyle_defaulttext = $mbutton['buttonstyle_defaulttext'];
	   		$buttonstyle_font = $mbutton['buttonstyle_font'];
	   		$buttonstyle_textsize = $mbutton['buttonstyle_textsize'];
	   		$buttonstyle_textalign = $mbutton['buttonstyle_textalign'];
	   		$buttonstyle_height = $mbutton['buttonstyle_height'];
	   		$buttonstyle_width = $mbutton['buttonstyle_width'];
	   		$buttonstyle_gamma = $mbutton['buttonstyle_gamma'];
	   		$buttonstyle_textcolor = $mbutton['buttonstyle_textcolor'];
	   		$buttonstyle_darkcolor = $mbutton['buttonstyle_darkcolor'];
	   		$buttonstyle_lightcolor = $mbutton['buttonstyle_lightcolor'];
	   		$buttonstyle_transparentcolor = $mbutton['buttonstyle_transparentcolor'];
			if ($buttonstyle_transparentcolor) {
				$buttonstyle_usetransparent=true;
			}
			else {
				$buttonstyle_usetransparent=false;
			}
	   		$buttonstyle_margin = $mbutton['buttonstyle_margin'];
	   		$buttonstyle_horizontal = $mbutton['buttonstyle_horizontal'];
	   		$buttonstyle_usecache = $mbutton['buttonstyle_usecache'];
		}
	}
	else {
		F_display_db_error();
	}
}

//cw = corners width (width of the left and right button frame images)
if(isset($_REQUEST['cw'])) {$imgbutton->corners_width = $_REQUEST['cw'];}
else {$imgbutton->corners_width = $buttonstyle_cornerswidth;}

//pf = path to fonts folder
if(isset($_REQUEST['pf'])) {$imgbutton->path_fonts = $_REQUEST['pf'];}
else {$imgbutton->path_fonts = K_PATH_FONTS;}

//pb = path to PNG buttons images folder
if(isset($_REQUEST['pb'])) {$imgbutton->path_dynamic_buttons = K_PATH_IMAGES_BUTTONS.$_REQUEST['pb']."/";}
else {$imgbutton->path_dynamic_buttons = $buttonstyle_imgdir;}

//t = button text
if(isset($_REQUEST['t'])) {$imgbutton->text = unhtmlentities(stripslashes($_REQUEST['t']));}
else {$imgbutton->text = $buttonstyle_defaulttext;}

//tf = button TTF font
if(isset($_REQUEST['tf'])) {$imgbutton->text_font = $_REQUEST['tf'];}
else {$imgbutton->text_font = $buttonstyle_font;}

//ts = text size
if(isset($_REQUEST['ts'])) {$imgbutton->text_size = $_REQUEST['ts'];}
else {$imgbutton->text_size = $buttonstyle_textsize;}

//ta = text alignment (left | middle | right)
if(isset($_REQUEST['ta'])) {$imgbutton->text_alignment = $_REQUEST['ta'];}
else {$imgbutton->text_alignment = $buttonstyle_textalign;}

//h = button height (-1 for automatic)
if(isset($_REQUEST['h'])) {$imgbutton->button_height = $_REQUEST['h'];}
else {$imgbutton->button_height = $buttonstyle_height; }

//w = button width (-1 for automatic)
if(isset($_REQUEST['w'])) {$imgbutton->button_width = $_REQUEST['w'];}
else {$imgbutton->button_width = $buttonstyle_width;}

//g = button gamma (1=normal | <1 darker | >1 lighter )
if(isset($_REQUEST['g'])) {$imgbutton->button_gamma = $_REQUEST['g'];}
else {$imgbutton->button_gamma = $buttonstyle_gamma;}

// Text Color (RGB)
if(isset($_REQUEST['tcr'])) {$imgbutton->text_color['red'] = $_REQUEST['tcr'];}
else {$imgbutton->text_color['red'] = hexdec(substr($buttonstyle_textcolor,1,2));}
if(isset($_REQUEST['tcg'])) {$imgbutton->text_color['green'] = $_REQUEST['tcg'];}
else {$imgbutton->text_color['green'] = hexdec(substr($buttonstyle_textcolor,3,2));}
if(isset($_REQUEST['tcb'])) {$imgbutton->text_color['blue'] = $_REQUEST['tcb'];}
else {$imgbutton->text_color['blue'] = hexdec(substr($buttonstyle_textcolor,5,2));}

// Button Dark Color (RGB)
if(isset($_REQUEST['dcr'])) {$imgbutton->button_dark_color['red'] = $_REQUEST['dcr'];}
else {$imgbutton->button_dark_color['red'] = hexdec(substr($buttonstyle_darkcolor,1,2));}
if(isset($_REQUEST['dcg'])) {$imgbutton->button_dark_color['green'] = $_REQUEST['dcg'];}
else {$imgbutton->button_dark_color['green'] = hexdec(substr($buttonstyle_darkcolor,3,2));}
if(isset($_REQUEST['dcb'])) {$imgbutton->button_dark_color['blue'] = $_REQUEST['dcb'];}
else {$imgbutton->button_dark_color['blue'] = hexdec(substr($buttonstyle_darkcolor,5,2));}

// Button Light Color (RGB)
if(isset($_REQUEST['lcr'])) {$imgbutton->button_light_color['red'] = $_REQUEST['lcr'];}
else {$imgbutton->button_light_color['red'] = hexdec(substr($buttonstyle_lightcolor,1,2));}
if(isset($_REQUEST['lcg'])) {$imgbutton->button_light_color['green'] = $_REQUEST['lcg'];}
else {$imgbutton->button_light_color['green'] = hexdec(substr($buttonstyle_lightcolor,3,2));}
if(isset($_REQUEST['lcb'])) {$imgbutton->button_light_color['blue'] = $_REQUEST['lcb'];}
else {$imgbutton->button_light_color['blue'] = hexdec(substr($buttonstyle_lightcolor,5,2));}

// Transparent Color (RGB)
if(isset($_REQUEST['trcr'])) {$imgbutton->transparent_color['red'] = $_REQUEST['trcr'];}
else {$imgbutton->transparent_color['red'] = hexdec(substr($buttonstyle_transparentcolor,1,2));}
if(isset($_REQUEST['trcg'])) {$imgbutton->transparent_color['green'] = $_REQUEST['trcg'];}
else {$imgbutton->transparent_color['green'] = hexdec(substr($buttonstyle_transparentcolor,3,2));}
if(isset($_REQUEST['trcb'])) {$imgbutton->transparent_color['blue'] = $_REQUEST['trcb'];}
else {$imgbutton->transparent_color['blue'] = hexdec(substr($buttonstyle_transparentcolor,5,2));}

//set tranparent color use
if(isset($_REQUEST['utr'])) {$imgbutton->use_transparent = $_REQUEST['utr'];}
else {$imgbutton->use_transparent = $buttonstyle_usetransparent;}

// Margin Width
if(isset($_REQUEST['mw'])) {$imgbutton->padding = $_REQUEST['mw'];}
else {$imgbutton->padding = $buttonstyle_margin;}

// Horizontal
if(isset($_REQUEST['hz'])) {$imgbutton->horizontal = $_REQUEST['hz'];}
else {$imgbutton->horizontal = $buttonstyle_horizontal;}

// use cache option
if(isset($_REQUEST['uc'])) {$imgbutton->use_cache = $_REQUEST['uc'];}
else {$imgbutton->use_cache = $buttonstyle_usecache;}

$imgbutton->use_gd2 = K_USE_GD2;

//display button
$imgbutton->F_display();

//============================================================+
// END OF FILE                                                 
//============================================================+
?>