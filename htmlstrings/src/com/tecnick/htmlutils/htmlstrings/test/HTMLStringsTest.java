package com.tecnick.htmlutils.htmlstrings.test;

import junit.framework.Test;
import junit.framework.TestCase;
import junit.framework.TestSuite;

import com.tecnick.htmlutils.htmlstrings.HTMLStrings;

/**
 * JUnit test for HTMLStrings class.<br/><br/>
 * Copyright (c) 2004-2005 Tecnick.com S.r.l (www.tecnick.com) Via Ugo Foscolo
 * n.19 - 09045 Quartu Sant'Elena (CA) - ITALY - www.tecnick.com -
 * info@tecnick.com<br/>
 * License: http://www.gnu.org/copyleft/lesser.html LGPL
 * @author Nicola Asuni [www.tecnick.com].
 * @version 1.0.004
 */
public class HTMLStringsTest extends TestCase {
	
	protected static String teststr;
	
	public static void main (String[] args) {
		junit.textui.TestRunner.run(suite());
	}
	
	protected void setUp() {
		teststr = new String("\nline one\nline two\nline three\nàèìòù");
	}
	
	public static Test suite() {
		return new TestSuite(HTMLStringsTest.class);
	}
	
	public void testAdd() {
		assertTrue(HTMLStrings.compactString(teststr).compareTo(" line one line two line three àèìòù") == 0);
		assertTrue(HTMLStrings.autoBR(teststr).compareTo("<br/>\nline one<br/>\nline two<br/>\nline three<br/>\nàèìòù") == 0);
		assertTrue(HTMLStrings.getEncodedString(teststr, "UTF-8", "ISO-8859-1").compareTo("\nline one\nline two\nline three\nàèìòù") == 0);
	}
}

