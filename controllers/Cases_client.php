<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Cases_client extends ClientsController
{
    public function __construct()
    {
        parent::__construct();
        
        // Load models
        $this->load->model('Cases_model');
        
        // Basic session validation - standard Perfex pattern
        if (!is_client_logged_in()) {
            redirect(site_url('authentication/login'));
        }
    }

    /**
     * Main cases listing for clients
     */
    public function index()
    {
        // Get the logged-in client ID
        $client_id = get_client_user_id();
        
        if (!$client_id) {
            show_error('Unable to identify client. Please log in again.');
        }
        
        // Get client's cases using existing method
        $cases = $this->get_client_cases($client_id);
        
        // Pass data to the view - standard Perfex pattern
        $this->data([
            'title' => 'My Cases',
            'cases' => $cases,
            'client_id' => $client_id
        ]);
        
        // Set page title
        $this->title('My Cases');
        
        // The view name
        $this->view('list');
        
        // Render the layout/view
        $this->layout();
    }

    /**
     * View specific case details and documents
     */
    public function view($case_id)
    {
        if (!$case_id || !is_numeric($case_id)) {
            show_404();
        }
        
        $client_id = get_client_user_id();
        
        try {
            // Validate client has access to this case
            if (function_exists('validate_client_case_access')) {
                if (!validate_client_case_access($case_id, $client_id)) {
                    if (function_exists('log_client_security_event')) {
                        log_client_security_event('Unauthorized case access attempt', [
                            'case_id' => $case_id,
                            'client_id' => $client_id
                        ], 'warning');
                    }
                    show_error('Case not found or you do not have permission to view it.');
                }
            }
            
            // Get case details and verify ownership
            $case = $this->get_client_case($case_id, $client_id);
            
            if (!$case) {
                show_error('Case not found or you do not have permission to view it.');
            }
            
            $data['case'] = $case;
            $data['title'] = _l('case_details') . ' - ' . $case['case_title'];
            
            // Get case documents
            $data['documents'] = $this->get_client_case_documents($case_id);
            
            // Get case hearings
            $data['hearings'] = $this->get_client_case_hearings($case_id);
            
            // Use Perfex CRM's client area view pattern
            $this->data($data);
            $this->view('view');
            $this->layout();
            
        } catch (Exception $e) {
            log_message('error', 'Error loading case details: ' . $e->getMessage());
            show_error('An error occurred while loading case details. Please try again later.');
        }
    }

    /**
     * Download a document (with security checks) - Client Area
     */
    public function download_document($file_id)
    {
        if (!$file_id || !is_numeric($file_id)) {
            show_404();
        }
        
        $client_id = get_client_user_id();
        
        try {
            // Validate client has access to this document
            if (function_exists('validate_client_document_access')) {
                if (!validate_client_document_access($file_id, $client_id)) {
                    if (function_exists('log_client_security_event')) {
                        log_client_security_event('Unauthorized document access attempt', [
                            'file_id' => $file_id,
                            'client_id' => $client_id
                        ], 'warning');
                    }
                    show_error('File not found or you do not have permission to access it.');
                }
            }
            
            // Get file details and verify client access
            $file = $this->get_client_file($file_id, $client_id);
            
            if (!$file) {
                show_error('File not found or you do not have permission to access it.');
            }
            
            // Security check: ensure file belongs to client's cases
            if (!$this->verify_file_access($file, $client_id)) {
                if (function_exists('log_client_security_event')) {
                    log_client_security_event('Failed file access verification', [
                        'file_id' => $file_id,
                        'client_id' => $client_id,
                        'file_type' => $file['rel_type']
                    ], 'warning');
                }
                show_error('Access denied.');
            }
            
            // Construct file path based on Perfex CRM's structure
            // Check common upload locations
            $possible_paths = [
                FCPATH . 'uploads/documents/' . $file['file_name'], // Custom documents location
                FCPATH . 'uploads/' . $file['rel_type'] . 's/' . $file['rel_id'] . '/' . $file['file_name'], // Module specific
                FCPATH . 'uploads/customers/' . $file['rel_id'] . '/' . $file['file_name'], // Customer files
                FCPATH . 'uploads/' . $file['file_name'] // Root uploads
            ];
            
            $file_path = null;
            foreach ($possible_paths as $path) {
                if (file_exists($path)) {
                    $file_path = $path;
                    break;
                }
            }
            
            if (!$file_path || !file_exists($file_path)) {
                if (function_exists('log_client_security_event')) {
                    log_client_security_event('File not found on server', [
                        'file_id' => $file_id,
                        'file_name' => $file['file_name'],
                        'attempted_paths' => $possible_paths
                    ], 'error');
                }
                show_error('File not found on server.');
            }
            
            // Log download activity
            $this->log_client_activity($client_id, 'Downloaded document: ' . $file['file_name'], $file['rel_type'], $file['rel_id']);
            if (function_exists('log_client_security_event')) {
                log_client_security_event('Document downloaded', [
                    'file_id' => $file_id,
                    'file_name' => $file['file_name'],
                    'rel_type' => $file['rel_type'],
                    'rel_id' => $file['rel_id']
                ], 'info');
            }
            
            // Force download
            $this->load->helper('download');
            force_download($file['file_name'], file_get_contents($file_path));
            
        } catch (Exception $e) {
            log_message('error', 'Error downloading file: ' . $e->getMessage());
            show_error('An error occurred while downloading the file. Please try again later.');
        }
    }

    /**
     * Get cases belonging to a specific client - Client Area specific
     */
    private function get_client_cases($client_id)
    {
        try {
            // Enhanced query with court details
            $this->db->select('
                c.id, 
                c.case_title, 
                c.case_number, 
                c.date_filed, 
                c.date_created, 
                c.client_id,
                c.court_room_id,
                cr.court_no,
                cr.judge_name,
                ct.name as court_name,
                CASE 
                    WHEN cr.court_no IS NOT NULL AND cr.judge_name IS NOT NULL 
                    THEN CONCAT("Court ", cr.court_no, " - Hon\'ble ", cr.judge_name)
                    WHEN ct.name IS NOT NULL 
                    THEN ct.name
                    ELSE "Court not specified"
                END as court_display
            ');
            $this->db->from(db_prefix() . 'cases c');
            $this->db->join(db_prefix() . 'court_rooms cr', 'cr.id = c.court_room_id', 'left');
            $this->db->join(db_prefix() . 'courts ct', 'ct.id = cr.court_id', 'left');
            $this->db->where('c.client_id', $client_id);
            $this->db->order_by('c.date_created', 'DESC');
            
            $query = $this->db->get();
            
            if (!$query) {
                log_message('error', 'Database error in get_client_cases: ' . $this->db->error()['message']);
                return [];
            }
            
            $cases = $query->result_array();
            
            // Add document and hearing counts
            foreach ($cases as &$case) {
                $case['document_count'] = $this->get_client_case_document_count($case['id']);
                $case['hearing_count'] = $this->get_client_case_hearing_count($case['id']);
            }
            
            return $cases;
            
        } catch (Exception $e) {
            log_message('error', 'Error in get_client_cases: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get a specific case if it belongs to the client
     */
    private function get_client_case($case_id, $client_id)
    {
        $this->db->select('
            c.*,
            cr.court_no,
            cr.judge_name,
            ct.name as court_name,
            CONCAT("Court ", COALESCE(cr.court_no, "N/A"), " - Hon\'ble ", COALESCE(cr.judge_name, "TBD")) as court_display
        ');
        $this->db->from(db_prefix() . 'cases c');
        $this->db->join(db_prefix() . 'court_rooms cr', 'cr.id = c.court_room_id', 'left');
        $this->db->join(db_prefix() . 'courts ct', 'ct.id = cr.court_id', 'left');
        $this->db->where('c.id', $case_id);
        $this->db->where('c.client_id', $client_id);
        
        return $this->db->get()->row_array();
    }

    /**
     * Get documents for a specific case - Client Area version
     */
    private function get_client_case_documents($case_id)
    {
        $this->db->select('
            f.*,
            CASE 
                WHEN f.rel_type = "case" THEN "Case Document"
                WHEN f.rel_type = "hearing" THEN CONCAT("Hearing Document - ", DATE_FORMAT(h.date, "%d-%m-%Y"))
                ELSE f.rel_type
            END as document_context
        ');
        $this->db->from(db_prefix() . 'files f');
        $this->db->join(db_prefix() . 'hearings h', 'h.id = f.rel_id AND f.rel_type = "hearing"', 'left');
        
        // Get case documents and hearing documents for this case
        $this->db->group_start();
        $this->db->where(['f.rel_type' => 'case', 'f.rel_id' => $case_id]);
        $this->db->or_where("f.rel_type = 'hearing' AND f.rel_id IN (SELECT id FROM " . db_prefix() . "hearings WHERE case_id = ?)", $case_id);
        $this->db->group_end();
        
        $this->db->order_by('f.dateadded', 'DESC');
        
        return $this->db->get()->result_array();
    }

    /**
     * Get hearings for a specific case - Client Area version
     */
    private function get_client_case_hearings($case_id)
    {
        $this->db->select('
            h.*,
            cr.court_no,
            cr.judge_name,
            ct.name as court_name
        ');
        $this->db->from(db_prefix() . 'hearings h');
        $this->db->join(db_prefix() . 'cases c', 'c.id = h.case_id', 'left');
        $this->db->join(db_prefix() . 'court_rooms cr', 'cr.id = c.court_room_id', 'left');
        $this->db->join(db_prefix() . 'courts ct', 'ct.id = cr.court_id', 'left');
        $this->db->where('h.case_id', $case_id);
        $this->db->order_by('h.date', 'DESC');
        
        return $this->db->get()->result_array();
    }

    /**
     * Get file details if accessible by client
     */
    private function get_client_file($file_id, $client_id)
    {
        $this->db->select('f.*');
        $this->db->from(db_prefix() . 'files f');
        
        // Complex join to verify client ownership for cases, hearings, and consultations
        $this->db->join('(
            SELECT "case" as type, id, client_id FROM ' . db_prefix() . 'cases 
            UNION ALL 
            SELECT "hearing" as type, h.id, c.client_id 
            FROM ' . db_prefix() . 'hearings h 
            JOIN ' . db_prefix() . 'cases c ON c.id = h.case_id
            UNION ALL
            SELECT "consultation" as type, id, client_id FROM ' . db_prefix() . 'case_consultations
        ) ownership', '
            f.rel_type = ownership.type AND f.rel_id = ownership.id
        ', 'inner');
        
        $this->db->where('f.id', $file_id);
        $this->db->where('ownership.client_id', $client_id);
        
        return $this->db->get()->row_array();
    }

    /**
     * Verify client has access to a file
     */
    private function verify_file_access($file, $client_id)
    {
        switch ($file['rel_type']) {
            case 'case':
                // Check if case belongs to client
                $this->db->where('id', $file['rel_id']);
                $this->db->where('client_id', $client_id);
                return $this->db->count_all_results(db_prefix() . 'cases') > 0;
                
            case 'hearing':
                // Check if hearing's case belongs to client
                $this->db->select('c.client_id');
                $this->db->from(db_prefix() . 'hearings h');
                $this->db->join(db_prefix() . 'cases c', 'c.id = h.case_id');
                $this->db->where('h.id', $file['rel_id']);
                $hearing = $this->db->get()->row();
                
                return $hearing && $hearing->client_id == $client_id;
                
            case 'consultation':
                // Check if consultation belongs to client
                $this->db->where('id', $file['rel_id']);
                $this->db->where('client_id', $client_id);
                return $this->db->count_all_results(db_prefix() . 'case_consultations') > 0;
                
            default:
                return false;
        }
    }

    /**
     * Get document count for a case - Client Area version
     */
    private function get_client_case_document_count($case_id)
    {
        $this->db->where('rel_type', 'case');
        $this->db->where('rel_id', $case_id);
        $case_docs = $this->db->count_all_results(db_prefix() . 'files');
        
        // Also count hearing documents
        $this->db->where("rel_type = 'hearing' AND rel_id IN (SELECT id FROM " . db_prefix() . "hearings WHERE case_id = ?)", $case_id);
        $hearing_docs = $this->db->count_all_results(db_prefix() . 'files');
        
        return $case_docs + $hearing_docs;
    }

    /**
     * Get hearing count for a case - Client Area version
     */
    private function get_client_case_hearing_count($case_id)
    {
        $this->db->where('case_id', $case_id);
        return $this->db->count_all_results(db_prefix() . 'hearings');
    }

    /**
     * Get all consultations for a specific client
     */
    private function get_client_consultations($client_id)
    {
        try {
            $this->db->select('*');
            $this->db->from(db_prefix() . 'case_consultations');
            $this->db->where('client_id', $client_id);
            $this->db->order_by('date_added', 'DESC');
            
            $consultations = $this->db->get()->result_array();
            
            // Add document counts for each consultation
            foreach ($consultations as &$consultation) {
                $consultation['document_count'] = $this->get_consultation_document_count($consultation['id']);
            }
            
            return $consultations;
            
        } catch (Exception $e) {
            log_message('error', 'Error in get_client_consultations: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get a specific consultation if it belongs to the client
     */
    private function get_client_consultation($consultation_id, $client_id)
    {
        $this->db->select('*');
        $this->db->from(db_prefix() . 'case_consultations');
        $this->db->where('id', $consultation_id);
        $this->db->where('client_id', $client_id);
        
        return $this->db->get()->row_array();
    }

    /**
     * Get documents for a specific consultation
     */
    private function get_consultation_documents($consultation_id, $client_id)
    {
        // First verify the consultation belongs to the client
        if (!$this->get_client_consultation($consultation_id, $client_id)) {
            return [];
        }
        
        $this->db->select('*');
        $this->db->from(db_prefix() . 'files');
        $this->db->where('rel_type', 'consultation');
        $this->db->where('rel_id', $consultation_id);
        $this->db->order_by('dateadded', 'DESC');
        
        return $this->db->get()->result_array();
    }

    /**
     * Check if consultation was upgraded to a case
     */
    private function get_consultation_related_case($consultation_id, $client_id)
    {
        // Check if there's a case that was created from this consultation
        $this->db->select('c.*');
        $this->db->from(db_prefix() . 'cases c');
        $this->db->where('c.consultation_id', $consultation_id);
        $this->db->where('c.client_id', $client_id);
        
        return $this->db->get()->row_array();
    }

    /**
     * Get a specific hearing if its case belongs to the client
     */
    private function get_client_hearing($hearing_id, $client_id)
    {
        $this->db->select('
            h.*,
            cr.court_no,
            cr.judge_name,
            ct.name as court_name
        ');
        $this->db->from(db_prefix() . 'hearings h');
        $this->db->join(db_prefix() . 'cases c', 'c.id = h.case_id');
        $this->db->join(db_prefix() . 'court_rooms cr', 'cr.id = c.court_room_id', 'left');
        $this->db->join(db_prefix() . 'courts ct', 'ct.id = cr.court_id', 'left');
        $this->db->where('h.id', $hearing_id);
        $this->db->where('c.client_id', $client_id);
        
        return $this->db->get()->row_array();
    }

    /**
     * Get documents for a specific hearing
     */
    private function get_hearing_documents($hearing_id, $client_id)
    {
        // First verify the hearing belongs to the client
        if (!$this->get_client_hearing($hearing_id, $client_id)) {
            return [];
        }
        
        $this->db->select('*');
        $this->db->from(db_prefix() . 'files');
        $this->db->where('rel_type', 'hearing');
        $this->db->where('rel_id', $hearing_id);
        $this->db->order_by('dateadded', 'DESC');
        
        return $this->db->get()->result_array();
    }

    /**
     * Get document count for a consultation
     */
    private function get_consultation_document_count($consultation_id)
    {
        $this->db->where('rel_type', 'consultation');
        $this->db->where('rel_id', $consultation_id);
        return $this->db->count_all_results(db_prefix() . 'files');
    }

    /**
     * Log client activity
     */
    private function log_client_activity($client_id, $description, $rel_type = null, $rel_id = null)
    {
        $log_data = [
            'client_id' => $client_id,
            'contact_id' => get_contact_user_id(),
            'description' => $description,
            'rel_type' => $rel_type,
            'rel_id' => $rel_id,
            'date' => date('Y-m-d H:i:s'),
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent()
        ];
        
        // Log to activity log table if exists
        if ($this->db->table_exists(db_prefix() . 'client_activity_log')) {
            $this->db->insert(db_prefix() . 'client_activity_log', $log_data);
        }
        
        // Always log to CI logs
        log_message('info', "Client {$client_id} activity: {$description}");
    }

    /**
     * List all consultations for the client
     */
    public function consultations()
    {
        $client_id = get_client_user_id();
        
        if (!$client_id) {
            show_error('Unable to identify client. Please log in again.');
        }
        
        try {
            // Get client's consultations
            $consultations = $this->get_client_consultations($client_id);
            
            $data = [
                'title' => 'My Consultations',
                'consultations' => $consultations,
                'client_id' => $client_id
            ];
            
            $this->data($data);
            $this->title('My Consultations');
            $this->view('consultations');
            $this->layout();
            
        } catch (Exception $e) {
            log_message('error', 'Error loading consultations: ' . $e->getMessage());
            show_error('An error occurred while loading consultations. Please try again later.');
        }
    }

    /**
     * View consultation details with contextual documents
     */
    public function consultation($consultation_id)
    {
        if (!$consultation_id || !is_numeric($consultation_id)) {
            show_404();
        }
        
        $client_id = get_client_user_id();
        
        try {
            // Get consultation details and verify ownership
            $consultation = $this->get_client_consultation($consultation_id, $client_id);
            
            if (!$consultation) {
                show_error('Consultation not found or you do not have permission to view it.');
            }
            
            $data['consultation'] = $consultation;
            $data['title'] = 'Consultation Details - ' . ($consultation['tag'] ?: 'Consultation #' . $consultation['id']);
            
            // Get consultation documents
            $data['consultation_documents'] = $this->get_consultation_documents($consultation_id, $client_id);
            
            // Check if consultation was upgraded to a case
            $data['related_case'] = $this->get_consultation_related_case($consultation_id, $client_id);
            
            $this->data($data);
            $this->view('consultation_detail');
            $this->layout();
            
        } catch (Exception $e) {
            log_message('error', 'Error loading consultation details: ' . $e->getMessage());
            show_error('An error occurred while loading consultation details. Please try again later.');
        }
    }

    /**
     * View hearing details with contextual documents
     */
    public function hearing($hearing_id)
    {
        if (!$hearing_id || !is_numeric($hearing_id)) {
            show_404();
        }
        
        $client_id = get_client_user_id();
        
        try {
            // Get hearing details and verify ownership
            $hearing = $this->get_client_hearing($hearing_id, $client_id);
            
            if (!$hearing) {
                show_error('Hearing not found or you do not have permission to view it.');
            }
            
            $data['hearing'] = $hearing;
            $data['title'] = 'Hearing Details - ' . _dt($hearing['date']);
            
            // Get hearing documents
            $data['hearing_documents'] = $this->get_hearing_documents($hearing_id, $client_id);
            
            // Get parent case information
            $data['parent_case'] = $this->get_client_case($hearing['case_id'], $client_id);
            
            $this->data($data);
            $this->view('hearing_detail');
            $this->layout();
            
        } catch (Exception $e) {
            log_message('error', 'Error loading hearing details: ' . $e->getMessage());
            show_error('An error occurred while loading hearing details. Please try again later.');
        }
    }

    /**
     * AJAX endpoint to get case details
     */
    public function get_case_details($case_id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        header('Content-Type: application/json');
        
        $client_id = get_client_user_id();
        
        try {
            $case = $this->get_client_case($case_id, $client_id);
            
            if (!$case) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Case not found or access denied'
                ]);
                return;
            }
            
            echo json_encode([
                'success' => true,
                'case' => $case
            ]);
            
        } catch (Exception $e) {
            log_message('error', 'Error getting case details: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error loading case details'
            ]);
        }
    }
    
}