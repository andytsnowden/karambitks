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
require_once KKS_CLASS.'class.toplist.php';

//Turn on Dev mode
define('KKS_DEV_MODE', 'Just needs to be defined');

//Check to see if Week and Year are set
if(isset($_GET['w']) && is_numeric($_GET['w'])) {
    $week=$_GET['w'];
}
else {
    $week=date('W');
}
if(isset($_GET['y']) && is_numeric($_GET['y'])) {
    $year=$_GET['y'];
    $y=$year;
}
else {
    $year=date('Y');
    $y=$year;

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

//Set needed KillList options
$kl->corpID=KKS_KBCORPID;
$kl->fetchCorp=TRUE;
$kl->week=$week;
$kl->year=$year;


//New Statslist
$sc= New shipClassStats();

//Set needed KillList options
$sc->corpID=KKS_KBCORPID;
$sc->fetchCorp=TRUE;
$sc->week=$week;
$sc->year=$year;

//New Top List
$tl = New toplist();

//Get the list
$kl->fetchList();
$tl->char_corplist(KKS_KBCORPID, 10);
//Get the results
$list=$kl->rarray;
$table=$sc->fetchShipClassTableArray();
$charCorpTop=$tl->ra_char_corp;
//assign Data to Dwoo
$data = new Dwoo_Data();

//menu
$nav = new kss_navigation();
$menu = $nav->generateMenu();
$w = floor(100 / count($menu));
$data->assign('menu_w',$w.'%');
$data->assign('menu', $menu);
//$menu = $nav->addmenu('123','123','123');

//template data
$data->assign('kb_title', $kb_title);
$data->assign('style_url', $style_url);
$data->assign('banner_link', $_SERVER['SCRIPT_URI']);
$data->assign('banner', $banner);
$data->assign('theme_url', $theme_url);


//Page data
$sckill = array('table' => $table);
$recent = array('recent' => $list);
$chartl = array('chartoplist' => $charCorpTop);
$data->assign($recent, 'recent');
$data->assign($sckill, 'table');
$data->assign($chartl, 'chartl');

//execution time
$data->assign('gen', round(array_sum(explode(' ', microtime())) - array_sum(explode(' ', $time_start)), 3));
}

echo TemplateHandler::fetch($template, $data);
?>