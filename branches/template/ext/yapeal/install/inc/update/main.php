<?php
/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2: */

/**
 * Yapeal Setup - Config page.
 *
 *
 * PHP version 5
 *
 * LICENSE: This file is part of Yet Another Php Eve Api library also know as Yapeal.
 *  Yapeal is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Lesser General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Yapeal is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Lesser General Public License for more details.
 *
 *  You should have received a copy of the GNU Lesser General Public License
 *  along with Yapeal. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Claus Pedersen <satissis@gmail.com>
 * @author Michael Cummings <mgcummings@yahoo.com>
 * @copyright Copyright (c) 2008-2009, Claus Pedersen, Michael Cummings
 * @license http://www.gnu.org/copyleft/lesser.html GNU LGPL
 * @package Yapeal
 */

/**
 * @internal Only let this code be included or required not ran directly.
 */
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  exit();
}
// Get config info
$ini_yapeal = parse_ini_file('..'.$DS.'config'.$DS.'yapeal.ini', true);
if (conRev($ini_yapeal['version'])<=471) {
  require('inc'.$DS.'update'.$DS.'update.php');
  exit;
};// if (conRev($ini_yapeal['version'])<=471)
$db = new mysqli($ini_yapeal['Database']['host'],$ini_yapeal['Database']['username'],$ini_yapeal['Database']['password']);
$query = "SELECT * FROM `".$ini_yapeal['Database']['database']."`.`".$ini_yapeal['Database']['table_prefix']."utilconfig`";
$result = $db->query($query);
while ($row = $result->fetch_assoc()) {
  $conf[$row['Name']] = $row['Value'];
}
$result->close();
$db->close();
// Get login info
require_once('inc'.$DS.'update'.$DS.'login.php');
if (isset($_GET['edit']) && $_GET['edit'] == "setup") {
  // Main edit site
  require_once('inc'.$DS.'update'.$DS.'config.php');
} elseif (isset($_GET['edit']) && $_GET['edit'] == "select") {
  // Main edit site
  require_once('inc'.$DS.'update'.$DS.'char_select.php');
} elseif (isset($_GET['edit']) && $_GET['edit'] == "go") {
  // Main edit site
  require_once('inc'.$DS.'update'.$DS.'go.php');
} else {
  header("Location: ".$_SERVER['SCRIPT_NAME']."?lang=".GetBrowserLang()."&edit=setup");
};
?>
