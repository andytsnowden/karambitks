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
 * @copyright  2009 (C) Michael Cummings, Stephen Gulick, and Andy Snowden 
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @package    KarambitKS
 * @link       http://code.google.com/p/karambitks/
 * @link       http://www.eve-online.com/
 */

//Database Configuration Options
$host="localhost";
$dbusername="dbusername";
$dbpassword="dbpassword";
$database="dbdatabase";
//$dbtable_prefix="";
$dbdriver="mysqli://";

//Cache Timers (in Seconds unless noted)
$killlist=60;
$stats=900;
$killdetail=1800;
$dwoo_cache=300;

//Your Corporation ID
$corporationID=0;

//Server Compression
$compression=1;

/*******************************************
** DO NOT CHANGE ANYTHING BELOW THIS LINE **
*******************************************/

//build and set the Database Source Name
$dsn = $dbdriver.$dbusername.':'.$dbpassword.'@'.$host.'/'.$database;
define('KKS_DSN', $dsn);

//Set CorporationID constant
define('KKS_KBCORPID', $corporationID);

//Set Cache Timer constants
$cache = array('KILLLIST' => $killlist, 'STATS' => $stats, 'KILLDETAIL' =>
    $killdetail, 'DWOO' => $dwoo_cache);
foreach ($cache as $k => $v)
{
        define('KKS_CACHE_' . $k, $v);
}
; // foreach $cache ...

//DEBUGING PURPOSES ONLY

//Turn ON/OFF ADODB LOGGING
define('KKS_ADODB_LOG', 'FALSE');

//Turn On/Off ADODB Debug
define('KKS_ADODB_DEBUG', 'FALSE');


?>