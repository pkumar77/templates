<?php
session_start();

// Report all errors except E_NOTICE
error_reporting(E_ALL & ~(E_STRICT|E_DEPRECATED|E_NOTICE));

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



define("NAME_SITE", "Site name here..");

define("SITE_DIR", "/SASUserHome/pkumar/public_html/templates/templates");
define("SITE_DOCS_DIR", SITE_DIR."/");

define("SITE_ELEMENT_DIR", SITE_DOCS_DIR . "elements/");
define("SITE_LIBS_DIR", SITE_DOCS_DIR . "include/");
define("SITE_IMAGES_DIR", SITE_DOCS_DIR . "images/");	
define("SITE_UPLOAD_DIR", SITE_DOCS_DIR . "uploads/");	

define("SITE_URL", "http://biomttbt01/~pkumar/templates/templates");
define("SITE_FILES_URL", SITE_URL . '/files/');

define("SITE_IMAGES_URL", SITE_URL . '/images/');
define("SITE_PHP_DIR", SITE_LIBS_DIR . "libs/");
define("SITE_JS_DIR", SITE_URL . "/js/");
define("SITE_UPLOAD_URL", SITE_URL . '/uploads/');

define('WF_BINARY_DIR', "/SASGRID/u02/www/dev/workflow/bin");
define('WF_SHELL_COMMAND', WF_BINARY_DIR . "/wfwrapper " . WF_BINARY_DIR . "/docmd");
define('WF_SAS_COMMAND', WF_BINARY_DIR . "/wfwrapper " . WF_BINARY_DIR . "/gridsas");

define('DB_SERVER', "biomttbt01");
define('DB_USER', "root");
define('DB_PASS', "Biogen2015");
define('DB_NAME', "templates");

define('DB_EXECUTE_WRITES', "1");
define('DB_PRINT_QUERIES', "1");
define('DB_PRINT_ERRORS', "1");

define('LDAP_SERVER', "camvmcorpdc01");

define('DEBUG', "0");
define('WORKFLOW_URL', "http://biomttbt01/dev/workflow");

define('BGCOLOR_LEFT_COLUMN', "#ffffff");
define('WIDTH_LEFT_COLUMN', "5");
define('BGCOLOR_RIGHT_COLUMN', "#FFFFFF");
define('WIDTH_RIGHT_COLUMN', "95");
define('TABLE_NAME', "employee");



require_once(SITE_LIBS_DIR . "common.lib.php");
require_once(SITE_LIBS_DIR . "db.class.php");
require_once(SITE_LIBS_DIR . "Page.class.php");

$site_caching = false;
$dev = true;
$debug = true;
$sql_logging = false;
$notifications = true;

//error_reporting(0);

$db = new db();	// general usage database pointer
$wfdb = new db(DB_SERVER, DB_USER, DB_PASS, "workflow");

$glossary = new glossary();	// general usage database pointer

if(!isset($_SESSION['userid']) && basename($_SERVER['SCRIPT_NAME'])!='index.php' && !isset($_REQUEST['internal_script'])){
	header("location:index.php");
	exit;
}

?>
