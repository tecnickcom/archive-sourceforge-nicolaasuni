//============================================================+
// File name   : navigator.js                             
// Begin       : 2001-08-19                                    
// Last Update : 2001-09-05                                    
//                                                             
// Description : handle Navigator Buttons                                    
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

imgpath = "../../images/navigator/";
// array of Images (for precharge):
ImgagesArray = new Array("first.gif","fast_back.gif","back.gif","up.gif","down.gif","forward.gif","fast_forward.gif","last.gif","record.gif","pause.gif","stop.gif","x.gif");

NumImg=ImgagesArray.length; //  Number of images


// --------------------------------------------------------------------------
// Swap the images
// --------------------------------------------------------------------------
function hilite(target,image) {
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
function DrawButton(button_num, bdescription) {

document.write('<a href="javascript:NBPushed('+button_num+')" onClick="hilite(\'nav'+button_num+'\',\''+imgpath+'on/'+ImgagesArray[button_num]+'\')" onMouseOut="hilite(\'nav'+button_num+'\',\''+imgpath+'off/'+ImgagesArray[button_num]+'\')" onMouseOver="hilite(\'nav'+button_num+'\',\''+imgpath+'over/'+ImgagesArray[button_num]+'\')"><img src="'+imgpath+'off/'+ImgagesArray[button_num]+'" width="20" height="17" border="0" alt="'+bdescription+'" name="nav'+button_num+'" /></a>');
return;
}
// --------------------------------------------------------------------------

ImagePrecharger();
// -------------------------------------------------------------------------
// END OF SCRIPT
// -------------------------------------------------------------------------