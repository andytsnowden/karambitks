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
* Set Dwoo Template and Data.
*/
$tpl = new Dwoo_Template_File(SETUP_TEMPLATE_DIR . 'req.tpl');
$data = new Dwoo_Data();
/*
 * Run the script if check_c_action(); didn't exit the script
 */
$error = 0;
$chmodcheck = 0;
$req = array();
$data->assign('req', $req);
/*
 * Insure minimum version of PHP 5 we need to run.
 */
if (version_compare(PHP_VERSION, '5.2.1', '<')) {
  $req = array(
    'require' => 'PHP Version 5.2.1 +',
    'result' => phpversion() ,
    'status' => 'Failed',
    'check' => 0
  );
  $error++;
} else {
  $req = array(
    'require' => 'PHP Version 5.2.1 +',
    'result' => phpversion() ,
    'status' => 'Ok',
    'check' => 1
  );
};
$data->append('req', $req);
// Check for some required extensions
$required = array('curl', 'date', 'mysqli', 'SimpleXML', 'SPL', 'pcre');
$exts = get_loaded_extensions();
foreach ($required as $ext) {
  if (!in_array($ext, $exts)) {
    $req = array(
      'require' => 'PHP extension: ' . $ext,
      'result' => 'Missing',
      'status' => 'The required PHP extension ' . $ext . ' is missing!',
      'check' => 0
    );
    $error++;
  } else {
    $req = array(
      'require' => 'PHP extension: ' . $ext,
      'result' => 'Loaded',
      'status' => 'Ok',
      'check' => 1
    );
  };
  $data->append('req', $req);
};
unset($req);
/*
 * check if writable
 */
$write = array();
$data->assign('write', $write);
WritChecker('cache' . $ds);
WritChecker('cache' . $ds . 'Dwoo_Cache');
WritChecker('cache' . $ds . 'Dwoo_Compile');
//WritChecker('cache' . $ds . 'log' . $ds);
//WritChecker('cache' . $ds . 'log' . $ds . 'empa_error.log');
//WritChecker('cache' . $ds . 'log' . $ds . 'empa_notice.log');
//WritChecker('cache' . $ds . 'log' . $ds . 'empa_setup_error.log');
//WritChecker('cache' . $ds . 'log' . $ds . 'empa_warning.log');
WritChecker('cache' . $ds . 'yapeal' . $ds);
WritChecker('cache' . $ds . 'yapeal' . $ds . 'account' . $ds);
WritChecker('cache' . $ds . 'yapeal' . $ds . 'char' . $ds);
WritChecker('cache' . $ds . 'yapeal' . $ds . 'corp' . $ds);
WritChecker('cache' . $ds . 'yapeal' . $ds . 'eve' . $ds);
WritChecker('cache' . $ds . 'yapeal' . $ds . 'log' . $ds);
WritChecker('cache' . $ds . 'yapeal' . $ds . 'log' . $ds . 'yapeal_error.log');
WritChecker('cache' . $ds . 'yapeal' . $ds . 'log' . $ds . 'yapeal_notice.log');
WritChecker('cache' . $ds . 'yapeal' . $ds . 'log' . $ds . 'yapeal_trace.log');
WritChecker('cache' . $ds . 'yapeal' . $ds . 'log' . $ds . 'yapeal_warning.log');
WritChecker('cache' . $ds . 'yapeal' . $ds . 'map' . $ds);
WritChecker('cache' . $ds . 'yapeal' . $ds . 'server' . $ds);
WritChecker('config' . $ds);
unset($write);
/*
 * check if there was an extension or write premission that was not set corect
 */
$data->assign('error', $error);
$data->assign('chmodcheck', $chmodcheck);
$data->assign('link', $_SERVER['SCRIPT_NAME']);
setupHeader('Requirement Check');
$dwoo->output($tpl, $data);
unset($tpl, $data);
setupFooter();
?>
