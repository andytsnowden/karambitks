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

/**
 * Gets a list of feeds from feeds.
 *
 * @return array Returns the list of feeds.
 *
 * @throws ADODB_Exception for any errors.
 */

function getFeeds() {
  global $tracing;
  $con = connect(YAPEAL_DSN, 'Yapeal');
  /* Generate a list of feed(s) to connect to */
  $sql = 'SELECT `feedUrl`, `feedKey`, `cachedUntil`, `ID`, `idType`, UNIX_TIMESTAMP(`cachedUntil`) as unixCachedUntil';
  $sql .= ' FROM `'. YAPEAL_TABLE_PREFIX .'utilFeeds` WHERE `isActive`=1';
  echo $sql.PHP_EOL; 
  $mess = 'Before GetAll feeds in ' . basename(__FILE__);
  $tracing->activeTrace(YAPEAL_TRACE_DATABASE, 2) &&
  $tracing->logTrace(YAPEAL_TRACE_DATABASE, $mess);
  $list = $con->GetAll($sql);
  return $list;
}// function getFeeds

?>