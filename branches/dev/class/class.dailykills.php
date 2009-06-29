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

class dailyKills extends kks
{
    
    function __construct()
    {
        /*"SELECT date(kl.`killTime`) AS thedate, count(kl.`killID`) AS count, dayofweek(date(kl.`killTime`)) as dayOfWeek\n"
    . "FROM yapeal_corpKillLog kl\n"
    . "JOIN `yapeal_corpVictim` cv ON cv.`killID`=kl.`killID`\n"
    . "JOIN `yapeal_corpAttackers` ca ON ca.`killID`=cv.`killID`\n"
    . "JOIN `yapeal_corpAttackers` caf ON caf.`killID`=cv.`killID`\n"
    . "WHERE ca.`corporationID`=170567768 AND kl.`killTime`BETWEEN \"2009-06-15 00:00:00\" AND \"2009-06-21 23:59:59\"\n"
    . "GROUP BY thedate\n"
    . "ORDER BY thedate ASC ";*/
    
    
        $this->SQL_start = "SELECT date(kl.`killTime`) AS thedate, count(kl.`killID`) AS count, dayofweek(date(kl.`killTime`)) as dayOfWeek FROM `".PREFIX_YAPEAL."corpKillLog` kl ";
        $this->SQL_joins = "JOIN `".PREFIX_YAPEAL."corpVictim` cv ON cv.`killID`=kl.`killID`\n"
    . "JOIN `".PREFIX_YAPEAL."corpAttackers` ca ON ca.`killID`=cv.`killID`";
        $this->SQL_end ="GROUP BY date(kl.`killTime`) ORDER BY date(kl.`killTime`) ASC ";;
    }


/**
     * dailykills::fetchList()
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
        $sql .= ' WHERE 1=1';
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
        
        //echo $sql;
        
        
        if($this->rs=$con->CacheExecute(KKS_CACHE_KILLLIST, $sql)){
            //echo"<pre>";var_dump($this->rs);echo"</pre>";
            $this->rarray=$this->rs->GetAssoc();
        } else {
            trigger_error('SQL Query Failed', E_USER_ERROR);
        } 

    }
    public function formatArray(){
        $return=array();
        foreach($this->rarray as $line){
            $return[]=$line['count'];
        }
        return $return;             
    }
}
?>