<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Security Helper for Cases Module
 * Provides comprehensive security functions for input validation and output escaping
 */

if (!function_exists('cases_escape_output')) {
    /**
     * Safely escape output for HTML display
     * @param string $string The string to escape
     * @param bool $double_encode Whether to double encode entities
     * @return string Escaped string
     */
    function cases_escape_output($string, $double_encode = false) {
        if ($string === null || $string === '') {
            return '';
        }
        return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8', $double_encode);
    }
}

if (!function_exists('cases_validate_date')) {
    /**
     * Validate date input
     * @param string $date Date string to validate
     * @param string $format Expected date format (default: Y-m-d)
     * @return string|false Valid date string or false if invalid
     */
    function cases_validate_date($date, $format = 'Y-m-d') {
        if (!$date || !is_string($date)) {
            return false;
        }
        
        $date_obj = DateTime::createFromFormat($format, $date);
        if (!$date_obj || $date_obj->format($format) !== $date) {
            return false;
        }
        
        // Additional validation for reasonable date ranges
        $year = (int)$date_obj->format('Y');
        if ($year < 1900 || $year > 2100) {
            return false;
        }
        
        return $date;
    }
}

if (!function_exists('cases_validate_integer')) {
    /**
     * Validate integer input
     * @param mixed $value Value to validate
     * @param int $min Minimum allowed value
     * @param int $max Maximum allowed value
     * @return int|false Valid integer or false if invalid
     */
    function cases_validate_integer($value, $min = 1, $max = PHP_INT_MAX) {
        if (!is_numeric($value)) {
            return false;
        }
        
        $int_value = (int)$value;
        if ($int_value < $min || $int_value > $max) {
            return false;
        }
        
        return $int_value;
    }
}

if (!function_exists('cases_sanitize_string')) {
    /**
     * Sanitize string input
     * @param string $string String to sanitize
     * @param int $max_length Maximum allowed length
     * @param bool $allow_html Whether to allow HTML tags
     * @return string Sanitized string
     */
    function cases_sanitize_string($string, $max_length = 1000, $allow_html = false) {
        if (!is_string($string)) {
            return '';
        }
        
        // Trim whitespace
        $string = trim($string);
        
        // Limit length
        if (strlen($string) > $max_length) {
            $string = substr($string, 0, $max_length);
        }
        
        // Remove HTML if not allowed
        if (!$allow_html) {
            $string = strip_tags($string);
        }
        
        return $string;
    }
}

if (!function_exists('cases_validate_email')) {
    /**
     * Validate email address
     * @param string $email Email to validate
     * @return string|false Valid email or false if invalid
     */
    function cases_validate_email($email) {
        if (!is_string($email) || strlen($email) > 254) {
            return false;
        }
        
        $email = trim($email);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        
        return $email;
    }
}

if (!function_exists('cases_validate_file_upload')) {
    /**
     * Validate file upload
     * @param array $file $_FILES array element
     * @param array $allowed_types Allowed MIME types
     * @param int $max_size Maximum file size in bytes
     * @return array Validation result with 'valid' and 'error' keys
     */
    function cases_validate_file_upload($file, $allowed_types = [], $max_size = 10485760) {
        $result = ['valid' => false, 'error' => ''];
        
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            $result['error'] = 'Invalid file upload';
            return $result;
        }
        
        // Check file size
        if ($file['size'] > $max_size) {
            $result['error'] = 'File size exceeds maximum allowed size';
            return $result;
        }
        
        // Check MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!empty($allowed_types) && !in_array($mime_type, $allowed_types)) {
            $result['error'] = 'File type not allowed';
            return $result;
        }
        
        // Additional security checks
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $dangerous_extensions = ['php', 'php3', 'php4', 'php5', 'phtml', 'exe', 'com', 'bat', 'cmd', 'scr'];
        
        if (in_array($extension, $dangerous_extensions)) {
            $result['error'] = 'File extension not allowed for security reasons';
            return $result;
        }
        
        $result['valid'] = true;
        return $result;
    }
}

if (!function_exists('cases_generate_csrf_token')) {
    /**
     * Generate CSRF token for forms
     * @return string CSRF token
     */
    function cases_generate_csrf_token() {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        $token = bin2hex(random_bytes(32));
        $_SESSION['cases_csrf_token'] = $token;
        $_SESSION['cases_csrf_time'] = time();
        
        return $token;
    }
}

if (!function_exists('cases_verify_csrf_token')) {
    /**
     * Verify CSRF token (use CodeIgniter's built-in CSRF if available)
     * @param string $token Token to verify (optional, will check POST data if not provided)
     * @param int $timeout Token timeout in seconds (default: 3600)
     * @return bool True if valid, false otherwise
     */
    function cases_verify_csrf_token($token = null, $timeout = 3600) {
        $CI = &get_instance();
        
        // Use CodeIgniter's built-in CSRF protection if enabled
        if ($CI->config->item('csrf_protection') === TRUE) {
            $csrf_token_name = $CI->config->item('csrf_token_name');
            $csrf_cookie_name = $CI->config->item('csrf_cookie_name');
            
            if ($token === null) {
                $token = $CI->input->post($csrf_token_name) ?: $CI->input->get($csrf_token_name);
            }
            
            if (!$token) {
                return false;
            }
            
            // Get expected token from cookie
            $expected_token = $CI->input->cookie($csrf_cookie_name);
            
            return hash_equals($expected_token, $token);
        }
        
        // Fallback to custom CSRF implementation
        if (!isset($_SESSION)) {
            session_start();
        }
        
        if ($token === null) {
            $token = $_POST['cases_csrf_token'] ?? $_GET['cases_csrf_token'] ?? null;
        }
        
        if (!$token || !isset($_SESSION['cases_csrf_token']) || !isset($_SESSION['cases_csrf_time'])) {
            return false;
        }
        
        // Check timeout
        if (time() - $_SESSION['cases_csrf_time'] > $timeout) {
            unset($_SESSION['cases_csrf_token'], $_SESSION['cases_csrf_time']);
            return false;
        }
        
        // Verify token
        $valid = hash_equals($_SESSION['cases_csrf_token'], $token);
        
        if ($valid) {
            // Regenerate token after successful verification
            unset($_SESSION['cases_csrf_token'], $_SESSION['cases_csrf_time']);
        }
        
        return $valid;
    }
}

if (!function_exists('cases_log_security_event')) {
    /**
     * Log security events
     * @param string $event Event description
     * @param array $data Additional data to log
     * @param string $level Log level (info, warning, error)
     */
    function cases_log_security_event($event, $data = [], $level = 'warning') {
        $log_data = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event' => $event,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'user_id' => get_staff_user_id() ?? 'anonymous',
            'data' => $data
        ];
        
        log_message($level, 'Cases Security Event: ' . json_encode($log_data));
    }
}