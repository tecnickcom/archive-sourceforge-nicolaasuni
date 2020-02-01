//============================================================+
// File name   : menutree.js
// Begin	   : 2004-03-31
// Last Update : 2004-03-31
// 
// Description : Converts an unordered list to an 
//               explorer-style tree, with clickable icons.
//               To make this work, simply add one line to your HTML:
//               <script language="JavaScript" type="text/javascript" src="jscripts/menutree.js"></script>			  
//               and then make the top UL of your nested unordered list of class "menutree".
//
// Authors: Nicola Asuni, 
//          based on menutree by Stuart Langridge (sil@kryogenix.org), November 2002 
//          Inspired by Aaron's labels.js (http://youngpup.net/demos/labels/) and 
//          Dave Lindquist's menuDropDown.js (http://www.gazingus.org/dhtml/?id=109)
//
// (c) Copyright:
//               Tecnick.com LTD
//               Manor Coach House, Church Hill
//               Aldershot, Hants, GU12 4RQ
//               UK
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

// set CSS classes names
var mt_class_name = "altmenu";
var mt_class_open = "itemopen";
var mt_class_closed = "itemclosed";
var mt_class_bullet = "itembullet";

addEvent(window, "load", makeTreesC);

function makeTreesC() {
	// We don't actually need createElement, but we do
	// need good DOM support, so this is a good check.
	if (!document.createElement) return;
	
	uls = document.getElementsByTagName("ul");
	for (uli = 0; uli < uls.length; uli++) {
		ul = uls[uli];
		if ((ul.nodeName == "UL") && (ul.className == mt_class_name)) {
			processULELC(ul);
		}
	}
}

function processULELC(ul) {
	if ((!ul.childNodes) || (ul.childNodes.length == 0)) {
		return;
	}
	
	// Iterate LIs
	for (var itemi = 0; itemi < ul.childNodes.length; itemi++) {
		var item = ul.childNodes[itemi];
		if (item.nodeName == "LI") {
			// Iterate things in this LI
			var a;
			var subul;
			subul = "";
			for (var sitemi = 0; sitemi < item.childNodes.length; sitemi++) {
				var sitem = item.childNodes[sitemi];
				switch (sitem.nodeName) {
					case "A": {
						a = sitem; 
						break;
					}
					case "UL": {
						subul = sitem; 
						processULELC(subul);
						break;
					}
				}
			}
			if (subul) {
				associateELC(a,subul);
			} else {
				a.parentNode.className = mt_class_bullet;
			}
		}
	}
}

function associateELC(a,ul) {
	if (a.parentNode.className.indexOf(mt_class_open) == -1) {
		a.parentNode.className = mt_class_closed;
	}
	a.onclick = function () {
		this.parentNode.className = (this.parentNode.className == mt_class_open) ? mt_class_closed : mt_class_open;
		return false;
	}
}

/**
* Adds an eventListener for browsers which support it.
* Written by Scott Andrew.
*/
function addEvent(obj, evType, fn){

  if (obj.addEventListener) {
	obj.addEventListener(evType, fn, true);
	return true;
  } else if (obj.attachEvent) {
	var r = obj.attachEvent("on"+evType, fn);
	return r;
  } else {
	return false;
  }
}

//============================================================+
// END OF FILE                                                 
//============================================================+