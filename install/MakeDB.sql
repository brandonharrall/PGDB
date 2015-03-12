-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Mar 10, 2015 at 03:14 AM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `games`
--
CREATE DATABASE IF NOT EXISTS `games` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `games`;

-- --------------------------------------------------------

--
-- Table structure for table `distromethod`
--

CREATE TABLE IF NOT EXISTS `distromethod` (
  `DistroID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'PKey',
  `Name` varchar(30) NOT NULL COMMENT 'Name of the distribution method',
  `DRM` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Whether the method has Digital Rights Management',
  `ImagePath` varchar(120) DEFAULT NULL,
  PRIMARY KEY (`DistroID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Stores details different distribution methods for video games.' AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `system`
--

CREATE TABLE IF NOT EXISTS `system` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` char(30) DEFAULT NULL,
  `Mfg` char(30) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Table structure for table `titles`
--

CREATE TABLE IF NOT EXISTS `titles` (
  `TitleID` int(11) NOT NULL AUTO_INCREMENT,
  `SystemID` int(11) DEFAULT NULL,
  `Title` varchar(255) DEFAULT NULL,
  `Genre` varchar(255) DEFAULT NULL,
  `CoverArt` varchar(255) DEFAULT NULL,
  `Active` tinyint(1) NOT NULL,
  `ReleaseDate` int(4) DEFAULT NULL COMMENT 'Year of Release',
  PRIMARY KEY (`TitleID`),
  KEY `SystemID` (`SystemID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=279 ;

-- --------------------------------------------------------

--
-- Table structure for table `userentries`
--

CREATE TABLE IF NOT EXISTS `userentries` (
  `EntryID` int(11) NOT NULL AUTO_INCREMENT,
  `TitleID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `DistroID` int(11) NOT NULL COMMENT 'FK to game.distromethod.distroID',
  `Progress` int(11) NOT NULL DEFAULT '0',
  `Wanted` tinyint(1) NOT NULL,
  `Acquired` tinyint(1) NOT NULL,
  `Priority` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `Rating` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`EntryID`),
  KEY `DistroID` (`DistroID`),
  KEY `TitleID` (`TitleID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=268 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `UserID` int(11) NOT NULL AUTO_INCREMENT,
  `Role` int(11) NOT NULL,
  `UserName` varchar(30) NOT NULL,
  `Password` varchar(120) NOT NULL,
  `Active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`UserID`),
  UNIQUE KEY `UserName` (`UserName`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `titles`
--
ALTER TABLE `titles`
  ADD CONSTRAINT `titles_ibfk_1` FOREIGN KEY (`SystemID`) REFERENCES `system` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `userentries`
--
ALTER TABLE `userentries`
  ADD CONSTRAINT `userentries_ibfk_1` FOREIGN KEY (`TitleID`) REFERENCES `titles` (`TitleID`) ON DELETE NO ACTION,
  ADD CONSTRAINT `userentries_ibfk_2` FOREIGN KEY (`DistroID`) REFERENCES `distromethod` (`DistroID`) ON DELETE NO ACTION;

--
-- Insert required entries
--
INSERT INTO `distromethod`(`Name`, `DRM`) VALUES ('Other',0)
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
