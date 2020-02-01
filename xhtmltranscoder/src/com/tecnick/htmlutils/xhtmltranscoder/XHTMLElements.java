package com.tecnick.htmlutils.xhtmltranscoder;

import com.tecnick.xmlconfigreader.XMLConfigReader;

/**
 * <p>Config</p>
 * <p>Load  configuration files (XML and TMX) and store data as static
 * resources.<br>
 * The language code for TMX is taken from param_lang
 *
 * Copyright (c) 2004-2005 Tecnick.com S.r.l (www.tecnick.com) Via Ugo Foscolo
 * n.19 - 09045 Quartu Sant'Elena (CA) - ITALY - www.tecnick.com -
 * info@tecnick.com<br/>
 * License: http://www.gnu.org/copyleft/lesser.html LGPL
 * @author Nicola Asuni [www.tecnick.com].
 * @version 1.0.007
 */

public class XHTMLElements {
	
	/**
	 * XHTML elements status definitions loaded from XML configuration file
	 */
	private static XMLConfigReader xhtml_status = null;
	
	/**
	 * XHTML elements categories definitions loaded from XML configuration file
	 */
	private static XMLConfigReader xhtml_categories = null;
	
	/**
	 * XHTML elements definitions loaded from XML configuration file
	 */
	private static XMLConfigReader xhtml_tags = null;
	
	/**
	 * XHTML elements attributes definitions loaded from XML configuration file
	 */
	private static XMLConfigReader xhtml_attributes = null;
	
	/**
	 * Load XHTML elements definitions from default configuration files
	 * 
	 * @param config_dir String directory or URL where config files are stored
	 */
	public XHTMLElements(String config_dir) {
		xhtml_status = new XMLConfigReader(config_dir + "status.xml");
		xhtml_categories = new XMLConfigReader(config_dir + "categories.xml");
		xhtml_tags = new XMLConfigReader(config_dir + "tags.xml");
		xhtml_attributes = new XMLConfigReader(config_dir + "attributes.xml");
	}
	
	/**
	 * Void constructor.
	 * Load XHTML elements definitions from configuration files
	 */
	public XHTMLElements() {
		this("");
	}
	
	/**
	 * Returns an XMLConfigReader object for XHTML elements status definitions.
	 * @return XHTML elements status definitions.
	 */
	public XMLConfigReader getXHTMLStatus() {
		return xhtml_status;
	}
	
	/**
	 * Returns an XMLConfigReader object for XHTML elements categories definitions.
	 * @return XHTML elements categories definitions.
	 */
	public XMLConfigReader getXHTMLCategories() {
		return xhtml_categories;
	}
	
	/**
	 * Returns an XMLConfigReader object for XHTML elements definitions.
	 * @return XHTML elements definitions.
	 */
	public XMLConfigReader getXHTMLTags() {
		return xhtml_tags;
	}
	
	/**
	 * Returns an XMLConfigReader object for XHTML elements attributes definitions.
	 * @return XHTML elements attributes definitions.
	 */
	public XMLConfigReader getXHTMLAttributes() {
		return xhtml_attributes;
	}
	
}
