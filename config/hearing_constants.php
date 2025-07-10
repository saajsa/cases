<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Hearing System Constants and Configuration
 * Single source of truth for hearing-related constants and business rules
 */

// =============================================================================
// HEARING STATUS DEFINITIONS
// =============================================================================

/**
 * Official hearing statuses with clear definitions
 */
if (!defined('HEARING_STATUS_SCHEDULED')) {
    define('HEARING_STATUS_SCHEDULED', 'Scheduled');
    define('HEARING_STATUS_IN_PROGRESS', 'In Progress');
    define('HEARING_STATUS_COMPLETED', 'Completed');
    define('HEARING_STATUS_ADJOURNED', 'Adjourned');
    define('HEARING_STATUS_POSTPONED', 'Postponed');
    define('HEARING_STATUS_CANCELLED', 'Cancelled');
    define('HEARING_STATUS_NO_APPEARANCE', 'No Appearance');
}

/**
 * Get all valid hearing statuses
 */
if (!function_exists('hearing_get_all_statuses')) {
    function hearing_get_all_statuses() {
        return [
            HEARING_STATUS_SCHEDULED    => 'Scheduled',
            HEARING_STATUS_IN_PROGRESS  => 'In Progress', 
            HEARING_STATUS_COMPLETED    => 'Completed',
            HEARING_STATUS_ADJOURNED    => 'Adjourned',
            HEARING_STATUS_POSTPONED    => 'Postponed',
            HEARING_STATUS_CANCELLED    => 'Cancelled',
            HEARING_STATUS_NO_APPEARANCE => 'No Appearance'
        ];
    }
}

/**
 * Get status definitions with descriptions
 */
if (!function_exists('hearing_get_status_definitions')) {
    function hearing_get_status_definitions() {
        return [
            HEARING_STATUS_SCHEDULED => [
                'label' => 'Scheduled',
                'description' => 'Hearing is scheduled and awaiting the date',
                'color' => 'primary',
                'icon' => 'fa-calendar',
                'is_active' => true,
                'is_completed' => false
            ],
            HEARING_STATUS_IN_PROGRESS => [
                'label' => 'In Progress',
                'description' => 'Hearing is currently ongoing',
                'color' => 'warning',
                'icon' => 'fa-clock',
                'is_active' => true,
                'is_completed' => false
            ],
            HEARING_STATUS_COMPLETED => [
                'label' => 'Completed',
                'description' => 'Hearing has been completed successfully',
                'color' => 'success',
                'icon' => 'fa-check',
                'is_active' => false,
                'is_completed' => true
            ],
            HEARING_STATUS_ADJOURNED => [
                'label' => 'Adjourned',
                'description' => 'Hearing was adjourned during proceedings with new date set',
                'color' => 'info',
                'icon' => 'fa-calendar-plus',
                'is_active' => false,
                'is_completed' => false
            ],
            HEARING_STATUS_POSTPONED => [
                'label' => 'Postponed',
                'description' => 'Hearing was postponed before proceedings with new date set',
                'color' => 'warning',
                'icon' => 'fa-calendar-times',
                'is_active' => false,
                'is_completed' => false
            ],
            HEARING_STATUS_CANCELLED => [
                'label' => 'Cancelled',
                'description' => 'Hearing was cancelled and will not proceed',
                'color' => 'danger',
                'icon' => 'fa-times',
                'is_active' => false,
                'is_completed' => false
            ],
            HEARING_STATUS_NO_APPEARANCE => [
                'label' => 'No Appearance',
                'description' => 'No parties appeared for the hearing',
                'color' => 'secondary',
                'icon' => 'fa-user-times',
                'is_active' => false,
                'is_completed' => false
            ]
        ];
    }
}

// =============================================================================
// HEARING PURPOSES
// =============================================================================

/**
 * Standard hearing purposes
 */
if (!function_exists('hearing_get_standard_purposes')) {
    function hearing_get_standard_purposes() {
        return [
            'Arguments' => 'Arguments',
            'Evidence Presentation' => 'Evidence Presentation',
            'Witness Examination' => 'Witness Examination',
            'Cross Examination' => 'Cross Examination',
            'Motion Hearing' => 'Motion Hearing',
            'Case Management' => 'Case Management',
            'Judgment' => 'Judgment',
            'Final Arguments' => 'Final Arguments',
            'Interim Orders' => 'Interim Orders',
            'Settlement Discussion' => 'Settlement Discussion',
            'Compliance Review' => 'Compliance Review',
            'Status Report' => 'Status Report',
            'Mediation' => 'Mediation',
            'Arbitration' => 'Arbitration',
            'Bail Application' => 'Bail Application',
            'Charge Sheet' => 'Charge Sheet',
            'Framing of Issues' => 'Framing of Issues',
            'Pre-trial Conference' => 'Pre-trial Conference'
        ];
    }
}

// =============================================================================
// TEMPORAL CLASSIFICATION RULES
// =============================================================================

/**
 * Temporal classification constants
 */
if (!defined('HEARING_TEMPORAL_PAST')) {
    define('HEARING_TEMPORAL_PAST', 'past');
    define('HEARING_TEMPORAL_TODAY', 'today');
    define('HEARING_TEMPORAL_UPCOMING', 'upcoming');
    define('HEARING_TEMPORAL_OVERDUE', 'overdue');
}

/**
 * Get temporal classification for a hearing
 */
if (!function_exists('hearing_get_temporal_classification')) {
    function hearing_get_temporal_classification($hearing_date, $hearing_status = null) {
        $today = date('Y-m-d');
        $hearing_date = date('Y-m-d', strtotime($hearing_date));
        
        if ($hearing_date < $today) {
            // Past hearing - check if it was completed
            if ($hearing_status === HEARING_STATUS_COMPLETED || 
                $hearing_status === HEARING_STATUS_CANCELLED ||
                $hearing_status === HEARING_STATUS_NO_APPEARANCE) {
                return HEARING_TEMPORAL_PAST;
            } else {
                return HEARING_TEMPORAL_OVERDUE;
            }
        } elseif ($hearing_date === $today) {
            return HEARING_TEMPORAL_TODAY;
        } else {
            return HEARING_TEMPORAL_UPCOMING;
        }
    }
}

/**
 * Check if hearing is considered "active" (can be updated/modified)
 */
if (!function_exists('hearing_is_active')) {
    function hearing_is_active($hearing_date, $hearing_status) {
        $temporal = hearing_get_temporal_classification($hearing_date, $hearing_status);
        $status_def = hearing_get_status_definitions()[$hearing_status] ?? null;
        
        // Active if it's upcoming, today, or overdue and not in a final state
        return ($temporal === HEARING_TEMPORAL_UPCOMING || 
                $temporal === HEARING_TEMPORAL_TODAY || 
                $temporal === HEARING_TEMPORAL_OVERDUE) && 
               ($status_def['is_active'] ?? false);
    }
}

// =============================================================================
// STATUS TRANSITION RULES
// =============================================================================

/**
 * Define valid status transitions
 */
if (!function_exists('hearing_get_valid_transitions')) {
    function hearing_get_valid_transitions() {
        return [
            HEARING_STATUS_SCHEDULED => [
                HEARING_STATUS_IN_PROGRESS,
                HEARING_STATUS_COMPLETED,
                HEARING_STATUS_ADJOURNED,
                HEARING_STATUS_POSTPONED,
                HEARING_STATUS_CANCELLED,
                HEARING_STATUS_NO_APPEARANCE
            ],
            HEARING_STATUS_IN_PROGRESS => [
                HEARING_STATUS_COMPLETED,
                HEARING_STATUS_ADJOURNED,
                HEARING_STATUS_POSTPONED
            ],
            HEARING_STATUS_COMPLETED => [
                // Completed hearings generally cannot be changed
                // but may allow corrections with proper permissions
            ],
            HEARING_STATUS_ADJOURNED => [
                HEARING_STATUS_SCHEDULED, // When new date is set
                HEARING_STATUS_CANCELLED
            ],
            HEARING_STATUS_POSTPONED => [
                HEARING_STATUS_SCHEDULED, // When new date is set
                HEARING_STATUS_CANCELLED
            ],
            HEARING_STATUS_CANCELLED => [
                HEARING_STATUS_SCHEDULED // May be rescheduled
            ],
            HEARING_STATUS_NO_APPEARANCE => [
                HEARING_STATUS_SCHEDULED, // May be rescheduled
                HEARING_STATUS_CANCELLED
            ]
        ];
    }
}

/**
 * Check if status transition is valid
 */
if (!function_exists('hearing_is_valid_transition')) {
    function hearing_is_valid_transition($from_status, $to_status) {
        $valid_transitions = hearing_get_valid_transitions();
        return in_array($to_status, $valid_transitions[$from_status] ?? []);
    }
}

// =============================================================================
// BUSINESS RULES
// =============================================================================

/**
 * Business rule: Statuses that require a next_date
 */
if (!function_exists('hearing_status_requires_next_date')) {
    function hearing_status_requires_next_date($status) {
        return in_array($status, [
            HEARING_STATUS_ADJOURNED,
            HEARING_STATUS_POSTPONED
        ]);
    }
}

/**
 * Business rule: Statuses that automatically set is_completed
 */
if (!function_exists('hearing_status_is_completed')) {
    function hearing_status_is_completed($status) {
        return $status === HEARING_STATUS_COMPLETED;
    }
}

/**
 * Business rule: Statuses that prevent further modifications
 */
if (!function_exists('hearing_status_is_final')) {
    function hearing_status_is_final($status) {
        return in_array($status, [
            HEARING_STATUS_COMPLETED,
            HEARING_STATUS_CANCELLED
        ]);
    }
}

/**
 * Business rule: Get default hearing time
 */
if (!function_exists('hearing_get_default_time')) {
    function hearing_get_default_time() {
        return '10:00:00';
    }
}

/**
 * Business rule: Minimum advance notice for hearing scheduling (in days)
 */
if (!function_exists('hearing_get_min_advance_notice')) {
    function hearing_get_min_advance_notice() {
        return 1; // 1 day minimum notice
    }
}

/**
 * Business rule: Maximum hearing duration (in hours)
 */
if (!function_exists('hearing_get_max_duration')) {
    function hearing_get_max_duration() {
        return 8; // 8 hours maximum
    }
}

// =============================================================================
// VALIDATION HELPERS
// =============================================================================

/**
 * Validate hearing date constraints
 */
if (!function_exists('hearing_validate_date_constraints')) {
    function hearing_validate_date_constraints($hearing_date, $current_hearing_date = null, $allow_past_dates = false) {
        $errors = [];
        $today = date('Y-m-d');
        $min_advance = hearing_get_min_advance_notice();
        
        // Check if date is in the past (for new hearings) - skip if past dates are allowed
        if (!$allow_past_dates && $current_hearing_date === null && $hearing_date < $today) {
            $errors[] = 'Hearing date cannot be in the past';
        }
        
        // Check minimum advance notice (for new hearings) - skip if past dates are allowed
        if (!$allow_past_dates && $current_hearing_date === null && 
            strtotime($hearing_date) < strtotime("+{$min_advance} days")) {
            $errors[] = "Hearing must be scheduled at least {$min_advance} day(s) in advance";
        }
        
        // Check if next_date is after current hearing date
        if ($current_hearing_date !== null && 
            strtotime($hearing_date) <= strtotime($current_hearing_date)) {
            $errors[] = 'Next hearing date must be after current hearing date';
        }
        
        return $errors;
    }
}

/**
 * Validate hearing time constraints
 */
if (!function_exists('hearing_validate_time_constraints')) {
    function hearing_validate_time_constraints($hearing_time) {
        $errors = [];
        
        // Basic time format validation
        if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $hearing_time)) {
            $errors[] = 'Invalid time format (use HH:MM or HH:MM:SS)';
            return $errors;
        }
        
        // Business hours validation (8 AM to 6 PM)
        $time_parts = explode(':', $hearing_time);
        $hour = (int)$time_parts[0];
        
        if ($hour < 8 || $hour > 18) {
            $errors[] = 'Hearing time must be between 8:00 AM and 6:00 PM';
        }
        
        return $errors;
    }
}

/**
 * Validate status transition with business rules
 */
if (!function_exists('hearing_validate_status_transition')) {
    function hearing_validate_status_transition($current_status, $new_status, $hearing_date = null) {
        $errors = [];
        
        // Check if transition is valid
        if (!hearing_is_valid_transition($current_status, $new_status)) {
            $errors[] = "Cannot change status from '{$current_status}' to '{$new_status}'";
        }
        
        // Check temporal constraints
        if ($hearing_date) {
            $temporal = hearing_get_temporal_classification($hearing_date, $current_status);
            
            // Cannot mark future hearings as completed
            if ($new_status === HEARING_STATUS_COMPLETED && 
                $temporal === HEARING_TEMPORAL_UPCOMING) {
                $errors[] = 'Cannot mark future hearings as completed';
            }
            
            // Cannot mark past hearings as scheduled without proper justification
            if ($new_status === HEARING_STATUS_SCHEDULED && 
                $temporal === HEARING_TEMPORAL_PAST &&
                $current_status !== HEARING_STATUS_POSTPONED &&
                $current_status !== HEARING_STATUS_ADJOURNED) {
                $errors[] = 'Cannot reschedule past hearings without proper workflow';
            }
        }
        
        return $errors;
    }
}

/**
 * Auto-detect appropriate status based on date and current status
 */
if (!function_exists('hearing_auto_detect_status')) {
    function hearing_auto_detect_status($hearing_date, $current_status) {
        $temporal = hearing_get_temporal_classification($hearing_date, $current_status);
        
        // If hearing is overdue and still scheduled, suggest appropriate status
        if ($temporal === HEARING_TEMPORAL_OVERDUE && 
            $current_status === HEARING_STATUS_SCHEDULED) {
            return [
                'suggested_status' => HEARING_STATUS_NO_APPEARANCE,
                'reason' => 'Hearing date has passed and status is still scheduled'
            ];
        }
        
        return null;
    }
}

/**
 * Get appropriate status for past date entries
 */
if (!function_exists('hearing_get_default_past_status')) {
    function hearing_get_default_past_status($hearing_date) {
        $today = date('Y-m-d');
        
        if ($hearing_date < $today) {
            // For past dates, default to completed unless specified otherwise
            return HEARING_STATUS_COMPLETED;
        }
        
        return HEARING_STATUS_SCHEDULED;
    }
}

/**
 * Check if user can create historical hearings
 */
if (!function_exists('hearing_can_create_historical')) {
    function hearing_can_create_historical() {
        // For now, allow all users with create permission to add historical hearings
        // You can enhance this with a specific permission check
        if (function_exists('has_permission')) {
            return has_permission('cases', '', 'create');
        }
        // Fallback to true if permission function not available
        return true;
    }
}