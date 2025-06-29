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
        $this->load->view('cases/hearings/manage', $data);
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
            // Get form data
            $data = [
                'case_id'         => $this->input->post('case_id', true),
                'date'            => $this->input->post('date', true),
                'time'            => $this->input->post('time', true),
                'description'     => $this->input->post('description', true),
                'status'          => $this->input->post('status', true) ?: 'Scheduled',
                'next_date'       => $this->input->post('next_date', true) ?: null,
                'hearing_purpose' => $this->input->post('hearing_purpose', true),
                'created_at'      => date('Y-m-d H:i:s'),
                'is_completed'    => $this->input->post('is_completed') ? 1 : 0
            ];
            
            // Add added_by if column exists
            if ($this->db->field_exists('added_by', db_prefix() . 'hearings')) {
                $data['added_by'] = get_staff_user_id();
            }
            
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
            set_alert('danger', 'Error: ' . $e->getMessage());
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
            
        $this->load->view('cases/hearings/manage', $data);
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
            // Get form data
            $data = [
                'case_id'         => $this->input->post('case_id', true),
                'date'            => $this->input->post('date', true),
                'time'            => $this->input->post('time', true),
                'description'     => $this->input->post('description', true),
                'status'          => $this->input->post('status', true) ?: 'Scheduled',
                'next_date'       => $this->input->post('next_date', true) ?: null,
                'hearing_purpose' => $this->input->post('hearing_purpose', true),
                'is_completed'    => $this->input->post('is_completed') ? 1 : 0
            ];
            
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
        
        // Get case details
        $this->db->where('id', $data['hearing']['case_id']);
        $data['case'] = $this->db->get(db_prefix() . 'cases')->row_array();
        
        // Check if this is the only hearing for this case
        $this->db->where('case_id', $data['hearing']['case_id']);
        $hearings_count = $this->db->count_all_results(db_prefix() . 'hearings');
        $data['is_first_hearing'] = ($hearings_count == 1);
        
        // Get case list
        $this->db->select('id, case_title');
        $this->db->from(db_prefix() . 'cases');
        $data['cases'] = $this->db->get()->result_array();
        
        // Get linked upcoming hearing if exists
        $this->db->where('parent_hearing_id', $id);
        $data['upcoming_hearing'] = $this->db->get(db_prefix() . 'hearings')->row_array();
        
        $data['title'] = 'Edit Hearing';
        $this->load->view('cases/hearings/manage', $data);
    }
}

    /**
     * Delete a hearing
     */
    public function delete($id)
    {
        if (!has_permission('cases', '', 'delete')) {
            access_denied('cases');
        }

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'hearings');
        
        if ($this->db->affected_rows() > 0) {
            set_alert('success', 'Hearing deleted successfully');
        } else {
            set_alert('danger', 'Failed to delete hearing');
        }
        
        // Check if referrer exists to redirect back
        if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect(admin_url('cases/hearings'));
        }
    }

/**
 * Controller Method: Causelist - Updated to include judge information
 */
public function causelist()
{
    if (!has_permission('cases', '', 'view')) {
        access_denied('cases');
    }
    
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
    
    // SECURE QUERY using Query Builder - looking for hearings ON this date
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
    $this->db->order_by('h.time', 'ASC');
    
    $query = $this->db->get();
    
    // Initialize hearings array
    $data['hearings'] = [];
    
    if ($query && $query->num_rows() > 0) {
        $data['hearings'] = $query->result_array();
    }
    
    // Get available dates with hearings using Query Builder
    $this->db->select('DISTINCT DATE(date) as hearing_date');
    $this->db->from(db_prefix() . 'hearings');
    $this->db->order_by('date', 'ASC');
    $date_query = $this->db->get();
    
    $data['upcoming_dates'] = $date_query->result_array();
    
    $data['date'] = $date;
    $data['title'] = 'Daily Cause List - ' . date('d M Y', strtotime($date));
    
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
    $this->load->view('cases/hearings/causelist', $data);
}

/**
 * Get cause list as JSON for AJAX requests
 * Updated to include judge information and filter by next_date
 */
public function get_causelist()
{
    if (!has_permission('cases', '', 'view')) {
        access_denied('cases');
    }
    
    // Load rate limiter and enforce limits for AJAX endpoint
    $this->load->helper('modules/cases/helpers/rate_limiter_helper');
    cases_enforce_rate_limit('get_causelist', 30, 300); // 30 requests per 5 minutes
    
    // Default to today's date
    $date = $this->input->get('date') ? $this->input->get('date') : date('Y-m-d');
    
    // DIRECT DB QUERY - Filtering by NEXT_DATE with court and judge information
    $sql = "SELECT 
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
                CONCAT(co.firstname, ' ', co.lastname) as contact_name,
                ct.name as court_name,
                cr.court_no,
                cr.judge_name
            FROM " . db_prefix() . "hearings h
            LEFT JOIN " . db_prefix() . "cases c ON c.id = h.case_id
            LEFT JOIN " . db_prefix() . "clients cl ON cl.userid = c.client_id
            LEFT JOIN " . db_prefix() . "contacts co ON co.id = c.contact_id
            LEFT JOIN " . db_prefix() . "court_rooms cr ON cr.id = c.court_room_id
            LEFT JOIN " . db_prefix() . "courts ct ON ct.id = cr.court_id
            WHERE DATE(h.next_date) = ?
            ORDER BY h.time ASC";
            
    $query = $this->db->query($sql, [$date]);
    
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
        
        $this->load->view('cases/hearings/calendar', $data);
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
        // Load security helpers
        $this->load->helper('modules/cases/helpers/security_helper');
        $this->load->helper('modules/cases/helpers/validation_helper');
        $this->load->helper('modules/cases/helpers/rate_limiter_helper');
        
        // Rate limiting for form submissions
        cases_enforce_rate_limit('hearing_update', 10, 300); // 10 updates per 5 minutes
        
        // Verify CSRF token
        if (!cases_verify_csrf_token()) {
            cases_log_security_event('Invalid CSRF token in quick_update', ['hearing_id' => $hearing_id], 'error');
            set_alert('danger', 'Security token mismatch. Please try again.');
            redirect(admin_url('cases/hearings/quick_update/' . $hearing_id));
            return;
        }

        // Validate hearing ID
        $validated_hearing_id = cases_validate_integer($hearing_id, 1);
        if ($validated_hearing_id === false) {
            set_alert('danger', 'Invalid hearing ID');
            redirect(admin_url('cases/hearings'));
            return;
        }

        $this->db->trans_begin(); // Start transaction
        
        try {
            // Validate and sanitize input data
            $update_data = [
                'status' => $this->input->post('status', true),
                'description' => $this->input->post('description', true),
                'next_date' => $this->input->post('next_date', true),
                'next_time' => $this->input->post('next_time', true),
                'upcoming_purpose' => $this->input->post('upcoming_purpose', true)
            ];
            
            $validation = cases_validate_hearing_data($update_data);
            if (!$validation['valid']) {
                set_alert('danger', 'Validation failed: ' . implode(', ', $validation['errors']));
                redirect(admin_url('cases/hearings/quick_update/' . $validated_hearing_id));
                return;
            }
            
            $validated_data = $validation['data'];
            
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
            
            // Log security event for successful update
            cases_log_security_event('Hearing updated successfully', [
                'hearing_id' => $validated_hearing_id,
                'case_id' => $hearing['case_id'],
                'updated_by' => get_staff_user_id(),
                'status_change' => $validated_data['status']
            ], 'info');
            
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                cases_log_security_event('Hearing update failed - transaction error', [
                    'hearing_id' => $validated_hearing_id
                ], 'error');
                set_alert('danger', 'Failed to update hearing');
            } else {
                $this->db->trans_commit();
                set_alert('success', 'Hearing updated successfully');
            }
            
            // FIXED REDIRECT - Go to main cases page
            redirect(admin_url('cases'));
            
        } catch (Exception $e) {
            $this->db->trans_rollback();
            cases_log_security_event('Hearing update failed - exception', [
                'hearing_id' => $hearing_id,
                'error' => $e->getMessage()
            ], 'error');
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
    
    $this->load->view('cases/hearings/quick_update', $data);
}
}