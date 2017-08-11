DROP TABLE IF EXISTS 'parents';
CREATE TABLE `parents` (
  `contract_number` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `status` enum('Мать','Отец','Брат','Сестра','Бабушка','Дедушка','Дядя','Тетя','Опекун') COLLATE utf8_unicode_ci NOT NULL,
  `surname` varchar(90) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(90) COLLATE utf8_unicode_ci NOT NULL,
  `second_name` varchar(90) COLLATE utf8_unicode_ci NOT NULL,
  `passport_series` VARCHAR(2) COLLATE utf8_unicode_ci NOT NULL,
  `passport_number` int(6) COLLATE utf8_unicode_ci NOT NULL,
  `passport_issue_address` VARCHAR(256) COLLATE utf8_unicode_ci NOT NULL,
  `actual_address` VARCHAR(256) COLLATE utf8_unicode_ci NOT NULL,
  `registration_address` VARCHAR(256) COLLATE utf8_unicode_ci NOT NULL,
  `email1` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `email2` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `email3` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `phone1` int(15) NOT NULL,
  `phone2` int(15) NOT NULL,
  `phone3` int(15) NOT NULL,
  `postal_office_region` VARCHAR (8) NOT NULL,
  `postal_office` int(4) NOT NULL,
  FULLTEXT KEY `contract_number` (`contract_number`),
  FULLTEXT KEY `parent_fullname` (`parent_fullname`),
  FULLTEXT KEY `parent_email1` (`parent_email1`),
  FULLTEXT KEY `parent_email2` (`parent_email1`),
  FULLTEXT KEY `parent_email3` (`parent_email1`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `contracts_info` (
  `contract_number` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `paid_sum` decimal(7,2) unsigned zerofill NOT NULL,
  `order_number` VARCHAR(11) COLLATE utf8_unicode_ci NOT NULL,
  `order_date` date NOT NULL,
  `learning_start` date NOT NULL,
  `learning_end` date NOT NULL,
  `conclusion_date` date NOT NULL,
  `activation_date` date NOT NULL,
  `deactivation_date` date NOT NULL,
  PRIMARY KEY (`contract_number`),
  FULLTEXT KEY `contract_number` (`contract_number`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `students` ADD COLUMN `birthday` date NOT NULL,
  ADD COLUMN `actual_address` VARCHAR(256) COLLATE utf8_unicode_ci NOT NULL,
  ADD COLUMN `registration_address` VARCHAR(256) COLLATE utf8_unicode_ci NOT NULL;

ALTER TABLE `payments` ADD COLUMN `payment_system` enum('Ukrsib Bank','Privat Bank','LiqPay') COLLATE utf8_unicode_ci NOT NULL;





