//============================================================+
// File name   : inserttag.js
// Begin       : 2001-10-25
// Last Update : 2007-02-10
// 
// Description : Insert TAGS on Textarea Form (XHTML)
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

/**
 * save the text history for undo/redo functions
 */
var text_history = new Array();

/**
 * current text history index
 */
var thid = 0;

/**
 * max text history index
 */
var maxthid = 0;

/**
 * current selection (for IE only)
 */
var txtsel = null;

/**
 * selection start
 */
var posStart;

/**
 * selection end
 */
var posEnd;

/**
 * Creates open and close tags and call display tag.
 * Use '&' as first tag character to obtain also a closed tag.
 * @param editText string text to be edited
 * @param tag string element to be added
 */
function FJ_insert_tag(editText, tag) {
	var opentag = tag;
	var closetag = '';
	
	if (tag.charAt(opentag.length-2) != '/') {
		tmpstr = opentag.split(' ');
		if(opentag.charAt(0)=='<'){ //XHTML tag
			var closetag = '</'+tmpstr[0].substring(1,(tmpstr[0].length));
			if (closetag.charAt(closetag.length-1)!='>') {closetag += '>';} //HTML style close tag
		}
		else{ //custom tag
			var closetag = '[/'+tmpstr[0].substring(1,(tmpstr[0].length));
			if (closetag.charAt(closetag.length-1)!=']') {closetag += ']';} //custom code style close tag
		}
	}
	FJ_display_tag(editText, opentag, closetag);
	return;
}

/**
 * Insert text before selected text or at the end of text.
 * @param editText string text to be edited
 * @param newtext string text to be added
 */
function FJ_insert_text(editText, newtext) {
	FJ_display_tag(editText, newtext, '');
	return;
}

/**
 * Insert open and close TAG on selected text.
 * @param editText string text to be edited
 * @param opentag string opening element to be added
 * @param closetag string closing element to be added
 */
function FJ_display_tag(editText, opentag, closetag) {
	// save previous text on history
	text_history[thid] = editText.value;
	thid++;
	if (editText.createTextRange && document.selection) { // if text has been selected (only IE browser)
		if (txtsel != null) {
			// uses always the last selection...
			txtsel = txtsel.duplicate();
			var sellen = 0; // selection length
			if (txtsel.text.length > 0) {
				sellen = txtsel.text.length + opentag.length + closetag.length;
				txtsel.text = opentag + '' + txtsel.text + '' + closetag;
			} else {
				editText.value = editText.value + '' + opentag + '' + closetag;
			}
			
			// restore selection
			txtsel.moveStart("character", - sellen);      
			txtsel.select();
		} else {
			editText.value = editText.value + '' + opentag + '' + closetag;
		}
	} else if (window.getSelection && editText.setSelectionRange) { // MOZ
		posStart = editText.selectionStart;
		posEnd = editText.selectionEnd;
		editText.value = editText.value.substr(0, posStart) + '' + opentag + '' + editText.value.substr(posStart, posEnd-posStart) + '' + closetag + '' + editText.value.substr(posEnd);
		// renew selection range
		editText.setSelectionRange(posStart, (opentag.length * 2 + 1 + posEnd));
	} else { //text has not been selected or incompatible browser
		editText.value = editText.value + '' + opentag + '' + closetag;
	}
	// save current text on history
	text_history[thid] = editText.value;
	maxthid = thid;
	editText.focus();
	return;
}

/**
 * UNDO
 * Restore the text previous to last tag insert.
 * @since 3.0.008 (2006-05-13)
 * @param editText string text to be edited
 */
function FJ_undo(editText) {
	// undo
	if (thid > 0) {
		thid--;
		editText.value = text_history[thid];
	}
	return;
}

/**
 * REDO
 * Redoe the last tag insert.
 * @since 3.0.008 (2006-05-13)
 * @param editText string text to be edited
 */
function FJ_redo(editText) {
	if (thid < maxthid) {
		thid++;
		editText.value = text_history[thid];
	}
	return;
}

/**
 * Tracks selection changes.
 * Preserve selection on IE
 * @since 3.0.008 (2006-05-13)
 * @param editText string text to be edited
 */
function FJ_update_selection(editText) {
	if (editText.createTextRange && document.selection) {
		txtsel = document.selection.createRange();
	}
	return;
}

/**
 * Replace new line with HTML equivalent
 * @param editText string text to be edited
 */
function FJ_auto_br(editText) {
	editText.value  = editText.value.replace(new RegExp('[\r\n]', 'gi'), "<br />\r\n");
	return;
}

/**
 * Compact code (remove tabs and newlines)
 * @param editText string text to be edited
 */
function FJ_remove_indentation(editText) {
	editText.value = editText.value.replace(new RegExp('[\r\n]+[\t]', 'gi'), "\t");
	editText.value = editText.value.replace(new RegExp('[\t]', 'gi'), "");
	editText.value = editText.value.replace(new RegExp('[>][\r\n]+[<]', 'gi'), "><");
	return;
}

/**
 * Only works with IE (account for text selection)
 * @param editText string text to be edited
 */
function FJ_store_caret(editText) {
	if (editText.createTextRange) {
		editText.caretPos = document.selection.createRange().duplicate();
	}
}

// -------------------------------------------------------------------------
// END OF SCRIPT
// -------------------------------------------------------------------------