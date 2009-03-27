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

@set_time_limit(120);


    //Set Header to XML
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public");
   // header("Content-Description: File Transfer");
    //header('Content-Type: text/xml');
   
    
    //Create DoM Doc
    $doc = new DOMDocument('1.0', 'iso-8859-1');
    $doc->formatOutput = true;
    
    //Create Root Element
    $root = $doc->createElement('kksfeed');
    $root = $doc->appendChild($root);
    $root->setAttribute('version', '0.1');
    
    //Add comment for tracker
    $comment = $doc->createComment('KKS XML Feed');
    $comment = $root->appendChild($comment);
    
    // Add Current Time Elelement
    //Get and format time
    $time        = time();
    $currentTime = gmdate("Y-m-d H:i:s", $time);
    //create xml for element
    $cur_time = $doc->createElement(currentTime);
    $cur_time = $root->appendChild($cur_time);
    $value = $doc->createTextNode($currentTime);
    $value = $cur_time->appendChild($value);
    
    //Result Element
    $result = $doc->createElement(result);
    $result = $root->appendChild($result);
    
    //Rowset Element
    $rowset = $doc->createElement(rowset);
    $rowset = $result->appendChild($rowset);
    //Add Atributes to rowset
    $rowset->setAttribute('name', 'kills');
    $rowset->setAttribute('key', 'killID');
    $rowset->setAttribute('columns', 'killID,solarSystemID,killTime,moonID');
    

//Get Needed Classes
require_once KKS_CLASS.'class.xmlfeed.php';

//Check to see if Week and Year are set //TODO:interval may change
if(isset($_GET['w']) && is_numeric($_GET['w'])) {
    $week=$_GET['w'];
}
else {
    $week=date('W');
}
if(isset($_GET['y']) && is_numeric($_GET['y'])) {
    $year=$_GET['y'];
    $y=$year;
}
else {
    $year=date('Y');
    $y=$year;

}
//New KillList
$kl = New xmlFeed();


if(is_numeric($_GET['id'] && !empty($_GET['id']))) {
    if(!empty($_GET['type']) && is_string($_GET['type'])) {
        //Shorter vars to work with
        $type=$_GET['type'];
        $id=$_GET['id'];
        //Set options for killlist
        switch ($type){ 
	case 'char':
	    $kl->charID=$id;
        $kl->fetchChar=TRUE;
	break;   
	case 'corp':
        $kl->corpID=$id;
        $kl->fetchCorp=TRUE;
	break;
	case 'alliance':
	    $kl->allianceID=$id;
        $kl->fetchAlliance=TRUE;
	break;
	case 'faction':
	    $kl->factionID=$id;
        $kl->fetchFaction=TRUE;
	break;
}
            
    }
}

//Set KillList timeframe options
$kl->week=$week;
$kl->year=$year;

//Get the list
$kl->fetchList();
//Get the results
$kills=$kl->rarray;

/*
foreach($kills as $kill) {
echo"<pre>";print_r($kill);echo"</pre><br><br><hr>\n";
}*/


foreach ($kills as $kill)
{

    //Row Element (kill)
    $killrow = $doc->createElement(row);
    $killrow = $rowset->appendChild($killrow);
    //Adding attributes to kill row
    $killrow->setAttribute('killID', $kill['killID']);
    $killrow->setAttribute('solarSystemID', $kill['solarSystemID']);
    $killrow->setAttribute('killTime', $kill['killTime']);
    $killrow->setAttribute('moonID', $kill['moonID']);
    
    //Add Victom element
    $victim = $doc->createElement(victim);
    $victim = $killrow->appendChild($victim);
    //Add Attributes to Victom
    $victim->setAttribute('characterID', $kill['victimID']);
    $victim->setAttribute('characterName', $kill['victimName']);
    $victim->setAttribute('corporationID', $kill['vcorpID']);
    $victim->setAttribute('corporationName', $kill['vcorpName']);
    $victim->setAttribute('allianceID', $kill['valliID']);
    $victim->setAttribute('damageTaken', $kill['damageTaken']); 
    $victim->setAttribute('shipTypeID', $kill['shipTypeID']);
    
    
    //Add Attacker rowset element
    $attackrs = $doc->createElement(rowset);
    $attackrs = $killrow->appendChild($attackrs);
    
      //Add Atributes to rowset
    $attackrs->setAttribute('name', 'attackers');
    $attackrs->setAttribute('columns', 'characterID,characterName,corporationID,corporationName,allianceID,allianceName,securityStatus,damageDone,finalBlow,weaponTypeID,shipTypeID');
    
    $attackerlist=$kl->getAttackers($kill['killID']);
    foreach($attackerlist AS $killer)
    {
            //Add Attacker element
            $attacker = $doc->createElement(row);
            $attacker = $attackrs->appendChild($attacker);
            //Add Attributes to Attacker Row
            $attacker->setAttribute('characterID', $killer['characterID']);
            $attacker->setAttribute('characterName', $killer['characterName']);
            $attacker->setAttribute('corporationID', $killer['corporationID']);
            $attacker->setAttribute('corporationName', $killer['corporationName']);
            $attacker->setAttribute('allianceID', $killer['allianceID']);
            $attacker->setAttribute('allianceName', $killer['allianceName']);
            
            
    }
    

    //Add Item rowset element
    $items = $doc->createElement(rowset);
    $items = $killrow->appendChild($items);
    
    
    //Add Item to rowset
    $items->setAttribute('name', 'items');
    $items->setAttribute('columns', 'typeID,flag,qtyDropped,qtyDestroyed');
    
    $itemslist=$kl->getItems($kill['killID']);
    //echo"<pre>";print_r($itemslist);echo"</pre></br><hr>";
    
    foreach($itemslist as $kitem) {
            //Add Item element
            $item = $doc->createElement(row);
            $item = $items->appendChild($item);
            
            //Add Attributes to Item Row
            $item->setAttribute('typeID', $kitem['typeID']);
            $item->setAttribute('lft', $kitem['lft']);
            $item->setAttribute('rgt', $kitem['rgt']);
            $item->setAttribute('typeName', $kitem['typeName']);
    }
    
}
$xml_string = $doc->saveXML();
echo $xml_string;



/*
if ($_GET['gz'])
{
    echo gzdeflate($html, 9);
}
else
{
    echo $html;
}*/
?>
