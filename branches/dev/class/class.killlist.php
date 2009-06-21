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
 * killList
 * 
 * @package KarambitKS
 * @author Stephen Gulick
 * @copyright 2009
 * @version $Id$
 * @access public
 */
class killList extends kks
{
    function __construct()
    {
        $this->SQL_start = 'SELECT  it.typeName as shiptype, cv.characterName as victimName, cv.corporationName as vcorpName, cv.allianceName as valliName, map.solarSystemName, map.security, caf.characterName as killerName, caf.corporationName AS kcorpName, caf.allianceName AS kalliNmae,it.graphicID, kl.killTime, kl.killID';
        $this->SQL_joins = ' FROM `'.PREFIX_YAPEAL.'corpKillLog` kl' .
            ' JOIN `'.PREFIX_YAPEAL.'corpVictim` cv ON cv.`killID`=kl.`killID`' .
            ' JOIN `'.PREFIX_YAPEAL.'corpAttackers` ca ON ca.`killID`=cv.`killID`' .
            ' JOIN `'.PREFIX_YAPEAL.'corpAttackers` caf ON caf.`killID`=cv.`killID`' .
            ' JOIN `'.PREFIX_EVE.'invTypes` it ON it.`typeID` = cv.`shipTypeID`' .
            ' JOIN `'.PREFIX_EVE.'mapSolarSystems` map ON map.`solarSystemID` = kl.`solarSystemID`';
        $this->SQL_end = ' ORDER BY kl.`killTime` DESC';
    }

    /**
     * killList::fetchList()
     * 
     * Use this to generate a list of kills
     * 
     * @return void
     */
    function fetchList()
    {
        $this->getTimeFrame();
        
        $con = $this->get_connection();
        /*
        //Change Week and Year to format we can use
        if(!isset($this->week) && !is_numeric($this->week)) {
            $this->week = date('W');
        }
        if(!isset($this->year) && !is_numeric($this->year)) {
            $this->year = date('Y');
        }
        if($this->week<10) {
            $week=$this->week;
            $pad=str_pad($week, 2, 0, STR_PAD_LEFT);
            $this->week=substr($pad, -2, 2);
        }
        $start_date=date( 'Y-m-d H:i:s', strtotime($this->year.'W'.$this->week));
        $end_date=date( 'Y-m-d H:i:s', strtotime($this->year.'W'.$this->week.'7 23 hour 59 minutes 59 seconds'));*/
        
        $sql = $this->SQL_start;
        $sql .= $this->SQL_joins;
        $sql .= ' WHERE caf.`finalBlow`=1';
        if ($this->fetchCorp == true && $this->corpID > 0)
        {
            $sql .= ' AND ca.corporationID=' . $this->corpID;
        }
        if ($this->fetchAlliance == true && $this->allianceID > 0)
        {
            $sql .= ' AND ca.allianceID=' . $this->allianceID;
        }
        if ($this->fetchFaction == true && $this->factionID > 0)
        {
            $sql .= ' AND ca.factionID=' . $this->factionID;
        }
        if($this->fetchWeek == true) {
            $sql .= ' AND kl.`killTime`BETWEEN "'.$this->startDate.'" AND "'.$this->endDate.'"';    
        }
        $sql .= $this->SQL_end;
        if($this->rs=$con->CacheExecute(KKS_CACHE_KILLLIST, $sql)){
        $this->rarray=$this->rs->GetAssoc();
        } else {
        trigger_error('SQL Query Failed', E_USER_ERROR);
        } 

    }
}

?>