<?php

defined('BASEPATH') or exit('No direct script access allowed');

define('PHASE_CONSULTATION', 'consultation');
define('PHASE_LITIGATION', 'litigation');

class Cases extends AdminController
{
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


    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Cases_model');
        $this->load->model('Appointments_model');
    }

    public function index()
    {
        if (!has_permission('cases', '', 'view')) {
            access_denied('cases');
        }

        $data['title'] = _l('cases_management');
        $data['clients'] = $this->get_all_clients();
        $data['contacts'] = $this->get_all_contacts();

        $this->load->view('cases/manage', $data);
    }

    public function consultations_list()
{
    if (!has_permission('cases', '', 'view')) {
        access_denied('cases');
    }

    // Use the updated model method which includes client and contact names
    $result = $this->Cases_model->get_consultations_with_names();
    header('Content-Type: application/json');
    echo json_encode(['data' => $result]);
}

public function cases_list()
{
    if (!has_permission('cases', '', 'view')) {
        access_denied('cases');
    }

    // Use the updated model method which includes client and court information
    $result = $this->Cases_model->get_all_cases_with_details();
    header('Content-Type: application/json');
    echo json_encode(['data' => $result]);
}

    public function create_consultation()
{
    if (!has_permission('cases', '', 'create')) {
        access_denied('cases');
    }

    $consultation_id = $this->input->post('consultation_id', true);

    $data = [
        'client_id' => $this->input->post('client_id', true),
        'contact_id' => $this->input->post('contact_id', true) ?: NULL,
        'tag' => $this->input->post('tag', true),
        'note' => $this->input->post('note', false), // false to allow HTML from CKEditor
        'staff_id' => get_staff_user_id(),
    ];

    if ($consultation_id) {
        // Update existing consultation
        $this->db->where('id', $consultation_id);
        $updated = $this->db->update(db_prefix() . 'case_consultations', $data);
        echo json_encode(['success' => (bool) $updated]);
    } else {
        // New consultation
        $data['date_added'] = date('Y-m-d H:i:s');
        $data['phase'] = PHASE_CONSULTATION;
        $this->db->insert(db_prefix() . 'case_consultations', $data);
        echo json_encode(['success' => $this->db->affected_rows() > 0]);
    }
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
        if (!empty($data['hearings'])) {
            $hearing_ids = array_column($data['hearings'], 'id');
            
            // Get all documents related to these hearings
            $this->db->where('rel_type', 'hearing');
            $this->db->where_in('rel_id', $hearing_ids);
            $data['hearing_documents'] = $this->db->get(db_prefix() . 'files')->result_array();
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
    foreach ($cases as &$case) {
        $this->db->where('case_id', $case['id']);
        $case['hearing_count'] = $this->db->count_all_results(db_prefix().'hearings');
        
        // Get document counts
        $this->db->where('rel_type', 'case');
        $this->db->where('rel_id', $case['id']);
        $case['document_count'] = $this->db->count_all_results(db_prefix().'files');
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

// Start Here for Badges and New Menu 

/**
 * AJAX endpoint for menu statistics
 */
public function get_menu_stats()
{
    header('Content-Type: application/json');
    
    if (!has_permission('cases', '', 'view')) {
        echo json_encode(['error' => 'No permission']);
        return;
    }

    try {
        $today = date('Y-m-d');
        
        // Count pending consultations
        $this->db->where('phase', 'consultation');
        $consultations = $this->db->count_all_results(db_prefix() . 'case_consultations');
        
        // Count today's hearings
        $this->db->where('DATE(date)', $today);
        $this->db->or_where('DATE(next_date)', $today);
        $today_hearings = $this->db->count_all_results(db_prefix() . 'hearings');
        
        echo json_encode([
            'consultations' => (int)$consultations,
            'today_hearings' => (int)$today_hearings,
            'success' => true
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage(), 'success' => false]);
    }
}

/**
 * Fix existing consultations_list method
 */
public function consultations_list()
{
    header('Content-Type: application/json');
    
    if (!has_permission('cases', '', 'view')) {
        echo json_encode(['success' => false, 'data' => []]);
        return;
    }

    try {
        // Use the updated model method which includes client and contact names
        $result = $this->Cases_model->get_consultations_with_names();
        echo json_encode(['data' => $result, 'success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
    }
}

/**
 * Fix existing cases_list method
 */
public function cases_list()
{
    header('Content-Type: application/json');
    
    if (!has_permission('cases', '', 'view')) {
        echo json_encode(['success' => false, 'data' => []]);
        return;
    }

    try {
        // Use the updated model method which includes client and court information
        $result = $this->Cases_model->get_all_cases_with_details();
        echo json_encode(['data' => $result, 'success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
    }
}

}
