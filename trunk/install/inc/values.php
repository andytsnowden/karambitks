<?php
/**
 * EVE MultiPurpose Application
 *
 * EMPA Setup.
 *
 * PHP version 5
 *
 * LICENSE: This file is part of EVE MultiPurpose Application also know as EMPA.
 * EMPA is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3 of the License.
 *
 * EMPA is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with EMPA.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author     Ben Cole <wengole@gmail.com>
 * @author     Claus Pedersen <satissis@gmail.com>
 * @author     Michael Cummings <dragonrun1@gmail.com>
 * @author     Stephen Gulick <stephenmg12@gmail.com>
 * @copyright  2008-2009 (C) Ben Cole, Claus Pedersen, Stephen Gulick, and Michael Cummings
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @package    EMPA
 * @subpackage EMPASetup
 * @version    SVN: $Id$ (Only to be used in key files)
 * @link       http://code.google.com/p/empa/
 * @link       http://www.eve-online.com/
 */
/**
 * @internal Only let this code be included or required not ran directly.
 */
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  exit();
};
/*
 * EMPA Version
 */
$EMPA_Version = 0.0007;
/*
 * Yapeal Version
 */
$Yapeal_Version = 786;
/*
 * This is schema xml files to use in AXMLS + it curently version
 */
$schemas = array(
  'kks' => array('KKStables') ,
  'yapeal' => array('util', 'account', 'char', 'corp', 'eve', 'map', 'server') ,
  'db_dump' => array('invTypes', 'invTypes_Dump', 'mapSolarSystems', 'mapSolarSystems_Dump', 'eveGraphics', 'eveGraphics_Dump')
);
/*
 * Access per Api defined by level.
 *
 * 0 = Guests.
 * 1 = Registered users that is not in the corporation.
 * 2 = Corporation member Api
 * 3 = Corporation Api
 */
$accessPerApi = array(
  0 => array(),
  1 => array('charCharacterSheet'),
  2 => array('charCharacterSheet'),
  3 => array('corpCorporationSheet', 'corpMemberTracking', 'corpKillLog')
);
?>
