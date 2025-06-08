<?php

// File: modules/cases/install.php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Module Installation Script
 */

// Create case_types table
if (!$CI->db->table_exists(db_prefix() . 'case_types')) {
    $CI->db->query("
        CREATE TABLE `" . db_prefix() . "case_types` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(255) NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");
}

// Create case_parties table
if (!$CI->db->table_exists(db_prefix() . 'case_parties')) {
    $CI->db->query("
        CREATE TABLE `" . db_prefix() . "case_parties` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `case_id` INT(11) NOT NULL,
            `party_type` ENUM('Petitioner', 'Respondent'),
            `name` VARCHAR(255),
            `advocate_name` VARCHAR(255),
            `contact_details` TEXT,
            PRIMARY KEY (`id`),
            FOREIGN KEY (`case_id`) REFERENCES `" . db_prefix() . "cases`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");
}

// Create case_stages table
if (!$CI->db->table_exists(db_prefix() . 'case_stages')) {
    $CI->db->query("
        CREATE TABLE `" . db_prefix() . "case_stages` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `case_id` INT(11) NOT NULL,
            `stage` VARCHAR(255),
            `purpose` TEXT,
            `date` DATE,
            PRIMARY KEY (`id`),
            FOREIGN KEY (`case_id`) REFERENCES `" . db_prefix() . "cases`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");
}

// Create case_acts table
if (!$CI->db->table_exists(db_prefix() . 'case_acts')) {
    $CI->db->query("
        CREATE TABLE `" . db_prefix() . "case_acts` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `case_id` INT(11) NOT NULL,
            `act_name` VARCHAR(255),
            `sections` VARCHAR(255),
            PRIMARY KEY (`id`),
            FOREIGN KEY (`case_id`) REFERENCES `" . db_prefix() . "cases`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");
}

// Create case_orders table
if (!$CI->db->table_exists(db_prefix() . 'case_orders')) {
    $CI->db->query("
        CREATE TABLE `" . db_prefix() . "case_orders` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `case_id` INT(11) NOT NULL,
            `order_date` DATE,
            `order_summary` TEXT,
            `next_date` DATE,
            `purpose_next` TEXT,
            PRIMARY KEY (`id`),
            FOREIGN KEY (`case_id`) REFERENCES `" . db_prefix() . "cases`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");
}

// Create interim_applications table
if (!$CI->db->table_exists(db_prefix() . 'interim_applications')) {
    $CI->db->query("
        CREATE TABLE `" . db_prefix() . "interim_applications` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `case_id` INT(11) NOT NULL,
            `ia_number` VARCHAR(100),
            `description` TEXT,
            `status` VARCHAR(100),
            `filed_on` DATE,
            PRIMARY KEY (`id`),
            FOREIGN KEY (`case_id`) REFERENCES `" . db_prefix() . "cases`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");
}

// Create appointments table
if (!$CI->db->table_exists(db_prefix() . 'appointments')) {
    $CI->db->query("
        CREATE TABLE `" . db_prefix() . "appointments` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `client_id` INT(11) NOT NULL,
            `contact_id` INT(11) DEFAULT NULL,
            `service_id` INT(11) NOT NULL,
            `staff_id` INT(11) NOT NULL,
            `appointment_date` DATE NOT NULL,
            `start_time` TIME NOT NULL,
            `end_time` TIME NOT NULL,
            `duration_minutes` INT(11) NOT NULL,
            `status` ENUM('pending','confirmed','completed','cancelled','no_show','rescheduled') DEFAULT 'pending',
            `notes` TEXT DEFAULT NULL,
            `internal_notes` TEXT DEFAULT NULL,
            `invoice_id` INT(11) DEFAULT NULL,
            `booking_invoice_id` INT(11) DEFAULT NULL,
            `consultation_id` INT(11) DEFAULT NULL,
            `total_amount` DECIMAL(15,2) DEFAULT 0.00,
            `paid_amount` DECIMAL(15,2) DEFAULT 0.00,
            `payment_status` ENUM('unpaid','partial','paid','refunded','cancelled') DEFAULT 'unpaid',
            `payment_required` TINYINT(1) DEFAULT 0,
            `auto_invoice` TINYINT(1) DEFAULT 1,
            `booked_by` ENUM('client','staff','online') DEFAULT 'staff',
            `booking_source` VARCHAR(50) DEFAULT 'dashboard',
            `confirmation_key` VARCHAR(32) DEFAULT NULL,
            `cancellation_reason` TEXT DEFAULT NULL,
            `rescheduled_from` INT(11) DEFAULT NULL,
            `confirmation_sent` TINYINT(1) DEFAULT 0,
            `reminder_sent` TINYINT(1) DEFAULT 0,
            `reminder_count` INT(11) DEFAULT 0,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
            `updated_at` DATETIME DEFAULT NULL,
            `created_by` INT(11) DEFAULT NULL,
            `updated_by` INT(11) DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");
}

// Create appointment_services table
if (!$CI->db->table_exists(db_prefix() . 'appointment_services')) {
    $CI->db->query("
        CREATE TABLE `" . db_prefix() . "appointment_services` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(255) NOT NULL,
            `description` TEXT DEFAULT NULL,
            `duration_minutes` INT(11) NOT NULL DEFAULT 60,
            `price` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
            `currency` INT(11) NOT NULL DEFAULT 1,
            `tax_id` INT(11) DEFAULT NULL,
            `tax_id_2` INT(11) DEFAULT NULL,
            `item_id` INT(11) DEFAULT NULL,
            `requires_prepayment` TINYINT(1) NOT NULL DEFAULT 0,
            `booking_fee` DECIMAL(15,2) DEFAULT 0.00,
            `cancellation_fee` DECIMAL(15,2) DEFAULT 0.00,
            `color` VARCHAR(7) DEFAULT '#1a6bcc',
            `active` TINYINT(1) NOT NULL DEFAULT 1,
            `sort_order` INT(11) DEFAULT 0,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
            `updated_at` DATETIME DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");
}

// Create appointment_staff_services table
if (!$CI->db->table_exists(db_prefix() . 'appointment_staff_services')) {
    $CI->db->query("
        CREATE TABLE `" . db_prefix() . "appointment_staff_services` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `staff_id` INT(11) NOT NULL,
            `service_id` INT(11) NOT NULL,
            `custom_price` DECIMAL(15,2) DEFAULT NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");
}

// Create appointment_staff_availability table
if (!$CI->db->table_exists(db_prefix() . 'appointment_staff_availability')) {
    $CI->db->query("
        CREATE TABLE `" . db_prefix() . "appointment_staff_availability` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `staff_id` INT(11) NOT NULL,
            `day_of_week` TINYINT(1) NOT NULL,
            `start_time` TIME NOT NULL,
            `end_time` TIME NOT NULL,
            `break_start` TIME DEFAULT NULL,
            `break_end` TIME DEFAULT NULL,
            `is_available` TINYINT(1) DEFAULT 1,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
            `updated_at` DATETIME DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");
}

// Create appointment_blocked_times table
if (!$CI->db->table_exists(db_prefix() . 'appointment_blocked_times')) {
    $CI->db->query("
        CREATE TABLE `" . db_prefix() . "appointment_blocked_times` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `staff_id` INT(11) NOT NULL,
            `start_datetime` DATETIME NOT NULL,
            `end_datetime` DATETIME NOT NULL,
            `reason` VARCHAR(255) DEFAULT NULL,
            `is_recurring` TINYINT(1) DEFAULT 0,
            `recurring_type` ENUM('daily','weekly','monthly') DEFAULT NULL,
            `recurring_until` DATE DEFAULT NULL,
            `created_by` INT(11) NOT NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");
}

// Create appointment_payments table
if (!$CI->db->table_exists(db_prefix() . 'appointment_payments')) {
    $CI->db->query("
        CREATE TABLE `" . db_prefix() . "appointment_payments` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `appointment_id` INT(11) NOT NULL,
            `invoice_id` INT(11) NOT NULL,
            `payment_type` ENUM('booking_fee','service_fee','full_payment','cancellation_fee','refund') NOT NULL,
            `amount` DECIMAL(15,2) NOT NULL,
            `payment_date` DATETIME DEFAULT NULL,
            `payment_method` VARCHAR(50) DEFAULT NULL,
            `transaction_id` VARCHAR(255) DEFAULT NULL,
            `notes` TEXT DEFAULT NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");
}

// Create appointment_settings table
if (!$CI->db->table_exists(db_prefix() . 'appointment_settings')) {
    $CI->db->query("
        CREATE TABLE `" . db_prefix() . "appointment_settings` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `setting_name` VARCHAR(100) NOT NULL,
            `setting_value` TEXT DEFAULT NULL,
            `setting_type` VARCHAR(50) DEFAULT 'text',
            `description` TEXT DEFAULT NULL,
            `updated_at` DATETIME DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");
}
