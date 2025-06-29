<?php

defined('BASEPATH') or exit('No direct script access allowed');

define('PHASE_CONSULTATION', 'consultation');
define('PHASE_LITIGATION', 'litigation');

class Cases extends AdminController
{
    public function __construct()
{
    parent::__construct();
    $this->load->model('Cases_model');
    $this->load->model('Appointments_model');
    $this->load->helper('cases/security');
    $this->load->helper('cases/cases_css');
    $this->load->helper('cases/access_control');
    
    // Set JSON header for AJAX requests
    if ($this->input->is_ajax_request()) {
        header('Content-Type: application/json');
    }
}

    public function index()
    {
        if (!has_permission('cases', '', 'view')) {
            access_denied('cases');
        }

        // Get only accessible cases for current user
        $accessible_case_ids = cases_get_accessible_resources('case', 'view');
        
        $data['title'] = _l('cases_management');
        $data['clients'] = $this->get_all_clients();
        $data['contacts'] = $this->get_all_contacts();
        $data['accessible_case_ids'] = $accessible_case_ids;

        $this->load->view('cases/manage', $data);
    }

    public function consultations_list()
{
    // Set JSON header first
    header('Content-Type: application/json');
    
    try {
        if (!has_permission('cases', '', 'view')) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Access denied',
                'data' => []
            ]);
            exit;
        }

        // Load model if not already loaded
        if (!isset($this->Cases_model)) {
            $this->load->model('Cases_model');
        }
        
        $result = $this->Cases_model->get_consultations_with_names();
        
        // Ensure we have valid data
        if (!is_array($result)) {
            $result = [];
        }
        
        // Log for debugging
        log_message('debug', 'Consultations loaded: ' . count($result));
        
        echo json_encode([
            'success' => true,
            'data' => $result,
            'count' => count($result),
            'message' => count($result) . ' consultations loaded'
        ]);
        
    } catch (Exception $e) {
        log_message('error', 'Error in consultations_list: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Server error occurred',
            'data' => [],
            'error' => ENVIRONMENT === 'development' ? $e->getMessage() : 'Internal server error'
        ]);
    }
    exit;
}

    public function cases_list()
{
    header('Content-Type: application/json');
    
    try {
        if (!has_permission('cases', '', 'view')) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Access denied',
                'data' => []
            ]);
            exit;
        }

        // Load model if not already loaded
        if (!isset($this->Cases_model)) {
            $this->load->model('Cases_model');
        }
        
        $result = $this->Cases_model->get_all_cases_with_details();
        
        // Ensure we have valid data
        if (!is_array($result)) {
            $result = [];
        }
        
        // Log for debugging
        log_message('debug', 'Cases loaded: ' . count($result));
        
        echo json_encode([
            'success' => true,
            'data' => $result,
            'count' => count($result),
            'message' => count($result) . ' cases loaded'
        ]);
        
    } catch (Exception $e) {
        log_message('error', 'Error in cases_list: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Server error occurred',
            'data' => [],
            'error' => ENVIRONMENT === 'development' ? $e->getMessage() : 'Internal server error'
        ]);
    }
    exit;
}

public function check_database()
{
    if (!is_admin()) {
        access_denied();
    }
    
    $tables_to_check = [
        db_prefix() . 'case_consultations',
        db_prefix() . 'cases',
        db_prefix() . 'hearings',
        db_prefix() . 'courts',
        db_prefix() . 'court_rooms'
    ];
    
    $results = [];
    
    foreach ($tables_to_check as $table) {
        $exists = $this->db->table_exists(str_replace(db_prefix(), '', $table));
        $results[$table] = $exists;
        
        if ($exists) {
            $count = $this->db->count_all($table);
            $results[$table . '_count'] = $count;
        }
    }
    
    echo json_encode([
        'success' => true,
        'tables' => $results,
        'db_prefix' => db_prefix()
    ]);
    exit;
}

private function log_ajax_error($method, $error, $additional_data = [])
{
    $log_data = [
        'method' => $method,
        'error' => $error->getMessage(),
        'file' => $error->getFile(),
        'line' => $error->getLine(),
        'user_id' => get_staff_user_id(),
        'ip' => $this->input->ip_address(),
        'user_agent' => $this->input->user_agent(),
        'timestamp' => date('Y-m-d H:i:s'),
        'additional_data' => $additional_data
    ];
    
    log_message('error', 'AJAX Error in Cases controller: ' . json_encode($log_data));
}

public function get_menu_stats()
{
    try {
        if (!has_permission('cases', '', 'view')) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Access denied',
                'consultations' => 0,
                'today_hearings' => 0
            ]);
            return;
        }

        // Load rate limiter and enforce limits for AJAX endpoint
        $this->load->helper('modules/cases/helpers/rate_limiter_helper');
        cases_enforce_rate_limit('get_menu_stats', 60, 300); // 60 requests per 5 minutes

        $today = date('Y-m-d');
        
        // Count pending consultations with error handling
        $this->db->where('phase', 'consultation');
        $consultations = $this->db->count_all_results(db_prefix() . 'case_consultations');
        
        // Count today's hearings with error handling
        $this->db->where('DATE(date)', $today);
        $today_hearings = $this->db->count_all_results(db_prefix() . 'hearings');
        
        echo json_encode([
            'success' => true,
            'consultations' => (int)$consultations,
            'today_hearings' => (int)$today_hearings
        ]);
        
    } catch (Exception $e) {
        log_message('error', 'Error in get_menu_stats: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Server error occurred',
            'consultations' => 0,
            'today_hearings' => 0,
            'error' => ENVIRONMENT === 'development' ? $e->getMessage() : 'Internal server error'
        ]);
    }
    exit; // Important: prevent any additional output
}



    public function create_consultation()
{
    try {
        $consultation_id = $this->input->post('consultation_id', true);
        
        // Check permissions - create for new, edit for existing
        $required_permission = $consultation_id ? 'edit' : 'create';
        if (!has_permission('cases', '', $required_permission)) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Access denied'
            ]);
            return;
        }
        
        // If updating existing consultation, validate ownership
        if ($consultation_id) {
            cases_enforce_resource_access('consultation', $consultation_id, 'edit', ['ajax' => true]);
        }

        $raw_note = $this->input->post('note', false);
        $sanitized_note = cases_sanitize_string($raw_note, 5000, false); // Sanitize HTML to prevent XSS
        
        $data = [
            'client_id' => $this->input->post('client_id', true),
            'contact_id' => $this->input->post('contact_id', true) ?: NULL,
            'tag' => $this->input->post('tag', true),
            'note' => $sanitized_note,
            'staff_id' => get_staff_user_id(),
        ];

        // Validate required fields
        if (empty($data['client_id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Client is required'
            ]);
            return;
        }

        if (empty($data['note'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Consultation note is required'
            ]);
            return;
        }

        if ($consultation_id) {
            // Update existing consultation
            $this->db->where('id', $consultation_id);
            $updated = $this->db->update(db_prefix() . 'case_consultations', $data);
            
            echo json_encode([
                'success' => (bool) $updated,
                'message' => $updated ? 'Consultation updated successfully' : 'Failed to update consultation'
            ]);
        } else {
            // New consultation
            $data['date_added'] = date('Y-m-d H:i:s');
            $data['phase'] = PHASE_CONSULTATION;
            
            $this->db->insert(db_prefix() . 'case_consultations', $data);
            $insert_id = $this->db->insert_id();
            
            echo json_encode([
                'success' => $this->db->affected_rows() > 0,
                'message' => $insert_id ? 'Consultation created successfully' : 'Failed to create consultation',
                'id' => $insert_id
            ]);
        }
        
    } catch (Exception $e) {
        log_message('error', 'Error in create_consultation: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Server error occurred',
            'error' => ENVIRONMENT === 'development' ? $e->getMessage() : 'Internal server error'
        ]);
    }
    exit; // Important: prevent any additional output
}

    public function create_case()
    {
        if (!has_permission('cases', '', 'create')) {
            access_denied('cases');
        }

        $data = $this->input->post(NULL, true);
        $id = $this->Cases_model->create_case($data);

        if (!empty($data['consultation_id'])) {
            $this->Cases_model->update_phase($data['consultation_id'], PHASE_LITIGATION);
        }

        echo json_encode(['success' => (bool)$id, 'id' => $id]);
    }

    public function update_consultation_phase($id)
    {
        if (!has_permission('cases', '', 'edit')) {
            access_denied('cases');
        }

        $phase = $this->input->post('phase', true);
        $success = $this->Cases_model->update_phase($id, $phase);
        echo json_encode(['success' => $success]);
    }

    public function delete_consultation($id)
    {
        if (!has_permission('cases', '', 'delete')) {
            access_denied('cases');
        }

        $deleted = $this->db->delete(db_prefix() . 'case_consultations', ['id' => $id]);
        echo json_encode(['success' => $deleted]);
    }

    public function get_consultation_note($id)
    {
        if (!has_permission('cases', '', 'view')) {
            access_denied('cases');
        }

        $note = $this->Cases_model->get_note_by_id($id);
        if ($note) {
            echo json_encode(['success' => true, 'note' => $note['note']]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    public function get_consultation($id)
    {
        if (!has_permission('cases', '', 'view')) {
            access_denied('cases');
        }

        $data = $this->Cases_model->get_consultation_by_id($id);
        echo json_encode(['success' => (bool)$data, 'data' => $data]);
    }

    public function update_consultation()
    {
        if (!has_permission('cases', '', 'edit')) {
            access_denied('cases');
        }

        $id = $this->input->post('id', true);
        $data = [
            'client_id' => $this->input->post('client_id', true),
            'contact_id' => $this->input->post('contact_id', true) ?: NULL,
            'tag' => $this->input->post('tag', true),
            'note' => $this->input->post('note', true)
        ];

        $this->db->where('id', $id);
        $updated = $this->db->update(db_prefix().'case_consultations', $data);
        echo json_encode(['success' => $updated]);
    }

    private function get_all_clients()
    {
        return $this->db->select('userid, company')
                        ->from(db_prefix() . 'clients')
                        ->get()
                        ->result_array();
    }

    private function get_all_contacts()
    {
        return $this->db->select('id, firstname, lastname, userid')
                        ->from(db_prefix() . 'contacts')
                        ->get()
                        ->result_array();
    }

    public function get_contacts_by_client($client_id)
    {
        // Enhanced permission check
        if (!has_permission('cases', '', 'view') && !is_admin()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Access denied',
                'data' => []
            ]);
            return;
        }

        // Validate client_id
        if (!$client_id || !is_numeric($client_id)) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Invalid client ID',
                'data' => []
            ]);
            return;
        }

        try {
            // Load clients model
            $this->load->model('clients_model');
            
            // Get contacts using the official Perfex CRM method
            $contacts = $this->clients_model->get_contacts($client_id);
            
            // Alternative method if the above doesn't work
            if (empty($contacts)) {
                $this->db->select('id, firstname, lastname, email, phonenumber, userid');
                $this->db->from(db_prefix() . 'contacts');
                $this->db->where('userid', $client_id);
                $this->db->where('active', 1); // Only active contacts
                $this->db->order_by('firstname ASC, lastname ASC');
                
                $query = $this->db->get();
                $contacts = $query ? $query->result_array() : [];
            }
            
            // Format response data
            $formatted_contacts = [];
            if (!empty($contacts)) {
                foreach ($contacts as $contact) {
                    // Handle different data formats
                    if (is_object($contact)) {
                        $contact = (array) $contact;
                    }
                    
                    $formatted_contacts[] = [
                        'id' => $contact['id'],
                        'firstname' => $contact['firstname'] ?? '',
                        'lastname' => $contact['lastname'] ?? '',
                        'email' => $contact['email'] ?? '',
                        'phonenumber' => $contact['phonenumber'] ?? '',
                        'userid' => $contact['userid'] ?? $client_id,
                        'full_name' => trim(($contact['firstname'] ?? '') . ' ' . ($contact['lastname'] ?? ''))
                    ];
                }
            }
            
            // Log for debugging (remove in production)
            log_message('debug', 'Contacts found for client ' . $client_id . ': ' . count($formatted_contacts));
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => count($formatted_contacts) . ' contacts found',
                'data' => $formatted_contacts,
                'count' => count($formatted_contacts)
            ]);
            
        } catch (Exception $e) {
            // Log error
            log_message('error', 'Error in get_contacts_by_client: ' . $e->getMessage());
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error loading contacts: ' . $e->getMessage(),
                'data' => []
            ]);
        }
    }

    public function get_invoices_by_client($client_id)
    {
        // Permission check
        if (!has_permission('cases', '', 'view') && !is_admin()) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Access denied',
                'data' => []
            ]);
            return;
        }

        // Validate client_id
        if (!$client_id || !is_numeric($client_id)) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid client ID provided',
                'data' => []
            ]);
            return;
        }

        try {
            // Log the request for debugging
            log_message('debug', 'Loading invoices for client: ' . $client_id);

            // Method 1: Try using Perfex CRM's built-in invoice functions
            $invoices = [];
            
            // Load invoices model if available
            if (file_exists(APPPATH . 'models/Invoices_model.php')) {
                $this->load->model('invoices_model');
                
                // Get invoices using Perfex CRM method
                $this->db->where('clientid', $client_id);
                $this->db->order_by('date', 'DESC');
                $query = $this->db->get(db_prefix() . 'invoices');
                
                if ($query && $query->num_rows() > 0) {
                    $invoices = $query->result_array();
                }
            }

            // Method 2: Direct database query if Method 1 fails
            if (empty($invoices)) {
                $this->db->select('
                    inv.id,
                    inv.number,
                    inv.prefix,
                    inv.total,
                    inv.status,
                    inv.date,
                    inv.duedate,
                    inv.currency,
                    inv.clientid,
                    curr.symbol as currency_symbol,
                    curr.name as currency_name
                ');
                $this->db->from(db_prefix() . 'invoices inv');
                $this->db->join(db_prefix() . 'currencies curr', 'curr.id = inv.currency', 'left');
                $this->db->where('inv.clientid', $client_id);
                $this->db->order_by('inv.date DESC, inv.id DESC');
                
                $query = $this->db->get();
                
                if ($query && $query->num_rows() > 0) {
                    $invoices = $query->result_array();
                }
            }

            // Format invoices for frontend
            $formatted_invoices = [];
            
            if (!empty($invoices)) {
                foreach ($invoices as $invoice) {
                    // Create formatted invoice number
                    $formatted_number = '';
                    
                    // Try using Perfex CRM's format_invoice_number function
                    if (function_exists('format_invoice_number')) {
                        $formatted_number = format_invoice_number($invoice['id']);
                    } else {
                        // Manual formatting
                        $prefix = !empty($invoice['prefix']) ? $invoice['prefix'] : 'INV-';
                        $number = str_pad($invoice['number'], 6, '0', STR_PAD_LEFT);
                        $formatted_number = $prefix . $number;
                    }

                    // Get status text
                    $status_text = $this->get_invoice_status_text($invoice['status']);
                    
                    // Get currency symbol
                    $currency_symbol = '₹'; // Default
                    if (!empty($invoice['currency_symbol'])) {
                        $currency_symbol = $invoice['currency_symbol'];
                    } else {
                        // Try to get default currency
                        $default_currency = get_option('default_currency');
                        if ($default_currency) {
                            $this->db->where('id', $default_currency);
                            $curr = $this->db->get(db_prefix() . 'currencies')->row();
                            if ($curr && !empty($curr->symbol)) {
                                $currency_symbol = $curr->symbol;
                            }
                        }
                    }

                    $formatted_invoices[] = [
                        'id' => $invoice['id'],
                        'number' => $invoice['number'],
                        'formatted_number' => $formatted_number,
                        'total' => number_format((float)$invoice['total'], 2, '.', ''),
                        'status' => $invoice['status'],
                        'status_text' => $status_text,
                        'date' => $invoice['date'],
                        'duedate' => $invoice['duedate'] ?? null,
                        'currency' => $invoice['currency'] ?? null,
                        'currency_symbol' => $currency_symbol,
                        'currency_name' => $invoice['currency_name'] ?? 'INR',
                        'clientid' => $invoice['clientid']
                    ];
                }
            }

            // Log results
            log_message('debug', 'Found ' . count($formatted_invoices) . ' invoices for client ' . $client_id);

            // Return response
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => count($formatted_invoices) . ' invoices found',
                'data' => $formatted_invoices,
                'count' => count($formatted_invoices),
                'client_id' => $client_id
            ]);

        } catch (Exception $e) {
            // Log error
            log_message('error', 'Error in get_invoices_by_client: ' . $e->getMessage());
            
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage(),
                'data' => [],
                'error_details' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]
            ]);
        }
    }

    /**
     * Helper method to get invoice status text
     */
    private function get_invoice_status_text($status)
    {
        $status_map = [
            1 => 'Unpaid',
            2 => 'Paid',
            3 => 'Partially Paid',
            4 => 'Overdue',
            5 => 'Cancelled',
            6 => 'Draft'
        ];
        
        return isset($status_map[$status]) ? $status_map[$status] : 'Unknown Status';
    }

    public function upgrade_to_litigation()
    {
        if (!has_permission('cases', '', 'create')) {
            access_denied('cases');
        }

        $consultation_id = $this->input->post('litigation_consultation_id', true);

        // Fetch client/contact from the consultation record
        $this->db->select('client_id, contact_id');
        $this->db->where('id', $consultation_id);
        $row = $this->db->get(db_prefix() . 'case_consultations')->row();

        if (!$row) {
            echo json_encode(['success' => false, 'message' => 'Invalid consultation']);
            return;
        }

        $data = [
            'consultation_id' => $consultation_id,
            'client_id' => $row->client_id,
            'contact_id' => $row->contact_id,
            'case_title' => $this->input->post('case_title', true),
            'case_number' => $this->input->post('case_number', true),
            'court_room_id' => $this->input->post('court_room_id', true),
            'date_filed' => $this->input->post('date_filed', true),
            'date_created' => date('Y-m-d H:i:s'),
        ];

        $this->db->insert(db_prefix() . 'cases', $data);

        if ($this->db->affected_rows() > 0) {
            // Update the consultation phase
            $this->db->where('id', $consultation_id);
            $this->db->update(db_prefix() . 'case_consultations', ['phase' => PHASE_LITIGATION]);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Insert failed']);
        }
    }

    /**
     * Display detailed case information
     */
    public function details()
    {
        if (!has_permission('cases', '', 'view')) {
            access_denied('cases');
        }

        $case_id = $this->input->get('id');
        
        if (!$case_id) {
            set_alert('danger', 'No case ID provided');
            redirect(admin_url('cases'));
        }

        try {
            // Get case details with related information
            $this->db->select('c.*, 
                             cl.company as client_name,
                             CONCAT(COALESCE(co.firstname, ""), " ", COALESCE(co.lastname, "")) as contact_name,
                             cr.court_no, cr.judge_name,
                             ct.name as court_name,
                             cons.note as consultation_note,
                             cons.tag as consultation_tag');
            $this->db->from(db_prefix().'cases c');
            $this->db->join(db_prefix().'clients cl', 'cl.userid = c.client_id', 'left');
            $this->db->join(db_prefix().'contacts co', 'co.id = c.contact_id', 'left');
            $this->db->join(db_prefix().'court_rooms cr', 'cr.id = c.court_room_id', 'left');
            $this->db->join(db_prefix().'courts ct', 'ct.id = cr.court_id', 'left');
            $this->db->join(db_prefix().'case_consultations cons', 'cons.id = c.consultation_id', 'left');
            $this->db->where('c.id', $case_id);
            
            $query = $this->db->get();
            
            if (!$query) {
                // Database error
                $error = $this->db->error();
                log_message('error', 'Database error in cases/details: ' . print_r($error, true));
                show_error('Database error occurred. Please contact administrator.');
            }
            
            $data['case'] = $query->row_array();
            
            if (!$data['case']) {
                set_alert('danger', 'Case not found');
                redirect(admin_url('cases'));
            }
            
            // Format court display
            $court_display = '';
            if (!empty($data['case']['court_name'])) {
                $court_display = $data['case']['court_name'];
                if (!empty($data['case']['court_no'])) {
                    $court_display .= ' - Court ' . $data['case']['court_no'];
                }
                if (!empty($data['case']['judge_name'])) {
                    $court_display .= ' (Hon\'ble ' . $data['case']['judge_name'] . ')';
                }
            }
            $data['case']['court_display'] = $court_display ?: 'Not specified';
            
            // Get hearings for this case
            $this->db->select('h.*, cr.court_no, cr.judge_name, ct.name as court_name');
            $this->db->from(db_prefix().'hearings h');
            $this->db->join(db_prefix().'cases c', 'c.id = h.case_id', 'left');
            $this->db->join(db_prefix().'court_rooms cr', 'cr.id = c.court_room_id', 'left');
            $this->db->join(db_prefix().'courts ct', 'ct.id = cr.court_id', 'left');
            $this->db->where('h.case_id', $case_id);
            $this->db->order_by('h.date', 'DESC');

            $hearings_query = $this->db->get();

            if (!$hearings_query) {
                // Database error
                $error = $this->db->error();
                log_message('error', 'Database error in hearings query: ' . print_r($error, true));
                $data['hearings'] = array();
            } else {
                $data['hearings'] = $hearings_query->result_array();
            }

            // Separate upcoming and past hearings
            $today = date('Y-m-d');
            $data['upcoming_hearings'] = array();
            $data['past_hearings'] = array();

            if (!empty($data['hearings'])) {
                foreach ($data['hearings'] as $hearing) {
                    if ($hearing['date'] >= $today) {
                        $data['upcoming_hearings'][] = $hearing;
                    } else {
                        $data['past_hearings'][] = $hearing;
                    }
                }
            }
            
            // DOCUMENT INTEGRATION: Direct database queries for documents
            // Get case-level documents
            $this->db->where('rel_type', 'case');
            $this->db->where('rel_id', $case_id);
            $data['case_documents'] = $this->db->get(db_prefix() . 'files')->result_array();

            // Get hearing-level documents for this case
            $data['hearing_documents'] = array();
            $data['hearing_documents_by_hearing'] = array();

            if (!empty($data['hearings'])) {
                $hearing_ids = array_column($data['hearings'], 'id');
                
                if (!empty($hearing_ids)) {
                    // Get all documents related to these hearings
                    $this->db->where('rel_type', 'hearing');
                    $this->db->where_in('rel_id', $hearing_ids);
                    $this->db->order_by('dateadded', 'DESC');
                    $hearing_documents_raw = $this->db->get(db_prefix() . 'files')->result_array();
                    
                    $data['hearing_documents'] = $hearing_documents_raw;
                    
                    // Organize hearing documents by hearing ID for easier display
                    foreach ($hearing_documents_raw as $doc) {
                        $hearing_id = $doc['rel_id'];
                        if (!isset($data['hearing_documents_by_hearing'][$hearing_id])) {
                            $data['hearing_documents_by_hearing'][$hearing_id] = array();
                        }
                        $data['hearing_documents_by_hearing'][$hearing_id][] = $doc;
                    }
                }
            }

            // Set title
            $data['title'] = 'Case Details - ' . ($data['case']['case_title'] ?? 'Unknown Case');

            // Load view
            $this->load->view('cases/cases/details', $data);

            } catch (Exception $e) {
                log_message('error', 'Error in cases/details: ' . $e->getMessage());
                show_error('An error occurred while loading case details. Please try again later.');
            }
    }

    /**
     * Caseboard for cases and consultations
     */
    public function caseboard()
    {
        if (!has_permission('cases', '', 'view')) {
            access_denied('cases');
        }

        $data = [];
        
        // Get upcoming hearings (next 7 days)
        $today = date('Y-m-d');
        $next_week = date('Y-m-d', strtotime('+7 days'));
        
        $this->db->select('h.*, c.case_title, c.client_id, ct.name as court_name');
        $this->db->from(db_prefix().'hearings h');
        $this->db->join(db_prefix().'cases c', 'c.id = h.case_id', 'left');
        $this->db->join(db_prefix().'court_rooms cr', 'cr.id = c.court_room_id', 'left');
        $this->db->join(db_prefix().'courts ct', 'ct.id = cr.court_id', 'left');
        $this->db->where('h.date >=', $today);
        $this->db->where('h.date <=', $next_week);
        $this->db->order_by('h.date', 'ASC');
        $this->db->order_by('h.time', 'ASC');
        $this->db->limit(10);
        
        $data['upcoming_hearings'] = $this->db->get()->result_array();
        
        // Get active cases
        $this->db->select('c.*, cl.company as client_name');
        $this->db->from(db_prefix().'cases c');
        $this->db->join(db_prefix().'clients cl', 'cl.userid = c.client_id', 'left');
        $this->db->order_by('c.date_created', 'DESC');
        $this->db->limit(10);
        
        $cases = $this->db->get()->result_array();
        
        // Get hearing counts for each case
        // Get hearing counts for all cases in one query (fixes N+1 problem)
        if (!empty($cases)) {
            $case_ids = array_column($cases, 'id');
            
            // Get hearing counts
            $this->db->select('case_id, COUNT(*) as count');
            $this->db->from(db_prefix().'hearings');
            $this->db->where_in('case_id', $case_ids);
            $this->db->group_by('case_id');
            $hearing_counts = $this->db->get()->result_array();
            $hearing_counts_map = array_column($hearing_counts, 'count', 'case_id');
            
            // Get document counts
            $this->db->select('rel_id, COUNT(*) as count');
            $this->db->from(db_prefix().'files');
            $this->db->where('rel_type', 'case');
            $this->db->where_in('rel_id', $case_ids);
            $this->db->group_by('rel_id');
            $document_counts = $this->db->get()->result_array();
            $document_counts_map = array_column($document_counts, 'count', 'rel_id');
            
            // Assign counts to cases
            foreach ($cases as &$case) {
                $case['hearing_count'] = isset($hearing_counts_map[$case['id']]) ? $hearing_counts_map[$case['id']] : 0;
                $case['document_count'] = isset($document_counts_map[$case['id']]) ? $document_counts_map[$case['id']] : 0;
            }
        }
        
        $data['cases'] = $cases;
        
        // Get recent consultations
        $this->db->select('cc.*, cl.company as client_name, CONCAT(co.firstname, " ", co.lastname) as contact_name');
        $this->db->from(db_prefix().'case_consultations cc');
        $this->db->join(db_prefix().'clients cl', 'cl.userid = cc.client_id', 'left');
        $this->db->join(db_prefix().'contacts co', 'co.id = cc.contact_id', 'left');
        $this->db->order_by('cc.date_added', 'DESC');
        $this->db->limit(10);
        
        $data['consultations'] = $this->db->get()->result_array();
        
        $data['title'] = 'Caseboard';
        $this->load->view('cases/caseboard', $data);
    }

    /**
     * AJAX endpoint for menu statistics
     */

    /**
     * Case search functionality
     */
    public function search_cases()
    {
        if (!has_permission('cases', '', 'view')) {
            access_denied('cases');
        }

        $search_term = $this->input->get('q', true);
        $limit = $this->input->get('limit', true) ?: 10;

        $this->db->select('c.id, c.case_title, c.case_number, cl.company as client_name');
        $this->db->from(db_prefix() . 'cases c');
        $this->db->join(db_prefix() . 'clients cl', 'cl.userid = c.client_id', 'left');
        
        if ($search_term) {
            $this->db->group_start();
            $this->db->like('c.case_title', $search_term);
            $this->db->or_like('c.case_number', $search_term);
            $this->db->or_like('cl.company', $search_term);
            $this->db->group_end();
        }
        
        $this->db->order_by('c.date_created', 'DESC');
        $this->db->limit($limit);
        
        $cases = $this->db->get()->result_array();
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $cases
        ]);
    }

    /**
     * Export cases data
     */
    public function export_cases()
    {
        if (!has_permission('cases', '', 'view')) {
            access_denied('cases');
        }

        $format = $this->input->get('format', true) ?: 'csv';
        
        // Get all cases with details
        $cases = $this->Cases_model->get_all_cases_with_details();
        
        if ($format === 'csv') {
            $this->export_cases_csv($cases);
        } else {
            $this->export_cases_json($cases);
        }
    }

    private function export_cases_csv($cases)
    {
        $filename = 'cases_export_' . date('Y_m_d_H_i_s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($output, [
            'Case ID',
            'Case Title',
            'Case Number',
            'Client Name',
            'Contact Name',
            'Court Name',
            'Court Room',
            'Judge Name',
            'Date Filed',
            'Date Created',
            'Consultation Reference'
        ]);
        
        // CSV data
        foreach ($cases as $case) {
            fputcsv($output, [
                $case['id'],
                $case['case_title'],
                $case['case_number'],
                $case['client_name'],
                $case['contact_name'],
                $case['court_name'],
                $case['court_no'],
                $case['judge_name'],
                $case['date_filed'],
                $case['date_created'],
                $case['consultation_reference']
            ]);
        }
        
        fclose($output);
    }

    private function export_cases_json($cases)
    {
        $filename = 'cases_export_' . date('Y_m_d_H_i_s') . '.json';
        
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        echo json_encode([
            'export_date' => date('Y-m-d H:i:s'),
            'total_cases' => count($cases),
            'cases' => $cases
        ], JSON_PRETTY_PRINT);
    }

    /**
     * Generate case reports
     */
    public function generate_report()
    {
        if (!has_permission('cases', '', 'view')) {
            access_denied('cases');
        }

        $type = $this->input->get('type', true) ?: 'summary';
        $from_date = $this->input->get('from', true);
        $to_date = $this->input->get('to', true);
        
        switch ($type) {
            case 'detailed':
                $this->generate_detailed_report($from_date, $to_date);
                break;
            case 'hearings':
                $this->generate_hearings_report($from_date, $to_date);
                break;
            case 'consultations':
                $this->generate_consultations_report($from_date, $to_date);
                break;
            default:
                $this->generate_summary_report($from_date, $to_date);
        }
    }

    private function generate_summary_report($from_date, $to_date)
    {
        $data = [];
        
        // Cases statistics
        $this->db->select('COUNT(*) as total_cases');
        if ($from_date) $this->db->where('date_created >=', $from_date);
        if ($to_date) $this->db->where('date_created <=', $to_date);
        $data['total_cases'] = $this->db->get(db_prefix() . 'cases')->row()->total_cases;
        
        // Consultations statistics
        $this->db->select('COUNT(*) as total_consultations');
        if ($from_date) $this->db->where('date_added >=', $from_date);
        if ($to_date) $this->db->where('date_added <=', $to_date);
        $data['total_consultations'] = $this->db->get(db_prefix() . 'case_consultations')->row()->total_consultations;
        
        // Hearings statistics
        $this->db->select('COUNT(*) as total_hearings');
        if ($from_date) $this->db->where('date >=', $from_date);
        if ($to_date) $this->db->where('date <=', $to_date);
        $data['total_hearings'] = $this->db->get(db_prefix() . 'hearings')->row()->total_hearings;
        
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['title'] = 'Cases Summary Report';
        
        $this->load->view('cases/reports/summary', $data);
    }

    private function generate_detailed_report($from_date, $to_date)
    {
        // Get detailed case information
        $this->db->select('c.*, cl.company as client_name, 
                          CONCAT(co.firstname, " ", co.lastname) as contact_name,
                          cr.court_no, cr.judge_name, ct.name as court_name');
        $this->db->from(db_prefix() . 'cases c');
        $this->db->join(db_prefix() . 'clients cl', 'cl.userid = c.client_id', 'left');
        $this->db->join(db_prefix() . 'contacts co', 'co.id = c.contact_id', 'left');
        $this->db->join(db_prefix() . 'court_rooms cr', 'cr.id = c.court_room_id', 'left');
        $this->db->join(db_prefix() . 'courts ct', 'ct.id = cr.court_id', 'left');
        
        if ($from_date) $this->db->where('c.date_created >=', $from_date);
        if ($to_date) $this->db->where('c.date_created <=', $to_date);
        
        $this->db->order_by('c.date_created', 'DESC');
        
        $data['cases'] = $this->db->get()->result_array();
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['title'] = 'Detailed Cases Report';
        
        $this->load->view('cases/reports/detailed', $data);
    }

    private function generate_hearings_report($from_date, $to_date)
    {
        // Get hearings with case information
        $this->db->select('h.*, c.case_title, c.case_number, cl.company as client_name');
        $this->db->from(db_prefix() . 'hearings h');
        $this->db->join(db_prefix() . 'cases c', 'c.id = h.case_id', 'left');
        $this->db->join(db_prefix() . 'clients cl', 'cl.userid = c.client_id', 'left');
        
        if ($from_date) $this->db->where('h.date >=', $from_date);
        if ($to_date) $this->db->where('h.date <=', $to_date);
        
        $this->db->order_by('h.date', 'DESC');
        
        $data['hearings'] = $this->db->get()->result_array();
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['title'] = 'Hearings Report';
        
        $this->load->view('cases/reports/hearings', $data);
    }

    private function generate_consultations_report($from_date, $to_date)
    {
        // Get consultations with client information
        $consultations = $this->Cases_model->get_consultations_with_names();
        
        // Filter by date if provided
        if ($from_date || $to_date) {
            $consultations = array_filter($consultations, function($consultation) use ($from_date, $to_date) {
                $consultation_date = $consultation['date_added'];
                
                if ($from_date && $consultation_date < $from_date) return false;
                if ($to_date && $consultation_date > $to_date) return false;
                
                return true;
            });
        }
        
        $data['consultations'] = $consultations;
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['title'] = 'Consultations Report';
        
        $this->load->view('cases/reports/consultations', $data);
    }

    /**
     * Case statistics for dashboard widgets
     */
    public function get_statistics()
    {
        if (!has_permission('cases', '', 'view')) {
            access_denied('cases');
        }

        $stats = [];
        
        // Total cases
        $stats['total_cases'] = $this->db->count_all_results(db_prefix() . 'cases');
        
        // Total consultations
        $stats['total_consultations'] = $this->db->count_all_results(db_prefix() . 'case_consultations');
        
        // Consultations by phase
        $this->db->where('phase', 'consultation');
        $stats['consultation_phase'] = $this->db->count_all_results(db_prefix() . 'case_consultations');
        
        $this->db->where('phase', 'litigation');
        $stats['litigation_phase'] = $this->db->count_all_results(db_prefix() . 'case_consultations');
        
        // Upcoming hearings (next 30 days)
        $today = date('Y-m-d');
        $next_month = date('Y-m-d', strtotime('+30 days'));
        
        $this->db->where('date >=', $today);
        $this->db->where('date <=', $next_month);
        $this->db->where('status !=', 'Completed');
        $this->db->where('status !=', 'Cancelled');
        $stats['upcoming_hearings'] = $this->db->count_all_results(db_prefix() . 'hearings');
        
        // Today's hearings
        $this->db->where('DATE(date)', $today);
        $this->db->or_where('DATE(next_date)', $today);
        $stats['today_hearings'] = $this->db->count_all_results(db_prefix() . 'hearings');
        
        // Cases by court
        $this->db->select('ct.name as court_name, COUNT(c.id) as case_count');
        $this->db->from(db_prefix() . 'cases c');
        $this->db->join(db_prefix() . 'court_rooms cr', 'cr.id = c.court_room_id', 'left');
        $this->db->join(db_prefix() . 'courts ct', 'ct.id = cr.court_id', 'left');
        $this->db->group_by('ct.id');
        $this->db->order_by('case_count', 'DESC');
        $stats['cases_by_court'] = $this->db->get()->result_array();
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'statistics' => $stats
        ]);
    }

    /**
     * Bulk operations on cases
     */
    public function bulk_action()
    {
        if (!has_permission('cases', '', 'edit')) {
            access_denied('cases');
        }

        $action = $this->input->post('action', true);
        $case_ids = $this->input->post('case_ids', true);
        
        if (!$action || !$case_ids || !is_array($case_ids)) {
            echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
            return;
        }

        $success_count = 0;
        
        switch ($action) {
            case 'delete':
                if (!has_permission('cases', '', 'delete')) {
                    echo json_encode(['success' => false, 'message' => 'No delete permission']);
                    return;
                }
                
                foreach ($case_ids as $case_id) {
                    if ($this->delete_case_with_relations($case_id)) {
                        $success_count++;
                    }
                }
                break;
                
            case 'export':
                // Get cases by IDs and export
                $this->db->where_in('id', $case_ids);
                $cases = $this->Cases_model->get_all_cases_with_details();
                $this->export_cases_csv($cases);
                return;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Unknown action']);
                return;
        }
        
        echo json_encode([
            'success' => $success_count > 0,
            'message' => $success_count . ' cases processed successfully'
        ]);
    }

    private function delete_case_with_relations($case_id)
    {
        try {
            $this->db->trans_begin();
            
            // Delete related hearings
            $this->db->delete(db_prefix() . 'hearings', ['case_id' => $case_id]);
            
            // Delete related documents
            $this->db->delete(db_prefix() . 'files', ['rel_type' => 'case', 'rel_id' => $case_id]);
            
            // Delete the case
            $this->db->delete(db_prefix() . 'cases', ['id' => $case_id]);
            
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return false;
            } else {
                $this->db->trans_commit();
                return true;
            }
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error deleting case: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Archive old cases
     */
    public function archive_old_cases()
    {
        if (!is_admin()) {
            access_denied();
        }

        $cutoff_date = $this->input->post('cutoff_date', true);
        
        if (!$cutoff_date) {
            // Default to cases older than 2 years
            $cutoff_date = date('Y-m-d', strtotime('-2 years'));
        }

        try {
            // Create archive table if it doesn't exist
            $this->create_archive_table();
            
            // Get cases to archive
            $this->db->where('date_created <', $cutoff_date);
            $this->db->where('status', 'Closed'); // Only archive closed cases
            $cases_to_archive = $this->db->get(db_prefix() . 'cases')->result_array();
            
            $archived_count = 0;
            
            foreach ($cases_to_archive as $case) {
                if ($this->archive_single_case($case)) {
                    $archived_count++;
                }
            }
            
            echo json_encode([
                'success' => true,
                'message' => $archived_count . ' cases archived successfully'
            ]);
            
        } catch (Exception $e) {
            log_message('error', 'Error archiving cases: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error archiving cases: ' . $e->getMessage()
            ]);
        }
    }

    private function create_archive_table()
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS " . db_prefix() . "cases_archive (
                id INT PRIMARY KEY,
                case_title VARCHAR(255),
                case_number VARCHAR(100),
                client_id INT,
                contact_id INT,
                court_room_id INT,
                consultation_id INT,
                date_filed DATE,
                date_created DATETIME,
                archived_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                case_data TEXT
            ) ENGINE=InnoDB
        ");
    }

    private function archive_single_case($case)
    {
        try {
            $this->db->trans_begin();
            
            // Get all related data
            $case_data = [
                'case' => $case,
                'hearings' => $this->get_case_hearings($case['id']),
                'documents' => $this->get_case_documents($case['id'])
            ];
            
            // Insert into archive
            $archive_data = [
                'id' => $case['id'],
                'case_title' => $case['case_title'],
                'case_number' => $case['case_number'],
                'client_id' => $case['client_id'],
                'contact_id' => $case['contact_id'],
                'court_room_id' => $case['court_room_id'],
                'consultation_id' => $case['consultation_id'],
                'date_filed' => $case['date_filed'],
                'date_created' => $case['date_created'],
                'case_data' => json_encode($case_data)
            ];
            
            $this->db->insert(db_prefix() . 'cases_archive', $archive_data);
            
            // Delete from active tables
            $this->delete_case_with_relations($case['id']);
            
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return false;
            } else {
                $this->db->trans_commit();
                return true;
            }
            
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error archiving case ' . $case['id'] . ': ' . $e->getMessage());
            return false;
        }
    }

    private function get_case_hearings($case_id)
    {
        return $this->db->where('case_id', $case_id)
                       ->get(db_prefix() . 'hearings')
                       ->result_array();
    }

    private function get_case_documents($case_id)
    {
        return $this->db->where('rel_type', 'case')
                       ->where('rel_id', $case_id)
                       ->get(db_prefix() . 'files')
                       ->result_array();
    }

    /**
     * Calendar integration for cases and hearings
     */
    public function calendar_events()
    {
        if (!has_permission('cases', '', 'view')) {
            access_denied('cases');
        }

        $start = $this->input->get('start');
        $end = $this->input->get('end');
        
        $events = [];
        
        // Get hearings
        $this->db->select('h.*, c.case_title, c.case_number');
        $this->db->from(db_prefix() . 'hearings h');
        $this->db->join(db_prefix() . 'cases c', 'c.id = h.case_id', 'left');
        
        if ($start) $this->db->where('h.date >=', $start);
        if ($end) $this->db->where('h.date <=', $end);
        
        $hearings = $this->db->get()->result_array();
        
        foreach ($hearings as $hearing) {
            $events[] = [
                'id' => 'hearing_' . $hearing['id'],
                'title' => $hearing['case_title'] . ' - ' . $hearing['status'],
                'start' => $hearing['date'] . 'T' . $hearing['time'],
                'end' => date('Y-m-d\TH:i:s', strtotime($hearing['date'] . ' ' . $hearing['time'] . ' +2 hours')),
                'className' => 'hearing-event',
                'backgroundColor' => $this->get_hearing_color($hearing['status']),
                'url' => admin_url('cases/hearings/edit/' . $hearing['id'])
            ];
        }
        
        header('Content-Type: application/json');
        echo json_encode($events);
    }

    private function get_hearing_color($status)
    {
        switch (strtolower($status)) {
            case 'scheduled': return '#3498db';
            case 'completed': return '#2ecc71';
            case 'adjourned': return '#f39c12';
            case 'cancelled': return '#e74c3c';
            default: return '#95a5a6';
        }
    }

    /**
     * Notification system for upcoming hearings
     */
    public function check_upcoming_hearings()
    {
        if (!has_permission('cases', '', 'view')) {
            access_denied('cases');
        }

        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $next_week = date('Y-m-d', strtotime('+7 days'));
        
        // Get hearings for tomorrow
        $this->db->select('h.*, c.case_title, c.case_number, cl.company as client_name');
        $this->db->from(db_prefix() . 'hearings h');
        $this->db->join(db_prefix() . 'cases c', 'c.id = h.case_id', 'left');
        $this->db->join(db_prefix() . 'clients cl', 'cl.userid = c.client_id', 'left');
        $this->db->where('h.date', $tomorrow);
        $this->db->where('h.status', 'Scheduled');
        
        $tomorrow_hearings = $this->db->get()->result_array();
        
        // Get hearings for next week
        $this->db->select('h.*, c.case_title, c.case_number, cl.company as client_name');
        $this->db->from(db_prefix() . 'hearings h');
        $this->db->join(db_prefix() . 'cases c', 'c.id = h.case_id', 'left');
        $this->db->join(db_prefix() . 'clients cl', 'cl.userid = c.client_id', 'left');
        $this->db->where('h.date >=', $tomorrow);
        $this->db->where('h.date <=', $next_week);
        $this->db->where('h.status', 'Scheduled');
        
        $week_hearings = $this->db->get()->result_array();
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'tomorrow_hearings' => $tomorrow_hearings,
            'week_hearings' => $week_hearings,
            'counts' => [
                'tomorrow' => count($tomorrow_hearings),
                'week' => count($week_hearings)
            ]
        ]);
    }

    /**
     * Integration with Perfex CRM projects (if needed)
     */
    public function sync_with_projects()
    {
        if (!is_admin()) {
            access_denied();
        }

        try {
            // Load projects model if available
            if (file_exists(APPPATH . 'models/Projects_model.php')) {
                $this->load->model('projects_model');
                
                // Get cases that don't have associated projects
                $this->db->select('c.*, cl.company as client_name');
                $this->db->from(db_prefix() . 'cases c');
                $this->db->join(db_prefix() . 'clients cl', 'cl.userid = c.client_id', 'left');
                $this->db->where('c.project_id IS NULL OR c.project_id = 0');
                
                $cases_without_projects = $this->db->get()->result_array();
                
                $synced_count = 0;
                
                foreach ($cases_without_projects as $case) {
                    // Create project for case
                    $project_data = [
                        'name' => $case['case_title'],
                        'description' => 'Legal case: ' . $case['case_number'],
                        'clientid' => $case['client_id'],
                        'start_date' => $case['date_filed'] ?: $case['date_created'],
                        'status' => 2, // In progress
                        'billing_type' => 1, // Fixed rate
                        'project_cost' => 0,
                        'project_rate_per_hour' => 0,
                        'estimated_hours' => 0
                    ];
                    
                    $project_id = $this->projects_model->add($project_data);
                    
                    if ($project_id) {
                        // Update case with project ID
                        $this->db->where('id', $case['id']);
                        $this->db->update(db_prefix() . 'cases', ['project_id' => $project_id]);
                        $synced_count++;
                    }
                }
                
                echo json_encode([
                    'success' => true,
                    'message' => $synced_count . ' cases synced with projects'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Projects module not available'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error syncing with projects: ' . $e->getMessage()
            ]);
        }
    }

    public function debug()
{
    if (!is_admin()) {
        access_denied();
    }
    
    $debug_info = [];
    
    // 1. Check if module is properly loaded
    $debug_info['module_loaded'] = defined('CASES_MODULE_NAME');
    
    // 2. Check database tables
    $tables_to_check = [
        'case_consultations',
        'cases',
        'hearings',
        'courts',
        'court_rooms'
    ];
    
    $debug_info['tables'] = [];
    foreach ($tables_to_check as $table) {
        $full_table_name = db_prefix() . $table;
        $exists = $this->db->table_exists($table);
        
        $debug_info['tables'][$table] = [
            'exists' => $exists,
            'full_name' => $full_table_name
        ];
        
        if ($exists) {
            try {
                $count = $this->db->count_all($full_table_name);
                $debug_info['tables'][$table]['count'] = $count;
                
                // Get table structure
                $fields = $this->db->field_data($full_table_name);
                $debug_info['tables'][$table]['fields'] = array_map(function($field) {
                    return $field->name . ' (' . $field->type . ')';
                }, $fields);
            } catch (Exception $e) {
                $debug_info['tables'][$table]['error'] = $e->getMessage();
            }
        }
    }
    
    // 3. Check permissions
    $debug_info['permissions'] = [
        'view' => has_permission('cases', '', 'view'),
        'create' => has_permission('cases', '', 'create'),
        'edit' => has_permission('cases', '', 'edit'),
        'delete' => has_permission('cases', '', 'delete'),
        'is_admin' => is_admin(),
        'user_id' => get_staff_user_id()
    ];
    
    // 4. Check models
    $debug_info['models'] = [];
    try {
        $this->load->model('Cases_model');
        $debug_info['models']['Cases_model'] = 'Loaded successfully';
        
        // Test a simple model method
        $consultations = $this->Cases_model->get_consultations();
        $debug_info['models']['Cases_model_test'] = 'Method works, returned ' . count($consultations) . ' consultations';
    } catch (Exception $e) {
        $debug_info['models']['Cases_model'] = 'Error: ' . $e->getMessage();
    }
    
    // 5. Check routes
    $debug_info['routes'] = [
        'current_controller' => $this->router->class,
        'current_method' => $this->router->method,
        'base_url' => base_url(),
        'admin_url' => admin_url(),
        'site_url' => site_url()
    ];
    
    // 6. Test database connection
    try {
        $this->db->query('SELECT 1');
        $debug_info['database'] = 'Connection OK';
        $debug_info['db_prefix'] = db_prefix();
    } catch (Exception $e) {
        $debug_info['database'] = 'Error: ' . $e->getMessage();
    }
    
    // 7. Check environment
    $debug_info['environment'] = [
        'php_version' => PHP_VERSION,
        'ci_version' => CI_VERSION,
        'environment' => ENVIRONMENT,
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'
    ];
    
    // 8. Test JSON endpoints
    $debug_info['endpoints'] = [];
    $endpoints_to_test = [
        'consultations_list',
        'cases_list',
        'get_menu_stats'
    ];
    
    foreach ($endpoints_to_test as $endpoint) {
        try {
            // Simulate the method call
            ob_start();
            $this->$endpoint();
            $output = ob_get_clean();
            
            $json_data = json_decode($output, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $debug_info['endpoints'][$endpoint] = 'Valid JSON response';
            } else {
                $debug_info['endpoints'][$endpoint] = 'Invalid JSON: ' . substr($output, 0, 200);
            }
        } catch (Exception $e) {
            $debug_info['endpoints'][$endpoint] = 'Error: ' . $e->getMessage();
        }
    }
    
    // Output as HTML for easy reading
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Cases Module Debug</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .debug-section { margin-bottom: 30px; }
            .debug-title { font-size: 18px; font-weight: bold; color: #333; border-bottom: 2px solid #ccc; padding-bottom: 5px; }
            .debug-content { margin-top: 10px; }
            .success { color: green; }
            .error { color: red; }
            .warning { color: orange; }
            pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
            table { border-collapse: collapse; width: 100%; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
        </style>
    </head>
    <body>
        <h1>Cases Module Debug Information</h1>
        <p><strong>Generated:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
        
        <div class="debug-section">
            <div class="debug-title">Module Status</div>
            <div class="debug-content">
                <p>Module Loaded: <span class="<?php echo $debug_info['module_loaded'] ? 'success' : 'error'; ?>">
                    <?php echo $debug_info['module_loaded'] ? 'Yes' : 'No'; ?>
                </span></p>
            </div>
        </div>
        
        <div class="debug-section">
            <div class="debug-title">Database Tables</div>
            <div class="debug-content">
                <table>
                    <tr>
                        <th>Table</th>
                        <th>Exists</th>
                        <th>Count</th>
                        <th>Status</th>
                    </tr>
                    <?php foreach ($debug_info['tables'] as $table => $info): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($table); ?></td>
                        <td class="<?php echo $info['exists'] ? 'success' : 'error'; ?>">
                            <?php echo $info['exists'] ? 'Yes' : 'No'; ?>
                        </td>
                        <td><?php echo isset($info['count']) ? $info['count'] : 'N/A'; ?></td>
                        <td>
                            <?php if (isset($info['error'])): ?>
                                <span class="error"><?php echo htmlspecialchars($info['error']); ?></span>
                            <?php else: ?>
                                <span class="success">OK</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
        
        <div class="debug-section">
            <div class="debug-title">Permissions</div>
            <div class="debug-content">
                <table>
                    <tr>
                        <th>Permission</th>
                        <th>Status</th>
                    </tr>
                    <?php foreach ($debug_info['permissions'] as $perm => $status): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($perm); ?></td>
                        <td class="<?php echo $status ? 'success' : 'error'; ?>">
                            <?php echo $status ? 'Granted' : 'Denied'; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
        
        <div class="debug-section">
            <div class="debug-title">Models</div>
            <div class="debug-content">
                <?php foreach ($debug_info['models'] as $model => $status): ?>
                    <p><strong><?php echo htmlspecialchars($model); ?>:</strong> 
                    <span class="<?php echo strpos($status, 'Error') === false ? 'success' : 'error'; ?>">
                        <?php echo htmlspecialchars($status); ?>
                    </span></p>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="debug-section">
            <div class="debug-title">JSON Endpoints</div>
            <div class="debug-content">
                <table>
                    <tr>
                        <th>Endpoint</th>
                        <th>Status</th>
                    </tr>
                    <?php foreach ($debug_info['endpoints'] as $endpoint => $status): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($endpoint); ?></td>
                        <td class="<?php echo strpos($status, 'Valid JSON') === 0 ? 'success' : 'error'; ?>">
                            <?php echo htmlspecialchars($status); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
        
        <div class="debug-section">
            <div class="debug-title">Routes & URLs</div>
            <div class="debug-content">
                <?php foreach ($debug_info['routes'] as $key => $value): ?>
                    <p><strong><?php echo htmlspecialchars($key); ?>:</strong> <?php echo htmlspecialchars($value); ?></p>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="debug-section">
            <div class="debug-title">Environment</div>
            <div class="debug-content">
                <?php foreach ($debug_info['environment'] as $key => $value): ?>
                    <p><strong><?php echo htmlspecialchars($key); ?>:</strong> <?php echo htmlspecialchars($value); ?></p>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="debug-section">
            <div class="debug-title">Database Connection</div>
            <div class="debug-content">
                <p class="<?php echo strpos($debug_info['database'], 'Error') === false ? 'success' : 'error'; ?>">
                    <?php echo htmlspecialchars($debug_info['database']); ?>
                </p>
                <p><strong>DB Prefix:</strong> <?php echo htmlspecialchars($debug_info['db_prefix']); ?></p>
            </div>
        </div>
        
        <div class="debug-section">
            <div class="debug-title">Raw Debug Data</div>
            <div class="debug-content">
                <pre><?php echo htmlspecialchars(json_encode($debug_info, JSON_PRETTY_PRINT)); ?></pre>
            </div>
        </div>
        
        <div class="debug-section">
            <div class="debug-title">Quick Tests</div>
            <div class="debug-content">
                <p><a href="<?php echo admin_url('cases/debug'); ?>?test=json">Test JSON Response</a></p>
                <p><a href="<?php echo admin_url('cases/consultations_list'); ?>">Direct Consultations List</a></p>
                <p><a href="<?php echo admin_url('cases/cases_list'); ?>">Direct Cases List</a></p>
                <p><a href="<?php echo admin_url('cases/get_menu_stats'); ?>">Direct Menu Stats</a></p>
            </div>
        </div>
    </body>
    </html>
    <?php
    
    // Handle test parameter
    if ($this->input->get('test') === 'json') {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'JSON response working',
            'timestamp' => date('Y-m-d H:i:s'),
            'debug_info' => $debug_info
        ]);
    }
    
    exit; // Important: prevent any additional output
}

public function debug_data()
{
    if (!is_admin()) {
        access_denied();
    }
    
    header('Content-Type: application/json');
    
    $debug_info = [];
    
    // Check database tables
    $tables_to_check = [
        db_prefix() . 'case_consultations',
        db_prefix() . 'cases', 
        db_prefix() . 'clients',
        db_prefix() . 'contacts'
    ];
    
    foreach ($tables_to_check as $table) {
        $table_name = str_replace(db_prefix(), '', $table);
        $exists = $this->db->table_exists($table_name);
        $debug_info['tables'][$table] = [
            'exists' => $exists,
            'count' => $exists ? $this->db->count_all($table) : 0
        ];
        
        if ($exists) {
            // Get sample data
            $this->db->limit(1);
            $sample = $this->db->get($table)->row_array();
            $debug_info['tables'][$table]['sample'] = $sample;
        }
    }
    
    // Test consultations query
    try {
        $consultations = $this->Cases_model->get_consultations_with_names();
        $debug_info['consultations'] = [
            'count' => count($consultations),
            'data' => array_slice($consultations, 0, 2) // First 2 records
        ];
    } catch (Exception $e) {
        $debug_info['consultations'] = [
            'error' => $e->getMessage()
        ];
    }
    
    // Test cases query  
    try {
        $cases = $this->Cases_model->get_all_cases_with_details();
        $debug_info['cases'] = [
            'count' => count($cases),
            'data' => array_slice($cases, 0, 2) // First 2 records
        ];
    } catch (Exception $e) {
        $debug_info['cases'] = [
            'error' => $e->getMessage()
        ];
    }
    
    echo json_encode($debug_info, JSON_PRETTY_PRINT);
    exit;
}

}
