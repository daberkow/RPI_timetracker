-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 21, 2013 at 09:28 PM
-- Server version: 5.5.31
-- PHP Version: 5.4.4-14

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `timetracker`
--
CREATE DATABASE `timetracker` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `timetracker`;

-- --------------------------------------------------------

--
-- Table structure for table `email`
--

CREATE TABLE IF NOT EXISTS `email` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `group` int(11) NOT NULL,
  `setting` tinyint(11) NOT NULL,
  `type` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `email`
--

INSERT INTO `email` (`id`, `user`, `group`, `setting`, `type`) VALUES
(1, 0, 1, 1, 1),
(2, 0, 1, 1, 2),
(5, 2, 1, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `page` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `page`) VALUES
(1, 'Helpdesk', 3);

-- --------------------------------------------------------

--
-- Table structure for table `groupusers`
--

CREATE TABLE IF NOT EXISTS `groupusers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `groupid` int(11) NOT NULL,
  `privilege` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17 ;

--
-- Dumping data for table `groupusers`
--

INSERT INTO `groupusers` (`id`, `userid`, `groupid`, `privilege`) VALUES
(2, 2, 1, 3),
(3, 4, 1, 0),
(4, 6, 1, 0),
(5, 7, 1, 0),
(6, 8, 1, 0),
(7, 3, 1, 0),
(8, 10, 1, 0),
(9, 11, 1, 0),
(10, 12, 1, 0),
(11, 13, 1, 0),
(12, 14, 1, 0),
(13, 15, 1, 0),
(14, 16, 1, 3),
(15, 17, 1, 3),
(16, 18, 1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page` varchar(32) NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `page`, `data`) VALUES
(1, 'home', '%3Cdiv+style%3D%27text-align%3Acenter%3B%27%3E%0A%3Ch3%3EWelcome%3C%2Fh3%3E%0A%3Cp%3EPlease+sign+in.%3C%2Fp%3E%0A%3C%2Fdiv%3E'),
(3, 'group', '%3Cdiv+style%3D%27text-align%3Acenter%3B%27%3E%0D%0A%3Ch3%3EGROUP+Helpdesk%21%3C%2Fh3%3E%0D%0A%3Ch4%3EAnnouncements%3A+%3C%2Fh4%3E%0D%0A%3Cp%3ENo+meeting+this+week+%28November+26%2C+2012%29%3C%2Fp%3E%0D%0A%3Ch4%3ELinks%3A%3C%2Fh4%3E%0D%0A%3Cp%3EView+Schedule+at+%3Ca+href%3D%27http%3A%2F%2Fafsws.rpi.edu%2FAFS%2Fdept%2Facs%2Fconsult%2FSchedule.html%27%3Ehttp%3A%2F%2Fafsws.rpi.edu%2FAFS%2Fdept%2Facs%2Fconsult%2FSchedule.html%3C%2Fa%3E%3C%2Fp%3E%0D%0A%3Cp%3EWiki+%3Ca+href%3D%27http%3A%2F%2Fleet.arc.rpi.edu%2Fwiki%27%3Ehttp%3A%2F%2Fleet.arc.rpi.edu%2Fwiki%3C%2Fa%3E%3C%2Fp%3E%0D%0A%3Cp%3EQuickLogs+%3Ca+href%3D%27http%3A%2F%2Fleet.arc.rpi.edu%2Fquicklogs%27%3Ehttp%3A%2F%2Fleet.arc.rpi.edu%2Fquicklogs%3C%2Fa%3E%3C%2Fp%3E%0D%0A%3Cp%3ETickets+%3Ca+href%3D%27http%3A%2F%2Fj2ee7.server.rpi.edu%3A8080%2Fhelpdesk%2Fstylesheets%2Fwelcome.faces%27%3Ehttp%3A%2F%2Fj2ee7.server.rpi.edu%3A8080%2Fhelpdesk%2Fstylesheets%2Fwelcome.faces%3C%2Fa%3E%3C%2Fp%3E%0D%0A%3Cp%3EFacebook+Group+%3Ca+href%3D%27https%3A%2F%2Fwww.facebook.com%2Fgroups%2F277701498964844%2F%27%3Ehttps%3A%2F%2Fwww.facebook.com%2Fgroups%2F277701498964844%2F%3C%2Fa%3E%3C%2Fp%3E%0D%0A%3C%2Fdiv%3E'),
(2, 'homeAuth', '%3Cdiv+style%3D%27text-align%3Acenter%3B%27%3E%0D%0A%3Ch3%3EWelcome%3C%2Fh3%3E%0D%0A%3Cp%3ESelect+a+group+at+the+bottom+right+of+the+page%3C%2Fp%3E%0D%0A%3Cp%3EIf+there+is+a+group+missing+for+you%2C+contact+the+groups+administrator+to+be+added%3C%2Fp%3E%0D%0A%3C%2Fdiv%3E');

-- --------------------------------------------------------

--
-- Table structure for table `templates`
--

CREATE TABLE IF NOT EXISTS `templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data` text NOT NULL,
  `name` varchar(32) NOT NULL,
  `owner` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `timedata`
--

CREATE TABLE IF NOT EXISTS `timedata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `startTime` datetime NOT NULL,
  `stopTime` datetime NOT NULL,
  `submitted` datetime NOT NULL,
  `group` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fname` varchar(35) NOT NULL,
  `lname` varchar(35) NOT NULL,
  `username` varchar(12) NOT NULL,
  `privilege` tinyint(4) NOT NULL,
  `defaultgroup` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fname`, `lname`, `username`, `privilege`, `defaultgroup`) VALUES
(10, '', '', 'oatman', 1, 0),
(2, '', '', 'berkod2', 2, 0),
(16, '', '', 'sheaj', 2, 0),
(4, '', '', 'ungk2', 1, 0),
(6, '', '', 'brookj7', 1, 0),
(7, '', '', 'pateln8', 1, 0),
(8, '', '', 'daniej3', 1, 0),
(9, '', '', 'heimj', 1, 0),
(17, '', '', 'rigby', 2, 0),
(18, '', '', 'valiqp', 2, 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
