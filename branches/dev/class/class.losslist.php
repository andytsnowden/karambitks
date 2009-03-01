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
 * lossList
 * 
 * @package KarambitKS
 * @author Stephen Gulick
 * @copyright 2009
 * @version $Id$
 * @access public
 */
class lossList 
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
    

    /**
     * lossList::fetchList()
     * 
     * Use this to generate a list of losses
     * 
     * @param mixed $ID
     * @param bool  $isAlliance
     * @param mixed $week
     * @param mixed $year
     * @return void
     */
    function fetchList($ID=KKS_KBCORPID, $isAlliance=FALSE,$week=NULL, $year=NULL) {
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
            
            $sql = 'SELECT  kl . * , cv . * , ca . * , it.typeName, it.graphicID FROM `corpKillLog` kl'
        . ' JOIN `corpVictim` cv ON cv.`killID`=kl.`killID`'
        . ' JOIN `corpAttackers` ca ON ca.`killID`=cv.`killID`'
        . ' JOIN `invTypes` it ON it.`typeID` = cv.`shipTypeID`'
        . ' WHERE '.$WHERE.' '
        .'AND WEEK( kl.`killTime` ) = '.$week.' AND YEAR(kl. `killTime`)='.$year.''; 

            $this->rs=$con->CacheExecute(KKS_CACHE_KILLLIST, $sql);
            $this->rarray=$this->rs->GetAssoc();
            
    }
}

?>