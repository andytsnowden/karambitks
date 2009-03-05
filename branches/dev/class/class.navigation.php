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
 * kss_navigation
 * 
 * @package KarambitKS
 * @author Andy Snowden
 * @copyright 2009
 * @version $Id$
 * @access public
 * @abstract based on EDK classes.
 */
class kss_navigation{

	/**
	 * class constructor
	 * runs prechecks on nav table or loads defaults
	 */
	function kss_navigation()
	{
		// checking if a minimum navigation exists
		$this->check_navtable();  
		
		$this->sql_start = "SELECT * FROM navigation";
        $this->sql_end = " ORDER BY posnr";
        //$this->type_ = 'top';      
	}
	
	/**
	 * connection function // returns active connection or establishes new sql con
	 */
	function get_connection()
	{
		//Get ADODB Factory INSTANCE
        $instance = ADOdbFactory::getInstance();
        //Get DB Connection
        $con = $instance->factory(KKS_DSN);
        return $con;
	}

	/**
	 * kss_navigation::execQuery()
	 * Called by kss_navigation::generateMenu() - Queries menu against Db depending on kb settings.
	 * @return Array of data in assocative array.
	 */
	function execQuery()
	{		
        //Get DB Connection
        $con = 	$this->get_connection();	
	
		$sql = $this->sql_start;
		$sql .= " WHERE 1=1";
		
		if ($this->hasContracts() == false){
			$sql .= " AND url NOT LIKE '?v=contracts'";
		}
		if ($this->hasCampaigns() == false){
			$sql .= " AND url NOT LIKE '?v=campaigns'";
		} 
		if (kss_config::get('public_losses'))
		{
			$sql .= " AND url NOT LIKE '?v=losses'";
		}		
		if (kss_config::get('public_stats')=='remove')
		{
			$sql .= " AND url NOT LIKE '?v=stats'";
		}
		$sql .= " AND hidden = 0";
		$sql .= $this->sql_end;	
				
		if($this->qry=$con->CacheExecute(3600, $sql)){
            $this->qry=$this->qry->GetAssoc();
            $this->sql = $sql;
            return true;
        } else {
            return false;
        }
	}
	

	/**
	 * kss_navigation::generateMenu()
	 * Calls kss_navigation::execQuery() to pull and generate a data array with the menu contents.
	 * @return returns assocative menu array
	 */
	function generateMenu()
    {    	
    	//make sure we get data from the query    	
    	if (!$this->execQuery()){   	    	
        	trigger_error('No data returned from Query', E_USER_ERROR);
        } else {
			      $a=0;
	    	foreach($this->qry as $key => $data){
	    		
				$menu[$a]['link'] = $data['url']. '" target="' . $data['target'];
				$menu[$a]['text'] = $data['descr'];
	    		$a++;
	    	}    
	        return $menu;  	
        }        
    }

	/**
	 * kss_navigation::check_navtable()
	 * Checks if navigation table is empty, if so fills in default values
	 */
    function check_navtable(){
    	
    	//Get DB Connection
        $con = 	$this->get_connection();
        
		if (KKS_KBCORPID)
		{
		    $statlink = '?v=corp_detail&crp_id='.KKS_KBCORPID;
		}
		elseif (KKS_KBALLIANCEID)
		{
		    $statlink = '?v=alliance_detail&all_id='.KKS_KBALLIANCEID;
		}
		$sql = "select count(ID) as count from navigation";
		if($query=$con->CacheExecute(0, $sql)){} 
		else {
            trigger_error('SQL Query Failed', E_USER_ERROR);
        }
        
        $query = substr($query,6,7);
		if ($query == 0)
		{
			
			// add a menu			
			$sql = "
			INSERT INTO `navigation` (`ID`, `descr`, `url`, `target`, `posnr`, `hidden`) VALUES
			(null, 'Home', '?v=home', '_self', 1, 0),
			(null, 'Campaigns', '?v=campaigns', '_self', 2, 0),
			(null, 'Contracts', '?v=contracts', '_self', 3, 0),
			(null, 'Kills', '?v=kills', '_self', 4, 0),
			(null, 'Losses', '?v=losses', '_self', 5, 0),
			(null, 'Post Mail', '?v=post', '_self', 6, 0),			
			(null, 'Stats', '".$statlink."', '_self', 7, 0),
			(null, 'Search', '?v=search', '_self', 8, 0),
			(null, 'Admin', '?v=admin', '_self', 9, 0),
			(null, 'About', '?v=about', '_self', 10, 0)";
			
			$con->Execute($sql);
		} 
	}
	
	/**
	 * Campaign/Contract
	 * Returns data if active else returns false
	 * @author EDK // Modified for use with KKS
	 */
	function hasCampaigns($active = false)
    {
        //Get DB Connection
        $con = 	$this->get_connection();
        
        $sql = "select ctr_id from contracts where ctr_campaign = 1";
        if ($active) $sql .= " and ( ctr_ended is null or now() <= ctr_ended )";
        if ($query = $con->Execute($sql)){
	        if ($query->RecordCount() > 0){
	        	return $query->RecordCount();
	        } else {
	        	return FALSE;
	        }
        } else {
        	return FALSE;
        }
    }

    function hasContracts($active = false)
    {
        //Get DB Connection
        $con = 	$this->get_connection();
        
        $sql = "select ctr_id from contracts where ctr_campaign = 0";
        if ($active) $sql .= " and ( ctr_ended is null or now() <= ctr_ended )";
        if ($query = $con->Execute($sql)){
	        if ($query->RecordCount() > 0){
	        	return $query->RecordCount();
	        } else {
	        	return FALSE;
	        }
        } else {
        	return FALSE;
        }
    }
    
    /**
     * kss_navigation::addmenu
     * Adds menu items
     * @var Desc - Display Name
     * @var url - internal/external link
     * @var target - SELF, blank (Do not sent _ just the word)
     * @var hidden (optional) - will hide from view.
     * @return True/False -- will throw errors for sql related issues.
     */
    function addmenu($desc, $url, $target, $hidden=0)
    {
    	//Get DB Connection
        $con = 	$this->get_connection();
    	
    	if (!empty($desc) AND !empty($url) AND !empty($target)){
    		
    		$sql = "SELECT posnr FROM navigation ORDER BY posnr DESC LIMIT 1";
    		if ($recordSet = $con->Execute($sql)){
    			$this->lastposnr = ($recordSet->fields[0] + 1);
    			
    			$sql = "INSERT INTO `navigation` (`ID`, `descr`, `url`, `target`, `posnr`, `hidden`) VALUES (NULL, '$desc', '$url', '_$target', '$this->lastposnr', '$hidden');";
    			if ($query = $con->Execute($sql)){
    				//success now lets flush the cache and reload the menu
    				$this->execQuery();
    				$con->CacheFlush($this->sql);
    				return true;
    			} else {
    				trigger_error('unable to insert record', E_USER_ERROR);	
    			}    			
    		} else {
    			trigger_error('unable to run query', E_USER_ERROR);
    		}    		
    	} else {
    		return false;
    	}
    }
}
?>