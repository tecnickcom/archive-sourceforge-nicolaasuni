package com.tecnick.jddm;

import java.applet.AudioClip;
import java.awt.AWTEventMulticaster;
import java.awt.Color;
import java.awt.Font;
import java.awt.FontMetrics;
import java.awt.Graphics;
import java.awt.Image;
import java.awt.Panel;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.event.MouseAdapter;
import java.awt.event.MouseEvent;
import java.util.StringTokenizer;

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
 * @version 2.1.003
 */
public class ImageButton
extends Panel {
	
	
	/**
	 * serialVersionUID
	 */
	private static final long serialVersionUID = -864166275572624948L;
	
//	CONSTANTS AND VARIABLES ===================================================
	
	
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
	 * Remember mouse position status
	 */
	protected boolean isMouseInside = false;
	
	/**
	 * Remember mouse up/down status
	 */
	protected boolean isButtonPushed = false;
	
	/**
	 * menu item id to be returned on event as actionCommand
	 */
	private int buttonID;
	
	/**
	 * 3D bevel width in pixels
	 */
	private int bevel_width = 2;
	
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
	 * button label
	 */
	private String label;
	
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
	 * inactive image for disabled button
	 */
	private Image inactive_img;
	
	/**
	 * current arrow image
	 */
	private Image img_arrow;
	
	/**
	 * arrow image off status
	 */
	private Image img_arrow_off;
	
	/**
	 * arrow image ovr status
	 */
	private Image img_arrow_over;
	
	/**
	 * arrow image on status
	 */
	private Image img_arrow_on;
	
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
	 * if true display the 3D button border
	 */
	private boolean fShowBorder = true;
	
	/**
	 * if true make the button "depress" when clicked.
	 */
	private boolean fDrawPushedIn = true;
	
	/**
	 * label position relative to icon position.
	 * possible values are: ImageButton.LEFT, ImageButton.RIGHT, ImageButton.TOP, ImageButton.BOTTOM
	 */
	private int pos = RIGHT;
	
	/**
	 * if true center the block [image + label]
	 */
	private boolean center_block = false;
	
	/**
	 * arrow image position
	 * possible values are: ImageButton.LEFT, ImageButton.RIGHT
	 */
	private int arrow_pos = RIGHT;
	
	/**
	 * array of button paddings (Left, Right, Top, Bottom)
	 * distance in pixels between button border and button objects (image or label)
	 */
	private int[] padding = new int[] {
			2, 2, 2, 2};
	
	/**
	 * distance in pixels between image and label
	 */
	private int gap = 0;
	
	/**
	 * text shadow relative horizontal position in pixels
	 */
	private int shadow_x = 0;
	
	/**
	 * text shadow relative vertical position in pixels
	 */
	private int shadow_y = 0;
	
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
	
	// ARROW
	
	/**
	 * horizontal position of arrow image
	 */
	private int ax = 0;
	
	/**
	 * vertical position of arrow image
	 */
	private int ay = 0;
	
	/**
	 * width of arrow image in pixels
	 */
	private int aw = 0;
	
	/**
	 * height of arrow image in pixels
	 */
	private int ah = 0;
	
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
	 * text color for mouse-off state
	 */
	private Color col_off_txt;
	
	/**
	 * text color for mouse-over state
	 */
	private Color col_over_txt;
	
	/**
	 * text color for mouse-on state
	 */
	private Color col_on_txt;
	
	/**
	 * text shadow color for mouse-off state
	 */
	private Color col_off_sdw;
	
	/**
	 * text shadow color for mouse-over state
	 */
	private Color col_over_sdw;
	
	/**
	 * text shadow color for mouse-on state
	 */
	private Color col_on_sdw;
	
	/**
	 * remember current text shadow color
	 */
	private Color col_shadow; //current shadow color
	
	/**
	 * current button action to send to listener
	 * -1 means no action
	 */
	protected int current_action = -1;
	
	// METHODS ===================================================================
	
	/**
	 * Void constructor
	 */
	public ImageButton() {
	}
	
	/**
	 * Constructor for void label
	 * @param image Button Image
	 */
	public ImageButton(Image image) {
		this(image, null, null);
	}
	
	/**
	 * Constructor for void image
	 * @param label Text label of the button
	 */
	public ImageButton(String label) {
		this(null, label, null);
	}
	
	/**
	 * Buid a new image button
	 * @param image Image Button Image
	 * @param label String Text label of the button
	 * @param shortcut String keyboard shortcut description (e.g.: SHIFT+A)
	 */
	public ImageButton(Image image, String label, String shortcut) {
		//REGISTER_LISTENERS
		//Insert "ImageButton register listeners"
		imageButtonMouseAdapter aMouse = new imageButtonMouseAdapter();
		this.addMouseListener(aMouse);
		actionCommand = "void";
		
		this.label = label;
		setLayout(null);
		
		img_off = image;
		img = img_off;
		
		resize();
	}
	
	/**
	 * Returns whether this Component can become the focus owner.
	 * @return boolean true
	 */
	public boolean isFocusTraversable() {
		return true;
	}
	
	/**
	 * Returns whether this Component can become the focus owner.
	 * @return boolean true
	 */
	public boolean isFocusable() {
		return true;
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
	 * Set the button label font
	 * @param f font
	 */
	public void setFont(Font f) {
		super.setFont(f);
		resize();
	}
	
	/**
	 * Set the button colors for each state
	 * @param ba background button color when mouse is off
	 * @param bb background button color when mouse is over
	 * @param bc background button color when button is clicked
	 * @param ta foreground text color when mouse is off
	 * @param tb foreground text color when mouse is over
	 * @param tc foreground text color when button is clicked
	 * @param sa shadow text color when mouse is off
	 * @param sb shadow text color when mouse is over
	 * @param sc shadow text color when button is clicked
	 */
	public void setStateColors(Color ba, Color bb, Color bc, Color ta, Color tb, Color tc, Color sa, Color sb, Color sc) {
		col_off_bck = ba;
		col_over_bck = bb;
		col_on_bck = bc;
		
		col_off_txt = ta;
		col_over_txt = tb;
		col_on_txt = tc;
		
		col_off_sdw = sa;
		col_over_sdw = sb;
		col_on_sdw = sc;
		
		col_shadow = col_off_sdw;
		
		this.setForeground(col_off_txt);
		this.setBackground(col_off_bck);
	}
	
	/**
	 * Set shadow relative position
	 * @param x horizontal pixels between text position and shadow position
	 * @param y vertical pixels between text position and shadow position
	 */
	public void setShadowPosition(int x, int y) {
		shadow_x = x;
		shadow_y = y;
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
	 * Set the button bevel width in pixels
	 * @param bb bevel size
	 */
	public void setBorderWidth(int bb) {
		if (bb > 0) {
			fShowBorder = true;
		}
		else {
			fShowBorder = false;
		}
		bevel_width = bb;
		resize();
	}
	
	/**
	 * Make the button "depress" when clicked.
	 * If false the button will behave normally,
	 * except when pushed it will give no visual feedback.
	 * @param b true or false
	 */
	public void setDrawPushedIn(boolean b) {
		fDrawPushedIn = b;
	}
	
	/**
	 * Get the label position (relative to button icon)
	 * possible values are: ImageButton.LEFT, ImageButton.RIGHT, ImageButton.TOP, ImageButton.BOTTOM
	 * @return label position
	 */
	public int getLabelPosition() {
		return pos;
	}
	
	/**
	 * Set the position of the label relative to icon
	 * @param a ImageButton.LEFT, ImageButton.RIGHT, ImageButton.TOP, ImageButton.BOTTOM
	 */
	public void setLabelPosition(int a) {
		if (a != LEFT && a != TOP && a != RIGHT && a != BOTTOM) {
			throw new IllegalArgumentException();
		}
		pos = a;
		resize();
	}
	
	/**
	 * Set the position of the block (label + image)
	 * @param a if true center the block
	 */
	public void setCenterBlock(boolean a) {
		center_block = a;
		resize();
	}
	
	/**
	 * Get the button text label
	 * @return current text label string
	 */
	public String getLabel() {
		return label;
	}
	
	/**
	 * Set the button text label
	 *  @param l button text label
	 */
	public void setLabel(String l) {
		label = l;
		resize();
		repaint();
	}
	
	/**
	 * Set the audio clips to play for mouse-on and mouse over events.
	 *  @param a audio clip when button is clicked
	 *  @param b audio clip when mouse is over
	 */
	public void setAudioStateClips(AudioClip a, AudioClip b) {
		audio_click = a;
		audio_over = b;
	}
	
	/**
	 * Set the padding in pixels.
	 * Padding is the distance between the button border and button components.
	 * @param p array of button paddings in pixels (Left, Right, Top, Bottom)
	 */
	public void setPadding(int[] p) {
		padding = p;
		resize();
		repaint();
	}
	
	/**
	 * Get button paddings in pixels as array (Left, Right, Top, Bottom).
	 * Padding is the distance between the button border and button components.
	 * @return padding in pixels
	 */
	public int[] getPadding() {
		return padding;
	}
	
	/**
	 * Set the gap in pixels between the label (if any) and image.
	 * @param g pixels
	 */
	public void setImageLabelGap(int g) {
		gap = g;
		resize();
		repaint();
	}
	
	/**
	 * Get the gap in pixels between the label (if any) and image.
	 * @return gap in pixels
	 */
	public int getImageLabelGap() {
		return gap;
	}
	
	/**
	 * Set the state images (one image for each button/mouse state)
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
		repaintImages(bufferGraphics);
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
		repaintImages(bufferGraphics);
	}
	
	/**
	 * Set the current arrow image to be shown.
	 * @param i Button Arrow Image
	 */
	public void setArrowImage(Image i) {
		Image previous_img = img_arrow;
		img_arrow = i;
		if ( (i == null) || (previous_img == null) ||
				(i.getWidth(this) != img_arrow.getWidth(this) ||
						i.getHeight(this) != img_arrow.getHeight(this))) {
			resize(); // If new image has a different size, then resize
		}
		repaintImages(bufferGraphics);
	}
	
	/**
	 * Set the button arrow images (one image for each button/mouse state).
	 * The arrow image indicate the submenu presence.
	 * @param aoff arrow image for off status
	 * @param aover arrow image for over status
	 * @param aon arrow image for on status
	 * @param pos arrow image position
	 */
	public void setArrowImages(Image aoff, Image aover, Image aon, int pos) {
		img_arrow_off = aoff;
		img_arrow_over = aover;
		img_arrow_on = aon;
		img_arrow = img_arrow_off; //set current image
		if ( (pos != LEFT) && (pos != RIGHT)) {
			throw new IllegalArgumentException();
		}
		arrow_pos = pos;
		resize();
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
				isButtonPushed = false;
				super.setEnabled(true);
				repaint();
			}
		}
		else {
			if (isEnabled()) {
				isButtonPushed = false;
				//super.setEnabled(false);
				repaint();
			}
		}
	}
	
	/**
	 * Resize the size of button and components.
	 */
	public void resize() {
		Font f = getFont();
		FontMetrics fm = null; //used to measure font
		int fdescent = 0, fascent = 0, fleading = 0; //string metrics
		int lw = 0, lh = 0; //label size
		int num_lines = 1; //number of lines in label
		String lines[] = new String[num_lines];
		int line_widths[] = new int[num_lines];
		
		int max_width = 0; //max width of a label row
		int rowheight = 0;
		
		//calculate label size
		if ( (f != null) && (label != null)) {
			fm = getFontMetrics(f);
			fdescent = fm.getMaxDescent();
			fascent = fm.getMaxAscent();
			fleading = fm.getLeading(); //default distance between text lines
			rowheight = fascent + fdescent + fleading;
			
			// parse the string , '\' is the line separator
			StringTokenizer st = new StringTokenizer(label, "\\");
			num_lines = st.countTokens();
			lines = new String[num_lines];
			line_widths = new int[num_lines];
			for (int i = 0; i < num_lines; i++) {
				lines[i] = st.nextToken();
				line_widths[i] = fm.stringWidth(lines[i]);
				if (line_widths[i] > max_width) {
					max_width = line_widths[i];
				}
			}
			
			lw = max_width + Math.abs(shadow_x); // label width
			lh = (rowheight * num_lines) - fleading + Math.abs(shadow_y) + 1; // label height
		}
		
		//consider icon image size
		int iw = 0, ih = 0, i_gap = 0;
		if (img != null) {
			iw = (int) (img.getWidth(this) * imageScale);
			ih = (int) (img.getHeight(this) * imageScale);
			i_gap = gap;
		}
		
		//consider arrow image size
		int aw = 0, ah = 0, a_gap = 0;
		if (img_arrow != null) {
			aw = (int) (img_arrow.getWidth(this));
			ah = (int) (img_arrow.getHeight(this));
			a_gap = gap;
		}
		
		//calculate button size
		int w, h;
		w = iw + aw + padding[0] + padding[1] + (2 * bevel_width);
		h = Math.max(ih, ah) + padding[2] + padding[3] + (2 * bevel_width);
		
		if (fm != null) {
			if (pos == LEFT || pos == RIGHT) {
				w += i_gap + a_gap + lw;
				h = Math.max(h, lh + padding[2] + padding[3] + (2 * bevel_width));
			}
			else {
				h += i_gap + lh;
				w = Math.max(w, lw + padding[0] + padding[1] + (2 * bevel_width));
			}
		}
		
		//consider background image size
		int bw = 0, bh = 0;
		if (img_bg != null) {
			bw = (int) (img_bg.getWidth(this) * imageScale);
			bh = (int) (img_bg.getHeight(this) * imageScale);
			h = Math.max(h, bh);
			w = Math.max(w, bw);
		}
		
		setSize(w, h);
	}
	
	/**
	 * Redraw the button images
	 * @param gbuffer current graphics context
	 */
	protected void repaintImages(Graphics gbuffer) {
		if (gbuffer == null) {
			return;
		}
		
		// paint background image
		if (img_bg != null) {
			gbuffer.drawImage(img_bg, bx, by, this);
		}
		
		// paint image icon
		if (img != null) {
			if (imageScale == 1) {
				gbuffer.drawImage(isEnabled() ? img : inactive_img, ix, iy, this);
			}
			else {
				gbuffer.drawImage(isEnabled() ? img : inactive_img, ix, iy, iw, ih, this);
			}
		}
		
		// paint arrow image
		if (img_arrow != null) {
			gbuffer.drawImage(img_arrow, ax, ay, this);
		}
	}
	
	/**
	 * Draw button elements (border, image and label) at calculated positions
	 * @param g the graphic area when diplay button elements
	 */
	public synchronized void paint(Graphics g) {
		
		if (!isEnabled() && inactive_img == null) {
			//inactive_img = createDisabledImage(img, this);
			inactive_img = img;
		}
		
		//get button size
		int w = getSize().width;
		int h = getSize().height;
		
		//DOUBLE BUFFERING:
		// Create an offscreen image to draw on
		offscreen = createImage(w, h);
		// by doing this everything that is drawn by bufferGraphics will be written on the offscreen image.
		bufferGraphics = offscreen.getGraphics();
		
		bufferGraphics.clearRect(0, 0, w, h); //clean area
		//bufferGraphics.setPaintMode();
		
		// display border
		if (fShowBorder) {
			ButtonBevel r = new ButtonBevel(this, 0, 0, w, h, bevel_width);
			if (isButtonPushed && fDrawPushedIn) {
				r.setDrawingMode(ButtonBevel.IN);
			}
			else {
				r.setDrawingMode(ButtonBevel.OUT);
			}
			r.paint(bufferGraphics);
		}
		
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
		
		//get image icon size
		iw = 0;
		ih = 0;
		int i_gap = 0;
		
		//image icon measures
		if (img != null) {
			iw = (int) (img.getWidth(this) * imageScale);
			ih = (int) (img.getHeight(this) * imageScale);
			if (label != null) {
				i_gap = gap;
			}
		}
		
		//get arrow image size
		aw = 0;
		ah = 0;
		int a_gap = 0;
		
		//arrow image measures
		if (img_arrow != null) {
			aw = (int) (img_arrow.getWidth(this));
			ah = (int) (img_arrow.getHeight(this));
			a_gap = gap;
		}
		
		FontMetrics fm = null; //used to measure font
		int fdescent = 0, fascent = 0, fleading = 0; //string metrics
		int lx = 0, ly = 0; //label position coordinates
		int lw = 0, lh = 0; //label size
		int num_lines = 1; //number of lines in label
		String lines[] = new String[num_lines];
		int line_widths[] = new int[num_lines];
		int max_width = 0; //max width of a label row
		int rowheight = 0;
		
		//calculate label size
		if (label != null) {
			fm = getFontMetrics(getFont());
			fdescent = fm.getMaxDescent();
			fascent = fm.getMaxAscent();
			fleading = fm.getLeading(); //default distance between text lines
			rowheight = fascent + fdescent + fleading;
			
			// parse the string , '\' is the line separator
			StringTokenizer st = new StringTokenizer(label, "\\");
			num_lines = st.countTokens();
			lines = new String[num_lines];
			line_widths = new int[num_lines];
			for (int i = 0; i < num_lines; i++) {
				lines[i] = st.nextToken();
				line_widths[i] = fm.stringWidth(lines[i]);
				if (line_widths[i] > max_width) {
					max_width = line_widths[i];
				}
			}
			
			lw = max_width + Math.abs(shadow_x); // label width
			lh = (rowheight * num_lines) - fleading + Math.abs(shadow_y); // label height
		}
		
		//distances to border
		int borderL = padding[0] + bevel_width + ( (isButtonPushed && fDrawPushedIn) ? 1 : 0);
		int borderR = padding[1] + bevel_width - ( (isButtonPushed && fDrawPushedIn) ? 1 : 0);
		int borderT = padding[2] + bevel_width + ( (isButtonPushed && fDrawPushedIn) ? 1 : 0);
		int borderB = padding[3] + bevel_width - ( (isButtonPushed && fDrawPushedIn) ? 1 : 0);
		
		//horizontal position
		if (arrow_pos == RIGHT) {
			int xblockpos = (int) ( (w + borderL - borderR - aw - a_gap - iw - i_gap - lw) / 2);
			if (pos == RIGHT) {
				if (center_block) {
					ix = xblockpos;
				}
				else {
					ix = borderL;
				}
				lx = ix + iw + i_gap;
			}
			else {
				if (pos == LEFT) {
					if (center_block) {
						lx = xblockpos;
					}
					else {
						lx = w - borderL - aw - a_gap - iw - i_gap - lw;
					}
					ix = lx + lw + i_gap;
				}
				else { // TOP or BOTTOM
					//center image horizzontally
					ix = (int) ( (w + borderL - borderR - aw - a_gap - iw) / 2);
					lx = (int) ( (w + borderL - borderR - aw - a_gap - lw) / 2);
				}
			}
		}
		else { // arrow in left position
			int xblockpos = (int) ( (w + borderL - borderR + aw + a_gap - iw - i_gap - lw) / 2);
			if (pos == RIGHT) {
				if (center_block) {
					ix = xblockpos;
				}
				else {
					ix = borderL + aw + a_gap;
				}
				lx = ix + iw + i_gap;
			}
			else {
				if (pos == LEFT) {
					if (center_block) {
						lx = xblockpos;
					}
					else {
						lx = w - borderR - iw - i_gap - lw;
					}
					ix = lx + lw + i_gap;
				}
				else { // TOP or BOTTOM
					//center image horizzontally
					ix = (int) ( (w + borderL - borderR + aw + a_gap - iw) / 2);
					lx = (int) ( (w + borderL - borderR + aw + a_gap - lw) / 2);
				}
			}
		}
		// vertical coordinates
		if (pos == TOP) {
			//coordinate of first label string row
			//fascent translation because g.drawString behaviuor
			ly = (int) ( (h + borderT - borderB - lh - i_gap - ih) / 2) + fascent;
			iy = ly - fascent + lh + i_gap;
		}
		else {
			if (pos == BOTTOM) {
				iy = (int) ( (h + borderT - borderB - lh - i_gap - ih) / 2);
				ly = iy + ih + i_gap + fascent;
			}
			else { // LEFT or RIGHT
				//center image vertically
				iy = (int) ( (h + borderT - borderB - ih) / 2);
				ly = (int) ( (h + borderT - borderB - lh) / 2) + fascent;
			}
		}
		
		//calculate arrow image position
		ay = (int) ( (h + borderT - borderB - ah) / 2);
		if (arrow_pos == RIGHT) {
			ax = w - borderR - aw;
		}
		else {
			ax = borderL;
		}
		
		repaintImages(bufferGraphics);
		
		// draw label
		if (label != null) {
			int ltx, lty, lsx, lsy;
			//calculate text and shadow coordinates
			if (shadow_x < 0) {
				ltx = lx - shadow_x;
				lsx = lx;
			}
			else {
				ltx = lx;
				lsx = lx + shadow_x;
			}
			if (shadow_y < 0) {
				lty = ly - shadow_y;
				lsy = ly;
			}
			else {
				lty = ly;
				lsy = ly + shadow_y;
			}
			
			//draw LABEL
			for (int i = 0; i < num_lines; i++) {
				int tsx = lsx;
				int tsy = lsy + (rowheight * i);
				int ttx = ltx;
				int tty = lty + (rowheight * i);
				
				if (pos == RIGHT) {
					tsx = lsx;
					ttx = ltx;
				}
				else {
					if (pos == LEFT) {
						tsx = lsx + lw - line_widths[i];
						ttx = ltx + lw - line_widths[i];
					}
					else { // TOP or BOTTOM
						tsx = lsx + ( (lw - line_widths[i]) / 2);
						ttx = ltx + ( (lw - line_widths[i]) / 2);
					}
				}
				bufferGraphics.setColor(col_shadow);
				bufferGraphics.drawString(lines[i], tsx, tsy); //draw shadow
				
				bufferGraphics.setColor(getForeground());
				bufferGraphics.drawString(lines[i], ttx, tty); //draw label
			}
			
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
	public synchronized void addActionListener(ActionListener l) {
		actionListener = AWTEventMulticaster.add(actionListener, l);
	}
	
	/**
	 * Removes the specified action listener so it no longer receives
	 * action events from this button.
	 * @param l the action listener
	 */
	public synchronized void removeActionListener(ActionListener l) {
		actionListener = AWTEventMulticaster.remove(actionListener, l);
	}
	
	/**
	 * Fire an action event to the listeners.
	 * @param actiontype ActionEvent.ACTION_PERFORMED,
	 */
	protected void fireActionEvent(int actiontype) {
		if (actionListener != null) {
			actionListener.actionPerformed(new ActionEvent(this, actiontype, actionCommand));
		}
	}
	
	// Fuctions to define button states (on, over, off):
	
	/**
	 * Gets called when the mouse button is pressed.
	 * Repaint button setting images and colors.
	 */
	protected void imageButton_MousePushed() {
		isButtonPushed = true;
		setButtonComponents(audio_click, col_on_txt, col_on_bck, col_on_sdw, img_bg_on, img_on, img_arrow_on);
	}
	
	/**
	 * Gets called when the mouse crosses into the button area.
	 * Repaint button setting images and colors.
	 * @param sound_on boolean to turn off sound
	 */
	protected void imageButton_MouseOver(boolean sound_on) {
		isButtonPushed = false;
		AudioClip sound = null;
		if (sound_on) {
			sound = audio_over;
		}
		setButtonComponents(sound, col_over_txt, col_over_bck, col_over_sdw, img_bg_over, img_over, img_arrow_over);
	}
	
	/**
	 * Gets called when the mouse crosses out of the button area.
	 * Repaint button setting images and colors.
	 */
	protected void imageButton_MouseOff() {
		isButtonPushed = false;
		AudioClip sound = null;
		setButtonComponents(sound, col_off_txt, col_off_bck, col_off_sdw, img_bg_off, img_off, img_arrow_off);
	}
	
	/**
	 * Set and repaint button components
	 * @param sound audioclip to play
	 * @param foreground foreground color
	 * @param background background color
	 * @param shadow text shadow color
	 * @param bg background image
	 * @param icon image icon
	 * @param arrow image arrow
	 */
	private void setButtonComponents(AudioClip sound, Color foreground,
			Color background, Color shadow, Image bg,
			Image icon, Image arrow) {
		if (sound != null) {
			sound.play();
		}
		this.setForeground(foreground);
		this.setBackground(background);
		col_shadow = shadow;
		if (bg != null) {
			setBgImage(bg);
		}
		if (icon != null) {
			setImage(icon);
		}
		if (arrow != null) {
			setArrowImage(arrow);
		}
		repaint();
	}
	
//	-----------------------------------------------------------------------------
	
	/**
	 * Inner class for handling mouse events.
	 * Insert "ImageButton Mouse Handling"
	 */
	class imageButtonMouseAdapter
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
			imageButton_MouseOver(true);
			current_action = MouseEvent.MOUSE_ENTERED;
		}
		
		/**
		 * Invoked when the mouse exits a component.
		 * @param event MouseEvent
		 */
		public void mouseExited(MouseEvent event) {
			imageButton_MouseOff();
			current_action = MouseEvent.MOUSE_EXITED;
		}
		
		/**
		 * Invoked when a mouse button has been pressed on a component.
		 * @param event MouseEvent
		 */
		public void mousePressed(MouseEvent event) {
			imageButton_MousePushed();
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
				imageButton_MouseOff();
			}
			else {
				imageButton_MouseOver(false);
			}
			current_action = MouseEvent.MOUSE_RELEASED;
		}
	} //end class imageButtonMouseAdapter
	
//	-----------------------------------------------------------------------------
	
}

//=============================================================================
//END OF FILE
//=============================================================================
