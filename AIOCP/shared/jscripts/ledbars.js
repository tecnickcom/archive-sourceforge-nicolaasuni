//============================================================+
// File name   : ledbars.js                             
// Begin       : 2001-08-26                                    
// Last Update : 2001-08-26                                    
//                                                             
// Description : handle led bars                                    
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

// Constants / Variables:
// -------------------------------------------------------------------------

var lbnum=0; //unique identifier for each bar;
var lbarprop = new Array(); //remember each bar proprieties
LedArray = new Array(); // array of Displays

ledsimgpath = "../../images/led/";
offpath = "off/";
onpath = "on/";

LedTypesArray = new Array ("hsquare/","vsquare/","round/");
WidthLedArray = new Array (16,10,17);
HeightLedArray = new Array (10,16,17);
LedColorsArray = new Array ("b.gif","g.gif","y.gif","r.gif");
// --------------------------------------------------------------------------
// Swap the images
// --------------------------------------------------------------------------
function shilite(target,image) {
	document.images[target].src=image;
}

// --------------------------------------------------------------------------
//  Create horizontal led bar
//  ltype=refer to LedTypesArray (0,1,2); 
//  bnum=number of the bar (unique identifier);
//  n0= number of leds of type 0...; 
// --------------------------------------------------------------------------
function CreateHLedDisplay(ltype, n0, n1, n2, n3)
{
  LedArray[lbnum] = new Array(); //digits
  lbarprop[lbnum] = new Array (n0, n1, n2, n3,(n0+n1+n2+n3),ltype);
  var x=0;
  
  	for(j=0; j<4; j++) {
		for(i=0; i<lbarprop[lbnum][j]; i++) {
			lbn = "led"+lbnum+"_"+j+"_"+i; //name of each single image
  			document.write('<img name=\"'+lbn+'\" src=\"'+ledsimgpath+''+LedTypesArray[ltype]+''+offpath+''+LedColorsArray[j]+'\" width=\"'+WidthLedArray[ltype]+'\" height=\"'+HeightLedArray[ltype]+'\" border=\"0\" alt=\"\" />');
  			LedArray[lbnum][x++] = document.images[document.images.length-1];
		}
	}
 lbnum++;
 return x;
}

// --------------------------------------------------------------------------
//  Create vertical led bar
//  ltype=refer to LedTypesArray (0,1,2); 
//  bnum=number of the bar (unique identifier);
//  n0= number of leds of type 0...; 
// --------------------------------------------------------------------------
function CreateVLedDisplay(ltype, n0, n1, n2, n3)
{
  LedArray[lbnum] = new Array(); //digits
  lbarprop[lbnum] = new Array (n0, n1, n2, n3,(n0+n1+n2+n3),ltype);
  var x=lbarprop[lbnum][4]-1;
  
  	for(j=3; j>=0; j--) {
		for(i=lbarprop[lbnum][j]-1; i>=0; i--) {
			lbn = "led"+lbnum+"_"+j+"_"+i; //name of each single image
  			document.write('<img name=\"'+lbn+'\" src=\"'+ledsimgpath+''+LedTypesArray[ltype]+''+offpath+''+LedColorsArray[j]+'\" width=\"'+WidthLedArray[ltype]+'\" height=\"'+HeightLedArray[ltype]+'\" border=\"0\" alt=\"\" /><br />');
  			LedArray[lbnum][x--] = document.images[document.images.length-1];
		}
	}
 lbnum++;
 return x;
}

// --------------------------------------------------------------------------
//  Show value in led bar
// --------------------------------------------------------------------------
function ShowLedValue(lbarnum, number, scale) {
	//number of led that be turned on:
	onleds = ((number * lbarprop[lbarnum][4]) / scale);
	var x=0;
	
    for(j=0; j<4; j++) {
		for(i=0; i<lbarprop[lbarnum][j]; i++) {
			if (x<onleds){ //put on leds
				LedArray[lbarnum][x].src = ledsimgpath+''+LedTypesArray[lbarprop[lbarnum][5]]+''+onpath+''+LedColorsArray[j];
			}
			else{ //put off the remaining leds
				LedArray[lbarnum][x].src = ledsimgpath+''+LedTypesArray[lbarprop[lbarnum][5]]+''+offpath+''+LedColorsArray[j];
			}
		x++;
		}
	}
 return;
}
// --------------------------------------------------------------------------
//  END OF SCRIPT
// --------------------------------------------------------------------------