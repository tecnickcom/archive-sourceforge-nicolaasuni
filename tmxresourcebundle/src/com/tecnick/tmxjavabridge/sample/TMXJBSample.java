package com.tecnick.tmxjavabridge.sample;

import com.tecnick.tmxjavabridge.TMXResourceBundle;

/**
 * Sample class for TMXResourceBundle class.
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
public class TMXJBSample {
	
	/**
	 * loads TMX data
	 */
	final static TMXResourceBundle res_en = new TMXResourceBundle("tmx/sample_tmx.xml", "en");
	// test cache system
	final static TMXResourceBundle res_it = new TMXResourceBundle("tmx/sample_tmx.xml", "it", "src/com/tecnick/tmxjavabridge/test/test_tmx_it.obj");
	
	/**
	 * Prints 2 strings on System.out
	 * @param args String[]
	 */
	public static void main(String[] args) {
		System.out.println(res_en.getString("hello", ""));
		System.out.println(res_en.getString("world", ""));
		System.out.println(res_it.getString("hello", ""));
		System.out.println(res_it.getString("world", ""));
	}
}
