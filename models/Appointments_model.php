<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Step 3: Create this file as modules/cases/models/Appointments_model.php
 */

class Appointments_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all appointments with related data
     */
    public function get_appointments($filters = [])
    {
        $this->db->select('
            a.*,
            s.name as service_name,
            s.price as service_price,
            s.duration_minutes,
            st.firstname as staff_firstname,
            st.lastname as staff_lastname,
            c.company as client_name,
            CONCAT(ct.firstname, " ", ct.lastname) as contact_name,
            i.total as invoice_total,
            i.status as invoice_status
        ');
        $this->db->from(db_prefix() . 'appointments a');
        $this->db->join(db_prefix() . 'appointment_services s', 's.id = a.service_id', 'left');
        $this->db->join(db_prefix() . 'staff st', 'st.staffid = a.staff_id', 'left');
        $this->db->join(db_prefix() . 'clients c', 'c.userid = a.client_id', 'left');
        $this->db->join(db_prefix() . 'contacts ct', 'ct.id = a.contact_id', 'left');
        $this->db->join(db_prefix() . 'invoices i', 'i.id = a.invoice_id', 'left');

        // Apply filters
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

        $this->db->order_by('a.appointment_date', 'ASC');
        $this->db->order_by('a.start_time', 'ASC');

        return $this->db->get()->result_array();
    }

    /**
     * Get single appointment by ID
     */
    public function get_appointment($id)
    {
        $this->db->select('
            a.*,
            s.name as service_name,
            s.price as service_price,
            s.duration_minutes,
            s.requires_prepayment,
            s.booking_fee,
            s.cancellation_fee,
            st.firstname as staff_firstname,
            st.lastname as staff_lastname,
            c.company as client_name,
            CONCAT(ct.firstname, " ", ct.lastname) as contact_name,
            i.total as invoice_total,
            i.status as invoice_status
        ');
        $this->db->from(db_prefix() . 'appointments a');
        $this->db->join(db_prefix() . 'appointment_services s', 's.id = a.service_id', 'left');
        $this->db->join(db_prefix() . 'staff st', 'st.staffid = a.staff_id', 'left');
        $this->db->join(db_prefix() . 'clients c', 'c.userid = a.client_id', 'left');
        $this->db->join(db_prefix() . 'contacts ct', 'ct.id = a.contact_id', 'left');
        $this->db->join(db_prefix() . 'invoices i', 'i.id = a.invoice_id', 'left');
        $this->db->where('a.id', $id);

        return $this->db->get()->row_array();
    }

    /**
     * Create new appointment
     */
    public function create_appointment($data)
    {
        // Validate required fields
        $required_fields = ['client_id', 'service_id', 'staff_id', 'appointment_date', 'start_time'];
        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                log_message('error', 'Missing required field: ' . $field);
                return false;
            }
        }

        // Get service details for duration
        $service = $this->get_service($data['service_id']);
        if (!$service) {
            log_message('error', 'Service not found: ' . $data['service_id']);
            return false;
        }

        // Calculate end time
        $start_time = strtotime($data['start_time']);
        $end_time = $start_time + ($service['duration_minutes'] * 60);

        // Check for conflicts
        if (!$this->is_time_slot_available($data['staff_id'], $data['appointment_date'], $data['start_time'], date('H:i:s', $end_time))) {
            log_message('error', 'Time slot not available');
            return false;
        }

        $appointment_data = [
            'client_id' => $data['client_id'],
            'contact_id' => $data['contact_id'] ?? null,
            'service_id' => $data['service_id'],
            'staff_id' => $data['staff_id'],
            'appointment_date' => $data['appointment_date'],
            'start_time' => $data['start_time'],
            'end_time' => date('H:i:s', $end_time),
            'duration_minutes' => $service['duration_minutes'],
            'total_amount' => $service['price'],
            'notes' => $data['notes'] ?? '',
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_required' => $service['requires_prepayment'] ? 1 : 0,
            'booked_by' => $data['booked_by'] ?? 'staff',
            'booking_source' => $data['booking_source'] ?? 'dashboard',
            'confirmation_key' => $this->generate_confirmation_key(),
            'created_by' => get_staff_user_id(),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert(db_prefix() . 'appointments', $appointment_data);
        
        if ($this->db->affected_rows() > 0) {
            return $this->db->insert_id();
        }

        return false;
    }

    /**
     * Update appointment
     */
    public function update_appointment($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['updated_by'] = get_staff_user_id();

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'appointments', $data);

        return $this->db->affected_rows() > 0;
    }

    /**
     * Check if time slot is available
     */
    public function is_time_slot_available($staff_id, $date, $start_time, $end_time, $exclude_appointment_id = null)
    {
        // Check against existing appointments
        $this->db->where('staff_id', $staff_id);
        $this->db->where('appointment_date', $date);
        $this->db->where('status !=', 'cancelled');
        
        if ($exclude_appointment_id) {
            $this->db->where('id !=', $exclude_appointment_id);
        }

        // Check for time conflicts
        $this->db->group_start();
        $this->db->where('start_time <', $end_time);
        $this->db->where('end_time >', $start_time);
        $this->db->group_end();

        $conflicting_appointments = $this->db->get(db_prefix() . 'appointments')->num_rows();

        if ($conflicting_appointments > 0) {
            return false;
        }

        // Check against blocked times
        $datetime_start = $date . ' ' . $start_time;
        $datetime_end = $date . ' ' . $end_time;

        $this->db->where('staff_id', $staff_id);
        $this->db->where('start_datetime <', $datetime_end);
        $this->db->where('end_datetime >', $datetime_start);

        $blocked_times = $this->db->get(db_prefix() . 'appointment_blocked_times')->num_rows();

        return $blocked_times == 0;
    }

    /**
     * Get available time slots for a staff member on a specific date
     */
    public function get_available_slots($staff_id, $date, $service_id)
    {
        $service = $this->get_service($service_id);
        if (!$service) {
            return [];
        }

        // Get staff availability for the day
        $day_of_week = date('N', strtotime($date)); // 1=Monday, 7=Sunday
        $availability = $this->get_staff_availability($staff_id, $day_of_week);

        if (!$availability || !$availability['is_available']) {
            return [];
        }

        $settings = $this->get_settings();
        $slot_interval = $settings['time_slot_interval'] ?? 30;
        $buffer_before = $settings['buffer_time_before'] ?? 15;
        $buffer_after = $settings['buffer_time_after'] ?? 15;

        $available_slots = [];
        $start_time = strtotime($availability['start_time']);
        $end_time = strtotime($availability['end_time']);
        $break_start = $availability['break_start'] ? strtotime($availability['break_start']) : null;
        $break_end = $availability['break_end'] ? strtotime($availability['break_end']) : null;

        $current_time = $start_time;

        while ($current_time + ($service['duration_minutes'] * 60) <= $end_time) {
            $slot_start = date('H:i:s', $current_time);
            $slot_end = date('H:i:s', $current_time + ($service['duration_minutes'] * 60));

            // Skip lunch break
            if ($break_start && $break_end) {
                if (!($current_time + ($service['duration_minutes'] * 60) <= $break_start || $current_time >= $break_end)) {
                    $current_time += ($slot_interval * 60);
                    continue;
                }
            }

            // Check if slot is available
            if ($this->is_time_slot_available($staff_id, $date, $slot_start, $slot_end)) {
                $available_slots[] = [
                    'start_time' => $slot_start,
                    'end_time' => $slot_end,
                    'formatted_time' => date('h:i A', $current_time)
                ];
            }

            $current_time += ($slot_interval * 60);
        }

        return $available_slots;
    }

    /**
     * Get services
     */
    public function get_services($active_only = true)
    {
        if ($active_only) {
            $this->db->where('active', 1);
        }
        $this->db->order_by('sort_order', 'ASC');
        return $this->db->get(db_prefix() . 'appointment_services')->result_array();
    }

    /**
     * Get single service
     */
    public function get_service($id)
    {
        return $this->db->get_where(db_prefix() . 'appointment_services', ['id' => $id])->row_array();
    }

    /**
     * Get staff availability
     */
    public function get_staff_availability($staff_id, $day_of_week = null)
    {
        if ($day_of_week) {
            $this->db->where('day_of_week', $day_of_week);
        }
        $this->db->where('staff_id', $staff_id);
        
        if ($day_of_week) {
            return $this->db->get(db_prefix() . 'appointment_staff_availability')->row_array();
        } else {
            return $this->db->get(db_prefix() . 'appointment_staff_availability')->result_array();
        }
    }

    /**
     * Get staff who can perform a service
     */
    public function get_service_staff($service_id)
    {
        $this->db->select('
            s.staffid, 
            s.firstname, 
            s.lastname,
            ass.custom_price
        ');
        $this->db->from(db_prefix() . 'staff s');
        $this->db->join(db_prefix() . 'appointment_staff_services ass', 'ass.staff_id = s.staffid');
        $this->db->where('ass.service_id', $service_id);
        $this->db->where('s.active', 1);
        
        return $this->db->get()->result_array();
    }

    /**
     * Get appointment settings
     */
    public function get_settings()
    {
        $settings = [];
        $query = $this->db->get(db_prefix() . 'appointment_settings');
        
        foreach ($query->result_array() as $setting) {
            $value = $setting['setting_value'];
            
            // Convert based on type
            switch ($setting['setting_type']) {
                case 'boolean':
                    $value = (bool) $value;
                    break;
                case 'integer':
                    $value = (int) $value;
                    break;
                case 'decimal':
                    $value = (float) $value;
                    break;
            }
            
            $settings[$setting['setting_name']] = $value;
        }
        
        return $settings;
    }

    /**
     * Update setting
     */
    public function update_setting($name, $value)
    {
        $this->db->where('setting_name', $name);
        $exists = $this->db->get(db_prefix() . 'appointment_settings')->num_rows() > 0;
        
        if ($exists) {
            $this->db->where('setting_name', $name);
            return $this->db->update(db_prefix() . 'appointment_settings', [
                'setting_value' => $value,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            return $this->db->insert(db_prefix() . 'appointment_settings', [
                'setting_name' => $name,
                'setting_value' => $value
            ]);
        }
    }

    /**
     * Generate unique confirmation key
     */
    private function generate_confirmation_key()
    {
        do {
            $key = md5(uniqid(rand(), true));
            $this->db->where('confirmation_key', $key);
            $exists = $this->db->get(db_prefix() . 'appointments')->num_rows() > 0;
        } while ($exists);
        
        return $key;
    }

    /**
     * Get appointments for calendar view
     */
    public function get_calendar_appointments($start_date, $end_date, $staff_id = null)
    {
        $this->db->select('
            a.id,
            a.appointment_date,
            a.start_time,
            a.end_time,
            a.status,
            s.name as service_name,
            s.color as service_color,
            c.company as client_name,
            CONCAT(st.firstname, " ", st.lastname) as staff_name
        ');
        $this->db->from(db_prefix() . 'appointments a');
        $this->db->join(db_prefix() . 'appointment_services s', 's.id = a.service_id', 'left');
        $this->db->join(db_prefix() . 'clients c', 'c.userid = a.client_id', 'left');
        $this->db->join(db_prefix() . 'staff st', 'st.staffid = a.staff_id', 'left');
        
        $this->db->where('a.appointment_date >=', $start_date);
        $this->db->where('a.appointment_date <=', $end_date);
        $this->db->where('a.status !=', 'cancelled');
        
        if ($staff_id) {
            $this->db->where('a.staff_id', $staff_id);
        }
        
        return $this->db->get()->result_array();
    }

    /**
     * Get upcoming appointments (for reminders)
     */
    public function get_upcoming_appointments($hours_ahead = 24)
    {
        $start_time = date('Y-m-d H:i:s');
        $end_time = date('Y-m-d H:i:s', strtotime('+' . $hours_ahead . ' hours'));
        
        $this->db->select('
            a.*,
            s.name as service_name,
            c.company as client_name,
            CONCAT(ct.firstname, " ", ct.lastname) as contact_name,
            ct.email as contact_email,
            CONCAT(st.firstname, " ", st.lastname) as staff_name
        ');
        $this->db->from(db_prefix() . 'appointments a');
        $this->db->join(db_prefix() . 'appointment_services s', 's.id = a.service_id', 'left');
        $this->db->join(db_prefix() . 'clients c', 'c.userid = a.client_id', 'left');
        $this->db->join(db_prefix() . 'contacts ct', 'ct.id = a.contact_id', 'left');
        $this->db->join(db_prefix() . 'staff st', 'st.staffid = a.staff_id', 'left');
        
        $this->db->where('CONCAT(a.appointment_date, " ", a.start_time) >=', $start_time);
        $this->db->where('CONCAT(a.appointment_date, " ", a.start_time) <=', $end_time);
        $this->db->where('a.status', 'confirmed');
        $this->db->where('a.reminder_sent', 0);
        
        return $this->db->get()->result_array();
    }

    /**
     * Mark appointment reminder as sent
     */
    public function mark_reminder_sent($appointment_id)
    {
        $this->db->set('reminder_sent', 1);
        $this->db->set('reminder_count', 'reminder_count+1', false);
        $this->db->where('id', $appointment_id);
        $this->db->update(db_prefix() . 'appointments');

        return $this->db->affected_rows() > 0;
    }

    /**
     * Get payment tracking for appointment
     */
    public function get_appointment_payments($appointment_id)
    {
        $this->db->select('
            ap.*,
            i.number as invoice_number,
            i.total as invoice_total,
            i.status as invoice_status
        ');
        $this->db->from(db_prefix() . 'appointment_payments ap');
        $this->db->join(db_prefix() . 'invoices i', 'i.id = ap.invoice_id', 'left');
        $this->db->where('ap.appointment_id', $appointment_id);
        $this->db->order_by('ap.created_at', 'DESC');
        
        return $this->db->get()->result_array();
    }

    /**
     * Add payment record
     */
    public function add_payment_record($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert(db_prefix() . 'appointment_payments', $data);
        return $this->db->insert_id();
    }

    /**
     * Cancel appointment
     */
    public function cancel_appointment($appointment_id, $reason = '')
    {
        return $this->update_appointment($appointment_id, [
            'status' => 'cancelled',
            'cancellation_reason' => $reason
        ]);
    }

    /**
     * Reschedule appointment
     */
    public function reschedule_appointment($appointment_id, $new_date, $new_start_time, $new_staff_id = null)
    {
        $appointment = $this->get_appointment($appointment_id);
        if (!$appointment) {
            return false;
        }

        $staff_id = $new_staff_id ?: $appointment['staff_id'];
        $service = $this->get_service($appointment['service_id']);
        $new_end_time = date('H:i:s', strtotime($new_start_time) + ($service['duration_minutes'] * 60));

        // Check availability
        if (!$this->is_time_slot_available($staff_id, $new_date, $new_start_time, $new_end_time, $appointment_id)) {
            return false;
        }

        // Update appointment
        return $this->update_appointment($appointment_id, [
            'appointment_date' => $new_date,
            'start_time' => $new_start_time,
            'end_time' => $new_end_time,
            'staff_id' => $staff_id,
            'status' => 'rescheduled'
        ]);
    }

    /**
     * Complete appointment
     */
    public function complete_appointment($appointment_id, $notes = '')
    {
        $update_data = [
            'status' => 'completed'
        ];
        
        if ($notes) {
            $update_data['internal_notes'] = $notes;
        }
        
        return $this->update_appointment($appointment_id, $update_data);
    }

    /**
     * Get statistics
     */
    public function get_statistics($date_from = null, $date_to = null)
    {
        if (!$date_from) {
            $date_from = date('Y-m-01'); // First day of current month
        }
        if (!$date_to) {
            $date_to = date('Y-m-t'); // Last day of current month
        }

        // Total appointments
        $this->db->where('appointment_date >=', $date_from);
        $this->db->where('appointment_date <=', $date_to);
        $total = $this->db->count_all_results(db_prefix() . 'appointments');

        // By status
        $statuses = ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'];
        $by_status = [];
        
        foreach ($statuses as $status) {
            $this->db->where('appointment_date >=', $date_from);
            $this->db->where('appointment_date <=', $date_to);
            $this->db->where('status', $status);
            $by_status[$status] = $this->db->count_all_results(db_prefix() . 'appointments');
        }

        // Revenue
        $this->db->select('SUM(total_amount) as total_revenue, SUM(paid_amount) as paid_revenue');
        $this->db->where('appointment_date >=', $date_from);
        $this->db->where('appointment_date <=', $date_to);
        $this->db->where('status !=', 'cancelled');
        $revenue = $this->db->get(db_prefix() . 'appointments')->row_array();

        return [
            'total' => $total,
            'by_status' => $by_status,
            'total_revenue' => $revenue['total_revenue'] ?: 0,
            'paid_revenue' => $revenue['paid_revenue'] ?: 0,
            'date_from' => $date_from,
            'date_to' => $date_to
        ];
    }

    /**
     * Get blocked times for staff
     */
    public function get_blocked_times($staff_id, $date_from = null, $date_to = null)
    {
        $this->db->where('staff_id', $staff_id);
        
        if ($date_from && $date_to) {
            $this->db->where('DATE(start_datetime) >=', $date_from);
            $this->db->where('DATE(end_datetime) <=', $date_to);
        }
        
        $this->db->order_by('start_datetime', 'ASC');
        return $this->db->get(db_prefix() . 'appointment_blocked_times')->result_array();
    }

    /**
     * Add blocked time
     */
    public function add_blocked_time($data)
    {
        $data['created_by'] = get_staff_user_id();
        $data['created_at'] = date('Y-m-d H:i:s');
        
        $this->db->insert(db_prefix() . 'appointment_blocked_times', $data);
        return $this->db->insert_id();
    }

    /**
     * Delete blocked time
     */
    public function delete_blocked_time($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete(db_prefix() . 'appointment_blocked_times');
    }
}

?>