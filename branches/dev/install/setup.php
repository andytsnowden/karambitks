<?php
/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2: */
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
/*
 * make a short value for Directory Separators
 */
$ds = DIRECTORY_SEPARATOR;
// Require the common_paths.inc
/** @ignore */
$baseDir = realpath(dirname(__FILE__));
require_once ($baseDir . $ds . '..' . $ds . 'inc' . $ds . 'common_paths.php');
/*
 * Check if empa.ini and yapeal.ini is created.
 * If they exists, then goto main page.
 */
if (file_exists(KKS_CONFIG . 'empa.ini')) {
  $path = str_replace('install/setup.php', '', $_SERVER['SCRIPT_NAME']);
  header('Location: http://' . $_SERVER['HTTP_HOST'] . $path);
};
/** @ignore */
// Require the common_backend.php
require_once (KKS_INSTALL . 'inc' . $ds . 'common_backend.php');
// Require the function file
/** @ignore */
require_once (KKS_INSTALL . 'inc' . $ds . 'function.php');
// Require the value file
/** @ignore */
require_once (EMPA_INSTALL . 'inc' . $ds . 'values.php');
echo "test";
// Check if the browser is IGB (Ingame Browser)
if (isIGB()) {
  // Generate IGB error site
  setupHeader('No IGB Support');
  $tpl = new Dwoo_Template_File(SETUP_TEMPLATE_DIR . 'noigb.tpl');
  $data = new Dwoo_Data();
  $data->assign('link', 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']);
  /*
   * Output dwoo result
   */
  $dwoo->output($tpl, $data);
  unset($tpl, $data);
  setupFooter();
  // if not the Ingame Browser
} else {
  // Get Page Switcher
  /** @ignore */
  require_once (KKS_INSTALL . 'inc' . $ds . 'setup' . $ds . 'main.php');
};
if (isset($con) && $con->IsConnected()) {
  $con->Close();
};
?>
