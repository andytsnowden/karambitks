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
//Get Daily Kills Class
require_once KKS_CLASS.'class.dailykills.php';
require_once KKS_CLASS.'class.dailyloss.php';
//Get Charting
require_once KKS_EXT.'pChart/pChart.class.php';
require_once KKS_EXT.'pChart/pData.class.php';

//Check to see if Week and Year are set
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

//Check to see if ID and Type are set
if(isset($_GET['ID']) && isset($_GET['type']))
{
    $type=$_GET['ID'];
    $ID=$_GET['type'];
} else {
    $type=$kkskb['type'];
    $ID=$kkskb['ID'];
}
//New Daily Kills
$dk= New dailyKills();
$dl= New dailyLoss();
/**
* Set Daily Kill Class Week and Year
*/
    $dk->week=$week;
    $dk->year=$year;
    $dl->week=$week;
    $dl->year=$year;
/**
 * Set Type Options
 */
switch ($kkskb['type']){ 
	case 'corp':
        /**
         * Corp Kills
         */
        $dk->fetchCorp=TRUE;
        $dk->corpID=$ID;
        /**
         * Corp Losses
         */
        $dl->fetchCorp=TRUE;
        $dl->corpID=$ID;	
	break;

	case 'alliance':
        /**
         * Alliance Kills
         */
        $dk->fetchAlliance=TRUE;
        $dk->allianceID=$ID;
        /**
         * Alliance Losses
         */
        $dl->fetchAlliance=TRUE;
        $dl->allianceID=$ID;
	break;

	case 'faction':
        /**
         * Faction Kills
         */
        $dk->fetchFaction=TRUE;
        $dk->factionID=$ID;
        /**
         * Faction Losses
         */
        $dk->fetchFaction=TRUE;
        $dk->factionID=$ID;
	break;
}
/**
 * Fetch Kills and Loss list
 */
$dk->fetchList();
$dl->fetchList();
/**
 * Format Data
 */
 $dailykills=$dk->formatArray();
 $dailyloss=$dl->formatArray();

//Build Chart
  // Dataset definition   
  $DataSet = new pData;  
  $DataSet->AddPoint($dailykills,"Serie1");  
  $DataSet->AddPoint($dailyloss,"Serie2");
  $DataSet->AddAllSeries();  
  $DataSet->SetAbsciseLabelSerie();  
  $DataSet->SetSerieName("Kills","Serie1");  
  $DataSet->SetSerieName("Losses","Serie2");
  
  // Initialise the graph  
  $Graph = new pChart(745,230);
  $Graph->drawGraphAreaGradient(132,153,172,50,TARGET_BACKGROUND);
  $Graph->setFontProperties(KKS_EXT."pChart/Fonts/tahoma.ttf",8);  
  $Graph->setGraphArea(50,30,720,200);  
  $Graph->drawFilledRoundedRectangle(7,7,727,223,5,240,240,240);  
  $Graph->drawRoundedRectangle(5,5,730,225,5,230,230,230);  
  $Graph->drawGraphArea(255,255,255,TRUE);  
  $Graph->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2,TRUE); 
  $Graph->drawGraphAreaGradient(162,183,202,50); 
  $Graph->drawGrid(4,TRUE,230,230,230,50);
  
  // Draw the 0 line  
  $Graph->setFontProperties(KKS_EXT."pChart/Fonts/tahoma.ttf",6);  
  $Graph->drawTreshold(0,143,55,72,TRUE,TRUE);     

  // Draw the bar graph  
  $Graph->drawBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),TRUE);
  
   // Finish the graph  
  $Graph->setFontProperties(KKS_EXT."pChart/Fonts/tahoma.ttf",8);  
  $Graph->drawLegend(500,50,$DataSet->GetDataDescription(), 255, 255, 255, -1,-1,-1, 0,0,0, FALSE);  
  $Graph->setFontProperties(KKS_EXT."pChart/Fonts/tahoma.ttf",10);  
  $Graph->drawTitle(50,22,"Kills/Losses Per Day",50,50,50,585);  
  //$Graph->Render(KKS_CACHE."pGraph/test.png");
  $Graph->Stroke();
  

?>