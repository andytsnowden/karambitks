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
 
 
class Navigation{

	function Navigation()
	{
		// checking if a minimum navigation exists
		$this->check_navigationtable();  
		
		$this->sql_start = "SELECT * FROM navigation";
        $this->sql_end = " ORDER BY posnr";
        //$this->type_ = 'top';      
	}

	function execQuery()
	{
		//Get ADODB Factory INSTANCE
        $instance = ADOdbFactory::getInstance();
        //Get DB Connection
        $con = $instance->factory(KKS_DSN);				
	
		$sql = $this->sql_start;
		$sql .= " WHERE 1=1";

		/* Disabled until we add contracts/campigns
		if (killboard::hasContracts() == false){
			$sql .= " AND url NOT LIKE '?a=contracts'";
		}
		if (killboard::hasCampaigns() == false){
			$sql .= " AND url NOT LIKE '?a=campaigns'";
		} */
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
		
		if($this->qry=$con->CacheExecute(0, $sql)){
            $this->qry=$this->qry->GetAssoc();
        } else {
            trigger_error('SQL Query Failed', E_USER_ERROR);
        }
	}
	


	function generateMenu()
    {
    	$this->execQuery();
    	
    	$a=0;
    	foreach($this->qry as $key => $data){
    		
			$menu[$a]['link'] = $data['url']. '" target="' . $data['target'];
			$menu[$a]['text'] = $data['descr'];
    		$a++;
    	}    
        return $menu;
    }

    function check_navigationtable(){
    	
    	//Get ADODB Factory INSTANCE
        $instance = ADOdbFactory::getInstance();
        //Get DB Connection
        $con = $instance->factory(KKS_DSN); 
        
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
}
?>