<?php
//============================================================+
// File name   : XMLConfigReader.php
// Begin       : 2004-10-19
// Last Update : 2005-03-20
//                                                             
// Description : Reads structured resource text data from an 
//               XML file and store it on array.
// Platform    : PHP 5
//
// Author: Nicola Asuni
//                                                             
// (c) Copyright:
//               Tecnick.com S.r.l.
//               Via Ugo Foscolo n.19
//               09045 Quartu Sant'Elena (CA)
//               ITALY
//               www.tecnick.com
//               info@tecnick.com                                                            
//============================================================+
 
/**
 * XML Configuration Reader Class (XMLConfigReader).
 * @package com.tecnick.xmlconfigreader
 */
 
/**
 * This PHP Class reads resource text data directly from a XML file and store it 
 * on a bidimensional array.
 * The first dimension array keys are taken from the value of the first attribute of the
 * <item> elements. The second dimension array keys are taken from the names
 * of the sub-items tags. The values off the array are taken from the content of sub-items tags.
 *
 * @name TMXResourceBundle
 * @package com.tecnick.xmlconfigreader
 * @abstract TMX-PHP Bridge Class
 * @link http://xmlcfgreader.sourceforge.net
 * @license http://www.gnu.org/copyleft/lesser.html LGPL
 * @author Nicola Asuni [www.tecnick.com]
 * @copyright Copyright (c) 2004-2005 - Tecnick.com S.r.l (www.tecnick.com) - Via Ugo Foscolo n.19 - 09045 Quartu Sant'Elena (CA) - ITALY - www.tecnick.com - info@tecnick.com
 * @version 1.0.000
 */
class XMLConfigReader {
	
	/**
	 * @var Array used to contain resources.
	 * @access private
	 */
	private $resource = array();
	
	/**
	 * @var Current primary key (item id).
	 * @access private
	 */
	private $current_primary_key = "";
	
	/**
	 * @var Current secondary key (sub-item element name).
	 * @access private
	 */
	private $current_secondary_key = "";
	
	/**
	 * @var Current data value (strings inside sub-item elements).
	 * @access private
	 */
	private $current_data = "";
	
	/**
	 * @var Is TRUE when we are inside a sub-item element
	 * @access private
	 */
	private $subitemdata = false;
		
	/**
	 * Class constructor.
	 * @param string $xmlfile XML file name
	 */
	public function __construct($xmlfile) {
		// reset array
		$this->resource = array();
		// creates a new XML parser to be used by the other XML functions
		$this->parser = xml_parser_create();
		// the following function allows to use parser inside object
		xml_set_object($this->parser, $this);
		// disable case-folding for this XML parser
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, 0);
		// sets the element handler functions for the XML parser
		xml_set_element_handler($this->parser, "startElementHandler", "endElementHandler");
		// sets the character data handler function for the XML parser
		xml_set_character_data_handler($this->parser, "dataContentHandler");
		// start parsing an XML document
		if(!xml_parse($this->parser, file_get_contents($xmlfile))) {
			die(sprintf("ERROR TMXResourceBundle :: XML error: %s at line %d",
			xml_error_string(xml_get_error_code($this->parser)),
			xml_get_current_line_number($this->parser)));
		}
		// free this XML parser
		xml_parser_free($this->parser);
	}
	
	/**
	 * Class destructor; resets $resource array.
	 */
	public function __destruct() {
		$resource = array(); // reset resource array
	}
	
	/**
	 * Sets the start element handler function for the XML parser parser.start_element_handler.
	 * @param resource $parser The first parameter, parser, is a reference to the XML parser calling the handler.
	 * @param string $name The second parameter, name, contains the name of the element for which this handler is called. If case-folding is in effect for this parser, the element name will be in uppercase letters. 
	 * @param array $attribs The third parameter, attribs, contains an associative array with the element's attributes (if any). The keys of this array are the attribute names, the values are the attribute values. Attribute names are case-folded on the same criteria as element names. Attribute values are not case-folded. The original order of the attributes can be retrieved by walking through attribs the normal way, using each(). The first key in the array was the first attribute, and so on. 
	 * @access private
	 */
	private function startElementHandler($parser, $name, $attribs) {
		switch(strtolower($name)) {
			case 'item': {
				// process item elements. 
				if (array_key_exists('id', $attribs)) {
					// Each item element contains an unique identifier (id).
					$this->current_primary_key = $attribs['id'];
					$this->resource[$this->current_primary_key] = array();
				}
				break;
			}
			default: {
				// process any other sub-item element
				if ((!empty($this->current_primary_key)) AND (empty($this->current_secondary_key))) {
					$this->current_secondary_key = $name;
					$this->current_data = "";
					$this->subitemdata = true;
				}
				break;
			}
		}
	}
	
	/**
	 * Sets the end element handler function for the XML parser parser.end_element_handler.
	 * @param resource $parser The first parameter, parser, is a reference to the XML parser calling the handler.
	 * @param string $name The second parameter, name, contains the name of the element for which this handler is called. If case-folding is in effect for this parser, the element name will be in uppercase letters. 
	 * @access private
	 */
	private function endElementHandler($parser, $name) {
		switch(strtolower($name)) {
			case 'item': {
				// discard item ID (primary key)
				$this->current_primary_key = "";
				break;
			}
			default: {
				if ((!empty($this->current_primary_key)) AND ($this->current_secondary_key == $name) ) {
					// get sub-item value
					if (!array_key_exists($this->current_secondary_key, $this->resource[$this->current_primary_key])) {
						// set new array element
						$this->resource[$this->current_primary_key][$this->current_secondary_key] = $this->current_data;
					}
					$this->current_secondary_key = "";
					$this->current_data = "";
					$this->subitemdata = false;
				}
				break;
			}
		}
	}
	
	/**
	 * Sets the character data handler function for the XML parser parser.handler.
	 * @param resource $parser The first parameter, parser, is a reference to the XML parser calling the handler.
	 * @param string $data The second parameter, data, contains the character data as a string. 
	 * @access private
	 */
	private function dataContentHandler($parser, $data) {
		if ($this->subitemdata) {
			$this->current_data .= $data;
		}
	}
	
	/**
	 * Get the item key for the selected subkey and value.
	 * 
	 * @param $subkey the sub-item key to search
	 * @param $subvalue the sub-item value to search
	 * @return the item key or false in case of error
	 */
	public function getKey($subkey, $subvalue) {
		reset($this->resource);
		while (list($itemkey, $subitem) = each($this->resource)) {
			if (isset($subitem[$subkey]) AND ($subitem[$subkey] == $subvalue)) {
				return $itemkey;
			}
		}
		return false;
	}
	
	/**
	 * Returns the resource array..
	 * @return Array.
	 */
	public function getResource() {
		return $this->resource;
	}
	
} // END OF CLASS

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
