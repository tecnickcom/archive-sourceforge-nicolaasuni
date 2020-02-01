package com.tecnick.xmlconfigreader;

import java.io.File;
import java.io.IOException;
import java.io.InputStream;
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
import org.w3c.dom.NamedNodeMap;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;
import org.xml.sax.SAXException;

/**
 * <p>
 * Reads structured resource text data from an XML file and store it on a
 * hashtable.
 * </p>
 * <p>
 * The hashtable keys are taken from the value of the first attribute of the
 * &lt;item&gt; elements. <br>
 * The hash table values are hashtables containing the sub-items names as keys
 * and the sub-items data as value.
 * </p>
 * <p>
 * 
 * <h4>Implementation notes</h4>
 * <p>
 * You instantiate the XMLConfigReader class in a program to read data from a XML
 * file. Once the class is instantiated, it reads all the data in a XML file and
 * loads into a DOM tree. Then it populates a hashtable so the getString method
 * can be called to find text information based on a key and subkey.
 * </p>
 * 
 * Copyright (c) 2004-2005 
 * Tecnick.com S.r.l (www.tecnick.com) 
 * Via Ugo Foscolo n.19 - 09045 Quartu Sant'Elena (CA) - ITALY 
 * www.tecnick.com - info@tecnick.com <br/>
 * Project homepage: <a href="http://xmlconfigreader.sourceforge.net" target="_blank">http://xmlconfigreader.sourceforge.net</a><br/>
 * License: http://www.gnu.org/copyleft/lesser.html LGPL
 * 
 * @author Nicola Asuni [www.tecnick.com].
 * @version 1.0.003
 */

public class XMLConfigReader extends ResourceBundle {
	
	/**
	 * The hastable that will contain data loaded from XML.
	 */
	protected Hashtable hashcontents = null;
	
	/**
	 * Number of items (&lt;item&gt elements).
	 */
	protected int numberOfItems = 0;
	
	/**
	 * Vector to store items keys (&lt;item&gt;).
	 */
	protected Vector vectOfItems;
	
	/**
	 * TMX to Hashtable conversion. Reads XML and store data in HashTable.
	 * 
	 * @param xmlfile the XML file to read, supports also URI resources or JAR resources
	 */
	public XMLConfigReader(String xmlfile) {
		
		// Create Document with parser
		Document document = parseXmlFile(xmlfile, false);
		
		// handle document error
		if (document == null) {
			hashcontents = new Hashtable(); //initialize a void hashtable
			return;
		}
		
		// Make a list of Items and count the number of items
		NodeList listOfItems = document.getElementsByTagName("item");
		numberOfItems = listOfItems.getLength();
		
		// set items keys vector size
		vectOfItems = new Vector(numberOfItems);
		
		// set hash size
		hashcontents = new Hashtable(numberOfItems);
		
		//iterate thru items
		for (int i = 0; i < numberOfItems; i++) {
			
			NodeList listOfSUBs = null;
			int numberOfSUBs = 0;
			
			// set a key
			NamedNodeMap temp_list = listOfItems.item(i).getAttributes();
			Attr temp_attr = (Attr) temp_list.item(0);
			String temp_key = temp_attr.getValue();
			
			vectOfItems.add(temp_key); // store key on vector
			
			// make a list of Sub Items and count the number of sub items
			Node SUBs = listOfItems.item(i);
			listOfSUBs = SUBs.getChildNodes();
			numberOfSUBs = listOfSUBs.getLength();
			
			// set hash table to store sub items
			Hashtable hashsubcontents = new Hashtable(numberOfSUBs);
			
			// Check each sub item. and store values on a hash table
			for (int j = 0; j < numberOfSUBs; j++) {
				//consider only elements nodes
				if (listOfSUBs.item(j).getNodeType() == Node.ELEMENT_NODE) {
					String temp_name = null;
					String temp_value = null;
					temp_name = listOfSUBs.item(j).getNodeName(); //get element name
					try {
						temp_value = listOfSUBs.item(j).getFirstChild().getNodeValue(); //get element value
					} catch (Exception e) {
						//System.err.println(this.getClass().getName() + "(\"" + xmlfile + "\") :: " + ":: Void <" + temp_name + "> value on <" + temp_key + "> item");
						temp_value = "";
					}
					if ((temp_name != null) && (temp_value != null)) {
						hashsubcontents.put(temp_name, temp_value); // store this  value on hash table
					}
				}
			}
			
			// Populate hashtable
			if ((temp_key != null) && (hashsubcontents != null)) {
				hashcontents.put(temp_key, hashsubcontents);
			}
		} // for loop
	} // convert
	
	/**
	 * Parses an XML file and returns a DOM document.
	 * 
	 * @param filename the name of XML file
	 * @param validating If true, the contents is validated against the DTD specified in the file.
	 * @return the parsed document
	 */
	public Document parseXmlFile(String filename, boolean validating) {
		Document doc = null;
		DocumentBuilderFactory factory = null;
		//  Create a builder factory
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
							// try to resolve the path as relative to local class folder
							String[] classPath = System.getProperties().getProperty("java.class.path", ".").split(";");
							String newpath = classPath[0] + "/" + filename;
							doc = factory.newDocumentBuilder().parse(new File(newpath));
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
	 * handleGetObject implementation.
	 * 
	 * @param key the resource key
	 * @return the content associated to the specified key
	 * @throws MissingResourceException
	 */
	public final Object handleGetObject(String key) throws MissingResourceException {
		return hashcontents.get(key);
	}
	
	/**
	 * Returns the number of items.
	 * 
	 * @return number of Items
	 */
	public int getNumberOfItems() {
		return numberOfItems;
	}
	
	/**
	 * Define getKeys method.
	 * 
	 * @return item elements
	 */
	public Enumeration getKeys() {
		return vectOfItems.elements();
	}
	
	/**
	 * Overloading of getString method with additional subkey and default
	 * parameter.
	 * 
	 * @param key name of key
	 * @param subkey name of sub key
	 * @param def default value
	 * @return parameter value or default
	 */
	public String getString(String key, String subkey, String def) {
		String param_value = "";
		try {
			Hashtable subtable = (Hashtable) this.getObject(key);
			param_value = (String) subtable.get(subkey);
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
	 * Overloading of getString method with additional subkey.
	 * 
	 * @param key name of key
	 * @param subkey name of sub key
	 * @return parameter value or void string
	 * @see #getString(String, String, String)
	 */
	public String getString(String key, String subkey) {
		return getString(key, subkey, "");
	}
	
	/**
	 * Call getString method to get an int value.
	 * 
	 * @param key name of key
	 * @param subkey name of sub key
	 * @param def default value
	 * @return parameter value or default
	 */
	public int getInt(String key, String subkey, int def) {
		int i = 0;
		String param_value = getString(key, subkey);
		try {
			i = Integer.parseInt(param_value);
		} catch (NumberFormatException e) {
			i = def; // return the default value
		}
		return i;
	}
	
	/**
	 * Call getString method to get a double value.
	 * 
	 * @param key name of key
	 * @param subkey name of sub key
	 * @param def default value
	 * @return parameter value or default
	 */
	public double getDouble(String key, String subkey, double def) {
		double i = 0;
		String param_value = getString(key, subkey);
		try {
			i = Double.parseDouble(param_value);
		} catch (NumberFormatException e) {
			i = def; // return the default value
		}
		return i;
	}
	
	/**
	 * Get the key for the selected subkey and value.
	 * 
	 * @param subkey String the subkey
	 * @param value String the value to search
	 * @return String the main key or null if not found
	 */
	public String getKey(String subkey, String value) {
		String current_key; // store current key
		Enumeration data_keys = getKeys(); // get current keys
		while (data_keys.hasMoreElements()) {
			current_key = (String) data_keys.nextElement();
			if (getString(current_key, subkey).compareToIgnoreCase(value) == 0) {
				return current_key;
			}
		}
		return null;
	}
	
}