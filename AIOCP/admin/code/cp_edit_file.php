<?php
//============================================================+
// File name   : cp_edit_file.php                              
// Begin       : 2001-09-22                                    
// Last Update : 2008-07-07                                    
//                                                             
// Description : edit a text file                              
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

$pagelevel = K_AUTH_ADMIN_CP_EDIT_FILE;
require_once('../../shared/code/cp_authorization.'.CP_EXT);

$thispage_title = $l['t_text_editor'];

require_once('../code/cp_page_header.'.CP_EXT);
F_print_error(0, ""); //clear header messages;

require_once('../../shared/code/cp_functions_form.'.CP_EXT);

if (isset($_REQUEST['action'])) {
	$action = $_REQUEST['action'];
} else {
	$action = "";
}
switch ($action) {
	case unhtmlentities($l['w_save']):
	case $l['w_save']: {
		if($handlerFile = fopen($file, "w")) {
			$texttoedit = stripslashes($texttoedit);
			if(!fwrite($handlerFile, $texttoedit, strlen($texttoedit))) {
				F_print_error("MESSAGE", $l['m_writefile_not'].": ".basename($file));
			}
			fclose($handlerFile);
			F_print_error("MESSAGE", basename($file).": ".$l['m_writefile_yes']);
		}
		else { //print an error message
			F_print_error("MESSAGE", $l['m_savefile_not'].": ".basename($file));
		}
		break;
	}
	
	case unhtmlentities($l['w_clear']):
	case $l['w_clear']: {
		$texttoedit = "";
		break;
	}
	
	default: {
		if(isset($file) AND (!empty($file))) {// Open and read file
			if(file_exists($file)) { //check if file exist
				if($handlerFile = fopen($file, "r")) {
					$fileopen = TRUE; //remember when a file is open VS new
					$texttoedit = fread($handlerFile, filesize($file));
				}
				else { //print an error message
					F_print_error("ERROR", basename($file).": ".$l['m_openfile_not']);
				}
			}
			else { //print an error message
				F_print_error("WARNING", basename($file).": ".$l['m_file_non_existent']);
				//$texttoedit = "";
			}
		}
		else { // create a new file with unique name
			$fileopen = FALSE;
			$texttoedit = "";
			//try to create a unique filename
			$fid = 0;
			$dir = K_PATH_PUBLIC_CODE_REAL;
			$file = $dir."_newfile".$fid.".".CP_EXT;
			while(@fopen($file, "r")) {
				$fid++;
				$file = $dir."_newfile".$fid.".".CP_EXT;
			}
		}
	}
}

	//if((!$texttoedit) OR ($action == $l['w_reload']))


// Initialize variables
$texttoedit = htmlentities(stripslashes($texttoedit), ENT_NOQUOTES, $l['a_meta_charset']);

if((!isset($wordwrap) OR (!$wordwrap)) OR ($wordwrap == "off")) {
	$wordwrap = " wrap=\"off\"";
	$checkoff = "checked=\"checked\"";
	$checkon = "";
}
else {
	$wordwrap = " wrap=\"on\"";
	$checkoff = "";
	$checkon = "checked=\"checked\"";
}

//calculate file URL
$fileurl = ereg_replace("([A-Za-z]*)\:","",$file); //remove drive letter
$fileurl = eregi_replace(K_PATH_MAIN, K_PATH_HOST.K_PATH_AIOCP, $fileurl); //change path to URL 
?>

<!-- ====================================================== -->

<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" name="form_editfile" id="form_editfile">

<input type="hidden" name="action" id="action" value="" />

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<th class="edge" align="left">
<?php if($fileopen) { ?>
<a class="edge" href="<?php echo htmlentities(urldecode($file)); ?>" target="_blank"><small><?php echo htmlentities(urldecode($file)); ?></small></a><br />
<a class="edge" href="<?php echo htmlentities(urldecode($fileurl)); ?>" target="_blank"><small><?php echo htmlentities(urldecode($fileurl)); ?></small></a>
<?php 
}
else {
?>
<small><?php echo $file; ?></small><br />
<small><?php echo $fileurl; ?></small>
<?php 
}
?>
</th></tr>

<tr class="edge">
<td class="edge">
<table class="fill" border="0" cellspacing="2" cellpadding="1">

<tr class="fill">
<td class="fill">
<?php echo $l['w_file']; ?>: <input type="text" name="file" id="file" value="<?php echo $file; ?>" size="50" maxlength="255" /> 
<?php F_submit_button("form_editfile","action",$l['w_reload']); ?>
<?php F_submit_button("form_editfile","action",$l['w_save']); ?>
</td>
</tr>

<tr class="fill">
<td class="fill">
<textarea cols="80" rows="20" name="texttoedit" id="texttoedit"<?php echo $wordwrap; ?>><?php echo $texttoedit; ?></textarea>
</td>
</tr>

</table>

</td>
</tr>

<tr class="edge">
<td class="edge">
<?php echo $l['w_wrap']; ?>: <input type="radio" name="wordwrap" value="off" <?php echo $checkoff; ?> onclick="document.form_editfile.submit()" /><?php echo $l['w_off']; ?> <input type="radio" name="wordwrap" value="hard" <?php echo $checkon; ?> onclick="document.form_editfile.submit()" /><?php echo $l['w_on']; ?>&nbsp; 
<?php F_submit_button("form_editfile","action",$l['w_clear']); ?> 
<?php F_html_button("all", "form_editfile", "texttoedit", false); ?> 
<?php F_generic_button("newpageeditor", "PHP WIZARD", "htmlWindow=window.open('cp_edit_newpage.".CP_EXT."?callingform=form_editfile&amp;callingfield=texttoedit','newpageWindow','dependent,height=500,width=400,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no')"); ?>
</td>
</tr>

</table>
</form>

<!-- Display code preview button -->
<form action="cp_php_code_preview.<?php echo CP_EXT; ?>" target="_blank" method="post" enctype="multipart/form-data" name="form_codepreview" id="form_codepreview">
<input type="hidden" name="phpcode" id="phpcode" value="" />
<?php F_generic_button("form_codepreview", $l['w_code_preview'], "document.form_codepreview.phpcode.value=document.form_editfile.texttoedit.value; document.form_codepreview.submit();"); ?>
</form>



<?php if($fileopen) {fclose($handlerFile);}
require_once('../code/cp_page_footer.'.CP_EXT);

//============================================================+
// END OF FILE                                                 
//============================================================+
?>