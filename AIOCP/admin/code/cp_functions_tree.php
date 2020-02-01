<?php
//============================================================+
// File name   : cp_functions_tree.php                         
// Begin       : 2001-09-06                                    
// Last Update : 2003-10-12                                    
//                                                             
// Description : Functions for Handling data in Tree structure 
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
* Make a position space encreasing by 1 the successive 
* tree_position values for entries with the same sub_id
* @param 
* @access public
*/
function F_add_tree_position($tree_sub_id, $tree_position, $tree_language, $tree_table, $tree_suffix) {
	global $db;
	
	if ($tree_language) {
		$wherestr = " AND (".$tree_suffix."_language='".$tree_language."')";
	}
	else {
		$wherestr = "";
	}
	
	$sql = "UPDATE IGNORE ".$tree_table." SET ".$tree_suffix."_position=(".$tree_suffix."_position+1) WHERE ( (".$tree_suffix."_sub_id=".$tree_sub_id.") AND (".$tree_suffix."_position>=".$tree_position.")".$wherestr.")";
	if(!$r = F_aiocpdb_query($sql, $db)) {
		F_display_db_error();
	}
}

/**
* Eliminate a position space decreasing by 1 the successive 
* tree_position values for entries with the same sub_id
* @param 
* @access public
*/
function F_delete_tree_position($tree_sub_id, $tree_position, $tree_language, $tree_table, $tree_suffix) {
	global $db;
	
	if ($tree_language) {
		$wherestr = " AND (".$tree_suffix."_language='".$tree_language."')";
	}
	else {
		$wherestr = "";
	}
	
	$sql = "UPDATE IGNORE ".$tree_table." SET ".$tree_suffix."_position=(".$tree_suffix."_position-1) WHERE ((".$tree_suffix."_sub_id=".$tree_sub_id.") AND (".$tree_suffix."_position>=".$tree_position.")".$wherestr.")";
	if(!$r = F_aiocpdb_query($sql, $db)) {
		F_display_db_error();
	}
}

/**
* Retrieve tree_id 
* @param 
* @access public
*/
function F_retrieve_tree_id($tree_sub_id, $tree_position, $tree_language, $tree_table, $tree_suffix) {
	global $db;
	
	if ($tree_language) {
		$wherestr = " AND (".$tree_suffix."_language='".$tree_language."')";
	}
	else {
		$wherestr = "";
	}
	
	$sql = "SELECT ".$tree_suffix."_id FROM ".$tree_table." WHERE ((".$tree_suffix."_sub_id=".$tree_sub_id.") AND (".$tree_suffix."_position=".$tree_position.")".$wherestr.") LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			return ($m[$tree_suffix.'_id']);
		}
	}
	else {
		F_display_db_error();
	}
	return FALSE;
}

/**
* Search and return the last position for the current path
* @param 
* @access public
*/
function F_search_tree_position($tree_sub_id, $tree_language, $tree_table, $tree_suffix) {
	global $db;
	
	if ($tree_language) {
		$wherestr = " AND (".$tree_suffix."_language='".$tree_language."')";
	}
	else {
		$wherestr = "";
	}
	
	$sql = "SELECT COUNT(*) FROM ".$tree_table." WHERE (".$tree_suffix."_sub_id=".$tree_sub_id.")".$wherestr." LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			return ($m['0']);
		}
	}
	else {
		F_display_db_error();
	}
	return(0);
}

/**
* Convert node to item if necessary
* @param 
* @access public
*/
function F_fix_node_to_item($tree_sub_id, $tree_language, $tree_table, $tree_suffix) {
	global $db;
	
	if ($tree_language) {
		$wherestr = " AND (".$tree_suffix."_language='".$tree_language."')";
	}
	else {
		$wherestr = "";
	}
	
	if(!F_search_tree_position($tree_sub_id, $tree_language, $tree_table, $tree_suffix)) {
		$sql = "UPDATE IGNORE ".$tree_table." SET ".$tree_suffix."_item=1 WHERE (".$tree_suffix."_id=".$tree_sub_id.")".$wherestr."";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		return TRUE;
	}
	return FALSE;
}

/**
* Search and return the min value of sub_id 
* (tree_id of first level)
* @param 
* @access public
*/
function F_search_tree_min_sub_id($tree_language, $tree_table, $tree_suffix) {
	global $db;
	
	if ($tree_language) {
		$wherestr = " WHERE (".$tree_suffix."_language='".$tree_language."')";
	}
	else {
		$wherestr = "";
	}
	
	$sql = "SELECT MIN(".$tree_suffix."_sub_id) FROM ".$tree_table."".$wherestr." LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			return ($m['0']);
		}
	}
	else {
		F_display_db_error();
	}
	return(0);
}

/**
* Search and return the min value of sub_id 
* (tree_id of first level)
* @param 
* @access public
*/
function F_delete_subtrees($tree_id, $tree_language, $tree_table, $tree_suffix) {
	global $db;
	
	if ($tree_language) {
		$wherestr = " AND (".$tree_suffix."_language='".$tree_language."')";
	}
	else {
		$wherestr = "";
	}
	
	$sql = "SELECT * FROM ".$tree_table." WHERE (".$tree_suffix."_sub_id=".$tree_id.")".$wherestr."";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			$sql2 = "DELETE FROM ".$tree_table." WHERE (".$tree_suffix."_id=".$m[$tree_suffix.'_id'].")".$wherestr."";
			if(!$r2 = F_aiocpdb_query($sql2, $db)) {
				F_display_db_error();
			}
			if(!$m[$tree_suffix.'_item']) { // delete item
				F_delete_subtrees($m[$tree_suffix.'_id'], $tree_language, $tree_table, $tree_suffix);
			}
		}
	}
	else {
		F_display_db_error();
	}
}

/**
* Delete item and subtree 
* @param 
* @access public
*/
function F_delete_item($tree_id, $tree_item, $tree_sub_id, $tree_position, $tree_language, $tree_table, $tree_suffix) {
	global $db;
	
	if ($tree_language) {
		$wherestr = " AND (".$tree_suffix."_language='".$tree_language."')";
	}
	else {
		$wherestr = "";
	}
	
	if(!$tree_item){ //Delete subtrees
		F_delete_subtrees($tree_id, $tree_language, $tree_table, $tree_suffix);
	}
	
	//delete item
	$sql = "DELETE FROM ".$tree_table." WHERE (".$tree_suffix."_id=".$tree_id.")".$wherestr."";
	if(!$r = F_aiocpdb_query($sql, $db)) {
		F_display_db_error();
	}
	
	F_delete_tree_position($tree_sub_id, $tree_position, $tree_language, $tree_table, $tree_suffix);
	// convert node to item if necessary
	F_fix_node_to_item($tree_sub_id, $tree_language, $tree_table, $tree_suffix);
}

/**
* Move item 1 position up
* @param 
* @access public
*/
function F_move_up_tree_item($tree_id, $tree_sub_id, $tree_position, $tree_language, $tree_table, $tree_suffix) {
	global $db;
	
	if ($tree_language) {
		$wherestr = " AND (".$tree_suffix."_language='".$tree_language."')";
	}
	else {
		$wherestr = "";
	}
	
	if($tree_position > 1) { //element is not the first
		$new_position = $tree_position - 1;
		$prev_id = F_retrieve_tree_id($tree_sub_id, $new_position, $tree_language, $tree_table, $tree_suffix);
		//change next item position
		$sql = "UPDATE IGNORE ".$tree_table." SET ".$tree_suffix."_position=".$tree_position." WHERE (".$tree_suffix."_id=".$prev_id.")".$wherestr."";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		//change node position
		$sql = "UPDATE IGNORE ".$tree_table." SET ".$tree_suffix."_position=".$new_position." WHERE (".$tree_suffix."_id=".$tree_id.")".$wherestr."";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		return TRUE;
	}
	return FALSE;
}

/**
* Move item 1 position down
* @param 
* @access public
*/
function F_move_down_tree_item($tree_id, $tree_sub_id, $tree_position, $tree_language, $tree_table, $tree_suffix) {
	global $db;
	
	if ($tree_language) {
		$wherestr = " AND (".$tree_suffix."_language='".$tree_language."')";
	}
	else {
		$wherestr = "";
	}
	
	$last_position = F_search_tree_position($tree_sub_id, $tree_language, $tree_table, $tree_suffix);
	
	if($tree_position < $last_position) { //element is not the last
		$new_position = $tree_position + 1;
		$next_id = F_retrieve_tree_id($tree_sub_id, $new_position, $tree_language, $tree_table, $tree_suffix);
		//change next item position
		$sql = "UPDATE IGNORE ".$tree_table." SET ".$tree_suffix."_position=".$tree_position." WHERE (".$tree_suffix."_id=".$next_id.")".$wherestr."";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		//change node position
		$sql = "UPDATE IGNORE ".$tree_table." SET ".$tree_suffix."_position=".$new_position." WHERE (".$tree_suffix."_id=".$tree_id.")".$wherestr."";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		return TRUE;
	}
	return FALSE;
}

/**
* Move item and subtree 1 level up
* @param 
* @access public
*/
function F_move_back_tree_item($tree_id, $tree_sub_id, $tree_position, $tree_language, $tree_table, $tree_suffix) {
	global $db;
	
	if ($tree_language) {
		$wherestr = " AND (".$tree_suffix."_language='".$tree_language."')";
	}
	else {
		$wherestr = "";
	}
	
	$min_sub_id = F_search_tree_min_sub_id($tree_language, $tree_table, $tree_suffix);
	
	if($tree_sub_id > $min_sub_id){ //Element cannot be moved if first level reached
		//Get node information
		$sql = "SELECT * FROM ".$tree_table." WHERE (".$tree_suffix."_id=".$tree_sub_id.")".$wherestr." LIMIT 1";
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$node_sub_id = $m[$tree_suffix.'_sub_id'];
				$new_position = $m[$tree_suffix.'_position']+1;
			}
		}
		else {
			F_display_db_error(); 
		}
		
		// Restore the positions in the new level
		F_add_tree_position($node_sub_id, $new_position, $tree_language, $tree_table, $tree_suffix);
		
		// Move back the level
		$sql = "UPDATE IGNORE ".$tree_table." SET ".$tree_suffix."_sub_id=".$node_sub_id.", ".$tree_suffix."_position=".$new_position." WHERE (".$tree_suffix."_id=".$tree_id.")".$wherestr."";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		// convert node to item if necessary
		F_fix_node_to_item($tree_sub_id, $tree_language, $tree_table, $tree_suffix);
		return TRUE;
	}
	return FALSE;
}

/**
* Move item and subtree 1 level down
* @param 
* @access public
*/
function F_move_forward_tree_item($tree_id, $tree_sub_id, $tree_position, $tree_language, $tree_table, $tree_suffix) {
	global $db;
	
	if ($tree_language) {
		$wherestr = " AND (".$tree_suffix."_language='".$tree_language."')";
	}
	else {
		$wherestr = "";
	}
	
	if($tree_position > 1){ //Element cannot be the first
		//Set variables for the node
		$node_position = $tree_position -1;
		$node_id = F_retrieve_tree_id($tree_sub_id, $node_position, $tree_language, $tree_table, $tree_suffix);
		
		// Make previous item a node (menu_item=0)
		$sql = "UPDATE IGNORE ".$tree_table." SET ".$tree_suffix."_item=0 WHERE (".$tree_suffix."_id=".$node_id.")".$wherestr."";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		//Calculate new position
		$new_position = F_search_tree_position($node_id, $tree_language, $tree_table, $tree_suffix) + 1;
		
		// Move the item inside
		$sql = "UPDATE IGNORE ".$tree_table." SET ".$tree_suffix."_sub_id=".$node_id.", ".$tree_suffix."_position=".$new_position." WHERE (".$tree_suffix."_id=".$tree_id.")".$wherestr."";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		// Restore the positions in the old level
		F_delete_tree_position($tree_sub_id, $tree_position, $tree_language, $tree_table, $tree_suffix);
		return TRUE;
	}
	return FALSE;
}

/**
* Explore tree tree
* @param 
* @access public
*/
function F_explore_tree($tree_language, $tree_table, $tree_suffix) {
	echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
	F_explore_tree_level(0, "", $tree_language, $tree_table, $tree_suffix);
	echo "</table>";
}

/**
* Explore tree recursively
* Show the tree items in the right order with option buttons
* @param 
* @access public
*/
function F_explore_tree_level($treelevel, $mlevel, $tree_language, $tree_table, $tree_suffix) {
	global $l, $db, $selected_language;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	
	if ($tree_language) {
		$wherestr = " AND (".$tree_suffix."_language='".$tree_language."')";
	}
	else {
		$wherestr = "";
	}
	
	// -- buttons:
	$opt_addbefore = "<img src=\"".K_PATH_IMAGES."keypad/off/plus.gif\" width=\"17\" height=\"17\" border=\"0\" alt=\"".$l['d_addbefore']."\" />";
	$opt_addafter = "<img src=\"".K_PATH_IMAGES."keypad/off/plus.gif\" width=\"17\" height=\"17\" border=\"0\" alt=\"".$l['d_addafter']."\" />";
	$opt_delete = "<img src=\"".K_PATH_IMAGES."keypad/off/mult.gif\" width=\"17\" height=\"17\" border=\"0\" alt=\"".$l['w_delete']."\" />";
	$opt_moveup = "<img src=\"".K_PATH_IMAGES."keypad/off/up.gif\" width=\"17\" height=\"17\" border=\"0\" alt=\"".$l['d_moveup']."\" />";
	$opt_movedown = "<img src=\"".K_PATH_IMAGES."keypad/off/down.gif\" width=\"17\" height=\"17\" border=\"0\" alt=\"".$l['d_movedown']."\" />";
	$opt_moveback = "<img src=\"".K_PATH_IMAGES."keypad/off/back.gif\" width=\"17\" height=\"17\" border=\"0\" alt=\"".$l['d_moveback']."\" />";
	$opt_moveforward = "<img src=\"".K_PATH_IMAGES."keypad/off/forward.gif\" width=\"17\" height=\"17\" border=\"0\" alt=\"".$l['d_moveforward']."\" />";
    // -- END buttons:
	$sql = "SELECT * FROM ".$tree_table." WHERE (".$tree_suffix."_sub_id=".$treelevel.")".$wherestr." ORDER BY ".$tree_suffix."_sub_id, ".$tree_suffix."_position";
	$mlevel .= "|&nbsp;&nbsp;";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<tr>";
			echo "<td>";
			echo $mlevel;
			
			if ($tree_language) {
				$stradd = "&amp;".$tree_suffix."_language=".$tree_language."";
				$item_name = $m[$tree_suffix.'_name'];
			}
			else {
				$stradd = "";
				$item_name = unserialize($m[$tree_suffix.'_name']);
				$item_name = $item_name[$selected_language];
			}
			
			echo "<a href=\"".htmlentities(urldecode($_SERVER['SCRIPT_NAME']))."?".$tree_suffix."_id=".$m[$tree_suffix.'_id']."".$stradd."&amp;".$tree_suffix."_table=".$tree_table."&amp;menu_mode=refresh\">";
			if (isset($m['menu_enabled']) AND (!$m['menu_enabled'])) {
				echo "".$item_name."";
			}
			else {
				echo "<b>".$item_name."</b>";
			}
			echo "</a>&nbsp;";
			echo "</td>";
			
			echo "<td>";
			echo "<a href=\"".htmlentities(urldecode($_SERVER['SCRIPT_NAME']))."?".$tree_suffix."_id=".$m[$tree_suffix.'_id']."".$stradd."&amp;".$tree_suffix."_table=".$tree_table."&amp;menu_mode=addbefore\">".$opt_addbefore."</a>";
			echo "<a href=\"".htmlentities(urldecode($_SERVER['SCRIPT_NAME']))."?".$tree_suffix."_id=".$m[$tree_suffix.'_id']."".$stradd."&amp;".$tree_suffix."_table=".$tree_table."&amp;menu_mode=delete\">".$opt_delete."</a>";
			echo "<a href=\"".htmlentities(urldecode($_SERVER['SCRIPT_NAME']))."?".$tree_suffix."_id=".$m[$tree_suffix.'_id']."".$stradd."&amp;".$tree_suffix."_table=".$tree_table."&amp;menu_mode=addafter\">".$opt_addafter."</a>";
			echo "<a href=\"".htmlentities(urldecode($_SERVER['SCRIPT_NAME']))."?".$tree_suffix."_id=".$m[$tree_suffix.'_id']."".$stradd."&amp;".$tree_suffix."_table=".$tree_table."&amp;menu_mode=moveup\">".$opt_moveup."</a>";
			echo "<a href=\"".htmlentities(urldecode($_SERVER['SCRIPT_NAME']))."?".$tree_suffix."_id=".$m[$tree_suffix.'_id']."".$stradd."&amp;".$tree_suffix."_table=".$tree_table."&amp;menu_mode=movedown\">".$opt_movedown."</a>";
			echo "<a href=\"".htmlentities(urldecode($_SERVER['SCRIPT_NAME']))."?".$tree_suffix."_id=".$m[$tree_suffix.'_id']."".$stradd."&amp;".$tree_suffix."_table=".$tree_table."&amp;menu_mode=moveback\">".$opt_moveback."</a>";
			echo "<a href=\"".htmlentities(urldecode($_SERVER['SCRIPT_NAME']))."?".$tree_suffix."_id=".$m[$tree_suffix.'_id']."".$stradd."&amp;".$tree_suffix."_table=".$tree_table."&amp;menu_mode=moveforward\">".$opt_moveforward."</a>";
			echo "</td>";
			echo "</tr>";
			if(!$m[$tree_suffix.'_item']){ //is a node (not the first)
				F_explore_tree_level($m[$tree_suffix.'_id'], $mlevel, $tree_language, $tree_table, $tree_suffix);
			}
		}
	}
	else {
		F_display_db_error();
	}
return;
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
