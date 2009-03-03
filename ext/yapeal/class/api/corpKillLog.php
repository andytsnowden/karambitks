<?php
/**
 * Class used to fetch and store KillLog API.
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
/**
 * Class used to fetch and store KillLog API.
 *
 * @package Yapeal
 * @subpackage Api_corporation
 */
class corpKillLog  extends ACorporation {
  /**
   * @var string Holds the name of the API.
   */
  protected $api = 'KillLog';
  /**
   * Used to store XML to killLog tables.
   *
   * @return boolean Returns TRUE if item was saved to database.
   */
  public function apiStore() {
    global $tracing;
    global $cachetypes;
    $ret = 0;
    $tableName = $this->tablePrefix . $this->api;
    if ($this->xml instanceof SimpleXMLElement) {
      // Solves a problem with clone not working right for simpleXML again.
      $xml = simplexml_load_string($this->xml->asXML());
      $this->editAttribute($xml);
      // Solves problems with simpleXML write not always showing up in later
      // operations.
      $this->xml = simplexml_load_string($xml->asXML());
      unset($xml);
      $junk = $tableName . $this->corporationID . '.xml';
      YapealApiRequests::cacheXml($this->xml->asXML(), $junk, YAPEAL_API_CORP);
      if ($this->attackers()) {
        ++$ret;
      };
      //if ($this->items()) {
      //  ++$ret;
      //};
      if ($this->killLog()) {
        ++$ret;
      };
      if ($this->victim()) {
        ++$ret;
      };
      try {
        // Update CachedUntil time since we should have a new one.
        $cuntil = (string)$this->xml->cachedUntil[0];
        $data = array( 'tableName' => $tableName,
          'ownerID' => $this->characterID, 'cachedUntil' => $cuntil
        );
        $mess = 'Upsert for '. $tableName;
        $mess .= ' from char section in ' . __FILE__;
        $tracing->activeTrace(YAPEAL_TRACE_CACHE, 0) &&
        $tracing->logTrace(YAPEAL_TRACE_CACHE, $mess);
        upsert($data, $cachetypes, YAPEAL_TABLE_PREFIX . 'utilCachedUntil',
          YAPEAL_DSN);
      }
      catch (ADODB_Exception $e) {
        // Already logged nothing to do here.
      }
    };// if $this->xml ...
    if ($ret == 4) {
      return TRUE;
    } else {
      return FALSE;
    };
  }// function apiStore()
  /**
   * Used to store XML to attackers table.
   *
   * @return Bool Return TRUE if store was successful.
   */
  protected function attackers() {
    global $tracing;
    $types = array(
      'allianceID' => 'I', 'allianceName' => 'C', 'characterID' => 'I',
      'characterName' => 'C', 'corporationID' => 'I', 'corporationName' => 'C',
      'damageDone' => 'I', 'factionID' => 'I', 'factionName' => 'C',
      'finalBlow' => 'L', 'killID' => 'I',
      'securityStatus' => 'N', 'shipTypeID' => 'I', 'weaponTypeID' => 'I'
    );
    $ret = FALSE;
    $tableName = $this->tablePrefix . 'Attackers';
    $datum = $this->xml->xpath('//rowset[@name="attackers"]/row');
    if (!empty($datum)) {
      try {
        $mess = 'multipleUpsertAttributes for ' . $tableName;
        $mess .= ' from char section in ' . __FILE__;
        $tracing->activeTrace(YAPEAL_TRACE_CHAR, 1) &&
        $tracing->logTrace(YAPEAL_TRACE_CHAR, $mess);
        multipleUpsertAttributes($datum, $types, $tableName, YAPEAL_DSN);
      }
      catch (ADODB_Exception $e) {
        return FALSE;
      }
      $ret = TRUE;
    } else {
    $mess = 'There was no XML data to store for ' . $tableName;
    $mess .= ' from char section in ' . __FILE__;
    trigger_error($mess, E_USER_NOTICE);
    $ret = FALSE;
    };// else count $datum ...
    return $ret;
  }// function attackers
  /**
   * Used to store XML to Items table.
   *
   * @return Bool Return TRUE if store was successful.
   */
  protected function items() {
    global $tracing;
    $ret = FALSE;
    $tableName = $this->tablePrefix . 'Items';
    // Set the field types of query by name.
    $types = array('flag' => 'I', 'killID' => 'I', 'typeID' => 'I',
      'qtyDestroyed' => 'I', 'qtyDropped' => 'I'
    );
    $datum = $this->xml->xpath('//rowset[@name="items"]//row');
    if (!empty($datum)) {
      try {
        $mess = 'multipleUpsertAttributes for ' . $tableName;
        $mess .= ' from char section in ' . __FILE__;
        $tracing->activeTrace(YAPEAL_TRACE_CHAR, 1) &&
        $tracing->logTrace(YAPEAL_TRACE_CHAR, $mess);
        multipleUpsertAttributes($datum, $types, $tableName, YAPEAL_DSN);
      }
      catch (ADODB_Exception $e) {
        return FALSE;
      }
      $ret = TRUE;
    } else {
    $mess = 'There was no XML data to store for ' . $tableName;
    $mess .= ' from char section in ' . __FILE__;
    trigger_error($mess, E_USER_NOTICE);
    $ret = FALSE;
    };// else count $datum ...
    return $ret;
  }// function items
  /**
   * Used to store XML to KillLog table.
   *
   * @return Bool Return TRUE if store was successful.
   */
  protected function killLog() {
    global $tracing;
    $ret = FALSE;
    $tableName = $this->tablePrefix . 'KillLog';
    // Set the field types of query by name.
    $types = array('killID' => 'I', 'killTime' => 'T', 'moonID' => 'I',
      'solarSystemID' => 'I');
    $xml = simplexml_load_string($this->xml->asXML());
    $datum = $xml->xpath('//rowset[@name="kills"]/row');
    if (!empty($datum)) {
      foreach ($datum as $data) {
        unset($data->victim[0], $data->rowset[1], $data->rowset[0]);
      }
      try {
        $mess = 'multipleUpsertAttributes for ' . $tableName;
        $mess .= ' from char section in ' . __FILE__;
        $tracing->activeTrace(YAPEAL_TRACE_CHAR, 1) &&
        $tracing->logTrace(YAPEAL_TRACE_CHAR, $mess);
        multipleUpsertAttributes($datum, $types, $tableName, YAPEAL_DSN);
      }
      catch (ADODB_Exception $e) {
        return FALSE;
      }
      $ret = TRUE;
    } else {
    $mess = 'There was no XML data to store for ' . $tableName;
    $mess .= ' from char section in ' . __FILE__;
    trigger_error($mess, E_USER_NOTICE);
    $ret = FALSE;
    };// else count $datum ...
    return $ret;
  }// function killLog
  /**
   * Used to store XML to Victim table.
   *
   * @return Bool Return TRUE if store was successful.
   */
  protected function victim() {
    global $tracing;
    $ret = FALSE;
    $tableName = $this->tablePrefix . 'Victim';
    // Set the field types of query by name.
    $types = array(
      'allianceID' => 'I', 'allianceName' => 'C', 'characterID' => 'I',
      'characterName' => 'C', 'corporationID' => 'I', 'corporationName' => 'C',
      'damageTaken' => 'I', 'factionID' => 'I', 'factionName' => 'C',
      'killID' => 'I', 'shipTypeID' => 'I'
    );
    $datum = $this->xml->xpath('//victim');
    if (!empty($datum)) {
      try {
        $mess = 'multipleUpsertAttributes for ' . $tableName;
        $mess .= ' from char section in ' . __FILE__;
        $tracing->activeTrace(YAPEAL_TRACE_CHAR, 1) &&
        $tracing->logTrace(YAPEAL_TRACE_CHAR, $mess);
        multipleUpsertAttributes($datum, $types, $tableName, YAPEAL_DSN);
      }
      catch (ADODB_Exception $e) {
        return FALSE;
      }
      $ret = TRUE;
    } else {
    $mess = 'There was no XML data to store for ' . $tableName;
    $mess .= ' from char section in ' . __FILE__;
    trigger_error($mess, E_USER_NOTICE);
    $ret = FALSE;
    };// else count $datum ...
    return $ret;
  }// function victim
  /**
   * Navigates XML and adds KillID attribute.
   *
   * @param SimpleXMLElement $node Current element from tree.
   * @param integer $attribute killID of kill.
   * Used to propagate information from parents to children that don't include it
   * by default.
   *
   * @return integer Current killID of kill.
   */
  protected function editAttribute($node, $attribute = '') {
    $nodeName = $node->getName();
    if ($nodeName == 'row' || $nodeName == 'victim') {
      if (isset($node['killID'])) {
        $attribute = $node['killID'];
      } else {
        $node->addAttribute('killID', $attribute);
      };// if isset $node['kill']...
    };// if $nodeName=='row'
    if ($children = $node->children()) {
      foreach ($children as $child) {
        $attribute = $this->editAttribute($child, $attribute);
      };// foreach children as child
    };
    return $attribute;
  }// function editAttribute
}
?>
