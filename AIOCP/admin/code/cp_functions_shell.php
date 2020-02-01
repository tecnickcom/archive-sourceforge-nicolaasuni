<?php
//============================================================+
// File name   : cp_functions_shell.php                        
// Begin       : 2001-09-21                                    
// Last Update : 2004-12-07                                    
//                                                             
// Description : function to browse a local directory          
//                                                             
// Some of these functions are deep modified versions of:      
// ----------------------------------------------------------  
// PHP Explorer 0.5 Alpha version                              
// Author: Marcelo L. Mottalli <mottalli@sinectis.com.ar>      
// Homepage: http://phpexplorer.sourceforge.net/               
// ----------------------------------------------------------  
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

require_once('../../shared/code/cp_functions_mime.'.CP_EXT);

$default_directory = dirname($_SERVER['PATH_TRANSLATED']);

if(!isset($dir)) {
	$dir = $default_directory;// Webroot dir as default
}
//$dir = stripslashes($dir);
$dir = str_replace("\\", "/", $dir);      // Windows compatibility

// ------------------------------------------------------------
// DIRECTORY FUNCTIONS                                         
// ------------------------------------------------------------

//------------------------------------------------------------
// Stores a directory's files and directories on 
// the arrays $files and $directories respectively.
// * Modified version of PHP Explorer 0.5 Alpha version
//------------------------------------------------------------
function F_read_directory($directory) {
	global $files, $directories, $dir;
	$files = array();
	$directories = array();
	$a = 0;
	$b = 0;
	
	$dirHandler = opendir($directory);
	
	while($file = readdir($dirHandler)) {
		if($file != "." && $file != "..") {
		$fullName = $dir.($dir == "/" ? "" : "/").$file;
			if(is_dir($fullName)) $directories[$a++] = $fullName;
			else $files[$b++] = $fullName;
		}
	}
	sort($directories); // We want them to be displayed alphabetically
	sort($files);
}

//------------------------------------------------------------
// Shows a directory's information
// * Modified version of PHP Explorer 0.5 Alpha version
//------------------------------------------------------------
function F_show_directory_info($directory) {
	global $l;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	$dirs = split("/", $directory);
	echo "<b>".$l['w_directory_current'].":<br />";
	echo "<a href=\"".htmlentities(urldecode($_SERVER['SCRIPT_NAME']))."?dir=/\">&raquo;</a>";
	for ($i = 0; $i < (sizeof($dirs)); $i++) {
		echo "<a href=\"".htmlentities(urldecode($_SERVER['SCRIPT_NAME']))."?dir=";
		for ($a = 0; $a <= $i; $a++) {
			echo $dirs[$a];
			if($a<$i) {echo "/";}
		}
		echo "\">".$dirs[$i]."</a>";
		if($i<(sizeof($dirs)-1)) {echo "/";}
	}
	echo "</b><br />\n";
}

//------------------------------------------------------------
// Shows directory's content
// * Modified version of PHP Explorer 0.5 Alpha version
//------------------------------------------------------------
function F_show_directory($directory) {
	global $l;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	global $files, $directories, $fileInfo;
	F_read_directory($directory);
	F_show_directory_info($directory);
?>

<table class="edge" border="0" cellspacing="1" cellpadding="2">

<tr class="edge">
<td class="edge">

<table class="fill" border="0" cellspacing="2" cellpadding="1">

<tr class="fill" valign="top">
	<th class="fillO">&nbsp;<?php echo $l['w_name']; ?>&nbsp;</th>
	<th class="fillE">&nbsp;<?php echo $l['w_size']; ?>&nbsp;<br />[bytes]</th>
	<th class="fillO">&nbsp;<?php echo $l['w_date']; ?>&nbsp;<br />[<?php echo $l['w_datepattern']." ".$l['w_timepattern']; ?>]</th>
	<th class="fillE">&nbsp;<?php echo $l['w_permissions']; ?>&nbsp;<br />[ooogggwww]</th>
	<th class="fillO">&nbsp;<?php echo $l['w_actions']; ?>&nbsp;</th>
</tr>

<?php
    $row="E"; //used to display different style for each row (even / odd)
	for ($i = 0; $i < sizeof($directories); $i++) {
		$fileInfo->F_get_file_info ($directories[$i]);
		$row = F_show_file_info($fileInfo, $row);
	}
	for ($i = 0; $i < sizeof($files); $i++) {
		$fileInfo->F_get_file_info ($files[$i]);
		$row = F_show_file_info($fileInfo, $row);
	}
?>
      </table>
</td>
</tr>

<tr class="edge">
<td class="edge">
<?php
	// --- print stats ---
	echo "<b>";
	echo $l['w_directories'].": ".sizeof($directories);
	echo "&nbsp;&nbsp;|&nbsp;&nbsp;";
	echo $l['w_files'].": ".sizeof($files);
	echo "&nbsp;&nbsp;|&nbsp;&nbsp;";
	echo "".$l['w_free_space'].": ";
	$freeSpace = diskfreespace($directory);
	if(($freeSpace/(1024*1024)) > 1024) printf("%.2f GBytes", ($freeSpace/(1024*1024*1024)));
	else echo (int)($freeSpace/(1024*1024))."Mbytes<br />\n";
	echo "</b>";
	?>
</td>
</tr>
</table>
<?php
}

// ------------------------------------------------------------
// FILE FUNCTIONS
// * Modified version of PHP Explorer 0.5 Alpha version
// ------------------------------------------------------------

//------------------------------------------------------------
// Class C_file_info: stores a file's information
//------------------------------------------------------------
class C_file_info {
	var $name, $path, $fullname, $isDir, $lastmod, $owner, $perms, $size, $isLink, $linkTo, $extension;

	function F_file_permissions ($mode) {
		$perms  = ($mode & 00400) ? "r" : "-";
		$perms .= ($mode & 00200) ? "w" : "-";
		$perms .= ($mode & 00100) ? "x" : "-";
		$perms .= ($mode & 00040) ? "r" : "-";
		$perms .= ($mode & 00020) ? "w" : "-";
		$perms .= ($mode & 00010) ? "x" : "-";
		$perms .= ($mode & 00004) ? "r" : "-";
		$perms .= ($mode & 00002) ? "w" : "-";
		$perms .= ($mode & 00001) ? "x" : "-";
		return $perms;
      }

	function F_get_file_info ($file) {              // Stores a file's information in the class variables
		$this->name = basename($file);
		$this->path = dirname($file);
		$this->fullname = $file;
		$this->isDir = is_dir($file);
		$this->lastmod = date("Y-m-d H:i:s", @filemtime($file));
		$this->owner = @fileowner($file);
		$this->perms = $this->F_file_permissions (@fileperms($file));
		$this->size = @filesize($file);
		$this->isLink = is_link($file);
		if($this->isLink) $this->linkTo = readlink($file);
		$buffer = explode(".", $this->fullname);
		$this->extension = $buffer[sizeof($buffer)-1];      
	}
}

$fileInfo = new C_file_info;     // This will hold a file's information all over the script

//------------------------------------------------------------
// Shows a file and/or directory info and 
// makes the corresponding links
// * Modified version of PHP Explorer 0.5 Alpha version
//------------------------------------------------------------
function F_show_file_info($fileInfo, $row) {
	global $l;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	global $associations;
	echo "<tr class=\"fill".$row."\">";
	echo "<td class=\"fill".$row."O\" align=\"left\">&nbsp;";
	
	if($fileInfo->isLink) {
		echo $fileInfo->name." -&gt; ";
		$fileInfo->fullname = $fileInfo->linkTo;
		$fileInfo->name = $fileInfo->linkTo;
	}
	
	if($fileInfo->isDir) {
		echo "<a href=\"".htmlentities(urldecode($_SERVER['SCRIPT_NAME']))."?dir=".$fileInfo->fullname."\" class=\"dirlink\"";
		echo ">".$fileInfo->name."</a>";
	}
	else echo $fileInfo->name;

	echo "&nbsp;</td>";
	echo "<td class=\"fill".$row."E\" align=\"right\">&nbsp;".$fileInfo->size."&nbsp;</td>";
	echo "<td class=\"fill".$row."O\">&nbsp;".$fileInfo->lastmod."&nbsp;</td>";
	echo "<td class=\"fill".$row."E\" align=\"middle\">&nbsp;".$fileInfo->perms."&nbsp;</td>";
	echo "<td class=\"fill".$row."O\" align=\"left\">&nbsp;";
	
	if(!$fileInfo->isDir) {
		if($fileInfo->perms[6] == 'r') {
			echo "<a href=\"".htmlentities(urldecode($_SERVER['SCRIPT_NAME']))."?dir=".urlencode($fileInfo->fullname)."&amp;action=view\">";
			echo "<img src=\"".K_PATH_IMAGES_SHELL."buttons/view.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"".$l['w_view']."\" />";
			echo "</a>";
			
			echo "<a href=\"".htmlentities(urldecode($_SERVER['SCRIPT_NAME']))."?dir=".urlencode($fileInfo->fullname)."&amp;action=download\">";
			echo "<img src=\"".K_PATH_IMAGES_SHELL."buttons/download.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"".$l['w_download']."\" />";
			echo "</a>";
		}
		if($fileInfo->perms[7] == 'w') {
			echo "<a href=\"".htmlentities(urldecode($_SERVER['SCRIPT_NAME']))."?dir=".urlencode($fileInfo->fullname)."&amp;action=edit\">";
			echo "<img src=\"".K_PATH_IMAGES_SHELL."buttons/edit.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"".$l['w_edit']."\" />";
			echo "</a>";
			echo "<a href=\"".htmlentities(urldecode($_SERVER['SCRIPT_NAME']))."?dir=".urlencode($fileInfo->fullname)."&amp;action=rename\">";
			echo "<img src=\"".K_PATH_IMAGES_SHELL."buttons/rename.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"".$l['w_rename']."\" />";
			echo "</a>";
			echo "<a href=\"".htmlentities(urldecode($_SERVER['SCRIPT_NAME']))."?dir=".urlencode($fileInfo->fullname)."&amp;action=delete\">";
			echo "<img src=\"".K_PATH_IMAGES_SHELL."buttons/delete.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"".$l['w_delete']."\" />";
			echo "</a>";
		}
	}
	echo "&nbsp;</td>";
	echo "</tr>";
	
	if ($row=="E") {return "O";} //return even / odd row value 
	else return "E";
}

//------------------------------------------------------------
// Deletes a file
//------------------------------------------------------------
function F_delete_file($file) {
	global $l;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	if(!unlink($file)) {
		F_print_error("ERROR", $file.": ".$l['m_delete_not']);
		return FALSE;
	}
	else {
		F_print_error("MESSAGE", $file.": ".$l['m_delete_yes']);
	}
return TRUE;
}

//------------------------------------------------------------
// Renames a file
//------------------------------------------------------------
function F_rename_file($file, $newname) {
	global $l;
	require_once('../../shared/config/cp_extension.inc');
	require_once('../config/cp_config.'.CP_EXT);
	if($file AND $newname AND rename($file, $newname)) {
		F_print_error("MESSAGE", basename($file)." ".$l['m_rename_yes']);
	}
	else {
		F_print_error("MESSAGE", basename($file)." ".$l['m_rename_yes']);
	}
return TRUE;
}

//------------------------------------------------------------
// Uploads a file to the server 
//------------------------------------------------------------
function F_upload_file() {
	global $dir;
	if($dir == "/") {$separator = "";}
	else {$separator = "/";}
	if(move_uploaded_file ($_FILES['userfile']['tmp_name'], $dir.$separator.$_FILES['userfile']['name'])) {
		return TRUE;
	}
return FALSE;
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
