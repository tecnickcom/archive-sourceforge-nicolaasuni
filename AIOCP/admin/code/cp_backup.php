<?php
//============================================================+
// File name   : cp_backup.php                                 
// Begin       : 2002-01-11                                    
// Last Update : 2004-09-30                                    
//                                                             
// Description : Backup site and database on two zip files     
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
require_once('../../shared/code/cp_functions_form.'.CP_EXT);
require_once('../code/cp_functions_zip.'.CP_EXT);
require_once('../code/cp_functions_backup.'.CP_EXT);

$pagelevel = K_AUTH_ADMIN_CP_BACKUP;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

//initialize menu command
if (isset($_REQUEST["menu_mode"])) {
	$menu_mode = $_REQUEST["menu_mode"];
}
else {
	$menu_mode = "";
}


$progress_log = "../log/cp_backup.log";	//log file

$thispage_title = $l['t_backup'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages;

switch($menu_mode) {

	case unhtmlentities($l['w_delete']):
	case $l['w_delete']:{
		F_stripslashes_formfields(); // Delete ---- DANGEROUS COMMAND ---
		if(!unlink(K_PATH_FILES_BACKUP.$backupfile)) {
			F_print_error("ERROR", $backupfile.": ".$l['m_delete_not']);
		}
		else {
			F_print_error("MESSAGE", $backupfile.": ".$l['m_delete_yes']);
		}
		break;
	}
	
	case unhtmlentities($l['w_download']):
	case $l['w_download']:{ // Download
		$filetodownload = K_PATH_FILES_BACKUP.$backupfile;
		//create verification code to avoid improper use of cp_download.php file
		$verifycode = F_generate_verification_code($filetodownload, 4);
		echo "<script language=\"JavaScript\" type=\"text/javascript\">";
		echo "//<![CDATA[\n";
		echo "dw=window.open('../../shared/code/cp_download.".CP_EXT."?c=".$verifycode."&d=4&f=".urlencode($filetodownload)."', 'dw', 'dependent,height=1,width=1,menubar=no,resizable=no,scrollbars=no,status=no,toolbar=no');\n";
		echo "setInterval('dw.close()', 5000);\n";
		echo "//]]>\n";
		echo "</script>\n";
		break;
	}
	
	case unhtmlentities($l['w_backup']):
	case $l['w_backup']:{ // Backup site
		F_print_error("MESSAGE", $l['m_backup_wait']);
		
		//open log popup display to show process progress
		@unlink($progress_log); //clear progress log file if exist
		error_log("--- START LOG: ".gmdate("Y-m-d H:i:s")." ---\n", 3, $progress_log); //create progress log file
		echo "\n<script language=\"JavaScript\" type=\"text/javascript\">\n";
		echo "//<![CDATA[\n";
		echo "logview=window.open('cp_show_progress.".CP_EXT."?log=".$progress_log."','logview','dependent,height=280,width=400,menubar=no,resizable=yes,scrollbars=no,status=no,toolbar=no');\n";
		echo "//]]>\n";
		echo "</script>\n";
		
		break;
	}
	
	case "startlongprocess":{ // start backup
		$thistime = gmdate('YmdHis');
		if(K_PATH_DATABASE_DATA) { //backup MySQL data files
			$backupmysqlfile = F_backup_dir(K_PATH_DATABASE_DATA, $thistime."mysql");
		}
		$backupsitefile = F_backup_dir(K_PATH_MAIN, $thistime."site");
		if($backupsitefile AND $backupmysqlfile) {
			F_print_error("MESSAGE", $l['m_backup_successfull']);
			$backupfile = $backupmysqlfile;
		}
		else { // backup error
			F_print_error("ERROR", $l['m_backup_error']);
		}
		error_log("--- END LOG: ".gmdate("Y-m-d H:i:s")." ---\n", 3, $progress_log); //create progress log file
		break;
	}
	
}
?>
<!-- ====================================================== -->	
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_backup" id="form_backup">

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<tr class="fillO">
<td class="fillOO" align="right"><b><?php echo F_display_field_name('w_backup', 'h_backup'); ?></b></td>
<td class="fillOE">
<select name="backupfile" id="backupfile" size="0">
<?php
// read directory for files (only ZIP files).
$handle = opendir(K_PATH_FILES_BACKUP);
	while (false !== ($file = readdir($handle))) {
		$path_parts = pathinfo($file);
		$file_ext = strtolower($path_parts['extension']);
		//check file type (GIF, JPG, PNG)
		if($file_ext=="zip") {
			echo "<option value=\"".$file."\"";
			if($file==basename($backupfile)) {
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

</table>

</td>
</tr>

<tr class="edge">
<td class="edge" align="center">
<input type="hidden" name="menu_mode" id="menu_mode" value="" />
<?php //show buttons
F_submit_button("form_backup","menu_mode",$l['w_backup']); 
F_submit_button("form_backup","menu_mode",$l['w_download']); 
F_submit_button("form_backup","menu_mode",$l['w_delete']); 
?>
</td>
</tr>
</table>
</form>
<!-- ====================================================== -->
<?php //recall backup page to start backup
if (($menu_mode == $l['w_backup']) OR ($menu_mode == unhtmlentities($l['w_backup'])) ) {
	echo "<script language=\"JavaScript\" type=\"text/javascript\">";
	echo "//<![CDATA[\n";
	echo "document.form_backup.menu_mode.value='startlongprocess';";
	echo "document.form_backup.submit();";
	echo "//]]>\n";
	echo "</script>";
}

require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>