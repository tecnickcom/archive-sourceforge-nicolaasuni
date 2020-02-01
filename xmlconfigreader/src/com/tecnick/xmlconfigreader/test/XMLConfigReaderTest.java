package com.tecnick.xmlconfigreader.test;

import junit.framework.Test;
import junit.framework.TestCase;
import junit.framework.TestSuite;

import com.tecnick.xmlconfigreader.XMLConfigReader;


/**
 * JUnit Test for XMLConfigReader class.
 * <br/><br/>
 * Copyright (c) 2004-2005
 * Tecnick.com S.r.l (www.tecnick.com) 
 * Via Ugo Foscolo n.19 - 09045 Quartu Sant'Elena (CA) - ITALY
 * www.tecnick.com - info@tecnick.com<br/>
 * License: http://www.gnu.org/copyleft/lesser.html LGPL
 * 
 * @author Nicola Asuni [www.tecnick.com].
 * @version 1.0.003
 */

public class XMLConfigReaderTest extends TestCase {
	
	protected static XMLConfigReader res;
	
	public static void main (String[] args) {
		junit.textui.TestRunner.run(suite());
	}
	
	protected void setUp() {
		res = new XMLConfigReader("src/com/tecnick/xmlconfigreader/test/test.xml");
	}
	
	public static Test suite() {
		return new TestSuite(XMLConfigReaderTest.class);
	}
	
	public void testAdd() {
		assertTrue(res.getString("1", "name").compareTo("one") == 0);
		assertTrue(res.getString("1", "description").compareTo("first element") == 0);
		assertTrue(res.getString("1", "value").compareTo("value one") == 0);
		assertTrue(res.getInt("2", "value", 0) == 2);
		assertTrue(res.getDouble("3", "value", 0) == 3.0);
		assertTrue(res.getString("inxexistent", "name", "default id").compareTo("default id") == 0);
		assertTrue(res.getString("1", "inxexistent", "default name").compareTo("default name") == 0);
	}
}

