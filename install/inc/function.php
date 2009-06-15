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
 * @author     Stephen Gulickk <stephenmg12@gmail.com>
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
}

/**
 * @internal Load functions that is both used by EMPA and EMPA Setup
 */
require_once(EMPA_INC.'globalfunctions.php');

//////////////////////////////////
// Functions
//////////////////////////////////
/**
 * Input standard Site header 
 *
 * @param string $subtitle. If set, then it will show the subtitle in the browser top title bar.
 * @return string Output HTML header until right after <body> start
 * @internal This can only be used in EMPA Setup.
 */
function setupHeader($subtitle = NULL) {
  // get global values
  global $dwoo;
  /*
  * Load a template file (name it as you please), this is reusable
  */
  $tpl = new Dwoo_Template_File(SETUP_TEMPLATE_DIR . 'header.tpl');
  /*
  * Create Dwoo data set
  */
  $data = new Dwoo_Data();
  /*
  * Assign data to template
  */
  if ($subtitle !== NULL) {
    $data->assign('pagetitle', 'EMPA Setup - ' . $subtitle);
  } else {
    $data->assign('pagetitle', 'EMPA Setup');
  };// if $title !== NULL
  /*
  * Output dwoo result
  */
  $dwoo->output($tpl, $data);
  unset($data, $tpl);
}
/**
 * Input standard Site footer
 *
 * @return string Output HTML </body> stop and closes the rest of the page HTML tags.
 * @internal This can only be used in EMPA Setup.
 */
function setupFooter() {
  /*
  * get global values
  */
  global $dwoo;
  /*
  * Load a template file (name it as you please), this is reusable
  */
  $tpl = new Dwoo_Template_File(SETUP_TEMPLATE_DIR . 'footer.tpl');
  /*
  * Output dwoo result
  */
  $dwoo->output($tpl);
  unset($tpl);
}
/**
 * Database Handler
 *
 * @param string $dbtype This is the command for what to do.
 * <pre>
 * CON      = Check if you are connected to database.
 *            This don't need any other parameters.
 *
 * UPD      = Build Insert Update Query and execute it.
 *            This needs $info, $data and $types parameters.
 *            $info  = Table name with table prefix.
 *            $data  = Data array for what to insert.
 *            $types = Table structure that you are inserting into.
 *            See BuildInsertUpdateQuery function on how to create $data and $types array
 *
 * CHKTAB   = Check if table exista in database.
 *            This needs $info.
 *            $info =  Table name without table prefix.
 * </pre>
 * @param string $info This holds the table name that you need to access if required.
 * @param array $data This holds the data array that need to be inserted or updated in a table.
 * @param array $types This holds the table structure for an insert/update.
 * @return bool True on success and false if not.
 * @internal This can only be used in EMPA Setup.
 */
function DBHandler($dbtype, $info = "", $data = "", $types = "") {
  global $con, $output, $stop, $ini, $dbconerror, $config;
  $select = false;
  $prefix = '';
  if (isset($ini['Database']['table_prefix'])) {
    $prefix = $ini['Database']['table_prefix'];
  };
  if (isset($config['DB_Prefix'])) {
    $prefix = $config['DB_Prefix'];
  };
  switch ($dbtype) {
    case 'CON':
      // Check Connection
      $errorval = 1;
      $request_type = DATABASE . ": " . CONNECT_TO;
      $okay = CONNECTED;
      $select = $con->IsConnected();
      if ($select) {
        try {
          $con->Execute("SET NAMES utf8");
        }
        catch(ADODB_Exception $e) {
          trigger_error($e->getMessage() , E_USER_NOTICE);
        }
      } else {
        $errortext = $dbconerror;
      };// if $select
    break;
    case 'UPD':
      // Check Connection
      $errorval = 1;
      $request_type = DATABASE . ": " . UPDATE;
      $okay = DONE;
      $info = $prefix . $info;
      $select = BuildInsertUpdateQuery($data, $types, $info);
      if ($select) {
        try {
          $con->Execute($select);
        }
        catch(ADODB_Exception $e) {
          trigger_error($e->getMessage() , E_USER_NOTICE);
        }
      } else {
        $errortext = FAILED;
      };// if $select
    break;
    case 'CHKTAB':
      // Check If Table Exists
      $query = "SELECT count(*) FROM information_schema.tables
                WHERE table_schema = '" . $ini['Database']['database'] . "'
                AND table_name = '" . $prefix . $info . "'";
      $rs = $con->GetOne($query);
      if ($rs > 0) {
        return true;
      } else {
        return false;
      }; // if ($result[0]>0)
    break;
    default:
      return false;
  };// case
  /*
  * Show action
  */
  if (!$select) {
    /**
     * Output error
     */
    $stop+= $errorval;
    $output.= '  <tr>' . PHP_EOL;
    $output.= '    <td class="tableinfolbl" style="text-align: left;">' . $request_type . '</td>' . PHP_EOL;
    $output.= '    <td class="notis">' . $info . '</td>' . PHP_EOL;
    $output.= '    <td class="warning">' . $errortext . '</td>' . PHP_EOL;
    $output.= '  </tr>' . PHP_EOL;
    return false;
  } else {
    /**
     * Output success
     */
    $output.= '  <tr>' . PHP_EOL;
    $output.= '    <td class="tableinfolbl" style="text-align: left;">' . $request_type . '</td>' . PHP_EOL;
    $output.= '    <td class="notis">' . $info . '</td>' . PHP_EOL;
    $output.= '    <td class="good">' . $okay . '</td>' . PHP_EOL;
    $output.= '  </tr>' . PHP_EOL;
    return true;
  };
}
/**
 * Dir and File write enabled checker.
 *
 * @param string $path This is the path to the file or dir to check, from EMPA base dir.
 * @return array Append an exsisting array to the output template
 * @internal This can only be used in EMPA Setup.
 */
function WritChecker($path) {
  global $data, $dwoo, $chmodcheck;
  if (is_file(EMPA_BASE . $path)) {
    $type = '(File)';
    $cmod = 'file to 666';
    $mod = 0666;
  } else {
    $type = '(Dir)';
    $cmod = 'dir to 777';
    $mod = 0777;
  };
  if (is_writable(EMPA_BASE . $path)) {
    $write = array(
      'path' => $type . ' ' . $path,
      'status' => 'Yes',
      'check' => 1
    );
  } else {
    if (chmod(EMPA_BASE . $path, $mod)) {
      $write = array(
        'path' => $type . ' ' . $path,
        'status' => 'Yes',
        'check' => 1
      );
    } else {
      $write = array(
        'path' => $type . ' ' . $path,
        'status' => 'No - chmod ' . $cmod,
        'check' => 0
      );
      $chmodcheck++;
    }; // if (chmod(EMPA_BASE.$path,$mod))
  };
  $data->append('write', $write);
  unset($write);
}
/**
 * Get converted revision string to a integer value
 *
 * @param string $rev This is the full $Revision: xxx $ string that need
 * to be converted over to an integer, so we can make an version check.
 * @return integer Returns the revision number from the string in an int number.
 * @internal This can only be used in EMPA Setup.
 */
function conRev($rev) {
  $rev = preg_replace('/\$Revision: /', '', $rev);
  $rev = preg_replace('/ \$/', '', $rev);
  return (int)$rev;
}

?>
