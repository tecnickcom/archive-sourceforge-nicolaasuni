<?php
//============================================================+
// File name   : cp_functions_general.php                      
// Begin       : 2001-09-08                                    
// Last Update : 2005-03-15                                    
//                                                             
// Description : General functions                             
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

// ------------------------------------------------------------
// Count rows of the given table
// ------------------------------------------------------------
function F_count_rows($dbtable, $where="WHERE 1") {
	global $db;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	$numofrows = 0;
	$sql = "SELECT COUNT(*) FROM ".$dbtable." ".$where." LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$numofrows = $m['0'];
		} 
	}
	else {
		F_display_db_error();
	}
	return($numofrows);
}
// ------------------------------------------------------------

// ------------------------------------------------------------
// Check if link is external
// ------------------------------------------------------------
function F_is_relative_link($linktocheck) {
	$linktocheck = strtolower ($linktocheck);
	if((substr($linktocheck,0,5)=="http:") OR (substr($linktocheck,0,6)=="https:") OR (substr($linktocheck,0,4)=="ftp:") OR (substr($linktocheck,0,4)=="udp:") OR (substr($linktocheck,0,4)=="ssl:") OR (substr($linktocheck,0,4)=="tls:") ) {
		return(FALSE);
	}
	return(TRUE);
}
// ------------------------------------------------------------

// ------------------------------------------------------------
// decode field and return the value for current language
// ------------------------------------------------------------
function F_decode_field($fieldtodecode) {
	global $selected_language;
	
	$thisfieldarray = unserialize($fieldtodecode);
	$thisname = stripslashes($thisfieldarray[$selected_language]);
	if(!$thisname) {$thisname = stripslashes($thisfieldarray[K_DEFAULT_LANGUAGE]);}
	return ($thisname);
}
// ------------------------------------------------------------

// ------------------------------------------------------------
// replace quotes with unicode
// used by language templates
// ------------------------------------------------------------
function F_replace_quotes($quotestring) {
	$quoteTable = array("\"" => "&quot;","'" => "&rsquo;","&#039;" => "&rsquo;"); //to escape quotes
	return strtr($quotestring, $quoteTable);
}

// ------------------------------------------------------------
// replace unicode quotes with quote symbols
// ------------------------------------------------------------
function F_reverse_unicode_quotes($quotestring) {
	$quoteTable = array("&quot;" => "\"", "&rsquo;" => "'","&#039;" => "'");
	return strtr($quotestring, $quoteTable);
}

// ------------------------------------------------------------
// new line to <br />
// ------------------------------------------------------------
function F_remove_linebreaks($nlstring) {
	$quoteTable = array("\n" => "", "\r" => ""); 
	return strtr($nlstring, $quoteTable);
}

// ------------------------------------------------------------
// load page templates in current language
// return an array of templates
// ------------------------------------------------------------
function F_load_page_templates($language, $page) {
	global $db;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	
	if(!isset($language)) {$language = K_DEFAULT_LANGUAGE;} //select default language
	
	$sql = "SELECT template, ".$language." FROM ".K_TABLE_LANGUAGE_PAGES." WHERE page='".$page."'";
	if($r = F_aiocpdb_query($sql, $db)) {
		$pt = array(); //create array of templates in selected language
		while($m = F_aiocpdb_fetch_array($r)) {
			if($m[$language]) { //check if the template exist
				$pt[$m['template']] = $m[$language]; //add elements to array
			}
			elseif ($m[K_DEFAULT_LANGUAGE]) { //if word not exist a default language word will be used
				$pt[$m['template']] = $m[K_DEFAULT_LANGUAGE];
			}
			else {
				$pt[$m['template']] = "";
			}
		}
	}
	else {
		F_display_db_error();
	}
	return $pt;
}

/**
 * Resolve combined relative links (e.g.: "/dir/subdir/../image.gif" became "/dir/image.gif").
 * @param $path String path to resolve
 */
function F_resolve_url_path($path) {
	$path = urldecode($path);
	$path = str_replace("\\", "/", $path); //UNIX - WINDOWS compatibility
	$patharray = explode('/', $path);
	$path = ""; 
	$remdir = 0; // directories to remove
	$count = count($patharray) - 1;
	for ($i = $count; $i >= 0; $i--) {
		if (!(($patharray[$i] == '.') OR ($patharray[$i] == '..'))) {
			if ($remdir == 0) {
				$path = $patharray[$i]."/".$path;
			}
			else {
				$remdir--;
				if ($remdir < 0) {
					$remdir = 0;
				}
			}
		}
		if ($patharray[$i] == '..') {
				$remdir++;
		}
	}
		
	//Trim trailing slash
	if (!empty($path)) {
		$path = substr($path, 0, -1);
	}
	return $path;
}

// ------------------------------------------------------------
// Return the current time in secons + microsecons decimals
// ------------------------------------------------------------
function getmicrotime() {
	list($usec, $sec) = explode(" ", microtime()); 
	return ((float)$usec + (float)$sec); 
}

// ------------------------------------------------------------
// Reverse function for htmlentities
// from comment posted on :
// http://www.php.net/manual/en/function.get-html-translation-table.php
// by: alan@akbkhome.com
// on: 03-Jun-2002 10:00
// ------------------------------------------------------------
function unhtmlentities($text_to_convert, $preserve_tagsign=FALSE) {
	$trans_tbl = get_html_translation_table(HTML_ENTITIES);
	$trans_tbl = array_flip($trans_tbl);
	if ($preserve_tagsign) {
		$trans_tbl['&lt;']="&lt;"; //do not convert '<' equivalent
		$trans_tbl['&gt;']="&gt;"; //do not convert '>' equivalent
	}
	$return_text = strtr($text_to_convert, $trans_tbl);
	$return_text = preg_replace('/\&\#([0-9]+)\;/me', "chr('\\1')", $return_text);
	return $return_text;
}

// ------------------------------------------------------------
// format the currency number
// ------------------------------------------------------------
function F_FormatCurrency($n) {
	return number_format(round($n,K_MONEY_DECIMALS), K_MONEY_DECIMALS, K_MONEY_DECIMAL_SEPARATOR, K_MONEY_THOUSAND_SEPARATOR);
}

// ------------------------------------------------------------
// get country name from id  
// ------------------------------------------------------------
function F_GetCountryName($id) {
	global $l, $db;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	$country_name = "";
	$sql = "SELECT country_name FROM ".K_TABLE_COUNTRIES." WHERE country_id='".$id."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$country_name = strtoupper($m['country_name']);
		}
	}
	else {
		F_display_db_error();
	}
	return $country_name;
}

//------------------------------------------------------------
// return unique id (32 characters alphanumeric string)
//------------------------------------------------------------
function F_generate_unique_code($str="") {
	mt_srand((double)microtime()*1000000);
	return(md5($str.uniqid(mt_rand(),true)));
}

//------------------------------------------------------------
// generate and return verification code for downloads
// the code validity has the following duration
// $duration: 1 = 4 sec
//            2 = 49 sec
//            3 = 499 sec    = 8.3 min
//            4 = 4999 sec   = 83.3 min   = 1.4 hours
//            5 = 49999 sec  = 833.3 min  = 13.9 hours
//            6 = 499999 sec = 8333.3 min = 138.9 hours = 5.8 days
//------------------------------------------------------------
function F_generate_verification_code($string="", $duration=4) {
	require_once('../../shared/config/cp_extension.inc');
	require_once('../../shared/config/cp_general_constants.'.CP_EXT);
	
	$time_interval = round(time(), -$duration);
	$verifycode = urlencode(md5($string.K_RANDOM_SECURITY.$time_interval));
	return $verifycode;
}

// ------------------------------------------------------------
// remove the following characters:
// "\t" (ASCII 9 (0x09)), a tab. 
// "\n" (ASCII 10 (0x0A)), a new line (line feed). 
// "\r" (ASCII 13 (0x0D)), a carriage return. 
// "\0" (ASCII 0 (0x00)), the NUL-byte. 
// "\x0B" (ASCII 11 (0x0B)), a vertical tab. 
// ------------------------------------------------------------
function F_compact_string($string) {
	$repTable = array("\t" => " ","\n" => " ","\r" => " ","\0" => " ","\x0B" => " "); //to escape quotes
	return strtr($string, $repTable);
}

// ------------------------------------------------------------
// define file_get_contents for php < 4.3
// ------------------------------------------------------------
if (!function_exists("file_get_contents")) {
  function file_get_contents($filename, $use_include_path = 0) {
   $data = ""; // just to be safe. Dunno, if this is really needed
   $file = @fopen($filename, "rb", $use_include_path);
   if ($file) {
     while (!feof($file)) $data .= fread($file, 1024);
     fclose($file);
   }
   return $data;
  }
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>