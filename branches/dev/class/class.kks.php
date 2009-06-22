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

class kks
{
    /**
     * 
     * @var object
     */
    protected $rs;

    /**
     * 
     * @var array
     */
    public $rarray;

    /**
     * Varible to set mode to fatch data by alliance
     * 
     * @var bool
     */
    public $fetchAlliance = false;

    public $fetchCorp = false;

    public $fetchFaction = false;
    
    public $fetchWeek=true;
    
    Public $fetchMonth=false;

    public $corpID = 0;

    public $allianceID = 0;

    public $factionID = 0;

    public $week = NULL; // 1-53
    
    public $month = NULL;

    public $year = NULL;

    public $countInvolved = false;

    public $filterClass = null;
    
    public $startDate;
    
    public $endDate;
    
    protected function getTimeFrame()
    {
        if(!isset($this->year) && !is_numeric($this->year)) {
            $this->year = date('Y');
        } //if year is set and number
        /**
         * Check to see if we are fetching data by week
         */        
        if($this->fetchWeek) {
            if(!isset($this->week) && !is_numeric($this->week)) {
                $this->week = date('W');
            } //if $this->week set and number
            /**
             * Check to see if week number is below 10 (sigle digit)
             */
            if($this->week<10) {
                $week=$this->week;
                $pad=str_pad($week, 2, 0, STR_PAD_LEFT);
                $this->week=substr($pad, -2, 2);
            } //if week<10
            /**
             * Set start and End Dates
             */
            $this->startDate=date( 'Y-m-d H:i:s', strtotime($this->year.'W'.$this->week));
            $this->endDate=date( 'Y-m-d H:i:s', strtotime($this->year.'W'.$this->week.'7 23 hour 59 minutes 59 seconds'));
        } //if fetchWeek==TRUE                
    }
    
    /**
     * kks::get_connection()
     * 
     * @return ADOdb connection object
     */
    protected function get_connection()
    {
        //Get ADODB Factory INSTANCE
        $instance = ADOdbFactory::getInstance();
        //Get DB Connection
        $this->con = $instance->factory(KKS_DSN);
        return $this->con;
    }
}

?>