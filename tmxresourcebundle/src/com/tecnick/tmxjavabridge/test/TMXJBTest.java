package com.tecnick.tmxjavabridge.test;

import junit.framework.Test;
import junit.framework.TestCase;
import junit.framework.TestSuite;

import com.tecnick.tmxjavabridge.TMXResourceBundle;

/**
 * JUnit Test for TMXResourceBundle class.
 * <br/><br/>
 * Copyright (c) 2004-2006
 * Tecnick.com S.r.l (www.tecnick.com) 
 * Via Ugo Foscolo n.19 - 09045 Quartu Sant'Elena (CA) - ITALY
 * www.tecnick.com - info@tecnick.com<br/>
 * License: http://www.gnu.org/copyleft/lesser.html LGPL
 * 
 * @author Nicola Asuni [www.tecnick.com].
 * @version 1.1.008
 */

public class TMXJBTest extends TestCase {
	
	protected static TMXResourceBundle res_en;
	protected static TMXResourceBundle res_it;
	
	public static void main (String[] args) {
		junit.textui.TestRunner.run(suite());
	}
	
	protected void setUp() {
		res_en = new TMXResourceBundle("src/com/tecnick/tmxjavabridge/test/test_tmx.xml", "en");
		res_it = new TMXResourceBundle("src/com/tecnick/tmxjavabridge/test/test_tmx.xml", "it", "src/com/tecnick/tmxjavabridge/test/test_tmx_it.obj");
	}
	
	public static Test suite() {
		return new TestSuite(TMXJBTest.class);
	}
	
	public void testAdd() {
		assertTrue(res_en.getString("hello", "").compareTo("Hello") == 0);
		assertTrue(res_en.getString("world", "").compareTo("World") == 0);
		assertTrue(res_it.getString("hello", "").compareTo("Ciao") == 0);
		assertTrue(res_it.getString("world", "").compareTo("Mondo") == 0);
		assertTrue(res_en.getString("-", "test 01").compareTo("test 01") == 0);
		assertTrue(res_en.getString("-", "test 02").compareTo("test 02") == 0);
		assertTrue(res_it.getString("-", "test 03").compareTo("test 03") == 0);
		assertTrue(res_it.getString("-", "test 04").compareTo("test 04") == 0);
	}
}

