<?php
//============================================================+
// File name   : cp_functions_zip.php                          
// Begin       : 2001-09-23                                    
// Last Update : 2003-01-10 (by Nicola Asuni)                  
//                                                             
// Description : Zip file creation class                       
//               (makes zip files on the fly... )              
//               compatible with pkzip 2.0 protocol            
//                                                             
// Requires    : gz library, PHP 4 or better                   
//                                                             
// This is a modified version of:                              
// ----------------------------------------------------------- 
// Name        : Zip file creation class                       
// License     : public domain                                 
//                                                             
// Author      : Eric Mueller <eric@themepark.com>             
//                                                             
// v1.1 9-20-01                                                
//   - added comments to example                               
//                                                             
// v1.0 2-5-01                                                 
//                                                             
// initial version with:                                       
//   - class appearance                                        
//   - F_add_file() and F_file() methods                       
//   - gzcompress() output hacking                             
//   by Denis O.Philippov,                                     
//   webmaster@atlant.ru, http://www.atlant.ru                 
// ----------------------------------------------------------- 
// See also:                                                   
// http://www.pkware.com/products/white_papers/white_appnote.html
//                                                             
// Author of changes : Nicola Asuni                            
//                                                             
//               Tecnick.com LTD
//               Manor Coach House, Church Hill
//               Aldershot, Hants, GU12 4RQ
//               UK
//               www.tecnick.com
//               info@tecnick.com                           
//============================================================+

/**
 * Zip Archive creation class. 
 * Makes compressed zip files archive.
 * @access  public
 */
class C_zip_file {
	
	/**
	* Array to store compressed data
	* @var array
	*/
	var $datasec = array();
	
	/**
	* central directory
	* @var array
	*/
	var $ctrl_dir = array();
	
	/**
	* end of Central directory record
	* @var string
	*/
	var $eof_ctrl_dir = "\x50\x4b\x05\x06\x00\x00\x00\x00";
	
	/**
	* Last offset position
	* @var integer
	*/
	var $old_offset = 0;
	
	/**
	* Converts an Unix timestamp to a four byte MS-DOS date and time format.
	*
	* Format of the MS-DOS time stamp (32-bit):
	* The MS-DOS time stamp is limited to an even count of seconds, since the
	* count for seconds is only 5 bits wide.
	* 
	*  31 30 29 28 27 26 25 24 23 22 21 20 19 18 17 16
	* |<---- year-1980 --->|<- month ->|<--- day ---->|
	* 
	*  15 14 13 12 11 10  9  8  7  6  5  4  3  2  1  0
	* |<--- hour --->|<---- minute --->|<- second/2 ->|
	* 
	* @param  integer $unix_time the current Unix timestamp
	* @return string  the file time in MS-DOS format
	* @access private
	*/
	function FormatZipTime($unix_time = 0) {
		
		if (!$unix_time) {
			$time_data = getdate();
		}
		else {
			$time_data = getdate($unix_time);
		}
		
		if ($time_data['year'] < 1980) {
			$time_data['year'] = 1980;
			$time_data['mon'] = 1;
			$time_data['mday'] = 1;
			$time_data['hours'] = 0;
			$time_data['minutes'] = 0;
			$time_data['seconds'] = 0;
		}
		
		$hex_time = "".dechex((($time_data['year'] - 1980) << 25) | ($time_data['mon'] << 21) | ($time_data['mday'] << 16) | ($time_data['hours'] << 11) | ($time_data['minutes'] << 5) | ($time_data['seconds'] >> 1))."";
        $str_time = "\x".$hex_time[6].$hex_time[7]."\x".$hex_time[4].$hex_time[5]."\x".$hex_time[2].$hex_time[3]."\x".$hex_time[0].$hex_time[1]."";
		eval('$str_time = "'.$str_time.'";');
		return $str_time;
	}
	
	/**
	* adds "directory" to archive
	* (do this before putting any files in directory!)
	* @param  string  name of directory (path must end with "/")
	* @access public
	*/
	function F_add_dir($name, $dirtime = 0) {
		$name = str_replace("\\", "/", $name);
		
		$zip_dir_time = $this->FormatZipTime($dirtime);
		
		// --- Local file header ---
		$fr = "\x50\x4b\x03\x04"; 	// local file header signature 	4 bytes (0x04034b50) 
		$fr .= "\x0a\x00"; 			//version needed to extract 	2 bytes 
		$fr .= "\x00\x00"; 			// general purpose bit flag 	2 bytes 
		$fr .= "\x00\x00"; 			// compression method 			2 bytes 
		$fr .= $zip_dir_time; // last mod file time 2 bytes + last mod file date 2 bytes 
		
		$fr .= pack("V",0); 		// crc-32 						4 bytes 
		$fr .= pack("V",0); 		// compressed size 				4 bytes 
		$fr .= pack("V",0); 		// uncompressed size 			4 bytes 
		$fr .= pack("v", strlen($name)); // file name length 		2 bytes 
		$fr .= pack("v", 0); 		// extra field length 			2 bytes 
		$fr .= $name; 				//file name 					(variable size) 
		//$fr .= $extrefield; 		// extra field (variable size) 
		// --- END OF: Local file header ---
		
		// no "file data" segment for path 
		
		// "data descriptor" segment (optional but necessary if archive is not served as file) 
		$fr .= pack("V",0); //crc32
		$fr .= pack("V",0); //compressed filesize
		$fr .= pack("V",0); //uncompressed filesize
		
		// add this entry to array 
		$this -> datasec[] = $fr;
		
		$new_offset = strlen(implode("", $this->datasec));
		
		// ext. file attributes mirrors MS-DOS directory attr byte, detailed 
		// at http://support.microsoft.com/support/kb/articles/Q125/0/19.asp 
		
		// now add to central record
		$cdrec = "\x50\x4b\x01\x02";
		$cdrec .="\x00\x00"; // version made by
		$cdrec .="\x0a\x00"; // version needed to extract
		$cdrec .="\x00\x00"; // gen purpose bit flag
		$cdrec .="\x00\x00"; // compression method
		$cdrec .= $zip_dir_time; // last mod file time 2 bytes + last mod file date 2 bytes 
		$cdrec .= pack("V", 0); // crc32
		$cdrec .= pack("V", 0); //compressed filesize
		$cdrec .= pack("V", 0); //uncompressed filesize
		$cdrec .= pack("v", strlen($name)); //length of filename 
		$cdrec .= pack("v", 0); //extra field length
		$cdrec .= pack("v", 0); //file comment length
		$cdrec .= pack("v", 0); //disk number start
		$cdrec .= pack("v", 0); //internal file attributes
		$ext = "\x00\x00\x10\x00";
		$ext = "\xff\xff\xff\xff";
		$cdrec .= pack("V", 16 ); //external file attributes  - 'directory' bit set
		
		$cdrec .= pack("V", $this->old_offset ); //relative offset of local header
		$this->old_offset = $new_offset;
		
		$cdrec .= $name;
		// optional extra field, file comment goes here
		// save to array 
		$this->ctrl_dir[] = $cdrec;
	}
	
	/**
	* Adds "file" to archive
	* @param  string  file contents
	* @param  string  name of the file in the archive (may contains the path)
	* @param  int filetime time the file was last modified in unix timestamp
	* @access public
	*/
	function F_add_file($data, $name, $filetime = 0) {
		$name = str_replace("\\", "/", $name);
		
		$zip_file_time = $this->FormatZipTime($filetime);
		
		$fr = "\x50\x4b\x03\x04";
		$fr .= "\x14\x00";    // ver needed to extract
		$fr .= "\x00\x00";    // gen purpose bit flag
		$fr .= "\x08\x00";    // compression method
		$fr .= $zip_file_time; // last mod time and date
		
		$unc_len = strlen($data);
		$crc = crc32($data);
		$zdata = gzcompress($data); //you could also specify compression level 0-9
		$zdata = substr( substr($zdata, 0, strlen($zdata) - 4), 2); // fix crc bug
		$c_len = strlen($zdata);
		$fr .= pack("V", $crc); // crc32
		$fr .= pack("V", $c_len); //compressed filesize
		$fr .= pack("V", $unc_len); //uncompressed filesize
		$fr .= pack("v", strlen($name)); //length of filename
		$fr .= pack("v", 0); //extra field length
		$fr .= $name;   
		// end of "local file header" segment
		
		// "file data" segment
		$fr .= $zdata;
		
		// "data descriptor" segment (optional but necessary if archive is not served as file) 
		$fr .= pack("V", $crc); //crc32
		$fr .= pack("V", $c_len); //compressed filesize
		$fr .= pack("V", $unc_len); //uncompressed filesize
		
		// add this entry to array 
		$this->datasec[] = $fr;
		
		$new_offset = strlen(implode("", $this->datasec));
		
		// now add to central directory record
		$cdrec = "\x50\x4b\x01\x02";
		$cdrec .= "\x00\x00";    // version made by
		$cdrec .= "\x14\x00";    // version needed to extract
		$cdrec .= "\x00\x00";    // gen purpose bit flag
		$cdrec .= "\x08\x00";    // compression method
		$cdrec .= $zip_file_time; // last mod time & date
		$cdrec .= pack("V", $crc); // crc32
		$cdrec .= pack("V", $c_len); //compressed filesize
		$cdrec .= pack("V", $unc_len); //uncompressed filesize
		$cdrec .= pack("v", strlen($name)); //length of filename
		$cdrec .= pack("v", 0); //extra field length
		$cdrec .= pack("v", 0); //file comment length
		$cdrec .= pack("v", 0); //disk number start
		$cdrec .= pack("v", 0); //internal file attributes
		$cdrec .= pack("V", 32); //external file attributes - 'archive' bit set
		
		$cdrec .= pack("V", $this->old_offset); //relative offset of local header
		//echo "old offset is ".$this->old_offset.", new offset is $new_offset<br />";
		$this->old_offset = $new_offset;
		
		$cdrec .= $name;
		// optional extra field, file comment goes here
		// save to central directory
		$this->ctrl_dir[] = $cdrec;
	}
	
	/**
	* Dumps out file
	* @return  string  the zipped file
	* @access public
	*/
	function F_file() {
		$data = implode("", $this->datasec);
		$ctrldir = implode("", $this->ctrl_dir);
		
		return
			$data.   
			$ctrldir.   
			$this->eof_ctrl_dir.   
			pack("v", sizeof($this->ctrl_dir)). // total # of entries "on this disk" 
			pack("v", sizeof($this->ctrl_dir)). // total # of entries overall 
			pack("V", strlen($ctrldir)). // size of central dir 
			pack("V", strlen($data)). // offset to start of central dir 
			"\x00\x00"; // .zip file comment length 
	}
	
} // END of class

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
