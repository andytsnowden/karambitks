#!/usr/bin/php
<?php
PHP_EOL;
echo '<pre>';
/**
 * EVE MultiPurpose Application
 *
 * empa.php - Cronjob handler
 *
 * PHP version 5
 *
 * LICENSE: This file is part of EVE MultiPurpose Application also know as KKS.
 * KKS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3 of the License.
 *
 * KKS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with KKS.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author     Ben Cole <wengole@gmail.com>
 * @author     Claus Pedersen <satissis@gmail.com>
 * @author     Michael Cummings <dragonrun1@gmail.com>
 * @author     Stephen Gulick <stephenmg12@gmail.com>
 * @copyright  2008-2009 (C) Ben Cole, Claus Pedersen, Stephen Gulick, and Michael Cummings 
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @package    KKS
 * @subpackage EMPA
 * @version    SVN: $Id$ (Only to be used in key files)
 * @link       http://code.google.com/p/empa/
 * @link       http://www.eve-online.com/
 */

/*
 * Turn off execution time limit
 */
set_time_limit(0);
/*
 * Set short DIRECTORY_SEPARATOR value
 */
$ds = DIRECTORY_SEPARATOR;
/*
 * Set what path this file is in and change the dir to that location.
 */
$baseDir = realpath(dirname(__FILE__));
chdir($baseDir);
/*
 * Run KKS backend.
 */
$text = <<<BODY
-----------------------------------------------------------
-- Starting up KKS backend handler
-----------------------------------------------------------
BODY;
print $text . PHP_EOL;

/**
 * @ignore
 */
require_once($baseDir.$ds.'inc'.$ds.'common_backend.php');
/**
 * @ignore
 */
require_once(KKS_INC.'function.php');
/**
 * @ignore
 */
//require_once(KKS_INC.'backend.php');

/*
 * Set KKS logging level to 0 so we don't catch Yapeal errors in KKS logs.
 */
//$KKS_LOG_LEVEL = 0;
$con->close();
/*
 * Run Yapeal backend.
 */
$text = <<<BODY
-----------------------------------------------------------
-- Starting up Yapeal
-----------------------------------------------------------
BODY;
print PHP_EOL . PHP_EOL . $text . PHP_EOL;

//require_once(KKS_EXT.'yaplea'.$ds.'feed.php');

require_once(KKS_EXT.'yapeal'.$ds.'yapeal.php');

echo '</pre>';
?>