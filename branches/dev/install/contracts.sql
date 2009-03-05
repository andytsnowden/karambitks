-- phpMyAdmin SQL Dump
-- version 3.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Mar 05, 2009 at 07:39 AM
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
-- Table structure for table `contracts`
--

CREATE TABLE IF NOT EXISTS `contracts` (
  `ctr_id` int(11) NOT NULL auto_increment,
  `ctr_name` char(128) NOT NULL,
  `ctr_campaign` smallint(6) NOT NULL default '0',
  `ctr_started` datetime NOT NULL default '0000-00-00 00:00:00',
  `ctr_ended` datetime default '0000-00-00 00:00:00',
  PRIMARY KEY  (`ctr_id`),
  KEY `ctr_id` (`ctr_id`,`ctr_campaign`,`ctr_ended`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `contracts`
--

INSERT INTO `contracts` (`ctr_id`, `ctr_name`, `ctr_campaign`, `ctr_started`, `ctr_ended`) VALUES
(1, 'Insurgency', 1, '2008-03-01 00:00:00', '2008-06-01 23:59:59'),
(3, 'Centauris is a Cunt (NC Vs GBC)', 1, '2008-09-01 00:00:00', '2008-12-01 23:59:59'),
(4, 'NC Cleanup', 1, '2008-11-24 00:00:00', '2009-01-01 23:59:59');
