package com.tecnick.htmlutils.htmlcolors;

import java.awt.Color;

/**
 * Collection of static methods to handle HTML colors.<br/><br/>
 * Copyright (c) 2004-2005
 * Tecnick.com S.r.l (www.tecnick.com) 
 * Via Ugo Foscolo n.19 - 09045 Quartu Sant'Elena (CA) - ITALY
 * www.tecnick.com - info@tecnick.com<br/>
 * Project homepage: <a href="http://htmlcolors.sourceforge.net" target="_blank">http://htmlcolors.sourceforge.net</a><br/>
 * License: http://www.gnu.org/copyleft/lesser.html LGPL
 * 
 * @author Nicola Asuni [www.tecnick.com].
 * @version 1.0.002
 */
public class HTMLColors {
	
	/**
	 * Void Constructor.
	 */
	public HTMLColors() {
	}
	
	/**
	 * Convert Color object to HTML string rapresentation (#RRGGBB).
	 * 
	 * @param c color to convert
	 * @return html string rapresentation (#RRGGBB)
	 */
	public static String getHTMLColor(Color c) {
		String colorR = "0" + Integer.toHexString(c.getRed());
		colorR = colorR.substring(colorR.length() - 2);
		String colorG = "0" + Integer.toHexString(c.getGreen());
		colorG = colorG.substring(colorG.length() - 2);
		String colorB = "0" + Integer.toHexString(c.getBlue());
		colorB = colorB.substring(colorB.length() - 2);
		String html_color = "#" + colorR + colorG + colorB;
		return html_color;
	}
	
	/**
	 * Convert HTML color (#RRGGBB) to Color object.
	 * 
	 * @param c HTML color string to convert (#RRGGBB)
	 * @return Color object or null in case of error
	 */
	public static Color getColorObject(String c) {
		Color colobj = null;
		try {
			if (c.charAt(0) == '#' ) {
				c = c.substring(1); // remove first character
			}
			int colvalue = Integer.parseInt(c, 16);
			colobj = new Color(colvalue);
		} catch (Exception e) {
			e.printStackTrace();
		}
		return colobj;
	}
	
}