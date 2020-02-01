package com.tecnick.htmlutils.htmlentities.test;

import junit.framework.*;
import com.tecnick.htmlutils.htmlentities.HTMLEntities;

/**
 * JUnit test for HTMLEntities class.<br/><br/>
 * Copyright (c) 2004-2005 Tecnick.com S.r.l (www.tecnick.com) Via Ugo Foscolo
 * n.19 - 09045 Quartu Sant'Elena (CA) - ITALY - www.tecnick.com -
 * info@tecnick.com<br/>
 * License: http://www.gnu.org/copyleft/lesser.html LGPL
 * @author Nicola Asuni [www.tecnick.com].
 * @version 1.0.004
 */
public class HTMLEntitiesTest extends TestCase {
	
	protected static String rawstr; // string without HTML entities
	protected static String htmlstr; // string with HTML entities
	
	public static void main (String[] args) {
		junit.textui.TestRunner.run(suite());
	}
	
	protected void setUp() {
		rawstr = new String("test string: <b>àèìòù€<b>");
		htmlstr = new String("test string: <b>&agrave;&egrave;&igrave;&ograve;&ugrave;&euro;<b>");
	}
	
	public static Test suite() {
		return new TestSuite(HTMLEntitiesTest.class);
	}
	
	public void testAdd() {
		assertTrue(HTMLEntities.htmlentities(rawstr).compareTo(htmlstr) == 0);
		assertTrue(HTMLEntities.unhtmlentities("&euro;&#8364;&#x20AC;").compareTo("€€€") == 0);
		assertTrue(HTMLEntities.htmlSingleQuotes("'").compareTo("&rsquo;") == 0);
		assertTrue(HTMLEntities.unhtmlSingleQuotes("&rsquo;").compareTo("'") == 0);
		assertTrue(HTMLEntities.htmlDoubleQuotes("\"").compareTo("&quot;") == 0);
		assertTrue(HTMLEntities.unhtmlDoubleQuotes("&quot;").compareTo("\"") == 0);
		assertTrue(HTMLEntities.htmlQuotes("'\"").compareTo("&rsquo;&quot;") == 0);
		assertTrue(HTMLEntities.unhtmlQuotes("&rsquo;&quot;").compareTo("'\"") == 0);
		assertTrue(HTMLEntities.htmlAngleBrackets("<>").compareTo("&lt;&gt;") == 0);
		assertTrue(HTMLEntities.unhtmlAngleBrackets("&lt;&gt;").compareTo("<>") == 0);
		assertTrue(HTMLEntities.htmlAmpersand("&").compareTo("&amp;") == 0);
		assertTrue(HTMLEntities.unhtmlAmpersand("&amp;").compareTo("&") == 0);
	}
}

