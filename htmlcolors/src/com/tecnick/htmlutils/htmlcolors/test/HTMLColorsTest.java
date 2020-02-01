package com.tecnick.htmlutils.htmlcolors.test;

import java.awt.Color;

import junit.framework.Test;
import junit.framework.TestCase;
import junit.framework.TestSuite;

import com.tecnick.htmlutils.htmlcolors.HTMLColors;

/**
 * JUnit test for HTMLColors class.<br/><br/>
 * Copyright (c) 2004-2005 Tecnick.com S.r.l (www.tecnick.com) Via Ugo Foscolo
 * n.19 - 09045 Quartu Sant'Elena (CA) - ITALY - www.tecnick.com -
 * info@tecnick.com<br/>
 * License: http://www.gnu.org/copyleft/lesser.html LGPL
 * @author Nicola Asuni [www.tecnick.com].
 * @version 1.0.002
 */
public class HTMLColorsTest extends TestCase {
	
	protected static Color testcolor;
	
	public static void main (String[] args) {
		junit.textui.TestRunner.run(suite());
	}
	
	protected void setUp() {
		testcolor = new Color(255, 128, 64);
	}
	
	public static Test suite() {
		return new TestSuite(HTMLColorsTest.class);
	}
	
	public void testAdd() {
		assertTrue(HTMLColors.getHTMLColor(testcolor).compareTo("#ff8040") == 0);
		assertTrue(HTMLColors.getColorObject("#abcdef").toString().compareTo("java.awt.Color[r=171,g=205,b=239]") == 0);
		assertTrue(HTMLColors.getColorObject("aabbcc").toString().compareTo("java.awt.Color[r=170,g=187,b=204]") == 0);
	}
}

