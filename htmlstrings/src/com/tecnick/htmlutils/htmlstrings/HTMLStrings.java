package com.tecnick.htmlutils.htmlstrings;

import java.nio.ByteBuffer;
import java.nio.CharBuffer;
import java.nio.charset.Charset;
import java.nio.charset.CharsetDecoder;
import java.nio.charset.CharsetEncoder;

/**
 * Collection of static utility methods to manipulate HTML strings.<br/><br/>
 * Copyright (c) 2004-2006 Tecnick.com S.r.l (www.tecnick.com) Via Ugo Foscolo
 * n.19 - 09045 Quartu Sant'Elena (CA) - ITALY - www.tecnick.com -
 * info@tecnick.com <br/>
 * Project homepage: <a href="http://htmlstrings.sourceforge.net" target="_blank">http://htmlstrings.sourceforge.net</a><br/>
 * License: http://www.gnu.org/copyleft/lesser.html LGPL
 * 
 * @author Nicola Asuni [www.tecnick.com].
 * @version 1.0.004
 */
public class HTMLStrings {
	
	/**
	 * Void Constructor.
	 */
	public HTMLStrings() {
	}
	
	/**
	 * Converts byte-oriented character set such as ISO-8859-1 to UTF-8 Unicode.
	 * 
	 * @param source String source string to convert
	 * @param encoding String input encoding (name of a supported charset)
	 * @return String converted string, or original string in case of error
	 */
	public static String charsetToUnicode(String source, String encoding) {
		String str = source;
		if (encoding.equalsIgnoreCase("UTF-8")) {
			return str;
		}
		try {
			Charset charset = Charset.forName(encoding);
			CharsetDecoder decoder = charset.newDecoder();
			CharsetEncoder encoder = charset.newEncoder();
			// Convert a string to bytes in a ByteBuffer
			ByteBuffer bbuf = encoder.encode(CharBuffer.wrap(source));
			// Convert bytes in a ByteBuffer to a character ByteBuffer and then to a string.
			CharBuffer cbuf = decoder.decode(bbuf);
			str = cbuf.toString();
		} catch (Exception e) {
			System.err.println(e);
		}
		return str;
	}
	
	/**
	 * Converts UTF-8 Unicode strings to byte-oriented character set such as ISO-8859-1.
	 * 
	 * @param source String source string to convert
	 * @param encoding String output encoding (name of a supported charset)
	 * @return String converted string, or original string in case of error
	 */
	public static String unicodeToCharset(String source, String encoding) {
		String str = source;
		if (encoding.equalsIgnoreCase("UTF-8")) {
			return str;
		}
		try {
			Charset charset = Charset.forName(encoding);
			CharsetEncoder encoder = charset.newEncoder();
			// encodes Unicode characters into bytes in this charset
			ByteBuffer bbuf = encoder.encode(CharBuffer.wrap(source));
			str = new String(bbuf.array());
		} catch (Exception e) {
			System.err.println(e);
		}
		return str;
	}
	
	/**
	 * Convert string to the requested encoding.
	 * 
	 * @param source String HTML source code to convert
	 * @param encoding_in String input encoding (name of a supported charset)
	 * @param encoding_out String output encoding (name of a supported charset)
	 * @return String converted string, or original string in case of error
	 */
	public static String getEncodedString(String source, String encoding_in, String encoding_out) {
		String str = source;
		str = charsetToUnicode(str, encoding_in);
		str = unicodeToCharset(str, encoding_out);
		return str;
	}
	
	/**
	 * Replace the following characters sequences with a blank space:<ul>
	 * <li>"\t" (ASCII 9 (0x09)), a tab</li>
	 * <li>"\n" (ASCII 10 (0x0A)), a new line (line feed)</li>
	 * <li>"\r" (ASCII 13 (0x0D)), a carriage return</li>
	 * <li>"\0" (ASCII 0 (0x00)), the NUL-byte</li>
	 * <li>"\f" (\u000C'), a form feed.</li>
	 * </ul>
	 * 
	 * @param str the input string
	 * @return compacted string
	 */
	public static String compactString(String str) {
		return str.replaceAll("[\t\n\r\0\f ]+", " ");
	}
	
	/**
	 * Replace newlines characters sequences with &lt;br/&gt; element.
	 * 
	 * @param str String text to change
	 * @return String original string with replaced newlines
	 */
	public static String autoBR(String str) {
		return str.replaceAll("[\n\r]+", "<br/>\n");
	}
	
}