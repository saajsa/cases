<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Client Security Helper for Cases Module
 * Contains security functions specific to client area access
 */

if (!function_exists('validate_client_case_access')) {
    /**
     * Validate that a client has access to a specific case
     *
     * @param int $case_id
     * @param int $client_id
     * @return bool
     */
    function validate_client_case_access($case_id, $client_id)
    {
        $CI = &get_instance();
        
        if (!$case_id || !$client_id) {
            return false;
        }
        
        // Check if case belongs to client
        $CI->db->where('id', $case_id);
        $CI->db->where('client_id', $client_id);
        $case = $CI->db->get(db_prefix() . 'cases')->row();
        
        return $case !== null;
    }
}

if (!function_exists('validate_client_document_access')) {
    /**
     * Validate that a client has access to a specific document
     *
     * @param int $document_id
     * @param int $client_id
     * @return bool
     */
    function validate_client_document_access($document_id, $client_id)
    {
        $CI = &get_instance();
        
        if (!$document_id || !$client_id) {
            return false;
        }
        
        // Get document details
        $CI->db->where('id', $document_id);
        $document = $CI->db->get(db_prefix() . 'files')->row();
        
        if (!$document) {
            return false;
        }
        
        // Check access based on document type
        switch ($document->rel_type) {
            case 'case':
                return validate_client_case_access($document->rel_id, $client_id);
                
            case 'hearing':
                // Check if hearing's case belongs to client
                $CI->db->select('c.client_id');
                $CI->db->from(db_prefix() . 'hearings h');
                $CI->db->join(db_prefix() . 'cases c', 'c.id = h.case_id');
                $CI->db->where('h.id', $document->rel_id);
                $hearing = $CI->db->get()->row();
                
                return $hearing && $hearing->client_id == $client_id;
                
            case 'client':
                return $document->rel_id == $client_id;
                
            case 'consultation':
                // Check if consultation belongs to client
                $CI->db->where('id', $document->rel_id);
                $CI->db->where('client_id', $client_id);
                $consultation = $CI->db->get(db_prefix() . 'case_consultations')->row();
                
                return $consultation !== null;
                
            default:
                return false;
        }
    }
}

if (!function_exists('log_client_security_event')) {
    /**
     * Log security events for client area access
     *
     * @param string $event
     * @param array $data
     * @param string $level
     */
    function log_client_security_event($event, $data = [], $level = 'info')
    {
        $CI = &get_instance();
        
        $log_data = [
            'event' => $event,
            'client_id' => get_client_user_id(),
            'contact_id' => get_contact_user_id(),
            'ip_address' => $CI->input->ip_address(),
            'user_agent' => $CI->input->user_agent(),
            'timestamp' => date('Y-m-d H:i:s'),
            'data' => $data
        ];
        
        // Log to CI logs
        log_message($level, 'Client Security Event: ' . $event . ' - ' . json_encode($log_data));
        
        // Optionally log to database table if exists
        if ($CI->db->table_exists(db_prefix() . 'client_security_log')) {
            try {
                $CI->db->insert(db_prefix() . 'client_security_log', [
                    'event' => $event,
                    'client_id' => get_client_user_id(),
                    'contact_id' => get_contact_user_id(),
                    'ip_address' => $CI->input->ip_address(),
                    'user_agent' => $CI->input->user_agent(),
                    'data' => json_encode($data),
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            } catch (Exception $e) {
                log_message('error', 'Failed to log client security event to database: ' . $e->getMessage());
            }
        }
    }
}

if (!function_exists('validate_client_session')) {
    /**
     * Validate client session and redirect if invalid
     *
     * @param bool $redirect_on_fail
     * @return bool
     */
    function validate_client_session($redirect_on_fail = true)
    {
        if (!is_client_logged_in()) {
            log_client_security_event('Invalid session attempt', [], 'warning');
            
            if ($redirect_on_fail) {
                redirect(site_url('authentication/login'));
            }
            
            return false;
        }
        
        return true;
    }
}

if (!function_exists('get_client_accessible_cases')) {
    /**
     * Get all cases accessible by the current client
     *
     * @param int $client_id
     * @param array $filters
     * @return array
     */
    function get_client_accessible_cases($client_id, $filters = [])
    {
        $CI = &get_instance();
        
        if (!$client_id) {
            return [];
        }
        
        $CI->db->select('id, case_title, case_number, date_filed, date_created');
        $CI->db->from(db_prefix() . 'cases');
        $CI->db->where('client_id', $client_id);
        
        // Apply filters if provided
        if (!empty($filters['status'])) {
            $CI->db->where('status', $filters['status']);
        }
        
        if (!empty($filters['date_from'])) {
            $CI->db->where('DATE(date_created) >=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $CI->db->where('DATE(date_created) <=', $filters['date_to']);
        }
        
        $CI->db->order_by('date_created', 'DESC');
        
        return $CI->db->get()->result_array();
    }
}

if (!function_exists('sanitize_client_input')) {
    /**
     * Sanitize input from client area
     *
     * @param mixed $input
     * @param string $type
     * @return mixed
     */
    function sanitize_client_input($input, $type = 'string')
    {
        $CI = &get_instance();
        
        switch ($type) {
            case 'int':
                return (int)$input;
                
            case 'email':
                return filter_var($input, FILTER_SANITIZE_EMAIL);
                
            case 'url':
                return filter_var($input, FILTER_SANITIZE_URL);
                
            case 'html':
                return $CI->security->xss_clean($input);
                
            case 'string':
            default:
                return htmlspecialchars(strip_tags($input), ENT_QUOTES, 'UTF-8');
        }
    }
}

if (!function_exists('check_client_rate_limit')) {
    /**
     * Simple rate limiting for client requests
     *
     * @param string $action
     * @param int $max_requests
     * @param int $time_window
     * @return bool
     */
    function check_client_rate_limit($action, $max_requests = 60, $time_window = 3600)
    {
        $CI = &get_instance();
        
        $client_id = get_client_user_id();
        $ip = $CI->input->ip_address();
        $key = "rate_limit_{$action}_{$client_id}_{$ip}";
        
        // Simple file-based rate limiting (in production, use Redis or database)
        $cache_dir = APPPATH . 'cache/client_rate_limits/';
        if (!is_dir($cache_dir)) {
            mkdir($cache_dir, 0755, true);
        }
        
        $cache_file = $cache_dir . md5($key) . '.txt';
        $current_time = time();
        
        if (file_exists($cache_file)) {
            $data = json_decode(file_get_contents($cache_file), true);
            if ($data && isset($data['requests']) && isset($data['window_start'])) {
                // Check if we're still in the same time window
                if (($current_time - $data['window_start']) < $time_window) {
                    if ($data['requests'] >= $max_requests) {
                        log_client_security_event('Rate limit exceeded', [
                            'action' => $action,
                            'requests' => $data['requests'],
                            'max_requests' => $max_requests,
                            'time_window' => $time_window
                        ], 'warning');
                        return false;
                    }
                    
                    // Increment counter
                    $data['requests']++;
                    file_put_contents($cache_file, json_encode($data));
                } else {
                    // Reset window
                    $data = ['requests' => 1, 'window_start' => $current_time];
                    file_put_contents($cache_file, json_encode($data));
                }
            }
        } else {
            // First request
            $data = ['requests' => 1, 'window_start' => $current_time];
            file_put_contents($cache_file, json_encode($data));
        }
        
        return true;
    }
}