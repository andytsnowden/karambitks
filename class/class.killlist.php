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
        $this->SQL_start = "SELECT  it.typeName as shiptype, it.typeID as shipTypeID, cv.characterID as victimID, cv.characterName as victimName, cv.corporationName as vcorpName, cv.corporationID as vcorpID, cv.allianceName as valliName, cv.allianceID as valliID, al.icon, map.solarSystemName, format(map.security,2) AS security,  caf.characterName as killerName, caf.corporationName AS kcorpName, caf.allianceName AS kalliNmae,it.graphicID, kl.killTime, kl.killID as killID, (SELECT count(killID) FROM `yapealcorpAttackers` WHERE `killID` = `killID`)";
        $this->SQL_joins = ' FROM `'.PREFIX_YAPEAL.'corpKillLog` kl' .
            ' JOIN `'.PREFIX_YAPEAL.'corpVictim` cv ON cv.`killID`=kl.`killID`' .
            ' JOIN `'.PREFIX_YAPEAL.'corpAttackers` ca ON ca.`killID`=cv.`killID`' .
            ' JOIN `'.PREFIX_YAPEAL.'corpAttackers` caf ON caf.`killID`=cv.`killID`' .
            ' JOIN `'.PREFIX_EVE.'invTypes` it ON it.`typeID` = cv.`shipTypeID`' .
            ' JOIN `'.PREFIX_EVE.'mapSolarSystems` map ON map.`solarSystemID` = kl.`solarSystemID`'
            . ' LEFT JOIN '.PREFIX_KKS.'allianceLogo al ON al.allianceID=cv.allianceID';
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
        
        $sql = $this->SQL_start;
        $sql .= $this->SQL_joins;
        $sql .= ' WHERE caf.`finalBlow`=1';
        if ($this->fetchCorp == true && $this->corpID > 0)
        {
            $sql .= ' AND ca.corporationID=' . $con->qstr($this->corpID);
        }
        if ($this->fetchAlliance == true && $this->allianceID > 0)
        {
            $sql .= ' AND ca.allianceID=' . $con->qstr($this->allianceID);
        }
        if ($this->fetchFaction == true && $this->factionID > 0)
        {
            $sql .= ' AND ca.factionID=' . $con->qstr($this->factionID);
        }
        if($this->fetchWeek == true) {
            $sql .= ' AND kl.`killTime`BETWEEN "'.$this->startDate.'" AND "'.$this->endDate.'"';    
        }
        $sql .= $this->SQL_end;
        echo $sql;
        if($this->rs=$con->CacheExecute(KKS_CACHE_KILLLIST, $sql)){
        $this->rarray=$this->rs->GetAssoc();
        } else {
        trigger_error('SQL Query Failed', E_USER_ERROR);
        } 

    }
}

?>