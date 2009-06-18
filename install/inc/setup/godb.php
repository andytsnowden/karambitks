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
 * Turn off max execution time.
 */
set_time_limit(0);
/*
 * Set some values.
 */
$prefix = $_REQUEST['Prefix'];
if (isset($_REQUEST['Install'])) {
  $installmod = $_REQUEST['Install'];
} else {
  $installmod = array();
};// else (isset($_REQUEST['Install']))
/*
 * Set Dwoo Template.
 */
$tpl = new Dwoo_Template_File(SETUP_TEMPLATE_DIR . 'dodb_head.tpl');
/*
 * Output page head
 */
setupHeader('Database Settings');
$dwoo->output($tpl);
unset($tpl);
@ob_flush();
flush();
/*
 * Set some values
 */
$stop = 0;
$modnum = 0;
/*
 * Check DB connection
 */
require_once (KKS_ADODB. 'adodb.inc.php');
/*
 * Check DB connection
 */
$dsn = 'mysql://' . $_REQUEST['DB_Username'] . ':' . $_REQUEST['DB_Password'] . '@' .
  $_REQUEST['DB_Host'] . '/' . $_REQUEST['DB_Database'];
try {
  $con = ADONewConnection($dsn);
}
catch(ADODB_Exception $e) {
  OutPut(array(
    'action' => 'Database: Connect to',
    'info' => $_REQUEST['DB_Host'],
    'status' => $e->getMessage() ,
    'check' => 0
  ));
  $stop++;
}
if (isset($con) && $con->IsConnected()) {
  OutPut(array(
    'action' => 'Database: Connect to',
    'info' => $_REQUEST['DB_Host'],
    'status' => 'Connected',
    'check' => 1
  ));
  $con->Execute("SET NAMES utf8");
  /*
   * Start time. Used to tell how long the queryes is taking total
   */
  $StartTotal = microtime(true);
  /*
   * Create Tables
   */
  $schemaDir['kks'] = KKS_INSTALL;
  $schemaDir['yapeal'] = KKS_INSTALL . 'yapeal' . $ds;
  $schemaDir['db_dump'] = KKS_INSTALL . 'static' . $ds;
  foreach ($schemas as $type => $schemaFiles) {
    foreach ($schemaFiles as $schemaFile) {
      if ($stop == 0) {
        $Start = microtime(true);
        /*
         * Create Tables
         */
        $saveErrHandlers = $con->IgnoreErrors();
        $schema = new adoSchema($con);
        //$schema->debug = false;
        $schema->ExecuteInline(FALSE);
        $schema->ContinueOnError(FALSE);
        $schema->SetUpgradeMethod('ALTER');
        $schema->SetPrefix($prefix[$type], FALSE);
        $xml = _file_get_contents($schemaDir[$type] . $schemaFile . '.xml');
        $sql = $schema->ParseSchemaString($xml);
        $result = $schema->ExecuteSchema($sql);   
        if ($result == 2) {
          $Stop = microtime(true);
          $LoadTime = $Stop - $Start;
          OutPut(array(
            'action' => 'Database: Execute',
            'info' => $schemaFile . '.xml',
            'status' => 'Done in ' . number_format($LoadTime, 3) . 's',
            'check' => 1
          ));
        } elseif ($result == 1) {
          OutPut(array(
            'action' => 'Database: Execute',
            'info' => $schemaFile . '.xml',
            'status' => 'Error',
            'check' => 0
          ));
          $schema->SaveSQL(KKS_CACHE . $type . '_' . $schemaFile . '_missed_tables.sql');
          $stop++;
        } else {
          OutPut(array(
            'action' => 'Database: Execute',
            'info' => $schemaFile . '.xml',
            'status' => 'Failed',
            'check' => 0
          ));
          $stop++;
        };// else $result
        unset($schema);
        $con->IgnoreErrors($saveErrHandlers);
      };// if $stop == 0
    };
  };
  /*
   * Update AccessPerApi
   */
   
  //if ($stop == 0) {
    //$Start = microtime(true);
    /*
     * Insert some data
     */
     /*
    $dbdata = array();
    foreach ($accessPerApi as $level => $ApiList) {
      $dbdata[] = array('Id' => $level, 'ApiList' => implode(' ', $ApiList));
    };
    $types = array('Id' => 'I', 'ApiList' => 'C');
    $query = BuildInsertUpdateQuery($dbdata, $types, $prefix['empa'] . 'AccessPerApi');
    unset($dbdata);
    try {
      $con->Execute($query);
      $Stop = microtime(true);
      $LoadTime = $Stop - $Start;
      OutPut(array(
        'action' => 'Database: Update Table',
        'info' => $prefix['empa'] . 'AccessPerApi',
        'status' => 'Done in ' . number_format($LoadTime, 3) . 's',
        'check' => 1
      ));
    }
    catch(ADODB_Exception $e) {
      OutPut(array(
        'action' => 'Database: Update Table',
        'info' => $prefix['empa'] . 'AccessPerApi',
        'status' => $e->getMessage() ,
        'check' => 0
      ));
      $stop++;
    }
  }; // if $stop = 0
  */
  /*
   * Stop time. Used to tell how long the queryes is taking total
   */
  $StopTotal = microtime(true);
  $TotalLoadTime = $StopTotal - $StartTotal;
};// if $con->IsConnected()
/*
 * Set Dwoo Template and Data.
 */
$tpl = new Dwoo_Template_File(SETUP_TEMPLATE_DIR . 'dodb_foot.tpl');
$data = new Dwoo_Data();
/*
 * Assign Some Data.
 */
$data->assign('link', $_SERVER['SCRIPT_NAME'] . '?funk=configapi');
$data->assign('stop', $stop);
$data->assign('totaltime', number_format($TotalLoadTime, 3));
$exceptions = array('Install', 'Prefix[testmod]');
$data->assign('inputHiddenPost', inputHiddenPost($exceptions));
$data->assign('siteSalt', substr(md5(uniqid(rand() , true)) , 0, 20));
/*
 * Output page foot
 */
$dwoo->output($tpl, $data);
unset($tpl, $data);
setupFooter();
if (isset($con) && $con!=FALSE && $con->IsConnected()) {
  $con->close();
};
?>
