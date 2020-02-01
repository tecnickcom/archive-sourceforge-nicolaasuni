package com.tecnick.jplaysound;

import java.applet.Applet;
import java.applet.AudioClip;
import java.awt.Button;
import java.awt.Font;
import java.awt.GridLayout;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.net.MalformedURLException;
import java.net.URL;

/**
 * <p>Title: PlaySound</p>
 * <p>Description: a button to play "au" sound files</p>
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

public class JPlaySound extends Applet {
	
	/**
	 * serialVersionUID
	 */
	private static final long serialVersionUID = 1730097318509272844L;
	
	/**
	 * Software version
	 */
	private static final String PLAYSOUND_VERSION = "1.0.001";
	
	/**
	 * Sound file to play.
	 * Format: 8-bit mu-Law Encoded Next/Sun AU - 8000Hz, 16-bit, Mono
	 */
	private String p_soundfile;
	
	/**
	 * Label for the "play" button.
	 */
	private String p_label;
	
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
	
	/**
	 * Applet constructor (void)
	 */
	public JPlaySound() {
	}
	
	/**
	 * Initialize the applet
	 */
	public void init() {
		try {
			p_soundfile = this.getParameter("soundfile", "");
		}
		catch(Exception e) {
			e.printStackTrace();
		}
		try {
			p_label = this.getParameter("label", ">");
		}
		catch(Exception e) {
			e.printStackTrace();
		}
		try {
			jbInit();
		}
		catch(Exception e) {
			e.printStackTrace();
		}
		
		Button b = new Button();
		
		Font bfont = new Font("Arial, Verdana, Helvetica", Font.BOLD, 18);
		b.setFont(bfont);
		b.setLabel(p_label);
		add(b);
		
		setLayout(new GridLayout());
		
		validate();
		
		if (p_soundfile.length() > 1) {
			final AudioClip bsound = getAudioClip(setURL(p_soundfile));
			
			b.addActionListener( new ActionListener() {
				public void actionPerformed(ActionEvent e) {
					if (bsound != null) {
						bsound.play();
					}
				}
			});
		}
		
		
		
	}
	
	/**
	 * Component initialization
	 * @throws Exception
	 */
	private void jbInit() throws Exception {
	}
	
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
	 * Get Applet information.
	 * @return applet info
	 */
	public String getAppletInfo() {
		String message = "JPlaySound" + PLAYSOUND_VERSION + "\n";
		message += "http://jplaysound.sourceforge.net\n";
		message += "Author: Nicola Asuni\n";
		message += "Copyright (c) 2002-2006 Tecnick.com s.r.l. - www.tecnick.com\n";
		message += "Open Source License: GPL 2\n";
		return message;
	}
	
	/**
	 * Returns parameters information.
	 * @return parameters information.
	 */
	public String[][] getParameterInfo() {
		String[][] pinfo =
		{
				{"soundfile", "String", "sound file name and path"},
				{"label", "String", "button label"},
		};
		return pinfo;
	}
	
}