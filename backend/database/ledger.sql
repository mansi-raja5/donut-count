-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 31, 2020 at 11:44 AM
-- Server version: 10.1.35-MariaDB
-- PHP Version: 7.2.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ledger`
--

-- --------------------------------------------------------

--
-- Table structure for table `attachment_name_setting`
--

CREATE TABLE `attachment_name_setting` (
  `id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `invoice_name` varchar(255) DEFAULT NULL,
  `uploaded_invoice_name` varchar(255) DEFAULT NULL,
  `document_name_1` varchar(255) DEFAULT NULL,
  `document_name_2` varchar(255) DEFAULT NULL,
  `document_name_3` varchar(255) DEFAULT NULL,
  `uploaded_document_name_1` varchar(255) DEFAULT NULL,
  `uploaded_document_name_2` varchar(255) DEFAULT NULL,
  `uploaded_document_name_3` varchar(255) DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bank_statement`
--

CREATE TABLE `bank_statement` (
  `id` int(11) NOT NULL,
  `store_key` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `status` enum('reconcile','partially_reconcile','unreconcile') NOT NULL DEFAULT 'unreconcile',
  `created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bank_statement_entries`
--

CREATE TABLE `bank_statement_entries` (
  `id` int(11) NOT NULL,
  `bank_statement_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `transaction` varchar(100) NOT NULL,
  `transaction_type` varchar(255) NOT NULL,
  `check_num` int(11) NOT NULL,
  `description` text NOT NULL,
  `amount` double NOT NULL,
  `is_reconcile` tinyint(1) NOT NULL DEFAULT '0',
  `is_void` tinyint(1) DEFAULT '0',
  `ledger_statement_id` varchar(255) NOT NULL,
  `reconcile_type` enum('auto','manual','void','adjustment') DEFAULT NULL,
  `created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `checkbook_record`
--

CREATE TABLE `checkbook_record` (
  `id` int(11) NOT NULL,
  `ledger_id` int(11) NOT NULL,
  `payble_to` varchar(255) NOT NULL,
  `check_number` varchar(255) NOT NULL,
  `memo` text NOT NULL,
  `amount1` float NOT NULL,
  `credit_received_from` varchar(255) NOT NULL,
  `amount2` float NOT NULL,
  `created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ledger`
--

CREATE TABLE `ledger` (
  `id` int(11) NOT NULL,
  `store_key` varchar(255) NOT NULL,
  `month` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `status` enum('reconcile','partially_reconcile','unreconcile') NOT NULL DEFAULT 'unreconcile',
  `filename` text NOT NULL,
  `final_credit_total` double NOT NULL,
  `final_debit_total` double NOT NULL,
  `general_credit_total` double NOT NULL,
  `general_debit_total` double NOT NULL,
  `donut_total` double NOT NULL,
  `payroll_net_total` double NOT NULL,
  `payroll_gross_total` double NOT NULL,
  `dcp_total` double NOT NULL,
  `roy_total` double NOT NULL,
  `dean_total` double NOT NULL,
  `food_total` double NOT NULL COMMENT 'dean+dcp+donut',
  `balance_cf` double NOT NULL,
  `ledger_balance` double NOT NULL,
  `ending_balance` double NOT NULL COMMENT 'balance CF + ledger balance',
  `created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ledger_attachments`
--

CREATE TABLE `ledger_attachments` (
  `id` int(11) NOT NULL,
  `statement_id` int(11) NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `original_file_name` varchar(255) NOT NULL,
  `uploaded_file_name` varchar(255) NOT NULL,
  `uploaded_url` varchar(255) NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ledger_document`
--

CREATE TABLE `ledger_document` (
  `id` int(11) NOT NULL,
  `key_name` varchar(100) NOT NULL,
  `label` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=no,1=yes',
  `created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ledger_document`
--

INSERT INTO `ledger_document` (`id`, `key_name`, `label`, `is_active`, `created_on`, `updated_on`) VALUES
(1, 'general_section', 'General section', 1, '2020-01-31 00:16:26', '0000-00-00 00:00:00'),
(2, 'donut_purchases', 'DONUT PURCHASES from CML', 1, '2020-01-31 00:16:26', '0000-00-00 00:00:00'),
(3, 'impound', 'Impound Amt.', 1, '2020-01-31 00:22:52', '0000-00-00 00:00:00'),
(4, 'payroll_net', 'PAYROLL NET', 1, '2020-01-31 00:32:22', '0000-00-00 00:00:00'),
(5, 'payroll_gross', 'PAYROLL GROSS', 1, '2020-01-31 00:32:22', '0000-00-00 00:00:00'),
(6, 'roy_adv', 'ROY. & ADV. (First BR; Second Dunkin)', 1, '2020-01-31 00:33:23', '0000-00-00 00:00:00'),
(7, 'dean_foods', 'DEAN FOODS', 1, '2020-01-31 00:35:08', '0000-00-00 00:00:00'),
(8, 'dcp_efts', 'DCP EFTS', 1, '2020-01-31 00:37:53', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `ledger_statement`
--

CREATE TABLE `ledger_statement` (
  `id` bigint(20) NOT NULL,
  `store_key` int(11) NOT NULL,
  `ledger_id` int(11) NOT NULL,
  `credit_amt` double DEFAULT NULL,
  `debit_amt` double DEFAULT NULL,
  `transaction_type` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `credit_date` datetime DEFAULT NULL,
  `debit_date` datetime DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `is_payroll_net` tinyint(1) DEFAULT NULL,
  `is_payroll_gross` tinyint(1) DEFAULT NULL,
  `is_roy_adv` tinyint(1) DEFAULT NULL,
  `is_impound_amt` tinyint(1) DEFAULT NULL,
  `is_donut_purchase_cml` tinyint(1) DEFAULT NULL,
  `is_dcp_efts` tinyint(1) DEFAULT NULL,
  `is_dean_food` tinyint(1) DEFAULT NULL,
  `document_type` varchar(100) DEFAULT NULL,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `is_reconcile` tinyint(1) NOT NULL DEFAULT '0',
  `bank_statement_id` varchar(255) NOT NULL,
  `reconcile_type` enum('auto','manual','void','adjustment') DEFAULT NULL,
  `total_attachment` int(11) DEFAULT '0',
  `reconcile_date` date DEFAULT NULL,
  `is_manual` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ledger_statement_comment`
--

CREATE TABLE `ledger_statement_comment` (
  `comment_id` int(11) NOT NULL,
  `id` bigint(20) NOT NULL,
  `comment` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ledger_statement_splits`
--

CREATE TABLE `ledger_statement_splits` (
  `id` int(11) NOT NULL,
  `ledger_id` int(11) NOT NULL,
  `statement_id` int(11) NOT NULL COMMENT 'debit statement id ',
  `description` text NOT NULL,
  `amount` double NOT NULL,
  `document_type` varchar(255) NOT NULL,
  `created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `reconcile_document`
--

CREATE TABLE `reconcile_document` (
  `id` int(11) NOT NULL,
  `ledger_id` int(11) NOT NULL,
  `bank_statement_id` int(11) NOT NULL,
  `is_reconcile` tinyint(1) NOT NULL,
  `created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `store_master`
--

CREATE TABLE `store_master` (
  `store_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `key` varchar(100) NOT NULL,
  `location` text NOT NULL,
  `status` enum('A','I') NOT NULL COMMENT 'A=active,I=inactive',
  `created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `store_master`
--

INSERT INTO `store_master` (`store_id`, `name`, `key`, `location`, `status`, `created_on`, `updated_on`) VALUES
(1, 'store1', '352613', 'surat', 'A', '2020-01-31 02:22:24', '2020-02-04 01:28:46'),
(2, 'store2', '987456', 'baroda', 'A', '2020-01-31 02:23:09', '2020-02-01 16:52:43'),
(3, 'store3', '88989891', 'kkklk', 'A', '2020-01-31 02:40:12', '2020-02-01 16:57:43'),
(4, 'store4', '8898984', 'kkklk', 'A', '2020-01-31 02:40:23', '2020-02-01 16:57:47'),
(5, 'store5', '8898985', 'kkklk', 'A', '2020-01-31 02:40:23', '2020-02-01 16:57:50'),
(6, 'store6', '8898989', 'kkklk', 'A', '2020-01-31 02:40:23', '2020-02-01 16:36:50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attachment_name_setting`
--
ALTER TABLE `attachment_name_setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bank_statement`
--
ALTER TABLE `bank_statement`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bank_statement_entries`
--
ALTER TABLE `bank_statement_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bank_statement_id` (`bank_statement_id`);

--
-- Indexes for table `checkbook_record`
--
ALTER TABLE `checkbook_record`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ledger`
--
ALTER TABLE `ledger`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `store_key` (`store_key`,`month`,`year`);

--
-- Indexes for table `ledger_attachments`
--
ALTER TABLE `ledger_attachments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ledger_document`
--
ALTER TABLE `ledger_document`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ledger_statement`
--
ALTER TABLE `ledger_statement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ledger_id` (`ledger_id`);

--
-- Indexes for table `ledger_statement_comment`
--
ALTER TABLE `ledger_statement_comment`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `ledger_statement_splits`
--
ALTER TABLE `ledger_statement_splits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `splits_ledger_id` (`ledger_id`);

--
-- Indexes for table `reconcile_document`
--
ALTER TABLE `reconcile_document`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `store_master`
--
ALTER TABLE `store_master`
  ADD PRIMARY KEY (`store_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attachment_name_setting`
--
ALTER TABLE `attachment_name_setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bank_statement`
--
ALTER TABLE `bank_statement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bank_statement_entries`
--
ALTER TABLE `bank_statement_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `checkbook_record`
--
ALTER TABLE `checkbook_record`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ledger`
--
ALTER TABLE `ledger`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ledger_attachments`
--
ALTER TABLE `ledger_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ledger_document`
--
ALTER TABLE `ledger_document`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ledger_statement`
--
ALTER TABLE `ledger_statement`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ledger_statement_comment`
--
ALTER TABLE `ledger_statement_comment`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ledger_statement_splits`
--
ALTER TABLE `ledger_statement_splits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reconcile_document`
--
ALTER TABLE `reconcile_document`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `store_master`
--
ALTER TABLE `store_master`
  MODIFY `store_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bank_statement_entries`
--
ALTER TABLE `bank_statement_entries`
  ADD CONSTRAINT `bank_statement_id` FOREIGN KEY (`bank_statement_id`) REFERENCES `bank_statement` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ledger_statement`
--
ALTER TABLE `ledger_statement`
  ADD CONSTRAINT `ledger_id` FOREIGN KEY (`ledger_id`) REFERENCES `ledger` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ledger_statement_comment`
--
ALTER TABLE `ledger_statement_comment`
  ADD CONSTRAINT `ledger_statement_comment_ibfk_1` FOREIGN KEY (`id`) REFERENCES `ledger_statement` (`id`);

--
-- Constraints for table `ledger_statement_splits`
--
ALTER TABLE `ledger_statement_splits`
  ADD CONSTRAINT `splits_ledger_id` FOREIGN KEY (`ledger_id`) REFERENCES `ledger` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
