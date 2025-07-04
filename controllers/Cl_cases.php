<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Client Cases Controller
 * Handles client area functionality for the Cases module
 */
class Cl_cases extends ClientsController
{
    public function __construct()
    {
        parent::__construct();
        
        // Load Cases model
        $this->load->model('cases/Cases_model');
        
        // Load client security helper
        $helper_path = FCPATH . 'modules/cases/helpers/client_security_helper.php';
        if (file_exists($helper_path)) {
            require_once($helper_path);
        }
        
        // Load client data fetcher helper
        $helper_path = FCPATH . 'modules/cases/helpers/cl_data_fetcher.php';
        if (file_exists($helper_path)) {
            require_once($helper_path);
        }
        
        // Validate client session - use Perfex CRM's built-in validation
        if (!is_client_logged_in()) {
            redirect(site_url('authentication/login'));
        }
    }

    /**
     * Dashboard - Main client area landing page
     */
    public function index()
    {
        $client_id = get_client_user_id();
        
        try {
            // Get dashboard statistics
            $data['stats'] = $this->get_client_dashboard_stats($client_id);
            
            // Get recent activity
            $data['recent_activity'] = $this->get_client_recent_activity($client_id);
            
            // Get client documents for dashboard
            $data['client_documents'] = $this->get_client_documents_for_dashboard($client_id);
            
        } catch (Exception $e) {
            // Log error and provide fallback data
            log_message('error', 'Error loading dashboard for client ' . $client_id . ': ' . $e->getMessage());
            
            // Provide empty fallback data
            $data['stats'] = [
                'total_cases' => 0,
                'active_consultations' => 0,
                'total_documents' => 0,
                'upcoming_hearings' => 0
            ];
            $data['recent_activity'] = [];
            $data['client_documents'] = [];
        }
        
        // Set page title
        $data['title'] = 'My Cases Dashboard';
        
        // Load dashboard view
        $this->data($data);
        $this->view('cl_cases_list');
        $this->layout();
    }

    /**
     * Get dashboard statistics for client
     */
    private function get_client_dashboard_stats($client_id)
    {
        $stats = [
            'total_cases' => 0,
            'active_consultations' => 0,
            'total_documents' => 0,
            'upcoming_hearings' => 0
        ];

        if (!$client_id) {
            return $stats;
        }

        // Get total cases
        $this->db->where('client_id', $client_id);
        $stats['total_cases'] = $this->db->count_all_results(db_prefix() . 'cases');

        // Get active consultations (if table exists)
        if ($this->db->table_exists(db_prefix() . 'case_consultations')) {
            $this->db->where('client_id', $client_id);
            $stats['active_consultations'] = $this->db->count_all_results(db_prefix() . 'case_consultations');
        }

        // Get total documents
        $this->db->select('COUNT(*) as total');
        $this->db->from(db_prefix() . 'files f');
        $this->db->where('((f.rel_type = "client" AND f.rel_id = ' . $client_id . ') OR (f.rel_type = "case" AND f.rel_id IN (SELECT id FROM ' . db_prefix() . 'cases WHERE client_id = ' . $client_id . ')))');
        $result = $this->db->get()->row();
        $stats['total_documents'] = $result ? $result->total : 0;

        // Get upcoming hearings (if table exists)
        if ($this->db->table_exists(db_prefix() . 'hearings')) {
            $this->db->select('COUNT(*) as total');
            $this->db->from(db_prefix() . 'hearings h');
            $this->db->join(db_prefix() . 'cases c', 'c.id = h.case_id');
            $this->db->where('c.client_id', $client_id);
            $this->db->where('h.date >=', date('Y-m-d'));
            $result = $this->db->get()->row();
            $stats['upcoming_hearings'] = $result ? $result->total : 0;
        }

        return $stats;
    }

    /**
     * Get recent activity for client - simplified version
     */
    private function get_client_recent_activity($client_id)
    {
        $activity = [];

        if (!$client_id) {
            return $activity;
        }

        try {
            // Simple case activity only
            $this->db->select('id, case_title, case_number, date_created');
            $this->db->from(db_prefix() . 'cases');
            $this->db->where('client_id', $client_id);
            $this->db->order_by('date_created', 'DESC');
            $this->db->limit(5);
            $cases = $this->db->get()->result_array();

            foreach ($cases as $case) {
                $activity[] = [
                    'date' => $case['date_created'],
                    'title' => 'Case Created',
                    'description' => 'Case "' . htmlspecialchars($case['case_title']) . '" was created',
                    'icon' => 'fa-folder-open',
                    'color' => 'success'
                ];
            }

        } catch (Exception $e) {
            log_message('error', 'Error fetching activity: ' . $e->getMessage());
        }

        return $activity;
    }

    /**
     * Get client documents for dashboard display
     */
    private function get_client_documents_for_dashboard($client_id)
    {
        $documents = [
            'client_owned' => [],
            'contact_documents' => [],
            'recent_uploads' => []
        ];

        if (!$client_id) {
            return $documents;
        }

        // Get client-owned documents
        $this->db->select('id, file_name as name, dateadded as date_added, filetype as file_type');
        $this->db->from(db_prefix() . 'files');
        $this->db->where('rel_type', 'client');
        $this->db->where('rel_id', $client_id);
        $this->db->order_by('dateadded', 'DESC');
        $this->db->limit(5);
        $documents['client_owned'] = $this->db->get()->result_array();

        // Get recent uploads (last 30 days)
        $this->db->select('id, file_name as name, dateadded as date_added, filetype as file_type, rel_type as owner_type');
        $this->db->from(db_prefix() . 'files f');
        $this->db->where('((f.rel_type = "client" AND f.rel_id = ' . $client_id . ') OR (f.rel_type = "case" AND f.rel_id IN (SELECT id FROM ' . db_prefix() . 'cases WHERE client_id = ' . $client_id . ')))');
        $this->db->where('f.dateadded >=', date('Y-m-d H:i:s', strtotime('-30 days')));
        $this->db->order_by('f.dateadded', 'DESC');
        $this->db->limit(5);
        $documents['recent_uploads'] = $this->db->get()->result_array();

        return $documents;
    }

    /**
     * Cases listing page
     */
    public function cases()
    {
        $client_id = get_client_user_id();
        
        // Get all cases for this client - map fields correctly
        $data['cases'] = [];
        if ($client_id) {
            $this->db->select('c.*, c.date_filed as filing_date');
            $this->db->from(db_prefix() . 'cases c');
            $this->db->where('c.client_id', $client_id);
            $this->db->order_by('c.date_created', 'DESC');
            $cases = $this->db->get()->result_array();
            
            // Add additional data for each case
            foreach ($cases as &$case) {
                // Get court name via court_room_id
                if ($case['court_room_id']) {
                    $this->db->select('c.name, cr.court_no, cr.judge_name');
                    $this->db->from(db_prefix() . 'court_rooms cr');
                    $this->db->join(db_prefix() . 'courts c', 'c.id = cr.court_id');
                    $this->db->where('cr.id', $case['court_room_id']);
                    $court = $this->db->get()->row();
                    $case['court_name'] = $court ? $court->name : 'No Court Found';
                } else {
                    $case['court_name'] = 'No Court Assigned';
                }
                
                // Get next hearing
                $this->db->select('MIN(date) as next_hearing');
                $this->db->from(db_prefix() . 'hearings');
                $this->db->where('case_id', $case['id']);
                $this->db->where('date >=', date('Y-m-d'));
                $hearing = $this->db->get()->row();
                $case['next_hearing'] = $hearing ? $hearing->next_hearing : null;
                
                // Add case_type placeholder
                $case['case_type'] = 'General';
            }
            
            $data['cases'] = $cases;
        }
        
        $data['title'] = 'My Cases';
        
        $this->data($data);
        $this->view('cl_cases');
        $this->layout();
    }

    /**
     * Case details page
     */
    public function case_details($case_id)
    {
        $client_id = get_client_user_id();
        
        // Get case with enhanced data - same pattern as cases listing
        $this->db->select('c.*, c.date_filed as filing_date');
        $this->db->from(db_prefix() . 'cases c');
        $this->db->where('c.id', $case_id);
        $this->db->where('c.client_id', $client_id);
        $case = $this->db->get()->row_array();
        
        if (!$case) {
            show_404();
        }
        
        // Add enhanced data using same pattern
        // Get court name and judge via court_room_id
        if ($case['court_room_id']) {
            $this->db->select('c.name, cr.court_no, cr.judge_name');
            $this->db->from(db_prefix() . 'court_rooms cr');
            $this->db->join(db_prefix() . 'courts c', 'c.id = cr.court_id');
            $this->db->where('cr.id', $case['court_room_id']);
            $court = $this->db->get()->row();
            $case['court_name'] = $court ? $court->name : 'No Court Found';
            $case['judge_name'] = $court ? $court->judge_name : 'N/A';
        } else {
            $case['court_name'] = 'No Court Assigned';
            $case['judge_name'] = 'N/A';
        }
        
        // Get next hearing
        $this->db->select('MIN(date) as next_hearing');
        $this->db->from(db_prefix() . 'hearings');
        $this->db->where('case_id', $case['id']);
        $this->db->where('date >=', date('Y-m-d'));
        $hearing = $this->db->get()->row();
        $case['next_hearing'] = $hearing ? $hearing->next_hearing : null;
        
        // Add case_type placeholder
        $case['case_type'] = 'General';
        
        // Get timeline events for this case
        $timeline = [];
        
        // Add case creation event
        $timeline[] = [
            'date' => $case['date_created'],
            'title' => 'Case Created',
            'description' => 'Case "' . $case['case_title'] . '" was created and filed.'
        ];
        
        // Add filing date event if different from creation
        if ($case['date_filed'] && $case['date_filed'] != date('Y-m-d', strtotime($case['date_created']))) {
            $timeline[] = [
                'date' => $case['date_filed'],
                'title' => 'Case Filed',
                'description' => 'Case officially filed in court.'
            ];
        }
        
        // Add hearing events
        $this->db->select('date, time, description, status');
        $this->db->from(db_prefix() . 'hearings');
        $this->db->where('case_id', $case['id']);
        $this->db->order_by('date', 'ASC');
        $hearings = $this->db->get()->result_array();
        
        foreach ($hearings as $hearing) {
            $timeline[] = [
                'date' => $hearing['date'],
                'title' => 'Court Hearing',
                'description' => $hearing['description'] ?: 'Court hearing scheduled.',
                'status' => $hearing['status'],
                'type' => 'hearing'
            ];
        }
        
        // Sort timeline by date
        usort($timeline, function($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });
        
        $data['case'] = $case;
        $data['timeline'] = $timeline;
        $data['title'] = 'Case Details - ' . $case['case_title'];
        
        $this->data($data);
        $this->view('cl_case_details');
        $this->layout();
    }

    /**
     * Consultations page
     */
    public function consultations()
    {
        $client_id = get_client_user_id();
        
        // Get consultations for this client - simple direct query
        $data['consultations'] = [];
        if ($client_id) {
            $this->db->select('*');
            $this->db->from(db_prefix() . 'case_consultations');
            $this->db->where('client_id', $client_id);
            $this->db->order_by('date_added', 'DESC');
            $consultations = $this->db->get()->result_array();
            
            // Add enhanced data for each consultation
            foreach ($consultations as &$consultation) {
                // Map fields for view compatibility
                $consultation['consultation_date'] = $consultation['date_added'];
                $consultation['subject'] = $consultation['note'] ? 'Consultation Notes' : 'General Consultation';
                $consultation['consultation_type'] = isset($consultation['phase']) ? $consultation['phase'] : 'General';
                $consultation['status'] = 'Completed';
                $consultation['summary'] = $consultation['note'];
                $consultation['duration'] = 60;
                
                // Get case title separately if case_id exists
                if (isset($consultation['case_id']) && $consultation['case_id']) {
                    $this->db->select('case_title');
                    $this->db->from(db_prefix() . 'cases');
                    $this->db->where('id', $consultation['case_id']);
                    $case = $this->db->get()->row();
                    $consultation['case_title'] = $case ? $case->case_title : 'N/A';
                }
            }
            
            $data['consultations'] = $consultations;
        }
        
        $data['title'] = 'My Consultations';
        
        $this->data($data);
        $this->view('cl_consultations');
        $this->layout();
    }

    /**
     * Consultation details page
     */
    public function consultation_details($consultation_id)
    {
        $client_id = get_client_user_id();
        
        // Get consultation details - simple direct query
        $this->db->select('*');
        $this->db->from(db_prefix() . 'case_consultations');
        $this->db->where('id', $consultation_id);
        $this->db->where('client_id', $client_id);
        $consultation = $this->db->get()->row_array();
        
        if (!$consultation) {
            show_404();
        }
        
        // Add enhanced data for view compatibility
        $consultation['consultation_date'] = $consultation['date_added'];
        $consultation['subject'] = $consultation['note'] ? 'Consultation Notes' : 'General Consultation';
        $consultation['consultation_type'] = isset($consultation['phase']) ? $consultation['phase'] : 'General';
        $consultation['status'] = 'Completed';
        $consultation['summary'] = $consultation['note'];
        $consultation['duration'] = 60;
        
        // Get case details separately if case_id exists
        if (isset($consultation['case_id']) && $consultation['case_id']) {
            $this->db->select('case_title, case_number, status');
            $this->db->from(db_prefix() . 'cases');
            $this->db->where('id', $consultation['case_id']);
            $case = $this->db->get()->row();
            if ($case) {
                $consultation['case_title'] = $case->case_title;
                $consultation['case_number'] = $case->case_number;
                $consultation['case_status'] = $case->status;
            }
        }
        
        // Get staff details separately if staff_id exists
        if (isset($consultation['staff_id']) && $consultation['staff_id']) {
            $this->db->select('firstname, lastname');
            $this->db->from(db_prefix() . 'staff');
            $this->db->where('staffid', $consultation['staff_id']);
            $staff = $this->db->get()->row();
            if ($staff) {
                $consultation['staff_name'] = $staff->firstname . ' ' . $staff->lastname;
            } else {
                $consultation['staff_name'] = 'N/A';
            }
        } else {
            $consultation['staff_name'] = 'N/A';
        }
        
        // Simple timeline
        $timeline = [];
        $timeline[] = [
            'date' => $consultation['date_added'],
            'title' => 'Consultation Created',
            'description' => 'Consultation session was scheduled and recorded.',
            'type' => 'creation'
        ];
        
        if ($consultation['note']) {
            $timeline[] = [
                'date' => isset($consultation['last_updated']) ? $consultation['last_updated'] : $consultation['date_added'],
                'title' => 'Consultation Notes Added',
                'description' => $consultation['note'],
                'type' => 'notes'
            ];
        }
        
        $data['consultation'] = $consultation;
        $data['timeline'] = $timeline;
        $data['title'] = 'Consultation Details';
        
        $this->data($data);
        $this->view('cl_consultation_details');
        $this->layout();
    }

    /**
     * My documents page
     */
    public function my_documents()
    {
        $client_id = get_client_user_id();
        
        // Get documents for this client - simple direct query
        $data['documents'] = [];
        if ($client_id) {
            // Get client documents
            $this->db->select('*');
            $this->db->from(db_prefix() . 'files');
            $this->db->where('rel_type', 'client');
            $this->db->where('rel_id', $client_id);
            $this->db->order_by('dateadded', 'DESC');
            $client_docs = $this->db->get()->result_array();
            
            // Get case documents for this client
            $this->db->select('c.id');
            $this->db->from(db_prefix() . 'cases c');
            $this->db->where('c.client_id', $client_id);
            $case_ids = $this->db->get()->result_array();
            
            $case_docs = [];
            if (!empty($case_ids)) {
                $case_id_list = array_column($case_ids, 'id');
                $this->db->select('*');
                $this->db->from(db_prefix() . 'files');
                $this->db->where('rel_type', 'case');
                $this->db->where_in('rel_id', $case_id_list);
                $this->db->order_by('dateadded', 'DESC');
                $case_docs = $this->db->get()->result_array();
            }
            
            // Combine and enhance documents
            $all_documents = array_merge($client_docs, $case_docs);
            
            foreach ($all_documents as &$doc) {
                // Map fields for view compatibility
                $doc['name'] = $doc['file_name'];
                $doc['date_added'] = $doc['dateadded'];
                $doc['file_type'] = $doc['filetype'];
                $doc['owner_type'] = $doc['rel_type'];
                
                // Get case title if it's a case document
                if ($doc['rel_type'] == 'case') {
                    $this->db->select('case_title');
                    $this->db->from(db_prefix() . 'cases');
                    $this->db->where('id', $doc['rel_id']);
                    $case = $this->db->get()->row();
                    $doc['case_title'] = $case ? $case->case_title : 'N/A';
                }
            }
            
            // Sort by date
            usort($all_documents, function($a, $b) {
                return strtotime($b['dateadded']) - strtotime($a['dateadded']);
            });
            
            $data['documents'] = $all_documents;
        }
        
        $data['title'] = 'My Documents';
        
        $this->data($data);
        $this->view('cl_my_documents');
        $this->layout();
    }

    /**
     * Preview document
     */
    public function preview_document($document_id)
    {
        $client_id = get_client_user_id();
        
        // Get document details and validate access
        $this->db->where('id', $document_id);
        $document = $this->db->get(db_prefix() . 'files')->row_array();
        
        if (!$document) {
            echo '<div class="alert alert-danger">Document not found</div>';
            return;
        }
        
        // Simple access validation - check if document belongs to client or client's cases
        $has_access = false;
        if ($document['rel_type'] == 'client' && $document['rel_id'] == $client_id) {
            $has_access = true;
        } elseif ($document['rel_type'] == 'case') {
            $this->db->where('id', $document['rel_id']);
            $this->db->where('client_id', $client_id);
            $case = $this->db->get(db_prefix() . 'cases')->row();
            if ($case) {
                $has_access = true;
            }
        }
        
        if (!$has_access) {
            echo '<div class="alert alert-danger">Access denied</div>';
            return;
        }
        
        // Map fields for view compatibility
        $document['name'] = $document['file_name'];
        $document['type'] = $document['filetype'];
        $document['size'] = 'N/A'; // Size not stored in this implementation
        
        // Ensure proper file extension handling
        $document['file_extension'] = strtolower(pathinfo($document['file_name'], PATHINFO_EXTENSION));
        
        $data['document'] = $document;
        $this->load->view('cases/cl_document_preview', $data);
    }

    /**
     * Download document
     */
    public function download_document($document_id)
    {
        $client_id = get_client_user_id();
        
        // Get document details and validate access
        $this->db->where('id', $document_id);
        $document = $this->db->get(db_prefix() . 'files')->row_array();
        
        if (!$document) {
            show_404();
        }
        
        // Simple access validation
        $has_access = false;
        if ($document['rel_type'] == 'client' && $document['rel_id'] == $client_id) {
            $has_access = true;
        } elseif ($document['rel_type'] == 'case') {
            $this->db->where('id', $document['rel_id']);
            $this->db->where('client_id', $client_id);
            $case = $this->db->get(db_prefix() . 'cases')->row();
            if ($case) {
                $has_access = true;
            }
        }
        
        if (!$has_access) {
            show_404();
        }
        
        // Debug: Check the file paths being tried
        $upload_path = FCPATH . 'uploads/';
        $possible_paths = [];
        
        // Try multiple possible path structures based on Perfex CRM file storage patterns
        
        // Standard Perfex CRM document storage
        $possible_paths[] = $upload_path . 'documents/' . $document['file_name'];
        
        // Type-based subdirectories
        if ($document['rel_type'] == 'client') {
            $possible_paths[] = $upload_path . 'client/' . $document['rel_id'] . '/' . $document['file_name'];
            $possible_paths[] = $upload_path . 'clients/' . $document['rel_id'] . '/' . $document['file_name'];
            $possible_paths[] = $upload_path . $document['rel_type'] . '/' . $document['rel_id'] . '/' . $document['file_name'];
        } elseif ($document['rel_type'] == 'case') {
            $possible_paths[] = $upload_path . 'case/' . $document['rel_id'] . '/' . $document['file_name'];
            $possible_paths[] = $upload_path . 'cases/' . $document['rel_id'] . '/' . $document['file_name'];
            $possible_paths[] = $upload_path . $document['rel_type'] . '/' . $document['rel_id'] . '/' . $document['file_name'];
        }
        
        // Fallback: root uploads directory
        $possible_paths[] = $upload_path . $document['file_name'];
        
        // Check if external_link is set (for external files)
        if (isset($document['external_link']) && !empty($document['external_link'])) {
            redirect($document['external_link']);
            return;
        }
        
        // Try each possible path
        $file_path = null;
        foreach ($possible_paths as $path) {
            if (file_exists($path)) {
                $file_path = $path;
                break;
            }
        }
        
        if (!$file_path) {
            // Log debug information for troubleshooting
            log_message('error', 'File not found for document ID: ' . $document_id . 
                       '. Tried paths: ' . implode(', ', $possible_paths) . 
                       '. Document data: ' . json_encode($document));
            show_404();
        }
        
        // Force download
        $this->load->helper('download');
        force_download($document['file_name'], file_get_contents($file_path));
    }

    /**
     * View PDF document inline
     */
    public function view_pdf($document_id)
    {
        $client_id = get_client_user_id();
        
        // Get document details and validate access
        $this->db->where('id', $document_id);
        $document = $this->db->get(db_prefix() . 'files')->row_array();
        
        if (!$document) {
            show_404();
        }
        
        // Check if it's a PDF file (handle different formats)
        $file_type = strtolower($document['filetype']);
        $file_extension = strtolower(pathinfo($document['file_name'], PATHINFO_EXTENSION));
        $is_pdf = ($file_type == 'pdf' || $file_type == 'application/pdf' || $file_extension == 'pdf');
        
        if (!$is_pdf) {
            show_404();
        }
        
        // Simple access validation
        $has_access = false;
        if ($document['rel_type'] == 'client' && $document['rel_id'] == $client_id) {
            $has_access = true;
        } elseif ($document['rel_type'] == 'case') {
            $this->db->where('id', $document['rel_id']);
            $this->db->where('client_id', $client_id);
            $case = $this->db->get(db_prefix() . 'cases')->row();
            if ($case) {
                $has_access = true;
            }
        }
        
        if (!$has_access) {
            show_404();
        }
        
        // Try to build file path with multiple possibilities
        $upload_path = FCPATH . 'uploads/';
        $possible_paths = [];
        
        // Standard Perfex CRM document storage
        $possible_paths[] = $upload_path . 'documents/' . $document['file_name'];
        
        // Type-based subdirectories
        if ($document['rel_type'] == 'client') {
            $possible_paths[] = $upload_path . 'client/' . $document['rel_id'] . '/' . $document['file_name'];
            $possible_paths[] = $upload_path . 'clients/' . $document['rel_id'] . '/' . $document['file_name'];
            $possible_paths[] = $upload_path . $document['rel_type'] . '/' . $document['rel_id'] . '/' . $document['file_name'];
        } elseif ($document['rel_type'] == 'case') {
            $possible_paths[] = $upload_path . 'case/' . $document['rel_id'] . '/' . $document['file_name'];
            $possible_paths[] = $upload_path . 'cases/' . $document['rel_id'] . '/' . $document['file_name'];
            $possible_paths[] = $upload_path . $document['rel_type'] . '/' . $document['rel_id'] . '/' . $document['file_name'];
        }
        
        $possible_paths[] = $upload_path . $document['file_name'];
        
        $file_path = null;
        foreach ($possible_paths as $path) {
            if (file_exists($path)) {
                $file_path = $path;
                break;
            }
        }
        
        if (!$file_path) {
            show_404();
        }
        
        // Output PDF with proper headers
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $document['file_name'] . '"');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
    }

    /**
     * View image document inline
     */
    public function view_image($document_id)
    {
        $client_id = get_client_user_id();
        
        // Get document details and validate access
        $this->db->where('id', $document_id);
        $document = $this->db->get(db_prefix() . 'files')->row_array();
        
        if (!$document) {
            show_404();
        }
        
        // Check if it's an image file (handle different formats)
        $file_type = strtolower($document['filetype']);
        $file_extension = strtolower(pathinfo($document['file_name'], PATHINFO_EXTENSION));
        $image_types = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        $image_mime_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/bmp', 'image/webp'];
        
        $is_image = (in_array($file_extension, $image_types) || 
                     in_array($file_type, $image_types) || 
                     in_array($file_type, $image_mime_types));
        
        if (!$is_image) {
            show_404();
        }
        
        // Simple access validation
        $has_access = false;
        if ($document['rel_type'] == 'client' && $document['rel_id'] == $client_id) {
            $has_access = true;
        } elseif ($document['rel_type'] == 'case') {
            $this->db->where('id', $document['rel_id']);
            $this->db->where('client_id', $client_id);
            $case = $this->db->get(db_prefix() . 'cases')->row();
            if ($case) {
                $has_access = true;
            }
        }
        
        if (!$has_access) {
            show_404();
        }
        
        // Try to build file path with multiple possibilities
        $upload_path = FCPATH . 'uploads/';
        $possible_paths = [];
        
        // Standard Perfex CRM document storage
        $possible_paths[] = $upload_path . 'documents/' . $document['file_name'];
        
        // Type-based subdirectories
        if ($document['rel_type'] == 'client') {
            $possible_paths[] = $upload_path . 'client/' . $document['rel_id'] . '/' . $document['file_name'];
            $possible_paths[] = $upload_path . 'clients/' . $document['rel_id'] . '/' . $document['file_name'];
            $possible_paths[] = $upload_path . $document['rel_type'] . '/' . $document['rel_id'] . '/' . $document['file_name'];
        } elseif ($document['rel_type'] == 'case') {
            $possible_paths[] = $upload_path . 'case/' . $document['rel_id'] . '/' . $document['file_name'];
            $possible_paths[] = $upload_path . 'cases/' . $document['rel_id'] . '/' . $document['file_name'];
            $possible_paths[] = $upload_path . $document['rel_type'] . '/' . $document['rel_id'] . '/' . $document['file_name'];
        }
        
        $possible_paths[] = $upload_path . $document['file_name'];
        
        $file_path = null;
        foreach ($possible_paths as $path) {
            if (file_exists($path)) {
                $file_path = $path;
                break;
            }
        }
        
        if (!$file_path) {
            show_404();
        }
        
        // Get image info and output with proper headers
        $image_info = getimagesize($file_path);
        if ($image_info) {
            header('Content-Type: ' . $image_info['mime']);
            header('Content-Length: ' . filesize($file_path));
            header('Content-Disposition: inline; filename="' . $document['file_name'] . '"');
            readfile($file_path);
        } else {
            show_404();
        }
    }

    /**
     * Case documents page - shows documents for a specific case
     */
    public function documents()
    {
        $client_id = get_client_user_id();
        $case_id = $this->input->get('case');
        
        if (!$case_id) {
            redirect(site_url('cases/Cl_cases'));
        }
        
        // Validate that this case belongs to the client
        $this->db->select('*');
        $this->db->from(db_prefix() . 'cases');
        $this->db->where('id', $case_id);
        $this->db->where('client_id', $client_id);
        $case = $this->db->get()->row_array();
        
        if (!$case) {
            show_404();
        }
        
        // Get documents for this specific case
        $data['documents'] = [];
        $this->db->select('*');
        $this->db->from(db_prefix() . 'files');
        $this->db->where('rel_type', 'case');
        $this->db->where('rel_id', $case_id);
        $this->db->order_by('dateadded', 'DESC');
        $case_documents = $this->db->get()->result_array();
        
        // Enhance documents data
        foreach ($case_documents as &$doc) {
            $doc['name'] = $doc['file_name'];
            $doc['date_added'] = $doc['dateadded'];
            $doc['file_type'] = $doc['filetype'];
            $doc['owner_type'] = 'case';
        }
        
        $data['documents'] = $case_documents;
        $data['case'] = $case;
        $data['title'] = 'Case Documents - ' . $case['case_title'];
        
        $this->data($data);
        $this->view('cl_case_documents');
        $this->layout();
    }

    /**
     * Consultation documents page - shows documents for a specific consultation
     */
    public function consultation_documents($consultation_id)
    {
        $client_id = get_client_user_id();
        
        if (!$consultation_id) {
            redirect(site_url('cases/Cl_cases/consultations'));
        }
        
        // Validate that this consultation belongs to the client
        $this->db->select('*');
        $this->db->from(db_prefix() . 'case_consultations');
        $this->db->where('id', $consultation_id);
        $this->db->where('client_id', $client_id);
        $consultation = $this->db->get()->row_array();
        
        if (!$consultation) {
            show_404();
        }
        
        // Get case details if consultation is linked to a case
        if (isset($consultation['case_id']) && $consultation['case_id']) {
            $this->db->select('case_title, case_number');
            $this->db->from(db_prefix() . 'cases');
            $this->db->where('id', $consultation['case_id']);
            $case = $this->db->get()->row();
            if ($case) {
                $consultation['case_title'] = $case->case_title;
                $consultation['case_number'] = $case->case_number;
            }
        }
        
        // Get documents for this specific consultation
        $data['documents'] = [];
        $this->db->select('*');
        $this->db->from(db_prefix() . 'files');
        $this->db->where('rel_type', 'consultation');
        $this->db->where('rel_id', $consultation_id);
        $this->db->order_by('dateadded', 'DESC');
        $consultation_documents = $this->db->get()->result_array();
        
        // If no direct consultation documents, get case documents if consultation is linked to a case
        if (empty($consultation_documents) && isset($consultation['case_id']) && $consultation['case_id']) {
            $this->db->select('*');
            $this->db->from(db_prefix() . 'files');
            $this->db->where('rel_type', 'case');
            $this->db->where('rel_id', $consultation['case_id']);
            $this->db->order_by('dateadded', 'DESC');
            $consultation_documents = $this->db->get()->result_array();
        }
        
        // Enhance documents data
        foreach ($consultation_documents as &$doc) {
            $doc['name'] = $doc['file_name'];
            $doc['date_added'] = $doc['dateadded'];
            $doc['file_type'] = $doc['filetype'];
            $doc['owner_type'] = $doc['rel_type'];
        }
        
        // Add subject and type for display
        $consultation['subject'] = $consultation['note'] ? 'Consultation Notes' : 'General Consultation';
        $consultation['consultation_type'] = isset($consultation['phase']) ? $consultation['phase'] : 'General';
        
        $data['documents'] = $consultation_documents;
        $data['consultation'] = $consultation;
        $data['title'] = 'Consultation Documents - ' . $consultation['subject'];
        
        $this->data($data);
        $this->view('cl_consultation_documents');
        $this->layout();
    }
}