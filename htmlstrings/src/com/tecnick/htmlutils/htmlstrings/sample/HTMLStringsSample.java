package com.tecnick.htmlutils.htmlstrings.sample;

import com.tecnick.htmlutils.htmlstrings.HTMLStrings;

/**
 * Implementation example of HTMLStrings class.<br/><br/>
 * Copyright (c) 2004-2005 Tecnick.com S.r.l (www.tecnick.com) Via Ugo Foscolo
 * n.19 - 09045 Quartu Sant'Elena (CA) - ITALY - www.tecnick.com -
 * info@tecnick.com<br/>
 * License: http://www.gnu.org/copyleft/lesser.html LGPL
 * @author Nicola Asuni [www.tecnick.com].
 * @version 1.0.004
 */
public class HTMLStringsSample {
	
	protected static String rawstr = new String("\nline one\nline two\nline three\nàèìòù");
	
	/**
	 * Prints 2 strings on System.out
	 * @param args String[]
	 */
	public static void main(String[] args) {
		System.out.println("\noriginal string:" + rawstr);
		System.out.println("\ncompactString:" + HTMLStrings.compactString(rawstr));
		System.out.println("\nautoBR:" + HTMLStrings.autoBR(rawstr));
		
		String tmpstr = HTMLStrings.getEncodedString(rawstr, "UTF-8", "ISO-8859-1");
		System.out.println("\nencoding ISO-8859-1:" + tmpstr);
		
		tmpstr = HTMLStrings.getEncodedString(tmpstr, "ISO-8859-1", "UTF-8");
		System.out.println("\nencoding UTF-8:" + tmpstr);
	}	  
}
