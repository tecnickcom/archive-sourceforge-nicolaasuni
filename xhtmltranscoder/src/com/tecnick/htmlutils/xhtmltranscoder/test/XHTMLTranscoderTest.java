package com.tecnick.htmlutils.xhtmltranscoder.test;

import junit.framework.Test;
import junit.framework.TestCase;
import junit.framework.TestSuite;

import com.tecnick.htmlutils.xhtmltranscoder.XHTMLTranscoder;

/**
 * JUnit test for HTMLEntities class.<br/><br/>
 * Copyright (c) 2004-2005 Tecnick.com S.r.l (www.tecnick.com) Via Ugo Foscolo
 * n.19 - 09045 Quartu Sant'Elena (CA) - ITALY - www.tecnick.com -
 * info@tecnick.com<br/>
 * License: http://www.gnu.org/copyleft/lesser.html LGPL
 * @author Nicola Asuni [www.tecnick.com].
 * @version 1.0.007
 */
public class XHTMLTranscoderTest extends TestCase {
	
	XHTMLTranscoder xhtml_transcoder;
	
	public static void main (String[] args) {
		junit.textui.TestRunner.run(suite());
	}
	
	protected void setUp() {
		xhtml_transcoder = new XHTMLTranscoder();
	}
	
	public static Test suite() {
		return new TestSuite(XHTMLTranscoderTest.class);
	}
	
	public void testAdd() {
		assertTrue(xhtml_transcoder.transcode("<B>bold</B>").compareTo("<b>bold</b>") == 0);
		assertTrue(xhtml_transcoder.transcode("<a HREF=\"http://www.tecnick.com\">link</a>").compareTo("<a href=\"http://www.tecnick.com\">link</a>") == 0);
		assertTrue(xhtml_transcoder.transcode("<b>bold<i>italic</b></i>").compareTo("<b>bold<i>italic</i></b>") == 0);
		assertTrue(xhtml_transcoder.transcode("<b>bold<i>italic</b>").compareTo("<b>bold<i>italic</i></b>") == 0);
		assertTrue(xhtml_transcoder.transcode("<a href=http://www.tecnick.com>link</a>").compareTo("<a href=\"http://www.tecnick.com\">link</a>") == 0);
		assertTrue(xhtml_transcoder.transcode("<input type=\"checkbox\" checked />").compareTo("<input type=\"checkbox\" checked=\"checked\" />") == 0);
		assertTrue(xhtml_transcoder.transcode("<br>").compareTo("<br />") == 0);
		assertTrue(xhtml_transcoder.transcode("<?php\necho \"Hello World!\"\n?>").compareTo("<?php\necho \"Hello World!\"\n?>") == 0);
		assertTrue(xhtml_transcoder.transcode("&euro; & è", true, false, "iso-8859-1").compareTo("&euro; &amp; &egrave;") == 0);
	}
}

