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
 *<?php

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
 * killList
 * 
 * @package KarambitKS
 * @author Stephen Gulick
 * @copyright 2009
 * @version $Id: class.killlist.php 42 2009-03-08 05:41:15Z stephenmg12 $
 * @access public
 */
class toplist extends kks
{
   
    function __construct()
    {
        $this->SQL_start = 'SELECT count(ca.killID) as stats,';
        $this->SQL_table = ' FROM `'.PREFIX_YAPEAL.'corpAttackers` ca';
        $this->SQL_end = ' ORDER BY stats DESC';
    }

    /**
     * killList::fetchList()
     * 
     * Use this to generate a top list
     * 
     * @param mixed $ID
     * @param bool  $isAlliance
     * @param mixed $week
     * @param mixed $year
     * @return void
     */
    function fetchList()
    {
        $this->getTimeFrame();
        
        $con = $this->get_connection();
    
        
        $sql = $this->SQL_start;
        
        if($this->getCharacter) {
            $sql .= ' ca.characterID, ca.characterName';        
        }elseif ($this->getCorporation) {
            $sql .= ' ca.corporationID, ca.corporationName';
        }
        
        $sql .= $this->SQL_table;
        
        if($this->fetchWeek == true) {
            $sql .= ' JOIN `'.PREFIX_YAPEAL.'corpKillLog` kl ON kl.killID=ca.killID';    
        }
                
        if($this->fetchCorp){
            $sql .=' WHERE ca.`corporationID`='.$con->qstr($this->corpID);
        }elseif($this->fetchAlliance) {
            $sql .=' WHERE ca.`allianceID`='.$con->qstr($this->allianceID);       
        }elseif ($this->fetchFaction) {
            $sql .=' WHERE ca.`factionID`='.$con->qstr($this->allianceID);  
        }
        
        if($this->fetchWeek == true) {
            $sql .= ' AND kl.`killTime`BETWEEN "'.$this->startDate.'" AND "'.$this->endDate.'"';    
        }
        
        if($this->getCharacter) {
            $sql .= ' GROUP BY ca.`characterID` ';        
        }elseif ($this->getCorporation) {
            $sql .= ' GROUP BY ca.`corporationID` ';
        }
        
        $sql .= $this->SQL_end;
        if(isset($this->limit) && $this->limit>0)
        {
            $sql .='LIMIT 0,'.$this->limit;  
        }
        
        if($this->rs=$con->CacheExecute(KKS_CACHE_KILLLIST, $sql)){
                $a=0;
            	while (!$this->rs->EOF) {
        	 	
        	 	$temp[$a]['stats'] = $this->rs->fields['stats'];
        	 	if($this->getCharacter) {
            	 	$temp[$a]['characterName'] = $this->rs->fields['characterName'];
            	 	$temp[$a]['characterID'] = $this->rs->fields['characterID'];
                } elseif ($this->getCorporation) {
                    $temp[$a]['corporationName'] = $this->rs->fields['corporationName'];
            	 	$temp[$a]['corporationID'] = $this->rs->fields['corporationID'];            
                }
         		$this->rs->MoveNext();
				$a++;
				}            	
            	
            	return $this->rarray=$temp;
            
        } else {
            trigger_error('SQL Query Failed', E_USER_ERROR);
        } 
        
    }
}

?>