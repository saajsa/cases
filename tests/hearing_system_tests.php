<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Comprehensive Test Suite for Enhanced Hearing System
 * Tests all the logical improvements and validation enhancements
 */

class Hearing_System_Tests {
    
    private $CI;
    private $test_results = [];
    
    public function __construct() {
        $this->CI = &get_instance();
        $this->CI->load->helper('modules/cases/helpers/validation_helper');
        require_once(APPPATH . 'modules/cases/config/hearing_constants.php');
    }
    
    /**
     * Run all tests
     */
    public function run_all_tests() {
        echo "<h1>Hearing System Test Suite</h1>\n";
        
        $this->test_status_definitions();
        $this->test_temporal_classification();
        $this->test_status_transitions();
        $this->test_validation_rules();
        $this->test_business_rules();
        $this->test_conflict_detection();
        $this->test_date_constraints();
        
        $this->display_results();
    }
    
    /**
     * Test status definitions and consistency
     */
    private function test_status_definitions() {
        echo "<h2>Testing Status Definitions</h2>\n";
        
        // Test all statuses are defined
        $statuses = hearing_get_all_statuses();
        $this->assert(count($statuses) > 0, "Status definitions loaded");
        
        // Test status definitions have required fields
        $definitions = hearing_get_status_definitions();
        foreach ($definitions as $status => $def) {
            $this->assert(isset($def['label']), "Status $status has label");
            $this->assert(isset($def['description']), "Status $status has description");
            $this->assert(isset($def['color']), "Status $status has color");
            $this->assert(isset($def['icon']), "Status $status has icon");
            $this->assert(isset($def['is_active']), "Status $status has is_active flag");
            $this->assert(isset($def['is_completed']), "Status $status has is_completed flag");
        }
        
        // Test consistency between functions
        $this->assert(
            hearing_status_is_completed(HEARING_STATUS_COMPLETED),
            "Completed status is marked as completed"
        );
        
        $this->assert(
            !hearing_status_is_completed(HEARING_STATUS_SCHEDULED),
            "Scheduled status is not marked as completed"
        );
    }
    
    /**
     * Test temporal classification logic
     */
    private function test_temporal_classification() {
        echo "<h2>Testing Temporal Classification</h2>\n";
        
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        
        // Test today classification
        $this->assert(
            hearing_get_temporal_classification($today) === HEARING_TEMPORAL_TODAY,
            "Today's date classified as TODAY"
        );
        
        // Test future classification
        $this->assert(
            hearing_get_temporal_classification($tomorrow) === HEARING_TEMPORAL_UPCOMING,
            "Tomorrow's date classified as UPCOMING"
        );
        
        // Test past classification with completed status
        $this->assert(
            hearing_get_temporal_classification($yesterday, HEARING_STATUS_COMPLETED) === HEARING_TEMPORAL_PAST,
            "Yesterday's completed hearing classified as PAST"
        );
        
        // Test overdue classification
        $this->assert(
            hearing_get_temporal_classification($yesterday, HEARING_STATUS_SCHEDULED) === HEARING_TEMPORAL_OVERDUE,
            "Yesterday's scheduled hearing classified as OVERDUE"
        );
    }
    
    /**
     * Test status transition validation
     */
    private function test_status_transitions() {
        echo "<h2>Testing Status Transitions</h2>\n";
        
        // Test valid transitions
        $this->assert(
            hearing_is_valid_transition(HEARING_STATUS_SCHEDULED, HEARING_STATUS_COMPLETED),
            "Can transition from Scheduled to Completed"
        );
        
        $this->assert(
            hearing_is_valid_transition(HEARING_STATUS_SCHEDULED, HEARING_STATUS_ADJOURNED),
            "Can transition from Scheduled to Adjourned"
        );
        
        // Test invalid transitions
        $this->assert(
            !hearing_is_valid_transition(HEARING_STATUS_COMPLETED, HEARING_STATUS_SCHEDULED),
            "Cannot transition from Completed to Scheduled"
        );
        
        // Test transition validation with date constraints
        $future_date = date('Y-m-d', strtotime('+1 day'));
        $errors = hearing_validate_status_transition(
            HEARING_STATUS_SCHEDULED, 
            HEARING_STATUS_COMPLETED, 
            $future_date
        );
        
        $this->assert(
            !empty($errors),
            "Cannot mark future hearing as completed"
        );
    }
    
    /**
     * Test validation rules
     */
    private function test_validation_rules() {
        echo "<h2>Testing Validation Rules</h2>\n";
        
        // Test valid hearing data
        $valid_data = [
            'case_id' => 1,
            'date' => date('Y-m-d', strtotime('+1 day')),
            'time' => '10:00',
            'status' => HEARING_STATUS_SCHEDULED,
            'hearing_purpose' => 'Arguments'
        ];
        
        $result = cases_validate_hearing_data($valid_data);
        $this->assert($result['valid'], "Valid hearing data passes validation");
        
        // Test invalid hearing data
        $invalid_data = [
            'case_id' => 'invalid',
            'date' => 'invalid-date',
            'time' => '25:00',
            'status' => 'InvalidStatus'
        ];
        
        $result = cases_validate_hearing_data($invalid_data);
        $this->assert(!$result['valid'], "Invalid hearing data fails validation");
        $this->assert(count($result['errors']) > 0, "Validation errors are reported");
    }
    
    /**
     * Test business rules
     */
    private function test_business_rules() {
        echo "<h2>Testing Business Rules</h2>\n";
        
        // Test status requiring next_date
        $this->assert(
            hearing_status_requires_next_date(HEARING_STATUS_ADJOURNED),
            "Adjourned status requires next date"
        );
        
        $this->assert(
            !hearing_status_requires_next_date(HEARING_STATUS_COMPLETED),
            "Completed status does not require next date"
        );
        
        // Test final status rules
        $this->assert(
            hearing_status_is_final(HEARING_STATUS_COMPLETED),
            "Completed status is final"
        );
        
        $this->assert(
            !hearing_status_is_final(HEARING_STATUS_SCHEDULED),
            "Scheduled status is not final"
        );
        
        // Test hearing activity
        $future_date = date('Y-m-d', strtotime('+1 day'));
        $this->assert(
            hearing_is_active($future_date, HEARING_STATUS_SCHEDULED),
            "Future scheduled hearing is active"
        );
        
        $past_date = date('Y-m-d', strtotime('-1 day'));
        $this->assert(
            !hearing_is_active($past_date, HEARING_STATUS_COMPLETED),
            "Past completed hearing is not active"
        );
    }
    
    /**
     * Test conflict detection
     */
    private function test_conflict_detection() {
        echo "<h2>Testing Conflict Detection</h2>\n";
        
        // Test conflict detection function exists
        $this->assert(
            function_exists('cases_check_hearing_conflicts'),
            "Conflict detection function exists"
        );
        
        // Test basic conflict structure
        $hearing_data = [
            'case_id' => 1,
            'date' => date('Y-m-d', strtotime('+1 day')),
            'time' => '10:00'
        ];
        
        $result = cases_check_hearing_conflicts($hearing_data);
        $this->assert(
            is_array($result) && isset($result['conflicts']),
            "Conflict detection returns proper structure"
        );
    }
    
    /**
     * Test date constraints
     */
    private function test_date_constraints() {
        echo "<h2>Testing Date Constraints</h2>\n";
        
        // Test past date validation
        $past_date = date('Y-m-d', strtotime('-1 day'));
        $errors = hearing_validate_date_constraints($past_date);
        $this->assert(
            !empty($errors),
            "Past dates are rejected for new hearings"
        );
        
        // Test future date validation
        $future_date = date('Y-m-d', strtotime('+7 days'));
        $errors = hearing_validate_date_constraints($future_date);
        $this->assert(
            empty($errors),
            "Future dates are accepted for new hearings"
        );
        
        // Test minimum advance notice
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $errors = hearing_validate_date_constraints($tomorrow);
        $this->assert(
            empty($errors),
            "Minimum advance notice is enforced"
        );
    }
    
    /**
     * Assert function for testing
     */
    private function assert($condition, $message) {
        $result = $condition ? 'PASS' : 'FAIL';
        $this->test_results[] = [
            'message' => $message,
            'result' => $result,
            'status' => $condition
        ];
        
        $color = $condition ? 'green' : 'red';
        echo "<div style='color: $color;'>[$result] $message</div>\n";
    }
    
    /**
     * Display test results summary
     */
    private function display_results() {
        echo "<h2>Test Results Summary</h2>\n";
        
        $total = count($this->test_results);
        $passed = count(array_filter($this->test_results, function($r) { return $r['status']; }));
        $failed = $total - $passed;
        
        echo "<div style='background: #f0f0f0; padding: 10px; margin: 10px 0;'>\n";
        echo "<strong>Total Tests:</strong> $total<br>\n";
        echo "<strong style='color: green;'>Passed:</strong> $passed<br>\n";
        echo "<strong style='color: red;'>Failed:</strong> $failed<br>\n";
        echo "<strong>Success Rate:</strong> " . round(($passed / $total) * 100, 2) . "%<br>\n";
        echo "</div>\n";
        
        if ($failed > 0) {
            echo "<h3>Failed Tests:</h3>\n";
            foreach ($this->test_results as $test) {
                if (!$test['status']) {
                    echo "<div style='color: red;'>• {$test['message']}</div>\n";
                }
            }
        }
    }
}

// Usage example (uncomment to run tests):
// $tests = new Hearing_System_Tests();
// $tests->run_all_tests();