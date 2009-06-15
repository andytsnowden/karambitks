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
 * Link handler
 */
if (isset($_GET['funk'])) {
  $switch = $_GET['funk'];
} else {
  $switch = '';
};
switch ($switch) {
  case 'doapi':
    require_once (SETUP_FILES . 'goapi.php');
    break;
    /*
     * Select a test character
     */
  case 'csel':
    require_once (SETUP_FILES . 'char_select.php');
    break;
    /*
     * Input API info for test character
     */
  case 'configapi':
    require_once (SETUP_FILES . 'configapi.php');
    break;
    /*
     * Create database
     */
  case 'dodb':
    require_once (SETUP_FILES . 'godb.php');
    break;
    /*
     * Input DB info
     */
  case 'configdb':
    require_once (SETUP_FILES . 'configdb.php');
    break;
    /*
     * Show requirement page
     */
  case 'req':
    require_once (SETUP_FILES . 'req.php');
   break;
    /*
     * Show welcome page
     */
  case 'welcome':
    setupHeader('Welcome');
    $tpl = new Dwoo_Template_File(SETUP_TEMPLATE_DIR . 'welcome.tpl');
    $data = new Dwoo_Data();
    $data->assign('link', 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']);
    $dwoo->output($tpl, $data);
    unset($tpl, $data);
    setupFooter();
    break;
    /*
     * Welcome page
     */
  default:
    header('Location: ' . $_SERVER['SCRIPT_NAME'] . '?funk=welcome');
   break;
};// switch
?>
