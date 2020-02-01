package com.tecnick.jxhtmledit;

import java.applet.Applet;
import java.awt.BorderLayout;
import java.awt.Color;
import java.awt.Dimension;
import java.awt.FlowLayout;
import java.awt.GridLayout;
import java.awt.Insets;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.event.KeyAdapter;
import java.awt.event.KeyEvent;
import java.io.CharArrayWriter;
import java.io.InputStream;
import java.io.UnsupportedEncodingException;
import java.net.URL;
import java.net.URLEncoder;
import java.util.ArrayList;
import java.util.Enumeration;
import java.util.Stack;
import java.util.Vector;

import javax.swing.AbstractAction;
import javax.swing.Action;
import javax.swing.BoxLayout;
import javax.swing.ImageIcon;
import javax.swing.JApplet;
import javax.swing.JButton;
import javax.swing.JCheckBox;
import javax.swing.JColorChooser;
import javax.swing.JComboBox;
import javax.swing.JComponent;
import javax.swing.JEditorPane;
import javax.swing.JLabel;
import javax.swing.JOptionPane;
import javax.swing.JPanel;
import javax.swing.JScrollPane;
import javax.swing.JTabbedPane;
import javax.swing.JTextArea;
import javax.swing.JTextField;
import javax.swing.JToolBar;
import javax.swing.KeyStroke;
import javax.swing.SingleSelectionModel;
import javax.swing.UIManager;
import javax.swing.event.CaretEvent;
import javax.swing.event.CaretListener;
import javax.swing.event.ChangeEvent;
import javax.swing.event.ChangeListener;
import javax.swing.text.DefaultEditorKit;
import javax.swing.text.Document;
import javax.swing.text.EditorKit;
import javax.swing.text.html.HTMLDocument;
import javax.swing.text.html.HTMLEditorKit;
import javax.swing.text.html.StyleSheet;

import netscape.javascript.JSException;
import netscape.javascript.JSObject;

import com.tecnick.htmlutils.htmlcolors.HTMLColors;
import com.tecnick.htmlutils.htmlentities.HTMLEntities;
import com.tecnick.htmlutils.htmlstrings.HTMLStrings;
import com.tecnick.htmlutils.htmlurls.HTMLURLs;

/**
 * WYSIWYG XHTML Editor <br/>
 * Java Applet to edit XHTML in WYSIWYG mode. 
 *
 * <br/>Copyright (c) 2002-2006 Tecnick.com S.r.l (www.tecnick.com) Via Ugo
 * Foscolo n.19 - 09045 Quartu Sant'Elena (CA) - ITALY - www.tecnick.com -
 * info@tecnick.com <br/>
 * Project homepage: <a href="http://jxhtmledit.sourceforge.net" target="_blank">http://jxhtmledit.sourceforge.net</a><br/>
 * License: http://www.gnu.org/copyleft/gpl.html GPL 2
 * 
 * @author Nicola Asuni [www.tecnick.com].
 * @version 4.0.005
 */
public class JXHTMLedit extends JApplet {
	
	/**
	 * serialVersionUID
	 */
	private static final long serialVersionUID = -5517820545596597533L;
	
	/**
	 * Software Version
	 */
	private String jxhtmledit_version = "4.0.005";
	
	/**
	 * Configuration data.
	 */
	private JXHTMLConfig configuration = null;
	
	/** This is an applet */
	boolean isStandalone = false;
	
	/** "this" applet */
	Applet this_applet = this; // only final variables can be used in inner
	
	/** max undo operation size */
	private static final int undo_size = 20;
	
	/** Undo stack */
	private Stack undo_stack = new Stack();
	
	/** Redo stack */
	private Stack redo_stack = new Stack();
	
	// anonymous classes
	
	/** path to jar file */
	private String jarpath = "";
	
	/** WYSIWYG editor pane */
	private JEditorPane editor;
	
	/** Source editor pane */
	private JEditorPane editsource;
	
	/** Selected Editor */
	private JEditorPane current_editor;
	
	// applet parameters <param>:
	
	/** Applet parameter (param_encoding): charset encoding */
	private String encoding_out = "UTF-8";
	
	/** Applet parameter (param_page_encoding): input page charset encoding */
	private String encoding_in = "UTF-8";
	
	/**
	 * ISO 639 language identifier (a two- or three-letter code) used on TMX files
	 */
	private String param_lang = "en";
	
	/** Applet parameter (param_callingform): name of the calling html form */
	private String param_callingform;
	
	/**
	 * Applet parameter (param_callingfield): name of the calling html textarea form field
	 */
	private String param_callingfield;
	
	/**
	 * Applet parameter (param_stylesheet): name of the default CSS stylesheet file
	 */
	private String param_stylesheet;
	
	/** Applet parameter (param_hide_source): if true hide source edit panel */
	private boolean param_hide_source = false;
	
	/**
	 * Applet parameter (param_xhtml): if true export (save) as XHTML instead of HTML
	 */
	private boolean param_xhtml = true;
	
	/** Applet parameter (param_indent): when true indent XHTML output code */
	private boolean param_indent = false;
	
	/**
	 * Applet parameter (param_entities_off): when true convert htmlentities to extended chars
	 */
	private boolean param_entities_off = false;
	
	/**
	 * Applet parameter (param_buttons_images_path): path to buttons images directory (relative to document base)
	 */
	private String buttons_images_path;
	
	/**
	 * Applet parameter (param_config_files_path): path configuration files directory (relative to document base)
	 */
	private String config_files_path;
	
	/** Applet parameter (param_images_path): absolute URI of images directory */
	private String images_path;
	
	/**
	 * Applet parameter (param_images_list): list of images in images_path directory separted by ":"
	 */
	private String images_list;
	
	/**
	 * Applet parameter (param_separate_window): true if applet is loaded in a separate browser window respect one
	 * containing the calling form field
	 */
	private boolean param_separate_window = true;
	
	/** HTML source code to edit */
	String current_html_code = "";
	
	/** undo action */
	private UndoAction undoAction = new UndoAction();
	
	/** redo action */
	private RedoAction redoAction = new RedoAction();
	
	/** Set current edit mode: WYSIWYG/source */
	private boolean source_mode = false;
	
	/** the following will contain the string attribute to attach on tag */
	private String[] attribute_string_array;
	
	/**
	 * remember auto <br>
	 * settings for editor
	 */
	private boolean br_state_editor = true;
	
	/**
	 * remember auto <br>
	 * settings for source editor
	 */
	private boolean br_state_source = false;
	
	/** remember last search */
	private String text_to_search = "";
	
	/**
	 * enable auto
	 * <li></li>
	 */
	private boolean auto_li_enabled = false;
	
	/**
	 * checkbox to enable/disable the auto
	 * <li>mode
	 */
	private JCheckBox autoListItem;
	
	/**
	 * checkbox to enable/disable the auto <br>
	 * mode
	 */
	private JCheckBox autobreak;
	
	/** ActionListener array for buttons */
	private ArrayList buttonActionListener = new ArrayList();
	
	/** open manufacturer site on a new browser window */
	private ActionListener openManufacturerSite = new ActionListener() {
		public void actionPerformed(ActionEvent e) {
			this_applet.getAppletContext().showDocument(
					// load the URL on the target browser window
					HTMLURLs.setURL("http://www.tecnick.com"), "_blank");
		}
	};
	
	/**
	 * Get the applet parameters from applet call and set default values for void parameters
	 * 
	 * @param key
	 *        name of param to get
	 * @param def
	 *        default value for void param
	 * @return param value
	 */
	private String getParameter(String key, String def) {
		if (isStandalone) {
			return System.getProperty(key, def);
		} else {
			String param_value = getParameter(key);
			if ((param_value != null) && (param_value.length() > 0)) {
				return param_value;
			}
		}
		return def;
	}
	
	/**
	 * set the applet parameters (param)
	 */
	private void getParameters() {
		try {
			encoding_out = this.getParameter("param_encoding", "iso-8859-1");
			encoding_in = this.getParameter("param_page_encoding", "iso-8859-1");
			param_lang = this.getParameter("param_lang", "en");
			param_separate_window = Boolean.valueOf(this.getParameter("param_separate_window", "true")).booleanValue();
			param_callingform = this.getParameter("param_callingform", "");
			param_callingfield = this.getParameter("param_callingfield", "");
			
			param_stylesheet = this.getParameter("param_stylesheet", "");
			if (HTMLURLs.isRelativeLink(param_stylesheet)) {
				param_stylesheet = HTMLURLs.resolveRelativeURL(jarpath + this.getParameter("param_stylesheet", ""));
			}
			
			param_hide_source = Boolean.valueOf(this.getParameter("param_hide_source", "false")).booleanValue();
			param_xhtml = Boolean.valueOf(this.getParameter("param_xhtml", "true")).booleanValue();
			param_indent = Boolean.valueOf(this.getParameter("param_indent", "false")).booleanValue();
			param_entities_off = Boolean.valueOf(this.getParameter("param_entities_off", "false")).booleanValue();
			
			config_files_path = this.getParameter("param_config_files_path", "");
			if (config_files_path.length() <= 0) {
				config_files_path = "/com/tecnick/jxhtmledit/config/";
			} else if (HTMLURLs.isRelativeLink(config_files_path)) {
				config_files_path = HTMLURLs.resolveRelativeURL(jarpath
						+ this.getParameter("param_config_files_path", ""))
						+ "/";
			}
			
			buttons_images_path = this.getParameter("param_buttons_images_path", "");
			if (buttons_images_path.length() <= 0) {
				buttons_images_path = "/com/tecnick/jxhtmledit/buttons/";
			} else if (HTMLURLs.isRelativeLink(buttons_images_path)) {
				buttons_images_path = HTMLURLs.resolveRelativeURL(jarpath
						+ this.getParameter("param_buttons_images_path", ""))
						+ "/";
			}
			
			images_path = this.getParameter("param_images_path", "");
			if (HTMLURLs.isRelativeLink(images_path)) {
				images_path = HTMLURLs.resolveRelativeURL(jarpath + this.getParameter("param_images_path", "")) + "/";
			}
			
			images_list = this.getParameter("param_images_list", "");
		} catch (Exception e) {
			e.printStackTrace();
		}
	}
	
	/**
	 * Set the HTML code in the current editor pane
	 * 
	 * @param html
	 *        HTML source code to write on document
	 * @param update_source
	 *        if true, then update source editor
	 */
	private void setHTMLCode(String html, boolean update_source, boolean update_undo) {
		if (!source_mode) {
			//create temporary JEditorPane to clear current document and model
			JEditorPane new_text = new JEditorPane();
			new_text.setContentType("text/html; charset=" + encoding_out);
			new_text.setText(HTMLStrings.getEncodedString(html, encoding_in, encoding_out));
			editor.setDocument(new_text.getDocument()); //clear document
		}
		if (update_source) {
			editsource.setText(html); //update editsource to record UNDO events
		}
		current_editor.requestFocus(); //request focus for current editor
		if (update_undo) {
			// set text on undo stack
			setUndo(current_editor.getText());
		}
	}
	
	/**
	 * Convenience method to set html code from javascript.
	 * 
	 * @param code
	 */
	public void setCode(String code) {
		// convert xhtml self closing tags to standard html
		code = code.replaceAll(" />", ">");
		setHTMLCode(code, true, true);
	}
	
	/**
	 * Convenience method to get html code from javascript.
	 * 
	 * @return html code
	 */
	public String getCode() {
		String text_to_send = "";
		text_to_send = current_editor.getText();
		
		//remove some tags
		text_to_send = text_to_send.replaceAll("<html>", "");
		text_to_send = text_to_send.replaceAll("</html>", "");
		text_to_send = text_to_send.replaceAll("<head>", "");
		text_to_send = text_to_send.replaceAll("</head>", "");
		text_to_send = text_to_send.replaceAll("<body>", "");
		text_to_send = text_to_send.replaceAll("</body>", "");
		
		//remove some whitespaces
		text_to_send = text_to_send.replaceAll("[\r\n][ \t\n\f\r]+", "\r\n");
		text_to_send = text_to_send.trim();
		
		//convert HTML to XHTML
		if (param_xhtml) {
			text_to_send = HTMLEntities.unhtmlDoubleQuotes(text_to_send);
			text_to_send = configuration.getTranscoder().transcode(text_to_send, param_indent, param_entities_off, encoding_out);
		}
		return text_to_send;
	}
	
	/**
	 * Insert open and close tag around selection or at caret position
	 * 
	 * @param intag
	 *        open tag &lt;tagname&gt; or ""
	 * @param outtag
	 *        close tag &lt;/tagname&gt; or ""
	 */
	private void insertTag(String intag, String outtag) {
		String in_mark = "|$#=*";
		String out_mark = "*=#$|";
		
		try {
			boolean normal_insert = true;
			int current_position = current_editor.getCaretPosition(); //get
			// cursor
			// position
			// on text
			String selected = current_editor.getSelectedText();
			if (selected == null) {
				current_editor.select(current_position, current_position);
				selected = "";
			}
			
			int selection_start = current_editor.getSelectionStart();
			int selection_end = current_editor.getSelectionEnd();
			
			if (source_mode) {
				current_editor.replaceSelection(intag + selected + outtag);
				selection_start += intag.length();
				selection_end += intag.length();
				current_position += intag.length();
			} else { //wyiswig mode
				current_editor.select(selection_start, selection_start);
				current_editor.replaceSelection(in_mark);
				current_editor.select(selection_end + in_mark.length(), selection_end + in_mark.length()); //translate
				// end
				// points of 2 that
				// is the length of
				// "$_"
				//check if a open/close tag has been inserted without selection
				if ((selected.length() <= 0) && (intag.length() > 0) && (outtag.length() > 0)) {
					current_editor.replaceSelection("_" + out_mark); //add
					// space to
					// allow
					// selection
					// between
					// new tags
					normal_insert = false;
					selection_end++;
				} else {
					current_editor.replaceSelection(out_mark);
				}
				
				current_position++;
				
				Document doc = current_editor.getDocument();
				EditorKit ed = current_editor.getEditorKit();
				
				// get html code
				CharArrayWriter caw = new CharArrayWriter();
				ed.write(caw, doc, 0, doc.getLength());
				String html_code = caw.toString();
				
				int end_link = html_code.indexOf(out_mark);
				int start_link = html_code.indexOf(in_mark);
				html_code = html_code.substring(0, end_link) + outtag
				+ html_code.substring(end_link + out_mark.length());
				html_code = html_code.substring(0, start_link) + intag
				+ html_code.substring(start_link + in_mark.length());
				
				setHTMLCode(html_code, true, false);
			} //end wysiwyg mode
			
			if ((selected.length() > 0) && (normal_insert)) {
				// restore text selection
				current_editor.select(selection_start, selection_end);
			} else {
				// restore caret position
				current_editor.setCaretPosition(current_position);
			}
			current_editor.requestFocus(); //request focus for this editor
			setUndo(current_editor.getText()); // set undo
		} // end of try
		catch (Exception e) {
			System.out.println("insertTag Exception " + e);
		}
	}
	
	/**
	 * Remove selected or adjacent tag
	 */
	private void removeTag() {
		try {
			String selected = current_editor.getSelectedText();
			
			if (selected != null) {
				if (source_mode) { //remove adjacent tags on source view
					int doc_end = current_editor.getText().length() - 1;
					int select_start = current_editor.getSelectionStart();
					int select_end = current_editor.getSelectionEnd();
					int new_select_start = select_start - 1;
					int new_select_end = select_end + 1;
					
					//search first open tag
					while ((current_editor.getText(new_select_start, 1).compareTo("<") != 0) && (new_select_start >= 0)) {
						new_select_start--;
					}
					//search first close tag
					while ((current_editor.getText(new_select_end, 1).compareTo(">") != 0)
							&& (new_select_end <= doc_end)) {
						new_select_end++;
					}
					if ((current_editor.getText(new_select_start, 1).compareTo("<") == 0)
							&& (current_editor.getText(new_select_end, 1).compareTo(">") == 0)) {
						
						// search open tag
						int new_open_end = new_select_start + 1;
						while ((current_editor.getText(new_open_end, 1).compareTo(">") != 0)
								&& (new_open_end <= new_select_end)) {
							new_open_end++;
						}
						// search closing tag
						int new_close_start = new_select_end - 1;
						while ((current_editor.getText(new_close_start, 1).compareTo("<") != 0)
								&& (new_close_start >= new_select_start)) {
							new_close_start--;
						}
						
						if ((new_open_end < new_close_start)
								&& (current_editor.getText(new_select_start + 1, 1).compareTo(
										current_editor.getText(new_close_start + 2, 1)) == 0)) {
							// remove tags
							new_select_end++;
							current_editor.select(new_select_start, new_select_end);
							current_editor.replaceSelection(current_editor.getText(new_open_end + 1, new_close_start
									- new_open_end - 1));
							// restore selection
							current_editor.select(new_select_start, new_select_start + new_close_start - new_open_end
									- 1);
						}
					}
				} else { //remove all adjacent tags on wysiwyg mode
					current_editor.replaceSelection(selected);
				}
			}
			current_editor.requestFocus(); //request focus for this editor
			setUndo(current_editor.getText()); // set undo
		} // end of try
		catch (Exception e) {
			System.out.println("removeTag Exception " + e);
		}
	}
	
	/**
	 * Replace HTML tag on selection
	 * 
	 * @param search_tag
	 *        tag to replace
	 * @param replace_tag
	 *        new tag
	 */
	private void replaceTag(String search_tag, String replace_tag) {
		String selected = current_editor.getSelectedText();
		if (selected != null) { //works only if text has been selected
			int selection_start = current_editor.getSelectionStart();
			int selection_end = current_editor.getSelectionEnd();
			
			if (source_mode) {
				String new_text = selected.replaceAll(search_tag, replace_tag); //replace
				// tag
				current_editor.replaceSelection(new_text);
				int replace_diff = new_text.length() - selected.length();
				selection_end += replace_diff;
			} else { //visual mode
				try {
					//mark start and end of selection
					current_editor.select(selection_start, selection_start);
					current_editor.replaceSelection("#_");
					current_editor.select(selection_end + 3, selection_end + 3); //translate
					// end
					// points
					// of 2
					// that
					// is
					// the
					// length
					// of
					// "$_"
					current_editor.replaceSelection("_#");
					
					Document doc = current_editor.getDocument();
					EditorKit ed = current_editor.getEditorKit();
					
					// get html code
					CharArrayWriter caw = new CharArrayWriter();
					ed.write(caw, doc, 0, doc.getLength());
					String html_code = caw.toString();
					
					int start_link = html_code.indexOf("#_");
					int end_link = html_code.indexOf("_#");
					
					String[] new_text_array = html_code.split("(#_)|(_#)");
					String new_text = new_text_array[1].replaceAll(search_tag, replace_tag); //replace tag
					
					html_code = html_code.substring(0, start_link) + new_text + html_code.substring(end_link + 2);
					setHTMLCode(html_code, true, false);
					
				} catch (Exception e) {
					System.out.println("removeTag Exception " + e);
				}
				
			}
			current_editor.select(selection_start, selection_end); //restore
			// text
			// selection
			current_editor.requestFocus(); //request focus for this editor
			setUndo(current_editor.getText()); // set undo
		}
	}
	
	/**
	 * Class to store caret position of table elements
	 */
	class tableIndex {
		public int table_start;
		
		public int table_end;
		
		public Integer[] row_start;
		
		public Integer[] row_end;
		
		public Integer[][] cell_start;
		
		public Integer[][] cell_end;
		
		public Integer[][] cell_content_start;
		
		public int current_row;
		
		public int current_col;
		
		public int rows;
		
		public int cols;
		
		//initialize arrays
		public tableIndex() {
		}
	}
	
	/**
	 * Create table index (find caret start positions of each table element)
	 * 
	 * @return table data
	 */
	private tableIndex createTableIndex() {
		tableIndex table_data = new tableIndex();
		
		try {
			String original_html_code = current_editor.getText(); //remember
			// original
			// code
			
			int start_point = current_editor.getCaretPosition(); //get cursor
			// position on
			// text
			current_editor.select(start_point, start_point); //select at caret
			current_editor.replaceSelection("_#_"); //insert mark to remember
			// current cell
			
			String html_code = current_editor.getText();
			
			start_point = html_code.indexOf("_#_");
			int text_length = html_code.length(); //text length
			
			//find table start point
			table_data.table_start = start_point;
			if (table_data.table_start - 6 >= 0) {
				while ((html_code.substring(table_data.table_start - 6, table_data.table_start).compareToIgnoreCase(
				"<table") != 0)
				&& ((table_data.table_start - 6) > 0)) {
					table_data.table_start--;
				}
				table_data.table_start -= 6;
				if (table_data.table_start <= 0) {
					//System.out.println("ERROR: unable to find table start
					// (A)");
					setHTMLCode(original_html_code, true, true); //restore html code
					return null;
				}
			} else {
				//System.out.println("ERROR: unable to find table start (B)");
				setHTMLCode(original_html_code, true, true); //restore html code
				return null;
			}
			
			//find table end point
			table_data.table_end = start_point;
			if (table_data.table_end + 8 < text_length) {
				while ((html_code.substring(table_data.table_end, table_data.table_end + 8).compareToIgnoreCase(
				"</table>") != 0)
				&& ((table_data.table_end + 8) < text_length)) {
					table_data.table_end++;
				}
				table_data.table_end += 5; //(8 - 3 = 4, where 3 is the length
				// of "_#_")
				if (table_data.table_end >= text_length) {
					//System.out.println("ERROR: unable to find table end (A)");
					setHTMLCode(original_html_code, true, true); //restore html code
					return null;
				}
			} else {
				//System.out.println("ERROR: unable to find table end (B)");
				setHTMLCode(original_html_code, true, true); //restore html code
				return null;
			}
			
			int row_index = -1;
			int col_index = -1;
			String intag = "";
			String outtag = "";
			table_data.cols = 0;
			boolean inside_cell = false;
			
			Vector row_start = new Vector(1);
			Vector row_end = new Vector(1);
			Vector cell_start = new Vector(1);
			Vector cell_start_data = new Vector(1);
			Vector cell_end = new Vector(1);
			Vector cell_end_data = new Vector(1);
			Vector cell_content_start = new Vector(1);
			Vector cell_content_start_data = new Vector(1);
			
			//map elements positions inside table
			for (int i = table_data.table_start; i < table_data.table_end; i++) {
				intag = html_code.substring(i, i + 3);
				outtag = html_code.substring(i, i + 5);
				
				if (inside_cell && (html_code.substring(i, i + 1).compareToIgnoreCase(">") == 0)) {
					cell_content_start_data.addElement(new Integer(i + 1));
					inside_cell = false;
				}
				
				//found current cell
				if (intag.compareToIgnoreCase("_#_") == 0) {
					table_data.current_row = row_index;
					table_data.current_col = col_index;
					//remove markup
					html_code = html_code.substring(0, i) + html_code.substring(i + 3);
				}
				
				//found starting row
				else if (intag.compareToIgnoreCase("<tr") == 0) {
					table_data.cols = Math.max(table_data.cols, col_index);
					col_index = -1; //reset column index
					row_index++;
					row_start.addElement(new Integer(i));
				}
				
				//found starting cell
				else if ((intag.compareToIgnoreCase("<th") == 0) || (intag.compareToIgnoreCase("<td") == 0)) {
					col_index++;
					cell_start_data.addElement(new Integer(i));
					inside_cell = true;
				}
				
				//found ending row
				if (outtag.compareToIgnoreCase("</tr>") == 0) {
					row_end.addElement(new Integer(i + 5));
					
					Integer[] temp_cell_start_array = new Integer[1];
					temp_cell_start_array = (Integer[]) cell_start_data.toArray(temp_cell_start_array);
					cell_start.addElement(temp_cell_start_array);
					cell_start_data.clear();
					
					Integer[] temp_cell_end_array = new Integer[1];
					temp_cell_end_array = (Integer[]) cell_end_data.toArray(temp_cell_end_array);
					cell_end.addElement(temp_cell_end_array);
					cell_end_data.clear();
					
					Integer[] temp_cell_content_start_array = new Integer[1];
					temp_cell_content_start_array = (Integer[]) cell_content_start_data
					.toArray(temp_cell_content_start_array);
					cell_content_start.addElement(temp_cell_content_start_array);
					cell_content_start_data.clear();
				}
				
				//found ending cell
				else if ((outtag.compareToIgnoreCase("</th>") == 0) || (outtag.compareToIgnoreCase("</td>") == 0)) {
					cell_end_data.addElement(new Integer(i + 5));
					inside_cell = false;
				}
				
			}
			table_data.rows = row_index + 1;
			table_data.cols++;
			
			//initialize arrays
			table_data.row_start = new Integer[1];
			table_data.row_end = new Integer[1];
			table_data.cell_start = new Integer[table_data.rows][table_data.cols];
			table_data.cell_end = new Integer[table_data.rows][table_data.cols];
			table_data.cell_content_start = new Integer[table_data.rows][table_data.cols];
			
			table_data.row_start = (Integer[]) row_start.toArray(table_data.row_start);
			table_data.row_end = (Integer[]) row_end.toArray(table_data.row_end);
			
			Integer[] temp_array;
			for (int r = 0; r < table_data.rows; r++) {
				temp_array = (Integer[]) cell_start.get(r);
				for (int c = 0; c < temp_array.length; c++) {
					table_data.cell_start[r][c] = temp_array[c];
				}
				temp_array = (Integer[]) cell_end.get(r);
				for (int c = 0; c < temp_array.length; c++) {
					table_data.cell_end[r][c] = temp_array[c];
				}
				temp_array = (Integer[]) cell_content_start.get(r);
				for (int c = 0; c < temp_array.length; c++) {
					table_data.cell_content_start[r][c] = temp_array[c];
				}
			}
			
			//restore html code
			setHTMLCode(html_code, true, true);
			
		} catch (Exception e) {
			System.out.println("removeTableRow Exception " + e);
		}
		return table_data;
	}
	
	/**
	 * find first occurence of specified text from current caret position
	 * 
	 * @param text_to_search
	 *        text to search
	 */
	private void searchText(String text_to_search) {
		if (text_to_search != null) {
			int len = text_to_search.length();
			if (len > 0) {
				int text_length = current_editor.getText().length();
				int current_position = current_editor.getCaretPosition(); //get
				// cursor
				// position
				// on
				// text
				current_editor.select(current_position, current_position + len);
				while ((current_editor.getSelectedText().compareToIgnoreCase(text_to_search) != 0)
						&& (current_position < text_length)) {
					current_editor.select(++current_position, current_position + len);
				}
			}
		}
		current_editor.requestFocus(); //request focus for this editor
	}
	
	/**
	 * Get HTML code from calling form
	 * 
	 * @return code from calling form
	 */
	private String getCodeOnForm() {
		String input_html_code = "";
		try {
			JSObject jsroot = JSObject.getWindow(this_applet);
			if (param_separate_window) {
				input_html_code = (String) jsroot.eval("window.opener.document." + param_callingform + "."
						+ param_callingfield + ".value;");
			} else {
				input_html_code = (String) jsroot.eval("document." + param_callingform + "." + param_callingfield
						+ ".value;");
			}
		} catch (Exception jse) {
			System.err.println("JSObject is not supported: " + jse);
		}
		// convert xhtml self closing tags to standard html
		input_html_code = input_html_code.replaceAll(" />", ">");
		return input_html_code;
	}
	
	/**
	 * Create a JButton that display JXHTMLEDIT infos and open tecnick.com site
	 * 
	 * @return JButton
	 */
	private JPanel info_panel() {
		String message = "JXHTMLEDIT " + jxhtmledit_version + "\n";
		message += "http://jxhtmledit.sourceforge.net\n";
		message += "Author: Nicola Asuni\n";
		message += "Copyright (c) 2003-2006 Tecnick.com s.r.l. - www.tecnick.com\n";
		message += "Open Source License: GPL 2\n";
		
		JTextArea msgarea = new JTextArea(message);
		msgarea.setEditable(false);
		
		JButton linkbutton = new JButton("www.tecnick.com");
		linkbutton.addActionListener(openManufacturerSite);
		
		JPanel info = new JPanel();
		info.setLayout(new BorderLayout());
		info.add(msgarea, BorderLayout.CENTER);
		info.add(linkbutton, BorderLayout.SOUTH);
		
		return info;
	}
	
	/**
	 * display this software information dialog window
	 */
	public void about_this_applet() {
		JOptionPane.showMessageDialog(this, info_panel(), "About", JOptionPane.INFORMATION_MESSAGE);
	}
	
	/**
	 * Get the ImageIcon object from file.
	 * 
	 * @param imgpath image file path (inside jar or external URL)
	 * @return ImageIcon object
	 */
	private ImageIcon getImageIcon(String imgpath) {
		ImageIcon img = null;
		try {
			InputStream in = getClass().getResourceAsStream(imgpath);
			byte buffer[];
			buffer = new byte[in.available()];
			in.read(buffer);
			img = new ImageIcon(buffer);
		} catch (Exception e1) {
			try {
				img = new ImageIcon(HTMLURLs.setURL(imgpath));
			} catch (Exception e2) {
				System.err.println(e2 + " :: " + imgpath);
			}
		}
		return img;
	}
	
	/**
	 * Initialize applet
	 */
	public void init() {
		
		//display some info on console
		System.out.println(" ");
		System.out.println("JXHTMLEDIT " + jxhtmledit_version);
		System.out.println("http://jxhtmledit.sourceforge.net");
		System.out.println("Author: Nicola Asuni");
		System.out.println("Copyright (c) 2003-2006 Tecnick.com s.r.l. - www.tecnick.com");
		System.out.println("Open Source License: GPL 2");
		System.out.println(" ");
		
		jarpath = this.getCodeBase().toString();
		
		getParameters(); //get applet parameters (menu data)
		
		//Initialize configuration files
		configuration = new JXHTMLConfig(config_files_path, param_lang);
		
		
		current_html_code = getCodeOnForm(); // get code from calling form
		
		// set cross platform look and feel
		try {
			UIManager.setLookAndFeel(UIManager.getCrossPlatformLookAndFeelClassName());
		} catch (Exception e) {
		}
		
		JToolBar buttons_panel = new JToolBar(); // toolbar for buttons
		buttons_panel.setRollover(true);
		buttons_panel.setFloatable(false);
		buttons_panel.setLayout(new FlowLayout(FlowLayout.LEFT, 0, 0));
		
		final JComboBox headingsComboBox = new JComboBox();
		final JComboBox charsComboBox = new JComboBox();
		final JComboBox allTagsComboBox = new JComboBox();
		
		// create wysiwyg editor pane
		editor = new JEditorPane();
		editor.setContentType("text/html; charset=" + encoding_out);
		current_editor = editor;
		source_mode = false;
		editor.setText(" ");
		editor.setDragEnabled(true); //enable drag
		editor.setCaretPosition(0);
		editor.requestFocus();
		
		HTMLDocument doc = (HTMLDocument) editor.getDocument();
		doc.setPreservesUnknownTags(true); // add custom tag support
		
		StyleSheet docStyleSheet = doc.getStyleSheet();
		
		URL styleurl = HTMLURLs.setURL(param_stylesheet);
		if (styleurl != null) {
			docStyleSheet.importStyleSheet(styleurl);
			HTMLEditorKit ed = (HTMLEditorKit) editor.getEditorKit();
			ed.setStyleSheet(docStyleSheet);
		}
		
		//add keyboard listener for ENTER key and auto <BR>
		editor.addKeyListener(new KeyAdapter() {
			public void keyPressed(KeyEvent e) {
				//intercept enter key and display <br> tag
				if (e.getKeyCode() == KeyEvent.VK_ENTER) {
					if (br_state_editor) {
						insertTag("<br>", "");
						e.consume(); //block key event dispatch in visual mode
					}
					if (auto_li_enabled) {
						insertTag("</li><li>", "");
						e.consume(); //block key event dispatch in visual mode
					}
				}
				if (!(((e.getKeyCode() >= KeyEvent.VK_0) && (e.getKeyCode() <= KeyEvent.VK_9)) || ((e.getKeyCode() >= KeyEvent.VK_A) && (e
						.getKeyCode() <= KeyEvent.VK_Z)))) {
					// add current text to undo stack
					setUndo(editor.getText());
				}
			}
		});
		
		// create source editor pane
		editsource = new JEditorPane();
		editsource.setContentType("text/plain");
		editsource.setText(current_html_code);
		editsource.setCaretPosition(0);
		//editsource.setDragEnabled(true); //enable drag
		
		//add keyboard listener for ENTER key and auto <BR>
		editsource.addKeyListener(new KeyAdapter() {
			public void keyPressed(KeyEvent e) {
				//intercept enter key and display <br> tag
				if (e.getKeyCode() == KeyEvent.VK_ENTER) {
					if (br_state_source) {
						insertTag("<br>", "");
					}
					if (auto_li_enabled) {
						insertTag("</li><li>", "");
					}
				}
				if (!(((e.getKeyCode() >= KeyEvent.VK_0) && (e.getKeyCode() <= KeyEvent.VK_9)) || ((e.getKeyCode() >= KeyEvent.VK_A) && (e
						.getKeyCode() <= KeyEvent.VK_Z)))) {
					// add current text to undo stack
					setUndo(editsource.getText());
				}
			}
		});
		
		setUndo(current_html_code);
		
		// === SET BUTTONS ACTIONS LISTENERS
		// ===========================================
		// -----------------------------------------------------------------------------
		
		// -----------------------------------------------------------------------------
		// display JXHTMLEDIT info dialog
		ActionListener aboutActionListener = new ActionListener() {
			public void actionPerformed(ActionEvent e) {
				about_this_applet();
			}
		};
		// -----------------------------------------------------------------------------
		//reload code from calling form
		ActionListener loadDocumentActionListener = new ActionListener() {
			public void actionPerformed(ActionEvent e) {
				setHTMLCode(getCodeOnForm(), true, true);
			}
		};
		// -----------------------------------------------------------------------------
		//clear document (delete all)
		ActionListener clearDocumentActionListener = new ActionListener() {
			public void actionPerformed(ActionEvent e) {
				setHTMLCode("<html><body></body></html>", true, true);
			}
		};
		// -----------------------------------------------------------------------------
		//paste html source code to calling textarea form
		ActionListener saveDocumentActionListener = new ActionListener() {
			public void actionPerformed(ActionEvent e) {
				try {
					JSObject jsroot = JSObject.getWindow(this_applet);
					
					String text_to_send = getCode();
					try {
						//encode text
						text_to_send = URLEncoder.encode(text_to_send, encoding_out);
					} catch (UnsupportedEncodingException uee) {
						System.out.println("UnsupportedEncodingException: " + uee);
					}
					
					//convert and remove multiple spaces
					text_to_send = text_to_send.replaceAll("[+]+", " ");
					
					//paste code
					if (param_separate_window) {
						jsroot.eval("window.opener.document." + param_callingform + "." + param_callingfield
								+ ".value=unescape('" + text_to_send + "');");
						//close applet window
						//jsroot.eval("window.close();"); //20031113 (this
						// generate a crash bug on W98)
						//this_applet.stop(); //20031113
					} else {
						jsroot.eval("document." + param_callingform + "." + param_callingfield + ".value=unescape('"
								+ text_to_send + "');");
					}
				} catch (Exception jse) {
					System.err.println("JSObject is not supported: " + jse);
				}
				
			}
		};
		// -----------------------------------------------------------------------------
		//display dialog to set font
		ActionListener fontActionListener = new ActionListener() {
			public void actionPerformed(ActionEvent e) {
				int num_attributes = 4;
				attribute_string_array = new String[num_attributes];
				
				ImageIcon icon_path = getImageIcon(buttons_images_path + "color.gif");
				
				// create attributes panel
				JPanel attributesPanel = new JPanel();
				attributesPanel.setLayout(new BoxLayout(attributesPanel, BoxLayout.Y_AXIS));
				attributesPanel.setAlignmentX(JPanel.LEFT_ALIGNMENT);
				
				//add input label
				JLabel attribute_label0 = input_label_component(configuration.getTMXresources().getString("R_FONT", "font"),
						configuration.getTMXresources().getString("R_FONT", "font"));
				attributesPanel.add(attribute_label0);
				
				JComponent this_attribute0 = attribute_input_component(3, 0, "face",
						"sans-serif:serif:monospace:cursive:fantasy", "", null, null);
				attributesPanel.add(this_attribute0);
				
				JLabel attribute_label1 = input_label_component(configuration.getTMXresources().getString("R_SIZE", "size"),
						configuration.getTMXresources().getString("R_FONT_SIZE", "font size (absolute or relative)"));
				attributesPanel.add(attribute_label1);
				
				JComponent this_attribute1 = attribute_input_component(3, 1, "size",
						"+1:+2:+3:+4:+5:+6:1:2:3:4:5:6:7:-1:-2:-3:-4:-5:-6", "", null, null);
				attributesPanel.add(this_attribute1);
				
				JLabel attribute_label2 = input_label_component(configuration.getTMXresources().getString("R_FGCOLOR",
				"foreground color"), configuration.getTMXresources().getString("R_FGCOLOR", "foreground color"));
				attributesPanel.add(attribute_label2);
				
				JComponent this_attribute2 = attribute_input_component(0, 2, "color", "", "", null, null);
				attributesPanel.add(this_attribute2);
				JComponent color_chooser2 = attribute_input_component(4, 2, "color", "", "", icon_path,
						(JTextField) this_attribute2);
				attributesPanel.add(color_chooser2);
				
				JLabel attribute_label3 = input_label_component(configuration.getTMXresources().getString("R_BGCOLOR",
				"background color"), configuration.getTMXresources().getString("R_BGCOLOR", "background color"));
				attributesPanel.add(attribute_label3);
				
				JComponent this_attribute3 = attribute_input_component2(0, 3, "color", "style=\"background-color: ",
						"\"", "", "", null, null);
				attributesPanel.add(this_attribute3);
				JComponent color_chooser3 = attribute_input_component(4, 3, "color", "", "", icon_path,
						(JTextField) this_attribute3);
				attributesPanel.add(color_chooser3);
				
				int result = JOptionPane.showConfirmDialog(this_applet, attributesPanel, configuration.getTMXresources().getString(
						"R_SET_FONT", "Font settings"), JOptionPane.OK_CANCEL_OPTION, JOptionPane.PLAIN_MESSAGE);
				
				if (result == 0) { //OK button has been selected
					//create attribute string
					String attribute_string = "";
					//for each attribute
					for (int j = 0; j < num_attributes; j++) {
						if (attribute_string_array[j] != null) {
							attribute_string += attribute_string_array[j]; //compose
							// attribute
							// string
						}
					}
					if (attribute_string.length() > 0) {
						insertTag("<font " + attribute_string + ">", "</font>");
					}
				}
			}
		};
		// -----------------------------------------------------------------------------
		//select all text
		ActionListener selectAllActionListener = new ActionListener() {
			public void actionPerformed(ActionEvent e) {
				current_editor.requestFocus();
				current_editor.select(0, current_editor.getText().length());
			}
		};
		// -----------------------------------------------------------------------------
		//display dialog to search text
		ActionListener findActionListener = new ActionListener() {
			public void actionPerformed(ActionEvent e) {
				current_editor.requestFocus();
				text_to_search = (String) JOptionPane.showInputDialog(current_editor, configuration.getTMXresources().getString(
						"R_SEARCH_TEXT", "Text to search"), configuration.getTMXresources().getString("R_FIND", "Find"),
						JOptionPane.PLAIN_MESSAGE, null, null, text_to_search);
				searchText(text_to_search);
			}
		};
		// -----------------------------------------------------------------------------
		//find next occurence of search string
		ActionListener findNextActionListener = new ActionListener() {
			public void actionPerformed(ActionEvent e) {
				current_editor.requestFocus();
				searchText(text_to_search);
			}
		};
		// -----------------------------------------------------------------------------
		//add headings selected on comboBox
		ActionListener headingsComboBoxActionListener = new ActionListener() {
			public void actionPerformed(ActionEvent e) {
				int i = headingsComboBox.getSelectedIndex() + 1;
				String headtag = "h" + String.valueOf(i);
				insertTag("<" + headtag + ">", "</" + headtag + ">");
			}
		};
		// -----------------------------------------------------------------------------
		//add htmlentities selected on comboBox
		ActionListener charsComboBoxActionListener = new ActionListener() {
			public void actionPerformed(ActionEvent e) {
				int i = charsComboBox.getSelectedIndex() - 1;
				Integer charval = (Integer) HTMLEntities.getEntitiesTable()[i][1];
				insertTag("&#" + charval.toString() + ";", "");
			}
		};
		// -----------------------------------------------------------------------------
		//insert unordered list
		ActionListener ulActionListener = new ActionListener() {
			public void actionPerformed(ActionEvent e) {
				insertTag("<ul type=\"disc\">\r\n<li>\r\n", "\r\n</li>\r\n</ul>");
				if (current_editor.getSelectedText() != null) {
					replaceTag("<br>", "\r\n</li>\r\n<li>");
				}
				auto_li_enabled = true;
				if (source_mode) {
					br_state_source = false;
				} else {
					br_state_editor = false;
				}
				autobreak.setSelected(false);
				autoListItem.setSelected(auto_li_enabled);
			}
		};
		// -----------------------------------------------------------------------------
		//insert ordered list
		ActionListener olActionListener = new ActionListener() {
			public void actionPerformed(ActionEvent e) {
				insertTag("<ol type=\"1\">\r\n<li>\r\n", "\r\n</li>\r\n</ol>");
				if (current_editor.getSelectedText() != null) {
					replaceTag("<br>", "\r\n</li>\r\n<li>");
				}
				auto_li_enabled = true;
				if (source_mode) {
					br_state_source = false;
				} else {
					br_state_editor = false;
				}
				autobreak.setSelected(false);
				autoListItem.setSelected(auto_li_enabled);
			}
		};
		// -----------------------------------------------------------------------------
		//delete table row
		ActionListener delRowActionListener = new ActionListener() {
			public void actionPerformed(ActionEvent e) {
				tableIndex this_table_data = createTableIndex();
				if (this_table_data != null) {
					String html_code = current_editor.getText();
					if (this_table_data.row_start[this_table_data.current_row] != null) {
						html_code = html_code.substring(0, this_table_data.row_start[this_table_data.current_row]
						                                                             .intValue())
						                                                             + html_code.substring(this_table_data.row_end[this_table_data.current_row].intValue());
					}
					setHTMLCode(html_code, true, true);
				}
			}
		};
		// -----------------------------------------------------------------------------
		//delete table column
		ActionListener delColActionListener = new ActionListener() {
			public void actionPerformed(ActionEvent e) {
				tableIndex this_table_data = createTableIndex();
				if (this_table_data != null) {
					String html_code = current_editor.getText();
					int original_len = html_code.length();
					int removed_len;
					for (int r = 0; r < this_table_data.rows; r++) {
						if (this_table_data.cell_start[r][this_table_data.current_col] != null) {
							removed_len = original_len - html_code.length();
							html_code = html_code
							.substring(0, this_table_data.cell_start[r][this_table_data.current_col].intValue()
									- removed_len)
									+ html_code.substring(this_table_data.cell_end[r][this_table_data.current_col]
									                                                  .intValue()
									                                                  - removed_len);
						}
					}
					setHTMLCode(html_code, true, true);
				}
			}
		};
		// -----------------------------------------------------------------------------
		// insert table row
		ActionListener insRowActionListener = new ActionListener() {
			public void actionPerformed(ActionEvent e) {
				tableIndex this_table_data = createTableIndex();
				if (this_table_data != null) {
					String html_code = current_editor.getText();
					if (this_table_data.row_end[this_table_data.current_row] != null) {
						String new_row = "<tr>";
						for (int c = 0; c < this_table_data.cols; c++) {
							new_row += "<td>&nbsp;</td>";
						}
						new_row += "</tr>";
						int insert_point = this_table_data.row_end[this_table_data.current_row].intValue();
						html_code = html_code.substring(0, insert_point) + new_row + html_code.substring(insert_point);
					}
					setHTMLCode(html_code, true, true);
				}
			}
		};
		
		// -----------------------------------------------------------------------------
		// insert table column
		ActionListener insColActionListener = new ActionListener() {
			public void actionPerformed(ActionEvent e) {
				tableIndex this_table_data = createTableIndex();
				if (this_table_data != null) {
					String html_code = current_editor.getText();
					int original_len = html_code.length();
					int added_len;
					int insert_point;
					for (int r = 0; r < this_table_data.rows; r++) {
						if (this_table_data.cell_end[r][this_table_data.current_col] != null) {
							added_len = html_code.length() - original_len;
							insert_point = this_table_data.cell_end[r][this_table_data.current_col].intValue()
							+ added_len;
							html_code = html_code.substring(0, insert_point) + "<td>&nbsp;</td>"
							+ html_code.substring(insert_point);
						}
					}
					setHTMLCode(html_code, true, true);
				}
			}
		};
		// -----------------------------------------------------------------------------
		// merge horizontal table cells
		ActionListener mergeHorizCellsActionListener = new ActionListener() {
			public void actionPerformed(ActionEvent e) {
				tableIndex this_table_data = createTableIndex();
				if (this_table_data != null) {
					String html_code = current_editor.getText();
					int colspan = 0;
					if (this_table_data.current_col < this_table_data.cols) {
						if ((this_table_data.cell_start[this_table_data.current_row][this_table_data.current_col] != null)
								&& (this_table_data.cell_start[this_table_data.current_row][this_table_data.current_col + 1] != null)) {
							//get first cell tag data
							String new_cell = html_code
							.substring(
									this_table_data.cell_start[this_table_data.current_row][this_table_data.current_col]
									                                                        .intValue(),
									                                                        this_table_data.cell_content_start[this_table_data.current_row][this_table_data.current_col]
									                                                                                                                        .intValue());
							//adjust colspan
							int colspan_pos = new_cell.indexOf("colspan=");
							if (colspan_pos > 0) {
								colspan = 1 + Integer.parseInt(new_cell.substring(colspan_pos,
										Math.min(colspan_pos + 13, new_cell.length())).split("\"")[1]);
								new_cell = new_cell.replaceFirst("colspan=\"[0-9]{0,2}\"", "colspan=\""
										+ String.valueOf(colspan) + "\"");
							} else {
								new_cell = new_cell.substring(0, new_cell.length() - 1) + " colspan=\"2\""
								+ new_cell.substring(new_cell.length() - 1);
							}
							//add first cell content
							new_cell += html_code
							.substring(
									this_table_data.cell_content_start[this_table_data.current_row][this_table_data.current_col]
									                                                                .intValue(),
									                                                                this_table_data.cell_end[this_table_data.current_row][this_table_data.current_col]
									                                                                                                                      .intValue() - 5);
							//add second cell content and closetag
							new_cell += html_code
							.substring(
									this_table_data.cell_content_start[this_table_data.current_row][this_table_data.current_col + 1]
									                                                                .intValue(),
									                                                                this_table_data.cell_end[this_table_data.current_row][this_table_data.current_col + 1]
									                                                                                                                      .intValue());
							//compose new html code
							html_code = html_code
							.substring(
									0,
									this_table_data.cell_start[this_table_data.current_row][this_table_data.current_col]
									                                                        .intValue())
									                                                        + new_cell
									                                                        + html_code
									                                                        .substring(this_table_data.cell_end[this_table_data.current_row][this_table_data.current_col + 1]
									                                                                                                                         .intValue());
						}
					}
					setHTMLCode(html_code, true, true);
				}
			}
		};
		// -----------------------------------------------------------------------------
		// merge vertical table cells
		ActionListener mergeVertCellsActionListener = new ActionListener() {
			public void actionPerformed(ActionEvent e) {
				tableIndex this_table_data = createTableIndex();
				if (this_table_data != null) {
					String html_code = current_editor.getText();
					int rowspan = 0;
					if (this_table_data.current_col < this_table_data.cols) {
						if ((this_table_data.cell_start[this_table_data.current_row][this_table_data.current_col] != null)
								&& (this_table_data.cell_start[this_table_data.current_row + 1][this_table_data.current_col] != null)) {
							//get first cell tag data
							String new_cell = html_code
							.substring(
									this_table_data.cell_start[this_table_data.current_row][this_table_data.current_col]
									                                                        .intValue(),
									                                                        this_table_data.cell_content_start[this_table_data.current_row][this_table_data.current_col]
									                                                                                                                        .intValue());
							//adjust colspan
							int rowspan_pos = new_cell.indexOf("rowspan=");
							int affected_row_offset = 1;
							if (rowspan_pos > 0) {
								rowspan = 1 + Integer.parseInt(new_cell.substring(rowspan_pos,
										Math.min(rowspan_pos + 13, new_cell.length())).split("\"")[1]);
								new_cell = new_cell.replaceFirst("rowspan=\"[0-9]{0,2}\"", "rowspan=\""
										+ String.valueOf(rowspan) + "\"");
								affected_row_offset = rowspan - 1;
							} else {
								new_cell = new_cell.substring(0, new_cell.length() - 1) + " rowspan=\"2\""
								+ new_cell.substring(new_cell.length() - 1);
							}
							//add first cell content
							new_cell += html_code
							.substring(
									this_table_data.cell_content_start[this_table_data.current_row][this_table_data.current_col]
									                                                                .intValue(),
									                                                                this_table_data.cell_end[this_table_data.current_row][this_table_data.current_col]
									                                                                                                                      .intValue() - 5);
							//add second cell content and closetag
							new_cell += html_code
							.substring(
									this_table_data.cell_content_start[this_table_data.current_row
									                                   + affected_row_offset][this_table_data.current_col].intValue(),
									                                   this_table_data.cell_end[this_table_data.current_row + affected_row_offset][this_table_data.current_col]
									                                                                                                               .intValue());
							//compose new html code
							html_code = html_code
							.substring(
									0,
									this_table_data.cell_start[this_table_data.current_row][this_table_data.current_col]
									                                                        .intValue())
									                                                        + new_cell
									                                                        + html_code
									                                                        .substring(
									                                                        		this_table_data.cell_end[this_table_data.current_row][this_table_data.current_col]
									                                                        		                                                      .intValue(),
									                                                        		                                                      this_table_data.cell_start[this_table_data.current_row
									                                                        		                                                                                 + affected_row_offset][this_table_data.current_col]
									                                                        		                                                                                                        .intValue())
									                                                        		                                                                                                        + html_code.substring(this_table_data.cell_end[this_table_data.current_row
									                                                        		                                                                                                                                                       + affected_row_offset][this_table_data.current_col].intValue());
						}
					}
					setHTMLCode(html_code, true, true);
				}
			}
		};
		// -----------------------------------------------------------------------------
		// display table dialog
		ActionListener tableActionListener = new ActionListener() {
			public void actionPerformed(ActionEvent e) {
				ImageIcon icon_path = getImageIcon(buttons_images_path + "color.gif");
				int num_attributes = 8;
				attribute_string_array = new String[num_attributes];
				
				// create attributes panel
				JPanel attributesPanel = new JPanel();
				attributesPanel.setLayout(new BoxLayout(attributesPanel, BoxLayout.Y_AXIS));
				attributesPanel.setAlignmentX(JPanel.LEFT_ALIGNMENT);
				
				//add input
				
				attribute_string_array[6] = "2";
				final JTextField this_attributer = new JTextField("2");
				CaretListener rowsAction = new CaretListener() {
					public void caretUpdate(CaretEvent e) {
						if (this_attributer.getText().length() > 0) { //if a
							// value
							// has
							// been
							// selected
							attribute_string_array[6] = this_attributer.getText();
						} else {
							attribute_string_array[6] = "1";
						}
					}
				};
				this_attributer.setAlignmentX(JTextField.LEFT_ALIGNMENT);
				this_attributer.addCaretListener(rowsAction);
				
				attribute_string_array[7] = "2";
				final JTextField this_attributec = new JTextField("2");
				CaretListener colsAction = new CaretListener() {
					public void caretUpdate(CaretEvent e) {
						if (this_attributec.getText().length() > 0) { //if a
							// value
							// has
							// been
							// selected
							attribute_string_array[7] = this_attributer.getText();
						} else {
							attribute_string_array[7] = "1";
						}
					}
				};
				this_attributec.setAlignmentX(JTextField.LEFT_ALIGNMENT);
				this_attributec.addCaretListener(colsAction);
				
				JLabel attribute_labelr = input_label_component(configuration.getTMXresources().getString("R_ROWS", "number of rows"),
						"cols : " + configuration.getTMXresources().getString("R_ROWS", "number of rows"));
				
				JLabel attribute_labelc = input_label_component(configuration.getTMXresources().getString("R_COLS",
				"number of columns"), "cols : " + configuration.getTMXresources().getString("R_COLS", "number of columns"));
				
				JLabel attribute_label0 = input_label_component(configuration.getTMXresources().getString("R_WIDTH",
				"override image width"), "width : "
				+ configuration.getTMXresources().getString("R_WIDTH", "override image width") + " [pixels] [%]");
				JComponent this_attribute0 = attribute_input_component(0, 0, "width", "", "", null, null);
				
				JLabel attribute_label1 = input_label_component(configuration.getTMXresources().getString("R_BORDER_SIZE",
				"border size"), "border : " + configuration.getTMXresources().getString("R_BORDER_SIZE", "border size")
				+ " [pixels]");
				JComponent this_attribute1 = attribute_input_component(0, 1, "border", "", "1", null, null);
				
				JLabel attribute_label2 = input_label_component(configuration.getTMXresources().getString("R_CELL_PADDING",
				"spacing within cells"), "cellpadding : "
				+ configuration.getTMXresources().getString("R_CELL_PADDING", "spacing within cells") + "[pixels]");
				JComponent this_attribute2 = attribute_input_component(0, 2, "cellpadding", "", "1", null, null);
				
				JLabel attribute_label3 = input_label_component(configuration.getTMXresources().getString("R_CELL_SPACING",
				"spacing between cells"), "cellspacing : "
				+ configuration.getTMXresources().getString("R_CELL_SPACING", "spacing between cells") + "[pixels]");
				JComponent this_attribute3 = attribute_input_component(0, 3, "cellspacing", "", "1", null, null);
				
				JLabel attribute_label4 = input_label_component(configuration.getTMXresources().getString("R_ALIGNMENT", "alignment"),
						"align : " + configuration.getTMXresources().getString("R_ALIGNMENT", "alignment"));
				JComponent this_attribute4 = attribute_input_component(3, 4, "align", "left:center:right", "", null,
						null);
				
				JLabel attribute_label5 = input_label_component(configuration.getTMXresources().getString("R_BGCOLOR",
				"background color"), "bgcolor : " + configuration.getTMXresources().getString("R_BGCOLOR", "background color"));
				JComponent this_attribute5 = attribute_input_component(0, 5, "bgcolor", "", "", null, null);
				
				JComponent color_chooser5 = attribute_input_component(4, 5, "color", "", "", icon_path,
						(JTextField) this_attribute5);
				
				attributesPanel.add(attribute_labelr);
				attributesPanel.add(this_attributer);
				attributesPanel.add(attribute_labelc);
				attributesPanel.add(this_attributec);
				attributesPanel.add(attribute_label0);
				attributesPanel.add(this_attribute0);
				attributesPanel.add(attribute_label1);
				attributesPanel.add(this_attribute1);
				attributesPanel.add(attribute_label2);
				attributesPanel.add(this_attribute2);
				attributesPanel.add(attribute_label3);
				attributesPanel.add(this_attribute3);
				attributesPanel.add(attribute_label4);
				attributesPanel.add(this_attribute4);
				attributesPanel.add(attribute_label5);
				attributesPanel.add(color_chooser5);
				attributesPanel.add(this_attribute5);
				
				int result = JOptionPane.showConfirmDialog(this_applet, attributesPanel, configuration.getTMXresources().getString(
						"R_INSERT_TABLE", "Insert Table"), JOptionPane.OK_CANCEL_OPTION, JOptionPane.PLAIN_MESSAGE);
				
				if (result == 0) { //OK button has been selected
					//create attribute string
					String attribute_string = "";
					//for each attribute
					for (int j = 0; j < (num_attributes - 2); j++) {
						if (attribute_string_array[j] != null) {
							attribute_string += attribute_string_array[j]; //compose
							// attribute
							// string
						}
					}
					String intag = "<table " + attribute_string + ">";
					//add rows and columns:
					int rows = Integer.parseInt(attribute_string_array[6]);
					int cols = Integer.parseInt(attribute_string_array[7]);
					for (int r = 0; r < rows; r++) {
						intag += "<tr>";
						for (int c = 0; c < cols; c++) {
							intag += "<td>&nbsp;</td>";
						}
						intag += "</tr>";
					}
					intag += "</table>";
					insertTag(intag, "");
				}
			}
		};
		// -----------------------------------------------------------------------------
		//display image dialog
		ActionListener imageActionListener = new ActionListener() {
			public void actionPerformed(ActionEvent e) {
				int num_attributes = 10;
				attribute_string_array = new String[num_attributes];
				
				// create main panel
				JPanel mainPanel = new JPanel();
				mainPanel.setLayout(new BorderLayout());
				
				final JLabel imagePane = new JLabel();
				imagePane.setVerticalAlignment(JLabel.TOP);
				imagePane.setHorizontalAlignment(JLabel.LEFT);
				
				// create attributes panel
				JPanel attributesPanel = new JPanel();
				attributesPanel.setLayout(new BoxLayout(attributesPanel, BoxLayout.Y_AXIS));
				attributesPanel.setAlignmentX(JPanel.LEFT_ALIGNMENT);
				
				//add inputs
				JLabel attribute_label1 = input_label_component(configuration.getTMXresources()
						.getString("R_ALT", "alternate content"), "alt : "
						+ configuration.getTMXresources().getString("R_ALT", "alternate content"));
				JComponent this_attribute1 = attribute_input_component(0, 1, "alt", "", "", null, null);
				
				JLabel attribute_label2 = input_label_component(configuration.getTMXresources().getString("R_WIDTH",
				"override image width"), "width : "
				+ configuration.getTMXresources().getString("R_WIDTH", "override image width") + " [pixels]");
				final JTextField this_attribute2 = (JTextField) attribute_input_component(0, 2, "width", "", "", null,
						null);
				
				JLabel attribute_label3 = input_label_component(configuration.getTMXresources().getString("R_HEIGHT",
				"override image height"), "height : "
				+ configuration.getTMXresources().getString("R_HEIGHT", "override image height") + " [pixels]");
				final JTextField this_attribute3 = (JTextField) attribute_input_component(0, 3, "height", "", "", null,
						null);
				
				JLabel attribute_label4 = input_label_component(configuration.getTMXresources().getString("R_HORIZONTAL_SPACE",
				"horizontal gutter"), "hspace : "
				+ configuration.getTMXresources().getString("R_HORIZONTAL_SPACE", "horizontal gutter") + " [pixels]");
				JComponent this_attribute4 = attribute_input_component(0, 4, "hspace", "", "", null, null);
				
				JLabel attribute_label5 = input_label_component(configuration.getTMXresources().getString("R_VERTICAL_SPACE",
				"vertical gutter"), "vspace : "
				+ configuration.getTMXresources().getString("R_VERTICAL_SPACE", "vertical gutter") + " [pixels]");
				JComponent this_attribute5 = attribute_input_component(0, 5, "vspace", "", "", null, null);
				
				JLabel attribute_label6 = input_label_component(configuration.getTMXresources().getString("R_BORDER_SIZE",
				"border size"), "border : " + configuration.getTMXresources().getString("R_BORDER_SIZE", "border size")
				+ " [pixels]");
				JComponent this_attribute6 = attribute_input_component(0, 6, "border", "", "0", null, null);
				
				JLabel attribute_label7 = input_label_component(configuration.getTMXresources().getString("R_ALIGNMENT", "alignment"),
						"align : " + configuration.getTMXresources().getString("R_ALIGNMENT", "alignment"));
				JComponent this_attribute7 = attribute_input_component(3, 7, "align", "left:right:top:middle:bottom",
						"", null, null);
				
				JLabel attribute_label8 = input_label_component(configuration.getTMXresources().getString("R_NAME", "name"), "name : "
						+ configuration.getTMXresources().getString("R_TAG_NAME_DESC", "named link end"));
				JComponent this_attribute8 = attribute_input_component(0, 8, "name", "", "", null, null);
				
				JLabel attribute_label9 = input_label_component("id", "id : "
						+ configuration.getTMXresources().getString("R_TAG_ID_DESC", "document-wide unique id"));
				JComponent this_attribute9 = attribute_input_component(0, 9, "id", "", "", null, null);
				
				JLabel attribute_label0 = input_label_component("source", "src: URI of the image");
				//img src input -------------------------
				final JComboBox this_attribute0 = new JComboBox();
				ActionListener attribute0InputAction = new ActionListener() {
					public void actionPerformed(ActionEvent e) {
						int i = this_attribute0.getSelectedIndex(); //selected
						// tag index
						//URL imgurl;
						String imgsrc;
						if (i > 0) { //if a value has been selected
							imgsrc = (String) this_attribute0.getItemAt(i);
						} else {
							imgsrc = (String) this_attribute0.getEditor().getItem();
						}
						if ((imgsrc == null) || (imgsrc.length() <= 0)) {
							attribute_string_array[0] = "";
							imagePane.setIcon(null);
							imagePane.repaint();
						} else {
							//calculate correct image address
							if (HTMLURLs.isRelativeLink(imgsrc)) {
								imgsrc = HTMLURLs.resolveRelativeURL(images_path + imgsrc);
								
								ImageIcon this_image = new ImageIcon(HTMLURLs.setURL(imgsrc));
								//set width and height
								this_attribute2.setText(String.valueOf(this_image.getIconWidth()));
								this_attribute3.setText(String.valueOf(this_image.getIconHeight()));
								//display image preview
								imagePane.setIcon(this_image);
								imagePane.repaint();
							} else {
								imagePane.setIcon(null);
								imagePane.repaint();
								
							}
							attribute_string_array[0] = "src=\"" + imgsrc + "\"";
						}
					}
				};
				String[] attrib_items = images_list.split(":");
				int num_attrib_items = attrib_items.length;
				this_attribute0.addItem(""); //add void item
				this_attribute0.setEditable(true);
				int k;
				//for each attribute item
				for (k = 0; k < num_attrib_items; k++) {
					this_attribute0.addItem(attrib_items[k]);
				}
				//set default value
				this_attribute0.setSelectedItem("");
				this_attribute0.setAlignmentX(JComboBox.LEFT_ALIGNMENT);
				this_attribute0.addActionListener(attribute0InputAction);
				//end img src input -------------------------
				
				attributesPanel.add(attribute_label0);
				attributesPanel.add(this_attribute0);
				attributesPanel.add(attribute_label1);
				attributesPanel.add(this_attribute1);
				attributesPanel.add(attribute_label2);
				attributesPanel.add(this_attribute2);
				attributesPanel.add(attribute_label3);
				attributesPanel.add(this_attribute3);
				attributesPanel.add(attribute_label4);
				attributesPanel.add(this_attribute4);
				attributesPanel.add(attribute_label5);
				attributesPanel.add(this_attribute5);
				attributesPanel.add(attribute_label6);
				attributesPanel.add(this_attribute6);
				attributesPanel.add(attribute_label7);
				attributesPanel.add(this_attribute7);
				attributesPanel.add(attribute_label8);
				attributesPanel.add(this_attribute8);
				attributesPanel.add(attribute_label9);
				attributesPanel.add(this_attribute9);
				
				JScrollPane imagePreviewPanel = new JScrollPane(imagePane);
				imagePreviewPanel.setPreferredSize(new Dimension(getSize().width * 1 / 2, getSize().height * 1 / 2));
				
				mainPanel.add(attributesPanel, BorderLayout.WEST);
				mainPanel.add(imagePreviewPanel, BorderLayout.CENTER);
				
				int result = JOptionPane.showConfirmDialog(this_applet, mainPanel, configuration.getTMXresources().getString(
						"R_INSERT_IMAGE", "Insert Image"), JOptionPane.OK_CANCEL_OPTION, JOptionPane.PLAIN_MESSAGE);
				
				if (result == 0) { //OK button has been selected
					//create attribute string
					String attribute_string = "";
					//for each attribute
					for (int j = 0; j < num_attributes; j++) {
						if (attribute_string_array[j] != null) {
							// compose attribute string
							attribute_string += attribute_string_array[j];
						}
					}
					if (attribute_string.length() > 0) {
						insertTag("<img " + attribute_string + ">", "");
					}
				}
			}
		};
		// -----------------------------------------------------------------------------
		// display link dialog
		ActionListener linkActionListener = new ActionListener() {
			public void actionPerformed(ActionEvent e) {
				int num_attributes = 3;
				attribute_string_array = new String[num_attributes];
				
				// create attributes panel
				JPanel attributesPanel = new JPanel();
				attributesPanel.setLayout(new BoxLayout(attributesPanel, BoxLayout.Y_AXIS));
				attributesPanel.setAlignmentX(JPanel.LEFT_ALIGNMENT);
				
				//add input label
				JLabel attribute_label0 = input_label_component(configuration.getTMXresources().getString("R_HREF", "href"), "href : "
						+ configuration.getTMXresources().getString("R_HREF_DESC", "URI for linked resource"));
				JComponent this_attribute0 = attribute_input_component(0, 0, "href", "", "", null, null);
				
				JLabel attribute_label1 = input_label_component(configuration.getTMXresources().getString("R_TARGET", "target"),
						"target : " + configuration.getTMXresources().getString("R_TARGET_DESC", "render in this frame"));
				JComponent this_attribute1 = attribute_input_component(3, 1, "target", "_top:_parent:_self:_blank", "",
						null, null);
				
				JLabel attribute_label2 = input_label_component(configuration.getTMXresources().getString("R_NAME", "name"), "name : "
						+ configuration.getTMXresources().getString("R_NAME_DESC", "name"));
				JComponent this_attribute2 = attribute_input_component(0, 2, "name", "", "", null, null);
				
				attributesPanel.add(attribute_label0);
				attributesPanel.add(this_attribute0);
				attributesPanel.add(attribute_label1);
				attributesPanel.add(this_attribute1);
				attributesPanel.add(attribute_label2);
				attributesPanel.add(this_attribute2);
				
				int result = JOptionPane.showConfirmDialog(this_applet, attributesPanel, configuration.getTMXresources().getString(
						"R_INSERT_HYPERLINK", "Insert Hyperlink"), JOptionPane.OK_CANCEL_OPTION,
						JOptionPane.PLAIN_MESSAGE);
				
				if (result == 0) { //OK button has been selected
					//create attribute string
					String attribute_string = "";
					//for each attribute
					for (int j = 0; j < num_attributes; j++) {
						if (attribute_string_array[j] != null) {
							// compose attribute string
							attribute_string += attribute_string_array[j];
						}
					}
					if (attribute_string.length() > 0) {
						insertTag("<a " + attribute_string + ">", "</a>");
					}
				}
				
			}
		};
		// -----------------------------------------------------------------------------
		// set autobreak mode (insert br when pushing enter button
		ActionListener autoBreakActionListener = new ActionListener() {
			public void actionPerformed(ActionEvent e) {
				if (source_mode) {
					br_state_source = autobreak.isSelected();
				} else {
					br_state_editor = autobreak.isSelected();
				}
				current_editor.requestFocus(); //request focus for this editor
			}
		};
		// -----------------------------------------------------------------------------
		//set auto li mode (insert li when pushing enter button)
		ActionListener autoListItemActionListener = new ActionListener() {
			public void actionPerformed(ActionEvent e) {
				auto_li_enabled = autoListItem.isSelected();
				if (source_mode) {
					br_state_source = false;
					autobreak.setSelected(false);
				} else {
					br_state_editor = !auto_li_enabled;
					autobreak.setSelected(br_state_editor);
				}
				current_editor.requestFocus(); //request focus for this editor
			}
		};
		// -----------------------------------------------------------------------------
		//remove the tag nearest to cursor position
		ActionListener removeTagAction = new ActionListener() {
			public void actionPerformed(ActionEvent e) {
				removeTag();
			}
		};
		// -----------------------------------------------------------------------------
		//all xhtml tags combo box
		ActionListener allTagsComboBoxAction = new ActionListener() {
			public void actionPerformed(ActionEvent e) {
				int i = allTagsComboBox.getSelectedIndex(); //selected tag
				// index
				if (i > 0) {
					String tagkey = String.valueOf(i);
					if (configuration.getTranscoder().getXHTMLelements().getXHTMLTags().getString(tagkey, "attributes").length() > 0) { //display tag dialog
						
						// create main attribute panel (intro + attributes
						// panel)
						JPanel attributeMainPanel = new JPanel();
						attributeMainPanel.setLayout(new BorderLayout());
						
						//compose introductive message
						JLabel intro1 = new JLabel(configuration.getTMXresources().getString("R_ELEMENT", "ELEMENT").toUpperCase()
								+ ": <" + configuration.getTranscoder().getXHTMLelements().getXHTMLTags().getString(tagkey, "name") + ">");
						intro1.setToolTipText(configuration.getTranscoder().getXHTMLelements().getXHTMLTags().getString(tagkey, "description"));
						
						//category data
						JLabel intro2 = new JLabel(configuration.getTMXresources().getString("R_CATEGORY", "CATEGORY").toUpperCase()
								+ ": "
								+ configuration.getTranscoder().getXHTMLelements().getXHTMLCategories().getString(configuration.getTranscoder().getXHTMLelements().getXHTMLTags().getString(tagkey,
								"category"), "name"));
						intro2.setToolTipText(configuration.getTranscoder().getXHTMLelements().getXHTMLCategories().getString(configuration.getTranscoder().getXHTMLelements().getXHTMLTags()
								.getString(tagkey, "category"), "description"));
						
						//status data
						JLabel intro3 = new JLabel(configuration.getTMXresources().getString("R_STATUS", "STATUS").toUpperCase()
								+ ": "
								+ configuration.getTranscoder().getXHTMLelements().getXHTMLStatus().getString(configuration.getTranscoder().getXHTMLelements().getXHTMLTags().getString(tagkey,
								"status"), "name"));
						intro3.setToolTipText(configuration.getTranscoder().getXHTMLelements().getXHTMLStatus().getString(configuration.getTranscoder().getXHTMLelements().getXHTMLTags().getString(
								tagkey, "status"), "description"));
						
						JLabel intro4 = new JLabel(configuration.getTMXresources().getString("R_ATTRIBUTES", "ATTRIBUTES")
								.toUpperCase()
								+ ":");
						
						JPanel intro = new JPanel();
						intro.setLayout(new GridLayout(4, 1));
						intro.add(intro1);
						intro.add(intro2);
						intro.add(intro3);
						intro.add(intro4);
						
						attributeMainPanel.add(intro, BorderLayout.NORTH);
						
						//get attrbutes IDs
						String[] attribute_id = configuration.getTranscoder().getXHTMLelements().getXHTMLTags().getString(tagkey, "attributes").split(":");
						int num_attributes = attribute_id.length;
						
						//the following will contain the string attribute to
						// attach on tag
						attribute_string_array = new String[num_attributes];
						
						//Arrays.sort(attrib_id); // sort array
						
						// create attributes panel
						JPanel attributesPanel = new JPanel();
						attributesPanel.setLayout(new BoxLayout(attributesPanel, BoxLayout.Y_AXIS));
						attributesPanel.setAlignmentX(JPanel.LEFT_ALIGNMENT);
						
						//for each attribute id
						for (int j = 0; j < num_attributes; j++) {
							//create attribute label
							//final int attribute_id = attribute_id[j]);
							final int attrib_index = j;
							
							//add input label
							JLabel attrib_label = attribute_label_component(
									
									configuration.getTranscoder().getXHTMLelements().getXHTMLAttributes().getString(attribute_id[j], "name"),
									configuration.getTranscoder().getXHTMLelements().getXHTMLAttributes().getString(attribute_id[j], "description"),
									configuration.getTranscoder().getXHTMLelements().getXHTMLAttributes().getInt(attribute_id[j], "status", 0),
									configuration.getTranscoder().getXHTMLelements().getXHTMLStatus().getString(configuration.getTranscoder().getXHTMLelements().getXHTMLAttributes().getString(
											attribute_id[j], "status"), "name"), configuration.getTranscoder().getXHTMLelements().getXHTMLStatus().getString(
													configuration.getTranscoder().getXHTMLelements().getXHTMLAttributes().getString(attribute_id[j], "status"),
											"description"));
							attributesPanel.add(attrib_label);
							
							//add input component
							JComponent this_attribute = attribute_input_component(configuration.getTranscoder().getXHTMLelements().getXHTMLAttributes().getInt(
									attribute_id[j], "type", 0), attrib_index, configuration.getTranscoder().getXHTMLelements().getXHTMLAttributes().getString(
											attribute_id[j], "name"), configuration.getTranscoder().getXHTMLelements().getXHTMLAttributes().getString(attribute_id[j],
											"options"), configuration.getTranscoder().getXHTMLelements().getXHTMLAttributes().getString(attribute_id[j], "default"),
											null, null);
							attributesPanel.add(this_attribute);
							
							//add color chooser button
							if (configuration.getTranscoder().getXHTMLelements().getXHTMLAttributes().getString(attribute_id[j], "description").toLowerCase()
									.indexOf("color") > 0) {
								JTextField parent_component = (JTextField) this_attribute;
								ImageIcon icon_path = getImageIcon(buttons_images_path + "color.gif");
								JComponent color_chooser = attribute_input_component(4, attrib_index,
										configuration.getTranscoder().getXHTMLelements().getXHTMLAttributes().getString(attribute_id[j], "name"),
										configuration.getTranscoder().getXHTMLelements().getXHTMLAttributes().getString(attribute_id[j], "options"),
										configuration.getTranscoder().getXHTMLelements().getXHTMLAttributes().getString(attribute_id[j], "default"), icon_path,
										parent_component);
								attributesPanel.add(color_chooser);
							}
						} //close for
						
						//add scrollable option
						JScrollPane scrollAttribute = new JScrollPane(attributesPanel);
						attributeMainPanel.add(scrollAttribute, BorderLayout.CENTER);
						scrollAttribute.setPreferredSize(new Dimension(getSize().width * 2 / 3, getSize().height / 3));
						//display attribute dialog
						int result = JOptionPane.showConfirmDialog(this_applet, attributeMainPanel, configuration.getTMXresources()
								.getString("R_ATTRIBUTES", "Attributes"), JOptionPane.OK_CANCEL_OPTION,
								JOptionPane.PLAIN_MESSAGE);
						
						if (result == 0) { //OK button has been selected
							//create attribute string
							String attribute_string = "";
							//for each attribute
							for (int j = 0; j < num_attributes; j++) {
								if (attribute_string_array[j] != null) {
									attribute_string += attribute_string_array[j]; //compose
									// attribute
									// string
								}
							}
							if (attribute_string.length() > 0) {
								attribute_string = " " + attribute_string;
							}
							if (configuration.getTranscoder().getXHTMLelements().getXHTMLTags().getInt(tagkey, "endtag", 0) == 0) {
								insertTag("<" + configuration.getTranscoder().getXHTMLelements().getXHTMLTags().getString(tagkey, "name") + attribute_string
										+ ">", "");
							} else {
								insertTag("<" + configuration.getTranscoder().getXHTMLelements().getXHTMLTags().getString(tagkey, "name") + attribute_string
										+ ">", "</" + configuration.getTranscoder().getXHTMLelements().getXHTMLTags().getString(tagkey, "name") + ">");
							}
						}
					} else { //insert tag directly
						if (configuration.getTranscoder().getXHTMLelements().getXHTMLTags().getInt(tagkey, "endtag", 0) == 0) {
							insertTag("<" + configuration.getTranscoder().getXHTMLelements().getXHTMLTags().getString(tagkey, "name") + ">", "");
						} else {
							insertTag("<" + configuration.getTranscoder().getXHTMLelements().getXHTMLTags().getString(tagkey, "name") + ">", "</"
									+ configuration.getTranscoder().getXHTMLelements().getXHTMLTags().getString(tagkey, "name") + ">");
						}
					}
				}
			}
		};
		// -----------------------------------------------------------------------------
		// cut action
		Action cutAction = new DefaultEditorKit.CutAction();
		// -----------------------------------------------------------------------------
		// copy action
		Action copyAction = new DefaultEditorKit.CopyAction();
		// -----------------------------------------------------------------------------
		// paste action
		Action pasteAction = new DefaultEditorKit.PasteAction();
		// -----------------------------------------------------------------------------
		// === END BUTTONS ACTIONS LISTENERS
		// ===========================================
		
		// Store Actions and ActionListeners on a vector to be referred from
		// configuration file
		
		// Actions 0-4
		buttonActionListener.add(cutAction); // 0
		buttonActionListener.add(copyAction); // 1
		buttonActionListener.add(pasteAction); // 2
		buttonActionListener.add(undoAction); // 3
		buttonActionListener.add(redoAction); // 4
		
		// ActionListeners 5-25
		buttonActionListener.add(aboutActionListener); // 5
		buttonActionListener.add(loadDocumentActionListener); // 6
		buttonActionListener.add(saveDocumentActionListener); // 7
		buttonActionListener.add(fontActionListener); // 8
		buttonActionListener.add(selectAllActionListener); // 9
		buttonActionListener.add(findActionListener); // 10
		buttonActionListener.add(findNextActionListener); // 11
		buttonActionListener.add(headingsComboBoxActionListener); // 12
		buttonActionListener.add(ulActionListener); // 13
		buttonActionListener.add(olActionListener); // 14
		buttonActionListener.add(tableActionListener); // 15
		buttonActionListener.add(delRowActionListener); // 16
		buttonActionListener.add(delColActionListener); // 17
		buttonActionListener.add(insRowActionListener); // 18
		buttonActionListener.add(insColActionListener); // 19
		buttonActionListener.add(mergeHorizCellsActionListener); // 20
		buttonActionListener.add(mergeVertCellsActionListener); // 21
		buttonActionListener.add(imageActionListener); // 22
		buttonActionListener.add(linkActionListener); // 23
		buttonActionListener.add(autoBreakActionListener); // 24
		buttonActionListener.add(autoListItemActionListener); // 25
		buttonActionListener.add(removeTagAction); // 26
		buttonActionListener.add(allTagsComboBoxAction); // 27
		buttonActionListener.add(clearDocumentActionListener); // 28
		
		// Predefined menu elements:
		// -----------------------------------------------------------------------------
		headingsComboBox.setAlignmentX(JComboBox.LEFT_ALIGNMENT);
		for (int i = 1; i <= 6; i++) {
			headingsComboBox.addItem("H" + String.valueOf(i));
		}
		headingsComboBox.setToolTipText(configuration.getTMXresources().getString("R_HEADINGS_DESC", "Insert a title heading"));
		headingsComboBox.addActionListener(headingsComboBoxActionListener);
		// -----------------------------------------------------------------------------
		charsComboBox.setAlignmentX(JComboBox.LEFT_ALIGNMENT);
		charsComboBox.addItem("CH");
		for (int i = 0; i < HTMLEntities.getEntitiesTable().length; i++) {
			Integer charval = (Integer) HTMLEntities.getEntitiesTable()[i][1];
			//String entity = (String) HTMLUtils.getEntitiesTable()[i][0];
			charsComboBox.addItem(String.valueOf(" " + (char) charval.intValue()));
		}
		charsComboBox.setToolTipText(configuration.getTMXresources().getString("R_CHARS_DESC", "Insert an extended char"));
		charsComboBox.addActionListener(charsComboBoxActionListener);
		// -----------------------------------------------------------------------------
		//display a list of selectable tags
		allTagsComboBox.setAlignmentX(JComboBox.LEFT_ALIGNMENT);
		allTagsComboBox.addItem("HTML");
		//int i;
		// get current keys
		Enumeration tag_keys = configuration.getTranscoder().getXHTMLelements().getXHTMLTags().getKeys();
		// add products definition loading data from XML file
		while (tag_keys.hasMoreElements()) {
			String current_key = (String) tag_keys.nextElement();
			allTagsComboBox.addItem("<" + configuration.getTranscoder().getXHTMLelements().getXHTMLTags().getString(current_key, "name") + ">");
		}
		allTagsComboBox.setToolTipText(configuration.getTMXresources().getString("R_HTML_COMBO_DESC",
		"Allows you to set all attributes of selected HTML element for inserting"));
		allTagsComboBox.addActionListener(allTagsComboBoxAction);
		// -----------------------------------------------------------------------------
		autobreak = new JCheckBox(configuration.getTMXresources().getString("R_AUTO_BR", "auto <br>"), br_state_editor);
		autobreak.setAlignmentX(JCheckBox.LEFT_ALIGNMENT);
		autobreak.addActionListener(autoBreakActionListener);
		autobreak.setToolTipText(configuration.getTMXresources().getString("R_SET_BR_DESC", "enable/disable automatic BR"));
		// -----------------------------------------------------------------------------
		autoListItem = new JCheckBox(configuration.getTMXresources().getString("R_AUTO_LI", "auto <li>"), auto_li_enabled);
		autoListItem.setAlignmentX(JCheckBox.LEFT_ALIGNMENT);
		autoListItem.addActionListener(autoListItemActionListener);
		autoListItem.setToolTipText(configuration.getTMXresources().getString("R_SET_LI_DESC", "enable/disable automatic list item"));
		// -----------------------------------------------------------------------------
		
		// BUILD BUTTONS LOADING DATA FROM XML ---------
		
		buttons_panel.setLayout(new FlowLayout(FlowLayout.LEFT, 0, 0));
		
		Enumeration buttons_keys = configuration.getButtons().getKeys(); // get current keys
		while (buttons_keys.hasMoreElements()) {
			String ctkey = (String) buttons_keys.nextElement();
			
			//check if is a combobox
			if (configuration.getButtons().getString(ctkey, "action", "").indexOf("|") >= 0) {
				
				final JComboBox customComboBox = new JComboBox();
				customComboBox.setAlignmentX(JComboBox.LEFT_ALIGNMENT);
				customComboBox.setToolTipText(configuration.getButtons().getString(ctkey, "description"));
				customComboBox.addItem(configuration.getButtons().getString(ctkey, "name"));
				
				String[] custom_tag_strings = configuration.getButtons().getString(ctkey, "action", "").split("\\|\\|"); // entries
				// array
				// store intag and outtag
				final String[][] tagslist = new String[custom_tag_strings.length][2];
				for (int j = 0; j < custom_tag_strings.length; j++) {
					String[] thistag = custom_tag_strings[j].split("\\|"); //tags
					// parts
					if (thistag.length > 0) {
						customComboBox.addItem(thistag[0].trim()); // tag name
						if (thistag.length > 1) {
							tagslist[j][0] = HTMLEntities.unhtmlAngleBrackets(thistag[1].trim());
							if (thistag.length > 2) {
								tagslist[j][1] = HTMLEntities.unhtmlAngleBrackets(thistag[2].trim());
							} else {
								tagslist[j][1] = "";
							}
						} else {
							tagslist[j][0] = "";
							tagslist[j][1] = "";
						}
					}
				}
				//build action listener
				ActionListener customComboBoxActionListener = new ActionListener() {
					public void actionPerformed(ActionEvent e) {
						int k = customComboBox.getSelectedIndex() - 1;
						if ((k >= 0) && (k < tagslist.length) && (tagslist[k].length == 2)) {
							insertTag(tagslist[k][0], tagslist[k][1]);
						}
					}
				};
				customComboBox.addActionListener(customComboBoxActionListener);
				buttons_panel.add(customComboBox); // add customComboBox
			} // check for predefined menu elements
			else if (configuration.getButtons().getInt(ctkey, "action", -1) >= 100) {
				// predefined elements
				switch (configuration.getButtons().getInt(ctkey, "action", -1)) {
				case 100: {
					// auto BR and auto LI selectors
					buttons_panel.add(autobreak);
					buttons_panel.add(autoListItem);
					break;
				}
				case 101: {
					// extended chars
					buttons_panel.add(charsComboBox);
					break;
				}
				case 102: {
					// font headings
					buttons_panel.add(headingsComboBox);
					break;
				}
				case 103: {
					// all tags selector
					buttons_panel.add(allTagsComboBox);
					break;
				}
				}
			} else { //build a custom button
				JButton custom_button = customButton(configuration.getButtons().getString(ctkey, "name"), configuration.getButtons().getString(
						ctkey, "description"), configuration.getButtons().getString(ctkey, "icon"), HTMLEntities
						.unhtmlAngleBrackets(configuration.getButtons().getString(ctkey, "intag")), HTMLEntities
						.unhtmlAngleBrackets(configuration.getButtons().getString(ctkey, "outtag")), configuration.getButtons().getInt(ctkey,
								"action", -1), configuration.getButtons().getInt(ctkey, "keystroke", 0), configuration.getButtons().getInt(ctkey,
										"keymodifier", 0));
				buttons_panel.add(custom_button); // add button
			}
			
		}
		// wysiwyg editor
		JScrollPane scrollWYSIWYGPane = new JScrollPane(editor);
		// source editor
		JScrollPane scrollSourcePane = new JScrollPane(editsource);
		
		// create tabbed editor windows
		JTabbedPane tabbedPane = new JTabbedPane(JTabbedPane.BOTTOM);
		tabbedPane.getModel().addChangeListener(new TabListener());
		tabbedPane.addTab(configuration.getTMXresources().getString("R_WYSIWYG_EDIT", "WYSIWYG"), null, scrollWYSIWYGPane,
				configuration.getTMXresources().getString("R_WYSIWYG_EDIT_DESC", "WYSIWYG editor"));
		if (!param_hide_source) {
			tabbedPane.addTab(configuration.getTMXresources().getString("R_SOURCE_EDIT", "HTML"), null, scrollSourcePane,
					configuration.getTMXresources().getString("R_SOURCE_EDIT_DESC", "Edit source code"));
		}
		tabbedPane.addTab(configuration.getTMXresources().getString("R_INFO", "info"), null, info_panel(), configuration.getTMXresources().getString(
				"R_INFO_DESC", "Display info about this software"));
		
		//set the size of the buttons bar
		Dimension bpdim = buttons_panel.getMinimumSize();
		if (bpdim.width > this.getWidth()) {
			int newheight = (int) Math.round(0.5 + ((double) bpdim.width / this.getWidth())) * bpdim.height;
			buttons_panel.setPreferredSize(new Dimension(this.getWidth(), newheight));
		}
		
		getContentPane().setSize(this.getWidth(), this.getHeight());
		getContentPane().setLayout(new BorderLayout(0, 0));
		getContentPane().add(buttons_panel, BorderLayout.NORTH);
		getContentPane().add(tabbedPane, BorderLayout.CENTER);
		
		//quit from application
		ActionListener quitApplication = new ActionListener() {
			public void actionPerformed(ActionEvent e) {
				
				try {
					JSObject jsroot = JSObject.getWindow(this_applet);
					//close applet window
					jsroot.eval("window.close();");
					this_applet.stop();
				} catch (JSException jse) {
					System.err.println("JSObject is not supported: " + jse);
				}
				
			}
		};
		buttons_panel.registerKeyboardAction(quitApplication,
				KeyStroke.getKeyStroke(KeyEvent.VK_Q, KeyEvent.CTRL_MASK), JComponent.WHEN_IN_FOCUSED_WINDOW);
		
		setVisible(true);
	}
	
	// -----------------------------------------------------------------------------
	
	/**
	 * Listener Class to switch between WYISWIG and HTML SOURCE tab editors
	 */
	class TabListener implements ChangeListener {
		public void stateChanged(ChangeEvent e) {
			SingleSelectionModel model = (SingleSelectionModel) e.getSource();
			boolean sourceEditSelected = model.getSelectedIndex() == 1;
			if (sourceEditSelected) {
				source_mode = true;
				current_editor = editsource;
				setHTMLCode(editor.getText().replaceAll("<br>", "<br>\r\n"), true, false);
				autobreak.setSelected(br_state_source);
			} else {
				source_mode = false;
				current_editor = editor;
				setHTMLCode(editsource.getText(), true, false);
				autobreak.setSelected(br_state_editor);
			}
			current_editor.setCaretPosition(0);
			current_editor.requestFocus();
		}
	}
	
	/**
	 * Add text objects to undo stack.
	 * 
	 * @param txt
	 *        text to add.
	 */
	private void setUndo(String txt) {
		if (undo_stack.size() >= undo_size) {
			undo_stack.remove(0);
		}
		undo_stack.push(txt);
		undoAction.update();
	}
	
	/**
	 * Add text objects to redo stack.
	 * 
	 * @param txt
	 *        text to add.
	 */
	private void setRedo(String txt) {
		if (redo_stack.size() >= undo_size) {
			redo_stack.remove(0);
		}
		redo_stack.push(txt);
		redoAction.update();
	}
	
	/**
	 * class to handle UNDO action
	 */
	class UndoAction extends AbstractAction {
		
		/**
		 * 
		 */
		private static final long serialVersionUID = -8335764329752447139L;
		
		public UndoAction() {
			super("Undo");
			setEnabled(false);
		}
		
		public void actionPerformed(ActionEvent e) {
			if (!undo_stack.empty()) {
				// put last text to redo stack
				setRedo((String) undo_stack.pop());
				setHTMLCode((String) redo_stack.peek(), true, false);
			}
			update();
		}
		
		protected void update() {
			setEnabled(!undo_stack.empty());
		}
	}
	
	/**
	 * class to handle REDO action
	 */
	class RedoAction extends AbstractAction {
		/**
		 * 
		 */
		private static final long serialVersionUID = -6897457807728329142L;
		
		public RedoAction() {
			super("Redo");
			setEnabled(false);
		}
		
		public void actionPerformed(ActionEvent e) {
			if (!redo_stack.empty()) {
				// put last text to redo stack
				setUndo((String) redo_stack.pop());
				setHTMLCode((String) undo_stack.peek(), true, false);
			}
			update();
		}
		
		protected void update() {
			setEnabled(!redo_stack.empty());
		}
	}
	
	/**
	 * create label for attribute input
	 * 
	 * @param attribute_name
	 *        attribute name
	 * @param attribute_description
	 *        attribute description
	 * @param attribute_status
	 *        status of attribute (0-4) for displaying different colors
	 * @param status_name
	 *        status name
	 * @param status_description
	 *        status description
	 * @return label
	 */
	private JLabel attribute_label_component(String attribute_name, String attribute_description, int attribute_status,
			String status_name, String status_description) {
		
		JLabel attrib_label = new JLabel();
		
		String attribute_label = attribute_name + ": ";
		
		Color attribute_color = new Color(Integer.parseInt("000000", 16));
		switch (attribute_status) { //change color by status
		default:
		case 1: { //normal
			attribute_color = new Color(Integer.parseInt("000000", 16));
			break;
		}
		case 2: { //browser specific
			attribute_color = new Color(Integer.parseInt("008000", 16));
			break;
		}
		case 3: { //deprecated
			attribute_color = new Color(Integer.parseInt("800000", 16));
			break;
		}
		case 4: { //obsolete
			attribute_color = new Color(Integer.parseInt("808000", 16));
			break;
		}
		}
		
		attribute_label += attribute_description;
		
		attrib_label.setText(attribute_label);
		attrib_label.setForeground(attribute_color);
		attrib_label.setAlignmentX(JLabel.LEFT_ALIGNMENT);
		
		String attrib_status_description = configuration.getTMXresources().getString("R_STATUS", "STATUS") + ":";
		attrib_status_description += " " + status_name;
		attrib_status_description += " (" + status_description + ")";
		attrib_label.setToolTipText(attrib_status_description);
		
		return attrib_label;
	}
	
	/**
	 * return input component for tag attributes
	 * 
	 * @param input_type
	 *        type of input: 0=inputttext; 1=checkbox; 2=textarea; 3=combobox; 4=colorchooser;
	 * @param attribute_index
	 *        this attribute number
	 * @param attribute_name
	 *        this attribute name
	 * @param attribute_values
	 *        list of values separated by ":" for combobox (type 3)
	 * @param default_value
	 *        default value
	 * @param attribute_icon
	 *        ImageIcon for colorchooser button (only for type 4)
	 * @param parent_component
	 *        inputtext component where to store the colorchooser value (only for type 4)
	 * @return input component
	 */
	private JComponent attribute_input_component(int input_type, int attribute_index, String attribute_name,
			String attribute_values, String default_value, ImageIcon attribute_icon, JTextField parent_component) {
		
		return attribute_input_component2(input_type, attribute_index, attribute_name, attribute_name + "=\"", "\"",
				attribute_values, default_value, attribute_icon, parent_component);
		
	}
	
	/**
	 * return input component for tag attributes
	 * 
	 * @param input_type
	 *        type of input: 0=inputttext; 1=checkbox; 2=textarea; 3=combobox; 4=colorchooser;
	 * @param attribute_index
	 *        this attribute number
	 * @param attribute_name
	 *        this attribute name
	 * @param attribute_start
	 *        this attribute start string (e.g: style=")
	 * @param attribute_end
	 *        this attribute end string (e.g: ")
	 * @param attribute_values
	 *        list of values separated by ":" for combobox (type 3)
	 * @param default_value
	 *        default value
	 * @param attribute_icon
	 *        ImageIcon for colorchooser button (only for type 4)
	 * @param parent_component
	 *        inputtext component where to store the colorchooser value (only for type 4)
	 * @return input component
	 */
	private JComponent attribute_input_component2(int input_type, final int attribute_index,
			final String attribute_name, final String attribute_start, final String attribute_end,
			final String attribute_values, final String default_value, final ImageIcon attribute_icon,
			final JTextField parent_component) {
		final JComponent return_component;
		
		//seet dfault value
		if ((default_value != null) && (default_value.length() > 0)) {
			attribute_string_array[attribute_index] = attribute_start + default_value + attribute_end;
		}
		
		//display different input dialog by case
		switch (input_type) {
		case 0: { //input
			final JTextField attribute_input = new JTextField(default_value);
			CaretListener attributeInputAction = new CaretListener() {
				public void caretUpdate(CaretEvent e) {
					if (attribute_input.getText().length() > 0) {
						// if a value has been selected
						attribute_string_array[attribute_index] = attribute_start + attribute_input.getText()
						+ attribute_end;
					} else {
						attribute_string_array[attribute_index] = "";
					}
				}
			};
			attribute_input.setAlignmentX(JTextField.LEFT_ALIGNMENT);
			attribute_input.addCaretListener(attributeInputAction);
			return_component = attribute_input;
			break;
		}
		case 1: { //checkbox
			final JCheckBox attribute_input = new JCheckBox();
			ActionListener attributeInputAction = new ActionListener() {
				public void actionPerformed(ActionEvent e) {
					if (attribute_input.isSelected()) {
						// if a value has been selected
						attribute_string_array[attribute_index] = attribute_start + attribute_name + attribute_end;
					} else {
						attribute_string_array[attribute_index] = "";
					}
				}
			};
			attribute_input.setAlignmentX(JCheckBox.LEFT_ALIGNMENT);
			attribute_input.addActionListener(attributeInputAction);
			return_component = attribute_input;
			break;
		}
		case 2: { //textarea
			final JTextArea attribute_input = new JTextArea(default_value);
			CaretListener attributeInputAction = new CaretListener() {
				public void caretUpdate(CaretEvent e) {
					// if a value has been selected
					if (attribute_input.getText().length() > 0) {
						attribute_string_array[attribute_index] = attribute_start + attribute_input.getText()
						+ attribute_end;
					} else {
						attribute_string_array[attribute_index] = "";
					}
				}
			};
			attribute_input.setRows(2);
			attribute_input.setAlignmentX(JTextArea.LEFT_ALIGNMENT);
			attribute_input.setLineWrap(true);
			attribute_input.addCaretListener(attributeInputAction);
			return_component = attribute_input;
			break;
		}
		case 3: { //select from predefined list of values
			final JComboBox attribute_input = new JComboBox();
			ActionListener attributeInputAction = new ActionListener() {
				public void actionPerformed(ActionEvent e) {
					int i = attribute_input.getSelectedIndex(); //selected tag
					// index
					if (i > 0) { //if a value has been selected
						attribute_string_array[attribute_index] = attribute_start + attribute_input.getItemAt(i)
						+ attribute_end;
					} else {
						String new_value = (String) attribute_input.getEditor().getItem();
						if (new_value != null) {
							attribute_string_array[attribute_index] = attribute_start + new_value + attribute_end;
						} else {
							attribute_string_array[attribute_index] = "";
						}
					}
				}
			};
			String[] attrib_items = attribute_values.split(":");
			int num_attrib_items = attrib_items.length;
			attribute_input.addItem(""); //add void item
			attribute_input.setEditable(true);
			int k;
			//for each attribute item
			for (k = 0; k < num_attrib_items; k++) {
				attribute_input.addItem(attrib_items[k]);
			}
			//set default value
			attribute_input.setSelectedItem(default_value);
			attribute_input.setAlignmentX(JComboBox.LEFT_ALIGNMENT);
			attribute_input.addActionListener(attributeInputAction);
			return_component = attribute_input;
			break;
		}
		case 4: {
			//if attribute description contain the "color word",
			//then display get color button
			final JButton getColorButton = new JButton(attribute_icon);
			ActionListener getColorAction = new ActionListener() {
				public void actionPerformed(ActionEvent e) {
					// Bring up a color chooser
					Color c = JColorChooser.showDialog(getColorButton, configuration.getTMXresources().getString(
							"R_COLOR_CHOOSER", "color chooser"), Color.WHITE);
					if (c != null) {
						// set color on input dialog
						parent_component.setText(HTMLColors.getHTMLColor(c));
					}
				}
			};
			getColorButton.setMargin(new Insets(0, 0, 0, 0));
			getColorButton.addActionListener(getColorAction);
			getColorButton.setToolTipText(configuration.getTMXresources().getString("R_GET_COLOR", "get color"));
			return_component = getColorButton;
			break;
		}
		default: {
			return_component = null;
		}
		} //close switch
		return return_component;
	}
	
	/**
	 * create a formatted label with tooltip
	 * 
	 * @param name
	 *        name of label
	 * @param description
	 *        text for tooltip
	 * @return label
	 */
	private JLabel input_label_component(String name, String description) {
		JLabel attrib_label = new JLabel();
		attrib_label.setText(name);
		attrib_label.setAlignmentX(JLabel.LEFT_ALIGNMENT);
		attrib_label.setToolTipText(description);
		return attrib_label;
	}
	
	/**
	 * Create a custom TAG button
	 * 
	 * @param name
	 *        String button name (used if icon is null)
	 * @param description
	 *        String description for tooltip
	 * @param icon
	 *        String image filename (must be located inside the buttons_images_path)
	 * @param intag
	 *        String the opening tag (e.g.: &lt;h1&gt;)
	 * @param outtag
	 *        String the closing tag (e.g.: &lt;/h1&gt;)
	 * @param action_id
	 *        int ID of buttonsActionListener
	 * @param keystroke
	 *        int keyboard key for KeyboardAction
	 * @param keymodifier
	 *        int keyboard modifier key for KeyboardAction
	 * @return JButton
	 */
	private JButton customButton(String name, String description, String icon, String intag, String outtag,
			int action_id, int keystroke, int keymodifier) {
		
		final String in_tag = intag;
		final String out_tag = outtag;
		// number of Actions on the buttonActionListener List
		final int action_limit = 4;
		
		// if necessary load string definitions from TMX
		if ((name.length() > 1) && (name.substring(0, 2) == "R_")) {
			name = configuration.getTMXresources().getString(name, name);
		}
		if ((description.length() > 1) && (description.substring(0, 2) == "R_")) {
			description = configuration.getTMXresources().getString(description, description);
		}
		
		JButton custom_button = new JButton(); // create void button
		ImageIcon buttonImage = getImageIcon(buttons_images_path + icon);
		
		if ((action_id >= 0) && (action_id <= action_limit)) {
			// Action Values
			Action customTagAction = (Action) buttonActionListener.get(action_id);
			if (buttonImage != null) {
				customTagAction.putValue(Action.SMALL_ICON, buttonImage);
				custom_button.setAction(customTagAction);
				custom_button.setText("");
			} else {
				custom_button.setAction(customTagAction);
				// set text if image is not available
				custom_button.setText(name);
			}
			if (keystroke > 0) {
				custom_button.registerKeyboardAction(customTagAction, KeyStroke.getKeyStroke(keystroke, keymodifier),
						JComponent.WHEN_IN_FOCUSED_WINDOW);
			}
		} else {
			// ActionListener Values
			ActionListener customTagAction;
			if ((action_id > action_limit) && (action_id < buttonActionListener.size())) {
				customTagAction = (ActionListener) buttonActionListener.get(action_id);
			} else {
				customTagAction = new ActionListener() {
					public void actionPerformed(ActionEvent e) {
						//custom tag insertion
						insertTag(in_tag, out_tag);
					}
				};
			}
			// set button action
			custom_button.addActionListener(customTagAction);
			if (keystroke > 0) {
				custom_button.registerKeyboardAction(customTagAction, KeyStroke.getKeyStroke(keystroke, keymodifier),
						JComponent.WHEN_IN_FOCUSED_WINDOW);
			}
			if (buttonImage != null) {
				custom_button.setIcon(buttonImage); //set button image
			} else {
				// set text if image is not available
				custom_button.setText(name);
			}
		}
		
		custom_button.setMargin(new Insets(0, 0, 0, 0)); //set margins
		custom_button.setToolTipText(description); // set tooltip
		
		return custom_button;
	}
	
}
