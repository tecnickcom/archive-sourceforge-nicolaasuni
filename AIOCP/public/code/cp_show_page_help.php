<?php
//============================================================+
// File name   : cp_show_page_help.php                         
// Begin       : 2002-11-11                                    
// Last Update : 2003-10-12                                    
//                                                             
// Description : Display page HELP                             
//                                                             
// NOTES:                                                      
// Help pages are stored as templates on "language_help" table.
// The name of help template for an existing php page is:      
// "hp_"+"file_name_without_extension"                         
// The name for general help pages that do not refer to an     
// existing php page is:                                       
// "hp_"+"help_page_title"                                     
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

require_once('../../shared/config/cp_extension.inc');
require_once('../config/cp_config.'.CP_EXT);

$pagelevel = 0;
require_once('../../shared/code/cp_authorization.'.CP_EXT);
require_once('../../shared/code/cp_functions_collect_stats.'.CP_EXT);

$index_template = "hp_000_index";
$imgdir = K_PATH_IMAGES."aiocp_guide/";
$img_width = 200;
$img_height = 150;

if (substr($_REQUEST['hp'],0,3)=="hp_") {
	$page_name = $_REQUEST['hp'];
}
else {
	$page_name = explode("?", $_REQUEST['hp']); //remove parameters
	$page_name = $page_name[0];
	$page_name = "hp_".basename(urldecode($page_name), ".".CP_EXT);
}

require_once('../../shared/code/cp_functions_language.'.CP_EXT);
$help_page = F_help_language($selected_language, $page_name);

//compose page title
$thispage_title = "AIOCP";
//try to get title from first heading
if (preg_match("/<h[1-9]>([^<]+)<\/h[1-9]>/i", $help_page, $regs)) {
	$thispage_title .= ": ".$regs[1];
}

//leave following variables void for default values
$thispage_description = $thispage_title;
$thispage_author = "";
$thispage_reply = "";
$thispage_keywords = $thispage_title;
$thispage_style = "";
$hidetitle = true;

$page_body = "";
$menu_previous = "";
$menu_next = "";
$menu_back = "<a href=\"javascript:window.history.back()\">&lt; ".$l['w_back']."</a>";
$menu_forward = "<a href=\"javascript:window.history.forward()\">".$l['w_forward']." &gt;</a>";
$menu_index = "<a href=\"cp_show_page_help.".CP_EXT."\">".$l['w_index']."</a>";

require_once('../code/cp_page_header.'.CP_EXT);

$link_name = Array();
$help_id = Array();

//get help pages names and IDs
$sql = "SELECT help_id,".$selected_language." FROM ".K_TABLE_LANGUAGE_HELP." WHERE help_id LIKE 'hp_%'";
if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		if ($m['help_id'] != $index_template) {
			$help_id[] = $m['help_id'];
			//try to get title from first heading
			if (preg_match("/<h[1-9]>([^<]+)<\/h[1-9]>/i", $m[$selected_language], $regs)) {
				$link_name[] = $regs[1];
			}
			else { // use template name as title
				$link_name[] = substr($m['help_id'],3);
			}
		}
	}
}
else {
	F_display_db_error();
}


//sort array
$help_index_key = Array(); //contain the ordered help index
$help_index_name = Array(); //contain the ordered help index
natcasesort($link_name);
reset($link_name);
$current_key = 0;
$i = 0;
while(list($key, $val) = each($link_name)) {
	$help_index_key[$i] = $help_id[$key];
	$help_index_name[$i] = $val;
	if ($help_id[$key] == $page_name) {
		$current_key = $i;
	}
	$i++;
}


if ($help_page) { //if page exist display help page
	$sub_link_name = Array();
	$sub_help_id = Array();
	$page_body .= $help_page; //print page content
	
	//get page title
	if (preg_match("/(<h[1-9]>)([^<]+)(<\/h[1-9]>)/i", $help_page, $regs)) {
		$page_title = $regs[2];
		$full_title = $regs[1].$regs[2].$regs[3];
		//get paragraph number
		if (preg_match("/^([.A0-9]+) /i", $page_title, $regs)) {
			$chapter = $regs[1];
			
			$image_name = str_replace(".", "-", $chapter);
			$image_file = $imgdir.$image_name.".jpg";
			$image_small_file = $imgdir."m_".$image_name.".jpg";
			if (file_exists($image_file) AND file_exists($image_small_file)) {
				$img_string = "<a href=\"".$image_file."\" target=\"_blank\"><img src=\"".$image_small_file."\" alt=\"".$l['d_click_to_enlarge']."\" width=\"".$img_width."\" height=\"".$img_height."\" border=\"0\" /></a><br /><br />";
				$page_body = str_replace($full_title, $full_title.$img_string, $page_body);
			}
			
			$current_level = count(split('[.]', $chapter)); //1=chapter, 2=paragraph,...
			//display this chapter paragraphs in order
			$sql = "SELECT help_id,".$selected_language." FROM ".K_TABLE_LANGUAGE_HELP." WHERE help_id LIKE 'hp_%'";
			if($r = F_aiocpdb_query($sql, $db)) {
				while($m = F_aiocpdb_fetch_array($r)) {
					if (preg_match("/<h[1-9]>([^<]+)<\/h[1-9]>/i", $m[$selected_language], $regs)) {
						$temp_title = $regs[1];
						//get sub paragraphs
						if (preg_match("/^(".$chapter."[.][.0-9]+) /i", $temp_title)) {
							$sub_link_name[] = $temp_title;
							$sub_help_id[] = $m['help_id'];
						}
					}
				}
			}
			else {
				F_display_db_error();
			}
			if (count($sub_link_name) > 0) {
				$page_body .= "<hr width=\"90%\" />";
				natcasesort($sub_link_name);
				$page_body .= F_create_xhtml_ordered_index($sub_link_name, $sub_help_id, $current_level);
			}
		}
	}
	
	if ($current_key > 0) {
		$menu_previous = "<a href=\"cp_show_page_help.".CP_EXT."?hp=".$help_index_key[$current_key-1]."\">&lt;&lt; ".$l['t_page_previous']."</a>";
	}
	else {
		$menu_previous = "&lt;&lt; ".$l['t_page_previous']."";
	}
	if ($current_key < count($help_index_key)-1) {
		$menu_next = "<a href=\"cp_show_page_help.".CP_EXT."?hp=".$help_index_key[$current_key+1]."\">".$l['t_page_next']." &gt;&gt;</a>";
	}
	else {
		$menu_next = "".$l['t_page_next']." &gt;&gt;";
	}
}
else { //display help index
	
	$page_body .= F_help_language($selected_language, $index_template); //display index introduction
	$page_body .= F_create_xhtml_ordered_index($help_index_name, $help_index_key, 0);
	
	$menu_previous = "&lt;&lt; ".$l['t_page_previous']."";
	$menu_next = "<a href=\"cp_show_page_help.".CP_EXT."?hp=".$help_index_key[0]."\">".$l['t_page_next']." &gt;&gt;</a>";
}

$menu_bar = "<center><small>".$menu_previous." | ".$menu_back." | ".$menu_index." | ".$menu_forward." | ".$menu_next."</small></center>";
$page_body = $menu_bar."<hr />".$page_body."<hr />".$menu_bar;
echo $page_body;

require_once('../code/cp_page_footer.'.CP_EXT);


// ------------------------------------------------------------
// Return XHTML ordered list of Index
// $link_name = titles
// $help_id = href links
// ------------------------------------------------------------
function F_create_xhtml_ordered_index($link_name, $help_id, $starting_level) {
	
	//sort arrays
	reset($link_name);
	
	$previous_level = $starting_level;
	
	//create xhtml index
	$index_list = "<ul>\n";
	while(list($key, $val) = each($link_name)) {
		if (preg_match("/^([.A0-9]+) /i", $val, $regs)) {
			$current_level = count(split('[.]', $regs[1])); //1=chapter, 2=paragraph,...
			if ($current_level == 1) {
				$val = "<b>".$val."</b>"; //bold chapters
			}
		}
		else {
			$current_level = $starting_level;
		}
		//indent
		if ($current_level > $previous_level) {
			$index_list = substr($index_list, 0, -5); //remove last </li> tag
			for ($j=$previous_level; $j<$current_level; $j++) {
				$index_list .= "<ul><li>";
			}
			$index_list = substr($index_list, 0, -4); //remove last <li> tag
		}
		//unindent
		elseif ($current_level < $previous_level) {
			for ($j=$current_level; $j<$previous_level; $j++) {
				$index_list .= "</ul></li>";
			}
		}
		$index_list .= "<li><a href=\"cp_show_page_help.".CP_EXT."?hp=".$help_id[$key]."\">".$val."</a></li>";
		$previous_level = $current_level;
	}
	
	//close remaining open tags
	for ($j=$starting_level; $j<$current_level-1; $j++) {
		$index_list .= "</ul></li>";
	}
	
	$index_list .= "</ul>\n";
	return $index_list;
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>