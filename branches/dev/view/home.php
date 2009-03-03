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
require_once KKS_CLASS.'class.navigation.php';

//Turn on Dev mode
define('KKS_DEV_MODE', 'Just needs to be defined');

//Check to see if Week and Year are set
if(isset($_GET['w']) && is_numeric($_GET['w'])) {
    (int) $week=$_GET['w'];
}
else {
    $week='';
    $date=date_parse(strtotime("now"));
}
if(isset($_GET['y']) && is_numeric($_GET['y'])) {
    (int) $year=$_GET['y'];
    $y=$year;
}
else {
    $year='';
    $date=date_parse(strtotime("now"));
    $y=$date['year'];
}
//Generate Cache ID
$cacheID='home_id'.KKS_KBCORPID.'a0w'.$week.'y'.$year;
//Generate Template
$template = TemplateHandler::createTemplate('home', KKS_CACHE_DWOO, $cacheID);

//Check to see if template was cached
if(TemplateHandler::getInstance()->isCached($template))
{
    // we assign a blank array as the data
    $data = array();
} else {
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
$table=$sc->fetchShipClassTableArray(KKS_KBCORPID, false, $week, $year);
$sc->fetchShipClassTableArray();
//assign Data to Dwoo
$data = new Dwoo_Data();

//menu
$nav = new Navigation();
$menu = $nav->generateMenu();
$w = floor(100 / count($menu));
$data->assign('menu_w',$w.'%');
$data->assign('menu', $menu);

//template data
$data->assign('kb_title', $kb_title);
$data->assign('style_url', $style_url);
$data->assign('banner_link', $_SERVER['SCRIPT_URI']);
$data->assign('banner', $banner);
$data->assign('theme_url', $theme_url);

//echo"<pre>";print_r($list);echo"</pre><br>";

$sckill = array('table' => $table);
$recent = array('recent' => $list);
$data->assign($recent, 'recent');
$data->assign($sckill, 'table');

//execution time
$data->assign('gen', round(array_sum(explode(' ', microtime())) - array_sum(explode(' ', $time_start)), 3));
}

//debug_var('sckill', print_r($sckill, true));
echo TemplateHandler::fetch($template, $data);
?>