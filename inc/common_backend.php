<?php
/**
 * EVE MultiPurpose Application
 *
 * common_backend.inc.php - Holds all the default code that need to be run on all pages.
 *
 * PHP version 5
 *
 * LICENSE: This file is part of EVE MultiPurpose Application also know as KKS.
 * KKS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3 of the License.
 *
 * KKS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with KKS.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author     Ben Cole <wengole@gmail.com>
 * @author     Claus Pedersen <satissis@gmail.com>
 * @author     Michael Cummings <dragonrun1@gmail.com>
 * @author     Stephen Gulick <stephenmg12@gmail.com>
 * @copyright  2008-2009 (C) Ben Cole, Claus Pedersen, Stephen Gulick, and Michael Cummings
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @package    KKS
 * @version    SVN: $Id$ (Only to be used in key files)
 * @link       http://code.google.com/p/empa/
 * @link       http://www.eve-online.com/
 */
/**
 * @internal Only let this code be included or required not ran directly.
 */
if (basename(__FILE__) == basename($_SERVER['SCRIPT_NAME'])) {
  exit();
};
/** Any errors that are trigger in this file are reported to the system default
 * logging location until we're done setting up some of the required vars and
 * we can start our own logging.
 */
// Used to over come path issues caused by how script is ran on server.
$incDir = realpath(dirname(__FILE__));
chdir($incDir);
$ds = DIRECTORY_SEPARATOR;
// Set some basic common settings so we know we'll get to see any errors etc.
error_reporting(E_ALL);
ini_set('ignore_repeated_errors', 0);
ini_set('ignore_repeated_source', 0);
ini_set('html_errors', 0);
ini_set('display_errors', 1);
ini_set('error_log', NULL);
ini_set('log_errors', 0);
/* // Special debugging command-line override.
if (defined('KKS_DEBUG')) {
  ob_start();
  trigger_error(str_pad(' Pre-custom ', 75, '-', STR_PAD_BOTH), E_USER_NOTICE);
  ini_set('track_errors', 1);
} else {
  ini_set('track_errors', 0);
};// else defined YAPEAL_DEBUG ... */
// Insure minimum version of PHP 5 we need to run.
if (version_compare(PHP_VERSION, '5.2.1', '<')) {
  echo 'Need minimum of PHP 5.2.1 to use this software!';
  exit(1);
};
if(ini_get('safe_mode')) {
    $error='Safe Mode is on and will cause problems with this software!';
    trigger_error($error, E_USER_WARNING);
}
// Check for some required extensions
$required = array('curl', 'date', 'mysqli', 'SimpleXML', 'SPL');
$exts = get_loaded_extensions();
foreach ($required as $ext) {
  if (!in_array($ext, $exts)) {
    echo 'The required PHP extension ' . $ext . ' is missing!';
    exit(2);
  };
};// foreach $exts ...

/* **************************************************************************
 * Check if valid ini config file
 * **************************************************************************/
// Set a constant for location of configuration file.
$iniFile = realpath($incDir . $ds . '..' . $ds . 'config' . $ds . 'kks.ini');
// Check iniFile
if (!($iniFile && is_readable($iniFile) && is_file($iniFile))) {
  echo 'The required ' . $iniFile . ' configuration file is missing';
  exit(3);
};
// Grab the info from ini file.
$iniVars = parse_ini_file($iniFile, TRUE);
// Abort if required sections aren't defined
//$sections = array('Database', 'Prefix', 'KKSLogging');
$sections = array('Database', 'Prefix');
$mess = '';
foreach ($sections as $section) {
  if (!isset($iniVars[$section])) {
    $mess .= 'Required section [' . $section;
    $mess .= '] is missing from ' . $iniFile . '<br />' . PHP_EOL;
  }; // if isset ...
};
if (!empty($mess)) {
  echo $mess;
  exit(4);
};
// Set vars use in error messages.
$req1 = 'Missing required setting ';
$req2 = ' in ' . $iniFile;
$nonexist = 'Nonexistent directory defined for ';
/* **************************************************************************
 * Paths
 * **************************************************************************/
require_once $incDir . $ds . 'common_paths.php';
// Check writable paths
$mess = '';
if (!is_writable(KKS_CACHE)) {
  $mess .= realpath(KKS_CACHE) . ' is not writeable<br />' . PHP_EOL;
};
/*
$sections = array('dwoo');
foreach ($sections as $section) {
  $realpath = realpath(KKS_CACHE . $section);
  if (!$realpath || !is_dir($realpath)) {
    $mess .= 'Missing required directory ' . KKS_CACHE . $section . '<br />' . PHP_EOL;
  };
  if (!is_writable($realpath)) {
    $mess .= KKS_CACHE . $section . ' is not writeable<br />' . PHP_EOL;
  };
};// foreach $sections ...
*/
//Set in common Paths
/*$subsections = array('cache', 'compiled');
foreach ($subsections as $section) {
  $realpath = realpath(KKS_CACHE . 'dwoo' . $ds . $section);
  if (!$realpath || !is_dir($realpath)) {
    $mess .= 'Missing required directory ' . KKS_CACHE . 'dwoo' . $ds . $section . '<br />' . PHP_EOL;
  };
  if (!is_writable($realpath)) {
    $mess .= KKS_CACHE . 'dwoo' . $ds . $section . ' is not writeable<br />' . PHP_EOL;
  };
};// foreach $subsections ...
*/
// log_dir is relative to YAPEAL_CACHE
/*$realpath = realpath(KKS_CACHE . 'log');
if ($realpath && is_dir($realpath)) {
  define('KKS_LOG', $realpath . $ds);
} else {
  $mess .= $nonexist . 'log<br />' . PHP_EOL;
};
if (!is_writable($realpath)) {
  $mess .= $realpath . ' is not writeable<br />' . PHP_EOL;
};*/
// Output file and dir check error
if (!empty($mess)) {
  echo $mess;
  exit(5);
};
/* **************************************************************************
 * KKSLogging section
 * **************************************************************************/
// Special debugging command-line override.
/*
$settings = array('error_log', 'notice_log', 'warning_log');
foreach ($settings as $setting) {
  if (isset($iniVars['KKSLogging'][$setting])) {
    define('KKS_' . strtoupper($setting),
      KKS_LOG . $iniVars['KKSLogging'][$setting]);
  } else {
    echo $req1 . $setting . $req2;
    exit();
  };// else isset $iniVars...
};// foreach $settings ...
// Set error reporting level.
if (isset($iniVars['KKSLogging']['log_level'])) {
  error_reporting($iniVars['KKSLogging']['log_level']);
  $KKS_LOG_LEVEL = $iniVars['KKSLogging']['log_level'];
} else {
  echo $req1 . $setting . $req2;
  exit();
};
*/
/* **************************************************************************
 * Change over to our custom error and exception code
 * **************************************************************************/
// Change some error logging settings.
/*
ini_set('error_log', KKS_ERROR_LOG);
ini_set('log_errors', 1);
require_once KKS_INC . 'elog.php';
require_once KKS_CLASS . 'KKSErrorHandler.php';
// Start using custom error handler.
set_error_handler(array('KKSErrorHandler', 'handle'));
// Setup custom exception handlers
require_once KKS_CLASS . 'ADODB_Exception.php';
require_once KKS_CLASS . 'LogExceptionObserver.php';
// Setup exception observers.
$logObserver = new LogExceptionObserver(KKS_WARNING_LOG);
// Attach (start) our custom printing and logging of exceptions.
ADODB_Exception::attach($logObserver);
unset($logObserver);
*/
/* **************************************************************************
 * Prefix section
 * **************************************************************************/
$settings = array('kks', 'yapeal', 'eve');
foreach ($settings as $setting) {
  // Set to section value if it exists.
  if (isset($iniVars['Prefix'][$setting])) {
    /**
     * $prefix['xxx']
     *
     * This is the prefix to use in empa. there is 4 types
     * <pre>
     * $prefix['kks']   = KKS core system table prefix
     * $prefix['yapeal'] = Yapeal table prefix
     * $prefix['eve']    = EVE Static database dump table prefix
     * $prefix['mod']    = Mod table prefix
     * </pre>
     *
     * @deprecated deprecated since version 0.0006
     */
    $prefix[$setting] = $iniVars['Prefix'][$setting];
    /**
     * PREFIX_XXX
     *
     * This is the prefix to use in empa. there is 4 types
     * <pre>
     * PREFIX_KKS   = KKS core system table prefix
     * PREFIX_YAPEAL = Yapeal table prefix
     * PREFIX_EVE    = EVE Static database dump table prefix
     * PREFIX_MOD    = Mod table prefix
     * </pre>
     *
     * @since Version 0.0006
     */
    define("PREFIX_" . strtoupper($setting), $iniVars['Prefix'][$setting]);
  } else {
    echo $req1 . $setting . $req2 . ' section [Prefix].';
    exit;
  };// else isset $iniVars...
};// foreach $settings ...

/* **************************************************************************
 * Cache section
 * **************************************************************************/
$settings = array('killlist', 'stats', 'killdetail', 'dwoo');
foreach ($settings as $setting) {
  // Set to section value if it exists.
  if (isset($iniVars['Cache'][$setting])) {
        /**
     * KKS_CACHE_XXX
     *     
     */
    define("KKS_CACHE_" . strtoupper($setting), $iniVars['Cache'][$setting]);
  } else {
    echo $req1 . $setting . $req2 . ' section [Cache].';
    exit;
  };// else isset $iniVars...
};// foreach $settings ...


/* **************************************************************************
 * Salt value
 * **************************************************************************/
if (isset($iniVars['salt'])) {
  define("KKS_SALT", $iniVars['salt']);
} else {
  echo $req1 . 'salt' . $req2;
  exit;
};// else isset $iniVars...
/* **************************************************************************
 * Datebase section
 * **************************************************************************/
$settings = array('database', 'driver', 'host', 'suffix', 'username', 'password', 'xmldriver');
$data = array();
foreach ($settings as $setting) {
  if (isset($iniVars['Database'][$setting])) {
    $data[$setting] = $iniVars['Database'][$setting];
  } else {
    echo $req1 . $setting . $req2 . ' section [Database].';
    exit;
  };
};// foreach $settings ...
// Extract the required fields from array.
extract($data);
// Define some constants from vars.
/**
 * Defines the database Name.
 */
define('KKS_DB', $database);
/**
 * Defines the DSN used for ADOdb connection.
 */
define('KKS_DSN', $driver . $username . ':' . $password . '@' . $host . '/' . $database . $suffix);
/**
 * Defines the XML DSN used for ADOdb axmls schema connection.
 */
define('KKS_XML_DSN', $xmldriver . $username . ':' . $password . '@' . $host . '/' . $database);
/*
* Fire up database connection
*/
try {
  require_once (KKS_ADODB. 'adodb.inc.php');
  // Connect to db
  $con = ADONewConnection(KKS_DSN);
  // Set connection charset
  $con->Execute("SET NAMES utf8");
}
catch(ADODB_Exception $e) {
  die($e->getMessage());
}
/*
* Turn ADODB Logging On/Off
*/
define('KKS_ADODB_LOG', 'FALSE');

//Turn On/Off ADODB Debug
define('KKS_ADODB_DEBUG', 'FALSE');
/*
 * Get KKS config from database
 
try {
  // Get KKS config from db
  $query = "SELECT * FROM `" . PREFIX_KKS . "Config`";
  $rs = $con->Execute($query);
  $config = $rs->FetchRow();
  $rs->close();
}
catch(ADODB_Exception $e) {
  die($e->getMessage());
}
/*
 * Get corp info
 
try {
  $query = "SELECT * FROM `" . PREFIX_YAPEAL . "corpCorporationSheet` WHERE `corporationID` = " . $config['corporationID'];
  $rs = $con->Execute($query);
  $corpinfo = $rs->FetchRow();
  $rs->close();
}
catch(ADODB_Exception $e) {
  die($e->getMessage());
}
*/
define('KKS_KBCORPID', '170567768');
?>
