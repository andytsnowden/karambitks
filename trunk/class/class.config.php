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
 * kss_config
 * 
 * @package KarambitKS
 * @author Andy Snowden
 * @copyright 2009
 * @version $Id$
 * @access public
 */
class kss_config{
	
	/** Class constructor
	 * Call primary start_up function and loads all data into assocative array
	 */
	function kss_config()
    {
        kss_config::start_up();
    }
    
    /** kss_config::start_up
     * Loads primary data into global array $kks_config
     * @var $kks_config
     * @return N/A
     */
    function start_up()
	{
		global $kks_config;
		//Get ADODB Factory INSTANCE
        $instance = ADOdbFactory::getInstance();
        //Get DB Connection
        $con = $instance->factory(KKS_DSN); 
		       
        $sql = "SELECT config_string,config_value FROM ".PREFIX_KKS."config";	
		
		//short cache time while we'er still developing.. after we finish need to come up with a method to detect config changes and reload the config.. May look into shoving the config into memcache rather than using adobe's cache	       
        if($this->rs=$con->CacheExecute(3600, $sql)){
            $kks_config=$this->rs->GetAssoc();
        } else {
            trigger_error('SQL Query Failed', E_USER_ERROR);
        }
        
        // Don't bail if the user does.
    	ignore_user_abort(true);
	}
	
	/** kss_config:get
	 * Pulls data down from global array if there is a matching key, else returns null
	 * @var config_name
	 * @return value or null
	 */
	function get($config_string)
	{
		global $kks_config;
		
		if (array_key_exists($config_string, $kks_config)){
			return $kks_config[$config_string];
		} else {
			return null;
		}		
	}
	
	/** kss_config:put
	 * Stores new config var
	 * @var config_name
	 * @var config_value
	 * @var action -- required!!
	 * @return true/false on sucess/fail
	 * 1 = insert 2 = update 3 = delete
	 * If returns true the config cache will be purged and reloaded
	 */
	 function action($config_name, $config_value, $action)
	 {
	 	//Get ADODB Factory INSTANCE
        $instance = ADOdbFactory::getInstance();
        //Get DB Connection
        $con = $instance->factory(KKS_DSN); 
        
        if (empty($action)){
        	return false;
        } else {
			switch ($action){ 
				case 1:
		 		$sql = "INSERT INTO ".PREFIX_KKS."config (`config_string` ,`config_value`)VALUES('".$config_name."', '".$config_value."') ON DUPLICATE KEY UPDATE config_value = '".$config_value."';";
				break;
			
				case 2:
				$sql = "UPDATE ".PREFIX_KKS."config SET `config_value` = '".$config_value."' WHERE `".PREFIX_KKS."config`.`config_string` = '".$config_name."' LIMIT 1 ;";
				break;
			
				case 3:
				$sql = "DELETE FROM ".PREFIX_KKS."config WHERE `config_string` = '".$config_name."' LIMIT 1";
				break;
}		 	
		 	//execute the query
		 	if ($con->Execute($sql)){
		 		//clear the cache
		 		$con->CacheFlush('SELECT config_string,config_value FROM '.PREFIX_KKS.'config');	 	
		 		//reload the settings global array
		 		kss_config::start_up();	 	
		 		//sucess return turn
				return true;	
		 	} else {
		 		//failure gets a false
		 		return false;	 	
		 	}
	 	}
	}	
}
?>