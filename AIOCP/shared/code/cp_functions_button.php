<?php
//============================================================+
// File name   : cp_functions_button.php                       
// Begin       : 2002-01-14                                    
// Last Update : 2003-03-30                                    
//                                                             
// Description : A class to create a graphic buttons with text 
//               require GD library                            
//                                                             
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Tecnick.com LTD
//               Manor Coach House, Church Hill
//               Aldershot, Hants, GU12 4RQ
//               UK
//               www.tecnick.com
//               info@tecnick.com
//
// License: GNU GENERAL PUBLIC LICENSE v.2
//          http://www.gnu.org/copyleft/gpl.html
//============================================================+
// NOTES:
// each button will be generated using 3 grayscale 8bit images 
// as show below:
//
// +------------+------....------+-------------+
// | LEFT IMAGE |  MIDDLE IMAGE  | RIGHT IMAGE |
// | - fixed -  |  - enlarged -  | - fixed -   |
// +------------+------....------+-------------+
//
// the central image is 1 pixel width and coul be expanded as needed
//============================================================+

/**
 * C_button - Create a graphic 8bit palette button with text
 * @author Nicola Asuni (c) Copyright: Tecnick.com LTD
 */
class C_button {
	
	/////////////////////////////////////////////////
	// PUBLIC VARIABLES
	/////////////////////////////////////////////////
	
	/**
	* TTF fonts path
	* @public
	* @type string
	*/
	var $path_fonts = "";
	
	/**
	* Library Images path
	* @public
	* @type string
	*/
	var $path_dynamic_buttons = "";
	
	/**
	* Text of the button
	* @public
	* @type string
	*/
	var $text = "button text";
	
	/**
	* True Type Font (TTF) for text
	* @public
	* @type string
	*/
	var $text_font = "Vera.ttf";
	
	/**
	* Font size
	* @public
	* @type int
	*/
	var $text_size = 12;
	
	/**
	* Text aligment (left | middle | right)
	* @public
	* @type string
	*/
	var $text_alignment = "middle";
	
	/**
	* Button height in pixels (-1 for automatic)
	* @public
	* @type int
	*/
	var $button_height = -1; 
	
	/**
	* Button width in pixels (-1 for automatic)
	* @public
	* @type int
	*/
	var $button_width = -1;
	
	
	/**
	* Button gamma (1=normal | <1 darker | >1 lighter )
	* @public
	* @type float
	*/
	var $button_gamma = 1;
	
	/**
	* Text color (RGB)
	* @public
	* @type array
	*/
	var $text_color = array("red"=>0,"green"=>0,"blue"=>0);
	
	/**
	* Button darkest color (RGB)
	* @public
	* @type array
	*/
	var $button_dark_color = array("red"=>0,"green"=>127,"blue"=>255); //contain the darkest color of the button
	
	/**
	* Button lighter color (RGB)
	* @public
	* @type array
	*/
	var $button_light_color = array("red"=>255,"green"=>255,"blue"=>255); //contain the lightest color of the button
	
	/**
	* set to true if you want use transparent color
	* @public
	* @type boolean
	*/
	var $use_transparent = false;
	
	/**
	* Transparent color (RGB)
	* @public
	* @type array
	*/
	var $transparent_color = array("red"=>255,"green"=>255,"blue"=>255); //contain the tranparent color
	
	/**
	* button padding (space to leave between text and top button margin)
	* @public
	* @type int
	*/
	var $padding = 2; 
	
	/**
	* button horizontal (if false draw vertical button)
	* @public
	* @type boolean
	*/
	var $horizontal = true; 
	
	/**
	* left and right button images width in pixels
	* @public
	* @type int
	*/
	var $corners_width = 8; 
	
	/**
	* if true use chache
	* @public
	* @type boolean
	*/
	var $use_cache = true;
	
	/**
	* if true use chache
	* @public
	* @type boolean
	*/
	var $use_gd2 = true;
	
	// --- END public variables ------------------------------------

	//costants (fixed by PNG images used)
	var $middle_width = 1; //width of central image

	/**
	* Display button
	* @public
	* @returns image
	*/
	function F_display() {
		$file_name = $this->F_cache_file_name(); //get file name
		
		if (($this->use_cache) AND (file_exists($file_name))){ //read file from cache
			$button_image = imagecreatefrompng($file_name);
		}
		else { //build new button
			$button_image = $this->F_create($file_name);
		}
		
		//send headers
		header('Cache-Control: public', TRUE); 
		header("Last-Modified: ".gmdate('D, d M Y H:i:s T', time())."", TRUE); 
		header('Content-Transfer-Encoding: base64', TRUE); 
		header("Accept-Ranges: bytes", TRUE);  
		header("Content-Disposition: inline; filename=".$file_name."", TRUE);
		header("Content-type: image/png; filename=".$file_name."", TRUE);
		
		ImagePng($button_image); //send image to output as PNG
	} //END display function

	/**
	* Create button
	* @public
	* @returns image object
	*/
	function F_create($file_name) {
		//get text information for correct positioning
		if ($this->use_gd2) { //GD2
			putenv("GDFONTPATH=".$this->path_fonts); //set GD library font path for GD2
			$fontfile = substr($this->text_font, 0, -4);
		}
		else {
			$fontfile = $this->path_fonts.$this->text_font;
		}
		
		//claculate text height for correct positioning
		if (isset($this->text) AND (strlen($this->text)>0)) {
			$text_box = imagettfbbox ($this->text_size, 0, $fontfile, "_Åpd");
			$text_max_height = abs($text_box[1]-$text_box[7]);
			$text_box = imagettfbbox ($this->text_size, 0, $fontfile, "a");
			$text_min_height = abs($text_box[1]-$text_box[7]);
			$text_box = imagettfbbox ($this->text_size, 0, $fontfile, $this->text);
			$text_width = abs($text_box[4]-$text_box[6]);
		}
		else {
			$text_max_height = 0;
			$text_min_height = 0;
			$text_width = 0;
		}
		//auto height for button (standard values are 10,15,20,25,30,35,40)
		if($this->button_height==-1) {
			$min_height = $text_max_height + (2 * $this->padding );
			if($min_height<10){$buttonheight = 10;}
			elseif($min_height<15){$buttonheight = 15;}
			elseif($min_height<20){$buttonheight = 20;}
			elseif($min_height<25){$buttonheight = 25;}
			elseif($min_height<30){$buttonheight = 30;}
			elseif($min_height<35){$buttonheight = 35;}
			else $buttonheight = 40;
		}
		else {$buttonheight = $this->button_height;}
		
		//auto width for button
		if($this->button_width==-1) { //auto width for button
			$buttonwidth = $text_width + (2 * $this->corners_width);
		}
		else {$buttonwidth = $this->button_width;}
		
		$text_y = round(($buttonheight + $text_min_height) / 2) ;
		
		//text alignment
		switch($this->text_alignment) {
			case "left": {
				$text_x = $this->corners_width;
				break;
			}
			case "right": {
				$text_x = $buttonwidth - $text_width - $this->corners_width;
				break;
			}
			default:
			case "middle": {
				$text_x = round(($buttonwidth - $text_width)/2);
				break;
			}
		}
		
		// create a blank image
		$im = @ImageCreate($buttonwidth, $buttonheight) or die ("Cannot Initialize new GD image stream");
		
		//create grayscale button image (source images must be 8bit grayscale)
		
		if ($this->corners_width > 0) {
			//left corner
			ImageCopy($im, imagecreatefrompng($this->path_dynamic_buttons.$buttonheight."l.png"), 0, 0, 0, 0, $this->corners_width, $buttonheight);
			//right corner
			ImageCopy($im, imagecreatefrompng($this->path_dynamic_buttons.$buttonheight."r.png"), $buttonwidth-$this->corners_width, 0, 0, 0, $this->corners_width, $buttonheight);
		}
		
		//middle 
		$middlewidth = $buttonwidth - ( 2 * $this->corners_width);
		$mig = imagecreatefrompng($this->path_dynamic_buttons.$buttonheight."m.png");
		
		
		if ($this->use_gd2) { //GD2
			//the following function do not work with paletted images if php < 4.3.0
			//imagecopyresampled($im, $mig, $this->corners_width, 0, 0, 0, $middlewidth, $buttonheight, $this->middle_width, $buttonheight);
			for ($j=0; $j<$middlewidth; $j++) {
				ImageCopy($im, $mig, $this->corners_width + $j, 0, 0, 0, 1, $buttonheight);
			}
		}
		else {
			imagecopyresized($im, $mig, $this->corners_width, 0, 0, 0, $middlewidth, $buttonheight, $this->middle_width, $buttonheight);
		}
		
		//calculate values for color change
		$step_color = array(); //calculate color coefficients
		$step_color['red'] = ($this->button_light_color['red'] - $this->button_dark_color['red'])/255;
		$step_color['green'] = ($this->button_light_color['green'] - $this->button_dark_color['green'])/255;
		$step_color['blue'] = ($this->button_light_color['blue'] - $this->button_dark_color['blue'])/255;
		
		//replace colors
		$new_color = array();
		$imagecolors = imagecolorstotal($im); //total image colors
		
		for($i=0;$i<$imagecolors;$i++) {
			$current_color = imagecolorsforindex ($im, $i); //get current color for this index
			
			$new_color['red'] = $this->button_dark_color['red'] + round($current_color['red']*$step_color['red']);
			if($new_color['red']>255) {$new_color['red'] = 255;} //check values
			elseif($new_color['red']<0) {$new_color['red'] = 0;}
			$new_color['green'] = $this->button_dark_color['green'] + round($current_color['green']*$step_color['green']);
			if($new_color['green']>255) {$new_color['green'] = 255;} //check values
			elseif($new_color['green']<0) {$new_color['green'] = 0;}
			$new_color['blue'] = $this->button_dark_color['blue'] + round($current_color['blue']*$step_color['blue']);
			if($new_color['blue']>255) {$new_color['blue'] = 255;} //check values
			elseif($new_color['blue']<0) {$new_color['blue'] = 0;}
			
			imagecolorset ($im, $i, $new_color['red'], $new_color['green'], $new_color['blue']); //replace colors
		}
		
		//change button gamma
		ImageGammaCorrect($im, 1, $this->button_gamma);
		
		$textcolor = ImageColorAllocate($im, $this->text_color['red'], $this->text_color['green'], $this->text_color['blue']);
		
		if (isset($this->text) AND (strlen($this->text)>0)) {
			ImageTTFText($im, $this->text_size, 0, $text_x, $text_y, $textcolor, $fontfile, $this->text);
		}
		
		if ($this->use_transparent) {
			$transparentcolor = imagecolorclosest ($im, $this->transparent_color['red'], $this->transparent_color['green'], $this->transparent_color['blue']);
			imagecolortransparent ($im, $transparentcolor);
		}
		
		//if necessary, rotate the image
		if (!$this->horizontal) {
			$im = $this->F_rotate($im, $buttonheight, $buttonwidth);
		}
		
		//save image file in cache
		if ($this->use_cache) {
			ImagePNG($im, $file_name);
		}
		
		return($im); //return the image button
	} //END create function

	/**
	* Return the cache filename
	* @private
	* @returns cache file
	*/
	function F_cache_file_name() {
		$cachefile = K_PATH_CACHE;
		//build file name using all options 
		$cachefile .= md5("".$this->path_fonts.$this->path_dynamic_buttons.$this->text.$this->text_font.$this->text_size.$this->text_alignment.$this->button_height.$this->button_width.$this->button_gamma.$this->text_color['red'].$this->text_color['green'].$this->text_color['blue'].$this->button_dark_color['red'].$this->button_dark_color['green'].$this->button_dark_color['blue'].$this->button_light_color['red'].$this->button_light_color['green'].$this->button_light_color['blue'].$this->use_transparent.$this->transparent_color['red'].$this->transparent_color['green'].$this->transparent_color['blue'].$this->padding.$this->horizontal.$this->corners_width."");
		$cachefile .= ".png";
		return $cachefile;
	}

	/**
	* rotate and image 90° counter clockwise (slow function, copy 1 pixel at time)
	* @public
	* @returns image object rotated
	*/
	function F_rotate($image_source, $height, $width) {
		// create a blank rotated image
		$image_rotated = @ImageCreate($height, $width) or die ("Cannot Initialize new GD image stream");
		
		for($x=0; $x<$width; $x++){
			for($y=0; $y<$height; $y++){
				$ry = $width - ($x + 1);
				$rx = $y;
				imagecopy($image_rotated, $image_source, $rx, $ry, $x, $y, 1, 1);
			}
		}
		return $image_rotated;
	}

} // End of class

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
