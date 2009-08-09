<?php
$time_start = microtime();

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

//Define KB Version 
define('KKS_VERSION', '1.0'); 

//Turn off some possible annoances
@set_magic_quotes_runtime(0);

/**
 * LOAD AND SET PATH CONSTANTS 
 * LOAD CONFIGURATION AND SET CONSTANTS
 * LOAD ADODB CONNECTION FACTORY
 * LOAD ERROR Handeling Class
 * LOAD ODD/END FUNCTIONS 
 */
 
require_once'inc/common_paths.php';
require_once KKS_INC.'common_backend.php';
require_once KKS_CLASS . 'ADOdbFactory.class.php';
require_once KKS_CLASS . 'class.errors.php';
require_once KKS_CLASS . 'class.config.php';
require_once KKS_INC . 'functions.php';
require_once KKS_CLASS.'class.kks.php';

//Load config from SQL and some basic config needed for all pages
$config = new kss_config();
$kb_host = $config->get('kb_host');
$theme_url = $config->get('theme_url');
$style_url = $config->get('style_url');
$kb_title = $config->get('kb_title');
$banner = $config->get('banner');

//action example 1=insert 2=update 3=delete return T/F
//$config->action('test_config', '19923', 1);

//set new Error Handlerset_error_handler(array('errors', 'handler'));//LOAD DWOO
require_once KKS_DWOO.'dwooAutoload.php';
require_once KKS_CLASS.'class.DwooTemplateHandler.php';
//Session name
session_name('KKS_SESSION');
//Start Session
session_start();

//Compress output if server can and enabled in config.
if (!empty($compression) && !headers_sent() && ob_get_length() == 0)
{
	// If zlib is being used, turn off output compression.
	if (@ini_get('zlib.output_compression') == '1' || @ini_get('output_handler') == 'ob_gzhandler' || @version_compare(PHP_VERSION, '4.2.0') == -1) {
		ob_start();
	} else {
		ob_start('ob_gzhandler');
	}
} else {	
	ob_start();
}


//UNSET GLOBAL VARIBLES IF HOST HAS register_globals ON (SECURITY RISK)
unregister_globals('_POST', '_GET', '_REQUEST');

 //Find out what page the user wants to view & check for invalid request or empty request
if(isset($_GET['v'])) {
    $view=$_GET['v'];
 } //if is set $_GET['v']
else {
    $view='home'; 
} //else if is set $_GET['v']
 //if the user tries to put anything but valid data in the broswer this check will fail.
 if ((!eregi("^[a-z_./]*$", $view) && !eregi("\\.\\.", $view))) {
 	//invalid data was sent to the header, Possible break/XSS attack
 	//we prob want to log this with the ip the request came from
 	trigger_error('Invalid Link/Page', E_USER_ERROR);
	} else {
		//if its IGB, redirect to igp pages
		if (substr($_SERVER['HTTP_USER_AGENT'], 0, 15) == 'EVE-minibrowser'){
			$loadPage='view/igb.php';
			if(file_exists($loadPage)) {
				include($loadPage);
			}
		
		} else {
			if(ctype_alpha($view) OR !empty($view)) {
				$loadPage=KKS_VIEW.$view.'.php';
				if(file_exists($loadPage)) {
					include($loadPage);
				}
			} elseif (empty($view)){
				include('view/home.php');
			} else {
				trigger_error("An Error has occured!", E_USER_WARNING);
			} 
		}
 }
 


//all output should be finished, send headers to the broswer.
ob_end_flush();
?>