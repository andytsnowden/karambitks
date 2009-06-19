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
class toplist
{
    /**
     * 
     * @var object
     */
    private $rs;

    /**
     * 
     * @var array
     */
    public $rarray;

    public $fetchAlliance = false;

    public $fetchCorp = false;

    public $fetchFaction = false;
    
    public $fetchWeek=true;

    public $corpID = 0;

    public $allianceID = 0;

    public $factionID = 0;

    public $week = NULL; // 1-53

    public $year = NULL;
    
    public $getCharacter = false;
    
    public $getCorporation = false;
    
    public $limit;

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
      $con = $this->get_connection();

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
        $end_date=date( 'Y-m-d H:i:s', strtotime($this->year.'W'.$this->week.'7 23 hour 59 minutes 59 seconds'));
        
        
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
            $sql .=' WHERE ca.`corporationID`='.$this->corpID;
        }elseif($this->fetchAlliance) {
            $sql .=' WHERE ca.`allianceID`='.$this->allianceID;       
        }elseif ($this->fetchFaction) {
            $sql .=' WHERE ca.`factionID`='.$this->allianceID;  
        }
        
        if($this->fetchWeek == true) {
            $sql .= ' AND kl.`killTime`BETWEEN "'.$start_date.'" AND "'.$end_date.'"';    
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

    /**
     * killList::get_connection()
     * 
     * connection function // returns active connection or establishes new sql con
     * 
     * @return $con
     */
    function get_connection()
    {
        //Get ADODB Factory INSTANCE
        $instance = ADOdbFactory::getInstance();
        //Get DB Connection
        $con = $instance->factory(KKS_DSN);
        return $con;
    }
}

?>