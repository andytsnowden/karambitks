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
 * @version    SVN: $Id: class.killlist.php 42 2009-03-08 05:41:15Z stephenmg12 $
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
class xmlFeed
{
    /**
     * 
     * @var object
     */
    public $rs;

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

    public $countInvolved = false;

    public $filterClass = null;

    function __construct()
    {   
        require_once KKS_ADODB.'adodb-exceptions.inc.php';
        $this->SQL_start = 'SELECT  cv.characterName as victimName, cv.characterID as victimID, cv.corporationName as vcorpName, cv.corporationID as vcorpID, cv.allianceName as valliName, cv.allianceID as valliID, cv.factionID as vfactID, cv.factionName as vfactName, kl.solarSystemID, kl.killTime, kl.killID, kl.moonID, cv.shipTypeID, cv.damageTaken';
        $this->SQL_joins = ' FROM `'.PREFIX_YAPEAL.'corpKillLog` kl' .
            ' JOIN `'.PREFIX_YAPEAL.'corpVictim` cv ON cv.`killID`=kl.`killID`' .
            ' JOIN `'.PREFIX_YAPEAL.'corpAttackers` ca ON ca.`killID`=cv.`killID`';
        $this->SQL_end = ' ORDER BY kl.`killID`;';
    }

    /**
     * killList::fetchList()
     * 
     * Use this to generate a list of kills
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
        $sql .= $this->SQL_joins;
        $sql .= ' WHERE 1=1';
        if ($this->fetchCorp == true && $this->corpID > 0)
        {
            $sql .= ' AND ca.corporationID=' . $this->corpID.' AND cv.corporationID='.$this->corpID;
        }
        if ($this->fetchAlliance == true && $this->allianceID > 0)
        {
            //$sql .= ' AND ca.allianceID=' . $this->allianceID.' AND cv.allianceID='.$this->allianceID;
        }
        if ($this->fetchFaction == true && $this->factionID > 0)
        {
            $sql .= ' AND ca.factionID=' . $this->factionID.' AND cv.cfactionID='.$this->factionID;
        }
        if($this->fetchWeek == true) {
            //$sql .= ' AND kl.`killTime`BETWEEN "'.$start_date.'" AND "'.$end_date.'"';
        }
        $sql .= $this->SQL_end;
        
        if($this->rs=$con->Execute($sql)){

        $this->rarray=$this->rs->GetAssoc();
        } else {
            echo $sql.PHP_EOL;
            trigger_error('SQL Query Failed', E_USER_ERROR);       
        } 

    }
    
    function getAttackers($killID) {
            //Get Connection
            $con = $this->get_connection();
            

            $sql = 'SELECT `characterID`, `characterName`, `corporationID`, `corporationName`, `allianceID`, `allianceName`,`factionID`, `factionName`, `finalBlow`, `damageDone`, `securityStatus`, `shipTypeID`, `weaponTypeID` FROM `'.PREFIX_YAPEAL.'corpAttackers` WHERE `killID`='.$killID.' LIMIT 0, 100 ';
            
            
            try {if($this->rs_attackers=$con->Execute($sql)){
                $this->rarray_attackers=$this->rs_attackers->GetAssoc();
            } else {
                echo $sql.PHP_EOL;
                //trigger_error('SQL Query Failed', E_USER_ERROR);
            }
            unset($this_rs_attackers);
            return $this->rarray_attackers;
            }
            catch (exception $e) {
                    print_r($e);        
            }
    }
    
    function getItems($killID) {
            //Get Connection
            $con = $this->get_connection();
            
            $sql = 'SELECT  ci.`lft`, ci.`rgt`, ci.`lvl`, ci.`killID`, ci.`flag`, ci.`qtyDropped`, ci.`qtyDestroyed`, ci.`typeID`, it.typeName FROM `'.PREFIX_YAPEAL.'corpItems` ci'
        . ' JOIN '.PREFIX_EVE.'invTypes it ON it.`typeID`=ci.`typeID`'
        . ' WHERE `killID`='.$killID.' ORDER BY `lft`'; 
            
            //echo $sql.PHP_EOL;
            if($this->rs_items=$con->Execute($sql)){
                $this->rarray_items=$this->rs_items->GetAssoc();
            } else {
                trigger_error('SQL Query Failed', E_USER_ERROR);
            }
            //$rs=$con->Execute($sql);
            //echo"<pre>";print_r($this->rs_items);echo("</pre><hr>");
            //unset($this->_rs_items);
            return $this->rarray_items;
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
        if($con = $instance->factory(KKS_DSN)){
            return $con; 
        }
        else { trigger_error('SQL Connection ERROR', E_USER_ERROR);}
    }
}

?>