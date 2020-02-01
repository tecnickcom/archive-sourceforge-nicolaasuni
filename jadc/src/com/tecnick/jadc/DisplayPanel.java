package com.tecnick.jadc;

import java.awt.Graphics;
import java.awt.Image;
import java.awt.Panel;

/**
 * <p>Title: DisplayPanel Class</p>
 * <p>Description: Class to create digital display</p>
 *
 * <br/>Copyright (c) 2002-2006 Tecnick.com S.r.l (www.tecnick.com) Via Ugo
 * Foscolo n.19 - 09045 Quartu Sant'Elena (CA) - ITALY - www.tecnick.com -
 * info@tecnick.com <br/>
 * Project homepage: <a href="http://jddm.sourceforge.net" target="_blank">http://jxhtmledit.sourceforge.net</a><br/>
 * License: http://www.gnu.org/copyleft/gpl.html GPL 2
 * 
 * @author Nicola Asuni [www.tecnick.com].
 * @version  1.0.007
 */
public class DisplayPanel
extends Panel {
	
	
	/**
	 * serialVersionUID
	 */
	private static final long serialVersionUID = 4474611071354733117L;
	
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
	private Image img_bg;
	
	/**
	 * digit images
	 */
	private Image[] dig = new Image[15];
	
	/**
	 * images to display in order.
	 */
	private Image[] display_digits;
	
	/**
	 * current string to display
	 */
	private String current_info;
	
	/**
	 * number of display digits
	 */
	private int num_digits;
	
	/**
	 * total display width
	 */
	private int w;
	
	/**
	 * total display height
	 */
	private int h;
	
	/**
	 * digits width
	 */
	private int dw;
	
	/**
	 * digits height
	 */
	private int dh;
	
	/**
	 * digits X start coordinate
	 */
	private int dx;
	
	/**
	 * digits Y start coordinate
	 */
	private int dy;
	
	/**
	 * background image width
	 */
	private int bw;
	
	/**
	 * background image height
	 */
	private int bh;
	
	/**
	 * background image X start coordinate
	 */
	private int bx;
	
	/**
	 * background image Y start coordinate
	 */
	private int by;
	
	/**
	 * Build a void panel.
	 */
	public DisplayPanel() {
		current_info = "";
		num_digits = 1;
		img_bg = null;
		dig = null;
	}
	
	/**
	 * Build a display panel.
	 * @param numdig number of display digits
	 * @param info string to display
	 * @param image background image
	 * @param dimg array of digits images
	 * @param aw applet width
	 * @param ah applet height
	 */
	public DisplayPanel(int numdig, String info, Image image, Image[] dimg,
			int aw, int ah) {
		num_digits = numdig;
		display_digits = new Image[numdig]; //set array for digits
		current_info = info;
		img_bg = image; //background image
		dig = dimg; // digits images
		w = aw;
		h = ah;
		
		setLayout(null);
		resize();
	}
	
	
	/**
	 * overwrite update method for double buffering
	 * @param g graphics
	 */
	public void update(Graphics g) {
		paint(g);
	}
	
	/**
	 * Set string to display
	 * @param info string to display
	 */
	public void setInfo(String info) {
		current_info = info;
		repaint();
	}
	
	/**
	 * Set the display background
	 * @param image background image
	 */
	public void setBackgroundImage(Image image) {
		img_bg = image; //background image
		repaint();
	}
	
	/**
	 * Set number of display digits
	 * @param numdig number of digits on display
	 */
	public void setNumDigits(int numdig) {
		num_digits = numdig;
		display_digits = new Image[numdig]; //set array for digits
		repaint();
	}
	
	/**
	 * Set the display digits
	 * @param images array of digits images
	 */
	public void setDigitsImages(Image[] images) {
		dig = images; //digits images
		repaint();
	}
	
	/**
	 * Creates the Panel's peer.
	 * The peer allows you to modify the appearance of the panel without changing its functionality.
	 */
	public synchronized void addNotify() {
		resize();
		super.addNotify();
	}
	
	/**
	 * Resize the display
	 */
	public void resize() {
		
		bw = 0;
		bh = 0;
		bx = 0;
		by = 0;
		dw = 0;
		dh = 0;
		dx = 0;
		dy = 0; //reset variables
		
		int digit = 0; //track current digit
		
		//consider background image size
		if (img_bg != null) {
			bw = (int) img_bg.getWidth(this);
			bh = (int) img_bg.getHeight(this);
		}
		
		//calculate string size
		if (current_info != null) {
			if (current_info.length() > num_digits) {
				current_info = current_info.substring(0, num_digits); //resize string to max num of digits
			}
			for (int i = 0; i < num_digits; i++) {
				try {
					digit = Integer.parseInt(current_info.substring(i, i + 1));
				}
				catch (NumberFormatException e) { // the character is not a number
					if (current_info.substring(i, i + 1).compareTo(":") == 0) {
						digit = 10;
					}
					else if (current_info.substring(i, i + 1).compareTo(".") == 0) {
						digit = 11;
					}
					else if (current_info.substring(i, i + 1).compareTo(" ") == 0) {
						digit = 12;
					}
					else if (current_info.substring(i, i + 1).compareTo("+") == 0) {
						digit = 13;
					}
					else if (current_info.substring(i, i + 1).compareTo("-") == 0) {
						digit = 14;
					}
					else {
						digit = 12; //blank image
					}
				}
				if (dig[digit] != null) {
					dw += dig[digit].getWidth(this); //sum width
					dh = Math.max(dh, dig[digit].getHeight(this)); //calc highest digit
					display_digits[i] = dig[digit]; //assign digit to display
				}
			}
		}
		
		//display size (background + digits)
		//h = Math.max(dh, bh);
		//w = Math.max(dw, bw);
		
		//calc coordinates (center objects)
		dx = (int) ( (w - dw) / 2);
		dy = (int) ( (h - dh) / 2);
		bx = (int) ( (w - bw) / 2);
		by = (int) ( (h - bh) / 2);
		
		setSize(w, h);
	}
	
	/**
	 * Paint image at specified position
	 * @param gbuffer graphic context
	 * @param img image to paint
	 * @param x X coordinate
	 * @param y Y coordinate
	 */
	protected void paintImage(Graphics gbuffer, Image img, int x, int y) {
		if (img != null) {
			//Graphics g = getGraphics();
			if (gbuffer == null) {
				return;
			}
			gbuffer.drawImage(img, x, y, this);
		}
	}
	
	/**
	 * draw button elements (border, image and label) at calculated positions
	 *  @param g the graphic area when diplay button elements
	 */
	public synchronized void paint(Graphics g) {
		resize(); //calculate size and get images to display
		
		//DOUBLE BUFFERING:
		// Create an offscreen image to draw on
		offscreen = createImage(w,h);
		// by doing this everything that is drawn by bufferGraphics will be written on the offscreen image.
		bufferGraphics = offscreen.getGraphics();
		
		bufferGraphics.clearRect(0, 0, w, h); //clean digits area
		
		//paint background image
		if (img_bg != null) {
			paintImage(bufferGraphics, img_bg, bx, by);
		}
		
		//paint display digits
		int posx = dx; //current digit X position
		for (int i = 0; i < num_digits; i++) {
			paintImage(bufferGraphics, display_digits[i], posx, dy); //paint current digit image
			posx += display_digits[i].getWidth(this);
		}
		g.drawImage(offscreen,0,0,this);
	}
	
}