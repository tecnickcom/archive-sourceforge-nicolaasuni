package com.tecnick.htmlutils.htmlcolors.sample;

import java.awt.Color;

import com.tecnick.htmlutils.htmlcolors.HTMLColors;

/**
 * Implementation example of HTMLColors class.<br/><br/>
 * Copyright (c) 2004-2005 Tecnick.com S.r.l (www.tecnick.com) Via Ugo Foscolo
 * n.19 - 09045 Quartu Sant'Elena (CA) - ITALY - www.tecnick.com -
 * info@tecnick.com<br/>
 * License: http://www.gnu.org/copyleft/lesser.html LGPL
 * @author Nicola Asuni [www.tecnick.com].
 * @version 1.0.002
 */
public class HTMLColorsSample {
	
	/**
	 * Prints strings on System.out
	 * @param args String[]
	 */
	public static void main(String[] args) {
		Color testcolor = new Color(255, 128, 64);
		System.out.println("R=255, G=128, B=64: " + HTMLColors.getHTMLColor(testcolor));
		System.out.println("#abcdef: " + HTMLColors.getColorObject("#abcdef").toString());
		System.out.println("aabbcc: " + HTMLColors.getColorObject("aabbcc").toString());
	}	  
}
