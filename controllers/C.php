<?php

defined('BASEPATH') or exit('No direct script access allowed');

class C extends ClientsController
{
    public function __construct()
    {
        parent::__construct();
        
        // Basic authentication check
        if (!is_client_logged_in()) {
            redirect(site_url('authentication/login'));
        }
    }

    public function index()
    {
        // DASHBOARD PAGE - Overview with client documents
        $client_id = get_client_user_id();
        
        if (!$client_id) {
            show_error('Unable to identify client. Please log in again.');
        }
        
        // Get cases with court information
        $cases = [];
        try {
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
            
            if ($query) {
                $cases = $query->result_array();
                
                // Add next hearing date and simple counts
                foreach ($cases as &$case) {
                    // Get next hearing date
                    $this->db->select('date');
                    $this->db->from(db_prefix() . 'hearings');
                    $this->db->where('case_id', $case['id']);
                    $this->db->where('date >=', date('Y-m-d'));
                    $this->db->order_by('date', 'ASC');
                    $this->db->limit(1);
                    $hearing_query = $this->db->get();
                    
                    if ($hearing_query && $hearing_query->num_rows() > 0) {
                        $case['next_hearing_date'] = $hearing_query->row()->date;
                    } else {
                        $case['next_hearing_date'] = null;
                    }
                    
                    // Get document count
                    $this->db->where('rel_type', 'case');
                    $this->db->where('rel_id', $case['id']);
                    $case_docs = $this->db->count_all_results(db_prefix() . 'files');
                    
                    // Get hearing document count
                    $this->db->select('COUNT(*) as count');
                    $this->db->from(db_prefix() . 'files f');
                    $this->db->join(db_prefix() . 'hearings h', 'h.id = f.rel_id');
                    $this->db->where('f.rel_type', 'hearing');
                    $this->db->where('h.case_id', $case['id']);
                    $hearing_docs_query = $this->db->get();
                    $hearing_docs = $hearing_docs_query ? $hearing_docs_query->row()->count : 0;
                    
                    $case['document_count'] = $case_docs + $hearing_docs;
                    
                    // Get hearing count
                    $this->db->where('case_id', $case['id']);
                    $case['hearing_count'] = $this->db->count_all_results(db_prefix() . 'hearings');
                }
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error in get_client_cases: ' . $e->getMessage());
            $cases = [];
        }
        
        // Get client documents (general documents not linked to specific cases)
        $client_documents = [];
        try {
            $this->db->select('f.*, "Client Document" as document_context');
            $this->db->from(db_prefix() . 'files f');
            $this->db->where('f.rel_type', 'client');  // Fixed: changed from 'customer' to 'client'
            $this->db->where('f.rel_id', $client_id);
            $this->db->order_by('f.dateadded', 'DESC');
            $this->db->limit(5); // Limit to recent documents
            $client_docs_query = $this->db->get();
            
            if ($client_docs_query) {
                $client_documents = $client_docs_query->result_array();
            }
        } catch (Exception $e) {
            log_message('error', 'Error fetching client documents: ' . $e->getMessage());
        }
        
        // Get consultations for this client
        $consultations = [];
        try {
            $this->db->select('cc.*, cc.note, cc.tag, cc.date_added, cc.phase');
            $this->db->from(db_prefix() . 'case_consultations cc');
            $this->db->where('cc.client_id', $client_id);
            $this->db->order_by('cc.date_added', 'DESC');
            $this->db->limit(5); // Limit to recent consultations
            $consultations_query = $this->db->get();
            
            if ($consultations_query) {
                $consultations = $consultations_query->result_array();
                
                // Get document count for each consultation
                foreach ($consultations as &$consultation) {
                    $this->db->where('rel_type', 'consultation');
                    $this->db->where('rel_id', $consultation['id']);
                    $consultation['document_count'] = $this->db->count_all_results(db_prefix() . 'files');
                }
            }
        } catch (Exception $e) {
            log_message('error', 'Error fetching consultations: ' . $e->getMessage());
        }

        // Calculate totals for dashboard
        $total_cases = count($cases);
        $total_documents = count($client_documents);
        foreach ($cases as $case) {
            $total_documents += (int)($case['document_count'] ?? 0);
        }
        $total_hearings = 0;
        foreach ($cases as $case) {
            $total_hearings += (int)($case['hearing_count'] ?? 0);
        }

        // Pass data to the view
        $this->data([
            'title' => 'Legal Dashboard',
            'total_cases' => $total_cases,
            'total_documents' => $total_documents,
            'total_hearings' => $total_hearings,
            'client_documents' => $client_documents,
            'recent_activity' => $this->get_recent_activity($client_id, 5),
            'client_id' => $client_id
        ]);
        
        // Set page title
        $this->title('Legal Dashboard');
        
        // The view name
        $this->view('legal_dashboard');
        
        // Render the layout/view
        $this->layout();
    }
    
    /**
     * CASES PAGE - Cases + Hearings + Related Documents
     */
    public function cases()
    {
        $client_id = get_client_user_id();
        
        if (!$client_id) {
            show_error('Unable to identify client. Please log in again.');
        }
        
        try {
            // Get cases with detailed information
            $cases = $this->get_detailed_cases($client_id);
            
            // Get all case-related documents and hearings
            $case_documents = [];
            $case_hearings = [];
            
            foreach ($cases as $case) {
                $case_documents[$case['id']] = $this->get_case_documents($case['id']);
                $case_hearings[$case['id']] = $this->get_case_hearings($case['id']);
            }
            
            $this->data([
                'title' => 'My Cases & Hearings',
                'cases' => $cases,
                'case_documents' => $case_documents,
                'case_hearings' => $case_hearings,
                'client_id' => $client_id
            ]);
            
            $this->title('Cases & Hearings');
            $this->view('cases_hearings');
            $this->layout();
            
        } catch (Exception $e) {
            log_message('error', 'Error loading client cases: ' . $e->getMessage());
            show_error('An error occurred while loading your cases. Please try again later.');
        }
    }
    
    /**
     * CONSULTATIONS PAGE - Consultations + Related Documents  
     */
    public function consultations()
    {
        $client_id = get_client_user_id();
        
        if (!$client_id) {
            show_error('Unable to identify client. Please log in again.');
        }
        
        try {
            // Get all client's consultations
            $consultations = $this->get_detailed_consultations($client_id);
            
            // Get consultation-related documents
            $consultation_documents = [];
            foreach ($consultations as $consultation) {
                $consultation_documents[$consultation['id']] = $this->get_consultation_documents_detailed($consultation['id']);
            }
            
            $this->data([
                'title' => 'My Consultations',
                'consultations' => $consultations,
                'consultation_documents' => $consultation_documents,
                'client_id' => $client_id
            ]);
            
            $this->title('Consultations');
            $this->view('consultations');
            $this->layout();
            
        } catch (Exception $e) {
            log_message('error', 'Error loading client consultations: ' . $e->getMessage());
            show_error('An error occurred while loading your consultations. Please try again later.');
        }
    }
    
    /**
     * View documents for a specific case
     */
    public function documents($case_id)
    {
        if (!$case_id || !is_numeric($case_id)) {
            show_404();
        }
        
        $client_id = get_client_user_id();
        
        if (!$client_id) {
            show_error('Unable to identify client. Please log in again.');
        }
        
        try {
            // Verify client owns this case
            $this->db->select('id, case_title, case_number');
            $this->db->from(db_prefix() . 'cases');
            $this->db->where('id', $case_id);
            $this->db->where('client_id', $client_id);
            $case_query = $this->db->get();
            
            if (!$case_query || $case_query->num_rows() == 0) {
                show_error('Case not found or you do not have permission to view it.');
            }
            
            $case = $case_query->row_array();
            
            // Get case documents
            $case_documents = [];
            $this->db->select('f.*, "Case Document" as document_context');
            $this->db->from(db_prefix() . 'files f');
            $this->db->where('f.rel_type', 'case');
            $this->db->where('f.rel_id', $case_id);
            $this->db->order_by('f.dateadded', 'DESC');
            $case_docs_query = $this->db->get();
            
            if ($case_docs_query) {
                $case_documents = $case_docs_query->result_array();
            }
            
            // Get hearing documents with hearing date
            $hearing_documents = [];
            $this->db->select('f.*, h.date as hearing_date, CONCAT("Hearing Document - ", DATE_FORMAT(h.date, "%d-%m-%Y")) as document_context');
            $this->db->from(db_prefix() . 'files f');
            $this->db->join(db_prefix() . 'hearings h', 'h.id = f.rel_id');
            $this->db->where('f.rel_type', 'hearing');
            $this->db->where('h.case_id', $case_id);
            $this->db->order_by('f.dateadded', 'DESC');
            $hearing_docs_query = $this->db->get();
            
            if ($hearing_docs_query) {
                $hearing_documents = $hearing_docs_query->result_array();
            }
            
            // Combine all documents for total count
            $all_documents = array_merge($case_documents, $hearing_documents);
            usort($all_documents, function($a, $b) {
                return strtotime($b['dateadded']) - strtotime($a['dateadded']);
            });
            
            // Pass data to view
            $this->data([
                'title' => 'Documents - ' . $case['case_title'],
                'case' => $case,
                'case_documents' => $case_documents,
                'hearing_documents' => $hearing_documents,
                'all_documents' => $all_documents,
                'client_id' => $client_id
            ]);
            
            $this->title('Case Documents');
            $this->view('case_documents');
            $this->layout();
            
        } catch (Exception $e) {
            log_message('error', 'Error loading case documents: ' . $e->getMessage());
            show_error('An error occurred while loading case documents. Please try again later.');
        }
    }
    
    /**
     * Download a document (client area)
     */
    public function download($file_id)
    {
        if (!$file_id || !is_numeric($file_id)) {
            show_404();
        }
        
        $client_id = get_client_user_id();
        
        if (!$client_id) {
            show_error('Unable to identify client. Please log in again.');
        }
        
        try {
            // Get file details and verify client access
            $this->db->select('f.*, f.file_name, f.filetype');
            $this->db->from(db_prefix() . 'files f');
            $this->db->where('f.id', $file_id);
            $file_query = $this->db->get();
            
            if (!$file_query || $file_query->num_rows() == 0) {
                show_error('File not found.');
            }
            
            $file = $file_query->row_array();
            
            // Verify client has access to this file
            $has_access = false;
            
            if ($file['rel_type'] == 'case') {
                // Check if case belongs to client
                $this->db->select('client_id');
                $this->db->from(db_prefix() . 'cases');
                $this->db->where('id', $file['rel_id']);
                $this->db->where('client_id', $client_id);
                $case_check = $this->db->get();
                $has_access = ($case_check && $case_check->num_rows() > 0);
                
            } elseif ($file['rel_type'] == 'hearing') {
                // Check if hearing's case belongs to client
                $this->db->select('c.client_id');
                $this->db->from(db_prefix() . 'hearings h');
                $this->db->join(db_prefix() . 'cases c', 'c.id = h.case_id');
                $this->db->where('h.id', $file['rel_id']);
                $this->db->where('c.client_id', $client_id);
                $hearing_check = $this->db->get();
                $has_access = ($hearing_check && $hearing_check->num_rows() > 0);
                
            } elseif ($file['rel_type'] == 'client') {
                // Check if client document belongs to this client
                $has_access = ($file['rel_id'] == $client_id);
                
            } elseif ($file['rel_type'] == 'consultation') {
                // Check if consultation belongs to client
                $this->db->select('client_id');
                $this->db->from(db_prefix() . 'case_consultations');
                $this->db->where('id', $file['rel_id']);
                $this->db->where('client_id', $client_id);
                $consultation_check = $this->db->get();
                $has_access = ($consultation_check && $consultation_check->num_rows() > 0);
            }
            
            if (!$has_access) {
                show_error('You do not have permission to access this file.');
            }
            
            // Construct file path
            $file_path = FCPATH . 'uploads/documents/' . $file['file_name'];
            
            if (!file_exists($file_path)) {
                show_error('File not found on server.');
            }
            
            // Log download activity
            log_message('info', "Client {$client_id} downloaded document: {$file['file_name']}");
            
            // Force download
            $this->load->helper('download');
            force_download($file['file_name'], file_get_contents($file_path));
            
        } catch (Exception $e) {
            log_message('error', 'Error downloading file: ' . $e->getMessage());
            show_error('An error occurred while downloading the file. Please try again later.');
        }
    }
    
    /**
     * View a document inline (client area)
     */
    public function view_document($file_id)
    {
        if (!$file_id || !is_numeric($file_id)) {
            show_404();
        }
        
        $client_id = get_client_user_id();
        
        if (!$client_id) {
            show_error('Unable to identify client. Please log in again.');
        }
        
        try {
            // Get file details and verify client access
            $this->db->select('f.*, f.file_name, f.filetype');
            $this->db->from(db_prefix() . 'files f');
            $this->db->where('f.id', $file_id);
            $file_query = $this->db->get();
            
            if (!$file_query || $file_query->num_rows() == 0) {
                show_error('File not found.');
            }
            
            $file = $file_query->row_array();
            
            // Verify client has access to this file
            $has_access = false;
            $context_info = '';
            
            if ($file['rel_type'] == 'case') {
                // Check if case belongs to client
                $this->db->select('c.case_title, c.case_number');
                $this->db->from(db_prefix() . 'cases c');
                $this->db->where('c.id', $file['rel_id']);
                $this->db->where('c.client_id', $client_id);
                $case_check = $this->db->get();
                if ($case_check && $case_check->num_rows() > 0) {
                    $has_access = true;
                    $case_data = $case_check->row_array();
                    $context_info = 'Case: ' . $case_data['case_title'] . ' (' . $case_data['case_number'] . ')';
                }
                
            } elseif ($file['rel_type'] == 'hearing') {
                // Check if hearing's case belongs to client
                $this->db->select('c.case_title, c.case_number, h.date as hearing_date');
                $this->db->from(db_prefix() . 'hearings h');
                $this->db->join(db_prefix() . 'cases c', 'c.id = h.case_id');
                $this->db->where('h.id', $file['rel_id']);
                $this->db->where('c.client_id', $client_id);
                $hearing_check = $this->db->get();
                if ($hearing_check && $hearing_check->num_rows() > 0) {
                    $has_access = true;
                    $hearing_data = $hearing_check->row_array();
                    $context_info = 'Hearing: ' . date('M j, Y', strtotime($hearing_data['hearing_date'])) . ' - ' . $hearing_data['case_title'];
                }
                
            } elseif ($file['rel_type'] == 'client') {
                // Check if client document belongs to this client
                if ($file['rel_id'] == $client_id) {
                    $has_access = true;
                    $context_info = 'Client Document';
                }
                
            } elseif ($file['rel_type'] == 'consultation') {
                // Check if consultation belongs to client
                $this->db->select('cc.tag, cc.date_added');
                $this->db->from(db_prefix() . 'case_consultations cc');
                $this->db->where('cc.id', $file['rel_id']);
                $this->db->where('cc.client_id', $client_id);
                $consultation_check = $this->db->get();
                if ($consultation_check && $consultation_check->num_rows() > 0) {
                    $has_access = true;
                    $consultation_data = $consultation_check->row_array();
                    $context_info = 'Consultation: ' . ($consultation_data['tag'] ?: 'Consultation #' . $file['rel_id']);
                }
            }
            
            if (!$has_access) {
                show_error('You do not have permission to access this file.');
            }
            
            // Construct file path
            $file_path = FCPATH . 'uploads/documents/' . $file['file_name'];
            
            if (!file_exists($file_path)) {
                show_error('File not found on server.');
            }
            
            // Get file extension
            $file_extension = strtolower(pathinfo($file['file_name'], PATHINFO_EXTENSION));
            
            // Log view activity
            log_message('info', "Client {$client_id} viewed document: {$file['file_name']}");
            
            // Set appropriate content type and display
            switch ($file_extension) {
                case 'pdf':
                    header('Content-Type: application/pdf');
                    header('Content-Disposition: inline; filename="' . $file['file_name'] . '"');
                    break;
                    
                case 'jpg':
                case 'jpeg':
                    header('Content-Type: image/jpeg');
                    break;
                    
                case 'png':
                    header('Content-Type: image/png');
                    break;
                    
                case 'gif':
                    header('Content-Type: image/gif');
                    break;
                    
                case 'txt':
                    header('Content-Type: text/plain; charset=utf-8');
                    break;
                    
                case 'doc':
                case 'docx':
                case 'xls':
                case 'xlsx':
                case 'ppt':
                case 'pptx':
                    // For Office documents, show a preview page instead
                    $this->show_document_preview($file, $context_info, $file_path);
                    return;
                    
                default:
                    // For other file types, show a preview page
                    $this->show_document_preview($file, $context_info, $file_path);
                    return;
            }
            
            // For viewable files, output the content directly
            header('Content-Length: ' . filesize($file_path));
            readfile($file_path);
            
        } catch (Exception $e) {
            log_message('error', 'Error viewing file: ' . $e->getMessage());
            show_error('An error occurred while viewing the file. Please try again later.');
        }
    }
    
    /**
     * Show document preview page for non-viewable files
     */
    private function show_document_preview($file, $context_info, $file_path)
    {
        $file_extension = strtolower(pathinfo($file['file_name'], PATHINFO_EXTENSION));
        $file_size = filesize($file_path);
        $file_size_formatted = $this->format_file_size($file_size);
        
        // Pass data to preview view
        $this->data([
            'title' => 'Document Preview - ' . $file['file_name'],
            'file' => $file,
            'context_info' => $context_info,
            'file_extension' => $file_extension,
            'file_size' => $file_size_formatted,
            'client_id' => get_client_user_id()
        ]);
        
        $this->title('Document Preview');
        $this->view('document_preview');
        $this->layout();
    }
    
    /**
     * Format file size for display
     */
    private function format_file_size($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
    
    /**
     * View consultation details and documents
     */
    public function consultation($consultation_id)
    {
        if (!$consultation_id || !is_numeric($consultation_id)) {
            show_404();
        }
        
        $client_id = get_client_user_id();
        
        if (!$client_id) {
            show_error('Unable to identify client. Please log in again.');
        }
        
        try {
            // Get consultation details and verify client ownership
            $this->db->select('cc.*');
            $this->db->from(db_prefix() . 'case_consultations cc');
            $this->db->where('cc.id', $consultation_id);
            $this->db->where('cc.client_id', $client_id);
            $consultation_query = $this->db->get();
            
            if (!$consultation_query || $consultation_query->num_rows() == 0) {
                show_error('Consultation not found or you do not have permission to view it.');
            }
            
            $consultation = $consultation_query->row_array();
            
            // Get consultation documents
            $documents = [];
            $this->db->select('f.*, "Consultation Document" as document_context');
            $this->db->from(db_prefix() . 'files f');
            $this->db->where('f.rel_type', 'consultation');
            $this->db->where('f.rel_id', $consultation_id);
            $this->db->order_by('f.dateadded', 'DESC');
            $docs_query = $this->db->get();
            
            if ($docs_query) {
                $documents = $docs_query->result_array();
            }
            
            // Pass data to view
            $this->data([
                'title' => 'Consultation Details - ' . ($consultation['tag'] ?: 'Consultation'),
                'consultation' => $consultation,
                'documents' => $documents,
                'client_id' => $client_id
            ]);
            
            $this->title('Consultation Details');
            $this->view('consultation_details');
            $this->layout();
            
        } catch (Exception $e) {
            log_message('error', 'Error loading consultation details: ' . $e->getMessage());
            show_error('An error occurred while loading consultation details. Please try again later.');
        }
    }
    
    /**
     * AJAX endpoint to get consultation notes for modal display
     */
    public function get_consultation_notes()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        header('Content-Type: application/json');
        
        $consultation_id = $this->input->post('consultation_id');
        $client_id = get_client_user_id();
        
        if (!$consultation_id || !$client_id) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid parameters'
            ]);
            return;
        }
        
        try {
            // Get consultation and verify client ownership
            $this->db->select('cc.*');
            $this->db->from(db_prefix() . 'case_consultations cc');
            $this->db->where('cc.id', $consultation_id);
            $this->db->where('cc.client_id', $client_id);
            $consultation_query = $this->db->get();
            
            if (!$consultation_query || $consultation_query->num_rows() == 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Consultation not found or access denied'
                ]);
                return;
            }
            
            $consultation = $consultation_query->row_array();
            
            // Format the note content (preserve HTML but clean it)
            $formatted_note = $consultation['note'];
            if (!empty($formatted_note)) {
                // Allow basic HTML tags for formatting
                $allowed_tags = '<p><br><strong><b><em><i><u><ul><ol><li><h1><h2><h3><h4><h5><h6>';
                $formatted_note = strip_tags($formatted_note, $allowed_tags);
                
                // Clean up and ensure proper formatting
                $formatted_note = str_replace(['<p>', '</p>'], ['<p style="margin-bottom: 1em;">', '</p>'], $formatted_note);
            } else {
                $formatted_note = '<p style="color: #999;">No notes available for this consultation.</p>';
            }
            
            echo json_encode([
                'success' => true,
                'consultation' => [
                    'id' => $consultation['id'],
                    'tag' => $consultation['tag'] ?: 'Consultation #' . $consultation['id'],
                    'formatted_date' => date('M j, Y g:i A', strtotime($consultation['date_added'])),
                    'formatted_note' => $formatted_note,
                    'phase' => $consultation['phase']
                ]
            ]);
            
        } catch (Exception $e) {
            log_message('error', 'Error fetching consultation notes: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error loading consultation notes'
            ]);
        }
    }
    
    // === HELPER METHODS FOR 3-PAGE FLOW ===
    
    /**
     * Get recent activity for dashboard
     */
    private function get_recent_activity($client_id, $limit = 5)
    {
        $activity = [];
        
        try {
            // Get recent cases
            $this->db->select('id, case_title as title, date_created as date, "case" as type');
            $this->db->from(db_prefix() . 'cases');
            $this->db->where('client_id', $client_id);
            $this->db->order_by('date_created', 'DESC');
            $this->db->limit($limit);
            $recent_cases = $this->db->get()->result_array();
            
            foreach ($recent_cases as $case) {
                $activity[] = [
                    'title' => 'New case: ' . $case['title'],
                    'date' => $case['date'],
                    'type' => 'case',
                    'icon' => 'fa-gavel'
                ];
            }
            
            // Sort by date and limit
            usort($activity, function($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });
            
            return array_slice($activity, 0, $limit);
            
        } catch (Exception $e) {
            log_message('error', 'Error getting recent activity: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get detailed cases for cases page
     */
    private function get_detailed_cases($client_id)
    {
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
        
        return $this->db->get()->result_array();
    }
    
    /**
     * Get case documents
     */
    private function get_case_documents($case_id)
    {
        $this->db->select('f.*, "Case Document" as document_context');
        $this->db->from(db_prefix() . 'files f');
        $this->db->where('f.rel_type', 'case');
        $this->db->where('f.rel_id', $case_id);
        $this->db->order_by('f.dateadded', 'DESC');
        
        return $this->db->get()->result_array();
    }
    
    /**
     * Get case hearings
     */
    private function get_case_hearings($case_id)
    {
        $this->db->select('h.*, cr.court_no, cr.judge_name, ct.name as court_name');
        $this->db->from(db_prefix() . 'hearings h');
        $this->db->join(db_prefix() . 'cases c', 'c.id = h.case_id', 'left');
        $this->db->join(db_prefix() . 'court_rooms cr', 'cr.id = c.court_room_id', 'left');
        $this->db->join(db_prefix() . 'courts ct', 'ct.id = cr.court_id', 'left');
        $this->db->where('h.case_id', $case_id);
        $this->db->order_by('h.date', 'DESC');
        
        return $this->db->get()->result_array();
    }
    
    /**
     * Get detailed consultations
     */
    private function get_detailed_consultations($client_id)
    {
        $this->db->select('cc.*, cc.note, cc.tag, cc.date_added, cc.phase');
        $this->db->from(db_prefix() . 'case_consultations cc');
        $this->db->where('cc.client_id', $client_id);
        $this->db->order_by('cc.date_added', 'DESC');
        
        $consultations = $this->db->get()->result_array();
        
        // Get document count for each consultation
        foreach ($consultations as &$consultation) {
            $this->db->where('rel_type', 'consultation');
            $this->db->where('rel_id', $consultation['id']);
            $consultation['document_count'] = $this->db->count_all_results(db_prefix() . 'files');
        }
        
        return $consultations;
    }
    
    /**
     * Get consultation documents detailed
     */
    private function get_consultation_documents_detailed($consultation_id)
    {
        $this->db->select('f.*, "Consultation Document" as document_context');
        $this->db->from(db_prefix() . 'files f');
        $this->db->where('f.rel_type', 'consultation');
        $this->db->where('f.rel_id', $consultation_id);
        $this->db->order_by('f.dateadded', 'DESC');
        
        return $this->db->get()->result_array();
    }
}