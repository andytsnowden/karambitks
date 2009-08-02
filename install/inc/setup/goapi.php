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
 * @author     Stephen Gulick <stephenmg12@gmail.com>
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
 * Set some values.
 */
$prefix = $_REQUEST['Prefix'];
$charInfo = $_REQUEST['charInfo'];
$allifact = $_REQUEST['allifact'];
$stop = 0;
$step1 = 0;
$step2 = 0;
/*
 * Set Dwoo Template and Data.
 */
$tpl = new Dwoo_Template_File(SETUP_TEMPLATE_DIR . 'doapiini.tpl');
$data = new Dwoo_Data();
/*
 * Assign Some Data.
 */
$path = str_replace('install/setup.php', '', $_SERVER['SCRIPT_NAME']);
$data->assign('link', 'http://' . $_SERVER['HTTP_HOST'] . $path);
$outputs = array();
$data->assign('outputs', $outputs);
$inis = array();
$data->assign('inis', $inis);
/*
 * Get ADOdb Class to handle database
 */
require_once (KKS_ADODB. 'adodb.inc.php');
/*
 * Check DB connection
 */
$dsn = 'mysql://' . $_REQUEST['DB_Username'] . ':' . $_REQUEST['DB_Password'] . '@' .
  $_REQUEST['DB_Host'] . '/' . $_REQUEST['DB_Database'];
try {
  $con = ADONewConnection($dsn);
}
catch(ADODB_Exception $e) {
  $outputs = array(
    'action' => 'Database: Connect to',
    'info' => $_REQUEST['DB_Host'],
    'status' => $e->getMessage() ,
    'check' => 0
  );
  $data->append('outputs', $outputs);
  unset($con, $outputs);
  $stop++;
  $step2++;
}
/*
* Validate full api key
*/
if ($stop == 0) {
  $params = array();
  $params['userID'] = $_REQUEST['api_user_id'];
  $params['apiKey'] = $_REQUEST['api_key'];
  $params['characterID'] = $charInfo[$_REQUEST['charName']]['charId'];
  $xml = getApiInfo($params, '/char/AccountBalance.xml.aspx');
  if ($xml) {
    /*
     * Handle xml data
     */
    if (isset($xml->error)) {
      /*
       * Show XML error is there is one
       */
      if ($xml->error['code'] == 200) {
        $xmlerrortext = 'Require Full Api Key';
      } else {
        $xmlerrortext = $xml->error;
      };
      $regchecks = array(
        'action' => 'API Info',
        'status' => $xmlerrortext
      );
      $data->append('regchecks', $regchecks);
      unset($regchecks, $xml);
      $stop++;
      $step1++;
    } else {
      unset($xml);
    };
  } else {
    /*
     * Api server is offline
     */
    $regchecks = array(
      'action' => 'API Info',
      'status' => 'EVE API Server if Offline. Please try later.'
    );
    $data->append('regchecks', $regchecks);
    unset($regchecks);
    $stop++;
    $step1++;
  };
}; // if $stop == 0
if ($stop == 0) {
    /*
   * Check Password
   */
  if (!preg_match("^[A-Za-z0-9]{6,24}$", $_REQUEST['regPass'])) {
    $regchecks = array(
      'action' => 'Password',
      'status' => 'Invalid Password'
    );
    $data->append('regchecks', $regchecks);
    unset($regchecks);
    $stop++;
    $step1++;
  } elseif ($_REQUEST['regPass'] !== $_REQUEST['regCheckPass']) {
    $regchecks = array(
      'action' => 'Password',
      'status' => 'Password and password check did not match'
    );
    $data->append('regchecks', $regchecks);
    unset($regchecks);
    $stop++;
    $step1++;
  };// Check Password
};// if $stop == 0
/*
 * If no errors.
 */
if ($stop == 0) {
  if (isset($con) && $con->IsConnected()) {
    $outputs = array(
      'action' => 'Database: Connect to',
      'info' => $_REQUEST['DB_Host'],
      'status' => 'Connected',
      'check' => 1
    );
    $data->append('outputs', $outputs);
    unset($outputs);
    $con->Execute("SET NAMES utf8");
    /*
     * Get corporation sheet and put it in Yapeal
     */
    if ($stop == 0) {
      /*
       * Build cURL query
       */
      $params = array();
      $params['userID'] = $_REQUEST['api_user_id'];
      $params['apiKey'] = $_REQUEST['api_key'];
      $params['characterID'] = $charInfo[$_REQUEST['charName']]['charId'];
      /*
       * Get xml
       */
      $corpxml = getApiInfo($params, '/corp/CorporationSheet.xml.aspx');
      /*
       * Check if $corpxml is false and output server is down if it is.
       */
      if ($corpxml) {
        /*
         * Handle xml data
         */
        if (isset($corpxml->error)) {
          /*
           * Show XML error is there is one
           */
          $outputs = array(
            'action' => 'Database: Put Corporation In',
            'info' => $prefix['yapeal'] . 'corpCorporationSheet',
            'status' => 'Api Error:<br />' . $corpxml->error,
            'check' => 0
          );
          $data->append('outputs', $outputs);
          unset($outputs);
          $stop++;
          $step2++;
          $stop++;
        } else {
          /*
           * Put Corporation In corpCorporationSheet
           */
          $dbdata = array(
            array(
              'allianceID' => $corpxml->result->allianceID,
              'allianceName' => $corpxml->result->allianceName,
              'ceoID' => $corpxml->result->ceoID,
              'ceoName' => $corpxml->result->ceoName,
              'corporationID' => $corpxml->result->corporationID,
              'corporationName' => $corpxml->result->corporationName,
              'description' => $corpxml->result->description,
              'memberCount' => $corpxml->result->memberCount,
              'memberLimit' => $corpxml->result->memberLimit,
              'shares' => $corpxml->result->shares,
              'stationID' => $corpxml->result->stationID,
              'stationName' => $corpxml->result->stationName,
              'taxRate' => $corpxml->result->taxRate,
              'ticker' => $corpxml->result->ticker,
              'url' => $corpxml->result->url
            )
          );
          $types = array('allianceID' => 'I', 'allianceName' => 'C',
            'ceoID' => 'I', 'ceoName' => 'C', 'corporationID' => 'I',
            'corporationName' => 'C', 'description' => 'X',
            'memberCount' => 'I', 'memberLimit' => 'I', 'shares' => 'I',
            'stationID' => 'I', 'stationName' => 'C', 'taxRate' => 'N',
            'ticker' => 'C', 'url' => 'C'
          );
          $query = BuildInsertUpdateQuery($dbdata, $types, $prefix['yapeal'] .
            'corpCorporationSheet');
          try {
            $con->Execute($query);
            $outputs = array(
              'action' => 'Database: Put Corporation In',
              'info' => $prefix['yapeal'] . 'corpCorporationSheet',
              'status' => 'Done',
              'check' => 1
            );
            $data->append('outputs', $outputs);
            unset($outputs);
          }
          catch(ADODB_Exception $e) {
            $outputs = array(
              'action' => 'Database: Put Corporation In',
              'info' => $prefix['yapeal'] . 'corpCorporationSheet',
              'status' => $e->getMessage() ,
              'check' => 0
            );
            $data->append('outputs', $outputs);
            unset($outputs);
            $stop++;
            $step2++;
          }
          if($allifact=='on')
          {
                /**
                 * Set var $allianceID to allianceID from API
                 */

                $allianceID=(integer) $corpxml->result->allianceID;
                /**
                 * Check to see if allianceID is 0 or NULL
                 */
                if(is_numeric($allianceID) && $allianceID!=0) {
                    /**
                     * Set Values for INI (alliance)
                     */
                    $kks['kks']['allianceID']=$allianceID;
                    $kks['kks']['allianceName']=$corpxml->result->allianceName;
                    $kks['kks']['factionID']=0;
                    $kks['kks']['factionName']='';
                    $kks['kks']['corporationID']=0;
                    $kks['kks']['corporationName']='';
                    echo "ALLiance Set".$allianceID."<br>";
                } else {
                    /**
                     * Get Faction Info from API
                     */
                    $factionxml = getApiInfo($params, '	 /corp/FacWarStats.xml.aspx');
                    if($factionxml)
                    {
                            /**
                             * Handel XML Data
                             */
                            if (isset($factionxml->error)) {
                                $outputs = array(
                                'action' => 'Database: Put Character In',
                                'info' => $prefix['yapeal'] . 'charCharacterSheet',
                                'status' => 'Api Error:<br />' . $charxml->error,
                                'check' => 0
                              );
                              $data->append('outputs', $outputs);
                              unset($outputs);
                              $stop++;
                              $step2++;
                              $stop++;
                            } else {
                                /**
                                 * Set INI Vars
                                 */
                                 $kks['kks']['factionID']=(int)$factionxml->result->factionID;
                                 $kks['kks']['factionName']=$factionxml->result->factionName;
                                 $kks['kks']['allianceID']=0;
                                 $kks['kks']['allianceName']='';
                                 $kks['kks']['corporationID']=0;
                                 $kks['kks']['corporationName']='';
                            } //if else is set $factionxml->error
                    }
                    unset($outputs, $corpxml, $factionxml);                       
                }                                                                                                
          } else {
            /**
             * Set CorpID and Name
             */
             $kks['kks']['corporationID']=(int)$corpxml->result->corporationID;
             $kks['kks']['corporationName']=$corpxml->result->corporationName;
             $kks['kks']['allianceID']=0;
             $kks['kks']['allianceName']='';
             $kks['kks']['factionID']=0;
             $kks['kks']['factionName']='';
          }
        };
      } else {
        /*
         * Api server is offline
         */
        $outputs = array(
          'action' => 'Database: Put Corporation In',
          'info' => $prefix['yapeal'] . 'corpCorporationSheet',
          'status' => 'EVE API Server if Offline. Please try later.',
          'check' => 0
        );
        $data->append('outputs', $outputs);
        unset($outputs);
        $stop++;
        $step2++;
      };
    };// if $stop == 0
    /*
     * Get character sheet and put it in Yapeal
     */
    if ($stop == 0) {
      /*
       * Build cURL query
       */
      $params = array();
      $params['userID'] = $_REQUEST['api_user_id'];
      $params['apiKey'] = $_REQUEST['api_key'];
      $params['characterID'] = $charInfo[$_REQUEST['charName']]['charId'];
      /*
       * Get xml
       */
      $charxml = getApiInfo($params, '/char/CharacterSheet.xml.aspx');
      /*
       * Check if $corpxml is false and output server is down if it is.
       */
      if ($charxml) {
        /*
         * Handle xml data
         */
        if (isset($charxml->error)) {
          /*
           * Show XML error is there is one
           */
          $outputs = array(
            'action' => 'Database: Put Character In',
            'info' => $prefix['yapeal'] . 'charCharacterSheet',
            'status' => 'Api Error:<br />' . $charxml->error,
            'check' => 0
          );
          $data->append('outputs', $outputs);
          unset($outputs);
          $stop++;
          $step2++;
          $stop++;
        } else {
          /*
           * Put Character In charCharacterSheet
           */
          $dbdata = array(
            array(
              'balance' => $charxml->result->balance,
              'bloodLine' => $charxml->result->bloodLine,
              'characterID' => $charxml->result->characterID,
              'cloneName' => $charxml->result->cloneName,
              'cloneSkillPoints' => $charxml->result->cloneSkillPoints,
              'corporationID' => $charxml->result->corporationID,
              'corporationName' => $charxml->result->corporationName,
              'gender' => $charxml->result->gender,
              'name' => $charxml->result->name,
              'race' => $charxml->result->race
            )
          );
          $types = array('balance' => 'N', 'bloodLine' => 'C',
            'characterID' => 'I', 'cloneName' => 'C', 'cloneSkillPoints' => 'I',
            'corporationID' => 'I', 'corporationName' => 'C', 'gender' => 'C',
            'name' => 'C', 'race' => 'C'
          );
          $query = BuildInsertUpdateQuery($dbdata, $types, $prefix['yapeal'] .
            'charCharacterSheet');
          try {
            $con->Execute($query);
            $outputs = array(
              'action' => 'Database: Put Character In',
              'info' => $prefix['yapeal'] . 'charCharacterSheet',
              'status' => 'Done',
              'check' => 1
            );
            $data->append('outputs', $outputs);
            unset($outputs, $charxml);
          }
          catch(ADODB_Exception $e) {
            $outputs = array(
              'action' => 'Database: Put Character In',
              'info' => $prefix['yapeal'] . 'charCharacterSheet',
              'status' => $e->getMessage() ,
              'check' => 0
            );
            $data->append('outputs', $outputs);
            unset($outputs, $charxml);
            $stop++;
            $step2++;
          }
        };
      } else {
        /*
         * Api server is offline
         */
        $outputs = array(
          'action' => 'Database: Put Character In',
          'info' => $prefix['yapeal'] . 'charCharacterSheet',
          'status' => 'EVE API Server if Offline. Please try later.',
          'check' => 0
        );
        $data->append('outputs', $outputs);
        unset($outputs);
        $stop++;
        $step2++;
      };
    };// if $stop == 0
    /*
     * Register account in Yapeal Library
     */
    if ($stop == 0) {
      /*
       * Get AccessPerApi
       */
      $query = "SELECT * FROM `" . $prefix['kks'] . "AccessPerApi`";
      $apilist = $con->GetAssoc($query);
      /*
       * Put Character In utilRegisteredCharacter
       */
      $dbdata = array(
        array(
          'activeAPI' => $apilist[2],
          'characterID' => $charInfo[$_REQUEST['charName']]['charId'],
          'corporationID' => $charInfo[$_REQUEST['charName']]['corpId'],
          'corporationName' => $charInfo[$_REQUEST['charName']]['corpName'],
          'isActive' => 0,
          'name' => $_REQUEST['charName'],
          'userID' => $_REQUEST['api_user_id']
        )
      );
      $types = array('activeAPI' => 'X', 'characterID' => 'I',
        'corporationID' => 'I', 'corporationName' => 'C', 'isActive' => 'I',
        'name' => 'C', 'userID' => 'I'
      );
      $query = BuildInsertUpdateQuery($dbdata, $types, $prefix['yapeal'] .
        'utilRegisteredCharacter');
      try {
        $con->Execute($query);
        $outputs = array(
          'action' => 'Database: Register Account In',
          'info' => $prefix['yapeal'] . 'utilRegisteredCharacter',
          'status' => 'Done',
          'check' => 1
        );
        $data->append('outputs', $outputs);
        unset($outputs);
      }
      catch(ADODB_Exception $e) {
        $outputs = array(
          'action' => 'Database: Register Account In',
          'info' => $prefix['yapeal'] . 'utilRegisteredCharacter',
          'status' => $e->getMessage() ,
          'check' => 0
        );
        $data->append('outputs', $outputs);
        unset($outputs, $apilist);
        $stop++;
        $step2++;
      }
      /*
       * Put Corporation In utilRegisteredCorporation
       */
      $dbdata = array(
        array(
          'activeAPI' => 'corpKillLog',
          'characterID' => $charInfo[$_REQUEST['charName']]['charId'],
          'corporationID' => $charInfo[$_REQUEST['charName']]['corpId'],
          'isActive' => 1
        )
      );
      $types = array('activeAPI' => 'X', 'characterID' => 'I',
        'corporationID' => 'I', 'isActive' => 'I'
      );
      $query = BuildInsertUpdateQuery($dbdata, $types, $prefix['yapeal'] .
        'utilRegisteredCorporation');
      try {
        $con->Execute($query);
        $outputs = array(
          'action' => 'Database: Register Account In',
          'info' => $prefix['yapeal'] . 'utilRegisteredCorporation',
          'status' => 'Done',
          'check' => 1
        );
        $data->append('outputs', $outputs);
        unset($outputs);
      }
      catch(ADODB_Exception $e) {
        $outputs = array(
          'action' => 'Database: Register Account In',
          'info' => $prefix['yapeal'] . 'utilRegisteredCorporation',
          'status' => $e->getMessage() ,
          'check' => 0
        );
        $data->append('outputs', $outputs);
        unset($outputs, $apilist);
        $stop++;
        $step2++;
      }
      /*
       * Put Api Info In utilRegisteredUser
       */
      $dbdata = array(
        array(
          'fullApiKey' => $_REQUEST['api_key'],
          'userID' => $_REQUEST['api_user_id']
        )
      );
      $types = array('fullApiKey' => 'C', 'userID' => 'I');
      $query = BuildInsertUpdateQuery($dbdata, $types, $prefix['yapeal'] .
        'utilRegisteredUser');
      try {
        $con->Execute($query);
        $outputs = array(
          'action' => 'Database: Register Account In',
          'info' => $prefix['yapeal'] . 'utilRegisteredUser',
          'status' => 'Done',
          'check' => 1
        );
        $data->append('outputs', $outputs);
        unset($outputs, $apilist);
      }
      catch(ADODB_Exception $e) {
        $outputs = array(
          'action' => 'Database: Register Account In',
          'info' => $prefix['yapeal'] . 'utilRegisteredUser',
          'status' => $e->getMessage() ,
          'check' => 0
        );
        $data->append('outputs', $outputs);
        unset($outputs, $apilist);
        $stop++;
        $step2++;
      }
    }; // if $stop == 0
        /*
     * Register Admin Password in Config
     */
    if ($stop == 0) {
      $salt = substr(md5(uniqid(rand() , true)) , 0, 8);
      $password = sha1($_REQUEST['siteSalt'] .
        $_REQUEST['regPass'] . $salt);
      $query = 'INSERT INTO `' . $prefix['kks'] . 'config`';
      $query .= ' (`config_string`,`config_value`)';
      $query .= ' VALUES (`adminPassword`, '.$password.'`);';
      try {
        $con->Execute($query);
        $outputs = array(
          'action' => 'Database: Register Admin In',
          'info' => $prefix['kks'] . 'config',
          'status' => 'Done',
          'check' => 1
        );
        $data->append('outputs', $outputs);
        unset($outputs);
      }
      catch(ADODB_Exception $e) {
        $outputs = array(
          'action' => 'Database: Register Admin In',
          'info' => $prefix['kks'] . 'config',
          'status' => $e->getMessage() ,
          'check' => 0
        );
        $data->append('outputs', $outputs);
        unset($outputs);
        $stop++;
        $step2++;
      }
    };// if $stop = 0
    /*
     * Configuere EMPA
     */
    /*
     * Greate empa.ini and yapeal.ini file
     */
    if ($stop == 0) {
      require_once (KKS_INSTALL . 'inc' . $ds . 'ini_creator.php');
    };// if $stop = 0
  };// if $con->IsConnected()
};// if $stop == 0
/*
 * Assign the last values to the template
 */
$data->assign('stop', $stop);
$data->assign('step1', $step1);
$data->assign('step2', $step2);
$data->assign('inputHiddenPost', inputHiddenPost());
/*
 * Generate page
 */
setupHeader('Register Account - Create Config Files');
$dwoo->output($tpl, $data);
unset($tpl, $data);
setupFooter();
if (isset($con) && $con->IsConnected()) {
  $con->close();
};
?>
