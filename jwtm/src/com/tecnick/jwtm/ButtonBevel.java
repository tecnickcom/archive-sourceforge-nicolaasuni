package com.tecnick.jwtm;

import java.awt.Color;
import java.awt.Component;
import java.awt.Graphics;

/**
 * <p>Title: Button Bevel</p>
 * <p>Description: Class to draw a 3D button bevel (rectangle) on a Graphics area</p>
 *
 * <br/>Copyright (c) 2002-2006 Tecnick.com S.r.l (www.tecnick.com) Via Ugo
 * Foscolo n.19 - 09045 Quartu Sant'Elena (CA) - ITALY - www.tecnick.com -
 * info@tecnick.com <br/>
 * Project homepage: <a href="http://jddm.sourceforge.net" target="_blank">http://jxhtmledit.sourceforge.net</a><br/>
 * License: http://www.gnu.org/copyleft/gpl.html GPL 2
 * 
 * @author Nicola Asuni [www.tecnick.com].
 * @version 1.1.003
 */
public class ButtonBevel {
	
	/**
	 * constant to indicate the "IN" drawing mode (button pushed)
	 */
	public final static int IN = 0;
	
	/**
	 * constant to indicate the "OUT" drawing mode (button unpushed)
	 */
	public final static int OUT = 1;
	
	/**
	 * parent component
	 */
	Component parent;
	
	/**
	 * component width in pixels
	 */
	private int w;
	
	/**
	 * component height in pixels
	 */
	private int h;
	
	/**
	 * starting horizontal position in pixels
	 */
	private int x = 0;
	
	/**
	 * starting vertical position in pixels
	 */
	private int y = 0;
	
	/**
	 * bevel width  in pixels
	 */
	private int bevel_width = 2;
	
	/**
	 * bevel mode (IN = pushed, OUT = unpushed)
	 */
	private int mode = IN;
	
	/**
	 * Create button bevel with specified measures
	 * @param c component where you want add bevel
	 * @param X starting horizontal position in pixels
	 * @param Y starting vertical position in pixels
	 * @param width component width in pixels
	 * @param height component height in pixels
	 * @param bw bevel width in pixels
	 */
	public ButtonBevel(Component c, int X, int Y, int width, int height, int bw) {
		x = X;
		y = Y;
		w = width;
		h = height;
		bevel_width = bw;
		parent = c;
	}
	
	/**
	 * Set the drawing mode (pushed - unpushed)
	 * @param m drawing mode. Possible values are: ButtonBevel.IN, ButtonBevel.OUT
	 */
	public void setDrawingMode(int m) {
		mode = m;
	}
	
	/**
	 * paint the border
	 * @param g Graphics
	 */
	public synchronized void paint(Graphics g) {
		Color c = parent.getBackground();
		Color darker = c.darker();
		Color brighter = c.brighter();
		
		Color c1, c2; //border colors
		
		int i = 0;
		
		switch (mode) {
		case IN: {
			c1 = darker;
			c2 = brighter;
			break;
		}
		default:
		case OUT: {
			c1 = brighter;
			c2 = darker;
			break;
		}
		}
		
		//set bevel
		
		g.setColor(c1);
		for (i = 0; i < bevel_width; i++) {
			g.drawLine(x + i, y + i, x + w - i - 1, y + i); // top
			g.drawLine(x + i, y + i, x + i, y + h - 1); // left
		}
		
		g.setColor(c2);
		for (i = 0; i < bevel_width; i++) {
			g.drawLine(x + i, y + h - i - 1, x + w - i, y + h - i - 1); // bottom
			g.drawLine(x + w - i - 1, y + i, x + w - i - 1, y + h - i); // right
		}
	}
}
//=============================================================================
//END OF FILE
//=============================================================================
