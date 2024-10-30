-- mansi - 2020-04-01
ALTER TABLE `bank_statement_entries` ADD `is_manual` BOOLEAN NOT NULL DEFAULT FALSE;

-- mansi - 2020-04-02
ALTER TABLE `bank_statement_entries` ADD `reconcile_date` DATE NOT NULL;

-- mansi - 2020-04-09
ALTER TABLE `checkbook_record` ADD `is_reconcile` BOOLEAN NOT NULL DEFAULT FALSE AFTER `updated_on`, ADD `bank_statement_id` VARCHAR(255) NOT NULL AFTER `is_reconcile`, ADD `reconcile_type` ENUM('auto','manual','void','adjustment') NULL DEFAULT NULL AFTER `bank_statement_id`, ADD `reconcile_date` DATE NULL DEFAULT NULL AFTER `reconcile_type`;
ALTER TABLE `bank_statement_entries` CHANGE `check_num` `check_num` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `checkbook_record` CHANGE `reconcile_date` `reconcile_date` DATETIME NULL DEFAULT NULL;
ALTER TABLE `bank_statement_entries` CHANGE `reconcile_date` `reconcile_date` DATETIME NOT NULL;
ALTER TABLE `ledger_statement` CHANGE `reconcile_date` `reconcile_date` DATETIME NULL DEFAULT NULL;
ALTER TABLE `checkbook_record` ADD  FOREIGN KEY (`ledger_id`) REFERENCES `ledger`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

-- nidhi - 2020-04-11
ALTER TABLE `ledger` ADD `notes` TEXT NOT NULL;


-- mansi - 2020-04-12
ALTER TABLE `ledger_statement` CHANGE `bank_statement_id` `bank_statement_id` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

--nidhi
--new table
CREATE TABLE `donut_count` (
  `id` int(11) NOT NULL,
  `store_key` varchar(255) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `donut_type` varchar(30) DEFAULT NULL,
  `week_ending_date` varchar(20) DEFAULT NULL,
  `week_day` varchar(20) DEFAULT NULL,
  `total_order` double DEFAULT NULL,
  `total_sale` double DEFAULT NULL,
  `created_on` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `donut_count` ADD PRIMARY KEY (`id`);

ALTER TABLE `donut_count`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;


CREATE TABLE `monthly_recap` (
  `id` int(11) NOT NULL,
  `store_key` varchar(255) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `month` varchar(20) DEFAULT NULL,
  `year` varchar(20) DEFAULT NULL,
  `cdate` varchar(30) DEFAULT NULL,
  `day` varchar(30) DEFAULT NULL,
  `baskin_sales` double DEFAULT NULL,
  `dunkin_sales` double DEFAULT NULL,
  `net_sales` double DEFAULT NULL,
  `newspaper` double DEFAULT NULL,
  `sales_tax` double DEFAULT NULL,
  `gross_sales` double DEFAULT NULL,
  `all_card_totals` double DEFAULT NULL,
  `bank_deposit` double DEFAULT NULL,
  `actual_bank_deposit` double DEFAULT NULL,
  `paidout` double DEFAULT NULL,
  `diff_count` double DEFAULT NULL,
  `guess_count` double DEFAULT NULL,
  `avg_ticket` double DEFAULT NULL,
  `item_del_bef_total` double DEFAULT NULL,
  `item_del_aft_total` double DEFAULT NULL,
  `cancel_transaction` double DEFAULT NULL,
  `created_on` datetime DEFAULT current_timestamp(),
  `updated_on` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `monthly_recap` ADD PRIMARY KEY (`id`);

ALTER TABLE `monthly_recap`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;


CREATE TABLE `card_recap` (
  `id` int(11) NOT NULL,
  `store_key` varchar(255) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `month` varchar(20) DEFAULT NULL,
  `year` varchar(10) DEFAULT NULL,
  `cdate` varchar(30) DEFAULT NULL,
  `day` varchar(30) DEFAULT NULL,
  `master_transaction` double DEFAULT NULL,
  `master_amount` double DEFAULT NULL,
  `visa_transaction` double DEFAULT NULL,
  `visa_amount` double DEFAULT NULL,
  `amex_transaction` double DEFAULT NULL,
  `amex_amount` double DEFAULT NULL,
  `discover_transaction` double DEFAULT NULL,
  `discover_amount` double DEFAULT NULL,
  `cc_recap_total_sales` double DEFAULT NULL,
  `dunkin_transaction` double DEFAULT NULL,
  `dunkin_amount` double DEFAULT NULL,
  `dd_cards_total` double DEFAULT NULL,
  `dd_paper_redeemed` double DEFAULT NULL,
  `created_on` datetime DEFAULT current_timestamp(),
  `updated_on` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `card_recap`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `card_recap`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;


CREATE TABLE `paid_out_recap` (
  `id` int(11) NOT NULL,
  `store_key` varchar(255) DEFAULT NULL,
  `file_name` text DEFAULT NULL,
  `month` varchar(10) DEFAULT NULL,
  `year` varchar(10) DEFAULT NULL,
  `cdate` varchar(30) DEFAULT NULL,
  `day` varchar(30) DEFAULT NULL,
  `baskin_foods` double DEFAULT NULL,
  `newspaper` double DEFAULT NULL,
  `dunkin_foods` double DEFAULT NULL,
  `employee_meals` double DEFAULT NULL,
  `employee_bonus` double DEFAULT NULL,
  `repairs` double DEFAULT NULL,
  `maintenance` double DEFAULT NULL,
  `office_supplies` double DEFAULT NULL,
  `cleaning_supplies` double DEFAULT NULL,
  `gas` double DEFAULT NULL,
  `other_expenses` double DEFAULT NULL,
  `total` double DEFAULT NULL,
  `created_on` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `paid_out_recap`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `paid_out_recap`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

-- nidhi 2020-04-26

ALTER TABLE donut_count
MODIFY COLUMN week_ending_date datetime null;


ALTER TABLE `donut_count` CHANGE `updated_on` `updated_on` DATETIME NULL DEFAULT NULL;
ALTER TABLE donut_count ADD UNIQUE (store_key,donut_type,week_ending_date,week_day,daily_date);


ALTER TABLE `donut_count` ADD `daily_date` DATETIME NULL AFTER `week_day`;

TRUNCATE TABLE `card_recap`;
TRUNCATE TABLE `paid_out_recap`;
TRUNCATE TABLE `monthly_recap`;

ALTER TABLE card_recap
MODIFY COLUMN cdate datetime null;

ALTER TABLE paid_out_recap
MODIFY COLUMN cdate datetime null;

ALTER TABLE monthly_recap
MODIFY COLUMN cdate datetime null;

ALTER TABLE card_recap ADD UNIQUE (store_key,cdate);
ALTER TABLE paid_out_recap ADD UNIQUE (store_key,cdate);
ALTER TABLE monthly_recap ADD UNIQUE (store_key,cdate);

-- mansi 26-04-2020
ALTER TABLE `checkbook_record` ADD `is_void` BOOLEAN NOT NULL DEFAULT FALSE;


-- nidhi 30-4-2020
CREATE TABLE `master_payroll` (
  `id` int(11) NOT NULL,
  `store_key` varchar(255) DEFAULT NULL,
  `file_name` text DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `fed_941_sum` double DEFAULT NULL,
  `futa_sum` double DEFAULT NULL,
  `swt_ga_sum` double DEFAULT NULL,
  `sui_ga_sum` double DEFAULT NULL,
  `total_tax_recap_sum` double DEFAULT NULL,
  `gross_wages_sum` double DEFAULT NULL,
  `net_sum` double DEFAULT NULL,
  `health_insurance` double DEFAULT NULL,
  `no_of_checks` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT current_timestamp(),
  `updated_on` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE `master_payroll`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `master_payroll`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE master_payroll ADD UNIQUE (store_key,start_date,end_date);

-- mansi 01-05-2020

CREATE TABLE `ledger_credit_received_from` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `ledger_id` int(11) NOT NULL,
 `label` varchar(255) NOT NULL,
 `amount` double NOT NULL,
 `created_on` datetime NOT NULL,
 `updated_on` datetime NOT NULL,
 PRIMARY KEY (`id`),
 KEY `ledger_credit_received_from_ibfk_1` (`ledger_id`),
 CONSTRAINT `ledger_credit_received_from_ibfk_1` FOREIGN KEY (`ledger_id`) REFERENCES `ledger` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1

-- mansi 01-05-2020
ALTER TABLE `bank_statement_entries` ADD `is_reconciled_current` BOOLEAN NOT NULL DEFAULT FALSE;
ALTER TABLE `checkbook_record` ADD `is_reconciled_current` BOOLEAN NOT NULL DEFAULT FALSE;
ALTER TABLE `ledger_statement` ADD `is_reconciled_current` BOOLEAN NOT NULL DEFAULT FALSE;

-- Sweety 03-05-2020
CREATE TABLE `user` (
 `id` int(11) NOT NULL,
 `username` varchar(20) NOT NULL,
 `password` varchar(20) NOT NULL,
 `status` int(11) NOT NULL,
 `type` varchar(1) NOT NULL,
 `updated_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

  ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

INSERT INTO `user` (`id`, `username`, `password`, `status`, `type`, `updated_date`) VALUES (NULL, 'admin', 'admin', '1', 'a', current_timestamp());

-- master pos functionality
--
-- Table structure for table `pos_master_key`
--

CREATE TABLE `pos_master_key` (
  `id` int(11) NOT NULL,
  `master_key` varchar(200) NOT NULL,
  `key_name` varchar(200) NOT NULL,
  `key_label` varchar(200) NOT NULL,
  `is_active` int(11) NOT NULL DEFAULT 1,
  `created_on` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `pos_master_key` (`id`, `master_key`, `key_name`, `key_label`, `is_active`, `created_on`, `updated_on`) VALUES
(1, 'd_espresso', 'd_espresso_qty', 'D-Espresso Subtotal Qty', 1, '2020-05-03 22:56:21', '0000-00-00 00:00:00'),
(2, 'd_espresso', 'd_espresso_subtotal', 'D-Espresso Subtotal', 1, '2020-05-03 22:56:21', '0000-00-00 00:00:00'),
(3, 'd_beverage', 'd_beverage_subtotal', 'D-Beverage Subtotal', 1, '2020-05-03 22:56:21', '0000-00-00 00:00:00'),
(4, 'd_donuts', 'd_donuts_subtotal', 'D-Donuts Subtotal', 1, '2020-05-03 22:56:21', '0000-00-00 00:00:00'),
(5, 'd_sandwich', 'd_sandwich_subtotal', 'D-Sandwich Subtotal', 1, '2020-05-03 22:56:21', '0000-00-00 00:00:00'),
(6, 'd_bagel', 'd_bagel_cc_subtotal', 'D-Bagel & CC Subtotal', 1, '2020-05-03 22:56:21', '0000-00-00 00:00:00'),
(7, 'dd_retail', 'dd_retail_net_sales', 'DD-Retail Net Sales', 1, '2020-05-03 22:56:22', '0000-00-00 00:00:00'),
(8, 'br_retail', 'br_retail_net_sales', 'BR-Retail Net Sales', 1, '2020-05-03 22:56:22', '0000-00-00 00:00:00'),
(9, 'trans_count', 'trans_count_qty', 'Trans Count Qty', 1, '2020-05-03 22:56:22', '0000-00-00 00:00:00'),
(10, 'br_retail', 'br_retail_gross_sales', 'BR-Retail Gross Sales', 1, '2020-05-03 22:56:22', '0000-00-00 00:00:00'),
(11, 'dd_retail', 'dd_retail_gross_sales', 'DD-Retail Gross Sales', 1, '2020-05-03 22:56:22', '0000-00-00 00:00:00'),
(12, 'refunds', 'refunds_qty', 'Refunds (-) Qty', 1, '2020-05-03 22:56:22', '0000-00-00 00:00:00'),
(13, 'refunds', 'refunds_amount', 'Refunds (-) Amount', 1, '2020-05-03 22:56:22', '0000-00-00 00:00:00'),
(14, 'discounts', 'discounts_qty', 'Discounts (-) Qty', 1, '2020-05-03 22:56:22', '0000-00-00 00:00:00'),
(15, 'discounts', 'discounts_amount', 'Discounts (-) Amount', 1, '2020-05-03 22:56:22', '0000-00-00 00:00:00'),
(16, 'coupons', 'coupons_qty', 'Coupons (-) Qty', 1, '2020-05-03 22:56:22', '0000-00-00 00:00:00'),
(17, 'coupons', 'coupons_amount', 'Coupons (-) Amount', 1, '2020-05-03 22:56:22', '0000-00-00 00:00:00'),
(18, 'net_autodetect', 'net_autodetect_disc_amount', 'Net AutoDetect Disc. (-) Amount', 1, '2020-05-03 22:56:22', '0000-00-00 00:00:00'),
(19, 'gift_card', 'gift_card_sales_qty', 'Gift Card Sales Qty', 1, '2020-05-03 22:56:22', '0000-00-00 00:00:00'),
(20, 'gift_card', 'gift_card_sales_amount', 'Gift Card Sales Amount', 1, '2020-05-03 22:56:22', '0000-00-00 00:00:00'),
(21, 'no_sale', 'no_sale_transactions_qty', 'No Sale Transactions Qty', 1, '2020-05-03 22:56:22', '0000-00-00 00:00:00'),
(22, 'no_sale', 'no_sale_transactions_amount', 'No Sale Transactions Amount', 1, '2020-05-03 22:56:22', '0000-00-00 00:00:00'),
(23, 'item_deletions', 'item_deletions_before_total_qty', 'Item Deletions Before Total Qty', 1, '2020-05-03 22:56:22', '0000-00-00 00:00:00'),
(24, 'item_deletions', 'item_deletions_before_total_amount', 'Item Deletions Before Total Amount', 1, '2020-05-03 22:56:22', '0000-00-00 00:00:00'),
(25, 'item_deletions', 'item_deletions_after_total_qty', 'Item Deletions After Total Qty', 1, '2020-05-03 22:56:22', '0000-00-00 00:00:00'),
(26, 'item_deletions', 'item_deletions_after_total_qty_amount', 'Item Deletions After Total Amount', 1, '2020-05-03 22:56:22', '2020-05-04 02:39:21'),
(27, 'cancelled_transactions', 'cancelled_transactions_qty', 'Cancelled Transactions Qty', 1, '2020-05-03 22:56:23', '0000-00-00 00:00:00'),
(28, 'cancelled_transactions', 'cancelled_transactions_amount', 'Cancelled Transactions Amount', 1, '2020-05-03 22:56:23', '0000-00-00 00:00:00'),
(29, 'tracked_fee_exempt', 'tracked_fee_exempt_net_sales', 'Tracked Fee Exempt Net Sales', 1, '2020-05-04 00:37:14', '0000-00-00 00:00:00'),
(30, 'charity', 'charity_net_sales', 'Charity Net Sales', 1, '2020-05-04 00:37:15', '0000-00-00 00:00:00'),
(31, 'paid_ins', 'paid_ins', 'Paid Ins (+)', 1, '2020-05-04 00:37:15', '0000-00-00 00:00:00'),
(32, 'gift_certificate', 'gift_certificate_sales', 'Gift Certificate Sales', 1, '2020-05-04 00:37:15', '0000-00-00 00:00:00'),
(33, 'sales_tax', 'sales_tax', 'Sales Tax (+)', 1, '2020-05-04 00:37:15', '0000-00-00 00:00:00'),
(34, 'sales_tax', 'sales_tax_percentage', 'Sales Tax (+) Percentage', 1, '2020-05-04 00:37:15', '0000-00-00 00:00:00'),
(35, 'taxable', 'taxable_sales', 'Taxable Sales', 1, '2020-05-04 00:37:15', '0000-00-00 00:00:00'),
(36, 'non_taxable', 'non_taxable_sales', 'Non Taxable Sales', 1, '2020-05-04 00:37:15', '0000-00-00 00:00:00'),
(37, 'deposit', 'deposit_total', 'Deposit Total', 1, '2020-05-04 00:37:15', '0000-00-00 00:00:00'),
(38, 'paid_out', 'paid_out', 'Paid Out', 1, '2020-05-04 00:37:15', '0000-00-00 00:00:00'),
(39, 'over_shot', 'over_shot', 'Over / Short', 1, '2020-05-04 00:37:15', '0000-00-00 00:00:00'),
(40, 'mastercard', 'mastercard_qty', 'MasterCard Qty', 1, '2020-05-04 00:37:15', '0000-00-00 00:00:00'),
(41, 'mastercard', 'mastercard_amount', 'MasterCard Amount', 1, '2020-05-04 00:37:15', '0000-00-00 00:00:00'),
(42, 'visa', 'visa_qty', 'Visa Qty', 1, '2020-05-04 00:37:15', '0000-00-00 00:00:00'),
(43, 'visa', 'visa_amount', 'Visa Amount', 1, '2020-05-04 00:37:15', '0000-00-00 00:00:00'),
(44, 'american_express', 'american_express_qty', 'American Express Qty', 1, '2020-05-04 00:37:16', '0000-00-00 00:00:00'),
(45, 'american_express', 'american_express_amount', 'American Express Amount', 1, '2020-05-04 00:37:16', '0000-00-00 00:00:00'),
(46, 'discover_novis', 'discover_novis_qty', 'Discover (NOVIS) Qty', 1, '2020-05-04 00:37:16', '0000-00-00 00:00:00'),
(47, 'discover_novis', 'discover_novis_amount', 'Discover (NOVIS) Amount', 1, '2020-05-04 00:37:16', '0000-00-00 00:00:00'),
(48, 'gift_card', 'gift_card_amount', 'Gift Card Amount', 1, '2020-05-04 00:37:16', '0000-00-00 00:00:00'),
(49, 'gift_card', 'gift_card_qty', 'Gift Card Qty', 1, '2020-05-04 00:37:16', '0000-00-00 00:00:00'),
(50, 'external_discover', 'external_discover_novis_qty', 'External Discover (NOVIS) Qty', 1, '2020-05-04 00:37:16', '2020-05-04 20:35:07'),
(51, 'external_discover', 'external_discover_novis_amount', 'External Discover (NOVIS) Amount', 1, '2020-05-04 00:37:16', '2020-05-04 20:34:41'),
(52, 'external_amex', 'external_amex_qty', 'External Amex Qty', 1, '2020-05-04 00:37:16', '0000-00-00 00:00:00'),
(53, 'external_amex', 'external_amex_amount', 'External Amex Amount', 1, '2020-05-04 00:37:16', '0000-00-00 00:00:00'),
(54, 'external_giftcard', 'external_giftcard_qty', 'External Gift Card Qty', 1, '2020-05-04 00:37:16', '0000-00-00 00:00:00'),
(55, 'external_giftcard', 'external_giftcard_amount', 'External Gift Card Amount', 1, '2020-05-04 00:37:16', '0000-00-00 00:00:00'),
(56, 'external_mastercard', 'external_mastercard_qty', 'External Mastercard Qty', 1, '2020-05-04 00:37:16', '0000-00-00 00:00:00'),
(57, 'external_mastercard', 'external_mastercard_amount', 'External Mastercard Amount', 1, '2020-05-04 00:37:16', '0000-00-00 00:00:00'),
(58, 'external_order', 'external_order_qty', 'External Order Qty', 1, '2020-05-04 00:37:16', '0000-00-00 00:00:00'),
(59, 'external_order', 'external_order_amount', 'External Order Amount', 1, '2020-05-04 00:37:16', '0000-00-00 00:00:00'),
(60, 'external_visa', 'external_visa_qty', 'External Visa Qty', 1, '2020-05-04 00:37:16', '0000-00-00 00:00:00'),
(61, 'external_visa', 'external_visa_amount', 'External Visa Amount', 1, '2020-05-04 00:37:16', '0000-00-00 00:00:00'),
(62, 'delivery_grubhub', 'delivery_grubhub_qty', 'Delivery:Grubhub Qty', 1, '2020-05-04 00:37:16', '0000-00-00 00:00:00'),
(63, 'delivery_grubhub', 'delivery_grubhub_amount', 'Delivery:Grubhub Amount', 1, '2020-05-04 00:37:16', '0000-00-00 00:00:00');

ALTER TABLE `pos_master_key`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `pos_master_key`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

-----------------------------------------------------------------------------------------------


--
-- Table structure for table `master_pos_daily`
--

CREATE TABLE `master_pos_daily` (
  `id` int(11) NOT NULL,
  `store_key` varchar(250) NOT NULL,
  `cdate` datetime NOT NULL,
  `data` mediumtext NOT NULL,
  `created_on` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE `master_pos_daily`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `store_key` (`store_key`,`cdate`);
ALTER TABLE `master_pos_daily`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-----------------------------------------------------------------------------------------------
--
-- Table structure for table `master_pos_weekly`
--

CREATE TABLE `master_pos_weekly` (
  `id` int(11) NOT NULL,
  `store_key` varchar(250) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `data` mediumtext NOT NULL,
  `created_on` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE `master_pos_weekly`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `store_key` (`store_key`,`start_date`,`end_date`);
ALTER TABLE `master_pos_weekly`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


-- daily sales add data dynamic column
CREATE TABLE `dynamic_dailysales_column` (
  `id` int(11) NOT NULL,
  `column_name` varchar(250) NOT NULL,
  `key_name` varchar(250) NOT NULL,
  `is_active` int(11) NOT NULL DEFAULT 1,
  `created_on` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO `dynamic_dailysales_column` (`id`, `column_name`, `key_name`, `is_active`, `created_on`, `updated_on`) VALUES
(1, 'Baskin Food', 'baskin_foods', 1, '2020-05-05 22:45:52', '2020-05-06 06:20:52'),
(2, 'Newspaper', 'newspaper', 1, '2020-05-05 22:46:36', '2020-05-06 06:21:05'),
(3, 'Dunkin Food', 'dunkin_foods', 1, '2020-05-05 22:46:36', '2020-05-06 06:21:08'),
(4, 'Employee Meals', 'employee_meals', 1, '2020-05-05 22:47:00', '2020-05-06 06:21:16'),
(5, 'Employee Bonus', 'employee_bonus', 1, '2020-05-05 22:47:00', '2020-05-06 06:21:25'),
(6, 'Repairs', 'repairs', 1, '2020-05-05 22:47:58', '2020-05-06 06:21:33'),
(7, 'Maintenance', 'maintenance', 1, '2020-05-05 22:47:58', '2020-05-06 06:21:41'),
(8, 'Office Supplies', 'office_supplies', 1, '2020-05-05 22:48:23', '2020-05-06 06:21:52'),
(9, 'Cleaning Supplies', 'cleaning_supplies', 1, '2020-05-05 22:48:23', '2020-05-06 06:21:59'),
(10, 'Gas', 'gas', 1, '2020-05-05 22:48:43', '2020-05-06 06:22:06'),
(11, 'Other Expense', 'other_expenses', 1, '2020-05-05 22:48:43', '2020-05-06 06:22:13');

ALTER TABLE `dynamic_dailysales_column`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `dynamic_dailysales_column`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;


  -- nidhi add dailysales data
  ALTER TABLE `attachment_name_setting`  ADD `selected_type` VARCHAR(30) NOT NULL  AFTER `id`;
  ALTER TABLE `paid_out_recap` ADD `is_lock` INT NOT NULL DEFAULT '0' AFTER `total`;

  -- dynamic_dailysales_column

  CREATE TABLE `dynamic_dailysales_column` (
  `id` int(11) NOT NULL,
  `column_name` varchar(250) NOT NULL,
  `key_name` varchar(250) NOT NULL,
  `is_active` int(11) NOT NULL DEFAULT 1,
  `created_on` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

  ALTER TABLE `dynamic_dailysales_column`
  ADD PRIMARY KEY (`id`);

  ALTER TABLE `dynamic_dailysales_column`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

  INSERT INTO `dynamic_dailysales_column` (`id`, `column_name`, `key_name`, `is_active`, `created_on`, `updated_on`) VALUES
(1, 'Baskin Food', 'baskin_foods', 1, '2020-05-05 22:45:52', '2020-05-06 06:20:52'),
(2, 'Newspaper', 'newspaper', 1, '2020-05-05 22:46:36', '2020-05-06 06:21:05'),
(3, 'Dunkin Food', 'dunkin_foods', 1, '2020-05-05 22:46:36', '2020-05-06 06:21:08'),
(4, 'Employee Meals', 'employee_meals', 1, '2020-05-05 22:47:00', '2020-05-06 06:21:16'),
(5, 'Employee Bonus', 'employee_bonus', 1, '2020-05-05 22:47:00', '2020-05-06 06:21:25'),
(6, 'Repairs', 'repairs', 1, '2020-05-05 22:47:58', '2020-05-06 06:21:33'),
(7, 'Maintenance', 'maintenance', 1, '2020-05-05 22:47:58', '2020-05-06 06:21:41'),
(8, 'Office Supplies', 'office_supplies', 1, '2020-05-05 22:48:23', '2020-05-06 06:21:52'),
(9, 'Cleaning Supplies', 'cleaning_supplies', 1, '2020-05-05 22:48:23', '2020-05-06 06:21:59'),
(10, 'Gas', 'gas', 1, '2020-05-05 22:48:43', '2020-05-06 06:22:06'),
(11, 'Other Expense', 'other_expenses', 1, '2020-05-05 22:48:43', '2020-05-06 06:22:13');

-----------------------------------------

-- dailysales_attachments_upload for attachments

CREATE TABLE `dailysales_attachments_upload` (
  `id` int(11) NOT NULL,
  `store_key` varchar(200) NOT NULL,
  `cdate` date NOT NULL,
  `year` varchar(200) NOT NULL,
  `month` varchar(200) NOT NULL,
  `dynamic_column_id` varchar(200) NOT NULL,
  `original_file_name` varchar(250) NOT NULL,
  `uploaded_file_name` varchar(250) NOT NULL,
  `uploaded_url` varchar(250) NOT NULL,
  `created_on` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_on` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `dailysales_attachments_upload`
  ADD PRIMARY KEY (`id`);
  ------------------------------------------

ALTER TABLE `ledger`.`store_master` ADD UNIQUE (`key`);


CREATE TABLE `customer_review` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `start_date` datetime NOT NULL,
 `end_date` datetime NOT NULL,
 `visit_date` datetime NOT NULL,
 `store_key` int(11) NOT NULL,
 `type` varchar(255) NOT NULL,
 `n_number` int(11) NOT NULL,
 `five_star` double NOT NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `start_date` (`start_date`,`end_date`,`store_key`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1

-- nidhi - 2020-05-17
CREATE TABLE `dynamic_cardrecap_column` (
  `id` int(11) NOT NULL,
  `master_key` varchar(250) NOT NULL,
  `key_name` varchar(250) NOT NULL,
  `key_label` varchar(250) NOT NULL,
  `is_active` int(11) NOT NULL DEFAULT 1,
  `created_on` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `dynamic_cardrecap_column` (`id`, `master_key`, `key_name`, `key_label`, `is_active`, `created_on`) VALUES
(1, 'MASTERCARD', 'master_transaction', '# of Trans', 1, '2020-05-17 01:14:32'),
(2, 'MASTERCARD', 'master_amount', 'Amount', 1, '2020-05-17 01:21:25'),
(3, 'VISA', 'visa_transaction', '# of Trans', 1, '2020-05-17 01:21:25'),
(4, 'VISA', 'visa_amount', 'Amount', 1, '2020-05-17 01:21:25'),
(5, 'AMEX', 'amex_transaction', '# of Trans', 1, '2020-05-17 01:21:26'),
(6, 'AMEX', 'amex_amount', 'Amount', 1, '2020-05-17 01:21:26'),
(7, 'DISCOVER', 'discover_transaction', '# of Trans', 1, '2020-05-17 01:21:26'),
(8, 'DISCOVER', 'discover_amount', 'Amount', 1, '2020-05-17 01:21:26'),
(9, 'CC RECAP', 'cc_recap_total_sales', 'Total Sales', 1, '2020-05-17 01:21:26'),
(10, 'DUNKIN CARDS', 'dunkin_transaction', 'Act/Reload', 1, '2020-05-17 01:21:26'),
(11, 'DUNKIN CARDS', 'dunkin_amount', 'Redeemed', 1, '2020-05-17 01:21:26'),
(12, 'DD CARDS', 'dd_cards_total', 'Total', 1, '2020-05-17 01:21:26'),
(13, 'DD PAPER', 'dd_paper_redeemed', 'Redeemed', 1, '2020-05-17 01:21:26');

ALTER TABLE `dynamic_cardrecap_column`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `dynamic_cardrecap_column`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Table structure for table `dynamic_monthlyrecap_column`
--

CREATE TABLE `dynamic_monthlyrecap_column` (
  `id` int(11) NOT NULL,
  `key_name` varchar(250) NOT NULL,
  `key_label` varchar(250) NOT NULL,
  `is_active` int(11) NOT NULL DEFAULT 1,
  `created_on` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `dynamic_monthlyrecap_column` (`id`, `key_name`, `key_label`, `is_active`, `created_on`) VALUES
(1, 'baskin_sales', 'BASKIN SALES', 1, '2020-05-17 01:42:23'),
(2, 'dunkin_sales', 'DUNKIN SALES', 1, '2020-05-17 01:44:54'),
(3, 'net_sales', 'NET SALES', 1, '2020-05-17 01:44:54'),
(4, 'newspaper', 'NEWS PAPERS', 1, '2020-05-17 01:44:55'),
(5, 'sales_tax', 'SALES TAX', 1, '2020-05-17 01:44:55'),
(6, 'gross_sales', 'GROSS SALES', 1, '2020-05-17 01:44:55'),
(7, 'all_card_totals', 'ALL CARDS TOTAL', 1, '2020-05-17 01:44:55'),
(8, 'actual_bank_deposit', 'BANK DEPOSIT', 1, '2020-05-17 01:44:55'),
(9, 'paidout', 'PAID OUTS', 1, '2020-05-17 01:44:55'),
(10, 'diff_count', 'DIFF', 1, '2020-05-17 01:44:55'),
(11, 'guess_count', 'GUEST COUNT', 1, '2020-05-17 01:44:55'),
(12, 'avg_ticket', 'AVG. TICKET', 1, '2020-05-17 01:46:08'),
(13, 'item_del_bef_total', 'Item Del bef Total', 1, '2020-05-17 01:48:04'),
(14, 'item_del_aft_total', 'Item Del Aft Total', 1, '2020-05-17 01:48:04'),
(15, 'cancel_transaction', 'Cancell Transac.', 1, '2020-05-17 01:48:05');

ALTER TABLE `dynamic_monthlyrecap_column`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `dynamic_monthlyrecap_column`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- nidhi 19-05-2020
ALTER TABLE `customer_review` ADD `duration_type` VARCHAR(30) NOT NULL AFTER `id`;
-- sweety - 2020-05-19
CREATE TABLE `cars_entry` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `store_key` int(11) NOT NULL,
 `weekend_date` date NOT NULL,
 `weekend_data` text NOT NULL,
 `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`),
 UNIQUE KEY `store_id` (`store_key`,`weekend_date`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1

-- nidhi 20-05-2020
CREATE TABLE `admin_settings` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `key_name` varchar(150) NOT NULL,
 `key_value` varchar(250) NOT NULL,
 `created_on` datetime NOT NULL DEFAULT current_timestamp(),
 `updated_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4

CREATE TABLE `labor_summary` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `store_key` int(11) NOT NULL,
 `week_ending_date` datetime NOT NULL,
 `gross_pay` double NOT NULL,
 `tax_amount` double NOT NULL,
 `net_sales` double NOT NULL,
 `tax_percentage` double NOT NULL,
 `bonus` double NOT NULL,
 `total_pay` double NOT NULL,
 `labor_percentage` double NOT NULL,
 `created_on` timestamp NOT NULL DEFAULT current_timestamp(),
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1

-- nidhi 22-05-2020
CREATE TABLE `dynamic_donutcount_column` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `master_key` varchar(30) NOT NULL,
 `key_name` varchar(50) NOT NULL,
 `key_label` varchar(50) NOT NULL,
 `is_active` int(11) NOT NULL DEFAULT 1,
 `created_on` datetime NOT NULL DEFAULT current_timestamp(),
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4;

INSERT INTO `dynamic_donutcount_column` (`id`, `master_key`, `key_name`, `key_label`, `is_active`, `created_on`) VALUES
(10, 'Donuts', 'donuts_order', 'Order', 1, '2020-05-20 21:58:58'),
(11, 'Donuts', 'donuts_sale', 'Sale', 1, '2020-05-20 21:59:53'),
(12, 'Fancy', 'fancy_order', 'Order', 1, '2020-05-20 21:59:54'),
(13, 'Fancy', 'fancy_sale', 'Order', 1, '2020-05-20 21:59:54'),
(14, 'Munkins', 'munkins_order', 'Order', 1, '2020-05-20 21:59:54'),
(15, 'Munkins', 'munkins_sale', 'Order', 1, '2020-05-20 21:59:54');

CREATE TABLE `dynamic_payroll_column` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `key_name` varchar(50) NOT NULL,
 `key_label` varchar(50) NOT NULL,
 `is_active` int(11) NOT NULL DEFAULT 1,
 `created_on` datetime NOT NULL DEFAULT current_timestamp(),
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4;

INSERT INTO `dynamic_payroll_column` (`id`, `key_name`, `key_label`, `is_active`, `created_on`) VALUES
(1, 'fed_941_sum', 'Fed 941_Sum', 1, '2020-05-20 21:52:29'),
(2, 'futa_sum', 'FUTA_Sum', 1, '2020-05-20 21:54:18'),
(3, 'swt_ga_sum', 'SWT-GA_Sum', 1, '2020-05-20 21:54:18'),
(4, 'sui_ga_sum', 'SUI-GA_Sum', 1, '2020-05-20 21:54:18'),
(5, 'total_tax_recap_sum', 'Total Tax Recap_Sum', 1, '2020-05-20 21:54:18'),
(6, 'gross_wages_sum', 'Gross Wages_Sum', 1, '2020-05-20 21:56:12'),
(7, 'net_sum', 'Net_Sum', 1, '2020-05-20 21:56:12'),
(8, 'health_insurance', 'Health Insurance', 1, '2020-05-20 21:56:12'),
(9, 'no_of_checks', 'Number of Checks ', 1, '2020-05-20 21:56:12');

-- sweety 24-05-2020
ALTER TABLE `ledger`  ADD `is_locked` TINYINT(1) NOT NULL COMMENT '1=yes,0=no'  AFTER `final_debit_total`;
ALTER TABLE `bank_statement`  ADD `is_locked` TINYINT(1) NOT NULL COMMENT '1=yes,0=no'  AFTER `status`;

ALTER TABLE `dailysales_attachments_upload` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;

-- nidhi 25-05-2020
UPDATE `dynamic_monthlyrecap_column` SET `key_name` = 'bank_deposit' WHERE `dynamic_monthlyrecap_column`.`id` = 8;
DELETE FROM `dynamic_monthlyrecap_column` WHERE `dynamic_monthlyrecap_column`.`id` = 9;
DELETE FROM `dynamic_monthlyrecap_column` WHERE `dynamic_monthlyrecap_column`.`id` = 10;
DELETE FROM `dynamic_monthlyrecap_column` WHERE `dynamic_monthlyrecap_column`.`id` = 11;
DELETE FROM `dynamic_monthlyrecap_column` WHERE `dynamic_monthlyrecap_column`.`id` = 12;
DELETE FROM `dynamic_monthlyrecap_column` WHERE `dynamic_monthlyrecap_column`.`id` = 13;
DELETE FROM `dynamic_monthlyrecap_column` WHERE `dynamic_monthlyrecap_column`.`id` = 14;
DELETE FROM `dynamic_monthlyrecap_column` WHERE `dynamic_monthlyrecap_column`.`id` = 15;
INSERT INTO `dynamic_monthlyrecap_column` (`id`, `key_name`, `key_label`, `is_active`, `created_on`) VALUES (NULL, 'actual_bank_deposit', 'Actual Bank Deposit', '1', current_timestamp());
INSERT INTO `dynamic_monthlyrecap_column` (`id`, `key_name`, `key_label`, `is_active`, `created_on`) VALUES (NULL, 'paidout', 'PAID OUTS', '1', current_timestamp());
INSERT INTO `dynamic_monthlyrecap_column` (`id`, `key_name`, `key_label`, `is_active`, `created_on`) VALUES (NULL, 'diff_count', 'DIFF', '1', current_timestamp());
INSERT INTO `dynamic_monthlyrecap_column` (`id`, `key_name`, `key_label`, `is_active`, `created_on`) VALUES (NULL, 'guess_count', 'GUEST COUNT', '1', current_timestamp());
INSERT INTO `dynamic_monthlyrecap_column` (`id`, `key_name`, `key_label`, `is_active`, `created_on`) VALUES (NULL, 'avg_ticket', 'AVG. TICKET', '1', current_timestamp());
INSERT INTO `dynamic_monthlyrecap_column` (`id`, `key_name`, `key_label`, `is_active`, `created_on`) VALUES (NULL, 'item_del_bef_total', 'Item Del bef Total', 1, current_timestamp());
INSERT INTO `dynamic_monthlyrecap_column` (`id`, `key_name`, `key_label`, `is_active`, `created_on`) VALUES (NULL, 'item_del_aft_total', 'Item Del Aft Total', '1', current_timestamp());
INSERT INTO `dynamic_monthlyrecap_column` (`id`, `key_name`, `key_label`, `is_active`, `created_on`) VALUES (NULL, 'cancel_transaction', 'Cancell Transac.', '1', current_timestamp());

-- nidhi 28-05-2020
CREATE TABLE `admin_store_setting` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `store_key` varchar(255) NOT NULL,
 `year` varchar(50) NOT NULL,
 `data` text NOT NULL,
 `created_on` datetime NOT NULL DEFAULT current_timestamp(),
 `updated_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4

-- nidhi 3-6-2020
CREATE TABLE `admin_excludecalculation_settings` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `store_key` varchar(250) NOT NULL,
 `key_label` varchar(250) NOT NULL,
 `from_date` date NOT NULL,
 `to_date` date NOT NULL,
 `is_infinite` int(11) NOT NULL,
 `is_active` int(11) NOT NULL,
 `created_on` datetime NOT NULL DEFAULT current_timestamp(),
 `updated_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `dynamic_excludecalculation_column` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `key_name` varchar(250) NOT NULL,
 `key_label` varchar(250) NOT NULL,
 `is_active` int(11) NOT NULL DEFAULT 1,
 `created_on` datetime NOT NULL DEFAULT current_timestamp(),
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
INSERT INTO `dynamic_excludecalculation_column` (`id`, `key_name`, `key_label`, `is_active`, `created_on`) VALUES (NULL, 'test1', 'test1', '1', current_timestamp());

-- sweety 11-06-2020
ALTER TABLE `master_pos_daily` CHANGE `cdate` `cdate` DATE NOT NULL;

ALTER TABLE `store_master` ADD `tax_id` VARCHAR(255) NULL AFTER `location`;

-- nidhi 16-06-2020
  CREATE TABLE `admin_labor_bonus_setting` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `store_key` text NOT NULL,
 `calculation_type` varchar(20) NOT NULL,
 `year` varchar(10) NOT NULL,
 `month` varchar(20) NOT NULL,
 `weekly_date` date NOT NULL,
 `amount` int(11) NOT NULL,
 `created_on` datetime NOT NULL DEFAULT current_timestamp(),
 `updated_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `admin_calculationconditional_settings` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `store_key` varchar(255) NOT NULL,
 `year` int(11) NOT NULL,
 `pos_key` varchar(250) NOT NULL,
 `value_type` varchar(20) NOT NULL,
 `expression_type` varchar(5) NOT NULL,
 `amount` double NOT NULL,
 `color` varchar(20) NOT NULL,
 `created_on` datetime NOT NULL DEFAULT current_timestamp(),
 `updated_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- mansi - 20-06-2020
ALTER TABLE `ledger_statement` DROP `debit_date`;
ALTER TABLE `ledger_statement` DROP `is_payroll_net`;
ALTER TABLE `ledger_statement` DROP `is_payroll_gross`;
ALTER TABLE `ledger_statement` DROP `is_roy_adv`;
ALTER TABLE `ledger_statement` DROP `is_dean_food`;
ALTER TABLE `ledger_statement` DROP `is_dcp_efts`;
ALTER TABLE `ledger_statement` DROP `is_donut_purchase_cml`;
ALTER TABLE `ledger_statement` DROP `is_impound_amt`;

-- sweety 27-06-2020
ALTER TABLE `store_master`  ADD `certipay_control` VARCHAR(200) NULL  AFTER `store_id`,  ADD `business_name` VARCHAR(200) NULL  AFTER `certipay_control`;

-- mansi - 27-06-2020
ALTER TABLE `master_payroll` ADD UNIQUE KEY `store_key` (`store_key`(100),`start_date`,`end_date`);

-- sweety - 05-07-2020
CREATE TABLE `special_day` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `store_key` varchar(255) NOT NULL,
 `date` date NOT NULL,
 `name` varchar(100) NOT NULL,
 `status` enum('A','I') NOT NULL DEFAULT 'A' COMMENT 'A=active,I=inactive',
 `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`),
 UNIQUE KEY `store_key` (`store_key`(100),`date`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

CREATE TABLE `season` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `store_key` varchar(255) NOT NULL,
 `from_date` date NOT NULL,
 `to_date` date NOT NULL,
 `name` varchar(100) NOT NULL,
 `status` enum('A','I') NOT NULL DEFAULT 'A' COMMENT 'A=active,I=inactive',
 `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`),
 UNIQUE KEY `store_key` (`store_key`(100),`from_date`,`to_date`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;


-- mansi - 07-07-2020
ALTER TABLE `ledger_statement` ADD `is_adjustment_entry` BOOLEAN NOT NULL DEFAULT FALSE;

-- sweety - 11-07-2020
ALTER TABLE `ledger_attachments`  ADD `source` ENUM('ledger','checkbook','credit_received_from') NOT NULL DEFAULT 'ledger'  AFTER `uploaded_url`;
ALTER TABLE `checkbook_record`  ADD `total_attachment` INT(11) NOT NULL DEFAULT '0'  AFTER `is_void`;
ALTER TABLE `ledger_credit_received_from`  ADD `total_attachment` INT(11) NOT NULL DEFAULT '0'  AFTER `amount`;

-- sweety - 23-07-2020
ALTER TABLE `pos_master_key` CHANGE `updated_on` `updated_on` DATETIME on update CURRENT_TIMESTAMP NULL DEFAULT NULL;
UPDATE `pos_master_key` SET `key_label` = 'Deposit Total (+)' WHERE `pos_master_key`.`id` = 37;
UPDATE `pos_master_key` SET `key_label` = 'Paid Out (-)' WHERE `pos_master_key`.`id` = 38;
INSERT INTO `pos_master_key` (`id`, `master_key`, `key_name`, `key_label`, `is_active`, `created_on`, `updated_on`) VALUES (NULL, 'delivery_grubhub', 'delivery_grubhub_net_sales', 'Delivery:Grubhub Net Sales', '1', CURRENT_TIMESTAMP, NULL), (NULL, 'delivery_uber_eats', 'delivery_uber_eats_qty', 'Delivery:Uber Eats Qty', '1', CURRENT_TIMESTAMP, NULL), (NULL, 'delivery_uber_eats', 'delivery_uber_eats_amount', 'Delivery:Uber Eats Amount', '1', CURRENT_TIMESTAMP, NULL), (NULL, 'delivery_uber_eats', 'delivery_uber_eats_net_sales', 'Delivery:Uber Eats Net Sales', '1', CURRENT_TIMESTAMP, NULL);

-- sweety 02-08-2020
INSERT INTO `admin_settings` (`id`, `key_name`, `key_value`, `created_on`, `updated_on`) VALUES (NULL, 'check_number_starting', '', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000');

-- mansi 02-08-2020
ALTER TABLE `ledger_statement` ADD `with_point_diff` BOOLEAN NOT NULL DEFAULT;
ALTER TABLE `bank_statement_entries` ADD `with_point_diff` BOOLEAN NOT NULL DEFAULT;


INSERT INTO `dynamic_monthlyrecap_column` (`id`, `key_name`, `key_label`, `is_active`, `created_on`) VALUES (NULL, 'tracked_fee_exempt_net_sales', 'Tracked Fee Exempt Net Sales', '1', CURRENT_TIMESTAMP), (NULL, 'charity_net_sales', 'Charity Net Sales', '1', CURRENT_TIMESTAMP);
INSERT INTO `dynamic_monthlyrecap_column` (`id`, `key_name`, `key_label`, `is_active`, `created_on`) VALUES (NULL, 'paid_ins', 'Paid Ins (+)', '1', CURRENT_TIMESTAMP), (NULL, 'gift_certificate_sales', 'Gift Certificate Sales', '1', CURRENT_TIMESTAMP);

ALTER TABLE `monthly_recap`  ADD `tracked_fee_exempt_net_sales` DOUBLE NULL  AFTER `paidout`,  ADD `charity_net_sales` DOUBLE NULL  AFTER `tracked_fee_exempt_net_sales`,  ADD `paid_ins` DOUBLE NULL  AFTER `charity_net_sales`,  ADD `gift_certificate_sales` DOUBLE NULL  AFTER `paid_ins`;

-- mansi 18-08-2020
CREATE TABLE `admin_year_setting` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `year` varchar(10) NOT NULL,
 `year_starting_date` datetime NOT NULL,
 `year_weeks` int(5) NOT NULL,
 `month` int(5) NOT NULL,
 `month_weeks` int(5) NOT NULL,
 `weeks` text NOT NULL,
 `created_on` datetime NOT NULL,
 `updated_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`),
 UNIQUE KEY `year` (`year`,`month`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1

-- sweety 16-08-2020
ALTER TABLE `master_pos_daily`  ADD `is_lock` TINYINT NOT NULL DEFAULT '0'  AFTER `data`;
ALTER TABLE `monthly_recap`  ADD `is_lock` TINYINT NOT NULL DEFAULT '0' COMMENT '1=yes,0=no'  AFTER `is_gift_certificate_sales`;
ALTER TABLE `card_recap`  ADD `is_lock` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '1=yes,0=no'  AFTER `dd_paper_redeemed`;


-- mansi 20-08-2020
CREATE TABLE auto_reconcilation ( id int(11) NOT NULL AUTO_INCREMENT, ledger_desc varchar(255) NOT NULL, bank_desc varchar(255) NOT NULL, is_active tinyint(1) NOT NULL DEFAULT '1', created_on datetime NOT NULL, PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=latin1
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('DEPOSIT', 'DEPOSIT');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Sales Tax (last month)', 'GA TX PYMT%');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Jesani Accounting', '%JESANI ACCOUNTIN%');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Electricity - GA Power', 'GPC EFT GPC%');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Exterminating - EcoLab ', '%EcoLab%');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Exterminating - EcoLab ', 'ePay Ecolab%');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Exterminating - EcoLab', '%Manual Bill for Ecolab%');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Garbage - Waste Management', '%WASTE MANAGEMENT%');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Garbage-Waste Management', '%WASTE MANAGEMENT%');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Garbage -Waste Management', '%WASTE MANAGEMENT%');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Garbage - Waste Management', '%WASTE MANAGEMENT%');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Gas - Infinite Energy', '%INFINITE ENERGY%');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Real Estate Tax (FC Enterprise)', 'FC Enterprises Inc');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Tillster Ordering', 'Baskin Rob EMN%');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Water & Sewer -City of Atlanta', '%CITY OF ATL%');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Worker\'s Compensation - Peachstate', 'PeachState Concessioniares Inc');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Worker\'s Compensation - Peachstate ', 'Peachstate Concessioniarres Inc');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Rent (FC Enterprise)', 'FC Enterprises Inc');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Bank Loan Payment', '%COMM LOANS BBT%');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('MTV Food Enterprises (Maint. Guy)', 'MTV Food Enterprises LLC');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Gas - Scana Energy', '%SCANA ENERGY%');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Telephone - Granite', '%Granite%');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Bank Loan (Pacific Premier)', '%PACIFIC PREMIER%');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Bank Loan Real Estate (BB&T)', '%COMM LOANS BBT%');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('MTV Food Enterprises (Main. person)', 'MTV Food Enterprises LLC');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Payroll Net ', '%CKS CERTISTAFF%');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Payroll Net', '%PAYROLL%');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Roy Adv', '%EFT DEBIT DUNKIN BRANDS%');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Impound', '%TAX COL CERTITAX LLC%');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Donut ', '%CustmerCol Bluemont Group%');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Donut', '%Golden Donut LLC%');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Dcp Efts', '%BNKCRD DEP NATIONAL DCP%');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Dean Foods', '%DEAN FOODS%');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Georgia Power (Pole Lights)', '%GEORGIA POWER%');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Garbage - Grogan Disposal', '%Grogan Disposal%');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Gas - City of Sugar Hill', '%CITY OF SUGAR%');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('DTT Camera Service', 'SERVICES DTT%');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Water & Sewer - Gwinnett County', '%GWINNETT COUNTY%');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Rent (FC Enterprises)', 'FC Enterprises Inc');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Property Tax (FC Enterprises)', 'FC Enterprises Inc');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Bank Loan Payment', 'FRA PAY Franchise%');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('City of Lawrenceville (Utilities)', 'ONLINE PMT CITYOFLAWRENCEVI%');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Rent (V&W Horiuchi LLC)', 'V&W Horiuchi LLC');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Bank Loan (Pacific Premier)', '%PACIFIC PREMIER%');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Garbage - Waste Industry', '%WASTE INDUSTRIES%');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Granite Telecommunition', 'Granite Telecommunications');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Water & Sewer - Dekalb County', '%DEKALB CNTY%');
INSERT INTO `auto_reconcilation` (`ledger_desc`, `bank_desc`) VALUES ('Sharp Twist LLC', '%Sharp Twist LLC%');

-- sweety 20-08-2020
ALTER TABLE `monthly_recap`  ADD `grubhub_total_gross` DOUBLE NULL  AFTER `gross_sales`,  ADD `uber_eats_total_gross` DOUBLE NULL  AFTER `grubhub_total_gross`;
ALTER TABLE `card_recap`  ADD `otgo_tender` DOUBLE NULL  AFTER `dunkin_amount`;

CREATE TABLE `delivery_recap` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `store_key` varchar(255) DEFAULT NULL,
 `file_name` varchar(255) DEFAULT NULL,
 `month` varchar(20) DEFAULT NULL,
 `year` varchar(10) DEFAULT NULL,
 `cdate` datetime DEFAULT NULL,
 `day` varchar(30) DEFAULT NULL,
 `grubhub_net` double DEFAULT NULL,
 `uber_eats_transactions` double DEFAULT NULL,
 `uber_easts_amount` double DEFAULT NULL,
 `uber_easts_net_amount` double DEFAULT NULL,
 `delivery_net_recap_total_sales` double DEFAULT NULL,
 `visa_transactions` double DEFAULT NULL,
 `visa_amount` double DEFAULT NULL,
 `mastercard_transactions` double DEFAULT NULL,
 `mastercard_amount` double DEFAULT NULL,
 `american_express_transactions` double DEFAULT NULL,
 `american_express_amount` double DEFAULT NULL,
 `discover_transactions` double DEFAULT NULL,
 `discover_amount` double DEFAULT NULL,
 `order_amount` double DEFAULT NULL,
 `gift_card_amount` double DEFAULT NULL,
 `is_lock` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1=yes,0=no',
 `created_on` datetime DEFAULT CURRENT_TIMESTAMP,
 `updated_on` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4

DROP TABLE `dynamic_cardrecap_column`;
DROP TABLE `dynamic_monthlyrecap_column`;

-- sweety 15-09-2020
CREATE TABLE `bill` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `month` int(11) NOT NULL,
 `year` int(11) NOT NULL DEFAULT '0',
 `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1

CREATE TABLE `bill_item_entries` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `bill_id` int(11) NOT NULL,
 `store_key` int(11) NOT NULL,
 `store_physical_address` text NOT NULL,
 `bill_date` date NOT NULL,
 `bill_no` varchar(100) NOT NULL,
 `category_id` text NOT NULL,
 `description` int(11) NOT NULL,
 `breakdown_description` text,
 `qty` int(11) NOT NULL,
 `rate` float(16,2) NOT NULL,
 `amount` float(16,2) NOT NULL,
 `status` varchar(100) NOT NULL,
 `last_paid_date` date NOT NULL,
 `last_paid_amount` float(16,2) NOT NULL,
 `attachment` varchar(100) NOT NULL,
 `is_paid` tinyint(4) NOT NULL,
 `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`)

-- mansi 14-09-2020
CREATE TABLE `royalty` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `week_ending_date` date NOT NULL,
 `store_key` int(11) NOT NULL,
 `royal_type` enum('BR','DD') NOT NULL,
 `net_sales` double NOT NULL COMMENT '[from pos]',
 `royalty_amt` double NOT NULL,
 `adfund_amt` double NOT NULL,
 `cust_count` int(11) NOT NULL,
 `sys_eft_amt` double NOT NULL,
 `actual_eft_amt` double NOT NULL,
 `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`),
 UNIQUE KEY `week_ending_date` (`week_ending_date`,`store_key`,`royal_type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1

CREATE TABLE `admin_royalty` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `store_key` int(11) NOT NULL,
 `type` enum('BR','DD') NOT NULL,
 `royalty_percentage` int(11) NOT NULL,
 `adfund_percentage` int(11) NOT NULL,
 `customer_count_for_br` int(11) NOT NULL,
 `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`),
 UNIQUE KEY `store_key` (`store_key`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- sweety 15-09-2020
CREATE TABLE `vendor` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `username` varchar(100) DEFAULT NULL,
 `password` varchar(255) NOT NULL,
 `name_f` varchar(50) NOT NULL,
 `name_m` varchar(32) DEFAULT NULL,
 `name_l` varchar(50) NOT NULL,
 `company` varchar(100) DEFAULT NULL,
 `emp_no` varchar(50) DEFAULT NULL,
 `phys_addr1` varchar(128) DEFAULT NULL,
 `phys_addr2` varchar(128) DEFAULT NULL,
 `phys_city` varchar(128) DEFAULT NULL,
 `phys_state` varchar(2) DEFAULT NULL,
 `phys_zip` varchar(5) DEFAULT NULL,
 `phone_no` varchar(100) DEFAULT NULL,
 `website` varchar(100) DEFAULT NULL,
 `email` varchar(100) DEFAULT NULL,
 `notes` blob,
 `schedule_payment_date` date NOT NULL,
 `bill_due_date` date NOT NULL,
 `preferred_payment_method` varchar(50) DEFAULT NULL,
 `account_no` varchar(100) DEFAULT NULL,
 `recurring_period` int(11) DEFAULT NULL,
 `createdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `lastdate` datetime DEFAULT NULL,
 `updated_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `vendor_attachments` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `vendor_id` int(11) NOT NULL,
 `file_md5` longtext NOT NULL,
 `filename` varchar(255) NOT NULL,
 `filename_ext` varchar(22) NOT NULL,
 `size` int(11) NOT NULL,
 `module` varchar(100) DEFAULT NULL,
 `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `bill_category` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `category_name` varchar(100) NOT NULL,
 `type` enum('description','breakdown_description','week_description') NOT NULL,
 `description` text NOT NULL,
 `vendor_id` int(11) NOT NULL,
 `status` enum('A','I') NOT NULL,
 `created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_on` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `bill_breakdown_category` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `bill_category_id` int(11) NOT NULL,
 `description` text NOT NULL,
 `vendor_id` int(11) DEFAULT NULL,
 `status` enum('A','I') NOT NULL DEFAULT 'A',
 `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`),
 KEY `bill_category_id` (`bill_category_id`),
 CONSTRAINT `bill_breakdown_category_ibfk_1` FOREIGN KEY (`bill_category_id`) REFERENCES `bill_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `bill` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `month` int(11) NOT NULL,
 `year` int(11) NOT NULL DEFAULT '0',
 `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

CREATE TABLE `bill_item_entries` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `bill_id` int(11) NOT NULL,
 `store_key` int(11) NOT NULL,
 `store_physical_address` text NOT NULL,
 `bill_date` date NOT NULL,
 `bill_no` varchar(100) NOT NULL,
 `category_id` text NOT NULL,
 `description` int(11) NOT NULL,
 `breakdown_description` text,
 `qty` int(11) NOT NULL,
 `rate` float(16,2) NOT NULL,
 `amount` float(16,2) NOT NULL,
 `status` varchar(100) NOT NULL,bi
 `last_paid_date` date NOT NULL,
 `last_paid_amount` float(16,2) NOT NULL,
 `attachment` varchar(100) NOT NULL,
 `is_paid` tinyint(4) NOT NULL,
 `type` enum('description','breakdown_description') NOT NULL DEFAULT 'description',
 `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`),
 KEY `bill_id` (`bill_id`),
 CONSTRAINT `bill_item_entries_ibfk_1` FOREIGN KEY (`bill_id`) REFERENCES `bill` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `admin_settings` CHANGE `key_value` `key_value` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL


--mansi 10-10-2020
ALTER TABLE `user` CHANGE `password` `password` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
UPDATE `user` SET `password` = '$2y$10$UPULr9JipcLQ.wpZFkOKvuDh9eP88NALC9T0cMqXO0f2OsJaVOzhK' WHERE `user`.`id` = 1;

-- sweety 11-10-2020
ALTER TABLE `bill_category`  ADD `week_date_display_option` TINYINT(1) NOT NULL COMMENT '1=yes,0=no'  AFTER `vendor_id`;
ALTER TABLE `bill_category`  ADD `is_display_calender` INT(1) NOT NULL DEFAULT '0' COMMENT '1=yes,0=no'  AFTER `week_date_display_option`;
ALTER TABLE `bill_category` CHANGE `week_date_display_option` `week_date_display_option` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '1=yes,0=no';

ALTER TABLE `labor_summary`  ADD `covid` DOUBLE NOT NULL  AFTER `bonus`;

-- mansi 09-02-2020
ALTER TABLE `pos_master_key` ADD UNIQUE( `master_key`, `key_name`);
ALTER TABLE `pos_master_key` ADD UNIQUE( `key_label`);

-- START: Darshan 14-02-2020
ALTER TABLE `admin_year_setting` ADD `week_position` TINYINT NOT NULL DEFAULT '1' AFTER `year_weeks`, ADD `is_shifted` TINYINT NULL AFTER `week_position`;

ALTER TABLE `master_pos_daily` ADD `file_id` INT UNSIGNED NOT NULL COMMENT 'File Id' AFTER `id`;

ALTER TABLE `master_pos_weekly` ADD `file_id` INT UNSIGNED NOT NULL COMMENT 'File Id' AFTER `id`;

ALTER TABLE `card_recap` DROP `file_name`;
ALTER TABLE `card_recap` ADD `file_id` INT UNSIGNED NOT NULL COMMENT 'File Id' AFTER `id`;

ALTER TABLE `monthly_recap` DROP `file_name`;
ALTER TABLE `monthly_recap` ADD `file_id` INT UNSIGNED NOT NULL COMMENT 'File Id' AFTER `id`;

ALTER TABLE `delivery_recap` DROP `file_name`;
ALTER TABLE `delivery_recap` ADD `file_id` INT UNSIGNED NOT NULL COMMENT 'File Id' AFTER `id`;

ALTER TABLE `master_payroll` DROP `file_name`;
ALTER TABLE `master_payroll` ADD `file_id` INT UNSIGNED NOT NULL COMMENT 'File Id' AFTER `id`;

ALTER TABLE `paid_out_recap` DROP `file_name`;
ALTER TABLE `paid_out_recap` ADD `file_id` INT UNSIGNED NOT NULL COMMENT 'File Id' AFTER `id`;

ALTER TABLE `customer_review` ADD `file_id` INT UNSIGNED NOT NULL COMMENT 'File Id' AFTER `id`;
-- END: Darshan 14-02-2020

-- START: Darshan 05-05-2021
CREATE TABLE `file_history` (
  `file_id` int(10) UNSIGNED NOT NULL COMMENT 'File ID',
  `file_name` varchar(255) NOT NULL COMMENT 'File Name',
  `file_type` varchar(255) NOT NULL COMMENT 'File Type',
  `file_path` text NOT NULL COMMENT 'File Path',
  `upload_at` datetime NOT NULL COMMENT 'Uploaded At'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='File History';

ALTER TABLE `file_history`
  ADD PRIMARY KEY (`file_id`);

ALTER TABLE `file_history`
  MODIFY `file_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'File ID';
-- END: Darshan 05-05-2021
