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
class killList
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
    
    public $fetchMonth = false;
    
    public $fetchLastDay = false;
    
    public $charID = 0;

    public $corpID = 0;

    public $allianceID = 0;

    public $factionID = 0;

    public $week = NULL; // 1-53
    
    public $month = NULL; // 1-12

    public $year = NULL;
    
    private $startDate;
    
    private $endDate;

    public $countInvolved = false;

    public $filterClass = null;


    
    function getTimeFrame() {
        if($this->fetchLastDay) {
            $this->startDate=date( 'Y-m-d H:i:s', time()-86400);
            $this->endDate=date( 'Y-m-d H:i:s', time());
            return true;
        }
        else if($this->fetchWeek) {
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
                $this->startDate=date( 'Y-m-d H:i:s', strtotime($this->year.'W'.$this->week));
                $this->endDate=date( 'Y-m-d H:i:s', strtotime($this->year.'W'.$this->week.'7 23 hour 59 minutes 59 seconds'));
                return true;
        }
        else if($this->fetchMonth) {
            //Change Week and Year to format we can use
                if(!isset($this->month) && !is_numeric($this->month)) {
                    $this->month = date('M');
                }
                if(!isset($this->year) && !is_numeric($this->year)) {
                    $this->year = date('Y');
                }
                if($this->month<10) {
                    $month=$this->month;
                    $pad=str_pad($month, 2, 0, STR_PAD_LEFT);
                    $this->month=substr($pad, -2, 2);
                }
                $start = mktime(0, 0, 0, $this->month  , '01', $this->year);
                $end = mktime(23, 59, 59, $this->month+1  , 01-1, $this->year);
                $this->startDate=date( 'Y-m-d H:i:s', $start);
                $this->endDate=date( 'Y-m-d H:i:s', $end);
                return true;
        }
        return false;
    }

    /**
     * karambitks::get_connection()
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