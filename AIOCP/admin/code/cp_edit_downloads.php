<?php
//============================================================+
// File name   : cp_edit_downloads.php                         
// Begin       : 2001-11-18                                    
// Last Update : 2008-07-06
//                                                             
// Description : Edit Downloads                                
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_DOWNLOADS;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

require_once('../../shared/code/cp_functions_form.'.CP_EXT);
require_once('../code/cp_functions_upload.'.CP_EXT);
require_once('../../shared/code/cp_functions_tree.'.CP_EXT);

$thispage_title = $l['t_download_editor'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages;

// Initialize variables
$userlevel = $_SESSION['session_user_level'];

if (isset($_REQUEST['menu_mode'])) {
	$menu_mode = $_REQUEST['menu_mode'];
} else {
	$menu_mode = "";
}
switch($menu_mode) {

	case unhtmlentities($l['w_delete']):
	case $l['w_delete']:{
		F_stripslashes_formfields(); // Delete target
		F_print_error("WARNING", $l['m_delete_confirm']);
		?>
		<p><?php echo $l['t_warning'].": ".$l['m_delete_confirm']; ?></p>
		
		<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_delete" id="form_delete">
		<input type="hidden" name="menu_mode" id="menu_mode" value="forcedelete" />
		<input type="hidden" name="forcedelete" id="forcedelete" value="" />
		<input type="hidden" name="download_id" id="download_id" value="<?php echo $download_id; ?>" />
		<?php 
		F_submit_button("form_delete","forcedelete",$l['w_delete']);
		F_submit_button("form_delete","forcedelete",$l['w_cancel']);
		?>
		</form>
		
		<?php
		break;
	}

	case "forcedelete":{ // Delete category and all associated messages and users
		if ($forcedelete == $l['w_delete']) { //check if delete button has been pushed (redundant check)
			$sql = "DELETE FROM ".K_TABLE_DOWNLOADS." WHERE download_id=".$download_id."";
			if(!$r = F_aiocpdb_query($sql, $db)) {
				F_display_db_error();
			}
			$download_id=FALSE;
		}
		break;
	}

	case unhtmlentities($l['w_update']):
	case $l['w_update']:{ // Update
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			if(!F_check_unique(K_TABLE_DOWNLOADS, "download_name='".$download_name."'", "download_id", $download_id)) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else {
				if($_FILES['userfile']['name']) {$download_link = F_upload_file("userfile", K_PATH_FILES_DOWNLOAD);} //upload file
				$download_description_small = addslashes(serialize($d_description_small));
				$download_description_large = addslashes(serialize($d_description_large));
				$download_limitations = addslashes(serialize($d_limitations));
				//check if is linked externally (not default directory)
				if(F_is_relative_link($download_link)) {
					$download_size = F_read_file_size(K_PATH_FILES_DOWNLOAD.$download_link);
				}
				else {
					$download_size = F_read_file_size($download_link);
				}
				
				$sql = "UPDATE IGNORE ".K_TABLE_DOWNLOADS." SET 
				download_category='".$download_category."', 
				download_name='".$download_name."', 
				download_link='".$download_link."', 
				download_size='".$download_size."', 
				download_description_small='".$download_description_small."',
				download_description_large='".$download_description_large."', 
				download_publisher_name='".$download_publisher_name."', 
				download_publisher_link='".$download_publisher_link."', 
				download_date='".$download_date."', 
				download_license='".$download_license."', 
				download_os='".$download_os."', 
				download_limitations='".$download_limitations."', 
				download_requisite='".$download_requisite."', 
				download_downloads='".$download_downloads."' 
				WHERE download_id=".$download_id."";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
			}
		}
		break;
	}

	case unhtmlentities($l['w_add']):
	case $l['w_add']:{ // Add
		if($formstatus = F_check_form_fields()) {
			//check if name is unique
			$sql = "SELECT download_id FROM ".K_TABLE_DOWNLOADS." WHERE download_name='".$download_name."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_name']);
				$formstatus = FALSE; F_stripslashes_formfields();
			}
			else { //add item
				if($_FILES['userfile']['name']) {$download_link = F_upload_file("userfile", K_PATH_FILES_DOWNLOAD);} //upload file
				$download_downloads = 0;
				$download_date = gmdate("Y-m-d H:i:s"); // get the actual date and time
				$download_description_small = addslashes(serialize($d_description_small));
				$download_description_large = addslashes(serialize($d_description_large));
				$download_limitations = addslashes(serialize($d_limitations));
				//check if is linked externally (not default directory)
				if(F_is_relative_link($download_link)) {
					$download_size = F_read_file_size(K_PATH_FILES_DOWNLOAD.$download_link);
				}
				else {
					$download_size = F_read_file_size($download_link);
				}
				$sql = "INSERT IGNORE INTO ".K_TABLE_DOWNLOADS." (
				download_category, 
				download_name, 
				download_link, 
				download_size, 
				download_description_small, 
				download_description_large, 
				download_publisher_name, 
				download_publisher_link, 
				download_date, 
				download_license, 
				download_os, 
				download_limitations, 
				download_requisite, 
				download_downloads
				) VALUES (
				'".$download_category."', 
				'".$download_name."', 
				'".$download_link."', 
				'".$download_size."', 
				'".$download_description_small."', 
				'".$download_description_large."', 
				'".$download_publisher_name."', 
				'".$download_publisher_link."', 
				'".$download_date."', 
				'".$download_license."', 
				'".$download_os."', 
				'".$download_limitations."', 
				'".$download_requisite."', 
				'".$download_downloads."'
				)";
				if(!$r = F_aiocpdb_query($sql, $db)) {
					F_display_db_error();
				}
				else {
					$download_id = F_aiocpdb_insert_id();
				}
			}
		}
		break;
	}

	case unhtmlentities($l['w_clear']):
	case $l['w_clear']:{ // Clear form fields
		$download_category = "";
		$download_name = "";
		$download_link = "";
		$download_size = "";
		$d_description_small = array();
		$d_description_large = array();
		$download_publisher_name = "";
		$download_publisher_link = "";
		$download_date = gmdate("Y-m-d H:i:s");
		$download_license = "";
		$download_os = "";
		$d_limitations = array();
		$download_requisite = "";
		break;
		}

	default :{ 
		break;
		}

} //end of switch


if(!isset($download_category) OR (!$download_category)) {
	$sql = "SELECT * FROM ".K_TABLE_DOWNLOADS_CATEGORIES." WHERE downcat_level<=".$userlevel." ORDER BY downcat_sub_id,downcat_position LIMIT 1";
	if($r = F_aiocpdb_query($sql, $db)) {
		if($m = F_aiocpdb_fetch_array($r)) {
			$download_category = $m['downcat_id'];
		}
	}
	else {
		F_display_db_error();
	}
}

if($formstatus) {
	if($download_category) {
		if (($menu_mode != $l['w_clear']) AND ($menu_mode != unhtmlentities($l['w_clear']))) {
			if((!isset($download_id) OR (!$download_id)) OR (isset($changecategory) AND $changecategory)) {
				$sql = "SELECT * FROM ".K_TABLE_DOWNLOADS." WHERE download_category=".$download_category." ORDER BY download_name LIMIT 1";
			}
			else {$sql = "SELECT * FROM ".K_TABLE_DOWNLOADS." WHERE download_id=".$download_id." LIMIT 1";}
			if($r = F_aiocpdb_query($sql, $db)) {
				if($m = F_aiocpdb_fetch_array($r)) {
					$download_id = $m['download_id'];
					$download_name = $m['download_name'];
					$download_link = $m['download_link'];
					$download_size = $m['download_size'];
					$download_description_small = $m['download_description_small'];
					$d_description_small = unserialize($download_description_small);
					$download_description_large = $m['download_description_large'];
					$d_description_large = unserialize($download_description_large);
					$download_publisher_name = $m['download_publisher_name'];
					$download_publisher_link = $m['download_publisher_link'];
					$download_date = $m['download_date'];
					$download_license = $m['download_license'];
					$download_os = $m['download_os'];
					$download_limitations = $m['download_limitations'];
					$d_limitations = unserialize($download_limitations);
					$download_requisite = $m['download_requisite'];
					$download_downloads = $m['download_downloads'];
				}
				else {
					$download_name = "";
					$download_link = "";
					$download_size = "";
					$d_description_small = array();
					$d_description_large = array();
					$download_publisher_name = "";
					$download_publisher_link = "";
					$download_date = gmdate("Y-m-d H:i:s");
					$download_license = "";
					$download_os = "";
					$d_limitations = array();
					$download_requisite = "";
				}
			}
			else {
				F_display_db_error();
			}
		}
	}
}
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_downloadeditor" id="form_downloadeditor">

<!-- comma separated list of required fields -->
<input type="hidden" name="ff_required" id="ff_required" value="download_name" />
<input type="hidden" name="ff_required_labels" id="ff_required_labels" value="<?php echo $l['w_name']; ?>" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT category ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_category', 'h_downloadcat_select'); ?></b></td>
<td class="fillOE">
<input type="hidden" name="changecategory" id="changecategory" value="0" />

<select name="download_category" id="download_category" size="0" onchange="document.form_downloadeditor.changecategory.value=1; document.form_downloadeditor.submit()">
<?php
$noscriptlink = $_SERVER['SCRIPT_NAME']."?";
if (isset($aiocp_dp) AND (!empty($aiocp_dp))) {
	$noscriptlink .= "aiocp_dp=".$aiocp_dp."&amp;";
}
$noscriptlink .= "changecategory=1&amp;";
$noscriptlink .= "download_category=";
F_form_select_tree($download_category, false, K_TABLE_DOWNLOADS_CATEGORIES, "downcat", $noscriptlink);
?>
</td>
</tr>
<!-- END SELECT category ==================== -->

<!-- SELECT download ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_download', 'h_downloaded_select'); ?></b></td>
<td class="fillEE">
<select name="download_id" id="download_id" size="0" onchange="document.form_downloadeditor.submit()">
<?php
if($download_category) {
	$sql = "SELECT * FROM ".K_TABLE_DOWNLOADS." WHERE download_category=".$download_category." ORDER BY download_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"".$m['download_id']."\"";
			if($m['download_id'] == $download_id) {
				echo " selected=\"selected\"";
			}
			echo ">".htmlentities($m['download_name'], ENT_NOQUOTES, $l['a_meta_charset'])."</option>\n";
		}
	}
	else {
		F_display_db_error();
	}
}
?>
</select>
</td>
</tr>
<!-- END SELECT download ==================== -->

<tr class="fillO">
<td class="fillOO">&nbsp;</td>
<td class="fillOE"><hr /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_name', 'h_downloaded_name'); ?></b></td>
<td class="fillEE"><input type="text" name="download_name" id="download_name" value="<?php echo htmlentities($download_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="50" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_link', 'h_downloaded_link'); ?></b></td>
<td class="fillOE"><input type="text" name="download_link" id="download_link" value="<?php echo $download_link; ?>" size="50" maxlength="255" /></td>
</tr>

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_download_dir', 'h_downloaded_dir'); ?></b></td>
<td class="fillEE" colspan="2">
<select name="download_link_dir" id="download_link_dir" size="0" onchange="document.form_downloadeditor.download_link.value=document.form_downloadeditor.download_link_dir.options[document.form_downloadeditor.download_link_dir.selectedIndex].value">
<?php
// read directory for files
$handle = opendir(K_PATH_FILES_DOWNLOAD);
echo "<option value=\"\" selected=\"selected\"> - </option>\n";
	while (false !== ($file = readdir($handle))) {
		if(($file != ".")AND($file != "..")) {
			echo "<option value=\"".$file."\"";
			if($file == $download_link) {
				echo " selected=\"selected\"";
			}
			echo ">".$file."</option>\n";
		}
	}
closedir($handle);
?>
</select>
</td>
</tr>

<!-- Upload file ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_upload', 'h_downloaded_upload'); ?></b></td>
<td class="fillOE">
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo K_MAX_UPLOAD_SIZE; ?>" />
<input type="file" name="userfile" id="userfile" size="20" />
</td>
</tr>
<!-- END Upload file ==================== -->

<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_publisher_name', 'h_downloaded_publisher_name'); ?></b></td>
<td class="fillEE"><input type="text" name="download_publisher_name" id="download_publisher_name" value="<?php echo htmlentities($download_publisher_name, ENT_COMPAT, $l['a_meta_charset']); ?>" size="50" maxlength="255" /></td>
</tr>

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_publisher_link', 'h_downloaded_publisher_link'); ?></b></td>
<td class="fillOE"><input type="text" name="download_publisher_link" id="download_publisher_link" value="<?php echo $download_publisher_link; ?>" size="50" maxlength="255" /></td>
</tr>

<!-- SELECT license ==================== -->
<tr class="fillE">
<td class="fillEO" align="right"><b><?php echo F_display_field_name('w_license', 'h_downloaded_license'); ?></b></td>
<td class="fillEE">
<select name="download_license" id="download_license" size="0">
<?php
	$sql = "SELECT * FROM ".K_TABLE_SOFTWARE_LICENSES." ORDER BY license_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			echo "<option value=\"".$m['license_id']."\"";
			if($m['license_id'] == $download_license) {
				echo " selected=\"selected\"";
			}
			echo ">".$m['license_name']."</option>\n";
		}
	}
	else {
		F_display_db_error();
	}
?>
</select>
</td>
</tr>
<!-- END SELECT license ==================== -->

<!-- SELECT os ==================== -->
<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_os', 'h_downloaded_os'); ?></b></td>
<td class="fillOE">
<select name="download_os" id="download_os" size="0">
<?php
$sql = "SELECT * FROM ".K_TABLE_SOFTWARE_OS." ORDER BY os_name";

if($r = F_aiocpdb_query($sql, $db)) {
	while($m = F_aiocpdb_fetch_array($r)) {
		echo "<option value=\"".$m['os_id']."\"";
		if($m['os_id'] == $download_os) {
			echo " selected=\"selected\"";
		}
		echo ">".$m['os_name']."</option>\n";
	}
}
else {
	F_display_db_error();
}
?>
</select>
</td>
</tr>
<!-- END SELECT os ==================== -->

<tr class="fillE">
<td class="fillEO" align="right" valign="top"><b><?php echo F_display_field_name('w_minimum_requirements', 'h_downloaded_minreq'); ?></b><br />
<?php 
$doc_charset = $l['a_meta_charset'];
F_html_button("page", "form_downloadeditor", "download_requisite", $doc_charset);

$current_ta_code = $download_requisite;
if(!$formstatus) {
	$current_ta_code = stripslashes($current_ta_code);
}
?>
</td>
<td class="fillEE"><textarea cols="50" rows="4" name="download_requisite" id="download_requisite"><?php echo htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset); ?></textarea>
</td>
</tr>

<!-- iterate for each language ==================== -->
<?php
	$sql = "SELECT * FROM ".K_TABLE_LANGUAGE_CODES." WHERE language_enabled=1 ORDER BY language_name";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			$doc_charset = F_word_language($m['language_code'], "a_meta_charset");
			if ($doc_charset) {
				$doc_charset_url = "&amp;charset=".$doc_charset;
			}
			else {
				$doc_charset_url = "";
			}
			
			if (!isset($d_description_small[$m['language_code']])) {
				$d_description_small[$m['language_code']] = "";
			}
			if (!isset($d_description_large[$m['language_code']])) {
				$d_description_large[$m['language_code']] = "";
			}
			if (!isset($d_limitations[$m['language_code']])) {
				$d_limitations[$m['language_code']] = "";
			}
			
			echo "<tr class=\"fillO\">";
			echo "<td class=\"fillOO\"><hr /></td>";
			echo "<td class=\"fillOE\"><b>".$m['language_name']."</b></td>";
			echo "</tr>";
			echo "<tr class=\"fillE\">";
			echo "<td class=\"fillEO\" align=\"right\"><b>".F_display_field_name('w_description_small', 'h_downloaded_smalldesc')."</b></td>";
			echo "<td class=\"fillEE\"><input type=\"text\" name=\"d_description_small[".$m['language_code']."]\" id=\"d_description_small_".$m['language_code']."\" value=\"".htmlentities(stripslashes($d_description_small[$m['language_code']]), ENT_COMPAT, $l['a_meta_charset'])."\" size=\"50\" maxlength=\"255\" /></td>";
			echo "</tr>";
			
			echo "<tr class=\"fillO\">";
			echo "<td class=\"fillOO\" align=\"right\" valign=\"top\"><b>".F_display_field_name('w_description', 'h_downloaded_description')."</b><br />";
?>
<input type="button" name="htmleditor_<?php echo $m['language_code']; ?>" id="htmleditor_<?php echo $m['language_code']; ?>" value="<?php echo $l['w_button_html_editor']; ?>" onclick="htmlWindow=window.open('cp_edit_html.<?php echo CP_EXT; ?>?templates=page&amp;callingform=form_downloadeditor&amp;callingfield=elements['+FJ_get_field_index(this)+']<?php echo $doc_charset_url; ?>','htmlWindow','dependent,height=500,width=800,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')" />
<?php
echo "</td>";
$current_ta_code = $d_description_large[$m['language_code']];
$current_ta_code = stripslashes($current_ta_code);
echo "<td class=\"fillOE\"><textarea cols=\"50\" rows=\"5\" name=\"d_description_large[".$m['language_code']."]\" id=\"d_description_large_".$m['language_code']."\">".htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset)."</textarea></td>";
echo "</tr>";
echo "<tr class=\"fillE\">";
echo "<td class=\"fillEO\" align=\"right\" valign=\"top\"><b>".F_display_field_name('w_limitations', 'h_downloaded_limits')."</b><br />";
?>
<input type="button" name="htmleditor_2<?php echo $m['language_code']; ?>" id="htmleditor_2<?php echo $m['language_code']; ?>" value="<?php echo $l['w_button_html_editor']; ?>" onclick="htmlWindow=window.open('cp_edit_html.<?php echo CP_EXT; ?>?templates=page&amp;callingform=form_downloadeditor&amp;callingfield=elements['+FJ_get_field_index(this)+']<?php echo $doc_charset_url; ?>','htmlWindow','dependent,height=500,width=800,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')" />
<?php
echo "</td>";
$current_ta_code = $d_limitations[$m['language_code']];
$current_ta_code = stripslashes($current_ta_code);
echo "<td class=\"fillEE\"><textarea cols=\"50\" rows=\"5\" name=\"d_limitations[".$m['language_code']."]\" id=\"d_limitations_".$m['language_code']."\">".htmlentities($current_ta_code, ENT_NOQUOTES, $doc_charset)."</textarea></td>";
echo "</tr>";
		}
	}
	else {
		F_display_db_error();
	}
?>
<!-- END iterate for each language ==================== -->

<tr class="fillO">
<td class="fillOO" align="right">&nbsp;</td>
<td class="fillOE"><a href="cp_edit_download_categories.<?php echo CP_EXT; ?>?downcat_id=<?php echo $download_category; ?>"><b>&lt;&lt;&nbsp;<?php echo $l['t_download_categories_editor']; ?></b></a></td>
</tr>

</table>
</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="download_date" id="download_date" value="<?php echo $download_date; ?>" />
<?php
if (!isset($download_downloads)) {
	$download_downloads = 0;
}
?>
<input type="hidden" name="download_downloads" id="download_downloads" value="<?php echo $download_downloads; ?>" />
<input type="hidden" name="download_size" id="download_size" value="<?php echo $download_size; ?>" />
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
if(isset($download_id) AND ($download_id > 0)) {
	F_submit_button("form_downloadeditor","menu_mode",$l['w_update']); 
	F_submit_button("form_downloadeditor","menu_mode",$l['w_delete']); 
}
F_submit_button("form_downloadeditor","menu_mode",$l['w_add']); 
F_submit_button("form_downloadeditor","menu_mode",$l['w_clear']); 
?>
</td>
</tr>
</table>
</form>
<!-- ====================================================== -->

<!-- Cange focus to download_id select -->
<script language="JavaScript" type="text/javascript">
//<![CDATA[
document.form_downloadeditor.download_id.focus();

//get next elements ID
function FJ_get_field_index(what) {
	for (var i=0;i<document.form_downloadeditor.elements.length;i++) {
		if(what == document.form_downloadeditor.elements[i]) {
			return(i+1);
		}
	}
	return (-1);
}
//]]>
</script>
<!-- END Cange focus to download_id select -->

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
