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
 * @copyright  2008 (C) Michael Cummings, Stephen Gulick, and Andy Snowden 
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @package    KarambitKS
 * @version    SVN: $Id$
 * @link       http://code.google.com/p/karambitks/
 * @link       http://www.eve-online.com/
 */
 
 /*
 //Load configuration File
 require_once 'config/config.php';
 //Load ADODB
 require_once 'ext/adodb5/adodb.inc.php';
 //Load Dwoo
 require_once 'ext/dwoo/dwooAutoload.php';
 */

require_once'inc/common_paths.inc';

echo KARAMBITKS_ADODB;

 //Find out what page the user wants to view
 $view=$_GET['v'];
 if(ctype_alpha($view)) {
	 $loadPage='view/'.$view.'.php';
	 if(file_exists($loadPage)) {
	 	include($loadPage);
	 }
 }
 else {
 	echo "An Error has occured!";
 }


?>