-- PGDB
-- Author: Brandon harrall
-- https://github.com/brandonharrall/PGDB
--
-- MakeDB.sql
-- Run only upon first installation, for updates please login as admin (role=1) and visit the settings page.

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Stores details different distribution methods for video games.';

-- --------------------------------------------------------

--
-- Table structure for table `system`
--

CREATE TABLE IF NOT EXISTS `system` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` char(30) DEFAULT NULL,
  `Mfg` char(30) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `UserID` int(11) NOT NULL AUTO_INCREMENT,
  `Role` int(11) NOT NULL DEFAULT '2',
  `UserName` varchar(30) NOT NULL,
  `Password` varchar(256) NOT NULL,
  `Salt` varchar(120) NOT NULL,
  `Active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`UserID`),
  UNIQUE KEY `UserName` (`UserName`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `globals`
--

CREATE TABLE IF NOT EXISTS `globals` (
  `NAME` varchar(30) NOT NULL,
  `VALUE` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Insert required global variables
--

INSERT INTO `globals` (`NAME`, `VALUE`) VALUES('SCHEMA_VERSION', '1');
INSERT INTO `globals` (`NAME`, `VALUE`) VALUES('REQUIRE_LOGIN', '1');
INSERT INTO `globals` (`NAME`, `VALUE`) VALUES('ALLOW_REGISTRATION', '1');
INSERT INTO `globals` (`NAME`, `VALUE`) VALUES('MINROLE_ADDTITLE', '1');

--
-- Insert required entries
--
INSERT INTO `distromethod`(`Name`, `DRM`) VALUES ('Other',0);
INSERT INTO `system` (`Name`, `Mfg`) VALUES('PC', NULL);

-- --------------------------------------------------------

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
