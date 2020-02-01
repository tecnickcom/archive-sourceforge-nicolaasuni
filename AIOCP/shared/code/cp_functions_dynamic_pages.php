<?php
//============================================================+
// File name   : cp_functions_dynamic_pages.php
// Begin       : 2002-04-20
// Last Update : 2008-07-06
// 
// Description : Functions for dynamic pages
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
// Return the requested language template for selected page
// ------------------------------------------------------------
function F_read_lang_page_template($page, $template) {
	global $l, $db, $selected_language;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_PAGES." WHERE page='".$page."' AND template='".$template."' LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			if ($m[$selected_language]) {
				return $m[$selected_language];
			}
			elseif ($m[K_DEFAULT_LANGUAGE]) { //return in default language if previous was void
				return $m[K_DEFAULT_LANGUAGE];
			}
			else {
				return "";
			}
		}
	}
	else {
		F_display_db_error();
	}
	return FALSE;
}


// ------------------------------------------------------------
// Return a string containing the full HTML code of
// requested page and/or header and/or footer
// ------------------------------------------------------------
function F_get_dynamic_page_data($pagedata_name, $show_body=TRUE, $show_header=TRUE, $show_footer=TRUE) {
	global $l, $db, $selected_language;
	global $thispage_title, $pagelevel, $thispage_description, $thispage_keywords, $thispage_author, $thispage_reply, $thispage_style, $thispage_icon;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);

	//get page body
	if ($show_body) {
		//initialize variable
		$userlevel = $_SESSION['session_user_level'];
		
		//read K_TABLE_PAGE_DATA
		$sql = "SELECT * FROM ".K_TABLE_PAGE_DATA." WHERE pagedata_name='".$pagedata_name."' LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$pagelevel = $m['pagedata_level'];
				$thispage_author = $m['pagedata_author'];
				$thispage_reply = $m['pagedata_replyto'];
				$thispage_style = $m['pagedata_style'];
				$pagedata_hf = $m['pagedata_hf'];
				$pagedata_enabled = $m['pagedata_enabled'];
			}
			else {
				//default page
				return F_get_dynamic_page_data("_main", $show_body, $show_header, $show_footer);
			}
		}
		else {
			F_display_db_error();
		}
	
		
		if($pagedata_enabled) { // if pagelevel=0 means access to anonymous user
			if($userlevel < $pagelevel) { //check user level
				if($_SESSION['session_user_id'] == 1) { //actions for anonymous user
					F_login_form(); //display login form
				}
				else {
					//check if user has a special permission to access the requested resource:
					$current_time = gmdate("Y-m-d H:i:s");
								
					//delete expired special accounts (garbage collector)
					$sql = "DELETE FROM ".K_TABLE_USERS_AUTH." WHERE ua_time_end<'".$current_time."'";
					if(!$r = F_aiocpdb_query($sql, $db)) {
						F_display_db_error();
					}
						
					// get current page
					$current_page = K_PATH_HOST.$_SERVER['SCRIPT_NAME'];
					if ($_SERVER['QUERY_STRING']) {
						$current_page .= "?".$_SERVER['QUERY_STRING'];
					}
					
					//check user permission
					$sql = "SELECT * FROM ".K_TABLE_USERS_AUTH." WHERE ua_user_id='".$_SESSION['session_user_id']."' AND ua_time_start<='".$current_time."' AND ua_time_end>='".$current_time."' AND LOCATE(ua_resource,'".$current_page."')>0 LIMIT 1";
					if($r = F_aiocpdb_query($sql, $db)) {
						if(!F_aiocpdb_fetch_array($r)) {
							F_print_error("WARNING", $l['m_authorization_deny']); //display error message
							F_logout_page();
						}
					}
					else {
						F_display_db_error();
					}	
				}
			}
		}
		else {
			F_print_error("WARNING", $l['m_authorization_deny']);
			return FALSE;
		}
		
		$thispage_title = F_read_lang_page_template($pagedata_name, '_title');
		$thispage_description = F_read_lang_page_template($pagedata_name, '_description');
		$thispage_keywords = F_read_lang_page_template($pagedata_name, '_keywords');
		$pagedata_body = F_read_lang_page_template($pagedata_name, '_body');
	}
	
	//get header and footer data
	if ($show_header OR $show_footer) {
		if ($show_body) {
			$sql = "SELECT * FROM ".K_TABLE_PAGE_HEADER_FOOTER." WHERE pagehf_id='".$pagedata_hf."' LIMIT 1";
		}
		else {
			$sql = "SELECT * FROM ".K_TABLE_PAGE_HEADER_FOOTER." WHERE pagehf_name='".$pagedata_name."' LIMIT 1";
		}
		//read page header and footer
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$pagedata_header = $m['pagehf_header'];
				$pagedata_footer = $m['pagehf_footer'];
				$pagehf_name = $m['pagehf_name'];
				$pagedata_hf = $m['pagehf_id']; 
			}
		}
		else {
			F_display_db_error();
		}
	}
	
	//compose page
	$fullpage = "";
	
	//add header
	if ($show_header) {
		if(isset($hidetitle) AND ($hidetitle)) {//remove title from header
			$pagedata_header = preg_replace("/<h[0-9][^>]*?>#TITLE#<\/h[0-9]>/i", "", $pagedata_header); 
		}
		
		$fullpage .= $pagedata_header."\n";
		
		if (strcmp($pagehf_name,"default_popup") != 0) { //default_popup do not use the following
			if(K_CHECK_JAVASCRIPT) {
				$fullpage = str_replace("</head>", "<noscript><meta http-equiv='refresh' CONTENT='0;url=".K_REDIRECT_JAVASCRIPT_ERROR."' /></noscript>\n</head>", $fullpage);
			}
			
			if(K_USE_FRAMES) {
				$fullpage .= "\n<script language=\"JavaScript\" type=\"text/javascript\">\n";
				$fullpage .= "//<![CDATA[\n";
				$fullpage .= "if(window.name != \"".K_MAIN_FRAME_NAME."\") {\n";
				$fullpage .= "document.write(\"<meta http-equiv='refresh' CONTENT='0;url=../code/index.".CP_EXT."?load_page=\" + escape(document.location.href) + \"' />\");\n}\n";
				$fullpage .= "//]]>\n";
				$fullpage .= "</script>\n";
			}
			
			//load overlib to display quick description help (DO NOT CHANGE)
			if (K_DISPLAY_QUICK_HELP) { 
				$fullpage .= "\n<!-- overLIB ==================== -->\n";
				$fullpage .= "<div id=\"overDiv\" style=\"z-index:1000; visibility:hidden; position:absolute\"></div>\n";
				$fullpage .= "<script language=\"JavaScript\" src=\"".K_PATH_SHARED_JSCRIPTS."overlib_aiocp.js\" type=\"text/javascript\"></script>\n";
				$fullpage .= "<!-- END overLIB ==================== -->\n";
			}
		}
	}
	
	//add page body
	if ($show_body) {
		$fullpage .= "\n".$pagedata_body."\n";
	}
	
	//add footer
	if ($show_footer) {
		$fullpage .= "\n".$pagedata_footer."\n";
	}
	
	// evaluate PHP modules and language templates
	$fullpage = F_evaluate_modules($fullpage);

	return $fullpage;
}

// ------------------------------------------------------------
// return dinamic page data
// ------------------------------------------------------------
function F_show_dynamic_page($pagedata_name) {
	return F_get_dynamic_page_data($pagedata_name, TRUE, TRUE, TRUE);
}
// ------------------------------------------------------------
// return requested header
// ------------------------------------------------------------
function F_show_header($pagedata_hf) {
	return F_get_dynamic_page_data($pagedata_hf, FALSE, TRUE, FALSE);
}

// ------------------------------------------------------------
// return requested footer
// ------------------------------------------------------------
function F_show_footer($pagedata_hf) {
	return F_get_dynamic_page_data($pagedata_hf, FALSE, FALSE, TRUE);
}

// ------------------------------------------------------------
// evaluate PHP modules and language templates
// ------------------------------------------------------------
function F_evaluate_modules($codedtext) {
	global $l, $db, $selected_language;
	global $thispage_title, $pagelevel, $thispage_description, $thispage_keywords, $thispage_author, $thispage_reply, $thispage_style, $thispage_icon;
	
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	if (!isset($codedtext) or (strlen($codedtext)<1)) {
		return "";
	}
	
	$returncode = $codedtext;
	
	//if necessary load default values
	if(!isset($pagelevel) OR (!$pagelevel)) {$pagelevel = 0;}
	if(!isset($thispage_title) OR (!$thispage_title)) {$thispage_title = K_SITE_TITLE;}
	if(!isset($thispage_description) OR (!$thispage_description)) {$thispage_description = K_SITE_DESCRIPTION;}
	if(!isset($thispage_author) OR (!$thispage_author)) {$thispage_author = K_SITE_AUTHOR;}
	if(!isset($thispage_reply) OR (!$thispage_reply)) {$thispage_reply = K_SITE_REPLY;}
	if(!isset($thispage_keywords) OR (!$thispage_keywords)) {$thispage_keywords = K_SITE_KEYWORDS;}
	if(!isset($thispage_icon) OR (!$thispage_icon)) {$thispage_icon = K_SITE_ICON;}
	if(!isset($thispage_style) OR (!$thispage_style)) {$thispage_style = K_SITE_STYLE;}
		
	//replace templates
	$returncode = str_replace("#LANGUAGE#", $l['a_meta_language'], $returncode);
	$returncode = str_replace("#LANGDIR#", $l['a_meta_dir'], $returncode);
	$returncode = str_replace("#CHARSET#", $l['a_meta_charset'], $returncode);
	$returncode = str_replace("#TITLE#", htmlentities(unhtmlentities($thispage_title,true), ENT_NOQUOTES, $l['a_meta_charset']), $returncode);
	$returncode = str_replace("#LEVEL#", $pagelevel, $returncode);
	$returncode = str_replace("#DESCRIPTION#", htmlentities(unhtmlentities($thispage_description,true), ENT_NOQUOTES, $l['a_meta_charset']), $returncode);
	$returncode = str_replace("#KEYWORDS#", htmlentities(unhtmlentities($thispage_keywords,true), ENT_NOQUOTES, $l['a_meta_charset']), $returncode);
	$returncode = str_replace("#AUTHOR#", htmlentities(unhtmlentities($thispage_author,true), ENT_NOQUOTES, $l['a_meta_charset']), $returncode);
	$returncode = str_replace("#REPLYTO#", $thispage_reply, $returncode);
	$returncode = str_replace("#STYLE#", K_PATH_STYLE_SHEETS.$thispage_style, $returncode);
	$returncode = str_replace("#ICON#", $thispage_icon, $returncode);
	
	// display language template
	while (ereg("\#LT=([A-Za-z0-9_]*)\#", $returncode, $regs)) {
		$returncode = str_replace("#LT=".$regs[1]."#", $l[$regs[1]], $returncode);
	}
	
	//paste standard and custom php modules
	$sqlcm = "SELECT * FROM ".K_TABLE_PAGE_MODULES."";
	if($rcm = F_aiocpdb_query($sqlcm, $db)) {
		while($mcm = F_aiocpdb_fetch_array($rcm)) {
			if (!$mcm['pagemod_params']) {
				$template_name = "#".strtoupper($mcm['pagemod_name'])."#";
				
				if (strpos($returncode, $template_name)) {
					$code_to_evaluate = $mcm['pagemod_code'];
					
					ob_start(); //store output data to buffer
					eval($code_to_evaluate); //evaluate code
					$code_to_paste = ob_get_contents(); //read buffer
					ob_end_clean(); //clean buffer
					
					$returncode = str_replace($template_name, $code_to_paste, $returncode); //add code to page
				}
			}
			else { //there are parameters on template
				//build template search string
				$template_name = "/#".strtoupper($mcm['pagemod_name'])."=";
				for ($i=1; $i<=$mcm['pagemod_params']; $i++) {
					$template_name .= "([A-Za-z0-9_]*),";
				}
				$template_name = substr($template_name, 0, -1); //remove trailing comma
				$template_name .= "#/";
				
				//while (ereg($template_name, $returncode, $regs)) {
				while (preg_match($template_name, $returncode, $regs)) {
					// get module code
					$code_to_evaluate = $mcm['pagemod_code'];
					//substitute parameters in the module code block
					while(list($key, $val) = each($regs)) {
						if ($key) {
							$code_to_evaluate = str_replace("#P".$key."#", $val, $code_to_evaluate);
						}
					}
					
					ob_start(); //store output data to buffer
					eval($code_to_evaluate); //evaluate code
					$code_to_paste = ob_get_contents(); //read buffer
					ob_end_clean(); //clean buffer
					
					$returncode = preg_replace($template_name, $code_to_paste, $returncode, 1);
				}
			}
		}
	}
	else {
		F_display_db_error();
	}
	return $returncode;
}
//============================================================+
// END OF FILE                                                 
//============================================================+
?>