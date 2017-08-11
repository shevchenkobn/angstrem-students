-- MySQL dump 10.13  Distrib 5.6.29-76.2, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: students
-- ------------------------------------------------------
-- Server version	5.6.29-76.2

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
-- Table structure for table `contracts_info`
--

DROP TABLE IF EXISTS `contracts_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contracts_info` (
  `contract_number` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `balance` decimal(7,2) unsigned zerofill NOT NULL,
  `conclusion_date` date NOT NULL,
  `activation_date` date NOT NULL,
  `deactivation_date` date NOT NULL,
  PRIMARY KEY (`contract_number`),
  FULLTEXT KEY `contract_number` (`contract_number`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contracts_info`
--

LOCK TABLES `contracts_info` WRITE;
/*!40000 ALTER TABLE `contracts_info` DISABLE KEYS */;
INSERT INTO `contracts_info` VALUES ('1',00000.00,'2017-05-26','2017-06-26','0000-00-00'),('2',00000.00,'2017-05-27','2017-06-27','0000-00-00'),('3',00030.00,'2017-05-28','2017-06-28','0000-00-00'),('4',00050.00,'2017-05-29','2017-06-29','0000-00-00'),('5',00010.00,'2017-05-30','2017-06-30','0000-00-00'),('6',00005.00,'2017-05-31','2017-07-01','0000-00-00'),('7',00000.00,'2017-06-01','2017-07-02','0000-00-00'),('8',00000.00,'2017-06-02','2017-07-03','0000-00-00'),('9',00000.00,'2017-06-03','2017-07-04','0000-00-00'),('10',00002.00,'2017-06-04','2017-07-05','0000-00-00');
/*!40000 ALTER TABLE `contracts_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parents`
--

DROP TABLE IF EXISTS `parents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parents` (
  `contract_number` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `mother_fullname` varchar(90) COLLATE utf8_unicode_ci NOT NULL,
  `mother_email` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `mother_phone` decimal(15,0) NOT NULL,
  `father_fullname` varchar(90) COLLATE utf8_unicode_ci NOT NULL,
  `father_email` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `father_phone` decimal(15,0) NOT NULL,
  `postal_office` int(4) NOT NULL,
  PRIMARY KEY (`contract_number`),
  FULLTEXT KEY `contract_number` (`contract_number`),
  FULLTEXT KEY `mother_fullname` (`mother_fullname`),
  FULLTEXT KEY `mother_email` (`mother_email`),
  FULLTEXT KEY `father_fullname` (`father_fullname`),
  FULLTEXT KEY `father_email` (`father_email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parents`
--

LOCK TABLES `parents` WRITE;
/*!40000 ALTER TABLE `parents` DISABLE KEYS */;
INSERT INTO `parents` VALUES ('1','Woman','woman1@gmail.com',1111100000,'Man','man1@gmail.com',2222200000,686),('2','Woman','woman2@gmail.com',1111111111,'Man','man2@gmail.com',2222211111,452),('3','Woman','woman3@gmail.com',1111122222,'Man','man3@gmail.com',2222222222,218),('4','Woman','woman4@gmail.com',1111133333,'Man','man4@gmail.com',2222233333,219),('5','Женщина','woman5@gmail.com',1111144444,'Мужик','man5@gmail.com',2222244444,220),('6','Женщина','woman6@gmail.com',1111155555,'Мужик','man6@gmail.com',2222255555,221),('7','Женщина','woman7@gmail.com',1111166666,'Мужик','man7@gmail.com',2222266666,222),('8','Жінка','woman8@gmail.com',1111177777,'Батя','man8@gmail.com',2222277777,223),('9','Жінка','woman9@gmail.com',1111188888,'Батя','man9@gmail.com',2222288888,224),('10','Жінка','woman10@gmail.com',1111199999,'Батя','man10@gmail.com',2222299999,225);
/*!40000 ALTER TABLE `parents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `payment_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `contract_number` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `payment_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `start_period` date NOT NULL,
  `end_period` date NOT NULL,
  PRIMARY KEY (`payment_id`),
  FULLTEXT KEY `contract_number` (`contract_number`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (9,'1','2017-07-15 14:25:39','2017-05-29','2017-06-29'),(8,'1','2017-07-15 14:25:39','2017-05-28','2017-06-28'),(7,'1','2017-07-15 14:25:39','2017-05-27','2017-06-27'),(6,'1','2017-07-15 14:25:39','2017-05-26','2017-06-26'),(10,'1','2017-07-15 14:25:39','2017-05-30','2017-06-30'),(11,'2','2017-07-15 14:25:39','2017-05-31','2017-07-01'),(12,'2','2017-07-15 14:25:39','2017-06-01','2017-07-02'),(13,'2','2017-07-15 14:25:39','2017-06-02','2017-07-03'),(14,'2','2017-07-15 14:25:39','2017-06-03','2017-07-04'),(15,'2','2017-07-15 14:25:39','2017-06-04','2017-07-05'),(16,'2','2017-07-15 14:25:39','2017-06-05','2017-07-06'),(17,'2','2017-07-15 14:25:39','2017-06-06','2017-07-07'),(18,'3','2017-07-15 14:25:39','2017-06-07','2017-07-08'),(19,'3','2017-07-15 14:25:39','2017-06-08','2017-07-09'),(20,'3','2017-07-15 14:25:39','2017-06-09','2017-07-10'),(21,'3','2017-07-15 14:25:39','2017-06-10','2017-07-11');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staff_history`
--

DROP TABLE IF EXISTS `staff_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `staff_history` (
  `id` int(3) NOT NULL,
  `query_string` text COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff_history`
--

LOCK TABLES `staff_history` WRITE;
/*!40000 ALTER TABLE `staff_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `staff_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staff_users`
--

DROP TABLE IF EXISTS `staff_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `staff_users` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `email` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `fullname` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fullname` (`fullname`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff_users`
--

LOCK TABLES `staff_users` WRITE;
/*!40000 ALTER TABLE `staff_users` DISABLE KEYS */;
INSERT INTO `staff_users` VALUES (1,'Ha_Ha@meta.ua','$2y$10$l/TdCCwx/LmrtiHrlVOUMO04iIjc58cD3ursMHAAxykIcEElgOBOO','test');
/*!40000 ALTER TABLE `staff_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `students` (
  `contract_number` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `second_name` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `surname` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `form_number` enum('1','2','3','4','5','6','7','8','9','10','11','12') COLLATE utf8_unicode_ci NOT NULL,
  `form_letter` enum('А','Б','В','Г','Д') COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`contract_number`),
  FULLTEXT KEY `name` (`name`),
  FULLTEXT KEY `second_name` (`second_name`),
  FULLTEXT KEY `surname` (`surname`),
  FULLTEXT KEY `contract_number` (`contract_number`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `students`
--

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;
INSERT INTO `students` VALUES ('1','Григорий','Витальевич','Мельник','5','А'),('2','Богдан','Владимирович','Субота','5','А'),('3','Лика','Артемовна','Афян','5','Б'),('4','Анна','Васильевна','Гордийчук','5','Б'),('5','Лана','Кареновна','Симонян','5','А'),('6','Ксения','Вячеславовна','Килимчук','6','А'),('7','Диана','Маликовна','Мухарамова ','6','А'),('8','Илья','Александрович','Анисимов','6','А'),('9','Даниил','Денисович','Белодед','6','Б'),('10','Серафим','Сергеевич','Бикреев','6','Б');
/*!40000 ALTER TABLE `students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `students_info`
--

DROP TABLE IF EXISTS `students_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `students_info` (
  `contract_number` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `medical_features` text COLLATE utf8_unicode_ci NOT NULL,
  `psychological_features` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`contract_number`),
  FULLTEXT KEY `medical_features` (`medical_features`),
  FULLTEXT KEY `psycological_features` (`psychological_features`),
  FULLTEXT KEY `contract_number` (`contract_number`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `students_info`
--

LOCK TABLES `students_info` WRITE;
/*!40000 ALTER TABLE `students_info` DISABLE KEYS */;
INSERT INTO `students_info` VALUES ('1','\"blah blah blah',' Mr. Freeman\"'),('2','\"blah blah blah',' Mr. Freeman\"'),('3','\"blah blah blah',' Mr. Freeman\"'),('4','\"blah blah blah',' Mr. Freeman\"'),('5','\"blah blah blah',' Mr. Freeman\"'),('6','\"blah blah blah',' Mr. Freeman\"'),('7','\"blah blah blah',' Mr. Freeman\"'),('8','\"blah blah blah',' Mr. Freeman\"'),('9','\"blah blah blah',' Mr. Freeman\"'),('10','\"blah blah blah',' Mr. Freeman\"');
/*!40000 ALTER TABLE `students_info` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-07-23 16:12:42
