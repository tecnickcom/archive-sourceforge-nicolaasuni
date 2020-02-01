<?php 
//============================================================+
// File name   : cp_functions_install.php
// Begin       : 2002-05-13
// Last Update : 2007-01-11
// 
// Description : Installation functions for AIOCP.
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
 * Install AIOCP database.
 * @param string $db_type database type (MySQL)
 * @param string $db_host database host 
 * @param string $db_port database port number
 * @param string $db_user database user
 * @param string $db_password database password
 * @param string $database_name database name
 * @param string $table_prefix table prefix
 * @param string $progress_log log file name
 * @return boolean True in case of success, False otherwise.
 */
function F_install_database($db_type, $db_host, $db_port, $db_user, $db_password, $database_name, $table_prefix, $progress_log) {
	ini_set("memory_limit", "256M");
	set_time_limit(0); //remove maximum execution time limit
	
	define ("K_DATABASE_TYPE", $db_type); // database type (for Database Abstraction Layer)
	
	// Load the Database Abstraction Layer for selected DATABASE type
	switch (K_DATABASE_TYPE) {
		case "POSTGRESQL":
			default: {
			require_once('../shared/code/cp_db_dal_postgresql.php');
			break;
		}
		case "MYSQL":
			default: {
			require_once('../shared/code/cp_db_dal_mysql.php');
			break;
		}
	}
	
	echo "\n<li>create or empty database........";
	error_log("  create or empty database\n", 3, $progress_log); //log info
	if ($db = F_create_database(K_DATABASE_TYPE, $db_host, $db_port, $db_user, $db_password, $database_name)) { //create database if not exist
		echo "[OK]</li>";
		$db = F_database_connect($db, $database_name); //connect with database
		echo "\n<li>create database tables..........";
		error_log("  [START] create database tables\n", 3, $progress_log); //log info
		// process structure sql file
		if (F_execute_sql_queries($db, "aiocp_db_structure.sql", "aiocp_", $table_prefix, $progress_log)) { 
			echo "[OK]</li>";
			error_log("  [END:OK] create database tables\n", 3, $progress_log); //log info
		}
		else {
			echo "[ERROR]</li>";
			error_log("  [END:ERROR] create database tables\n", 3, $progress_log); //log info
		}
		echo "\n<li>fill tables with default data...";
		error_log("  [START] fill tables with default data\n", 3, $progress_log); //log info
		// process data sql file 
		if (F_execute_sql_queries($db, "aiocp_db_data.sql", "aiocp_", $table_prefix, $progress_log)) { 
			echo "[OK]</li>";
			error_log("  [END:OK] fill tables with default data\n", 3, $progress_log); //log info
		}
		else {
			echo "[ERROR]</li>";
			error_log("  [END:ERROR] fill tables with default data\n", 3, $progress_log); //log info
		}
	}
	flush();
	return TRUE;
}


/**
 * Parses an SQL file and execute queries.
 * @param string $db database connector
 * @param string $sql_file file to parse
 * @param string $search string to replace
 * @param string $replace replace string
 * @param string $progress_log log file name
 * @return boolean true in case of success, false otherwise.
 */
function F_execute_sql_queries($db, $sql_file, $search, $replace, $progress_log) {
	ini_set("memory_limit", -1); // remove memory limit
	set_time_limit(0); //remove maximum execution time limit
	
	$sql_data = @fread(@fopen($sql_file, 'r'), @filesize($sql_file)); //open and read file
	if ($search) {
		$sql_data = str_replace($search, $replace, $sql_data); // execute search and replace for the given parameters
	}
	$sql_data = str_replace("\r", "", $sql_data); // remove CR
	$sql_data = "\n".$sql_data; //prepare string for replacements
	$sql_data = preg_replace("/\/\*([^\*]*)\*\//si", " ", $sql_data); // remove comments (/* ... */)
	$sql_data = preg_replace("/\n([\s]*)\#([^\n]*)/si", "", $sql_data); // remove comments (lines starting with '#' (MySQL))
	$sql_data = preg_replace("/\n([\s]*)\-\-([^\n]*)/si", "", $sql_data); // remove comments (lines starting with '--' (PostgreSQL))
	$sql_data = preg_replace("/;([\s]*)\n/si", ";\r", $sql_data); // mark valid new lines
	$sql_data = str_replace("\n", " ", $sql_data); // remove carriage returns
	$sql_data = preg_replace("/(;\r)$/si", "", $sql_data); // remove last ";\r"
	$sql_query = explode(";\r", $sql_data); // split sql string into SQL statements
	//execute queries
	//require_once("html_entity_decode_php4.php");
	while(list($key, $sql) = each($sql_query)) { //for query on sql file
	  //$sql = html_entity_decode_php4($sql);
		//error_log("".$sql.";\n", 3, $progress_log.".sql");
		error_log("    [SQL] ".$key."\n", 3, $progress_log); //create progress log file
		echo " "; //print something to keep browser live
		if (($key % 300) == 0) { //force flush output every 300 processed queries
			echo "<!-- ".$key." -->\n"; flush(); //force flush output to browser
		}
		if(!$r = F_aiocpdb_query($sql, $db)) {
			echo "\n<p>".F_aiocpdb_error()."</p>";
			error_log("\n".F_aiocpdb_error()."\n", 3, $progress_log); //progress log file
			return FALSE;
		}
	}	
	return TRUE;
}

/**
 * Create new database. Existing database will be dropped.
 * @param string $host Database server path. It can also include a port number. e.g. "hostname:port" or a path to a local socket e.g. ":/path/to/socket" for the localhost. Note: Whenever you specify "localhost" or "localhost:port" as server, the MySQL client library will override this and try to connect to a local socket (named pipe on Windows). If you want to use TCP/IP, use "127.0.0.1" instead of "localhost". If the MySQL client library tries to connect to the wrong local socket, you should set the correct path as mysql.default_host in your PHP configuration and leave the server field blank.
 * @param string $dbtype database type ('MYSQL' or 'POSTGREQL')
 * @param string $host database host
 * @param string $port database port
 * @param string $user Name of the user that owns the server process.
 * @param string $password Password of the user that owns the server process.
 * @param string $database Database name.
 * @return database link identifier on success, FALSE otherwise.
 */
function F_create_database($dbtype, $host, $port, $user, $password, $database) {
	// open default connection
	if($db = @F_aiocpdb_connect($host.":".$port, $user, $password)) {
		@F_aiocpdb_query("DROP DATABASE ".$database."", $db); // DROP existing database (if exist)
		// create database
		$sql = "CREATE DATABASE ".$database."";
		if ($dbtype == "POSTGRESQL") {
			$sql .= " ENCODING='UNICODE'";
		} elseif ($dbtype == "MYSQL") {
			//$sql .= " DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$sql .= "";
		}
		if(!$r = F_aiocpdb_query($sql, $db)) {
			echo "\n<p>".F_aiocpdb_error()."</p>";
			return FALSE;
		}
		@F_aiocpdb_close($db);
	}
	else {
		// unable to get dbms connection
		echo "\n<p>ERROR: Unable to get Database connection.</p>";
		return FALSE;
	}
	return @F_aiocpdb_connect($host.":".$port, $user, $password, $database);
}

/**
 * Update some configuration files.
 * @param string $db_type database type (MySQL)
 * @param string $db_host database host 
 * @param string $db_port database port number
 * @param string $db_user database user
 * @param string $db_password database password
 * @param string $database_name database name
 * @param string $table_prefix table prefix
 * @param string $path_host host URL
 * @param string $path_aiocp relative URL where this program is installed
 * @param string $path_main real full server path where this program is installed
 * @param string $standard_port standard http web port
 * @param string $progress_log log file name
 * @return boolean true in case of success, false otherwise
 */
function F_update_config_files($db_type, $db_host, $db_port, $db_user, $db_password, $database_name, $table_prefix, $path_host, $path_aiocp, $path_main, $path_dbfiles, $standard_port, $progress_log) {
	
	set_magic_quotes_runtime(0); //disable magic quotes
	
	$config_file = array(); // configuration files
	
	$config_file[0] = "../shared/config/cp_db_config.php";
	$config_file[1] = "../shared/config/cp_paths.php";
	$config_file[2] = "../admin/phpMyAdmin/config.inc.php";
	
	// file parameters to change as regular expressions (0=>search, 1=>replace)
	$parameter = array(); 
	
	$parameter[0] = array(
		
		"0"  => array ("0" => "K_DATABASE_TYPE\", \"([^\"]*)\"", "1" => "K_DATABASE_TYPE\", \"".$db_type."\""),
		"1"  => array ("0" => "K_DATABASE_HOST\", \"([^\"]*)\"", "1" => "K_DATABASE_HOST\", \"".$db_host."\""),
		"2"  => array ("0" => "K_DATABASE_PORT\", \"([^\"]*)\"", "1" => "K_DATABASE_PORT\", \"".$db_port."\""),
		"3"  => array ("0" => "K_DATABASE_NAME\", \"([^\"]*)\"", "1" => "K_DATABASE_NAME\", \"".$database_name."\""),
		"4"  => array ("0" => "K_DATABASE_USER_NAME\", \"([^\"]*)\"", "1" => "K_DATABASE_USER_NAME\", \"".$db_user."\""),
		"5"  => array ("0" => "K_DATABASE_USER_PASSWORD\", \"([^\"]*)\"", "1" => "K_DATABASE_USER_PASSWORD\", \"".$db_password."\""),
		"6"  => array ("0" => "K_TABLE_PREFIX\", \"([^\"]*)\"", "1" => "K_TABLE_PREFIX\", \"".$table_prefix."\"")
	);
	
	$parameter[1] = array(
		"0"  => array ("0" => "K_PATH_HOST\", \"([^\"]*)\"", "1" => "K_PATH_HOST\", \"".$path_host."\""),
		"1"  => array ("0" => "K_PATH_AIOCP\", \"([^\"]*)\"", "1" => "K_PATH_AIOCP\", \"".$path_aiocp."\""),
		"2"  => array ("0" => "K_PATH_MAIN\", \"([^\"]*)\"", "1" => "K_PATH_MAIN\", \"".$path_main."\""),
		"3"  => array ("0" => "K_STANDARD_PORT\", ([^\)]*)", "1" => "K_STANDARD_PORT\", ".$standard_port.""),
		"4"  => array ("0" => "K_PATH_DATABASE_DATA\", \"([^\"]*)\"", "1" => "K_PATH_DATABASE_DATA\", \"".$path_dbfiles."\"")
	);
	
	$parameter[2] = array(
		"0"  => array ("0" => "cfg\['PmaAbsoluteUri'\] = '([^']*)'", "1" => "cfg['PmaAbsoluteUri'] = '".$path_host.$path_aiocp."admin/phpMyAdmin/'"),
		"1"  => array ("0" => "cfg\['Servers'\]\[\\\$i\]\['host'\]([^=]*)= '([^']*)'", "1" => "cfg['Servers'][\$i]['host']          = '".$db_host."'"),
		"2"  => array ("0" => "cfg\['Servers'\]\[\\\$i\]\['port'\]([^=]*)= '([^']*)'", "1" => "cfg['Servers'][\$i]['port']          = '".$db_port."'"),
		"3"  => array ("0" => "cfg\['Servers'\]\[\\\$i\]\['user'\]([^=]*)= '([^']*)'", "1" => "cfg['Servers'][\$i]['user']          = '".$db_user."'"),
		"4"  => array ("0" => "cfg\['Servers'\]\[\\\$i\]\['password'\]([^=]*)= '([^']*)'", "1" => "cfg['Servers'][\$i]['password']      = '".$db_password."'")
	);
	
	while(list($key, $file_name) = each($config_file)) { //for each configuration file
		
		error_log("  [START] process file: ".basename($file_name)."\n", 3, $progress_log); //log info
		echo "\n<li>start process <i>".basename($file_name)."</i> file:";
		echo "\n<ul>";
		//try to change file permissions (unix-like only)
		//chmod($file_name, 0777);
		
		echo "\n<li>open file.................";
		error_log("    open file", 3, $progress_log); //log info
		$fp = fopen($file_name, "r+");
		if (!$fp) {
			echo "[ERROR]</li>";
			error_log(" [ERROR]\n", 3, $progress_log); //log info
		}
		else { // the file has been opened
			echo "[OK]</li>";
			error_log(" [OK]\n", 3, $progress_log); //log info
			
			//read the file
			echo "\n<li>read file.................";
			error_log("    read file", 3, $progress_log); //log info
			$file_data = fread($fp, filesize($file_name));
			if (!$file_data){
				echo "[ERROR]</li>";
				error_log(" [ERROR]\n", 3, $progress_log); //log info
			}
			else { 
				echo "[OK]</li>";
				error_log(" [OK]\n", 3, $progress_log); //log info
				
				//change cfg file values
				while(list($pkey, $pval) = each($parameter[$key])) { //for each file parameter
					echo "\n<li>update value ".$pkey." ...........";
					error_log("      update value ".$pkey."", 3, $progress_log); //log info
					$file_data = ereg_replace ($pval[0], $pval[1], $file_data); //update cfg parameters
					echo "[OK]</li>";
					error_log(" [OK]\n", 3, $progress_log); //log info
				}
			}
			
			//write the file
			echo "\n<li>write file................";
			error_log("    write file", 3, $progress_log); //log info
			rewind ($fp);
			if (!fwrite ($fp, $file_data)) {
				echo "[ERROR]</li>";
				error_log(" [ERROR]\n", 3, $progress_log); //log info
			}
			else { 
				echo "[OK]</li>";
				error_log(" [OK]\n", 3, $progress_log); //log info
			}
			
			if (strlen($file_data) < filesize($file_name)) {
				ftruncate ($fp, strlen($file_data)); //truncate file
			}
			
			echo "\n<li>close file................";
			error_log("    close file", 3, $progress_log); //log info
			if (fclose($fp)) {
				echo "[OK]</li>";
				error_log(" [OK]\n", 3, $progress_log); //log info
			}
			else {
				echo "[ERROR]</li>";
				error_log(" [ERROR]\n", 3, $progress_log); //log info
			}
		}
		
		//try to set file permissions to read only (unix-like only)
		//chmod($file_name, 0644);
		echo "\n</ul>";
		echo "\n</li>";
		echo "\n<li>end process <i>".basename($file_name)."</i> file</li>";
		error_log("  [END] process file: ".basename($file_name)."\n", 3, $progress_log); //log info
	}
	set_magic_quotes_runtime(get_magic_quotes_gpc()); //restore magic quotes settings
	flush(); // force browser output
	return TRUE;
}

/**
 * Connects to specified mysql database.
 * @param int $db database connection handler
 * @param string $database_name database name
 * @return int $db database connection handler
 */
function F_database_connect($db, $database_name) {
	// Make a database connection
	if(!@F_aiocpdb_select_db($database_name, $db)) {
		echo "\n<p>".F_aiocpdb_errno().": ".F_aiocpdb_error()."</p>";
		die("Unable to find the database <b>".$database_name."</b> on MySQL server.");
	}
	return $db;
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>