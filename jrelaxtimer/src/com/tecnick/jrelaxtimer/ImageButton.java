package com.tecnick.jrelaxtimer;

import java.awt.AWTEventMulticaster;
import java.awt.Graphics;
import java.awt.Image;
import java.awt.Panel;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.event.MouseAdapter;
import java.awt.event.MouseEvent;

/**
 * <p>Title: ImageButton Class</p>
 * <p>Description: Class to create image buttons with text and behaviour</p>
 *
 * <br/>Copyright (c) 2002-2006 Tecnick.com S.r.l (www.tecnick.com) Via Ugo
 * Foscolo n.19 - 09045 Quartu Sant'Elena (CA) - ITALY - www.tecnick.com -
 * info@tecnick.com <br/>
 * Project homepage: <a href="http://jddm.sourceforge.net" target="_blank">http://jxhtmledit.sourceforge.net</a><br/>
 * License: http://www.gnu.org/copyleft/gpl.html GPL 2
 * 
 * @author Nicola Asuni [www.tecnick.com].
 * @version 1.0.001
 */
public class ImageButton
extends Panel {
	
	// CONSTANTS AND VARIABLES ===================================================
	
	/**
	 * serialVersionUID
	 */
	private static final long serialVersionUID = 3261255555618629240L;
	
	/**
	 * action command to be returned to the listener
	 */
	protected String actionCommand;
	
	/**
	 * the action listener (for events)
	 */
	protected ActionListener actionListener = null;
	
	/**
	 * Remember mouse up/down status
	 */
	protected boolean isMouseDown = false;
	
	/**
	 * Remember mouse ino/out status
	 */
	protected boolean isMouseInside = false;
	
	/**
	 * menu item id to be returned on event as actionCommand
	 */
	private int buttonID;
	
	/**
	 * current background button image
	 */
	private Image img_bg;
	
	/**
	 * button background image for normal state
	 */
	private Image img_bg_off;
	
	/**
	 * button background image for button pressed
	 */
	private Image img_bg_on;
	
	/**
	 * button background image for mouse over
	 */
	private Image img_bg_over;
	
	/**
	 * inactive image for disabled button
	 */
	private Image inactive_img;
	
	/**
	 * float parameter to scale the image size
	 */
	private float imageScale = 1;
	
	// BACKGROUND
	
	/**
	 * horizontal position of background image
	 */
	private int bx = 0;
	
	/**
	 * vertical position of background image
	 */
	private int by = 0;
	
	/**
	 * width of background image in pixels
	 */
	private int bw = 0;
	
	/**
	 * height of background image in pixels
	 */
	private int bh = 0;
	
	/**
	 * true if element is a closed node
	 */
	private boolean button_state = true;
	
	// METHODS ===================================================================
	
	/**
	 * Void Constructor
	 */
	public ImageButton() {
		this(null, null, null);
	}
	
	/**
	 * Constructor
	 * @param imgoff button image for mouse off state
	 * @param imgon button image for mouse clicked state
	 * @param imgover button image for mouse over state
	 */
	public ImageButton(Image imgoff, Image imgon, Image imgover) {
		//REGISTER_LISTENERS
		//Insert "ImageButton register listeners"
		Mouse aMouse = new Mouse();
		this.addMouseListener(aMouse);
		actionCommand = "void";
		setLayout(null);
		setStateBgImages(imgoff, imgon, imgover);
		resize();
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
	 * Set buttonID (unique numeric identifier for current component)
	 * @param id button ID
	 */
	public void setButtonID(int id) {
		buttonID = id;
	}
	
	/**
	 * Get the buttonID (unique numeric identifier for current component)
	 * @return buttonID
	 */
	public int getButtonID() {
		return buttonID;
	}
	
	/**
	 * Set the background state images  (one image for each button/mouse state)
	 *  @param imgoff button background image when mouse is off
	 *  @param imgon button background image when button is pressed
	 *  @param imgover button background image when mouse is off
	 */
	public void setStateBgImages(Image imgoff, Image imgon, Image imgover) {
		img_bg_off = imgoff;
		img_bg_on = imgon;
		img_bg_over = imgover;
		img_bg = img_bg_off;
		setBgImage(img_bg);
	}
	
	/**
	 * Set the current background image to be shown.
	 * @param i Button Background Image
	 */
	public void setBgImage(Image i) {
		Image previous_img = img_bg;
		img_bg = i;
		if ( (i == null) || (previous_img == null) ||
				(i.getWidth(this) != img_bg.getWidth(this) ||
						i.getHeight(this) != img_bg.getHeight(this))) {
			resize(); // If new image has a different size, then resize
		}
		repaintImages();
	}
	
	
	/**
	 * Scale the image to the given value (1.0 = 100%).
	 * @param f float
	 */
	public void setImageScale(double f) {
		setImageScale( (float) f);
	}
	
	/**
	 * Scale the image to the given value (1.0 = 100%).
	 *  @param pct float
	 */
	public void setImageScale(float pct) {
		if (pct <= 0) {
			pct = 1;
		}
		imageScale = pct;
		resize();
	}
	
	/**
	 * Enables or disables this component, depending on the value of the
	 * parameter b. An enabled component can respond to user input and generate
	 * events. Components are enabled initially by default.
	 * @param a If true, this component is enabled; otherwise this component is disabled
	 */
	public void setEnable(boolean a) {
		if (a) {
			if (!isEnabled()) {
				isMouseDown = false;
				super.setEnabled(true);
				repaint();
			}
		}
		else {
			if (isEnabled()) {
				isMouseDown = false;
				//super.setEnabled(false);
				repaint();
			}
		}
	}
	
	/**
	 * Resize the size of button and components.
	 */
	public void resize() {
		//consider background image size
		int w = 0, h = 0;
		if (img_bg != null) {
			w = (int) (img_bg.getWidth(this) * imageScale);
			h = (int) (img_bg.getHeight(this) * imageScale);
		}
		setSize(w, h);
	}
	
	/**
	 * Redraw the button object
	 */
	protected void repaintButton() {
		paint(getGraphics());
	}
	
	/**
	 * Redraw the button images
	 */
	protected void repaintImages() {
		// paint background image
		if (img_bg != null) {
			Graphics g = getGraphics();
			if (g == null) {
				return;
			}
			
			int w = getSize().width;
			int h = getSize().height;
			g.clearRect(0, 0, w, h); //clean area
			
			g.drawImage(img_bg, bx, by, this);
		}
	}
	
	/**
	 * Draw button elements (border, image and label) at calculated positions
	 * @param g the graphic area when diplay button elements
	 */
	public synchronized void paint(Graphics g) {
		if (!isEnabled() && inactive_img == null) {
			//inactive_img = createDisabledImage(img, this);
		}
		
		//get button size
		int w = getSize().width;
		int h = getSize().height;
		
		g.clearRect(0, 0, w, h); //clean area
		//g.setPaintMode();
		
		//get image background size
		bx = 0;
		by = 0;
		bw = 0;
		bh = 0;
		//image background measures
		if (img_bg != null) {
			bw = (int) (img_bg.getWidth(this) * imageScale);
			bh = (int) (img_bg.getHeight(this) * imageScale);
			bx = (int) ( (w - bw) / 2);
			by = (int) ( (h - bh) / 2);
		}
		
		repaintImages();
	}
	
	/**
	 * Get the current node status.
	 * @return status Current Status (true = closed; false = opened)
	 */
	public boolean getButtonStatus() {
		return button_state;
	}
	
	/**
	 * Set the current node status (open/closed)
	 * @param status New status (true = closed; false = opened).
	 *
	 */
	public void setButtonStatus(boolean status) {
		button_state = status;
		if (button_state) {
			if (img_bg_on != null) {
				setBgImage(img_bg_on);
			}
		}
		else {
			if (img_bg_off != null) {
				setBgImage(img_bg_off);
			}
		}
	}
	
	//Routines for handling ActionListener management.
	//Insert "ImageButton Action Management"
	//----------------------------------------------------------------------------
	
	/**
	 * Sets the command name of the action event fired by this button.
	 * @param command The name of the action event command fired by this button
	 */
	public void setActionCommand(String command) {
		actionCommand = command;
	}
	
	/**
	 * Returns the command name of the action event fired by this button.
	 * @return the action command name
	 */
	public String getActionCommand() {
		return actionCommand;
	}
	
	/**
	 * Adds the specified action listener to receive action events
	 * from this button.
	 * @param l the action listener
	 */
	public void addActionListener(ActionListener l) {
		actionListener = AWTEventMulticaster.add(actionListener, l);
	}
	
	/**
	 * Removes the specified action listener so it no longer receives
	 * action events from this button.
	 * @param l the action listener
	 */
	public void removeActionListener(ActionListener l) {
		actionListener = AWTEventMulticaster.remove(actionListener, l);
	}
	
	/**
	 * Fire an action event to the listeners.
	 * @param actiontype ActionEvent.ACTION_PERFORMED,
	 */
	protected void fireActionEvent(int actiontype) {
		if (actionListener != null) {
			actionListener.actionPerformed(new ActionEvent(this, actiontype,
					actionCommand));
		}
	}
	
//	-----------------------------------------------------------------------------
	
	/**
	 * Inner class for handing mouse events.
	 * Insert "ImageButton Mouse Handling"
	 */
	class Mouse
	extends MouseAdapter {
		
		/**
		 * Invoked when the mouse exits a component.
		 * @param event MouseEvent
		 */
		public void mouseExited(MouseEvent event) {
			ImageButton_MouseExited(event);
		}
		
		/**
		 * Invoked when the mouse enters a component.
		 * @param event MouseEvent
		 */
		public void mouseEntered(MouseEvent event) {
			ImageButton_MouseEntered(event);
		}
		
		/**
		 * Invoked when a mouse button has been released on a component.
		 * @param event MouseEvent
		 */
		public void mouseReleased(MouseEvent event) {
			ImageButton_MouseReleased(event);
		}
		
		/**
		 * Invoked when a mouse button has been pressed on a component.
		 * @param event MouseEvent
		 */
		public void mousePressed(MouseEvent event) {
			ImageButton_MousePressed(event);
		}
	}
	
	/**
	 * Gets called when the mouse button is pressed.
	 * Repaint images.
	 * @param event Mouse Event
	 */
	protected void ImageButton_MousePressed(MouseEvent event) {
		isMouseDown = true;
		if (!button_state) {
			if (img_bg_on != null) {
				setBgImage(img_bg_on);
			}
		}
		else {
			if (img_bg_off != null) {
				setBgImage(img_bg_off);
			}
		}
		fireActionEvent(MouseEvent.MOUSE_PRESSED);
	}
	
	/**
	 * Gets called when the mouse button is released on this connector.
	 * Repaint images.
	 * @param event Mouse Event
	 */
	
	protected void ImageButton_MouseReleased(MouseEvent event) {
		isMouseDown = false;
		if (isMouseInside) {
			if (img_bg_over != null) {
				setBgImage(img_bg_over);
			}
			fireActionEvent(MouseEvent.MOUSE_CLICKED);
		}
		else {
			if (button_state) {
				if (img_bg_on != null) {
					setBgImage(img_bg_on);
				}
			}
			else {
				if (img_bg_off != null) {
					setBgImage(img_bg_off);
				}
			}
			fireActionEvent(MouseEvent.MOUSE_RELEASED);
		}
	}
	
	/**
	 * Gets called when the mouse crosses into the connector area.
	 * Repaint images.
	 * @param event Mouse Event
	 */
	
	protected void ImageButton_MouseEntered(MouseEvent event) {
		isMouseInside = true;
		if (img_bg_over != null) {
			setBgImage(img_bg_over);
		}
		fireActionEvent(MouseEvent.MOUSE_ENTERED);
	}
	
	/**
	 * Gets called when the mouse crosses out of the connector area.
	 * Repaint images.
	 * @param event Mouse Event
	 */
	protected void ImageButton_MouseExited(MouseEvent event) {
		isMouseInside = false;
		isMouseDown = false; //remove if you want display menus on MOUSE_CLICKED
		if (button_state) {
			if (img_bg_on != null) {
				setBgImage(img_bg_on);
			}
		}
		else {
			if (img_bg_off != null) {
				setBgImage(img_bg_off);
			}
		}
		fireActionEvent(MouseEvent.MOUSE_EXITED);
	}
	
	
}
//=============================================================================
//END OF FILE
//=============================================================================