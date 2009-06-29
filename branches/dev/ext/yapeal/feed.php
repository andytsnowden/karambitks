#!/usr/bin/php
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
/* **************************************************************************
* THESE SETTINGS MAY NEED TO BE CHANGED WHEN PORTING TO NEW SERVER.
* **************************************************************************/
// Move down to 'inc' directory to read common_backend.inc
$dir = realpath(dirname(__FILE__));
chdir($dir);
$ds = DIRECTORY_SEPARATOR;
$path = $dir . $ds . 'inc' . $ds . 'common_backend.php';
echo $path.PHP_EOL;
require_once $path;

/* **************************************************************************
* NOTHING BELOW THIS POINT SHOULD NEED TO BE CHANGED WHEN PORTING TO NEW
* SERVER. YOU SHOULD ONLY NEED TO CHANGE SETTINGS IN INI FILE.
* **************************************************************************/
require_once YAPEAL_INC . 'common_db.php';
require_once YAPEAL_INC . 'killboard_db.php';
require_once YAPEAL_CLASS . 'YapealFeedRequests.class.php';

require_once YAPEAL_CLASS . 'api' . $ds . 'AFeed.php';
require_once YAPEAL_CLASS . 'api' . $ds . 'feedKillLog.php';

$feeds=getFeeds();

foreach($feeds as $feed) {
    echo "FEED LOOP".PHP_EOL;
    if($feed['unixCachedUntil']<=(time()-360)) {
      $params = array('feedUrl' => $feed['feedUrl'], 'feedKey' => $feed['feedKey'],
        'ID' => $feed['ID'], 'idType' => $feed['idType'], 'serverName' => 'kksfeed'
      );
      print_r($params);echo PHP_EOL;
      $feed = new feedKillLog($params);
      $feed->apiFetch();
      $feed->apiStore();
      $feed = null;
    }
}
    
?>