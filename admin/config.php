<?php
session_start();

if (!array_key_exists("valid_user", $_SESSION) || !strlen($_SESSION["valid_user"]))
	header("location:http://biomttbp01/workflow");
	
if (get_magic_quotes_gpc()) {
	$process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
	while (list($key, $val) = each($process))
		{
		foreach ($val as $k => $v)
			{
			unset($process[$key][$k]);
			if (is_array($v))
				{
				$process[$key][stripslashes($k)] = $v;
				$process[] = &$process[$key][stripslashes($k)];
				}
			else
				{
				$process[$key][stripslashes($k)] = stripslashes($v);
				}
			}
		}
	unset($process);
}

	error_reporting(E_ALL^E_NOTICE);
	
	define("NAME_SITE", "SAS Template Library");

	$dir = "..";
	define("SITE_DOCS_DIR", "$dir/");

	define("SITE_ELEMENT_DIR", SITE_DOCS_DIR . "elements/");
	define("SITE_LIBS_DIR", SITE_DOCS_DIR . "include/");
	define("SITE_IMAGES_DIR", SITE_DOCS_DIR . "images/");	
	define("SITE_FILES_DIR", '/biostats/testproduct/templatestudy/csr/prog/');
	define("SITE_UPLOAD_DIR", SITE_DOCS_DIR . "uploads/");	

	define("SITE_URL", "http://biomttbp01/tlibrary");
	define("SITE_FILES_URL", SITE_URL . '/files/');

	define("SITE_IMAGES_URL", SITE_URL . '/images/');
	define("SITE_PHP_DIR", SITE_LIBS_DIR . "libs/");
	define("SITE_JS_DIR", SITE_URL . "/js/");
	define("SITE_UPLOAD_URL", SITE_URL . '/uploads/');

define('WF_BINARY_DIR', "/SASGRID/u02/www/workflow/bin");
define('WF_SHELL_COMMAND', WF_BINARY_DIR . "/wfwrapper " . WF_BINARY_DIR . "/docmd");
define('WF_SAS_COMMAND', WF_BINARY_DIR . "/wfwrapper " . WF_BINARY_DIR . "/gridsas");

	define('DB_SERVER', "biomttbp01");
	define('DB_USER', "root");
	define('DB_PASS', "Biogen2015");
	define('DB_NAME', "podtemlib");
	
	define('DB_EXECUTE_WRITES', "1");
	define('DB_PRINT_QUERIES', "0");
	define('DB_PRINT_ERRORS', "1");
	
	define('DEBUG', "0");
	define('WORKFLOW_URL', "http://biomttbp01/workflow");

	require_once(SITE_LIBS_DIR . "common.lib.php");
	require_once(SITE_LIBS_DIR . "db.class.php");
	require_once(SITE_LIBS_DIR . "Page.class.php");

	$site_caching = false;
	$dev = true;
	$debug = false;
	$sql_logging = false;
	$notifications = true;

	error_reporting(0);

	$db = new db();	// general usage database pointer
	$wfdb = new db(DB_SERVER, DB_USER, DB_PASS, "workflow");
?>
