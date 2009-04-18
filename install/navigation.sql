-- phpMyAdmin SQL Dump
-- version 3.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Mar 04, 2009 at 06:50 AM
-- Server version: 5.0.67
-- PHP Version: 5.2.6-2ubuntu4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `cynetent_kks`
--

-- --------------------------------------------------------

--
-- Table structure for table `navigation`
--

CREATE TABLE IF NOT EXISTS `navigation` (
  `ID` int(11) NOT NULL auto_increment,
  `descr` tinytext NOT NULL,
  `url` tinytext NOT NULL,
  `target` char(10) NOT NULL,
  `posnr` int(11) NOT NULL,
  `hidden` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=22 ;

--
-- Dumping data for table `navigation`
--

INSERT INTO `navigation` (`ID`, `descr`, `url`, `target`, `posnr`, `hidden`) VALUES
(21, 'About', '?v=about', '_self', 10, 0),
(20, 'Admin', '?v=admin', '_self', 9, 0),
(19, 'Search', '?v=search', '_self', 8, 0),
(18, 'Stats', '?v=corp_detail&crp_id=147823541', '_self', 7, 0),
(17, 'Post Mail', '?v=post', '_self', 6, 0),
(15, 'Kills', '?v=kills', '_self', 4, 0),
(16, 'Losses', '?v=losses', '_self', 5, 0),
(14, 'Contracts', '?v=contracts', '_self', 3, 0),
(13, 'Campaigns', '?v=campaigns', '_self', 2, 0),
(12, 'Home', '?v=home', '_self', 1, 0);
