package com.tecnick.htmlutils.htmlurls;

import java.net.MalformedURLException;
import java.net.URL;

/**
 * Collection of static utility methods to manipulate URLs addresses on HTML
 * documents.<br/><br/>
 * Copyright (c) 2004-2005 Tecnick.com S.r.l
 * (www.tecnick.com) Via Ugo Foscolo n.19 - 09045 Quartu Sant'Elena (CA) - ITALY -
 * www.tecnick.com - info@tecnick.com <br/>
 * Project homepage: <a href="http://htmlurls.sourceforge.net" target="_blank">http://htmlurls.sourceforge.net</a><br/>
 * License: http://www.gnu.org/copyleft/lesser.html LGPL
 * 
 * @author Nicola Asuni [www.tecnick.com].
 * @version 1.0.004
 */
public class HTMLURLs {
	
	/**
	 * Void Constructor.
	 */
	public HTMLURLs() {
	}
	
	/**
	 * Check if the specified link is absolute or relative.
	 * 
	 * @param linktocheck link to check
	 * @return true if the link is relative, false otherwise
	 */
	public static boolean isRelativeLink(String linktocheck) {
		try {
			if ((linktocheck.substring(0, 5).compareToIgnoreCase("http:") == 0)
					|| (linktocheck.substring(0, 6).compareToIgnoreCase("https:") == 0)
					|| (linktocheck.substring(0, 4).compareToIgnoreCase("ftp:") == 0)
					|| (linktocheck.substring(0, 4).compareToIgnoreCase("udp:") == 0)
					|| (linktocheck.substring(0, 4).compareToIgnoreCase("ssl:") == 0)
					|| (linktocheck.substring(0, 4).compareToIgnoreCase("tls:") == 0)) {
				return (false);
			}
		} catch (Exception e) {
		}
		return (true);
	}
	
	/**
	 * Resolve combined relative links (e.g.: "/dir/subdir/../image.gif" became "/dir/image.gif")
	 * 
	 * @param urllink link to resolve
	 * @return resolved URL
	 */
	public static String resolveRelativeURL(String urllink) {
		String[] patharray = urllink.split("/");
		String path = "";
		int remdir = 0; // directories to remove
		int count = patharray.length - 1;
		for (int i = count; i >= 0; i--) {
			if (!((patharray[i].compareTo(".") == 0) || (patharray[i].compareTo("..") == 0))) {
				if (remdir == 0) {
					path = patharray[i] + "/" + path;
				}
				else {
					remdir--;
					if (remdir < 0) {
						remdir = 0;
					}
				}
			}
			if (patharray[i].compareTo("..") == 0) {
				remdir++;
			}
		}
		//Trim trailing slash
		if (path.length() > 0) {
			path = path.substring(0, path.length() - 1);
		}
		return path;
	}
	
	/**
	 * Creates a URL object from the String representation.
	 * 
	 * @param link link
	 * @return canonical URL from a relative specification or null in case of error
	 */
	public static URL setURL(String link) {
		URL url = null;
		try {
			url = new URL(link);
		} catch (MalformedURLException murle) {
			System.err.println("MalformedURLException: " + murle.getMessage());
		}
		return url;
	}
	
	/**
	 * Creates a URL object from the String representation.
	 * 
	 * @param document_base base document path (e.g.: http://www.yoursite.com:8080)
	 * @param link link
	 * @return canonical URL from a relative specification or null in case of error
	 */
	public static URL setURL(String document_base, String link) {
		URL url = null;
		URL docbase = setURL(document_base);
		try {
			url = new URL(docbase, link);
		} catch (MalformedURLException murle) {
			System.err.println("MalformedURLException: " + murle.getMessage());
		}
		return url;
	}
}