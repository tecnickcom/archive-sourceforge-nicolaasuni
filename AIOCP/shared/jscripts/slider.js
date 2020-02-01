//============================================================+
// File name   : slider.js                             
// Begin       : 2001-08-20                                    
// Last Update : 2001-08-20                                    
//                                                             
// Description : handle Sliders                                    
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
//============================================================+

// --------------------------------------------------------------------------
// Variables
// --------------------------------------------------------------------------

var sldnum=0; //unique identifier for each slider
sldcurpos = new Array(); // array of sliders
sldcurprop = new Array(); // //remember each dslider proprieties

HCursorImage = "../../images/sliders/hcursor.gif";
VCursorImage = "../../images/sliders/vcursor.gif";
Hbar = "../../images/sliders/hmarkedbar.gif";
Vbar = "../../images/sliders/vmarkedbar.gif";

// --------------------------------------------------------------------------
// Swap the images
// --------------------------------------------------------------------------
function shilite(target,image) {
	document.images[target].src=image;
}

// --------------------------------------------------------------------------
// Draw Horizontal Slider
// slength=slide length, pos=initial cursor position
// --------------------------------------------------------------------------
function DrawHSlide(slength, pos) {

	sldcurpos[sldnum] = new Array();
	sldcurprop[sldnum] = slength;
	
	for(i=0;i<slength;i++) {
		spn = "s"+sldnum+"_"+i; //name of each single image
			if (i!=pos) {
				document.write("<a href=\"javascript:SPushed(\'"+spn+"\',"+i+")\" onClick=\"MoveHCursor("+sldnum+","+i+")\"><img src=\""+Hbar+"\" width=\"11\" height=\"21\" border=\"0\" alt=\"\" name=\""+spn+"\" /></a>");
			}
			else {
				document.write("<a href=\"javascript:SPushed(\'"+spn+"\',"+i+")\" onClick=\"MoveHCursor("+sldnum+","+i+")\"><img src=\""+HCursorImage+"\" width=\"11\" height=\"21\" border=\"0\" alt=\"\" name=\""+spn+"\" /></a>");
			}
	}
sldnum++;
return;
}
// --------------------------------------------------------------------------
// Draw vertical Slider
// slength=slide length, pos=initial cursor position
// --------------------------------------------------------------------------
function DrawVSlide(slength, pos) {

	sldcurpos[sldnum] = new Array();
	sldcurprop[sldnum] = slength;
	
	for(i=slength-1;i>=0;i--) {
		spn = "s"+sldnum+"_"+i; //name of each single image
			if (i!=pos) {
				document.write("<a href=\"javascript:SPushed(\'"+spn+"\',"+i+")\" onClick=\"MoveVCursor("+sldnum+","+i+")\"><img src=\""+Vbar+"\" width=\"21\" height=\"11\" border=\"0\" alt=\"\" name=\""+spn+"\" /></a><br />");
			}
			else {
				document.write("<a href=\"javascript:SPushed(\'"+spn+"\',"+i+")\" onClick=\"MoveVCursor("+sldnum+","+i+")\"><img src=\""+VCursorImage+"\" width=\"21\" height=\"11\" border=\"0\" alt=\"\" name=\""+spn+"\" /></a><br />");
			}
	}
sldnum++;
return;
}
// --------------------------------------------------------------------------
// Put horizontal cursor in the right position
// --------------------------------------------------------------------------
function MoveHCursor(snum, pos) 
{
    slength = sldcurprop[snum];
	
	for(j=0;j<slength;j++) {
	   itarget = "s"+snum+"_"+j; 
	   shilite(itarget, Hbar);
	}
	itarget = "s"+snum+"_"+pos; 
	shilite(itarget, HCursorImage);
return;
}
// --------------------------------------------------------------------------
// Put vertical cursor in the right position
// --------------------------------------------------------------------------
function MoveVCursor(snum, pos) 
{
    slength = sldcurprop[snum];
	
	for(j=0;j<slength;j++) {
	   itarget = "s"+snum+"_"+j; 
	   shilite(itarget, Vbar);
	}
	itarget = "s"+snum+"_"+pos; 
	shilite(itarget, VCursorImage);
return;
}

// -------------------------------------------------------------------------
// END OF SCRIPT
// -------------------------------------------------------------------------