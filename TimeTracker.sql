CREATE DATABASE  IF NOT EXISTS `timetracker` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `timetracker`;
-- MySQL dump 10.13  Distrib 5.5.16, for Win32 (x86)
--
-- Host: 192.168.254.128    Database: timetracker
-- ------------------------------------------------------
-- Server version	5.1.66-0+squeeze1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `page` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groups`
--

LOCK TABLES `groups` WRITE;
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;
INSERT INTO `groups` VALUES (1,'Helpdesk',3);
/*!40000 ALTER TABLE `groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `groupusers`
--

DROP TABLE IF EXISTS `groupusers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groupusers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `groupid` int(11) NOT NULL,
  `privilege` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groupusers`
--

LOCK TABLES `groupusers` WRITE;
/*!40000 ALTER TABLE `groupusers` DISABLE KEYS */;
INSERT INTO `groupusers` VALUES (2,2,1,3),(3,4,1,0),(4,6,1,0),(5,7,1,0),(6,8,1,0),(7,3,1,0),(8,10,1,0),(9,11,1,0),(10,12,1,0),(11,13,1,0),(12,14,1,0),(13,15,1,0),(14,16,1,3),(15,17,1,3),(16,18,1,3);
/*!40000 ALTER TABLE `groupusers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page` varchar(32) NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
INSERT INTO `pages` VALUES (1,'home','%3Cdiv+style%3D%27text-align%3Acenter%3B%27%3E%0A%3Ch3%3EWelcome%3C%2Fh3%3E%0A%3Cp%3EPlease+sign+in.%3C%2Fp%3E%0A%3C%2Fdiv%3E'),(3,'group','%3Cdiv+style%3D%27text-align%3Acenter%3B%27%3E%0D%0A%3Ch3%3EGROUP+Helpdesk%21%3C%2Fh3%3E%0D%0A%3Ch4%3EAnnouncements%3A+%3C%2Fh4%3E%0D%0A%3Cp%3ENo+meeting+this+week+%28November+26%2C+2012%29%3C%2Fp%3E%0D%0A%3Ch4%3ELinks%3A%3C%2Fh4%3E%0D%0A%3Cp%3EView+Schedule+at+%3Ca+href%3D%27http%3A%2F%2Fafsws.rpi.edu%2FAFS%2Fdept%2Facs%2Fconsult%2FSchedule.html%27%3Ehttp%3A%2F%2Fafsws.rpi.edu%2FAFS%2Fdept%2Facs%2Fconsult%2FSchedule.html%3C%2Fa%3E%3C%2Fp%3E%0D%0A%3Cp%3EWiki+%3Ca+href%3D%27http%3A%2F%2Fleet.arc.rpi.edu%2Fwiki%27%3Ehttp%3A%2F%2Fleet.arc.rpi.edu%2Fwiki%3C%2Fa%3E%3C%2Fp%3E%0D%0A%3Cp%3EQuickLogs+%3Ca+href%3D%27http%3A%2F%2Fleet.arc.rpi.edu%2Fquicklogs%27%3Ehttp%3A%2F%2Fleet.arc.rpi.edu%2Fquicklogs%3C%2Fa%3E%3C%2Fp%3E%0D%0A%3Cp%3ETickets+%3Ca+href%3D%27http%3A%2F%2Fj2ee7.server.rpi.edu%3A8080%2Fhelpdesk%2Fstylesheets%2Fwelcome.faces%27%3Ehttp%3A%2F%2Fj2ee7.server.rpi.edu%3A8080%2Fhelpdesk%2Fstylesheets%2Fwelcome.faces%3C%2Fa%3E%3C%2Fp%3E%0D%0A%3Cp%3EFacebook+Group+%3Ca+href%3D%27https%3A%2F%2Fwww.facebook.com%2Fgroups%2F277701498964844%2F%27%3Ehttps%3A%2F%2Fwww.facebook.com%2Fgroups%2F277701498964844%2F%3C%2Fa%3E%3C%2Fp%3E%0D%0A%3C%2Fdiv%3E'),(2,'homeAuth','%3Cdiv+style%3D%27text-align%3Acenter%3B%27%3E%0D%0A%3Ch3%3EWelcome%3C%2Fh3%3E%0D%0A%3Cp%3ESelect+a+group+at+the+bottom+right+of+the+page%3C%2Fp%3E%0D%0A%3Cp%3EIf+there+is+a+group+missing+for+you%2C+contact+the+groups+administrator+to+be+added%3C%2Fp%3E%0D%0A%3C%2Fdiv%3E');
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `templates`
--

DROP TABLE IF EXISTS `templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data` text NOT NULL,
  `name` varchar(32) NOT NULL,
  `owner` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `templates`
--

LOCK TABLES `templates` WRITE;
/*!40000 ALTER TABLE `templates` DISABLE KEYS */;
INSERT INTO `templates` VALUES (1,'1231231231231323161346','Windows 2008',2,1),(2,'5_12_0,4_1_2,4_2_2,4_4_0,4_5_0,3_14_2,3_13_0,4_4_2,11_0_2,11_1_2,3_1_2,','Dan',2,1),(3,'11_12_2,11_1_2,1_12_0,1_12_2,1_13_2,1_13_0,1_14_0,1_14_2,1_15_2,1_15_0,','Monday',2,1),(4,'11_12_2,11_1_2,2_12_0,2_12_2,2_13_2,2_13_0,2_14_0,2_14_2,2_15_2,2_15_0,','Tuesday',2,1);
/*!40000 ALTER TABLE `templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `timedata`
--

DROP TABLE IF EXISTS `timedata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `timedata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `startTime` datetime NOT NULL,
  `stopTime` datetime NOT NULL,
  `submitted` datetime NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `timedata`
--

LOCK TABLES `timedata` WRITE;
/*!40000 ALTER TABLE `timedata` DISABLE KEYS */;
INSERT INTO `timedata` VALUES (1,2,'2013-01-06 00:00:00','2013-01-06 00:29:00','2013-01-15 00:59:50',0),(2,2,'2013-01-06 01:00:00','2013-01-06 01:29:00','2013-01-15 00:59:53',0),(3,2,'2013-01-06 02:00:00','2013-01-06 02:29:00','2013-01-15 00:59:52',0),(4,2,'2013-01-06 03:00:00','2013-01-06 03:29:00','2013-01-15 00:58:04',0),(5,2,'2013-01-06 01:30:00','2013-01-06 01:59:00','2013-01-15 00:59:50',0),(6,2,'2013-01-06 03:30:00','2013-01-06 03:59:00','2013-01-15 00:58:07',1),(7,2,'2013-01-06 04:00:00','2013-01-06 04:29:00','2013-01-15 00:58:08',1),(8,2,'2013-01-06 05:30:00','2013-01-06 05:59:00','2013-01-15 00:58:08',1),(9,2,'2013-01-05 05:00:00','2013-01-05 05:29:00','2013-01-15 00:58:09',1),(10,2,'2013-01-05 04:30:00','2013-01-05 04:59:00','2013-01-15 00:58:10',1),(11,2,'2013-01-05 03:30:00','2013-01-05 03:59:00','2013-01-15 00:58:10',1),(12,2,'2013-01-05 02:30:00','2013-01-05 02:59:00','2013-01-15 00:58:11',1),(13,2,'2013-01-05 01:30:00','2013-01-05 01:59:00','2013-01-15 00:58:18',0),(14,2,'2013-01-16 00:30:00','2013-01-16 00:59:00','2013-01-15 01:09:52',1),(15,2,'2013-01-06 06:30:00','2013-01-06 06:59:00','2013-01-15 02:25:53',1),(16,2,'2013-01-06 07:30:00','2013-01-06 07:59:00','2013-01-15 02:25:54',1),(17,2,'2013-01-06 08:30:00','2013-01-06 08:59:00','2013-01-15 02:25:54',1),(18,2,'2013-01-06 09:30:00','2013-01-06 09:59:00','2013-01-15 02:25:55',1),(19,2,'2012-12-23 00:00:00','2012-12-23 00:29:00','2013-01-15 02:28:26',1),(20,2,'2012-12-23 00:30:00','2012-12-23 00:59:00','2013-01-15 02:28:26',1),(21,4,'2013-01-09 02:30:00','2013-01-09 02:59:00','2013-01-15 02:40:59',1),(22,4,'2013-01-09 03:30:00','2013-01-09 03:59:00','2013-01-15 02:40:59',1);
/*!40000 ALTER TABLE `timedata` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(12) NOT NULL,
  `privilege` tinyint(4) NOT NULL,
  `defaultgroup` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (10,'oatman',1,0),(2,'berkod2',2,0),(16,'sheaj',2,0),(4,'ungk2',1,0),(6,'brookj7',1,0),(7,'pateln8',1,0),(8,'daniej3',1,0),(9,'heimj',1,0),(17,'rigby',2,0),(18,'valiqp',2,0);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-01-15  2:54:16
