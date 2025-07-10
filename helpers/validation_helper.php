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
     * Enhanced hearing data validation with business rules
     * @param array $data Input data
     * @param array $context Additional context (current_hearing_data for updates)
     * @return array Validation result
     */
    function cases_validate_hearing_data($data, $context = []) {
        $CI = &get_instance();
        $CI->load->helper('modules/cases/helpers/security_helper');
        
        // Load hearing constants for validation
        require_once(__DIR__ . '/../config/hearing_constants.php');
        
        $errors = [];
        $warnings = [];
        $validated = [];
        
        // Required fields validation
        if (empty($data['case_id'])) {
            $errors[] = 'Case ID is required';
        }
        if (empty($data['date'])) {
            $errors[] = 'Hearing date is required';
        }
        
        if (!empty($errors)) {
            return ['valid' => false, 'errors' => $errors, 'warnings' => [], 'data' => []];
        }
        
        // Validate case_id (int 11)
        $validated['case_id'] = cases_validate_integer($data['case_id'], 1);
        if ($validated['case_id'] === false) {
            $errors[] = 'Invalid case ID';
        }
        
        // Validate date with business rule constraints
        $validated['date'] = cases_validate_date($data['date']);
        if ($validated['date'] === false) {
            $errors[] = 'Invalid hearing date format';
        } else {
            // Apply business rule validation for dates
            $current_hearing_date = $context['current_hearing_date'] ?? null;
            $allow_past_dates = $context['allow_past_dates'] ?? false;
            $date_errors = hearing_validate_date_constraints($validated['date'], $current_hearing_date, $allow_past_dates);
            $errors = array_merge($errors, $date_errors);
        }
        
        // Validate time with business rule constraints
        if (!empty($data['time'])) {
            $time_errors = hearing_validate_time_constraints($data['time']);
            if (!empty($time_errors)) {
                $errors = array_merge($errors, $time_errors);
            } else {
                $validated['time'] = $data['time'];
            }
        } else {
            $validated['time'] = hearing_get_default_time();
        }
        
        // Validate description (text, optional)
        if (!empty($data['description'])) {
            $validated['description'] = cases_sanitize_string($data['description'], 5000, true);
        }
        
        // Enhanced status validation with transition rules
        $all_statuses = hearing_get_all_statuses();
        $new_status = $data['status'] ?? HEARING_STATUS_SCHEDULED;
        
        if (!array_key_exists($new_status, $all_statuses)) {
            $errors[] = 'Invalid hearing status';
        } else {
            $validated['status'] = $new_status;
            
            // Check status transition validity for updates
            if (isset($context['current_status']) && $context['current_status'] !== $new_status) {
                $transition_errors = hearing_validate_status_transition(
                    $context['current_status'], 
                    $new_status, 
                    $validated['date']
                );
                $errors = array_merge($errors, $transition_errors);
            }
        }
        
        // Validate next_date with business rules
        if (!empty($data['next_date'])) {
            $validated['next_date'] = cases_validate_date($data['next_date']);
            if ($validated['next_date'] === false) {
                $errors[] = 'Invalid next hearing date format';
            } else {
                // Next date must be after current hearing date
                if (strtotime($validated['next_date']) <= strtotime($validated['date'])) {
                    $errors[] = 'Next hearing date must be after current hearing date';
                }
            }
        }
        
        // Business rule: Check if status requires next_date
        if (hearing_status_requires_next_date($validated['status']) && empty($validated['next_date'])) {
            $errors[] = "Status '{$validated['status']}' requires a next hearing date";
        }
        
        // Validate hearing_purpose
        if (!empty($data['hearing_purpose'])) {
            $validated['hearing_purpose'] = cases_sanitize_string($data['hearing_purpose'], 255);
            
            // Check if it's a standard purpose or custom
            $standard_purposes = hearing_get_standard_purposes();
            if (!array_key_exists($validated['hearing_purpose'], $standard_purposes)) {
                $warnings[] = 'Using custom hearing purpose: ' . $validated['hearing_purpose'];
            }
        }
        
        // Validate upcoming_purpose (varchar 255, optional)
        if (!empty($data['upcoming_purpose'])) {
            $validated['upcoming_purpose'] = cases_sanitize_string($data['upcoming_purpose'], 255);
        }
        
        // Enhanced parent_hearing_id validation
        if (!empty($data['parent_hearing_id'])) {
            $validated['parent_hearing_id'] = cases_validate_integer($data['parent_hearing_id'], 1);
            if ($validated['parent_hearing_id'] === false) {
                $errors[] = 'Invalid parent hearing ID';
            } else {
                // Check for circular reference
                if (isset($context['hearing_id']) && $validated['parent_hearing_id'] == $context['hearing_id']) {
                    $errors[] = 'Hearing cannot be its own parent';
                }
                
                // Validate parent hearing exists and belongs to same case
                if (!empty($validated['case_id'])) {
                    $CI->db->where('id', $validated['parent_hearing_id']);
                    $CI->db->where('case_id', $validated['case_id']);
                    $parent_hearing = $CI->db->get(db_prefix() . 'hearings')->row_array();
                    
                    if (!$parent_hearing) {
                        $errors[] = 'Parent hearing does not exist or belongs to different case';
                    }
                }
            }
        }
        
        // Validate adjournment_reason
        if (!empty($data['adjournment_reason'])) {
            $validated['adjournment_reason'] = cases_sanitize_string($data['adjournment_reason'], 2000, true);
        }
        
        // Auto-set is_completed based on status
        $validated['is_completed'] = hearing_status_is_completed($validated['status']) ? 1 : 0;
        
        // Check for potential scheduling conflicts
        if (!empty($validated['date']) && !empty($validated['time']) && !empty($validated['case_id'])) {
            $conflict_check = cases_check_hearing_conflicts($validated, $context['hearing_id'] ?? null);
            if (!empty($conflict_check['conflicts'])) {
                $warnings[] = 'Potential scheduling conflicts detected';
            }
        }
        
        // Auto-detect status suggestions
        if (!empty($validated['date']) && !empty($validated['status'])) {
            $auto_status = hearing_auto_detect_status($validated['date'], $validated['status']);
            if ($auto_status) {
                $warnings[] = $auto_status['reason'] . '. Consider changing status to: ' . $auto_status['suggested_status'];
            }
        }
        
        // Add notice for past date entries
        if (!empty($validated['date']) && $validated['date'] < date('Y-m-d') && ($allow_past_dates ?? false)) {
            $warnings[] = 'Creating historical hearing record for past date: ' . date('M j, Y', strtotime($validated['date']));
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
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

if (!function_exists('cases_check_hearing_conflicts')) {
    /**
     * Check for potential hearing scheduling conflicts
     * @param array $hearing_data Hearing data to check
     * @param int $exclude_hearing_id ID to exclude from conflict check
     * @return array Conflict information
     */
    function cases_check_hearing_conflicts($hearing_data, $exclude_hearing_id = null) {
        $CI = &get_instance();
        $conflicts = [];
        
        // Load hearing constants
        require_once(__DIR__ . '/../config/hearing_constants.php');
        
        // Check for same case, same date/time conflicts
        $CI->db->select('h.id, h.date, h.time, h.status, c.case_title');
        $CI->db->from(db_prefix() . 'hearings h');
        $CI->db->join(db_prefix() . 'cases c', 'c.id = h.case_id', 'left');
        $CI->db->where('h.case_id', $hearing_data['case_id']);
        $CI->db->where('h.date', $hearing_data['date']);
        $CI->db->where('h.time', $hearing_data['time']);
        
        if ($exclude_hearing_id) {
            $CI->db->where('h.id !=', $exclude_hearing_id);
        }
        
        $same_case_conflicts = $CI->db->get()->result_array();
        
        foreach ($same_case_conflicts as $conflict) {
            $conflicts[] = [
                'type' => 'same_case_time',
                'severity' => 'high',
                'message' => "Another hearing for the same case is scheduled at the same time",
                'hearing_id' => $conflict['id'],
                'case_title' => $conflict['case_title']
            ];
        }
        
        // Check for court room conflicts (if court_room_id is available)
        if (!empty($hearing_data['court_room_id'])) {
            $CI->db->select('h.id, h.date, h.time, h.status, c.case_title, c.case_number');
            $CI->db->from(db_prefix() . 'hearings h');
            $CI->db->join(db_prefix() . 'cases c', 'c.id = h.case_id', 'left');
            $CI->db->where('c.court_room_id', $hearing_data['court_room_id']);
            $CI->db->where('h.date', $hearing_data['date']);
            $CI->db->where('h.time', $hearing_data['time']);
            $CI->db->where('h.status NOT IN', [HEARING_STATUS_CANCELLED, HEARING_STATUS_COMPLETED]);
            
            if ($exclude_hearing_id) {
                $CI->db->where('h.id !=', $exclude_hearing_id);
            }
            
            $court_conflicts = $CI->db->get()->result_array();
            
            foreach ($court_conflicts as $conflict) {
                $conflicts[] = [
                    'type' => 'court_room',
                    'severity' => 'medium',
                    'message' => "Another hearing is scheduled in the same court room at the same time",
                    'hearing_id' => $conflict['id'],
                    'case_title' => $conflict['case_title'],
                    'case_number' => $conflict['case_number']
                ];
            }
        }
        
        return [
            'conflicts' => $conflicts,
            'has_conflicts' => !empty($conflicts),
            'high_severity' => !empty(array_filter($conflicts, function($c) { return $c['severity'] === 'high'; }))
        ];
    }
}