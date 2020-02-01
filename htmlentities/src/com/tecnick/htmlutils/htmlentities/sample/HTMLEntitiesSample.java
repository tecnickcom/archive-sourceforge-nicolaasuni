package com.tecnick.htmlutils.htmlentities.sample;

import com.tecnick.htmlutils.htmlentities.HTMLEntities;

/**
 * Implementation example of HTMLEntities class.<br/><br/>
 * Copyright (c) 2004-2005 Tecnick.com S.r.l (www.tecnick.com) Via Ugo Foscolo
 * n.19 - 09045 Quartu Sant'Elena (CA) - ITALY - www.tecnick.com -
 * info@tecnick.com<br/>
 * License: http://www.gnu.org/copyleft/lesser.html LGPL
 * @author Nicola Asuni [www.tecnick.com].
 * @version 1.0.004
 */
public class HTMLEntitiesSample {
	
	protected static String rawstr= new String("<b>àèìòù€€€<b>");
	protected static String htmlstr= new String("<b>&agrave;&egrave;&igrave;&ograve;&ugrave;&euro;&#8364;&#x20AC;<b>");
	
	/**
	 * Prints 2 strings on System.out
	 * @param args String[]
	 */
	public static void main(String[] args) {
		System.out.println("original string 1: " + rawstr);
		System.out.println("htmlentities string 1: " + HTMLEntities.htmlentities(rawstr));
		System.out.println("original string 2: " + htmlstr);
		System.out.println("unhtmlentities string 2: " + HTMLEntities.unhtmlentities(htmlstr));
		
		System.out.println("htmlentities mixed: & &amp; € = " + HTMLEntities.htmlentities("& &amp; €"));
		System.out.println("unhtmlentities mixed: & &amp; &amp; = " + HTMLEntities.unhtmlentities("& &amp; &amp;"));
		System.out.println("htmlSingleQuotes: ' = " + HTMLEntities.htmlSingleQuotes("'"));
		System.out.println("unhtmlSingleQuotes: &rsquo; = " + HTMLEntities.unhtmlSingleQuotes("&rsquo;"));
		System.out.println("htmlDoubleQuotes: \" = " + HTMLEntities.htmlDoubleQuotes("\""));
		System.out.println("unhtmlDoubleQuotes: &quot; = " + HTMLEntities.unhtmlDoubleQuotes("&quot;"));
		System.out.println("htmlQuotes: '\" = " + HTMLEntities.htmlQuotes("'\""));
		System.out.println("unhtmlQuotes: &rsquo;&quot; = " + HTMLEntities.unhtmlQuotes("&rsquo;&quot;"));
		System.out.println("htmlAngleBrackets: <> = " + HTMLEntities.htmlAngleBrackets("<>"));
		System.out.println("unhtmlAngleBrackets: &lt;&gt; = " + HTMLEntities.unhtmlAngleBrackets("&lt;&gt;"));
		System.out.println("htmlAmpersand: & = " + HTMLEntities.htmlAmpersand("&"));
		System.out.println("unhtmlAmpersand: &amp; = " + HTMLEntities.unhtmlAmpersand("&amp;"));
	}	  
}
