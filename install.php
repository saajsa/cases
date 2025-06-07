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
