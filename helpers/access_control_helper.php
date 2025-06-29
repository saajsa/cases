<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Access Control Helper for Cases Module
 * Provides comprehensive resource-level access control with ownership validation
 */

if (!function_exists('cases_has_resource_access')) {
    /**
     * Check if user has access to a specific resource with ownership validation
     * @param string $resource_type The type of resource (case, consultation, hearing, document)
     * @param int $resource_id The ID of the resource
     * @param string $action The action being performed (view, edit, delete, create)
     * @param array $options Additional options for access control
     * @return array Result with 'allowed' boolean and 'reason' string
     */
    function cases_has_resource_access($resource_type, $resource_id, $action, $options = []) {
        $CI = &get_instance();
        $current_user_id = get_staff_user_id();
        
        if (!$current_user_id) {
            return [
                'allowed' => false,
                'reason' => 'User not authenticated'
            ];
        }
        
        // Check basic role permissions first
        $permission_key = 'cases';
        if (!has_permission($permission_key, '', $action)) {
            return [
                'allowed' => false,
                'reason' => 'Insufficient role permissions'
            ];
        }
        
        // Admin users bypass ownership checks (configurable)
        $bypass_ownership = isset($options['admin_bypass']) ? $options['admin_bypass'] : true;
        if ($bypass_ownership && is_admin()) {
            return [
                'allowed' => true,
                'reason' => 'Admin access granted'
            ];
        }
        
        // For creation actions, check if user can create resources
        if ($action === 'create') {
            return cases_check_creation_access($resource_type, $options);
        }
        
        // For existing resources, validate ownership
        return cases_validate_ownership($resource_type, $resource_id, $action, $options);
    }
}

if (!function_exists('cases_validate_ownership')) {
    /**
     * Validate ownership of a specific resource
     * @param string $resource_type The type of resource
     * @param int $resource_id The ID of the resource
     * @param string $action The action being performed
     * @param array $options Additional options
     * @return array Result with 'allowed' boolean and 'reason' string
     */
    function cases_validate_ownership($resource_type, $resource_id, $action, $options = []) {
        $CI = &get_instance();
        $current_user_id = get_staff_user_id();
        
        switch ($resource_type) {
            case 'case':
                return cases_validate_case_ownership($resource_id, $action, $current_user_id, $options);
            case 'consultation':
                return cases_validate_consultation_ownership($resource_id, $action, $current_user_id, $options);
            case 'hearing':
                return cases_validate_hearing_ownership($resource_id, $action, $current_user_id, $options);
            case 'document':
                return cases_validate_document_ownership($resource_id, $action, $current_user_id, $options);
            default:
                return [
                    'allowed' => false,
                    'reason' => 'Unknown resource type'
                ];
        }
    }
}

if (!function_exists('cases_validate_case_ownership')) {
    /**
     * Validate case ownership and access
     * @param int $case_id Case ID
     * @param string $action Action being performed
     * @param int $user_id Current user ID
     * @param array $options Additional options
     * @return array Access result
     */
    function cases_validate_case_ownership($case_id, $action, $user_id, $options = []) {
        $CI = &get_instance();
        
        // Get case details
        $CI->db->select('id, client_id, created_by, assigned_to, status, is_public');
        $CI->db->where('id', $case_id);
        $case = $CI->db->get(db_prefix() . 'cases')->row();
        
        if (!$case) {
            return [
                'allowed' => false,
                'reason' => 'Case not found'
            ];
        }
        
        // Check various ownership scenarios
        $access_reasons = [];
        
        // 1. Creator access
        if ($case->created_by == $user_id) {
            $access_reasons[] = 'case_creator';
        }
        
        // 2. Assigned lawyer access
        if ($case->assigned_to == $user_id) {
            $access_reasons[] = 'assigned_lawyer';
        }
        
        // 3. Client team access (if user is part of client's team)
        if (cases_is_user_in_client_team($case->client_id, $user_id)) {
            $access_reasons[] = 'client_team_member';
        }
        
        // 4. Public case access (if enabled and case is marked public)
        if (isset($case->is_public) && $case->is_public && isset($options['allow_public']) && $options['allow_public']) {
            $access_reasons[] = 'public_case';
        }
        
        // 5. Department/supervisor access
        if (cases_is_department_supervisor($user_id, $case->created_by)) {
            $access_reasons[] = 'department_supervisor';
        }
        
        // Check action-specific permissions
        $allowed = cases_check_action_permissions($action, $access_reasons, $case, $options);
        
        if ($allowed) {
            return [
                'allowed' => true,
                'reason' => 'Access granted: ' . implode(', ', $access_reasons)
            ];
        }
        
        // Log access denial for audit
        cases_log_security_event('Case access denied', [
            'case_id' => $case_id,
            'action' => $action,
            'user_id' => $user_id,
            'attempted_access_reasons' => $access_reasons
        ], 'warning');
        
        return [
            'allowed' => false,
            'reason' => 'No valid ownership or access rights'
        ];
    }
}

if (!function_exists('cases_validate_consultation_ownership')) {
    /**
     * Validate consultation ownership and access
     * @param int $consultation_id Consultation ID
     * @param string $action Action being performed
     * @param int $user_id Current user ID
     * @param array $options Additional options
     * @return array Access result
     */
    function cases_validate_consultation_ownership($consultation_id, $action, $user_id, $options = []) {
        $CI = &get_instance();
        
        // Get consultation details
        $CI->db->select('id, client_id, staff_id, case_id, phase, date_added');
        $CI->db->where('id', $consultation_id);
        $consultation = $CI->db->get(db_prefix() . 'case_consultations')->row();
        
        if (!$consultation) {
            return [
                'allowed' => false,
                'reason' => 'Consultation not found'
            ];
        }
        
        $access_reasons = [];
        
        // 1. Staff who created the consultation
        if ($consultation->staff_id == $user_id) {
            $access_reasons[] = 'consultation_creator';
        }
        
        // 2. If consultation is linked to a case, check case access
        if ($consultation->case_id) {
            $case_access = cases_validate_case_ownership($consultation->case_id, 'view', $user_id, $options);
            if ($case_access['allowed']) {
                $access_reasons[] = 'linked_case_access';
            }
        }
        
        // 3. Client team access
        if (cases_is_user_in_client_team($consultation->client_id, $user_id)) {
            $access_reasons[] = 'client_team_member';
        }
        
        // 4. Department supervision
        if (cases_is_department_supervisor($user_id, $consultation->staff_id)) {
            $access_reasons[] = 'department_supervisor';
        }
        
        $allowed = cases_check_action_permissions($action, $access_reasons, $consultation, $options);
        
        if ($allowed) {
            return [
                'allowed' => true,
                'reason' => 'Access granted: ' . implode(', ', $access_reasons)
            ];
        }
        
        return [
            'allowed' => false,
            'reason' => 'No valid ownership or access rights to consultation'
        ];
    }
}

if (!function_exists('cases_validate_hearing_ownership')) {
    /**
     * Validate hearing ownership and access
     * @param int $hearing_id Hearing ID
     * @param string $action Action being performed
     * @param int $user_id Current user ID
     * @param array $options Additional options
     * @return array Access result
     */
    function cases_validate_hearing_ownership($hearing_id, $action, $user_id, $options = []) {
        $CI = &get_instance();
        
        // Get hearing details including case information
        $CI->db->select('h.id, h.case_id, h.created_by, h.status, c.client_id, c.created_by as case_creator, c.assigned_to');
        $CI->db->from(db_prefix() . 'hearings h');
        $CI->db->join(db_prefix() . 'cases c', 'c.id = h.case_id', 'left');
        $CI->db->where('h.id', $hearing_id);
        $hearing = $CI->db->get()->row();
        
        if (!$hearing) {
            return [
                'allowed' => false,
                'reason' => 'Hearing not found'
            ];
        }
        
        $access_reasons = [];
        
        // 1. Hearing creator
        if ($hearing->created_by == $user_id) {
            $access_reasons[] = 'hearing_creator';
        }
        
        // 2. Case ownership (delegate to case validation)
        if ($hearing->case_id) {
            $case_access = cases_validate_case_ownership($hearing->case_id, 'view', $user_id, $options);
            if ($case_access['allowed']) {
                $access_reasons[] = 'case_access';
            }
        }
        
        // 3. Court calendar access (for court staff)
        if (isset($options['court_staff']) && $options['court_staff'] && cases_is_court_staff($user_id)) {
            $access_reasons[] = 'court_staff';
        }
        
        $allowed = cases_check_action_permissions($action, $access_reasons, $hearing, $options);
        
        if ($allowed) {
            return [
                'allowed' => true,
                'reason' => 'Access granted: ' . implode(', ', $access_reasons)
            ];
        }
        
        return [
            'allowed' => false,
            'reason' => 'No valid ownership or access rights to hearing'
        ];
    }
}

if (!function_exists('cases_validate_document_ownership')) {
    /**
     * Validate document ownership and access
     * @param int $document_id Document ID
     * @param string $action Action being performed
     * @param int $user_id Current user ID
     * @param array $options Additional options
     * @return array Access result
     */
    function cases_validate_document_ownership($document_id, $action, $user_id, $options = []) {
        $CI = &get_instance();
        
        // Check if files table exists
        if (!$CI->db->table_exists(db_prefix() . 'files')) {
            return [
                'allowed' => false,
                'reason' => 'Document system not available'
            ];
        }
        
        // Get document details
        $CI->db->select('id, rel_type, rel_id, staffid, visible_to_customer');
        $CI->db->where('id', $document_id);
        $document = $CI->db->get(db_prefix() . 'files')->row();
        
        if (!$document) {
            return [
                'allowed' => false,
                'reason' => 'Document not found'
            ];
        }
        
        $access_reasons = [];
        
        // 1. Document uploader
        if ($document->staffid == $user_id) {
            $access_reasons[] = 'document_uploader';
        }
        
        // 2. Related resource access
        if ($document->rel_type === 'case' && $document->rel_id) {
            $case_access = cases_validate_case_ownership($document->rel_id, 'view', $user_id, $options);
            if ($case_access['allowed']) {
                $access_reasons[] = 'related_case_access';
            }
        }
        
        $allowed = cases_check_action_permissions($action, $access_reasons, $document, $options);
        
        if ($allowed) {
            return [
                'allowed' => true,
                'reason' => 'Access granted: ' . implode(', ', $access_reasons)
            ];
        }
        
        return [
            'allowed' => false,
            'reason' => 'No valid ownership or access rights to document'
        ];
    }
}

if (!function_exists('cases_check_action_permissions')) {
    /**
     * Check if specific action is allowed based on access reasons
     * @param string $action The action being performed
     * @param array $access_reasons List of reasons user has access
     * @param object $resource The resource object
     * @param array $options Additional options
     * @return bool Whether action is allowed
     */
    function cases_check_action_permissions($action, $access_reasons, $resource, $options = []) {
        if (empty($access_reasons)) {
            return false;
        }
        
        // Define action requirements
        $action_requirements = [
            'view' => ['any'], // Any access reason allows viewing
            'edit' => ['case_creator', 'assigned_lawyer', 'consultation_creator', 'hearing_creator', 'document_uploader', 'department_supervisor'],
            'delete' => ['case_creator', 'consultation_creator', 'hearing_creator', 'document_uploader', 'department_supervisor'],
            'create' => ['any'] // Handled separately in cases_check_creation_access
        ];
        
        $required_reasons = $action_requirements[$action] ?? ['case_creator', 'assigned_lawyer'];
        
        // If 'any' access reason is sufficient
        if (in_array('any', $required_reasons)) {
            return true;
        }
        
        // Check if user has required access reason
        foreach ($access_reasons as $reason) {
            if (in_array($reason, $required_reasons)) {
                return true;
            }
        }
        
        return false;
    }
}

if (!function_exists('cases_check_creation_access')) {
    /**
     * Check if user can create new resources
     * @param string $resource_type Type of resource to create
     * @param array $options Additional options
     * @return array Access result
     */
    function cases_check_creation_access($resource_type, $options = []) {
        $current_user_id = get_staff_user_id();
        
        if (!$current_user_id) {
            return [
                'allowed' => false,
                'reason' => 'User not authenticated'
            ];
        }
        
        // Check basic permissions
        $permission_checks = [
            'case' => has_permission('cases', '', 'create'),
            'consultation' => has_permission('cases', '', 'create'),
            'hearing' => has_permission('cases', '', 'create'),
            'document' => has_permission('cases', '', 'create')
        ];
        
        if (!isset($permission_checks[$resource_type]) || !$permission_checks[$resource_type]) {
            return [
                'allowed' => false,
                'reason' => 'Insufficient permissions to create ' . $resource_type
            ];
        }
        
        // Additional creation restrictions can be added here
        // e.g., quota limits, department restrictions, etc.
        
        return [
            'allowed' => true,
            'reason' => 'Creation permissions granted'
        ];
    }
}

if (!function_exists('cases_is_user_in_client_team')) {
    /**
     * Check if user is part of client's team
     * @param int $client_id Client ID
     * @param int $user_id User ID
     * @return bool Whether user is in client team
     */
    function cases_is_user_in_client_team($client_id, $user_id) {
        $CI = &get_instance();
        
        // Check if user is assigned to client
        $CI->db->where('clientid', $client_id);
        $CI->db->where('staff_id', $user_id);
        $assignment = $CI->db->get(db_prefix() . 'customer_admins')->row();
        
        return $assignment !== null;
    }
}

if (!function_exists('cases_is_department_supervisor')) {
    /**
     * Check if user is a supervisor of another user
     * @param int $supervisor_id Potential supervisor ID
     * @param int $subordinate_id Subordinate user ID
     * @return bool Whether user is supervisor
     */
    function cases_is_department_supervisor($supervisor_id, $subordinate_id) {
        $CI = &get_instance();
        
        // Simple implementation - can be enhanced based on org structure
        // Check if supervisor has admin role or specific supervisor permissions
        if (is_admin($supervisor_id)) {
            return true;
        }
        
        // Check for department-based supervision
        // This would require additional tables/logic for organizational hierarchy
        // For now, return false - implement based on specific requirements
        
        return false;
    }
}

if (!function_exists('cases_is_court_staff')) {
    /**
     * Check if user is court staff
     * @param int $user_id User ID
     * @return bool Whether user is court staff
     */
    function cases_is_court_staff($user_id) {
        // This would depend on how court staff are identified in the system
        // Could be a specific role, department, or custom field
        
        return has_permission('cases', '', 'court_management', $user_id);
    }
}

if (!function_exists('cases_enforce_resource_access')) {
    /**
     * Enforce resource access and exit with error if denied
     * @param string $resource_type Resource type
     * @param int $resource_id Resource ID
     * @param string $action Action being performed
     * @param array $options Additional options
     */
    function cases_enforce_resource_access($resource_type, $resource_id, $action, $options = []) {
        $access_result = cases_has_resource_access($resource_type, $resource_id, $action, $options);
        
        if (!$access_result['allowed']) {
            // Log the access denial
            cases_log_security_event('Resource access denied', [
                'resource_type' => $resource_type,
                'resource_id' => $resource_id,
                'action' => $action,
                'reason' => $access_result['reason'],
                'user_id' => get_staff_user_id()
            ], 'warning');
            
            // Return appropriate error response
            if (isset($options['ajax']) && $options['ajax']) {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Access denied: ' . $access_result['reason']
                ]);
                exit;
            } else {
                access_denied('cases');
            }
        }
    }
}

if (!function_exists('cases_get_accessible_resources')) {
    /**
     * Get list of resources user has access to
     * @param string $resource_type Resource type
     * @param string $action Action type
     * @param array $options Additional filters and options
     * @return array List of accessible resource IDs
     */
    function cases_get_accessible_resources($resource_type, $action = 'view', $options = []) {
        $CI = &get_instance();
        $current_user_id = get_staff_user_id();
        
        if (!$current_user_id) {
            return [];
        }
        
        // If admin and bypass is enabled, return all
        if (is_admin() && (!isset($options['admin_bypass']) || $options['admin_bypass'])) {
            return cases_get_all_resource_ids($resource_type, $options);
        }
        
        switch ($resource_type) {
            case 'case':
                return cases_get_accessible_cases($current_user_id, $action, $options);
            case 'consultation':
                return cases_get_accessible_consultations($current_user_id, $action, $options);
            case 'hearing':
                return cases_get_accessible_hearings($current_user_id, $action, $options);
            default:
                return [];
        }
    }
}

if (!function_exists('cases_get_accessible_cases')) {
    /**
     * Get cases user has access to
     * @param int $user_id User ID
     * @param string $action Action type
     * @param array $options Additional options
     * @return array Case IDs
     */
    function cases_get_accessible_cases($user_id, $action, $options = []) {
        $CI = &get_instance();
        
        $CI->db->select('DISTINCT c.id');
        $CI->db->from(db_prefix() . 'cases c');
        
        // Build WHERE conditions for accessible cases
        $CI->db->group_start();
        
        // Cases created by user
        $CI->db->or_where('c.created_by', $user_id);
        
        // Cases assigned to user
        $CI->db->or_where('c.assigned_to', $user_id);
        
        // Cases for clients user is assigned to
        $CI->db->or_where('c.client_id IN (SELECT clientid FROM ' . db_prefix() . 'customer_admins WHERE staff_id = ' . $user_id . ')');
        
        // Public cases (if enabled)
        if (isset($options['include_public']) && $options['include_public']) {
            $CI->db->or_where('c.is_public', 1);
        }
        
        $CI->db->group_end();
        
        // Apply additional filters
        if (isset($options['status'])) {
            $CI->db->where('c.status', $options['status']);
        }
        
        if (isset($options['limit'])) {
            $CI->db->limit($options['limit']);
        }
        
        $result = $CI->db->get()->result_array();
        return array_column($result, 'id');
    }
}

if (!function_exists('cases_get_accessible_consultations')) {
    /**
     * Get consultations user has access to
     * @param int $user_id User ID
     * @param string $action Action type
     * @param array $options Additional options
     * @return array Consultation IDs
     */
    function cases_get_accessible_consultations($user_id, $action, $options = []) {
        $CI = &get_instance();
        
        $CI->db->select('DISTINCT cc.id');
        $CI->db->from(db_prefix() . 'case_consultations cc');
        
        $CI->db->group_start();
        
        // Consultations created by user
        $CI->db->or_where('cc.staff_id', $user_id);
        
        // Consultations for cases user has access to
        $accessible_cases = cases_get_accessible_cases($user_id, 'view', $options);
        if (!empty($accessible_cases)) {
            $CI->db->or_where_in('cc.case_id', $accessible_cases);
        }
        
        // Consultations for clients user is assigned to
        $CI->db->or_where('cc.client_id IN (SELECT clientid FROM ' . db_prefix() . 'customer_admins WHERE staff_id = ' . $user_id . ')');
        
        $CI->db->group_end();
        
        if (isset($options['limit'])) {
            $CI->db->limit($options['limit']);
        }
        
        $result = $CI->db->get()->result_array();
        return array_column($result, 'id');
    }
}

if (!function_exists('cases_get_accessible_hearings')) {
    /**
     * Get hearings user has access to
     * @param int $user_id User ID
     * @param string $action Action type
     * @param array $options Additional options
     * @return array Hearing IDs
     */
    function cases_get_accessible_hearings($user_id, $action, $options = []) {
        $CI = &get_instance();
        
        $CI->db->select('DISTINCT h.id');
        $CI->db->from(db_prefix() . 'hearings h');
        
        $CI->db->group_start();
        
        // Hearings created by user
        $CI->db->or_where('h.created_by', $user_id);
        
        // Hearings for cases user has access to
        $accessible_cases = cases_get_accessible_cases($user_id, 'view', $options);
        if (!empty($accessible_cases)) {
            $CI->db->or_where_in('h.case_id', $accessible_cases);
        }
        
        $CI->db->group_end();
        
        if (isset($options['limit'])) {
            $CI->db->limit($options['limit']);
        }
        
        $result = $CI->db->get()->result_array();
        return array_column($result, 'id');
    }
}

if (!function_exists('cases_get_all_resource_ids')) {
    /**
     * Get all resource IDs for admin access
     * @param string $resource_type Resource type
     * @param array $options Additional options
     * @return array Resource IDs
     */
    function cases_get_all_resource_ids($resource_type, $options = []) {
        $CI = &get_instance();
        
        $table_map = [
            'case' => db_prefix() . 'cases',
            'consultation' => db_prefix() . 'case_consultations',
            'hearing' => db_prefix() . 'hearings'
        ];
        
        if (!isset($table_map[$resource_type])) {
            return [];
        }
        
        $CI->db->select('id');
        
        if (isset($options['limit'])) {
            $CI->db->limit($options['limit']);
        }
        
        $result = $CI->db->get($table_map[$resource_type])->result_array();
        return array_column($result, 'id');
    }
}