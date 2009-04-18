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

//Define KB Version 
define('KKS_VERSION', '1.0'); 

//Turn off some possible annoances
@set_magic_quotes_runtime(0);

//LOAD AND SET PATH CONSTANTS
require_once'inc/common_paths.inc';

//LOAD CONFIGURATION AND SET CONSTANTS
require_once KKS_CONFIG.'config.php';

//LOAD ADODB CONNECTION FACTORY
require_once KKS_CLASS . 'ADOdbFactory.class.php';

//LOAD ODD/END FUNCTIONS
require_once KKS_INC . 'functions.inc';

//UNSET GLOBAL VARIBLES IF HOST HAS register_globals ON (SECURITY RISK)
unregister_globals('_POST', '_GET', '_REQUEST');

echo "<h3>ADODB Performance Monitor</h3>\n";
if(KKS_ADODB_LOG==FALSE) {
    echo "Performance monitoring is turned off. Edit your config.php file to turn on.<br>\n<hr>\n";
}
 //Get ADODB Factory INSTANCE
            $instance = ADOdbFactory::getInstance();
            //Get DB Connection
            $con = $instance->factory(KKS_DSN);

$perf = NewPerfMonitor($con);

$perf->ui();
?>