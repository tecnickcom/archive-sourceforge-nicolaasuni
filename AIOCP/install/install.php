<?php
//============================================================+
// File name   : install.php
// Begin       : 2002-05-13
// Last Update : 2008-07-07
// 
// Description : AIOCP installation script.
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

$progress_log = "install.log"; //installation log file
$start_installation = FALSE; // becomes true on form submission

// define supported databases
$dbtypes = Array("MYSQL");

require_once('cp_functions_install.php');

//send XHTML headers
echo "<"."?"."xml version=\"1.0\" encoding=\"UTF-8\" "."?".">\n";
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
echo "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\" dir=\"ltr\">\n\n";

echo "<head>\n";
echo "<title>AIOCP 1.4 - Installation</title>\n";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />\n";
echo "<meta name=\"description\" content=\"Installation Script for AIOCP\" />\n";
echo "<meta name=\"author\" content=\"Nicola Asuni - Tecnick.com LTD\" />\n";
echo "<meta name=\"reply-to\" content=\"info@tecnick.com\" />\n";
echo "<meta http-equiv=\"Pragma\" content=\"no-cache\" />\n";
echo "<link rel=\"stylesheet\" href=\"install.css\" type=\"text/css\" />\n";
echo "</head>\n\n";

echo "<body>\n";
?>

<h1>AIOCP 1.4 - Installation</h1>

<p>
<b>This is the <a href="index.htm" title="installation manual" target="_blank">installation</a> script of <a href="http://www.aiocp.com" target="_blank">AIOCP</a> by <a href="http://www.tecnick.com" target="_blank">Tecnick.com LTD</a></b>
</p>

<?php
//if this form has been submitted go on with installation process		
if (isset($_REQUEST['forceinstall']) AND ($_REQUEST['forceinstall']==1)) {
	
	if (!isset($_REQUEST['path_main'])) {
		$_REQUEST['path_main'] = ""; //this field is not required
	} 
	
	$_REQUEST['path_dbfiles'] = "";
	
	echo "\n<p><b>Please wait, this installation may take a while...</b></p>";
	
	//install database
	echo "\n<ul class=\"log\">";
	echo "\n<li>start AIOCP installation............[OK]</li>";
	
	echo "\n<li>start database installation.........[OK]";
	
	echo "\n<ul class=\"log\">";
	error_log("[START] database installation\n", 3, $progress_log); //log info
	// Install AIOCP database
	F_install_database($_REQUEST['db_type'], $_REQUEST['db_host'], $_REQUEST['db_port'], $_REQUEST['db_user'], $_REQUEST['db_password'], $_REQUEST['database_name'], $_REQUEST['table_prefix'], $progress_log);
	error_log("[END] database installation\n", 3, $progress_log); //log info
	echo "\n</ul>";
	echo "\n</li>";
	
	echo "\n<li>end database installation...........[OK]</li>";
	
	// update configuration files
	echo "\n<li>start config files update...........[OK]";
	echo "\n<ul class=\"log\">";
	error_log("[START] update config files\n", 3, $progress_log); //log info
	// Update configuration files
	F_update_config_files($_REQUEST['db_type'], $_REQUEST['db_host'], $_REQUEST['db_port'], $_REQUEST['db_user'], $_REQUEST['db_password'], $_REQUEST['database_name'], $_REQUEST['table_prefix'], $_REQUEST['path_host'], $_REQUEST['path_aiocp'], $_REQUEST['path_main'], $_REQUEST['path_dbfiles'],  $_REQUEST['standard_port'], $progress_log);
	error_log("[END] update config files\n", 3, $progress_log); //log info
	echo "\n</ul>";
	echo "\n</li>";
	
	echo "\n<li>end config files update.............[OK]</li>";
	
	echo "\n<li>end AIOCP installation..............[OK]</li>";
	echo "\n</ul>";
	
	//display here post-installation comments
	echo "\n<p>";
	echo "\nThe automatic installation process is finished.<br />";
	echo "\nTake a look to the above log for installation errors.<br />";
	echo "\nThe configuration files (if no errors has been reported) are now set as minimum needed just to start using the software.<br />";
	echo "\nNow you can manually change the configuration files to fit your needs.<br />";
	echo "\nAfter config files changes please set the write permission of these files to read only (chmod 644 on unix like systems).<br />";
	echo "\n<br />";
	echo "\nIf it's all OK <a href=\"../admin/code/index.php\" target=\"_top\">click here</a> to start AIOCP.<br />";
	echo "\n<br /></p>";
	
	error_log("--- END LOG: ".date("Y-m-d H:i:s")." ---\n", 3, $progress_log); //create progress log file
}
else { //display input form
	
	if (isset($_REQUEST['startinstall'])) {
		// check if all required fields have been submitted
		if ( isset($_REQUEST['db_type']) AND $_REQUEST['db_type'] 
			AND isset($_REQUEST['db_host']) AND $_REQUEST['db_host'] 
			AND isset($_REQUEST['db_port']) AND $_REQUEST['db_port'] 
			AND isset($_REQUEST['db_user']) AND $_REQUEST['db_user'] 
			AND isset($_REQUEST['db_password']) AND $_REQUEST['db_password'] 
			AND isset($_REQUEST['database_name']) AND $_REQUEST['database_name'] 
			AND isset($_REQUEST['table_prefix']) AND $_REQUEST['table_prefix']
			AND isset($_REQUEST['path_host']) AND $_REQUEST['path_host']
			AND isset($_REQUEST['path_aiocp']) AND $_REQUEST['path_aiocp']
			AND isset($_REQUEST['path_main']) AND $_REQUEST['path_main']
			AND isset($_REQUEST['standard_port']) AND $_REQUEST['standard_port']
		) {
			$start_installation = TRUE;
			// replace backslashes on paths
			$_REQUEST['path_aiocp'] = str_replace("\\", "/", stripslashes($_REQUEST['path_aiocp']));
			$_REQUEST['path_main'] = str_replace("\\", "/", stripslashes($_REQUEST['path_main']));
		}
		else {
			echo "<p><b>ERROR:</b> Some fields are missing. Please fill all form fields with the right values.</p>";
		}
	}
	else { //form has not been submitted
		//initialize variables to default values
		$_REQUEST['db_type'] = "MYSQL";
		$_REQUEST['db_host'] = "localhost";
		$_REQUEST['db_port'] = "3306";
		$_REQUEST['db_user'] = "root";
		$_REQUEST['db_password'] = "";
		$_REQUEST['database_name'] = "aiocp";
		$_REQUEST['table_prefix'] = "aiocp_";
		if (isset($_SERVER["HTTP_HOST"]) and !empty($_SERVER["HTTP_HOST"])) {
			if(isset($_SERVER["HTTPS"]) AND !empty($_SERVER["HTTPS"]) AND strtolower($_SERVER['HTTPS'])!='off') {
				$_REQUEST['path_host'] = "https://";
			} else {
				$_REQUEST['path_host'] = "http://";
			}
			$_REQUEST['path_host'] .= $_SERVER["HTTP_HOST"];
		} else {
			$_REQUEST['path_host'] = "http://localhost";
		}
		$_REQUEST['path_aiocp'] = substr($_SERVER["SCRIPT_NAME"], 0, -19);
		$_REQUEST['path_main'] = substr(str_replace("\\", "/", dirname(__FILE__)), 0, -7);
		$httphost = explode(':', $_SERVER['HTTP_HOST']);
		if(isset($httphost[1]) AND !empty($httphost[1])) {
			$_REQUEST['standard_port'] = $httphost[1];
		} elseif(isset($_SERVER["HTTPS"]) AND !empty($_SERVER["HTTPS"]) AND strtolower($_SERVER['HTTPS'])!='off') {
			$_REQUEST['standard_port'] = 443;
		} else {
			$_REQUEST['standard_port'] = 80;
		}
	}

// display an input form to collect installation data
?>

<p>
<b>WARNING:</b> this installation process will destroy any database data from previous installations.<br />
Note that the installation process may take a while, so please be patient and do not hit reload button on browser.<br />
To start installation fill the form below and click the INSTALL button.<br />
</p>

<div class="container">

<div class="tceformbox">
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data" id="form_AIOCP_installer">

<div class="row">
<span class="label">
<label for="db_type" title="database type">db type</label>
</span>
<span class="formw">
<select name="db_type" id="db_type" size="0">
<?PHP
	while(list($key, $val) = each($dbtypes)) { //for each file on list
		echo "<option value=\"".$val."\"";
		if( (isset($_REQUEST['db_type'])) AND ($val == $_REQUEST['db_type']) ) {
			echo " selected=\"selected\"";
		}
		echo ">".$val."</option>\n";
	}
?>
</select>
</span>
</div>

<div class="row">
<span class="label">
<label for="db_host" title="database host name">db host</label>
</span>
<span class="formw">
<input type="text" name="db_host" id="db_host" value="<?PHP echo $_REQUEST['db_host']; ?>" size="30" maxlength="255" />
</span>
</div>

<div class="row">
<span class="label">
<label for="db_port" title="database port">db port</label>
</span>
<span class="formw">
<input type="text" name="db_port" id="db_port" value="<?PHP echo $_REQUEST['db_port']; ?>" size="30" maxlength="255" />
</span>
</div>

<div class="row">
<span class="label">
<label for="db_user" title="database user name">db user</label>
</span>
<span class="formw">
<input type="text" name="db_user" id="db_user" value="<?PHP echo $_REQUEST['db_user']; ?>" size="30" maxlength="255" />
</span>
</div>

<div class="row">
<span class="label">
<label for="db_password" title="database user password">db password</label>
</span>
<span class="formw">
<input type="text" name="db_password" id="db_password" value="<?PHP echo $_REQUEST['db_password']; ?>" size="30" maxlength="255" />
</span>
</div>

<div class="row">
<span class="label">
<label for="database_name" title="database name">db name</label>
</span>
<span class="formw">
<input type="text" name="database_name" id="database_name" value="<?PHP echo $_REQUEST['database_name']; ?>" size="30" maxlength="255" />
</span>
</div>

<div class="row">
<span class="label">
<label for="table_prefix" title="prefix for database tables names">tables prefix</label>
</span>
<span class="formw">
<input type="text" name="table_prefix" id="table_prefix" value="<?PHP echo $_REQUEST['table_prefix']; ?>" size="30" maxlength="255" />
</span>
</div>

<div class="row">
<span class="label">
<label for="path_host" title="host URL">host URL</label>
</span>
<span class="formw">
<input type="text" name="path_host" id="path_host" value="<?PHP echo $_REQUEST['path_host']; ?>" size="30" maxlength="255" />
</span>
</div>

<div class="row">
<span class="label">
<label for="path_aiocp" title="relative URL where this program is installed">relative URL</label>
</span>
<span class="formw">
<input type="text" name="path_aiocp" id="path_aiocp" value="<?PHP echo $_REQUEST['path_aiocp']; ?>" size="30" maxlength="255" />
</span>
</div>

<div class="row">
<span class="label">
<label for="path_main" title="real full server path where this program is installed">AIOCP path</label>
</span>
<span class="formw">
<input type="text" name="path_main" id="path_main" value="<?PHP echo $_REQUEST['path_main']; ?>" size="30" maxlength="255" />
</span>
</div>


<div class="row">
<span class="label">
<label for="standard_port" title="http connection port">AIOCP Port</label>
</span>
<span class="formw">
<input type="text" name="standard_port" id="standard_port" value="<?PHP echo $_REQUEST['standard_port']; ?>" size="30" maxlength="255" />
</span>
</div>


<input type="hidden" name="forceinstall" id="forceinstall" value="" />
<input type="hidden" name="startinstall" id="startinstall" value="" />

<div class="row">
<input type="button" name="install" id="install" value="INSTALL" onclick="document.getElementById('form_AIOCP_installer').startinstall.value=1; document.getElementById('form_AIOCP_installer').submit()" title="click here to install AIOCP"/>
</div>

</form>
</div>

</div>
<?php
}


if ($start_installation) {
	//open log popup display to show process progress
	@fopen($progress_log, "w"); //clear progress log file if exist
	error_log("--- START LOG: ".date("Y-m-d H:i:s")." ---\n", 3, $progress_log); //create progress log file
	echo "\n<script type=\"text/javascript\">\n";
	echo "//<![CDATA[\n";
	//echo "logview=window.open('cp_show_progress.php?log=".$progress_log."','logview','dependent,height=300,width=500,menubar=no,resizable=yes,scrollbars=no,status=no,toolbar=no');\n";
	echo "document.getElementById('form_AIOCP_installer').forceinstall.value=1;\n";
	echo "document.getElementById('form_AIOCP_installer').submit();\n"; //submit to start installation
	echo "//]]>\n";
	echo "</script>\n";
}

?>

</body>
</html>

<?php
//============================================================+
// END OF FILE                                                 
//============================================================+
?>