<?php
/**
 * EVE MultiPurpose Application
 *
 * EMPA Setup.
 *
 * PHP version 5
 *
 * LICENSE: This file is part of EVE MultiPurpose Application also know as EMPA.
 * EMPA is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3 of the License.
 *
 * EMPA is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with EMPA.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author     Ben Cole <wengole@gmail.com>
 * @author     Claus Pedersen <satissis@gmail.com>
 * @author     Michael Cummings <dragonrun1@gmail.com>
 * @author     Stephen Gulickk <stephenmg12@gmail.com>
 * @copyright  2008-2009 (C) Ben Cole, Claus Pedersen, Stephen Gulick, and Michael Cummings
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @package    EMPA
 * @subpackage EMPASetup
 * @version    SVN: $Id$ (Only to be used in key files)
 * @link       http://code.google.com/p/empa/
 * @link       http://www.eve-online.com/
 */
/**
 * @internal Only let this code be included or required not ran directly.
 */
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  exit();
};
/*
* Set some values
*/
$stop = 0;
$errortext = '';
/*
* Set Dwoo Template and Data.
*/
$tpl = new Dwoo_Template_File(SETUP_TEMPLATE_DIR . 'charsel.tpl');
$data = new Dwoo_Data();
/*
* Assign Some Data.
*/
$data->assign('link', $_SERVER['SCRIPT_NAME'] . '?funk=doapi');
$exceptions = array('getlist');
$data->assign('inputHiddenPost', inputHiddenPost($exceptions));
/**
 * Check if api info is set
 */
if ($apiuserid != "" && $apikey != "") {
  /**
   * Build cURL query
   */
  $params = array();
  $params['userID'] = $apiuserid;
  $params['apiKey'] = $apikey;
  /**
   * Get xml
   */
  $xml = getApiInfo($params, '/account/Characters.xml.aspx');
  /**
   * Check if $xml is false and output server is down if it is.
   */
  if ($xml) {
    /**
     * Handle xml data
     */
    if (isset($xml->error)) {
      /*
      * Show XML error is there is one
      */
      $stop++;
      $errortext = 'Error: ' . $xml->error;
    } else {
      /**
       * Generate character list
       */
      $charinfo = array();
      foreach($xml->result->rowset->row as $row) {
        $charinfo[] = array(
          'charName' => $row['name'],
          'charId' => $row['characterID'],
          'corpName' => $row['corporationName'],
          'corpId' => $row['corporationID']
        );
      };
      $data->assign('charinfo', $charinfo);
    };
  } else {
    /**
     * Api server is offline
     */
    $stop++;
    $errortext = 'EVE API Server if Offline. Please try later.';
  };
} else {
  /**
   * Api info not set
   */
  $stop++;
  $errortext = 'You must provide API Info';
};
/*
* Output Result
*/
$data->assign('stop', $stop);
$data->assign('errortext', $errortext);
$exceptions = array('getlist');
$data->assign('inputHiddenPost', inputHiddenPost($exceptions));
$dwoo->output($tpl, $data);
unset($tpl, $data);
?>
