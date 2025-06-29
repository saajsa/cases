<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Rate Limiter Helper for Cases Module
 * Provides rate limiting functionality for API endpoints and AJAX requests
 */

if (!function_exists('cases_check_rate_limit')) {
    /**
     * Check rate limit for a specific action
     * @param string $action Action identifier (e.g., 'get_consultations', 'add_hearing')
     * @param int $max_requests Maximum requests allowed
     * @param int $time_window Time window in seconds
     * @param string $identifier User identifier (defaults to IP + user_id)
     * @return array Result with 'allowed' boolean and 'retry_after' seconds
     */
    function cases_check_rate_limit($action, $max_requests = 60, $time_window = 3600, $identifier = null) {
        $CI = &get_instance();
        
        // Generate identifier
        if ($identifier === null) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $user_id = get_staff_user_id() ?? 'anonymous';
            $identifier = md5($ip . '_' . $user_id . '_' . $action);
        }
        
        $cache_key = "cases_rate_limit_{$identifier}";
        
        // Get current request count from cache
        $CI->load->driver('cache');
        $requests = $CI->cache->get($cache_key);
        
        if ($requests === FALSE) {
            // First request in time window
            $requests = [
                'count' => 1,
                'window_start' => time()
            ];
            $CI->cache->save($cache_key, $requests, $time_window);
            
            return [
                'allowed' => true,
                'retry_after' => 0,
                'requests_remaining' => $max_requests - 1
            ];
        }
        
        // Check if we're still in the same time window
        $current_time = time();
        $window_elapsed = $current_time - $requests['window_start'];
        
        if ($window_elapsed >= $time_window) {
            // New time window, reset counter
            $requests = [
                'count' => 1,
                'window_start' => $current_time
            ];
            $CI->cache->save($cache_key, $requests, $time_window);
            
            return [
                'allowed' => true,
                'retry_after' => 0,
                'requests_remaining' => $max_requests - 1
            ];
        }
        
        // Check if limit exceeded
        if ($requests['count'] >= $max_requests) {
            $retry_after = $time_window - $window_elapsed;
            
            // Log rate limit violation
            cases_log_security_event('Rate limit exceeded', [
                'action' => $action,
                'requests' => $requests['count'],
                'max_requests' => $max_requests,
                'time_window' => $time_window
            ], 'warning');
            
            return [
                'allowed' => false,
                'retry_after' => $retry_after,
                'requests_remaining' => 0
            ];
        }
        
        // Increment counter
        $requests['count']++;
        $CI->cache->save($cache_key, $requests, $time_window);
        
        return [
            'allowed' => true,
            'retry_after' => 0,
            'requests_remaining' => $max_requests - $requests['count']
        ];
    }
}

if (!function_exists('cases_enforce_rate_limit')) {
    /**
     * Enforce rate limit for current request (outputs JSON and exits if exceeded)
     * @param string $action Action identifier
     * @param int $max_requests Maximum requests allowed
     * @param int $time_window Time window in seconds
     * @param string $identifier User identifier (optional)
     */
    function cases_enforce_rate_limit($action, $max_requests = 60, $time_window = 3600, $identifier = null) {
        $result = cases_check_rate_limit($action, $max_requests, $time_window, $identifier);
        
        if (!$result['allowed']) {
            http_response_code(429); // Too Many Requests
            header('Retry-After: ' . $result['retry_after']);
            header('Content-Type: application/json');
            
            echo json_encode([
                'success' => false,
                'error' => 'Rate limit exceeded',
                'message' => 'Too many requests. Please try again later.',
                'retry_after' => $result['retry_after']
            ]);
            exit;
        }
        
        // Add rate limit headers
        header('X-RateLimit-Limit: ' . $max_requests);
        header('X-RateLimit-Remaining: ' . $result['requests_remaining']);
        header('X-RateLimit-Reset: ' . (time() + $result['retry_after']));
    }
}

if (!function_exists('cases_reset_rate_limit')) {
    /**
     * Reset rate limit for a specific action and identifier
     * @param string $action Action identifier
     * @param string $identifier User identifier (optional)
     */
    function cases_reset_rate_limit($action, $identifier = null) {
        $CI = &get_instance();
        
        if ($identifier === null) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $user_id = get_staff_user_id() ?? 'anonymous';
            $identifier = md5($ip . '_' . $user_id . '_' . $action);
        }
        
        $cache_key = "cases_rate_limit_{$identifier}";
        
        $CI->load->driver('cache');
        $CI->cache->delete($cache_key);
    }
}

if (!function_exists('cases_get_rate_limit_status')) {
    /**
     * Get current rate limit status for an action
     * @param string $action Action identifier
     * @param int $max_requests Maximum requests allowed
     * @param int $time_window Time window in seconds
     * @param string $identifier User identifier (optional)
     * @return array Status information
     */
    function cases_get_rate_limit_status($action, $max_requests = 60, $time_window = 3600, $identifier = null) {
        $CI = &get_instance();
        
        if ($identifier === null) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $user_id = get_staff_user_id() ?? 'anonymous';
            $identifier = md5($ip . '_' . $user_id . '_' . $action);
        }
        
        $cache_key = "cases_rate_limit_{$identifier}";
        
        $CI->load->driver('cache');
        $requests = $CI->cache->get($cache_key);
        
        if ($requests === FALSE) {
            return [
                'requests_made' => 0,
                'requests_remaining' => $max_requests,
                'window_start' => time(),
                'window_end' => time() + $time_window,
                'reset_time' => time() + $time_window
            ];
        }
        
        $current_time = time();
        $window_elapsed = $current_time - $requests['window_start'];
        $window_remaining = $time_window - $window_elapsed;
        
        if ($window_elapsed >= $time_window) {
            return [
                'requests_made' => 0,
                'requests_remaining' => $max_requests,
                'window_start' => $current_time,
                'window_end' => $current_time + $time_window,
                'reset_time' => $current_time + $time_window
            ];
        }
        
        return [
            'requests_made' => $requests['count'],
            'requests_remaining' => max(0, $max_requests - $requests['count']),
            'window_start' => $requests['window_start'],
            'window_end' => $requests['window_start'] + $time_window,
            'reset_time' => $requests['window_start'] + $time_window
        ];
    }
}