<?php
//============================================================+
// File name   : XHTMLTranscoder.php
// Begin       : 2002-05-25
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
 * XHTMLTranscoder is an open-source PHP 5 class that quickly converts broken HTML code to well-formed XHTML.
 * @package com.tecnick.xhtmltranscoder
 */
 
/**
 * Define the directory path where are stored the XML configuration files for XHTML elements.
 */
define ("XHTMLDATADIR", "config/");

/**
 * Loads the XML configuration files reader.
 */
require_once("XMLConfigReader.php");

/**
 * XHTMLTranscoder is an open-source PHP 5 class that quickly converts broken HTML code to well-formed XHTML.
 * XHTMLTranscoder is a fast transcoder useful to convert HTML code in real-time.
 * This class do not check headers, it checks only the general rules for tags, attributes and nesting: tags (elements) names in lowercase; attributes names in lowercase; elements nesting; elements termination; unquoted attributes; unminimized attributes; unterminated empty tags; preserve other languages elements (php, asp, jsp, ...).
 *
 * @name XHTMLTranscoder
 * @package com.tecnick.xhtmltranscoder
 * @abstract PHP 5 class that quickly converts broken HTML code to well-formed XHTML.
 * @link http://xmlvalidator.sourceforge.net
 * @license http://www.gnu.org/copyleft/lesser.html LGPL
 * @author Nicola Asuni [www.tecnick.com]
 * @copyright Copyright (c) 2004-2005 - Tecnick.com S.r.l (www.tecnick.com) - Via Ugo Foscolo n.19 - 09045 Quartu Sant'Elena (CA) - ITALY - www.tecnick.com - info@tecnick.com
 * @version 1.0.000
 */
class XHTMLTranscoder {
	
	/**
	 * XHTML elements status object.
	 */
	private static $html_status_obj;
	
	/**
	 * XHTML elements categories object.
	 */
	private static $html_categories_obj;
	
	/**
	 * XHTML elements definitions object.
	 */
	private static $html_tags_obj;
	
	/**
	 * XHTML elements attributes object.
	 */
	private static $html_attributes_obj;
	

	/**
	 * XHTML elements status array.
	 */
	private static $html_status;
	
	/**
	 * XHTML elements categories array.
	 */
	private static $html_categories;
	
	/**
	 * XHTML elements definitions array.
	 */
	private static $html_tags;
	
	/**
	 * XHTML elements attributes array.
	 */
	private static $html_attributes;
		
	/**
	 * Initialize transcoder loading XHTML elements data from XML files.
	 */
	public function __construct() {
		// load XHTML elements data from XML configuration files.
		$this->html_status_obj = new XMLConfigReader(XHTMLDATADIR."status.xml");
		$this->html_categories_obj = new XMLConfigReader(XHTMLDATADIR."categories.xml");
		$this->html_tags_obj = new XMLConfigReader(XHTMLDATADIR."tags.xml");
		$this->html_attributes_obj = new XMLConfigReader(XHTMLDATADIR."attributes.xml");
		
		// get data arrays
		$this->html_status = $this->html_status_obj->getResource();
		$this->html_categories = $this->html_categories_obj->getResource();
		$this->html_tags = $this->html_tags_obj->getResource();
		$this->html_attributes = $this->html_attributes_obj->getResource();
	}
	
	/**
	 * Trancode using default parameters (false, false, "UTF-8")
	 * 
	 * @param code_to_clean String text to transcode
	 * @return String transcoded text
	 */
	 
	/**
	 * Get generic HTML and returns XHTML code cleaned up.
	 * XHTMLTranscoder is a <em>fast</em> transcoder useful to convert HTML
	 * code in real-time. 
	 * This class do not check headers, it checks only the general rules for
	 * tags, attributes and nesting:
	 * tags (elements) names in lowercase; 
	 * attributes names in lowercase; 
	 * elements nesting; 
	 * elements termination; 
	 * unquoted attributes; 
	 * unminimized attributes; 
	 * unterminated empty tags; 
	 * preserve other languages elements (php, asp, jsp, ...). 
	 * 
	 * @param code_to_clean String the text to transcode
	 * @param indent boolean if true return the text indented
	 * @param entities_off boolean if true replace htmlentities with extended chars
	 * @param encoding String document encoding (e.g.: "UTF-8")
	 * @return String the transcoded text
	 */
	public function transcode($code_to_clean, $indent = true, $entities_off = false, $encoding = "UTF-8") {
				
		//CRLF to LF (windows to unix style)
		$code_to_clean = str_replace("\r\n", "\n", $code_to_clean);
		
		//remove session variable PHPSESSID
		$code_to_clean = preg_replace("/(\?|\&|%3F|%26|\&amp;|%26amp%3B)PHPSESSID(=|%3D)[a-z0-9]{32,32}/i", "", $code_to_clean); 
		
		// convert equivalent HTML code entities 
		$code_to_clean = $this->unhtmlentities($code_to_clean, true);
		
		// replace extended UTF-8 characters with html numeric entities
		$code_to_clean = $this->htmlentitiesUTF8($code_to_clean);
		
		// try to get the charset encoding from document
		if (preg_match("/<\?xml[\s]+version=['\"]?[0-9][\.][0-9]['\"]?[\s]+encoding=['\"]?([^'\"<>]*)['\"]?\?".">/i", $code_to_clean, $regs) ) {
			$doc_charset = trim($regs[1]);
		}
		elseif ( preg_match("/<meta[\s]+http-equiv=['\"]?Content-Type['\"]?[\s]+content=['\"]?([^'\"<>]*)/i", $code_to_clean, $regs) ) {
			if ( preg_match("/charset=([^'\"<> ]*)/i", $regs[1], $regs2) ) {
				$doc_charset = trim($regs2[1]);
			}
		}
		if (!$doc_charset) {
			$doc_charset = $encoding; //use the default charset
		}
		
		$codelen = strlen($code_to_clean); //number of characters in code
		$xhtml_code = ""; //this will contain the return code
		$tag_list = Array(); //list of open (not closed) tags
		$subtag = false; //is true when a subtag is found (php, jsp, asp tags)
		$checkindent = true; //check if add indentation before non tag data
		$i = 0;
		$j = -1;
		$k = 0;
		
		while ($i < $codelen) {
			$currentchar = substr($code_to_clean, $i, 1);
			
			// OPEN TAG FOUND =======================================
			if ( ($currentchar == "<") AND (eregi("[[:alpha:]]", substr($code_to_clean, $i+1, 1))) ) { //we are inside a tag
				$checkindent = true;
				$j++;
				$tag_list[$j] = "";
				do {//get tag name
					$tag_list[$j] .= strtolower($currentchar); //put open tag in a list
					$i++;
					$currentchar = substr($code_to_clean, $i, 1);
				} while (eregi("[[:alpha:][:digit:]]", $currentchar));
				
				//clean tabs and newlines from the end of the code
				if (strlen($xhtml_code)) {
					while (eregi("[\t\n]", substr($xhtml_code, strlen($xhtml_code)-1, 1)) ) {
						$xhtml_code = substr($xhtml_code, 0, strlen($xhtml_code)-1);
					}
				}
				
				//check if some special tags has been closed
				if ($j>0) {
					if (!strcasecmp(substr($tag_list[$j], 1),$tag_list[$j-1])) {
						$lct_name = $tag_list[$j-1];						
						$tagkey = $this->html_tags_obj->getKey("name", $lct_name);
						if (($tagkey !== false) AND ($this->html_tags[$tagkey]["endtag"] == "2")) {
							if ($indent) { //add indentation
								$xhtml_code .= "\n"; //put tag in a new line
								for ($k=0; $k<($j-1); $k++) {
									$xhtml_code .= "\t"; //indent code
								}
							}
							$xhtml_code .= "</".$lct_name.">"; // add closing tag
							unset($tag_list[$j]); //remove tag from list
							$tag_list[$j-1] = "<".$lct_name."";
							$j--;
						}
					}
				}
				if ($indent) { //add indentation
					$xhtml_code .= "\n"; //put tag in a new line
					for ($k=0; $k<$j; $k++) {
						$xhtml_code .= "\t"; //indent code
					}
				}
				$xhtml_code .= $tag_list[$j];
				$tag_list[$j] = substr($tag_list[$j], 1); //remove first "<" from the tag
				
				//check if it's an empty element
				$tagkey = $this->html_tags_obj->getKey("name", $tag_list[$j]);
				$emptyelement = false;
				if ($tagkey !== false) {
					$emptyelement = ($this->html_tags[$tagkey]["endtag"] == 0);
				}
								
				while (($currentchar != ">") OR ( ($currentchar == ">") AND ($subtag) ) ) { //check tag attributes
					
					if (!$subtag) { //we are not inside a subtag
						if (eregi("[[:alpha:]]", $currentchar)) { //attribute found
							$attributename = "";
							do {//get attribute name
								$attributename .= strtolower($currentchar);
								$i++;
								$currentchar = substr($code_to_clean, $i, 1);
							} while (eregi("[[:alpha:][:digit:]\:-]", $currentchar));
							
							$xhtml_code .= " ".$attributename; //get attribute name
							
							$attribdef = false;
							$attribquote = false;
							$attribvoid = false;
							
							while (eregi("[= \t\n\r\"]", $currentchar)) { //look for attribute data
								if ($currentchar == "=") {
									$attribdef = true;
								}
								if ($currentchar == "\""){
									if ($attribquote) {
										$attribvoid = true; //found void attribute
										break;
									}
									$attribquote = true; //attribute start with quotes
								}
								$i++;
								$currentchar = substr($code_to_clean, $i, 1);
							}
							if (!$attribdef) {
								//fix attribute minimization
								$attribkey = $this->html_attributes_obj->getKey("name", $attributename);
								if (($attribkey !== false) AND ($this->html_attributes[$attribkey]["type"] == "1")) {
									$xhtml_code .= "=\"".$attributename."\"";
								}
								else {
									$xhtml_code .= "=\"\"";
								}
							}
							elseif ($attribvoid) {
								$xhtml_code .= "=\"\"";
							}
							else { //get attribute data
								$attributedata = "";
								if ($attribquote) {
									while ($currentchar != "\"") { //look for attribute data
										$attributedata .= $currentchar;
										$i++;
										$currentchar = substr($code_to_clean, $i, 1);
									}
									$xhtml_code .= "=\"".$attributedata."\"";
								}
								else {
									while (eregi("[^> \t\n\r]", $currentchar) OR ($subtag) ) { //look for attribute data
										if ($currentchar == "<") {
											$subtag = true; //we are inside a subtag
										}
										$attributedata .= $currentchar;
										$i++;
										$currentchar = substr($code_to_clean, $i, 1);
										if ( ($currentchar == ">") AND ($subtag) ) {
											$subtag = false; //the subtag is ended
											$attributedata .= $currentchar;
										}
									}
									$xhtml_code .= "=\"".$attributedata."\"";
								}
							} //end get attribute data
						} //end attribute found
						
						if ($currentchar != ">") {
							do { //eleminate spaces, tabs, newlines
								$i++;
								$currentchar = substr($code_to_clean, $i, 1);
							} while (eregi("[ \t\n\r]", $currentchar));
						}
					} // end if not subtag
					else { //we are inside a subtag
						$xhtml_code .= $currentchar;
						$i++;
						$currentchar = substr($code_to_clean, $i, 1);
					}
					
					if ($currentchar == "<") {
						$subtag = true; //we are inside a subtag
					}
					elseif ( ($currentchar == ">") AND ($subtag) ) {
						$subtag = false; //the subtag is ended
						$xhtml_code .= $currentchar;
						$i++;
						$currentchar = substr($code_to_clean, $i, 1);
					}
					
				} // END check tag attributes
				
				if ((substr($code_to_clean, $i-2, 2) == " /") OR (substr($code_to_clean, $i-1, 1) == "/") OR $emptyelement) {
					$xhtml_code .= " />"; // get close empty element
					unset($tag_list[$j]); //remove tag from list
					$j--;
				}
				else{
					$xhtml_code .= ">"; // get open tag
				}
			}// END OPEN TAG FOUND =======================================
			// CLOSE TAG FOUND =======================================
			elseif ( ($currentchar == "<") AND (substr($code_to_clean, $i+1, 1) == "/" ) ) { //we are inside a close tag
				$checkindent = true;
				$closetag = ""; 
				$i += 2;
				$currentchar = substr($code_to_clean, $i, 1);
				
				//get tag name
				do {
					$closetag .= strtolower($currentchar);
					$i++;
					$currentchar = substr($code_to_clean, $i, 1);
				} while (eregi("[[:alpha:][:digit:]]", $currentchar));
				
				//remove white spaces
				while (eregi("[[:space:]]", $currentchar)) {
					$i++;
					$currentchar = substr($code_to_clean, $i, 1);
				}
				
				//clean tabs and newlines from the end of the code
				if (strlen($xhtml_code)) {
					while (eregi("[\t\n]", substr($xhtml_code, strlen($xhtml_code)-1, 1)) ) {
						$xhtml_code = substr($xhtml_code, 0, strlen($xhtml_code)-1);
					}
				}
				
				//check tag nesting
				if ($j >= 0) { //check if the open tag list is not empty
					$n = $j+1;
					$closetagcode = "";
					do { //check if tag is nested correctly
						$n--;
						if ($n >= 0) {
							if ($indent) { //add indentation
								//make indentation (before closing tag)
								$closetagcode .= "\n"; //put tag in a new line
								for ($k=0; $k<$n; $k++) {
									$closetagcode .= "\t"; //indent code
								}
							}
							$closetagcode .= "</".$tag_list[$n].">"; // add close tag
						}
					} while (($n >= 0) AND ($closetag != $tag_list[$n]));
					if (($n >= 0) AND ($closetag == $tag_list[$n])) {
						$xhtml_code .= $closetagcode;
						for ($k=$j; $k<$n; $k--) {
							unset($tag_list[$j]); //remove tag from list
						}
						$j = $n-1; //remove closed tags from list
					}
				}
			}// END CLOSE TAG FOUND =======================================
			//NOT TAG DATA FOUND =======================================
			else {
				if ($checkindent AND ( isset($tag_list[$j]) AND ($tag_list[$j] != "pre"))) { //make indentation (before non tag data)
					//ignore tabs and newlines
					if (eregi("[\t\n]", $currentchar)) {
						while (eregi("[\t\n]", $currentchar)) {
							$i++;
							$currentchar = substr($code_to_clean, $i, 1);
						}
						$i--;
						$currentchar = "";
					}
					
					if ($indent) { //add indentation
						$xhtml_code .= "\n"; //put data in a new line
						for ($k=0; $k<$j; $k++) {
							$xhtml_code .= "\t"; //indent code
						}
					}
					$checkindent = false;
				}
				
				//check for other kind of tags
				if ($currentchar == "<") { //special char found
					// check if we are inside a tag of a different language (php, asp, jsp...)
					$closechar = substr($code_to_clean, $i+1, 1); //character that identify the tag (%,?,#)
					if ( ($closechar == "%" ) OR ($closechar == "?" ) OR ($closechar == "#" ) ) { //we are inside a subtag
						while (substr($code_to_clean, $i, 2) != "".$closechar.">" ) {
							$xhtml_code .= $currentchar;
							$i++;
							$currentchar = substr($code_to_clean, $i, 1);
						}
						$i++;
						$xhtml_code .= "".$closechar.">";
					}
					elseif (substr($code_to_clean, $i+1, 3) == "!--") { //we are inside an HTML comment
						while (substr($code_to_clean, $i, 3) != "-->") {
							$xhtml_code .= $currentchar;
							$i++;
							$currentchar = substr($code_to_clean, $i, 1);
						}
						$i +=2;
						$xhtml_code .= "-->";
					}
					elseif (substr($code_to_clean, $i+1, 1) == "!") { //we are inside a DOCTYPE DTD or something like this
						while (substr($code_to_clean, $i, 1) != ">" ) {
							$xhtml_code .= $currentchar;
							$i++;
							$currentchar = substr($code_to_clean, $i, 1);
						}
						$xhtml_code .= ">";
					}
					else {
						$xhtml_code .= "&lt;";
					}
				}
				else {
					if (strlen($code_to_clean) >= ($i + 4)) {
						$temp_entity = substr($code_to_clean, $i, 4);
						if (($currentchar == "&") AND (($temp_entity == "&lt;") OR ($temp_entity == "&gt;") OR (substr($code_to_clean, $i, 2) == "&#"))) {
							$xhtml_code .= $currentchar;
						}
						else {
							//convert character entity in HTML equivalent
							$xhtml_code .= htmlentities($currentchar, ENT_NOQUOTES, $doc_charset);
						}
					}
					else {
						//convert character entity in HTML equivalent
						$xhtml_code .= htmlentities($currentchar, ENT_NOQUOTES, $doc_charset);
					}
				}
			}//END NOT TAG DATA FOUND =======================================
			
			$i++;
		} //end while (html parsing)
		
		//close unclosed tags
		if ($j >= 0) { 
			for ($n=$j; $n>=0; $n--) {
				$closetagcode = "";
				if ($indent) { //add indentation
					$closetagcode .= "\n"; //put tag in a new line
					for ($k=0; $k<$n; $k++) {
						$closetagcode .= "\t"; //indent code
					}
				}
				$closetagcode .= "</".$tag_list[$n].">";
				$xhtml_code .= $closetagcode;
			}
		}
		
		$xhtml_code = str_replace("//<![CDATA[", "\n//<![CDATA[", $xhtml_code); //fix script comment newline
		$xhtml_code = str_replace("</script>", "\n</script>", $xhtml_code); //fix script comment newline
		
		$xhtml_code = preg_replace("/^[\n]/", "", $xhtml_code); //eliminate newlines from the beginning of document
		
		if ($entities_off) {
			// remove htmlentities
			$xhtml_code = $this->unhtmlentities($xhtml_code, true);
		}
		elseif ($encoding == "UTF-8") {
			$xhtml_code = preg_replace_callback('/\&\#([0-9]+)\;/m', create_function('$matches', 'return XHTMLTranscoder::unicodeToChars($matches[1]);'), $xhtml_code);
		}
		
		return ($xhtml_code);
	}
	
	/**
	 * Reverse function for htmlentities.
	 * Convert entities in UTF-8.
	 *
	 * @param $text_to_convert Text to convert.
	 * @param $preserve_tagsign If true preserve the characters: '<' and '>'(default = true).
	 * @return string converted in UTF-8
	 */
	public function unhtmlentities($text_to_convert, $preserve_tagsign = true) {
		$trans_tbl = get_html_translation_table(HTML_ENTITIES);
		$trans_tbl = array_flip($trans_tbl);
		if ($preserve_tagsign) {
			$trans_tbl['&lt;']="&lt;"; //do not convert '<' equivalent
			$trans_tbl['&gt;']="&gt;"; //do not convert '>' equivalent
		}
		$return_text = strtr($text_to_convert, $trans_tbl);
		$return_text = preg_replace_callback('/\&\#([0-9]+)\;/m', create_function('$matches', 'return XHTMLTranscoder::unicodeToChars($matches[1]);'), $return_text);
		$return_text = preg_replace_callback('/\&\#x([0-9A-Fa-f]+)\;/m', create_function('$matches', 'return XHTMLTranscoder::unicodeToChars(intval($matches[1], 16));'), $return_text);
		return $return_text;
	}
		
	/**
	 * Convert unicode number to string.
	 * This is a callback function used by preg_replace_callback.
	 * Based on: http://www.faqs.org/rfcs/rfc3629.html
	 * <pre>
	 * 	  Char. number range  |        UTF-8 octet sequence
	 *       (hexadecimal)    |              (binary)
	 *    --------------------+-----------------------------------------------
	 *    0000 0000-0000 007F | 0xxxxxxx
	 *    0000 0080-0000 07FF | 110xxxxx 10xxxxxx
	 *    0000 0800-0000 FFFF | 1110xxxx 10xxxxxx 10xxxxxx
	 *    0001 0000-0010 FFFF | 11110xxx 10xxxxxx 10xxxxxx 10xxxxxx
	 *    ---------------------------------------------------------------------
	 *
	 *   ABFN notation:
	 *   ---------------------------------------------------------------------
	 *   UTF8-octets = *( UTF8-char )
	 *   UTF8-char   = UTF8-1 / UTF8-2 / UTF8-3 / UTF8-4
	 *   UTF8-1      = %x00-7F
	 *   UTF8-2      = %xC2-DF UTF8-tail
	 *
	 *   UTF8-3      = %xE0 %xA0-BF UTF8-tail / %xE1-EC 2( UTF8-tail ) /
	 *                 %xED %x80-9F UTF8-tail / %xEE-EF 2( UTF8-tail )
	 *   UTF8-4      = %xF0 %x90-BF 2( UTF8-tail ) / %xF1-F3 3( UTF8-tail ) /
	 *                 %xF4 %x80-8F 2( UTF8-tail )
	 *   UTF8-tail   = %x80-BF
	 *   ---------------------------------------------------------------------
	 * </pre>
	 * @param $matches array containing number to convert at position 0.
	 * @return string to replace.
	 */
	public static function unicodeToChars($char) {
		$str = ""; //return string
		
		if ($char <= 0x7F) { // one byte
			$str .= chr($char); // use the character "as is" because is ASCII
		} elseif ($char <= 0x07FF) { // two bytes
			$str .= chr(0xC0 + ($char >> 0x06));
			$str .= chr(0x80 + ($char & 0x3F));
		} elseif ($char <= 0xFFFF) { // three bytes
			$str .= chr(0xE0 + ($char >> 0x0C));
			$str .= chr(0x80 + (($char >> 0x06) & 0x3F));
			$str .= chr(0x80 + ($char & 0x3F));
		} elseif ($char <= 0x10FFFF) { // four bytes
			$str .= chr(0xF0 + ($char >> 0x12));
			$str .= chr(0x80 + (($char >> 0x0C) & 0x3F));
			$str .= chr(0x80 + (($char >> 0x06) & 0x3F));
			$str .= chr(0x80 + ($char & 0x3F));
		} else {
			// do not replace invadid sequences
			$str .= "&#".$char.";";
		}
		return $str;
	}
             
	/**
	 * Converts extended UTF-8 characters to &#number; notation.<br>
	 * Invalid byte sequences will be replaced with 0xFFFD (replacement character)<br>
	 * Based on: http://www.faqs.org/rfcs/rfc3629.html
	 * <pre>
	 * 	  Char. number range  |        UTF-8 octet sequence
	 *       (hexadecimal)    |              (binary)
	 *    --------------------+-----------------------------------------------
	 *    0000 0000-0000 007F | 0xxxxxxx
	 *    0000 0080-0000 07FF | 110xxxxx 10xxxxxx
	 *    0000 0800-0000 FFFF | 1110xxxx 10xxxxxx 10xxxxxx
	 *    0001 0000-0010 FFFF | 11110xxx 10xxxxxx 10xxxxxx 10xxxxxx
	 *    ---------------------------------------------------------------------
	 *
	 *   ABFN notation:
	 *   ---------------------------------------------------------------------
	 *   UTF8-octets = *( UTF8-char )
	 *   UTF8-char   = UTF8-1 / UTF8-2 / UTF8-3 / UTF8-4
	 *   UTF8-1      = %x00-7F
	 *   UTF8-2      = %xC2-DF UTF8-tail
	 *
	 *   UTF8-3      = %xE0 %xA0-BF UTF8-tail / %xE1-EC 2( UTF8-tail ) /
	 *                 %xED %x80-9F UTF8-tail / %xEE-EF 2( UTF8-tail )
	 *   UTF8-4      = %xF0 %x90-BF 2( UTF8-tail ) / %xF1-F3 3( UTF8-tail ) /
	 *                 %xF4 %x80-8F 2( UTF8-tail )
	 *   UTF8-tail   = %x80-BF
	 *   ---------------------------------------------------------------------
	 * </pre>
	 * @param string $str string to process.
	 * @return String
	 */
	public static function htmlentitiesUTF8($str) {
		$bytes  = array(); // array containing single character byte sequences
		$numbytes  = 1; // number of octetc needed to represent the UTF-8 character
		
		$str .= ""; // force $str to be a string
		$newstr = ""; // string to be returned
		
		$length = strlen($str); //number of bytes in string
		
		for($i = 0; $i < $length; $i++) {
			$char = ord($str{$i}); // get one string byte at time
			if(count($bytes) == 0) { // get starting octect
				if ($char <= 0x7F) {
					$newstr .= chr($char); // use the character "as is" because is ASCII
				} elseif (($char >> 0x05) == 0x06) { // 2 bytes character (0x06 = 110 BIN)
					$bytes[] = ($char - 0xC0) << 0x06; 
					$numbytes = 2;
				} elseif (($char >> 0x04) == 0x0E) { // 3 bytes character (0x0E = 1110 BIN)
					$bytes[] = ($char - 0xE0) << 0x0C; 
					$numbytes = 3;
				} elseif (($char >> 0x03) == 0x1E) { // 4 bytes character (0x1E = 11110 BIN)
					$bytes[] = ($char - 0xF0) << 0x12; 
					$numbytes = 4;
				} else {
					// use replacement character for other invalid sequences
					$newstr .= "&#xFFFD;";
					$bytes = array();
					$numbytes = 1;
				}
			} elseif (($char >> 0x06) == 0x02) { // bytes 2, 3 and 4 must start with 0x02 = 10 BIN
				$bytes[] = $char - 0x80;
				if (count($bytes) == $numbytes) {
					// compose UTF-8 bytes to a single unicode value
					$char = $bytes[0];
					for($j = 1; $j < $numbytes; $j++) {
						$char += ($bytes[$j] << (($numbytes - $j - 1) * 0x06));
					}
					if ((($char >= 0xD800) AND ($char <= 0xDFFF)) OR ($char >= 0x10FFFF)) {
						/* The definition of UTF-8 prohibits encoding character numbers between
						U+D800 and U+DFFF, which are reserved for use with the UTF-16
						encoding form (as surrogate pairs) and do not directly represent
						characters. */
						$newstr .= "&#xFFFD;"; // use replacement character
					}
					else {
						$newstr .= "&#".$char.";"; // add char to array
					}
					// reset data for next char
					$bytes = array(); 
					$numbytes = 1;
				}
			} else {
				// use replacement character for other invalid sequences
				$newstr .= "&#xFFFD;";
				$bytes = array();
				$numbytes = 1;
			}
		}
		return $newstr;
	}
	
} // END OF CLASS

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
