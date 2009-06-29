<?php
/**
 * Contents abstract class for corp section.
 *
 * PHP version 5
 *
 * LICENSE: This file is part of Yet Another Php Eve Api library also know
 * as Yapeal which will be used to refer to it in the rest of this license.
 *
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
 * @author Michael Cummings <mgcummings@yahoo.com>
 * @copyright Copyright (c) 2008-2009, Michael Cummings
 * @license http://www.gnu.org/copyleft/lesser.html GNU LGPL
 * @package Yapeal
 */
/**
 * @internal Only let this code be included or required not ran directly.
 */
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  exit();
};
require_once YAPEAL_CLASS . 'IFetchApiTable.php';
require_once YAPEAL_CLASS . 'IStoreApiTable.php';
/**
 * Abstract class for Corporation APIs.
 *
 * @package Yapeal
 * @subpackage Api_corporation
 */
abstract class AFeed implements IFetchApiTable, IStoreApiTable {
  /**
   * @var string feedUrl for this feed.
   */
  protected $feedUrl;
  /**
   * @var string feedKey for this feed.
   */
  protected $feedKey;
  /**
   * @var int ID for this feed.
   */
  protected $ID;
    /**
   * @var string idType for this feed.
   */
  protected $idType;
  /**
   * @var string Name of feed server.
   */  
  protected $serverName;
  /**
   * @var string DB table prefix.
   */
  protected $tablePrefix;
 /**
   * @var SimpleXMLElement Hold the XML return from API.
   */
  protected $xml;
  /**
   * Constructor
   *
   * @param array $params Holds the required parameters like userID, apiKey,
   * etc as needed.
   *
   * @return object Returns the instance of the class.
   *
   * @throws LengthException for any missing required $params.
   */
  public function __construct(array $params = array()) {
    echo "Constructor".PHP_EOL;
    $this->tablePrefix = YAPEAL_TABLE_PREFIX . 'corp';
    if (isset($params['feedUrl']) && is_string($params['feedUrl'])) {
      $this->feedUrl = $params['feedUrl'];
    } else {
        print_r($params);echo PHP_EOL;
      $mess = 'Missing required parameter $params["feedUrl"] to constructor';
      $mess .= ' for ' . $this->api . ' in ' . __FILE__;
      throw new LengthException($mess, 1);
    };// else isset $params['feedUrl'] ...
    if (isset($params['ID']) && is_numeric($params['ID'])) {
      $this->ID = $params['ID'];
    } else {
      $mess = 'Missing required parameter $params["ID"] to constructor';
      $mess .= ' for ' . $this->api . ' in ' . __FILE__;
      throw new LengthException($mess, 2);
    };// else isset $params['characterID'] ...
    if (isset($params['idType']) && is_string($params['idType'])) {
      $this->idType = $params['idType'];
    } else {
      $mess = 'Missing required parameter $params["idType"] to constructor';
      $mess .= ' for ' . $this->api . ' in ' . __FILE__;
      throw new LengthException($mess, 3);
    };// else isset $params['corporationID'] ...
    if (isset($params['serverName']) && is_string($params['serverName'])) {
      $this->serverName = $params['serverName'];
    } else {
      $mess = 'Missing required parameter $params["serverName"] to constructor';
      $mess .= ' for ' . $this->api . ' in ' . __FILE__;
      throw new LengthException($mess, 4);
    };// else isset $params['serverName'] ...
    if (isset($params['feedKey']) && is_string($params['feedKey'])) {
      $this->feedKey = $params['feedKey'];
    }
  }// function __construct
  /**
   * Used to get an item from Eve API.
   *
   * @return boolean Returns TRUE if item received.
   */
  public function apiFetch() {
    global $tracing;
    global $cachetypes;
    //$postdata = array('id'=> $this->ID, 'type'=> $this->idType, 'feedKey'=> $this->feedKey);
    echo "POSTDATA IN apiFetch()";print_r($postdata);echo PHP_EOL;
    $tableName = $this->tablePrefix . $this->api;
    $xml = FALSE;
    try {
      // Build base part of cache file name.
      $cacheName = $this->serverName . $tableName;
      $cacheName .= $this->ID . '.xml';
      // Try to get XML from local cache first if we can.
      $mess = 'getCachedXml for ' . $cacheName;
      $mess .= ' in ' . __FILE__;
      $tracing->activeTrace(YAPEAL_TRACE_CORP, 2) &&
      $tracing->logTrace(YAPEAL_TRACE_CORP, $mess);
      $xml = YapealFeedRequests::getCachedXml($cacheName, YAPEAL_API_CORP);
      if ($xml === FALSE) {
        $mess = 'getAPIinfo for ' . $this->api;
        $mess .= ' in ' . __FILE__;
        $tracing->activeTrace(YAPEAL_TRACE_CORP, 2) &&
        $tracing->logTrace(YAPEAL_TRACE_CORP, $mess);
        $xml = YapealFeedRequests::getAPIinfo($this->feedUrl, $postdata);
        if ($xml instanceof SimpleXMLElement) {
          // Store XML in local cache.
          YapealFeedRequests::cacheXml($xml->asXML(), $cacheName,
            YAPEAL_API_CORP);
        };// if $xml === FALSE ...
      };// if empty $xml ...
      if ($xml !== FALSE) {
        $this->xml = $xml;
        return TRUE;
      } else {
        $mess = 'No XML found for ' . $tableName;
        $mess .= ' in ' . __FILE__;
        trigger_error($mess, E_USER_NOTICE);
        return FALSE;
      };
    }
    catch (YapealApiErrorException $e) {
      // Some error codes give us a new time to retry after that should be
      // used for cached until time.
      switch ($e->getCode()) {
        case 101: // Wallet exhausted.
        case 103: // Already returned one week of data.
        case 115: // Assets already downloaded.
        case 116: // Industry jobs already downloaded.
        case 117: // Market orders already downloaded.
        case 119: // Kills exhausted.
          $cuntil = substr($e->getMessage() , -21, 20);
          $data = array( 'tableName' => $tableName,
            'ownerID' => $this->corporationID, 'cachedUntil' => $cuntil
          );
          upsert($data, $cachetypes, YAPEAL_TABLE_PREFIX . 'utilCachedUntil',
            YAPEAL_DSN);
          break;
        case 211: // Login denied by account status.
          // The corporation's account isn't active no use trying any of the other APIs.
          break 2;// switch, foreach $apis
        default:
          // Do nothing but logging by default
      };// switch $e->getCode()
      return FALSE;
    }
    catch (YapealApiException $e) {
      return FALSE;
    }
    catch (ADODB_Exception $e) {
      return FALSE;
    }
  }// function apiFetch
}
?>
