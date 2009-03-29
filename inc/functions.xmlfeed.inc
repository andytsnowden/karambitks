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

function itemsTreeXML($curLevel, $killID, $node, $dom, $_left, $_right) {
    //Get ADODB Factory INSTANCE
    $instance = ADOdbFactory::getInstance();
    //Get DB Connection
    $con = $instance->factory(KKS_DSN);
    $nextLevel=$curLevel+1;
    
    //SQL to get currentl level items
    $sql = 'SELECT  ci.`lft`, ci.`rgt`, ci.`lvl`, ci.`killID`, ci.`flag`, ci.`qtyDropped`, ci.`qtyDestroyed`, ci.`typeID`, it.typeName FROM `corpItems` ci'
        . ' JOIN invTypes it ON it.`typeID`=ci.`typeID`'
        . ' WHERE `killID`='.$killID.' AND ci.`lft` BETWEEN '.$_left.' AND '.$_right.' AND ci.lvl='.$nextLevel.' ORDER BY `lft`'; 
        
    if($rarray=$con->GetAssoc($sql)) {
        foreach($rarray AS $_item) {
            //Add Item element
            $item = $dom->createElement(row);
            $item = $node->appendChild($item);
            
            //Add Attributes to Item Row
            $item->setAttribute('typeID', $_item['typeID']);
            $item->setAttribute('flag', $_item['flag']);
            $item->setAttribute('qtyDropped', $_item['qtyDropped']);
            $item->setAttribute('qtyDestroyed', $_item['qtyDestroyed']);
            //$item->setAttribute('lft', $_item['lft']);
            //$item->setAttribute('rgt', $_item['rgt']);
            //$item->setAttribute('lvl', $_item['lvl']);
            //$item->setAttribute('typeName', $_item['typeName']);
            
            
            //Check to see if item has children
            if($_item['rgt']-$_item['lft']!=1 && $curLevel<1) {
                //call self recursively
                itemsTreeXML($_item['lvl'], $_item['killID'], $item, $dom, $_item['lft'], $_item['rgt']);
            } // if children
        } //foreach item 
        return true;     
    } // execute sql assoc
    return false;
} //itemsTreeXML

function getRootItem($killID) {
    //Get ADODB Factory INSTANCE
    $instance = ADOdbFactory::getInstance();
    //Get DB Connection
    $con = $instance->factory(KKS_DSN);
    
    //SQL to get root item (the ship)
    $sql = 'SELECT ci.`lft`, ci.`rgt`, ci.`lvl`, ci.`killID`, ci.`flag`, ci.`qtyDropped`, ci.`qtyDestroyed`, ci.`typeID`, it.typeName FROM `corpItems` ci JOIN invTypes it ON it.`typeID`=ci.`typeID` '
        . ' WHERE `killID`=6268346 AND ci.`lft`=1 LIMIT 1'; 
     
    //Get first row (should only be one)   
    return $rarray=$con->GetRow($sql);
}

?>