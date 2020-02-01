package com.tecnick.htmlutils.htmlurls.test;

import java.net.URL;

import junit.framework.Test;
import junit.framework.TestCase;
import junit.framework.TestSuite;

import com.tecnick.htmlutils.htmlurls.HTMLURLs;

/**
 * JUnit test for HTMLStrings class.<br/><br/>
 * Copyright (c) 2004-2005 Tecnick.com S.r.l (www.tecnick.com) Via Ugo Foscolo
 * n.19 - 09045 Quartu Sant'Elena (CA) - ITALY - www.tecnick.com -
 * info@tecnick.com<br/>
 * License: http://www.gnu.org/copyleft/lesser.html LGPL
 * @author Nicola Asuni [www.tecnick.com].
 * @version 1.0.004
 */
public class HTMLURLsTest extends TestCase {
	
	protected static URL testurl; // string without HTML entities
	
	public static void main (String[] args) {
		junit.textui.TestRunner.run(suite());
	}
	
	protected void setUp() {
		testurl = HTMLURLs.setURL("http://www.tecnick.com/", "/public/code/index.php");
	}
	
	public static Test suite() {
		return new TestSuite(HTMLURLsTest.class);
	}
	
	public void testAdd() {
		assertTrue(HTMLURLs.isRelativeLink("../test.html"));
		assertTrue(HTMLURLs.isRelativeLink(""));
		assertTrue(!HTMLURLs.isRelativeLink("http://www.tecnick.com/"));
		assertTrue(HTMLURLs.resolveRelativeURL("/dir1/dir2/../dir3/dir4/../../dir5/test.html").compareTo("/dir1/dir5/test.html") == 0);
		assertTrue(testurl.toString().compareTo("http://www.tecnick.com/public/code/index.php") == 0);
	}
}

