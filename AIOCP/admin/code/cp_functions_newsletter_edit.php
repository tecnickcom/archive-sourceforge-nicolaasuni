<?php
//============================================================+
// File name   : cp_functions_newsletter_edit.php              
// Begin       : 2001-10-19                                    
// Last Update : 2003-11-18                                    
//                                                             
// Description : Functions for Edit Newsletter                 
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

//------------------------------------------------------------
// Uploads attach file to the server on default attach directory
//------------------------------------------------------------
function F_upload_newsletter_attachment() {
	global $l;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	$path_parts = pathinfo($_FILES['userfile']['name']);
	$filename = $path_parts['basename'];
	
	if($_FILES['userfile']['size'] <= K_MAX_UPLOAD_SIZE) { //check for size
		if(move_uploaded_file ($_FILES['userfile']['tmp_name'], K_PATH_FILES_ATTACHMENTS.$filename)) {
			F_print_error("MESSAGE", $filename.": ".$l['m_upload_yes']);
			return $filename;
		}
	}
	F_print_error("ERROR", $_FILES['userfile']['name'].": ".$l['m_upload_not']."");
return FALSE;
}
// ------------------------------------------------------------

//------------------------------------------------------------
// Deletes a file
//------------------------------------------------------------
function F_delete_attachment($file) {
	global $l;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	if(!unlink($file)) {
		F_print_error("ERROR", $file.": ".$l['m_delete_not']);
		return FALSE;
	}
	else {
		F_print_error("MESSAGE", $file.": ".$l['m_delete_yes']);
	}
return TRUE;
}

//------------------------------------------------------------
// Analyze message body for src="..." and replace src content
// with unique ID that refer to attachment file.
//------------------------------------------------------------
function F_unique_id() {
	mt_srand((double)microtime()*1000000);
	return("CID_".md5(uniqid(mt_rand(),true)));
}

//------------------------------------------------------------
// Analyze message body for src="..." and return first link
// that not start with "_&-" or external path like http://
// (links starting with "_&-" will be not parsed)
//------------------------------------------------------------
function F_newsletter_find_link($message) {
	$searchmessage = stripslashes($message);
	//mark external links
	$searchmessage = str_replace("http://", "_&-", $searchmessage);
	$searchmessage = str_replace("https://", "_&-", $searchmessage);
	$searchmessage = str_replace("ftp://", "_&-", $searchmessage);
	
	eregi("[[:space:]](src|code|codebase)[=][\"|\']([^_][^&][^-][^\"|^\'|^>|[:space:]]*)", $searchmessage, $foundlinks);
	return $foundlinks[2];
}

//------------------------------------------------------------
// Add relative external files to attaches folder and to attaches database
// Relative links are the links not starting with K_PATH_FILES_ATTACHMENTS 
// or http:// or https:// ...
//------------------------------------------------------------
function F_newsletter_fix_relative_src_links($msgID, $message) {
	global $l, $db;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	//reset nlattach_cid of all attachments of the message
	$sql = "UPDATE IGNORE ".K_TABLE_NEWSLETTER_ATTACHMENTS." SET nlattach_cid='' WHERE nlattach_nlmsgid=".$msgID."";
	if(!$r = F_aiocpdb_query($sql, $db)) {
		F_display_db_error();
	}
	
	//convert attachments absolute paths to relative
	$message = str_replace(K_PATH_FILES_ATTACHMENTS_FULL, K_PATH_FILES_ATTACHMENTS, $message);
	
	while($thislink = F_newsletter_find_link($message)) { //search all non-external links
		$filename = basename($thislink);
		$newlink = K_PATH_FILES_ATTACHMENTS.$filename; //new file path name on attachments folder
		$message = str_replace($thislink,"_&-".$newlink,$message); //update address and mark it
		
		$thislink = str_replace("\\","/",$thislink); //windows-UNIX compatibility
		$thislink = eregi_replace("^/",F_get_web_root(),$thislink); //resolve path
		
		if(is_file($thislink)) { //if link is valid (file exist)
			//if file is not already on attaches directory
			if((dirname($thislink) != K_PATH_FILES_ATTACHMENTS)AND(!is_file ($newlink))) {
				if(!copy($thislink, $newlink)) { //copy file to attaches dir
					F_print_error("ERROR", $thislink.": ".$l['m_copy_file_not']);
				}
			}
			$CID = F_unique_id(); //generate unique ID
			
			//add to attaches database
			$sql = "SELECT * FROM ".K_TABLE_NEWSLETTER_ATTACHMENTS." WHERE nlattach_nlmsgid=".$msgID." AND  nlattach_file='".$filename."'";
			if(!(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r)))) { //check if $newlink is unique
				$sql = "INSERT IGNORE INTO ".K_TABLE_NEWSLETTER_ATTACHMENTS." (
				nlattach_nlmsgid, 
				nlattach_file, 
				nlattach_cid
				) VALUES (
				'".$msgID."', 
				'".$filename."', 
				'".$CID."')";
				if(!$r = F_aiocpdb_query($sql, $db)) { //add item
					F_display_db_error();
				}
			}
			else { //update existing record with new $CID
				$sql = "UPDATE IGNORE ".K_TABLE_NEWSLETTER_ATTACHMENTS." SET nlattach_cid='".$CID."' WHERE nlattach_nlmsgid=".$msgID." AND  nlattach_file='".$filename."'";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
			}
		}
	} //end of while
	$message = str_replace("_&-","",$message); //restore addresses
return $message;
}

//------------------------------------------------------------
// string F_get_web_root()
// returns path to the web document root.
//------------------------------------------------------------
function F_get_web_root() {
	//replace slashes for unix/windows compatibility:
	$pathinfo = str_replace("//","/",str_replace("\\","/",$_SERVER['PATH_INFO']));
	$pathtranslated = str_replace("//","/",str_replace("\\","/",$_SERVER['PATH_TRANSLATED']));
	
	$webroot = str_replace($pathinfo,"/",$pathtranslated);
	return($webroot);
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
