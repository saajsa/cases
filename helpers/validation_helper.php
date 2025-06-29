<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Database Schema Validation Helper for Cases Module
 * Based on actual database structure from crm_legum.txt
 */

if (!function_exists('cases_validate_case_data')) {
    /**
     * Validate case data according to tblcases schema
     * @param array $data Input data
     * @return array Validation result with 'valid' and 'errors' keys
     */
    function cases_validate_case_data($data) {
        $CI = &get_instance();
        $CI->load->helper('modules/cases/helpers/security_helper');
        
        $errors = [];
        $validated = [];
        
        // Required fields validation
        $required_fields = ['case_title', 'case_number', 'date_filed', 'client_id', 'consultation_id'];
        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }
        
        if (!empty($errors)) {
            return ['valid' => false, 'errors' => $errors, 'data' => []];
        }
        
        // Validate case_title (varchar 255)
        $validated['case_title'] = cases_sanitize_string($data['case_title'], 255);
        if (empty($validated['case_title'])) {
            $errors[] = 'Case title cannot be empty';
        }
        
        // Validate case_number (varchar 100, should be unique)
        $validated['case_number'] = cases_sanitize_string($data['case_number'], 100);
        if (empty($validated['case_number'])) {
            $errors[] = 'Case number cannot be empty';
        }
        
        // Validate date_filed (date format)
        $validated['date_filed'] = cases_validate_date($data['date_filed']);
        if ($validated['date_filed'] === false) {
            $errors[] = 'Invalid date filed format';
        }
        
        // Validate client_id (int 11)
        $validated['client_id'] = cases_validate_integer($data['client_id'], 1);
        if ($validated['client_id'] === false) {
            $errors[] = 'Invalid client ID';
        }
        
        // Validate consultation_id (int 11)
        $validated['consultation_id'] = cases_validate_integer($data['consultation_id'], 1);
        if ($validated['consultation_id'] === false) {
            $errors[] = 'Invalid consultation ID';
        }
        
        // Optional fields
        if (!empty($data['contact_id'])) {
            $validated['contact_id'] = cases_validate_integer($data['contact_id'], 1);
            if ($validated['contact_id'] === false) {
                $errors[] = 'Invalid contact ID';
            }
        }
        
        if (!empty($data['court_room_id'])) {
            $validated['court_room_id'] = cases_validate_integer($data['court_room_id'], 1);
            if ($validated['court_room_id'] === false) {
                $errors[] = 'Invalid court room ID';
            }
        }
        
        // Validate status (ENUM values from schema)
        $allowed_statuses = ['active', 'on_hold', 'completed', 'dismissed', 'settled', 'transferred'];
        $validated['status'] = in_array($data['status'] ?? 'active', $allowed_statuses) 
            ? $data['status'] : 'active';
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'data' => $validated
        ];
    }
}

if (!function_exists('cases_validate_hearing_data')) {
    /**
     * Validate hearing data according to tblhearings schema
     * @param array $data Input data
     * @return array Validation result
     */
    function cases_validate_hearing_data($data) {
        $CI = &get_instance();
        $CI->load->helper('modules/cases/helpers/security_helper');
        
        $errors = [];
        $validated = [];
        
        // Required fields
        if (empty($data['case_id'])) {
            $errors[] = 'Case ID is required';
        }
        if (empty($data['date'])) {
            $errors[] = 'Hearing date is required';
        }
        
        if (!empty($errors)) {
            return ['valid' => false, 'errors' => $errors, 'data' => []];
        }
        
        // Validate case_id (int 11)
        $validated['case_id'] = cases_validate_integer($data['case_id'], 1);
        if ($validated['case_id'] === false) {
            $errors[] = 'Invalid case ID';
        }
        
        // Validate date (date format)
        $validated['date'] = cases_validate_date($data['date']);
        if ($validated['date'] === false) {
            $errors[] = 'Invalid hearing date format';
        }
        
        // Validate time (optional, time format)
        if (!empty($data['time'])) {
            if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $data['time'])) {
                $errors[] = 'Invalid time format (use HH:MM or HH:MM:SS)';
            } else {
                $validated['time'] = $data['time'];
            }
        }
        
        // Validate description (text, optional)
        if (!empty($data['description'])) {
            $validated['description'] = cases_sanitize_string($data['description'], 5000, true);
        }
        
        // Validate status (ENUM values from schema)
        $allowed_statuses = ['Scheduled', 'Adjourned', 'Completed', 'Cancelled', 'Postponed'];
        $validated['status'] = in_array($data['status'] ?? 'Scheduled', $allowed_statuses) 
            ? $data['status'] : 'Scheduled';
        
        // Validate next_date (optional date)
        if (!empty($data['next_date'])) {
            $validated['next_date'] = cases_validate_date($data['next_date']);
            if ($validated['next_date'] === false) {
                $errors[] = 'Invalid next hearing date format';
            }
        }
        
        // Validate hearing_purpose (varchar 255, optional)
        if (!empty($data['hearing_purpose'])) {
            $validated['hearing_purpose'] = cases_sanitize_string($data['hearing_purpose'], 255);
        }
        
        // Validate upcoming_purpose (varchar 255, optional)
        if (!empty($data['upcoming_purpose'])) {
            $validated['upcoming_purpose'] = cases_sanitize_string($data['upcoming_purpose'], 255);
        }
        
        // Validate parent_hearing_id (int 11, optional)
        if (!empty($data['parent_hearing_id'])) {
            $validated['parent_hearing_id'] = cases_validate_integer($data['parent_hearing_id'], 1);
            if ($validated['parent_hearing_id'] === false) {
                $errors[] = 'Invalid parent hearing ID';
            }
        }
        
        // Validate adjournment_reason (text, optional)
        if (!empty($data['adjournment_reason'])) {
            $validated['adjournment_reason'] = cases_sanitize_string($data['adjournment_reason'], 2000, true);
        }
        
        // Validate is_completed (tinyint 1)
        $validated['is_completed'] = isset($data['is_completed']) && $data['is_completed'] ? 1 : 0;
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'data' => $validated
        ];
    }
}

if (!function_exists('cases_validate_consultation_data')) {
    /**
     * Validate consultation data according to tblcase_consultations schema
     * @param array $data Input data
     * @return array Validation result
     */
    function cases_validate_consultation_data($data) {
        $CI = &get_instance();
        $CI->load->helper('modules/cases/helpers/security_helper');
        
        $errors = [];
        $validated = [];
        
        // Required fields
        if (empty($data['client_id'])) {
            $errors[] = 'Client ID is required';
        }
        if (empty($data['note'])) {
            $errors[] = 'Consultation note is required';
        }
        
        if (!empty($errors)) {
            return ['valid' => false, 'errors' => $errors, 'data' => []];
        }
        
        // Validate client_id (int 11)
        $validated['client_id'] = cases_validate_integer($data['client_id'], 1);
        if ($validated['client_id'] === false) {
            $errors[] = 'Invalid client ID';
        }
        
        // Validate contact_id (int 11, optional)
        if (!empty($data['contact_id'])) {
            $validated['contact_id'] = cases_validate_integer($data['contact_id'], 1);
            if ($validated['contact_id'] === false) {
                $errors[] = 'Invalid contact ID';
            }
        }
        
        // Validate invoice_id (int 11, optional)
        if (!empty($data['invoice_id'])) {
            $validated['invoice_id'] = cases_validate_integer($data['invoice_id'], 1);
            if ($validated['invoice_id'] === false) {
                $errors[] = 'Invalid invoice ID';
            }
        }
        
        // Validate tag (varchar 100, optional)
        if (!empty($data['tag'])) {
            $validated['tag'] = cases_sanitize_string($data['tag'], 100);
        }
        
        // Validate note (text, required)
        $validated['note'] = cases_sanitize_string($data['note'], 5000, true);
        if (empty($validated['note'])) {
            $errors[] = 'Consultation note cannot be empty';
        }
        
        // Validate phase (ENUM values from schema)
        $allowed_phases = ['consultation', 'litigation'];
        $validated['phase'] = in_array($data['phase'] ?? 'consultation', $allowed_phases) 
            ? $data['phase'] : 'consultation';
        
        // Validate appointment_id (int 11, optional)
        if (!empty($data['appointment_id'])) {
            $validated['appointment_id'] = cases_validate_integer($data['appointment_id'], 1);
            if ($validated['appointment_id'] === false) {
                $errors[] = 'Invalid appointment ID';
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'data' => $validated
        ];
    }
}

if (!function_exists('cases_validate_court_data')) {
    /**
     * Validate court data according to tblcourts schema
     * @param array $data Input data
     * @return array Validation result
     */
    function cases_validate_court_data($data) {
        $CI = &get_instance();
        $CI->load->helper('modules/cases/helpers/security_helper');
        
        $errors = [];
        $validated = [];
        
        // Required fields
        if (empty($data['name'])) {
            $errors[] = 'Court name is required';
        }
        
        if (!empty($errors)) {
            return ['valid' => false, 'errors' => $errors, 'data' => []];
        }
        
        // Validate name (varchar 255, required)
        $validated['name'] = cases_sanitize_string($data['name'], 255);
        if (empty($validated['name'])) {
            $errors[] = 'Court name cannot be empty';
        }
        
        // Validate hierarchy (varchar 100, optional)
        if (!empty($data['hierarchy'])) {
            $validated['hierarchy'] = cases_sanitize_string($data['hierarchy'], 100);
        }
        
        // Validate location (varchar 255, optional)
        if (!empty($data['location'])) {
            $validated['location'] = cases_sanitize_string($data['location'], 255);
        }
        
        // Validate status (ENUM values from schema)
        $allowed_statuses = ['Active', 'Inactive'];
        $validated['status'] = in_array($data['status'] ?? 'Active', $allowed_statuses) 
            ? $data['status'] : 'Active';
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'data' => $validated
        ];
    }
}

if (!function_exists('cases_validate_court_room_data')) {
    /**
     * Validate court room data according to tblcourt_rooms schema
     * @param array $data Input data
     * @return array Validation result
     */
    function cases_validate_court_room_data($data) {
        $CI = &get_instance();
        $CI->load->helper('modules/cases/helpers/security_helper');
        
        $errors = [];
        $validated = [];
        
        // Required fields
        if (empty($data['court_id'])) {
            $errors[] = 'Court ID is required';
        }
        if (empty($data['court_no'])) {
            $errors[] = 'Court number is required';
        }
        
        if (!empty($errors)) {
            return ['valid' => false, 'errors' => $errors, 'data' => []];
        }
        
        // Validate court_id (int 11)
        $validated['court_id'] = cases_validate_integer($data['court_id'], 1);
        if ($validated['court_id'] === false) {
            $errors[] = 'Invalid court ID';
        }
        
        // Validate court_no (varchar 10)
        $validated['court_no'] = cases_sanitize_string($data['court_no'], 10);
        if (empty($validated['court_no'])) {
            $errors[] = 'Court number cannot be empty';
        }
        
        // Validate judge_name (varchar 255, optional)
        if (!empty($data['judge_name'])) {
            $validated['judge_name'] = cases_sanitize_string($data['judge_name'], 255);
        }
        
        // Validate from_date (date, optional)
        if (!empty($data['from_date'])) {
            $validated['from_date'] = cases_validate_date($data['from_date']);
            if ($validated['from_date'] === false) {
                $errors[] = 'Invalid from date format';
            }
        }
        
        // Validate to_date (date, optional)
        if (!empty($data['to_date'])) {
            $validated['to_date'] = cases_validate_date($data['to_date']);
            if ($validated['to_date'] === false) {
                $errors[] = 'Invalid to date format';
            }
        }
        
        // Validate date range
        if (!empty($validated['from_date']) && !empty($validated['to_date'])) {
            if (strtotime($validated['from_date']) > strtotime($validated['to_date'])) {
                $errors[] = 'From date cannot be later than to date';
            }
        }
        
        // Validate type (varchar 100, optional)
        if (!empty($data['type'])) {
            $validated['type'] = cases_sanitize_string($data['type'], 100);
        }
        
        // Validate bench_type (varchar 100, optional)
        if (!empty($data['bench_type'])) {
            $validated['bench_type'] = cases_sanitize_string($data['bench_type'], 100);
        }
        
        // Validate status (ENUM values from schema)
        $allowed_statuses = ['Active', 'Inactive'];
        $validated['status'] = in_array($data['status'] ?? 'Active', $allowed_statuses) 
            ? $data['status'] : 'Active';
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'data' => $validated
        ];
    }
}

if (!function_exists('cases_check_unique_constraint')) {
    /**
     * Check if a value is unique in a specific table column
     * @param string $table Table name (without prefix)
     * @param string $column Column name
     * @param mixed $value Value to check
     * @param int $exclude_id ID to exclude from check (for updates)
     * @return bool True if unique, false if duplicate
     */
    function cases_check_unique_constraint($table, $column, $value, $exclude_id = null) {
        $CI = &get_instance();
        
        $CI->db->where($column, $value);
        
        if ($exclude_id !== null) {
            $CI->db->where('id !=', $exclude_id);
        }
        
        $result = $CI->db->get(db_prefix() . $table);
        
        return $result->num_rows() === 0;
    }
}