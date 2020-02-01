package com.tecnick.htmlutils.htmlurls.sample;

import java.net.URL;

import com.tecnick.htmlutils.htmlurls.HTMLURLs;

/**
 * Implementation example of HTMLURLs class.<br/><br/>
 * Copyright (c) 2004-2005 Tecnick.com S.r.l (www.tecnick.com) Via Ugo Foscolo
 * n.19 - 09045 Quartu Sant'Elena (CA) - ITALY - www.tecnick.com -
 * info@tecnick.com<br/>
 * License: http://www.gnu.org/copyleft/lesser.html LGPL
 * @author Nicola Asuni [www.tecnick.com].
 * @version 1.0.004
 */
public class HTMLURLsSample {
	
	protected static String testurl = new String("");
	
	/**
	 * Prints some strings on System.out
	 * @param args String[]
	 */
	public static void main(String[] args) {
		System.out.println("isRelativeLink(\"../test.html\"): " + HTMLURLs.isRelativeLink("../test.html"));
		System.out.println("isRelativeLink(\"http://www.tecnick.com/\"): " + HTMLURLs.isRelativeLink("http://www.tecnick.com/"));
		System.out.println("resolveRelativeURL(\"/dir1/dir2/../dir3/dir4/../../dir5/test.html\"): " + HTMLURLs.resolveRelativeURL("/dir1/dir2/../dir3/dir4/../../dir5/test.html"));	
		URL testurl = HTMLURLs.setURL("http://www.tecnick.com/", "/public/code/index.php");
		System.out.println("setURL: " + testurl.toString());
	}	  
}
