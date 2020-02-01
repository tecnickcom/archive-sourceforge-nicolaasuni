package com.tecnick.junitconv;

import java.applet.Applet;
import java.awt.BorderLayout;
import java.awt.Button;
import java.awt.Choice;
import java.awt.Color;
import java.awt.Component;
import java.awt.FlowLayout;
import java.awt.Font;
import java.awt.GridBagConstraints;
import java.awt.GridBagLayout;
import java.awt.Label;
import java.awt.Panel;
import java.awt.TextArea;
import java.awt.TextField;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.event.ItemEvent;
import java.awt.event.TextEvent;
import java.awt.event.TextListener;
import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.io.UnsupportedEncodingException;
import java.net.MalformedURLException;
import java.net.URL;

/**
 * Title: JRelaxTimer<br>
 * Description: 
 * JUnitConv is an universal Units of Measure Converter, it converts numbers 
 * from one unit of measure to another.
 * Built as a Java Applet, JUnitConv is platform-independent and highly-configurable, 
 * it supports an unlimited number of Units Categories, Units of Measure and Multiplier 
 * Prefixes that could be customized using external text files. You could setup your 
 * own data files using your preferred spoken language, units categories, units 
 * definitions and multiplier prefixes. The default configuration data files contains 
 * 580 basic units of measure definitions divided in 31 categories and 27 multiplier 
 * prefixes for a total of 15660 composed units.
 *
 * <br/>Copyright (c) 2002-2006 Tecnick.com S.r.l (www.tecnick.com) Via Ugo
 * Foscolo n.19 - 09045 Quartu Sant'Elena (CA) - ITALY - www.tecnick.com -
 * info@tecnick.com <br/>
 * Project homepage: <a href="http://junitconv.sourceforge.net" target="_blank">http://junitconv.sourceforge.net</a><br/>
 * License: http://www.gnu.org/copyleft/gpl.html GPL 2
 * 
 * @author Nicola Asuni [www.tecnick.com].
 * @version 1.0.004
 */
public class JUnitConv extends Applet {
	
	/**
	 * serialVersionUID
	 */
	private static final long serialVersionUID = -2118350734564102088L;
	
	/**
	 * Software version
	 */
	private static final String JUNITCONV_VERSION = "1.0.004";
	
	Panel panel;
	
	final Applet a = this; // only final variables can be used in inner anonymous classes
	
	// applet parameters <param>
	
	
	/**
	 * Background color.
	 */
	Color p_background_color;
	
	/**
	 * Foreground color.
	 */
	Color p_foreground_color;
	
	/**
	 * Default font.
	 */
	Font default_font;
	
	/**
	 * Font for labels.
	 */
	Font label_font;
	
	/**
	 * Font for title.
	 */
	Font title_font;
	
	/**
	 * Font for applet description.
	 */
	Font group_font;
	
	/**
	 * Font name.
	 */
	String p_font; // font name
	
	/**
	 * Font style (PLAIN, BOLD, ITALIC).
	 */
	int p_font_style;
	
	/**
	 * Font size.
	 */
	int p_font_size;
	
	/**
	 * Charset encoding.
	 */
	String p_encoding;
	
	/**
	 * HTML page encoding.
	 */
	String p_page_encoding;
	
	/**
	 * Default frame target where to open author homepage
	 */
	private String p_target = "_blank";
	
	/**
	 * Author's homepage.
	 */
	private String p_link = "http://www.tecnick.com";
	
	/**
	 * Label for the link button.
	 */
	private String p_copyright = "Author: Nicola Asuni © 2002-2006 Tecnick.com S.r.l. (www.tecnick.com)";
	
	/**
	 * URL of text data file containing labels definitions.
	 */
	String p_labels_data_file;
	
	/**
	 * URL of text data file containing multiplier definitions.
	 */
	String p_multiplier_data_file;
	
	/**
	 * URL of text data file containing units categories data.
	 */
	String p_category_data_file;
	
	/**
	 * URL of text data file containing units data.
	 */
	String p_unit_data_file;
	
	/**
	 * Array of string labels.
	 */	
	String[] p_label;
	
	/**
	 * Array of category names.
	 */
	String[] p_category_name;
	
	/**
	 * Array of Multiple/Submultiple names.
	 */
	String[] p_multiplier_name;
	
	/**
	 * Array of Multiple/Submultiple values.
	 */
	Double[] p_multiplier_value;
	
	/**
	 * Array of Multiple/Submultiple descriptions.
	 */
	String[] p_multiplier_description;
	
	/**
	 * Array of category ID (link to category table: p_category_id).
	 */
	Integer[] p_unit_category_id;
	
	/**
	 * Array of unit of measure symbols.
	 */
	String[] p_unit_symbol;
	
	/**
	 * Array of unit of measure names.
	 */
	String[] p_unit_name;
	
	/**
	 * Array of unit of measure descriptions.
	 */
	String[] p_unit_description;
	
	/**
	 * Array of unit of measure conversion scale factors.
	 */
	Double[] p_unit_scale;
	
	/**
	 * Array of unit of measure conversion offsets.
	 */
	Double[] p_unit_offset;
	
	/**
	 * Array of powers to apply to unit multipliers.
	 */
	Double[] p_unit_power;
	
	/**
	 * Current category.
	 */
	private int current_category = 0;
	
	/**
	 * Current unit offset.
	 */
	private int current_unit_offset = 0; //offset for unit index on selectors
	
	/**
	 * Current input multiplier.
	 */
	private int current_in_multiplier = 0;
	
	/**
	 * Current output multiplier.
	 */
	private int current_out_multiplier = 0;
	
	/**
	 * Current input unit.
	 */
	private int current_in_unit = 0;
	
	/**
	 * Current output unit.
	 */
	private int current_out_unit = 0;
	
	/**
	 * Current precision (number of decimals).
	 * default value = 3
	 */
	private int current_precision = 3;
	
	/**
	 * Current input value.
	 */
	private Double current_in_value = new Double(1);
	
	/**
	 * Current output value.
	 */
	private Double current_out_value = new Double(1);
	
	/**
	 * Selector for units category.
	 */
	private Choice category_selector = new Choice();
	
	/**
	 * Selector for required precision.
	 */
	private Choice precision_selector = new Choice();
	
	/**
	 * Selector for input unit type.
	 */
	private Choice in_unit_selector = new Choice();
	
	/**
	 * Selector for output unit type.
	 */
	private Choice out_unit_selector = new Choice();
	
	/**
	 * Selector for input multiplier (SI prefixes).
	 */
	private Choice in_multiplier_selector = new Choice();
	
	/**
	 * Selector for output multiplier (SI prefixes).
	 */
	private Choice out_multiplier_selector = new Choice();
	
	/**
	 * Input value.
	 */
	private TextField in_value = new TextField(current_in_value.toString(), 10);
	
	/**
	 * Output value.
	 */
	private TextField out_value = new TextField(current_in_value.toString(), 10);
	
	/**
	 * Number of rows for textarea (the area that display unit-of-measure definition).
	 * Default = 2.
	 */
	private int textarea_rows = 2;
	
	/**
	 * Number of columns for textarea (the area that display unit-of-measure definition).
	 * Default = 50.
	 */
	private int textarea_cols = 50;
	
	/**
	 * Input unit of measure description (definition).
	 */
	private TextArea in_description = new TextArea("", textarea_rows, textarea_cols, TextArea.SCROLLBARS_VERTICAL_ONLY);
	
	/**
	 * Output unit of measure description (definition).
	 */
	private TextArea out_description = new TextArea("", textarea_rows, textarea_cols, TextArea.SCROLLBARS_VERTICAL_ONLY);
	
//	-----------------------------------------------------------------------------
	
	/**
	 * When a menu item is clicked, jump to corresponding URL
	 */
	ActionListener jumpToUrl = new ActionListener() {
		public void actionPerformed(ActionEvent e) {
			a.getAppletContext().showDocument(setURL(p_link), p_target); // load the URL on the target browser window
		}
	};
	
	
	/**
	 * Computes full, canonical URL from a relative specification.
	 * @param link string containing URL
	 * @return canonical URL
	 */
	private URL setURL(String link) {
		if (link.length() <= 0) {
			return null;
		}
		URL url = null;
		try {
			url = new URL(getDocumentBase(), link);
		}
		catch (MalformedURLException e) {
			System.out.println("ERROR - Malformed URL: " + link);
		}
		return url;
	}	
	
	/**
	 * Convert string to specified encoding.
	 * @param original original string
	 * @param encoding_in input encoding table
	 * @param encoding_out output encoding table
	 * @return encoded string
	 */
	private String getEncodedString(String original, String encoding_in, String encoding_out) {
		String encoded_string;
		if (encoding_in.compareTo(encoding_out) != 0) {
			byte[] encoded_bytes;
			try {
				encoded_bytes = original.getBytes(encoding_in);
			}
			catch (UnsupportedEncodingException e) {
				System.out.println("Unsupported Charset: " + encoding_in);
				return original;
			}
			try {
				encoded_string = new String(encoded_bytes, encoding_out);
				return encoded_string;
			}
			catch (UnsupportedEncodingException e) {
				//e.printStackTrace();
				System.out.println("Unsupported Charset: " + encoding_out);
				return original;
			}
		}
		return original;
	}
	
	/**
	 * return the int associated to font style
	 * @param stylename name of style
	 * @return style int code
	 */
	private int getFontStyleCode(String stylename) {
		String sname = stylename.toUpperCase().trim();
		int return_value = 0;
		try {
			if (sname.indexOf("PLAIN") >= 0) {return_value = Font.PLAIN;}
			if (sname.indexOf("BOLD") >= 0) {return_value += Font.BOLD;}
			if (sname.indexOf("ITALIC") >= 0) {return_value += Font.ITALIC;}
		} catch (NullPointerException ne) {
			return Font.PLAIN;
		}
		return return_value; //default return
	}
	
	/**
	 * Get applet parameter value, return default if void.
	 * @param key name of parameter to read
	 * @param def default value
	 * @return parameter value or default
	 */
	private String getParameter(String key, String def) {
		String param_value = getParameter(key);
		if ( (param_value != null) && (param_value.length() > 0)) {
			return param_value;
		}
		return def;
	}
	
	/**
	 * Return "def" if "str" is null or empty.
	 * @param str value to return if not null
	 * @param def default value to return
	 * @return def or str by case
	 */
	private String getDefaultValue(String str, String def) {
		if ( (str != null) && (str.length() > 0)) {
			return str;
		}
		return def;
	}
	
	
	/**
	 * Get the applet parameters from HTML page.
	 */
	void getParameters() {
		try {
			p_background_color = new Color(Integer.parseInt(this.getParameter("background_color", "CCCCCC"),16));
			p_foreground_color = new Color(Integer.parseInt(this.getParameter("foreground_color", "000000"),16));
			p_font = this.getParameter("font", "Helvetica, Verdana, Arial");
			p_font_style = getFontStyleCode(this.getParameter("font_style", "PLAIN")); // PLAIN, BOLD, ITALIC
			p_font_size = Integer.parseInt(this.getParameter("font_size", String.valueOf(a.getFont().getSize())));
			p_encoding = this.getParameter("encoding", "utf-8");
			p_page_encoding = this.getParameter("page_encoding", "utf-8");
			p_labels_data_file = this.getParameter("labels_data_file", "eng/labels.txt");
			p_multiplier_data_file = this.getParameter("multiplier_data_file", "eng/muldata.txt");
			p_category_data_file = this.getParameter("categories_data_file", "eng/catdata.txt");
			p_unit_data_file = this.getParameter("units_data_file", "eng/unitdata.txt");
		}
		catch(Exception e) {
			e.printStackTrace();
		}
		readDataFile(0, p_multiplier_data_file); //read data from external text file
		readDataFile(1, p_category_data_file); //read data from external text file
		readDataFile(2, p_unit_data_file); //read data from external text file
		readDataFile(3, p_labels_data_file); //read data from external text file
	}
	
	/**
	 * set arrays size for unit categories
	 * @param i size of array
	 */
	private void setMultipliersArraySize(int i) {
		p_multiplier_name = new String[i];
		p_multiplier_value = new Double[i];
		p_multiplier_description = new String[i];
	}
	
	/**
	 * set arrays size for unit categories
	 * @param i size of array
	 */
	private void setCategoriesArraySize(int i) {
		p_category_name = new String[i];
	}
	
	/**
	 * set arrays size for units
	 * @param i size of array
	 */
	private void setUnitsArraySize(int i) {
		p_unit_category_id = new Integer[i];
		p_unit_symbol = new String[i];
		p_unit_name = new String[i];
		p_unit_description = new String[i];
		p_unit_scale = new Double[i];
		p_unit_offset = new Double[i];
		p_unit_power = new Double[i];
	}
	
	/**
	 * Split a string in array of predefined size
	 * @param input_string string to split
	 * @param sep_ch separator character
	 * @param size max elements to retrieve, remaining elements will be filled with empty string
	 * @return splitted_array of strings
	 */
	private String[] splitData(String input_string, char sep_ch, int size) {
		String str1 = new String(); // temp var to contain found strings
		String splitted_array[] = new String[size]; // array of splitted string to return
		int element_num = 0; //number of found elements
		// analize string char by char
		for(int i=0; i<input_string.length(); i++) {
			if(input_string.charAt(i) == sep_ch) { //separator found
				splitted_array[element_num] = str1; //put string to array
				str1 = new String(); //reinitialize variable
				element_num++; //count strings
				if (element_num >= size) {
					break; //quit if limit is reached
				}
			}
			else {
				str1 += input_string.charAt(i);
			}
		}
		//get last element
		if (element_num < size) {
			splitted_array[element_num] = str1; //put string to vector
			element_num++;
		}
		//fill remaining values with empty string
		for(int i=element_num; i<size; i++) {
			splitted_array[i] = "";
		}
		return splitted_array;
	}
	
	/**
	 * Read menu items data from external text file
	 * "\n" separate items
	 * "\t" separate values
	 * @param filetype 0=multiplier file, 1= category file, 2=units file
	 * @param filename the text file containing menu data
	 */
	public void readDataFile(int filetype, String filename) {
		int nfields=7; //number of data fields
		int i = 0; //temp elements counter
		int num_elements = 0; //number of items (lines)
		String dataline;
		String[] elementdata;
		try {
			URL filesource = setURL(filename);
			//open data file
			BufferedReader in = new BufferedReader(new InputStreamReader(filesource.openStream()));
			//count elements
			while(null != (dataline = in.readLine())) {
				i++;
			}
			in.close();
			num_elements = i;
			//num_elements = i+1;
			//set arrays size by case
			if (filetype == 0) {
				setMultipliersArraySize(num_elements);
				nfields = 3;
			}
			else if (filetype == 1) {
				setCategoriesArraySize(num_elements);
				nfields = 1;
			}
			else if (filetype == 2) {
				setUnitsArraySize(num_elements);
				nfields = 7;
			}
			else if (filetype == 3) {
				p_label = new String[i];
				nfields = 1;
			}
			
			i = 0;
			in = new BufferedReader(new InputStreamReader(filesource.openStream()));
			//read lines (each line is one menu element)
			while(null != (dataline = in.readLine())) {
				//get element data array
				elementdata = splitData(dataline, '\t', nfields);
				//assign data
				if (filetype == 0) { //multipliers file
					p_multiplier_name[i] = getEncodedString(getDefaultValue(elementdata[0], ""), p_page_encoding, p_encoding);
					p_multiplier_description[i] = getEncodedString(getDefaultValue(elementdata[1], ""), p_page_encoding, p_encoding);
					p_multiplier_value[i] = parseNumber(getDefaultValue(elementdata[2], "1"));
				}
				else if (filetype == 1) { //category file
					p_category_name[i] = getEncodedString(getDefaultValue(elementdata[0], ""), p_page_encoding, p_encoding);
				}
				else if (filetype == 2) { //units file
					p_unit_category_id[i] = new Integer(elementdata[0]);
					p_unit_symbol[i] = getEncodedString(getDefaultValue(elementdata[1], ""), p_page_encoding, p_encoding);
					p_unit_name[i] = getEncodedString(getDefaultValue(elementdata[2], ""), p_page_encoding, p_encoding);
					p_unit_scale[i] = parseNumber(getDefaultValue(elementdata[3], "1"));
					p_unit_offset[i] = parseNumber(getDefaultValue(elementdata[4], "0"));
					p_unit_power[i] = parseNumber(getDefaultValue(elementdata[5], "1"));
					p_unit_description[i] = getEncodedString(getDefaultValue(elementdata[6], ""), p_page_encoding, p_encoding);
				}
				else if (filetype == 3) { //labels file
					p_label[i] = getEncodedString(getDefaultValue(elementdata[0], ""), p_page_encoding, p_encoding);
				}
				i++;
			}
			in.close();
		}
		catch(Exception e) {
			e.printStackTrace();
		}
	}
	
	
	/**
	 * Applet constructor (void)
	 */
	public JUnitConv() {
	}
	
	/**
	 * Initialize the applet
	 */
	public void init() {
		
		//display some info on console
		System.out.println(" ");
		System.out.println("JUnitConv " + JUNITCONV_VERSION);
		System.out.println("http://junitconv.sourceforge.net");
		System.out.println("Author: Nicola Asuni");
		System.out.println("Copyright (c) 2002-2006 Tecnick.com s.r.l. - www.tecnick.com");
		System.out.println("Open Source License: GPL 2");
		System.out.println(" ");
		
		add(new Label("Loading...")); //display loading message
		validate();
		
		getParameters(); //get applet parameters (menu data)
		
		removeAll(); //remove loading message
		
		//set aesthetic things
		setBackground(p_background_color);
		setForeground(p_foreground_color);
		default_font = new Font(p_font, p_font_style, p_font_size);
		setFont(default_font);
		label_font = new Font(p_font, Font.PLAIN, p_font_size-1);
		title_font = new Font(p_font, Font.BOLD, p_font_size+2);
		group_font = new Font(p_font, Font.PLAIN, p_font_size);
		
		// fill category selector
		for (int i=0; i<p_category_name.length; i++) { // iterate through categories
			if (p_category_name[i] != null) {
				category_selector.add(p_category_name[i]);
			}
		}
		
		// fill precision selector
		for (int i=0; i<=10; i++) { // iterate
			precision_selector.add(String.valueOf(i));
		}
		
		// fill multiplier selector
		for (int i=0; i<p_multiplier_name.length; i++) { // iterate through multipliers
			String multiplier_name = "";
			if (p_multiplier_name[i].length() > 0) {multiplier_name = ""+p_multiplier_name[i]+" ";}
			if (p_multiplier_description[i].length() > 0) {multiplier_name += "("+p_multiplier_description[i]+")";}
			if (multiplier_name != null) {
				in_multiplier_selector.add(multiplier_name);
				out_multiplier_selector.add(multiplier_name);
			}
		}
		
		setUnitCategory(); //initialize components values
		
		try {
			jbInit();
		}
		catch(Exception e) {
			e.printStackTrace();
		}
		
		validate();
		setUnitCategory();
		
	} // end of init
	
	
	/**
	 * Fill units selectors filtering by category
	 * @param category selected units category (filter)
	 */
	private void fillUnitsSelector(int category) {
		//fill units selector
		in_unit_selector.removeAll(); // clear previous data
		out_unit_selector.removeAll();
		for (int i=0; i<p_unit_category_id.length; i++) { // iterate through units
			if (p_unit_category_id[i].intValue() == current_category) {
				current_unit_offset = i;
				String unit_name = "";
				if (p_unit_symbol[i].length() > 0) {unit_name = ""+p_unit_symbol[i]+"";}
				if (p_unit_name[i].length() > 0) {unit_name += " ("+p_unit_name[i]+")";}
				if (unit_name != null) {
					in_unit_selector.add(unit_name);
					out_unit_selector.add(unit_name);
				}
			}
		}
		current_unit_offset -= in_unit_selector.getItemCount() - 1;
	}
	
	
	/**
	 * Component initialization
	 */
	private void jbInit() throws Exception {
		
		category_selector.addItemListener(new java.awt.event.ItemListener() {
			public void itemStateChanged(ItemEvent e) {
				category_selector_itemStateChanged(e);
			}
		});
		
		precision_selector.addItemListener(new java.awt.event.ItemListener() {
			public void itemStateChanged(ItemEvent e) {
				precision_selector_itemStateChanged(e);
			}
		});
		
		in_multiplier_selector.addItemListener(new java.awt.event.ItemListener() {
			public void itemStateChanged(ItemEvent e) {
				in_multiplier_selector_itemStateChanged(e);
			}
		});
		
		out_multiplier_selector.addItemListener(new java.awt.event.ItemListener() {
			public void itemStateChanged(ItemEvent e) {
				out_multiplier_selector_itemStateChanged(e);
			}
		});
		
		in_unit_selector.addItemListener(new java.awt.event.ItemListener() {
			public void itemStateChanged(ItemEvent e) {
				in_unit_selector_itemStateChanged(e);
			}
		});
		
		out_unit_selector.addItemListener(new java.awt.event.ItemListener() {
			public void itemStateChanged(ItemEvent e) {
				out_unit_selector_itemStateChanged(e);
			}
		});
		
		in_value.addTextListener(new TextListener() {
			public void textValueChanged(TextEvent e) {
				in_value_itemStateChanged(e);
			}
		});
		
		
		GridBagLayout gridbag = new GridBagLayout();
		GridBagConstraints c = new GridBagConstraints();
		c.fill = GridBagConstraints.HORIZONTAL;
		c.weightx = 1.0;
		c.gridwidth = GridBagConstraints.REMAINDER;
		
		
		//selection panel
		Panel p_selection = new Panel();
		p_selection.setLayout(new FlowLayout(FlowLayout.LEFT, 0, 0));
		p_selection.add(addLabel(category_selector, p_label[3]), null);
		p_selection.add(addLabel(precision_selector, p_label[4]), null);
		
		//--- INPUT ----------------------------------------------
		//input data
		Panel p_input_sel = new Panel();
		p_input_sel.setLayout(new FlowLayout(FlowLayout.LEFT, 0, 0));
		p_input_sel.add(addLabel(in_value, p_label[5]), null);
		p_input_sel.add(addLabel(in_multiplier_selector, p_label[6]), null);
		p_input_sel.add(addLabel(in_unit_selector, p_label[7]), null);
		
		//input description
		in_description.setEditable(false);
		
		GroupBox p_input = new GroupBox(p_label[1], 2, 2, 2, group_font);
		p_input.setLayout(gridbag);
		p_input.add(p_input_sel, c);
		p_input.add(addLabel(in_description, p_label[8]), c);
		//--------------------------------------------------------
		
		
		//--- OUTPUT ---------------------------------------------
		//output data
		Panel p_output_sel = new Panel();
		p_output_sel.setLayout(new FlowLayout(FlowLayout.LEFT, 0, 0));
		out_value.setEditable(false); //result value is not editable
		p_output_sel.add(addLabel(out_value, p_label[5]), null);
		p_output_sel.add(addLabel(out_multiplier_selector, p_label[6]), null);
		p_output_sel.add(addLabel(out_unit_selector, p_label[7]), null);
		
		//output description
		out_description.setEditable(false);
		
		GroupBox p_output = new GroupBox(p_label[2], 2, 2, 2, group_font);
		p_output.setLayout(gridbag);
		p_output.add(p_output_sel, c);
		p_output.add(addLabel(out_description, p_label[8]), c);
		//--------------------------------------------------------
		
		Button linklabel = new Button(p_copyright);
		linklabel.addActionListener(jumpToUrl);
		//--------------------------------------------------------
		
		//applet layout
		Panel p_main = new Panel();
		p_main.setLayout(gridbag);
		//p_main.add(title, c);
		p_main.add(p_selection, c);
		p_main.add(p_input, c); //add void line
		p_main.add(p_output, c); //add void line
		c.ipady = 2;
		p_main.add(linklabel, c);
		
		GroupBox p_main_group = new GroupBox("JUnitConv "+JUNITCONV_VERSION+" - "+p_label[0], 2, 2, 2, title_font);
		p_main_group.setLayout(new FlowLayout(FlowLayout.LEFT, 0, 0));
		p_main_group.add(p_main, null);
		
		//--------------------------------------------------------
		
		a.setLayout(gridbag);
		c.fill = GridBagConstraints.NONE;
		c.weightx = 0;
		a.add(p_main_group, c);
		
	}
	
	
	/**
	 * add label to a component
	 * @param comp component
	 * @param label label string
	 * @return labeled component
	 */
	private Component addLabel(Component comp, String label) {
		Panel temp_panel = new Panel();
		temp_panel.setLayout(new BorderLayout(0,0));
		Label thislabel = new Label(label);
		thislabel.setFont(label_font);
		temp_panel.add(thislabel, BorderLayout.NORTH);
		temp_panel.add(comp, BorderLayout.CENTER);
		return temp_panel;
	}
	
	
	/**
	 * Start the applet
	 */
	public void start() {
	}
	
	/**
	 * Stop the applet
	 */
	public void stop() {
	}
	
	/**
	 * Destroy the applet
	 */
	public void destroy() {
	}
	
	/**
	 * Get Applet information.
	 * @return applet info
	 */
	public String getAppletInfo() {
		String message = "JUnitConv " + JUNITCONV_VERSION + "\n";
		message += "http://junitconv.sourceforge.net\n";
		message += "Author: Nicola Asuni\n";
		message += "Copyright (c) 2002-2006 Tecnick.com s.r.l. - www.tecnick.com\n";
		message += "Open Source License: GPL 2\n";
		return message;
	}
	
//	-----------------------------------------------------------------------------
//	functions to handle selectors events:
	
	/**
	 * change units on units selctors by selected category
	 * @param e event
	 */
	private void category_selector_itemStateChanged(ItemEvent e) {
		setUnitCategory();
	}
	
	/**
	 * change result precision (number of decimals)
	 * @param e event
	 */
	private void precision_selector_itemStateChanged(ItemEvent e) {
		getSelectedIndexes();
	}
	
	/**
	 * get selected input multiplier index
	 * @param e event
	 */
	private void in_multiplier_selector_itemStateChanged(ItemEvent e) {
		getSelectedIndexes();
	}
	
	/**
	 * get selected output multiplier index
	 * @param e event
	 */
	private void out_multiplier_selector_itemStateChanged(ItemEvent e) {
		getSelectedIndexes();
	}
	
	/**
	 * get selected input unit index
	 * @param e event
	 */
	private void in_unit_selector_itemStateChanged(ItemEvent e) {
		getSelectedIndexes();
	}
	
	/**
	 * get selected output unit index
	 * @param e event
	 */
	private void out_unit_selector_itemStateChanged(ItemEvent e) {
		getSelectedIndexes();
	}
	
	/**
	 * get input value
	 * @param e event
	 */
	private void in_value_itemStateChanged(TextEvent e) {
		getSelectedIndexes();
	}
	
	
	/**
	 * get selected category and set other parameters
	 */
	private void setUnitCategory() {
		current_category = category_selector.getSelectedIndex();
		if (current_category < 0) { //category has not been selected
			current_category = 0; //select firt item
		}
		fillUnitsSelector(current_category);
		in_multiplier_selector.select(current_in_multiplier);
		out_multiplier_selector.select(current_out_multiplier);
		in_unit_selector.select(0);
		out_unit_selector.select(0);
		precision_selector.select(current_precision);
		getSelectedIndexes();
	}
	
	
	/**
	 * get current selected index on selectors
	 */
	private void getSelectedIndexes() {
		
		current_in_value = Double.valueOf(in_value.getText());
		current_in_multiplier = in_multiplier_selector.getSelectedIndex();
		current_out_multiplier = out_multiplier_selector.getSelectedIndex();
		current_in_unit = current_unit_offset + in_unit_selector.getSelectedIndex();
		current_out_unit = current_unit_offset + out_unit_selector.getSelectedIndex();
		current_precision = precision_selector.getSelectedIndex();
		
		//make conversion here ....
		Double intempvalue = new Double( current_in_value.doubleValue() * p_unit_scale[current_in_unit].doubleValue() * Math.pow(p_multiplier_value[current_in_multiplier].doubleValue(), p_unit_power[current_in_unit].doubleValue()));
		Double outtempvalue = new Double( p_unit_scale[current_out_unit].doubleValue() );
		Double outvalue = new Double( ((intempvalue.doubleValue() + p_unit_offset[current_in_unit].doubleValue() - p_unit_offset[current_out_unit].doubleValue()) / (outtempvalue.doubleValue() * Math.pow(p_multiplier_value[current_out_multiplier].doubleValue(), p_unit_power[current_out_unit].doubleValue()))) );
		Double roundedoutvalue = new Double(roundNumber(outvalue.doubleValue(), current_precision));
		out_value.setText(roundedoutvalue.toString());
		
		//set input description
		String indesc = ""+in_value.getText()+" "+p_multiplier_name[current_in_multiplier]+""+p_unit_symbol[current_in_unit]+"";
		if (p_multiplier_value[current_in_multiplier].doubleValue() != 1) {
			Double tempvalue = new Double(current_in_value.doubleValue() * Math.pow(p_multiplier_value[current_in_multiplier].doubleValue(), p_unit_power[current_in_unit].doubleValue()) );
			indesc += " = "+tempvalue.toString()+" "+p_unit_symbol[current_in_unit]+"";
			indesc += "\n"+p_multiplier_name[current_in_multiplier]+" ("+p_multiplier_description[current_in_multiplier]+") = "+p_multiplier_value[current_in_multiplier]+"";
		}
		indesc += "\n"+p_unit_symbol[current_in_unit]+" ("+p_unit_name[current_in_unit]+"): "+p_unit_description[current_in_unit]+"";
		in_description.setText(indesc);
		
		//set ouput description
		String outdesc = ""+out_value.getText()+" "+p_multiplier_name[current_out_multiplier]+""+p_unit_symbol[current_out_unit]+"";
		if (p_multiplier_value[current_out_multiplier].doubleValue() != 1) {
			Double tempvalue = new Double(current_out_value.doubleValue() * Math.pow(p_multiplier_value[current_out_multiplier].doubleValue(), p_unit_power[current_out_unit].doubleValue()) );
			outdesc += " = "+tempvalue.toString()+" "+p_unit_symbol[current_in_unit]+"";
			outdesc += "\n"+p_multiplier_name[current_out_multiplier]+" ("+p_multiplier_description[current_out_multiplier]+") = "+p_multiplier_value[current_out_multiplier]+"";
		}
		outdesc += "\n"+p_unit_symbol[current_out_unit]+" ("+p_unit_name[current_out_unit]+"): "+p_unit_description[current_out_unit]+"";
		out_description.setText(outdesc);
	}
	
	
	/**
	 * return a rounded number
	 * @param in_number numer to round
	 * @param precision max decimal numbers
	 * @return rounded number
	 */
	private double roundNumber(double in_number, int precision) {
		double round_precision = Math.pow(10, (double) precision);
		return Math.round(in_number * round_precision) / round_precision;
	}
	
	
	/**
	 * simple number parser (allows to use math operators operators: +,-,*,/,^,P=PI,X=exp)
	 * operator precedence: P X * / + - ^
	 * @param num string to parse
	 * @return Double parsed number
	 */
	private Double parseNumber(String num) {
		Double tempnum = new Double(0);
		int opos; //operator position
		if ((num == null) || (num.length() < 1) ) {
			return tempnum;
		}
		
		//replace constants with their value
		while (num.indexOf("P") >= 0) { //PI constant
			String[] numparts = splitData(num, 'P', 2);
			num = numparts[0]+String.valueOf(Math.PI)+numparts[1];
		}
		while (num.indexOf("X") >= 0) { //e constant
			String[] numparts = splitData(num, 'X', 2);
			num = numparts[0]+String.valueOf(Math.E)+numparts[1];
		}
		
		if (num.indexOf("^") >= 0) { //allows to specify powers (e.g.: 2^10)
			String[] numparts = splitData(num, '^', 2);
			tempnum = new Double(Math.pow(parseNumber(numparts[0]).doubleValue(), parseNumber(numparts[1]).doubleValue()));
		}
		else if ( ((opos = num.indexOf("-")) > 0) && (num.charAt(opos-1) != 'E') && (num.charAt(opos-1) != '^')) {
			String[] numparts = splitData(num, '-', 2);
			tempnum = new Double(parseNumber(numparts[0]).doubleValue() - parseNumber(numparts[1]).doubleValue());
		}
		else if ( ((opos = num.indexOf("+")) > 0) && (num.charAt(opos-1) != 'E') && (num.charAt(opos-1) != '^')) {
			String[] numparts = splitData(num, '+', 2);
			tempnum = new Double(parseNumber(numparts[0]).doubleValue() + parseNumber(numparts[1]).doubleValue());
		}
		else if (num.indexOf("/") >= 0) {
			String[] numparts = splitData(num, '/', 2);
			tempnum = new Double(parseNumber(numparts[0]).doubleValue() / parseNumber(numparts[1]).doubleValue());
		}
		else if (num.indexOf("*") >= 0) {
			String[] numparts = splitData(num, '*', 2);
			tempnum = new Double(parseNumber(numparts[0]).doubleValue() * parseNumber(numparts[1]).doubleValue());
		}
		else {
			tempnum = Double.valueOf(num);
		}
		
		return tempnum;
	}
	
	
} //end of applet
//=============================================================================
//END OF FILE
//=============================================================================

