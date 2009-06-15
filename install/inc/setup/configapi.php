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
};
/*
* Set some values
*/
$prefix = $_REQUEST['Prefix'];
/*
* Set Dwoo Template and Data.
*/
$tpl = new Dwoo_Template_File(SETUP_TEMPLATE_DIR . 'configapi.tpl');
$data = new Dwoo_Data();
/*
* Set some values
*/
if (isset($_REQUEST['getlist'])) {
  $apiuserid = $_REQUEST['api_user_id'];
  $apikey = $_REQUEST['api_key'];
  $loadcharsel = true;
} else {
  $apiuserid = '';
  $apikey = '';
  $loadcharsel = false;
};// if isset $_REQUEST['getlist']
/*
* Assign Some Data.
*/
$data->assign('link', $_SERVER['SCRIPT_NAME'] . '?funk=configapi');
$exceptions = array('getlist', 'api_user_id', 'api_key');
$data->assign('inputHiddenPost', inputHiddenPost($exceptions));
$data->assign('apiuserid', $apiuserid);
$data->assign('apikey', $apikey);
/*
* Create site
*/
setupHeader('Corporation Select');
$dwoo->output($tpl, $data);
unset($tpl, $data);
if ($loadcharsel) {
  require_once (EMPA_INSTALL . 'inc' . $ds . 'setup' . $ds . 'char_select.php');
};
setupFooter();
?>
