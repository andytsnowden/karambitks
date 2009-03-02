-- phpMyAdmin SQL Dump
-- version 2.11.9.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 02, 2009 at 01:27 PM
-- Server version: 5.0.67
-- PHP Version: 5.2.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `evecorpo_rfkdev`
--

-- --------------------------------------------------------

--
-- Table structure for table `invShipclass`
--

CREATE TABLE IF NOT EXISTS `invShipclass` (
  `groupID` bigint(20) NOT NULL,
  `groupName` varchar(255) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`groupID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `invShipclass`
--

INSERT INTO `invShipclass` (`groupID`, `groupName`) VALUES
(25, 'Frigate'),
(26, 'Cruiser'),
(27, 'Battleship'),
(28, 'Industrial'),
(29, 'Capsule'),
(30, 'Titan'),
(31, 'Shuttle'),
(237, 'Rookie ship'),
(324, 'Assault Ship'),
(358, 'Heavy Assault Ship'),
(380, 'Transport Ship'),
(381, 'Elite Battleship'),
(419, 'Battlecruiser'),
(420, 'Destroyer'),
(463, 'Mining Barge'),
(485, 'Dreadnought'),
(513, 'Freighter'),
(540, 'Command Ship'),
(541, 'Interdictor'),
(543, 'Exhumer'),
(547, 'Carrier'),
(659, 'Mothership'),
(830, 'Covert Ops'),
(831, 'Interceptor'),
(832, 'Logistics'),
(833, 'Force Recon Ship'),
(834, 'Stealth Bomber'),
(883, 'Capital Industrial Ship'),
(893, 'Electronic Attack Ship'),
(894, 'Heavy Interdictor'),
(898, 'Black Ops'),
(900, 'Marauder'),
(902, 'Jump Freighter'),
(906, 'Combat Recon Ship');
