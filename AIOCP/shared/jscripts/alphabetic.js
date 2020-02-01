//============================================================+
// File name   : alphabetic.js                             
// Begin       : 2001-09-18                                    
// Last Update : 2001-09-18                                    
//                                                             
// Description : handle Alphabetic Buttons                                    
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

imgpath = "../../images/alphabetic/";
// array of Images (for precharge):
ImgagesArray = new Array("num.gif","a.gif","b.gif","c.gif","d.gif","e.gif","f.gif","g.gif","h.gif","i.gif","j.gif","k.gif","l.gif","m.gif","n.gif","o.gif","p.gif","q.gif","r.gif","s.gif","t.gif","u.gif","v.gif","w.gif","x.gif","y.gif","z.gif","all.gif");

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
function ImageAlphaPrecharger() {
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
function DrawAlphaButton(button_num, bdescription) {

document.write('<a href="javascript:ABPushed('+button_num+')" onClick="hilite(\'nav'+button_num+'\',\''+imgpath+'on/'+ImgagesArray[button_num]+'\')" onMouseOut="hilite(\'nav'+button_num+'\',\''+imgpath+'off/'+ImgagesArray[button_num]+'\')" onMouseOver="hilite(\'nav'+button_num+'\',\''+imgpath+'over/'+ImgagesArray[button_num]+'\')"><img src="'+imgpath+'off/'+ImgagesArray[button_num]+'" width="17" height="18" border="0" alt="'+bdescription+'" name="nav'+button_num+'" /></a>');
return;
}
// --------------------------------------------------------------------------

ImageAlphaPrecharger();
// -------------------------------------------------------------------------
// END OF SCRIPT
// -------------------------------------------------------------------------