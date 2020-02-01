package com.tecnick.jrelaxtimer;

import java.applet.Applet;
import java.awt.Choice;
import java.awt.Color;
import java.awt.FlowLayout;
import java.awt.Image;
import java.awt.Label;
import java.awt.MediaTracker;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.event.ItemEvent;
import java.awt.event.ItemListener;
import java.awt.event.MouseEvent;
import java.net.MalformedURLException;
import java.net.URL;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.TimeZone;

//-----------------------------------------------------------------------------
/**
 * Title: JRelaxTimer<br>
 * Description: Applet to display timer that allows you to open Web pages at specific time intervals<br>
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
public class JRelaxTimer
extends Applet
implements Runnable {
	
	/**
	 * serialVersionUID
	 */
	private static final long serialVersionUID = -4199622298412980618L;
	
	/**
	 * Software version
	 */
	private static final String JRELAXTIMER_VERSION = "1.0.001";
	
	/**
	 * The thread that displays clock
	 */
	Thread timer;
	
	/**
	 * display panel for starting time
	 */
	private DisplayPanel dpanel_started = null;
	
	/**
	 * display panel for current time (clock)
	 */
	private DisplayPanel dpanel_current_time = null;
	
	/**
	 * display panel to show elapsed time since start
	 */
	private DisplayPanel dpanel_elapsed = null;
	
	/**
	 * display panel to show remaining time to next break
	 */
	private DisplayPanel dpanel_next = null;
	
	/**
	 * display panel to count breaks
	 */
	private DisplayPanel dpanel_counter = null;
	
	/**
	 * only final variables can be used in inner anonymous classes
	 */
	final Applet a = this;
	
	// applet parameters <param />
	
	/**
	 * target url of the page to open for breaks
	 */
	private String p_target_url;
	
	/**
	 * url of the company (link associated with logo image)
	 */
	private String p_logo_url;
	
	/**
	 * the break counter pattern
	 */
	private String p_counter_pattern;
	
	/**
	 * the remaining time pattern
	 */
	private String p_remaining_pattern;
	
	/**
	 * the full datetime pattern
	 */
	private String p_display_pattern;
	
	/**
	 * if true show the counter display
	 */
	private boolean p_display_counter;
	
	/**
	 * if true show the starting time display
	 */
	private boolean p_display_started;
	
	/**
	 * if true show the current time display
	 */
	private boolean p_display_current_time;
	
	/**
	 * if true show the elapsed time display
	 */
	private boolean p_display_elapsed;
	
	/**
	 * if true show the remaining time display
	 */
	private boolean p_display_next;
	
	/**
	 * background color for applet
	 */
	private Color p_background_color;
	
	/**
	 * background color (RRGGBB) for staring time display
	 */
	private Color p_background_color_started;
	
	/**
	 * background color (RRGGBB) for current time display
	 */
	private Color p_background_color_current_time;
	
	/**
	 * background color (RRGGBB) for elapsed time display
	 */
	private Color p_background_color_elapsed;
	
	/**
	 * background color (RRGGBB) for remaining time to next break display
	 */
	private Color p_background_color_next;
	
	/**
	 * background color (RRGGBB) for break counter display
	 */
	private Color p_background_color_counter;
	
	/**
	 * logo image
	 */
	private Image p_logo_image;
	
	/**
	 * background image for staring time display
	 */
	private Image p_background_image_started;
	
	/**
	 * background image for current time display
	 */
	private Image p_background_image_current_time;
	
	/**
	 * background image for elapsed time display
	 */
	private Image p_background_image_elapsed;
	
	/**
	 * background image for remaining time to next break display
	 */
	private Image p_background_image_next;
	
	/**
	 * background image for break counter display
	 */
	private Image p_background_image_counter;
	
	/**
	 * dicrectory where are stored images for starting time display
	 */
	private String p_img_dir_started;
	
	/**
	 * dicrectory where are stored images for current time display
	 */
	private String p_img_dir_current_time;
	
	/**
	 * dicrectory where are stored images for elapsed time display
	 */
	private String p_img_dir_elapsed;
	
	/**
	 * dicrectory where are stored images for remaining time display
	 */
	private String p_img_dir_next;
	
	/**
	 * dicrectory where are stored images for break counter display
	 */
	private String p_img_dir_counter;
	
	/**
	 * dicrectory where are stored images for buttons
	 */
	private String p_img_dir_buttons;
	
	/**
	 * number and symbols images
	 */
	private Image[] p_img_started = new Image[15];
	
	/**
	 * number and symbols images
	 */
	private Image[] p_img_current_time = new Image[15];
	
	/**
	 * number and symbols images
	 */
	private Image[] p_img_elapsed = new Image[15];
	
	/**
	 * number and symbols images
	 */
	private Image[] p_img_next = new Image[15];
	
	/**
	 * number and symbols images
	 */
	private Image[] p_img_counter = new Image[15];
	
	/**
	 * pause button image for inactive state
	 */
	private Image p_img_pause_off;
	
	/**
	 * pause button image for mouse over state
	 */
	private Image p_img_pause_over;
	
	/**
	 * stop button image for active state
	 */
	private Image p_img_pause_on;
	
	/**
	 * stop button image for inactive state
	 */
	private Image p_img_stop_off;
	
	/**
	 * stop button image for mouse over state
	 */
	private Image p_img_stop_over;
	
	/**
	 * stop button image for active state
	 */
	private Image p_img_stop_on;
	
	/**
	 * play button image for inactive state
	 */
	private Image p_img_play_off;
	
	/**
	 * play button image for mouse over state
	 */
	private Image p_img_play_over;
	
	/**
	 * play button image for active state
	 */
	private Image p_img_play_on;
	
	/**
	 * break button image for inactive state
	 */
	private Image p_img_break_off;
	
	/**
	 * break button image for mouse over state
	 */
	private Image p_img_break_over;
	
	/**
	 * break button image for active state
	 */
	private Image p_img_break_on;
	
	/**
	 * current time
	 */
	private Date now;
	
	/**
	 * starting time
	 */
	private Date start_time;
	
	/**
	 * time to next break
	 */
	private long dtarget;
	
	/**
	 * remaining time to next break
	 */
	private long remaining_time;
	
	/**
	 /**
	  * date formatter
	  */
	private SimpleDateFormat formatter;
	
	/**
	 * pause button
	 */
	private ImageButton button_pause = null;
	
	/**
	 * stop button
	 */
	private ImageButton button_stop = null;
	
	/**
	 * play button
	 */
	private ImageButton button_play = null;
	
	/**
	 * break button
	 */
	private ImageButton button_break = null;
	
	/**
	 * Time zone
	 */
	private TimeZone tz;
	
	//time constants
	
	/**
	 * CONSTANT: number of seconds in one minute
	 */
	private static final int SECONDS_IN_MINUTE = 60;
	
	/**
	 * CONSTANT: number of seconds in one hour
	 */
	private static final int SECONDS_IN_HOUR = 60 * SECONDS_IN_MINUTE;
	
	/**
	 * CONSTANT: number of seconds in one day
	 */
	private static final int SECONDS_IN_DAY = 24 * SECONDS_IN_HOUR;
	
	/**
	 * CONSTANT: number of seconds in one month
	 */
	private static final int SECONDS_IN_MONTH = 30 * SECONDS_IN_DAY;
	
	/**
	 * CONSTANT: number of seconds in one year
	 */
	private static final int SECONDS_IN_YEAR = 365 * SECONDS_IN_DAY;
	
	/**
	 * sleep time in milliseconds between two successive call to display function
	 */
	private static final int SLEEP_TIME = 250;
	
	/**
	 * current status is true when timer is running
	 */
	private boolean play_status = true;
	
	/**
	 * current status is true when timer is running
	 */
	private boolean pause_status = false;
	
	/**
	 * count breaks
	 */
	private int current_break = 0;
	
	/**
	 * time interval in minutes
	 */
	public int current_interval = 30;
	
	/**
	 * time interval in milliseconds
	 */
	private int time_interval = current_interval * SECONDS_IN_MINUTE * 1000;
	
	/**
	 * array containing intervals values
	 */
	String[] intervals;
	
	/**
	 * array containing break types information
	 */
	String[][] break_types;
	
	/**
	 * type of break index
	 */
	public int current_break_type = 0;
	
	/**
	 * extension of the break pages to call
	 */
	private String p_page_extension = ".htm";
	
	/**
	 * selector for break type
	 */
	private Choice break_type_selector;
	
	/**
	 * selector for interval
	 */
	private Choice interval_selector;
	
//	-----------------------------------------------------------------------------
//	METHODS
//	-----------------------------------------------------------------------------
	
//	-----------------------------------------------------------------------------
	
	/** Load an image.
	 *  @param a applet
	 *  @param file image file URL
	 *  @return loadImage
	 */
	static private Image loadImage(Applet a, URL file) {
		if (file == null) {
			return null;
		}
		Image i;
		MediaTracker t = new MediaTracker(a);
		i = a.getImage(file);
		t.addImage(i, 0);
		try {
			t.waitForAll();
		}
		catch (InterruptedException ee) {
			System.out.println("loadImage exception: " + ee);
		}
		return i;
	}
	
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
	
//	-----------------------------------------------------------------------------
	/**
	 * Get the applet parameters.
	 */
	private void getParameters() {
		try {
			
			p_counter_pattern = "xx"; // input pattern for remaining time
			p_remaining_pattern = " HH:mm:ss"; // input pattern for counter
			p_display_pattern = "yyyy-MM-dd HH:mm:ss"; // display pattern
			
			p_target_url = this.getParameter("target_url", ""); // url of the break pagesp_logo_url
			p_logo_url = this.getParameter("logo_url", ""); // link associated with the logo image
			
			current_interval = Integer.parseInt(this.getParameter("default_interval",
			"30"));
			time_interval = current_interval * SECONDS_IN_MINUTE * 1000;
			
			String p_intervals = this.getParameter("intervals", "15,30,45,60"); // comma separated list of break intervals in minutes
			intervals = splitData(p_intervals, ',');
			
			String p_break_types = this.getParameter("break_types", ""); // comma separated list of break intervals in minutes
			String[] break_types_blocks = splitData(p_break_types, '|');
			
			if ( (break_types_blocks != null) && (break_types_blocks.length > 0)) {
				break_types = new String[break_types_blocks.length][];
				for (int i = 0; i < break_types_blocks.length; i++) {
					if (break_types_blocks[i] != null) {
						break_types[i] = splitData(break_types_blocks[i], ',');
					}
				}
			}
			
			p_page_extension = this.getParameter("page_extension", ".htm"); // extension of the break pages to call
			
			p_background_color = new Color(Integer.parseInt(this.getParameter(
					"background_color", "000000"), 16)); //background color (RRGGBB)
			p_logo_image = loadImage(this, setURL(this.getParameter("logo_image", ""))); // logo image
			
			// enable / disable displays
			p_display_started = Boolean.valueOf(this.getParameter("display_started",
			"true")).booleanValue();
			p_display_current_time = Boolean.valueOf(this.getParameter(
					"display_current_time", "true")).booleanValue();
			p_display_elapsed = Boolean.valueOf(this.getParameter("display_elapsed",
			"true")).booleanValue();
			p_display_counter = Boolean.valueOf(this.getParameter("display_counter",
			"true")).booleanValue();
			p_display_next = Boolean.valueOf(this.getParameter("display_next", "true")).
			booleanValue();
			
			// background colors for displays
			p_background_color_started = new Color(Integer.parseInt(this.getParameter(
					"background_color_started", "000000"), 16)); //background color (RRGGBB)
			p_background_color_current_time = new Color(Integer.parseInt(this.
					getParameter("background_color_current_time", "000000"), 16)); //background color (RRGGBB)
			p_background_color_elapsed = new Color(Integer.parseInt(this.getParameter(
					"background_color_elapsed", "000000"), 16)); //background color (RRGGBB)
			p_background_color_next = new Color(Integer.parseInt(this.getParameter(
					"background_color_next", "000000"), 16)); //background color (RRGGBB)
			p_background_color_counter = new Color(Integer.parseInt(this.getParameter(
					"background_color_counter", "000000"), 16)); //background color (RRGGBB)
			
			// background images for displays
			p_background_image_started = loadImage(this,
					setURL(this.getParameter("background_image_started",
					""))); // background image
			p_background_image_current_time = loadImage(this,
					setURL(this.
							getParameter("background_image_current_time", ""))); // background image
			p_background_image_elapsed = loadImage(this,
					setURL(this.getParameter("background_image_elapsed",
					""))); // background image
			p_background_image_next = loadImage(this,
					setURL(this.getParameter("background_image_next",
					""))); // background image
			p_background_image_counter = loadImage(this,
					setURL(this.getParameter("background_image_counter",
					""))); // background image
			
			//images directories
			p_img_dir_started = this.getParameter("img_dir_started", ""); // target time for counter
			p_img_dir_current_time = this.getParameter("img_dir_current_time", ""); // target time for counter
			p_img_dir_elapsed = this.getParameter("img_dir_elapsed", ""); // target time for counter
			p_img_dir_next = this.getParameter("img_dir_next", ""); // target time for counter
			p_img_dir_counter = this.getParameter("img_dir_counter", ""); // target time for counter
			
			//load images for displays:
			if (p_img_dir_started.length() > 0) {
				for (int i = 0; i <= 14; i++) {
					p_img_started[i] = loadImage(this,
							setURL(p_img_dir_started + i + ".gif"));
				}
			}
			
			if (p_img_dir_current_time.length() > 0) {
				for (int i = 0; i <= 14; i++) {
					p_img_current_time[i] = loadImage(this,
							setURL(p_img_dir_current_time + i +
							".gif"));
				}
			}
			
			if (p_img_dir_elapsed.length() > 0) {
				for (int i = 0; i <= 14; i++) {
					p_img_elapsed[i] = loadImage(this,
							setURL(p_img_dir_elapsed + i + ".gif"));
				}
			}
			
			if (p_img_dir_next.length() > 0) {
				for (int i = 0; i <= 14; i++) {
					p_img_next[i] = loadImage(this, setURL(p_img_dir_next + i + ".gif"));
				}
			}
			
			if (p_img_dir_counter.length() > 0) {
				for (int i = 0; i <= 14; i++) {
					p_img_counter[i] = loadImage(this,
							setURL(p_img_dir_counter + i + ".gif"));
				}
			}
			
			p_img_dir_buttons = this.getParameter("img_dir_buttons", ""); // directory containing buttons images
			
			//load button images
			if (p_img_dir_buttons.length() > 0) {
				p_img_pause_off = loadImage(this,
						setURL(p_img_dir_buttons + "pause_off.gif"));
				p_img_pause_over = loadImage(this,
						setURL(p_img_dir_buttons +
						"pause_over.gif"));
				p_img_pause_on = loadImage(this,
						setURL(p_img_dir_buttons + "pause_on.gif"));
				
				p_img_stop_off = loadImage(this,
						setURL(p_img_dir_buttons + "stop_off.gif"));
				p_img_stop_over = loadImage(this,
						setURL(p_img_dir_buttons + "stop_over.gif"));
				p_img_stop_on = loadImage(this,
						setURL(p_img_dir_buttons + "stop_on.gif"));
				
				p_img_play_off = loadImage(this,
						setURL(p_img_dir_buttons + "play_off.gif"));
				p_img_play_over = loadImage(this,
						setURL(p_img_dir_buttons + "play_over.gif"));
				p_img_play_on = loadImage(this,
						setURL(p_img_dir_buttons + "play_on.gif"));
				
				p_img_break_off = loadImage(this,
						setURL(p_img_dir_buttons + "break_off.gif"));
				p_img_break_over = loadImage(this,
						setURL(p_img_dir_buttons +
						"break_over.gif"));
				p_img_break_on = loadImage(this,
						setURL(p_img_dir_buttons + "break_on.gif"));
				
				//create buttons
				button_pause = new ImageButton(p_img_pause_off, p_img_pause_on,
						p_img_pause_over);
				button_stop = new ImageButton(p_img_stop_off, p_img_stop_on,
						p_img_stop_over);
				button_play = new ImageButton(p_img_play_off, p_img_play_on,
						p_img_play_over);
				button_break = new ImageButton(p_img_break_off, p_img_break_on,
						p_img_break_over);
			}
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
	 * @return splitted_array of strings
	 */
	private String[] splitData(String input_string, char sep_ch) {
		
		if (input_string.length() <= 0) {
			return null;
		}
		
		String str1 = new String(); // temp var to contain found strings
		int element_num = 0; //number of found elements
		int size = 1; //number of found elements
		
		//count elements
		for (int i = 0; i < input_string.length(); i++) {
			if (input_string.charAt(i) == sep_ch) { //separator found
				size++; //count strings
			}
		}
		
		String splitted_array[] = new String[size]; // array of splitted string to return
		
		// analize string char by char
		for (int i = 0; i < input_string.length(); i++) {
			if (input_string.charAt(i) == sep_ch) { //separator found
				splitted_array[element_num] = str1.trim(); //put string to array
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
			splitted_array[element_num] = str1.trim(); //put string to vector
		}
		
		return splitted_array;
	}
	
//	-----------------------------------------------------------------------------
	/**
	 * Applet void constructor
	 */
	public JRelaxTimer() {
	}
	
//	-----------------------------------------------------------------------------
	/**
	 * Initialize the applet
	 */
	public void init() {
		
		//  display some info on console
		System.out.println(" ");
		System.out.println("JRelaxTimer " + JRELAXTIMER_VERSION);
		System.out.println("http://jddm.sourceforge.net");
		System.out.println("Author: Nicola Asuni");
		System.out.println("Copyright (c) 2003-2006 Tecnick.com s.r.l. - www.tecnick.com");
		System.out.println("Open Source License: GPL 2");
		System.out.println(" ");
		
		add(new Label("Loading...")); //display loading message
		validate();
		
		getParameters(); //get applet parameters (menu data)
		
		removeAll(); //remove loading message
		
		
		// ---------------------
		
		formatter = new SimpleDateFormat(p_display_pattern);
		
		//calculate timezone
		tz = TimeZone.getDefault(); //get local timezone
		
		//set timezone
		formatter.setTimeZone(tz);
		
		//build display panel
		setLayout(new FlowLayout(FlowLayout.RIGHT, 0, 0));
		this.setBackground(p_background_color);
		
		if (p_logo_image != null) {
			ImageButton button_logo = new ImageButton(p_logo_image, null, null);
			button_logo.addActionListener(new ActionListener() { // add listener for button events
				public void actionPerformed(ActionEvent e) { // an event occur
					showStatus("Information"); // display button description on status
					if (e.getID() == MouseEvent.MOUSE_PRESSED) { // Clicking on the button should open the relative popup menu or link
						getAppletContext().showDocument(setURL(p_logo_url), "_blank"); // load the URL on the target browser window
					}
				}
			});
			add(button_logo);
		}
		
		button_pause.addActionListener(new ActionListener() { // add listener for button events
			public void actionPerformed(ActionEvent e) { // an event occur
				showStatus("PAUSE"); // display button description on status
				if (e.getID() == MouseEvent.MOUSE_PRESSED) { // Clicking on the button should open the relative popup menu or link
					if (pause_status) {
						start();
						button_pause.setButtonStatus(false);
						button_stop.setButtonStatus(false);
						button_play.setButtonStatus(true);
					}
					else {
						if (play_status) {
							stop();
							button_pause.setButtonStatus(true);
							button_stop.setButtonStatus(false);
							button_play.setButtonStatus(false);
							pause_status = true;
						}
					}
				}
			}
		});
		button_pause.setButtonStatus(false);
		add(button_pause);
		
		button_stop.addActionListener(new ActionListener() { // add listener for button events
			public void actionPerformed(ActionEvent e) { // an event occur
				showStatus("STOP AND RESET"); // display button description on status
				if (e.getID() == MouseEvent.MOUSE_PRESSED) { // Clicking on the button should open the relative popup menu or link
					if (play_status || pause_status) {
						stop();
						button_pause.setButtonStatus(false);
						button_stop.setButtonStatus(true);
						button_play.setButtonStatus(false);
						pause_status = false;
					}
				}
			}
		});
		button_stop.setButtonStatus(false);
		add(button_stop);
		
		button_play.addActionListener(new ActionListener() { // add listener for button events
			public void actionPerformed(ActionEvent e) { // an event occur
				showStatus("START"); // display button description on status
				if (e.getID() == MouseEvent.MOUSE_PRESSED) { // Clicking on the button should open the relative popup menu or link
					if (!play_status) {
						start();
						button_pause.setButtonStatus(false);
						button_stop.setButtonStatus(false);
						button_play.setButtonStatus(true);
					}
				}
			}
		});
		button_play.setButtonStatus(true);
		add(button_play);
		
		button_break.addActionListener(new ActionListener() { // add listener for button events
			public void actionPerformed(ActionEvent e) { // an event occur
				showStatus("TAKE A BREAK"); // display button description on status
				if (e.getID() == MouseEvent.MOUSE_PRESSED) { // Clicking on the button should open the relative popup menu or link
					TakeBreak();
				}
			}
		});
		button_break.setButtonStatus(false);
		add(button_break);
		
		if (p_display_counter) {
			dpanel_counter = new DisplayPanel(p_counter_pattern.length(),
					p_counter_pattern,
					p_background_image_counter,
					p_img_counter,
					p_counter_pattern.length() *
					p_img_counter[8].getWidth(this),
					p_img_counter[8].getHeight(this));
			dpanel_counter.setBackground(p_background_color_counter);
			add(dpanel_counter);
		}
		
		if (p_display_next) {
			dpanel_next = new DisplayPanel(p_remaining_pattern.length(),
					p_remaining_pattern,
					p_background_image_next,
					p_img_next,
					p_remaining_pattern.length() *
					p_img_next[8].getWidth(this),
					p_img_next[8].getHeight(this));
			dpanel_next.setBackground(p_background_color_next);
			add(dpanel_next);
		}
		
		if (p_display_elapsed) {
			dpanel_elapsed = new DisplayPanel(p_display_pattern.length(),
					p_display_pattern,
					p_background_image_elapsed,
					p_img_elapsed,
					p_display_pattern.length() *
					p_img_elapsed[8].getWidth(this),
					p_img_elapsed[8].getHeight(this));
			dpanel_elapsed.setBackground(p_background_color_elapsed);
			add(dpanel_elapsed);
		}
		
		if (p_display_current_time) {
			dpanel_current_time = new DisplayPanel(p_display_pattern.length(),
					p_display_pattern,
					p_background_image_current_time,
					p_img_current_time,
					p_display_pattern.length() *
					p_img_current_time[8].getWidth(this),
					p_img_current_time[8].getHeight(this));
			dpanel_current_time.setBackground(p_background_color_current_time);
			add(dpanel_current_time);
		}
		
		if (p_display_started) {
			dpanel_started = new DisplayPanel(p_display_pattern.length(),
					p_display_pattern,
					p_background_image_started,
					p_img_started,
					p_display_pattern.length() *
					p_img_started[8].getWidth(this),
					p_img_started[8].getHeight(this));
			dpanel_started.setBackground(p_background_color_started);
			add(dpanel_started);
		}
		
		if ( (break_types != null) && (break_types.length > 1)) {
			// display type of break selector
			break_type_selector = new Choice();
			for (int i = 0; i < break_types.length; i++) {
				break_type_selector.add(break_types[i][0]);
			}
			break_type_selector.select(current_break_type);
			
			break_type_selector.addItemListener(new ItemListener() { // add listener for button events
				public void itemStateChanged(ItemEvent e) { // an event occur
					current_break_type = break_type_selector.getSelectedIndex();
				}
			});
			add(break_type_selector);
		}
		
		if ( (intervals != null) && (intervals.length > 1)) {
			// display type of break selector
			interval_selector = new Choice();
			for (int i = 0; i < intervals.length; i++) {
				interval_selector.add(intervals[i]);
			}
			interval_selector.select(String.valueOf(current_interval));
			
			interval_selector.addItemListener(new ItemListener() { // add listener for button events
				public void itemStateChanged(ItemEvent e) { // an event occur
					current_interval = Integer.parseInt(interval_selector.getSelectedItem());
					time_interval = current_interval * SECONDS_IN_MINUTE * 1000;
					pause_status = true;
					start();
				}
			});
			add(interval_selector);
		}
		
	} // end of init
	
	/**
	 * execute the break opening a popup page
	 * call the specified URL passing the following parameters:
	 * t for the type of break
	 * b the current break number
	 * i the time interval
	 */
	private void TakeBreak() {
		current_break++;
		if (p_display_counter) {
			dpanel_counter.setInfo(numberToPattern(current_break, p_counter_pattern)); //display current break number
		}
		//add parameters to url call
		String break_page = p_target_url;
		
		//call the selected directory/page
		if ( (break_types != null) && (break_types.length > 0)) {
			break_page += "/" + break_types[current_break_type][1]; //add directory on path
			int pages = Integer.parseInt(break_types[current_break_type][2], 10); //get number of available pages on dir
			int selected_page = current_break;
			//pages are called in cyclic way
			while (selected_page > pages) {
				selected_page -= pages;
			}
			break_page += "/" + String.valueOf(selected_page); // call selected page
			break_page += p_page_extension; //add page extension
		}
		else { //if no pages directories a re specified, the dynamic page technique is used:
			break_page += "?t=" + String.valueOf(current_break_type);
			break_page += "&b=" + String.valueOf(current_break);
			break_page += "&i=" + String.valueOf(current_interval);
		}
		getAppletContext().showDocument(setURL(break_page), "_blank"); // load the URL on the target browser window
	}
	
//	-----------------------------------------------------------------------------
	/**
	 * Start the timers
	 * reset the timers and counters
	 */
	public void start() {
		timer = new Thread(this);
		timer.start();
		play_status = true;
		Date now = new Date(); //get current time
		if (!pause_status) {
			start_time = now; //get current time
			if (p_display_started) {
				dpanel_started.setInfo(formatter.format(start_time)); //display start time
			}
			if (p_display_counter) {
				current_break = 0;
				dpanel_counter.setInfo(numberToPattern(current_break, p_counter_pattern)); //display current break number
			}
		}
		dtarget = now.getTime() + time_interval;
		pause_status = false;
	}
	
	/**
	 * Stop the applet
	 */
	public void stop() {
		timer = null;
		play_status = false;
	}
	
	/**
	 * Destroy the applet
	 */
	public void destroy() {
	}
	
	/**
	 * run
	 */
	public void run() {
		Thread me = Thread.currentThread();
		while (timer == me) {
			try {
				Thread.sleep(SLEEP_TIME);
			}
			catch (InterruptedException e) {
			}
			
			now = new Date(); //get current time
			remaining_time = now.getTime() - dtarget; //calculate remaining time to break
			if ( (now.getTime() > dtarget) && (remaining_time < SLEEP_TIME)) {
				dtarget = now.getTime() + time_interval;
				TakeBreak(); // call break
			}
			
			// update panels
			if (p_display_next) {
				dpanel_next.setInfo(millisecToPattern(Math.abs(remaining_time),
						p_remaining_pattern)); //display remaining time to next break
			}
			if (p_display_elapsed) {
				dpanel_elapsed.setInfo(millisecToPattern(now.getTime() -
						start_time.getTime(),
						p_display_pattern)); //display elapsed time from start
			}
			if (p_display_current_time) {
				dpanel_current_time.setInfo(formatter.format(now)); //display current time
			}
		}
	}
	
	/**
	 * Convert milliseconds to date pattern format.
	 * @param millisecs time to convert (milliseconds)
	 * @param tmppattern string pattern to apply
	 * @return String representation of time
	 */
	private String millisecToPattern(long millisecs, String tmppattern) {
		
		String tmpstr;
		
		long seconds = millisecs / 1000;
		
		seconds = Math.abs(seconds); //consider absolute value
		
		char[] pattern = tmppattern.toCharArray();
		
		//calculate time parts (replace pattern with values)
		
		// YEARS
		if (tmppattern.indexOf('y') >= 0) {
			int years = (int) (seconds / SECONDS_IN_YEAR);
			seconds = seconds % SECONDS_IN_YEAR;
			tmpstr = String.valueOf(years);
			int j = tmpstr.length() - 1;
			for (int i = tmppattern.lastIndexOf('y'); i >= tmppattern.indexOf('y'); i--) {
				if (j >= 0) {
					pattern[i] = tmpstr.charAt(j);
					j--;
				}
				else {
					pattern[i] = '0';
				}
			}
		}
		
		// MONTHS
		if (tmppattern.indexOf('M') >= 0) {
			int months = (int) (seconds / SECONDS_IN_MONTH);
			seconds = seconds % SECONDS_IN_MONTH;
			tmpstr = String.valueOf(months);
			int j = tmpstr.length() - 1;
			for (int i = tmppattern.lastIndexOf('M'); i >= tmppattern.indexOf('M'); i--) {
				if (j >= 0) {
					pattern[i] = tmpstr.charAt(j);
					j--;
				}
				else {
					pattern[i] = '0';
				}
			}
		}
		
		// DAYS
		if (tmppattern.indexOf('d') >= 0) {
			int days = (int) (seconds / SECONDS_IN_DAY);
			seconds = seconds % SECONDS_IN_DAY;
			tmpstr = String.valueOf(days);
			int j = tmpstr.length() - 1;
			for (int i = tmppattern.lastIndexOf('d'); i >= tmppattern.indexOf('d'); i--) {
				if (j >= 0) {
					pattern[i] = tmpstr.charAt(j);
					j--;
				}
				else {
					pattern[i] = '0';
				}
			}
		}
		
		// HOURS
		if (tmppattern.indexOf('H') >= 0) {
			int hours = (int) (seconds / SECONDS_IN_HOUR);
			seconds = seconds % SECONDS_IN_HOUR;
			tmpstr = String.valueOf(hours);
			int j = tmpstr.length() - 1;
			for (int i = tmppattern.lastIndexOf('H'); i >= tmppattern.indexOf('H'); i--) {
				if (j >= 0) {
					pattern[i] = tmpstr.charAt(j);
					j--;
				}
				else {
					pattern[i] = '0';
				}
			}
		}
		
		// MINUTES
		if (tmppattern.indexOf('m') >= 0) {
			int minutes = (int) (seconds / SECONDS_IN_MINUTE);
			seconds = seconds % SECONDS_IN_MINUTE;
			tmpstr = String.valueOf(minutes);
			int j = tmpstr.length() - 1;
			for (int i = tmppattern.lastIndexOf('m'); i >= tmppattern.indexOf('m'); i--) {
				if (j >= 0) {
					pattern[i] = tmpstr.charAt(j);
					j--;
				}
				else {
					pattern[i] = '0';
				}
			}
		}
		
		// SECONDS
		if (tmppattern.indexOf('s') >= 0) {
			tmpstr = String.valueOf(seconds);
			int j = tmpstr.length() - 1;
			for (int i = tmppattern.lastIndexOf('s'); i >= tmppattern.indexOf('s'); i--) {
				if (j >= 0) {
					pattern[i] = tmpstr.charAt(j);
					j--;
				}
				else {
					pattern[i] = '0';
				}
			}
		}
		
		return new String(pattern);
	}
	
	/**
	 * Convert number to pattern format.
	 * @param number num,ber to display
	 * @param tmppattern string pattern to apply
	 * @return String representation of number
	 */
	private String numberToPattern(long number, String tmppattern) {
		
		String tmpstr;
		
		number = Math.abs(number); //consider absolute value
		
		char[] pattern = tmppattern.toCharArray();
		
		//calculate time parts (replace pattern with values)
		
		if (tmppattern.indexOf('x') >= 0) {
			tmpstr = String.valueOf(number);
			int j = tmpstr.length() - 1;
			for (int i = tmppattern.lastIndexOf('x'); i >= tmppattern.indexOf('x'); i--) {
				if (j >= 0) {
					pattern[i] = tmpstr.charAt(j);
					j--;
				}
				else {
					pattern[i] = '0';
				}
			}
		}
		return new String(pattern);
	}
	
	
	/**
	 * Get Applet information.
	 * @return applet info
	 */
	public String getAppletInfo() {
		String message = "JRelaxTimer " + JRELAXTIMER_VERSION + "\n";
		message += "http://jrelaxtimer.sourceforge.net\n";
		message += "Author: Nicola Asuni\n";
		message += "Copyright (c) 2002-2006 Tecnick.com s.r.l. - www.tecnick.com\n";
		message += "Open Source License: GPL 2\n";
		return message;
	}
//	=============================================================================
	
}
