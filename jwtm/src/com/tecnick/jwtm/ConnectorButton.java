package com.tecnick.jwtm;

import java.applet.AudioClip;
import java.awt.AWTEventMulticaster;
import java.awt.Color;
import java.awt.Graphics;
import java.awt.Image;
import java.awt.Panel;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.event.MouseAdapter;
import java.awt.event.MouseEvent;

/**
 * <p>Title: ConnectorButton Class</p>
 * <p>Description: Class to create image buttons and behaviour for tree connector</p>
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
public class ConnectorButton
extends Panel {
	
	/**
	 * serialVersionUID
	 */
	private static final long serialVersionUID = 7114914860789537256L;
	
	/**
	 * graphics for double buffering
	 */
	private Graphics bufferGraphics;
	
	/**
	 * offscreen image to handle double buffering
	 */
	private Image offscreen;
	
	/**
	 * action command to be returned to the listener
	 */
	protected String actionCommand;
	
	/**
	 * the action listener (for events)
	 */
	protected ActionListener actionListener = null;
	
	/**
	 * menu item id to be returned on event as actionCommand
	 */
	private int connectorID; // connector item id to be returned on event as actionCommand;
	
	/**
	 * constant LEFT
	 */
	public static final int LEFT = 0;
	
	/**
	 * constant TOP
	 */
	public static final int TOP = 1;
	
	/**
	 * constant RIGHT
	 */
	public static final int RIGHT = 2;
	
	/**
	 * constant BOTTOM
	 */
	public static final int BOTTOM = 3;
	
	/**
	 * constant CENTER
	 */
	public static final int CENTER = 4;
	
	/**
	 * current button icon
	 */
	private Image img;
	
	/**
	 * button icon for normal state
	 */
	private Image img_off;
	
	/**
	 * button icon for button pressed
	 */
	private Image img_on;
	
	/**
	 * button icon for mouse over
	 */
	private Image img_over;
	
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
	 * float parameter to scale the image size
	 */
	private float imageScale = 1;
	
	/**
	 * audio clip for button click event
	 */
	private AudioClip audio_click;
	
	/**
	 * audio clip for mouse over event
	 */
	private AudioClip audio_over;
	
	/**
	 * connector direction (right = left-to-right)
	 */
	private int direction = LEFT;
	
	// INTERNAL BUTTON
	
	/**
	 * horizontal position of internal button icon image
	 */
	private int ix = 0;
	
	/**
	 * vertical position of internal button icon image
	 */
	private int iy = 0;
	
	/**
	 * width of internal button icon image in pixels
	 */
	private int iw = 0;
	
	/**
	 * height of internal button icon image in pixels
	 */
	private int ih = 0;
	
	// BACKGROUND
	
	/**
	 * width of background image in pixels
	 */
	private int bw = 0;
	
	/**
	 * height of background image in pixels
	 */
	private int bh = 0;
	
	//state button colors
	// COLORS
	
	/**
	 * background color for mouse-off state
	 */
	private Color col_off_bck;
	
	/**
	 * background color for mouse-over state
	 */
	private Color col_over_bck;
	
	/**
	 * background color for mouse-on state
	 */
	private Color col_on_bck;
	
	/**
	 * line color for mouse-off state
	 */
	private Color col_off_line;
	
	/**
	 * line color for mouse-over state
	 */
	private Color col_over_line;
	
	/**
	 * line color for mouse-on state
	 */
	private Color col_on_line;
	
	/**
	 * connector line width in pixels (0 = no line)
	 */
	private int line_width;
	
	/**
	 * connector block minimum width
	 */
	private int connector_min_width;
	
	/**
	 * connector final width
	 */
	private int connector_width;
	
	/**
	 * connector height (must be equal to button height)
	 */
	private int connector_height;
	
	/**
	 * connector height (same as button height)
	 */
	private boolean[] vertical_connector;
	
	/**
	 * node level
	 */
	private int item_level;
	
	/**
	 * true if element is a first node
	 */
	private boolean first_node = false;
	
	/**
	 * true if element is a last node
	 */
	private boolean last_node = false;
	
	/**
	 * true if element is a closed node
	 */
	private boolean connector_opened = false;
	
	/**
	 * first connector line position
	 */
	private int first_line_x = 0;
	
	/**
	 * first connector line position
	 */
	private int h_line_y = 0;
	
	/**
	 * horizontal line lenght
	 */
	private int h_line_lenght = 0;
	
	/**
	 * vertical line lenght
	 */
	private int v_line_lenght = 0;
	
	/**
	 * current button action to send to listener
	 * -1 means no action
	 */
	private int current_action = -1;
	
//	------------------------------------------------------------------------------
//	METHODS
//	------------------------------------------------------------------------------
	/**
	 * Build a clickable connector for the menu tree.
	 * @param w connector minimum width
	 * @param h connector height (same as button height)
	 * @param lw connector line width in pixels (0 = no line)
	 * @param l item level (indentation level)
	 * @param isnode true if it's a node
	 * @param f true if is the first element
	 * @param n true if element is a last node
	 * @param dvc array of vertical connectors to display
	 */
	public ConnectorButton(int w, int h, int lw, int l, boolean isnode, boolean f,
			boolean n, boolean[] dvc) {
		//REGISTER_LISTENERS
		//Insert "ConnectorButton register listeners"
		if (isnode) {
			connectorButtonMouseAdapter connectorMouseAdapter = new
			connectorButtonMouseAdapter();
			this.addMouseListener(connectorMouseAdapter);
			actionCommand = "void";
		}
		
		setLayout(null);
		
		connector_min_width = w;
		connector_height = h;
		line_width = lw;
		item_level = l;
		first_node = f;
		last_node = n;
		
		vertical_connector = dvc;
		
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
	 * Set the button colors for each state (mouse off/on/over)
	 * @param ba background button color when mouse is off
	 * @param bb background button color when mouse is over
	 * @param bc background button color when button is clicked
	 * @param la foreground line color when mouse is off
	 * @param lb foreground line color when mouse is over
	 * @param lc foreground line color when button is clicked
	 */
	public void setStateColors(Color ba, Color bb, Color bc, Color la, Color lb,
			Color lc) {
		col_off_bck = ba;
		col_over_bck = bb;
		col_on_bck = bc;
		
		col_off_line = la;
		col_over_line = lb;
		col_on_line = lc;
		
		this.setForeground(col_off_line);
		this.setBackground(col_off_bck);
	}
	
	/**
	 * Set connectorID (unique numeric identifier for current component)
	 * @param id button ID
	 */
	public void setConnectorID(int id) {
		connectorID = id;
	}
	
	/**
	 * Get connectorID (unique numeric identifier for current component)
	 * @return connectorID
	 */
	public int getConnectorID() {
		return connectorID;
	}
	
	/**
	 * Get the current node status.
	 * @return status Current Status (true = closed; false = opened)
	 */
	public boolean getNodeStatus() {
		return connector_opened;
	}
	
	/**
	 * Set the current node status (open/closed)
	 * @param status New status (true = closed; false = opened).
	 *
	 */
	public void setNodeStatus(boolean status) {
		connector_opened = status;
		if (connector_opened) {
			connectorButton_Opened(false);
		}
		else {
			connectorButton_Closed(false);
		}
	}
	
	/**
	 * Set the connector direction (left-to-right or right-to-left).
	 * @param a TOP, LEFT, RIGHT or BOTTOM
	 */
	public void setDirection(int a) {
		if (a != LEFT && a != TOP && a != RIGHT && a != BOTTOM) {
			throw new IllegalArgumentException();
		}
		direction = a;
	}
	
	/**
	 * Set the audio clip to play when the button is pushed.
	 * @param a audio clip when button is clicked
	 * @param b audio clip when mouse is over
	 */
	public void setAudioStateClips(AudioClip a, AudioClip b) {
		audio_click = a;
		audio_over = b;
	}
	
	/**
	 * Set the state images (one image for each node/folder state)
	 * @param imgoff button icon image when mouse is off
	 * @param imgon button icon image when button is pressed
	 * @param imgover button icon image when mouse is off
	 */
	public void setStateImages(Image imgoff, Image imgon, Image imgover) {
		img_off = imgoff;
		img_on = imgon;
		img_over = imgover;
		img = img_off;
		setImage(img);
	}
	
	/**
	 * Set the background state images (one image for each button/mouse state).
	 * @param imgoff button background image when mouse is off
	 * @param imgon button background image when button is pressed
	 * @param imgover button background image when mouse is off
	 */
	public void setStateBgImages(Image imgoff, Image imgon, Image imgover) {
		img_bg_off = imgoff;
		img_bg_on = imgon;
		img_bg_over = imgover;
		img_bg = img_bg_off;
		setBgImage(img_bg);
	}
	
	/**
	 * Set the current image icon to be shown.
	 * @param i Button Image
	 */
	public void setImage(Image i) {
		Image previous_img = img;
		img = i;
		if ( (i == null) || (previous_img == null) ||
				(i.getWidth(this) != img.getWidth(this) ||
						i.getHeight(this) != img.getHeight(this))) {
			resize(); // If new image has a different size, then resize
		}
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
	 * @param pct float
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
				super.setEnabled(true);
				repaint();
			}
		}
		else {
			if (isEnabled()) {
				//super.setEnabled(false);
				repaint();
			}
		}
	}
	
	/**
	 * Set button size and images coordinates.
	 */
	public void resize() {
		connector_width = item_level * connector_min_width;
		setSize(connector_width, connector_height);
		
		//calculate image background size and position
		bw = 0;
		bh = 0;
		if (img_bg != null) {
			bw = (int) (img_bg.getWidth(this) * imageScale);
			bh = (int) (img_bg.getHeight(this) * imageScale);
		}
		
		//calculate image icon size and position
		ix = 0;
		iy = 0;
		iw = 0;
		ih = 0;
		if (img != null) {
			iw = (int) (img.getWidth(this) * imageScale);
			ih = (int) (img.getHeight(this) * imageScale);
			iy = (int) ( (connector_height - ih) / 2);
			if (direction == LEFT) {
				ix = (int) ( (connector_min_width - iw) / 2) +
				( (item_level - 1) * connector_min_width);
			}
			else {
				ix = (int) ( (connector_min_width - iw) / 2);
			}
		}
		
		//first connector line position
		first_line_x = (int) (connector_min_width / 2) - line_width + 1;
		h_line_y = (int) (connector_height / 2) - line_width + 1;
		//horizontal line lenght
		h_line_lenght = (int) Math.ceil( (float) (connector_min_width - iw) / 2);
		//vertical line lenght
		v_line_lenght = (int) Math.ceil( (float) (connector_height - ih) / 2);
	}
	
	/**
	 * Draw button elements (border, image and label) at calculated positions.
	 * @param g the graphic area when diplay button elements
	 */
	public synchronized void paint(Graphics g) {
		
		//get button size
		int w = connector_width; //getSize().width;
		int h = connector_height; //getSize().height;
		
		//DOUBLE BUFFERING:
//		Create an offscreen image to draw on
		offscreen = createImage(w, h);
//		by doing this everything that is drawn by bufferGraphics will be written on the offscreen image.
		bufferGraphics = offscreen.getGraphics();
		
		bufferGraphics.clearRect(0, 0, w, h); //clean area
		
		// paint background image
		if (img_bg != null) {
			bufferGraphics.drawImage(img_bg, 0, 0, this);
			// Tile effect (fill panel repeating image)
			for (int y = 0; y < h; y += bh) { //for each column
				for (int x = 0; x < w; x += bw) { //for each row
					bufferGraphics.drawImage(img_bg, x, y, this);
				}
			}
		}
		
		//draw connector lines
		if (direction == LEFT) {
			bufferGraphics.fillRect(w - h_line_lenght, h_line_y, h_line_lenght,
					line_width); //horizontal
			int xpos = w - connector_min_width + first_line_x;
			if (!first_node) {
				bufferGraphics.fillRect(xpos, 0, line_width,
						v_line_lenght); //vertical up
			}
			if (!last_node) {
				bufferGraphics.fillRect(xpos, h - v_line_lenght,
						line_width, v_line_lenght); //vertical down
			}
			if (item_level > 1) { //remaining vertical connectors
				int j = 0;
				for (int x = first_line_x; x < (w - connector_min_width);
				x += connector_min_width) {
					if (vertical_connector[j]) {
						bufferGraphics.fillRect(x, 0, line_width, h);
					}
					j++;
				}
			}
		}
		else {
			bufferGraphics.fillRect(0, h_line_y, h_line_lenght, line_width); //horizontal
			if (!first_node) {
				bufferGraphics.fillRect(first_line_x, 0, line_width, v_line_lenght); //vertical up
			}
			if (!last_node) {
				bufferGraphics.fillRect(first_line_x, h - v_line_lenght, line_width,
						v_line_lenght); //vertical down
			}
			if (item_level > 1) { //remaining vertical connectors
				int j = item_level - 2;
				for (int x = (first_line_x + connector_min_width);
				x < w; x += connector_min_width) {
					if (vertical_connector[j]) {
						bufferGraphics.fillRect(x, 0, line_width, h);
					}
					j--;
				}
			}
		}
		
		// paint image icon ([+]/[-] folder icon)
		if (img != null) {
			bufferGraphics.drawImage(img, ix, iy, this);
		}
		
		g.drawImage(offscreen, 0, 0, this); //display buffer
		
		//fire action to listener
		if (current_action >= 0) {
			fireActionEvent(current_action);
			current_action = -1;
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
	 * @param actiontype ActionEvent.ACTION_PERFORMED
	 */
	protected void fireActionEvent(int actiontype) {
		if (actionListener != null) {
			actionListener.actionPerformed(new ActionEvent(this, actiontype,
					actionCommand));
		}
	}
	
//	-----------------------------------------------------------------------------
	
//	connector_closed
	
//	-----------------------------------------------------------------------------
	// Fuctions to define button states (on, over, off):
	
	/**
	 * Gets called when the mouse button is pressed.
	 * Repaint button setting images and colors.
	 * @param sound_on boolean to turn off sound
	 */
	protected void connectorButton_Opened(boolean sound_on) {
		AudioClip sound = null;
		if (sound_on) {
			sound = audio_click;
		}
		setButtonComponents(sound, col_on_line, col_on_bck, img_bg_on, img_on);
	}
	
	/**
	 * Gets called when the mouse crosses into the button area.
	 * Repaint button setting images and colors.
	 * @param sound_on boolean to turn off sound
	 */
	protected void connectorButton_MouseOver(boolean sound_on) {
		AudioClip sound = null;
		if (sound_on) {
			sound = audio_over;
		}
		setButtonComponents(sound, col_over_line, col_over_bck, img_bg_over, img_over);
	}
	
	/**
	 * Gets called when the mouse crosses out of the button area.
	 * Repaint button setting images and colors.
	 * @param sound_on boolean to turn off sound
	 */
	protected void connectorButton_Closed(boolean sound_on) {
		AudioClip sound = null;
		if (sound_on) {
			sound = audio_click;
		}
		setButtonComponents(sound, col_off_line, col_off_bck, img_bg_off, img_off);
	}
	
	/**
	 * Set and repaint button components
	 * @param sound audioclip to play
	 * @param foreground foreground color
	 * @param background background color
	 * @param bg background image
	 * @param icon image icon
	 */
	private void setButtonComponents(AudioClip sound, Color foreground,
			Color background, Image bg, Image icon) {
		if (sound != null) {
			sound.play();
		}
		this.setForeground(foreground);
		this.setBackground(background);
		if (bg != null) {
			setBgImage(bg);
		}
		if (icon != null) {
			setImage(icon);
		}
		repaint();
	}
	
//	------------------------------------------------------------------------------
	
	/**
	 * Inner class for handling mouse events.
	 * Insert "ImageButton Mouse Handling"
	 */
	class connectorButtonMouseAdapter
	extends MouseAdapter {
		
		/**
		 * Invoked when a mouse button has been clicked on a component.
		 * @param event MouseEvent
		 */
		public void mouseClicked(MouseEvent event) {
		}
		
		/**
		 * Invoked when the mouse enters a component.
		 * @param event MouseEvent
		 */
		public void mouseEntered(MouseEvent event) {
			connectorButton_MouseOver(true);
			current_action = MouseEvent.MOUSE_ENTERED;
		}
		
		/**
		 * Invoked when the mouse exits a component.
		 * @param event MouseEvent
		 */
		public void mouseExited(MouseEvent event) {
			if (connector_opened) {
				connectorButton_Opened(false);
			}
			else {
				connectorButton_Closed(false);
			}
			current_action = MouseEvent.MOUSE_EXITED;
		}
		
		/**
		 * Invoked when a mouse button has been pressed on a component.
		 * @param event MouseEvent
		 */
		public void mousePressed(MouseEvent event) {
			connector_opened = !connector_opened;
			if (connector_opened) {
				connectorButton_Opened(false);
			}
			else {
				connectorButton_Closed(false);
			}
			current_action = MouseEvent.MOUSE_PRESSED;
		}
		
		/**
		 * Invoked when a mouse button has been released on a component.
		 * @param event MouseEvent
		 */
		public void mouseReleased(MouseEvent event) {
			//check if the mouse has been released out of button
			if ( (event.getX() < 0) || (event.getX() > getSize().width) ||
					(event.getY() < 0) || (event.getY() > getSize().height)) {
				if (connector_opened) {
					connectorButton_Opened(false);
				}
				else {
					connectorButton_Closed(false);
				}
			}
			else {
				connectorButton_MouseOver(false);
			}
			current_action = MouseEvent.MOUSE_RELEASED;
		}
	} //end class connectorButtonMouseAdapter
	
}
//=============================================================================
//END OF FILE
//=============================================================================
