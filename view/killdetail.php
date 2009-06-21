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
require_once KKS_CLASS.'class.killDetail.php';
require_once KKS_CLASS.'class.shipclassstats.php';
require_once KKS_CLASS.'class.navigation.php';

//Turn on Dev mode
define('KKS_DEV_MODE', 'Just needs to be defined');

//Check to see if killID is set
if(is_numeric($_GET['kid'])) {
    (int) $killID=$_GET['kid'];
} else {
    (int) $killID=0;
    echo"No kill ID Submitted!";exit;
}
//Generate Cache ID
$cacheID='detail_id'.$killID;
//Generate Template
$template = TemplateHandler::createTemplate('killdetail', 21600, $cacheID);

//Check to see if template was cached
if(TemplateHandler::getInstance()->isCached($template))
{
    // we assign a blank array as the data
    $data = array();
} else {

//New KillDetial
$kd = New killDetail();

//GET the kill detail
$kd->fetchAttackers($killID);
$attackers=$kd->rarray_attackers;
$kd->fetchItems($killID);
$items=$kd->rarray_items;

//assign Data to Dwoo
$data = new Dwoo_Data();

//menu
$nav = new kss_navigation();
$menu = $nav->generateMenu();
$w = floor(100 / count($menu));
$data->assign('menu_w',$w.'%');
$data->assign('menu', $menu);

//template data
$data->assign('kb_title', $kb_title);
$data->assign('style_url', $style_url);
$data->assign('banner_link', $_SERVER['PHP_SELF']);
$data->assign('banner', $banner);
$data->assign('theme_url', $theme_url);


//Page data
$attackers = array('attack' => $attackers);
$items = array('items' => $items);
$data->assign($attackers, 'attack');
$data = $kd->fetchDetail($killID, $data);
$data->assign($items, 'items');

//execution time
$data->assign('gen', round(array_sum(explode(' ', microtime())) - array_sum(explode(' ', $time_start)), 3));
}

echo TemplateHandler::fetch($template, $data);
?>