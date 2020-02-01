package com.tecnick.jxhtmledit;

import com.tecnick.htmlutils.xhtmltranscoder.XHTMLTranscoder;
import com.tecnick.tmxjavabridge.TMXResourceBundle;
import com.tecnick.xmlconfigreader.XMLConfigReader;

/**
 * Loads configutation files for JXHTMLEDIT.
 * 
 * <br/>Copyright (c) 2002-2006 Tecnick.com S.r.l (www.tecnick.com) Via Ugo
 * Foscolo n.19 - 09045 Quartu Sant'Elena (CA) - ITALY - www.tecnick.com -
 * info@tecnick.com <br/>
 * Project homepage: <a href="http://jxhtmledit.sourceforge.net" target="_blank">http://jxhtmledit.sourceforge.net</a><br/>
 * License: http://www.gnu.org/copyleft/gpl.html GPL 2
 * 
 * @author Nicola Asuni [www.tecnick.com].
 * @version 1.1.005
 */
public class JXHTMLConfig {
	
	/**
	 * Buttons definitions
	 */
	private static XMLConfigReader buttons;
	
	/**
	 * Localized text resources loaded from XML configuration file
	 */
	private static TMXResourceBundle resources;
	
	/**
	 * XHTMLTranscoder object.
	 */
	private static XHTMLTranscoder xhtml_transcoder;
	
	/**
	 * Constructor
	 * Must be called on applet init to load applet data from configuration files.
	 * @param config_dir String configuration files directory
	 * @param lang String ISO 639 language identifier (a two- or three-letter code)
	 */
	public JXHTMLConfig(String config_dir, String lang) {
		buttons = new XMLConfigReader(config_dir + "buttons.xml");
		resources = new TMXResourceBundle(config_dir + "tmx.xml", lang);
		xhtml_transcoder = new XHTMLTranscoder(config_dir);
	}
	
	/**
	 * Void Constructor.
	 */
	public JXHTMLConfig() {
		this("", "eng");
	}
	
	/**
	 * Returns an XMLConfigReader object containing buttons data.
	 * @return buttons data.
	 */
	public XMLConfigReader getButtons() {
		return buttons;
	}
	
	/**
	 * Returns localized text resources loaded from XML configuration file.
	 * @return text resources for the specified language.
	 */
	public TMXResourceBundle getTMXresources() {
		return resources;
	}
	
	/**
	 * Returns a XHTMLTranscoder object used to cleanup XHTML code.
	 * @return XHTMLTranscoder object.
	 */
	public XHTMLTranscoder getTranscoder() {
		return xhtml_transcoder;
	}
	
}
