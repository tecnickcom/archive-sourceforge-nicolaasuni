package com.tecnick.jwtm;

import java.awt.Graphics;
import java.awt.Image;
import java.awt.Panel;

/**
 * <p>Title: Background Panel</p>
 * <p>Description: Class to draw panel with image background</p>
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
public class BgPanel
extends Panel {
	
	/**
	 * serialVersionUID
	 */
	private static final long serialVersionUID = -8605629717650358229L;
	
	/**
	 * graphics for double buffering
	 */
	private Graphics bufferGraphics;
	
	/**
	 * offscreen image to handle double buffering
	 */
	private Image offscreen;
	
	/**
	 * background image
	 */
	private Image bg_image = null;
	
	/**
	 * stetched copy of background image
	 */
	private Image stretched_img = null;
	
	/**
	 * Background image width
	 */
	private int img_w;
	
	/**
	 * Background image height
	 */
	private int img_h;
	
	/**
	 * Panel width
	 */
	private int pnl_w;
	
	/**
	 * Panel height
	 */
	private int pnl_h;
	
	/**
	 * remember when image stretching is done (to avoid loops)
	 */
	private boolean stretch_done = false;
	
	/**
	 * constant for LEFT position
	 */
	public static final int LEFT = 0;
	
	/**
	 * constant for TOP position
	 */
	public static final int TOP = 1;
	
	/**
	 * constant for RIGHT position
	 */
	public static final int RIGHT = 2;
	
	/**
	 * constant for BOTTOM position
	 */
	public static final int BOTTOM = 3;
	
	/**
	 * constant for CENTER position
	 */
	public static final int CENTER = 4;
	
	/**
	 * constant for STRETCH mode
	 */
	public static final int STRETCH = 5;
	
	/**
	 * constant for TILE mode
	 */
	public static final int TILE = 6;
	
	private int mode = TILE;
	
	/**
	 * Build a void bakground panel.
	 */
	public BgPanel() {
		this(null, TOP);
	}
	
	/**
	 * Build Panel with background image.
	 * @param i image to display as background.
	 * @param m Display Mode. Possible values are: BgPanel.TILE, BgPanel.CENTER, BgPanel.STRETCH, BgPanel.LEFT, BgPanel.RIGHT.
	 */
	public BgPanel(Image i, int m) {
		bg_image = i;
		mode = m;
	}
	
	/**
	 * Set background image.
	 * @param i image
	 */
	public void setImage(Image i) {
		bg_image = i;
		stretch_done = false;
	}
	
	/**
	 * Set image display mode.
	 * @param m Display Mode. Possible values are: BgPanel.TILE, BgPanel.CENTER, BgPanel.STRETCH, BgPanel.LEFT, BgPanel.RIGHT.
	 */
	public void setMode(int m) {
		mode = m;
	}
	
	/**
	 * Get image and panel size.
	 */
	private void getSizes() {
		img_w = 0;
		img_h = 0;
		
		if (bg_image != null) {
			img_w = bg_image.getWidth(this);
			img_h = bg_image.getHeight(this);
		}
		
		pnl_w = getSize().width;
		pnl_h = getSize().height;
	}
	
	/**
	 * Paint panel with background.
	 * @param g graphics context
	 */
	public void paint(Graphics g) {
		
		getSizes();
		
		//DOUBLE BUFFERING:
		// Create an offscreen image to draw on
		offscreen = createImage(pnl_w, pnl_h);
		// by doing this everything that is drawn by bufferGraphics will be written on the offscreen image.
		bufferGraphics = offscreen.getGraphics();
		
		if (bg_image != null) {
			switch (mode) {
			case TILE: {
				// Tile effect (fill panel repeating image)
				for (int y = 0; y < pnl_h; y += img_h) { //for each column
					for (int x = 0; x < pnl_w; x += img_w) { //for each row
						bufferGraphics.drawImage(bg_image, x, y, this);
					}
				}
				break;
			}
			case CENTER: {
				bufferGraphics.drawImage(bg_image, (int) ( (pnl_w - img_w) / 2),
						(int) ( (pnl_h - img_h) / 2), this);
				break;
			}
			case STRETCH: {
				if ( (!stretch_done) && (pnl_w > 0) && (pnl_h > 0)) {
					doImageStretch();
				}
				if (stretched_img != null) {
					bufferGraphics.drawImage(stretched_img, 0, 0, this);
				}
				else {
					bufferGraphics.drawImage(bg_image, 0, 0, this);
				}
				break;
			}
			case LEFT: {
				bufferGraphics.drawImage(bg_image, 0, 0, this);
				break;
			}
			case RIGHT: {
				bufferGraphics.drawImage(bg_image, pnl_w - img_w, 0, this);
				break;
			}
			}
		}
		g.drawImage(offscreen, 0, 0, this); //display buffer
	}
	
	/**
	 * stretch image
	 */
	private void doImageStretch() {
		if (bg_image != null) {
			stretched_img = bg_image.getScaledInstance(pnl_w, pnl_h, Image.SCALE_SMOOTH);
			stretch_done = true; // this is needed to avoid getScaledInstance/paint loop
		}
	}
	
}
//=============================================================================
//END OF FILE
//=============================================================================
