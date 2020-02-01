package com.tecnick.xmlconfigreader.sample;

import com.tecnick.xmlconfigreader.XMLConfigReader;

/**
 * Sample class for XMLConfigReader class.
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
public class XMLConfigReaderSample {
	
	/**
	 * loads TMX data
	 */
	final static XMLConfigReader res = new XMLConfigReader("src/com/tecnick/xmlconfigreader/sample/sample.xml");
	
	/**
	 * Prints 2 strings on System.out
	 * @param args String[]
	 */
	public static void main(String[] args) {
		System.out.println(res.getString("1", "name"));
		System.out.println(res.getString("1", "description"));
		System.out.println(res.getString("1", "value"));
		System.out.println(res.getInt("2", "value", 0));
		System.out.println(res.getDouble("3", "value", 0));
		System.out.println(res.getString("inxexistent", "name", "default id"));
		System.out.println(res.getString("1", "inxexistent", "default name"));
	}
}
