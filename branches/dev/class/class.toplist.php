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

class toplist
{
    Private $rs_char_corp;
    
    Public $ra_char_corp;
    
    
    function char_corplist ($corpID, $limit=50){
        //Get ADODB Factory INSTANCE
        $instance = ADOdbFactory::getInstance();
        //Get DB Connection
        $con = $instance->factory(KKS_DSN);
        
        //Make sure corpID is a number
        if(!is_int($corpID)) {
            $corpID=0;
        }
        if(!is_int($limit)) {
            $corpID=50;
        }
        
        
        $sql = 'SELECT characterID, count(killID) as stats, characterName FROM `corpAttackers`'
        .' WHERE `corporationID`='.$corpID.' GROUP BY `characterID` ORDER BY stats DESC LIMIT 0,'.$limit;
        
        if($this->rs_char_corp=$con->CacheExecute(KKS_CACHE_KILLLIST, $sql)){
            	
            	
            	$a=0;
            	while (!$this->rs_char_corp->EOF) {
        	 	
        	 	$temp[$a]['stats'] = $this->rs_char_corp->fields['stats'];
        	 	$temp[$a]['characterName'] = $this->rs_char_corp->fields['characterName'];
        	 	$temp[$a]['characterID'] = $this->rs_char_corp->fields['characterID'];

         		$this->rs_char_corp->MoveNext();
				$a++;
				}            	
            	
            	$this->ra_char_corp=$temp;
            	
            } else {
            	trigger_error('SQL Query Failed', E_USER_ERROR);
            }
    }
}

?>