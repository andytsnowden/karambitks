<?php

/**
 * Karambit Killboard System
 *
 * Builds and checks the path constants.
 *
 * PHP version 5
 *
 * LICENSE: This file is part of EVE MultiPurpose Application also know as KarambitKS.
 * KarambitKS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3 of the License.
 *
 * KarambitKS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with KarambitKS.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author     Michael Cummings <mgcummings@yahoo.com>
 * @author     Stephen Gulickk <stephenmg12@gmail.com>
 * @author     Andy Snowden <forumadmin@eve-razor.com>
 * @copyright  2009(C) Michael Cummings, Stephen Gulick, and Andy Snowden 
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @package    KarambitKS
 * @version    SVN: $Id$
 * @link       http://code.google.com/p/karambitks/
 * @link       http://www.eve-online.com/
 */
/**
 * @internal Only let this code be included or required not ran directly.
 */
if (basename(__file__) == basename($_SERVER['PHP_SELF']))
{
    exit();
}
;
// Used to over come path issues caused by how script is ran on server.
$incDir = realpath(dirname(__file__));
$ds = DIRECTORY_SEPARATOR;
/**
 * Since this file has to be in the 'inc' directory we can set that path now and
 * all the other paths are relative to it.
 *
 */
define('KKS_INC', $incDir . $ds);
/* **************************************************************************
* Paths
* **************************************************************************/
$settings = array('ADODB' => '../ext/adodb5', 'DWOO'=> '../ext/dwoo', 'BASE' => '../', 'CACHE' =>
    '../cache/', 'CLASS' => '../class/', 'CONFIG' => '../config/', 'STYLE' => '../style/', 'INSTALL' =>'../install', 'EXT' =>'../ext/', 'VIEW' => '../view');
foreach ($settings as $k => $v)
{
    $realpath = realpath($incDir . $ds . $v);
    if ($realpath && is_dir($realpath))
    {
        define('KKS_' . $k, $realpath . $ds);
    } else
    {
        $mess = 'Nonexistent directory defined for KARAMBITKS_' . $k . ' constant';
        trigger_error($mess, E_USER_ERROR);
    }
    ;
}
; // foreach $settings ...


?>
