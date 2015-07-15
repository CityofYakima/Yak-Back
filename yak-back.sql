# ************************************************************
# Sequel Pro SQL dump
# Version 4096
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 127.0.0.1 (MySQL 5.5.40-0+wheezy1)
# Database: yakimaconnect
# Generation Time: 2015-07-15 23:49:28 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table calendar_table
# ------------------------------------------------------------

DROP TABLE IF EXISTS `calendar_table`;

CREATE TABLE `calendar_table` (
  `dt` date NOT NULL,
  `y` smallint(6) DEFAULT NULL,
  `q` tinyint(4) DEFAULT NULL,
  `m` tinyint(4) DEFAULT NULL,
  `d` tinyint(4) DEFAULT NULL,
  `dw` tinyint(4) DEFAULT NULL,
  `monthName` varchar(9) DEFAULT NULL,
  `dayName` varchar(9) DEFAULT NULL,
  `w` tinyint(4) DEFAULT NULL,
  `isWeekday` binary(1) DEFAULT NULL,
  `isHoliday` binary(1) DEFAULT NULL,
  `holidayDescr` varchar(32) DEFAULT NULL,
  `isPayday` binary(1) DEFAULT NULL,
  PRIMARY KEY (`dt`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table comments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `comments`;

CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comment` text NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `requestID` int(10) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `posted` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table department
# ------------------------------------------------------------

DROP TABLE IF EXISTS `department`;

CREATE TABLE `department` (
  `departmentID` tinyint(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`departmentID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table email
# ------------------------------------------------------------

DROP TABLE IF EXISTS `email`;

CREATE TABLE `email` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `requestID` int(100) NOT NULL,
  `staffID` int(100) NOT NULL,
  `dateSent` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table notes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `notes`;

CREATE TABLE `notes` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `note` text NOT NULL,
  `requestID` int(100) NOT NULL,
  `staffID` int(100) NOT NULL,
  `datePosted` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table requests
# ------------------------------------------------------------

DROP TABLE IF EXISTS `requests`;

CREATE TABLE `requests` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `location` varchar(255) NOT NULL DEFAULT 'NULL',
  `photo` varchar(255) DEFAULT NULL,
  `description` mediumtext NOT NULL,
  `dateOpened` datetime NOT NULL,
  `dateClosed` datetime DEFAULT NULL,
  `status` mediumtext,
  `typeID` tinyint(10) DEFAULT NULL,
  `userID` int(100) DEFAULT NULL,
  `contacted` int(10) DEFAULT NULL,
  `contactedBy` varchar(255) DEFAULT NULL,
  `dateContacted` datetime DEFAULT NULL,
  `closedBy` tinyint(10) DEFAULT NULL,
  `closedType` varchar(255) DEFAULT NULL,
  `dupID` int(100) DEFAULT NULL,
  `latitude` double DEFAULT NULL COMMENT 'WGS84 Y coordinate',
  `longitude` double DEFAULT NULL COMMENT 'WGS84 X coordinate',
  `assignedTo` tinyint(10) DEFAULT NULL,
  `dateAssigned` datetime DEFAULT NULL,
  `assignedBy` int(100) DEFAULT NULL,
  `source` varchar(255) DEFAULT NULL,
  `webID` int(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `typeID` (`typeID`),
  KEY `userID` (`userID`),
  KEY `closedBy` (`closedBy`),
  KEY `assignedTo` (`assignedTo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table requestType
# ------------------------------------------------------------

DROP TABLE IF EXISTS `requestType`;

CREATE TABLE `requestType` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL DEFAULT 'NULL',
  `order` int(10) NOT NULL,
  `source` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table staff
# ------------------------------------------------------------

DROP TABLE IF EXISTS `staff`;

CREATE TABLE `staff` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `departmentID` tinyint(4) DEFAULT NULL,
  `inactive` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `departmentID` (`departmentID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table type-department
# ------------------------------------------------------------

DROP TABLE IF EXISTS `type-department`;

CREATE TABLE `type-department` (
  `requesttypeID` int(10) NOT NULL,
  `departmentID` tinyint(10) NOT NULL,
  KEY `requesttypeID` (`requesttypeID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table webFormUser
# ------------------------------------------------------------

DROP TABLE IF EXISTS `webFormUser`;

CREATE TABLE `webFormUser` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `zip` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(14) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
