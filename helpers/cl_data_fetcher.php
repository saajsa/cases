<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Client Data Fetcher Helper for Cases Module
 * Contains client-specific data fetching functions
 */

if (!function_exists('cl_get_client_cases')) {
    /**
     * Get all cases for a specific client
     *
     * @param int $client_id
     * @param array $filters
     * @return array
     */
    function cl_get_client_cases($client_id, $filters = [])
    {
        $CI = &get_instance();
        
        if (!$client_id) {
            return [];
        }
        
        $CI->db->select('*');
        $CI->db->from(db_prefix() . 'cases');
        $CI->db->where('client_id', $client_id);
        
        // Apply filters
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

if (!function_exists('cl_get_client_case_details')) {
    /**
     * Get detailed information about a specific case
     *
     * @param int $case_id
     * @param int $client_id
     * @return array|null
     */
    function cl_get_client_case_details($case_id, $client_id)
    {
        $CI = &get_instance();
        
        if (!$case_id || !$client_id) {
            return null;
        }
        
        // Validate access
        if (!validate_client_case_access($case_id, $client_id)) {
            return null;
        }
        
        $CI->db->select('c.*, cl.company as client_company');
        $CI->db->from(db_prefix() . 'cases c');
        $CI->db->join(db_prefix() . 'clients cl', 'cl.userid = c.client_id', 'left');
        $CI->db->where('c.id', $case_id);
        $CI->db->where('c.client_id', $client_id);
        
        $case = $CI->db->get()->row_array();
        
        if ($case) {
            // Get case hearings
            $case['hearings'] = cl_get_case_hearings($case_id);
            
            // Get case documents
            $case['documents'] = cl_get_case_documents($case_id);
            
            // Get case consultations
            $case['consultations'] = cl_get_case_consultations($case_id, $client_id);
        }
        
        return $case;
    }
}

if (!function_exists('cl_get_client_consultations')) {
    /**
     * Get consultations for a specific client
     *
     * @param int $client_id
     * @param array $filters
     * @return array
     */
    function cl_get_client_consultations($client_id, $filters = [])
    {
        $CI = &get_instance();
        
        if (!$client_id) {
            return [];
        }
        
        $CI->db->select('cc.*, s.firstname as staff_firstname, s.lastname as staff_lastname');
        $CI->db->from(db_prefix() . 'case_consultations cc');
        $CI->db->join(db_prefix() . 'staff s', 's.staffid = cc.staff_id', 'left');
        $CI->db->where('cc.client_id', $client_id);
        
        // Apply filters
        if (!empty($filters['status'])) {
            $CI->db->where('cc.status', $filters['status']);
        }
        
        if (!empty($filters['date_from'])) {
            $CI->db->where('DATE(cc.date_added) >=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $CI->db->where('DATE(cc.date_added) <=', $filters['date_to']);
        }
        
        $CI->db->order_by('cc.date_added', 'DESC');
        
        return $CI->db->get()->result_array();
    }
}

if (!function_exists('cl_get_case_consultations')) {
    /**
     * Get consultations for a specific case
     *
     * @param int $case_id
     * @param int $client_id
     * @return array
     */
    function cl_get_case_consultations($case_id, $client_id)
    {
        $CI = &get_instance();
        
        if (!$case_id || !$client_id) {
            return [];
        }
        
        $CI->db->select('cc.*, s.firstname as staff_firstname, s.lastname as staff_lastname');
        $CI->db->from(db_prefix() . 'case_consultations cc');
        $CI->db->join(db_prefix() . 'staff s', 's.staffid = cc.staff_id', 'left');
        $CI->db->where('cc.client_id', $client_id);
        // Note: If consultations are linked to cases, add case_id filter
        $CI->db->order_by('cc.date_added', 'DESC');
        
        return $CI->db->get()->result_array();
    }
}

if (!function_exists('cl_get_client_documents')) {
    /**
     * Get documents for a specific client
     *
     * @param int $client_id
     * @param array $filters
     * @return array
     */
    function cl_get_client_documents($client_id, $filters = [])
    {
        $CI = &get_instance();
        
        if (!$client_id) {
            return [];
        }
        
        $CI->db->select('f.*, c.case_title, c.case_number');
        $CI->db->from(db_prefix() . 'files f');
        $CI->db->join(db_prefix() . 'cases c', 'c.id = f.rel_id AND f.rel_type = "case"', 'left');
        
        // Build where clause for client access
        $where_clause = '(f.rel_type = "client" AND f.rel_id = ' . $client_id . ')';
        $where_clause .= ' OR (f.rel_type = "case" AND f.rel_id IN (SELECT id FROM ' . db_prefix() . 'cases WHERE client_id = ' . $client_id . '))';
        $where_clause .= ' OR (f.rel_type = "consultation" AND f.rel_id IN (SELECT id FROM ' . db_prefix() . 'case_consultations WHERE client_id = ' . $client_id . '))';
        
        $CI->db->where($where_clause);
        
        // Apply filters
        if (!empty($filters['file_type'])) {
            $CI->db->where('f.filetype', $filters['file_type']);
        }
        
        if (!empty($filters['rel_type'])) {
            $CI->db->where('f.rel_type', $filters['rel_type']);
        }
        
        if (!empty($filters['date_from'])) {
            $CI->db->where('DATE(f.dateadded) >=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $CI->db->where('DATE(f.dateadded) <=', $filters['date_to']);
        }
        
        $CI->db->order_by('f.dateadded', 'DESC');
        
        return $CI->db->get()->result_array();
    }
}

if (!function_exists('cl_get_case_documents')) {
    /**
     * Get documents for a specific case
     *
     * @param int $case_id
     * @return array
     */
    function cl_get_case_documents($case_id)
    {
        $CI = &get_instance();
        
        if (!$case_id) {
            return [];
        }
        
        $CI->db->select('*');
        $CI->db->from(db_prefix() . 'files');
        $CI->db->where('rel_type', 'case');
        $CI->db->where('rel_id', $case_id);
        $CI->db->order_by('dateadded', 'DESC');
        
        return $CI->db->get()->result_array();
    }
}

if (!function_exists('cl_get_case_hearings')) {
    /**
     * Get hearings for a specific case
     *
     * @param int $case_id
     * @return array
     */
    function cl_get_case_hearings($case_id)
    {
        $CI = &get_instance();
        
        if (!$case_id) {
            return [];
        }
        
        $CI->db->select('h.*, c.court_name, cr.room_name');
        $CI->db->from(db_prefix() . 'hearings h');
        $CI->db->join(db_prefix() . 'courts c', 'c.id = h.court_id', 'left');
        $CI->db->join(db_prefix() . 'court_rooms cr', 'cr.id = h.court_room_id', 'left');
        $CI->db->where('h.case_id', $case_id);
        $CI->db->order_by('h.date', 'ASC');
        
        return $CI->db->get()->result_array();
    }
}

if (!function_exists('cl_get_client_dashboard_stats')) {
    /**
     * Get dashboard statistics for a client
     *
     * @param int $client_id
     * @return array
     */
    function cl_get_client_dashboard_stats($client_id)
    {
        $CI = &get_instance();
        
        if (!$client_id) {
            return [
                'total_cases' => 0,
                'active_consultations' => 0,
                'total_documents' => 0,
                'upcoming_hearings' => 0
            ];
        }
        
        $stats = [];
        
        // Get total cases
        $CI->db->where('client_id', $client_id);
        $stats['total_cases'] = $CI->db->count_all_results(db_prefix() . 'cases');
        
        // Get active consultations
        $CI->db->where('client_id', $client_id);
        $CI->db->where('status', 'active');
        $stats['active_consultations'] = $CI->db->count_all_results(db_prefix() . 'case_consultations');
        
        // Get total documents
        $CI->db->select('COUNT(*) as total');
        $CI->db->from(db_prefix() . 'files f');
        $where_clause = '(f.rel_type = "client" AND f.rel_id = ' . $client_id . ')';
        $where_clause .= ' OR (f.rel_type = "case" AND f.rel_id IN (SELECT id FROM ' . db_prefix() . 'cases WHERE client_id = ' . $client_id . '))';
        $where_clause .= ' OR (f.rel_type = "consultation" AND f.rel_id IN (SELECT id FROM ' . db_prefix() . 'case_consultations WHERE client_id = ' . $client_id . '))';
        $CI->db->where($where_clause);
        $result = $CI->db->get()->row();
        $stats['total_documents'] = $result ? $result->total : 0;
        
        // Get upcoming hearings
        $CI->db->select('COUNT(*) as total');
        $CI->db->from(db_prefix() . 'hearings h');
        $CI->db->join(db_prefix() . 'cases c', 'c.id = h.case_id');
        $CI->db->where('c.client_id', $client_id);
        $CI->db->where('h.date >=', date('Y-m-d'));
        $result = $CI->db->get()->row();
        $stats['upcoming_hearings'] = $result ? $result->total : 0;
        
        return $stats;
    }
}

if (!function_exists('cl_get_client_recent_activity')) {
    /**
     * Get recent activity for a client
     *
     * @param int $client_id
     * @param int $limit
     * @return array
     */
    function cl_get_client_recent_activity($client_id, $limit = 10)
    {
        $CI = &get_instance();
        
        if (!$client_id) {
            return [];
        }
        
        $activity = [];
        
        // Get recent case updates
        $CI->db->select('id, case_title, case_number, date_updated, date_created');
        $CI->db->from(db_prefix() . 'cases');
        $CI->db->where('client_id', $client_id);
        $CI->db->where('date_updated >=', date('Y-m-d H:i:s', strtotime('-30 days')));
        $CI->db->order_by('date_updated', 'DESC');
        $CI->db->limit($limit);
        $cases = $CI->db->get()->result_array();
        
        foreach ($cases as $case) {
            $activity[] = [
                'type' => 'case_update',
                'date' => $case['date_updated'],
                'title' => 'Case Updated',
                'description' => 'Case "' . $case['case_title'] . '" (' . $case['case_number'] . ') was updated',
                'rel_id' => $case['id']
            ];
        }
        
        // Get recent consultations
        $CI->db->select('id, note, tag, date_added');
        $CI->db->from(db_prefix() . 'case_consultations');
        $CI->db->where('client_id', $client_id);
        $CI->db->where('date_added >=', date('Y-m-d H:i:s', strtotime('-30 days')));
        $CI->db->order_by('date_added', 'DESC');
        $CI->db->limit($limit);
        $consultations = $CI->db->get()->result_array();
        
        foreach ($consultations as $consultation) {
            $activity[] = [
                'type' => 'consultation_added',
                'date' => $consultation['date_added'],
                'title' => 'Consultation Added',
                'description' => 'New consultation: ' . ($consultation['tag'] ?: 'General consultation'),
                'rel_id' => $consultation['id']
            ];
        }
        
        // Get recent documents
        $CI->db->select('f.id, f.file_name, f.dateadded, f.rel_type');
        $CI->db->from(db_prefix() . 'files f');
        $where_clause = '(f.rel_type = "client" AND f.rel_id = ' . $client_id . ')';
        $where_clause .= ' OR (f.rel_type = "case" AND f.rel_id IN (SELECT id FROM ' . db_prefix() . 'cases WHERE client_id = ' . $client_id . '))';
        $where_clause .= ' OR (f.rel_type = "consultation" AND f.rel_id IN (SELECT id FROM ' . db_prefix() . 'case_consultations WHERE client_id = ' . $client_id . '))';
        $CI->db->where($where_clause);
        $CI->db->where('f.dateadded >=', date('Y-m-d H:i:s', strtotime('-30 days')));
        $CI->db->order_by('f.dateadded', 'DESC');
        $CI->db->limit($limit);
        $documents = $CI->db->get()->result_array();
        
        foreach ($documents as $doc) {
            $activity[] = [
                'type' => 'document_added',
                'date' => $doc['dateadded'],
                'title' => 'Document Added',
                'description' => 'Document "' . $doc['file_name'] . '" was added',
                'rel_id' => $doc['id']
            ];
        }
        
        // Get recent hearings
        $CI->db->select('h.id, h.date, h.time, h.purpose, c.case_title');
        $CI->db->from(db_prefix() . 'hearings h');
        $CI->db->join(db_prefix() . 'cases c', 'c.id = h.case_id');
        $CI->db->where('c.client_id', $client_id);
        $CI->db->where('h.date >=', date('Y-m-d'));
        $CI->db->order_by('h.date', 'ASC');
        $CI->db->limit($limit);
        $hearings = $CI->db->get()->result_array();
        
        foreach ($hearings as $hearing) {
            $activity[] = [
                'type' => 'hearing_scheduled',
                'date' => $hearing['date'] . ' ' . $hearing['time'],
                'title' => 'Upcoming Hearing',
                'description' => 'Hearing scheduled for case "' . $hearing['case_title'] . '" - ' . $hearing['purpose'],
                'rel_id' => $hearing['id']
            ];
        }
        
        // Sort all activity by date
        usort($activity, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        
        return array_slice($activity, 0, $limit);
    }
}

if (!function_exists('cl_get_document_safe')) {
    /**
     * Safely get document information with access validation
     *
     * @param int $document_id
     * @param int $client_id
     * @return array|null
     */
    function cl_get_document_safe($document_id, $client_id)
    {
        $CI = &get_instance();
        
        if (!$document_id || !$client_id) {
            return null;
        }
        
        // Validate access first
        if (!validate_client_document_access($document_id, $client_id)) {
            return null;
        }
        
        $CI->db->select('f.*, c.case_title, c.case_number');
        $CI->db->from(db_prefix() . 'files f');
        $CI->db->join(db_prefix() . 'cases c', 'c.id = f.rel_id AND f.rel_type = "case"', 'left');
        $CI->db->where('f.id', $document_id);
        
        return $CI->db->get()->row_array();
    }
}

if (!function_exists('cl_get_consultation_safe')) {
    /**
     * Safely get consultation information with access validation
     *
     * @param int $consultation_id
     * @param int $client_id
     * @return array|null
     */
    function cl_get_consultation_safe($consultation_id, $client_id)
    {
        $CI = &get_instance();
        
        if (!$consultation_id || !$client_id) {
            return null;
        }
        
        $CI->db->select('cc.*, s.firstname as staff_firstname, s.lastname as staff_lastname');
        $CI->db->from(db_prefix() . 'case_consultations cc');
        $CI->db->join(db_prefix() . 'staff s', 's.staffid = cc.staff_id', 'left');
        $CI->db->where('cc.id', $consultation_id);
        $CI->db->where('cc.client_id', $client_id);
        
        return $CI->db->get()->row_array();
    }
}

if (!function_exists('cl_get_timeline_events')) {
    /**
     * Get timeline events for a client's cases
     *
     * @param int $client_id
     * @param int $limit
     * @return array
     */
    function cl_get_timeline_events($client_id, $limit = 20)
    {
        $CI = &get_instance();
        
        if (!$client_id) {
            return [];
        }
        
        $timeline = [];
        
        // Get case events
        $CI->db->select('id, case_title, case_number, date_created, date_updated');
        $CI->db->from(db_prefix() . 'cases');
        $CI->db->where('client_id', $client_id);
        $CI->db->order_by('date_created', 'DESC');
        $cases = $CI->db->get()->result_array();
        
        foreach ($cases as $case) {
            $timeline[] = [
                'date' => $case['date_created'],
                'type' => 'case_created',
                'title' => 'Case Created',
                'description' => 'Case "' . $case['case_title'] . '" was created',
                'icon' => 'fa-balance-scale',
                'color' => 'primary'
            ];
        }
        
        // Get hearing events
        $CI->db->select('h.id, h.date, h.time, h.purpose, c.case_title');
        $CI->db->from(db_prefix() . 'hearings h');
        $CI->db->join(db_prefix() . 'cases c', 'c.id = h.case_id');
        $CI->db->where('c.client_id', $client_id);
        $CI->db->order_by('h.date', 'DESC');
        $hearings = $CI->db->get()->result_array();
        
        foreach ($hearings as $hearing) {
            $timeline[] = [
                'date' => $hearing['date'] . ' ' . $hearing['time'],
                'type' => 'hearing',
                'title' => 'Hearing Scheduled',
                'description' => 'Hearing for "' . $hearing['case_title'] . '" - ' . $hearing['purpose'],
                'icon' => 'fa-calendar',
                'color' => 'warning'
            ];
        }
        
        // Sort timeline by date
        usort($timeline, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        
        return array_slice($timeline, 0, $limit);
    }
}