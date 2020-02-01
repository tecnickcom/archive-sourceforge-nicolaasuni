<?php
//============================================================+
// File name   : cp_functions_htmlcolorpicker.php              
// Begin       : 2001-11-05                                    
// Last Update : 2002-09-29                                    
//                                                             
// Description : HTML Color Picker Functions                   
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
// Display Color Picker
// ------------------------------------------------------------
function F_html_color_picker($callingform, $callingfield) {
	global $l, $db;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	require_once('../../shared/code/cp_functions_form.'.CP_EXT);
	require_once('../../shared/config/cp_colors.'.CP_EXT);
	
	$alink = ""; //remove link for dhtml wsiwyg editor call
	if($callingform AND $callingfield) {
		$alink = "href=\"javascript:void(0)\""; //nestscape compatibility
	}
?>

<a <?php echo $alink; ?> onclick="FJ_pick_color(0); document.form_colorpicker.colorname.selectedIndex=0;">
<img src="../../images/colors/color_table.jpg" alt="" name="colorboard" id="colorboard" width="320" height="300" hspace="0" vspace="0" border="0" /></a>

<!-- WEB safe colors -->
<table border="0" cellspacing="1" cellpadding="0">
<?php
$i = 1;
reset($webcolor);
while(list($key, $val) = each($webcolor)) { // for each color in table
	if ($i == 1) {
		echo "<tr>";
	}
	echo "<td bgcolor=\"".$key."\"><a ".$alink." onclick=\"document.form_colorpicker.CSELECTED.value='".$key."';FJ_pick_color(1);document.form_colorpicker.colorname.selectedIndex=0;\"><img src=\"../../images/spacer.gif\" alt=\"".$val."\" width=\"12\" height=\"8\" border=\"0\" /></a></td>\n";
	if ($i == 23) {
		echo "</tr>";
		$i = 0;
	}
	$i++;
}
?>
</table>

<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_colorpicker" id="form_colorpicker">

<table class="edge" border="0" cellspacing="1" cellpadding="2" >

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<tr class="fill">
	<th class="fill">&nbsp;</th>
	<th class="fill">R</th>
	<th class="fill">G</th>
	<th class="fill">B</th>
	<th class="fill">&nbsp;</th>
	<th class="fill">&nbsp;</th>
</tr>
<tr class="fill">
	<td class="fill">DEC</td>
	<td class="fill"><input type="text" name="RED" id="RED" size="3" maxlength="3" /></td>
	<td class="fill"><input type="text" name="GREEN" id="GREEN" size="3" maxlength="3" /></td>
	<td class="fill"><input type="text" name="BLUE" id="BLUE" size="3" maxlength="3" /></td>
	<td class="fill" colspan="2">
	<!-- Color names ==================== -->
<select name="colorname" id="colorname" size="0" onchange="document.form_colorpicker.CSELECTED.value=document.form_colorpicker.colorname.options[document.form_colorpicker.colorname.selectedIndex].value; FJ_pick_color(1);">
<option value="">- <?php echo $l['w_color']; ?> -</option>
<?php
reset($webcolor);
while(list($key, $val) = each($webcolor)) { // for each color in table
	echo "<option value=\"".$key."\">".$val."</option>\n";
}
?>
</select>
</td>
</tr>

<tr class="fill">
	<td class="fill">HEX</td>
	<td class="fill"><input type="text" name="HRED" id="HRED" size="3" maxlength="2" /></td>
	<td class="fill"><input type="text" name="HGREEN" id="HGREEN" size="3" maxlength="2" /></td>
	<td class="fill"><input type="text" name="HBLUE" id="HBLUE" size="3" maxlength="2" /></td>
	<td class="fill"><input type="text" name="CSELECTED" id="CSELECTED" size="10" maxlength="7" value="<?php echo $color; ?>" onchange="FJ_pick_color(1); document.form_colorpicker.colorname.selectedIndex=0;" /></td>
	<td class="fill" align="center"><div id="pickedcolor" style="position:relative; visibility:visible">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div></td>
</tr>

</table>

</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<?php F_generic_button("cancel",$l['w_cancel'],"window.close()"); ?>
<?php 
if ($callingform AND $callingfield) {
	F_generic_button("pastecolor", $l['w_submit'], "window.opener.document.".$callingform.".".$callingfield.".value=document.form_colorpicker.CSELECTED.value; window.close()"); 
}
else {
	F_generic_button("pastecolor", $l['w_submit'], "window.returnValue=document.form_colorpicker.CSELECTED.value; window.close()");
}
?>
</td>
</tr>
</table>
</form>

<script language="JavaScript" type="text/javascript">
//<![CDATA[
// variables
// ------------------------------------------------------------
var nnbrowser = window.Event ? true : false; //check netscape browser
var Xpos, Ypos;
var Red, Green, Blue;
var hexChars = "0123456789ABCDEF";

// ------------------------------------------------------------
// capture event
// ------------------------------------------------------------
if(nnbrowser) {// Netscape
	document.captureEvents(Event.MOUSEMOVE);
}

document.onmousemove = FJ_get_coordinates;

// ------------------------------------------------------------
// Get cursor coordinates and store on Xpos and Ypos variables
// ------------------------------------------------------------
function FJ_get_coordinates(e) {
  if(nnbrowser) { // Netscape
      Xpos = e.pageX;
      Ypos = e.pageY;
  } 
  else { // IE
      Xpos = (event.clientX + document.body.scrollLeft);
      Ypos = (event.clientY + document.body.scrollTop);
  }
  
  //calculate color
  if(Xpos<=50){
  	Red=255;
	Green=Math.round(Xpos * 5.1);
	Blue=0;
  }
  else if(Xpos<=100){
  	Red=255-Math.round((Xpos-50) * 5.1);
	Green=255;
	Blue=0;
  }
  else if(Xpos<=150){
  	Red=0;
	Green=255;
	Blue=Math.round((Xpos-100) * 5.1);
  }
  else if(Xpos<=200){
  	Red=0;
	Green=255-Math.round((Xpos-150) * 5.1);
	Blue=255;
  }
  else if(Xpos<=250){
  	Red=Math.round((Xpos-200) * 5.1);
	Green=0;
	Blue=255;
  }
  else if(Xpos<=300){
  	Red=255;
	Green=0;
	Blue=255-Math.round((Xpos-250) * 5.1);
  }
  else if(Xpos<=320){ //grey scale
	light = Math.round((1-(Ypos/300))*255);
	Red=light;
	Green=light;
	Blue=light;
  }
  
  // change luminosity
	if((Xpos>=0)&&(Xpos<=300)&&(Ypos>=0)&&(Ypos<=300)) {
		light = Math.round((1-(Ypos/150))*255);
		Red += light; 
		if(Red>255) {Red=255;}
		else if(Red<0) {Red=0;}
		Green += light;
		if(Green>255) {Green=255;}
		else if(Green<0) {Green=0;}
		Blue += light;
		if(Blue>255) {Blue=255;}
		else if(Blue<0) {Blue=0;}
	} 
  
  // display color
	if((Xpos>=0)&&(Xpos<=320)&&(Ypos>=0)&&(Ypos<=300)) {
		document.form_colorpicker.RED.value = Red;
		document.form_colorpicker.GREEN.value = Green;
		document.form_colorpicker.BLUE.value = Blue;
	
		document.form_colorpicker.HRED.value = FJ_dec_to_hex(Red);
		document.form_colorpicker.HGREEN.value = FJ_dec_to_hex(Green);
		document.form_colorpicker.HBLUE.value = FJ_dec_to_hex(Blue);
	}


	return;
}

// ------------------------------------------------------------
// calculate color from coordinates
// manual=1 means color introduced by keyboard
// ------------------------------------------------------------
function FJ_pick_color(manual) {
	if((manual)||((Xpos<=320)&&(Ypos<=300))) { //check if coordinates are valid
		
		if(!manual) {
			document.form_colorpicker.CSELECTED.value = '#'+document.form_colorpicker.HRED.value+''+document.form_colorpicker.HGREEN.value+''+document.form_colorpicker.HBLUE.value;
		}
		
		newcolor = document.form_colorpicker.CSELECTED.value;
		
		//show selected color on picked color layer
		// check browser capabilities
		if(document.layers){                   
			document.layers['pickedcolor'].bgColor=newcolor;         
		}         
		if(document.all){      
			document.all.pickedcolor.style.backgroundColor=newcolor;  
		}        
		if(!document.all && document.getElementById){               
			document.getElementById("pickedcolor").style.backgroundColor=newcolor;           
		}
	}
	return;
}

// ------------------------------------------------------------
// convert decimal value to hexadecimal (FF is the max value)
// ------------------------------------------------------------
function FJ_dec_to_hex (Dec) {
	var a = Dec % 16; 
	var b = (Dec - a)/16; 
	hex = hexChars.charAt(b)+""+hexChars.charAt(a); 
	return hex; 
}

// ------------------------------------------------------------
//get color value from calling document
// ------------------------------------------------------------
<?php if($callingform AND $callingfield) { ?>
var passedcolor = window.opener.document.<?php echo "".$callingform.".".$callingfield.""; ?>.value;
if (passedcolor) {
	document.form_colorpicker.CSELECTED.value = passedcolor;
}
FJ_pick_color(1);
<?php } ?>

//]]>
</script>

<!-- ====================================================== -->
<?php 
return;
}
?>

<?php
//============================================================+
// END OF FILE                                                 
//============================================================+
?>
