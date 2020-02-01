<?php
//============================================================+
// File name   : cp_edit_newsletter_attachments.php
// Begin       : 2001-10-19
// Last Update : 2007-03-29
// 
// Description : Edit newsletter attachments
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
require_once('../code/cp_functions_newsletter_edit.'.CP_EXT);
require_once('../../shared/code/cp_functions_newsletter.'.CP_EXT);
require_once('../../shared/code/cp_functions_form.'.CP_EXT);

$pagelevel = K_AUTH_ADMIN_CP_EDIT_NEWSLETTER_ATTACHMENTS;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_newsletter_attachments_editor'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages; ?>
<!-- ====================================================== -->
<?php
switch($menu_mode) {

	case unhtmlentities($l['w_delete']):
	case $l['w_delete']: { // print confirmation message for hard delete
		F_print_error("WARNING", $l['m_delete_confirm']);
		?>
		
		<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_delete" id="form_delete">
		<input type="hidden" name="nlattach_nlmsgid" id="nlattach_nlmsgid" value="<?php echo $nlattach_nlmsgid; ?>" />
		<input type="hidden" name="nlattach_id" id="nlattach_id" value="<?php echo $nlattach_id; ?>" />
		<input type="hidden" name="menu_mode" id="menu_mode" value="forcedelete" />
		<input type="hidden" name="nlattach_file" id="nlattach_file" value="<?php echo $nlattach_file; ?>" />
		<b><?php echo $nlattach_file; ?></b><br />
		<input type="hidden" name="forcedelete" id="forcedelete" value="" />
		<?php 
		F_submit_button("form_delete","forcedelete",$l['w_delete']);
		F_submit_button("form_delete","forcedelete",$l['w_cancel']);
		?>
		</form>
		
		<?php
		break;
	}

	case "forcedelete":{ // hard delete (remove file from disk)
		if($forcedelete == $l['w_delete']) { //check if delete button has been pushed
			$filetodelete = K_PATH_FILES_ATTACHMENTS.$nlattach_file;
			F_delete_attachment($filetodelete);
		}
		else {
			break;
		}
	}

	case "softdelete":{ // delete from database
		$sql = "DELETE FROM ".K_TABLE_NEWSLETTER_ATTACHMENTS." WHERE nlattach_id=".$nlattach_id."";
		if(!$r = F_aiocpdb_query($sql, $db)) {
			F_display_db_error();
		}
		break;
	}

	case unhtmlentities($l['w_upload']):
	case $l['w_upload']:{ // Add
		if($_FILES['userfile']['name']) {$nlattach_file = F_upload_newsletter_attachment();} //upload file
	}

	case unhtmlentities($l['w_add']):
	case $l['w_add']:{ // Add
		if($formstatus = F_check_form_fields()) {
			//check if nlattach_file is unique
			$sql = "SELECT nlattach_file FROM ".K_TABLE_NEWSLETTER_ATTACHMENTS." WHERE nlattach_nlmsgid=".$nlattach_nlmsgid." AND  nlattach_file='".$nlattach_file."'";
			if(($r = F_aiocpdb_query($sql, $db))AND(F_aiocpdb_num_rows($r))) {
				F_print_error("WARNING", $l['m_duplicate_file']);
			}
			else { //add item
				if($nlattach_file) {
					$sql = "INSERT IGNORE INTO ".K_TABLE_NEWSLETTER_ATTACHMENTS." (
					nlattach_nlmsgid, 
					nlattach_file, 
					nlattach_cid
					) VALUES (
					'".$nlattach_nlmsgid."', 
					'".$nlattach_file."', 
					'0')";
					if(!$r = F_aiocpdb_query($sql, $db)) {
						F_display_db_error();
					}
					else {
						$nlattach_id = F_aiocpdb_insert_id();
					}
				}
			}
		}
		break;
	}

	default :{ 
		break;
	}

} //end of switch
?>

<!-- ====================================================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_nlattacheditor" id="form_nlattacheditor">

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<th class="edge">
<?php 
$sql = "SELECT * FROM ".K_TABLE_NEWSLETTER_MESSAGES." WHERE nlmsg_id=".$nlattach_nlmsgid." LIMIT 1";
if($r = F_aiocpdb_query($sql, $db)) {
	if($m = F_aiocpdb_fetch_array($r)) {
		echo "".htmlentities($m['nlmsg_title'], ENT_NOQUOTES, $l['a_meta_charset'])."";
	}
}
else {
	F_display_db_error();
}
?>
</th>
</tr>

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<!-- SELECT newsletter attachments ==================== -->
<tr>
<td class="fill" align="right">&nbsp;</td>
<td class="fill">

<table class="fill" border="1" cellspacing="0" cellpadding="2">
<tr class="fill">
	<th class="fillO"><?php echo $l['w_actions']; ?></th>
	<th class="fillE"><?php echo $l['w_file']; ?></th>
	<th class="fillO">CID</th>
</tr>

<?php
//list of attached files in selected message
if($nlattach_nlmsgid) {
	$sql = "SELECT * FROM ".K_TABLE_NEWSLETTER_ATTACHMENTS." WHERE nlattach_nlmsgid=".$nlattach_nlmsgid." ORDER BY nlattach_file";
	if($r = F_aiocpdb_query($sql, $db)) {
		while($m = F_aiocpdb_fetch_array($r)) {
			//change style for each row
			if (isset($rowodd) AND ($rowodd)) {
				$rowclass = "O";
				$rowodd=0;
			} else {
				$rowclass = "E";
				$rowodd=1;
			}
			
			echo "<tr class=\"fill".$rowclass."\"><td class=\"fill".$rowclass."O\">";
			echo "<a href=\"".htmlentities(urldecode($_SERVER['SCRIPT_NAME']))."?nlattach_nlmsgid=".$m['nlattach_nlmsgid']."&amp;nlattach_id=".$m['nlattach_id']."&amp;nlattach_file=".urlencode($m['nlattach_file'])."&amp;menu_mode=softdelete\">".$l['w_delete']."</a>\n";
			echo "<a  href=\"".htmlentities(urldecode($_SERVER['SCRIPT_NAME']))."?nlattach_nlmsgid=".$m['nlattach_nlmsgid']."&amp;nlattach_id=".$m['nlattach_id']."&amp;nlattach_file=".urlencode($m['nlattach_file'])."&amp;menu_mode=".$l['w_delete']."\">".$l['w_remove']."</a>\n";
			echo "</td><td class=\"fill".$rowclass."E\">";
			echo $m['nlattach_file'];
			echo "</td><td class=\"fill".$rowclass."O\">";
			echo "&nbsp;".$m['nlattach_cid'];
			echo "</td></tr>";
		}
	}
	else {
		F_display_db_error();
	}
}
?>
</table>

</td>
</tr>
<!-- END SELECT newsletter attachments ==================== -->

<tr class="fill">
<td class="fill">&nbsp;</td>
<td class="fill"><hr /></td>
</tr>

<tr class="fill">
<td class="fill" align="right"><b><?php echo F_display_field_name('w_file', ''); ?></b></td>
<td class="fill">
<select name="nlattach_file" id="nlattach_file" size="0">
<?php
// read directory for files.
$handle = opendir(K_PATH_FILES_ATTACHMENTS);
	while (false !== ($file = readdir($handle))) {
		if(($file != ".")AND($file != "..")) {
			echo "<option value=\"".$file."\"n";
			if($file == $nlattach_file) {
				echo " selected=\"selected\"";
			}
			echo ">".$file."</option>\n";
		}
	}
closedir($handle);
?>
</select>
<?php //show buttons
F_submit_button("form_nlattacheditor","menu_mode",$l['w_add']); 
F_submit_button("form_nlattacheditor","menu_mode",$l['w_delete']); 
?>
</td>
</tr>

<tr class="fill">
<td class="fill" align="right">&nbsp;</td>
<td class="fill">
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo K_MAX_UPLOAD_SIZE; ?>" />
<input type="file" name="userfile" id="userfile" value="<?php echo $nlattach_file; ?>" size="25" />
<?php F_submit_button("form_nlattacheditor","menu_mode",$l['w_upload']); ?>
</td>
</tr>

<tr class="fill">
<td class="fill" align="right">&nbsp;</td>
<td class="fill"><a href="cp_edit_newsletter_messages.<?php echo CP_EXT; ?>?nlmsg_nlcatid=<?php echo $nlmsg_nlcatid; ?>&amp;nlmsg_id=<?php echo $nlattach_nlmsgid; ?>"><b>&lt;&lt;&nbsp;<?php echo $l['t_newsletter_messages_editor']; ?></b></a></td>
</tr>

</table>
<input type="hidden" name="nlmsg_nlcatid" id="nlmsg_nlcatid" value="<?php echo $nlmsg_nlcatid; ?>" />
<input type="hidden" name="nlattach_nlmsgid" id="nlattach_nlmsgid" value="<?php echo $nlattach_nlmsgid; ?>" />
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
</td>
</tr>
</table>
</form>
<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
