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
 * @version    SVN: $Id$
 * @link       http://code.google.com/p/karambitks/
 * @link       http://www.eve-online.com/
 */

//Get Needed Classes
require_once KKS_CLASS.'class.killlist.php';
require_once KKS_CLASS.'class.shipclassstats.php';

//Check to see if Week and Year are set
if(isset($_GET['w']) && is_numeric($_GET['w'])) {
    (int) $week=$_GET['w'];
}
else {
    $week='';
    echo "WEEK :".$_GET['w']."<br><br>\n";
}
if(isset($_GET['y']) && is_numeric($_GET['y'])) {
    (int) $year=$_GET['y'];
}
else {
    $year='';
}
//New KillList
$kl = New killList();
//New Statslist
$sc= New shipClassStats();

//Get the list
$kl->fetchList(KKS_KBCORPID, false, $week, $year);
$sc->fetchShipLostList(KKS_KBCORPID, false, $week, $year);
$sc->fetchShipKIllList(KKS_KBCORPID, false, $week, $year);
//Get the results
$list=$kl->rarray;
$list_scloss=$sc->rarray_scloss;
$list_sckill=$sc->rarray_sckill;

//Dump
echo"Losses: <pre>";print_r($list_scloss);echo"</pre><br>\n";
echo"Kills: <pre>";print_r($list_sckill);echo"</pre><br>\n";
?>