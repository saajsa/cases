<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Hearings extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        
        // Check if user is logged in
        if (!is_staff_logged_in()) {
            redirect(admin_url('authentication'));
        }
    }
    
    
    /**
     * Main hearings page - lists all hearings
     */
    public function index()
    {
        if (!has_permission('cases', '', 'view')) {
            access_denied('cases');
        }

        $this->db->select('h.*, c.case_title');
        $this->db->from(db_prefix() . 'hearings h');
        $this->db->join(db_prefix() . 'cases c', 'c.id = h.case_id', 'left');
        $this->db->order_by('h.date', 'DESC');
        $data['hearings'] = $this->db->get()->result_array();
        
        // If case_id is provided in URL, redirect to add with that case_id
        if ($this->input->get('case_id')) {
            redirect(admin_url('cases/hearings/add?case_id=' . $this->input->get('case_id')));
        }
        
        $data['title'] = 'Hearings';
        $this->load->view('admin/hearings/manage', $data);
    }

 /**
 * Add a new hearing
 */
public function add()
{
    if (!has_permission('cases', '', 'create')) {
        access_denied('cases');
    }

    if ($this->input->post()) {
        try {
            // Enable error reporting for debugging
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
            
            // Load hearing constants and validation helper
            require_once(__DIR__ . '/../config/hearing_constants.php');
            require_once(__DIR__ . '/../helpers/validation_helper.php');
            
            // Get and validate form data using enhanced validation
            $input_data = [
                'case_id'         => $this->input->post('case_id', true),
                'date'            => $this->input->post('date', true),
                'time'            => $this->input->post('time', true),
                'description'     => $this->input->post('description', true),
                'status'          => $this->input->post('status', true) ?: HEARING_STATUS_SCHEDULED,
                'next_date'       => $this->input->post('next_date', true) ?: null,
                'hearing_purpose' => $this->input->post('hearing_purpose', true) ?: $this->input->post('custom_purpose', true),
                'upcoming_purpose' => $this->input->post('upcoming_purpose', true),
                'is_completed'    => $this->input->post('is_completed') ? 1 : 0
            ];
            
            // Check if this is a past date entry - simple approach
            $allow_past_dates = false;
            if (!empty($input_data['date']) && $input_data['date'] < date('Y-m-d')) {
                $allow_past_dates = true;
                // Auto-set status to completed for past dates
                if (empty($input_data['status']) || $input_data['status'] === HEARING_STATUS_SCHEDULED) {
                    $input_data['status'] = HEARING_STATUS_COMPLETED;
                }
            }
            
            // Simplified validation - just basic required fields
            if (empty($input_data['case_id']) || empty($input_data['date'])) {
                set_alert('danger', 'Case ID and date are required');
                redirect(admin_url('cases/hearings/add'));
                return;
            }
            
            // Prepare basic data for insert
            $data = [
                'case_id' => (int)$input_data['case_id'],
                'date' => $input_data['date'],
                'time' => $input_data['time'] ?: '10:00:00',
                'description' => $input_data['description'] ?: '',
                'status' => $input_data['status'],
                'hearing_purpose' => $input_data['hearing_purpose'] ?: '',
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            // Add added_by if column exists
            if ($this->db->field_exists('added_by', db_prefix() . 'hearings')) {
                $data['added_by'] = get_staff_user_id();
            }
            
            log_message('info', 'About to insert hearing data: ' . json_encode($data));
            
            // Start transaction
            $this->db->trans_begin();
            
            // Insert data
            $this->db->insert(db_prefix() . 'hearings', $data);
            $current_hearing_id = $this->db->insert_id();
            
            // If next hearing details are provided, create an entry for it
            $next_date = $this->input->post('next_date', true);
            $next_purpose = $this->input->post('upcoming_purpose', true);
            
            if (!empty($next_date) && $current_hearing_id) {
                $next_hearing = [
                    'case_id'           => $data['case_id'],
                    'date'              => $next_date,
                    'time'              => $this->input->post('next_time', true) ?: '10:00',
                    'status'            => 'Scheduled',
                    'parent_hearing_id' => $current_hearing_id,
                    'hearing_purpose'   => $next_purpose,
                    'description'       => 'Follow-up to hearing on ' . $data['date'],
                    'created_at'        => date('Y-m-d H:i:s')
                ];
                
                if ($this->db->field_exists('added_by', db_prefix() . 'hearings')) {
                    $next_hearing['added_by'] = get_staff_user_id();
                }
                
                $this->db->insert(db_prefix() . 'hearings', $next_hearing);
            }
            
            // Commit transaction
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                set_alert('danger', 'Failed to add hearing');
            } else {
                $this->db->trans_commit();
                set_alert('success', 'Hearing added successfully');
            }
            
            // Redirect appropriately
            $redirect_url = $this->input->post('redirect_url');
            if ($redirect_url) {
                redirect($redirect_url);
            } else {
                redirect(admin_url('cases'));
            }
        } catch (Exception $e) {
            // Log detailed error information
            log_message('error', 'Hearing add failed: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' | Line: ' . $e->getLine() . ' | Trace: ' . $e->getTraceAsString());
            set_alert('danger', 'Error: ' . $e->getMessage() . ' (Line: ' . $e->getLine() . ')');
            redirect(admin_url('cases/hearings'));
        } catch (Error $e) {
            // Also catch PHP errors
            log_message('error', 'Hearing add PHP error: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' | Line: ' . $e->getLine());
            set_alert('danger', 'PHP Error: ' . $e->getMessage() . ' (Line: ' . $e->getLine() . ')');
            redirect(admin_url('cases/hearings'));
        }
    } else {
        // Get case list
        $this->db->select('id, case_title, case_number');
        $this->db->from(db_prefix() . 'cases');
        $data['cases'] = $this->db->get()->result_array();
        
        // If case_id is provided, get case details
        $case_id = $this->input->get('case_id');
        if ($case_id) {
            $this->db->where('id', $case_id);
            $data['case'] = $this->db->get(db_prefix() . 'cases')->row_array();
            
            // Check if this is first hearing
            $this->db->where('case_id', $case_id);
            $data['existing_hearings_count'] = $this->db->count_all_results(db_prefix() . 'hearings');
            $data['is_first_hearing'] = ($data['existing_hearings_count'] == 0);
        }
        
        // If viewing all hearings, get the hearing list too
        $this->db->select('h.*, c.case_title');
        $this->db->from(db_prefix() . 'hearings h');
        $this->db->join(db_prefix() . 'cases c', 'c.id = h.case_id', 'left');
        $this->db->order_by('h.date', 'DESC');
        $data['hearings'] = $this->db->get()->result_array();
        
        $data['title'] = $case_id && isset($data['is_first_hearing']) && $data['is_first_hearing'] 
            ? 'Add First Hearing' 
            : 'Add Hearing';
            
        $this->load->view('admin/hearings/manage', $data);
    }
}

/**
 * Edit a hearing
 */
public function edit($id)
{
    if (!has_permission('cases', '', 'edit')) {
        access_denied('cases');
    }

    if ($this->input->post()) {
        try {
            // Load hearing constants and validation helper
            require_once(__DIR__ . '/../config/hearing_constants.php');
            require_once(__DIR__ . '/../helpers/validation_helper.php');
            
            // Get current hearing for validation context
            $this->db->where('id', $id);
            $current_hearing = $this->db->get(db_prefix() . 'hearings')->row_array();
            
            if (!$current_hearing) {
                set_alert('danger', 'Hearing not found');
                redirect(admin_url('cases/hearings'));
                return;
            }
            
            // Get and validate form data using enhanced validation
            $input_data = [
                'case_id'         => $this->input->post('case_id', true),
                'date'            => $this->input->post('date', true),
                'time'            => $this->input->post('time', true),
                'description'     => $this->input->post('description', true),
                'status'          => $this->input->post('status', true) ?: HEARING_STATUS_SCHEDULED,
                'next_date'       => $this->input->post('next_date', true) ?: null,
                'hearing_purpose' => $this->input->post('hearing_purpose', true) ?: $this->input->post('custom_purpose', true),
                'upcoming_purpose' => $this->input->post('upcoming_purpose', true),
                'is_completed'    => $this->input->post('is_completed') ? 1 : 0
            ];
            
            // Check if this is a past date entry - simple approach
            $allow_past_dates = false;
            if (!empty($input_data['date']) && $input_data['date'] < date('Y-m-d')) {
                $allow_past_dates = true;
                // Auto-set status to completed for past dates
                if (empty($input_data['status']) || $input_data['status'] === HEARING_STATUS_SCHEDULED) {
                    $input_data['status'] = HEARING_STATUS_COMPLETED;
                }
            }
            
            // Validate data using enhanced validation with context
            $validation_result = cases_validate_hearing_data($input_data, [
                'hearing_id' => $id,
                'current_hearing_date' => $current_hearing['date'],
                'current_status' => $current_hearing['status'],
                'allow_past_dates' => $allow_past_dates
            ]);
            
            if (!$validation_result['valid']) {
                set_alert('danger', 'Validation errors: ' . implode(', ', $validation_result['errors']));
                redirect(admin_url('cases/hearings/edit/' . $id));
                return;
            }
            
            // Show warnings if any
            if (!empty($validation_result['warnings'])) {
                foreach ($validation_result['warnings'] as $warning) {
                    set_alert('warning', $warning);
                }
            }
            
            $data = $validation_result['data'];
            
            // Start transaction
            $this->db->trans_begin();
            
            // Update record
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'hearings', $data);
            
            // Handle next hearing
            $next_date = $this->input->post('next_date', true);
            $next_purpose = $this->input->post('upcoming_purpose', true);
            
            if (!empty($next_date)) {
                // Check if a next hearing already exists
                $this->db->where('parent_hearing_id', $id);
                $existing_next = $this->db->get(db_prefix() . 'hearings')->row_array();
                
                if ($existing_next) {
                    // Update existing next hearing
                    $this->db->where('id', $existing_next['id']);
                    $this->db->update(db_prefix() . 'hearings', [
                        'date'            => $next_date,
                        'time'            => $this->input->post('next_time', true) ?: $existing_next['time'],
                        'hearing_purpose' => $next_purpose
                    ]);
                } else {
                    // Create new next hearing
                    $next_hearing = [
                        'case_id'           => $data['case_id'],
                        'date'              => $next_date,
                        'time'              => $this->input->post('next_time', true) ?: '10:00',
                        'status'            => 'Scheduled',
                        'parent_hearing_id' => $id,
                        'hearing_purpose'   => $next_purpose,
                        'description'       => 'Follow-up to hearing on ' . $data['date'],
                        'created_at'        => date('Y-m-d H:i:s')
                    ];
                    
                    if ($this->db->field_exists('added_by', db_prefix() . 'hearings')) {
                        $next_hearing['added_by'] = get_staff_user_id();
                    }
                    
                    $this->db->insert(db_prefix() . 'hearings', $next_hearing);
                }
            } else {
                // If next_date is removed, check and delete any follow-up hearings
                $this->db->where('parent_hearing_id', $id);
                $this->db->delete(db_prefix() . 'hearings');
            }
            
            // Commit transaction
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                set_alert('warning', 'No changes were made');
            } else {
                $this->db->trans_commit();
                set_alert('success', 'Hearing updated successfully');
            }
            
            // Redirect appropriately
            $redirect_url = $this->input->post('redirect_url');
            if ($redirect_url) {
                redirect($redirect_url);
            } else {
                redirect(admin_url('cases'));
            }
        } catch (Exception $e) {
            set_alert('danger', 'Error: ' . $e->getMessage());
            redirect(admin_url('cases/hearings/edit/' . $id));
        }
    } else {
        // Get hearing details
        $this->db->where('id', $id);
        $data['hearing'] = $this->db->get(db_prefix() . 'hearings')->row_array();
        
        if (!$data['hearing']) {
            set_alert('danger', 'Hearing not found');
            redirect(admin_url('cases/hearings'));
        }
        
        // Get comprehensive case details with related information
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
        $this->db->where('c.id', $data['hearing']['case_id']);
        $data['case'] = $this->db->get()->row_array();
        
        if (!$data['case']) {
            set_alert('danger', 'Associated case not found');
            redirect(admin_url('cases/hearings'));
        }
        
        // Format court display for case context
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
        
        // Get hearing history for this case (for context)
        $this->db->select('h.*, cr.court_no, cr.judge_name, ct.name as court_name');
        $this->db->from(db_prefix().'hearings h');
        $this->db->join(db_prefix().'cases c', 'c.id = h.case_id', 'left');
        $this->db->join(db_prefix().'court_rooms cr', 'cr.id = c.court_room_id', 'left');
        $this->db->join(db_prefix().'courts ct', 'ct.id = cr.court_id', 'left');
        $this->db->where('h.case_id', $data['hearing']['case_id']);
        $this->db->order_by('h.date', 'DESC');
        $this->db->limit(10); // Show last 10 hearings for context
        $data['hearing_history'] = $this->db->get()->result_array();
        
        // Get hearing-specific documents
        $this->db->where('rel_type', 'hearing');
        $this->db->where('rel_id', $id);
        $this->db->order_by('dateadded', 'DESC');
        $data['hearing_documents'] = $this->db->get(db_prefix() . 'files')->result_array();
        
        // Get linked upcoming hearing if exists
        $this->db->where('parent_hearing_id', $id);
        $data['upcoming_hearing'] = $this->db->get(db_prefix() . 'hearings')->row_array();
        
        // Count total hearings for this case
        $this->db->where('case_id', $data['hearing']['case_id']);
        $data['total_hearings'] = $this->db->count_all_results(db_prefix() . 'hearings');
        
        $data['title'] = 'Edit Hearing - ' . $data['case']['case_title'];
        $this->load->view('admin/hearings/edit', $data);
    }
}

    /**
     * Delete a hearing with enhanced safety
     */
    public function delete($id)
    {
        if (!has_permission('cases', '', 'delete')) {
            access_denied('cases');
        }

        // Validate hearing ID
        if (!is_numeric($id) || $id <= 0) {
            set_alert('danger', 'Invalid hearing ID');
            redirect(admin_url('cases/hearings'));
            return;
        }

        // Get hearing details before deletion for logging
        $this->db->where('id', $id);
        $hearing = $this->db->get(db_prefix() . 'hearings')->row_array();
        
        if (!$hearing) {
            set_alert('danger', 'Hearing not found');
            redirect(admin_url('cases/hearings'));
            return;
        }

        // Get case info for better messaging
        $this->db->where('id', $hearing['case_id']);
        $case = $this->db->get(db_prefix() . 'cases')->row_array();

        // Start transaction for safe deletion
        $this->db->trans_begin();

        try {
            // Delete any child hearings first (where this hearing is parent)
            $this->db->where('parent_hearing_id', $id);
            $child_hearings = $this->db->get(db_prefix() . 'hearings')->result_array();
            
            if (!empty($child_hearings)) {
                $this->db->where('parent_hearing_id', $id);
                $this->db->delete(db_prefix() . 'hearings');
            }

            // Delete the main hearing
            $this->db->where('id', $id);
            $this->db->delete(db_prefix() . 'hearings');
            
            // Delete associated documents if any
            $this->db->where('rel_type', 'hearing');
            $this->db->where('rel_id', $id);
            $this->db->delete(db_prefix() . 'files');

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                set_alert('danger', 'Failed to delete hearing');
            } else {
                $this->db->trans_commit();
                
                // Log the deletion
                log_message('info', 'Hearing deleted: ID=' . $id . ', Case=' . ($case['case_title'] ?? 'Unknown') . ', User=' . get_staff_user_id());
                
                $success_msg = 'Hearing deleted successfully';
                if ($case) {
                    $success_msg .= ' from case: ' . $case['case_title'];
                }
                if (!empty($child_hearings)) {
                    $success_msg .= ' (including ' . count($child_hearings) . ' linked hearing(s))';
                }
                
                set_alert('success', $success_msg);
            }
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Hearing deletion failed: ' . $e->getMessage());
            set_alert('danger', 'Error deleting hearing: ' . $e->getMessage());
        }
        
        // Redirect back to appropriate page
        if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect(admin_url('cases'));
        }
    }

/**
 * Controller Method: Causelist - Enhanced with unified temporal logic
 */
public function causelist()
{
    if (!has_permission('cases', '', 'view')) {
        access_denied('cases');
    }
    
    // Load hearing constants for consistent logic
    require_once(__DIR__ . '/../config/hearing_constants.php');
    
    // Validate and sanitize date input
    $date = $this->input->get('date');
    if (!$date || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        $date = date('Y-m-d');
    }
    
    // Validate date format and range
    $date_obj = DateTime::createFromFormat('Y-m-d', $date);
    if (!$date_obj || $date_obj->format('Y-m-d') !== $date) {
        $date = date('Y-m-d');
    }
    
    // UNIFIED QUERY - Get hearings scheduled for this date (not cancelled)
    $this->db->select('
        h.id as hearing_id,
        h.date,
        h.time,
        h.description,
        h.status,
        h.next_date,
        h.hearing_purpose,
        h.upcoming_purpose,
        h.is_completed,
        h.parent_hearing_id,
        c.id as case_id,
        c.case_title,
        c.case_number,
        cl.company as client_name,
        ct.name as court_name,
        cr.court_no,
        cr.judge_name
    ');
    $this->db->from(db_prefix() . 'hearings h');
    $this->db->join(db_prefix() . 'cases c', 'c.id = h.case_id', 'left');
    $this->db->join(db_prefix() . 'clients cl', 'cl.userid = c.client_id', 'left');
    $this->db->join(db_prefix() . 'court_rooms cr', 'cr.id = c.court_room_id', 'left');
    $this->db->join(db_prefix() . 'courts ct', 'ct.id = cr.court_id', 'left');
    $this->db->where('DATE(h.date)', $date);
    $this->db->where('h.status !=', HEARING_STATUS_CANCELLED);
    $this->db->order_by('h.time', 'ASC');
    
    $query = $this->db->get();
    
    // Initialize hearings array with temporal classification
    $data['hearings'] = [];
    
    if ($query && $query->num_rows() > 0) {
        $results = $query->result_array();
        
        foreach ($results as $hearing) {
            // Add temporal classification to each hearing
            $hearing['temporal_class'] = hearing_get_temporal_classification($hearing['date'], $hearing['status']);
            $hearing['is_active'] = hearing_is_active($hearing['date'], $hearing['status']);
            
            // Add status definition for UI
            $status_defs = hearing_get_status_definitions();
            $hearing['status_info'] = $status_defs[$hearing['status']] ?? null;
            
            $data['hearings'][] = $hearing;
        }
    }
    
    // Get available dates with active hearings (not cancelled)
    $this->db->select('DISTINCT DATE(date) as hearing_date');
    $this->db->from(db_prefix() . 'hearings');
    $this->db->where('DATE(date) >=', date('Y-m-d')); // Only include today and future dates
    $this->db->where('status !=', HEARING_STATUS_CANCELLED);
    $this->db->order_by('date', 'ASC');
    $date_query = $this->db->get();
    
    $data['upcoming_dates'] = $date_query->result_array();
    
    $data['date'] = $date;
    $data['title'] = 'Daily Cause List - ' . date('d M Y', strtotime($date));
    
    // Add temporal classification info for the date
    $data['date_temporal_class'] = hearing_get_temporal_classification($date);
    
    // Add debug information
    if (ENVIRONMENT == 'development' || isset($_GET['debug'])) {
        $data['debug'] = [
            'date_requested' => $date,
            'formatted_date' => date('Y-m-d', strtotime($date)),
            'query' => $sql,
            'params' => [$date],
            'results_count' => count($data['hearings']),
        ];
        
        // Get all hearings for debugging
        $all_hearings_query = $this->db->query(
            "SELECT id, case_id, date, next_date, time, status 
             FROM " . db_prefix() . "hearings 
             ORDER BY date DESC LIMIT 20"
        );
        
        $data['debug']['raw_hearings'] = $all_hearings_query->result_array();
    }
    
    // Load the view
    $this->load->view('admin/hearings/causelist', $data);
}

/**
 * Get cause list as JSON for AJAX requests - Enhanced with unified logic
 */
public function get_causelist()
{
    if (!has_permission('cases', '', 'view')) {
        access_denied('cases');
    }
    
    // Load rate limiter and enforce limits for AJAX endpoint
    $this->load->helper('modules/cases/helpers/rate_limiter_helper');
    cases_enforce_rate_limit('get_causelist', 30, 300); // 30 requests per 5 minutes
    
    // Load hearing constants for consistent logic
    require_once(__DIR__ . '/../config/hearing_constants.php');
    
    // Default to today's date
    $date = $this->input->get('date') ? $this->input->get('date') : date('Y-m-d');
    
    // UNIFIED QUERY - Use same logic as causelist method (filter by date, not next_date)
    $this->db->select('
        h.id as hearing_id,
        h.date,
        h.time,
        h.description,
        h.status,
        h.next_date,
        h.hearing_purpose,
        h.parent_hearing_id,
        c.id as case_id,
        c.case_title,
        c.case_number,
        cl.company as client_name,
        CONCAT(co.firstname, " ", co.lastname) as contact_name,
        ct.name as court_name,
        cr.court_no,
        cr.judge_name
    ');
    $this->db->from(db_prefix() . 'hearings h');
    $this->db->join(db_prefix() . 'cases c', 'c.id = h.case_id', 'left');
    $this->db->join(db_prefix() . 'clients cl', 'cl.userid = c.client_id', 'left');
    $this->db->join(db_prefix() . 'contacts co', 'co.id = c.contact_id', 'left');
    $this->db->join(db_prefix() . 'court_rooms cr', 'cr.id = c.court_room_id', 'left');
    $this->db->join(db_prefix() . 'courts ct', 'ct.id = cr.court_id', 'left');
    $this->db->where('DATE(h.date)', $date);
    $this->db->where('h.status !=', HEARING_STATUS_CANCELLED);
    $this->db->order_by('h.time', 'ASC');
    
    $query = $this->db->get();
    
    $hearings = [];
    
    if ($query && $query->num_rows() > 0) {
        $results = $query->result_array();
        
        foreach ($results as $row) {
            // Format data for display
            $row['formatted_time'] = date('h:i A', strtotime($row['time']));
            $row['formatted_date'] = date('d M Y', strtotime($row['date']));
            
            if (!empty($row['next_date'])) {
                $row['formatted_next_date'] = date('d M Y', strtotime($row['next_date']));
            } else {
                $row['formatted_next_date'] = '';
            }
            
            // Add temporal classification
            $row['temporal_class'] = hearing_get_temporal_classification($row['date'], $row['status']);
            $row['is_active'] = hearing_is_active($row['date'], $row['status']);
            
            // Add status definition for UI
            $status_defs = hearing_get_status_definitions();
            $row['status_info'] = $status_defs[$row['status']] ?? null;
            
            $hearings[] = $row;
        }
    }
    
    // Debug info
    $debug = [
        'date_requested' => $date,
        'formatted_date' => date('Y-m-d', strtotime($date)),
        'query' => $sql,
        'params' => [$date],
        'results_count' => count($hearings),
    ];
    
    header('Content-Type: application/json');
    echo json_encode([
        'data' => $hearings,
        'debug' => $debug
    ]);
}
    
    /**
     * Calendar view of hearings
     */
    public function calendar()
    {
        if (!has_permission('cases', '', 'view')) {
            access_denied('cases');
        }
        
        $data['title'] = 'Hearings Calendar';
        
        // For calendar we need to get all hearings
        $this->db->select('
            ' . db_prefix() . 'hearings.id,
            ' . db_prefix() . 'hearings.date,
            ' . db_prefix() . 'hearings.time,
            ' . db_prefix() . 'hearings.status,
            ' . db_prefix() . 'cases.case_title,
            ' . db_prefix() . 'cases.case_number
        ');
        $this->db->from(db_prefix() . 'hearings');
        $this->db->join(db_prefix() . 'cases', db_prefix() . 'cases.id = ' . db_prefix() . 'hearings.case_id', 'left');
        $result = $this->db->get();
        
        $data['hearings'] = [];
        
        if ($result && $result->num_rows() > 0) {
            foreach ($result->result_array() as $hearing) {
                // Format for calendar
                $data['hearings'][] = [
                    'id' => $hearing['id'],
                    'title' => $hearing['case_title'] . ' (' . $hearing['case_number'] . ')',
                    'start' => $hearing['date'] . 'T' . $hearing['time'],
                    'url' => admin_url('cases/hearings/edit/' . $hearing['id']),
                    'className' => 'hearing-status-' . strtolower($hearing['status'])
                ];
            }
        }
        
        $this->load->view('admin/shared/calendar', $data);
    }

/**
 * Quick update method for Hearings controller with fixed redirect
 */
public function quick_update($hearing_id)
{
    if (!has_permission('cases', '', 'edit')) {
        access_denied('cases');
    }

    // Get the existing hearing
    $this->db->where('id', $hearing_id);
    $hearing = $this->db->get(db_prefix() . 'hearings')->row_array();
    
    if (!$hearing) {
        set_alert('danger', 'Hearing not found');
        redirect(admin_url('cases/hearings'));
    }
    
    // Get the case details
    $this->db->where('id', $hearing['case_id']);
    $case = $this->db->get(db_prefix() . 'cases')->row_array();
    
    if ($this->input->post()) {
        // Simple validation without helper functions
        if (!is_numeric($hearing_id) || $hearing_id <= 0) {
            set_alert('danger', 'Invalid hearing ID');
            redirect(admin_url('cases/hearings'));
            return;
        }
        
        $validated_hearing_id = (int)$hearing_id;

        $this->db->trans_begin(); // Start transaction
        
        try {
            // Simple validation without helper functions
            $validated_data = [
                'status' => $this->input->post('status', true),
                'description' => $this->input->post('description', true),
                'next_date' => $this->input->post('next_date', true),
                'next_time' => $this->input->post('next_time', true),
                'upcoming_purpose' => $this->input->post('upcoming_purpose', true),
                'send_notification' => $this->input->post('send_notification', true)
            ];
            
            // Basic validation
            if (empty($validated_data['status'])) {
                set_alert('danger', 'Status is required');
                redirect(admin_url('cases/hearings/quick_update/' . $validated_hearing_id));
                return;
            }
            
            // Update existing hearing status
            $current_update = [
                'status'      => $validated_data['status'],
                'description' => $validated_data['description'] ?? '',
                'is_completed' => ($validated_data['status'] == 'Completed') ? 1 : 0
            ];
            
            $this->db->where('id', $hearing_id);
            $this->db->update(db_prefix() . 'hearings', $current_update);
            
            // Check if next hearing is being scheduled
            if (!empty($validated_data['next_date'])) {
                // Check if a next hearing already exists based on parent_hearing_id
                $this->db->where('parent_hearing_id', $validated_hearing_id);
                $existing_next = $this->db->get(db_prefix() . 'hearings')->row_array();
                
                $next_hearing_data = [
                    'date' => $validated_data['next_date'],
                    'time' => $validated_data['next_time'] ?? '10:00:00',
                    'hearing_purpose' => $validated_data['upcoming_purpose'] ?? ''
                ];
                
                if ($existing_next) {
                    // Update existing next hearing
                    $this->db->where('id', $existing_next['id']);
                    $this->db->update(db_prefix() . 'hearings', $next_hearing_data);
                } else {
                    // Create new hearing record for the next date
                    $next_hearing = [
                        'case_id'         => $hearing['case_id'],
                        'date'            => $validated_data['next_date'],
                        'time'            => $validated_data['next_time'] ?? '10:00:00',
                        'hearing_purpose' => $validated_data['upcoming_purpose'] ?? '',
                        'status'          => 'Scheduled',
                        'parent_hearing_id' => $validated_hearing_id,
                        'created_at'      => date('Y-m-d H:i:s')
                    ];
                    
                    // Add added_by if column exists
                    if ($this->db->field_exists('added_by', db_prefix() . 'hearings')) {
                        $next_hearing['added_by'] = get_staff_user_id();
                    }
                    
                    $this->db->insert(db_prefix() . 'hearings', $next_hearing);
                }
                
                // Update current hearing with next date reference
                $this->db->where('id', $validated_hearing_id);
                $this->db->update(db_prefix() . 'hearings', ['next_date' => $validated_data['next_date']]);
            }
            
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                set_alert('danger', 'Failed to update hearing');
            } else {
                $this->db->trans_commit();
                
                // Enhanced success message
                $success_message = 'Hearing updated successfully';
                if (!empty($validated_data['next_date'])) {
                    $success_message .= ' and next hearing scheduled for ' . date('M j, Y', strtotime($validated_data['next_date']));
                    if (!empty($validated_data['send_notification'])) {
                        $success_message .= ' with notification reminder enabled';
                    }
                }
                
                set_alert('success', $success_message);
            }
            
            // FIXED REDIRECT - Go to main cases page
            redirect(admin_url('cases'));
            
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Hearing update failed: ' . $e->getMessage());
            set_alert('danger', 'Error: ' . $e->getMessage());
            redirect(admin_url('cases/hearings/quick_update/' . $hearing_id));
        }
    }
    
    // Get linked upcoming hearing if exists
    $this->db->where('parent_hearing_id', $hearing_id);
    $data['upcoming_hearing'] = $this->db->get(db_prefix() . 'hearings')->row_array();
    
    // Load view data
    $data = [
        'title'           => 'Update Hearing Status',
        'hearing'         => $hearing,
        'case'            => $case,
        'upcoming_hearing' => $data['upcoming_hearing'] ?? null
    ];
    
    $this->load->view('admin/hearings/quick_update', $data);
}
}