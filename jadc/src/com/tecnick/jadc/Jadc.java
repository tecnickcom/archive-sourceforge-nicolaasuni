package com.tecnick.jadc;

import java.applet.Applet;
import java.awt.BorderLayout;
import java.awt.Color;
import java.awt.Image;
import java.awt.Label;
import java.awt.MediaTracker;
import java.net.MalformedURLException;
import java.net.URL;
import java.text.ParsePosition;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;
import java.util.TimeZone;

//-----------------------------------------------------------------------------
/**
 * Title: JADC (Advanced Digital Clock)<br>
 * Description: Applet to display digital clock or timer<br>
 *
 * <br/>Copyright (c) 2002-2006 Tecnick.com S.r.l (www.tecnick.com) Via Ugo
 * Foscolo n.19 - 09045 Quartu Sant'Elena (CA) - ITALY - www.tecnick.com -
 * info@tecnick.com <br/>
 * Project homepage: <a href="http://jddm.sourceforge.net" target="_blank">http://jxhtmledit.sourceforge.net</a><br/>
 * License: http://www.gnu.org/copyleft/gpl.html GPL 2
 * 
 * @author Nicola Asuni [www.tecnick.com].
 * @version 1.0.007
 */
public class Jadc
extends Applet
implements Runnable {
	
	/**
	 * serialVersionUID
	 */
	private static final long serialVersionUID = 6109313958742450102L;
	
	/**
	 * when false indicate that this is not a standalone application
	 */
	private boolean isStandalone = false;
	
	/**
	 * Software version
	 */
	private static final String JADC_VERSION = "1.0.007";
	
	/**
	 * The thread that displays clock
	 */
	Thread timer;
	
	/**
	 * display panel
	 */
	private DisplayPanel dpanel = null;
	
	/**
	 * only final variables can be used in inner anonymous classes
	 */
	final Applet a = this;
	
	// applet parameters <param />
	
	/**
	 * if true display time counter
	 */
	private boolean p_counter_mode;
	
	/**
	 * target time for counter
	 */
	private String p_counter_time;
	
	/**
	 * if true use local timezone
	 */
	private boolean p_local_time;
	
	/**
	 * This is the hours offset to add to UTC to get local time.
	 */
	private int p_timezone_hours;
	
	/**
	 * This is the minutes offset to add to UTC to get local time.
	 */
	private int p_timezone_minutes;
	
	/**
	 * if true display year
	 */
	private String p_input_pattern;
	
	/**
	 * if true display year
	 */
	private String p_display_pattern;
	
	/**
	 * background color (RRGGBB)
	 */
	private Color p_background_color;
	
	/**
	 * background image
	 */
	private Image p_background_image;
	
	/**
	 * number and symbols images
	 */
	private Image[] p_img = new Image[15];
	
	/**
	 * current time
	 */
	private Date now;
	
	/**
	 * date formatter
	 */
	private SimpleDateFormat formatter;
	
	/**
	 * target date formatter (for counter)
	 */
	private SimpleDateFormat target_formatter;
	
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
	private static final int SLEEP_TIME = 500;
	
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
	
//	-----------------------------------------------------------------------------
	/**
	 * get applet parameter value, return default if void
	 * @param key name of parameter to read
	 * @param def default value
	 * @return parameter value or default
	 */
	private String getParameter(String key, String def) {
		if (isStandalone) {
			return System.getProperty(key, def);
		}
		else {
			String param_value = getParameter(key);
			if ( (param_value != null) && (param_value.length() > 0)) {
				return param_value;
			}
		}
		return def;
	}
	
//	-----------------------------------------------------------------------------
	/**
	 * Computes full, canonical URL from a relative specification.
	 * @param link string containing url
	 * @return url
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
			p_counter_mode = Boolean.valueOf(this.getParameter("counter_mode",
			"false")).booleanValue(); // if true display time counter
			p_counter_time = this.getParameter("counter_time", "2000-01-01 00:00:00"); // target time for counter
			p_local_time = Boolean.valueOf(this.getParameter("local_time", "true")).
			booleanValue(); // if true use local timezone
			p_timezone_hours = Integer.parseInt(this.getParameter("timezone_hours",
			"0")); // This is the hours offset to add to UTC to get local time.
			p_timezone_minutes = Integer.parseInt(this.getParameter(
					"timezone_minutes", "0")); // This is the minutes offset to add to UTC to get local time.
			p_input_pattern = this.getParameter("input_pattern",
			"yyyy-MM-dd HH:mm:ss"); // input pattern for p_counter_time
			p_display_pattern = this.getParameter("display_pattern",
			"yyyy-MM-dd HH:mm:ss"); // display pattern
			p_background_color = new Color(Integer.parseInt(this.getParameter(
					"background_color", "FFFFFF"), 16)); //background color (RRGGBB)
			p_background_image = loadImage(this,
					setURL(this.getParameter(
							"background_image",
					""))); // background image
			//number and symbols images:
			p_img[0] = loadImage(this, setURL(this.getParameter("img_0", ""))); // 0
			p_img[1] = loadImage(this, setURL(this.getParameter("img_1", ""))); // 1
			p_img[2] = loadImage(this, setURL(this.getParameter("img_2", ""))); // 2
			p_img[3] = loadImage(this, setURL(this.getParameter("img_3", ""))); // 3
			p_img[4] = loadImage(this, setURL(this.getParameter("img_4", ""))); // 4
			p_img[5] = loadImage(this, setURL(this.getParameter("img_5", ""))); // 5
			p_img[6] = loadImage(this, setURL(this.getParameter("img_6", ""))); // 6
			p_img[7] = loadImage(this, setURL(this.getParameter("img_7", ""))); // 7
			p_img[8] = loadImage(this, setURL(this.getParameter("img_8", ""))); // 8
			p_img[9] = loadImage(this, setURL(this.getParameter("img_9", ""))); // 9
			p_img[10] = loadImage(this, setURL(this.getParameter("img_sep", ""))); // digits separator
			p_img[11] = loadImage(this, setURL(this.getParameter("img_dec", ""))); // decimal separator
			p_img[12] = loadImage(this, setURL(this.getParameter("img_blk", ""))); // blank digit image (filler)
			p_img[13] = loadImage(this, setURL(this.getParameter("img_pos", ""))); // plus symbol
			p_img[14] = loadImage(this, setURL(this.getParameter("img_neg", ""))); // minus symbol
			
			
			// FIX missing date parameters when in counter mode:
			
			//add current year if not specified:
			if (p_counter_mode) {
				Calendar rightNow = Calendar.getInstance(); //get current date
				if (p_input_pattern.indexOf("yyyy") < 0) {
					p_input_pattern += " yyyy";
					int current_year = rightNow.get(Calendar.YEAR);
					p_counter_time += " "+String.valueOf(current_year);
				}
				//add current month if not specified:
				if ( (p_input_pattern.indexOf("MM") < 0) && (p_input_pattern.indexOf("D") < 0) ) {
					p_input_pattern += " MM";
					int current_month = rightNow.get(Calendar.MONTH) + 1;
					p_counter_time += " ";
					if (current_month < 10) {
						p_counter_time += "0";
					}
					p_counter_time += String.valueOf(current_month);
				}
				//add current day if not specified:
				if ( (p_input_pattern.indexOf("dd") < 0) && (p_input_pattern.indexOf("D") < 0) ) {
					p_input_pattern += " dd";
					int current_day = rightNow.get(Calendar.DATE);
					p_counter_time += " ";
					if (current_day < 10) {
						p_counter_time += "0";
					}
					p_counter_time += String.valueOf(current_day);
				}
			}
			
		}
		catch (Exception e) {
			e.printStackTrace();
		}
	}
	
//	-----------------------------------------------------------------------------
	/**
	 * Applet void constructor
	 */
	public Jadc() {
	}
	
//	-----------------------------------------------------------------------------
	/**
	 * Initialize the applet
	 */
	public void init() {
		
		//display some info on console
		System.out.println(" ");
		System.out.println("JADC (Drop Down Menu) " + JADC_VERSION);
		System.out.println("http://jadc.sourceforge.net");
		System.out.println("Author: Nicola Asuni");
		System.out.println("Copyright (c) 2002-2006 Tecnick.com s.r.l. - www.tecnick.com");
		System.out.println("Open Source License: GPL 2");
		System.out.println(" ");
		
		add(new Label("Loading...")); //display loading message
		validate();
		
		getParameters(); //get applet parameters (menu data)
		
		removeAll(); //remove loading message
		
		// ---------------------
		
		formatter = new SimpleDateFormat(p_display_pattern);
		
		//calculate timezone
		if (p_local_time) {
			tz = TimeZone.getDefault(); //get local timezone
		}
		else { // Set offset to add to UTC to get local time.
			tz = TimeZone.getTimeZone("GMT");
			int offsetMillis = ( (p_timezone_hours * SECONDS_IN_HOUR) +
					(p_timezone_minutes * SECONDS_IN_MINUTE)) * 1000;
			tz.setRawOffset(offsetMillis);
		}
		
		//set timezone
		formatter.setTimeZone(tz);
		
		String temp_pattern = p_display_pattern;
		int display_digits = p_display_pattern.length();
		if (p_counter_mode) {
			temp_pattern = " " + temp_pattern;
			display_digits++;
		}
		
		int ah = a.getSize().height; // applet height
		int aw = a.getSize().width; // applet width
		
		//build display panel
		dpanel = new DisplayPanel(display_digits, temp_pattern, p_background_image,
				p_img, aw, ah);
		
		setLayout(new BorderLayout());
		this.setBackground(p_background_color);
		add(dpanel, BorderLayout.CENTER);
	} // end of init
	
//	-----------------------------------------------------------------------------
	/**
	 * Start the applet
	 */
	public void start() {
		timer = new Thread(this);
		timer.start();
	}
	
	/**
	 * Stop the applet
	 */
	public void stop() {
		timer = null;
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
				//Thread.currentThread().sleep(SLEEP_TIME);
				Thread.sleep(SLEEP_TIME);
			}
			catch (InterruptedException e) {
			}
			
			now = new Date(); //get current time
			
			if (p_counter_mode) { //counter mode
				target_formatter = new SimpleDateFormat(p_input_pattern);
				target_formatter.setLenient(true); //make parsing tollerant
				target_formatter.setTimeZone(tz); //set timezone
				Date dtarget = new Date();
				try {
					dtarget = target_formatter.parse(p_counter_time, new ParsePosition(0)); //get target date
				}
				catch (NullPointerException e) {
				}
				
				//convert time_diff to selected display pattern
				dpanel.setInfo(millisecToPattern(now.getTime() - dtarget.getTime()));
			}
			else {
				dpanel.setInfo(formatter.format(now));
			}
		}
	}
	
	/**
	 * Convert milliseconds to date pattern format.
	 * @param millisecs time to convert (milliseconds)
	 * @return String representation of time
	 */
	private String millisecToPattern(long millisecs) {
		
		String tmpstr;
		String tmppattern = p_display_pattern;
		
		long seconds = millisecs / 1000;
		
		if (p_counter_mode) { //add +/- symbol by case
			if (seconds > 0) {
				tmppattern = "+" + tmppattern;
			}
			else {
				if (seconds < 0) {
					tmppattern = "-" + tmppattern;
				}
				else {
					tmppattern = " " + tmppattern;
				}
			}
		}
		
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
	 * Get Applet information
	 * @return applet information
	 */
	public String getAppletInfo() {
		String message = "JADC (Advanced Digital Clock) " + JADC_VERSION + "\n";
		message += "http://jadc.sourceforge.net\n";
		message += "Author: Nicola Asuni\n";
		message += "Copyright (c) 2002-2006 Tecnick.com s.r.l. - www.tecnick.com\n";
		message += "Open Source License: GPL 2\n";
		return message;
	}
//	=============================================================================
	
}
