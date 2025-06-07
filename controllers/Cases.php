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


            /**
         * Appointments management page
         */
                public function appointments()
                {
                    if (!has_permission('cases', '', 'view')) {
                        access_denied('cases');
                    }

                    $data['title'] = 'Appointments Management';
                    $data['clients'] = $this->get_all_clients();
                    $data['services'] = $this->Appointments_model->get_services();
                    $data['staff'] = $this->get_active_staff();
                    
                    $this->load->view('cases/appointments/manage', $data);
                }

                /**
                 * Calendar view for appointments
                 */
                public function calendar()
                {
                    if (!has_permission('cases', '', 'view')) {
                        access_denied('cases');
                    }

                    $data['title'] = 'Appointments Calendar';
                    $data['staff'] = $this->get_active_staff();
                    
                    $this->load->view('cases/appointments/calendar', $data);
                }

                /**
                 * Get appointments for DataTables
                 */
                public function appointments_list()
{
    if (!has_permission('cases', '', 'view')) {
        header('Content-Type: application/json');
        echo json_encode([
            'draw' => $this->input->get('draw'),
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
            'error' => 'Access denied'
        ]);
        return;
    }

    try {
        // Load the model
        $this->load->model('Appointments_model');
        
        // Get DataTable parameters
        $draw = intval($this->input->get('draw'));
        $start = intval($this->input->get('start'));
        $length = intval($this->input->get('length'));
        $search_value = $this->input->get('search')['value'] ?? '';
        
        // Get order parameters
        $order_column_index = $this->input->get('order')[0]['column'] ?? 0;
        $order_dir = $this->input->get('order')[0]['dir'] ?? 'asc';
        
        // Define columns for ordering
        $columns = [
            'appointment_date',
            'client_name',
            'service_name',
            'staff_full_name',
            'status',
            'total_amount',
            'payment_status',
            null // Actions column - not sortable
        ];
        
        $order_column = $columns[$order_column_index] ?? 'appointment_date';
        
        // Get custom filters
        $filters = [
            'date_from' => $this->input->get('date_from'),
            'date_to' => $this->input->get('date_to'),
            'staff_id' => $this->input->get('staff_id'),
            'status' => $this->input->get('status'),
            'client_id' => $this->input->get('client_id')
        ];

        // Build the query
        $this->db->select('
            a.*,
            s.name as service_name,
            s.price as service_price,
            s.duration_minutes,
            st.firstname as staff_firstname,
            st.lastname as staff_lastname,
            c.company as client_name,
            CONCAT(COALESCE(ct.firstname, ""), " ", COALESCE(ct.lastname, "")) as contact_name,
            i.total as invoice_total,
            i.status as invoice_status
        ');
        $this->db->from(db_prefix() . 'appointments a');
        $this->db->join(db_prefix() . 'appointment_services s', 's.id = a.service_id', 'left');
        $this->db->join(db_prefix() . 'staff st', 'st.staffid = a.staff_id', 'left');
        $this->db->join(db_prefix() . 'clients c', 'c.userid = a.client_id', 'left');
        $this->db->join(db_prefix() . 'contacts ct', 'ct.id = a.contact_id', 'left');
        $this->db->join(db_prefix() . 'invoices i', 'i.id = a.invoice_id', 'left');

        // Apply custom filters
        if (!empty($filters['date_from'])) {
            $this->db->where('a.appointment_date >=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $this->db->where('a.appointment_date <=', $filters['date_to']);
        }
        
        if (!empty($filters['staff_id'])) {
            $this->db->where('a.staff_id', $filters['staff_id']);
        }
        
        if (!empty($filters['status'])) {
            $this->db->where('a.status', $filters['status']);
        }
        
        if (!empty($filters['client_id'])) {
            $this->db->where('a.client_id', $filters['client_id']);
        }

        // Apply search filter if provided
        if (!empty($search_value)) {
            $this->db->group_start();
            $this->db->like('c.company', $search_value);
            $this->db->or_like('s.name', $search_value);
            $this->db->or_like('CONCAT(st.firstname, " ", st.lastname)', $search_value);
            $this->db->or_like('a.status', $search_value);
            $this->db->or_like('CONCAT(ct.firstname, " ", ct.lastname)', $search_value);
            $this->db->group_end();
        }

        // Get total records before filtering (for recordsTotal)
        $total_query = clone $this->db;
        $total_query->select('COUNT(*) as total', false);
        $recordsTotal = $this->db->count_all(db_prefix() . 'appointments');

        // Get filtered count
        $filtered_query = clone $this->db;
        $recordsFiltered = $filtered_query->count_all_results();

        // Apply ordering
        if ($order_column != null) {
            // Special handling for computed columns
            if ($order_column == 'client_name') {
                $this->db->order_by('c.company', $order_dir);
            } elseif ($order_column == 'staff_full_name') {
                $this->db->order_by('CONCAT(st.firstname, " ", st.lastname)', $order_dir);
            } elseif ($order_column == 'service_name') {
                $this->db->order_by('s.name', $order_dir);
            } else {
                $this->db->order_by('a.' . $order_column, $order_dir);
            }
        } else {
            // Default ordering
            $this->db->order_by('a.appointment_date', 'DESC');
            $this->db->order_by('a.start_time', 'DESC');
        }

        // Apply pagination
        if ($length != -1) {
            $this->db->limit($length, $start);
        }

        // Get the data
        $appointments = $this->db->get()->result_array();
        
        // Format data for frontend
        $formatted_appointments = [];
        
        foreach ($appointments as $appointment) {
            // Format date and time
            $appointment['formatted_date'] = date('d M Y', strtotime($appointment['appointment_date']));
            $appointment['formatted_time'] = date('h:i A', strtotime($appointment['start_time']));
            
            // Format staff name
            $appointment['staff_full_name'] = trim(($appointment['staff_firstname'] ?? '') . ' ' . ($appointment['staff_lastname'] ?? ''));
            if (empty($appointment['staff_full_name'])) {
                $appointment['staff_full_name'] = 'Unknown Staff';
            }
            
            // Ensure client name is not empty
            if (empty($appointment['client_name'])) {
                $appointment['client_name'] = 'Unknown Client';
            }
            
            // Ensure service name is not empty  
            if (empty($appointment['service_name'])) {
                $appointment['service_name'] = 'Unknown Service';
            }
            
            // Status badge classes
            $status_classes = [
                'pending' => 'label-warning',
                'confirmed' => 'label-info', 
                'completed' => 'label-success',
                'cancelled' => 'label-danger',
                'no_show' => 'label-default',
                'rescheduled' => 'label-primary'
            ];
            $appointment['status_class'] = $status_classes[$appointment['status']] ?? 'label-default';
            
            // Ensure numeric fields are properly formatted
            $appointment['total_amount'] = number_format((float)($appointment['total_amount'] ?? 0), 2, '.', '');
            $appointment['paid_amount'] = number_format((float)($appointment['paid_amount'] ?? 0), 2, '.', '');
            
            // Ensure payment_status has a default
            if (empty($appointment['payment_status'])) {
                $appointment['payment_status'] = 'unpaid';
            }
            
            $formatted_appointments[] = $appointment;
        }

        // Return DataTable formatted response
        header('Content-Type: application/json');
        echo json_encode([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $formatted_appointments,
            'debug' => [
                'filters' => $filters,
                'search' => $search_value,
                'order' => $order_column . ' ' . $order_dir,
                'pagination' => 'start: ' . $start . ', length: ' . $length
            ]
        ]);

    } catch (Exception $e) {
        // Log the error
        log_message('error', 'Error in appointments_list: ' . $e->getMessage());
        log_message('error', 'Stack trace: ' . $e->getTraceAsString());
        
        // Return error response in DataTable format
        header('Content-Type: application/json');
        echo json_encode([
            'draw' => $this->input->get('draw') ?? 0,
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
            'error' => 'Database error occurred: ' . $e->getMessage()
        ]);
    }
}

        /**
         * Create new appointment
         */
        public function create_appointment()
        {
            if (!has_permission('cases', '', 'create')) {
                access_denied('cases');
            }

            $this->load->library('form_validation');
            
            // Validation rules
            $this->form_validation->set_rules('client_id', 'Client', 'required|numeric');
            $this->form_validation->set_rules('service_id', 'Service', 'required|numeric');
            $this->form_validation->set_rules('staff_id', 'Staff', 'required|numeric');
            $this->form_validation->set_rules('appointment_date', 'Date', 'required|callback_validate_date');
            $this->form_validation->set_rules('start_time', 'Time', 'required');

            if ($this->form_validation->run() === FALSE) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => validation_errors()
                ]);
                return;
            }

            $data = $this->input->post();
            
            $this->db->trans_begin();
            
            try {
                // Create appointment
                $appointment_id = $this->Appointments_model->create_appointment($data);
                
                if (!$appointment_id) {
                    throw new Exception('Failed to create appointment');
                }
                
                // Handle invoice generation
                $service = $this->Appointments_model->get_service($data['service_id']);
                $settings = $this->Appointments_model->get_settings();
                
                $invoice_id = null;
                
                if ($service['requires_prepayment'] || ($settings['require_prepayment'] ?? false)) {
                    $invoice_id = $this->generate_appointment_invoice($appointment_id, 'prepaid');
                    
                    if ($invoice_id) {
                        $this->Appointments_model->update_appointment($appointment_id, [
                            'invoice_id' => $invoice_id,
                            'payment_required' => 1
                        ]);
                    }
                }
                
                $this->db->trans_commit();
                
                // Send confirmation
                $this->send_appointment_notification($appointment_id, 'created');
                
                echo json_encode([
                    'success' => true,
                    'appointment_id' => $appointment_id,
                    'invoice_id' => $invoice_id,
                    'message' => 'Appointment created successfully'
                ]);
                
            } catch (Exception $e) {
                $this->db->trans_rollback();
                log_message('error', 'Appointment creation failed: ' . $e->getMessage());
                
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to create appointment: ' . $e->getMessage()
                ]);
            }
        }

        /**
         * Generate invoice for appointment
         */
        private function generate_appointment_invoice($appointment_id, $type = 'full')
        {
            $appointment = $this->Appointments_model->get_appointment($appointment_id);
            $service = $this->Appointments_model->get_service($appointment['service_id']);
            $settings = $this->Appointments_model->get_settings();
            
            $this->load->model('invoices_model');
            
            // Calculate amounts
            $full_amount = $service['price'];
            $booking_fee = $service['booking_fee'] ?? 0;
            
            switch ($type) {
                case 'prepaid':
                    $amount = $full_amount;
                    $description = $service['name'] . ' - Appointment on ' . 
                                date('M d, Y', strtotime($appointment['appointment_date']));
                    break;
                case 'booking_fee':
                    $amount = $booking_fee;
                    $description = 'Booking Fee - ' . $service['name'];
                    break;
                default:
                    $amount = $full_amount;
                    $description = $service['name'];
            }
            
            // Prepare invoice data
            $invoice_data = [
                'clientid' => $appointment['client_id'],
                'date' => date('Y-m-d'),
                'duedate' => date('Y-m-d', strtotime('+7 days')),
                'currency' => $service['currency'] ?? get_option('default_currency'),
                'prefix' => $settings['invoice_prefix'] ?? 'APPT-',
                'adminnote' => 'Generated automatically for appointment #' . $appointment_id,
                'terms' => $settings['default_payment_terms'] ?? get_option('predefined_terms_invoice'),
                'show_quantity_as' => 1,
                'addedfrom' => get_staff_user_id()
            ];
            
            // Create invoice
            $invoice_id = $this->invoices_model->add($invoice_data);
            
            if (!$invoice_id) {
                throw new Exception('Failed to create invoice');
            }
            
            // Add invoice item
            $item_data = [
                'rel_id' => $invoice_id,
                'rel_type' => 'invoice',
                'description' => $description,
                'long_description' => $service['description'],
                'qty' => 1,
                'rate' => $amount,
                'unit' => 'session'
            ];
            
            $this->db->insert(db_prefix() . 'itemable', $item_data);
            
            // Add taxes if configured
            if ($service['tax_id']) {
                $this->add_invoice_tax($invoice_id, $service['tax_id']);
            }
            
            // Update invoice totals
            update_invoice_status($invoice_id);
            
            // Log payment tracking
            $this->Appointments_model->add_payment_record([
                'appointment_id' => $appointment_id,
                'invoice_id' => $invoice_id,
                'payment_type' => $type,
                'amount' => $amount
            ]);
            
            // Send invoice if configured
            if ($settings['send_invoice_immediately'] ?? false) {
                $this->send_invoice_notification($invoice_id);
            }
            
            return $invoice_id;
        }

        /**
         * Complete appointment and convert to consultation
         */
        public function complete_appointment($appointment_id)
        {
            if (!has_permission('cases', '', 'edit')) {
                access_denied('cases');
            }
            
            $appointment = $this->Appointments_model->get_appointment($appointment_id);
            
            if (!$appointment) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Appointment not found'
                ]);
                return;
            }
            
            $this->db->trans_begin();
            
            try {
                // Complete appointment
                $notes = $this->input->post('notes');
                $this->Appointments_model->complete_appointment($appointment_id, $notes);
                
                // Generate post-service invoice if needed
                if ($appointment['auto_invoice'] && !$appointment['invoice_id']) {
                    $invoice_id = $this->generate_appointment_invoice($appointment_id, 'full');
                    
                    $this->Appointments_model->update_appointment($appointment_id, [
                        'invoice_id' => $invoice_id
                    ]);
                }
                
                // Convert to consultation if requested
                $create_consultation = $this->input->post('create_consultation');
                $consultation_id = null;
                
                if ($create_consultation) {
                    $consultation_id = $this->convert_appointment_to_consultation($appointment_id);
                }
                
                $this->db->trans_commit();
                
                echo json_encode([
                    'success' => true,
                    'consultation_id' => $consultation_id,
                    'message' => 'Appointment completed successfully'
                ]);
                
            } catch (Exception $e) {
                $this->db->trans_rollback();
                
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to complete appointment: ' . $e->getMessage()
                ]);
            }
        }

        /**
         * Convert appointment to consultation
         */
        private function convert_appointment_to_consultation($appointment_id)
        {
            $appointment = $this->Appointments_model->get_appointment($appointment_id);
            $service = $this->Appointments_model->get_service($appointment['service_id']);
            
            $consultation_data = [
                'client_id' => $appointment['client_id'],
                'contact_id' => $appointment['contact_id'],
                'invoice_id' => $appointment['invoice_id'],
                'tag' => $service['name'],
                'note' => 'Consultation converted from appointment #' . $appointment_id . 
                        ($appointment['notes'] ? "\n\nAppointment Notes:\n" . $appointment['notes'] : ''),
                'staff_id' => $appointment['staff_id'],
                'date_added' => date('Y-m-d H:i:s'),
                'phase' => 'consultation'
            ];
            
            $this->db->insert(db_prefix() . 'case_consultations', $consultation_data);
            $consultation_id = $this->db->insert_id();
            
            // Update appointment with consultation link
            $this->Appointments_model->update_appointment($appointment_id, [
                'consultation_id' => $consultation_id
            ]);
            
            return $consultation_id;
        }

        /**
         * Get available time slots
         */
        public function get_available_slots()
        {
            $staff_id = $this->input->get('staff_id');
            $date = $this->input->get('date');
            $service_id = $this->input->get('service_id');
            
            if (!$staff_id || !$date || !$service_id) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Missing required parameters'
                ]);
                return;
            }
            
            $slots = $this->Appointments_model->get_available_slots($staff_id, $date, $service_id);
            
            echo json_encode([
                'success' => true,
                'data' => $slots
            ]);
        }

        /**
         * Get services by staff
         */
        public function get_staff_services($staff_id)
        {
            $services = $this->Appointments_model->get_service_staff($staff_id);
            
            echo json_encode([
                'success' => true,
                'data' => $services
            ]);
        }

        /**
         * Cancel appointment
         */
        public function cancel_appointment($appointment_id)
        {
            if (!has_permission('cases', '', 'edit')) {
                access_denied('cases');
            }
            
            $reason = $this->input->post('reason');
            
            $this->db->trans_begin();
            
            try {
                // Cancel appointment
                $this->Appointments_model->cancel_appointment($appointment_id, $reason);
                
                // Handle refunds if needed
                $appointment = $this->Appointments_model->get_appointment($appointment_id);
                
                if ($appointment['paid_amount'] > 0) {
                    $this->process_appointment_cancellation_refund($appointment_id);
                }
                
                $this->db->trans_commit();
                
                // Send notification
                $this->send_appointment_notification($appointment_id, 'cancelled');
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Appointment cancelled successfully'
                ]);
                
            } catch (Exception $e) {
                $this->db->trans_rollback();
                
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to cancel appointment: ' . $e->getMessage()
                ]);
            }
        }

        /**
         * Get appointment calendar data
         */
        public function calendar_data()
        {
            $start = $this->input->get('start');
            $end = $this->input->get('end');
            $staff_id = $this->input->get('staff_id');
            
            $appointments = $this->Appointments_model->get_calendar_appointments($start, $end, $staff_id);
            
            $events = [];
            foreach ($appointments as $appointment) {
                $events[] = [
                    'id' => $appointment['id'],
                    'title' => $appointment['client_name'] . ' - ' . $appointment['service_name'],
                    'start' => $appointment['appointment_date'] . 'T' . $appointment['start_time'],
                    'end' => $appointment['appointment_date'] . 'T' . $appointment['end_time'],
                    'backgroundColor' => $appointment['service_color'] ?? '#1a6bcc',
                    'borderColor' => $appointment['service_color'] ?? '#1a6bcc',
                    'url' => admin_url('cases/appointments/view/' . $appointment['id'])
                ];
            }
            
            header('Content-Type: application/json');
            echo json_encode($events);
        }

        /**
         * Helper method to get active staff
         */
        private function get_active_staff()
        {
            $this->db->select('staffid, firstname, lastname, email');
            $this->db->where('active', 1);
            $this->db->order_by('firstname ASC, lastname ASC');
            return $this->db->get(db_prefix() . 'staff')->result_array();
        }

        /**
         * Helper method to add tax to invoice
         */
        private function add_invoice_tax($invoice_id, $tax_id)
        {
            // Get tax details
            $this->db->where('id', $tax_id);
            $tax = $this->db->get(db_prefix() . 'taxes')->row_array();
            
            if ($tax) {
                // Get invoice items to apply tax
                $this->db->where('rel_id', $invoice_id);
                $this->db->where('rel_type', 'invoice');
                $items = $this->db->get(db_prefix() . 'itemable')->result_array();
                
                foreach ($items as $item) {
                    $tax_data = [
                        'itemid' => $item['id'],
                        'rel_id' => $invoice_id,
                        'rel_type' => 'invoice',
                        'taxrate' => $tax['taxrate'],
                        'taxname' => $tax['name']
                    ];
                    
                    $this->db->insert(db_prefix() . 'item_tax', $tax_data);
                }
            }
        }

        /**
         * Send appointment notifications
         */
        private function send_appointment_notification($appointment_id, $type)
        {
            // Implement email/SMS notifications
            // This can integrate with Perfex's existing email system
            $appointment = $this->Appointments_model->get_appointment($appointment_id);
            
            if (!$appointment) {
                return false;
            }
            
            // Load email template based on type
            $template_data = [
                'appointment' => $appointment,
                'client_name' => $appointment['client_name'],
                'service_name' => $appointment['service_name'],
                'appointment_date' => date('M d, Y', strtotime($appointment['appointment_date'])),
                'appointment_time' => date('h:i A', strtotime($appointment['start_time'])),
                'staff_name' => $appointment['staff_firstname'] . ' ' . $appointment['staff_lastname']
            ];
            
            // You can integrate this with Perfex's email templates
            // For now, we'll log the notification
            log_message('info', "Appointment {$type} notification for appointment #{$appointment_id}");
            
            return true;
        }

        /**
         * Send invoice notification
         */
        private function send_invoice_notification($invoice_id)
        {
            // Use Perfex's built-in invoice email functionality
            if (function_exists('send_invoice_to_client')) {
                return send_invoice_to_client($invoice_id);
            }
            
            return true;
        }

        /**
         * Process cancellation refund
         */
        private function process_appointment_cancellation_refund($appointment_id)
        {
            $appointment = $this->Appointments_model->get_appointment($appointment_id);
            $settings = $this->Appointments_model->get_settings();
            
            if (!$appointment || $appointment['paid_amount'] <= 0) {
                return false;
            }
            
            // Calculate refund based on cancellation policy
            $appointment_datetime = strtotime($appointment['appointment_date'] . ' ' . $appointment['start_time']);
            $hours_until = ($appointment_datetime - time()) / 3600;
            $cancellation_fee_hours = $settings['cancellation_fee_hours'] ?? 24;
            
            $service = $this->Appointments_model->get_service($appointment['service_id']);
            $cancellation_fee = 0;
            $refund_amount = $appointment['paid_amount'];
            
            if ($hours_until < $cancellation_fee_hours) {
                $cancellation_fee = $service['cancellation_fee'] ?? 0;
                $refund_amount = max(0, $appointment['paid_amount'] - $cancellation_fee);
            }
            
            // Create credit note for refund if amount > 0
            if ($refund_amount > 0) {
                $this->create_refund_credit_note($appointment_id, $refund_amount);
            }
            
            // Create cancellation fee invoice if applicable
            if ($cancellation_fee > 0) {
                $this->generate_cancellation_fee_invoice($appointment_id, $cancellation_fee);
            }
            
            return true;
        }

        /**
         * Create credit note for refund
         */
        private function create_refund_credit_note($appointment_id, $amount)
        {
            $appointment = $this->Appointments_model->get_appointment($appointment_id);
            
            if (!$appointment) {
                return false;
            }
            
            $this->load->model('credit_notes_model');
            
            $credit_note_data = [
                'clientid' => $appointment['client_id'],
                'date' => date('Y-m-d'),
                'adminnote' => 'Refund for cancelled appointment #' . $appointment_id,
                'currency' => get_option('default_currency'),
                'addedfrom' => get_staff_user_id()
            ];
            
            $credit_note_id = $this->credit_notes_model->add($credit_note_data);
            
            if ($credit_note_id) {
                // Add credit note item
                $item_data = [
                    'rel_id' => $credit_note_id,
                    'rel_type' => 'credit_note',
                    'description' => 'Refund for cancelled appointment - ' . $appointment['service_name'],
                    'qty' => 1,
                    'rate' => $amount
                ];
                
                $this->db->insert(db_prefix() . 'itemable', $item_data);
                
                // Update credit note totals
                update_credit_note_status($credit_note_id);
                
                // Log the refund
                $this->Appointments_model->add_payment_record([
                    'appointment_id' => $appointment_id,
                    'invoice_id' => $credit_note_id,
                    'payment_type' => 'refund',
                    'amount' => $amount
                ]);
            }
            
            return $credit_note_id;
        }

        /**
         * Generate cancellation fee invoice
         */
        private function generate_cancellation_fee_invoice($appointment_id, $fee_amount)
        {
            $appointment = $this->Appointments_model->get_appointment($appointment_id);
            
            if (!$appointment || $fee_amount <= 0) {
                return false;
            }
            
            $this->load->model('invoices_model');
            
            $invoice_data = [
                'clientid' => $appointment['client_id'],
                'date' => date('Y-m-d'),
                'duedate' => date('Y-m-d'),
                'currency' => get_option('default_currency'),
                'prefix' => 'CANCEL-',
                'adminnote' => 'Cancellation fee for appointment #' . $appointment_id,
                'terms' => 'Due immediately',
                'addedfrom' => get_staff_user_id()
            ];
            
            $invoice_id = $this->invoices_model->add($invoice_data);
            
            if ($invoice_id) {
                // Add invoice item
                $item_data = [
                    'rel_id' => $invoice_id,
                    'rel_type' => 'invoice',
                    'description' => 'Cancellation Fee - ' . $appointment['service_name'],
                    'qty' => 1,
                    'rate' => $fee_amount
                ];
                
                $this->db->insert(db_prefix() . 'itemable', $item_data);
                
                // Update invoice totals
                update_invoice_status($invoice_id);
                
                // Log the fee
                $this->Appointments_model->add_payment_record([
                    'appointment_id' => $appointment_id,
                    'invoice_id' => $invoice_id,
                    'payment_type' => 'cancellation_fee',
                    'amount' => $fee_amount
                ]);
                
                // Send invoice
                $this->send_invoice_notification($invoice_id);
            }
            
            return $invoice_id;
        }

        /**
         * Validation callback for date
         */
        public function validate_date($date)
        {
            $today = date('Y-m-d');
            
            if ($date < $today) {
                $this->form_validation->set_message('validate_date', 'Appointment date cannot be in the past');
                return false;
            }
            
            return true;
        }

        /**
         * Appointment settings page
         */
        public function settings()
        {
            if (!is_admin()) {
                access_denied();
            }
            
            if ($this->input->post()) {
                $settings = $this->input->post('settings');
                
                foreach ($settings as $name => $value) {
                    $this->Appointments_model->update_setting($name, $value);
                }
                
                set_alert('success', 'Settings updated successfully');
                redirect(admin_url('cases/settings'));
            }
            
            $data['title'] = 'Appointment Settings';
            $data['settings'] = $this->Appointments_model->get_settings();
            
            $this->load->view('cases/appointments/settings', $data);
        }

        /**
         * Services management
         */
        public function services()
        {
            if (!has_permission('cases', '', 'view')) {
                access_denied('cases');
            }
            
            $data['title'] = 'Appointment Services';
            $data['services'] = $this->Appointments_model->get_services(false);
            
            $this->load->view('cases/appointments/services', $data);
        }

        /**
         * Staff availability management
         */
        public function availability()
        {
            if (!has_permission('cases', '', 'edit')) {
                access_denied('cases');
            }
            
            $data['title'] = 'Staff Availability';
            $data['staff'] = $this->get_active_staff();
            
            $this->load->view('cases/appointments/availability', $data);
        }

        /**
         * Get appointment statistics
         */
        public function statistics()
        {
            if (!has_permission('cases', '', 'view')) {
                access_denied('cases');
            }
            
            $date_from = $this->input->get('date_from') ?: date('Y-m-01');
            $date_to = $this->input->get('date_to') ?: date('Y-m-t');
            
            $stats = $this->Appointments_model->get_statistics($date_from, $date_to);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $stats
            ]);
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

}
