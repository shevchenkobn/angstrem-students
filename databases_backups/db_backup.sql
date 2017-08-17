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
  `paid_sum` decimal(7,2) unsigned zerofill NOT NULL,
  `order_number` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `order_date` date NOT NULL,
  `learning_start` date NOT NULL,
  `learning_end` date NOT NULL,
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
INSERT INTO `contracts_info` VALUES ('1',01950.00,'56','2017-05-29','2017-09-01','2018-05-31','2017-08-31','2017-08-31','0000-00-00'),('2',01950.00,'57','2017-05-29','2017-09-01','2018-05-31','2017-08-30','2017-08-30','0000-00-00'),('3',01950.00,'58','2017-05-30','2017-09-01','2018-05-31','2017-08-29','2017-08-29','0000-00-00'),('4',01950.00,'59','2017-05-30','2017-09-01','2018-05-31','2017-08-28','2017-08-28','0000-00-00'),('5',01965.00,'60','2017-05-31','2017-09-01','2018-05-31','2017-08-27','2017-08-27','0000-00-00'),('6',01980.00,'61','2017-05-31','2017-09-01','2018-05-31','2017-08-26','2017-08-26','0000-00-00'),('7',01995.00,'62','2017-06-01','2017-09-01','2018-05-31','2017-08-25','2017-08-25','0000-00-00'),('8',02010.00,'63','2017-06-01','2017-09-01','2018-05-31','2017-08-24','2017-08-24','0000-00-00'),('9',02025.00,'64','2017-06-02','2017-09-01','2018-05-31','2017-08-23','2017-08-23','0000-00-00'),('10',02040.00,'65','2017-06-02','2017-09-01','2018-05-31','2017-08-22','2017-08-22','0000-00-00');
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
  `status` enum('Мать','Отец','Брат','Сестра','Бабушка','Дедушка','Дядя','Тетя','Опекун') COLLATE utf8_unicode_ci NOT NULL,
  `surname` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `second_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `passport_series` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `passport_number` int(6) NOT NULL,
  `passport_issue_address` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `actual_address` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `registration_address` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `email1` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `email2` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `email3` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `phone1` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `phone2` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `phone3` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `postal_office` int(6) NOT NULL,
  `postal_office_region` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  FULLTEXT KEY `contract_number` (`contract_number`),
  FULLTEXT KEY `name` (`name`),
  FULLTEXT KEY `second_name` (`second_name`),
  FULLTEXT KEY `surname` (`surname`),
  FULLTEXT KEY `email1` (`email1`),
  FULLTEXT KEY `email2` (`email2`),
  FULLTEXT KEY `email3` (`email3`),
  FULLTEXT KEY `phone1` (`phone1`),
  FULLTEXT KEY `phone2` (`phone2`),
  FULLTEXT KEY `phone3` (`phone3`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parents`
--

LOCK TABLES `parents` WRITE;
/*!40000 ALTER TABLE `parents` DISABLE KEYS */;
INSERT INTO `parents` VALUES ('1','Мать','Мельник','Woman','Woman','АА',123456,'some_random_address1','actual_address1','registration_address1','woman1@mail.com','','','1234567891','','',123,'region1'),('2','Мать','Субота','Woman','Woman','АБ',123457,'some_random_address2','actual_address2','registration_address2','woman2@mail.com','','','1234567892','','',124,'region2'),('3','Мать','Афян','Woman','Woman','АВ',123458,'some_random_address3','actual_address3','registration_address3','woman3@mail.com','','','1234567893','','',125,'region3'),('4','Мать','Гордийчук','Woman','Woman','АГ',123459,'some_random_address4','actual_address4','registration_address4','woman4@mail.com','','','1234567894','','',126,'region4'),('5','Мать','Симонян','Жінка','Жінка','АА',123460,'some_random_address5','actual_address5','registration_address5','woman5@mail.com','','','1234567895','','',127,'region5'),('6','Мать','Килимчук','Жінка','Жінка','АБ',123461,'some_random_address6','actual_address6','registration_address6','woman6@mail.com','','','1234567896','','',128,'region6'),('7','Мать','Мухарамова','Жінка','Жінка','АВ',123462,'some_random_address7','actual_address7','registration_address7','woman7@mail.com','','','1234567897','','',129,'region7'),('8','Мать','Анисимов','Женщина','Женщина','АГ',123463,'some_random_address8','actual_address8','registration_address8','woman8@mail.com','','','1234567898','','',130,'region8'),('9','Мать','Белодед','Женщина','Женщина','АА',123464,'some_random_address9','actual_address9','registration_address9','woman9@mail.com','','','1234567899','','',131,'region9'),('10','Мать','Бикреев','Женщина','Женщина','АБ',123465,'some_random_address10','actual_address10','registration_address10','woman10@mail.com','','','1234567900','','',132,'region10'),('1','Отец','Мельник','Man','Man','АВ',123466,'some_random_address11','actual_address11','registration_address11','man1@mail.com','','','1234567901','','',133,'region11'),('2','Отец','Субота','Man','Man','АГ',123467,'some_random_address12','actual_address12','registration_address12','man2@mail.com','','','1234567902','','',134,'region12'),('3','Отец','Афян','Man','Man','АА',123468,'some_random_address13','actual_address13','registration_address13','man3@mail.com','','','1234567903','','',135,'region13'),('4','Отец','Гордийчук','Man','Man','АБ',123469,'some_random_address14','actual_address14','registration_address14','man4@mail.com','','','1234567904','','',136,'region14'),('5','Отец','Симонян','Чоловік','Чоловік','АВ',123470,'some_random_address15','actual_address15','registration_address15','man5@mail.com','','','1234567905','','',137,'region15'),('6','Отец','Килимчук','Чоловік','Чоловік','АГ',123471,'some_random_address16','actual_address16','registration_address16','man6@mail.com','','','1234567906','','',138,'region16'),('7','Отец','Мухарамова','Чоловік','Чоловік','АА',123472,'some_random_address17','actual_address17','registration_address17','man7@mail.com','','','1234567907','','',139,'region17'),('8','Отец','Анисимов','Мужик','Мужик','АБ',123473,'some_random_address18','actual_address18','registration_address18','man8@mail.com','','','1234567908','','',140,'region18'),('9','Отец','Белодед','Мужик','Мужик','АВ',123474,'some_random_address19','actual_address19','registration_address19','man9@mail.com','','','1234567909','','',141,'region19'),('10','Отец','Бикреев','Мужик','Мужик','АГ',123475,'some_random_address20','actual_address20','registration_address20','man10@mail.com','','','1234567910','','',142,'region20');
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
  `payment_system` enum('УкрСиббанк','ПриватБанк','LiqPay') COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`payment_id`),
  FULLTEXT KEY `contract_number` (`contract_number`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (9,'1','2017-07-15 14:25:39','2017-05-29','2017-06-29','УкрСиббанк'),(8,'1','2017-07-15 14:25:39','2017-05-28','2017-06-28','УкрСиббанк'),(7,'1','2017-07-15 14:25:39','2017-05-27','2017-06-27','УкрСиббанк'),(6,'1','2017-07-15 14:25:39','2017-05-26','2017-06-26','УкрСиббанк'),(10,'1','2017-07-15 14:25:39','2017-05-30','2017-06-30','УкрСиббанк'),(11,'2','2017-07-15 14:25:39','2017-05-31','2017-07-01','УкрСиббанк'),(12,'2','2017-07-15 14:25:39','2017-06-01','2017-07-02','УкрСиббанк'),(13,'2','2017-07-15 14:25:39','2017-06-02','2017-07-03','УкрСиббанк'),(14,'2','2017-07-15 14:25:39','2017-06-03','2017-07-04','УкрСиббанк'),(15,'2','2017-07-15 14:25:39','2017-06-04','2017-07-05','УкрСиббанк'),(16,'2','2017-07-15 14:25:39','2017-06-05','2017-07-06','УкрСиббанк'),(17,'2','2017-07-15 14:25:39','2017-06-06','2017-07-07','УкрСиббанк'),(18,'3','2017-07-15 14:25:39','2017-06-07','2017-07-08','УкрСиббанк'),(19,'3','2017-07-15 14:25:39','2017-06-08','2017-07-09','УкрСиббанк'),(20,'3','2017-07-15 14:25:39','2017-06-09','2017-07-10','УкрСиббанк'),(21,'3','2017-07-15 14:25:39','2017-06-10','2017-07-11','УкрСиббанк');
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
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `second_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `surname` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `form_number` enum('1','2','3','4','5','6','7','8','9','10','11','12') COLLATE utf8_unicode_ci NOT NULL,
  `form_letter` enum('А','Б','В','Г','Д') COLLATE utf8_unicode_ci NOT NULL,
  `birthday` date NOT NULL,
  `actual_address` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `registration_address` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
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
INSERT INTO `students` VALUES ('1','Григорий','Витальевич','Мельник','5','А','2006-06-05','actual_address1','registration_address1'),('2','Богдан','Владимирович','Субота','5','А','2006-06-06','actual_address2','registration_address2'),('3','Лика','Артемовна','Афян','5','Б','2006-06-07','actual_address3','registration_address3'),('4','Анна','Васильевна','Гордийчук','5','Б','2006-06-08','actual_address4','registration_address4'),('5','Лана','Кареновна','Симонян','5','А','2006-06-09','actual_address5','registration_address5'),('6','Ксения','Вячеславовна','Килимчук','6','А','2006-06-10','actual_address6','registration_address6'),('7','Диана','Маликовна','Мухарамова ','6','А','2006-06-11','actual_address7','registration_address7'),('8','Илья','Александрович','Анисимов','6','А','2006-06-12','actual_address8','registration_address8'),('9','Даниил','Денисович','Белодед','6','Б','2006-06-13','actual_address9','registration_address9'),('10','Серафим','Сергеевич','Бикреев','6','Б','2006-06-14','actual_address10','registration_address10');
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

-- Dump completed on 2017-08-13 14:20:32
