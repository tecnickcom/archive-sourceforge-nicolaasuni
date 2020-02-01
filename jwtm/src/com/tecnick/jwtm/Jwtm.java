/*
 * @(#)jwtm.java	2003-09-22
 *
 * Copyright 2002-2005 Tecnick.com S.r.l. - All rights reserved.
 */

package com.tecnick.jwtm;

import java.applet.*;
import java.io.*;
import java.net.*;

import java.awt.*;
import java.awt.event.*;

import netscape.javascript.*;

//-----------------------------------------------------------------------------
/**
 * Title: JWTM (Web Tree Menu)<br>
 * Description: Applet to display tree menus<br>
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
public class Jwtm
extends Applet {
	
	/**
	 * serialVersionUID
	 */
	private static final long serialVersionUID = 7233702908126976235L;
	
	/**
	 * Software version
	 */
	private static final String JWTM_VERSION = "1.1.003";
	
	/**
	 * array to contain tree item elements (cliccable buttons)
	 */
	ImageButton[] ab = null;
	
	/**
	 * array to contain tree connectors elements
	 */
	ConnectorButton[] cb = null;
	
	/**
	 * this applet.
	 * (only final variables can be used in inner anonymous classes)
	 */
	final Applet a = this;
	
	/**
	 * layout for [connector + button] block
	 */
	private FlowLayout item_layout = new FlowLayout();
	
	/**
	 * layout for the tree
	 */
	private VFlowLayout tree_layout = new VFlowLayout();
	
	/**
	 * scroll pane to scroll the tree structure
	 */
	private ScrollPane scroll_pane = new ScrollPane(ScrollPane.SCROLLBARS_AS_NEEDED);
	
	/**
	 * Panel with background
	 */
	private BgPanel tree_pane = new BgPanel();
	
	/**
	 * number of buttons
	 */
	private int num_buttons = 0;
	
	/**
	 * button font
	 */
	private Font abfont;
	
	// applet parameters <param>
	
	/**
	 * Menu direction (LEFT=left-to-right or RIGHT=right-to-left)
	 */
	private int p_menu_direction;
	
	//TREE CONNECTORS
	
	//background images for connectors
	
	/**
	 * button image for off status
	 */
	private String p_connector_bck_img_off;
	
	/**
	 * button image for mouse-over status
	 */
	private String p_connector_bck_img_over;
	
	/**
	 * button image for mouse-click status
	 */
	private String p_connector_bck_img_on;
	
	//image symbols for tree structure
	
	/**
	 * connector icon when mouse is off (closed)
	 */
	private Image p_img_node_off;
	
	/**
	 * connector icon when mouse is on (open)
	 */
	private Image p_img_node_on;
	
	/**
	 * connector icon when mouse is over
	 */
	private Image p_img_node_over;
	
	//default connectors sounds
	
	/**
	 * mouse over button  sound
	 */
	private String p_connector_sound_over;
	
	/**
	 * mouse click button sound
	 */
	private String p_connector_sound_click;
	
	//connector background color
	
	/**
	 * button off background color
	 */
	private Color p_connector_bck_col_off;
	
	/**
	 * button over background color
	 */
	private Color p_connector_bck_col_over;
	
	/**
	 * button on background color
	 */
	private Color p_connector_bck_col_on;
	
	//connector line color
	
	/**
	 * button off background color
	 */
	private Color p_connector_line_col_off;
	
	/**
	 * button over background color
	 */
	private Color p_connector_line_col_over;
	
	/**
	 * button on background color
	 */
	private Color p_connector_line_col_on;
	
	/**
	 * connector line width in pixels (0 = no line)
	 */
	private int p_connector_line_width;
	
	/**
	 * connector minimum width
	 */
	private int p_connector_min_width;
	
	//BUTTONS
	
	/**
	 * array of button paddings (Left, Right, Top, Bottom)
	 * pixels between button border and first element (image or label)
	 */
	private int[] p_default_padding = new int[4];
	
	/**
	 * default vertical margin between buttons
	 * @since 1.1.000
	 */
	private int p_default_vmargin;
	
	/**
	 * default horizontal margin between buttons
	 * @since 1.1.000
	 */
	private int p_default_hmargin;
	
	/**
	 * distance between button objects (image, label)
	 */
	private int p_default_gap;
	
	/**
	 * if true enable button pushed status
	 */
	private boolean p_default_pushed;
	
	/**
	 * label position respect the image (LEFT, RIGHT, TOP, BOTTOM)
	 */
	private int p_default_label_position;
	
	/**
	 * if true center the block (image+label) on the button
	 */
	private boolean p_default_center_block;
	
	/**
	 * button bevel width (0 = no bevel)
	 */
	private int p_default_border_width;
	
	/**
	 * default frame target
	 */
	private String p_default_target;
	
	/**
	 * default disabled message
	 */
	private String p_disabled_msg;
	
	//default colors
	
	/**
	 * applet background color
	 */
	private Color p_background_col;
	
	/**
	 * applet background imag
	 */
	private Image p_background_img = null;
	
	/**
	 * applet background image position (TILE, CENTER, STRETCH, LEFT, RIGHT)
	 */
	private int p_background_img_pos;
	
	/**
	 * button off background color
	 */
	private Color p_default_colbck_off;
	
	/**
	 * button over background color
	 */
	private Color p_default_colbck_over;
	
	/**
	 * button on background color
	 */
	private Color p_default_colbck_on;
	
	/**
	 * button off text color
	 */
	private Color p_default_coltxt_off;
	
	/**
	 * button over text color
	 */
	private Color p_default_coltxt_over;
	
	/**
	 * button on text color
	 */
	private Color p_default_coltxt_on;
	
	/**
	 * button off text shadow color
	 */
	private Color p_default_colsdw_off;
	
	/**
	 * button over text shadow color
	 */
	private Color p_default_colsdw_over;
	
	/**
	 * button on text shadow color
	 */
	private Color p_default_colsdw_on;
	
	/**
	 * text shadow relative horizontal position
	 */
	private int p_default_shadow_x;
	
	/**
	 * text shadow relative vertical position
	 */
	private int p_default_shadow_y;
	
	//default background images for buttons
	
	/**
	 * button image for off status
	 */
	private String p_default_bck_img_off;
	
	/**
	 * button image for mouse-over status
	 */
	private String p_default_bck_img_over;
	
	/**
	 * button image for mouse-click status
	 */
	private String p_default_bck_img_on;
	
	//default buttons icons
	
	/**
	 * button image icon off status
	 */
	private String p_default_icon_off;
	
	/**
	 * button image icon over status
	 */
	private String p_default_icon_over;
	
	/**
	 * button image icon on status
	 */
	private String p_default_icon_on;
	
	//fonts
	
	//button font
	
	/**
	 * button font name
	 */
	private String p_default_main_font;
	
	/**
	 * button style (PLAIN, BOLD, ITALIC, BOLD+ITALIC)
	 */
	private int p_default_main_font_style;
	
	/**
	 * font size
	 */
	private int p_default_main_font_size;
	
	/**
	 * charset encoding
	 */
	private String p_default_encoding;
	
	/**
	 * html page encoding
	 */
	private String p_page_encoding;
	
	//default sounds
	
	/**
	 * mouse over button  sound
	 */
	private String p_default_sound_over;
	
	/**
	 * mouse click button sound
	 */
	private String p_default_sound_click;
	
	/**
	 * url of text file containing menu data (alternative to parameters)
	 */
	private String p_data_file;
	
	// menu items data ------
	
	/**
	 * menu id
	 */
	private int[] p_id;
	
	/**
	 * menu parent id (id of node)
	 */
	private int[] p_subid;
	
	/**
	 * true if is a node
	 */
	private boolean[] p_node;
	
	/**
	 * true if is enabled
	 */
	private boolean[] p_enabled;
	
	/**
	 * remember elements indentation level
	 */
	private int[] p_level;
	
	/**
	 * true when element is the last child of tree branch
	 */
	private boolean[] p_last_node;
	
	/**
	 * remember wich vertical connector to draw next to the element node
	 */
	private boolean[][] p_v_connectors;
	
	/**
	 * menu link
	 */
	private String[] p_link;
	
	/**
	 * frame target
	 */
	private String[] p_target;
	
	/**
	 * menu item charset encoding
	 */
	private String[] p_encoding;
	
	/**
	 * menu item name
	 */
	private String[] p_name;
	
	/**
	 * menu item description
	 */
	private String[] p_description;
	
	//icons
	
	/**
	 * button image icon off status
	 */
	private String[] p_icon_off;
	
	/**
	 * button image icon over status
	 */
	private String[] p_icon_over;
	
	/**
	 * button image icon on status
	 */
	private String[] p_icon_on;
	
	//colors
	
	/**
	 * button off background color
	 */
	private Color[] p_colbck_off;
	
	/**
	 * button over background color
	 */
	private Color[] p_colbck_over;
	
	/**
	 * button on background color
	 */
	private Color[] p_colbck_on;
	
	/**
	 * button off text color
	 */
	private Color[] p_coltxt_off;
	
	/**
	 * button over text color
	 */
	private Color[] p_coltxt_over;
	
	/**
	 * button on text color
	 */
	private Color[] p_coltxt_on;
	
	/**
	 * button off text shadow color
	 */
	private Color[] p_colsdw_off;
	
	/**
	 * button over text shadow color
	 */
	private Color[] p_colsdw_over;
	
	/**
	 * button on text shadow color
	 */
	private Color[] p_colsdw_on;
	
	/**
	 * text shadow relative horizontal position
	 */
	private int[] p_shadow_x;
	
	/**
	 * text shadow relative vertical position
	 */
	private int[] p_shadow_y;
	
	//background images
	
	/**
	 * button image for off status
	 */
	private String[] p_bck_img_off;
	
	/**
	 * button image for mouse-over status
	 */
	private String[] p_bck_img_over;
	
	/**
	 * button image for maouse-click status
	 */
	private String[] p_bck_img_on;
	
	//sounds
	
	/**
	 * mouse over button  sound
	 */
	private String[] p_sound_over;
	
	/**
	 * mouse click button sound
	 */
	private String[] p_sound_click;
	
	/**
	 * if true enable button pushed status
	 */
	private boolean[] p_pushed;
	
	/**
	 * array of button paddings (Left, Right, Top, Bottom)
	 * pixels between left button border and first element (image or label)
	 */
	private int[][] p_padding;
	
	/**
	 * distance between button objects (image and label)
	 */
	private int[] p_gap;
	
	/**
	 * label position respect the image (LEFT, RIGHT, TOP, BOTTOM)
	 */
	private int[] p_label_position;
	
	/**
	 * if true center the block (image+label) on the button
	 */
	private boolean[] p_center_block;
	
	/**
	 * button bevel width (0 = no bevel)
	 */
	private int[] p_border_width;
	
	//button font
	
	/**
	 * button font name
	 */
	private String[] p_font;
	
	/**
	 * button style (PLAIN, BOLD, ITALIC, BOLD+ITALIC)
	 */
	private int[] p_font_style;
	
	/**
	 * font size
	 */
	private int[] p_font_size;
	
	/**
	 * keyboard shortcut to activate button (e.g: SHIFT+A)
	 */
	private String[] p_shortcut;
	
	// -----------------------------------------------------------------------------
	
//	When a menu item is clicked, jump to corresponding URL
	ActionListener jumpToUrl = new ActionListener() {
		public void actionPerformed(ActionEvent e) {
			int index = Integer.parseInt(e.getActionCommand());
			if (p_enabled[index]) {
				if (p_description[index].length() > 0) {
					showStatus(p_description[index] + " [" + p_shortcut[index] + "]"); //display status message
				}
				//check for javascript schema (needed to support javascrit calls on old JVMs)
				if ( (p_link[index].length() > 11) && (p_link[index].substring(0, 11).toLowerCase().compareTo("javascript:") == 0)) {
					try {
						JSObject jsroot = JSObject.getWindow(a);
						jsroot.eval(p_link[index].substring(11));
					}
					catch (Exception ej) {
						// this fix a mozilla bug
						a.getAppletContext().showDocument(setURL(p_link[index]), p_target[index]);
					}
				}
				else {
					a.getAppletContext().showDocument(setURL(p_link[index]), p_target[index]); // load the URL on the target browser window
				}
			}
			else {
				if (p_disabled_msg.length() > 0) {
					showStatus(p_disabled_msg); //display disabled status message
				}
			}
		}
	};
	
	// -----------------------------------------------------------------------------
	/**
	 * Computes full, canonical URL from a relative specification.
	 * @param link string containing URL
	 * @return canonical URL
	 */
	private URL setURL(String link) {
		if (link.length() <= 0) {
			return null;
		}
		URL url = null;
		try {
			url = new URL(getDocumentBase(), link);
		}
		catch (MalformedURLException e) {
			System.out.println("ERROR - Malformed URL: " + link);
		}
		return url;
	}
	
	// -----------------------------------------------------------------------------
	/**
	 * Convert string to specified encoding.
	 * @param original original string
	 * @param encoding_in input encoding table
	 * @param encoding_out output encoding table
	 * @return encoded string
	 */
	private String getEncodedString(String original, String encoding_in, String encoding_out) {
		String encoded_string;
		if (encoding_in.compareTo(encoding_out) != 0) {
			byte[] encoded_bytes;
			try {
				encoded_bytes = original.getBytes(encoding_in);
			}
			catch (UnsupportedEncodingException e) {
				System.out.println("Unsupported Charset: " + encoding_in);
				return original;
			}
			try {
				encoded_string = new String(encoded_bytes, encoding_out);
				return encoded_string;
			}
			catch (UnsupportedEncodingException e) {
				//e.printStackTrace();
				System.out.println("Unsupported Charset: " + encoding_out);
				return original;
			}
		}
		return original;
	}
	
	// -----------------------------------------------------------------------------
	/**
	 * Return the int associated to string position name.
	 * @param posname name of position
	 * @return position int code
	 */
	private int getPositionCode(String posname) {
		String pname = posname.toUpperCase().trim();
		if (pname.compareTo("LEFT") == 0) {
			return ImageButton.LEFT;
		}
		if (pname.compareTo("RIGHT") == 0) {
			return ImageButton.RIGHT;
		}
		if (pname.compareTo("TOP") == 0) {
			return ImageButton.TOP;
		}
		if (pname.compareTo("BOTTOM") == 0) {
			return ImageButton.BOTTOM;
		}
		if (pname.compareTo("CENTER") == 0) {
			return ImageButton.CENTER;
		}
		if (pname.compareTo("TILE") == 0) {
			return BgPanel.TILE;
		}
		if (pname.compareTo("STRETCH") == 0) {
			return BgPanel.STRETCH;
		}
		
		return ImageButton.RIGHT; //default return
	}
	
//	-----------------------------------------------------------------------------
	/**
	 * Return the int associated to font style.
	 * @param stylename name of style
	 * @return style int code
	 */
	private int getFontStyleCode(String stylename) {
		String sname = stylename.toUpperCase().trim();
		int return_value = 0;
		try {
			if (sname.indexOf("PLAIN") >= 0) {
				return_value = Font.PLAIN;
			}
			if (sname.indexOf("BOLD") >= 0) {
				return_value += Font.BOLD;
			}
			if (sname.indexOf("ITALIC") >= 0) {
				return_value += Font.ITALIC;
			}
		}
		catch (NullPointerException ne) {
			return Font.PLAIN;
		}
		return return_value; //default return
	}
	
//	-----------------------------------------------------------------------------
	/**
	 * Load an image.
	 * @param a applet
	 * @param file image file URL
	 * @return loadImage
	 */
	static private Image loadImage(Applet a, URL file) {
		if (file == null) {
			return null;
		}
		Image i = null;
		MediaTracker t = new MediaTracker(a);
		try {
			i = a.getImage(file);
			t.addImage(i, 0);
		}
		catch (Exception ee) {
			System.out.println("Unable to load Image: " + file);
		}
		try {
			t.waitForAll();
		}
		catch (InterruptedException ee) {}
		return i;
	}
	
//	-----------------------------------------------------------------------------
	/**
	 * Get applet parameter value, return default if void.
	 * @param key name of parameter to read
	 * @param def default value
	 * @return parameter value or default
	 */
	private String getParameter(String key, String def) {
		String param_value = getParameter(key);
		if ( (param_value != null) && (param_value.length() > 0)) {
			return param_value;
		}
		return def;
	}
	
	/**
	 * Count the number of buttons
	 * @return int number of specified buttons
	 */
	private synchronized int countButtons() {
		int i = 1;
		while (getParameter("id" + String.valueOf(i)) != null) {
			i++;
		}
		return i;
	}
	
	// -----------------------------------------------------------------------------
	/**
	 * Return "def" if "str" is null or empty
	 * @param str value to return if not null
	 * @param def default value to return
	 * @return def or str by case
	 */
	private String getDefaultValue(String str, String def) {
		if ( (str != null) && (str.length() > 0)) {
			return str;
		}
		return def;
	}
	
//	-----------------------------------------------------------------------------
	/**
	 * Get the applet parameters.
	 */
	private void getParameters() {
		try {
			p_menu_direction = getPositionCode(this.getParameter("menu_direction", "LEFT"));
			
			//image symbols for tree structure
			p_img_node_off = loadImage(this, setURL(this.getParameter("img_node_off", "")));
			p_img_node_on = loadImage(this, setURL(this.getParameter("img_node_on", "")));
			p_img_node_over = loadImage(this, setURL(this.getParameter("img_node_over", "")));
			
			p_connector_line_width = Integer.parseInt(this.getParameter("connector_line_width", "1"));
			p_connector_min_width = Integer.parseInt(this.getParameter("connector_min_width", "21"));
			
			p_default_label_position = getPositionCode(this.getParameter("default_label_position", "RIGHT"));
			p_default_center_block = Boolean.valueOf(this.getParameter("default_center_block", "false")).booleanValue();
			
			String[] str_default_padding = splitData(this.getParameter("default_padding", "2:2:2:2"), ':', 4);
			p_default_padding[0] = 2; // set default value
			for (int i = 0; i < 4; i++) {
				if (str_default_padding[i].length() > 0) {
					p_default_padding[i] = Integer.parseInt(str_default_padding[i]);
				}
				else {
					p_default_padding[i] = p_default_padding[0]; // to be compatible with JDDM < 1.1.000
				}
			}
			p_default_vmargin = Integer.parseInt(this.getParameter("default_vmargin", "0")); // since 1.1.000
			p_default_hmargin = Integer.parseInt(this.getParameter("default_hmargin", "0")); // since 1.1.000
			
			p_default_gap = Integer.parseInt(this.getParameter("default_gap", "2"));
			p_default_border_width = Integer.parseInt(this.getParameter("default_border_width", "2"));
			p_default_pushed = Boolean.valueOf(this.getParameter("default_pushed", "true")).booleanValue();
			
			p_default_sound_over = this.getParameter("default_sound_over", "");
			p_default_sound_click = this.getParameter("default_sound_click", "");
			
			//default connectors sounds
			p_connector_sound_over = this.getParameter("connector_sound_over", "");
			p_connector_sound_click = this.getParameter("connector_sound_click", "");
			
			p_background_img = loadImage(this, setURL(this.getParameter("background_img", "")));
			
			p_background_img_pos = getPositionCode(this.getParameter("background_img_pos", "TILE"));
			
			p_background_col = new Color(Integer.parseInt(this.getParameter("background_col", "FFFFFF"), 16));
			p_default_colbck_off = new Color(Integer.parseInt(this.getParameter("default_colbck_off", "FFFFFF"), 16));
			p_default_colbck_over = new Color(Integer.parseInt(this.getParameter("default_colbck_over", "BBDDFF"), 16));
			p_default_colbck_on = new Color(Integer.parseInt(this.getParameter("default_colbck_on", "FFAAAA"), 16));
			
			//connector background color
			p_connector_bck_col_off = new Color(Integer.parseInt(this.getParameter("connector_bck_col_off", "FFFFFF"), 16));
			p_connector_bck_col_over = new Color(Integer.parseInt(this.getParameter("connector_bck_col_over", "FFFFAA"), 16));
			p_connector_bck_col_on = new Color(Integer.parseInt(this.getParameter("connector_bck_col_on", "FFFFFF"), 16));
			
			p_default_coltxt_off = new Color(Integer.parseInt(this.getParameter("default_coltxt_off", "000000"), 16));
			p_default_coltxt_over = new Color(Integer.parseInt(this.getParameter("default_coltxt_over", "000000"), 16));
			p_default_coltxt_on = new Color(Integer.parseInt(this.getParameter("default_coltxt_on", "000000"), 16));
			
			p_default_colsdw_off = new Color(Integer.parseInt(this.getParameter("default_colsdw_off", "DDDDDD"), 16));
			p_default_colsdw_over = new Color(Integer.parseInt(this.getParameter("default_colsdw_over", "DDDDDD"), 16));
			p_default_colsdw_on = new Color(Integer.parseInt(this.getParameter("default_colsdw_on", "DDDDDD"), 16));
			
			p_default_shadow_x = Integer.parseInt(this.getParameter("default_shadow_x", "0"));
			p_default_shadow_y = Integer.parseInt(this.getParameter("default_shadow_y", "0"));
			
			//connector line color
			p_connector_line_col_off = new Color(Integer.parseInt(this.getParameter("connector_line_col_off", "CCCCCC"), 16));
			p_connector_line_col_over = new Color(Integer.parseInt(this.getParameter("connector_line_col_over", "CCCCCC"), 16));
			p_connector_line_col_on = new Color(Integer.parseInt(this.getParameter("connector_line_col_on", "CCCCCC"), 16));
			
			p_default_bck_img_off = this.getParameter("default_bck_img_off", "");
			p_default_bck_img_over = this.getParameter("default_bck_img_over", "");
			p_default_bck_img_on = this.getParameter("default_bck_img_on", "");
			
			//background images for connectors
			p_connector_bck_img_off = this.getParameter("connector_bck_img_off", "");
			p_connector_bck_img_over = this.getParameter("connector_bck_img_over", "");
			p_connector_bck_img_on = this.getParameter("connector_bck_img_on", "");
			
			p_default_main_font = this.getParameter("default_main_font", "Helvetica");
			p_default_main_font_style = getFontStyleCode(this.getParameter("default_main_font_style", "PLAIN")); //  PLAIN,  BOLD,  ITALIC
			p_default_main_font_size = Integer.parseInt(this.getParameter("default_main_font_size", "12"));
			
			p_default_encoding = this.getParameter("default_encoding", "iso-8859-1");
			p_page_encoding = this.getParameter("page_encoding", "iso-8859-1");
			
			p_disabled_msg = getEncodedString(this.getParameter("disabled_msg", "DISABLED"), p_page_encoding, p_default_encoding);
			p_default_target = this.getParameter("default_target", "_self");
			p_default_icon_off = this.getParameter("default_icon_off", "");
			p_default_icon_over = this.getParameter("default_icon_over", "");
			p_default_icon_on = this.getParameter("default_icon_on", "");
			p_data_file = this.getParameter("data_file", "");
		}
		catch (Exception e) {
			e.printStackTrace();
		}
		
		if (p_data_file.length() > 0) {
			readMenuDataFile(p_data_file); //read data from external text file
		}
		else { //get explicit parameters
			
			setMenuItemSize(countButtons());
			
			//read values and assign to array
			for (int i = 1; i < p_id.length; i++) {
				try {
					String tempstring = "";
					
					p_id[i] = Integer.parseInt(this.getParameter("id" + i, "0"));
					p_subid[i] = Integer.parseInt(this.getParameter("subid" + i, "0"));
					
					//get element level:
					p_level[i] = 0;
					int lev = i;
					while ( (p_subid[lev] > 0) && (lev > 0)) {
						lev = p_subid[lev];
						p_level[i]++;
					}
					
					//System.out.println(p_level[i]); //DEBUG: display levels
					
					p_last_node[i] = false;
					p_v_connectors[i] = new boolean[p_level[i]];
					
					if ( (i > 0) && (p_level[i] > p_level[i - 1])) { //increased level
						p_last_node[i - 1] = true;
					}
					else {
						if ( (i > 0) && (p_level[i] < p_level[i - 1])) { //decresed level
							p_last_node[i - 1] = true; //mark as last node the previous node
							//reopen last nodes
							for (int l = i - 1; l > p_subid[i]; l--) {
								if (p_level[l] == p_level[i]) {
									p_last_node[l] = false; //unset last node flat from the previous node
									break;
								}
							}
						}
					}
					
					num_buttons++; //count buttons
					p_node[i] = Boolean.valueOf(this.getParameter("node" + i, "false")).booleanValue();
					p_enabled[i] = Boolean.valueOf(this.getParameter("enabled" + i, "true")).booleanValue();
					p_link[i] = this.getParameter("link" + i, "");
					//p_link[i] = URLDecoder.decode(p_link[i], "UTF-8"); //decode encoded URLs
					p_target[i] = this.getParameter("target" + i, p_default_target);
					if (p_target[i].length() == 0) {
						p_target[i] = "_self";
					}
					
					tempstring = this.getParameter("encoding" + i, "");
					if (tempstring.length() > 0) {
						p_encoding[i] = tempstring;
					}
					else {
						p_encoding[i] = p_default_encoding;
					}
					
					p_name[i] = getEncodedString(this.getParameter("name" + i, ""), p_page_encoding, p_encoding[i]);
					p_description[i] = getEncodedString(this.getParameter("description" + i, ""), p_page_encoding, p_encoding[i]);
					p_icon_off[i] = this.getParameter("icon_off" + i, p_default_icon_off);
					p_icon_over[i] = this.getParameter("icon_over" + i, p_default_icon_over);
					p_icon_on[i] = this.getParameter("icon_on" + i, p_default_icon_on);
					
					tempstring = this.getParameter("colbck_off" + i, "");
					if (tempstring.length() > 0) {
						p_colbck_off[i] = new Color(Integer.parseInt(tempstring, 16));
					}
					else {
						p_colbck_off[i] = p_default_colbck_off;
					}
					tempstring = this.getParameter("colbck_over" + i, "");
					if (tempstring.length() > 0) {
						p_colbck_over[i] = new Color(Integer.parseInt(tempstring, 16));
					}
					else {
						p_colbck_over[i] = p_default_colbck_over;
					}
					tempstring = this.getParameter("colbck_on" + i, "");
					if (tempstring.length() > 0) {
						p_colbck_on[i] = new Color(Integer.parseInt(tempstring, 16));
					}
					else {
						p_colbck_on[i] = p_default_colbck_on;
					}
					
					tempstring = this.getParameter("coltxt_off" + i, "");
					if (tempstring.length() > 0) {
						p_coltxt_off[i] = new Color(Integer.parseInt(tempstring, 16));
					}
					else {
						p_coltxt_off[i] = p_default_coltxt_off;
					}
					tempstring = this.getParameter("coltxt_over" + i, "");
					if (tempstring.length() > 0) {
						p_coltxt_over[i] = new Color(Integer.parseInt(tempstring, 16));
					}
					else {
						p_coltxt_over[i] = p_default_coltxt_over;
					}
					tempstring = this.getParameter("coltxt_on" + i, "");
					if (tempstring.length() > 0) {
						p_coltxt_on[i] = new Color(Integer.parseInt(tempstring, 16));
					}
					else {
						p_coltxt_on[i] = p_default_coltxt_on;
					}
					
					tempstring = this.getParameter("colsdw_off" + i, "");
					if (tempstring.length() > 0) {
						p_colsdw_off[i] = new Color(Integer.parseInt(tempstring, 16));
					}
					else {
						p_colsdw_off[i] = p_default_colsdw_off;
					}
					tempstring = this.getParameter("colsdw_over" + i, "");
					if (tempstring.length() > 0) {
						p_colsdw_over[i] = new Color(Integer.parseInt(tempstring, 16));
					}
					else {
						p_colsdw_over[i] = p_default_colsdw_over;
					}
					tempstring = this.getParameter("colsdw_on" + i, "");
					if (tempstring.length() > 0) {
						p_colsdw_on[i] = new Color(Integer.parseInt(tempstring, 16));
					}
					else {
						p_colsdw_on[i] = p_default_colsdw_on;
					}
					
					tempstring = this.getParameter("shadow_x" + i, "");
					if (tempstring.length() > 0) {
						p_shadow_x[i] = Integer.parseInt(tempstring);
					}
					else {
						p_shadow_x[i] = p_default_shadow_x;
					}
					tempstring = this.getParameter("shadow_y" + i, "");
					if (tempstring.length() > 0) {
						p_shadow_y[i] = Integer.parseInt(tempstring);
					}
					else {
						p_shadow_y[i] = p_default_shadow_y;
					}
					
					p_bck_img_off[i] = this.getParameter("bck_img_off" + i, p_default_bck_img_off);
					p_bck_img_over[i] = this.getParameter("bck_img_over" + i, p_default_bck_img_over);
					p_bck_img_on[i] = this.getParameter("bck_img_on" + i, p_default_bck_img_on);
					p_sound_over[i] = this.getParameter("sound_over" + i, p_default_sound_over);
					p_sound_click[i] = this.getParameter("sound_click" + i, p_default_sound_click);
					
					tempstring = this.getParameter("pushed" + i, "");
					if (tempstring.length() > 0) {
						p_pushed[i] = Boolean.valueOf(tempstring).booleanValue();
					}
					else {
						p_pushed[i] = p_default_pushed;
					}
					
					tempstring = this.getParameter("padding" + i, "");
					if (tempstring.length() > 0) {
						String[] str_padding = splitData(tempstring, ':', 4);
						p_padding[i][0] = 2; // set default value
						for (int j = 0; j < 4; j++) {
							if (str_padding[i].length() > 0) {
								p_padding[i][j] = Integer.parseInt(str_padding[j]);
							}
							else {
								p_padding[i][j] = p_padding[i][0]; // to be compatible with JDDM < 2.1.000
							}
						}
					}
					else {
						for (int j = 0; j < 4; j++) {
							p_padding[i][j] = p_default_padding[j];
						}
					}
					
					tempstring = this.getParameter("gap" + i, "");
					if (tempstring.length() > 0) {
						p_gap[i] = Integer.parseInt(tempstring);
					}
					else {
						p_gap[i] = p_default_gap;
					}
					tempstring = this.getParameter("label_position" + i, "");
					if (tempstring.length() > 0) {
						p_label_position[i] = getPositionCode(tempstring);
					}
					else {
						p_label_position[i] = p_default_label_position;
					}
					tempstring = this.getParameter("border_width" + i, "");
					if (tempstring.length() > 0) {
						p_border_width[i] = Integer.parseInt(tempstring);
					}
					else {
						p_border_width[i] = p_default_border_width;
					}
					tempstring = this.getParameter("center_block" + i, "");
					if (tempstring.length() > 0) {
						p_center_block[i] = Boolean.valueOf(tempstring).booleanValue();
					}
					else {
						p_center_block[i] = p_default_center_block;
					}
					
					p_font[i] = this.getParameter("font" + i, p_default_main_font);
					
					tempstring = this.getParameter("font_style" + i, "");
					if (tempstring.length() > 0) {
						p_font_style[i] = getFontStyleCode(tempstring);
					}
					else {
						p_font_style[i] = p_default_main_font_style;
					}
					
					tempstring = this.getParameter("font_size" + i, "");
					if (tempstring.length() > 0) {
						p_font_size[i] = Integer.parseInt(tempstring);
					}
					else {
						p_font_size[i] = p_default_main_font_size;
					}
					
					tempstring = this.getParameter("shortcut" + i, "");
					if (tempstring.length() > 0) {
						p_shortcut[i] = tempstring.toUpperCase();
					}
					else {
						p_shortcut[i] = "";
					}
					
				}
				catch (Exception e) {
					e.printStackTrace();
				}
			} //end for cycle
		} //end if readfile
		
		p_last_node[num_buttons] = true;
		ab = new ImageButton[num_buttons]; //set number of buttons
		cb = new ConnectorButton[num_buttons]; //set number of buttons
	}
	
//	-----------------------------------------------------------------------------
	
	/**
	 * set arrays size
	 * @param i size of array
	 */
	private void setMenuItemSize(int i) {
		p_id = new int[i];
		p_subid = new int[i];
		p_node = new boolean[i];
		p_level = new int[i];
		p_last_node = new boolean[i];
		p_v_connectors = new boolean[i][];
		p_enabled = new boolean[i];
		p_link = new String[i];
		p_target = new String[i];
		p_encoding = new String[i];
		p_name = new String[i];
		p_description = new String[i];
		p_icon_off = new String[i];
		p_icon_over = new String[i];
		p_icon_on = new String[i];
		p_colbck_off = new Color[i];
		p_colbck_over = new Color[i];
		p_colbck_on = new Color[i];
		p_coltxt_off = new Color[i];
		p_coltxt_over = new Color[i];
		p_coltxt_on = new Color[i];
		p_colsdw_off = new Color[i];
		p_colsdw_over = new Color[i];
		p_colsdw_on = new Color[i];
		p_shadow_x = new int[i];
		p_shadow_y = new int[i];
		p_bck_img_off = new String[i];
		p_bck_img_over = new String[i];
		p_bck_img_on = new String[i];
		p_sound_over = new String[i];
		p_sound_click = new String[i];
		p_pushed = new boolean[i];
		p_padding = new int[i][4];
		p_gap = new int[i];
		p_label_position = new int[i];
		p_center_block = new boolean[i];
		p_border_width = new int[i];
		p_font = new String[i];
		p_font_style = new int[i];
		p_font_size = new int[i];
		p_shortcut = new String[i];
	}
	
//	-----------------------------------------------------------------------------
	/**
	 * Read menu items data from external text file.
	 * "\n" separate items
	 * "\t" separate values
	 * @param filename the text file containing menu data
	 */
	private void readMenuDataFile(String filename) {
		int nfields = 36; //number of data fields
		int i = 0; //temp elements counter
		int num_elements = 0; //number of menu items
		String dataline;
		String[] elementdata;
		String tempstring;
		try {
			URL filesource = setURL(filename);
			//open data file
			BufferedReader in = new BufferedReader(new InputStreamReader(filesource.openStream()));
			//count elements
			while (null != (dataline = in.readLine())) {
				i++;
			}
			in.close();
			num_elements = i + 1;
			setMenuItemSize(num_elements); //set arrays size
			
			i = 1;
			
			in = new BufferedReader(new InputStreamReader(filesource.openStream()));
			//read lines (each line is one menu element)
			while (null != (dataline = in.readLine())) {
				//get element data array
				elementdata = splitData(dataline, '\t', nfields);
				
				//assign data
				p_id[i] = i;
				p_node[i] = false;
				p_level[i] = Integer.parseInt(elementdata[0]);
				
				p_last_node[i] = false;
				p_v_connectors[i] = new boolean[p_level[i]];
				
				num_buttons++; //count buttons
				
				if ( (p_level[i] == 0) || (i <= 1)) {
					p_subid[i] = 0;
				}
				
				if ( (i > 0) && (p_level[i] == p_level[i - 1])) { //same level as previous
					p_subid[i] = p_subid[i - 1]; //set same sub_id as previous
				}
				else {
					if ( (i > 0) && (p_level[i] > p_level[i - 1])) { //increased level
						p_last_node[i - 1] = true;
						p_subid[i] = p_id[i - 1]; //set sub_id as id of previous (child)
						p_node[i - 1] = true; //set previous as a node
					}
					else {
						if ( (i > 0) && (p_level[i] < p_level[i - 1])) { //decresed level
							p_last_node[i - 1] = true; //mark as last node the previous node
							//reopen last nodes
							for (int l = i - 1; l > p_subid[i]; l--) {
								if (p_level[l] == p_level[i]) {
									p_last_node[l] = false; //unset last node flat from the previous node
									break;
								}
							}
							int j = 1;
							while ( (j < i) && (p_level[i] < p_level[i - j])) {
								j++;
							}
							if (p_level[i] == p_level[i - j]) {
								p_subid[i] = p_subid[i - j];
							}
						}
					}
				}
				
				//System.out.println(p_id[i]+" - "+p_subid[i]); //debug
				
				p_enabled[i] = Boolean.valueOf(elementdata[1]).booleanValue();
				p_link[i] = elementdata[2];
				p_target[i] = getDefaultValue(elementdata[3], p_default_target);
				if (p_target[i].length() == 0) {
					p_target[i] = "_self";
				}
				p_encoding[i] = getDefaultValue(elementdata[4], p_default_encoding);
				p_name[i] = getEncodedString(elementdata[5], p_page_encoding, p_encoding[i]);
				p_description[i] = getEncodedString(elementdata[6], p_page_encoding, p_encoding[i]);
				
				tempstring = elementdata[7];
				if (tempstring.length() > 0) {
					p_label_position[i] = getPositionCode(tempstring);
				}
				else {
					p_label_position[i] = p_default_label_position;
				}
				tempstring = elementdata[8];
				if (tempstring.length() > 0) {
					p_center_block[i] = Boolean.valueOf(tempstring).booleanValue();
				}
				else {
					p_center_block[i] = p_default_center_block;
				}
				
				tempstring = elementdata[9];
				if (tempstring.length() > 0) {
					String[] str_padding = splitData(tempstring, ':', 4);
					p_padding[i][0] = 2; // set default value
					for (int j = 0; j < 4; j++) {
						if (str_padding[j].length() > 0) {
							p_padding[i][j] = Integer.parseInt(str_padding[j]);
						}
						else {
							p_padding[i][j] = p_padding[i][0]; // to be compatible with JDDM < 2.1.000
						}
					}
				}
				else {
					for (int j = 0; j < 4; j++) {
						p_padding[i][j] = p_default_padding[j];
					}
				}
				
				tempstring = elementdata[10];
				if (tempstring.length() > 0) {
					p_gap[i] = Integer.parseInt(tempstring);
				}
				else {
					p_gap[i] = p_default_gap;
				}
				tempstring = elementdata[11];
				if (tempstring.length() > 0) {
					p_border_width[i] = Integer.parseInt(tempstring);
				}
				else {
					p_border_width[i] = p_default_border_width;
				}
				tempstring = elementdata[12];
				if (tempstring.length() > 0) {
					p_pushed[i] = Boolean.valueOf(tempstring).booleanValue();
				}
				else {
					p_pushed[i] = p_default_pushed;
				}
				
				p_font[i] = getDefaultValue(elementdata[13], p_default_main_font);
				
				tempstring = elementdata[14];
				if (tempstring.length() > 0) {
					p_font_style[i] = getFontStyleCode(tempstring);
				}
				else {
					p_font_style[i] = p_default_main_font_style;
				}
				tempstring = elementdata[15];
				if (tempstring.length() > 0) {
					p_font_size[i] = Integer.parseInt(tempstring);
				}
				else {
					p_font_size[i] = p_default_main_font_size;
				}
				
				tempstring = elementdata[16];
				if (tempstring.length() > 0) {
					p_colbck_off[i] = new Color(Integer.parseInt(tempstring, 16));
				}
				else {
					p_colbck_off[i] = p_default_colbck_off;
				}
				tempstring = elementdata[17];
				if (tempstring.length() > 0) {
					p_colbck_over[i] = new Color(Integer.parseInt(tempstring, 16));
				}
				else {
					p_colbck_over[i] = p_default_colbck_over;
				}
				tempstring = elementdata[18];
				if (tempstring.length() > 0) {
					p_colbck_on[i] = new Color(Integer.parseInt(tempstring, 16));
				}
				else {
					p_colbck_on[i] = p_default_colbck_on;
				}
				
				tempstring = elementdata[19];
				if (tempstring.length() > 0) {
					p_coltxt_off[i] = new Color(Integer.parseInt(tempstring, 16));
				}
				else {
					p_coltxt_off[i] = p_default_coltxt_off;
				}
				tempstring = elementdata[20];
				if (tempstring.length() > 0) {
					p_coltxt_over[i] = new Color(Integer.parseInt(tempstring, 16));
				}
				else {
					p_coltxt_over[i] = p_default_coltxt_over;
				}
				tempstring = elementdata[21];
				if (tempstring.length() > 0) {
					p_coltxt_on[i] = new Color(Integer.parseInt(tempstring, 16));
				}
				else {
					p_coltxt_on[i] = p_default_coltxt_on;
				}
				
				tempstring = elementdata[22];
				if (tempstring.length() > 0) {
					p_colsdw_off[i] = new Color(Integer.parseInt(tempstring, 16));
				}
				else {
					p_colsdw_off[i] = p_default_colsdw_off;
				}
				tempstring = elementdata[23];
				if (tempstring.length() > 0) {
					p_colsdw_over[i] = new Color(Integer.parseInt(tempstring, 16));
				}
				else {
					p_colsdw_over[i] = p_default_colsdw_over;
				}
				tempstring = elementdata[24];
				if (tempstring.length() > 0) {
					p_colsdw_on[i] = new Color(Integer.parseInt(tempstring, 16));
				}
				else {
					p_colsdw_on[i] = p_default_colsdw_on;
				}
				
				tempstring = elementdata[25];
				if (tempstring.length() > 0) {
					p_shadow_x[i] = Integer.parseInt(tempstring);
				}
				else {
					p_shadow_x[i] = p_default_shadow_x;
				}
				tempstring = elementdata[26];
				if (tempstring.length() > 0) {
					p_shadow_y[i] = Integer.parseInt(tempstring);
				}
				else {
					p_shadow_y[i] = p_default_shadow_y;
				}
				
				p_icon_off[i] = getDefaultValue(elementdata[27], p_default_icon_off);
				p_icon_over[i] = getDefaultValue(elementdata[28], p_default_icon_over);
				p_icon_on[i] = getDefaultValue(elementdata[29], p_default_icon_on);
				
				p_bck_img_off[i] = getDefaultValue(elementdata[30], p_default_bck_img_off);
				p_bck_img_over[i] = getDefaultValue(elementdata[31], p_default_bck_img_over);
				p_bck_img_on[i] = getDefaultValue(elementdata[32], p_default_bck_img_on);
				
				p_sound_over[i] = getDefaultValue(elementdata[33], p_default_sound_over);
				p_sound_click[i] = getDefaultValue(elementdata[34], p_default_sound_click);
				
				p_shortcut[i] = getDefaultValue(elementdata[35], ""); // since 1.1.000
				
				i++;
			}
			in.close();
			p_last_node[num_elements - 1] = true;
		}
		catch (Exception e) {
			e.printStackTrace();
		}
	}
	
//	-----------------------------------------------------------------------------
	/**
	 * Split a string in array of predefined size.
	 * @param input_string string to split
	 * @param sep_ch separator character
	 * @param size max elements to retrieve, remaining elements will be filled with empty string
	 * @return splitted_array of strings
	 */
	private String[] splitData(String input_string, char sep_ch, int size) {
		String str1 = new String(); // temp var to contain found strings
		String splitted_array[] = new String[size]; // array of splitted string to return
		int element_num = 0; //number of found elements
		// analize string char by char
		for (int i = 0; i < input_string.length(); i++) {
			if (input_string.charAt(i) == sep_ch) { //separator found
				splitted_array[element_num] = str1; //put string to array
				str1 = new String(); //reinitialize variable
				element_num++; //count strings
				if (element_num >= size) {
					break; //quit if limit is reached
				}
			}
			else {
				str1 += input_string.charAt(i);
			}
		}
		//get last element
		if (element_num < size) {
			splitted_array[element_num] = str1; //put string to vector
			element_num++;
		}
		//fill remaining values with empty string
		for (int i = element_num; i < size; i++) {
			splitted_array[i] = "";
		}
		return splitted_array;
	}
	
//	-----------------------------------------------------------------------------
	/**
	 * Applet constructor
	 */
	public Jwtm() {
	}
	
//	-----------------------------------------------------------------------------
	/**
	 * Initialize the applet
	 */
	public void init() {
		
		//display some info on console
		System.out.println(" ");
		System.out.println("JWTM (Web Tree Menu) " + JWTM_VERSION);
		System.out.println("http://jwtm.sourceforge.net");
		System.out.println("Author: Nicola Asuni");
		System.out.println("Copyright (c) 2002-2006 Tecnick.com s.r.l. - www.tecnick.com");
		System.out.println("Open Source License: GPL 2");
		System.out.println(" ");
		
		//add(new Label("Loading...")); //display loading message
		//validate();
		
		getParameters(); //get applet parameters (menu data)
		
		setBackground(p_background_col); //draw background color
		
		// set layout properties
		item_layout.setHgap(0);
		item_layout.setVgap(0);
		
		tree_layout.setHgap(p_default_hmargin);
		tree_layout.setVgap(p_default_vmargin);
		tree_layout.setHAlignment(p_menu_direction);
		tree_layout.setVAlignment(VFlowLayout.TOP);
		
		int rid = 0; //real item id
		int vid = 0; //virtual item id
		
		for (rid = 1; rid < p_id.length; rid++) { //build tree items
			
			//calculate which vertical connectors to display
			if (p_level[rid] > 0) {
				int previous_node = p_subid[rid];
				for (int j = p_level[rid] - 1; j >= 0; j--) {
					if (p_last_node[previous_node]) {
						p_v_connectors[rid][j] = false;
					}
					else {
						p_v_connectors[rid][j] = true;
					}
					previous_node = p_subid[previous_node];
				}
			}
			
			//System.out.println(p_last_node[rid]); //DEBUG: display if last node
			
			//insert button applet parameters
			ab[vid] = new ImageButton(p_name[rid]);
			ab[vid].setButtonID(rid); //set the button ID
			
			//set icon images
			Image button_icon_off = null;
			if (p_icon_off[rid].length() > 0) {
				button_icon_off = loadImage(this, setURL(p_icon_off[rid]));
			}
			Image button_icon_over = null;
			if (p_icon_over[rid].length() > 0) {
				button_icon_over = loadImage(this, setURL(p_icon_over[rid]));
			}
			Image button_icon_on = null;
			if (p_icon_on[rid].length() > 0) {
				button_icon_on = loadImage(this, setURL(p_icon_on[rid]));
			}
			ab[vid].setStateImages(button_icon_off, button_icon_on, button_icon_over);
			
			//set background images
			Image button_bck_img_off = null;
			if (p_bck_img_off[rid].length() > 0) {
				button_bck_img_off = loadImage(this, setURL(p_bck_img_off[rid]));
			}
			Image button_bck_img_over = null;
			if (p_bck_img_over[rid].length() > 0) {
				button_bck_img_over = loadImage(this, setURL(p_bck_img_over[rid]));
			}
			Image button_bck_img_on = null;
			if (p_bck_img_on[rid].length() > 0) {
				button_bck_img_on = loadImage(this, setURL(p_bck_img_on[rid]));
			}
			ab[vid].setStateBgImages(button_bck_img_off, button_bck_img_on, button_bck_img_over);
			
			//set audio clips
			AudioClip button_sound_click = null;
			if (p_sound_click[rid].length() > 0) {
				button_sound_click = getAudioClip(setURL(p_sound_click[rid]));
			}
			AudioClip button_sound_over = null;
			if (p_sound_over[rid].length() > 0) {
				button_sound_over = getAudioClip(setURL(p_sound_over[rid]));
			}
			ab[vid].setAudioStateClips(button_sound_click, button_sound_over);
			
			ab[vid].setBorderWidth(p_border_width[rid]);
			ab[vid].setDrawPushedIn(p_pushed[rid]);
			ab[vid].setLabelPosition(p_label_position[rid]);
			ab[vid].setCenterBlock(p_center_block[rid]);
			ab[vid].setImageLabelGap(p_gap[rid]);
			ab[vid].setPadding(p_padding[rid]);
			
			abfont = new Font(p_font[rid], p_font_style[rid], p_font_size[rid]);
			ab[vid].setFont(abfont);
			
			//set buttons colors
			ab[vid].setStateColors(p_colbck_off[rid], p_colbck_over[rid],
					p_colbck_on[rid], p_coltxt_off[rid],
					p_coltxt_over[rid], p_coltxt_on[rid],
					p_colsdw_off[rid], p_colsdw_over[rid],
					p_colsdw_on[rid]);
			//set shadow position
			ab[vid].setShadowPosition(p_shadow_x[rid], p_shadow_y[rid]);
			
			//ab[vid].setActionCommand("link");
			
			if (p_link[rid].length() > 0) {
				ab[vid].setActionCommand("link");
			}
			else {
				ab[vid].setActionCommand("");
			}
			
			// --- set connector properties ***************
			
			cb[vid] = new ConnectorButton(p_connector_min_width,
					ab[vid].getSize().height + 1,
					p_connector_line_width, p_level[rid] + 1,
					p_node[rid], (vid == 0), p_last_node[rid],
					p_v_connectors[rid]);
			
			cb[vid].setConnectorID(rid); //set the button ID
			cb[vid].setDirection(p_menu_direction); //set direction
			
			Image connector_bck_img_off = null;
			if (p_connector_bck_img_off.length() > 0) {
				connector_bck_img_off = loadImage(this, setURL(p_connector_bck_img_off));
			}
			Image connector_bck_img_on = null;
			if (p_connector_bck_img_on.length() > 0) {
				connector_bck_img_on = loadImage(this, setURL(p_connector_bck_img_on));
			}
			Image connector_bck_img_over = null;
			if (p_connector_bck_img_over.length() > 0) {
				connector_bck_img_over = loadImage(this, setURL(p_connector_bck_img_over));
			}
			
			cb[vid].setStateBgImages(connector_bck_img_off, connector_bck_img_on,
					connector_bck_img_over);
			cb[vid].setStateColors(p_connector_bck_col_off, p_connector_bck_col_over,
					p_connector_bck_col_on,
					p_connector_line_col_off, p_connector_line_col_over,
					p_connector_line_col_on);
			if (p_node[rid]) {
				cb[vid].setStateImages(p_img_node_off, p_img_node_on, p_img_node_over);
			}
			
			//set audio clips
			AudioClip connector_sound_click = null;
			if (p_connector_sound_click.length() > 0) {
				connector_sound_click = getAudioClip(setURL(p_connector_sound_click));
			}
			AudioClip connector_sound_over = null;
			if (p_connector_sound_over.length() > 0) {
				connector_sound_over = getAudioClip(setURL(p_connector_sound_over));
			}
			cb[vid].setAudioStateClips(connector_sound_click, connector_sound_over);
			
			if (p_node[rid]) {
				cb[vid].setActionCommand("tree");
			}
			else {
				cb[vid].setActionCommand("");
			}
			
			drawItem(vid, rid); //add button
			
			vid++;
		}
		
		tree_pane.setLayout(tree_layout);
		tree_pane.setImage(p_background_img);
		tree_pane.setMode(p_background_img_pos);
		
		tree_layout.setorientation(p_menu_direction);
		
		//set scollable pane
		scroll_pane.add(tree_pane);
		
		removeAll(); //remove loading message
		
		setLayout(null); //set main layout
		
		add(scroll_pane);
		
		//resize scroll pane
		Adjustable vadjust = scroll_pane.getVAdjustable();
		Adjustable hadjust = scroll_pane.getHAdjustable();
		hadjust.setUnitIncrement(10);
		vadjust.setUnitIncrement(10);
		Insets i = scroll_pane.getInsets();
		//increase ScrollPane size to remove border
		scroll_pane.setSize(getSize().width + i.right + i.left,
				getSize().height + i.top + i.bottom);
		scroll_pane.setLocation( -i.left, -i.top); //center scroll_pane
		
		validate();
		
		if (p_menu_direction == ImageButton.LEFT) {
			scroll_pane.setScrollPosition(0, 0);
		}
		else {
			scroll_pane.setScrollPosition(tree_pane.getSize().width - scroll_pane.getViewportSize().width, 0);
		}
		
		validate();
		
		try {
			jbInit();
		}
		catch (Exception e) {
			e.printStackTrace();
		}
		
	} // end of init
	
//	-----------------------------------------------------------------------------
	
	/**
	 * Draw the block [connector + button].
	 * @param vid virtual ID
	 * @param rid real ID
	 */
	private void drawItem(final int vid, final int rid) {
		Panel item_pane = new Panel();
		item_pane.setLayout(item_layout); // contain connector + button
		
		if (p_menu_direction == ImageButton.LEFT) {
			item_pane.add(cb[vid]); //add element
			drawConnector(vid, rid);
			item_pane.add(ab[vid]); //add element
			drawButton(vid, rid);
		}
		else {
			item_pane.add(ab[vid]); //add element
			drawButton(vid, rid);
			item_pane.add(cb[vid]); //add element
			drawConnector(vid, rid);
		}
		tree_pane.add(item_pane); //add block (connector + button)
	}
	
//	-----------------------------------------------------------------------------
	/**
	 * Draw the item button and handle behaviour.
	 * @param vid virtual ID
	 * @param rid real ID
	 */
	private void drawButton(final int vid, final int rid) {
		
		if ( (p_level[rid]) > 0) {
			ab[vid].setVisible(false); //shows only first level elements
		}
		
		final int index = ab[vid].getButtonID();
		
		//add keyboard listener for shortcuts
		ab[vid].addKeyListener(new KeyAdapter() {
			public void keyPressed(KeyEvent e) {
				String m = KeyEvent.getKeyModifiersText(e.getModifiers());
				if (m == null) {
					m = "";
				}
				String c = KeyEvent.getKeyText(e.getKeyCode());
				if (c == null) {
					c = "";
				}
				String sep = "";
				if ( (m.length() > 0) && (c.length() > 0)) {
					sep = "+";
				}
				String keycode = m.concat(sep).concat(c).toUpperCase();
				//System.out.println("APPLET: " + keycode + " "); // DEBUG
				for (int bvid = 0; bvid < ab.length; bvid++) { //search buttons
					if (keycode.compareTo(p_shortcut[ab[bvid].getButtonID()]) == 0) {
						ab[bvid].imageButton_MousePushed();
						ab[bvid].current_action = MouseEvent.MOUSE_PRESSED;
					}
					else {
						ab[bvid].imageButton_MouseOff();
					}
				}
			}
		});
		
		ab[vid].addActionListener(new ActionListener() { // add listener for button events
			public void actionPerformed(ActionEvent e) { // an event occur
				if (p_enabled[index]) {
					if (p_description[index].length() > 0) { // display button description on status
						showStatus(p_description[index] + " [" + p_shortcut[index] + "]");
					}
					else {
						if (p_link[index].length() > 0) { // display button link on status
							showStatus(p_link[index] + " [" + p_shortcut[index] + "]");
						}
					}
					if (e.getID() == MouseEvent.MOUSE_PRESSED) { // Clicking on the button should open the relative link
						if ( (e.getActionCommand() == "link")) { //button has a link
							if (p_link[index].length() > 0) { //open document
								if ( (p_link[index].length() > 11) && (p_link[index].substring(0, 11).toLowerCase().compareTo("javascript:") == 0)) {
									try {
										JSObject jsroot = JSObject.getWindow(a);
										jsroot.eval(p_link[index].substring(11));
									}
									catch (Exception ej) {
										// this fix a mozilla bug
										a.getAppletContext().showDocument(setURL(p_link[index]), p_target[index]);
									}
								}
								else {
									a.getAppletContext().showDocument(setURL(p_link[index]), p_target[index]); // load the URL to the target browser window
								}
							}
						}
						//if is a node and node is closed
						if (p_node[index] && (!cb[vid].getNodeStatus())) {
							//expand or collapse tree node (open/close child submenu)
							changeView(vid, rid, true);
						}
					}
				}
				else {
					if (p_disabled_msg.length() > 0) {
						showStatus(p_disabled_msg); //display disabled status message
					}
				}
			}
		});
	}
	
//	-----------------------------------------------------------------------------
	/**
	 * Draw the item connector and handle behaviour.
	 * @param vid virtual ID
	 * @param rid real ID
	 */
	private void drawConnector(final int vid, final int rid) {
		
		if ( (p_level[rid]) > 0) {
			cb[vid].setVisible(false); //shows only first level elements
		}
		
		final int index = cb[vid].getConnectorID();
		cb[vid].addActionListener(new ActionListener() { // add listener for button events
			public void actionPerformed(ActionEvent e) { // an event occur
				if (p_enabled[index]) {
					if (e.getID() == MouseEvent.MOUSE_PRESSED) { // Clicking on the button should open the relative node
						if ( (e.getActionCommand() == "tree")) {
							changeView(vid, rid, false); //expand or collapse tree node (open/close child submenu)
						}
					}
				}
			}
		});
	}
	
//	-----------------------------------------------------------------------------
	/**
	 * Expand/Collapse subtrees
	 * @param vid virtual ID
	 * @param rid real ID
	 * @param updatenode updatenode
	 */
	private void changeView(final int vid, final int rid, boolean updatenode) {
		
		if (updatenode) {
			cb[vid].setNodeStatus(!cb[vid].getNodeStatus()); //change node status
		}
		
		//check for submenu elements (child)
		for (int j = rid + 1; j < p_id.length; j++) {
			if (p_subid[j] < rid) {
				break;
			}
			//close/expand the immediate sublevel.
			if (p_subid[j] == rid) {
				cb[j - 1].setVisible(!cb[j - 1].isVisible());
				ab[j - 1].setVisible(!ab[j - 1].isVisible());
			}
			// if we are closing, then close also all nested levels:
			if ( (!cb[vid].getNodeStatus()) && ( (p_subid[j] >= rid))) {
				cb[j - 1].setNodeStatus(false);
				cb[j - 1].setVisible(false);
				ab[j - 1].setVisible(false);
			}
		}
		
		validate(); //repaint applet
		
		// move scrollbars
		//calculate viewport/panel ratio
		float cx = (float) 1 - ( (float) scroll_pane.getViewportSize().width /
				(float) tree_pane.getSize().width);
		float cy = (float) 1 - ( (float) scroll_pane.getViewportSize().height /
				(float) tree_pane.getSize().height);
		
		int sx = 0;
		if (p_menu_direction == ImageButton.LEFT) {
			sx = (int) (cx * cb[vid + 1].getSize().width);
		}
		else {
			sx = (int) (cx *
					( (float) tree_pane.getSize().width - (float) cb[vid +
					                                                 1].getSize().width));
		}
		int sy = (int) (cy *
				( (float) ab[vid + 1].getParent().getLocation().y +
						(float) ab[vid + 1].getSize().height));
		scroll_pane.setScrollPosition(sx, sy);
	}
	
//	-----------------------------------------------------------------------------
	/**
	 * Component initialization
	 * @throws Exception
	 */
	private void jbInit() throws Exception {
	}
	
//	-----------------------------------------------------------------------------
	/**
	 * Start the applet
	 */
	public void start() {
	}
	
	/**
	 * Stop the applet
	 */
	public void stop() {
	}
	
	/**
	 * Destroy the applet
	 */
	public void destroy() {
	}
	
	/**
	 * Get Applet information
	 * @return Applet information
	 */
	public String getAppletInfo() {
		String message = "JWTM (Web Tree Menu) " + JWTM_VERSION + "\n";
		message += "http://jwtm.sourceforge.net\n";
		message += "Author: Nicola Asuni\n";
		message += "Copyright (c) 2002-2006 Tecnick.com s.r.l. - www.tecnick.com\n";
		message += "Open Source License: GPL 2\n";
		return message;
	}
	
	// -----------------------------------------------------------------------------
	
//	=============================================================================
	
}
