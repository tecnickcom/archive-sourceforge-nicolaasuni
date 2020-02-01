package com.tecnick.htmlutils.jreplugintest;

import java.applet.Applet;

//-----------------------------------------------------------------------------
/**
 * Title: JREPluginTest<br>
 * Description: Dummy Applet to test JRE Plugin.<br>
 * Prints the java JRE version on System.out.<br>
 *
 * <br/>Copyright (c) 2002-2006 Tecnick.com S.r.l (www.tecnick.com) Via Ugo
 * Foscolo n.19 - 09045 Quartu Sant'Elena (CA) - ITALY - www.tecnick.com -
 * info@tecnick.com <br/>
 * Project homepage: <a href="http://jreplugintest.sourceforge.net" target="_blank">http://jxhtmledit.sourceforge.net</a><br/>
 * License: http://www.gnu.org/copyleft/gpl.html GPL 2
 * 
 * @author Nicola Asuni [www.tecnick.com].
 * @version 1.0.001
 */
public class JREPluginTest extends Applet {
	
	/**
	 * serialVersionUID
	 */
	private static final long serialVersionUID = 4158889281064676976L;
	
	/**
	 * Applet void constructor
	 */
	public JREPluginTest() {
	}
	
	/**
	 * Initialize the applet
	 */
	public void init() {
		//display some info on console
		System.out.println("Java Version: " + System.getProperty("java.version") + " from "
				+ System.getProperty("java.vendor"));
	} // end of init
	
}
