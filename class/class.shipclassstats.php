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
class shipClassStats 
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
    function fetchShipLostList($ID=KKS_KBCORPID, $isAlliance=FALSE,$week=NULL, $year=NULL) {
            //Get ADODB Factory INSTANCE
            $instance = ADOdbFactory::getInstance();
            //Get DB Connection
            $con = $instance->factory(KKS_DSN);
            
            if(!is_int($ID)) {
                $ID=KKS_KBCORPID;            
            }
            if(!is_int($week) && $week<=0 || $week>=53) {
                $week='WEEK(NOW())';   
            }
            if(!is_int($year)) {
                $year='YEAR(NOW())';            
            }
            if($isAlliance){
                $WHERE=' cv.allianceID='.$ID.'';
            }
            else {
                $WHERE=' cv.corporationID='.$ID.'';
            }
            $sql = 'SELECT sc.groupID, sc.groupName, count(cv.killID) as shiplosscount FROM `corpVictim` cv'
        . ' JOIN `invTypes` it ON it.typeID=cv.shipTypeID'
        . ' JOIN `corpKillLog` kl ON kl.`killID`=cv.`killID`'
        . ' RIGHT JOIN `invShipclass` sc ON sc.groupID=it.groupID'
        . ' WHERE '.$WHERE.' '
        .'AND WEEK( kl.`killTime` ) = '.$week.' AND YEAR(kl. `killTime`)='.$year.''
        . ' GROUP BY sc.groupID'
        . ' ORDER BY sc.groupName'
        . ' LIMIT 0, 40;';
            

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
    function fetchShipKillList($ID=KKS_KBCORPID, $isAlliance=FALSE,$week=NULL, $year=NULL) {
            //Get ADODB Factory INSTANCE
            $instance = ADOdbFactory::getInstance();
            //Get DB Connection
            $con = $instance->factory(KKS_DSN);
            
            if(!is_int($corporationID)) {
                $corporationID=KKS_KBCORPID;            
            }
            if(!is_int($week) && $week<=0 || $week>=53) {
                $week='WEEK(NOW())';   
            }
            if(!is_int($year)) {
                $year='YEAR(NOW())';            
            }
            if($isAlliance){
                $WHERE=' ca.allianceID='.$ID.'';
            }
            else {
                $WHERE=' ca.corporationID='.$ID.'';
            }
            $sql = 'SELECT sc.groupID, sc.groupName, count(cv.killID) as shipkillcount FROM `corpVictim` cv'
        . ' JOIN `invTypes` it ON it.typeID=cv.shipTypeID'
        . ' JOIN `corpKillLog` kl ON kl.`killID`=cv.`killID`'
        . ' JOIN `corpAttackers` ca ON ca.`killID`=cv.`killID`'
        . ' RIGHT JOIN `invShipclass` sc ON sc.groupID=it.groupID'
        . ' WHERE '.$WHERE.' '
        .'AND WEEK( kl.`killTime` ) = '.$week.' AND YEAR(kl. `killTime`)='.$year.''
        . ' GROUP BY sc.groupID'
        . ' ORDER BY sc.groupName'
        . ' LIMIT 0, 40;';
            

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
    function fetchShipClassTableArray($ID=KBCORPID, $isAlliance=FALSE, $week=NULL, $year=NULL) {
            //Get ADODB Factory INSTANCE
            $instance = ADOdbFactory::getInstance();
            //Get DB Connection
            $con = $instance->factory(KKS_DSN);
            //SQL to get a list of ship classes
            $sql = 'SELECT `groupID`, `groupName` FROM `invShipclass` LIMIT 0, 40 '; 
            //It should not change often, so cache for 6 hours
            if ($this->rs_sckill=$con->CacheExecute(21600, $sql)){
            	$sclasses=$this->rs_sckill->GetAssoc();
            } else {
            	trigger_error('SQL Query Failed', E_USER_ERROR);
            }
            $scloss=$this->rarray_scloss;
            $sckill=$this->rarray_sckill;
            foreach($sclasses as $key=>$sc) {
                if(!is_numeric($sckill[$key]['shipkillcount'])) {
                    $sckill[$key]['shipkillcount']=0;             
                }
                if(!is_numeric($scloss[$key]['shiplosscount']) || empty($scloss[$key]['shiplosscount'])) {
                    $scloss[$key]['shiplosscount']=0;             
                }
                $table[$key]=array('shipclass'=>$sc, 'shipkill'=>$sckill[$key]['shipkillcount'], 'shiploss'=>$scloss[$key]['shiplosscount']);
            }
            return $table;
    }
}

?>