<?php
//============================================================+
// File name   : cp_functions_tree.php                         
// Begin       : 2001-09-06                                    
// Last Update : 2006-02-01                                    
//                                                             
// Description : functions for Handling data in Tree structure 
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

/**
* Display form select tree 
* @param $selected_item current selected item ID
* @param $tree_language language selector
* @param $tree_table table where to get tree data
* @param $tree_suffix table items suffix
* @param $noscriptlink link for noscript alternative
* @access public
*/
function F_form_select_tree($selected_item, $tree_language, $tree_table, $tree_suffix, $noscriptlink) {
	$output = F_form_select_tree_level(0, "", $selected_item, $tree_language, $tree_table, $tree_suffix, $noscriptlink);
	echo "".$output->script."</select>\n";
	// alternative links for search engines
	echo "<noscript>\n<ul>".$output->noscript."</noscript>\n";
}

/**
* Explore tree recursively
* Show the tree items in the right order with option buttons
* @param $treelevel current level
* @param $mlevel string for indentation
* @param $selected_item current selected item ID
* @param $tree_language language selector
* @param $tree_table table where to get tree data
* @param $tree_suffix table items suffix
* @param $noscriptlink link for noscript alternative
* @param $rc code to be returned
* @access public
*/
function F_form_select_tree_level($treelevel, $mlevel, $selected_item, $tree_language, $tree_table, $tree_suffix, $noscriptlink, $rc="") {
	global $l, $db, $selected_language;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	$indent = "&nbsp;&nbsp;&nbsp;";
	
	if ($tree_language) {
		$wherestr = " AND (".$tree_suffix."_language='".$tree_language."')";
	}
	else {
		$wherestr = "";
	}
	
	$sql = "SELECT * FROM ".$tree_table." WHERE (".$tree_suffix."_sub_id=".$treelevel.")".$wherestr." ORDER BY ".$tree_suffix."_sub_id, ".$tree_suffix."_position";
	$mlevel .= $indent;
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			if ($tree_language) {
				$item_name = $m[$tree_suffix.'_name'];
			}
			else {
				$item_name = unserialize($m[$tree_suffix.'_name']);
				$item_name = $item_name[$selected_language];
			}
			
			$rc->script .= "<option class=\"cat".strlen($mlevel)/strlen($indent)."\" value=\"".$m[$tree_suffix.'_id']."\"";
			if($m[$tree_suffix.'_id'] == $selected_item) {
				$rc->script .= " selected=\"selected\"";
			}
			$rc->script .= ">".$mlevel."".htmlentities($item_name, ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
			$rc->noscript .= "<li><a href=\"".$noscriptlink."".$m[$tree_suffix.'_id']."\">".htmlentities($item_name, ENT_NOQUOTES, $l['a_meta_charset'])."</a>";
			
			if(!$m[$tree_suffix.'_item']) { //is a node (not the first)
				$rc->noscript .= "<ul>\n";
				$nc = F_form_select_tree_level($m[$tree_suffix.'_id'], $mlevel, $selected_item, $tree_language, $tree_table, $tree_suffix, $noscriptlink);
				$rc->script .= $nc->script;
				$rc->noscript .= $nc->noscript;
			} else {
				$rc->noscript .= "</li>\n";
			}
		}
	}
	else {
		F_display_db_error();
	}
	$rc->noscript .= "</ul>\n";
	return $rc;
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
