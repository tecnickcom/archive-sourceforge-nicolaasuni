<?php
//============================================================+
// File name   : cp_edit_links.php                             
// Begin       : 2001-09-21                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit links                                    
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_LINKS;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

require_once('../../shared/code/cp_functions_form.'.CP_EXT);
require_once('../../shared/code/cp_functions_tree.'.CP_EXT);
require_once('cp_functions_checklinks.'.CP_EXT);

$thispage_title = $l['t_links_editor'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages;

if (isset($_REQUEST['menu_mode'])) {
	$menu_mode = $_REQUEST['menu_mode'];
} else {
	$menu_mode = "";
}

switch($menu_mode) {

	case unhtmlentities($l['w_delete']):
	case $l['w_delete']:{
		F_stripslashes_formfields(); // ask confirmation
		F_print_error("WARNING", $l['m_delete_confirm']);
		?>
		<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_delete" id="form_delete">
		<input type="hidden" name="menu_mode" id="menu_mode" value="forcedelete" />
		<input type="hidden" name="links_category" id="links_category" value="<?php echo $links_category; ?>" />
		<input type="hidden" name="links_id" id="links_id" value="<?php echo $links_id; ?>" />
		<input type="hidden" name="forcedelete" id="forcedelete" value="" />
		<?php 
		F_submit_button("form_delete","forcedelete",$l['w_delete']);
		F_submit_button("form_delete","forcedelete",$l['w_cancel']);
		?>
		</form>
		<?php
		break;
	}

	case 'forcedelete':{
		F_stripslashes_formfields(); // Delete
		if($forcedelete == $l['w_delete']) { //check if delete button has been pushed
			$sql = "DELETE FROM ".K_TABLE_LINKS." WHERE links_id=".$links_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			$links_id=FALSE;
		}
		break;
		}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update links
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_LINKS, "links_name='".$links_name."'", "links_id", $links_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				if (($links_verify) AND (!F_checklink($links_link))) { // broken link
					F_print_error("WARNING", $l['m_link_broken']);
					$formstatus = FALSE; F_stripslashes_formfields();
				}
				else {
					$links_description = addslashes(serialize($l_description));
					$sql = "UPDATE IGNORE ".K_TABLE_LINKS." SET 
					links_category='".$links_category."', 
					links_name='".$links_name."', 
					links_link='".$links_link."', 
					links_description='".$links_description."' 
					WHERE links_id=".$links_id."";
					if(!$r = F_aiocpdb_query($sql, $db)) {
						F_display_db_error();
					}
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_add']):
	case $l['w_add']:{ // Add links
		if($formstatus = F_check_form_fields()) {
			//check if links is unique
			$sql = "SELECT links_id FROM ".K_TABLE_LINKS." WHERE links_name='".$links_name."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //check link validity
				if (($links_verify) AND (!F_checklink($links_link))) { // broken link
					F_print_error("WARNING", $l['m_link_broken']);
					$formstatus = FALSE; F_stripslashes_formfields();
				}
				else {
					//add item
					$links_description = addslashes(serialize($l_description));
					$sql = "INSERT IGNORE INTO ".K_TABLE_LINKS." (
					links_category, 
					links_name, 
					links_link, 
					links_description
					) VALUES (
					'".$links_category."', 
					'".$links_name."', 
					'".$links_link."', 
					'".$links_description."')";
					if(!$r = F_aiocpdb_query($sql, $db)) {
						F_display_db_error();
					}
					else {
						$links_id = F_aiocpdb_insert_id();
					}
				}
			}
		}
		break;
		}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$links_name = "";
		$links_link = "";
		$l_description = array();
		break;
		}

	default :{ 
		break;
		}

} //end of switch


// Initialize variables
$links_verify = 1;

if(!isset($links_category) OR (!$links_category)) {
	$sql = "SELECT * FROM ".K_TABLE_LINKS_CATEGORIES." ORDER BY linkscat_sub_id,linkscat_position LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$links_category = $m['linkscat_id'];
		}
	}
	else {
		F_display_db_error();
	}
}

if($formstatus) {
	if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
		if((!isset($links_id) OR (!$links_id)) OR (isset($changecategory) AND $changecategory)) {
			$sql = "SELECT * FROM ".K_TABLE_LINKS." WHERE links_category=".$links_category." ORDER BY links_name LIMIT 1";
		}
		else {$sql = "SELECT * FROM ".K_TABLE_LINKS." WHERE links_id=".$links_id." LIMIT 1";}
		if($r = F_aiocpdb_query($sql, $db)) {
			if($m = F_aiocpdb_fetch_array($r)) {
				$links_id = $m['links_id'];
				$links_category = $m['links_category'];
				$links_name = $m['links_name'];
				$links_link = $m['links_link'];
				$links_description = $m['links_description'];
				$l_description = unserialize($links_description);
			}
			else {
				$links_name = "";
				$links_link = "";
				$l_description = array();
			}
		}
		else {
			F_display_db_error();
		}
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_linkseditor" id="form_linkseditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="links_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT category ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_category', 'h_linkscat_select'); ?></b></td>
<td class="fillOE">
<select name="links_category" id="links_category" size="0" onchange="document.form_linkseditor.changecategory.value=1; document.form_linkseditor.submit()">
<?php
$noscriptlink = $_SERVER['SCRIPT_NAME']."?";
if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {
	$noscriptlink .= "aiocp_dp=".$aiocp_dp."&amp;";
}
$noscriptlink .= "changecategory=1&amp;";
$noscriptlink .= "links_category=";
F_form_select_tree($links_category, false, K_TABLE_LINKS_CATEGORIES, "linkscat", $noscriptlink);
?>
</td>
</tr>
<!-- END SELECT category ==================== -->

<!-- SELECT links ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_link', 'h_linksed_link'); ?></b></td>
<td class="fillEE">
<select name="links_id" id="links_id" size="0" onchange="document.form_linkseditor.submit()">
<?php
$sql = "SELECT * FROM ".K_TABLE_LINKS." WHERE links_category=".$links_category." ORDER BY links_name";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['links_id']."\"";
		if($m['links_id'] == $links_id) {
			echo " selected=\"selected\"";
		}
		//echo ">".htmlentities($m['links_name'], ENT_NOQUOTES, $l['a_meta_charset'])." (".$m['links_link'].")</option>\n";
	echo ">".htmlentities($m['links_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT links ==================== -->

<tr class="fillO">
<td class="fillOO">&nbsp;</td>
<td class="fillOE"><hr /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_name', 'h_linksed_name'); ?></b></td>
<td class="fillEE"><input type="text" name="links_name" id="links_name" value="<?php echo htmlentities($links_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_url', 'h_linksed_url'); ?></b></td>
<td class="fillOE"><input type="text" name="links_link" id="links_link" value="<?php echo $links_link; ?>" size="30" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_verify', 'h_linksed_verify'); ?></b></td>
<td class="fillEE">
<?php
echo "<input type=\"checkbox\" name=\"links_verify\" id=\"links_verify\" value=\"1\"";
if ($links_verify) { echo "checked";}
echo" />";
?>
</td>
</tr>

<!-- iterate for each language ==================== -->
<?php
	$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<tr class=\"fillE\">";
			echo "<td class=\"fillEO\"><hr /></td>";
			echo "<td class=\"fillEE\"><b>".$m['language_name']."</b></td>";
			echo "</tr>";
			echo "<tr class=\"fillO\">";
			echo "<td class=\"fillOO\" align=\"right\" valign=\"top\"><b>".F_display_field_name('w_description', '')."</b><br />";
			
			$doc_charset = F_word_language($m['language_code'], "a_meta_charset");
			if ($doc_charset) {
				$doc_charset_url = "&amp;charset=".$doc_charset;
			}
			else {
				$doc_charset_url = "";
			}
?>
<input type="button" name="htmleditor_<?php echo $m['language_code']; ?>" id="htmleditor_<?php echo $m['language_code']; ?>" value="<?php echo $l['w_button_html_editor']; ?>" onclick="htmlWindow=window.open('cp_edit_html.<?php echo CP_EXT; ?>?templates=page&amp;callingform=form_linkseditor&amp;callingfield=elements['+FJ_get_field_index(this)+']<?php echo $doc_charset_url; ?>','htmlWindow','dependent,height=500,width=800,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')" />
<?php
echo "</td>";
if (isset($l_description[$m['language_code']])) {
	$current_ta_code = $l_description[$m['language_code']];
} else {
	$current_ta_code = "";
}
$current_ta_code = stripslashes($current_ta_code);
echo "<td class=\"fillOE\"><textarea cols=\"50\" rows=\"5\" name=\"l_description[".$m['language_code']."]\" id=\"l_description_".$m['language_code']."\">".htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset)."</textarea></td>";
echo "</tr>";
		}
	}
	else {
		F_display_db_error();
	}
?>
<!-- END iterate for each language ==================== -->
<?php
if (isset($links_category) AND ($links_category > 0)) {
?>
<tr class="fillE">
<td class="fillEO" align="right">&nbsp;</td>
<td class="fillEE"><a href="cp_edit_links_categories.<?php echo CP_EXT; ?>?linkscat_id=<?php echo $links_category; ?>"><b>&lt;&lt;&nbsp;<?php echo $l['t_links_categories_editor']; ?></b></a></td>
</tr>
<?php
}
?>
</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="changecategory" id="changecategory" value="0" />
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if (isset($links_id) AND ($links_id > 0)) {
	F_submit_button("form_linkseditor","menu_mode",$l['w_update']); 
	F_submit_button("form_linkseditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_linkseditor","menu_mode",$l['w_add']); 
F_submit_button("form_linkseditor","menu_mode",$l['w_clear']); 
?>
</td>

</tr>
</table>
</form>
<!-- ====================================================== -->

<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_linkseditor.links_id.focus();

//get next elements ID
function FJ_get_field_index(what) {
	for (var i=0;i<document.form_linkseditor.elements.length;i++) {
		if(what == document.form_linkseditor.elements[i]) {
			return(i+1);
		}
	}
	return (-1);
}
//]]>
</script>

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
