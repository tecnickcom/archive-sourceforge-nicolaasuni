package com.tecnick.htmlutils.xhtmltranscoder.sample;

import com.tecnick.htmlutils.xhtmltranscoder.XHTMLTranscoder;

/**
 * Implementation example of HTMLEntities class.<br/><br/>
 * Copyright (c) 2004-2005 Tecnick.com S.r.l (www.tecnick.com) Via Ugo Foscolo
 * n.19 - 09045 Quartu Sant'Elena (CA) - ITALY - www.tecnick.com -
 * info@tecnick.com<br/>
 * License: http://www.gnu.org/copyleft/lesser.html LGPL
 * @author Nicola Asuni [www.tecnick.com].
 * @version 1.0.007
 */
public class XHTMLTranscoderSample {
	
	/**
	 * Prints some strings on System.out
	 * @param args String[]
	 */
	public static void main(String[] args) {
		// initialize Transcoder
		XHTMLTranscoder xhtml_transcoder = new XHTMLTranscoder();
		// create a dummy HMTML string
		String html_string = "<B CLASS=test>put<I>here some broken HTML</B></I>";
		// transcode the string to XHTML
		String xhtml_string = xhtml_transcoder.transcode(html_string);
		// print output
		System.out.println(xhtml_string);
		
		// further examples
		System.out.println("\ntags (elements) names in lowercase:");
		System.out.println("<B>bold</B> ==> " + xhtml_transcoder.transcode("<B>bold</B>"));
		
		System.out.println("\nattributes names in lowercase:");
		System.out.println("<a HREF=\"http://www.tecnick.com\">link</a> ==> " + xhtml_transcoder.transcode("<a HREF=\"http://www.tecnick.com\">link</a>"));
		
		System.out.println("\nelements nesting:");
		System.out.println("<b>bold<i>italic</b></i> ==> " + xhtml_transcoder.transcode("<b>bold<i>italic</b></i>"));
		
		System.out.println("\nelements termination:");
		System.out.println("<b>bold<i>italic<b>bold2</b> ==> " + xhtml_transcoder.transcode("<b>bold<i>italic<b>bold2</b>"));
		
		System.out.println("\nunquoted attributes:");
		System.out.println("<a href=http://www.tecnick.com>link</a> ==> " + xhtml_transcoder.transcode("<a href=http://www.tecnick.com>link</a>"));
		
		System.out.println("\nunminimized attributes:");
		System.out.println("<input type=\"checkbox\" checked /> ==> " + xhtml_transcoder.transcode("<input type=\"checkbox\" checked />"));
		
		System.out.println("\nunterminated empty tags:");
		System.out.println("<br> ==> " + xhtml_transcoder.transcode("<br>"));
		
		System.out.println("\npreserve other languages elements (php, asp, jsp, ...):");
		System.out.println("<?php\necho \"Hello World!\"\n?>\n ==> \n" + xhtml_transcoder.transcode("<?php\necho \"Hello World!\"\n?>"));
		
		System.out.println("\nExtended characters with entities_off = false:");
		System.out.println("\"' &quot; € &euro; &#8364; &#x20AC; & &amp; ==> " + xhtml_transcoder.transcode("\"' &quot; € &euro; &#8364; &#x20AC; & &amp;", true, false, "UTF-8"));
		
		System.out.println("\nExtended characters with entities_off = true:");
		System.out.println("\"' &quot; € &euro; &#8364; &#x20AC; & &amp; ==> " + xhtml_transcoder.transcode("\"' &quot; € &euro; &#8364; &#x20AC; & &amp;", true, true, "UTF-8"));
	}	  
}
