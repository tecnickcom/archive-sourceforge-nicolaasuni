<?php
//============================================================+
// File name   : cp_shell.php                                  
// Begin       : 2001-09-21                                    
// Last Update : 2008-07-06                                   
//                                                             
// Description : web interface to execute shell                
//               commands/external programs on a server        
//               without telnet access.                        
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
require_once('../code/cp_functions_shell.'.CP_EXT);

$pagelevel = K_AUTH_ADMIN_CP_SHELL;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_shell'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages;
require_once('../../shared/code/cp_functions_form.'.CP_EXT); 

if (isset($_REQUEST['action'])) {
	$action = $_REQUEST['action'];
} else {
	$action = "";
}
?>

<!-- EXECUTE SHELL COMMAND ==================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_shell" id="form_shell">
<input type="hidden" name="dir_from" id="dir_from" value="<?php echo $dir; ?>" />
<b><?php echo F_display_field_name('d_execute_cmd', 'h_execute_shell_cmd'); ?>:</b><br />
<input type="text" name="shellcmd" size="25" />
<input type="hidden" name="action" id="action" value="" />
<?php F_submit_button("form_shell","action",$l['w_execute']); ?>
</form>
<!-- END EXECUTE SHELL COMMAND ==================== -->

<br />

<?php
if(is_writeable($dir) AND ($action != "exec")) {  // check if $dir is writeable
?>

<!-- UPLOAD FILE ==================== -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_upload" id="form_upload">
<input type="hidden" name="dir" id="dir" value="<?php echo $dir; ?>" />
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo K_MAX_UPLOAD_SIZE; ?>" />
<b><?php echo F_display_field_name('d_upload_file', 'h_upload_file_shell'); ?>:</b><br />
<input type="file" name="userfile" id="userfile" />
<input type="hidden" name="action" id="action" value="" />
<?php F_submit_button("form_upload","action",$l['w_upload']); ?>
</form>
<!-- END UPLOAD FILE ==================== -->
<br />
<?php
} 
?>

<!-- ====================================================== -->
<?php
switch($action) {

	case "delete": { // print confirmation message
		F_print_error("WARNING", $l['m_delete_confirm']);
		?>
		
		<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_delete" id="form_delete">
		<input type="hidden" name="action" id="action" value="forcedelete" />
		<input type="hidden" name="filetodelete" id="filetodelete" value="<?php echo $dir; ?>" />
		<b><?php echo $dir; ?></b><br />
		<?php $dir = dirname($dir); ?>
		<input type="hidden" name="dir" id="dir" value="<?php echo $dir; ?>" />
		<input type="hidden" name="forcedelete" id="forcedelete" value="" />
		<?php 
		F_submit_button("form_delete","forcedelete",$l['w_delete']);
		F_submit_button("form_delete","forcedelete",$l['w_cancel']);
		?>
		</form>
		
		<?php
		F_show_directory($dir);
		break;
	}

	case "forcedelete": { //delete the selected file
		if($forcedelete == $l['w_delete']) { //check if delete button has been pushed
			F_delete_file($filetodelete);
		}
		$forcedelete = "";
		F_show_directory($dir);
		break;
	}

	case "rename": { // print confirmation message
		F_print_error("WARNING", $l['m_rename_file']);
		?>
		
		<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_rename" id="form_rename">
		<input type="hidden" name="action" id="action" value="forcerename" />
		<input type="hidden" name="filetorename" id="filetorename" value="<?php echo $dir; ?>" />
		<b><?php echo basename($dir); ?></b><br />
		<input type="text" name="newname" id="newname" value="<?php echo basename($dir); ?>" />
		<?php $dir = dirname($dir); ?>
		<input type="hidden" name="dir" id="dir" value="<?php echo $dir; ?>" />
		<input type="hidden" name="forcerename" id="forcerename" value="" />
		<?php 
		F_submit_button("form_rename","forcerename",$l['w_rename']);
		F_submit_button("form_rename","forcerename",$l['w_cancel']);
		?>
		</form>
		
		<?php
		F_show_directory($dir);
		break;
	}

	case "forcerename": { //delete the selected file
		if($forcerename == $l['w_rename']) { //check if delete button has been pushed
			F_rename_file($filetorename, $dir."/".$newname);
		}
		$forcerename = "";
		F_show_directory($dir);
		break;
	}
	
	case unhtmlentities($l['w_upload']):
	case $l['w_upload']: {
         if(F_upload_file()) {
			F_print_error("MESSAGE", htmlentities($_FILES['userfile']['name']).": ".$l['m_upload_yes'], ENT_NOQUOTES, $l['a_meta_charset']);
		}
		else {
			F_print_error("ERROR", htmlentities($_FILES['userfile']['name']).": ".$l['m_upload_not'], ENT_NOQUOTES, $l['a_meta_charset']);
		}
         F_show_directory($dir);
         break;
	}

	case unhtmlentities($l['w_execute']):
	case $l['w_execute']: {
		echo "<p><b>".$l['d_output_cmd'].":</b> ".$shellcmd."</p>";
		echo "<pre class=\"shell\">";
		echo system($shellcmd);
		echo "\n\n</pre>";
		echo "<p><a href=\"".htmlentities(urldecode($_SERVER['SCRIPT_NAME']))."\"><b>&lt;&lt; ".$l['d_reload_page']." &gt;&gt;</b></a></p>";
		break;
		//exit();
	}

	case "edit": {
		echo "<script language=\"JavaScript\" type=\"text/javascript\">";
		echo "//<![CDATA[\n";
		echo "window.open(\"cp_edit_file.".CP_EXT."?file=".urlencode($dir)."\",\"CPMAIN\");";
		echo "//]]>\n";
		echo "</script>\n";
      	break;
	}

	case "download": {
		//create verification code to avoid improper use of cp_show_file.php file
		$verifycode = F_generate_verification_code($dir, 4);
		//open file in a new window
		echo "<script language=\"JavaScript\" type=\"text/javascript\">";
		echo "//<![CDATA[\n";
		echo "tempwin=window.open(\"cp_show_file.".CP_EXT."?h=download&c=".$verifycode."&f=".urlencode($dir)."\",\"_blank\",\"scrollbars=no,resizable=no,width=1,height=1\");\n";
		echo "setInterval('tempwin.close()', 5000);\n"; // mozilla fix
		echo "//]]>\n";
		echo "</script>\n";
		
		$dir = dirname($dir);
		F_show_directory($dir);
		break;
	}

	case "view": {
		//create verification code to avoid improper use of cp_show_file.php file
		$verifycode = F_generate_verification_code($dir, 4);
		//choose the appropriate header
		$header = "Content-type: ".F_choose_mime(basename($dir));
		//open file in a new window
		echo "<script language=\"JavaScript\" type=\"text/javascript\">";
		echo "//<![CDATA[\n";
		echo "window.open(\"cp_show_file.".CP_EXT."?h=".urlencode($header)."&c=".$verifycode."&f=".urlencode($dir)."\",\"_blank\");";
		echo "//]]>\n";
		echo "</script>\n";
		$dir = dirname($dir);
		F_show_directory($dir);
		break;
	}

	default: {
		F_show_directory($dir);
		break;
	}
};

$action = ""; // reset action value
?>

<!-- ====================================================== -->
<?php require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
