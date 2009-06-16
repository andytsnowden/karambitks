<?php
/**
 * EVE MultiPurpose Application
 *
 * EMPA Setup.
 *
 * PHP version 5
 *
 * LICENSE: This file is part of EVE MultiPurpose Application also know as EMPA.
 * EMPA is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3 of the License.
 *
 * EMPA is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with EMPA.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author     Ben Cole <wengole@gmail.com>
 * @author     Claus Pedersen <satissis@gmail.com>
 * @author     Michael Cummings <dragonrun1@gmail.com>
 * @author     Stephen Gulick <stephenmg12@gmail.com>
 * @copyright  2008-2009 (C) Ben Cole, Claus Pedersen, Stephen Gulick, and Michael Cummings
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @package    EMPA
 * @subpackage EMPASetup
 * @version    SVN: $Id$ (Only to be used in key files)
 * @link       http://code.google.com/p/empa/
 * @link       http://www.eve-online.com/
 */
/**
 * @internal Only let this code be included or required not ran directly.
 */
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  exit();
};
/*
 * Define some values
 */
define("SETUP_TEMPLATE_DIR", $baseDir . $ds . 'tpl' . $ds);
define("SETUP_INC", $baseDir . $ds . 'inc' . $ds);
define("SETUP_FILES", SETUP_INC . 'setup' . $ds);
//require_once KKS_ADODB . 'ADODB_Exception.php';
//require_once KKS_CLASS . 'LogExceptionObserver.php';
/*
 * Define log file path
 */
define("KKS_ERROR_LOG", KKS_CACHE . $ds . "log" . $ds . "KKS_setup_error.log");
/*
 * Setup error reporting.
 */
error_reporting(E_ALL);
ini_set('ignore_repeated_errors', 0);
ini_set('ignore_repeated_source', 0);
ini_set('html_errors', 0);
ini_set('display_errors', 1);
//ini_set('error_log', KKS_ERROR_LOG);
ini_set('log_errors', 1);
/*
 * Setup exception observers.
 */
/*$logObserver = new LogExceptionObserver(KKS_ERROR_LOG);
ADODB_Exception::attach($logObserver);
unset($logObserver);*/
/*
 * Require adodb.inc.php to be able to create connection to db
 */
require_once (KKS_ADODB . $ds . 'adodb.inc.php');
/*
 * Require adodb-xmlschema03.inc.php to be able to use AXMLS
 */
require_once (KKS_ADODB. $ds . 'adodb-xmlschema03.inc.php');
/*
 * Dwoo template engine include
 */
if (!(is_writable(KKS_CACHE.'Dwoo_Compile') && is_writable(KKS_CACHE.'Dwoo_Cache'))) {
  echo 'ERROR:<br />This folders need to be writeable:<br />' . PHP_EOL . KKS_CACHE . 'Dwoo_Cachep' . $ds . 'compiled' . $ds . '<br />' . PHP_EOL . EMPA_CACHE . 'dwoo' . $ds . 'cache' . $ds . '<br />' . PHP_EOL;
  trigger_error('Before Dwoo Init: Cache and compiled folder not write able', E_USER_ERROR);
  exit;
}
require_once (KKS_DWOO. 'dwooAutoload.php');
//Create the Dwoo controller
$dwoo = new Dwoo(KKS_CACHE.'Dwoo_Compile', KKS_CACHE.'Dwoo_Cache');
?>
