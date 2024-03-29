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
 * killDetail
 * 
 * @package KarambitKS
 * @author Stephen Gulick
 * @copyright 2009
 * @version $Id$
 * @access public
 */
class killDetail
{   
    /**
     * 
     * @var object
     */
    private $rs_detail;
    
    /**
     * 
     * @var array
     */
    public $rarray_detail;
    
    /**
     * 
     * @var object
     */
    private $rs_attackers;
    
    /**
     * 
     * @var array
     */
    public $rarray_attackers;

    /**
     * killList::fetchAttackers()
     * 
     * Use this to fetch detail on a kill
     * 
     * @param mixed $ID
     * @return void
     */
    function fetchAttackers($killID) {
            //Get ADODB Factory INSTANCE
            $instance = ADOdbFactory::getInstance();
            //Get DB Connection
            $con = $instance->factory(KKS_DSN);
            
            
            $sql = 'SELECT `killID`, ca.`allianceID`, `allianceName`, `characterID`, `characterName`, `corporationID`, `corporationName`, `factionID`, `factionName`, `damageDone`, `finalBlow`, st.typeName AS shipType, wt.typeName AS weaponType, al.icon FROM `'.PREFIX_YAPEAL.'corpAttackers` ca'
        . ' JOIN '.PREFIX_EVE.'invTypes st ON st.typeID=ca.`shipTypeID`'
        . ' JOIN '.PREFIX_EVE.'invTypes wt ON wt.typeID=ca.`weaponTypeID`'
        . ' LEFT JOIN '.PREFIX_KKS.'allianceLogo al ON al.allianceID=ca.allianceID'
        . ' WHERE ca.`killID`='.$killID; 
        

            if($this->rs_attackers=$con->CacheExecute(KKS_CACHE_KILLLIST, $sql)){
            	//$this->rarray_attackers=$this->rs_attackers->GetArray();
            	
            	$a=0;
            	while (!$this->rs_attackers->EOF) {
        	 	
        	 	$temp[$a]['killID'] = $this->rs_attackers->fields['killID'];
        	 	$temp[$a]['allianceID'] = $this->rs_attackers->fields['allianceID'];
        	 	$temp[$a]['icon']=$this->rs_attackers->fields['icon'];
        	 	$temp[$a]['allianceName'] = $this->rs_attackers->fields['allianceName'];
        	 	$temp[$a]['characterID'] = $this->rs_attackers->fields['characterID'];
        	 	$temp[$a]['characterName'] = $this->rs_attackers->fields['characterName'];
        	 	$temp[$a]['corporationID'] = $this->rs_attackers->fields['corporationID'];
        	 	$temp[$a]['corporationName'] = $this->rs_attackers->fields['corporationName'];
        	 	$temp[$a]['factionID'] = $this->rs_attackers->fields['factionID'];
        	 	$temp[$a]['factionName'] = $this->rs_attackers->fields['factionName'];
        	 	$temp[$a]['damageDone'] = $this->rs_attackers->fields['damageDone'];
        	 	$temp[$a]['finalBlow'] = $this->rs_attackers->fields['finalBlow'];
        	 	$temp[$a]['shipType'] = $this->rs_attackers->fields['shipType'];
        	 	$temp[$a]['weaponType'] = $this->rs_attackers->fields['weaponType'];

         		$this->rs_attackers->MoveNext();
				$a++;
				}            	
            	
            	$this->rarray_attackers=$temp;
            	
            } else {
            	trigger_error('SQL Query Failed', E_USER_ERROR);
            }
            
    }
    
    function fetchItems($killID) {
            //Get ADODB Factory INSTANCE
            $instance = ADOdbFactory::getInstance();
            //Get DB Connection
            $con = $instance->factory(KKS_DSN);
            
            
            $sql = 'SELECT im.`killID`, it.`typeID`, eg.`icon`, it.`typeName`, im.`qtyDropped`, im.`qtyDestroyed` FROM `'.PREFIX_YAPEAL.'corpItems` im'
        . ' JOIN '.PREFIX_EVE.'invTypes it ON it.`typeID`=im.`typeID`'
        . ' JOIN '.PREFIX_EVE.'eveGraphics eg ON it.`graphicID`=eg.`graphicId`'
        . ' WHERE im.`killID`='.$killID.' AND im.`lft`!=1 LIMIT 0, 100 '; 

            if($this->rs_items=$con->CacheExecute(KKS_CACHE_KILLLIST, $sql)){
            	
            	$a=0;
            	while (!$this->rs_items->EOF) {
        	 	
        	 	$temp[$a]['killID'] = $this->rs_items->fields['killID'];
        	 	$temp[$a]['icon'] = $this->rs_items->fields['icon'];
        	 	$temp[$a]['typeID'] = $this->rs_items->fields['typeID'];
                $temp[$a]['typeName'] = $this->rs_items->fields['typeName'];
                $temp[$a]['qtyDropped'] = $this->rs_items->fields['qtyDropped'];
                $temp[$a]['qtyDestroyed'] = $this->rs_items->fields['qtyDestroyed'];

         		$this->rs_items->MoveNext();
				$a++;
				}            	
            	
            	return $this->rarray_items=$temp;
            	
            } else {
            	trigger_error('SQL Query Failed', E_USER_ERROR);
            }
            
    }
    
    /**
     * killList::fetchDetail()
     * 
     * Use this to fetch detail on a kill // process results and load into dwoo data
     * 
     * @param mixed $ID
     * @param object data
     * @return object data
     */
    function fetchDetail($killID, $data) {
            //Get ADODB Factory INSTANCE
            $instance = ADOdbFactory::getInstance();
            //Get DB Connection
            $con = $instance->factory(KKS_DSN);
            
            
            $sql = 'SELECT kl.*, cv.*, map.*, types.typeName as shipTypeName, al.icon FROM `'.PREFIX_YAPEAL.'corpKillLog` kl'
        . ' JOIN '.PREFIX_YAPEAL.'corpVictim cv ON cv.`killID`=kl.`killID`'
        . ' JOIN '.PREFIX_EVE.'mapSolarSystems map ON kl.`solarSystemID` = map.solarSystemID'
        . ' JOIN '.PREFIX_EVE.'invTypes types ON types.typeID=cv.shipTypeID'
        . ' LEFT JOIN '.PREFIX_KKS.'allianceLogo al ON al.allianceID=cv.allianceID'
        . ' WHERE kl.`killID`='.$killID.' LIMIT 1 '; 
        
        

            if($this->rs_detail=$con->CacheExecute(KKS_CACHE_KILLLIST, $sql)){
            	$this->rarray_detail=$this->rs_detail->GetAssoc();
            	foreach($this->rarray_detail[$killID] as $key => $data2){
				    if(!is_numeric($key)){
				    	$data->assign($key, $data2);				    	
				    }
				}
				return $data;
            } else {
            	trigger_error('SQL Query Failed', E_USER_ERROR);
            } 
            
    }
}

?>