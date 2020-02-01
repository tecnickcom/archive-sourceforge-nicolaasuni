//============================================================+
// File name   : keypad.js                             
// Begin       : 2001-08-18                                    
// Last Update : 2001-08-21                                    
//                                                             
// Description : handle Keypad                                    
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

imgpath = "../../images/keypad/";
// array of Images (for precharge):
ImgagesArray = new Array("0.gif","1.gif","2.gif","3.gif","4.gif","5.gif","6.gif","7.gif","8.gif","9.gif","a.gif","back.gif","c.gif","div.gif","equal.gif","m.gif","min.gif","mult.gif","num.gif","plus.gif");

NumImg=ImgagesArray.length; //  Number of images

// --------------------------------------------------------------------------
// Swap the images
// --------------------------------------------------------------------------
function hilite(target,image) 
{
	document.images[target].src=image;
}

// --------------------------------------------------------------------------
// Precharge the images
// --------------------------------------------------------------------------
function ImagePrecharger() {
    ImagePrecharge = new Array();
	for(var i=0; i<(3*NumImg); i+=3) {
		ImagePrecharge[i] = new Image();
		ImagePrecharge[i].src = imgpath+'off/'+ImgagesArray[i];
		ImagePrecharge[i+1] = new Image();
		ImagePrecharge[i+1].src = imgpath+'on/'+ImgagesArray[i];
		ImagePrecharge[i+2] = new Image();
		ImagePrecharge[i+2].src = imgpath+'over/'+ImgagesArray[i];
	}
return;
}

// --------------------------------------------------------------------------
// Draw the button with behavior
// --------------------------------------------------------------------------
function DrawKPButton(button_num) {

document.write('<a href="javascript:NKPPushed('+button_num+')" onClick="hilite(\'nav'+button_num+'\',\''+imgpath+'on/'+ImgagesArray[button_num]+'\')" onMouseOut="hilite(\'nav'+button_num+'\',\''+imgpath+'off/'+ImgagesArray[button_num]+'\')" onMouseOver="hilite(\'nav'+button_num+'\',\''+imgpath+'over/'+ImgagesArray[button_num]+'\')"><img src="'+imgpath+'off/'+ImgagesArray[button_num]+'" width="17" height="17" border="0" alt="" name="nav'+button_num+'" /></a>');

return;
}

// --------------------------------------------------------------------------
// Draw keypad
// --------------------------------------------------------------------------
function DrawKeypad() {

document.write('<table border="0" cellspacing="0" cellpadding="0">');
document.write('<tr>');
document.write('<td>'); DrawKPButton(1); document.write('</td>');
document.write('<td>'); DrawKPButton(2); document.write('</td>');
document.write('<td>'); DrawKPButton(3); document.write('</td>');
document.write('</tr>');
document.write('<tr>');
document.write('<td>'); DrawKPButton(4); document.write('</td>');
document.write('<td>'); DrawKPButton(5); document.write('</td>');
document.write('<td>'); DrawKPButton(6); document.write('</td>');
document.write('</tr>');
document.write('<tr>');
document.write('<td>'); DrawKPButton(7); document.write('</td>');
document.write('<td>'); DrawKPButton(8); document.write('</td>');
document.write('<td>'); DrawKPButton(9); document.write('</td>');
document.write('</tr>');
document.write('<tr>');
document.write('<td>'); DrawKPButton(10); document.write('</td>');
document.write('<td>'); DrawKPButton(0); document.write('</td>');
document.write('<td>'); DrawKPButton(18); document.write('</td>');
document.write('</tr>');
document.write('</table>');
return;
}

// --------------------------------------------------------------------------

ImagePrecharger();

// -------------------------------------------------------------------------
// END OF SCRIPT
// -------------------------------------------------------------------------