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
 * Check to see if ID is set and is a number
 */
 if(!isset($_GET['ID']) && !is_numeric($_GET['ID'])){
    exit(); 
 }
 /**
  * Check to see if ID is 0, exit script
  */
 if($_GET['ID']==0) {
    exit(); 
 }
 
 /**
  * require curl request class
  */
 require_once KKS_EXT.'/yapeal/class/CurlRequest.class.php';
 
 
 /**
  * Get the Database connection
  */
 $con = get_connection();
 
 /**
  * SQL query to fetch character image from database
  */
 $sql = "SELECT * FROM `".PREFIX_KKS."characterIcon` WHERE `characterID`=".$con->qstr($_GET['ID'], get_magic_quotes_gpc())." LIMIT 1 ";
try {
 if($rs=$con->CacheExecute(KKS_CACHE_KILLLIST, $sql)){
    $rarray=$rs->GetAssoc();
    /**
     * Display Image from Database
     */
    header('Content-Type: image/jpeg');
    echo $rarray[$_GET['ID']]['icon'];
    
 } else {
 /**
  * If not in database, add it and display it
  */
   $image=fetchIcon($_GET['ID']);
   header('Content-Type: image/jpeg');
   echo $image;
}
}
catch (Exception $e) {
    /**
     * If no resluts, add it to database and display it
     */
    $image=fetchIcon($_GET['ID']);
   header('Content-Type: image/jpeg');
   echo $image;
}
if($rs->EOF){
    /**
     * If no resluts, add it to database and display it
     */
    $image=fetchIcon($_GET['ID']);
   header('Content-Type: image/jpeg');
   echo $image;
}


/**
 * fetchIcon()
 * 
 * @param interger $characterID
 * @return image string
 */
function fetchIcon($characterID) {
    /**
     * Get database connection
     */
    $con = get_connection();
    /**
     * Set Needed Paramaters for CURL
     */
        $params['url']='http://img.eve.is/serv.asp?s=64&c='.$characterID;
        $params['method']='GET';
        
        /**
         * New Curl Request Class
         */
        $curl= new CurlRequest($params);
        /**
         * Execute Curl Request
         */
        $image=$curl->exec();
       
        /** 
         * SQL Query to insert image into database
         */
        $sql="INSERT INTO `".PREFIX_KKS."characterIcon` (`characterID`, `icon`, `date`) VALUES (".$con->qstr($characterID, get_magic_quotes_gpc()).", NULL, ".$con->DBDate(time()).") ON DUPLICATE KEY UPDATE date=".$con->DBDate(time()).";";
        
        /**
         * Execute SQL query
         */
        if ($con->Execute($sql) === false) {
        
                 print 'error inserting: '.$conn->ErrorMsg().'<BR>';
        
        }
        /**
         * Update database record with image
         */
        $con->UpdateBlob(PREFIX_KKS.'characterIcon','icon',$image['body'], 'characterID='.$characterID);
        return $image['body'];
}




 /**
  * get_connection()
  * 
  * @return ADODB database object
  */
 function get_connection()
    {
        //Get ADODB Factory INSTANCE
        $instance = ADOdbFactory::getInstance();
        //Get DB Connection
        $con = $instance->factory(KKS_DSN);
        return $con;
    }

?>