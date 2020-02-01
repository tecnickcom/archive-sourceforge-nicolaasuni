package com.tecnick.tmxjavabridge;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.ObjectInputStream;
import java.io.ObjectOutputStream;
import java.io.Serializable;
import java.util.Enumeration;
import java.util.Hashtable;
import java.util.MissingResourceException;
import java.util.ResourceBundle;
import java.util.Vector;

import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.FactoryConfigurationError;
import javax.xml.parsers.ParserConfigurationException;

import org.w3c.dom.Attr;
import org.w3c.dom.Document;
import org.w3c.dom.Element;
import org.w3c.dom.NamedNodeMap;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;
import org.xml.sax.SAXException;

/**
 * <p>
 * Reads resource text data directly from a TMX (XML) file.
 * </p>
 * <p>
 * First, the TMXResourceBundle class instantiates itself with two parameters: a
 * TMX file name and a target language name. Then, using a DOM parser, it reads
 * all of a translation unit's properties for the key information and specified
 * language data and populates a hashtable with them.
 * </p>
 * <p>
 * <b>TMX info: </b> http://www.lisa.org/tmx/
 * </p>
 * 
 * <h4>Implementation notes</h4>
 * <p>
 * You instantiate the TMXResourceBundle class in a program to read data from a
 * TMX file. Once the class is instantiated, it reads all the data in a TMX file
 * and loads into a DOM tree. Then it populates a hashtable so the
 * handleGetObject() method can be called to find text information based on a
 * key just as a standard ResourceBundle class does. <br>
 * Instantiating the TMXResouceBundle class is the same as instantiating the
 * PropertyResourceBundle class. First you obtain a system language code (e.g.:
 * from a locale's information). In TMX the value of the attribute must be one
 * of the ISO language identifiers (a two- or three-letter code) or one of the
 * standard locale identifiers (a two- or three-letter language code, a dash,
 * and a two-letter region code).
 * </p>
 * 
 * Copyright (c) 2004-2006 Tecnick.com S.r.l (www.tecnick.com) Via Ugo Foscolo
 * n.19 - 09045 Quartu Sant'Elena (CA) - ITALY www.tecnick.com -
 * info@tecnick.com <br/> Project homepage: <a
 * href="http://tmxjavabridge.sourceforge.net"
 * target="_blank">http://tmxjavabridge.sourceforge.net</a><br/> License:
 * http://www.gnu.org/copyleft/lesser.html LGPL
 * 
 * @author Nicola Asuni [www.tecnick.com].
 * @version 1.1.008
 */

public class TMXResourceBundle extends ResourceBundle implements Serializable {

	/**
	 * Serial Version UID
	 */
	private static final long serialVersionUID = -2098421084432070017L;

	/**
	 * The hastable that will contain data loaded from XML
	 */
	protected Hashtable hashcontents = null;

	/**
	 * Number of translation units (tu) items
	 */
	protected int numberOfItems = 0;

	/**
	 * Vector to store tu items keys
	 */
	protected Vector vectOfItems;
	
	/**
	 * TMX to Hashtable conversion. Reads XML and store data in HashTable.
	 * 
	 * @param xmlfile
	 *            the TMX (XML) file to read, supports also URI resources or JAR
	 *            resources
	 * @param language
	 *            ISO language identifier (a two- or three-letter code)
	 */
	public TMXResourceBundle(String xmlfile, String language) {
		this(xmlfile, language, "");
	}

	/**
	 * Copy object data to this object.
	 * @param obj object to copy.
	 */
	private void copyTMXResourceBundle(TMXResourceBundle obj) {
		this.hashcontents = obj.hashcontents;
		this.numberOfItems = obj.numberOfItems;
		this.vectOfItems = obj.vectOfItems;
	}
	
	/**
	 * TMX to Hashtable conversion. Reads XML and store data in HashTable. NOTE:
	 * you must manually delete the cachefile to refresh its content.
	 * 
	 * @param xmlfile
	 *            the TMX (XML) file to read, supports also URI resources or JAR
	 *            resources
	 * @param language
	 *            ISO language identifier (a two- or three-letter code)
	 * @param cachefile
	 *            name of the file used to store cache data for the specified
	 *            language
	 */
	public TMXResourceBundle(String xmlfile, String language, String cachefile) {
		
		// try to get data from cachefile (if any)
		if (cachefile.length() > 0) {
			try {
				FileInputStream fis = new FileInputStream(cachefile);
				ObjectInputStream in = new ObjectInputStream(fis);
				copyTMXResourceBundle((TMXResourceBundle)in.readObject());
				in.close();
				return;
			} catch (Exception e) {
				System.err.println("Exception:" + e);
			}
		}
		
		String temp_key = null; // store hashtable key names
		String temp_value = null; // store hashtable values
		NamedNodeMap temp_list = null; // list of <tu> attributes
		Attr temp_attr = null; // <tu> attribute
		NodeList listOfTUVs = null; // list of <tuv> elements
		NodeList listOfSEG = null; // list of <seg> elements
		Element SEGElements = null; // <seg> element
		int numberOfTUVs = 0; // number of <tuv> elements

		// Create Document with parser
		Document document = parseXmlFile(xmlfile, false);

		// handle document error
		if (document == null) {
			hashcontents = new Hashtable(); // initialize a void hashtable
			return;
		}

		// Make a list of Term Units and count the number of items
		NodeList listOfTermUnits = document.getElementsByTagName("tu");
		numberOfItems = listOfTermUnits.getLength();

		// set tu keys vector size
		vectOfItems = new Vector(numberOfItems);

		// set hash size
		hashcontents = new Hashtable(numberOfItems);
		for (int i = 0; i < numberOfItems; i++) {
			temp_value = null;

			// set a key
			temp_list = listOfTermUnits.item(i).getAttributes();
			temp_attr = (Attr) temp_list.getNamedItem("tuid");
			temp_key = temp_attr.getValue();

			vectOfItems.add(temp_key); // store key on vector

			// get a value
			// Make a TUV list => "listOfTUVs"
			Node TUVs = listOfTermUnits.item(i);
			if (TUVs.getNodeType() == Node.ELEMENT_NODE) {
				Element TUVElements = (Element) TUVs;
				listOfTUVs = TUVElements.getElementsByTagName("tuv");
				numberOfTUVs = listOfTUVs.getLength();
			}

			// Check each TUV. If it's a specified lang, then get a SEG value
			for (int j = 0; j < numberOfTUVs; j++) {
				temp_list = listOfTUVs.item(j).getAttributes();
				temp_attr = (Attr) temp_list.getNamedItem("xml:lang");
				if (temp_attr.getValue().equalsIgnoreCase(language)) {
					// -- Get a SEG value
					SEGElements = (Element) listOfTUVs.item(j);
					listOfSEG = SEGElements.getElementsByTagName("seg");
					try {
						temp_value = listOfSEG.item(0).getFirstChild()
								.getNodeValue();
					} catch (Exception e) {
						// in case of error print error message and set value to
						// void string
						System.err.println(this.getClass().getName() + "(\""
								+ xmlfile + "\", \"" + language + "\") :: "
								+ "Void <seg> value on <tu tuid=\"" + temp_key
								+ "\"> key");
						temp_value = "";
					}
				}
			}

			// Populate hashtable
			if ((temp_key != null) && (temp_value != null)) {
				hashcontents.put(temp_key, temp_value);
			}
		} // for loop
		// try to save this object on cache file
		if (cachefile.length() > 0) {
			try {
				FileOutputStream fos = new FileOutputStream(cachefile);
				ObjectOutputStream out = new ObjectOutputStream(fos);
				out.writeObject(this);
				out.close();
			} catch (Exception e) {
				System.err.println("Exception:" + e);
			}
		}
	}

	/**
	 * Parses an XML file and returns a DOM document.
	 * 
	 * @param filename
	 *            the name of XML file
	 * @param validating
	 *            If true, the contents is validated against the DTD specified
	 *            in the file.
	 * @return the parsed document
	 */
	public Document parseXmlFile(String filename, boolean validating) {
		Document doc = null;
		DocumentBuilderFactory factory = null;
		// Create a builder factory
		try {
			factory = DocumentBuilderFactory.newInstance();
		} catch (FactoryConfigurationError e) {
			System.err.println(e);
			return null;
		}
		factory.setValidating(validating);
		// Create the builder and parse the file
		try {
			try {
				// try to get the file from jar
				InputStream instream = getClass().getResourceAsStream(filename);
				doc = factory.newDocumentBuilder().parse(instream);
			} catch (Exception ejar) {
				try {
					// try to get the file as external URI
					doc = factory.newDocumentBuilder().parse(filename);
				} catch (IOException euri) {
					try {
						// try to get the file as local filename
						doc = factory.newDocumentBuilder().parse(new File(filename));
					} catch (IOException efile) {
						try {
							// try to resolve the path as relative to local
							// class folder
							String[] classPath = System.getProperties().getProperty("java.class.path", ".").split(";");
							String newpath = classPath[0] + "/" + filename;
							doc = factory.newDocumentBuilder().parse(
									new File(newpath));
						} catch (IOException epath) {
							// unable to get the input file
							System.err.println("IOException:" + epath);
						}
					}
				}
			}
		} catch (ParserConfigurationException e) {
			System.err.println("[" + filename + "] ParserConfigurationException:" + e);
		} catch (SAXException e) {
			System.err.println("[" + filename + "] SAXException:" + e);
		}
		return doc;
	}

	/**
	 * Get key value, return default if void.
	 * 
	 * @param key
	 *            name of key
	 * @param def
	 *            default value
	 * @return parameter value or default
	 */
	public String getString(String key, String def) {
		String param_value = "";
		try {
			param_value = this.getString(key);
			if ((param_value != null) && (param_value.length() > 0)) {
				return param_value;
			}
		} catch (Exception e) {
			// for any exception return the default value
			return def;
		}
		return def;
	}

	/**
	 * handleGetObject implementation
	 * 
	 * @param key
	 *            the resource key
	 * @return the content associated to the specified key
	 * @throws MissingResourceException
	 */
	public final Object handleGetObject(String key) throws MissingResourceException {
		return hashcontents.get(key);
	}

	/**
	 * Returns the number of translation units
	 * 
	 * @return number of Items
	 */
	public int getNumberOfItems() {
		return numberOfItems;
	}

	/**
	 * Define getKeys method
	 * 
	 * @return item elements
	 */
	public Enumeration getKeys() {
		return vectOfItems.elements();
	}

}