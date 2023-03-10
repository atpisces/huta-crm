<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'pur_unit')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() .'pur_unit` (
  `unit_id` INT(11) NOT NULL AUTO_INCREMENT,
  `unit_name` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`unit_id`));');
}
if (!$CI->db->table_exists(db_prefix() . 'pur_vendor')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "pur_vendor` (
      `userid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `company` varchar(200) NULL,
      `vat` varchar(200) NULL,
      `phonenumber` varchar(30) NULL,
      `country` int(11) NOT NULL DEFAULT '0',
      `city` varchar(100) NULL,
      `zip` varchar(15) NULL,
      `state` varchar(50) NULL,
      `address` varchar(100) NULL,
      `website` varchar(150) NULL,
      `datecreated` DATETIME NOT NULL,
      `active` INT(11) NOT NULL DEFAULT '1',
      `leadid` INT(11) NULL,
      `billing_street` varchar(200) NULL,
      `billing_city` varchar(100) NULL,
      `billing_state` varchar(100) NULL,
      `billing_zip` varchar(100) NULL,
      `billing_country` int(11) NULL DEFAULT '0',
      `shipping_street` varchar(200) NULL,
      `shipping_city` varchar(100) NULL,
      `shipping_state` varchar(100) NULL,
      `shipping_zip` varchar(100) NULL,
      `shipping_country` int(11) NULL DEFAULT '0',
      `longitude` varchar(191) NULL,
      `latitude` varchar(191) NULL,
      `default_language` varchar(40) NULL,
      `default_currency` INT(11) NOT NULL DEFAULT '0',
      `show_primary_contact` INT(11) NOT NULL DEFAULT '0',
      `stripe_id` varchar(40) NULL,
      `registration_confirmed` INT(11) NOT NULL DEFAULT '1',
      `addedfrom` INT(11) NOT NULL DEFAULT '0',
      PRIMARY KEY (`userid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'pur_contacts')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "pur_contacts` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `userid` int(11) NOT NULL,
      `is_primary` int(11) NOT NULL DEFAULT '1',
      `firstname` varchar(191) NOT NULL,
      `lastname` VARCHAR(191) NOT NULL,
      `email` varchar(100) NOT NULL,
      `phonenumber` varchar(100) NOT NULL,
      `title` varchar(100) NULL,
      `datecreated` datetime NOT NULL,
      `password` varchar(255) NULL,
      `new_pass_key` varchar(32) NULL,
      `new_pass_key_requested` datetime NULL,
      `email_verified_at` datetime NULL,
      `email_verification_key` varchar(32) NULL,
      `email_verification_sent_at` DATETIME NULL,
      `last_ip` varchar(40) NULL,
      `last_login` DATETIME NULL,
      `last_password_change` DATETIME NULL,
      `active` TINYINT(1) NOT NULL DEFAULT '1',
      `profile_image` varchar(191) NULL,
      `direction` varchar(3) NULL,
      `invoice_emails` TINYINT(1) NOT NULL DEFAULT '1',
      `estimate_emails` TINYINT(1) NOT NULL DEFAULT '1',
      `credit_note_emails` TINYINT(1) NOT NULL DEFAULT '1',
      `contract_emails` TINYINT(1) NOT NULL DEFAULT '1',
      `task_emails` TINYINT(1) NOT NULL DEFAULT '1',
      `project_emails` TINYINT(1) NOT NULL DEFAULT '1',
      `ticket_emails` TINYINT(1) NOT NULL DEFAULT '1',
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'pur_vendor_admin')) {
    $CI->db->query('CREATE TABLE `'.db_prefix()."pur_vendor_admin` (
  `staff_id` INT(11) NOT NULL,
  `vendor_id` INT(11) NOT NULL,
  `date_assigned` DATETIME NOT NULL);");
}

if (!$CI->db->table_exists(db_prefix() . 'pur_approval_setting')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() .'pur_approval_setting` (
    `id` INT NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL,
	`related` VARCHAR(255) NOT NULL,
	`setting` LONGTEXT NOT NULL,
	PRIMARY KEY (`id`));');
}

if (!$CI->db->table_exists(db_prefix() . 'pur_approval_details')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() .'pur_approval_details` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `rel_id` INT(11) NOT NULL,
  `rel_type` VARCHAR(45) NOT NULL,
  `staffid` VARCHAR(45) NULL,
  `approve` VARCHAR(45) NULL,
  `note` TEXT NULL,
  `date` DATETIME NULL,
  `approve_action` VARCHAR(255) NULL,
  `reject_action` VARCHAR(255) NULL,
  `approve_value` VARCHAR(255) NULL,
  `reject_value` VARCHAR(255) NULL,
  `staff_approve` INT(11) NULL,
  `action` VARCHAR(45) NULL,
  PRIMARY KEY (`id`));');
}

if (!$CI->db->table_exists(db_prefix() . 'pur_request')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() .'pur_request` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `pur_rq_code` VARCHAR(45) NOT NULL,
  `pur_rq_name` VARCHAR(100) NOT NULL,
  `rq_description` TEXT NULL,
  `requester` INT(11) NOT NULL,
  `department` INT(11) NOT NULL,
  `request_date` DATETIME NOT NULL,
  `status` INT(11) NULL,
  `status_goods` INT(11) NOT NULL DEFAULT "0", 
  PRIMARY KEY (`id`));');
}

if (!$CI->db->table_exists(db_prefix() . 'pur_request_detail')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() .'pur_request_detail` (
  `prd_id` INT(11) NOT NULL AUTO_INCREMENT,
  `pur_request` INT(11) NOT NULL,
  `item_code` VARCHAR(100) NOT NULL,
  `unit_id` INT(11) NULL,
  `unit_price` DECIMAL(15,0) NULL,
  `quantity` int(11) NOT NULL,
  `into_money` DECIMAL(15,0) NULL,
  `inventory_quantity` int(11) NOT NULL DEFAULT "0",
  PRIMARY KEY (`prd_id`));');
}

if (!$CI->db->table_exists(db_prefix() . 'pur_estimates')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "pur_estimates` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `sent` TINYINT(1) NOT NULL DEFAULT '0',
      `datesend` DATETIME NULL,
      `vendor` INT(11) NOT NULL,
      `deleted_vendor_name` VARCHAR(100) NULL,
      `pur_request` INT(11) NOT NULL,
      `number` INT(11) NOT NULL,
      `prefix` varchar(50) NULL,
      `number_format` INT(11) NOT NULL DEFAULT '0',
      `hash` VARCHAR(32) NULL,
      `datecreated` DATETIME NOT NULL,
      `date` DATE NOT NULL,
      `expirydate` DATE NULL,
      `currency` INT(11) NOT NULL,
      `subtotal` DECIMAL(15,2) NOT NULL,
      `total_tax` DECIMAL(15,2) NOT NULL,
      `total` DECIMAL(15,2) NOT NULL,
      `adjustment` DECIMAL(15,2) NULL,
      `addedfrom` INT(11) NOT NULL,
      `status` INT(11) NOT NULL DEFAULT '1',
      `vendornote` TEXT NULL,
      `adminnote` TEXT NULL,
      `discount_percent` DECIMAL(15,2) NULL DEFAULT '0.00',
      `discount_total` DECIMAL(15,2) NULL DEFAULT '0.00',
      `discount_type` VARCHAR(30) NULL,
      `invoiceid` INT(11) NULL,
      `invoiced_date` DATETIME NULL,
      `terms` TEXT NULL,
      `reference_no` VARCHAR(100) NULL,
      `buyer` INT(11) NOT NULL DEFAULT '0',
      `billing_street` VARCHAR(200) NULL,
      `billing_city` VARCHAR(100) NULL,
      `billing_state` VARCHAR(100) NULL,
      `billing_zip` VARCHAR(100) NULL,
      `billing_country` INT(11) NULL,
      `shipping_street` VARCHAR(200) NULL,
      `shipping_city` VARCHAR(100) NULL,
      `shipping_state` VARCHAR(100) NULL,
      `shipping_zip` VARCHAR(100) NULL,
      `shipping_country` INT(11) NULL,
      `include_shipping` TINYINT(1) NOT NULL,
      `show_shipping_on_estimate` TINYINT(1) NOT NULL DEFAULT '1',
      `show_quantity_as` INT(11) NOT NULL DEFAULT '1',
      `pipeline_order` INT(11) NOT NULL DEFAULT '0',
      `is_expiry_notified` INT(11) NOT NULL DEFAULT '0',
      `acceptance_firstname` VARCHAR(50) NULL,
      `acceptance_lastname` VARCHAR(50) NULL,
      `acceptance_email` VARCHAR(100) NULL,
      `acceptance_date` DATETIME NULL,
      `acceptance_ip` VARCHAR(40) NULL,
      `signature` VARCHAR(40) NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'pur_estimate_detail')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() .'pur_estimate_detail` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `pur_estimate` INT(11) NOT NULL,
  `item_code` VARCHAR(100) NOT NULL,
  `unit_id` INT(11) NULL,
  `unit_price` DECIMAL(15,0) NULL,
  `quantity` int(11) NOT NULL,
  `into_money` DECIMAL(15,0) NULL,
  `tax` text NULL,
  `total` DECIMAL(15,0) NULL,
  PRIMARY KEY (`id`));');
}

if (!$CI->db->field_exists('discount_%' ,db_prefix() . 'pur_estimate_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_estimate_detail`
    ADD COLUMN `discount_%` DECIMAL(15,0) NULL AFTER `total`
  ;");
}

if (!$CI->db->field_exists('discount_money' ,db_prefix() . 'pur_estimate_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_estimate_detail`
    ADD COLUMN `discount_money` DECIMAL(15,0) NULL AFTER `total`
  ;");
}

if (!$CI->db->field_exists('total_money' ,db_prefix() . 'pur_estimate_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_estimate_detail`
    ADD COLUMN `total_money` DECIMAL(15,0) NULL AFTER `total`
  ;");
}

if (!$CI->db->table_exists(db_prefix() . 'pur_orders')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "pur_orders` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `pur_order_name` varchar(100) NOT NULL,
      `vendor` INT(11) NOT NULL,
      `estimate` INT(11) NOT NULL,
      `pur_order_number` VARCHAR(30) NOT NULL,
      `order_date` date NOT NULL,
      `status` INT(32) NOT NULL DEFAULT '1',
      `approve_status` INT(32) NOT NULL DEFAULT '1',
      `datecreated` DATETIME NOT NULL,
      `days_owed` INT(11) NOT NULL,
      `delivery_date` DATE NULL,
      `subtotal` DECIMAL(15,2) NOT NULL,
      `total_tax` DECIMAL(15,2) NOT NULL,
      `total` DECIMAL(15,2) NOT NULL,
      `addedfrom` INT(11) NOT NULL,
      `vendornote` TEXT NULL,
      `terms` TEXT NULL,
      `discount_percent` DECIMAL(15,2) NULL DEFAULT '0.00',
      `discount_total` DECIMAL(15,2) NULL DEFAULT '0.00',
      `discount_type` VARCHAR(30) NULL,
      `buyer` INT(11) NOT NULL DEFAULT '0',
      `status_goods` INT(11) NOT NULL DEFAULT '0', 
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'pur_order_detail')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() .'pur_order_detail` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `pur_order` INT(11) NOT NULL,
  `item_code` VARCHAR(100) NOT NULL,
  `unit_id` INT(11) NULL,
  `unit_price` DECIMAL(15,0) NULL,
  `quantity` int(11) NOT NULL,
  `into_money` DECIMAL(15,0) NULL,
  `tax` text NULL,
  `total` DECIMAL(15,0) NULL,
  `discount_%` DECIMAL(15,0) NULL,
  `discount_money` DECIMAL(15,0) NULL,
  `total_money` DECIMAL(15,0) NULL,
  PRIMARY KEY (`id`));');
}

if (!$CI->db->table_exists(db_prefix() . 'pur_contracts')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "pur_contracts` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `contract_number` varchar(200) NOT NULL,
      `contract_name` varchar(200) NOT NULL,
      `content` LONGTEXT NULL,
      `vendor` INT(11) NOT NULL,
      `pur_order` INT(11) NOT NULL,
      `contract_value` DECIMAL(15,0) NOT NULL,
      `start_date` date NOT NULL,
      `end_date` date NULL,
      `buyer` INT(11) NULL,
      `time_payment`date NULL,
      `add_from` INT(11) NOT NULL,
      `signed` INT(32) NOT NULL DEFAULT '0',
      `note` LONGTEXT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('contract_value' ,db_prefix() . 'pur_contracts')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_contracts`
    ADD COLUMN `contract_value` DECIMAL(15,0) NULL AFTER `pur_order`
  ;");
}

if (!$CI->db->field_exists('signed_status' ,db_prefix() . 'pur_contracts')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_contracts`
    ADD COLUMN `signed_status` varchar(50) NULL AFTER `note`
  ;");
}

if (!$CI->db->field_exists('signed_date' ,db_prefix() . 'pur_contracts')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_contracts`
    ADD COLUMN `signed_date` DATE NULL AFTER `note`
  ;");
}

if (!$CI->db->field_exists('signer' ,db_prefix() . 'pur_contracts')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_contracts`
    ADD COLUMN `signer` INT(11) NULL AFTER `note`
  ;");
}


if (!$CI->db->field_exists('sender', db_prefix() .'pur_approval_details')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'pur_approval_details` 
ADD COLUMN `sender` INT(11) NULL AFTER `action`,
ADD COLUMN `date_send` DATETIME NULL AFTER `sender`;');            
}

if (!$CI->db->table_exists(db_prefix() . 'purchase_option')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "purchase_option` (
      `option_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `option_name` varchar(200) NOT NULL,
      `option_val` longtext NULL,
      `auto` tinyint(1) NULL,
      PRIMARY KEY (`option_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (row_purchase_options_exist('"purchase_order_setting"') == 0){
  $CI->db->query('INSERT INTO `tblpurchase_option` (`option_name`, `option_val`, `auto`) VALUES ("purchase_order_setting", "1", "1");
');
}

if (!$CI->db->table_exists(db_prefix() . 'pur_order_payment')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "pur_order_payment` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `pur_order` int(11) NOT NULL,
      `amount` DECIMAL(15,2) NOT NULL,
      `paymentmode` LONGTEXT NULL,
      `date` DATE NOT NULL,
      `daterecorded` DATETIME NOT NULL,
      `note` TEXT NOT NULL,
      `transactionid` MEDIUMTEXT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('commodity_code' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
  ADD COLUMN `commodity_code` varchar(100) NOT NULL,
  ADD COLUMN `commodity_barcode` text NULL,
  ADD COLUMN `unit_id` int(11) NULL,
  ADD COLUMN `sku_code` varchar(200)  NULL,
  ADD COLUMN `sku_name` varchar(200)  NULL,
  ADD COLUMN `purchase_price` decimal(15,2)  NULL
  ;");
}

if (!$CI->db->table_exists(db_prefix() . 'ware_unit_type')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "ware_unit_type` (
      `unit_type_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `unit_code` varchar(100) NULL,
      `unit_name` text NULL,
      `unit_symbol` text NULL,
      `order` int(10) NULL,
      `display` int(1) NULL COMMENT  'display 1: display (yes)  0: not displayed (no)',
      `note` text NULL,
      PRIMARY KEY (`unit_type_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('commodity_group_code' ,db_prefix() . 'items_groups')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items_groups`
  ADD COLUMN `commodity_group_code` varchar(100) NULL AFTER `name`,
  ADD COLUMN `order` int(10) NULL AFTER `commodity_group_code`,
  ADD COLUMN `display` int(1)  NULL AFTER `order` ,
  ADD COLUMN `note` text NULL AFTER `display`
  ;");
}

// Version 1.0.1
if (row_purchase_options_exist('"pur_order_prefix"') == 0){
    $CI->db->query('INSERT INTO `tblpurchase_option` (`option_name`, `option_val`, `auto`) VALUES ("pur_order_prefix", "#PO", "1");
  ');
  }

  if (!$CI->db->field_exists('number', db_prefix() .'pur_orders')) {
      $CI->db->query('ALTER TABLE `'.db_prefix() . 'pur_orders` 
    ADD COLUMN `number` INT(11) NULL;');            
  }

  if (!$CI->db->field_exists('expense_convert', db_prefix() .'pur_orders')) {
      $CI->db->query('ALTER TABLE `'.db_prefix() . 'pur_orders` 
    ADD COLUMN `expense_convert` INT(11) NULL DEFAULT "0";');            
  }

// Version 1.0.2
if (!$CI->db->field_exists('vendor', db_prefix() .'expenses')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'expenses` 
  ADD COLUMN `vendor` INT(11) NULL;');            
}

// Version 1.0.3
if (!$CI->db->table_exists(db_prefix() . 'pur_vendor_items')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "pur_vendor_items` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `vendor` int(11) NOT NULL,
    `group_items` int(11) NULL,
    `items` int(11) NOT NULL,
    `add_from` int(11) NULL,
    `datecreate` DATE NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

// Version 1.0.4
if (!$CI->db->field_exists('description' ,db_prefix() . 'pur_order_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_order_detail`
    ADD COLUMN `description` TEXT NULL AFTER `item_code`
  ;");
}

if (!$CI->db->field_exists('commodity_group_code' ,db_prefix() . 'items_groups')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items_groups`
  ADD COLUMN `commodity_group_code` varchar(100) NULL AFTER `name`,
  ADD COLUMN `order` int(10) NULL AFTER `commodity_group_code`,
  ADD COLUMN `display` int(1)  NULL AFTER `order` ,
  ADD COLUMN `note` text NULL AFTER `display`
  ;");
}

if (!$CI->db->table_exists(db_prefix() . 'wh_sub_group')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "wh_sub_group` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `sub_group_code` varchar(100) NULL,
      `sub_group_name` text NULL,
      `order` int(10) NULL,
      `display` int(1) NULL COMMENT  'display 1: display (yes)  0: not displayed (no)',
      `note` text NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('group_id' ,db_prefix() . 'wh_sub_group')) { 
    $CI->db->query('ALTER TABLE `' . db_prefix() . "wh_sub_group`
        ADD COLUMN `group_id` int(11)  NULL
    ;");
  } 

if (!$CI->db->field_exists('sub_group' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
      ADD COLUMN `sub_group` varchar(200)  NULL
  ;");
}

//version 1.0.5, update decimal 15,0 to 15,2

        //purchase request detail
        if ($CI->db->field_exists('unit_price' ,db_prefix() . 'pur_request_detail')) { 
      $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request_detail`
        CHANGE COLUMN `unit_price` `unit_price` DECIMAL(15,2) NULL DEFAULT NULL
      ;");
    }

    if ($CI->db->field_exists('into_money' ,db_prefix() . 'pur_request_detail')) { 
      $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request_detail`
        CHANGE COLUMN `into_money` `into_money` DECIMAL(15,2) NULL DEFAULT NULL
      ;");
    }

    //purchase order detail
    if ($CI->db->field_exists('unit_price' ,db_prefix() . 'pur_order_detail')) { 
      $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_order_detail`
        CHANGE COLUMN `unit_price` `unit_price` DECIMAL(15,2) NULL DEFAULT NULL
      ;");
    }

    if ($CI->db->field_exists('into_money' ,db_prefix() . 'pur_order_detail')) { 
      $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_order_detail`
        CHANGE COLUMN `into_money` `into_money` DECIMAL(15,2) NULL DEFAULT NULL
      ;");
    }

    if ($CI->db->field_exists('total' ,db_prefix() . 'pur_order_detail')) { 
      $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_order_detail`
        CHANGE COLUMN `total` `total` DECIMAL(15,2) NULL DEFAULT NULL
      ;");
    }

    if ($CI->db->field_exists('discount_%' ,db_prefix() . 'pur_order_detail')) { 
      $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_order_detail`
        CHANGE COLUMN `discount_%` `discount_%` DECIMAL(15,2) NULL DEFAULT NULL
      ;");
    }

    if ($CI->db->field_exists('discount_money' ,db_prefix() . 'pur_order_detail')) { 
      $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_order_detail`
        CHANGE COLUMN `discount_money` `discount_money` DECIMAL(15,2) NULL DEFAULT NULL
      ;");
    }

    if ($CI->db->field_exists('total_money' ,db_prefix() . 'pur_order_detail')) { 
      $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_order_detail`
        CHANGE COLUMN `total_money` `total_money` DECIMAL(15,2) NULL DEFAULT NULL
      ;");
    }

    //pur estimate detail
    if ($CI->db->field_exists('unit_price' ,db_prefix() . 'pur_contracts')) { 
      $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_estimate_detail`
        CHANGE COLUMN `unit_price` `unit_price` DECIMAL(15,2) NULL DEFAULT NULL
      ;");
    }

    if ($CI->db->field_exists('into_money' ,db_prefix() . 'pur_estimate_detail')) { 
      $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_estimate_detail`
        CHANGE COLUMN `into_money` `into_money` DECIMAL(15,2) NULL DEFAULT NULL
      ;");
    }

    if ($CI->db->field_exists('total' ,db_prefix() . 'pur_estimate_detail')) { 
      $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_estimate_detail`
        CHANGE COLUMN `total` `total` DECIMAL(15,2) NULL DEFAULT NULL
      ;");
    }

    if ($CI->db->field_exists('total_money' ,db_prefix() . 'pur_estimate_detail')) { 
      $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_estimate_detail`
        CHANGE COLUMN `total_money` `total_money` DECIMAL(15,2) NULL DEFAULT NULL
      ;");
    }

    if ($CI->db->field_exists('discount_money' ,db_prefix() . 'pur_estimate_detail')) { 
      $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_estimate_detail`
        CHANGE COLUMN `discount_money` `discount_money` DECIMAL(15,2) NULL DEFAULT NULL
      ;");
    }

    if ($CI->db->field_exists('discount_%' ,db_prefix() . 'pur_estimate_detail')) { 
      $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_estimate_detail`
        CHANGE COLUMN `discount_%` `discount_%` DECIMAL(15,2) NULL DEFAULT NULL
      ;");
    }

    //pur contract
    if ($CI->db->field_exists('contract_value' ,db_prefix() . 'pur_contracts')) { 
      $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_contracts`
        CHANGE COLUMN `contract_value` `contract_value` DECIMAL(15,2) NULL DEFAULT NULL
      ;");
    }

// purchase request hash
if (!$CI->db->field_exists('hash' ,db_prefix() . 'pur_request')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request`
    ADD COLUMN `hash` VARCHAR(32) NULL
  ;");
}

// purchase order hash
if (!$CI->db->field_exists('hash' ,db_prefix() . 'pur_orders')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_orders`
    ADD COLUMN `hash` VARCHAR(32) NULL
  ;");
}

// version 1.0.6  purchase order client
if (!$CI->db->field_exists('clients' ,db_prefix() . 'pur_orders')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_orders`
    ADD COLUMN `clients` TEXT NULL
  ;");
}

// version 1.0.6  purchase request fix
if ($CI->db->field_exists('inventory_quantity' ,db_prefix() . 'pur_request_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request_detail`
    CHANGE COLUMN `inventory_quantity` `inventory_quantity` INT(11) NULL DEFAULT '0'
  ;");
}

//version 1.0.7 vendor category table
if (!$CI->db->table_exists(db_prefix() . 'pur_vendor_cate')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "pur_vendor_cate` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `category_name` VARCHAR(255) NULL,
      `description` text NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

//version 1.0.7 additional field vendor
if (!$CI->db->field_exists('category' ,db_prefix() . 'pur_vendor')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_vendor`
      ADD COLUMN `category` TEXT  NULL
  ;");
}

if (!$CI->db->field_exists('bank_detail' ,db_prefix() . 'pur_vendor')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_vendor`
      ADD COLUMN `bank_detail` TEXT  NULL
  ;");
}

if (!$CI->db->field_exists('payment_terms' ,db_prefix() . 'pur_vendor')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_vendor`
      ADD COLUMN `payment_terms` TEXT  NULL
  ;");
}

if (!$CI->db->field_exists('vendor_code' ,db_prefix() . 'pur_vendor')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_vendor`
      ADD COLUMN `vendor_code` VARCHAR(100)  NULL
  ;");
}

//version 1.0.7 additional field purchase request

if (!$CI->db->field_exists('type' ,db_prefix() . 'pur_request')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request`
      ADD COLUMN `type` TEXT  NULL
  ;");
}

if (!$CI->db->field_exists('project' ,db_prefix() . 'pur_request')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request`
      ADD COLUMN `project` INT(11)  NULL
  ;");
}

if (!$CI->db->field_exists('number' ,db_prefix() . 'pur_request')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request`
      ADD COLUMN `number` INT(11)  NULL
  ;");
}

if (!$CI->db->field_exists('from_items' ,db_prefix() . 'pur_request')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request`
      ADD COLUMN `from_items` INT(2)  NULL DEFAULT '1'
  ;");
}


//version 1.0.7 additional field purchase order

if (!$CI->db->field_exists('delivery_status' ,db_prefix() . 'pur_orders')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_orders`
      ADD COLUMN `delivery_status` int(2)  NULL DEFAULT '0'
  ;");
}

if (!$CI->db->field_exists('type' ,db_prefix() . 'pur_orders')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_orders`
      ADD COLUMN `type` TEXT  NULL
  ;");
}

if (!$CI->db->field_exists('project' ,db_prefix() . 'pur_orders')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_orders`
      ADD COLUMN `project` INT(11)  NULL
  ;");
}

if (!$CI->db->field_exists('pur_request' ,db_prefix() . 'pur_orders')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_orders`
      ADD COLUMN `pur_request` INT(11)  NULL
  ;");
}

if (!$CI->db->field_exists('department' ,db_prefix() . 'pur_orders')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_orders`
      ADD COLUMN `department` INT(11)  NULL
  ;");
}

if (!$CI->db->field_exists('tax_order_rate' ,db_prefix() . 'pur_orders')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_orders`
      ADD COLUMN `tax_order_rate` DECIMAL(15,2)  NULL
  ;");
}

if (!$CI->db->field_exists('tax_order_amount' ,db_prefix() . 'pur_orders')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_orders`
      ADD COLUMN `tax_order_amount` DECIMAL(15,2)  NULL
  ;");
}

//version 1.0.7 Purchase option

if (row_purchase_options_exist('"next_po_number"') == 0){
    $CI->db->query('INSERT INTO `tblpurchase_option` (`option_name`, `option_val`, `auto`) VALUES ("next_po_number", "1", "1");
  ');
}

if (row_purchase_options_exist('"date_reset_number"') == 0){
    $CI->db->query('INSERT INTO `tblpurchase_option` (`option_name`, `option_val`, `auto`) VALUES ("date_reset_number", "", "1");
  ');
}

if (row_purchase_options_exist('"pur_request_prefix"') == 0){
    $CI->db->query('INSERT INTO `tblpurchase_option` (`option_name`, `option_val`, `auto`) VALUES ("pur_request_prefix", "#PR", "1");
  ');
}

if (row_purchase_options_exist('"next_pr_number"') == 0){
    $CI->db->query('INSERT INTO `tblpurchase_option` (`option_name`, `option_val`, `auto`) VALUES ("next_pr_number", "1", "1");
  ');
}

if (row_purchase_options_exist('"date_reset_pr_number"') == 0){
    $CI->db->query('INSERT INTO `tblpurchase_option` (`option_name`, `option_val`, `auto`) VALUES ("date_reset_pr_number", "", "1");
  ');
}

//version 1.0.7 Purchase request detail
if (!$CI->db->field_exists('item_text' ,db_prefix() . 'pur_request_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request_detail`
      ADD COLUMN `item_text` TEXT  NULL
  ;");
}
// version 1.0.7 Contract
if (!$CI->db->field_exists('service_category' ,db_prefix() . 'pur_contracts')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_contracts`
    ADD COLUMN `service_category` TEXT NULL
  ;");
}

if (!$CI->db->field_exists('project' ,db_prefix() . 'pur_contracts')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_contracts`
    ADD COLUMN `project` INT(11) NULL
  ;");
}

if (!$CI->db->field_exists('payment_terms' ,db_prefix() . 'pur_contracts')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_contracts`
    ADD COLUMN `payment_terms` TEXT NULL
  ;");
}

if (!$CI->db->field_exists('payment_amount' ,db_prefix() . 'pur_contracts')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_contracts`
    ADD COLUMN `payment_amount` DECIMAL(15,2) NULL
  ;");
}

if (!$CI->db->field_exists('payment_cycle' ,db_prefix() . 'pur_contracts')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_contracts`
    ADD COLUMN `payment_cycle` VARCHAR(50) NULL
  ;");
}
if (!$CI->db->field_exists('department' ,db_prefix() . 'pur_contracts')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_contracts`
    ADD COLUMN `department` INT(11) NULL
  ;");
}

// version 1.0.7 Create table pur_invoices
if (!$CI->db->table_exists(db_prefix() . 'pur_invoices')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "pur_invoices` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `number` int(11) NOT NULL,
      `invoice_number` TEXT NULL,
      `invoice_date` DATE NULL,
      `subtotal` DECIMAL(15,2) NULL,
      `tax_rate` INT(11) NULL,
      `tax` DECIMAL(15,2) NULL,
      `total` DECIMAL(15,2) NULL,
      `contract` int(11) NULL,
      `vendor` int(11) NULL,
      `transactionid` MEDIUMTEXT NULL,
      `transaction_date` DATE NULL,
      `payment_request_status` VARCHAR(30) NULL,
      `payment_status` VARCHAR(30) NULL,
      `vendor_note` TEXT NULL, 
      `adminnote` TEXT NULL, 
      `terms` TEXT NULL,
      `add_from` INT(11) NULL,
      `date_add` DATE NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (row_purchase_options_exist('"pur_inv_prefix"') == 0){
    $CI->db->query('INSERT INTO `tblpurchase_option` (`option_name`, `option_val`, `auto`) VALUES ("pur_inv_prefix", "#INV", "1");
  ');
}

if (row_purchase_options_exist('"next_inv_number"') == 0){
    $CI->db->query('INSERT INTO `tblpurchase_option` (`option_name`, `option_val`, `auto`) VALUES ("next_inv_number", "1", "1");
  ');
}

if ($CI->db->field_exists('contract' ,db_prefix() . 'pur_invoices')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_invoices`
    CHANGE COLUMN `contract` `contract` INT(11) NULL
  ;");
}

if ($CI->db->field_exists('vendor' ,db_prefix() . 'pur_invoices')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_invoices`
    CHANGE COLUMN `vendor` `vendor` INT(11) NULL
  ;");
}

if (!$CI->db->field_exists('pur_order' ,db_prefix() . 'pur_invoices')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_invoices`
    ADD COLUMN `pur_order` INT(11) NULL
  ;");
}

if (row_purchase_options_exist('"create_invoice_by"') == 0){
    $CI->db->query('INSERT INTO `tblpurchase_option` (`option_name`, `option_val`, `auto`) VALUES ("create_invoice_by", "contract", "1");
  ');
}

// version 1.0.7 Create table invoices payment
if (!$CI->db->table_exists(db_prefix() . 'pur_invoice_payment')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "pur_invoice_payment` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `pur_invoice` int(11) NOT NULL,
      `amount` DECIMAL(15,2) NOT NULL,
      `paymentmode` LONGTEXT NULL,
      `date` DATE NOT NULL,
      `daterecorded` DATETIME NOT NULL,
      `note` TEXT NOT NULL,
      `transactionid` MEDIUMTEXT NULL,
      `approval_status` INT(2) NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('requester' ,db_prefix() . 'pur_invoice_payment')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_invoice_payment`
    ADD COLUMN `requester` INT(11) NULL
  ;");
}

//version 1.0.7 remove required condition for purchase request field in purchase estimate

if ($CI->db->field_exists('pur_request' ,db_prefix() . 'pur_estimates')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_estimates`
    CHANGE COLUMN `pur_request` `pur_request` INT(11) NULL
  ;");
}

//version 1.0.7 email template
create_email_template('Purchase Order', '<span style=\"font-size: 12pt;\"> Hello !. </span><br /><br /><span style=\"font-size: 12pt;\"> We would like to share with you a link of Purchase Order information with the number {po_number} </span><br /><br /><span style=\"font-size: 12pt;\"><br />Please click on the link to view information: {public_link}
  </span><br /><br />', 'purchase_order', 'Purchase Order (Sent to contact)', 'purchase-order-to-contact');

if (row_purchase_options_exist('"item_by_vendor"') == 0){
  $CI->db->query('INSERT INTO `'.db_prefix().'purchase_option` (`option_name`, `option_val`, `auto`) VALUES ("item_by_vendor", "0", "1");
');
}

// version 1.0.9 term condition & vendor note
if (row_purchase_options_exist('"terms_and_conditions"') == 0){
  $CI->db->query('INSERT INTO `'.db_prefix().'purchase_option` (`option_name`, `option_val`, `auto`) VALUES ("terms_and_conditions", "", "1");
');
}

if (row_purchase_options_exist('"vendor_note"') == 0){
  $CI->db->query('INSERT INTO `'.db_prefix().'purchase_option` (`option_name`, `option_val`, `auto`) VALUES ("vendor_note", "", "1");
');
}

// 1.1.0
if (!$CI->db->table_exists(db_prefix() . 'pur_comments')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "pur_comments` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `content` MEDIUMTEXT NULL,
      `rel_type` VARCHAR(50) NOT NULL,
      `rel_id` INT(11) NULL,
      `staffid` INT(11) NOT NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

//version 1.10 Purchase request detail
if (!$CI->db->field_exists('tax' ,db_prefix() . 'pur_request_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request_detail`
      ADD COLUMN `tax` TEXT  NULL
  ;");
}

//version 1.10 Purchase request detail
if (!$CI->db->field_exists('tax_rate' ,db_prefix() . 'pur_request_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request_detail`
      ADD COLUMN `tax_rate` TEXT  NULL
  ;");
}

//version 1.10 Purchase request detail
if (!$CI->db->field_exists('tax_value' ,db_prefix() . 'pur_request_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request_detail`
      ADD COLUMN `tax_value` DECIMAL(15,2)  NULL
  ;");
}

//version 1.10 Purchase request detail
if (!$CI->db->field_exists('total' ,db_prefix() . 'pur_request_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request_detail`
      ADD COLUMN `total` DECIMAL(15,2)  NULL
  ;");
}

if (!$CI->db->field_exists('subtotal' ,db_prefix() . 'pur_request')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request`
      ADD COLUMN `subtotal` DECIMAL(15,2) NULL
  ;");
}

if (!$CI->db->field_exists('total_tax' ,db_prefix() . 'pur_request')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request`
      ADD COLUMN `total_tax` DECIMAL(15,2) NULL
  ;");
}

if (!$CI->db->field_exists('total' ,db_prefix() . 'pur_request')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request`
      ADD COLUMN `total` DECIMAL(15,2) NULL
  ;");
}

if (!$CI->db->field_exists('sale_invoice' ,db_prefix() . 'pur_request')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request`
      ADD COLUMN `sale_invoice` int(11) NULL
  ;");
}

if (!$CI->db->field_exists('sale_invoice' ,db_prefix() . 'pur_orders')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_orders`
      ADD COLUMN `sale_invoice` int(11) NULL
  ;");
}

if (!$CI->db->field_exists('tax_value' ,db_prefix() . 'pur_order_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_order_detail`
      ADD COLUMN `tax_value` DECIMAL(15,2) NULL
  ;");
}

if (!$CI->db->field_exists('tax_rate' ,db_prefix() . 'pur_order_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_order_detail`
      ADD COLUMN `tax_rate` TEXT NULL
  ;");
}

//Ver 1.1.1
if (!$CI->db->field_exists('tax_value' ,db_prefix() . 'pur_estimate_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_estimate_detail`
      ADD COLUMN `tax_value` DECIMAL(15,2) NULL
  ;");
}

if (!$CI->db->field_exists('tax_rate' ,db_prefix() . 'pur_estimate_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_estimate_detail`
      ADD COLUMN `tax_rate` TEXT NULL
  ;");
}

//Ver 1.1.2
if ($CI->db->field_exists('quantity' ,db_prefix() . 'pur_order_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_order_detail`
      CHANGE COLUMN `quantity` `quantity` DECIMAL(15,2) NOT NULL 
  ;");
}

if ($CI->db->field_exists('quantity' ,db_prefix() . 'pur_estimate_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_estimate_detail`
       CHANGE COLUMN `quantity` `quantity` DECIMAL(15,2) NOT NULL 
  ;");
}

if ($CI->db->field_exists('quantity' ,db_prefix() . 'pur_request_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request_detail`
       CHANGE COLUMN `quantity` `quantity` DECIMAL(15,2) NOT NULL 
  ;");
}

//Ver 1.1.3
create_email_template('Purchase Request', '<span style=\"font-size: 12pt;\"> Hello !. </span><br /><br /><span style=\"font-size: 12pt;\"> We would like to share with you a link of Purchase Request information with the number {pr_number} </span><br /><br /><span style=\"font-size: 12pt;\"><br />Please click on the link to view information: {public_link}<br/ >{additional_content}
  </span><br /><br />', 'purchase_order', 'Purchase Request (Sent to contact)', 'purchase-request-to-contact');

create_email_template('Purchase Quotation', '<span style=\"font-size: 12pt;\"> Hello !. </span><br /><br /><span style=\"font-size: 12pt;\"> We would like to share with you a link of Purchase Quotation information with the number {pq_number} </span><br /><br /><span style=\"font-size: 12pt;\"><br />Please click on the link to view information: {quotation_link}<br/ >{additional_content}
  </span><br /><br />', 'purchase_order', 'Purchase Quotation (Sent to contact)', 'purchase-quotation-to-contact');

//Ver 1.1.5

if (!$CI->db->field_exists('recurring' ,db_prefix() . 'pur_invoices')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_invoices`
      ADD COLUMN `recurring` INT(11) NULL
  ;");
}

if (!$CI->db->field_exists('recurring_type' ,db_prefix() . 'pur_invoices')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_invoices`
      ADD COLUMN `recurring_type` VARCHAR(10) NULL
  ;");
}

if (!$CI->db->field_exists('cycles' ,db_prefix() . 'pur_invoices')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_invoices`
      ADD COLUMN `cycles` INT(11) NULL DEFAULT '0'
  ;");
}

if (!$CI->db->field_exists('total_cycles' ,db_prefix() . 'pur_invoices')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_invoices`
      ADD COLUMN `total_cycles` INT(11) NULL DEFAULT '0'
  ;");
}

if (!$CI->db->field_exists('last_recurring_date' ,db_prefix() . 'pur_invoices')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_invoices`
      ADD COLUMN `last_recurring_date` DATE NULL
  ;");
}

if (!$CI->db->field_exists('is_recurring_from' ,db_prefix() . 'pur_invoices')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_invoices`
      ADD COLUMN `is_recurring_from` INT(11) NULL
  ;");
}

if (!$CI->db->field_exists('duedate' ,db_prefix() . 'pur_invoices')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_invoices`
      ADD COLUMN `duedate` DATE NULL
  ;");
}

add_option('pur_invoice_auto_operations_hour', 21);

if (!$CI->db->field_exists('compare_note' ,db_prefix() . 'pur_request')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_request`
      ADD COLUMN `compare_note` text NULL
  ;");
}

if (!$CI->db->field_exists('make_a_contract' ,db_prefix() . 'pur_estimates')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_estimates`
      ADD COLUMN `make_a_contract` text NULL
  ;");
}

create_email_template('Purchase Statement', '<span style=\"font-size: 12pt;\"> Dear {contact_firstname} {contact_lastname} !. </span><br /><br /><span style=\"font-size: 12pt;\">Its been a great experience working with you. </span><br /><br /><span style=\"font-size: 12pt;\"><br />Attached with this email is a list of all transactions for the period between {statement_from} to {statement_to}<br/ ><br/ >For your information your account balance due is total:??{statement_balance_due}<br /><br/ > Please contact us if you need more information.<br/ > <br />{additional_content}
  </span><br /><br />', 'purchase_order', 'Purchase Statement (Sent to contact)', 'purchase-statement-to-contact');

add_option('show_purchase_tax_column', 1);

if ($CI->db->field_exists('address' ,db_prefix() . 'pur_vendor')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "pur_vendor`
      CHANGE COLUMN `address` `address` TEXT NULL DEFAULT NULL
  ;");
}