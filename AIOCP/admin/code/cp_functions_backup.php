<?php
//============================================================+
// File name   : cp_functions_backup.php                       
// Begin       : 2001-01-11                                    
// Last Update : 2004-01-29                                    
//                                                             
// Description : Browse and backup all files starting from     
//               a given directory.                            
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
//============================================================+

// ------------------------------------------------------------
// Backup the entire site in a single .zip file
// ------------------------------------------------------------
function F_backup_dir($base_backup_path, $zipname) {
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	global $fullpath, $relpath, $isdir, $progress_log;
	
	ini_set("memory_limit", K_MAX_MEMORY_LIMIT); //extend menory limit
	set_time_limit(K_MAX_EXECUTION_TIME); //extend the maximum execution time
	
	$backupfile = new C_zip_file();
	$backupfile->F_add_dir("/"); //create starting dir
	
	//variables to keep the filesystem structure
	$fullpath = Array();
	$relpath = Array();
	$isdir = Array();
	
	$excluded_dirs = explode(",", K_BACKUP_EXCLUDE); //array of excluded dirs
	
	//create file list to zip
	F_browse_dir($base_backup_path, $base_backup_path, $excluded_dirs);
	
	$file_num = 0; //count processed files/dirs
	
	//put files on zip archive
	while(list($key, $val) = each($fullpath)) { //for each file on list
		$file_num++; //count processed files/dirs

		if($isdir[$key]) { // is a directory
			$backupfile->F_add_dir($relpath[$key]."/", filemtime($val)); //add dir to backup zipped file
		}
		else { // is a file
			$backupfile->F_add_file(file_get_contents($val), $relpath[$key], filemtime($val)); //add file to backup zipped file
		}
		
		$file_log = "".$file_num." - ".$relpath[$key]."";
		error_log($file_log."\n", 3, $progress_log); //create progress log file
		//echo "<small>".$file_log."</small><br />\n"; //output processed files
		echo "          "; //print something to keep browser live
		if (($file_num % 30) == 0) { //force flush output every 30 processed files
			echo "<!-- ".$file_num." -->\n"; flush(); //force flush output to browser
		}
	}
	
	//write zip file
	$zipfilename = K_PATH_FILES_BACKUP.$zipname.".zip";
	
	if($fd = fopen($zipfilename, "wb")) {
		if(!$out = fwrite($fd, $backupfile->F_file ())) {
			F_print_error("ERROR", $l['m_writefile_not']."<br />".$zipfilename);
			return false;
		}
		fclose($fd);
	}
	else {
		F_print_error("ERROR", $l['m_savefile_not']."<br />".$zipfilename);
		return false;
	}
	
	return $zipfilename;
}

// ------------------------------------------------------------
// get the entire contents of a dir and the tree below to 
// Note:
// The last character of the path should be a slash:
// BackupSite("/home/httpd/html/mywebsite/");
// ------------------------------------------------------------
function F_browse_dir($basepath, $sPath, $excluded_dirs) {
	global $fullpath, $relpath, $isdir;
	
	$handle=opendir($sPath);
	while (false !== ($file = readdir($handle))) { // browse directory
		if (($file != ".") AND ($file != "..")) {
			$temp_fullpath = $sPath.$file;
			$temp_relpath = substr($temp_fullpath, strlen($basepath)); //create file relative path
			
			if(!is_dir($temp_fullpath)) { // is a file
				$isdir[] = false;
				$fullpath[] = $temp_fullpath;
				$relpath[] = $temp_relpath;
			}
			else { // is a directory
				if (!in_array($temp_relpath, $excluded_dirs)) {// check for excluded directories
					$isdir[] = true;
					$fullpath[] = $temp_fullpath;
					$relpath[] = $temp_relpath;
					F_browse_dir($basepath, $temp_fullpath."/", $excluded_dirs);
				}
			}
		}
	}
	closedir($handle);
	return;
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>