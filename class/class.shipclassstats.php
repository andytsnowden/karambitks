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
 * shipClassStats
 * 
 * @package KarambitKS
 * @author Stephen Gulick
 * @copyright 2009
 * @version $Id$
 * @access public
 */
class shipClassStats extends kks 
{   
    /**
     * 
     * @var object
     */
    private $rs_scloss;
    
    /**
     * 
     * @var array
     */
    public $rarray_scloss;
    
        /**
     * 
     * @var object
     */
    private $rs_sckill;
    
    /**
     * 
     * @var array
     */
    public $rarray_sckill;
    
    
    function __construct()
    {
        //Begining of SQL statements
        $this->SQL_start='SELECT sc.groupID, sc.groupName,';        
        $this->SQL_joins='FROM `'.PREFIX_YAPEAL.'corpVictim` cv JOIN `'.PREFIX_EVE.'invTypes` it ON it.typeID=cv.shipTypeID JOIN `'.PREFIX_YAPEAL.'corpKillLog` kl ON kl.`killID`=cv.`killID` RIGHT JOIN `'.PREFIX_KKS.'invShipclass` sc ON sc.groupID=it.groupID';
        $this->SQL_end=' GROUP BY sc.groupID ORDER BY sc.groupName LIMIT 0, 40;';
        
    }
    
    /**
     * lossList::fetchShipLostList()
     * 
     * Use this to generate a list of losses
     * 
     * @param mixed $ID
     * @param bool  $isAlliance
     * @param mixed $week
     * @param mixed $year
     * @return void
     */
    function fetchShipLostList() {
        $con = $this->get_connection();
        
        $this->getTimeFrame();
            
        $sql  = $this->SQL_start;
        $sql .=' count(cv.killID) as shiplosscount ';
        $sql .= $this->SQL_joins;
        $sql .= ' WHERE 1=1';
        if ($this->fetchCorp == true && $this->corpID > 0)
        {
            $sql .= ' AND cv.corporationID=' . $con->qstr($this->corpID);
        }
        elseif ($this->fetchAlliance == true && $this->allianceID > 0)
        {
            $sql .= ' AND cv.allianceID=' . $con->qstr($this->allianceID);
        }
        elseif ($this->fetchFaction == true && $this->factionID > 0)
        {
            $sql .= ' AND cv.factionID=' . $con->qstr($this->factionID);
        }
	    else
        {
            $put_an_and_at_the_end = false;
        }
        if($this->fetchWeek == true)
        {
            $sql .= ' AND kl.killTime BETWEEN "'.$this->startDate.'" AND "'.$this->endDate.'"';    
        }
        $sql .=$this->SQL_end;
                
        //Execute Query
            if($this->rs_scloss=$con->CacheExecute(KKS_CACHE_STATS, $sql)){
            	$this->rarray_scloss=$this->rs_scloss->GetAssoc();
            } else {
            	trigger_error('SQL Query Failed', E_USER_ERROR);
            }
            
    }
    
        /**
     * lossList::fetchShipKillList()
     * 
     * Use this to generate a list of losses
     * 
     * @param mixed $ID
     * @param bool  $isAlliance
     * @param mixed $week
     * @param mixed $year
     * @return void
     */
    function fetchShipKillList() {
        $con = $this->get_connection();
        
        $this->getTimeFrame();
        
        $sql  = $this->SQL_start;
        $sql .=' count(cv.killID) as shipkillcount ';
        $sql .= $this->SQL_joins;
        $sql .= ' JOIN `'.PREFIX_YAPEAL.'corpAttackers` ca ON ca.`killID`=cv.`killID` ';
        $sql .= ' WHERE 1=1';
        if ($this->fetchCorp == true && $this->corpID > 0)
        {
            $sql .= ' AND ca.corporationID=' . $con->qstr($this->corpID);
        }
        elseif ($this->fetchAlliance == true && $this->allianceID > 0)
        {
            $sql .= ' AND ca.allianceID=' . $con->qstr($this->allianceID);
        }
        elseif ($this->fetchFaction == true && $this->factionID > 0)
        {
            $sql .= ' AND ca.factionID=' . $con->qstr($this->factionID);
        }
        else
        {
            $put_an_and_at_the_end = false;
        }
        if($this->fetchWeek == true) {
            $sql .= ' AND kl.killTime BETWEEN "'.$this->startDate.'" AND "'.$this->endDate.'"';    
        }
        $sql .=$this->SQL_end;
        
        //Execute Query
            if($this->rs_sckill=$con->CacheExecute(KKS_CACHE_STATS, $sql)){
            	$this->rarray_sckill=$this->rs_sckill->GetAssoc();
            } else {
            	trigger_error('SQL Query Failed', E_USER_ERROR);
            }
            
    }
    
    /**
     * shipClassStats::fetchShipClassTableArray()
     * 
     * @param mixed $ID
     * @param bool $isAlliance
     * @param mixed $week
     * @param mixed $year
     * @return array $table
     */
    function fetchShipClassTableArray() {
            //Get ADODB Factory INSTANCE
            $instance = ADOdbFactory::getInstance();
            //Get DB Connection
            $con = $instance->factory(KKS_DSN);
            //SQL to get a list of ship classes
            $sql = 'SELECT `groupID`, `groupName` FROM `'.PREFIX_KKS.'invShipclass` LIMIT 0, 40 '; 
            //It should not change often, so cache for 6 hours
            if ($this->rs_sckill=$con->CacheExecute(21600, $sql)){
            	$sclasses=$this->rs_sckill->GetAssoc();
            } else {
            	trigger_error('SQL Query Failed', E_USER_ERROR);
            }
            $this->fetchShipKillList();
            $this->fetchShipLostList();
            $scloss=$this->rarray_scloss;
            $sckill=$this->rarray_sckill;
            foreach($sclasses as $key=>$sc) {
                if(empty($sckill[$key]) || !is_numeric($sckill[$key]['shipkillcount'])) {
                    $sckill[$key]['shipkillcount']=0;             
                }
                if(empty($scloss[$key]['shiplosscount']) || !is_numeric($scloss[$key]['shiplosscount'])) {
                    $scloss[$key]['shiplosscount']=0;             
                }
                $table[$key]=array('shipclass'=>$sc, 'shipkill'=>$sckill[$key]['shipkillcount'], 'shiploss'=>$scloss[$key]['shiplosscount']);
            }
            return $table;
    }
}

?>
