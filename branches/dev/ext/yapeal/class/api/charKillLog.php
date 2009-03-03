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
 * @subpackage Api_character
 */
class charKillLog  extends ACharacter {
  /**
   * @var string Holds the name of the API.
   */
  protected $api = 'KillLog';
  /**
   * Used to store XML to CharacterSheet tables.
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
      if ($this->attackers()) {
        ++$ret;
      };
      if ($this->items()) {
        ++$ret;
      };
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
    $xml = $this->xml->xpath('//rowset[@name="kills"]/row');
    foreach ($xml as $kill) {
      $datum = $kill->rowset[1];
      if (count($datum) > 0) {
        $lft = 1;
        $rgt = $this->editItems($datum);
        print count($datum) . PHP_EOL;
        print 'Character ' . $this->characterID . PHP_EOL;
        print $rgt . PHP_EOL;
        print_r($datum) . PHP_EOL;
        try {
          $con = connect(YAPEAL_DSN, $tableName);
          $sql = 'delete from ' . $tableName;
          $sql .= ' where killID=' . $killID;
          $mess = 'Before delete for ' . $tableName;
          $mess .= ' from char section in ' . __FILE__;
          $tracing->activeTrace(YAPEAL_TRACE_CHAR, 2) &&
          $tracing->logTrace(YAPEAL_TRACE_CHAR, $mess);
          // Clear out old tree for this owner.
          $con->Execute($sql);
          $mess = 'Before upsert owner node for ' . $tableName;
          $mess .= ' from char section in ' . __FILE__;
          $tracing->activeTrace(YAPEAL_TRACE_CHAR, 2) &&
          $tracing->logTrace(YAPEAL_TRACE_CHAR, $mess);
          // Insert the new owner's root node.
          upsert($nodeData, $this->types, $tableName, YAPEAL_DSN);
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
    };
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
  /**
   * Navigates XML and adds lft and rgt attributes.
   *
   * Navigates XML using SimpleXML and adds lft and rgt attributes of Nested Set
   * for insertion into database.
   *
   * Original idea for function coded by Stephen.
   *
   * @author Stephen <stephenmg12@gmail.com>
   * @author Michael Cummings <mgcummings@yahoo.com>
   *
   * @param SimpleXMLElement $node Current element from tree.
   * @param integer $index Current index for lft/rgt counting.
   * @param integer $level Level of nesting.
   *
   * @return integer Current index for lft/rgt counting.
   *
   * @todo Look at adding a $level based on the rowset/row depth. Would pass it
   * in as param and add increment inside of if ($children = ...).
   * @todo Look at pre-sort the <row>s by flag so items in the same hanger etc
   * are grouped together for lft/rgt.
   */
  protected function editItems($node, $index = 2, $level = -1) {
    $nodeName = $node->getName();
    if ($nodeName == 'row') {
      $node->addAttribute('lft', $index++);
      $node->addAttribute('level', $level);
    };// if $nodeName=='row'...
    if ($nodeName == 'rowset') {
      ++$level;
    };
    if ($children = $node->children()) {
      foreach ($children as $child) {
        $index = $this->editItems($child, $index, $level);
      };// foreach children ...
    };
    if ($nodeName == 'row') {
      $node->addAttribute('rgt', $index++);
    };
    return $index;
  }// function editItems
}
?>
