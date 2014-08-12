-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 12, 2014 at 04:23 PM
-- Server version: 5.6.12-log
-- PHP Version: 5.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `sniders2013`
--

-- --------------------------------------------------------

--
-- Table structure for table `t-a-billing`
--

CREATE TABLE IF NOT EXISTS `t-a-billing` (
  `AB-CUSTNO` varchar(5) NOT NULL COMMENT 'Customer Number',
  `AB-USE-DT` datetime NOT NULL COMMENT 'Use Date',
  `AB-TKT` varchar(5) NOT NULL,
  `AB-TKT-SUB` tinyint(1) NOT NULL,
  `AB-BILL-NO` varchar(7) NOT NULL COMMENT 'BILL nUMBER',
  `AB-BILL-DT` datetime NOT NULL,
  `AB-BILL-TYP;` varchar(1) NOT NULL COMMENT 'B=BILL,C=CRED, P=PAYMENTM=MANUAL CHARGE',
  `AB-INV-NO` varchar(7) NOT NULL,
  `AB-INV-DT` date NOT NULL,
  `AB-REF` int(29) NOT NULL COMMENT 'wORK REFERENCE',
  `AB-AMT` decimal(10,0) NOT NULL COMMENT 'WORK TKT AMOUNT',
  KEY `AB-CUSTNO` (`AB-CUSTNO`,`AB-USE-DT`,`AB-BILL-NO`,`AB-BILL-DT`),
  KEY `AB-BILL-DT` (`AB-BILL-DT`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
